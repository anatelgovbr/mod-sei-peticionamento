<?php
require_once dirname(__FILE__) . '/../../../SEI.php';

/**
 * Created by PhpStorm.
 * User: jhon.carvalho
 * Date: 27/12/2017
 * Time: 09:52
 */
class MdPetVincTpProcessoRN extends InfraRN
{

    public static $ID_FIXO_MD_PET_VINCULO_USU_EXT = '1';

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    protected function cadastrarControlado(MdPetVincTpProcessoDTO $objMdPetVincTpProcessoDTO)
    {

        try {

            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_vinc_tp_processo_cadastrar', __METHOD__, $objMdPetVincTpProcessoDTO);

            if ($this->_validarExistenciaVinculoCadastrado() > 0) {

            	$objMdPetVincTpProcesso = $this->alterar($objMdPetVincTpProcessoDTO);

            } else {

                $objMdPetVincTpProcessoDTO->setNumIdMdPetVincTpProcesso(self::$ID_FIXO_MD_PET_VINCULO_USU_EXT);
                $objMdPetVincTpProcessoBD = new MdPetVincTpProcessoBD($this->getObjInfraIBanco());
                $objMdPetVincTpProcesso = $objMdPetVincTpProcessoBD->cadastrar($objMdPetVincTpProcessoDTO);
            }
            return $objMdPetVincTpProcesso;
        } catch (Exception $e) {
            throw  new InfraException('Erro cadastrando Tipo de Processo do Vínculo.', $e);
        }
    }

    private function _validarExistenciaVinculoCadastrado()
    {
        $objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();
        $objMdPetVincTpProcessoDTO->setNumIdMdPetVincTpProcesso(self::$ID_FIXO_MD_PET_VINCULO_USU_EXT);
        $objMdPetVincTpProcessoDTO->retNumIdMdPetVincTpProcesso();
        return $this->consultarConectado($objMdPetVincTpProcessoDTO);


    }


    protected function consultarConectado(MdPetVincTpProcessoDTO $objMdPetVincTpProcessoDTO)
    {
        try {

            // Valida Permissao
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_vinc_tp_processo_cadastrar', __METHOD__, $objMdPetVincTpProcessoDTO);


            $objMdPetVincTpProcessoBD = new MdPetVincTpProcessoBD($this->getObjInfraIBanco());
            $objMdPetVincTpProcesso = $objMdPetVincTpProcessoBD->consultar($objMdPetVincTpProcessoDTO);

            return $objMdPetVincTpProcesso;

        } catch (Exception $e) {
            throw new InfraException('Erro consultando Tipo de Processo do Vínculo.', $e);
        }
    }

    protected function listarConectado(MdPetVincTpProcessoDTO $objMdPetVincTpProcessoDTO)
    {
        try {
            // Valida Permissao
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_vinc_tp_processo_cadastrar', __METHOD__, $objMdPetVincTpProcessoDTO);


            $objMdPetVincTpProcessoBD = new MdPetVincTpProcessoBD($this->getObjInfraIBanco());
            $objMdPetVincTpProcesso = $objMdPetVincTpProcessoBD->listar($objMdPetVincTpProcessoDTO);

            return $objMdPetVincTpProcesso;

        } catch (Exception $e) {
            throw new InfraException('Erro listando Tipo de Processo do Vínculo.', $e);
        }
    }

    protected function contarConectado(MdPetVincTpProcessoDTO $objMdPetVincTpProcessoDTO)
    {
        try {

            //Valida Permissao

            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_vinc_tp_processo_cadastrar', __METHOD__, $objMdPetVincTpProcessoDTO);

            //Regras de Negocio
            //$objInfraException = new InfraException();

            //$objInfraException->lancarValidacoes();

            $objMdPetVincTpProcessoBD = new MdPetVincTpProcessoBD($this->getObjInfraIBanco());
            $ret = $objMdPetVincTpProcessoBD->contar($objMdPetVincTpProcessoDTO);


            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando Tipo de Processo do Vínculo.', $e);
        }
    }

    protected function alterarControlado($objMdPetVincTpProcessoDTO)
    {

        try {

            $objMdPetVincTpProcessoDTO->setNumIdMdPetVincTpProcesso(self::$ID_FIXO_MD_PET_VINCULO_USU_EXT);
            $objMdPetVincTpProcessoBD = new MdPetVincTpProcessoBD($this->getObjInfraIBanco());
            $objMdPetVincTpProcesso = $objMdPetVincTpProcessoBD->alterar($objMdPetVincTpProcessoDTO);
            return $objMdPetVincTpProcesso;
        } catch (Exception $e) {
            throw  new InfraException('Erro alterando Tipo de Processo do Vínculo.', $e);
        }
    }

}