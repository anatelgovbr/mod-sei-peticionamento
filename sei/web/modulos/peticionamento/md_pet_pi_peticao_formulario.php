<?php
/**
 * ANATEL
 *
 * Formulario do Documento Principal do Peticionamento Interno, aberto em outra janela.
 * Mesma tela do formulario do Peticionamento Externo (md_pet_formulario.php): Salvar confere os campos pelas regras do
 * core e fecha a janela. Os valores ficam na sessao ate a conclusao do peticionamento.
 *
 * 17/09/2026 - criado por emerson_b.goncalves - ANATEL
 */
try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    PaginaSEI::getInstance()->setBolXHTML(false);
    PaginaSEI::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);

    SessaoSEI::getInstance()->validarLink();

    $strAcao = MdPetPiEntradaINT::getStr('acao');

    SessaoSEI::getInstance()->validarPermissao($strAcao);

    if ($strAcao !== 'md_pet_pi_peticao_formulario_gerar') {
        throw new InfraException("Ação '" . $strAcao . "' não reconhecida.");
    }

    $strTitulo = 'Documento Principal';
    $strHtmlAtributos = '';
    $strJavascriptAtributos = '';
    $bolConteudoSalvo = false;

    // Mesmo nome do botão do core: com ele no POST, o AtributoINT remonta os campos pelo que foi enviado
    $arrComandos = [];
    $arrComandos[] = '<button type="submit" accesskey="s" name="sbmFormularioProcessar" id="sbmFormularioProcessar" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';

    $objMdPetPiDocPrincDTO = MdPetPiPeticaoINT::consultarDocPrincipalDisponivel(
        (int) MdPetPiEntradaINT::getInt('id_md_pet_pi_tp_processo'),
        MdPetPiTpProcessoRN::$DOC_FORMULARIO
    );

    $strTitulo = $objMdPetPiDocPrincDTO->getStrNomeSerie();

    $objSerieDTO = new SerieDTO();
    $objSerieDTO->retNumIdTipoFormulario();
    $objSerieDTO->setNumIdSerie($objMdPetPiDocPrincDTO->getNumIdSerie());
    $objSerieDTO = (new SerieRN())->consultarRN0644($objSerieDTO);

    if ($objSerieDTO === null || $objSerieDTO->getNumIdTipoFormulario() === null) {
        throw new InfraException('Tipo de Documento sem formulario associado.');
    }

    $numIdTipoFormulario = $objSerieDTO->getNumIdTipoFormulario();

    if (MdPetPiEntradaINT::postStr('sbmFormularioProcessar') !== '') {

        try {
            MdPetPiPeticaoINT::salvarFormularioDocPrincipal($objMdPetPiDocPrincDTO->getNumIdMdPetPiTpProcesso(), $numIdTipoFormulario);

            $bolConteudoSalvo = true;

        } catch (Exception $e) {
            PaginaSEI::getInstance()->processarExcecao($e);
        }

    } else {
        MdPetPiPeticaoINT::restaurarFormularioDocPrincipal($objMdPetPiDocPrincDTO->getNumIdMdPetPiTpProcesso());
    }

    // O core monta os campos do formulário e o JavaScript de crítica dos obrigatórios
    AtributoINT::montar(null, $numIdTipoFormulario, $strHtmlAtributos, $strJavascriptAtributos);

} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: ' . PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
require_once 'md_pet_pi_peticao_formulario_css.php';
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
require_once 'md_pet_pi_peticao_formulario_js.php';
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
?>
<body onload="inicializar();">
<form id="frmMdPetPiPeticaoFormulario" method="post" onsubmit="return onSubmitForm();"
      action="<?= PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $strAcao . '&acao_origem=' . $strAcao . '&id_md_pet_pi_tp_processo=' . $objMdPetPiDocPrincDTO->getNumIdMdPetPiTpProcesso())) ?>">
    <br/>
    <?php PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos); ?>

    <div id="divTituloFormulario" class="tituloProcessoDocumento">
        <label id="lblTituloFormulario"><?= PaginaSEI::tratarHTML($strTitulo) ?></label>
    </div>

    <?= $strHtmlAtributos ?>

    <?php PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos); ?>
</form>
<?php
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
