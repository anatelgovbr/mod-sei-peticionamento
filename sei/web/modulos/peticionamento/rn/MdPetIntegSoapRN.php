<?php
/**
 * ANATEL
 *
 * 17/11/2026 - criado para a integração REST do Mapeamento das Integrações (ADR-PET-011)
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

/**
 * Adaptador do MdPetSoapClienteRN aos contratos de consulta e de descoberta, sem alterar o cliente SOAP: repassa as
 * chamadas e devolve o mesmo resultado de antes do contrato. O cliente SOAP só é criado na primeira chamada, porque o
 * construtor dele já lê o WSDL.
 */
class MdPetIntegSoapRN implements MdPetIntegClienteRN, MdPetIntegDescobertaRN
{
    private ?MdPetSoapClienteRN $objClienteSoap = null;

    /**
     * @param string|null $strEnderecoWsdl
     * @param mixed $varVersao versão SOAP gravada na integração
     */
    public function __construct(private ?string $strEnderecoWsdl, private mixed $varVersao)
    {
    }

    public function consultar(string $strOperacao, array $arrParametros): array
    {
        return $this->obterClienteSoap()->execOperacao($strOperacao, $arrParametros);
    }

    public function lerDado(array $arrResposta, ?string $strCaminho): mixed
    {
        return MdPetIntegracaoINT::acessarDadoPorChave($arrResposta, $strCaminho);
    }

    public function possuiDados(array $arrResposta, string $strRaizSoap): bool
    {
        return array_key_exists($strRaizSoap, $arrResposta);
    }

    public function listarOperacoes(): array
    {
        return $this->obterClienteSoap()->getFunctions();
    }

    public function listarCampos(string $strOperacao, string $strTipo): array
    {
        $arrTipos = $this->obterClienteSoap()->getParametrosEntradaSaidaWsdl();
        $arrParametro = [];

        if ($strTipo == 'e') {
            MdPetIntegracaoINT::trataDadosEntrada($arrTipos, $strOperacao, $arrParametro);
        } else {
            MdPetIntegracaoINT::trataDadosSaida($arrTipos, $strOperacao, $arrParametro);
        }

        ksort($arrParametro);
        $arrCampos = [];

        foreach ($arrParametro as $key => $arrayParam) {
            if (!is_array($arrayParam)) {
                $arrCampos[] = ['campo' => (string) $arrayParam, 'local' => null];
                continue;
            }

            ksort($arrayParam);
            foreach ($arrayParam as $chave => $item) {
                if (!is_array($item)) {
                    $arrCampos[] = ['campo' => $key . ' - ' . $item, 'local' => null];
                    continue;
                }

                foreach ($item as $chaveItem => $value) {
                    $arrCampos[] = ['campo' => $key . ' - ' . $chave . ' - ' . $chaveItem, 'local' => null];
                }
            }
        }

        return $arrCampos;
    }

    public function sugerirEnderecoExecucao(string $strEnderecoDocumento): ?string
    {
        return null;
    }

    public function descreverFalha(Exception $objExcecao): string
    {
        return 'Erro na conexão SOAP: ' . $objExcecao->getMessage();
    }

    private function obterClienteSoap(): MdPetSoapClienteRN
    {
        return $this->objClienteSoap ??= new MdPetSoapClienteRN($this->strEnderecoWsdl, ['soap_version' => $this->varVersao]);
    }
}
