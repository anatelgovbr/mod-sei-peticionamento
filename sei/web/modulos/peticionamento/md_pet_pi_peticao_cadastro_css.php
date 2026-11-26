<?php
/**
 * ANATEL
 *
 * 17/09/2026 - criado por emerson_b.goncalves - ANATEL
 */
?>
#fldOrientacoesTipoProcesso {
    max-width: 64rem;
    margin: 8px auto 24px;
    padding: 4px 24px 8px;
    border-left: 4px solid var(--infra-esquema-cor-escura, #315b70);
}

.md-pet-orientacoes-cabecalho {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 0 8px 12px;
    color: var(--infra-esquema-cor-escura, #315b70);
}

.md-pet-orientacoes-cabecalho svg {
    flex-shrink: 0;
}

#ttlOrientacoesTipoProcesso {
    margin: 0;
    color: #243746;
    font-size: 1.25rem;
    font-weight: 600;
    line-height: 1.4;
}

#ifrOrientacoesTipoProcesso {
    display: block;
    width: 100%;
    height: 80px;
    border: 0;
}

@media (max-width: 575.98px) {
    #fldOrientacoesTipoProcesso {
        padding: 4px 12px 8px;
    }

    #ttlOrientacoesTipoProcesso {
        font-size: 1.0625rem;
    }
}

#fldMetadadosProcesso,
#fldDocumentoPrincipal,
#fldAnexosObrigatorios,
#fldMaisAnexos {
    height: auto;
    padding-top: 15px;
    padding-bottom: 15px;
    margin-bottom: 15px;
}

#divAssuntos {
    padding-left: 12px;
}

#divFormatoDocPrinc .infraDivRadio,
#divFormatoAnexoObrig .infraDivRadio,
#divFormatoMaisAnexos .infraDivRadio {
    display: inline-block;
    margin-right: 2rem;
}

/* Campo de arquivo, Adicionar e caixa de edicao iguais aos do Peticionamento Externo (md_pet_usu_ext_cadastro_css.php) */
#divArquivoDocPrinc,
#divArquivoAnexoObrig,
#divArquivoMaisAnexos {
    height: 95px;
}

.drop-zone-style {
    width: 100%;
    border: 2px dashed #ccc;
    border-radius: 5px;
    padding: 18px;
    text-align: left;
    font-family: sans-serif;
    color: #555;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: start;
    justify-content: start;
    min-height: 40px;
    transition: all 0.1s ease-in-out;
}

.drop-zone-style:hover {
    background: #eee;
}

.drop-zone-style::file-selector-button {
    background: #FFF;
    color: #555;
    border: 1px solid #666;
    padding: 4px 10px;
    border-radius: 4px;
    cursor: pointer;
}

.drop-zone-style::file-selector-button:hover {
    background: #0494C7;
    color: white;
}

#fldDocumentoPrincipal input[type=button],
#fldAnexosObrigatorios input[type=button],
#fldMaisAnexos input[type=button] {
    margin-left: 5px;
}

#lblAbrirDocumentoJanela {
    cursor: pointer;
}

#lblAbrirDocumentoJanela img {
    vertical-align: middle;
}

/* Tela curta nao gera barra de rolagem; reservar o espaco dela mantem a mesma margem direita das telas longas */
#divInfraAreaTelaD {
    scrollbar-gutter: stable;
}

/* O infraUpload posiciona a barra de progresso por position absolute a partir do offsetTop do campo de arquivo;
   sem um ancestral posicionado ela cai muito abaixo do quadro e estica a pagina */
#fldDocumentoPrincipal .form-group,
#fldAnexosObrigatorios .form-group,
#fldMaisAnexos .form-group {
    position: relative;
}

/* Informacao de acesso sempre visivel, entre orientacoes e metadados. */
.md-pet-interessado {
    max-width: 64rem;
    margin: 8px auto 24px;
    overflow: hidden;
    color: #243746;
    background: #fff;
    border: 1px solid #d5dde2;
    border-top: 4px solid var(--infra-esquema-cor-escura, #315b70);
    border-radius: 8px;
}

.md-pet-interessado .md-pet-interessado-cabecalho {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px 24px;
    background: #f4f7f9;
    border-bottom: 1px solid #d5dde2;
}

.md-pet-interessado-icone {
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 56px;
    height: 56px;
    color: var(--infra-esquema-cor-escura, #315b70);
    background: #fff;
    border: 1px solid #d5dde2;
    border-radius: 12px;
}

.md-pet-interessado-titulo {
    margin: 0 0 4px;
    color: #52636d;
    font-size: .875rem;
    font-weight: 500;
    line-height: 1.4;
}

.md-pet-interessado-nome {
    margin: 0;
    color: #243746;
    font-size: 1.375rem;
    font-weight: 600;
    line-height: 1.3;
}

.md-pet-interessado .md-pet-interessado-regras {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(100%, 24rem), 1fr));
    gap: 24px;
    padding: 20px 24px 24px;
}

.md-pet-interessado-contexto {
    border-left: 3px solid #d5dde2;
    padding-left: 16px;
}

.md-pet-interessado-subtitulo {
    margin: 0 0 8px;
    color: #243746;
    font-size: .9375rem;
    font-weight: 700;
    line-height: 1.4;
}

.md-pet-interessado-contexto p {
    max-width: 80ch;
    margin: 0;
    font-size: .9375rem;
    line-height: 1.6;
    overflow-wrap: anywhere;
}

@media (max-width: 575.98px) {
    .md-pet-interessado .md-pet-interessado-cabecalho,
    .md-pet-interessado .md-pet-interessado-regras {
        padding: 16px;
    }

    .md-pet-interessado-nome {
        font-size: 1.125rem;
    }
}

<?php if (in_array(PaginaSEI::getInstance()->getStrEsquemaCores(), [InfraPaginaEsquema3::$ESQUEMA_PRETO, InfraPaginaEsquema3::$ESQUEMA_CINZA_ESCURO], true)) { ?>
#fldOrientacoesTipoProcesso {
    border-left-color: var(--infra-esquema-cor-texto);
}

.md-pet-orientacoes-cabecalho,
#ttlOrientacoesTipoProcesso,
.md-pet-interessado,
.md-pet-interessado-icone,
.md-pet-interessado-titulo,
.md-pet-interessado-nome,
.md-pet-interessado-subtitulo,
.md-pet-interessado-contexto p,
.drop-zone-style {
    color: var(--infra-esquema-cor-texto);
}

.md-pet-interessado,
.md-pet-interessado .md-pet-interessado-cabecalho,
.md-pet-interessado-icone,
.drop-zone-style:hover {
    background: transparent;
}

.md-pet-interessado {
    border-top-color: var(--infra-esquema-cor-texto);
}

#divInfraTooltip .infraTooltipTexto * {
    color: inherit !important;
    background-color: transparent !important;
}
<?php } elseif (PaginaSEI::getInstance()->getStrEsquemaCores() === InfraPaginaEsquema3::$ESQUEMA_VERDE_MAR) { ?>
#divInfraTooltip .infraTooltipTitulo {
    background-color: var(--infra-esquema-cor-escura);
}
<?php } ?>
