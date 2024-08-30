<?

/**
 * ANATEL
 *
 * 30/03/2016 - criado por jaqueline.mendes@cast.com.br - CAST
 * 26/08/2024 - Atualização por gabrielg.colab - SPASSU
 *
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetTipoPoderLegalINT extends InfraINT {

    public static function saidaPoderesLegais($objDTO){

    }

    /**
     * Função responsável por montar os options do select "Hipótese Legal" para a tela de vinculacao
     * @return string
     */
    public static function montarOptionsTipoPoder($default)
    {

        $objMdPetTipoPoderRN = new MdPetTipoPoderLegalRN();
        $objMdPetTipoPoderDTO = new MdPetTipoPoderLegalDTO();
        $objMdPetTipoPoderDTO->retTodos();
        $arrObjTipoPoder = $objMdPetTipoPoderRN->listar($objMdPetTipoPoderDTO);

        $strOptions = '<option value=""></option>';

        foreach ($arrObjTipoPoder as $objObjTipoPoder) {
            $selected = $default == $objObjTipoPoder->getNumIdTipoPoderLegal() ? 'selected="selected"' : '';
            $strOptions .= '<option value="' . $objObjTipoPoder->getNumIdTipoPoderLegal() . '" '.$selected.'>';
            $strOptions .= $objObjTipoPoder->getStrNome();
            $strOptions .= '</option>';
        }

        return $strOptions;
        
    }

    public static function montarArrSelect($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado){
        
        $objMdPetTipoPoderRN = new MdPetTipoPoderLegalRN();
        $objMdPetTipoPoderDTO = new MdPetTipoPoderLegalDTO();
        $objMdPetTipoPoderDTO->retTodos();
        $arrObjTipoPoder = $objMdPetTipoPoderRN->listar($objMdPetTipoPoderDTO);
        
        return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjTipoPoder, 'IdTipoPoderLegal', 'Nome');
    
    }

}