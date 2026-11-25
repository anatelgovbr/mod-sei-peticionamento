<?php
/**
 * ANATEL
 *
 * 17/09/2026 - criado por emerson_b.goncalves - ANATEL
 */
?>
<?php if ($staTipoEditor === EditorRN::$VE_CK5) { ?>
// O Salvar da barra do editor grava na sessao; a tela de origem e avisada quando o salvamento termina
function aguardarEditor() {
    var objEditor = window.infraEditor && window.infraEditor.isReady ? window.infraEditor.editor : null;

    if (!objEditor) {
        setTimeout(aguardarEditor, 200);
        return;
    }

    objEditor.on('salvar', function (evt) {
        Promise.resolve(evt.return).then(function (bolSalvou) {
            if (bolSalvou === true && window.opener && typeof window.opener.receberConteudoDocumentoPrincipal === 'function') {
                window.opener.receberConteudoDocumentoPrincipal();
            }
        });
    }, {priority: 'low'});
}

aguardarEditor();
<?php } else { ?>
function inicializar() {
    infraAdicionarEvento(window, 'resize', redimensionar);
    CKEDITOR.on('instanceReady', redimensionar);
    redimensionar();
<?php if ($bolConteudoSalvo) { ?>

    if (window.opener && typeof window.opener.receberConteudoDocumentoPrincipal === 'function') {
        window.opener.receberConteudoDocumentoPrincipal();
    }
<?php } ?>
}

// A barra do editor fica no topo e o texto ocupa o resto da janela, como no editor do Peticionamento Externo
function redimensionar() {
    setTimeout(function () {
        var numAlturaComandos = document.getElementById('divComandos').offsetHeight;
        var divEditores = document.getElementById('divEditores');

        if (numAlturaComandos > divEditores.offsetHeight) {
            numAlturaComandos -= divEditores.offsetHeight;
        }

        var numAlturaEditor = infraClientHeight() - numAlturaComandos - 20;

        divEditores.style.height = (numAlturaEditor > 0 ? numAlturaEditor : 1) + 'px';
    }, 0);
}
<?php } ?>
