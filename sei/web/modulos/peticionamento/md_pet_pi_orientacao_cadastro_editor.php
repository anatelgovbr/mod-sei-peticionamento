<?php
/**
 * ANATEL
 *
 * Saida de CSS e JS do editor de texto rico das Orientacoes Gerais.
 * Conteudo gerado pelo proprio editor do core, emitido sem escape por construcao.
 *
 * 03/09/2026 - criado por emerson_b.goncalves - ANATEL
 */

if ($staTipoEditor == EditorRN::$VE_CK5) {
    echo $objEditorDTO->getStrCss();
    echo $objEditorDTO->getStrJs();
} else {
    echo $objEditorDTO->getStrInicializacao();
}
