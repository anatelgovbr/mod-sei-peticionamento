<?
/**
* ANATEL
*
* 30/08/2016 - criado por jaqueline.mendes@castgroup.com.br - CAST
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
	
}