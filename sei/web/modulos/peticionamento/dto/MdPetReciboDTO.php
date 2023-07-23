<?
/**
* ANATEL
*
* 28/06/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetReciboDTO extends InfraDTO  {

	public function getStrNomeTabela() {
		return 'md_pet_rel_recibo_protoc';
	}

  public function getStrStaTipoPeticionamentoFormatado()
  {      
    if ($this->isSetStrStaTipoPeticionamento() && $this->getStrStaTipoPeticionamento() == MdPetReciboRN::$TP_RECIBO_NOVO) {
      return  MdPetReciboRN::$STR_TP_RECIBO_NOVO;
    } else if ($this->isSetStrStaTipoPeticionamento() && $this->getStrStaTipoPeticionamento() == MdPetReciboRN::$TP_RECIBO_INTERCORRENTE) {
      return  MdPetReciboRN::$STR_TP_RECIBO_INTERCORRENTE;
    } else if ($this->isSetStrStaTipoPeticionamento() && $this->getStrStaTipoPeticionamento() == MdPetReciboRN::$TP_RECIBO_RESPOSTA_INTIMACAO) {
      return MdPetReciboRN::$STR_TP_RECIBO_RESPOSTA_INTIMACAO;
    } else if ($this->isSetStrStaTipoPeticionamento() && $this->getStrStaTipoPeticionamento() == MdPetReciboRN::$TP_RECIBO_RESPONSAVEL_LEGAL_INICIAL) {
      return MdPetReciboRN::$STR_TP_RECIBO_RESPONSAVEL_LEGAL_INICIAL;
    } else if ($this->isSetStrStaTipoPeticionamento() && $this->getStrStaTipoPeticionamento() == MdPetReciboRN::$TP_RECIBO_RESPONSAVEL_LEGAL_ALTERACAO) {
      return MdPetReciboRN::$STR_TP_RECIBO_RESPONSAVEL_LEGAL_ALTERACAO;
    } else if ($this->isSetStrStaTipoPeticionamento() && $this->getStrStaTipoPeticionamento() == MdPetReciboRN::$TP_RECIBO_ATUALIZACAO_ATOS_CONSTITUTIVOS) {
        return MdPetReciboRN::$STR_TP_RECIBO_ATUALIZACAO_ATOS_CONSTITUTIVOS;
    } else if ($this->isSetStrStaTipoPeticionamento() && $this->getStrStaTipoPeticionamento() == MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_EMISSAO) {
      return MdPetReciboRN::$STR_TP_RECIBO_PROCURACAO_ELETRONICA_EMISSAO;
    } else if ($this->isSetStrStaTipoPeticionamento() && $this->getStrStaTipoPeticionamento() == MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_RENUNCIA) {
        return MdPetReciboRN::$STR_TP_RECIBO_PROCURACAO_ELETRONICA_RENUNCIA;
    } else if ($this->isSetStrStaTipoPeticionamento() && $this->getStrStaTipoPeticionamento() == MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_REVOGACAO) {
        return MdPetReciboRN::$STR_TP_RECIBO_PROCURACAO_ELETRONICA_REVOGACAO;
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

		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DBL,
				'IdDocumento',
				'id_documento');

		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
				'NumeroProcessoFormatadoDoc',
				'protdoc.protocolo_formatado',
				'protocolo protdoc');

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

		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DBL,
				'IdDocumento',
				'id_documento');

		//INICIO - atributos usados para exibir informa�oes da resposta a intima�ao
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'TextoDocumentoPrincipalIntimac',
				'txt_doc_principal_intimacao');

		//FIM - atributos usados para exibir informa�oes da resposta a intima�ao

		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM,
				'IdProtocoloProcedimento',
				'prot.id_protocolo',
				'protocolo prot');

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
		$this->adicionarAtributo(InfraDTO::$PREFIXO_ARR, 'ObjMdPetRelReciboDocumentoAnexoDTO');
		$this->adicionarAtributo(InfraDTO::$PREFIXO_DTH,'Inicial');
		$this->adicionarAtributo(InfraDTO::$PREFIXO_DTH,'Final');

		$this->adicionarAtributo(InfraDTO::$PREFIXO_OBJ,'ProtocoloDTO');

		$this->configurarPK('IdReciboPeticionamento',InfraDTO::$TIPO_PK_NATIVA);

		$this->configurarFK('IdDocumento', 'documento doc', 'documento.id_documento', InfraDTO::$TIPO_FK_OPCIONAL);
		$this->configurarFK('IdDocumento', 'protocolo protdoc', 'protdoc.id_protocolo', InfraDTO::$TIPO_FK_OPCIONAL);
		$this->configurarFK('IdProtocoloRelacionado', 'protocolo protRel', 'protRel.id_protocolo', InfraDTO::$TIPO_FK_OPCIONAL);
		$this->configurarFK('IdProtocolo', 'protocolo prot', 'prot.id_protocolo', InfraDTO::$TIPO_FK_OPCIONAL);

		//unidade
		$this->configurarFK('IdUnidadeGeradora', 'unidade und', 'und.id_unidade');
		$this->configurarFK('IdUsuario', 'usuario user', 'user.id_usuario');

		//documento
		$this->configurarFK('IdDocumento', 'documento doc', 'doc.id_documento');

	}}
?>