<?
/**
* ANATEL
*
* 28/06/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class ProcessoPeticionamentoRN extends InfraRN { 
	
	public static $PROPRIO_USUARIO_EXTERNO = 'U';
	public static $INDICACAO_DIRETA = 'I';
	public static $DOC_GERADO = 'G';
	public static $DOC_EXTERNO = 'E';	
	
	public function __construct() {
		
		session_start();
		
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
	
	protected function validarSenhaConectado( $arrParametros ) {
		
		$objInfraException = new InfraException();
				
		//validar a senha no SEI
		$objUsuarioDTO = new UsuarioDTO();
		$objUsuarioDTO->retStrSigla();
		$objUsuarioDTO->retStrSenha();
		$objUsuarioDTO->setStrSigla(SessaoSEIExterna::getInstance()->getStrSiglaUsuarioExterno());
		$objUsuarioDTO->setStrSenha(md5( $arrParametros['senhaSEI'] ) );
		 
		$objUsuarioRN = new UsuarioRN();
		$arrListaUsuario = $objUsuarioRN->listarRN0490( $objUsuarioDTO );
		$totalUsuarioValido = count( $arrListaUsuario );
		
		//ASSINATURA VALIDA
		if( $totalUsuarioValido == 0 ){
			$objInfraException->adicionarValidacao("Senha inválida.");
			$objInfraException->lancarValidacoes();
		}
		
	}	

	protected function gerarProcedimentoControlado( $arrParametros ){
		
		try {	
			
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
			
			//echo $idUnidade; die();
			
			//obter unidade configurada no "Tipo de Processo para peticionamento"
			//$idUnidade = $objTipoProcDTO->getNumIdUnidade();
			$unidadeRN = new UnidadeRN();
			$unidadeDTO = new UnidadeDTO();
			$unidadeDTO->retTodos();
			$unidadeDTO->setNumIdUnidade( $idUnidade );
			$unidadeDTO = $unidadeRN->consultarRN0125( $unidadeDTO );
			
			//print_r( $unidadeDTO );die();
			$protocoloRN = new ProtocoloPeticionamentoRN();
			$numeracaoProcesso = $protocoloRN->gerarNumeracaoProcessoExterno( $unidadeDTO );
			
			//Atribuição de dados do protocolo
			$objProtocoloDTO = new ProtocoloDTO();
			$objProtocoloDTO->setDblIdProtocolo(null);
			$objProtocoloDTO->setStrDescricao( $arrParametros['txtEspecificacaoDocPrincipal'] );
			$objProtocoloDTO->setStrStaNivelAcessoLocal( ProtocoloRN::$NA_PUBLICO );
			$objProtocoloDTO->setStrProtocoloFormatado( $numeracaoProcesso );
			$objProtocoloDTO->setNumIdUnidadeGeradora( $unidadeDTO->getNumIdUnidade() );
			$objProtocoloDTO->setNumIdUsuarioGerador( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
			
			$objProtocoloDTO->setDtaGeracao( InfraData::getStrDataAtual() );
			$objProtocoloDTO->setArrObjAnexoDTO(array());
			$objProtocoloDTO->setArrObjRelProtocoloAssuntoDTO(array());
			$objProtocoloDTO->setArrObjRelProtocoloProtocoloDTO(array());
			//$this->atribuirRemetente($objProtocoloDTO, $objRemetente);
					
			$arrParticipantesParametro = array();
			
			//verificar se esta vindo o array de participantes
			if( isset( $arrParametros['hdnListaInteressados'] ) && $arrParametros['hdnListaInteressados'] != "" ){			
				
				$arrContatosInteressados = array();
				$idsContatos = split(",", $arrParametros['hdnListaInteressados']);
				$arrParticipantesParametro = $this->atribuirParticipantes($objProtocoloDTO, $this->montarArrContatosInteressados( $idsContatos ) );
							
			} else {
				
				$this->atribuirParticipantes($objProtocoloDTO, array() );
			}
					
			$objProtocoloDTO->setArrObjObservacaoDTO( array() );
			
			//Atribuição de dados do procedimento
			$objProcedimentoDTO = new ProcedimentoDTO();
			$objProcedimentoDTO->setNumIdUnidadeGeradoraProtocolo( $unidadeDTO->getNumIdUnidade() );
			$objProcedimentoDTO->setDblIdProcedimento(null);
			$objProcedimentoDTO->setObjProtocoloDTO($objProtocoloDTO);
			$objProcedimentoDTO->setStrNomeTipoProcedimento( $txtTipoProcessoEscolhido );
			$objProcedimentoDTO->setDtaGeracaoProtocolo( InfraData::getStrDataAtual() );
			$objProcedimentoDTO->setStrProtocoloProcedimentoFormatado( $numeracaoProcesso );
			$objProcedimentoDTO->setStrSinGerarPendencia('S');
			$objProcedimentoDTO->setNumVersaoLock(0);  //TODO: Avaliar o comportamento desse campo no cadastro do processo
			$objProcedimentoDTO->setArrObjDocumentoDTO(array());
		
			//Identificar o tipo de procedimento correto para atribuição ao novo processo
			$numIdTipoProcedimento = $objTipoProcDTO->getNumIdProcedimento();
			$this->atribuirTipoProcedimento($objProcedimentoDTO, $numIdTipoProcedimento );
		
			//atribuir unidade destinataria do processo
			$objUnidadeDTO = $this->atribuirDadosUnidade($objProcedimentoDTO, $unidadeDTO);		
			$objProcedimentoDTO->setNumIdUnidadeGeradoraProtocolo( $objUnidadeDTO->getNumIdUnidade() );
			
			$objProcedimentoRN = new ProcedimentoPeticionamentoRN();
			$objProcedimentoDTOGerado = $objProcedimentoRN->gerarRN0156($objProcedimentoDTO);
			$objProcedimentoDTO->setDblIdProcedimento($objProcedimentoDTOGerado->getDblIdProcedimento());
			
			//gerando recibo e adicionando recibo NAO ASSINADO ao processo
			$reciboPeticionamentoRN = new ReciboPeticionamentoRN();
			
			//$reciboDTOBasico = null;
			$reciboDTOBasico = $reciboPeticionamentoRN->gerarReciboSimplificado( $objProcedimentoDTO->getDblIdProcedimento() );
			//print_r( $reciboDTOBasico ); die();
			
			$this->montarArrDocumentos( $arrParametros, $objUnidadeDTO, $objProcedimentoDTO, $arrParticipantesParametro, $reciboDTOBasico );
			
			//$this->enviarProcedimentoUnidade($objProcedimentoDTO);
			
			$arrParams = array();
			$arrParams[0] = $arrParametros;
			$arrParams[1] = $objUnidadeDTO;
			$arrParams[2] = $objProcedimentoDTO;
			$arrParams[3] = $arrParticipantesParametro;
			//$arrDocsPrincipais = $arrParams[4]; //array de DocumentoDTO (docs principais)
			//$arrDocsEssenciais = $arrParams[5]; //array de DocumentoDTO (docs essenciais)
			//$arrDocsComplementares = $arrParams[6]; //array de DocumentoDTO (docs complementares)
					
			//$reciboPeticionamentoRN->montarRecibo( $arrParams );
			
			$arrProcessoReciboRetorno = array();
			$arrProcessoReciboRetorno[0] = $reciboDTOBasico;
			//$arrProcessoReciboRetorno[0] = $retornoRecibo;
			$arrProcessoReciboRetorno[1] = $objProcedimentoDTO;
			
			//enviando email de sistema EU 5155  / 5156 - try catch por causa que em localhost o envio de email gera erro 
			try {
			  $emailNotificacaoPeticionamentoRN = new EmailNotificacaoPeticionamentoRN();
			  $emailNotificacaoPeticionamentoRN->notificaoPeticionamentoExterno( $arrParams );
			} catch( Exception $exEmail ){}
			
			return $arrProcessoReciboRetorno;
		
		} catch(Exception $e){
			 throw new InfraException('Erro cadastrando processo peticionamento do SEI.',$e);
		}
		
	}
	
	private function montarArrDocumentos( $arrParametros, $objUnidadeDTO, $objProcedimentoDTO, $arrParticipantesParametro, $reciboDTOBasico ){
		
		//tentando simular sessao de usuario interno do SEI
		SessaoSEI::getInstance()->setNumIdUnidadeAtual( $objUnidadeDTO->getNumIdUnidade() );
		SessaoSEI::getInstance()->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
		
		$arrDocumentoDTO = array();
		
		//verificar se foi editado documento principal gerado pelo editor do SEI
		if( isset( $arrParametros['docPrincipalConteudoHTML'] ) && $arrParametros['docPrincipalConteudoHTML'] != ""  ){
						
			$idTipoProc = $arrParametros['id_tipo_procedimento'];
			$objTipoProcDTO = new TipoProcessoPeticionamentoDTO();
			$objTipoProcDTO->retTodos(true);
			$objTipoProcDTO->setNumIdTipoProcessoPeticionamento( $idTipoProc );
			$objTipoProcRN = new TipoProcessoPeticionamentoRN();
			$objTipoProcDTO = $objTipoProcRN->consultar( $objTipoProcDTO );
			
			$protocoloRN = new ProtocoloPeticionamentoRN();
			$numeroDocumento = $protocoloRN->gerarNumeracaoDocumento();
			
			//====================================
			//gera no sistema as informações referentes ao documento principal
			//====================================
			$documentoDTOPrincipal = $this->montarDocumentoPrincipal( $objProcedimentoDTO, 
					                          $objTipoProcDTO, $objUnidadeDTO, 
					                          $arrParticipantesParametro, $arrParametros );
			
			//====================================
			//ASSINAR O DOCUMENTO PRINCIPAL
			//====================================			
			$this->assinarETravarDocumento( $objUnidadeDTO, $arrParametros, $documentoDTOPrincipal, $objProcedimentoDTO );
														
		} 
		
		//verificar se o documento principal é do tipo externo (ANEXO)
		else {
			
			//echo "aqui 2"; die();
			$idTipoProc = $arrParametros['id_tipo_procedimento'];
			$objTipoProcDTO = new TipoProcessoPeticionamentoDTO();
			$objTipoProcDTO->retTodos(true);
			$objTipoProcDTO->setNumIdTipoProcessoPeticionamento( $idTipoProc );
			$objTipoProcRN = new TipoProcessoPeticionamentoRN();
			$objTipoProcDTO = $objTipoProcRN->consultar( $objTipoProcDTO );
				
			$protocoloRN = new ProtocoloPeticionamentoRN();
			$numeroDocumento = $protocoloRN->gerarNumeracaoDocumento();
			
			//$arrAnexoDocPrincipal = AnexoINT::processarRI0872( $arrParametros['hdnDocPrincipal'] );
			
		}
				
		//tratando documentos essenciais e complementares
		$anexoRN = new AnexoPeticionamentoRN();
		$strSiglaUsuario = SessaoSEIExterna::getInstance()->getStrSiglaUsuarioExterno();
		
		if( isset( $arrParametros['hdnDocPrincipal'] ) && $arrParametros['hdnDocPrincipal']  != "") {
			
			$arrAnexoDocPrincipal = $this->processarStringAnexos( $arrParametros['hdnDocPrincipal'] ,
					$objUnidadeDTO->getNumIdUnidade() ,
					$strSiglaUsuario,
					true,
					$objProcedimentoDTO->getDblIdProcedimento()  );
			
			$arrAnexoPrincipalVinculacaoProcesso = array();
			$arrLinhasAnexos = PaginaSEI::getInstance()->getArrItensTabelaDinamica(  $arrParametros['hdnDocPrincipal']  );
			$contador = 0;	
			$objDocumentoRN = new DocumentoRN();
			
			foreach( $arrAnexoDocPrincipal as $itemAnexo ){
				
				//================================
				//PROTOCOLO / DOCUMENTO DO ANEXO
				//=================================
				
				$idSerieAnexo = $arrLinhasAnexos[ $contador ][9];
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
				$objDocumentoDTO->setDblIdDocumento(null);
				$objDocumentoDTO->setDblIdProcedimento( $objProcedimentoDTO->getDblIdProcedimento() );
				
				$objProtocoloDTO = new ProtocoloDTO();
				$objProtocoloDTO->setDblIdProtocolo(null);
				
				$objDocumentoDTO->setStrStaNivelAcessoLocalProtocolo( $idNivelAcesso );
				
				if( $idNivelAcesso == ProtocoloRN::$NA_PUBLICO ){
					
					$objDocumentoDTO->setNumIdHipoteseLegalProtocolo( null );
				}
				
				else if( $idNivelAcesso == ProtocoloRN::$NA_RESTRITO ){
					
					$objDocumentoDTO->setNumIdHipoteseLegalProtocolo( $idHipoteseLegal );
				}
								
				$objDocumentoDTO->setDblIdDocumentoEdoc( null );
				$objDocumentoDTO->setDblIdDocumentoEdocBase( null );
				$objDocumentoDTO->setNumIdUnidadeResponsavel( SessaoSEI::getInstance()->getNumIdUnidadeAtual() );
				$objDocumentoDTO->setStrNumero(null);
				$objDocumentoDTO->setNumIdTipoConferencia( $idTipoConferencia );
				$objDocumentoDTO->setStrSinFormulario('N');
				$objDocumentoDTO->setStrSinBloqueado('N');
				
				$objDocumentoDTO->setStrStaEditor( null );				
				$objDocumentoDTO->setNumVersaoLock(0);
				
				$arrObjUnidadeDTOReabertura = array();
				//se setar array da unidade pode cair na regra: "Unidade <nome-Unidade> não está sinalizada como protocolo." 
				//nao esta fazendo reabertura de processo - trata-se de processo novo
				//$arrObjUnidadeDTOReabertura[] = $objUnidadeDTO;				
				$objDocumentoDTO->setArrObjUnidadeDTO($arrObjUnidadeDTOReabertura);
				
				$objProtocoloDTO->setStrStaNivelAcessoLocal( $idNivelAcesso );
				
				if( $idNivelAcesso == ProtocoloRN::$NA_PUBLICO ){
				   $objProtocoloDTO->setNumIdHipoteseLegal( null );
				}
				
				else if( $idNivelAcesso == ProtocoloRN::$NA_RESTRITO ){
					$objProtocoloDTO->setNumIdHipoteseLegal( $idHipoteseLegal );
				}
				
				$objProtocoloDTO->setStrStaGrauSigilo( $idGrauSigilo );
				
				$objProtocoloDTO->setStrDescricao('');
				$objProtocoloDTO->setDtaGeracao(InfraData::getStrDataAtual());
				
				//$arrAssuntos = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnAssuntos']);
				//ASSUNTOS
				$arrObjAssuntosDTO = array();
				$objProtocoloDTO->setArrObjRelProtocoloAssuntoDTO($arrObjAssuntosDTO);
				
				//INTERESSADOS
				$arrObjParticipantesDTO = array();
				$objProtocoloDTO->setArrObjParticipanteDTO($arrObjParticipantesDTO);
				
				//OBSERVACOES
				$objObservacaoDTO  = new ObservacaoDTO();
				$objObservacaoDTO->setStrDescricao('');
				$objProtocoloDTO->setArrObjObservacaoDTO(array($objObservacaoDTO));
				
				//ATRIBUTOS
				$arrRelProtocoloAtributo = AtributoINT::processarRI0691();
				$arrObjRelProtocoloAtributoDTO = array();
				for($x = 0;$x<count($arrRelProtocoloAtributo);$x++){
					$arrRelProtocoloAtributoDTO = new RelProtocoloAtributoDTO();
					$arrRelProtocoloAtributoDTO->setStrValor($arrRelProtocoloAtributo[$x]->getStrValor());
					$arrRelProtocoloAtributoDTO->setNumIdAtributo($arrRelProtocoloAtributo[$x]->getNumIdAtributo());
					$arrObjRelProtocoloAtributoDTO[$x] = $arrRelProtocoloAtributoDTO;
				}
				$objProtocoloDTO->setArrObjRelProtocoloAtributoDTO($arrObjRelProtocoloAtributoDTO);
				
				//ANEXOS
				$objProtocoloDTO->setArrObjAnexoDTO( array() );
				//$objProtocoloDTO->setArrObjAnexoDTO(AnexoINT::processarRI0872($_POST['hdnAnexos']));
				
				$objDocumentoDTO->setObjProtocoloDTO($objProtocoloDTO);
				
				$objDocumentoDTO->setNumIdTextoPadraoInterno('');
				$objDocumentoDTO->setStrProtocoloDocumentoTextoBase('');
				
				$objDocumentoDTO->setNumIdSerie( $idSerieAnexo );
				$objProtocoloDTO->setNumIdSerieDocumento( $idSerieAnexo );
				
				$objDocumentoDTO = $objDocumentoRN->receberRN0991($objDocumentoDTO);
								
				//=============================
				//criando registro em anexo
				//=============================
				$strTamanho = str_replace("","Kb", $itemAnexo->getNumTamanho() );
				$strTamanho = str_replace("","Mb", $strTamanho );
				
				//TODO aplicar regra para validar tamanho do anexo enviado 
				
				$itemAnexo->setDblIdProtocolo( $objDocumentoDTO->getDblIdDocumento() );
				$itemAnexo->setNumIdUnidade( $objUnidadeDTO->getNumIdUnidade() );
				$itemAnexo->setNumTamanho( (int)$strTamanho );
				$itemAnexo->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
				$itemAnexo->setStrSinAtivo('S');
				$itemAnexo = $anexoRN->cadastrarRN0172( $itemAnexo );
				
				$this->assinarETravarDocumento( $objUnidadeDTO, $arrParametros, $objDocumentoDTO, $objProcedimentoDTO );
				
				$arrAnexoPrincipalVinculacaoProcesso[] = $itemAnexo;
				$contador = $contador+1;
		
			}
				
			//cria o protocolo, cria o documento, e no documento aponta o procedimento (o processo)
			$arrParametros['CLASSIFICACAO_RECIBO'] = ReciboDocumentoAnexoPeticionamentoRN::$TP_PRINCIPAL;
			
			$this->montarProtocoloDocumentoAnexo( $arrParametros, $objUnidadeDTO, $objProcedimentoDTO,
					$arrParticipantesParametro, $arrAnexoPrincipalVinculacaoProcesso, $reciboDTOBasico );
				
		}
		
		if( isset( $arrParametros['hdnDocEssencial'] ) && $arrParametros['hdnDocEssencial']  != "") {
			
			$arrAnexoDocEssencial = $this->processarStringAnexos( $arrParametros['hdnDocEssencial'] , 
					                      $objUnidadeDTO->getNumIdUnidade() , 
					                      $strSiglaUsuario, 
					                      false, 
					                      $objProcedimentoDTO->getDblIdProcedimento()  );
			
			//$arrAnexoDocEssencial = AnexoINT::processarRI0872( $arrParametros['hdnDocEssencial'] );
			$arrAnexoEssencialVinculacaoProcesso = array();
			$arrAnexoComplementarVinculacaoProcesso = array();
			
			$arrLinhasAnexos = PaginaSEI::getInstance()->getArrItensTabelaDinamica(  $arrParametros['hdnDocEssencial']  );
			$contador = 0;
			
			foreach( $arrAnexoDocEssencial as $itemAnexo ){
				
				//================================
				//PROTOCOLO / DOCUMENTO DO ANEXO
				//=================================
				
				$idSerieAnexo = $arrLinhasAnexos[ $contador ][9];
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
				$objDocumentoDTO->setDblIdDocumento(null);
				$objDocumentoDTO->setDblIdProcedimento( $objProcedimentoDTO->getDblIdProcedimento() );
					
				$objProtocoloDTO = new ProtocoloDTO();
				$objProtocoloDTO->setDblIdProtocolo(null);
					
				$objDocumentoDTO->setNumIdSerie( $idSerieAnexo );
				$objProtocoloDTO->setNumIdSerieDocumento( $idSerieAnexo );
					
				$objDocumentoDTO->setDblIdDocumentoEdoc( null );
				$objDocumentoDTO->setDblIdDocumentoEdocBase( null );
				$objDocumentoDTO->setNumIdUnidadeResponsavel( SessaoSEI::getInstance()->getNumIdUnidadeAtual() );
				$objDocumentoDTO->setStrNumero(null);
				$objDocumentoDTO->setNumIdTipoConferencia( $idTipoConferencia );
				$objDocumentoDTO->setStrSinFormulario('N');
				$objDocumentoDTO->setStrSinBloqueado('N');
					
				$objDocumentoDTO->setStrStaEditor( null );
					
				$objDocumentoDTO->setNumVersaoLock(0);
					
				$arrObjUnidadeDTOReabertura = array();
				//se setar array da unidade pode cair na regra: "Unidade <nome-Unidade> não está sinalizada como protocolo."
				//nao esta fazendo reabertura de processo - trata-se de processo novo
				//$arrObjUnidadeDTOReabertura[] = $objUnidadeDTO;
				$objDocumentoDTO->setArrObjUnidadeDTO($arrObjUnidadeDTOReabertura);
					
				$objProtocoloDTO->setStrStaNivelAcessoLocal( $idNivelAcesso );
				$objProtocoloDTO->setNumIdHipoteseLegal( $idHipoteseLegal );
				$objProtocoloDTO->setStrStaGrauSigilo( $idGrauSigilo );
					
				$objProtocoloDTO->setStrDescricao('');
				$objProtocoloDTO->setDtaGeracao(InfraData::getStrDataAtual());
					
				//$arrAssuntos = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnAssuntos']);
				//ASSUNTOS
				$arrObjAssuntosDTO = array();
				$objProtocoloDTO->setArrObjRelProtocoloAssuntoDTO($arrObjAssuntosDTO);
					
				//INTERESSADOS
				$arrObjParticipantesDTO = array();
				$objProtocoloDTO->setArrObjParticipanteDTO($arrObjParticipantesDTO);
					
				//OBSERVACOES
				$objObservacaoDTO  = new ObservacaoDTO();
				$objObservacaoDTO->setStrDescricao('');
				$objProtocoloDTO->setArrObjObservacaoDTO(array($objObservacaoDTO));
					
				//ATRIBUTOS
				$arrRelProtocoloAtributo = AtributoINT::processarRI0691();
				$arrObjRelProtocoloAtributoDTO = array();
				for($x = 0;$x<count($arrRelProtocoloAtributo);$x++){
					$arrRelProtocoloAtributoDTO = new RelProtocoloAtributoDTO();
					$arrRelProtocoloAtributoDTO->setStrValor($arrRelProtocoloAtributo[$x]->getStrValor());
					$arrRelProtocoloAtributoDTO->setNumIdAtributo($arrRelProtocoloAtributo[$x]->getNumIdAtributo());
					$arrObjRelProtocoloAtributoDTO[$x] = $arrRelProtocoloAtributoDTO;
				}
				$objProtocoloDTO->setArrObjRelProtocoloAtributoDTO($arrObjRelProtocoloAtributoDTO);
					
				//ANEXOS
				$objProtocoloDTO->setArrObjAnexoDTO( array() );
				//$objProtocoloDTO->setArrObjAnexoDTO(AnexoINT::processarRI0872($_POST['hdnAnexos']));
					
				$objDocumentoDTO->setObjProtocoloDTO($objProtocoloDTO);
					
				$objDocumentoDTO->setNumIdTextoPadraoInterno('');
				$objDocumentoDTO->setStrProtocoloDocumentoTextoBase('');
				
				$objDocumentoDTO->setNumIdSerie( $idSerieAnexo );
				$objProtocoloDTO->setNumIdSerieDocumento( $idSerieAnexo );
				
				$objDocumentoDTO = $objDocumentoRN->receberRN0991($objDocumentoDTO);
				
				//==================================
				//CRIANDO ANEXOS
				//=================================
				
				$strTamanho = str_replace("","Kb", $itemAnexo->getNumTamanho() );
				$strTamanho = str_replace("","Mb", $strTamanho );
				$itemAnexo->setDblIdProtocolo( $objDocumentoDTO->getDblIdDocumento() );
				$itemAnexo->setNumIdUnidade( $objUnidadeDTO->getNumIdUnidade() );
				$itemAnexo->setNumTamanho( (int)$strTamanho );
				$itemAnexo->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
				$itemAnexo->setStrSinAtivo('S');
				//print_r( $itemAnexo ); die();
				$itemAnexo = $anexoRN->cadastrarRN0172( $itemAnexo );
				
				$this->assinarETravarDocumento( $objUnidadeDTO, $arrParametros, $objDocumentoDTO, $objProcedimentoDTO );
				
				$arrAnexoEssencialVinculacaoProcesso[] = $itemAnexo; 
				$contador =  $contador+1;
				
			}
			
			//cria o protocolo, cria o documento, e no documento aponta o procedimento (o processo)
			$arrParametros['CLASSIFICACAO_RECIBO'] = ReciboDocumentoAnexoPeticionamentoRN::$TP_ESSENCIAL;
			
			$this->montarProtocoloDocumentoAnexo( $arrParametros, $objUnidadeDTO, $objProcedimentoDTO,
					$arrParticipantesParametro, $arrAnexoEssencialVinculacaoProcesso, $reciboDTOBasico );
			
		}
		
		if( isset( $arrParametros['hdnDocComplementar'] ) && $arrParametros['hdnDocComplementar']  != "" ) {
			
			$arrAnexoDocComplementar = $this->processarStringAnexos( $arrParametros['hdnDocComplementar'] ,
					$objUnidadeDTO->getNumIdUnidade() ,
					$strSiglaUsuario,
					false,
					$objProcedimentoDTO->getDblIdProcedimento()  );
			
			//$arrAnexoDocComplementar = AnexoINT::processarRI0872( $arrParametros['hdnDocComplementar'] );
			
			$arrLinhasAnexos = PaginaSEI::getInstance()->getArrItensTabelaDinamica(  $arrParametros['hdnDocComplementar']  );
			$contador = 0;
						
			foreach( $arrAnexoDocComplementar as $itemAnexoComplementar ){
				
				//================================
				//PROTOCOLO / DOCUMENTO DO ANEXO
				//=================================
				
				$idSerieAnexo = $arrLinhasAnexos[ $contador ][9];
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
				$objDocumentoDTO->setDblIdDocumento(null);
				$objDocumentoDTO->setDblIdProcedimento( $objProcedimentoDTO->getDblIdProcedimento() );
					
				$objProtocoloDTO = new ProtocoloDTO();
				$objProtocoloDTO->setDblIdProtocolo(null);
									
				$objDocumentoDTO->setDblIdDocumentoEdoc( null );
				$objDocumentoDTO->setDblIdDocumentoEdocBase( null );
				$objDocumentoDTO->setNumIdUnidadeResponsavel( SessaoSEI::getInstance()->getNumIdUnidadeAtual() );
				$objDocumentoDTO->setStrNumero(null);
				$objDocumentoDTO->setNumIdTipoConferencia( $idTipoConferencia );
				$objDocumentoDTO->setStrSinFormulario('N');
				$objDocumentoDTO->setStrSinBloqueado('N');
					
				$objDocumentoDTO->setStrStaEditor( null );
					
				$objDocumentoDTO->setNumVersaoLock(0);
					
				$arrObjUnidadeDTOReabertura = array();
				//se setar array da unidade pode cair na regra: "Unidade <nome-Unidade> não está sinalizada como protocolo."
				//nao esta fazendo reabertura de processo - trata-se de processo novo
				//$arrObjUnidadeDTOReabertura[] = $objUnidadeDTO;
				$objDocumentoDTO->setArrObjUnidadeDTO($arrObjUnidadeDTOReabertura);
					
				$objProtocoloDTO->setStrStaNivelAcessoLocal( $idNivelAcesso );
				$objProtocoloDTO->setNumIdHipoteseLegal( $idHipoteseLegal );
				$objProtocoloDTO->setStrStaGrauSigilo( $idGrauSigilo );
					
				$objProtocoloDTO->setStrDescricao('');
				$objProtocoloDTO->setDtaGeracao(InfraData::getStrDataAtual());
					
				//$arrAssuntos = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnAssuntos']);
				//ASSUNTOS
				$arrObjAssuntosDTO = array();
				$objProtocoloDTO->setArrObjRelProtocoloAssuntoDTO($arrObjAssuntosDTO);
					
				//INTERESSADOS
				$arrObjParticipantesDTO = array();
				$objProtocoloDTO->setArrObjParticipanteDTO($arrObjParticipantesDTO);
					
				//OBSERVACOES
				$objObservacaoDTO  = new ObservacaoDTO();
				$objObservacaoDTO->setStrDescricao('');
				$objProtocoloDTO->setArrObjObservacaoDTO(array($objObservacaoDTO));
					
				//ATRIBUTOS
				$arrRelProtocoloAtributo = AtributoINT::processarRI0691();
				$arrObjRelProtocoloAtributoDTO = array();
				for($x = 0;$x<count($arrRelProtocoloAtributo);$x++){
					$arrRelProtocoloAtributoDTO = new RelProtocoloAtributoDTO();
					$arrRelProtocoloAtributoDTO->setStrValor($arrRelProtocoloAtributo[$x]->getStrValor());
					$arrRelProtocoloAtributoDTO->setNumIdAtributo($arrRelProtocoloAtributo[$x]->getNumIdAtributo());
					$arrObjRelProtocoloAtributoDTO[$x] = $arrRelProtocoloAtributoDTO;
				}
				$objProtocoloDTO->setArrObjRelProtocoloAtributoDTO($arrObjRelProtocoloAtributoDTO);
					
				//ANEXOS
				$objProtocoloDTO->setArrObjAnexoDTO( array() );
				//$objProtocoloDTO->setArrObjAnexoDTO(AnexoINT::processarRI0872($_POST['hdnAnexos']));
					
				$objDocumentoDTO->setObjProtocoloDTO($objProtocoloDTO);
					
				$objDocumentoDTO->setNumIdTextoPadraoInterno('');
				$objDocumentoDTO->setStrProtocoloDocumentoTextoBase('');
				
				$objDocumentoDTO->setNumIdSerie( $idSerieAnexo );
				$objProtocoloDTO->setNumIdSerieDocumento( $idSerieAnexo );
				
				$objDocumentoDTO = $objDocumentoRN->receberRN0991($objDocumentoDTO);
				
				//========================
				//CRIANDO ANEXOS
				//========================
				$strTamanho = str_replace("","Kb", $itemAnexo->getNumTamanho() );
				$strTamanho = str_replace("","Mb", $strTamanho );
				$itemAnexoComplementar->setDblIdProtocolo( $objDocumentoDTO->getDblIdDocumento() );
				$itemAnexoComplementar->setNumIdUnidade( $objUnidadeDTO->getNumIdUnidade() );
				$itemAnexoComplementar->setNumTamanho( (int)$strTamanho );
				$itemAnexoComplementar->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
				$itemAnexoComplementar->setStrSinAtivo('S');
				$itemAnexoComplementar = $anexoRN->cadastrarRN0172( $itemAnexoComplementar );
				$arrAnexoComplementarVinculacaoProcesso[] = $itemAnexoComplementar;
				
				$this->assinarETravarDocumento( $objUnidadeDTO, $arrParametros, $objDocumentoDTO, $objProcedimentoDTO );
				
				$contador = $contador+1;
			}
			
			//cria o protocolo, cria o documento, e no documento aponta o procedimento (o processo)
			$arrParametros['CLASSIFICACAO_RECIBO'] = ReciboDocumentoAnexoPeticionamentoRN::$TP_COMPLEMENTAR;
			
			$this->montarProtocoloDocumentoAnexo( $arrParametros, $objUnidadeDTO, $objProcedimentoDTO,
					$arrParticipantesParametro, $arrAnexoComplementarVinculacaoProcesso, $reciboDTOBasico );
			
		}
						
	}
	
	private function montarProtocoloDocumentoAnexo( $arrParametros, $objUnidadeDTO, $objProcedimentoDTO, 
			                                        $arrParticipantesParametro, $arrAnexos, $reciboDTOBasico ){
					                                        	
	    $reciboAnexoRN = new ReciboDocumentoAnexoPeticionamentoRN();
	    $strClassificacao = $arrParametros['CLASSIFICACAO_RECIBO'];
	    
		foreach( $arrAnexos as $anexoDTOVinculado){	                                        	
			
			$anexoBD = new AnexoBD( $this->getObjInfraIBanco() );
			//$anexoDTOVinculado->setDblIdProtocolo( $documentoReciboDTO->getDblIdDocumento() );
			$anexoBD->alterar( $anexoDTOVinculado );
			
			$reciboAnexoDTO = new ReciboDocumentoAnexoPeticionamentoDTO();
			$reciboAnexoDTO->setNumIdAnexo( $anexoDTOVinculado->getNumIdAnexo() );
			$reciboAnexoDTO->setNumIdReciboPeticionamento( $reciboDTOBasico->getNumIdReciboPeticionamento() );
			$reciboAnexoDTO->setNumIdDocumento( null );
			$reciboAnexoDTO->setStrClassificacaoDocumento( $strClassificacao );				
			$reciboAnexoDTO = $reciboAnexoRN->cadastrar( $reciboAnexoDTO );
			
		}
		
	}
	
	private function montarDocumentoPrincipal( $objProcedimentoDTO, 
			                                   $objTipoProcDTO, 
			                                   $objUnidadeDTO, 
			                                   $arrParticipantesParametro, 
			                                   $arrParametros ){
		
		    $protocoloRN = new ProtocoloPeticionamentoRN();
			$numeroDocumento = $protocoloRN->gerarNumeracaoDocumento();
			
			$grauSigiloDocPrincipal = $arrParametros['grauSigiloDocPrincipal'];
			$hipoteseLegalDocPrincipal = $arrParametros['hipoteseLegalDocPrincipal'];
			
			//=============================================
			//MONTAGEM DO PROTOCOLODTO DO DOCUMENTO
			//=============================================
			
			$protocoloPrincipalDocumentoDTO = new ProtocoloDTO();
			
			$protocoloPrincipalDocumentoDTO->setDblIdProtocolo(null);
			$protocoloPrincipalDocumentoDTO->setStrDescricao( null );
			$protocoloPrincipalDocumentoDTO->setStrStaNivelAcessoLocal( ProtocoloRN::$NA_PUBLICO );
			$protocoloPrincipalDocumentoDTO->setStrProtocoloFormatado( $numeroDocumento );
			$protocoloPrincipalDocumentoDTO->setStrProtocoloFormatadoPesquisa( $numeroDocumento );
			$protocoloPrincipalDocumentoDTO->setNumIdUnidadeGeradora( $objUnidadeDTO->getNumIdUnidade() );
			$protocoloPrincipalDocumentoDTO->setNumIdUsuarioGerador( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
			$protocoloPrincipalDocumentoDTO->setStrStaProtocolo( ProtocoloRN::$TP_DOCUMENTO_GERADO );
			
			$protocoloPrincipalDocumentoDTO->setStrStaNivelAcessoLocal( $grauSigiloDocPrincipal );
			$protocoloPrincipalDocumentoDTO->setNumIdHipoteseLegal( $hipoteseLegalDocPrincipal );
			$protocoloPrincipalDocumentoDTO->setStrStaGrauSigilo('');
						
			$protocoloPrincipalDocumentoDTO->setDtaGeracao( InfraData::getStrDataAtual() );
			$protocoloPrincipalDocumentoDTO->setArrObjAnexoDTO(array());
			$protocoloPrincipalDocumentoDTO->setArrObjRelProtocoloAssuntoDTO(array());
			$protocoloPrincipalDocumentoDTO->setArrObjRelProtocoloProtocoloDTO(array());
			
			$protocoloPrincipalDocumentoDTO->setStrStaEstado( ProtocoloRN::$TE_NORMAL );
			$protocoloPrincipalDocumentoDTO->setStrStaArquivamento(ProtocoloRN::$TA_NAO_ARQUIVADO);
			$protocoloPrincipalDocumentoDTO->setNumIdLocalizador(null);
			$protocoloPrincipalDocumentoDTO->setNumIdUnidadeArquivamento(null);
			$protocoloPrincipalDocumentoDTO->setNumIdUsuarioArquivamento(null);
			$protocoloPrincipalDocumentoDTO->setDthArquivamento(null);
			$protocoloPrincipalDocumentoDTO->setArrObjObservacaoDTO( array() );
			$protocoloPrincipalDocumentoDTO->setArrObjParticipanteDTO( $arrParticipantesParametro );
			$protocoloPrincipalDocumentoDTO->setNumIdSerieDocumento( $objTipoProcDTO->getNumIdSerie() );

			//==========================
			//ATRIBUTOS
			//==========================
			
			$arrRelProtocoloAtributo = AtributoINT::processarRI0691();
			$arrObjRelProtocoloAtributoDTO = array();
			
			for($x = 0;$x<count($arrRelProtocoloAtributo);$x++){
				$arrRelProtocoloAtributoDTO = new RelProtocoloAtributoDTO();
				$arrRelProtocoloAtributoDTO->setStrValor($arrRelProtocoloAtributo[$x]->getStrValor());
				$arrRelProtocoloAtributoDTO->setNumIdAtributo($arrRelProtocoloAtributo[$x]->getNumIdAtributo());
				$arrObjRelProtocoloAtributoDTO[$x] = $arrRelProtocoloAtributoDTO;
			}
			
			$protocoloPrincipalDocumentoDTO->setArrObjRelProtocoloAtributoDTO($arrObjRelProtocoloAtributoDTO);
			
			//=============================================
			//MONTAGEM DO DOCUMENTODTO
			//=============================================
						
			//TESTE COMENTADO $documentoBD = new DocumentoBD( $this->getObjInfraIBanco() );
			$docRN = new DocumentoPeticionamentoRN();
			
			$documentoDTOPrincipal = new DocumentoDTO();
			$documentoDTOPrincipal->setDblIdDocumento( $protocoloPrincipalDocumentoDTO->getDblIdProtocolo() );
			$documentoDTOPrincipal->setDblIdProcedimento( $objProcedimentoDTO->getDblIdProcedimento() );
			$documentoDTOPrincipal->setNumIdSerie( $objTipoProcDTO->getNumIdSerie() );
			$documentoDTOPrincipal->setNumIdUnidadeResponsavel( $objUnidadeDTO->getNumIdUnidade() );
			$documentoDTOPrincipal->setObjProtocoloDTO( $protocoloPrincipalDocumentoDTO );
			
			//setando o conjunto de estilos mais atual ativo no documento
			$objConjuntoEstilosDTO = new ConjuntoEstilosDTO();
			$objConjuntoEstilosRN = new ConjuntoEstilosRN();
			$objConjuntoEstilosItemDTO = new ConjuntoEstilosItemDTO();
			$objConjuntoEstilosItemRN = new ConjuntoEstilosItemRN();
			$objConjuntoEstilosDTO->setStrSinUltimo('S');
			$objConjuntoEstilosDTO->retNumIdConjuntoEstilos();
			$objConjuntoEstilosDTO = $objConjuntoEstilosRN->consultar($objConjuntoEstilosDTO);			
			$documentoDTOPrincipal->setNumIdConjuntoEstilos( $objConjuntoEstilosDTO->getNumIdConjuntoEstilos() );
			
			$documentoDTOPrincipal->setNumIdTipoConferencia( null );
			$documentoDTOPrincipal->setStrNumero(''); //sistema atribui numeracao sequencial automatica						
			$documentoDTOPrincipal->setStrConteudo( $arrParametros['docPrincipalConteudoHTML'] );
			
			$documentoDTOPrincipal->setStrConteudoAssinatura(null);			
			$documentoDTOPrincipal->setStrCrcAssinatura(null);			
			$documentoDTOPrincipal->setStrQrCodeAssinatura(null);
			
			$documentoDTOPrincipal->setStrSinBloqueado('S');			
			$documentoDTOPrincipal->setStrStaEditor( EditorRN::$TE_INTERNO );			
			$documentoDTOPrincipal->setStrSinFormulario('N');			
			$documentoDTOPrincipal->setNumVersaoLock(0);
			
			$documentoDTOPrincipal->setNumIdTextoPadraoInterno(null);
			$documentoDTOPrincipal->setStrProtocoloDocumentoTextoBase('');
			
			$documentoDTOPrincipal = $docRN->gerarRN0003Customizado( $documentoDTOPrincipal );
			
			return $documentoDTOPrincipal;
		
	}
	
	private function assinarETravarDocumento( $objUnidadeDTO, $arrParametros, $documentoDTO, $objProcedimentoDTO ){
			
		    //consultar email da unidade (orgao)
		    $orgaoRN = new OrgaoRN();
			$orgaoDTO = new OrgaoDTO();
			$orgaoDTO->retTodos();
			$orgaoDTO->setNumIdOrgao( $objUnidadeDTO->getNumIdOrgao() );
			$orgaoDTO->setStrSinAtivo('S');
			$orgaoDTO = $orgaoRN->consultarRN1352($orgaoDTO);

			//consultar nome do cargao funcao selecionada na combo
			$cargoRN = new CargoRN();
			$cargoDTO = new CargoDTO();
			$cargoDTO->retTodos();
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
				$objParticipanteDTO->setStaParticipacao( ParticipanteRN::$TP_ACESSO_EXTERNO );
				$objParticipanteDTO->setNumIdUnidade( $objUnidadeDTO->getNumIdUnidade() );
				$objParticipanteDTO->setNumSequencia(0);
				
				$objParticipanteDTO = $objParticipanteRN->cadastrar( $objParticipanteDTO );
				$idParticipante = $objParticipanteDTO->getNumIdParticipante();
				
			} else {
				
				$idParticipante = $arrObjParticipanteDTO[0]->getNumIdParticipante();
			}
			
			$objAcessoExternoDTO->setStrEmailUnidade($orgaoDTO->getStrEmail() ); //informando o email do orgao associado a unidade
			$objAcessoExternoDTO->setDblIdDocumento( $documentoDTO->getDblIdDocumento() );
			$objAcessoExternoDTO->setNumIdParticipante( $idParticipante );
			$objAcessoExternoDTO->setNumIdUsuarioExterno( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
			$objAcessoExternoDTO->setStrSinProcesso('N'); //visualizacao integral do processo
			
			$objAcessoExternoRN = new AcessoExternoPeticionamentoRN();
			$objAcessoExternoDTO = $objAcessoExternoRN->cadastrar($objAcessoExternoDTO);
			
			//realmente assinando o documento depois da assinatura externa ser liberada
			$objAssinaturaDTO = new AssinaturaDTO();
			$objAssinaturaDTO->setStrStaFormaAutenticacao(AssinaturaRN::$TA_SENHA);
			$objAssinaturaDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
			$objAssinaturaDTO->setStrSenhaUsuario( $arrParametros['senhaSEI'] );
			$objAssinaturaDTO->setStrCargoFuncao( "Usuário Externo - " . $cargoDTO->getStrExpressao() );
			$objAssinaturaDTO->setArrObjDocumentoDTO(array($documentoDTO));
			
			$documentoRN = new DocumentoRN();
			$objAssinaturaDTO = $documentoRN->assinar($objAssinaturaDTO);

			//nao aplicando metodo alterar da RN de Documento por conta de regras de negocio muito especificas aplicadas ali
			$documentoBD = new DocumentoBD( $this->getObjInfraIBanco() );
			$documentoDTO->setStrSinBloqueado('S');
			$documentoBD->alterar( $documentoDTO );
			
			//remover a liberação de acesso externo //AcessoRN.excluir nao permite exclusao, por isso chame AcessoExternoBD diretamente daqui
			$objAcessoExternoBD = new AcessoExternoBD($this->getObjInfraIBanco());
			$objAcessoExternoBD->excluir( $objAcessoExternoDTO );
		
	}
	
	private function montarArrContatosInteressados( $idsContatos ){
		
		$contatoRN = new ContatoRN();
		$objContatoDTO = new ContatoDTO();
		$objContatoDTO->retStrSigla();
		$objContatoDTO->retStrNome();
		$objContatoDTO->retNumIdContato();
		
		$objContatoDTO->adicionarCriterio(array('IdContato', 'SinAtivo'),
				array(InfraDTO::$OPER_IN, InfraDTO::$OPER_IGUAL),
				array( $idsContatos,'S'),
				InfraDTO::$OPER_LOGICO_AND);
		
		$arrContatos = $contatoRN->listarRN0325( $objContatoDTO );
		return $arrContatos;
	}
	
	//TODO: Avaliar a necessidade de registrar os dados do remetente como participante do processo
	private function atribuirRemetente(ProtocoloDTO $objProtocoloDTO, $objRemetente)
	{
		$arrObjParticipantesDTO = array();
		if($objProtocoloDTO->isSetArrObjParticipanteDTO()) {
			$arrObjParticipantesDTO = $objProtocoloDTO->getArrObjParticipanteDTO();
		}
	
		//Obtenção de detalhes do remetente na infraestrutura do PEN
		$objEstruturaDTO = $this->objProcessoEletronicoRN->consultarEstrutura(
				$objRemetente->identificacaoDoRepositorioDeEstruturas,
				$objRemetente->numeroDeIdentificacaoDaEstrutura);
	
		if(!empty($objEstruturaDTO)) {
			$objParticipanteDTO  = new ParticipanteDTO();
			$objParticipanteDTO->setStrSiglaContato($objEstruturaDTO->getStrSigla());
			$objParticipanteDTO->setStrNomeContato($objEstruturaDTO->getStrNome());
			$objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_REMETENTE);
			$objParticipanteDTO->setNumSequencia(0);
			$arrObjParticipantesDTO[] = $objParticipanteDTO;
			$arrObjParticipantesDTO = $this->prepararParticipantes($arrObjParticipantesDTO);
		}
	
		$objProtocoloDTO->setArrObjParticipanteDTO($arrObjParticipantesDTO);
	}
	
	private function atribuirParticipantes(ProtocoloDTO $objProtocoloDTO, $arrObjInteressados)
	{		
		
		$arrObjParticipantesDTO = array();
		
		if($objProtocoloDTO->isSetArrObjParticipanteDTO()) {
			$arrObjParticipantesDTO = $objProtocoloDTO->getArrObjParticipanteDTO();
		}
	
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
		
		//obter objeto Contato do usuario logado
		$idUsuarioExterno = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();
		
		$objUsuarioDTO = new UsuarioDTO();
		$objUsuarioDTO->retTodos();
		$objUsuarioDTO->setNumIdUsuario( $idUsuarioExterno );
		$objUsuarioRN = new UsuarioRN();
		$objUsuarioDTO = $objUsuarioRN->consultarRN0489( $objUsuarioDTO );
		$idContatoUsuario = $objUsuarioDTO->getNumIdContato();
		
		$contatoRN = new ContatoRN();
		$contatoDTO = new ContatoDTO();
		$contatoDTO->retTodos();
		$contatoDTO->setNumIdContato( $idContatoUsuario );
		$contatoDTO = $contatoRN->consultarRN0324( $contatoDTO );
		
		$objParticipanteDTO  = new ParticipanteDTO();
		$objParticipanteDTO->setStrSiglaContato($contatoDTO->getStrSigla());
		$objParticipanteDTO->setStrNomeContato($contatoDTO->getStrNome());
		$objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_REMETENTE);
		
		if( $i == 0) {
		    $objParticipanteDTO->setNumSequencia($i);
		} else if( $i > 0 ){
			$objParticipanteDTO->setNumSequencia($i++);
		}
		
		$arrObjParticipantesDTO[] = $objParticipanteDTO;
		
		$arrObjParticipanteDTO = $this->prepararParticipantes($arrObjParticipantesDTO);
		$objProtocoloDTO->setArrObjParticipanteDTO($arrObjParticipantesDTO);
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
	
	private function enviarProcedimentoUnidade(ProcedimentoDTO $parObjProcedimentoDTO)
	{
		$objAtividadeRN = new AtividadePeticionamentoRN();
		$objInfraException = new InfraException();
	
		if(!$parObjProcedimentoDTO->isSetArrObjUnidadeDTO() || count($parObjProcedimentoDTO->getArrObjUnidadeDTO()) == 0) {
			$objInfraException->lancarValidacao('Unidade de destino do processo não informada.');
		}
	
		$arrObjUnidadeDTO = $parObjProcedimentoDTO->getArrObjUnidadeDTO();
		
		$arrObjUnidadeDTO = array_values($parObjProcedimentoDTO->getArrObjUnidadeDTO());
		$objUnidadeDTO = $arrObjUnidadeDTO[0];
	
		$objProcedimentoDTO = new ProcedimentoDTO();
		$objProcedimentoDTO->retDblIdProcedimento();
		$objProcedimentoDTO->retNumIdTipoProcedimento();
		$objProcedimentoDTO->retStrProtocoloProcedimentoFormatado();
		$objProcedimentoDTO->retNumIdTipoProcedimento();
		$objProcedimentoDTO->retStrNomeTipoProcedimento();
		$objProcedimentoDTO->retStrStaNivelAcessoGlobalProtocolo();
		$objProcedimentoDTO->setStrProtocoloProcedimentoFormatado($parObjProcedimentoDTO->getStrProtocoloProcedimentoFormatado());
	
		$objProcedimentoRN = new ProcedimentoRN();
		$objProcedimentoDTO = $objProcedimentoRN->consultarRN0201($objProcedimentoDTO);
		
		//PETICIONAMENTO NAO VAI ACEITAR NIVEL SIGILOSO
		//if ($objProcedimentoDTO == null || $objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo()==ProtocoloRN::$NA_SIGILOSO) {
			//$objInfraException->lancarValidacao('Processo ['.$parObjProcedimentoDTO->getStrProtocoloProcedimentoFormatado().'] não encontrado.');
		//}
	
		if ($objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo()==ProtocoloRN::$NA_RESTRITO) {
			$objAcessoDTO = new AcessoDTO();
			$objAcessoDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());
			$objAcessoDTO->setNumIdUnidade($objUnidadeDTO->getNumIdUnidade());
	
			$objAcessoRN = new AcessoRN();
			if ($objAcessoRN->contar($objAcessoDTO)==0) {
				$objInfraException->adicionarValidacao('Unidade ['.$objUnidadeDTO->getStrSigla().'] não possui acesso ao processo ['.$objProcedimentoDTO->getStrProtocoloProcedimentoFormatado().'].');
			}
		}
	
		$objPesquisaPendenciaDTO = new PesquisaPendenciaDTO();
		$objPesquisaPendenciaDTO->setDblIdProtocolo(array($objProcedimentoDTO->getDblIdProcedimento()));
		$objPesquisaPendenciaDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
		
		$objPesquisaPendenciaDTO->setNumIdUnidade( $objUnidadeDTO->getNumIdUnidade() );
			
		$objPenAtividadeRN = new AtividadePeticionamentoRN();
		$arrObjProcedimentoDTO = $objAtividadeRN->listarPendenciasRN0754($objPesquisaPendenciaDTO);
	
		$objInfraException->lancarValidacoes();	
	
		$objEnviarProcessoDTO = new EnviarProcessoDTO();
		$objEnviarProcessoDTO->setArrAtividadesOrigem($arrObjProcedimentoDTO[0]->getArrObjAtividadeDTO());
		
		$objAtividadeDTO = new AtividadeDTO();
		$objAtividadeDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());
		$objAtividadeDTO->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() ); //TODO precisa setar esse atributo da atividade?
		$objAtividadeDTO->setNumIdUsuarioOrigem(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
		$objAtividadeDTO->setNumIdUnidade($objUnidadeDTO->getNumIdUnidade());
		$objAtividadeDTO->setNumIdUnidadeOrigem( $objUnidadeDTO->getNumIdUnidade() );
		$objEnviarProcessoDTO->setArrAtividades(array($objAtividadeDTO));
	
		$objEnviarProcessoDTO->setStrSinManterAberto('N');
		
		$strEnviaEmailNotificacao = 'N';
		$objEnviarProcessoDTO->setStrSinEnviarEmailNotificacao($strEnviaEmailNotificacao);
		$objEnviarProcessoDTO->setStrSinRemoverAnotacoes('S');
		$objEnviarProcessoDTO->setDtaPrazo(null);
		$objEnviarProcessoDTO->setNumDias(null);
		$objEnviarProcessoDTO->setStrSinDiasUteis('N');
	
		$objAtividadeRN->enviarRN0023Customizado($objEnviarProcessoDTO);
	
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
		
		//echo $numIdTipoProcedimento; die();
		
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
	public function processarStringAnexos($strDelimitadaAnexos, $idUnidade, $strSiglaUsuario, $bolDocumentoPrincipal, $idProtocolo){
		
		$arrAnexos = array();
				
		$arrAnexos = PaginaSEI::getInstance()->getArrItensTabelaDinamica($strDelimitadaAnexos);
		$arrObjAnexoDTO = array();
		
		foreach($arrAnexos as $anexo){
			
			$objAnexoDTO = new AnexoDTO();
			$objAnexoDTO->setNumIdAnexo( null );
			$objAnexoDTO->setStrSinAtivo('S');
			$objAnexoDTO->setStrNome($anexo[8]);
			$objAnexoDTO->setDthInclusao($anexo[1]);
			$objAnexoDTO->setNumTamanho($anexo[2]);
			$objAnexoDTO->setStrSiglaUsuario( $strSiglaUsuario );
			$objAnexoDTO->setStrSiglaUnidade( $idUnidade );
			$objAnexoDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
			$arrObjAnexoDTO[] = $objAnexoDTO;
		}
		
		return $arrObjAnexoDTO;
	}
}
?>