<?php
/**
 * ANATEL
 *
 * 17/11/2026 - criado para a integração REST do Mapeamento das Integrações (ADR-PET-011)
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

/**
 * Cliente REST da consulta: monta a chamada com o método, o caminho, o local de cada parâmetro e os headers fixos
 * gravados na integração, verifica o certificado do servidor e trata como falha a resposta HTTP fora de 2xx, o JSON
 * inválido e a resposta com o campo "erro" preenchido, que o serviço da Receita devolve com HTTP 200.
 */
class MdPetIntegRestRN implements MdPetIntegClienteRN
{
    public const LOCAL_QUERY = 'Q';
    public const LOCAL_CAMINHO = 'C';
    public const LOCAL_HEADER = 'H';
    public const LOCAL_CORPO = 'B';

    public const SEGUNDOS_CONEXAO = 5;
    public const SEGUNDOS_RESPOSTA = 15;

    private const MSG_ERRO_SERVICO = 'Não foi possível obter os dados na Receita Federal. Entre em contato com o administrador do SEI.';
    private const MSG_ERRO_SERVICO_SEM_CACHE = 'Não foi possível obter os dados na Receita Federal. A integração está configurada sem cache, e a consulta online falhou. Entre em contato com o administrador do SEI.';

    /**
     * @param string $strEnderecoExecucao endereço base das chamadas
     * @param string $strMetodo método HTTP da operação
     * @param string $strCaminho caminho da operação, com {nome} nos parâmetros de caminho
     * @param array<string, string> $arrLocais local de cada parâmetro de entrada, pelo campo do serviço; sem local, vai na query
     * @param array<string, string> $arrHeaders headers fixos, pelo nome
     * @param int $numSegundosResposta tempo limite da resposta
     * @param array<string, string> $arrCorpoFixo campos enviados sempre no corpo JSON, como o token do Body Authentication by Token
     */
    public function __construct(
        private string $strEnderecoExecucao,
        private string $strMetodo,
        private string $strCaminho,
        private array $arrLocais = [],
        private array $arrHeaders = [],
        private int $numSegundosResposta = self::SEGUNDOS_RESPOSTA,
        private array $arrCorpoFixo = []
    ) {
    }

    public function consultar(string $strOperacao, array $arrParametros): array
    {
        $arrEntrada = $arrParametros[$strOperacao] ?? $arrParametros;
        $strCaminho = $this->strCaminho;
        $arrQuery = [];
        $arrCorpo = [];
        $arrHeaders = $this->arrHeaders;

        foreach ($arrEntrada as $strCampo => $varValor) {
            $varValor = self::converterTexto($varValor, 'UTF-8', 'ISO-8859-1');
            switch ($this->arrLocais[$strCampo] ?? self::LOCAL_QUERY) {
                case self::LOCAL_CAMINHO:
                    $strCaminho = str_replace('{' . $strCampo . '}', rawurlencode((string) $varValor), $strCaminho);
                    break;
                case self::LOCAL_HEADER:
                    $arrHeaders[$strCampo] = (string) $varValor;
                    break;
                case self::LOCAL_CORPO:
                    $arrCorpo[$strCampo] = $varValor;
                    break;
                default:
                    $arrQuery[$strCampo] = $varValor;
            }
        }

        foreach ($this->arrCorpoFixo as $strCampo => $strValor) {
            $arrCorpo[$strCampo] = self::converterTexto($strValor, 'UTF-8', 'ISO-8859-1');
        }

        $strUrl = rtrim($this->strEnderecoExecucao, '/') . '/' . ltrim($strCaminho, '/');
        if ($arrQuery !== []) {
            $strUrl .= '?' . http_build_query($arrQuery);
        }

        try {
            $strCorpo = self::chamar($strUrl, $this->strMetodo, $arrHeaders, $arrCorpo === [] ? null : json_encode($arrCorpo), $this->numSegundosResposta);
        } catch (InfraException $e) {
            return self::falhar($e->getMessage());
        }

        $arrResposta = json_decode($strCorpo, true);
        if (!is_array($arrResposta)) {
            return self::falhar('A resposta do serviço REST não é um JSON válido.');
        }

        $arrResposta = self::converterTexto($arrResposta, 'ISO-8859-1', 'UTF-8');
        if (!empty($arrResposta['erro'])) {
            // O usuário externo vê o texto da tela, e o código e o texto do serviço ficam no log; mesesExpiraCache 0 é consulta online, sem cache
            $bolSemCache = isset($arrEntrada['mesesExpiraCache']) && (int) $arrEntrada['mesesExpiraCache'] === 0;
            $strDetalhe = trim($arrResposta['erro'] . ' - ' . ($arrResposta['resultado'] ?? ''), ' -');
            return self::falhar($bolSemCache ? self::MSG_ERRO_SERVICO_SEM_CACHE : self::MSG_ERRO_SERVICO, $strDetalhe);
        }

        return $arrResposta;
    }

    /**
     * Navega pelo caminho na resposta. O nível terminado em "[]" é uma lista, e a leitura segue pelo primeiro item dela.
     */
    public function lerDado(array $arrResposta, ?string $strCaminho): mixed
    {
        if ($strCaminho === null || trim($strCaminho) === '') {
            return null;
        }

        $varValor = $arrResposta;
        foreach (array_map('trim', explode(' - ', $strCaminho)) as $strChave) {
            $bolLista = str_ends_with($strChave, '[]');
            $strChave = $bolLista ? substr($strChave, 0, -2) : $strChave;

            if ($strChave !== '') {
                if (!is_array($varValor) || !array_key_exists($strChave, $varValor)) {
                    return null;
                }
                $varValor = $varValor[$strChave];
            }

            if ($bolLista) {
                if (!is_array($varValor) || !array_key_exists(0, $varValor)) {
                    return null;
                }
                $varValor = $varValor[0];
            }
        }

        return $varValor;
    }

    public function possuiDados(array $arrResposta, string $strRaizSoap): bool
    {
        return $arrResposta !== [] && !array_key_exists('faultstring ', $arrResposta);
    }

    /**
     * Faz a chamada HTTP com o certificado do servidor verificado e devolve o corpo da resposta 2xx.
     *
     * @param array<string, string> $arrHeaders
     * @throws InfraException na falha de conexão, de certificado, de tempo limite ou de resposta fora de 2xx
     */
    public static function chamar(string $strUrl, string $strMetodo, array $arrHeaders = [], ?string $strCorpo = null, int $numSegundosResposta = self::SEGUNDOS_RESPOSTA): string
    {
        $arrLinhasHeader = ['Accept: application/json'];
        foreach ($arrHeaders as $strNome => $strValor) {
            $arrLinhasHeader[] = $strNome . ': ' . $strValor;
        }
        if ($strCorpo !== null) {
            $arrLinhasHeader[] = 'Content-Type: application/json';
        }

        $objCurl = curl_init($strUrl);
        curl_setopt_array($objCurl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => strtoupper($strMetodo),
            CURLOPT_HTTPHEADER => $arrLinhasHeader,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CONNECTTIMEOUT => self::SEGUNDOS_CONEXAO,
            CURLOPT_TIMEOUT => $numSegundosResposta,
            CURLOPT_FOLLOWLOCATION => false,
        ]);
        if ($strCorpo !== null) {
            curl_setopt($objCurl, CURLOPT_POSTFIELDS, $strCorpo);
        }

        $strResposta = curl_exec($objCurl);
        $numHttp = (int) curl_getinfo($objCurl, CURLINFO_RESPONSE_CODE);
        $strErro = curl_error($objCurl);
        curl_close($objCurl);

        if ($strResposta === false) {
            throw new InfraException('Falha na chamada ao serviço REST: ' . $strErro);
        }

        if ($numHttp < 200 || $numHttp > 299) {
            throw new InfraException('O serviço REST respondeu com o código HTTP ' . $numHttp . '.');
        }

        return $strResposta;
    }

    /**
     * Converte os textos de um valor, inclusive dentro de arrays.
     */
    public static function converterTexto(mixed $varValor, string $strPara, string $strDe): mixed
    {
        if (is_array($varValor)) {
            return array_map(static fn(mixed $varItem): mixed => self::converterTexto($varItem, $strPara, $strDe), $varValor);
        }

        return is_string($varValor) ? mb_convert_encoding($varValor, $strPara, $strDe) : $varValor;
    }

    /**
     * @param string $strMensagem texto da falha, mostrado na tela
     * @param string|null $strDetalhe resposta do serviço, gravada no log junto com o texto da falha
     * @return array{"faultcode ": string, "faultstring ": string}
     */
    private static function falhar(string $strMensagem, ?string $strDetalhe = null): array
    {
        LogSEI::getInstance()->gravar($strDetalhe === null ? $strMensagem : $strMensagem . ' Resposta do serviço: ' . $strDetalhe, InfraLog::$INFORMACAO);
        return ['faultcode ' => 'env:Server', 'faultstring ' => $strMensagem];
    }
}
