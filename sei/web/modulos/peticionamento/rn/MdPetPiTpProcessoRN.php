<?php
/**
 * ANATEL
 *
 * Regras de negocio do Tipo de Processo parametrizado para o Peticionamento Interno.
 * Orquestra os blocos de documento e a disponibilizacao, na mesma transacao.
 *
 * 08/09/2026 - criado por emerson_b.goncalves - ANATEL
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetPiTpProcessoRN extends InfraRN
{
    /** Tipo de Indicacao de Interessado */
    public static $II_INTERESSE_PESSOAL = 'P';
    public static $II_INTERESSE_UNIDADE = 'U';

    /** Aplicabilidade do Documento Principal */
    public static $DOC_GERADO = 'G';
    public static $DOC_EXTERNO = 'E';
    public static $DOC_FORMULARIO = 'F';

    /** Comportamento do Nível de Acesso de cada documento */
    public static $TNA_USUARIO = 'U';
    public static $TNA_PADRAO = 'P';

    public function __construct()
    {
        parent::__construct();
    }

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    /**
     * @param MdPetPiTpProcessoDTO $objMdPetPiTpProcessoDTO
     * @return MdPetPiTpProcessoDTO[]
     */
    protected function listarConectado(MdPetPiTpProcessoDTO $objMdPetPiTpProcessoDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_tp_processo_listar', __METHOD__, $objMdPetPiTpProcessoDTO);

            $objMdPetPiTpProcessoBD = new MdPetPiTpProcessoBD($this->getObjInfraIBanco());

            return $objMdPetPiTpProcessoBD->listar($objMdPetPiTpProcessoDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro listando Tipo de Processo do Peticionamento Interno.', $e);
        }
    }

    /**
     * @param MdPetPiTpProcessoDTO $objMdPetPiTpProcessoDTO
     * @return int
     */
    protected function contarConectado(MdPetPiTpProcessoDTO $objMdPetPiTpProcessoDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_tp_processo_listar', __METHOD__, $objMdPetPiTpProcessoDTO);

            $objMdPetPiTpProcessoBD = new MdPetPiTpProcessoBD($this->getObjInfraIBanco());

            return $objMdPetPiTpProcessoBD->contar($objMdPetPiTpProcessoDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro contando Tipo de Processo do Peticionamento Interno.', $e);
        }
    }

    /**
     * @param MdPetPiTpProcessoDTO $objMdPetPiTpProcessoDTO
     * @return MdPetPiTpProcessoDTO|null
     */
    protected function consultarConectado(MdPetPiTpProcessoDTO $objMdPetPiTpProcessoDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_tp_processo_consultar', __METHOD__, $objMdPetPiTpProcessoDTO);

            $objMdPetPiTpProcessoBD = new MdPetPiTpProcessoBD($this->getObjInfraIBanco());

            return $objMdPetPiTpProcessoBD->consultar($objMdPetPiTpProcessoDTO);
        } catch (Exception $e) {
            throw new InfraException('Erro consultando Tipo de Processo do Peticionamento Interno.', $e);
        }
    }

    /**
     * Configurações que o usuário pode peticionar na unidade atual.
     *
     * @param MdPetPiTpProcessoDTO $objMdPetPiTpProcessoDTO atributos de retorno, filtros e ordenação
     * @return MdPetPiTpProcessoDTO[]
     */
    protected function listarDisponiveisConectado(MdPetPiTpProcessoDTO $objMdPetPiTpProcessoDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_tp_processo_listar', __METHOD__, $objMdPetPiTpProcessoDTO);

            $objMdPetPiTpProcessoDTO->retNumIdMdPetPiTpProcesso();
            $objMdPetPiTpProcessoDTO->retNumIdTipoProcedimento();
            $objMdPetPiTpProcessoDTO->setStrSinAtivo('S');

            $objMdPetPiTpProcessoBD = new MdPetPiTpProcessoBD($this->getObjInfraIBanco());
            $arrObjMdPetPiTpProcessoDTO = $objMdPetPiTpProcessoBD->listar($objMdPetPiTpProcessoDTO);

            if (count($arrObjMdPetPiTpProcessoDTO) === 0) {
                return [];
            }

            $arrIdTipoProcedimento = array_values(array_unique(InfraArray::converterArrInfraDTO($arrObjMdPetPiTpProcessoDTO, 'IdTipoProcedimento')));

            $arrIdTipoProcedimentoLiberado = array_intersect(
                $this->_listarIdsTipoProcedimentoAtivo($arrIdTipoProcedimento),
                $this->listarIdsTipoProcedimentoComAssunto($arrIdTipoProcedimento),
                $this->_listarIdsTipoProcedimentoSemRestricaoUnidadeAtual($arrIdTipoProcedimento)
            );

            $arrIdMdPetPiTpProcessoDisponibilizado = $this->_listarIdsDisponibilizadosUnidadeAtual(
                InfraArray::converterArrInfraDTO($arrObjMdPetPiTpProcessoDTO, 'IdMdPetPiTpProcesso')
            );

            $arrRetorno = [];

            foreach ($arrObjMdPetPiTpProcessoDTO as $objDTO) {
                if (in_array($objDTO->getNumIdTipoProcedimento(), $arrIdTipoProcedimentoLiberado)
                    && in_array($objDTO->getNumIdMdPetPiTpProcesso(), $arrIdMdPetPiTpProcessoDisponibilizado)) {
                    $arrRetorno[] = $objDTO;
                }
            }

            return $arrRetorno;
        } catch (Exception $e) {
            throw new InfraException('Erro listando Tipos de Processo disponíveis para o Peticionamento Interno.', $e);
        }
    }

    /**
     * @param MdPetPiTpProcessoDTO $objMdPetPiTpProcessoDTO
     * @return MdPetPiTpProcessoDTO
     */
    protected function cadastrarControlado(MdPetPiTpProcessoDTO $objMdPetPiTpProcessoDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_tp_processo_cadastrar', __METHOD__, $objMdPetPiTpProcessoDTO);

            $objInfraException = new InfraException();

            $this->_validarCamposObrigatorios($objMdPetPiTpProcessoDTO, $objInfraException);
            $this->_validarDuplicidade($objMdPetPiTpProcessoDTO, $objInfraException, true);
            $this->_validarAssuntoTipoProcesso($objMdPetPiTpProcessoDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $objMdPetPiTpProcessoBD = new MdPetPiTpProcessoBD($this->getObjInfraIBanco());
            $objRetornoDTO = $objMdPetPiTpProcessoBD->cadastrar($objMdPetPiTpProcessoDTO);

            $this->_gravarFilhos($objRetornoDTO->getNumIdMdPetPiTpProcesso(), $objMdPetPiTpProcessoDTO);

            return $objRetornoDTO;
        } catch (Exception $e) {
            throw new InfraException('Erro cadastrando Tipo de Processo do Peticionamento Interno.', $e);
        }
    }

    /**
     * @param MdPetPiTpProcessoDTO $objMdPetPiTpProcessoDTO
     * @return bool
     */
    protected function alterarControlado(MdPetPiTpProcessoDTO $objMdPetPiTpProcessoDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_tp_processo_alterar', __METHOD__, $objMdPetPiTpProcessoDTO);

            $objInfraException = new InfraException();

            $this->_validarCamposObrigatorios($objMdPetPiTpProcessoDTO, $objInfraException);
            $this->_validarDuplicidade($objMdPetPiTpProcessoDTO, $objInfraException, false);
            $this->_validarAssuntoTipoProcesso($objMdPetPiTpProcessoDTO, $objInfraException);

            $objInfraException->lancarValidacoes();

            $this->_validarReativacaoAoAlterar($objMdPetPiTpProcessoDTO);

            $objMdPetPiTpProcessoBD = new MdPetPiTpProcessoBD($this->getObjInfraIBanco());
            $objMdPetPiTpProcessoBD->alterar($objMdPetPiTpProcessoDTO);

            $numId = $objMdPetPiTpProcessoDTO->getNumIdMdPetPiTpProcesso();

            $this->_excluirFilhos($numId);
            $this->_gravarFilhos($numId, $objMdPetPiTpProcessoDTO);

            return true;
        } catch (Exception $e) {
            throw new InfraException('Erro alterando Tipo de Processo do Peticionamento Interno.', $e);
        }
    }

    /**
     * @param MdPetPiTpProcessoDTO[] $arrMdPetPiTpProcessoDTO
     * @return void
     */
    protected function excluirControlado($arrMdPetPiTpProcessoDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_tp_processo_excluir', __METHOD__, $arrMdPetPiTpProcessoDTO);

            $objMdPetPiTpProcessoBD = new MdPetPiTpProcessoBD($this->getObjInfraIBanco());

            for ($i = 0; $i < count($arrMdPetPiTpProcessoDTO); $i++) {
                $this->_excluirFilhos($arrMdPetPiTpProcessoDTO[$i]->getNumIdMdPetPiTpProcesso());
                $objMdPetPiTpProcessoBD->excluir($arrMdPetPiTpProcessoDTO[$i]);
            }
        } catch (Exception $e) {
            throw new InfraException('Erro excluindo Tipo de Processo do Peticionamento Interno.', $e);
        }
    }

    /**
     * @param MdPetPiTpProcessoDTO[] $arrMdPetPiTpProcessoDTO
     * @return void
     */
    protected function desativarControlado($arrMdPetPiTpProcessoDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_tp_processo_desativar', __METHOD__, $arrMdPetPiTpProcessoDTO);

            $objMdPetPiTpProcessoBD = new MdPetPiTpProcessoBD($this->getObjInfraIBanco());

            for ($i = 0; $i < count($arrMdPetPiTpProcessoDTO); $i++) {
                $objMdPetPiTpProcessoBD->desativar($arrMdPetPiTpProcessoDTO[$i]);
            }
        } catch (Exception $e) {
            throw new InfraException('Erro desativando Tipo de Processo do Peticionamento Interno.', $e);
        }
    }

    /**
     * @param MdPetPiTpProcessoDTO[] $arrMdPetPiTpProcessoDTO
     * @return void
     */
    protected function reativarControlado($arrMdPetPiTpProcessoDTO)
    {
        try {
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_pi_tp_processo_reativar', __METHOD__, $arrMdPetPiTpProcessoDTO);

            $this->_validarReativacao($arrMdPetPiTpProcessoDTO);

            $objMdPetPiTpProcessoBD = new MdPetPiTpProcessoBD($this->getObjInfraIBanco());

            for ($i = 0; $i < count($arrMdPetPiTpProcessoDTO); $i++) {
                $objMdPetPiTpProcessoBD->reativar($arrMdPetPiTpProcessoDTO[$i]);
            }
        } catch (Exception $e) {
            throw new InfraException('Erro reativando Tipo de Processo do Peticionamento Interno.', $e);
        }
    }

    /**
     * Valida o lote do evento do core, considerando apenas parametrizações ativas na desativação.
     * @param TipoProcedimentoAPI[] $arrObjTipoProcedimentoAPI
     */
    public function validarOperacaoTipoProcesso(array $arrObjTipoProcedimentoAPI, bool $bolDesativar): void
    {
        if (count($arrObjTipoProcedimentoAPI) === 0) {
            return;
        }

        $arrIds = array_map(static fn(TipoProcedimentoAPI $objAPI) => $objAPI->getIdTipoProcedimento(), $arrObjTipoProcedimentoAPI);
        $objFiltro = new MdPetPiTpProcessoDTO();
        $objFiltro->setNumIdTipoProcedimento($arrIds, InfraDTO::$OPER_IN);
        $this->_criticarAssociacoes($objFiltro, false, $bolDesativar);
    }

    /**
     * Verifica os três blocos documentais em lote, sem repetir o mesmo tipo de processo na crítica.
     * @param SerieAPI[] $arrObjSerieAPI
     */
    public function validarOperacaoTipoDocumento(array $arrObjSerieAPI, bool $bolDesativar): void
    {
        if (count($arrObjSerieAPI) === 0) {
            return;
        }

        $arrIds = array_map(static fn(SerieAPI $objAPI) => $objAPI->getIdSerie(), $arrObjSerieAPI);
        $arrIdConfiguracao = [];
        foreach ([MdPetPiDocPrincDTO::class, MdPetPiAnexoObrigDTO::class, MdPetPiDocAutoDTO::class] as $strClasseDTO) {
            $objFiltroDocumento = new $strClasseDTO();
            $objFiltroDocumento->retNumIdMdPetPiTpProcesso();
            $objFiltroDocumento->setNumIdSerie($arrIds, InfraDTO::$OPER_IN);
            $strClasseRN = substr($strClasseDTO, 0, -3) . 'RN';
            $arrIdConfiguracao = array_merge($arrIdConfiguracao, InfraArray::converterArrInfraDTO((new $strClasseRN())->listar($objFiltroDocumento), 'IdMdPetPiTpProcesso'));
        }

        if (count($arrIdConfiguracao) === 0) {
            return;
        }

        $objFiltro = new MdPetPiTpProcessoDTO();
        $objFiltro->setNumIdMdPetPiTpProcesso(array_values(array_unique($arrIdConfiguracao)), InfraDTO::$OPER_IN);
        $this->_criticarAssociacoes($objFiltro, true, $bolDesativar);
    }

    /** Lança a crítica no evento, preservando a transação do lote do core. */
    private function _criticarAssociacoes(MdPetPiTpProcessoDTO $objFiltro, bool $bolDocumento, bool $bolDesativar): void
    {
        $objFiltro->setBolExclusaoLogica(false);
        if ($bolDesativar) {
            $objFiltro->setStrSinAtivo('S');
        }
        $objFiltro->retStrNomeTipoProcedimento();
        $objFiltro->setOrdStrNomeTipoProcedimento(InfraDTO::$TIPO_ORDENACAO_ASC);
        $arrConfiguracoes = $this->listar($objFiltro);
        if (count($arrConfiguracoes) === 0) {
            return;
        }

        $strMensagem = $bolDocumento ? 'Este Tipo de Documento' : 'Este Tipo de Processo';
        $strMensagem .= $bolDesativar ? ' não pode ser desativado, pois está associado com Peticionamento Interno ativo' : ' não pode ser excluído, pois está associado com Peticionamento Interno';
        $strMensagem .= ' na Administração de Peticionamento Eletrônico';
        $strMensagem .= $bolDocumento ? ' sobre os Tipos de Processos abaixo listados:' : ':';
        $strMensagem .= "\n";
        foreach ($arrConfiguracoes as $objConfiguracao) {
            $strMensagem .= "\n   - " . $objConfiguracao->getStrNomeTipoProcedimento();
        }
        (new InfraException())->lancarValidacao($strMensagem);
    }

    /**
     * Confere as dependências do lote antes de persistir qualquer reativação.
     * @param MdPetPiTpProcessoDTO[] $arrConfiguracoes
     */
    private function _validarReativacao(array $arrConfiguracoes): void
    {
        if (count($arrConfiguracoes) === 0) {
            return;
        }

        $arrIds = InfraArray::converterArrInfraDTO($arrConfiguracoes, 'IdMdPetPiTpProcesso');
        $objFiltro = new MdPetPiTpProcessoDTO();
        $objFiltro->setBolExclusaoLogica(false);
        $objFiltro->retNumIdTipoProcedimento();
        $objFiltro->setNumIdMdPetPiTpProcesso($arrIds, InfraDTO::$OPER_IN);
        $arrIdTipoProcedimento = InfraArray::converterArrInfraDTO($this->listar($objFiltro), 'IdTipoProcedimento');

        $arrIdSerie = [];
        foreach ([MdPetPiDocPrincDTO::class, MdPetPiAnexoObrigDTO::class, MdPetPiDocAutoDTO::class] as $strClasseDTO) {
            $objFiltroDocumento = new $strClasseDTO();
            $objFiltroDocumento->retNumIdSerie();
            $objFiltroDocumento->setNumIdMdPetPiTpProcesso($arrIds, InfraDTO::$OPER_IN);
            $strClasseRN = substr($strClasseDTO, 0, -3) . 'RN';
            $arrIdSerie = array_merge($arrIdSerie, InfraArray::converterArrInfraDTO((new $strClasseRN())->listar($objFiltroDocumento), 'IdSerie'));
        }
        $this->_criticarDependenciasDesativadas($arrIdTipoProcedimento, $arrIdSerie);
    }

    /** O salvamento de uma configuração inativa também pode solicitar sua reativação. */
    private function _validarReativacaoAoAlterar(MdPetPiTpProcessoDTO $objConfiguracao): void
    {
        if (!$objConfiguracao->isSetStrSinAtivo() || $objConfiguracao->getStrSinAtivo() !== 'S') {
            return;
        }
        $objFiltro = new MdPetPiTpProcessoDTO();
        $objFiltro->setBolExclusaoLogica(false);
        $objFiltro->setNumIdMdPetPiTpProcesso($objConfiguracao->getNumIdMdPetPiTpProcesso());
        $objFiltro->setStrSinAtivo('N');
        if ($this->contar($objFiltro) === 0) {
            return;
        }
        $arrDocumentos = array_merge([$objConfiguracao->getObjObjMdPetPiDocPrincDTO()],
            (array) $objConfiguracao->getArrObjMdPetPiAnexoObrigDTO(), (array) $objConfiguracao->getArrObjMdPetPiDocAutoDTO());
        $this->_criticarDependenciasDesativadas([$objConfiguracao->getNumIdTipoProcedimento()],
            InfraArray::converterArrInfraDTO($arrDocumentos, 'IdSerie'));
    }

    /**
     * Consulta os registros desativados no core em lote, inclusive os excluídos logicamente.
     * @param int[] $arrIdTipoProcedimento
     * @param int[] $arrIdSerie
     */
    private function _criticarDependenciasDesativadas(array $arrIdTipoProcedimento, array $arrIdSerie): void
    {
        $objTipoProcedimentoDTO = new TipoProcedimentoDTO();
        $objTipoProcedimentoDTO->setBolExclusaoLogica(false);
        $objTipoProcedimentoDTO->retStrNome();
        $objTipoProcedimentoDTO->setStrSinAtivo('N');
        $objTipoProcedimentoDTO->setNumIdTipoProcedimento($arrIdTipoProcedimento, InfraDTO::$OPER_IN);
        $objTipoProcedimentoDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);
        $arrLinhas = [];
        foreach ((new TipoProcedimentoRN())->listarRN0244($objTipoProcedimentoDTO) as $objTipoProcedimento) {
            $arrLinhas[] = 'Tipo de Processo: ' . $objTipoProcedimento->getStrNome();
        }

        $arrIdSerie = array_values(array_unique(array_filter($arrIdSerie)));
        if (count($arrIdSerie) > 0) {
            $objSerieDTO = new SerieDTO();
            $objSerieDTO->setBolExclusaoLogica(false);
            $objSerieDTO->retStrNome();
            $objSerieDTO->setStrSinAtivo('N');
            $objSerieDTO->setNumIdSerie($arrIdSerie, InfraDTO::$OPER_IN);
            $objSerieDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);
            foreach ((new SerieRN())->listarRN0646($objSerieDTO) as $objSerie) {
                $arrLinhas[] = 'Tipo de Documento: ' . $objSerie->getStrNome();
            }
        }

        if (count($arrLinhas) > 0) {
            (new InfraException())->lancarValidacao('Este Tipo de Processo não pode ser reativado no Peticionamento Interno, pois está associado com Tipo de Processo e/ou Tipo de Documento desativados. Antes, reative esses registros na Administração do SEI para poder depois reativar o Peticionamento Interno:' . "\n\n   - " . implode("\n   - ", $arrLinhas));
        }
    }

    /**
     * Critica a exclusão de documento usado como modelo, antes da limpeza dos vínculos no evento do core.
     */
    public function validarExclusaoDocumentoModelo(DocumentoAPI $objDocumentoAPI): void
    {
        $objDocPrincDTO = new MdPetPiDocPrincDTO();
        $objDocPrincDTO->retNumIdMdPetPiTpProcesso();
        $objDocPrincDTO->setDblIdProtocoloModelo($objDocumentoAPI->getIdDocumento());
        $arrObjDocPrincDTO = (new MdPetPiDocPrincRN())->listar($objDocPrincDTO);

        $objDocAutoDTO = new MdPetPiDocAutoDTO();
        $objDocAutoDTO->retNumIdMdPetPiTpProcesso();
        $objDocAutoDTO->setDblIdProtocoloModelo($objDocumentoAPI->getIdDocumento());
        $arrObjDocAutoDTO = (new MdPetPiDocAutoRN())->listar($objDocAutoDTO);

        $arrIdTipoProcesso = array_values(array_unique(array_merge(
            InfraArray::converterArrInfraDTO($arrObjDocPrincDTO, 'IdMdPetPiTpProcesso'),
            InfraArray::converterArrInfraDTO($arrObjDocAutoDTO, 'IdMdPetPiTpProcesso')
        )));

        if (count($arrIdTipoProcesso) === 0) {
            return;
        }

        $objTpProcessoDTO = new MdPetPiTpProcessoDTO();
        $objTpProcessoDTO->retStrNomeTipoProcedimento();
        $objTpProcessoDTO->setNumIdMdPetPiTpProcesso($arrIdTipoProcesso, InfraDTO::$OPER_IN);
        $objTpProcessoDTO->setOrdStrNomeTipoProcedimento(InfraDTO::$TIPO_ORDENACAO_ASC);
        $arrObjTpProcessoDTO = $this->listar($objTpProcessoDTO);

        $strMensagem = 'Este documento não pode ser excluído, pois está associado como "Modelo por Protocolo" na Administração de Peticionamento Interno sobre os Tipos de Processos abaixo listados:' . "\n";

        foreach ($arrObjTpProcessoDTO as $objTipoProcessoDTO) {
            $strMensagem .= "\n   - " . $objTipoProcessoDTO->getStrNomeTipoProcedimento();
        }

        (new InfraException())->lancarValidacao($strMensagem);
    }

    /**
     * Indica se o Tipo de Processo possui ao menos uma sugestao de assunto parametrizada no core.
     *
     * @param int $numIdTipoProcedimento
     * @return bool
     */
    public function possuiAssuntoParametrizado($numIdTipoProcedimento)
    {
        if (InfraString::isBolVazia($numIdTipoProcedimento)) {
            return false;
        }

        $objRelTipoProcedimentoAssuntoDTO = new RelTipoProcedimentoAssuntoDTO();
        $objRelTipoProcedimentoAssuntoDTO->setNumIdTipoProcedimento($numIdTipoProcedimento);

        return (new RelTipoProcedimentoAssuntoRN())->contarRN0287($objRelTipoProcedimentoAssuntoDTO) > 0;
    }

    /**
     * Ids de Tipo de Processo que possuem Sugestao de Assuntos, dentre os informados.
     * Evita uma consulta por linha na listagem.
     *
     * @param int[] $arrIdTipoProcedimento
     * @return int[]
     */
    public function listarIdsTipoProcedimentoComAssunto($arrIdTipoProcedimento)
    {
        if (!is_array($arrIdTipoProcedimento) || count($arrIdTipoProcedimento) === 0) {
            return [];
        }

        $objRelTipoProcedimentoAssuntoDTO = new RelTipoProcedimentoAssuntoDTO();
        $objRelTipoProcedimentoAssuntoDTO->retNumIdTipoProcedimento();
        $objRelTipoProcedimentoAssuntoDTO->setDistinct(true);
        $objRelTipoProcedimentoAssuntoDTO->setNumIdTipoProcedimento($arrIdTipoProcedimento, InfraDTO::$OPER_IN);

        $arrObjDTO = (new RelTipoProcedimentoAssuntoRN())->listarRN0192($objRelTipoProcedimentoAssuntoDTO);

        return InfraArray::converterArrInfraDTO($arrObjDTO, 'IdTipoProcedimento');
    }

    /**
     * Ids de Tipo de Processo ativos no core, dentre os informados.
     *
     * @param int[] $arrIdTipoProcedimento
     * @return int[]
     */
    private function _listarIdsTipoProcedimentoAtivo($arrIdTipoProcedimento)
    {
        $objTipoProcedimentoDTO = new TipoProcedimentoDTO();
        $objTipoProcedimentoDTO->retNumIdTipoProcedimento();
        $objTipoProcedimentoDTO->setStrSinAtivo('S');
        $objTipoProcedimentoDTO->setNumIdTipoProcedimento($arrIdTipoProcedimento, InfraDTO::$OPER_IN);

        return InfraArray::converterArrInfraDTO((new TipoProcedimentoRN())->listarRN0244($objTipoProcedimentoDTO), 'IdTipoProcedimento');
    }

    /**
     * Ids de Tipo de Processo cuja restrição por órgão e unidade no core libera a unidade atual, como exige a geração do processo.
     *
     * @param int[] $arrIdTipoProcedimento
     * @return int[]
     */
    private function _listarIdsTipoProcedimentoSemRestricaoUnidadeAtual($arrIdTipoProcedimento)
    {
        $numIdOrgaoAtual = SessaoSEI::getInstance()->getNumIdOrgaoUnidadeAtual();
        $numIdUnidadeAtual = SessaoSEI::getInstance()->getNumIdUnidadeAtual();

        $objTipoProcedRestricaoDTO = new TipoProcedRestricaoDTO();
        $objTipoProcedRestricaoDTO->retNumIdTipoProcedimento();
        $objTipoProcedRestricaoDTO->retNumIdOrgao();
        $objTipoProcedRestricaoDTO->retNumIdUnidade();
        $objTipoProcedRestricaoDTO->setNumIdTipoProcedimento($arrIdTipoProcedimento, InfraDTO::$OPER_IN);

        $arrRestricaoLibera = [];

        foreach ((new TipoProcedRestricaoRN())->listar($objTipoProcedRestricaoDTO) as $objDTO) {
            $numIdTipoProcedimento = $objDTO->getNumIdTipoProcedimento();

            if (!isset($arrRestricaoLibera[$numIdTipoProcedimento])) {
                $arrRestricaoLibera[$numIdTipoProcedimento] = false;
            }

            if ($objDTO->getNumIdOrgao() == $numIdOrgaoAtual
                && ($objDTO->getNumIdUnidade() === null || $objDTO->getNumIdUnidade() == $numIdUnidadeAtual)) {
                $arrRestricaoLibera[$numIdTipoProcedimento] = true;
            }
        }

        $arrRetorno = [];

        foreach ($arrIdTipoProcedimento as $numIdTipoProcedimento) {
            if (!isset($arrRestricaoLibera[$numIdTipoProcedimento]) || $arrRestricaoLibera[$numIdTipoProcedimento]) {
                $arrRetorno[] = $numIdTipoProcedimento;
            }
        }

        return $arrRetorno;
    }

    /**
     * Ids de configuração liberados para a unidade atual pela Disponibilização: sem nenhuma linha, com linha do órgão ou com linha da unidade.
     *
     * @param int[] $arrIdMdPetPiTpProcesso
     * @return int[]
     */
    private function _listarIdsDisponibilizadosUnidadeAtual($arrIdMdPetPiTpProcesso)
    {
        $numIdOrgaoAtual = SessaoSEI::getInstance()->getNumIdOrgaoUnidadeAtual();
        $numIdUnidadeAtual = SessaoSEI::getInstance()->getNumIdUnidadeAtual();

        $arrComDisponibilizacao = [];
        $arrLiberado = [];

        $objMdPetPiRelOrgaoDTO = new MdPetPiRelOrgaoDTO();
        $objMdPetPiRelOrgaoDTO->retNumIdMdPetPiTpProcesso();
        $objMdPetPiRelOrgaoDTO->retNumIdOrgao();
        $objMdPetPiRelOrgaoDTO->setNumIdMdPetPiTpProcesso($arrIdMdPetPiTpProcesso, InfraDTO::$OPER_IN);

        foreach ((new MdPetPiRelOrgaoRN())->listar($objMdPetPiRelOrgaoDTO) as $objDTO) {
            $arrComDisponibilizacao[$objDTO->getNumIdMdPetPiTpProcesso()] = true;

            if ($objDTO->getNumIdOrgao() == $numIdOrgaoAtual) {
                $arrLiberado[$objDTO->getNumIdMdPetPiTpProcesso()] = true;
            }
        }

        $objMdPetPiRelUnidadeDTO = new MdPetPiRelUnidadeDTO();
        $objMdPetPiRelUnidadeDTO->retNumIdMdPetPiTpProcesso();
        $objMdPetPiRelUnidadeDTO->retNumIdUnidade();
        $objMdPetPiRelUnidadeDTO->setNumIdMdPetPiTpProcesso($arrIdMdPetPiTpProcesso, InfraDTO::$OPER_IN);

        foreach ((new MdPetPiRelUnidadeRN())->listar($objMdPetPiRelUnidadeDTO) as $objDTO) {
            $arrComDisponibilizacao[$objDTO->getNumIdMdPetPiTpProcesso()] = true;

            if ($objDTO->getNumIdUnidade() == $numIdUnidadeAtual) {
                $arrLiberado[$objDTO->getNumIdMdPetPiTpProcesso()] = true;
            }
        }

        $arrRetorno = [];

        foreach ($arrIdMdPetPiTpProcesso as $numIdMdPetPiTpProcesso) {
            if (!isset($arrComDisponibilizacao[$numIdMdPetPiTpProcesso]) || isset($arrLiberado[$numIdMdPetPiTpProcesso])) {
                $arrRetorno[] = $numIdMdPetPiTpProcesso;
            }
        }

        return $arrRetorno;
    }

    /**
     * Grava o Documento Principal, os Anexos Obrigatorios, os Documentos Automaticos e a
     * disponibilizacao por Orgao e Unidade.
     *
     * @param int $numIdMdPetPiTpProcesso
     * @param MdPetPiTpProcessoDTO $objMdPetPiTpProcessoDTO
     * @return void
     */
    private function _gravarFilhos($numIdMdPetPiTpProcesso, MdPetPiTpProcessoDTO $objMdPetPiTpProcessoDTO)
    {
        $objDocPrincDTO = $objMdPetPiTpProcessoDTO->getObjObjMdPetPiDocPrincDTO();

        if ($objDocPrincDTO !== null) {
            $objDocPrincDTO->setNumIdMdPetPiTpProcesso($numIdMdPetPiTpProcesso);
            (new MdPetPiDocPrincRN())->cadastrar($objDocPrincDTO);
        }

        $this->_gravarColecao(
            new MdPetPiAnexoObrigRN(),
            $objMdPetPiTpProcessoDTO->getArrObjMdPetPiAnexoObrigDTO(),
            $numIdMdPetPiTpProcesso
        );

        $this->_gravarColecao(
            new MdPetPiDocAutoRN(),
            $objMdPetPiTpProcessoDTO->getArrObjMdPetPiDocAutoDTO(),
            $numIdMdPetPiTpProcesso
        );

        $this->_gravarColecao(
            new MdPetPiRelOrgaoRN(),
            $objMdPetPiTpProcessoDTO->getArrObjMdPetPiRelOrgaoDTO(),
            $numIdMdPetPiTpProcesso
        );

        $this->_gravarColecao(
            new MdPetPiRelUnidadeRN(),
            $objMdPetPiTpProcessoDTO->getArrObjMdPetPiRelUnidadeDTO(),
            $numIdMdPetPiTpProcesso
        );
    }

    /**
     * @param InfraRN $objRN
     * @param InfraDTO[]|null $arrObjDTO
     * @param int $numIdMdPetPiTpProcesso
     * @return void
     */
    private function _gravarColecao($objRN, $arrObjDTO, $numIdMdPetPiTpProcesso)
    {
        if (!is_array($arrObjDTO) || count($arrObjDTO) === 0) {
            return;
        }

        foreach ($arrObjDTO as $objDTO) {
            $objDTO->setNumIdMdPetPiTpProcesso($numIdMdPetPiTpProcesso);
            $objRN->cadastrar($objDTO);
        }
    }

    /**
     * @param int $numIdMdPetPiTpProcesso
     * @return void
     */
    private function _excluirFilhos($numIdMdPetPiTpProcesso)
    {
        $this->_excluirColecao(new MdPetPiDocPrincRN(), new MdPetPiDocPrincDTO(), $numIdMdPetPiTpProcesso);
        $this->_excluirColecao(new MdPetPiAnexoObrigRN(), new MdPetPiAnexoObrigDTO(), $numIdMdPetPiTpProcesso);
        $this->_excluirColecao(new MdPetPiDocAutoRN(), new MdPetPiDocAutoDTO(), $numIdMdPetPiTpProcesso);
        $this->_excluirColecao(new MdPetPiRelOrgaoRN(), new MdPetPiRelOrgaoDTO(), $numIdMdPetPiTpProcesso);
        $this->_excluirColecao(new MdPetPiRelUnidadeRN(), new MdPetPiRelUnidadeDTO(), $numIdMdPetPiTpProcesso);
    }

    /**
     * @param InfraRN $objRN
     * @param InfraDTO $objDTOFiltro
     * @param int $numIdMdPetPiTpProcesso
     * @return void
     */
    private function _excluirColecao($objRN, $objDTOFiltro, $numIdMdPetPiTpProcesso)
    {
        $objDTOFiltro->retTodos();
        $objDTOFiltro->setNumIdMdPetPiTpProcesso($numIdMdPetPiTpProcesso);

        $arrObjDTO = $objRN->listar($objDTOFiltro);

        if (count($arrObjDTO) > 0) {
            $objRN->excluir($arrObjDTO);
        }
    }

    /**
     * @param MdPetPiTpProcessoDTO $objMdPetPiTpProcessoDTO
     * @param InfraException $objInfraException
     * @return void
     */
    private function _validarCamposObrigatorios(MdPetPiTpProcessoDTO $objMdPetPiTpProcessoDTO, InfraException $objInfraException)
    {
        if (InfraString::isBolVazia($objMdPetPiTpProcessoDTO->getNumIdTipoProcedimento())) {
            $objInfraException->adicionarValidacao('Tipo de Processo não informado.');
        }

        if (InfraString::isBolVazia($objMdPetPiTpProcessoDTO->getStrOrientacoes())) {
            $objInfraException->adicionarValidacao('Orientações não informadas.');
        }

        if (!in_array($objMdPetPiTpProcessoDTO->getStrStaIndicacaoInteressado(), [self::$II_INTERESSE_PESSOAL, self::$II_INTERESSE_UNIDADE], true)) {
            $objInfraException->adicionarValidacao('Tipo de Indicação de Interessado não informado.');
        }

        $this->_validarDocumentoPrincipal($objMdPetPiTpProcessoDTO->getObjObjMdPetPiDocPrincDTO(), $objInfraException);

        $bolHipoteseLegalHabilitada = self::isHipoteseLegalHabilitada();

        if ($objMdPetPiTpProcessoDTO->getObjObjMdPetPiDocPrincDTO() !== null) {
            $this->_validarNivelAcessoDocumento($objMdPetPiTpProcessoDTO->getObjObjMdPetPiDocPrincDTO(), true, 'do Documento Principal', $bolHipoteseLegalHabilitada, $objInfraException);
            $this->_validarModeloPorProtocolo($objMdPetPiTpProcessoDTO->getObjObjMdPetPiDocPrincDTO(), 'do Documento Principal', $objInfraException);
        }

        foreach ((array) $objMdPetPiTpProcessoDTO->getArrObjMdPetPiAnexoObrigDTO() as $objAnexoObrigDTO) {
            $this->_validarNivelAcessoDocumento($objAnexoObrigDTO, true, 'dos Anexos de Arquivos Obrigatórios', $bolHipoteseLegalHabilitada, $objInfraException);
        }

        foreach ((array) $objMdPetPiTpProcessoDTO->getArrObjMdPetPiDocAutoDTO() as $objDocAutoDTO) {
            $this->_validarNivelAcessoDocumento($objDocAutoDTO, false, 'do Documento Adicional Gerado Automático', $bolHipoteseLegalHabilitada, $objInfraException);
            $this->_validarModeloPorProtocolo($objDocAutoDTO, 'do Documento Adicional Gerado Automático', $objInfraException);
        }
    }

    /**
     * Hipótese legal em uso no órgão, pelo parâmetro do core SEI_HABILITAR_HIPOTESE_LEGAL: com 0 ela não é exibida;
     * com 1 ou 2 o Peticionamento Interno a exige no nível Restrito.
     *
     * @return bool
     */
    public static function isHipoteseLegalHabilitada()
    {
        return (string) (new InfraParametro(BancoSEI::getInstance()))->getValor('SEI_HABILITAR_HIPOTESE_LEGAL', false) !== '0';
    }

    /**
     * Com "Usuário indica diretamente", o documento não guarda nível nem hipótese. Com "Padrão pré-definido" o nível é
     * obrigatório e, se Restrito, a hipótese legal também, salvo quando o parâmetro SEI_HABILITAR_HIPOTESE_LEGAL vale 0.
     * O Documento Adicional Gerado Automático não tem comportamento: é sempre pré-definido.
     *
     * @param MdPetPiDocPrincDTO|MdPetPiAnexoObrigDTO|MdPetPiDocAutoDTO $objDocumentoDTO
     * @param bool $bolComComportamento
     * @param string $strRotulo
     * @param bool $bolHipoteseLegalHabilitada
     * @param InfraException $objInfraException
     * @return void
     */
    private function _validarNivelAcessoDocumento($objDocumentoDTO, $bolComComportamento, $strRotulo, $bolHipoteseLegalHabilitada, InfraException $objInfraException)
    {
        $strStaTipoNivelAcesso = $bolComComportamento ? $objDocumentoDTO->getStrStaTipoNivelAcesso() : self::$TNA_PADRAO;

        if (!in_array($strStaTipoNivelAcesso, [self::$TNA_USUARIO, self::$TNA_PADRAO], true)) {
            $objInfraException->adicionarValidacao('Comportamento do Nível de Acesso ' . $strRotulo . ' não informado.');
            return;
        }

        if ($strStaTipoNivelAcesso === self::$TNA_USUARIO) {
            $objDocumentoDTO->setStrStaNivelAcesso(null);
            $objDocumentoDTO->setNumIdHipoteseLegal(null);
            return;
        }

        $strStaNivelAcesso = $objDocumentoDTO->getStrStaNivelAcesso();

        if (!in_array($strStaNivelAcesso, [ProtocoloRN::$NA_PUBLICO, ProtocoloRN::$NA_RESTRITO], true)) {
            $objInfraException->adicionarValidacao('Nível de Acesso ' . $strRotulo . ' não informado.');
            return;
        }

        if ($strStaNivelAcesso !== ProtocoloRN::$NA_RESTRITO || !$bolHipoteseLegalHabilitada) {
            $objDocumentoDTO->setNumIdHipoteseLegal(null);
            return;
        }

        if (InfraString::isBolVazia($objDocumentoDTO->getNumIdHipoteseLegal())) {
            $objInfraException->adicionarValidacao('Hipótese Legal ' . $strRotulo . ' não informada.');
        }
    }

    /**
     * O conteúdo do Modelo por Protocolo vai para documentos de outros usuários, então só vale documento Público.
     *
     * @param MdPetPiDocPrincDTO|MdPetPiDocAutoDTO $objDocumentoDTO
     * @param string $strRotulo
     * @param InfraException $objInfraException
     * @return void
     */
    private function _validarModeloPorProtocolo($objDocumentoDTO, $strRotulo, InfraException $objInfraException)
    {
        if ($objDocumentoDTO->isSetDblIdProtocoloModelo() && !InfraString::isBolVazia($objDocumentoDTO->getDblIdProtocoloModelo())
            && !self::isModeloPorProtocoloPublico($objDocumentoDTO->getDblIdProtocoloModelo())) {
            $objInfraException->adicionarValidacao('Modelo por Protocolo ' . $strRotulo . ' precisa ser um documento com nível de acesso Público.');
        }
    }

    /**
     * Confere se o documento indicado como Modelo por Protocolo existe e tem nível de acesso global Público.
     *
     * @param float $dblIdDocumento
     * @return bool
     */
    public static function isModeloPorProtocoloPublico($dblIdDocumento): bool
    {
        $objDocumentoDTO = new DocumentoDTO();
        $objDocumentoDTO->setDblIdDocumento($dblIdDocumento);
        $objDocumentoDTO->setStrStaNivelAcessoGlobalProtocolo(ProtocoloRN::$NA_PUBLICO);

        return (new DocumentoRN())->contarRN0007($objDocumentoDTO) > 0;
    }

    /**
     * @param MdPetPiDocPrincDTO|null $objDocPrincDTO
     * @param InfraException $objInfraException
     * @return void
     */
    private function _validarDocumentoPrincipal($objDocPrincDTO, InfraException $objInfraException)
    {
        if ($objDocPrincDTO === null) {
            $objInfraException->adicionarValidacao('Documento Principal não informado.');
            return;
        }

        $strStaAplicabilidade = $objDocPrincDTO->getStrStaAplicabilidade();

        if (!in_array($strStaAplicabilidade, [self::$DOC_GERADO, self::$DOC_EXTERNO, self::$DOC_FORMULARIO], true)) {
            $objInfraException->adicionarValidacao('Aplicabilidade do Documento Principal não informada.');
            return;
        }

        if (InfraString::isBolVazia($objDocPrincDTO->getNumIdSerie())) {
            $objInfraException->adicionarValidacao('Tipo do Documento Principal não informado.');
            return;
        }

        $arrAplicabilidadesSerie = [
            self::$DOC_GERADO => [SerieRN::$TA_INTERNO, SerieRN::$TA_INTERNO_EXTERNO],
            self::$DOC_EXTERNO => [SerieRN::$TA_EXTERNO, SerieRN::$TA_INTERNO_EXTERNO],
            self::$DOC_FORMULARIO => [SerieRN::$TA_FORMULARIO],
        ];
        $objSerieDTO = new SerieDTO();
        $objSerieDTO->retNumIdTipoFormulario();
        $objSerieDTO->setNumIdSerie($objDocPrincDTO->getNumIdSerie());
        $objSerieDTO->setStrStaAplicabilidade($arrAplicabilidadesSerie[$strStaAplicabilidade], InfraDTO::$OPER_IN);
        $objSerieDTO->setStrSinAtivo('S');
        $arrObjSerieDTO = (new SerieRN())->listarRN0646($objSerieDTO);

        if (count($arrObjSerieDTO) !== 1) {
            $objInfraException->adicionarValidacao('Tipo do Documento Principal deve ser um Tipo de Documento ativo compatível com a aplicabilidade selecionada.');
            return;
        }

        if ($strStaAplicabilidade === self::$DOC_FORMULARIO && InfraString::isBolVazia($arrObjSerieDTO[0]->getNumIdTipoFormulario())) {
            $objInfraException->adicionarValidacao('Tipo do Documento Principal deve possuir formulário associado.');
            return;
        }

        if ($strStaAplicabilidade !== self::$DOC_GERADO && !InfraString::isBolVazia($objDocPrincDTO->getDblIdProtocoloModelo())) {
            $objInfraException->adicionarValidacao('Modelo por Protocolo só se aplica ao Documento Principal Gerado.');
        }
    }

    /**
     * Um Tipo de Processo so pode ter um cadastro de Peticionamento Interno.
     *
     * @param MdPetPiTpProcessoDTO $objMdPetPiTpProcessoDTO
     * @param InfraException $objInfraException
     * @param bool $bolCadastrar
     * @return void
     */
    private function _validarDuplicidade(MdPetPiTpProcessoDTO $objMdPetPiTpProcessoDTO, InfraException $objInfraException, $bolCadastrar)
    {
        $objDTOValidacao = new MdPetPiTpProcessoDTO();
        $objDTOValidacao->setNumIdTipoProcedimento($objMdPetPiTpProcessoDTO->getNumIdTipoProcedimento());

        if (!$bolCadastrar) {
            $objDTOValidacao->setNumIdMdPetPiTpProcesso($objMdPetPiTpProcessoDTO->getNumIdMdPetPiTpProcesso(), InfraDTO::$OPER_DIFERENTE);
        }

        $objMdPetPiTpProcessoBD = new MdPetPiTpProcessoBD($this->getObjInfraIBanco());

        if ($objMdPetPiTpProcessoBD->contar($objDTOValidacao) > 0) {
            $objInfraException->adicionarValidacao('Este Tipo de Processo já possui cadastro para Peticionamento Interno. Não é possível fazer dois cadastros para o mesmo Tipo de Processo.');
        }
    }

    /**
     * O Tipo de Processo precisa ter ao menos uma sugestao de assunto parametrizada no core.
     *
     * @param MdPetPiTpProcessoDTO $objMdPetPiTpProcessoDTO
     * @param InfraException $objInfraException
     * @return void
     */
    private function _validarAssuntoTipoProcesso(MdPetPiTpProcessoDTO $objMdPetPiTpProcessoDTO, InfraException $objInfraException)
    {
        if (!$this->possuiAssuntoParametrizado($objMdPetPiTpProcessoDTO->getNumIdTipoProcedimento())) {
            $objInfraException->adicionarValidacao('Não é possível incluir este Tipo de Processo para Peticionamento Interno. Antes, solicite ao Administrador para editar o Tipo de Processo e vincular as Classificações por Assunto.');
        }
    }
}
