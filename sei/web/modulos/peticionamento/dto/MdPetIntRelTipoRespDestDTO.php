<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 08/12/2016 - criado por Marcelo Bezerra - CAST
*
* Versão do Gerador de Código: 1.39.0
*/

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntRelTipoRespDestDTO extends InfraDTO {

  public function getStrNomeTabela() {
  	 return 'md_pet_int_rel_tpo_res_des';
  }

  public function montar() {

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
                                   'IdMdPetIntTipoRespDest',
                                   'id_md_pet_int_rel_tipo_res_des');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
                                   'IdMdPetIntRelTipoResp',
                                   'id_md_pet_int_rel_tipo_resp');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
                                   'IdMdPetIntRelDest',
                                   'id_md_pet_int_rel_dest');

	$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'DataLimite', 'data_limite');

	$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'DataProrrogada', 'data_prorrogada');
    
    $this->configurarPK('IdMdPetIntTipoRespDest',InfraDTO::$TIPO_PK_NATIVA);

      //MdPetIntRelDest
      $this->configurarFK('IdMdPetIntRelDest', 'md_pet_int_rel_dest mddes', 'mddes.id_md_pet_int_rel_dest');
      $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'StaSituacaoIntimacao', 'mddes.sta_situacao_intimacao', 'md_pet_int_rel_dest mddes');

  }
}
?>