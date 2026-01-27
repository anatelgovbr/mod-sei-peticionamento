<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 08/02/2012 - criado por bcu
*
* Versão do Gerador de Código: 1.32.1
*
* Versão no CVS: $Id$
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
  		throw new InfraException('Erro listando Extensões de Arquivos.',$e);
  	}
  }	
	
}
?>