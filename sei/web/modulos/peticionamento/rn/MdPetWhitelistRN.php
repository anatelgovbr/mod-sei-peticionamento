<?php
/**
 * Generic, context-aware whitelist business rules.
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetWhitelistRN extends InfraRN
{
    public const FINALIDADE_PETICIONAMENTO_INTERCORRENTE = 'I';
    public const FINALIDADE_PROCESSO_VINCULACAO = 'V';
    public const FINALIDADE_SUSPENSAO_AUTOMATICA = 'S';

    public const TIPO_PROCESSO = 'P';
    public const TIPO_DOCUMENTO = 'D';
    public const TIPO_CONTATO = 'C';

    private const RECURSO_PROCESSO_ESCRITA = 'md_pet_vinc_tp_processo_cadastrar';

    private const TIPOS_OBJETO = [
        self::TIPO_PROCESSO,
        self::TIPO_DOCUMENTO,
        self::TIPO_CONTATO,
    ];

    private const FINALIDADES = [
        self::FINALIDADE_PETICIONAMENTO_INTERCORRENTE => [
            'tipos' => [self::TIPO_PROCESSO],
            'recurso_escrita' => self::RECURSO_PROCESSO_ESCRITA,
        ],
        self::FINALIDADE_PROCESSO_VINCULACAO => [
            'tipos' => [self::TIPO_PROCESSO],
            'recurso_escrita' => self::RECURSO_PROCESSO_ESCRITA,
        ],
        self::FINALIDADE_SUSPENSAO_AUTOMATICA => [
            'tipos' => [self::TIPO_CONTATO],
            'recurso_escrita' => NULL,
        ],
    ];

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    /**
     * Validates the complete selection before starting its atomic persistence.
     */
    protected function sincronizarContextoConectado(MdPetWhitelistDTO $objMdPetWhitelistDTO)
    {
        $arrConfiguracaoFinalidade = $this->validarContexto($objMdPetWhitelistDTO);
        $this->validarSincronizacaoContexto($arrConfiguracaoFinalidade);

        SessaoSEI::getInstance()->validarAuditarPermissao(
            $arrConfiguracaoFinalidade['recurso_escrita'],
            __METHOD__,
            $objMdPetWhitelistDTO
        );

        if (!$objMdPetWhitelistDTO->isSetArrIdObjetos()) {
            throw new InfraException('A lista de objetos da whitelist nao foi informada.');
        }

        $arrIdObjetos = $this->normalizarIds($objMdPetWhitelistDTO->getArrIdObjetos());
        $objMdPetWhitelistDTO->setArrIdObjetos($arrIdObjetos);

        $arrObjetosResolvidos = $this->resolverObjetos(
            $objMdPetWhitelistDTO->getStrStaTipoObjeto(),
            $arrIdObjetos
        );
        $this->validarObjetosResolvidos($arrIdObjetos, $arrObjetosResolvidos);

        return $this->sincronizarContextoPersistencia($objMdPetWhitelistDTO);
    }

    /**
     * Applies the contextual diff inside one InfraRN transaction.
     */
    protected function sincronizarContextoPersistenciaControlado(MdPetWhitelistDTO $objMdPetWhitelistDTO)
    {
        $arrConfiguracaoFinalidade = $this->validarContexto($objMdPetWhitelistDTO);
        $this->validarSincronizacaoContexto($arrConfiguracaoFinalidade);
        $arrIdObjetos = $this->normalizarIds($objMdPetWhitelistDTO->getArrIdObjetos());

        $objFiltroDTO = $this->montarFiltroContexto($objMdPetWhitelistDTO);
        $objFiltroDTO->retNumIdMdPetWhitelist();
        $objFiltroDTO->retDblIdObjeto();

        $objMdPetWhitelistBD = new MdPetWhitelistBD($this->getObjInfraIBanco());
        $arrObjMdPetWhitelistDTO = $objMdPetWhitelistBD->listar($objFiltroDTO);

        $arrExistentes = [];
        $arrDuplicados = [];
        foreach ($arrObjMdPetWhitelistDTO as $objWhitelistDTO) {
            $strIdObjeto = (string) $objWhitelistDTO->getDblIdObjeto();
            if (isset($arrExistentes[$strIdObjeto])) {
                $arrDuplicados[] = $objWhitelistDTO;
            } else {
                $arrExistentes[$strIdObjeto] = $objWhitelistDTO;
            }
        }

        $arrSelecionados = [];
        foreach ($arrIdObjetos as $numIdObjeto) {
            $arrSelecionados[(string) $numIdObjeto] = $numIdObjeto;
        }

        foreach ($arrDuplicados as $objWhitelistDTO) {
            $objMdPetWhitelistBD->excluir($objWhitelistDTO);
        }

        foreach ($arrExistentes as $strIdObjeto => $objWhitelistDTO) {
            if (!isset($arrSelecionados[$strIdObjeto])) {
                $objMdPetWhitelistBD->excluir($objWhitelistDTO);
            }
        }

        foreach ($arrSelecionados as $strIdObjeto => $numIdObjeto) {
            if (!isset($arrExistentes[$strIdObjeto])) {
                $objNovoDTO = new MdPetWhitelistDTO();
                $objNovoDTO->setStrStaFinalidade($objMdPetWhitelistDTO->getStrStaFinalidade());
                $objNovoDTO->setStrStaTipoObjeto($objMdPetWhitelistDTO->getStrStaTipoObjeto());
                $objNovoDTO->setDblIdObjeto($numIdObjeto);
                $objMdPetWhitelistBD->cadastrar($objNovoDTO);
            }
        }

        return count($arrSelecionados);
    }

    /**
     * Lists one explicit context and resolves display data in a single batch.
     */
    protected function listarContextoInternoConectado(MdPetWhitelistDTO $objMdPetWhitelistDTO): array
    {
        $this->validarContexto($objMdPetWhitelistDTO);

        $objFiltroDTO = $this->montarFiltroContexto($objMdPetWhitelistDTO);
        $objFiltroDTO->retNumIdMdPetWhitelist();
        $objFiltroDTO->retStrStaFinalidade();
        $objFiltroDTO->retStrStaTipoObjeto();
        $objFiltroDTO->retDblIdObjeto();

        $objMdPetWhitelistBD = new MdPetWhitelistBD($this->getObjInfraIBanco());
        $arrObjMdPetWhitelistDTO = $objMdPetWhitelistBD->listar($objFiltroDTO);

        $arrWhitelistPorObjeto = [];
        foreach ($arrObjMdPetWhitelistDTO as $objWhitelistDTO) {
            $strIdObjeto = (string) $objWhitelistDTO->getDblIdObjeto();
            if (!isset($arrWhitelistPorObjeto[$strIdObjeto])) {
                $arrWhitelistPorObjeto[$strIdObjeto] = $objWhitelistDTO;
            }
        }
        $arrObjMdPetWhitelistDTO = array_values($arrWhitelistPorObjeto);

        $arrIdObjetos = [];
        foreach ($arrObjMdPetWhitelistDTO as $objWhitelistDTO) {
            $arrIdObjetos[] = $objWhitelistDTO->getDblIdObjeto();
        }

        $arrObjetosResolvidos = $this->resolverObjetos(
            $objMdPetWhitelistDTO->getStrStaTipoObjeto(),
            $arrIdObjetos
        );

        foreach ($arrObjMdPetWhitelistDTO as $objWhitelistDTO) {
            $strIdObjeto = (string) $objWhitelistDTO->getDblIdObjeto();
            if (isset($arrObjetosResolvidos[$strIdObjeto])) {
                $objWhitelistDTO->setStrIdentificadorObjeto(
                    $arrObjetosResolvidos[$strIdObjeto]['identificador']
                );
                $objWhitelistDTO->setStrDescricaoObjeto(
                    $arrObjetosResolvidos[$strIdObjeto]['descricao']
                );
            } else {
                $objWhitelistDTO->setStrIdentificadorObjeto($strIdObjeto);
                $objWhitelistDTO->setStrDescricaoObjeto('Objeto nao localizado');
            }
        }

        usort($arrObjMdPetWhitelistDTO, static function ($objA, $objB) {
            $numComparacao = strnatcasecmp(
                (string) $objA->getStrIdentificadorObjeto(),
                (string) $objB->getStrIdentificadorObjeto()
            );

            if ($numComparacao !== 0) {
                return $numComparacao;
            }

            return (int) $objA->getDblIdObjeto() <=> (int) $objB->getDblIdObjeto();
        });

        return $arrObjMdPetWhitelistDTO;
    }

    /**
     * Counts entries from one explicit context without an administrative read resource.
     */
    protected function contarContextoInternoConectado(MdPetWhitelistDTO $objMdPetWhitelistDTO): int
    {
        $this->validarContexto($objMdPetWhitelistDTO);

        $objFiltroDTO = $this->montarFiltroContexto($objMdPetWhitelistDTO);
        $objFiltroDTO->retDblIdObjeto();
        $objFiltroDTO->setDistinct(true);

        $objMdPetWhitelistBD = new MdPetWhitelistBD($this->getObjInfraIBanco());
        return count($objMdPetWhitelistBD->listar($objFiltroDTO));
    }

    /**
     * Checks one semantic key without depending on an administrative session.
     */
    protected function verificarPermitidoInternoConectado(MdPetWhitelistDTO $objMdPetWhitelistDTO): bool
    {
        $this->validarContexto($objMdPetWhitelistDTO);

        if (!$objMdPetWhitelistDTO->isSetDblIdObjeto()) {
            throw new InfraException('O objeto da whitelist nao foi informado.');
        }

        $arrIdObjetos = $this->normalizarIds([$objMdPetWhitelistDTO->getDblIdObjeto()]);
        if (count($arrIdObjetos) !== 1) {
            throw new InfraException('O objeto da whitelist e invalido.');
        }

        $objFiltroDTO = $this->montarFiltroContexto($objMdPetWhitelistDTO);
        $objFiltroDTO->setDblIdObjeto($arrIdObjetos[0]);

        $objMdPetWhitelistBD = new MdPetWhitelistBD($this->getObjInfraIBanco());
        return $objMdPetWhitelistBD->contar($objFiltroDTO) > 0;
    }

    /** Pesquisa contatos vinculados pelo documento e tipo, incluindo desativados. */
    protected function pesquisarContatosSuspensaoConectado(MdPetWhitelistDTO $objDTO): array
    {
        $this->validarContextoSuspensao($objDTO);
        SessaoSEI::getInstance()->validarPermissao('md_pet_exc_suspensao_auto_listar');
        SessaoSEI::getInstance()->validarAuditarPermissao(
            'md_pet_exc_suspensao_auto_selecionar',
            __METHOD__,
            $this->montarFiltroSuspensao()
        );

        return $this->buscarContatosSuspensao($objDTO);
    }

    /** Lista as excecoes com dados de contato carregados em lote. */
    protected function listarExcecoesSuspensaoConectado(MdPetWhitelistDTO $objDTO): array
    {
        $this->validarContextoSuspensao($objDTO);
        $objFiltroDTO = $this->montarFiltroSuspensao();
        SessaoSEI::getInstance()->validarAuditarPermissao(
            'md_pet_exc_suspensao_auto_listar',
            __METHOD__,
            $objFiltroDTO
        );

        return $this->recuperarExcecoesSuspensaoInterno($objDTO);
    }

    /** Leitura compartilhada pela tela e pelos agendamentos, sem recurso administrativo. */
    protected function recuperarExcecoesSuspensaoInternoConectado(MdPetWhitelistDTO $objDTO): array
    {
        $this->validarContextoSuspensao($objDTO);
        $objFiltroDTO = $this->montarFiltroSuspensao();
        $objFiltroDTO->retTodos();
        $objBD = new MdPetWhitelistBD($this->getObjInfraIBanco());
        $arrExcecoes = $objBD->listar($objFiltroDTO);
        if (empty($arrExcecoes)) {
            return [];
        }

        $objContatoDTO = $this->montarConsultaContatosSuspensao();
        $objContatoDTO->setNumIdContato(
            InfraArray::converterArrInfraDTO($arrExcecoes, 'IdObjeto'),
            InfraDTO::$OPER_IN
        );
        $arrContatos = (new MdPetContatoRN())->listarRN0325($objContatoDTO);
        $arrPorId = [];
        foreach ($arrContatos as $objContato) {
            $arrPorId[(string) $objContato->getNumIdContato()] = $objContato;
        }

        foreach ($arrExcecoes as $objExcecao) {
            $strId = (string) $objExcecao->getDblIdObjeto();
            $objExcecao->setStrNaturezaContato('');
            $objExcecao->setStrDocumentoContato('');
            $objExcecao->setStrSituacaoContato('Contato nao localizado');
            $objExcecao->setStrTipoContato('');
            $objExcecao->setStrDescricaoObjeto('Contato ID ' . $strId);
            if (isset($arrPorId[$strId])) {
                $objContato = $arrPorId[$strId];
                $objExcecao->setStrNaturezaContato($objContato->getStrStaNatureza());
                $objExcecao->setStrDocumentoContato(
                    $objContato->getStrStaNatureza() === ContatoRN::$TN_PESSOA_FISICA
                        ? InfraUtil::formatarCpf($objContato->getDblCpf())
                        : InfraUtil::formatarCnpj($objContato->getStrCnpj())
                );
                $objExcecao->setStrSituacaoContato(
                    $objContato->getStrSinAtivo() === 'S' ? 'Ativo' : 'Desativado'
                );
                $objExcecao->setStrTipoContato($objContato->getStrNomeTipoContato());
                $objExcecao->setStrDescricaoObjeto($objContato->getStrNome());
            }
        }

        return $arrExcecoes;
    }

    /** Inclui o primeiro contato elegivel em S/C e devolve o ID da associacao no DTO. */
    protected function cadastrarExcecoesSuspensaoControlado(MdPetWhitelistDTO $objDTO): int
    {
        $this->validarContextoSuspensao($objDTO);
        SessaoSEI::getInstance()->validarPermissao('md_pet_exc_suspensao_auto_listar');
        SessaoSEI::getInstance()->validarPermissao('md_pet_exc_suspensao_auto_selecionar');
        SessaoSEI::getInstance()->validarPermissao('md_pet_exc_suspensao_auto_cadastrar');
        if ($objDTO->isSetArrIdObjetos()) {
            (new InfraException())->lancarValidacao('Informe somente a natureza e o CPF ou CNPJ para inclusão automática.');
        }
        $arrContatos = $this->buscarContatosSuspensao($objDTO);
        if (empty($arrContatos)) {
            (new InfraException())->lancarValidacao('Nenhum contato elegível localizado para a natureza e o documento informados.');
        }
        [$numIdContato] = $this->normalizarIds([$arrContatos[0]->getNumIdContato()]);
        $objAuditoriaDTO = $this->montarFiltroSuspensao();
        $objAuditoriaDTO->setArrIdObjetos([$numIdContato]);
        SessaoSEI::getInstance()->validarAuditarPermissao(
            'md_pet_exc_suspensao_auto_cadastrar',
            __METHOD__,
            $objAuditoriaDTO
        );

        $objFiltroDTO = $this->montarFiltroSuspensao();
        $objFiltroDTO->setDblIdObjeto($numIdContato);
        $objBD = new MdPetWhitelistBD($this->getObjInfraIBanco());
        if ($objBD->contar($objFiltroDTO) > 0) {
            (new InfraException())->lancarValidacao('O primeiro contato elegível já possui esta exceção. Nenhum contato foi incluído.');
        }

        $objNovoDTO = $this->montarFiltroSuspensao();
        $objNovoDTO->setDblIdObjeto($numIdContato);
        $objBD->cadastrar($objNovoDTO);
        $objDTO->setNumIdMdPetWhitelist($objNovoDTO->getNumIdMdPetWhitelist());

        return 1;
    }

    /** Exclui uma associacao sem alterar o contato ou outras finalidades. */
    protected function excluirExcecaoSuspensaoControlado(MdPetWhitelistDTO $objDTO): void
    {
        $this->validarContextoSuspensao($objDTO);
        SessaoSEI::getInstance()->validarPermissao('md_pet_exc_suspensao_auto_listar');
        if (!$objDTO->isSetNumIdMdPetWhitelist()) {
            (new InfraException())->lancarValidacao('A exceção não foi informada.');
        }
        $arrIds = $this->normalizarIds([$objDTO->getNumIdMdPetWhitelist()]);
        $objAuditoriaDTO = $this->montarFiltroSuspensao();
        $objAuditoriaDTO->setNumIdMdPetWhitelist($arrIds[0]);
        SessaoSEI::getInstance()->validarAuditarPermissao(
            'md_pet_exc_suspensao_auto_excluir',
            __METHOD__,
            $objAuditoriaDTO
        );
        $objFiltroDTO = $this->montarFiltroSuspensao();
        $objFiltroDTO->setNumIdMdPetWhitelist($arrIds[0]);
        $objFiltroDTO->retNumIdMdPetWhitelist();
        $objBD = new MdPetWhitelistBD($this->getObjInfraIBanco());
        $objExcecao = $objBD->consultar($objFiltroDTO);
        if ($objExcecao === null) {
            (new InfraException())->lancarValidacao('Exceção de suspensão automática não encontrada.');
        }
        $objBD->excluir($objExcecao);
    }

    private function montarFiltroSuspensao(): MdPetWhitelistDTO
    {
        $objDTO = new MdPetWhitelistDTO();
        $objDTO->setStrStaFinalidade(self::FINALIDADE_SUSPENSAO_AUTOMATICA);
        $objDTO->setStrStaTipoObjeto(self::TIPO_CONTATO);
        return $objDTO;
    }

    private function validarContextoSuspensao(MdPetWhitelistDTO $objDTO): void
    {
        if (($objDTO->isSetStrStaFinalidade()
                && $objDTO->getStrStaFinalidade() !== self::FINALIDADE_SUSPENSAO_AUTOMATICA)
            || ($objDTO->isSetStrStaTipoObjeto()
                && $objDTO->getStrStaTipoObjeto() !== self::TIPO_CONTATO)) {
            throw new InfraException('Contexto invalido para excecoes de suspensao automatica.');
        }
    }

    private function validarSincronizacaoContexto(array $arrConfiguracao): void
    {
        if ($arrConfiguracao['recurso_escrita'] === null) {
            throw new InfraException('Este contexto exige inclusao e exclusao individuais com permissao propria.');
        }
    }

    private function montarConsultaContatosSuspensao(): ContatoDTO
    {
        $objDTO = new ContatoDTO();
        $objDTO->setBolExclusaoLogica(false);
        $objDTO->retNumIdContato();
        $objDTO->retStrStaNatureza();
        $objDTO->retStrNome();
        $objDTO->retDblCpf();
        $objDTO->retStrCnpj();
        $objDTO->retStrSinAtivo();
        $objDTO->retStrNomeTipoContato();
        $objDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);
        $objDTO->setOrdNumIdContato(InfraDTO::$TIPO_ORDENACAO_ASC);
        return $objDTO;
    }

    private function buscarContatosSuspensao(MdPetWhitelistDTO $objDTO): array
    {
        if (!$objDTO->isSetStrNaturezaContato() || !$objDTO->isSetStrDocumentoContato()) {
            (new InfraException())->lancarValidacao('Informe a natureza e o CPF ou CNPJ.');
        }
        $strNatureza = $objDTO->getStrNaturezaContato();
        $strDocumento = $objDTO->getStrDocumentoContato();
        if (!is_string($strNatureza) || !is_string($strDocumento)) {
            (new InfraException())->lancarValidacao('Natureza ou documento inválido.');
        }
        $strDocumento = trim($strDocumento);
        $objContatoDTO = $this->montarConsultaContatosSuspensao();
        $objContatoDTO->setStrStaNatureza($strNatureza);
        if ($strNatureza === ContatoRN::$TN_PESSOA_FISICA) {
            if (preg_match('/^(?:[0-9]{11}|[0-9]{3}\\.[0-9]{3}\\.[0-9]{3}-[0-9]{2})$/D', $strDocumento) !== 1
                || !InfraUtil::validarCpf($strDocumento, false)) {
                (new InfraException())->lancarValidacao('CPF inválido. Informe o documento completo.');
            }
            $objContatoDTO->setDblCpf(str_replace(['.', '-'], '', $strDocumento));
            $strSiglaOrgao = SessaoSEI::getInstance()->getStrSiglaOrgaoSistema();
            if (!is_string($strSiglaOrgao) || trim($strSiglaOrgao) === '') {
                throw new InfraException('Órgão não identificado.');
            }
            $strParametro = $strSiglaOrgao . '_ID_TIPO_CONTATO_USUARIOS_EXTERNOS';
            $mixIdTipoContato = (new InfraParametro($this->getObjInfraIBanco()))->getValor($strParametro);
            $numIdTipoContato = filter_var($mixIdTipoContato, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if ($numIdTipoContato === false) {
                throw new InfraException('Parâmetro ' . $strParametro . ' deve conter um identificador de tipo de contato válido.');
            }
            $objContatoDTO->setNumIdTipoContato($numIdTipoContato);
        } elseif ($strNatureza === ContatoRN::$TN_PESSOA_JURIDICA) {
            $strDocumento = strtoupper($strDocumento);
            $strCnpj = InfraUtil::prepararCnpj($strDocumento);
            if (preg_match('/^(?:[A-Z0-9]{12}[0-9]{2}|[A-Z0-9]{2}\\.[A-Z0-9]{3}\\.[A-Z0-9]{3}\\/[A-Z0-9]{4}-[0-9]{2})$/D', $strDocumento) !== 1
                || $strCnpj === '00000000000000'
                || !InfraUtil::validarCnpj($strDocumento, false)) {
                (new InfraException())->lancarValidacao('CNPJ inválido. Informe o documento completo.');
            }
            $objContatoDTO->setStrCnpj($strCnpj);
        } else {
            (new InfraException())->lancarValidacao('Selecione Pessoa Física ou Pessoa Jurídica.');
        }
        $arrContatos = (new MdPetContatoRN())->listarRN0325($objContatoDTO);
        if (empty($arrContatos)) {
            return [];
        }
        $objVinculoDTO = new MdPetVinculoDTO();
        $objVinculoDTO->setNumIdContato(
            InfraArray::converterArrInfraDTO($arrContatos, 'IdContato'),
            InfraDTO::$OPER_IN
        );
        $arrVinculos = (new MdPetVinculoRN())->filtrarContatosSuspensao($objVinculoDTO);
        $arrPorId = array_fill_keys(InfraArray::converterArrInfraDTO($arrVinculos, 'IdContato'), true);
        return array_values(array_filter($arrContatos, static function (ContatoDTO $objContato) use ($arrPorId): bool {
            return isset($arrPorId[(string) $objContato->getNumIdContato()]);
        }));
    }

    private function montarFiltroContexto(MdPetWhitelistDTO $objMdPetWhitelistDTO): MdPetWhitelistDTO
    {
        $objFiltroDTO = new MdPetWhitelistDTO();
        $objFiltroDTO->setStrStaFinalidade($objMdPetWhitelistDTO->getStrStaFinalidade());
        $objFiltroDTO->setStrStaTipoObjeto($objMdPetWhitelistDTO->getStrStaTipoObjeto());

        return $objFiltroDTO;
    }

    private function validarContexto(MdPetWhitelistDTO $objMdPetWhitelistDTO): array
    {
        $strFinalidade = $objMdPetWhitelistDTO->getStrStaFinalidade();
        $strTipoObjeto = $objMdPetWhitelistDTO->getStrStaTipoObjeto();

        if (!isset(self::FINALIDADES[$strFinalidade])) {
            throw new InfraException('Finalidade de whitelist desconhecida.');
        }

        if (!in_array($strTipoObjeto, self::TIPOS_OBJETO, true)) {
            throw new InfraException('Tipo de objeto da whitelist desconhecido.');
        }

        $arrConfiguracaoFinalidade = self::FINALIDADES[$strFinalidade];
        if (!in_array($strTipoObjeto, $arrConfiguracaoFinalidade['tipos'], true)) {
            throw new InfraException('Tipo de objeto nao permitido para a finalidade da whitelist.');
        }

        return $arrConfiguracaoFinalidade;
    }

    private function normalizarIds($arrIdObjetos): array
    {
        if (!is_array($arrIdObjetos)) {
            (new InfraException())->lancarValidacao('A lista de objetos da whitelist é inválida.');
        }

        $arrNormalizados = [];
        $strPhpIntMax = (string) PHP_INT_MAX;

        foreach ($arrIdObjetos as $mixIdObjeto) {
            if (!is_string($mixIdObjeto) && !is_int($mixIdObjeto) && !is_float($mixIdObjeto)) {
                (new InfraException())->lancarValidacao('A lista de objetos da whitelist contém um identificador inválido.');
            }
            $strIdObjeto = trim((string) $mixIdObjeto);
            if ($strIdObjeto === '' || preg_match('/^[0-9]+$/D', $strIdObjeto) !== 1) {
                (new InfraException())->lancarValidacao('A lista de objetos da whitelist contém um identificador inválido.');
            }

            $strIdObjeto = ltrim($strIdObjeto, '0');
            if ($strIdObjeto === '') {
                (new InfraException())->lancarValidacao('A lista de objetos da whitelist deve conter apenas identificadores positivos.');
            }

            if (strlen($strIdObjeto) > strlen($strPhpIntMax)
                || (strlen($strIdObjeto) === strlen($strPhpIntMax) && strcmp($strIdObjeto, $strPhpIntMax) > 0)) {
                (new InfraException())->lancarValidacao('A lista de objetos da whitelist contém um identificador fora do limite suportado.');
            }

            $numIdObjeto = (int) $strIdObjeto;
            $arrNormalizados[(string) $numIdObjeto] = $numIdObjeto;
        }

        return array_values($arrNormalizados);
    }

    private function validarObjetosResolvidos(array $arrIdObjetos, array $arrObjetosResolvidos): void
    {
        $arrNaoLocalizados = [];

        foreach ($arrIdObjetos as $numIdObjeto) {
            if (!isset($arrObjetosResolvidos[(string) $numIdObjeto])) {
                $arrNaoLocalizados[] = $numIdObjeto;
            }
        }

        if (!empty($arrNaoLocalizados)) {
            throw new InfraException(
                'Objeto(s) da whitelist nao localizado(s): ' . implode(', ', $arrNaoLocalizados) . '.'
            );
        }
    }

    private function resolverObjetos(string $strTipoObjeto, array $arrIdObjetos): array
    {
        if (empty($arrIdObjetos)) {
            return [];
        }

        switch ($strTipoObjeto) {
            case self::TIPO_PROCESSO:
                return $this->resolverProcessos($arrIdObjetos);

            case self::TIPO_DOCUMENTO:
                return $this->resolverDocumentos($arrIdObjetos);

            case self::TIPO_CONTATO:
                return $this->resolverContatos($arrIdObjetos);
        }

        throw new InfraException('Tipo de objeto da whitelist desconhecido.');
    }

    private function resolverProcessos(array $arrIdObjetos): array
    {
        $objProcedimentoDTO = new ProcedimentoDTO();
        $objProcedimentoDTO->setDblIdProcedimento($arrIdObjetos, InfraDTO::$OPER_IN);
        $objProcedimentoDTO->retDblIdProcedimento();
        $objProcedimentoDTO->retStrProtocoloProcedimentoFormatado();
        $objProcedimentoDTO->retStrNomeTipoProcedimento();

        $arrObjProcedimentoDTO = (new ProcedimentoRN())->listarRN0278($objProcedimentoDTO);
        $arrResolvidos = [];

        foreach ($arrObjProcedimentoDTO as $objDTO) {
            $arrResolvidos[(string) $objDTO->getDblIdProcedimento()] = [
                'identificador' => (string) $objDTO->getStrProtocoloProcedimentoFormatado(),
                'descricao' => (string) $objDTO->getStrNomeTipoProcedimento(),
            ];
        }

        return $arrResolvidos;
    }

    private function resolverDocumentos(array $arrIdObjetos): array
    {
        $objDocumentoDTO = new DocumentoDTO();
        $objDocumentoDTO->setDblIdDocumento($arrIdObjetos, InfraDTO::$OPER_IN);
        $objDocumentoDTO->retDblIdDocumento();
        $objDocumentoDTO->retStrProtocoloDocumentoFormatado();
        $objDocumentoDTO->retStrNumero();
        $objDocumentoDTO->retStrNomeSerie();
        $objDocumentoDTO->retStrDescricaoProtocolo();

        $arrObjDocumentoDTO = (new DocumentoRN())->listarRN0008($objDocumentoDTO);
        $arrResolvidos = [];

        foreach ($arrObjDocumentoDTO as $objDTO) {
            $strDescricao = trim($objDTO->getStrNomeSerie() . ' ' . $objDTO->getStrNumero());
            if ($strDescricao === '') {
                $strDescricao = (string) $objDTO->getStrDescricaoProtocolo();
            }

            $arrResolvidos[(string) $objDTO->getDblIdDocumento()] = [
                'identificador' => (string) $objDTO->getStrProtocoloDocumentoFormatado(),
                'descricao' => $strDescricao,
            ];
        }

        return $arrResolvidos;
    }

    private function resolverContatos(array $arrIdObjetos): array
    {
        $objContatoDTO = new ContatoDTO();
        $objContatoDTO->setNumIdContato($arrIdObjetos, InfraDTO::$OPER_IN);
        $objContatoDTO->retNumIdContato();
        $objContatoDTO->retStrSigla();
        $objContatoDTO->retStrNome();

        $arrObjContatoDTO = (new ContatoRN())->listarRN0325($objContatoDTO);
        $arrResolvidos = [];

        foreach ($arrObjContatoDTO as $objDTO) {
            $strIdentificador = trim((string) $objDTO->getStrSigla());
            if ($strIdentificador === '') {
                $strIdentificador = (string) $objDTO->getStrNome();
            }

            $arrResolvidos[(string) $objDTO->getNumIdContato()] = [
                'identificador' => $strIdentificador,
                'descricao' => (string) $objDTO->getStrNome(),
            ];
        }

        return $arrResolvidos;
    }
}
