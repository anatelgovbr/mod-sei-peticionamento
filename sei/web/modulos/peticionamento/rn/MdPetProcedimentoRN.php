<?php
/**
 * ANATEL
 *
 * 21/07/2016 - criado por marcelo.bezerra - CAST
 *
 */
require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetProcedimentoRN extends ProcedimentoRN {
	
	protected function lancarAcessoControleInternoControlado(ProcedimentoDTO $objProcedimentoDTO){
		try{
			
			if ($objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo()!=ProtocoloRN::$NA_SIGILOSO){
				
				$objControleInternoDTO = new ControleInternoDTO();
				$objControleInternoDTO->setDistinct(true);
				
				//alteracoes seiv3
				$objControleInternoDTO->retNumIdUnidadeControle();
				$objControleInternoDTO->setNumIdTipoProcedimentoControlado( $objProcedimentoDTO->getNumIdTipoProcedimento() );
				$objControleInternoDTO->setNumIdOrgaoControlado(SessaoSEI::getInstance()->getNumIdOrgaoUnidadeAtual());
				$objControleInternoDTO->setNumIdUnidadeControle(SessaoSEI::getInstance()->getNumIdUnidadeAtual(),InfraDTO::$OPER_DIFERENTE);
				
				$objControleInternoRN = new ControleInternoRN();
				$arrObjControleInternoDTO = $objControleInternoRN->listar($objControleInternoDTO);
				
				$objProtocoloRN = new ProtocoloRN();
				
				foreach($arrObjControleInternoDTO as $objControleInternoDTO){
					
					$objAtividadeDTO = new AtividadeDTO();
					$objAtividadeDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());
					$objAtividadeDTO->setNumIdUnidade($objControleInternoDTO->getNumIdUnidadeRelControleInternoUnidade());
					
					$objAtividadeRN = new AtividadeRN();
					
					if ($objAtividadeRN->contarRN0035($objAtividadeDTO)==0){
						
						$objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_ACESSO_AUTOMATICO_AO_PROCESSO);
						
						$objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);
						
						
						//Associar o processo e seus documentos com esta unidade
						$objAssociarDTO = new AssociarDTO();
						$objAssociarDTO->setDblIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
						$objAssociarDTO->setDblIdDocumento(null);
						$objAssociarDTO->setNumIdUnidade($objControleInternoDTO->getNumIdUnidadeRelControleInternoUnidade());
						$objAssociarDTO->setNumIdUsuario(null);
						$objAssociarDTO->setStrStaNivelAcessoGlobal($objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo());
						
						$objProtocoloRN->associarRN0982($objAssociarDTO);
						
					}
				}
			}
		}catch(Exception $e){
			throw new InfraException('Erro lançando acesso para o Controle Interno.',$e);
		}
	}
	
	public function concederCredencialControlado($params) {
		
		try {
			$objProcedimentoDTO     = $params[0];
			$idUnidadeProcesso      = $params[1];
			$numIdUsuarioCredencial = isset($params[4]) ? $params[4] : SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();
			
			if ($objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo() == ProtocoloRN::$NA_SIGILOSO
				|| $objProcedimentoDTO->getStrStaNivelAcessoLocalProtocolo() == ProtocoloRN::$NA_SIGILOSO){
				
				$objMdPetIntimacaoRN = new MdPetIntimacaoRN;
				$arrAtividadeDTO = $objMdPetIntimacaoRN->verificarUnidadeAberta( array($objProcedimentoDTO, $idUnidadeProcesso) );
				
				if (count($arrAtividadeDTO)>0){
					$numIdUsuarioAcesso  = $arrAtividadeDTO[0]->getNumIdUsuario();
					$numIdUnidadeAcesso  = $arrAtividadeDTO[0]->getNumIdUnidade();
					
					// Verificar se a credencial já existe
					$arrObjAtividadeDTO = $this->listarCredenciais( array($objProcedimentoDTO->getDblIdProcedimento(), null, $numIdUnidadeAcesso, $numIdUsuarioCredencial, TarefaRN::getArrTarefasConcessaoCredencial(false)) );
					
					// Usuário antes de tratamento de SIGILOSO
					if ( is_numeric(SessaoSEI::getInstance()->getNumIdUsuario()) && is_numeric(SessaoSEI::getInstance()->getNumIdUnidadeAtual()) ){
						$numIdUsuarioAntesSigiloso = SessaoSEI::getInstance()->getNumIdUsuario();
						$numIdUnidadeAtualAntesSigiloso = SessaoSEI::getInstance()->getNumIdUnidadeAtual();
					}
					
					if (count($arrObjAtividadeDTO)>0){
						for ($i=0; $i<count($arrObjAtividadeDTO); $i++) {
							
							$numIdUsuarioAcesso  = $arrObjAtividadeDTO[$i]->getNumIdUsuarioOrigem();
							
							$retorno = array();
							$retorno[0] = $params[0];
							$retorno[1] = $params[1];
							$retorno[2] = $arrObjAtividadeDTO[$i];
							$retorno[3] = isset($idUnidade) ? $idUnidade : null;
							$retorno[4] = $numIdUsuarioCredencial;
							$retorno[5] = $numIdUsuarioAcesso;
							$retorno[6] = $numIdUnidadeAcesso;
							$retorno[7] = $numIdUsuarioAntesSigiloso;
							$retorno[8] = $numIdUnidadeAtualAntesSigiloso;
							
							return $retorno;
						}
					}
					
					//Simulando para usuário que tem credencial
					SessaoSEI::getInstance()->simularLogin(null, null, $numIdUsuarioAcesso, $numIdUnidadeAcesso);
					
					$objAtividadeRN = new AtividadeRN();
					$objPesquisaPendenciaDTO = new PesquisaPendenciaDTO();
					$objPesquisaPendenciaDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());
					$objPesquisaPendenciaDTO->setNumIdUsuario($numIdUsuarioAcesso);
					$objPesquisaPendenciaDTO->setNumIdUnidade($numIdUnidadeAcesso);
					$arrObjProcedimentoDTO = $objAtividadeRN->listarPendenciasRN0754($objPesquisaPendenciaDTO);
					
					if (count($arrObjProcedimentoDTO)==0){
						// Usuário antes de tratamento de SIGILOSO - RETORNANDO
						if ( is_numeric($numIdUsuarioAntesSigiloso) && is_numeric($numIdUnidadeAtualAntesSigiloso) ){
							SessaoSEI::getInstance()->simularLogin(null, null, $numIdUsuarioAntesSigiloso, $numIdUnidadeAtualAntesSigiloso);
						}else{
							// destruir sessão
						}
						
						throw new InfraException('concederCredencial - Processo não encontrado.');
					}
					
					$arrAtividadesOrigem = InfraArray::converterArrInfraDTO($arrObjProcedimentoDTO[0]->getArrObjAtividadeDTO(),'IdAtividade');
					
					$objConcederCredencialDTO = new ConcederCredencialDTO();
					$objConcederCredencialDTO->setDblIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
					$objConcederCredencialDTO->setNumIdUsuario($numIdUsuarioCredencial);
					$objConcederCredencialDTO->setNumIdUnidade($numIdUnidadeAcesso);
					
					$objConcederCredencialDTO->setArrAtividadesOrigem(InfraArray::gerarArrInfraDTO('AtividadeDTO','IdAtividade',$arrAtividadesOrigem));
					
					// atribuindo credencial provisória
					$objAtividadeRN = new AtividadeRN();
					$objConcederCredencial = $objAtividadeRN->concederCredencial($objConcederCredencialDTO);
					
					// Usuário antes de tratamento de SIGILOSO - RETORNANDO
					if ( is_numeric($numIdUsuarioAntesSigiloso) && is_numeric($numIdUnidadeAtualAntesSigiloso) ){
						SessaoSEI::getInstance()->simularLogin(null, null, $numIdUsuarioAntesSigiloso, $numIdUnidadeAtualAntesSigiloso);
					}else{
						// destruir sessão
					}
					
					$retorno = array();
					$retorno[0] = $params[0];
					$retorno[1] = $params[1];
					$retorno[2] = $objConcederCredencial;
					$retorno[3] = isset($idUnidade) ? $idUnidade : null;
					$retorno[4] = $numIdUsuarioCredencial;
					$retorno[5] = $numIdUsuarioAcesso;
					$retorno[6] = $numIdUnidadeAcesso;
					$retorno[7] = $numIdUsuarioAntesSigiloso;
					$retorno[8] = $numIdUnidadeAtualAntesSigiloso;
					
					return $retorno;
				}
				
			}
			
		}catch(Exception $e){
			throw new InfraException('Erro concederCredencial.',$e);
		}
	}
	
	public function cassarCredencialControlado($params) {
		try {
			$objProcedimentoDTO             = $params[0];
			$idUnidadeProcesso              = $params[1];
			$objConcederCredencial          = $params[2];
			
			if ($objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo() == ProtocoloRN::$NA_SIGILOSO
				|| $objProcedimentoDTO->getStrStaNivelAcessoLocalProtocolo() == ProtocoloRN::$NA_SIGILOSO){
				
				$objAtividadeDTO = new AtividadeDTO();
				$objAtividadeDTO->retTodos();
				$objAtividadeDTO->setNumIdAtividade($objConcederCredencial->getNumIdAtividade());
				
				$objAtividadeRN = new AtividadeRN();
				$arrObjAtividadeDTO = $objAtividadeRN->listarRN0036($objAtividadeDTO);
				if (count($arrObjAtividadeDTO)){
					$numIdUsuarioExterno = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();
					$numIdUsuarioAcesso  = $arrObjAtividadeDTO[0]->getNumIdUsuarioOrigem();
					$numIdUnidadeAcesso  = $arrObjAtividadeDTO[0]->getNumIdUnidade();
				}
				
				// Usuário antes de tratamento de SIGILOSO
				if ( is_numeric(SessaoSEI::getInstance()->getNumIdUsuario()) && is_numeric(SessaoSEI::getInstance()->getNumIdUnidadeAtual()) ){
					$numIdUsuarioAntesSigiloso = SessaoSEI::getInstance()->getNumIdUsuario();
					$numIdUnidadeAtualAntesSigiloso = SessaoSEI::getInstance()->getNumIdUnidadeAtual();
				}
				SessaoSEI::getInstance()->simularLogin(null, null, $numIdUsuarioAcesso, $numIdUnidadeAcesso);
				$arrObjAtividadeDTO = InfraArray::gerarArrInfraDTO('AtividadeDTO','IdAtividade',array($objConcederCredencial->getNumIdAtividade()));
				
				//guardando credencial sem recebido, exceto aquela a ser cassada
				$objAtividadeDTO = new AtividadeDTO();
				$objAtividadeDTO->retNumIdAtividade();
				$objAtividadeDTO->setDblIdProtocolo($objConcederCredencial->getDblIdProtocolo());
				$objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_PROCESSO_CONCESSAO_CREDENCIAL);
				$objAtividadeDTO->setNumIdAtividade($objConcederCredencial->getNumIdAtividade(), InfraDTO::$OPER_DIFERENTE);
				$objAtividadeRN = new AtividadeRN();
				$arrObjAtividadeConcessaoDTO = $objAtividadeRN->listarRN0036($objAtividadeDTO);
				
				//cassando credencial usuário externo
				$objAtividadeRN = new AtividadeRN();
				$objAtividadeRN->cassarCredenciais($arrObjAtividadeDTO);
				
				//retornando credencial sem recebido
				if (count($arrObjAtividadeConcessaoDTO)>0){
					foreach ($arrObjAtividadeConcessaoDTO as $itemDTO) {
						$itemDTO->setDthConclusao(null);
						$objMdPetAtividadeRN = new MdPetAtividadeRN();
						$objMdPetAtividadeRN->alterar($itemDTO);
					}
				}
				
				// Usuário antes de tratamento de SIGILOSO - RETORNANDO
				if ($numIdUnidadeAtualAntesSigiloso!='' && $numIdUsuarioAntesSigiloso!=''){
					SessaoSEI::getInstance()->simularLogin(null, null, $numIdUsuarioAntesSigiloso, $numIdUnidadeAtualAntesSigiloso);
				}
				
				$retorno = array();
				$retorno[0] = $objProcedimentoDTO;
				$retorno[1] = $idUnidadeProcesso;
				$retorno[2] = $objConcederCredencial;
				$retorno[3] = isset($idUnidade) ? $idUnidade : null;
				
			}
			
		}catch(Exception $e){
			throw new InfraException('Erro cassarCredencial.',$e);
		}
	}
	
	// baseada na: AtividadeRN->listarCredenciaisConectado
	public function listarCredenciaisConectado($param) {
		try{
			
			$idProcedimento      = isset($param[0]) ? $param[0] : null;
			$numIdUsuario        = isset($param[1]) ? $param[1] : null;
			$numIdUnidadeAtual   = isset($param[2]) ? $param[2] : SessaoSEI::getInstance()->getNumIdUnidadeAtual();
			$numIdUsuarioDestino = isset($param[3]) ? $param[3] : null;
			$arrTarefas          = isset($param[4]) ? $param[4] : array_merge(TarefaRN::getArrTarefasConcessaoCredencial(false), TarefaRN::getArrTarefasCassacaoCredencial(false));
			
			$objInfraException = new InfraException();
			
			$objProtocoloDTO = new ProtocoloDTO();
			$objProtocoloDTO->retDblIdProtocolo();
			$objProtocoloDTO->retStrProtocoloFormatado();
			$objProtocoloDTO->retStrStaNivelAcessoGlobal();
			$objProtocoloDTO->setDblIdProtocolo($idProcedimento);
			
			$objProtocoloRN = new ProtocoloRN();
			$objProtocoloDTO = $objProtocoloRN->consultarRN0186($objProtocoloDTO);
			
			//Não verifica se usuário tem credencial de acesso
			$objAtividadeDTO = new AtividadeDTO();
			$objAtividadeDTO->retNumIdAtividade();
			$objAtividadeDTO->retNumIdUsuarioOrigem();
			$objAtividadeDTO->retStrSiglaUsuario();
			$objAtividadeDTO->retStrNomeUsuario();
			$objAtividadeDTO->retStrSiglaUnidade();
			$objAtividadeDTO->retStrDescricaoUnidade();
			$objAtividadeDTO->retDthAbertura();
			$objAtividadeDTO->retDthConclusao();
			$objAtividadeDTO->retNumIdTarefa();
			if ($numIdUsuario){
				$objAtividadeDTO->setNumIdUsuarioOrigem($numIdUsuario);
			}
			if ($numIdUsuarioDestino){
				$objAtividadeDTO->setNumIdUsuario($numIdUsuarioDestino);
			}
			$objAtividadeDTO->setDblIdProtocolo($objProtocoloDTO->getDblIdProtocolo());
			$objAtividadeDTO->setNumIdTarefa($arrTarefas, InfraDTO::$OPER_IN);
			$objAtividadeDTO->setOrdNumIdAtividade(InfraDTO::$TIPO_ORDENACAO_DESC);
			
			$objAtividadeRN = new AtividadeRN();
			
			$arrObjAtividadeDTO = $objAtividadeRN->listarRN0036($objAtividadeDTO);
			
			if (count($arrObjAtividadeDTO)){
				
				$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
				$objAtributoAndamentoDTO->retNumIdAtividade();
				$objAtributoAndamentoDTO->retStrNome();
				$objAtributoAndamentoDTO->retStrValor();
				$objAtributoAndamentoDTO->setNumIdAtividade(InfraArray::converterArrInfraDTO($arrObjAtividadeDTO,'IdAtividade'), InfraDTO::$OPER_IN);
				
				$objAtributoAndamentoRN = new AtributoAndamentoRN();
				$arrObjAtributoAndamentoDTO = InfraArray::indexarArrInfraDTO($objAtributoAndamentoRN->listarRN1367($objAtributoAndamentoDTO),'IdAtividade',true);
				
				foreach($arrObjAtividadeDTO as $objAtividadeDTO){
					if (isset($arrObjAtributoAndamentoDTO[$objAtividadeDTO->getNumIdAtividade()])){
						$objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO[$objAtividadeDTO->getNumIdAtividade()]);
					}else{
						$objAtividadeDTO->setArrObjAtributoAndamentoDTO(array());
					}
				}
			}
			
			return $arrObjAtividadeDTO;
			
		}catch(Exception $e){
			throw new InfraException('Erro listarCredenciais.',$e);
		}
	}
	
	public function excluirAndamentoCredencialControlado($params) {
		try {
			$objProcedimentoDTO             = $params[0];
			$idUnidadeProcesso              = $params[1];
			$objConcederCredencial          = $params[2];
			$idUnidade                      = $params[3];
			$numIdUsuarioExterno            = $params[4];
			$numIdUsuarioAcesso		        = $params[5];
			$numIdUnidadeAcesso		        = $params[6];
			$numIdUsuarioAntesSigiloso      = $params[7];
			$numIdUnidadeAtualAntesSigiloso = $params[8];
			
			if ($objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo() == ProtocoloRN::$NA_SIGILOSO
				|| $objProcedimentoDTO->getStrStaNivelAcessoLocalProtocolo() == ProtocoloRN::$NA_SIGILOSO){
				
				//concessão
				$objAtividadeRN = new AtividadeRN();
				$objAtividadeDTOLiberacao = new AtividadeDTO();
				$objAtividadeDTOLiberacao->retTodos();
				$objAtividadeDTOLiberacao->setDblIdProtocolo( $objProcedimentoDTO->getDblIdProcedimento() );
				$objAtividadeDTOLiberacao->setNumIdAtividade( $objConcederCredencial->getArrObjAtributoAndamentoDTO()[0]->getNumIdAtividade() );
				$arrDTOAtividades = $objAtividadeRN->listarRN0036( $objAtividadeDTOLiberacao );
				if (count($arrDTOAtividades)>0){
					$objAtividadeRN->excluirRN0034( $arrDTOAtividades );
				}

//            processo recebido
				$objAtividadeRN = new AtividadeRN();
				$objAtividadeDTOLiberacao = new AtividadeDTO();
				$objAtividadeDTOLiberacao->retTodos();
				$objAtividadeDTOLiberacao->setDblIdProtocolo( $objProcedimentoDTO->getDblIdProcedimento() );
				$objAtividadeDTOLiberacao->setNumIdTarefa( TarefaRN::$TI_PROCESSO_RECEBIMENTO_CREDENCIAL );
				$objAtividadeDTOLiberacao->setNumIdUsuario( $numIdUsuarioExterno );
				$arrDTOAtividades = $objAtividadeRN->listarRN0036( $objAtividadeDTOLiberacao );
				if (count($arrDTOAtividades)>0){
					$objAtividadeRN->excluirRN0034( $arrDTOAtividades );
				}

//            processo recebido
				$objAtividadeRN = new AtividadeRN();
				$objAtividadeDTOLiberacao = new AtividadeDTO();
				$objAtividadeDTOLiberacao->retTodos();
				$objAtividadeDTOLiberacao->setDblIdProtocolo( $objProcedimentoDTO->getDblIdProcedimento() );
				$objAtividadeDTOLiberacao->setNumIdTarefa( TarefaRN::$TI_PROCESSO_RECEBIMENTO_CREDENCIAL );
				$objAtividadeDTOLiberacao->setNumIdUsuario( $numIdUsuarioAcesso );
				$arrDTOAtividades = $objAtividadeRN->listarRN0036( $objAtividadeDTOLiberacao );
				if (count($arrDTOAtividades)>0){
					$objAtividadeRN->excluirRN0034( $arrDTOAtividades );
				}
				
				//conclusão automática
				$objAtividadeRN = new AtividadeRN();
				$objAtividadeDTOLiberacao = new AtividadeDTO();
				$objAtividadeDTOLiberacao->retTodos();
				$objAtividadeDTOLiberacao->setDblIdProtocolo( $objProcedimentoDTO->getDblIdProcedimento() );
				$objAtividadeDTOLiberacao->setNumIdTarefa( TarefaRN::$TI_CONCLUSAO_AUTOMATICA_USUARIO );
				$arrDTOAtividades = $objAtividadeRN->listarRN0036( $objAtividadeDTOLiberacao );
				if (count($arrDTOAtividades)>0){
					$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
					$objAtributoAndamentoDTO->retNumIdAtividade();
					$objAtributoAndamentoDTO->setStrNome('USUARIO');
					$objAtributoAndamentoDTO->setStrIdOrigem($numIdUsuarioExterno);
					$objAtributoAndamentoDTO->setNumIdAtividade(InfraArray::converterArrInfraDTO($arrDTOAtividades, 'IdAtividade'), InfraDTO::$OPER_IN);
					$objAtributoAndamentoDTO->setOrdNumIdAtributoAndamento(InfraDTO::$TIPO_ORDENACAO_DESC);
					
					$objAtributoAndamentoRN = new AtributoAndamentoRN();
					$arrObjAtributoAndamentoDTO = $objAtributoAndamentoRN->listarRN1367($objAtributoAndamentoDTO);
					
					$objAtividadeDTOLiberacao = new AtividadeDTO();
					$objAtividadeDTOLiberacao->retTodos();
					$objAtividadeDTOLiberacao->setDblIdProtocolo( $objProcedimentoDTO->getDblIdProcedimento() );
					$objAtividadeDTOLiberacao->setNumIdAtividade(InfraArray::converterArrInfraDTO($arrObjAtributoAndamentoDTO, 'IdAtividade'), InfraDTO::$OPER_IN);
					$arrDTOAtividades = $objAtividadeRN->listarRN0036( $objAtividadeDTOLiberacao );
					if (count($arrDTOAtividades)>0){
						$objAtividadeRN->excluirRN0034( $arrDTOAtividades );
					}
				}
				
				//cassacão
				$objAtividadeRN = new AtividadeRN();
				$objAtividadeDTOLiberacao = new AtividadeDTO();
				$objAtividadeDTOLiberacao->retTodos();
				$objAtividadeDTOLiberacao->setDblIdProtocolo( $objProcedimentoDTO->getDblIdProcedimento() );
				$objAtividadeDTOLiberacao->setNumIdTarefa( TarefaRN::$TI_PROCESSO_CASSACAO_CREDENCIAL );
				$arrDTOAtividades = $objAtividadeRN->listarRN0036( $objAtividadeDTOLiberacao );
				if (count($arrDTOAtividades)>0){
					$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
					$objAtributoAndamentoDTO->retNumIdAtividade();
					$objAtributoAndamentoDTO->setStrNome('USUARIO');
					$objAtributoAndamentoDTO->setStrIdOrigem($numIdUsuarioExterno);
					$objAtributoAndamentoDTO->setNumIdAtividade(InfraArray::converterArrInfraDTO($arrDTOAtividades, 'IdAtividade'), InfraDTO::$OPER_IN);
					$objAtributoAndamentoDTO->setOrdNumIdAtributoAndamento(InfraDTO::$TIPO_ORDENACAO_DESC);
					
					$objAtributoAndamentoRN = new AtributoAndamentoRN();
					$arrObjAtributoAndamentoDTO = $objAtributoAndamentoRN->listarRN1367($objAtributoAndamentoDTO);
					
					$objAtividadeDTOLiberacao = new AtividadeDTO();
					$objAtividadeDTOLiberacao->retTodos();
					$objAtividadeDTOLiberacao->setDblIdProtocolo( $objProcedimentoDTO->getDblIdProcedimento() );
					$objAtividadeDTOLiberacao->setNumIdAtividade(InfraArray::converterArrInfraDTO($arrObjAtributoAndamentoDTO, 'IdAtividade'), InfraDTO::$OPER_IN);
					$arrDTOAtividades = $objAtividadeRN->listarRN0036( $objAtividadeDTOLiberacao );
					if (count($arrDTOAtividades)>0){
						$objAtividadeRN->excluirRN0034( $arrDTOAtividades );
					}
				}
				
				$objMdPetIntimacaoRN = new MdPetIntimacaoRN();
				$arrAtividadeDTO = $objMdPetIntimacaoRN->verificarUnidadeAberta( array($objProcedimentoDTO) );
				if (count($arrAtividadeDTO)==0){
					// Usuário antes de tratamento de SIGILOSO
					if ( is_numeric(SessaoSEI::getInstance()->getNumIdUsuario()) && is_numeric(SessaoSEI::getInstance()->getNumIdUnidadeAtual()) ){
						$numIdUsuarioAntesSigiloso = SessaoSEI::getInstance()->getNumIdUsuario();
						$numIdUnidadeAtualAntesSigiloso = SessaoSEI::getInstance()->getNumIdUnidadeAtual();
					}
					SessaoSEI::getInstance()->simularLogin(null, null, $objConcederCredencial->getNumIdUsuarioOrigem(), $objConcederCredencial->getNumIdUnidadeOrigem());
					
					$objReabrirProcessoDTO = new ReabrirProcessoDTO();
					$objReabrirProcessoDTO->setDblIdProcedimento( $objProcedimentoDTO->getDblIdProcedimento() );
					$objReabrirProcessoDTO->setNumIdUnidade( $objConcederCredencial->getNumIdUnidadeOrigem() );
					$objReabrirProcessoDTO->setNumIdUsuario( $objConcederCredencial->getNumIdUsuarioOrigem() );
					$this->reabrirRN0966Controlado($objReabrirProcessoDTO);
					
					// Usuário antes de tratamento de SIGILOSO - RETORNANDO
					if ($numIdUnidadeAtualAntesSigiloso!='' && $numIdUsuarioAntesSigiloso!=''){
						SessaoSEI::getInstance()->simularLogin(null, null, $numIdUsuarioAntesSigiloso, $numIdUnidadeAtualAntesSigiloso);
					}
					
				}
				
				
			}
		}catch(Exception $e){
			throw new InfraException('Erro excluirAndamentoCredencial.',$e);
		}
	}
	
}
