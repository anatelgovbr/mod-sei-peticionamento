<?
/**
* ANATEL
*
* 30/08/2016 - criado por jaqueline.mendes@castgroup.com.br - CAST
* 26/08/2024 - Atualização por gabrielg.colab - SPASSU
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetHipoteseLegalINT extends InfraINT {

    public static function autoCompletarHipoteseLegal($strPalavrasPesquisa, $nivelAcesso = ''){

        $objHipoteseLegalDTO = new HipoteseLegalDTO();
        $objHipoteseLegalDTO->retTodos();
        $objHipoteseLegalDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);
        $objHipoteseLegalDTO->setStrNome('%'.$strPalavrasPesquisa. '%', InfraDTO::$OPER_LIKE);
        $objHipoteseLegalDTO->setStrStaNivelAcesso($nivelAcesso);
        $objHipoteseLegalDTO->setStrSinAtivo('S');
        $objHipoteseLegalDTO->setNumMaxRegistrosRetorno(50);
        $objHipoteseLegalRN = new HipoteseLegalRN();
        $arrObjHipoteseLegalDTO = $objHipoteseLegalRN->listar($objHipoteseLegalDTO);

        foreach($arrObjHipoteseLegalDTO as  $key=>$obj){
            $arrObjHipoteseLegalDTO[$key]->setStrNome(MdPetHipoteseLegalINT::formatarStrNome($arrObjHipoteseLegalDTO[$key]->getStrNome(), $arrObjHipoteseLegalDTO[$key]->getStrBaseLegal()));
        }

        return $arrObjHipoteseLegalDTO;

    }
	
    public static function formatarStrNome($nome, $baseLegal){
        return $nome .' ('.$baseLegal.')';
    }

    public static function montarSelectHipoteseLegal($booOnlyOptions = false, $idHipoteseLegal = null){

        $objMdPetHipoteseLegalDTO = new MdPetHipoteseLegalDTO();
        $objMdPetHipoteseLegalDTO->setStrNivelAcessoHl(ProtocoloRN::$NA_RESTRITO);
        $objMdPetHipoteseLegalDTO->setStrSinAtivo('S');
        $objMdPetHipoteseLegalDTO->retStrBaseLegal();
        $objMdPetHipoteseLegalDTO->retStrNome();
        $objMdPetHipoteseLegalDTO->retNumIdHipoteseLegalPeticionamento();
        $objMdPetHipoteseLegalDTO->setOrd("Nome", InfraDTO::$TIPO_ORDENACAO_ASC);

        $objHipoteseLegalPetRN = new MdPetHipoteseLegalRN();
        $arrObjMdPetHipoteseLegalDTO = $objHipoteseLegalPetRN->listar($objMdPetHipoteseLegalDTO);

        $strOptions = '<option value=""></option>';
        if( is_array( $arrObjMdPetHipoteseLegalDTO ) && count( $arrObjMdPetHipoteseLegalDTO ) > 0){
            foreach ($arrObjMdPetHipoteseLegalDTO as $objMdPetHipoteseLegalDTO) {
                $nomeBaseLegal = $objMdPetHipoteseLegalDTO->getStrNome() . ' (' . $objMdPetHipoteseLegalDTO->getStrBaseLegal() . ')';
                $strOptions .= '<option value="' . $objMdPetHipoteseLegalDTO->getNumIdHipoteseLegalPeticionamento() . '" '.(!is_null($idHipoteseLegal) && $idHipoteseLegal == $objMdPetHipoteseLegalDTO->getNumIdHipoteseLegalPeticionamento() ? 'selected="selected"' : '').'>';
                $strOptions .= $nomeBaseLegal;
                $strOptions .= '</option>';
            }
        }

        if($booOnlyOptions){
            return $strOptions;
        }

        return '<select id="selHipoteseLegal" class="infraSelect form-control" onchange="salvarValorHipoteseLegal(this)"
                        tabindex="'. PaginaSEIExterna::getInstance()->getProxTabDados() . '">'.$strOptions.'</select>';

    }
	
}