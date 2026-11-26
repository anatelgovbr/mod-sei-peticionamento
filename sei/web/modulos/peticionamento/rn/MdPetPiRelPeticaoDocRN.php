<?php
/**
 * ANATEL
 *
 * Regras de negócio dos documentos que compuseram um Peticionamento Interno.
 *
 * 17/09/2026 - criado por emerson_b.goncalves - ANATEL
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetPiRelPeticaoDocRN extends InfraRN
{
    /** Papel do documento no peticionamento */
    public static $PAPEL_PRINCIPAL = 'P';
    public static $PAPEL_ANEXO_OBRIGATORIO = 'O';
    public static $PAPEL_MAIS_ANEXOS = 'M';
    public static $PAPEL_ADICIONAL_AUTOMATICO = 'A';

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    /**
     * @param MdPetPiRelPeticaoDocDTO $objDTO
     * @return MdPetPiRelPeticaoDocDTO[]
     */
    protected function listarConectado(MdPetPiRelPeticaoDocDTO $objDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_rel_peticao_doc_listar', __METHOD__, $objDTO);

            $objBD = new MdPetPiRelPeticaoDocBD($this->getObjInfraIBanco());

            return $objBD->listar($objDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro listando documentos do Peticionamento Interno.', $e);
        }
    }

    /**
     * @param MdPetPiRelPeticaoDocDTO $objDTO
     * @return int
     */
    protected function contarConectado(MdPetPiRelPeticaoDocDTO $objDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_rel_peticao_doc_listar', __METHOD__, $objDTO);

            $objBD = new MdPetPiRelPeticaoDocBD($this->getObjInfraIBanco());

            return $objBD->contar($objDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro contando documentos do Peticionamento Interno.', $e);
        }
    }

    /**
     * @param MdPetPiRelPeticaoDocDTO $objDTO
     * @return MdPetPiRelPeticaoDocDTO
     */
    protected function cadastrarControlado(MdPetPiRelPeticaoDocDTO $objDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_rel_peticao_doc_cadastrar', __METHOD__, $objDTO);

            $objInfraException = new InfraException();

            if (InfraString::isBolVazia($objDTO->getNumIdMdPetPiPeticao())) {
                $objInfraException->adicionarValidacao('Peticionamento Interno não informado.');
            }

            if (InfraString::isBolVazia($objDTO->getDblIdDocumento())) {
                $objInfraException->adicionarValidacao('Documento do Peticionamento Interno não informado.');
            }

            $arrPapeis = [self::$PAPEL_PRINCIPAL, self::$PAPEL_ANEXO_OBRIGATORIO, self::$PAPEL_MAIS_ANEXOS, self::$PAPEL_ADICIONAL_AUTOMATICO];

            if (!in_array($objDTO->getStrStaPapel(), $arrPapeis, true)) {
                $objInfraException->adicionarValidacao('Papel do documento no Peticionamento Interno inválido.');
            }

            $objInfraException->lancarValidacoes();

            $objBD = new MdPetPiRelPeticaoDocBD($this->getObjInfraIBanco());

            return $objBD->cadastrar($objDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando documento do Peticionamento Interno.', $e);
        }
    }

    /**
     * @param MdPetPiRelPeticaoDocDTO[] $arrObjDTO
     * @return void
     */
    protected function excluirControlado($arrObjDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_rel_peticao_doc_excluir', __METHOD__, $arrObjDTO);

            $objBD = new MdPetPiRelPeticaoDocBD($this->getObjInfraIBanco());

            for ($i = 0; $i < count($arrObjDTO); $i++) {
                $objBD->excluir($arrObjDTO[$i]);
            }
        } catch (Exception $e) {
            throw new InfraException('Erro excluindo documento do Peticionamento Interno.', $e);
        }
    }

    /**
     * Documentos com nível de acesso próprio Restrito, de Peticionamento Interno com indicação de Interesse Pessoal cuja unidade de abertura é a da sessão
     * cujo autor não é o usuário da sessão e que não liberou acesso a ele, entre os identificadores informados no DTO.
     *
     * Helper interno, sem verificação de permissão: roda no evento de acesso a protocolo, em todo acesso do core,
     * onde não há ação de usuário a autorizar. Não abre transação, não grava e não envia mensagem.
     *
     * @param MdPetPiRelPeticaoDocDTO $objDTO com IdDocumento informado por OPER_IN
     * @return MdPetPiRelPeticaoDocDTO[]
     */
    protected function obterDocumentosNegadosUnidadeAberturaConectado(MdPetPiRelPeticaoDocDTO $objDTO)
    {
        try {
            $objDTO->retDblIdDocumento();
            $objDTO->retNumIdMdPetPiPeticao();
            $objDTO->setStrStaNivelAcessoLocalProtocolo(ProtocoloRN::$NA_RESTRITO);
            $objDTO->setStrStaIndicacaoInteressadoPeticao(MdPetPiTpProcessoRN::$II_INTERESSE_PESSOAL);
            $objDTO->setNumIdUnidadePeticao(SessaoSEI::getInstance()->getNumIdUnidadeAtual());
            $objDTO->setNumIdUsuarioPeticao(SessaoSEI::getInstance()->getNumIdUsuario(), InfraDTO::$OPER_DIFERENTE);

            $objBD = new MdPetPiRelPeticaoDocBD($this->getObjInfraIBanco());

            $arrObjDTO = $objBD->listar($objDTO);

            if (count($arrObjDTO) == 0) {
                return $arrObjDTO;
            }

            $objMdPetPiRelPeticaoUsuDTO = new MdPetPiRelPeticaoUsuDTO();
            $objMdPetPiRelPeticaoUsuDTO->setNumIdMdPetPiPeticao(array_values(array_unique(InfraArray::converterArrInfraDTO($arrObjDTO, 'IdMdPetPiPeticao'))), InfraDTO::$OPER_IN);

            $arrIdPeticaoLiberada = InfraArray::converterArrInfraDTO((new MdPetPiRelPeticaoUsuRN())->obterLiberacoesUsuarioAtual($objMdPetPiRelPeticaoUsuDTO), 'IdMdPetPiPeticao');

            $ret = [];

            foreach ($arrObjDTO as $objDocumentoDTO) {
                if (!in_array($objDocumentoDTO->getNumIdMdPetPiPeticao(), $arrIdPeticaoLiberada)) {
                    $ret[] = $objDocumentoDTO;
                }
            }

            return $ret;
        } catch (Exception $e) {
            throw new InfraException('Erro verificando o acesso a documentos do Peticionamento Interno.', $e);
        }
    }

    /**
     * Papel no Peticionamento Interno dos documentos informados no DTO, para o ícone ao lado do documento na árvore.
     *
     * Helper interno, sem verificação de permissão: roda no evento de montagem da árvore do processo, onde não há
     * ação de usuário a autorizar. Não abre transação, não grava e não envia mensagem.
     *
     * @param MdPetPiRelPeticaoDocDTO $objDTO com IdDocumento informado por OPER_IN
     * @return MdPetPiRelPeticaoDocDTO[]
     */
    protected function obterPapeisDocumentosConectado(MdPetPiRelPeticaoDocDTO $objDTO)
    {
        try {
            $objDTO->retDblIdDocumento();
            $objDTO->retStrStaPapel();

            $objBD = new MdPetPiRelPeticaoDocBD($this->getObjInfraIBanco());

            return $objBD->listar($objDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro consultando o papel de documentos do Peticionamento Interno.', $e);
        }
    }
}
