<!-- INICIO FIELDSET PROCESSOS -->

<fieldset id="field_processos" class="infraFieldset sizeFieldset form-control" style="height: auto">
    <legend class="infraLegend">&nbsp; Processo &nbsp;</legend>
    <div class="row">
        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
            <label id="lblNumeroSei" for="txtNumeroProcesso" accesskey="n" class="infraLabelObrigatorio"><span
                        class="infraTeclaAtalho">N</span>úmero:</label>
            <div class="input-group mb-3">
                <input onchange="controlarChangeNumeroProcesso();" type="text" id="txtNumeroProcesso"
                       name="txtNumeroProcesso"
                       class="infraText form-control" maxlength="100"
                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"
                       value="<?= PaginaSEI::tratarHTML($txtNumeroProcesso) ?>"/>
                <button tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" type="button" accesskey="v"
                        id="btnValidar" onclick="validarNumeroProcesso()" class="infraButton"><span
                            class="infraTeclaAtalho">V</span>alidar
                </button>
            </div>
        </div>
        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-5">
            <label id="lblTipo" for="txtTipo" class="infraLabelObrigatorio">Tipo:</label>
            <div class="input-group mb-3">
                <input type="text" id="txtTipo" name="txtTipo" class="infraText form-control" readonly="readonly"
                       value="<?= PaginaSEI::tratarHTML($txtTipo) ?>" disabled/>
                <button type="button" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"
                        onclick="adicionarProcesso();" id="btnAdicionar" class="infraButton" style="display: none">
                    Adicionar
                </button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <table width="99%" class="infraTable" summary="Demanda" id="tbProcesso" style="display: none;">
                <tr>
                    <th class="infraTh" style="display: none;">ID do Processo</th>
                    <th class="infraTh" align="center">Processo</th>
                    <th class="infraTh" align="center">Tipo</th>
                    <th class="infraTh" align="center">Peticionamento Intercorrente</th>
                    <th class="infraTh" align="center">Data de Autuação</th>
                    <th class="infraTh" align="center">Ações</th>
                </tr>

            </table>
            <!-- FIM GRID DO PROCESSO ADICIONADO -->
            <input type="hidden" name="hdnIdTipoProcedimento" id="hdnIdTipoProcedimento" value="" disabled
                   tabindex="-1"/>
            <input type="hidden" name="hdnTbProcesso" id="hdnTbProcesso" value="<?php echo $strGridProcesso ?>" disabled
                   tabindex="-1"/>
            <input type="hidden" name="hdnProcessoIntercorrente" id="hdnProcessoIntercorrente" value="" disabled
                   tabindex="-1"/>
            <input type="hidden" name="hdnDataAtuacao" id="hdnDataAtuacao" value="" disabled tabindex="-1"/>
            <input type="hidden" name="urlValidaAssinaturaProcesso" id="urlValidaAssinaturaProcesso" value="" disabled
                   tabindex="-1"/>
        </div>
    </div>
</fieldset>
<!-- FIM FIELDSET PROCESSOS -->