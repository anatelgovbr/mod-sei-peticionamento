<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 08/02/2012 - criado por bcu
* 26/08/2024 - Atualização por gabrielg.colab - SPASSU
*
* Versão do Gerador de Código: 1.32.1
*
* Versão no CVS: $Id$
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetArquivoExtensaoDTO extends ArquivoExtensaoDTO {

  public function montar(){
    parent::montar();
    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'PalavrasPesquisa');
  }	

}
?>