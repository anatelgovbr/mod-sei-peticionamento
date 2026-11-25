<?php
/**
 * ANATEL
 *
 * 17/11/2026 - criado para a integração REST do Mapeamento das Integrações (ADR-PET-011)
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

/**
 * Contrato da descoberta das operações e dos campos de um serviço, usado só pela tela de cadastro da integração.
 */
interface MdPetIntegDescobertaRN
{
    /**
     * @return array<int|string, string> operações do serviço, na ordem de exibição
     */
    public function listarOperacoes(): array;

    /**
     * Campos de entrada ("e") ou de saída ("s") da operação, no formato de caminho com " - " que o mapeamento grava.
     *
     * @param string $strOperacao
     * @param string $strTipo
     * @return list<array{campo: string, local: string|null}> local do campo de entrada na chamada; null quando não se aplica
     */
    public function listarCampos(string $strOperacao, string $strTipo): array;

    /**
     * Endereço de execução sugerido pelo documento do serviço, quando o tipo de integração usa um.
     *
     * @param string $strEnderecoDocumento endereço informado na tela
     * @return string|null
     */
    public function sugerirEnderecoExecucao(string $strEnderecoDocumento): ?string;

    /**
     * Mensagem mostrada na tela quando a descoberta falha.
     *
     * @param Exception $objExcecao
     * @return string
     */
    public function descreverFalha(Exception $objExcecao): string;
}
