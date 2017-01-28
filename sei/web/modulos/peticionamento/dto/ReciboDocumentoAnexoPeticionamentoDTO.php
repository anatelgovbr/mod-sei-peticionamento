<?
/**
* ANATEL
*
* 29/06/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class ReciboDocumentoAnexoPeticionamentoDTO extends InfraDTO  {

	public function getStrNomeTabela() {
		return 'md_pet_rel_recibo_docanexo';
	}
	
	public function montar() {
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'IdReciboDocumentoAnexoPeticionamento',
				'id_md_pet_rel_recibo_docanexo');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'IdReciboPeticionamento',
				'id_md_pet_rel_recibo_protoc');

		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'IdDocumento',
				'id_documento');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'IdAnexo',
				'id_anexo');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'ClassificacaoDocumento',
				'classificacao_documento');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'FormatoDocumento',
				'formato_documento');
		
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM,
				'IdSerie',
				'doc.id_serie',
				'documento doc');
		
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
				'NumeroDocumento',
				'doc.numero',
				'documento doc');

		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
				'NomeSerie',
				'nome',
				'serie ser');
		
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
				'ProtocoloFormatado',
				'protocolo_formatado',
				'protocolo prot');
		
		$this->configurarPK('IdReciboDocumentoAnexoPeticionamento',InfraDTO::$TIPO_PK_NATIVA);
		
		//recibo
		$this->configurarFK('IdReciboPeticionamento', 'md_pet_rel_recibo_protoc recibo', 'recibo.id_md_pet_rel_recibo_protoc');
		
		//documento
		$this->configurarFK('IdDocumento', 'documento doc', 'doc.id_documento', InfraDTO::$TIPO_FK_OPCIONAL);
		
		//serie
		$this->configurarFK('IdSerie', 'serie ser', 'ser.id_serie', InfraDTO::$TIPO_FK_OPCIONAL);
		
		//protocolo
		$this->configurarFK('IdDocumento', 'protocolo prot', 'prot.id_protocolo', InfraDTO::$TIPO_FK_OPCIONAL);
		
		//anexo
		$this->configurarFK('IdAnexo', 'anexo anex', 'anex.id_anexo', InfraDTO::$TIPO_FK_OPCIONAL);
		
		
		//protocolo
		//$this->configurarFK('IdDocumento', 'protocolo prot', 'id_', InfraDTO::$TIPO_FK_OPCIONAL);
		
	
	}}
?>