<?
/**
* ANATEL
*
* 29/06/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetRelTpCtxContatoDTO extends InfraDTO {

  public function getStrNomeTabela() {
  	 return 'md_pet_rel_tp_ctx_contato';
  }

  public function montar() {
	
  	$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
  			'IdTipoContextoPeticionamento',
  			'id_md_pet_rel_tp_ctx_contato');

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

    //versao SEIv3
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
    'NomeTipoContexto',
    'tp.nome',
    'tipo_contato tp');
    
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
    		'SinSistema',
    		'tp.sin_sistema',
    		'tipo_contato tp');
    
    $this->configurarPK('IdTipoContextoPeticionamento', InfraDTO::$TIPO_PK_NATIVA );

    //versao SEIv3
    $this->configurarFK('IdTipoContextoContato', 'tipo_contato tp', 'tp.id_tipo_contato');
  }
}
?>