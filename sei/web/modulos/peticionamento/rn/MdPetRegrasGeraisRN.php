<?php
/**
 * Created by PhpStorm.
 * User: jhon.carvalho
 * Date: 21/11/2017
 * Time: 09:13
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetRegrasGeraisRN extends InfraRN
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    public function _retornaObjAtributoAndamentoAPI($nome, $valor, $idOrigem = null)
    {
        $objAtributoAndamentoAPI = new AtributoAndamentoAPI();
        $objAtributoAndamentoAPI->setNome($nome);
        $objAtributoAndamentoAPI->setValor($valor);

        if($idOrigem != null){
            $objAtributoAndamentoAPI->setIdOrigem($idOrigem); //ID do prédio, pode ser null
        }

        return $objAtributoAndamentoAPI;
    }

    protected function verificarCumprimentoIntimacaoConectado($idAcessoExterno){


            $objMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
            $objMdPetIntRelDestinatarioDTO->setNumIdAcessoExterno($idAcessoExterno);
            $objMdPetIntRelDestinatarioDTO->retNumIdMdPetIntRelDestinatario();


            $objMdPetIntRelDestinatarioRN = new MdPetIntRelDestinatarioRN();
            $arrMdPetIntRelDestinatario = $objMdPetIntRelDestinatarioRN->listar($objMdPetIntRelDestinatarioDTO);

            foreach ($arrMdPetIntRelDestinatario as $objMdPetIntRelDestinatario){

                $objMdPetIntAceiteDTO = new MdPetIntAceiteDTO();
                $objMdPetIntAceiteDTO->setNumIdMdPetIntRelDestinatario($objMdPetIntRelDestinatario->getNumIdMdPetIntRelDestinatario());
                $objMdPetIntAceiteDTO->retNumIdMdPetIntAceite();

                $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
                $obj = $objMdPetIntAceiteRN->contar($objMdPetIntAceiteDTO)>0;


                if(!$obj){
                    return false;
                }
          }

        return true;
    }
    
    protected function verificarDocumentoTipoIntegralConectado($IdAcessoExterno){
        
     
        $objRelAcessoExtProtocoloDTO = new RelAcessoExtProtocoloDTO();
        $objRelAcessoExtProtocoloDTO->setNumIdAcessoExterno($IdAcessoExterno);
        $objRelAcessoExtProtocoloDTO->retTodos();
        $objRelAcessoExtProtocoloRN = new RelAcessoExtProtocoloRN();
        $objRelAcessoExtProtocolo = $objRelAcessoExtProtocoloRN->contar($objRelAcessoExtProtocoloDTO) >0;

        if($objRelAcessoExtProtocolo){
            return false;
        }
        
        return true;
        
    }
    protected function verificarDocumentoIndisponibilidadeConectado($objDocumentoAPI){

        $acao =$objDocumentoAPI[1];
        $objDocumentoDTO = $objDocumentoAPI[0];
        $msg='';

        $objMdPetIndisponibilidadeDocDTO = new MdPetIndisponibilidadeDocDTO();
        $objMdPetIndisponibilidadeDocDTO->setDblIdDocumento($objDocumentoDTO->getIdDocumento());
        $objMdPetIndisponibilidadeDocDTO->retDblIdDocumento();

        $objMdPetIndisponibilidadeDocRN = new MdPetIndisponibilidadeDocRN();
        $existeDocumento = $objMdPetIndisponibilidadeDocRN->contar($objMdPetIndisponibilidadeDocDTO)>0;

        if($existeDocumento){
            $msg.='Não é permitido '.$acao.' o documento pois o mesmo está vinculado à uma indisponilidade.';
        }
        return $msg;
    }
    protected function verificarExistenciaUnidadeConectado($arrObjUnidadeAPI)
    {

        $acao = $arrObjUnidadeAPI[1];
        $arrObjUnidadeAPI = $arrObjUnidadeAPI[0];
        $arrIds = array();
        $msg = '';
        foreach ($arrObjUnidadeAPI as $objUnidade) {
            $arrIds[] = $objUnidade->getIdUnidade();
        }

        $objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
        $objMdPetRelTpProcessoUnidDTO->setNumIdUnidade($arrIds, InfraDTO::$OPER_IN);
        $objMdPetRelTpProcessoUnidRN = new MdPetRelTpProcessoUnidRN();

        $existeMdPetRelTpProcessoUnid = $objMdPetRelTpProcessoUnidRN->contar($objMdPetRelTpProcessoUnidDTO) > 0;


        if ($existeMdPetRelTpProcessoUnid) {
            $msg = $this->_retornaMsgTipoProcessoUnidade($objMdPetRelTpProcessoUnidDTO, $acao);
            return $msg;
        }

        $objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
        $objMdPetIntRelDestDTO->setNumIdUnidade($arrIds, InfraDTO::$OPER_IN);
        $objMdPetIntRelDestDTO->setDthDataAceite(null, InfraDTO::$OPER_IGUAL);
        $objMdPetIntRelDestDTO->retNumIdMdPetIntimacao();
        $objMdPetIntRelDestDTO->retNumIdUnidade();
        $objMdPetIntRelDestRN = new MdPetIntRelDestinatarioRN();
        $existeobjMdPetIntRelDest = $objMdPetIntRelDestRN->contar($objMdPetIntRelDestDTO) > 0;

        if ($existeobjMdPetIntRelDest) {
            $objMdPetIntRelDest = $objMdPetIntRelDestRN->listar($objMdPetIntRelDestDTO);
            $objMdPetIntRelDestDTO = $this->_prazoIntimacaoUnidade($objMdPetIntRelDest);

            if(!empty($objMdPetIntRelDestDTO)) {
                $msg = $this->_retornaMsgIntimacaoUnidadeDestinatario($objMdPetIntRelDestDTO, $acao);
                return $msg;
            }
        }

        $objMdPetIndispDocDTO = new MdPetIndisponibilidadeDocDTO();
        $objMdPetIndispDocDTO->setNumIdUnidade($arrIds, InfraDTO::$OPER_IN);
        $objMdPetIndispDocRN = new MdPetIndisponibilidadeDocRN();
        $existeobjMdPetIndispDoc = $objMdPetIndispDocRN->contar($objMdPetIndispDocDTO) > 0;

        if($existeobjMdPetIndispDoc){

            $msg = $this->_retornaMsgAnexoUnidade($objMdPetIndispDocDTO,$acao);
            return $msg;
        }

        return $msg;
        //return true;



    }

    // Retorna o Id's do MdPetIntimacao que ainda estão em curso
    private function  _prazoIntimacaoUnidade($objMdPetIntRelDestDTO){

            $arrIdsInt = InfraArray::converterArrInfraDTO($objMdPetIntRelDestDTO, 'IdMdPetIntimacao');
            $arrIdsInt = array_unique($arrIdsInt);
        //Dto com intimações ainda em curso
        $objIntimacaoCursoDTO = array();
            if (count($arrIdsInt) > 0) {

                $objMdPetIntRelDestRN = new MdPetIntRelDestinatarioRN();
                $objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
                $objMdPetIntRelDestDTO->retDthDataAceite();
                $objMdPetIntRelDestDTO->retNumIdMdPetIntimacao();
                $objMdPetIntRelDestDTO->retNumIdMdPetIntRelDestinatario();
                $objMdPetIntRelDestDTO->retStrNomeTipoRespostaAceita();
                $objMdPetIntRelDestDTO->setStrSinPrincipalDoc('S');
                $objMdPetIntRelDestDTO->retStrNomeSerie();
                $objMdPetIntRelDestDTO->retStrNumero();
                $objMdPetIntRelDestDTO->retStrProtocoloFormatadoDocumento();

                $objMdPetIntRelDestDTO->setNumIdMdPetIntimacao($arrIdsInt, InfraDTO::$OPER_IN);

                $arrDTO = $objMdPetIntRelDestRN->listar($objMdPetIntRelDestDTO);

                $objMdPetIntDestRespostaDto = new MdPetIntDestRespostaDTO();
                $objMdPetIntDestRespostaDto->retNumIdMdPetIntRelDestinatario();

                $objMdPetIntTpRespRN = new MdPetIntRelTipoRespRN();
                $objMdPetIntTpRespDTO = new MdPetIntRelTipoRespDTO();
                $objMdPetIntTpRespDTO->retNumIdMdPetIntTipoResp();
                $objMdPetIntTpRespDTO->retDthDataLimite();
                $objMdPetIntTpRespDTO->retDthDataProrrogada();


                foreach ($arrDTO as $objDTO) {
                    /**
                     * Verifica se a intimacao foi cumprida, caso nao tenha sido fica bloqueado.
                     */
                    if (is_null($objDTO->getDthDataAceite())) {
                        $objIntimacaoCursoDTO[]=$objDTO;
                    }

                    /**
                     * Verifica se o tipo de resposta da intimação é sem resposta
                     */
                    if($objDTO->getStrNomeTipoRespostaAceita() != 'S'){
                        $objMdPetIntDestRespostaDto->setNumIdMdPetIntRelDestinatario($objDTO->getNumIdMdPetIntRelDestinatario());

                        $objMdPetIntTpRespDTO->setNumIdMdPetIntimacao($objDTO->getNumIdMdPetIntimacao());

                        /**
                         * Verifica se o tipo de resposta, para verificar se tem mais de uma intimação a ser respondida
                         */
                        $arrRespostaFacultada = $objMdPetIntTpRespRN->listar($objMdPetIntTpRespDTO);
                        foreach ($arrRespostaFacultada as $arrResposta){
                            $dataFim = !is_null($arrResposta->getDthDataProrrogada()) ? $arrResposta->getDthDataProrrogada() : $arrResposta->getDthDataLimite();
                            if (is_null($dataFim)) {
                                $objIntimacaoCursoDTO[]=$objDTO;
                            }else{
                                $arrData = explode(' ', $dataFim);
                                $arrData = count($arrData) > 0 ? explode('/', $arrData[0]) : null;

                                if (count($arrData) > 0) {
                                    $objDateTimeBd = new DateTime();
                                    $objDateTimeBd->setDate($arrData[2], $arrData[1], $arrData[0]);
                                    $objDateTimeAtual = new DateTime();
                                    $isValidoInt = $objDateTimeBd >= $objDateTimeAtual;
                                    if($isValidoInt){
                                        $objIntimacaoCursoDTO[]=$objDTO;
                                    }
                                }
                            }
                        }

                    }
                }
            }
        return array_unique($objIntimacaoCursoDTO);
    }

    protected function verificarExistenciaTipoProcessoConectado($arrObjTipoProcessoDTO)
    {

        $acao = $arrObjTipoProcessoDTO[1];
        $arrObjTipoProcessoDTO = $arrObjTipoProcessoDTO[0];
        $msg = '';

        $arrIds = array();
        foreach ($arrObjTipoProcessoDTO as $objTipoProcedimentoAPI) {
            $arrIds[] = $objTipoProcedimentoAPI->getIdTipoProcedimento();
        }

        $objMdPetTipoProcessoDTO = new MdPetTipoProcessoDTO();
        $objMdPetTipoProcessoDTO->setNumIdProcedimento($arrIds, InfraDTO::$OPER_IN);
        $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();

        $existeMdPetTipoProcesso = $objMdPetTipoProcessoRN->contar($objMdPetTipoProcessoDTO) > 0;
        $existeProcesso = $existeMdPetTipoProcesso;


        if (!$existeProcesso) {

          /**
           * Retirada a validação de desativar tipo de processo para peticionamento a partir de criterios
           * cadastrados Item 103.2 - Planilha de peticionamento
           * @since 10/01/2018
           */
          /**
           * Fim de retirada de validação de tipo de processo para peticionamento a partir de criterios
           * Para descomentar devera ser retirado o bloco de codigo  $existeProcesso = false;
          */
            $existeProcesso = false;

        }

        //Condições para retornar a mensagem de erro
        if ($existeProcesso) {

            if (count($arrIds) > 1)
                $msg = 'Não é permitido ' . $acao . ' estes Tipos de Processos, pois eles são utilizados';
            else
                $msg = 'Não é permitido ' . $acao . ' este Tipo de Processo, pois ele é utilizado';

            $msg .= ' pelo Módulo de Peticionamento e Intimação Eletrônicos. Verifique as parametrizações no menu';

            if ($existeMdPetTipoProcesso)
                $msg .= ' Administração > Peticionamento Eletrônico > Tipos para Peticionamento';
            else
                $msg .= ' Administração > Peticionamento Eletrônico > Critérios para Intercorrente .';

        }
        return $msg;
    }
    private function _retornaMsgAnexoUnidade($objMdPetIndispDocDTO, $acao){


        $objMdPetIndispDocDTO->retNumIdIndisponibilidade();
        $objMdPetIndispDocRN = new MdPetIndisponibilidadeDocRN();
        $objMdPetIndispDoc = $objMdPetIndispDocRN->listar($objMdPetIndispDocDTO);

        $arrId =array();

        foreach ($objMdPetIndispDoc as $obj){
            $arrId []= $obj->getNumIdIndisponibilidade();
        }
        
        $objMdPetIndisponibilidadeDTO = new MdPetIndisponibilidadeDTO();
        $objMdPetIndisponibilidadeDTO->setNumIdIndisponibilidade($arrId,InfraDTO::$OPER_IN);
        $objMdPetIndisponibilidadeDTO->retDthDataInicio();
        $objMdPetIndisponibilidadeDTO->retDthDataFim();
        
        
        $objMdPetIndisponibilidadeRN = new MdPetIndisponibilidadeRN();
        $objMdPetIndisponibilidade = $objMdPetIndisponibilidadeRN->listar($objMdPetIndisponibilidadeDTO);

        $msg ="Não é permitido ".$acao ." esta Unidade, pois ela é utilizada pelo Módulo de Peticionamento e Intimação ";
        $msg.="Eletrônicos. Verifique os Documentos vinculados a esta Unidade no menu Administração > Peticionamento Eletrônico > Indisponibilidades do SEI.\n\n Referente ao periodo:\n\n";
        foreach ($objMdPetIndisponibilidade as $obj){
            $dtInicio = str_replace(' ', ' - ',substr($obj->getDthDataInicio(), 0, -3));
            $dtFim= str_replace(' ', ' - ',substr($obj->getDthDataFim(), 0, -3));
            $msg.="     \" ". $dtInicio." a ".$dtFim." \"\n";
        }
        return $msg;

    }

    private function _retornaMsgIntimacaoUnidadeDestinatario($objMdPetIntRelDestDTO, $acao)
    {

        $msg = "Não é permitido " . $acao . " esta Unidade, pois ela é utilizada pelo Módulo de Peticionamento e Intimação Eletrônicos ";
        $msg .= "para as intimações abaixo criadas nesta Unidade, que ainda estão em curso (não cumpridas ou com prazo externo não vencido):\n\n";

        foreach ($objMdPetIntRelDestDTO as $obj) {
            $numeroDoc='';
            if($obj->getStrNumero()>0){
                $numeroDoc = 'n°'.$obj->getStrNumero();
            }

            $msg .= "     - Intimação do Documento Principal ". $obj->getStrNomeSerie() ." ".$numeroDoc . " ( SEI nº" . $obj->getStrProtocoloFormatadoDocumento() . " )\n";
        }

        return $msg;
    }

    private function _retornaMsgTipoProcessoUnidade($objMdPetRelTpProcessoUnidDTO, $acao)
    {

        $arrayUnidade = array();

        $objMdPetRelTpProcessoUnidDTO->retNumIdUnidade();
        $objMdPetRelTpProcessoUnidDTO->retStrsiglaUnidade();
        $objMdPetRelTpProcessoUnidDTO->retStrdescricaoUnidade();
        $objMdPetRelTpProcessoUnidDTO->retNumIdTipoProcessoPeticionamento();


        $objMdPetRelTpProcUnidadeRN = new MdPetRelTpProcessoUnidRN();
        $objMdPetRelTpProcUnidade = $objMdPetRelTpProcUnidadeRN->listar($objMdPetRelTpProcessoUnidDTO);
        $arrIdTipoProcessoPeticionamento = InfraArray::converterArrInfraDTO($objMdPetRelTpProcUnidade, 'IdTipoProcessoPeticionamento');
        $arrIdUnidade = InfraArray::converterArrInfraDTO($objMdPetRelTpProcUnidade, 'IdUnidade');

        foreach ($objMdPetRelTpProcUnidade as $key => $obj) {

            $arrayUnidade['idUnidade'] = $obj->getNumIdUnidade();
            $arrayUnidade['siglaUnidade'] = $obj->getStrsiglaUnidade();
            $arrayUnidade['descricaoUnidade'] = $obj->getStrdescricaoUnidade();

        }

        $objMdPetTipoProcessoDTO = new MdPetTipoProcessoDTO();
        $objMdPetTipoProcessoDTO->setNumIdTipoProcessoPeticionamento($arrIdTipoProcessoPeticionamento, InfraDTO::$OPER_IN);
        $objMdPetTipoProcessoDTO->retNumIdTipoProcessoPeticionamento();
        $objMdPetTipoProcessoDTO->retStrNomeProcesso();


        $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
        $objMdPetTipoProcesso = $objMdPetTipoProcessoRN->listar($objMdPetTipoProcessoDTO);

        foreach ($objMdPetTipoProcesso as $obj) {

            if (array_key_exists($obj->getNumIdTipoProcessoPeticionamento(), $arrayUnidade)) ;
            {

                if (!array_key_exists('NomeProcesso', $arrayUnidade)) {
                    $arrayUnidade['NomeProcesso'] = array();
                }
                array_push($arrayUnidade['NomeProcesso'], $obj->getStrNomeProcesso());

            }

        }


        $msg = "Não é permitido " . $acao . " esta Unidade, pois ela é utilizada pelo Módulo de Peticionamento e Intimação Eletrônicos.";
        $msg .= "Verifique as parametrizações no menu Administração > Peticionamento Eletrônico > Tipos para Peticionamento relativo aos Tipos de Processos:";
        $msg .= "\n\n* Tipo de processo:\n";

        foreach ($arrayUnidade['NomeProcesso'] as $tipoProcesso) {
            $msg .= "        - " . $tipoProcesso . "\n";
        }

        return $msg;

    }

    private function _retornaDocumentoPrincipalProcesso($objDTO)
    {
        $arrIdTipoProcessoPeticionamento = InfraArray::converterArrInfraDTO($objDTO, 'IdTipoProcessoPeticionamento');

        $objMdPetTipoProcessoDTO = new MdPetTipoProcessoDTO();
        $objMdPetTipoProcessoDTO->setNumIdTipoProcessoPeticionamento($arrIdTipoProcessoPeticionamento, InfraDTO::$OPER_IN);
        $objMdPetTipoProcessoDTO->retNumIdTipoProcessoPeticionamento();
        $objMdPetTipoProcessoDTO->retStrNomeProcesso();
        $objMdPetTipoProcessoDTO->retStrNomeSerie();
        $objMdPetTipoProcessoDTO->retNumIdSerie();


        $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
        $objMdPetTipoProcesso = $objMdPetTipoProcessoRN->listar($objMdPetTipoProcessoDTO);

        $arrTpProcesso = array();
        foreach ($objMdPetTipoProcesso as $tpProcesso) {
            if (!array_key_exists($tpProcesso->getNumIdTipoProcessoPeticionamento(), $tpProcesso)) {
                $arrProcesso[$tpProcesso->getNumIdTipoProcessoPeticionamento()] = array();
            }
            if (!array_key_exists($tpProcesso->getNumIdTipoProcessoPeticionamento(), $tpProcesso)) {
                $arrProcesso[$tpProcesso->getNumIdTipoProcessoPeticionamento()]['NomeProcesso'] = array();
            }
            if (!array_key_exists($tpProcesso->getNumIdTipoProcessoPeticionamento(), $tpProcesso)) {
                $arrProcesso[$tpProcesso->getNumIdTipoProcessoPeticionamento()]['Documento'] = array();
            }

            $arrTpProcesso[$tpProcesso->getNumIdTipoProcessoPeticionamento()]['NomeProcesso'] = $tpProcesso->getStrNomeProcesso();
            $arrTpProcesso[$tpProcesso->getNumIdTipoProcessoPeticionamento()]['Documento'] = $tpProcesso->getStrNomeSerie();
        }

        return $arrTpProcesso;

    }

    private function _retornaTipoDocEssenCompl($objDTO)
    {
        $arrIdTipoProcessoPeticionamento = InfraArray::converterArrInfraDTO($objDTO, 'IdTipoProcessoPeticionamento');
        $arrIdSerie = InfraArray::converterArrInfraDTO($objDTO, 'IdSerie');
        $arrMdPetRelTpProcSerie = array();

        $objMdPetTipoProcessoDTO = new MdPetTipoProcessoDTO();
        $objMdPetTipoProcessoDTO->setNumIdTipoProcessoPeticionamento($arrIdTipoProcessoPeticionamento, InfraDTO::$OPER_IN);
        $objMdPetTipoProcessoDTO->retNumIdTipoProcessoPeticionamento();
        $objMdPetTipoProcessoDTO->retStrNomeProcesso();

        $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
        $objMdPetTipoProcesso = $objMdPetTipoProcessoRN->listar($objMdPetTipoProcessoDTO);

        $arrProcesso = array();

        foreach ($objMdPetTipoProcesso as $objProcesso) {
            if (!array_key_exists($objProcesso->getNumIdTipoProcessoPeticionamento(), $arrProcesso)) {
                $arrProcesso[$objProcesso->getNumIdTipoProcessoPeticionamento()] = array();
            }
            if (!array_key_exists($objProcesso->getNumIdTipoProcessoPeticionamento(), $arrProcesso)) {
                $arrProcesso[$objProcesso->getNumIdTipoProcessoPeticionamento()]['NomeProcesso'] = array();
            }

            $arrProcesso[$objProcesso->getNumIdTipoProcessoPeticionamento()]['NomeProcesso'] = $objProcesso->getStrNomeProcesso();

        }

        $objMdPetRelTpProcSerieDTO = new MdPetRelTpProcSerieDTO();
        $objMdPetRelTpProcSerieDTO->setNumIdTipoProcessoPeticionamento($arrIdTipoProcessoPeticionamento, InfraDTO::$OPER_IN);
        $objMdPetRelTpProcSerieDTO->setNumIdSerie($arrIdSerie, InfraDTO::$OPER_IN);
        $objMdPetRelTpProcSerieDTO->retNumIdTipoProcessoPeticionamento();
        $objMdPetRelTpProcSerieDTO->retStrNomeSerie();

        $objMdPetRelTpProcSerieRn = new MdPetRelTpProcSerieRN();
        $objMdPetRelTpProcSerie = $objMdPetRelTpProcSerieRn->listar($objMdPetRelTpProcSerieDTO);

        foreach ($objMdPetRelTpProcSerie as $objProcSerie) {

            if (!array_key_exists('Documento', $arrProcesso[$objProcSerie->getNumIdTipoProcessoPeticionamento()])) {
                $arrProcesso[$objProcSerie->getNumIdTipoProcessoPeticionamento()]['Documento'] = array();
            }
            array_push($arrProcesso[$objProcSerie->getNumIdTipoProcessoPeticionamento()]['Documento'], $objProcSerie->getStrNomeSerie());

        }
        return $arrProcesso;

    }

    private function _retornaMsgTipoDocumento($arrObj, $acao)
    {

        $arrMdPetRelTpProcSerie = array();
        $msg = 'Não é permitido ' . $acao . ' este Tipo de Documento, pois ele é utilizado pelo Módulo de Peticionamento e Intimação ';
        $msg .= 'Eletrônicos. Verifique as parametrizações no menu Administração > Peticionamento Eletrônico > Tipos para ';
        $msg .= "Peticionamento relativo aos Tipos de Processos:\n";

        for ($i = 0; $i < count($arrObj); $i++) {

            $msg .= "\n * " . $arrObj[$i]['NomeProcesso'];
            if (!is_array($arrObj[$i]['Documento'])) {
                $msg .= "\n   - " . $arrObj[$i]['Documento'];
            } else {
                for ($cont = 0; $cont < count($arrObj[$i]['Documento']); $cont++) {
                    $msg .= "\n   - " . $arrObj[$i]['Documento'][$cont];
                }
            }


            $msg .= "\n";
        }

        return $msg;

    }

    protected function verificarExistenciaTipoDocumentoConectado($arrObjSerieAPI)
    {

        $arrSerieAPI = $arrObjSerieAPI[0];
        $acao = $arrObjSerieAPI[1];
        $msg = '';
        $arrIds = array();
        $arrDocumento = array();

        foreach ($arrSerieAPI as $objSerieAPI) {
            $arrIds[] = $objSerieAPI->getIdSerie();
        }

        // Verifica se existe Documento principal no processo
        $objMdPetTipoProcessoDTO = new MdPetTipoProcessoDTO();
        $objMdPetTipoProcessoDTO->setNumIdSerie($arrIds, InfraDTO::$OPER_IN);
        $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();

        if ($objMdPetTipoProcessoRN->contar($objMdPetTipoProcessoDTO) > 0) {

            $objMdPetTipoProcessoDTO->retNumIdSerie();
            $objMdPetTipoProcessoDTO->retNumIdTipoProcessoPeticionamento();
            $objMdPetTipoProcesso = $objMdPetTipoProcessoRN->listar($objMdPetTipoProcessoDTO);

            $objMdPetTipoProcesso = $this->_retornaDocumentoPrincipalProcesso($objMdPetTipoProcesso);
            $existeDocumento = true;

        }


        // Verifica se existe Tipos dos Documentos Essenciais ou Tipos dos Documentos Complementares
        $objMdPetRelTpProcSerieDTO = new MdPetRelTpProcSerieDTO();
        $objMdPetRelTpProcSerieDTO->setNumIdSerie($arrIds, InfraDTO::$OPER_IN);
        $objMdPetRelTpProcSerieRN = new MdPetRelTpProcSerieRN();

        if ($objMdPetRelTpProcSerieRN->contar($objMdPetRelTpProcSerieDTO) > 0) {

            $objMdPetRelTpProcSerieDTO->retNumIdSerie();
            $objMdPetRelTpProcSerieDTO->retNumIdTipoProcessoPeticionamento();
            $objMdPetRelTpProcSerie = $objMdPetRelTpProcSerieRN->listar($objMdPetRelTpProcSerieDTO);

            $objMdPetRelTpProcSerie = $this->_retornaTipoDocEssenCompl($objMdPetRelTpProcSerie);

            if (isset($objMdPetTipoProcesso) && is_array($objMdPetTipoProcesso)) {
                foreach ($objMdPetRelTpProcSerie as $key => $doc) {
                    if (array_key_exists($key, $objMdPetRelTpProcSerie)) {
                        array_push($doc['Documento'], $objMdPetTipoProcesso[$key]['Documento']);
                        array_push($arrDocumento, $doc);
                    }
                }

            } else {
                foreach ($objMdPetRelTpProcSerie as $key => $doc) {
                    array_push($arrDocumento, $doc);
                }
            }

            $existeDocumento = true;

        } else {
            if (isset($objMdPetTipoProcesso) && is_array($objMdPetTipoProcesso)) {
                foreach ($objMdPetTipoProcesso as $key => $doc) {
                    array_push($arrDocumento, $doc);
                }
            }
        }

        if ($existeDocumento) {
            $msg = $this->_retornaMsgTipoDocumento($arrDocumento, $acao);

        } else {

            $objMdPetIntSerieDTO = new MdPetIntSerieDTO();
            $objMdPetIntSerieDTO->setNumIdSerie($arrIds, InfraDTO::$OPER_IN);
            $objMdPetIntSerieRN = new MdPetIntSerieRN();

            $existeMdPetIntSerie = $objMdPetIntSerieRN->contar($objMdPetIntSerieDTO) > 0;
            $existeDocumento = $existeMdPetIntSerie;

            if ($existeDocumento) {
                $msg = "Não é permitido " . $acao . " este Tipo de Documento, pois ele é utilizado pelo Módulo de Peticionamento e Intimação ";
                $msg .= "Eletrônicos. Verifique as parametrizações no menu Administração > Peticionamento Eletrônico > Intimação Eletrônica > ";
                $msg .= "Tipos de Documentos para Intimação.";
            }
        }

        return $msg;

    }


    private function _verificaSituacaoRespondida($idsRelDest, $arrIdsRelDest){
        $objMdPetIntDestRespRN = new MdPetIntDestRespostaRN();

        $objMdPetIntDestRespDTO = new MdPetIntDestRespostaDTO();
        $objMdPetIntDestRespDTO->setNumIdMdPetIntRelDestinatario($idsRelDest, InfraDTO::$OPER_IN);
        $objMdPetIntDestRespDTO->retNumIdMdPetIntDestResposta();
        $objMdPetIntDestRespDTO->retNumIdMdPetIntRelDestinatario();

        $count = $objMdPetIntDestRespRN->contar($objMdPetIntDestRespDTO);

        if($count > 0)
        {
            $arrObjDTOResp = $objMdPetIntDestRespRN->listar($objMdPetIntDestRespDTO);

            foreach($arrObjDTOResp as $objDTO){
                $arrIdsRelDest[$objDTO->getNumIdMdPetIntRelDestinatario()] = MdPetIntimacaoRN::$INTIMACAO_RESPONDIDA;
            }
        }

        return $arrIdsRelDest;
    }

    /*
     * Realiza uma consulta verificando todas as situações das intimações, em cima dos ids enviados
     *
     */
    protected function retornaSituacoesIntimacoesConectado($arrDados){

        //Ids das situação (Vínculo de Intimação x Contato = MdPetIntRelDestinatario)
        $idsRelDest         = array_key_exists(0, $arrDados) ? $arrDados[0] : null;

        //Se for setado para TRUE irá retornar além das situações padrões, retornara uma situação para o prazo da resposta vencido.
        $addSitPrazoVencido = array_key_exists(1, $arrDados) ? $arrDados[1] : false;

        $arrIdsRelDest = array();

        if(!is_null($idsRelDest) && count($idsRelDest) > 0)
        {
            //Gera um array com o id do relacionamento do contato e intimação como Key
            $arrIdsRelDest = $this->_formatarArrayChaveIdRelDest($idsRelDest);

            //Verifica a primeira condicional-> Respondida
            $arrIdsRelDest = $this->_verificaSituacaoRespondida($idsRelDest, $arrIdsRelDest);

            // Remove os ids que já possuem situação identificada
            $idsRelDest = $this->_removerIdsSituacoesIdentificadas($idsRelDest, $arrIdsRelDest);

            // Verifica se Situacao é cumprida, prazo tácito, ou vencida
            $arrIdsRelDest = $this->_verificaSituacaoCumpridaEPrazoExterno($idsRelDest, $arrIdsRelDest, $addSitPrazoVencido);

        }

        //Retorna um array com todas as situações e como key o idRelDest da intimação
        return $arrIdsRelDest;
    }



    private function _verificaSituacaoCumpridaEPrazoExterno($idsRelDest, $arrIdsRelDest, $addSitPrazoVencido){
        if(count($idsRelDest) > 0) {
            $objMdPetIntAceiteDTO = new MdPetIntAceiteDTO();
            $objMdPetIntAceiteRN = new MdPetIntAceiteRN();

            $objMdPetIntAceiteDTO->setNumIdMdPetIntRelDestinatario($idsRelDest, InfraDTO::$OPER_IN);
            $objMdPetIntAceiteDTO->retNumIdMdPetIntRelDestinatario();
            $objMdPetIntAceiteDTO->retNumIdMdPetIntAceite();
            $objMdPetIntAceiteDTO->retStrTipoAceite();

            $count = $objMdPetIntAceiteRN->contar($objMdPetIntAceiteDTO);

            if ($count > 0) {
                $arrObjDTOAceite = $objMdPetIntAceiteRN->listar($objMdPetIntAceiteDTO);
                $idsIntimacoesCumpridas = array();

                foreach ($arrObjDTOAceite as $objDTO) {
                    $tipoAceite = $objDTO->getStrTipoAceite();
                    $acessoDireto = ($tipoAceite == MdPetIntimacaoRN::$TP_MANUAL_USUARIO_EXTERNO_ACEITE);
                    $arrIdsRelDest[$objDTO->getNumIdMdPetIntRelDestinatario()] = $acessoDireto ? MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_POR_ACESSO : MdPetIntimacaoRN::$INTIMACAO_CUMPRIDA_PRAZO;
                    array_push($idsIntimacoesCumpridas, $objDTO->getNumIdMdPetIntRelDestinatario());
                }
            }

            if ($addSitPrazoVencido) {
                $arrIdsRelDest = $this->_verificaSituacaoPrazoExternoVencido($idsIntimacoesCumpridas, $arrIdsRelDest);
            }
        }

        return $arrIdsRelDest;
    }

    private function _retornaObjPadraoIntRelTipoRespDest($idsIntimacoesCumpridas){
        $objMdPetIntRelRpDtDTO = new MdPetIntRelTipoRespDestDTO();
        $objMdPetIntRelRpDtDTO->setNumIdMdPetIntRelDest($idsIntimacoesCumpridas, InfraDTO::$OPER_IN);
        $objMdPetIntRelRpDtDTO->retNumIdMdPetIntRelDest();
        $objMdPetIntRelRpDtDTO->retDthDataProrrogada();
        $objMdPetIntRelRpDtDTO->retDthDataLimite();

        return $objMdPetIntRelRpDtDTO;
    }

    private function _verificaSituacaoPrazoExternoVencido($idsIntimacoesCumpridas, $arrIdsRelDest)
    {
        $objMdPetIntRelRpDtRN = new MdPetIntRelTipoRespDestRN();
        $arrControle          = array();
        $arrControleIntResp   = array();

        if(count($idsIntimacoesCumpridas) > 0)
        {
            //Verifica quais intimações devem possuir resposta
            $objMdPetIntRelRpDtDTO2 = $this->_retornaObjPadraoIntRelTipoRespDest($idsIntimacoesCumpridas);
            $arrDadosComResposta = $objMdPetIntRelRpDtRN->listar($objMdPetIntRelRpDtDTO2);
            $arrControleIntResp  = count($arrDadosComResposta) > 0 ? InfraArray::converterArrInfraDTO($arrDadosComResposta, 'IdMdPetIntRelDest') : array();
            $arrControleIntResp = array_unique($arrControleIntResp);

            $dtAtual = InfraData::getStrDataHoraAtual();
            $objMdPetIntRelRpDtRN = new MdPetIntRelTipoRespDestRN();
            $objMdPetIntRelRpDtDTO = $this->_retornaObjPadraoIntRelTipoRespDest($idsIntimacoesCumpridas);
            $objMdPetIntRelRpDtDTO->adicionarCriterio(array('DataProrrogada', 'DataLimite'),
                array(InfraDTO::$OPER_MAIOR, InfraDTO::$OPER_MAIOR),
                array($dtAtual, $dtAtual), array(InfraDTO::$OPER_LOGICO_OR));

            $count = $objMdPetIntRelRpDtRN->contar($objMdPetIntRelRpDtDTO);

            if ($count > 0)
            {
                $arrObjDTOPrazoVenc = $objMdPetIntRelRpDtRN->listar($objMdPetIntRelRpDtDTO);

                foreach ($arrObjDTOPrazoVenc as $objDTO)
                {
                    //Verifica se não possue a data solicitada
                    if(!array_key_exists($objDTO->getNumIdMdPetIntRelDest(), $arrControle))
                    {
                        $arrControle[$objDTO->getNumIdMdPetIntRelDest()] = 0;
                    }
                }
            }



            foreach($idsIntimacoesCumpridas as $idIntimacaoCumprida){

                $existeRespostaValida = array_key_exists($idIntimacaoCumprida, $arrControle);
                $precisaResposta      = in_array($idIntimacaoCumprida, $arrControleIntResp);

                if((!$existeRespostaValida) && $precisaResposta){

                    $arrIdsRelDest[$idIntimacaoCumprida] = MdPetIntimacaoRN::$INTIMACAO_PRAZO_VENCIDO;
                }
            }
        }

        return $arrIdsRelDest;
    }

    private function _removerIdsSituacoesIdentificadas($idsRelDest, $arrIdsRelDest){
        $ids = array();

        foreach($idsRelDest as $key => $id){
            //Condição para verificar se o id deve ser adicionado ou não
            $addSituacao = array_key_exists($id, $arrIdsRelDest) && $arrIdsRelDest[$id] == MdPetIntimacaoRN::$INTIMACAO_PENDENTE;
            if($addSituacao){
                array_push($ids, $id);
            }
        }

        return $ids;
    }

    private function _formatarArrayChaveIdRelDest($idsRelDest, $situacao = true){
        $arrRetorno = array();

        foreach($idsRelDest as $idRel){
            if($situacao) {
                $arrRetorno[$idRel] = MdPetIntimacaoRN::$INTIMACAO_PENDENTE;
            }else{
                $arrRetorno[$idRel] = 'N';
            }
        }

        return $arrRetorno;
    }

    protected function retornarArrIntimacaoAnexoConectado($idsRelDest)
    {
        $arrIdsRelDest = array();

        if (!is_null($idsRelDest) && count($idsRelDest) > 0) {
            //Gera um array com o id do relacionamento do contato e intimação como Key
            $arrIdsRelDest = $this->_formatarArrayChaveIdRelDest($idsRelDest, false);

            //Busca todas as intimações que possuem anexo
            $arrObjMdPetRelDestDTO = $this->_buscaIntimacoesComAnexo($idsRelDest);

            foreach ($arrIdsRelDest as $keyId => $idRelDest) {
                foreach ($arrObjMdPetRelDestDTO as $objDTO){
                    if($keyId == $objDTO->getNumIdMdPetIntRelDestinatario()){
                        $arrIdsRelDest[$keyId]= 'S';
                    }
                }
            }
        }

        return $arrIdsRelDest;

    }

    private function _buscaIntimacoesComAnexo($idsRelDest)
    {
        $objMdPetIntRelDestRN  = new MdPetIntRelDestinatarioRN();
        $objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
        $objMdPetIntRelDestDTO->setNumIdMdPetIntRelDestinatario($idsRelDest, InfraDTO::$OPER_IN);
        $objMdPetIntRelDestDTO->setStrSinPrincipalDoc('N');
        $objMdPetIntRelDestDTO->retNumIdMdPetIntRelDestinatario();

        $arrRetorno = $objMdPetIntRelDestRN->listar($objMdPetIntRelDestDTO);

        return $arrRetorno;
    }

    public function formatarArrSituacoes($arrSituacoes){

        $arrRetorno     = array();

        $arrIdsRelDest = array();
        foreach($arrSituacoes as $idRelDest => $idSituacaoIntimacao) {

            if (array_key_exists($idSituacaoIntimacao, $arrRetorno)) {
                $arrDados = $arrRetorno[$idSituacaoIntimacao];
                $arrDados[]= $idRelDest;
                $arrRetorno[$idSituacaoIntimacao] = $arrDados;
            } else {
                $arrRetorno[$idSituacaoIntimacao] = array($idRelDest);
            }
        }

        return $arrRetorno;
    }



}