<?
/**
* ANATEL
*
* 07/04/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIndisponibilidadeRN extends InfraRN {
	
	
	public static $SIM = 'S';
	public static $NAO = 'N';
	public static $ID_TAREFA_PRORROGACAO = 'MD_PET_INTIMACAO_PRORROGACAO_AUTOMATICA_PRAZO_EXT';

	public function __construct() {
		parent::__construct ();
	}
	
	protected function inicializarObjInfraIBanco() {
		return BancoSEI::getInstance ();
	}
	
	/**
	 * Short description of method listarConectado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param
	 *        	$objMdPetIndisponibilidadeDTO
	 * @return mixed
	 */
	protected function listarConectado(MdPetIndisponibilidadeDTO $objMdPetIndisponibilidadeDTO) {
	
		try {
			//SessaoSEI::getInstance()->validarAuditarPermissao('conduta_litigioso_listar',__METHOD__,$objCondutaLitigiosoDTO);
	
			//Regras de Negocio
			//$objInfraException = new InfraException();
	
			//$objInfraException->lancarValidacoes();
			$objMdPetIndisponibilidadeBD = new MdPetIndisponibilidadeBD($this->getObjInfraIBanco());
			$ret = $objMdPetIndisponibilidadeBD->listar($objMdPetIndisponibilidadeDTO);
				
			return $ret;
		} catch (Exception $e) {
			throw new InfraException ('Erro listando Indisponibilidade Peticionamento.', $e);
		}
	}
	
	public function listarValoresProrrogacao(){
		
		try{
		$objArrMdPetProrrogacaoAutomaticaPrazosDTO = array();
		
		$objMdPetProrrogacaoAutomaticaPrazosDTO = new MdPetProrrogacaoAutomaticaPrazosDTO();
		$objMdPetProrrogacaoAutomaticaPrazosDTO->setStrSinProrrogacao(self::$SIM);
		$objMdPetProrrogacaoAutomaticaPrazosDTO->setStrDescricao('Sim');
		$objArrMdPetProrrogacaoAutomaticaPrazosDTO[] = $objMdPetProrrogacaoAutomaticaPrazosDTO;
		
		$objMdPetProrrogacaoAutomaticaPrazosDTO = new MdPetProrrogacaoAutomaticaPrazosDTO();
		$objMdPetProrrogacaoAutomaticaPrazosDTO->setStrSinProrrogacao(self::$NAO);
		$objMdPetProrrogacaoAutomaticaPrazosDTO->setStrDescricao('Não');
		$objArrMdPetProrrogacaoAutomaticaPrazosDTO[] = $objMdPetProrrogacaoAutomaticaPrazosDTO;
		
		return $objArrMdPetProrrogacaoAutomaticaPrazosDTO;
		}catch(Exception $e){
			throw new InfraException('Erro listando valores de Prorrogacao.',$e);
		}
	}
	
	
/**
	 * Short description of method consultarConectado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param  $objMdPetIndisponibilidadeDTO
	 * @return mixed
	 */
	protected function consultarConectado(MdPetIndisponibilidadeDTO $objMdPetIndisponibilidadeDTO) {
		try {
			
			// Valida Permissao			
			$objMdPetIndisponibilidadeBD = new MdPetIndisponibilidadeBD($this->getObjInfraIBanco());
			$ret = $objMdPetIndisponibilidadeBD->consultar($objMdPetIndisponibilidadeDTO);
			
			return $ret;
		} catch ( Exception $e ) {
			throw new InfraException('Erro consultando Indisponibilidade Peticionamento.', $e);
		}
	}
	
	/**
	 * Short description of method alterarControlado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param  $objMdPetIndisponibilidadeDTO
	 * @return mixed
	 */
	protected function alterarControlado(MdPetIndisponibilidadeDTO $objMdPetIndisponibilidadeDTO){
		try {
				
			// Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao ('md_pet_indisponibilidade_alterar', __METHOD__, $objMdPetIndisponibilidadeDTO);


			// Regras de Negocio
			$objInfraException = new InfraException ();
			
			$this->_validarDuplicidade($objInfraException, $objMdPetIndisponibilidadeDTO);
			$this->_validarTxtResumoIndisponibilidade($objInfraException, $objMdPetIndisponibilidadeDTO);
			
			$objInfraException->lancarValidacoes();
		
			$objMdPetIndisponibilidadeBD = new MdPetIndisponibilidadeBD($this->getObjInfraIBanco());
			$objMdPetIndisponibilidadeBD->alterar($objMdPetIndisponibilidadeDTO);

			$this->_controlarProrrogacoesIntimacoes();
			$this->_cadastrarDocumentoIndisponibilidadePeticionamento($objMdPetIndisponibilidadeDTO->getNumIdIndisponibilidade());
				
			// Auditoria
		} catch ( Exception $e ) {
			throw new InfraException ('Erro alterando Indisponibilidade Peticionamento, ', $e);
		}
	}
	
	
	
	/**
	 * Short description of method cadastrarControlado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param  $objMdPetIndisponibilidadeDTO
	 * @return mixed
	 */
	protected function cadastrarControlado(MdPetIndisponibilidadeDTO $objMdPetIndisponibilidadeDTO)
	{
		try {
			// Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_indisponibilidade_cadastrar', __METHOD__, $objMdPetIndisponibilidadeDTO);

			// Regras de Negocio
			$objInfraException = new InfraException();

			$this->_validarDuplicidade($objInfraException, $objMdPetIndisponibilidadeDTO);
			$this->_validarTxtResumoIndisponibilidade($objInfraException, $objMdPetIndisponibilidadeDTO);

			$objInfraException->lancarValidacoes();

			//Cadastrar Indisponibilidade
			$objMdPetIndisponibilidadeBD = new MdPetIndisponibilidadeBD($this->getObjInfraIBanco());
			$objMdPetIndisponibilidadeDTO->setStrSinAtivo('S');

			$objRetorno = $objMdPetIndisponibilidadeBD->cadastrar($objMdPetIndisponibilidadeDTO);

			$this->_controlarProrrogacoesIntimacoes();
			$this->_cadastrarDocumentoIndisponibilidadePeticionamento($objRetorno->getNumIdIndisponibilidade());

			return $objRetorno;
		} catch (Exception $e) {
			throw new InfraException ('Erro cadastrando Tamanho de Arquivo Peticionamento.', $e);
		}

	}

	private function _controlarProrrogacoesIntimacoes()
	{
	  $dtPostFim        = array_key_exists('txtDtFim', $_POST) && $_POST['txtDtFim'] != '' ? $_POST['txtDtFim']  : false;
	  $arrDtInicio      = explode(' ', $_POST['txtDtInicio']);
	  $arrDtFim         = explode(' ', $dtPostFim);
	  $dataInicioIndisp = $arrDtInicio[0]. ' 00:00:00';
	  $dataFimIndisp    = $arrDtFim[0]. ' 23:59:59';
	  $sinProrrogar     = $_POST['hdnSinProrrogacao'] == 'S';

		if($sinProrrogar && $dtPostFim)
		{
			$objDTOS = $this->_retornaObjsIntimacoesProrrogacao($dataInicioIndisp, $dataFimIndisp);

			if (count($objDTOS) > 0)
			{
				$dtProrrogacao = $this->_retornaDataProrrogacao($dataFimIndisp);
				$this->_efetivarProrrogacoes($objDTOS, $dtProrrogacao);
				$this->_verificaIndisponibilidadesNaDataProrrogada($dtProrrogacao);
			}
		}
	}

	private function _retornaObjsIntimacoesProrrogacao($dataInicioIndisp, $dataFimIndisp, $consultaDtLimite = true){

		$objMdPetIntTpRespDestDTO = new MdPetIntRelTipoRespDestDTO();
		$objMdPetIntTpRespDestDTO->retTodos();

		if($consultaDtLimite)
		{
			/* Pesquisa os objetos que possuem data prorrogada null e data limite no periodo da indisponibilidade OU se data prorrogada not null e data prorrogada no periodo da indisponibilidade*/
			$objMdPetIntTpRespDestDTO->adicionarCriterio(array('DataProrrogada', 'DataLimite', 'DataLimite', 'DataProrrogada', 'DataProrrogada', 'DataProrrogada'),
				array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_MAIOR_IGUAL, InfraDTO::$OPER_MENOR_IGUAL, InfraDTO::$OPER_DIFERENTE, InfraDTO::$OPER_MAIOR_IGUAL, InfraDTO::$OPER_MENOR_IGUAL),
				array(null, $dataInicioIndisp, $dataFimIndisp, null, $dataInicioIndisp, $dataFimIndisp),
				array(InfraDTO::$OPER_LOGICO_AND, InfraDTO::$OPER_LOGICO_AND, InfraDTO::$OPER_LOGICO_OR, InfraDTO::$OPER_LOGICO_AND, InfraDTO::$OPER_LOGICO_AND));
		}else
		{
			$objMdPetIntTpRespDestDTO->adicionarCriterio(array('DataProrrogada', 'DataProrrogada', 'DataProrrogada'),
				array(InfraDTO::$OPER_DIFERENTE, InfraDTO::$OPER_MAIOR_IGUAL, InfraDTO::$OPER_MENOR_IGUAL),
				array(null, $dataInicioIndisp, $dataFimIndisp),
				array(InfraDTO::$OPER_LOGICO_AND, InfraDTO::$OPER_LOGICO_AND));
		}

		$objMdPetIntTpRespDestRN = new MdPetIntRelTipoRespDestRN();

		$objDTOS = $objMdPetIntTpRespDestRN->listar($objMdPetIntTpRespDestDTO);

		return $objDTOS;
	}

	private function _efetivarProrrogacoes($objDTOS, $dtProrrogacao)
	{
	 $objMdPetIntTpRespDestRN = new MdPetIntRelTipoRespDestRN();
	 $objMdPetUsuRN           = new MdPetIntUsuarioRN();
	 $idUsuarioIntimacao      = $objMdPetUsuRN->getObjUsuarioPeticionamento(true);

	if(count($objDTOS) > 0)
	{
		foreach ($objDTOS as $objDTO)
		{
			$objDTO->setDthDataProrrogada($dtProrrogacao);
			$objMdPetIntTpRespDestRN->alterar($objDTO);
			$this->lancarAndamentoProrrogacao(array($objDTO->getNumIdMdPetIntRelDest(), $dtProrrogacao, $idUsuarioIntimacao));
		}
	}

	}

	private function _retornaIndisponibilidadesPeriodoProrrogado($dtProrrogacao){
		$arrDtProrrogacaoInicio = explode(' ', $dtProrrogacao);
		$arrDtProrrogacaoFim    = explode(' ', $dtProrrogacao);
		$dtProrrogacaoInicio    = $arrDtProrrogacaoInicio[0].' 00:00:00';
		$dtProrrogacaoFim       = $arrDtProrrogacaoFim[0].' 23:59:59';

		$objMdPetIndisponibilidadeDTO = new MdPetIndisponibilidadeDTO();
		$objMdPetIndisponibilidadeDTO->adicionarCriterio(array('DataInicio', 'DataInicio', 'DataFim', 'DataFim'),
			array(InfraDTO::$OPER_MAIOR_IGUAL, InfraDTO::$OPER_MENOR_IGUAL, InfraDTO::$OPER_MAIOR_IGUAL, InfraDTO::$OPER_MENOR_IGUAL),
			array($dtProrrogacaoInicio, $dtProrrogacaoFim, $dtProrrogacaoInicio, $dtProrrogacaoFim),
			array(InfraDTO::$OPER_LOGICO_AND, InfraDTO::$OPER_LOGICO_OR, InfraDTO::$OPER_LOGICO_AND));

		$objMdPetIndisponibilidadeDTO->setOrdDthDataFim(InfraArray::$TIPO_ORDENACAO_ASC);
		$objMdPetIndisponibilidadeDTO->setStrSinProrrogacao('S');
		$objMdPetIndisponibilidadeDTO->setNumMaxRegistrosRetorno(1);
		$objMdPetIndisponibilidadeDTO->retTodos();
		$objMdPetIndisponibilidadeRN = new MdPetIndisponibilidadeRN();
		$objDTO = $objMdPetIndisponibilidadeRN->consultar($objMdPetIndisponibilidadeDTO);

		return $objDTO;
	}

	private function _verificaIndisponibilidadesNaDataProrrogada($dtProrrogacaoAnterior){

		$objDTOIndisp = $this->_retornaIndisponibilidadesPeriodoProrrogado($dtProrrogacaoAnterior);

		if(!is_null($objDTOIndisp)){
			$objDTOS           = $this->_retornaObjsIntimacoesProrrogacao($objDTOIndisp->getDthDataInicio(), $objDTOIndisp->getDthDataFim(), false);

			if(count($objDTOS) > 0)
			{
				$novaDtProrrogacao = $this->_retornaDataProrrogacao($objDTOIndisp->getDthDataFim());

				$this->_efetivarProrrogacoes($objDTOS, $novaDtProrrogacao);
				if(count($objDTOS) > 0) {
					$this->_verificaIndisponibilidadesNaDataProrrogada($novaDtProrrogacao);
				}
			}
		}
	}


	private function _retornaDataProrrogacao($dataFimIndisp){
	  $objMdPetIntPrazoRN = new MdPetIntPrazoRN();
	  $proximoDiaUtil =  $objMdPetIntPrazoRN->somarDiaUtil(1 , $dataFimIndisp);
      $dataRetorno    = $proximoDiaUtil. ' 23:59:59';

	  return $dataRetorno;
	}

	private function _validarTxtResumoIndisponibilidade($objInfraException, $objMdPetIndisponibilidadeDTO){
		if (InfraString::isBolVazia ($objMdPetIndisponibilidadeDTO->getStrResumoIndisponibilidade())) {
			$objInfraException->adicionarValidacao('Resumo da Indisponibilidade não informada.');
		}
		if (trim ( $objMdPetIndisponibilidadeDTO->getStrResumoIndisponibilidade () ) != '')
		{
			if (strlen ( $objMdPetIndisponibilidadeDTO->getStrResumoIndisponibilidade () ) > 500) {
				$objInfraException->adicionarValidacao('Resumo da Indisponibilidade possui tamanho superior a 500 caracteres.');
			}
		}
	}
	
	
	
	private function _validarDuplicidade($objInfraException, $objMdPetIndisponibilidadeDTO){
		// VALIDA DUPLICAÇÃO
		// VALIDACAO A SER EXECUTADA NA INSERÇAO DE NOVOS REGISTROS
		$objMdPetIndisponibilidadeDTO2 = new MdPetIndisponibilidadeDTO();
		$objMdPetIndisponibilidadeDTO2->setDthDataInicio($objMdPetIndisponibilidadeDTO->getDthDataInicio());
		$objMdPetIndisponibilidadeDTO2->setDthDataFim($objMdPetIndisponibilidadeDTO->getDthDataFim());
		
		$objMdPetIndisponibilidadeBD = new MdPetIndisponibilidadeBD($this->getObjInfraIBanco());
		
		if (!is_numeric($objMdPetIndisponibilidadeDTO->getNumIdIndisponibilidade())) {
				
			$ret = $objMdPetIndisponibilidadeBD->contar($objMdPetIndisponibilidadeDTO2);
				
			if ($ret > 0) {
				$objInfraException->adicionarValidacao ('Já existe o período de indisponibilidade (Início/Fim) cadastrado.');
			} // VALIDACAO A SER EXECUTADA QUANDO É FEITO UPDATE DE REGISTROS
				
		} else {
				
			$dtoValidacao = new MdPetIndisponibilidadeDTO();
			$dtoValidacao->setDthDataInicio($objMdPetIndisponibilidadeDTO->getDthDataInicio(), InfraDTO::$OPER_IGUAL);
			$dtoValidacao->setDthDataFim($objMdPetIndisponibilidadeDTO->getDthDataFim(), InfraDTO::$OPER_IGUAL);
			$dtoValidacao->setNumIdIndisponibilidade( $objMdPetIndisponibilidadeDTO->getNumIdIndisponibilidade(), InfraDTO::$OPER_DIFERENTE );
				
			$retDuplicidade = $objMdPetIndisponibilidadeBD->contar( $dtoValidacao );
				
			if ($retDuplicidade > 0) {
				$objInfraException->adicionarValidacao('Já existe o período de indisponibilidade (Início/Fim) cadastrado.');
			}
		}
		
	}
	
	private function _cadastrarDocumentoIndisponibilidadePeticionamento($idIndisponibilidade){
		$idDocumento = $_POST['hdnIdDocumento'];
		if ($idDocumento != '') {
			$idProcedimento = $this->_retornaIdProcedimentoGeradoDocumento($idDocumento);
			$objAcessoExternoDTO = $this->_retornaObjAcessoExterno($idDocumento, $idProcedimento);

			$objRN = new MdPetIndisponibilidadeDocRN();
			$objMdPetIndisponibilidadeDocDTO = new MdPetIndisponibilidadeDocDTO();
			$objMdPetIndisponibilidadeDocDTO->setDblIdDocumento($idDocumento);
			$objMdPetIndisponibilidadeDocDTO->setNumIdIndisponibilidade($idIndisponibilidade);
			$objMdPetIndisponibilidadeDocDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
			$objMdPetIndisponibilidadeDocDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
			$objMdPetIndisponibilidadeDocDTO->setDthInclusao(InfraData::getStrDataHoraAtual());
			$objMdPetIndisponibilidadeDocDTO->setStrSinAtivo('S');
			$objMdPetIndisponibilidadeDocDTO->setNumIdAcessoExterno($objAcessoExternoDTO->getNumIdAcessoExterno());
			$objDTO = $objRN->cadastrar($objMdPetIndisponibilidadeDocDTO);
		}
	}

	private function _retornaIdProcedimentoGeradoDocumento($idDocumento)
	{
		$objDocumentoRN = new DocumentoRN();

		if ($idDocumento != null && $idDocumento != '')
		{
			$objDocumentoDTO = new DocumentoDTO();
			$objDocumentoDTO->setDblIdDocumento($idDocumento);
			$objDocumentoDTO->retDblIdProcedimento();
			$objDocumentoDTO->setNumMaxRegistrosRetorno(1);

			$objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

			if(!is_null($objDocumentoDTO)) {
				return $objDocumentoDTO->getDblIdProcedimento();
			}
		}

		return null;
	}

	private function _retornaObjAcessoExterno($idDocumento, $idProcedimento)
	{
		$objMdPetIntUsuarioRN = new MdPetIntUsuarioRN();

		$idUsuario     = $objMdPetIntUsuarioRN->getObjUsuarioPeticionamento(true);
		$objContatoDTO = $objMdPetIntUsuarioRN->retornaObjContatoPorIdUsuario(array($idUsuario));

		$objAcessoExternoDTO = $this->_obterAcessoExternoSistema($idProcedimento, $objContatoDTO->getNumIdContato());

		return $objAcessoExternoDTO;
	}

	private function _obterAcessoExternoSistema($dlbIdProcedimento, $idContato){

			try {

				$objAcessoExternoDTO = new AcessoExternoDTO();
				$objAcessoExternoDTO->retNumIdAcessoExterno();
				$objAcessoExternoDTO->setDblIdProtocoloAtividade($dlbIdProcedimento);
				$objAcessoExternoDTO->setNumIdContatoParticipante($idContato);
				$objAcessoExternoDTO->setStrStaTipo(AcessoExternoRN::$TA_SISTEMA);
				$objAcessoExternoDTO->setNumMaxRegistrosRetorno(1);

				$objAcessoExternoRN = new AcessoExternoRN();
				$objAcessoExternoDTO = $objAcessoExternoRN->consultar($objAcessoExternoDTO);

				if ($objAcessoExternoDTO == null) {

					$objParticipanteDTO = new ParticipanteDTO();
					$objParticipanteDTO->retNumIdParticipante();
					$objParticipanteDTO->setNumIdContato($idContato);
					$objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_ACESSO_EXTERNO);
					$objParticipanteDTO->setDblIdProtocolo($dlbIdProcedimento);

					$objParticipanteRN = new ParticipanteRN();
					$objParticipanteDTO = $objParticipanteRN->consultarRN1008($objParticipanteDTO);

					if ($objParticipanteDTO == null) {
						$objParticipanteDTO = new ParticipanteDTO();
						$objParticipanteDTO->setDblIdProtocolo($dlbIdProcedimento);
						$objParticipanteDTO->setNumIdContato($idContato);
						$objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_ACESSO_EXTERNO);
						$objParticipanteDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
						$objParticipanteDTO->setNumSequencia(0);
						$objParticipanteDTO = $objParticipanteRN->cadastrarRN0170($objParticipanteDTO);
					}

					$objAcessoExternoDTO = new AcessoExternoDTO();
					$objAcessoExternoDTO->setNumIdParticipante($objParticipanteDTO->getNumIdParticipante());
					$objAcessoExternoDTO->setStrStaTipo(AcessoExternoRN::$TA_SISTEMA);

					$objAcessoExternoRN = new AcessoExternoRN();
					$objAcessoExternoDTO = $objAcessoExternoRN->cadastrar($objAcessoExternoDTO);
				}

				return $objAcessoExternoDTO;

			}catch(Exception $e){
				throw new InfraException('Erro obtendo acesso externo no processo para o sistema.',$e);
			}
		}

	

    protected function lancarAndamentoProrrogacaoControlado($arrParams){

		$idMdPetRelDest       = array_key_exists(0, $arrParams) ? $arrParams[0] : null;
		$dtProrrogada         = array_key_exists(1, $arrParams) ? $arrParams[1] : null;
		$arrDtSemHora         = !is_null($dtProrrogada) ? explode(' ',$dtProrrogada) : null;
		$dtSemHora            = is_array($arrDtSemHora) ? current($arrDtSemHora) : '';
		$idUsuarioPet         = array_key_exists(2, $arrParams) ? $arrParams[2] : null;
		$objMdPetIntRelDestRN = new MdPetIntRelDestinatarioRN();
        $objMdIntimacaoRN     = new MdPetIntimacaoRN();
        $objMdPetIntAceiteRN  = new MdPetIntAceiteRN();
        $idProcedimento       = null;
		$objMdPetIntimacao    = $objMdPetIntRelDestRN->getObjIntimacaoPorIdPetIntRelDest($idMdPetRelDest);
		$dtExpedicao          = $objMdPetIntimacao->getDthDataCadastro();
        $docPrinc             = $objMdIntimacaoRN->retornaDadosDocPrincipalIntimacao(array($objMdPetIntimacao->getNumIdMdPetIntimacao()));
		$objUnidadeDTO        = $objMdIntimacaoRN->getUnidadeIntimacao(array($objMdPetIntimacao->getNumIdMdPetIntimacao()));

		SessaoSEI::getInstance()->setBolHabilitada(false);
		SessaoSEI::getInstance()->simularLogin(null, null, $idUsuarioPet , $objUnidadeDTO->getNumIdUnidade() );


        if (count($docPrinc)>0){
            $idDoc = $docPrinc[3];
            $docForm = $docPrinc[0];
            $idProcedimento = $docPrinc[2];
        }

         $objEntradaLancarAndamentoAPI = new EntradaLancarAndamentoAPI();
         $objEntradaLancarAndamentoAPI->setIdProcedimento($idProcedimento);
         $objEntradaLancarAndamentoAPI->setIdTarefaModulo(MdPetIndisponibilidadeRN::$ID_TAREFA_PRORROGACAO);
         $arrObjAtributoAndamentoAPI[] = $objMdPetIntAceiteRN->retornaObjAtributoAndamentoAPI('DATA_EXPEDICAO_INTIMACAO', $dtExpedicao);
         $arrObjAtributoAndamentoAPI[] = $objMdPetIntAceiteRN->retornaObjAtributoAndamentoAPI('DATA_LIMITE_RESPOSTAS', $dtSemHora);
         $arrObjAtributoAndamentoAPI[] = $objMdPetIntAceiteRN->retornaObjAtributoAndamentoAPI('DOCUMENTO', $docForm, $idDoc);

         $objEntradaLancarAndamentoAPI->setAtributos($arrObjAtributoAndamentoAPI);

         $objSeiRN = new SeiRN();
         $objSeiRN->lancarAndamento($objEntradaLancarAndamentoAPI);
		 SessaoSEI::getInstance()->setBolHabilitada(true);
		 SessaoSEI::getInstance()->simularLogin(null, null, SessaoSEI::getInstance()->getNumIdUsuario() , SessaoSEI::getInstance()->getNumIdUnidadeAtual());
    }

		/**
		 * Short description of method desativarControlado
		 *
		 * @access protected
		 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
		 * @param  $arrMdPetIndisponibilidadeDTO
		 * @return void
		 */
		protected function desativarControlado($arrMdPetIndisponibilidadeDTO) {
		
			try {

				SessaoSEI::getInstance ()->validarAuditarPermissao('md_pet_indisponibilidade_desativar', __METHOD__ ,$arrMdPetIndisponibilidadeDTO);

				$objMdPetIndisponibilidadeBD = new MdPetIndisponibilidadeBD($this->getObjInfraIBanco());
					
				for($i = 0; $i < count($arrMdPetIndisponibilidadeDTO); $i ++) {
					$objMdPetIndisponibilidadeBD->desativar($arrMdPetIndisponibilidadeDTO[$i]);
				}
					
			} catch(Exception $e) {
				throw new InfraException ('Erro desativando Indisponibilidade Peticionamento.', $e );
			}
		}
		
		
		/**
		 * Short description of method reativarControlado
		 *
		 * @access protected
		 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
		 * @param  $arrMdPetIndisponibilidadeDTO
		 * @return void
		 */
		protected function reativarControlado($arrMdPetIndisponibilidadeDTO) {
		
			try {

				SessaoSEI::getInstance ()->validarAuditarPermissao('md_pet_indisponibilidade_reativar', __METHOD__,$arrMdPetIndisponibilidadeDTO);

				$objMdPetIndisponibilidadeBD = new MdPetIndisponibilidadeBD($this->getObjInfraIBanco());
					
				for($i = 0; $i < count($arrMdPetIndisponibilidadeDTO); $i ++) {
					$objMdPetIndisponibilidadeBD->reativar($arrMdPetIndisponibilidadeDTO[$i]);
				}
					
			} catch(Exception $e) {
				throw new InfraException ('Erro reativando Indisponibilidade Peticionamento.', $e );
			}
		}
		
		
		/**
		 * Short description of method excluirControlado
		 *
		 * @access protected
		 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
		 * @param  $arrMdPetIndisponibilidadeDTO
		 * @return void
		 */
		protected function excluirControlado($arrMdPetIndisponibilidadeDTO) {
		
			try {

				SessaoSEI::getInstance ()->validarAuditarPermissao('md_pet_indisponibilidade_excluir', __METHOD__,$arrMdPetIndisponibilidadeDTO);

				$objMdPetIndisponibilidadeBD = new MdPetIndisponibilidadeBD($this->getObjInfraIBanco());
				$objMdPetIndisponibilidadeDocRN = new MdPetIndisponibilidadeDocRN();
				
				for($i = 0; $i < count($arrMdPetIndisponibilidadeDTO); $i ++) {
					
					//Excluindo anexos relacionados
					$objMdPetIndisponibilidadeDocDTO = new MdPetIndisponibilidadeDocDTO();
					$objMdPetIndisponibilidadeDocDTO->retTodos();
					$objMdPetIndisponibilidadeDocDTO->setNumIdIndisponibilidade($arrMdPetIndisponibilidadeDTO[$i]->getNumIdIndisponibilidade(), InfraDTO::$OPER_IGUAL);
					$arrObjMdPetIndisponibilidadeDocDTO = $objMdPetIndisponibilidadeDocRN->listar($objMdPetIndisponibilidadeDocDTO);
				    					
					$objMdPetIndisponibilidadeDocRN->excluir($arrObjMdPetIndisponibilidadeDocDTO);
				    
				    //Excluindo Indisponibilidade
					$objMdPetIndisponibilidadeBD->excluir($arrMdPetIndisponibilidadeDTO[$i]);
				}
					
			} catch(Exception $e) {
				throw new InfraException ('Erro excluindo Indisponibilidade Peticionamento.', $e );
			}
		}
		
		//metodo customizado de upload visando permitir download posterior do arquivo
		public function processarUploadComRetornoDoNomeReal($strCampoArquivo, $strDirUpload, $bolArquivoTemporarioIdentificado = true){

			LimiteSEI::getInstance()->configurarNivel3();

			$ret = '';
			try{
		
				$_FILES[$strCampoArquivo]["name"] = str_replace(chr(0), '', $_FILES[$strCampoArquivo]["name"]);
		
				$arrStrNome = explode('.', $_FILES[$strCampoArquivo]["name"]);
		
				if (count($arrStrNome) < 2){
					$ret = 'ERRO#Nome do arquivo não possui extensão.';
				}else{
					if (in_array(str_replace(' ','',InfraString::transformarCaixaBaixa($arrStrNome[count($arrStrNome)-1])), array('php', 'php3', 'php4', 'phtml', 'sh' ,'cgi'))){
						$ret = 'ERRO#Extensão de arquivo não permitida.';
					}else{
		
						if (!isset($_FILES[$strCampoArquivo])){
							$ret = 'ERRO#Campo de arquivo "'.$strCampoArquivo.'" não foi enviado.';
						}else{
		
							if ($_FILES[$strCampoArquivo]["error"] != UPLOAD_ERR_OK){
		
								switch($_FILES[$strCampoArquivo]["error"]){
									
									case UPLOAD_ERR_INI_SIZE:
										$ret = 'ERRO#Tamanho do arquivo "'.$_FILES[$strCampoArquivo]["name"].'" excedeu o limite de '.ini_get('upload_max_filesize').'b permitido pelo servidor.';
										break;
		
									case UPLOAD_ERR_FORM_SIZE:
										$ret = 'ERRO#Tamanho do arquivo "'.$_FILES[$strCampoArquivo]["name"].'" excedeu o limite de '.$_POST['MAX_FILE_SIZE'].' bytes permitido pelo navegador.';
										break;
		
									case UPLOAD_ERR_PARTIAL:
										$ret = 'ERRO#Apenas uma parte do arquivo foi transferida.';
										break;
		
									case UPLOAD_ERR_NO_FILE:
										$ret = 'ERRO#Arquivo não foi transferido.';
										break;
		
									case UPLOAD_ERR_NO_TMP_DIR:
										$ret = 'ERRO#Diretório temporário para transferência não encontrado.';
										break;
		
									case UPLOAD_ERR_CANT_WRITE:
										$ret = 'ERRO#Erro gravando dados no servidor.';
										break;
		
									case UPLOAD_ERR_EXTENSION:
										$ret = 'ERRO#Transferência interrompida.';
										break;
		
									default:
										$ret = 'ERRO#Erro desconhecido tranferindo arquivo ['.$_FILES[$strCampoArquivo]["error"].'].';
										break;
								}
		
							}else {
		
								$strMime = null;
		
								if (function_exists(finfo_open)) {
									$finfo = finfo_open(FILEINFO_MIME_TYPE);
									$strMime = finfo_file($finfo, $_FILES[$strCampoArquivo]["tmp_name"]);
									finfo_close($finfo);
								}
		
								if ($strMime != null && strpos($strMime, 'text/x-php') !== false || strpos($strMime, 'text/x-shellscript') !== false) {
									$ret = 'ERRO#Tipo de arquivo não permitido.';
								}else{
		
									if (PaginaSEI::getInstance()->getObjInfraSessao() !== null) {
										$strUsuario = PaginaSEI::getInstance()->getObjInfraSessao()->getStrSiglaUsuario();
									} else {
										$strUsuario = 'anonimo';
									}
		
									$numTimestamp = time();
		
									if ($bolArquivoTemporarioIdentificado) {
										//[usuario][ddmmaaaa-hhmmss]-nomearquivo
										$strArquivo = InfraUtil::montarNomeArquivoUpload($strUsuario, $numTimestamp, $_FILES[$strCampoArquivo]["name"]);
									} else {
										$strArquivo = md5($strUsuario . mt_rand() . $numTimestamp . mt_rand() . $_FILES[$strCampoArquivo]["name"] . uniqid(mt_rand(), true));
									}
		
									if (file_exists($strDirUpload . '/' . $strArquivo)) {
										$ret = 'ERRO#Arquivo "' . $strArquivo . '" já existe no diretório de upload.';
									} else {
										try {
											//se der certo retorna o nome real do arquivo gerado
											if (!move_uploaded_file($_FILES[$strCampoArquivo]["tmp_name"], $strDirUpload . '/' . $strArquivo)) {
												$ret = 'ERRO#Erro movendo arquivo para o diretório de upload.';
											} else {
												$ret = $strDirUpload . '/' . $strArquivo;
											}

										} catch (Exception $e) {
											if (strpos(strtoupper($e->__toString()), 'PERMISSION DENIED') !== false) {
												$ret = 'ERRO#Permissão negada tentando mover o arquivo para o diretório de upload.';
											}
											throw $e;
										}
									}
								}
							}
						}
					}
				}
			}catch(Exception $e){
				$ret = 'ERRO#'.$e->__toString();
			}
				    
			if (substr($ret,0,5)=='ERRO#' && PaginaSEI::getInstance()->getObjInfraLog() instanceof InfraLog){
		
				$strTextoLog = '';
				
				if (PaginaSEI::getInstance()->getObjInfraSessao()!==null){
					
					if ( PaginaSEI::getInstance()->getObjInfraSessao()->getStrSiglaUsuario()!==null){
						
						$strTextoLog .= "Usuário: ". PaginaSEI::getInstance()->getObjInfraSessao()->getStrSiglaUsuario();
		
						if ( PaginaSEI::getInstance()->getObjInfraSessao()->getStrSiglaOrgaoUsuario()!==null){
							$strTextoLog .= '/'. PaginaSEI::getInstance()->getObjInfraSessao()->getStrSiglaOrgaoUsuario();
						}
					}
				}
		
				$strTextoLog .= "\nServidor: ". $_SERVER['SERVER_NAME'] . " (".$_SERVER['SERVER_ADDR'].")";
				$strTextoLog .= "\nErro: ".substr($ret,5);
				$strTextoLog .= "\nNavegador: ". $_SERVER['HTTP_USER_AGENT'];
				
				if (is_array($_GET)){
					$strTextoLog .= "\nGET:\n".print_r($_GET,true);
				}
		
				if (is_array($_FILES)) {
					$strTextoLog .= "\nFILES:\n" . print_r($_FILES, true);
				}
		
				try{
					PaginaSEI::getInstance()->getObjInfraLog()->gravar($strTextoLog);
				}catch(Exception $e){
					//Ignora, erro mais provavel queda da conexao com o banco
				}
			} 
		
			echo $ret;
			
		}

	protected function buscarDadosDocumentoConectado($idDocumento){
		$objDocumentoDTO = new DocumentoDTO();
		$objDocumentoDTO->setDblIdDocumento($idDocumento);
		$objDocumentoDTO->retDblIdDocumento();
		$objDocumentoDTO->retStrProtocoloDocumentoFormatado();
		$objDocumentoDTO->retArrObjAssinaturaDTO();
		$objDocumentoDTO->retStrStaDocumento();
		$objDocumentoDTO->retDtaGeracaoProtocolo();
		$objDocumentoDTO->retStrNomeSerie();
		$objDocumentoDTO->retNumIdSerie();
		$objDocumentoDTO->retDblIdProcedimento();
		$objDocumentoDTO->retStrNumero();
		$objDocumentoDTO->setNumMaxRegistrosRetorno(1);

		$objDocumentoRN = new DocumentoRN();
		$objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

		$dtDocumento = $this->_getDataDocumento($objDocumentoDTO);

		return $dtDocumento;
	}

	private function _getDataDocumento($objDocumentoDTO){
		$dthDocumento = '';

		if ($objDocumentoDTO->getStrStaDocumento() == DocumentoRN::$TD_EDITOR_INTERNO) {
			$arrAssinatura = $objDocumentoDTO->getArrObjAssinaturaDTO();
			if (count($arrAssinatura) > 0) {
				$objAssinaturaDTO = new AssinaturaDTO();
				$objAssinaturaDTO->setDblIdDocumento($objDocumentoDTO->getDblIdDocumento());
				$objAssinaturaDTO->retDthAberturaAtividade();
				$objAssinaturaDTO->setOrdDthAberturaAtividade(InfraDTO::$TIPO_ORDENACAO_ASC);
				$objAssinaturaRN = new AssinaturaRN();
				$arrObjAssinaturaDTO = $objAssinaturaRN->listarRN1323($objAssinaturaDTO);
				$countAss = $objAssinaturaRN->contarRN1324($objAssinaturaDTO);
				if ($countAss > 0) {
					$dthDocumento = $arrObjAssinaturaDTO[0]->getDthAberturaAtividade();
				}
			}

			if($dthDocumento == ''){
				$dthDocumento = $objDocumentoDTO->getDtaGeracaoProtocolo();
			}

		}

		if ($objDocumentoDTO->getStrStaDocumento() == DocumentoRN::$TD_EXTERNO) {
			$dthDocumento = $objDocumentoDTO->getDtaGeracaoProtocolo();
		}

		return $dthDocumento;
	}
	
}
?>