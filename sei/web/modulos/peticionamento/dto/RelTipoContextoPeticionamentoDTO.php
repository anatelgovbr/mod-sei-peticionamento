<?
/**
* ANATEL
*
* 29/06/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class RelTipoContextoPeticionamentoDTO extends InfraDTO {

  public function getStrNomeTabela() {
  	 return 'md_pet_rel_tp_ctx_contato';
  }

  public function montar() {
	
  	$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
  			'IdTipoContextoPeticionamento',
  			'id_md_pet_rel_tp_ctx_contato');
  	
  	//versao SEIv2
    //$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
    //                               'IdTipoContextoContato',
    //                              'id_tipo_contexto_contato');
    
  	//versao SEIv3
  	$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
  	                               'IdTipoContextoContato',
  	                              'id_tipo_contato');
  	
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
    		'SinCadastroInteressado',
    		'sin_cadastro_interessado');
    
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
    		'SinSelecaoInteressado',
    		'sin_selecao_interessado');
    
    //versao SEIv2
    //$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
    		//'NomeTipoContexto',
    		//'tp.nome',
    		//'tipo_contexto_contato tp');
    
    //versao SEIv3
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
    'NomeTipoContexto',
    'tp.nome',
    'tipo_contato tp');
    
    $this->configurarPK('IdTipoContextoPeticionamento', InfraDTO::$TIPO_PK_NATIVA );
    
    //versao SEIv2
    //$this->configurarFK('IdTipoContextoContato', 'tipo_contexto_contato tp', 'tp.id_tipo_contexto_contato');
    
    //versao SEIv3
    $this->configurarFK('IdTipoContextoContato', 'tipo_contato tp', 'tp.id_tipo_contato');
  }
}
?>