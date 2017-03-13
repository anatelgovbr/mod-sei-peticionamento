<?
/**
* ANATEL
*
* 25/11/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntercorrenteProcessoRN extends ProcessoPeticionamentoRN {

	public static $FORMATO_DIGITALIZADO = 'D';
	public static $FORMATO_NATO_DIGITAL = 'N';

    private $objAcessoExternoDTO = null;
    private $objUsuarioDTO       = null;
    private $objCargoDTO         = null;
    private $objOrgaoDTO         = null;
    private $objUnidadeDTO       = null;
    private $objProcedimentoDTO  = null;
    private $senha               = null;
    private $reciboDTO           = null;
    private $participantesDTO    = null;
    private $documentoRecibo     = null;
    private $arrDocumentos       = array();


    private $tipoConferenciaAlterado = false;
	
	public function __construct() {
		parent::__construct ();
		
	}
	
	protected function inicializarObjInfraIBanco() {
		return BancoSEI::getInstance ();
	}

	/**
	 * Short description of method validarUnidadeProcessoConectado
	 * Valida se as unidades dos processos abertos para esse procedimento estão ativas
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param  ProcedimentoDTO $objProcedimentoDTO
	 * @return boolean
	 */
	protected function validarUnidadeProcessoConectado($objProcedimentoDTO){
        $objAtividadeRN  = new AtividadeRN();
        $arrObjUnidadeDTO = $objAtividadeRN->listarUnidadesTramitacao($objProcedimentoDTO);

		if(count($arrObjUnidadeDTO) == 0){
			return false;
		}

		return true;
	}

	/**
	 * Short description of method pesquisarProtocoloFormatadoConectado
	 * Pesquisa o processo exatamente como foi digitado SEM considerar a formatação
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param  ProtocoloDTO $parObjProtocoloDTO
	 * @return mixed
	 */
	protected function pesquisarProtocoloFormatadoConectado(ProtocoloDTO $parObjProtocoloDTO){
		try{
			$objProtocoloRN = new ProtocoloRN();
			//busca pelo numero do processo
			$objProtocoloDTOPesquisa = new ProtocoloDTO();
			$objProtocoloDTOPesquisa->retDblIdProtocolo();
			$objProtocoloDTOPesquisa->retStrProtocoloFormatado();
			$objProtocoloDTOPesquisa->retStrStaProtocolo();
			$objProtocoloDTOPesquisa->retStrStaNivelAcessoGlobal();
			$objProtocoloDTOPesquisa->setNumMaxRegistrosRetorno(2);

			$strProtocoloPesquisa = InfraUtil::retirarFormatacao($parObjProtocoloDTO->getStrProtocoloFormatadoPesquisa(),false);

			$objProtocoloDTOPesquisa->setStrProtocoloFormatadoPesquisa($strProtocoloPesquisa);
			$arrObjProtocoloDTO = $objProtocoloRN->listarRN0668($objProtocoloDTOPesquisa);

			if (count($arrObjProtocoloDTO) > 1) {
				return null;
			}

			if (count($arrObjProtocoloDTO) == 1) {
				return $arrObjProtocoloDTO[0];
			}


			return null;

		}catch(Exception $e){
			throw new InfraException('Erro pesquisando protocolo.',$e);
		}
	}

    /**
     * Retorna a ultima unidade que o processo foi 
     * Pesquisa o processo exatamente como foi digitado SEM considerar a formatação
     * @access protected
     * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
     * @param  ProtocoloDTO $parObjProtocoloDTO
     * @return mixed
     */
	protected function retornaUltimaUnidadeProcessoConcluidoConectado(AtividadeDTO $objAtividadeDTO){

		$idUnidadeReabrirProcesso = null;
		$objAtividadeRN  = new AtividadeRN();
		$objAtividadeDTO->retDthConclusao();
		$objAtividadeDTO->retNumIdUnidade();
		$objAtividadeDTO->setOrdDthConclusao(InfraDTO::$TIPO_ORDENACAO_DESC);
		$arrObjAtividadeDTO = $objAtividadeRN->listarRN0036($objAtividadeDTO);
		$objUltimaAtvProcesso = count($arrObjAtividadeDTO) > 0 ? current($arrObjAtividadeDTO) : null;
		if(!is_null($objUltimaAtvProcesso)) {
			$idUnidadeReabrirProcesso = $objUltimaAtvProcesso->getNumIdUnidade();
		}

		return $idUnidadeReabrirProcesso;
	}

    protected function gerarProcedimentoApi($params)
    {
        $objProcedimentoDTO = $params[0];
        $objCriterioIntercorrenteDTO = $params[1];
        //$arrObjDocumentoAPI = $params[2];
        $especificacao = $params[2];

        if($objProcedimentoDTO->getStrStaEstadoProtocolo() == 3){
            $objRelProtocoloProtocoloDTO = new RelProtocoloProtocoloDTO();
            $objRelProtocoloProtocoloDTO->retDblIdProtocolo1();
            $objRelProtocoloProtocoloDTO->retStrProtocoloFormatadoProtocolo1();
            $objRelProtocoloProtocoloDTO->setDblIdProtocolo2($objProcedimentoDTO->getDblIdProcedimento());
            $objRelProtocoloProtocoloDTO->setStrStaAssociacao(RelProtocoloProtocoloRN::$TA_PROCEDIMENTO_ANEXADO);

            $objRelProtocoloProtocoloRN = new RelProtocoloProtocoloRN();
            $objRelProtocoloProtocoloDTO = $objRelProtocoloProtocoloRN->consultarRN0841($objRelProtocoloProtocoloDTO);

            $idUnidadeAbrirNovoProcesso = $this->retornaUltimaUnidadeProcessoAberto($objRelProtocoloProtocoloDTO->getDblIdProtocolo1());
        }else{
            $idUnidadeAbrirNovoProcesso = $this->retornaUltimaUnidadeProcessoAberto($objProcedimentoDTO->getDblIdProcedimento());
        }

        // inicio da verificação da unidade ativa, caso não esteja tenta buscar uma unidade ativa para reabrir o processo.
        $unidadeDTO = new UnidadeDTO();
        $unidadeDTO->retTodos();
        $unidadeDTO->setBolExclusaoLogica(false);
        $unidadeDTO->setNumIdUnidade($idUnidadeAbrirNovoProcesso);
        $unidadeRN = new UnidadeRN();
        $objUnidadeDTO = $unidadeRN->consultarRN0125($unidadeDTO);

        if($objUnidadeDTO->getStrSinAtivo() == 'N'){
            $idUnidadeAbrirNovoProcesso = null;
            $objAtividadeRN  = new MdPetIntercorrenteAtividadeRN();
            $arrObjUnidadeDTO = $objAtividadeRN->listarUnidadesTramitacao($objProcedimentoDTO);

            foreach ($arrObjUnidadeDTO as $itemObjUnidadeDTO) {
                if ($itemObjUnidadeDTO->getStrSinAtivo() == 'S') {
                    $idUnidadeAbrirNovoProcesso = $itemObjUnidadeDTO->getNumIdUnidade();
                }
            }
        }

        if($idUnidadeAbrirNovoProcesso == null) {
            $objInfraException = new InfraException();
            $objInfraException->adicionarValidacao('O processo indicado não aceita peticionamento intercorrente. Utilize o Peticionamento de Processo Novo para protocolizar sua demanda.');
            $objInfraException->lancarValidacoes();
            return false;
        }
        // fim da verificação da unidade ativa

        // Salva um processo do tipo padrão selecionado
        $this->simularLogin($idUnidadeAbrirNovoProcesso);

        $objEntradaGerarProcedimentoAPI = new EntradaGerarProcedimentoAPI();
        $arrProcedimentoRelacionado = array($objProcedimentoDTO->getDblIdProcedimento());

        $objProcedimentoAPI = new ProcedimentoAPI();
        $objProcedimentoAPI->setIdTipoProcedimento($objCriterioIntercorrenteDTO->getNumIdTipoProcedimento());
        if ($especificacao!=null){
            $objProcedimentoAPI->setEspecificacao( $especificacao );
        }


        $objEntradaGerarProcedimentoAPI->setProcedimento($objProcedimentoAPI);
        $objEntradaGerarProcedimentoAPI->setProcedimentosRelacionados($arrProcedimentoRelacionado);
        //$objEntradaGerarProcedimentoAPI->setDocumentos($arrObjDocumentoAPI);
        $objSEIRN = new SeiRN();
        return $objSEIRN->gerarProcedimento($objEntradaGerarProcedimentoAPI);
    }

    /**
     * Função responsável por Retornar a última unidade em que o processo ESTÀ aberto agora
     * @param $idProcedimento
     * @return  string $idUnidade
     */
    protected function retornaUltimaUnidadeProcessoAbertoConectado($idProcedimento){

    	$objSEIRN = new SeiRN();
        $objProcedimentoDTO = new ProcedimentoDTO();
        $objProcedimentoDTO->retTodos(true);
        $objProcedimentoDTO->setDblIdProcedimento($idProcedimento);
        $objProcedimentoRN = new ProcedimentoRN();
        $objProcedimentoDTO = $objProcedimentoRN->consultarRN0201($objProcedimentoDTO);

        $objEntradaConsultaProcApi = new EntradaConsultarProcedimentoAPI();
        $objEntradaConsultaProcApi->setIdProcedimento($idProcedimento);
        $objEntradaConsultaProcApi->setSinRetornarUnidadesProcedimentoAberto('S');
        $objEntradaConsultaProcApi->setSinRetornarUltimoAndamento('S');
        $objEntradaConsultaProcApi->setSinRetornarAndamentoConclusao('N');

        /**
         * @var $saidaConsultarProcedimentoAPI SaidaConsultarProcedimentoAPI
         */
        if($objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo() == ProtocoloRN::$NA_SIGILOSO ||
            $objProcedimentoDTO->getStrStaNivelAcessoLocalProtocolo() == ProtocoloRN::$NA_SIGILOSO){
            $md = new MdPetIntercorrenteAndamentoSigilosoRN();
            $saidaConsultarProcedimentoAPI = $md->consultarProcedimento($objEntradaConsultaProcApi);
        } else {
            $saidaConsultarProcedimentoAPI = $objSEIRN->consultarProcedimento($objEntradaConsultaProcApi);
        }

        //informaçoes da tarefa de conclusao de processo na unidade
        $tarefaRN = new TarefaRN();
        $tarefaDTO = new TarefaDTO();
        $tarefaDTO->retNumIdTarefa( );
        $tarefaDTO->retStrNome( );
        $tarefaDTO->setNumIdTarefa( TarefaRN::$TI_CONCLUSAO_PROCESSO_UNIDADE );
        $arrTarefaDTO = $tarefaRN->listar( $tarefaDTO );
        $tarefaDTO = $arrTarefaDTO[0];
        
        //lista de unidades nas quais o processo ainda encontra-se aberto
        $arrUnidadesAbertas = $saidaConsultarProcedimentoAPI->getUnidadesProcedimentoAberto();        	
        	
        //o processo encontra-se aberto em pelo menos uma unidade
        if( is_array( $arrUnidadesAbertas ) && count( $arrUnidadesAbertas ) > 0 ){
        		
        	$objEntradaAndamentos = new EntradaListarAndamentosAPI();
        	$objEntradaAndamentos->setIdProcedimento( $idProcedimento );
        	$objEntradaAndamentos->setTarefas( array( TarefaRN::$TI_GERACAO_PROCEDIMENTO , TarefaRN::$TI_REABERTURA_PROCESSO_UNIDADE, TarefaRN::$TI_PROCESSO_REMETIDO_UNIDADE ) );
        	$arrAndamentos = $objSEIRN->listarAndamentos( $objEntradaAndamentos );
        		
        	$arrIdUnidade = array();
        	
        	foreach( $arrUnidadesAbertas as $unidadeAberta ){
        		$arrIdUnidade[] = $unidadeAberta->getUnidade()->getIdUnidade();
        	}
        	        	
        	foreach( $arrAndamentos as $andamento ){
        			
        		$idUnidadeAndamento = $andamento->getUnidade()->getIdUnidade();
        		
        		if( in_array( $idUnidadeAndamento, $arrIdUnidade ) ){
        			return $idUnidadeAndamento;
        		}
        		        			
        	}
        		
        } 
        	
        //o processo nao esta aberto em nenhuma unidade, nao ha id para ser retornado
        else {
        	return null;
        }
        	
    }        

    /**
     * Função responsável por Retornar todas as unidades em que o processo está aberto
     *   buscado em: ProcedimentoINT.php - montarAcoesArvore() - linhas 766 a 870
     * @param $idProcedimento
     * @return AtividadeDTO $arrObjAtividadeDTO
     * @since  07/03/2017
     * @author CAST - castgroup.com.br
     */    
    protected function retornaUnidadesProcessoAbertoConectado($idProcedimento){

		$arrAtividade = array();

		$objProcedimentoDTO = new ProcedimentoDTO();
		$objProcedimentoDTO->setDblIdProcedimento($idProcedimento);
		$objProcedimentoDTO->retStrStaNivelAcessoGlobalProtocolo();
		$objProcedimentoDTO->retStrStaEstadoProtocolo();

		$objProcedimentoRN = new ProcedimentoRN();
		$objProcedimentoDTO = $objProcedimentoRN->consultarRN0201($objProcedimentoDTO);
		$strStaNivelAcessoGlobal = $objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo();

		$bolFlagSobrestado = false;
		if ($objProcedimentoDTO->getStrStaEstadoProtocolo()==ProtocoloRN::$TE_PROCEDIMENTO_SOBRESTADO){
			$bolFlagSobrestado = true;
		}

		$objAtividadeRN = new AtividadeRN();
		$objAtividadeDTO = new AtividadeDTO();
		$objAtividadeDTO->setDistinct(true);
		$objAtividadeDTO->retNumIdUnidade();
		$objAtividadeDTO->retStrSiglaUnidade();
		$objAtividadeDTO->retStrDescricaoUnidade();

		$objAtividadeDTO->setOrdStrSiglaUnidade(InfraDTO::$TIPO_ORDENACAO_ASC);

		if ($strStaNivelAcessoGlobal==ProtocoloRN::$NA_SIGILOSO){
			$objAtividadeDTO->retNumIdUsuario();
			$objAtividadeDTO->retStrSiglaUsuario();
			$objAtividadeDTO->retStrNomeUsuario();
		}else{
			$objAtividadeDTO->retNumIdUsuarioAtribuicao();
			$objAtividadeDTO->retStrSiglaUsuarioAtribuicao();
			$objAtividadeDTO->retStrNomeUsuarioAtribuicao();

			//ordena descendente pois no envio de processo que já existe na unidade e está atribuído ficará com mais de um andamento em aberto
			//desta forma os andamentos com usuário nulo (envios do processo) serão listados depois
			$objAtividadeDTO->setOrdStrSiglaUsuarioAtribuicao(InfraDTO::$TIPO_ORDENACAO_DESC);
		}
		$objAtividadeDTO->setDblIdProtocolo($idProcedimento);
		$objAtividadeDTO->setDthConclusao(null);

		//sigiloso sem credencial nao considera o usuario atual
		if ($strStaNivelAcessoGlobal==ProtocoloRN::$NA_SIGILOSO){
			$objAcessoDTO = new AcessoDTO();
			$objAcessoDTO->setDistinct(true);
			$objAcessoDTO->retNumIdUsuario();
			$objAcessoDTO->setDblIdProtocolo($dblIdProcedimento);
			$objAcessoDTO->setStrStaTipo(AcessoRN::$TA_CREDENCIAL_PROCESSO);

			$objAcessoRN = new AcessoRN();
			$arrObjAcessoDTO = $objAcessoRN->listar($objAcessoDTO);

			$objAtividadeDTO->setNumIdUsuario(InfraArray::converterArrInfraDTO($arrObjAcessoDTO,'IdUsuario'),InfraDTO::$OPER_IN);
		}

		$arrObjAtividadeDTO = $objAtividadeRN->listarRN0036($objAtividadeDTO);

		if ($strStaNivelAcessoGlobal!=ProtocoloRN::$NA_SIGILOSO){
			//filtra andamentos com indicação de usuário atribuído 
			$arrObjAtividadeDTO = InfraArray::distinctArrInfraDTO($arrObjAtividadeDTO,'SiglaUnidade');
		}
		return $arrObjAtividadeDTO;
    }

    protected function incluirDocumentosApi($objProcedimentoDTO, $arrObjDocumentoAPI)
    {
        $arrObjReciboDocPet = array();
        $objSEIRN = new SeiRN();
        $idUnidadeProcesso = $this->retornaUltimaUnidadeProcessoAberto($objProcedimentoDTO->getDblIdProcedimento());


        // inicio da verificação da unidade ativa, caso não esteja tenta buscar uma unidade ativa para reabrir o processo.
        $unidadeDTO = new UnidadeDTO();
        $unidadeDTO->retTodos();
        $unidadeDTO->setBolExclusaoLogica(false);
        $unidadeDTO->setNumIdUnidade($idUnidadeProcesso);
        $unidadeRN = new UnidadeRN();
        $objUnidadeDTO = $unidadeRN->consultarRN0125($unidadeDTO);

        if($objUnidadeDTO->getStrSinAtivo() == 'N'){
            $idUnidadeProcesso = null;
            $objAtividadeRN  = new AtividadeRN();
            $arrObjAtividadeDTO = $objAtividadeRN->listarUnidadesTramitacao($objProcedimentoDTO);

            foreach ($arrObjAtividadeDTO as $itemObjAtividadeDTO) {
                $unidadeDTO = new UnidadeDTO();
                $unidadeDTO->retTodos();
                $unidadeDTO->setBolExclusaoLogica(false);
                $unidadeDTO->setNumIdUnidade($itemObjAtividadeDTO->getNumIdUnidade());
                $unidadeRN = new UnidadeRN();
                $objUnidadeDTO = $unidadeRN->consultarRN0125($unidadeDTO);


                if ($objUnidadeDTO->getStrSinAtivo() == 'S') {
                    $idUnidadeProcesso = $objUnidadeDTO->getNumIdUnidade();
                }
            }
        }



        $this->simularLogin($idUnidadeProcesso);

        foreach ($arrObjDocumentoAPI as $documentoAPI) {
            /* @var $documentoAPI DocumentoAPI */
            $saidaIncluirDocumentoAPI = $objSEIRN->incluirDocumento($documentoAPI);

			// Remententes
			$idsParticipantes = array();

			$objParticipante  = new ParticipanteDTO();
			$objParticipante->setDblIdProtocolo($saidaIncluirDocumentoAPI->getIdDocumento());
			$objParticipante->setNumIdContato($this->getContatoDTOUsuarioLogado()->getNumIdContato());
			$objParticipante->setNumIdUnidade($idUnidadeProcesso);
			$objParticipante->setStrStaParticipacao(ParticipanteRN::$TP_REMETENTE);
			$objParticipante->setNumSequencia(0);
			$idsParticipantes[] = $objParticipante;

			//Interessados
			// Processo Principal - Interessados
			$objParticipanteProcPrincDTO = new ParticipanteDTO();
			$objParticipanteProcPrincDTO->retNumIdParticipante();
			$objParticipanteProcPrincDTO->retNumIdContato();
			$objParticipanteProcPrincDTO->retNumIdUnidade();
			$objParticipanteProcPrincDTO->retStrStaParticipacao();
			$objParticipanteProcPrincDTO->retStrNomeContato();
			$objParticipanteProcPrincDTO->retNumSequencia();
			$objParticipanteProcPrincDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());
			$objParticipanteProcPrincDTO->setStrStaParticipacao(ParticipanteRN::$TP_INTERESSADO);	        

			$objParticipanteProcPrincRN = new ParticipanteRN();
			$arrobjParticipanteProcPrinc = $objParticipanteProcPrincRN->listarRN0189($objParticipanteProcPrincDTO);            
			// Processo Principal - Interessados - FIM

			// Processo - Interessados
			$i=0;
			foreach($arrobjParticipanteProcPrinc as $objParticipanteProcPrinc){
				$objParticipante  = new ParticipanteDTO();
				$objParticipante->setDblIdProtocolo($saidaIncluirDocumentoAPI->getIdDocumento());
				$objParticipante->setNumIdContato($objParticipanteProcPrinc->getNumIdContato());
				$objParticipante->setNumIdUnidade($objParticipanteProcPrinc->getNumIdUnidade());
				$objParticipante->setStrStaParticipacao(ParticipanteRN::$TP_INTERESSADO);
				$objParticipante->setNumSequencia($i);
				$idsParticipantes[] = $objParticipante;
				$i++;
			}

			$objMdPetParticipanteRN = new MdPetParticipanteRN();
			$arrInteressado = array();
			$arrInteressado[0] = $saidaIncluirDocumentoAPI->getIdDocumento();
			$arrInteressado[1] = $idsParticipantes;
			//$arrInteressado[2] = $idsRemententes;
			$objMdPetParticipanteRN->setInteressadosRemetentesProcedimentoDocumento( $arrInteressado );            
			// Processo - Interessados - FIM

			$formato = MdPetIntercorrenteINT::retornaTipoFormatoDocumento($saidaIncluirDocumentoAPI);
			$objReciboDocAnexPetDTO = MdPetIntercorrenteINT::retornaObjReciboDocPreenchido(array($saidaIncluirDocumentoAPI->getIdDocumento(), $formato));
			array_push($arrObjReciboDocPet, $objReciboDocAnexPetDTO);
			$this->assinarETravarDocumento($saidaIncluirDocumentoAPI);
        }
        return $arrObjReciboDocPet;
    }

    private function abrirDocumentoParaAssinatura($dblIdDocumento)
    {
        $documentoRN = new DocumentoRN();
        $documentoBD = new DocumentoBD( $this->getObjInfraIBanco() );

        $documentoDTO = new DocumentoDTO();
        //$documentoDTO->retDblIdDocumento();
        //$documentoDTO->retNumIdTipoConferencia();
        $documentoDTO->retTodos(true);
        $documentoDTO->setDblIdDocumento( $dblIdDocumento );
        $documentoDTO = $documentoRN->consultarRN0005( $documentoDTO );

        $this->tipoConferenciaAlterado = false;
        //setar temporariamente e depois remover da entidade
        if(! $documentoDTO->getNumIdTipoConferencia() ){
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
            $this->tipoConferenciaAlterado = true;
        }
        return $documentoDTO;
    }

    private function fecharDocumentoParaAssinatura($documentoDTO)
    {
        $documentoBD = new DocumentoBD( $this->getObjInfraIBanco() );
        if( $this->tipoConferenciaAlterado ){
            $documentoDTO->setNumIdTipoConferencia(null);
            $documentoBD->alterar( $documentoDTO );
        }
        //nao aplicando metodo alterar da RN de Documento por conta de regras de negocio muito especificas aplicadas ali
        $documentoDTO->setStrSinBloqueado('S');
        $documentoBD->alterar( $documentoDTO );
        //remover a liberação de acesso externo //AcessoRN.excluir nao permite exclusao, por isso chame AcessoExternoBD diretamente daqui
    }

    private function abrirAcessoParaAssinatura($orgaoDTO, $documentoDTO, $objParticipanteDTO)
    {
        //liberando assinatura externa para o documento
        $objAcessoExternoDTO = new AcessoExternoDTO();

        //trocado de $TA_ASSINATURA_EXTERNA para $TA_SISTEMA para evitar o envio de email de notificação
        $objAcessoExternoDTO->setStrStaTipo(AcessoExternoRN::$TA_ASSINATURA_EXTERNA );
        $objAcessoExternoDTO->setStrEmailUnidade($orgaoDTO->getStrEmailContato() ); //informando o email do orgao associado a unidade
        $objAcessoExternoDTO->setDblIdDocumento( $documentoDTO->getDblIdDocumento() );
        $objAcessoExternoDTO->setNumIdParticipante( $objParticipanteDTO->getNumIdParticipante() );
        $objAcessoExternoDTO->setNumIdUsuarioExterno( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
        $objAcessoExternoDTO->setStrSinProcesso('N'); //visualizacao integral do processo

        $objAcessoExternoRN = new AcessoExternoPeticionamentoRN();
        $this->objAcessoExternoDTO = $objAcessoExternoRN->cadastrar($objAcessoExternoDTO);
    }

    private function fecharAcessoParaAssinatura()
    {
        $objAcessoExternoBD = new AcessoExternoBD($this->getObjInfraIBanco());
        $objAcessoExternoBD->excluir( $this->objAcessoExternoDTO );
    }

    private function retornarParticipante($objUsuarioDTO, $objUnidadeDTO, $objProcedimentoDTO)
    {
        $objParticipanteDTO = new ParticipanteDTO();
        $objParticipanteDTO->retTodos(true);
        $objParticipanteDTO->retStrSiglaContato();
        $objParticipanteDTO->retStrNomeContato();
        $objParticipanteDTO->retNumIdUnidade();
        $objParticipanteDTO->retDblIdProtocolo();
        $objParticipanteDTO->retNumIdParticipante();
        //FK de BD (ak1_participante) trata somente id_contato, id_protocolo, sta_participacao, então desconsiderar unidade
        //$objParticipanteDTO->setNumIdUnidade( $objUnidadeDTO->getNumIdUnidade() );
        $objParticipanteDTO->setNumIdContato( $objUsuarioDTO->getNumIdContato() );
        $objParticipanteDTO->setDblIdProtocolo( $objProcedimentoDTO->getDblIdProcedimento() );

        $objParticipanteRN = new ParticipanteRN();
        $arrObjParticipanteDTO = $objParticipanteRN->listarRN0189($objParticipanteDTO);

        if( $arrObjParticipanteDTO == null || count( $arrObjParticipanteDTO ) == 0){
            //cadastrar o participante
            $objParticipanteDTO = new ParticipanteDTO();
            $objParticipanteDTO->setNumIdContato( $objUsuarioDTO->getNumIdContato() );
            $objParticipanteDTO->setDblIdProtocolo( $objProcedimentoDTO->getDblIdProcedimento() );
            $objParticipanteDTO->setStrStaParticipacao( ParticipanteRN::$TP_ACESSO_EXTERNO );
            $objParticipanteDTO->setNumIdUnidade( $objUnidadeDTO->getNumIdUnidade() );
            $objParticipanteDTO->setNumSequencia(0);

            $participanteDTO = $objParticipanteRN->cadastrarRN0170( $objParticipanteDTO );

            $objParticipanteDTO = new ParticipanteDTO();
            $objParticipanteDTO->retTodos(true);
            $objParticipanteDTO->setNumIdParticipante( $participanteDTO->getNumIdParticipante() );
            $ret = $objParticipanteRN->consultarRN1008($objParticipanteDTO);
            //$idParticipante = $objParticipanteDTO->getNumIdParticipante();
        } else {
            $ret = $arrObjParticipanteDTO[0];
        }
        $this->setParticipante($ret);
        return $ret;
    }

    private function assinarETravarDocumento( $documento )
    {
        //consultar email da unidade (orgao)
        //$this->carregarObjetos($idCargo, );
        $orgaoDTO           = $this->getOrgaoDTO();
        $cargoDTO           = $this->getCargoDTO();
        $objUsuarioDTO      = $this->getUsuarioDTO();
        $objUnidadeDTO      = $this->getUnidadeDTO();
        $objProcedimentoDTO = $this->getProcedimentoDTO();

        $documentoDTO = $documento;
        if($documento instanceof SaidaIncluirDocumentoAPI){
            $dlbIdDocumento = $documento->getIdDocumento();
        } else {
            $dlbIdDocumento = $documento->getDblIdDocumento();
        }
        $documentoDTO = $this->abrirDocumentoParaAssinatura($dlbIdDocumento);
        $objParticipanteDTO = $this->retornarParticipante($objUsuarioDTO, $objUnidadeDTO, $objProcedimentoDTO);

        $this->abrirAcessoParaAssinatura($orgaoDTO, $documentoDTO, $objParticipanteDTO);
        $objAssinaturaDTO = new AssinaturaDTO();
        $objAssinaturaDTO->setStrStaFormaAutenticacao(AssinaturaRN::$TA_SENHA);
        $objAssinaturaDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
        $objAssinaturaDTO->setStrSenhaUsuario( $this->getSenha() );
        $objAssinaturaDTO->setStrCargoFuncao( "Usuário Externo - " . $cargoDTO->getStrExpressao() );
        $objAssinaturaDTO->setArrObjDocumentoDTO(array($documentoDTO));

        $documentoRN = new DocumentoPeticionamentoRN();
        $objAssinaturaDTO = $documentoRN->assinar($objAssinaturaDTO);
        $this->addDocumentos($documentoDTO);
        $this->fecharDocumentoParaAssinatura($documentoDTO);
        $this->fecharAcessoParaAssinatura();
        return $objAssinaturaDTO;
    }

    private function retornarDocumentosRecibo($objSaidaGerarProcedimentoAPI) {
        $arrObjReciboDocPet = array();
        $objRetornoDocs = $objSaidaGerarProcedimentoAPI->getRetornoInclusaoDocumentos();
        $arrRetornoTpFormato = MdPetIntercorrenteINT::retornaTipoFormatoDocumento($objRetornoDocs);

        $formato = null;
        foreach($objRetornoDocs as $doc){
            if(array_key_exists($doc->getIdDocumento(), $arrRetornoTpFormato)){
                $formato =  $arrRetornoTpFormato[$doc->getIdDocumento()];
            }
            $objReciboDocAnexPetDTO = MdPetIntercorrenteINT::retornaObjReciboDocPreenchido(array($doc->getIdDocumento(), $formato));
            array_push($arrObjReciboDocPet, $objReciboDocAnexPetDTO);
        }
        return $arrObjReciboDocPet;
    }

    /**
     * @param $params
     * @return DocumentoDTO
     */
    private function montarReciboIntercorrente($params)
    {
        $arrParams = array(
            $params,
            $this->getUnidadeDTO(),
            $this->getProcedimentoDTO(),
            array($this->getParticipanteDTO()),
            $this->getReciboDTO(),
            $this->getDocumentos()
        );

        $reciboPeticionamentoRN = new ReciboPeticionamentoIntercorrenteRN();
        return $reciboPeticionamentoRN->montarRecibo($arrParams);
    }

    protected function cadastrarReciboDocumentoAnexoConectado($params)
    {
        $reciboPeticionamentoDTO = $params[0];
        $arrObjReciboDocPet = $params[1];

        $objReciboDocAnexRN = new ReciboDocumentoAnexoPeticionamentoRN();

        $numIdReciboPeticionamento = $reciboPeticionamentoDTO->getNumIdReciboPeticionamento();
        //Gerar Recibo Docs
        foreach($arrObjReciboDocPet as $objReciboDocPet){
            //Remover Depois - Campo está como NOT NULL, deve ser NULL na adaptação
            $objReciboDocPet->setStrClassificacaoDocumento('A');
            $objReciboDocPet->setNumIdReciboPeticionamento($numIdReciboPeticionamento);
            $objReciboDocAnexRN->cadastrar($objReciboDocPet);
        }
    }

    protected function enviarEmailConectado($params)
    {
        $arrParams = array();
        $arrParams[0] = $params;
        $arrParams[1] = $this->getUnidadeDTO();
        $arrParams[2] = $this->getProcedimentoDTO();
        $arrParams[3] = array($this->getParticipanteDTO());
        $arrParams[4] = $this->getReciboDTO();
        $arrParams[5] = $this->getDocumentoRecibo();

        $emailNotificacaoPeticionamentoRN = new EmailNotificacaoPetIntercorrenteRN();
        return $emailNotificacaoPeticionamentoRN->notificaoPeticionamentoExterno( $arrParams );

        //return $this->notificaoPetIntercorrenteExterno($arrParams);
    }

    protected function cadastrarControlado($params)
    {
        // Bloco de validações
        $this->validarCadastro($params);

        // setando atributos que serão utilizados em outros métodos
        $this->setCargoDTO($params['selCargo']);
        $this->setSenha($params['senhaSEI']);

        //Busca o Procedimento Principal
        $objProcedimentoDTO = new ProcedimentoDTO();
        $objProcedimentoRN = new ProcedimentoRN();
        $objProcedimentoDTO->setDblIdProcedimento($params['id_procedimento']);
        $objProcedimentoDTO->retTodos(true);
        //$objProcedimentoDTO->retStrProtocoloProcedimentoFormatado();
        $objProcedimentoDTO = $objProcedimentoRN->consultarRN0201($objProcedimentoDTO);

        $objCriterioIntercorrenteRN = new CriterioIntercorrentePeticionamentoRN();
        $objCriterioIntercorrenteDTO = $objCriterioIntercorrenteRN->retornarCriterioPorTipoProcesso($params['id_tipo_procedimento']);

        // Verifica se o processo possui critério intercorrente cadastrado
        //Se não possui busca o padrão e cria um processo relacionado ao processo selecionado
        $arrObjReciboDocPet = array();
        $arrDadosRecibo = array();
        $arrDadosRecibo['idProcedimento'] = $params['id_procedimento'];

		// Remententes 
		$contatoDTOUsuarioLogado = $this->getContatoDTOUsuarioLogado();
		$idsRemententes = array();
		$idsRemententes[] = $contatoDTOUsuarioLogado->getNumIdContato();

        $estadosReabrirRelacionado = array(ProtocoloRN::$TE_PROCEDIMENTO_SOBRESTADO, ProtocoloRN::$TE_PROCEDIMENTO_ANEXADO, ProtocoloRN::$TE_PROCEDIMENTO_BLOQUEADO);
        /**
         * Verifica se:
         * 1 - Se o processo eh sigiloso (nivel de acesso global ou local eh igual a 2)
         * 2 - Se o Tipo do Processo do procedimento informado nao possui um intercorrente cadastrado(neste caso irah utilizar o Intercorrente Padrao)
         */
        $params['diretoProcessoIndicado']=false;
        if ($objCriterioIntercorrenteDTO->getStrSinCriterioPadrao() == 'S'
            || $objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo() == ProtocoloRN::$NA_SIGILOSO
            || $objProcedimentoDTO->getStrStaNivelAcessoLocalProtocolo() == ProtocoloRN::$NA_SIGILOSO
            || in_array($objProcedimentoDTO->getStrStaEstadoProtocolo(), $estadosReabrirRelacionado)) {

            $especificacao = 'Peticionamento Intercorrente relacionado ao Processo nº ' . $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado();
            $objSaidaGerarProcedimentoAPI = $this->gerarProcedimentoApi(array($objProcedimentoDTO, $objCriterioIntercorrenteDTO, $especificacao));

            $arrDadosRecibo['idProcedimentoRel'] = $params['id_procedimento'];
            $arrDadosRecibo['idProcedimento'] = $objSaidaGerarProcedimentoAPI->getIdProcedimento();
            $params['id_procedimento'] = $objSaidaGerarProcedimentoAPI->getIdProcedimento();
            //Se possui critérios intercorrentes setta os documentos no processo existente
            $this->setProcedimentoDTO($objSaidaGerarProcedimentoAPI->getIdProcedimento());
        } 
        
        //SE o criterio existe, e NAO é o criterio padrao, tenta incluir documento no proprio processo (caso o mesmo esteja aberto) ou reabrir o processo (caso o mesmo esteja fechado)
        else if( $objCriterioIntercorrenteDTO != null && 
        		$objCriterioIntercorrenteDTO->getStrSinCriterioPadrao() == 'N') {
            
        	//se for necessario, executar reabertura do processo		
        	$reaberturaRN = new MdPetIntercorrenteReaberturaRN();
        			
        	if( $reaberturaRN->isNecessarioReabrirProcedimento( $objProcedimentoDTO ) ) {		
        		$reaberturaRN->reabrirProcessoApi($objProcedimentoDTO);
        	}
        	
        	$params['diretoProcessoIndicado']=true;
        	
            $this->setProcedimentoDTO($params['id_procedimento']);
        }

        if($objProcedimentoDTO->getStrStaEstadoProtocolo() == 3){
            $objRelProtocoloProtocoloDTO = new RelProtocoloProtocoloDTO();
            $objRelProtocoloProtocoloDTO->retDblIdProtocolo1();
            $objRelProtocoloProtocoloDTO->retStrProtocoloFormatadoProtocolo1();
            $objRelProtocoloProtocoloDTO->setDblIdProtocolo2($objProcedimentoDTO->getDblIdProcedimento());
            $objRelProtocoloProtocoloDTO->setStrStaAssociacao(RelProtocoloProtocoloRN::$TA_PROCEDIMENTO_ANEXADO);

            $objRelProtocoloProtocoloRN = new RelProtocoloProtocoloRN();
            $objRelProtocoloProtocoloDTO = $objRelProtocoloProtocoloRN->consultarRN0841($objRelProtocoloProtocoloDTO);

            $idUnidadeProcesso = $this->retornaUltimaUnidadeProcessoAberto($objRelProtocoloProtocoloDTO->getDblIdProtocolo1());

        }else{
            $idUnidadeProcesso = $this->retornaUltimaUnidadeProcessoAberto($this->getProcedimentoDTO()->getDblIdProcedimento());
        }

		//Remetentes
		$idsParticipantes = array();

		$objParticipante  = new ParticipanteDTO();
		$objParticipante->setDblIdProtocolo($this->getProcedimentoDTO()->getDblIdProcedimento());
		$objParticipante->setNumIdContato($this->getContatoDTOUsuarioLogado()->getNumIdContato());
		$objParticipante->setNumIdUnidade($idUnidadeProcesso);
		$objParticipante->setStrStaParticipacao(ParticipanteRN::$TP_REMETENTE);
		$objParticipante->setNumSequencia(0);
		$idsParticipantes[] = $objParticipante;

		//Interessados
		// Processo Principal - Interessados
		$objParticipanteProcPrincDTO = new ParticipanteDTO();
		$objParticipanteProcPrincDTO->retNumIdParticipante();
		$objParticipanteProcPrincDTO->retNumIdContato();
		$objParticipanteProcPrincDTO->retNumIdUnidade();
		$objParticipanteProcPrincDTO->retStrStaParticipacao();
		$objParticipanteProcPrincDTO->retStrNomeContato();
		$objParticipanteProcPrincDTO->retNumSequencia();
		$objParticipanteProcPrincDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());
		$objParticipanteProcPrincDTO->setStrStaParticipacao(ParticipanteRN::$TP_INTERESSADO);	        

		$objParticipanteProcPrincRN = new ParticipanteRN();
		$arrobjParticipanteProcPrinc = $objParticipanteProcPrincRN->listarRN0189($objParticipanteProcPrincDTO);            
		// Processo Principal - Interessados - FIM

		// Processo - Interessados
		$i=0;
		foreach($arrobjParticipanteProcPrinc as $objParticipanteProcPrinc){
			$objParticipante  = new ParticipanteDTO();
			$objParticipante->setDblIdProtocolo($this->getProcedimentoDTO()->getDblIdProcedimento());
			$objParticipante->setNumIdContato($objParticipanteProcPrinc->getNumIdContato());
			$objParticipante->setNumIdUnidade($objParticipanteProcPrinc->getNumIdUnidade());
			$objParticipante->setStrStaParticipacao(ParticipanteRN::$TP_INTERESSADO);
			$objParticipante->setNumSequencia($i);
			$idsParticipantes[] = $objParticipante;
			$i++;
		}

		$objMdPetParticipanteRN = new MdPetParticipanteRN();
		$arrInteressado = array();
		$arrInteressado[0] = $this->getProcedimentoDTO()->getDblIdProcedimento();
		$arrInteressado[1] = $idsParticipantes;
		//$arrInteressado[2] = $idsRemententes;
		$objMdPetParticipanteRN->setInteressadosRemetentesProcedimentoDocumento( $arrInteressado );            
		// Processo - Interessados - FIM


		$arrDocApi = MdPetIntercorrenteINT::montarArrDocumentoAPI($params['id_procedimento'],$params['hdnTbDocumento']);

		$arrObjReciboDocPet = $this->incluirDocumentosApi($this->getProcedimentoDTO(), $arrDocApi);

        $objReciboRN = new ReciboPeticionamentoRN();
        $objReciboDTO = $objReciboRN->gerarReciboSimplificadoIntercorrente($arrDadosRecibo);

        if (count($objReciboDTO) > 0) {
            $this->setReciboDTO($objReciboDTO);
            $this->cadastrarReciboDocumentoAnexo(array($objReciboDTO, $arrObjReciboDocPet));
            $documentoReciboDTO = $this->montarReciboIntercorrente($params);

			//Remetentes
			$idsParticipantes = array();

			$objParticipante  = new ParticipanteDTO();
			$objParticipante->setDblIdProtocolo($documentoReciboDTO->getDblIdDocumento());
			$objParticipante->setNumIdContato($this->getContatoDTOUsuarioLogado()->getNumIdContato());
			$objParticipante->setNumIdUnidade($idUnidadeProcesso);
			$objParticipante->setStrStaParticipacao(ParticipanteRN::$TP_REMETENTE);
			$objParticipante->setNumSequencia(0);
			$idsParticipantes[] = $objParticipante;

			// Recibo - Interessados
			$i=0;
			foreach($arrobjParticipanteProcPrinc as $objParticipanteProcPrinc){
				$objParticipante  = new ParticipanteDTO();
				$objParticipante->setDblIdProtocolo($documentoReciboDTO->getDblIdDocumento());
				$objParticipante->setNumIdContato($objParticipanteProcPrinc->getNumIdContato());
				$objParticipante->setNumIdUnidade($objParticipanteProcPrinc->getNumIdUnidade());
				$objParticipante->setStrStaParticipacao(ParticipanteRN::$TP_INTERESSADO);
				$objParticipante->setNumSequencia($i);
				$idsParticipantes[] = $objParticipante;
				$i++;
			}

			$objMdPetParticipanteRN = new MdPetParticipanteRN();
			$arrInteressado = array();
			$arrInteressado[0] = $documentoReciboDTO->getDblIdDocumento();
			$arrInteressado[1] = $idsParticipantes;
			//$arrInteressado[2] = $idsRemententes;

			$objMdPetParticipanteRN->setInteressadosRemetentesProcedimentoDocumento( $arrInteressado );            
			// Recibo - Interessados - FIM

			$this->setDocumentoRecibo($documentoReciboDTO);
			$this->enviarEmail($params);
			
			// obtendo a ultima atividade informada para o processo, para marcar
			// como nao visualizada, deixando assim o processo marcado como "vermelho"
			// (status de Nao Visualizado) na listagem da tela "Controle de processos"
			//trecho comentado para preservar apresentacao do "icone amarelo" na tela de Controle de Processos
			/*
			$atividadeRN = new AtividadeRN();
			$atividadeBD = new AtividadeBD( $this->getObjInfraIBanco() );
			$atividadeDTO = new AtividadeDTO();
			$atividadeDTO->retTodos();
			$atividadeDTO->setDblIdProtocolo( $this->getProcedimentoDTO()->getDblIdProcedimento() );
			$atividadeDTO->setOrd("IdAtividade", InfraDTO::$TIPO_ORDENACAO_DESC);
			$ultimaAtividadeDTO = $atividadeRN->listarRN0036( $atividadeDTO );
						
			//alterar a ultima atividade criada para nao visualizado
			if( $ultimaAtividadeDTO != null && count( $ultimaAtividadeDTO ) > 0){
				$ultimaAtividadeDTO[0]->setNumTipoVisualizacao( AtividadeRN::$TV_NAO_VISUALIZADO );
				$atividadeBD->alterar( $ultimaAtividadeDTO[0] );
			} */
			
            return array(
                'recibo'    => $objReciboDTO,
                'documento' => $documentoReciboDTO
            );
            //Gerar Recibo e executar javascript para fechar janela filha e redirecionar janela pai para a tela de detalhes do recibo que foi gerado]
            
            
        }

        return false;
    }

    private function getDocumentoRecibo()
    {
        return $this->documentoRecibo;
    }

    private function setDocumentoRecibo($documentoDTO)
    {
        $this->documentoRecibo = $documentoDTO;
    }

    private function simularLogin($idUnidade)
    {
        SessaoSEI::getInstance()->simularLogin(null, null, SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno(), $idUnidade);
        $this->setUnidadeDTO($idUnidade);
    }

    private function validarCadastro($params){
        // Bloco de validações
        $objInfraException = new InfraException();
        $this->validarSenhaSei($params['senhaSEI'], $objInfraException);
        $this->validarDocumentos($params['hdnTbDocumento'], $objInfraException);
        $this->validarNumIdProcedimento($params['id_procedimento'], $objInfraException);
        $this->validarNumIdTipoProcedimento($params['id_tipo_procedimento'], $objInfraException);
        $objInfraException->lancarValidacoes();
    }

    private function validarSenhaSei($senha, InfraException $objInfraException){
        if (InfraString::isBolVazia($senha)){
            $objInfraException->adicionarValidacao('Senha não informada.');
        }
        $objProcessoPeticionamentoRN = new ProcessoPeticionamentoRN();
        $objProcessoPeticionamentoRN->validarSenha(array('senhaSEI' => $senha));
    }

    private function validarDocumentos($hdnTbDocumento, InfraException $objInfraException){
        if (InfraString::isBolVazia($hdnTbDocumento)){
            $objInfraException->adicionarValidacao('Nenhum documento foi enviado.');
        }
    }

    private function validarNumIdProcedimento($numIdProcedimento, InfraException $objInfraException){
        if (InfraString::isBolVazia($numIdProcedimento)){
            $objInfraException->adicionarValidacao('Processo não informado.');
        }
    }

    private function validarNumIdTipoProcedimento($numIdTipoProcedimento, InfraException $objInfraException){
        if (InfraString::isBolVazia($numIdTipoProcedimento)){
            $objInfraException->adicionarValidacao('Senha não informada.');
        }
    }

    private function setUnidadeDTO($numIdUnidade)
    {
        $objUnidadeConsultaDTO = new UnidadeDTO();
        $objUnidadeConsultaDTO->retTodos(true);
        $objUnidadeConsultaDTO->setNumIdUnidade($numIdUnidade);
        $objUnidadeRN = new UnidadeRN();
        $this->objUnidadeDTO = $objUnidadeRN->consultarRN0125($objUnidadeConsultaDTO);
        $this->setOrgaoDTO($this->objUnidadeDTO->getNumIdOrgao());
    }

    private function getUnidadeDTO()
    {
        return $this->objUnidadeDTO;
    }

    private function setOrgaoDTO($numIdOrgao)
    {
        $orgaoDTO = new OrgaoDTO();
        $orgaoDTO->retTodos();
        $orgaoDTO->retStrEmailContato();
        $orgaoDTO->setNumIdOrgao( $numIdOrgao );
        $orgaoDTO->setStrSinAtivo('S');
        $orgaoRN = new OrgaoRN();
        $this->objOrgaoDTO = $orgaoRN->consultarRN1352($orgaoDTO);

    }

    private function getOrgaoDTO()
    {
        return $this->objOrgaoDTO;
    }

    private function setCargoDTO($numIdCargo)
    {
        $cargoDTO = new CargoDTO();
        $cargoDTO->retNumIdCargo();
        $cargoDTO->retStrExpressao();
        $cargoDTO->retStrSinAtivo();
        $cargoDTO->setNumIdCargo( $numIdCargo );
        $cargoDTO->setStrSinAtivo('S');
        $cargoRN = new CargoRN();
        $this->objCargoDTO = $cargoRN->consultarRN0301($cargoDTO);
    }

    private function getCargoDTO()
    {
        return $this->objCargoDTO;
    }

    private function getUsuarioDTO()
    {
        if($this->objUsuarioDTO === null){
            $objUsuarioDTO = new UsuarioDTO();
            $objUsuarioDTO->retTodos();
            $objUsuarioDTO->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );

            $objUsuarioRN = new UsuarioRN();
            $this->objUsuarioDTO = $objUsuarioRN->consultarRN0489( $objUsuarioDTO );
        }
        return $this->objUsuarioDTO;

    }

    private function setProcedimentoDTO($idProcedimento)
    {
        $objProcedimentoDTO = new ProcedimentoDTO();
        $objProcedimentoRN = new ProcedimentoRN();
        $objProcedimentoDTO->setDblIdProcedimento($idProcedimento);
        $objProcedimentoDTO->retDblIdProcedimento();
        $objProcedimentoDTO->retStrProtocoloProcedimentoFormatado();
        $objProcedimentoDTO->retTodos(true);
        $this->objProcedimentoDTO = $objProcedimentoRN->consultarRN0201($objProcedimentoDTO);
    }

    private function getProcedimentoDTO()
    {
        return $this->objProcedimentoDTO;
    }

    private function getSenha(){
        return $this->senha;
    }

    private function setSenha($senha){
        $this->senha = $senha;
    }

    private function setReciboDTO($reciboDTO){
        $this->reciboDTO = $reciboDTO;
    }

    private function getReciboDTO()
    {
        return $this->reciboDTO;
    }

    private function setParticipante($participanteDTO)
    {
        $this->participantesDTO = $participanteDTO;
    }

    private function getParticipanteDTO()
    {
        return $this->participantesDTO;
    }

    private function addDocumentos($documento){
        $this->arrDocumentos[] = $documento;
    }

    private function getDocumentos(){
        return $this->arrDocumentos;
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