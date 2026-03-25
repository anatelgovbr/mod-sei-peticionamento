<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 29/03/2017 - criado por marcelo.cast
 *
 * Versão do Gerador de Código: 1.40.0
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetAgendamentoAutomaticoRN extends InfraRN
{
	const URL_ANATEL = 'https://sei.anatel.gov.br/sei/controlador_ws.php';

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }
	
	/* Método que realiza cumprimento automático de intimações por decurso de prazo */
	protected function CumprirPorDecursoPrazoTacitoConectado()
	{
		
		ini_set('max_execution_time', '0');
		ini_set('memory_limit', '1024M');
		
		InfraDebug::getInstance()->setBolLigado(true);
		InfraDebug::getInstance()->setBolDebugInfra(false);
		InfraDebug::getInstance()->setBolEcho(false);
		InfraDebug::getInstance()->limpar();
		
		$idUsuarioPet = (new MdPetIntUsuarioRN())->getObjUsuarioPeticionamento(true);
		SessaoSEI::getInstance(false)->simularLogin(null, SessaoSEI::$UNIDADE_TESTE, $idUsuarioPet, null);
		
		$numSeg = InfraUtil::verificarTempoProcessamento();
		InfraDebug::getInstance()->gravar('CUMPRINDO INTIMACOES POR DECURSO DE PRAZO EM ' . InfraData::getStrDataAtual());

		// TODO: Remover em versao futura. Contigencia para evitar que intimação fique no limbo.
		(new MdPetIntimacaoRN())->preencherDadaPrazoTacito();

		// Contigencia caso tenha havido o cadastro de um Feriado na data do cumprimento.
		(new MdPetIntimacaoRN())->recalculaCumprimentoIntimacaoPorFeriado();
		
		// Busca pela pelas intimações a cumprir na data de hoje.
		$intimacoesPendentes = (new MdPetIntimacaoRN())->retornarDadosIntimacaoPrazoExpirado();
		
		InfraDebug::getInstance()->gravar('Qtd. Intimacoes Pendentes: ' . count($intimacoesPendentes));
		
		if (count($intimacoesPendentes) > 0) {
			
			$processamento = [];

			// Step 1 - Cumprir Intimação para cada Destinatário:
			foreach($intimacoesPendentes as $intimacao){

				$arrIntimacoes = (new MdPetIntAceiteRN())->realizarEtapasAceiteAgendado([$intimacao]);

				if(is_array($arrIntimacoes)){

					$processamento['cumpridas'] 	+= $arrIntimacoes['cumpridas'];
					$processamento['naoCumpridas'] 	+= $arrIntimacoes['naoCumpridas'];

					if(!empty($arrIntimacoes['erros']) && count($arrIntimacoes['erros']) > 0){
						$processamento['erros'][] = $arrIntimacoes['erros'];
					}

				}

			}

			// Step 2 - Logar o resultado:
			InfraDebug::getInstance()->gravar('Qtd. Intimacoes Cumpridas: ' . $processamento['cumpridas']);
			InfraDebug::getInstance()->gravar('Qtd. Intimacoes Nao Cumpridas: ' . $processamento['naoCumpridas']);

			if(isset($processamento['erros']) && count($processamento['erros']) > 0) {
					
				$mensagem = ':: Este é um e-mail automático ::\n\n';
				$mensagem .= 'Qtd. Intimacoes Cumpridas: ' . $processamento['cumpridas'].'\n';
				$mensagem .= 'Qtd. Intimacoes Nao Cumpridas: ' . $processamento['naoCumpridas'].'\n';
				$mensagem .= 'Processos em que o cumprimento falhou: \n';

				foreach ($processamento['erros'] as $erro) {
					InfraDebug::getInstance()->gravar($erro[0] . ' - Motivo: ' . $erro[1]);
					$mensagem .= $erro[0] . ' - Motivo: ' . $erro[1].'\n';
				}

				// Step 3 - Encaminha e-mail em caso de erro:
				$infraAgendamentoDTO = new InfraAgendamentoTarefaDTO();
				$infraAgendamentoDTO->setStrComando('MdPetAgendamentoAutomaticoRN::CumprirPorDecursoPrazoTacito');
				$infraAgendamentoDTO->retStrEmailErro();
				$infraAgendamentoDTO->setNumMaxRegistrosRetorno(1);
				$agendamento = (new InfraAgendamentoTarefaRN())->consultar($infraAgendamentoDTO);

				if(!empty($agendamento)){

					$emails = array_map('trim', explode(';', $agendamento->getStrEmailErro()));

					foreach ($emails as $email) {
						
						$objEmailDTO = new EmailDTO();
						$objEmailDTO->setStrDe((new InfraParametro(BancoSEI::getInstance()))->getValor('SEI_EMAIL_SISTEMA'));
						$objEmailDTO->setStrPara($email);
						$objEmailDTO->setStrAssunto('Falha no Agendamento de Cumprimento Tácito de Intimação Eletrônica');
						$objEmailDTO->setStrMensagem($mensagem);
						EmailRN::processar(array($objEmailDTO));

					}

				}
				
			}

		}

		$numSeg = InfraUtil::verificarTempoProcessamento($numSeg);
		InfraDebug::getInstance()->gravar('TEMPO TOTAL DE EXECUCAO: ' . $numSeg . ' s');
		InfraDebug::getInstance()->gravar('FIM');
		LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug(), InfraLog::$INFORMACAO);
		
	}
	
	/* Método que realiza cumprimento automático de intimações por decurso de prazo */
    protected function CumprirPorDecursoPrazoTacitoConectado_bkp()
    {
    	
    	try {

            ini_set('max_execution_time', '0');
            ini_set('memory_limit', '1024M');

            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            InfraDebug::getInstance()->limpar();

            $objUsuarioPetRN = new MdPetIntUsuarioRN();
            $idUsuarioPet = $objUsuarioPetRN->getObjUsuarioPeticionamento(true);
            SessaoSEI::getInstance(false)->simularLogin(null, SessaoSEI::$UNIDADE_TESTE, $idUsuarioPet, null);

            $numSeg = InfraUtil::verificarTempoProcessamento();
            InfraDebug::getInstance()->gravar('CUMPRINDO INTIMACOES POR DECURSO DE PRAZO');

            $intimacoesPendentes = (new MdPetIntimacaoRN())->retornarDadosIntimacaoPrazoExpirado();

            InfraDebug::getInstance()->gravar('Qtd. Intimacoes Pendentes: ' . count($intimacoesPendentes));

            if (count($intimacoesPendentes) > 0) {
	
	            $registros = count($intimacoesPendentes);
	            
	            $arrRetornoIntimacoes = array(
		            'cumpridas' => 0,
		            'naoCumpridas' => 0,
		            'procedimentos' => array()
	            );
	
	            if ($registros > 0) {
		            
	            	for ($i = 0; $i < $registros; $i++) {
			
			            $arrIntimacoes = (new MdPetIntAceiteRN())->realizarEtapasAceiteAgendadoIndividual($intimacoesPendentes[$i]);
			            
			            if($arrIntimacoes){
				            $arrRetornoIntimacoes['cumpridas'] = $arrRetornoIntimacoes['cumpridas'] + 1;
			            }else{
				            $arrRetornoIntimacoes['naoCumpridas'] = $arrRetornoIntimacoes['naoCumpridas'] + 1;
				            $arrRetornoIntimacoes['procedimentos'] = $intimacoesPendentes[$i]->getNumIdMdPetIntimacao();
			            }
	            	
	            	}
	            	
	            }

                InfraDebug::getInstance()->gravar('Qtd. Intimacoes Cumpridas: ' . $arrIntimacoes['cumpridas']);
                InfraDebug::getInstance()->gravar('Qtd. Intimacoes Nao Cumpridas: ' . $arrIntimacoes['naoCumpridas']);

                if(isset($arrIntimacoes['procedimentos'])) {
                    foreach ($arrIntimacoes['procedimentos'] as $procedimento) {
                        InfraDebug::getInstance()->gravar('Intimação nº ' .$procedimento);
                    }
                }
                if($arrIntimacoes['erros']){
                    foreach ($arrIntimacoes['erros'] as $procedimentos) {
                        InfraDebug::getInstance()->gravar($procedimentos[0] . ' - Motivo: ' . $procedimentos[1]);
                    }
                }
            }
            
            $numSeg = InfraUtil::verificarTempoProcessamento($numSeg);
            
            InfraDebug::getInstance()->gravar('TEMPO TOTAL DE EXECUCAO: ' . $numSeg . ' s');
            InfraDebug::getInstance()->gravar('FIM');
            LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug(), InfraLog::$INFORMACAO);
            
        } catch (Exception $e) {
        	
            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(true);
            InfraDebug::getInstance()->setBolEcho(true);
            InfraDebug::getInstance()->limpar();

            SessaoSEI::getInstance(false);

            LogSEI::getInstance()->gravar('Erro cumprindo intimacao por decurso de prazo.' . $e, InfraLog::$INFORMACAO);
            throw new InfraException('Erro cumprindo intimacao por decurso de prazo.', $e);
            
        }

    }

    protected function AtualizarSituacaoProcuracaoSimplesVencidaControlado( ){

        try {
            
            ini_set('max_execution_time','0');
            ini_set('memory_limit','1024M');

            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            InfraDebug::getInstance()->limpar();

            SessaoSEI::getInstance(false);
           
            $numSeg = InfraUtil::verificarTempoProcessamento();
            InfraDebug::getInstance()->gravar('ALTERANDO SITUACAO DE PROCURACOES VENCIDAS');

            //Alterando o Status das Procurações Eletrônicas
            $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
            $objMdPetVincRepresentantDTO->retDthDataLimite();
            $objMdPetVincRepresentantDTO->retStrStaEstado();
            $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
            $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
            $objMdPetVincRepresentantRN = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);
            $contador = 0;
            foreach ($objMdPetVincRepresentantRN as $dto) {

                if($dto->getStrStaEstado() == MdPetVincRepresentantRN::$RP_ATIVO){
                    if(infraData::compararDatas(infraData::getStrDataAtual(),$dto->getDthDataLimite()) < 0){
                        
                        $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
                        $objMdPetVincRepresentantDTO->setNumIdMdPetVinculoRepresent($dto->getNumIdMdPetVinculoRepresent());
                        $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_VENCIDA);
                        $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
                        $objMdPetVincRepresentantRN = $objMdPetVincRepresentantRN->alterar($objMdPetVincRepresentantDTO);
                        $contador += 1;

                }
            }
        }
           
            InfraDebug::getInstance()->gravar('Qtd. Procuracoes Vencidas: '.$contador.' ');

            $numSeg = InfraUtil::verificarTempoProcessamento($numSeg);
            InfraDebug::getInstance()->gravar('TEMPO TOTAL DE EXECUCAO: '.$numSeg.' s');
            InfraDebug::getInstance()->gravar('FIM');
            LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug(),InfraLog::$INFORMACAO);
        } catch (Exception $e) {
            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(true);
            InfraDebug::getInstance()->setBolEcho(true);
            InfraDebug::getInstance()->limpar();

            SessaoSEI::getInstance(false);
            
            LogSEI::getInstance()->gravar('Erro alterando situacao da procuracao eletronica.' . $e, InfraLog::$INFORMACAO);
            throw new InfraException('Erro alterando situacao da procuracao eletronica.',$e);
        }

    }

    protected function configDebugSession(){
        InfraDebug::getInstance()->setBolLigado(true);
        InfraDebug::getInstance()->setBolDebugInfra(false);
        InfraDebug::getInstance()->setBolEcho(false);
        InfraDebug::getInstance()->limpar();
        SessaoSEI::getInstance(false);
    }

    protected function gravaLogs($startScript){
        $numSeg = InfraUtil::verificarTempoProcessamento($startScript);
        InfraDebug::getInstance()->gravar('TEMPO TOTAL DE EXECUCAO: ' . $numSeg . ' s');
        InfraDebug::getInstance()->gravar('FIM');
        LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug(), InfraLog::$INFORMACAO);
    }


    /* Metodo que reintera via email acerca de Intimacao Eletronica:
     *    - Com Tipo de Resposta que Exige Resposta pelo Usuario Externo 
     *    e Pendente de Resposta apos a Intimacao ter sido Cumprida.
     */
    protected function ReiterarIntimacaoExigeRespostaControlado()
    {

        try {

            ini_set('max_execution_time', '0');
            ini_set('memory_limit', '1024M');

            SessaoSEI::getInstance(false);

            $startScript = InfraUtil::verificarTempoProcessamento();

            $objMdPetIntimacaoRN = new MdPetIntimacaoRN();

            $intimacoesExigeRespostaDTO = $objMdPetIntimacaoRN->getIntimacoesPossuemData();
            $intimacoesExigeRespostaJuridicoDTO = $objMdPetIntimacaoRN->getIntimacoesPossuemData($PessoaJuridica = true);

            if(count($intimacoesExigeRespostaDTO) == 0 || count($intimacoesExigeRespostaJuridicoDTO) == 0){
                $this->configDebugSession();
                InfraDebug::getInstance()->gravar('NEHUMA INTIMAÇÃO A REITERAR.');
                $this->gravaLogs($startScript);
            }

            if (count($intimacoesExigeRespostaDTO) > 0) {
                $objMdPetIntEmailNotificacaoRN = new MdPetIntEmailNotificacaoRN();

                $this->configDebugSession();

                InfraDebug::getInstance()->gravar('REITERANDO INTIMACOES QUE EXIGEM RESPOSTA PF');

                $qtdEnviadas = $objMdPetIntEmailNotificacaoRN->enviarEmailReiteracaoIntimacao(array($intimacoesExigeRespostaDTO, $pessoa = "F"));
                if (is_numeric($qtdEnviadas['qtdEnviadas'])) {
                    InfraDebug::getInstance()->gravar('Qtd. Reiterações: ' . $qtdEnviadas['qtdEnviadas']);
                }
                if (is_array($qtdEnviadas['arrDadosEmailNaoEnviados']) && count($qtdEnviadas['arrDadosEmailNaoEnviados']) > 0) {
                    foreach($qtdEnviadas['arrDadosEmailNaoEnviados'] as $email) {
                        InfraDebug::getInstance()->gravar('Nº Processo: ' . $email['processo'] . ' - Usuário Externo: ' . $email['nome_usuario_externo'] . ' - E-mail Usuário Externo: ' . $email['email_usuario_externo']);
                    }
                }

                $this->gravaLogs($startScript);
            }

            //Juridico

            if (count($intimacoesExigeRespostaJuridicoDTO) > 0) {
                $objMdPetIntEmailNotificacaoRN = new MdPetIntEmailNotificacaoRN();

                $this->configDebugSession();

                InfraDebug::getInstance()->gravar('REITERANDO INTIMACOES QUE EXIGEM RESPOSTA PJ');

                $qtdEnviadasJuridico = $objMdPetIntEmailNotificacaoRN->enviarEmailReiteracaoIntimacaoJuridico(array($intimacoesExigeRespostaJuridicoDTO, $pessoa = "J"));
                if (is_numeric($qtdEnviadasJuridico['qtdEnviadas'])) {
                    InfraDebug::getInstance()->gravar('Qtd. Reiterações: ' . $qtdEnviadasJuridico['qtdEnviadas']);
                }
                if (is_array($qtdEnviadasJuridico['arrDadosEmailNaoEnviados']) && count($qtdEnviadasJuridico['arrDadosEmailNaoEnviados']) > 0) {
                    foreach($qtdEnviadasJuridico['arrDadosEmailNaoEnviados'] as $email) {
                        InfraDebug::getInstance()->gravar('Nº Processo: ' . $email['processo'] . ' - Usuário Externo: ' . $email['nome_usuario_externo'] . ' - E-mail Usuário Externo: ' . $email['email_usuario_externo']);
                    }
                }

                $this->gravaLogs($startScript);
            }

        } catch (Exception $e) {
            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(true);
            InfraDebug::getInstance()->setBolEcho(true);
            InfraDebug::getInstance()->limpar();

            SessaoSEI::getInstance(false);

            LogSEI::getInstance()->gravar('Erro reiterando intimacoes pendentes exige resposta.' . $e, InfraLog::$INFORMACAO);
            throw new InfraException('Erro reiterando intimacoes pendentes exige resposta.', $e);
        }

    }

    protected function atualizarEstadoIntimacoesPrazoExternoVencidoControlado()
    {

        try {

            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            InfraDebug::getInstance()->limpar();

            SessaoSEI::getInstance(false);

            $numSeg = InfraUtil::verificarTempoProcessamento();
            InfraDebug::getInstance()->gravar('ATUALIZANDO O ESTADO DE INTIMACOES VENCIDAS E SEM RESPOSTA');

            $objMdPetIntRelDestRN = new MdPetIntRelDestinatarioRN();

            $intimacoesVencidas = $objMdPetIntRelDestRN->retornaAtualizaIntimacoesSemRespostaVencidas();

            InfraDebug::getInstance()->gravar('Qtd. Intimacoes Vencidas e Sem Resposta: ' . $intimacoesVencidas);

            $numSeg = InfraUtil::verificarTempoProcessamento($numSeg);
            InfraDebug::getInstance()->gravar('TEMPO TOTAL DE EXECUCAO: ' . $numSeg . ' s');
            InfraDebug::getInstance()->gravar('FIM');

            LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug(), InfraLog::$INFORMACAO);

        } catch (Exception $e) {
            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(true);
            InfraDebug::getInstance()->setBolEcho(true);
            InfraDebug::getInstance()->limpar();

            SessaoSEI::getInstance(false);

            LogSEI::getInstance()->gravar('Erro atualizando o estado das intimacoes vencidas e sem resposta.' . $e, InfraLog::$INFORMACAO);
            throw new InfraException('Erro atualizando o estado das intimacoes vencidas e sem resposta.', $e);
        }

    }

    protected function atualizarEstadoTodasIntimacoesControlado()
    {
        try {

            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            InfraDebug::getInstance()->limpar();

            SessaoSEI::getInstance(false);

            $numSeg = InfraUtil::verificarTempoProcessamento();
            InfraDebug::getInstance()->gravar('ATUALIZANDO O ESTADO DE TODAS AS INTIMACOES JA REALIZADAS NO SEI');

            $objMdPetRegrasGeraisRN = new MdPetRegrasGeraisRN();
            $objMdPetIntRelDestRN = new MdPetIntRelDestinatarioRN();

            $objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
            $objMdPetIntRelDestDTO->retNumIdMdPetIntRelDestinatario();

            $count = $objMdPetIntRelDestRN->contar($objMdPetIntRelDestDTO);

            if ($count > 0) {
                $arrRetornoDTO = $objMdPetIntRelDestRN->listar($objMdPetIntRelDestDTO);
                $idsRelDest = InfraArray::converterArrInfraDTO($arrRetornoDTO, 'IdMdPetIntRelDestinatario');
                $arrSituacoesAtuais = $objMdPetRegrasGeraisRN->retornaSituacoesIntimacoes(array($idsRelDest, true));
                $arrSituacaoSeparado = $objMdPetRegrasGeraisRN->formatarArrSituacoes($arrSituacoesAtuais);

                $objMdPetIntRelDestRN->atualizarCadaEstadoIntimacao($arrSituacaoSeparado, MdPetIntimacaoRN::$INTIMACAO_PENDENTE);
                $objMdPetIntRelDestRN->atualizarCadaEstadoIntimacao($arrSituacaoSeparado, MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_PRAZO);
                $objMdPetIntRelDestRN->atualizarCadaEstadoIntimacao($arrSituacaoSeparado, MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_POR_ACESSO);
                $objMdPetIntRelDestRN->atualizarCadaEstadoIntimacao($arrSituacaoSeparado, MdPetIntimacaoRN::$INTIMACAO_RESPONDIDA);
                $objMdPetIntRelDestRN->atualizarCadaEstadoIntimacao($arrSituacaoSeparado, MdPetIntimacaoRN::$INTIMACAO_PRAZO_VENCIDO);
            }

            InfraDebug::getInstance()->gravar('Qtd. Intimacoes Atualizadas: ' . $count);

            $numSeg = InfraUtil::verificarTempoProcessamento($numSeg);
            InfraDebug::getInstance()->gravar('TEMPO TOTAL DE EXECUCAO: ' . $numSeg . ' s');
            InfraDebug::getInstance()->gravar('FIM');

            LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug(), InfraLog::$INFORMACAO);

        } catch (Exception $e) {
            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(true);
            InfraDebug::getInstance()->setBolEcho(true);
            InfraDebug::getInstance()->limpar();

            SessaoSEI::getInstance(false);

            LogSEI::getInstance()->gravar('Erro atualizando o estado das intimacoes.' . $e, InfraLog::$INFORMACAO);
            throw new InfraException('Erro atualizando o estado das intimacoes.', $e);
        }
    }
	
	protected function atualizarAutorrepresentacaoUsuarioExternoControlado()
	{
		try {
			
			InfraDebug::getInstance()->setBolLigado(true);
			InfraDebug::getInstance()->setBolDebugInfra(false);
			InfraDebug::getInstance()->setBolEcho(false);
			InfraDebug::getInstance()->limpar();
			
			$numSeg = InfraUtil::verificarTempoProcessamento();
			InfraDebug::getInstance()->gravar('ATUALIZANDO REGISTROS DE USUARIOS EXTERNOS AUTORREPRESENTAVEIS');
			
			// USANDO SQL PURO E NOT EXISTS PARA COMPATIBILIDADE ENTRE OS BANCOS
			$sqlContatoUsuarioNovos = "SELECT t1.id_contato
							FROM usuario t1
							WHERE NOT EXISTS (
								SELECT 1
								FROM md_pet_vinculo_represent t2
								WHERE t1.id_contato = t2.id_contato
								AND t2.tipo_representante = '".MdPetVincRepresentantRN::$PE_AUTORREPRESENTACAO."'
							)
							AND t1.sta_tipo = '".UsuarioRN::$TU_EXTERNO."'
							AND t1.sin_ativo = 'S'";
			
			$arrContatoUsuariosNovos = $this->getObjInfraIBanco()->consultarSql($sqlContatoUsuarioNovos);
			
			InfraDebug::getInstance()->gravar(count($arrContatoUsuariosNovos) . ' REGISTROS A ATUALIZAR.');
			
			if(!empty($arrContatoUsuariosNovos)){
				
				$atualizados = 0;
				
				foreach ($arrContatoUsuariosNovos as $contatoNovo){
					
					// VERIFICA SE JÁ EXISTE UM VÍNCULO
					$objMdPetVinculoDTO = new MdPetVinculoDTO();
					$objMdPetVinculoDTO->setNumIdContato($contatoNovo['id_contato']);
					$objMdPetVinculoDTO->retNumIdMdPetVinculo();
					$objMdPetVinculoDTO->setOrdNumIdMdPetVinculo(InfraDTO::$TIPO_ORDENACAO_ASC);
					$objMdPetVinculoDTO->setNumMaxRegistrosRetorno(1);
					$objMdPetVinculoDTO = (new MdPetVinculoRN())->consultar($objMdPetVinculoDTO);
					
					if(empty($objMdPetVinculoDTO)) {
						
						// CADASTRA O PRIMEIRO VÍNCULO DA PESSOA FISICA QUE SERÁ REPRESENTADA
						$objMdPetVinculoDTO = new MdPetVinculoDTO();
						$objMdPetVinculoDTO->setNumIdContato($contatoNovo['id_contato']);
						$objMdPetVinculoDTO->setStrSinValidado('N');
						$objMdPetVinculoDTO->retNumIdMdPetVinculo();
						$objMdPetVinculoDTO->setDblIdProtocolo(null);
						$objMdPetVinculoDTO->setStrTpVinculo('F');
						$objMdPetVinculoDTO->setStrSinWebService('N');
						$objMdPetVinculoDTO->setDthDataUltimaConsultaRFB(InfraData::getStrDataHoraAtual());
						$objMdPetVinculoDTO = (new MdPetVinculoRN())->cadastrar($objMdPetVinculoDTO);
						
					}
					
					// CADASTRAR O VINCULO DE AUTORREPRESENTAÇÃO
					$objNovoMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
					$objNovoMdPetVincRepresentantDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_AUTORREPRESENTACAO);
					$objNovoMdPetVincRepresentantDTO->setNumIdContato($contatoNovo['id_contato']);
					$objNovoMdPetVincRepresentantDTO->setNumIdMdPetVinculo($objMdPetVinculoDTO->getNumIdMdPetVinculo());
					$objNovoMdPetVincRepresentantDTO->setNumIdContatoOutorg($contatoNovo['id_contato']);
					$objNovoMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
					$objNovoMdPetVincRepresentantDTO->setDthDataCadastro(InfraData::getStrDataHoraAtual());
					(new MdPetVincRepresentantRN())->cadastrar($objNovoMdPetVincRepresentantDTO);
					
					$atualizados++;
					
				}
				
				InfraDebug::getInstance()->gravar($atualizados . ' REGISTROS ATUALIZADOS.');
				
			}
			
			if($atualizaVinculacoes = false){
				
				// ATUALIZAR REPRESENTANTES EXISTENTES DE USUARIOS AUTORREPRESENTAVEIS
				InfraDebug::getInstance()->gravar('ATUALIZANDO REPRESENTANTES EXISTENTES DE USUARIOS AUTORREPRESENTAVEIS');
				
				$objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
				
				$objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
				$objMdPetVincRepresentantDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_AUTORREPRESENTACAO);
				$objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
				$objMdPetVincRepresentantDTO->retNumIdContato();
				$objMdPetVincRepresentantDTO->setDistinct(true);
				$arrObjMdPetVincRepresentant = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);
				
				InfraDebug::getInstance()->gravar(count($arrObjMdPetVincRepresentant) . ' REGISTROS A ATUALIZAR.');
				
				if(!empty($arrObjMdPetVincRepresentant)){
					
					$vincAtualizados = 0;
					
					foreach ($arrObjMdPetVincRepresentant as $objMdPetVincRepresentant){
						
						$objUsuarioDTO = new UsuarioDTO();
						$objUsuarioDTO->setStrStaTipo(UsuarioRN::$TU_EXTERNO);
						$objUsuarioDTO->setNumIdContato($objMdPetVincRepresentant->getNumIdContato());
						$objUsuarioDTO->setStrSinAtivo('S');
						$objUsuarioDTO->retNumIdContato();
						$arrObjUsuariosAtivos = (new UsuarioRN())->listarRN0490($objUsuarioDTO);
						
						$arrIdContatoUsuariosAtivos = InfraArray::converterArrInfraDTO($arrObjUsuariosAtivos,'IdContato');
						
						$objMdPetVincRepresentant->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
						$objMdPetVincRepresentant->setDthDataEncerramento(null);
						
						if(empty($arrIdContatoUsuariosAtivos)){
							$objMdPetVincRepresentant->setStrStaEstado(MdPetVincRepresentantRN::$RP_INATIVO);
							$objMdPetVincRepresentant->setDthDataEncerramento(InfraData::getStrDataHoraAtual());
						}
						
						$objMdPetVincRepresentantRN->alterar($objMdPetVincRepresentant);
						$vincAtualizados++;
						
					}
					
					InfraDebug::getInstance()->gravar($vincAtualizados . ' VÍNCULOS ATUALIZADOS.');
					
				}
				
			}
			
			$numSeg = InfraUtil::verificarTempoProcessamento($numSeg);
			InfraDebug::getInstance()->gravar('TEMPO TOTAL DE EXECUCAO: ' . $numSeg . ' s');
			InfraDebug::getInstance()->gravar('FIM');
			
			LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug(), InfraLog::$INFORMACAO);
			
		} catch (Exception $e) {
			InfraDebug::getInstance()->setBolLigado(false);
			InfraDebug::getInstance()->setBolDebugInfra(false);
			InfraDebug::getInstance()->setBolEcho(false);
			throw new InfraException('Erro ao atualizar as autorrepresentações.', $e);
		}
	}

  protected function ConsultarSituacaoReceitaCnpjConectado()
  {
	
	  $numSeg       = InfraUtil::verificarTempoProcessamento();
	  $qtConsulta   = intval($this->recuperarQuantidadeParametrizadaPessoaJuridica());
	
	  if(!empty($qtConsulta) && $qtConsulta > 0){
		
		  $filaConsultaCNPJ = $this->recuperarFilaConsultaCnpj($qtConsulta);
		
		  $retorno = $this->consultarAtualizarVinculoPJ($filaConsultaCNPJ, 0, $qtConsulta);
		
		  $titulo = 'ALTERANDO RESPONSÁVEIS LEGAIS E PROCURACÕES ELETRÔNICAS DE PESSOAS JURÍDICAS CONFORME SITUAÇÃO CADASTRAL NA RECEITA FEDERAL';
		  $this->logarSuspensaoVinculacoesEProcuracoes($retorno, $numSeg, $titulo, MdPetVincRepresentantRN::$NT_JURIDICA, $qtConsulta);
		
	  }
	  
  }

  protected function ConsultarSituacaoReceitaCpfConectado()
  {
  	
  	  // Zerar timeout e aumentar a memoria
	  ini_set('max_execution_time', '0');
	  ini_set('memory_limit', '1024M');
    
  	  $numSeg       = InfraUtil::verificarTempoProcessamento();
      $qtConsulta   = intval($this->recuperarQuantidadeParametrizadaPessoaFisica());
      
      if(!empty($qtConsulta)){
	
	      $filaConsultaCPF = $this->recuperarFilaConsultaCpf($qtConsulta);
	
	      $retorno = $this->consultarAtualizarVinculoPF($filaConsultaCPF, 0, $qtConsulta);
	
	      $titulo = 'ALTERANDO AUTORREPRESENTAÇÕES E PROCURACÕES ELETRÔNICAS DE PESSOAS FÍSICAS CONFORME SITUAÇÃO CADASTRAL NA RECEITA FEDERAL';
	      $this->logarSuspensaoVinculacoesEProcuracoes($retorno, $numSeg, $titulo, MdPetVincRepresentantRN::$NT_FISICA, $qtConsulta);
      	
      }
      
  }

  protected function logarSuspensaoVinculacoesEProcuracoes($filaProcessada, $numSeg, $titulo, $origem, $qtConsulta)
  {
      InfraDebug::getInstance()->setBolLigado(true);
      InfraDebug::getInstance()->setBolDebugInfra(false);
      InfraDebug::getInstance()->setBolEcho(false);
      InfraDebug::getInstance()->limpar();

      $objUsuarioPetRN = new MdPetIntUsuarioRN();
      $idUsuarioPet = $objUsuarioPetRN->getObjUsuarioPeticionamento(true);
      SessaoSEI::getInstance(false)->simularLogin(null, SessaoSEI::$UNIDADE_TESTE, $idUsuarioPet, null);
      InfraDebug::getInstance()->gravar($titulo);
	
	  $suspensos = $filaProcessada['arrObjMdPetVincRepresentantSuspensos'];
      $qtdeAutorepresentacoes = 0;
	  $qtdeResponsavelLegal = 0;
      $qtdeProcuradores = 0;
      $arrMsg = [];
	
	  if(isset($suspensos) && !empty($suspensos)){
		
		  foreach($suspensos as $suspenso){
			
			  $docSuspensao         = $suspenso['documentoSuspensao'];
			  $tipoRepresentante    = $suspenso['tipoRepresentante'];
			  $documentoObjVinc     = $suspenso['documentoObjVinc'];
			  $razaoSocialNomeVinc  = $suspenso['razaoSocialNomeVinc'];
			  $procuracoes          = $suspenso['procuracoes'];
			
			  switch ($tipoRepresentante) {
				
				  case MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL:
					  $arrMsg[] = $razaoSocialNomeVinc.' (' . $documentoObjVinc . ')';
					  $arrMsg[] = ' ';
					  
					  if(!empty($procuracoes)) {
						
						  $arrMsgProcuracoes[] = 'Procurações Suspensas: ';
						
						  foreach ($procuracoes as $objMdPetVincRepresentantDTO) {
							
							  switch ($objMdPetVincRepresentantDTO->getStrTipoRepresentante()) {
								
								  case MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES:
									  $arrMsg[] = $objMdPetVincRepresentantDTO->getStrNomeProcurador() . ' - CPF: ' . $objMdPetVincRepresentantDTO->getStrCpfProcurador();
									  $arrMsg[] = 'Procuração Eletrônica Simples (' . $docSuspensao . ')';
									  $arrMsg[] = ' ';
									  $qtdeProcuradores++;
									  break;
								
								  case MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL:
									  $arrMsg[] = $objMdPetVincRepresentantDTO->getStrNomeProcurador() . ' - CPF: ' . $objMdPetVincRepresentantDTO->getStrCpfProcurador();
									  $arrMsg[] = 'Procuração Eletrônica Especial (' . $docSuspensao . ')';
									  $arrMsg[] = ' ';
									  $qtdeProcuradores++;
									  break;
								
							  }
							
						  }
					  
					  }
					
					  $arrMsg[] = ' ';
					  $arrMsg[] = 'Responsável Legal Suspenso: ' . $suspenso['responsavel_legal']->getStrNomeProcurador() . ' - CPF: ' . $suspenso['responsavel_legal']->getStrCpfProcurador();
					  $arrMsg[] = 'Documento de Suspensão de Vinculação à Pessoa Jurídica (' . $docSuspensao . ')';
					
					  $qtdeResponsavelLegal++;
					  
					  $arrMsg[] = '------------------------------------------------------------------------------';
					  
					  break;
				
				
				  case MdPetVincRepresentantRN::$PE_AUTORREPRESENTACAO:
					
					  $arrMsg[] = 'Autorrepresentação de '.current($suspenso['autorrepresentante'])->getStrNomeProcurador() . ' - CPF: ' . current($suspenso['autorrepresentante'])->getStrCPF();
					
					  foreach ($suspenso['arrBuscarOurasRepresentacoesPorCPF'] as $arrMdPetVincRepresentant) {
						
						  if ($arrMdPetVincRepresentant['objMdPetVincRepresentant']->getStrTpVinc() == MdPetVincRepresentantRN::$NT_JURIDICA) {
							
							  if($arrMdPetVincRepresentant['objMdPetVincRepresentant']->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL){
								  $arrMsg[] = 'Documento de Suspensão de Vinculação à Pessoa Jurídica (' . $arrMdPetVincRepresentant['documentoSuspensao'] . ')';
								  $qtdeResponsavelLegal++;
							  }
							
							  if(in_array($arrMdPetVincRepresentant['objMdPetVincRepresentant']->getStrTipoRepresentante(), [MdPetVincRepresentantRN::$PE_PROCURADOR, MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES])){
								  $arrMsg[] = 'Documento de Suspensão de Procuração Eletrônica Simples PJ (' . $arrMdPetVincRepresentant['documentoSuspensao'] . ')';
								  $qtdeProcuradores++;
							  }
							
							  if(in_array($arrMdPetVincRepresentant['objMdPetVincRepresentant']->getStrTipoRepresentante(), [MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL])){
								  $arrMsg[] = 'Documento de Suspensão de Procuração Eletrônica Especial PJ (' . $arrMdPetVincRepresentant['documentoSuspensao'] . ')';
								  $qtdeProcuradores++;
							  }
							
						  }else{
							
							  if(in_array($arrMdPetVincRepresentant['objMdPetVincRepresentant']->getStrTipoRepresentante(), [MdPetVincRepresentantRN::$PE_PROCURADOR, MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES, MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL])){
								  $arrMsg[] = 'Documento de Suspensão de Procuração Eletrônica Simples PF (' . $arrMdPetVincRepresentant['documentoSuspensao'] . ')';
								  $qtdeProcuradores++;
							  }
							
						  }
						
					  }
					  $arrMsg[] = 'Desativação de Usuário Externo: ' . current($suspenso['autorrepresentante'])->getStrEmail();
					  $qtdeAutorepresentacoes++;
					
					  $arrMsg[] = '------------------------------------------------------------------------------';
					  break;
				
			    }
			
		  }
		
	  }
      
	  InfraDebug::getInstance()->gravar('Qtd. Registros verificados: ' . $qtConsulta);
	
	  // CASO AGENDAMENTO TENHA SIDO EXECUTADO PELA CONSULTA AUTOREPRESENTAÇÃO
	  if($origem == MdPetVincRepresentantRN::$NT_FISICA){
		  InfraDebug::getInstance()->gravar('Qtd. de Autorepresentação suspensas: ' . $qtdeAutorepresentacoes);
          InfraDebug::getInstance()->gravar('Qtd. de Usuários Externos desativados: ' . $qtdeAutorepresentacoes);
      }

      InfraDebug::getInstance()->gravar('Qtd. de Vinculações à Pessoa Jurídica suspensas: ' . $qtdeResponsavelLegal);
      InfraDebug::getInstance()->gravar('Qtd. de Procurações Eletrônica suspensas: ' . $qtdeProcuradores);
      InfraDebug::getInstance()->gravar('------------------------------------------------------------------------------');

      foreach($arrMsg as $msg){
          InfraDebug::getInstance()->gravar($msg);
      }
	
	  if(isset($filaProcessada['erros']) && !empty($filaProcessada['erros'])) {
		
	  	  InfraDebug::getInstance()->gravar('Qtd. Falhas na suspensão: ' . count($filaProcessada['erros']));
		  InfraDebug::getInstance()->gravar('Falhas na suspensão: ');
		  foreach($filaProcessada['erros'] as $itemErro){
			  InfraDebug::getInstance()->gravar($itemErro);
		  }
		
	  }

      $numSeg = InfraUtil::verificarTempoProcessamento($numSeg);
      InfraDebug::getInstance()->gravar('TEMPO TOTAL DE EXECUCAO: ' . $numSeg . ' s');
      InfraDebug::getInstance()->gravar('FIM');
      LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug(), InfraLog::$INFORMACAO);
      
  }
	
	protected function extrairVinculos($arrObjMdPetVincRepresentantDTO, $cpf) {
  
		$vinculos = [
			'outorgante' => [],
			'outorgado' => [],
			'autorrepresentado' => []
		];
		
		foreach ($arrObjMdPetVincRepresentantDTO as $objVinc) {
			
			if ($cpf === $objVinc->getStrCPF() && $objVinc->getStrCPF() !== $objVinc->getStrCpfProcurador()) {
				$vinculos['outorgante'][] = $objVinc;
			} elseif ($cpf === $objVinc->getStrCpfProcurador() && $objVinc->getStrCPF() !== $objVinc->getStrCpfProcurador()) {
				$vinculos['outorgado'][] = $objVinc;
			} elseif ($cpf === $objVinc->getStrCPF() && $objVinc->getStrCPF() === $objVinc->getStrCpfProcurador()) {
				$vinculos['autorrepresentado'][] = $objVinc;
			}
			
		}
		
		return $vinculos;
	}
	
	
	protected function consultarAtualizarVinculoPF($filaConsultaCPF, $count, $qtConsulta)
  {
	
  	/*
     *
     * PASSOS PARA EXECUTAR O PROCESSO DE SUSPENSÃO DE PESSOA FISICA
     *
     *  1. CPF ja ter emitido procuracoes
     *  1.1 CPF possuir processo de Vinculação para anexar documentos (É OUTORGANTE PF)
     *  1.2 Suspensão
     *      3.1. OUTORGANTE - Suspende procurações emitidas pela PF (Doc e E-mail)
     *      3.2. OUTORGADO - Suspende procurações recebidas de PF, PJ e Vinculações à Pessoa Jurídica (Doc e E-mail)
     *      3.3. AUTORREPRESENTADO - Suspende Autorrepresentação (Doc e E-mail)
     *      3.4. USUARIO EXTERNO - Desativa o Usuário Externo  (Doc e E-mail)
     *      3.5. Logar as informações
     *
    */
  	
  	$objMdPetIntegracaoRN = new MdPetIntegracaoRN();
    $arrObjMdPetVincRepresentantSuspensos = [];
	  
    foreach ($filaConsultaCPF as $cpf) {
    	
	    $situacao = 'processando';
	    
	    // Adicionando os cnpjs na tabela de log
	    BancoSEI::getInstance()->executarSql("INSERT INTO md_pet_fila_consulta_rfb (cpf_cnpj, tipo, created_at, situacao) VALUES ('".$cpf."', 'F', '".date('Y-m-d H:i:s')."', '".$situacao."')");
	
	    $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
	
	    if (InfraUtil::validarCpf($cpf) && $qtConsulta > 0 && $count < $qtConsulta){
		
		    // 1. VERIFICA SE O USUARIO DO CPF JA EMITIU PROCURACAO ELETRONICA:
		    $emitiuProcuracao = $this->listarVinculosOutorgantePF($cpf);
		
		    if(!empty($emitiuProcuracao) && count($emitiuProcuracao) > 0 && $objMdPetVincRepresentantRN->possuiProcessoVinculacao($cpf)){
			
			    if($objMdPetIntegracaoRN->consultarCPFReceitaWsResponsavelLegal($cpf)) {
				
			    	// Aqui ele lista todos os vinculos do CPF
				    $arrObjMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->listarVincRepresentAtivosPorCPF($cpf);
				    
				    // Sempre vai ter ao menos o vinculo de autorepresentacao ativo
				    if(!empty($arrObjMdPetVincRepresentantDTO) && count($arrObjMdPetVincRepresentantDTO) > 0){
					
					    $vinculos = $this->extrairVinculos($arrObjMdPetVincRepresentantDTO, $cpf);
				    
					    // 3.1. OUTORGANTE - Suspende procurações emitidas pela PF (Doc e E-mail)
					    $outorgante = $objMdPetVincRepresentantRN->suspenderVinculacoesConectado($vinculos['outorgante'], true, []);
					    if(!empty($outorgante)){
						    $situacao = 'susp_outorgante';
						    $arrObjMdPetVincRepresentantSuspensos[] = $outorgante;
					    }
					
					    // 3.2. OUTORGADO - Suspende procurações recebidas de PF, PJ e Vinculações à Pessoa Jurídica (Doc e E-mail)
					    $outorgado = $objMdPetVincRepresentantRN->suspenderVinculacoesConectado($vinculos['outorgado'], true, []);
					    if(!empty($outorgado)){
						    $situacao = 'susp_outorgado';
						    $arrObjMdPetVincRepresentantSuspensos[] = $outorgado;
					    }
					    
					    // 3.3. AUTORREPRESENTADO - Suspende Autorrepresentação (Doc e E-mail)
					    $autorrepresentado = $objMdPetVincRepresentantRN->suspenderVinculacoesConectado($vinculos['autorrepresentado'], true, []);
					    if(!empty($autorrepresentado)){
						    $situacao = 'suspenso';
						    $arrObjMdPetVincRepresentantSuspensos[] = $autorrepresentado;
					    }
				    
				    }else{

					    $situacao = 'sem_vinculos';

				    }
				
			    }else{

					$situacao = 'nao_localizado';
					
				}
		    	
		    }else{

		    	$situacao = 'sem_processo';

		    }
		
	    }else{
		
		    $situacao = 'cpf_invalido';
		    
	    }
	
	    BancoSEI::getInstance()->executarSql("UPDATE md_pet_fila_consulta_rfb SET situacao = '".$situacao."' where cpf_cnpj = '".$cpf."' and situacao = 'processando'");
	
	    $count ++;
	    $this->atualizarDataUltimaConsultaRFB($cpf, MdPetVincRepresentantRN::$NT_FISICA);
      
    }
	
	$erros = array_reduce($arrObjMdPetVincRepresentantSuspensos, function ($carry, $suspensoes) {
	  foreach ($suspensoes as $suspensao) {
		  if (isset($suspensao['erros'])) {
			  $carry[] = $suspensao['erros'];
		  }
	  }
	  return $carry;
	}, []);
	
	$retorno['count'] = $count;
    $retorno['erros'] = $erros;
    $retorno['arrObjMdPetVincRepresentantSuspensos'] = $arrObjMdPetVincRepresentantSuspensos;
    
    return $retorno;
    
  }
  
  public function listarVinculosOutorganteAtivos($cpf){
	
	  $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
	  $objMdPetVincRepresentantDTO->setStrTpVinc([MdPetVincRepresentantRN::$NT_FISICA, MdPetVincRepresentantRN::$NT_JURIDICA], InfraDTO::$OPER_IN);
	  $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
	  $objMdPetVincRepresentantDTO->setStrCPF($cpf); // ONDE O CONTATO É O OUTORGANTE
	  $objMdPetVincRepresentantDTO->retTodos();
	  $objMdPetVincRepresentantDTO->retStrTipoRepresentante();
	  $objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
	  $objMdPetVincRepresentantDTO->retStrNomeProcurador();
	  $objMdPetVincRepresentantDTO->retStrEmail();
	  $objMdPetVincRepresentantDTO->retStrCpfProcurador();
	  return (new MdPetVincRepresentantRN())->listar($objMdPetVincRepresentantDTO);
   
  }
	
	public function listarVinculosOutorgantePF($cpf){
		
		$objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
		$objMdPetVincRepresentantDTO->setStrTpVinc([MdPetVincRepresentantRN::$NT_FISICA], InfraDTO::$OPER_IN);
		$objMdPetVincRepresentantDTO->setStrTipoRepresentante([MdPetVincRepresentantRN::$PE_AUTORREPRESENTACAO], InfraDTO::$OPER_NOT_IN); // VERIFICA SE JÁ EMITIU PROCURAÇÃO
		$objMdPetVincRepresentantDTO->setStrCPF($cpf); // ONDE O CONTATO É O OUTORGANTE
		$objMdPetVincRepresentantDTO->retTodos();
		$objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
		$objMdPetVincRepresentantDTO->retNumIdMdPetVinculo();
		$objMdPetVincRepresentantDTO->retStrTipoRepresentante();
		$objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
		$objMdPetVincRepresentantDTO->retStrNomeProcurador();
		$objMdPetVincRepresentantDTO->retStrStaEstado();
		$objMdPetVincRepresentantDTO->retStrEmail();
		$objMdPetVincRepresentantDTO->retStrCpfProcurador();
		
		return (new MdPetVincRepresentantRN())->listar($objMdPetVincRepresentantDTO);
		
	}
	
	public function listarVinculosOutorgantePFAtivos($cpf){
		
		$objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
		$objMdPetVincRepresentantDTO->setStrTpVinc([MdPetVincRepresentantRN::$NT_FISICA], InfraDTO::$OPER_IN);
		$objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
		$objMdPetVincRepresentantDTO->setStrTipoRepresentante([MdPetVincRepresentantRN::$PE_AUTORREPRESENTACAO], InfraDTO::$OPER_NOT_IN); // VERIFICA SE JÁ EMITIU PROCURAÇÃO
		$objMdPetVincRepresentantDTO->setStrCPF($cpf); // ONDE O CONTATO É O OUTORGANTE
		$objMdPetVincRepresentantDTO->retTodos();
		$objMdPetVincRepresentantDTO->retStrTipoRepresentante();
		$objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
		$objMdPetVincRepresentantDTO->retStrNomeProcurador();
		$objMdPetVincRepresentantDTO->retStrEmail();
		$objMdPetVincRepresentantDTO->retStrCpfProcurador();
		
		return (new MdPetVincRepresentantRN())->listar($objMdPetVincRepresentantDTO);
		
	}
	
	public function listarVinculosOutorgantePJAtivos($cpf){
		
		$objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
		$objMdPetVincRepresentantDTO->setStrTpVinc([MdPetVincRepresentantRN::$NT_JURIDICA], InfraDTO::$OPER_IN);
		$objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
		$objMdPetVincRepresentantDTO->setStrCPF($cpf); // ONDE O CONTATO É O OUTORGANTE
		$objMdPetVincRepresentantDTO->retTodos();
		$objMdPetVincRepresentantDTO->retStrTipoRepresentante();
		$objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
		$objMdPetVincRepresentantDTO->retStrNomeProcurador();
		$objMdPetVincRepresentantDTO->retStrEmail();
		$objMdPetVincRepresentantDTO->retStrCpfProcurador();
		return (new MdPetVincRepresentantRN())->listar($objMdPetVincRepresentantDTO);
		
	}
	
	public function listarVinculosOutorgadoPJAtivos($cpf){
		
		$objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
		$objMdPetVincRepresentantDTO->setStrTpVinc([MdPetVincRepresentantRN::$NT_JURIDICA], InfraDTO::$OPER_IN);
		$objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
		$objMdPetVincRepresentantDTO->setStrCpfProcurador($cpf); // ONDE O CONTATO É O OUTORGADO
		$objMdPetVincRepresentantDTO->retTodos();
		$objMdPetVincRepresentantDTO->retStrTipoRepresentante();
		$objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
		$objMdPetVincRepresentantDTO->retStrNomeProcurador();
		$objMdPetVincRepresentantDTO->retStrEmail();
		$objMdPetVincRepresentantDTO->retStrCPF();
		return (new MdPetVincRepresentantRN())->listar($objMdPetVincRepresentantDTO);
		
	}
	
	public function listarVinculosOutorgadoAtivos($cpf){
		
		$objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
		$objMdPetVincRepresentantDTO->setStrTpVinc([MdPetVincRepresentantRN::$NT_FISICA, MdPetVincRepresentantRN::$NT_JURIDICA], InfraDTO::$OPER_IN);
		$objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
		$objMdPetVincRepresentantDTO->setStrCpfProcurador($cpf); // ONDE O CONTATO É O OUTORGADO
		$objMdPetVincRepresentantDTO->retTodos();
		$objMdPetVincRepresentantDTO->retStrTipoRepresentante();
		$objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
		$objMdPetVincRepresentantDTO->retStrNomeProcurador();
		$objMdPetVincRepresentantDTO->retStrEmail();
		$objMdPetVincRepresentantDTO->retStrCpfProcurador();
		return (new MdPetVincRepresentantRN())->listar($objMdPetVincRepresentantDTO);
		
	}

  protected function consultarAtualizarVinculoPJ($filaConsultaCNPJ, $count, $qtConsulta)
  {
	
	  	$erros = [];
	  	$objMdPetIntegracaoRN = new MdPetIntegracaoRN();
	  	$arrObjMdPetVincRepresentantSuspensos = [];
	  
	  	foreach ($filaConsultaCNPJ as $cnpj) {
		
		  	if(!empty($cnpj) && InfraUtil::validarCnpj($cnpj)){
			
			  	$objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();

			  	if ($qtConsulta > 0 && $count < $qtConsulta){

					$situacao = 'processando';

                  	// Adicionando os cnpjs na tabela de log
                  	BancoSEI::getInstance()->executarSql("INSERT INTO md_pet_fila_consulta_rfb (cpf_cnpj, tipo, created_at, situacao) VALUES ('".$cnpj."', 'J', '".date('Y-m-d H:i:s')."', '".$situacao."')");

				  	if($objMdPetIntegracaoRN->consultarCNPJReceitaWsResponsavelLegal($cnpj)) {

					  	$arrObjMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->listarVincRepresentAtivosPorCNPJ($cnpj);
                      	$suspenso = $objMdPetVincRepresentantRN->suspenderProcuracaoConectado($arrObjMdPetVincRepresentantDTO, true, []);

                      	if(is_array($suspenso)){
                          	$arrObjMdPetVincRepresentantSuspensos[] = $suspenso;
                          	$situacao = 'suspenso';
                      	}else{
                          	$situacao = 'falha';
                      	}

                      	// Atualizando os cnpjs na tabela de log
                      	BancoSEI::getInstance()->executarSql("UPDATE md_pet_fila_consulta_rfb SET situacao = '".$situacao."' where cpf_cnpj = '".$cnpj."' and situacao = 'processando'");
                      	// BancoSEI::getInstance()->executarSql("INSERT INTO md_pet_fila_consulta_rfb (cpf_cnpj, tipo, created_at, situacao) VALUES ('".$cnpj."', 'J', '".date('Y-m-d H:i:s')."', '".$situacao."')");

				  	}else{

                      	$situacao = 'consultado';

                      	// Atualizando os cnpjs na tabela de log
                      	BancoSEI::getInstance()->executarSql("UPDATE md_pet_fila_consulta_rfb SET situacao = '".$situacao."' where cpf_cnpj = '".$cnpj."' and situacao = 'processando'");

                  	}

			  	}
			
			  	$count ++;
			  	$this->atualizarDataUltimaConsultaRFB($cnpj, MdPetVincRepresentantRN::$NT_JURIDICA);
			
		  	}
      
	  	}
	
		$erros = array_map(function($erro) {
			return is_array($erro) ? implode(', ', $erro) : $erro;
		}, $erros);
		
		$retorno['count'] = $count;
		$retorno['erros'] = $erros;
		$retorno['arrObjMdPetVincRepresentantSuspensos'] = $arrObjMdPetVincRepresentantSuspensos;
		
		return $retorno;
    
  	}

  protected function atualizarDataUltimaConsultaRFB($cpfCnpj, $natureza)
  {
  	
  	  $objContatoDTO = new ContatoDTO();
	  if($natureza == 'F'){
		  $objContatoDTO->setDblCpf($cpfCnpj);
	  }else{
		  $objContatoDTO->setStrCnpj($cpfCnpj);
	  }
	  $objContatoDTO->retNumIdContato();
	  $arrObjContato = (new ContatoRN())->listarRN0325($objContatoDTO);
	  
	  $arrIdContato = InfraArray::converterArrInfraDTO($arrObjContato, 'IdContato');
	  $arrIdContatos = array_chunk($arrIdContato, 10);
	  
	  foreach($arrIdContatos as $arrIdContato) {
		
		  $objMdPetVinculoDTO = new MdPetVinculoDTO();
		  $objMdPetVinculoDTO->retNumIdMdPetVinculo();
		  $objMdPetVinculoDTO->retDthDataUltimaConsultaRFB();
		  $objMdPetVinculoDTO->setNumIdContato($arrIdContato, InfraDTO::$OPER_IN);
		  $objMdPetVinculoDTO->setStrTpVinculo($natureza);
		  $objMdPetVinculoDTO->setNumMaxRegistrosRetorno(1);
		  $objMdPetVinculoDTO = (new MdPetVinculoRN())->consultar($objMdPetVinculoDTO);
		
		  if (!empty($objMdPetVinculoDTO)) {
			
			  $objMdPetVinculoDTO->setDthDataUltimaConsultaRFB(InfraData::getStrDataHoraAtual());
			  (new MdPetVinculoRN())->alterar($objMdPetVinculoDTO);
			
		  }
		
	  }
   
  }

  protected function recuperarQuantidadeParametrizadaPessoaFisica()
  {
  	$retorno = 0;
    $infraAgendamentoDTO    = new InfraAgendamentoTarefaDTO();
    $infraAgendamentoDTO->setStrComando('MdPetAgendamentoAutomaticoRN::ConsultarSituacaoReceitaCpf');
    $infraAgendamentoDTO->retStrParametro();
    $infraAgendamentoDTO->setNumMaxRegistrosRetorno(1);
    $agendamento = (new InfraAgendamentoTarefaRN())->consultar($infraAgendamentoDTO);
    
    if(!empty($agendamento)){
	    $arrParametros = explode(',', $agendamento->getStrParametro());
	
	    foreach ($arrParametros as $parametro) {
		    $obj = explode('=', $parametro);
		    if ($obj[0] == 'QtConsulta') {
			    $retorno = $obj[1];
		    }
	    }
    }
	
    return $retorno;
	
  }

  protected function recuperarQuantidadeParametrizadaPessoaJuridica()
  {
	
  	$retorno = 0;
  	
    $infraAgendamentoDTO = new InfraAgendamentoTarefaDTO();
    $infraAgendamentoDTO->setStrComando('MdPetAgendamentoAutomaticoRN::ConsultarSituacaoReceitaCnpj');
    $infraAgendamentoDTO->retStrParametro();
    $infraAgendamentoDTO->setNumMaxRegistrosRetorno(1);
    $agendamento = (new InfraAgendamentoTarefaRN())->consultar($infraAgendamentoDTO);
    
    if(!empty($agendamento)){
	
	    $arrParametros = explode(',', $agendamento->getStrParametro());
	
	    foreach ($arrParametros as $parametro) {
		    $obj = explode('=', $parametro);
		    if ($obj[0] == 'QtConsulta') {
			    $retorno = $obj[1];
		    }
	    }
    	
    }
    
    return $retorno;

  }

  protected function recuperarFilaConsultaCpf($qtConsulta)
  {
	
	  $objMdPetVinculoDTO = new MdPetVinculoDTO();
	  $objMdPetVinculoDTO->retNumIdMdPetVinculo();
	  $objMdPetVinculoDTO->retStrCpfContatoRepresentante();
	  $objMdPetVinculoDTO->setStrTpVinculo('F');
	  $objMdPetVinculoDTO->setDblIdProtocolo(NULL, InfraDTO::$OPER_DIFERENTE); // Pega apenas os que tem processo de vinculacao
	  $objMdPetVinculoDTO->setOrdDthDataUltimaConsultaRFB(InfraDTO::$TIPO_ORDENACAO_ASC);
	  $objMdPetVinculoDTO->setNumMaxRegistrosRetorno($qtConsulta);
	  $arrObjMdPetVinculoRN = (new MdPetVinculoRN())->listar($objMdPetVinculoDTO);
	
	  $filaConsultaCPF = InfraArray::converterArrInfraDTO($arrObjMdPetVinculoRN, 'CpfContatoRepresentante');
	
	  $filaConsultaCPF = array_filter($filaConsultaCPF, function($value) {
		  return !is_null($value);
	  });
	
	  return $filaConsultaCPF;
	 
  }

  protected function recuperarFilaConsultaCnpj($qtConsulta)
  {
	
	  $objMdPetVinculoDTO = new MdPetVinculoDTO();
	  $objMdPetVinculoDTO->retNumIdMdPetVinculo();
	  $objMdPetVinculoDTO->retStrCNPJ();
	  $objMdPetVinculoDTO->setStrTpVinculo('J');
	  $objMdPetVinculoDTO->setOrdDthDataUltimaConsultaRFB(InfraDTO::$TIPO_ORDENACAO_ASC);
	  $objMdPetVinculoDTO->setNumMaxRegistrosRetorno($qtConsulta);
	  $arrObjMdPetVinculoRN = (new MdPetVinculoRN())->listar($objMdPetVinculoDTO);
	
	  $filaConsultaCNPJ = InfraArray::converterArrInfraDTO($arrObjMdPetVinculoRN, 'CNPJ');
	
	  $filaConsultaCNPJ = array_filter($filaConsultaCNPJ, function($value) {
		  return !is_null($value);
	  });
	
	  return $filaConsultaCNPJ;
  
  }
	
	
  // Todo: Remover funcao apos testes em SU e validacao em PD
  protected function atualizarFilaConsultaReceitaFederalCPF()
  {
      $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
      $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
      $objMdPetVincRepresentantDTO->setStrTpVinc('F');
      $objMdPetVincRepresentantDTO->setStrStaEstado('A');
      $objMdPetVincRepresentantDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_AUTORREPRESENTACAO);
      $objMdPetVincRepresentantDTO->setOrdDthDataCadastro(InfraDTO::$TIPO_ORDENACAO_ASC);
      $objMdPetVincRepresentantDTO->retStrCPF();
	  $objMdPetVincRepresentantDTO->setNumMaxRegistrosRetorno(20);
      $arrCpf = InfraArray::converterArrInfraDTO($objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO), 'CPF');

      foreach ($arrCpf as $cpf){
        $objMdPetFilaConsultaRfRN = new MdPetFilaConsultaRfRN();
        $MdPetFilaConsultaRfDTO = new MdPetFilaConsultaRfDTO();
        $MdPetFilaConsultaRfDTO->setStrStaNatureza('F');
        $MdPetFilaConsultaRfDTO->setDblCpfCnpj($cpf);
        $objMdPetFilaConsultaRfRN->cadastrar($MdPetFilaConsultaRfDTO);
      }

      return $arrCpf;
  }
	
	// Todo: Remover funcao apos testes em SU e validacao em PD
	protected function atualizarFilaConsultaReceitaFederalCNPJ()
  {
  	
    $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
    $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
    $objMdPetVincRepresentantDTO->setStrTpVinc(MdPetVincRepresentantRN::$NT_JURIDICA);
    $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
    $objMdPetVincRepresentantDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL);
    $objMdPetVincRepresentantDTO->setOrdDthDataCadastro(InfraDTO::$TIPO_ORDENACAO_ASC);
    $objMdPetVincRepresentantDTO->retStrCNPJ();
    // Todo: Remover o retorno maximo de registros
    $objMdPetVincRepresentantDTO->setNumMaxRegistrosRetorno(10);
    $arrCNPJ = InfraArray::converterArrInfraDTO($objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO), 'CNPJ');

    foreach ($arrCNPJ as $cnpj){
      $objMdPetFilaConsultaRfRN = new MdPetFilaConsultaRfRN();
      $MdPetFilaConsultaRfDTO = new MdPetFilaConsultaRfDTO();
      $MdPetFilaConsultaRfDTO->setStrStaNatureza(MdPetVincRepresentantRN::$NT_JURIDICA);
      $MdPetFilaConsultaRfDTO->setDblCpfCnpj($cnpj);
      $objMdPetFilaConsultaRfRN->cadastrar($MdPetFilaConsultaRfDTO);
    }
	
//	  $MdPetFilaConsultaRfDTO = new MdPetFilaConsultaRfDTO();
//	  $MdPetFilaConsultaRfDTO->setStrStaNatureza(MdPetVincRepresentantRN::$NT_JURIDICA);
//	  $MdPetFilaConsultaRfDTO->retDblCpfCnpj();
//	  $retorno = (new MdPetFilaConsultaRfRN())->listar($MdPetFilaConsultaRfDTO);
//
//	  die(var_dump($retorno));

    return $arrCNPJ;
    
  }

  protected function EnviarDadosSistemaModuloConectado(): void
    {
        try {
            if (!ConfiguracaoSEI::getInstance()->getValor('SEI', 'Producao', false, false)) {
                return;
            }

            ini_set('max_execution_time', '0');
            $debug = InfraDebug::getInstance();
            $debug->setBolLigado(true);
            $debug->setBolDebugInfra(false);
            $debug->setBolEcho(false);
            $debug->limpar();

            $inicioProcessamento = InfraUtil::verificarTempoProcessamento();
            InfraDebug::getInstance()->gravar('INICIANDO ENVIO DE DADOS PARA CENTRALIZA MODULOS');

            // 1) Tentativa de obter/gerar chave
            $payloadChave = self::montarPayloadGerarChave();
            $palavraChave = $this->obterChaveAcessoValida($payloadChave);

            if (empty($palavraChave)) {
                InfraDebug::getInstance()->gravar('ERRO: CHAVE DE ACESSO NÃO ENCONTRADA OU NÃO GERADA.');
                $this->finalizarProcesso($inicioProcessamento);
                return;
            }

            // 2) Obter lista de módulos
            $modulosMantidos = self::listarModulosMantidosAnatel($palavraChave, $payloadChave['SiglaOrgao']);

            // 3) Se a lista falhar (chave expirada), tenta renovar UMA vez
            if (empty($modulosMantidos->data)) {
                InfraDebug::getInstance()->gravar('CHAVE POSSIVELMENTE EXPIRADA. TENTANDO RENOVAÇÃO ÚNICA...');
                $palavraChave = $this->renovarChaveAcesso($payloadChave);

                if (!empty($palavraChave)) {
                    $modulosMantidos = self::listarModulosMantidosAnatel($palavraChave, $payloadChave['SiglaOrgao']);
                }
            }

            // 4) Processa os módulos se houver dados
            if (!empty($modulosMantidos->data)) {
                $configuracoes = ConfiguracaoSEI::getInstance()->getArrConfiguracoes();
                $modulosInstalados = $configuracoes['SEI']['Modulos'] ?? [];

                foreach ($modulosMantidos->data as $moduloAnatel) {
                    $nomeClasse = $moduloAnatel->Modulo;

                    if (!isset($modulosInstalados[$nomeClasse]) || !class_exists($nomeClasse)) {
                        continue;
                    }

                    $objModulo = new $nomeClasse();
                    InfraDebug::getInstance()->gravar("ENVIANDO: " . $objModulo->getNome());

                    $payload = $this->montarPayloadEnviarDados($objModulo, $palavraChave, $nomeClasse);
                    $resposta = $this->enviarDados($payload, 30);

                    InfraDebug::getInstance()->gravar($this->validarRespostaEnviarDados($resposta));
                    $this->verificarNotificarUltimaVersaoModuloDisponivel($objModulo, $moduloAnatel->Versao, $moduloAnatel->URLRepositorio);
                }
            } else {
                InfraDebug::getInstance()->gravar('AVISO: NENHUM MÓDULO PENDENTE DE ENVIO OU RETORNO VAZIO DA API.');
            }

            $this->finalizarProcesso($inicioProcessamento);
        } catch (Exception $e) {
            // Log de erro crítico sem dar throw para não quebrar o agendador
            InfraDebug::getInstance()->gravar('ERRO CRÍTICO NA FUNÇÃO: ' . $e->getMessage());
            $debug->setBolLigado(false);
            $debug->setBolDebugInfra(false);
            $debug->setBolEcho(false);
        }
    }

    /**
     * Encapsula o fechamento do log e tempo de execução
     */
    private function finalizarProcesso($tempoInicio): void
    {
        $tempoTotal = InfraUtil::verificarTempoProcessamento($tempoInicio);
        InfraDebug::getInstance()->gravar("TEMPO TOTAL: {$tempoTotal}s - FIM.");
        // Desliga o debug para evitar logs desnecessários
        //LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug(), InfraLog::$INFORMACAO);
    }

    private function obterChaveAcessoValida(array $payload): string
    {
        $chave = self::recuperaChaveAcessoParametro();
        return !empty($chave) ? $chave : $this->renovarChaveAcesso($payload);
    }

    protected function renovarChaveAcesso(array $payload): string
    {
        $arrChave = self::gerarChaveAcesso($payload, 30);
        if ($arrChave['http_code'] == 200 && isset($arrChave['json']['message'])) {
            self::salvarChaveAcessoAgendamento($arrChave['json']['message']);
            return $arrChave['json']['message'];
        }

        InfraDebug::getInstance()->gravar('FALHA AO RENOVAR CHAVE DE ACESSO. HTTP CODE: ' . $arrChave['http_code'] . ' - RESPOSTA: ' . $arrChave['json']['message']);
        return '';
    }

    protected function recuperaChaveAcessoParametro()
    {
        $retorno = '';
        $infraAgendamentoDTO    = new InfraAgendamentoTarefaDTO();
        $infraAgendamentoDTO->setStrComando('MdPetAgendamentoAutomaticoRN::EnviarDadosSistemaModulo');
        $infraAgendamentoDTO->retStrParametro();
        $infraAgendamentoDTO->setNumMaxRegistrosRetorno(1);
        $agendamento = (new InfraAgendamentoTarefaRN())->consultar($infraAgendamentoDTO);

        if (!empty($agendamento)) {
            $arrParametros = explode(',', $agendamento->getStrParametro());

            foreach ($arrParametros as $parametro) {
                $obj = explode('=', $parametro);
                if ($obj[0] == 'palavraChave') {
                    $retorno = $obj[1];
                }
            }
        }

        return $retorno;
    }

    private function salvarChaveAcessoAgendamento($palavraChave)
    {

        $infraAgendamentoTarefaRN = new InfraAgendamentoTarefaRN();
        $infraAgendamentoDTO = new InfraAgendamentoTarefaDTO();
        $infraAgendamentoDTO->setStrComando('MdPetAgendamentoAutomaticoRN::EnviarDadosSistemaModulo');
        $infraAgendamentoDTO->retNumIdInfraAgendamentoTarefa();
        $infraAgendamentoDTO->retStrParametro();
        $infraAgendamentoDTO->setNumMaxRegistrosRetorno(1);
        $objInfraAgendamentoDTO = $infraAgendamentoTarefaRN->consultar($infraAgendamentoDTO);
        $objInfraAgendamentoDTO->setStrParametro('palavraChave=' . $palavraChave);
        $infraAgendamentoTarefaRN->alterar($objInfraAgendamentoDTO);
    }

    private static function gerarChaveAcesso(array $body, int $timeout = 30)
    {
        // ----------------------------------------------------------------------------
        // 1) Monta URL do serviço
        // ----------------------------------------------------------------------------

        $base = self::URL_ANATEL;

        $path = '/md_central_mds_gerar_chave_acesso';
        $servico = 'md_central_mds_gerar_chave_acesso';

        $url = $base . $path . '?servico=' . urlencode($servico);

        $ch = curl_init($url);

        $json = json_encode($body, JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            throw new InfraException('Falha ao gerar JSON do payload: ' . json_last_error_msg());
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
        ]);

        $raw = curl_exec($ch);

        if ($raw === false) {
            $err = curl_error($ch);
            $no  = curl_errno($ch);
            throw new InfraException("Erro ao chamar serviço REST (cURL #$no): $err");
        }

        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $decoded = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            // nem sempre a API devolve JSON em erro; então retornamos raw junto
            return [
                'http_code' => $httpCode,
                'raw' => $raw,
                'json' => null
            ];
        }

        return [
            'http_code' => $httpCode,
            'raw' => $raw,
            'json' => $decoded
        ];
    }

    private static function listarModulosMantidosAnatel($palavraChave, $siglaOrgao)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => self::URL_ANATEL . '/md_central_mds_lista_modulos_distribuidos'
                . '?servico=md_central_mds_lista_modulos_distribuidos'
                . '&SiglaOrgao=' . $siglaOrgao
                . '&IdentificacaoServico=' . $palavraChave,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_TIMEOUT_MS => 15000
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($httpCode === 200 && $response !== false) {
            return json_decode($response);
        }

        self::logErroConsultaModulosAnatel();
        return null;
    }

    private static function logErroConsultaModulosAnatel()
    {
        $log  = "00001 - ERRO DE RECURSO NO SEI IA\n";
        $log .= "00002 - NÃO FOI POSSIVEL CONSULTAR OS MÓDULOS MANTIDOS PELA ANATEL\n";
        $log .= "00003 - DATA E HORA: " . InfraData::getStrDataHoraAtual() . "\n";
        $log .= "00004 - FIM\n";

        //LogSEI::getInstance()->gravar($log, InfraLog::$INFORMACAO);
    }

    private function montarPayloadEnviarDados($moduloIntegracao, $palavraChave, $nomeClasse)
    {
        $objInfraConfiguracao = ConfiguracaoSEI::getInstance();
        $SessaoSei = $objInfraConfiguracao->getValor('SessaoSEI');

        return [
            'SiglaOrgao'           => mb_convert_encoding($SessaoSei['SiglaOrgaoSistema'], 'UTF-8', 'ISO-8859-1'),
            'IdentificacaoServico' => $palavraChave,
            'Modulo'               => mb_convert_encoding($nomeClasse, 'UTF-8', 'ISO-8859-1'),
            'VersaoModulo'         => mb_convert_encoding($moduloIntegracao->getVersao(), 'UTF-8', 'ISO-8859-1')
        ];
    }

    private function montarPayloadGerarChave()
    {
        $objInfraConfiguracao = ConfiguracaoSEI::getInstance();
        $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
        $SessaoSei = $objInfraConfiguracao->getValor('SessaoSEI');
        $BancoSEI = $objInfraConfiguracao->getValor('BancoSEI');
        $SEI = $objInfraConfiguracao->getValor('SEI');
        $emailAdministrador = $objInfraParametro->getValor('SEI_EMAIL_ADMINISTRADOR');

        return [
            'SiglaOrgao'            => mb_convert_encoding($SessaoSei['SiglaOrgaoSistema'], 'UTF-8', 'ISO-8859-1'),
            'VersaoSei'             => SEI_VERSAO,
            'Url'                   => $SEI['URL'],
            'EmailAdministrador'    => $emailAdministrador,
            'TipoBancoDados'        => $BancoSEI['Tipo']
        ];
    }

    private function validarRespostaEnviarDados($resp)
    {
        // ----------------------------------------------------------------------------
        // 4) Valida retorno
        // ----------------------------------------------------------------------------
        $msgRetotorno = 'DADOS ENVIADOS COM SUCESSO.';
        if ($resp['http_code'] < 200 || $resp['http_code'] >= 300) {
            $msg = 'Falha ao enviar dados. HTTP ' . $resp['http_code'];
            if (is_array($resp['json']) && isset($resp['json']['message'])) {
                $msg .= ' - ' . $resp['json']['message'];
            }
            $msgRetotorno = $msg;
        }

        if (is_array($resp['json']) && ($resp['json']['status'] ?? '') !== 'success') {
            $msgRetotorno = 'SERVIÇO RETORNOU ERRO: ' . mb_convert_encoding($resp['json']['message'] ?? $resp['raw'], 'ISO-8859-1', 'UTF-8');
        }

        return $msgRetotorno;
    }

    private function enviarDados(array $body, int $timeout = 30): array
    {
        // ----------------------------------------------------------------------------
        // 1) Monta URL do serviço
        // ----------------------------------------------------------------------------

        $base = self::URL_ANATEL;

        $path = '/md_central_mds_recebe_dados_orgaos_externos';
        $servico = 'md_central_mds_recebe_dados_orgaos_externos';

        $url = $base . $path . '?servico=' . urlencode($servico);

        $ch = curl_init($url);

        $json = json_encode($body, JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            throw new InfraException('Falha ao gerar JSON do payload: ' . json_last_error_msg());
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
        ]);

        $raw = curl_exec($ch);

        if ($raw === false) {
            $err = curl_error($ch);
            $no  = curl_errno($ch);
            throw new InfraException("Erro ao chamar serviço REST (cURL #$no): $err");
        }

        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $decoded = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            // nem sempre a API devolve JSON em erro; então retornamos raw junto
            return [
                'http_code' => $httpCode,
                'raw' => $raw,
                'json' => null
            ];
        }

        return [
            'http_code' => $httpCode,
            'raw' => $raw,
            'json' => $decoded
        ];
    }

    private function verificarNotificarUltimaVersaoModuloDisponivel($moduloIntegracao, $versaoModuloDisponivel, $urlRepositorio)
    {

        $versaoGitHub = ltrim($versaoModuloDisponivel, 'vV');
        if (version_compare($moduloIntegracao->getVersao(), $versaoGitHub) < 0) {
            $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
            $strEmailSistema = $objInfraParametro->getValor('SEI_EMAIL_SISTEMA');
            $strEmailAdministrador = $objInfraParametro->getValor('SEI_EMAIL_ADMINISTRADOR');

            $objInfraConfiguracao = ConfiguracaoSEI::getInstance();
            $SessaoSei = $objInfraConfiguracao->getValor('SessaoSEI');

            if (!InfraString::isBolVazia($strEmailSistema) && !InfraString::isBolVazia($strEmailAdministrador)) {

                MailSEI::getInstance()->limpar();

                $objEmailDTO = new EmailDTO();
                $objEmailDTO->setStrDe($strEmailSistema);
                $objEmailDTO->setStrPara($strEmailAdministrador);
                $objEmailDTO->setStrAssunto('SEI - Nova versão do módulo ' . $moduloIntegracao->getNome() . ' disponível.');

                $strConteudo = 'Prezado(a),' . "\n\n\n";
                $strConteudo .= 'Informamos que foi disponibilizada uma nova versão do módulo ' . $moduloIntegracao->getNome() . ', mantido pela Anatel.' . "\n\n";
                $strConteudo .= 'Versão ' . $versaoModuloDisponivel . ' disponível.' . "\n\n";
                $strConteudo .= 'Atualmente, o órgão ' . $SessaoSei['SiglaOrgaoSistema'] . ' utiliza a versão ' . $moduloIntegracao->getVersao() . ' deste módulo.' . "\n\n";
                $strConteudo .= 'Recomendamos que seja verificada a compatibilidade da nova versão com a versão do SEI instalada no ambiente, a fim de possibilitar a atualização e o aproveitamento das melhorias, correções e eventuais ajustes de segurança disponibilizados.' . "\n\n";
                $strConteudo .= 'Para esclarecimento de dúvidas ou solicitação de suporte, utilize as issues do repositório oficial no GitHub ' . $urlRepositorio . ', onde a equipe responsável realiza o acompanhamento.' . "\n\n\n";
                $strConteudo .= 'Atenciosamente,' . "\n";
                $strConteudo .= 'SEI - Sistema Eletrônico de Informações' . "\n";

                $objEmailDTO->setStrMensagem($strConteudo);

                MailSEI::getInstance()->adicionar($objEmailDTO);
                MailSEI::getInstance()->enviar();
            }
        }
    }
}
?>