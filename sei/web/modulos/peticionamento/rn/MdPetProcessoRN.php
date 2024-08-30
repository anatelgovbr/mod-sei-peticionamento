<?
/**
* ANATEL
*
* 28/06/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetProcessoRN extends InfraRN {

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

		//alteracoes SEIv3
		$objUsuarioDTO = new UsuarioDTO();
		$objUsuarioDTO->retNumIdUsuario();
		$objUsuarioDTO->retStrSigla();
		$objUsuarioDTO->retStrSenha();
		$objUsuarioDTO->setStrSigla( SessaoSEIExterna::getInstance()->getStrSiglaUsuarioExterno() );

		$objUsuarioRN = new UsuarioRN();
		$objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);
		$senhaBanco=$objUsuarioDTO->getStrSenha();
		$bcrypt = new InfraBcrypt();

		$stringSenha = base64_decode($arrParametros['pwdsenhaSEI']);

		if (!$bcrypt->verificar($stringSenha,$senhaBanco)) {
			$objInfraException->adicionarValidacao("Senha inválida.");
			$objInfraException->lancarValidacoes();
		} 

	}

    protected function gerarProcedimentoControlado( $arrParametros )
    {
        FeedSEIProtocolos::getInstance()->setBolAcumularFeeds(true);

        $retorno = $this->gerarProcedimentoInterno($arrParametros);

        FeedSEIProtocolos::getInstance()->setBolAcumularFeeds(false);
        FeedSEIProtocolos::getInstance()->indexarFeeds();

        try {
            $emailMdPetEmailNotificacaoRN = new MdPetEmailNotificacaoRN();
            $emailMdPetEmailNotificacaoRN->notificaoPeticionamentoExterno( $retorno['parametrosEmail'] );
        } catch( Exception $exEmail ){}

        return $retorno['paramentrosRecibo'];
    }

	protected function gerarProcedimentoInterno( $arrParametros ){
		try {
			
			$contatoDTOUsuarioLogado = $this->getContatoDTOUsuarioLogado();
			
			//Remententes 
			$idsRemententes = array();
			$idsRemententes[] = $contatoDTOUsuarioLogado->getNumIdContato();
			
			$idTipoProc = $arrParametros['id_tipo_procedimento'];
			$objMdPetTipoProcessoDTO = new MdPetTipoProcessoDTO();
			$objMdPetTipoProcessoDTO->retTodos(true);
			$objMdPetTipoProcessoDTO->setNumIdTipoProcessoPeticionamento( $idTipoProc );
			$objTipoProcRN = new MdPetTipoProcessoRN();
			$objMdPetTipoProcessoDTO = $objTipoProcRN->listar( $objMdPetTipoProcessoDTO );
			$txtTipoProcessoEscolhido = $objMdPetTipoProcessoDTO[0]->getStrNomeProcesso();

			//=============================================================================================================
			//obtendo a unidade do tipo de processo selecionado - Pac 10 - pode ser uma ou MULTIPLAS unidades selecionadas
			//=============================================================================================================
			$objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
			$objMdPetRelTpProcessoUnidDTO->retTodos();
			$objMdPetRelTpProcessoUnidRN = new MdPetRelTpProcessoUnidRN();
			$objMdPetRelTpProcessoUnidDTO->setNumIdTipoProcessoPeticionamento( $idTipoProc );
			$arrMdPetRelTpProcessoUnidDTO = $objMdPetRelTpProcessoUnidRN->listar( $objMdPetRelTpProcessoUnidDTO );

			$arrUnidadeUFDTO = null;
			$idUnidadeTipoProcesso = null;
			
			//=====================================================
			//TIPO DE PROCESSADO CONFIGURADO COM APENAS UMA UNIDADE
			//=====================================================
			if( $arrMdPetRelTpProcessoUnidDTO != null && count( $arrMdPetRelTpProcessoUnidDTO ) == 1 ) {
				$idUnidade = $arrMdPetRelTpProcessoUnidDTO[0]->getNumIdUnidade();
			}
			
			//========================================================================================================================
			//TIPO DE PROCESSO CONFIGURADO COM MULTIPLAS UNIDADES -> pegar a unidade a partir da UF selecionada pelo usuario na combo
			//========================================================================================================================
			else if( $arrMdPetRelTpProcessoUnidDTO != null && count( $arrMdPetRelTpProcessoUnidDTO ) > 1 ){		
				$idUnidade = $arrParametros['hdnIdUnidadeMultiplaSelecionada'];
			}
						
			//obter unidade configurada no "Tipo de Processo para peticionamento"
			$unidadeRN = new UnidadeRN();
			$unidadeDTO = new UnidadeDTO();
			$unidadeDTO->retTodos();
			$unidadeDTO->setNumIdUnidade( $idUnidade );
			$unidadeDTO = $unidadeRN->consultarRN0125( $unidadeDTO );				

			if( $objMdPetTipoProcessoDTO[0]->getStrSinIIProprioUsuarioExterno() == 'S' ){

				$arrParametros['hdnListaInteressados'] = $contatoDTOUsuarioLogado->getNumIdContato();

				// Interessados
				$idsContatos = array();
				$idsContatos[] = $arrParametros['hdnListaInteressados'];
				$idsContatos = array_merge($idsRemententes, $idsContatos);

			}

			//verificar se esta vindo o array de participantes
			//participantes selecionados via pop up OU indicados diretamente por CPF/CNPJ
			else if( $objMdPetTipoProcessoDTO[0]->getStrSinIIProprioUsuarioExterno() == 'N' && 
				isset( $arrParametros['hdnListaInteressados'] ) && 
				$arrParametros['hdnListaInteressados'] != "" ){			
				
				$arrContatosInteressados = array();
				
				if (strpos( $arrParametros['hdnListaInteressados'] , ',') !== false) {
					// Interessados
					$idsContatos = explode(",", $arrParametros['hdnListaInteressados']);
				} else {
					// Interessados				
					$idsContatos = array();
					$idsContatos[] = $arrParametros['hdnListaInteressados'];
				}

			} 

			$idsContatos = array_unique($idsContatos);

			$arrInteressados = array();
			foreach ($idsContatos as $contato){
			    $objContatoDTO = new ContatoDTO();
			    $objContatoDTO->setNumIdContato($contato);
                $objContatoDTO->retTodos(true);
			    $objContato = (new ContatoRN())->consultarRN0324($objContatoDTO);
			    $objParticipanteContato = new ContatoAPI();
                $objParticipanteContato->setIdContato($objContato->getNumIdContato());
                $objParticipanteContato->setSigla($objContato->getStrSigla());
                $objParticipanteContato->setNome($objContato->getStrNome());
			    array_push($arrInteressados, $objParticipanteContato);
            }

			//Gera um processo
			$objProcedimentoAPI = new ProcedimentoAPI();
			$objProcedimentoAPI->setIdTipoProcedimento( $objMdPetTipoProcessoDTO[0]->getNumIdProcedimento() );
			$objProcedimentoAPI->setIdUnidadeGeradora( $unidadeDTO->getNumIdUnidade() );
			$objProcedimentoAPI->setEspecificacao( $arrParametros['txtEspecificacaoDocPrincipal'] );
			$objProcedimentoAPI->setNumeroProtocolo('');
            $objProcedimentoAPI->setInteressados($arrInteressados);

            $objEntradaGerarProcedimentoAPI = new EntradaGerarProcedimentoAPI();
            $objEntradaGerarProcedimentoAPI->setProcedimento($objProcedimentoAPI);

            $objSeiRN = new SeiRN();
            SessaoSEI::getInstance()->simularLogin(null, null , SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() , $unidadeDTO->getNumIdUnidade() );
			
            // Tipo de Procedimento - Nivel de Acesso e Hipótese Legal permitidos
            $objNivelAcessoPermitidoDTO = new NivelAcessoPermitidoDTO();
            $objNivelAcessoPermitidoDTO->retStrStaNivelAcesso();
            $objNivelAcessoPermitidoDTO->setNumIdTipoProcedimento($objMdPetTipoProcessoDTO[0]->getNumIdProcedimento());

            $objNivelAcessoPermitidoRN = new NivelAcessoPermitidoRN();
            $arrObjNivelAcessoPermitidoDTO = $objNivelAcessoPermitidoRN->listar($objNivelAcessoPermitidoDTO);

            $strSinSigilosoPermitido = 'N';
            $strSinRestritoPermitido = 'N';
            $strSinPublicoPermitido  = 'N';
            foreach($arrObjNivelAcessoPermitidoDTO as $objNivelAcessoPermitidoDTO){
                if ($objNivelAcessoPermitidoDTO->getStrStaNivelAcesso()==ProtocoloRN::$NA_SIGILOSO){
                    $strSinSigilosoPermitido = 'S';
                }else if ($objNivelAcessoPermitidoDTO->getStrStaNivelAcesso()==ProtocoloRN::$NA_RESTRITO){
                    $strSinRestritoPermitido = 'S'; 
                }else if ($objNivelAcessoPermitidoDTO->getStrStaNivelAcesso()==ProtocoloRN::$NA_PUBLICO){
                    $strSinPublicoPermitido = 'S';
                }
			}

            if ($strSinPublicoPermitido=='S'){
                $objProcedimentoAPI->setNivelAcesso( ProtocoloRN::$NA_PUBLICO );
                $objProcedimentoAPI->setIdHipoteseLegal( null );
            }else{
                // Documento Principal - determina Nivel Acesso e Hipótese Legal do Procedimento ação
                $arrLinhasAnexos = PaginaSEI::getInstance()->getArrItensTabelaDinamica(  $arrParametros['hdnDocPrincipal']  );
                $contador = 0;

                if (count($arrLinhasAnexos)){
                    foreach( $arrLinhasAnexos as $itemAnexo ){
                        if( $arrLinhasAnexos[ $contador ][4] == "Restrito" && $strSinRestritoPermitido=='S'){
                             $objProcedimentoAPI->setNivelAcesso( ProtocoloRN::$NA_RESTRITO );
                             if ( !empty($arrLinhasAnexos[ $contador ][5]) ){
                                  $objProcedimentoAPI->setIdHipoteseLegal( $arrLinhasAnexos[ $contador ][5] );  
                             }
                        }
                        $idGrauSigilo = null;
                     }
                }
            }

            if ( is_null($objProcedimentoAPI->getNivelAcesso()) ){
                $objInfraException = new InfraException();
                $objInfraException->adicionarValidacao('Nível de Acesso não definido');
                $objInfraException->lancarValidacoes();
            }
			
			$objSaidaGerarProcedimentoAPI = new SaidaGerarProcedimentoAPI();
			$objSaidaGerarProcedimentoAPI = $objSeiRN->gerarProcedimento($objEntradaGerarProcedimentoAPI);
			
			//seiv3
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
			$objMdPetReciboRN = new MdPetReciboRN();
			$reciboDTOBasico = $objMdPetReciboRN->gerarReciboSimplificado( $objSaidaGerarProcedimentoAPI->getIdProcedimento() );

			$objEntradaConsultarProcedimentoAPI = new EntradaConsultarProcedimentoAPI();
			$objEntradaConsultarProcedimentoAPI->setIdProcedimento( $objSaidaGerarProcedimentoAPI->getIdProcedimento() );
			$objSaidaConsultarProcedimentoAPI = $objSeiRN->consultarProcedimento( $objEntradaConsultarProcedimentoAPI );
			
			$nomeTipo = $objSaidaConsultarProcedimentoAPI->getTipoProcedimento()->getNome();
			
			$objProcedimentoDTO = new ProcedimentoDTO();
			$objProcedimentoDTO->setStrNomeTipoProcedimento( $nomeTipo );
			$objProcedimentoDTO->setDblIdProcedimento( $objSaidaGerarProcedimentoAPI->getIdProcedimento() );
			$objProcedimentoDTO->setStrProtocoloProcedimentoFormatado( $objSaidaConsultarProcedimentoAPI->getProcedimentoFormatado()  );
			$objProcedimentoDTO->setNumIdTipoProcedimento( $objSaidaConsultarProcedimentoAPI->getTipoProcedimento()->getIdTipoProcedimento()  );
			
			$this->montarArrDocumentos( $arrParametros, $unidadeDTO, $objProcedimentoDTO, $reciboDTOBasico );
			
			$arrParams = array();
			$arrParams[0] = $arrParametros;
			$arrParams[1] = $unidadeDTO;
			$arrParams[2] = $objProcedimentoDTO;
			$arrParams[4] = $reciboDTOBasico;
			// variavel flag para retorno do IdDocumento
			$arrParams[5] = true;

			$reciboGerado = $objMdPetReciboRN->montarRecibo( $arrParams );

			$arrProcessoReciboRetorno = array();
			$arrProcessoReciboRetorno[0] = $reciboDTOBasico;
			$arrProcessoReciboRetorno[1] = $objProcedimentoDTO;

			//enviando email de sistema EU 5155  / 5156 - try catch por causa que em localhost o envio de email gera erro

			
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
			// Andamento Processo NOVO
			$objMdPetReciboDTO = new MdPetReciboDTO();
			$objMdPetReciboDTO->retStrNumeroProcessoFormatadoDoc();
			$objMdPetReciboDTO->retNumIdProtocolo();
			$objMdPetReciboDTO->setDblIdDocumento($reciboGerado->getDblIdDocumento());

			$objMdPetReciboRN = new MdPetReciboRN();

			$objMdPetRecibo = $objMdPetReciboRN->consultar($objMdPetReciboDTO);

			$arrParametrosResp['idDocumento']= $reciboGerado->getDblIdDocumento();
			$arrParametrosResp['idProcedimento']= $objMdPetRecibo->getNumIdProtocolo();
			$arrParametrosResp['nomeTipoResposta']= MdPetIntDestRespostaRN::$TIPO_PROCESSO_NOVO;
			$arrParametrosResp['nomeDocumentoPrincipal']=$objMdPetRecibo->getStrNumeroProcessoFormatadoDoc();

			$objMdPetIntDestRespostaRN = new MdPetIntDestRespostaRN();
			$objMdPetIntDestRespostaRN->lancarAndamentoRecibo($arrParametrosResp);

			$this->_controlarAcessoExterno($objProcedimentoDTO->getDblIdProcedimento());

			// Andamento - Processo remetido pela unidade
    		$arrObjAtributoAndamentoDTO = array();
    		$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
    		$objAtributoAndamentoDTO->setStrNome('UNIDADE');
    		$objAtributoAndamentoDTO->setStrValor($unidadeDTO->getStrSigla().'¥'.$unidadeDTO->getStrDescricao());
    		$objAtributoAndamentoDTO->setStrIdOrigem($unidadeDTO->getNumIdUnidade());
    		$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;

    		$objAtividadeDTO = new AtividadeDTO();
    		$objAtividadeDTO->setDblIdProtocolo( $objProcedimentoDTO->getDblIdProcedimento() );
    		$objAtividadeDTO->setNumIdUnidade( $unidadeDTO->getNumIdUnidade() );
    		$objAtividadeDTO->setNumIdUnidadeOrigem( $unidadeDTO->getNumIdUnidade() );
    		$objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO);
    		$objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_PROCESSO_REMETIDO_UNIDADE);

    		$objAtividadeRN = new AtividadeRN();
    		$objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);      


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


			return array('paramentrosRecibo' => $arrProcessoReciboRetorno, 'parametrosEmail' => $arrParams);
		
		} catch(Exception $e){
			throw new InfraException('Erro cadastrando processo peticionamento do SEI.',$e);
		}
		
	}
	
	private function _controlarAcessoExterno($idProcedimento){

		if(!is_null($idProcedimento)) {
			$objMdPetAcessoExternoRN = new MdPetAcessoExternoRN();
			$objMdPetAcessoExternoRN->aplicarRegrasGeraisAcessoExterno($idProcedimento,  MdPetAcessoExternoRN::$MD_PET_PROCESSO_NOVO);
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
		$this->assinarETravarDocumentoProcesso( $objUnidadeDTO, $arrParametros, $docDTO, $objProcedimentoDTO );
		
		//adiciona o doc no recibo pesquisavel
		//recibo do doc principal para consultar do usuario externo
		$objMdPetRelReciboDocumentoAnexoDTO = new MdPetRelReciboDocumentoAnexoDTO();
		$objMdPetRelReciboDocumentoAnexoRN = new MdPetRelReciboDocumentoAnexoRN();

		$objMdPetRelReciboDocumentoAnexoDTO->setNumIdAnexo( null );
		$objMdPetRelReciboDocumentoAnexoDTO->setNumIdReciboPeticionamento( $reciboDTOBasico->getNumIdReciboPeticionamento() );
		$objMdPetRelReciboDocumentoAnexoDTO->setNumIdDocumento( $idDocumentoAnexo );
		$objMdPetRelReciboDocumentoAnexoDTO->setStrClassificacaoDocumento( $tipoDocRecibo );
		$objMdPetRelReciboDocumentoAnexoDTO = $objMdPetRelReciboDocumentoAnexoRN->cadastrar( $objMdPetRelReciboDocumentoAnexoDTO );

		return $saidaDocExternoAPI;
		
	}

	private function montarArrDocumentos( $arrParametros , $objUnidadeDTO , 
			                              $objProcedimentoDTO , $reciboDTOBasico ){

		LimiteSEI::getInstance()->configurarNivel3();

		$versaoPeticionamento = intval(preg_replace("/\D/", "", (new InfraParametro(BancoSEI::getInstance()))->getValor('VERSAO_MODULO_PETICIONAMENTO', false)));

		//tentando simular sessao de usuario interno do SEI
		SessaoSEI::getInstance()->setNumIdUnidadeAtual( $objUnidadeDTO->getNumIdUnidade() );
		SessaoSEI::getInstance()->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );

		$arrDocumentoDTO = array();

		//verificar se foi editado documento principal gerado pelo editor do SEI
		if( isset( $arrParametros['docPrincipalConteudoHTML'] ) && $arrParametros['docPrincipalConteudoHTML'] != ""  ){
						
			$idTipoProc = $arrParametros['id_tipo_procedimento'];
			$objMdPetTipoProcessoDTO = new MdPetTipoProcessoDTO();
			$objMdPetTipoProcessoDTO->retTodos(true);
			$objMdPetTipoProcessoDTO->setNumIdTipoProcessoPeticionamento( $idTipoProc );
			$objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
			$objMdPetTipoProcessoDTO = $objMdPetTipoProcessoRN->listar( $objMdPetTipoProcessoDTO );
						
			//====================================
			//gera no sistema as informações referentes ao documento principal
			//====================================
			//seiv3
			$documentoDTOPrincipal = $this->montarDocumentoPrincipal( $objProcedimentoDTO, 
					                          $objMdPetTipoProcessoDTO, $objUnidadeDTO, $arrParametros );

			//====================================
			//ASSINAR O DOCUMENTO PRINCIPAL
			//====================================			
			$this->assinarETravarDocumentoProcesso( $objUnidadeDTO, $arrParametros, $documentoDTOPrincipal, $objProcedimentoDTO );
			
			//recibo do doc principal para consultar do usuario externo
			$reciboDocAnexoDTO = new MdPetRelReciboDocumentoAnexoDTO();
			$objMdPetRelReciboDocumentoAnexoRN = new MdPetRelReciboDocumentoAnexoRN();
			
			$reciboDocAnexoDTO->setNumIdAnexo( null );
			$reciboDocAnexoDTO->setNumIdReciboPeticionamento( $reciboDTOBasico->getNumIdReciboPeticionamento() );
			$reciboDocAnexoDTO->setNumIdDocumento( $documentoDTOPrincipal->getDblIdDocumento() );
			$reciboDocAnexoDTO->setStrClassificacaoDocumento( MdPetRelReciboDocumentoAnexoRN::$TP_PRINCIPAL );
			$reciboDocAnexoDTO = $objMdPetRelReciboDocumentoAnexoRN->cadastrar( $reciboDocAnexoDTO );

		} 

		//verificar se o documento principal é do tipo externo (ANEXO)
		else {
			
			$idTipoProc = $arrParametros['id_tipo_procedimento'];
			$objMdPetTipoProcessoDTO = new MdPetTipoProcessoDTO();
			$objMdPetTipoProcessoDTO->retTodos(true);
			$objMdPetTipoProcessoDTO->setNumIdTipoProcessoPeticionamento( $idTipoProc );
			$objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
			$objMdPetTipoProcessoDTO = $objMdPetTipoProcessoRN->listar( $objMdPetTipoProcessoDTO );
			
		}
				
		//tratando documentos essenciais e complementares
		$anexoRN = new MdPetAnexoRN();
		$strSiglaUsuario = SessaoSEIExterna::getInstance()->getStrSiglaUsuarioExterno();
		
		$objMdPetTamanhoArquivoRN = new MdPetTamanhoArquivoRN();
		$objMdPetTamanhoArquivoDTO = new MdPetTamanhoArquivoDTO();
		$objMdPetTamanhoArquivoDTO->setStrSinAtivo('S');
		$objMdPetTamanhoArquivoDTO->retTodos();
		
		$arrTamanhoDTO = $objMdPetTamanhoArquivoRN->listarTamanhoMaximoConfiguradoParaUsuarioExterno( $objMdPetTamanhoArquivoDTO );
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

                if($versaoPeticionamento >= 410){
                    $nivelAcessoDoc = MdPetForcarNivelAcessoDocINT::getDadosForcarNivelAcessoDoc('N');
                    if(!empty($nivelAcessoDoc) && is_array($nivelAcessoDoc['documentos']) && count($nivelAcessoDoc['documentos']) > 0){
                        if(in_array($arrLinhasAnexos[ $contador ][9], $nivelAcessoDoc['documentos'])){
                            $idNivelAcesso = $nivelAcessoDoc['nivel'];
                            $idHipoteseLegal = $nivelAcessoDoc['hipotese'];
                        }
                    }
                }
								
				$idGrauSigilo = null;
				
				//criando registro em protocolo
				$objDocumentoDTO = new DocumentoDTO();
				$objDocumentoDTO->setStrNomeArvore( $strComplemento );
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

				$objDocumentoDTO->setNumIdTextoPadraoInterno('');
				$objDocumentoDTO->setStrProtocoloDocumentoTextoBase('');
				$objDocumentoDTO->setNumIdSerie( $idSerieAnexo );
				$objDocumentoDTO->setStrStaDocumento(DocumentoRN::$TD_EXTERNO);

				$objSaidaDocumentoAPI = $this->gerarAssinarDocumentoAnexoSeiRN( $objUnidadeDTO, $arrParametros, $objDocumentoDTO, $objProcedimentoDTO, $itemAnexo, $reciboDTOBasico, MdPetRelReciboDocumentoAnexoRN::$TP_PRINCIPAL );

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

                if($versaoPeticionamento >= 410){
                    $nivelAcessoDoc = MdPetForcarNivelAcessoDocINT::getDadosForcarNivelAcessoDoc('N');
                    if(!empty($nivelAcessoDoc) && is_array($nivelAcessoDoc['documentos']) && count($nivelAcessoDoc['documentos']) > 0){
                        if(in_array($arrLinhasAnexos[ $contador ][9], $nivelAcessoDoc['documentos'])){
                            $idNivelAcesso = $nivelAcessoDoc['nivel'];
                            $idHipoteseLegal = $nivelAcessoDoc['hipotese'];
                        }
                    }
                }
								
				$idGrauSigilo = null;
					
				//criando registro em protocolo
				$objDocumentoDTO = new DocumentoDTO();
				$objDocumentoDTO->setStrNomeArvore( $strComplemento );
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

				$objDocumentoDTO->setNumIdTextoPadraoInterno('');
				$objDocumentoDTO->setStrProtocoloDocumentoTextoBase('');				
				$objDocumentoDTO->setNumIdSerie( $idSerieAnexo );
				
				$objSaidaDocumentoAPI = $this->gerarAssinarDocumentoAnexoSeiRN( $objUnidadeDTO, $arrParametros, $objDocumentoDTO, $objProcedimentoDTO, $itemAnexo, $reciboDTOBasico, MdPetRelReciboDocumentoAnexoRN::$TP_ESSENCIAL );

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

				$arrAnexoEssencialVinculacaoProcesso[] = $itemAnexo; 
				$arrIdAnexoEssencial[] = $idDocumentoAnexo;
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

				if($versaoPeticionamento >= 410){
                    $nivelAcessoDoc = MdPetForcarNivelAcessoDocINT::getDadosForcarNivelAcessoDoc('N');
                    if(!empty($nivelAcessoDoc) && is_array($nivelAcessoDoc['documentos']) && count($nivelAcessoDoc['documentos']) > 0){
                        if(in_array($arrLinhasAnexos[ $contador ][9], $nivelAcessoDoc['documentos'])){
                            $idNivelAcesso = $nivelAcessoDoc['nivel'];
                            $idHipoteseLegal = $nivelAcessoDoc['hipotese'];
                        }
                    }
                }
				
				$idGrauSigilo = null;
					
				//criando registro em protocolo
				$objDocumentoDTO = new DocumentoDTO();
				$objDocumentoDTO->setStrNomeArvore( $strComplemento );
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

				$objDocumentoDTO->setNumIdTextoPadraoInterno('');
				$objDocumentoDTO->setStrProtocoloDocumentoTextoBase('');
				$objDocumentoDTO->setNumIdSerie( $idSerieAnexo );

				$objSaidaDocumentoAPI = $this->gerarAssinarDocumentoAnexoSeiRN( $objUnidadeDTO, $arrParametros, $objDocumentoDTO, $objProcedimentoDTO, $itemAnexoComplementar, $reciboDTOBasico, MdPetRelReciboDocumentoAnexoRN::$TP_COMPLEMENTAR );

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
				$arrAnexoComplementarVinculacaoProcesso[] = $itemAnexoComplementar;
				$arrIdAnexoComplementar[] = $idDocumentoAnexo;
				
				$contador = $contador+1;
			}
			
			if( count( $arrIdAnexoComplementar ) > 0 ){
				SessaoSEIExterna::getInstance()->setAtributo('arrIdAnexoComplementar', $arrIdAnexoComplementar);
			}
			
		}
						
	}

	private function montarDocumentoPrincipal( $objProcedimentoDTO, 
			                                   $objMdPetTipoProcessoDTO, 
			                                   $objUnidadeDTO, 
			                                   $arrParametros ){

			$objSeiRN = new SeiRN();
			
			$nivelAcessoDocPrincipal = $arrParametros['nivelAcessoDocPrincipal'];
			$grauSigiloDocPrincipal = $arrParametros['grauSigiloDocPrincipal'];
			$hipoteseLegalDocPrincipal = $arrParametros['hipoteseLegalDocPrincipal'];

			//o proprio usuario externo logado é remetente do documento
			$contatoDTO = $this->getContatoDTOUsuarioLogado();

			//Incluir documento interno
			$objDocumentoAPI = new DocumentoAPI();
			
			//Se o ID do processo é conhecido utilizar setIdProcedimento no lugar de
			$objDocumentoAPI->setIdProcedimento( $objProcedimentoDTO->getDblIdProcedimento() );
			$objDocumentoAPI->setTipo( ProtocoloRN::$TP_DOCUMENTO_GERADO );
			$objDocumentoAPI->setIdHipoteseLegal( $hipoteseLegalDocPrincipal );
			$objDocumentoAPI->setNivelAcesso( $nivelAcessoDocPrincipal );
			$objDocumentoAPI->setIdSerie( $objMdPetTipoProcessoDTO[0]->getNumIdSerie() );
			$objDocumentoAPI->setConteudo(base64_encode( $arrParametros['docPrincipalConteudoHTML'] ));
			$objDocumentoAPI->setSinAssinado('S');
			$objDocumentoAPI->setSinBloqueado('S');

			$objSaidaIncluirDocumentoAPI = $objSeiRN->incluirDocumento($objDocumentoAPI);

			SessaoSEIExterna::getInstance()->setAtributo('idDocPrincipalGerado', $objSaidaIncluirDocumentoAPI->getIdDocumento() );

			$documentoDTOPrincipal = new DocumentoDTO();
			$documentoDTOPrincipal->setDblIdDocumento( $objSaidaIncluirDocumentoAPI->getIdDocumento() );
			return $documentoDTOPrincipal;
		
	}

	public function assinarETravarDocumentoProcesso( $objUnidadeDTO, $arrParametros, $documentoDTO, $objProcedimentoDTO ){
			
		    //consultar email da unidade (orgao)
		    $orgaoRN = new OrgaoRN();
			$orgaoDTO = new OrgaoDTO();
			$orgaoDTO->retTodos();
			$orgaoDTO->retStrEmailContato();
			$orgaoDTO->setNumIdOrgao( $objUnidadeDTO->getNumIdOrgao() );
			$orgaoDTO->setStrSinAtivo('S');
			$orgaoDTO = $orgaoRN->consultarRN1352($orgaoDTO);

			if (!empty($arrParametros['selCargo'])){
				//consultar nome do cargao funcao selecionada na combo
				$cargoRN = new CargoRN();
				$cargoDTO = new CargoDTO();

				//alteracoes seiv3
				$cargoDTO->retNumIdCargo();
				$cargoDTO->retStrExpressao();
				$cargoDTO->retStrSinAtivo();

				$cargoDTO->setNumIdCargo( $arrParametros['selCargo'] );
				$cargoDTO->setStrSinAtivo('S');
				$cargoDTO = $cargoRN->consultarRN0301($cargoDTO);

				if (!is_null($cargoDTO)){
					$cargoExpressao = "Usuário Externo - " . $cargoDTO->getStrExpressao();
				}
			}else{
				$cargoExpressao = $arrParametros['selCargoFuncao'];
			}

			//liberando assinatura externa para o documento
			$objAcessoExternoDTO = new AcessoExternoDTO();
			
			//trocado de $TA_ASSINATURA_EXTERNA para $TA_SISTEMA para evitar o envio de email de notificação
			$objAcessoExternoDTO->setStrStaTipo(AcessoExternoRN::$TA_ASSINATURA_EXTERNA ); 
			
			//checar se o proprio usuario ja foi adicionado como interessado (participante) do processo
			$objUsuarioDTO = new UsuarioDTO();
			$objUsuarioDTO->retTodos();

			$idUsuario = !empty(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()) ? SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() : SessaoSEI::getInstance()->getNumIdUsuario();
			$objUsuarioDTO->setNumIdUsuario( $idUsuario );

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

			if (!empty(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno())){
				//alteracoes seiv3
				$objAcessoExternoDTO->setStrEmailUnidade($orgaoDTO->getStrEmailContato() ); //informando o email do orgao associado a unidade

				$objAcessoExternoDTO->setDblIdDocumento( $documentoDTO->getDblIdDocumento() );
				$objAcessoExternoDTO->setNumIdParticipante( $idParticipante );


				$objAcessoExternoDTO->setNumIdUsuarioExterno( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
				$objAcessoExternoDTO->setStrSinProcesso('N'); //visualizacao integral do processo

				$objMdPetAcessoExternoRN = new MdPetAcessoExternoRN();
				$objAcessoExternoDTO = $objMdPetAcessoExternoRN->cadastrarAcessoExternoCore($objAcessoExternoDTO);
			}

			//realmente assinando o documento depois da assinatura externa ser liberada

			//seiv3 - só permite assinar doc externo (upload) nato-digital se tiver o tipo de conferencia setado
			//setar temporariamente e depois remover da entidade
			$documentoRN = new DocumentoRN();
			$documentoPetRN = new MdPetDocumentoRN();
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
				$objAssinaturaDTO->setNumIdUsuario($idUsuario);
				$objAssinaturaDTO->setNumIdOrgaoUsuario( SessaoSEI::getInstance()->getNumIdOrgaoUsuario() );

				$objAssinaturaDTO->setStrSenhaUsuario( $arrParametros['pwdsenhaSEI'] );
				$objAssinaturaDTO->setStrCargoFuncao( $cargoExpressao );

				if (empty(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno())){
//					$objAssinaturaDTO->setNumIdContextoUsuario( null );
				}

				$documentoDTO->setStrDescricaoTipoConferencia("do próprio documento nato-digital");
				$objAssinaturaDTO->setArrObjDocumentoDTO(array($documentoDTO));
				
			} else {
				
				$objAssinaturaDTO = new AssinaturaDTO();
				$objAssinaturaDTO->setStrStaFormaAutenticacao(AssinaturaRN::$TA_SENHA);
				$objAssinaturaDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
				$objAssinaturaDTO->setStrSenhaUsuario( $arrParametros['pwdsenhaSEI'] );
				$objAssinaturaDTO->setStrCargoFuncao( $cargoExpressao );
				$objAssinaturaDTO->setArrObjDocumentoDTO(array($documentoDTO));		

			}

			$objAssinaturaDTO = $documentoPetRN->assinar($objAssinaturaDTO);

			//alteracoes seiv3 - removendo o tipo de conferencia padrao que foi informado
			if( $bolRemoverTipoConferencia ){
				$documentoDTO->setNumIdTipoConferencia(null);
				$documentoBD->alterar( $documentoDTO );
			}

			//nao aplicando metodo alterar da RN de Documento por conta de regras de negocio muito especificas aplicadas ali
			$documentoDTO->setStrSinBloqueado('S');
			$documentoBD->alterar( $documentoDTO );

			if (!empty(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno())){
				//remover a liberação de acesso externo -> AcessoRN.excluir nao permite exclusao, por isso chame AcessoExternoBD diretamente daqui
				$objAcessoExternoBD = new AcessoExternoBD($this->getObjInfraIBanco());
				$objAcessoExternoBD->excluir( $objAcessoExternoDTO );
			}
	}

	private function atribuirParticipantes( $arrObjInteressados)
	{
		
		$arrObjParticipantesDTO = array();

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