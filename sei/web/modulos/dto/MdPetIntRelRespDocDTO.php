<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 22/03/2017 - criado por jaqueline.cast
*
* Versão do Gerador de Código: 1.40.1
*/

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntRelRespDocDTO extends InfraDTO {

  public function getStrNomeTabela() {
  	 return 'md_pet_int_rel_resp_doc';
  }

  public function montar() {

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntRespDocumento', 'id_md_pet_int_resp_documento');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntDestResposta', 'id_md_pet_int_dest_resposta');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DBL, 'IdDocumento', 'id_documento');

    $this->configurarPK('IdMdPetIntRespDocumento',InfraDTO::$TIPO_PK_NATIVA);

    $this->configurarFK('IdMdPetIntDestResposta','md_pet_int_dest_resposta','id_md_pet_int_dest_resposta');
    $this->configurarFK('IdDocumento','documento','id_documento');
  }
}
?>