<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 14/03/2017 - criado por pedro.cast
 * 26/08/2024 - Atualização por gabrielg.colab - SPASSU
 *
 * Versão do Gerador de Código: 1.40.0
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntRelDestinatarioDTO extends InfraDTO {

	private $AceiteTIPOFK;
	private $ProcedimentoDocTIPOFK;

	public function __construct(){
		$this->AceiteTIPOFK = InfraDTO::$TIPO_FK_OPCIONAL;
		$this->ProcedimentoDocTIPOFK = InfraDTO::$TIPO_FK_OPCIONAL;
		parent::__construct();
	}

	public function getStrNomeTabela() {
		return 'md_pet_int_rel_dest';
	}

	public function montar() {

		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntRelDestinatario', 'id_md_pet_int_rel_dest');

		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinAtivo', 'sin_ativo');

		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinPessoaJuridica', 'sin_pessoa_juridica');

		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntimacao', 'id_md_pet_intimacao');

		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdContato', 'id_contato');

		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'DataCadastro', 'data_cadastro');

		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdUnidade', 'id_unidade');

		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'StaSituacaoIntimacao', 'sta_situacao_intimacao');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTA, 'DataPrazoTacito', 'dta_prazo_tacito');

		//Atts Gerais
		$this->adicionarAtributo(InfraDTO::$PREFIXO_DTH,'DataFinal');
		$this->adicionarAtributo(InfraDTO::$PREFIXO_STR,'Anexos');
		$this->adicionarAtributo(InfraDTO::$PREFIXO_STR,'SituacaoIntimacao');
		$this->adicionarAtributo(InfraDTO::$PREFIXO_STR,'DocumentoPrincipal');
		$this->adicionarAtributo(InfraDTO::$PREFIXO_NUM,'IdSituacaoIntimacao');
		$this->adicionarAtributo(InfraDTO::$PREFIXO_STR,'DocumentoCertidaoAceite');

		//PK
		$this->configurarPK('IdMdPetIntRelDestinatario',InfraDTO::$TIPO_PK_NATIVA);

		/// Configs Fks
		$this->configurarFK('IdAcessoExterno', 'acesso_externo a', 'a.id_acesso_externo', InfraDTO::$TIPO_FK_OPCIONAL);
		$this->configurarFK('IdAcessoExterno', 'md_pet_acesso_externo mpa', 'mpa.id_acesso_externo', InfraDTO::$TIPO_FK_OPCIONAL);

		$this->configurarFK('IdMdPetIntimacao', 'md_pet_intimacao mpi', 'mpi.id_md_pet_intimacao');
		$this->configurarFK('IdContato', 'contato c', 'c.id_contato');
		$this->configurarFK('IdMdPetIntimacao', 'md_pet_int_protocolo mpd', 'mpd.id_md_pet_intimacao');
		$this->configurarFK('IdProtocolo','protocolo pd','pd.id_protocolo');
		$this->configurarFK('IdDocumento', 'documento d', 'd.id_documento', $this->getProcedimentoDocTIPOFK());
		$this->configurarFK('IdSerie', 'serie s', 's.id_serie');
		$this->configurarFK('IdProtocoloProcedimento', 'protocolo pp', 'pp.id_protocolo');
		$this->configurarFK('IdMdPetTipoIntimacao', 'md_pet_int_tipo_intimacao mpit', 'mpit.id_md_pet_int_tipo_intimacao');
		$this->configurarFK('IdProcedimento', 'procedimento pro', 'pro.id_procedimento');
		$this->configurarFK('IdTipoProcedimento', 'tipo_procedimento tpro', 'tpro.id_tipo_procedimento');
		$this->configurarFK('IdMdPetIntRelDestinatario', 'md_pet_int_aceite aceite', 'aceite.id_md_pet_int_rel_dest', $this->getAceiteTIPOFK(), InfraDTO::$FILTRO_FK_WHERE );
		$this->configurarFK('IdUnidade', 'unidade uint', 'uint.id_unidade');
		$this->configurarFK('IdAtividadeAcessoExterno', 'atividade atv', 'atv.id_atividade', InfraDTO::$TIPO_FK_OPCIONAL);
		$this->configurarFK('IdUnidadeAcessoExterno', 'unidade u', 'u.id_unidade', InfraDTO::$TIPO_FK_OPCIONAL);


		//Add Atr

		//Acesso Externo
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_DTA, 'ValidadeAcessoExterno', 'a.dta_validade', 'acesso_externo a');
		
		//Acesso Externo do Módulo
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'SinAtivoModulo', 'mpa.sin_ativo', 'md_pet_acesso_externo mpa');
	
		//Atividade do Acesso Externo
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdAtividadeAcessoExterno','a.id_atividade','acesso_externo a');

		//Unidade da Atividade do Acesso Externo
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdUnidadeAcessoExterno','atv.id_unidade','atividade atv');

		//Unidade da Intimação
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'SiglaUnidadeIntimacao','uint.sigla','unidade uint');
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'DescricaoUnidadeIntimacao','uint.descricao','unidade uint');
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdOrgao','uint.id_orgao','unidade uint');

		//Intimação
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdMdPetIntimacaoMdPetIntimacao', 'mpi.id_md_pet_intimacao', 'md_pet_intimacao mpi');
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdMdPetTipoIntimacao','mpi.id_md_pet_int_tipo_intimacao','md_pet_intimacao mpi');

		//Tipo de Intimação
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeTipoIntimacao','mpit.nome','md_pet_int_tipo_intimacao mpit');
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeTipoRespostaAceita','mpit.tipo_resposta_aceita','md_pet_int_tipo_intimacao mpit');

		//Contato
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeContato', 'c.nome','contato c');
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'EmailContato', 'c.email','contato c');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'CnpjContato', 'c.cnpj','contato c');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_DBL, 'CpfContato', 'c.cpf','contato c');

		// Intimação x Protocolo
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_DBL, 'IdProtocolo', 'mpd.id_protocolo', 'md_pet_int_protocolo mpd');
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'SinPrincipalDoc', 'mpd.sin_principal', 'md_pet_int_protocolo mpd');

		//Protocolo Processo
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'ProtocoloFormatadoProcedimento','pp.protocolo_formatado','protocolo pp');
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'EspecificacaoProcedimento','pp.descricao','protocolo pp');

		//Protocolo Documento
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_DBL, 'IdDocumento', 'pd.id_protocolo', 'protocolo pd');
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'ProtocoloFormatadoDocumento','pd.protocolo_formatado','protocolo pd');

		//Documento
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdSerie','d.id_serie','documento d');
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_DBL, 'IdProtocoloProcedimento','d.id_procedimento','documento d');
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_DBL, 'IdProcedimento','d.id_procedimento','documento d');
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'Numero','d.numero','documento d');

		//Serie
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeSerie','s.nome','serie s');

		//Procedimento
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdTipoProcedimento','pro.id_tipo_procedimento','procedimento pro');

		//Tipo Procedimento
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeTipoProcedimento','tpro.nome','tipo_procedimento tpro');

		//Aceite
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_DTH, 'DataAceite','aceite.data','md_pet_int_aceite aceite');
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'TipoAceite','aceite.tipo_aceite','md_pet_int_aceite aceite');
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdMdPetAceite','aceite.id_md_pet_int_aceite','md_pet_int_aceite aceite');
		//
        $this->configurarFK('IdMdPetIntRelDestinatario', 'md_pet_rel_int_dest_extern destinatario', 'destinatario.id_md_pet_int_rel_dest');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdAcessoExterno','destinatario.id_acesso_externo','md_pet_rel_int_dest_extern destinatario');

        $this->configurarFK('IdParticipanteAcessoExterno', 'participante', 'id_participante', false, true);
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM,
            'IdParticipanteAcessoExterno',
            'a.id_participante',
            'acesso_externo a');


        $this->configurarFK('IdContatoParticipante', 'contato cp', 'cp.id_contato');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM,
            'IdContatoParticipante',
            'id_contato',
            'participante');
        
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
            'NomeContatoParticipante',
            'cp.nome',
            'contato cp');


        $this->configurarFK('IdContatoParticipante', 'usuario usu', 'usu.id_contato');
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM,
            'IdUsuario',
            'usu.id_usuario',
            'usuario usu');
		$this->adicionarAtributo(InfraDTO::$PREFIXO_STR,'NomeEmailCnpjCpf');
		//$this->adicionarAtributo(InfraDTO::$PREFIXO_STR,'NomeCpfEmail');

	}
	
	public function getStrNomeEmailCnpjCpf(){
		if($this->getStrSinPessoaJuridica() == "S"){
			return $this->getStrNomeContato().' - '.infraUtil::formatarCnpj($this->getStrCnpjContato()); 
		}else{
			return $this->getStrNomeContato().' - '.$this->getStrEmailContato().' - '.infraUtil::formatarCpf($this->getDblCpfContato()) ; 

		}
	}

	public function getStrTipoDestinatario(){
		if($this->getStrSinPessoaJuridica() == "S"){
			return "Pessoa Jurídica";
		}else{
			return "Pessoa Física";

		}
	}
        
	public function getAceiteTIPOFK() {
		return $this->AceiteTIPOFK;
	}

	public function setAceiteTIPOFK($AceiteTIPOFK) {
		$this->AceiteTIPOFK = $AceiteTIPOFK;
	}

	public function getProcedimentoDocTIPOFK() {
		return $this->ProcedimentoDocTIPOFK;
	}

	public function setProcedimentoDocTIPOFK($ProcedimentoDocTIPOFK) {
		$this->ProcedimentoDocTIPOFK = $ProcedimentoDocTIPOFK;
	}
	
	public function getIdDocumentoCertidaoAceite(){
		
		if(!empty($this->getNumIdMdPetAceite()) && is_numeric($this->getNumIdMdPetAceite())){
			
			$objMdPetIntAceiteDTO = new MdPetIntAceiteDTO();
			$objMdPetIntAceiteDTO->setNumIdMdPetIntAceite($this->getNumIdMdPetAceite());
			$objMdPetIntAceiteDTO->retDblIdDocumentoCertidao();
			$objMdPetIntAceiteDTO = (new MdPetIntAceiteRN())->consultar($objMdPetIntAceiteDTO);
			
			if(!empty($objMdPetIntAceiteDTO)){
				
				return $objMdPetIntAceiteDTO->getDblIdDocumentoCertidao();
				
			}
			
		}
		
	}
	
	public function getStrDocumentoCertidaoAceite(){
		
		if(!empty($this->getNumIdMdPetAceite()) && is_numeric($this->getNumIdMdPetAceite())){
			
			$objMdPetIntAceiteDTO = new MdPetIntAceiteDTO();
			$objMdPetIntAceiteDTO->setNumIdMdPetIntAceite($this->getNumIdMdPetAceite());
			$objMdPetIntAceiteDTO->retDblIdDocumentoCertidao();
			$objMdPetIntAceiteDTO = (new MdPetIntAceiteRN())->consultar($objMdPetIntAceiteDTO);
			
			if(!empty($objMdPetIntAceiteDTO)){
			
				$objProtocoloDTO = new ProtocoloDTO();
				$objProtocoloDTO->setDblIdProtocolo($objMdPetIntAceiteDTO->getDblIdDocumentoCertidao());
				$objProtocoloDTO->retStrProtocoloFormatado();
				$objProtocoloDTO = (new ProtocoloRN())->consultarRN0186($objProtocoloDTO);
				
				if(!empty($objProtocoloDTO)){
					return $objProtocoloDTO->getStrProtocoloFormatado();
				}
			
			}
			
		}
		
	}
	
}

?>