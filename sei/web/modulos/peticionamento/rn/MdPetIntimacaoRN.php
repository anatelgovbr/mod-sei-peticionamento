<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 14/03/2017 - criado por pedro.cast
 *
 * Versão do Gerador de Código: 1.40.0
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntimacaoRN extends InfraRN {

    //Id Tarefa Módulo
    public static $ID_TAREFA_MODULO_CADASTRO_INTIMACAO = 'MD_PET_INTIMACAO_EXPEDIDA';

    //tipo acesso externo
    public static $TIPO_ACESSO_EXTERNO_INTEGRAL = 'I';
    public static $TIPO_ACESSO_EXTERNO_PARCIAL  = 'P';

    //Id Situação Intimação
    public static $INTIMACAO_PENDENTE            = '1';
    public static $INTIMACAO_CUMPRIDA_PRAZO      = '2';
    public static $INTIMACAO_CUMPRIDA_POR_ACESSO = '3';
    public static $INTIMACAO_RESPONDIDA          = '4';
    public static $INTIMACAO_PRAZO_VENCIDO       = '5';
    public static $TODAS                         = '6';

    //String Situação Intimação
    public static $STR_INTIMACAO_PENDENTE            = 'Pendente';
    public static $STR_INTIMACAO_CUMPRIDA_PRAZO      = 'Cumprida por Decurso do Prazo Tácito';
    public static $STR_INTIMACAO_CUMPRIDA_POR_ACESSO = 'Cumprida por Consulta Direta';
    public static $STR_INTIMACAO_RESPONDIDA          = 'Respondida';
    public static $STR_INTIMACAO_PRAZO_VENCIDO       = 'Pendente de Resposta com Prazo Externo Vencido';
    public static $STR_TODAS                         = 'Todas';
    public static $STR_SITUACAO_NAO_CADASTRADA       = 'Situação não cadastrada';

    //TP = Tipo de Aceite da Intimação
    public static $TP_AUTOMATICO_POR_DECURSO_DE_PRAZO   = 'A';
    public static $TP_MANUAL_USUARIO_EXTERNO_ACEITE     = 'U';

    //Utilizado para Lancamento do Andamento no Processo
    public static $STR_TP_CUMPRIMENTO_LANC_ACESSO_DIRETO    = 'consulta direta';
    public static $STR_TP_MANUAL_USUARIO_EXTERNO_ACEITE     = 'decurso do prazo tácito';

    //String Situação Intimação Aceite
    public static $STR_INTIMACAO_PENDENTE_ACEITE            = 'Pendente';
    public static $STR_INTIMACAO_CUMPRIDA_PRAZO_ACEITE      = 'Por Decurso do Prazo Tácito';
    public static $STR_INTIMACAO_CUMPRIDA_POR_ACESSO_ACEITE = 'Consulta Direta';
    public static $STR_INTIMACAO_RESPONDIDA_ACEITE          = 'Respondida';
    public static $STR_INTIMACAO_PRAZO_VENCIDO_ACEITE       = 'Pendente de Resposta com Prazo Externo Vencido';
    public static $STR_TODAS_ACEITE                         = 'Todas';
    public static $STR_SITUACAO_NAO_CADASTRADA_ACEITE       = 'Situação não cadastrada';

    public function __construct(){
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco(){
        return BancoSEI::getInstance();
    }

    private function validarNumIdMdPetIntTipoIntimacao(MdPetIntimacaoDTO $objMdPetIntimacaoDTO, InfraException $objInfraException){
        if (InfraString::isBolVazia($objMdPetIntimacaoDTO->getNumIdMdPetIntTipoIntimacao())){
            $objInfraException->adicionarValidacao('Tipo de Intimacao não informado.');
        }
    }

    private function validarStrSinTipoAcessoProcesso(MdPetIntimacaoDTO $objMdPetIntimacaoDTO, InfraException $objInfraException){
        if (InfraString::isBolVazia($objMdPetIntimacaoDTO->getStrSinTipoAcessoProcesso())){
            $objInfraException->adicionarValidacao('Sinalizador de Tipo de Acesso não informado.');
        }
    }

    protected function cadastrarControlado(MdPetIntimacaoDTO $objMdPetIntimacaoDTO) {
        try{

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

        }catch(Exception $e){
            throw new InfraException('Erro cadastrando Intimação.',$e);
        }
    }

    protected function alterarControlado(MdPetIntimacaoDTO $objMdPetIntimacaoDTO){
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_intimacao_alterar');

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdPetIntimacaoDTO->isSetNumIdMdPetIntTipoIntimacao()){
                $this->validarNumIdMdPetIntTipoIntimacao($objMdPetIntimacaoDTO, $objInfraException);
            }
            if ($objMdPetIntimacaoDTO->isSetStrSinTipoAcessoProcesso()){
                $this->validarStrSinTipoAcessoProcesso($objMdPetIntimacaoDTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();

            $objMdPetIntimacaoBD = new MdPetIntimacaoBD($this->getObjInfraIBanco());
            $objMdPetIntimacaoBD->alterar($objMdPetIntimacaoDTO);

            //Auditoria

        }catch(Exception $e){
            throw new InfraException('Erro alterando Intimação.',$e);
        }
    }

    protected function excluirControlado($arrObjMdPetIntimacaoDTO){
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_intimacao_excluir');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntimacaoBD = new MdPetIntimacaoBD($this->getObjInfraIBanco());
            for($i=0;$i<count($arrObjMdPetIntimacaoDTO);$i++){
                $objMdPetIntimacaoBD->excluir($arrObjMdPetIntimacaoDTO[$i]);
            }

            //Auditoria

        }catch(Exception $e){
            throw new InfraException('Erro excluindo Intimação.',$e);
        }
    }

    protected function consultarConectado(MdPetIntimacaoDTO $objMdPetIntimacaoDTO){
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
        }catch(Exception $e){
            throw new InfraException('Erro consultando Intimação.',$e);
        }
    }

    protected function listarConectado(MdPetIntimacaoDTO $objMdPetIntimacaoDTO) {
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

        }catch(Exception $e){
            throw new InfraException('Erro listando Intimações.',$e);
        }
    }

    protected function contarConectado(MdPetIntimacaoDTO $objMdPetIntimacaoDTO){
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
        }catch(Exception $e){
            throw new InfraException('Erro contando Intimações.',$e);
        }
    }

    private function _retornaDocumentoPorProtocolo($idDocumento){
        $objDocumentoRN = new DocumentoRN();

        $objDocumentoDTO = new DocumentoDTO();
        $objDocumentoDTO->retNumIdOrgaoUnidadeResponsavel();
        $objDocumentoDTO->retStrProtocoloDocumentoFormatado();
        $objDocumentoDTO->retStrNomeSerie();
        $objDocumentoDTO->retStrNumero();
        $objDocumentoDTO->retNumIdSerie();
        $objDocumentoDTO->setDblIdDocumento($idDocumento);
        $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

        return $objDocumentoDTO;
    }

    private function _retornaProcedimentoPorId($idProtocolo){
        $objProtocoloRN  = new ProcedimentoRN();

        $objProtocoloDTO = new ProcedimentoDTO();
        $objProtocoloDTO->setDblIdProcedimento($idProtocolo);
        $objProtocoloDTO->retStrNomeTipoProcedimento();
        $objProtocoloDTO->retStrProtocoloProcedimentoFormatado();

        return $objProtocoloRN->consultarRN0201($objProtocoloDTO);
    }

    public function dadosIntimacaoByID($id_intimacao, $id_contato){

        //Busca Intimacao
        $objMdPetIntimacaoDTO = new MdPetIntimacaoDTO();
        $objMdPetIntimacaoDTO->retTodos();
        $objMdPetIntimacaoDTO->setNumIdMdPetIntimacao($id_intimacao);

        $objMdPetIntimacaoDTO = $this->consultar($objMdPetIntimacaoDTO);

        //Get Data Intimação
        $objMdPetIntDestinatarioRN = new MdPetIntRelDestinatarioRN();
        $objMdDestDTO              = $objMdPetIntDestinatarioRN->consultarDadosIntimacao($id_intimacao, true, $id_contato);
        $dtIntimacao               = $objMdDestDTO->getDthDataCadastro();
        $idRelDest                 = $objMdDestDTO->getNumIdMdPetIntRelDestinatario();

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
        foreach($tiposResposta as $id => $tipoResposta){
            $tipoResposta = explode('-#-', $tipoResposta);
            $checked = '';
            foreach($objMdPetIntRelTipoRespDTO as $objTipoResposta){
                if($tipoResposta[0] == $objTipoResposta->getNumIdMdPetIntTipoResp()){
                    $strSelect .= '<input type="checkbox" disabled="disabled" class="infraCheckbox" checked="checked"/><label class="infraLabelOpcional">'.$tipoResposta[1].'</label><br/>';
                }
            }
        }

        //Busca Documentos da Intimação
        $objMdPetIntDocumentoDTO = new MdPetIntProtocoloDTO();
        $objMdPetIntDocumentoDTO->retTodos(null);
        $objMdPetIntDocumentoDTO->setNumIdMdPetIntimacao($id_intimacao);


        $objMdPetIntDocumentoRN = new MdPetIntProtocoloRN();
        $objMdPetIntDocumentoDTO = $objMdPetIntDocumentoRN->listar($objMdPetIntDocumentoDTO);

        $arr_protocolos_anexos = '';

        foreach($objMdPetIntDocumentoDTO as $i => $docsIntimacao){
            if($docsIntimacao->getStrSinPrincipal() == 'N')
            {
                $objDocumentoDTO = $this->_retornaDocumentoPorProtocolo($docsIntimacao->getDblIdProtocolo());

                if (!is_null($objDocumentoDTO))
                {
                    $strProtocoloDocumentoFormatado = $objDocumentoDTO->getStrProtocoloDocumentoFormatado();
                    $arr_protocolos_anexos .= '<option>' . DocumentoINT::formatarIdentificacao($objDocumentoDTO) . ' (' . $strProtocoloDocumentoFormatado . ')</option>';
                }
                else
                {
                    $objProcedimentoDTO =  $this->_retornaProcedimentoPorId($docsIntimacao->getDblIdProtocolo());
                    if(!is_null($objProcedimentoDTO)){
                        $str = PaginaSEI::tratarHTML($objProcedimentoDTO->getStrNomeTipoProcedimento().' ('.$objProcedimentoDTO->getStrProtocoloProcedimentoFormatado().')');
                        $arr_protocolos_anexos .= '<option>' . $str .'</option>';
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
        foreach($objMdPetIntDocDisponivelDTO as $i => $docsDisponibilizados){
            $objDocumentoDTO = new DocumentoDTO();
            $objDocumentoDTO->retNumIdOrgaoUnidadeResponsavel();
            $objDocumentoDTO->retStrProtocoloDocumentoFormatado();
            $objDocumentoDTO->retStrNomeSerie();
            $objDocumentoDTO->retStrNumero();
            $objDocumentoDTO->retNumIdSerie();
            $objDocumentoDTO->setDblIdDocumento($docsDisponibilizados->getDblIdProtocolo());
            $objDocumentoRN = new DocumentoRN();
            $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

            if (!is_null($objDocumentoDTO)) {
                $strProtocoloDocumentoFormatado = $objDocumentoDTO->getStrProtocoloDocumentoFormatado();
                $arr_protocolos_disponibilizados .= '<option>' . DocumentoINT::formatarIdentificacao($objDocumentoDTO) . ' (' . $strProtocoloDocumentoFormatado . ')</option>';
            }else{
                $objProcedimentoDTO =  $this->_retornaProcedimentoPorId($docsDisponibilizados->getDblIdProtocolo());
                if(!is_null($objProcedimentoDTO)){
                    $str = PaginaSEI::tratarHTML($objProcedimentoDTO->getStrNomeTipoProcedimento().' ('.$objProcedimentoDTO->getStrProtocoloProcedimentoFormatado().')');
                    $arr_protocolos_anexos .= '<option>' . $str .'</option>';
                }
            }
        }

        $arrSituacao               = MdPetIntRelDestinatarioINT::getArraySituacaoRelatorio();
        $strSituacao               = !is_null($objMdDestDTO->getStrStaSituacaoIntimacao()) && $objMdDestDTO->getStrStaSituacaoIntimacao() != 0 ? $arrSituacao[$objMdDestDTO->getStrStaSituacaoIntimacao()] :MdPetIntimacaoRN::$STR_SITUACAO_NAO_CADASTRADA;

        $dadosIntimacao['id_dest_int']  = $idRelDest;
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

        return $dadosIntimacao;

    }

    protected function retornaIdDocumentoPrincipalIntimacaoConectado($arr){
        $idIntimacao = current($arr);
        $idDocumento =  null;

        $objMdPetIntDocDTO = new MdPetIntProtocoloDTO();
        $objMdPetIntDocRN  = new MdPetIntProtocoloRN();

        $objMdPetIntDocDTO->setNumIdMdPetIntimacao($idIntimacao);
        $objMdPetIntDocDTO->setStrSinPrincipal('S');
        $objMdPetIntDocDTO->retDblIdDocumento();
        $arrObjMdPetIntDocDTO = $objMdPetIntDocRN->listar($objMdPetIntDocDTO);

        if(!is_null($arrObjMdPetIntDocDTO)){
            $obj = count($arrObjMdPetIntDocDTO) > 0 ? current($arrObjMdPetIntDocDTO) : null;
            $idDocumento = $obj ?  $obj->getDblIdDocumento() : null;
        }

        return $idDocumento;
    }


    protected function retornaDadosDocPrincipalIntimacaoConectado($dados){

        $idIntimacao = current($dados);

        $idDocumento = $this->retornaIdDocumentoPrincipalIntimacaoConectado($dados);

        if ($idDocumento) {
            $objDocumentoDTO = new DocumentoDTO();
            $objDocumentoRN = new DocumentoRN();
            $objDocumentoDTO->setDblIdDocumento($idDocumento);
            $objDocumentoDTO->retStrProtocoloDocumentoFormatado();
            $objDocumentoDTO->retStrNomeSerie();
            $objDocumentoDTO->retDblIdProcedimento();
            $objDocumentoDTO->retStrNumero();
            $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

            if ($objDocumentoDTO) {
                return array($objDocumentoDTO->getStrProtocoloDocumentoFormatado(), $objDocumentoDTO->getStrNomeSerie(), $objDocumentoDTO->getDblIdProcedimento(), $idDocumento, $objDocumentoDTO->getStrNumero() );
            }
        }

        return null;
    }

    public function getTextoTolTipIntimacaoEletronica($arr){
        $dtIntimacao = $arr[0];
        $docFormat   = $arr[1];
        $docTipo     = $arr[2];
        $docNum      = $arr[3];
        $anexo       = $arr[4];
        $ToolTipText = $arr[5];

        $ToolTipTitle = 'Intimação Eletrônica: expedida em ';
        $ToolTipTitle .= $dtIntimacao.' ';
        $ToolTipTitle .= '<br/>Documento Principal: ';
        $ToolTipTitle .= $docTipo . ' ';
        if ($docNum){
            $ToolTipTitle .= $docNum. ' ' ;
        }
        $ToolTipTitle .= '(SEI nº ';
        $ToolTipTitle .= $docFormat;
        $ToolTipTitle .= ')';
        if ($anexo=='N') {
            $ToolTipTitle .= '<span style=font-weight: ligther;> - Documento Anexo</span>';
        }

        $ToolTipText = 'Clique para consultar a Intimação e liberar o acesso aos documentos.';
        return array($ToolTipTitle,$ToolTipText);
    }

    public function getTextoTolTipIntimacaoEletronicaCumprida($arr){
        $dtIntimacao = $arr[0];
        $docFormat   = $arr[1];
        $docTipo     = $arr[2];
        $docNum      = $arr[3];
        $anexo       = $arr[4];
        $ToolTipText = $arr[5];

        $ToolTipTitle = 'Intimação Eletrônica: cumprida em ';
        $ToolTipTitle .= $dtIntimacao.' ';
        $ToolTipTitle .= '<br/>Documento Principal: ';
        $ToolTipTitle .= $docTipo . ' ';
        if ($docNum){
            $ToolTipTitle .= $docNum. ' ' ;
        }
        $ToolTipTitle .= '(SEI nº ';
        $ToolTipTitle .= $docFormat;
        $ToolTipTitle .= ')';
        if ($anexo=='N') {
            $ToolTipTitle .= '<span style=font-weight: ligther;> - Documento Anexo</span>';
        }

        $ToolTipText = 'Acesse o documento liberado.';
        return array($ToolTipTitle,$ToolTipText);
    }

    public function retornaLinkCompletoIconeIntimacao($arr){
        $js = $arr[0];
        $img = $arr[1];
        $ToolTipTitle = $arr[2];
        $ToolTipText = $arr[3];

        $conteudoHtml  = '<a onclick="'.$js.'"';
        $conteudoHtml .= 'onmouseover ="return infraTooltipMostrar(\''.$ToolTipText.'\',\''.$ToolTipTitle.'\')"';
        $conteudoHtml .= 'onmouseout="return infraTooltipOcultar()">';
        $conteudoHtml .= $img;
        $conteudoHtml .= '</a>';

        return $conteudoHtml;
    }

    protected function getUnidadeIntimacaoConectado($arr){
        $idIntimacao = isset($arr[0]) ? $arr[0] : null;
        $retTodos    = isset($arr[1]) ? $arr[1] : false;

        if($idIntimacao){
            $idDocumento = $this->retornaIdDocumentoPrincipalIntimacaoConectado($arr);

            $objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
            $objMdPetIntRelDestDTO->setNumIdMdPetIntimacao($idIntimacao);
            $objMdPetIntRelDestDTO->retNumIdUnidade();
            $objMdPetIntRelDestDTO->setNumMaxRegistrosRetorno(1);
            $objMdPetIntRelDestRN = new MdPetIntRelDestinatarioRN();
            $objMdPetIntRelDestDTO = $objMdPetIntRelDestRN->consultar($objMdPetIntRelDestDTO);

            $idUnidade = !is_null($objMdPetIntRelDestDTO) ? $objMdPetIntRelDestDTO->getNumIdUnidade() : null;

            if(is_null($idUnidade)){
                 $idUnidade = $this->_getIdUnidadeDocumentoPrincipal($arr, $idDocumento);
            }

            if($idUnidade != '' && !is_null($idUnidade)){
                $objUnidadeDTO = $this->retornaObjUnidadePorId($idUnidade, $retTodos);
            }

            return $objUnidadeDTO;

        }

        return null;
    }

    private function _buscarUnidadesVinculadasAoProcessoAtivas($idDocumento){
        $objDocumentoDTO = $this->_retornaObjDocumento($idDocumento);

        if(!is_null($objDocumentoDTO)){
            $idProcesso       = $objDocumentoDTO->getDblIdProtocoloProtocolo();

        }

        return null;
    }

    public function retornaObjUnidadePorId($idUnidade, $retTodos){

        $objUnidadeDTO = new UnidadeDTO();
        $objUnidadeRN  = new UnidadeRN();
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

    private function _getIdUnidadeDocumentoPrincipal($arr, $idDocumento){

        if($idDocumento){
            $objDocumentoRN  = new DocumentoRN();
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
        $documentoBD = new DocumentoBD( $this->getObjInfraIBanco() );

        //nao aplicando metodo alterar da RN de Documento por conta de regras de negocio muito especificas aplicadas ali
        $documentoDTO->setStrSinBloqueado('S');
        $documentoBD->alterar( $documentoDTO );
        //remover a liberação de acesso externo //AcessoRN.excluir nao permite exclusao, por isso chame AcessoExternoBD diretamente daqui
    }

    private function _cadastrarObjIntimacao($dadosCadastro){
        $objMdPetIntimacaoDTO = new MdPetIntimacaoDTO();
        $objMdPetIntimacaoDTO->setNumIdMdPetIntimacao(null);
        $objMdPetIntimacaoDTO->setNumIdMdPetIntTipoIntimacao($dadosCadastro['selTipoIntimacao']);
        $objMdPetIntimacaoDTO->setStrSinTipoAcessoProcesso($dadosCadastro['optIntegral'] ? $dadosCadastro['optIntegral'] : $dadosCadastro['optParcial']);
        return $this->cadastrar($objMdPetIntimacaoDTO);
    }

    private function _vincularTipoResposta($arrTiposResposta, $idIntimacao){
        if (count($arrTiposResposta)>0){
        foreach($arrTiposResposta as $tipoResp){

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

    private function _vincularDocumentosIntimacao($idProtocolo, $idIntimacao, $isPrincipal = false){
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

    private function _retornaObjDocumento($idProtocolo){
        //Bloqueia conteudo documento
        $objDocumentoDTO = new DocumentoDTO();
        $objDocumentoRN = new DocumentoRN();
        $objDocumentoDTO->setDblIdDocumento($idProtocolo);
        $objDocumentoDTO->retTodos(true);

        $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

        return $objDocumentoDTO;
    }

    //bloquear (caneta preta) o doc principal da intimação
    private function _bloquearDocIntimacao($idProtocolo){
        $objDocDTO = new DocumentoDTO();
        $objDocDTO->retTodos();
        $objDocDTO->setDblIdDocumento($idProtocolo);
        $docBD = new DocumentoBD( $this->getObjInfraIBanco() );
        $objDocDTO = $docBD->consultar( $objDocDTO );
        if (!is_null($objDocDTO)) {
            $this->bloquearDocumento($objDocDTO);
        }
    }

    private function _vincularAnexosIntimacao($dadosCadastro, $idIntimacao){
        if(isset($dadosCadastro['rdoPossuiAnexo']) == 'S'){

            $arrHdnAnexo = $_POST['hdnIdsDocAnexo'] != '' ? json_decode($_POST['hdnIdsDocAnexo']) : null;

            foreach($arrHdnAnexo as $idAnexosIntimacao){
                   $this->_vincularDocumentosIntimacao($idAnexosIntimacao, $idIntimacao);
            }
        }
    }

    private function _vincularDocumentosDisponiveis($dadosCadastro, $idIntimacao){
        if($dadosCadastro['optParcial'] == MdPetIntAcessoExternoDocumentoRN::$ACESSO_PARCIAL){
            $arrHdnProtDispon = $_POST['hdnIdsDocDisponivel'] != '' ? json_decode($_POST['hdnIdsDocDisponivel']) : null;

            if (count($arrHdnProtDispon)>0){
                foreach($arrHdnProtDispon as $idAnexosDisponibilizados){
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

    private function _vincularDestinatariosIntimacao($dadosCadastro, $idIntimacao, $dataHoraGeracao, $procedimentoFormatado){
        $objMdPetIntAcessoExtDocRN = new MdPetIntAcessoExternoDocumentoRN();
        $objMdPetAcessoExtRN       = new MdPetAcessoExternoRN();

        $arrHdnDadosUsuario = PaginaSEI::getInstance()->getArrItensTabelaDinamica($dadosCadastro['hdnDadosUsuario']);
        $destinatarios = array();
        foreach($arrHdnDadosUsuario as $dadosUsuarios){

            $arrParams = array($dadosUsuarios, $dadosCadastro);

            $idAcessoExt = $objMdPetIntAcessoExtDocRN->controlarAcessoExternoIntimacao($arrParams);

            $destinatarios[] = $dadosUsuarios[1];
            $objMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
            $objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntRelDestinatario(null);
            $objMdPetIntRelDestinatarioDTO->setStrSinAtivo('S');
            $objMdPetIntRelDestinatarioDTO->setStrSinPessoaJuridica('N');
            $objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntimacao($idIntimacao);
            $objMdPetIntRelDestinatarioDTO->setNumIdContato($dadosUsuarios[0]);
            $objMdPetIntRelDestinatarioDTO->setNumIdAcessoExterno($idAcessoExt);
            $objMdPetIntRelDestinatarioDTO->setDthDataCadastro($dataHoraGeracao);
            $objMdPetIntRelDestinatarioDTO->setStrStaSituacaoIntimacao(MdPetIntimacaoRN::$INTIMACAO_PENDENTE);
            $objMdPetIntRelDestinatarioDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());

            $objMdPetIntRelDestinatarioRN = new MdPetIntRelDestinatarioRN();
            $objMdPetIntRelDestinatario = $objMdPetIntRelDestinatarioRN->cadastrar($objMdPetIntRelDestinatarioDTO);

            $objMdPetAcessoExtRN->atualizarIdAcessoExternoModulo($idAcessoExt, MdPetAcessoExternoRN::$MD_PET_INTIMACAO);

            //Envia Email para usuários
            $dadosIntimacao                 = array();
            $dadosIntimacao['POST']         = $dadosCadastro;
            $dadosIntimacao['nome']         = $dadosUsuarios[1];
            $dadosIntimacao['email']        = $dadosUsuarios[2];
            $dadosIntimacao['dataHora']     = $dataHoraGeracao;
            $dadosIntimacao['id_intimacao'] = $idIntimacao;
            $dadosIntimacao['processo']     = $procedimentoFormatado;

            $emailNotificacaoIntimacaoRN = new MdPetIntEmailNotificacaoRN();
            $emailNotificacaoIntimacaoRN->enviarEmailIntimacao($dadosIntimacao);
        }

        return $destinatarios;
    }

    private function _lancarAndamentoIntimacao($dadosCadastro, $dataHoraGeracao, $objDocumentoDTO, $strDestinatarios){
        //Cadastro de Andamento
        $objEntradaLancarAndamentoAPI = new EntradaLancarAndamentoAPI();
        $objEntradaLancarAndamentoAPI->setIdProcedimento($dadosCadastro['hdnIdProcedimento']);
        $objEntradaLancarAndamentoAPI->setIdTarefaModulo(self::$ID_TAREFA_MODULO_CADASTRO_INTIMACAO);

        $arrObjAtributoAndamentoAPI   = array();
        $arrObjAtributoAndamentoAPI[] = $this->_retornaObjAtributoAndamentoAPI('DATA_EXPEDICAO_INTIMACAO', $dataHoraGeracao);
        $arrObjAtributoAndamentoAPI[] = $this->_retornaObjAtributoAndamentoAPI('DOCUMENTO', $objDocumentoDTO->getStrProtocoloDocumentoFormatado(), $objDocumentoDTO->getDblIdDocumento());
        $arrObjAtributoAndamentoAPI[] = $this->_retornaObjAtributoAndamentoAPI('USUARIO_EXTERNO_NOME', implode(', ', $strDestinatarios));

        $objEntradaLancarAndamentoAPI->setAtributos($arrObjAtributoAndamentoAPI);

        $objSeiRN = new SeiRN();
        $objSeiRN->lancarAndamento($objEntradaLancarAndamentoAPI);
    }


    /*
     * Essa função verifica se nas intimações os documentos são anexos e em quais são principais
     *
     * Principal questão tratada na função:
     * Para os anexos ela verifica a qual Intimação de Documento Principal o Anexo pertence
     * */
    private function _getDadosDocumentoIntimacao($idsDocs)
    {
        $arrRetorno = array();

        if (count($idsDocs > 0))
        {
            $objMdPetIntDestRN = new MdPetIntRelDestinatarioRN();

            $objMdPetIntDestDTO1 = new MdPetIntRelDestinatarioDTO();
            $objMdPetIntDestDTO1->retNumIdMdPetIntimacao();
            $objMdPetIntDestDTO1->setDblIdProtocolo($idsDocs, InfraDTO::$OPER_IN);
            $objMdPetIntDestDTO1->retDblIdDocumento();
            $objMdPetIntDestDTO1->retDblIdProtocolo();
            $objMdPetIntDestDTO1->retStrSinPrincipalDoc();

            $arrObjDTOAdd = $objMdPetIntDestRN->listar($objMdPetIntDestDTO1);

            //Se o documento for anexo add o id da intimação, se for principal deixa vazio
            $arrDocIntimacao = array();

            //Array para pesquisar todas as Intimações
            $idsIntimacao = array();
            if (count($arrObjDTOAdd) > 0)
            {
                $idsIntimacao = array_unique(InfraArray::converterArrInfraDTO($arrObjDTOAdd, 'IdMdPetIntimacao'));
            }

            if (count($idsIntimacao) > 0)
            {
                //Consulta todos os dados das Intimações dos Documentos Anexos
                $objMdPetIntDestDTO2 = new MdPetIntRelDestinatarioDTO();
                $objMdPetIntDestDTO1->setNumIdMdPetIntimacao($idsIntimacao, InfraDTO::$OPER_IN);
                $objMdPetIntDestDTO2->setStrSinPrincipalDoc('S');
                $objMdPetIntDestDTO2->retDblIdDocumento();
                $objMdPetIntDestDTO2->retDblIdProtocolo();
                $objMdPetIntDestDTO2->retStrNumero();
                $objMdPetIntDestDTO2->retStrNomeSerie();
                $objMdPetIntDestDTO2->retStrProtocoloFormatadoDocumento();
                $objMdPetIntDestDTO2->retNumIdMdPetIntimacao();
                $objMdPetIntDestDTO2->retStrSinPrincipalDoc();

                $arrObjDTOMain = $objMdPetIntDestRN->listar($objMdPetIntDestDTO2);

                //Verifica a qual documento principal pertence cada documento adicionado
                foreach ($arrObjDTOMain as $objDTOMain)
                {
                    foreach ($arrObjDTOAdd as $objDTOAdd)
                    {
                        $isMesmaIntimacao = $objDTOMain->getNumIdMdPetIntimacao() == $objDTOAdd->getNumIdMdPetIntimacao();
                        if ($isMesmaIntimacao)
                        {
                            $idMain = $objDTOAdd->getNumIdMdPetIntimacao().'_'.$objDTOAdd->getDblIdProtocolo();
                            $arrRetorno[$objDTOAdd->getDblIdProtocolo()][$objDTOAdd->getNumIdMdPetIntimacao()]['idDocMain']   = $objDTOMain->getDblIdProtocolo();
                            $arrRetorno[$objDTOAdd->getDblIdProtocolo()][$objDTOAdd->getNumIdMdPetIntimacao()]['nomeDocMain'] = $this->_formataNomeDocumentoParaIntimacao($objDTOMain);
                            $arrRetorno[$objDTOAdd->getDblIdProtocolo()][$objDTOAdd->getNumIdMdPetIntimacao()]['isPrincipal'] = $objDTOAdd->getDblIdProtocolo() == $objDTOMain->getDblIdProtocolo() ? 'S' : 'N';
                        }
                    }
                }
            }
        }

        return $arrRetorno;
    }


    private function _verificarDocumentosDuplicados($idDocumentoPrinc, $arrIdsAnexo, $hdnIdsUsuarios, $idProcedimento)
    {
        $msg               = '';
        $msgFim            = "\nNão será possível concluir esta intimação. Para prosseguir, antes é necessário remover o Destinatário acima sobre o qual constam os conflitos indicados ou retirar os protocolos do conflito da presente Intimação.";
        $arrMsgAnexo       = array();
        $idsDocs           = $arrIdsAnexo;
        $objMdPetIntDestRN = new MdPetIntRelDestinatarioRN();

        array_push($idsDocs, $idDocumentoPrinc);

        $arrDadosAllDocs = $this->_getDadosDocumentoIntimacao($idsDocs);

        foreach ($hdnIdsUsuarios as $usuario)
        {
            $idContato = $usuario[0];
            $nomeContato = $this->_getNomeContato($idContato);
            $msgInicio   = "O Destinatário ". $nomeContato. " já possui protocolos indicados nesta Intimação a ser gerada vinculados a Intimação anterior neste processo. Veja a lista abaixo: \n\n";

            $objMdPetIntDestDTO = new MdPetIntRelDestinatarioDTO();
            $objMdPetIntDestDTO->retNumIdMdPetIntimacao();
            $objMdPetIntDestDTO->setNumIdContato($idContato);
            $objMdPetIntDestDTO->setDblIdProtocolo($idsDocs, InfraDTO::$OPER_IN);
            $objMdPetIntDestDTO->setDblIdProtocoloProcedimento($idProcedimento);
            $objMdPetIntDestDTO->retDblIdDocumento();
            $objMdPetIntDestDTO->retDblIdProtocolo();
            $objMdPetIntDestDTO->retDblIdProtocoloProcedimento();

            //Dados Documento
            $objMdPetIntDestDTO->retStrSinPrincipalDoc();
            $objMdPetIntDestDTO->retStrNumero();
            $objMdPetIntDestDTO->retStrNomeSerie();
            $objMdPetIntDestDTO->retStrProtocoloFormatadoDocumento();
            $objMdPetIntDestDTO->setOrdStrSinPrincipalDoc(InfraDTO::$TIPO_ORDENACAO_DESC);

            $countDoc = $objMdPetIntDestRN->contar($objMdPetIntDestDTO);
            $arrDuplicados = array();
            if ($countDoc > 0)
            {
                $arrObjDTOUtilizados = $objMdPetIntDestRN->listar($objMdPetIntDestDTO);
                foreach ($arrObjDTOUtilizados as $objDTO)
                {
                    $arrDadosDocMain =  array_key_exists($objDTO->getDblIdProtocolo(), $arrDadosAllDocs) ? $arrDadosAllDocs[$objDTO->getDblIdProtocolo()] : null;
                    $dadosDoc        =  $this->_formatarNomeProtocolo($objDTO);

                    foreach($arrDadosDocMain as $idIntimacao=> $arrDadoDoc){
                    if (!is_null($arrDadoDoc))
                    {

                        $idDocPrincipal = $arrDadoDoc['idDocMain'];
                        $idValidacao = $idDocPrincipal.'_'.$objDTO->getDblIdProtocolo();
                        $notDuplicado = (!array_key_exists($idValidacao, $arrDuplicados));

                        if (array_key_exists($idDocPrincipal, $arrMsgAnexo) && $notDuplicado)
                        {
                            $arrDuplicados[$idValidacao]                    = '';
                            $arrMsgAnexo[$idDocPrincipal]['dadosDoc']      .=  "    ".$dadosDoc."\n";
                        } else
                        {
                           if($notDuplicado)
                           {
                            $arrDuplicados[$idValidacao]                    = '';
                            $arrMsgAnexo[$idDocPrincipal]['dadosDoc']       =  "    ".$dadosDoc."\n";
                            $arrMsgAnexo[$idDocPrincipal]['dadosIntimacao'] = $arrDadoDoc['nomeDocMain'];
                           }
                        }
                    }
                    }
                }

               if(count($arrMsgAnexo) > 0) {
                   $corpoMsg = "";
                  foreach($arrMsgAnexo as $arrMsg){
                      $inicioInt = $corpoMsg != "" ? "\n " : "";
                      $inicioInt .= "* Intimação referente ao Documento Principal ";
                      $inicioInt .= $arrMsg['dadosIntimacao'];
                      $inicioInt .= ": \n \n";
                      $inicioInt .= $arrMsg['dadosDoc'];
                      $corpoMsg .= $inicioInt;
                  }

                   $msg = $msgInicio. $corpoMsg. $msgFim;
               }
            }

            if($msg != ''){
                return $msg;
            }
        }

        return '';
    }

    private function _formatarNomeProtocolo($objDTO){
        $objDocumentoDTO = $this->_retornaDocumentoPorProtocolo($objDTO->getDblIdProtocolo());
        $strReturn       = '';

        if (!is_null($objDocumentoDTO))
        {
            $strReturn = $this->_formataNomeDocumentoParaIntimacao($objDTO);
        } else
        {
            $objProcedimentoDTO = $this->_retornaProcedimentoPorId($objDTO->getDblIdProtocolo());
            if (!is_null($objProcedimentoDTO)) {
                $strReturn = PaginaSEI::tratarHTML($objProcedimentoDTO->getStrNomeTipoProcedimento() . ' (' . $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado() . ') - Anexo');
            }
        }

        return $strReturn;
    }


    private function _formataNomeCompletoDocumento($objDTO){
        $nomeDocCompleto  = !is_null($objDTO) ? $objDTO->getStrProtocoloFormatadoDocumento() : '';
        $nomeSerie        = $objDTO->getStrNomeSerie() != '' ? trim($objDTO->getStrNomeSerie()) : '';
        $nomeSerieNumero  = $objDTO->getStrNumero() != '' ? $nomeSerie. ' '.$objDTO->getStrNumero() : $nomeSerie;
        $nomeDocCompleto .= !is_null($objDTO) ? ' ('.$nomeSerieNumero.')' : '';

        return $nomeDocCompleto;
    }

    private function _formataNomeDocumentoParaIntimacao($objDTO){
        $protocoloForm    = !is_null($objDTO) ? $objDTO->getStrProtocoloFormatadoDocumento() : '';
        $nomeSerie        = $objDTO->getStrNomeSerie() != '' ? trim($objDTO->getStrNomeSerie()) : '';
        $nomeSerieNumero  = $objDTO->getStrNumero() != '' ? $nomeSerie. ' '.$objDTO->getStrNumero() : $nomeSerie;
        $nomeReturn       = $nomeSerieNumero. ' (SEI nº '. $protocoloForm.')';
        $tipoDoc          = $objDTO->getStrSinPrincipalDoc() == 'S' ? ' - Documento Principal' : ' - Anexo';
        $nomeReturn      .= $tipoDoc;
        return $nomeReturn;
    }


    protected function realizarValidacoesCadastroIntimacaoConectado($arr){

        //Init vars
        $msg                   = '';
        $impedimento           = false;
        $alerta                = false;
        $objMdPetAcessoExtRN   = new MdPetAcessoExternoRN();
        $hdnDadosUsuario       = count($arr) > 0 ? current($arr) : null;
        $hdnIdsUsuarios        =  (!is_null($hdnDadosUsuario)) ?  PaginaSEI::getInstance()->getArrItensTabelaDinamica($hdnDadosUsuario) : null;
        $tpAcessoSolicitado    = array_key_exists('1', $arr) ? $arr[1] : null;
        $idProcedimento        = array_key_exists('2', $arr) ? $arr[2] : null;
        $tpsAcessoAnterior     = $objMdPetAcessoExtRN->getUltimaConcAcessoExtModuloPorContatos(array($hdnIdsUsuarios, $idProcedimento));

        $idDocumentoPrinc      = $arr[4];
        $arrIdsAnexo           = $arr[3] != '' ? explode('_', $arr[3]) : array();

        //Realiza as validações relacionadas a duplicação de anexo
        $msg = $this->_verificarDocumentosDuplicados($idDocumentoPrinc, $arrIdsAnexo, $hdnIdsUsuarios, $idProcedimento);
        if($msg != ''){
            return (array($msg, true, false));
        }

        //Realizar Validações de Acesso Externo
        if (count($tpsAcessoAnterior) > 0) {

            // Se já possui acesso integral, e a concessão atual é para acesso Parcial - Impeditivo
            $msg  = $this->_verificarPossuiAcessoIntegralAtualParcial($tpAcessoSolicitado, $tpsAcessoAnterior);

            if($msg != '') {
                $impedimento = true;
            }

            //Se já possui parcial, e a concessão atual é para Acesso Integral - Alerta de Cancelamento do Parcial
            if($msg == '') {
                $msg = $this->_verificarPossuiAcessoParcialAtualIntegral($tpAcessoSolicitado, $tpsAcessoAnterior);

                if($msg != '') {
                    $alerta = true;
                }
            }
        }


        return array($msg, $impedimento, $alerta);
    }

    private function _verificarPossuiAcessoParcialAtualIntegral($tpAcessoSolicitado, $tpsAcessoAnterior){
        $existeAcessoParcial = in_array(MdPetIntAcessoExternoDocumentoRN::$ACESSO_PARCIAL, $tpsAcessoAnterior);
        if ($tpAcessoSolicitado == MdPetIntAcessoExternoDocumentoRN::$ACESSO_INTEGRAL && $existeAcessoParcial) {
            $destinatarios = '';

            foreach($tpsAcessoAnterior as $idContato=> $tpAcessoCadaContato){

                if($tpAcessoCadaContato == MdPetIntAcessoExternoDocumentoRN::$ACESSO_PARCIAL){
                    $destinatarios .= "\n * ";
                    $destinatarios .= $this->_getNomeContato($idContato);
                }
            }

            $msg  = "Os destinatários abaixo listados já possuem Acesso Externo do Tipo Parcial neste processo, concedido por meio do módulo Peticionamento e Intimação Eletrônicos: \n";
            $msg .= $destinatarios;
            $msg .= " \n \n";
            $msg .= "Nova Intimação para os Destinatários acima do Acesso Externo do Tipo Integral neste processo cancelará o Acesso Externo Parcial anterior. Deseja continuar?";
            $msg .= " \n ";

        }

        return $msg;
    }


    private function _verificarPossuiAcessoIntegralAtualParcial($tpAcessoSolicitado, $tpsAcessoAnterior)
    {
        $msg = '';
        $existeAcessoIntegral = in_array(MdPetIntAcessoExternoDocumentoRN::$ACESSO_INTEGRAL, $tpsAcessoAnterior);

        if ($tpAcessoSolicitado == MdPetIntAcessoExternoDocumentoRN::$ACESSO_PARCIAL && $existeAcessoIntegral) {
            $destinatarios = '';

            foreach ($tpsAcessoAnterior as $idContato => $tpAcessoCadaContato) {

                if ($tpAcessoCadaContato == MdPetIntAcessoExternoDocumentoRN::$ACESSO_INTEGRAL) {
                    $destinatarios = "\n * ";
                    $destinatarios .= $this->_getNomeContato($idContato);
                }
            }

            $msg = "A Intimação não pode ser gerada, pois existem Destinatários que já possuem Acesso Externo do Tipo Integral neste processo, concedido por meio de Intimação Eletrônica anterior. Sendo assim, para os Destinatários listados abaixo, as Intimações subsequentes neste processo também devem selecionar o Acesso Externo do Tipo Integral. Caso necessário, para os demais Destinatários, a Intimação pode ser gerada em separado para a lista abaixo e para os que devem de fato receber Acesso Parcial nesta nova Intimação. \n\n";
            $msg .= "Lista dos Destinatários que já possuem Acesso Externo Integral em razão de Intimação Eletrônica anterior neste processo:";
            $msg .= " \n ";
            $msg .= $destinatarios;
        }

        return $msg;
    }

    private function _getNomeContato($idContato){
        $objContatoRN  = new ContatoRN();
        $objContatoDTO = new ContatoDTO();
        $objContatoDTO->setNumIdContato($idContato);
        $objContatoDTO->retStrNome();
        $objContatoDTO = $objContatoRN->consultarRN0324($objContatoDTO);
        $nome = !is_null($objContatoDTO) ? $objContatoDTO->getStrNome()  : '';
        return $nome;
    }


    public function cadastrarIntimacao($dadosCadastro){

        try {
                $dataHoraGeracao = InfraData::getStrDataHoraAtual();
                $objDocumentoRN = new DocumentoRN();

                $objProcedimentoDTO = new ProcedimentoDTO();
                $objProcedimentoRN = new ProcedimentoRN();
                $objProcedimentoDTO->setDblIdProcedimento($dadosCadastro['hdnIdProcedimento']);
                $objProcedimentoDTO->retStrProtocoloProcedimentoFormatado();
                $objProcedimentoDTO = $objProcedimentoRN->consultarRN0201($objProcedimentoDTO);

                $objMdPetIntimacaoDTO = $this->_cadastrarObjIntimacao($dadosCadastro);
                $idIntimacao = $objMdPetIntimacaoDTO->getNumIdMdPetIntimacao();

                $this->_vincularTipoResposta($dadosCadastro['selectItemselTipoResposta'], $idIntimacao);
                $this->_vincularDocumentosIntimacao($dadosCadastro['hndIdDocumento'], $idIntimacao, true);
                $this->_vincularAnexosIntimacao($dadosCadastro, $idIntimacao);
                $this->_vincularDocumentosDisponiveis($dadosCadastro, $idIntimacao);
            
                $strDestinatarios = $this->_vincularDestinatariosIntimacao($dadosCadastro, $idIntimacao, $dataHoraGeracao, $objProcedimentoDTO->getStrProtocoloProcedimentoFormatado());

                $objDocumentoDTO = $this->_retornaObjDocumento($dadosCadastro['hndIdDocumento']);
                $objDocumentoRN->bloquearConteudo($objDocumentoDTO);
                $this->_lancarAndamentoIntimacao($dadosCadastro, $dataHoraGeracao, $objDocumentoDTO, $strDestinatarios);

                return $objMdPetIntimacaoDTO;

        } catch (Exception $e) {
            PaginaSEI::getInstance()->processarExcecao($e);
            return false;
        }
    }

    public function getSituacaoIntimacao($id_intimacao, $id_contato){
        $situacao = " ";

        if(is_null($id_intimacao) || empty($id_intimacao)){
            return MdPetIntimacaoRN::$STR_INTIMACAO_PENDENTE_ACEITE;
        }

        $objMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
        $objMdPetIntRelDestinatarioDTO->retTodos();
        $objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntimacao($id_intimacao);
        $objMdPetIntRelDestinatarioDTO->setNumIdContato($id_contato);

        $objMdPetIntRelDestinatarioRN = new MdPetIntRelDestinatarioRN();
        $objMdPetIntRelDestinatarioDTO = $objMdPetIntRelDestinatarioRN->consultar($objMdPetIntRelDestinatarioDTO);

        if(!empty($objMdPetIntRelDestinatarioDTO)){

            $situacao = MdPetIntimacaoRN::$STR_INTIMACAO_PENDENTE_ACEITE;

            $objMdPetIntAceiteDTO = new MdPetIntAceiteDTO();
            $objMdPetIntAceiteDTO->retTodos();
            $objMdPetIntAceiteDTO->setNumIdMdPetIntRelDestinatario($objMdPetIntRelDestinatarioDTO->getNumIdMdPetIntRelDestinatario());

            $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
            $objMdPetIntAceiteDTO = $objMdPetIntAceiteRN->consultar($objMdPetIntAceiteDTO);

            if(!empty($objMdPetIntAceiteDTO)){

                $objMdPetIntDestRespostaDTO = new MdPetIntDestRespostaDTO();
                $objMdPetIntDestRespostaDTO->retTodos();
                $objMdPetIntDestRespostaDTO->setNumIdMdPetIntRelDestinatario($objMdPetIntRelDestinatarioDTO->getNumIdMdPetIntRelDestinatario());

                //ajustando esta consulta para limitar retorno a 1 registro, e para trazer a resposta mais atual deste destinatario
                //se nao aplicar isso, a tela de consultar detalhes da intimaçao (Processo -> Ver intimaçoes -> lupinha)
                //dá erro por causa de chamada a metodo "consultar" retornando mais de uma linha

                $objMdPetIntDestRespostaDTO->setNumMaxRegistrosRetorno(1);
                $objMdPetIntDestRespostaDTO->setOrd('IdMdPetIntDestResposta', InfraDTO::$TIPO_ORDENACAO_DESC);

                $objMdPetIntDestRespostaRN = new MdPetIntDestRespostaRN();
                $objMdPetIntDestRespostaDTO = $objMdPetIntDestRespostaRN->consultar($objMdPetIntDestRespostaDTO);

                if(!empty($objMdPetIntDestRespostaDTO)){
                    $situacao = MdPetIntimacaoRN::$STR_INTIMACAO_RESPONDIDA_ACEITE;
                }else if($objMdPetIntAceiteDTO->getStrTipoAceite() == MdPetIntimacaoRN::$TP_AUTOMATICO_POR_DECURSO_DE_PRAZO){
                    $situacao = MdPetIntimacaoRN::$STR_INTIMACAO_CUMPRIDA_PRAZO;
                }else if($objMdPetIntAceiteDTO->getStrTipoAceite() == MdPetIntimacaoRN::$TP_MANUAL_USUARIO_EXTERNO_ACEITE){
                    $situacao = MdPetIntimacaoRN::$STR_INTIMACAO_CUMPRIDA_POR_ACESSO;
                }
            }
        }
        return $situacao;
    }

    public function getIntimacoesPossuemDataConectado($params){

        $VerificarRespondidas = isset($params[0]) ? $params[0] : true;
        $ExigeResposta = isset($params[1]) ? $params[1] : false;

        $arrObjMdPetIntRelDestinatarioDTO = null;

        $objMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();

        if ($VerificarRespondidas){
            //RESPOSTA
            $objMdPetIntDestRespostaDTO = new MdPetIntDestRespostaDTO();
            $objMdPetIntDestRespostaDTO->retNumIdMdPetIntRelDestinatario();

            $objMdPetIntDestRespostaRN = new MdPetIntDestRespostaRN();
            $arrObjMdPetIntDestRespostaDTO = $objMdPetIntDestRespostaRN->listar($objMdPetIntDestRespostaDTO);

            if (count($arrObjMdPetIntDestRespostaDTO)>0){
                $arrIdMdPetIntRelDestinatario = InfraArray::converterArrInfraDTO($arrObjMdPetIntDestRespostaDTO,'IdMdPetIntRelDestinatario');
                //SEM RESPOSTA
                $objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntRelDestinatario($arrIdMdPetIntRelDestinatario,InfraDTO::$OPER_NOT_IN);
            }
        }

        $objMdPetIntRelDestinatarioDTO->setAceiteTIPOFK(InfraDTO::$TIPO_FK_OBRIGATORIA);

        // Tipos de Resposta com algum tipo de prazo
        $objMdPetIntRelTipoRespDTO = new MdPetIntRelTipoRespDTO();
        $objMdPetIntRelTipoRespDTO->setStrTipoPrazoExterno('N',InfraDTO::$OPER_DIFERENTE);
        $objMdPetIntRelTipoRespDTO->retNumIdMdPetIntimacao();

        $objMdPetIntRelTipoRespRN = new MdPetIntRelTipoRespRN();
        $arrObjMdPetIntRelTipoRespDTO = $objMdPetIntRelTipoRespRN->listar($objMdPetIntRelTipoRespDTO);

        if (count($arrObjMdPetIntRelTipoRespDTO)>0){
           $arrIdMdPetIntimacao = InfraArray::converterArrInfraDTO($arrObjMdPetIntRelTipoRespDTO,'IdMdPetIntimacao');
           $objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntimacao($arrIdMdPetIntimacao,InfraDTO::$OPER_IN);
        }

        if ($ExigeResposta){
            $objMdPetIntRelDestinatarioDTO->setStrNomeTipoRespostaAceita( MdPetIntTipoIntimacaoRN::$EXIGE_RESPOSTA );
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

    }

    public function buscaIntimacoesCadastradas($idDocumento){
        $arrContatos = array();
        $objMdPetIntDocumentoDTO = new MdPetIntProtocoloDTO();
        $objMdPetIntDocumentoDTO->setDblIdProtocolo($idDocumento);
        $objMdPetIntDocumentoDTO->setStrSinPrincipal('S');
        $objMdPetIntDocumentoDTO->retTodos();

        $objMdPetIntDocumentoRN = new MdPetIntProtocoloRN();
        $arrMdPetIntDocumentoDTO = $objMdPetIntDocumentoRN->listar($objMdPetIntDocumentoDTO);

        if(!empty($arrMdPetIntDocumentoDTO)){
            foreach($arrMdPetIntDocumentoDTO as $MdPetIntDocumento){
                $objMdPetIntRelDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
                $objMdPetIntRelDestinatarioDTO->retTodos();
                $objMdPetIntRelDestinatarioDTO->setNumIdMdPetIntimacao($MdPetIntDocumento->getNumIdMdPetIntimacao());

                $objMdPetIntRelDestinatarioRN = new MdPetIntRelDestinatarioRN();
                $objMdPetIntRelDestinatarioDTO = $objMdPetIntRelDestinatarioRN->listar($objMdPetIntRelDestinatarioDTO);

                if(!empty($objMdPetIntRelDestinatarioDTO)){
                    foreach ($objMdPetIntRelDestinatarioDTO as $MdPetIntRelDestinatario){

                        $arrContatos[] = MdPetContatoINT::getDadosContatos( $MdPetIntRelDestinatario->getNumIdContato(), $idDocumento, false );

                    }
                }
            }
        }
        return $arrContatos;
    }


    protected function retornarDadosIntimacaoPrazoExpiradoControlado($arrParams)
    {
        $dateatual = strtotime(str_replace('/', '-', date('d/m/Y')));
        $diasExp = current($arrParams);
        $arrDados = array();

        $objMdPetIntDestinatarioRN = new MdPetIntRelDestinatarioRN();
        $objMdPetIntDestinatarioDTO = new MdPetIntRelDestinatarioDTO();

        $objMdPetIntDestinatarioDTO->retDthDataCadastro();
        $objMdPetIntDestinatarioDTO->retNumIdMdPetIntimacao();
        $objMdPetIntDestinatarioDTO->retNumIdMdPetIntRelDestinatario();
        $objMdPetIntDestinatarioDTO->retNumIdContato();
        $objMdPetIntDestinatarioDTO->retDthDataCadastro();
        $objMdPetIntDestinatarioDTO->setDistinct(true);

        $objMdPetIntDestinatarioDTO->adicionarCriterio('DataAceite', InfraDTO::$OPER_IGUAL, NULL);

        $objIntimacoesLista = $objMdPetIntDestinatarioRN->listar($objMdPetIntDestinatarioDTO);

        if (count($objIntimacoesLista) > 0) {
            $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
            foreach ($objIntimacoesLista as $objRelDestIntimacao) {
                $dataHrIntimacao = $objRelDestIntimacao->getDthDataCadastro();
                $arrFormtDtInt = explode(" ", $dataHrIntimacao);
                $dataIntimacao = count($arrFormtDtInt) > 0 ? current($arrFormtDtInt) : null;

                $objMdPetIntPrazoRN = new MdPetIntPrazoRN();

                $datafinal = $objMdPetIntPrazoRN->calcularDataPrazo($diasExp, $dataIntimacao);
                $datefinal = strtotime(str_replace('/', '-', $datafinal));

                // TODO: if para PULAR cumprimento por decurso do prazo tácito sobre processo Sigiloso (Nivel de Acesso local OU Global 2),
                //       ate a resolucao do item 22.2 da lista de correções
                $idIntimacao = $objRelDestIntimacao->getNumIdMdPetIntimacao();
                $docPrinc = $objMdPetIntimacaoRN->retornaDadosDocPrincipalIntimacao(array($idIntimacao));
                if (count($docPrinc) > 0) {
                    $idProcedimento = $docPrinc[2];
                } else {
                    $idProcedimento = 0;
                }

                $objProtocoloDTO = new ProtocoloDTO();
                $objProtocoloDTO->retStrStaNivelAcessoLocal();
                $objProtocoloDTO->retStrStaNivelAcessoGlobal();
                $objProtocoloDTO->retStrProtocoloFormatado();
                $objProtocoloDTO->retStrStaEstado();
                $objProtocoloDTO->setDblIdProtocolo($idProcedimento);

                $objProtocoloRN = new ProtocoloRN();
                $objProtocoloDTO = $objProtocoloRN->consultarRN0186($objProtocoloDTO);

                if (count($objProtocoloDTO) > 0) {
                    $strStaNivelAcessoLocal = $objProtocoloDTO->getStrStaNivelAcessoLocal();
                    $strStaNivelAcessoGlobal = $objProtocoloDTO->getStrStaNivelAcessoGlobal();
                } else {
                    $strStaNivelAcessoLocal = ProtocoloRN::$NA_SIGILOSO;
                    $strStaNivelAcessoGlobal = ProtocoloRN::$NA_SIGILOSO;
                }
                // TODO: if para PULAR... - FIM

                if ($dateatual >= $datefinal) {
                    $objRelDestIntimacao->setDthDataFinal($datafinal);
                    $arrDados[] = $objRelDestIntimacao;
                }
    
            }
        }
        return $arrDados;
    }

    private function _retornaObjAtributoAndamentoAPI($nome, $valor, $idOrigem = null) {
        $objAtributoAndamentoAPI = new AtributoAndamentoAPI();
        $objAtributoAndamentoAPI->setNome($nome);
        $objAtributoAndamentoAPI->setValor($valor);
        $objAtributoAndamentoAPI->setIdOrigem($idOrigem); //ID do prédio, pode ser null

        return $objAtributoAndamentoAPI;
    }

    public function getObjDocumentoPorIdDoc($idDocumento){
        $objDocumentoDTO = new DocumentoDTO();
        $objDocumentoRN  = new DocumentoRN();
        $objDocumentoDTO->setDblIdDocumento($idDocumento);
        $objDocumentoDTO->retStrProtocoloDocumentoFormatado();
        $objDocumentoDTO->retStrNomeSerie();
        $objDocumentoDTO->retStrNumero();

        $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

        return $objDocumentoDTO;
    }

    protected function verificaVinculosDocumentoConectado($idDocumento){
        //Init Vars
        $isIntimacao = false;
        $objDTO      = new MdPetIntProtDisponivelDTO();
        $objRN       = new MdPetIntProtDisponivelRN();

        $objDTO->setDblIdDocumento($idDocumento);
        $objDTO->retDblIdDocumento();
        $isIntimacao = $objRN->contar($objDTO) > 0 ? true : false;

        if(!$isIntimacao){
            $objDTO      = new MdPetIntProtocoloDTO();
            $objRN       = new MdPetIntProtocoloRN();

            $objDTO->setDblIdDocumento($idDocumento);
            $objDTO->retDblIdDocumento();

            $isIntimacao = $objRN->contar($objDTO) > 0 ? true : false;
        }

        return $isIntimacao;
    }

    protected function verificaProcessoPossuiIntimacaoConectado($idProcedimento){
        $arrIdsDoc   = $this->_getDocumentosProcesso($idProcedimento);
        $isIntimacao = false;

        if(count($arrIdsDoc)>0){
            $objDTO      = new MdPetIntProtDisponivelDTO();
            $objRN       = new MdPetIntProtDisponivelRN();

            $objDTO->setDblIdDocumento($arrIdsDoc, InfraDTO::$OPER_IN);
            $objDTO->retDblIdDocumento();
            $isIntimacao = $objRN->contar($objDTO) > 0 ? true : false;

            if(!$isIntimacao){
                $objDTO      = new MdPetIntProtocoloDTO();
                $objRN       = new MdPetIntProtocoloRN();

                $objDTO->setDblIdDocumento($arrIdsDoc, InfraDTO::$OPER_IN);
                $objDTO->retDblIdDocumento();

                $isIntimacao = $objRN->contar($objDTO) > 0 ? true : false;
            }
        }

        return $isIntimacao;
    }

    private function _getDocumentosProcesso($idProcedimento){
        //Init Vars
        $arrIdsDoc       = array();
        $objDocumentoRN  = new DocumentoRN();
        $objDocumentoDTO = new DocumentoDTO();

        $objDocumentoDTO->setDblIdProcedimento($idProcedimento);
        $objDocumentoDTO->retDblIdDocumento();
        $count   = $objDocumentoRN->contarRN0007($objDocumentoDTO);

        if ($count > 0) {
            $arrObjDTODoc   = $objDocumentoRN->listarRN0008($objDocumentoDTO);
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
            $arrIntimacoes    = InfraArray::converterArrInfraDTO($arrObjIntimacoes, 'IdMdPetIntimacao');

            return $arrIntimacoes;
        }

        return array();
    }

    /* Função responsável por verificar a exibição de documentos,  se o documento é disponível e anexo retorna 2,
       se ele é disponível retorna 1 e se for somente anexo retorna 0. Obs: Leva em consideração todas as intimações para o usuário
      logado. */
    protected function retornaIntimacoesVinculadasDocumentoConectado($idDocumento){

        $arrRetorno  = array();
        $arrIntDoc   = $this->_getIntimacoesPorDocumento($idDocumento, true);
        $arrIntDips  = $this->_getIntimacoesPorDocumento($idDocumento, false);

        if((count($arrIntDoc) > 0) && (count($arrIntDips) > 0)){
            $arrRetorno['T'] = 2;
        }else if((count($arrIntDips) > 0) && count($arrIntDoc) == 0){
            $arrRetorno['T'] = 1;
        }else if((count($arrIntDoc) > 0) && count($arrIntDips) == 0){
            $arrRetorno['T'] = 0;
        }

        $arrIntimacoes = array_unique(array_merge($arrIntDoc , $arrIntDips));
        $arrRetorno['I']  = $arrIntimacoes;
        $arrRetorno['A'] =  $arrIntDoc;

        return $arrRetorno;
    }

    private function _getIntimacoesPorDocumento($idDocumento, $anexo = false){
        $arrDTO     = array();
        $arrRetorno = array();

        if ($anexo) {
            $objDTO = new MdPetIntProtocoloDTO();
            $objRN  = new MdPetIntProtocoloRN();
        } else {
            $objDTO = new MdPetIntProtDisponivelDTO();
            $objRN  = new MdPetIntProtDisponivelRN();
        }

        $objDTO->setDblIdProtocolo($idDocumento);
        $objDTO->retNumIdMdPetIntimacao();
        $count = $objRN->contar($objDTO);

        if($count > 0){
            $arrDTO = $objRN->listar($objDTO);
        }

        $arrRetorno =  InfraArray::converterArrInfraDTO($arrDTO, 'IdMdPetIntimacao');

        $this->_removerIntimacoesOutrosContatos($arrRetorno);

        return $arrRetorno;
    }

    protected function getIntimacoesPorContatoConectado()
    {
        $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
        $objMdPetIntRelDestRN = new MdPetIntRelDestinatarioRN();

        $objContato = $objMdPetIntAceiteRN->retornaObjContatoIdUsuario(array(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()));
        $idContato = $objContato->getNumIdContato();

        $objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
        $objMdPetIntRelDestDTO->setNumIdContato($idContato);
        $objMdPetIntRelDestDTO->retNumIdMdPetIntimacao();
        $count = $objMdPetIntRelDestRN->contar($objMdPetIntRelDestDTO);

        if ($count > 0) {
            $arrIntimacoesContato = InfraArray::converterArrInfraDTO($objMdPetIntRelDestRN->listar($objMdPetIntRelDestDTO), 'IdMdPetIntimacao');
            return $arrIntimacoesContato;
        }
        
        return false;
    }

    private function _removerIntimacoesOutrosContatos(&$arrRetorno){
        if (is_numeric(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno())){
            $objMdPetIntAceiteRN  = new MdPetIntAceiteRN();
            $objMdPetIntRelDestRN = new MdPetIntRelDestinatarioRN();

            $objContato           =  $objMdPetIntAceiteRN->retornaObjContatoIdUsuario(array(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()));
            $idContato            = $objContato->getNumIdContato();

            $objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
            $objMdPetIntRelDestDTO->setNumIdContato($idContato);
            $objMdPetIntRelDestDTO->retNumIdMdPetIntimacao();
            $count = $objMdPetIntRelDestRN->contar($objMdPetIntRelDestDTO);

            if($count > 0){
                $arrIntContato =  InfraArray::converterArrInfraDTO($objMdPetIntRelDestRN->listar($objMdPetIntRelDestDTO), 'IdMdPetIntimacao');
                $arrIntercept  = array_intersect($arrIntContato, $arrRetorno);
                $arrRetorno    = array();
                $arrRetorno    = $arrIntercept;
            }
        }else{
            $arrRetorno    = array();
        }
    }

    protected function getSeriesPermitidasIntimacaoConectado(){
        $arrSerie            = array();
        $objMdPetIntSerieRN  = new MdPetIntSerieRN();

        $objMdPetIntSerieDTO = new MdPetIntSerieDTO();
        $objMdPetIntSerieDTO->retNumIdSerie();

        $count = $objMdPetIntSerieRN->contar( $objMdPetIntSerieDTO );
        if($count > 0){
            $arrSerie =   InfraArray::converterArrInfraDTO($objMdPetIntSerieRN->listar( $objMdPetIntSerieDTO ), 'IdSerie');
        }

        return $arrSerie;
    }

    protected function existeIntimacaoDocumentoConectado($idDocumento){
        $objMdPetIntDocumentoRN  = new MdPetIntProtocoloRN();

        $objMdPetIntDocumentoDTO = new MdPetIntProtocoloDTO();
        $objMdPetIntDocumentoDTO->setDblIdProtocolo($idDocumento);
        $objMdPetIntDocumentoDTO->setStrSinPrincipal('S');
        $objMdPetIntDocumentoDTO->retDblIdDocumento();

        return $objMdPetIntDocumentoRN->contar($objMdPetIntDocumentoDTO) > 0;
    }

    public function verificarUnidadeAbertaConectado( $params ){

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

        if ($idUnidade){
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

            //ordena descendente pois no envio de processo que já existe na unidade e está atribuído ficará com mais de um andamento em aberto
            //desta forma os andamentos com usuário nulo (envios do processo) serão listados depois
            $objAtividadeDTO->setOrdStrSiglaUsuarioAtribuicao(InfraDTO::$TIPO_ORDENACAO_DESC);
        }

        $objAtividadeDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());

        $objAtividadeDTO->setDthConclusao(null);

        //sigiloso sem credencial - não preparado
        //                        -     considera o último usuário da unidade informada
        //                        -     considera o último usuário da última independente da unidade informada
        if ($strStaNivelAcessoGlobal == ProtocoloRN::$NA_SIGILOSO) {
            //último usuário da unidade informada
            $objAcessoDTO = new AcessoDTO();
            $objAcessoDTO->retNumIdUsuario();
            $objAcessoDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());
            if ($idUnidade){
                $objAcessoDTO->setNumIdUnidade($idUnidade);
            }
            $objAcessoDTO->setStrStaTipo(AcessoRN::$TA_CREDENCIAL_PROCESSO);
            $objAcessoDTO->setOrd('IdAcesso', InfraDTO::$TIPO_ORDENACAO_DESC);
            $objAcessoDTO->setNumMaxRegistrosRetorno(1);

            $objAcessoRN = new AcessoRN();
            $arrObjAcessoDTO = $objAcessoRN->listar($objAcessoDTO);

            if (count($arrObjAcessoDTO)==0){
                //último usuário da última independente da unidade informada
                $objAcessoDTO = new AcessoDTO();
                $objAcessoDTO->setDistinct(true);
                $objAcessoDTO->retNumIdUsuario();
                $objAcessoDTO->retNumIdUnidade();
                $objAcessoDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());
                $objAcessoDTO->setStrStaTipo(AcessoRN::$TA_CREDENCIAL_PROCESSO);
                $objAcessoDTO->setOrd('IdAcesso', InfraDTO::$TIPO_ORDENACAO_DESC);
                $objAcessoDTO->setNumMaxRegistrosRetorno(1);

                $objAcessoRN = new AcessoRN();
                $arrObjAcessoDTO = $objAcessoRN->listar($objAcessoDTO);

                // sempre tem credencial - não preparado para sem credencial
                if (count($arrObjAcessoDTO)>0){
                    $objAtividadeDTO->setNumIdUnidade($arrObjAcessoDTO[0]->getNumIdUnidade());
                }
            }

            // sempre tem credencial - não preparado para sem credencial
            $objAtividadeDTO->setNumIdUsuario(InfraArray::converterArrInfraDTO($arrObjAcessoDTO, 'IdUsuario'), InfraDTO::$OPER_IN);
        }

        $arrObjAtividadeDTO = $objAtividadeRN->listarRN0036($objAtividadeDTO);

        if ($strStaNivelAcessoGlobal != ProtocoloRN::$NA_SIGILOSO) {
            //filtra andamentos com indicação de usuário atribuído
            $arrObjAtividadeDTO = InfraArray::distinctArrInfraDTO($arrObjAtividadeDTO, 'SiglaUnidade');
        }

        if (count($arrObjAtividadeDTO) == 0) {
        }

        return $arrObjAtividadeDTO;

    }

    public function reabrirUnidadeConectado( $params ){
        try{

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

            if($objUnidadeDTO->getStrSinAtivo() == 'N'){
                $objAtividadeRN  = new AtividadeRN();
                //VERIFICAR SE RETORNA ÚLTIMA OU QUALQUER UMA
                $arrObjUnidadeDTO = $objAtividadeRN->listarUnidadesTramitacao($objProcedimentoDTO);
                foreach ($arrObjUnidadeDTO as $itemObjUnidadeDTO) {
                    $unidadeDTO = new UnidadeDTO();
                    $unidadeDTO->retStrSinAtivo();
                    $unidadeDTO->setNumIdUnidade($itemObjUnidadeDTO->getNumIdUnidade());
                    $unidadeRN = new UnidadeRN();
                    $objUnidadeDTO = $unidadeRN->consultarRN0125($unidadeDTO);
                    if (count($objUnidadeDTO)==1 && $objUnidadeDTO->getStrSinAtivo() == 'S') {
                        $idUnidadeAberta = $itemObjUnidadeDTO->getNumIdUnidade();
                        break;
                    }
                }
                if ($idUnidadeAberta) {
                    return $idUnidadeAberta;
                }else{
                    return null;
                }

            }else{

                $idUnidadeReabrirProcesso = $objUnidadeDTO->getNumIdUnidade();

                $numIdUsuario = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();
                if ( is_numeric($numIdUsuario) ){
                    $usuarioTipo = "E";
                    //simulando
                    SessaoSEI::getInstance()->simularLogin(null, null, $numIdUsuario, $idUnidadeReabrirProcesso );
                }

                if ( !is_numeric($numIdUsuario) ){
                    $numIdUsuario = SessaoSEI::getInstance()->getNumIdUsuario();
                    if ( is_numeric($numIdUsuario) ){
                        $usuarioTipo = "I";
                        $idUnidadeAtual = SessaoSEI::getInstance()->getNumIdUnidadeAtual();
                        SessaoSEI::getInstance()->setNumIdUnidadeAtual( $idUnidadeReabrirProcesso );
                    }
                }

                if ( !is_numeric($numIdUsuario) ){
                    $objContatoDTO = new ContatoDTO();
                    $objContatoDTO->setStrSigla(MdPetContatoRN::$STR_SIGLA_CONTATO_MODULO);
                    $objContatoDTO->retNumIdContato();
                    $objContatoRN = new ContatoRN();
                    $arrObjContatoDTO = $objContatoRN->consultarRN0324($objContatoDTO);

                    $numIdUsuario = count($arrObjContatoDTO) > 0 ? $arrObjContatoDTO->getNumIdContato() : null;
                    if ( is_numeric($numIdUsuario) ){
                        $usuarioTipo = "S";
                        $idUnidadeAtual = SessaoSEI::getInstance()->getNumIdUnidadeAtual();
                        SessaoSEI::getInstance()->setNumIdUnidadeAtual( $idUnidadeReabrirProcesso );
                    }
                }

                if ( is_numeric($numIdUsuario) ){
                    $objEntradaReabrirProcessoAPI = new EntradaReabrirProcessoAPI();
                    $objEntradaReabrirProcessoAPI->setIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
                    $objEntradaReabrirProcessoAPI->setProtocoloProcedimento($objProcedimentoDTO->getStrProtocoloProcedimentoFormatado());
                    $objSEIRN->reabrirProcesso($objEntradaReabrirProcessoAPI);

                    if ($usuarioTipo = "I" || $usuarioTipo = "S"){
                        SessaoSEI::getInstance()->setNumIdUnidadeAtual($idUnidadeAtual);
                    }else if ($usuarioTipo = "E"){
                        //DESTRUIR O SIMULA LOGIN
                    }

                    // Confirmando se foi reaberta
                    $arrAtividadeDTO = $this->verificarUnidadeAberta( array($objProcedimentoDTO, $idUnidadeReabrirProcesso) );
                    if (count($arrAtividadeDTO)>0){
                        return $idUnidadeReabrirProcesso;
                    }
                }

                return null;

            }

        }catch(Exception $e){
            throw new InfraException('Erro cadastrando Intimação.',$e);
        }

    }

    public function reenviarReatribuirUnidadeConectado( $params ){
        try{
            $idUnidadeAberta     = $params[0];
            $idProcedimento      = $params[1];
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
            $objAtributoAndamentoDTO->setStrValor($unidadeDTO->getStrSigla().'¥'.$unidadeDTO->getStrDescricao());
            $objAtributoAndamentoDTO->setStrIdOrigem($unidadeDTO->getNumIdUnidade());
            $arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;

            $objAtividadeDTO = new AtividadeDTO();
            $objAtividadeDTO->setDblIdProtocolo($idProcedimento);
            $objAtividadeDTO->setNumIdUnidade( $unidadeDTO->getNumIdUnidade() );
            $objAtividadeDTO->setNumIdUnidadeOrigem( $unidadeDTO->getNumIdUnidade() );

            if ( !is_null($idUsuarioAtribuicao) ){
                $objAtividadeDTO->setNumIdUsuarioAtribuicao( $idUsuarioAtribuicao );
            }else{
                $objAtividadeDTO->unSetNumIdUsuarioAtribuicao();
            }

            $objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO);
            $objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_PROCESSO_REMETIDO_UNIDADE);

            $objAtividadeRN = new AtividadeRN();
            $objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);
        }catch(Exception $e){
            throw new InfraException('Erro ao Reenviar e Reatribuir na Intimação.',$e);
        }

    }

    protected function existeIntimacaoPrazoValidoConectado($arrParams)
    {
      $idAcessoExt = false;

      if(is_array($arrParams)){
          $arrIdsDoc   = $this->_getDocumentosProcesso($arrParams[0]);
          $idAcessoExt = $arrParams[1];
      }else{
          $arrIdsDoc = $this->_getDocumentosProcesso($arrParams);
      }

      if (count($arrIdsDoc)==0)  return false;

      $isValidoInt = false;

      $objDTO = new MdPetIntProtocoloDTO();
      $objRN = new MdPetIntProtocoloRN();

      $objDTO->setDblIdDocumento($arrIdsDoc, InfraDTO::$OPER_IN);
      $objDTO->retDblIdDocumento();

      $isDoc = $objRN->contar($objDTO) > 0 ? true : false;

      if ($isDoc) {
        $objDTO->retNumIdMdPetIntimacao();
        $arrObjDTO = $objRN->listar($objDTO);

        $arrIdsInt = InfraArray::converterArrInfraDTO($arrObjDTO, 'IdMdPetIntimacao');
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
          if($idAcessoExt) {
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
                      return true;
                    }else{
                      $arrData = explode(' ', $dataFim);
                      $arrData = count($arrData) > 0 ? explode('/', $arrData[0]) : null;

                      if (count($arrData) > 0) {
                        $objDateTimeBd = new DateTime();
                        $objDateTimeBd->setDate($arrData[2], $arrData[1], $arrData[0]);
                        $objDateTimeAtual = new DateTime();
                        $isValidoInt = $objDateTimeBd >= $objDateTimeAtual;
                        if($isValidoInt){
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

    protected function filtrarContatosPesquisaIntimacaoConectado($post){

        $objUsuarioDTO = new UsuarioDTO();
        $objUsuarioDTO->setStrStaTipo( UsuarioRN::$TU_EXTERNO );
        $objUsuarioDTO->retNumIdContato();

        $objUsuarioRN     =  new UsuarioRN();
        $arrObjUsuarioDTO = $objUsuarioRN->listarRN0490($objUsuarioDTO);

        $idsContatoUsuarioExterno = InfraArray::converterArrInfraDTO($arrObjUsuarioDTO, 'IdContato');

        if(count($idsContatoUsuarioExterno) > 0) {
            $objContatoDTO = new ContatoDTO();
            $objContatoDTO->retNumIdContato();
            $objContatoDTO->retStrSigla();
            $objContatoDTO->retStrNome();
            $objContatoDTO->retStrEmail();
            $objContatoDTO->setStrSinAtivoTipoContato('S');
            $objContatoDTO->setNumIdContato($idsContatoUsuarioExterno, InfraDTO::$OPER_IN);
            $objContatoDTO->setStrIdxContato('%'.$post['txtUsuario'].'%', InfraDTO::$OPER_LIKE);
            $objContatoDTO->setNumMaxRegistrosRetorno(50);
            $objContatoDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);


            $objContatoRN = new ContatoRN();
            $arrObjContatoDTO = $objContatoRN->listarRN0325($objContatoDTO);

            return $arrObjContatoDTO;
        }

        return array();
    }
}
?>