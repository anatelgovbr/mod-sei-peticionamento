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
		$objAtividadeDTO = new AtividadeDTO();
		$objAtividadeRN  = new AtividadeRN();
		$objAtividadeDTO->setDblIdProcedimentoProtocolo($objProcedimentoDTO->getDblIdProcedimento());
		$objAtividadeDTO->retNumIdUnidade();
		$arrObjAtividUnidTramitado = $objAtividadeRN->listarRN0036($objAtividadeDTO);

		$arr = array();
		if(count($arrObjAtividUnidTramitado) > 0){
			foreach($arrObjAtividUnidTramitado as $objAtividadeDTO){
			      	$idUnidade = $objAtividadeDTO->getNumIdUnidade();
					array_push($arr, $idUnidade);
			}
		}

		$arrIdsUnidades = array_unique($arr);
		$objUnidadeDTO = new UnidadeDTO();
		$objUnidadeRN = new UnidadeRN();

		$objUnidadeDTO->setStrSinAtivo('S');
		$objUnidadeDTO->setNumIdUnidade($arrIdsUnidades, InfraDTO::$OPER_IN);
		$objUnidadeDTO->retNumIdUnidade();
		$count = $objUnidadeRN->contarRN0128($objUnidadeDTO);

		if($count == 0){
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
//ini_set('xdebug.var_display_max_depth', 10); ini_set('xdebug.var_display_max_children', 256); ini_set('xdebug.var_display_max_data', 1024); echo '<pre>';
//var_dump($arrObjAtividadeDTO); echo '</pre>'; exit;
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

        $idUnidadeAbrirNovoProcesso = $this->retornaUltimaUnidadeProcessoAberto($objProcedimentoDTO->getDblIdProcedimento());
//ini_set('xdebug.var_display_max_depth', 10); ini_set('xdebug.var_display_max_children', 256); ini_set('xdebug.var_display_max_data', 1024); echo '<pre>';
//var_dump($idUnidadeAbrirNovoProcesso); echo '</pre>'; exit;
        //Salva um processo do tipo padrão selecionado
        $this->simularLogin($idUnidadeAbrirNovoProcesso);

        $objEntradaGerarProcedimentoAPI = new EntradaGerarProcedimentoAPI();
        $arrProcedimentoRelacionado = array($objProcedimentoDTO->getDblIdProcedimento());

        $objProcedimentoAPI = new ProcedimentoAPI();
        $objProcedimentoAPI->setIdTipoProcedimento($objCriterioIntercorrenteDTO->getNumIdTipoProcedimento());
        $objEntradaGerarProcedimentoAPI->setProcedimento($objProcedimentoAPI);
        $objEntradaGerarProcedimentoAPI->setProcedimentosRelacionados($arrProcedimentoRelacionado);
        //$objEntradaGerarProcedimentoAPI->setDocumentos($arrObjDocumentoAPI);
        $objSEIRN = new SeiRN();
        return $objSEIRN->gerarProcedimento($objEntradaGerarProcedimentoAPI);
    }

    /**
     * Função responsável por Retornar a última unidade em que o processo foi aberto
     * @param $idProcedimento
     * @return  string $idUnidade
     * @since  19/12/2016
     * @author Jaqueline Mendes <jaqueline.mendes@castgroup.com.br>
     */
    private function retornaUltimaUnidadeProcessoAberto($idProcedimento){
        $objSEIRN = new SeiRN();
        $objEntradaConsultaProcApi = new EntradaConsultarProcedimentoAPI();
        $objEntradaConsultaProcApi->setIdProcedimento($idProcedimento);
        $objEntradaConsultaProcApi->setSinRetornarUltimoAndamento('S');
        /**
         * @var $saidaConsultarProcedimentoAPI SaidaConsultarProcedimentoAPI
         */
        $saidaConsultarProcedimentoAPI = $objSEIRN->consultarProcedimento($objEntradaConsultaProcApi);


        /*
        $objProcedimentoHistoricoDTO = new ProcedimentoHistoricoDTO();
        $objProcedimentoHistoricoDTO->setDblIdProcedimento($idProcedimento);
        $objProcedimentoRN = new ProcedimentoRN();
        $objProcedimentoDTOHistorico = $objProcedimentoRN->consultarHistoricoRN1025($objProcedimentoHistoricoDTO);
        $arrObjAtividadeDTOHistorico = $objProcedimentoDTOHistorico->getArrObjAtividadeDTO();
        */

//        ini_set('xdebug.var_display_max_depth', 10); ini_set('xdebug.var_display_max_children', 256); ini_set('xdebug.var_display_max_data', 1024); echo '<pre>';
//        var_dump($arrObjAtividadeDTOHistorico);
//        var_dump($saidaConsultarProcedimentoAPI); echo '</pre>'; exit;
        /**
         * @var $ultimoAndamento AndamentoAPI
         */
        $ultimoAndamento = $saidaConsultarProcedimentoAPI->getUltimoAndamento();
        /**
         * @var $objUnidadeAPI UnidadeAPI
         */
        $objUnidadeAPI = $ultimoAndamento->getUnidade();


        return $objUnidadeAPI->getIdUnidade();
    }

    protected function reabrirProcessoApiConectado(ProcedimentoDTO $objProcedimentoDTO) {
        $objSEIRN = new SeiRN();
        //Reabre o Processo quando necessário de Critério Intercorrente
        $objEntradaConsultaProcApi = new EntradaConsultarProcedimentoAPI();
        $objEntradaConsultaProcApi->setIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
        $objEntradaConsultaProcApi->setSinRetornarUnidadesProcedimentoAberto('S');

        $ret = $objSEIRN->consultarProcedimento($objEntradaConsultaProcApi);
        $arrUnidadesAberto = $ret->getUnidadesProcedimentoAberto();
        $unidadesAberto = count($arrUnidadesAberto);
//        var_dump($unidadesAberto); echo '</pre>'; exit;

        if ($unidadesAberto < 0) {
            return false;
        }
        $objAtividadeDTO = new AtividadeDTO();
        $objAtividadeDTO->setDblIdProcedimentoProtocolo($objProcedimentoDTO->getDblIdProcedimento());
        $idUnidadeReabrirProcesso = $this->retornaUltimaUnidadeProcessoConcluido($objAtividadeDTO);

//        ini_set('xdebug.var_display_max_depth', 10); ini_set('xdebug.var_display_max_children', 256); ini_set('xdebug.var_display_max_data', 1024); echo '<pre>';
//        var_dump($idUnidadeReabrirProcesso); echo '</pre>'; exit;

        if (!$idUnidadeReabrirProcesso) {
            return true;
        }

        $this->simularLogin($idUnidadeReabrirProcesso);

        $objEntradaReabrirProcessoAPI = new EntradaReabrirProcessoAPI();
        $objEntradaReabrirProcessoAPI->setIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
        $objEntradaReabrirProcessoAPI->setProtocoloProcedimento($objProcedimentoDTO->getStrProtocoloProcedimentoFormatado());

        $objSEIRN->reabrirProcesso($objEntradaReabrirProcessoAPI);
        return true;
    }

    protected function incluirDocumentosApi($objProcedimentoDTO, $arrObjDocumentoAPI)
    {
        $arrObjReciboDocPet = array();
        $objSEIRN = new SeiRN();
        $idUnidadeProcesso = $this->retornaUltimaUnidadeProcessoAberto($objProcedimentoDTO->getDblIdProcedimento());
        $this->simularLogin($idUnidadeProcesso);
        /*
        $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
        $idSerieParam = $objInfraParametro->getValor('ID_SERIE_RECIBO_MODULO_PETICIONAMENTO');
        */
        foreach ($arrObjDocumentoAPI as $documentoAPI) {
            /* @var $documentoAPI DocumentoAPI */
            //$documentoAPI->setIdSerie($idSerieParam);
            //$documentoAPI->set
            $saidaIncluirDocumentoAPI = $objSEIRN->incluirDocumento($documentoAPI);
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
            //setando um tipo de conferencia padrao (que sera removido depois), apenas para passar na validação
            $documentoDTO->setNumIdTipoConferencia(1);
            $documentoAlteracaoDTO = new DocumentoDTO();
            $documentoAlteracaoDTO->retDblIdDocumento();
            $documentoAlteracaoDTO->retNumIdTipoConferencia();
            $documentoAlteracaoDTO->setDblIdDocumento( $documentoDTO->getDblIdDocumento() );
            $documentoAlteracaoDTO = $documentoRN->consultarRN0005( $documentoAlteracaoDTO );

            $documentoAlteracaoDTO->setNumIdTipoConferencia(1);
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
        $objParticipanteDTO->setNumIdUnidade( $objUnidadeDTO->getNumIdUnidade() );
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

        $documentoRN = new DocumentoRN();
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
        $objProcedimentoDTO->retDblIdProcedimento();
        $objProcedimentoDTO->retStrProtocoloProcedimentoFormatado();
        $objProcedimentoDTO = $objProcedimentoRN->consultarRN0201($objProcedimentoDTO);

        $objCriterioIntercorrenteRN = new CriterioIntercorrentePeticionamentoRN();
        $objCriterioIntercorrenteDTO = $objCriterioIntercorrenteRN->retornarCriterioPorTipoProcesso($params['id_tipo_procedimento']);

        // Verifica se o processo possui critério intercorrente cadastrado
        //Se não possui busca o padrão e cria um processo relacionado ao processo selecionado
        $arrObjReciboDocPet = array();
        $arrDadosRecibo = array();
        $arrDadosRecibo['idProcedimento'] = $params['id_procedimento'];

        if ($objCriterioIntercorrenteDTO->getStrSinCriterioPadrao() == 'S') {


            $objSaidaGerarProcedimentoAPI = $this->gerarProcedimentoApi(array($objProcedimentoDTO, $objCriterioIntercorrenteDTO));
            $arrDadosRecibo['idProcedimentoRel'] = $params['id_procedimento'];
            $arrDadosRecibo['idProcedimento'] = $objSaidaGerarProcedimentoAPI->getIdProcedimento();
            $params['id_procedimento'] = $objSaidaGerarProcedimentoAPI->getIdProcedimento();
            //$arrDadosRecibo['idProcedimento'] = ;
            //$arrObjReciboDocPet = $this->retornarDocumentosRecibo($objSaidaGerarProcedimentoAPI);
            //Se possui critérios intercorrentes setta os documentos no processo existente
            $this->setProcedimentoDTO($objSaidaGerarProcedimentoAPI->getIdProcedimento());
        } else {
//            var_dump('Padrão'); echo '</pre>'; exit;
            $this->reabrirProcessoApi($objProcedimentoDTO);
            $this->setProcedimentoDTO($params['id_procedimento']);
        }
        $arrDocApi = MdPetIntercorrenteINT::montarArrDocumentoAPI($params['id_procedimento'],$params['hdnTbDocumento']);

        /*
        ini_set('xdebug.var_display_max_depth', 10); ini_set('xdebug.var_display_max_children', 256); ini_set('xdebug.var_display_max_data', 1024); echo '<pre>';
        var_dump($arrDadosRecibo);
        var_dump($objProcedimentoDTO);
        var_dump($this->getProcedimentoDTO());
        echo '</pre>'; exit;
        */

        $arrObjReciboDocPet = $this->incluirDocumentosApi($this->getProcedimentoDTO(), $arrDocApi);
        $objReciboRN = new ReciboPeticionamentoRN();
        $objReciboDTO = $objReciboRN->gerarReciboSimplificadoIntercorrente($arrDadosRecibo);

        if (count($objReciboDTO) > 0) {
            $this->setReciboDTO($objReciboDTO);
            $this->cadastrarReciboDocumentoAnexo(array($objReciboDTO, $arrObjReciboDocPet));
            $documentoReciboDTO = $this->montarReciboIntercorrente($params);
            $this->setDocumentoRecibo($documentoReciboDTO);
            $this->enviarEmail($params);
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

}
?>