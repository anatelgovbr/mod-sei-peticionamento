<?php
/**
 * ANATEL
 *
 * 17/09/2026 - criado por emerson_b.goncalves - ANATEL
 */
?>
<?php if ($staTipoEditor === EditorRN::$VE_CK5) { ?>
body {
    overflow: hidden;
    margin: 0;
    padding: 0;
}

div.infraAreaDebug {
    overflow: auto;
    display: table;
    white-space: pre-wrap;
    font-size: 1em;
    width: 100%;
}
<?php } else { ?>
body {
    overflow: hidden;
}

#divComandos {
    margin: 0;
}

#divEditores {
    overflow: auto;
    border-top: 2px solid;
    border-bottom: 0;
}

#txaConteudo {
    height: 300px;
}

.cke_contents#cke_1_contents {
    height: 300px;
}
<?php } ?>
