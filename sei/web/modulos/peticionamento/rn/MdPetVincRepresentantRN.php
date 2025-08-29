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
                $strMensagemNaoReestabelecer = 'Não é possível Reestabelecer desta Pessoa Jurídica, tendo em vista que os Usuários Externos abaixo estão desativados: \n \n';
                $usuariosExternosDesativados = [];

                foreach ($arrObjMdPetVincRepresentantDTO as $objMdPetVincRepresentantDTO) {
                    $objUsuarioDTO = new UsuarioDTO();
                    $objUsuarioDTO->setNumIdContato($objMdPetVincRepresentantDTO->getNumIdContato());
                    $objUsuarioDTO->retStrSinAtivo();
                    $objUsuarioDTO->retStrNome();
                    $objUsuarioDTO->setBolExclusaoLogica(false);
                    $objUsuarioDTO = (new UsuarioRN)->consultarRN0489($objUsuarioDTO);
                    if (!empty($objUsuarioDTO) && $objUsuarioDTO->getStrSinAtivo() == 'N') {
                        $bolNaoReestabelecer = true;
                        array_push($usuariosExternosDesativados, '    - ' . $objUsuarioDTO->getStrNome() . ' (' . InfraUtil::formatarCpf($objMdPetVincRepresentantDTO->getStrCpfProcurador()) . ') \n');
                    }
                }

                if ($bolNaoReestabelecer) {

                    $strMensagemNaoReestabelecer .= implode('', array_unique($usuariosExternosDesativados));
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
                        $procurador = $objUsuarioDTO->getStrNome() . ' - '.$objMdPetVincRepresentantDTO->getStrNomeTipoVinculacao().' nº ' . $arrObjMdPetVincDocumentoDTO->getStrProtocoloFormatadoProtocolo();
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

        // PARA SUSPENDER/RESTABELECER SEGUE OS SEQUINTES PASSOS
        // SUSPENDE/RESTABELECE A PROCURAO
        // GERA DOCUMENTO DE SUSPENSAO/RESTABELECIMENTO
        // VINCULAR DOCUMENTO DE JUSTIFICATIVA AO VINCULO
        // ENVIA E-MAIL INFORMANDO A SUSPENSAO/RESTABELECIMENTO
        // INSERIR ANDAMENTOS DO PROCESSO (ATIVIDADES)

        public function suspenderRestabelecerProcuracaoControlado($params)
        {
            // OPERAO PARA SUSPENDER PROCURAES
            if ($params['hdnOperacao'] == MdPetVincRepresentantRN::$RP_SUSPENSO) {
                switch ($_POST['hdnStrTipoVinculo']) {
                    case MdPetVincRepresentantRN::$PE_AUTORREPRESENTACAO:
                        $objAutoRepresentacaoDTO = $this->_buscarRepresentacaoPorId($params['hdnIdVinculoRepresent']);
                        $arrObjMdPetVincRepresentantDTO = $this->listarVincRepresentAtivosPorCPF($objAutoRepresentacaoDTO->getStrCPF());
                        $this->suspenderProcuracaoControlado($arrObjMdPetVincRepresentantDTO, false, $params);
                        break;

                    case MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES:
                    case MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL:
                        $objMdPetVincRepresentantDTO = $this->_buscarRepresentacaoPorId($params['hdnIdVinculoRepresent']);
                        $this->suspenderProcuracaoControlado(array($objMdPetVincRepresentantDTO), false, $params);
                        break;

                    case MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL:
                        $this->_prepararSuspenderResponsavelLegal($params);
                        break;
                }
            }

            // OPERAO PARA RESTABELECER PROCURAES
            if ($params['hdnOperacao'] == MdPetVincRepresentantRN::$RP_ATIVO) {
                switch ($_POST['hdnStrTipoVinculo']) {
                    case MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES:
                    case MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL:
                        $objMdPetVincRepresentantDTO = $this->_buscarRepresentacaoPorId($params['hdnIdVinculoRepresent']);
                        $this->restabelecerProcuracaoControlado(array($objMdPetVincRepresentantDTO), false, $params);
                        break;

                    case MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL:
                        $this->_prepararRestabelecerResponsavelLegal($params);
                        break;
                }
            }
        }

        public function restabelecerProcuracaoPFeAutorepresentacaoControlado($params)
        {
            try {
                $objAutoRepresentacaoDTO = $this->_buscarRepresentacaoPorId($params['hdnIdVinculoRepresent']);
                $arrObjMdPetVincRepresentantDTO = $this->listarVincRepresentInativosPorCPF($objAutoRepresentacaoDTO->getStrCPF());
                $this->restabelecerVinculoPFControlado($arrObjMdPetVincRepresentantDTO, false, $params);
            } catch (Exception $e) {
                PaginaSEI::getInstance()->processarExcecao($e, true);
            }
        }

        private function _prepararSuspenderResponsavelLegal($params, $agendamento = false)
        {
            $objMdPetVincRepresentantDTO = $this->_buscarRepresentacaoPorId($params['hdnIdVinculoRepresent']);

            if ($params['hdnCascata'] == 'S') {
                $arrObjMdPetVincRepresentantDTO = $this->listarVincRepresentAtivosPorCNPJ($objMdPetVincRepresentantDTO->getStrCNPJ());
                $this->suspenderProcuracaoControlado($arrObjMdPetVincRepresentantDTO, $agendamento, $params);
            } else {
                $this->suspenderProcuracaoControlado(array($objMdPetVincRepresentantDTO), $agendamento, $params);
            }
        }

        private function _prepararRestabelecerResponsavelLegal($params)
        {
            $objMdPetVincRepresentantDTO = $this->_buscarRepresentacaoPorId($params['hdnIdVinculoRepresent']);

            if ($params['hdnCascata'] == 'S') {
                $arrObjMdPetVincRepresentantDTO = $this->listarVincRepresentInativosPorCNPJControlado($objMdPetVincRepresentantDTO->getStrCNPJ());
                $this->restabelecerProcuracaoControlado($arrObjMdPetVincRepresentantDTO, false,  $params);
            } else {
                $this->restabelecerProcuracaoControlado(array($objMdPetVincRepresentantDTO), false, $params);
            }
        }

        public function listarVincRepresentAtivosPorCPFControlado($cpf)
        {
            $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
            $objMdPetVincRepresentantDTO->setStrTpVinc(MdPetVincRepresentantRN::$NT_FISICA);
            $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
	        $objMdPetVincRepresentantDTO->adicionarCriterio(['CPF','CpfProcurador'],
						    [InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL],
						    [$cpf,$cpf],
						    InfraDTO::$OPER_LOGICO_OR);
            $objMdPetVincRepresentantDTO->retTodos();
            $objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
            $objMdPetVincRepresentantDTO->retStrNomeProcurador();
            $objMdPetVincRepresentantDTO->retStrEmail();
            $objMdPetVincRepresentantDTO->retStrCpfProcurador();
            return (new MdPetVincRepresentantRN())->listar($objMdPetVincRepresentantDTO);
        }

        public function listarVincRepresentInativosPorCPFControlado($cpf)
        {
            $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
            $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
            $objMdPetVincRepresentantDTO->setStrTpVinc(MdPetVincRepresentantRN::$NT_FISICA);
            $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_INATIVO);
            $objMdPetVincRepresentantDTO->setStrCPF($cpf);
            $objMdPetVincRepresentantDTO->retTodos();
            $objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
            $objMdPetVincRepresentantDTO->retStrNomeProcurador();
            return $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);
        }
        
        public function possuiProcessoVinculacaoControlado($cpf){
	
	        $contatoDTO = new ContatoDTO();
	        $contatoDTO->setDblCpf($cpf);
	        $contatoDTO->retDblCpf();
	        $contatoDTO->retNumIdContato();
	        $arrIdContato = InfraArray::converterArrInfraDTO((new ContatoRN())->listarRN0325($contatoDTO), 'IdContato');
	
	        $objMdPetVinculoDTO = new MdPetVinculoDTO();
	        $objMdPetVinculoDTO->retNumIdMdPetVinculo();
	        $objMdPetVinculoDTO->retNumIdContato();
	        $objMdPetVinculoDTO->setNumIdContato($arrIdContato, InfraDTO::$OPER_IN);
	        $objMdPetVinculoDTO->setDblIdProtocolo(NULL, InfraDTO::$OPER_DIFERENTE);
	        $objMdPetVinculoDTO->retDblIdProtocolo();
	        $arrIdProtocolo = InfraArray::converterArrInfraDTO((new MdPetVinculoRN())->listar($objMdPetVinculoDTO), 'IdProtocolo');
	
	        return !empty($arrIdProtocolo);
    	
        }

        public function listarVincRepresentAtivosPorCNPJControlado($cnpj)
        {
            $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
            $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
            $objMdPetVincRepresentantDTO->setStrTpVinc(MdPetVincRepresentantRN::$NT_JURIDICA);
            $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
            $objMdPetVincRepresentantDTO->setStrCNPJ($cnpj);
            $objMdPetVincRepresentantDTO->retTodos();
            $objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
            $objMdPetVincRepresentantDTO->retStrNomeProcurador();
            $objMdPetVincRepresentantDTO->retStrEmail();
            $objMdPetVincRepresentantDTO->retStrCpfProcurador();
            return $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);
        }

        public function listarVincRepresentInativosPorCNPJControlado($cnpj)
        {
            $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
            $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
            $objMdPetVincRepresentantDTO->setStrCNPJ($cnpj);
            $objMdPetVincRepresentantDTO->retTodos();
            $objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
            $objMdPetVincRepresentantDTO->retStrNomeProcurador();
            $objMdPetVincRepresentantDTO->retStrTpVinc();
            return $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);
        }

        public function restabelecerProcuracaoControlado($arrObjMdPetVincRepresentantDTO, $agendamento,  $params)
        {
            try {
                $objResponsavelLegal = null;
                $arrObjMdPetVincRepresentantProcuradores = array();

                //REALIZA O RESTABELECIMENTO DOS VINCULOS
                foreach ($arrObjMdPetVincRepresentantDTO as $objMdPetVincRepresentantDTO) {
                    switch ($objMdPetVincRepresentantDTO->getStrTipoRepresentante()) {

                        case MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES:
                            $this->_restabelecerVinculo($objMdPetVincRepresentantDTO);
                            $arrObjMdPetVincRepresentantProcuradores[] = $objMdPetVincRepresentantDTO;
                            $idVinculo = $objMdPetVincRepresentantDTO->getNumIdMdPetVinculo();
                            break;

                        case MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL:
                            $this->_restabelecerVinculo($objMdPetVincRepresentantDTO);
                            $objResponsavelLegal = $objMdPetVincRepresentantDTO;
                            $idVinculo = $objMdPetVincRepresentantDTO->getNumIdMdPetVinculo();
                            break;
                    }
                }

                $objDocumento = $this->_encaminhamentoParaCriarDocumento(null, $arrObjMdPetVincRepresentantProcuradores, $objResponsavelLegal, $agendamento, $params);

                //BUSCAR PROCEDIMENTO DA PROCURACAO
                $objProcedimento = (new MdPetVinculoUsuExtRN())->_getObjProcedimentoPorVinculo($idVinculo);

                //SE A OPERAO FOR EXECUTADA MANUALMENTE VIA SISTEMA DEVE ASSINAR E TRAVAR DOCUMENTO
                $this->_assinarTravarDocumentoGerado($objProcedimento, $params, $objDocumento);

                // ENVIA E-MAIL DE SUSPENSAO
                $objVinculo = $this->_recuperaVinculo($idVinculo);
                $objUsuario = $this->_recuperaUsuario($objVinculo->getNumIdContatoRepresentante());
                $paramsParaEmail['procedimento'] = $objProcedimento;
                $paramsParaEmail['arrListaProcuradores'] = $this->_montarListaProcuradoresParaEmail($objUsuario, $arrObjMdPetVincRepresentantProcuradores, $objProcedimento->getStrProtocoloProcedimentoFormatado());
                $paramsParaEmail['dados']['hdnIdVinculo'] = $idVinculo;
                $paramsParaEmail['dados']['hdnNumeroSei'] = $agendamento ? 'No informado' : $params['hdnNumeroSei'];
                $paramsParaEmail['dados']['numeroSeiVinculacao'] = $objDocumento->getIdDocumento();
                $paramsParaEmail['dados']['hdnOperacao'] = $params['hdnOperacao'];

                if ($objResponsavelLegal) {
                    (new MdPetIntEmailNotificacaoRN())->enviarEmailVincRestabelecimento($paramsParaEmail);
                }

                foreach($arrObjMdPetVincRepresentantProcuradores as $objMdPetVincRepresentantProcuradores){
                    $paramsParaEmail['dados']['hdnIdVinculoRepresent'] = $objMdPetVincRepresentantProcuradores->getNumIdMdPetVinculoRepresent();
                    $paramsParaEmail['dados']['procuracao_tipo']       = $objMdPetVincRepresentantProcuradores->getStrNomeTipoVinculacao();
                    $paramsParaEmail['dados']['procuracao_doc_num']    = $this->_buscarProcuracaoDocNum($objMdPetVincRepresentantProcuradores->getNumIdMdPetVinculoRepresent());
                    (new MdPetIntEmailNotificacaoRN())->enviarEmailProcuracaoSuspRest($paramsParaEmail);
                }

                // VINCULAR DOCUMENTO DE JUSTIFICATIVA AO VINCULO CASO NAO SEJA DE AGENDAMENTO
                if (!$agendamento) {
                    $staDiligenciaTipoDocumento = MdPetVincDocumentoRN::$TP_PROTOCOLO_DILIGENCIA_RESTABELECIMENTO;
                    $this->_vincularDocumentoVinculo($params['hdnNumeroSei'], $params['hdnIdVinculoRepresent'], $staDiligenciaTipoDocumento);
                }

                //INSERIR ANDAMENTOS
                $this->_inserirAndamento($objProcedimento);

            } catch (Exception $e) {
                PaginaSEI::getInstance()->processarExcecao($e, true);
            }
        }

        private function _montarListaProcuradoresParaEmail($objUsuarioDTO, $arrObjMdPetVincRepresentantDTO)
        {
            $arrListaProcuradores = [];
            foreach ($arrObjMdPetVincRepresentantDTO as $objMdPetVincRepresentantDTO) {
                $procurador = $objMdPetVincRepresentantDTO->getStrNomeProcurador() . ' - ' . $objMdPetVincRepresentantDTO->getStrNomeTipoVinculacao() . ' n ' . $this->_buscarProcuracaoDocNum($objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent());
                array_push($arrListaProcuradores, $procurador);
                return $arrListaProcuradores;
            }
        }

        public function suspenderProcuracaoControlado($arrObjMdPetVincRepresentantDTO, $agendamento = false, $params)
        {
            
                $arrObjMdPetVincRepresentantProcuradores = [];
	            $arrRerepentVincSuspensos = [];
	            $objResponsavelLegal = null;
                $autoRepresentacao = null;

                //REALIZA A SUSPENSO DOS VINCULOS
                foreach ($arrObjMdPetVincRepresentantDTO as $objMdPetVincRepresentantDTO) {
	                
                	try {
		            
		                $arrRerepentVincSuspensos['tipoRepresentante']      = $objMdPetVincRepresentantDTO->getStrTipoRepresentante();
		                $arrRerepentVincSuspensos['documentoObjVinc']       = $objMdPetVincRepresentantDTO->getStrTpVinc() == 'J' ? $objMdPetVincRepresentantDTO->getStrCNPJ() : $objMdPetVincRepresentantDTO->getStrCPF();
		                $arrRerepentVincSuspensos['razaoSocialNomeVinc']    = $objMdPetVincRepresentantDTO->getStrRazaoSocialNomeVinc();
	                    
	                    switch ($objMdPetVincRepresentantDTO->getStrTipoRepresentante()) {
	                        
	                        case MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES:
	                        case MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL:
	                            
	                            $this->_suspenderVinculo($objMdPetVincRepresentantDTO);
	                            $arrObjMdPetVincRepresentantProcuradores[] = $objMdPetVincRepresentantDTO;
	                            $idVinculo = $objMdPetVincRepresentantDTO->getNumIdMdPetVinculo();
	                            $arrRerepentVincSuspensos['procuracoes'][] = $objMdPetVincRepresentantDTO;
	                            break;
	
	                        case MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL:
	                            $this->_suspenderVinculo($objMdPetVincRepresentantDTO);
	                            $objResponsavelLegal = $objMdPetVincRepresentantDTO;
	                            $idVinculo = $objMdPetVincRepresentantDTO->getNumIdMdPetVinculo();
	                            $arrRerepentVincSuspensos['responsavel_legal'] = $objMdPetVincRepresentantDTO;
	                            break;
		
		                    case MdPetVincRepresentantRN::$PE_AUTORREPRESENTACAO:
			
			                    $arrRerepentVincSuspensos['arrBuscarOurasRepresentacoesPorCPF'] = $this->_buscarRepresentacoesPorCPF($objMdPetVincRepresentantDTO->getStrCPF(), $agendamento, $params);
			                    $this->_suspenderAutoRepresentacao($objMdPetVincRepresentantDTO);
			                    $autoRepresentacao = $objMdPetVincRepresentantDTO;
			                    $arrRerepentVincSuspensos['autorrepresentante'][] = $autoRepresentacao;
			                    break;
	                      
	                    }
	                    
	                } catch (Exception $e) {
		                PaginaSEI::getInstance()->processarExcecao($e, true);
	                }
                    
                }

                if($idVinculo){

                    // CRIA DOCUMENTO DE SUSPENSO
                    $objDocumento = $this->_encaminhamentoParaCriarDocumento($autoRepresentacao, $arrObjMdPetVincRepresentantProcuradores, $objResponsavelLegal, $agendamento, $params);
                    $arrRerepentVincSuspensos['documentoSuspensao'] = $objDocumento->getDocumentoFormatado();

                    //BUSCAR PROCEDIMENTO DA PROCURACAO
                    $objProcedimento = (new MdPetVinculoUsuExtRN())->_getObjProcedimentoPorVinculo($idVinculo);

                    // SE A OPERAO FOR EXECUTADA MANUALMENTE VIA SISTEMA DEVE ASSINAR E TRAVAR DOCUMENTO
                    if (!$agendamento) {
                        $this->_assinarTravarDocumentoGerado($objProcedimento, $params, $objDocumento);
                    }

                    //ENVIA E-MAIL DE SUSPENSAO
                    $objVinculo = $this->_recuperaVinculo($idVinculo);
                    $objUsuario = $this->_recuperaUsuario($objVinculo->getNumIdContatoRepresentante());
                    $paramsParaEmail['procedimento'] = $objProcedimento;
                    $paramsParaEmail['arrListaProcuradores'] = $this->_montarListaProcuradoresParaEmail($objUsuario, $arrObjMdPetVincRepresentantProcuradores, $objProcedimento->getStrProtocoloProcedimentoFormatado());
                    $paramsParaEmail['dados']['hdnIdVinculo'] = $idVinculo;
                    $paramsParaEmail['dados']['hdnNumeroSei'] = $agendamento ? $arrRerepentVincSuspensos['documentoSuspensao'] : $params['hdnNumeroSei'];
                    $paramsParaEmail['dados']['numeroSeiVinculacao'] = $objDocumento->getIdDocumento();
                    $paramsParaEmail['dados']['hdnOperacao'] = $params['hdnOperacao'];
                    $paramsParaEmail['autoRepresentacao'] = false;
                    $paramsParaEmail['agendamento'] = $agendamento;
                    $paramsParaEmail['documento_suspensao_procuracao'] = $paramsParaEmail['dados']['hdnNumeroSei'];

                    //E-MAIL  DIFERENTE PARA RESPONSAVEL LEGAL / PROCURADOR / AUTOREPRESENTACAO
                    if ($objResponsavelLegal) {
                        (new MdPetIntEmailNotificacaoRN())->enviarEmailVincSuspensao($paramsParaEmail);
                    }

                    if($autoRepresentacao){
                        $paramsParaEmail['autoRepresentacao'] = true;
                        $objUsuarioAutorepresentacao = $this->_recuperaUsuario($autoRepresentacao->getNumIdContato());
                        $paramsAuto['paramsParaEmail'] = $paramsParaEmail;
                        $paramsAuto['objUsuarioAutorepresentacao'] = $objUsuarioAutorepresentacao;
                        $paramsAuto['autoRepresentacao'] = $autoRepresentacao;
                        $paramsAuto['agendamento'] = $agendamento;
                        (new MdPetIntEmailNotificacaoRN())->enviarEmailSuspensaoAutorepresentacao($paramsAuto);
                    }

                    foreach($arrObjMdPetVincRepresentantProcuradores as $objMdPetVincRepresentantProcuradores){
                        $paramsParaEmail['dados']['hdnIdVinculoRepresent'] = $objMdPetVincRepresentantProcuradores->getNumIdMdPetVinculoRepresent();
                        $paramsParaEmail['dados']['procuracao_tipo']       = $objMdPetVincRepresentantProcuradores->getStrNomeTipoVinculacao();
                        $paramsParaEmail['dados']['procuracao_doc_num']    = $this->_buscarProcuracaoDocNum($objMdPetVincRepresentantProcuradores->getNumIdMdPetVinculoRepresent());
                        (new MdPetIntEmailNotificacaoRN())->enviarEmailProcuracaoSuspRest($paramsParaEmail);
                    }

                    //VINCULAR DOCUMENTO DE JUSTIFICATIVA AO VINCULO CASO NAO SEJA DE AGENDAMENTO
                    if (!$agendamento) {
                        $staDiligenciaTipoDocumento = MdPetVincDocumentoRN::$TP_PROTOCOLO_DILIGENCIA_SUSPENSAO;
                        $this->_vincularDocumentoVinculo($params['hdnNumeroSei'], $params['hdnIdVinculoRepresent'], $staDiligenciaTipoDocumento);
                    }

                    //INSERIR ANDAMENTOS
                    $this->_inserirAndamento($objProcedimento);
                }

                return $arrRerepentVincSuspensos;
                
        }
	
		public function suspenderVinculacoesConectado($arrObjMdPetVincRepresentantDTO, $agendamento = false, $params)
		{
				
			$arrObjMdPetVincRepresentantProcuradores = [];
			$arrRerepentVincSuspensos = [];
			$objResponsavelLegal = null;
			$autoRepresentacao = null;
			$enviarEmail = true;
			
			//REALIZA A SUSPENSO DOS VINCULOS
			foreach ($arrObjMdPetVincRepresentantDTO as $objMdPetVincRepresentantDTO) {
				
				$arrRerepentVincSuspensos['tipoRepresentante']      = $objMdPetVincRepresentantDTO->getStrTipoRepresentante();
				$arrRerepentVincSuspensos['documentoObjVinc']       = $objMdPetVincRepresentantDTO->getStrTpVinc() == 'J' ? $objMdPetVincRepresentantDTO->getStrCNPJ() : $objMdPetVincRepresentantDTO->getStrCPF();
				$arrRerepentVincSuspensos['razaoSocialNomeVinc']    = $objMdPetVincRepresentantDTO->getStrRazaoSocialNomeVinc();
				
				try {
					
					switch ($objMdPetVincRepresentantDTO->getStrTipoRepresentante()) {
						
						case MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES:
						case MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL:
							
							$this->_suspenderVinculo($objMdPetVincRepresentantDTO);
							$arrObjMdPetVincRepresentantProcuradores[] = $objMdPetVincRepresentantDTO;
							$idVinculo = $objMdPetVincRepresentantDTO->getNumIdMdPetVinculo();
							$arrRerepentVincSuspensos['procuracoes'][] = $objMdPetVincRepresentantDTO;
							break;
						
						case MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL:
							$this->_suspenderVinculo($objMdPetVincRepresentantDTO);
							$objResponsavelLegal = $objMdPetVincRepresentantDTO;
							$idVinculo = $objMdPetVincRepresentantDTO->getNumIdMdPetVinculo();
							$arrRerepentVincSuspensos['responsavel_legal'] = $objMdPetVincRepresentantDTO;
							break;
						
						case MdPetVincRepresentantRN::$PE_AUTORREPRESENTACAO:
							
							$arrRerepentVincSuspensos['arrBuscarOurasRepresentacoesPorCPF'] = $this->_buscarRepresentacoesPorCPF($objMdPetVincRepresentantDTO->getStrCPF(), $agendamento, $params);
							$this->_suspenderAutoRepresentacao($objMdPetVincRepresentantDTO);
							$autoRepresentacao = $objMdPetVincRepresentantDTO;
							$arrRerepentVincSuspensos['autorrepresentante'][] = $autoRepresentacao;
							break;
						
					}
					
				} catch (Exception $e) {
					
					$arrRerepentVincSuspensos['erros'][] = $arrRerepentVincSuspensos['documentoObjVinc'];
					
				}
				
			}
			
			if($idVinculo){
				
				// CRIA DOCUMENTO DE SUSPENSO
				$objDocumento = $this->_encaminhamentoParaCriarDocumento($autoRepresentacao, $arrObjMdPetVincRepresentantProcuradores, $objResponsavelLegal, $agendamento, $params);
				
				if($objDocumento){
					
					$arrRerepentVincSuspensos['documentoSuspensao'] = $objDocumento->getDocumentoFormatado();
					
					//BUSCAR PROCEDIMENTO DA PROCURACAO
					$objProcedimento = (new MdPetVinculoUsuExtRN())->_getObjProcedimentoPorVinculo($idVinculo);
					
					// SE A OPERAO FOR EXECUTADA MANUALMENTE VIA SISTEMA DEVE ASSINAR E TRAVAR DOCUMENTO
					if (!$agendamento) {
						$this->_assinarTravarDocumentoGerado($objProcedimento, $params, $objDocumento);
					}
					
					//ENVIA E-MAIL DE SUSPENSAO
					$objVinculo = $this->_recuperaVinculo($idVinculo);
					$objUsuario = $this->_recuperaUsuario($objVinculo->getNumIdContatoRepresentante());
					$paramsParaEmail['procedimento'] = $objProcedimento;
					$paramsParaEmail['arrListaProcuradores'] = $this->_montarListaProcuradoresParaEmail($objUsuario, $arrObjMdPetVincRepresentantProcuradores, $objProcedimento->getStrProtocoloProcedimentoFormatado());
					$paramsParaEmail['dados']['hdnIdVinculo'] = $idVinculo;
					$paramsParaEmail['dados']['hdnNumeroSei'] = $agendamento ? $arrRerepentVincSuspensos['documentoSuspensao'] : $params['hdnNumeroSei'];
					$paramsParaEmail['dados']['numeroSeiVinculacao'] = $objDocumento->getIdDocumento();
					$paramsParaEmail['dados']['hdnOperacao'] = $params['hdnOperacao'];
					$paramsParaEmail['autoRepresentacao'] = false;
					$paramsParaEmail['agendamento'] = $agendamento;
					$paramsParaEmail['documento_suspensao_procuracao'] = $paramsParaEmail['dados']['hdnNumeroSei'];
					
					//E-MAIL  DIFERENTE PARA RESPONSAVEL LEGAL / PROCURADOR / AUTOREPRESENTACAO
					if ($objResponsavelLegal && $enviarEmail) {
						(new MdPetIntEmailNotificacaoRN())->enviarEmailVincSuspensao($paramsParaEmail);
					}
					
					if($autoRepresentacao){
						$paramsParaEmail['autoRepresentacao'] = true;
						$objUsuarioAutorepresentacao = $this->_recuperaUsuario($autoRepresentacao->getNumIdContato());
						if(!empty($objUsuarioAutorepresentacao) && $enviarEmail){
							$paramsAuto['paramsParaEmail'] = $paramsParaEmail;
							$paramsAuto['objUsuarioAutorepresentacao'] = $objUsuarioAutorepresentacao;
							$paramsAuto['autoRepresentacao'] = $autoRepresentacao;
							$paramsAuto['agendamento'] = $agendamento;
							(new MdPetIntEmailNotificacaoRN())->enviarEmailSuspensaoAutorepresentacao($paramsAuto);
						}
					}
					
					foreach($arrObjMdPetVincRepresentantProcuradores as $objMdPetVincRepresentantProcuradores){
						$paramsParaEmail['dados']['hdnIdVinculoRepresent'] = $objMdPetVincRepresentantProcuradores->getNumIdMdPetVinculoRepresent();
						$paramsParaEmail['dados']['procuracao_tipo']       = $objMdPetVincRepresentantProcuradores->getStrNomeTipoVinculacao();
						$paramsParaEmail['dados']['procuracao_doc_num']    = $this->_buscarProcuracaoDocNum($objMdPetVincRepresentantProcuradores->getNumIdMdPetVinculoRepresent());
						if($enviarEmail){
							(new MdPetIntEmailNotificacaoRN())->enviarEmailProcuracaoSuspRest($paramsParaEmail);
						}
					}
					
					//VINCULAR DOCUMENTO DE JUSTIFICATIVA AO VINCULO CASO NAO SEJA DE AGENDAMENTO
					if (!$agendamento) {
						$staDiligenciaTipoDocumento = MdPetVincDocumentoRN::$TP_PROTOCOLO_DILIGENCIA_SUSPENSAO;
						$this->_vincularDocumentoVinculo($params['hdnNumeroSei'], $params['hdnIdVinculoRepresent'], $staDiligenciaTipoDocumento);
					}
					
					//INSERIR ANDAMENTOS
					$this->_inserirAndamento($objProcedimento);
					
				}
				
			}
			
			return $arrRerepentVincSuspensos;
			
		}

        private function _suspenderVinculo($objMdPetVincRepresentantDTO)
        {
            $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
            $representanteDTO = new MdPetVincRepresentantDTO();
            $representanteDTO->setNumIdMdPetVinculoRepresent($objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent());
            $representanteDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_SUSPENSO);
            $objMdPetVincRepresentantRN->alterar($representanteDTO);
        }

        private function _restabelecerVinculo($objMdPetVincRepresentantDTO)
        {
            $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
            $representanteDTO = new MdPetVincRepresentantDTO();
            $representanteDTO->setNumIdMdPetVinculoRepresent($objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent());
            $representanteDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
            $objMdPetVincRepresentantRN->alterar($representanteDTO);
        }

        private function _encaminhamentoParaCriarDocumento($autoRepresentacao = null, $arrObjMdPetVincRepresentantProcuradores = null, $objResponsavelLegal = null, $agendamento = false, $params)
        {
            //Agendamento sempre suspende e nao passa parametros
            $operacao = $agendamento ? MdPetVincRepresentantRN::$RP_SUSPENSO : $params['hdnOperacao'];

            // NO CASO DE PROCURACOES
            if (!$objResponsavelLegal && !empty($arrObjMdPetVincRepresentantProcuradores)) {
                if (count($arrObjMdPetVincRepresentantProcuradores) == 1) {
                    return $this->_prepararGerarDocumentoDetalhado(current($arrObjMdPetVincRepresentantProcuradores), $agendamento, $operacao, $params['hdnNumeroSei']);
                } else {
                    return $this->_prepararGerarDocumentoSimplificado($arrObjMdPetVincRepresentantProcuradores, $agendamento, $params['hdnNumeroSei'], $operacao);
                }
            }

            // NO CASO DE RESPONSAVEL LEGAL
            if($objResponsavelLegal){
                return $this->_prepararGerarDocumentoResponsavelLegal(
                  $objResponsavelLegal,
                  $arrObjMdPetVincRepresentantProcuradores,
                  $agendamento,
                  $params['hdnOperacao'],
                  $params['hdnNumeroSei']
                );
            }
        }

        private function _prepararGerarDocumentoDetalhado($objMdPetVincRepresentantDTO, $agendamento, $operacao, $numeroSei)
        {
	
        	$params['agendamento'] = $agendamento;
            $params['vinculoRepresent'] = $objMdPetVincRepresentantDTO;
            $params['operacao'] = $operacao;
            $params['tipoProcuracao'] = $objMdPetVincRepresentantDTO->getStrTipoRepresentante();
            $params['idVinculo'] = $objMdPetVincRepresentantDTO->getNumIdMdPetVinculo();
            $params['outorganteTipoPessoa'] = $objMdPetVincRepresentantDTO->getStrTpVinc();
            $params['idVinculoRepresent'] = $objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent();
            $params['objVinculo'] = $this->_recuperaVinculo($params['idVinculo']);
            $params['objusuario'] = $this->_recuperaUsuario($params['objVinculo']->getNumIdContatoRepresentante());
            $params['tipoVinculo'] = $objMdPetVincRepresentantDTO->getStrTipoRepresentante();
            $params['ortoganteNome'] = $objMdPetVincRepresentantDTO->getStrRazaoSocialNomeVinc();
            $params['ortogadoNome'] = $objMdPetVincRepresentantDTO->getStrNomeProcurador();
            $params['dtLimiteValidade'] = $objMdPetVincRepresentantDTO->getDthDataLimiteValidade();
            $params['poderes'] = $objMdPetVincRepresentantDTO->getStrTipoPoderesLista();
            $params['abrangencia'] = $objMdPetVincRepresentantDTO->getStrStaAbrangencia();
            $params['abrangenciaTipo'] = $objMdPetVincRepresentantDTO->getStrStaAbrangenciaTipo();
            $params['procuracao_doc_num'] = $this->_buscarProcuracaoDocNum($objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent());
            $params['numeroSei'] = $numeroSei;
            $params['procedimento'] = (new MdPetVinculoUsuExtRN())->_getObjProcedimentoPorVinculo($objMdPetVincRepresentantDTO->getNumIdMdPetVinculo());
	        $params['idSerie'] = $this->_getTipoDocumentoGerado($operacao, $objMdPetVincRepresentantDTO->getStrTipoRepresentante());
	        
	        if(isset($params['objusuario']) && !empty($params['objusuario']) && !empty($params['objusuario']->getStrSiglaOrgao())){
		        $params['corpoDocumentoHTML'] = $this->_montarHtmlDetalhado($params);
		        return $agendamento ? $this->_gerarDocumentoAgendamento($params) : $this->_gerarDocumento($params);
	        }else{
	        	return null;
	        }
	        
        }

        private function _prepararGerarDocumentoSimplificado($arrObjMdPetVincRepresentantDTO, $agendamento, $numeroSei, $operacao)
        {
         
        	$objMdPetVincRepresentantDTO = current($arrObjMdPetVincRepresentantDTO);
            $params['idVinculo'] = $objMdPetVincRepresentantDTO->getNumIdMdPetVinculo();
            $params['vinculoRepresent'] = $objMdPetVincRepresentantDTO;
            $params['objVinculo'] = $this->_recuperaVinculo($params['idVinculo']);
	        $params['objusuario'] = $this->_recuperaUsuario($params['objVinculo']->getNumIdContatoRepresentante());
			
	        if(empty($params['objusuario'])){
	        	return false;
	        }
	        
	        $params['ortoganteNome'] = $objMdPetVincRepresentantDTO->getStrRazaoSocialNomeVinc();
            $params['arrObjMdPetVincRepresentant'] = $arrObjMdPetVincRepresentantDTO;
            $params['numeroSei'] = $numeroSei;
            $params['corpoDocumentoHTML'] = $this->_montarHtmlSuspensaoSimplificado($params);
            $params['idSerie'] = $this->_getTipoDocumentoGerado($operacao, $objMdPetVincRepresentantDTO->getStrTipoRepresentante());
            $params['procedimento'] = (new MdPetVinculoUsuExtRN())->_getObjProcedimentoPorVinculo($objMdPetVincRepresentantDTO->getNumIdMdPetVinculo());
	
	        return $agendamento ? $this->_gerarDocumentoAgendamento($params) : $this->_gerarDocumento($params);
	        
        }

        private function _prepararGerarDocumentoResponsavelLegal($objResponsavelLegal, $arrObjMdPetVincRepresentantDTO, $agendamento, $operacao, $numeroSei = null)
        {
            $params['agendamento'] = $agendamento;
            $params['tipoProcuracao'] = MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL;
            $params['operacao'] = $operacao;
            $params['arrObjMdPetVincRepresentant'] = $arrObjMdPetVincRepresentantDTO;
            $params['vinculoRepresent'] = $objResponsavelLegal;
            $params['idVinculo'] = $objResponsavelLegal->getNumIdMdPetVinculo();
            $params['idVinculoRepresent'] = $objResponsavelLegal->getNumIdMdPetVinculoRepresent();
            $params['objVinculo'] = $this->_recuperaVinculo($params['idVinculo']);
            $params['objusuario'] = $this->_recuperaUsuario($params['objVinculo']->getNumIdContatoRepresentante());
            $params['tipoVinculo'] = $objResponsavelLegal->getStrTipoRepresentante();
            $params['ortoganteNome'] = $objResponsavelLegal->getStrRazaoSocialNomeVinc();
            $params['ortogadoNome'] = $objResponsavelLegal->getStrNomeProcurador();
            $params['dtLimiteValidade'] = $objResponsavelLegal->getDthDataLimiteValidade();
            $params['poderes'] = $objResponsavelLegal->getStrTipoPoderesLista();
            $params['abrangencia'] = $objResponsavelLegal->getStrStaAbrangencia();
            $params['abrangenciaTipo'] = $objResponsavelLegal->getStrStaAbrangenciaTipo();
            $params['procuracao_doc_num'] = $this->_buscarProcuracaoDocNum($objResponsavelLegal->getNumIdMdPetVinculoRepresent());
            $params['numeroSei'] = $numeroSei;
            $params['procedimento'] = (new MdPetVinculoUsuExtRN())->_getObjProcedimentoPorVinculo($objResponsavelLegal->getNumIdMdPetVinculo());
	        $params['idSerie'] = $this->_getTipoDocumentoGerado($operacao, $objResponsavelLegal->getStrTipoRepresentante(), $agendamento);
            $params['corpoDocumentoHTML'] = $this->_montarHtmlDetalhado($params);

            return $agendamento ? $this->_gerarDocumentoAgendamento($params) : $this->_gerarDocumento($params);
	        
        }
	
		private function _getTipoDocumentoGerado($operacao, $tipoRepresentante, $agendamento = false)
		{
			
			$objInfraParametro = new InfraParametro($this->getObjInfraIBanco());
			
			if(in_array($tipoRepresentante, [ self::$PE_PROCURADOR_ESPECIAL, self::$PE_PROCURADOR, self::$PE_PROCURADOR_SIMPLES ])){
				
				$idSerieTipoDocProc = ($operacao == MdPetVincRepresentantRN::$RP_SUSPENSO) ? MdPetIntSerieRN::$MD_PET_ID_SERIE_PROCURACAO_SUSPENSAO : MdPetIntSerieRN::$MD_PET_ID_SERIE_PROCURACAO_RESTABELECIMENTO;
				return $objInfraParametro->getValor($idSerieTipoDocProc);
				
			}
			
			if($tipoRepresentante == self::$PE_RESPONSAVEL_LEGAL){
				
				$idSerieTipoDocVinc = ($operacao == MdPetVincRepresentantRN::$RP_SUSPENSO) ? ($agendamento ? MdPetIntSerieRN::$MD_PET_ID_SERIE_VINC_SUSPENSAO_AUTOMATICA : MdPetIntSerieRN::$MD_PET_ID_SERIE_VINC_SUSPENSAO) : MdPetIntSerieRN::$MD_PET_ID_SERIE_VINC_RESTABELECIMENTO;
				return $objInfraParametro->getValor($idSerieTipoDocVinc);
				
			}
			
		}

        private function _assinarTravarDocumentoGerado($objProcedimentoDTO, $params, $objDocumento)
        {
            //RECUPERANDO DADOS PARA ASSINATURA
            $mdPetProcessoRN = new mdPetProcessoRN();

            $objMdPetVincTpProcessoRN = new MdPetVincTpProcessoRN();
            $objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();
            $objMdPetVincTpProcessoDTO->retTodos();
            $objMdPetVincTpProcessoDTO->setNumIdMdPetVincTpProcesso(MdPetVincTpProcessoRN::$ID_FIXO_MD_PET_VINCULO_USU_EXT);
            $objMdPetVincTpProcesso = $objMdPetVincTpProcessoRN->consultar($objMdPetVincTpProcessoDTO);

            $idUnidade = null;
            if (!is_null($objMdPetVincTpProcesso)) {
                $idUnidade = $objMdPetVincTpProcesso->getNumIdUnidade();
            }

            //Unidade
            $objUnidadeDTO = new UnidadeDTO();
            $objUnidadeRN = new UnidadeRN();
            $objUnidadeDTO->setNumIdUnidade($idUnidade);
            $objUnidadeDTO->retTodos();
            $objUnidadeDTO = $objUnidadeRN->consultarRN0125($objUnidadeDTO);

            // Recupera documento
            $docRN = new DocumentoRN();
            $parObjDocumentoDTO = new DocumentoDTO();
            $parObjDocumentoDTO->retStrProtocoloProcedimentoFormatado();
            $parObjDocumentoDTO->retStrProtocoloDocumentoFormatado();
            $parObjDocumentoDTO->retStrStaDocumento();
            $parObjDocumentoDTO->setDblIdDocumento($objDocumento->getIdDocumento());
            $parObjDocumentoDTO->retDblIdDocumento();
            $objDocumento = $docRN->consultarRN0005($parObjDocumentoDTO);

            //Assina e trava documento
            $mdPetProcessoRN->assinarETravarDocumentoProcesso($objUnidadeDTO, $params, $objDocumento, $objProcedimentoDTO);
        }

        private function _buscarRepresentacoesPorCPF($cpf, $agendamento, $params)
        {
	
	        $arrRetornoLogAgendamento = [];
        	
	        // BUSCANDO VINCULOS ONDE O CPF É O OUTORGADO
            $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
            $objMdPetVincRepresentantDTO->setStrStaEstado('A');
            $objMdPetVincRepresentantDTO->setStrCpfProcurador($cpf);
            $objMdPetVincRepresentantDTO->setStrTipoRepresentante([MdPetVincRepresentantRN::$PE_AUTORREPRESENTACAO], InfraDTO::$OPER_NOT_IN);
            $objMdPetVincRepresentantDTO->retTodos();
            $objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
            $objMdPetVincRepresentantDTO->retStrNomeProcurador();
            $objMdPetVincRepresentantDTO->retStrTpVinc();
            $objMdPetVincRepresentantDTO->retStrCNPJ();
            $arrObjMdPetVincRepresentantDTO = (new MdPetVincRepresentantRN())->listar($objMdPetVincRepresentantDTO);
            
            if((new MdPetVincRepresentantRN())->contar($objMdPetVincRepresentantDTO) > 0){
	
	            foreach ($arrObjMdPetVincRepresentantDTO as $objMdPetVincRepresentantDTO) {
		
		            // VERIFICA SE O OBJETO DO USUARIO ESTA ACESSIVEL
		            $objVinculo = $this->_recuperaVinculo($objMdPetVincRepresentantDTO->getNumIdMdPetVinculo());
		            $objUsuario = $this->_recuperaUsuario($objVinculo->getNumIdContatoRepresentante());
		
		            if(!empty($objUsuario) && $objUsuario->getStrSiglaOrgao()){
			
			            $this->_suspenderVinculo($objMdPetVincRepresentantDTO);
			            $objDocumento = $this->_prepararGerarDocumentoDetalhado($objMdPetVincRepresentantDTO, $agendamento, MdPetVincRepresentantRN::$RP_SUSPENSO, $params['hdnNumeroSei']);
			            $objProcedimento = (new MdPetVinculoUsuExtRN())->_getObjProcedimentoPorVinculo($objMdPetVincRepresentantDTO->getNumIdMdPetVinculo());
			
			            //INFORMACOES RETORNADAS PARA CRIACAO DE LOG QUANDO A EXECUCAO VIER DE UM AGENDAMENTO
			            $vinc['documentoSuspensao'] = $objDocumento->getDocumentoFormatado();
			            $vinc['objMdPetVincRepresentant'] = $objMdPetVincRepresentantDTO;
			            $arrRetornoLogAgendamento[] = $vinc;
			
			            if (!$agendamento) {
				            $this->_assinarTravarDocumentoGerado($objProcedimento, $params, $objDocumento);
			            }
			
		            }else{
			
			            //INFORMACOES RETORNADAS PARA CRIACAO DE LOG QUANDO A EXECUCAO VIER DE UM AGENDAMENTO
			            $vinc['erros'][] = $objMdPetVincRepresentantDTO->getNumIdMdPetVinculo();
			            $arrRetornoLogAgendamento[] = $vinc;
			
		            }
		
	            }
            	
            }

            return $arrRetornoLogAgendamento;
            
        }

        private function _buscarRepresentacaoPorId($idMdPetVinculoRepresent)
        {
            $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
            $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
            $objMdPetVincRepresentantDTO->setNumIdMdPetVinculoRepresent($idMdPetVinculoRepresent);
            $objMdPetVincRepresentantDTO->retTodos();
            $objMdPetVincRepresentantDTO->retStrCPF();
            $objMdPetVincRepresentantDTO->retStrCNPJ();
            $objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
            $objMdPetVincRepresentantDTO->retStrTpVinc();
            $objMdPetVincRepresentantDTO->retStrNomeProcurador();
            $objMdPetVincRepresentantDTO->setBolExclusaoLogica(false);
            return $objMdPetVincRepresentantRN->consultar($objMdPetVincRepresentantDTO);
        }

        public function _buscarProcuracaoDocNum($idVincRepresent)
        {
            $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
            $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
            $objMdPetVincRepresentantDTO->setNumIdMdPetVinculoRepresent($idVincRepresent);
            $objMdPetVincRepresentantDTO->retDblIdDocumento();
            $objMdPetVincRepresentantDTO->setOrdDblIdDocumento(InfraDTO::$TIPO_ORDENACAO_ASC);
            $objMdPetVincRepresentantDTO->setNumMaxRegistrosRetorno(1);
            $objMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->consultar($objMdPetVincRepresentantDTO);

            $objProtocoloDTO = new ProtocoloDTO();
            $objProtocoloDTO->retStrProtocoloFormatado();
            $objProtocoloDTO->setDblIdProtocolo($objMdPetVincRepresentantDTO->getDblIdDocumento());
            $objProtocolo = (new ProtocoloRN())->consultarRN0186($objProtocoloDTO);

            return $objProtocolo->getStrProtocoloFormatado();
        }

        private function _gerarDocumentoAgendamento($params)
        {

            // DEVE SIMULAR LOGIN NA UNIDADE PARA GERAR DOCUMENTO E DEPOIS REESTABELECER
            $objUsuarioPetRN = new MdPetIntUsuarioRN();
            $idUsuarioPet = $objUsuarioPetRN->getObjUsuarioPeticionamento(true);
            SessaoSEI::getInstance()->setBolHabilitada(false);
            SessaoSEI::getInstance()->setNumIdUnidadeAtual($params['procedimento']->getNumIdUnidadeGeradoraProtocolo());
            SessaoSEI::getInstance()->setNumIdUsuario($idUsuarioPet);

            $objSaidaDocumentoAPI = $this->_gerarDocumento($params);

            //necessario forçar update da coluna sta_documento da tabela documento
            //Add conteúdo atualizado com o nome do documento formatado gerado.
            //inclusão via SeiRN não permitiu definir como documento de formulário automático

            $objDocumentoRN = new DocumentoRN();
            $objDocumentoDTO = new DocumentoDTO();

            $objDocumentoDTO->retTodos();
            $objDocumentoDTO->setDblIdDocumento($objSaidaDocumentoAPI->getIdDocumento());
            $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);
            $objDocumentoDTO->setStrStaDocumento(DocumentoRN::$TD_FORMULARIO_AUTOMATICO);
            $objDocumentoBD = new DocumentoBD($this->getObjInfraIBanco());
            $objDocumentoBD->alterar($objDocumentoDTO);

            SessaoSEI::getInstance()->setBolHabilitada(true);

            return $objSaidaDocumentoAPI;
        }

        private function _gerarDocumento($params)
        {
	
	        $objSeiRN = new SeiRN();
            $objDocumentoAPI = new DocumentoAPI();
            $objDocumentoAPI->setIdProcedimento($params['objVinculo']->getDblIdProtocolo());
            $objDocumentoAPI->setTipo(ProtocoloRN::$TP_DOCUMENTO_GERADO);
            $objDocumentoAPI->setIdSerie($params['idSerie']);
            $objDocumentoAPI->setSinAssinado($params['agendamento'] ? 'N' : 'S');
            $objDocumentoAPI->setSinBloqueado('S');
            $objDocumentoAPI->setIdHipoteseLegal(null);
            $objDocumentoAPI->setNivelAcesso(ProtocoloRN::$NA_PUBLICO);
            $objDocumentoAPI->setIdTipoConferencia(null);
            $objDocumentoAPI->setConteudo(base64_encode(utf8_encode($params['corpoDocumentoHTML'])));
            $objDocumentoAPI->setIdUnidadeGeradora($params['procedimento']->getNumIdUnidadeGeradoraProtocolo());

            return $objSeiRN->incluirDocumento($objDocumentoAPI);
        }

        private function _vincularDocumentoVinculo($numeroSEI, $idMdPetVinculoRepresentLegal, $staDiligenciaTipoDocumento)
        {
            // Justificativa  um documento
            $objDocumentoDTO = new DocumentoDTO();
            $numeroSEIFormt = '%' . trim($numeroSEI);
            $objDocumentoDTO->setStrProtocoloDocumentoFormatado($numeroSEIFormt, InfraDTO::$OPER_LIKE);
            $objDocumentoDTO->retDblIdDocumento();
            $objDocumentoDTO->setNumMaxRegistrosRetorno('1');
            $objDocumentoDTO = (new DocumentoRN())->consultarRN0005($objDocumentoDTO);

            $objMdPetVinUsuExtProcRN = new MdPetVinUsuExtProcRN();
            $objMdPetVinUsuExtProcRN->_adicionarDadosArquivoVinculacao($objDocumentoDTO->getDblIdDocumento(), $idMdPetVinculoRepresentLegal, $staDiligenciaTipoDocumento);

        }

        private function _inserirAndamento($objProcedimentoDTO)
        {
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

                // 2) ltima aberta
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
            $objAtributoAndamentoDTO->setStrValor($objUnidadeDTO->getStrSigla() . '  ' . $objUnidadeDTO->getStrDescricao());
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

            $objAtividadeRN = new AtividadeRN();
            $objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);
        }

        private function _retornarOrtogadoDados($arrObjMdPetVincRepresentantDTO)
        {
            $outorganteDados = '';

            foreach ($arrObjMdPetVincRepresentantDTO as $objMdPetVincRepresentantDTO) {
                $procuracaoDocNum = $this->_buscarProcuracaoDocNum($objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent());
                $outorganteDados .= '<tr>';
                $outorganteDados .= '<td class="tdTabulacao">Procuração:</td>';
                $outorganteDados .= '<td style="font-weight: normal">' . $procuracaoDocNum . '</td>';
                $outorganteDados .= '</tr>';
                $outorganteDados .= '<tr>';
                $outorganteDados .= '<td class="tdTabulacao">Nome:</td>';
                $outorganteDados .= '<td style="font-weight: normal">' . $objMdPetVincRepresentantDTO->getStrNomeProcurador() . '</td>';
                $outorganteDados .= '</tr>';

                $outorganteDados .= '<tr><td colspan="2" class="clear">&nbsp;</td></tr>';

            }

            return $outorganteDados;
        }

        private function _retornarNSei($numeroSei)
        {
            $nSei = '';
            
            if($numeroSei){
                $nSei = '';
                $nSei .= '<tr class="trEspaco">';
                $nSei .= '<td>Documento de Justificativa:</td>';
                $nSei .= '<td style="font-weight: normal">Suspensão corrida conforme formalizado no documento SEI nº ' . $numeroSei . '</td>';
                $nSei .= '</tr>';
            }

            return $nSei;
        }

        private function _montarHtmlDetalhado($params)
        {
            // CASO SEJA RESPONSAVEL LEGAL
            if($params['tipoProcuracao'] == MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL){
                if ($params['operacao'] == MdPetVincRepresentantRN::$RP_SUSPENSO) {
                    return $this->_montarHtmlSuspensaoResponsavelLegal($params);
                }

                if ($params['operacao'] == MdPetVincRepresentantRN::$RP_ATIVO) {
                    return $this->_montarHtmlRestabelcimentoResponsavelLegal($params);
                }
            }

            // CASO SEJA PROCURACAO
            if ($params['tipoProcuracao'] == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES || $params['tipoProcuracao'] == MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL) {

                // CASO O DOCUMENTO SEJA PARA SUSPENDER UMA PROCURAO
                if ($params['operacao'] == MdPetVincRepresentantRN::$RP_SUSPENSO) {
                    return $this->_montarHtmlSuspensaoProcuracaoDetalhada($params);

                }
                // CASO O DOCUMENTO SEJA PARA RESTABELECER UMA PROCURAO
                if ($params['operacao'] == MdPetVincRepresentantRN::$RP_ATIVO) {
                    return $this->_montarHtmlRestabelecimentoProcuracaoDetalhada($params);
                }
            }
        }

        private function _montarHtmlSuspensaoProcuracaoDetalhada($params)
        {
            $procuracaoTipo = $params['tipoProcuracao'] == MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL ? 'Especial' : 'Simples';
	        $outorganteTipoPessoa = $params['outorganteTipoPessoa'] == 'F' ? 'Pessoa Física' : 'Pessoa Jurídica';
	        
	        $justificativa = 'Suspensão ocorrida conforme formalizado no documento SEI nº ' . $params['numeroSei'];
	        $justificativaAgendamento = 'Suspensão ocorrida automaticamente em razão da consulta à Receita Federal retornar situação cadastral do procurador como não "Ativa".';
	        if($params['agendamento']){
		        $justificativa = $justificativaAgendamento;
	        }
         
	        $url = dirname(__FILE__) . '/../md_pet_vinc_modelo_suspensao_procurador.php';
            
            $htmlModelo = file_get_contents($url);

            $htmlModelo = str_replace('@procuracaoDados', $this->_retornaProcuradorDados($params), $htmlModelo);
            $htmlModelo = str_replace('@outorganteDados', $this->_retornaOrtoganteDados($params['ortoganteNome']), $htmlModelo);
            $htmlModelo = str_replace('@outorgadoNome', $params['ortogadoNome'], $htmlModelo);
            $htmlModelo = str_replace('@numeroSEI', $params['numeroSei'], $htmlModelo);
            $htmlModelo = str_replace('@sigla_orgao@', $params['objusuario']->getStrSiglaOrgao(), $htmlModelo);
            $htmlModelo = str_replace('@procuracaoTipo', $procuracaoTipo, $htmlModelo);
            $htmlModelo = str_replace('@procuracaoDocNum', $params['procuracao_doc_num'], $htmlModelo);
            $htmlModelo = str_replace('@descricao_orgao@', $params['objusuario']->getStrDescricaoOrgao(), $htmlModelo);
	        $htmlModelo = str_replace('@outorganteTipoPessoa', $outorganteTipoPessoa, $htmlModelo);
	        $htmlModelo = str_replace('@justificativa', $justificativa, $htmlModelo);
	        $htmlModelo = str_replace('@documento_suspensao_procuracao', $params['numeroSei'], $htmlModelo);

            return $htmlModelo;
        }

        private function _montarHtmlRestabelecimentoProcuracaoDetalhada($params)
        {
            $procuracaoTipo = $params['tipoProcuracao'] == MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL ? 'Especial' : 'Simples';
	        $outorganteTipoPessoa = $params['outorganteTipoPessoa'] == 'F' ? 'Pessoa Física' : 'Pessoa Jurídica';

            // parei na alterao das variaveis de restabelecimentop
            $url = dirname(__FILE__) . '/../md_pet_vinc_modelo_restabelecimento_procurador.php';
            $htmlModelo = file_get_contents($url);
            $htmlModelo = str_replace('@procuracaoTipo', $procuracaoTipo, $htmlModelo);
            $htmlModelo = str_replace('@procuracaoDocNum', $params['procuracao_doc_num'], $htmlModelo);
            $htmlModelo = str_replace('@outorganteTipoPessoa', 'Pessoa Fsica', $htmlModelo);
            $htmlModelo = str_replace('@procuracaoDados', $this->_retornaProcuradorDados($params), $htmlModelo);
            $htmlModelo = str_replace('@outorganteDados', $this->_retornaOrtoganteDados($params['ortoganteNome']), $htmlModelo);
            $htmlModelo = str_replace('@outorgadoNome', $params['ortogadoNome'], $htmlModelo);
            $htmlModelo = str_replace('@numeroSEI', $params['numeroSei'], $htmlModelo);
            $htmlModelo = str_replace('@sigla_orgao@', $params['objusuario']->getStrSiglaOrgao(), $htmlModelo);
            $htmlModelo = str_replace('@descricao_orgao@', $params['objusuario']->getStrDescricaoOrgao(), $htmlModelo);
	        $htmlModelo = str_replace('@outorganteTipoPessoa', $outorganteTipoPessoa, $htmlModelo);

            return $htmlModelo;
            
        }

        private function _montarHtmlSuspensaoResponsavelLegal($params)
        {
	
	        $justificativa = 'Suspensão ocorrida conforme formalizado no documento SEI nº ' . $params['numeroSei'];
	        $justificativaAgendamento = 'Suspensão ocorrida automaticamente em razão da consulta à Receita Federal retornar situação cadastral da entidade como não "Ativa".';
	        
	        if($params['agendamento']){
		        $justificativa = $justificativaAgendamento;
	        }
	
	        $url = dirname(__FILE__) . '/../md_pet_vinc_modelo_suspensao.php';
	        
            $htmlModelo = file_get_contents($url);
            
//            die(var_dump([
//            	'getStrCNPJ' => InfraUtil::formatarCnpj($params['vinculoRepresent']->getStrCNPJ()),
//            	'getDthDataVinculo' => $params['objVinculo']->getDthDataVinculo(),
//            	'getStrProtocoloProcedimentoFormatado' => $params['procedimento']->getStrProtocoloProcedimentoFormatado(),
//            	'_getParagrafoCascataSuspensaoRestabelecimento' => $this->_getParagrafoCascataSuspensaoRestabelecimento($params['arrObjMdPetVincRepresentant'], 'suspensas'),
//            	'getStrSiglaOrgao' => $params['objusuario']->getStrSiglaOrgao(),
//            	'getStrDescricaoOrgao' => $params['objusuario']->getStrDescricaoOrgao(),
//            ]));

            $htmlModelo = str_replace('@RazaoSocial', $params['ortoganteNome'], $htmlModelo);
            $htmlModelo = str_replace('@Cnpj', InfraUtil::formatarCnpj($params['vinculoRepresent']->getStrCNPJ()), $htmlModelo);
            $htmlModelo = str_replace('@nomeRespLegal', $params['ortogadoNome'], $htmlModelo);
            $htmlModelo = str_replace('@numeroSEI', $params['numeroSei'], $htmlModelo);
	        $htmlModelo = str_replace('@justificativa', $justificativa, $htmlModelo);
            $htmlModelo = str_replace('@dataVinc', $params['objVinculo']->getDthDataVinculo(), $htmlModelo);
            $htmlModelo = str_replace('@numProcessoVinc', $params['procedimento']->getStrProtocoloProcedimentoFormatado(), $htmlModelo);
            $htmlModelo = str_replace('@paragrafoCascata', $this->_getParagrafoCascataSuspensaoRestabelecimento($params['arrObjMdPetVincRepresentant'], 'suspensas'), $htmlModelo);
            $htmlModelo = str_replace('@sigla_orgao@', $params['objusuario']->getStrSiglaOrgao(), $htmlModelo);
            $htmlModelo = str_replace('@descricao_orgao@', $params['objusuario']->getStrDescricaoOrgao(), $htmlModelo);
            
            return $htmlModelo;
            
        }

        private function _montarHtmlRestabelcimentoResponsavelLegal($params)
        {
            $url = dirname(__FILE__) . '/../md_pet_vinc_modelo_restabelecimento.php';
            $htmlModelo = file_get_contents($url);
            $htmlModelo = str_replace('@RazaoSocial', $params['ortoganteNome'], $htmlModelo);
            $htmlModelo = str_replace('@Cnpj', InfraUtil::formatarCnpj($params['vinculoRepresent']->getStrCNPJ()), $htmlModelo);
            $htmlModelo = str_replace('@nomeRespLegal', $params['ortogadoNome'], $htmlModelo);
            $htmlModelo = str_replace('@numeroSEI', $params['numeroSei'], $htmlModelo);
            $htmlModelo = str_replace('@dataVinc', $params['objVinculo']->getDthDataVinculo(), $htmlModelo);
            $htmlModelo = str_replace('@numProcessoVinc', $params['procedimento']->getStrProtocoloProcedimentoFormatado(), $htmlModelo);
            $htmlModelo = str_replace('@paragrafoCascata', $this->_getParagrafoCascataSuspensaoRestabelecimento($params['arrObjMdPetVincRepresentant'], 'restabelecidas'), $htmlModelo);
            $htmlModelo = str_replace('@descricao_orgao@', $params['objusuario']->getStrDescricaoOrgao(), $htmlModelo);

            return $htmlModelo;
        }

        private function _getParagrafoCascataSuspensaoRestabelecimento($arrObjMdPetVincRepresentant, $tipoLista){

            $paragrafoCascata = $ulListaProcuradores = '';

            if(!empty($arrObjMdPetVincRepresentant)){
                $commonStyle = 'font: normal 12pt Calibri; text-align: justify; word-wrap: normal; text-indent: 0; margin: 6pt;';
                $commonText  = 'Procurações Eletrônicas, abaixo listadas, concedidas para representação da Pessoa Jurídica restam igualmente '.$tipoLista.':';
                $ulListaProcuradores .= '<ul>';
                foreach ($arrObjMdPetVincRepresentant as $objMdPetVincRepresentant) {
                    $ulListaProcuradores .= '<li><p style="'.$commonStyle.'">'.$objMdPetVincRepresentant->getStrNomeProcurador().'</p></li>';
                }
                $ulListaProcuradores .= '</ul>';

                // Montando listagem dos procuradores para o documento
                $paragrafoCascata = '<p style="'.$commonStyle.'">Comunicamos que as '.$commonText.'</p>'.$ulListaProcuradores;

            }
            return $paragrafoCascata;
        }

        private function _montarHtmlSuspensaoSimplificado($params)
        {
            $url = dirname(__FILE__) . '/../md_pet_vinc_modelo_suspensao_procurador_simplificado.php';
            $htmlModelo = file_get_contents($url);

            // DADOS DO OUTORGANTE
            $htmlModelo = str_replace('@outorganteDados', $this->_retornaOrtoganteDados($params['ortoganteNome']), $htmlModelo);
            $htmlModelo = str_replace('@outorgadoDados', $this->_retornarOrtogadoDados($params['arrObjMdPetVincRepresentant']), $htmlModelo);

            $htmlModelo = str_replace('@numeroSEI', $this->_retornarNSei($params['numeroSei']), $htmlModelo);

            // INJETA OS MEMAIS DADOS NO TEMPLATE
            $htmlModelo = str_replace('@sigla_orgao@', $params['objusuario']->getStrSiglaOrgao(), $htmlModelo);
            $htmlModelo = str_replace('@descricao_orgao@', $params['objusuario']->getStrDescricaoOrgao(), $htmlModelo);
	
	        $htmlModelo = str_replace('@data_suspensao@', date('d/m/Y H:i:s'), $htmlModelo);
	        $htmlModelo = str_replace('@protocolo_formatado@', $params['objVinculo']->getStrProtocoloFormatado(), $htmlModelo);

            return $htmlModelo;
        }

        private function _retornaProcuradorDados($params)
        {
            $procuracaoDados = '';
            $incluiPoderes = false;

            if (in_array($params['tipoVinculo'], [MdPetVincRepresentantRN::$PE_PROCURADOR, MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES])) {

                $procuracaoDados .= '<tr class="trEspaco"><td colspan="2" style="font: bold 12pt Calibri, sans-serif">Dados da Procurao Eletrônica:</td></tr>';
                $procuracaoDados .= '<tr>';
                $procuracaoDados .= '<td class="tdTabulacao" style="font: bold 12pt Calibri, sans-serif">Validade:</td>';
                $procuracaoDados .= '<td style="font-weight: normal">' . (!empty($params['dtLimiteValidade']) ? $params['dtLimiteValidade'] : 'Indeterminada') . '</td>';
                $procuracaoDados .= '</tr>';
                
                if($incluiPoderes){
	                $procuracaoDados .= '<tr>';
	                $procuracaoDados .= '<td class="tdTabulacao" style="font: bold 12pt Calibri, sans-serif">Poderes:</td>';
	                $procuracaoDados .= '<td style="font-weight: normal">' . $params['poderes'] . '</td>';
	                $procuracaoDados .= '</tr>';
                }
                
                $procuracaoDados .= '<tr>';
                $procuracaoDados .= '<td class="tdTabulacao" style="font: bold 12pt Calibri, sans-serif">Abrangência:</td>';

                if ($params['abrangencia'] == MdPetVincRepresentantRN::$PR_ESPECIFICO) {

                    $procuracaoDados .= '<td style="font-weight: normal">';
                    $procuracaoDados .= $params['abrangenciaTipo'] . ':<br>';
                    $procuracaoDados .= '<ul class="abrangenciaLista">';

                    $objMdPetRelVincRepProtocDTO = new MdPetRelVincRepProtocDTO();
                    $objMdPetRelVincRepProtocDTO->retTodos();
                    $objMdPetRelVincRepProtocDTO->setNumIdVincRepresent($params['idVinculoRepresent']);
                    $arrObjMdPetRelVincRepProtocDTO = (new MdPetRelVincRepProtocRN)->listar($objMdPetRelVincRepProtocDTO);

                    if ($arrObjMdPetRelVincRepProtocDTO) {
                        foreach ($arrObjMdPetRelVincRepProtocDTO as $objMdPetRelVincRepProtoc) {
                            $objProtocoloDTO = new ProtocoloDTO();
                            $objProtocoloDTO->retStrProtocoloFormatado();
                            $objProtocoloDTO->setDblIdProtocolo($objMdPetRelVincRepProtoc->getNumIdProtocolo());
                            $objProtocoloDTO = (new ProtocoloRN())->consultarRN0186($objProtocoloDTO);
                            if ($objProtocoloDTO) {
                                $procuracaoDados .= '<li>' . $objProtocoloDTO->getStrProtocoloFormatado() . '</li>';
                            }
                        }
                    }

                    $procuracaoDados .= '</ul>';
                    $procuracaoDados .= '</td>';

                } else {
                    $procuracaoDados .= '<td style="font: normal 12pt Calibri, sans-serif">' . $params['abrangenciaTipo'] . '</td>';
                }
                $procuracaoDados .= '</tr>';
                $procuracaoDados .= '<tr><td colspan="2" class="clear">&nbsp;</td></tr>';
            }

            return $procuracaoDados;
        }

        private function _retornaOrtoganteDados($nome)
        {
            $outorganteDados = '';
            $outorganteDados .= '<tr>';
            $outorganteDados .= '<td class="tdTabulacao" style="font: bold 12pt Calibri, sans-serif">Nome:</td>';
            $outorganteDados .= '<td style="font-weight: normal">' . $nome . '</td>';
            $outorganteDados .= '</tr>';

            return $outorganteDados;
        }

        private function _recuperaVinculo($idVinculo)
        {
            $objMdPetVinculoRN = new MdPetVinculoRN();
            $objMdPetVinculoDTO = new MdPetVinculoDTO();
            $objMdPetVinculoDTO->retTodos(true);
            $objMdPetVinculoDTO->retNumIdMdPetVinculo();
            $objMdPetVinculoDTO->retNumIdContatoRepresentante();
            $objMdPetVinculoDTO->retDblIdProtocolo();
            $objMdPetVinculoDTO->retDthDataVinculo();
            $objMdPetVinculoDTO->retStrProtocoloFormatado();
            $objMdPetVinculoDTO->retStrProtocoloFormatado();
            $objMdPetVinculoDTO->setNumIdMdPetVinculo($idVinculo);
            $objMdPetVinculoDTO->setNumMaxRegistrosRetorno(1);

            return $objMdPetVinculoRN->consultar($objMdPetVinculoDTO);
        }

        private function _recuperaUsuario($idContato)
        {
         
        	$usuarioDTO = new UsuarioDTO();
            $usuarioDTO->retStrSiglaOrgao();
            $usuarioDTO->retStrDescricaoOrgao();
            $usuarioDTO->retStrNome();
            $usuarioDTO->retStrEmailContato();
            $usuarioDTO->setNumIdContato($idContato);
            $usuarioDTO->setStrSinAtivo('S');

            return (new UsuarioRN())->consultarRN0489($usuarioDTO);
            
        }

        private function _suspenderAutoRepresentacao($objMdPetVincRepresentantDTO)
        {
            // SUSPENDE VINCULAO
            $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_SUSPENSO);
            $this->alterar($objMdPetVincRepresentantDTO);

            // ALTERA A OBSERVACAO DO CONTATO PARA INFORMAR MOTIVO DE ESTAR INATIVO
            $objContatoDTO = new ContatoDTO();
            $objContatoDTO->retNumIdContato();
            $objContatoDTO->retStrObservacao();
            $objContatoDTO->setNumIdContato($objMdPetVincRepresentantDTO->getNumIdContato());

            $objContatoRN = new ContatoRN();
            $objContatoDTO = $objContatoRN->consultarRN0324($objContatoDTO);

            $msg = "Usurio Externo desativado em razo da Situao Cadastral de seu CPF na Receita Federal - Data: " . InfraData::getStrDataAtual() . ".\n" . $objContatoDTO->getStrObservacao();
            $objContatoDTO->setStrObservacao($msg);
            $objContatoRN->alterarRN0323($objContatoDTO);

            // DESATIVA USUARIO EXTERNO
            $objUsuarioRN = new UsuarioRN();
            $objUsuarioDTO = new UsuarioDTO();
            $objUsuarioDTO->retNumIdUsuario();
            $objUsuarioDTO->retNumIdOrgao();
            $objUsuarioDTO->retStrEmailContato();
            $objUsuarioDTO->setNumIdContato($objMdPetVincRepresentantDTO->getNumIdContato());

            $objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

            $objUsuario = new UsuarioDTO();
            $objUsuario->setNumIdUsuario($objUsuarioDTO->getNumIdUsuario());
            $objUsuarioRN->desativarRN0695(array($objUsuario));
        }

        private function _restabelecerAutoRepresentacao($objMdPetVincRepresentantDTO)
        {
            // SUSPENDE VINCULAO
            $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
            $this->alterar($objMdPetVincRepresentantDTO);

            // ALTERA A OBSERVACAO DO CONTATO PARA INFORMAR MOTIVO DE ESTAR INATIVO
            $objContatoDTO = new ContatoDTO();
            $objContatoDTO->retNumIdContato();
            $objContatoDTO->retStrObservacao();
            $objContatoDTO->setNumIdContato($objMdPetVincRepresentantDTO->getNumIdContato());

            // DESATIVA USUARIO EXTERNO
            $objUsuarioRN = new UsuarioRN();
            $objUsuarioDTO = new UsuarioDTO();
            $objUsuarioDTO->retNumIdUsuario();
            $objUsuarioDTO->retNumIdOrgao();
            $objUsuarioDTO->retStrEmailContato();
            $objUsuarioDTO->setNumIdContato($objMdPetVincRepresentantDTO->getNumIdContato());

            $objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

            $objUsuario = new UsuarioDTO();
            $objUsuario->setNumIdUsuario($objUsuarioDTO->getNumIdUsuario());
            $objUsuarioRN->reativarRN0696(array($objUsuario));
        }

        public function realizarProcessoSuspensaoRestabelecimentoProcuradorControlado($params)
        {

	    try {

            $dados      = isset($params['dados']) ? $params['dados'] : null;
            $idVinculo  = isset($dados['hdnIdVinculo']) ? $dados['hdnIdVinculo'] : null;
            $numeroSEI  = isset($dados['hdnNumeroSei']) ? $dados['hdnNumeroSei'] : null;
            $operacao   = isset($dados['hdnOperacao']) ? $dados['hdnOperacao'] : null;

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
                if($situacao == MdPetVincRepresentantRN::$RP_SUSPENSO) {
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
