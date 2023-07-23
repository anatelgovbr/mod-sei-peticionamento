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

class MdPetArquivoExtensaoRN extends ArquivoExtensaoRN {
  //
  // @author Alan Campos <alan.campos@castgroup.com.br>
  //
  protected function listarAutoCompleteConectado(ArquivoExtensaoDTO $objArquivoExtensaoDTO) {
  	try {
  
  		//Valida Permissao
  		SessaoSEI::getInstance()->validarAuditarPermissao('arquivo_extensao_listar',__METHOD__,$objArquivoExtensaoDTO);
 
  
  		$objArquivoExtensaoBD = new ArquivoExtensaoBD($this->getObjInfraIBanco());
  		$objArquivoExtensaoDTO->setStrExtensao('%'.$objArquivoExtensaoDTO->getStrPalavrasPesquisa().'%',InfraDTO::$OPER_LIKE);
  		
  		$ret = $objArquivoExtensaoBD->listar($objArquivoExtensaoDTO);
  
  		//Auditoria
  
  		return $ret;
  
  	}catch(Exception $e){
  		throw new InfraException('Erro listando Extens�es de Arquivos.',$e);
  	}
  }	
	
}
?>