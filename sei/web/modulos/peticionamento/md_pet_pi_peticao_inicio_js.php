<?php
/**
 * ANATEL
 *
 * 17/09/2026 - criado por emerson_b.goncalves - ANATEL
 */
?>
function inicializar() {
    infraEfeitoTabelas();
    seiPrepararFiltroTabela(document.getElementById('tblTipoProcedimento'), document.getElementById('txtFiltro'));

    window.addEventListener('resize', function () {
        redimensionarOrientacoes(document.getElementById('ifrOrientacoesGerais'));
    });
}

function redimensionarOrientacoes(ifr) {
    var doc = ifr.contentDocument ? ifr.contentDocument : ifr.contentWindow.document;

    if (!doc || !doc.body) {
        return;
    }

    ifr.style.height = '10px';
    ifr.style.height = Math.max(doc.body.scrollHeight, doc.documentElement.scrollHeight) + 'px';
}
