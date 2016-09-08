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
  	
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
                                   'IdTipoContextoContato',
                                   'id_tipo_contexto_contato');
    
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
    		'SinCadastroInteressado',
    		'sin_cadastro_interessado');
    
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
    		'SinSelecaoInteressado',
    		'sin_selecao_interessado');
    
    $this->configurarPK('IdTipoContextoPeticionamento', InfraDTO::$TIPO_PK_NATIVA );
    
    $this->configurarFK('IdTipoContextoContato', 'tipo_contexto_contato tp', 'tp.id_tipo_contexto_contato');
    
  }
}
?>