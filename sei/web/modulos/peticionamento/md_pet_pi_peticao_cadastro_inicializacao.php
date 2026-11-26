<?php
/**
 * ANATEL
 *
 * Carga da configuração, links e variáveis da tela de Peticionamento Interno.
 *
 * 17/09/2026 - criado por emerson_b.goncalves - ANATEL
 */

$strTitulo = 'Peticionamento Interno - Iniciar Processo';
$arrComandos = [];

$numIdMdPetPiTpProcesso = null;
$strLinkOrientacoes = '';
$strNomeTipoProcedimento = '';
$arrAssuntos = [];
$strItensSelTipoPrioridade = '';
$strStaIndicacaoInteressado = '';
$strNomeIndicacaoInteressado = '';
$strMsgTooltipInteressado = '';

$strStaAplicabilidadeDocPrinc = '';
$bolDocPrincExterno = false;
$bolDocPrincFormulario = false;
$numTamanhoMaximoDocPrinc = 0;
$strJsExtensoesDocPrinc = '';
$numIdSerieDocPrinc = null;
$strNomeDocPrinc = '';
$strStaNivelAcessoDocPrinc = '';
$strNomeNivelAcessoDocPrinc = '';
$numIdHipoteseLegalDocPrinc = null;
$strNomeHipoteseLegalDocPrinc = '';
$strItensSelTipoConferencia = '';
$strLinkUploadDocPrinc = '';
$strLinkDocumentoJanela = '';
$strConteudoDocPrincPreenchido = '';

$arrObjMdPetPiAnexoObrigDTO = [];
$bolTemAnexoObrig = false;
$strItensSelSerieAnexoObrig = '';
$strJsConfigAnexoObrig = '{}';
$strItensSelSerieMaisAnexos = '';
$strItensSelNivelAcessoMaisAnexos = '';
$strItensSelHipoteseLegalMaisAnexos = '';
$numTamanhoMaximoAnexo = 0;
$strJsExtensoesAnexo = '';
$strLinkUploadAnexo = '';
$strLinkRemoverArquivo = '';
$strLinkConfirmacao = '';

$strEspecificacao = MdPetPiEntradaINT::postStr('txtEspecificacao');
$numIdTipoPrioridade = MdPetPiEntradaINT::postInt('selTipoPrioridade');
$strGridDocPrincipal = MdPetPiEntradaINT::postStr('hdnDocPrincipal');
$strGridAnexoObrig = MdPetPiEntradaINT::postStr('hdnAnexoObrig');
$strGridMaisAnexos = MdPetPiEntradaINT::postStr('hdnMaisAnexos');

// Textos provisórios, a definir com o negócio (QA-03)
$strMsgTooltipTipoDocPrinc = 'O Tipo de Documento do Documento Principal é definido na configuração deste Tipo de Processo. Complemente o Tipo no campo ao lado.';
$strMsgTooltipComplementoDocPrinc = 'O Complemento do Tipo de Documento é o texto que completa a identificação do documento, somado ao nome do Tipo.'
    . "\n\n" . 'Exemplo: Tipo "Requerimento" com Complemento "de Licença" identifica o documento como "Requerimento de Licença".';
$strMsgTooltipNivelAcessoDocPrinc = 'O Nível de Acesso do Documento Principal é pré-definido na configuração deste Tipo de Processo.';
$strMsgTooltipHipoteseLegalDocPrinc = 'Para o Nível de Acesso "Restrito", a Hipótese Legal também é pré-definida na configuração deste Tipo de Processo.';
$strMsgTooltipNivelAcessoEscolha = 'Escolha o Nível de Acesso do documento, entre os permitidos para este Tipo de Processo.';
$strMsgTooltipHipoteseLegalEscolha = 'Com o Nível de Acesso "Restrito", informe a Hipótese Legal.';
$strMsgTooltipNivelAcessoAnexoObrig = 'O Nível de Acesso de cada Tipo de Documento segue a configuração deste Tipo de Processo: pode apresentar pré-definido e bloqueado ou fica liberado para sua indicação direta.';
$strMsgTooltipHipoteseLegalAnexoObrig = 'Com o Nível de Acesso "Restrito", a Hipótese Legal segue a configuração deste Tipo de Processo: pode apresentar pré-definida e bloqueada ou fica liberada para sua indicação direta.';
$strMsgTooltipFormatoDocPrinc = 'Selecione "Nato-digital" se o arquivo foi criado originalmente em meio eletrônico.'
    . "\n\n" . 'Selecione "Digitalizado" somente se o arquivo foi produzido a partir da digitalização de um documento em papel.';
$strMsgTooltipDocumentoJanela = 'O conteúdo deste documento é preenchido em outra janela. Depois de salvar, use o botão Adicionar para incluí-lo na lista.';
$strMsgTooltipAnexoObrig = 'Este Tipo de Processo exige carregar ao menos um arquivo de cada Tipo de Documento listado.';
$strMsgTooltipMaisAnexos = 'Use este quadro para anexar outros documentos que considerar necessários. Nenhum deles é obrigatório.';
$strMsgTooltipNivelAcessoMaisAnexos = 'Escolha o Nível de Acesso de cada documento que anexar.';
$strMsgTooltipHipoteseLegalMaisAnexos = 'Informe a Hipótese Legal que justifica o Nível de Acesso "Restrito".';

$strLinkIniciar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_pi_peticao_iniciar&acao_origem=' . $strAcao);

$objMdPetPiTpProcessoDTO = new MdPetPiTpProcessoDTO();
$objMdPetPiTpProcessoDTO->retStrNomeTipoProcedimento();
$objMdPetPiTpProcessoDTO->retStrOrientacoes();
$objMdPetPiTpProcessoDTO->retNumIdConjuntoEstilos();
$objMdPetPiTpProcessoDTO->retStrStaIndicacaoInteressado();
$objMdPetPiTpProcessoDTO->setNumIdMdPetPiTpProcesso((int) MdPetPiEntradaINT::getInt('id_md_pet_pi_tp_processo'));

$arrObjMdPetPiTpProcessoDTO = (new MdPetPiTpProcessoRN())->listarDisponiveis($objMdPetPiTpProcessoDTO);

if (count($arrObjMdPetPiTpProcessoDTO) === 0) {
    PaginaSEI::getInstance()->adicionarMensagem('Tipo de Processo não disponível para Peticionamento Interno.', InfraPagina::$TIPO_MSG_AVISO);
    header('Location: ' . $strLinkIniciar);
    die;
}

$objMdPetPiTpProcessoDTO = $arrObjMdPetPiTpProcessoDTO[0];
$numIdMdPetPiTpProcesso = $objMdPetPiTpProcessoDTO->getNumIdMdPetPiTpProcesso();
$numIdTipoProcedimento = $objMdPetPiTpProcessoDTO->getNumIdTipoProcedimento();

if (MdPetPiEntradaINT::getStr('iframe') === 'S') {
    $strOrientacoesHtml = $objMdPetPiTpProcessoDTO->getStrOrientacoes();
    $numIdConjuntoEstilosOrientacoes = $objMdPetPiTpProcessoDTO->getNumIdConjuntoEstilos();
    require_once 'md_pet_pi_peticao_orientacoes.php';
    die;
}

$strLinkOrientacoes = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $strAcao . '&iframe=S&id_md_pet_pi_tp_processo=' . $numIdMdPetPiTpProcesso);
$strLinkConfirmacao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_pi_peticao_confirmar&acao_origem=' . $strAcao . '&id_md_pet_pi_tp_processo=' . $numIdMdPetPiTpProcesso);
$strLinkRemoverArquivo = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_pi_peticao_remover_arquivo');

// Metadados do Processo
$strNomeTipoProcedimento = $objMdPetPiTpProcessoDTO->getStrNomeTipoProcedimento();
$strStaIndicacaoInteressado = $objMdPetPiTpProcessoDTO->getStrStaIndicacaoInteressado();
$strNomeIndicacaoInteressado = MdPetPiPeticaoINT::listarValoresIndicacaoInteressado()[$strStaIndicacaoInteressado] ?? '';
$strMsgTooltipInteressado = MdPetPiPeticaoINT::listarAjudasIndicacaoInteressado()[$strStaIndicacaoInteressado] ?? '';
$arrAssuntos = MdPetPiPeticaoINT::listarAssuntosTipoProcedimento($numIdTipoProcedimento);
$strItensSelTipoPrioridade = TipoPrioridadeINT::montarSelectIdTipoPrioridade('null', '&nbsp;', $numIdTipoPrioridade);

$strItensSelTipoConferencia = TipoConferenciaINT::montarSelectDescricao('null', '&nbsp;', null);

// Documento Principal
$objMdPetPiDocPrincDTO = new MdPetPiDocPrincDTO();
$objMdPetPiDocPrincDTO->retTodos();
$objMdPetPiDocPrincDTO->retStrNomeSerie();
// $objMdPetPiDocPrincDTO->retStrNomeTipoFormulario();
$objMdPetPiDocPrincDTO->retStrNomeHipoteseLegal();
$objMdPetPiDocPrincDTO->setNumIdMdPetPiTpProcesso($numIdMdPetPiTpProcesso);

$objMdPetPiDocPrincDTO = (new MdPetPiDocPrincRN())->consultar($objMdPetPiDocPrincDTO);

if ($objMdPetPiDocPrincDTO === null) {
    throw new InfraException('Documento Principal não configurado para este Tipo de Processo.');
}

$strStaAplicabilidadeDocPrinc = $objMdPetPiDocPrincDTO->getStrStaAplicabilidade();
$bolDocPrincExterno = $strStaAplicabilidadeDocPrinc === MdPetPiTpProcessoRN::$DOC_EXTERNO;
$bolDocPrincFormulario = $strStaAplicabilidadeDocPrinc === MdPetPiTpProcessoRN::$DOC_FORMULARIO;

$numIdSerieDocPrinc = $objMdPetPiDocPrincDTO->getNumIdSerie();
$strNomeDocPrinc = $objMdPetPiDocPrincDTO->getStrNomeSerie();

$bolDocPrincUsuarioIndica = $objMdPetPiDocPrincDTO->getStrStaTipoNivelAcesso() === MdPetPiTpProcessoRN::$TNA_USUARIO;
$bolHipoteseLegalHabilitada = MdPetPiTpProcessoRN::isHipoteseLegalHabilitada();
$strStaNivelAcessoDocPrinc = (string) $objMdPetPiDocPrincDTO->getStrStaNivelAcesso();
$strNomeNivelAcessoDocPrinc = $bolDocPrincUsuarioIndica ? '' : ProtocoloINT::montarStaNivelAcesso($strStaNivelAcessoDocPrinc);
$numIdHipoteseLegalDocPrinc = $objMdPetPiDocPrincDTO->getNumIdHipoteseLegal();
$strNomeHipoteseLegalDocPrinc = $objMdPetPiDocPrincDTO->getStrNomeHipoteseLegal();

if ($bolDocPrincExterno) {
    $numTamanhoMaximoDocPrinc = (int) MdPetPiPeticaoINT::obterTamanhoMaximoDocPrincipalMb();
    $strJsExtensoesDocPrinc = MdPetPiPeticaoINT::montarJsExtensoesPermitidas($numTamanhoMaximoDocPrinc);
    $strLinkUploadDocPrinc = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_pi_peticao_upload_principal&acao_origem=' . $strAcao);
} else {
    $strAcaoJanela = $bolDocPrincFormulario ? 'md_pet_pi_peticao_formulario_gerar' : 'md_pet_pi_peticao_editor_montar';

    $strLinkDocumentoJanela = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $strAcaoJanela . '&acao_origem=' . $strAcao . '&id_md_pet_pi_tp_processo=' . $numIdMdPetPiTpProcesso);
    $strConteudoDocPrincPreenchido = SessaoSEI::getInstance()->isSetAtributo(MdPetPiPeticaoINT::obterAtributoConteudoDocPrincipal($numIdMdPetPiTpProcesso)) ? 'S' : '';
}

// Anexos Obrigatórios e Mais Anexos
$arrObjMdPetPiAnexoObrigDTO = MdPetPiPeticaoINT::listarAnexosObrigatorios($numIdMdPetPiTpProcesso);
$bolTemAnexoObrig = count($arrObjMdPetPiAnexoObrigDTO) > 0;

// Mesmo formato do select habilitado, com as bases legais carregadas em lote.
$arrIdsHipoteseLegal = [$numIdHipoteseLegalDocPrinc];
foreach ($arrObjMdPetPiAnexoObrigDTO as $objAnexoObrigDTO) {
    $arrIdsHipoteseLegal[] = $objAnexoObrigDTO->getNumIdHipoteseLegal();
}
$arrIdsHipoteseLegal = array_values(array_unique(array_filter($arrIdsHipoteseLegal)));

if (count($arrIdsHipoteseLegal) > 0) {
    $objHipoteseLegalDTO = new HipoteseLegalDTO();
    $objHipoteseLegalDTO->retNumIdHipoteseLegal();
    $objHipoteseLegalDTO->retStrNome();
    $objHipoteseLegalDTO->retStrBaseLegal();
    $objHipoteseLegalDTO->setBolExclusaoLogica(false);
    $objHipoteseLegalDTO->setNumIdHipoteseLegal($arrIdsHipoteseLegal, InfraDTO::$OPER_IN);

    $arrNomesHipoteseLegal = [];
    foreach ((new HipoteseLegalRN())->listar($objHipoteseLegalDTO) as $objHipoteseLegalDTO) {
        $arrNomesHipoteseLegal[$objHipoteseLegalDTO->getNumIdHipoteseLegal()] = HipoteseLegalINT::formatarHipoteseLegal(
            $objHipoteseLegalDTO->getStrNome(), $objHipoteseLegalDTO->getStrBaseLegal()
        );
    }

    $strNomeHipoteseLegalDocPrinc = $arrNomesHipoteseLegal[$numIdHipoteseLegalDocPrinc] ?? $strNomeHipoteseLegalDocPrinc;
    foreach ($arrObjMdPetPiAnexoObrigDTO as $objAnexoObrigDTO) {
        $objAnexoObrigDTO->setStrNomeHipoteseLegal($arrNomesHipoteseLegal[$objAnexoObrigDTO->getNumIdHipoteseLegal()] ?? $objAnexoObrigDTO->getStrNomeHipoteseLegal());
    }
}

if ($bolTemAnexoObrig) {
    $strItensSelSerieAnexoObrig = MdPetPiPeticaoINT::montarSelectAnexosObrigatorios($arrObjMdPetPiAnexoObrigDTO);
    $strJsConfigAnexoObrig = MdPetPiPeticaoINT::montarJsConfigAnexosObrigatorios($arrObjMdPetPiAnexoObrigDTO);
}

$strItensSelSerieMaisAnexos = MdPetPiTpProcessoINT::montarSelectTipoDocumento('null', '&nbsp;', null, MdPetPiTpProcessoRN::$DOC_EXTERNO);
$strItensSelNivelAcessoMaisAnexos = MdPetPiTpProcessoINT::montarSelectNivelAcesso('null', '&nbsp;', null, $numIdTipoProcedimento);
$strItensSelHipoteseLegalMaisAnexos = MdPetPiTpProcessoINT::montarSelectHipoteseLegal('null', '&nbsp;', null, ProtocoloRN::$NA_RESTRITO);

$numTamanhoMaximoAnexo = (int) MdPetPiPeticaoINT::obterTamanhoMaximoAnexoMb();
$strJsExtensoesAnexo = MdPetPiPeticaoINT::montarJsExtensoesPermitidas($numTamanhoMaximoAnexo);
$strLinkUploadAnexo = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_pi_peticao_upload_anexo&acao_origem=' . $strAcao);

$arrComandos[] = '<button type="button" accesskey="p" name="btnPeticionar" id="btnPeticionar" value="Peticionar" onclick="peticionar();" class="infraButton"><span class="infraTeclaAtalho">P</span>eticionar</button>';

if (SessaoSEI::getInstance()->verificarPermissao('md_pet_pi_peticao_iniciar')) {
    $arrComandos[] = '<button type="button" accesskey="v" name="btnVoltar" id="btnVoltar" value="Voltar" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML($strLinkIniciar . PaginaSEI::getInstance()->montarAncora($numIdMdPetPiTpProcesso)) . '\';" class="infraButton"><span class="infraTeclaAtalho">V</span>oltar</button>';
}

// Conclusão: só depois da confirmação no modal. Erro de validação volta para a tela com o preenchimento preservado.
if (MdPetPiEntradaINT::postStr('hdnConfirmado') === 'S') {

    try {
        if ($bolDocPrincExterno) {
            // ASVS 2.2.2 e 2.3.1: conferir a senha no servidor antes de abrir o processo.
            $arrValidacaoSenha = MdPetPiPeticaoINT::validarSenhaAssinatura(is_string(PaginaSEI::POST('pwdSenha')) ? PaginaSEI::POST('pwdSenha') : '');

            if (isset($arrValidacaoSenha['MensagemValidacao'])) {
                (new InfraException())->lancarValidacao($arrValidacaoSenha['MensagemValidacao']);
            }
        }

        $objMdPetPiPeticaoDTO = new MdPetPiPeticaoDTO();
        $objMdPetPiPeticaoDTO->setNumIdMdPetPiTpProcesso($numIdMdPetPiTpProcesso);
        $objMdPetPiPeticaoDTO->setStrEspecificacao($strEspecificacao);
        $objMdPetPiPeticaoDTO->setNumIdTipoPrioridade($numIdTipoPrioridade);
        $objMdPetPiPeticaoDTO->setStrCargoFuncaoAssinatura(MdPetPiEntradaINT::postStr('hdnCargoFuncaoAssinatura'));
        $objMdPetPiPeticaoDTO->setStrSenhaAssinatura(is_string(PaginaSEI::POST('pwdSenha')) ? PaginaSEI::POST('pwdSenha') : '');
        $objMdPetPiPeticaoDTO->setArrObjMdPetPiRelPeticaoDocDTO(MdPetPiPeticaoINT::montarDocumentosPeticionamento(
            $objMdPetPiDocPrincDTO,
            $arrObjMdPetPiAnexoObrigDTO,
            $numIdTipoProcedimento,
            ['principal' => $strGridDocPrincipal, 'obrigatorio' => $strGridAnexoObrig, 'mais' => $strGridMaisAnexos]
        ));

        $objMdPetPiPeticaoRN = new MdPetPiPeticaoRN();
        $objMdPetPiPeticaoDTO = $objMdPetPiPeticaoRN->cadastrar($objMdPetPiPeticaoDTO);

        // Depois do commit: indexação da assinatura e limpeza da área temporária
        if ($objMdPetPiPeticaoDTO->getDblIdDocumentoAssinado() !== null) {
            (new InfraDadoUsuario(SessaoSEI::getInstance()))->setValor('ASSINATURA_CARGO_FUNCAO_' . SessaoSEI::getInstance()->getNumIdUnidadeAtual(), $objMdPetPiPeticaoDTO->getStrCargoFuncaoAssinatura());

            try {
                $objMdPetPiPeticaoRN->indexarDocumentoAssinado($objMdPetPiPeticaoDTO);
            } catch (Exception $eIndexacao) {
                LogSEI::getInstance()->gravar('Peticionamento Interno concluído sem a indexação da assinatura:' . "\n" . InfraException::inspecionar($eIndexacao));
            }
        }

        MdPetPiPeticaoINT::limparDadosPeticionamento($numIdMdPetPiTpProcesso);

        header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=procedimento_trabalhar&acao_origem=' . $strAcao . '&id_procedimento=' . $objMdPetPiPeticaoDTO->getDblIdProcedimento()));
        die;

    } catch (Exception $e) {
        PaginaSEI::getInstance()->processarExcecao($e);
    }
}
