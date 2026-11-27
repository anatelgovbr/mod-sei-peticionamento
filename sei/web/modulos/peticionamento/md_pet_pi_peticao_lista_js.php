<?php
/**
 * ANATEL
 *
 * 21/09/2026 - criado por emerson_b.goncalves - ANATEL
 */
?>
function inicializar() {
    document.getElementById('txtProcesso').focus();
    infraEfeitoTabelas();
}

function pesquisar() {
    document.getElementById('frmMdPetPiPeticaoLista').submit();
}
