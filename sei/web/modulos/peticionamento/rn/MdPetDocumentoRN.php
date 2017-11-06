<?
/**
* ANATEL
*
* 28/06/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetDocumentoRN extends InfraRN { 
	
	public function __construct() {
		
		//session_start();
		
		//////////////////////////////////////////////////////////////////////////////
		InfraDebug::getInstance()->setBolLigado(true);
		InfraDebug::getInstance()->setBolDebugInfra(true);
		InfraDebug::getInstance()->limpar();
		//////////////////////////////////////////////////////////////////////////////
		
		parent::__construct ();
	}
	
	protected function inicializarObjInfraIBanco() {
		return BancoSEI::getInstance ();
	}
	
	//TODO: Grande parte da regra de negócio se baseou em SEIRN:199 - incluirDocumento.
	//Avaliar a refatoração para impedir a duplicação de código
	private function atribuirDocumentos($objProcedimentoDTO, $arrObjDocumentoDTO , $objUnidadeDTO, $parObjMetadadosProcedimento)
	{
		if(!isset($objProcesso)) {
			throw new InfraException('Parâmetro $objProcesso não informado.');
		}
	
		if(!isset($objUnidadeDTO)) {
			throw new InfraException('Unidade responsável pelo documento não informada.');
		}
	
		if(!isset($objProcesso->documento)) {
			throw new InfraException('Lista de documentos do processo não informada.');
		}
	
		$arrObjDocumentos = $arrObjDocumentoDTO;
		
		if(!is_array($arrObjDocumentos)) {
			$arrObjDocumentos = array($arrObjDocumentos);
		}
	
		$strNumeroRegistro = $parObjMetadadosProcedimento->metadados->NRE;
		//$numTramite = $parObjMetadadosProcedimento->metadados->IDT;
	
		//Ordenação dos documentos conforme informado pelo remetente. Campo documento->ordem
		usort($arrObjDocumentos, array("ReceberProcedimentoRN", "comparacaoOrdemDocumentos"));
	
		//Obter dados dos documentos já registrados no sistema
		$objComponenteDigitalDTO = new ComponenteDigitalDTO();
		$objComponenteDigitalDTO->retNumOrdem();
		$objComponenteDigitalDTO->retDblIdDocumento();
		$objComponenteDigitalDTO->retStrHashConteudo();
		$objComponenteDigitalDTO->setStrNumeroRegistro($strNumeroRegistro);
		$objComponenteDigitalDTO->setOrdNumOrdem(InfraDTO::$TIPO_ORDENACAO_ASC);
	
		$objComponenteDigitalBD = new ComponenteDigitalBD($this->getObjInfraIBanco());
		$arrObjComponenteDigitalDTO = $objComponenteDigitalBD->listar($objComponenteDigitalDTO);
		$arrObjComponenteDigitalDTOIndexado = InfraArray::indexarArrInfraDTO($arrObjComponenteDigitalDTO, "Ordem");
		$arrStrHashConteudo = InfraArray::converterArrInfraDTO($arrObjComponenteDigitalDTO, 'IdDocumento', 'HashConteudo');
	
		$objProtocoloBD = new ProtocoloBD($this->getObjInfraIBanco());
	
		$arrObjDocumentoDTO = array();
		foreach($arrObjDocumentos as $objDocumento){
	
			// @join_tec US027 (#3498)
			// Previne que o documento digital seja cadastrado na base de dados
			if(isset($objDocumento->retirado) && $objDocumento->retirado === true) {
	
				$strHashConteudo = ProcessoEletronicoRN::getHashFromMetaDados($objDocumento->componenteDigital->hash);
	
				// Caso já esteja cadastrado, de um reenvio anterior, então move para bloqueado
				if(array_key_exists($strHashConteudo, $arrStrHashConteudo)) {
	
					//Busca o ID do protocolo
					$dblIdProtocolo = $arrStrHashConteudo[$strHashConteudo];
	
					//Instancia o DTO do protocolo
					$objProtocoloDTO = new ProtocoloDTO();
					$objProtocoloDTO->setDblIdProtocolo($dblIdProtocolo);
					$objProtocoloDTO->setStrMotivoCancelamento('Cancelado pelo remetente');
	
	
					$objProtocoloRN = new PenProtocoloRN();
					$objProtocoloRN->cancelar($objProtocoloDTO);
	
				}
				continue;
			}
	
			if(array_key_exists($objDocumento->ordem, $arrObjComponenteDigitalDTOIndexado)){
				continue;
			}
	
			//Validação dos dados dos documentos
			if(!isset($objDocumento->especie)){
				throw new InfraException('Espécie do documento ['.$objDocumento->descricao.'] não informada.');
			}
	
			//---------------------------------------------------------------------------------------------------
	
			$objDocumentoDTO = new DocumentoDTO();
			$objDocumentoDTO->setDblIdDocumento(null);
			$objDocumentoDTO->setDblIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
	
			$objSerieDTO = $this->obterSerieMapeada($objDocumento->especie->codigo);
	
			if ($objSerieDTO==null){
				throw new InfraException('Tipo de documento [Espécie '.$objDocumento->especie->codigo.'] não encontrado.');
			}
	
			if (InfraString::isBolVazia($objDocumento->dataHoraDeProducao)) {
				//$objInfraException->lancarValidacao('Data do documento não informada.');
                throw new InfraException('Data do documento não informada.');
			}
	
			$objProcedimentoDTO2 = new ProcedimentoDTO();
			$objProcedimentoDTO2->retDblIdProcedimento();
			$objProcedimentoDTO2->retNumIdUsuarioGeradorProtocolo();
			$objProcedimentoDTO2->retNumIdTipoProcedimento();
			$objProcedimentoDTO2->retStrStaNivelAcessoGlobalProtocolo();
			$objProcedimentoDTO2->retStrProtocoloProcedimentoFormatado();
			$objProcedimentoDTO2->retNumIdTipoProcedimento();
			$objProcedimentoDTO2->retStrNomeTipoProcedimento();
			$objProcedimentoDTO2->adicionarCriterio(array('IdProcedimento','ProtocoloProcedimentoFormatado','ProtocoloProcedimentoFormatadoPesquisa'),
					array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),
					array($objDocumentoDTO->getDblIdProcedimento(),$objDocumentoDTO->getDblIdProcedimento(),$objDocumentoDTO->getDblIdProcedimento()),
					array(InfraDTO::$OPER_LOGICO_OR,InfraDTO::$OPER_LOGICO_OR));
	
			$objProcedimentoRN = new ProcedimentoRN();
			$objProcedimentoDTO = $objProcedimentoRN->consultarRN0201($objProcedimentoDTO2);
	
			if ($objProcedimentoDTO==null){
				throw new InfraException('Processo ['.$objDocumentoDTO->getDblIdProcedimento().'] não encontrado.');
			}
	
			$objDocumentoDTO->setDblIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
			$objDocumentoDTO->setNumIdSerie($objSerieDTO->getNumIdSerie());
			$objDocumentoDTO->setStrNomeSerie($objSerieDTO->getStrNome());
	
			$objDocumentoDTO->setDblIdDocumentoEdoc(null);
			$objDocumentoDTO->setDblIdDocumentoEdocBase(null);
			$objDocumentoDTO->setNumIdUnidadeResponsavel($objUnidadeDTO->getNumIdUnidade());
			$objDocumentoDTO->setNumIdTipoConferencia(null);
			$objDocumentoDTO->setStrConteudo(null);
	
			$objDocumentoDTO->setNumVersaoLock(0);
	
			$objProtocoloDTO = new ProtocoloDTO();
			$objDocumentoDTO->setObjProtocoloDTO($objProtocoloDTO);
			$objProtocoloDTO->setDblIdProtocolo(null);
			$objProtocoloDTO->setStrStaProtocolo(ProtocoloRN::$TP_DOCUMENTO_RECEBIDO);
			$objProtocoloDTO->setStrDescricao(utf8_decode($objDocumento->descricao));
			$objDocumentoDTO->setStrNumero((isset($objDocumento->identificacao) ? $objDocumento->identificacao->numero : utf8_decode($objDocumento->descricao)));
			$objProtocoloDTO->setStrStaNivelAcessoLocal($this->obterNivelSigiloSEI($objDocumento->nivelDeSigilo));
			$objProtocoloDTO->setDtaGeracao($this->objProcessoEletronicoRN->converterDataSEI($objDocumento->dataHoraDeProducao));
			$objProtocoloDTO->setArrObjAnexoDTO(array());
			$objProtocoloDTO->setArrObjRelProtocoloAssuntoDTO(array());
			$objProtocoloDTO->setArrObjRelProtocoloProtocoloDTO(array());
			$objProtocoloDTO->setArrObjParticipanteDTO(array());
				
			$objObservacaoDTO  = new ObservacaoDTO();
			$objObservacaoDTO->setStrDescricao($objDocumento->Observacao);
			$objProtocoloDTO->setArrObjObservacaoDTO(array($objObservacaoDTO));
		
			$bolReabriuAutomaticamente = false;
			if ($objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo()==ProtocoloRN::$NA_PUBLICO || $objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo()==ProtocoloRN::$NA_RESTRITO) {
	
				$objAtividadeDTO = new AtividadeDTO();
				$objAtividadeDTO->setDblIdProtocolo($objDocumentoDTO->getDblIdProcedimento());
				$objAtividadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
	
				//TODO: Possivelmente, essa regra é desnecessária já que o processo pode ser enviado para outra unidade do órgão através da expedição
				
				$objMdPetAtividadeRN = new MdPetAtividadeRN();
				
				if ($objMdPetAtividadeRN->contarRN0035($objAtividadeDTO) == 0) {
					throw new InfraException('Unidade '.$objUnidadeDTO->getStrSigla().' não possui acesso ao Procedimento '.$objProcedimentoDTO->getStrProtocoloProcedimentoFormatado().'.');
				}
	
				$objAtividadeDTO = new AtividadeDTO();
				$objAtividadeDTO->setDblIdProtocolo($objDocumentoDTO->getDblIdProcedimento());
				$objAtividadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
				$objAtividadeDTO->setDthConclusao(null);

			}
	
			$objTipoProcedimentoDTO = new TipoProcedimentoDTO();
			$objTipoProcedimentoDTO->retStrStaNivelAcessoSugestao();
			$objTipoProcedimentoDTO->retStrStaGrauSigiloSugestao();
			$objTipoProcedimentoDTO->retNumIdHipoteseLegalSugestao();
			$objTipoProcedimentoDTO->setNumIdTipoProcedimento($objProcedimentoDTO->getNumIdTipoProcedimento());
	
			$objTipoProcedimentoRN = new TipoProcedimentoRN();
			$objTipoProcedimentoDTO = $objTipoProcedimentoRN->consultarRN0267($objTipoProcedimentoDTO);
	
			if (InfraString::isBolVazia($objDocumentoDTO->getObjProtocoloDTO()->getStrStaNivelAcessoLocal()) || $objDocumentoDTO->getObjProtocoloDTO()->getStrStaNivelAcessoLocal()==$objTipoProcedimentoDTO->getStrStaNivelAcessoSugestao()) {
				$objDocumentoDTO->getObjProtocoloDTO()->setStrStaNivelAcessoLocal($objTipoProcedimentoDTO->getStrStaNivelAcessoSugestao());
				$objDocumentoDTO->getObjProtocoloDTO()->setStrStaGrauSigilo($objTipoProcedimentoDTO->getStrStaGrauSigiloSugestao());
				$objDocumentoDTO->getObjProtocoloDTO()->setNumIdHipoteseLegal($objTipoProcedimentoDTO->getNumIdHipoteseLegalSugestao());
			}
	
			$objDocumentoDTO->getObjProtocoloDTO()->setArrObjParticipanteDTO($this->prepararParticipantes($objDocumentoDTO->getObjProtocoloDTO()->getArrObjParticipanteDTO()));
	
			$objDocumentoRN = new DocumentoRN();
	
			$strConteudoCodificado = $objDocumentoDTO->getStrConteudo();
			$objDocumentoDTO->setStrConteudo(null);
			$objDocumentoDTO->setStrSinFormulario('N');
	
			// @join_tec US027 (#3498)
			$numIdUnidadeGeradora = $this->objInfraParametro->getValor('PEN_UNIDADE_GERADORA_DOCUMENTO_RECEBIDO', false);
			// Registro existe e pode estar vazio
			if(!empty($numIdUnidadeGeradora)) {
				$objDocumentoDTO->getObjProtocoloDTO()->setNumIdUnidadeGeradora($numIdUnidadeGeradora);
			}
			$objDocumentoDTO->setStrSinBloqueado('S');
	
			//TODO: Fazer a atribuição dos componentes digitais do processo a partir desse ponto
			$this->atribuirComponentesDigitais($objDocumentoDTO, $objDocumento->componenteDigital);
			$objDocumentoDTOGerado = $objDocumentoRN->receberRN0991($objDocumentoDTO);
	
			$objAtividadeDTOVisualizacao = new AtividadeDTO();
			$objAtividadeDTOVisualizacao->setDblIdProtocolo($objDocumentoDTO->getDblIdProcedimento());
			$objAtividadeDTOVisualizacao->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
	
			if (!$bolReabriuAutomaticamente){
				$objAtividadeDTOVisualizacao->setNumTipoVisualizacao(AtividadeRN::$TV_ATENCAO);
			}else{
				$objAtividadeDTOVisualizacao->setNumTipoVisualizacao(AtividadeRN::$TV_NAO_VISUALIZADO | AtividadeRN::$TV_ATENCAO);
			}
			$objMdPetAtividadeRN = new MdPetAtividadeRN();
			$objMdPetAtividadeRN->atualizarVisualizacaoUnidade($objAtividadeDTOVisualizacao);
	
			$objDocumento->idDocumentoSEI = $objDocumentoDTO->getDblIdDocumento();
			$arrObjDocumentoDTO[] = $objDocumentoDTO;
		}
	
		$objProcedimentoDTO->setArrObjDocumentoDTO($arrObjDocumentoDTO);
	}
	
	//TODO: Método deverá poderá ser transferido para a classe responsável por fazer o recebimento dos componentes digitais
	private function atribuirComponentesDigitais(DocumentoDTO $parObjDocumentoDTO, $parArrObjComponentesDigitais)
	{
		if(!isset($parArrObjComponentesDigitais)) {
			throw new InfraException('Componentes digitais do documento não informado.');
		}
	
		//TODO: Aplicar mesmas validações realizadas no momento do upload de um documento InfraPagina::processarUpload
		//TODO: Avaliar a refatoração do código abaixo para impedir a duplicação de regras de negócios
		
		$arrObjAnexoDTO = array();
		if($parObjDocumentoDTO->getObjProtocoloDTO()->isSetArrObjAnexoDTO()) {
			$arrObjAnexoDTO = $parObjDocumentoDTO->getObjProtocoloDTO()->getArrObjAnexoDTO();
		}
	
		if (!is_array($parArrObjComponentesDigitais)) {
			$parArrObjComponentesDigitais = array($parArrObjComponentesDigitais);
		}
	
		//TODO: Tratar a ordem dos componentes digitais
		//...
	
	
		$parObjDocumentoDTO->getObjProtocoloDTO()->setArrObjAnexoDTO($arrObjAnexoDTO);
	}
	
	public function gerarRN0003Customizado(DocumentoDTO $objDocumentoDTO){
	
		$bolAcumulacaoPrevia = FeedSEIProtocolos::getInstance()->isBolAcumularFeeds();
	
		FeedSEIProtocolos::getInstance()->setBolAcumularFeeds(true);
	
		$objDocumentoDTO = $this->gerarRN0003InternoCustomizado($objDocumentoDTO);
	
		$objIndexacaoDTO = new IndexacaoDTO();
		$objIndexacaoRN = new IndexacaoRN();
	
		$objProtocoloDTO = new ProtocoloDTO();
		$objProtocoloDTO->setDblIdProtocolo($objDocumentoDTO->getDblIdDocumento());
	
		//seiv2
		//$objIndexacaoDTO->setArrObjProtocoloDTO(array($objProtocoloDTO));
		
		//alteracoes seiv3
		$objIndexacaoDTO->setArrIdProtocolos( array( $objProtocoloDTO->getDblIdProtocolo() ) );
		
		//seiv2
		//$objIndexacaoDTO->setStrStaOperacao(IndexacaoRN::$TO_GERACAO_PROTOCOLO);
		
		//alteracoes seiv3
		$objIndexacaoDTO->setStrStaOperacao(IndexacaoRN::$TO_PROTOCOLO_METADADOS);
		
		$objIndexacaoRN->indexarProtocolo($objIndexacaoDTO);
	
		if (!$bolAcumulacaoPrevia){
			FeedSEIProtocolos::getInstance()->setBolAcumularFeeds(false);
			FeedSEIProtocolos::getInstance()->indexarFeeds();
		}
	
		return $objDocumentoDTO;
	
	}
	
	protected function gerarRN0003InternoCustomizadoControlado(DocumentoDTO $objDocumentoDTO) {
				
		try{
			
			$idUnidadeResponsavel = $objDocumentoDTO->getNumIdUnidadeResponsavel();
			
			//Valida Permissao
			//SessaoSEI::getInstance()->validarAuditarPermissao('documento_gerar',__METHOD__,$objDocumentoDTO);
	
			//Regras de Negocio
			$objInfraException = new InfraException();
	
			if( !$objDocumentoDTO->isSetStrStaProtocoloProtocolo() 
				|| $objDocumentoDTO->getStrStaProtocoloProtocolo() == '' 
				|| $objDocumentoDTO->getStrStaProtocoloProtocolo() == null ){
			  
				$objDocumentoDTO->setStrStaProtocoloProtocolo(ProtocoloRN::$TP_DOCUMENTO_GERADO);
			
			}
			
			$this->validarNumIdUnidadeResponsavelRN0915($objDocumentoDTO, $objInfraException);	
			$this->validarNumIdSerieRN0009($objDocumentoDTO, $objInfraException);
	
			//conteudo nao existe nas telas de cadastro, apenas em documentos gerados por servicos
			if ($objDocumentoDTO->isSetStrConteudo()){
				$this->validarStrConteudo($objDocumentoDTO, $objInfraException);
			}else{
				$objDocumentoDTO->setStrConteudo(null);
			}
	
			$objDocumentoDTO->setStrConteudoAssinatura(null);
			$objDocumentoDTO->setStrCrcAssinatura(null);
			$objDocumentoDTO->setStrQrCodeAssinatura(null);
			$objDocumentoDTO->setStrSinBloqueado('N');
			$objDocumentoDTO->setNumIdConjuntoEstilos(null);
			$objDocumentoDTO->setNumIdTipoConferencia(null);
	
			$this->validarStrStaEditor($objDocumentoDTO, $objInfraException);
			$this->validarStrSinFormulario($objDocumentoDTO, $objInfraException);
	
			if ($objDocumentoDTO->isSetStrProtocoloDocumentoTextoBase() && !InfraString::isBolVazia($objDocumentoDTO->getStrProtocoloDocumentoTextoBase())){
	
				$strProtocoloDocumentoTextoBaseFormatado = str_pad($objDocumentoDTO->getStrProtocoloDocumentoTextoBase(), DIGITOS_DOCUMENTO, '0', STR_PAD_LEFT);
	
				$objPesquisaProtocoloDTO = new PesquisaProtocoloDTO();
				$objPesquisaProtocoloDTO->setStrStaTipo(ProtocoloRN::$TPP_DOCUMENTOS_GERADOS);
				$objPesquisaProtocoloDTO->setStrStaAcesso(ProtocoloRN::$TAP_AUTORIZADO);
				$objPesquisaProtocoloDTO->setStrProtocolo($strProtocoloDocumentoTextoBaseFormatado);
	
				$objProtocoloRN = new ProtocoloRN();
				$arrObjProtocoloDTO = $objProtocoloRN->pesquisarRN0967($objPesquisaProtocoloDTO);
	
				if (count($arrObjProtocoloDTO)==0){
					$objInfraException->lancarValidacao('Documento Base não encontrado.');
				}
	
				if ($arrObjProtocoloDTO[0]->getStrStaEditorDocumento()!=EditorRN::$TE_INTERNO){
					$objInfraException->lancarValidacao('Documento Base não foi gerado pelo editor interno.');
				}
	
				$objDocumentoDTO->setDblIdDocumentoTextoBase($arrObjProtocoloDTO[0]->getDblIdProtocolo());
			}
	
			$objInfraException->lancarValidacoes();
	
			$objProtocoloDTO = $objDocumentoDTO->getObjProtocoloDTO();
			$objProtocoloDTO->setDblIdProcedimento($objDocumentoDTO->getDblIdProcedimento());
	
			$this->tratarProtocoloRN1164($objDocumentoDTO);
	
			$objMdPetProtocoloRN = new MdPetProtocoloRN();
			$objProtocoloDTOGerado  = $objMdPetProtocoloRN->gerarRN0154($objProtocoloDTO);
	
			$objDocumentoDTO->setDblIdProcedimento($objProtocoloDTO->getDblIdProcedimento());
			$objDocumentoDTO->setDblIdDocumento($objProtocoloDTOGerado->getDblIdProtocolo());
			$objDocumentoDTO->setDblIdDocumentoEdoc(null);
	
			$objSerieDTO = new SerieDTO();
			$objSerieDTO->retNumIdSerie();
			$objSerieDTO->setBolExclusaoLogica(false);
			$objSerieDTO->retStrNome();
			$objSerieDTO->retStrStaNumeracao();
			$objSerieDTO->retNumIdModelo();
			$objSerieDTO->setNumIdSerie($objDocumentoDTO->getNumIdSerie());
	
			$objSerieRN = new SerieRN();
			$objSerieDTO = $objSerieRN->consultarRN0644($objSerieDTO);
	
			//Associar o documento nesta unidade e nas unidades que tem acesso ao processo
			$objAssociarDTO = new AssociarDTO();
			$objAssociarDTO->setDblIdProcedimento($objProtocoloDTO->getDblIdProcedimento());
			//seiv2
			//$objAssociarDTO->setDblIdDocumento($objDocumentoDTO->getDblIdDocumento());
			$objAssociarDTO->setNumIdUnidade( $idUnidadeResponsavel );
			//alteracoes seiv3
			//$objAssociarDTO->setNumIdUnidade(null);
			$objAssociarDTO->setNumIdUsuario(null);
			$objAssociarDTO->setStrStaNivelAcessoGlobal($objProtocoloDTOGerado->getStrStaNivelAcessoGlobal());
			$objMdPetProtocoloRN->associarRN0982($objAssociarDTO);
	
			$objRelProtocoloProtocoloDTO = new RelProtocoloProtocoloDTO();
			$objRelProtocoloProtocoloDTO->setDblIdRelProtocoloProtocolo(null);
			$objRelProtocoloProtocoloDTO->setDblIdProtocolo1($objProtocoloDTO->getDblIdProcedimento());
			$objRelProtocoloProtocoloDTO->setDblIdProtocolo2($objDocumentoDTO->getDblIdDocumento());
			$objRelProtocoloProtocoloDTO->setNumIdUsuario($objProtocoloDTO->getNumIdUsuarioGerador());
			$objRelProtocoloProtocoloDTO->setNumIdUnidade ($objProtocoloDTO->getNumIdUnidadeGeradora());
			$objRelProtocoloProtocoloDTO->setNumSequencia($objMdPetProtocoloRN->obterSequencia($objProtocoloDTO));
			$objRelProtocoloProtocoloDTO->setStrStaAssociacao (RelProtocoloProtocoloRN::$TA_DOCUMENTO_ASSOCIADO);
			$objRelProtocoloProtocoloDTO->setDthAssociacao(InfraData::getStrDataHoraAtual());
	
			$objRelProtocoloProtocoloRN = new RelProtocoloProtocoloRN();
			$objRelProtocoloProtocoloRN->cadastrarRN0839($objRelProtocoloProtocoloDTO);
		
			//numeracao - inicio
			$strNomeSerie = $objSerieDTO->getStrNome();
			$strStaNumeracao = $objSerieDTO->getStrStaNumeracao();
	
			if($strStaNumeracao == SerieRN::$TN_SEM_NUMERACAO){
				// nao deve entrar nunca
				if(!InfraString::isBolVazia($objDocumentoDTO->getStrNumero())){
					$objInfraException->lancarValidacao('Documento com número preenchido mas o tipo '.$strNomeSerie.' não tem numeração.');
				}
			}else if($strStaNumeracao == SerieRN::$TN_INFORMADA){
				if(InfraString::isBolVazia($objDocumentoDTO->getStrNumero())){
					$objInfraException->lancarValidacao('Tipo '.$strNomeSerie.' requer preenchimento do número do documento.');
				}else{
					$this->validarTamanhoNumeroRN0993($objDocumentoDTO, $objInfraException);
				}
			}else if (InfraString::isBolVazia($objDocumentoDTO->getStrNumero())) {
				 
				$objUnidadeDTO = new UnidadeDTO();
				$objUnidadeDTO->retNumIdOrgao();
				$objUnidadeDTO->retStrSigla();
				$objUnidadeDTO->setNumIdUnidade($objDocumentoDTO->getNumIdUnidadeResponsavel());
				 
				$objUnidadeRN = new UnidadeRN();
				$objUnidadeDTO = $objUnidadeRN->consultarRN0125($objUnidadeDTO);
	
				$objNumeracaoDTO = new NumeracaoDTO();
				$objNumeracaoDTO->retNumIdNumeracao();
				$objNumeracaoDTO->setNumIdSerie($objDocumentoDTO->getNumIdSerie());
	
				if($strStaNumeracao == SerieRN::$TN_SEQUENCIAL_UNIDADE){
					$objNumeracaoDTO->setNumIdUnidade($objDocumentoDTO->getNumIdUnidadeResponsavel());
					$objNumeracaoDTO->setNumIdOrgao(null);
					$objNumeracaoDTO->setNumAno(null);
				}else if($strStaNumeracao == SerieRN::$TN_SEQUENCIAL_ORGAO){
					$objNumeracaoDTO->setNumIdUnidade(null);
					$objNumeracaoDTO->setNumIdOrgao($objUnidadeDTO->getNumIdOrgao());
					$objNumeracaoDTO->setNumAno(null);
				}else if($strStaNumeracao == SerieRN::$TN_SEQUENCIAL_ANUAL_UNIDADE){
					$objNumeracaoDTO->setNumIdUnidade($objDocumentoDTO->getNumIdUnidadeResponsavel());
					$objNumeracaoDTO->setNumIdOrgao(null);
					$objNumeracaoDTO->setNumAno(Date('Y'));
				}else if($strStaNumeracao == SerieRN::$TN_SEQUENCIAL_ANUAL_ORGAO){
					$objNumeracaoDTO->setNumIdUnidade(null);
					$objNumeracaoDTO->setNumIdOrgao($objUnidadeDTO->getNumIdOrgao());
					$objNumeracaoDTO->setNumAno(Date('Y'));
				}else{
					$objInfraException->lancarValidacao('Tipo de numeração inválido.');
				}
	
				$objNumeracaoRN = new NumeracaoRN();
				$objNumeracaoDTORet = $objNumeracaoRN->consultar($objNumeracaoDTO);
	
				if ($objNumeracaoDTORet == null){
					$objNumeracaoDTO->setNumSequencial(0);
					$objNumeracaoDTORet = $objNumeracaoRN->cadastrar($objNumeracaoDTO);
				}
	
				$objNumeracaoDTORet = $objNumeracaoRN->bloquear($objNumeracaoDTORet);
	
				$objNumeracaoDTO = new NumeracaoDTO();
				$objNumeracaoDTO->setNumSequencial($objNumeracaoDTORet->getNumSequencial()+1);
				$objNumeracaoDTO->setNumIdNumeracao($objNumeracaoDTORet->getNumIdNumeracao());
	
				$objDocumentoDTO->setStrNumero($objNumeracaoDTO->getNumSequencial());
	
				$objNumeracaoRN->alterar($objNumeracaoDTO);
			}
			//numeracao - fim
	
			$objDocumentoBD = new DocumentoBD($this->getObjInfraIBanco());
			$objDocumentoDTO = $objDocumentoBD->cadastrar($objDocumentoDTO);
	
			$objInfraException->lancarValidacoes();
	
			$this->verificarSobrestamento($objDocumentoDTO);
		
			$arrObjAtributoAndamentoDTO = array();
			$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
			$objAtributoAndamentoDTO->setStrNome('DOCUMENTO');
			$objAtributoAndamentoDTO->setStrValor($objProtocoloDTOGerado->getStrProtocoloFormatado());
			$objAtributoAndamentoDTO->setStrIdOrigem($objProtocoloDTOGerado->getDblIdProtocolo());
			$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;
	
			$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
			$objAtributoAndamentoDTO->setStrNome('NIVEL_ACESSO');
			$objAtributoAndamentoDTO->setStrValor(null);
			$objAtributoAndamentoDTO->setStrIdOrigem($objDocumentoDTO->getObjProtocoloDTO()->getStrStaNivelAcessoLocal());
			$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;
	
			if (!InfraString::isBolVazia($objDocumentoDTO->getObjProtocoloDTO()->getNumIdHipoteseLegal())){
				$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
				$objAtributoAndamentoDTO->setStrNome('HIPOTESE_LEGAL');
				$objAtributoAndamentoDTO->setStrValor(null);
				$objAtributoAndamentoDTO->setStrIdOrigem($objDocumentoDTO->getObjProtocoloDTO()->getNumIdHipoteseLegal());
				$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;
			}
	
			if (!InfraString::isBolVazia($objDocumentoDTO->getObjProtocoloDTO()->getStrStaGrauSigilo())){
				$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
				$objAtributoAndamentoDTO->setStrNome('GRAU_SIGILO');
				$objAtributoAndamentoDTO->setStrValor(null);
				$objAtributoAndamentoDTO->setStrIdOrigem($objDocumentoDTO->getObjProtocoloDTO()->getStrStaGrauSigilo());
				$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;
			}
	
			$objAtividadeDTO = new AtividadeDTO();
			$objAtividadeDTO->setDblIdProtocolo($objProtocoloDTO->getDblIdProcedimento());
			$objAtividadeDTO->setNumIdUnidade( $idUnidadeResponsavel );
			$objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_GERACAO_DOCUMENTO);
			$objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO);
	
			$objMdPetAtividadeRN = new MdPetAtividadeRN();
			$objMdPetAtividadeRN->gerarInternaRN0727($objAtividadeDTO);
		
			$arrAnexos = $objProtocoloDTO->getArrObjAnexoDTO();
			for($i=0;$i<count($arrAnexos);$i++){
	
				$arrObjAtributoAndamentoDTO = array();
				$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
				$objAtributoAndamentoDTO->setStrNome('ANEXO');
				$objAtributoAndamentoDTO->setStrValor($arrAnexos[$i]->getStrNome());
				$objAtributoAndamentoDTO->setStrIdOrigem($arrAnexos[$i]->getNumIdAnexo());
				$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;
	
				$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
				$objAtributoAndamentoDTO->setStrNome('DOCUMENTO');
				$objAtributoAndamentoDTO->setStrValor($objProtocoloDTOGerado->getStrProtocoloFormatado());
				$objAtributoAndamentoDTO->setStrIdOrigem($objProtocoloDTOGerado->getDblIdProtocolo());
				$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;
	
				$objAtividadeDTO = new AtividadeDTO();
				$objAtividadeDTO->setDblIdProtocolo($objProtocoloDTO->getDblIdProcedimento());
				$objAtividadeDTO->setNumIdUnidade( $idUnidadeResponsavel );
				$objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_ARQUIVO_ANEXADO);
				$objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO);
				 
				$objMdPetAtividadeRN = new MdPetAtividadeRN();
				$objMdPetAtividadeRN->gerarInternaRN0727($objAtividadeDTO);
				 
			}
	        
			//seiv2
			/* 
			$strStaEditor = $objDocumentoDTO->getStrStaEditor();
	
			if ($strStaEditor == EditorRN::$TE_EDOC){
				 
				if (!InfraString::isBolVazia($objDocumentoDTO->getDblIdDocumentoEdocBase())){
	
					if (ConfiguracaoSEI::getInstance()->getValor('Editor','Edoc')){
	
						$objEDocRN = new EDocRN();
						$dto = new DocumentoDTO();
						$dto->setDblIdDocumento($objProtocoloDTOGerado->getDblIdProtocolo());
						$dto->setDblIdDocumentoEdoc(null);
						$dto->setDblIdDocumentoEdocBase($objDocumentoDTO->getDblIdDocumentoEdocBase());
						$dto->setDblIdTextoPadraoEdoc(null);
						$objEDocRN->processarDocumentoRN1143($dto);
	
					}else{
	
						$dto = new DocumentoDTO();
						$dto->setStrStaEditor(EditorRN::$TE_INTERNO);
						$dto->setDblIdDocumento($objProtocoloDTOGerado->getDblIdProtocolo());
	
						$objDocumentoBD->alterar($dto);
	
						$strStaEditor = EditorRN::$TE_INTERNO;
	
					}
				}
			}
	
			if ($strStaEditor == EditorRN::$TE_INTERNO){
			*/	 
				$objEditorDTO = new EditorDTO();
				$objEditorDTO->setDblIdDocumento($objProtocoloDTOGerado->getDblIdProtocolo());
				$objEditorDTO->setNumIdBaseConhecimento(null);
	
				if ($objSerieDTO->getNumIdModelo()==null){
					throw new InfraException('Tipo '.$objSerieDTO->getStrNome().' não possui modelo interno associado.');
				}
	
				$objEditorDTO->setNumIdModelo($objSerieDTO->getNumIdModelo());
	
				if ($objDocumentoDTO->isSetDblIdDocumentoBase() && !InfraString::isBolVazia($objDocumentoDTO->getDblIdDocumentoBase())){
	
					$objEditorDTO->setDblIdDocumentoBase($objDocumentoDTO->getDblIdDocumentoBase());
	
				}else if ($objDocumentoDTO->isSetDblIdDocumentoTextoBase() && !InfraString::isBolVazia($objDocumentoDTO->getDblIdDocumentoTextoBase())){
	
					$objEditorDTO->setDblIdDocumentoTextoBase($objDocumentoDTO->getDblIdDocumentoTextoBase());
	
				}else if ($objDocumentoDTO->isSetDblIdDocumentoEdocBase() && !InfraString::isBolVazia($objDocumentoDTO->getDblIdDocumentoEdocBase())){
	
					$objEditorDTO->setDblIdDocumentoEdocBase($objDocumentoDTO->getDblIdDocumentoEdocBase());
	
				}else if ($objDocumentoDTO->getStrConteudo()!=null){
	
					$objEditorDTO->setStrConteudoSecaoPrincipal($objDocumentoDTO->getStrConteudo());
	
				}else if ($objDocumentoDTO->isSetNumIdTextoPadraoInterno() && $objDocumentoDTO->getNumIdTextoPadraoInterno()!=null){
					$objEditorDTO->setNumIdTextoPadraoInterno($objDocumentoDTO->getNumIdTextoPadraoInterno());
				}
					
				$objEditorRN = new MdPetEditorUsuarioExternoRN();
				$objEditorRN->gerarVersaoInicial($objEditorDTO);
			
			//seiv2
		    //}
	
			$objDocumentoDTO->setStrStaNivelAcessoGlobalProtocolo($objProtocoloDTOGerado->getStrStaNivelAcessoGlobal());
			$this->lancarAcessoControleInterno($objDocumentoDTO);
	
			$objSerieEscolhaDTO = new SerieEscolhaDTO();
			$objSerieEscolhaDTO->setNumIdSerie($objDocumentoDTO->getNumIdSerie());
			$objSerieEscolhaDTO->setNumIdUnidade( $idUnidadeResponsavel );
	
			$objSerieEscolhaRN = new SerieEscolhaRN();
			if ($objSerieEscolhaRN->contar($objSerieEscolhaDTO)==0){
				$objSerieEscolhaRN->cadastrar($objSerieEscolhaDTO);
			}
	
			$ret = new DocumentoDTO();
			$ret->setDblIdDocumento($objProtocoloDTOGerado->getDblIdProtocolo());
			$ret->setStrProtocoloDocumentoFormatado($objProtocoloDTOGerado->getStrProtocoloFormatado());
			return $ret;
	
		} catch(Exception $e){
			throw new InfraException('Erro gerando Documento.',$e);
		}
		
	}
	
	private function validarStrStaEditor(DocumentoDTO $objDocumentoDTO, InfraException $objInfraException){
		
		//seiv2
		//if (InfraString::isBolVazia($objDocumentoDTO->getStrStaEditor())){
			//$objInfraException->adicionarValidacao('Editor não informado.');
		//}
	
		//if ($objDocumentoDTO->getStrStaEditor()!=EditorRN::$TE_EDOC && $objDocumentoDTO->getStrStaEditor()!=EditorRN::$TE_INTERNO && $objDocumentoDTO->getStrStaEditor()!=EditorRN::$TE_NENHUM){
			//$objInfraException->adicionarValidacao('Editor ['.$objDocumentoDTO->getStrStaEditor().'] inválido.');
		//}
		
	}
	
	private function validarStrSinFormulario(DocumentoDTO $objDocumentoDTO, InfraException $objInfraException){
		
		//seiv2
		//if (InfraString::isBolVazia($objDocumentoDTO->getStrSinFormulario())){
			//$objInfraException->adicionarValidacao('Sinalizador de Formulário não informado.');
		//}else{
			//if (!InfraUtil::isBolSinalizadorValido($objDocumentoDTO->getStrSinFormulario())){
				//$objInfraException->adicionarValidacao('Sinalizador de Formulário inválido.');
			//}
		//}
		
	}
	
	private function validarNumIdUnidadeResponsavelRN0915(DocumentoDTO $objDocumentoDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objDocumentoDTO->getNumIdUnidadeResponsavel ())){
			$objInfraException->adicionarValidacao('Unidade Responsável não informada.');
		}
	}
	
	private function validarStrCrcAssinatura(DocumentoDTO $objDocumentoDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objDocumentoDTO->getStrCrcAssinatura())){
			$objDocumentoDTO->setStrCrcAssinatura(null);
		}else{
			$objDocumentoDTO->setStrCrcAssinatura(strtoupper(trim($objDocumentoDTO->getStrCrcAssinatura())));
			if (strlen($objDocumentoDTO->getStrCrcAssinatura())>8){
				$objInfraException->lancarValidacao('Tamanho do código CRC inválido.');
			}
		}
	}
	
	private function validarStrCodigoVerificador(DocumentoDTO $objDocumentoDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objDocumentoDTO->getStrCodigoVerificador())){
			$objDocumentoDTO->setStrCodigoVerificador(null);
		}else{
			$objDocumentoDTO->setStrCodigoVerificador(strtoupper(trim($objDocumentoDTO->getStrCodigoVerificador())));
			if (!preg_match("/^[0-9]{10}(V)[0-9]+$/i", $objDocumentoDTO->getStrCodigoVerificador()) && strlen($objDocumentoDTO->getStrCodigoVerificador())!=7){
				$objInfraException->lancarValidacao('Código Verificador inválido.');
			}
		}
	}
	
	private function validarNumIdSerieRN0009(DocumentoDTO $objDocumentoDTO, InfraException $objInfraException){
		 
		if (InfraString::isBolVazia($objDocumentoDTO->getNumIdSerie())){
			$objInfraException->lancarValidacao('ABS Tipo do documento não informado.');
		}else{
	
			$objSerieDTO = new SerieDTO();
			$objSerieDTO->setBolExclusaoLogica(false);
			$objSerieDTO->retStrStaAplicabilidade();
			$objSerieDTO->retStrNome();
			$objSerieDTO->setNumIdSerie($objDocumentoDTO->getNumIdSerie());
			 
			$objSerieRN = new SerieRN();
			$objSerieDTO = $objSerieRN->consultarRN0644($objSerieDTO);
	
			if ($objSerieDTO==null){
				throw new InfraException('Tipo do documento ['.$objDocumentoDTO->getNumIdSerie().'] não encontrado.');
			}
	        
			//seiv2
			//if ($objSerieDTO->getStrStaAplicabilidade()!=SerieRN::$TA_TODOS){
			
			//alteracoes seiv3
			if ($objSerieDTO->getStrStaAplicabilidade()!=SerieRN::$TA_INTERNO_EXTERNO){
				if ($objDocumentoDTO->getStrStaProtocoloProtocolo()==ProtocoloRN::$TP_DOCUMENTO_GERADO && $objSerieDTO->getStrStaAplicabilidade()==SerieRN::$TA_EXTERNO){
					$objInfraException->adicionarValidacao('Tipo do documento não aplicável para documentos internos.');
				}else if ($objDocumentoDTO->getStrStaProtocoloProtocolo()==ProtocoloRN::$TP_DOCUMENTO_RECEBIDO && $objSerieDTO->getStrStaAplicabilidade()==SerieRN::$TA_INTERNO){
					$objInfraException->adicionarValidacao('Tipo do documento não aplicável para documentos externos.');
				}
			}
		}
	}
	
	private function validarTamanhoNumeroRN0993(DocumentoDTO $objDocumentoDTO, InfraException $objInfraException){
		$objDocumentoDTO->setStrNumero(trim($objDocumentoDTO->getStrNumero()));
		if (strlen($objDocumentoDTO->getStrNumero())>50){
			$objInfraException->adicionarValidacao('Número possui tamanho superior a 50 caracteres.');
		}
	}
	
	private function validarStrConteudo(DocumentoDTO $objDocumentoDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objDocumentoDTO->getStrConteudo())){
			$objDocumentoDTO->setStrConteudo(null);
		}
	}
	
	private function validarNumIdTipoConferencia(DocumentoDTO $objDocumentoDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objDocumentoDTO->getNumIdTipoConferencia())){
			$objDocumentoDTO->setNumIdTipoConferencia(null);
		}
	}

	private function tratarProtocoloRN1164(DocumentoDTO $objDocumentoDTO) {
				
		try{
			
			$idUnidadeResponsavel = $objDocumentoDTO->getNumIdUnidadeResponsavel();						
			$objProtocoloDTO = $objDocumentoDTO->getObjProtocoloDTO();	
			$objProtocoloDTO->setStrProtocoloFormatado(null);	
			$objProtocoloDTO->setStrStaProtocolo($objDocumentoDTO->getStrStaProtocoloProtocolo());
	
			if (!$objProtocoloDTO->isSetNumIdUnidadeGeradora()){
				$objProtocoloDTO->setNumIdUnidadeGeradora( $idUnidadeResponsavel );
			}
	
			if (!$objProtocoloDTO->isSetNumIdUsuarioGerador()){
				$objProtocoloDTO->setNumIdUsuarioGerador(SessaoSEI::getInstance()->getNumIdUsuario());
			}
	
			if (!$objProtocoloDTO->isSetDtaGeracao()){
				$objProtocoloDTO->setDtaGeracao(InfraData::getStrDataAtual());
			}
	
			if (!$objProtocoloDTO->isSetArrObjRelProtocoloAssuntoDTO()){
				$objProtocoloDTO->setArrObjRelProtocoloAssuntoDTO(array());
			}
	
			$objDocumentoDTO->setObjProtocoloDTO($objProtocoloDTO);
	
		}catch(Exception $e){
			throw new InfraException('Erro tratando protocolo do documento.',$e);
		}
	}
	
	protected function verificarSobrestamento(DocumentoDTO $objDocumentoDTO){
		
		try{
	
			$objProtocoloDTO = new ProtocoloDTO();
			$objProtocoloDTO->retStrStaEstado();
			$objProtocoloDTO->setDblIdProtocolo($objDocumentoDTO->getDblIdProcedimento());
	
			$objProtocoloRN = new ProtocoloRN();
			$objProtocoloDTO = $objProtocoloRN->consultarRN0186($objProtocoloDTO);
	
			if ($objProtocoloDTO->getStrStaEstado()==ProtocoloRN::$TE_PROCEDIMENTO_SOBRESTADO){
	
	
				$objRelProtocoloProtocoloDTO = new RelProtocoloProtocoloDTO();
				$objRelProtocoloProtocoloDTO->setDblIdProtocolo2($objDocumentoDTO->getDblIdProcedimento());
	
				$objProcedimentoRN = new ProcedimentoRN();
				$objProcedimentoRN->removerSobrestamentoRN1017(array($objRelProtocoloProtocoloDTO));
			}
	
		}catch(Exception $e){
			throw new InfraException('Erro verificando sobrestamento do processo.',$e);
		}
	}
	
	protected function lancarAcessoControleInternoControlado(DocumentoDTO $objDocumentoDTO){
		
		try{
	
			if ($objDocumentoDTO->getStrStaNivelAcessoGlobalProtocolo()!=ProtocoloRN::$NA_SIGILOSO){
	
				$objControleInternoDTO = new ControleInternoDTO();
				$objControleInternoDTO->setDistinct(true);
				
				//alteracoes seiv3
				$objControleInternoDTO->retNumIdUnidadeControle();
				
				//seiv2
				//$objControleInternoDTO->retNumIdUnidadeRelControleInternoUnidade();
				
				//alteracoes seiv3
				$objControleInternoDTO->setNumIdSerieControlada($objDocumentoDTO->getNumIdSerie());
				$objControleInternoDTO->setNumIdOrgaoControlado(SessaoSEI::getInstance()->getNumIdOrgaoUnidadeAtual());
				$objControleInternoDTO->setNumIdUnidadeControle(SessaoSEI::getInstance()->getNumIdUnidadeAtual(),InfraDTO::$OPER_DIFERENTE);
								
				//seiv2
				//$objControleInternoDTO->setNumIdSerieRelControleInternoSerie($objDocumentoDTO->getNumIdSerie());
				//$objControleInternoDTO->setNumIdOrgaoRelControleInternoOrgao(SessaoSEI::getInstance()->getNumIdOrgaoUnidadeAtual());
				//$objControleInternoDTO->setNumIdUnidadeRelControleInternoUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual(),InfraDTO::$OPER_DIFERENTE);
	
				$objControleInternoRN = new ControleInternoRN();
				$arrObjControleInternoDTO = $objControleInternoRN->listar($objControleInternoDTO);
	
				$objProtocoloRN = new ProtocoloRN();
	
				foreach($arrObjControleInternoDTO as $objControleInternoDTO){
	
					$objAtividadeDTO = new AtividadeDTO();
					$objAtividadeDTO->setDblIdProtocolo($objDocumentoDTO->getDblIdProcedimento());
					$objAtividadeDTO->setNumIdUnidade($objControleInternoDTO->getNumIdUnidadeRelControleInternoUnidade());
	
					$objAtividadeRN = new AtividadeRN();
	
					if ($objAtividadeRN->contarRN0035($objAtividadeDTO)==0){
	
						$objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_ACESSO_AUTOMATICO_AO_PROCESSO);
	
						$objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);
	
						//Associar o processo e seus documentos com esta unidade
						$objAssociarDTO = new AssociarDTO();
						$objAssociarDTO->setDblIdProcedimento($objDocumentoDTO->getDblIdProcedimento());
						$objAssociarDTO->setDblIdDocumento(null);
						$objAssociarDTO->setNumIdUnidade($objControleInternoDTO->getNumIdUnidadeRelControleInternoUnidade());
						$objAssociarDTO->setNumIdUsuario(null);
						$objAssociarDTO->setStrStaNivelAcessoGlobal($objDocumentoDTO->getStrStaNivelAcessoGlobalProtocolo());
	
						$objProtocoloRN->associarRN0982($objAssociarDTO);
	
					}
				}
			}
		} catch(Exception $e){
			 throw new InfraException('Erro lançando acesso para o Controle Interno.',$e);
		}
		
	}
	
	//método copiado da antiga DocumentoRN do seiv2, porque o SEIv3 removeu o metodo receberRN0991
	public function receberRN0991(DocumentoDTO $objDocumentoDTO){
	
		$bolAcumulacaoPrevia = FeedSEIProtocolos::getInstance()->isBolAcumularFeeds();
	
		FeedSEIProtocolos::getInstance()->setBolAcumularFeeds(true);
	
		$objDocumentoDTO = $this->receberRN0991Interno($objDocumentoDTO);
	
		$objIndexacaoDTO = new IndexacaoDTO();
		$objIndexacaoRN = new IndexacaoRN();
	
		$objProtocoloDTO = new ProtocoloDTO();
		$objProtocoloDTO->setDblIdProtocolo($objDocumentoDTO->getDblIdDocumento());
	
		$objIndexacaoDTO->setArrIdProtocolos( array( $objProtocoloDTO->getDblIdProtocolo() ) );
		$objIndexacaoDTO->setStrStaOperacao( IndexacaoRN::$TO_PROTOCOLO_METADADOS );
		$objIndexacaoRN->indexarProtocolo($objIndexacaoDTO);
	
		if (!$bolAcumulacaoPrevia){
			FeedSEIProtocolos::getInstance()->setBolAcumularFeeds(false);
			FeedSEIProtocolos::getInstance()->indexarFeeds();
		}
	
		return $objDocumentoDTO;
	}
	
	//método copiado da antiga DocumentoRN do seiv2 (com pequenas alterações), porque o SEIv3 removeu o metodo receberRN0991InternoControlado
	protected function receberRN0991InternoControlado(DocumentoDTO $objDocumentoDTO) {
		try{
	
			global $SEI_MODULOS;
	
			//Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao('documento_receber',__METHOD__,$objDocumentoDTO);
	
			//Regras de Negocio
			$objInfraException = new InfraException();
	
			$objDocumentoDTO->setStrStaProtocoloProtocolo(ProtocoloRN::$TP_DOCUMENTO_RECEBIDO);
	
			if ($objDocumentoDTO->isSetDblIdDocumentoEdoc() && $objDocumentoDTO->getDblIdDocumentoEdoc()!=null){
				$objInfraException->adicionarValidacao('Identificador do eDoc não pode ser informado na geração.');
			}
	
	
			$this->validarNumIdUnidadeResponsavelRN0915($objDocumentoDTO, $objInfraException);
			$this->validarNumIdSerieRN0009($objDocumentoDTO, $objInfraException);
			$this->validarTamanhoNumeroRN0993($objDocumentoDTO, $objInfraException);
	
			//conteudo nao existe nas telas de cadastro, apenas em documentos gerados por servicos
			if ($objDocumentoDTO->isSetStrConteudo()){
				$this->validarStrConteudo($objDocumentoDTO, $objInfraException);
			}else{
				$objDocumentoDTO->setStrConteudo(null);
			}
	
			$objDocumentoDTO->setStrConteudoAssinatura(null);
			$objDocumentoDTO->setStrCrcAssinatura(null);
			$objDocumentoDTO->setStrQrCodeAssinatura(null);
			$objDocumentoDTO->setStrSinBloqueado('N');
			
			if ($objDocumentoDTO->getObjProtocoloDTO()->isSetArrObjAnexoDTO()){
				if (count($objDocumentoDTO->getObjProtocoloDTO()->getArrObjAnexoDTO())>1){
					throw new InfraException('Mais de um anexo informado.');
				}
			}
	
			$this->validarNumIdTipoConferencia($objDocumentoDTO, $objInfraException);
	
			$objInfraException->lancarValidacoes();
	
			$objProtocoloDTO = $objDocumentoDTO->getObjProtocoloDTO();
			$objProtocoloDTO->setDblIdProcedimento($objDocumentoDTO->getDblIdProcedimento());
	
			$this->tratarProtocoloRN1164($objDocumentoDTO);
	
			$objProtocoloRN = new ProtocoloRN();
			$objProtocoloDTOGerado  = $objProtocoloRN->gerarRN0154($objProtocoloDTO);
	
			$objDocumentoDTO->setDblIdProcedimento($objProtocoloDTO->getDblIdProcedimento());
			$objDocumentoDTO->setDblIdDocumento($objProtocoloDTOGerado->getDblIdProtocolo());
			$objDocumentoDTO->setDblIdDocumentoEdoc(null);
	
			$objSerieDTO = new SerieDTO();
			$objSerieDTO->setBolExclusaoLogica(false);
			$objSerieDTO->retStrNome();
			$objSerieDTO->setNumIdSerie($objDocumentoDTO->getNumIdSerie());
	
			$objSerieRN = new SerieRN();
			$objSerieDTO = $objSerieRN->consultarRN0644($objSerieDTO);
	
			//Associar o documento nesta unidade e nas unidades que tem acesso ao processo
			$objAssociarDTO = new AssociarDTO();
			$objAssociarDTO->setDblIdProcedimento($objProtocoloDTO->getDblIdProcedimento());
			//seiv2
			//$objAssociarDTO->setNumIdUnidade(null);
			
			//alteracoes seiv3
			$objAssociarDTO->setNumIdUnidade( $objProtocoloDTO->getNumIdUnidadeGeradora() );
			
			$objAssociarDTO->setNumIdUsuario(null);
			$objAssociarDTO->setStrStaNivelAcessoGlobal($objProtocoloDTOGerado->getStrStaNivelAcessoGlobal());
			$objProtocoloRN->associarRN0982($objAssociarDTO);
		
			$objRelProtocoloProtocoloDTO = new RelProtocoloProtocoloDTO();
			$objRelProtocoloProtocoloDTO->setDblIdRelProtocoloProtocolo(null);
			$objRelProtocoloProtocoloDTO->setDblIdProtocolo1($objProtocoloDTO->getDblIdProcedimento());
			$objRelProtocoloProtocoloDTO->setDblIdProtocolo2($objDocumentoDTO->getDblIdDocumento());
			$objRelProtocoloProtocoloDTO->setNumIdUsuario($objProtocoloDTO->getNumIdUsuarioGerador());
			$objRelProtocoloProtocoloDTO->setNumIdUnidade ($objProtocoloDTO->getNumIdUnidadeGeradora());
			$objRelProtocoloProtocoloDTO->setStrStaAssociacao (RelProtocoloProtocoloRN::$TA_DOCUMENTO_ASSOCIADO);
			$objRelProtocoloProtocoloDTO->setNumSequencia($objProtocoloRN->obterSequencia($objProtocoloDTO));
			$objRelProtocoloProtocoloDTO->setDthAssociacao(InfraData::getStrDataHoraAtual());
	
			$objRelProtocoloProtocoloRN = new RelProtocoloProtocoloRN();
			$objRelProtocoloProtocoloRN->cadastrarRN0839($objRelProtocoloProtocoloDTO);
	
			$objDocumentoBD = new DocumentoBD($this->getObjInfraIBanco());
			$objDocumentoBD->cadastrar($objDocumentoDTO);
	
			$this->verificarSobrestamento($objDocumentoDTO);
		
			$arrObjAtributoAndamentoDTO = array();
			$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
			$objAtributoAndamentoDTO->setStrNome('DOCUMENTO');
			$objAtributoAndamentoDTO->setStrValor($objProtocoloDTOGerado->getStrProtocoloFormatado());
			$objAtributoAndamentoDTO->setStrIdOrigem($objProtocoloDTOGerado->getDblIdProtocolo());
			$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;
	
			$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
			$objAtributoAndamentoDTO->setStrNome('NIVEL_ACESSO');
			$objAtributoAndamentoDTO->setStrValor(null);
			$objAtributoAndamentoDTO->setStrIdOrigem($objDocumentoDTO->getObjProtocoloDTO()->getStrStaNivelAcessoLocal());
			$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;
	
			if (!InfraString::isBolVazia($objDocumentoDTO->getObjProtocoloDTO()->getNumIdHipoteseLegal())){
				$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
				$objAtributoAndamentoDTO->setStrNome('HIPOTESE_LEGAL');
				$objAtributoAndamentoDTO->setStrValor(null);
				$objAtributoAndamentoDTO->setStrIdOrigem($objDocumentoDTO->getObjProtocoloDTO()->getNumIdHipoteseLegal());
				$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;
			}
	
			if (!InfraString::isBolVazia($objDocumentoDTO->getObjProtocoloDTO()->getStrStaGrauSigilo())){
				$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
				$objAtributoAndamentoDTO->setStrNome('GRAU_SIGILO');
				$objAtributoAndamentoDTO->setStrValor(null);
				$objAtributoAndamentoDTO->setStrIdOrigem($objDocumentoDTO->getObjProtocoloDTO()->getStrStaGrauSigilo());
				$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;
			}
	
			if (!InfraString::isBolVazia($objDocumentoDTO->getNumIdTipoConferencia())){
				$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
				$objAtributoAndamentoDTO->setStrNome('TIPO_CONFERENCIA');
				$objAtributoAndamentoDTO->setStrValor(null);
				$objAtributoAndamentoDTO->setStrIdOrigem($objDocumentoDTO->getNumIdTipoConferencia());
				$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;
			}
	
			$objAtividadeDTO = new AtividadeDTO();
			$objAtividadeDTO->setDblIdProtocolo($objProtocoloDTO->getDblIdProcedimento());
			$objAtividadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
			$objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_RECEBIMENTO_DOCUMENTO);
			$objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO);
	
			$objAtividadeRN = new AtividadeRN();
			$objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);
	
			$arrAnexos = $objProtocoloDTO->getArrObjAnexoDTO();
			for($i=0;$i<count($arrAnexos);$i++){
	
				$arrObjAtributoAndamentoDTO = array();
				$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
				$objAtributoAndamentoDTO->setStrNome('ANEXO');
				$objAtributoAndamentoDTO->setStrValor($arrAnexos[$i]->getStrNome());
				$objAtributoAndamentoDTO->setStrIdOrigem($arrAnexos[$i]->getNumIdAnexo());
				$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;
	
				$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
				$objAtributoAndamentoDTO->setStrNome('DOCUMENTO');
				$objAtributoAndamentoDTO->setStrValor($objProtocoloDTOGerado->getStrProtocoloFormatado());
				$objAtributoAndamentoDTO->setStrIdOrigem($objProtocoloDTOGerado->getDblIdProtocolo());
				$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;
	
				$objAtividadeDTO = new AtividadeDTO();
				$objAtividadeDTO->setDblIdProtocolo($objProtocoloDTO->getDblIdProcedimento());
				$objAtividadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
				$objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_ARQUIVO_ANEXADO);
				$objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO);
				 
				$objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);
			}
	
			$objDocumentoDTO->setStrStaNivelAcessoGlobalProtocolo($objProtocoloDTOGerado->getStrStaNivelAcessoGlobal());
			$this->lancarAcessoControleInterno($objDocumentoDTO);
	
			//Reabertura Automática
			if ($objDocumentoDTO->isSetArrObjUnidadeDTO() && count($objDocumentoDTO->getArrObjUnidadeDTO()) > 0){
	
				if ($objProtocoloDTOGerado->getStrStaNivelAcessoGlobal()==ProtocoloRN::$NA_SIGILOSO){
					$objInfraException->lancarValidacao('Não é possível reabrir automaticamente um processo sigiloso.');
				}
	
				$objUnidadeDTO = new UnidadeDTO();
				$objUnidadeDTO->setBolExclusaoLogica(false);
				$objUnidadeDTO->retStrSigla();
				$objUnidadeDTO->retStrSinProtocolo();
				$objUnidadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
	
				$objUnidadeRN = new UnidadeRN();
				$objUnidadeDTO = $objUnidadeRN->consultarRN0125($objUnidadeDTO);
	
				if ($objUnidadeDTO->getStrSinProtocolo()=='N'){
					$objInfraException->lancarValidacao('Unidade '.$objUnidadeDTO->getStrSigla().' não está sinalizada como protocolo.');
				}
	
				$arrIdUnidadesReabertura = InfraArray::converterArrInfraDTO($objDocumentoDTO->getArrObjUnidadeDTO(),'IdUnidade');
	
				$objAtividadeDTO = new AtividadeDTO();
				$objAtividadeDTO->setDistinct(true);
				$objAtividadeDTO->retNumIdUnidade();
				$objAtividadeDTO->setStrStaNivelAcessoGlobalProtocolo(ProtocoloRN::$NA_SIGILOSO, InfraDTO::$OPER_DIFERENTE);
				$objAtividadeDTO->setDblIdProtocolo($objDocumentoDTO->getDblIdProcedimento());
				$objAtividadeDTO->setNumIdTarefa(array(TarefaRN::$TI_GERACAO_PROCEDIMENTO, TarefaRN::$TI_PROCESSO_REMETIDO_UNIDADE), InfraDTO::$OPER_IN);
				$objAtividadeDTO->setNumIdUnidade($arrIdUnidadesReabertura,InfraDTO::$OPER_IN);
	
				$arrIdUnidadeTramitacao = InfraArray::converterArrInfraDTO($objAtividadeRN->listarRN0036($objAtividadeDTO),'IdUnidade');
	
				foreach($arrIdUnidadesReabertura as $numIdUnidadeReabertura){
					if (!in_array($numIdUnidadeReabertura, $arrIdUnidadeTramitacao)){
	
						$objUnidadeDTO = new UnidadeDTO();
						$objUnidadeDTO->setBolExclusaoLogica(false);
						$objUnidadeDTO->retStrSigla();
						$objUnidadeDTO->setNumIdUnidade($numIdUnidadeReabertura);
	
						$objUnidadeDTO = $objUnidadeRN->consultarRN0125($objUnidadeDTO);
	
						if ($objUnidadeDTO==null){
							$objInfraException->adicionarValidacao('Unidade ['.$numIdUnidadeReabertura.'] não encontrada para reabertura do processo.');
						}else{
							$objInfraException->adicionarValidacao('Não é possível reabrir o processo na unidade '.$objUnidadeDTO->getStrSigla().' pois não ocorreu tramitação nesta unidade.');
						}
					}
				}
	
				$objInfraException->lancarValidacoes();
	
				$objAtividadeDTO = new AtividadeDTO();
				$objAtividadeDTO->setDistinct(true);
				$objAtividadeDTO->retNumIdUnidade();
				$objAtividadeDTO->setDblIdProtocolo($objDocumentoDTO->getDblIdProcedimento());
				$objAtividadeDTO->setNumIdUnidade($arrIdUnidadeTramitacao, InfraDTO::$OPER_IN);
				$objAtividadeDTO->setDthConclusao(null);
	
				$arrIdUnidadeAberto = InfraArray::converterArrInfraDTO($objAtividadeRN->listarRN0036($objAtividadeDTO),'IdUnidade');
	
				$objProcedimentoRN = new ProcedimentoRN();
				foreach($arrIdUnidadesReabertura as $numIdUnidadeReabertura){
					if (!in_array($numIdUnidadeReabertura, $arrIdUnidadeAberto)){
						$objReabrirProcessoDTO = new ReabrirProcessoDTO();
						$objReabrirProcessoDTO->setDblIdProcedimento($objDocumentoDTO->getDblIdProcedimento());
						$objReabrirProcessoDTO->setNumIdUnidade($numIdUnidadeReabertura);
						$objReabrirProcessoDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
						$objProcedimentoRN->reabrirRN0966($objReabrirProcessoDTO);
					}
				}
			}
	
			$objDocumentoDTORet = new DocumentoDTO();
			$objDocumentoDTORet->setDblIdDocumento($objProtocoloDTOGerado->getDblIdProtocolo());
			$objDocumentoDTORet->setStrProtocoloDocumentoFormatado($objProtocoloDTOGerado->getStrProtocoloFormatado());
	
			if (count($SEI_MODULOS)){
	
				$objDocumentoDTOIntegracao = clone($objDocumentoDTORet);
				$objDocumentoDTOIntegracao->setDblIdProcedimento($objDocumentoDTO->getDblIdProcedimento());
				$objDocumentoDTOIntegracao->setNumIdSerie($objDocumentoDTO->getNumIdSerie());
				$objDocumentoDTOIntegracao->setStrStaNivelAcessoLocalProtocolo($objDocumentoDTO->getObjProtocoloDTO()->getStrStaNivelAcessoLocal());
				$objDocumentoDTOIntegracao->setStrStaNivelAcessoGlobalProtocolo($objProtocoloDTOGerado->getStrStaNivelAcessoGlobal());
	
				foreach($SEI_MODULOS as $seiModulo){
										
					if ( is_object( $seiModulo ) && method_exists($seiModulo,'receberDocumento')) {
						$seiModulo->receberDocumento($objDocumentoDTOIntegracao);
					}
					
				}
				
			}
		
			return $objDocumentoDTORet;
	
		}catch(Exception $e){
			throw new InfraException('Erro recebendo Documento.',$e);
		}
	}
	
	public function assinar(AssinaturaDTO $objAssinaturaDTO){
	
		$arrObjAssinaturaDTO = $this->assinarInterno($objAssinaturaDTO);
	
		if ($objAssinaturaDTO->getStrStaFormaAutenticacao()==AssinaturaRN::$TA_SENHA){
	
			$objIndexacaoDTO = new IndexacaoDTO();
			$objIndexacaoDTO->setArrIdProtocolos(InfraArray::converterArrInfraDTO($objAssinaturaDTO->getArrObjDocumentoDTO(),'IdDocumento'));
			$objIndexacaoDTO->setStrStaOperacao(IndexacaoRN::$TO_PROTOCOLO_METADADOS);
	
			$objIndexacaoRN = new IndexacaoRN();
			$objIndexacaoRN->indexarProtocolo($objIndexacaoDTO);
		}
	
		return $arrObjAssinaturaDTO;
	}
	
	protected function assinarInternoControlado(AssinaturaDTO $objAssinaturaDTO) {
		try{
	
			global $SEI_MODULOS;
	
			//Valida Permissao
			$objAssinaturaDTOAuditoria = clone($objAssinaturaDTO);
			$objAssinaturaDTOAuditoria->unSetStrSenhaUsuario();
	
			SessaoSEI::getInstance()->validarAuditarPermissao('documento_assinar',__METHOD__,$objAssinaturaDTOAuditoria);
	
			//Regras de Negocio
			$objInfraException = new InfraException();
	
			$objInfraParametro = new InfraParametro(BancoSEI::getInstance());
	
			$objUsuarioDTOPesquisa = new UsuarioDTO();
			$objUsuarioDTOPesquisa->setBolExclusaoLogica(false);
			$objUsuarioDTOPesquisa->retNumIdUsuario();
			$objUsuarioDTOPesquisa->retStrSigla();
			$objUsuarioDTOPesquisa->retStrNome();
			$objUsuarioDTOPesquisa->retDblCpfContato();
			$objUsuarioDTOPesquisa->retStrStaTipo();
			$objUsuarioDTOPesquisa->retStrSenha();
			$objUsuarioDTOPesquisa->retNumIdContato();
			$objUsuarioDTOPesquisa->setNumIdUsuario($objAssinaturaDTO->getNumIdUsuario());
	
			$objUsuarioRN = new UsuarioRN();
			$objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTOPesquisa);
	
			if ($objUsuarioDTO==null){
				throw new InfraException('Assinante não cadastrado como usuário do sistema.');
			}
	
			if ($objUsuarioDTO->getStrStaTipo()==UsuarioRN::$TU_EXTERNO_PENDENTE){
				$objInfraException->lancarValidacao('Usuário externo '.$objUsuarioDTO->getStrSigla().' não foi liberado.');
			}
	
			if ($objUsuarioDTO->getStrStaTipo()!=UsuarioRN::$TU_SIP && $objUsuarioDTO->getStrStaTipo()!=UsuarioRN::$TU_EXTERNO){
				throw new InfraException('Tipo do usuário ['.$objUsuarioDTO->getStrStaTipo().'] inválido para assinatura.');
			}
	
			if ($objAssinaturaDTO->getStrStaFormaAutenticacao()==AssinaturaRN::$TA_CERTIFICADO_DIGITAL &&
					InfraString::isBolVazia($objUsuarioDTO->getDblCpfContato()) &&
					$objInfraParametro->getValor('SEI_HABILITAR_VALIDACAO_CPF_CERTIFICADO_DIGITAL')=='1'){
						$objInfraException->lancarValidacao('Assinante não possui CPF cadastrado.');
			}
	
			if (SessaoSEI::getInstance()->getNumIdUsuario()==$objAssinaturaDTO->getNumIdUsuario()){
				$objUsuarioDTOLogado = clone($objUsuarioDTO);
			}else{
				$objUsuarioDTOPesquisa->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
				$objUsuarioDTOLogado = $objUsuarioRN->consultarRN0489($objUsuarioDTOPesquisa);
			}
			
			$docDTOVerificacao = $objAssinaturaDTO->getArrObjDocumentoDTO()[0];
						
			if( $docDTOVerificacao != null 
				&& 	$docDTOVerificacao->isSetStrDescricaoTipoConferencia() 
				&& $docDTOVerificacao->getStrDescricaoTipoConferencia() == "do próprio documento nato-digital"  ){
				    
					//instanciando RN especifica, para viabilizar customizaçao da tarja de assinatura para documentos do tipo nato-digital
					$objAssinaturaRN = new MdPetAssinaturaRN();
				
			} else {
				
				    $objAssinaturaRN = new AssinaturaRN();
			}
				
			$objSecaoDocumentoRN = new SecaoDocumentoRN();
	
			$arrIdDocumentoAssinatura = array_unique(InfraArray::converterArrInfraDTO($objAssinaturaDTO->getArrObjDocumentoDTO(),'IdDocumento'));
	
			//verifica permissão de acesso ao documento
			$objPesquisaProtocoloDTO = new PesquisaProtocoloDTO();
			$objPesquisaProtocoloDTO->setStrStaTipo(ProtocoloRN::$TPP_DOCUMENTOS);
			$objPesquisaProtocoloDTO->setStrStaAcesso(ProtocoloRN::$TAP_TODOS);
			$objPesquisaProtocoloDTO->setDblIdProtocolo($arrIdDocumentoAssinatura);
	
			$objProtocoloRN = new ProtocoloRN();
			$arrObjProtocoloDTO = $objProtocoloRN->pesquisarRN0967($objPesquisaProtocoloDTO);
	
			$numDocOrigem = count($arrIdDocumentoAssinatura);
			$numDocEncontrado = count($arrObjProtocoloDTO);
			$n = $numDocOrigem - $numDocEncontrado;
	
			if ($n == 1){
				if ($numDocOrigem == 1){
					$objInfraException->lancarValidacao('Documento não encontrado para assinatura.');
				}else{
					$objInfraException->lancarValidacao('Um documento não está mais disponível para assinatura.');
				}
			}else if ($n > 1){
				$objInfraException->lancarValidacao($n.' documentos não estão mais disponíveis para assinatura.');
			}
		
			$objProtocoloDTOProcedimento = new ProtocoloDTO();
			$objProtocoloDTOProcedimento->retStrProtocoloFormatado();
			$objProtocoloDTOProcedimento->retStrStaEstado();
			$objProtocoloDTOProcedimento->setDblIdProtocolo(InfraArray::converterArrInfraDTO($arrObjProtocoloDTO,'IdProcedimentoDocumento'),InfraDTO::$OPER_IN);
	
			$objProtocoloRN = new ProtocoloRN();
			$arrObjProtocoloDTOProcedimentos = $objProtocoloRN->listarRN0668($objProtocoloDTOProcedimento);
	
			$objProcedimentoRN = new ProcedimentoRN();
			foreach($arrObjProtocoloDTOProcedimentos as $objProtocoloDTOProcedimento){
				$objProcedimentoRN->verificarEstadoProcedimento($objProtocoloDTOProcedimento);
			}
	
	
			$objAcessoExternoRN = new AcessoExternoRN();
	
			foreach($arrObjProtocoloDTO as $objProtocoloDTO){
	
				if ($objProtocoloDTO->getStrStaEstado()==ProtocoloRN::$TE_DOCUMENTO_CANCELADO){
	
					$objInfraException->adicionarValidacao('Documento '.$objProtocoloDTO->getStrProtocoloFormatado().' foi cancelado.');
	
				}else if ($objUsuarioDTOLogado->getStrStaTipo()==UsuarioRN::$TU_SIP && $objProtocoloDTO->getNumCodigoAcesso() < 0){
	
					$objInfraException->adicionarValidacao('Usuário '.$objUsuarioDTOLogado->getStrSigla().' não possui acesso ao documento '.$objProtocoloDTO->getStrProtocoloFormatado().'.');
	
					//só valida se o usuário externo estiver logado pois ele pode estar na instituição para assinar através do login de outro usuário
				}elseif ($objUsuarioDTO->getStrStaTipo()==UsuarioRN::$TU_EXTERNO && $objUsuarioDTO->getNumIdUsuario()==$objUsuarioDTOLogado->getNumIdUsuario()){
	
					$objAcessoExternoDTO = new AcessoExternoDTO();
					$objAcessoExternoDTO->retNumIdAcessoExterno();
					$objAcessoExternoDTO->setNumIdContatoParticipante($objUsuarioDTO->getNumIdContato());
					$objAcessoExternoDTO->setDblIdDocumento($objProtocoloDTO->getDblIdProtocolo());
					$objAcessoExternoDTO->setStrStaTipo(AcessoExternoRN::$TA_ASSINATURA_EXTERNA);
					$objAcessoExternoDTO->setNumMaxRegistrosRetorno(1);
	
					if ($objAcessoExternoRN->consultar($objAcessoExternoDTO) == null){
						$objInfraException->adicionarValidacao('Usuário externo '.$objUsuarioDTO->getStrSigla().' não recebeu liberação para assinar o documento '.$objProtocoloDTO->getStrProtocoloFormatado().'.');
					}
				}
	
				if ($objProtocoloDTO->getStrStaProtocolo()==ProtocoloRN::$TP_DOCUMENTO_GERADO){
					if ($objProtocoloDTO->getStrSinPublicado()=='S'){
						$objInfraException->adicionarValidacao('Documento '.$objProtocoloDTO->getStrProtocoloFormatado().' já foi publicado.');
					}
	
					if ($objProtocoloDTO->getStrStaDocumentoDocumento()==DocumentoRN::$TD_FORMULARIO_AUTOMATICO){
						$objInfraException->adicionarValidacao('Formulário '.$objProtocoloDTO->getStrProtocoloFormatado().' não pode receber assinatura.');
					}
	
					if ($objProtocoloDTO->getStrStaDocumentoDocumento()==DocumentoRN::$TD_EDITOR_EDOC){
						$objInfraException->adicionarValidacao('Não é possível assinar documentos gerados pelo e-Doc ('.$objProtocoloDTO->getStrProtocoloFormatado().').');
					}
				}
	
				$dto = new AssinaturaDTO();
				$dto->retStrNomeUsuario();
				$dto->setDblIdDocumento($objProtocoloDTO->getDblIdProtocolo());
				$dto->setNumIdUsuario($objAssinaturaDTO->getNumIdUsuario());
				$dto = $objAssinaturaRN->consultarRN1322($dto);
	
				if ($dto != null){
					$objInfraException->adicionarValidacao('Documento '.$objProtocoloDTO->getStrProtocoloFormatado().' já foi assinado por "'.$dto->getStrNomeUsuario().'".');
				}
	
				if ($objProtocoloDTO->getStrStaDocumentoDocumento()==DocumentoRN::$TD_EDITOR_INTERNO) {
					$objSecaoDocumentoDTO = new SecaoDocumentoDTO();
					$objSecaoDocumentoDTO->retNumIdSecaoDocumento();
					$objSecaoDocumentoDTO->setDblIdDocumento($objProtocoloDTO->getDblIdProtocolo());
					$objSecaoDocumentoDTO->setStrSinAssinatura('S');
					$objSecaoDocumentoDTO->setNumMaxRegistrosRetorno(1);
	
					if ($objSecaoDocumentoRN->consultar($objSecaoDocumentoDTO) == null) {
						$objInfraException->adicionarValidacao('Documento ' . $objProtocoloDTO->getStrProtocoloFormatado() . ' não contém seção de assinatura.');
					}
				}
	
				if ($objProtocoloDTO->getStrStaProtocolo()==ProtocoloRN::$TP_DOCUMENTO_RECEBIDO && $objProtocoloDTO->getNumIdTipoConferenciaDocumento()==null){
					$objInfraException->adicionarValidacao('Documento ' . $objProtocoloDTO->getStrProtocoloFormatado() . ' não possui Tipo de Conferência informada.');
				}
			}
	
			$objInfraException->lancarValidacoes();
	
			$objInfraException->lancarValidacoes();
	
			if ($objAssinaturaDTO->getStrStaFormaAutenticacao()==AssinaturaRN::$TA_SENHA){
	
				if ($objUsuarioDTO->getStrStaTipo()==UsuarioRN::$TU_SIP){
	
					$objInfraSip = new InfraSip(SessaoSEI::getInstance());
					$objInfraSip->autenticar($objAssinaturaDTO->getNumIdOrgaoUsuario(),
							$objAssinaturaDTO->getNumIdContextoUsuario(),
							$objUsuarioDTO->getStrSigla(),
							$objAssinaturaDTO->getStrSenhaUsuario());
	
				}else{
	
					$bcrypt = new InfraBcrypt();
					if (!$bcrypt->verificar(md5($objAssinaturaDTO->getStrSenhaUsuario()), $objUsuarioDTO->getStrSenha())) {
						$objInfraException->lancarValidacao('Senha inválida.');
					}
	
				}
			}
	
			foreach($arrObjProtocoloDTO as $objProtocoloDTO){
				
				if ($objProtocoloDTO->getStrStaProtocolo()==ProtocoloRN::$TP_DOCUMENTO_GERADO){
				
					if ($objProtocoloDTO->getStrSinAssinado()=='N'){
	
						if ($objProtocoloDTO->getStrStaDocumentoDocumento()==DocumentoRN::$TD_EDITOR_INTERNO) {
		
							/////////
							$objEditorDTO = new EditorDTO();
							$objEditorDTO->setDblIdDocumento($objProtocoloDTO->getDblIdProtocolo());
							$objEditorDTO->setNumIdBaseConhecimento(null);
							$objEditorDTO->setStrSinCabecalho('S');
							$objEditorDTO->setStrSinRodape('S');
							$objEditorDTO->setStrSinIdentificacaoVersao('N');
							$objEditorDTO->setStrSinCarimboPublicacao('S');
	
							$objEditorRN = new EditorRN();
							$strDocumentoHTML = $objEditorRN->consultarHtmlVersao($objEditorDTO);
	
						}else if ($objProtocoloDTO->getStrStaDocumentoDocumento()==DocumentoRN::$TD_FORMULARIO_GERADO){
	
							$dto = new DocumentoDTO();
							$dto->setDblIdDocumento($objProtocoloDTO->getDblIdProtocolo());
							$strDocumentoHTML = $this->consultarHtmlFormulario($dto);
	
						}
	
						$objDocumentoConteudoDTO = new DocumentoConteudoDTO();
						$objDocumentoConteudoDTO->setStrConteudoAssinatura($strDocumentoHTML);
						$objDocumentoConteudoDTO->setStrCrcAssinatura(strtoupper(hash('crc32b', $strDocumentoHTML)));
						$this->gerarQrCode($objProtocoloDTO, $objDocumentoConteudoDTO);
						$objDocumentoConteudoDTO->setDblIdDocumento($objProtocoloDTO->getDblIdProtocolo());
	
						$objDocumentoConteudoBD = new DocumentoConteudoBD($this->getObjInfraIBanco());
						$objDocumentoConteudoBD->alterar($objDocumentoConteudoDTO);
	
					}
	
				}
				
				else{
	
					$objAnexoDTO = new AnexoDTO();
					$objAnexoDTO->retNumIdAnexo();
					$objAnexoDTO->retDthInclusao();
					$objAnexoDTO->setDblIdProtocolo($objProtocoloDTO->getDblIdProtocolo());
	
					$objAnexoRN = new AnexoRN();
					$objAnexoDTO = $objAnexoRN->consultarRN0736($objAnexoDTO);
	
					if ($objAnexoDTO==null){
						$objInfraException->lancarValidacao('Documento '.$objProtocoloDTO->getStrProtocoloFormatado().' não possui anexo associado.');
					}
	
					$objDocumentoConteudoBD = new DocumentoConteudoBD($this->getObjInfraIBanco());
	
					$objDocumentoConteudoDTO = new DocumentoConteudoDTO();
					$objDocumentoConteudoDTO->setDblIdDocumento($objProtocoloDTO->getDblIdProtocolo());
	
					if ($objDocumentoConteudoBD->contar($objDocumentoConteudoDTO) == 0){
						$objDocumentoConteudoDTO->setStrConteudo(null);
						$objDocumentoConteudoDTO->setStrConteudoAssinatura(null);
						$objDocumentoConteudoDTO->setStrCrcAssinatura(strtoupper(hash_file('crc32b', $objAnexoRN->obterLocalizacao($objAnexoDTO))));
						$this->gerarQrCode($objProtocoloDTO, $objDocumentoConteudoDTO);
						$objDocumentoConteudoBD->cadastrar($objDocumentoConteudoDTO);
					}else{
						$objDocumentoConteudoDTO->setStrCrcAssinatura(strtoupper(hash_file('crc32b', $objAnexoRN->obterLocalizacao($objAnexoDTO))));
						$this->gerarQrCode($objProtocoloDTO, $objDocumentoConteudoDTO);
						$objDocumentoConteudoBD->alterar($objDocumentoConteudoDTO);
					}
				}
			}
	
			$objTarjaAssinaturaDTO = new TarjaAssinaturaDTO();
			$objTarjaAssinaturaDTO->retNumIdTarjaAssinatura();

            if ($objUsuarioDTO->getStrStaTipo() == UsuarioRN::$TU_EXTERNO) {
                $objTarjaAssinaturaDTO->setStrStaTarjaAssinatura( MdPetAssinaturaRN::$TT_ASSINATURA_SENHA_PETICIONAMENTO );
            } else {
                if ($objProtocoloDTO->getStrStaProtocolo()==ProtocoloRN::$TP_DOCUMENTO_GERADO) {
                    if ($objAssinaturaDTO->getStrStaFormaAutenticacao() == AssinaturaRN::$TA_SENHA) {
                        $objTarjaAssinaturaDTO->setStrStaTarjaAssinatura(TarjaAssinaturaRN::$TT_ASSINATURA_SENHA);
                        // $objTarjaAssinaturaDTO->setStrStaTarjaAssinatura(TarjaAssinaturaRN::$TT_AUTENTICACAO_SENHA);
                    } else {
                        $objTarjaAssinaturaDTO->setStrStaTarjaAssinatura(TarjaAssinaturaRN::$TT_ASSINATURA_CERTIFICADO_DIGITAL);
                    }
                }else{

                    //CUSTOMIZACAO DA TARJA DE ASSINATURA PARA DOC ANEXO NATO-DIGITAL
                    $arrDocs = $objAssinaturaDTO->getArrObjDocumentoDTO();

                    if( is_array( $arrDocs ) && count( $arrDocs ) > 0 ) {

                        $documentoDTOParaAssinar = $arrDocs[0];

                        if( $documentoDTOParaAssinar->isSetStrDescricaoTipoConferencia()
                            && $documentoDTOParaAssinar->getStrDescricaoTipoConferencia() == "do próprio documento nato-digital" ){

                            //vai usar tarja customizada
                            $objTarjaAssinaturaDTO->setStrStaTarjaAssinatura( MdPetAssinaturaRN::$TT_ASSINATURA_SENHA_PETICIONAMENTO );

                        } else {

                            //vai usar a tarja normal
                            $objTarjaAssinaturaDTO->setStrStaTarjaAssinatura(TarjaAssinaturaRN::$TT_AUTENTICACAO_SENHA);

                        }
                    }

                }
            }
	

				
			$objTarjaAssinaturaRN = new TarjaAssinaturaRN();
			$objTarjaAssinaturaDTO = $objTarjaAssinaturaRN->consultar($objTarjaAssinaturaDTO);
//ini_set('xdebug.var_display_max_depth', 10); ini_set('xdebug.var_display_max_children', 256); ini_set('xdebug.var_display_max_data', 1024); echo '<pre>';
//var_dump($objTarjaAssinaturaDTO); echo '</pre>'; exit;
			$objAtividadeRN = new AtividadeRN();
			$arrObjAssinaturaDTO = array();
			$arrObjDocumentoDTOCredencialAssinatura = array();
			foreach($arrObjProtocoloDTO as $objProtocoloDTO){
	
				$numIdAtividade = null;
				if ($objAssinaturaDTO->getStrStaFormaAutenticacao()==AssinaturaRN::$TA_SENHA){
	
					//lança tarefa de assinatura
					$arrObjAtributoAndamentoDTO = array();
					$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
					$objAtributoAndamentoDTO->setStrNome('DOCUMENTO');
					$objAtributoAndamentoDTO->setStrValor($objProtocoloDTO->getStrProtocoloFormatado());
					$objAtributoAndamentoDTO->setStrIdOrigem($objProtocoloDTO->getDblIdProtocolo());
					$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;
	
					$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
					$objAtributoAndamentoDTO->setStrNome('USUARIO');
					$objAtributoAndamentoDTO->setStrValor($objUsuarioDTO->getStrSigla().'¥'.$objUsuarioDTO->getStrNome());
					$objAtributoAndamentoDTO->setStrIdOrigem($objUsuarioDTO->getNumIdUsuario());
					$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;
	
					//Define se o propósito da operação é assinar ou autenticar o documento
					$numIdTarefaAssinatura = TarefaRN::$TI_ASSINATURA_DOCUMENTO;
					if($objProtocoloDTO->getStrStaProtocolo() == ProtocoloRN::$TP_DOCUMENTO_RECEBIDO) {
						$numIdTarefaAssinatura = TarefaRN::$TI_AUTENTICACAO_DOCUMENTO;
					}
	
					$objAtividadeDTO = new AtividadeDTO();
					$objAtividadeDTO->setDblIdProtocolo($objProtocoloDTO->getDblIdProcedimentoDocumento());
					$objAtividadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
					$objAtividadeDTO->setNumIdTarefa($numIdTarefaAssinatura);
					$objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO);
	
					$objAtividadeDTO = $objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);
					$numIdAtividade = $objAtividadeDTO->getNumIdAtividade();
				}
	
				//remove ocorrência pendente, se existir
				$dto = new AssinaturaDTO();
				$dto->retNumIdAssinatura();
				$dto->setDblIdDocumento($objProtocoloDTO->getDblIdProtocolo());
				$dto->setNumIdUsuario($objAssinaturaDTO->getNumIdUsuario());
				$dto->setBolExclusaoLogica(false);
				$dto->setStrSinAtivo('N');
				$dto = $objAssinaturaRN->consultarRN1322($dto);
	
				if ($dto!=null){
					$objAssinaturaRN->excluirRN1321(array($dto));
				}
	
				$dto = new AssinaturaDTO();
				$dto->setDblIdDocumento($objProtocoloDTO->getDblIdProtocolo());
				$dto->setStrProtocoloDocumentoFormatado($objProtocoloDTO->getStrProtocoloFormatado());
				$dto->setNumIdUsuario($objAssinaturaDTO->getNumIdUsuario());
				$dto->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
				$dto->setNumIdAtividade($numIdAtividade);
				$dto->setNumIdTarjaAssinatura($objTarjaAssinaturaDTO->getNumIdTarjaAssinatura());
				$dto->setStrSiglaUsuario($objUsuarioDTO->getStrSigla());
				$dto->setStrNome($objUsuarioDTO->getStrNome());
				$dto->setStrTratamento($objAssinaturaDTO->getStrCargoFuncao());
				$dto->setDblCpf($objUsuarioDTO->getDblCpfContato());
				$dto->setStrStaFormaAutenticacao($objAssinaturaDTO->getStrStaFormaAutenticacao());
				$dto->setStrP7sBase64(null);
	
				if ($objAssinaturaDTO->getStrStaFormaAutenticacao()==AssinaturaRN::$TA_CERTIFICADO_DIGITAL){
					$dto->setStrSinAtivo('N');
				}else{
					$dto->setStrSinAtivo('S');
				}
	
				$arrObjAssinaturaDTO[] = $objAssinaturaRN->cadastrarRN1319($dto);
	
				if ($objAssinaturaDTO->getStrStaFormaAutenticacao()==AssinaturaRN::$TA_SENHA && $objProtocoloDTO->getStrSinCredencialAssinatura()=='S'){
					$objDocumentoDTO = new DocumentoDTO();
					$objDocumentoDTO->setDblIdDocumento($objProtocoloDTO->getDblIdProtocolo());
					$arrObjDocumentoDTOCredencialAssinatura[] = $objDocumentoDTO;
				}
			}
	
			if (count($arrObjDocumentoDTOCredencialAssinatura)){
				$objAtividadeRN->concluirCredencialAssinatura($arrObjDocumentoDTOCredencialAssinatura);
			}
	
			if (count($SEI_MODULOS) && $objAssinaturaDTO->getStrStaFormaAutenticacao()==AssinaturaRN::$TA_SENHA){
				$arrObjDocumentoAPI = array();
				foreach($arrObjProtocoloDTO as $objProtocoloDTO){
					$objDocumentoAPI = new DocumentoAPI();
					$objDocumentoAPI->setIdDocumento($objProtocoloDTO->getDblIdProtocolo());
					$objDocumentoAPI->setNumeroProtocolo($objProtocoloDTO->getStrProtocoloFormatado());
					$objDocumentoAPI->setIdSerie($objProtocoloDTO->getNumIdSerieDocumento());
					$objDocumentoAPI->setIdUnidadeGeradora($objProtocoloDTO->getNumIdUnidadeGeradora());
					$objDocumentoAPI->setTipo($objProtocoloDTO->getStrStaProtocolo());
					$objDocumentoAPI->setSubTipo($objProtocoloDTO->getStrStaDocumentoDocumento());
					$objDocumentoAPI->setNivelAcesso($objProtocoloDTO->getStrStaNivelAcessoGlobal());
					$arrObjDocumentoAPI[] = $objDocumentoAPI;
				}
	
				foreach($SEI_MODULOS as $seiModulo){
					$seiModulo->executar('assinarDocumento', $arrObjDocumentoAPI);
				}
			}
	
	
			return $arrObjAssinaturaDTO;
	
		}catch(Exception $e){
			throw new InfraException('Erro assinando documento.',$e);
		}
	}
	
	private function gerarQrCode(ProtocoloDTO $objProtocoloDTO, DocumentoConteudoDTO $objDocumentoConteudoDTO){
		try{
	
			$objAnexoRN = new AnexoRN();
			$strArquivoQRCaminhoCompleto = DIR_SEI_TEMP.'/'.$objAnexoRN->gerarNomeArquivoTemporario();
			$strUrlVerificacao = ConfiguracaoSEI::getInstance()->getValor('SEI','URL').'/controlador_externo.php?acao=documento_conferir&id_orgao_acesso_externo='.$objProtocoloDTO->getNumIdOrgaoUnidadeGeradora().'&cv='.$objProtocoloDTO->getStrProtocoloFormatado().'&crc='.$objDocumentoConteudoDTO->getStrCrcAssinatura();
	
			InfraQRCode::gerar($strUrlVerificacao, $strArquivoQRCaminhoCompleto,'L',2,1);
	
			$objInfraException = new InfraException();
	
	
			if (!file_exists($strArquivoQRCaminhoCompleto)){
				$objInfraException->lancarValidacao('Arquivo do QRCode não encontrado.');
			}
	
			if (filesize($strArquivoQRCaminhoCompleto)==0){
				$objInfraException->lancarValidacao('Arquivo do QRCode vazio.');
			}
	
			if (($binQrCode = file_get_contents($strArquivoQRCaminhoCompleto))===false){
				$objInfraException->lancarValidacao('Não foi possível ler o arquivo do QRCode.');
			}
	
			$objDocumentoConteudoDTO->setStrQrCodeAssinatura(base64_encode($binQrCode));
	
			unlink($strArquivoQRCaminhoCompleto);
	
		}catch(Exception $e){
			throw new InfraException('Erro gerando QRCode da assinatura.',$e);
		}
	}
	
}
?>