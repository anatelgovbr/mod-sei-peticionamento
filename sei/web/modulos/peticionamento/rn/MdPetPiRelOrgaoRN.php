<?php
/**
 * ANATEL
 *
 * Regras de negocio de a disponibilizacao por Orgao do Peticionamento Interno.
 * A escrita e sempre acionada por MdPetPiTpProcessoRN, dentro da transacao do Tipo de Processo.
 *
 * 08/09/2026 - criado por emerson_b.goncalves - ANATEL
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetPiRelOrgaoRN extends InfraRN
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
     * @param MdPetPiRelOrgaoDTO $objDTO
     * @return MdPetPiRelOrgaoDTO[]
     */
    protected function listarConectado(MdPetPiRelOrgaoDTO $objDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_rel_orgao_listar', __METHOD__, $objDTO);

            $objBD = new MdPetPiRelOrgaoBD($this->getObjInfraIBanco());

            return $objBD->listar($objDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro listando a disponibilizacao por Orgao do Peticionamento Interno.', $e);
        }
    }

    /**
     * @param MdPetPiRelOrgaoDTO $objDTO
     * @return int
     */
    protected function contarConectado(MdPetPiRelOrgaoDTO $objDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_rel_orgao_listar', __METHOD__, $objDTO);

            $objBD = new MdPetPiRelOrgaoBD($this->getObjInfraIBanco());

            return $objBD->contar($objDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro contando a disponibilizacao por Orgao do Peticionamento Interno.', $e);
        }
    }

    /**
     * @param MdPetPiRelOrgaoDTO $objDTO
     * @return MdPetPiRelOrgaoDTO
     */
    protected function cadastrarControlado(MdPetPiRelOrgaoDTO $objDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_rel_orgao_cadastrar', __METHOD__, $objDTO);

            $objBD = new MdPetPiRelOrgaoBD($this->getObjInfraIBanco());

            return $objBD->cadastrar($objDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando a disponibilizacao por Orgao do Peticionamento Interno.', $e);
        }
    }

    /**
     * @param MdPetPiRelOrgaoDTO[] $arrObjDTO
     * @return void
     */
    protected function excluirControlado($arrObjDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_rel_orgao_excluir', __METHOD__, $arrObjDTO);

            $objBD = new MdPetPiRelOrgaoBD($this->getObjInfraIBanco());

            for ($i = 0; $i < count($arrObjDTO); $i++) {
                $objBD->excluir($arrObjDTO[$i]);
            }
        } catch (Exception $e) {
            throw new InfraException('Erro excluindo a disponibilizacao por Orgao do Peticionamento Interno.', $e);
        }
    }
}
