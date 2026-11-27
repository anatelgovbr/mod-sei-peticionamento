<?php
/**
 * Centralizes functional validations for whitelist contexts.
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetWhitelistINT extends InfraINT
{
    /**
     * Validates a formatted protocol as a representation process for the AJAX contract.
     */
    public static function validarProcessoRepresentacao($strProtocolo): string
    {
        $objMdPetVinculoDTO = new MdPetVinculoDTO();
        $objMdPetVinculoDTO->retStrProtocoloFormatado();
        $objMdPetVinculoDTO->retStrNomeTipoProcedimento();
        $objMdPetVinculoDTO->retDblIdProtocolo();
        $objMdPetVinculoDTO->setDistinct(true);
        $objMdPetVinculoDTO->setStrProtocoloFormatado($strProtocolo);

        $arrObjMdPetVinculoDTO = (new MdPetVinculoRN())->listar($objMdPetVinculoDTO);

        $xml = '<dados>';
        if (is_null($arrObjMdPetVinculoDTO) || count($arrObjMdPetVinculoDTO) === 0) {
            $xml .= "<success>false</success>\n";
            $xml .= "<msg>O processo informado n&#227;o existe ou n&#227;o corresponde a um processo de representa&#231;&#227;o.</msg>\n";
            $xml .= '</dados>';
            return $xml;
        }

        $xml .= "<success>true</success>\n";
        $xml .= '<idProcesso>'
            . InfraString::formatarXML((string) $arrObjMdPetVinculoDTO[0]->getDblIdProtocolo())
            . "</idProcesso>\n";
        $xml .= '<tipoProcedimento>'
            . InfraString::formatarXML((string) $arrObjMdPetVinculoDTO[0]->getStrNomeTipoProcedimento())
            . "</tipoProcedimento>\n";
        $xml .= '</dados>';

        return $xml;
    }

    /**
     * Validates in one query that every submitted ID belongs to a representation process.
     *
     * @param array<int, int|string> $arrIdObjetos
     * @throws InfraException
     */
    public static function validarProcessosRepresentacao(array $arrIdObjetos): void
    {
        $arrIdObjetos = self::normalizarIdsPositivos($arrIdObjetos);
        if (empty($arrIdObjetos)) {
            return;
        }

        $objMdPetVinculoDTO = new MdPetVinculoDTO();
        $objMdPetVinculoDTO->setDblIdProtocolo($arrIdObjetos, InfraDTO::$OPER_IN);
        $objMdPetVinculoDTO->retDblIdProtocolo();
        $objMdPetVinculoDTO->setDistinct(true);

        $arrObjMdPetVinculoDTO = (new MdPetVinculoRN())->listar($objMdPetVinculoDTO);
        $arrProcessosRepresentacao = [];

        if (is_array($arrObjMdPetVinculoDTO)) {
            foreach ($arrObjMdPetVinculoDTO as $objMdPetVinculoDTO) {
                $arrProcessosRepresentacao[(string) $objMdPetVinculoDTO->getDblIdProtocolo()] = true;
            }
        }

        $arrIdObjetosInvalidos = [];
        foreach ($arrIdObjetos as $numIdObjeto) {
            if (!isset($arrProcessosRepresentacao[(string) $numIdObjeto])) {
                $arrIdObjetosInvalidos[] = $numIdObjeto;
            }
        }

        if (!empty($arrIdObjetosInvalidos)) {
            throw new InfraException(
                'Processo(s) informado(s) nao corresponde(m) a processo(s) de representacao: '
                . implode(', ', $arrIdObjetosInvalidos)
                . '.'
            );
        }
    }

    /**
     * Normalizes and deduplicates positive integer IDs without accepting overflow.
     *
     * @param array<int, mixed> $arrIdObjetos
     * @return int[]
     * @throws InfraException
     */
    private static function normalizarIdsPositivos(array $arrIdObjetos): array
    {
        $arrIdObjetosNormalizados = [];
        $strPhpIntMax = (string) PHP_INT_MAX;

        foreach ($arrIdObjetos as $mixIdObjeto) {
            if (!is_int($mixIdObjeto) && !is_string($mixIdObjeto)) {
                throw new InfraException('A lista de processos de representacao contem um identificador invalido.');
            }

            $strIdObjeto = trim((string) $mixIdObjeto);
            if ($strIdObjeto === '' || preg_match('/^[0-9]+$/D', $strIdObjeto) !== 1) {
                throw new InfraException('A lista de processos de representacao contem um identificador invalido.');
            }

            $strIdObjeto = ltrim($strIdObjeto, '0');
            if ($strIdObjeto === '') {
                throw new InfraException('A lista de processos de representacao deve conter apenas identificadores positivos.');
            }

            if (strlen($strIdObjeto) > strlen($strPhpIntMax)
                || (strlen($strIdObjeto) === strlen($strPhpIntMax)
                    && strcmp($strIdObjeto, $strPhpIntMax) > 0)) {
                throw new InfraException('A lista de processos de representacao contem um identificador fora do limite suportado.');
            }

            $numIdObjeto = (int) $strIdObjeto;
            $arrIdObjetosNormalizados[(string) $numIdObjeto] = $numIdObjeto;
        }

        return array_values($arrIdObjetosNormalizados);
    }
}