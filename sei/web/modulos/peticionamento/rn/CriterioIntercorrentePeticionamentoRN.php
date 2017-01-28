<?
/**
 * ANATEL
 *
 * 21/10/2016 - criado por marcelo.bezerra - CAST
 *
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class CriterioIntercorrentePeticionamentoRN extends InfraRN
{

    //Tipos de Nível de Acesso
    public static $TIPO_NA_PADRAO = 'P';
    public static $TIPO_NA_USUARIO_PODE_INDICAR = 'I';

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    /**
     * Short description of method listarConectado
     *
     * @access protected
     * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
     * @param  $objCriterioIntercorrentePeticionamentoDTO
     * @return mixed
     * @throws InfraException
     */
    protected function listarConectado(CriterioIntercorrentePeticionamentoDTO $objCriterioIntercorrentePeticionamentoDTO)
    {

        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('criterio_intercorrente_peticionamento_listar', __METHOD__, $objCriterioIntercorrentePeticionamentoDTO);

            $objCriterioIntercorrentePeticionamentoBD = new CriterioIntercorrentePeticionamentoBD($this->getObjInfraIBanco());
            $ret = $objCriterioIntercorrentePeticionamentoBD->listar($objCriterioIntercorrentePeticionamentoDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException ('Erro listando', $e);
        }
    }


    /**
     * Short description of method consultarConectado
     *
     * @access protected
     * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
     * @param  $objCriterioIntercorrentePeticionamentoDTO
     * @return mixed
     * @throws InfraException
     */
    protected function consultarConectado(CriterioIntercorrentePeticionamentoDTO $objCriterioIntercorrentePeticionamentoDTO)
    {

        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('criterio_intercorrente_peticionamento_consultar', __METHOD__, $objCriterioIntercorrentePeticionamentoDTO);

            // Valida Permissao
            $objCriterioIntercorrentePeticionamentoBD = new CriterioIntercorrentePeticionamentoBD($this->getObjInfraIBanco());
            $ret = $objCriterioIntercorrentePeticionamentoBD->consultar($objCriterioIntercorrentePeticionamentoDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro consultando', $e);
        }
    }

    /**
     * Short description of method alterarControlado
     *
     * @access protected
     * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
     * @param  $objCriterioIntercorrentePeticionamentoDTO
     * @return mixed
     * @throws InfraException
     */
    protected function alterarControlado(CriterioIntercorrentePeticionamentoDTO $objCriterioIntercorrentePeticionamentoDTO)
    {

        try {

            // Valida Permissao
            SessaoSEI::getInstance()->validarAuditarPermissao('criterio_intercorrente_peticionamento_alterar', __METHOD__, $objCriterioIntercorrentePeticionamentoDTO);

            $objInfraException = new InfraException();
            $this->_validarCamposObrigatorios($objCriterioIntercorrentePeticionamentoDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objCriterioIntercorrentePeticionamentoBD = new CriterioIntercorrentePeticionamentoBD($this->getObjInfraIBanco());
            $objRetorno = $objCriterioIntercorrentePeticionamentoBD->alterar($objCriterioIntercorrentePeticionamentoDTO);
            return $objRetorno;
        } catch (Exception $e) {
            throw new InfraException ('Erro alterando', $e);
        }
    }

    /**
     * Short description of method cadastrarControlado
     *
     * @access protected
     * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
     * @param  $objCriterioIntercorrentePeticionamentoDTO
     * @return mixed
     * @throws InfraException
     */
    protected function cadastrarControlado(CriterioIntercorrentePeticionamentoDTO $objCriterioIntercorrentePeticionamentoDTO)
    {

        try {
            // Valida Permissao
            SessaoSEI::getInstance()->validarAuditarPermissao('criterio_intercorrente_peticionamento_cadastrar', __METHOD__, $objCriterioIntercorrentePeticionamentoDTO);

            //Cadastrar Indisponibilidade
            $objCriterioIntercorrentePeticionamentoBD = new CriterioIntercorrentePeticionamentoBD($this->getObjInfraIBanco());
            $objCriterioIntercorrentePeticionamentoDTO->setStrSinAtivo('S');
            $objInfraException = new InfraException();

            $this->_validarCamposObrigatorios($objCriterioIntercorrentePeticionamentoDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objRetorno = $objCriterioIntercorrentePeticionamentoBD->cadastrar($objCriterioIntercorrentePeticionamentoDTO);

            return $objRetorno;
        } catch (Exception $e) {
            throw new InfraException ('Erro cadastrando Tamanho de Arquivo Peticionamento.', $e);
        }
    }

    /**
     * Short description of method cadastrarControlado
     *
     * @access protected
     * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
     * @param  $objCriterioIntercorrentePeticionamentoDTO
     * @return mixed
     * @throws InfraException
     */
    public function cadastrarPadrao(CriterioIntercorrentePeticionamentoDTO $objCriterioIntercorrentePeticionamentoDTO)
    {
        $objCriterioIntercorrentePeticionamentoDTO->setStrSinCriterioPadrao('S');
        return $this->cadastrarControlado($objCriterioIntercorrentePeticionamentoDTO);
    }

    /**
     * Short description of method cadastrarControlado
     *
     * @access protected
     * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
     * @param  $objCriterioIntercorrentePeticionamentoDTO
     * @return mixed
     * @throws InfraException
     */
    public function cadastrar(CriterioIntercorrentePeticionamentoDTO $objCriterioIntercorrentePeticionamentoDTO)
    {
        $objCriterioIntercorrentePeticionamentoDTO->setStrSinCriterioPadrao('N');
        return $this->cadastrarControlado($objCriterioIntercorrentePeticionamentoDTO);
    }

    /**
     * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
     * @access protected
     * @param $arrCriterioIntercorrentePeticionamentoDTO
     * @return void
     * @throws InfraException
     */
    protected function desativarControlado($arrCriterioIntercorrentePeticionamentoDTO)
    {

        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('criterio_intercorrente_peticionamento_desativar', __METHOD__, $arrCriterioIntercorrentePeticionamentoDTO);
            $objCriterioIntercorrentePeticionamentoBD = new CriterioIntercorrentePeticionamentoBD($this->getObjInfraIBanco());
            for($i = 0; $i < count($arrCriterioIntercorrentePeticionamentoDTO); $i ++) {
                $objCriterioIntercorrentePeticionamentoBD->desativar($arrCriterioIntercorrentePeticionamentoDTO[$i]);
            }

        } catch (Exception $e) {
            throw new InfraException ('Erro desativando.', $e);
        }

    }


    /**
     * Short description of method reativarControlado
     *
     * @access protected
     * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
     * @param  $arrCriterioIntercorrentePeticionamentoDTO
     * @return void
     * @throws InfraException
     */
    protected function reativarControlado($arrCriterioIntercorrentePeticionamentoDTO)
    {

        try {

            SessaoSEI::getInstance ()->validarAuditarPermissao('criterio_intercorrente_peticionamento_desativar', __METHOD__, $arrCriterioIntercorrentePeticionamentoDTO);

            $objCriterioIntercorrentePeticionamentoBD = new CriterioIntercorrentePeticionamentoBD($this->getObjInfraIBanco());
            for($i = 0; $i < count($arrCriterioIntercorrentePeticionamentoDTO); $i ++) {
                $objCriterioIntercorrentePeticionamentoBD->reativar($arrCriterioIntercorrentePeticionamentoDTO[$i]);
            }

        } catch(Exception $e) {
            throw new InfraException ('Erro desativando Critério Intercorrente Peticionamento.', $e );
        }
    }

    /**
     * Short description of method excluirControlado
     *
     * @access protected
     * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
     * @param  $arrCriterioIntercorrentePeticionamentoDTO
     * @return void
     * @throws InfraException
     */
    protected function excluirControlado($arrCriterioIntercorrentePeticionamentoDTO)
    {

        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('criterio_intercorrente_peticionamento_excluir', __METHOD__, $arrCriterioIntercorrentePeticionamentoDTO);

        } catch (Exception $e) {
            throw new InfraException ('Erro excluindo.', $e);
        }

    }

    /**
     * Short description of method _validarCamposObrigatorios
     *
     * @access private
     * @author Wilton Junior <wiltonbsjr@gmail.com>
     * @param  $objCriterioIntercorrentePeticionamentoDTO
     * @param  $objInfraException
     * @return mixed
     */
    private function _validarCamposObrigatorios($objCriterioIntercorrentePeticionamentoDTO, $objInfraException)
    {
        $objCriterioIntercorrentePeticionamentoValidarDTO = new CriterioIntercorrentePeticionamentoDTO();
        $objCriterioIntercorrentePeticionamentoValidarDTO->setNumIdTipoProcedimento($objCriterioIntercorrentePeticionamentoDTO->getNumIdTipoProcedimento());
        $objCriterioIntercorrentePeticionamentoValidarDTO->retTodos();
        $arrObjCriterioIntercorrentePeticionamentoValidarDTO = $this->consultar($objCriterioIntercorrentePeticionamentoValidarDTO);

        if(count($arrObjCriterioIntercorrentePeticionamentoValidarDTO) > 0){
            $objInfraException->adicionarValidacao('Tipo de Processo já possui Critério Intercorrente associado.');
        }

        /*
        $objCriterioIntercorrentePeticionamentoDTO = new CriterioIntercorrentePeticionamentoDTO();
        //$objCriterioIntercorrentePeticionamentoDTO->setNumIdCriterioIntercorrentePeticionamento();

        $objCriterioIntercorrentePeticionamentoDTO->setStrStaNivelAcesso($strStaNivelAcesso);
        $objCriterioIntercorrentePeticionamentoDTO->setNumIdHipoteseLegal($_POST['selHipoteseLegal']);
        */

        $valorParametroHipoteseLegal = $this->_retornaValorParametroHipoteseLegal();
        //Tipo de Processo
        if (InfraString::isBolVazia($objCriterioIntercorrentePeticionamentoDTO->getNumIdTipoProcedimento())) {
            $objInfraException->adicionarValidacao('Tipo de Processo Associado não informado.');
        }

        if (($objCriterioIntercorrentePeticionamentoDTO->getStrStaNivelAcesso() == 'S' && InfraString::isBolVazia($objCriterioIntercorrentePeticionamentoDTO->getStrStaNivelAcesso()))) {
            $objInfraException->adicionarValidacao('Nível de Acesso não informado.');
            //se informar nivel de acesso E o nivel for restrito ou sigiloso, PRECISA informar hipotese legal padrao
        } else if ($objCriterioIntercorrentePeticionamentoDTO->getStrStaNivelAcesso() == 'S' && $objCriterioIntercorrentePeticionamentoDTO->getStrStaNivelAcesso() == ProtocoloRN::$NA_RESTRITO && $valorParametroHipoteseLegal != '0') {

            if(! in_array($objCriterioIntercorrentePeticionamentoDTO->getStrStaNivelAcesso(), array('I', 'P'))){
                $objInfraException->adicionarValidacao('Nível de Acesso divergente.');
            }

            if (InfraString::isBolVazia($objCriterioIntercorrentePeticionamentoDTO->getNumIdHipoteseLegal())) {
                $objInfraException->adicionarValidacao('Hipótese legal não informada.');
            }
        }
    }

    private function _retornaValorParametroHipoteseLegal(){
        $objInfraParametroDTO = new InfraParametroDTO();
        $objInfraParametroRN  = new InfraParametroRN();
        $objInfraParametroDTO->retTodos();
        $objInfraParametroDTO->setStrNome('SEI_HABILITAR_HIPOTESE_LEGAL');
        $objInfraParametroDTO = $objInfraParametroRN->consultar($objInfraParametroDTO);
        $valorParametroHipoteseLegal = $objInfraParametroDTO->getStrValor();
        return $valorParametroHipoteseLegal;
    }



    /**
     * Short description of method consultarConectado
     *
     * @access protected
     * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
     * @param  $objCriterioIntercorrentePeticionamentoDTO
     * @return mixed
     * @throws InfraException
     */
    protected function contarConectado(CriterioIntercorrentePeticionamentoDTO $objCriterioIntercorrentePeticionamentoDTO)
    {

        try {

            // Valida Permissao
            $objCriterioIntercorrentePeticionamentoBD = new CriterioIntercorrentePeticionamentoBD($this->getObjInfraIBanco());
            $ret = $objCriterioIntercorrentePeticionamentoBD->contar($objCriterioIntercorrentePeticionamentoDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro contando', $e);
        }
    }

    protected function retornarCriterioPorTipoProcessoConectado($idTpProcedimento)
    {
        try {
            $objCriterioIntercorrenteDTO = new CriterioIntercorrentePeticionamentoDTO();
            $objCriterioIntercorrenteRN = new CriterioIntercorrentePeticionamentoRN();

            SessaoSEI::getInstance()->validarAuditarPermissao('criterio_intercorrente_peticionamento_consultar', __METHOD__, $objCriterioIntercorrenteDTO);

            // Verifica se o processo possui critério intercorrente cadastrado
            $objCriterioIntercorrenteDTO->retTodos();
            $objCriterioIntercorrenteDTO->setNumIdTipoProcedimento($idTpProcedimento);
            $objCriterioIntercorrenteDTO->setStrSinCriterioPadrao('N');
            $arrObjCriterioIntercorrenteDTO = $objCriterioIntercorrenteRN->listar($objCriterioIntercorrenteDTO);

            //Se não possui busca o padrão e cria um processo relacionado ao processo selecionado
            if (count($arrObjCriterioIntercorrenteDTO) > 0) {
                $ret = $arrObjCriterioIntercorrenteDTO[0];
            } else {
                $objCriterioIntercorrentePadraoDTO = new CriterioIntercorrentePeticionamentoDTO();
                $objCriterioIntercorrentePadraoDTO->setStrSinCriterioPadrao('S');
                $objCriterioIntercorrentePadraoDTO->retTodos();
                $arrObjCriterioIntercorrenteDTO = $objCriterioIntercorrenteRN->listar($objCriterioIntercorrentePadraoDTO);
                if (count($arrObjCriterioIntercorrenteDTO) <= 0) {
                    throw new InfraException ('Nenhum critério para Intercorrente Foi encontrado para o Tipo de Processo informado.');
                }
                $ret = $arrObjCriterioIntercorrenteDTO[0];
            }
            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando', $e);
        }
    }

    protected function retornarCriterioPorTipoProcessoEPadraoConectado($idTpProcedimento, $padrao = 'N')
    {
        $objCriterioIntercorrenteDTO = new CriterioIntercorrentePeticionamentoDTO();
        $objCriterioIntercorrenteRN = new CriterioIntercorrentePeticionamentoRN();

        SessaoSEI::getInstance()->validarAuditarPermissao('criterio_intercorrente_peticionamento_consultar', __METHOD__, $objCriterioIntercorrenteDTO);

        // Verifica se o processo possui critério intercorrente cadastrado
        $objCriterioIntercorrenteDTO->retTodos();
        $objCriterioIntercorrenteDTO->setNumIdTipoProcedimento($idTpProcedimento);
        $objCriterioIntercorrenteDTO->setStrSinCriterioPadrao($padrao);
        $arrObjCriterioIntercorrenteDTO = $objCriterioIntercorrenteRN->listar($objCriterioIntercorrenteDTO);
        return $arrObjCriterioIntercorrenteDTO;
    }


}

?>