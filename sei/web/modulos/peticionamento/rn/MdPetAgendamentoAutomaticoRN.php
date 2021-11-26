<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
 *
 * 29/03/2017 - criado por marcelo.cast
 *
 * Vers�o do Gerador de C�digo: 1.40.0
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetAgendamentoAutomaticoRN extends InfraRN
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    /* M�todo que realiza cumprimento autom�tico de intima��es por decurso de prazo */
    protected function CumprirPorDecursoPrazoTacitoControlado()
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

            $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
            $intimacoesPendentes = $objMdPetIntAceiteRN->verificarIntimacoesPrazoExpirado();

            InfraDebug::getInstance()->gravar('Qtd. Intimacoes Pendentes: ' . count($intimacoesPendentes));

            if (count($intimacoesPendentes) > 0) {
                $arrIntimacoes = $objMdPetIntAceiteRN->realizarEtapasAceiteAgendado($intimacoesPendentes);

                InfraDebug::getInstance()->gravar('Qtd. Intimacoes Cumpridas: ' . $arrIntimacoes['cumpridas']);
                InfraDebug::getInstance()->gravar('Qtd. Intimacoes Nao Cumpridas: ' . $arrIntimacoes['naoCumpridas']);

                foreach ($arrIntimacoes['procedimentos'] as $procedimentos) {
                    InfraDebug::getInstance()->gravar('Processo n� ' . $procedimentos[0] . ' - Motivo: ' . $procedimentos[1]);
                }
            }
            
            $numSeg = InfraUtil::verificarTempoProcessamento($numSeg);
            InfraDebug::getInstance()->gravar('TEMPO TOTAL DE EXECUCAO: ' . $numSeg . ' s');
            InfraDebug::getInstance()->gravar('FIM');
            LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug(), InfraLog::$INFORMACAO);

            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
        } catch (Exception $e) {
            SessaoSEI::getInstance()->setBolHabilitada(true);
            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            throw new InfraException('Erro cumprindo intimacao por decurso de prazo.', $e);
        }

    }

    protected function AtualizarSituacaoProcuracaoSimplesVencidaControlado( ){

        try{
            
            ini_set('max_execution_time','0');
            ini_set('memory_limit','1024M');

            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            InfraDebug::getInstance()->limpar();

            
           
            $numSeg = InfraUtil::verificarTempoProcessamento();
            InfraDebug::getInstance()->gravar('ALTERANDO SITUACAO DE PROCURACOES VENCIDAS');

            //Alterando o Status das Procura��es Eletr�nicas
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

            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
        }catch(Exception $e){
            SessaoSEI::getInstance()->setBolHabilitada(true);
            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            throw new InfraException('Erro alterando situacao da procuracao eletronica.',$e);
        }

    }


    /* M�todo que reintera via email acerca de Intima��o Eletr�nica:
     *    - Com Tipo de Resposta que Exige Resposta pelo Usu�rio Externo 
     *    e Pendente de Resposta ap�s a Intima��o ter sido Cumprida.
     */
    protected function ReiterarIntimacaoExigeRespostaControlado()
    {

        try {

            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            InfraDebug::getInstance()->limpar();

            $numSeg = InfraUtil::verificarTempoProcessamento();
            InfraDebug::getInstance()->gravar('REITERANDO INTIMACOES PENDENTES EXIGE RESPOSTA');


            $objMdPetIntimacaoRN = new MdPetIntimacaoRN();

            $intimacoesExigeRespostaDTO = $objMdPetIntimacaoRN->getIntimacoesPossuemData(array(true, true));

            //Juridico DTO

            $intimacoesExigeRespostaJuridicoDTO = $objMdPetIntimacaoRN->getIntimacoesPossuemDataJuridico(array(true, true));


            InfraDebug::getInstance()->gravar('Qtd. Intimacoes Exige Resposta: ' . count($intimacoesExigeRespostaDTO));

            if (count($intimacoesExigeRespostaDTO) > 0) {
                $objMdPetIntEmailNotificacaoRN = new MdPetIntEmailNotificacaoRN();

                InfraDebug::getInstance()->setBolLigado(true);
                InfraDebug::getInstance()->setBolDebugInfra(false);
                InfraDebug::getInstance()->setBolEcho(false);

                InfraDebug::getInstance()->gravar('REITERANDO INTIMACOES PENDENTES EXIGE RESPOSTA');
                InfraDebug::getInstance()->gravar('Qtd. Intimacoes Exige Resposta: ' . count($intimacoesExigeRespostaDTO));

                $qtdEnviadas = $objMdPetIntEmailNotificacaoRN->enviarEmailReiteracaoIntimacao(array($intimacoesExigeRespostaDTO, $pessoa = "F"));
                if (is_numeric($qtdEnviadas)) {
                    InfraDebug::getInstance()->gravar('Qtd. Intimacoes Reiteradas: ' . $qtdEnviadas);
                }
            }


            //Juridico
            InfraDebug::getInstance()->gravar('Qtd. Intimacoes Exige Resposta: ' . count($intimacoesExigeRespostaJuridicoDTO));


            if (count($intimacoesExigeRespostaJuridicoDTO) > 0) {
                $objMdPetIntEmailNotificacaoRN = new MdPetIntEmailNotificacaoRN();

                InfraDebug::getInstance()->setBolLigado(true);
                InfraDebug::getInstance()->setBolDebugInfra(false);
                InfraDebug::getInstance()->setBolEcho(false);

                InfraDebug::getInstance()->gravar('REITERANDO INTIMACOES PENDENTES EXIGE RESPOSTA');
                InfraDebug::getInstance()->gravar('Qtd. Intimacoes Exige Resposta: ' . count($intimacoesExigeRespostaJuridicoDTO));

                $qtdEnviadasJuridico = $objMdPetIntEmailNotificacaoRN->enviarEmailReiteracaoIntimacaoJuridico(array($intimacoesExigeRespostaJuridicoDTO, $pessoa = "J"));
                if (is_numeric($qtdEnviadas)) {
                    InfraDebug::getInstance()->gravar('Qtd. Intimacoes Reiteradas: ' . $qtdEnviadasJuridico);
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
            throw new InfraException('Erro reiterando intimacoes pendentes exige resposta.', $e);
        }

    }


    /* M�todo que reintera via email acerca de Intima��o Eletr�nica:
   *    - Com Tipo de Resposta que Exige Resposta pelo Usu�rio Externo
   *    e Pendente de Resposta ap�s a Intima��o ter sido Cumprida.
   */
    protected function atualizarEstadoIntimacoesPrazoExternoVencidoControlado()
    {

        try {

            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            InfraDebug::getInstance()->limpar();

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
            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
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
            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            throw new InfraException('Erro atualizando o estado das intimacoes.', $e);
        }


    }
}

?>