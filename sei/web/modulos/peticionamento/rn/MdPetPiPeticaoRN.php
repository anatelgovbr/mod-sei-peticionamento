<?php
/**
 * ANATEL
 *
 * Regras de negócio do registro do Peticionamento Interno concluído.
 *
 * 17/09/2026 - criado por emerson_b.goncalves - ANATEL
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetPiPeticaoRN extends InfraRN
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
     * @param MdPetPiPeticaoDTO $objMdPetPiPeticaoDTO
     * @return MdPetPiPeticaoDTO[]
     */
    protected function listarConectado(MdPetPiPeticaoDTO $objMdPetPiPeticaoDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_peticao_listar', __METHOD__, $objMdPetPiPeticaoDTO);

            $objMdPetPiPeticaoBD = new MdPetPiPeticaoBD($this->getObjInfraIBanco());

            return $objMdPetPiPeticaoBD->listar($objMdPetPiPeticaoDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro listando Peticionamento Interno.', $e);
        }
    }

    /**
     * @param MdPetPiPeticaoDTO $objMdPetPiPeticaoDTO
     * @return MdPetPiPeticaoDTO|null
     */
    protected function consultarConectado(MdPetPiPeticaoDTO $objMdPetPiPeticaoDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_peticao_consultar', __METHOD__, $objMdPetPiPeticaoDTO);

            $objMdPetPiPeticaoBD = new MdPetPiPeticaoBD($this->getObjInfraIBanco());

            return $objMdPetPiPeticaoBD->consultar($objMdPetPiPeticaoDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro consultando Peticionamento Interno.', $e);
        }
    }

    /**
     * @param MdPetPiPeticaoDTO $objMdPetPiPeticaoDTO
     * @return int
     */
    protected function contarConectado(MdPetPiPeticaoDTO $objMdPetPiPeticaoDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_peticao_listar', __METHOD__, $objMdPetPiPeticaoDTO);

            $objMdPetPiPeticaoBD = new MdPetPiPeticaoBD($this->getObjInfraIBanco());

            return $objMdPetPiPeticaoBD->contar($objMdPetPiPeticaoDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro contando Peticionamento Interno.', $e);
        }
    }

    /**
     * Exclui o registro do peticionamento e, antes, os documentos associados a ele.
     *
     * @param MdPetPiPeticaoDTO[] $arrObjMdPetPiPeticaoDTO
     * @return void
     */
    protected function excluirControlado($arrObjMdPetPiPeticaoDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_peticao_excluir', __METHOD__, $arrObjMdPetPiPeticaoDTO);

            $objMdPetPiRelPeticaoDocRN = new MdPetPiRelPeticaoDocRN();
            $objMdPetPiRelPeticaoUsuRN = new MdPetPiRelPeticaoUsuRN();
            $objMdPetPiPeticaoBD = new MdPetPiPeticaoBD($this->getObjInfraIBanco());

            for ($i = 0; $i < count($arrObjMdPetPiPeticaoDTO); $i++) {
                $objMdPetPiRelPeticaoDocDTO = new MdPetPiRelPeticaoDocDTO();
                $objMdPetPiRelPeticaoDocDTO->retNumIdMdPetPiPeticao();
                $objMdPetPiRelPeticaoDocDTO->retDblIdDocumento();
                $objMdPetPiRelPeticaoDocDTO->setNumIdMdPetPiPeticao($arrObjMdPetPiPeticaoDTO[$i]->getNumIdMdPetPiPeticao());

                $arrObjMdPetPiRelPeticaoDocDTO = $objMdPetPiRelPeticaoDocRN->listar($objMdPetPiRelPeticaoDocDTO);

                if (count($arrObjMdPetPiRelPeticaoDocDTO) > 0) {
                    $objMdPetPiRelPeticaoDocRN->excluir($arrObjMdPetPiRelPeticaoDocDTO);
                }

                $objMdPetPiRelPeticaoUsuDTO = new MdPetPiRelPeticaoUsuDTO();
                $objMdPetPiRelPeticaoUsuDTO->retNumIdMdPetPiPeticao();
                $objMdPetPiRelPeticaoUsuDTO->retNumIdUsuario();
                $objMdPetPiRelPeticaoUsuDTO->setNumIdMdPetPiPeticao($arrObjMdPetPiPeticaoDTO[$i]->getNumIdMdPetPiPeticao());

                $arrObjMdPetPiRelPeticaoUsuDTO = $objMdPetPiRelPeticaoUsuRN->listar($objMdPetPiRelPeticaoUsuDTO);

                if (count($arrObjMdPetPiRelPeticaoUsuDTO) > 0) {
                    $objMdPetPiRelPeticaoUsuRN->excluir($arrObjMdPetPiRelPeticaoUsuDTO);
                }

                $objMdPetPiPeticaoBD->excluir($arrObjMdPetPiPeticaoDTO[$i]);
            }
        } catch (Exception $e) {
            throw new InfraException('Erro excluindo Peticionamento Interno.', $e);
        }
    }

    /**
     * Conclui o Peticionamento Interno: abre o processo, inclui os documentos da tela, gera os Documentos Adicionais
     * Gerados Automáticos configurados, grava o registro do peticionamento e assina por senha o Documento Principal
     * Gerado ou Formulário. Tudo na mesma transação: senha errada desfaz a conclusão inteira.
     * A indexação da assinatura roda depois do commit, por indexarDocumentoAssinado.
     *
     * @param MdPetPiPeticaoDTO $objMdPetPiPeticaoDTO
     * @return MdPetPiPeticaoDTO com o processo gerado e os documentos criados
     */
    protected function cadastrarControlado(MdPetPiPeticaoDTO $objMdPetPiPeticaoDTO)
    {
        try {
            $objMdPetPiPeticaoDTOAuditoria = clone $objMdPetPiPeticaoDTO;

            if ($objMdPetPiPeticaoDTOAuditoria->isSetStrSenhaAssinatura()) {
                $objMdPetPiPeticaoDTOAuditoria->unSetStrSenhaAssinatura();
            }

            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_peticao_cadastrar', __METHOD__, $objMdPetPiPeticaoDTOAuditoria);

            $objMdPetPiTpProcessoDTO = $this->validarPeticionamento($objMdPetPiPeticaoDTO);
            $this->validarXssPeticao($objMdPetPiPeticaoDTO);
            $objMdPetPiPeticaoDTO->setDblIdDocumentoAssinado(null);

            LimiteSEI::getInstance()->configurarNivel3();

            $numIdUnidade = SessaoSEI::getInstance()->getNumIdUnidadeAtual();
            $numIdUsuario = SessaoSEI::getInstance()->getNumIdUsuario();

            $objInteressadoAPI = $this->montarInteressado($objMdPetPiTpProcessoDTO->getStrStaIndicacaoInteressado(), $numIdUnidade);
            $objSaidaGerarProcedimentoAPI = $this->gerarProcedimento($objMdPetPiPeticaoDTO, $objMdPetPiTpProcessoDTO, $numIdUnidade, $objInteressadoAPI);

            $dblIdProcedimento = $objSaidaGerarProcedimentoAPI->getIdProcedimento();

            $arrObjDocumentoDTO = $objMdPetPiPeticaoDTO->getArrObjMdPetPiRelPeticaoDocDTO();
            $arrIdSerieGerados = [];

            foreach ($arrObjDocumentoDTO as $objDocumentoDTO) {
                if ($objDocumentoDTO->getStrNomeArquivoUpload() === '') {
                    $objDocumentoDTO->setNumIdSerie($this->obterIdSerieDocumentoGerado($objDocumentoDTO));
                    $arrIdSerieGerados[] = $objDocumentoDTO->getNumIdSerie();
                }
            }

            $arrIdSerieComInteressados = $this->listarSeriesComInteressados($arrIdSerieGerados);

            foreach ($arrObjDocumentoDTO as $objDocumentoDTO) {
                $objDocumentoDTO->setDblIdDocumento($this->criarDocumentoNoProcesso($objDocumentoDTO, $dblIdProcedimento, $objInteressadoAPI, $arrIdSerieComInteressados));
            }

            foreach ($this->gerarDocumentosAutomaticos($objMdPetPiPeticaoDTO->getNumIdMdPetPiTpProcesso(), $dblIdProcedimento, $objInteressadoAPI) as $objDocumentoDTO) {
                $arrObjDocumentoDTO[] = $objDocumentoDTO;
            }

            $objMdPetPiPeticaoDTO->setDblIdProcedimento($dblIdProcedimento);
            $objMdPetPiPeticaoDTO->setNumIdUsuario($numIdUsuario);
            $objMdPetPiPeticaoDTO->setNumIdUnidade($numIdUnidade);
            $objMdPetPiPeticaoDTO->setStrStaIndicacaoInteressado($objMdPetPiTpProcessoDTO->getStrStaIndicacaoInteressado());
            $objMdPetPiPeticaoDTO->setDthPeticionamento(InfraData::getStrDataHoraAtual());

            $objMdPetPiPeticaoBD = new MdPetPiPeticaoBD($this->getObjInfraIBanco());
            $objMdPetPiPeticaoDTO = $objMdPetPiPeticaoBD->cadastrar($objMdPetPiPeticaoDTO);

            $objMdPetPiRelPeticaoDocRN = new MdPetPiRelPeticaoDocRN();

            foreach ($arrObjDocumentoDTO as $objDocumentoDTO) {
                $objDocumentoDTO->setNumIdMdPetPiPeticao($objMdPetPiPeticaoDTO->getNumIdMdPetPiPeticao());
                $objMdPetPiRelPeticaoDocRN->cadastrar($objDocumentoDTO);
            }

            $objMdPetPiPeticaoDTO->setArrObjMdPetPiRelPeticaoDocDTO($arrObjDocumentoDTO);

            foreach ($arrObjDocumentoDTO as $objDocumentoDTO) {
                if ($objDocumentoDTO->getStrStaPapel() === MdPetPiRelPeticaoDocRN::$PAPEL_PRINCIPAL && $objDocumentoDTO->getStrNomeArquivoUpload() === '') {
                    $this->assinarDocumentoPrincipal($objDocumentoDTO, $objMdPetPiPeticaoDTO->getStrCargoFuncaoAssinatura(), $objMdPetPiPeticaoDTO->getStrSenhaAssinatura());
                    $objMdPetPiPeticaoDTO->setDblIdDocumentoAssinado($objDocumentoDTO->getDblIdDocumento());
                }
            }

            if ($objMdPetPiPeticaoDTO->isSetStrSenhaAssinatura()) {
                $objMdPetPiPeticaoDTO->unSetStrSenhaAssinatura();
            }

            return $objMdPetPiPeticaoDTO;
        } catch (Exception $e) {
            throw new InfraException('Erro concluindo Peticionamento Interno.', $e);
        }
    }

    /**
     * Confere de novo, no servidor, a configuração, os metadados e os documentos obrigatórios.
     *
     * @param MdPetPiPeticaoDTO $objMdPetPiPeticaoDTO
     * @return MdPetPiTpProcessoDTO configuração conferida
     */
    private function validarPeticionamento(MdPetPiPeticaoDTO $objMdPetPiPeticaoDTO)
    {
        $objInfraException = new InfraException();

        $objMdPetPiTpProcessoDTO = new MdPetPiTpProcessoDTO();
        $objMdPetPiTpProcessoDTO->retTodos();
        $objMdPetPiTpProcessoDTO->setNumIdMdPetPiTpProcesso($objMdPetPiPeticaoDTO->getNumIdMdPetPiTpProcesso());

        $arrObjMdPetPiTpProcessoDTO = (new MdPetPiTpProcessoRN())->listarDisponiveis($objMdPetPiTpProcessoDTO);

        if (count($arrObjMdPetPiTpProcessoDTO) === 0) {
            $objInfraException->lancarValidacao('Tipo de Processo não disponível para Peticionamento Interno.');
        }

        $objMdPetPiTpProcessoDTO = $arrObjMdPetPiTpProcessoDTO[0];

        $strEspecificacao = trim((string) $objMdPetPiPeticaoDTO->getStrEspecificacao());

        if ($strEspecificacao === '') {
            $objInfraException->adicionarValidacao('Informe a Especificação.');
        }

        if (strlen($strEspecificacao) > 100) {
            $objInfraException->adicionarValidacao('Especificação deve ter no máximo 100 caracteres.');
        }

        $objMdPetPiPeticaoDTO->setStrEspecificacao($strEspecificacao);

        $numIdTipoPrioridade = $objMdPetPiPeticaoDTO->getNumIdTipoPrioridade();

        if ($numIdTipoPrioridade !== null) {

            $objTipoPrioridadeDTO = new TipoPrioridadeDTO();
            $objTipoPrioridadeDTO->setNumIdTipoPrioridade($numIdTipoPrioridade);

            if ((new TipoPrioridadeRN())->contar($objTipoPrioridadeDTO) !== 1) {
                $objInfraException->adicionarValidacao('Prioridade informada não está ativa.');
            }
        }

        if (!in_array(ProtocoloRN::$NA_PUBLICO, $this->listarNiveisAcessoPermitidos($objMdPetPiTpProcessoDTO->getNumIdTipoProcedimento()), true)) {
            $objInfraException->adicionarValidacao('O Tipo de Processo não permite o nível de acesso Público, exigido pelo Peticionamento Interno.');
        }

        $arrObjDocumentoDTO = $objMdPetPiPeticaoDTO->getArrObjMdPetPiRelPeticaoDocDTO();
        $arrIdSeriePorPapel = [];

        foreach ($arrObjDocumentoDTO as $objDocumentoDTO) {
            $arrIdSeriePorPapel[$objDocumentoDTO->getStrStaPapel()][] = (int) $objDocumentoDTO->getNumIdSerie();
        }

        if (!isset($arrIdSeriePorPapel[MdPetPiRelPeticaoDocRN::$PAPEL_PRINCIPAL])) {
            $objInfraException->adicionarValidacao('Informe o Documento Principal do peticionamento.');
        }

        foreach ($arrObjDocumentoDTO as $objDocumentoDTO) {
            if ($objDocumentoDTO->getStrStaPapel() === MdPetPiRelPeticaoDocRN::$PAPEL_PRINCIPAL && $objDocumentoDTO->getStrNomeArquivoUpload() === '') {
                if (!SessaoSEI::getInstance()->verificarPermissao('documento_assinar')) {
                    $objInfraException->adicionarValidacao('Seu perfil não permite assinar documentos, e o Documento Principal deste Tipo de Processo é assinado na confirmação do peticionamento.');
                } else {
                    if (InfraString::isBolVazia($objMdPetPiPeticaoDTO->getStrCargoFuncaoAssinatura())) {
                        $objInfraException->adicionarValidacao('Informe o Cargo/Função para assinar o Documento Principal.');
                    }

                    if ((string) $objMdPetPiPeticaoDTO->getStrSenhaAssinatura() === '') {
                        $objInfraException->adicionarValidacao('Informe a senha para assinar o Documento Principal.');
                    }
                }
            }
        }

        $arrIdSerieAnexados = isset($arrIdSeriePorPapel[MdPetPiRelPeticaoDocRN::$PAPEL_ANEXO_OBRIGATORIO])
            ? $arrIdSeriePorPapel[MdPetPiRelPeticaoDocRN::$PAPEL_ANEXO_OBRIGATORIO]
            : [];

        foreach (MdPetPiPeticaoINT::listarAnexosObrigatorios($objMdPetPiTpProcessoDTO->getNumIdMdPetPiTpProcesso()) as $objAnexoObrigDTO) {
            if (!in_array((int) $objAnexoObrigDTO->getNumIdSerie(), $arrIdSerieAnexados, true)) {
                $objInfraException->adicionarValidacao('Informe ao menos um documento do Anexo Obrigatório "' . $objAnexoObrigDTO->getStrNomeSerie() . '".');
            }
        }

        $objInfraException->lancarValidacoes();

        return $objMdPetPiTpProcessoDTO;
    }

    /**
     * ADR-PET-005: passa pelo filtro XSS do core a especificação, o cargo ou função da assinatura e, de cada documento, o
     * nome do arquivo, o complemento e os campos de formulário, e confere o texto do editor do Documento Principal e os
     * arquivos enviados com as regras do salvar no editor e do upload, antes de abrir o processo.
     *
     * @param MdPetPiPeticaoDTO $objMdPetPiPeticaoDTO documentos montados por MdPetPiPeticaoINT::montarDocumentosPeticionamento
     * @param string $strDiretorio diretório dos arquivos temporários
     * @return void
     * @throws InfraException validação para o usuário quando o conteúdo não é permitido
     */
    public function validarXssPeticao(MdPetPiPeticaoDTO $objMdPetPiPeticaoDTO, string $strDiretorio = DIR_SEI_TEMP): void
    {
        $objInfraException = new InfraException();

        PeticionamentoIntegracao::validarXssEspecificacao($objMdPetPiPeticaoDTO->getStrEspecificacao(), $objInfraException);
        PeticionamentoIntegracao::validarXssCampo($objMdPetPiPeticaoDTO->getStrCargoFuncaoAssinatura(), 'Cargo/Função', $objInfraException);

        $strConteudoEditor = null;
        $arrArquivos = [];

        foreach ($objMdPetPiPeticaoDTO->getArrObjMdPetPiRelPeticaoDocDTO() as $objDocumentoDTO) {
            if ($objDocumentoDTO->getStrNomeArquivoUpload() !== '') {
                PeticionamentoIntegracao::validarXssCampo($objDocumentoDTO->getStrNomeArquivo(), 'Nome de arquivo enviado', $objInfraException);
                PeticionamentoIntegracao::validarXssComplemento($objDocumentoDTO->getStrComplemento(), $objInfraException);
                $arrArquivos[$objDocumentoDTO->getStrNomeArquivoUpload()] = $objDocumentoDTO->getStrNomeArquivo();
                continue;
            }

            foreach ($objDocumentoDTO->getArrCamposFormulario() as $strNomeCampo => $strValor) {
                PeticionamentoIntegracao::validarXssCampo($strValor, '"' . $strNomeCampo . '" do Documento Principal', $objInfraException);
            }

            if ($objDocumentoDTO->getStrStaPapel() === MdPetPiRelPeticaoDocRN::$PAPEL_PRINCIPAL) {
                $strConteudoEditor = implode('', $objDocumentoDTO->getArrConteudoSecoes()) . $objDocumentoDTO->getStrConteudo();
            }
        }

        $objInfraException->lancarValidacoes();

        PeticionamentoIntegracao::validarXssConclusao($strConteudoEditor, $arrArquivos, $strDiretorio);
        PeticionamentoIntegracao::validarNomesAnexosConclusao($arrArquivos);
        PeticionamentoIntegracao::validarTamanhoConclusao($arrArquivos, $strDiretorio);
    }

    /**
     * Assina por senha o Documento Principal recém-criado, pela assinatura do core, dentro da transação da conclusão.
     * Documento do editor sem seção de assinatura bloqueia a conclusão, porque o modelo do tipo de documento está errado.
     *
     * @param MdPetPiRelPeticaoDocDTO $objDocumentoDTO Documento Principal já criado no processo
     * @param string $strCargoFuncao
     * @param string $strSenha
     * @return void
     */
    private function assinarDocumentoPrincipal(MdPetPiRelPeticaoDocDTO $objDocumentoDTO, $strCargoFuncao, $strSenha)
    {
        if (count($objDocumentoDTO->getArrCamposFormulario()) == 0) {
            $objSecaoDocumentoDTO = new SecaoDocumentoDTO();
            $objSecaoDocumentoDTO->setDblIdDocumento($objDocumentoDTO->getDblIdDocumento());
            $objSecaoDocumentoDTO->setStrSinAssinatura('S');

            if ((new SecaoDocumentoRN())->contar($objSecaoDocumentoDTO) == 0) {
                (new InfraException())->lancarValidacao('O modelo do Documento Principal não tem seção de assinatura. Procure a administração do Peticionamento Interno para corrigir o Tipo de Documento.');
            }
        }

        $objDocumentoAssinaturaDTO = new DocumentoDTO();
        $objDocumentoAssinaturaDTO->setDblIdDocumento($objDocumentoDTO->getDblIdDocumento());

        $objAssinaturaDTO = new AssinaturaDTO();
        $objAssinaturaDTO->setStrStaFormaAutenticacao(AssinaturaRN::$TA_SENHA);
        $objAssinaturaDTO->setNumIdOrgaoUsuario(SessaoSEI::getInstance()->getNumIdOrgaoUsuario());
        $objAssinaturaDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
        $objAssinaturaDTO->setStrSenhaUsuario($strSenha);
        $objAssinaturaDTO->setStrCargoFuncao($strCargoFuncao);
        $objAssinaturaDTO->setArrObjDocumentoDTO([$objDocumentoAssinaturaDTO]);

        (new DocumentoRN())->assinarInterno($objAssinaturaDTO);
    }

    /**
     * Indexa o Documento Principal assinado na conclusão. Roda depois do commit, como o DocumentoRN::assinar do core,
     * porque a indexação não entra na transação.
     *
     * @param MdPetPiPeticaoDTO $objMdPetPiPeticaoDTO retorno de cadastrar
     * @return void
     */
    public function indexarDocumentoAssinado(MdPetPiPeticaoDTO $objMdPetPiPeticaoDTO)
    {
        if (!$objMdPetPiPeticaoDTO->isSetDblIdDocumentoAssinado() || $objMdPetPiPeticaoDTO->getDblIdDocumentoAssinado() === null) {
            return;
        }

        $objIndexacaoDTO = new IndexacaoDTO();
        $objIndexacaoDTO->setArrIdProtocolos([$objMdPetPiPeticaoDTO->getDblIdDocumentoAssinado()]);
        $objIndexacaoDTO->setStrStaOperacao(IndexacaoRN::$TO_PROTOCOLO_ASSINATURA);

        (new IndexacaoRN())->indexarProtocolo($objIndexacaoDTO);
    }

    /**
     * Abre o processo com nível de acesso Público na unidade atual, com os metadados da tela e o interessado da
     * indicação configurada. As classificações por assunto do tipo são acrescentadas pelo próprio core.
     *
     * @param MdPetPiPeticaoDTO $objMdPetPiPeticaoDTO
     * @param MdPetPiTpProcessoDTO $objMdPetPiTpProcessoDTO
     * @param int $numIdUnidade
     * @param ContatoAPI $objInteressadoAPI mesmo contato utilizado nos documentos
     * @return SaidaGerarProcedimentoAPI
     */
    private function gerarProcedimento(MdPetPiPeticaoDTO $objMdPetPiPeticaoDTO, MdPetPiTpProcessoDTO $objMdPetPiTpProcessoDTO, $numIdUnidade, ContatoAPI $objInteressadoAPI)
    {
        $objProcedimentoAPI = new ProcedimentoAPI();
        $objProcedimentoAPI->setIdTipoProcedimento($objMdPetPiTpProcessoDTO->getNumIdTipoProcedimento());
        $objProcedimentoAPI->setIdUnidadeGeradora($numIdUnidade);
        $objProcedimentoAPI->setEspecificacao($objMdPetPiPeticaoDTO->getStrEspecificacao());
        $objProcedimentoAPI->setNumeroProtocolo('');
        $objProcedimentoAPI->setNivelAcesso(ProtocoloRN::$NA_PUBLICO);
        $objProcedimentoAPI->setIdHipoteseLegal(null);
        $objProcedimentoAPI->setInteressados([$objInteressadoAPI]);

        if ($objMdPetPiPeticaoDTO->getNumIdTipoPrioridade() !== null) {
            $objProcedimentoAPI->setIdTipoPrioridade($objMdPetPiPeticaoDTO->getNumIdTipoPrioridade());
        }

        $objEntradaGerarProcedimentoAPI = new EntradaGerarProcedimentoAPI();
        $objEntradaGerarProcedimentoAPI->setProcedimento($objProcedimentoAPI);

        return (new SeiRN())->gerarProcedimento($objEntradaGerarProcedimentoAPI);
    }

    /**
     * Interessado do processo: o contato do usuário logado na indicação P e o da unidade logada na indicação U.
     * A indicação vem sempre da configuração, nunca da tela.
     *
     * @param string $strStaIndicacaoInteressado
     * @param int $numIdUnidade
     * @return ContatoAPI
     */
    private function montarInteressado($strStaIndicacaoInteressado, $numIdUnidade)
    {
        if ($strStaIndicacaoInteressado === MdPetPiTpProcessoRN::$II_INTERESSE_UNIDADE) {

            $objUnidadeDTO = new UnidadeDTO();
            $objUnidadeDTO->retNumIdContato();
            $objUnidadeDTO->setNumIdUnidade($numIdUnidade);

            $objUnidadeDTO = (new UnidadeRN())->consultarRN0125($objUnidadeDTO);

            if ($objUnidadeDTO === null) {
                throw new InfraException('Unidade atual não encontrada para o interessado do processo.');
            }

            $numIdContato = $objUnidadeDTO->getNumIdContato();

        } else {

            $objUsuarioDTO = new UsuarioDTO();
            $objUsuarioDTO->retNumIdContato();
            $objUsuarioDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());

            $objUsuarioDTO = (new UsuarioRN())->consultarRN0489($objUsuarioDTO);

            if ($objUsuarioDTO === null) {
                throw new InfraException('Usuário logado não encontrado para o interessado do processo.');
            }

            $numIdContato = $objUsuarioDTO->getNumIdContato();
        }

        $objContatoDTO = new ContatoDTO();
        $objContatoDTO->retNumIdContato();
        $objContatoDTO->retStrSigla();
        $objContatoDTO->retStrNome();
        $objContatoDTO->setNumIdContato($numIdContato);

        $objContatoDTO = (new ContatoRN())->consultarRN0324($objContatoDTO);

        if ($objContatoDTO === null) {
            throw new InfraException('Contato do interessado do processo não encontrado.');
        }

        $objContatoAPI = new ContatoAPI();
        $objContatoAPI->setIdContato($objContatoDTO->getNumIdContato());
        $objContatoAPI->setSigla($objContatoDTO->getStrSigla());
        $objContatoAPI->setNome($objContatoDTO->getStrNome());

        return $objContatoAPI;
    }

    /**
     * Cria no processo um documento da tela, replica o interessado permitido e bloqueia apenas o Principal pela API do core.
     *
     * @param MdPetPiRelPeticaoDocDTO $objDocumentoDTO
     * @param float $dblIdProcedimento
     * @param ContatoAPI $objInteressadoAPI interessado informado ao processo
     * @param int[] $arrIdSerieComInteressados tipos gerados que permitem interessados
     * @return float identificador do documento criado
     */
    private function criarDocumentoNoProcesso(MdPetPiRelPeticaoDocDTO $objDocumentoDTO, $dblIdProcedimento, ContatoAPI $objInteressadoAPI, array $arrIdSerieComInteressados)
    {
        $objDocumentoAPI = new DocumentoAPI();
        $objDocumentoAPI->setIdProcedimento($dblIdProcedimento);
        $objDocumentoAPI->setNivelAcesso($objDocumentoDTO->getStrStaNivelAcesso());
        $objDocumentoAPI->setIdHipoteseLegal($objDocumentoDTO->getNumIdHipoteseLegal());
        $objDocumentoAPI->setNomeArvore($objDocumentoDTO->getStrComplemento());
        $objDocumentoAPI->setSinBloqueado($objDocumentoDTO->getStrStaPapel() === MdPetPiRelPeticaoDocRN::$PAPEL_PRINCIPAL ? 'S' : 'N');

        if ($objDocumentoDTO->getStrNomeArquivoUpload() !== '') {

            $strCaminho = DIR_SEI_TEMP . '/' . $objDocumentoDTO->getStrNomeArquivoUpload();

            if (!is_file($strCaminho)) {
                throw new InfraException('Arquivo "' . $objDocumentoDTO->getStrNomeArquivo() . '" não está mais disponível na área temporária.');
            }

            $objDocumentoAPI->setTipo(ProtocoloRN::$TP_DOCUMENTO_RECEBIDO);
            $objDocumentoAPI->setIdSerie($objDocumentoDTO->getNumIdSerie());
            $objDocumentoAPI->setData(InfraData::getStrDataAtual());
            $objDocumentoAPI->setIdTipoConferencia($objDocumentoDTO->getNumIdTipoConferencia());
            $objDocumentoAPI->setNomeArquivo($objDocumentoDTO->getStrNomeArquivo());
            $objDocumentoAPI->setConteudo(base64_encode(file_get_contents($strCaminho)));

        } else {

            $objDocumentoAPI->setTipo(ProtocoloRN::$TP_DOCUMENTO_GERADO);
            $objDocumentoAPI->setIdSerie($this->obterIdSerieDocumentoGerado($objDocumentoDTO));

            $arrCamposFormulario = $objDocumentoDTO->getArrCamposFormulario();

            if (count($arrCamposFormulario) > 0) {

                $arrObjCampoAPI = [];

                foreach ($arrCamposFormulario as $strNomeCampo => $strValor) {
                    $objCampoAPI = new CampoAPI();
                    $objCampoAPI->setNome((string) $strNomeCampo);
                    $objCampoAPI->setValor($strValor);

                    $arrObjCampoAPI[] = $objCampoAPI;
                }

                $objDocumentoAPI->setCampos($arrObjCampoAPI);
                $objDocumentoAPI->setConteudo('');
            } elseif (count($objDocumentoDTO->getArrConteudoSecoes()) > 0) {

                $arrObjSecaoDocumentoAPI = [];

                foreach ($objDocumentoDTO->getArrConteudoSecoes() as $strNomeSecao => $strConteudoSecao) {
                    $objSecaoDocumentoAPI = new SecaoDocumentoAPI();
                    $objSecaoDocumentoAPI->setNome((string) $strNomeSecao);
                    $objSecaoDocumentoAPI->setConteudo(base64_encode(InfraString::toUTF8($strConteudoSecao)));

                    $arrObjSecaoDocumentoAPI[] = $objSecaoDocumentoAPI;
                }

                $objDocumentoAPI->setConteudoSecoes($arrObjSecaoDocumentoAPI);
                $objDocumentoAPI->setConteudo('');
            } else {
                $objDocumentoAPI->setConteudo(base64_encode($objDocumentoDTO->getStrConteudo()));
            }
        }

        if ($objDocumentoAPI->getTipo() === ProtocoloRN::$TP_DOCUMENTO_RECEBIDO || in_array((int) $objDocumentoAPI->getIdSerie(), $arrIdSerieComInteressados, true)) {
            $objDocumentoAPI->setInteressados([$objInteressadoAPI]);
        }

        return (new SeiRN())->incluirDocumento($objDocumentoAPI)->getIdDocumento();
    }

    /**
     * Tipo de documento de um documento gerado. Na aplicabilidade Formulário a administração guarda o Tipo de
     * Formulário, então aqui se resolve o tipo de documento ativo que usa esse formulário.
     *
     * @param MdPetPiRelPeticaoDocDTO $objDocumentoDTO
     * @return int
     */
    private function obterIdSerieDocumentoGerado(MdPetPiRelPeticaoDocDTO $objDocumentoDTO)
    {
        if ($objDocumentoDTO->getNumIdSerie() !== null) {
            return $objDocumentoDTO->getNumIdSerie();
        }

        $objSerieDTO = new SerieDTO();
        $objSerieDTO->retNumIdSerie();
        $objSerieDTO->setStrSinAtivo('S');
        $objSerieDTO->setStrStaAplicabilidade(SerieRN::$TA_FORMULARIO);

        $arrObjSerieDTO = (new SerieRN())->listarRN0646($objSerieDTO);

        if (count($arrObjSerieDTO) !== 1) {
            throw new InfraException('O Tipo de Formulário configurado no Documento Principal precisa corresponder a um único Tipo de Documento ativo.');
        }

        return $arrObjSerieDTO[0]->getNumIdSerie();
    }

    /**
     * Tipos de documento que permitem interessados, consultados em lote antes da inclusão dos documentos.
     * SerieAPI não expõe essa opção administrativa.
     *
     * @param int[] $arrIdSerie
     * @return int[]
     */
    private function listarSeriesComInteressados(array $arrIdSerie): array
    {
        if (count($arrIdSerie) === 0) {
            return [];
        }

        $objSerieDTO = new SerieDTO();
        $objSerieDTO->retNumIdSerie();
        $objSerieDTO->setNumIdSerie(array_values(array_unique($arrIdSerie)), InfraDTO::$OPER_IN);
        $objSerieDTO->setStrSinInteressado('S');

        return array_map('intval', InfraArray::converterArrInfraDTO((new SerieRN())->listarRN0646($objSerieDTO), 'IdSerie'));
    }

    /**
     * Gera um documento para cada Documento Adicional Gerado Automático configurado, sem interação do usuário.
     *
     * @param int $numIdMdPetPiTpProcesso
     * @param float $dblIdProcedimento
     * @param ContatoAPI $objInteressadoAPI interessado informado ao processo
     * @return MdPetPiRelPeticaoDocDTO[]
     */
    private function gerarDocumentosAutomaticos($numIdMdPetPiTpProcesso, $dblIdProcedimento, ContatoAPI $objInteressadoAPI)
    {
        $objMdPetPiDocAutoDTO = new MdPetPiDocAutoDTO();
        $objMdPetPiDocAutoDTO->retTodos();
        $objMdPetPiDocAutoDTO->setNumIdMdPetPiTpProcesso($numIdMdPetPiTpProcesso);
        $objMdPetPiDocAutoDTO->setOrdNumIdMdPetPiDocAuto(InfraDTO::$TIPO_ORDENACAO_ASC);

        $arrObjDocAutoDTO = (new MdPetPiDocAutoRN())->listar($objMdPetPiDocAutoDTO);
        $arrIdSerieComInteressados = $this->listarSeriesComInteressados(InfraArray::converterArrInfraDTO($arrObjDocAutoDTO, 'IdSerie'));
        $arrObjDocumentoDTO = [];

        foreach ($arrObjDocAutoDTO as $objDocAutoDTO) {

            $objDocumentoAPI = new DocumentoAPI();
            $objDocumentoAPI->setIdProcedimento($dblIdProcedimento);
            $objDocumentoAPI->setTipo(ProtocoloRN::$TP_DOCUMENTO_GERADO);
            $objDocumentoAPI->setIdSerie($objDocAutoDTO->getNumIdSerie());
            $objDocumentoAPI->setNivelAcesso($objDocAutoDTO->getStrStaNivelAcesso());
            $objDocumentoAPI->setIdHipoteseLegal($objDocAutoDTO->getNumIdHipoteseLegal());
            $objDocumentoAPI->setConteudo(base64_encode(MdPetPiPeticaoINT::obterConteudoModelo($objDocAutoDTO->getDblIdProtocoloModelo(), $objDocAutoDTO->getNumIdSerie())));

            if (in_array((int) $objDocAutoDTO->getNumIdSerie(), $arrIdSerieComInteressados, true)) {
                $objDocumentoAPI->setInteressados([$objInteressadoAPI]);
            }

            $objDocumentoDTO = new MdPetPiRelPeticaoDocDTO();
            $objDocumentoDTO->setStrStaPapel(MdPetPiRelPeticaoDocRN::$PAPEL_ADICIONAL_AUTOMATICO);
            $objDocumentoDTO->setDblIdDocumento((new SeiRN())->incluirDocumento($objDocumentoAPI)->getIdDocumento());

            $arrObjDocumentoDTO[] = $objDocumentoDTO;
        }

        return $arrObjDocumentoDTO;
    }

    /**
     * Níveis de acesso permitidos no core para o tipo de processo.
     *
     * @param int $numIdTipoProcedimento
     * @return string[]
     */
    private function listarNiveisAcessoPermitidos($numIdTipoProcedimento)
    {
        $objNivelAcessoPermitidoDTO = new NivelAcessoPermitidoDTO();
        $objNivelAcessoPermitidoDTO->retStrStaNivelAcesso();
        $objNivelAcessoPermitidoDTO->setNumIdTipoProcedimento($numIdTipoProcedimento);

        return InfraArray::converterArrInfraDTO((new NivelAcessoPermitidoRN())->listar($objNivelAcessoPermitidoDTO), 'StaNivelAcesso');
    }

    /**
     * Peticionamentos Internos dos processos informados no DTO, para o ícone do processo na árvore, no Controle de
     * Processos e no Acompanhamento Especial.
     *
     * Helper interno, sem verificação de permissão: roda nos eventos de montagem de ícones do core, onde não há
     * ação de usuário a autorizar. Não abre transação, não grava e não envia mensagem.
     *
     * @param MdPetPiPeticaoDTO $objMdPetPiPeticaoDTO com IdProcedimento informado por OPER_IN
     * @return MdPetPiPeticaoDTO[]
     */
    protected function obterPeticoesProcessosConectado(MdPetPiPeticaoDTO $objMdPetPiPeticaoDTO)
    {
        try {
            $objMdPetPiPeticaoDTO->retDblIdProcedimento();
            $objMdPetPiPeticaoDTO->retDthPeticionamento();

            $objMdPetPiPeticaoBD = new MdPetPiPeticaoBD($this->getObjInfraIBanco());

            return $objMdPetPiPeticaoBD->listar($objMdPetPiPeticaoDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro consultando processos do Peticionamento Interno.', $e);
        }
    }
}
