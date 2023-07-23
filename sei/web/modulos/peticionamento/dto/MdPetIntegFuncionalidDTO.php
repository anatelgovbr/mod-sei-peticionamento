<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
*
* 25/01/2018 - criado por Usu�rio
*
* Vers�o do Gerador de C�digo: 1.41.0
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntegFuncionalidDTO extends InfraDTO {

  public function getStrNomeTabela() {
  	 return 'md_pet_adm_integ_funcion';
  }

  public function montar() {

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntegFuncionalid', 'id_md_pet_adm_integ_funcion');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'Nome', 'nome');

    $this->configurarPK('IdMdPetIntegFuncionalid',InfraDTO::$TIPO_PK_NATIVA);

  }
}
