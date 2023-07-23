<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
 *
 * 31/01/2008 - criado por marcio_db
 *
 * Vers�o do Gerador de C�digo: 1.13.1
 *
 * Vers�o no CVS: $Id$
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntercorrenteAndamentoSigilosoRN extends InfraRN
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    protected function consultarProcedimentoConectado(EntradaConsultarProcedimentoAPI $objEntradaConsultarProcedimentoAPI){

        try{
            $objProcedimentoDTO = new ProcedimentoDTO();
            $objProcedimentoDTO->retDblIdProcedimento();
            $objProcedimentoDTO->retNumIdTipoProcedimento();
            $objProcedimentoDTO->retStrNomeTipoProcedimento();
            $objProcedimentoDTO->retStrStaNivelAcessoGlobalProtocolo();
            $objProcedimentoDTO->retStrProtocoloProcedimentoFormatado();
            $objProcedimentoDTO->retStrDescricaoProtocolo();
            $objProcedimentoDTO->retDtaGeracaoProtocolo();
            $objProcedimentoDTO->setDblIdProcedimento($objEntradaConsultarProcedimentoAPI->getIdProcedimento());

            $objProcedimentoRN = new ProcedimentoRN();
            $objProcedimentoDTO = $objProcedimentoRN->consultarRN0201($objProcedimentoDTO);

            $objProcedimentoHistoricoDTO = new ProcedimentoHistoricoDTO();
            $objProcedimentoHistoricoDTO->setDblIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
            $objProcedimentoHistoricoDTO->setStrStaHistorico(ProcedimentoRN::$TH_RESUMIDO);
            $objProcedimentoHistoricoDTO->setStrSinGerarLinksHistorico('N');
            $objProcedimentoHistoricoDTO->setNumMaxRegistrosRetorno(1);
            $objProcedimentoDTOHistorico = $this->consultarHistoricoRN1025($objProcedimentoHistoricoDTO);
            $arrObjAtividadeDTOHistorico = $objProcedimentoDTOHistorico->getArrObjAtividadeDTO();
            $objAtividadeDTO = $arrObjAtividadeDTOHistorico[0];

            if ($objAtividadeDTO!=null) {
                $objAndamentoAPIUltimo = new AndamentoAPI();
                $objAndamentoAPIUltimo->setDescricao($objAtividadeDTO->getStrNomeTarefa());
                $objAndamentoAPIUltimo->setDataHora($objAtividadeDTO->getDthAbertura());

                $objUsuarioAPI = new UsuarioAPI();
                $objUsuarioAPI->setIdUsuario($objAtividadeDTO->getNumIdUsuarioOrigem());
                $objUsuarioAPI->setSigla($objAtividadeDTO->getStrSiglaUsuarioOrigem());
                $objUsuarioAPI->setNome($objAtividadeDTO->getStrNomeUsuarioOrigem());
                $objAndamentoAPIUltimo->setUsuario($objUsuarioAPI);

                $objUnidadeAPI = new UnidadeAPI();
                $objUnidadeAPI->setIdUnidade($objAtividadeDTO->getNumIdUnidade());
                $objUnidadeAPI->setSigla($objAtividadeDTO->getStrSiglaUnidade());
                $objUnidadeAPI->setDescricao($objAtividadeDTO->getStrDescricaoUnidade());
                $objAndamentoAPIUltimo->setUnidade($objUnidadeAPI);
            }
            $objSaidaConsultarProcedimentoAPI = new SaidaConsultarProcedimentoAPI();
            $objSaidaConsultarProcedimentoAPI->setUltimoAndamento($objAndamentoAPIUltimo);

            return $objSaidaConsultarProcedimentoAPI;
        }catch(Exception $e){
            throw new InfraException('Erro processando consulta de processo.',$e);
        }
    }

    protected function consultarHistoricoRN1025Conectado(ProcedimentoHistoricoDTO $parObjProcedimentoHistoricoDTO)
    {
        try {

            //Valida Permissao
  //trecho comentado porque a funcao � acessada por usuario externo          //SessaoSEI::getInstance()->validarAuditarPermissao('procedimento_consultar_historico', __METHOD__, $parObjProcedimentoHistoricoDTO);

            //Regras de Negocio
            if (!$parObjProcedimentoHistoricoDTO->isSetStrSinGerarLinksHistorico()) {
                $parObjProcedimentoHistoricoDTO->setStrSinGerarLinksHistorico('S');
            }

            if (!$parObjProcedimentoHistoricoDTO->isSetStrSinRetornarAtributos()) {
                $parObjProcedimentoHistoricoDTO->setStrSinRetornarAtributos('N');
            }


            $objPesquisaProtocoloDTO = new PesquisaProtocoloDTO();
            $objPesquisaProtocoloDTO->setStrStaTipo(ProtocoloRN::$TPP_PROCEDIMENTOS);
            $objPesquisaProtocoloDTO->setStrStaAcesso(ProtocoloRN::$TAP_TODOS);
            $objPesquisaProtocoloDTO->setDblIdProtocolo($parObjProcedimentoHistoricoDTO->getDblIdProcedimento());

            $objProtocoloRN = new ProtocoloRN();
            $arrObjProtocoloDTO = $objProtocoloRN->pesquisarRN0967($objPesquisaProtocoloDTO);

            if (count($arrObjProtocoloDTO) == 0) {
                throw new InfraException('Processo n�o encontrado.', null, null, false);
            }

            $objProtocoloDTO = $arrObjProtocoloDTO[0];
            /*
            if ($objProtocoloDTO->getStrStaNivelAcessoGlobal()==ProtocoloRN::$NA_SIGILOSO && $objProtocoloDTO->getNumCodigoAcesso()<0 && $parObjProcedimentoHistoricoDTO->getStrStaHistorico()!=ProcedimentoRN::$TH_EXTERNO){
              throw new InfraException('Processo n�o encontrado para exibi��o do hist�rico.');
            }
            */

            $objProcedimentoDTO = new ProcedimentoDTO();
            $objProcedimentoDTO->setDblIdProcedimento($objProtocoloDTO->getDblIdProtocolo());
            $objProcedimentoDTO->setNumIdUnidadeGeradoraProtocolo($objProtocoloDTO->getNumIdUnidadeGeradora());
            $objProcedimentoDTO->setStrProtocoloProcedimentoFormatado($objProtocoloDTO->getStrProtocoloFormatado());
            $objProcedimentoDTO->setDtaGeracaoProtocolo($objProtocoloDTO->getDtaGeracao());
            $objProcedimentoDTO->setStrSiglaUnidadeGeradoraProtocolo($objProtocoloDTO->getStrSiglaUnidadeGeradora());
            $objProcedimentoDTO->setStrStaNivelAcessoGlobalProtocolo($objProtocoloDTO->getStrStaNivelAcessoGlobal());

            $objAtividadeDTO = new AtividadeDTO();
            $objAtividadeDTO->retNumIdAtividade();
            $objAtividadeDTO->retDblIdProtocolo();
            $objAtividadeDTO->retNumIdUnidade();
            $objAtividadeDTO->retNumIdUsuario();
            $objAtividadeDTO->retStrSiglaUnidade();
            $objAtividadeDTO->retStrDescricaoUnidade();
            $objAtividadeDTO->retNumIdUnidadeOrigem();
            $objAtividadeDTO->retDthAbertura();
            $objAtividadeDTO->retDthConclusao();
            $objAtividadeDTO->retNumIdTarefa();
            $objAtividadeDTO->retStrNomeTarefa();
            $objAtividadeDTO->retStrIdTarefaModuloTarefa();
            $objAtividadeDTO->retNumIdUsuarioOrigem();
            $objAtividadeDTO->retStrSiglaUnidadeOrigem();
            $objAtividadeDTO->retStrSiglaUsuarioOrigem();
            $objAtividadeDTO->retStrNomeUsuarioOrigem();
            $objAtividadeDTO->retStrSiglaUsuarioAtribuicao();
            $objAtividadeDTO->retStrNomeUsuarioAtribuicao();
            $objAtividadeDTO->retStrSiglaUsuarioConclusao();
            $objAtividadeDTO->retStrNomeUsuarioConclusao();
            $objAtividadeDTO->retDtaPrazo();
            $objAtividadeDTO->retStrStaProtocoloProtocolo();
            $objAtividadeDTO->retStrSinInicial();

            if ($parObjProcedimentoHistoricoDTO->getStrStaHistorico() == ProcedimentoRN::$TH_RESUMIDO || $parObjProcedimentoHistoricoDTO->getStrStaHistorico() == ProcedimentoRN::$TH_EXTERNO) {

                $objAtividadeDTO->adicionarCriterio(array('IdTarefa', 'SinHistoricoResumidoTarefa'),
                    array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL),
                    array(null, 'S'),
                    InfraDTO::$OPER_LOGICO_OR);


            } else if ($parObjProcedimentoHistoricoDTO->getStrStaHistorico() == ProcedimentoRN::$TH_PARCIAL) {

                $objAtividadeDTO->adicionarCriterio(array('IdTarefa', 'SinHistoricoCompletoTarefa'),
                    array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL),
                    array(null, 'S'),
                    InfraDTO::$OPER_LOGICO_OR);

            } else if ($parObjProcedimentoHistoricoDTO->getStrStaHistorico() == ProcedimentoRN::$TH_PERSONALIZADO) {

                if (!$parObjProcedimentoHistoricoDTO->isSetDblIdProcedimentoAnexado() && !$parObjProcedimentoHistoricoDTO->isSetDblIdDocumento()) {

                    if ($parObjProcedimentoHistoricoDTO->isSetNumIdAtividade()) {
                        if (!is_array($parObjProcedimentoHistoricoDTO->getNumIdAtividade())) {
                            $objAtividadeDTO->setNumIdAtividade($parObjProcedimentoHistoricoDTO->getNumIdAtividade());
                        } else {
                            $objAtividadeDTO->setNumIdAtividade($parObjProcedimentoHistoricoDTO->getNumIdAtividade(), InfraDTO::$OPER_IN);
                        }
                    }

                    if ($parObjProcedimentoHistoricoDTO->isSetNumIdTarefa()) {
                        if (!is_array($parObjProcedimentoHistoricoDTO->getNumIdTarefa())) {
                            $objAtividadeDTO->setNumIdTarefa($parObjProcedimentoHistoricoDTO->getNumIdTarefa());
                        } else {
                            $objAtividadeDTO->setNumIdTarefa($parObjProcedimentoHistoricoDTO->getNumIdTarefa(), InfraDTO::$OPER_IN);
                        }
                    }

                    if ($parObjProcedimentoHistoricoDTO->isSetStrIdTarefaModulo()) {
                        if (!is_array($parObjProcedimentoHistoricoDTO->getStrIdTarefaModulo())) {
                            $objAtividadeDTO->setStrIdTarefaModuloTarefa($parObjProcedimentoHistoricoDTO->getStrIdTarefaModulo());
                        } else {
                            $objAtividadeDTO->setStrIdTarefaModuloTarefa($parObjProcedimentoHistoricoDTO->getStrIdTarefaModulo(), InfraDTO::$OPER_IN);
                        }
                    }

                } else {

                    $objAtributoAndamentoDTO = new AtributoAndamentoDTO();
                    $objAtributoAndamentoDTO->retNumIdAtividade();
                    $objAtributoAndamentoDTO->setDblIdProtocoloAtividade($objProcedimentoDTO->getDblIdProcedimento());

                    if ($parObjProcedimentoHistoricoDTO->isSetNumIdAtividade()) {
                        if (!is_array($parObjProcedimentoHistoricoDTO->getNumIdAtividade())) {
                            $objAtributoAndamentoDTO->setNumIdAtividade($parObjProcedimentoHistoricoDTO->getNumIdAtividade());
                        } else {
                            $objAtributoAndamentoDTO->setNumIdAtividade($parObjProcedimentoHistoricoDTO->getNumIdAtividade(), InfraDTO::$OPER_IN);
                        }
                    }

                    if ($parObjProcedimentoHistoricoDTO->isSetNumIdTarefa()) {
                        if (!is_array($parObjProcedimentoHistoricoDTO->getNumIdTarefa())) {
                            $objAtributoAndamentoDTO->setNumIdTarefaAtividade($parObjProcedimentoHistoricoDTO->getNumIdTarefa());
                        } else {
                            $objAtributoAndamentoDTO->setNumIdTarefaAtividade($parObjProcedimentoHistoricoDTO->getNumIdTarefa(), InfraDTO::$OPER_IN);
                        }
                    }

                    if ($parObjProcedimentoHistoricoDTO->isSetStrIdTarefaModulo()) {
                        if (!is_array($parObjProcedimentoHistoricoDTO->getStrIdTarefaModulo())) {
                            $objAtividadeDTO->setStrIdTarefaModuloTarefa($parObjProcedimentoHistoricoDTO->getStrIdTarefaModulo());
                        } else {
                            $objAtividadeDTO->setStrIdTarefaModuloTarefa($parObjProcedimentoHistoricoDTO->getStrIdTarefaModulo(), InfraDTO::$OPER_IN);
                        }
                    }

                    if ($parObjProcedimentoHistoricoDTO->isSetDblIdProcedimentoAnexado()) {
                        $objAtributoAndamentoDTO->setStrNome('PROCESSO');
                        $objAtributoAndamentoDTO->setStrIdOrigem($parObjProcedimentoHistoricoDTO->getDblIdProcedimentoAnexado());
                    } else {
                        $objAtributoAndamentoDTO->setStrNome('DOCUMENTO');
                        $objAtributoAndamentoDTO->setStrIdOrigem($parObjProcedimentoHistoricoDTO->getDblIdDocumento());
                    }

                    $objAtributoAndamentoRN = new AtributoAndamentoRN();
                    $arrObjAtributoAndamentoDTO = $objAtributoAndamentoRN->listarRN1367($objAtributoAndamentoDTO);

                    if (count($arrObjAtributoAndamentoDTO)) {
                        $objAtividadeDTO->setNumIdAtividade(InfraArray::converterArrInfraDTO($arrObjAtributoAndamentoDTO, 'IdAtividade'), InfraDTO::$OPER_IN);
                    } else {
                        $objAtividadeDTO->setNumIdAtividade(null);
                    }
                }
            }

            $objAtividadeDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());

            $objAtividadeDTO->setOrdNumIdAtividade(InfraDTO::$TIPO_ORDENACAO_DESC);

            //pagina��o
            $objAtividadeDTO->setNumMaxRegistrosRetorno($parObjProcedimentoHistoricoDTO->getNumMaxRegistrosRetorno());
            $objAtividadeDTO->setNumPaginaAtual($parObjProcedimentoHistoricoDTO->getNumPaginaAtual());

            $objAtividadeRN = new AtividadeRN();
            $arrObjAtividadeDTO = InfraArray::indexarArrInfraDTO($objAtividadeRN->listarRN0036($objAtividadeDTO), 'IdAtividade');

            //pagina��o
            $parObjProcedimentoHistoricoDTO->setNumTotalRegistros($objAtividadeDTO->getNumTotalRegistros());
            $parObjProcedimentoHistoricoDTO->setNumRegistrosPaginaAtual($objAtividadeDTO->getNumRegistrosPaginaAtual());


            if (count($arrObjAtividadeDTO)) {
                $objAtributoAndamentoDTO = new AtributoAndamentoDTO();
                $objAtributoAndamentoDTO->retTodos(true);
                $objAtributoAndamentoDTO->setNumIdAtividade(InfraArray::converterArrInfraDTO($arrObjAtividadeDTO, 'IdAtividade'), InfraDTO::$OPER_IN);

                $objAtributoAndamentoDTO->setOrdNumIdAtributoAndamento(InfraDTO::$TIPO_ORDENACAO_ASC);

                $objAtributoAndamentoRN = new AtributoAndamentoRN();
                $arrObjAtributoAndamentoDTO = $objAtributoAndamentoRN->listarRN1367($objAtributoAndamentoDTO);

                if (count($arrObjAtributoAndamentoDTO) > 0) {

                    if ($parObjProcedimentoHistoricoDTO->getStrSinGerarLinksHistorico() == 'N') {
                        $bolAcaoDownload = false;
                        $bolAcaoProcedimentoTrabalhar = false;
                        $bolAcaoRelBlocoProtocoloListar = false;
                        $bolAcaoDocumentoVisualizar = false;
                        $bolAcaoLocalizadorProtocolosListar = false;
                    } else if ($objProtocoloDTO->getNumCodigoAcesso() < 0) {
                        $bolAcaoDownload = false;
                        $bolAcaoProcedimentoTrabalhar = false;
                        $bolAcaoDocumentoVisualizar = false;
                        $bolAcaoRelBlocoProtocoloListar = false;

                        //monta link de arquivo mesmo se n�o tem acesso
                        $bolAcaoLocalizadorProtocolosListar = SessaoSEI::getInstance()->verificarPermissao('localizador_protocolos_listar');

                    } else {
                        $bolAcaoDownload = SessaoSEI::getInstance()->verificarPermissao('documento_download_anexo');
                        $bolAcaoProcedimentoTrabalhar = SessaoSEI::getInstance()->verificarPermissao('procedimento_trabalhar');
                        $bolAcaoDocumentoVisualizar = SessaoSEI::getInstance()->verificarPermissao('documento_visualizar');
                        $bolAcaoRelBlocoProtocoloListar = SessaoSEI::getInstance()->verificarPermissao('rel_bloco_protocolo_listar');
                        $bolAcaoLocalizadorProtocolosListar = SessaoSEI::getInstance()->verificarPermissao('localizador_protocolos_listar');
                    }

                    $arrObjAtributoAndamentoDTOPorNome = InfraArray::indexarArrInfraDTO($arrObjAtributoAndamentoDTO, 'Nome', true);

                    if (isset($arrObjAtributoAndamentoDTOPorNome['PROCESSO'])) {
                        $dto = new ProcedimentoDTO();
                        $dto->retDblIdProcedimento();
                        $dto->setDblIdProcedimento(InfraArray::converterArrInfraDTO($arrObjAtributoAndamentoDTOPorNome['PROCESSO'], 'IdOrigem'), InfraDTO::$OPER_IN);

                        $arrObjProcedimentoDTO = InfraArray::indexarArrInfraDTO($this->listarRN0278($dto), 'IdProcedimento');
                    }

                    if (isset($arrObjAtributoAndamentoDTOPorNome['DOCUMENTO'])) {
                        $dto = new DocumentoDTO();
                        $dto->retDblIdDocumento();
                        $dto->retStrProtocoloDocumentoFormatado();
                        $dto->retStrNomeSerie();
                        $dto->retStrNumero();
                        $dto->retStrStaProtocoloProtocolo();
                        $dto->setDblIdDocumento(InfraArray::converterArrInfraDTO($arrObjAtributoAndamentoDTOPorNome['DOCUMENTO'], 'IdOrigem'), InfraDTO::$OPER_IN);

                        $objDocumentoRN = new DocumentoRN();
                        $arrObjDocumentoDTO = InfraArray::indexarArrInfraDTO($objDocumentoRN->listarRN0008($dto), 'IdDocumento');
                    }

                    if (isset($arrObjAtributoAndamentoDTOPorNome['BLOCO'])) {
                        $objBlocoDTO = new BlocoDTO();
                        $objBlocoDTO->retNumIdBloco();
                        $objBlocoDTO->setNumIdBloco(InfraArray::converterArrInfraDTO($arrObjAtributoAndamentoDTOPorNome['BLOCO'], 'IdOrigem'), InfraDTO::$OPER_IN);
                        $objBlocoDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());

                        $objBlocoRN = new BlocoRN();
                        $arrIdBloco = InfraArray::converterArrInfraDTO($objBlocoRN->listarRN1277($objBlocoDTO), 'IdBloco');

                        $objRelBlocoUnidadeDTO = new RelBlocoUnidadeDTO();
                        $objRelBlocoUnidadeDTO->retNumIdBloco();
                        $objRelBlocoUnidadeDTO->setNumIdBloco(InfraArray::converterArrInfraDTO($arrObjAtributoAndamentoDTOPorNome['BLOCO'], 'IdOrigem'), InfraDTO::$OPER_IN);
                        $objRelBlocoUnidadeDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());

                        $objRelBlocoUnidadeRN = new RelBlocoUnidadeRN();
                        $arrIdBloco = array_unique(array_merge($arrIdBloco, InfraArray::converterArrInfraDTO($objRelBlocoUnidadeRN->listarRN1304($objRelBlocoUnidadeDTO), 'IdBloco')));
                    }

                    if ($bolAcaoLocalizadorProtocolosListar && isset($arrObjAtributoAndamentoDTOPorNome['LOCALIZADOR'])) {
                        $dto = new LocalizadorDTO();
                        $dto->retNumIdLocalizador();
                        $dto->retNumIdUnidade();
                        $dto->setNumIdLocalizador(InfraArray::converterArrInfraDTO($arrObjAtributoAndamentoDTOPorNome['LOCALIZADOR'], 'IdOrigem'), InfraDTO::$OPER_IN);

                        $objLocalizadorRN = new LocalizadorRN();
                        $arrObjLocalizadorDTO = InfraArray::indexarArrInfraDTO($objLocalizadorRN->listarRN0622($dto), 'IdLocalizador');
                    } else {
                        $arrObjLocalizadorDTO = array();
                    }

                    $arrObjNivelAcessoDTO = InfraArray::indexarArrInfraDTO($objProtocoloRN->listarNiveisAcessoRN0878(), 'StaNivel');
                    foreach ($arrObjNivelAcessoDTO as $objNivelAcessoDTO) {
                        $objNivelAcessoDTO->setStrDescricao(InfraString::transformarCaixaBaixa($objNivelAcessoDTO->getStrDescricao()));
                    }

                    $arrObjGrauSigiloDTO = InfraArray::indexarArrInfraDTO(ProtocoloRN::listarGrausSigiloso(), 'StaGrau');
                    foreach ($arrObjGrauSigiloDTO as $objGrauSigiloDTO) {
                        $objGrauSigiloDTO->setStrDescricao(InfraString::transformarCaixaBaixa($objGrauSigiloDTO->getStrDescricao()));
                    }


                    $objTipoConferenciaDTO = new TipoConferenciaDTO();
                    $objTipoConferenciaDTO->setBolExclusaoLogica(false);
                    $objTipoConferenciaDTO->retNumIdTipoConferencia();
                    $objTipoConferenciaDTO->retStrDescricao();

                    $objTipoConferenciaRN = new TipoConferenciaRN();
                    $arrObjTipoConferenciaDTO = InfraArray::indexarArrInfraDTO($objTipoConferenciaRN->listar($objTipoConferenciaDTO), 'IdTipoConferencia');
                    foreach ($arrObjTipoConferenciaDTO as $objTipoConferenciaDTO) {
                        $objTipoConferenciaDTO->setStrDescricao(InfraString::transformarCaixaBaixa($objTipoConferenciaDTO->getStrDescricao()));
                    }

                    $objHipoteseLegalDTO = new HipoteseLegalDTO();
                    $objHipoteseLegalDTO->setBolExclusaoLogica(false);
                    $objHipoteseLegalDTO->retNumIdHipoteseLegal();
                    $objHipoteseLegalDTO->retStrNome();
                    $objHipoteseLegalDTO->retStrBaseLegal();

                    $objHipoteseLegalRN = new HipoteseLegalRN();
                    $arrObjHipoteseLegalDTO = InfraArray::indexarArrInfraDTO($objHipoteseLegalRN->listar($objHipoteseLegalDTO), 'IdHipoteseLegal');

                    foreach ($arrObjAtributoAndamentoDTO as $objAtributoAndamentoDTO) {

                        $objAtividadeDTO = $arrObjAtividadeDTO[$objAtributoAndamentoDTO->getNumIdAtividade()];

                        $strNomeTarefa = $objAtividadeDTO->getStrNomeTarefa();

                        $objAtributoAndamentoDTO->setStrValor(PaginaSEI::tratarHTML($objAtributoAndamentoDTO->getStrValor()));

                        switch ($objAtributoAndamentoDTO->getStrNome()) {

                            case 'DOCUMENTO':
                                $this->substituirAtributoDocumentoHistorico($objAtributoAndamentoDTO, $arrObjDocumentoDTO, $bolAcaoDocumentoVisualizar, $strNomeTarefa);
                                break;

                            case 'DOCUMENTOS':
                                $this->substitutirAtributoMultiploDocumentos($objAtributoAndamentoDTO, $arrObjAtributoAndamentoDTOPorNome['DOCUMENTO'], $arrObjDocumentoDTO, $bolAcaoDocumentoVisualizar, $strNomeTarefa);
                                break;

                            case 'NIVEL_ACESSO':
                                $strNomeTarefa = str_replace('@NIVEL_ACESSO@', $arrObjNivelAcessoDTO[$objAtributoAndamentoDTO->getStrIdOrigem()]->getStrDescricao(), $strNomeTarefa);
                                break;

                            case 'GRAU_SIGILO':
                                if ($objAtributoAndamentoDTO->getNumIdTarefaAtividade() == TarefaRN::$TI_GERACAO_PROCEDIMENTO ||
                                    $objAtributoAndamentoDTO->getNumIdTarefaAtividade() == TarefaRN::$TI_GERACAO_DOCUMENTO ||
                                    $objAtributoAndamentoDTO->getNumIdTarefaAtividade() == TarefaRN::$TI_RECEBIMENTO_DOCUMENTO ||
                                    $objAtributoAndamentoDTO->getNumIdTarefaAtividade() == TarefaRN::$TI_ALTERACAO_NIVEL_ACESSO_GLOBAL
                                ) {
                                    $strNomeTarefa = str_replace('@GRAU_SIGILO@', ' (' . $arrObjGrauSigiloDTO[$objAtributoAndamentoDTO->getStrIdOrigem()]->getStrDescricao() . ')', $strNomeTarefa);
                                } else {
                                    $strNomeTarefa = str_replace('@GRAU_SIGILO@', ' ' . $arrObjGrauSigiloDTO[$objAtributoAndamentoDTO->getStrIdOrigem()]->getStrDescricao(), $strNomeTarefa);
                                }
                                break;

                            case 'HIPOTESE_LEGAL':
                                if ($objAtributoAndamentoDTO->getNumIdTarefaAtividade() == TarefaRN::$TI_ALTERACAO_NIVEL_ACESSO_PROCESSO ||
                                    $objAtributoAndamentoDTO->getNumIdTarefaAtividade() == TarefaRN::$TI_ALTERACAO_GRAU_SIGILO_PROCESSO ||
                                    $objAtributoAndamentoDTO->getNumIdTarefaAtividade() == TarefaRN::$TI_ALTERACAO_HIPOTESE_LEGAL_PROCESSO ||
                                    $objAtributoAndamentoDTO->getNumIdTarefaAtividade() == TarefaRN::$TI_ALTERACAO_NIVEL_ACESSO_DOCUMENTO ||
                                    $objAtributoAndamentoDTO->getNumIdTarefaAtividade() == TarefaRN::$TI_ALTERACAO_GRAU_SIGILO_DOCUMENTO ||
                                    $objAtributoAndamentoDTO->getNumIdTarefaAtividade() == TarefaRN::$TI_ALTERACAO_HIPOTESE_LEGAL_DOCUMENTO
                                ) {
                                    if ($objAtributoAndamentoDTO->getStrIdOrigem() == null) {
                                        $strNomeTarefa = str_replace('@HIPOTESE_LEGAL@', '"n�o informada"', $strNomeTarefa);
                                    } else {
                                        $strNomeTarefa = str_replace('@HIPOTESE_LEGAL@', HipoteseLegalINT::formatarHipoteseLegal($arrObjHipoteseLegalDTO[$objAtributoAndamentoDTO->getStrIdOrigem()]->getStrNome(), $arrObjHipoteseLegalDTO[$objAtributoAndamentoDTO->getStrIdOrigem()]->getStrBaseLegal()), $strNomeTarefa);
                                    }
                                } else {
                                    $strNomeTarefa = str_replace('@HIPOTESE_LEGAL@', ', ' . HipoteseLegalINT::formatarHipoteseLegal($arrObjHipoteseLegalDTO[$objAtributoAndamentoDTO->getStrIdOrigem()]->getStrNome(), $arrObjHipoteseLegalDTO[$objAtributoAndamentoDTO->getStrIdOrigem()]->getStrBaseLegal()), $strNomeTarefa);
                                }
                                break;

                            case 'VISUALIZACAO':
                                if ($objAtributoAndamentoDTO->getStrIdOrigem() == null || $objAtributoAndamentoDTO->getStrIdOrigem() == AcessoExternoRN::$TV_INTEGRAL) {
                                    $strNomeTarefa = str_replace('@VISUALIZACAO@', ' Com visualiza��o integral do processo.', $strNomeTarefa);
                                } else if ($objAtributoAndamentoDTO->getStrIdOrigem() == AcessoExternoRN::$TV_PARCIAL) {
                                    if ($objAtividadeDTO->getNumIdTarefa() == TarefaRN::$TI_LIBERACAO_ACESSO_EXTERNO) {
                                        $strNomeTarefa = str_replace('@VISUALIZACAO@', ' Para disponibiliza��o de documentos.', $strNomeTarefa);
                                    } else {
                                        $strNomeTarefa = str_replace('@VISUALIZACAO@', ' Com visualiza��o parcial do processo.', $strNomeTarefa);
                                    }
                                } else if ($objAtributoAndamentoDTO->getStrIdOrigem() == AcessoExternoRN::$TV_NENHUM) {
                                    $strNomeTarefa = str_replace('@VISUALIZACAO@', ' Sem acesso ao processo.', $strNomeTarefa);
                                }
                                break;

                            case 'DATA_AUTUACAO':
                                if ($objAtributoAndamentoDTO->getStrValor() != null) {
                                    $strNomeTarefa = str_replace('@DATA_AUTUACAO@', ' (autuado em ' . $objAtributoAndamentoDTO->getStrValor() . ')', $strNomeTarefa);
                                }
                                break;

                            case 'TIPO_CONFERENCIA':
                                if ($objAtributoAndamentoDTO->getNumIdTarefaAtividade() == TarefaRN::$TI_ALTERACAO_TIPO_CONFERENCIA_DOCUMENTO) {
                                    if ($objAtributoAndamentoDTO->getStrIdOrigem() == null) {
                                        $strNomeTarefa = str_replace('@TIPO_CONFERENCIA@', '"n�o informado"', $strNomeTarefa);
                                    } else {
                                        $strNomeTarefa = str_replace('@TIPO_CONFERENCIA@', $arrObjTipoConferenciaDTO[$objAtributoAndamentoDTO->getStrIdOrigem()]->getStrDescricao(), $strNomeTarefa);
                                    }
                                } else {
                                    $strNomeTarefa = str_replace('@TIPO_CONFERENCIA@', ', conferido com ' . $arrObjTipoConferenciaDTO[$objAtributoAndamentoDTO->getStrIdOrigem()]->getStrDescricao(), $strNomeTarefa);
                                }
                                break;

                            case 'PROCESSO':
                                $this->substituirAtributoProcessoHistorico($objAtributoAndamentoDTO, $arrObjProcedimentoDTO, $bolAcaoProcedimentoTrabalhar, $strNomeTarefa);
                                break;

                            case 'USUARIO':
                                if ($objAtributoAndamentoDTO->getStrValor() != null) {
                                    $arrValor = explode('�', $objAtributoAndamentoDTO->getStrValor());
                                    $strSubstituicao = '<a href="javascript:void(0);" alt="' . $arrValor[1] . '" title="' . $arrValor[1] . '" class="ancoraSigla">' . $arrValor[0] . '</a>';
                                } else {
                                    $strSubstituicao = '';
                                }
                                $strNomeTarefa = str_replace('@USUARIO@', $strSubstituicao, $strNomeTarefa);
                                break;

                            case 'USUARIOS':
                                $this->substitutirAtributoMultiploUsuarios($objAtributoAndamentoDTO, $arrObjAtributoAndamentoDTOPorNome['USUARIO'], $strNomeTarefa);
                                break;

                            case 'UNIDADE':
                                $arrValor = explode('�', $objAtributoAndamentoDTO->getStrValor());
                                $strSubstituicao = '<a href="javascript:void(0);" alt="' . $arrValor[1] . '" title="' . $arrValor[1] . '" class="ancoraSigla">' . $arrValor[0] . '</a>';
                                $strNomeTarefa = str_replace('@UNIDADE@', $strSubstituicao, $strNomeTarefa);
                                break;

                            case 'BLOCO':
                                $this->substituirAtributoBlocoHistorico($objAtributoAndamentoDTO, $arrIdBloco, $bolAcaoRelBlocoProtocoloListar, $strNomeTarefa);
                                break;

                            case 'DATA_HORA':
                                $strNomeTarefa = str_replace('@DATA_HORA@', substr($objAtributoAndamentoDTO->getStrValor(), 0, 16), $strNomeTarefa);
                                break;

                            case 'USUARIO_ANULACAO':
                                $arrValor = explode('�', $objAtributoAndamentoDTO->getStrValor());
                                $strSubstituicao = '<a href="javascript:void(0);" alt="' . $arrValor[1] . '" title="' . $arrValor[1] . '" class="ancoraSigla">' . $arrValor[0] . '</a>';
                                $strNomeTarefa = str_replace('@USUARIO_ANULACAO@', $strSubstituicao, $strNomeTarefa);
                                break;

                            case 'INTERESSADO':
                                $arrValor = explode('�', $objAtributoAndamentoDTO->getStrValor());
                                $strSubstituicao = '<a href="javascript:void(0);" alt="' . $arrValor[1] . '" title="' . $arrValor[1] . '" class="ancoraSigla">' . $arrValor[0] . '</a>';
                                $strNomeTarefa = str_replace('@INTERESSADO@', $strSubstituicao, $strNomeTarefa);
                                break;

                            case 'LOCALIZADOR':
                                $this->substituirAtributoLocalizadorHistorico($objAtributoAndamentoDTO, $arrObjLocalizadorDTO, $bolAcaoLocalizadorProtocolosListar, $strNomeTarefa);
                                break;

                            case 'ANEXO':

                                $strSubstituicao = $objAtributoAndamentoDTO->getStrValor();

                                if ($bolAcaoDownload) {
                                    $objAnexoDTO = new AnexoDTO();
                                    $objAnexoDTO->retNumIdAnexo();
                                    $objAnexoDTO->setNumIdAnexo($objAtributoAndamentoDTO->getStrIdOrigem());
                                    $objAnexoDTO->setNumMaxRegistrosRetorno(1);

                                    $objAnexoRN = new AnexoRN();
                                    if ($objAnexoRN->consultarRN0736($objAnexoDTO) != null) {
                                        $strSubstituicao = '<a href="' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=documento_download_anexo&id_anexo=' . $objAtributoAndamentoDTO->getStrIdOrigem()) . '" target="_blank" class="ancoraHistoricoProcesso">' . $objAtributoAndamentoDTO->getStrValor() . '</a>';
                                    } else {
                                        $strSubstituicao = '<a href="javascript:void(0);" onclick="alert(\'Este anexo foi exclu�do.\');"  class="ancoraHistoricoProcesso">' . $objAtributoAndamentoDTO->getStrValor() . '</a>';
                                    }
                                }
                                $strNomeTarefa = str_replace('@ANEXO@', $strSubstituicao, $strNomeTarefa);
                                break;

                            default:
                                $strNomeTarefa = str_replace('@' . $objAtributoAndamentoDTO->getStrNome() . '@', $objAtributoAndamentoDTO->getStrValor(), $strNomeTarefa);
                        }

                        if ($parObjProcedimentoHistoricoDTO->getStrStaHistorico() == ProcedimentoRN::$TH_AUDITORIA && $objAtributoAndamentoDTO->getStrNome() == 'USUARIO_EMULADOR') {
                            $arrValor = explode('�', $objAtributoAndamentoDTO->getStrValor());
                            $arrUsuario = explode('�', $arrValor[0]);
                            $arrOrgaoUsuario = explode('�', $arrValor[1]);
                            $strUsuario = '<a href="javascript:void(0);" alt="' . $arrUsuario[1] . '" title="' . $arrUsuario[1] . '" class="ancoraSigla">' . $arrUsuario[0] . '</a>';
                            $strOrgaoUsuario = '<a href="javascript:void(0);" alt="' . $arrOrgaoUsuario[1] . '" title="' . $arrOrgaoUsuario[1] . '" class="ancoraSigla">' . $arrOrgaoUsuario[0] . '</a>';
                            $strNomeTarefa .= ' (emulado por ' . $strUsuario . ' / ' . $strOrgaoUsuario . ')';
                        }

                        $objAtividadeDTO->setStrNomeTarefa($strNomeTarefa);
                    }
                }


                if ($parObjProcedimentoHistoricoDTO->getStrStaHistorico() == ProcedimentoRN::$TH_TOTAL) {

                    foreach ($arrObjAtividadeDTO as $objAtividadeDTO) {
                        if ($objAtividadeDTO->getDthConclusao() == null) {
                            $objAtividadeDTO->setStrSinUltimaUnidadeHistorico('S');
                        } else {
                            $objAtividadeDTO->setStrSinUltimaUnidadeHistorico('N');
                        }
                    }

                } else {

                    //buscar as unidades/usuarios que possuem andamento em aberto
                    $objAtividadeDTO = new AtividadeDTO();
                    $objAtividadeDTO->setDistinct(true);
                    $objAtividadeDTO->retNumIdUnidade();
                    $objAtividadeDTO->retNumIdUsuario();
                    $objAtividadeDTO->setDthConclusao(null);
                    $objAtividadeDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());

                    $objAtividadeRN = new AtividadeRN();
                    $arrObjAtividadeDTOAbertas = $objAtividadeRN->listarRN0036($objAtividadeDTO);

                    foreach ($arrObjAtividadeDTOAbertas as $objAtividadeDTOAberta) {
                        foreach ($arrObjAtividadeDTO as $objAtividadeDTO) {
                            if ($objAtividadeDTO->getNumIdUnidade() == $objAtividadeDTOAberta->getNumIdUnidade() && ($objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo() != ProtocoloRN::$NA_SIGILOSO || $objAtividadeDTO->getNumIdUsuario() == $objAtividadeDTOAberta->getNumIdUsuario())) {
                                $objAtividadeDTO->setStrSinUltimaUnidadeHistorico('S');
                                break;
                            }
                        }
                    }

                    foreach ($arrObjAtividadeDTO as $objAtividadeDTO) {
                        if (!$objAtividadeDTO->isSetStrSinUltimaUnidadeHistorico()) {
                            $objAtividadeDTO->setStrSinUltimaUnidadeHistorico('N');
                        }
                    }
                }

                if ($parObjProcedimentoHistoricoDTO->getStrSinRetornarAtributos() == 'S') {

                    $arrObjAtributoAndamentoDTOPorAtividade = InfraArray::indexarArrInfraDTO($arrObjAtributoAndamentoDTO, 'IdAtividade', true);

                    foreach ($arrObjAtividadeDTO as $objAtividadeDTO) {
                        if (isset($arrObjAtributoAndamentoDTOPorAtividade[$objAtividadeDTO->getNumIdAtividade()])) {
                            $objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTOPorAtividade[$objAtividadeDTO->getNumIdAtividade()]);
                        } else {
                            $objAtividadeDTO->setArrObjAtributoAndamentoDTO(array());
                        }
                    }
                }
            }


            foreach ($arrObjAtividadeDTO as $objAtividadeDTO) {

                if (in_array($objAtividadeDTO->getNumIdTarefa(), array(TarefaRN::$TI_GERACAO_PROCEDIMENTO,
                    TarefaRN::$TI_GERACAO_DOCUMENTO,
                    TarefaRN::$TI_RECEBIMENTO_DOCUMENTO,
                    TarefaRN::$TI_LIBERACAO_ACESSO_EXTERNO,
                    TarefaRN::$TI_LIBERACAO_ACESSO_EXTERNO_CANCELADA,
                    TarefaRN::$TI_LIBERACAO_ASSINATURA_EXTERNA,
                    TarefaRN::$TI_LIBERACAO_ASSINATURA_EXTERNA_CANCELADA))) {

                    $objAtividadeDTO->setStrNomeTarefa(str_replace(array('@NIVEL_ACESSO@',
                        '@GRAU_SIGILO@',
                        '@TIPO_CONFERENCIA@',
                        '@DATA_AUTUACAO@',
                        '@HIPOTESE_LEGAL@',
                        '@VISUALIZACAO@'), '', $objAtividadeDTO->getStrNomeTarefa()));
                }
            }

            $objProcedimentoDTO->setArrObjAtividadeDTO(array_values($arrObjAtividadeDTO));

            return $objProcedimentoDTO;

        } catch (Exception $e) {
            throw new InfraException('Erro consultando hist�rico do processo.', $e);
        }
    }

    private function montarAtributoDocumentoHistorico(AtributoAndamentoDTO $objAtributoAndamentoDTO, $arrObjDocumentoDTO, $bolAcaoDocumentoVisualizar)
    {

        if (!isset($arrObjDocumentoDTO[$objAtributoAndamentoDTO->getStrIdOrigem()])) {
            $strSubstituicao = '<a href="javascript:void(0);" onclick="alert(\'Este documento foi exclu�do.\');" class="ancoraHistoricoProcesso">' . $objAtributoAndamentoDTO->getStrValor() . '</a>';
        } else {
            $objDocumentoDTO = $arrObjDocumentoDTO[$objAtributoAndamentoDTO->getStrIdOrigem()];
            $strIdentificacao = PaginaSEI::tratarHTML(trim($objDocumentoDTO->getStrNomeSerie() . ' ' . $objDocumentoDTO->getStrNumero()));
            if ($bolAcaoDocumentoVisualizar) {
                $strSubstituicao = '<a href="' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=documento_visualizar&id_documento=' . $objAtributoAndamentoDTO->getStrIdOrigem()) . '" target="_blank" class="ancoraHistoricoProcesso">' . $objAtributoAndamentoDTO->getStrValor() . '</a> (' . $strIdentificacao . ')';
            } else {
                $strSubstituicao = $objAtributoAndamentoDTO->getStrValor() . ' (' . $strIdentificacao . ')';
            }
        }

        return $strSubstituicao;
    }

    private function substitutirAtributoMultiploDocumentos($objAtributoAndamentoDTO, $arrObjAtributoAndamentoDTO, $arrObjDocumentoDTO, $bolAcaoDocumentoVisualizar, &$strNomeTarefa)
    {
        if (is_array($arrObjAtributoAndamentoDTO)) {

            $arr = array();

            $numAtributosTotal = count($arrObjAtributoAndamentoDTO);
            for ($i = 0; $i < $numAtributosTotal; $i++) {
                if ($arrObjAtributoAndamentoDTO[$i]->getNumIdAtividade() == $objAtributoAndamentoDTO->getNumIdAtividade()) {
                    $arr[] = $arrObjAtributoAndamentoDTO[$i];
                }
            }

            $n = count($arr);
            $strValorMultiplo = '';
            for ($i = 0; $i < $n; $i++) {
                if ($strValorMultiplo != '') {
                    if ($i == ($n - 1)) {
                        $strValorMultiplo .= ' e ';
                    } else {
                        $strValorMultiplo .= ', ';
                    }
                }
                $strValorMultiplo .= $this->montarAtributoDocumentoHistorico($arr[$i], $arrObjDocumentoDTO, $bolAcaoDocumentoVisualizar);
            }

            $strNomeTarefa = str_replace('#DOCUMENTOS#', $strValorMultiplo, $strNomeTarefa);
        }
    }

    private function substitutirAtributoMultiploUsuarios($objAtributoAndamentoDTO, $arrObjAtributoAndamentoDTO, &$strNomeTarefa)
    {
        if (is_array($arrObjAtributoAndamentoDTO)) {

            $arr = array();

            $numAtributosTotal = count($arrObjAtributoAndamentoDTO);
            for ($i = 0; $i < $numAtributosTotal; $i++) {
                if ($arrObjAtributoAndamentoDTO[$i]->getNumIdAtividade() == $objAtributoAndamentoDTO->getNumIdAtividade()) {
                    $arr[] = $arrObjAtributoAndamentoDTO[$i];
                }
            }

            $n = count($arr);
            $strValorMultiplo = '';
            for ($i = 0; $i < $n; $i++) {
                if ($strValorMultiplo != '') {
                    if ($i == ($n - 1)) {
                        $strValorMultiplo .= ' e ';
                    } else {
                        $strValorMultiplo .= ', ';
                    }
                }
                $arrValor = explode('�', $arr[$i]->getStrValor());
                $strValorMultiplo .= '<a href="javascript:void(0);" alt="' . $arrValor[1] . '" title="' . $arrValor[1] . '" class="ancoraSigla">' . $arrValor[0] . '</a>';
            }

            $strNomeTarefa = str_replace('#USUARIOS#', $strValorMultiplo, $strNomeTarefa);
        }
    }

    private function substituirAtributoDocumentoHistorico(AtributoAndamentoDTO $objAtributoAndamentoDTO, $arrObjDocumentoDTO, $bolAcaoDocumentoVisualizar, &$strNomeTarefa)
    {
        $strSubstituicao = $this->montarAtributoDocumentoHistorico($objAtributoAndamentoDTO, $arrObjDocumentoDTO, $bolAcaoDocumentoVisualizar);
        $strNomeTarefa = str_replace('@DOCUMENTO@', $strSubstituicao, $strNomeTarefa);
    }

    private function substituirAtributoBlocoHistorico(AtributoAndamentoDTO $objAtributoAndamentoDTO, $arrIdBloco, $bolAcaoRelBlocoProtocoloListar, &$strNomeTarefa)
    {

        $strSubstituicao = $objAtributoAndamentoDTO->getStrValor();

        if ($bolAcaoRelBlocoProtocoloListar) {

            if (in_array($objAtributoAndamentoDTO->getStrIdOrigem(), $arrIdBloco)) {
                $strSubstituicao = '<a href="' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=rel_bloco_protocolo_listar&id_bloco=' . $objAtributoAndamentoDTO->getStrIdOrigem()) . '" target="_blank" class="ancoraHistoricoProcesso">' . $objAtributoAndamentoDTO->getStrValor() . '</a>';
            }

        }

        $strNomeTarefa = str_replace('@BLOCO@', $strSubstituicao, $strNomeTarefa);
    }

    private function substituirAtributoProcessoHistorico(AtributoAndamentoDTO $objAtributoAndamentoDTO, $arrObjProcedimentoDTO, $bolAcaoProcedimentoTrabalhar, &$strNomeTarefa)
    {

        $strSubstituicao = $objAtributoAndamentoDTO->getStrValor();

        if ($bolAcaoProcedimentoTrabalhar) {
            if (isset($arrObjProcedimentoDTO[$objAtributoAndamentoDTO->getStrIdOrigem()])) {
                $strSubstituicao = '<a href="' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=procedimento_trabalhar&id_procedimento=' . $objAtributoAndamentoDTO->getStrIdOrigem()) . '" target="_blank" class="ancoraHistoricoProcesso">' . $objAtributoAndamentoDTO->getStrValor() . '</a>';
            } else {
                $strSubstituicao = '<a href="javascript:void(0);" onclick="alert(\'Este processo foi exclu�do.\');" class="ancoraHistoricoProcesso">' . $objAtributoAndamentoDTO->getStrValor() . '</a>';
            }
        }

        $strNomeTarefa = str_replace('@PROCESSO@', $strSubstituicao, $strNomeTarefa);
    }

    private function substituirAtributoLocalizadorHistorico(AtributoAndamentoDTO $objAtributoAndamentoDTO, $arrObjLocalizadorDTO, $bolAcaoLocalizadorProtocoloListar, &$strNomeTarefa)
    {

        $strIdOrigem = $objAtributoAndamentoDTO->getStrIdOrigem();

        //s� mostra link se o localizador � da unidade atual
        if ($bolAcaoLocalizadorProtocoloListar && isset($arrObjLocalizadorDTO[$strIdOrigem]) && $arrObjLocalizadorDTO[$strIdOrigem]->getNumIdUnidade() == SessaoSEI::getInstance()->getNumIdUnidadeAtual()) {
            $strSubstituicao = '<a href="' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=localizador_protocolos_listar&id_localizador=' . $strIdOrigem) . '" target="_blank" class="ancoraHistoricoProcesso">' . $objAtributoAndamentoDTO->getStrValor() . '</a>';
        } else {
            $strSubstituicao = $objAtributoAndamentoDTO->getStrValor();
        }

        $strNomeTarefa = str_replace('@LOCALIZADOR@', $strSubstituicao, $strNomeTarefa);
    }

    //m�todo que retorna a unidade de abertura de processo novo relacionado ao processo sigiloso que foi informado pelo usuario na tela de processo intercorrente
    public function retornaIdUnidadeAberturaProcessoConectado( $idProcedimento ){

    	//encontra a unidade de abertura e setar aqui
    	$idUnidadeSigiloso = null;
    	$unidadeRN = new UnidadeRN();
    	
    	//====================================================================================
    	//INICIO CASO 1 - Processo sigiloso que possui credencial apontada para unidade ativa
    	//====================================================================================
    	$acessoDTO = new AcessoDTO();    	
    	$acessoDTO->retNumIdUnidade();
    	$acessoDTO->setDblIdProtocolo( $idProcedimento );
    	$acessoDTO->setStrStaTipo( array( AcessoRN::$TA_CREDENCIAL_PROCESSO, AcessoRN::$TA_CREDENCIAL_ASSINATURA_PROCESSO ), InfraDTO::$OPER_IN );
    	$acessoDTO->setOrd('IdAcesso', InfraDTO::$TIPO_ORDENACAO_DESC );
    	
    	$acessoRN = new AcessoRN();
    	$arrCredenciaisComUnidadeValida = array();
    	$arrCredenciais = $acessoRN->listar( $acessoDTO );
    	    	
    	if( is_array( $arrCredenciais ) && count( $arrCredenciais ) > 0 ){
    		    		
    		foreach( $arrCredenciais as $itemCredencial ){
    			
    			//descobrir se a unidade vinculada a esta credencial ainda est� ativa
    			$idUnidade = $itemCredencial->getNumIdUnidade();
    			$unidadeDTO = new UnidadeDTO();
    			$unidadeDTO->retNumIdUnidade();
    			$unidadeDTO->retStrSinAtivo();
    			$unidadeDTO->setNumIdUnidade( $idUnidade );
    			$unidadeDTO->setStrSinAtivo('S');
    			
    			$retUnidadeDTO = $unidadeRN->pesquisar( $unidadeDTO );
    			    			
    			//encontrou uma unidade ativa com credencial, usar ela
    			if( is_array( $retUnidadeDTO ) && count( $retUnidadeDTO ) > 0 && $retUnidadeDTO[0] != null && $retUnidadeDTO[0]->isSetNumIdUnidade() ){
    				$idUnidadeSigiloso = $retUnidadeDTO[0]->getNumIdUnidade();
    				return $idUnidadeSigiloso;
    			}
    		}
    	}
    	
    	//====================================================================================
    	//FIM CASO 1 - Processo sigiloso que possui credencial apontada para unidade ativa
    	//====================================================================================
    	
    	//====================================================================================
    	//CASO 2 - Processo sigiloso com andamento de Credencial cassada ou revogada apontada para unidade ativa
    	//====================================================================================
    	
    	$objAtividadeBD = new AtividadeBD( $this->getObjInfraIBanco() );
    	$objAtividadeDTO = new AtividadeDTO();
    	$objAtividadeDTO->retNumIdTarefa();
    	$objAtividadeDTO->retStrNomeTarefa();
    	$objAtividadeDTO->retNumIdAtividade();
    	$objAtividadeDTO->retNumIdUnidade();
    	$objAtividadeDTO->retStrSiglaUnidade();
    	$objAtividadeDTO->retStrDescricaoUnidade();
    	
    	$objAtividadeDTO->setNumIdTarefa( 
    		array(
    	 		TarefaRN::$TI_PROCESSO_CASSACAO_CREDENCIAL,
    	 		TarefaRN::$TI_PROCESSO_CONCESSAO_CREDENCIAL,
    	 		TarefaRN::$TI_PROCESSO_CONCESSAO_CREDENCIAL_CASSADA,
    	 		TarefaRN::$TI_PROCESSO_RENUNCIA_CREDENCIAL,
    	 		TarefaRN::$TI_PROCESSO_ATIVACAO_CREDENCIAL,
    			TarefaRN::$TI_PROCESSO_TRANSFERENCIA_CREDENCIAL,
    	 		TarefaRN::$TI_CONCESSAO_CREDENCIAL_ASSINATURA
    	), InfraDTO::$OPER_IN);
    	
    	 $objAtividadeDTO->setDblIdProtocolo( $idProcedimento );
    	
    	 //ordenando pelo id da atividade, obtendo a ordem cronologica da tramitacao
    	 $objAtividadeDTO->setOrdNumIdAtividade(InfraDTO::$TIPO_ORDENACAO_DESC);

    	 $arrObjAtividadeDTO = $objAtividadeBD->listar( $objAtividadeDTO );

    	 if( is_array( $arrObjAtividadeDTO ) && count( $arrObjAtividadeDTO ) > 0){

    	 	foreach( $arrObjAtividadeDTO as $atividade ){
    	 		
    	 		//verificando se a unidade desta atividade est� ativa
    	 		$idUnidade = $atividade->getNumIdUnidade();
    	 		
    	 		$unidadeDTO = new UnidadeDTO();
    	 		$unidadeDTO->retNumIdUnidade();
    	 		$unidadeDTO->retStrSinAtivo();
    	 		$unidadeDTO->setNumIdUnidade( $idUnidade );
    	 		
    	 		$unidadeDTO = $unidadeRN->consultarRN0125( $unidadeDTO );
    	 		    	 		
    	 		//verificar se a unidade est� ativa
    	 		if( $unidadeDTO->getStrSinAtivo() == 'S' ){
    	 			
    	 			$idUnidadeSigiloso = $unidadeDTO->getNumIdUnidade();
    	 			return $idUnidadeSigiloso;
    	 			
    	 		}
    	 		
    	 	}
    	 	
    	 }
    	 
    	//====================================================================================
    	//CASO 3 - Nao possui credencial apontada para unidade ativa, os andamentos de concessao e revoga�ao de credencial nao estao apontados para unidade ativa
    	// Resta fazer a checagem "padrao" em todo o andamento do processo para ver a ultima unidade ativa por onde o processo tramitou
    	//====================================================================================
    	    	
    	//1 - obtendo TODAS as unidades por onde o processo ja tramitou    	
    	$objAtividadeDTO = new AtividadeDTO();
    	$objAtividadeDTO->retNumIdAtividade();
    	$objAtividadeDTO->retNumIdUnidade();
    	$objAtividadeDTO->retStrSiglaUnidade();
    	 
    	/* 
    	 * Tarefas que implicam na abertura do processo na Unidade  (ID/Nome):
    	 * MESCLANDO TAREFAS DE PROCESSOS PUBLICO/RESTRITO + SIGILOSO
    	   1 - Processo @NIVEL_ACESSO@@GRAU_SIGILO@ gerado @DATA_AUTUACAO@@HIPOTESE_LEGAL@
           21 - Remo��o de sobrestamento        
           29 - Reabertura do processo na unidade 
           32 - Processo remetido pela unidade @UNIDADE@ 
           64 - Reabertura do processo    
         */
    	
    	$objAtividadeDTO->setNumIdTarefa( 
    		  array(
    		  	TarefaRN::$TI_GERACAO_PROCEDIMENTO,
    			TarefaRN::$TI_REMOCAO_SOBRESTAMENTO,
    			TarefaRN::$TI_REABERTURA_PROCESSO_UNIDADE,
    			TarefaRN::$TI_PROCESSO_REMETIDO_UNIDADE,
    			TarefaRN::$TI_REABERTURA_PROCESSO_USUARIO
    		  ), InfraDTO::$OPER_IN);
    	
    	$objAtividadeDTO->setDblIdProtocolo( $idProcedimento );
    	
    	//ordenando pelo id da atividade, obtendo a ordem cronologica da tramitacao
    	$objAtividadeDTO->setOrdNumIdAtividade(InfraDTO::$TIPO_ORDENACAO_DESC);
    	
    	$arrObjAtividadeDTO = $objAtividadeBD->listar($objAtividadeDTO);

    	if( is_array( $arrObjAtividadeDTO ) && count( $arrObjAtividadeDTO ) > 0){

    		foreach( $arrObjAtividadeDTO as $atividade ){
	    		
    			$idUnidade = $atividade->getNumIdUnidade();
    			
    			$unidadeDTO = new UnidadeDTO();
    			$unidadeDTO->retNumIdUnidade();
    			$unidadeDTO->retStrSinAtivo();
    			$unidadeDTO->setNumIdUnidade( $idUnidade );
    			
    			$unidadeDTO = $unidadeRN->consultarRN0125( $unidadeDTO );
    			
    			//1- verificar se a unidade est� ativa
    			if( $unidadeDTO != null && $unidadeDTO->getStrSinAtivo() == 'S' ){
    				
    				$idUnidadeSigiloso = $unidadeDTO->getNumIdUnidade();
    				return $idUnidadeSigiloso;
    				
    			} 
    		    			
	    	}
	    		    	
    	} 
    	
    	//====================================================================================
    	//CASO 4 - Nao h� nenhuma unidade ativa dentre aquelas em que o processo tramitou, deve dar erro / msg de valida��o
    	//====================================================================================
    	if( $idUnidadeSigiloso == null ){
    		
    	}
    	
    	return $idUnidadeSigiloso;
    	
}

/**
 * Fun��o respons�vel por Retornar a �ltima unidade em que o processo EST� aberto agora (m�todo v�lido apenas para processos sigilosos)
 * @param $idProcedimento
 * @return  string $idUnidade
 */
protected function retornaUltimaUnidadeProcessoSigilosoAbertoConectado($idProcedimento){

	$objSEIRN = new SeiRN();
	$objProcedimentoDTO = new ProcedimentoDTO();
	$objProcedimentoDTO->retTodos(true);
	$objProcedimentoDTO->setDblIdProcedimento($idProcedimento);
	$objProcedimentoRN = new ProcedimentoRN();
	$objProcedimentoDTO = $objProcedimentoRN->consultarRN0201($objProcedimentoDTO);

	$objEntradaConsultaProcApi = new EntradaConsultarProcedimentoAPI();
	$objEntradaConsultaProcApi->setIdProcedimento($idProcedimento);
	$objEntradaConsultaProcApi->setSinRetornarUnidadesProcedimentoAberto('S');
	$objEntradaConsultaProcApi->setSinRetornarUltimoAndamento('S');
	$objEntradaConsultaProcApi->setSinRetornarAndamentoConclusao('N');

	$saidaConsultarProcedimentoAPI = $this->consultarProcedimento($objEntradaConsultaProcApi);

	/*  
	Tarefas que implicam na conclus�o do processo na Unidade  (ID - Nome):
    28 - Conclus�o do processo na unidade
    41 - Conclus�o autom�tica de processo na unidade
    63 - Processo conclu�do
    70 - Conclus�o Autom�tica de Processo do Usu�rio @USUARIO@
    77 - Ren�ncia de credencial
    117 Cancelamento de credencial por Coordenador de Acervo do usu�rio na unidade
	*/
	
	//informa�oes das tarefas de conclusao de processo na unidade
	$tarefaRN = new TarefaRN();
	$tarefaDTO = new TarefaDTO();
	$tarefaDTO->retNumIdTarefa( );
	$tarefaDTO->retStrNome( );
	$tarefaDTO->setNumIdTarefa( array( 
			TarefaRN::$TI_CONCLUSAO_PROCESSO_UNIDADE, //28
			TarefaRN::$TI_CONCLUSAO_AUTOMATICA_UNIDADE, //41
			TarefaRN::$TI_CONCLUSAO_PROCESSO_USUARIO, //63
			TarefaRN::$TI_CONCLUSAO_AUTOMATICA_USUARIO, //70 
			TarefaRN::$TI_PROCESSO_RENUNCIA_CREDENCIAL, //77 
			TarefaRN::$TI_PROCESSO_CANCELAMENTO_CREDENCIAL //117
	), InfraDTO::$OPER_IN );
	
	$arrTarefaDTO = $tarefaRN->listar( $tarefaDTO );
	$tarefaDTO = $arrTarefaDTO[0];

	//lista de unidades nas quais o processo ainda encontra-se aberto
	$arrUnidadesAbertas = $saidaConsultarProcedimentoAPI->getUnidadesProcedimentoAberto();
	
	//o processo encontra-se aberto em pelo menos uma unidade
	if( is_array( $arrUnidadesAbertas ) && count( $arrUnidadesAbertas ) > 0 ){
		$arrIdUnidade = array();
		foreach( $arrUnidadesAbertas as $unidadeAberta ){
			$arrIdUnidade[] = $unidadeAberta->getUnidade()->getIdUnidade();
		}

		//o processo nao esta aberto em nenhuma unidade, nao ha id para ser retornado
		if (count($arrIdUnidade)<1){
			return null;
		}

		$objEntradaAndamentos = new EntradaListarAndamentosAPI();
		$objEntradaAndamentos->setIdProcedimento( $idProcedimento );
		$objEntradaAndamentos->setTarefas( array( TarefaRN::$TI_GERACAO_PROCEDIMENTO , TarefaRN::$TI_REABERTURA_PROCESSO_UNIDADE, TarefaRN::$TI_PROCESSO_REMETIDO_UNIDADE ) );
		$arrAndamentos = $objSEIRN->listarAndamentos( $objEntradaAndamentos );
		foreach( $arrAndamentos as $andamento ){
			$idUnidadeAndamento = $andamento->getUnidade()->getIdUnidade();
			if( in_array( $idUnidadeAndamento, $arrIdUnidade ) ){
				return $idUnidadeAndamento;
			}
			 
		}
	//o processo nao esta aberto em nenhuma unidade, nao ha id para ser retornado
	} else {
		return null;
	}

}
	 
}

?>