<?php
/**
 * ANATEL
 *
 * Regras de negocio das Orientacoes Gerais do Peticionamento Interno.
 * Registro unico, identificado por $ID_FIXO_ORIENTACOES.
 *
 * 03/09/2026 - criado por emerson_b.goncalves - ANATEL
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetPiOrientacaoRN extends InfraRN
{
    public static $ID_FIXO_ORIENTACOES = 1;

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    /**
     * @param MdPetPiOrientacaoDTO $objMdPetPiOrientacaoDTO
     * @return MdPetPiOrientacaoDTO|null
     */
    protected function consultarConectado(MdPetPiOrientacaoDTO $objMdPetPiOrientacaoDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_orientacao_consultar', __METHOD__, $objMdPetPiOrientacaoDTO);

            $objMdPetPiOrientacaoBD = new MdPetPiOrientacaoBD($this->getObjInfraIBanco());

            return $objMdPetPiOrientacaoBD->consultar($objMdPetPiOrientacaoDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro consultando Orientações Gerais do Peticionamento Interno.', $e);
        }
    }

    /**
     * @param MdPetPiOrientacaoDTO $objMdPetPiOrientacaoDTO
     * @return int
     */
    protected function contarConectado(MdPetPiOrientacaoDTO $objMdPetPiOrientacaoDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_orientacao_listar', __METHOD__, $objMdPetPiOrientacaoDTO);

            $objMdPetPiOrientacaoBD = new MdPetPiOrientacaoBD($this->getObjInfraIBanco());

            return $objMdPetPiOrientacaoBD->contar($objMdPetPiOrientacaoDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro contando Orientações Gerais do Peticionamento Interno.', $e);
        }
    }

    /**
     * Grava as Orientacoes Gerais, cadastrando o registro unico na primeira vez.
     *
     * @param MdPetPiOrientacaoDTO $objMdPetPiOrientacaoDTO
     * @return bool
     */
    protected function salvarControlado(MdPetPiOrientacaoDTO $objMdPetPiOrientacaoDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_orientacao_cadastrar', __METHOD__, $objMdPetPiOrientacaoDTO);

            $objInfraException = new InfraException();

            if (InfraString::isBolVazia($objMdPetPiOrientacaoDTO->getStrOrientacoesGerais())) {
                $objInfraException->adicionarValidacao('Orientações Gerais não informadas.');
            }

            $objInfraException->lancarValidacoes();

            $objMdPetPiOrientacaoDTO->setNumIdMdPetPiOrientacao(self::$ID_FIXO_ORIENTACOES);

            $objMdPetPiOrientacaoBD = new MdPetPiOrientacaoBD($this->getObjInfraIBanco());

            $objContagemDTO = new MdPetPiOrientacaoDTO();
            $objContagemDTO->setNumIdMdPetPiOrientacao(self::$ID_FIXO_ORIENTACOES);

            if ($objMdPetPiOrientacaoBD->contar($objContagemDTO) > 0) {
                $objMdPetPiOrientacaoBD->alterar($objMdPetPiOrientacaoDTO);
            } else {
                $objMdPetPiOrientacaoBD->cadastrar($objMdPetPiOrientacaoDTO);
            }

            return true;
        } catch (Exception $e) {
            throw new InfraException('Erro salvando Orientações Gerais do Peticionamento Interno.', $e);
        }
    }
}
