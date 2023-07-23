<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 02/04/2018 - criado por jose vieira
 *
 * Versão do Gerador de Código: 1.41.0
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetVincRepresentantRN extends InfraRN
{

    //Procuração Eletrônica
    public static $PE_RESPONSAVEL_LEGAL = 'L';
    public static $PE_PROCURADOR_ESPECIAL = 'E';
    public static $PE_PROCURADOR = 'C';
    public static $PE_PROCURADOR_SIMPLES = 'S';
    public static $PE_AUTORREPRESENTACAO = 'U';

    //Representação - Estado
    public static $RP_ATIVO = 'A';
    public static $RP_SUSPENSO = 'S';
    public static $RP_REVOGADA = 'R';
    public static $RP_RENUNCIADA = 'C';
    public static $RP_VENCIDA = 'V';
    public static $RP_SUBSTITUIDA = 'T';
    public static $RP_INATIVO = 'I';

    //Abrangência
    public static $PR_ESPECIFICO = 'E';
    public static $PR_QUALQUER = 'Q';

    public static $STR_PROCURADOR_ESPECIAL = 'Procurador Especial';
    public static $STR_RESPONSAVEL_LEGAL = 'Responsável Legal';
    public static $STR_PROCURADOR_SIMPLES = 'Procurador Simples';
    public static $STR_AUTORREPRESENTACAO = 'Autorrepresentação de Usuário Externo';

    //Natureza
    public static $NT_FISICA = 'F';
    public static $NT_JURIDICA = 'J';

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    private function validarNumIdMdPetVinculoRepresent(MdPetVincRepresentantDTO $objMdPetVincRepresentantDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent())) {
            $objInfraException->adicionarValidacao('IdMdPetVinculoRepresent não informado.');
        }
    }

    private function validarNumIdMdPetVinculo(MdPetVincRepresentantDTO $objMdPetVincRepresentantDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetVincRepresentantDTO->getNumIdMdPetVinculo())) {
            $objInfraException->adicionarValidacao('IdMdPetVinculo não informado.');
        }
    }

    private function validarNumIdContato(MdPetVincRepresentantDTO $objMdPetVincRepresentantDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetVincRepresentantDTO->getNumIdContato())) {
            $objInfraException->adicionarValidacao('IdContato não informado.');
        }
    }

    private function validarNumIdContatoOutorg(MdPetVincRepresentantDTO $objMdPetVincRepresentantDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetVincRepresentantDTO->getNumIdContatoOutorg())) {
            $objInfraException->adicionarValidacao('IdContatoOutorg não informado.');
        }
    }

    private function validarNumIdAcessoExterno(MdPetVincRepresentantDTO $objMdPetVincRepresentantDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetVincRepresentantDTO->getNumIdAcessoExterno())) {
            $objInfraException->adicionarValidacao('IdAcessoExterno não informado.');
        }
    }

    private function validarStrTipoRepresentante(MdPetVincRepresentantDTO $objMdPetVincRepresentantDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetVincRepresentantDTO->getStrTipoRepresentante())) {
            $objInfraException->adicionarValidacao(' TipoRepresentante não informado.');
        } else {
            $objMdPetVincRepresentantDTO->setStrTipoRepresentante(trim($objMdPetVincRepresentantDTO->getStrTipoRepresentante()));

            if (strlen($objMdPetVincRepresentantDTO->getStrTipoRepresentante()) > 1) {
                $objInfraException->adicionarValidacao(' possui tamanho superior a 1 caracteres.');
            }
        }
    }

    private function validarDthDataCadastro(MdPetVincRepresentantDTO $objMdPetVincRepresentantDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetVincRepresentantDTO->getDthDataCadastro())) {
            $objInfraException->adicionarValidacao('DthDataCadastro não informad.');
        }
    }

    private function validarStrStaEstado(MdPetVincRepresentantDTO $objMdPetVincRepresentantDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetVincRepresentantDTO->getStrStaEstado())) {
            $objInfraException->adicionarValidacao('StaEstado não informad.');
        } else {
            if (!in_array($objMdPetVincRepresentantDTO->getStrStaEstado(), InfraArray::converterArrInfraDTO($this->listarValoresEstado(), 'StaEstado'))) {
                $objInfraException->adicionarValidacao(' inválid.');
            }
        }
    }

    protected function cadastrarControlado(MdPetVincRepresentantDTO $objMdPetVincRepresentantDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_vinc_representant_cadastrar');

            //Regras de Negocio
            $objInfraException = new InfraException();

            $this->validarNumIdMdPetVinculo($objMdPetVincRepresentantDTO, $objInfraException);
            $this->validarNumIdContato($objMdPetVincRepresentantDTO, $objInfraException);
            $this->validarNumIdContatoOutorg($objMdPetVincRepresentantDTO, $objInfraException);
            $this->validarStrTipoRepresentante($objMdPetVincRepresentantDTO, $objInfraException);
            $this->validarDthDataCadastro($objMdPetVincRepresentantDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdPetVincRepresentantBD = new MdPetVincRepresentantBD($this->getObjInfraIBanco());
            $ret = $objMdPetVincRepresentantBD->cadastrar($objMdPetVincRepresentantDTO);

            //Auditoria

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando Representante do Vínculo.', $e);
        }
    }

    protected function alterarControlado(MdPetVincRepresentantDTO $objMdPetVincRepresentantDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_vinc_representant_alterar');

            //Regras de Negocio
            $objInfraException = new InfraException();

            if ($objMdPetVincRepresentantDTO->isSetNumIdMdPetVinculoRepresent()) {
                $this->validarNumIdMdPetVinculoRepresent($objMdPetVincRepresentantDTO, $objInfraException);
            }
            if ($objMdPetVincRepresentantDTO->isSetNumIdMdPetVinculo()) {
                $this->validarNumIdMdPetVinculo($objMdPetVincRepresentantDTO, $objInfraException);
            }
            if ($objMdPetVincRepresentantDTO->isSetNumIdContato()) {
                $this->validarNumIdContato($objMdPetVincRepresentantDTO, $objInfraException);
            }
            if ($objMdPetVincRepresentantDTO->isSetNumIdContatoOutorg()) {
                $this->validarNumIdContatoOutorg($objMdPetVincRepresentantDTO, $objInfraException);
            }
            if ($objMdPetVincRepresentantDTO->isSetStrTipoRepresentante()) {
                $this->validarStrTipoRepresentante($objMdPetVincRepresentantDTO, $objInfraException);
            }
            if ($objMdPetVincRepresentantDTO->isSetDthDataCadastro()) {
                $this->validarDthDataCadastro($objMdPetVincRepresentantDTO, $objInfraException);
            }

            $objInfraException->lancarValidacoes();

            $objMdPetVincRepresentantBD = new MdPetVincRepresentantBD($this->getObjInfraIBanco());
            $objMdPetVincRepresentantBD->alterar($objMdPetVincRepresentantDTO);

            //Auditoria

        } catch (Exception $e) {
            throw new InfraException('Erro alterando Representante do Vínculo.', $e);
        }
    }

    protected function excluirControlado($arrObjMdPetVincRepresentantDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_vinc_representant_excluir');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetVincRepresentantBD = new MdPetVincRepresentantBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdPetVincRepresentantDTO); $i++) {
                $objMdPetVincRepresentantBD->excluir($arrObjMdPetVincRepresentantDTO[$i]);
            }

            //Auditoria

        } catch (Exception $e) {
            throw new InfraException('Erro excluindo Representante do Vínculo.', $e);
        }
    }

    protected function consultarConectado(MdPetVincRepresentantDTO $objMdPetVincRepresentantDTO)
    {
        try {

            //Valida Permissao
//      SessaoSEI::getInstance()->validarPermissao('md_pet_vinc_representant_consultar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();
            $objMdPetVincRepresentantBD = new MdPetVincRepresentantBD($this->getObjInfraIBanco());

            $ret = $objMdPetVincRepresentantBD->consultar($objMdPetVincRepresentantDTO);


            //Auditoria

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando Representante do Vínculo.', $e);
        }
    }

    protected function listarConectado(MdPetVincRepresentantDTO $objMdPetVincRepresentantDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_vinc_usu_ext_pe_listar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetVincRepresentantBD = new MdPetVincRepresentantBD($this->getObjInfraIBanco());
            $ret = $objMdPetVincRepresentantBD->listar($objMdPetVincRepresentantDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro listando Representante do Vínculo.', $e);
        }
    }

    protected function contarConectado(MdPetVincRepresentantDTO $objMdPetVincRepresentantDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_vinc_representant_listar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetVincRepresentantBD = new MdPetVincRepresentantBD($this->getObjInfraIBanco());
            $ret = $objMdPetVincRepresentantBD->contar($objMdPetVincRepresentantDTO);
            $ret = $objMdPetVincRepresentantBD->contar($objMdPetVincRepresentantDTO);

            //Auditoria

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando Representante do Vínculo.', $e);
        }
    }

    protected function desativarControlado($arrObjMdPetVincRepresentantDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_vinc_representant_desativar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetVincRepresentantBD = new MdPetVincRepresentantBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdPetVincRepresentantDTO); $i++) {
                $objMdPetVincRepresentantBD->desativar($arrObjMdPetVincRepresentantDTO[$i]);
            }

            //Auditoria

        } catch (Exception $e) {
            throw new InfraException('Erro desativando Representante do Vínculo.', $e);
        }
    }

    protected function reativarControlado($arrObjMdPetVincRepresentantDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_vinc_representant_reativar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetVincRepresentantBD = new MdPetVincRepresentantBD($this->getObjInfraIBanco());
            for ($i = 0; $i < count($arrObjMdPetVincRepresentantDTO); $i++) {
                $objMdPetVincRepresentantBD->reativar($arrObjMdPetVincRepresentantDTO[$i]);
            }

            //Auditoria

        } catch (Exception $e) {
            throw new InfraException('Erro reativando Representante do Vínculo.', $e);
        }
    }

    protected function bloquearControlado(MdPetVincRepresentantDTO $objMdPetVincRepresentantDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_vinc_representant_consultar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetVincRepresentantBD = new MdPetVincRepresentantBD($this->getObjInfraIBanco());
            $ret = $objMdPetVincRepresentantBD->bloquear($objMdPetVincRepresentantDTO);

            //Auditoria

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro bloqueando Representante do Vínculo.', $e);
        }
    }

    protected function realizarProcessosAlteracaoResponsavelLegalControlado($dados)
    {

        $objMdPetVinculoUsuExtRN = new MdPetVinculoUsuExtRN();
        $responsavelLegal = $objMdPetVinculoUsuExtRN->verificaMudancaResponsavelLegal($dados);

        $dados['isAlteradoRespLegal'] = false;

        if ($responsavelLegal instanceof MdPetVincRepresentantDTO) {

            $idVinculo = isset($_GET['idVinculo']) ? $_GET['idVinculo'] : $_POST['hdnIdVinculo'];
            $numeroSEI = isset($_GET['numeroSEI']) ? $_GET['numeroSEI'] : $_POST['hdnNumeroSei'];
            $this->_realizarProcessoEncerramentoVinculo(array($idVinculo, $numeroSEI));
            $dados['isAlteradoRespLegal'] = true;
            $dados['NomeProcurador'] = $responsavelLegal->getStrNomeProcurador();
            $dados['CpfProcurador'] = $responsavelLegal->getStrCpfProcurador();
        }

        $reciboGerado = $objMdPetVinculoUsuExtRN->gerarProcedimentoVinculo($dados);
        $idRecibo = $reciboGerado ? $reciboGerado->getNumIdReciboPeticionamento() : '';
        return $idRecibo;
    }

    private function _realizarProcessoEncerramentoVinculo($params)
    {
        $idVinculo = isset($params[0]) ? $params[0] : null;
        $numeroSEI = isset($params[1]) ? $params[1] : null;

        $objMdPetVinculoUsuExtRN = new MdPetVinculoUsuExtRN();

        $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
        $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
        $objMdPetVincRepresentantDTO->retTodos();
        $objMdPetVincRepresentantDTO->setNumIdMdPetVinculo($idVinculo);
        $objMdPetVincRepresentantDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL);
        $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
        $objMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->consultar($objMdPetVincRepresentantDTO);

        $idRepresentante = $objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent();

        if (is_numeric($numeroSEI)) {
            // Justificativa é um documento
            $objDocumentoRN = new DocumentoRN();
            $objDocumentoDTO = new DocumentoDTO();
            $numeroSEIFormt = '%' . trim($numeroSEI);
            $objDocumentoDTO->setStrProtocoloDocumentoFormatado($numeroSEIFormt, InfraDTO::$OPER_LIKE);
            $objDocumentoDTO->retDblIdDocumento();
            $objDocumentoDTO->setNumMaxRegistrosRetorno('1');

            $arrObjDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

            $tpProtocolo = MdPetVincDocumentoRN::$TP_PROTOCOLO_RECIBO;
            $objMdPetVinculoUsuExtRN->_adicionarDadosArquivoVinculacao($arrObjDocumentoDTO->getDblIdDocumento(), $idRepresentante, MdPetVincDocumentoRN::$TP_PROTOCOLO_RECIBO);
        }

        $objMdPetVincRepresentantDTO->setStrMotivo($_POST['txtMotivo']);
        $objMdPetVincRepresentantDTO->setStrStaEstado(self::$RP_SUBSTITUIDA); // Marcar como Procuração como Substituída
        $objMdPetVincRepresentantRN->alterar($objMdPetVincRepresentantDTO);

        // retornando representante anterior
        return $idRepresentante;
    }

    public function realizarProcessoSuspensaoRestabelecimentoVinculoControlado($params)
    {

        try {

            $dados      = isset($params['dados']) ? $params['dados'] : null;
            $idVinculo  = isset($dados['hdnIdVinculo']) ? $dados['hdnIdVinculo'] : null;
            $numeroSEI  = isset($dados['hdnNumeroSei']) ? $dados['hdnNumeroSei'] : null;
            $operacao   = isset($dados['hdnOperacao']) ? $dados['hdnOperacao'] : null;
            $arrListaProcuradores = [];
            $arrIdRepresentantes = null;
            $situacao = null;

            $objMdPetVinUsuExtProcRN        = new MdPetVinUsuExtProcRN();
            $objMdPetVincRepresentantRN     = new MdPetVincRepresentantRN();
            $objMdPetVincRepresentantDTO    = new MdPetVincRepresentantDTO();

            if ($operacao == MdPetVincRepresentantRN::$RP_ATIVO) {
                $arrIdRepresentantes = $this->getIdRepresentantesVinculo(array($idVinculo, true, false));
                $situacao = MdPetVincRepresentantRN::$RP_SUSPENSO;
            } else if ($operacao == MdPetVincRepresentantRN::$RP_SUSPENSO) {
                $arrIdRepresentantes = $this->getIdRepresentantesVinculo(array($idVinculo, true, true));
                $situacao = MdPetVincRepresentantRN::$RP_ATIVO;
            }

            if (is_array($arrIdRepresentantes) && !empty($situacao)) {

                $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
                $objMdPetVincRepresentantDTO->retStrTipoRepresentante();
                $objMdPetVincRepresentantDTO->retNumIdContato();
                $objMdPetVincRepresentantDTO->retDthDataCadastro();
                $objMdPetVincRepresentantDTO->retStrCpfProcurador();
                $objMdPetVincRepresentantDTO->retDblIdDocumento();
                $objMdPetVincRepresentantDTO->setNumIdMdPetVinculo($idVinculo);
                $objMdPetVincRepresentantDTO->setNumIdMdPetVinculoRepresent($arrIdRepresentantes, InfraDTO::$OPER_IN);
                $objMdPetVincRepresentantDTO->setStrStaEstado($situacao);

                $arrObjMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);

                $bolNaoReestabelecer = false;

                foreach ($arrObjMdPetVincRepresentantDTO as $objMdPetVincRepresentantDTO) {
                    $objUsuarioDTO = new UsuarioDTO();
                    $objUsuarioDTO->setNumIdContato($objMdPetVincRepresentantDTO->getNumIdContato());
                    $objUsuarioDTO->retStrSinAtivo();
                    $objUsuarioDTO->retStrNome();
                    $objUsuarioDTO->setBolExclusaoLogica(false);
                    $objUsuarioDTO = (new UsuarioRN)->consultarRN0489($objUsuarioDTO);
                    if ($objUsuarioDTO) {
                        if ($objUsuarioDTO->getStrSinAtivo() == 'N') {
                            $bolNaoReestabelecer = true;
                            $strMensagemNaoReestabelecer .= '    - ' . $objUsuarioDTO->getStrNome() . ' (' . InfraUtil::formatarCpf($objMdPetVincRepresentantDTO->getStrCpfProcurador()) . ') \n';
                        }
                    }
                }

                if ($bolNaoReestabelecer) {
                    $strMensagemNaoReestabelecer = 'Não é possível Reestabelecer desta Pessoa Jurídica, tendo em vista que os Usuários Externos abaixo estão desativados: \n \n';
                    $strMensagemNaoReestabelecer .= '\n Caso necessário, primeiramente regularize os cadastros dos Usuários Externos acima.';
                    $objInfraException = new InfraException();
                    $objInfraException->adicionarValidacao($strMensagemNaoReestabelecer);
                    $objInfraException->lancarValidacoes();
                }

                foreach ($arrObjMdPetVincRepresentantDTO as $objMdPetVincRepresentantDTO) {

                    if ($objMdPetVincRepresentantDTO->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL) {

                        $idMdPetVinculoRepresentLegal = $objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent();
                        $representanteDTO = new MdPetVincRepresentantDTO();
                        $representanteDTO->setNumIdMdPetVinculoRepresent($objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent());
                        $representanteDTO->setStrStaEstado($operacao);
                        $objMdPetVincRepresentantRN->alterar($representanteDTO);

                    }

                }

                $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
                $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
                $objMdPetVincRepresentantDTO->retStrTipoRepresentante();
                $objMdPetVincRepresentantDTO->retNumIdContato();
                $objMdPetVincRepresentantDTO->retDthDataCadastro();
                $objMdPetVincRepresentantDTO->retDblIdDocumento();
                $objMdPetVincRepresentantDTO->setNumIdMdPetVinculo($idVinculo);
                $objMdPetVincRepresentantDTO->retNumIdContatoOutorg();
                $objMdPetVincRepresentantDTO->setNumIdMdPetVinculoRepresent($arrIdRepresentantes, InfraDTO::$OPER_IN);
                $objMdPetVincRepresentantDTO->setStrStaEstado($situacao);

                $arrObjMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);

                foreach ($arrObjMdPetVincRepresentantDTO as $objMdPetVincRepresentantDTO) {

                    // Usuário do vínculo
                    $objUsuarioDTO = new UsuarioDTO();
                    $objUsuarioDTO->setNumIdContato($objMdPetVincRepresentantDTO->getNumIdContato());
                    $objUsuarioDTO->retStrSinAtivo();
                    $objUsuarioDTO->retStrNome();
                    $objUsuarioDTO->setBolExclusaoLogica(false);
                    $objUsuarioDTO = (new UsuarioRN)->consultarRN0489($objUsuarioDTO);

                    // Documento do Vinculo
                    $objMdPetVincDocumentoDTO = new MdPetVincDocumentoDTO();
                    $objMdPetVincDocumentoDTO->retStrProtocoloFormatadoProtocolo();
                    $objMdPetVincDocumentoDTO->setNumIdMdPetVinculoRepresent($objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent());
                    $objMdPetVincDocumentoDTO->setStrTipoDocumento([MdPetVincDocumentoRN::$TP_PROTOCOLO_PROCURACAO, MdPetVincDocumentoRN::$TP_PROTOCOLO_PROCURACAO_ESPECIAL], InfraDTO::$OPER_IN);
                    $objMdPetVincDocumentoDTO->setOrdNumIdMdPetVincDocumento(InfraDTO::$TIPO_ORDENACAO_DESC);
                    $arrObjMdPetVincDocumentoDTO = (new MdPetVincDocumentoRN)->consultar($objMdPetVincDocumentoDTO);

                    if(!empty($objUsuarioDTO) && !empty($arrObjMdPetVincDocumentoDTO)){
                        $procurador = $objUsuarioDTO->getStrNome() . ' nº ' . $arrObjMdPetVincDocumentoDTO->getStrProtocoloFormatadoProtocolo();
                        if(!in_array($procurador, $arrListaProcuradores)){
                            array_push($arrListaProcuradores, $procurador);
                        }
                    }

                    $representanteDTO = new MdPetVincRepresentantDTO();
                    $representanteDTO->setNumIdMdPetVinculoRepresent($objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent());
                    $representanteDTO->setStrStaEstado($operacao);
                    $objMdPetVincRepresentantRN->alterar($representanteDTO);

                }

                $objMdPetVinculoUsuExtRN = new MdPetVinculoUsuExtRN();
                $objProcedimentoDTO = $objMdPetVinculoUsuExtRN->_getObjProcedimentoPorVinculo($idVinculo);

                sort($arrListaProcuradores);

                $params = ['dados' => $dados, 'procedimento' => $objProcedimentoDTO, 'arrListaProcuradores' => $arrListaProcuradores];

                //gerar dcoumentos
                if ($operacao == MdPetVincRepresentantRN::$RP_SUSPENSO) {
                    $objSaidaIncluirDocumentoAPI = $objMdPetVinUsuExtProcRN->gerarDocumentoSuspensao($params);
                    $staTipoDocumento = MdPetVincDocumentoRN::$TP_PROTOCOLO_SUSPENSAO;
                    $staDiligenciaTipoDocumento = MdPetVincDocumentoRN::$TP_PROTOCOLO_DILIGENCIA_SUSPENSAO;
                } else if ($operacao == MdPetVincRepresentantRN::$RP_ATIVO) {
                    $objSaidaIncluirDocumentoAPI = $objMdPetVinUsuExtProcRN->gerarDocumentoRestabelecimento($params);
                    $staTipoDocumento = MdPetVincDocumentoRN::$TP_PROTOCOLO_RESTABELECIMENTO;
                    $staDiligenciaTipoDocumento = MdPetVincDocumentoRN::$TP_PROTOCOLO_DILIGENCIA_RESTABELECIMENTO;
                }

                if (is_numeric($objSaidaIncluirDocumentoAPI->getIdDocumento())) {
                    $objMdPetVinUsuExtProcRN = new MdPetVinUsuExtProcRN();
                    $objMdPetVinUsuExtProcRN->_adicionarDadosArquivoVinculacao($objSaidaIncluirDocumentoAPI->getIdDocumento(), $idMdPetVinculoRepresentLegal, $staTipoDocumento);
                    $params['dados']['numeroSeiVinculacao'] = $objSaidaIncluirDocumentoAPI->getIdDocumento();
                }

                if (is_numeric($numeroSEI)) {

                    // Justificativa é um documento
                    $objDocumentoDTO = new DocumentoDTO();
                    $numeroSEIFormt = '%' . trim($numeroSEI);
                    $objDocumentoDTO->setStrProtocoloDocumentoFormatado($numeroSEIFormt, InfraDTO::$OPER_LIKE);
                    $objDocumentoDTO->retDblIdDocumento();
                    $objDocumentoDTO->setNumMaxRegistrosRetorno('1');
                    $arrObjDocumentoDTO = (new DocumentoRN())->consultarRN0005($objDocumentoDTO);

                    $objMdPetVinUsuExtProcRN = new MdPetVinUsuExtProcRN();
                    $objMdPetVinUsuExtProcRN->_adicionarDadosArquivoVinculacao($arrObjDocumentoDTO->getDblIdDocumento(), $idMdPetVinculoRepresentLegal, $staDiligenciaTipoDocumento);

                }

                $mdPetVinculoUsuExtRN = new MdPetVinculoUsuExtRN();
                $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
                $objUnidadeDTO = $mdPetVinculoUsuExtRN->getUnidade();

                if ($objUnidadeDTO->getStrSinAtivo() == 'S') {
                    $arrUnidadeProcesso = $objMdPetIntimacaoRN->verificarUnidadeAberta(array($objProcedimentoDTO, $objUnidadeDTO->getNumIdUnidade()));
                    if (count($arrUnidadeProcesso) == 0) {
                        $idUnidadeAberta = $objMdPetIntimacaoRN->reabrirUnidade(array($objProcedimentoDTO, $objUnidadeDTO->getNumIdUnidade()));
                        if (is_numeric($idUnidadeAberta)) {
                            $arrUnidadeProcesso = $objMdPetIntimacaoRN->verificarUnidadeAberta(array($objProcedimentoDTO, $idUnidadeAberta));
                        }
                    }
                }

                // 1) ANEXADO, vai pegar do ANEXADOR/PRINCIPAL
                if ($objProcedimentoDTO->getStrStaEstadoProtocolo() == ProtocoloRN::$TE_PROCEDIMENTO_ANEXADO) {

                    $objRelProtocoloProtocoloDTO = new RelProtocoloProtocoloDTO();
                    $objRelProtocoloProtocoloDTO->retDblIdProtocolo1();
                    $objRelProtocoloProtocoloDTO->retStrProtocoloFormatadoProtocolo1();
                    $objRelProtocoloProtocoloDTO->setDblIdProtocolo2($objProcedimentoDTO->getDblIdProcedimento());
                    $objRelProtocoloProtocoloDTO->setStrStaAssociacao(RelProtocoloProtocoloRN::$TA_PROCEDIMENTO_ANEXADO);

                    $objRelProtocoloProtocoloRN = new RelProtocoloProtocoloRN();
                    $objRelProtocoloProtocoloDTO = $objRelProtocoloProtocoloRN->consultarRN0841($objRelProtocoloProtocoloDTO);

                    if (is_array($objRelProtocoloProtocoloDTO) && count($objRelProtocoloProtocoloDTO) == 1) {
                        $arrUnidadeProcesso = $this->retornaUltimaUnidadeProcessoAberto(array($objRelProtocoloProtocoloDTO->getDblIdProtocolo1()));
                    }

               // 2) Última aberta
                } else if (count($arrUnidadeProcesso) == 0) {
                    $arrUnidadeProcesso = $this->retornaUltimaUnidadeProcessoAberto(array($this->getProcedimentoDTO()->getDblIdProcedimento()));
                }

                $idUnidadeProcesso = null;
                $idUsuarioAtribuicao = null;

                if (is_array($arrUnidadeProcesso) && count($arrUnidadeProcesso) > 0) {
                    if (is_numeric($arrUnidadeProcesso[0])) {
                        $idUnidadeProcesso = $arrUnidadeProcesso[0];
                        if (is_numeric($arrUnidadeProcesso[1])) {
                            $idUsuarioAtribuicao = $arrUnidadeProcesso[1];
                        }
                    } else {
                        $idUnidadeProcesso = $arrUnidadeProcesso[0]->getNumIdUnidade();
                        if ($arrUnidadeProcesso[0]->isSetNumIdUsuarioAtribuicao()) {
                            $idUsuarioAtribuicao = $arrUnidadeProcesso[0]->getNumIdUsuarioAtribuicao();
                        }
                    }
                }

                if (!is_numeric($idUnidadeProcesso)) {
                    $mdPetAndamentoSigilosoRN = new MdPetIntercorrenteAndamentoSigilosoRN();
                    $idUnidadeProcesso = $mdPetAndamentoSigilosoRN->retornaIdUnidadeAberturaProcesso($this->getProcedimentoDTO()->getDblIdProcedimento());
                }

                $arrObjAtributoAndamentoDTO = array();
                $objAtributoAndamentoDTO = new AtributoAndamentoDTO();
                $objAtributoAndamentoDTO->setStrNome('UNIDADE');
                $objAtributoAndamentoDTO->setStrValor($objUnidadeDTO->getStrSigla() . ' ¥ ' . $objUnidadeDTO->getStrDescricao());
                $objAtributoAndamentoDTO->setStrIdOrigem($objUnidadeDTO->getNumIdUnidade());
                $arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;

                $objAtividadeDTO = new AtividadeDTO();
                $objAtividadeDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());
                $objAtividadeDTO->setNumIdUnidade($objUnidadeDTO->getNumIdUnidade());
                $objAtividadeDTO->setNumIdUnidadeOrigem($objUnidadeDTO->getNumIdUnidade());
                if (!empty($idUsuarioAtribuicao)) {
                    $objAtividadeDTO->setNumIdUsuarioAtribuicao($idUsuarioAtribuicao);
                }
                $objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO);
                $objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_PROCESSO_REMETIDO_UNIDADE);

                // Email
                $objMdPetIntEmailNotificacaoRN = new MdPetIntEmailNotificacaoRN();
                if ($operacao == MdPetVincRepresentantRN::$RP_SUSPENSO) {
                    $objMdPetIntEmailNotificacaoRN->enviarEmailVincSuspensao($params);
                } else if ($operacao == MdPetVincRepresentantRN::$RP_ATIVO) {
                    $objMdPetIntEmailNotificacaoRN->enviarEmailVincRestabelecimento($params);
                }

                $objAtividadeRN = new AtividadeRN();
                $objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);
            }

        } catch (Exception $e) {
            PaginaSEI::getInstance()->processarExcecao($e, true);
        }
    }

    public function realizarProcessoSuspensaoRestabelecimentoProcuradorControlado($params)
    {

	    try {

            $dados = isset($params['dados']) ? $params['dados'] : null;
            $idVinculo = isset($dados['hdnIdVinculo']) ? $dados['hdnIdVinculo'] : null;
            $numeroSEI = isset($dados['hdnNumeroSei']) ? $dados['hdnNumeroSei'] : null;
            $operacao = isset($dados['hdnOperacao']) ? $dados['hdnOperacao'] : null;

            $idContatoProc = isset($dados['hdnIdContatoProc']) ? $dados['hdnIdContatoProc'] : null;
            $idVinculoRepresent = isset($dados['hdnIdVinculoRepresent']) ? $dados['hdnIdVinculoRepresent'] : null;
		    $idDocumentoRepresent = isset($dados['hdnIdDocumentoRepresent']) ? $dados['hdnIdDocumentoRepresent'] : null;

		    // Pega o numero do Protocolo Formatado.
		    $objProtocoloDTO = new ProtocoloDTO();
		    $objProtocoloDTO->retStrProtocoloFormatado();
		    $objProtocoloDTO->setDblIdProtocolo($idDocumentoRepresent);
		    $objProtocolo = (new ProtocoloRN())->consultarRN0186($objProtocoloDTO);

		    $dados['procuracao_doc_num'] = $objProtocolo->getStrProtocoloFormatado();

            $objMdPetVinUsuExtProcRN = new MdPetVinUsuExtProcRN();
            $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();

            if ($operacao == MdPetVincRepresentantRN::$RP_ATIVO) {
                $situacao = MdPetVincRepresentantRN::$RP_SUSPENSO;
            } else if ($operacao == MdPetVincRepresentantRN::$RP_SUSPENSO) {
                $situacao = MdPetVincRepresentantRN::$RP_ATIVO;
            }

            // RETORNANDO DADOS DO VINCULO DO PROCURADOR
            $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
            $objMdPetVincRepresentantDTO->retTodos();
            $objMdPetVincRepresentantDTO->retNumIdMdPetVinculo();
            $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
            $objMdPetVincRepresentantDTO->retStrTipoRepresentante();
            $objMdPetVincRepresentantDTO->retStrStaEstado();
            $objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
            $objMdPetVincRepresentantDTO->retStrCNPJ();
            $objMdPetVincRepresentantDTO->retStrCPF();
            $objMdPetVincRepresentantDTO->retStrTpVinc();
            $objMdPetVincRepresentantDTO->retNumIdContatoVinc();
            $objMdPetVincRepresentantDTO->retStrNomeProcurador();
            $objMdPetVincRepresentantDTO->retNumIdContatoProcurador();
            $objMdPetVincRepresentantDTO->retStrCpfProcurador();
            $objMdPetVincRepresentantDTO->retDblIdProcedimentoVinculo();
            $objMdPetVincRepresentantDTO->setStrStaEstado($situacao);
            $objMdPetVincRepresentantDTO->setNumIdMdPetVinculoRepresent($idVinculoRepresent);
            $objMdPetVincRepresentantDTO = (new MdPetVincRepresentantRN())->consultar($objMdPetVincRepresentantDTO);

            $dados['procuracao_tipo'] = $objMdPetVincRepresentantDTO->getStrNomeTipoVinculacao();

            $strMensagemNaoReestabelecer = 'Não é possível Reestabelecer o Procurador, tendo em vista que os Usuários Externos abaixo estão desativados: \n \n';
            $bolNaoReestabelecer = false;

            // BUSCA OS DADOS DO USUÁRIO EXTERNO DO PROCURADOR:
            $objUsuarioDTO = new UsuarioDTO();
            $objUsuarioDTO->setNumIdContato($idContatoProc);
            $objUsuarioDTO->retStrSinAtivo();
            $objUsuarioDTO->retStrNome();
            $objUsuarioDTO->setBolExclusaoLogica(false);
            $objUsuarioDTO = (new UsuarioRN)->consultarRN0489($objUsuarioDTO);

            if ($objUsuarioDTO && $objUsuarioDTO->getStrSinAtivo() == 'N') {
                $bolNaoReestabelecer = true;
                $strMensagemNaoReestabelecer .= '    - ' . $objUsuarioDTO->getStrNome() . ' (' . InfraUtil::formatarCpf($objMdPetVincRepresentantDTO->getStrCpfProcurador()) . ') \n';
            }

            $strMensagemNaoReestabelecer .= '\n Caso necessário, primeiramente regularize os cadastros dos Usuários Externos acima.';

            if ($bolNaoReestabelecer) {
                $objInfraException = new InfraException();
                $objInfraException->adicionarValidacao($strMensagemNaoReestabelecer);
                $objInfraException->lancarValidacoes();
            }

            $representanteDTO = new MdPetVincRepresentantDTO();
            $representanteDTO->setNumIdMdPetVinculoRepresent($objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent());
            $representanteDTO->setStrStaEstado($operacao);
            $objMdPetVincRepresentantRN->alterar($representanteDTO);

            // TRATANDO OS DOCUMENTOS
            $objMdPetVinculoUsuExtRN = new MdPetVinculoUsuExtRN();
            $objProcedimentoDTO = $objMdPetVinculoUsuExtRN->_getObjProcedimentoPorVinculo($idVinculo);

            $params = ['dados' => $dados, 'procedimento' => $objProcedimentoDTO];

            // GERAR O DOCUMENTO
            if ($operacao == MdPetVincRepresentantRN::$RP_ATIVO) {
                $objSaidaIncluirDocumentoAPI = $objMdPetVinUsuExtProcRN->gerarDocumentoRestabelecimento($params);
                $staTipoDocumento = MdPetVincDocumentoRN::$TP_PROTOCOLO_RESTABELECIMENTO;
                $staDiligenciaTipoDocumento = MdPetVincDocumentoRN::$TP_PROTOCOLO_DILIGENCIA_RESTABELECIMENTO;
            } else if ($operacao == MdPetVincRepresentantRN::$RP_SUSPENSO) {
                $objSaidaIncluirDocumentoAPI = $objMdPetVinUsuExtProcRN->gerarDocumentoSuspensao($params);
                $staTipoDocumento = MdPetVincDocumentoRN::$TP_PROTOCOLO_SUSPENSAO;
                $staDiligenciaTipoDocumento = MdPetVincDocumentoRN::$TP_PROTOCOLO_DILIGENCIA_SUSPENSAO;
            }

            if (is_numeric($objSaidaIncluirDocumentoAPI->getIdDocumento())) {
                $objMdPetVinUsuExtProcRN = new MdPetVinUsuExtProcRN();
                $objMdPetVinUsuExtProcRN->_adicionarDadosArquivoVinculacao($objSaidaIncluirDocumentoAPI->getIdDocumento(), $objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent(), $staTipoDocumento);
                $params['dados']['numeroSeiVinculacao'] = $objSaidaIncluirDocumentoAPI->getIdDocumento();
            }

            if (is_numeric($numeroSEI)) {

                // JUSTIFICATIVA É UM DOCUMENTO
                $objDocumentoDTO = new DocumentoDTO();
                $numeroSEIFormt = '%' . trim($numeroSEI);
                $objDocumentoDTO->setStrProtocoloDocumentoFormatado($numeroSEIFormt, InfraDTO::$OPER_LIKE);
                $objDocumentoDTO->retDblIdDocumento();
                $objDocumentoDTO->setNumMaxRegistrosRetorno('1');
                $arrObjDocumentoDTO = (new DocumentoRN())->consultarRN0005($objDocumentoDTO);

                $objMdPetVinUsuExtProcRN = new MdPetVinUsuExtProcRN();
                $objMdPetVinUsuExtProcRN->_adicionarDadosArquivoVinculacao($arrObjDocumentoDTO->getDblIdDocumento(), $objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent(), $staDiligenciaTipoDocumento);

            }

            $mdPetVinculoUsuExtRN = new MdPetVinculoUsuExtRN();
            $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
            $objUnidadeDTO = $mdPetVinculoUsuExtRN->getUnidade();

            if ($objUnidadeDTO->getStrSinAtivo() == 'S') {
                $arrUnidadeProcesso = $objMdPetIntimacaoRN->verificarUnidadeAberta(array($objProcedimentoDTO, $objUnidadeDTO->getNumIdUnidade()));
                if (count($arrUnidadeProcesso) == 0) {
                    $idUnidadeAberta = $objMdPetIntimacaoRN->reabrirUnidade(array($objProcedimentoDTO, $objUnidadeDTO->getNumIdUnidade()));
                    if (is_numeric($idUnidadeAberta)) {
                        $arrUnidadeProcesso = $objMdPetIntimacaoRN->verificarUnidadeAberta(array($objProcedimentoDTO, $idUnidadeAberta));
                    }
                }
            }

            // 1) ANEXADO, VAI PEGAR DO ANEXADOR/PRINCIPAL
            if ($objProcedimentoDTO->getStrStaEstadoProtocolo() == ProtocoloRN::$TE_PROCEDIMENTO_ANEXADO) {

                $objRelProtocoloProtocoloDTO = new RelProtocoloProtocoloDTO();
                $objRelProtocoloProtocoloDTO->retDblIdProtocolo1();
                $objRelProtocoloProtocoloDTO->retStrProtocoloFormatadoProtocolo1();
                $objRelProtocoloProtocoloDTO->setDblIdProtocolo2($objProcedimentoDTO->getDblIdProcedimento());
                $objRelProtocoloProtocoloDTO->setStrStaAssociacao(RelProtocoloProtocoloRN::$TA_PROCEDIMENTO_ANEXADO);

                $objRelProtocoloProtocoloRN = new RelProtocoloProtocoloRN();
                $objRelProtocoloProtocoloDTO = $objRelProtocoloProtocoloRN->consultarRN0841($objRelProtocoloProtocoloDTO);

                if (is_array($objRelProtocoloProtocoloDTO) && count($objRelProtocoloProtocoloDTO) == 1) {
                    $arrUnidadeProcesso = $this->retornaUltimaUnidadeProcessoAberto(array($objRelProtocoloProtocoloDTO->getDblIdProtocolo1()));
                }

                // 2) ÚLTIMA ABERTA
            } else if (count($arrUnidadeProcesso) == 0) {
                $arrUnidadeProcesso = $this->retornaUltimaUnidadeProcessoAberto(array($this->getProcedimentoDTO()->getDblIdProcedimento()));
            }

            $idUnidadeProcesso = $idUsuarioAtribuicao = null;
            if (is_array($arrUnidadeProcesso) && count($arrUnidadeProcesso) > 0) {
                if (is_numeric($arrUnidadeProcesso[0])) {
                    $idUnidadeProcesso = $arrUnidadeProcesso[0];
                    if (is_numeric($arrUnidadeProcesso[1])) {
                        $idUsuarioAtribuicao = $arrUnidadeProcesso[1];
                    }
                } else {
                    $idUnidadeProcesso = $arrUnidadeProcesso[0]->getNumIdUnidade();
                    if ($arrUnidadeProcesso[0]->isSetNumIdUsuarioAtribuicao()) {
                        $idUsuarioAtribuicao = $arrUnidadeProcesso[0]->getNumIdUsuarioAtribuicao();
                    }
                }
            }

            if (!is_numeric($idUnidadeProcesso)) {
                $mdPetAndamentoSigilosoRN = new MdPetIntercorrenteAndamentoSigilosoRN();
                $idUnidadeProcesso = $mdPetAndamentoSigilosoRN->retornaIdUnidadeAberturaProcesso($this->getProcedimentoDTO()->getDblIdProcedimento());
            }

            $arrObjAtributoAndamentoDTO = array();
            $objAtributoAndamentoDTO = new AtributoAndamentoDTO();
            $objAtributoAndamentoDTO->setStrNome('UNIDADE');
            $objAtributoAndamentoDTO->setStrValor($objUnidadeDTO->getStrSigla() . ' ¥ ' . $objUnidadeDTO->getStrDescricao());
            $objAtributoAndamentoDTO->setStrIdOrigem($objUnidadeDTO->getNumIdUnidade());
            $arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;

            $objAtividadeDTO = new AtividadeDTO();
            $objAtividadeDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());
            $objAtividadeDTO->setNumIdUnidade($objUnidadeDTO->getNumIdUnidade());
            $objAtividadeDTO->setNumIdUnidadeOrigem($objUnidadeDTO->getNumIdUnidade());
            if (!empty($idUsuarioAtribuicao)) {
                $objAtividadeDTO->setNumIdUsuarioAtribuicao($idUsuarioAtribuicao);
            }
            $objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO);
            $objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_PROCESSO_REMETIDO_UNIDADE);

            // Email apenas se não estiver em localhost pra evitar estouro de tela
            if ($dados['hdnStrTipoVinculo'] == MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL){
                if($operacao == MdPetVincRepresentantRN::$RP_SUSPENSO) {
                    (new MdPetIntEmailNotificacaoRN())->enviarEmailVincSuspensao($params);
                }else{
                    (new MdPetIntEmailNotificacaoRN())->enviarEmailVincRestabelecimento($params);
                }
            }else{
                (new MdPetIntEmailNotificacaoRN())->enviarEmailProcuracaoSuspRest($params);
            }

            $objAtividadeRN = new AtividadeRN();
            $objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);

        } catch (Exception $e) {
            PaginaSEI::getInstance()->processarExcecao($e, true);
        }

    }

    public function getIdRepresentantesVinculoConectado($arrParam)
    {

        $idVinculo  = array_key_exists(0, $arrParam) ? $arrParam[0] : null;
        $estadoBol  = array_key_exists(2, $arrParam) ? $arrParam[2] : true; //true = Ativo false = Suspenso
	    $estado     = $estadoBol == false ? MdPetVincRepresentantRN::$RP_SUSPENSO : MdPetVincRepresentantRN::$RP_ATIVO;

        $arrIdRepresentantes = null;
        $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
        $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
        $objMdPetVincRepresentantDTO->setNumIdMdPetVinculo($idVinculo);
        $objMdPetVincRepresentantDTO->setStrTipoRepresentante([MdPetVincRepresentantRN::$PE_AUTORREPRESENTACAO], InfraDTO::$OPER_NOT_IN);
        $objMdPetVincRepresentantDTO->setStrStaEstado($estado);
        $arrObj = $this->listar($objMdPetVincRepresentantDTO);

        if (!is_null($arrObj) && count($arrObj) > 0) {
	        $arrIdRepresentantes = InfraArray::converterArrInfraDTO($arrObj, 'IdMdPetVinculoRepresent');
        }

        return $arrIdRepresentantes;

    }

    protected function getIdContatoTodosRepresentantesVinculoConectado($arrParam)
    {
        $idVinculo = array_key_exists(0, $arrParam) ? $arrParam[0] : null;

        $idsContatos = null;
        $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
        $objMdPetVincRepresentantDTO->retNumIdContato();
        $objMdPetVincRepresentantDTO->setNumIdMdPetVinculo($idVinculo);
	    $objMdPetVincRepresentantDTO->setBolExclusaoLogica(false);

        $count = $this->contar($objMdPetVincRepresentantDTO);

        if ($count > 0) {
            $arrObj = $this->listar($objMdPetVincRepresentantDTO);
            $idsContatos = InfraArray::converterArrInfraDTO($arrObj, 'IdContato');
        }

        return $idsContatos;
    }


    public function getResponsavelLegalConectado($arrParam)
    {

        $idVinculo = array_key_exists('idVinculo', $arrParam) ? $arrParam['idVinculo'] : null;

        $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
        $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();

        $objMdPetVincRepresentantDTO->retTodos(true);
        $objMdPetVincRepresentantDTO->setNumIdMdPetVinculo($idVinculo);
        $objMdPetVincRepresentantDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL);
        $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
        $objMdPetVincRepresentantDTO->setOrd('IdMdPetVinculoRepresent', InfraDTO::$TIPO_ORDENACAO_DESC);
        $objMdPetVincRepresentantDTO->setNumMaxRegistrosRetorno(1);

        $objMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->consultar($objMdPetVincRepresentantDTO);

        return $objMdPetVincRepresentantDTO;

    }

    /**
     * @param int $idContatoDestinatario
     * @param int $idDocumento
     * @param int $idContatoRepresentante (optional)
     * return mixed
     */
    public function retornarProcuradoresComPoderCumprirResponder($idContatoDestinatario, $idDocumento, $idContatoRepresentante = null)
    {

        $relProtocoloProtocolo = new RelProtocoloProtocoloRN();
        $objRelProtocoloProtocolo = new RelProtocoloProtocoloDTO();
        $objRelProtocoloProtocolo->setDblIdProtocolo2($idDocumento);
        $objRelProtocoloProtocolo->setStrStaAssociacao(RelProtocoloProtocoloRN::$TA_DOCUMENTO_ASSOCIADO);
        $objRelProtocoloProtocolo->retDblIdProtocolo1();
        $objRelProtocoloProtocolo = $relProtocoloProtocolo->consultarRN0841($objRelProtocoloProtocolo);
        $idProcesso = null;
        $arrObjMdPetVincRepresentante = null;
        if($objRelProtocoloProtocolo){
            $idProcesso = $objRelProtocoloProtocolo->getDblIdProtocolo1();
        }

	    $objMdPetVinculo = new MdPetVinculoDTO();
	    $objMdPetVinculo->setNumIdContato($idContatoDestinatario);
	    $objMdPetVinculo->setStrTipoRepresentante([MdPetVincRepresentantRN::$PE_AUTORREPRESENTACAO], InfraDTO::$OPER_NOT_IN);
	    $objMdPetVinculo->retNumIdMdPetVinculo();
	    $arrIdMdPetVinculo = InfraArray::converterArrInfraDTO((new MdPetVinculoRN())->listar($objMdPetVinculo), 'IdMdPetVinculo');

	    if (!empty($arrIdMdPetVinculo)) {
		    $objMdPetVincRepresentante = new MdPetVincRepresentantDTO();
		    $objMdPetVincRepresentante->setNumIdMdPetVinculo($arrIdMdPetVinculo, InfraDTO::$OPER_IN);

            if (!is_null($idContatoRepresentante)) {
                $objMdPetVincRepresentante->setNumIdContato((array) $idContatoRepresentante, InfraDTO::$OPER_IN);
            }

            $objMdPetVincRepresentante->setStrStaEstado($this::$RP_ATIVO);

            $objMdPetVincRepresentante->retTodos();
            $arrObjMdPetVincRepresentante = $this->listar($objMdPetVincRepresentante);

            if ($arrObjMdPetVincRepresentante) {

                foreach ($arrObjMdPetVincRepresentante as $chave => $objVincRepresentante){

                    if($objVincRepresentante->getStrTipoRepresentante() == $this::$PE_PROCURADOR_SIMPLES){
                        $mdPetRelVincRepTpPoderRN = new MdPetRelVincRepTpPoderRN();
                        $objMdPetRelVincRepTpPoder = new MdPetRelVincRepTpPoderDTO();
                        $objMdPetRelVincRepTpPoder->setNumIdVinculoRepresent($objVincRepresentante->getNumIdMdPetVinculoRepresent());
                        $objMdPetRelVincRepTpPoder->setNumIdTipoPoderLegal(1);
                        $objMdPetRelVincRepTpPoder->retTodos();
                        $arrObjMdPetRelVincRepTpPoder = $mdPetRelVincRepTpPoderRN->listar($objMdPetRelVincRepTpPoder);
                        if(!is_array($arrObjMdPetRelVincRepTpPoder)){
                            unset($arrObjMdPetVincRepresentante[$chave]);
                        }
                    }

                    if(!is_null($objVincRepresentante->getDthDataLimite())){
                        $strToTimeHoje = strtotime(date('d-m-Y 23:59:59'));
                        $strToTimeDataLimite = strtotime(str_replace('/', '-', $objVincRepresentante->getDthDataLimite()));
                        if($strToTimeHoje > $strToTimeDataLimite){
                            unset($arrObjMdPetVincRepresentante[$chave]);
                        }
                    }

                    if($objVincRepresentante->getStrStaAbrangencia() == $this::$PR_ESPECIFICO){
                        $mdPetRelVincRepProtocolo = new MdPetRelVincRepProtocRN();
                        $objMdPetRelVincRepProtocolo = new MdPetRelVincRepProtocDTO();
                        $objMdPetRelVincRepProtocolo->setNumIdVincRepresent($objVincRepresentante->getNumIdMdPetVinculoRepresent());
                        $objMdPetRelVincRepProtocolo->setNumIdProtocolo($idProcesso);
                        $objMdPetRelVincRepProtocolo->retTodos();
                        $objMdPetRelVincRepProtocolo = $mdPetRelVincRepProtocolo->consultar($objMdPetRelVincRepProtocolo);
                        if(is_null($objMdPetRelVincRepProtocolo)){
                            unset($arrObjMdPetVincRepresentante[$chave]);
                        }
                    }
                }
            }
        }
        return $arrObjMdPetVincRepresentante;
    }

    public function existeVinculoPorContato($idContato){

        $objContatoDTO = new ContatoDTO();
        $objContatoDTO->setBolExclusaoLogica(false);
        $objContatoDTO->retStrStaNatureza();
        $objContatoDTO->setNumIdContato($idContato);
        $objContatoDTO = (new ContatoRN())->consultarRN0324($objContatoDTO);

        if($objContatoDTO->getStrStaNatureza() == 'F'){

            $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
            $objMdPetVincRepresentantDTO->setStrTipoRepresentante([self::$PE_AUTORREPRESENTACAO], InfraDTO::$OPER_NOT_IN);
	        $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
            $objMdPetVincRepresentantDTO->adicionarCriterio(
                ['IdContato', 'IdContatoOutorg'],
                [InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL],
                [$idContato, $idContato],
                InfraDTO::$OPER_LOGICO_OR
            );
            $objMdPetVincRepresentantDTO->retNumIdContato();
            return (new MdPetVincRepresentantBD(BancoSEI::getInstance()))->contar($objMdPetVincRepresentantDTO) > 0;

        }

        if($objContatoDTO->getStrStaNatureza() == 'J'){

            $objVinculoDTO = new MdPetVinculoDTO();
            $objVinculoDTO->setNumIdContato($idContato);
            $objVinculoDTO->retNumIdMdPetVinculo();
            $arrIdMdPetVinculo = InfraArray::converterArrInfraDTO((new MdPetVinculoBD($this->getObjInfraIBanco()))->listar($objVinculoDTO),'IdMdPetVinculo');

            if(count($arrIdMdPetVinculo) > 0){

                $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
                $objMdPetVincRepresentantDTO->setStrTipoRepresentante([self::$PE_AUTORREPRESENTACAO], InfraDTO::$OPER_NOT_IN);
                $objMdPetVincRepresentantDTO->setNumIdMdPetVinculo((array) $arrIdMdPetVinculo, InfraDTO::$OPER_IN);
	            $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);

                $objMdPetVincRepresentantDTO->retNumIdContato();
                return (new MdPetVincRepresentantBD(BancoSEI::getInstance()))->contar($objMdPetVincRepresentantDTO) > 0;

            }

        }

        return false;

    }

}
