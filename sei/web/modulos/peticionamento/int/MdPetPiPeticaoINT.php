<?php
/**
 * ANATEL
 *
 * Dados de tela do Peticionamento Interno.
 *
 * 17/09/2026 - criado por emerson_b.goncalves - ANATEL
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetPiPeticaoINT extends InfraINT
{
    /**
     * Prefixo do atributo de sessão com o conteúdo do Documento Principal Gerado ou Formulário, um por Tipo de Processo.
     */
    public static $ATRIBUTO_CONTEUDO_DOC_PRINCIPAL = 'md_pet_pi_conteudo_doc_principal';

    /**
     * Atributo de sessão com os arquivos que o próprio usuário enviou para a área temporária.
     * Nenhum caminho no sistema de arquivos é montado com nome que não esteja nesta lista.
     */
    public static $ATRIBUTO_ARQUIVOS_UPLOAD = 'md_pet_pi_arquivos_upload';

    /** Identificador da linha do Documento Principal Gerado ou Formulário na lista de documentos */
    public static $ID_DOC_PRINCIPAL_SEM_ARQUIVO = 'DOC_PRINCIPAL';

    /** Posições das colunas na lista de documentos, iguais nos três quadros */
    public static $COL_IDENTIFICADOR = 0;
    public static $COL_NOME_ARQUIVO = 1;
    public static $COL_TAMANHO_BYTES = 7;
    public static $COL_ID_SERIE = 8;
    public static $COL_COMPLEMENTO = 9;
    public static $COL_ID_HIPOTESE_LEGAL = 10;
    public static $COL_ID_TIPO_CONFERENCIA = 11;
    public static $COL_STA_FORMATO = 12;
    public static $COL_STA_NIVEL_ACESSO = 13;

    /**
     * Classificações por assunto sugeridas no core para o tipo de processo, na ordem do core e no formato "código - descrição".
     *
     * @param int $numIdTipoProcedimento
     * @return string[]
     */
    public static function listarAssuntosTipoProcedimento($numIdTipoProcedimento)
    {
        $objRelTipoProcedimentoAssuntoDTO = new RelTipoProcedimentoAssuntoDTO();
        $objRelTipoProcedimentoAssuntoDTO->retStrCodigoEstruturadoAssunto();
        $objRelTipoProcedimentoAssuntoDTO->retStrDescricaoAssunto();
        $objRelTipoProcedimentoAssuntoDTO->setNumIdTipoProcedimento($numIdTipoProcedimento);
        $objRelTipoProcedimentoAssuntoDTO->setOrdNumSequencia(InfraDTO::$TIPO_ORDENACAO_ASC);

        $arrAssuntos = [];

        foreach ((new RelTipoProcedimentoAssuntoRN())->listarRN0192($objRelTipoProcedimentoAssuntoDTO) as $objDTO) {
            $arrAssuntos[] = AssuntoINT::formatarCodigoDescricaoRI0568($objDTO->getStrCodigoEstruturadoAssunto(), $objDTO->getStrDescricaoAssunto());
        }

        return $arrAssuntos;
    }

    /**
     * Rótulos da indicação de interessado na tela do usuário, em primeira pessoa.
     *
     * @return array
     */
    public static function listarValoresIndicacaoInteressado()
    {
        return [
            MdPetPiTpProcessoRN::$II_INTERESSE_PESSOAL => 'Seu Interesse Pessoal',
            MdPetPiTpProcessoRN::$II_INTERESSE_UNIDADE => 'Interesse desta Unidade',
        ];
    }

    /**
     * Ajuda de cada indicação de interessado na tela do usuário, equivalente à da administração.
     *
     * @return array
     */
    public static function listarAjudasIndicacaoInteressado()
    {
        return [
            MdPetPiTpProcessoRN::$II_INTERESSE_PESSOAL => 'Você será o Interessado do processo a ser aberto e controlará quem pode acessar documentos Restritos na Unidade de geração do processo: os demais usuários da Unidade de geração somente poderão acessar documentos Restritos se você liberar o acesso manualmente na tela sobre o processo "Peticionamento Interno: Disponibilizar e Remover Acesso".' . "\n\n" . 'As regras diferenciadas de acesso a documentos Restritos concedido manualmente vale apenas para a Unidade de geração do processo. Com o envio do processo para outras Unidades, nelas o acesso aos documentos funciona conforme as regras nativas do SEI, ou seja, os documentos Restritos serão acessados nas Unidades de tramitação como sempre.',
            MdPetPiTpProcessoRN::$II_INTERESSE_UNIDADE => 'A Unidade atual será o Interessado do processo a ser aberto. O acesso aos documentos seguirá as regras nativas do SEI mesmo na Unidade de geração do processo: todos os usuários da Unidade de geração acessarão os documentos do processo, inclusive os documentos Restritos.',
        ];
    }

    /**
     * Texto simples de um HTML do editor, para exibição em tooltip: parágrafos viram quebra de linha
     * e entidades são decodificadas para o Latin-1, transliterando o que não couber nele.
     *
     * @param string|null $strHtml
     * @return string
     */
    public static function converterHtmlEmTexto($strHtml)
    {
        $strTexto = preg_replace('/<br\s*\/?>|<\/(p|div|li|h[1-6]|tr)>/i', "\n", (string) $strHtml);
        $strTexto = html_entity_decode(strip_tags(mb_convert_encoding($strTexto, 'UTF-8', 'ISO-8859-1')), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $strTexto = (string) iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $strTexto);
        $strTexto = preg_replace('/[ \t\xA0]+/', ' ', $strTexto);
        $strTexto = preg_replace('/ *\n[\n ]*/', "\n", $strTexto);

        return trim($strTexto);
    }

    /**
     * Limite geral de Documento Externo do core, como na tela documento_receber.
     *
     * @return int
     */
    public static function obterTamanhoMaximoDocPrincipalMb(): int
    {
        $numValor = (new InfraParametro(BancoSEI::getInstance()))->getValor('SEI_TAM_MB_DOC_EXTERNO');

        if (InfraString::isBolVazia($numValor) || !is_numeric($numValor)) {
            throw new InfraException('Valor do parâmetro SEI_TAM_MB_DOC_EXTERNO inválido.');
        }

        return (int) $numValor;
    }

    /**
     * Anexos Obrigatórios e Mais Anexos usam o mesmo limite de Documento Externo do core.
     *
     * @return int
     */
    public static function obterTamanhoMaximoAnexoMb(): int
    {
        return self::obterTamanhoMaximoDocPrincipalMb();
    }

    /**
     * Anexos Obrigatórios configurados para o Tipo de Processo, na ordem do nome do Tipo de Documento.
     *
     * @param int $numIdMdPetPiTpProcesso
     * @return MdPetPiAnexoObrigDTO[]
     */
    public static function listarAnexosObrigatorios($numIdMdPetPiTpProcesso)
    {
        $objMdPetPiAnexoObrigDTO = new MdPetPiAnexoObrigDTO();
        $objMdPetPiAnexoObrigDTO->retTodos();
        $objMdPetPiAnexoObrigDTO->retStrNomeSerie();
        $objMdPetPiAnexoObrigDTO->retStrNomeHipoteseLegal();
        $objMdPetPiAnexoObrigDTO->setNumIdMdPetPiTpProcesso($numIdMdPetPiTpProcesso);
        $objMdPetPiAnexoObrigDTO->setOrdStrNomeSerie(InfraDTO::$TIPO_ORDENACAO_ASC);

        return (new MdPetPiAnexoObrigRN())->listar($objMdPetPiAnexoObrigDTO);
    }

    /**
     * Itens do select de Tipo de Documento dos Anexos Obrigatórios, restrito aos tipos configurados.
     *
     * @param MdPetPiAnexoObrigDTO[] $arrObjMdPetPiAnexoObrigDTO
     * @return string
     */
    public static function montarSelectAnexosObrigatorios($arrObjMdPetPiAnexoObrigDTO)
    {
        return parent::montarSelectArrInfraDTO('null', '&nbsp;', null, $arrObjMdPetPiAnexoObrigDTO, 'IdSerie', 'NomeSerie');
    }

    /**
     * Nível de acesso e hipótese legal de cada Anexo Obrigatório, como objeto JavaScript indexado pelo tipo de documento,
     * para a tela mostrar os campos bloqueados ao escolher o tipo.
     *
     * @param MdPetPiAnexoObrigDTO[] $arrObjMdPetPiAnexoObrigDTO
     * @return string
     */
    public static function montarJsConfigAnexosObrigatorios($arrObjMdPetPiAnexoObrigDTO)
    {
        $arrItens = [];

        foreach ($arrObjMdPetPiAnexoObrigDTO as $objDTO) {

            $strNomeHipoteseLegal = $objDTO->getStrNomeHipoteseLegal() === null ? '' : $objDTO->getStrNomeHipoteseLegal();

            $arrItens[] = '"' . (int) $objDTO->getNumIdSerie() . '": {'
                . 'tipoNivelAcesso: "' . PaginaSEI::getInstance()->formatarParametrosJavaScript($objDTO->getStrStaTipoNivelAcesso(), false) . '", '
                . 'nivelAcesso: "' . PaginaSEI::getInstance()->formatarParametrosJavaScript($objDTO->getStrStaNivelAcesso(), false) . '", '
                . 'nomeNivelAcesso: "' . PaginaSEI::getInstance()->formatarParametrosJavaScript($objDTO->getStrStaNivelAcesso() === null ? '' : ProtocoloINT::montarStaNivelAcesso($objDTO->getStrStaNivelAcesso()), false) . '", '
                . 'idHipoteseLegal: "' . (int) $objDTO->getNumIdHipoteseLegal() . '", '
                . 'nomeHipoteseLegal: "' . PaginaSEI::getInstance()->formatarParametrosJavaScript($strNomeHipoteseLegal, false) . '", '
                . 'nomeSerie: "' . PaginaSEI::getInstance()->formatarParametrosJavaScript($objDTO->getStrNomeSerie(), false) . '"'
                . '}';
        }

        return '{' . implode(', ', $arrItens) . '}';
    }

    /**
     * Documento Principal configurado para um tipo disponível ao usuário, com a aplicabilidade esperada.
     * Repete a conferência de disponibilidade porque as janelas do editor e do formulário recebem o identificador pela URL.
     *
     * @param int $numIdMdPetPiTpProcesso
     * @param string $strStaAplicabilidade
     * @return MdPetPiDocPrincDTO
     */
    public static function consultarDocPrincipalDisponivel($numIdMdPetPiTpProcesso, $strStaAplicabilidade)
    {
        $objMdPetPiTpProcessoDTO = new MdPetPiTpProcessoDTO();
        $objMdPetPiTpProcessoDTO->setNumIdMdPetPiTpProcesso($numIdMdPetPiTpProcesso);

        $arrObjMdPetPiTpProcessoDTO = (new MdPetPiTpProcessoRN())->listarDisponiveis($objMdPetPiTpProcessoDTO);

        if (count($arrObjMdPetPiTpProcessoDTO) === 0) {
            throw new InfraException('Tipo de Processo não disponível para Peticionamento Interno.');
        }

        $objMdPetPiDocPrincDTO = new MdPetPiDocPrincDTO();
        $objMdPetPiDocPrincDTO->retTodos();
        $objMdPetPiDocPrincDTO->retStrNomeSerie();
        // $objMdPetPiDocPrincDTO->retStrNomeTipoFormulario();
        $objMdPetPiDocPrincDTO->setNumIdMdPetPiTpProcesso($arrObjMdPetPiTpProcessoDTO[0]->getNumIdMdPetPiTpProcesso());

        $objMdPetPiDocPrincDTO = (new MdPetPiDocPrincRN())->consultar($objMdPetPiDocPrincDTO);

        if ($objMdPetPiDocPrincDTO === null || $objMdPetPiDocPrincDTO->getStrStaAplicabilidade() !== $strStaAplicabilidade) {
            throw new InfraException('Documento Principal deste Tipo de Processo não corresponde à tela solicitada.');
        }

        return $objMdPetPiDocPrincDTO;
    }

    /**
     * Conteúdo inicial do Documento Principal Gerado: o Modelo por Protocolo configurado ou o modelo do tipo de documento.
     *
     * @param MdPetPiDocPrincDTO $objMdPetPiDocPrincDTO
     * @return string
     */
    public static function obterConteudoInicialDocPrincipal(MdPetPiDocPrincDTO $objMdPetPiDocPrincDTO)
    {
        return self::obterConteudoModelo($objMdPetPiDocPrincDTO->getDblIdProtocoloModelo(), $objMdPetPiDocPrincDTO->getNumIdSerie());
    }

    /**
     * Conteúdo de partida de um documento gerado: o Modelo por Protocolo, quando configurado, ou o modelo do tipo de documento.
     *
     * @param float|null $dblIdProtocoloModelo
     * @param int $numIdSerie
     * @return string
     */
    public static function obterConteudoModelo($dblIdProtocoloModelo, $numIdSerie)
    {
        if ($dblIdProtocoloModelo !== null) {
            return self::obterConteudoDocumentoModelo($dblIdProtocoloModelo);
        }

        return self::montarConteudoModeloSerie($numIdSerie);
    }

    /**
     * Conteúdo da seção principal da última versão do documento indicado como Modelo por Protocolo, que precisa
     * continuar Público.
     *
     * @param float $dblIdDocumento
     * @return string
     */
    private static function obterConteudoDocumentoModelo($dblIdDocumento)
    {
        if (!MdPetPiTpProcessoRN::isModeloPorProtocoloPublico($dblIdDocumento)) {
            (new InfraException())->lancarValidacao('O Modelo por Protocolo deste Tipo de Processo não é mais um documento Público. Procure a administração do Peticionamento Interno para corrigir o Tipo de Processo.');
        }

        $objSecaoDocumentoDTO = new SecaoDocumentoDTO();
        $objSecaoDocumentoDTO->retNumIdSecaoDocumento();
        $objSecaoDocumentoDTO->setDblIdDocumento($dblIdDocumento);
        $objSecaoDocumentoDTO->setStrSinPrincipal('S');

        $objSecaoDocumentoDTO = (new SecaoDocumentoRN())->consultar($objSecaoDocumentoDTO);

        if ($objSecaoDocumentoDTO === null) {
            throw new InfraException('Modelo por Protocolo sem seção principal.');
        }

        $objVersaoSecaoDocumentoDTO = new VersaoSecaoDocumentoDTO();
        $objVersaoSecaoDocumentoDTO->retStrConteudo();
        $objVersaoSecaoDocumentoDTO->setNumIdSecaoDocumento($objSecaoDocumentoDTO->getNumIdSecaoDocumento());
        $objVersaoSecaoDocumentoDTO->setStrSinUltima('S');

        $objVersaoSecaoDocumentoDTO = (new VersaoSecaoDocumentoRN())->consultar($objVersaoSecaoDocumentoDTO);

        if ($objVersaoSecaoDocumentoDTO === null) {
            throw new InfraException('Modelo por Protocolo sem versão da seção principal.');
        }

        return $objVersaoSecaoDocumentoDTO->getStrConteudo();
    }

    /**
     * Conteúdo das seções editáveis do modelo do tipo de documento, na ordem do core.
     *
     * @param int $numIdSerie
     * @return string
     */
    private static function montarConteudoModeloSerie($numIdSerie)
    {
        $objSerieDTO = new SerieDTO();
        $objSerieDTO->retNumIdModelo();
        $objSerieDTO->setBolExclusaoLogica(false);
        $objSerieDTO->setNumIdSerie($numIdSerie);

        $objSerieDTO = (new SerieRN())->consultarRN0644($objSerieDTO);

        if ($objSerieDTO === null || $objSerieDTO->getNumIdModelo() === null) {
            return '';
        }

        $objSecaoModeloDTO = new SecaoModeloDTO();
        $objSecaoModeloDTO->retStrConteudo();
        $objSecaoModeloDTO->setNumIdModelo($objSerieDTO->getNumIdModelo());
        $objSecaoModeloDTO->setStrSinSomenteLeitura('N');
        $objSecaoModeloDTO->setOrdNumOrdem(InfraDTO::$TIPO_ORDENACAO_ASC);

        $strConteudo = '';

        foreach ((new SecaoModeloRN())->listar($objSecaoModeloDTO) as $objDTO) {
            $strConteudo .= $objDTO->getStrConteudo();
        }

        return $strConteudo;
    }

    /**
     * Editor do Documento Principal Gerado com a configuração do editor do Peticionamento Externo
     * (MdPetEditorUsuarioExternoRN::montarSimples e md_pet_editor_usuario_externo_processar.php), montada para a sessão
     * do usuário interno: CKEditor 4 com a barra no topo da janela começando por Salvar, estilos do órgão e crescimento
     * automático.
     *
     * @param int $numIdSerie tipo de documento do Documento Principal, cujo modelo define os estilos oferecidos
     * @param string $strNomeCampo textarea substituída pelo editor
     * @return EditorDTO script e configuração em StrInicializacao; criação do editor em StrEditores
     */
    public static function montarEditorDocumentoPrincipal($numIdSerie, $strNomeCampo)
    {
        $objOrgaoDTO = new OrgaoDTO();
        $objOrgaoDTO->retStrServidorCorretorOrtografico();
        $objOrgaoDTO->retStrStaCorretorOrtografico();
        $objOrgaoDTO->setNumIdOrgao(SessaoSEI::getInstance()->getNumIdOrgaoUnidadeAtual());

        $objOrgaoDTO = (new OrgaoRN())->consultarRN1352($objOrgaoDTO);

        $arrImagemPermitida = EditorINT::getArrImagensPermitidas();

        $arrIncluirPlugins = ['autogrow', 'sharedspace', 'simpleLink', 'extenso', 'maiuscula', 'stylesheetparser', 'tableresize', 'tableclean', 'symbol'];
        $arrRemoverPlugins = ['resize', 'maximize', 'link', 'wsc'];

        $strPluginScayt = '';

        if ($objOrgaoDTO !== null && $objOrgaoDTO->getStrStaCorretorOrtografico() === OrgaoRN::$TCO_LICENCIADO) {
            try {
                $strPluginScayt = InfraUtil::isBolUrlValida($objOrgaoDTO->getStrServidorCorretorOrtografico() . '/spellcheck/lf/scayt3/ckscayt/ckscayt.js') ? 'scayt3' : 'scayt';
                $arrIncluirPlugins[] = $strPluginScayt;
            } catch (Exception $e) {
                $strPluginScayt = '';
                LogSEI::getInstance()->gravar("Erro acessando servidor SCAYT para validar versão do plugin:\n" . InfraException::inspecionar($e));
            }
        }

        if (count($arrImagemPermitida) > 0 && !PaginaSEI::getInstance()->isBolNavegadorSafariIpad()) {
            $arrIncluirPlugins[] = 'base64image';
        } else {
            $arrRemoverPlugins[] = 'base64image';
        }

        $arrConfiguracao = ConfiguracaoSEI::getInstance()->getArrConfiguracoes();
        $strRegexSistema = preg_replace('@https?://@', '', str_replace('.', '\.', $arrConfiguracao['SEI']['URL']) . '.*infra_hash=.*');
        $strUsuarioDicionario = PaginaSEI::getInstance()->formatarParametrosJavaScript(
            strtolower(SessaoSEI::getInstance()->getStrSiglaUsuario() . '.' . SessaoSEI::getInstance()->getStrSiglaOrgaoUsuario()),
            false
        );

        $strInicializacao = '<script type="text/javascript" charset="utf-8" src="editor/ck4/ckeditor.js?t=' . EditorCk4RN::$VERSAO_CK . '"></script>' . "\n";
        $strInicializacao .= '<style type="text/css">' . "\n";
        $strInicializacao .= '.cke_combo__styles .cke_combo_text {width:230px !important;}' . "\n";
        $strInicializacao .= '.cke_button__save_label {display:inline !important;}' . "\n";
        $strInicializacao .= '.cke_button__autotexto_label {display:inline !important;}' . "\n";
        $strInicializacao .= '.cke_combopanel__styles {width:400px !important;}' . "\n";
        $strInicializacao .= '</style>' . "\n";
        $strInicializacao .= '<script type="text/javascript">' . "\n";
        $strInicializacao .= "CKEDITOR.config.url_sei_re='" . $strRegexSistema . "';\n";
        $strInicializacao .= "CKEDITOR.config.skin='moonocolor';\n";
        $strInicializacao .= "CKEDITOR.config.removePlugins='" . implode(',', $arrRemoverPlugins) . "';\n";
        $strInicializacao .= "CKEDITOR.config.extraPlugins='" . implode(',', $arrIncluirPlugins) . "';\n";
        $strInicializacao .= "CKEDITOR.config.dialog_noConfirmCancel=true;\n";
        $strInicializacao .= "CKEDITOR.config.base64image_filetypes='" . implode('|', $arrImagemPermitida) . "';\n";
        $strInicializacao .= "CKEDITOR.config.base64imageUploadUrl='" . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=editor_imagem_upload') . "';\n";
        $strInicializacao .= "CKEDITOR.config.base64imageAjaxUrl='" . SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=upload_buscar') . "';\n";
        $strInicializacao .= "CKEDITOR.config.scayt_sLang='pt_BR';\n";
        $strInicializacao .= "CKEDITOR.config.scayt_userDictionaryName='" . $strUsuarioDicionario . "';\n";
        $strInicializacao .= "CKEDITOR.config.wsc_userDictionaryName='" . $strUsuarioDicionario . "';\n";
        $strInicializacao .= "CKEDITOR.config.scayt_autoStartup=true;\n";
        $strInicializacao .= "CKEDITOR.config.autoGrow_minHeight=1;\n";
        $strInicializacao .= "CKEDITOR.config.autoGrow_bottomSpace=5;\n";

        if ($strPluginScayt !== '') {
            $strServidorCorretor = $objOrgaoDTO->getStrServidorCorretorOrtografico();
            $strCaminhoScayt = $strPluginScayt === 'scayt3' ? '/spellcheck/lf/scayt3/ckscayt/ckscayt.js' : '/spellcheck/lf/scayt/scayt.js';

            $strInicializacao .= "CKEDITOR.config.wsc_customLoaderScript='" . $strServidorCorretor . "/spellcheck/lf/22/js/wsc_fck2plugin.js';\n";
            $strInicializacao .= "CKEDITOR.config.scayt_srcUrl='" . $strServidorCorretor . $strCaminhoScayt . '?' . EditorCk4RN::$VERSAO_CK . "';\n";
            $strInicializacao .= "CKEDITOR.config.scayt_uiTabs='0,0,0';\n";
        }

        $strInicializacao .= "CKEDITOR.config.sharedSpaces={'top':'divComandos'};\n";
        $strInicializacao .= "CKEDITOR.on('dialogDefinition',function(ev){if(ev.data.name=='image'){var dd=ev.data.definition;dd.removeContents('Link');dd.removeContents('advanced');dd.minHeight=200;dd.minWidth=250;var tab=dd.getContents('info');tab.get('ratioLock').style='margin-top:20px;width:40px;height:40px;';tab.get('txtUrl').hidden=true;tab.get('txtAlt').hidden=true;tab.get('htmlPreview').hidden=true;tab.remove('txtHSpace');tab.remove('txtVSpace');tab.remove('cmbAlign');}});\n";
        $strInicializacao .= 'CKEDITOR.config.contentsCss=' . EditorRN::jsEncode(ConjuntoEstilosRN::montarCssEditor(0)) . ";\n";
        $strInicializacao .= "</script>\n";

        $arrBarraFerramentas = [
            ['Save'],
            ['Find', 'Replace', '-', 'RemoveFormat', 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', 'Maiuscula', 'Minuscula', 'TextColor', 'BGColor'],
            ['Cut', 'Copy', 'PasteFromWord', 'PasteText', '-', 'Undo', 'Redo', 'ShowBlocks', 'Symbol', 'Scayt'],
            ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', 'base64image'],
            ['Table', 'SpecialChar', 'SimpleLink', 'Extenso', 'Zoom'],
            ['Styles'],
        ];

        $arrEstilos = [];

        foreach (self::listarEstilosPermitidosModeloSerie($numIdSerie) as $strNomeEstilo) {
            $arrEstilos[] = preg_quote($strNomeEstilo, '/');
        }

        $strEditores = "CKEDITOR.replace('" . $strNomeCampo . "',{'autoGrow_onStartup':true,"
            . '\'stylesheetParser_validSelectors\':/^(p)\.(' . implode('|', $arrEstilos) . ')$/i,'
            . "'toolbar':" . EditorRN::jsEncode($arrBarraFerramentas) . '});';

        $objEditorDTO = new EditorDTO();
        $objEditorDTO->setStrInicializacao($strInicializacao);
        $objEditorDTO->setStrEditores($strEditores);

        return $objEditorDTO;
    }

    /**
     * Estilos que o core permite nas seções editáveis do modelo do tipo de documento, no conjunto de estilos atual.
     *
     * @param int $numIdSerie
     * @return string[]
     */
    private static function listarEstilosPermitidosModeloSerie($numIdSerie)
    {
        $objSerieDTO = new SerieDTO();
        $objSerieDTO->retNumIdModelo();
        $objSerieDTO->setBolExclusaoLogica(false);
        $objSerieDTO->setNumIdSerie($numIdSerie);

        $objSerieDTO = (new SerieRN())->consultarRN0644($objSerieDTO);

        if ($objSerieDTO === null || $objSerieDTO->getNumIdModelo() === null) {
            return [];
        }

        $objSecaoModeloDTO = new SecaoModeloDTO();
        $objSecaoModeloDTO->retNumIdSecaoModelo();
        $objSecaoModeloDTO->setNumIdModelo($objSerieDTO->getNumIdModelo());
        $objSecaoModeloDTO->setStrSinSomenteLeitura('N');

        $arrIdSecaoModelo = InfraArray::converterArrInfraDTO((new SecaoModeloRN())->listar($objSecaoModeloDTO), 'IdSecaoModelo');

        if (count($arrIdSecaoModelo) === 0) {
            return [];
        }

        $arrEstilos = [];

        foreach (RelSecaoModCjEstilosItemRN::buscarEstilosPermitidos(ConjuntoEstilosRN::buscarConjuntoEstilosAtual(), $arrIdSecaoModelo) as $arrObjRelSecaoModCjEstilosItemDTO) {
            foreach ($arrObjRelSecaoModCjEstilosItemDTO as $objRelSecaoModCjEstilosItemDTO) {

                $strNomeEstilo = (string) $objRelSecaoModCjEstilosItemDTO->getStrNomeEstilo();

                if (!in_array($strNomeEstilo, $arrEstilos, true)) {
                    $arrEstilos[] = $strNomeEstilo;
                }
            }
        }

        return $arrEstilos;
    }

    /**
     * Editor completo do CKEditor 5, como o editor de documento do core, montado pelas seções do modelo do tipo de documento.
     *
     * @param MdPetPiDocPrincDTO $objMdPetPiDocPrincDTO
     * @return EditorDTO CSS e JS do editor e o HTML das seções
     */
    public static function montarEditorCompletoDocPrincipal(MdPetPiDocPrincDTO $objMdPetPiDocPrincDTO)
    {
        $numIdMdPetPiTpProcesso = $objMdPetPiDocPrincDTO->getNumIdMdPetPiTpProcesso();
        $arrObjSecaoModeloDTO = self::listarSecoesModeloDocPrincipal($objMdPetPiDocPrincDTO->getNumIdSerie());
        $arrConteudoSessao = self::obterEditorCompletoSessao($numIdMdPetPiTpProcesso, $arrObjSecaoModeloDTO);

        $strConteudoModeloProtocolo = $objMdPetPiDocPrincDTO->getDblIdProtocoloModelo() !== null
            ? self::obterConteudoDocumentoModelo($objMdPetPiDocPrincDTO->getDblIdProtocoloModelo())
            : null;

        $objEditorDTO = new EditorDTO();
        $objEditorDTO->setNumIdModelo($arrObjSecaoModeloDTO[0]->getNumIdModelo());

        $arrNomeSecaoPorId = [];
        $arrElementoHtml = [];
        $arrConteudoInicial = [];
        $arrAtributosSecao = [];
        $strHtml = '';

        foreach ($arrObjSecaoModeloDTO as $objSecaoModeloDTO) {

            $strNomeSecao = EditorCk5RN::PREFIXO_SECAO_CONTEUDO . $objSecaoModeloDTO->getNumIdSecaoModelo();
            $bolEditavel = self::isSecaoEditavel($objSecaoModeloDTO);

            if ($bolEditavel && isset($arrConteudoSessao['secoes'][$objSecaoModeloDTO->getStrNome()])) {
                $strConteudo = $arrConteudoSessao['secoes'][$objSecaoModeloDTO->getStrNome()];
            } elseif ($bolEditavel && $strConteudoModeloProtocolo !== null && $objSecaoModeloDTO->getStrSinPrincipal() === 'S') {
                $strConteudo = $strConteudoModeloProtocolo;
            } else {
                $strConteudo = EditorTagRN::substituirTagsInterno($objEditorDTO, (string) $objSecaoModeloDTO->getStrConteudo(), true);
            }

            $arrNomeSecaoPorId[$objSecaoModeloDTO->getNumIdSecaoModelo()] = $strNomeSecao;
            $arrElementoHtml[$strNomeSecao] = '#' . $strNomeSecao;
            $arrConteudoInicial[$strNomeSecao] = $strConteudo;
            $arrAtributosSecao[$strNomeSecao] = [
                'somenteLeitura' => !$bolEditavel,
                'principal' => $objSecaoModeloDTO->getStrSinPrincipal() === 'S',
                'label' => $objSecaoModeloDTO->getStrNome(),
            ];

            $strHtml .= '<div id="' . $strNomeSecao . '"></div>' . "\n";
        }

        $numIdConjuntoEstilos = ConjuntoEstilosRN::buscarConjuntoEstilosAtual();
        $arrSecoesPorEstilo = [];
        $arrEstiloPadrao = [];

        foreach (RelSecaoModCjEstilosItemRN::buscarEstilosPermitidos($numIdConjuntoEstilos, array_keys($arrNomeSecaoPorId)) as $arrObjRelSecaoModCjEstilosItemDTO) {
            foreach ($arrObjRelSecaoModCjEstilosItemDTO as $objRelSecaoModCjEstilosItemDTO) {

                $numIdSecaoModelo = $objRelSecaoModCjEstilosItemDTO->getNumIdSecaoModelo();

                if (!isset($arrNomeSecaoPorId[$numIdSecaoModelo])) {
                    continue;
                }

                $strNomeEstilo = (string) $objRelSecaoModCjEstilosItemDTO->getStrNomeEstilo();
                $arrSecoesPorEstilo[$strNomeEstilo][] = $arrNomeSecaoPorId[$numIdSecaoModelo];

                if ($objRelSecaoModCjEstilosItemDTO->getStrSinPadrao() === 'S') {
                    $arrEstiloPadrao[$arrNomeSecaoPorId[$numIdSecaoModelo]] = $strNomeEstilo;
                }
            }
        }

        $arrCssEstilos = [];
        $arrItensEstilo = [];

        foreach (ConjuntoEstilosINT::montarArrEstilos($numIdConjuntoEstilos) as $strNomeEstilo => $strCss) {

            if (!isset($arrSecoesPorEstilo[$strNomeEstilo])) {
                continue;
            }

            $arrCssEstilos[] = '.' . $strNomeEstilo . '{' . str_replace("\n", '', $strCss) . '}';

            if (strpos($strNomeEstilo, ':') === false) {
                $arrItensEstilo[] = ['nome' => $strNomeEstilo, 'classeCss' => $strNomeEstilo, 'permitidoEm' => $arrSecoesPorEstilo[$strNomeEstilo]];
            }
        }

        $bolPodeVerCodigoFonte = SessaoSEI::getInstance()->verificarPermissao('editor_visualizar_codigo_fonte');
        $arrImagensPermitidas = EditorINT::getArrImagensPermitidas();
        $bolPodeEnviarImagens = count($arrImagensPermitidas) > 0;

        $objInfraEditor = new InfraEditor(BancoSEI::getInstance());

        $objInfraEditor->addPlugins([
            $bolPodeVerCodigoFonte ? InfraEditorPlugin::SOURCE_EDITING : null,
            InfraEditorPlugin::INFRA_ESTILO,
            InfraEditorPlugin::SHOW_BLOCKS,
            InfraEditorPlugin::INFRA_NIVEL_AMPLIACAO,
            InfraEditorPlugin::INFRA_NOTAS_RODAPE,
            InfraEditorPlugin::INFRA_MODO_VISUALIZACAO,
            InfraEditorPlugin::INFRA_SALVAR,
            InfraEditorPlugin::INFRA_TEXTO_PADRAO,
            InfraEditorPlugin::INFRA_VALOR_EXTENSO,
            'SEI.Sei',
            'SEI.LinkProtocoloSei',
        ]);

        $objInfraEditor->setBarraTarefas([
            $bolPodeVerCodigoFonte ? 'sourceEditing' : null,
            'salvar',
            'estilo',
            'nivelAmpliacao',
            'findAndReplace',
            '|',
            'copiarFormatacao',
            'removeFormat',
            'bold',
            'italic',
            'underline',
            'strikethrough',
            'subscript',
            'superscript',
            'transformarTexto',
            'fontColor',
            'fontBackgroundColor',
            'showBlocks',
            'specialCharacters',
            '|',
            'undo',
            'redo',
            'numberedList',
            'bulletedList',
            'outdent',
            'indent',
            'horizontalLine',
            'pageBreak',
            '|',
            $bolPodeEnviarImagens ? 'imageUpload' : null,
            'insertTable',
            'link',
            'linkProtocoloSei',
            '|',
            'valorExtenso',
            'inserirNotaRodape',
            'textoPadrao',
            '|',
            'modoVisualizacao',
        ]);

        $objInfraEditor->setEsquemaCores(PaginaSEI::getInstance()->getStrEsquemaCores());

        if (!ConfiguracaoSEI::getInstance()->getValor('SEI', 'Producao')) {
            $objInfraEditor->setHabilitarInspector(true);
        }

        $objInfraEditor->setConfiguracao('infra.permitirUploadImagens', $bolPodeEnviarImagens);
        $objInfraEditor->setConfiguracao('infra.permitirInsercaoImagensExternas', false);
        $objInfraEditor->setConfiguracao('image.upload.types', $arrImagensPermitidas);
        $objInfraEditor->setConfiguracao('infra.estilo.css', $arrCssEstilos);
        $objInfraEditor->setConfiguracao('infra.estilo.itens', $arrItensEstilo);
        $objInfraEditor->setConfiguracao('infra.estilo.estiloPadrao', (object) $arrEstiloPadrao);
        $objInfraEditor->setElementoHtmlPorSecaoConteudo($arrElementoHtml);
        $objInfraEditor->setTipoEditor(InfraEditorPlugin::EDITOR_COMPLETO);
        $objInfraEditor->setConfiguracao('initialData', $arrConteudoInicial);
        $objInfraEditor->setConfiguracao('rootsAttributes', $arrAtributosSecao);
        $objInfraEditor->setConfiguracao('infra.texto.urlBuscarTextosPadroes', SessaoSEI::getInstance()->assinarLink('controlador_rest.php?acao_rest=editor_listar_textos_padroes'));
        $objInfraEditor->setConfiguracao('infra.tamanhoMinimoConteudoModalAguarde', 16000);
        $objInfraEditor->setConfiguracao('sei', [
            'versao' => $arrConteudoSessao['versao'],
            'urlConfirmarAtualizacao' => SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_pi_peticao_editor_confirmar&id_md_pet_pi_tp_processo=' . $numIdMdPetPiTpProcesso),
            'urlSalvar' => SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_pi_peticao_editor_salvar&id_md_pet_pi_tp_processo=' . $numIdMdPetPiTpProcesso),
            'urlBuscarProtocoloSei' => SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=protocolo_link_editor'),
            'atualizarArvoreAoIniciar' => false,
        ]);
        $objInfraEditor->addJs('editor/ck5/dist/sei.umd.js?v=' . PaginaSEI::getInstance()->getNumVersao());
        $objInfraEditor->montar();

        $objEditorDTO->setStrCss($objInfraEditor->getCss());
        $objEditorDTO->setStrJs($objInfraEditor->getJs());
        $objEditorDTO->setStrHtml($strHtml);

        return $objEditorDTO;
    }

    /**
     * Grava na sessão o texto das seções editáveis enviado pelo Salvar do editor completo. Cada seção passa pelo filtro
     * XSS do core antes de gravar (ADR-PET-004).
     *
     * @param int|null $numIdMdPetPiTpProcesso
     * @param object|null $objJsonRequisicao corpo enviado pelo editor, com os textos em ISO-8859-1
     * @return array versão do conteúdo, que só muda quando o texto muda
     */
    public static function salvarEditorCompletoDocPrincipal($numIdMdPetPiTpProcesso, $objJsonRequisicao)
    {
        $objMdPetPiDocPrincDTO = self::consultarDocPrincipalDisponivel((int) $numIdMdPetPiTpProcesso, MdPetPiTpProcessoRN::$DOC_GERADO);
        $arrObjSecaoModeloDTO = self::listarSecoesModeloDocPrincipal($objMdPetPiDocPrincDTO->getNumIdSerie());

        $arrNomeSecaoEditavel = [];

        foreach ($arrObjSecaoModeloDTO as $objSecaoModeloDTO) {
            if (self::isSecaoEditavel($objSecaoModeloDTO)) {
                $arrNomeSecaoEditavel[EditorCk5RN::PREFIXO_SECAO_CONTEUDO . $objSecaoModeloDTO->getNumIdSecaoModelo()] = $objSecaoModeloDTO->getStrNome();
            }
        }

        $arrSecoesConteudo = is_object($objJsonRequisicao) && isset($objJsonRequisicao->secoesConteudo) && is_array($objJsonRequisicao->secoesConteudo)
            ? $objJsonRequisicao->secoesConteudo
            : [];

        $arrSecoes = [];

        foreach ($arrSecoesConteudo as $objSecaoConteudo) {
            if (is_object($objSecaoConteudo) && isset($objSecaoConteudo->nome) && is_string($objSecaoConteudo->nome)
                && isset($arrNomeSecaoEditavel[$objSecaoConteudo->nome])) {
                $arrSecoes[$arrNomeSecaoEditavel[$objSecaoConteudo->nome]] = isset($objSecaoConteudo->html) && is_string($objSecaoConteudo->html) ? $objSecaoConteudo->html : '';
            }
        }

        if (InfraString::isBolVazia(implode('', $arrSecoes))) {
            (new InfraException())->lancarValidacao('Conteúdo do documento não informado.');
        }

        foreach ($arrSecoes as $strConteudo) {

            EditorRN::validarTagsCriticas(EditorINT::getArrImagensPermitidas(), $strConteudo);

            if (preg_match(EditorCk5RN::$REGEXP_TAG_IMG_SRC_EXTERNO, $strConteudo)) {
                (new InfraException())->lancarValidacao('Não são permitidas imagens referenciadas.');
            }

            PeticionamentoIntegracao::validarXssEditor($strConteudo);
        }

        $arrConteudoSessao = self::obterEditorCompletoSessao($objMdPetPiDocPrincDTO->getNumIdMdPetPiTpProcesso(), $arrObjSecaoModeloDTO);
        $numVersao = $arrConteudoSessao['versao'];

        if ($arrConteudoSessao['secoes'] !== $arrSecoes) {
            $numVersao++;
            SessaoSEI::getInstance()->setAtributo(self::obterAtributoConteudoDocPrincipal($objMdPetPiDocPrincDTO->getNumIdMdPetPiTpProcesso()), ['versao' => $numVersao, 'secoes' => $arrSecoes]);
        }

        return ['versao' => $numVersao];
    }

    /**
     * Nome do atributo de sessão com o conteúdo do Documento Principal do Tipo de Processo.
     *
     * @param int $numIdMdPetPiTpProcesso
     * @return string
     */
    public static function obterAtributoConteudoDocPrincipal($numIdMdPetPiTpProcesso): string
    {
        return self::$ATRIBUTO_CONTEUDO_DOC_PRINCIPAL . '_' . (int) $numIdMdPetPiTpProcesso;
    }

    /**
     * Texto do Documento Principal Gerado guardado na sessão, para o editor antigo; do editor completo, junta as seções.
     *
     * @param int $numIdMdPetPiTpProcesso
     * @return string
     */
    public static function obterConteudoDocPrincipalSessao($numIdMdPetPiTpProcesso)
    {
        $mixConteudo = SessaoSEI::getInstance()->getAtributo(self::obterAtributoConteudoDocPrincipal($numIdMdPetPiTpProcesso));

        if (is_array($mixConteudo)) {
            return isset($mixConteudo['secoes']) && is_array($mixConteudo['secoes']) ? implode('', $mixConteudo['secoes']) : '';
        }

        return (string) $mixConteudo;
    }

    /**
     * Seções do modelo do tipo de documento na ordem do core, sem a de assinatura, como na simulação de modelo do core.
     *
     * @param int $numIdSerie
     * @return SecaoModeloDTO[]
     */
    private static function listarSecoesModeloDocPrincipal($numIdSerie)
    {
        $objSerieDTO = new SerieDTO();
        $objSerieDTO->retNumIdModelo();
        $objSerieDTO->setBolExclusaoLogica(false);
        $objSerieDTO->setNumIdSerie($numIdSerie);

        $objSerieDTO = (new SerieRN())->consultarRN0644($objSerieDTO);

        if ($objSerieDTO === null || $objSerieDTO->getNumIdModelo() === null) {
            throw new InfraException('Tipo de Documento do Documento Principal sem modelo.');
        }

        $objSecaoModeloDTO = new SecaoModeloDTO();
        $objSecaoModeloDTO->retTodos();
        $objSecaoModeloDTO->setNumIdModelo($objSerieDTO->getNumIdModelo());
        $objSecaoModeloDTO->setStrSinAssinatura('N');
        $objSecaoModeloDTO->setOrdNumOrdem(InfraDTO::$TIPO_ORDENACAO_ASC);

        $arrObjSecaoModeloDTO = (new SecaoModeloRN())->listar($objSecaoModeloDTO);

        if (count($arrObjSecaoModeloDTO) === 0) {
            throw new InfraException('Modelo do Tipo de Documento do Documento Principal sem seções.');
        }

        return $arrObjSecaoModeloDTO;
    }

    /**
     * Seção que aceita texto do usuário, pelas regras que o core aplica ao gerar o documento.
     *
     * @param SecaoModeloDTO $objSecaoModeloDTO
     * @return bool
     */
    private static function isSecaoEditavel(SecaoModeloDTO $objSecaoModeloDTO)
    {
        return $objSecaoModeloDTO->getStrSinSomenteLeitura() === 'N'
            && $objSecaoModeloDTO->getStrSinAssinatura() === 'N'
            && $objSecaoModeloDTO->getStrSinCabecalho() === 'N'
            && $objSecaoModeloDTO->getStrSinRodape() === 'N';
    }

    /**
     * Versão e texto por nome de seção salvos pelo editor completo; texto salvo pelo editor antigo vai para a seção principal.
     *
     * @param int $numIdMdPetPiTpProcesso
     * @param SecaoModeloDTO[] $arrObjSecaoModeloDTO
     * @return array
     */
    private static function obterEditorCompletoSessao($numIdMdPetPiTpProcesso, $arrObjSecaoModeloDTO)
    {
        $arrRetorno = ['versao' => 0, 'secoes' => []];
        $strAtributo = self::obterAtributoConteudoDocPrincipal($numIdMdPetPiTpProcesso);

        if (!SessaoSEI::getInstance()->isSetAtributo($strAtributo)) {
            return $arrRetorno;
        }

        $mixConteudo = SessaoSEI::getInstance()->getAtributo($strAtributo);

        if (is_array($mixConteudo) && isset($mixConteudo['secoes']) && is_array($mixConteudo['secoes'])) {
            return ['versao' => isset($mixConteudo['versao']) ? (int) $mixConteudo['versao'] : 0, 'secoes' => $mixConteudo['secoes']];
        }

        if (is_string($mixConteudo) && $mixConteudo !== '') {
            foreach ($arrObjSecaoModeloDTO as $objSecaoModeloDTO) {
                if ($objSecaoModeloDTO->getStrSinPrincipal() === 'S' && self::isSecaoEditavel($objSecaoModeloDTO)) {
                    $arrRetorno['secoes'][$objSecaoModeloDTO->getStrNome()] = $mixConteudo;
                }
            }
        }

        return $arrRetorno;
    }

    /**
     * Extensões de arquivo liberadas no core para upload pela interface, no formato de itens de array JavaScript,
     * com os limites por extensão do core. Vazio quando o core não valida extensão.
     *
     * @param int $numTamanhoMaximoMb limite geral de Documento Externo do core, em Mb
     * @return string
     */
    public static function montarJsExtensoesPermitidas(int $numTamanhoMaximoMb): string
    {
        $objInfraParametro = new InfraParametro(BancoSEI::getInstance());

        if ($objInfraParametro->getValor('SEI_HABILITAR_VALIDACAO_EXTENSAO_ARQUIVOS') !== '1') {
            return '';
        }

        $objArquivoExtensaoDTO = new ArquivoExtensaoDTO();
        $objArquivoExtensaoDTO->retStrExtensao();
        $objArquivoExtensaoDTO->retNumTamanhoMaximo();
        $objArquivoExtensaoDTO->setStrSinInterface('S');
        $objArquivoExtensaoDTO->setOrdStrExtensao(InfraDTO::$TIPO_ORDENACAO_ASC);

        $arrItens = [];

        foreach ((new ArquivoExtensaoRN())->listar($objArquivoExtensaoDTO) as $objDTO) {
            $numTamanho = $objDTO->getNumTamanhoMaximo() != null ? (int) $objDTO->getNumTamanhoMaximo() : $numTamanhoMaximoMb;

            $arrItens[] = '{nome: "' . preg_replace('/[^a-z0-9]/', '', InfraString::transformarCaixaBaixa($objDTO->getStrExtensao())) . '", tamanho: ' . $numTamanho . '}';
        }

        return implode(', ', $arrItens);
    }

    /**
     * Recebe o arquivo enviado pela tela, recusa o que passar do limite ou tiver conteúdo não permitido pelas regras XSS
     * do upload externo (ADR-PET-004) e registra o nome gerado na sessão do usuário. O texto devolvido é o do
     * `infraUpload` do core: "nome_upload#nome#tipo#tamanho#data#" ou "ERRO#mensagem".
     *
     * @param string $strCampoArquivo
     * @param int $numTamanhoMaximoMb
     * @param string $strRotuloDocumento
     * @param string $strCampoComplemento
     * @return string
     */
    public static function processarUploadArquivo(string $strCampoArquivo, int $numTamanhoMaximoMb, string $strRotuloDocumento, string $strCampoComplemento): string
    {
        if (!MdPetPiEntradaINT::isArquivoEnviado($strCampoArquivo)) {
            return 'ERRO#Arquivo do ' . $strRotuloDocumento . ' não enviado.';
        }

        $strErro = PeticionamentoIntegracao::validarXssComplementoUpload($strCampoComplemento);
        if ($strErro !== null) {
            return $strErro;
        }

        $numTamanhoEnviado = MdPetPiEntradaINT::obterTamanhoArquivoEnviado($strCampoArquivo);

        if ($numTamanhoEnviado > $numTamanhoMaximoMb * 1024 * 1024) {
            return 'ERRO#Arquivo excede o tamanho máximo geral permitido para documentos externos de ' . $numTamanhoMaximoMb . 'Mb.';
        }

        LimiteSEI::getInstance()->configurarNivel3();

        ob_start();
        PaginaSEI::getInstance()->processarUpload($strCampoArquivo, DIR_SEI_TEMP, false, true);
        $strRetorno = PeticionamentoIntegracao::validarArquivoUpload((string) ob_get_clean(), DIR_SEI_TEMP);

        if (strpos($strRetorno, 'ERRO#') !== 0) {
            $arrRetorno = explode('#', $strRetorno);
            self::registrarArquivoUpload($arrRetorno[0]);
        }

        return $strRetorno;
    }

    /**
     * Recebe o arquivo dos quadros Anexos Obrigatórios e Mais Anexos, que dividem o mesmo caso do controlador
     * porque têm o mesmo limite geral de Documento Externo do core.
     *
     * @return string
     */
    public static function processarUploadAnexo()
    {
        $strCampoArquivo = MdPetPiEntradaINT::isArquivoEnviado('filArquivoAnexoObrig') ? 'filArquivoAnexoObrig' : 'filArquivoMaisAnexos';

        $strCampoComplemento = $strCampoArquivo === 'filArquivoAnexoObrig' ? 'txtComplementoAnexoObrig' : 'txtComplementoMaisAnexos';

        return self::processarUploadArquivo($strCampoArquivo, self::obterTamanhoMaximoAnexoMb(), 'anexo', $strCampoComplemento);
    }

    /**
     * Remove da área temporária um arquivo enviado pelo próprio usuário nesta sessão. Nome desconhecido é ignorado.
     *
     * @param string $strNomeArquivo
     * @return string XML de retorno para a chamada assíncrona
     */
    public static function removerArquivoUpload($strNomeArquivo)
    {
        if (self::isArquivoUploadRegistrado($strNomeArquivo)) {

            $strCaminho = DIR_SEI_TEMP . '/' . $strNomeArquivo;

            if (is_file($strCaminho)) {
                unlink($strCaminho);
            }

            $arrArquivos = self::listarArquivosUpload();
            unset($arrArquivos[$strNomeArquivo]);

            SessaoSEI::getInstance()->setAtributo(self::$ATRIBUTO_ARQUIVOS_UPLOAD, $arrArquivos);
        }

        return '<Arquivo><Removido>S</Removido></Arquivo>';
    }

    /**
     * Apaga os arquivos que sobraram na área temporária e limpa os atributos de sessão da tela de peticionamento.
     *
     * @param int $numIdMdPetPiTpProcesso
     * @return void
     */
    public static function limparDadosPeticionamento($numIdMdPetPiTpProcesso)
    {
        foreach (array_keys(self::listarArquivosUpload()) as $strNomeArquivo) {

            $strCaminho = DIR_SEI_TEMP . '/' . $strNomeArquivo;

            if (is_file($strCaminho)) {
                unlink($strCaminho);
            }
        }

        if (SessaoSEI::getInstance()->isSetAtributo(self::$ATRIBUTO_ARQUIVOS_UPLOAD)) {
            SessaoSEI::getInstance()->removerAtributo(self::$ATRIBUTO_ARQUIVOS_UPLOAD);
        }

        $strAtributo = self::obterAtributoConteudoDocPrincipal($numIdMdPetPiTpProcesso);

        if (SessaoSEI::getInstance()->isSetAtributo($strAtributo)) {
            SessaoSEI::getInstance()->removerAtributo($strAtributo);
        }
    }

    /**
     * @return array nome do arquivo na área temporária => S
     */
    private static function listarArquivosUpload()
    {
        if (!SessaoSEI::getInstance()->isSetAtributo(self::$ATRIBUTO_ARQUIVOS_UPLOAD)) {
            return [];
        }

        $arrArquivos = SessaoSEI::getInstance()->getAtributo(self::$ATRIBUTO_ARQUIVOS_UPLOAD);

        return is_array($arrArquivos) ? $arrArquivos : [];
    }

    /**
     * @param string $strNomeArquivo
     * @return void
     */
    private static function registrarArquivoUpload($strNomeArquivo)
    {
        $arrArquivos = self::listarArquivosUpload();
        $arrArquivos[$strNomeArquivo] = 'S';

        SessaoSEI::getInstance()->setAtributo(self::$ATRIBUTO_ARQUIVOS_UPLOAD, $arrArquivos);
    }

    /**
     * @param string $strNomeArquivo
     * @return bool
     */
    private static function isArquivoUploadRegistrado($strNomeArquivo)
    {
        return $strNomeArquivo !== '' && array_key_exists($strNomeArquivo, self::listarArquivosUpload());
    }

    /**
     * Confere os campos do formulário do Documento Principal pelas regras do core, como a janela do formulário do
     * Peticionamento Externo, e pelo filtro XSS do core (ADR-PET-005), e guarda na sessão os campos como a janela
     * enviou, para remontá-la, e os valores no formato do core, para a conclusão.
     *
     * @param int $numIdMdPetPiTpProcesso
     * @param int $numIdTipoFormulario
     * @return void
     */
    public static function salvarFormularioDocPrincipal($numIdMdPetPiTpProcesso, $numIdTipoFormulario)
    {
        // O core lê o formulário enviado e devolve sinalizador como S ou N e opção pelo valor escolhido
        $arrObjRelProtocoloAtributoDTO = AtributoINT::processar(null, $numIdTipoFormulario);

        (new RelProtocoloAtributoRN())->validarValores($arrObjRelProtocoloAtributoDTO);

        self::validarXssCamposFormulario($numIdTipoFormulario, $arrObjRelProtocoloAtributoDTO);

        $arrValores = [];

        foreach ($arrObjRelProtocoloAtributoDTO as $objRelProtocoloAtributoDTO) {
            $arrValores[(int) $objRelProtocoloAtributoDTO->getNumIdAtributo()] = $objRelProtocoloAtributoDTO->getStrValor();
        }

        SessaoSEI::getInstance()->setAtributo(self::obterAtributoConteudoDocPrincipal($numIdMdPetPiTpProcesso), [
            'campos' => MdPetPiEntradaINT::postArrAtributosFormulario(),
            'valores' => $arrValores,
        ]);
    }

    /**
     * Passa cada valor do formulário pelo filtro XSS do core, com o rótulo do campo na mensagem, como o core faz nas
     * outras validações do formulário (ADR-PET-005).
     *
     * @param int $numIdTipoFormulario
     * @param RelProtocoloAtributoDTO[] $arrObjRelProtocoloAtributoDTO valores devolvidos por AtributoINT::processar
     * @return void
     */
    private static function validarXssCamposFormulario($numIdTipoFormulario, $arrObjRelProtocoloAtributoDTO): void
    {
        $objAtributoDTO = new AtributoDTO();
        $objAtributoDTO->setBolExclusaoLogica(false);
        $objAtributoDTO->retNumIdAtributo();
        $objAtributoDTO->retStrRotulo();
        $objAtributoDTO->setNumIdTipoFormulario($numIdTipoFormulario);

        $arrRotulos = InfraArray::converterArrInfraDTO((new AtributoRN())->listarRN0165($objAtributoDTO), 'Rotulo', 'IdAtributo');

        $objInfraException = new InfraException();

        foreach ($arrObjRelProtocoloAtributoDTO as $objRelProtocoloAtributoDTO) {
            $strRotulo = $arrRotulos[$objRelProtocoloAtributoDTO->getNumIdAtributo()] ?? '';
            PeticionamentoIntegracao::validarXssCampo($objRelProtocoloAtributoDTO->getStrValor(), '"' . $strRotulo . '"', $objInfraException);
        }

        $objInfraException->lancarValidacoes();
    }

    /**
     * Devolve à janela do formulário os campos que o usuário já salvou.
     *
     * @param int $numIdMdPetPiTpProcesso
     * @return void
     */
    public static function restaurarFormularioDocPrincipal($numIdMdPetPiTpProcesso)
    {
        $strAtributo = self::obterAtributoConteudoDocPrincipal($numIdMdPetPiTpProcesso);

        if (!SessaoSEI::getInstance()->isSetAtributo($strAtributo)) {
            return;
        }

        $arrFormulario = SessaoSEI::getInstance()->getAtributo($strAtributo);

        if (is_array($arrFormulario) && isset($arrFormulario['campos'])) {
            MdPetPiEntradaINT::restaurarAtributosFormulario($arrFormulario['campos']);
        }
    }

    /**
     * Campos do formulário pelo nome do atributo, que é como a API de documentos do core os recebe.
     * Ficam em texto porque o DTO da conclusão é auditado, e a auditoria só converte texto.
     *
     * @param array $arrValores id do atributo => valor no formato do core, gravado por salvarFormularioDocPrincipal
     * @return array nome do atributo => valor
     */
    public static function montarCamposFormulario($arrValores)
    {
        $arrCampos = [];

        if (!is_array($arrValores) || count($arrValores) === 0) {
            return $arrCampos;
        }

        $objAtributoDTO = new AtributoDTO();
        $objAtributoDTO->setBolExclusaoLogica(false);
        $objAtributoDTO->retNumIdAtributo();
        $objAtributoDTO->retStrNome();
        $objAtributoDTO->setNumIdAtributo(array_map('intval', array_keys($arrValores)), InfraDTO::$OPER_IN);
        $objAtributoDTO->setOrdNumIdAtributo(InfraDTO::$TIPO_ORDENACAO_ASC);

        foreach ((new AtributoRN())->listarRN0165($objAtributoDTO) as $objDTO) {
            $arrCampos[$objDTO->getStrNome()] = $arrValores[(int) $objDTO->getNumIdAtributo()];
        }

        return $arrCampos;
    }

    /**
     * Lê as três listas de documentos da tela e devolve os documentos do peticionamento já conferidos contra a
     * configuração, contra os arquivos da sessão e, nos Mais Anexos, contra os tipos e níveis permitidos no core.
     *
     * @param MdPetPiDocPrincDTO $objMdPetPiDocPrincDTO
     * @param MdPetPiAnexoObrigDTO[] $arrObjMdPetPiAnexoObrigDTO
     * @param int $numIdTipoProcedimento
     * @param array $arrGrids índices 'principal', 'obrigatorio' e 'mais', cada um com o valor do campo oculto da lista
     * @return MdPetPiRelPeticaoDocDTO[]
     */
    public static function montarDocumentosPeticionamento(
        MdPetPiDocPrincDTO $objMdPetPiDocPrincDTO,
        $arrObjMdPetPiAnexoObrigDTO,
        $numIdTipoProcedimento,
        $arrGrids
    ) {
        $objInfraException = new InfraException();
        $arrObjDocumentoDTO = [];
        $arrNiveisPermitidos = self::listarNiveisAcessoPermitidos($numIdTipoProcedimento);

        $arrLinhasPrincipal = self::lerGridDocumentos($arrGrids['principal']);

        if (count($arrLinhasPrincipal) > 1) {
            $objInfraException->adicionarValidacao('O peticionamento aceita um único Documento Principal.');
        }

        foreach ($arrLinhasPrincipal as $arrLinha) {
            $arrObjDocumentoDTO[] = self::montarDocumentoPrincipal($objMdPetPiDocPrincDTO, $arrLinha, $arrNiveisPermitidos, $objInfraException);
        }

        $arrConfigAnexoObrig = [];

        foreach ($arrObjMdPetPiAnexoObrigDTO as $objAnexoObrigDTO) {
            $arrConfigAnexoObrig[(int) $objAnexoObrigDTO->getNumIdSerie()] = $objAnexoObrigDTO;
        }

        foreach (self::lerGridDocumentos($arrGrids['obrigatorio']) as $arrLinha) {
            $arrObjDocumentoDTO[] = self::montarDocumentoAnexoObrigatorio($arrConfigAnexoObrig, $arrLinha, $arrNiveisPermitidos, $objInfraException);
        }

        $arrLinhasMaisAnexos = self::lerGridDocumentos($arrGrids['mais']);

        if (count($arrLinhasMaisAnexos) > 0) {
            $arrIdSerieExterna = self::listarIdsSerieExternaAtiva(self::listarValoresColuna($arrLinhasMaisAnexos, self::$COL_ID_SERIE));

            foreach ($arrLinhasMaisAnexos as $arrLinha) {
                $arrObjDocumentoDTO[] = self::montarDocumentoMaisAnexos($arrIdSerieExterna, $arrNiveisPermitidos, $arrLinha, $objInfraException);
            }
        }

        $objInfraException->lancarValidacoes();

        return array_values(array_filter($arrObjDocumentoDTO));
    }

    /**
     * @param string $strGrid
     * @return array
     */
    private static function lerGridDocumentos($strGrid)
    {
        return PaginaSEI::getInstance()->getArrItensTabelaDinamica($strGrid);
    }

    /**
     * @param array $arrLinhas
     * @param int $numColuna
     * @return array
     */
    private static function listarValoresColuna($arrLinhas, $numColuna)
    {
        $arrValores = [];

        foreach ($arrLinhas as $arrLinha) {
            $arrValores[] = isset($arrLinha[$numColuna]) ? (int) $arrLinha[$numColuna] : 0;
        }

        return array_values(array_unique($arrValores));
    }

    /**
     * @param MdPetPiDocPrincDTO $objMdPetPiDocPrincDTO
     * @param array $arrLinha
     * @param string[] $arrNiveisPermitidos
     * @param InfraException $objInfraException
     * @return MdPetPiRelPeticaoDocDTO|null
     */
    private static function montarDocumentoPrincipal(MdPetPiDocPrincDTO $objMdPetPiDocPrincDTO, $arrLinha, $arrNiveisPermitidos, InfraException $objInfraException)
    {
        $objDTO = new MdPetPiRelPeticaoDocDTO();
        $objDTO->setStrStaPapel(MdPetPiRelPeticaoDocRN::$PAPEL_PRINCIPAL);

        if (!self::preencherNivelAcesso($objDTO, $objMdPetPiDocPrincDTO, $arrLinha, $arrNiveisPermitidos, 'do Documento Principal', $objInfraException)) {
            return null;
        }

        $objDTO->setNumIdSerie($objMdPetPiDocPrincDTO->getNumIdSerie());
        $objDTO->setStrComplemento('');
        $objDTO->setNumIdTipoConferencia(null);
        $objDTO->setStrStaFormato('');
        $objDTO->setStrNomeArquivoUpload('');
        $objDTO->setStrNomeArquivo('');
        $objDTO->setStrConteudo('');
        $objDTO->setArrCamposFormulario([]);
        $objDTO->setArrConteudoSecoes([]);

        if ($objMdPetPiDocPrincDTO->getStrStaAplicabilidade() === MdPetPiTpProcessoRN::$DOC_EXTERNO) {

            if ((int) $arrLinha[self::$COL_ID_SERIE] !== (int) $objMdPetPiDocPrincDTO->getNumIdSerie()) {
                $objInfraException->adicionarValidacao('Tipo de Documento do Documento Principal diferente do configurado.');
                return null;
            }

            self::preencherDadosArquivo($objDTO, $arrLinha, 'Documento Principal', $objInfraException);

            return $objDTO;
        }

        $strAtributo = self::obterAtributoConteudoDocPrincipal($objMdPetPiDocPrincDTO->getNumIdMdPetPiTpProcesso());

        if (!SessaoSEI::getInstance()->isSetAtributo($strAtributo)) {
            $objInfraException->adicionarValidacao('Conteúdo do Documento Principal não preenchido.');
            return null;
        }

        $mixConteudo = SessaoSEI::getInstance()->getAtributo($strAtributo);

        if ($objMdPetPiDocPrincDTO->getStrStaAplicabilidade() === MdPetPiTpProcessoRN::$DOC_FORMULARIO) {
            $objDTO->setArrCamposFormulario(self::montarCamposFormulario(is_array($mixConteudo) && isset($mixConteudo['valores']) ? $mixConteudo['valores'] : []));
        } elseif (is_array($mixConteudo) && isset($mixConteudo['secoes'])) {
            $objDTO->setArrConteudoSecoes($mixConteudo['secoes']);
        } else {
            $objDTO->setStrConteudo(is_array($mixConteudo) ? '' : (string) $mixConteudo);
        }

        return $objDTO;
    }

    /**
     * @param MdPetPiAnexoObrigDTO[] $arrConfigAnexoObrig id do tipo de documento => configuração
     * @param array $arrLinha
     * @param string[] $arrNiveisPermitidos
     * @param InfraException $objInfraException
     * @return MdPetPiRelPeticaoDocDTO|null
     */
    private static function montarDocumentoAnexoObrigatorio($arrConfigAnexoObrig, $arrLinha, $arrNiveisPermitidos, InfraException $objInfraException)
    {
        $numIdSerie = (int) $arrLinha[self::$COL_ID_SERIE];

        if (!isset($arrConfigAnexoObrig[$numIdSerie])) {
            $objInfraException->adicionarValidacao('Tipo de Documento de Anexo Obrigatório fora dos tipos configurados para este Tipo de Processo.');
            return null;
        }

        $objAnexoObrigDTO = $arrConfigAnexoObrig[$numIdSerie];

        $objDTO = new MdPetPiRelPeticaoDocDTO();
        $objDTO->setStrStaPapel(MdPetPiRelPeticaoDocRN::$PAPEL_ANEXO_OBRIGATORIO);
        $objDTO->setNumIdSerie($numIdSerie);
        // $objDTO->setNumIdTipoFormulario(null);

        if (!self::preencherNivelAcesso($objDTO, $objAnexoObrigDTO, $arrLinha, $arrNiveisPermitidos, 'do Anexo Obrigatório', $objInfraException)) {
            return null;
        }

        $objDTO->setStrConteudo('');
        $objDTO->setArrCamposFormulario([]);
        $objDTO->setArrConteudoSecoes([]);

        self::preencherDadosArquivo($objDTO, $arrLinha, 'Anexo Obrigatório', $objInfraException);

        return $objDTO;
    }

    /**
     * @param int[] $arrIdSerieExterna
     * @param string[] $arrNiveisPermitidos
     * @param array $arrLinha
     * @param InfraException $objInfraException
     * @return MdPetPiRelPeticaoDocDTO|null
     */
    private static function montarDocumentoMaisAnexos($arrIdSerieExterna, $arrNiveisPermitidos, $arrLinha, InfraException $objInfraException)
    {
        $numIdSerie = (int) $arrLinha[self::$COL_ID_SERIE];

        if (!in_array($numIdSerie, $arrIdSerieExterna, true)) {
            $objInfraException->adicionarValidacao('Tipo de Documento de Mais Anexos não é um tipo externo ativo.');
            return null;
        }

        $objDTO = new MdPetPiRelPeticaoDocDTO();
        $objDTO->setStrStaPapel(MdPetPiRelPeticaoDocRN::$PAPEL_MAIS_ANEXOS);
        $objDTO->setNumIdSerie($numIdSerie);
        // $objDTO->setNumIdTipoFormulario(null);

        if (!self::lerNivelAcessoEscolhido($objDTO, $arrLinha, $arrNiveisPermitidos, 'de Mais Anexos', $objInfraException)) {
            return null;
        }

        $objDTO->setStrConteudo('');
        $objDTO->setArrCamposFormulario([]);
        $objDTO->setArrConteudoSecoes([]);

        self::preencherDadosArquivo($objDTO, $arrLinha, 'Mais Anexos', $objInfraException);

        return $objDTO;
    }

    /**
     * Nível e hipótese do documento: da configuração no "Padrão pré-definido", ignorando o que veio da tela; da tela,
     * conferidos, no "Usuário indica diretamente".
     *
     * @param MdPetPiRelPeticaoDocDTO $objDTO
     * @param MdPetPiDocPrincDTO|MdPetPiAnexoObrigDTO $objConfigDTO
     * @param array $arrLinha
     * @param string[] $arrNiveisPermitidos
     * @param string $strRotulo
     * @param InfraException $objInfraException
     * @return bool
     */
    private static function preencherNivelAcesso(MdPetPiRelPeticaoDocDTO $objDTO, $objConfigDTO, $arrLinha, $arrNiveisPermitidos, $strRotulo, InfraException $objInfraException)
    {
        if ($objConfigDTO->getStrStaTipoNivelAcesso() === MdPetPiTpProcessoRN::$TNA_USUARIO) {
            return self::lerNivelAcessoEscolhido($objDTO, $arrLinha, $arrNiveisPermitidos, $strRotulo, $objInfraException);
        }

        $objDTO->setStrStaNivelAcesso($objConfigDTO->getStrStaNivelAcesso());
        $objDTO->setNumIdHipoteseLegal($objConfigDTO->getNumIdHipoteseLegal());

        return true;
    }

    /**
     * Nível escolhido na tela, entre os permitidos para o tipo de processo; no Restrito, hipótese legal ativa, exigida
     * só quando o parâmetro SEI_HABILITAR_HIPOTESE_LEGAL não a desliga.
     *
     * @param MdPetPiRelPeticaoDocDTO $objDTO
     * @param array $arrLinha
     * @param string[] $arrNiveisPermitidos
     * @param string $strRotulo
     * @param InfraException $objInfraException
     * @return bool
     */
    private static function lerNivelAcessoEscolhido(MdPetPiRelPeticaoDocDTO $objDTO, $arrLinha, $arrNiveisPermitidos, $strRotulo, InfraException $objInfraException)
    {
        $strStaNivelAcesso = trim($arrLinha[self::$COL_STA_NIVEL_ACESSO]);

        if (!in_array($strStaNivelAcesso, $arrNiveisPermitidos, true)) {
            $objInfraException->adicionarValidacao('Nível de Acesso ' . $strRotulo . ' não permitido para este Tipo de Processo.');
            return false;
        }

        $numIdHipoteseLegal = trim($arrLinha[self::$COL_ID_HIPOTESE_LEGAL]) === '' ? null : (int) $arrLinha[self::$COL_ID_HIPOTESE_LEGAL];

        if ($strStaNivelAcesso === ProtocoloRN::$NA_RESTRITO && MdPetPiTpProcessoRN::isHipoteseLegalHabilitada()) {
            if ($numIdHipoteseLegal === null || !self::isHipoteseLegalAtiva($numIdHipoteseLegal)) {
                $objInfraException->adicionarValidacao('Hipótese Legal ' . $strRotulo . ' não informada ou inativa.');
                return false;
            }
        } else {
            $numIdHipoteseLegal = null;
        }

        $objDTO->setStrStaNivelAcesso($strStaNivelAcesso);
        $objDTO->setNumIdHipoteseLegal($numIdHipoteseLegal);

        return true;
    }

    /**
     * Preenche o arquivo e os campos comuns a todo documento externo da tela, conferindo o arquivo contra a sessão.
     *
     * @param MdPetPiRelPeticaoDocDTO $objDTO
     * @param array $arrLinha
     * @param string $strRotuloDocumento
     * @param InfraException $objInfraException
     * @return void
     */
    private static function preencherDadosArquivo(MdPetPiRelPeticaoDocDTO $objDTO, $arrLinha, $strRotuloDocumento, InfraException $objInfraException)
    {
        $strNomeUpload = trim($arrLinha[self::$COL_IDENTIFICADOR]);
        $strComplemento = trim($arrLinha[self::$COL_COMPLEMENTO]);
        $strStaFormato = trim($arrLinha[self::$COL_STA_FORMATO]);
        $numIdTipoConferencia = trim($arrLinha[self::$COL_ID_TIPO_CONFERENCIA]) === '' ? null : (int) $arrLinha[self::$COL_ID_TIPO_CONFERENCIA];

        if (!self::isArquivoUploadRegistrado($strNomeUpload) || !is_file(DIR_SEI_TEMP . '/' . $strNomeUpload)) {
            $objInfraException->adicionarValidacao('Arquivo de ' . $strRotuloDocumento . ' não está mais disponível. Remova a linha da lista e envie o arquivo novamente.');
            return;
        }

        if ($strComplemento === '') {
            $objInfraException->adicionarValidacao('Complemento do Tipo de Documento de ' . $strRotuloDocumento . ' não informado.');
        }

        if (!in_array($strStaFormato, ['N', 'D'], true)) {
            $objInfraException->adicionarValidacao('Formato do documento de ' . $strRotuloDocumento . ' não informado.');
        }

        if ($strStaFormato === 'D' && $numIdTipoConferencia === null) {
            $objInfraException->adicionarValidacao('Conferência com o Documento Digitalizado de ' . $strRotuloDocumento . ' não informada.');
        }

        if ($strStaFormato !== 'D') {
            $numIdTipoConferencia = null;
        }

        $objDTO->setStrNomeArquivoUpload($strNomeUpload);
        $objDTO->setStrNomeArquivo(trim($arrLinha[self::$COL_NOME_ARQUIVO]));
        $objDTO->setStrComplemento($strComplemento);
        $objDTO->setStrStaFormato($strStaFormato);
        $objDTO->setNumIdTipoConferencia($numIdTipoConferencia);
    }

    /**
     * Tipos de documento externos e ativos, entre os informados.
     *
     * @param int[] $arrIdSerie
     * @return int[]
     */
    private static function listarIdsSerieExternaAtiva($arrIdSerie)
    {
        if (count($arrIdSerie) === 0) {
            return [];
        }

        $objSerieDTO = new SerieDTO();
        $objSerieDTO->retNumIdSerie();
        $objSerieDTO->setStrSinAtivo('S');
        $objSerieDTO->adicionarCriterio(['IdSerie'], [InfraDTO::$OPER_IN], [$arrIdSerie]);
        $objSerieDTO->adicionarCriterio(['StaAplicabilidade'], [InfraDTO::$OPER_IN], [[SerieRN::$TA_INTERNO_EXTERNO, SerieRN::$TA_EXTERNO]]);

        return InfraArray::converterArrInfraDTO((new SerieRN())->listarRN0646($objSerieDTO), 'IdSerie');
    }

    /**
     * Níveis de acesso permitidos no core para o tipo de processo, sem o Sigiloso, que o peticionamento não usa.
     *
     * @param int $numIdTipoProcedimento
     * @return string[]
     */
    private static function listarNiveisAcessoPermitidos($numIdTipoProcedimento)
    {
        $objNivelAcessoPermitidoDTO = new NivelAcessoPermitidoDTO();
        $objNivelAcessoPermitidoDTO->retStrStaNivelAcesso();
        $objNivelAcessoPermitidoDTO->setNumIdTipoProcedimento($numIdTipoProcedimento);

        $arrNiveis = [];

        foreach ((new NivelAcessoPermitidoRN())->listar($objNivelAcessoPermitidoDTO) as $objDTO) {
            if ($objDTO->getStrStaNivelAcesso() !== ProtocoloRN::$NA_SIGILOSO) {
                $arrNiveis[] = $objDTO->getStrStaNivelAcesso();
            }
        }

        return array_values(array_unique($arrNiveis));
    }

    /**
     * @param int $numIdHipoteseLegal
     * @return bool
     */
    private static function isHipoteseLegalAtiva($numIdHipoteseLegal)
    {
        $objHipoteseLegalDTO = new HipoteseLegalDTO();
        $objHipoteseLegalDTO->retNumIdHipoteseLegal();
        $objHipoteseLegalDTO->setNumIdHipoteseLegal($numIdHipoteseLegal);
        $objHipoteseLegalDTO->setStrSinAtivo('S');

        return (new HipoteseLegalRN())->contar($objHipoteseLegalDTO) === 1;
    }

    /**
     * Opções do combo "Disponibilizar acesso para": usuários da unidade, como no Atribuir do core, sem quem já tem acesso.
     *
     * @param int $numIdUnidade
     * @param int[] $arrIdUsuarioComAcesso
     * @return string
     */
    public static function montarSelectUsuarioLiberacao($numIdUnidade, $arrIdUsuarioComAcesso)
    {
        $objUnidadeDTO = new UnidadeDTO();
        $objUnidadeDTO->setNumIdUnidade($numIdUnidade);

        $arrObjUsuarioDTO = [];

        foreach ((new UsuarioRN())->listarPorUnidadeRN0812($objUnidadeDTO) as $objUsuarioDTO) {
            if (!in_array($objUsuarioDTO->getNumIdUsuario(), $arrIdUsuarioComAcesso)) {
                $objUsuarioDTO->setStrSigla(UsuarioINT::formatarSiglaNome($objUsuarioDTO->getStrSigla(), $objUsuarioDTO->getStrNome()));
                $arrObjUsuarioDTO[] = $objUsuarioDTO;
            }
        }

        return parent::montarSelectArrInfraDTO('null', '&nbsp;', null, $arrObjUsuarioDTO, 'IdUsuario', 'Sigla');
    }

    /**
     * Descrição do papel do documento no Peticionamento Interno, para o ícone ao lado do documento na árvore.
     *
     * @param string $strStaPapel
     * @return string
     */
    public static function obterDescricaoPapelDocumento($strStaPapel)
    {
        $arrDescricoes = [
            MdPetPiRelPeticaoDocRN::$PAPEL_PRINCIPAL => 'Documento Principal',
            MdPetPiRelPeticaoDocRN::$PAPEL_ANEXO_OBRIGATORIO => 'Anexo Obrigatório',
            MdPetPiRelPeticaoDocRN::$PAPEL_MAIS_ANEXOS => 'Mais Anexos',
            MdPetPiRelPeticaoDocRN::$PAPEL_ADICIONAL_AUTOMATICO => 'Documento Adicional Gerado Automaticamente',
        ];

        return isset($arrDescricoes[$strStaPapel]) ? $arrDescricoes[$strStaPapel] : '';
    }

    /**
     * Confere a senha do usuário da sessão no SIP, para confirmar o peticionamento com ou sem assinatura.
     * A conclusão confere novamente a senha: pela assinatura ou, no Documento Externo, antes de abrir o processo.
     * O rastro da exceção não vai para o log, porque carrega a senha.
     *
     * @param string $strSenha
     * @return array{Valida: 'S'}|array{MensagemValidacao: string}
     */
    public static function validarSenhaAssinatura(string $strSenha): array
    {
        if ($strSenha === '') {
            return ['MensagemValidacao' => 'Informe a senha.'];
        }

        try {
            (new InfraSip(SessaoSEI::getInstance()))->autenticar(
                SessaoSEI::getInstance()->getNumIdOrgaoUsuario(),
                SessaoSEI::getInstance()->getNumIdContextoUsuario(),
                SessaoSEI::getInstance()->getStrSiglaUsuario(),
                $strSenha
            );
        } catch (InfraException $e) {
            return ['MensagemValidacao' => $e->contemValidacoes() ? $e->__toString() : $e->getMessage()];
        } catch (Exception $e) {
            LogSEI::getInstance()->gravar('Peticionamento Interno: erro conferindo a senha de assinatura: ' . $e->getMessage());

            return ['MensagemValidacao' => 'Não foi possível conferir a senha. Tente novamente.'];
        }

        return ['Valida' => 'S'];
    }
}
