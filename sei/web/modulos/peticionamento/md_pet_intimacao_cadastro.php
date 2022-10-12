<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 14/03/2017 - criado por pedro.cast
 *
 * Versão do Gerador de Código: 1.40.0
 */

try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();
    SessaoSEI::getInstance()->validarLink();
    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    //////////////////////////////////////////////////////////////////////////////
    //InfraDebug::getInstance()->setBolLigado(false);
    //InfraDebug::getInstance()->setBolDebugInfra(true);
    //InfraDebug::getInstance()->limpar();
    //////////////////////////////////////////////////////////////////////////////

    //Pessoa Física
    $strLinkTipoProcessoSelecaoF = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_pessoa_fisica&tipo_selecao=1&id_object=objLupaTipoProcesso');

    //Juridicos
    $strLinkTipoProcessoSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_pessoa_juridica&tipo_selecao=1&id_object=objLupaJuridico');
    $idDocumento = isset($_GET['id_documento']) ? $_GET['id_documento'] : $_POST['hdnIdDocumento'];

    $strLinkAjaxUsuariosJuridicos = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_int_usuario_auto_completar_juridica&id_documento=' . $idDocumento);
    $strLinkAjaxJuridicos = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=usuario_dados_tabela_juridica');
    $strParametros = '';
    if (isset($_GET['arvore'])) {
        PaginaSEI::getInstance()->setBolArvore($_GET['arvore']);
        $strParametros .= '&arvore=' . $_GET['arvore'];
    }

    //Inits
    $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
    $arrComandos = array();
    $idDocumento = isset($_GET['id_documento']) ? $_GET['id_documento'] : $_POST['hdnIdDocumento'];
    $strLinkAjaxUsuarios = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_int_usuario_auto_completar&id_documento=' . $idDocumento);
    $strLinkAjaxTransportaUsuarios = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=usuario_dados_tabela');
    $strLinkAjaxBuscaTiposRespostaTipoIntimacao = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=busca_tipo_resposta_intimacao');
    $strLinkAjaxValidacoesSubmit = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_int_validar_cadastro');
    $isAlterar = false;
    $countInt = 0;
    $urlTipoFisica = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_intimacao_cadastro_fisica');
    $urlTipoJuridica = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_intimacao_cadastro_juridica');

    switch ($_GET['acao']) {
        case 'md_pet_intimacao_cadastrar':


            $strEmailAcoes = array('true', 'true');
            $strTitulo = 'Gerar Intimação Eletrônica';

            $arrComandos[] = '<button type="button" onclick="onSubmitForm();" accesskey="G" name="sbmCadastrarMdPetIntimacao" id="sbmCadastrarMdPetIntimacao" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">G</span>erar Intimação</button>';

            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

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

            if (count($_POST) > 0) {

                try {
                    $objMdPetIntimacaoDTO = $objMdPetIntimacaoRN->cadastrarIntimacao($_POST);

                    if ($objMdPetIntimacaoDTO) {
                        $idProcedimento = $objDocumentoDTO->getDblIdProcedimento();

                        $url = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=arvore_visualizar&acao_origem=' . $_GET['acao'] . '&id_procedimento=' . $idProcedimento . '&id_documento=' . $objDocumentoDTO->getDblIdDocumento());
                        echo "<script>";
                        echo "window.location = '" . $url . "';";
                        echo "window.focus();";
                        echo "parent.infraFecharJanelaModal();";
                        echo "</script>";
                        //necessário para atualizara a arvore do processo e mostra caneta preta de imediato
                        die;
                    }
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }

            /*
            $objMdPetIntPrazoTacitaDTO = new MdPetIntPrazoTacitaDTO();
            $objMdPetIntPrazoTacitaDTO->setBolExclusaoLogica(false);
            $objMdPetIntPrazoTacitaDTO->retTodos();

            $objMdPetIntPrazoTacitaRN = new MdPetIntPrazoTacitaRN();
            $objMdPetIntPrazoTacitaDTO = $objMdPetIntPrazoTacitaRN->consultar($objMdPetIntPrazoTacitaDTO);
            $numNumPrazo = null;
            if ( !is_null( $objMdPetIntPrazoTacitaDTO ) ) {
                $numNumPrazo = $objMdPetIntPrazoTacitaDTO->getNumNumPrazo();
            }
            */
            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
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

require_once 'md_pet_intimacao_cadastro_js.php';

PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>

<form id="frmMdPetIntimacaoCadastro"
      method="post"
      action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">

    <? PaginaSEI::getInstance()->abrirAreaDados(); ?>
    <? PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos); ?><br>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <fieldset id="fldOrientacoesDestinatarios" class="infraFieldset sizeFieldset form-control"
                      style="width:auto; min-height: 250px">
                <legend class="infraLegend" class="infraLabelObrigatorio"> Tipo de Destinatário</legend>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <?= PaginaSEI::tratarHTML($txtConteudo) ?>
                        <?php echo $txtConteudo; ?>
                        <?= '<iframe id=ifrConteudoHTML name=ifrConteudoHTML style="height:100%;width:100%" frameborder="0" marginheight="0" marginwidth="0" src="' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_int_orientacoes_destinatario&iframe=S') . '"></iframe>'; ?>
                    </div>
                </div>
                <div class="row">
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
<?php

PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>

<script type="text/javascript">


    function OnSubmitForm() {
        return false;
    }

    function resizeIFramePorConteudo() {
        var id = 'ifrConteudoHTML';
        var ifrm = document.getElementById(id);
        ifrm.style.visibility = 'hidden';
        ifrm.style.height = "10px";

        var doc = ifrm.contentDocument ? ifrm.contentDocument : ifrm.contentWindow.document;
        doc = doc || document;
        var body = doc.body, html = doc.documentElement;

        var width = Math.max(body.scrollWidth, body.offsetWidth,
            html.clientWidth, html.scrollWidth, html.offsetWidth);
        ifrm.style.width = '100%';

        var height = Math.max(body.scrollHeight, body.offsetHeight,
            html.clientHeight, html.scrollHeight, html.offsetHeight);
        ifrm.style.height = height + 'px';

        ifrm.style.visibility = 'visible';
    }

    document.getElementById('ifrConteudoHTML').onload = function () {
        resizeIFramePorConteudo();
    }


    function intimacaoTipoPessoa(tipo) {

        try {

            var tbUsuarios = document.getElementById('hdnDadosUsuario');
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
                id_procedimento: <?= $_GET['id_procedimento'] ?>
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
