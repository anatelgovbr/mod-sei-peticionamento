<?php

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 26/10/2017 - criado por jaqueline.cast
 *
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntAtualizarAcessoExternoRN extends InfraRN{

    //Id Tarefa Módulo
    public static $ID_PET_LIBERAR_ANDAMENTOS_CONCLUIDOS = 'LIBERAR_ACESSO_EXTERNO_INTIMACAO';

    public function __construct(){
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco(){
        return BancoSEI::getInstance();
    }

    protected function updateIntimacoesExistentesControlado()
    {
        try {
            $arrRetorno = array();
            $objMdPetIntAcExDocRN = new MdPetIntAcessoExternoDocumentoRN();
            $objMdPetIntRelDestRN = new MdPetIntRelDestinatarioRN();
            $objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
            $objMdPetIntRelDestDTO->retNumIdAcessoExterno();
            $objMdPetIntRelDestDTO->retNumIdContato();
            $objMdPetIntRelDestDTO->setOrdDtaValidadeAcessoExterno(InfraDTO::$TIPO_ORDENACAO_DESC);

            $arrObjDTO = $objMdPetIntRelDestRN->listar($objMdPetIntRelDestDTO);
            $idsAcExterno = InfraArray::converterArrInfraDTO($arrObjDTO, 'IdAcessoExterno');

            //Gera um array de acessos com seus tipos de acesso
            $arrAcessosExtTipos = $objMdPetIntAcExDocRN->getTipoAcessoExternoPorAcessoExterno($idsAcExterno);

            if (count($arrAcessosExtTipos) > 0) {
                //Gera um array com os contatos_procedimento e seus respectivos acessos externos
                $arrUserAcessoExt = $this->_getAcessosExternosComUsuariosIntimacao();

                //Realiza as condicionais para manter os acessos externos por contato prioritarios
                $arrIdsUserAcExtPrior = $this->_retornarArrAcessosExternosPrioritarios($arrUserAcessoExt, $arrAcessosExtTipos);

                //Formata Array, removendo os prioritarios, com a intenção de verificar quais acessos devem ser cancelados
                $arrIdsAcessosExternosCancelar = $this->_retornaArrIdsAcessoExternoCancelar($arrUserAcessoExt, $arrIdsUserAcExtPrior);


                //Formata um array, verificando quais usuarios possuem o tipo de acesso parcial
                $arrIdsUsComAcessoExtParcial = $this->_retornaArrIdsUsuarioUnidadeAcessoParcial($arrIdsUserAcExtPrior, $arrAcessosExtTipos);

                //Busca todos os documentos que estão vinculado aos usuários que terão acessos parciais cancelados
                $arrUsuarioDocVinculados = $this->_buscarDocumentosAcessosExternosParciaisCancelados($arrIdsUsComAcessoExtParcial, $arrIdsAcessosExternosCancelar, $arrUserAcessoExt);

                //Adiciona para os acessos parciais prioritarios os documentos que estavam vinculados aos acessos externos que serão cancelados
                $this->_inserirNovosRelacionamentosAcessoParcial($arrUsuarioDocVinculados, $arrIdsUserAcExtPrior);

                //Atualiza as antiga intimações com  o novo id de acesso externo
                if (count($arrIdsAcessosExternosCancelar) > 0)
                {
                    $this->_atualizarAntigasIntimacoes($arrIdsUserAcExtPrior);
                }

                //Cancela os acessos externos que não serão utilizados
                $this->_cancelarAcessosExternosNaoUtilizados($arrIdsAcessosExternosCancelar);
            }

        }catch(Exception $e){
            throw new InfraException('Não foi possível atualizar os acessos externos para as novas regras.',$e);
        }
    }

    private function _getAcessosExternosComUsuariosIntimacao()
    {
        $arrRetorno = array();
        $objMdPetIntRelDestRN  = new MdPetIntRelDestinatarioRN();
        $objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
        $objMdPetIntRelDestDTO->retNumIdContato();
        $objMdPetIntRelDestDTO->retNumIdAcessoExterno();
        $objMdPetIntRelDestDTO->retNumIdMdPetIntRelDestinatario();
        $objMdPetIntRelDestDTO->retNumIdAtividadeAcessoExterno();
        $objMdPetIntRelDestDTO->retNumIdUnidadeAcessoExterno();
        $objMdPetIntRelDestDTO->retDblIdProcedimento();
        $objMdPetIntRelDestDTO->setProcedimentoDocTIPOFK(InfraDTO::$TIPO_FK_OBRIGATORIA);

        $count = $objMdPetIntRelDestRN->contar($objMdPetIntRelDestDTO);
        if ($count > 0) {
            $arrObjDTO = $objMdPetIntRelDestRN->listar($objMdPetIntRelDestDTO);

            foreach ($arrObjDTO as $key => $objDTO)
            {
                $idProcedimento = $objDTO->getDblIdProcedimento();
                $chave  = $objDTO->getNumIdContato().'_'.$idProcedimento;
                $arrRetorno[$chave][$objDTO->getNumIdAcessoExterno()] = $objDTO->getNumIdMdPetIntRelDestinatario();
            }
        }

        return $arrRetorno;
    }

    private function _retornarArrAcessosExternosPrioritarios($arrUserAcessoExt, $arrAcessosExtTipos)
    {
        $arrAcessoExtPrioritario  = array();

        foreach($arrUserAcessoExt as $idUserProcesso=> $arrAcessosUsuario)
        {
            $idAcessoExtPrioritario = $this->_retornaIdAcessoExternoPrioritario($arrAcessosUsuario, $arrAcessosExtTipos);
            $arrAcessoExtPrioritario[$idUserProcesso] = $idAcessoExtPrioritario;
        }

        return $arrAcessoExtPrioritario;
    }

    private function _retornaIdAcessoExternoPrioritario($arrAcessosUsuario, $arrAcessosExtTipos)
    {
        $idAcessoExtPrincipal  = '';

        foreach($arrAcessosUsuario as $idAcessoExterno => $idRelDest){

            $tipoAcessoAtual    = array_key_exists($idAcessoExterno, $arrAcessosExtTipos) ? $arrAcessosExtTipos[$idAcessoExterno] : MdPetIntAcessoExternoDocumentoRN::$NAO_POSSUI_ACESSO;
            $tipoAcessoAnterior = $idAcessoExtPrincipal != '' && array_key_exists($idAcessoExtPrincipal, $arrAcessosExtTipos) ? $arrAcessosExtTipos[$idAcessoExtPrincipal] : MdPetIntAcessoExternoDocumentoRN::$NAO_POSSUI_ACESSO;
            $anteriorIsParcial  = $tipoAcessoAnterior == MdPetIntAcessoExternoDocumentoRN::$ACESSO_PARCIAL;
            $atualIsIntegral    = $tipoAcessoAtual == MdPetIntAcessoExternoDocumentoRN::$ACESSO_INTEGRAL;

            if ($idAcessoExtPrincipal == '')
            {
                $idAcessoExtPrincipal = $idAcessoExterno;
            } else if ($anteriorIsParcial && $atualIsIntegral)
            {
                $idAcessoExtPrincipal = $idAcessoExterno;
            }
        }

        return $idAcessoExtPrincipal;
    }

    private function _retornaArrIdsAcessoExternoCancelar($arrUserAcessoExt, $arrIdsUserAcExtPrior){
        $arrAcessosExternosCancelar = array();

        foreach($arrUserAcessoExt as $key=> $arrAcessosUsuario)
        {
            $arr =  $this->_retornaArrIdsCancelarPorUsuario($arrAcessosUsuario, $arrIdsUserAcExtPrior);
            if(!empty($arr)){
                $arrAcessosExternosCancelar[$key] = $arr;
            }
        }

        return $arrAcessosExternosCancelar;
    }

    private function _retornaArrIdsCancelarPorUsuario($arrAcessosUsuario, $arrIdsUserAcExtPrior)
    {
        $arrRetorno = array();
        foreach($arrAcessosUsuario as $idAcessoExterno => $idRelDest)
        {
            if(!in_array($idAcessoExterno, $arrIdsUserAcExtPrior))
            {
                array_push($arrRetorno, $idAcessoExterno);
            }
        }

        return $arrRetorno;
    }

    private function _retornaArrIdsUsuarioUnidadeAcessoParcial($arrIdsUserAcExtPrior, $arrAcessosExtTipos){
        $arrAcessoExtParcial = array();
        foreach($arrIdsUserAcExtPrior as $idUsuIdUnIdProcesso => $idAcessoExternoPrioritario){
            $tipoAcesso  = array_key_exists($idAcessoExternoPrioritario, $arrAcessosExtTipos) ? $arrAcessosExtTipos[$idAcessoExternoPrioritario] : MdPetIntAcessoExternoDocumentoRN::$NAO_POSSUI_ACESSO;

            if($tipoAcesso == MdPetIntAcessoExternoDocumentoRN::$ACESSO_PARCIAL){
                $arrAcessoExtParcial[$idUsuIdUnIdProcesso] = $idAcessoExternoPrioritario;
            }
        }

        return $arrAcessoExtParcial;
    }


    private function _buscarDocumentosAcessosExternosParciaisCancelados($arrIdsUsComAcessoExtParcial, $arrIdsAcessosExternosCancelar, $arrUserAcessoExt)
    {

        $objAcessoExternoRN = new AcessoExternoRN();
        $arrRetorno = array();
        $idsAcessosExternosCancelar = $this->_formatarArrayRetornoAcessosExternosCancelar($arrIdsAcessosExternosCancelar);

        if (count($idsAcessosExternosCancelar) > 0)
        {
            foreach ($arrIdsUsComAcessoExtParcial as $idContIdProcesso => $idAcessoExternoPrioritario)
            {
                $arrIds = explode('_', $idContIdProcesso);
                if (count($arrIds) > 0) {
                    $idContato = array_key_exists(0, $arrIds) ? $arrIds[0] : null;
                    $idProcesso = array_key_exists(1, $arrIds) ? $arrIds[1] : null;

                    if (!is_null($idContato) && !is_null($idProcesso) && count($idsAcessosExternosCancelar) > 0) {
                        $objAcessoExternoDTO = new AcessoExternoDTO();
                        $objAcessoExternoDTO->setNumIdAcessoExterno($idsAcessosExternosCancelar, InfraDTO::$OPER_IN);
                        $objAcessoExternoDTO->retNumIdAcessoExterno();
                        $objAcessoExternoDTO->setNumIdContatoParticipante($idContato);
                        $objAcessoExternoDTO->setDblIdProtocoloAtividade($idProcesso);

                        $count = $objAcessoExternoRN->contar($objAcessoExternoDTO);

                        if ($count > 0) {
                            $arrObjDTOAcessoExt = $objAcessoExternoRN->listar($objAcessoExternoDTO);

                            $idAcessoExtUnidContProc = InfraArray::converterArrInfraDTO($arrObjDTOAcessoExt, 'IdAcessoExterno');

                            $objRN = new RelAcessoExtProtocoloRN();
                            $objRelProtocoloDTO = new RelAcessoExtProtocoloDTO();
                            $objRelProtocoloDTO->setNumIdAcessoExterno($idAcessoExtUnidContProc, InfraDTO::$OPER_IN);
                            $objRelProtocoloDTO->retDblIdProtocolo();
                            $arrObjDTO = $objRN->listar($objRelProtocoloDTO);

                            foreach ($arrObjDTO as $objDTO) {
                                $arrRetorno[$idContIdProcesso][$objDTO->getDblIdProtocolo()] = $idAcessoExternoPrioritario;
                            }
                        }
                    }

                }
            }
        }

        return $arrRetorno;
    }


    private function _formatarArrayRetornoAcessosExternosCancelar($arrIdsAcessosExternosCancelar){
        $arrRetorno = array();
        foreach($arrIdsAcessosExternosCancelar as $usuario => $arrAcessoExt){
            //Função feita por passagem de parametro por valor.
            $this->_retornaIdsAcessoExt($arrAcessoExt, $arrRetorno);
        }

        return $arrRetorno;
    }

    
    private function _retornaIdsAcessoExt($idsAcessoExt, &$arrRetorno){

        foreach($idsAcessoExt as $key => $idAcessoEx){
            $arrRetorno[] = $idAcessoEx;
        }
    }

    private function _cancelarAcessosExternosNaoUtilizados($arrIdsAcessosExternosCancelar)
    {
        $objAcessoExternoRN = new AcessoExternoRN();
        $arrObjsCancelar    = array();


        if(count($arrIdsAcessosExternosCancelar) > 0)
        {
            $idUnidadeLogada     = SessaoSEI::getInstance()->getNumIdUnidadeAtual();
            $idUsuarioLogado     = SessaoSEI::getInstance()->getNumIdUsuario();

            foreach ($arrIdsAcessosExternosCancelar as $idContUnidadeProcesso => $idsAcessoExterno) {

                if(count($idsAcessoExterno) > 0){
                    $objAcessoExternoDTO = new AcessoExternoDTO();
                    $objAcessoExternoDTO->setNumIdAcessoExterno($idsAcessoExterno, InfraDTO::$OPER_IN);
                    $objAcessoExternoDTO->retNumIdAcessoExterno();

                    $count = $objAcessoExternoRN->contar($objAcessoExternoDTO);
                    $arrObjAcessosExternosDTO = $objAcessoExternoRN->listar($objAcessoExternoDTO);

                    if ($count > 0) {
                        $arrParams = array($arrObjAcessosExternosDTO, 'Cancelado automaticamente em razão de evolução para unificação de Acessos Externos concedidos em Intimações Eletrônicas para o mesmo Destinatário e Processo.');
                        $this->_cancelarAcessosExternos($arrParams);
                    }
                }
            }


           $this->_atualizarSessaoSEI(true, $idUsuarioLogado, $idUnidadeLogada);
        }


    }


    private function _inserirNovosRelacionamentosAcessoParcial($arrUsuarioDocVinculados, $arrIdsUserAcExtPrior)
    {
        $objAcessoExternoRN = new AcessoExternoRN();
        if (count($arrUsuarioDocVinculados) > 0) {
            $objRelProtocoloRN = new RelAcessoExtProtocoloRN();
            foreach ($arrUsuarioDocVinculados as $idContatoUnidProcesso => $docsVinculados)
            {
                //Dentro dos acessos externos escolhidos, filtrando por usuario, para trazer o acesso externo que deve permanecer

                foreach($docsVinculados as $idDoc => $idAcessoExtPr)
                {
                    $objRelProtocoloDTO = new RelAcessoExtProtocoloDTO();
                    $objRelProtocoloDTO->setNumIdAcessoExterno($idAcessoExtPr);
                    $objRelProtocoloDTO->setDblIdProtocolo($idDoc);
                    $objRelProtocoloDTO->setNumMaxRegistrosRetorno(1);
                    $objRelProtocoloDTO->retDblIdProtocolo();
                    $objDTO = $objRelProtocoloRN->consultar($objRelProtocoloDTO);

                    if(is_null($objDTO)){
                        $objRelProtocoloRN->cadastrar($objRelProtocoloDTO);
                    }
                }
            }
        }
    }


    private function _atualizarAntigasIntimacoes($arrIdsUserAcExtPrior)
    {
        $objMdPetIntRelDestRN = new MdPetIntRelDestinatarioRN();
        if (count($arrIdsUserAcExtPrior) > 0) {
            foreach ($arrIdsUserAcExtPrior as $idContatoProcesso => $idAcessoExterno) {
                $arrIds = explode('_', $idContatoProcesso);
                $idContato = array_key_exists(0, $arrIds) ? $arrIds[0] : null;
                $idProcesso = array_key_exists(1, $arrIds) ? $arrIds[1] : null;

                if (!is_null($idContato) && !is_null($idProcesso)) {

                    $objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
                    $objMdPetIntRelDestDTO->setNumIdContato($idContato);
                    $objMdPetIntRelDestDTO->setDblIdProtocoloProcedimento($idProcesso);
                    $objMdPetIntRelDestDTO->retNumIdMdPetIntRelDestinatario();
                    $objMdPetIntRelDestDTO->retNumIdMdPetTipoIntimacao();
                    $objMdPetIntRelDestDTO->retDblIdDocumento();
                    $objMdPetIntRelDestDTO->retDblIdProtocolo();
                    $objMdPetIntRelDestDTO->setProcedimentoDocTIPOFK(InfraDTO::$TIPO_FK_OBRIGATORIA);

                    $count = $objMdPetIntRelDestRN->contar($objMdPetIntRelDestDTO);
                    $arrListaDTO = $objMdPetIntRelDestRN->listar($objMdPetIntRelDestDTO);

                    foreach ($arrListaDTO as $objDTO) {

                        $objDTO->setNumIdAcessoExterno($idAcessoExterno);
                        $objMdPetIntRelDestRN->alterar($objDTO);
                    }
                }
            }
        }
    }


    private function _cancelarAcessosExternos($arrParams)
    {
        //Set Var para liberar os andamentos
        $_SESSION[static::$ID_PET_LIBERAR_ANDAMENTOS_CONCLUIDOS] = true;

        $objInfraParametro   = new InfraParametro(BancoSEI::getInstance());
        $objAcessoExternoRN  = new AcessoExternoRN();
        $arrObjAcessosExtDTO = array_key_exists('0', $arrParams) ? $arrParams[0] : null;
        $motivoCancelamento  = array_key_exists('1', $arrParams) ? $arrParams[1] : null;
        $idUsuarioModulo     = $objInfraParametro->getValor(MdPetContatoRN::$STR_INFRA_PARAMETRO_SIGLA_CONTATO, false);
        $idUnidadeLogada     = SessaoSEI::getInstance()->getNumIdUnidadeAtual();
        $idUsuarioLogada     = SessaoSEI::getInstance()->getNumIdUsuario();

            if (!is_null($arrObjAcessosExtDTO) ** (!is_null($motivoCancelamento)))
            {
                foreach ($arrObjAcessosExtDTO as $objDTO) {
                    $objAcessoExternoDTO = new AcessoExternoDTO();
                    $objAcessoExternoDTO->setNumIdAcessoExterno($objDTO->getNumIdAcessoExterno());
                    $objAcessoExternoDTO->setStrMotivo($motivoCancelamento);
                    $arrObjsCancelar[] = $objAcessoExternoDTO;
                }
            }


      $this->_atualizarSessaoSEI(false, $idUsuarioModulo, $idUnidadeLogada);
      $this->_cancelarDisponibilizacaoAcessoExterno($arrObjsCancelar);


        //Atualiza para a sessao Antiga
        $this->_atualizarSessaoSEI(true, $idUsuarioLogada, $idUnidadeLogada);
        //Remove var que tem o intuito de controlar o acesso dos andamentos concluidos
        unset($_SESSION[static::$ID_PET_LIBERAR_ANDAMENTOS_CONCLUIDOS]);
    }

    private function _atualizarSessaoSEI($bolHabilitada = true, $idUsuario = null, $idUnidade = null){
        $idUnidadeLogada     = SessaoSEI::getInstance()->getNumIdUnidadeAtual();
        $idUsuarioLogado     = SessaoSEI::getInstance()->getNumIdUsuario();
        SessaoSEI::getInstance()->setBolHabilitada($bolHabilitada);

        $idUsuario = is_null($idUsuario) ? $idUsuarioLogado : $idUsuario;
        $idUnidade = is_null($idUnidade) ? $idUnidadeLogada : $idUnidade;

        SessaoSEI::getInstance()->simularLogin(null, null, $idUsuario, $idUnidade);
    }


    private function _cancelarDisponibilizacaoAcessoExterno($parArrObjAcessoExternoDTO){
        try {
            global $SEI_MODULOS;

            $objAcessoExternoRN = new AcessoExternoRN();
            $objInfraException = new InfraException();

            $objAcessoExternoDTO = new AcessoExternoDTO();
            $objAcessoExternoDTO->setBolExclusaoLogica(false);
            $objAcessoExternoDTO->retNumIdAcessoExterno();
            $objAcessoExternoDTO->retNumIdAtividade();
            $objAcessoExternoDTO->retDblIdProtocoloAtividade();
            $objAcessoExternoDTO->retNumIdTarefaAtividade();
            $objAcessoExternoDTO->retNumIdUnidadeAtividade();
            $objAcessoExternoDTO->retNumIdContatoParticipante();
            $objAcessoExternoDTO->retStrNomeContato();
            $objAcessoExternoDTO->retStrStaTipo();
            $objAcessoExternoDTO->retDblIdDocumento();
            $objAcessoExternoDTO->retStrProtocoloDocumentoFormatado();

            $objAcessoExternoDTO->setNumIdAcessoExterno(InfraArray::converterArrInfraDTO($parArrObjAcessoExternoDTO, 'IdAcessoExterno'), InfraDTO::$OPER_IN);

            $arrObjAcessoExternoDTO = InfraArray::indexarArrInfraDTO($objAcessoExternoRN->listar($objAcessoExternoDTO), 'IdAcessoExterno');


            foreach ($parArrObjAcessoExternoDTO as $parObjAcessoExternoDTO) {

                $objAcessoExternoDTO = $arrObjAcessoExternoDTO[$parObjAcessoExternoDTO->getNumIdAcessoExterno()];

                if ($objAcessoExternoDTO == null) {
                    throw new InfraException('Registro de acesso externo ['.$parObjAcessoExternoDTO->getNumIdAcessoExterno().'] não encontrado.');
                }

                $objAcessoExternoDTO->setStrMotivo($parObjAcessoExternoDTO->getStrMotivo());

                if ($objAcessoExternoDTO->getStrStaTipo() != AcessoExternoRN::$TA_INTERESSADO &&
                    $objAcessoExternoDTO->getStrStaTipo() != AcessoExternoRN::$TA_DESTINATARIO_ISOLADO &&
                    $objAcessoExternoDTO->getStrStaTipo() != AcessoExternoRN::$TA_USUARIO_EXTERNO
                ) {
                    $objInfraException->adicionarValidacao('Registro ['.$objAcessoExternoDTO->getNumIdAcessoExterno().'] não é uma Disponibilização de Acesso Externo.');
                }

                if ($objAcessoExternoDTO->getNumIdTarefaAtividade() == TarefaRN::$TI_LIBERACAO_ACESSO_EXTERNO_CANCELADA) {
                    $objInfraException->adicionarValidacao('Disponibilização de acesso externo para "'.$objAcessoExternoDTO->getStrNomeContato().'" já consta como cancelada.');
                } else if ($objAcessoExternoDTO->getNumIdTarefaAtividade() != TarefaRN::$TI_LIBERACAO_ACESSO_EXTERNO) {
                    $objInfraException->adicionarValidacao('Andamento do processo ['.$objAcessoExternoDTO->getNumIdTarefaAtividade().'] não é uma Disponibilização de Acesso Externo.');
                }

            }
            $objInfraException->lancarValidacoes();


            $strDataHoraAtual = InfraData::getStrDataHoraAtual();

            $objAtividadeRN = new AtividadeRN();
            $objAtributoAndamentoRN = new AtributoAndamentoRN();
            $objAcessoExternoBD = new AcessoExternoBD($this->getObjInfraIBanco());
            $arrObjAcessoExternoAPI = array();
            foreach ($arrObjAcessoExternoDTO as $objAcessoExternoDTO) {

                $objAtributoAndamentoDTO = new AtributoAndamentoDTO();
                $objAtributoAndamentoDTO->retStrNome();
                $objAtributoAndamentoDTO->retStrValor();
                $objAtributoAndamentoDTO->retStrIdOrigem();
                $objAtributoAndamentoDTO->setNumIdAtividade($objAcessoExternoDTO->getNumIdAtividade());

                $arrObjAtributoAndamentoDTO = $objAtributoAndamentoRN->listarRN1367($objAtributoAndamentoDTO);

                foreach ($arrObjAtributoAndamentoDTO as $objAtributoAndamentoDTO) {
                    if ($objAtributoAndamentoDTO->getStrNome() == 'MOTIVO') {
                        $objAtributoAndamentoDTO->setStrValor($objAcessoExternoDTO->getStrMotivo());
                        break;
                    }
                }

                //lança andamento para o usuário atual registrando o cancelamento da liberação
                $objAtividadeDTO = new AtividadeDTO();
                $objAtividadeDTO->setDblIdProtocolo($objAcessoExternoDTO->getDblIdProtocoloAtividade());
                $objAtividadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
                $objAtividadeDTO->setNumIdUnidadeOrigem(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
                $objAtividadeDTO->setNumIdUsuario(null);
                $objAtividadeDTO->setNumIdUsuarioOrigem(SessaoSEI::getInstance()->getNumIdUsuario());
                $objAtividadeDTO->setDtaPrazo(null);

                $objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO);

                $objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_CANCELAMENTO_LIBERACAO_ACESSO_EXTERNO);

                $ret = $objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);

                //altera andamento original de concessão ou transferência
                $objAtividadeDTO = new AtividadeDTO();

                $objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_LIBERACAO_ACESSO_EXTERNO_CANCELADA);

                $objAtividadeDTO->setNumIdAtividade($objAcessoExternoDTO->getNumIdAtividade());
                $this->_excluirTarefaAnterior($objAcessoExternoDTO->getNumIdAtividade());
                $objAtividadeRN->mudarTarefa($objAtividadeDTO);

                $objAtributoAndamentoDTO = new AtributoAndamentoDTO();
                $objAtributoAndamentoDTO->setStrNome('USUARIO');
                $objAtributoAndamentoDTO->setStrValor(SessaoSEI::getInstance()->getStrSiglaUsuario().'¥'.SessaoSEI::getInstance()->getStrNomeUsuario());
                $objAtributoAndamentoDTO->setStrIdOrigem(SessaoSEI::getInstance()->getNumIdUsuario());
                $objAtributoAndamentoDTO->setNumIdAtividade($objAcessoExternoDTO->getNumIdAtividade());
                $objAtributoAndamentoRN->cadastrarRN1363($objAtributoAndamentoDTO);

                $objAtributoAndamentoDTO = new AtributoAndamentoDTO();
                $objAtributoAndamentoDTO->setStrNome('DATA_HORA');
                $objAtributoAndamentoDTO->setStrValor($strDataHoraAtual);
                $objAtributoAndamentoDTO->setStrIdOrigem($ret->getNumIdAtividade()); //relaciona com o andamento de cassação
                $objAtributoAndamentoDTO->setNumIdAtividade($objAcessoExternoDTO->getNumIdAtividade());
                $objAtributoAndamentoRN->cadastrarRN1363($objAtributoAndamentoDTO);

                $objAcessoExternoBD->desativar($objAcessoExternoDTO);

                $objAcessoExternoAPI = new AcessoExternoAPI();
                $objAcessoExternoAPI->setIdAcessoExterno($objAcessoExternoDTO->getNumIdAcessoExterno());
                $objAcessoExternoAPI->setProcedimento($objAcessoExternoDTO->getDblIdProtocoloAtividade());
                $arrObjAcessoExternoAPI[] = $objAcessoExternoAPI;
            }

            foreach ($SEI_MODULOS as $seiModulo) {
                $seiModulo->executar('cancelarDisponibilizacaoAcessoExterno', $arrObjAcessoExternoAPI);
            }


        } catch (Exception $e) {
            throw new InfraException('Erro cancelando disponibilização de acesso externo.', $e);
        }
    }

    private function _excluirTarefaAnterior($idAtividade){
        if ($idAtividade) {
            $objAtributoAndamentoDTO = new AtributoAndamentoDTO();
            $objAtributoAndamentoRN = new AtributoAndamentoRN();
            $objAtributoAndamentoDTO->retNumIdAtributoAndamento();
            $objAtributoAndamentoDTO->setNumIdAtividade($idAtividade);
            $objAtributoAndamentoDTO->setStrNome('USUARIO');
            $objAtributoAndamentoDTO->setNumMaxRegistrosRetorno(1);
            $objAtributoAndamentoDTO = $objAtributoAndamentoRN->consultarRN1366($objAtributoAndamentoDTO);
            if (!is_null($objAtributoAndamentoDTO)) {
                $objAtributoAndamentoRN->excluirRN1365(array($objAtributoAndamentoDTO));
            }
        }
    }




    
    












}