<?php
/**
 * ANATEL
 *
 * 17/09/2026 - criado por emerson_b.goncalves - ANATEL
 */
?>
function inicializar() {
    infraEfeitoTabelas();

<?php if ($bolConteudoSalvo) { ?>
    if (window.opener && typeof window.opener.receberConteudoDocumentoPrincipal === 'function') {
        window.opener.receberConteudoDocumentoPrincipal();
    }

    window.close();
<?php } ?>
}

function onSubmitForm() {
    <?= $strJavascriptAtributos ?>

    return true;
}
