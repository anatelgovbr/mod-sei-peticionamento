<?php
/**
 * ANATEL
 *
 * Regras de negocio de os Anexos de Arquivos Obrigatorios do Peticionamento Interno.
 * A escrita e sempre acionada por MdPetPiTpProcessoRN, dentro da transacao do Tipo de Processo.
 *
 * 08/09/2026 - criado por emerson_b.goncalves - ANATEL
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetPiAnexoObrigRN extends InfraRN
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    /**
     * @param MdPetPiAnexoObrigDTO $objDTO
     * @return MdPetPiAnexoObrigDTO[]
     */
    protected function listarConectado(MdPetPiAnexoObrigDTO $objDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_anexo_obrig_listar', __METHOD__, $objDTO);

            $objBD = new MdPetPiAnexoObrigBD($this->getObjInfraIBanco());

            return $objBD->listar($objDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro listando os Anexos de Arquivos Obrigatorios do Peticionamento Interno.', $e);
        }
    }

    /**
     * @param MdPetPiAnexoObrigDTO $objDTO
     * @return int
     */
    protected function contarConectado(MdPetPiAnexoObrigDTO $objDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_anexo_obrig_listar', __METHOD__, $objDTO);

            $objBD = new MdPetPiAnexoObrigBD($this->getObjInfraIBanco());

            return $objBD->contar($objDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro contando os Anexos de Arquivos Obrigatorios do Peticionamento Interno.', $e);
        }
    }

    /**
     * @param MdPetPiAnexoObrigDTO $objDTO
     * @return MdPetPiAnexoObrigDTO
     */
    protected function cadastrarControlado(MdPetPiAnexoObrigDTO $objDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_anexo_obrig_cadastrar', __METHOD__, $objDTO);

            $objBD = new MdPetPiAnexoObrigBD($this->getObjInfraIBanco());

            return $objBD->cadastrar($objDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando os Anexos de Arquivos Obrigatorios do Peticionamento Interno.', $e);
        }
    }

    /**
     * @param MdPetPiAnexoObrigDTO $objDTO
     * @return bool
     */
    protected function alterarControlado(MdPetPiAnexoObrigDTO $objDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_anexo_obrig_alterar', __METHOD__, $objDTO);

            $objBD = new MdPetPiAnexoObrigBD($this->getObjInfraIBanco());
            $objBD->alterar($objDTO);

            return true;
        } catch (Exception $e) {
            throw new InfraException('Erro alterando os Anexos de Arquivos Obrigatorios do Peticionamento Interno.', $e);
        }
    }

    /**
     * @param MdPetPiAnexoObrigDTO[] $arrObjDTO
     * @return void
     */
    protected function excluirControlado($arrObjDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_anexo_obrig_excluir', __METHOD__, $arrObjDTO);

            $objBD = new MdPetPiAnexoObrigBD($this->getObjInfraIBanco());

            for ($i = 0; $i < count($arrObjDTO); $i++) {
                $objBD->excluir($arrObjDTO[$i]);
            }
        } catch (Exception $e) {
            throw new InfraException('Erro excluindo os Anexos de Arquivos Obrigatorios do Peticionamento Interno.', $e);
        }
    }
}
