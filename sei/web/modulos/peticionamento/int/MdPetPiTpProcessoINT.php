<?php
/**
 * ANATEL
 *
 * Montagem de componentes de tela do Tipo de Processo do Peticionamento Interno.
 *
 * 08/09/2026 - criado por emerson_b.goncalves - ANATEL
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetPiTpProcessoINT extends InfraINT
{
    /**
     * Opcoes de Tipo de Indicacao de Interessado, com os rotulos da tela de administracao.
     *
     * @return array
     */
    public static function listarValoresIndicacaoInteressado()
    {
        return [
            MdPetPiTpProcessoRN::$II_INTERESSE_PESSOAL => 'Interesse Pessoal do Usuário',
            MdPetPiTpProcessoRN::$II_INTERESSE_UNIDADE => 'Interesse da Unidade do Usuário',
        ];
    }

    /**
     * Ajuda de cada opcao de Tipo de Indicacao de Interessado, na otica do administrador.
     *
     * @return array
     */
    public static function listarAjudasIndicacaoInteressado()
    {
        return [
            MdPetPiTpProcessoRN::$II_INTERESSE_PESSOAL => 'O Usuário que abrir será o Interessado do processo e controlará quem pode acessar documentos Restritos na Unidade de geração do processo: os demais usuários da Unidade de geração somente poderão acessar documentos Restritos se ele liberar o acesso manualmente na tela sobre o processo "Peticionamento Interno: Disponibilizar e Remover Acesso".' . "\n\n" . 'As regras diferenciadas de acesso a documentos Restritos concedido manualmente vale apenas para a Unidade de geração do processo. Com o envio do processo para outras Unidades, nelas o acesso aos documentos funciona conforme as regras nativas do SEI, ou seja, os documentos Restritos serão acessados nas Unidades de tramitação como sempre.' . "\n\n" . 'Use para processos que envolver assunto pessoal do Usuário.',
            MdPetPiTpProcessoRN::$II_INTERESSE_UNIDADE => 'A Unidade onde abrir será o Interessado do processo. O acesso aos documentos seguirá as regras nativas do SEI mesmo na Unidade de geração do processo: todos os usuários da Unidade de geração acessarão os documentos do processo, inclusive os documentos Restritos.' . "\n\n" . 'Use para processos que envolver assunto institucional.',
        ];
    }

    /**
     * Opcoes de Comportamento do Nivel de Acesso de cada documento.
     *
     * @return array
     */
    public static function listarValoresTipoNivelAcesso()
    {
        return [
            MdPetPiTpProcessoRN::$TNA_USUARIO => 'Usuário indica diretamente',
            MdPetPiTpProcessoRN::$TNA_PADRAO => 'Padrão pré-definido',
        ];
    }

    /**
     * Ajuda de cada opcao de Comportamento do Nivel de Acesso, na otica do administrador.
     *
     * @return array
     */
    public static function listarAjudasTipoNivelAcesso(): array
    {
        return [
            MdPetPiTpProcessoRN::$TNA_USUARIO => 'O Usuário escolherá o Nível de Acesso diretamente, entre os Níveis de Acesso permitidos para o Tipo de Processo.',
            MdPetPiTpProcessoRN::$TNA_PADRAO => 'O Usuário não escolherá o Nível de Acesso, pois terá o Nível de Acesso e a Hipótese Legal pré-definidos.',
        ];
    }

    /**
     * Opcoes de aplicabilidade do Documento Principal.
     *
     * @return array
     */
    public static function listarValoresDocPrincipal()
    {
        return [
            MdPetPiTpProcessoRN::$DOC_GERADO => 'Gerado (Editor e Modelo)',
            MdPetPiTpProcessoRN::$DOC_EXTERNO => 'Externo (Anexação de Arquivo)',
            MdPetPiTpProcessoRN::$DOC_FORMULARIO => 'Formulário (Preenchimento de campos)',
        ];
    }

    /**
     * Ajuda das opcoes de aplicabilidade do Documento Principal.
     *
     * @return array<string, string>
     */
    public static function listarAjudasDocPrincipal(): array
    {
        return [
            MdPetPiTpProcessoRN::$DOC_GERADO => 'O Usuário deverá preencher um Documento de modelo pré-definido utilizando o Editor do SEI.' . "\n\n" . 'Neste caso, a seleção será entre Tipo de Documento parametrizado na Administração com Aplicabilidade de Documentos Internos ou Internos e Externos.',
            MdPetPiTpProcessoRN::$DOC_EXTERNO => 'O Usuário deverá anexar um Arquivo como Documento Principal.' . "\n\n" . 'Neste caso, a seleção será entre Tipo de Documento parametrizado na Administração com Aplicabilidade de Documentos Externos ou Internos e Externos.',
            MdPetPiTpProcessoRN::$DOC_FORMULARIO => 'O Usuário deverá preencher os campos de um Formulário como Documento Principal.' . "\n\n" . 'Neste caso, a seleção será entre Tipo de Documento parametrizado na Administração com Aplicabilidade de Formulário.',
        ];
    }

    /**
     * Ajuda da legenda e do Tipo de Documento de cada bloco da tela de administracao.
     *
     * @return array<string, array<string, string>>
     */
    public static function listarAjudasBlocoDocumento(): array
    {
        return [
            'DocPrinc' => [
                'legenda' => 'Documento Principal que o Usuário preenche ou anexa para abrir o processo. É obrigatório e somente aceita um Documento Principal por Peticionamento Interno.',
                'tipoDocumento' => 'As opções abaixo dependem do Tipo de Documento escolhido acima: Gerado lista os Tipos de Documento com Aplicabilidade de Documentos Internos ou Internos e Externos; Externo lista os de Aplicabilidade de Documentos Externos ou Internos e Externos; Formulário lista os Tipos de Documento de Aplicabilidade Formulário.' . "\n\n" . 'O Documento Principal admite um único Tipo de Documento.',
            ],
            'AnexoObrig' => [
                'legenda' => 'Esta opção não é obrigatória na parametrização, mas se for utilizada o Usuário será obrigado a anexar pelo menos um Arquivo para cada Tipo de Documento que for indicado aqui, respeitadas as Extensões de Arquivos Permitidas na Administração do SEI.',
                'tipoDocumento' => 'As opções abaixo são os Tipos de Documento parametrizados na Administração com Aplicabilidade de Documentos Externos ou Internos e Externos.' . "\n\n" . 'Cada Tipo de Documento pode ser adicionado uma única vez neste bloco.',
            ],
            'DocAuto' => [
                'legenda' => 'Esta opção não é obrigatória na parametrização, mas, se for utilizada, para cada Tipo de Documento indicado aqui o SEI vai gerar automaticamente um Documento no processo quando o Usuário conclui o Peticionamento.',
                'tipoDocumento' => 'As opções abaixo são os Tipos de Documento parametrizados na Administração com Aplicabilidade de Documentos Internos ou Internos e Externos.' . "\n\n" . 'Cada Tipo de Documento pode ser adicionado uma única vez neste bloco.',
            ],
        ];
    }

    /**
     * @param string $strStaIndicacaoInteressado
     * @return string
     */
    public static function obterDescricaoIndicacaoInteressado($strStaIndicacaoInteressado)
    {
        $arrDescricoes = self::listarValoresIndicacaoInteressado();

        return isset($arrDescricoes[$strStaIndicacaoInteressado]) ? $arrDescricoes[$strStaIndicacaoInteressado] : '';
    }

    /**
     * @param string $strStaDocPrincipal
     * @return string
     */
    public static function obterDescricaoDocPrincipal($strStaDocPrincipal)
    {
        $arrDescricoes = self::listarValoresDocPrincipal();

        return isset($arrDescricoes[$strStaDocPrincipal]) ? $arrDescricoes[$strStaDocPrincipal] : '';
    }

    /**
     * Niveis de Acesso permitidos para o Tipo de Processo. Delega para a parametrizacao do modulo,
     * que e compartilhada com o Peticionamento Externo.
     *
     * @param string|null $strPrimeiroItemValor
     * @param string|null $strPrimeiroItemDescricao
     * @param string|null $strValorItemSelecionado
     * @param int|null $numIdTipoProcedimento
     * @return string
     */
    public static function montarSelectNivelAcesso($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $numIdTipoProcedimento = null)
    {
        return MdPetTipoProcessoINT::montarSelectNivelAcesso($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $numIdTipoProcedimento);
    }

    /**
     * Hipoteses Legais **do core**, apenas as ativas, restritas ao nivel de acesso informado.
     *
     * Diferentemente do Peticionamento Externo, o Interno nao usa a parametrizacao
     * `md_pet_hipotese_legal`: como aqui tudo ja e interno, nao ha hipotese que precise ser filtrada.
     *
     * @param string|null $strPrimeiroItemValor
     * @param string|null $strPrimeiroItemDescricao
     * @param string|null $strValorItemSelecionado
     * @param string|null $strStaNivelAcesso
     * @return string
     */
    public static function montarSelectHipoteseLegal($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $strStaNivelAcesso = null)
    {
        if ($strStaNivelAcesso === null) {
            $strStaNivelAcesso = ProtocoloRN::$NA_RESTRITO;
        }

        return HipoteseLegalINT::montarSelectNomeBaseLegal($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $strStaNivelAcesso);
    }

    /**
     * Tipos de Documento ativos conforme a aplicabilidade, com os mesmos filtros do auto completar do modulo:
     * Gerado lista series internas, Externo lista series externas e Formulario lista series de formulario.
     *
     * @param string|null $strPrimeiroItemValor
     * @param string|null $strPrimeiroItemDescricao
     * @param string|null $strValorItemSelecionado
     * @param string $strStaAplicabilidade
     * @return string
     */
    public static function montarSelectTipoDocumento($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $strStaAplicabilidade)
    {
        $objSerieDTO = new SerieDTO();
        $objSerieDTO->retNumIdSerie();
        $objSerieDTO->retStrNome();
        $objSerieDTO->setStrSinAtivo('S');
        if ($strStaAplicabilidade === MdPetPiTpProcessoRN::$DOC_FORMULARIO) {
            $objSerieDTO->setStrStaAplicabilidade(SerieRN::$TA_FORMULARIO);
        } else {
            $strAplicabilidadeSerie = $strStaAplicabilidade === MdPetPiTpProcessoRN::$DOC_GERADO ? SerieRN::$TA_INTERNO : SerieRN::$TA_EXTERNO;
            $objSerieDTO->adicionarCriterio(['StaAplicabilidade'], [InfraDTO::$OPER_IN], [[SerieRN::$TA_INTERNO_EXTERNO, $strAplicabilidadeSerie]]);
        }
        $objSerieDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);

        $arrObjSerieDTO = (new SerieRN())->listarRN0646($objSerieDTO);

        return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjSerieDTO, 'IdSerie', 'Nome');
    }

    /**
     * Links assinados da lupa de Unidades, um por Orgao, porque a selecao de unidades e filtrada pelo Orgao escolhido
     * (mesmo desenho de OrgaoINT::montarRestricaoOrgaoUnidade do core).
     *
     * @return array [id do orgao => link assinado]
     */
    public static function montarLinksSelecaoUnidadePorOrgao()
    {
        $objOrgaoDTO = new OrgaoDTO();
        $objOrgaoDTO->retNumIdOrgao();

        $arrLinks = [];

        foreach ((new OrgaoRN())->listarRN1353($objOrgaoDTO) as $objDTO) {
            $numIdOrgao = $objDTO->getNumIdOrgao();
            $arrLinks[$numIdOrgao] = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=unidade_selecionar_orgao&tipo_selecao=2&id_object=objLupaUnidades&id_orgao=' . $numIdOrgao);
        }

        return $arrLinks;
    }

    /**
     * Especies de Documento Principal, com os rotulos curtos da listagem.
     *
     * @return array<string, string>
     */
    public static function listarOpcoesFiltroDocPrincipal(): array
    {
        return [
            MdPetPiTpProcessoRN::$DOC_GERADO => 'Gerado',
            MdPetPiTpProcessoRN::$DOC_EXTERNO => 'Externo',
            MdPetPiTpProcessoRN::$DOC_FORMULARIO => 'Formulário',
        ];
    }

    /**
     * Tipos de Processo para o auto completar da tela de cadastro.
     *
     * Diferentemente do Peticionamento Externo, lista tambem os Tipos de Processo sem Sugestao de
     * Assuntos. A critica ocorre na selecao e na gravacao, para que o administrador saiba o motivo.
     *
     * @param string $strPalavrasPesquisa
     * @param array|null $arrItensSelecionados
     * @return TipoProcedimentoDTO[]
     */
    public static function autoCompletarTipoProcedimento($strPalavrasPesquisa, $arrItensSelecionados = null)
    {
        $objTipoProcedimentoDTO = new TipoProcedimentoDTO();
        $objTipoProcedimentoDTO->retNumIdTipoProcedimento();
        $objTipoProcedimentoDTO->retStrNome();
        $objTipoProcedimentoDTO->setOrd('Nome', InfraDTO::$TIPO_ORDENACAO_ASC);

        $arrObjTipoProcedimentoDTO = (new TipoProcedimentoRN())->listarRN0244($objTipoProcedimentoDTO);

        $strPalavrasPesquisa = strtolower((string) $strPalavrasPesquisa);
        $arrRetorno = [];

        foreach ($arrObjTipoProcedimentoDTO as $objDTO) {
            if ($arrItensSelecionados !== null && in_array($objDTO->getNumIdTipoProcedimento(), $arrItensSelecionados)) {
                continue;
            }

            if ($strPalavrasPesquisa !== '' && strpos(strtolower($objDTO->getStrNome()), $strPalavrasPesquisa) === false) {
                continue;
            }

            $arrRetorno[] = $objDTO;
        }

        return $arrRetorno;
    }

    /**
     * Critica, via Ajax, a selecao de Tipo de Processo sem Sugestao de Assuntos.
     *
     * @param int|null $numIdTipoProcedimento
     * @return array vazio quando o tipo esta apto; com MensagemValidacao quando nao esta
     */
    public static function validarTipoProcessoComAssunto($numIdTipoProcedimento)
    {
        $arrRetorno = [];

        if (InfraString::isBolVazia($numIdTipoProcedimento)
            || (new MdPetPiTpProcessoRN())->possuiAssuntoParametrizado($numIdTipoProcedimento)) {
            return $arrRetorno;
        }

        $strNomeTipo = '';

        $objTipoProcedimentoDTO = new TipoProcedimentoDTO();
        $objTipoProcedimentoDTO->retStrNome();
        $objTipoProcedimentoDTO->setNumIdTipoProcedimento($numIdTipoProcedimento);
        $objTipoProcedimentoDTO = (new TipoProcedimentoRN())->consultarRN0267($objTipoProcedimentoDTO);

        if ($objTipoProcedimentoDTO !== null) {
            $strNomeTipo = ' ' . $objTipoProcedimentoDTO->getStrNome();
        }

        $arrRetorno['MensagemValidacao'] = 'Nao e possivel incluir este Tipo de Processo para Peticionamento Interno. '
            . 'Antes, solicite ao Administrador para editar o Tipo de Processo' . $strNomeTipo
            . ' e vincular as Classificacoes por Assunto.';

        return $arrRetorno;
    }

    /**
     * Confirma, via Ajax, se o numero informado em Modelo por Protocolo corresponde a um documento
     * do SEI com nivel de acesso Publico e devolve o protocolo formatado.
     *
     * @param string $strProtocoloFormatado
     * @return array com IdProtocolo e ProtocoloFormatado, ou com MensagemValidacao
     */
    public static function validarModeloPorProtocolo($strProtocoloFormatado)
    {
        if (InfraString::isBolVazia($strProtocoloFormatado)) {
            return [];
        }

        $objDocumentoDTO = new DocumentoDTO();
        $objDocumentoDTO->retDblIdDocumento();
        $objDocumentoDTO->retStrProtocoloDocumentoFormatado();
        $objDocumentoDTO->retStrStaNivelAcessoGlobalProtocolo();
        $objDocumentoDTO->setStrProtocoloDocumentoFormatado($strProtocoloFormatado);
        $objDocumentoDTO->setNumMaxRegistrosRetorno(1);

        $arrObjDocumentoDTO = (new DocumentoRN())->listarRN0008($objDocumentoDTO);

        if (count($arrObjDocumentoDTO) === 0) {
            return ['MensagemValidacao' => 'O numero informado em Modelo por Protocolo nao corresponde a um Documento do SEI.'];
        }

        if ($arrObjDocumentoDTO[0]->getStrStaNivelAcessoGlobalProtocolo() !== ProtocoloRN::$NA_PUBLICO) {
            return ['MensagemValidacao' => 'O documento informado em Modelo por Protocolo precisa ter nivel de acesso Publico.'];
        }

        return [
            'IdProtocolo' => $arrObjDocumentoDTO[0]->getDblIdDocumento(),
            'ProtocoloFormatado' => $arrObjDocumentoDTO[0]->getStrProtocoloDocumentoFormatado(),
        ];
    }

    /**
     * Le uma grid de documentos vinda do formulario.
     *
     * @param string $strJson
     * @param bool $bolComModelo aceita a chave modelo, usada nos blocos Gerados
     * @param bool $bolComComportamento aceita o Comportamento do Nivel de Acesso; sem ele o documento e pre-definido
     * @return array lista normalizada; a RN critica comportamento, nivel e hipotese
     */
    public static function decodificarGridDocumentos($strJson, $bolComModelo = false, $bolComComportamento = true)
    {
        $arrItens = self::decodificarJson($strJson);
        $arrRetorno = [];
        $arrIdsAdicionados = [];

        foreach ($arrItens as $arrItem) {
            if (!isset($arrItem['idSerie']) || !is_scalar($arrItem['idSerie'])) {
                continue;
            }

            $numIdSerie = (int) $arrItem['idSerie'];

            if ($numIdSerie <= 0 || in_array($numIdSerie, $arrIdsAdicionados, true)) {
                continue;
            }

            $arrIdsAdicionados[] = $numIdSerie;

            $strTipoNivelAcesso = MdPetPiTpProcessoRN::$TNA_PADRAO;

            if ($bolComComportamento) {
                $strTipoNivelAcesso = isset($arrItem['tipoNivelAcesso']) && is_scalar($arrItem['tipoNivelAcesso'])
                    && in_array((string) $arrItem['tipoNivelAcesso'], [MdPetPiTpProcessoRN::$TNA_USUARIO, MdPetPiTpProcessoRN::$TNA_PADRAO], true)
                    ? (string) $arrItem['tipoNivelAcesso']
                    : '';
            }

            $strNivelAcesso = isset($arrItem['nivelAcesso']) && is_scalar($arrItem['nivelAcesso']) ? (string) $arrItem['nivelAcesso'] : '';

            if ($strTipoNivelAcesso === MdPetPiTpProcessoRN::$TNA_USUARIO
                || !in_array($strNivelAcesso, [ProtocoloRN::$NA_PUBLICO, ProtocoloRN::$NA_RESTRITO], true)) {
                $strNivelAcesso = '';
            }

            $arrLinha = [
                'idSerie' => $numIdSerie,
                'nomeSerie' => isset($arrItem['nomeSerie']) && is_scalar($arrItem['nomeSerie']) ? (string) $arrItem['nomeSerie'] : '',
                'tipoNivelAcesso' => $strTipoNivelAcesso,
                'nivelAcesso' => $strNivelAcesso,
                'idHipoteseLegal' => $strNivelAcesso === ProtocoloRN::$NA_RESTRITO && isset($arrItem['idHipoteseLegal']) && (int) $arrItem['idHipoteseLegal'] > 0
                    ? (int) $arrItem['idHipoteseLegal']
                    : null,
                'nomeHipoteseLegal' => isset($arrItem['nomeHipoteseLegal']) && is_scalar($arrItem['nomeHipoteseLegal']) ? (string) $arrItem['nomeHipoteseLegal'] : '',
            ];

            if ($bolComModelo) {
                $arrLinha['idProtocoloModelo'] = isset($arrItem['idProtocoloModelo']) && (int) $arrItem['idProtocoloModelo'] > 0
                    ? (int) $arrItem['idProtocoloModelo']
                    : null;
                $arrLinha['protocoloModelo'] = isset($arrItem['protocoloModelo']) && is_scalar($arrItem['protocoloModelo']) ? (string) $arrItem['protocoloModelo'] : '';
            }

            $arrRetorno[] = $arrLinha;
        }

        return $arrRetorno;
    }

    /**
     * Restricao por Orgaos e Unidades gravada para o Tipo de Processo. Orgao sem Unidade libera o Orgao inteiro;
     * Orgao com Unidades libera so elas, como na restricao do Tipo de Processo do core.
     *
     * @param int $numIdMdPetPiTpProcesso
     * @return array ['orgaos' => [id do orgao => rotulo], 'unidades' => [id do orgao => [id da unidade => rotulo]]]
     */
    public static function listarRestricaoOrgaoUnidade($numIdMdPetPiTpProcesso)
    {
        $arrRestricao = ['orgaos' => [], 'unidades' => []];

        $objOrgaoDTO = new MdPetPiRelOrgaoDTO();
        $objOrgaoDTO->retNumIdOrgao();
        $objOrgaoDTO->retStrSiglaOrgao();
        $objOrgaoDTO->setNumIdMdPetPiTpProcesso($numIdMdPetPiTpProcesso);

        foreach ((new MdPetPiRelOrgaoRN())->listar($objOrgaoDTO) as $objDTO) {
            $arrRestricao['orgaos'][$objDTO->getNumIdOrgao()] = $objDTO->getStrSiglaOrgao();
        }

        $objUnidadeDTO = new MdPetPiRelUnidadeDTO();
        $objUnidadeDTO->retNumIdUnidade();
        $objUnidadeDTO->retStrSiglaUnidade();
        $objUnidadeDTO->retStrDescricaoUnidade();
        $objUnidadeDTO->retNumIdOrgaoUnidade();
        $objUnidadeDTO->retStrSiglaOrgaoUnidade();
        $objUnidadeDTO->setNumIdMdPetPiTpProcesso($numIdMdPetPiTpProcesso);
        $objUnidadeDTO->setOrdStrSiglaUnidade(InfraDTO::$TIPO_ORDENACAO_ASC);

        foreach ((new MdPetPiRelUnidadeRN())->listar($objUnidadeDTO) as $objDTO) {
            $arrRestricao['orgaos'][$objDTO->getNumIdOrgaoUnidade()] = $objDTO->getStrSiglaOrgaoUnidade();
            $arrRestricao['unidades'][$objDTO->getNumIdOrgaoUnidade()][$objDTO->getNumIdUnidade()] = UnidadeINT::formatarSiglaDescricao($objDTO->getStrSiglaUnidade(), $objDTO->getStrDescricaoUnidade());
        }

        asort($arrRestricao['orgaos'], SORT_STRING | SORT_FLAG_CASE);

        return $arrRestricao;
    }

    /**
     * Le a restricao por Orgaos e Unidades enviada pela tela: hdnOrgaos com os Orgaos e, para cada um, hdnOrgao<id>
     * com as Unidades, no formato das lupas do InfraPHP.
     *
     * @return array mesmo formato de listarRestricaoOrgaoUnidade
     */
    public static function lerRestricaoOrgaoUnidade()
    {
        $arrRestricao = ['orgaos' => [], 'unidades' => []];

        // O id 0 e valido: e o do primeiro Orgao cadastrado
        foreach (PaginaSEI::getInstance()->getArrOptionsSelect(MdPetPiEntradaINT::postStr('hdnOrgaos')) as $arrOpcaoOrgao) {
            if (!ctype_digit((string) $arrOpcaoOrgao[0])) {
                continue;
            }

            $numIdOrgao = (int) $arrOpcaoOrgao[0];

            $arrRestricao['orgaos'][$numIdOrgao] = isset($arrOpcaoOrgao[1]) ? (string) $arrOpcaoOrgao[1] : '';

            foreach (PaginaSEI::getInstance()->getArrOptionsSelect(MdPetPiEntradaINT::postStr('hdnOrgao' . $numIdOrgao)) as $arrOpcaoUnidade) {
                if (ctype_digit((string) $arrOpcaoUnidade[0])) {
                    $arrRestricao['unidades'][$numIdOrgao][(int) $arrOpcaoUnidade[0]] = isset($arrOpcaoUnidade[1]) ? (string) $arrOpcaoUnidade[1] : '';
                }
            }
        }

        return $arrRestricao;
    }

    /**
     * Valor do campo oculto de uma lupa do InfraPHP a partir de uma lista id => rotulo.
     *
     * @param array $arrItens
     * @return string
     */
    public static function gerarItensLupa($arrItens)
    {
        $arr = [];

        foreach ($arrItens as $numId => $strRotulo) {
            $arr[] = [$numId, $strRotulo];
        }

        return PaginaSEI::getInstance()->gerarItensLupa($arr);
    }

    /**
     * Serializa uma grid para o hidden do formulario. O texto do SEI e ISO-8859-1 e o json_encode exige UTF-8,
     * por isso converte antes; a saida fica em ASCII, com os acentos escapados.
     *
     * @param array $arrGrid
     * @return string
     */
    public static function codificarGrid($arrGrid)
    {
        array_walk_recursive($arrGrid, function (&$varValor) {
            if (is_string($varValor)) {
                $varValor = mb_convert_encoding($varValor, 'UTF-8', 'ISO-8859-1');
            }
        });

        $strJson = json_encode(array_values($arrGrid));

        return $strJson === false ? '[]' : $strJson;
    }

    /**
     * Le o JSON de uma grid e devolve os textos em ISO-8859-1, o encoding do SEI.
     *
     * @param string $strJson
     * @return array
     */
    private static function decodificarJson($strJson)
    {
        if (InfraString::isBolVazia($strJson)) {
            return [];
        }

        if (!mb_check_encoding($strJson, 'UTF-8')) {
            $strJson = mb_convert_encoding($strJson, 'UTF-8', 'ISO-8859-1');
        }

        $arrDecodificado = json_decode($strJson, true);

        if (!is_array($arrDecodificado)) {
            return [];
        }

        array_walk_recursive($arrDecodificado, function (&$varValor) {
            if (is_string($varValor)) {
                $varValor = mb_convert_encoding($varValor, 'ISO-8859-1', 'UTF-8');
            }
        });

        return array_filter($arrDecodificado, 'is_array');
    }
    /**
     * @param string $strStaAplicabilidade
     * @param array $arrGrid
     * @return MdPetPiDocPrincDTO|null
     */
    public static function montarDocPrincDTO($strStaAplicabilidade, $arrGrid)
    {
        if (count($arrGrid) === 0) {
            return null;
        }

        $arrLinha = $arrGrid[0];

        $objDTO = new MdPetPiDocPrincDTO();
        $objDTO->setStrStaAplicabilidade($strStaAplicabilidade);
        $objDTO->setStrStaTipoNivelAcesso($arrLinha['tipoNivelAcesso']);
        $objDTO->setStrStaNivelAcesso($arrLinha['nivelAcesso'] !== '' ? $arrLinha['nivelAcesso'] : null);
        $objDTO->setNumIdHipoteseLegal($arrLinha['idHipoteseLegal']);

        $objDTO->setNumIdSerie($arrLinha['idSerie']);

        $objDTO->setDblIdProtocoloModelo(
            $strStaAplicabilidade === MdPetPiTpProcessoRN::$DOC_GERADO && isset($arrLinha['idProtocoloModelo'])
                ? $arrLinha['idProtocoloModelo']
                : null
        );

        return $objDTO;
    }

    /**
     * @param array $arrGrid
     * @return MdPetPiAnexoObrigDTO[]
     */
    public static function montarAnexoObrigDTO($arrGrid)
    {
        $arrRetorno = [];

        foreach ($arrGrid as $arrLinha) {
            $objDTO = new MdPetPiAnexoObrigDTO();
            $objDTO->setNumIdSerie($arrLinha['idSerie']);
            $objDTO->setStrStaTipoNivelAcesso($arrLinha['tipoNivelAcesso']);
            $objDTO->setStrStaNivelAcesso($arrLinha['nivelAcesso'] !== '' ? $arrLinha['nivelAcesso'] : null);
            $objDTO->setNumIdHipoteseLegal($arrLinha['idHipoteseLegal']);
            $arrRetorno[] = $objDTO;
        }

        return $arrRetorno;
    }

    /**
     * @param array $arrGrid
     * @return MdPetPiDocAutoDTO[]
     */
    public static function montarDocAutoDTO($arrGrid)
    {
        $arrRetorno = [];

        foreach ($arrGrid as $arrLinha) {
            $objDTO = new MdPetPiDocAutoDTO();
            $objDTO->setNumIdSerie($arrLinha['idSerie']);
            $objDTO->setDblIdProtocoloModelo(isset($arrLinha['idProtocoloModelo']) ? $arrLinha['idProtocoloModelo'] : null);
            $objDTO->setStrStaNivelAcesso($arrLinha['nivelAcesso'] !== '' ? $arrLinha['nivelAcesso'] : null);
            $objDTO->setNumIdHipoteseLegal($arrLinha['idHipoteseLegal']);
            $arrRetorno[] = $objDTO;
        }

        return $arrRetorno;
    }

    /**
     * Orgaos liberados por inteiro: os da restricao sem nenhuma Unidade.
     *
     * @param array $arrRestricao formato de listarRestricaoOrgaoUnidade
     * @return MdPetPiRelOrgaoDTO[]
     */
    public static function montarRelOrgaoDTO($arrRestricao)
    {
        $arrRetorno = [];

        foreach (array_keys($arrRestricao['orgaos']) as $numIdOrgao) {
            if (!empty($arrRestricao['unidades'][$numIdOrgao])) {
                continue;
            }

            $objDTO = new MdPetPiRelOrgaoDTO();
            $objDTO->setNumIdOrgao($numIdOrgao);
            $arrRetorno[] = $objDTO;
        }

        return $arrRetorno;
    }

    /**
     * Unidades liberadas: as indicadas dentro de cada Orgao da restricao.
     *
     * @param array $arrRestricao formato de listarRestricaoOrgaoUnidade
     * @return MdPetPiRelUnidadeDTO[]
     */
    public static function montarRelUnidadeDTO($arrRestricao)
    {
        $arrRetorno = [];

        foreach (array_keys($arrRestricao['orgaos']) as $numIdOrgao) {
            foreach (array_keys($arrRestricao['unidades'][$numIdOrgao] ?? []) as $numIdUnidade) {
                $objDTO = new MdPetPiRelUnidadeDTO();
                $objDTO->setNumIdUnidade($numIdUnidade);
                $arrRetorno[] = $objDTO;
            }
        }

        return $arrRetorno;
    }
}
