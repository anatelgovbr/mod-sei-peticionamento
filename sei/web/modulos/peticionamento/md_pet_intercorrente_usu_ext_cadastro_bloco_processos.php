<!-- INICIO FIELDSET PROCESSOS -->

<fieldset id="field_processos" class="infraFieldset sizeFieldset" xmlns="http://www.w3.org/1999/html">
<legend class="infraLegend">&nbsp; Processo &nbsp;</legend>

    <!-- INICIO NUMERO DO PROCESSO -->
    <div class="bloco" style="width: 244px;">
        <label id="lblNumeroSei" for="txtNumeroProcesso" accesskey="n" class="infraLabelObrigatorio"><span class="infraTeclaAtalho">N</span>úmero:</label>
        <input onchange="controlarChangeNumeroProcesso();" type="text" id="txtNumeroProcesso" name="txtNumeroProcesso" class="infraText" maxlength="100" style="width: 182px;" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" value="<?= $txtNumeroProcesso ?>"/>
        <button tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" type="button" accesskey="v" id="btnValidar" onclick="validarNumeroProcesso()" class="infraButton"><span class="infraTeclaAtalho">V</span>alidar</button>
    </div>
    <!-- FIM NUMERO DO PROCESSO -->
    <!-- INICIO TIPO DO PROCESSO VALIDADO -->
    <div class="bloco" style="width: 390px;">
        <label id="lblTipo" for="txtTipo" class="infraLabelObrigatorio">Tipo:</label>
        <input type="text" id="txtTipo" name="txtTipo" class="infraText" readonly="readonly" style="width: 318px;" value="<?= $txtTipo ?>" disabled/>
        <button type="button" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" onclick="adicionarProcesso();" accesskey="a" id="btnAdicionar" class="infraButton" style="display: none"><span class="infraTeclaAtalho">A</span>dicionar</button>
    </div>
    <!-- FIM TIPO DO PROCESSO VALIDADO -->
<div class="clear"></div>
    <!-- INICIO GRID DO PROCESSO ADICIONADO -->
    <div class="bloco" style="width: 100%;">

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
        <input type="hidden" name="hdnIdTipoProcedimento" id="hdnIdTipoProcedimento" value="" disabled tabindex="-1"/>
        <input type="hidden" name="hdnTbProcesso" id="hdnTbProcesso" value="<?php echo $strGridProcesso?>" disabled tabindex="-1"/>
        <input type="hidden" name="hdnProcessoIntercorrente" id="hdnProcessoIntercorrente" value="" disabled tabindex="-1"/>
        <input type="hidden" name="hdnDataAtuacao" id="hdnDataAtuacao" value="" disabled tabindex="-1"/>
        <input type="hidden" name="urlValidaAssinaturaProcesso" id="urlValidaAssinaturaProcesso" value="" disabled tabindex="-1"/>
    </div>

</fieldset>
<!-- FIM FIELDSET PROCESSOS -->