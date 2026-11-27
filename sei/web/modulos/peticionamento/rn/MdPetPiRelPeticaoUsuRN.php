<?php
/**
 * ANATEL
 *
 * Regras de negócio da liberação de acesso aos documentos Restritos de um Peticionamento Interno de Interesse Pessoal.
 * Só quem peticionou, na unidade de abertura, libera e remove acesso; ele mesmo não entra na tabela e nunca sai.
 *
 * 25/09/2026 - criado por emerson_b.goncalves - ANATEL
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetPiRelPeticaoUsuRN extends InfraRN
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
     * @param MdPetPiRelPeticaoUsuDTO $objDTO
     * @return MdPetPiRelPeticaoUsuDTO[]
     */
    protected function listarConectado(MdPetPiRelPeticaoUsuDTO $objDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_rel_peticao_usu_listar', __METHOD__, $objDTO);

            $objBD = new MdPetPiRelPeticaoUsuBD($this->getObjInfraIBanco());

            return $objBD->listar($objDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro listando usuários com acesso liberado no Peticionamento Interno.', $e);
        }
    }

    /**
     * Libera o acesso de um usuário da unidade de abertura aos documentos Restritos do peticionamento.
     *
     * @param MdPetPiRelPeticaoUsuDTO $objDTO com IdMdPetPiPeticao e IdUsuario
     * @return MdPetPiRelPeticaoUsuDTO
     */
    protected function cadastrarControlado(MdPetPiRelPeticaoUsuDTO $objDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_rel_peticao_usu_cadastrar', __METHOD__, $objDTO);

            $objInfraException = new InfraException();

            if (InfraString::isBolVazia($objDTO->getNumIdUsuario())) {
                $objInfraException->lancarValidacao('Informe o usuário para liberar o acesso.');
            }

            $objMdPetPiPeticaoDTO = $this->validarAutor($objDTO->getNumIdMdPetPiPeticao());

            if ($objDTO->getNumIdUsuario() == $objMdPetPiPeticaoDTO->getNumIdUsuario()) {
                $objInfraException->adicionarValidacao('O usuário que peticionou já tem acesso aos documentos.');
            }

            $objUnidadeDTO = new UnidadeDTO();
            $objUnidadeDTO->setNumIdUnidade($objMdPetPiPeticaoDTO->getNumIdUnidade());

            $arrIdUsuarioUnidade = InfraArray::converterArrInfraDTO((new UsuarioRN())->listarPorUnidadeRN0812($objUnidadeDTO), 'IdUsuario');

            if (!in_array($objDTO->getNumIdUsuario(), $arrIdUsuarioUnidade)) {
                $objInfraException->adicionarValidacao('Usuário não pertence à unidade de abertura do processo.');
            }

            $objBD = new MdPetPiRelPeticaoUsuBD($this->getObjInfraIBanco());

            $objDTOExistente = new MdPetPiRelPeticaoUsuDTO();
            $objDTOExistente->setNumIdMdPetPiPeticao($objDTO->getNumIdMdPetPiPeticao());
            $objDTOExistente->setNumIdUsuario($objDTO->getNumIdUsuario());

            if ($objBD->contar($objDTOExistente) > 0) {
                $objInfraException->adicionarValidacao('Usuário já tem acesso liberado.');
            }

            $objInfraException->lancarValidacoes();

            return $objBD->cadastrar($objDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro liberando acesso no Peticionamento Interno.', $e);
        }
    }

    /**
     * Remove liberações pela tela do autor. Cada item precisa ser de peticionamento do usuário da sessão.
     *
     * @param MdPetPiRelPeticaoUsuDTO[] $arrObjDTO
     * @return void
     */
    protected function excluirLiberacaoControlado($arrObjDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_rel_peticao_usu_excluir', __METHOD__, $arrObjDTO);

            $objBD = new MdPetPiRelPeticaoUsuBD($this->getObjInfraIBanco());

            foreach ($arrObjDTO as $objDTO) {
                $this->validarAutor($objDTO->getNumIdMdPetPiPeticao());
                $objBD->excluir($objDTO);
            }
        } catch (Exception $e) {
            throw new InfraException('Erro removendo acesso no Peticionamento Interno.', $e);
        }
    }

    /**
     * Exclusão sem conferir o autor, usada quando o registro do peticionamento sai junto com o processo.
     *
     * @param MdPetPiRelPeticaoUsuDTO[] $arrObjDTO
     * @return void
     */
    protected function excluirControlado($arrObjDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_rel_peticao_usu_excluir', __METHOD__, $arrObjDTO);

            $objBD = new MdPetPiRelPeticaoUsuBD($this->getObjInfraIBanco());

            foreach ($arrObjDTO as $objDTO) {
                $objBD->excluir($objDTO);
            }
        } catch (Exception $e) {
            throw new InfraException('Erro excluindo acesso liberado no Peticionamento Interno.', $e);
        }
    }

    /**
     * Liberações do usuário da sessão entre os peticionamentos informados no DTO.
     *
     * Helper interno, sem verificação de permissão: roda no evento de acesso a protocolo, em todo acesso do core,
     * onde não há ação de usuário a autorizar. Não abre transação, não grava e não envia mensagem.
     *
     * @param MdPetPiRelPeticaoUsuDTO $objDTO com IdMdPetPiPeticao informado por OPER_IN
     * @return MdPetPiRelPeticaoUsuDTO[]
     */
    protected function obterLiberacoesUsuarioAtualConectado(MdPetPiRelPeticaoUsuDTO $objDTO)
    {
        try {
            $objDTO->retNumIdMdPetPiPeticao();
            $objDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());

            $objBD = new MdPetPiRelPeticaoUsuBD($this->getObjInfraIBanco());

            return $objBD->listar($objDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro verificando o acesso liberado no Peticionamento Interno.', $e);
        }
    }

    /**
     * Confere que o peticionamento é de Interesse Pessoal, do usuário da sessão e da unidade atual.
     *
     * @param int|null $numIdMdPetPiPeticao
     * @return MdPetPiPeticaoDTO
     * @throws InfraException de validação quando a sessão não é do autor
     */
    private function validarAutor($numIdMdPetPiPeticao)
    {
        $objMdPetPiPeticaoDTO = null;

        if (!InfraString::isBolVazia($numIdMdPetPiPeticao)) {
            $objMdPetPiPeticaoDTO = new MdPetPiPeticaoDTO();
            $objMdPetPiPeticaoDTO->retNumIdUsuario();
            $objMdPetPiPeticaoDTO->retNumIdUnidade();
            $objMdPetPiPeticaoDTO->setNumIdMdPetPiPeticao($numIdMdPetPiPeticao);
            $objMdPetPiPeticaoDTO->setStrStaIndicacaoInteressado(MdPetPiTpProcessoRN::$II_INTERESSE_PESSOAL);
            $objMdPetPiPeticaoDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
            $objMdPetPiPeticaoDTO->setNumIdUnidade(SessaoSEI::getInstance()->getNumIdUnidadeAtual());

            $objMdPetPiPeticaoDTO = (new MdPetPiPeticaoRN())->consultar($objMdPetPiPeticaoDTO);
        }

        if ($objMdPetPiPeticaoDTO === null) {
            (new InfraException())->lancarValidacao('Somente quem peticionou, na unidade de abertura, controla o acesso aos documentos restritos deste processo.');
        }

        return $objMdPetPiPeticaoDTO;
    }
}
