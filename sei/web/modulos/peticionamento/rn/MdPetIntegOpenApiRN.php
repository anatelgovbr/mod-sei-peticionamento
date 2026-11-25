<?php
/**
 * ANATEL
 *
 * 17/11/2026 - criado para a integração REST do Mapeamento das Integrações (ADR-PET-011)
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

/**
 * Descoberta das operações e dos campos de um serviço REST pelo documento OpenAPI 2.0 (Swagger) ou 3.x. Cada operação é
 * um par método e caminho; os campos de entrada saem dos parâmetros e do corpo JSON, e os de saída, do esquema da
 * resposta de sucesso, no mesmo formato de caminho do SOAP, com "[]" no nível que é lista. Só resolve referências
 * internas do documento.
 */
class MdPetIntegOpenApiRN implements MdPetIntegDescobertaRN
{
    private const METODOS = ['get', 'post', 'put', 'patch', 'delete'];
    private const LOCAIS = ['query' => MdPetIntegRestRN::LOCAL_QUERY, 'path' => MdPetIntegRestRN::LOCAL_CAMINHO, 'header' => MdPetIntegRestRN::LOCAL_HEADER];
    private const NIVEIS_MAXIMOS = 10;

    /** @var array<string, mixed>|null documento decodificado, com os textos em ISO-8859-1 */
    private ?array $arrDocumento = null;

    /**
     * O documento só é baixado na primeira chamada, para a falha aparecer como mensagem da descoberta.
     */
    public function __construct(private ?string $strEndereco = null)
    {
    }

    /**
     * Descoberta a partir do conteúdo do documento, para o serviço que não publica o endereço dele.
     *
     * @throws InfraException quando o conteúdo não é um documento OpenAPI 2.0 ou 3.x
     */
    public static function deJson(string $strJson): self
    {
        $objDescoberta = new self();
        $objDescoberta->arrDocumento = self::decodificar($strJson);

        return $objDescoberta;
    }

    /**
     * @return array<string, mixed>
     * @throws InfraException quando o conteúdo não é um documento OpenAPI 2.0 ou 3.x
     */
    private static function decodificar(string $strJson): array
    {
        $arrDocumento = json_decode($strJson, true);
        $bolOpenApi = is_array($arrDocumento) && is_array($arrDocumento['paths'] ?? null)
            && (($arrDocumento['swagger'] ?? null) === '2.0' || str_starts_with((string) ($arrDocumento['openapi'] ?? ''), '3.'));

        if (!$bolOpenApi) {
            throw new InfraException('O conteúdo não é um documento OpenAPI 2.0 ou 3.x.');
        }

        return MdPetIntegRestRN::converterTexto($arrDocumento, 'ISO-8859-1', 'UTF-8');
    }

    /**
     * @return array<string, mixed>
     * @throws InfraException quando o endereço não responde ou o conteúdo não é um documento OpenAPI
     */
    private function documento(): array
    {
        return $this->arrDocumento ??= self::decodificar(MdPetIntegRestRN::chamar((string) $this->strEndereco, 'GET'));
    }

    public function listarOperacoes(): array
    {
        $arrOperacoes = [];
        foreach ($this->documento()['paths'] as $strCaminho => $arrItem) {
            foreach (self::METODOS as $strMetodo) {
                if (is_array($arrItem[$strMetodo] ?? null)) {
                    $arrOperacoes[] = strtoupper($strMetodo) . ' ' . $strCaminho;
                }
            }
        }
        sort($arrOperacoes);

        return $arrOperacoes;
    }

    public function listarCampos(string $strOperacao, string $strTipo): array
    {
        [$strMetodo, $strCaminho] = self::separarOperacao($strOperacao);
        $arrItem = $this->documento()['paths'][$strCaminho] ?? null;
        $arrOperacao = is_array($arrItem) ? ($arrItem[strtolower($strMetodo)] ?? null) : null;

        if (!is_array($arrOperacao)) {
            throw new InfraException('A operação ' . $strOperacao . ' não existe no documento OpenAPI.');
        }

        return $strTipo == 'e' ? $this->listarEntrada($arrItem, $arrOperacao) : $this->listarSaida($arrOperacao);
    }

    public function descreverFalha(Exception $objExcecao): string
    {
        return 'Erro na leitura do documento OpenAPI: ' . $objExcecao->getMessage();
    }

    /**
     * Endereço de execução sugerido pelo documento: o servidor do OpenAPI 3.x ou o esquema, o host e o basePath do
     * Swagger 2.0, completados com o endereço do documento quando não declarados.
     */
    public function sugerirEnderecoExecucao(string $strEnderecoDocumento): ?string
    {
        $arrUrl = parse_url($strEnderecoDocumento);
        $strOrigem = isset($arrUrl['scheme'], $arrUrl['host']) ? $arrUrl['scheme'] . '://' . $arrUrl['host'] . (isset($arrUrl['port']) ? ':' . $arrUrl['port'] : '') : null;

        if (isset($this->documento()['openapi'])) {
            $strServidor = $this->documento()['servers'][0]['url'] ?? null;
            if (!is_string($strServidor) || $strServidor === '') {
                return $strOrigem;
            }
            return str_starts_with($strServidor, '/') && $strOrigem !== null ? $strOrigem . $strServidor : $strServidor;
        }

        $strEsquema = $this->documento()['schemes'][0] ?? ($arrUrl['scheme'] ?? null);
        $strHost = $this->documento()['host'] ?? (isset($arrUrl['host']) ? $arrUrl['host'] . (isset($arrUrl['port']) ? ':' . $arrUrl['port'] : '') : null);
        if ($strEsquema === null || $strHost === null) {
            return null;
        }

        return $strEsquema . '://' . $strHost . ($this->documento()['basePath'] ?? '');
    }

    /**
     * @return array{string, string} método e caminho
     */
    public static function separarOperacao(string $strOperacao): array
    {
        $arrPartes = explode(' ', trim($strOperacao), 2);
        if (count($arrPartes) !== 2) {
            throw new InfraException('Operação REST inválida: ' . $strOperacao . '.');
        }

        return [strtoupper($arrPartes[0]), $arrPartes[1]];
    }

    /**
     * @param array<string, mixed> $arrItem
     * @param array<string, mixed> $arrOperacao
     * @return list<array{campo: string, local: string|null}>
     */
    private function listarEntrada(array $arrItem, array $arrOperacao): array
    {
        $arrCampos = [];
        foreach (array_merge($arrItem['parameters'] ?? [], $arrOperacao['parameters'] ?? []) as $arrParametro) {
            $arrParametro = $this->resolver($arrParametro);
            $strLocal = $arrParametro['in'] ?? '';

            if ($strLocal === 'body') {
                $arrCampos = array_merge($arrCampos, $this->listarCorpo($arrParametro['schema'] ?? []));
            } elseif (isset(self::LOCAIS[$strLocal], $arrParametro['name'])) {
                $arrCampos[] = ['campo' => (string) $arrParametro['name'], 'local' => self::LOCAIS[$strLocal]];
            }
        }

        $arrCorpoOpenApi3 = $this->resolver($arrOperacao['requestBody'] ?? []);
        if (isset($arrCorpoOpenApi3['content']) && is_array($arrCorpoOpenApi3['content'])) {
            $arrConteudo = $arrCorpoOpenApi3['content']['application/json'] ?? reset($arrCorpoOpenApi3['content']);
            $arrCampos = array_merge($arrCampos, $this->listarCorpo($arrConteudo['schema'] ?? []));
        }

        usort($arrCampos, static fn(array $a, array $b): int => strcmp($a['campo'], $b['campo']));

        return $arrCampos;
    }

    /**
     * @param array<string, mixed> $arrEsquema
     * @return list<array{campo: string, local: string}>
     */
    private function listarCorpo(array $arrEsquema): array
    {
        $arrCampos = [];
        foreach (array_keys($this->resolver($arrEsquema)['properties'] ?? []) as $strCampo) {
            $arrCampos[] = ['campo' => (string) $strCampo, 'local' => MdPetIntegRestRN::LOCAL_CORPO];
        }

        return $arrCampos;
    }

    /**
     * @param array<string, mixed> $arrOperacao
     * @return list<array{campo: string, local: null}>
     */
    private function listarSaida(array $arrOperacao): array
    {
        $arrRespostas = $arrOperacao['responses'] ?? [];
        $arrResposta = $arrRespostas['200'] ?? $arrRespostas[200] ?? null;
        if ($arrResposta === null) {
            foreach ($arrRespostas as $strCodigo => $arrCandidata) {
                if (preg_match('/^2\d\d$/', (string) $strCodigo)) {
                    $arrResposta = $arrCandidata;
                    break;
                }
            }
        }

        $arrResposta = $this->resolver($arrResposta ?? []);
        $arrEsquema = $arrResposta['schema'] ?? null;
        if ($arrEsquema === null && is_array($arrResposta['content'] ?? null)) {
            $arrConteudo = $arrResposta['content']['application/json'] ?? reset($arrResposta['content']);
            $arrEsquema = $arrConteudo['schema'] ?? null;
        }

        $arrCaminhos = [];
        $this->achatar(is_array($arrEsquema) ? $arrEsquema : [], '', 0, $arrCaminhos);

        return array_map(static fn(string $strCampo): array => ['campo' => $strCampo, 'local' => null], $arrCaminhos);
    }

    /**
     * Junta em $arrCaminhos o caminho de cada campo simples do esquema.
     *
     * @param array<string, mixed> $arrEsquema
     * @param list<string> $arrCaminhos
     */
    private function achatar(array $arrEsquema, string $strPrefixo, int $numNivel, array &$arrCaminhos): void
    {
        if ($numNivel > self::NIVEIS_MAXIMOS) {
            return;
        }

        $arrEsquema = $this->resolver($arrEsquema);
        $arrPropriedades = $arrEsquema['properties'] ?? [];
        foreach ($arrEsquema['allOf'] ?? [] as $arrParte) {
            $arrPropriedades += $this->resolver($arrParte)['properties'] ?? [];
        }

        if (($arrEsquema['type'] ?? null) === 'array' || isset($arrEsquema['items'])) {
            $this->achatar($arrEsquema['items'] ?? [], $strPrefixo . '[]', $numNivel + 1, $arrCaminhos);
            return;
        }

        if ($arrPropriedades === []) {
            if ($strPrefixo !== '') {
                $arrCaminhos[] = $strPrefixo;
            }
            return;
        }

        foreach ($arrPropriedades as $strNome => $arrPropriedade) {
            $strCaminho = $strPrefixo === '' ? (string) $strNome : $strPrefixo . ' - ' . $strNome;
            $this->achatar(is_array($arrPropriedade) ? $arrPropriedade : [], $strCaminho, $numNivel + 1, $arrCaminhos);
        }
    }

    /**
     * Segue a referência "$ref" interna do documento, como "#/definitions/PessoaJuridica".
     *
     * @param mixed $varObjeto
     * @return array<string, mixed>
     */
    private function resolver(mixed $varObjeto, int $numNivel = 0): array
    {
        if (!is_array($varObjeto)) {
            return [];
        }

        $strRef = $varObjeto['$ref'] ?? null;
        if (!is_string($strRef)) {
            return $varObjeto;
        }

        if (!str_starts_with($strRef, '#/') || $numNivel > self::NIVEIS_MAXIMOS) {
            throw new InfraException('Referência não suportada no documento OpenAPI: ' . $strRef . '.');
        }

        $varAlvo = $this->documento();
        foreach (explode('/', substr($strRef, 2)) as $strParte) {
            $strParte = str_replace(['~1', '~0'], ['/', '~'], $strParte);
            if (!is_array($varAlvo) || !array_key_exists($strParte, $varAlvo)) {
                throw new InfraException('Referência inexistente no documento OpenAPI: ' . $strRef . '.');
            }
            $varAlvo = $varAlvo[$strParte];
        }

        return $this->resolver($varAlvo, $numNivel + 1);
    }
}
