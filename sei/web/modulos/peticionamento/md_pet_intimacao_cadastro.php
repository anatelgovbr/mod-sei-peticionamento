<?php
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 14/03/2017 - criado por pedro.cast
 *
 * Versão do Gerador de Código: 1.40.0
 */

$acao = null;
$arrComandos = [];

try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();
    SessaoSEI::getInstance()->validarLink();
    $acao = PaginaSEI::GET('acao');
    SessaoSEI::getInstance()->validarPermissao($acao);

    $isLoteUnitario = PaginaSEI::POST('modo_lote_unitario') === '1';

    if ($isLoteUnitario) {
        InfraAjax::decodificarPost();
    }

    $dadosCadastro = [
        'selTipoIntimacao' => PaginaSEI::POST('selTipoIntimacao'),
        'selTipoResposta' => (array) PaginaSEI::POST('selTipoResposta'),
        'optIntegral' => PaginaSEI::POST('optIntegral'),
        'optParcial' => PaginaSEI::POST('optParcial'),
        'rdoPossuiAnexo' => PaginaSEI::POST('rdoPossuiAnexo'),
        'hdnIdsDocAnexo' => PaginaSEI::POST('hdnIdsDocAnexo'),
        'hdnIdsDocDisponivel' => PaginaSEI::POST('hdnIdsDocDisponivel'),
        'hdnDadosUsuario' => PaginaSEI::POST('hdnDadosUsuario'),
        'hdnDadosUsuario2' => PaginaSEI::POST('hdnDadosUsuario2'),
        'hdnIdProcedimento' => PaginaSEI::POST('hdnIdProcedimento'),
        'hdnIdDocumento' => PaginaSEI::POST('hdnIdDocumento'),
        'hndIdDocumento' => PaginaSEI::POST('hndIdDocumento'),
        'tipoPessoa' => PaginaSEI::POST('tipoPessoa'),
        'hdnTipoPessoa' => PaginaSEI::POST('hdnTipoPessoa')
    ];

    //////////////////////////////////////////////////////////////////////////////
    //InfraDebug::getInstance()->setBolLigado(false);
    //InfraDebug::getInstance()->setBolDebugInfra(true);
    //InfraDebug::getInstance()->limpar();
    //////////////////////////////////////////////////////////////////////////////

    //Orientações Tipo de Destinatário
    $objMdPetIntOrientacoesDTO2 = new MdPetIntOrientacoesDTO();
    $objMdPetIntOrientacoesDTO2->setNumIdIntOrientTpDest(MdPetIntOrientacoesRN::$ID_FIXO_INT_ORIENTACOES);
    $objMdPetIntOrientacoesDTO2->retTodos();
    $objLista = (new MdPetIntOrientacoesRN())->listar($objMdPetIntOrientacoesDTO2);

    $txtOrientacoes = '';

    if(count($objLista) > 0){
        $txtOrientacoes = $objLista[0]->getStrOrientacoesTipoDestinatario();
    }

    //Pessoa Física
    $strLinkTipoProcessoSelecaoF = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_pessoa_fisica&tipo_selecao=1&id_object=objLupaTipoProcesso');

    //Juridicos
    $strLinkTipoProcessoSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_pessoa_juridica&tipo_selecao=1&id_object=objLupaJuridico');
    $idDocumento = PaginaSEI::GET('id_documento') ?? $dadosCadastro['hdnIdDocumento'];

    $strLinkAjaxUsuariosJuridicos = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_int_usuario_auto_completar_juridica&id_documento=' . $idDocumento);
    $strLinkAjaxJuridicos = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=usuario_dados_tabela_juridica');
    $strParametros = '';
    $arvore = PaginaSEI::GET('arvore');
    if (!is_null($arvore)) {
        PaginaSEI::getInstance()->setBolArvore($arvore);
        $strParametros .= '&arvore=' . $arvore;
    }

    //Inits
    $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
    $arrComandos = array();
    $strLinkAjaxUsuarios = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_int_usuario_auto_completar&id_documento=' . $idDocumento);
    $strLinkAjaxUsuariosLote = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_int_usuario_auto_completar_lote&intimacaoPF=t&id_documento=' . $idDocumento);
    $strLinkAjaxTransportaUsuarios = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=usuario_dados_tabela');
    $strLinkAjaxBuscaTiposRespostaTipoIntimacao = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=busca_tipo_resposta_intimacao');
    $strLinkAjaxValidacoesSubmit = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_int_validar_cadastro');
    $isAlterar = false;
    $countInt = 0;
    $urlTipoFisica = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_intimacao_cadastro_fisica');
    $urlTipoJuridica = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_intimacao_cadastro_juridica');

    switch ($acao) {
        case 'md_pet_intimacao_cadastrar':


            $strEmailAcoes = array('true', 'true');
            $strTitulo = 'Gerar Intimação Eletrônica';

            $idProcedimento = PaginaSEI::GET('id_procedimento') ?? $dadosCadastro['hdnIdProcedimento'];

            $countIntimacao = is_countable($objMdPetIntimacaoRN->getIntimacoesProcesso($idProcedimento)) ? count($objMdPetIntimacaoRN->getIntimacoesProcesso($idProcedimento)) : 0;

            $arrComandos[] = '<button type="button" onclick="onSubmitForm();" accesskey="G" name="sbmCadastrarMdPetIntimacao" id="sbmCadastrarMdPetIntimacao" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">G</span>erar Intimação</button>';
            if($countIntimacao > 0){
                $arrComandos[] = '<button type="button" onclick="infraAbrirJanelaModal(\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_intimacao_eletronica_listar&id_procedimento=' . $idProcedimento) . '\', 1200 , 600 );" class="infraButton" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" accesskey="V"><span class="infraTeclaAtalho">V</span>er intimações do processo ('.$countIntimacao.')</button>';
            }
            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $acao) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            $objDocumentoDTO = new DocumentoDTO();
            $objDocumentoDTO->retDblIdDocumento();
            $objDocumentoDTO->retDblIdProcedimento();
            $objDocumentoDTO->retNumIdOrgaoUnidadeResponsavel();
            $objDocumentoDTO->retStrProtocoloDocumentoFormatado();
            $objDocumentoDTO->retStrNomeSerie();
            $objDocumentoDTO->retStrNumero();
            $objDocumentoDTO->retNumIdSerie();
            $objDocumentoDTO->setDblIdDocumento($idDocumento);
            $objDocumentoRN = new DocumentoRN();
            $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

            $strProtocoloDocumentoFormatado = !is_null($objDocumentoDTO) ? $objDocumentoDTO->getStrProtocoloDocumentoFormatado() : '';

//            Buscar Intimações cadastradas.
            $arrIntimacoes = $objMdPetIntimacaoRN->buscaIntimacoesCadastradas($idDocumento);
            $isAlterar = (!empty($arrIntimacoes)) ? true : false;

            if ($isLoteUnitario) {
                header('Content-Type: application/json; charset=utf-8');

                try {
                    if (!in_array($dadosCadastro['tipoPessoa'], ['F', 'J'], true)
                        || $dadosCadastro['hdnTipoPessoa'] !== $dadosCadastro['tipoPessoa']) {
                        throw new InfraException('Tipo de destinatario invalido.');
                    }

                    foreach (['hdnIdProcedimento', 'hdnIdDocumento', 'hndIdDocumento'] as $campoId) {
                        if (!ctype_digit((string) $dadosCadastro[$campoId]) || (int) $dadosCadastro[$campoId] <= 0) {
                            throw new InfraException('Identificador invalido para gerar a intimacao.');
                        }
                    }

                    if (!ctype_digit((string) $dadosCadastro['selTipoIntimacao'])
                        || (int) $dadosCadastro['selTipoIntimacao'] <= 0) {
                        throw new InfraException('Tipo de intimacao invalido.');
                    }

                    $tipoAcesso = $dadosCadastro['optIntegral'] ?: $dadosCadastro['optParcial'];
                    if (!in_array($tipoAcesso, [
                        MdPetIntAcessoExternoDocumentoRN::$ACESSO_INTEGRAL,
                        MdPetIntAcessoExternoDocumentoRN::$ACESSO_PARCIAL
                    ], true) || (!empty($dadosCadastro['optIntegral']) && !empty($dadosCadastro['optParcial']))) {
                        throw new InfraException('Tipo de acesso externo invalido.');
                    }

                    foreach ($dadosCadastro['selTipoResposta'] as $idTipoResposta) {
                        if (!ctype_digit((string) $idTipoResposta) || (int) $idTipoResposta <= 0) {
                            throw new InfraException('Tipo de resposta invalido.');
                        }
                    }

                    $dadosCadastro['rdoPossuiAnexo'] = $dadosCadastro['rdoPossuiAnexo'] === 'S' ? 'S' : 'N';

                    if ((string) $dadosCadastro['hdnIdDocumento'] !== (string) $dadosCadastro['hndIdDocumento']) {
                        throw new InfraException('Documento principal divergente.');
                    }

                    if (is_null($objDocumentoDTO)
                        || (int) $objDocumentoDTO->getDblIdProcedimento() !== (int) $dadosCadastro['hdnIdProcedimento']) {
                        throw new InfraException('Documento principal nao pertence ao processo informado.');
                    }

                    $arrDestinatarios = PaginaSEI::getInstance()->getArrItensTabelaDinamica($dadosCadastro['hdnDadosUsuario2']);
                    if (count($arrDestinatarios) !== 1
                        || !ctype_digit((string) $arrDestinatarios[0][0])
                        || (int) $arrDestinatarios[0][0] <= 0
                        || !isset($arrDestinatarios[0][1])
                        || trim((string) $arrDestinatarios[0][1]) === '') {
                        throw new InfraException('A requisicao deve conter exatamente um destinatario valido.');
                    }

                    $dadosCadastro['hdnDadosUsuario'] = $dadosCadastro['hdnDadosUsuario2'];
                    $mensagemDuplicidade = $objMdPetIntimacaoRN->verificarDuplicidadeIntimacao($dadosCadastro);

                    if ($mensagemDuplicidade !== '') {
                        echo json_encode([
                            'status' => 'ignorado',
                            'mensagem' => mb_convert_encoding($mensagemDuplicidade, 'UTF-8', 'ISO-8859-1')
                        ], JSON_UNESCAPED_UNICODE);
                        die;
                    }

                    $objMdPetIntimacaoDTO = $objMdPetIntimacaoRN->cadastrarIntimacao($dadosCadastro);
                    $idIntimacao = $objMdPetIntimacaoDTO->getNumIdMdPetIntimacao();
                    
                    $strLinkMontarArvore = SessaoSEI::getInstance()->assinarLink(
                        'controlador.php?acao=procedimento_visualizar&acao_origem=' . PaginaSEI::GET('acao')
                        . '&montar_visualizacao=1&arvore=1&id_procedimento=' . $objDocumentoDTO->getDblIdProcedimento()
                        . '&id_documento=' . $objDocumentoDTO->getDblIdDocumento()
                    );
                    $strLinkNotificacao = SessaoSEI::getInstance()->assinarLink(
                        'modulos/peticionamento/controlador_ajax.php?acao_ajax=md_pet_notificar_intimacao&id_intimacao=' . $idIntimacao
                    );

                    echo json_encode([
                        'status' => 'sucesso',
                        'id_intimacao' => (int) $idIntimacao,
                        'url_notificacao' => $strLinkNotificacao,
                        'url_arvore' => $strLinkMontarArvore
                    ], JSON_UNESCAPED_UNICODE);

                } catch (Exception $e) {
                    LogSEI::getInstance()->gravar(
                        'Erro gerando unidade de intimacao em lote [' . get_class($e) . '].',
                        InfraLog::$INFORMACAO
                    );
                    http_response_code(400);
                    echo json_encode([
                        'status' => 'erro',
                        'mensagem' => 'Nao foi possivel gerar a intimacao deste destinatario.'
                    ]);
                }

                die;
            }

            if (!is_null($dadosCadastro['hdnIdDocumento'])) {

                try {
                    $objMdPetIntimacaoDTO = $objMdPetIntimacaoRN->cadastrarIntimacao($dadosCadastro);

                    if ($objMdPetIntimacaoDTO) {
                        $idProcedimento = $objDocumentoDTO->getDblIdProcedimento();

                        $strLinkMontarArvore = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=procedimento_visualizar&acao_origem='.$acao.'&montar_visualizacao=1&arvore=1&id_procedimento='.$idProcedimento .'&id_documento='.$objDocumentoDTO->getDblIdDocumento());
                        $strLinkNotificacao = SessaoSEI::getInstance()->assinarLink(
                            'modulos/peticionamento/controlador_ajax.php?acao_ajax=md_pet_notificar_intimacao&id_intimacao='
                            . $objMdPetIntimacaoDTO->getNumIdMdPetIntimacao()
                        );

	                    echo "<script>";
                        echo "fetch(" . json_encode($strLinkNotificacao) . ", {method: 'POST', credentials: 'same-origin', keepalive: true}).catch(function() {});";
	                    echo "window.focus();";
	                    echo "window.parent.parent.document.getElementById('ifrArvore').src = '".$strLinkMontarArvore."';";
	                    echo "parent.infraFecharJanelaModal();";
	                    echo "</script>";
                        //necessário para atualizara a arvore do processo e mostra caneta preta de imediato
                        die;
                    }
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }

            break;

        default:
            throw new InfraException("Ação '" . $acao . "' não reconhecida.");
    }


} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo);
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();

require_once 'md_pet_intimacao_cadastro_css.php';

PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();

require_once 'js/md_pet_global_js.php';
require_once 'md_pet_intimacao_cadastro_js.php';

PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>

<form id="frmMdPetIntimacaoCadastro"
      method="post"
    action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $acao . '&acao_origem=' . $acao) ?>">

    <? PaginaSEI::getInstance()->abrirAreaDados(); ?>
    <? PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos); ?><br>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <fieldset id="fldOrientacoesDestinatarios" class="infraFieldset sizeFieldset form-control" style="width:auto; min-height: 250px">
                <legend class="infraLegend" class="infraLabelObrigatorio"> Tipo de Destinatário</legend>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <div class="mb-5">
                            <?= $txtOrientacoes ?>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top:-10px">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <div id="divOptTipoPessoaFisica" class="infraDivRadio">
                            <div class="form-group">
                                <div class="infraRadioDiv ">
                                    <input type="radio" id="tipoPessoaFisica" name="tipoPessoa" value="F"
                                        class="infraRadioInput"
                                        onclick="intimacaoTipoPessoa(this.value)"
                                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                    <label class="infraRadioLabel" for="tipoPessoaFisica"></label>
                                </div>
                                <span id="spnFisica">
                                    <label id="lblFisica" for="tipoPessoaFisica" accesskey="" class="infraLabelRadio">Pessoa Física</label><br>
                                </span>
                            </div>
                        </div>
                        <div id="divOptTipoPessoaJuridica" class="infraDivRadio">
                            <div class="form-group">
                                <div class="infraRadioDiv ">
                                    <input type="radio" id="tipoPessoaJuridica" name="tipoPessoa" value="J"
                                        class="infraRadioInput"
                                        onclick="intimacaoTipoPessoa(this.value)"
                                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                    <label class="infraRadioLabel" for="tipoPessoaJuridica"></label>
                                </div>
                                <span id="spnJuridica">
                                    <label id="lblJuridica" for="tipoPessoaJuridica" accesskey="" class="infraLabelRadio">Pessoa Jurídica</label>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
    <div id="div_tipo_destinatario"></div>
    <?php PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos); ?>
    <?php PaginaSEI::getInstance()->fecharAreaDados(); ?>

</form>
<?php PaginaSEI::getInstance()->fecharBody(); ?>

<script type="text/javascript">

    function intimacaoTipoPessoa(tipo) {

        try {

            var tbUsuarios = document.getElementById('hdnDadosUsuario2');
            var tipoPessoa = null;

            if (tbUsuarios.value != "") {

                tipoPessoa = document.getElementById('hdnTipoPessoa').value == "J" ? "tipoPessoaJuridica" : "tipoPessoaFisica";

                var r = confirm("Os dados preenchidos serão desconsiderados. Deseja Continuar?");

                if (r == false) {
                    document.getElementById(tipoPessoa).checked = true;
                    return;
                }

            }

        } catch (err) {

        }

        url = tipo == 'F' ? '<?=$urlTipoFisica?>' : '<?=$urlTipoJuridica?>';

        $.ajax({
            async: true,
            type: "POST",
            url: url,
            data: {
                id_documento: <?= $idDocumento?>,
                id_procedimento: <?= (int) $idProcedimento ?>
                <?php if($isAlterar){?>
                , is_alterar: <?= $isAlterar ?>
                <?php }?>
            },
            success: function (result) {
                $('#div_tipo_destinatario').html(result);
                if (tipo == 'F') {
                    preparaPessoaFisica();
                } else {
                    preparaPessoaJuridica(tipo);
                }
            },
            error: function (msgError) {
                msgCommit = "Erro selecionar tipo de destinatário: " + msgError.responseText;
                console.log(msgCommit);
            },
            complete: function (result) {
                infraAvisoCancelar();
            }
        });
    }
</script>

<?php PaginaSEI::getInstance()->fecharHtml(); ?>
