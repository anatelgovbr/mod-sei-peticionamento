<?
/**
* ANATEL
*
* 18/05/2016 - criado por jaqueline.mendes - CAST
* 26/08/2024 - Atualização por gabrielg.colab - SPASSU
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetRelTpProcSerieDTO extends InfraDTO {

  public function getStrNomeTabela() {
  	 return 'md_pet_rel_tp_proc_serie';
  }

  public function montar() {
  	
  	$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
  			'IdRelTipoProcessoSeriePeticionamento',
  			'id_md_pet_rel_tipo_proc');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
                                   'IdTipoProcessoPeticionamento',
                                   'id_md_pet_tipo_processo');
    
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
						    		'IdSerie',
						    		'id_serie');
    
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
    								'StaTipoDoc',
    								'sta_tp_doc');

    $this->configurarPK('IdRelTipoProcessoSeriePeticionamento',InfraDTO::$TIPO_PK_NATIVA);
    
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeSerie', 's.nome', 'serie s');

    $this->configurarFK('IdTipoProcessoPeticionamento', 'md_pet_tipo_processo tp', 'tp.id_md_pet_tipo_processo');
	$this->configurarFK('IdSerie', 'serie s', 's.id_serie');
    
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeSerie', 's.nome', 'serie s');

  }
}
?>