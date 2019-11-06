<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 29/03/2017 - criado por marcelo.cast
 *
 * Versão do Gerador de Código: 1.40.0
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetAgendamentoAutomaticoRN extends InfraRN {

    public function __construct(){
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco(){
        return BancoSEI::getInstance();
    }

    /* Método que realiza cumprimento automático de intimações por decurso de prazo */
    protected function CumprirPorDecursoPrazoTacitoControlado( ){

        try{
            
            ini_set('max_execution_time','0');
            ini_set('memory_limit','1024M');

            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            InfraDebug::getInstance()->limpar();


            $objUsuarioPetRN  = new MdPetIntUsuarioRN();
            $idUsuarioPet = $objUsuarioPetRN->getObjUsuarioPeticionamento(true);
            SessaoSEI::getInstance(false)->simularLogin(null, SessaoSEI::$UNIDADE_TESTE, $idUsuarioPet , null);

            $numSeg = InfraUtil::verificarTempoProcessamento();
            InfraDebug::getInstance()->gravar('CUMPRINDO INTIMAÇOES POR DECURSO DE PRAZO');

            $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
            $intimacoesPendentes = $objMdPetIntAceiteRN->verificarIntimacoesPrazoExpirado();

            InfraDebug::getInstance()->gravar('Qtd. Intimações Pendentes: ' . count($intimacoesPendentes));

            if(count($intimacoesPendentes) > 0){
                $arrIntimacoes = $objMdPetIntAceiteRN->realizarEtapasAceiteAgendado($intimacoesPendentes);
            }

            InfraDebug::getInstance()->gravar('Qtd. Intimações Cumpridas: ' . $arrIntimacoes['cumpridas']);
            InfraDebug::getInstance()->gravar('Qtd. Intimações Não Cumpridas: ' . $arrIntimacoes['naoCumpridas']);

            foreach ($arrIntimacoes['procedimentos'] as $procedimentos) {
                InfraDebug::getInstance()->gravar('Processo nº ' . $procedimentos[0] . ' - Motivo: ' . $procedimentos[1]);
            }

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
            throw new InfraException('Erro cumprindo intimação por decurso de prazo.',$e);
        }

    }

    /* Método que reintera via email acerca de Intimação Eletrônica:
     *    - Com Tipo de Resposta que Exige Resposta pelo Usuário Externo 
     *    e Pendente de Resposta após a Intimação ter sido Cumprida.
     */
    protected function ReiterarIntimacaoExigeRespostaControlado( ){

        try{

            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            InfraDebug::getInstance()->limpar();

            $numSeg = InfraUtil::verificarTempoProcessamento();
            InfraDebug::getInstance()->gravar('REITERANDO INTIMAÇÕES PENDENTES EXIGE RESPOSTA');


            $objMdPetIntimacaoRN = new MdPetIntimacaoRN();

            $intimacoesExigeRespostaDTO = $objMdPetIntimacaoRN->getIntimacoesPossuemData( array(true,true) );

            //Juridico DTO

            $intimacoesExigeRespostaJuridicoDTO = $objMdPetIntimacaoRN->getIntimacoesPossuemDataJuridico( array(true,true) );


            InfraDebug::getInstance()->gravar('Qtd. Intimações Exige Resposta: ' . count($intimacoesExigeRespostaDTO));

            if(count($intimacoesExigeRespostaDTO) > 0){
                $objMdPetIntEmailNotificacaoRN = new MdPetIntEmailNotificacaoRN();

                InfraDebug::getInstance()->setBolLigado(true);
                InfraDebug::getInstance()->setBolDebugInfra(false);
                InfraDebug::getInstance()->setBolEcho(false);

                InfraDebug::getInstance()->gravar('REITERANDO INTIMAÇÕES PENDENTES EXIGE RESPOSTA');
                InfraDebug::getInstance()->gravar('Qtd. Intimações Exige Resposta: ' . count($intimacoesExigeRespostaDTO));
                
                $qtdEnviadas = $objMdPetIntEmailNotificacaoRN->enviarEmailReiteracaoIntimacao(array($intimacoesExigeRespostaDTO,$pessoa = "F"));
                if (is_numeric($qtdEnviadas)){
                    InfraDebug::getInstance()->gravar('Qtd. Intimações Reiteradas: ' . $qtdEnviadas);
                }
            }


            //Juridico
            InfraDebug::getInstance()->gravar('Qtd. Intimações Exige Resposta Juridico: ' . count($intimacoesExigeRespostaJuridicoDTO));


            if(count($intimacoesExigeRespostaJuridicoDTO) > 0){
                $objMdPetIntEmailNotificacaoRN = new MdPetIntEmailNotificacaoRN();

                InfraDebug::getInstance()->setBolLigado(true);
                InfraDebug::getInstance()->setBolDebugInfra(false);
                InfraDebug::getInstance()->setBolEcho(false);

                InfraDebug::getInstance()->gravar('REITERANDO INTIMAÇÕES PENDENTES EXIGE RESPOSTA - JURIDICO');
                InfraDebug::getInstance()->gravar('Qtd. Intimações Exige Resposta Juridico: ' . count($intimacoesExigeRespostaJuridicoDTO));
                
                $qtdEnviadasJuridico = $objMdPetIntEmailNotificacaoRN->enviarEmailReiteracaoIntimacaoJuridico(array($intimacoesExigeRespostaJuridicoDTO,$pessoa = "J"));
                if (is_numeric($qtdEnviadas)){
                    InfraDebug::getInstance()->gravar('Qtd. Intimações Reiteradas Juridico: ' . $qtdEnviadasJuridico);
                }
            }



            $numSeg = InfraUtil::verificarTempoProcessamento($numSeg);
            InfraDebug::getInstance()->gravar('TEMPO TOTAL DE EXECUCAO: '.$numSeg.' s');
            InfraDebug::getInstance()->gravar('FIM');

            LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug(),InfraLog::$INFORMACAO);

        }catch(Exception $e){
            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            throw new InfraException('Erro reiterando intimações pendentes exige resposta.',$e);
        }

    }


    /* Método que reintera via email acerca de Intimação Eletrônica:
   *    - Com Tipo de Resposta que Exige Resposta pelo Usuário Externo
   *    e Pendente de Resposta após a Intimação ter sido Cumprida.
   */
    protected function atualizarEstadoIntimacoesPrazoExternoVencidoControlado(){

        try{

            InfraDebug::getInstance()->setBolLigado(true);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            InfraDebug::getInstance()->limpar();

            $numSeg = InfraUtil::verificarTempoProcessamento();
            InfraDebug::getInstance()->gravar('ATUALIZANDO O ESTADO DE INTIMAÇÕES VENCIDAS E SEM RESPOSTA');

            $objMdPetIntRelDestRN = new MdPetIntRelDestinatarioRN();

            $intimacoesVencidas = $objMdPetIntRelDestRN->retornaAtualizaIntimacoesSemRespostaVencidas();

            InfraDebug::getInstance()->gravar('Qtd. Intimações Vencidas e Sem Resposta: ' . $intimacoesVencidas);

            $numSeg = InfraUtil::verificarTempoProcessamento($numSeg);
            InfraDebug::getInstance()->gravar('TEMPO TOTAL DE EXECUCAO: '.$numSeg.' s');
            InfraDebug::getInstance()->gravar('FIM');

            LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug(),InfraLog::$INFORMACAO);

        }catch(Exception $e){
            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            throw new InfraException('Erro atualizando o estado das intimações vencidas e sem resposta.',$e);
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
            InfraDebug::getInstance()->gravar('ATUALIZANDO O ESTADO DE TODAS AS INTIMAÇÕES JÁ REALIZADAS NO SEI');

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

            InfraDebug::getInstance()->gravar('Qtd. Intimações Atualizadas: ' . $count);

            $numSeg = InfraUtil::verificarTempoProcessamento($numSeg);
            InfraDebug::getInstance()->gravar('TEMPO TOTAL DE EXECUCAO: '.$numSeg.' s');
            InfraDebug::getInstance()->gravar('FIM');

            LogSEI::getInstance()->gravar(InfraDebug::getInstance()->getStrDebug(),InfraLog::$INFORMACAO);

        } catch (Exception $e) {
            InfraDebug::getInstance()->setBolLigado(false);
            InfraDebug::getInstance()->setBolDebugInfra(false);
            InfraDebug::getInstance()->setBolEcho(false);
            throw new InfraException('Erro atualizando o estado das intimações.', $e);
        }


    }
}
?>