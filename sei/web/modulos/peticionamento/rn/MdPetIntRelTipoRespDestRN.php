<?
/**
 * 
 *
 * 02/10/2016 - criado por CAST
 *
 * 
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntRelTipoRespDestRN extends InfraRN
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    protected function cadastrarControlado(MdPetIntRelTipoRespDestDTO $objMdPetIntRelTipoRespDestDTO)
    {
        try {

            //Valida Permissao
            SessaoSEI::getInstance()->validarPermissao('md_pet_int_aceite_cadastrar');

            $objMdPetIntRelTipoRespDestBD = new MdPetIntRelTipoRespDestBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntRelTipoRespDestBD->cadastrar($objMdPetIntRelTipoRespDestDTO);

            //Auditoria

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando .', $e);
        }
    }

    protected function alterarControlado(MdPetIntRelTipoRespDestDTO $objMdPetIntRelTipoRespDestDTO)
    {
        try {

            //Valida Permissao
//            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_int_tipo_resp_alterar',__METHOD__,$objMdPetIntTipoRespDTO);

            $objMdPetIntRelTipoRespDestBD = new MdPetIntRelTipoRespDestBD($this->getObjInfraIBanco());
            $objMdPetIntRelTipoRespDestBD->alterar($objMdPetIntRelTipoRespDestDTO);

            //Auditoria

        } catch (Exception $e) {
            throw new InfraException('Erro alterando .', $e);
        }
    }

    protected function consultarConectado(MdPetIntRelTipoRespDestDTO $objMdPetIntRelTipoRespDestDTO)
    {
        try {

            //Valida Permissao
//            SessaoSEI::getInstance()->validarPermissao('');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntRelTipoRespDestBD = new MdPetIntRelTipoRespDestBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntRelTipoRespDestBD->consultar($objMdPetIntRelTipoRespDestDTO);

            //Auditoria

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando .', $e);
        }
    }

    protected function listarConectado(MdPetIntRelTipoRespDestDTO $objMdPetIntRelTipoRespDestDTO)
    {
        try {

            //Valida Permissao
//            SessaoSEI::getInstance()->validarPermissao('md_pet_int_tipo_resp_listar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntRelTipoRespDestBD = new MdPetIntRelTipoRespDestBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntRelTipoRespDestBD->listar($objMdPetIntRelTipoRespDestDTO);

            //Auditoria

            return $ret;

        } catch (Exception $e) {
            throw new InfraException('Erro listando .', $e);
        }
    }

    protected function contarConectado(MdPetIntRelTipoRespDestDTO $objMdPetIntRelTipoRespDestDTO)
    {
        try {

            //Valida Permissao
//            SessaoSEI::getInstance()->validarPermissao('md_pet_int_tipo_resp_listar');

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetIntRelTipoRespDestBD = new MdPetIntRelTipoRespDestBD($this->getObjInfraIBanco());
            $ret = $objMdPetIntRelTipoRespDestBD->contar($objMdPetIntRelTipoRespDestDTO);
        	
            //Auditoria

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando .', $e);
        }
    }

    protected function retornaObjTipoRespostaDestPorIdsConectado($idsTipoRespDest)
    {
        $arrObjDTO      = array();
        $idsTipoRespDest = array_unique($idsTipoRespDest);

        if(count($idsTipoRespDest) > 0)
        {
            $objMdPetIntRelTpRespDTO = new MdPetIntRelTipoRespDestDTO();
            $objMdPetIntRelTpRespDTO->setNumIdMdPetIntTipoRespDest($idsTipoRespDest, InfraDTO::$OPER_IN);
            $objMdPetIntRelTpRespDTO->retDthDataProrrogada();
            $objMdPetIntRelTpRespDTO->retDthDataLimite();
            $objMdPetIntRelTpRespDTO->adicionarCriterio(array('DataFim','DataFim'),
                array(InfraDTO::$OPER_MAIOR_IGUAL,InfraDTO::$OPER_IGUAL),
                array(InfraData::getStrDataAtual(),null),
                InfraDTO::$OPER_LOGICO_OR);
            $arrObjDTO = $this->listar($objMdPetIntRelTpRespDTO);
        }

        return $arrObjDTO;
    }
	
}

?>