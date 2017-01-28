<?
/**
* ANATEL
*
* 04/08/2016 - criado por jaqueline.mendes@castgroup.com.br - CAST  (EU6155 - SM)
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class RelTipoProcessoUnidadePeticionamentoDTO extends InfraDTO {

  public function getStrNomeTabela() {
  	 return 'md_pet_rel_tp_processo_unid';
  }

  public function montar() {

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
                                   'IdTipoProcessoPeticionamento',
                                   'id_md_pet_tipo_processo');
    
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
						    		'IdUnidade',
						    		'id_unidade');
    
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
    								'StaTipoUnidade',
    								'sta_tp_unidade');
        
    $this->configurarPK('IdTipoProcessoPeticionamento',InfraDTO::$TIPO_PK_INFORMADO);
    $this->configurarPK('IdUnidade',InfraDTO::$TIPO_PK_INFORMADO);
    
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'siglaUnidade', 'u.sigla', 'unidade u');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'descricaoUnidade', 'u.descricao', 'unidade u');

    $this->configurarFK('IdTipoProcessoPeticionamento', 'md_pet_tipo_processo tp', 'tp.id_md_pet_tipo_processo');
	$this->configurarFK('IdUnidade', 'unidade u', 'u.id_unidade');
    

  }
}
?>