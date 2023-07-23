<fieldset class="infraFieldset p-3">
    <legend class="infraLegend">Área de Gráficos</legend>
    <div class="row mb-4" id="dv_grafico_scroll">
        <div class="col-md-6 col-lg-6 col-xl-6">
            <label id="lblGrafico" name="lblGrafico" class="infraLabelObrigatorio">Tipo de Gráfico:</label>
            <select onchange="gerarGrafico()" class="form-control" id="selGrafico" name="selGrafico">
                <?php echo $strSelGraficoGeral ?>
            </select>
        </div>
        <div class="col-md-6 col-lg-6 col-xl-6">
            <div class="form-group pt-2">
                <div class="form-check pt-4">
                    <input class="form-check-input" type="checkbox" name="ocultarTiposIntVazios" value="1" id="ocultarTiposIntVazios" checked="checked">
                    <label class="form-check-label" for="ocultarTiposIntVazios">
                        Ocultar Tipos de Intimação sem registro
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="divAreaTodosGraficos">
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card card-body shadow-sm mb-3" style="min-height: 400px">
                    <h6 class="font-weight-bold">Total Geral:</h6>
                    <div class="carregarGraficoAjax" data-idtipointimacao="0"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <?php if(($tipoGrafico) != 0 && count($arrTiposIntimacao) > 0): ?>
                <?php foreach($arrTiposIntimacao as $key => $value): ?>
                    <div class="col-6 pb-4">
                        <div class="card card-body shadow-sm mb-3" style="height: 100%">
                            <h6 class="font-weight-bold"><?= $value ?>:</h6>
                            <div class="carregarGraficoAjax" data-idtipointimacao="<?= $key ?>"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</fieldset>