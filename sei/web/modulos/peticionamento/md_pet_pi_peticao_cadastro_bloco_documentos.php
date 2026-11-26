<?php
/**
 * ANATEL
 *
 * Quadros de documentos da tela de Peticionamento Interno: Documento Principal, Anexos Obrigatórios e Mais Anexos.
 *
 * 17/09/2026 - criado por emerson_b.goncalves - ANATEL
 */

$bolDocPrincRestrito = !$bolDocPrincUsuarioIndica && $strNomeHipoteseLegalDocPrinc !== null && $strNomeHipoteseLegalDocPrinc !== '';

// A grade e igual nos tres quadros: a primeira coluna e o identificador da linha, como no "Incluir Documento" do core
$fnMontarGradeDocumentos = function ($strIdTabela) {
    ?>
    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <table id="<?= $strIdTabela ?>" name="<?= $strIdTabela ?>" class="infraTable" style="width: 100%;">
                    <tr>
                        <th class="infraTh" style="display: none;">Identificador</th>

                        <th class="infraTh" style="width: 25%;">Nome do Arquivo</th>
                        <th class="infraTh" style="width: 130px;" align="center">Data</th>
                        <th class="infraTh" style="width: 90px;" align="center">Tamanho</th>
                        <th class="infraTh" style="width: 25%;" align="center">Documento</th>
                        <th class="infraTh" style="width: 120px;" align="center">Nível de Acesso</th>
                        <th class="infraTh" style="width: 120px;" align="center">Formato</th>

                        <!-- Colunas sem exibicao: guardam na grid os valores lidos no servidor na conclusao -->
                        <th class="infraTh" style="display: none;">Tamanho em Bytes</th>
                        <th class="infraTh" style="display: none;">Tipo de Documento</th>
                        <th class="infraTh" style="display: none;">Complemento</th>
                        <th class="infraTh" style="display: none;">Hipótese Legal</th>
                        <th class="infraTh" style="display: none;">Tipo de Conferência</th>
                        <th class="infraTh" style="display: none;">Sinalizador de Formato</th>
                        <th class="infraTh" style="display: none;">Sinalizador de Nível de Acesso</th>

                        <th class="infraTh" style="width: 60px;" align="center">Ações</th>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <?php
};
?>
<div class="row">
    <div class="col-12">
        <fieldset id="fldDocumentoPrincipal" class="infraFieldset form-control">
            <legend class="infraLegend">&nbsp;Documento Principal&nbsp;</legend>

            <form id="frmMdPetPiDocPrincipal" method="post" onsubmit="return false;"
                  action="<?= PaginaSEI::getInstance()->formatarXHTML($strLinkUploadDocPrinc) ?>">

                <?php if ($bolDocPrincExterno) { ?>
                    <div class="row mb-3">
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label id="lblSerieDocPrinc" for="selSerieDocPrinc" class="infraLabelObrigatorio">Tipo de Documento:
                                    <img align="top" class="infraImg" name="ajuda"
                                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                         <?= PaginaSEI::montarTitleTooltip($strMsgTooltipTipoDocPrinc, 'Ajuda') ?> />
                                </label>
                                <select id="selSerieDocPrinc" class="infraSelect form-select" disabled="disabled" tabindex="-1">
                                    <option value=""><?= PaginaSEI::tratarHTML($strNomeDocPrinc) ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label id="lblComplementoDocPrinc" for="txtComplementoDocPrinc" class="infraLabelObrigatorio">Complemento do Tipo de Documento:
                                    <img align="top" class="infraImg" name="ajuda"
                                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                         <?= PaginaSEI::montarTitleTooltip($strMsgTooltipComplementoDocPrinc, 'Ajuda') ?> />
                                </label>
                                <input type="text" id="txtComplementoDocPrinc" name="txtComplementoDocPrinc" class="infraText form-control"
                                       maxlength="50" onkeypress="return infraMascaraTexto(this, event, 50);"
                                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <div class="row mb-3">
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label id="lblNivelAcessoDocPrinc" for="selNivelAcessoDocPrinc" class="infraLabelObrigatorio">Nível de Acesso:
                                <img align="top" class="infraImg" name="ajuda"
                                     src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                     <?= PaginaSEI::montarTitleTooltip($bolDocPrincUsuarioIndica ? $strMsgTooltipNivelAcessoEscolha : $strMsgTooltipNivelAcessoDocPrinc, 'Ajuda') ?> />
                            </label>
                            <?php if ($bolDocPrincUsuarioIndica) { ?>
                                <select id="selNivelAcessoDocPrinc" class="infraSelect form-select"
                                        onchange="exibirHipoteseEscolhida('selNivelAcessoDocPrinc', 'divHipoteseLegalDocPrinc', 'selHipoteseLegalDocPrinc');"
                                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                    <?= $strItensSelNivelAcessoMaisAnexos ?>
                                </select>
                            <?php } else { ?>
                                <select id="selNivelAcessoDocPrinc" class="infraSelect form-select" disabled="disabled" tabindex="-1">
                                    <option value=""><?= PaginaSEI::tratarHTML($strNomeNivelAcessoDocPrinc) ?></option>
                                </select>
                            <?php } ?>
                        </div>
                    </div>
                    <?php if ($bolDocPrincRestrito || ($bolDocPrincUsuarioIndica && $bolHipoteseLegalHabilitada)) { ?>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group" id="divHipoteseLegalDocPrinc" <?= $bolDocPrincUsuarioIndica ? 'style="display: none;"' : '' ?>>
                                <label id="lblHipoteseLegalDocPrinc" for="selHipoteseLegalDocPrinc" class="infraLabelObrigatorio">Hipótese Legal:
                                    <img align="top" class="infraImg" name="ajuda"
                                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                         <?= PaginaSEI::montarTitleTooltip($bolDocPrincUsuarioIndica ? $strMsgTooltipHipoteseLegalEscolha : $strMsgTooltipHipoteseLegalDocPrinc, 'Ajuda') ?> />
                                </label>
                                <?php if ($bolDocPrincUsuarioIndica) { ?>
                                    <select id="selHipoteseLegalDocPrinc" class="infraSelect form-select"
                                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                        <?= $strItensSelHipoteseLegalMaisAnexos ?>
                                    </select>
                                <?php } else { ?>
                                    <select id="selHipoteseLegalDocPrinc" class="infraSelect form-select" disabled="disabled" tabindex="-1">
                                        <option value=""><?= PaginaSEI::tratarHTML($strNomeHipoteseLegalDocPrinc) ?></option>
                                    </select>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <?php if ($bolDocPrincExterno) { ?>
                    <div class="row mb-3">
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label id="lblFormatoDocPrinc" class="infraLabelObrigatorio">Formato:
                                    <img align="top" class="infraImg" name="ajuda"
                                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                         <?= PaginaSEI::montarTitleTooltip($strMsgTooltipFormatoDocPrinc, 'Ajuda') ?> />
                                </label>
                                <div id="divFormatoDocPrinc">
                                    <div class="infraDivRadio">
                                        <input type="radio" name="rdFormatoDocPrinc" id="rdFormatoDocPrincN" value="N" class="infraRadio"
                                               onclick="selecionarFormatoDocumentoPrincipal();"
                                               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                        <label for="rdFormatoDocPrincN" class="infraLabelRadio">Nato-digital</label>
                                    </div>
                                    <div class="infraDivRadio">
                                        <input type="radio" name="rdFormatoDocPrinc" id="rdFormatoDocPrincD" value="D" class="infraRadio"
                                               onclick="selecionarFormatoDocumentoPrincipal();"
                                               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                        <label for="rdFormatoDocPrincD" class="infraLabelRadio">Digitalizado</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div id="divTipoConferenciaDocPrinc" class="form-group" style="display: none;">
                                <label id="lblTipoConferenciaDocPrinc" for="selTipoConferenciaDocPrinc" class="infraLabelObrigatorio">Conferência com o Documento Digitalizado:</label>
                                <select id="selTipoConferenciaDocPrinc" name="selTipoConferenciaDocPrinc" class="infraSelect form-select"
                                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                    <?= $strItensSelTipoConferencia ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <div id="divArquivoDocPrinc" class="form-group">
                                <input type="file" id="filArquivoDocPrinc" name="filArquivoDocPrinc" class="form-control-file drop-zone-style"
                                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                            </div>
                        </div>
                        <div class="col-12">
                            <input type="button" id="btnAdicionarDocPrinc" class="infraButton mt-2" value="Adicionar"
                                   onclick="adicionarDocumentoPrincipal();"
                                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="form-group mb-2">
                                <div class="input-group d-flex align-items-center">
                                    <div class="row">
                                        <div class="col-12">
                                            <label id="lblDocumentoJanela" class="infraLabelObrigatorio d-block pr-2 mb-1">Documento:</label>
                                        </div>
                                        <div class="col-12">
                                            <button type="button" id="lblAbrirDocumentoJanela" class="alert alert-primary d-block p-2 rounded mb-0"
                                                   onclick="abrirJanelaDocumentoPrincipal();"
                                                   <?= PaginaSEI::montarTitleTooltip($strMsgTooltipDocumentoJanela) ?>
                                                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                                <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgLocal() ?>/documento_formulario2.svg" name="formulario"
                                                     alt="Documento"/>
                                                <?= PaginaSEI::tratarHTML($strNomeDocPrinc) ?>
                                                <b>(clique aqui para editar conteúdo)</b>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <input type="button" id="btnAdicionarDocPrinc" class="infraButton mt-2" value="Adicionar"
                                   onclick="adicionarDocumentoPrincipal();"
                                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        </div>
                    </div>
                <?php } ?>

                <?php $fnMontarGradeDocumentos('tblDocPrincipal'); ?>

            </form>
        </fieldset>
    </div>
</div>

<?php if ($bolTemAnexoObrig) { ?>
<div class="row">
    <div class="col-12">
        <fieldset id="fldAnexosObrigatorios" class="infraFieldset form-control">
            <legend class="infraLegend">&nbsp;Anexos Obrigatórios&nbsp;</legend>

            <form id="frmMdPetPiAnexoObrig" method="post" onsubmit="return false;"
                  action="<?= PaginaSEI::getInstance()->formatarXHTML($strLinkUploadAnexo) ?>">

                <div class="row mb-3">
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label id="lblSerieAnexoObrig" for="selSerieAnexoObrig" class="infraLabelObrigatorio">Tipo de Documento:
                                <img align="top" class="infraImg" name="ajuda"
                                     src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                     <?= PaginaSEI::montarTitleTooltip($strMsgTooltipAnexoObrig, 'Ajuda') ?> />
                            </label>
                            <select id="selSerieAnexoObrig" name="selSerieAnexoObrig" class="infraSelect form-select"
                                    onchange="selecionarTipoAnexoObrigatorio();"
                                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                <?= $strItensSelSerieAnexoObrig ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label id="lblComplementoAnexoObrig" for="txtComplementoAnexoObrig" class="infraLabelObrigatorio">Complemento do Tipo de Documento:
                                <img align="top" class="infraImg" name="ajuda"
                                     src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                     <?= PaginaSEI::montarTitleTooltip($strMsgTooltipComplementoDocPrinc, 'Ajuda') ?> />
                            </label>
                            <input type="text" id="txtComplementoAnexoObrig" name="txtComplementoAnexoObrig" class="infraText form-control"
                                   maxlength="50" onkeypress="return infraMascaraTexto(this, event, 50);"
                                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label id="lblNivelAcessoAnexoObrig" for="selNivelAcessoAnexoObrig" class="infraLabelObrigatorio">Nível de Acesso:
                                <img align="top" class="infraImg" name="ajuda"
                                     src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                     <?= PaginaSEI::montarTitleTooltip($strMsgTooltipNivelAcessoAnexoObrig, 'Ajuda') ?> />
                            </label>
                            <select id="selNivelAcessoAnexoObrig" class="infraSelect form-select" disabled="disabled" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"
                                    onchange="exibirHipoteseEscolhida('selNivelAcessoAnexoObrig', 'divHipoteseLegalAnexoObrig', 'selHipoteseLegalAnexoObrig');">
                                <option value="">&nbsp;</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div id="divHipoteseLegalAnexoObrig" class="form-group" style="display: none;">
                            <label id="lblHipoteseLegalAnexoObrig" for="selHipoteseLegalAnexoObrig" class="infraLabelObrigatorio">Hipótese Legal:
                                <img align="top" class="infraImg" name="ajuda"
                                     src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                     <?= PaginaSEI::montarTitleTooltip($strMsgTooltipHipoteseLegalAnexoObrig, 'Ajuda') ?> />
                            </label>
                            <select id="selHipoteseLegalAnexoObrig" class="infraSelect form-select" disabled="disabled" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                <option value="">&nbsp;</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label id="lblFormatoAnexoObrig" class="infraLabelObrigatorio">Formato:
                                <img align="top" class="infraImg" name="ajuda"
                                     src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                     <?= PaginaSEI::montarTitleTooltip($strMsgTooltipFormatoDocPrinc, 'Ajuda') ?> />
                            </label>
                            <div id="divFormatoAnexoObrig">
                                <div class="infraDivRadio">
                                    <input type="radio" name="rdFormatoAnexoObrig" id="rdFormatoAnexoObrigN" value="N" class="infraRadio"
                                           onclick="selecionarFormatoAnexoObrigatorio();"
                                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                    <label for="rdFormatoAnexoObrigN" class="infraLabelRadio">Nato-digital</label>
                                </div>
                                <div class="infraDivRadio">
                                    <input type="radio" name="rdFormatoAnexoObrig" id="rdFormatoAnexoObrigD" value="D" class="infraRadio"
                                           onclick="selecionarFormatoAnexoObrigatorio();"
                                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                    <label for="rdFormatoAnexoObrigD" class="infraLabelRadio">Digitalizado</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div id="divTipoConferenciaAnexoObrig" class="form-group" style="display: none;">
                            <label id="lblTipoConferenciaAnexoObrig" for="selTipoConferenciaAnexoObrig" class="infraLabelObrigatorio">Conferência com o Documento Digitalizado:</label>
                            <select id="selTipoConferenciaAnexoObrig" name="selTipoConferenciaAnexoObrig" class="infraSelect form-select"
                                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                <?= $strItensSelTipoConferencia ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <div id="divArquivoAnexoObrig" class="form-group">
                            <input type="file" id="filArquivoAnexoObrig" name="filArquivoAnexoObrig" class="form-control-file drop-zone-style"
                                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        </div>
                    </div>
                    <div class="col-12">
                        <input type="button" id="btnAdicionarAnexoObrig" class="infraButton mt-2" value="Adicionar"
                               onclick="adicionarAnexoObrigatorio();"
                               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                    </div>
                </div>

                <?php $fnMontarGradeDocumentos('tblAnexoObrig'); ?>

            </form>
        </fieldset>
    </div>
</div>
<?php } ?>

<div class="row">
    <div class="col-12">
        <fieldset id="fldMaisAnexos" class="infraFieldset form-control">
            <legend class="infraLegend">&nbsp;Mais Anexos&nbsp;</legend>

            <form id="frmMdPetPiMaisAnexos" method="post" onsubmit="return false;"
                  action="<?= PaginaSEI::getInstance()->formatarXHTML($strLinkUploadAnexo) ?>">

                <div class="row mb-3">
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label id="lblSerieMaisAnexos" for="selSerieMaisAnexos" class="infraLabelObrigatorio">Tipo de Documento:
                                <img align="top" class="infraImg" name="ajuda"
                                     src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                     <?= PaginaSEI::montarTitleTooltip($strMsgTooltipMaisAnexos, 'Ajuda') ?> />
                            </label>
                            <select id="selSerieMaisAnexos" name="selSerieMaisAnexos" class="infraSelect form-select"
                                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                <?= $strItensSelSerieMaisAnexos ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label id="lblComplementoMaisAnexos" for="txtComplementoMaisAnexos" class="infraLabelObrigatorio">Complemento do Tipo de Documento:
                                <img align="top" class="infraImg" name="ajuda"
                                     src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                     <?= PaginaSEI::montarTitleTooltip($strMsgTooltipComplementoDocPrinc, 'Ajuda') ?> />
                            </label>
                            <input type="text" id="txtComplementoMaisAnexos" name="txtComplementoMaisAnexos" class="infraText form-control"
                                   maxlength="50" onkeypress="return infraMascaraTexto(this, event, 50);"
                                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label id="lblNivelAcessoMaisAnexos" for="selNivelAcessoMaisAnexos" class="infraLabelObrigatorio">Nível de Acesso:
                                <img align="top" class="infraImg" name="ajuda"
                                     src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                     <?= PaginaSEI::montarTitleTooltip($strMsgTooltipNivelAcessoMaisAnexos, 'Ajuda') ?> />
                            </label>
                            <select id="selNivelAcessoMaisAnexos" name="selNivelAcessoMaisAnexos" class="infraSelect form-select"
                                    onchange="selecionarNivelAcessoMaisAnexos();"
                                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                <?= $strItensSelNivelAcessoMaisAnexos ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div id="divHipoteseLegalMaisAnexos" class="form-group" style="display: none;">
                            <label id="lblHipoteseLegalMaisAnexos" for="selHipoteseLegalMaisAnexos" class="infraLabelObrigatorio">Hipótese Legal:
                                <img align="top" class="infraImg" name="ajuda"
                                     src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                     <?= PaginaSEI::montarTitleTooltip($strMsgTooltipHipoteseLegalMaisAnexos, 'Ajuda') ?> />
                            </label>
                            <select id="selHipoteseLegalMaisAnexos" name="selHipoteseLegalMaisAnexos" class="infraSelect form-select"
                                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                <?= $strItensSelHipoteseLegalMaisAnexos ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div class="form-group">
                            <label id="lblFormatoMaisAnexos" class="infraLabelObrigatorio">Formato:
                                <img align="top" class="infraImg" name="ajuda"
                                     src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                     <?= PaginaSEI::montarTitleTooltip($strMsgTooltipFormatoDocPrinc, 'Ajuda') ?> />
                            </label>
                            <div id="divFormatoMaisAnexos">
                                <div class="infraDivRadio">
                                    <input type="radio" name="rdFormatoMaisAnexos" id="rdFormatoMaisAnexosN" value="N" class="infraRadio"
                                           onclick="selecionarFormatoMaisAnexos();"
                                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                    <label for="rdFormatoMaisAnexosN" class="infraLabelRadio">Nato-digital</label>
                                </div>
                                <div class="infraDivRadio">
                                    <input type="radio" name="rdFormatoMaisAnexos" id="rdFormatoMaisAnexosD" value="D" class="infraRadio"
                                           onclick="selecionarFormatoMaisAnexos();"
                                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                    <label for="rdFormatoMaisAnexosD" class="infraLabelRadio">Digitalizado</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6">
                        <div id="divTipoConferenciaMaisAnexos" class="form-group" style="display: none;">
                            <label id="lblTipoConferenciaMaisAnexos" for="selTipoConferenciaMaisAnexos" class="infraLabelObrigatorio">Conferência com o Documento Digitalizado:</label>
                            <select id="selTipoConferenciaMaisAnexos" name="selTipoConferenciaMaisAnexos" class="infraSelect form-select"
                                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                <?= $strItensSelTipoConferencia ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <div id="divArquivoMaisAnexos" class="form-group">
                            <input type="file" id="filArquivoMaisAnexos" name="filArquivoMaisAnexos" class="form-control-file drop-zone-style"
                                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        </div>
                    </div>
                    <div class="col-12">
                        <input type="button" id="btnAdicionarMaisAnexos" class="infraButton mt-2" value="Adicionar"
                               onclick="adicionarMaisAnexos();"
                               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                    </div>
                </div>

                <?php $fnMontarGradeDocumentos('tblMaisAnexos'); ?>

            </form>
        </fieldset>
    </div>
</div>
