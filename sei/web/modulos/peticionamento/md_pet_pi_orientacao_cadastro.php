<?php
/**
 * ANATEL
 *
 * Orientacoes Gerais exibidas ao Usuario na tela de escolha do Tipo de Processo
 * do Peticionamento Interno. Registro unico.
 *
 * 03/09/2026 - criado por emerson_b.goncalves - ANATEL
 */
try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    PaginaSEI::getInstance()->setBolXHTML(false);

    SessaoSEI::getInstance()->validarLink();

    $strAcao = MdPetPiEntradaINT::getStr('acao');

    SessaoSEI::getInstance()->validarPermissao($strAcao);

    if ($strAcao !== 'md_pet_pi_orientacao_cadastrar') {
        throw new InfraException("Ação '" . $strAcao . "' não reconhecida.");
    }

    $strTitulo = 'Orientações Gerais do Peticionamento Interno';
    $staTipoEditor = EditorRN::obterTipoEditorSimples();

    $arrComandos = [];
    $arrComandos[] = '<button type="submit" accesskey="s" name="sbmSalvar" id="sbmSalvar" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
    $arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $strAcao)) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

    $objMdPetPiOrientacaoRN = new MdPetPiOrientacaoRN();

    // Conteudo ja gravado
    $objMdPetPiOrientacaoDTO = new MdPetPiOrientacaoDTO();
    $objMdPetPiOrientacaoDTO->setNumIdMdPetPiOrientacao(MdPetPiOrientacaoRN::$ID_FIXO_ORIENTACOES);
    $objMdPetPiOrientacaoDTO->retTodos();
    $objMdPetPiOrientacaoDTO = $objMdPetPiOrientacaoRN->consultar($objMdPetPiOrientacaoDTO);

    $strOrientacoesGerais = $objMdPetPiOrientacaoDTO !== null ? $objMdPetPiOrientacaoDTO->getStrOrientacoesGerais() : '';

    // Reaplica o que veio do formulario para nao perder o preenchimento em caso de erro
    if (MdPetPiEntradaINT::postStr('hdnSubmetido') === '1') {
        $strOrientacoesGerais = MdPetPiEntradaINT::postStr('txaOrientacoesGerais');
    }

    if (MdPetPiEntradaINT::postStr('sbmSalvar') !== '') {

        try {
            $objInfraException = new InfraException();

            if (InfraString::isBolVazia($strOrientacoesGerais)) {
                $objInfraException->adicionarValidacao('Orientações Gerais não informadas.');
            }

            $objInfraException->lancarValidacoes();

            (new EditorRN())->validarTagsCriticas(['jpg', 'png'], $strOrientacoesGerais);

            $objConjuntoEstilosDTO = new ConjuntoEstilosDTO();
            $objConjuntoEstilosDTO->retNumIdConjuntoEstilos();
            $objConjuntoEstilosDTO->setStrSinUltimo('S');
            $objConjuntoEstilosDTO = (new ConjuntoEstilosRN())->consultar($objConjuntoEstilosDTO);

            $objSalvarDTO = new MdPetPiOrientacaoDTO();
            $objSalvarDTO->setStrOrientacoesGerais($strOrientacoesGerais);
            $objSalvarDTO->setNumIdConjuntoEstilos($objConjuntoEstilosDTO !== null ? $objConjuntoEstilosDTO->getNumIdConjuntoEstilos() : null);

            $objMdPetPiOrientacaoRN->salvar($objSalvarDTO);

            PaginaSEI::getInstance()->adicionarMensagem('Operação realizada com sucesso.');

            header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $strAcao));
            die;

        } catch (Exception $e) {
            PaginaSEI::getInstance()->processarExcecao($e);
        }
    }

    $objEditorDTO = new EditorDTO();
    $objEditorDTO->setStrNomeCampo('txaOrientacoesGerais');
    $objEditorDTO->setStrSinSomenteLeitura('N');
    $objEditorDTO->setNumTamanhoEditor(200);
    $objEditorDTO->setStrConteudoInicial($strOrientacoesGerais);

    if ($staTipoEditor == EditorRN::$VE_CK5) {
        EditorCk5RN::montarSimples($objEditorDTO);
    } else {
        $objEditorDTO = (new EditorRN())->montarSimples($objEditorDTO);
    }

} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: ' . PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaSEI::getInstance()->montarStyle();
EditorINT::montarCss();
PaginaSEI::getInstance()->abrirStyle();
require_once 'md_pet_pi_orientacao_cadastro_css.php';
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();

require_once 'md_pet_pi_orientacao_cadastro_editor.php';

PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
<form id="frmMdPetPiOrientacaoCadastro" method="post" onsubmit="return onSubmitForm();"
      action="<?= PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $strAcao . '&acao_origem=' . $strAcao)) ?>">

    <?php
    PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
    PaginaSEI::getInstance()->abrirAreaDados('');
    PaginaSEI::getInstance()->fecharAreaDados();
    ?>

    <div class="row">
        <div class="col-12">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="form-group">
                        <label id="lblOrientacoesGerais" for="txaOrientacoesGerais" class="infraLabelObrigatorio">Orientações Gerais:
                            <img align="top" class="infraImg" name="ajuda"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                 <?= PaginaSEI::montarTitleTooltip('As orientações abaixo serão exibidas na tela de Peticionamento Interno, antes de o Usuário escolher o Tipo de Processo.', 'Ajuda') ?> />
                        </label>
                        <?php if ($staTipoEditor == EditorRN::$VE_CK5) { ?>
                            <div id="divEditores" class="infra-editor" style="visibility: visible;">
                                <?= $objEditorDTO->getStrHtml(); ?>
                            </div>
                        <?php } else { ?>
                            <div id="divEditores" class="mb-0">
                                <textarea id="txaOrientacoesGerais" name="txaOrientacoesGerais" rows="12" class="infraTextarea"
                                          tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"><?= PaginaSEI::tratarHTML($strOrientacoesGerais) ?></textarea>
                                <script type="text/javascript"> <?= $objEditorDTO->getStrEditores(); ?> </script>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="hdnSubmetido" name="hdnSubmetido" value="1"/>

    <?php PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos); ?>

</form>
<?php
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
require_once 'md_pet_pi_orientacao_cadastro_js.php';
