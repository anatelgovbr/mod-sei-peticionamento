<?php
/**
 * ANATEL
 *
 * Tela de Peticionamento Interno do Tipo de Processo escolhido.
 *
 * 17/09/2026 - criado por emerson_b.goncalves - ANATEL
 */
try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    SessaoSEI::getInstance()->validarLink();

    $strAcao = MdPetPiEntradaINT::getStr('acao');

    SessaoSEI::getInstance()->validarPermissao($strAcao);

    if ($strAcao !== 'md_pet_pi_peticao_cadastrar') {
        throw new InfraException("Ação '" . $strAcao . "' não reconhecida.");
    }

    $mixTextoXss = PaginaSEI::POST('mdPetXssTexto');
    if ($mixTextoXss !== null) {
        echo PaginaSEI::tratarHTML(PeticionamentoIntegracao::consultarXssTextoAjax($mixTextoXss));
        return;
    }

    require_once 'md_pet_pi_peticao_cadastro_inicializacao.php';

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
require_once 'md_pet_pi_peticao_cadastro_css.php';
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
require_once 'js/md_pet_global_js.php';
PaginaSEI::getInstance()->abrirJavaScript();
require_once 'md_pet_pi_peticao_cadastro_js.php';
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');

PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaSEI::getInstance()->abrirAreaDados('auto');
?>

<section id="fldOrientacoesTipoProcesso" aria-labelledby="ttlOrientacoesTipoProcesso">
    <div class="md-pet-orientacoes-cabecalho">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8zM14 2v6h6M8 12h8M8 16h6"/>
        </svg>
        <h2 id="ttlOrientacoesTipoProcesso">Orientações para este Tipo de Processo</h2>
    </div>
    <iframe id="ifrOrientacoesTipoProcesso" name="ifrOrientacoesTipoProcesso" title="Orientações para este Tipo de Processo" frameborder="0" marginheight="0" marginwidth="0"
            onload="redimensionarOrientacoes(this);"
            src="<?= PaginaSEI::getInstance()->formatarXHTML($strLinkOrientacoes) ?>"></iframe>
</section>

<section id="divTipoInteressado" class="card md-pet-interessado" aria-labelledby="ttlTipoInteressado nomeTipoInteressado">
    <div class="card-header md-pet-interessado-cabecalho">
        <span class="md-pet-interessado-icone" aria-hidden="true">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" focusable="false">
                <?php if ($strStaIndicacaoInteressado === MdPetPiTpProcessoRN::$II_INTERESSE_PESSOAL) { ?>
                    <circle cx="12" cy="7" r="4"/>
                    <path d="M4 21v-2a8 8 0 0 1 16 0v2"/>
                <?php } else { ?>
                    <path d="M4 21V5l8-3 8 3v16M2 21h20M10 21v-5h4v5M8 7h1m6 0h1M8 11h1m6 0h1"/>
                <?php } ?>
            </svg>
        </span>
        <div>
            <h2 id="ttlTipoInteressado" class="md-pet-interessado-titulo">Tipo de Interessado</h2>
            <p id="nomeTipoInteressado" class="md-pet-interessado-nome"><?= PaginaSEI::tratarHTML($strNomeIndicacaoInteressado) ?></p>
        </div>
    </div>
    <div class="card-body md-pet-interessado-regras">
        <?php foreach (explode("\n\n", $strMsgTooltipInteressado) as $numParagrafo => $strParagrafo) { ?>
            <div class="md-pet-interessado-contexto">
                <h3 class="md-pet-interessado-subtitulo"><?= $numParagrafo === 0 ? 'Na Unidade de geração' : 'Nas demais Unidades' ?></h3>
                <p><?= PaginaSEI::tratarHTML($strParagrafo) ?></p>
            </div>
        <?php } ?>
    </div>
</section>

<form id="frmMdPetPiPeticaoCadastro" method="post" onsubmit="return false;"
      action="<?= PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $strAcao . '&acao_origem=' . $strAcao . '&id_md_pet_pi_tp_processo=' . $numIdMdPetPiTpProcesso)) ?>">

    <?php require_once 'md_pet_pi_peticao_cadastro_bloco_metadados.php'; ?>

    <!-- As listas de documentos ficam neste form, o unico enviado na conclusao; o envio de arquivo usa form proprio -->
    <input type="hidden" id="hdnDocPrincipal" name="hdnDocPrincipal" value="<?= PaginaSEI::tratarHTML($strGridDocPrincipal) ?>"/>
    <input type="hidden" id="hdnConteudoDocPrincipal" name="hdnConteudoDocPrincipal" value="<?= $strConteudoDocPrincPreenchido ?>"/>
    <input type="hidden" id="hdnAnexoObrig" name="hdnAnexoObrig" value="<?= PaginaSEI::tratarHTML($strGridAnexoObrig) ?>"/>
    <input type="hidden" id="hdnMaisAnexos" name="hdnMaisAnexos" value="<?= PaginaSEI::tratarHTML($strGridMaisAnexos) ?>"/>
    <input type="hidden" id="hdnConfirmado" name="hdnConfirmado" value=""/>
    <input type="hidden" id="hdnCargoFuncaoAssinatura" name="hdnCargoFuncaoAssinatura" value=""/>
    <!-- Nome pwdSenha: a auditoria do SEI (AuditoriaSEI::getArrExcecoesPost) tira este campo do POST gravado -->
    <input type="hidden" id="pwdSenha" name="pwdSenha" value=""/>
</form>

<?php
require_once 'md_pet_pi_peticao_cadastro_bloco_documentos.php';

PaginaSEI::getInstance()->fecharAreaDados();
PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
