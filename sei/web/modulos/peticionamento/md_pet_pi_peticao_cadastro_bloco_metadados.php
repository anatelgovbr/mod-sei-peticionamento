<?php
/**
 * ANATEL
 *
 * Quadro Metadados do Processo da tela de Peticionamento Interno.
 *
 * 17/09/2026 - criado por emerson_b.goncalves - ANATEL
 */
?>
<div class="row">
    <div class="col-12">
        <fieldset id="fldMetadadosProcesso" class="infraFieldset form-control">
            <legend class="infraLegend">&nbsp;Metadados do Processo&nbsp;</legend>

            <div class="row mb-3">
                <div class="col-12">
                    <div class="form-group">
                        <label id="lblTipoProcesso" class="infraLabelObrigatorio">Tipo de Processo:</label>
                        <label id="lblNomeTipoProcesso" class="infraLabelOpcional"><?= PaginaSEI::tratarHTML($strNomeTipoProcedimento) ?></label>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-sm-12 col-md-8 col-lg-6">
                    <div class="form-group">
                        <label id="lblEspecificacao" for="txtEspecificacao" class="infraLabelObrigatorio">Especificação:</label>
                        <input type="text" id="txtEspecificacao" name="txtEspecificacao" class="infraText form-control" maxlength="100"
                               value="<?= PaginaSEI::tratarHTML($strEspecificacao) ?>"
                               onkeypress="return infraMascaraTexto(this, event, 100);"
                               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-sm-12 col-md-8 col-lg-6">
                    <div class="form-group">
                        <label id="lblTipoPrioridade" for="selTipoPrioridade" class="infraLabelOpcional">Prioridade:</label>
                        <select id="selTipoPrioridade" name="selTipoPrioridade" class="infraSelect form-select"
                                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                            <?= $strItensSelTipoPrioridade ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label id="lblAssuntos" class="infraLabelObrigatorio">Classificação por Assuntos:</label>
                        <div id="divAssuntos">
                            <?php foreach ($arrAssuntos as $strAssunto) { ?>
                                <label class="infraLabelOpcional d-block"><?= PaginaSEI::tratarHTML($strAssunto) ?></label>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

        </fieldset>
    </div>
</div>
