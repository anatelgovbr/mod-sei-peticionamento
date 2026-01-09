<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 22/03/2017 - criado por jaqueline.cast
 *
 * Versão do Gerador de Código: 1.40.0
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntDestRespostaRN extends InfraRN
{


    //Id Tarefa Módulo
    public static $ID_TAREFA_MODULO_RESPOSTA_INTIMACAO = 'MD_PET_INTIMACAO_RESPONDIDA';
    public static $ID_TAREFA_MODULO_RESPOSTA_EFETIVADO = 'MD_PET_PETICIONAMENTO_EFETIVADO';
    public static $TIPO_PETICIONAMENTO_INTIMACAO = 'de Resposta a Intimação';
    public static $TIPO_PROCESSO_NOVO = 'de Processo Novo';
    public static $PETICIONAMENTO_INTERCORRENTE= 'Intercorrente';

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function validarNumIdMdPetIntDestResposta(MdPetIntDestRespostaDTO $objMdPetIntDestRespostaDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetIntDestRespostaDTO->getNumIdMdPetIntDestResposta())) {
            $objInfraException->adicionarValidacao(' não informado.');
        }
    }

    private function validarNumIdMdPetIntRelDestinatario(MdPetIntDestRespostaDTO $objMdPetIntDestRespostaDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetIntDestRespostaDTO->getNumIdMdPetIntRelDestinatario())) {
            $objInfraException->adicionarValidacao(' não informado.');
        }
    }

    private function validarStrIp(MdPetIntDestRespostaDTO $objMdPetIntDestRespostaDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetIntDestRespostaDTO->getStrIp())) {
            $objMdPetIntDestRespostaDTO->setStrIp(null);
        } else {
            $objMdPetIntDestRespostaDTO->setStrIp(trim($objMdPetIntDestRespostaDTO->getStrIp()));

            if (strlen($objMdPetIntDestRespostaDTO->getStrIp()) > 45) {
                $objInfraException->adicionarValidacao(' possui tamanho superior a 45 caracteres.');
            }
        }
    }

    private function validarDthData(MdPetIntDestRespostaDTO $objMdPetIntDestRespostaDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetIntDestRespostaDTO->getDthData())) {
            $objMdPetIntDestRespostaDTO->setDthData(null);
        }
    }

    private function validarNumIdMdPetIntRelTipoResp(MdPetIntDestRespostaDTO $objMdPetIntDestRespostaDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetIntDestRespostaDTO->getNumIdMdPetIntRelTipoResp())) {
            $objInfraException->adicionarValidacao(' não informado.');
        }
    }

    protected function cadastrarControlado(MdPetIntDestRespostaDTO $objMdPetIntDestRespostaDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_dest_resposta_cadastrar');

            //Regras de Negocio
            $objInfraException = new InfraException();
            $this->validarNumIdMdPetIntRelDestinatario($objMdPetIntDestRespostaDTO, $objInfraException);
            $this->validarStrIp($objMdPetIntDestRespostaDTO, $objInfraException);
            $this->validarDthData($objMdPetIntDestRespostaDTO, $objInfraException);
            $this->validarNumIdMdPetIntRelTipoResp($objMdPetIntDestRespostaDTO, $objInfraException);
            $objInfraException->lancarValidacoes();


            $objMdPetIntDestRespostaBD = new MdPetIntDestRespostaBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntDestRespostaBD->cadastrar($objMdPetIntDestRespostaDTO);

            //Auditoria

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando .', $e);
        }
    }

    protected function alterarControlado(MdPetIntDestRespostaDTO $objMdPetIntDestRespostaDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_dest_resposta_alterar');

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdPetIntDestRespostaDTO->isSetNumIdMdPetIntDestResposta()) {
                $this->validarNumIdMdPetIntDestResposta($objMdPetIntDestRespostaDTO, $objInfraException);
            }
            if ($objMdPetIntDestRespostaDTO->isSetNumIdMdPetIntRelDestinatario()) {
                $this->validarNumIdMdPetIntRelDestinatario($objMdPetIntDestRespostaDTO, $objInfraException);
            }
            if ($objMdPetIntDestRespostaDTO->isSetStrIp()) {
                $this->validarStrIp($objMdPetIntDestRespostaDTO, $objInfraException);
            }
            if ($objMdPetIntDestRespostaDTO->isSetDthData()) {
                $this->validarDthData($objMdPetIntDestRespostaDTO, $objInfraException);
            }
            if ($objMdPetIntDestRespostaDTO->isSetNumIdMdPetIntRelTipoResp()) {
                $this->validarNumIdMdPetIntRelTipoResp($objMdPetIntDestRespostaDTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();

            $objMdPetIntDestRespostaBD = new MdPetIntDestRespostaBD($this->getObjInfraIBanco());
            $objMdPetIntDestRespostaBD->alterar($objMdPetIntDestRespostaDTO);

            //Auditoria

        } catch (Exception $e) {
            throw new InfraException('Erro alterando .', $e);
        }
    }

    protected function excluirControlado($arrObjMdPetIntDestRespostaDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_dest_resposta_excluir');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntDestRespostaBD = new MdPetIntDestRespostaBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdPetIntDestRespostaDTO); $i++) {
                $objMdPetIntDestRespostaBD->excluir($arrObjMdPetIntDestRespostaDTO[$i]);
            }

            //Auditoria

        } catch (Exception $e) {
            throw new InfraException('Erro excluindo .', $e);
        }
    }

    protected function consultarConectado(MdPetIntDestRespostaDTO $objMdPetIntDestRespostaDTO)
    {
        try {

            //Permissão herdada de:
            //  md_pet_intimacao_cadastrar
            //    MdPetIntimacaoRN->cadastrarIntimacao
            //      MdPetIntimacaoRN->dadosIntimacaoByID
            //        MdPetIntimacaoRN->getSituacaoIntimacao

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_intimacao_cadastrar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntDestRespostaBD = new MdPetIntDestRespostaBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntDestRespostaBD->consultar($objMdPetIntDestRespostaDTO);

            //Auditoria

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando .', $e);
        }
    }

    protected function listarConectado(MdPetIntDestRespostaDTO $objMdPetIntDestRespostaDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_dest_resposta_listar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntDestRespostaBD = new MdPetIntDestRespostaBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntDestRespostaBD->listar($objMdPetIntDestRespostaDTO);

            //Auditoria

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro listando .', $e);
        }
    }

    protected function contarConectado(MdPetIntDestRespostaDTO $objMdPetIntDestRespostaDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_dest_resposta_listar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntDestRespostaBD = new MdPetIntDestRespostaBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntDestRespostaBD->contar($objMdPetIntDestRespostaDTO);

            //Auditoria

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando .', $e);
        }
    }


    protected function salvarRespostaControlado($arrParametros)
    {

        try {

	        LimiteSEI::getInstance()->configurarNivel3();

	        $arrItensTbDocumento = PaginaSEIExterna::getInstance()->getArrItensTabelaDinamica($arrParametros['tbDocumentos']);

            //Salva o corpo
            $seiRN = new SeiRN();
            $objMdPetIntDestRespostaDTO = new MdPetIntDestRespostaDTO();
            $objMdPetIntDestRespostaDTO->setNumIdMdPetIntRelDestinatario($arrParametros['IdMdPetIntRelDestinatario']);
            $objMdPetIntDestRespostaDTO->setStrIp(InfraUtil::getStrIpUsuario());
            $objMdPetIntDestRespostaDTO->setDthData(InfraData::getStrDataAtual());
            $objMdPetIntDestRespostaDTO->setNumIdMdPetIntRelTipoResp($arrParametros['IdMdPetIntRelTipoResp']);
            $objMdPetIntDestRespostaDTO = $this->cadastrar($objMdPetIntDestRespostaDTO);

            //Salva os Documentos
            $objMdPetIntRelRespDocRN = new MdPetIntRelRespDocRN();
            foreach ($arrItensTbDocumento as $itemTbDocumento) {
                $documentoAPI = new DocumentoAPI();
                $objMdPetIntRelRespDocDTO = new MdPetIntRelRespDocDTO();
                $documentoAPI->setIdProcedimento($arrParametros['idProcedimento']);
                $documentoAPI->setIdSerie($itemTbDocumento[1]);
                $documentoAPI->setDescricao($itemTbDocumento[2]);
                $documentoAPI->setNivelAcesso($itemTbDocumento[3]);
                $documentoAPI->setIdHipoteseLegal($itemTbDocumento[4]);
                $documentoAPI->setIdTipoConferencia($itemTbDocumento[6]);
                $documentoAPI->setNomeArquivo($itemTbDocumento[9]);
                $documentoAPI->setTipo(ProtocoloRN::$TP_DOCUMENTO_RECEBIDO);
                $documentoAPI->setConteudo(base64_encode(file_get_contents(DIR_SEI_TEMP . '/' . $itemTbDocumento[7])));
                $documentoAPI->setData(InfraData::getStrDataAtual());
                $documentoAPI->setIdArquivo($itemTbDocumento[7]);
                $documentoAPI->setSinAssinado('S');
                $documentoAPI->setSinBloqueado('S');
                //Salva e popula a tabela
                $objSaidaIncluirDocumentoAPI = $seiRN->incluirDocumento($documentoAPI);
                $objMdPetIntRelRespDocDTO->setDblIdDocumento($objSaidaIncluirDocumentoAPI->getIdDocumento());
                $objMdPetIntRelRespDocDTO->setNumIdMdPetIntDestResposta($objMdPetIntDestRespostaDTO->getNumIdMdPetIntDestResposta());
                $objMdPetIntRelRespDocRN->cadastrar($objMdPetIntRelRespDocDTO);
            }

            $this->lancarAndamentoRecibo($arrParametros);

        } catch (Exception $e) {
            throw new InfraException('Erro contando .', $e);
        }

    }

    protected function verificarHipoteseLegalConectado()
    {
        $objInfraParametroDTO = new InfraParametroDTO();
        $objInfraParametroDTO->setStrNome('SEI_HABILITAR_HIPOTESE_LEGAL');
        $objInfraParametroDTO->retTodos();
        $objInfraParametroRN = new InfraParametroRN();

        $objInfraParametroDTO = $objInfraParametroRN->consultar($objInfraParametroDTO);

        return $objInfraParametroDTO->isSetStrValor() &&
        ($objInfraParametroDTO->getStrValor() == 1 || $objInfraParametroDTO->getStrValor() == 2);
    }

    protected function verificarCriterioIntercorrenteConectado($idTipoProcessoPeticionamento)
    {

        //Verifica se tem criterio intercorrente cadastrado;
        $objCriterioIntercorrenteDTO = new MdPetCriterioDTO();
        $objCriterioIntercorrenteDTO->setNumIdTipoProcedimento($idTipoProcessoPeticionamento);
        $objCriterioIntercorrenteDTO->setStrSinCriterioPadrao('N');
        $objCriterioIntercorrenteDTO->setStrSinAtivo('S');
        $objCriterioIntercorrenteDTO->retTodos(true);

        $objCriterioIntercorrenteRN = new MdPetCriterioRN();
        $objCriterioIntercorrenteDTO = $objCriterioIntercorrenteRN->consultar($objCriterioIntercorrenteDTO);

        //Se não tem criterio intercorrente cadastrado, verifica se tem interorrente padrão cadastrado.
        if (is_null($objCriterioIntercorrenteDTO)) {
            $objCriterioIntercorrenteDTO = new MdPetCriterioDTO();
            $objCriterioIntercorrenteDTO->setStrSinCriterioPadrao('S');
            $objCriterioIntercorrenteDTO->setStrSinAtivo('S');
            $objCriterioIntercorrenteDTO->retTodos(true);

            $objCriterioIntercorrenteRN = new MdPetCriterioRN();
            $objCriterioIntercorrenteDTO = $objCriterioIntercorrenteRN->consultar($objCriterioIntercorrenteDTO);

        }


        $arrRetorno = array();
        if (!is_null($objCriterioIntercorrenteDTO)) {

            $arrDescricaoNivelAcesso = ['P' => 'Público', 'I' => 'Restrito'];
            $arrIdNivelAcesso = ['P' => 0, 'I' => 1];

            if ($objCriterioIntercorrenteDTO->getStrStaNivelAcesso() == 2) { //2 = Padrão Pré-definido
                $descricaoNivel = $arrDescricaoNivelAcesso[$objCriterioIntercorrenteDTO->getStrStaTipoNivelAcesso()];

                $arrRetorno['nivelAcesso'] = array(
                    'id' => $arrIdNivelAcesso[$objCriterioIntercorrenteDTO->getStrStaTipoNivelAcesso()],
                    'descricao' => $descricaoNivel
                );

                if ($objCriterioIntercorrenteDTO->getStrStaTipoNivelAcesso() == 'I') {// I = Restrito
                    $descricaoHipotese = $objCriterioIntercorrenteDTO->getStrNomeHipoteseLegal() .
                        ' (' . $objCriterioIntercorrenteDTO->getStrBaseLegalHipoteseLegal() . ')';

                    $arrRetorno['hipoteseLegal'] = array(
                        'id' => $objCriterioIntercorrenteDTO->getNumIdHipoteseLegal(),
                        'descricao' => $descricaoHipotese
                    );

                }
            }

        }

        return $arrRetorno;

    }

    protected function lancarAndamentoReciboControlado($arrParametros)
    {

        $idProcedimento = $arrParametros['idProcedimento'];
        $idUnidade      = $arrParametros['idUnidade'];

        $objMdPetIntimacaoRN = new MdPetIntimacaoRN;

        // SIGILOSO - concedercredencial
        $objProcedimentoDTO = (new MdPetIntAceiteRN())->_retornaObjProcedimento($idProcedimento);
        if ($objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo() == ProtocoloRN::$NA_SIGILOSO
            || $objProcedimentoDTO->getStrStaNivelAcessoLocalProtocolo() == ProtocoloRN::$NA_SIGILOSO){
            if (is_numeric(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno())){
                $objMdPetProcedimentoRN = new MdPetProcedimentoRN();
                $objConcederCredencial = $objMdPetProcedimentoRN->concederCredencial( array($objProcedimentoDTO,$idUnidade) );
            }
        }
        // SIGILOSO - concedercredencial - FIM

        $idUsuario = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();

        $objUsuarioDTO = new UsuarioDTO();
        $objUsuarioDTO->retStrNome();
        $objUsuarioDTO->setNumIdUsuario($idUsuario);
        $objUsuarioRN = new UsuarioRN();
        $objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

        if (isset($arrParametros['idMdPetIntimacao'])) {
            $objUnidade = $objMdPetIntimacaoRN->getUnidadeIntimacao(array($arrParametros['idMdPetIntimacao']));
            //Unidade da intimação ainda tem credencial
            if ($objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo() == ProtocoloRN::$NA_SIGILOSO
                || $objProcedimentoDTO->getStrStaNivelAcessoLocalProtocolo() == ProtocoloRN::$NA_SIGILOSO){
                if ($idUnidade!=$objUnidade->getNumIdUnidade()){
                    $objUnidade = $objMdPetIntimacaoRN->retornaObjUnidadePorId($idUnidade, true);
                }
            }
            SessaoSEI::getInstance()->simularLogin(null, null, $idUsuario, $objUnidade->getNumIdUnidade());
        }else if(isset($arrParametros['idUnidade'])){
            SessaoSEI::getInstance()->simularLogin(null, null, $idUsuario, $arrParametros['idUnidade']);
        }

        $idDocumento = array_key_exists('idDocumento', $arrParametros) ? $arrParametros['idDocumento'] : null;
        $objEntradaLancarAndamentoAPI = new EntradaLancarAndamentoAPI();

        $objEntradaLancarAndamentoAPI->setIdProcedimento($arrParametros['idProcedimento']);
        $objEntradaLancarAndamentoAPI->setIdTarefaModulo(self::$ID_TAREFA_MODULO_RESPOSTA_EFETIVADO);
        $objMdPetRegrasGeraisRN = new MdPetRegrasGeraisRN();
        $arrObjAtributoAndamentoAPI = array();
        $arrObjAtributoAndamentoAPI[] = $objMdPetRegrasGeraisRN->_retornaObjAtributoAndamentoAPI('TIPO_PETICIONAMENTO', $arrParametros['nomeTipoResposta']);
        $arrObjAtributoAndamentoAPI[] = $objMdPetRegrasGeraisRN->_retornaObjAtributoAndamentoAPI('USUARIO_EXTERNO_NOME', $objUsuarioDTO->getStrNome());
        $arrObjAtributoAndamentoAPI[] = $objMdPetRegrasGeraisRN->_retornaObjAtributoAndamentoAPI('DOCUMENTO', $arrParametros['nomeDocumentoPrincipal'], $idDocumento);

        $objEntradaLancarAndamentoAPI->setAtributos($arrObjAtributoAndamentoAPI);

        $objSeiRN = new SeiRN();
        $objSeiRN->lancarAndamento($objEntradaLancarAndamentoAPI);

        // SIGILOSO - retirando credencial provisória
        if ($objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo() == ProtocoloRN::$NA_SIGILOSO
            || $objProcedimentoDTO->getStrStaNivelAcessoLocalProtocolo() == ProtocoloRN::$NA_SIGILOSO){
            if (is_numeric(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno())){
                $objMdPetProcedimentoRN = new MdPetProcedimentoRN();
                $objCassarCredencial = $objMdPetProcedimentoRN->cassarCredencial( $objConcederCredencial );
                // $objMdPetProcedimentoRN->excluirAndamentoCredencial( $objConcederCredencial ); // Removido por estar causando erro
            }
        }
        // SIGILOSO - retirando credencial provisória - FIM
    }

    private function _retornaObjAtributoAndamentoAPI($nome, $valor, $idOrigem = null)
    {
        $objAtributoAndamentoAPI = new AtributoAndamentoAPI();
        $objAtributoAndamentoAPI->setNome($nome);
        $objAtributoAndamentoAPI->setValor($valor);
        
        if($idOrigem != null){
            $objAtributoAndamentoAPI->setIdOrigem($idOrigem); //ID do prédio, pode ser null
        }
        
        return $objAtributoAndamentoAPI;
    }

}

?>