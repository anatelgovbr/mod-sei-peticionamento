<?php
/**
 * ANATEL
 *
 * 17/09/2026 - criado por emerson_b.goncalves - ANATEL
 */
?>
#fldOrientacoesGerais {
    height: auto;
    min-height: 120px;
    margin-bottom: 11px;
}

#ifrOrientacoesGerais {
    width: 100%;
    height: 80px;
}

#lblEscolhaTipoProcesso {
    display: block;
    font-size: 1.6em;
    margin-top: 20px;
    margin-bottom: 15px;
}

#txtFiltro {
    width: 100%;
    margin-bottom: 10px;
}

/* Tela curta nao gera barra de rolagem; reservar o espaco dela mantem a mesma margem direita das telas longas */
#divInfraAreaTelaD {
    scrollbar-gutter: stable;
}

<?php if (in_array(PaginaSEI::getInstance()->getStrEsquemaCores(), [InfraPaginaEsquema3::$ESQUEMA_PRETO, InfraPaginaEsquema3::$ESQUEMA_CINZA_ESCURO], true)) { ?>
#divInfraTooltip .infraTooltipTexto * {
    color: inherit !important;
    background-color: transparent !important;
}
<?php } elseif (PaginaSEI::getInstance()->getStrEsquemaCores() === InfraPaginaEsquema3::$ESQUEMA_VERDE_MAR) { ?>
#divInfraTooltip .infraTooltipTitulo {
    background-color: var(--infra-esquema-cor-escura);
}
<?php } ?>
