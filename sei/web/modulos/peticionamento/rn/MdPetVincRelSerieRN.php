<?php
require_once dirname(__FILE__) . '/../../../SEI.php';

/**
 * Created by PhpStorm.
 * User: jhon.carvalho
 * Date: 27/12/2017
 * Time: 10:36
 */
class MdPetVincRelSerieRN extends InfraRN
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    protected function cadastrarControlado($arrObjMdPetVincRelSerieDTO)
    {

        SessaoSEI::getInstance ()->validarAuditarPermissao ('md_pet_vinc_tp_processo_cadastrar', __METHOD__, $arrObjMdPetVincRelSerieDTO );

        try {
            if (is_array($arrObjMdPetVincRelSerieDTO)) {
                $arrRetorno = array();
                foreach ($arrObjMdPetVincRelSerieDTO as $chave => $objMdPetVincRelSerieDTO) {
                    if (is_a($objMdPetVincRelSerieDTO, 'MdPetVincRelSerieDTO')) {
                        $objMdPetVincRelSerieBD = new MdPetVincRelSerieBD($this->getObjInfraIBanco());
                        $arrRetorno[$chave] = $objMdPetVincRelSerieBD->cadastrar($objMdPetVincRelSerieDTO);
                    }
                }
            }
            return $arrRetorno;
        } catch (Exception $e) {
            throw  new InfraException('Erro cadastrando tipo documento relacionado ao tipo processo da vinculação.', $e);
        }
    }

    protected function excluirControlado($arrObjMdPetVincRelSerieDTO){

        // TODO refatorar a tela para retitar a exclusão desnecessária
//        SessaoSEI::getInstance ()->validarAuditarPermissao ('md_pet_vinc_tp_processo_cadastrar', __METHOD__, $arrObjMdPetVincRelSerieDTO );

        try {
            $objMdPetVincRelSerieBD = new MdPetVincRelSerieBD ($this->getObjInfraIBanco());
            for($i=0;$i<count($arrObjMdPetVincRelSerieDTO);$i++){
            	$objMdPetVincRelSerieBD->excluir($arrObjMdPetVincRelSerieDTO[$i]);
            }
        } catch (Exception $e) {
            throw  new InfraException('Erro excluindo tipo documento relacionado ao tipo processo da vinculação.', $e);
        }
    }

    protected function listarConectado(MdPetVincRelSerieDTO $objMdPetVincRelSerieDTO)
    {
        try {
            // Valida Permissao
            // SessaoSEI::getInstance ()->validarAuditarPermissao ('md_pet_vinc_tp_processo_cadastrar', __METHOD__, $objMdPetVincRelSerieDTO );

            $objMdPetVincRelSerieBD = new MdPetVincRelSerieBD($this->getObjInfraIBanco());
            $ret = $objMdPetVincRelSerieBD->listar($objMdPetVincRelSerieDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando tipo documento relacionado ao tipo processo da vinculação', $e);
        }
    }

    protected function contarConectado(MdPetVincRelSerieDTO $objMdPetVincRelSerieDTO)
    {
        try {
            // Valida Permissao
            // SessaoSEI::getInstance ()->validarAuditarPermissao ('md_pet_vinc_tp_processo_cadastrar', __METHOD__, $objMdPetVincRelSerieDTO );

            $objMdPetVincRelSerieBD = new MdPetVincRelSerieBD($this->getObjInfraIBanco());
            $ret = $objMdPetVincRelSerieBD->contar($objMdPetVincRelSerieDTO);

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro contando tipo documento relacionado ao tipo processo da vinculação', $e);
        }
    }

    private function _verificarExistenciaDocVinc($idMdPetVincTpProcesso)
    {

        $objMdPetVincRelSerieDTO = new MdPetVincRelSerieDTO();
        $objMdPetVincRelSerieDTO->setNumIdMdPetVincTpProcesso($idMdPetVincTpProcesso);
        $objMdPetVincRelSerieDTO->retNumIdMdPetVincRelSerie();

        return $this->listarConectado($objMdPetVincRelSerieDTO);

    }

    protected function buscarDocumentosObrigatoriosConectado(){
        $str      = '';
        $arrDados = array();
        $objMdPetVincRelSerieDTO = new MdPetVincRelSerieDTO();
        $objMdPetVincRelSerieDTO->retNumIdSerie();
        $objMdPetVincRelSerieDTO->retStrNomeSerie();
        $objMdPetVincRelSerieDTO->setStrSinObrigatorio('S');

        $arrObjMdPetVincRelSerieDTO = $this->listar($objMdPetVincRelSerieDTO);

        if(count($arrObjMdPetVincRelSerieDTO) > 0) {
            foreach ($arrObjMdPetVincRelSerieDTO as $objMdPetVincRelSerieDTO) {
                $arrDados[] = utf8_encode($objMdPetVincRelSerieDTO->getNumIdSerie().'__'.$objMdPetVincRelSerieDTO->getStrNomeSerie());
            }

            $str = json_encode($arrDados);
        }
        

      return $str;
    }
}
