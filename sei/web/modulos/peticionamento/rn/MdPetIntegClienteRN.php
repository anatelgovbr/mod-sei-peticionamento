<?php
/**
 * ANATEL
 *
 * 17/11/2026 - criado para a integração REST do Mapeamento das Integrações (ADR-PET-011)
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

/**
 * Contrato da consulta a um serviço de integração. Os pontos que consultam a Receita dependem só deste contrato; cada tipo
 * de integração é uma implementação, escolhida por MdPetIntegFabricaRN.
 */
interface MdPetIntegClienteRN
{
    /**
     * Executa a operação. A falha do serviço volta com as chaves "faultcode " e "faultstring ", como no cliente SOAP.
     *
     * @param string $strOperacao operação gravada na integração
     * @param array<string, mixed> $arrParametros parâmetros de entrada agrupados pela operação
     * @return array<string, mixed>
     */
    public function consultar(string $strOperacao, array $arrParametros): array;

    /**
     * Valor da resposta no caminho gravado no mapeamento de saída, com os níveis separados por " - ".
     *
     * @param array<string, mixed> $arrResposta
     * @param string|null $strCaminho
     * @return mixed null quando o caminho não existe na resposta
     */
    public function lerDado(array $arrResposta, ?string $strCaminho): mixed;

    /**
     * Indica se a resposta traz os dados consultados.
     *
     * @param array<string, mixed> $arrResposta
     * @param string $strRaizSoap nível raiz da resposta SOAP da operação
     * @return bool
     */
    public function possuiDados(array $arrResposta, string $strRaizSoap): bool;
}
