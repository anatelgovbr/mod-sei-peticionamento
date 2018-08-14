<?
/**
* ANATEL
*
* 28/06/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetAtividadeRN extends AtividadeRN {
	
	private $statusPesquisa = true;
	
	protected function gerarInternaRN0727CustomizadoControlado( $param ){
		
		try {
			
			$objAtividadeDTO = $param[0];
			$idUnidadeDTO = $param[1];
			
			//Valida Permissao
			//SessaoSEI::getInstance()->validarAuditarPermissao('atividade_gerar',__METHOD__,$objAtividadeDTO);
	
			//Regras de Negocio
			$objInfraException = new InfraException();
	
			$this->validarDblIdProtocoloRN0704($objAtividadeDTO, $objInfraException);
			$this->validarNumIdUnidadeRN0705($objAtividadeDTO, $objInfraException);
			$this->validarNumIdTarefaRN0706($objAtividadeDTO, $objInfraException);
	
			$numIdTarefa = $objAtividadeDTO->getNumIdTarefa(); //otimizacao de acesso
	
			if ($numIdTarefa == TarefaRN::$TI_GERACAO_PROCEDIMENTO){
				$objAtividadeDTO->setStrSinInicial('S');
			}else{
				$objAtividadeDTO->setStrSinInicial('N');
			}
	
			if ($objAtividadeDTO->isSetDtaPrazo()){
				$this->validarDtaPrazoRN0714($objAtividadeDTO, $objInfraException);
			}else{
				$objAtividadeDTO->setDtaPrazo(null);
			}
	
			if ($objAtividadeDTO->isSetNumIdUsuarioAtribuicao()){
				$this->validarNumIdUsuarioAtribuicao($objAtividadeDTO, $objInfraException);
			}else{
				$objAtividadeDTO->setNumIdUsuarioAtribuicao(null);
			}
	
			$objInfraException->lancarValidacoes();
	
			$objTarefaDTO = new TarefaDTO();
			$objTarefaDTO->retStrSinFecharAndamentosAbertos();
			$objTarefaDTO->retStrSinLancarAndamentoFechado();
			$objTarefaDTO->retStrSinPermiteProcessoFechado();
			$objTarefaDTO->setNumIdTarefa($numIdTarefa);
	
			$objTarefaRN = new TarefaRN();
			$objTarefaDTO = $objTarefaRN->consultar($objTarefaDTO);
	
			$objUnidadeDTO = new UnidadeDTO();
			$objUnidadeDTO->setBolExclusaoLogica(false);
			$objUnidadeDTO->retStrSinProtocolo();
			$objUnidadeDTO->setNumIdUnidade( $idUnidadeDTO );
	
			$objUnidadeRN = new UnidadeRN();
			$objUnidadeDTO = $objUnidadeRN->consultarRN0125($objUnidadeDTO);
	
			$bolFlagReaberturaAutomaticaProtocolo = false;
			if ($objUnidadeDTO->getStrSinProtocolo()=='S' &&
			$objAtividadeDTO->getNumIdUnidade() != $idUnidadeDTO &&
			$numIdTarefa == TarefaRN::$TI_REABERTURA_PROCESSO_UNIDADE){
				$bolFlagReaberturaAutomaticaProtocolo = true;
			}
	
			$objProtocoloDTO = new ProtocoloDTO();
			$objProtocoloDTO->retStrStaNivelAcessoGlobal();
			$objProtocoloDTO->retStrProtocoloFormatado();
			$objProtocoloDTO->retStrStaEstado();
			$objProtocoloDTO->setDblIdProtocolo($objAtividadeDTO->getDblIdProtocolo());
	
			$objProtocoloRN = new ProtocoloRN();
			$objProtocoloDTO = $objProtocoloRN->consultarRN0186($objProtocoloDTO);
	
			if ($objProtocoloDTO==null){
				throw new InfraException('Processo não encontrado.');
			}
	
			$strStaNivelAcessoGlobal = $objProtocoloDTO->getStrStaNivelAcessoGlobal();
	
			//alterando nível de acesso
			if ($numIdTarefa == TarefaRN::$TI_ALTERACAO_NIVEL_ACESSO_GLOBAL){
				if ($objAtividadeDTO->getNumIdUsuario()!=null){ //se alterando para sigiloso IdUsuario estará preenchido
					$objAtividadeDTO->setNumIdUsuarioAtribuicao($objAtividadeDTO->getNumIdUsuario());
				}
			}else{
	
				//concedendo credencial, transferindo credencial ou concedendo credencial de assinatura
				if ($strStaNivelAcessoGlobal == ProtocoloRN::$NA_SIGILOSO){
					if ($numIdTarefa == TarefaRN::$TI_PROCESSO_CONCESSAO_CREDENCIAL ||
					$numIdTarefa == TarefaRN::$TI_PROCESSO_TRANSFERENCIA_CREDENCIAL ||
					$numIdTarefa == TarefaRN::$TI_CONCESSAO_CREDENCIAL_ASSINATURA){
						//atribui para o usuario "destino"
						$objAtividadeDTO->setNumIdUsuarioAtribuicao($objAtividadeDTO->getNumIdUsuario());
					}else if ($numIdTarefa == TarefaRN::$TI_GERACAO_PROCEDIMENTO || $numIdTarefa == TarefaRN::$TI_REABERTURA_PROCESSO_USUARIO){
						//atribui para o usuario atual
						$objAtividadeDTO->setNumIdUsuarioAtribuicao(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
						$objAtividadeDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
					}else{
						 
						//verifica se o usuário atual tem acesso ao processo na unidade atual
						//se tiver acesso então preenche o IdUsuario automaticamente
						$objAcessoDTO = new AcessoDTO();
						$objAcessoDTO->setDblIdProtocolo($objAtividadeDTO->getDblIdProtocolo());
						$objAcessoDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
						$objAcessoDTO->setNumIdUnidade($idUnidadeDTO );
						 
						$objAcessoRN = new AcessoRN();
						 
						if ($objAcessoRN->contar($objAcessoDTO)){
							$objAtividadeDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
						}else{
							$objAtividadeDTO->setNumIdUsuario(null);
						}
					}
				}else{
	
					$objAtividadeDTO->setNumIdUsuario(null);

						if ($bolFlagReaberturaAutomaticaProtocolo || $numIdTarefa == TarefaRN::$TI_PROCESSO_REMETIDO_UNIDADE){

							//atribui para a última pessoa que trabalhou com o processo na unidade
							$dto = new AtividadeDTO();
							$dto->retNumIdUsuarioAtribuicao();
							$dto->setDblIdProtocolo($objAtividadeDTO->getDblIdProtocolo());
							$dto->setNumIdUnidade($objAtividadeDTO->getNumIdUnidade());
	
							//se remetendo verifica usuario de atribuicao apenas se o processo ja esta aberto na unidade
							if ($numIdTarefa == TarefaRN::$TI_PROCESSO_REMETIDO_UNIDADE){
								$dto->setDthConclusao(null);
							}
	
							$dto->setNumMaxRegistrosRetorno(1);
							$dto->setOrdNumIdAtividade(InfraDTO::$TIPO_ORDENACAO_DESC);
	
							$dto = $this->consultarRN0033($dto);
							if ($dto!=null){
								$objAtividadeDTO->setNumIdUsuarioAtribuicao($dto->getNumIdUsuarioAtribuicao());
							}

						}else if ($numIdTarefa == TarefaRN::$TI_REABERTURA_PROCESSO_UNIDADE){
							$objAtividadeDTO->setNumIdUsuarioAtribuicao(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
						}

				}
			}
	
			$strDataHoraAtual = InfraData::getStrDataHoraAtual();
	
			$objAtividadeDTO->setDthAbertura($strDataHoraAtual);
			$objAtividadeDTO->setNumIdUnidadeOrigem( $idUnidadeDTO );
			$objAtividadeDTO->setNumIdUsuarioOrigem(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
			$objAtividadeDTO->setNumTipoVisualizacao(self::$TV_VISUALIZADO);
	
			$arrObjAtividadeDTO = array();
	
			if (!$bolFlagReaberturaAutomaticaProtocolo && $objTarefaDTO->getStrSinFecharAndamentosAbertos()=='S'){
				 
				$objPesquisaPendenciaDTO = new PesquisaPendenciaDTO();
				$objPesquisaPendenciaDTO->setDblIdProtocolo( $objAtividadeDTO->getDblIdProtocolo() );
				$objPesquisaPendenciaDTO->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
				$objPesquisaPendenciaDTO->setNumIdUnidade( $objAtividadeDTO->getNumIdUnidade() );
		   
				$arrObjProcedimentoDTO = $this->listarPendenciasRN0754($objPesquisaPendenciaDTO);
	
				if (count($arrObjProcedimentoDTO)==1){
					$arrObjAtividadeDTO = $arrObjProcedimentoDTO[0]->getArrObjAtividadeDTO();
				}
			}
	
			$bolFlagConcluiu = false;
			 
			//se tem andamentos em aberto
			if (count($arrObjAtividadeDTO)){
				 
				$n = 0;
				foreach($arrObjAtividadeDTO as $dto){
					if ($dto->getStrSinInicial()=='S'){
						$n++;
					}
				}
	
				//se todos os andamentos são iniciais
				if ($n == count($arrObjAtividadeDTO)){
					$objAtividadeDTO->setStrSinInicial('S');
				}
		   
				foreach($arrObjAtividadeDTO as $dto){
		    
					//copia usuário que visualizou e o respectivo status de visualização
					if ($dto->getNumIdUsuarioVisualizacao()!=null){
						$objAtividadeDTO->setNumIdUsuarioVisualizacao($dto->getNumIdUsuarioVisualizacao());
						$objAtividadeDTO->setNumTipoVisualizacao($dto->getNumTipoVisualizacao());
					}
		    
					//copia usuário de atribuição
					if ($dto->getNumIdUsuarioAtribuicao()!=null && //último andamento tem atribuição
					$objAtividadeDTO->getNumIdUsuarioAtribuicao()==null && //nao foi atribuido antes
					$numIdTarefa != TarefaRN::$TI_REMOCAO_ATRIBUICAO){  //removendo atribuicao manualmente
						$objAtividadeDTO->setNumIdUsuarioAtribuicao($dto->getNumIdUsuarioAtribuicao());
					}
				}
	
				$this->concluirRN0726($arrObjAtividadeDTO);
	
				$bolFlagConcluiu = true;
	
				//quando reabrindo não tinha andamentos abertos e pode não ter tramitado
				//a verificação evita que na reabertura de um processo gerado que não tramitou ele
				//fique na coluna de recebidos
			}else if ($numIdTarefa == TarefaRN::$TI_REABERTURA_PROCESSO_UNIDADE ||
			$numIdTarefa == TarefaRN::$TI_REABERTURA_PROCESSO_USUARIO){
				 
				//verifica se o processo não tramitou fora da unidade
				$dto = new AtividadeDTO();
				$dto->setNumIdUnidade( $idUnidadeDTO );
				$dto->setDblIdProtocolo($objAtividadeDTO->getDblIdProtocolo());
				$dto->setStrSinInicial('N');
	
				if ($this->contarRN0035($dto)==0){
					$objAtividadeDTO->setStrSinInicial('S');
				}
			}
	
			//Lança andamento inicial:
			//- quando reabrindo automaticamente devido ao protocolo
			//- quando o processo esta sendo remetido para outra unidade
			//- quando esta sendo dada credencial de acesso ao processo para alguem em outra unidade
			//- quando esta sendo transferida credencial de acesso de ao processo na mesma unidade
			//- quanto esta sendo dada credencial de assinatura para alguem em outra unidade
		  
			if ($bolFlagReaberturaAutomaticaProtocolo ||
			$numIdTarefa == TarefaRN::$TI_PROCESSO_REMETIDO_UNIDADE ||
			$numIdTarefa == TarefaRN::$TI_PROCESSO_CONCESSAO_CREDENCIAL ||
			$numIdTarefa == TarefaRN::$TI_PROCESSO_TRANSFERENCIA_CREDENCIAL ||
			$numIdTarefa == TarefaRN::$TI_CONCESSAO_CREDENCIAL_ASSINATURA){
	
				$objAtividadeDTO->setNumIdUsuarioVisualizacao(null);
				$objAtividadeDTO->setNumIdUsuarioConclusao(null);
				$objAtividadeDTO->setDthConclusao(null);
				$objAtividadeDTO->setStrSinInicial('N');
				$objAtividadeDTO->setNumTipoVisualizacao(self::$TV_NAO_VISUALIZADO);
	
			}else if ($objTarefaDTO->getStrSinLancarAndamentoFechado()=='S'
					||
					(!$bolFlagConcluiu && //não estava com o processo aberto na unidade
							 
							($objTarefaDTO->getStrSinPermiteProcessoFechado()=='S'
	
									||
									 
									//incluindo documento ou recebendo documento externo em processo por web-services
									(!SessaoSEIExterna::getInstance()->isBolHabilitada() &&
											$objProtocoloDTO->getStrStaEstado()==ProtocoloRN::$TE_NORMAL &&
											($numIdTarefa==TarefaRN::$TI_GERACAO_DOCUMENTO ||
													$numIdTarefa==TarefaRN::$TI_RECEBIMENTO_DOCUMENTO ||
													$numIdTarefa==TarefaRN::$TI_ARQUIVO_ANEXADO))
									 
									||
									 
									//unidade PROTOCOLO pode lançar andamentos em processos que não estão abertos com ela, exceto nos casos onde a unidade PROTOCOLO
									//esteja realmente gerando ou reabrindo um processo ou alguma unidade esteja remetendo o processo para o PROTOCOLO
									($objUnidadeDTO->getStrSinProtocolo()=='S' &&
											$numIdTarefa != TarefaRN::$TI_GERACAO_PROCEDIMENTO &&
											$numIdTarefa != TarefaRN::$TI_REABERTURA_PROCESSO_UNIDADE &&
											$numIdTarefa != TarefaRN::$TI_REABERTURA_PROCESSO_USUARIO &&
											$numIdTarefa != TarefaRN::$TI_PROCESSO_REMETIDO_UNIDADE &&
											$numIdTarefa != TarefaRN::$TI_PROCESSO_CONCESSAO_CREDENCIAL &&
											$numIdTarefa != TarefaRN::$TI_PROCESSO_TRANSFERENCIA_CREDENCIAL &&
											$numIdTarefa != TarefaRN::$TI_CONCESSAO_CREDENCIAL_ASSINATURA)
									||
									//cancelando documento em processo anexado
									($objProtocoloDTO->getStrStaEstado()==ProtocoloRN::$TE_PROCEDIMENTO_ANEXADO &&
											($numIdTarefa == TarefaRN::$TI_CANCELAMENTO_DOCUMENTO ||
													$numIdTarefa == TarefaRN::$TI_GERACAO_DOCUMENTO ||
													$numIdTarefa == TarefaRN::$TI_ARQUIVO_ANEXADO ||
													$numIdTarefa == TarefaRN::$TI_ENVIO_EMAIL))))){
	
				//lança andamento fechado
				$objAtividadeDTO->setNumIdUsuarioConclusao(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
				$objAtividadeDTO->setDthConclusao($strDataHoraAtual);
	
			}else {
				 
				if (!$bolFlagConcluiu &&
				$numIdTarefa != TarefaRN::$TI_GERACAO_PROCEDIMENTO &&
				$numIdTarefa != TarefaRN::$TI_REABERTURA_PROCESSO_UNIDADE &&
				$numIdTarefa != TarefaRN::$TI_REABERTURA_PROCESSO_USUARIO &&
				$numIdTarefa != TarefaRN::$TI_CANCELAMENTO_AGENDAMENTO &&
				$numIdTarefa != TarefaRN::$TI_REMOCAO_SOBRESTANDO_PROCESSO){ //confirmacao de publicacao
					
					$objInfraException->lancarValidacao('Processo '.$objProtocoloDTO->getStrProtocoloFormatado().' não possui andamento aberto na unidade ID = '. $idUnidadeDTO .'.');
					
				}
				 
				//lança andamento em aberto mas não altera outros dados como usuário de visualização e atribuição
				$objAtividadeDTO->setNumIdUsuarioConclusao(null);
				$objAtividadeDTO->setDthConclusao(null);
			}
	
			$objAtividadeBD = new AtividadeBD($this->getObjInfraIBanco());
			
			if( !$objAtividadeDTO->isSetNumIdUsuarioOrigem() ||  $objAtividadeDTO->getNumIdUsuarioOrigem() == null ){
			  $objAtividadeDTO->setNumIdUsuarioOrigem(SessaoSEI::getInstance()->getNumIdUsuario());
			}
			
			$ret = $objAtividadeBD->cadastrar($objAtividadeDTO);
	
			//lança ícone de atenção para o processo em todas as unidades que possuam andamento aberto e já tenham visualizado
			if ($numIdTarefa == TarefaRN::$TI_ASSINATURA_DOCUMENTO || $numIdTarefa == TarefaRN::$TI_RECEBIMENTO_DOCUMENTO){
	
				$dto = new AtividadeDTO();
				$dto->setDblIdProtocolo($objAtividadeDTO->getDblIdProtocolo());
				 
				if ($strStaNivelAcessoGlobal==ProtocoloRN::$NA_SIGILOSO){
					$dto->setNumIdUsuario($objAtividadeDTO->getNumIdUsuario()); //em todos menos no atual
				}else{
					$dto->setNumIdUnidade($objAtividadeDTO->getNumIdUnidade()); //em todas menos na atual
				}
				 
				$dto->setNumTipoVisualizacao(self::$TV_ATENCAO);
				$this->atualizarVisualizacao($dto);
	
			}else if ($bolFlagReaberturaAutomaticaProtocolo){
	
				$dto = new AtividadeDTO();
				$dto->setDblIdProtocolo($objAtividadeDTO->getDblIdProtocolo());
				$dto->setNumIdUnidade($objAtividadeDTO->getNumIdUnidade());
				$dto->setNumTipoVisualizacao(self::$TV_ATENCAO);
				$this->atualizarVisualizacaoUnidade($dto);
	
			}else if ($numIdTarefa == TarefaRN::$TI_REMOCAO_SOBRESTAMENTO){
	
				//atualiza atividade de sobrestamento se existir em aberto
				$dto = new AtividadeDTO();
				$dto->retNumIdAtividade();
				$dto->retNumIdUnidade();
				$dto->retNumTipoVisualizacao();
				$dto->setNumIdTarefa(TarefaRN::$TI_SOBRESTAMENTO);
				$dto->setDthConclusao(null);
				$dto->setDblIdProtocolo($objAtividadeDTO->getDblIdProtocolo());
	
				$dto = $this->consultarRN0033($dto);
				 
				if ($dto != null){
					$dto->setNumTipoVisualizacao($dto->getNumTipoVisualizacao() | self::$TV_REMOCAO_SOBRESTAMENTO);
					$objAtividadeBD->alterar($dto);
				}
	
			}
	
			if (SessaoSEIExterna::getInstance()->getNumIdUsuarioEmulador()!=null){
	
				if ($objAtividadeDTO->isSetArrObjAtributoAndamentoDTO()){
					$arrObjAtributoAndamentoDTO = $objAtividadeDTO->getArrObjAtributoAndamentoDTO();
				}else{
					$arrObjAtributoAndamentoDTO = array();
				}
				 
				$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
				$objAtributoAndamentoDTO->setStrNome('USUARIO_EMULADOR');
				$objAtributoAndamentoDTO->setStrValor(SessaoSEIExterna::getInstance()->getStrSiglaUsuarioEmulador().'¥'.SessaoSEIExterna::getInstance()->getStrNomeUsuarioEmulador().'±'.SessaoSEIExterna::getInstance()->getStrSiglaOrgaoUsuarioEmulador().'¥'.SessaoSEIExterna::getInstance()->getStrDescricaoOrgaoUsuarioEmulador());
				$objAtributoAndamentoDTO->setStrIdOrigem(SessaoSEIExterna::getInstance()->getNumIdUsuarioEmulador().'/'.SessaoSEIExterna::getInstance()->getNumIdOrgaoUsuarioEmulador());
				$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;
	
				$objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO);
			}
	
			if ($objAtividadeDTO->isSetArrObjAtributoAndamentoDTO()){
				$objAtributoAndamentoRN = new AtributoAndamentoRN();
				$arrObjAtributoAndamentoDTO = $objAtividadeDTO->getArrObjAtributoAndamentoDTO();
				foreach($arrObjAtributoAndamentoDTO as $objAtributoAndamentoDTO){
					$objAtributoAndamentoDTO->setNumIdAtividade($ret->getNumIdAtividade());
					$objAtributoAndamentoRN->cadastrarRN1363($objAtributoAndamentoDTO);
				}
			}
			 
			return $ret;
	
			//Auditoria
	
		}catch(Exception $e){
			throw new InfraException('Erro cadastrando andamento do SEI.',$e);
		}
	}
	
	private function validarDblIdProtocoloRN0704(AtividadeDTO $objAtividadeDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objAtividadeDTO->getDblIdProtocolo())){
			$objInfraException->adicionarValidacao('Protocolo não informado.');
		}
	}
	
	private function validarNumIdUnidadeRN0705(AtividadeDTO $objAtividadeDTO, InfraException $objInfraException){
				
		$idUnidade = $objAtividadeDTO->get('IdUnidade');
		
		if ( $idUnidade == null || $idUnidade == '' ){
			$objInfraException->adicionarValidacao('Unidade não informada.');
		}
	}
	
	private function validarNumIdUnidadeOrigemRN0707(AtividadeDTO $objAtividadeDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objAtividadeDTO->getNumIdUnidadeOrigem())){
			$objAtividadeDTO->setNumIdUnidadeOrigem(null);
		}
	}
	
	private function validarNumIdUsuario(AtividadeDTO $objAtividadeDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objAtividadeDTO->getNumIdUsuario())){
			$objAtividadeDTO->setNumIdUsuario(null);
		}
	}
	
	private function validarNumIdTarefaRN0706(AtividadeDTO $objAtividadeDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objAtividadeDTO->getNumIdTarefa())){
			$objInfraException->adicionarValidacao('Tarefa não informada.');
		}
	}
	
	private function validarNumIdUsuarioOrigemRN0708(AtividadeDTO $objAtividadeDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objAtividadeDTO->getNumIdUsuarioOrigem())){
			$objInfraException->adicionarValidacao('Usuário origem não informado.');
		}
	}
	
	private function validarNumIdUsuarioVisualizacao(AtividadeDTO $objAtividadeDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objAtividadeDTO->getNumIdUsuarioVisualizacao())){
			$objAtividadeDTO->setNumIdUsuarioVisualizacao(null);
		}
	}
	
	private function validarNumIdUsuarioAtribuicao(AtividadeDTO $objAtividadeDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objAtividadeDTO->getNumIdUsuarioAtribuicao())){
			$objAtividadeDTO->setNumIdUsuarioAtribuicao(null);
		}
	}
	
	private function validarNumIdUsuarioConclusaoRN1194(AtividadeDTO $objAtividadeDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objAtividadeDTO->getNumIdUsuarioConclusao())){
			$objInfraException->adicionarValidacao('Usuário de conclusão não informado.');
		}
	}
	
	private function validarDthConclusaoRN0711(AtividadeDTO $objAtividadeDTO, InfraException $objInfraException){
	
		if (InfraString::isBolVazia($objAtividadeDTO->getDthConclusao())){
			$objAtividadeDTO->setDthConclusao(null);
		}else{
			if (!InfraData::validarDataHora($objAtividadeDTO->getDthConclusao())){
				$objInfraException->adicionarValidacao('Data de conclusão inválida.');
			}
		}
	}
	
	private function validarStrSinInicial(AtividadeDTO $objAtividadeDTO, InfraException $objInfraException){
	
		if (InfraString::isBolVazia($objAtividadeDTO->getStrSinInicial())){
			$objInfraException->adicionarValidacao('Sinalizador de andamento inicial não informado.');
		}else{
			if (!InfraUtil::isBolSinalizadorValido($objAtividadeDTO->getStrSinInicial())){
				$objInfraException->adicionarValidacao('Sinalizador de andamento inicial inválido.');
			}
		}
	}
	
	private function validarDtaPrazoRN0714(AtividadeDTO $objAtividadeDTO, InfraException $objInfraException){
	
		if (InfraString::isBolVazia($objAtividadeDTO->getDtaPrazo())){
			$objAtividadeDTO->setDtaPrazo(null);
		}else{
			if (!InfraData::validarData($objAtividadeDTO->getDtaPrazo())){
				$objInfraException->adicionarValidacao('Data de retorno programado da atividade inválida.');
			}
	
			if (InfraData::compararDatas(InfraData::getStrDataAtual(),$objAtividadeDTO->getDtaPrazo())<0){
				$objInfraException->adicionarValidacao('Data de retorno programado da atividade não pode estar no passado.');
			}
		}
	}
	
	private function validarDtaPrazo(EnviarProcessoDTO $objEnviarProcessoDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objEnviarProcessoDTO->getDtaPrazo())){
			$objEnviarProcessoDTO->setDtaPrazo(null);
		}else{
			if (!InfraData::validarData($objEnviarProcessoDTO->getDtaPrazo())){
				$objInfraException->adicionarValidacao('Data de retorno programado inválida.');
			}
	
			if (InfraData::compararDatas(InfraData::getStrDataAtual(),$objEnviarProcessoDTO->getDtaPrazo())<0){
				$objInfraException->adicionarValidacao('Data de retorno programado não pode estar no passado.');
			}
		}
	}
	
	private function validarNumDias(EnviarProcessoDTO $objEnviarProcessoDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objEnviarProcessoDTO->getNumDias())){
			$objEnviarProcessoDTO->setNumDias(null);
		}else{
	
			$objEnviarProcessoDTO->setNumDias(trim($objEnviarProcessoDTO->getNumDias()));
	
			if (!is_numeric($objEnviarProcessoDTO->getNumDias()) ||	$objEnviarProcessoDTO->getNumDias() < 1){
				$objInfraException->adicionarValidacao('Número de dias para retorno programado inválido.');
			}
		}
	}
	
	public function setStatusPesquisa($statusPesquisa) {
	
		$this->statusPesquisa = $statusPesquisa;
	}


	protected function alterarControlado(AtividadeDTO $objAtividadeDTO){
      try {

			//Valida Permissao
			//SessaoSEI::getInstance()->validarPermissao('');

			//Regras de Negocio
			//$objInfraException = new InfraException();

			//$objInfraException->lancarValidacoes();

			$objAtividadeBD = new AtividadeBD($this->getObjInfraIBanco());
			$objAtividadeBD->alterar($objAtividadeDTO);

			//Auditoria

      }catch(Exception $e){
		throw new InfraException('Erro alterando Atividade.',$e);
      }
	}

	protected function listarPendenciasRN0754Conectado(PesquisaPendenciaDTO $objPesquisaPendenciaDTO) {

		if ($this->statusPesquisa) {
			if (!$objPesquisaPendenciaDTO->isSetStrStaEstadoProcedimento()) {
				$objPesquisaPendenciaDTO->setStrStaEstadoProcedimento(ProtocoloRN::$TE_NORMAL);
			}
		}
	
		if (!$objPesquisaPendenciaDTO->isSetStrStaTipoAtribuicao()) {
			$objPesquisaPendenciaDTO->setStrStaTipoAtribuicao(self::$TA_TODAS);
		}
	
		if (!$objPesquisaPendenciaDTO->isSetNumIdUsuarioAtribuicao()) {
			$objPesquisaPendenciaDTO->setNumIdUsuarioAtribuicao(null);
		}
	
		if (!$objPesquisaPendenciaDTO->isSetStrSinMontandoArvore()) {
			$objPesquisaPendenciaDTO->setStrSinMontandoArvore('N');
		}

		if (!$objPesquisaPendenciaDTO->isSetStrSinAnotacoes()) {
			$objPesquisaPendenciaDTO->setStrSinAnotacoes('N');
		}
	
		if (!$objPesquisaPendenciaDTO->isSetStrSinInteressados()) {
			$objPesquisaPendenciaDTO->setStrSinInteressados('N');
		}
	
		if (!$objPesquisaPendenciaDTO->isSetStrSinRetornoProgramado()) {
			$objPesquisaPendenciaDTO->setStrSinRetornoProgramado('N');
		}
	
		if (!$objPesquisaPendenciaDTO->isSetStrSinCredenciais()) {
			$objPesquisaPendenciaDTO->setStrSinCredenciais('N');
		}

		if (!$objPesquisaPendenciaDTO->isSetStrSinHoje()) {
			$objPesquisaPendenciaDTO->setStrSinHoje('N');
		}
	
	
		$objAtividadeDTO = new AtividadeDTO();
		$objAtividadeDTO->retNumIdAtividade();
		$objAtividadeDTO->retNumIdTarefa();
		$objAtividadeDTO->retNumIdUsuarioAtribuicao();
		$objAtividadeDTO->retNumIdUsuarioVisualizacao();
		$objAtividadeDTO->retNumTipoVisualizacao();
		$objAtividadeDTO->retNumIdUnidade();
		$objAtividadeDTO->retDthConclusao();
		$objAtividadeDTO->retDblIdProtocolo();
		$objAtividadeDTO->retStrSiglaUnidade();
		$objAtividadeDTO->retStrSinInicial();
		$objAtividadeDTO->retNumIdUsuarioAtribuicao();
		$objAtividadeDTO->retStrSiglaUsuarioAtribuicao();
		$objAtividadeDTO->retStrNomeUsuarioAtribuicao();
	
		$objAtividadeDTO->setNumIdUnidade($objPesquisaPendenciaDTO->getNumIdUnidade());
	
		if ($objPesquisaPendenciaDTO->getStrSinHoje() == 'N') {
			$objAtividadeDTO->setDthConclusao(null);
		} else {
			$objAtividadeDTO->adicionarCriterio(array('Conclusao', 'Conclusao'), array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_MAIOR_IGUAL), array(null, InfraData::getStrDataAtual() . ' 00:00:00'), array(InfraDTO::$OPER_LOGICO_OR));
		}
	
		$objAtividadeDTO->adicionarCriterio(array('StaNivelAcessoGlobalProtocolo'), array(InfraDTO::$OPER_DIFERENTE), array(ProtocoloRN::$NA_SIGILOSO), array(), 'criterioRestritosPublicos');
	
		$objAtividadeDTO->adicionarCriterio(array('StaNivelAcessoGlobalProtocolo', 'IdUsuario'), array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL), array(ProtocoloRN::$NA_SIGILOSO, $objPesquisaPendenciaDTO->getNumIdUsuario()), array(InfraDTO::$OPER_LOGICO_AND), 'criterioSigilosos');
	
		$objAtividadeDTO->agruparCriterios(array('criterioRestritosPublicos', 'criterioSigilosos'), array(InfraDTO::$OPER_LOGICO_OR));
	
		if ($objPesquisaPendenciaDTO->getStrStaTipoAtribuicao() == self::$TA_MINHAS) {
			$objAtividadeDTO->setNumIdUsuarioAtribuicao($objPesquisaPendenciaDTO->getNumIdUsuario());
		} else if ($objPesquisaPendenciaDTO->getStrStaTipoAtribuicao() == self::$TA_DEFINIDAS) {
			$objAtividadeDTO->setNumIdUsuarioAtribuicao(null, InfraDTO::$OPER_DIFERENTE);
		} else if ($objPesquisaPendenciaDTO->getStrStaTipoAtribuicao() == self::$TA_ESPECIFICAS) {
			$objAtividadeDTO->setNumIdUsuarioAtribuicao($objPesquisaPendenciaDTO->getNumIdUsuarioAtribuicao());
		}
	
		if ($objPesquisaPendenciaDTO->isSetDblIdProtocolo()) {
			if (!is_array($objPesquisaPendenciaDTO->getDblIdProtocolo())) {
				$objAtividadeDTO->setDblIdProtocolo($objPesquisaPendenciaDTO->getDblIdProtocolo());
			} else {
				$objAtividadeDTO->setDblIdProtocolo($objPesquisaPendenciaDTO->getDblIdProtocolo(), InfraDTO::$OPER_IN);
			}
		}
	
		if ($objPesquisaPendenciaDTO->isSetStrStaEstadoProcedimento()) {
			if (is_array($objPesquisaPendenciaDTO->getStrStaEstadoProcedimento())) {
				$objAtividadeDTO->setStrStaEstadoProtocolo($objPesquisaPendenciaDTO->getStrStaEstadoProcedimento(), InfraDTO::$OPER_IN);
			} else {
				$objAtividadeDTO->setStrStaEstadoProtocolo($objPesquisaPendenciaDTO->getStrStaEstadoProcedimento());
			}
		}
	
		//ordenar pela data de abertura descendente
		$objAtividadeDTO->setOrdDthAbertura(InfraDTO::$TIPO_ORDENACAO_DESC);
	
	
		//paginação
		$objAtividadeDTO->setNumMaxRegistrosRetorno($objPesquisaPendenciaDTO->getNumMaxRegistrosRetorno());
		$objAtividadeDTO->setNumPaginaAtual($objPesquisaPendenciaDTO->getNumPaginaAtual());
	
		$arrAtividadeDTO = $this->listarRN0036($objAtividadeDTO);
	
		//paginação
		$objPesquisaPendenciaDTO->setNumTotalRegistros($objAtividadeDTO->getNumTotalRegistros());
		$objPesquisaPendenciaDTO->setNumRegistrosPaginaAtual($objAtividadeDTO->getNumRegistrosPaginaAtual());
	
		$arrProcedimentos = array();
	
		//Se encontrou pelo menos um registro
		if (count($arrAtividadeDTO) > 0) {
	
			$objProcedimentoDTO = new ProcedimentoDTO();
	
			$objProcedimentoDTO->retDblIdProcedimento();
			$objProcedimentoDTO->retStrProtocoloProcedimentoFormatado();
			$objProcedimentoDTO->retStrNomeTipoProcedimento();
			$objProcedimentoDTO->retNumIdUnidadeGeradoraProtocolo();
			$objProcedimentoDTO->retStrStaEstadoProtocolo();
			$objProcedimentoDTO->retStrDescricaoProtocolo();
			$objProcedimentoDTO->retArrObjDocumentoDTO();
	
	
			$arrProtocolosAtividades = array_unique(InfraArray::converterArrInfraDTO($arrAtividadeDTO, 'IdProtocolo'));
			$objProcedimentoDTO->setDblIdProcedimento($arrProtocolosAtividades, InfraDTO::$OPER_IN);
	
			if ($objPesquisaPendenciaDTO->getStrSinMontandoArvore() == 'S') {
				$objProcedimentoDTO->setStrSinMontandoArvore('S');
			}

			if ($objPesquisaPendenciaDTO->isSetDblIdDocumento()) {
				$objProcedimentoDTO->setArrDblIdProtocoloAssociado(array($objPesquisaPendenciaDTO->getDblIdDocumento()));
			}
	
			$objProcedimentoRN = new ProcedimentoRN();
	
			$arr = InfraArray::indexarArrInfraDTO($objProcedimentoRN->listarCompleto($objProcedimentoDTO), 'IdProcedimento');
	
			$arrObjAnotacaoDTO = null;
			if ($objPesquisaPendenciaDTO->getStrSinAnotacoes() == 'S') {
				$objAnotacaoDTO = new AnotacaoDTO();
				$objAnotacaoDTO->retDblIdProtocolo();
				$objAnotacaoDTO->retStrDescricao();
				$objAnotacaoDTO->retStrSiglaUsuario();
				$objAnotacaoDTO->retStrNomeUsuario();
				$objAnotacaoDTO->retStrSinPrioridade();
				$objAnotacaoDTO->retNumIdUsuario();
				$objAnotacaoDTO->retStrStaAnotacao();
				$objAnotacaoDTO->setNumIdUnidade($objPesquisaPendenciaDTO->getNumIdUnidade());
				$objAnotacaoDTO->setDblIdProtocolo($arrProtocolosAtividades, InfraDTO::$OPER_IN);
	
				$objAnotacaoRN = new AnotacaoRN();
				$arrObjAnotacaoDTO = InfraArray::indexarArrInfraDTO($objAnotacaoRN->listar($objAnotacaoDTO), 'IdProtocolo', true);
			}
	
	
			$arrObjParticipanteDTO = null;
			if ($objPesquisaPendenciaDTO->getStrSinInteressados() == 'S') {
	
				$arrObjParticipanteDTO = array();
	
				$objParticipanteDTO = new ParticipanteDTO();
				$objParticipanteDTO->retDblIdProtocolo();
				$objParticipanteDTO->retStrSiglaContato();
				$objParticipanteDTO->retStrNomeContato();
				$objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_INTERESSADO);
				$objParticipanteDTO->setDblIdProtocolo($arrProtocolosAtividades, InfraDTO::$OPER_IN);
	
				$objParticipanteRN = new ParticipanteRN();
				$arrTemp = $objParticipanteRN->listarRN0189($objParticipanteDTO);
	
				foreach ($arrTemp as $objParticipanteDTO) {
					if (!isset($arrObjParticipanteDTO[$objParticipanteDTO->getDblIdProtocolo()])) {
						$arrObjParticipanteDTO[$objParticipanteDTO->getDblIdProtocolo()] = array($objParticipanteDTO);
					} else {
						$arrObjParticipanteDTO[$objParticipanteDTO->getDblIdProtocolo()][] = $objParticipanteDTO;
					}
				}
			}
	
			$arrObjRetornoProgramadoDTO = null;
			if ($objPesquisaPendenciaDTO->getStrSinRetornoProgramado() == 'S') {
				$objRetornoProgramadoDTO = new RetornoProgramadoDTO();
				$objRetornoProgramadoDTO->retDblIdProtocoloAtividadeEnvio();
				$objRetornoProgramadoDTO->retStrSiglaUnidadeOrigemAtividadeEnvio();
				$objRetornoProgramadoDTO->retDtaProgramada();
				$objRetornoProgramadoDTO->setNumIdUnidadeAtividadeEnvio($objPesquisaPendenciaDTO->getNumIdUnidade());
				$objRetornoProgramadoDTO->setDblIdProtocoloAtividadeEnvio($arrProtocolosAtividades, InfraDTO::$OPER_IN);
				$objRetornoProgramadoDTO->setNumIdAtividadeRetorno(null);
	
				$objRetornoProgramadoRN = new RetornoProgramadoRN();
				$arrObjRetornoProgramadoDTO = InfraArray::indexarArrInfraDTO($objRetornoProgramadoRN->listar($objRetornoProgramadoDTO), 'IdProtocoloAtividadeEnvio', true);
			}
	
	
			//Manter ordem obtida na listagem das atividades
			$arrAdicionados = array();
			$arrIdProcedimentoSigiloso = array();
	
			foreach ($arrAtividadeDTO as $objAtividadeDTO) {
	
				$objProcedimentoDTO = $arr[$objAtividadeDTO->getDblIdProtocolo()];
	
				//pode não existir se o procedimento foi excluído
				if ($objProcedimentoDTO != null) {
	
					$dblIdProcedimento = $objProcedimentoDTO->getDblIdProcedimento();
	
					if ($objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo() == ProtocoloRN::$NA_SIGILOSO) {
	
						$objProcedimentoDTO->setStrSinCredencialProcesso('N');
						$objProcedimentoDTO->setStrSinCredencialAssinatura('N');
	
						$arrIdProcedimentoSigiloso[] = $dblIdProcedimento;
					}
	
					if (!isset($arrAdicionados[$dblIdProcedimento])) {
						$objProcedimentoDTO->setArrObjAtividadeDTO(array($objAtividadeDTO));
	
						if (is_array($arrObjAnotacaoDTO)) {
	
							$objProcedimentoDTO->setObjAnotacaoDTO(null);
	
							if (isset($arrObjAnotacaoDTO[$dblIdProcedimento])) {
	
								foreach ($arrObjAnotacaoDTO[$dblIdProcedimento] as $objAnotacaoDTO) {
									if ($objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo() == ProtocoloRN::$NA_SIGILOSO) {
										if ($objAnotacaoDTO->getNumIdUsuario() == $objPesquisaPendenciaDTO->getNumIdUsuario() && $objAnotacaoDTO->getStrStaAnotacao() == AnotacaoRN::$TA_INDIVIDUAL) {
											$objProcedimentoDTO->setObjAnotacaoDTO($objAnotacaoDTO);
											break;
										}
									} else {
										if ($objAnotacaoDTO->getStrStaAnotacao() == AnotacaoRN::$TA_UNIDADE) {
											$objProcedimentoDTO->setObjAnotacaoDTO($objAnotacaoDTO);
											break;
										}
									}
								}
							}
						}
	
						if (is_array($arrObjParticipanteDTO)) {
							if (isset($arrObjParticipanteDTO[$dblIdProcedimento])) {
								$objProcedimentoDTO->setArrObjParticipanteDTO($arrObjParticipanteDTO[$dblIdProcedimento]);
							} else {
								$objProcedimentoDTO->setArrObjParticipanteDTO(null);
							}
						}
	
						if (is_array($arrObjRetornoProgramadoDTO)) {
							if (isset($arrObjRetornoProgramadoDTO[$dblIdProcedimento])) {
								$objProcedimentoDTO->setArrObjRetornoProgramadoDTO($arrObjRetornoProgramadoDTO[$dblIdProcedimento]);
							} else {
								$objProcedimentoDTO->setArrObjRetornoProgramadoDTO(null);
							}
						}
	
						$arrProcedimentos[] = $objProcedimentoDTO;
						$arrAdicionados[$dblIdProcedimento] = 0;
					} else {
						$arrAtividadeDTOProcedimento = $objProcedimentoDTO->getArrObjAtividadeDTO();
						$arrAtividadeDTOProcedimento[] = $objAtividadeDTO;
						$objProcedimentoDTO->setArrObjAtividadeDTO($arrAtividadeDTOProcedimento);
					}
				}
			}
	
			if ($objPesquisaPendenciaDTO->getStrSinCredenciais() == 'S' && count($arrIdProcedimentoSigiloso)) {
	
				$objAcessoDTO = new AcessoDTO();
				$objAcessoDTO->retDblIdProtocolo();
				$objAcessoDTO->retStrStaTipo();
				$objAcessoDTO->setNumIdUsuario($objPesquisaPendenciaDTO->getNumIdUsuario());
				$objAcessoDTO->setNumIdUnidade($objPesquisaPendenciaDTO->getNumIdUnidade());
				$objAcessoDTO->setStrStaTipo(array(AcessoRN::$TA_CREDENCIAL_PROCESSO, AcessoRN::$TA_CREDENCIAL_ASSINATURA_PROCESSO), InfraDTO::$OPER_IN);
				$objAcessoDTO->setDblIdProtocolo($arrIdProcedimentoSigiloso, InfraDTO::$OPER_IN);
	
				$objAcessoRN = new AcessoRN();
				$arrObjAcessoDTO = $objAcessoRN->listar($objAcessoDTO);

				foreach ($arrObjAcessoDTO as $objAcessoDTO) {
					if ($objAcessoDTO->getStrStaTipo() == AcessoRN::$TA_CREDENCIAL_PROCESSO) {
						$arr[$objAcessoDTO->getDblIdProtocolo()]->setStrSinCredencialProcesso('S');
					} else if ($objAcessoDTO->getStrStaTipo() == AcessoRN::$TA_CREDENCIAL_ASSINATURA_PROCESSO) {
						$arr[$objAcessoDTO->getDblIdProtocolo()]->setStrSinCredencialAssinatura('S');
					}
				}
			}
		}
	
		return $arrProcedimentos;
	}

	// AtividadeRN.php -> listarUnidadesTramitacaoControlado - sem ordenação alfabética 
	protected function listarUnidadesTramitacaoControlado(ProcedimentoDTO $objProcedimentoDTO){
		try{

			$objAtividadeDTO = new AtividadeDTO();
			$objAtividadeDTO->setDistinct(true);
			$objAtividadeDTO->retNumIdUnidade();
			$objAtividadeDTO->retStrSiglaUnidade();
			$objAtividadeDTO->retStrDescricaoUnidade();
			$objAtividadeDTO->setNumIdTarefa(TarefaRN::getArrTarefasTramitacao(), InfraDTO::$OPER_IN);
			$objAtividadeDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());
			$objAtividadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual(),InfraDTO::$OPER_DIFERENTE);
			$objAtividadeDTO->setOrdNumIdAtividade(InfraDTO::$TIPO_ORDENACAO_ASC);

			$objAtividadeRN = new AtividadeRN();
			$arrObjAtividadeDTO = $objAtividadeRN->listarRN0036($objAtividadeDTO);

			foreach($arrObjAtividadeDTO as $objAtividadeDTO){
				$objAtividadeDTO->setDtaPrazo(null);
			}

			if (count($arrObjAtividadeDTO)>0){
				$arrObjAtividadeDTO = InfraArray::indexarArrInfraDTO($arrObjAtividadeDTO,'IdUnidade');

				$arrIdUnidade=InfraArray::converterArrInfraDTO($arrObjAtividadeDTO,'IdUnidade');

				//Acessar os retornos programados para a unidade atual
				$objRetornoProgramadoDTO = new RetornoProgramadoDTO();
				$objRetornoProgramadoDTO->setNumFiltroFkAtividadeRetorno(InfraDTO::$FILTRO_FK_WHERE);
				$objRetornoProgramadoDTO->retNumIdUnidade();
				$objRetornoProgramadoDTO->retDtaProgramada();
				$objRetornoProgramadoDTO->setNumIdUnidade($arrIdUnidade,InfraDTO::$OPER_IN);
				$objRetornoProgramadoDTO->setDblIdProtocoloAtividadeEnvio($objProcedimentoDTO->getDblIdProcedimento());
				$objRetornoProgramadoDTO->setNumIdUnidadeAtividadeEnvio(null);
				$objRetornoProgramadoDTO->setNumIdUnidadeAtividadeRetorno(null);

				$objRetornoProgramadoRN = new RetornoProgramadoRN();
				$arrObjRetornoProgramadoDTO = $objRetornoProgramadoRN->listar($objRetornoProgramadoDTO);

				foreach ($arrObjRetornoProgramadoDTO as $objRetornoProgramadoDTO) {
					$arrObjAtividadeDTO[$objRetornoProgramadoDTO->getNumIdUnidade()]->setDtaPrazo($objRetornoProgramadoDTO->getDtaProgramada());
				}
			}

			return $arrObjAtividadeDTO;

		}catch(Exception $e){
			throw new InfraException('Erro listando unidades de tramitação.',$e);
		}
	}

	protected function gerarInternaRN0727Controlado(AtividadeDTO $objAtividadeDTO){
		
		try {
	
			//Valida Permissao
			//SessaoSEI::getInstance()->validarAuditarPermissao('atividade_gerar',__METHOD__,$objAtividadeDTO);
	
			//Regras de Negocio
			$objInfraException = new InfraException();
	
			$this->validarDblIdProtocoloRN0704($objAtividadeDTO, $objInfraException);
			$this->validarNumIdUnidadeRN0705($objAtividadeDTO, $objInfraException);
			$this->validarNumIdTarefaRN0706($objAtividadeDTO, $objInfraException);
	
			$numIdTarefa = $objAtividadeDTO->getNumIdTarefa(); //otimizacao de acesso
	
			if ($numIdTarefa == TarefaRN::$TI_GERACAO_PROCEDIMENTO){
				$objAtividadeDTO->setStrSinInicial('S');
			}else{
				$objAtividadeDTO->setStrSinInicial('N');
			}
	
			if ($objAtividadeDTO->isSetDtaPrazo()){
				$this->validarDtaPrazoRN0714($objAtividadeDTO, $objInfraException);
			}else{
				$objAtividadeDTO->setDtaPrazo(null);
			}
	
			if ($objAtividadeDTO->isSetNumIdUsuarioAtribuicao()){
				$this->validarNumIdUsuarioAtribuicao($objAtividadeDTO, $objInfraException);
			}else{
				$objAtividadeDTO->setNumIdUsuarioAtribuicao(null);
			}
	
			$objInfraException->lancarValidacoes();
	
			$objTarefaDTO = new TarefaDTO();
			$objTarefaDTO->retStrSinFecharAndamentosAbertos();
			$objTarefaDTO->retStrSinLancarAndamentoFechado();
			$objTarefaDTO->retStrSinPermiteProcessoFechado();
			$objTarefaDTO->setNumIdTarefa($numIdTarefa);
	
			$objTarefaRN = new TarefaRN();
			$objTarefaDTO = $objTarefaRN->consultar($objTarefaDTO);
	
			$objUnidadeDTO = new UnidadeDTO();
			$objUnidadeDTO->setBolExclusaoLogica(false);
			$objUnidadeDTO->retStrSinProtocolo();

			$objUnidadeDTO->setNumIdUnidade( $objAtividadeDTO->getNumIdUnidade() );

			$objUnidadeRN = new UnidadeRN();
			$objUnidadeDTO = $objUnidadeRN->consultarRN0125($objUnidadeDTO);
	
			$bolFlagReaberturaAutomaticaProtocolo = false;

			$objProtocoloDTO = new ProtocoloDTO();
			$objProtocoloDTO->retStrStaNivelAcessoGlobal();
			$objProtocoloDTO->retStrProtocoloFormatado();
			$objProtocoloDTO->retStrStaEstado();
			$objProtocoloDTO->setDblIdProtocolo($objAtividadeDTO->getDblIdProtocolo());
	
			$objProtocoloRN = new ProtocoloRN();
			$objProtocoloDTO = $objProtocoloRN->consultarRN0186($objProtocoloDTO);
	
			if ($objProtocoloDTO==null){
				throw new InfraException('Processo não encontrado.');
			}
	
			$strStaNivelAcessoGlobal = $objProtocoloDTO->getStrStaNivelAcessoGlobal();
	
			//alterando nível de acesso
			if ($numIdTarefa == TarefaRN::$TI_ALTERACAO_NIVEL_ACESSO_GLOBAL){
				
				if ($objAtividadeDTO->getNumIdUsuario()!=null){ //se alterando para sigiloso IdUsuario estará preenchido
					$objAtividadeDTO->setNumIdUsuarioAtribuicao($objAtividadeDTO->getNumIdUsuario());
				}
				
			} else {
	
				//concedendo credencial, transferindo credencial ou concedendo credencial de assinatura
				if ($strStaNivelAcessoGlobal == ProtocoloRN::$NA_SIGILOSO){
					if ($numIdTarefa == TarefaRN::$TI_PROCESSO_CONCESSAO_CREDENCIAL ||
					$numIdTarefa == TarefaRN::$TI_PROCESSO_TRANSFERENCIA_CREDENCIAL ||
					$numIdTarefa == TarefaRN::$TI_CONCESSAO_CREDENCIAL_ASSINATURA){
						//atribui para o usuario "destino"
						$objAtividadeDTO->setNumIdUsuarioAtribuicao($objAtividadeDTO->getNumIdUsuario());
					}else if ($numIdTarefa == TarefaRN::$TI_GERACAO_PROCEDIMENTO || $numIdTarefa == TarefaRN::$TI_REABERTURA_PROCESSO_USUARIO){
						//atribui para o usuario atual
						$objAtividadeDTO->setNumIdUsuarioAtribuicao(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
						$objAtividadeDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
					}else{
						 
						//verifica se o usuário atual tem acesso ao processo na unidade atual
						//se tiver acesso então preenche o IdUsuario automaticamente
						$objAcessoDTO = new AcessoDTO();
						$objAcessoDTO->setDblIdProtocolo($objAtividadeDTO->getDblIdProtocolo());
						$objAcessoDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());

						$objAcessoDTO->setNumIdUnidade( $objAtividadeDTO->getNumIdUnidade() );

						$objAcessoRN = new AcessoRN();
						 
						if ($objAcessoRN->contar($objAcessoDTO)){
							$objAtividadeDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
						}else{
							$objAtividadeDTO->setNumIdUsuario(null);
						}
					}
				}else{
	
					$objAtividadeDTO->setNumIdUsuario(null);
	
					if (SessaoSEIExterna::getInstance()->isBolHabilitada()){
	
						if ($bolFlagReaberturaAutomaticaProtocolo || $numIdTarefa == TarefaRN::$TI_PROCESSO_REMETIDO_UNIDADE){
	
							//atribui para a última pessoa que trabalhou com o processo na unidade
							$dto = new AtividadeDTO();
							$dto->retNumIdUsuarioAtribuicao();
							$dto->setDblIdProtocolo($objAtividadeDTO->getDblIdProtocolo());
							$dto->setNumIdUnidade($objAtividadeDTO->getNumIdUnidade());
	
							//se remetendo verifica usuario de atribuicao apenas se o processo ja esta aberto na unidade
							if ($numIdTarefa == TarefaRN::$TI_PROCESSO_REMETIDO_UNIDADE){
								$dto->setDthConclusao(null);
							}
	
							$dto->setNumMaxRegistrosRetorno(1);
							$dto->setOrdNumIdAtividade(InfraDTO::$TIPO_ORDENACAO_DESC);
	
							$dto = $this->consultarRN0033($dto);
							if ($dto!=null){
								$objAtividadeDTO->setNumIdUsuarioAtribuicao($dto->getNumIdUsuarioAtribuicao());
							}

						}else if ($numIdTarefa == TarefaRN::$TI_REABERTURA_PROCESSO_UNIDADE){
							$objAtividadeDTO->setNumIdUsuarioAtribuicao(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
						}
					}
				}
			}
	
			$strDataHoraAtual = InfraData::getStrDataHoraAtual();
	
			$objAtividadeDTO->setDthAbertura($strDataHoraAtual);
			$objAtividadeDTO->setNumIdUnidadeOrigem( $objAtividadeDTO->getNumIdUnidade() );
			$objAtividadeDTO->setNumIdUsuarioOrigem(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
			$objAtividadeDTO->setNumTipoVisualizacao(self::$TV_VISUALIZADO);
	
			$arrObjAtividadeDTO = array();
	
			if (!$bolFlagReaberturaAutomaticaProtocolo && $objTarefaDTO->getStrSinFecharAndamentosAbertos()=='S'){
				 
				$objPesquisaPendenciaDTO = new PesquisaPendenciaDTO();
				$objPesquisaPendenciaDTO->setDblIdProtocolo($objAtividadeDTO->getDblIdProtocolo());
				$objPesquisaPendenciaDTO->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
				$objPesquisaPendenciaDTO->setNumIdUnidade($objAtividadeDTO->getNumIdUnidade());
		   
				$arrObjProcedimentoDTO = $this->listarPendenciasRN0754($objPesquisaPendenciaDTO);
	
				if (count($arrObjProcedimentoDTO)==1){
					$arrObjAtividadeDTO = $arrObjProcedimentoDTO[0]->getArrObjAtividadeDTO();
				}
			}
	
			$bolFlagConcluiu = false;
			 
			//se tem andamentos em aberto
			if (count($arrObjAtividadeDTO)){
				 
				$n = 0;
				foreach($arrObjAtividadeDTO as $dto){
					if ($dto->getStrSinInicial()=='S'){
						$n++;
					}
				}
	
				//se todos os andamentos são iniciais
				if ($n == count($arrObjAtividadeDTO)){
					$objAtividadeDTO->setStrSinInicial('S');
				}
		   
				foreach($arrObjAtividadeDTO as $dto){
		    
					//copia usuário que visualizou e o respectivo status de visualização
					if ($dto->getNumIdUsuarioVisualizacao()!=null){
						$objAtividadeDTO->setNumIdUsuarioVisualizacao($dto->getNumIdUsuarioVisualizacao());
						$objAtividadeDTO->setNumTipoVisualizacao($dto->getNumTipoVisualizacao());
					}
		    
					//copia usuário de atribuição
					if ($dto->getNumIdUsuarioAtribuicao()!=null && //último andamento tem atribuição
					$objAtividadeDTO->getNumIdUsuarioAtribuicao()==null && //nao foi atribuido antes
					$numIdTarefa != TarefaRN::$TI_REMOCAO_ATRIBUICAO){  //removendo atribuicao manualmente
						$objAtividadeDTO->setNumIdUsuarioAtribuicao($dto->getNumIdUsuarioAtribuicao());
					}
				}
	
				$this->concluirRN0726($arrObjAtividadeDTO);
	
				$bolFlagConcluiu = true;
	
				//quando reabrindo não tinha andamentos abertos e pode não ter tramitado
				//a verificação evita que na reabertura de um processo gerado que não tramitou ele
				//fique na coluna de recebidos
			}else if ($numIdTarefa == TarefaRN::$TI_REABERTURA_PROCESSO_UNIDADE || $numIdTarefa == TarefaRN::$TI_REABERTURA_PROCESSO_USUARIO){
				 
				//verifica se o processo não tramitou fora da unidade
				$dto = new AtividadeDTO();

				$dto->setNumIdUnidade( $objAtividadeDTO->getNumIdUnidade() );
				$dto->setDblIdProtocolo($objAtividadeDTO->getDblIdProtocolo());
				$dto->setStrSinInicial('N');
	
				if ($this->contarRN0035($dto)==0){
					$objAtividadeDTO->setStrSinInicial('S');
				}
			}
	
			//Lança andamento inicial:
			//- quando reabrindo automaticamente devido ao protocolo
			//- quando o processo esta sendo remetido para outra unidade
			//- quando esta sendo dada credencial de acesso ao processo para alguem em outra unidade
			//- quando esta sendo transferida credencial de acesso de ao processo na mesma unidade
			//- quanto esta sendo dada credencial de assinatura para alguem em outra unidade
		  
			if ($bolFlagReaberturaAutomaticaProtocolo ||
			$numIdTarefa == TarefaRN::$TI_PROCESSO_REMETIDO_UNIDADE ||
			$numIdTarefa == TarefaRN::$TI_PROCESSO_CONCESSAO_CREDENCIAL ||
			$numIdTarefa == TarefaRN::$TI_PROCESSO_TRANSFERENCIA_CREDENCIAL ||
			$numIdTarefa == TarefaRN::$TI_CONCESSAO_CREDENCIAL_ASSINATURA){
	
				$objAtividadeDTO->setNumIdUsuarioVisualizacao(null);
				$objAtividadeDTO->setNumIdUsuarioConclusao(null);
				$objAtividadeDTO->setDthConclusao(null);
				$objAtividadeDTO->setStrSinInicial('N');
				$objAtividadeDTO->setNumTipoVisualizacao(self::$TV_NAO_VISUALIZADO);
	
			}else if ($objTarefaDTO->getStrSinLancarAndamentoFechado()=='S'
					||
					(!$bolFlagConcluiu && //não estava com o processo aberto na unidade
							 
							($objTarefaDTO->getStrSinPermiteProcessoFechado()=='S'
	
									||
									 
									//incluindo documento ou recebendo documento externo em processo por web-services
									(!SessaoSEIExterna::getInstance()->isBolHabilitada() &&
											$objProtocoloDTO->getStrStaEstado()==ProtocoloRN::$TE_NORMAL &&
											($numIdTarefa==TarefaRN::$TI_GERACAO_DOCUMENTO ||
													$numIdTarefa==TarefaRN::$TI_RECEBIMENTO_DOCUMENTO ||
													$numIdTarefa==TarefaRN::$TI_ARQUIVO_ANEXADO))
									 
									||
									 
									//unidade PROTOCOLO pode lançar andamentos em processos que não estão abertos com ela, exceto nos casos onde a unidade PROTOCOLO
									//esteja realmente gerando ou reabrindo um processo ou alguma unidade esteja remetendo o processo para o PROTOCOLO
									($objUnidadeDTO->getStrSinProtocolo()=='S' &&
											$numIdTarefa != TarefaRN::$TI_GERACAO_PROCEDIMENTO &&
											$numIdTarefa != TarefaRN::$TI_REABERTURA_PROCESSO_UNIDADE &&
											$numIdTarefa != TarefaRN::$TI_REABERTURA_PROCESSO_USUARIO &&
											$numIdTarefa != TarefaRN::$TI_PROCESSO_REMETIDO_UNIDADE &&
											$numIdTarefa != TarefaRN::$TI_PROCESSO_CONCESSAO_CREDENCIAL &&
											$numIdTarefa != TarefaRN::$TI_PROCESSO_TRANSFERENCIA_CREDENCIAL &&
											$numIdTarefa != TarefaRN::$TI_CONCESSAO_CREDENCIAL_ASSINATURA)
									||
									//cancelando documento em processo anexado
									($objProtocoloDTO->getStrStaEstado()==ProtocoloRN::$TE_PROCEDIMENTO_ANEXADO &&
											($numIdTarefa == TarefaRN::$TI_CANCELAMENTO_DOCUMENTO ||
													$numIdTarefa == TarefaRN::$TI_GERACAO_DOCUMENTO ||
													$numIdTarefa == TarefaRN::$TI_ARQUIVO_ANEXADO ||
													$numIdTarefa == TarefaRN::$TI_ENVIO_EMAIL))))){
	
				//lança andamento fechado
				$objAtividadeDTO->setNumIdUsuarioConclusao(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
				$objAtividadeDTO->setDthConclusao($strDataHoraAtual);
	
			}else {
				 
				if (!$bolFlagConcluiu &&
				$numIdTarefa != TarefaRN::$TI_GERACAO_PROCEDIMENTO &&
				$numIdTarefa != TarefaRN::$TI_REABERTURA_PROCESSO_UNIDADE &&
				$numIdTarefa != TarefaRN::$TI_REABERTURA_PROCESSO_USUARIO &&
				$numIdTarefa != TarefaRN::$TI_CANCELAMENTO_AGENDAMENTO &&
				$numIdTarefa != TarefaRN::$TI_REMOCAO_SOBRESTANDO_PROCESSO){ //confirmacao de publicacao
					//throw new InfraException('Processo '.$objProtocoloDTO->getStrProtocoloFormatado().' não possui andamento aberto na unidade '.SessaoSEI::getInstance()->getStrSiglaUnidadeAtual().' ['.$numIdTarefa.'].');
					$objInfraException->lancarValidacao('Processo '.$objProtocoloDTO->getStrProtocoloFormatado().' não possui andamento aberto na unidade teste [NOME UNIDADE].');
				}
				 
				//lança andamento em aberto mas não altera outros dados como usuário de visualização e atribuição
				$objAtividadeDTO->setNumIdUsuarioConclusao(null);
				$objAtividadeDTO->setDthConclusao(null);
			}
	
			$objAtividadeBD = new AtividadeBD($this->getObjInfraIBanco());
			$ret = $objAtividadeBD->cadastrar($objAtividadeDTO);
	
			//lança ícone de atenção para o processo em todas as unidades que possuam andamento aberto e já tenham visualizado
			if ($numIdTarefa == TarefaRN::$TI_ASSINATURA_DOCUMENTO || $numIdTarefa == TarefaRN::$TI_RECEBIMENTO_DOCUMENTO){
	
				$dto = new AtividadeDTO();
				$dto->setDblIdProtocolo($objAtividadeDTO->getDblIdProtocolo());
				 
				if ($strStaNivelAcessoGlobal==ProtocoloRN::$NA_SIGILOSO){
					$dto->setNumIdUsuario($objAtividadeDTO->getNumIdUsuario()); //em todos menos no atual
				}else{
					$dto->setNumIdUnidade($objAtividadeDTO->getNumIdUnidade()); //em todas menos na atual
				}
				 
				$dto->setNumTipoVisualizacao(self::$TV_ATENCAO);
				$this->atualizarVisualizacao($dto);
	
			}else if ($bolFlagReaberturaAutomaticaProtocolo){
	
				$dto = new AtividadeDTO();
				$dto->setDblIdProtocolo($objAtividadeDTO->getDblIdProtocolo());
				$dto->setNumIdUnidade($objAtividadeDTO->getNumIdUnidade());
				$dto->setNumTipoVisualizacao(self::$TV_ATENCAO);
				$this->atualizarVisualizacaoUnidade($dto);
	
			}else if ($numIdTarefa == TarefaRN::$TI_REMOCAO_SOBRESTAMENTO){
	
				//atualiza atividade de sobrestamento se existir em aberto
				$dto = new AtividadeDTO();
				$dto->retNumIdAtividade();
				$dto->retNumIdUnidade();
				$dto->retNumTipoVisualizacao();
				$dto->setNumIdTarefa(TarefaRN::$TI_SOBRESTAMENTO);
				$dto->setDthConclusao(null);
				$dto->setDblIdProtocolo($objAtividadeDTO->getDblIdProtocolo());
	
				$dto = $this->consultarRN0033($dto);
				 
				if ($dto != null){
					$dto->setNumTipoVisualizacao($dto->getNumTipoVisualizacao() | self::$TV_REMOCAO_SOBRESTAMENTO);
					$objAtividadeBD->alterar($dto);
				}
	
			}

			if ($objAtividadeDTO->isSetArrObjAtributoAndamentoDTO()){

				$objAtributoAndamentoRN = new AtributoAndamentoRN();
				$arrObjAtributoAndamentoDTO = $objAtividadeDTO->getArrObjAtributoAndamentoDTO();
				
				foreach($arrObjAtributoAndamentoDTO as $objAtributoAndamentoDTO){
					$objAtributoAndamentoDTO->setNumIdAtividade($ret->getNumIdAtividade());
					$objAtributoAndamentoRN->cadastrarRN1363($objAtributoAndamentoDTO);
				}
			}
			 
			return $ret;
	
			//Auditoria
	
		}catch(Exception $e){
			throw new InfraException('Erro cadastrando andamento do SEI.',$e);
		}
	}
	
	public function enviarRN0023Customizado(EnviarProcessoDTO $parObjEnviarProcessoDTO){
		 
		if ($this->enviarRN0023CustomizadoInterno($parObjEnviarProcessoDTO)){
			 
			$arrObjAtividadeDTO = $parObjEnviarProcessoDTO->getArrAtividades();
	
			$objIndexacaoDTO = new IndexacaoDTO();
			$objIndexacaoDTO->setArrObjProtocoloDTO(InfraArray::gerarArrInfraDTO('ProtocoloDTO','IdProtocolo',array_unique(InfraArray::converterArrInfraDTO($arrObjAtividadeDTO,'IdProtocolo'))));
			$objIndexacaoDTO->setStrStaOperacao(IndexacaoRN::$TO_ENVIO_PROCESSO);
			 
			$objIndexacaoRN = new IndexacaoRN();
			$objIndexacaoRN->indexarProtocolo($objIndexacaoDTO);
		}
	}
	
	protected function enviarRN0023CustomizadoInternoControlado(EnviarProcessoDTO $parObjEnviarProcessoDTO) {
		
		try{
	
			//Valida Permissao
			//SessaoSEI::getInstance()->validarAuditarPermissao('atividade_enviar',__METHOD__,$parObjEnviarProcessoDTO);
	
			//Regras de Negocio
			$objInfraException = new InfraException();
			
			//verifica se não houve mudança nas atividades abertas
			$idUnidadeOrigem = '';
			$arrObjAtividadeDTOOrigem = $parObjEnviarProcessoDTO->getArrAtividadesOrigem();
			
			if( count( $arrObjAtividadeDTOOrigem ) > 0 ){
				
				$idUnidadeOrigem = $arrObjAtividadeDTOOrigem[0]->getNumIdUnidade();
				
			}
			
			$arrIdAtividadesOrigem = InfraArray::converterArrInfraDTO($arrObjAtividadeDTOOrigem,'IdAtividade');
			$arrObjAtividadeDTO = $parObjEnviarProcessoDTO->getArrAtividades();
			$arrIdProtocolosOrigem = array_unique(InfraArray::converterArrInfraDTO($arrObjAtividadeDTO,'IdProtocolo'));
	
			$this->validarStrSinConluirOriginaisRN0826($parObjEnviarProcessoDTO, $objInfraException);
			$this->validarStrSinRemoverAnotacoes($parObjEnviarProcessoDTO, $objInfraException);
			$this->validarStrSinEnviarEmailNotificacao($parObjEnviarProcessoDTO, $objInfraException);
	
			if ($parObjEnviarProcessoDTO->isSetDtaPrazo()) {
				$this->validarDtaPrazo($parObjEnviarProcessoDTO, $objInfraException);
			}else{
				$parObjEnviarProcessoDTO->setDtaPrazo(null);
			}
	
			if ($parObjEnviarProcessoDTO->isSetNumDias()) {
				$this->validarNumDias($parObjEnviarProcessoDTO, $objInfraException);
			}else{
				$parObjEnviarProcessoDTO->setNumDias(null);
			}
	
			if ($parObjEnviarProcessoDTO->isSetStrSinDiasUteis()) {
				$this->validarStrSinDiasUteis($parObjEnviarProcessoDTO, $objInfraException);
			}else{
				$parObjEnviarProcessoDTO->setStrSinDiasUteis('N');
			}
	
			if (!InfraString::isBolVazia($parObjEnviarProcessoDTO->getDtaPrazo()) && !InfraString::isBolVazia($parObjEnviarProcessoDTO->getNumDias())){
				$objInfraException->adicionarValidacao('Não é possível informar simultaneamente uma data específica e um número de dias para o Retorno Programado.');
			}
	
			$objInfraException->lancarValidacoes();
	
	
			$objRetornoProgramadoRN = new RetornoProgramadoRN();
			
			if ($parObjEnviarProcessoDTO->getStrSinManterAberto()=='N'){

				foreach($arrIdProtocolosOrigem as $dblIdProtocoloOrigem){

					$objRetornoProgramadoDTO 	= new RetornoProgramadoDTO();
					$objRetornoProgramadoDTO->setDistinct(true);
					$objRetornoProgramadoDTO->retStrSiglaUnidadeOrigemAtividadeEnvio();
					$objRetornoProgramadoDTO->setNumIdUnidadeOrigemAtividadeEnvio(InfraArray::converterArrInfraDTO($arrObjAtividadeDTO,'IdUnidade'),InfraDTO::$OPER_NOT_IN);
					$objRetornoProgramadoDTO->retStrProtocoloFormatadoAtividadeEnvio();
					$objRetornoProgramadoDTO->setNumIdUnidadeAtividadeEnvio( $idUnidadeOrigem );
					$objRetornoProgramadoDTO->setDblIdProtocoloAtividadeEnvio($dblIdProtocoloOrigem);
					$objRetornoProgramadoDTO->setNumIdAtividadeRetorno(null);
						
					$arrObjRetornoProgramadoDTO = $objRetornoProgramadoRN->listar($objRetornoProgramadoDTO);
	
					if (count($arrObjRetornoProgramadoDTO)){
	
						$strMsgRetornoProgramado = 'Processo '.$arrObjRetornoProgramadoDTO[0]->getStrProtocoloFormatadoAtividadeEnvio().' possui retorno programado requisitado ';
	
						if (count($arrObjRetornoProgramadoDTO)==1){
							$strMsgRetornoProgramado .= 'pela unidade '.$arrObjRetornoProgramadoDTO[0]->getStrSiglaUnidadeOrigemAtividadeEnvio();
						}else{
							$strMsgRetornoProgramado .= 'pelas unidades: '.implode(', ',InfraArray::converterArrInfraDTO($arrObjRetornoProgramadoDTO,'SiglaUnidadeOrigemAtividadeEnvio'));
						}
						$strMsgRetornoProgramado .= '.';
	
						$objInfraException->adicionarValidacao($strMsgRetornoProgramado);
					}
				}
			}
			$objInfraException->lancarValidacoes();
	
			//recupera dados dos processos
			$objProtocoloDTO = new ProtocoloDTO();
			$objProtocoloDTO->retDblIdProtocolo();
			$objProtocoloDTO->retStrStaNivelAcessoGlobal();
			$objProtocoloDTO->retStrProtocoloFormatado();
			$objProtocoloDTO->retStrNomeTipoProcedimentoProcedimento();
			$objProtocoloDTO->setDblIdProtocolo($arrIdProtocolosOrigem,InfraDTO::$OPER_IN);
	
			$objProtocoloRN = new ProtocoloRN();
			$arrObjProtocoloDTO = InfraArray::indexarArrInfraDTO($objProtocoloRN->listarRN0668($objProtocoloDTO),'IdProtocolo');
	
			$arrUnidadesAtividades = array_unique(array_merge(InfraArray::converterArrInfraDTO($arrObjAtividadeDTO,'IdUnidade'),InfraArray::converterArrInfraDTO($arrObjAtividadeDTO,'IdUnidadeOrigem')));
	
			//dados de unidades
			$objUnidadeDTO = new UnidadeDTO();
			$objUnidadeDTO->retNumIdUnidade();
			$objUnidadeDTO->retNumIdOrgao();
			$objUnidadeDTO->retStrSigla();
			$objUnidadeDTO->retStrDescricao();
			$objUnidadeDTO->retStrSiglaOrgao();
			$objUnidadeDTO->retStrDescricaoOrgao();
			$objUnidadeDTO->retStrSinMailPendencia();
			$objUnidadeDTO->setNumIdUnidade($arrUnidadesAtividades,InfraDTO::$OPER_IN);
				
			$objUnidadeRN = new UnidadeRN();
			$arrObjUnidadeDTO = InfraArray::indexarArrInfraDTO($objUnidadeRN->listarRN0127($objUnidadeDTO),'IdUnidade');
			 
			$arrUnidadesConsultadas = InfraArray::converterArrInfraDTO($arrObjUnidadeDTO,'IdUnidade');
				
			foreach($arrUnidadesAtividades as $numIdUnidadeAtividade){
				
				if (!in_array($numIdUnidadeAtividade,$arrUnidadesConsultadas)){
					throw new InfraException('Unidade ['.$numIdUnidadeAtividade.'] não encontrada.');
				}
			}
	
			////////////////////////////////////////////////////////////////
	
			$strPrazo = null;
	
			if (!InfraString::isBolVazia($parObjEnviarProcessoDTO->getDtaPrazo())){
	
				foreach($arrObjAtividadeDTO as $objAtividadeDTO) {
					$objAtividadeDTO->setDtaPrazo($parObjEnviarProcessoDTO->getDtaPrazo());
				}
	
			}else if (!InfraString::isBolVazia($parObjEnviarProcessoDTO->getNumDias())){
	
				if ($parObjEnviarProcessoDTO->getStrSinDiasUteis() == 'N'){
	
					$strPrazo = InfraData::calcularData($parObjEnviarProcessoDTO->getNumDias(),InfraData::$UNIDADE_DIAS, InfraData::$SENTIDO_ADIANTE);
	
					foreach($arrObjAtividadeDTO as $objAtividadeDTO) {
						$objAtividadeDTO->setDtaPrazo($strPrazo);
					}
	
				}else{
	
					$arrIdUnidades = InfraArray::converterArrInfraDTO($arrObjAtividadeDTO,'IdUnidade');
					$arrIdOrgaoEnvio = array();
	
					//filtra orgaos das unidades de destino
					foreach($arrIdUnidades as $numIdUnidadeEnvio){
						
						if (!in_array($arrObjUnidadeDTO[$numIdUnidadeEnvio]->getNumIdOrgao(), $arrIdOrgaoEnvio)){
							$arrIdOrgaoEnvio[] = $arrObjUnidadeDTO[$numIdUnidadeEnvio]->getNumIdOrgao();
						}
					}
	
					$strDataInicial = InfraData::getStrDataAtual();
	
					//busca feriados ate 1 ano a frente do periodo corrido solicitado
					$strDataFinal = InfraData::calcularData(($parObjEnviarProcessoDTO->getNumDias() + 365), InfraData::$UNIDADE_DIAS, InfraData::$SENTIDO_ADIANTE, $strDataInicial);
	
					$objPublicacaoRN = new PublicacaoRN();
					$arrDataPrazo = array();
	
					//pega todos os feriados cadastrados por órgão
					foreach($arrIdOrgaoEnvio as $numIdOrgaoEnvio) {
	
						$objFeriadoDTO = new FeriadoDTO();
						$objFeriadoDTO->setNumIdOrgao($numIdOrgaoEnvio);
						$objFeriadoDTO->setDtaInicial($strDataInicial);
						$objFeriadoDTO->setDtaFinal($strDataFinal);
	
	
						$arrFeriados = InfraArray::simplificarArr($objPublicacaoRN->listarFeriados($objFeriadoDTO), 'Data');
	
						$numDias = $parObjEnviarProcessoDTO->getNumDias();
						$strPrazo = $strDataInicial;
	
						while($numDias){
	
							do{
								$strPrazo = InfraData::calcularData(1, InfraData::$UNIDADE_DIAS, InfraData::$SENTIDO_ADIANTE, $strPrazo);
							}while (InfraData::obterDescricaoDiaSemana($strPrazo) == 'sábado' ||	InfraData::obterDescricaoDiaSemana($strPrazo) == 'domingo' ||	in_array($strPrazo, $arrFeriados));
	
							$numDias--;
						}
	
						$arrDataPrazo[$numIdOrgaoEnvio] = $strPrazo;
					}
	
	
					foreach($arrObjAtividadeDTO as $objAtividadeDTO) {
						$objAtividadeDTO->setDtaPrazo($arrDataPrazo[$arrObjUnidadeDTO[$objAtividadeDTO->getNumIdUnidade()]->getNumIdOrgao()]);
					}
				}
			}else{
	
				foreach($arrObjAtividadeDTO as $objAtividadeDTO) {
					$objAtividadeDTO->setDtaPrazo(null);
				}
	
			}
	
			$bolFlagEnviouParaOutraUnidade = false;
	
			$arrEmailUnidades = array();
	
			$objInfraParametro = new InfraParametro(BancoSEI::getInstance());
			$strEmailSistema = $objInfraParametro->getValor('SEI_EMAIL_SISTEMA');
	
			$objEmailSistemaDTO = new EmailSistemaDTO();
			$objEmailSistemaDTO->retStrDe();
			$objEmailSistemaDTO->retStrPara();
			$objEmailSistemaDTO->retStrAssunto();
			$objEmailSistemaDTO->retStrConteudo();
			$objEmailSistemaDTO->setNumIdEmailSistema(EmailSistemaRN::$ES_ENVIO_PROCESSO_PARA_UNIDADE);
				
			$objEmailSistemaRN = new EmailSistemaRN();
			$objEmailSistemaDTO = $objEmailSistemaRN->consultar($objEmailSistemaDTO);
				
			$objAnotacaoRN = new AnotacaoRN();
			$objDocumentoRN = new DocumentoRN();
				
			foreach($arrObjAtividadeDTO as $objAtividadeDTO){
	
				$objProtocoloDTO = $arrObjProtocoloDTO[$objAtividadeDTO->getDblIdProtocolo()];
				$objUnidadeDTO = $arrObjUnidadeDTO[$objAtividadeDTO->getNumIdUnidade()];
				$objUnidadeDTOOrigem = $arrObjUnidadeDTO[$objAtividadeDTO->getNumIdUnidadeOrigem()];
				 
				$this->validarDblIdProtocoloRN0704($objAtividadeDTO, $objInfraException);
				$this->validarNumIdUnidadeRN0705($objAtividadeDTO, $objInfraException);
				$this->validarNumIdUnidadeOrigemRN0707($objAtividadeDTO, $objInfraException);
				$this->validarNumIdUsuario($objAtividadeDTO, $objInfraException);
				$this->validarNumIdUsuarioOrigemRN0708($objAtividadeDTO, $objInfraException);
				$this->validarDtaPrazoRN0714($objAtividadeDTO, $objInfraException);
	
				$objInfraException->lancarValidacoes();
		
				// Filtra campos do DTO
				$dto = new AtividadeDTO();
				$dto->setDblIdProtocolo($objAtividadeDTO->getDblIdProtocolo());
				$dto->setNumIdUnidade($objAtividadeDTO->getNumIdUnidade());
				$dto->setNumIdUnidadeOrigem($objAtividadeDTO->getNumIdUnidadeOrigem());
				$dto->setNumIdUsuario($objAtividadeDTO->getNumIdUsuario());
				$dto->setNumIdUsuarioOrigem($objAtividadeDTO->getNumIdUsuarioOrigem());
				$dto->setDtaPrazo($objAtividadeDTO->getDtaPrazo());
				$objAtividadeDTO = $dto;
		
				if ($objProtocoloDTO->getStrStaNivelAcessoGlobal()==ProtocoloRN::$NA_SIGILOSO){
					$objInfraException->lancarValidacao('Não é possível enviar um processo sigiloso ('.$objProtocoloDTO->getStrProtocoloFormatado().') para outra unidade.');
				}
	
				$arrObjAtributoAndamentoDTO = array();
				 
				$objAtributoAndamentoDTO = new AtributoAndamentoDTO();
				$objAtributoAndamentoDTO->setStrNome('UNIDADE');
				$objAtributoAndamentoDTO->setStrValor($objUnidadeDTOOrigem->getStrSigla().'¥'.$objUnidadeDTOOrigem->getStrDescricao());
				$objAtributoAndamentoDTO->setStrIdOrigem($objAtividadeDTO->getNumIdUnidadeOrigem());
				$arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;
				 
				$objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO);
				$objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_PROCESSO_REMETIDO_UNIDADE);
				 	
				$ret = $this->gerarInternaRN0727($objAtividadeDTO);
				 
				//enviando para outra unidade
				if ($objAtividadeDTO->getNumIdUnidade()!= $idUnidadeOrigem ){
					 					 
					$bolFlagEnviouParaOutraUnidade = true;
					 
					//se informou uma data de retorno programado
					if ($objAtividadeDTO->getDtaPrazo() != null){
	
						//verifica se já não existe um retorno programado para a unidade
						$objRetornoProgramadoDTO 	= new RetornoProgramadoDTO();
						$objRetornoProgramadoDTO->retNumIdRetornoProgramado();
						$objRetornoProgramadoDTO->setNumIdUnidadeOrigemAtividadeEnvio($objAtividadeDTO->getNumIdUnidadeOrigem());
						$objRetornoProgramadoDTO->setNumIdUnidadeAtividadeEnvio($objAtividadeDTO->getNumIdUnidade());
						$objRetornoProgramadoDTO->setDblIdProtocoloAtividadeEnvio($objAtividadeDTO->getDblIdProtocolo());
						$objRetornoProgramadoDTO->setNumIdAtividadeRetorno(null);
						$objRetornoProgramadoDTO = $objRetornoProgramadoRN->consultar($objRetornoProgramadoDTO);
	
						if ($objRetornoProgramadoDTO!=null){
							$objInfraException->lancarValidacao('Já existe um Retorno Programado em aberto para a unidade '.$objUnidadeDTO->getStrSigla().'/'.$objUnidadeDTO->getStrSiglaOrgao().' no processo '.$objProtocoloDTO->getStrProtocoloFormatado().'.');
						}
	
						// cadastrar como Retorno Programado
						$objRetornoProgramadoDTO = new RetornoProgramadoDTO();
						$objRetornoProgramadoDTO->setNumIdRetornoProgramado(null);
						$objRetornoProgramadoDTO->setNumIdUnidade( $idUnidadeOrigem );
						$objRetornoProgramadoDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
						$objRetornoProgramadoDTO->setNumIdAtividadeEnvio($ret->getNumIdAtividade());
						$objRetornoProgramadoDTO->setNumIdAtividadeRetorno(null);
						$objRetornoProgramadoDTO->setDtaProgramada($objAtividadeDTO->getDtaPrazo());
						$objRetornoProgramadoDTO->setDthAlteracao(null);
						$objRetornoProgramadoRN->cadastrar($objRetornoProgramadoDTO);
					}
					 
					//verifica se esta respondendo um retorno programado existente para esta unidade e protocolo
					$objRetornoProgramadoDTO 	= new RetornoProgramadoDTO();
					$objRetornoProgramadoDTO->retNumIdRetornoProgramado();
					$objRetornoProgramadoDTO->setNumIdUnidadeAtividadeEnvio($objAtividadeDTO->getNumIdUnidadeOrigem());
					$objRetornoProgramadoDTO->setNumIdUnidadeOrigemAtividadeEnvio($objAtividadeDTO->getNumIdUnidade());
					$objRetornoProgramadoDTO->setDblIdProtocoloAtividadeEnvio($objAtividadeDTO->getDblIdProtocolo());
					$objRetornoProgramadoDTO->setNumIdAtividadeRetorno(null);
					$objRetornoProgramadoDTO = $objRetornoProgramadoRN->consultar($objRetornoProgramadoDTO);
						
					if ($objRetornoProgramadoDTO!=null){
						$objRetornoProgramadoDTO->setNumIdAtividadeRetorno($ret->getNumIdAtividade());
						$objRetornoProgramadoRN->alterar($objRetornoProgramadoDTO);
					}
	
					//Associar o processo e seus documentos com esta unidade
					$objAssociarDTO = new AssociarDTO();
					$objAssociarDTO->setDblIdProcedimento($objAtividadeDTO->getDblIdProtocolo());
					$objAssociarDTO->setDblIdDocumento(null);
					$objAssociarDTO->setNumIdUnidade($objAtividadeDTO->getNumIdUnidade());
					$objAssociarDTO->setNumIdUsuario(null);
					$objAssociarDTO->setStrStaNivelAcessoGlobal($objProtocoloDTO->getStrStaNivelAcessoGlobal());
					$objProtocoloRN->associarRN0982($objAssociarDTO);
	
						
					if ($parObjEnviarProcessoDTO->getStrSinManterAberto()=='N'){
	
						$dto = new AtividadeDTO();
						$dto->setDblIdProtocolo($objAtividadeDTO->getDblIdProtocolo());
						$dto->setNumIdUnidade( $idUnidadeOrigem );
						$dto->setNumIdTarefa(TarefaRN::$TI_CONCLUSAO_AUTOMATICA_UNIDADE);
	
						$this->gerarInternaRN0727($dto);
					}
	
					if ($parObjEnviarProcessoDTO->getStrSinRemoverAnotacoes()=='S'){
	
						$objAnotacaoDTO = new AnotacaoDTO();
						 
						$objAnotacaoDTO->retNumIdAnotacao();
						 
						$objAnotacaoDTO->setDblIdProtocolo($objAtividadeDTO->getDblIdProtocolo());
						$objAnotacaoDTO->setNumIdUnidade( $idUnidadeOrigem );
						$objAnotacaoDTO->setStrStaAnotacao(AnotacaoRN::$TA_UNIDADE);
						 
						$objAnotacaoRN->excluir($objAnotacaoRN->listar($objAnotacaoDTO));
					}
						
					//bloqueia assinaturas dos documentos gerados e assinados na unidade
					$objProcedimentoDTO = new ProcedimentoDTO();
					$objProcedimentoDTO->setDblIdProcedimento($objAtividadeDTO->getDblIdProtocolo());
					$objDocumentoRN->bloquearTramitacaoConclusao($objProcedimentoDTO);
	
					//mail
					if ($objEmailSistemaDTO!=null && ($parObjEnviarProcessoDTO->getStrSinEnviarEmailNotificacao()=='S' || $objUnidadeDTO->getStrSinMailPendencia()=='S')){
	
						$objEmailUnidadeDTO = new EmailUnidadeDTO();
						$objEmailUnidadeDTO->retStrEmail();
						$objEmailUnidadeDTO->retStrDescricao();
						$objEmailUnidadeDTO->setNumIdUnidade($objAtividadeDTO->getNumIdUnidade());
							
						$objEmailUnidadeRN = new EmailUnidadeRN();
						$arrObjEmailUnidadeDTO = $objEmailUnidadeRN->listar($objEmailUnidadeDTO);
							
						if (count($arrObjEmailUnidadeDTO)==0){
							$objInfraException->lancarValidacao('Unidade '.$objUnidadeDTO->getStrSigla().'/'.$objUnidadeDTO->getStrSiglaOrgao().' não possui email cadastrado.');
						}
							
						$strDe = $objEmailSistemaDTO->getStrDe();
						$strDe = str_replace('@email_sistema@',$strEmailSistema,$strDe);
						$strDe = str_replace('@sigla_sistema@',SessaoSEIExterna::getInstance()->getStrSiglaSistema(),$strDe);
							
						$strEmailsUnidade = '';
						foreach($arrObjEmailUnidadeDTO as $objEmailUnidadeDTO){
							$strEmailsUnidade .= $objEmailUnidadeDTO->getStrDescricao().' <'.$objEmailUnidadeDTO->getStrEmail().'> ;';
						}
						$strEmailsUnidade = substr($strEmailsUnidade,0,-1);
							
						$strPara = $objEmailSistemaDTO->getStrPara();
						$strPara = str_replace('@emails_unidade@',$strEmailsUnidade,$strPara);
							
						$strAssunto = $objEmailSistemaDTO->getStrAssunto();
						$strAssunto = str_replace('@processo@',$objProtocoloDTO->getStrProtocoloFormatado(),$strAssunto);
							
						$strConteudo = $objEmailSistemaDTO->getStrConteudo();
						$strConteudo = str_replace('@processo@',$objProtocoloDTO->getStrProtocoloFormatado(),$strConteudo);
						$strConteudo = str_replace('@tipo_processo@',$objProtocoloDTO->getStrNomeTipoProcedimentoProcedimento(),$strConteudo);
						$strConteudo = str_replace('@sigla_unidade_remetente@',$objUnidadeDTOOrigem->getStrSigla(),$strConteudo);
						$strConteudo = str_replace('@descricao_unidade_remetente@',$objUnidadeDTOOrigem->getStrDescricao(),$strConteudo);
						$strConteudo = str_replace('@sigla_orgao_unidade_remetente@',$objUnidadeDTOOrigem->getStrSiglaOrgao(),$strConteudo);
						$strConteudo = str_replace('@descricao_orgao_unidade_remetente@',$objUnidadeDTOOrigem->getStrDescricaoOrgao(),$strConteudo);
						$strConteudo = str_replace('@sigla_unidade_destinataria@',$objUnidadeDTO->getStrSigla(),$strConteudo);
						$strConteudo = str_replace('@descricao_unidade_destinataria@',$objUnidadeDTO->getStrDescricao(),$strConteudo);
						$strConteudo = str_replace('@sigla_orgao_unidade_destinataria@',$objUnidadeDTO->getStrSiglaOrgao(),$strConteudo);
						$strConteudo = str_replace('@descricao_orgao_unidade_destinataria@',$objUnidadeDTO->getStrDescricaoOrgao(),$strConteudo);
							
						$arrEmail = array();
						$arrEmail['UNIDADE'] = $objUnidadeDTO->getStrSigla().'/'.$objUnidadeDTO->getStrSiglaOrgao();
						$arrEmail['DE'] = $strDe;
						$arrEmail['PARA'] = $strPara;
						$arrEmail['ASSUNTO'] = $strAssunto;
						$arrEmail['MENSAGEM'] = $strConteudo;
							
						$arrEmailUnidades[] = $arrEmail;
					}
				}
			}
	
			foreach($arrEmailUnidades as $arrEmail){
				InfraMail::enviarConfigurado(ConfiguracaoSEI::getInstance(), $arrEmail['DE'], $arrEmail['PARA'], null, null, $arrEmail['ASSUNTO'], $arrEmail['MENSAGEM']);
			}
			 
			return $bolFlagEnviouParaOutraUnidade;
			 
			 
		}catch(Exception $e){
			throw new InfraException('Erro gerando andamento.',$e);
		}
	}
	
}
?>