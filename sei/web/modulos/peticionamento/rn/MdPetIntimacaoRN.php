<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 14/03/2017 - criado por pedro.cast
 *
 * Versão do Gerador de Código: 1.40.0
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntimacaoRN extends InfraRN
{

    //Id Tarefa Módulo
    public static $ID_TAREFA_MODULO_CADASTRO_INTIMACAO = 'MD_PET_INTIMACAO_EXPEDIDA';

    //tipo acesso externo
    public static $TIPO_ACESSO_EXTERNO_INTEGRAL = 'I';
    public static $TIPO_ACESSO_EXTERNO_PARCIAL = 'P';

    //Id Situação Intimação
    public static $INTIMACAO_PENDENTE = '1';
    public static $INTIMACAO_CUMPRIDA_PRAZO = '2';
    public static $INTIMACAO_CUMPRIDA_POR_ACESSO = '3';
    public static $INTIMACAO_RESPONDIDA = '4';
    public static $INTIMACAO_PRAZO_VENCIDO = '5';
    public static $TODAS = '6';

    //String Situação Intimação
    public static $STR_INTIMACAO_PENDENTE = 'Pendente';
    public static $STR_INTIMACAO_CUMPRIDA_PRAZO = 'Cumprida por Decurso do Prazo Tácito';
    public static $STR_INTIMACAO_CUMPRIDA_POR_ACESSO = 'Cumprida por Consulta Direta';
    public static $STR_INTIMACAO_RESPONDIDA = 'Respondida';
    public static $STR_INTIMACAO_PRAZO_VENCIDO = 'Pendente de Resposta com Prazo Externo Vencido';
    public static $STR_TODAS = 'Todas';
    public static $STR_SITUACAO_NAO_CADASTRADA = 'Situação não cadastrada';

    //TP = Tipo de Aceite da Intimação
    public static $TP_AUTOMATICO_POR_DECURSO_DE_PRAZO = 'A';
    public static $TP_MANUAL_USUARIO_EXTERNO_ACEITE = 'U';

    //Utilizado para Lancamento do Andamento no Processo
    public static $STR_TP_CUMPRIMENTO_LANC_ACESSO_DIRETO = 'consulta direta';
    public static $STR_TP_MANUAL_USUARIO_EXTERNO_ACEITE = 'decurso do prazo Tácito';

    //String Situação Intimação Aceite
    public static $STR_INTIMACAO_PENDENTE_ACEITE = 'Pendente';
    public static $STR_INTIMACAO_CUMPRIDA_PRAZO_ACEITE = 'Por Decurso do Prazo Tácito';
    public static $STR_INTIMACAO_CUMPRIDA_POR_ACESSO_ACEITE = 'Consulta Direta';
    public static $STR_INTIMACAO_RESPONDIDA_ACEITE = 'Respondida';
    public static $STR_INTIMACAO_PRAZO_VENCIDO_ACEITE = 'Pendente de Resposta com Prazo Externo Vencido';
    public static $STR_TODAS_ACEITE = 'Todas';
    public static $STR_SITUACAO_NAO_CADASTRADA_ACEITE = 'Situação não cadastrada';

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function validarNumIdMdPetIntTipoIntimacao(MdPetIntimacaoDTO $objMdPetIntimacaoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetIntimacaoDTO->getNumIdMdPetIntTipoIntimacao())) {
            $objInfraException->adicionarValidacao('Tipo de Intimação não informado.');
        }
    }

    private function validarStrSinTipoAcessoProcesso(MdPetIntimacaoDTO $objMdPetIntimacaoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetIntimacaoDTO->getStrSinTipoAcessoProcesso())) {
            $objInfraException->adicionarValidacao('Sinalizador de Tipo de Acesso não informado.');
        }
    }

    protected function cadastrarControlado(MdPetIntimacaoDTO $objMdPetIntimacaoDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_intimacao_cadastrar');

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarNumIdMdPetIntTipoIntimacao($objMdPetIntimacaoDTO, $objInfraException);
            $this->validarStrSinTipoAcessoProcesso($objMdPetIntimacaoDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdPetIntimacaoBD = new MdPetIntimacaoBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntimacaoBD->cadastrar($objMdPetIntimacaoDTO);

            //Auditoria

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando Intimação.', $e);
        }
    }

    protected function alterarControlado(MdPetIntimacaoDTO $objMdPetIntimacaoDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_intimacao_alterar');

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdPetIntimacaoDTO->isSetNumIdMdPetIntTipoIntimacao()) {
                $this->validarNumIdMdPetIntTipoIntimacao($objMdPetIntimacaoDTO, $objInfraException);
            }
            if ($objMdPetIntimacaoDTO->isSetStrSinTipoAcessoProcesso()) {
                $this->validarStrSinTipoAcessoProcesso($objMdPetIntimacaoDTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();

            $objMdPetIntimacaoBD = new MdPetIntimacaoBD($this->getObjInfraIBanco());
            $objMdPetIntimacaoBD->alterar($objMdPetIntimacaoDTO);

            //Auditoria

        } catch (Exception $e) {
            throw new InfraException('Erro alterando Intimação.', $e);
        }
    }

    protected function excluirControlado($arrObjMdPetIntimacaoDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_intimacao_excluir');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntimacaoBD = new MdPetIntimacaoBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdPetIntimacaoDTO); $i++) {
                $objMdPetIntimacaoBD->excluir($arrObjMdPetIntimacaoDTO[$i]);
            }

            //Auditoria

        } catch (Exception $e) {
            throw new InfraException('Erro excluindo Intimação.', $e);
        }
    }

    protected function consultarConectado(MdPetIntimacaoDTO $objMdPetIntimacaoDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_intimacao_consultar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntimacaoBD = new MdPetIntimacaoBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntimacaoBD->consultar($objMdPetIntimacaoDTO);

            //Auditoria

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando Intimação.', $e);
        }
    }

    protected function listarConectado(MdPetIntimacaoDTO $objMdPetIntimacaoDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_intimacao_listar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntimacaoBD = new MdPetIntimacaoBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntimacaoBD->listar($objMdPetIntimacaoDTO);

            //Auditoria

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro listando Intimações.', $e);
        }
    }

    protected function contarConectado(MdPetIntimacaoDTO $objMdPetIntimacaoDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_intimacao_listar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntimacaoBD = new MdPetIntimacaoBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntimacaoBD->contar($objMdPetIntimacaoDTO);

            //Auditoria

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando Intimações.', $e);
        }
    }

    private function _retornaDocumentoPorProtocolo($idDocumento)
    {
        $objDocumentoRN = new DocumentoRN();

        $objDocumentoDTO = new DocumentoDTO();
        $objDocumentoDTO->retNumIdOrgaoUnidadeResponsavel();
        $objDocumentoDTO->retStrProtocoloDocumentoFormatado();
        $objDocumentoDTO->retStrNomeSerie();
        $objDocumentoDTO->retStrNomeArvore();
        $objDocumentoDTO->retStrDescricaoProtocolo();
        $objDocumentoDTO->retStrNumero();
        $objDocumentoDTO->retNumIdSerie();
        $objDocumentoDTO->setDblIdDocumento($idDocumento);
        $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

        return $objDocumentoDTO;
    }

    private function _retornaProcedimentoPorId($idProtocolo)
    {
        $objProtocoloRN = new ProcedimentoRN();

        $objProtocoloDTO = new ProcedimentoDTO();
        $objProtocoloDTO->setDblIdProcedimento($idProtocolo);
        $objProtocoloDTO->retStrNomeTipoProcedimento();
        $objProtocoloDTO->retStrProtocoloProcedimentoFormatado();

        return $objProtocoloRN->consultarRN0201($objProtocoloDTO);
    }

    public function dadosIntimacaoByID($id_intimacao, $id_contato)
    {

        //Busca Intimacao
        $objMdPetIntimacaoDTO = new MdPetIntimacaoDTO();
        $objMdPetIntimacaoDTO->retTodos();
        $objMdPetIntimacaoDTO->setNumIdMdPetIntimacao($id_intimacao);

        $objMdPetIntimacaoDTO = $this->consultar($objMdPetIntimacaoDTO);

        //Get Data Intimação
        $objMdPetIntDestinatarioRN = new MdPetIntRelDestinatarioRN();
        $objMdDestDTO = $objMdPetIntDestinatarioRN->consultarDadosIntimacao($id_intimacao, true, $id_contato);
        $dtIntimacao = $objMdDestDTO->getDthDataCadastro();
        $idRelDest = $objMdDestDTO->getNumIdMdPetIntRelDestinatario();

        //Busca dados do Contato
        $objContatoDTO = new ContatoDTO();
        $objContatoDTO->retTodos();
        $objContatoDTO->setNumIdContato($id_contato);

        $objContatoRN = new ContatoRN();
        $objContatoDTO = $objContatoRN->consultarRN0324($objContatoDTO);

        //Busca Tipos de Resposta
        $objMdPetIntRelTipoRespDTO = new MdPetIntRelTipoRespDTO();
        $objMdPetIntRelTipoRespDTO->retTodos();
        $objMdPetIntRelTipoRespDTO->setNumIdMdPetIntimacao($id_intimacao);

        $objMdPetIntRelTipoRespRN = new MdPetIntRelTipoRespRN();
        $objMdPetIntRelTipoRespDTO = $objMdPetIntRelTipoRespRN->listar($objMdPetIntRelTipoRespDTO);

        $tiposResposta = MdPetIntTipoIntimacaoINT::montaSelectTipoRespostaIntimacao($objMdPetIntimacaoDTO->getNumIdMdPetIntTipoIntimacao(), false);
        $strSelect = '';
        foreach ($tiposResposta as $id => $tipoResposta) {
            $tipoResposta = explode('±', $tipoResposta);
            $checked = '';
            foreach ($objMdPetIntRelTipoRespDTO as $objTipoResposta) {
                if ($tipoResposta[0] == $objTipoResposta->getNumIdMdPetIntTipoResp()) {
                    $strSelect .= '<input type="checkbox" disabled="disabled" class="infraCheckbox" checked="checked"/><label class="infraLabelOpcional">' . $tipoResposta[1] . '</label><br/>';
                }
            }
        }

        //Busca Documentos da Intimação
        $objMdPetIntDocumentoDTO = new MdPetIntProtocoloDTO();
        $objMdPetIntDocumentoDTO->retTodos(null);
        $objMdPetIntDocumentoDTO->retNumIdTipoProcedimento();
        $objMdPetIntDocumentoDTO->setNumIdMdPetIntimacao($id_intimacao);
		$objMdPetIntDocumentoDTO = (new MdPetIntProtocoloRN())->listar($objMdPetIntDocumentoDTO);
	
	    $idTipoProcesso = !empty($objMdPetIntDocumentoDTO) ? $objMdPetIntDocumentoDTO[0]->getNumIdTipoProcedimento() : null;

        $arr_protocolos_anexos = '';

        foreach ($objMdPetIntDocumentoDTO as $i => $docsIntimacao) {
            if ($docsIntimacao->getStrSinPrincipal() == 'N') {
                $objDocumentoDTO = $this->_retornaDocumentoPorProtocolo($docsIntimacao->getDblIdProtocolo());

                if (!is_null($objDocumentoDTO)) {
                    $strProtocoloDocumentoFormatado = $objDocumentoDTO->getStrProtocoloDocumentoFormatado();
                    $arr_protocolos_anexos .= '<option>'.$objDocumentoDTO->getStrNomeSerie().' '.$objDocumentoDTO->getStrNomeArvore().' '.$objDocumentoDTO->getStrNumero().' (' . $strProtocoloDocumentoFormatado . ')</option>';
                } else {
                    $objProcedimentoDTO = $this->_retornaProcedimentoPorId($docsIntimacao->getDblIdProtocolo());
                    if (!is_null($objProcedimentoDTO)) {
                        $str = PaginaSEI::tratarHTML($objProcedimentoDTO->getStrNomeTipoProcedimento() . ' (' . $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado() . ')');
                        $arr_protocolos_anexos .= '<option>' . $str . '</option>';
                    }
                }
            }
        }

        //Protocolos Disponibilizados
        $objMdPetIntDocDisponivelDTO = new MdPetIntProtDisponivelDTO();
        $objMdPetIntDocDisponivelDTO->retTodos();
        $objMdPetIntDocDisponivelDTO->setNumIdMdPetIntimacao($id_intimacao);
		$objMdPetIntDocDisponivelDTO = (new MdPetIntProtDisponivelRN())->listar($objMdPetIntDocDisponivelDTO);

        $arr_protocolos_disponibilizados = '';
        foreach ($objMdPetIntDocDisponivelDTO as $i => $docsDisponibilizados) {
            $objDocumentoDTO = new DocumentoDTO();
            $objDocumentoDTO->retNumIdOrgaoUnidadeResponsavel();
            $objDocumentoDTO->retStrProtocoloDocumentoFormatado();
            $objDocumentoDTO->retStrNomeSerie();
            $objDocumentoDTO->retStrNomeArvore();
            $objDocumentoDTO->retStrNumero();
            $objDocumentoDTO->retNumIdSerie();
            $objDocumentoDTO->setDblIdDocumento($docsDisponibilizados->getDblIdProtocolo());
            $objDocumentoRN = new DocumentoRN();
            $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

            if (!is_null($objDocumentoDTO)) {
                $strProtocoloDocumentoFormatado = $objDocumentoDTO->getStrProtocoloDocumentoFormatado();
                $arr_protocolos_disponibilizados .= '<option>'.$objDocumentoDTO->getStrNomeSerie().' '.$objDocumentoDTO->getStrNomeArvore().' '.$objDocumentoDTO->getStrNumero().' (' . $strProtocoloDocumentoFormatado . ')</option>';
            } else {
                $objProcedimentoDTO = $this->_retornaProcedimentoPorId($docsDisponibilizados->getDblIdProtocolo());
                if (!is_null($objProcedimentoDTO)) {
                    $str = PaginaSEI::tratarHTML($objProcedimentoDTO->getStrNomeTipoProcedimento() . ' (' . $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado() . ')');
                    $arr_protocolos_anexos .= '<option>' . $str . '</option>';
                }
            }
        }

        $arrSituacao = MdPetIntRelDestinatarioINT::getArraySituacaoRelatorio();
        $strSituacao = !is_null($objMdDestDTO->getStrStaSituacaoIntimacao()) && $objMdDestDTO->getStrStaSituacaoIntimacao() != 0 ? $arrSituacao[$objMdDestDTO->getStrStaSituacaoIntimacao()] : MdPetIntimacaoRN::$STR_SITUACAO_NAO_CADASTRADA;

        $dadosIntimacao['id_dest_int'] = $idRelDest;
        $dadosIntimacao['id_intimacao'] = $id_intimacao;
        $dadosIntimacao['id_contato'] = $id_contato;
        $dadosIntimacao['nome'] = $objContatoDTO->getStrNome();
        $dadosIntimacao['email'] = $objContatoDTO->getStrSigla();
        $dadosIntimacao['cpf'] = $objContatoDTO->getDblCpf();
        $dadosIntimacao['data_geracao'] = $dtIntimacao;
        $dadosIntimacao['situacao'] = $strSituacao;
        $dadosIntimacao['tipo_intimacao'] = $objMdPetIntimacaoDTO->getNumIdMdPetIntTipoIntimacao();
        $dadosIntimacao['arr_tipo_resposta'] = $strSelect;
        $dadosIntimacao['documento_principal'] = (count($objMdPetIntDocumentoDTO) > 1) ? true : false;
        $dadosIntimacao['arr_protocolos_anexos'] = $arr_protocolos_anexos;
        $dadosIntimacao['tipo_acesso'] = $objMdPetIntimacaoDTO->getStrSinTipoAcessoProcesso();
        $dadosIntimacao['arr_protocolos_disponibilizados'] = $arr_protocolos_disponibilizados;
        $dadosIntimacao['id_tipo_processo'] = $idTipoProcesso;

        return $dadosIntimacao;

    }


    //JUridico


    public function dadosIntimacaoByIDJuridico($id_intimacao, $id_contato)
    {

        //Busca Intimacao
        $objMdPetIntimacaoDTO = new MdPetIntimacaoDTO();
        $objMdPetIntimacaoDTO->retTodos();
        $objMdPetIntimacaoDTO->setNumIdMdPetIntimacao($id_intimacao);

        $objMdPetIntimacaoDTO = $this->consultar($objMdPetIntimacaoDTO);

        //Get Data Intimação
        $objMdPetIntDestinatarioRN = new MdPetIntRelDestinatarioRN();
        $objMdDestDTO = $objMdPetIntDestinatarioRN->consultarDadosIntimacaoPorDestinario($id_intimacao, true, $id_contato);
        $dtIntimacao = $objMdDestDTO->getDthDataCadastro();
        $idRelDest = $objMdDestDTO->getNumIdMdPetIntRelDestinatario();

        //Busca dados do Contato
        $objContatoDTO = new ContatoDTO();
        $objContatoDTO->retTodos();
        $objContatoDTO->setNumIdContato($id_contato);

        $objContatoRN = new ContatoRN();
        $objContatoDTO = $objContatoRN->consultarRN0324($objContatoDTO);

        //Busca Tipos de Resposta
        $objMdPetIntRelTipoRespDTO = new MdPetIntRelTipoRespDTO();
        $objMdPetIntRelTipoRespDTO->retTodos();
        $objMdPetIntRelTipoRespDTO->setNumIdMdPetIntimacao($id_intimacao);

        $objMdPetIntRelTipoRespRN = new MdPetIntRelTipoRespRN();
        $objMdPetIntRelTipoRespDTO = $objMdPetIntRelTipoRespRN->listar($objMdPetIntRelTipoRespDTO);

        $tiposResposta = MdPetIntTipoIntimacaoINT::montaSelectTipoRespostaIntimacao($objMdPetIntimacaoDTO->getNumIdMdPetIntTipoIntimacao(), false);
        $strSelect = '';
        foreach ($tiposResposta as $id => $tipoResposta) {
            $tipoResposta = explode('±', $tipoResposta);
            $checked = '';
            foreach ($objMdPetIntRelTipoRespDTO as $objTipoResposta) {
                if ($tipoResposta[0] == $objTipoResposta->getNumIdMdPetIntTipoResp()) {
                    $strSelect .= '<input type="checkbox" disabled="disabled" class="infraCheckbox" checked="checked"/><label class="infraLabelOpcional">' . $tipoResposta[1] . '</label><br/>';
                }
            }
        }

        //Busca Documentos da Intimação
        $objMdPetIntDocumentoDTO = new MdPetIntProtocoloDTO();
        $objMdPetIntDocumentoDTO->retTodos(null);
        $objMdPetIntDocumentoDTO->retNumIdTipoProcedimento();
        $objMdPetIntDocumentoDTO->setNumIdMdPetIntimacao($id_intimacao);
        $objMdPetIntDocumentoDTO = (new MdPetIntProtocoloRN())->listar($objMdPetIntDocumentoDTO);
        
        $idTipoProcesso = !empty($objMdPetIntDocumentoDTO) ? $objMdPetIntDocumentoDTO[0]->getNumIdTipoProcedimento() : null;

        $arr_protocolos_anexos = '';

        foreach ($objMdPetIntDocumentoDTO as $i => $docsIntimacao) {
            if ($docsIntimacao->getStrSinPrincipal() == 'N') {
                $objDocumentoDTO = $this->_retornaDocumentoPorProtocolo($docsIntimacao->getDblIdProtocolo());

                if (!is_null($objDocumentoDTO)) {
                    $strProtocoloDocumentoFormatado = $objDocumentoDTO->getStrProtocoloDocumentoFormatado();
                    $arr_protocolos_anexos .= '<option>'.$objDocumentoDTO->getStrNomeSerie().' '.$objDocumentoDTO->getStrNomeArvore().' '.$objDocumentoDTO->getStrNumero().' (' . $strProtocoloDocumentoFormatado . ')</option>';
                } else {
                    $objProcedimentoDTO = $this->_retornaProcedimentoPorId($docsIntimacao->getDblIdProtocolo());
                    if (!is_null($objProcedimentoDTO)) {
                        $str = PaginaSEI::tratarHTML($objProcedimentoDTO->getStrNomeTipoProcedimento() . ' (' . $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado() . ')');
                        $arr_protocolos_anexos .= '<option>' . $str . '</option>';
                    }
                }


            }
        }

        //Protocolos Disponibilizados
        $objMdPetIntDocDisponivelDTO = new MdPetIntProtDisponivelDTO();
        $objMdPetIntDocDisponivelDTO->retTodos();
        $objMdPetIntDocDisponivelDTO->setNumIdMdPetIntimacao($id_intimacao);

        $objMdPetIntDocDisponivelRN = new MdPetIntProtDisponivelRN();
        $objMdPetIntDocDisponivelDTO = $objMdPetIntDocDisponivelRN->listar($objMdPetIntDocDisponivelDTO);

        $arr_protocolos_disponibilizados = '';
        foreach ($objMdPetIntDocDisponivelDTO as $i => $docsDisponibilizados) {
            $objDocumentoDTO = new DocumentoDTO();
            $objDocumentoDTO->retNumIdOrgaoUnidadeResponsavel();
            $objDocumentoDTO->retStrProtocoloDocumentoFormatado();
            $objDocumentoDTO->retStrNomeSerie();
            $objDocumentoDTO->retStrNomeArvore();
            $objDocumentoDTO->retStrNumero();
            $objDocumentoDTO->retNumIdSerie();
            $objDocumentoDTO->setDblIdDocumento($docsDisponibilizados->getDblIdProtocolo());
            $objDocumentoRN = new DocumentoRN();
            $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

            if (!is_null($objDocumentoDTO)) {
                $strProtocoloDocumentoFormatado = $objDocumentoDTO->getStrProtocoloDocumentoFormatado();
                $arr_protocolos_disponibilizados .= '<option>'.$objDocumentoDTO->getStrNomeSerie().' '.$objDocumentoDTO->getStrNomeArvore().' '.$objDocumentoDTO->getStrNumero().' (' . $strProtocoloDocumentoFormatado . ')</option>';
            } else {
                $objProcedimentoDTO = $this->_retornaProcedimentoPorId($docsDisponibilizados->getDblIdProtocolo());
                if (!is_null($objProcedimentoDTO)) {
                    $str = PaginaSEI::tratarHTML($objProcedimentoDTO->getStrNomeTipoProcedimento() . ' (' . $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado() . ')');
                    $arr_protocolos_anexos .= '<option>' . $str . '</option>';
                }
            }
        }

        $arrSituacao = MdPetIntRelDestinatarioINT::getArraySituacaoRelatorio();
        $strSituacao = !is_null($objMdDestDTO->getStrStaSituacaoIntimacao()) && $objMdDestDTO->getStrStaSituacaoIntimacao() != 0 ? $arrSituacao[$objMdDestDTO->getStrStaSituacaoIntimacao()] : MdPetIntimacaoRN::$STR_SITUACAO_NAO_CADASTRADA;

        $dadosIntimacao['id_dest_int'] = $idRelDest;
        $dadosIntimacao['id_intimacao'] = $id_intimacao;
        $dadosIntimacao['id_contato'] = $id_contato;
        $dadosIntimacao['nome'] = $objContatoDTO->getStrNome();
        $dadosIntimacao['email'] = $objContatoDTO->getStrSigla();
        $dadosIntimacao['cnpj'] = $objContatoDTO->getStrCnpj();
        $dadosIntimacao['data_geracao'] = $dtIntimacao;
        $dadosIntimacao['situacao'] = $strSituacao;
        $dadosIntimacao['tipo_intimacao'] = $objMdPetIntimacaoDTO->getNumIdMdPetIntTipoIntimacao();
        $dadosIntimacao['arr_tipo_resposta'] = $strSelect;
        $dadosIntimacao['documento_principal'] = (count($objMdPetIntDocumentoDTO) > 1) ? true : false;
        $dadosIntimacao['arr_protocolos_anexos'] = $arr_protocolos_anexos;
        $dadosIntimacao['tipo_acesso'] = $objMdPetIntimacaoDTO->getStrSinTipoAcessoProcesso();
        $dadosIntimacao['arr_protocolos_disponibilizados'] = $arr_protocolos_disponibilizados;
        $dadosIntimacao['id_tipo_processo'] = $idTipoProcesso;

        return $dadosIntimacao;

    }

    protected function retornaIdDocumentoPrincipalIntimacaoConectado($arr)
    {
        $idIntimacao = current($arr);
        $idDocumento = null;

        $objMdPetIntDocDTO = new MdPetIntProtocoloDTO();
        $objMdPetIntDocRN = new MdPetIntProtocoloRN();

        if (is_array($idIntimacao)) {
            $objMdPetIntDocDTO->setNumIdMdPetIntimacao($idIntimacao, InfraDTO::$OPER_IN);
        } else {
            $objMdPetIntDocDTO->setNumIdMdPetIntimacao($idIntimacao);
        }
        $objMdPetIntDocDTO->setStrSinPrincipal('S');
        $objMdPetIntDocDTO->retDblIdDocumento();
        $arrObjMdPetIntDocDTO = $objMdPetIntDocRN->listar($objMdPetIntDocDTO);

        if (!is_null($arrObjMdPetIntDocDTO)) {
            $obj = count($arrObjMdPetIntDocDTO) > 0 ? current($arrObjMdPetIntDocDTO) : null;
            $idDocumento = $obj ? $obj->getDblIdDocumento() : null;
        }

        return $idDocumento;
    }

    protected function retornaIdDocumentoPrincipalIntimacaoAcaoConectado($arr)
    {
        $idIntimacao = $arr;
        $idDocumento = null;

        $objMdPetIntDocDTO = new MdPetIntProtocoloDTO();
        $objMdPetIntDocRN = new MdPetIntProtocoloRN();

        if (is_array($idIntimacao)) {
            $objMdPetIntDocDTO->setNumIdMdPetIntimacao($idIntimacao, InfraDTO::$OPER_IN);
        } else {
            $objMdPetIntDocDTO->setNumIdMdPetIntimacao($idIntimacao);
        }

        $objMdPetIntDocDTO->setStrSinPrincipal('S');
        $objMdPetIntDocDTO->retDblIdDocumento();
        $arrObjMdPetIntDocDTO = $objMdPetIntDocRN->listar($objMdPetIntDocDTO);

        if (!is_null($arrObjMdPetIntDocDTO)) {
            $obj = count($arrObjMdPetIntDocDTO) > 0 ? current($arrObjMdPetIntDocDTO) : null;
            $idDocumento = $obj ? $obj->getDblIdDocumento() : null;
        }

        return $idDocumento;
    }

    protected function retornaDadosDocPrincipalIntimacaoConectado($dados)
    {

        $idDocumento = $this->retornaIdDocumentoPrincipalIntimacaoConectado($dados);

        if ($idDocumento) {

            $objDocumentoDTO = new DocumentoDTO();
            $objDocumentoDTO->setDblIdDocumento($idDocumento);
            $objDocumentoDTO->retStrProtocoloDocumentoFormatado();
            $objDocumentoDTO->retStrProtocoloProcedimentoFormatado();
            $objDocumentoDTO->retStrNomeSerie();
            $objDocumentoDTO->retDblIdProcedimento();
            $objDocumentoDTO->retStrNumero();
            $objDocumentoDTO = (new DocumentoRN())->consultarRN0005($objDocumentoDTO);

            if ($objDocumentoDTO) {
                return [
                    $objDocumentoDTO->getStrProtocoloDocumentoFormatado(), 
                    $objDocumentoDTO->getStrNomeSerie(), 
                    $objDocumentoDTO->getDblIdProcedimento(), 
                    $idDocumento, 
                    $objDocumentoDTO->getStrNumero(),
                    $objDocumentoDTO->getStrProtocoloProcedimentoFormatado()
                ];
            }

        }

        return null;

    }

    public function retornaArrDocumentosAnexosIntimacaoConectado($idIntimacao){

        //Busca Documentos Anexos da Intimação
        $objMdPetIntDocumentoDTO = new MdPetIntProtocoloDTO();
        $objMdPetIntDocumentoDTO->retTodos(null);
        $objMdPetIntDocumentoDTO->setNumIdMdPetIntimacao((array)$idIntimacao, InfraDTO::$OPER_IN);
        $objMdPetIntDocumentoDTO = (new MdPetIntProtocoloRN())->listar($objMdPetIntDocumentoDTO);

        $arr_protocolos_anexos = [];

        foreach ($objMdPetIntDocumentoDTO as $i => $docsIntimacao) {
            if ($docsIntimacao->getStrSinPrincipal() == 'N') {
                $objDocumentoDTO = $this->_retornaDocumentoPorProtocolo($docsIntimacao->getDblIdProtocolo());

                if (!is_null($objDocumentoDTO)) {
                    array_push($arr_protocolos_anexos, $objDocumentoDTO->getStrNomeSerie().' '.$objDocumentoDTO->getStrNumero().' '.$objDocumentoDTO->getStrNomeArvore().' (' . $objDocumentoDTO->getStrProtocoloDocumentoFormatado() . ')');
                } else {
                    // Caso seja um Processo:
                    $objProcedimentoDTO = $this->_retornaProcedimentoPorId($docsIntimacao->getDblIdProtocolo());
                    if (!is_null($objProcedimentoDTO)) {
                        $str = PaginaSEI::tratarHTML($objProcedimentoDTO->getStrNomeTipoProcedimento() . ' (' . $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado() . ')');
                        array_push($arr_protocolos_anexos, $str);
                    }
                }

            }
        }

        return $arr_protocolos_anexos;

    }

    public function getTextoTolTipIntimacaoEletronica($arr)
    {

        $dtIntimacao = $arr[0];
        $docFormat = $arr[1];
        $docTipo = $arr[2];
        $docNum = $arr[3];
        $anexo = $arr[4];
        $cnpjs = $arr[5];
        $cpfs = $arr[6];
        $tooltipACG = $arr[7];

        $ToolTipTitulo = 'Intimação Eletrônica';

        $ToolTipText = 'Expedida em ';
        $ToolTipText .= $dtIntimacao . ' ';
        $ToolTipText .= '<br/>Documento Principal: ';
        $ToolTipText .= $docTipo . ' ';
        if ($docNum) {
            $ToolTipText .= $docNum . ' ';
        }
        $ToolTipText .= '(SEI nº ';
        $ToolTipText .= $docFormat;
        $ToolTipText .= ')';
        if ($anexo == 'N') {
            $ToolTipText .= '<span style=font-weight: ligther;> - Documento Anexo</span>';
        }

        if ($cnpjs || $cpfs) {
            $ToolTipText .= '<br/><br/>Destinatários:<br/>';
            if ($cnpjs) {
                foreach ($cnpjs as $emp) {
                    $ToolTipText .= $emp . '<br/>';
                }
            }

            if ($cpfs) {
                foreach ($cpfs as $pes) {
                    $ToolTipText .= $pes . '<br/>';
                }
            }
        }

        $ToolTipText .= "<br />Clique para consultar a Intimação e liberar o acesso aos documentos.";
        //Caso essa Intimação tenha texto de tooltip aguardando cumprimento geral é adicionado o mesmo
        // no tooltip geral do cumprir
        if (!is_null($tooltipACG)) {
            $ToolTipText .= "<br><br>" . $tooltipACG;
        }

        return array($ToolTipTitulo, $ToolTipText);
    }

    public function getTextoTolTipIntimacaoEletronicaVinculoInativo($dtIntimacao, $docFormat, $docTipo, $docNum, $anexo, $idContatoRepresentante, $idContatoVinculo, $arrPessoaJuridica, $arrPessoaFisica)
    {
        $ToolTipTitle = 'Intimação Eletrônica';
        $toolTipText = 'Expedida em ';
        $toolTipText .= $dtIntimacao . ' ';
        $toolTipText .= '<br/>Documento Principal: ';
        $toolTipText .= $docTipo . ' ';
        if ($docNum) {
            $toolTipText .= $docNum . ' ';
        }
        $toolTipText .= '(SEI nº ';
        $toolTipText .= $docFormat;
        $toolTipText .= ')';
        if ($anexo == 'N') {
            $toolTipText .= '<span style=font-weight: ligther;> - Documento Anexo</span>';
        }

        if (!is_null($arrPessoaJuridica) || !is_null($arrPessoaFisica)) {
            $toolTipText .= '<br/><br/>Destinatários:<br/>';
            if ($arrPessoaJuridica) {
                foreach ($arrPessoaJuridica as $emp) {
                    $toolTipText .= $emp . '<br/>';
                }
            }

            if ($arrPessoaFisica) {
                foreach ($arrPessoaFisica as $pes) {
                    $toolTipText .= $pes . '<br/>';
                }
            }
        }

        $toolTipText .= '<br />Você não possui mais permissão para cumprir a Intimação Eletrônica. Verifique seus Poderes de Representação.';

        return array($ToolTipTitle, $toolTipText);
    }

    public function getTextoTolTipIntimacaoEletronicaCumprida($arr)
    {

        $dtIntimacao = $arr[0];
        $docFormat = $arr[1];
        $docTipo = $arr[2];
        $docNum = $arr[3];
        $anexo = $arr[4];
        $cnpjs = $arr[5];
        $cpfs = $arr[6];
        $tooltipACG = $arr[7];

        $ToolTipTitle = 'Intimação Eletrônica';
        $ToolTipText = 'Cumprida em: ';
        $ToolTipText .= $dtIntimacao . ' ';
        $ToolTipText .= '<br/>Documento Principal: ';
        $ToolTipText .= $docTipo . ' ';
        if ($docNum) {
            $ToolTipText .= $docNum . ' ';
        }
        $ToolTipText .= '(SEI nº ';
        $ToolTipText .= $docFormat;
        $ToolTipText .= ')';
        if ($anexo == 'N') {
            $ToolTipText .= '<span style=font-weight: ligther;> - Documento Anexo</span>';
        }

        if ($cnpjs || $cpfs) {
            $ToolTipText .= '<br/><br/>Destinatários:<br/>';
            if ($cnpjs) {
                foreach ($cnpjs as $emp) {
                    $ToolTipText .= $emp . '<br/>';
                }
            }

            if ($cpfs) {
                foreach ($cpfs as $pes) {
                    $ToolTipText .= $pes . '<br/>';
                }
            }
        }

        // Caso essa Intimação tenha texto de tooltip aguardando cumprimento geral é adicionado o mesmo no tooltip geral do cumprir
        if (!is_null($tooltipACG)) {
            $ToolTipText .= "<br />Clique para consultar a Intimação e liberar o acesso aos documentos.";
            $ToolTipText .= "<br><br>" . $tooltipACG;
        }else{
            $ToolTipText .= '<br/>Acesse o documento liberado.';
        }

        return array($ToolTipTitle, $ToolTipText);
    }

    public function retornaLinkCompletoIconeIntimacao($arr)
    {
        $js = $arr[0];
        $img = $arr[1];
        $ToolTipTitle = $arr[2];
        $ToolTipText = $arr[3];

        $conteudoHtml = '<a onclick="' . $js . '"';
        $conteudoHtml .= 'onmouseover ="return infraTooltipMostrar(\'' . $ToolTipText . '\',\'' . $ToolTipTitle . '\')"';
        $conteudoHtml .= 'onmouseout="return infraTooltipOcultar()">';
        $conteudoHtml .= $img;
        $conteudoHtml .= '</a>';

        return $conteudoHtml;
    }

    protected function getUnidadeIntimacaoConectado($arr)
    {
        $idIntimacao = isset($arr[0]) ? $arr[0] : null;
        $retTodos = isset($arr[1]) ? $arr[1] : false;
        if ($idIntimacao) {
            $idDocumento = $this->retornaIdDocumentoPrincipalIntimacaoAcaoConectado($idIntimacao);
            $objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
            if (is_array($idIntimacao)) {
                $objMdPetIntRelDestDTO->setNumIdMdPetIntimacao($idIntimacao, InfraDTO::$OPER_IN);
            } else {
                $objMdPetIntRelDestDTO->setNumIdMdPetIntimacao($idIntimacao);
            }
            $objMdPetIntRelDestDTO->retNumIdUnidade();
            $objMdPetIntRelDestDTO->setNumMaxRegistrosRetorno(1);
            $objMdPetIntRelDestRN = new MdPetIntRelDestinatarioRN();
            $objMdPetIntRelDestDTO = $objMdPetIntRelDestRN->consultar($objMdPetIntRelDestDTO);

            $idUnidade = !is_null($objMdPetIntRelDestDTO) ? $objMdPetIntRelDestDTO->getNumIdUnidade() : null;
            if (is_null($idUnidade)) {
                $idUnidade = $this->_getIdUnidadeDocumentoPrincipal($arr, $idDocumento);
            }

            if ($idUnidade != '' && !is_null($idUnidade)) {
                $objUnidadeDTO = $this->retornaObjUnidadePorId($idUnidade, $retTodos);
            }

            return $objUnidadeDTO;

        }

        return null;
    }

    private function _buscarUnidadesVinculadasAoProcessoAtivas($idDocumento)
    {
        $objDocumentoDTO = $this->_retornaObjDocumento($idDocumento);

        if (!is_null($objDocumentoDTO)) {
            $idProcesso = $objDocumentoDTO->getDblIdProtocoloProtocolo();

        }

        return null;
    }

    public function retornaObjUnidadePorId($idUnidade, $retTodos)
    {

        $objUnidadeDTO = new UnidadeDTO();
        $objUnidadeRN = new UnidadeRN();
        $objUnidadeDTO->setNumIdUnidade($idUnidade);
        $objUnidadeDTO->setBolExclusaoLogica(false);

        if ($retTodos) {
            $objUnidadeDTO->retTodos(true);
        } else {
            $objUnidadeDTO->retStrSinAtivo();
            $objUnidadeDTO->retNumIdUnidade();
        }

        $objUnidadeDTO = $objUnidadeRN->consultarRN0125($objUnidadeDTO);

        return $objUnidadeDTO;
    }

    private function _getIdUnidadeDocumentoPrincipal($arr, $idDocumento)
    {

        if ($idDocumento) {
            $objDocumentoRN = new DocumentoRN();
            $objDocumentoDTO = new DocumentoDTO();
            $objDocumentoDTO->retNumIdUnidadeGeradoraProtocolo();
            $objDocumentoDTO->retDblIdProtocoloProtocolo();
            $objDocumentoDTO->setDblIdDocumento($idDocumento);
            $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

            $idUnidade = $objDocumentoDTO->getNumIdUnidadeGeradoraProtocolo();

            if ($idUnidade && $idUnidade != '') {
                return $idUnidade;
            }
        }

        return null;
    }


    protected function bloquearDocumentoControlado(DocumentoDTO $documentoDTO)
    {
        $documentoBD = new DocumentoBD($this->getObjInfraIBanco());

        //nao aplicando metodo alterar da RN de Documento por conta de regras de negocio muito especificas aplicadas ali
        $documentoDTO->setStrSinBloqueado('S');
        $documentoBD->alterar($documentoDTO);
        //remover a liberação de acesso externo //AcessoRN.excluir nao permite exclusao, por isso chame AcessoExternoBD diretamente daqui
    }

    private function _cadastrarObjIntimacao($dadosCadastro)
    {
        $objMdPetIntimacaoDTO = new MdPetIntimacaoDTO();
        $objMdPetIntimacaoDTO->setNumIdMdPetIntimacao(null);
        $objMdPetIntimacaoDTO->setNumIdMdPetIntTipoIntimacao($dadosCadastro['selTipoIntimacao']);
        $objMdPetIntimacaoDTO->setStrSinTipoAcessoProcesso($dadosCadastro['optIntegral'] ? $dadosCadastro['optIntegral'] : $dadosCadastro['optParcial']);
        return $this->cadastrar($objMdPetIntimacaoDTO);
    }

    private function _vincularTipoResposta($arrTiposResposta, $idIntimacao)
    {
        $qtdArrTiposResposta = is_array($arrTiposResposta) ? count($arrTiposResposta) : 0;
        if ($qtdArrTiposResposta > 0) {
            foreach ($arrTiposResposta as $tipoResp) {

                $objMdPetIntRelTipoRespDTO = new MdPetIntRelTipoRespDTO();
                $objMdPetIntRelTipoRespDTO->setNumIdMdPetIntRelTipoResp(null);
                $objMdPetIntRelTipoRespDTO->setStrSinAtivo('S');
                $objMdPetIntRelTipoRespDTO->setNumIdMdPetIntimacao($idIntimacao);
                $objMdPetIntRelTipoRespDTO->setNumIdMdPetIntTipoResp($tipoResp);

                $objMdPetIntRelTipoRespRN = new MdPetIntRelTipoRespRN();
                $objMdPetIntRelTipoRespRN->cadastrar($objMdPetIntRelTipoRespDTO);
            }
        }
    }

    private function _vincularDocumentosIntimacao($idProtocolo, $idIntimacao, $isPrincipal = false)
    {
        $sinPrincipal = $isPrincipal ? 'S' : 'N';
        $objMdPetIntDocumentoDTO = new MdPetIntProtocoloDTO();
        $objMdPetIntDocumentoDTO->setNumIdMdPetIntProtocolo(null);
        $objMdPetIntDocumentoDTO->setStrSinPrincipal($sinPrincipal);
        $objMdPetIntDocumentoDTO->setNumIdMdPetIntimacao($idIntimacao);
        $objMdPetIntDocumentoDTO->setDblIdProtocolo($idProtocolo);

        $objMdPetIntDocumentoRN = new MdPetIntProtocoloRN();
        $objMdPetIntDocumentoRN->cadastrar($objMdPetIntDocumentoDTO);

        $this->_bloquearDocIntimacao($idProtocolo);
    }

    private function _retornaObjDocumento($idProtocolo)
    {
        //Bloqueia conteudo documento
        $objDocumentoDTO = new DocumentoDTO();
        $objDocumentoRN = new DocumentoRN();
        $objDocumentoDTO->setDblIdDocumento($idProtocolo);
        $objDocumentoDTO->retTodos(true);

        $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

        return $objDocumentoDTO;
    }

    //bloquear (caneta preta) o doc principal da Intimação
    private function _bloquearDocIntimacao($idProtocolo)
    {
        $objDocDTO = new DocumentoDTO();
        $objDocDTO->retTodos();
        $objDocDTO->setDblIdDocumento($idProtocolo);
        $docBD = new DocumentoBD($this->getObjInfraIBanco());
        $objDocDTO = $docBD->consultar($objDocDTO);
        if (!is_null($objDocDTO)) {
            $this->bloquearDocumento($objDocDTO);
        }
    }

    private function _vincularAnexosIntimacao($dadosCadastro, $idIntimacao)
    {
        if (isset($dadosCadastro['rdoPossuiAnexo']) == 'S') {

            $arrHdnAnexo = $_POST['hdnIdsDocAnexo'] != '' ? json_decode($_POST['hdnIdsDocAnexo']) : null;

            foreach ($arrHdnAnexo as $idAnexosIntimacao) {
                $this->_vincularDocumentosIntimacao($idAnexosIntimacao, $idIntimacao);
            }
        }
    }

    private function _vincularDocumentosDisponiveis($dadosCadastro, $idIntimacao)
    {
        if ($dadosCadastro['optParcial'] == MdPetIntAcessoExternoDocumentoRN::$ACESSO_PARCIAL) {
            $arrHdnProtDispon = $_POST['hdnIdsDocDisponivel'] != '' ? json_decode($_POST['hdnIdsDocDisponivel']) : null;

            if (is_iterable($arrHdnProtDispon)) {
                foreach ($arrHdnProtDispon as $idAnexosDisponibilizados) {
                    //Vincula Tipo de Acesso Externo Ao Processo
                    $objMdPetIntDocDisponivelDTO = new MdPetIntProtDisponivelDTO();
                    $objMdPetIntDocDisponivelDTO->setNumIdMdPetIntimacao($idIntimacao);
                    $objMdPetIntDocDisponivelDTO->setDblIdProtocolo($idAnexosDisponibilizados);

                    $objMdPetIntDocDisponivelRN = new MdPetIntProtDisponivelRN();
                    $objMdPetIntDocDisponivelRN->cadastrar($objMdPetIntDocDisponivelDTO);
                }
            }
        }
    }
	
	private function _setaDataPrazoTacitoIntimacao($objMdPetIntRelDestinatario, $objProcedimento){
    	
    	$dataIntimacao = explode(" ", $objMdPetIntRelDestinatario->getDthDataCadastro())[0];
		
		// Pega o número de dias para cumprimento tácito de acordo com o tipo de Procedimento(Processo)
		$objMdPetIntPrazoTacitaDTO = ( new MdPetIntPrazoTacitaRN() )->getTipoPrazoTacitoGeralEspecifico( $objProcedimento->getNumIdTipoProcedimento() );
		$diasExp = !empty($objMdPetIntPrazoTacitaDTO) ? $objMdPetIntPrazoTacitaDTO->getNumNumPrazo() : 0;
		
		$dataPrazoTacito = (new MdPetIntPrazoRN())->calcularDataPrazo( $diasExp, $dataIntimacao, SessaoSEI::getInstance()->getNumIdOrgaoUnidadeAtual() );
		
		$objMdPetIntRelDestinatario->setDtaDataPrazoTacito($dataPrazoTacito);
		(new MdPetIntRelDestinatarioRN())->alterar($objMdPetIntRelDestinatario);
		
	}
	
	public function _getDataPrazoTacitoIntimacao($dthDataCadastro, $objProcedimento){
		
		$dataIntimacao = explode(" ", $dthDataCadastro)[0];
		
		// Pega o número de dias para cumprimento tácito de acordo com o tipo de Procedimento(Processo)
		$objMdPetIntPrazoTacitaDTO = ( new MdPetIntPrazoTacitaRN() )->getTipoPrazoTacitoGeralEspecifico( $objProcedimento->getNumIdTipoProcedimento() );
		$diasExp = !empty($objMdPetIntPrazoTacitaDTO) ? $objMdPetIntPrazoTacitaDTO->getNumNumPrazo() : 0;
		
		return (new MdPetIntPrazoRN())->calcularDataPrazo( $diasExp, $dataIntimacao, SessaoSEI::getInstance()->getNumIdOrgaoUnidadeAtual() );
		
	}
	
	public function _recalculaDataPrazoTacitoIntimacoes($dataFeriado){
		
		// RECALCULA A DATA DE CUMPRIMENTO TACITO CASO HAJAM REGISTROS NA DATA DO FERIADO CRIADO/ALTERADO:
		
		$mdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
		$mdPetIntRelDestinatarioDTO->setDtaDataPrazoTacito($dataFeriado);
		$mdPetIntRelDestinatarioDTO->setStrStaSituacaoIntimacao(MdPetIntimacaoRN::$INTIMACAO_PENDENTE); // APENAS AS INTIMACOES PENDENTES
		$mdPetIntRelDestinatarioDTO->retDblIdProcedimento();
		$mdPetIntRelDestinatarioDTO->retTodos();
		$arrMdPetIntRelDestinatarioDTO = (new MdPetIntRelDestinatarioRN())->listar($mdPetIntRelDestinatarioDTO);
		
		foreach($arrMdPetIntRelDestinatarioDTO as $intimacao){
			
			if(!empty($intimacao->getDblIdProcedimento()) && is_numeric($intimacao->getDblIdProcedimento())){
				
				// Pega tipo do Processo
				$objProcedimento = new ProcedimentoDTO();
				$objProcedimento->retNumIdTipoProcedimento();
				$objProcedimento->setDblIdProcedimento($intimacao->getDblIdProcedimento());
				$objProcedimento = (new ProcedimentoRN())->consultarRN0201($objProcedimento);
				
				if(!empty($objProcedimento)){
					
					$novaDataPrazoTacito = $this->_getDataPrazoTacitoIntimacao($intimacao->getDthDataCadastro(), $objProcedimento);
					
					$intimacao->setDtaDataPrazoTacito($novaDataPrazoTacito);
					(new MdPetIntRelDestinatarioRN())->alterar($intimacao);
					
				}
				
			}
			
		}
		
	}

    private function _vincularDestinatariosIntimacao($dadosCadastro, $idIntimacao, $dataHoraGeracao, $objProcedimento, $documento)
    {
        $objMdPetIntAcessoExtDocRN = new MdPetIntAcessoExternoDocumentoRN();
        $objMdPetAcessoExtRN = new MdPetAcessoExternoRN();

        $arrHdnDadosUsuario = PaginaSEI::getInstance()->getArrItensTabelaDinamica($dadosCadastro['hdnDadosUsuario']);

        // Destinatarios em massa: Recebendo e complementando os dados do novo array
        if(isset($dadosCadastro['hdnDadosUsuario2']) && !empty($dadosCadastro['hdnDadosUsuario2'])){

            $hdnDadosUsuario2 = PaginaSEI::getInstance()->getArrItensTabelaDinamica($dadosCadastro['hdnDadosUsuario2']); // Monta array ['IdContato', 'Nome-email-cpf']
            $arrayNormalizado = [];

            // Normaliza array para PF ['IdContato', 'Nome', 'Email', 'Cpf', 'Data', 'Situacao']
            foreach($hdnDadosUsuario2 as $item){
                $idContato      = $item[0];
                $complemento    = explode(' - ', $item[1]);
                array_push($arrayNormalizado, [$idContato, $complemento[0], $complemento[1], $complemento[2], InfraData::getStrDataAtual(), 'Em geração']);
            }

            $arrHdnDadosUsuario = $arrayNormalizado;

        }

        $destinatarios = array();
        foreach ($arrHdnDadosUsuario as $dadosUsuarios) {
            $arrParams = array($dadosUsuarios, $dadosCadastro);
            $idAcessoExt = $objMdPetIntAcessoExtDocRN->controlarAcessoExternoIntimacao($arrParams);
	
	        // Buscar Prazo Tacita
	        $dtaPrazoTacito = $this->_getDataPrazoTacitoIntimacao($dataHoraGeracao, $objProcedimento);

            $destinatarios[] = $dadosUsuarios[1];
            $objMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
            $objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntRelDestinatario(null);
            $objMdPetIntRelDestinatarioDTO->setStrSinAtivo('S');
            $objMdPetIntRelDestinatarioDTO->setStrSinPessoaJuridica('N');
            $objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntimacao($idIntimacao);
            $objMdPetIntRelDestinatarioDTO->setNumIdContato($dadosUsuarios[0]);
            $objMdPetIntRelDestinatarioDTO->setDthDataCadastro($dataHoraGeracao);
            $objMdPetIntRelDestinatarioDTO->setDtaDataPrazoTacito($dtaPrazoTacito);
            $objMdPetIntRelDestinatarioDTO->setStrStaSituacaoIntimacao(MdPetIntimacaoRN::$INTIMACAO_PENDENTE);
            $objMdPetIntRelDestinatarioDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
			$objMdPetIntRelDestinatario = (new MdPetIntRelDestinatarioRN())->cadastrar($objMdPetIntRelDestinatarioDTO);

            //Novo
            $objMdPetIntRelDestExternoDTO = new MdPetRelIntDestExternoDTO();
            $objMdPetIntRelDestExternoDTO->setNumIdMdPetIntRelDestinatario($objMdPetIntRelDestinatario->getNumIdMdPetIntRelDestinatario());
            $objMdPetIntRelDestExternoDTO->setNumIdAcessoExterno($idAcessoExt);
            $objMdPetIntRelDestExternoRN = new MdPetRelIntDestExternoRN();
            $objMdPetIntRelDestExternoRN->cadastrar($objMdPetIntRelDestExternoDTO);
            //Fim novo

            //verifica se existe procurador simples para a pessoa fí­sica em questão
            $dtoMdPetVincReptDTO = new MdPetVincRepresentantDTO();
            $dtoMdPetVincReptDTO->setNumIdContatoVinc($objMdPetIntRelDestinatarioDTO->getNumIdContato());
            $dtoMdPetVincReptDTO->retNumIdContatoVinc();
            $dtoMdPetVincReptDTO->retStrNomeProcurador();
            $dtoMdPetVincReptDTO->retStrRazaoSocialNomeVinc();
            $dtoMdPetVincReptDTO->retStrTipoRepresentante();
            $dtoMdPetVincReptDTO->retStrCNPJ();
            $dtoMdPetVincReptDTO->retStrEmail();
            $dtoMdPetVincReptDTO->retStrTipoRepresentante();
            $dtoMdPetVincReptDTO->retNumIdMdPetVinculoRepresent();
            $dtoMdPetVincReptDTO->retDthDataLimite();
            $dtoMdPetVincReptDTO->retStrStaEstado();
            $dtoMdPetVincReptDTO->retStrStaAbrangencia();
            $dtoMdPetVincReptDTO->retNumIdMdPetVinculoRepresent();
            $dtoMdPetVincReptDTO->retNumIdContatoProcurador();
            $dtoMdPetVincReptDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
            $dtoMdPetVincReptDTO->setStrTipoRepresentante([MdPetVincRepresentantRN::$PE_AUTORREPRESENTACAO], InfraDTO::$OPER_NOT_IN);
            $rnMdPetVincRepRN = new MdPetVincRepresentantRN();
            $arrObjMdPetVincRepresentantDTO = $rnMdPetVincRepRN->listar($dtoMdPetVincReptDTO);

            if (is_iterable($arrObjMdPetVincRepresentantDTO)) {
                foreach ($arrObjMdPetVincRepresentantDTO as $value) {
                    //Caso o tipo de procuração seja "Simples" será necessário
                    //fazer algumas Validações para criação dos destinatários externos da Intimação
                    if ($value->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES) {
                        $procuradorSimplesValido = FALSE;
                        $verificacaoCriteriosProcuracaoSimples = $this->_verificarCriteriosProcuracaoSimples($value->getNumIdMdPetVinculoRepresent(), $value->getStrStaEstado(), $value->getDthDataLimite(), $documento, $value->getStrStaAbrangencia());
                        if ($verificacaoCriteriosProcuracaoSimples) {
                            $procuradorSimplesValido = TRUE;
                        }
                    } else {
                        $procuradorSimplesValido = NULL;
                    }

                    $arrParams = array($value->getNumIdContatoProcurador(), $dadosCadastro, $procuradorSimplesValido);
                    $idAcessoExt = $objMdPetIntAcessoExtDocRN->controlarAcessoExternoIntimacao($arrParams);

                    //Criação dos destinatários externos caso o procurador simples seja valido ou tenha outro tipo de representação
                    if (($value->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES && !is_null($procuradorSimplesValido) && $procuradorSimplesValido) || $value->getStrTipoRepresentante() != MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES) {
                        //Novo DTO
                        $objMdPetIntRelDestExternoDTO = new MdPetRelIntDestExternoDTO();
                        $objMdPetIntRelDestExternoDTO->setNumIdMdPetIntRelDestinatario($objMdPetIntRelDestinatario->getNumIdMdPetIntRelDestinatario());
                        $objMdPetIntRelDestExternoDTO->setNumIdAcessoExterno($idAcessoExt);
                        $objMdPetIntRelDestExternoRN = new MdPetRelIntDestExternoRN();
                        $objMdPetIntRelDestExternoRN->cadastrar($objMdPetIntRelDestExternoDTO);

                        $objMdPetAcessoExtRN->atualizarIdAcessoExternoModulo($idAcessoExt, MdPetAcessoExternoRN::$MD_PET_INTIMACAO);
                    }

                    if ($procuradorSimplesValido) {
                        //Envia Email para procuradores
                        $dadosIntimacao = array();
                        $dadosIntimacao['POST'] = $dadosCadastro;
                        $dadosIntimacao['nome'] = $value->getStrNomeProcurador();
                        $dadosIntimacao['email'] = $value->getStrEmail();
                        $dadosIntimacao['dataHora'] = $dataHoraGeracao;
                        $dadosIntimacao['id_intimacao'] = $idIntimacao;
                        $dadosIntimacao['processo'] = $objProcedimento->getStrProtocoloProcedimentoFormatado();

                        $emailNotificacaoIntimacaoRN = new MdPetIntEmailNotificacaoRN();
                        $emailNotificacaoIntimacaoRN->enviarEmailIntimacao($dadosIntimacao);
                    }
                }
            }
            //Envia Email para intimado
            $dadosIntimacao = array();
            $dadosIntimacao['POST'] = $dadosCadastro;
            $dadosIntimacao['nome'] = $dadosUsuarios[1];
            $dadosIntimacao['email'] = $dadosUsuarios[2];
            $dadosIntimacao['dataHora'] = $dataHoraGeracao;
            $dadosIntimacao['id_intimacao'] = $idIntimacao;
            $dadosIntimacao['processo'] = $objProcedimento->getStrProtocoloProcedimentoFormatado();

            $emailNotificacaoIntimacaoRN = new MdPetIntEmailNotificacaoRN();
            $emailNotificacaoIntimacaoRN->enviarEmailIntimacao($dadosIntimacao);
        }

        return $destinatarios;
    }

    private function _vincularDestinatariosIntimacaoJuridico($dadosCadastro, $idIntimacao, $dataHoraGeracao, $objProcedimento, $documento)
    {

        $objMdPetIntAcessoExtDocRN = new MdPetIntAcessoExternoDocumentoRN();
        $objMdPetAcessoExtRN = new MdPetAcessoExternoRN();

        //Cada um dos representantes
        $destinatarios = array();

        //Array Intimações
        $intimacao = array();

        $arrHdnDadosUsuario = PaginaSEI::getInstance()->getArrItensTabelaDinamica($dadosCadastro['hdnDadosUsuario']);

        // Destinatarios em massa: Recebendo e complementando os dados do novo array
        if(isset($dadosCadastro['hdnDadosUsuario2']) && !empty($dadosCadastro['hdnDadosUsuario2'])){

            $hdnDadosUsuario2 = PaginaSEI::getInstance()->getArrItensTabelaDinamica($dadosCadastro['hdnDadosUsuario2']); // Monta array ['IdContato', 'Nome-email-cpf']
            $arrayNormalizado = [];

            // Normaliza array para PJ ['IdContato', 'Nome', 'Cnpj', 'Data', 'Situacao']
            foreach($hdnDadosUsuario2 as $item){
                $idContato      = $item[0];
                $complemento    = explode(' - ', $item[1]);
                array_push($arrayNormalizado, [$idContato, $complemento[0], $complemento[1], InfraData::getStrDataAtual(), 'Em geração']);
            }

            $arrHdnDadosUsuario = $arrayNormalizado;

        }

        foreach ($arrHdnDadosUsuario as $dadosUsuarios) {
	
	        // Buscar Prazo Tacita
	        $dtaPrazoTacito = $this->_getDataPrazoTacitoIntimacao($dataHoraGeracao, $objProcedimento);

            $destinatarios[] = $dadosUsuarios[1];
            $objMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
            $objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntRelDestinatario(null);
            $objMdPetIntRelDestinatarioDTO->setStrSinAtivo('S');
            $objMdPetIntRelDestinatarioDTO->setStrSinPessoaJuridica('S');
            $objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntimacao($idIntimacao);
            $objMdPetIntRelDestinatarioDTO->setNumIdContato($dadosUsuarios[0]);
            $objMdPetIntRelDestinatarioDTO->setDthDataCadastro($dataHoraGeracao);
            $objMdPetIntRelDestinatarioDTO->setDtaDataPrazoTacito($dtaPrazoTacito);
            $objMdPetIntRelDestinatarioDTO->setStrStaSituacaoIntimacao(MdPetIntimacaoRN::$INTIMACAO_PENDENTE);
            $objMdPetIntRelDestinatarioDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());

            $objMdPetIntRelDestinatarioRN = new MdPetIntRelDestinatarioRN();
            $objMdPetIntRelDestinatario = $objMdPetIntRelDestinatarioRN->cadastrar($objMdPetIntRelDestinatarioDTO);

            $intimacao [] = $objMdPetIntRelDestinatario->getNumIdMdPetIntRelDestinatario();

            $dtoMdPetVincReptDTO = new MdPetVincRepresentantDTO();
            $dtoMdPetVincReptDTO->setNumIdContatoVinc($objMdPetIntRelDestinatarioDTO->getNumIdContato());
            $dtoMdPetVincReptDTO->retNumIdContatoVinc();
            $dtoMdPetVincReptDTO->retStrNomeProcurador();
            $dtoMdPetVincReptDTO->retStrRazaoSocialNomeVinc();
            $dtoMdPetVincReptDTO->retStrTipoRepresentante();
            $dtoMdPetVincReptDTO->retStrCNPJ();
            $dtoMdPetVincReptDTO->retStrEmail();
            $dtoMdPetVincReptDTO->retStrTipoRepresentante();
            $dtoMdPetVincReptDTO->retNumIdMdPetVinculoRepresent();
            $dtoMdPetVincReptDTO->retDthDataLimite();
            $dtoMdPetVincReptDTO->retStrStaEstado();
            $dtoMdPetVincReptDTO->retStrStaAbrangencia();
            $dtoMdPetVincReptDTO->retNumIdMdPetVinculoRepresent();
            $dtoMdPetVincReptDTO->retNumIdContatoProcurador();
            $dtoMdPetVincReptDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
            $rnMdPetVincRepRN = new MdPetVincRepresentantRN();
            $arrObjMdPetVincRepresentantDTO = $rnMdPetVincRepRN->listar($dtoMdPetVincReptDTO);

            if (is_iterable($arrObjMdPetVincRepresentantDTO)) {

                foreach ($arrObjMdPetVincRepresentantDTO as $value) {
                    $liberarAcesso = true;
                    //Caso o tipo de procuração seja "Simples" será necessário
                    //fazer algumas Validações para criação dos destinatários externos da Intimação
                    if ($value->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES) {
                        $verificacaoCriteriosProcuracaoSimples = $this->_verificarCriteriosProcuracaoSimples($value->getNumIdMdPetVinculoRepresent(), $value->getStrStaEstado(), $value->getDthDataLimite(), $documento, $value->getStrStaAbrangencia());
                        //se tiver alguma validação falsa não serão feitos os cadastros abaixo
                        if (!$verificacaoCriteriosProcuracaoSimples) {
                            $liberarAcesso = false;
                        }
                    }
                    if ($liberarAcesso) {
                        $arrParams = array($value->getNumIdContatoProcurador(), $dadosCadastro);
                        $idAcessoExt = $objMdPetIntAcessoExtDocRN->controlarAcessoExternoIntimacao($arrParams);

                        //Novo DTO
                        $objMdPetIntRelDestExternoDTO = new MdPetRelIntDestExternoDTO();
                        $objMdPetIntRelDestExternoDTO->setNumIdMdPetIntRelDestinatario($objMdPetIntRelDestinatario->getNumIdMdPetIntRelDestinatario());
                        $objMdPetIntRelDestExternoDTO->setNumIdAcessoExterno($idAcessoExt);
                        $objMdPetIntRelDestExternoDTO->retNumIdMdPetIntRelDestinatario();
                        $objMdPetIntRelDestExternoRN = new MdPetRelIntDestExternoRN();
                        $arrObjMdPetIntRelDestExternoDTO = $objMdPetIntRelDestExternoRN->consultar($objMdPetIntRelDestExternoDTO);

                        if (is_null($arrObjMdPetIntRelDestExternoDTO)) {
                            $objMdPetIntRelDestExternoRN->cadastrarJuridico($objMdPetIntRelDestExternoDTO);
                        }

                        $objMdPetAcessoExtRN->atualizarIdAcessoExternoModulo($idAcessoExt, MdPetAcessoExternoRN::$MD_PET_INTIMACAO);

                        $this->_enviaEmailJuridico($dadosCadastro, $value, $dataHoraGeracao, $objProcedimento->getStrProtocoloProcedimentoFormatado(), $idIntimacao);
                    }
                }
            }
        }

        return $destinatarios;
    }

    function _verificarCriteriosProcuracaoSimples($idMdPetVinculoRepresent, $staEstado, $dataLimite, $documento, $staAbrangencia, $procedimento = null)
    {

        $confirmaCriterios = true;
        $dtoMdPetRelVincRepTpPoderDTO = new MdPetRelVincRepTpPoderDTO();
        $rnMdPetRelVincRepTpPoderRN = new MdPetRelVincRepTpPoderRN();
        $dtoMdPetRelVincRepTpPoderDTO->setNumIdVinculoRepresent($idMdPetVinculoRepresent);
        $dtoMdPetRelVincRepTpPoderDTO->setNumIdTipoPoderLegal(1);
        $dtoMdPetRelVincRepTpPoderDTO->retNumIdVinculoRepresent();
        $arrObjMdPetRelVincRepTpPoderDTO = $rnMdPetRelVincRepTpPoderRN->listar($dtoMdPetRelVincRepTpPoderDTO);

        //Verifica se o usuário em questão possui o poder "Recebimento e Cumprimento de Intimação Eletrônica"
        if (!count($arrObjMdPetRelVincRepTpPoderDTO)) {
            $confirmaCriterios = false;
        }

        if (!is_null($dataLimite)) {
            $dataAtual = date("Y-m-d H:i:s");
//           $dataLimite = explode(' ',$dataLimite);
//           $dataLimite = $dataLimite[0];

            $anoLimite = substr($dataLimite, 6, 4);
            $mesLimite = substr($dataLimite, 3, 2);
            $diaLimite = substr($dataLimite, 0, 2);
            $horaLimite = substr($dataLimite, 11, 2);
            $minLimite = substr($dataLimite, 14, 2);
            $segLimite = substr($dataLimite, 17, 2);
            $dataLimite = $anoLimite . "-" . $mesLimite . "-" . $diaLimite . " " . $horaLimite . ":" . $minLimite . ":" . $segLimite;
        } else {
            $dataLimite = null;
        }

        //Verifica se a situação da procuração é vigente
        if ($staEstado != MdPetVincRepresentantRN::$RP_ATIVO) {
            $confirmaCriterios = false;
        }

        //Estado = A e caso tenha data limite a mesma não esteja vencida
        //Se a data limite estiver vencida o estado é alterado para vencido automaticamente
        if ($staEstado == 'A' && !is_null($dataLimite) && strtotime($dataAtual) > strtotime($dataLimite)) {
            $dtoMdPetVincReptDTO = new MdPetVincRepresentantDTO();
            $dtoMdPetVincReptDTO->setNumIdMdPetVinculoRepresent($idMdPetVinculoRepresent);
            $dtoMdPetVincReptDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_VENCIDA);
            $rnMdPetVincRepRN = new MdPetVincRepresentantRN();
            $arrObjMdPetVincRepresentantDTO = $rnMdPetVincRepRN->alterar($dtoMdPetVincReptDTO);
            $confirmaCriterios = false;
        }

        //Processos especificos escolhidos na procuração com abrangencia
        $dtoMdPetRelVincRepProtocDTO = new MdPetRelVincRepProtocDTO();
        $rnMdPetRelVincRepProtocRN = new MdPetRelVincRepProtocRN();
        $dtoMdPetRelVincRepProtocDTO->setNumIdVincRepresent($idMdPetVinculoRepresent);
        $dtoMdPetRelVincRepProtocDTO->retNumIdProtocolo();
        $arrObjMdPetRelVincRepProtocDTO = $rnMdPetRelVincRepProtocRN->listar($dtoMdPetRelVincRepProtocDTO);
        $arrMdPetRelVincRepProtocDTO = InfraArray::converterArrInfraDTO($arrObjMdPetRelVincRepProtocDTO, 'IdProtocolo');

        if ($documento) {
            $dtoRelProtocoloProtocoloDTO = new RelProtocoloProtocoloDTO();
            $rnRelProtocoloProtocoloRN = new RelProtocoloProtocoloRN();
            $dtoRelProtocoloProtocoloDTO->setDblIdProtocolo2($documento);

            $arrAssociacao = array(
                RelProtocoloProtocoloRN::$TA_DOCUMENTO_ASSOCIADO,
                RelProtocoloProtocoloRN::$TA_PROCEDIMENTO_ANEXADO,
                RelProtocoloProtocoloRN::$TA_DOCUMENTO_CIRCULAR
            );

            $dtoRelProtocoloProtocoloDTO->setStrStaAssociacao($arrAssociacao, InfraDTO::$OPER_IN);
            $dtoRelProtocoloProtocoloDTO->retDblIdProtocolo1();
            $dtoRelProtocoloProtocoloDTO->setOrdDblIdProtocolo1(InfraDTO::$TIPO_ORDENACAO_DESC);
            $dtoRelProtocoloProtocoloDTO->setNumMaxRegistrosRetorno(1);

            $objRelProtocoloProtocoloDTO = $rnRelProtocoloProtocoloRN->consultarRN0841($dtoRelProtocoloProtocoloDTO);
            $idProcesso = $objRelProtocoloProtocoloDTO->getDblIdProtocolo1();
        } else {
            $idProcesso = $procedimento;
        }

        //Verifica se existe algum processo associado a procuração
        //caso tenha é necessário confirmar se é o mesmo que originou a Intimação
        if ($staAbrangencia == 'E' && !in_array($idProcesso, $arrMdPetRelVincRepProtocDTO)) {
            $confirmaCriterios = false;
        }

        return $confirmaCriterios;
    }

    //Envia e-mail
    private function _enviaEmailJuridico($dadosCadastro, $value, $dataHoraGeracao, $procedimentoFormatado, $idIntimacao)
    {

        $dadosIntimacao = array();
        $dadosIntimacao['POST'] = $dadosCadastro;
        $dadosIntimacao['nome'] = $value->getStrNomeProcurador();
        $dadosIntimacao['email'] = $value->getStrEmail();
        $dadosIntimacao['dataHora'] = $dataHoraGeracao;
        $dadosIntimacao['id_intimacao'] = $idIntimacao;
        $dadosIntimacao['processo'] = $procedimentoFormatado;
        $dadosIntimacao['razaoSocial'] = $value->getStrRazaoSocialNomeVinc();
        $dadosIntimacao['cnpj'] = $value->getStrCNPJ();

        //Tipo Vinculação
        if ($value->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL) {
            $dadosIntimacao['tpVinc'] = "Responsavel Legal";
        }
        if ($value->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL) {
            $dadosIntimacao['tpVinc'] = "Procurador Especial";
        }
        if ($value->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES) {
            $dadosIntimacao['tpVinc'] = "Procurador Simples";
        }
//            if($value->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_PROCURADOR_SUBSTALECIDO){
//                $dadosIntimacao['tpVinc']         = "Procurador Substalecido";
//            }
        $emailNotificacaoIntimacaoRN = new MdPetIntEmailNotificacaoRN();
        $emailNotificacaoIntimacaoRN->enviarEmailIntimacaoJuridico($dadosIntimacao);

    }


    private function _lancarAndamentoIntimacao($dadosCadastro, $dataHoraGeracao, $objDocumentoDTO, $strDestinatarios)
    {
        //Cadastro de Andamento
        $objEntradaLancarAndamentoAPI = new EntradaLancarAndamentoAPI();
        $objEntradaLancarAndamentoAPI->setIdProcedimento($dadosCadastro['hdnIdProcedimento']);
        $objEntradaLancarAndamentoAPI->setIdTarefaModulo(self::$ID_TAREFA_MODULO_CADASTRO_INTIMACAO);

        $arrObjAtributoAndamentoAPI = array();
        $arrObjAtributoAndamentoAPI[] = $this->_retornaObjAtributoAndamentoAPI('DATA_EXPEDICAO_INTIMACAO', $dataHoraGeracao);
        $arrObjAtributoAndamentoAPI[] = $this->_retornaObjAtributoAndamentoAPI('DOCUMENTO', $objDocumentoDTO->getStrProtocoloDocumentoFormatado(), $objDocumentoDTO->getDblIdDocumento());
        $arrObjAtributoAndamentoAPI[] = $this->_retornaObjAtributoAndamentoAPI('USUARIO_EXTERNO_NOME', InfraString::formatarArray($strDestinatarios));

        $objEntradaLancarAndamentoAPI->setAtributos($arrObjAtributoAndamentoAPI);

        $objSeiRN = new SeiRN();
        $objSeiRN->lancarAndamento($objEntradaLancarAndamentoAPI);
    }


    /*
     * Essa função verifica se nas Intimações os documentos são anexos e em quais são principais
     *
     * Principal questão tratada na função:
     * Para os anexos ela verifica a qual Intimação de Documento Principal o Anexo pertence
     * */
    private function _getDadosDocumentoIntimacao($idsDocs)
    {
        $arrRetorno = array();

        if (is_array($idsDocs) && count($idsDocs) > 0) {
            $objMdPetIntDestRN = new MdPetIntRelDestinatarioRN();

            $objMdPetIntDestDTO1 = new MdPetIntRelDestinatarioDTO();
            $objMdPetIntDestDTO1->retNumIdMdPetIntimacao();
            $objMdPetIntDestDTO1->setDblIdProtocolo($idsDocs, InfraDTO::$OPER_IN);
            $objMdPetIntDestDTO1->retDblIdDocumento();
            $objMdPetIntDestDTO1->retDblIdProtocolo();
            $objMdPetIntDestDTO1->retStrSinPrincipalDoc();
            $objMdPetIntDestDTO1->retStrSinPessoaJuridica();

            $arrObjDTOAdd = $objMdPetIntDestRN->listar($objMdPetIntDestDTO1);

            //Se o documento for anexo add o id da Intimação, se for principal deixa vazio
            $arrDocIntimacao = array();

            //Array para pesquisar todas as Intimações
            $idsIntimacao = array();
            if (is_countable($arrObjDTOAdd) && count($arrObjDTOAdd) > 0) {
                $idsIntimacao = array_unique(InfraArray::converterArrInfraDTO($arrObjDTOAdd, 'IdMdPetIntimacao'));
            }

            if (is_countable($idsIntimacao) && count($idsIntimacao) > 0) {
                //Consulta todos os dados das Intimações dos Documentos Anexos
                $objMdPetIntDestDTO2 = new MdPetIntRelDestinatarioDTO();
                $objMdPetIntDestDTO2->setNumIdMdPetIntimacao($idsIntimacao, InfraDTO::$OPER_IN);
                $objMdPetIntDestDTO2->setStrSinPrincipalDoc('S');
                $objMdPetIntDestDTO2->retDblIdDocumento();
                $objMdPetIntDestDTO2->retDblIdProtocolo();
                $objMdPetIntDestDTO2->retStrNumero();
                $objMdPetIntDestDTO2->retStrNomeSerie();
                $objMdPetIntDestDTO2->retStrProtocoloFormatadoDocumento();
                $objMdPetIntDestDTO2->retNumIdMdPetIntimacao();
                $objMdPetIntDestDTO2->retStrSinPrincipalDoc();
	            $objMdPetIntDestDTO2->retStrSinPessoaJuridica();

                $arrObjDTOMain = $objMdPetIntDestRN->listar($objMdPetIntDestDTO2);

                //Verifica a qual documento principal pertence cada documento adicionado
                foreach ($arrObjDTOMain as $objDTOMain) {
                    foreach ($arrObjDTOAdd as $objDTOAdd) {
                        if ($objDTOMain->getNumIdMdPetIntimacao() == $objDTOAdd->getNumIdMdPetIntimacao()) {
                            $idMain = $objDTOAdd->getNumIdMdPetIntimacao() . '_' . $objDTOAdd->getDblIdProtocolo();
                            $arrRetorno[$objDTOAdd->getDblIdProtocolo()][$objDTOAdd->getNumIdMdPetIntimacao()]['idDocMain'] = $objDTOMain->getDblIdProtocolo();
                            $arrRetorno[$objDTOAdd->getDblIdProtocolo()][$objDTOAdd->getNumIdMdPetIntimacao()]['nomeDocMain'] = $this->_formataNomeDocumentoParaIntimacao($objDTOMain);
                            $arrRetorno[$objDTOAdd->getDblIdProtocolo()][$objDTOAdd->getNumIdMdPetIntimacao()]['isPrincipal'] = $objDTOAdd->getDblIdProtocolo() == $objDTOMain->getDblIdProtocolo() ? 'S' : 'N';
	                        $arrRetorno[$objDTOAdd->getDblIdProtocolo()][$objDTOAdd->getNumIdMdPetIntimacao()]['sinPessoaJuridica'] = $objDTOMain->getStrSinPessoaJuridica();
                        }
                    }
                }
            }
        }

        return $arrRetorno;
    }


    private function _verificarDocumentosDuplicados($idDocumentoPrinc, $arrIdsAnexo, $hdnIdsUsuarios, $idProcedimento, $tipoPessoa)
    {
        $msg = '';
        $msgFim = "\nNão será possí­vel concluir esta Intimação. Para prosseguir, antes é necessário remover o Destinatário acima sobre o qual constam os conflitos indicados ou retirar os protocolos do conflito da presente Intimação.";
        $arrMsgAnexo = array();
        $idsDocs = $arrIdsAnexo;
        $objMdPetIntDestRN = new MdPetIntRelDestinatarioRN();

        array_push($idsDocs, $idDocumentoPrinc);

        $arrDadosAllDocs = $this->_getDadosDocumentoIntimacao($idsDocs);

        foreach ($hdnIdsUsuarios as $usuario) {
        	
            $idContato      = $usuario;
            $nomeContato    = $this->_getNomeContato($idContato);
            $msgInicio      = "O Destinatário " . PaginaSEI::tratarHTML($nomeContato) . " já possui protocolos indicados nesta Intimação a ser gerada vinculados a Intimação anterior neste processo. Veja a lista abaixo: \n\n";

            $objMdPetIntDestDTO = new MdPetIntRelDestinatarioDTO();
            $objMdPetIntDestDTO->retNumIdMdPetIntimacao();
            $objMdPetIntDestDTO->setNumIdContato($idContato);
            $objMdPetIntDestDTO->setDblIdProtocolo($idsDocs, InfraDTO::$OPER_IN);
            $objMdPetIntDestDTO->setDblIdProtocoloProcedimento($idProcedimento);
            $objMdPetIntDestDTO->retDblIdDocumento();
            $objMdPetIntDestDTO->retDblIdProtocolo();
            $objMdPetIntDestDTO->retStrSinPessoaJuridica();
            $objMdPetIntDestDTO->retDblIdProtocoloProcedimento();

            //Dados Documento
            $objMdPetIntDestDTO->retStrSinPrincipalDoc();
            $objMdPetIntDestDTO->retStrNumero();
            $objMdPetIntDestDTO->retStrNomeSerie();
            $objMdPetIntDestDTO->retStrProtocoloFormatadoDocumento();
            $objMdPetIntDestDTO->setOrdStrSinPrincipalDoc(InfraDTO::$TIPO_ORDENACAO_DESC);

            $countDoc = $objMdPetIntDestRN->contar($objMdPetIntDestDTO);
            
            $arrDuplicados = array();
            
            if ($countDoc > 0) {
            	
                $arrObjDTOUtilizados = $objMdPetIntDestRN->listar($objMdPetIntDestDTO);
                
                foreach ($arrObjDTOUtilizados as $objDTO) {
	
	                $arrDadosDocMain = array_key_exists($objDTO->getDblIdProtocolo(), $arrDadosAllDocs) ? $arrDadosAllDocs[$objDTO->getDblIdProtocolo()] : null;
                    $dadosDoc = $this->_formatarNomeProtocolo($objDTO);

                    foreach ($arrDadosDocMain as $idIntimacao => $arrDadoDoc) {
                    	
                        if (!is_null($arrDadoDoc)) {
	
							// Contorno para que documentos objetos de Intimações é PF não impeça a PJ de ser intimada
	                        if(($arrDadoDoc['sinPessoaJuridica'] == 'N' && $tipoPessoa == 'F') || ($arrDadoDoc['sinPessoaJuridica'] == 'S' && in_array($tipoPessoa, ['F', 'J']))){
	
		                        $idDocPrincipal = $arrDadoDoc['idDocMain'];
	                            $idValidacao = $idDocPrincipal . '_' . $objDTO->getDblIdProtocolo();
	                            $notDuplicado = (!array_key_exists($idValidacao, $arrDuplicados));
	
	                            if (array_key_exists($idDocPrincipal, $arrMsgAnexo) && $notDuplicado) {
	                                
	                                $arrDuplicados[$idValidacao] = '';
	                                $arrMsgAnexo[$idDocPrincipal]['dadosDoc'] .= "    " . $dadosDoc . "\n";
	                                
	                            } else {
	                                
	                                if ($notDuplicado) {
	                                    
	                                    $arrDuplicados[$idValidacao] = '';
	                                    $arrMsgAnexo[$idDocPrincipal]['dadosDoc'] = "    " . $dadosDoc . "\n";
	                                    $arrMsgAnexo[$idDocPrincipal]['dadosIntimacao'] = $arrDadoDoc['nomeDocMain'];
	                                    
	                                }
	                                
	                            }
		
	                        }
                        }
                        
                    }
                }

                if (count($arrMsgAnexo) > 0) {
                	
                    $corpoMsg = "";
                    
                    foreach ($arrMsgAnexo as $arrMsg) {
                        $inicioInt = $corpoMsg != "" ? "\n " : "";
                        $inicioInt .= "* Intimação referente ao Documento Principal ";
                        $inicioInt .= $arrMsg['dadosIntimacao'];
                        $inicioInt .= ": \n \n";
                        $inicioInt .= $arrMsg['dadosDoc'];
                        $corpoMsg .= $inicioInt;
                    }

                    $msg = $msgInicio . $corpoMsg . $msgFim;
                    
                }
            }

            if ($msg != '') {
                return $msg;
            }
        }

        return '';
        
    }

    private function _formatarNomeProtocolo($objDTO)
    {
        $objDocumentoDTO = $this->_retornaDocumentoPorProtocolo($objDTO->getDblIdProtocolo());
        $strReturn = '';

        if (!is_null($objDocumentoDTO)) {
            $strReturn = $this->_formataNomeDocumentoParaIntimacao($objDTO);
        } else {
            $objProcedimentoDTO = $this->_retornaProcedimentoPorId($objDTO->getDblIdProtocolo());
            if (!is_null($objProcedimentoDTO)) {
                $strReturn = PaginaSEI::tratarHTML($objProcedimentoDTO->getStrNomeTipoProcedimento() . ' (' . $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado() . ') - Anexo');
            }
        }

        return $strReturn;
    }


    private function _formataNomeCompletoDocumento($objDTO)
    {
        $nomeDocCompleto = !is_null($objDTO) ? $objDTO->getStrProtocoloFormatadoDocumento() : '';
        $nomeSerie = $objDTO->getStrNomeSerie() != '' ? trim($objDTO->getStrNomeSerie()) : '';
        $nomeSerieNumero = $objDTO->getStrNumero() != '' ? $nomeSerie . ' ' . $objDTO->getStrNumero() : $nomeSerie;
        $nomeDocCompleto .= !is_null($objDTO) ? ' (' . $nomeSerieNumero . ')' : '';

        return $nomeDocCompleto;
    }

    private function _formataNomeDocumentoParaIntimacao($objDTO)
    {
        $protocoloForm = !is_null($objDTO) ? $objDTO->getStrProtocoloFormatadoDocumento() : '';
        $nomeSerie = $objDTO->getStrNomeSerie() != '' ? trim($objDTO->getStrNomeSerie()) : '';
        $nomeSerieNumero = $objDTO->getStrNumero() != '' ? $nomeSerie . ' ' . $objDTO->getStrNumero() : $nomeSerie;
        $nomeReturn = $nomeSerieNumero . ' (SEI nº ' . $protocoloForm . ')';
        $tipoDoc = $objDTO->getStrSinPrincipalDoc() == 'S' ? ' - Documento Principal' : ' - Anexo';
        $nomeReturn .= $tipoDoc;
        return $nomeReturn;
    }


    protected function realizarValidacoesCadastroIntimacaoConectado($arr)
    {

        //Init vars
        $msg = '';
        $impedimento = false;
        $alerta = false;
        $objMdPetAcessoExtRN = new MdPetAcessoExternoRN();

        $hdnDadosUsuario = count($arr) > 0 ? current($arr) : null;
        $hdnIdsUsuarios = (!is_null($hdnDadosUsuario)) ? PaginaSEI::getInstance()->getArrItensTabelaDinamica($hdnDadosUsuario) : null;
        $idProcedimento = array_key_exists('2', $arr) ? $arr[2] : null;


        if ($arr[5] == "J") {
            $arrHdnDadosUsuario = PaginaSEI::getInstance()->getArrItensTabelaDinamica($hdnDadosUsuario);
            $hdnIdsUsuarios = array();
            $arrContatos = array();
            foreach ($arrHdnDadosUsuario as $value) {
                array_push($arrContatos, $value[0]);
            }

            //Recuperando documento
            $documentos = $this->_getDocumentosProcesso($idProcedimento);


            $objDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
            $objDestinatarioDTO->retTodos();
            //Recuperar id do documento anterior
            $objDestinatarioDTO->setDblIdProtocolo($documentos, InfraDTO::$OPER_IN);
            $objDestinatarioDTO->setNumIdContato($arrContatos, InfraDTO::$OPER_IN);

            $objDestinatarioRN = new MdPetIntRelDestinatarioRN();
            $arrDestinatarioDTO = $objDestinatarioRN->listar($objDestinatarioDTO);
            $arrContatos = InfraArray::converterArrInfraDTO($arrDestinatarioDTO, 'IdContato');

            if (count($arrContatos)) {

                $dtoMdPetVincReptDTO = new MdPetVincRepresentantDTO();
                $dtoMdPetVincReptDTO->setNumIdContatoVinc($arrContatos, InfraDTO::$OPER_IN);
                $dtoMdPetVincReptDTO->retNumIdContatoVinc();
                $dtoMdPetVincReptDTO->retStrNomeProcurador();
                $dtoMdPetVincReptDTO->retStrRazaoSocialNomeVinc();
                $dtoMdPetVincReptDTO->retStrTipoRepresentante();
                $dtoMdPetVincReptDTO->retStrCNPJ();
                $dtoMdPetVincReptDTO->retStrEmail();
                $dtoMdPetVincReptDTO->retNumIdContatoProcurador();
                $dtoMdPetVincReptDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
                $rnMdPetVincRepRN = new MdPetVincRepresentantRN();
                $arrMdPetVincRepRN = $rnMdPetVincRepRN->listar($dtoMdPetVincReptDTO);

                if (count($arrMdPetVincRepRN)) {
                    foreach ($arrMdPetVincRepRN as $value) {
                        $hdnIdsUsuarios [] = $value->getNumIdContatoProcurador();

                    }

                } else {

                    $hdnIdsUsuarios [] = 00000;
                }


            } else {
                $hdnIdsUsuarios [] = 00000;

            }

        } else {
            //Fisico

            $arrHdnDadosUsuario = PaginaSEI::getInstance()->getArrItensTabelaDinamica($hdnDadosUsuario);
            $hdnIdsUsuarios = array();
            foreach ($arrHdnDadosUsuario as $value) {
                $hdnIdsUsuarios [] = $value[0];

            }

        }

        $tpAcessoSolicitado = array_key_exists('1', $arr) ? $arr[1] : null;

        $idProcedimento = array_key_exists('2', $arr) ? $arr[2] : null;

        $tpsAcessoAnterior = $objMdPetAcessoExtRN->getUltimaConcAcessoExtModuloPorContatos(array($hdnIdsUsuarios, $idProcedimento));

        $idDocumentoPrinc = $arr[4];
        $arrIdsAnexo = $arr[3] != '' ? explode('_', $arr[3]) : array();

        //Realiza as Validações relacionadas a duplicação de anexo
        if (count($arrIdsAnexo) > 0 && $arr[5] == "J") {
            $ids = array();
            $hdnUsuarios = PaginaSEI::getInstance()->getArrItensTabelaDinamica($hdnDadosUsuario);
            foreach ($hdnUsuarios as $key => $value) {
                $ids [] = $value[0];
            }
            $msg = $this->_verificarDocumentosDuplicados($idDocumentoPrinc, $arrIdsAnexo, $ids, $idProcedimento, $arr[5]);
            

            if ($msg != '') {
                return (array($msg, true, false));
            }
        } else {

            $msg = $this->_verificarDocumentosDuplicados($idDocumentoPrinc, $arrIdsAnexo, $hdnIdsUsuarios, $idProcedimento, $arr[5]);

            if ($msg != '') {
                return (array($msg, true, false));
            }
        }


        //Realizar Validações de Acesso Externo
        if (count($tpsAcessoAnterior) > 0) {

            // Se já possui acesso integral, e a concessão atual é para acesso Parcial - Impeditivo
            $msg = $this->_verificarPossuiAcessoIntegralAtualParcial($tpAcessoSolicitado, $tpsAcessoAnterior, $arr[5], $arrHdnDadosUsuario, $idProcedimento);

            if ($msg != '') {
                $impedimento = true;
            }

            //Se já possui parcial, e a concessão atual é para Acesso Integral - Alerta de Cancelamento do Parcial
            if ($msg == '') {
                $msg = $this->_verificarPossuiAcessoParcialAtualIntegral($tpAcessoSolicitado, $tpsAcessoAnterior, $arr[5], $arrHdnDadosUsuario, $idProcedimento);

                if ($msg != '') {
                    $alerta = true;
                }
            }
        }


        return array($msg, $impedimento, $alerta);
    }

    private function _verificarPossuiAcessoParcialAtualIntegral($tpAcessoSolicitado, $tpsAcessoAnterior, $pessoaTipo, $tabela, $idProcedimento)
    {
        $existeAcessoParcial = in_array(MdPetIntAcessoExternoDocumentoRN::$ACESSO_PARCIAL, $tpsAcessoAnterior);
        $destinatarios = '';
        $tabelaId = array();
        if ($tpAcessoSolicitado == MdPetIntAcessoExternoDocumentoRN::$ACESSO_INTEGRAL && $existeAcessoParcial) {

            if ($pessoaTipo == "J") {

                foreach ($tabela as $coluna) {
                    $tabelaId [] = $coluna[0];
                }

                //Recuperando documento
                $documentos = $this->_getDocumentosProcesso($idProcedimento);


                $objDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
                $objDestinatarioDTO->retTodos();
                //Recuperar id do documento anterior
                $objDestinatarioDTO->setDblIdProtocolo($documentos, InfraDTO::$OPER_IN);
                $objDestinatarioDTO->setNumIdContato($tabelaId, InfraDTO::$OPER_IN);

                $objDestinatarioRN = new MdPetIntRelDestinatarioRN();
                $arrDestinatarioDTO = $objDestinatarioRN->listar($objDestinatarioDTO);
                $arrContatos = InfraArray::converterArrInfraDTO($arrDestinatarioDTO, 'IdContato');


                $destinatarios = array();
                foreach ($tpsAcessoAnterior as $idContato => $tpAcessoCadaContato) {

                    if ($tpAcessoCadaContato == MdPetIntAcessoExternoDocumentoRN::$ACESSO_PARCIAL) {


                        $dtoMdPetVincReptDTO = new MdPetVincRepresentantDTO();
                        $dtoMdPetVincReptDTO->setNumIdContatoProcurador($idContato);
                        $dtoMdPetVincReptDTO->setNumIdContatoVinc($arrContatos, InfraDTO::$OPER_IN);
                        $dtoMdPetVincReptDTO->retNumIdContatoVinc();
                        $dtoMdPetVincReptDTO->retNumIdContatoProcurador();
                        $dtoMdPetVincReptDTO->retStrRazaoSocialNomeVinc();

                        $rnMdPetVincRepRN = new MdPetVincRepresentantRN();
                        $arrObjMdPetVincRepresentantDTO = $rnMdPetVincRepRN->listar($dtoMdPetVincReptDTO);

                        foreach ($arrObjMdPetVincRepresentantDTO as $key => $value) {
                            foreach ($tabela as $coluna) {
                                if ($value->getStrRazaoSocialNomeVinc() == $coluna[1]) {
                                    $destinatarios [] = $this->_getNomeContato($value->getNumIdContatoVinc());
                                }
                            }
                        }


                    }
                }

            } else {

                foreach ($tpsAcessoAnterior as $idContato => $tpAcessoCadaContato) {

                    if ($tpAcessoCadaContato == MdPetIntAcessoExternoDocumentoRN::$ACESSO_PARCIAL) {
                        $destinatarios .= "\n * ";
                        $destinatarios .= $this->_getNomeContato($idContato);
                    }
                }

            }
            $msg = "Os destinatários abaixo listados já possuem Acesso Externo do Tipo Parcial neste processo, concedido por meio do módulo Peticionamento e Intimação Eletrônicos: \n";
            if ($pessoaTipo == "J") {
                $novoArrayUnico = array_unique($destinatarios);
                foreach ($novoArrayUnico as $key => $value) {
                    $msg .= "\n * ";

                    $msg .= PaginaSEI::tratarHTML($value);
                }
            } else {
                $msg .= "\n ";
                $msg .= $destinatarios;
            }

            $msg .= " \n \n";
            $msg .= "Nova Intimação para os Destinatários acima do Acesso Externo do Tipo Integral neste processo cancelará o Acesso Externo Parcial anterior. Deseja continuar?";
            $msg .= " \n ";

        }

        return $msg;
    }


    private function _verificarPossuiAcessoIntegralAtualParcial($tpAcessoSolicitado, $tpsAcessoAnterior, $pessoaTipo, $tabela, $idProcedimento)
    {
        $msg = '';
        $existeAcessoIntegral = in_array(MdPetIntAcessoExternoDocumentoRN::$ACESSO_INTEGRAL, $tpsAcessoAnterior);

        if ($tpAcessoSolicitado == MdPetIntAcessoExternoDocumentoRN::$ACESSO_PARCIAL && $existeAcessoIntegral) {
            $destinatarios = '';
            if ($pessoaTipo == "J") {

                $tabelaId = array();

                foreach ($tabela as $coluna) {
                    $tabelaId [] = $coluna[0];
                }

                //Recuperando documento
                $documentos = $this->_getDocumentosProcesso($idProcedimento);

                $objDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
                $objDestinatarioDTO->retTodos();
                //Recuperar id do documento anterior
                $objDestinatarioDTO->setDblIdProtocolo($documentos, InfraDTO::$OPER_IN);
                $objDestinatarioDTO->setNumIdContato($tabelaId, InfraDTO::$OPER_IN);

                $objDestinatarioRN = new MdPetIntRelDestinatarioRN();
                $arrDestinatarioDTO = $objDestinatarioRN->listar($objDestinatarioDTO);
                $arrContatos = InfraArray::converterArrInfraDTO($arrDestinatarioDTO, 'IdContato');


                $destinatarios = array();
                foreach ($tpsAcessoAnterior as $idContato => $tpAcessoCadaContato) {

                    if ($tpAcessoCadaContato == MdPetIntAcessoExternoDocumentoRN::$ACESSO_INTEGRAL) {


                        $dtoMdPetVincReptDTO = new MdPetVincRepresentantDTO();
                        $dtoMdPetVincReptDTO->setNumIdContatoProcurador($idContato);
                        $dtoMdPetVincReptDTO->setNumIdContatoVinc($arrContatos, InfraDTO::$OPER_IN);
                        $dtoMdPetVincReptDTO->retStrRazaoSocialNomeVinc();
                        $dtoMdPetVincReptDTO->retNumIdContatoVinc();
                        $dtoMdPetVincReptDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
                        $rnMdPetVincRepRN = new MdPetVincRepresentantRN();
                        $arrObjMdPetVincRepresentantDTO = $rnMdPetVincRepRN->listar($dtoMdPetVincReptDTO);
                        $arrContatosEmpresas = InfraArray::converterArrInfraDTO($arrObjMdPetVincRepresentantDTO, 'IdContatoVinc');

                        foreach ($arrObjMdPetVincRepresentantDTO as $key => $value) {
                            foreach ($tabela as $coluna) {
                                if ($value->getNumIdContatoVinc() == $coluna[0]) {
                                    $destinatarios[] = $this->_getNomeContato($value->getNumIdContatoVinc());
                                }
                            }
                        }

                    }
                }

            } else {

                foreach ($tpsAcessoAnterior as $idContato => $tpAcessoCadaContato) {

                    if ($tpAcessoCadaContato == MdPetIntAcessoExternoDocumentoRN::$ACESSO_INTEGRAL) {
                        $destinatarios = "\n * ";
                        $destinatarios .= $this->_getNomeContato($idContato);
                    }
                }

            }

            $msg = "A Intimação não pode ser gerada, pois existem Destinatários que já possuem Acesso Externo do Tipo Integral neste processo, concedido por meio de Intimação Eletrônica anterior. Sendo assim, para os Destinatários listados abaixo, as Intimações subsequentes neste processo também devem selecionar o Acesso Externo do Tipo Integral. Caso necessário, para os demais Destinatários, a Intimação pode ser gerada em separado para a lista abaixo e para os que devem de fato receber Acesso Parcial nesta nova Intimação. \n\n";
            $msg .= "Lista dos Destinatários que já possuem Acesso Externo Integral em razão de Intimação Eletrônica anterior neste processo:";
            $msg .= " \n ";
            if ($pessoaTipo == "J") {
                $novoArrayUnico = array_unique($destinatarios);
                foreach ($novoArrayUnico as $key => $value) {
                    $msg .= "\n * ";
                    $msg .= PaginaSEI::tratarHTML($value);
                }
            } else {
                $msg .= "\n ";
                $msg .= $destinatarios;
            }
			$msg .= " \n ";
        }

        return $msg;
    }

    private function _getNomeContato($idContato)
    {
        $objContatoRN = new ContatoRN();
        $objContatoDTO = new ContatoDTO();
        $objContatoDTO->setNumIdContato($idContato);
        $objContatoDTO->retStrNome();
        $objContatoDTO = $objContatoRN->consultarRN0324($objContatoDTO);
        $nome = !is_null($objContatoDTO) ? $objContatoDTO->getStrNome() : '';
        return $nome;
    }

    public function verificaIntimacaoExistente(){

        $ids = [];
        $arrIdsDocAnexo = !empty($_POST['hdnIdsDocAnexo']) ? json_decode($_POST['hdnIdsDocAnexo']) : [];

        $hdnUsuarios = PaginaSEI::getInstance()->getArrItensTabelaDinamica($_POST['hdnDadosUsuario']);
        foreach ($hdnUsuarios as $key => $value) {
            $ids [] = $value[0];
        }

        return $msg = '<message>'.(new MdPetIntimacaoRN())->_verificarDocumentosDuplicados($_POST['hdnIdDocumento'], $arrIdsDocAnexo, $ids, $_POST['hdnIdProcedimento'], $_POST['tipoPessoa']).'</message>';

    }

    public function cadastrarIntimacaoControlado($dadosCadastro)
    {

        try {
            $dataHoraGeracao = InfraData::getStrDataHoraAtual();
            $objDocumentoRN = new DocumentoRN();

            $objProcedimentoDTO = new ProcedimentoDTO();
            $objProcedimentoDTO->retStrProtocoloProcedimentoFormatado();
            $objProcedimentoDTO->retNumIdTipoProcedimento();
            $objProcedimentoDTO->retNumIdOrgaoUnidadeGeradoraProtocolo();
	        $objProcedimentoDTO->setDblIdProcedimento($dadosCadastro['hdnIdProcedimento']);
            $objProcedimentoDTO = (new ProcedimentoRN())->consultarRN0201($objProcedimentoDTO);

            $objMdPetIntimacaoDTO = $this->_cadastrarObjIntimacao($dadosCadastro);
            $idIntimacao = $objMdPetIntimacaoDTO->getNumIdMdPetIntimacao();

            $this->_vincularTipoResposta($dadosCadastro['selTipoResposta'], $idIntimacao);
            $this->_vincularDocumentosIntimacao($dadosCadastro['hndIdDocumento'], $idIntimacao, true);
            $this->_vincularAnexosIntimacao($dadosCadastro, $idIntimacao);
            $this->_vincularDocumentosDisponiveis($dadosCadastro, $idIntimacao);

            if ($dadosCadastro['tipoPessoa'] == "J") {
                $strDestinatarios = $this->_vincularDestinatariosIntimacaoJuridico($dadosCadastro, $idIntimacao, $dataHoraGeracao, $objProcedimentoDTO, $dadosCadastro['hndIdDocumento']);
            } else {
                $strDestinatarios = $this->_vincularDestinatariosIntimacao($dadosCadastro, $idIntimacao, $dataHoraGeracao, $objProcedimentoDTO, $dadosCadastro['hndIdDocumento']);
            }

            $objDocumentoDTO = $this->_retornaObjDocumento($dadosCadastro['hndIdDocumento']);

            $objDocumentoRN->bloquearConteudo($objDocumentoDTO);

            $this->_lancarAndamentoIntimacao($dadosCadastro, $dataHoraGeracao, $objDocumentoDTO, $strDestinatarios);

            return $objMdPetIntimacaoDTO;

        } catch (Exception $e) {
            PaginaSEI::getInstance()->processarExcecao($e);
            return false;
        }

    }

    public function getSituacaoIntimacao($id_intimacao, $id_contato)
    {
        $situacao = " ";

        if (is_null($id_intimacao) || empty($id_intimacao)) {
            return MdPetIntimacaoRN::$STR_INTIMACAO_PENDENTE_ACEITE;
        }

        $objMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
        $objMdPetIntRelDestinatarioDTO->retTodos();
        $objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntimacao($id_intimacao);
        $objMdPetIntRelDestinatarioDTO->setNumIdContato($id_contato);

        $objMdPetIntRelDestinatarioRN = new MdPetIntRelDestinatarioRN();
        $objMdPetIntRelDestinatarioDTO = $objMdPetIntRelDestinatarioRN->consultar($objMdPetIntRelDestinatarioDTO);

        if (!empty($objMdPetIntRelDestinatarioDTO)) {

            $situacao = MdPetIntimacaoRN::$STR_INTIMACAO_PENDENTE_ACEITE;

            $objMdPetIntAceiteDTO = new MdPetIntAceiteDTO();
            $objMdPetIntAceiteDTO->retTodos();
            $objMdPetIntAceiteDTO->setNumIdMdPetIntRelDestinatario($objMdPetIntRelDestinatarioDTO->getNumIdMdPetIntRelDestinatario());
	        $objMdPetIntAceiteDTO->setNumIdMdPetIntimacao($id_intimacao);
	        $objMdPetIntAceiteDTO->setOrdDthData(InfraDTO::$TIPO_ORDENACAO_ASC);
	        $objMdPetIntAceiteDTO->setNumMaxRegistrosRetorno(1);
            $objMdPetIntAceiteDTO = (new MdPetIntAceiteRN())->consultar($objMdPetIntAceiteDTO);

            if (!empty($objMdPetIntAceiteDTO)) {

                $objMdPetIntDestRespostaDTO = new MdPetIntDestRespostaDTO();
                $objMdPetIntDestRespostaDTO->retTodos();
                $objMdPetIntDestRespostaDTO->setNumIdMdPetIntRelDestinatario($objMdPetIntRelDestinatarioDTO->getNumIdMdPetIntRelDestinatario());

                //ajustando esta consulta para limitar retorno a 1 registro, e para trazer a resposta mais atual deste destinatário
                //se nao aplicar isso, a tela de consultar detalhes da intimação (Processo -> Ver intimações -> lupinha)
                //dá erro por causa de chamada a metodo "consultar" retornando mais de uma linha

                $objMdPetIntDestRespostaDTO->setNumMaxRegistrosRetorno(1);
                $objMdPetIntDestRespostaDTO->setOrd('IdMdPetIntDestResposta', InfraDTO::$TIPO_ORDENACAO_DESC);

                $objMdPetIntDestRespostaRN = new MdPetIntDestRespostaRN();
                $objMdPetIntDestRespostaDTO = $objMdPetIntDestRespostaRN->consultar($objMdPetIntDestRespostaDTO);

                if (!empty($objMdPetIntDestRespostaDTO)) {
                    $situacao = MdPetIntimacaoRN::$STR_INTIMACAO_RESPONDIDA_ACEITE;
                } else if ($objMdPetIntAceiteDTO->getStrTipoAceite() == MdPetIntimacaoRN::$TP_AUTOMATICO_POR_DECURSO_DE_PRAZO) {
                    $situacao = MdPetIntimacaoRN::$STR_INTIMACAO_CUMPRIDA_PRAZO;
                } else if ($objMdPetIntAceiteDTO->getStrTipoAceite() == MdPetIntimacaoRN::$TP_MANUAL_USUARIO_EXTERNO_ACEITE) {
                    $situacao = MdPetIntimacaoRN::$STR_INTIMACAO_CUMPRIDA_POR_ACESSO;
                }
            }
        }
        return $situacao;
    }

    public function getIntimacoesPossuemDataConectado($PessoaJuridica = false)
    {
        try {

            $arrObjNaoRespondidos   = [];
            $dataReiteracao1        = date('d-m-Y', strtotime('+5 days'));
            $dataReiteracao2        = date('d-m-Y', strtotime('+1 day'));

            $objMdPetIntRelTipoRespDestDTO = new MdPetIntRelTipoRespDestDTO();
            $objMdPetIntRelTipoRespDestDTO->retNumIdMdPetIntRelTipoResp();

            $objMdPetIntRelTipoRespDestDTO->adicionarCriterio(array('DataLimite', 'DataLimite', 'DataProrrogada', 'DataProrrogada'),
            array(InfraDTO::$OPER_MAIOR_IGUAL, InfraDTO::$OPER_MENOR_IGUAL, InfraDTO::$OPER_MAIOR_IGUAL, InfraDTO::$OPER_MENOR_IGUAL),
            array($dataReiteracao1.' 00:00:00', $dataReiteracao1.' 23:59:59', $dataReiteracao1.' 00:00:00', $dataReiteracao1.' 23:59:59'),
            array(InfraDTO::$OPER_LOGICO_AND, InfraDTO::$OPER_LOGICO_OR, InfraDTO::$OPER_LOGICO_AND), 'dataLimite2');

            $objMdPetIntRelTipoRespDestDTO->adicionarCriterio(array('DataLimite', 'DataLimite', 'DataProrrogada', 'DataProrrogada'),
            array(InfraDTO::$OPER_MAIOR_IGUAL, InfraDTO::$OPER_MENOR_IGUAL, InfraDTO::$OPER_MAIOR_IGUAL, InfraDTO::$OPER_MENOR_IGUAL),
            array($dataReiteracao2.' 00:00:00', $dataReiteracao2.' 23:59:59', $dataReiteracao2.' 00:00:00', $dataReiteracao2.' 23:59:59'),
            array(InfraDTO::$OPER_LOGICO_AND, InfraDTO::$OPER_LOGICO_OR, InfraDTO::$OPER_LOGICO_AND), 'dataLimite1');

            $objMdPetIntRelTipoRespDestDTO->agruparCriterios(array('dataLimite1', 'dataLimite2'), array(InfraDTO::$OPER_LOGICO_OR));

            $objMdPetIntRelTipoRespDestDTO = (new MdPetIntRelTipoRespDestRN())->listar($objMdPetIntRelTipoRespDestDTO);
            $arrIdMdPetIntRelTipoResp = InfraArray::converterArrInfraDTO($objMdPetIntRelTipoRespDestDTO,'IdMdPetIntRelTipoResp');

            if(!empty($arrIdMdPetIntRelTipoResp)){

                $objMdPetIntRelTipoRespDTO = new MdPetIntRelTipoRespDTO();
                $objMdPetIntRelTipoRespDTO->retNumIdMdPetIntimacao();
                $objMdPetIntRelTipoRespDTO->setStrTipoPrazoExterno('N', InfraDTO::$OPER_DIFERENTE);
                $objMdPetIntRelTipoRespDTO->setNumIdMdPetIntRelTipoResp($arrIdMdPetIntRelTipoResp, InfraDTO::$OPER_IN);
                $arrObjMdPetIntRelTipoRespDTO = (new MdPetIntRelTipoRespRN())->listar($objMdPetIntRelTipoRespDTO);

                foreach($arrObjMdPetIntRelTipoRespDTO as $intimacao){

                    // Busca os Destinatarios que ja responderam esta intimacao:
                    $objMdPetIntDestRespostaDTO = new MdPetIntDestRespostaDTO();
                    $objMdPetIntDestRespostaDTO->retNumIdMdPetIntRelDestinatario();
                    $objMdPetIntDestRespostaDTO->setNumIdMdPetIntimacao($intimacao->getNumIdMdPetIntimacao());
                    $objMdPetIntDestRespostaDTO = (new MdPetIntDestRespostaRN())->listar($objMdPetIntDestRespostaDTO);
                    $arrMdPetIntDestResponderam = InfraArray::converterArrInfraDTO($objMdPetIntDestRespostaDTO,'IdMdPetIntRelDestinatario');

                    // Busca os Desdinatarios que nao responderam para reiterar:
                    $objMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
                    $objMdPetIntRelDestinatarioDTO->retNumIdMdPetIntimacao();
                    $objMdPetIntRelDestinatarioDTO->retNumIdMdPetIntRelDestinatario();
                    $objMdPetIntRelDestinatarioDTO->retNumIdContato();
                    $objMdPetIntRelDestinatarioDTO->retStrNomeTipoIntimacao();
                    $objMdPetIntRelDestinatarioDTO->retDthDataCadastro();
                    $objMdPetIntRelDestinatarioDTO->retDthDataAceite();
                    $objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntimacao($intimacao->getNumIdMdPetIntimacao());
                    $objMdPetIntRelDestinatarioDTO->setAceiteTIPOFK(InfraDTO::$TIPO_FK_OBRIGATORIA);
                    $objMdPetIntRelDestinatarioDTO->setStrNomeTipoRespostaAceita(MdPetIntTipoIntimacaoRN::$EXIGE_RESPOSTA);
                    $objMdPetIntRelDestinatarioDTO->setDistinct(true);
                    if($PessoaJuridica){
                        $objMdPetIntRelDestinatarioDTO->setStrSinPessoaJuridica('S');
                    }
                    if(!empty($arrMdPetIntDestResponderam) && count($arrMdPetIntDestResponderam) > 0){
                        $objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntRelDestinatario($arrMdPetIntDestResponderam, InfraDTO::$OPER_NOT_IN);
                    }
                    $arrObjMdPetIntRelDestinatarios = (new MdPetIntRelDestinatarioRN())->listar($objMdPetIntRelDestinatarioDTO);

                    foreach($arrObjMdPetIntRelDestinatarios as $objMdPetIntRelDestinatario){
                        array_push($arrObjNaoRespondidos, $objMdPetIntRelDestinatario);
                    }
                }

            }

            return $arrObjNaoRespondidos;

        } catch (Exception $e) {
            LogSEI::getInstance()->gravar('ReiterarIntimacaoExigeResposta: ' . $e, InfraLog::$INFORMACAO);
            throw new InfraException('Erro reiterando intimacoes pendentes exige resposta.', $e);
        }

    }

    public function BKPgetIntimacoesPossuemDataConectado($PessoaJuridica = false)
    {

        try {

            $VerificarRespondidas = true;
            $ExigeResposta = true;

            $arrObjMdPetIntRelDestinatarioDTO = null;
            $objMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();

            if ($VerificarRespondidas) {
                //RESPOSTA
                $objMdPetIntDestRespostaDTO = new MdPetIntDestRespostaDTO();
                $objMdPetIntDestRespostaDTO->retNumIdMdPetIntRelDestinatario();
                $objMdPetIntDestRespostaDTO->setNumMaxRegistrosRetorno(10);

                $objMdPetIntDestRespostaRN = new MdPetIntDestRespostaRN();
                $arrObjMdPetIntDestRespostaDTO = $objMdPetIntDestRespostaRN->listar($objMdPetIntDestRespostaDTO);

                if (is_countable($arrObjMdPetIntDestRespostaDTO) && count($arrObjMdPetIntDestRespostaDTO) > 0) {
                    $arrIdMdPetIntRelDestinatario = InfraArray::converterArrInfraDTO($arrObjMdPetIntDestRespostaDTO,'IdMdPetIntRelDestinatario');
                    //SEM RESPOSTA
                    $objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntRelDestinatario($arrIdMdPetIntRelDestinatario, InfraDTO::$OPER_NOT_IN);

                    unset($arrObjMdPetIntDestRespostaDTO);
                    unset($arrIdMdPetIntRelDestinatario);
                }
            }

            $objMdPetIntRelDestinatarioDTO->setAceiteTIPOFK(InfraDTO::$TIPO_FK_OBRIGATORIA);

            // Tipos de Resposta com algum tipo de prazo
            $objMdPetIntRelTipoRespDTO = new MdPetIntRelTipoRespDTO();
            $objMdPetIntRelTipoRespDTO->setStrTipoPrazoExterno('N', InfraDTO::$OPER_DIFERENTE);
            $objMdPetIntRelTipoRespDTO->retNumIdMdPetIntimacao();
//            $objMdPetIntRelTipoRespDTO->setNumMaxRegistrosRetorno(10);
            $arrObjMdPetIntRelTipoRespDTO = (new MdPetIntRelTipoRespRN())->contar($objMdPetIntRelTipoRespDTO);

            if (is_countable($arrObjMdPetIntRelTipoRespDTO) && count($arrObjMdPetIntRelTipoRespDTO) > 0) {
                $arrIdMdPetIntimacao = array_unique(InfraArray::converterArrInfraDTO($arrObjMdPetIntRelTipoRespDTO, 'IdMdPetIntimacao'));
                $objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntimacao($arrIdMdPetIntimacao, InfraDTO::$OPER_IN);

                unset($arrObjMdPetIntRelTipoRespDTO);
                unset($arrIdMdPetIntimacao);
            }

            if ($ExigeResposta) {
                $objMdPetIntRelDestinatarioDTO->setStrNomeTipoRespostaAceita(MdPetIntTipoIntimacaoRN::$EXIGE_RESPOSTA);
            }

            if($PessoaJuridica){
                $objMdPetIntRelDestinatarioDTO->setStrSinPessoaJuridica('S');
            }

            $objMdPetIntRelDestinatarioDTO->retNumIdMdPetIntimacao();
            $objMdPetIntRelDestinatarioDTO->retNumIdMdPetIntRelDestinatario();
            $objMdPetIntRelDestinatarioDTO->retNumIdContato();
            $objMdPetIntRelDestinatarioDTO->retStrNomeTipoIntimacao();
            $objMdPetIntRelDestinatarioDTO->retDthDataCadastro();
            $objMdPetIntRelDestinatarioDTO->retDthDataAceite();

            $objMdPetIntRelDestinatarioDTO->setDistinct(true);

            $objMdPetIntRelDestinatarioRN = new MdPetIntRelDestinatarioRN();
            $arrObjMdPetIntRelDestinatarioDTO = $objMdPetIntRelDestinatarioRN->listar($objMdPetIntRelDestinatarioDTO);

            return $arrObjMdPetIntRelDestinatarioDTO;

        } catch (Exception $e) {
            LogSEI::getInstance()->gravar('ReiterarIntimacaoExigeResposta: ' . $e, InfraLog::$INFORMACAO);
            throw new InfraException('Erro reiterando intimações pendentes que exigem resposta.', $e);
        }

    }

    public function buscaIntimacoesCadastradas($idDocumento)
    {
        $arrContatos = array();
        $objMdPetIntDocumentoDTO = new MdPetIntProtocoloDTO();
        $objMdPetIntDocumentoDTO->setDblIdProtocolo($idDocumento);
        $objMdPetIntDocumentoDTO->setStrSinPrincipal('S');
        $objMdPetIntDocumentoDTO->retTodos();

        $objMdPetIntDocumentoRN = new MdPetIntProtocoloRN();
        $arrMdPetIntDocumentoDTO = $objMdPetIntDocumentoRN->listar($objMdPetIntDocumentoDTO);

        if (!empty($arrMdPetIntDocumentoDTO)) {
            foreach ($arrMdPetIntDocumentoDTO as $MdPetIntDocumento) {
                $objMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
                $objMdPetIntRelDestinatarioDTO->retTodos();
                $objMdPetIntRelDestinatarioDTO->retNumIdContatoParticipante();
                $objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntimacao($MdPetIntDocumento->getNumIdMdPetIntimacao());

                $objMdPetIntRelDestinatarioRN = new MdPetIntRelDestinatarioRN();
                $objMdPetIntRelDestinatarioDTO = $objMdPetIntRelDestinatarioRN->listar($objMdPetIntRelDestinatarioDTO);

                if (!empty($objMdPetIntRelDestinatarioDTO)) {
                    foreach ($objMdPetIntRelDestinatarioDTO as $MdPetIntRelDestinatario) {
                        $arrContatos[] = MdPetContatoINT::getDadosContatos($MdPetIntRelDestinatario->getNumIdContatoParticipante(), $idDocumento, false);
                    }
                }
            }
        }
        return $arrContatos;
    }

    //Juri

    public function buscaIntimacoesCadastradasJuridico($idDocumento)
    {
        $arrContatos = array();
        $objMdPetIntDocumentoDTO = new MdPetIntProtocoloDTO();
        $objMdPetIntDocumentoDTO->setDblIdProtocolo($idDocumento);
        $objMdPetIntDocumentoDTO->setStrSinPrincipal('S');
        $objMdPetIntDocumentoDTO->retTodos();

        $objMdPetIntDocumentoRN = new MdPetIntProtocoloRN();
        $arrMdPetIntDocumentoDTO = $objMdPetIntDocumentoRN->listar($objMdPetIntDocumentoDTO);

        if (!empty($arrMdPetIntDocumentoDTO)) {
            foreach ($arrMdPetIntDocumentoDTO as $MdPetIntDocumento) {
                $objMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
                $objMdPetIntRelDestinatarioDTO->setStrSinPessoaJuridica('S');
                $objMdPetIntRelDestinatarioDTO->retTodos();
                $objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntimacao($MdPetIntDocumento->getNumIdMdPetIntimacao());

                $objMdPetIntRelDestinatarioRN = new MdPetIntRelDestinatarioRN();
                $objMdPetIntRelDestinatarioDTO = $objMdPetIntRelDestinatarioRN->listar($objMdPetIntRelDestinatarioDTO);

                if (!empty($objMdPetIntRelDestinatarioDTO)) {
                    foreach ($objMdPetIntRelDestinatarioDTO as $mdPetIntRelDestinatario) {

                        $arrContatos[] = MdPetContatoINT::getDadosContatosJuridicoRecuperar($mdPetIntRelDestinatario->getNumIdContato(), $idDocumento, false);
                    }
                }
            }
        }
        return $arrContatos;
    }

    //Pessoa Fisica
    public function buscaIntimacoesCadastradasFisica($idDocumento)
    {
        $arrContatos = array();
        $objMdPetIntDocumentoDTO = new MdPetIntProtocoloDTO();
        $objMdPetIntDocumentoDTO->setDblIdProtocolo($idDocumento);
        $objMdPetIntDocumentoDTO->setStrSinPrincipal('S');
        $objMdPetIntDocumentoDTO->retTodos();

        $objMdPetIntDocumentoRN = new MdPetIntProtocoloRN();
        $arrMdPetIntDocumentoDTO = $objMdPetIntDocumentoRN->listar($objMdPetIntDocumentoDTO);

        // echo var_dump($arrMdPetIntDocumentoDTO);

        if (is_iterable($arrMdPetIntDocumentoDTO)) {

            foreach ($arrMdPetIntDocumentoDTO as $MdPetIntDocumento) {

                $objMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
                $objMdPetIntRelDestinatarioDTO->setStrSinPessoaJuridica('N');
                $objMdPetIntRelDestinatarioDTO->retTodos();
                $objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntimacao($MdPetIntDocumento->getNumIdMdPetIntimacao());

                $objMdPetIntRelDestinatarioRN = new MdPetIntRelDestinatarioRN();
                $objMdPetIntRelDestinatarioDTO = $objMdPetIntRelDestinatarioRN->listar($objMdPetIntRelDestinatarioDTO);

                if (is_iterable($objMdPetIntRelDestinatarioDTO)) {
                    foreach ($objMdPetIntRelDestinatarioDTO as $MdPetIntRelDestinatario) {
                        $arrContatos[] = MdPetContatoINT::getDadosContatos($MdPetIntRelDestinatario->getNumIdContato(), $idDocumento, false);
                    }
                }
            }
        }
        return $arrContatos;
    }
	
    public function preencherDadaPrazoTacito(){

        $objMdPetIntDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
		$objMdPetIntDestinatarioDTO->retDthDataCadastro();
		$objMdPetIntDestinatarioDTO->retNumIdMdPetIntimacao();
		$objMdPetIntDestinatarioDTO->retNumIdMdPetIntRelDestinatario();
		$objMdPetIntDestinatarioDTO->retNumIdContato();
		$objMdPetIntDestinatarioDTO->retStrSinPessoaJuridica();
		$objMdPetIntDestinatarioDTO->retStrNomeContato();
		$objMdPetIntDestinatarioDTO->retNumIdOrgao();
		$objMdPetIntDestinatarioDTO->retNumIdTipoProcedimento();
		$objMdPetIntDestinatarioDTO->retDtaDataPrazoTacito();
		$objMdPetIntDestinatarioDTO->setDtaDataPrazoTacito(NULL, InfraDTO::$OPER_IGUAL); // Pega apenas as que tem Data Prazo Tacito NULL
		$objMdPetIntDestinatarioDTO->setStrStaSituacaoIntimacao(self::$INTIMACAO_PENDENTE);
		$objMdPetIntDestinatarioDTO->setDthDataAceite(NULL, InfraDTO::$OPER_IGUAL);
		$objMdPetIntDestinatarioDTO->setDistinct(true);
		$arrObjMdPetIntDestinatarioDTO = (new MdPetIntRelDestinatarioRN())->listar($objMdPetIntDestinatarioDTO);

        if(!empty($arrObjMdPetIntDestinatarioDTO)){

			foreach($arrObjMdPetIntDestinatarioDTO as $intimacao){
				
				$dataIntimacao = explode(" ", $intimacao->getDthDataCadastro())[0];

				$objMdPetIntPrazoTacitaDTO = ( new MdPetIntPrazoTacitaRN() )->getTipoPrazoTacitoGeralEspecifico( $intimacao->getNumIdTipoProcedimento() );
				$diasExp = !empty($objMdPetIntPrazoTacitaDTO) ? $objMdPetIntPrazoTacitaDTO->getNumNumPrazo() : 0;
				
				$dtaFinalCumprimento = (new MdPetIntPrazoRN())->calcularDataPrazo( $diasExp, $dataIntimacao, $intimacao->getNumIdOrgao() );
				
				$objMdPetIntDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
				$objMdPetIntDestinatarioDTO->setNumIdMdPetIntRelDestinatario($intimacao->getNumIdMdPetIntRelDestinatario());
				$objMdPetIntDestinatarioDTO->setDtaDataPrazoTacito($dtaFinalCumprimento);
				(new MdPetIntRelDestinatarioRN())->alterar($objMdPetIntDestinatarioDTO);

				$intimacao->setDtaDataPrazoTacito($dtaFinalCumprimento);
				
			}

		}

    }

    public function recalculaCumprimentoIntimacaoPorFeriado(){  

        $objFeriadoDTO = new FeriadoDTO();
		$objFeriadoDTO->retDtaFeriado();
		$objFeriadoDTO->retStrDescricao();
		$objFeriadoDTO->setDtaFeriado(InfraData::getStrDataAtual());
		$objFeriadoDTO->setNumMaxRegistrosRetorno(1);

		$objFeriadoDTO->adicionarCriterio(['IdOrgao', 'IdOrgao'],
										  [InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL],
										  [SessaoSEI::getInstance(false)->getNumIdOrgaoSistema(), NULL], // NULL = Todos os Orgaos
										  InfraDTO::$OPER_LOGICO_OR);

		$objFeriado = (new FeriadoRN())->consultar($objFeriadoDTO);

		if(!empty($objFeriado)){
			(new MdPetIntimacaoRN())->_recalculaDataPrazoTacitoIntimacoes($objFeriadoDTO->getDtaFeriado());
		}

    }

	protected function retornarDadosIntimacaoPrazoExpiradoConectado()
	{
		
		$intimacoesPendentes = [];
		$checkAceiteDuplicado = true;
		
		$objMdPetIntDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
		$objMdPetIntDestinatarioDTO->retDthDataCadastro();
		$objMdPetIntDestinatarioDTO->retNumIdMdPetIntimacao();
		$objMdPetIntDestinatarioDTO->retNumIdMdPetIntRelDestinatario();
		$objMdPetIntDestinatarioDTO->retNumIdContato();
		$objMdPetIntDestinatarioDTO->retStrSinPessoaJuridica();
		$objMdPetIntDestinatarioDTO->retStrNomeContato();
		$objMdPetIntDestinatarioDTO->retNumIdOrgao();
		$objMdPetIntDestinatarioDTO->retNumIdTipoProcedimento();
		$objMdPetIntDestinatarioDTO->retDtaDataPrazoTacito();

		$objMdPetIntDestinatarioDTO->setDtaDataPrazoTacito(InfraData::getStrDataAtual(), InfraDTO::$OPER_MENOR_IGUAL);

        // $objMdPetIntDestinatarioDTO->adicionarCriterio(array('DataPrazoTacito', 'DataPrazoTacito'),
        //                                 array(InfraDTO::$OPER_MENOR_IGUAL, InfraDTO::$OPER_IGUAL),
        //                                 array(InfraData::getStrDataAtual(), NULL),
        //                                 InfraDTO::$OPER_LOGICO_OR);

		$objMdPetIntDestinatarioDTO->setStrStaSituacaoIntimacao(self::$INTIMACAO_PENDENTE);
		$objMdPetIntDestinatarioDTO->setDthDataAceite(NULL, InfraDTO::$OPER_IGUAL);
		
		// Bypass em Intimacoes com problemas para permitir o processamento da fila
		$objMdPetIntDestinatarioDTO->setNumIdMdPetIntimacao([282287], InfraDTO::$OPER_NOT_IN);
		
		$objMdPetIntDestinatarioDTO->setDistinct(true);
		$arrObjMdPetIntDestinatarioDTO = (new MdPetIntRelDestinatarioRN())->listar($objMdPetIntDestinatarioDTO);
		
		// Evita cumprimento duplicado se já houver aceite
		if(!empty($arrObjMdPetIntDestinatarioDTO) && $checkAceiteDuplicado){
			foreach($arrObjMdPetIntDestinatarioDTO as $intimacao){
				if(is_null((new MdPetIntAceiteRN())->retornaDataCumprimentoIntimacao($intimacao->getNumIdMdPetIntRelDestinatario()))){
					array_push($intimacoesPendentes, $intimacao);
				}
			}
		}else{
			$intimacoesPendentes = $arrObjMdPetIntDestinatarioDTO;
		}
		
		return $intimacoesPendentes;
		
	}
	
	
	protected function retornarDadosIntimacaoPrazoExpiradoControlado_bkp()
    {
	
	    $intimacoesPendentes = [];
	    $checkAceiteDuplicado = true;
    	
        $objMdPetIntDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
        $objMdPetIntDestinatarioDTO->retDthDataCadastro();
        $objMdPetIntDestinatarioDTO->retNumIdMdPetIntimacao();
        $objMdPetIntDestinatarioDTO->retNumIdMdPetIntRelDestinatario();
        $objMdPetIntDestinatarioDTO->retNumIdContato();
        $objMdPetIntDestinatarioDTO->retNumIdUnidade();
        $objMdPetIntDestinatarioDTO->retStrSinPessoaJuridica();
        $objMdPetIntDestinatarioDTO->retStrNomeContato();
        $objMdPetIntDestinatarioDTO->retNumIdOrgao();
	    $objMdPetIntDestinatarioDTO->retNumIdTipoProcedimento();
	    $objMdPetIntDestinatarioDTO->retDtaDataPrazoTacito();
	    $objMdPetIntDestinatarioDTO->setDtaDataPrazoTacito(InfraData::getStrDataAtual(), InfraDTO::$OPER_MENOR_IGUAL);
	    $objMdPetIntDestinatarioDTO->setStrStaSituacaoIntimacao(self::$INTIMACAO_PENDENTE);
	    $objMdPetIntDestinatarioDTO->setDthDataAceite(NULL, InfraDTO::$OPER_IGUAL);
	    $objMdPetIntDestinatarioDTO->setNumMaxRegistrosRetorno(50);
        $objMdPetIntDestinatarioDTO->setDistinct(true);
        $arrObjMdPetIntDestinatarioDTO = (new MdPetIntRelDestinatarioRN())->listar($objMdPetIntDestinatarioDTO);
        
        // Evita cumprimento duplicado se houver aceite
        if(!empty($arrObjMdPetIntDestinatarioDTO) && $checkAceiteDuplicado){
        	foreach($arrObjMdPetIntDestinatarioDTO as $intimacao){
		        if(is_null((new MdPetIntAceiteRN())->retornaDataCumprimentoIntimacao($intimacao->getNumIdMdPetIntRelDestinatario()))){
		        	array_push($intimacoesPendentes, $intimacao);
		        }
	        }
        }else{
	        $intimacoesPendentes = $arrObjMdPetIntDestinatarioDTO;
        }
	
	    return $intimacoesPendentes;
     
    }

    private function _retornaObjAtributoAndamentoAPI($nome, $valor, $idOrigem = null)
    {
        $objAtributoAndamentoAPI = new AtributoAndamentoAPI();
        $objAtributoAndamentoAPI->setNome($nome);
        $objAtributoAndamentoAPI->setValor($valor);
        $objAtributoAndamentoAPI->setIdOrigem($idOrigem); //ID do prédio, pode ser null

        return $objAtributoAndamentoAPI;
    }

    public function getObjDocumentoPorIdDoc($idDocumento)
    {
        $objDocumentoDTO = new DocumentoDTO();
        $objDocumentoRN = new DocumentoRN();
        $objDocumentoDTO->setDblIdDocumento($idDocumento);
        $objDocumentoDTO->retStrProtocoloDocumentoFormatado();
        $objDocumentoDTO->retStrNomeSerie();
        $objDocumentoDTO->retStrNumero();

        $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

        return $objDocumentoDTO;
    }

    protected function verificaVinculosDocumentoConectado($idDocumento)
    {
        //Init Vars
        $isIntimacao = false;
        $objDTO = new MdPetIntProtDisponivelDTO();
        $objRN = new MdPetIntProtDisponivelRN();

        $objDTO->setDblIdDocumento($idDocumento);
        $objDTO->retDblIdDocumento();
        $isIntimacao = $objRN->contar($objDTO) > 0 ? true : false;

        if (!$isIntimacao) {
            $objDTO = new MdPetIntProtocoloDTO();
            $objRN = new MdPetIntProtocoloRN();

            $objDTO->setDblIdDocumento($idDocumento);
            $objDTO->retDblIdDocumento();

            $isIntimacao = $objRN->contar($objDTO) > 0 ? true : false;
        }

        return $isIntimacao;
    }

    protected function verificaProcessoPossuiIntimacaoConectado($idProcedimento)
    {
        $arrIdsDoc = $this->_getDocumentosProcesso($idProcedimento);
        $isIntimacao = false;

        if (count($arrIdsDoc) > 0) {
            $objDTO = new MdPetIntProtDisponivelDTO();
            $objRN = new MdPetIntProtDisponivelRN();

            $objDTO->setDblIdDocumento($arrIdsDoc, InfraDTO::$OPER_IN);
            $objDTO->retDblIdDocumento();
            $isIntimacao = $objRN->contar($objDTO) > 0 ? true : false;

            if (!$isIntimacao) {
                $objDTO = new MdPetIntProtocoloDTO();
                $objRN = new MdPetIntProtocoloRN();

                $objDTO->setDblIdDocumento($arrIdsDoc, InfraDTO::$OPER_IN);
                $objDTO->retDblIdDocumento();

                $isIntimacao = $objRN->contar($objDTO) > 0 ? true : false;
            }
        }

        return $isIntimacao;
    }

    private function _getDocumentosProcesso($idProcedimento)
    {
        //Init Vars
        $arrIdsDoc = array();
        $objDocumentoRN = new DocumentoRN();
        $objDocumentoDTO = new DocumentoDTO();

        $objDocumentoDTO->setDblIdProcedimento($idProcedimento);
        $objDocumentoDTO->retDblIdDocumento();
        $count = $objDocumentoRN->contarRN0007($objDocumentoDTO);

        if ($count > 0) {
            $arrObjDTODoc = $objDocumentoRN->listarRN0008($objDocumentoDTO);
            $arrIdsDoc = InfraArray::converterArrInfraDTO($arrObjDTODoc, 'IdDocumento');
        }

        return $arrIdsDoc;
    }

    public function getIntimacoesProcesso($idProcesso)
    {
        $arrIdsDoc = $this->_getDocumentosProcesso($idProcesso);
        $objRN = new MdPetIntProtocoloRN();
        $objDTO = new MdPetIntProtocoloDTO();

        $objDTO->setDblIdDocumento($arrIdsDoc, InfraDTO::$OPER_IN);
        $objDTO->setStrSinPrincipal('S');
        $objDTO->retNumIdMdPetIntimacao();

        $countInt = $objRN->contar($objDTO);
        if ($countInt > 0) {
            $arrObjIntimacoes = $objRN->listar($objDTO);
            $arrIntimacoes = array_unique(InfraArray::converterArrInfraDTO($arrObjIntimacoes, 'IdMdPetIntimacao'));

            return $arrIntimacoes;
        }

        return array();
    }

    /* Função responsável por verificar a exibição de documentos,  se o documento é disponí­vel e anexo retorna 2,
       se ele é disponí­vel retorna 1 e se for somente anexo retorna 0. Obs: Leva em consideração todas as Intimações para o usuário
      logado. */
    protected function retornaIntimacoesVinculadasDocumentoConectado($idDocumento)
    {

        $arrRetorno = array();
        $arrIntDoc = $this->_getIntimacoesPorDocumento($idDocumento, true);
        $arrIntDips = $this->_getIntimacoesPorDocumento($idDocumento, false);

        if ((count($arrIntDoc) > 0) && (count($arrIntDips) > 0)) {
            $arrRetorno['T'] = 2;
        } else if ((count($arrIntDips) > 0) && count($arrIntDoc) == 0) {
            $arrRetorno['T'] = 1;
        } else if ((count($arrIntDoc) > 0) && count($arrIntDips) == 0) {
            $arrRetorno['T'] = 0;
        }

        $arrIntimacoes = array_unique(array_merge($arrIntDoc, $arrIntDips));
        $arrRetorno['I'] = $arrIntimacoes;
        $arrRetorno['A'] = $arrIntDoc;

        return $arrRetorno;
    }

    private function _getIntimacoesPorDocumento($idDocumento, $anexo = false)
    {
        $arrDTO = array();
        $arrRetorno = array();

        if ($anexo) {
            $objDTO = new MdPetIntProtocoloDTO();
            $objRN = new MdPetIntProtocoloRN();
        } else {
            $objDTO = new MdPetIntProtDisponivelDTO();
            $objRN = new MdPetIntProtDisponivelRN();
        }

        $objDTO->setDblIdProtocolo($idDocumento);
        $objDTO->retNumIdMdPetIntimacao();
        $count = $objRN->contar($objDTO);

        if ($count > 0) {
            $arrDTO = $objRN->listar($objDTO);
        }

        $arrRetorno = array_unique(InfraArray::converterArrInfraDTO($arrDTO, 'IdMdPetIntimacao'));

        $this->_removerIntimacoesOutrosContatos($arrRetorno);

        return $arrRetorno;
    }

    protected function getIntimacoesPorContatoConectado($arrProtocoloDoProcesso = array())
    {
        $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
        $objMdPetIntRelDestRN = new MdPetIntRelDestinatarioRN();

        $objContato = $objMdPetIntAceiteRN->retornaObjContatoIdUsuario(array(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()));
        $idContato = $objContato->getNumIdContato();

        $objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
        $objMdPetIntRelDestDTO->setNumIdContatoParticipante($idContato);

        $objMdPetIntRelDestDTO->retNumIdMdPetIntimacao();
        $count = $objMdPetIntRelDestRN->contar($objMdPetIntRelDestDTO);

        if (count($arrProtocoloDoProcesso) > 0) {
            $arrContatosIntimacao = array();
            $objMdPetIntRelDestDTO->retDblIdProtocolo();
            $arrObjIntDest = $objMdPetIntRelDestRN->listar($objMdPetIntRelDestDTO);
            foreach ($arrObjIntDest as $objIntDest) {
                if (!in_array($objIntDest->getNumIdMdPetIntimacao(), $arrContatosIntimacao)) {
                    if (in_array($objIntDest->getDblIdProtocolo(), $arrProtocoloDoProcesso)) {
                        $arrContatosIntimacao[] = $objIntDest->getNumIdMdPetIntimacao();
                    }
                }
            }
            return $arrContatosIntimacao;
        } else {
            if ($count > 0) {
                $arrIntimacoesContato = array_unique(InfraArray::converterArrInfraDTO($objMdPetIntRelDestRN->listar($objMdPetIntRelDestDTO), 'IdMdPetIntimacao'));
                return $arrIntimacoesContato;
            }
        }

        return false;
    }

    private function _removerIntimacoesOutrosContatos(&$arrRetorno)
    {
        if (is_numeric(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno())) {
            $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
            $objMdPetIntRelDestRN = new MdPetIntRelDestinatarioRN();

            $objContato = $objMdPetIntAceiteRN->retornaObjContatoIdUsuario(array(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()));
            $idContato = $objContato->getNumIdContato();

            // @todo precisa ser refatorado o codigo para ser mais perfomatico, pode ocorrer despecho de memoria por trazer todas as intimações do usuario
            $objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
            $objMdPetIntRelDestDTO->setNumIdContatoParticipante($idContato);
            $objMdPetIntRelDestDTO->retNumIdMdPetIntimacao();
            $count = $objMdPetIntRelDestRN->contar($objMdPetIntRelDestDTO);

            if ($count > 0) {
                $arrIntContato = array_unique(InfraArray::converterArrInfraDTO($objMdPetIntRelDestRN->listar($objMdPetIntRelDestDTO), 'IdMdPetIntimacao'));
                $arrIntercept = array_intersect($arrIntContato, $arrRetorno);
                $arrRetorno = array();
                $arrRetorno = $arrIntercept;
            }
        } else {
            $arrRetorno = array();
        }
    }

    protected function getSeriesPermitidasIntimacaoConectado()
    {
        $arrSerie = array();
        $objMdPetIntSerieRN = new MdPetIntSerieRN();

        $objMdPetIntSerieDTO = new MdPetIntSerieDTO();
        $objMdPetIntSerieDTO->retNumIdSerie();

        $count = $objMdPetIntSerieRN->contar($objMdPetIntSerieDTO);
        if ($count > 0) {
            $arrSerie = InfraArray::converterArrInfraDTO($objMdPetIntSerieRN->listar($objMdPetIntSerieDTO), 'IdSerie');
        }

        return $arrSerie;
    }

    protected function existeIntimacaoDocumentoConectado($idDocumento)
    {
        $objMdPetIntDocumentoRN = new MdPetIntProtocoloRN();

        $objMdPetIntDocumentoDTO = new MdPetIntProtocoloDTO();
        $objMdPetIntDocumentoDTO->setDblIdProtocolo($idDocumento);
        $objMdPetIntDocumentoDTO->setStrSinPrincipal('S');
        $objMdPetIntDocumentoDTO->retDblIdDocumento();

        return $objMdPetIntDocumentoRN->contar($objMdPetIntDocumentoDTO) > 0;
    }

    public function verificarUnidadeAbertaConectado($params)
    {

        $objProcedimentoDTO = $params[0];
        $idUnidade = $params[1];

        $strStaNivelAcessoGlobal = $objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo();

        $objAtividadeRN = new MdPetAtividadeRN();

        $objAtividadeDTO = new AtividadeDTO();
        $objAtividadeDTO->setDistinct(true);
        $objAtividadeDTO->retNumIdAtividade();
        $objAtividadeDTO->retNumIdUnidade();
        $objAtividadeDTO->retStrSiglaUnidade();
        $objAtividadeDTO->retStrDescricaoUnidade();

        if ($idUnidade) {
            $objAtividadeDTO->setNumIdUnidade($idUnidade);
        }

        $objAtividadeDTO->setOrdStrSiglaUnidade(InfraDTO::$TIPO_ORDENACAO_ASC);
        $objAtividadeDTO->retNumIdTarefa();
        $objAtividadeDTO->retNumIdUsuarioOrigem();

        if ($strStaNivelAcessoGlobal == ProtocoloRN::$NA_SIGILOSO) {
            $objAtividadeDTO->retNumIdUsuario();
            $objAtividadeDTO->retNumIdUsuarioOrigem();
            $objAtividadeDTO->retStrSiglaUsuario();
            $objAtividadeDTO->retStrNomeUsuario();
        } else {
            $objAtividadeDTO->retNumIdUsuarioAtribuicao();
            $objAtividadeDTO->retStrSiglaUsuarioAtribuicao();
            $objAtividadeDTO->retStrNomeUsuarioAtribuicao();

            //ordena descendente pois no envio de processo que já existe na unidade e está atribuí­do ficará com mais de um andamento em aberto
            //desta forma os andamentos com usuário nulo (envios do processo) serão listados depois
            $objAtividadeDTO->setOrdStrSiglaUsuarioAtribuicao(InfraDTO::$TIPO_ORDENACAO_DESC);
        }

        $objAtividadeDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());

        $objAtividadeDTO->setDthConclusao(null);

        //sigiloso sem credencial - não preparado
        //                        -     considera o Último usuário da unidade informada
        //                        -     considera o Último usuário da Última independente da unidade informada
        if ($strStaNivelAcessoGlobal == ProtocoloRN::$NA_SIGILOSO) {
            //Último usuário da unidade informada
            $objAcessoDTO = new AcessoDTO();
            $objAcessoDTO->retNumIdUsuario();
            $objAcessoDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());
            if ($idUnidade) {
                $objAcessoDTO->setNumIdUnidade($idUnidade);
            }
            $objAcessoDTO->setStrStaTipo(AcessoRN::$TA_CREDENCIAL_PROCESSO);
            $objAcessoDTO->setOrd('IdAcesso', InfraDTO::$TIPO_ORDENACAO_DESC);
            $objAcessoDTO->setNumMaxRegistrosRetorno(1);

            $objAcessoRN = new AcessoRN();
            $arrObjAcessoDTO = $objAcessoRN->listar($objAcessoDTO);

            if (count($arrObjAcessoDTO) == 0) {
                //Último usuário da Última independente da unidade informada
                $objAcessoDTO = new AcessoDTO();
//                $objAcessoDTO->setDistinct(true);
                $objAcessoDTO->retNumIdUsuario();
                $objAcessoDTO->retNumIdUnidade();
                $objAcessoDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());
                $objAcessoDTO->setStrStaTipo(AcessoRN::$TA_CREDENCIAL_PROCESSO);
                $objAcessoDTO->setOrd('IdAcesso', InfraDTO::$TIPO_ORDENACAO_DESC);
                $objAcessoDTO->setNumMaxRegistrosRetorno(1);

                $objAcessoRN = new AcessoRN();
                $arrObjAcessoDTO = $objAcessoRN->listar($objAcessoDTO);

                // sempre tem credencial - não preparado para sem credencial
                if (count($arrObjAcessoDTO) > 0) {
                    $objAtividadeDTO->setNumIdUnidade($arrObjAcessoDTO[0]->getNumIdUnidade());
                }
            }

            // sempre tem credencial - não preparado para sem credencial
            // $objAtividadeDTO->setNumIdUsuario(InfraArray::converterArrInfraDTO($arrObjAcessoDTO, 'IdUsuario'), InfraDTO::$OPER_IN);
        }

        $arrObjAtividadeDTO = $objAtividadeRN->listarRN0036($objAtividadeDTO);

        if ($strStaNivelAcessoGlobal != ProtocoloRN::$NA_SIGILOSO) {
            //filtra andamentos com indicação de usuário atribuí­do
            $arrObjAtividadeDTO = InfraArray::distinctArrInfraDTO($arrObjAtividadeDTO, 'SiglaUnidade');
        }

        return $arrObjAtividadeDTO;

    }

    public function reabrirUnidadeConectado($params)
    {
        try {

            $objProcedimentoDTO = $params[0];
            $idUnidade = $params[1];

            $usuarioTipo = null;    //I-Interno, E-Externo e S-Sistema e/ou  Módulo
            $idUnidadeAtual = null;
            $idUnidadeReabrirProcesso = null;
            $idUnidadeAberta = null;

            $objSEIRN = new SeiRN();

            //unidade esta ativa
            $unidadeDTO = new UnidadeDTO();
            $unidadeDTO->retTodos();
            $unidadeDTO->setBolExclusaoLogica(false);
            $unidadeDTO->setNumIdUnidade($idUnidade);
            $unidadeRN = new UnidadeRN();
            $objUnidadeDTO = $unidadeRN->consultarRN0125($unidadeDTO);

            if ($objUnidadeDTO->getStrSinAtivo() == 'N' || $objUnidadeDTO->getStrSinEnvioProcesso() == 'N' ) {

                $objMdPetAtividadeRN = new MdPetAtividadeRN();
                $arrObjMdPetAtividadeDTO = $objMdPetAtividadeRN->listarUnidadesTramitacao($objProcedimentoDTO);

                foreach ($arrObjMdPetAtividadeDTO as $itemObjMdPetAtividadeDTO) {
                    $unidadeDTO = new UnidadeDTO();
                    $unidadeDTO->retNumIdUnidade();
                    $unidadeDTO->retStrSinAtivo();
                    $unidadeDTO->setBolExclusaoLogica(false);
                    $unidadeDTO->setNumIdUnidade($itemObjMdPetAtividadeDTO->getNumIdUnidade());
                    $unidadeRN = new UnidadeRN();
                    $objUnidadeDTO = $unidadeRN->consultarRN0125($unidadeDTO);
                    if (count($objUnidadeDTO) == 1 && $objUnidadeDTO->getStrSinAtivo() == 'S' && $objUnidadeDTO->getStrSinEnvioProcesso() == 'S') {
                        $idUnidadeAberta = $objUnidadeDTO->getNumIdUnidade();
                        break;
                    }
                }
                if ($idUnidadeAberta) {
                    return $idUnidadeAberta;
                } else {
                    return null;
                }

            } else {

                $idUnidadeReabrirProcesso = $objUnidadeDTO->getNumIdUnidade();

                $numIdUsuario = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();
                if (is_numeric($numIdUsuario)) {
                    $usuarioTipo = "E";
                    //simulando
                    SessaoSEI::getInstance()->simularLogin(null, null, $numIdUsuario, $idUnidadeReabrirProcesso);
                }

                if (!is_numeric($numIdUsuario)) {
                    $numIdUsuario = SessaoSEI::getInstance()->getNumIdUsuario();
                    if (is_numeric($numIdUsuario)) {
                        $usuarioTipo = "I";
                        $idUnidadeAtual = SessaoSEI::getInstance()->getNumIdUnidadeAtual();
                        SessaoSEI::getInstance()->setNumIdUnidadeAtual($idUnidadeReabrirProcesso);
                    }
                }

                if (!is_numeric($numIdUsuario)) {
                    $objContatoDTO = new ContatoDTO();
                    $objContatoDTO->setStrSigla(MdPetContatoRN::$STR_SIGLA_CONTATO_MODULO);
                    $objContatoDTO->retNumIdContato();
                    $objContatoRN = new ContatoRN();
                    $arrObjContatoDTO = $objContatoRN->consultarRN0324($objContatoDTO);

                    $numIdUsuario = count($arrObjContatoDTO) > 0 ? $arrObjContatoDTO->getNumIdContato() : null;
                    if (is_numeric($numIdUsuario)) {
                        $usuarioTipo = "S";
                        $idUnidadeAtual = SessaoSEI::getInstance()->getNumIdUnidadeAtual();
                        SessaoSEI::getInstance()->setNumIdUnidadeAtual($idUnidadeReabrirProcesso);
                    }
                }

                if (is_numeric($numIdUsuario)) {
                    $objEntradaReabrirProcessoAPI = new EntradaReabrirProcessoAPI();
                    $objEntradaReabrirProcessoAPI->setIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
                    $objEntradaReabrirProcessoAPI->setProtocoloProcedimento($objProcedimentoDTO->getStrProtocoloProcedimentoFormatado());
                    $objSEIRN->reabrirProcesso($objEntradaReabrirProcessoAPI);

                    if ($usuarioTipo = "I" || $usuarioTipo = "S") {
                        SessaoSEI::getInstance()->setNumIdUnidadeAtual($idUnidadeAtual);
                    } else if ($usuarioTipo = "E") {
                        //DESTRUIR O SIMULA LOGIN
                    }

                    // Confirmando se foi reaberta
                    $arrAtividadeDTO = $this->verificarUnidadeAberta(array($objProcedimentoDTO, $idUnidadeReabrirProcesso));
                    if (count($arrAtividadeDTO) > 0) {
                        return $idUnidadeReabrirProcesso;
                    }
                }

                return null;

            }

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando Intimação.', $e);
        }

    }

    public function reenviarReatribuirUnidadeConectado($params)
    {
        try {
            $idUnidadeAberta = $params[0];
            $idProcedimento = $params[1];
            $idUsuarioAtribuicao = $params[2];

            // Andamento - Processo remetido pela unidade
            $unidadeDTO = new UnidadeDTO();
            $unidadeDTO->retTodos();
            $unidadeDTO->setBolExclusaoLogica(false);
            $unidadeDTO->setNumIdUnidade($idUnidadeAberta);
            $unidadeRN = new UnidadeRN();
            $unidadeDTO = $unidadeRN->consultarRN0125($unidadeDTO);

            $arrObjAtributoAndamentoDTO = array();
            $objAtributoAndamentoDTO = new AtributoAndamentoDTO();
            $objAtributoAndamentoDTO->setStrNome('UNIDADE');
            $objAtributoAndamentoDTO->setStrValor($unidadeDTO->getStrSigla() . '¥' . $unidadeDTO->getStrDescricao());
            $objAtributoAndamentoDTO->setStrIdOrigem($unidadeDTO->getNumIdUnidade());
            $arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;

            $objAtividadeDTO = new AtividadeDTO();
            $objAtividadeDTO->setDblIdProtocolo($idProcedimento);
            $objAtividadeDTO->setNumIdUnidade($unidadeDTO->getNumIdUnidade());
            $objAtividadeDTO->setNumIdUnidadeOrigem($unidadeDTO->getNumIdUnidade());

            if (!is_null($idUsuarioAtribuicao)) {
                $objAtividadeDTO->setNumIdUsuarioAtribuicao($idUsuarioAtribuicao);
            } else {
                $objAtividadeDTO->unSetNumIdUsuarioAtribuicao();
            }

            $objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO);
            $objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_PROCESSO_REMETIDO_UNIDADE);

            $objAtividadeRN = new AtividadeRN();
            $objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro ao Reenviar e Reatribuir na Intimação.', $e);
        }

    }

    protected function existeIntimacaoPrazoValidoConectado($arrParams)
    {
        $idAcessoExt = false;

        if (is_array($arrParams)) {
            $arrIdsDoc = $this->_getDocumentosProcesso($arrParams[0]);
            $idAcessoExt = $arrParams[1];
        } else {
            $arrIdsDoc = $this->_getDocumentosProcesso($arrParams);
        }

        if (count($arrIdsDoc) == 0) return false;

        $isValidoInt = false;

        $objDTO = new MdPetIntProtocoloDTO();
        $objRN = new MdPetIntProtocoloRN();

        $objDTO->setDblIdDocumento($arrIdsDoc, InfraDTO::$OPER_IN);
        $objDTO->retDblIdDocumento();

        $isDoc = $objRN->contar($objDTO) > 0 ? true : false;

        if ($isDoc) {
            $objDTO->retNumIdMdPetIntimacao();
            $arrObjDTO = $objRN->listar($objDTO);

            $arrIdsInt = array_unique(InfraArray::converterArrInfraDTO($arrObjDTO, 'IdMdPetIntimacao'));
            $arrIdsInt = array_unique($arrIdsInt);


            if (count($arrIdsInt) > 0) {
                $isValidoInt = false;

                $objMdPetIntRelDestRN = new MdPetIntRelDestinatarioRN();
                $objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
                $objMdPetIntRelDestDTO->retDthDataAceite();
                $objMdPetIntRelDestDTO->retNumIdMdPetIntimacao();
                $objMdPetIntRelDestDTO->retNumIdMdPetIntRelDestinatario();
                $objMdPetIntRelDestDTO->retStrNomeTipoRespostaAceita();
                $objMdPetIntRelDestDTO->setNumIdMdPetIntimacao($arrIdsInt, InfraDTO::$OPER_IN);
                if ($idAcessoExt) {
                    $objMdPetIntRelDestDTO->setNumIdAcessoExterno($idAcessoExt);
                }
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
                        return true;
                    }

                    /**
                     * Verifica se o tipo de resposta da Intimação é sem resposta
                     */
                    if ($objDTO->getStrNomeTipoRespostaAceita() != 'S') {
                        $objMdPetIntDestRespostaDto->setNumIdMdPetIntRelDestinatario($objDTO->getNumIdMdPetIntRelDestinatario());

                        $objMdPetIntTpRespDTO->setNumIdMdPetIntimacao($objDTO->getNumIdMdPetIntimacao());

                        /**
                         * Verifica se o tipo de resposta, para verificar se tem mais de uma Intimação a ser respondida
                         */
                        $arrRespostaFacultada = $objMdPetIntTpRespRN->listar($objMdPetIntTpRespDTO);
                        foreach ($arrRespostaFacultada as $arrResposta) {
                            $dataFim = !is_null($arrResposta->getDthDataProrrogada()) ? $arrResposta->getDthDataProrrogada() : $arrResposta->getDthDataLimite();
                            if (is_null($dataFim)) {
                                return true;
                            } else {
                                $arrData = explode(' ', $dataFim);
                                $arrData = count($arrData) > 0 ? explode('/', $arrData[0]) : null;

                                if (count($arrData) > 0) {
                                    $objDateTimeBd = new DateTime();
                                    $objDateTimeBd->setDate($arrData[2], $arrData[1], $arrData[0]);
                                    $objDateTimeAtual = new DateTime();
                                    $isValidoInt = $objDateTimeBd >= $objDateTimeAtual;
                                    if ($isValidoInt) {
                                        return true;
                                    }
                                }
                            }
                        }

                    }
                }
            }
        }
        return $isValidoInt;
    }

    // Destinatarios em massa:
    protected function filtrarContatosPesquisaIntimacaoLoteConectado($post)
    {

        $post['txtUsuario'] = utf8_decode(urldecode($post['txtUsuario']));

        $isSessionExt   = array_key_exists('sessao_externa', $post) ? $post['sessao_externa'] : null;

        $objUsuarioDTO = new MdPetUsuarioExternoDTO();
        $objUsuarioDTO->retNumIdContato();
        $objUsuarioDTO->setStrStaTipo(UsuarioRN::$TU_EXTERNO);

        if (!is_null($isSessionExt) && $isSessionExt != 0) {
            $numIdUSuario = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();
            $objUsuarioDTO->setNumIdUsuario($numIdUSuario, InfraDTO::$OPER_DIFERENTE);
        }

        $objUsuarioDTO->retNumIdContato();
        $objUsuarioDTO->retStrSigla();
        $objUsuarioDTO->retStrNomeContato();
        $objUsuarioDTO->retStrCnpjContato();
        $objUsuarioDTO->retDblCpfContato();
        $objUsuarioDTO->retStrEmailContato();
        $objUsuarioDTO->setStrSinAtivoTipoContato('S');

        $conta      = "^[a-zA-Z0-9\._-]+@";
        $domino     = "[a-zA-Z0-9\._-]+.";
        $extensao   = "([a-zA-Z]{2,4})$";
        $pattern    = "#" . $conta . $domino . $extensao . "#";

        $isEmail = preg_match($pattern, $post['txtUsuario']);

        if ($isEmail) {
            $objUsuarioDTO->setStrEmailContato('%' . $post['txtUsuario'] . '%', InfraDTO::$OPER_LIKE);
        } else {
            $txtPesquisa = InfraString::prepararIndexacao($post['txtUsuario'],true);
            $txtPesquisa = ($txtPesquisa == "") ? $post['txtUsuario'] : $txtPesquisa;
            $objUsuarioDTO->setStrIdxContato('%' . $txtPesquisa . '%', InfraDTO::$OPER_LIKE);
        }

        $objUsuarioDTO->setNumMaxRegistrosRetorno(50);
        $objUsuarioDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);

        $arrObjUsuarioDTO = (new MdPetUsuarioExternoRN())->listar($objUsuarioDTO);

        if($arrObjUsuarioDTO){

            $arrTemp = [];
            foreach($arrObjUsuarioDTO as $objContatoDTO){
                if ($objContatoDTO->getStrSigla() != null && $objContatoDTO->getStrNomeContato() != null) {
                    $strChave = strtolower($objContatoDTO->getStrNomeContato().'-'.$objContatoDTO->getStrSigla());
                    if (!isset($arrTemp[$strChave])) {
                        $arrTemp[$strChave] = array($objContatoDTO);
                    } else {
                        $arrTemp[$strChave][] = $objContatoDTO;
                    }
                }
            }

            foreach($arrTemp as $arr){
            	
                if (count($arr) == 1){
                	
                	$strNome = [];
                	if(!empty($arr[0]->getStrNomeContato())){
                		array_push($strNome, $arr[0]->getStrNomeContato());
	                }
	                if(!empty($arr[0]->getStrSigla())){
		                array_push($strNome, $arr[0]->getStrSigla());
	                }
	                if(!empty($arr[0]->getStrDocContato())){
		                array_push($strNome, InfraUtil::formatarCpfCnpj($arr[0]->getStrDocContato()));
	                }
                	
                    $arr[0]->setStrNome(implode(' - ', $strNome));
	                
                }else{
                	
                    foreach($arr as $dto){
	
	                    $strNome = [];
	                    if(!empty($dto->getStrNomeContato())){
		                    array_push($strNome, $dto->getStrNomeContato());
	                    }
	                    if(!empty($dto->getStrSigla())){
		                    array_push($strNome, $dto->getStrSigla());
	                    }
	                    if(!empty($dto->getStrDocContato())){
		                    array_push($strNome, InfraUtil::formatarCpfCnpj($dto->getStrDocContato()));
	                    }
	
	                    $dto->setStrNome(implode(' - ', $strNome));
                    }
                    
                }
            }

            return $arrObjUsuarioDTO;
        }

        return array();
    }

    // Todo: Remover na sequencia
    protected function filtrarContatosPesquisaIntimacaoConectado($post)
    {

        $isSessionExt = array_key_exists('sessao_externa', $post) ? $post['sessao_externa'] : null;
        $objUsuarioDTO = new MdPetUsuarioExternoDTO();
        $objUsuarioDTO->retNumIdContato();
        $objUsuarioDTO->setStrStaTipo(UsuarioRN::$TU_EXTERNO);

        if (!is_null($isSessionExt) && $isSessionExt != 0) {
            $numIdUSuario = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();
            $objUsuarioDTO->setNumIdUsuario($numIdUSuario, InfraDTO::$OPER_DIFERENTE);
        }

        $objUsuarioRN = new MdPetUsuarioExternoRN();

        if ($_POST['intimacaoPF'] == 't' && $_POST['hdnDadosUsuario']) {
            /** Se o campo intimacaoPF vier preenchido com valor 't' e o campo hdnDadosUsuario tiver
             * preenchido com algum valor, isto quer dizer que pessoa(s) foram adicionadas para geração
             * de Intimação PF e por isso não poderão aparecer na listagem novamente. **/

            $arrNumIdContatos = array();
            $arr = PaginaSEI::getInstance()->getArrItensTabelaDinamica($_POST['hdnDadosUsuario']);
            foreach ($arr as $item) {
                $arrNumIdContatos[] = $item[0];
            }
            $objUsuarioDTO->setNumIdContato($arrNumIdContatos, InfraDTO::$OPER_NOT_IN);
        }

        $objUsuarioDTO->retNumIdContato();
        $objUsuarioDTO->retStrSigla();
        $objUsuarioDTO->retStrNomeContato();
        $objUsuarioDTO->retStrCnpjContato();
        $objUsuarioDTO->setStrSinAtivoTipoContato('S');

        $conta = "^[a-zA-Z0-9\._-]+@";
        $domino = "[a-zA-Z0-9\._-]+.";
        $extensao = "([a-zA-Z]{2,4})$";
        $pattern = "#" . $conta . $domino . $extensao . "#";
        $isEmail = preg_match($pattern, $post['txtUsuario']);
        if ($isEmail) {
            $objUsuarioDTO->setStrEmailContato('%' . $post['txtUsuario'] . '%', InfraDTO::$OPER_LIKE);
        } else {
            $txtPesquisa = InfraString::prepararIndexacao($post['txtUsuario'],true);
            if($txtPesquisa == ""){
                $txtPesquisa = $post['txtUsuario'];
            }
            $objUsuarioDTO->setStrIdxContato('%' . $txtPesquisa . '%', InfraDTO::$OPER_LIKE);
        }
        $objUsuarioDTO->setNumMaxRegistrosRetorno(50);
        $objUsuarioDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);

        $arrObjUsuarioDTO = $objUsuarioRN->listar($objUsuarioDTO);

        if($arrObjUsuarioDTO){
            return $arrObjUsuarioDTO;
        }

        return array();
    }

    // Todo: Remover na sequencia
    protected function filtrarContatosPesquisaIntimacaoJuridicaConectado($post)
    {

        $arrNumIdContatos = array();

        $dtoMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
        $dtoMdPetVincRepresentantDTO->retNumIdMdPetVinculo();
        $dtoMdPetVincRepresentantDTO->retNumIdContatoVinc();
        $dtoMdPetVincRepresentantDTO->retNumIdContatoProcurador();
        $dtoMdPetVincRepresentantDTO->setDistinct(true);
        $dtoMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
        $dtoMdPetVincRepresentantDTO->setStrTpVinc(MdPetVincRepresentantRN::$NT_JURIDICA);

        if ($_POST['intimacaoPJ'] == 't' && $_POST['hdnDadosUsuario']) {

            $arr = PaginaSEI::getInstance()->getArrItensTabelaDinamica($_POST['hdnDadosUsuario']);
            foreach ($arr as $item) {
                //$arrNumIdContatos [] = $_POST['gerados'];
                $arrNumIdContatos[] = $item[0];
            }

            $dtoMdPetVincRepresentantDTO->setNumIdContatoVinc($arrNumIdContatos, InfraDTO::$OPER_NOT_IN);

        }

        $dtoMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
        $dtoMdPetVincRepresentantDTO->setStrIdxContato('%' . $post['txtUsuario'] . '%', InfraDTO::$OPER_LIKE);
        $rnMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
        $arr = $rnMdPetVincRepresentantRN->listar($dtoMdPetVincRepresentantDTO);
        $novo = InfraArray::distinctArrInfraDTO($arr, "IdMdPetVinculo");

        return $novo;

    }

    protected function filtrarContatosPesquisaIntimacaoJuridicaLoteConectado($post)
    {

        $post['txtUsuario'] = utf8_decode(urldecode($post['txtUsuario']));

        $dtoMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
        $dtoMdPetVincRepresentantDTO->retNumIdMdPetVinculo();
        $dtoMdPetVincRepresentantDTO->retNumIdContatoVinc();
        $dtoMdPetVincRepresentantDTO->retNumIdContatoProcurador();
        $dtoMdPetVincRepresentantDTO->setDistinct(true);
        $dtoMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
        $dtoMdPetVincRepresentantDTO->setStrTpVinc(MdPetVincRepresentantRN::$NT_JURIDICA);
        $dtoMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
        $dtoMdPetVincRepresentantDTO->setStrIdxContato('%' . $post['txtUsuario'] . '%', InfraDTO::$OPER_LIKE);
        $arrObjMdPetVincRepresentantDTO = (new MdPetVincRepresentantRN())->listar($dtoMdPetVincRepresentantDTO);

        $arrRepres = array_unique(InfraArray::converterArrInfraDTO($arrObjMdPetVincRepresentantDTO, 'IdContatoVinc'));

        if(is_iterable($arrRepres) && count($arrRepres) > 0){

            $objContatoDTO = new ContatoDTO();
            $objContatoDTO->retStrNome();
            $objContatoDTO->retStrCnpj();
            $objContatoDTO->retNumIdContato();
            $objContatoDTO->setNumIdContato($arrRepres, infraDTO::$OPER_IN);
            $arrObjContatoDTO = (new ContatoRN())->listarRN0325($objContatoDTO);

            if(!empty($arrObjContatoDTO) && count($arrObjContatoDTO) > 0){

                $arrTemp = [];
                foreach($arrObjContatoDTO as $contatoDTO){
                    if ($contatoDTO->get('Nome') != null && $contatoDTO->get('Cnpj') != null) {
                        $strChave = strtolower($contatoDTO->get('Nome').'-'.$contatoDTO->get('Cnpj'));
                        if (!isset($arrTemp[$strChave])) {
                            $arrTemp[$strChave] = array($contatoDTO);
                        } else {
                            $arrTemp[$strChave][] = $contatoDTO;
                        }
                    }
                }

                foreach($arrTemp as $arr){
                    if (count($arr) == 1){
                        $arr[0]->setStrNome($arr[0]->get('Nome').' - '.InfraUtil::formatarCpfCnpj($arr[0]->get('Cnpj')));
                    }else{
                        foreach($arr as $dto){
                            $dto->setStrNome($dto->get('Nome').' - '.InfraUtil::formatarCpfCnpj($dto->get('Cnpj')));
                        }
                    }
                }

                return $arrObjContatoDTO;

            }

        }

        return [];

    }

    protected function getIdProcedimentoPorIntimacaoConectado($idIntimacao)
    {
        $objMdPetIntimacaoRN = new MdPetIntProtocoloRN();
        $objMdPetIntimacaoDTO = new MdPetIntProtocoloDTO();
        $objMdPetIntimacaoDTO->setNumIdMdPetIntimacao($idIntimacao);
        $objMdPetIntimacaoDTO->retDblIdProtocolo();
        $objMdPetIntimacaoDTO->retDblIdProtocoloProcedimento();
        $objMdPetIntimacaoDTO->setNumMaxRegistrosRetorno(1);

        $objMdPetIntimacaoDTO = $objMdPetIntimacaoRN->consultar($objMdPetIntimacaoDTO);

        if (!is_null($objMdPetIntimacaoDTO)) {
            return $objMdPetIntimacaoDTO->getDblIdProtocoloProcedimento();
        }

        return null;
    }

    protected function existeIntimacaoPorContatoConectado($idContato){

        $objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
        $objMdPetIntRelDestDTO->setNumIdContato($idContato);
        $objMdPetIntRelDestDTO->retNumIdMdPetIntimacao();
        $objMdPetIntRelDestDTO->retStrStaSituacaoIntimacao();

        return (new MdPetIntRelDestinatarioBD(BancoSEI::getInstance()))->contar($objMdPetIntRelDestDTO) > 0;

    }

    protected function existeIntimacoesEmCursoPorContatoConectado($idContato)
    {
        $objMdPetIntRelDestRN = new MdPetIntRelDestinatarioRN();
        $objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
        $objMdPetIntRelDestDTO->setNumIdContato($idContato);
        $objMdPetIntRelDestDTO->retNumIdMdPetIntimacao();
        $objMdPetIntRelDestDTO->retStrStaSituacaoIntimacao();
        $arrObjMdPetIntRelDestDTO = $objMdPetIntRelDestRN->listar($objMdPetIntRelDestDTO);

        if ($arrObjMdPetIntRelDestDTO) {
            $objMdPetIntRelTipoRespRN = new MdPetIntRelTipoRespRN();
            $objMdPetIntRelTipoRespDTO = new MdPetIntRelTipoRespDTO();

            foreach ($arrObjMdPetIntRelDestDTO as $mdPetIntRelDestDTO) {
                if ($mdPetIntRelDestDTO->getStrStaSituacaoIntimacao() == self::$INTIMACAO_PENDENTE) {
                    return true;
                } else {

                    $dataAtual = InfraData::getStrDataAtual();

                    $objMdPetIntRelTipoRespDTO->setNumIdMdPetIntimacao($mdPetIntRelDestDTO->getNumIdMdPetIntimacao());
                    $objMdPetIntRelTipoRespDTO->retDthDataProrrogada();
                    $objMdPetIntRelTipoRespDTO->retDthDataLimite();
                    $objMdPetIntRelTipoRespDTO->retNumIdMdPetIntRelTipoResp();
                    $arrObjMdPetIntRelTipoRespDTO = $objMdPetIntRelTipoRespRN->listar($objMdPetIntRelTipoRespDTO);

                    if ($arrObjMdPetIntRelTipoRespDTO) {
                        foreach ($arrObjMdPetIntRelTipoRespDTO as $mdPetIntRelTipoRespDTO) {
                            $dataLimite = $mdPetIntRelTipoRespDTO->getDthDataProrrogada();
                            if (empty($dataLimite)) {
                                $dataLimite = $mdPetIntRelTipoRespDTO->getDthDataLimite();
                            }
                            if (InfraData::compararDatas($dataLimite, $dataAtual) <= 0 || empty($dataLimite)) {
                                return true;
                            }
                        }
                    }
                }
            }
        }

        return false;
    }

    public function verificarVinculoComIntimacoesEmCurso($idContato)
    {
        $mdPetVinculoRN = new MdPetVinculoRN();
        $objMdPetVinculoDTO = new MdPetVinculoDTO();
        $objMdPetVinculoDTO->setNumIdContatoRepresentante($idContato);
        $objMdPetVinculoDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
        $objMdPetVinculoDTO->retNumIdMdPetVinculo();
        $objMdPetVinculoDTO->retNumIdContatoRepresentante();
        $objMdPetVinculoDTO->retNumIdContato();

        $arrObjMdPetVinculoDTO = $mdPetVinculoRN->listar($objMdPetVinculoDTO);
        if ($arrObjMdPetVinculoDTO) {
            foreach ($arrObjMdPetVinculoDTO as $mdPetVinculoDTO) {
                $idContatoVinculo = $mdPetVinculoDTO->getNumIdContato();

                if ($idContatoVinculo != $idContato) {
                    if ($this->existeIntimacoesEmCursoPorContato($idContatoVinculo)) {
                        return true;
                    }
                }
            }
        }
    }

    public function bloquearUltimaConclusaoProcessoSigilosoIntimacaoEmCurso($idProcedimento){

        $objProtocoloDTO = new ProtocoloDTO();
        $objProtocoloDTO->retStrStaNivelAcessoGlobal();
        $objProtocoloDTO->retStrStaNivelAcessoLocal();
        $objProtocoloDTO->setDblIdProtocolo($idProcedimento);
        $objProtocoloDTO = (new ProtocoloRN())->consultarRN0186($objProtocoloDTO);

        if (in_array(ProtocoloRN::$NA_SIGILOSO, [$objProtocoloDTO->getStrStaNivelAcessoGlobal(), $objProtocoloDTO->getStrStaNivelAcessoLocal()])) {

            $existeIntimacaoPrazoValido = $this->existeIntimacaoPrazoValido($idProcedimento);

            if($existeIntimacaoPrazoValido){

                $objDocumentoDTO = new DocumentoDTO();
                $objDocumentoDTO->setDblIdProcedimento($idProcedimento);
                $objDocumentoDTO->retDblIdDocumento();
                $objDocumentoDTO->retStrProtocoloDocumentoFormatado();
                $arrIdsDoc = (new DocumentoRN())->listarRN0008($objDocumentoDTO);

                $arrOficiosPrinc = '';
                $arrObjUnidadeIntimacao = [];

                for ($i=0; $i < count($arrIdsDoc); $i++){

                    $objDTO = new MdPetIntProtocoloDTO();
                    $objDTO->setDblIdDocumento($arrIdsDoc[$i]->getDblIdDocumento());
                    $objDTO->retNumIdMdPetIntimacao();
                    $countPrinc = (new MdPetIntProtocoloRN())->contar($objDTO);

                    if($countPrinc > 0){

                        $objMdPetIntProtocoloDTO = (new MdPetIntProtocoloRN())->listar($objDTO);
                        $arrIdIntimacoes = InfraArray::converterArrInfraDTO($objMdPetIntProtocoloDTO, 'IdMdPetIntimacao');

                        for ($j=0; $j < count($arrIdIntimacoes); $j++){
                            $arrObjUnidadeIntimacao[] = $this->getUnidadeIntimacao([$arrIdIntimacoes[$j]]);
                        }

                        $arrOficiosPrinc .= "- " . $arrIdsDoc[$i]->getStrProtocoloDocumentoFormatado() . "\n";

                    }

                }

                $arrObjUnidadeIntimacao = InfraArray::converterArrInfraDTO($arrObjUnidadeIntimacao, 'IdUnidade');

                if(in_array(SessaoSEI::getInstance()->getNumIdUnidadeAtual(), $arrObjUnidadeIntimacao)){

                    if(!empty($arrOficiosPrinc)){

                        $msgValidacao = (new PeticionamentoIntegracao())->getNome() . ": Em razão do processo ser sigiloso e estar vinculado à sua credencial de acesso não é possível conclui-lo, enquanto existirem Intimações Eletrônicas ainda em curso.\nIsso inclui cumprimento ainda pendente e prazo limite para possí­vel resposta de Intimação disponibilizada sobre os documentos principais abaixo: \n\n";
                        $msgValidacao .= $arrOficiosPrinc;

                        $objInfraException = new InfraException();
                        $objInfraException->adicionarValidacao($msgValidacao);
                        $objInfraException->lancarValidacoes();

                    }

                }

            }

        }

    }

    public function verificaUltimaConclusaoNaUnidade($idProtocolo, $idUnidade)
    {
        $objAtividadeDTO = new AtividadeDTO();
        $objAtividadeDTO->setDblIdProtocolo($idProtocolo);
        $objAtividadeDTO->setNumIdUnidade($idUnidade);
        $objAtividadeDTO->setDthConclusao(null);
        $objAtividadeDTO->retNumIdUnidade();
        $objAtividadeDTO = (new AtividadeRN())->contarRN0035($objAtividadeDTO);

        return ($objAtividadeDTO > 0) ? false : true;
    }

}

?>