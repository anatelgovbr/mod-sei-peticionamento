<?php
/**
 * ANATEL
 *
 * 17/11/2026 - criado para a integração REST do Mapeamento das Integrações (ADR-PET-011)
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

/**
 * Ponto único de escolha do cliente de integração pelo tipo gravado em sta_tp_cliente_ws: "R" é REST, e qualquer outro
 * valor segue com o SOAP, como antes do Tipo de Integração. Um tipo novo é uma classe nova e uma escolha aqui.
 */
final class MdPetIntegFabricaRN
{
    public const TIPO_SOAP = 'S';
    public const TIPO_REST = 'R';

    public const PARAMETRO_ENTRADA = 'E';
    public const PARAMETRO_SAIDA = 'P';
    public const PARAMETRO_HEADER = 'H';

    public const AUTENTICACAO_SEM = 'N';
    public const AUTENTICACAO_HEADER = 'H';
    public const AUTENTICACAO_CORPO = 'B';
    /** Nome da linha de entrada do token do Body Authentication by Token (ADR-PET-013). */
    public const ENTRADA_TOKEN = 'authorization';
    public const MASCARA_DADO_RESTRITO = '*****';

    /**
     * @param MdPetIntegracaoDTO $objIntegracao com TpClienteWs, EnderecoWsdl e NuVersao; no REST, também EnderecoExecucao,
     *                                          MetodoHttp e CaminhoOperacao
     * @param array<MdPetIntegParametroDTO> $arrObjParametros mapeamento da integração; no REST, com TpParametro, Nome,
     *                                                       NomeCampo, ValorPadrao, StaLocalParametro e SinDadoConfidencial
     * @return MdPetIntegClienteRN
     */
    public static function criarCliente(MdPetIntegracaoDTO $objIntegracao, array $arrObjParametros = []): MdPetIntegClienteRN
    {
        if ($objIntegracao->getStrTpClienteWs() !== self::TIPO_REST) {
            return new MdPetIntegSoapRN($objIntegracao->getStrEnderecoWsdl(), $objIntegracao->getDblNuVersao());
        }

        $arrLocais = [];
        $arrHeaders = [];
        $arrCorpoFixo = [];
        foreach ($arrObjParametros as $objParametro) {
            $strTipo = $objParametro->getStrTpParametro();

            if ($strTipo === self::PARAMETRO_ENTRADA && $objParametro->isSetStrNome() && $objParametro->getStrNome() === self::ENTRADA_TOKEN) {
                // O token vai sempre no corpo, no campo escolhido, e só é desfeito aqui (ADR-PET-013)
                if ($objParametro->isSetStrSinDadoConfidencial() && $objParametro->getStrSinDadoConfidencial() === 'S' && !InfraString::isBolVazia($objParametro->getStrNomeCampo())) {
                    $arrCorpoFixo[$objParametro->getStrNomeCampo()] = MdPetIntegracaoINT::desfazerOfuscacao((string) $objParametro->getStrValorPadrao());
                }
            } elseif ($strTipo === self::PARAMETRO_HEADER) {
                $bolRestrito = $objParametro->isSetStrSinDadoConfidencial() && $objParametro->getStrSinDadoConfidencial() === 'S';
                $strValor = (string) $objParametro->getStrValorPadrao();
                $arrHeaders[$objParametro->getStrNome()] = $bolRestrito ? MdPetIntegracaoINT::desfazerOfuscacao($strValor) : $strValor;
            } elseif ($strTipo === self::PARAMETRO_ENTRADA && !InfraString::isBolVazia($objParametro->getStrNomeCampo())
                && !InfraString::isBolVazia($objParametro->getStrStaLocalParametro())) {
                $arrLocais[$objParametro->getStrNomeCampo()] = $objParametro->getStrStaLocalParametro();
            }
        }

        return new MdPetIntegRestRN(
            (string) $objIntegracao->getStrEnderecoExecucao(),
            (string) $objIntegracao->getStrMetodoHttp(),
            (string) $objIntegracao->getStrCaminhoOperacao(),
            $arrLocais,
            $arrHeaders,
            arrCorpoFixo: $arrCorpoFixo
        );
    }

    /**
     * @param string|null $strTipo tipo de integração escolhido na tela
     * @param string|null $strEndereco endereço do WSDL ou do documento OpenAPI
     * @param mixed $varVersao versão SOAP
     * @return MdPetIntegDescobertaRN
     */
    public static function criarDescoberta(?string $strTipo, ?string $strEndereco, mixed $varVersao): MdPetIntegDescobertaRN
    {
        if ($strTipo === self::TIPO_REST) {
            return new MdPetIntegOpenApiRN($strEndereco);
        }

        return new MdPetIntegSoapRN($strEndereco, $varVersao);
    }
}
