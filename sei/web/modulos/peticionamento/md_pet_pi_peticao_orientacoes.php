<?php
/**
 * ANATEL
 *
 * Conteúdo de orientações exibido em iframe nas telas do usuário, com o conjunto de estilos do editor.
 *
 * 17/09/2026 - criado por emerson_b.goncalves - ANATEL
 */

PaginaSEI::getInstance()->abrirStyle();
echo (new EditorRN())->montarCssEditor($numIdConjuntoEstilosOrientacoes);
PaginaSEI::getInstance()->fecharStyle();

$strEsquemaCores = PaginaSEI::getInstance()->getStrEsquemaCores();
if (in_array($strEsquemaCores, [InfraPaginaEsquema3::$ESQUEMA_PRETO, InfraPaginaEsquema3::$ESQUEMA_CINZA_ESCURO], true)) {
    echo EditorINT::getCssEsquemaCores();
    PaginaSEI::getInstance()->abrirStyle();
    ?>
    html, body, body * {
        background-color: transparent !important;
    }
    body, body * {
        color: var(--infra-esquema-cor-texto) !important;
    }
    body a, body a * {
        color: <?= $strEsquemaCores === InfraPaginaEsquema3::$ESQUEMA_PRETO ? '#ffff00' : '#e0c456' ?> !important;
    }
    <?php
    PaginaSEI::getInstance()->fecharStyle();
}

echo $strOrientacoesHtml;
