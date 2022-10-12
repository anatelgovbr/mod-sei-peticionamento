<fieldset class="infraFieldset p-3">
    <legend class="infraLegend">Área de Gráficos</legend>
    <div class="row mb-4" id="dv_grafico_scroll">
        <div class="col-md-6 col-lg-6 col-xl-6">
            <label id="lblGrafico" name="lblGrafico" class="infraLabelObrigatorio">Tipo de Gráfico:</label>
            <select onchange="gerarGrafico()" class="form-control" id="selGrafico" name="selGrafico">
                <?php echo $strSelGraficoGeral ?>
            </select>
        </div>
    </div>

    <div class="divAreaTodosGraficos">
        <div class="row">
            <div class="col-12 mb-3">
                <?php if(!is_null($htmlGrafico)){ ?>                
                        <label id="lblGraficoTotalGeral" name="lblGraficoTotalGeral" class="infraLabelObrigatorio">Total Geral:</label>
                        <?php echo $htmlGrafico; ?>                
                <?php } else { ?>                    
                        <label class="infraLabelOpcional"> Nenhum registro encontrado. </label>
                <?php } ?>
            </div>
        </div>

        <?php
        //Se o grafico geral for null os outros serão tbm, por isso não será necessário exibir os dados abaixo.
        if(!is_null($htmlGrafico))
        {
            if(count($arrGraficosTipoIntimacao) > 0)
            {
                $qtdGraficos   = count($arrGraficosTipoIntimacao);
                $tamanhoUltimo = $qtdGraficos % 2 == 0 ? '7' : '13';
        ?>
            <div class="row">
                <?php
                foreach($arrGraficosTipoIntimacao as $key=> $graficoTipoIntimacao)
                {
                    $idLabel = 'lblGraficoTipoIntimacao_' .$key;
                    if(!is_null($graficoTipoIntimacao['html']))
                    {
                        $tamanhoMarginLeft =  MdPetIntRelatorioRN::$GRAFICO_TAMANHO_PADRAO / 3;
                ?>            
                        <div class="col-sm-12 col-md-7 col-lg-6 mb-3">
                            <div class="card" style="height: 100%;">
                                <div class="card-body">
                                    <label style="vertical-align: middle;" id="<?php echo $idLabel; ?>" name="<?php echo $idLabel; ?>" class="infraLabelObrigatorio"><?php echo $graficoTipoIntimacao['label']; ?>:</label>
                                    <?php echo $graficoTipoIntimacao['html']; ?>
                                </div>
                            </div>
                        </div>    
                <?php 
                    }
                } 
                ?>
            </div> 
        <?php 
            }
        } 
        ?>
    </div>
</fieldset>