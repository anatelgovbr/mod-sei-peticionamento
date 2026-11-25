<?php
/**
 * ANATEL
 *
 * Regras de negocio de os Documentos Adicionais Gerados Automaticamente do Peticionamento Interno.
 * A escrita e sempre acionada por MdPetPiTpProcessoRN, dentro da transacao do Tipo de Processo.
 *
 * 08/09/2026 - criado por emerson_b.goncalves - ANATEL
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetPiDocAutoRN extends InfraRN
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
     * @param MdPetPiDocAutoDTO $objDTO
     * @return MdPetPiDocAutoDTO[]
     */
    protected function listarConectado(MdPetPiDocAutoDTO $objDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_doc_auto_listar', __METHOD__, $objDTO);

            $objBD = new MdPetPiDocAutoBD($this->getObjInfraIBanco());

            return $objBD->listar($objDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro listando os Documentos Adicionais Gerados Automaticamente do Peticionamento Interno.', $e);
        }
    }

    /**
     * @param MdPetPiDocAutoDTO $objDTO
     * @return int
     */
    protected function contarConectado(MdPetPiDocAutoDTO $objDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_doc_auto_listar', __METHOD__, $objDTO);

            $objBD = new MdPetPiDocAutoBD($this->getObjInfraIBanco());

            return $objBD->contar($objDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro contando os Documentos Adicionais Gerados Automaticamente do Peticionamento Interno.', $e);
        }
    }

    /**
     * @param MdPetPiDocAutoDTO $objDTO
     * @return MdPetPiDocAutoDTO
     */
    protected function cadastrarControlado(MdPetPiDocAutoDTO $objDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_doc_auto_cadastrar', __METHOD__, $objDTO);

            $objBD = new MdPetPiDocAutoBD($this->getObjInfraIBanco());

            return $objBD->cadastrar($objDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando os Documentos Adicionais Gerados Automaticamente do Peticionamento Interno.', $e);
        }
    }

    /**
     * @param MdPetPiDocAutoDTO $objDTO
     * @return bool
     */
    protected function alterarControlado(MdPetPiDocAutoDTO $objDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_doc_auto_alterar', __METHOD__, $objDTO);

            $objBD = new MdPetPiDocAutoBD($this->getObjInfraIBanco());
            $objBD->alterar($objDTO);

            return true;
        } catch (Exception $e) {
            throw new InfraException('Erro alterando os Documentos Adicionais Gerados Automaticamente do Peticionamento Interno.', $e);
        }
    }

    /**
     * @param MdPetPiDocAutoDTO[] $arrObjDTO
     * @return void
     */
    protected function excluirControlado($arrObjDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_doc_auto_excluir', __METHOD__, $arrObjDTO);

            $objBD = new MdPetPiDocAutoBD($this->getObjInfraIBanco());

            for ($i = 0; $i < count($arrObjDTO); $i++) {
                $objBD->excluir($arrObjDTO[$i]);
            }
        } catch (Exception $e) {
            throw new InfraException('Erro excluindo os Documentos Adicionais Gerados Automaticamente do Peticionamento Interno.', $e);
        }
    }
}
