<?php
/**
 * ANATEL
 *
 * Fieldset reutilizavel de bloco de documento do cadastro do Peticionamento Interno.
 * Usado pelos tres blocos: Documento Principal, Anexos de Arquivos Obrigatorios e
 * Documento Adicional Gerado Automatico.
 *
 * Espera definidas: $strBlocoId, $strBlocoLegenda, $bolBlocoComAplicabilidade, $bolBlocoComModelo,
 * $bolBlocoComComportamento, $bolBlocoUnico e $arrBlocoGrid.
 *
 * 08/09/2026 - criado por emerson_b.goncalves - ANATEL
 */
?>
<div class="row mb-3 ml-0">
    <div class="col-sm-12 col-md-12 col-lg-12">
        <fieldset class="infraFieldset">
            <legend class="infraLegend">&nbsp;<?= PaginaSEI::tratarHTML($strBlocoLegenda) ?>&nbsp;
                <img align="top" class="infraImg" name="ajuda"
                     src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                     <?= PaginaSEI::montarTitleTooltip($arrAjudaBlocoDocumento[$strBlocoId]['legenda'], 'Ajuda') ?> />
            </legend>

            <?php if ($bolBlocoComAplicabilidade) { ?>
                <div class="form-group">
                    <?php foreach ($arrDocPrincipal as $strChave => $strDescricao) { ?>
                        <div class="infraDivRadio">
                            <input type="radio" name="rdAplicabilidade" id="rdApl<?= PaginaSEI::tratarHTML($strChave) ?>"
                                   onclick="changeAplicabilidade();" value="<?= PaginaSEI::tratarHTML($strChave) ?>"
                                   class="infraRadio" <?= $strDesabilitar ?>
                                   <?= $strStaAplicabilidadeDocPrinc === $strChave ? 'checked="checked"' : '' ?>
                                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                            <label for="rdApl<?= PaginaSEI::tratarHTML($strChave) ?>" class="infraLabelRadio"><?= PaginaSEI::tratarHTML($strDescricao) ?></label>
                            <img align="top" class="infraImg" name="ajuda"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                 <?= PaginaSEI::montarTitleTooltip($arrAjudaDocPrincipal[$strChave], 'Ajuda') ?> />
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

            <div id="divCampos<?= $strBlocoId ?>" <?= $bolBlocoComAplicabilidade && $strStaAplicabilidadeDocPrinc === '' ? 'style="display: none;"' : '' ?>>
                <?php if (!$bolConsultar) { ?>
                    <div class="row align-items-end">
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label id="lblSerie<?= $strBlocoId ?>" for="selSerie<?= $strBlocoId ?>" class="infraLabelObrigatorio">Tipo de Documento:
                                    <img align="top" class="infraImg" name="ajuda"
                                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                         <?= PaginaSEI::montarTitleTooltip($arrAjudaBlocoDocumento[$strBlocoId]['tipoDocumento'], 'Ajuda') ?> />
                                </label>
                                <select id="selSerie<?= $strBlocoId ?>" name="selSerie<?= $strBlocoId ?>" class="infraSelect form-select"
                                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                </select>
                            </div>
                        </div>
                        <?php if ($bolBlocoComModelo) { ?>
                            <div class="col-sm-12 col-md-6 col-lg-6">
                                <div class="form-group" id="divModelo<?= $strBlocoId ?>">
                                    <label id="lblModelo<?= $strBlocoId ?>" for="txtModelo<?= $strBlocoId ?>" class="infraLabelOpcional">Modelo por Protocolo:
                                        <img align="top" class="infraImg" name="ajuda"
                                             src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                             <?= PaginaSEI::montarTitleTooltip('Número SEI de um Documento Gerado no SEI não assinado e com seu processo disponível em Bloco de Reunião cujo conteúdo possa ser clonado na geração de documento, peticionamento a peticionamento.' . "\n\n" . 'Aplica-se apenas a documentos Gerados e não é obrigatório. Se não for indicado, o conteúdo inicial do documento será o do modelo geral da Administração associado ao Tipo de Documento.', 'Ajuda') ?> />
                                    </label>
                                    <input type="text" id="txtModelo<?= $strBlocoId ?>" name="txtModelo<?= $strBlocoId ?>" class="infraText form-control"
                                           onchange="validarModeloProtocolo('<?= $strBlocoId ?>');"
                                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                    <input type="hidden" id="hdnIdModelo<?= $strBlocoId ?>" name="hdnIdModelo<?= $strBlocoId ?>" value=""/>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <?php if ($bolBlocoComComportamento) { ?>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="form-group">
                                    <label id="lblTipoNivelAcesso<?= $strBlocoId ?>" class="infraLabelObrigatorio">Comportamento do Nível de Acesso:</label>
                                    <?php foreach ($arrTipoNivelAcesso as $strChave => $strDescricao) { ?>
                                        <div class="infraDivRadio">
                                            <input type="radio" name="rdTipoNivelAcesso<?= $strBlocoId ?>" id="rdTna<?= $strBlocoId . PaginaSEI::tratarHTML($strChave) ?>"
                                                   value="<?= PaginaSEI::tratarHTML($strChave) ?>" class="infraRadio"
                                                   <?= $strChave === MdPetPiTpProcessoRN::$TNA_USUARIO ? 'checked="checked"' : '' ?>
                                                   onclick="changeTipoNivelAcesso('<?= $strBlocoId ?>');"
                                                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                            <label for="rdTna<?= $strBlocoId . PaginaSEI::tratarHTML($strChave) ?>" class="infraLabelRadio"><?= PaginaSEI::tratarHTML($strDescricao) ?></label>
                                            <img align="top" class="infraImg" name="ajuda"
                                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                                 <?= PaginaSEI::montarTitleTooltip($arrAjudaTipoNivelAcesso[$strChave], 'Ajuda') ?> />
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="row align-items-end mt-3">
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group" id="divNivelAcesso<?= $strBlocoId ?>" <?= $bolBlocoComComportamento ? 'style="display: none;"' : '' ?>>
                                <label id="lblNivelAcesso<?= $strBlocoId ?>" for="selNivelAcesso<?= $strBlocoId ?>" class="infraLabelObrigatorio">Nível de Acesso:</label>
                                <select id="selNivelAcesso<?= $strBlocoId ?>" name="selNivelAcesso<?= $strBlocoId ?>" class="infraSelect form-select"
                                        onchange="changeNivelAcesso('<?= $strBlocoId ?>');"
                                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                    <?= $strItensSelNivelAcesso ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-5 col-lg-5">
                            <div class="form-group" id="divHipoteseLegal<?= $strBlocoId ?>" style="display: none;">
                                <label id="lblHipoteseLegal<?= $strBlocoId ?>" for="selHipoteseLegal<?= $strBlocoId ?>" class="infraLabelObrigatorio">Hipótese Legal:</label>
                                <select id="selHipoteseLegal<?= $strBlocoId ?>" name="selHipoteseLegal<?= $strBlocoId ?>" class="infraSelect form-select"
                                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                    <?= $strItensSelHipoteseLegal ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-1 col-lg-1">
                            <div class="form-group">
                                <button type="button" id="btnAdicionar<?= $strBlocoId ?>" value="Adicionar" style="margin-bottom: auto;"
                                        onclick="adicionarDocumento('<?= $strBlocoId ?>');" class="infraButton"
                                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">Adicionar
                                </button>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <table width="100%" summary="Tabela de <?= PaginaSEI::tratarHTML($strBlocoLegenda) ?>" class="infraTable" id="tbl<?= $strBlocoId ?>">
                            <caption class="infraCaption"><?= PaginaSEI::tratarHTML($strBlocoLegenda) ?> (<span id="spnQtd<?= $strBlocoId ?>">0</span> registros)</caption>
                            <thead>
                            <tr>
                                <th class="infraTh thLeft" width="18%">Espécie</th>
                                <th class="infraTh thLeft">Tipo de Documento</th>
                                <th class="infraTh thLeft" width="16%">Comportamento de Nível de Acesso</th>
                                <th class="infraTh thLeft" width="22%">Nível de Acesso</th>
                                <th class="infraTh thLeft" width="13%">Modelo por Protocolo</th>
                                <th class="infraTh" width="7%">Ações</th>
                            </tr>
                            </thead>
                            <tbody id="corpoTbl<?= $strBlocoId ?>"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </fieldset>
    </div>
</div>
