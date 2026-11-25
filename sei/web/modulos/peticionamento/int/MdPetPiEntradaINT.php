<?php
/**
 * ANATEL
 *
 * Leitura normalizada da entrada HTTP das telas do Peticionamento Interno.
 * Centraliza o acesso a $_GET e $_POST para que nenhuma pagina leia a superglobal direto.
 *
 * 03/09/2026 - criado por emerson_b.goncalves - ANATEL
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetPiEntradaINT extends InfraINT
{
    /**
     * Prefixos dos campos de formulario montados pelo AtributoINT do core.
     */
    private static $PREFIXOS_ATRIBUTO_FORMULARIO = ['txtAtributo', 'selAtributo', 'txaAtributo', 'hdnAtributo', 'rdoAtributo', 'chkAtributo'];

    /**
     * @param string $strIndice
     * @return string
     */
    public static function getStr($strIndice)
    {
        return isset($_GET[$strIndice]) && is_scalar($_GET[$strIndice]) ? trim((string) $_GET[$strIndice]) : '';
    }

    /**
     * @param string $strIndice
     * @return int|null
     */
    public static function getInt($strIndice)
    {
        $strValor = self::getStr($strIndice);

        return $strValor === '' ? null : (int) $strValor;
    }

    /**
     * @param string $strIndice
     * @return string
     */
    public static function postStr($strIndice)
    {
        return isset($_POST[$strIndice]) && is_scalar($_POST[$strIndice]) ? trim((string) $_POST[$strIndice]) : '';
    }

    /**
     * Vazio e o texto 'null', que o select do InfraPHP envia na opcao em branco, viram null, como nos setters do InfraDTO.
     *
     * @param string $strIndice
     * @return int|null
     */
    public static function postInt($strIndice)
    {
        $strValor = self::postStr($strIndice);

        return $strValor === '' || $strValor === 'null' ? null : (int) $strValor;
    }

    /**
     * Valor restrito a um conjunto conhecido de opcoes. Fora dele, devolve vazio.
     *
     * @param string $strIndice
     * @param array $arrValoresValidos valores aceitos, ou array associativo cujas chaves sao os valores
     * @return string
     */
    public static function postEnum($strIndice, $arrValoresValidos)
    {
        $strValor = self::postStr($strIndice);

        if ($strValor === '') {
            return '';
        }

        $arrPermitidos = array_keys($arrValoresValidos) === range(0, count($arrValoresValidos) - 1)
            ? $arrValoresValidos
            : array_keys($arrValoresValidos);

        return in_array($strValor, $arrPermitidos, true) ? $strValor : '';
    }

    /**
     * @param string $strIndice
     * @return int[]
     */
    public static function postArrInt($strIndice)
    {
        if (!isset($_POST[$strIndice]) || !is_array($_POST[$strIndice])) {
            return [];
        }

        $arrRetorno = [];

        foreach ($_POST[$strIndice] as $mixValor) {
            if (is_scalar($mixValor) && trim((string) $mixValor) !== '') {
                $arrRetorno[] = (int) $mixValor;
            }
        }

        return $arrRetorno;
    }

    /**
     * Campos do formulario do Documento Principal, so os montados pelo AtributoINT do core.
     *
     * @return array
     */
    public static function postArrAtributosFormulario()
    {
        $arrRetorno = [];

        foreach ($_POST as $strIndice => $mixValor) {
            if (self::_isCampoAtributoFormulario($strIndice) && is_scalar($mixValor)) {
                $arrRetorno[$strIndice] = (string) $mixValor;
            }
        }

        return $arrRetorno;
    }

    /**
     * Devolve ao $_POST os campos guardados na sessao, porque o AtributoINT do core le os valores de la.
     * Marca o formulario como ja enviado, como faz o botao Salvar do core, para o AtributoINT nao repor o valor padrao
     * em caixa que o usuario desmarcou ou em opcao que ele deixou sem escolha.
     *
     * @param array $arrValores
     * @return void
     */
    public static function restaurarAtributosFormulario($arrValores)
    {
        if (!is_array($arrValores)) {
            return;
        }

        foreach ($arrValores as $strIndice => $mixValor) {
            if (self::_isCampoAtributoFormulario($strIndice) && is_scalar($mixValor)) {
                $_POST[$strIndice] = (string) $mixValor;
            }
        }

        $_POST['sbmFormularioProcessar'] = 'Salvar';
    }

    /**
     * @param string $strCampo
     * @return bool
     */
    public static function isArquivoEnviado($strCampo)
    {
        return isset($_FILES[$strCampo]) && is_array($_FILES[$strCampo]);
    }

    /**
     * @param string $strCampo
     * @return int
     */
    public static function obterTamanhoArquivoEnviado($strCampo)
    {
        return self::isArquivoEnviado($strCampo) && isset($_FILES[$strCampo]['size']) ? (int) $_FILES[$strCampo]['size'] : 0;
    }

    /**
     * @param string $strIndice
     * @return bool
     */
    private static function _isCampoAtributoFormulario($strIndice)
    {
        foreach (self::$PREFIXOS_ATRIBUTO_FORMULARIO as $strPrefixo) {
            if (strpos($strIndice, $strPrefixo) === 0 && ctype_digit(substr($strIndice, strlen($strPrefixo)))) {
                return true;
            }
        }

        return false;
    }
}
