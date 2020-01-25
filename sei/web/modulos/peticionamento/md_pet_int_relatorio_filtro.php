<!-- DIV TIPO DE INTIMAÇÃO  -->
<div id="divTpIntimacao" name="divTpIntimacao">

        <label id="lblTpIntimacao" name="lblTpIntimacao" for="txtTpIntimacao" class="infraLabelObrigatorio inputSelect">Tipos de Intimação:</label>
        <input type="text" id="txtTpIntimacao" name="txtTpIntimacao" class="infraText inputSelect" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />

        <select size="6" id="selDescricaoTpIntimacao" name="selDescricaoTpIntimacao" multiple="multiple" class="infraText infraAutoCompletar selectPadrao">
            <?= $strItensSelTpIntimacao ?>
        </select>

    <div id="divOpcoesTpIntimacao">
        <img id="imgLupaTpIntimacao" onclick="objLupaTpIntimacao.selecionar(700,500);"
             src="/infra_css/imagens/lupa.gif"
             alt="Selecionar Tipos de Intimação"
             title="Selecionar Tipos de Intimação" class="infraImg"/>
        <br>
        <img id="imgExcluirTpIntimacao" onclick="objLupaTpIntimacao.remover();" src="/infra_css/imagens/remover.gif"
             alt="Remover Tipos de Intimação Selecionados"
             title="Remover Tipos de Intimação Selecionados" class="infraImg"/>
    </div>

    <input type="hidden" id="hdnTpIntimacao" name="hdnTpIntimacao" value="<?=$_POST['hdnTpIntimacao']?>" />
    <input type="hidden" id="hdnIdTpIntimacao" name="hdnIdTpIntimacao" value="<?=$_POST['hdnIdTpIntimacao']?>" />
</div>
<!-- FIM TIPO DE INTIMAÇÃO  -->

<div class="clear altura10"></div>

<!-- DIV UNIDADE -->
<div id="divUnidade" name="divUnidade" class="">

    <label id="lblUnidade" name="lblUnidade" for="txtUnidade" class="inputSelect infraLabelObrigatorio">Unidades:</label>
    <input type="text" id="txtUnidade" name="txtUnidade" class="infraText inputSelect" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />


    <select id="selDescricaoUnidade" name="selDescricaoUnidade" size="6" multiple="multiple"
            class="infraText infraAutoCompletar selectPadrao">
                <?= $strItensSelTpUnidade ?>
    </select>

        <div id="divOpcoesUnidade">
            <img id="imgLupaUnidade" onclick="objLupaUnidade.selecionar(700,500);"
                 src="/infra_css/imagens/lupa.gif"
                 alt="Selecionar Unidades"
                 title="Selecionar Unidades" class="infraImg"/>
            <br>
            <img id="imgExcluirUnidade" onclick="objLupaUnidade.remover();" src="/infra_css/imagens/remover.gif"
                 alt="Remover Unidades Selecionadas"
                 title="Remover Unidades Selecionadas" class="infraImg"/>
        </div>

    <input type="hidden" id="hdnUnidade" name="hdnUnidade" value="<?=$_POST['hdnUnidade']?>" />
    <input type="hidden" id="hdnIdUnidade" name="hdnIdUnidade" value="<?=$_POST['hdnIdUnidade']?>" />

</div>
<!-- FIM DIV UNIDADE -->

<div class="clear altura2"></div>

<!--  DIV PERÍODO DE GERAÇÃO  -->
<div id="divPeriodoGeracao" class="">

    <div id="lblPeriodo" name="lblPeriodo" class="">
        <label id="lblPeriodo" name="lblPeriodo" class="infraLabelObrigatorio">Período de Expedição:</label>
        <br/>
        <!-- Data Inicial -->
        <input class="inputData" style="margin-left: 1%" type="text" name="txtDataInicio" id="txtDataInicio"
               onkeypress="return infraMascaraData(this, event);" maxlength="10"
               tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"
               value="<?php echo array_key_exists('txtDataInicio', $_POST) ? PaginaSEI::tratarHTML($_POST['txtDataInicio']) : '' ?>"/>

        <img class="imgCalendario" src="<?= PaginaSEIExterna::getInstance()->getDiretorioImagensGlobal() ?>/calendario.gif"
             id="imgDataInicio"
             title="Selecionar Data Inicial"
             alt="Selecionar Data Inicial" class="infraImg"
             onclick="infraCalendario('txtDataInicio',this,false,'<?= InfraData::getStrDataAtual() ?>');"/>

        <input class="inputData" style="margin-left: 12%" type="text" id="txtDataFim" name="txtDataFim"
               value="<?php echo array_key_exists('txtDataFim', $_POST) ? PaginaSEI::tratarHTML($_POST['txtDataFim']) : '' ?>"
               onkeypress="return infraMascaraData(this, event);" maxlength="10"
               tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>

        <img style="margin-left: 4px;" class="imgCalendario" src="<?= PaginaSEIExterna::getInstance()->getDiretorioImagensGlobal() ?>/calendario.gif"
             id="imgDataFim"
             title="Selecionar Data Final"
             alt="Selecionar Data Final" class="infraImg"
             onclick="infraCalendario('txtDataFim',this,false,'<?= InfraData::getStrDataAtual() ?>');"/>
    </div>
    

</div>
<!--  FIM PERÍODO DE GERAÇÃO  -->

<div class="clear altura2"></div>

<!--  Tipo Destinatario  -->
<label  id="lblTpDest" name="lblTpDest" style="position: absolute;top: 74%;" class="infraLabelObrigatorio">Tipo de Destinatário</label>
<select id="selTipoDest"  name="selTipoDest" style="position: absolute;top: 78%;"  class="infraSelect" tabindex="504">
<?php if($_POST['selTipoDest'] == ""){ ?>
<option value="" selected>Todos</option>
<option value="N"> Pessoa Física </option>
<option value="S"> Pessoa Jurídica </option>
<?php } ?>
<?php if($_POST['selTipoDest'] == "N"){ ?>
<option value="" selected>Todos</option>
<option value="N" selected> Pessoa Física </option>
<option value="S"> Pessoa Jurídica </option>
<?php } ?>
<?php if($_POST['selTipoDest'] == "S"){ ?>
<option value="" selected>Todos</option>
<option value="N"> Pessoa Física </option>
<option value="S" selected> Pessoa Jurídica </option>
<?php } ?>

</select>
<!--  Tipo Destinatario - FIM  -->

<div id="divSituacao" class="">

    <div class="">
        <label  id="lblSituacao" name="lblSituacao" for="selSituacao" class="infraLabelObrigatorio">Situação da Intimação:</label>
        <select size="6" id="selSituacao" name="selSituacao" class="infraSelect selectPadrao" multiple="" rows="7" tabindex="502">
            <?php echo $strSelSituacao; ?>
        </select>
    </div>


</div>

<input type="hidden" name="hdnIdsSituacao" id="hdnIdsSituacao" value=<?php echo array_key_exists('hdnIdsSituacao', $_POST) ? $_POST['hdnIdsSituacao'] : null ?> >

<!-- Hiddens Gráficos -->
<input type="hidden" name="hdnTipoGrafico1" id="hdnTipoGrafico1" value="<?php echo $strUrlGrafico1 ?>"/>
<input type="hidden" name="hdnTipoGrafico2" id="hdnTipoGrafico2" value="<?php echo $strUrlGrafico2 ?>"/>
<input type="hidden" name="hdnTipoGrafico3" id="hdnTipoGrafico3" value="<?php echo $strUrlGrafico3 ?>"/>
<input type="hidden" name="hdnTipoGrafico4" id="hdnTipoGrafico4" value="<?php echo $strUrlGrafico4 ?>"/>
<input type="hidden" name="hdnTipoGrafico5" id="hdnTipoGrafico5" value="<?php echo $strUrlGrafico5 ?>"/>

<input type="hidden" name="hdnIsGrafico" id="hdnIsGrafico" value="<?php echo $tipoGrafico; ?>"/>
<input type="hidden" name="hdnExcel" id="hdnExcel" value="<?php echo $strUrlExcel; ?>"/>
<input type="hidden" name="hdnIdSitTodas" id="hdnIdSitTodas" value="<?php echo MdPetIntimacaoRN::$TODAS ?>" />