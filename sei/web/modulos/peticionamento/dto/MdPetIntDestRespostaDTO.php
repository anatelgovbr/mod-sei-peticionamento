<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 22/03/2017 - criado por jaqueline.cast
*
* Versão do Gerador de Código: 1.40.0
*/

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntDestRespostaDTO extends InfraDTO {

  public function getStrNomeTabela() {
  	 return 'md_pet_int_dest_resposta';
  }

  public function montar() {

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntDestResposta', 'id_md_pet_int_dest_resposta');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntRelDestinatario', 'id_md_pet_int_rel_dest');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'Ip', 'ip');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'Data', 'data');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntRelTipoResp', 'id_md_pet_int_rel_tipo_resp');
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdUsuario', 'id_usuario');

    $this->configurarPK('IdMdPetIntDestResposta',InfraDTO::$TIPO_PK_NATIVA);
    
    $this->configurarFK('IdMdPetIntRelDestinatario','md_pet_int_rel_dest','id_md_pet_int_rel_dest');
    $this->configurarFK('IdMdPetIntRelTipoResp','md_pet_int_rel_tipo_resp','id_md_pet_int_rel_tipo_resp');
    $this->configurarFK('IdUsuario','usuario','id_usuario');

    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntimacao', 'id_md_pet_intimacao', 'md_pet_int_rel_dest');


  }
}
?>