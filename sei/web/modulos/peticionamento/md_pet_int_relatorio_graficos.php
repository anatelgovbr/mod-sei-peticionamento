<div class="grid grid_13 alturaPadrao"></div>


<div class="grid grid_10">

    <label id="lblGrafico" name="lblGrafico" class="infraLabelObrigatorio">Tipo de Gráfico:</label>

    <div class="clear"></div>

    <select onchange="gerarGrafico()" style="width:35%;margin-top:0.4%" id="selGrafico" name="selGrafico">
        <?php echo $strSelGraficoGeral ?>

    </select>

</div>

<div class="clear alturaPadrao"></div>

<div class="divAreaTodosGraficos">
<?php if(!is_null($htmlGrafico)){ ?>
<div style="text-align: center;" class="grid grid_13">
    <label style="vertical-align: middle;" id="lblGraficoTotalGeral" name="lblGraficoTotalGeral" class="infraLabelObrigatorio">Total Geral:</label>
    <?php echo $htmlGrafico; ?>

</div>

<?php }else{ ?>
    <div id="divInfraAreaTabela" class="infraAreaTabela">
        <label>Nenhum registro encontrado.</label>
    </div>
<?php } ?>

<div class="clear alturaPadrao"></div>
<div class="alturaPadrao"></div>

<?php
//Se o grafico geral for null os outros serão tbm, por isso não será necessário exibir os dados abaixo.
if(!is_null($htmlGrafico)){

    if(count($arrGraficosTipoIntimacao) > 0)
    {
        $qtdGraficos   = count($arrGraficosTipoIntimacao);
        $tamanhoUltimo = $qtdGraficos % 2 == 0 ? '7' : '13';
        $contador = 0;

        foreach($arrGraficosTipoIntimacao as $key=> $graficoTipoIntimacao)
        {
            $idLabel = 'lblGraficoTipoIntimacao_' .$key;
            $contador++;
            if(!is_null($graficoTipoIntimacao['html']))
            {
                 $tamanhoMarginLeft =  MdPetIntRelatorioRN::$GRAFICO_TAMANHO_PADRAO / 3;
                ?>


        <div style="text-align: center;" class="grid grid_7">

        <label style="vertical-align: middle;" id="<?php echo $idLabel; ?>" name="<?php echo $idLabel; ?>" class="infraLabelObrigatorio"><?php echo $graficoTipoIntimacao['label']; ?>:</label>

        <?php echo $graficoTipoIntimacao['html']; ?>

        </div>
<?php if($contador % 2 == 0){ ?>
        <div class="clear alturaPadrao"></div>

<?php }}}}
} ?>
</div>