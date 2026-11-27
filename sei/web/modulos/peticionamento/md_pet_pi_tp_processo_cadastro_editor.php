<?php
/**
 * ANATEL
 *
 * Saida de CSS e JS dos editores de texto rico (Orientacoes e Orientacoes pos Peticionamento).
 * A saida e global da pagina e atende aos dois editores, por isso sai uma unica vez.
 * Conteudo gerado pelo proprio editor do core, emitido sem escape por construcao.
 *
 * 03/09/2026 - criado por emerson_b.goncalves - ANATEL
 */

if ($staTipoEditor == EditorRN::$VE_CK5) {
    echo $objEditorOrientacoesDTO->getStrCss();
    echo $objEditorOrientacoesDTO->getStrJs();
} else {
    echo $objEditorOrientacoesDTO->getStrInicializacao();
}
