<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4Њ REGIУO
 *
 * 31/03/2017 - criado por marcelo.cast
 *
 * Versуo do Gerador de Cѓdigo: 1.40.0
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntAcessoExternoDocumentoRN extends InfraRN {

	/*constante que define numero de dias que sera concedido o acesso externo (definido no momento em 100 anos) */
	public static $NUMERO_DIAS_LIBERACAO_ACESSO_EXTERNO = 36500;

	//Vars para Sta Concessao
	public static $STA_INTERNO     = 'I';
	public static $STA_EXTERNO     = 'E';
	public static $STA_AGENDAMENTO = 'A';

	//Tipo Acesso
	public static $ACESSO_PARCIAL    = 'P';
	public static $ACESSO_INTEGRAL   = 'I';
	public static $NAO_POSSUI_ACESSO = 'N';

    public function __construct(){
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco(){
        return BancoSEI::getInstance();
    }
    
    private function retornaObjAtributoAndamentoAPI($nome, $valor, $id = null){
     
        $objAtributoAndamentoAPI = new AtributoAndamentoAPI();
        $objAtributoAndamentoAPI->setNome($nome);
        
        $objAtributoAndamentoAPI->setValor($valor);
        $objAtributoAndamentoAPI->setIdOrigem($id); //ID do prщdio, pode ser null
        
        return $objAtributoAndamentoAPI;
    }
    
    /* Mщtodo responsсvel por conceder acesso externo (integral ou parcial) aos documentos envolvidoas na intimaчуo */
    protected function concederAcessoExternoParaDocumentosControlado( MdPetIntAcessoExternoDocumentoDTO $dto){
    	
    	//cadastrando acesso externo para um usuario em todos os documentos passados
    	$idUsuario      = $dto->getNumIdUsuarioExterno();
    	$nomeUsuario    = $dto->getStrNomeUsuarioExterno();
    	$emailUsuario   = $dto->getStrEmailUsuarioExterno();
    	$idUnidade      = $dto->getNumIdUnidade();
    	$idParticipante = $dto->getNumIdParticipante();
    	$idProtocolo    = $dto->getDblIdProtocoloProcesso();
    	$isIntegral     = $dto->getStrSinVisualizacaoIntegral();
    	$motivoPadrao   = 'MOTIVO: Acesso Externo para documentos de Intimaчуo';
    	$strMotivo      = $dto->getStrMotivo() == '' ?  $motivoPadrao : $dto->getStrMotivo();

    	//lista dos docs
    	$arrDocs = $dto->getArrIdDocumentos();
    	$arrAcessoExterno = null;
    	
    	if( is_array( $arrDocs ) && count( $arrDocs ) > 0 ){

    		$arrAcessoExterno = array();

    		$unidDTO = new UnidadeDTO();
    		$unidRN = new UnidadeRN();
    		$unidDTO->retTodos( );
    		$unidDTO->setNumIdUnidade( $idUnidade );
    		
    		$unidDTO = $unidRN->consultarRN0125( $unidDTO );
    		
    		$emailUnidadeRN = new EmailUnidadeRN();
    		$emailUnidadeDTO = new EmailUnidadeDTO();
    		$emailUnidadeDTO->retTodos();
    		$emailUnidadeDTO->setNumIdUnidade( $idUnidade );
    		$strEmailUnidade = '';
    		$strEmailDestinatario = '';
    		
    		$arrEmailUnidade = $emailUnidadeRN->listar( $emailUnidadeDTO );
    		    		
    		if( is_array( $arrEmailUnidade ) && count( $arrEmailUnidade ) > 0 ){
    			$strEmailUnidade = $arrEmailUnidade[0]->getStrEmail();
    		}
    		
    			$objAcessoExternoPeticionamentoRN = new MdPetAcessoExternoRN();
    			$objAcessoExternoBD = new AcessoExternoBD( $this->getObjInfraIBanco() );
    			$objAtividadePetRN = new MdPetAtividadeRN();
    			
    			//cadastrandop atividade
    			$objAtividadeDTO = new AtividadeDTO();
    			$objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_LIBERACAO_ACESSO_EXTERNO);
				
    			$objAtividadeDTO->setDthAbertura( InfraData::getStrDataAtual() );
    			$objAtividadeDTO->setStrSinInicial('S');
    			$objAtividadeDTO->setNumIdUsuarioOrigem( $idUsuario );
    			$objAtividadeDTO->setDblIdProtocolo( $idProtocolo );
    			$objAtividadeDTO->setNumIdUnidade( $idUnidade );
    			$objAtividadeDTO->setNumTipoVisualizacao( AtividadeRN::$TV_NAO_VISUALIZADO );
    			    			
    			$param = array();
    			$param[0] = $objAtividadeDTO;
    			$param[1] = $idUnidade;
    			$param[2] = $dto->getStrStaConcessao();
    			
    			//=========================================
    			//INICIO - preencher corretamente os atributos possiveis para os andamento de liberaчao de acesso externo, para permitir o parser correto nas variaveis do texto
    			//=========================================
    			
    			$numDias = MdPetIntAcessoExternoDocumentoRN::$NUMERO_DIAS_LIBERACAO_ACESSO_EXTERNO;
    			$dtValidade = InfraData::calcularData( $numDias , InfraData::$UNIDADE_DIAS, InfraData::$SENTIDO_ADIANTE );
    			$vlDiasValidade =  $numDias . ' ' . ( $numDias == 1 ? 'dia' : 'dias');
    			$idOrigemVis    = $isIntegral == 'S' ? AcessoExternoRN::$TV_INTEGRAL : AcessoExternoRN::$TV_PARCIAL;
    			
    			$arrObjAtributoAndamentoAPI = array();
    			
    			$arrObjAtributoAndamentoAPI[] = $this->retornaObjAtributoAndamentoAPI('DESTINATARIO_NOME', $nomeUsuario, $idParticipante);
    			$arrObjAtributoAndamentoAPI[] = $this->retornaObjAtributoAndamentoAPI('DESTINATARIO_EMAIL', $emailUsuario, $idParticipante);
    			$arrObjAtributoAndamentoAPI[] = $this->retornaObjAtributoAndamentoAPI('MOTIVO', $strMotivo, $idParticipante);
    			$arrObjAtributoAndamentoAPI[] = $this->retornaObjAtributoAndamentoAPI('DATA_VALIDADE', $dtValidade, null);
    			$arrObjAtributoAndamentoAPI[] = $this->retornaObjAtributoAndamentoAPI('DIAS_VALIDADE', $vlDiasValidade, null);
    			$arrObjAtributoAndamentoAPI[] = $this->retornaObjAtributoAndamentoAPI('VISUALIZACAO', null, $idOrigemVis);

    			//=========================================
    			//FIM - Preencher atributos do andamento
    			//==========================================

    			$objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO);
    			
    			//teste de inserir lancamento via SeiRN (visando resolver problema que ocorre em processo sigiloso)
    			$objEntradaLancarAndamentoAPI = new EntradaLancarAndamentoAPI();
    			$objEntradaLancarAndamentoAPI->setIdProcedimento( $idProtocolo );
    			$objEntradaLancarAndamentoAPI->setIdTarefa( TarefaRN::$TI_LIBERACAO_ACESSO_EXTERNO );
    			$objEntradaLancarAndamentoAPI->setAtributos( $arrObjAtributoAndamentoAPI );
    			
    			$arrObjAtributoAndamentoDTO = array();
    			
    			if ($objEntradaLancarAndamentoAPI->getAtributos()!=null){
    			    
    			    foreach($objEntradaLancarAndamentoAPI->getAtributos() as $objAtributoAndamentoAPI){
    			        
    			        $objAtributoAndamentoDTO = new AtributoAndamentoDTO();
    			        $objAtributoAndamentoDTO->setStrNome($objAtributoAndamentoAPI->getNome());
    			        $objAtributoAndamentoDTO->setStrValor($objAtributoAndamentoAPI->getValor());
    			        $objAtributoAndamentoDTO->setStrIdOrigem($objAtributoAndamentoAPI->getIdOrigem());
    			        $arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;
    			    }
    			}
    			

				// SIGILOSO - conceder credencial
				$objProcedimentoDTO = MdPetIntAceiteRN::_retornaObjProcedimento($idProtocolo);
				if ($objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo() == ProtocoloRN::$NA_SIGILOSO
					|| $objProcedimentoDTO->getStrStaNivelAcessoLocalProtocolo() == ProtocoloRN::$NA_SIGILOSO){
					if (is_numeric(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno())){
						$objMdPetProcedimentoRN = new MdPetProcedimentoRN();
						$objConcederCredencial = $objMdPetProcedimentoRN->concederCredencial( array($objProcedimentoDTO, $idUnidade) );
					}
				}
				// SIGILOSO - conceder credencial - FIM

    			$objAtividadeDTO = new AtividadeDTO();
    			$objAtividadeDTO->setDblIdProtocolo( $idProtocolo );
    			$objAtividadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
    			$objAtividadeDTO->setNumIdTarefa($objEntradaLancarAndamentoAPI->getIdTarefa());
    			$objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO);
    			
    			$objAtividadeRN = new AtividadeRN();
    			$objAtividadeDTO = $objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);
    			
    			//liberacao acesso externo por 100 anos
    			$objAcessoExternoDTO = new AcessoExternoDTO( );
    			$objAcessoExternoDTO->retTodos();
    			$objAcessoExternoDTO->setNumIdUsuarioExterno( $idUsuario );
    			$objAcessoExternoDTO->setDblIdProtocoloAtividade( $idProtocolo );
    			
    			$objAcessoExternoDTO->setNumIdAtividade( $objAtividadeDTO->getNumIdAtividade() );
    			$objAcessoExternoDTO->setNumIdParticipante( $idParticipante );

    			$objAcessoExternoDTO->setDtaValidade( $dtValidade );

    			$objAcessoExternoDTO->setStrEmailUnidade( $strEmailUnidade );
    			$objAcessoExternoDTO->setStrStaTipo( AcessoExternoRN::$TA_USUARIO_EXTERNO );
    			$objAcessoExternoDTO->setStrEmailDestinatario( $emailUsuario );
    			$objAcessoExternoDTO->setStrSinProcesso( 'S' ); //visualizacao integral
    			$objAcessoExternoDTO->setStrSinAtivo('S');
    			$objAcessoExternoDTO->setStrSenha('S'); //somente para nao ficar vazio e cair na validacao
    			$objAcessoExternoDTO->setStrMotivo( $strMotivo );
    			$objAcessoExternoDTO->setNumDias( MdPetIntAcessoExternoDocumentoRN::$NUMERO_DIAS_LIBERACAO_ACESSO_EXTERNO ); //100 anos
    			$objAcessoExternoDTO->setStrHashInterno(md5(time()));
    			
    			$ret = $objAcessoExternoBD->cadastrar( $objAcessoExternoDTO );
    			$arrAcessoExterno[] = $ret;
    			
    			if( $isIntegral == 'N' ){
    				
    				$dtoDocRN = new RelAcessoExtProtocoloRN(  );
    				
    				//vincular os docs do acesso parcial
    				foreach( $arrDocs as $idDocumento ){
    					
    					$dtoDoc = new RelAcessoExtProtocoloDTO( );
    					$dtoDoc->setNumIdAcessoExterno( $ret->getNumIdAcessoExterno() );    					
    					$dtoDoc->setDblIdProtocolo( $idDocumento );
						$existe = 	$dtoDocRN->contar( $dtoDoc ) > 0;

						if(!$existe){
							$dtoDocRN->cadastrar( $dtoDoc );
						}
    					
    				}
    				
    			}

				// SIGILOSO - cassarcredencial 
				$objProcedimentoDTO = MdPetIntAceiteRN::_retornaObjProcedimento($idProtocolo);
				if ($objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo() == ProtocoloRN::$NA_SIGILOSO
					|| $objProcedimentoDTO->getStrStaNivelAcessoLocalProtocolo() == ProtocoloRN::$NA_SIGILOSO){
					if (is_numeric(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno())){
						$objMdPetProcedimentoRN = new MdPetProcedimentoRN();
						$objCassarCredencial = $objMdPetProcedimentoRN->cassarCredencial( $objConcederCredencial );
						$objMdPetProcedimentoRN->excluirAndamentoCredencial( $objConcederCredencial );
					}
				}
				// SIGILOSO - cassarcredencial - FIM

			}

    	return $arrAcessoExterno;
    	
    }

	private function _preencherObjAtributoAndamento($nome, $valor, $idOrigem){
		$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
		$objAtributoAndamentoDTO->setStrNome($nome);
		$objAtributoAndamentoDTO->setStrValor( $valor );
		$objAtributoAndamentoDTO->setStrIdOrigem( $idOrigem );
		return $objAtributoAndamentoDTO;
	}
	
	protected function controlarAcessoExternoIntimacaoConectado($arrParams)
	{
		
		
		//Preparar Variaveis
		$dadosUsuario         = $arrParams[0];
		$post				  = $arrParams[1];
		if(is_array($arrParams[0])){
			$idContato    		  = $arrParams[0][0];
			
		}else{
			$idContato    		  = $arrParams[0];
			
		}

                $procuradorSimplesValido = isset($arrParams[2]) ? $arrParams[2] : NULL;
                
		$idProcedimento       = $post['hdnIdProcedimento'];
		
		$objMdPetAcessoExtRN  = new MdPetAcessoExternoRN();
		
		//Get Dados Documento
		$idDocumento = $post['hdnIdDocumento'];
		$nomeDoc = $this->_getNomeDocCompleto($idDocumento);
		$tpAcessoSolicitado   = array_key_exists('optIntegral', $post) && $post['optIntegral'] == static::$ACESSO_INTEGRAL ? static::$ACESSO_INTEGRAL : static::$ACESSO_PARCIAL;
		
		if($post['hdnTipoPessoa'] == "J"){
		//Atribuindo acesso integral para jurэdico
		$objMdPetAcessoExtRN   = new MdPetAcessoExternoRN();
		$tpAcessoAnterior =  $objMdPetAcessoExtRN->getUltimaConcAcessoExtModuloPorContatos(array(array($idContato), $idProcedimento));
		
		if($tpAcessoAnterior[$idContato] == "I"){
			$tpAcessoSolicitado = static::$ACESSO_INTEGRAL;
		}else if($tpAcessoAnterior[$idContato] == "P"){
			if($tpAcessoSolicitado == "I"){
				$tpAcessoSolicitado = static::$ACESSO_INTEGRAL;
			}
		}else if(empty($tpAcessoAnterior[$idContato])){
			if($tpAcessoSolicitado == static::$ACESSO_PARCIAL){
				$tpAcessoSolicitado = static::$ACESSO_PARCIAL;
			}else{
				$tpAcessoSolicitado = static::$ACESSO_INTEGRAL;
				
			}
		}
	}
        
        if(is_null($procuradorSimplesValido) || (!is_null($procuradorSimplesValido) && $procuradorSimplesValido)){
            $idAcessoExterno = $objMdPetAcessoExtRN->aplicarRegrasGeraisAcessoExterno($idProcedimento, MdPetAcessoExternoRN::$MD_PET_INTIMACAO, $idContato,  $tpAcessoSolicitado, $nomeDoc);
        }		
		
            return $idAcessoExterno;
	}


	private function _getNomeDocCompleto($idDoc){
		$objMdPetIntimacaoRN  = new MdPetIntimacaoRN();
		$objDocumentoDTO  = $objMdPetIntimacaoRN->getObjDocumentoPorIdDoc($idDoc);
		$nomeDocCompleto  = !is_null($objDocumentoDTO) ? $objDocumentoDTO->getStrProtocoloDocumentoFormatado() : '';
		$nomeDocCompleto .= !is_null($objDocumentoDTO) ? ' ('.$objDocumentoDTO->getStrNomeSerie().' '.$objDocumentoDTO->getStrNumero().')' : '';

		return $nomeDocCompleto;
	}

	private function _atualizarTodasIntimacoesProcesso($idProcesso, $idContato, $idAcessoExt)
	{
		$objMdPetIntimacaoRN = new MdPetIntimacaoRN();
		$objMdPetIntRelDestRN = new MdPetIntRelDestinatarioRN();
		$arrIdIntimacoes = $objMdPetIntimacaoRN->getIntimacoesProcesso($idProcesso);

		foreach ($arrIdIntimacoes as $idIntimacao)
		{
			$objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
			$objMdPetIntRelDestDTO->setNumIdContato($idContato);
			$objMdPetIntRelDestDTO->setNumIdMdPetIntimacao($idIntimacao);
			$objMdPetIntRelDestDTO->retTodos();
			$count = $objMdPetIntRelDestRN->contar($objMdPetIntRelDestDTO);

			if ($count > 0)
			{
				$objMdPetIntRelDestDTO = $objMdPetIntRelDestRN->consultar($objMdPetIntRelDestDTO);
				$objMdPetIntRelDestDTO->setNumIdAcessoExterno($idAcessoExt);
				$objMdPetIntRelDestRN->alterar($objMdPetIntRelDestDTO);
			}
		}
	}


	private function _preencherArrDocDisponibilizados($dadosCadastro){
		$isTpConcessao = isset($dadosCadastro['optParcial']) ? MdPetIntAcessoExternoDocumentoRN::$ACESSO_PARCIAL : MdPetIntAcessoExternoDocumentoRN::$ACESSO_INTEGRAL;

		$arrAnexos   = $_POST['hdnIdsDocAnexo'] != '' ? json_decode($_POST['hdnIdsDocAnexo']) : array();
		$arrProtDisp = $_POST['hdnIdsDocDisponivel'] != '' ? json_decode($_POST['hdnIdsDocDisponivel']) : array();

		$arrDoc = ($isTpConcessao == MdPetIntAcessoExternoDocumentoRN::$ACESSO_PARCIAL) ? array_merge($arrAnexos, $arrProtDisp) : $arrAnexos;

		array_push($arrDoc, $dadosCadastro['hdnIdDocumento']);

		return $arrDoc;
	}

	private function _cadastrarAcessoExternoIntimacao($arrParams, $nomeDocCompleto)
	{
		$dadosUsuario = $arrParams[0];
		$post 		  = $arrParams[1];
		$idContato    = $dadosUsuario[0];
		$idProcesso   = $post['hdnIdProcedimento'];

		$idParticipante = $this->_getIdParticipantePorContato($idContato, $idProcesso, true);

		$arrDocAcessoExt = $this->_preencherArrDocDisponibilizados($post);

		$objMdPetIntAcessoExtDocDTO = new MdPetIntAcessoExternoDocumentoDTO();
		$objMdPetIntAcessoExtDocDTO->setNumIdUsuarioExterno($dadosUsuario[0]);
		$objMdPetIntAcessoExtDocDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
		$objMdPetIntAcessoExtDocDTO->setNumIdParticipante($idParticipante);
		$objMdPetIntAcessoExtDocDTO->setDblIdProtocoloProcesso($post['hdnIdProcedimento']);
		$objMdPetIntAcessoExtDocDTO->setArrIdDocumentos($arrDocAcessoExt);
		$objMdPetIntAcessoExtDocDTO->setStrNomeUsuarioExterno($dadosUsuario[1]);
		$objMdPetIntAcessoExtDocDTO->setStrEmailUsuarioExterno($dadosUsuario[2]);
		$objMdPetIntAcessoExtDocDTO->setStrStaConcessao(MdPetIntAcessoExternoDocumentoRN::$STA_INTERNO);

		$isTpConcessao = isset($post['optParcial']) ? 'N': 'S';
		$objMdPetIntAcessoExtDocDTO->setStrSinVisualizacaoIntegral($isTpConcessao);
		$objMdPetIntAcessoExtDocDTO->setStrMotivo('Criado automaticamente no тmbito da Intimaчуo Eletrєnica afeta ao Documento '.$nomeDocCompleto.'.');

		return $this->concederAcessoExternoParaDocumentosControlado($objMdPetIntAcessoExtDocDTO);
   }


   /* Funчуo Responsсvel por verificar se o processo que estс sendo realizada a intimaчуo possui algum tipo de AcessoExterno Gerada Anteriormente.
    *
    */
	private function _verificarPossuiConcessaoAcesso($arrParams)
	{
	//Init Vars
		$tpAcesso			 = null;
		$dadosUsuario        = $arrParams[0];
		$post                = $arrParams[1];
		$idContato           = $dadosUsuario[0];
		$idProcesso          = $post['hdnIdProcedimento'];

		$arrObjAcessosExtDTO = $this->_getAcessosExternosValidos($idContato, $idProcesso);

		if (count($arrObjAcessosExtDTO) == 0) {
			return MdPetIntAcessoExternoDocumentoRN::$NAO_POSSUI_ACESSO;
		} else {
			$arrRel = $this->_getIdsRelAcessoExt($arrObjAcessosExtDTO);

			if (count($arrRel) == 0) {
				return MdPetIntAcessoExternoDocumentoRN::$ACESSO_INTEGRAL;
			} else {
				foreach ($arrObjAcessosExtDTO as $key => $objAcessoExtDTO) {
					$tpAcesso = !isset($arrRel[$objAcessoExtDTO->getNumIdAcessoExterno()]) ? MdPetIntAcessoExternoDocumentoRN::$ACESSO_INTEGRAL : $tpAcesso;
					$tpAcesso = isset($arrRel[$objAcessoExtDTO->getNumIdAcessoExterno()]) && $tpAcesso != MdPetIntAcessoExternoDocumentoRN::$ACESSO_INTEGRAL ? MdPetIntAcessoExternoDocumentoRN::$ACESSO_PARCIAL: $tpAcesso;
				}
			}
		}

		return $tpAcesso;
	}

	private function _getAcessosExternosValidos($idContato, $idProcesso, $cadNovoPart = true){
		$objAcessoExternoDTO = new AcessoExternoDTO();
		$objAcessoExternoRN  = new AcessoExternoRN();
		$bolTxtDtAtual 		 = InfraData::getStrDataAtual();

		//Get Participante por nњmero de Processo (Se nуo existir, realiza o cadastro)
		$idParticipante = $this->_getIdParticipantePorContato($idContato, $idProcesso, $cadNovoPart);

		//Remover pelo filtro Processos jс vencidos ou que sejam cancelados
		$objAcessoExternoDTO->setDtaValidade($bolTxtDtAtual, InfraDTO::$OPER_MAIOR_IGUAL);
		$objAcessoExternoDTO->setStrSinAtivo('S');
		$objAcessoExternoDTO->setStrStaTipo(AcessoExternoRN::$TA_USUARIO_EXTERNO);
		$objAcessoExternoDTO->setNumIdParticipante($idParticipante);
		$objAcessoExternoDTO->retNumIdAcessoExterno();

		$arrObjAcessosExtDTO = $objAcessoExternoRN->listar($objAcessoExternoDTO);

		return $arrObjAcessosExtDTO;
	}


	private function _getIdsRelAcessoExt($arrObjAcessosExtDTO){
		$arrRetorno = array();
		$objRelAcessoExtProtocoloRN = new RelAcessoExtProtocoloRN();
		$arrIdsAcExt = InfraArray::converterArrInfraDTO($arrObjAcessosExtDTO, 'IdAcessoExterno');

		$objRelAcessoExtProtocoloDTO = new RelAcessoExtProtocoloDTO();
		$objRelAcessoExtProtocoloDTO->setNumIdAcessoExterno($arrIdsAcExt, InfraDTO::$OPER_IN);
		$objRelAcessoExtProtocoloDTO->retDblIdProtocolo();
		$objRelAcessoExtProtocoloDTO->retNumIdAcessoExterno();

		$arr = $objRelAcessoExtProtocoloRN->listar($objRelAcessoExtProtocoloDTO);

		if(count($arr) > 0){
			foreach($arr as $objRel){
				$arrRetorno[$objRel->getNumIdAcessoExterno()] = true;
			}
		}

		return $arrRetorno;
	}

	public function cadastrarParticipante($idContato, $idProcesso){
		return $this->_getIdParticipantePorContato($idContato, $idProcesso, true);
	}
	
	private function _getIdParticipantePorContato($idContato, $idProcesso, $cadastrarNovo = true){
		$objParticipanteRN = new ParticipanteRN();

		$objParticipanteDTO = new ParticipanteDTO();
		$objParticipanteDTO->setNumIdContato($idContato);
		$objParticipanteDTO->setDblIdProtocolo($idProcesso);
		$objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_ACESSO_EXTERNO);
		$objParticipanteDTO->retNumIdParticipante();
		$objParticipanteDTO = $objParticipanteRN->consultarRN1008($objParticipanteDTO);

		$idParticipante = !is_null($objParticipanteDTO) ? $objParticipanteDTO->getNumIdParticipante() : null;

		if(is_null($idParticipante) && $cadastrarNovo){
			$idUnidade          = SessaoSEI::getInstance()->getNumIdUnidadeAtual();
		    $objParticipanteDTO = $this->adicionarParticipanteProcessoAcessoExterno(array($idProcesso, $idUnidade, $idContato));

			if(!is_null($objParticipanteDTO)){
				$idParticipante = $objParticipanteDTO->getNumIdParticipante();
			}
		}

		return $idParticipante;
	}


	public function adicionarParticipanteProcessoAcessoExterno($arr){
		$idProced      = $arr[0];
		$idUnidade     = $arr[1];
		$idContato     = $arr[2];

		$objParticipanteDTO = new ParticipanteDTO();
		$objParticipanteDTO->setNumIdContato($idContato);
		$objParticipanteDTO->setDblIdProtocolo($idProced);
		$objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_ACESSO_EXTERNO);
		$objParticipanteDTO->retNumIdParticipante();

		$objParticipanteRN  = new ParticipanteRN();
		$count = $objParticipanteRN->contarRN0461($objParticipanteDTO);

		if($count > 0){
			return $objParticipanteRN->consultarRN1008($objParticipanteDTO);
		}else{
			$objParticipanteDTO->setNumIdUnidade($idUnidade);
			$objParticipanteDTO->setNumSequencia(0);

			$objParticipanteRN  = new ParticipanteRN();
			$objParticipanteDTO = $objParticipanteRN->cadastrarRN0170($objParticipanteDTO);
			return $objParticipanteDTO;
		}

		return null;
	}

	//parte da Soluчуo atual para coluna de aчѕes. Item 35
	protected function getArrDocumentosAPIConectado($arrParams){
		
		$idAcessoExt = $arrParams[0];
		$idProxAn    = $arrParams[1];
		$isProced    = $arrParams[2];
		
		$objAcessoExternoRN = new AcessoExternoRN();
		
		$objAcessoExternoDTO = new AcessoExternoDTO();
		$objAcessoExternoDTO->setNumIdAcessoExterno($idAcessoExt);
		
		if ($idProxAn){
			$objAcessoExternoDTO->setDblIdProtocoloConsulta($idProxAn);
		}
		
		$objAcessoExternoDTO = $objAcessoExternoRN->consultarProcessoAcessoExterno($objAcessoExternoDTO);
		$objProcedimentoDTO = $objAcessoExternoDTO->getObjProcedimentoDTO();
		$arrObjRelProtocoloProtocoloDTO = $objProcedimentoDTO->getArrObjRelProtocoloProtocoloDTO();
		
		foreach ($arrObjRelProtocoloProtocoloDTO as $objRelProtocoloProtocoloDTO) {
			
			$objProtocoloDTO = $objRelProtocoloProtocoloDTO->getObjProtocoloDTO2();
			$validacaoDoc    = $isProced ? true : $objProtocoloDTO->isSetDblIdDocumento();

			if( $objProtocoloDTO != null && $validacaoDoc ){

				$arrIdsProtocolo[] = $isProced ? $objProtocoloDTO->getDblIdProcedimento() : $objProtocoloDTO->getDblIdDocumento();
			
			}
		}
		
		return $arrIdsProtocolo;
	}
	
	public function verificarConcessaoAcessoExterno($job, $objDocumento, $objMdPetIntAceiteDTO)
	{
        $arrObjMdPetIntDestDTO  = $this->_getIdAcessoExternoRelacionado($objMdPetIntAceiteDTO);

        foreach ($arrObjMdPetIntDestDTO as $objMdPetIntDestDTO) {

            $tpConcessao = $this->getTipoConcessaoAcesso($objMdPetIntDestDTO->getNumIdAcessoExterno());
            if ($tpConcessao == static::$ACESSO_PARCIAL) {
                $objRelProtAcessoExtRN = new RelAcessoExtProtocoloRN();
                $objRelProtAcessoExtDTO = new RelAcessoExtProtocoloDTO();
                $objRelProtAcessoExtDTO->setNumIdAcessoExterno($objMdPetIntDestDTO->getNumIdAcessoExterno());
                $objRelProtAcessoExtDTO->setDblIdProtocolo($objDocumento->getDblIdDocumento());
                $objRelProtAcessoExtRN->cadastrar($objRelProtAcessoExtDTO);
            }
        }
	}
	
	private function _getIdAcessoExternoRelacionado($objMdPetIntAceiteDTO){
		$objMdPetIntDestRN  = new MdPetIntRelDestinatarioRN();
		$idMdPetRelDest     = $objMdPetIntAceiteDTO->getNumIdMdPetIntRelDestinatario();
		
		$objMdPetIntDestDTO = new MdPetIntRelDestinatarioDTO();
		$objMdPetIntDestDTO->setNumIdMdPetIntRelDestinatario($idMdPetRelDest);
		$objMdPetIntDestDTO->retNumIdAcessoExterno();

		$arrObjMdPetIntDestDTO = $objMdPetIntDestRN->listar($objMdPetIntDestDTO);

		return $arrObjMdPetIntDestDTO;
	}
	
	public function getTipoConcessaoAcesso($idAcessoExt){
		$objRelProtAcessoExtRN  = new RelAcessoExtProtocoloRN();
		$objRelProtAcessoExtDTO = new RelAcessoExtProtocoloDTO();
		$objRelProtAcessoExtDTO->setNumIdAcessoExterno($idAcessoExt);
		$tpConcessao = $objRelProtAcessoExtRN->contar($objRelProtAcessoExtDTO) > 0 ? static::$ACESSO_PARCIAL : static::$ACESSO_INTEGRAL ;
		
		return $tpConcessao;
	}
	
	protected function verificarAcessoExternoValidoConectado($arrParams){
		$idIntimacao = $arrParams[0];
		$idContato   = $arrParams[1];
		$idAcessoEx  = $arrParams[2];
		
		$objMdPetIntRelDestRN  = new MdPetIntRelDestinatarioRN();
		$objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
		$objMdPetIntRelDestDTO->setNumIdMdPetIntimacao($idIntimacao);
                $objMdPetIntRelDestDTO->setNumIdAcessoExterno($idAcessoEx);
		$objMdPetIntRelDestDTO->retNumIdAcessoExterno();
		$objMdPetIntRelDestDTO->setNumIdContato($idContato);
		$objMdPetIntRelDestDTO = $objMdPetIntRelDestRN->consultar($objMdPetIntRelDestDTO);
		
		if (!is_null($objMdPetIntRelDestDTO)) {
			$idAcessoEx = $objMdPetIntRelDestDTO->getNumIdAcessoExterno();
			
			$dtAtual = InfraData::getStrDataAtual();
			$objAcessoExternoRN = new AcessoExternoRN();
			$objAcessoExternoDTO = new AcessoExternoDTO();
			
			$objAcessoExternoDTO->setNumIdAcessoExterno($idAcessoEx);
			$objAcessoExternoDTO->setDtaValidade($dtAtual, InfraDTO::$OPER_MAIOR_IGUAL);
			$objAcessoExternoDTO->setStrSinAtivo('S');
			
			$count = $objAcessoExternoRN->contar($objAcessoExternoDTO);
			
			if($count > 0){
				return $idAcessoEx;
			}
		}else{
			return $idAcessoEx;
		}
		
		return null;
	}
	
	
	protected function verificarPermissaoAcessoExternoConectado($arrParams){
		$isExibirDoc 	   = SeiIntegracao::$TAM_PERMITIDO;
		$idIntimacao 	   = $arrParams[0];
		$idDoc             = $arrParams[1];
		$idAcessoExt       = $arrParams[2];
		$objMdPetDocDispRN = new MdPetIntDocDisponivelRN();
		
		$objMdPetDocDispDTO = new MdPetIntProtDisponivelDTO();
		$objMdPetDocDispDTO->setDblIdDocumento($idDoc);
		$objMdPetDocDispDTO->setNumIdMdPetIntimacao($idIntimacao);
		$isDocDisponivel = $objMdPetDocDispRN->contar($objMdPetDocDispDTO) > 0;
		
		if($isDocDisponivel){
			$isAcessoExtValido = $this->_acessoExternoIntimacaoValido($idIntimacao);
		}
		
	}
	
	private function _acessoExternoIntimacaoValido($idIntimacao){
		$objMdPetIntAceiteRN  = new MdPetIntAceiteRN();
		$objMdPetIntRelDestRN = new MdPetIntRelDestinatarioRN();
		$objContato =  $objMdPetIntAceiteRN->retornaObjContatoIdUsuario(array(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()));
		
		$objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
		$objMdPetIntRelDestDTO->setNumIdMdPetIntimacao($idIntimacao);
		$objMdPetIntRelDestDTO->retNumIdAcessoExterno();
		$objMdPetIntRelDestDTO->setNumIdContato($objContato->getNumIdContato());
		$objMdPetIntRelDestDTO = $objMdPetIntRelDestRN->consultar($objMdPetIntRelDestDTO);
	}


	private function _getAcessosExternosUnidade(){
		$idUnidade = SessaoSEI::getInstance()->getNumIdUnidadeAtual() ? SessaoSEI::getInstance()->getNumIdUnidadeAtual() : SessaoSEIExterna::getInstance()->getNumIdUnidadeAtual();
		$objAcessoExternoDTO = new AcessoExternoDTO();
		$objAcessoExternoDTO->setNumIdUnidadeAtividade($idUnidade);
		$objAcessoExternoDTO->retNumIdAcessoExterno();

		$objAcessoExternoRN = new AcessoExternoRN();

		$arrRet = $objAcessoExternoRN->listar($objAcessoExternoDTO);
		$arrIds = InfraArray::converterArrInfraDTO($arrRet, 'IdAcessoExterno');

		return $arrIds;
	}






	
	

}
?>