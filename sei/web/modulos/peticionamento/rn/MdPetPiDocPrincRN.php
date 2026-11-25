<?php
/**
 * ANATEL
 *
 * Regras de negocio de o Documento Principal do Peticionamento Interno.
 * A escrita e sempre acionada por MdPetPiTpProcessoRN, dentro da transacao do Tipo de Processo.
 *
 * 08/09/2026 - criado por emerson_b.goncalves - ANATEL
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetPiDocPrincRN extends InfraRN
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
     * @param MdPetPiDocPrincDTO $objDTO
     * @return MdPetPiDocPrincDTO[]
     */
    protected function listarConectado(MdPetPiDocPrincDTO $objDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_doc_princ_listar', __METHOD__, $objDTO);

            $objBD = new MdPetPiDocPrincBD($this->getObjInfraIBanco());

            return $objBD->listar($objDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro listando o Documento Principal do Peticionamento Interno.', $e);
        }
    }

    /**
     * @param MdPetPiDocPrincDTO $objDTO
     * @return int
     */
    protected function contarConectado(MdPetPiDocPrincDTO $objDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_doc_princ_listar', __METHOD__, $objDTO);

            $objBD = new MdPetPiDocPrincBD($this->getObjInfraIBanco());

            return $objBD->contar($objDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro contando o Documento Principal do Peticionamento Interno.', $e);
        }
    }

    /**
     * @param MdPetPiDocPrincDTO $objDTO
     * @return MdPetPiDocPrincDTO|null
     */
    protected function consultarConectado(MdPetPiDocPrincDTO $objDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_doc_princ_consultar', __METHOD__, $objDTO);

            $objBD = new MdPetPiDocPrincBD($this->getObjInfraIBanco());

            $objRetornoDTO = $objBD->consultar($objDTO);

            if ($objRetornoDTO === null || !$objRetornoDTO->isSetStrStaAplicabilidade()
                || !$objRetornoDTO->isSetNumIdSerie()
                || $objRetornoDTO->getStrStaAplicabilidade() !== MdPetPiTpProcessoRN::$DOC_FORMULARIO) {
                return $objRetornoDTO;
            }

            // O formulario usado pelas telas vem da serie, sem persistir outra associacao administrativa.
            $objSerieDTO = new SerieDTO();
            $objSerieDTO->retNumIdSerie();
            $objSerieDTO->retNumIdTipoFormulario();
            $objSerieDTO->retStrNome();
            $objSerieDTO->setStrSinAtivo('S');
            $objSerieDTO->setStrStaAplicabilidade(SerieRN::$TA_FORMULARIO);

            if ($objRetornoDTO->getNumIdSerie() !== null) {
                $objSerieDTO->setNumIdSerie($objRetornoDTO->getNumIdSerie());
            } 
            // elseif ($objRetornoDTO->isSetNumIdTipoFormulario() && $objRetornoDTO->getNumIdTipoFormulario() !== null) {
            //     // Compatibilidade temporaria para cadastros antigos sem id_serie.
            //     $objSerieDTO->setNumIdTipoFormulario($objRetornoDTO->getNumIdTipoFormulario());
            // } 
            else {
                throw new InfraException('Tipo do Documento Principal não informado.');
            }
            $arrObjSerieDTO = (new SerieRN())->listarRN0646($objSerieDTO);

            if (count($arrObjSerieDTO) !== 1) {
                throw new InfraException('O Tipo de Formulário configurado no Documento Principal precisa corresponder a um único Tipo de Documento ativo.');
            }

            $objRetornoDTO->setNumIdSerie($arrObjSerieDTO[0]->getNumIdSerie());
            // $objRetornoDTO->setNumIdTipoFormulario($arrObjSerieDTO[0]->getNumIdTipoFormulario());
            $objRetornoDTO->setStrNomeSerie($arrObjSerieDTO[0]->getStrNome());

            return $objRetornoDTO;
        } catch (Exception $e) {
            throw new InfraException('Erro consultando o Documento Principal do Peticionamento Interno.', $e);
        }
    }

    /**
     * @param MdPetPiDocPrincDTO $objDTO
     * @return MdPetPiDocPrincDTO
     */
    protected function cadastrarControlado(MdPetPiDocPrincDTO $objDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_doc_princ_cadastrar', __METHOD__, $objDTO);

            // O identificador do formulario e apenas transitorio, obtido da serie na leitura.
            // $objDTO->unSetNumIdTipoFormulario();
            $objBD = new MdPetPiDocPrincBD($this->getObjInfraIBanco());

            return $objBD->cadastrar($objDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando o Documento Principal do Peticionamento Interno.', $e);
        }
    }

    /**
     * @param MdPetPiDocPrincDTO $objDTO
     * @return bool
     */
    protected function alterarControlado(MdPetPiDocPrincDTO $objDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_doc_princ_alterar', __METHOD__, $objDTO);

            $objBD = new MdPetPiDocPrincBD($this->getObjInfraIBanco());
            $objBD->alterar($objDTO);

            return true;
        } catch (Exception $e) {
            throw new InfraException('Erro alterando o Documento Principal do Peticionamento Interno.', $e);
        }
    }

    /**
     * @param MdPetPiDocPrincDTO[] $arrObjDTO
     * @return void
     */
    protected function excluirControlado($arrObjDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_doc_princ_excluir', __METHOD__, $arrObjDTO);

            $objBD = new MdPetPiDocPrincBD($this->getObjInfraIBanco());

            for ($i = 0; $i < count($arrObjDTO); $i++) {
                $objBD->excluir($arrObjDTO[$i]);
            }
        } catch (Exception $e) {
            throw new InfraException('Erro excluindo o Documento Principal do Peticionamento Interno.', $e);
        }
    }
}
