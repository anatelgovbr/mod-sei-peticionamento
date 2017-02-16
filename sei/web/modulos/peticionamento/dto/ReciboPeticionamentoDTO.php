<?
/**
* ANATEL
*
* 28/06/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class ReciboPeticionamentoDTO extends InfraDTO  {

	public function getStrNomeTabela() {
		return 'md_pet_rel_recibo_protoc';
	}
	
	public function getStrStaTipoPeticionamentoFormatado(){
		
		if( $this->isSetStrStaTipoPeticionamento() && $this->getStrStaTipoPeticionamento() == "N"  ){
			return "Processo Novo";
		} else if( $this->isSetStrStaTipoPeticionamento() && $this->getStrStaTipoPeticionamento() == "I" ){
			return "Intercorrente";
		} else {
			return "";
		}
	}
	
	public function montar() {

		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'IdReciboPeticionamento',
				'id_md_pet_rel_recibo_protoc');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'IdProtocolo',
				'id_protocolo');

		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'IdUsuario',
				'id_usuario');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH,
				'DataHoraRecebimentoFinal',
				'data_hora_recebimento_final');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'IpUsuario',
				'ip_usuario');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'SinAtivo',
				'sin_ativo');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'StaTipoPeticionamento',
				'sta_tipo_peticionamento');

		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DBL,
			  'IdProtocoloRelacionado',
			  'id_protocolo_relacionado');
				
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
				'NomeTipoPeticionamento',
				'prot.IdTipoProcedimentoProcedimento',
				'protocolo prot');
		
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
				'IdTipoProcedimentoProcedimento',
				'prot.IdTipoProcedimentoProcedimento',
				'protocolo prot');

		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
				'NumeroProcessoFormatado',
				'prot.protocolo_formatado',
				'protocolo prot');

		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM,
				'IdUnidadeGeradora',
				'prot.id_unidade_geradora',
				'protocolo prot');

		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
				'DscUnidadeGeradora',
				'und.descricao',
				'unidade und');

		//lista de documentos
		$this->adicionarAtributo(InfraDTO::$PREFIXO_ARR, 'ObjReciboDocumentoAnexoPeticionamentoDTO');
		$this->adicionarAtributo(InfraDTO::$PREFIXO_DTH,'Inicial');
		$this->adicionarAtributo(InfraDTO::$PREFIXO_DTH,'Final');
		
		$this->adicionarAtributo(InfraDTO::$PREFIXO_OBJ,'ProtocoloDTO');

		$this->configurarPK('IdReciboPeticionamento',InfraDTO::$TIPO_PK_NATIVA);

		$this->configurarFK('IdProtocoloRelacionado', 'protocolo protRel', 'protRel.id_protocolo', InfraDTO::$TIPO_FK_OPCIONAL);
		$this->configurarFK('IdProtocolo', 'protocolo prot', 'prot.id_protocolo', InfraDTO::$TIPO_FK_OPCIONAL);

		//unidade
		$this->configurarFK('IdUnidadeGeradora', 'unidade und', 'und.id_unidade');
		$this->configurarFK('IdUsuario', 'usuario user', 'user.id_usuario');						
	
	}}
?>