<?
/**
* ANATEL
*
* 28/06/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class ProcessoPeticionamentoRN extends InfraRN { 
	
	public static $INDICACAO_DIRETA = 'I';
	public static $DOC_GERADO = 'G';
	public static $DOC_EXTERNO = 'E';	
	
	public function __construct() {
		
		session_start();
		
		//////////////////////////////////////////////////////////////////////////////
		InfraDebug::getInstance()->setBolLigado(false);
		InfraDebug::getInstance()->setBolDebugInfra(false);
		InfraDebug::getInstance()->limpar();
		//////////////////////////////////////////////////////////////////////////////
		
		parent::__construct ();
	}
	
	protected function inicializarObjInfraIBanco() {
		return BancoSEI::getInstance ();
	}
	
	protected function validarSenhaConectado( $arrParametros ) {
		
		$objInfraException = new InfraException();
				
		//validar a senha no SEI
		//seiv2
		//$objUsuarioDTO = new UsuarioDTO();
		//$objUsuarioDTO->retStrSigla();
		//$objUsuarioDTO->retStrSenha();
		//$objUsuarioDTO->setStrSigla(SessaoSEIExterna::getInstance()->getStrSiglaUsuarioExterno());
		
		//alteracoes SEIv3
		$objUsuarioDTO = new UsuarioDTO();
		$objUsuarioDTO->retNumIdUsuario();
		$objUsuarioDTO->retStrSigla();
		//$objUsuarioDTO->retStrNome();
		//$objUsuarioDTO->retNumIdOrgao();
		//$objUsuarioDTO->retStrSiglaOrgao();
		//$objUsuarioDTO->retStrDescricaoOrgao();
		//$objUsuarioDTO->retStrStaTipo();
		$objUsuarioDTO->retStrSenha();
		$objUsuarioDTO->setStrSigla( SessaoSEIExterna::getInstance()->getStrSiglaUsuarioExterno() );
		//$objUsuarioDTO->setStrStaTipo(array(UsuarioRN::$TU_EXTERNO, UsuarioRN::$TU_EXTERNO_PENDENTE), InfraDTO::$OPER_IN);
		
		$objUsuarioRN = new UsuarioRN();
		$objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);
		$senhaBanco=$objUsuarioDTO->getStrSenha();
		$bcrypt = new InfraBcrypt();
		
		$senhaInformada=md5( $arrParametros['senhaSEI'] );
		//echo $arrParametros['senhaSEI']; die;
		
		if (!$bcrypt->verificar($senhaInformada,$senhaBanco)) {
			$objInfraException->adicionarValidacao("Senha inválida.");
			$objInfraException->lancarValidacoes();
		} 
		
		//seiv2
		//$objUsuarioDTO->setStrSenha(md5( $arrParametros['senhaSEI'] ) );
		/*
		$arrListaUsuario = $objUsuarioRN->listarRN0490( $objUsuarioDTO );
		$totalUsuarioValido = count( $arrListaUsuario );
		
		//ASSINATURA VALIDA
		if( $totalUsuarioValido == 0 ){
			$objInfraException->adicionarValidacao("Senha inválida.");
			$objInfraException->lancarValidacoes();
		}
		*/
		
	}	

	protected function gerarProcedimentoControlado( $arrParametros ){
		try {

			$contatoDTOUsuarioLogado = $this->getContatoDTOUsuarioLogado();
			
			//Remententes 
			$idsRemententes = array();
			$idsRemententes[] = $contatoDTOUsuarioLogado->getNumIdContato();
			
			$idTipoProc = $arrParametros['id_tipo_procedimento'];
			$objTipoProcDTO = new TipoProcessoPeticionamentoDTO();
			$objTipoProcDTO->retTodos(true);
			$objTipoProcDTO->setNumIdTipoProcessoPeticionamento( $idTipoProc );
			$objTipoProcRN = new TipoProcessoPeticionamentoRN();
			$objTipoProcDTO = $objTipoProcRN->consultar( $objTipoProcDTO );
			$txtTipoProcessoEscolhido = $objTipoProcDTO->getStrNomeProcesso();
			
			//=============================================================================================================
			//obtendo a unidade do tipo de processo selecionado - Pac 10 - pode ser uma ou MULTIPLAS unidades selecionadas
			//=============================================================================================================
			$relTipoProcUnidadeDTO = new RelTipoProcessoUnidadePeticionamentoDTO();
			$relTipoProcUnidadeDTO->retTodos();
			$relTipoProcUnidadeRN = new RelTipoProcessoUnidadePeticionamentoRN();
			$relTipoProcUnidadeDTO->setNumIdTipoProcessoPeticionamento( $idTipoProc );
			$arrRelTipoProcUnidadeDTO = $relTipoProcUnidadeRN->listar( $relTipoProcUnidadeDTO );
			
			$arrUnidadeUFDTO = null;
			$idUnidadeTipoProcesso = null;
			
			//=====================================================
			//TIPO DE PROCESSADO CONFIGURADO COM APENAS UMA UNIDADE
			//=====================================================
			if( $arrRelTipoProcUnidadeDTO != null && count( $arrRelTipoProcUnidadeDTO ) == 1 ) {
				$idUnidade = $arrRelTipoProcUnidadeDTO[0]->getNumIdUnidade();
			}
			
			//========================================================================================================================
			//TIPO DE PROCESSO CONFIGURADO COM MULTIPLAS UNIDADES -> pegar a unidade a partir da UF selecionada pelo usuario na combo
			//========================================================================================================================
			else if( $arrRelTipoProcUnidadeDTO != null && count( $arrRelTipoProcUnidadeDTO ) > 1 ){		
				$idUnidade = $arrParametros['hdnIdUnidadeMultiplaSelecionada'];
			}
						
			//obter unidade configurada no "Tipo de Processo para peticionamento"
			$unidadeRN = new UnidadeRN();
			$unidadeDTO = new UnidadeDTO();
			$unidadeDTO->retTodos();
			$unidadeDTO->setNumIdUnidade( $idUnidade );
			$unidadeDTO = $unidadeRN->consultarRN0125( $unidadeDTO );				
			
			$protocoloRN = new ProtocoloPeticionamentoRN();

			//seiv2
			//$arrParticipantesParametro = array();
			//$arrObjInteressadoAPI = array();

			if( $objTipoProcDTO->getStrSinIIProprioUsuarioExterno() == 'S' ){

				$arrParametros['hdnListaInteressados'] = $contatoDTOUsuarioLogado->getNumIdContato();

				// Interessados
				$idsContatos = array();
				$idsContatos[] = $arrParametros['hdnListaInteressados'];
				$idsContatos = array_merge($idsRemententes, $idsContatos);

				//seiv3 refatoraçao
				//$arrParticipantesParametro = $this->atribuirParticipantes( $this->montarArrContatosInteressados( $idsContatos ) );

			}

			//verificar se esta vindo o array de participantes
			//participantes selecionados via pop up OU indicados diretamente por CPF/CNPJ
			else if( $objTipoProcDTO->getStrSinIIProprioUsuarioExterno() == 'N' && 
				isset( $arrParametros['hdnListaInteressados'] ) && 
				$arrParametros['hdnListaInteressados'] != "" ){			
				
				$arrContatosInteressados = array();
				
				if (strpos( $arrParametros['hdnListaInteressados'] , ',') !== false) {
					// Interessados
					$idsContatos = split(",", $arrParametros['hdnListaInteressados']);
				} else {
					// Interessados				
					$idsContatos = array();
					$idsContatos[] = $arrParametros['hdnListaInteressados'];
				}

				//seiv3 refatoraçao
				//$arrParticipantesParametro = $this->atribuirParticipantes( $this->montarArrContatosInteressados( $idsContatos ) );

			} 

			$idsContatos = array_unique($idsContatos);

			//Gera um processo
			$objProcedimentoAPI = new ProcedimentoAPI();
			$objProcedimentoAPI->setIdTipoProcedimento( $objTipoProcDTO->getNumIdProcedimento() );
			$objProcedimentoAPI->setNivelAcesso( ProtocoloRN::$NA_PUBLICO );
			$objProcedimentoAPI->setIdUnidadeGeradora( $unidadeDTO->getNumIdUnidade() );
			$objProcedimentoAPI->setEspecificacao( $arrParametros['txtEspecificacaoDocPrincipal'] );
			$objProcedimentoAPI->setNumeroProtocolo('');

			//seiv2
			//$objProcedimentoAPI->setInteressados( $arrParticipantesParametro );
			
			$objEntradaGerarProcedimentoAPI = new EntradaGerarProcedimentoAPI();
			$objEntradaGerarProcedimentoAPI->setProcedimento($objProcedimentoAPI);
				
			$objSeiRN = new SeiRN();
			SessaoSEI::getInstance()->simularLogin(null, null , SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() , $unidadeDTO->getNumIdUnidade() );
			
			//$objSaidaGerarProcedimentoAPI = new SaidaGerarProcedimentoAPI();
			$objSaidaGerarProcedimentoAPI = $objSeiRN->gerarProcedimento($objEntradaGerarProcedimentoAPI);

			//seiv3
			//Remetentes
			$idsParticipantes = array();

			// Processo - Interessados
			$i=0;
			foreach($idsContatos as $interessado){
				$objParticipante  = new ParticipanteDTO();
				$objParticipante->setDblIdProtocolo($objSaidaGerarProcedimentoAPI->getIdProcedimento());
				$objParticipante->setNumIdContato($interessado);
				$objParticipante->setNumIdUnidade($unidadeDTO->getNumIdUnidade());
				$objParticipante->setStrStaParticipacao(ParticipanteRN::$TP_INTERESSADO);
				$objParticipante->setNumSequencia($i);
				$idsParticipantes[] = $objParticipante;
				$i++;
			}

			$objMdPetParticipanteRN = new MdPetParticipanteRN();
			$arrInteressado = array();
			$arrInteressado[0] = $objSaidaGerarProcedimentoAPI->getIdProcedimento();
			$arrInteressado[1] = $idsParticipantes;

			$objMdPetParticipanteRN->setInteressadosRemetentesProcedimentoDocumento( $arrInteressado );
			// Processo - Interessados - FIM

			//gerando recibo e adicionando recibo NAO ASSINADO ao processo
			$reciboPeticionamentoRN = new ReciboPeticionamentoRN();
			$reciboDTOBasico = $reciboPeticionamentoRN->gerarReciboSimplificado( $objSaidaGerarProcedimentoAPI->getIdProcedimento() );
							
			$objEntradaConsultarProcedimentoAPI = new EntradaConsultarProcedimentoAPI();
			$objEntradaConsultarProcedimentoAPI->setIdProcedimento( $objSaidaGerarProcedimentoAPI->getIdProcedimento() );
			$objSaidaConsultarProcedimentoAPI = $objSeiRN->consultarProcedimento( $objEntradaConsultarProcedimentoAPI );
			
			$nomeTipo = $objSaidaConsultarProcedimentoAPI->getTipoProcedimento()->getNome();
			
			$objProcedimentoDTO = new ProcedimentoDTO();
			$objProcedimentoDTO->setStrNomeTipoProcedimento( $nomeTipo );
			$objProcedimentoDTO->setDblIdProcedimento( $objSaidaGerarProcedimentoAPI->getIdProcedimento() );
			$objProcedimentoDTO->setStrProtocoloProcedimentoFormatado( $objSaidaConsultarProcedimentoAPI->getProcedimentoFormatado()  );
			$objProcedimentoDTO->setNumIdTipoProcedimento( $objSaidaConsultarProcedimentoAPI->getTipoProcedimento()->getIdTipoProcedimento()  );

			//seiv2
			//$this->montarArrDocumentos( $arrParametros, $unidadeDTO, $objProcedimentoDTO, 
			//		                    $arrParticipantesParametro, $reciboDTOBasico );
			//seiv3
			$this->montarArrDocumentos( $arrParametros, $unidadeDTO, $objProcedimentoDTO, $reciboDTOBasico );

			$arrParams = array();
			$arrParams[0] = $arrParametros;
			$arrParams[1] = $unidadeDTO;
			$arrParams[2] = $objProcedimentoDTO;
			//seiv2
			//$arrParams[3] = $arrParticipantesParametro;
			$arrParams[4] = $reciboDTOBasico;

			$reciboGerado = $reciboPeticionamentoRN->montarRecibo( $arrParams );
			
			$arrProcessoReciboRetorno = array();
			$arrProcessoReciboRetorno[0] = $reciboDTOBasico;
			$arrProcessoReciboRetorno[1] = $objProcedimentoDTO;

			//enviando email de sistema EU 5155  / 5156 - try catch por causa que em localhost o envio de email gera erro
			try {
			  $emailNotificacaoPeticionamentoRN = new EmailNotificacaoPeticionamentoRN();
			  $emailNotificacaoPeticionamentoRN->notificaoPeticionamentoExterno( $arrParams );
			} catch( Exception $exEmail ){}
			
			//obter todos os documentos deste processo
			$documentoRN = new DocumentoRN();
			$documentoListaDTO = new DocumentoDTO();
			$documentoListaDTO->retDblIdDocumento();
			$documentoListaDTO->setDblIdProcedimento( $objProcedimentoDTO->getDblIdProcedimento() );
			$arrDocsProcesso = $documentoRN->listarRN0008(  $documentoListaDTO );
			
			$atividadeRN = new AtividadeRN();
			$atividadeBD = new AtividadeBD( $this->getObjInfraIBanco() );
			
			//removendo as tarefas do tipo "Disponibilizado acesso externo para @INTERESSADO@"
			foreach( $arrDocsProcesso as $DocumentoProcessoDTO ){

				//seiv3
				//Remetentes
				$idsParticipantes = array();

				$objParticipante  = new ParticipanteDTO();
				$objParticipante->setDblIdProtocolo($DocumentoProcessoDTO->getDblIdDocumento());
				$objParticipante->setNumIdContato($this->getContatoDTOUsuarioLogado()->getNumIdContato());
				$objParticipante->setNumIdUnidade($unidadeDTO->getNumIdUnidade());
				$objParticipante->setStrStaParticipacao(ParticipanteRN::$TP_REMETENTE);
				$objParticipante->setNumSequencia(0);
				$idsParticipantes[] = $objParticipante;

				// Documento - Interessados
				$i=0;
				//foreach($arrobjParticipanteProcPrinc as $objParticipanteProcPrinc){
				foreach($idsContatos as $interessado){
					$objParticipante  = new ParticipanteDTO();
					$objParticipante->setDblIdProtocolo($DocumentoProcessoDTO->getDblIdDocumento());
					$objParticipante->setNumIdContato($interessado);
					$objParticipante->setNumIdUnidade($unidadeDTO->getNumIdUnidade());
					$objParticipante->setStrStaParticipacao(ParticipanteRN::$TP_INTERESSADO);
					$objParticipante->setNumSequencia($i);
					$idsParticipantes[] = $objParticipante;
					$i++;
				}

				$objMdPetParticipanteRN = new MdPetParticipanteRN();
				$arrInteressado = array();
				$arrInteressado[0] = $DocumentoProcessoDTO->getDblIdDocumento();
				$arrInteressado[1] = $idsParticipantes;
				
				$objMdPetParticipanteRN->setInteressadosRemetentesProcedimentoDocumento( $arrInteressado );
				// Documento - Interessados - FIM

				$objAtividadeDTOLiberacao = new AtividadeDTO();
				$objAtividadeDTOLiberacao->retTodos();
				$objAtividadeDTOLiberacao->setDblIdProtocolo( $objProcedimentoDTO->getDblIdProcedimento() );
				$objAtividadeDTOLiberacao->setNumIdTarefa(TarefaRN::$TI_ACESSO_EXTERNO_SISTEMA);
				
				$arrDTOAtividades = $atividadeRN->listarRN0036( $objAtividadeDTOLiberacao );
				$atividadeRN->excluirRN0034( $arrDTOAtividades );
				
			}
						
			// obtendo a ultima atividade informada para o processo, para marcar 
			// como nao visualizada, deixando assim o processo marcado como "vermelho" 
			// (status de Nao Visualizado) na listagem da tela "Controle de processos"
			$atividadeDTO = new AtividadeDTO();
			$atividadeDTO->retTodos();
			$atividadeDTO->setDblIdProtocolo( $objProcedimentoDTO->getDblIdProcedimento() );
			$atividadeDTO->setOrd("IdAtividade", InfraDTO::$TIPO_ORDENACAO_DESC);
			$ultimaAtividadeDTO = $atividadeRN->listarRN0036( $atividadeDTO );
						
			//alterar a ultima atividade criada para nao visualizado
			if( $ultimaAtividadeDTO != null && count( $ultimaAtividadeDTO ) > 0){
			  $ultimaAtividadeDTO[0]->setNumTipoVisualizacao( AtividadeRN::$TV_NAO_VISUALIZADO );
			  $atividadeBD->alterar( $ultimaAtividadeDTO[0] );
			}
			
			return $arrProcessoReciboRetorno;
		
		} catch(Exception $e){
			//print_r( $e->getTraceAsString() ); die; 
			throw new InfraException('Erro cadastrando processo peticionamento do SEI.',$e);
		}
		
	}
	
	/*
	 * Método responsavel por incluir documento externo (ANEXO) no processo, travar documento para ediçao e assinar o documento
	 * customizando sua tarja de assinatura. As operaçoes já fazem uso da classe SeiRN e classes de API do SEI 3.0
	 * */
	private function gerarAssinarDocumentoAnexoSeiRN( $objUnidadeDTO , $arrParametros, $docDTO, $objProcedimentoDTO, $itemAnexo, $reciboDTOBasico, $tipoDocRecibo ){
				
		$objDocumentoAPI = new DocumentoAPI();
		$objDocumentoAPI->setIdProcedimento( $objProcedimentoDTO->getDblIdProcedimento() );
		$objDocumentoAPI->setTipo( ProtocoloRN::$TP_DOCUMENTO_RECEBIDO );
		$objDocumentoAPI->setIdSerie( $docDTO->getNumIdSerie() );
		$objDocumentoAPI->setData( InfraData::getStrDataAtual() );
		$objDocumentoAPI->setSinAssinado('S');
		$objDocumentoAPI->setSinBloqueado('S');
		$objDocumentoAPI->setIdHipoteseLegal( $docDTO->getNumIdHipoteseLegalProtocolo() );
		$objDocumentoAPI->setNivelAcesso( $docDTO->getStrStaNivelAcessoLocalProtocolo() );
		$objDocumentoAPI->setIdTipoConferencia( $docDTO->getNumIdTipoConferencia() );
			
		$objDocumentoAPI->setNomeArquivo( $itemAnexo->getStrNome() );
		$objDocumentoAPI->setConteudo(base64_encode(file_get_contents(DIR_SEI_TEMP. '/'. $itemAnexo->getStrHash() )));

		$objSeiRN = new SeiRN();
		$saidaDocExternoAPI = $objSeiRN->incluirDocumento( $objDocumentoAPI );
		$idDocumentoAnexo = $saidaDocExternoAPI->getIdDocumento();
		$docDTO->setDblIdDocumento( $idDocumentoAnexo );
		$this->assinarETravarDocumento( $objUnidadeDTO, $arrParametros, $docDTO, $objProcedimentoDTO );
		
		//adiciona o doc no recibo pesquisavel
		//recibo do doc principal para consultar do usuario externo
		$reciboDocAnexoDTO = new ReciboDocumentoAnexoPeticionamentoDTO();
		$reciboDocAnexoRN = new ReciboDocumentoAnexoPeticionamentoRN();
			
		$reciboDocAnexoDTO->setNumIdAnexo( null );
		$reciboDocAnexoDTO->setNumIdReciboPeticionamento( $reciboDTOBasico->getNumIdReciboPeticionamento() );
		$reciboDocAnexoDTO->setNumIdDocumento( $idDocumentoAnexo );
		$reciboDocAnexoDTO->setStrClassificacaoDocumento( $tipoDocRecibo );
		$reciboDocAnexoDTO = $reciboDocAnexoRN->cadastrar( $reciboDocAnexoDTO );
		
		return $saidaDocExternoAPI;
		
	}

	private function montarArrDocumentos( $arrParametros , $objUnidadeDTO , 
			                              $objProcedimentoDTO , $reciboDTOBasico ){

		//tentando simular sessao de usuario interno do SEI
		SessaoSEI::getInstance()->setNumIdUnidadeAtual( $objUnidadeDTO->getNumIdUnidade() );
		SessaoSEI::getInstance()->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
		$objDocumentoRN = new DocumentoRN();
		$objDocumentoPeticionamentoRN = new DocumentoPeticionamentoRN();
		
		$arrDocumentoDTO = array();

		//verificar se foi editado documento principal gerado pelo editor do SEI
		if( isset( $arrParametros['docPrincipalConteudoHTML'] ) && $arrParametros['docPrincipalConteudoHTML'] != ""  ){
						
			$idTipoProc = $arrParametros['id_tipo_procedimento'];
			$objTipoProcDTO = new TipoProcessoPeticionamentoDTO();
			$objTipoProcDTO->retTodos(true);
			$objTipoProcDTO->setNumIdTipoProcessoPeticionamento( $idTipoProc );
			$objTipoProcRN = new TipoProcessoPeticionamentoRN();
			$objTipoProcDTO = $objTipoProcRN->consultar( $objTipoProcDTO );
						
			//====================================
			//gera no sistema as informações referentes ao documento principal
			//====================================
			//seiv2
			//$documentoDTOPrincipal = $this->montarDocumentoPrincipal( $objProcedimentoDTO, 
			//		                          $objTipoProcDTO, $objUnidadeDTO, 
			//		                          $arrParticipantesParametro, $arrParametros );
			//seiv3
			$documentoDTOPrincipal = $this->montarDocumentoPrincipal( $objProcedimentoDTO, 
					                          $objTipoProcDTO, $objUnidadeDTO, $arrParametros );

			//====================================
			//ASSINAR O DOCUMENTO PRINCIPAL
			//====================================			
			$this->assinarETravarDocumento( $objUnidadeDTO, $arrParametros, $documentoDTOPrincipal, $objProcedimentoDTO );
			
			//recibo do doc principal para consultar do usuario externo
			$reciboDocAnexoDTO = new ReciboDocumentoAnexoPeticionamentoDTO();
			$reciboDocAnexoRN = new ReciboDocumentoAnexoPeticionamentoRN();
			
			$reciboDocAnexoDTO->setNumIdAnexo( null );
			$reciboDocAnexoDTO->setNumIdReciboPeticionamento( $reciboDTOBasico->getNumIdReciboPeticionamento() );
			$reciboDocAnexoDTO->setNumIdDocumento( $documentoDTOPrincipal->getDblIdDocumento() );
			$reciboDocAnexoDTO->setStrClassificacaoDocumento( ReciboDocumentoAnexoPeticionamentoRN::$TP_PRINCIPAL );
			$reciboDocAnexoDTO = $reciboDocAnexoRN->cadastrar( $reciboDocAnexoDTO );

		} 

		//verificar se o documento principal é do tipo externo (ANEXO)
		else {
			
			$idTipoProc = $arrParametros['id_tipo_procedimento'];
			$objTipoProcDTO = new TipoProcessoPeticionamentoDTO();
			$objTipoProcDTO->retTodos(true);
			$objTipoProcDTO->setNumIdTipoProcessoPeticionamento( $idTipoProc );
			$objTipoProcRN = new TipoProcessoPeticionamentoRN();
			$objTipoProcDTO = $objTipoProcRN->consultar( $objTipoProcDTO );
			
		}
				
		//tratando documentos essenciais e complementares
		$anexoRN = new AnexoPeticionamentoRN();
		$strSiglaUsuario = SessaoSEIExterna::getInstance()->getStrSiglaUsuarioExterno();
		
		$tamanhoRN = new TamanhoArquivoPermitidoPeticionamentoRN();
		$tamanhoDTO = new TamanhoArquivoPermitidoPeticionamentoDTO();
		$tamanhoDTO->setStrSinAtivo('S');
		$tamanhoDTO->retTodos();
		
		$arrTamanhoDTO = $tamanhoRN->listarTamanhoMaximoConfiguradoParaUsuarioExterno( $tamanhoDTO );
		$tamanhoPrincipal = $arrTamanhoDTO[0]->getNumValorDocPrincipal();
		$tamanhoEssencialComplementar = $arrTamanhoDTO[0]->getNumValorDocComplementar();
		
		if( isset( $arrParametros['hdnDocPrincipal'] ) && $arrParametros['hdnDocPrincipal']  != "") {
			
			$arrAnexoDocPrincipal = $this->processarStringAnexos( $arrParametros['hdnDocPrincipal'] ,
					$objUnidadeDTO->getNumIdUnidade() ,
					$strSiglaUsuario,
					true,
					$objProcedimentoDTO->getDblIdProcedimento(), 
					$tamanhoPrincipal, "principais" );
			
			SessaoSEIExterna::getInstance()->setAtributo('arrIdAnexoPrincipal', null);
			$arrIdAnexoPrincipal = array();
			$arrAnexoPrincipalVinculacaoProcesso = array();
			$arrLinhasAnexos = PaginaSEI::getInstance()->getArrItensTabelaDinamica(  $arrParametros['hdnDocPrincipal']  );
			$contador = 0;	
			
			foreach( $arrAnexoDocPrincipal as $itemAnexo ){
				
				//================================
				//PROTOCOLO / DOCUMENTO DO ANEXO
				//=================================
				
				$idSerieAnexo = $arrLinhasAnexos[ $contador ][9];
				$strComplemento = $arrLinhasAnexos[ $contador ][10];
				$idTipoConferencia = $arrLinhasAnexos[ $contador ][7];
								
				$idNivelAcesso = null;
				
				if( $arrLinhasAnexos[ $contador ][4] == "Público" ){
					
					$idNivelAcesso = ProtocoloRN::$NA_PUBLICO;
					$idHipoteseLegal = null;
					
				} else if( $arrLinhasAnexos[ $contador ][4] == "Restrito" ){
					
					$idNivelAcesso = ProtocoloRN::$NA_RESTRITO;
					$idHipoteseLegal = $arrLinhasAnexos[ $contador ][5];
				}
								
				$idGrauSigilo = null;
				
				//criando registro em protocolo
				$objDocumentoDTO = new DocumentoDTO();
				$objDocumentoDTO->setStrNumero( $strComplemento );
				$objDocumentoDTO->setDblIdDocumento(null);
				$objDocumentoDTO->setDblIdProcedimento( $objProcedimentoDTO->getDblIdProcedimento() );
				$objDocumentoDTO->setStrStaNivelAcessoLocalProtocolo( $idNivelAcesso );
				
				if( $idNivelAcesso == ProtocoloRN::$NA_PUBLICO ){					
					$objDocumentoDTO->setNumIdHipoteseLegalProtocolo( null );
				} else if( $idNivelAcesso == ProtocoloRN::$NA_RESTRITO ){					
					$objDocumentoDTO->setNumIdHipoteseLegalProtocolo( $idHipoteseLegal );
				}
								
				$objDocumentoDTO->setDblIdDocumentoEdoc( null );
				$objDocumentoDTO->setDblIdDocumentoEdocBase( null );
				$objDocumentoDTO->setNumIdUnidadeResponsavel( SessaoSEI::getInstance()->getNumIdUnidadeAtual() );
				$objDocumentoDTO->setNumIdTipoConferencia( $idTipoConferencia );
                
                // alterações sei v3
                $objDocumentoDTO->setNumIdTipoFormulario(null);
                $objDocumentoDTO->setStrSinBloqueado('N');
                $objDocumentoDTO->setStrStaDocumento(DocumentoRN::$TD_EXTERNO);
				
                $arrObjUnidadeDTOReabertura = array();
				
				//se setar array da unidade pode cair na regra: "Unidade <nome-Unidade> não está sinalizada como protocolo." 
				//nao esta fazendo reabertura de processo - trata-se de processo novo
				$objDocumentoDTO->setArrObjUnidadeDTO($arrObjUnidadeDTOReabertura);

				//seiv2
				//$remetenteDTO = new ParticipanteDTO();
				//$remetenteRN = new ParticipanteRN();
				//$remetenteDTO->retTodos();
				//$remetenteDTO->setStrStaParticipacao( ParticipanteRN::$TP_REMETENTE );
				//$remetenteDTO->setNumIdContato( $contatoDTO->getNumIdContato() );
				//$remetenteDTO->setNumIdUnidade( $objUnidadeDTO->getNumIdUnidade() );
				//$remetenteDTO->setNumSequencia(0);

				//seiv2
				//$arrObjParticipantesDTO = $arrParticipantesParametro;				
				//$arrObjParticipantesDTO[] = $remetenteDTO;

				$objDocumentoDTO->setNumIdTextoPadraoInterno('');
				$objDocumentoDTO->setStrProtocoloDocumentoTextoBase('');
				$objDocumentoDTO->setNumIdSerie( $idSerieAnexo );
				$objDocumentoDTO->setStrStaDocumento(DocumentoRN::$TD_EXTERNO);

				$objSaidaDocumentoAPI = $this->gerarAssinarDocumentoAnexoSeiRN( $objUnidadeDTO, $arrParametros, $objDocumentoDTO, $objProcedimentoDTO, $itemAnexo, $reciboDTOBasico, ReciboDocumentoAnexoPeticionamentoRN::$TP_PRINCIPAL );

				//=============================
				//criando registro em anexo
				//=============================
				//TODO: obter o id_anexo
				$idDocumentoAnexo = $objSaidaDocumentoAPI->getIdDocumento();
				
				$strTamanho = str_replace("","Kb", $itemAnexo->getNumTamanho() );
				$strTamanho = str_replace("","Mb", $strTamanho );

				$itemAnexo->setDblIdProtocolo( $idDocumentoAnexo );
				$itemAnexo->setNumIdUnidade( $objUnidadeDTO->getNumIdUnidade() );
				$itemAnexo->setNumTamanho( (int)$strTamanho );
				$itemAnexo->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
				$itemAnexo->setStrSinAtivo('S');
				//$itemAnexo = $anexoRN->cadastrarRN0172( $itemAnexo );
												
				$arrAnexoPrincipalVinculacaoProcesso[] = $itemAnexo;
				$arrIdAnexoPrincipal[] = $idDocumentoAnexo;
				$contador = $contador+1;
		
			}
			
			if( count( $arrIdAnexoPrincipal ) > 0 ){
				SessaoSEIExterna::getInstance()->setAtributo('arrIdAnexoPrincipal', $arrIdAnexoPrincipal);
			}
				
		}
		
		if( isset( $arrParametros['hdnDocEssencial'] ) && $arrParametros['hdnDocEssencial']  != "") {
			
			$arrAnexoDocEssencial = $this->processarStringAnexos( $arrParametros['hdnDocEssencial'] , 
					                      $objUnidadeDTO->getNumIdUnidade() , 
					                      $strSiglaUsuario, 
					                      false, 
					                      $objProcedimentoDTO->getDblIdProcedimento(), 
										  $tamanhoEssencialComplementar, "essenciais");
			
			//$arrAnexoDocEssencial = AnexoINT::processarRI0872( $arrParametros['hdnDocEssencial'] );
			SessaoSEIExterna::getInstance()->setAtributo('arrIdAnexoEssencial', null);
			$arrIdAnexoEssencial = array();
			$arrAnexoEssencialVinculacaoProcesso = array();
			$arrAnexoComplementarVinculacaoProcesso = array();
			
			$arrLinhasAnexos = PaginaSEI::getInstance()->getArrItensTabelaDinamica(  $arrParametros['hdnDocEssencial']  );
			$contador = 0;
			
			foreach( $arrAnexoDocEssencial as $itemAnexo ){
				
				//================================
				//PROTOCOLO / DOCUMENTO DO ANEXO
				//=================================
				
				$idSerieAnexo = $arrLinhasAnexos[ $contador ][9];
				$strComplemento = $arrLinhasAnexos[ $contador ][10];
				$idTipoConferencia = $arrLinhasAnexos[ $contador ][7];
					
				$idNivelAcesso = null;
					
				if( $arrLinhasAnexos[ $contador ][4] == "Público" ){
					$idNivelAcesso = ProtocoloRN::$NA_PUBLICO;
					$idHipoteseLegal = null;
				} else if( $arrLinhasAnexos[ $contador ][4] == "Restrito" ){
					$idNivelAcesso = ProtocoloRN::$NA_RESTRITO;
					$idHipoteseLegal = $arrLinhasAnexos[ $contador ][5];
				}
								
				$idGrauSigilo = null;
					
				//criando registro em protocolo
				$objDocumentoDTO = new DocumentoDTO();
				$objDocumentoDTO->setStrNumero( $strComplemento );
				$objDocumentoDTO->setDblIdDocumento(null);
				$objDocumentoDTO->setDblIdProcedimento( $objProcedimentoDTO->getDblIdProcedimento() );
						
				$objDocumentoDTO->setNumIdSerie( $idSerieAnexo );
				$objDocumentoDTO->setNumIdHipoteseLegalProtocolo( $idHipoteseLegal );
				$objDocumentoDTO->setStrStaNivelAcessoLocalProtocolo( $idNivelAcesso );
					
				$objDocumentoDTO->setDblIdDocumentoEdoc( null );
				$objDocumentoDTO->setDblIdDocumentoEdocBase( null );
				$objDocumentoDTO->setNumIdUnidadeResponsavel( SessaoSEI::getInstance()->getNumIdUnidadeAtual() );
				$objDocumentoDTO->setNumIdTipoConferencia( $idTipoConferencia );
				$objDocumentoDTO->setStrSinBloqueado('S');
				$objDocumentoDTO->setStrStaDocumento(DocumentoRN::$TD_EXTERNO);
				
				$arrObjUnidadeDTOReabertura = array();
				//se setar array da unidade pode cair na regra: "Unidade <nome-Unidade> não está sinalizada como protocolo."
				//nao esta fazendo reabertura de processo - trata-se de processo novo
				$objDocumentoDTO->setArrObjUnidadeDTO($arrObjUnidadeDTOReabertura);

				//seiv2
				//$remetenteDTO = new ParticipanteDTO();
				//$remetenteRN = new ParticipanteRN();
				//$remetenteDTO->retTodos();
				//$remetenteDTO->setStrStaParticipacao( ParticipanteRN::$TP_REMETENTE );
				//$remetenteDTO->setNumIdContato( $contatoDTO->getNumIdContato() );
				//$remetenteDTO->setNumIdUnidade( $objUnidadeDTO->getNumIdUnidade() );
				//$remetenteDTO->setNumSequencia(0);

				//seiv2
				//$arrObjParticipantesDTO = $arrParticipantesParametro;
				//$arrObjParticipantesDTO[] = $remetenteDTO;

				$objDocumentoDTO->setNumIdTextoPadraoInterno('');
				$objDocumentoDTO->setStrProtocoloDocumentoTextoBase('');				
				$objDocumentoDTO->setNumIdSerie( $idSerieAnexo );

				$objSaidaDocumentoAPI = $this->gerarAssinarDocumentoAnexoSeiRN( $objUnidadeDTO, $arrParametros, $objDocumentoDTO, $objProcedimentoDTO, $itemAnexo, $reciboDTOBasico, ReciboDocumentoAnexoPeticionamentoRN::$TP_ESSENCIAL );

				//==================================
				//CRIANDO ANEXOS
				//=================================
				
				//TODO: obter o id_anexo
				$idDocumentoAnexo = $objSaidaDocumentoAPI->getIdDocumento();
				
				$strTamanho = str_replace("","Kb", $itemAnexo->getNumTamanho() );
				$strTamanho = str_replace("","Mb", $strTamanho );
				$itemAnexo->setDblIdProtocolo( $idDocumentoAnexo );
				$itemAnexo->setNumIdUnidade( $objUnidadeDTO->getNumIdUnidade() );
				$itemAnexo->setNumTamanho( (int)$strTamanho );
				$itemAnexo->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
				$itemAnexo->setStrSinAtivo('S');
				//$itemAnexo = $anexoRN->cadastrarRN0172( $itemAnexo );
				
				$arrAnexoEssencialVinculacaoProcesso[] = $itemAnexo; 
				$arrIdAnexoEssencial[] = $idDocumentoAnexo;
				//$arrIdAnexoEssencial[] = $itemAnexo->getNumIdAnexo();
				$contador = $contador+1;
				
			}
			
			if( count( $arrIdAnexoEssencial ) > 0 ){
				SessaoSEIExterna::getInstance()->setAtributo('arrIdAnexoEssencial', $arrIdAnexoEssencial);
			}
			
		}
		
		if( isset( $arrParametros['hdnDocComplementar'] ) && $arrParametros['hdnDocComplementar']  != "" ) {
			
			$arrAnexoDocComplementar = $this->processarStringAnexos( $arrParametros['hdnDocComplementar'] ,
					$objUnidadeDTO->getNumIdUnidade() ,
					$strSiglaUsuario,
					false,
					$objProcedimentoDTO->getDblIdProcedimento(), 
					$tamanhoEssencialComplementar, "complementares" );
			
			SessaoSEIExterna::getInstance()->setAtributo('arrIdAnexoComplementar', null);
			$arrIdAnexoComplementar = array();
			
			$arrLinhasAnexos = PaginaSEI::getInstance()->getArrItensTabelaDinamica(  $arrParametros['hdnDocComplementar']  );
			$contador = 0;
						
			foreach( $arrAnexoDocComplementar as $itemAnexoComplementar ){
				
				//================================
				//PROTOCOLO / DOCUMENTO DO ANEXO
				//=================================
				
				$idSerieAnexo = $arrLinhasAnexos[ $contador ][9];
				$strComplemento = $arrLinhasAnexos[ $contador ][10];
				$idTipoConferencia = $arrLinhasAnexos[ $contador ][7];
					
				$idNivelAcesso = null;
					
				if( $arrLinhasAnexos[ $contador ][4] == "Público" ){
					$idNivelAcesso = ProtocoloRN::$NA_PUBLICO;
					$idHipoteseLegal = null;
				} else if( $arrLinhasAnexos[ $contador ][4] == "Restrito" ){
					$idNivelAcesso = ProtocoloRN::$NA_RESTRITO;
					$idHipoteseLegal = $arrLinhasAnexos[ $contador ][5];
				}
				
				$idGrauSigilo = null;
					
				//criando registro em protocolo
				$objDocumentoDTO = new DocumentoDTO();
				$objDocumentoDTO->setStrNumero( $strComplemento );
				$objDocumentoDTO->setDblIdDocumento(null);
				$objDocumentoDTO->setDblIdProcedimento( $objProcedimentoDTO->getDblIdProcedimento() );
														
				$objDocumentoDTO->setDblIdDocumentoEdoc( null );
				$objDocumentoDTO->setDblIdDocumentoEdocBase( null );
				$objDocumentoDTO->setNumIdUnidadeResponsavel( SessaoSEI::getInstance()->getNumIdUnidadeAtual() );
				$objDocumentoDTO->setNumIdTipoConferencia( $idTipoConferencia );
				$objDocumentoDTO->setNumIdHipoteseLegalProtocolo( $idHipoteseLegal );
				$objDocumentoDTO->setStrStaNivelAcessoLocalProtocolo( $idNivelAcesso );
				$objDocumentoDTO->setStrSinBloqueado('S');
				$objDocumentoDTO->setStrStaDocumento(DocumentoRN::$TD_EXTERNO);
				$objDocumentoDTO->setNumVersao(0);
				
				$arrObjUnidadeDTOReabertura = array();
				//se setar array da unidade pode cair na regra: "Unidade <nome-Unidade> não está sinalizada como protocolo."
				//nao esta fazendo reabertura de processo - trata-se de processo novo
				$objDocumentoDTO->setArrObjUnidadeDTO($arrObjUnidadeDTOReabertura);
									
				//seiv2
				//$remetenteDTO = new ParticipanteDTO();
				//$remetenteRN = new ParticipanteRN();
				//$remetenteDTO->retTodos();
				//$remetenteDTO->setStrStaParticipacao( ParticipanteRN::$TP_REMETENTE );
				//$remetenteDTO->setNumIdContato( $contatoDTO->getNumIdContato() );
				//$remetenteDTO->setNumIdUnidade( $objUnidadeDTO->getNumIdUnidade() );
				//$remetenteDTO->setNumSequencia(0);

				//seiv3
				//$arrObjParticipantesDTO = $arrParticipantesParametro;
				//$arrObjParticipantesDTO[] = $remetenteDTO;

				$objDocumentoDTO->setNumIdTextoPadraoInterno('');
				$objDocumentoDTO->setStrProtocoloDocumentoTextoBase('');
				$objDocumentoDTO->setNumIdSerie( $idSerieAnexo );

				$objSaidaDocumentoAPI = $this->gerarAssinarDocumentoAnexoSeiRN( $objUnidadeDTO, $arrParametros, $objDocumentoDTO, $objProcedimentoDTO, $itemAnexoComplementar, $reciboDTOBasico, ReciboDocumentoAnexoPeticionamentoRN::$TP_COMPLEMENTAR );

				//========================
				//CRIANDO ANEXOS
				//========================
				
				//TODO: obter o id_anexo
				$idDocumentoAnexo = $objSaidaDocumentoAPI->getIdDocumento();
				
				$strTamanho = str_replace("","Kb", $itemAnexoComplementar->getNumTamanho() );
				$strTamanho = str_replace("","Mb", $strTamanho );
				$itemAnexoComplementar->setDblIdProtocolo( $idDocumentoAnexo );
				$itemAnexoComplementar->setNumIdUnidade( $objUnidadeDTO->getNumIdUnidade() );
				$itemAnexoComplementar->setNumTamanho( (int)$strTamanho );
				$itemAnexoComplementar->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
				$itemAnexoComplementar->setStrSinAtivo('S');
				//$itemAnexoComplementar = $anexoRN->cadastrarRN0172( $itemAnexoComplementar );
				$arrAnexoComplementarVinculacaoProcesso[] = $itemAnexoComplementar;
				$arrIdAnexoComplementar[] = $idDocumentoAnexo;
				
				$contador = $contador+1;
			}
			
			if( count( $arrIdAnexoComplementar ) > 0 ){
				SessaoSEIExterna::getInstance()->setAtributo('arrIdAnexoComplementar', $arrIdAnexoComplementar);
			}
			
		}
						
	}

	//NÃO FOI ACHADO USO
	//private function montarProtocoloDocumentoAnexo( $arrParametros, $objUnidadeDTO, $objProcedimentoDTO, 
	//		                                        $arrParticipantesParametro, $arrAnexos, $reciboDTOBasico ){
	//
	//    $reciboAnexoRN = new ReciboDocumentoAnexoPeticionamentoRN();
	//    $strClassificacao = $arrParametros['CLASSIFICACAO_RECIBO'];
	//
	//	foreach( $arrAnexos as $anexoDTOVinculado){	                                        	
	//
	//		$anexoBD = new AnexoBD( $this->getObjInfraIBanco() );
	//		$anexoBD->alterar( $anexoDTOVinculado );
	//
	//		$reciboAnexoDTO = new ReciboDocumentoAnexoPeticionamentoDTO();
	//		$reciboAnexoDTO->setNumIdAnexo( $anexoDTOVinculado->getNumIdAnexo() );
	//		$reciboAnexoDTO->setNumIdReciboPeticionamento( $reciboDTOBasico->getNumIdReciboPeticionamento() );
	//		$reciboAnexoDTO->setNumIdDocumento( $anexoDTOVinculado->getDblIdProtocolo() );
	//		$reciboAnexoDTO->setStrClassificacaoDocumento( $strClassificacao );				
	//		$reciboAnexoDTO = $reciboAnexoRN->cadastrar( $reciboAnexoDTO );
	//
	//	}
	//
	//}

	private function montarDocumentoPrincipal( $objProcedimentoDTO, 
			                                   $objTipoProcDTO, 
			                                   $objUnidadeDTO, 
			                                   //seiv2
			                                   //$arrParticipantesParametro,
			                                   $arrParametros ){

			$objSeiRN = new SeiRN();
			
			$nivelAcessoDocPrincipal = $arrParametros['nivelAcessoDocPrincipal'];
			$grauSigiloDocPrincipal = $arrParametros['grauSigiloDocPrincipal'];
			$hipoteseLegalDocPrincipal = $arrParametros['hipoteseLegalDocPrincipal'];

			//o proprio usuario externo logado é remetente do documento
			$contatoDTO = $this->getContatoDTOUsuarioLogado();

			//seiv2
			//$remetenteDTO = new ParticipanteDTO();
			//$remetenteRN = new ParticipanteRN();
			//$remetenteDTO->retTodos();
			//$remetenteDTO->setStrStaParticipacao( ParticipanteRN::$TP_REMETENTE );
			//$remetenteDTO->setNumIdContato( $contatoDTO->getNumIdContato() );
			//$remetenteDTO->setNumIdUnidade( $objUnidadeDTO->getNumIdUnidade() );
			//$remetenteDTO->setNumSequencia(0);

			//seiv2
			//$arrObjParticipantesDTO[] = $remetenteDTO;
			//$arrParticipantesParametro[] = $remetenteDTO;

			//Incluir documento interno
			$objDocumentoAPI = new DocumentoAPI();
			
			//Se o ID do processo é conhecido utilizar setIdProcedimento no lugar de
			$objDocumentoAPI->setIdProcedimento( $objProcedimentoDTO->getDblIdProcedimento() );
			$objDocumentoAPI->setTipo( ProtocoloRN::$TP_DOCUMENTO_GERADO );
			$objDocumentoAPI->setIdHipoteseLegal( $hipoteseLegalDocPrincipal );
			$objDocumentoAPI->setNivelAcesso( $nivelAcessoDocPrincipal );
			$objDocumentoAPI->setIdSerie( $objTipoProcDTO->getNumIdSerie() );
			$objDocumentoAPI->setConteudo(base64_encode( $arrParametros['docPrincipalConteudoHTML'] ));
			$objDocumentoAPI->setSinAssinado('S');
			$objDocumentoAPI->setSinBloqueado('S');

			$objSaidaIncluirDocumentoAPI = $objSeiRN->incluirDocumento($objDocumentoAPI);

			SessaoSEIExterna::getInstance()->setAtributo('idDocPrincipalGerado', $objSaidaIncluirDocumentoAPI->getIdDocumento() );

			$documentoDTOPrincipal = new DocumentoDTO();
			$documentoDTOPrincipal->setDblIdDocumento( $objSaidaIncluirDocumentoAPI->getIdDocumento() );
			return $documentoDTOPrincipal;
		
	}
	
	private function assinarETravarDocumento( $objUnidadeDTO, $arrParametros, $documentoDTO, $objProcedimentoDTO ){
			
		    //consultar email da unidade (orgao)
		    $orgaoRN = new OrgaoRN();
			$orgaoDTO = new OrgaoDTO();
			$orgaoDTO->retTodos();
			$orgaoDTO->retStrEmailContato();
			$orgaoDTO->setNumIdOrgao( $objUnidadeDTO->getNumIdOrgao() );
			$orgaoDTO->setStrSinAtivo('S');
			$orgaoDTO = $orgaoRN->consultarRN1352($orgaoDTO);

			//consultar nome do cargao funcao selecionada na combo
			$cargoRN = new CargoRN();
			$cargoDTO = new CargoDTO();
			//seiv2
			//$cargoDTO->retTodos();
			
			//alteracoes seiv3
            $cargoDTO->retNumIdCargo();
            $cargoDTO->retStrExpressao();
            $cargoDTO->retStrSinAtivo();
			
			$cargoDTO->setNumIdCargo( $arrParametros['selCargo'] );
			$cargoDTO->setStrSinAtivo('S');
			$cargoDTO = $cargoRN->consultarRN0301($cargoDTO);
						
			//liberando assinatura externa para o documento
			$objAcessoExternoDTO = new AcessoExternoDTO();
			
			//trocado de $TA_ASSINATURA_EXTERNA para $TA_SISTEMA para evitar o envio de email de notificação
			$objAcessoExternoDTO->setStrStaTipo(AcessoExternoRN::$TA_ASSINATURA_EXTERNA ); 
			
			//checar se o proprio usuario ja foi adicionado como interessado (participante) do processo
			$objUsuarioDTO = new UsuarioDTO();
			$objUsuarioDTO->retTodos();
			$objUsuarioDTO->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
			
			$objUsuarioRN = new UsuarioRN();
			$objUsuarioDTO = $objUsuarioRN->consultarRN0489( $objUsuarioDTO );
			$idContato = $objUsuarioDTO->getNumIdContato();
			
			$objParticipanteDTO = new ParticipanteDTO();
			$objParticipanteDTO->retStrSiglaContato();
			$objParticipanteDTO->retStrNomeContato();
			$objParticipanteDTO->retNumIdUnidade();
			$objParticipanteDTO->retDblIdProtocolo();
			$objParticipanteDTO->retNumIdParticipante();
			$objParticipanteDTO->setNumIdUnidade( $objUnidadeDTO->getNumIdUnidade() );
			$objParticipanteDTO->setNumIdContato( $idContato );
			$objParticipanteDTO->setDblIdProtocolo( $objProcedimentoDTO->getDblIdProcedimento() );
						
			$objParticipanteRN = new ParticipanteRN();
			$arrObjParticipanteDTO = $objParticipanteRN->listarRN0189($objParticipanteDTO);
			
			if( $arrObjParticipanteDTO == null || count( $arrObjParticipanteDTO ) == 0){
				
				//cadastrar o participante
				$objParticipanteDTO = new ParticipanteDTO();
				$objParticipanteDTO->setNumIdContato( $idContato );
				$objParticipanteDTO->setDblIdProtocolo( $objProcedimentoDTO->getDblIdProcedimento() );
				$objParticipanteDTO->setStrStaParticipacao( ParticipanteRN::$TP_ACESSO_EXTERNO );
				$objParticipanteDTO->setNumIdUnidade( $objUnidadeDTO->getNumIdUnidade() );
				$objParticipanteDTO->setNumSequencia(0);
				
				$objParticipanteDTO = $objParticipanteRN->cadastrarRN0170( $objParticipanteDTO );
				$idParticipante = $objParticipanteDTO->getNumIdParticipante();
				
			} else {
				
				$idParticipante = $arrObjParticipanteDTO[0]->getNumIdParticipante();
			}
			
			//seiv2
			//$objAcessoExternoDTO->setStrEmailUnidade($orgaoDTO->getStrEmail() ); //informando o email do orgao associado a unidade
			
			//alteracoes seiv3
			$objAcessoExternoDTO->setStrEmailUnidade($orgaoDTO->getStrEmailContato() ); //informando o email do orgao associado a unidade
			
			$objAcessoExternoDTO->setDblIdDocumento( $documentoDTO->getDblIdDocumento() );
			$objAcessoExternoDTO->setNumIdParticipante( $idParticipante );
			$objAcessoExternoDTO->setNumIdUsuarioExterno( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
			$objAcessoExternoDTO->setStrSinProcesso('N'); //visualizacao integral do processo
			
			$objAcessoExternoRN = new AcessoExternoPeticionamentoRN();
			$objAcessoExternoDTO = $objAcessoExternoRN->cadastrar($objAcessoExternoDTO);
			
			//realmente assinando o documento depois da assinatura externa ser liberada
			
			//seiv3 - só permite assinar doc externo (upload) nato-digital se tiver o tipo de conferencia setado
			//setar temporariamente e depois remover da entidade
			$documentoRN = new DocumentoRN();
			$documentoPetRN = new DocumentoPeticionamentoRN();
			$documentoBD = new DocumentoBD( $this->getObjInfraIBanco() );
			$bolRemoverTipoConferencia = false;
			
			if( !$documentoDTO->isSetNumIdTipoConferencia() || $documentoDTO->getNumIdTipoConferencia()==null ){

                // buscando o menor tipo de conferencia
                $tipoConferenciaDTOConsulta = new TipoConferenciaDTO();
                $tipoConferenciaDTOConsulta->retTodos();
                $tipoConferenciaDTOConsulta->setStrSinAtivo('S');
                $tipoConferenciaDTOConsulta->setOrd('IdTipoConferencia', InfraDTO::$TIPO_ORDENACAO_ASC);
                $tipoConferenciaRN = new TipoConferenciaRN();
                $arrTipoConferenciaDTO = $tipoConferenciaRN->listar($tipoConferenciaDTOConsulta);
                $numIdTipoConferencia = $arrTipoConferenciaDTO[0]->getNumIdTipoConferencia();
                // fim buscando o menor tipo de conferencia

				//setando um tipo de conferencia padrao (que sera removido depois), apenas para passar na validação
				$documentoDTO->setNumIdTipoConferencia($numIdTipoConferencia);
				$documentoAlteracaoDTO = new DocumentoDTO();
				$documentoAlteracaoDTO->retDblIdDocumento();
				$documentoAlteracaoDTO->retNumIdTipoConferencia();
				$documentoAlteracaoDTO->setDblIdDocumento( $documentoDTO->getDblIdDocumento() );
				$documentoAlteracaoDTO = $documentoRN->consultarRN0005( $documentoAlteracaoDTO );
				
				$documentoAlteracaoDTO->setNumIdTipoConferencia($numIdTipoConferencia);
				$documentoBD->alterar( $documentoAlteracaoDTO );
				$bolRemoverTipoConferencia = true;
				
				$objAssinaturaDTO = new AssinaturaDTO();
				$objAssinaturaDTO->setStrStaFormaAutenticacao(AssinaturaRN::$TA_SENHA);
				$objAssinaturaDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
				$objAssinaturaDTO->setStrSenhaUsuario( $arrParametros['senhaSEI'] );
				$objAssinaturaDTO->setStrCargoFuncao( "Usuário Externo - " . $cargoDTO->getStrExpressao() );
				$documentoDTO->setStrDescricaoTipoConferencia("do próprio documento nato-digital");
				$objAssinaturaDTO->setArrObjDocumentoDTO(array($documentoDTO));
				
			} else {
				
				$objAssinaturaDTO = new AssinaturaDTO();
				$objAssinaturaDTO->setStrStaFormaAutenticacao(AssinaturaRN::$TA_SENHA);
				$objAssinaturaDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
				$objAssinaturaDTO->setStrSenhaUsuario( $arrParametros['senhaSEI'] );
				$objAssinaturaDTO->setStrCargoFuncao( "Usuário Externo - " . $cargoDTO->getStrExpressao() );
				$objAssinaturaDTO->setArrObjDocumentoDTO(array($documentoDTO));		
				
			}
						
			//$documentoRN = new DocumentoPeticionamentoRN();
			$objAssinaturaDTO = $documentoPetRN->assinar($objAssinaturaDTO);
            
			//alteracoes seiv3 - removendo o tipo de conferencia padrao que foi informado
			if( $bolRemoverTipoConferencia ){
				$documentoDTO->setNumIdTipoConferencia(null);
				$documentoBD->alterar( $documentoDTO );
			}
			
			//nao aplicando metodo alterar da RN de Documento por conta de regras de negocio muito especificas aplicadas ali
			//$documentoBD = new DocumentoBD( $this->getObjInfraIBanco() );
			$documentoDTO->setStrSinBloqueado('S');
			$documentoBD->alterar( $documentoDTO );
			
			//remover a liberação de acesso externo -> AcessoRN.excluir nao permite exclusao, por isso chame AcessoExternoBD diretamente daqui
			$objAcessoExternoBD = new AcessoExternoBD($this->getObjInfraIBanco());
			$objAcessoExternoBD->excluir( $objAcessoExternoDTO );
		
	}

	//NÃO FOI ACHADO USO
	//private function montarArrContatosInteressados( $idsContatos ){
	//
	//	$contatoRN = new ContatoRN();
	//	$objContatoDTO = new ContatoDTO();
	//	$objContatoDTO->retStrSigla();
	//	$objContatoDTO->retStrNome();
	//	$objContatoDTO->retNumIdContato();

	//	$objContatoDTO->adicionarCriterio(array('IdContato', 'SinAtivo'),
	//			array(InfraDTO::$OPER_IN, InfraDTO::$OPER_IGUAL),
	//			array( $idsContatos,'S'),
	//			InfraDTO::$OPER_LOGICO_AND);

	//	$arrContatos = $contatoRN->listarRN0325( $objContatoDTO );
	//	return $arrContatos;
	//}

	private function atribuirParticipantes( $arrObjInteressados)
	{		
		
		$arrObjParticipantesDTO = array();
		
		//if($objProtocoloDTO->isSetArrObjParticipanteDTO()) {
			//$arrObjParticipantesDTO = $objProtocoloDTO->getArrObjParticipanteDTO();
		//}
	
		if (!is_array($arrObjInteressados)) {
			$arrObjInteressados = array($arrObjInteressados);
		}
	
		for($i=0; $i < count($arrObjInteressados); $i++){
			$objInteressado = $arrObjInteressados[$i];
			$objParticipanteDTO  = new ParticipanteDTO();
			$objParticipanteDTO->setStrSiglaContato($objInteressado->getStrSigla());
			$objParticipanteDTO->setStrNomeContato($objInteressado->getStrNome());
			$objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_INTERESSADO);
			$objParticipanteDTO->setNumSequencia($i);
			$objParticipanteDTO->setNumIdContato( $objInteressado->getNumIdContato() );
			$arrObjParticipantesDTO[] = $objParticipanteDTO;
		}
				
		$arrObjParticipanteDTO = $this->prepararParticipantes($arrObjParticipantesDTO);
		//$objProtocoloDTO->setArrObjParticipanteDTO($arrObjParticipantesDTO);
		return $arrObjParticipantesDTO;
	
	}
	
	private function atribuirDadosAndamento(ProcedimentoDTO $parObjProcedimentoDTO, $objHistorico, $objUnidadeDTO)
	{
		if(isset($objHistorico) && isset($objHistorico->operacao)){
	
			if (!is_array($objHistorico->operacao)) {
				$objHistorico->operacao = array($objHistorico->operacao);
			}
	
			$objAtividadeRN = new AtividadeRN();
			$objAtualizarAndamentoDTO = new AtualizarAndamentoDTO();
	
			//Buscar último andamento registrado do processo
			$objAtividadeDTO = new AtividadeDTO();
			$objAtividadeDTO->retDthAbertura();
			$objAtividadeDTO->retNumIdAtividade();
			$objAtividadeDTO->setDblIdProtocolo($parObjProcedimentoDTO->getDblIdProcedimento());
			$objAtividadeDTO->setOrdDthAbertura(InfraDTO::$TIPO_ORDENACAO_DESC);
			$objAtividadeDTO->setNumMaxRegistrosRetorno(1);
	
			$objAtividadeRN = new AtividadeRN();
			$objAtividadeDTO = $objAtividadeRN->consultarRN0033($objAtividadeDTO);
	
		}
	}
	
	protected function atribuirDadosUnidade(ProcedimentoDTO $objProcedimentoDTO, $objUnidadeDTOEnvio){
	
		if(!isset($objUnidadeDTOEnvio)){
			throw new InfraException('Parâmetro $objUnidadeDTOEnvio não informado.');
		}
		
		$arrObjUnidadeDTO = array();
		$arrObjUnidadeDTO[] = $objUnidadeDTOEnvio;
		$objProcedimentoDTO->setArrObjUnidadeDTO($arrObjUnidadeDTO);
	
		return $objUnidadeDTOEnvio;
	}
		
	//TODO: Método identico ao localizado na classe SeiRN:2214
	//Refatorar código para evitar problemas de manutenção
	private function prepararParticipantes($arrObjParticipanteDTO)
	{
		$objContatoRN = new ContatoRN();
		$objUsuarioRN = new UsuarioRN();
	
		foreach($arrObjParticipanteDTO as $objParticipanteDTO) {
	
			$objContatoDTO = new ContatoDTO();
			$objContatoDTO->retNumIdContato();
	
			if (!InfraString::isBolVazia($objParticipanteDTO->getStrSiglaContato()) && !InfraString::isBolVazia($objParticipanteDTO->getStrNomeContato())) {
				$objContatoDTO->setStrSigla($objParticipanteDTO->getStrSiglaContato());
				$objContatoDTO->setStrNome($objParticipanteDTO->getStrNomeContato());
	
			}  else if (!InfraString::isBolVazia($objParticipanteDTO->getStrSiglaContato())) {
				$objContatoDTO->setStrSigla($objParticipanteDTO->getStrSiglaContato());
	
			} else if (!InfraString::isBolVazia($objParticipanteDTO->getStrNomeContato())) {
				$objContatoDTO->setStrNome($objParticipanteDTO->getStrNomeContato());
			} else {
				if ($objParticipanteDTO->getStrStaParticipacao()==ParticipanteRN::$TP_INTERESSADO) {
					throw new InfraException('Interessado vazio ou nulo.');
				}
				else if ($objParticipanteDTO->getStrStaParticipacao()==ParticipanteRN::$TP_REMETENTE) {
					throw new InfraException('Remetente vazio ou nulo.');
				}
				else if ($objParticipanteDTO->getStrStaParticipacao()==ParticipanteRN::$TP_DESTINATARIO) {
					throw new InfraException('Destinatário vazio ou nulo.');
				}
			}
	
			$arrObjContatoDTO = $objContatoRN->listarRN0325($objContatoDTO);
	
			if (count($arrObjContatoDTO)) {
	
				$objContatoDTO = null;
	
				//preferencia para contatos que representam usuarios
				foreach($arrObjContatoDTO as $dto) {
	
					$objUsuarioDTO = new UsuarioDTO();
					$objUsuarioDTO->setBolExclusaoLogica(false);
					$objUsuarioDTO->setNumIdContato($dto->getNumIdContato());
	
					if ($objUsuarioRN->contarRN0492($objUsuarioDTO)) {
						$objContatoDTO = $dto;
						break;
					}
				}
	
				//nao achou contato de usuario pega o primeiro retornado
				if ($objContatoDTO==null)   {
					$objContatoDTO = $arrObjContatoDTO[0];
				}
			} else {
				$objContatoDTO = $objContatoRN->cadastrarContextoTemporario($objContatoDTO);
			}
	
			$objParticipanteDTO->setNumIdContato($objContatoDTO->getNumIdContato());
		}
	
		return $arrObjParticipanteDTO;
	}
	
	private function atribuirTipoProcedimento(ProcedimentoDTO $objProcedimentoDTO, $numIdTipoProcedimento)
	{
				
		if(!isset($numIdTipoProcedimento)){
			throw new InfraException('Parâmetro $numIdTipoProcedimento não informado.');
		}
	
		$objTipoProcedimentoDTO = new TipoProcedimentoDTO();
		$objTipoProcedimentoDTO->retNumIdTipoProcedimento();
		$objTipoProcedimentoDTO->retStrNome();
		$objTipoProcedimentoDTO->setNumIdTipoProcedimento($numIdTipoProcedimento);
	
		$objTipoProcedimentoRN = new TipoProcedimentoRN();
		$objTipoProcedimentoDTO = $objTipoProcedimentoRN->consultarRN0267($objTipoProcedimentoDTO);
	
		if ($objTipoProcedimentoDTO==null){
			throw new InfraException('Tipo de processo não encontrado.');
		}
	
		$objProcedimentoDTO->setNumIdTipoProcedimento($objTipoProcedimentoDTO->getNumIdTipoProcedimento());
		$objProcedimentoDTO->setStrNomeTipoProcedimento($objTipoProcedimentoDTO->getStrNome());
	
		//Busca e adiciona os assuntos sugeridos para o tipo informado
		$objRelTipoProcedimentoAssuntoDTO = new RelTipoProcedimentoAssuntoDTO();
		$objRelTipoProcedimentoAssuntoDTO->retNumIdAssunto();
		$objRelTipoProcedimentoAssuntoDTO->retNumSequencia();
		$objRelTipoProcedimentoAssuntoDTO->setNumIdTipoProcedimento($objProcedimentoDTO->getNumIdTipoProcedimento());
	
		$objRelTipoProcedimentoAssuntoRN = new RelTipoProcedimentoAssuntoRN();
		$arrObjRelTipoProcedimentoAssuntoDTO = $objRelTipoProcedimentoAssuntoRN->listarRN0192($objRelTipoProcedimentoAssuntoDTO);
		$arrObjAssuntoDTO = $objProcedimentoDTO->getObjProtocoloDTO()->getArrObjRelProtocoloAssuntoDTO();
	
		foreach($arrObjRelTipoProcedimentoAssuntoDTO as $objRelTipoProcedimentoAssuntoDTO){
			$objRelProtocoloAssuntoDTO = new RelProtocoloAssuntoDTO();
			$objRelProtocoloAssuntoDTO->setNumIdAssunto($objRelTipoProcedimentoAssuntoDTO->getNumIdAssunto());
			$objRelProtocoloAssuntoDTO->setNumSequencia($objRelTipoProcedimentoAssuntoDTO->getNumSequencia());
			$arrObjAssuntoDTO[] = $objRelProtocoloAssuntoDTO;
		}
	
		$objProcedimentoDTO->getObjProtocoloDTO()->setArrObjRelProtocoloAssuntoDTO($arrObjAssuntoDTO);
	}
	
	// public para que possa, eventualmente, ser usado por outras estorias de usuario
	// nao foi possivel usar a classe AnexoINT para processar a string de anexos, por conta da quantidade diferenciada 
	// de campos da grid da tela de peticionamento
	// dentre outras especificidades técnicas desta tela
	public function processarStringAnexos($strDelimitadaAnexos, $idUnidade, $strSiglaUsuario, $bolDocumentoPrincipal, $idProtocolo, 
			                              $numTamanhoArquivoPermitido, $strAreaDocumento ){
		
		$arrAnexos = array();
				
		$arrAnexos = PaginaSEI::getInstance()->getArrItensTabelaDinamica($strDelimitadaAnexos);
		$arrObjAnexoDTO = array();
		
		foreach($arrAnexos as $anexo){
			
			$tamanhoDoAnexo = $anexo[2];
			
			//o tamanho do arquivo pode vir em Mb ou em Kb
			//se vier em Mb compara o tamanho, se vier em Kb é porque é menor do que 1Mb e portanto deixar passar (nao havera limite inferior a 1Mb)
			if (strpos( $tamanhoDoAnexo , 'Mb') !== false) {
				
				$tamanhoDoAnexo = str_replace(" Mb","", $tamanhoDoAnexo );
								
				//validando tamanho máximo do arquivo
				if( floatval($tamanhoDoAnexo) > floatval($numTamanhoArquivoPermitido) ){
					
					$objInfraException = new InfraException();
					$objInfraException->adicionarValidacao('Um dos documentos ' . $strAreaDocumento . ' adicionados excedeu o tamanho máximo permitido (Limite: ' . $numTamanhoArquivoPermitido . ' Mb).');
					$objInfraException->lancarValidacoes();
					
				} else {
					
					$tamanhoDoAnexo = floatval( ( $tamanhoDoAnexo*1024 ) * 1024 );
				}
				
			} else {
				
				$tamanhoDoAnexo = str_replace(" Kb","", $tamanhoDoAnexo );
				$tamanhoDoAnexo = floatval($tamanhoDoAnexo*1024);
			}

			$objAnexoDTO = new AnexoDTO();
			$objAnexoDTO->setNumIdAnexo( null );
			$objAnexoDTO->setStrSinAtivo('S');
			$objAnexoDTO->setStrNome($anexo[0]);
			$objAnexoDTO->setStrHash($anexo[8]);
			$objAnexoDTO->setDthInclusao($anexo[1]);
			$objAnexoDTO->setNumTamanho( $tamanhoDoAnexo );
			$objAnexoDTO->setStrSiglaUsuario( $strSiglaUsuario );
			$objAnexoDTO->setStrSiglaUnidade( $idUnidade );
			$objAnexoDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
			$arrObjAnexoDTO[] = $objAnexoDTO;
		}
		
		return $arrObjAnexoDTO;
	}
	
	private function getContatoDTOUsuarioLogado(){
		
		$usuarioRN = new UsuarioRN();
		$usuarioDTO = new UsuarioDTO();
		$usuarioDTO->retNumIdUsuario();
		$usuarioDTO->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
		$usuarioDTO->retNumIdContato();
		$usuarioDTO->retStrNomeContato();
		$usuarioDTO = $usuarioRN->consultarRN0489( $usuarioDTO );
		
		$contatoRN = new ContatoRN();
		$contatoDTO = new ContatoDTO();
		$contatoDTO->retTodos();
		$contatoDTO->setNumIdContato( $usuarioDTO->getNumIdContato() );
		$contatoDTO = $contatoRN->consultarRN0324( $contatoDTO );
		
		return $contatoDTO;
	}

}
?>