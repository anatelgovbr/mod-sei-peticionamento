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

class MdPetArquivoExtensaoDTO extends ArquivoExtensaoDTO {

  public function montar(){
    parent::montar();
    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'PalavrasPesquisa');
  }	

}
?>