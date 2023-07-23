<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
*
* 08/02/2012 - criado por bcu
*
* Vers�o do Gerador de C�digo: 1.32.1
*
* Vers�o no CVS: $Id$
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetArquivoExtensaoINT extends InfraINT {

  /*
   * @author Alan Campos <alan.campos@castgroup.com.br>
   * 
   */
  
  public static function autoCompletarExtensao($strExtensao){
  	  
  	$objMdPetArquivoExtensaoDTO = new MdPetArquivoExtensaoDTO();
  	$objMdPetArquivoExtensaoDTO->retNumIdArquivoExtensao();
  	$objMdPetArquivoExtensaoDTO->retStrExtensao();
  	$objMdPetArquivoExtensaoDTO->retStrDescricao();
  	$objMdPetArquivoExtensaoDTO->setNumMaxRegistrosRetorno(50);
  	$objMdPetArquivoExtensaoDTO->setOrdStrExtensao(InfraDTO::$TIPO_ORDENACAO_ASC);
  
  	if ($strExtensao!=''){
  		$objMdPetArquivoExtensaoDTO->setStrPalavrasPesquisa($strExtensao);
  	}
  	
  	$objMdPetArquivoExtensaoRN = new MdPetArquivoExtensaoRN();
  	$arrObjMdPetArquivoExtensaoDTO = $objMdPetArquivoExtensaoRN->listarAutoComplete($objMdPetArquivoExtensaoDTO);
 
  	return $arrObjMdPetArquivoExtensaoDTO;
  }
}
?>