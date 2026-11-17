<?php
/**
 * ANATEL
 *
 * 21/10/2016 - criado por marcelo.bezerra - CAST
 *
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetCriterioRN extends InfraRN
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
     * @param  $objMdPetCriterioDTO
     * @return mixed
     * @throws InfraException
     */
    protected function listarConectado(MdPetCriterioDTO $objMdPetCriterioDTO)
    {

        try {

            $objMdPetCriterioBD = new MdPetCriterioBD($this->getObjInfraIBanco());
            $ret = $objMdPetCriterioBD->listar($objMdPetCriterioDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException ('Erro ao listar os critérios para Intercorrente', $e);
        }
    }


    /**
     * Short description of method consultarConectado
     *
     * @access protected
     * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
     * @param  $objMdPetCriterioDTO
     * @return mixed
     * @throws InfraException
     */
    protected function consultarConectado(MdPetCriterioDTO $objMdPetCriterioDTO)
    {

        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_intercorrente_criterio_consultar', __METHOD__, $objMdPetCriterioDTO);

            // Valida Permissao
            $objMdPetCriterioBD = new MdPetCriterioBD($this->getObjInfraIBanco());
            $ret = $objMdPetCriterioBD->consultar($objMdPetCriterioDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro ao consultar o critério para Intercorrente', $e);
        }
    }

    /**
     * Short description of method alterarControlado
     *
     * @access protected
     * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
     * @param  $objMdPetCriterioDTO
     * @return mixed
     * @throws InfraException
     */
    protected function alterarControlado(MdPetCriterioDTO $objMdPetCriterioDTO)
    {

        try {

            // Valida Permissao
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_intercorrente_criterio_alterar', __METHOD__, $objMdPetCriterioDTO);

            $objInfraException = new InfraException();
            $this->_validarCamposObrigatorios($objMdPetCriterioDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdPetCriterioBD = new MdPetCriterioBD($this->getObjInfraIBanco());
            $objRetorno = $objMdPetCriterioBD->alterar($objMdPetCriterioDTO);
            return $objRetorno;
        } catch (Exception $e) {
            throw new InfraException ('Erro ao alterar o critério para Intercorrente', $e);
        }
    }

    /**
     * Short description of method cadastrarControlado
     *
     * @access protected
     * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
     * @param  $objMdPetCriterioDTO
     * @return mixed
     * @throws InfraException
     */
    protected function cadastrarControlado(MdPetCriterioDTO $objMdPetCriterioDTO)
    {

        try {
            // Valida Permissao
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_intercorrente_criterio_cadastrar', __METHOD__, $objMdPetCriterioDTO);

            return $this->cadastrarInterno($objMdPetCriterioDTO);
        } catch (Exception $e) {
            throw new InfraException ('Erro ao cadastrar o critério para Intercorrente', $e);
        }
    }

    /**
     * Valida e grava o critério na transação de quem chama.
     *
     * @param MdPetCriterioDTO $objMdPetCriterioDTO
     * @return MdPetCriterioDTO
     * @throws InfraException
     */
    private function cadastrarInterno(MdPetCriterioDTO $objMdPetCriterioDTO)
    {
        //Cadastrar Indisponibilidade
        $objMdPetCriterioBD = new MdPetCriterioBD($this->getObjInfraIBanco());

        $objInfraException = new InfraException();

        // Adiciona valor padrão para o campo SinIntercorrenteSigiloso caso o mesmo nao esteja presente no objeto
        if(!$objMdPetCriterioDTO->isSetStrSinIntercorrenteSigiloso()){
            $objMdPetCriterioDTO->setStrSinIntercorrenteSigiloso('S');
        }

        $this->_validarCamposObrigatorios($objMdPetCriterioDTO, $objInfraException);

        $objInfraException->lancarValidacoes();

        return $objMdPetCriterioBD->cadastrar($objMdPetCriterioDTO);
    }

    /**
     * Troca o critério padrão em uma só transação: se o novo for recusado, o atual continua gravado.
     *
     * @param MdPetCriterioDTO $objMdPetCriterioDTO
     * @return MdPetCriterioDTO
     * @throws InfraException
     */
    protected function cadastrarPadraoControlado(MdPetCriterioDTO $objMdPetCriterioDTO)
    {

        try {
            $objMdPetCriterioDTO->setStrSinCriterioPadrao('S');

            // Valida Permissao
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_intercorrente_criterio_cadastrar', __METHOD__, $objMdPetCriterioDTO);

            $objCriterioIntercorrentePadraoConsultaDTO = new MdPetCriterioDTO();
            $objCriterioIntercorrentePadraoConsultaDTO->setStrSinCriterioPadrao('S');
            $objCriterioIntercorrentePadraoConsultaDTO->retTodos();
            $objMdPetCriterioRN = new MdPetCriterioRN();
            $objCriterioIntercorrentePadraoDTO = $objMdPetCriterioRN->consultar($objCriterioIntercorrentePadraoConsultaDTO);

            if($objCriterioIntercorrentePadraoDTO){
                $this->excluir([$objCriterioIntercorrentePadraoDTO]);
            }

            return $this->cadastrarInterno($objMdPetCriterioDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro ao cadastrar o critério padrão para Intercorrente', $e);
        }
    }

    /**
     * Short description of method cadastrarControlado
     *
     * @access protected
     * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
     * @param  $objMdPetCriterioDTO
     * @return mixed
     * @throws InfraException
     */
    public function cadastrar(MdPetCriterioDTO $objMdPetCriterioDTO)
    {
        $objMdPetCriterioDTO->setStrSinCriterioPadrao('N');
        return $this->cadastrarControlado($objMdPetCriterioDTO);
    }

    /**
     * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
     * @access protected
     * @param $arrMdPetCriterioDTO
     * @return void
     * @throws InfraException
     */
    protected function desativarControlado($arrMdPetCriterioDTO)
    {

        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_intercorrente_criterio_desativar', __METHOD__, $arrMdPetCriterioDTO);
            $objMdPetCriterioBD = new MdPetCriterioBD($this->getObjInfraIBanco());
            for($i = 0; $i < count($arrMdPetCriterioDTO); $i ++) {
                $objMdPetCriterioBD->desativar($arrMdPetCriterioDTO[$i]);
            }

        } catch (Exception $e) {
            throw new InfraException ('Erro ao desativar o critério para Intercorrente', $e);
        }

    }


    /**
     * Short description of method reativarControlado
     *
     * @access protected
     * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
     * @param  $arrMdPetCriterioDTO
     * @return void
     * @throws InfraException
     */
    protected function reativarControlado($arrMdPetCriterioDTO)
    {

        try {

            SessaoSEI::getInstance ()->validarAuditarPermissao('md_pet_intercorrente_criterio_reativar', __METHOD__, $arrMdPetCriterioDTO);

            $objMdPetCriterioBD = new MdPetCriterioBD($this->getObjInfraIBanco());
            for($i = 0; $i < count($arrMdPetCriterioDTO); $i ++) {
                $objMdPetCriterioBD->reativar($arrMdPetCriterioDTO[$i]);
            }

        } catch(Exception $e) {
            throw new InfraException ('Erro ao reativar o critério para Intercorrente', $e );
        }
    }

    /**
     * Short description of method excluirControlado
     *
     * @access protected
     * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
     * @param  $arrMdPetCriterioDTO
     * @return void
     * @throws InfraException
     */
    protected function excluirControlado($arrMdPetCriterioDTO)
    {

        try {
            // TODO Ajustar para não deletar e cadastrar, apenas alterar
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_intercorrente_criterio_excluir', __METHOD__, $arrMdPetCriterioDTO);
            $objMdPetCriterioBD = new MdPetCriterioBD($this->getObjInfraIBanco());
            for($i = 0; $i < count($arrMdPetCriterioDTO); $i ++) {
                $objMdPetCriterioBD->excluir($arrMdPetCriterioDTO[$i]);
            }
        } catch (Exception $e) {
            throw new InfraException ('Erro ao excluir o critério para Intercorrente', $e);
        }

    }

    /**
     * Short description of method _validarCamposObrigatorios
     *
     * @access private
     * @author Wilton Junior <wiltonbsjr@gmail.com>
     * @param  $objMdPetCriterioDTO
     * @param  $objInfraException
     * @return mixed
     */
    private function _validarCamposObrigatorios($objMdPetCriterioDTO, $objInfraException)
    {
        $objCriterioIntercorrentePeticionamentoValidarDTO = new MdPetCriterioDTO();
        $objCriterioIntercorrentePeticionamentoValidarDTO->setNumIdTipoProcedimento($objMdPetCriterioDTO->getNumIdTipoProcedimento());
        $objCriterioIntercorrentePeticionamentoValidarDTO->setStrSinCriterioPadrao($objMdPetCriterioDTO->getStrSinCriterioPadrao());

        if($objMdPetCriterioDTO->isSetNumIdCriterioIntercorrentePeticionamento()){
            $objCriterioIntercorrentePeticionamentoValidarDTO->setNumIdCriterioIntercorrentePeticionamento($objMdPetCriterioDTO->getNumIdCriterioIntercorrentePeticionamento(),InfraDTO::$OPER_DIFERENTE);
        }

        $objCriterioIntercorrentePeticionamentoValidarDTO->retTodos();
        $objCriterioIntercorrentePeticionamentoExistenteDTO = $this->consultar($objCriterioIntercorrentePeticionamentoValidarDTO);

        if ($objCriterioIntercorrentePeticionamentoExistenteDTO != null) {
            $objInfraException->adicionarValidacao('Tipo de Processo já possui Critério Intercorrente associado.');
        }

        $valorParametroHipoteseLegal = $this->_retornaValorParametroHipoteseLegal();
        //Tipo de Processo
        if (InfraString::isBolVazia($objMdPetCriterioDTO->getNumIdTipoProcedimento())) {
            $objInfraException->adicionarValidacao('Tipo de Processo Associado não informado.');
        }

        if (($objMdPetCriterioDTO->getStrStaNivelAcesso() == 'S' && InfraString::isBolVazia($objMdPetCriterioDTO->getStrStaNivelAcesso()))) {
            $objInfraException->adicionarValidacao('Nível de Acesso não informado.');
            //se informar nivel de acesso E o nivel for restrito ou sigiloso, PRECISA informar hipotese legal padrao
        } else if ($objMdPetCriterioDTO->getStrStaNivelAcesso() == 'S' && $objMdPetCriterioDTO->getStrStaNivelAcesso() == ProtocoloRN::$NA_RESTRITO && $valorParametroHipoteseLegal != '0') {

            if(! in_array($objMdPetCriterioDTO->getStrStaNivelAcesso(), array('I', 'P'))){
                $objInfraException->adicionarValidacao('Nível de Acesso divergente.');
            }

            if (InfraString::isBolVazia($objMdPetCriterioDTO->getNumIdHipoteseLegal())) {
                $objInfraException->adicionarValidacao('Hipótese legal não informada.');
            }
        }

        $this->_validarTipoProcedimentoComAssunto($objMdPetCriterioDTO, $objInfraException);
    }

    /**
     * Short description of method _validarTipoProcessoAssociado
     *
     * @access private
     * @author Marcelo Bezerra <marcelo.cast@castgroup.com.br>
     * @param  $objMdPetTipoProcessoDTO
     * @param  $objInfraException
     * @return mixed
     */
    private function _validarTipoProcedimentoComAssunto(MdPetCriterioDTO $objMdPetCriterioDTO, InfraException $objInfraException){

        //VALIDA NOVA REGRA ADICIONADA
        // somente aceita tipo de processo que na parametrização do SEI tenha
        //indicação de pelo menos uma sugestao de assunto

        $relTipoProcedimentoDTO = new RelTipoProcedimentoAssuntoDTO();
        $relTipoProcedimentoDTO->retTodos();
        $relTipoProcedimentoDTO->setNumIdTipoProcedimento( $objMdPetCriterioDTO->getNumIdTipoProcedimento() );

        $relTipoProcedimentoRN = new RelTipoProcedimentoAssuntoRN();
        $arrLista = $relTipoProcedimentoRN->listarRN0192( $relTipoProcedimentoDTO );

        if( !is_array( $arrLista ) || count( $arrLista ) == 0 ){
            $msg = "Por favor informe um tipo de processo que na parametrização do SEI tenha indicação de pelo menos uma sugestão de assunto.";
            $objInfraException->adicionarValidacao ($msg);
        }
    }

    private function _retornaValorParametroHipoteseLegal(){
        $objInfraParametroDTO = new InfraParametroDTO();
        $objMdPetParametroRN  = new MdPetParametroRN();
        $objInfraParametroDTO->retTodos();
        $objInfraParametroDTO->setStrNome('SEI_HABILITAR_HIPOTESE_LEGAL');
        $objInfraParametroDTO = $objMdPetParametroRN->consultar($objInfraParametroDTO);
        $valorParametroHipoteseLegal = $objInfraParametroDTO->getStrValor();
        return $valorParametroHipoteseLegal;
    }

    /**
     * Short description of method consultarConectado
     *
     * @access protected
     * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
     * @param  $objMdPetCriterioDTO
     * @return mixed
     * @throws InfraException
     */
    protected function contarConectado(MdPetCriterioDTO $objMdPetCriterioDTO)
    {

        try {

            // Valida Permissao
            $objMdPetCriterioBD = new MdPetCriterioBD($this->getObjInfraIBanco());
            $ret = $objMdPetCriterioBD->contar($objMdPetCriterioDTO);

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro ao contar os critérios para Intercorrente', $e);
        }
    }

    private function _getCriterioTipoDeProcessoValidado($idTpProcedimento){
        $objMdPetCriterioDTO = new MdPetCriterioDTO();
        $objMdPetCriterioDTO->setNumIdTipoProcedimento($idTpProcedimento);
        $objMdPetCriterioDTO->setStrSinCriterioPadrao('N');
        $objMdPetCriterioDTO->setStrSinAtivo('S');
        $objMdPetCriterioDTO->retTodos();
        $objMdPetCriterioDTO->setNumMaxRegistrosRetorno(1);

        if($this->contar($objMdPetCriterioDTO) > 0 ) {
            $objMdPetCriterioDTO = $this->consultar($objMdPetCriterioDTO);
            return $objMdPetCriterioDTO;
        }

        return null;
    }

    private function _validarRetornarExistenciaCriterioPadrao(){
        $objMdPetCriterioPadraoDTO = new MdPetCriterioDTO();
        $objMdPetCriterioPadraoDTO->setStrSinCriterioPadrao('S');
        $objMdPetCriterioPadraoDTO->retTodos();

        if($this->contar($objMdPetCriterioPadraoDTO) == 0) {
            $objInfraException = new InfraException();
            $objInfraException->lancarValidacao('Nenhum critério para Intercorrente foi encontrado para o Tipo de Processo informado.');
        }else{
            $objMdPetCriterioPadraoDTO->setNumMaxRegistrosRetorno(1);
            return $this->consultar($objMdPetCriterioPadraoDTO);
        }
    }

    protected function retornarCriterioPorTipoProcessoConectado($arrParametro)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_intercorrente_criterio_consultar', __METHOD__);

            $isNovoRelacionado  = false;
            $idTpProcedimento   = $arrParametro['id_tipo_procedimento'];
            $isIntercorrente    = $arrParametro['isRespostaIntercorrente'];
            $staEstadoProtocolo = $arrParametro['sta_estado_protocolo'];

            $idsEstadosProcessosImpedidos = array(ProtocoloRN::$TE_PROCEDIMENTO_BLOQUEADO, ProtocoloRN::$TE_PROCEDIMENTO_SOBRESTADO);

            //Se o protocolo do processo está com estado BLOQUEADO ou SOBRESTADO é novo relacionado
            if (in_array($staEstadoProtocolo, $idsEstadosProcessosImpedidos)) {
            	
                return $this->_validarRetornarExistenciaCriterioPadrao();
            
            } else {

                //Se o protocolo está com outro estado será utilizado o Critério para Intercorrente Configurado(quando não for RESPOSTA)
                if ($isIntercorrente) {
                    $objCriterioConfiguradoDTO = $this->_getCriterioTipoDeProcessoValidado($idTpProcedimento);
	                //Se NÃO existe critério intercorrente configurado para o tipo de processo
	                //ou se for resposta da intimação deve realizar o processo em novo relacionado
	                return (!is_null($objCriterioConfiguradoDTO)) ? $objCriterioConfiguradoDTO : $this->_validarRetornarExistenciaCriterioPadrao();
                }
                
            }

        } catch (Exception $e) {
            throw new InfraException('Erro ao consultar o critério para Intercorrente do tipo de processo', $e);
        }
    }

    protected function retornarCriterioPorTipoProcessoEPadraoConectado($idTpProcedimento, $padrao = 'N')
    {
        $objMdPetCriterioDTO = new MdPetCriterioDTO();
        $objMdPetCriterioRN = new MdPetCriterioRN();

        SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_intercorrente_criterio_consultar', __METHOD__, $objMdPetCriterioDTO);

        // Verifica se o processo possui critério intercorrente cadastrado
        $objMdPetCriterioDTO->retTodos();
        $objMdPetCriterioDTO->setNumIdTipoProcedimento($idTpProcedimento);
        $objMdPetCriterioDTO->setStrSinCriterioPadrao($padrao);
        $arrObjMdPetCriterioDTO = $objMdPetCriterioRN->listar($objMdPetCriterioDTO);
        return $arrObjMdPetCriterioDTO;
    }


}

?>