<?php
    $col_pesq  = 'col-sm-9 col-md-9 col-lg-7 col-xl-7';
    $col_combo = 'col-md-12 col-lg-10 col-xl-10';
?>
<!-- DIV TIPO DE INTIMAÇÃO  -->
<div class="row">
    <div class="<?= $col_pesq ?>">
        <label id="lblTpIntimacao" name="lblTpIntimacao" for="txtTpIntimacao" class="infraLabelObrigatorio inputSelect">
            Tipos de Intimação:
        </label>
        <input type="text" id="txtTpIntimacao" name="txtTpIntimacao" class="infraText form-control"
               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
    </div>
</div>
<div class="row">
    <div class="<?= $col_combo ?>">
        <div class="form-group">
            <div class="input-group mb-3">
                <select size="6" id="selDescricaoTpIntimacao" name="selDescricaoTpIntimacao" multiple="multiple"
                        class="infraSelect form-control mr-1" tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
                    <?= $strItensSelTpIntimacao ?>
                </select>
                <div id="divOpcoesTpIntimacao">
                    <img id="imgLupaTpIntimacao" onclick="objLupaTpIntimacao.selecionar(700,500);"
                        src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal(); ?>/pesquisar.svg?<?= Icone::VERSAO ?>"
                        alt="Selecionar Tipos de Intimação"
                        title="Selecionar Tipos de Intimação" class="infraImg"/>
                    <br>
                    <img id="imgExcluirTpIntimacao" onclick="objLupaTpIntimacao.remover();"
                        src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal(); ?>/remover.svg?<?= Icone::VERSAO ?>"
                        alt="Remover Tipos de Intimação Selecionados"
                        title="Remover Tipos de Intimação Selecionados" class="infraImg"/>
                </div>
                <input type="hidden" id="hdnTpIntimacao" name="hdnTpIntimacao" value="<?= $_POST['hdnTpIntimacao'] ?>"/>
                <input type="hidden" id="hdnIdTpIntimacao" name="hdnIdTpIntimacao" value="<?= $_POST['hdnIdTpIntimacao'] ?>"/>
            </div>
        </div>
    </div>
</div>
<!-- FIM TIPO DE INTIMAÇÃO  -->


<!-- DIV UNIDADE -->
<div class="row">
    <div class="<?= $col_pesq ?>">
        <label id="lblUnidade" name="lblUnidade" for="txtUnidade" class="inputSelect infraLabelObrigatorio"> Unidades: </label>
        <input type="text" id="txtUnidade" name="txtUnidade" class="infraText form-control"
               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
    </div>
</div>
<div class="row">
    <div class="<?= $col_combo ?>">
        <div class="form-group">
            <div class="input-group mb-3">
                <select id="selDescricaoUnidade" name="selDescricaoUnidade" size="6" multiple="multiple"
                        class="infraSelect form-control mr-1" tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
                    <?= $strItensSelTpUnidade ?>
                </select>

                <div id="divOpcoesUnidade">
                    <img id="imgLupaUnidade" onclick="objLupaUnidade.selecionar(700,500);"
                        src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal(); ?>/pesquisar.svg?<?= Icone::VERSAO ?>"
                        alt="Selecionar Unidades"
                        title="Selecionar Unidades" class="infraImg"/>
                    <br>
                    <img id="imgExcluirUnidade" onclick="objLupaUnidade.remover();"
                        src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal(); ?>/remover.svg?<?= Icone::VERSAO ?>"
                        alt="Remover Unidades Selecionadas"
                        title="Remover Unidades Selecionadas" class="infraImg"/>
                </div>

                <input type="hidden" id="hdnUnidade" name="hdnUnidade" value="<?= $_POST['hdnUnidade'] ?>"/>
                <input type="hidden" id="hdnIdUnidade" name="hdnIdUnidade" value="<?= $_POST['hdnIdUnidade'] ?>"/>
            </div>
        </div>
    </div>
</div>
<!-- FIM DIV UNIDADE -->
<!--  DIV PERÍODO DE GERAÇÃO  -->
<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-10">
        <div class="form-group">
            <label id="lblPeriodo" name="lblPeriodo" class="infraLabelObrigatorio">Período de Expedição:</label>
            <div class="input-group input-group-sm mb-6">
                <div class="input-group-prepend">
                    <span class="input-group-text">De</span>
                </div>
                <input class="infraText form-control" type="text" name="txtDataInicio" id="txtDataInicio"
                    onkeypress="return infraMascaraData(this, event);" maxlength="10"
                    tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"
                    value="<?php echo array_key_exists('txtDataInicio', $_POST) ? PaginaSEI::tratarHTML($_POST['txtDataInicio']) : '' ?>"/>

                <img class="imgCalendario"
                    src="<?= PaginaSEIExterna::getInstance()->getDiretorioSvgGlobal() ?>/calendario.svg?<?= Icone::VERSAO ?>"
                    id="imgDataInicio"
                    title="Selecionar Data Inicial"
                    alt="Selecionar Data Inicial" class="infraImg"
                    onclick="infraCalendario('txtDataInicio',this,false,'<?= InfraData::getStrDataAtual() ?>');"/>

                <div class="input-group-prepend ml-1">
                    <span class="input-group-text">Até</span>
                </div>

                <input class="infraText form-control" type="text" id="txtDataFim" name="txtDataFim"
                    value="<?php echo array_key_exists('txtDataFim', $_POST) ? PaginaSEI::tratarHTML($_POST['txtDataFim']) : '' ?>"
                    onkeypress="return infraMascaraData(this, event);" maxlength="10"
                    tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>

                <img class="imgCalendario"
                    src="<?= PaginaSEIExterna::getInstance()->getDiretorioSvgGlobal() ?>/calendario.svg?<?= Icone::VERSAO ?>"
                    id="imgDataFim"
                    title="Selecionar Data Final"
                    alt="Selecionar Data Final" class="infraImg"
                    onclick="infraCalendario('txtDataFim',this,false,'<?= InfraData::getStrDataAtual() ?>');"/>
            </div>
        </div>
    </div>
</div>
<!--  FIM PERÍODO DE GERAÇÃO  -->
<!--  Tipo Destinatario  -->
<div class="row">
    <div class="col-md-6 col-lg-5 col-xl-5">
        <div class="form-group">
            <label id="lblTpDest" name="lblTpDest" class="infraLabelObrigatorio"> Tipo de Destinatário: </label>
            <select id="selTipoDest" name="selTipoDest"
                    class="infraSelect form-control"
                    tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
                <?php if ($_POST['selTipoDest'] == "") { ?>
                    <option value="" selected>Todos</option>
                    <option value="N"> Pessoa Física</option>
                    <option value="S"> Pessoa Jurídica</option>
                <?php } ?>
                <?php if ($_POST['selTipoDest'] == "N") { ?>
                    <option value="" selected>Todos</option>
                    <option value="N" selected> Pessoa Física</option>
                    <option value="S"> Pessoa Jurídica</option>
                <?php } ?>
                <?php if ($_POST['selTipoDest'] == "S") { ?>
                    <option value="" selected>Todos</option>
                    <option value="N"> Pessoa Física</option>
                    <option value="S" selected> Pessoa Jurídica</option>
                <?php } ?>

            </select>
        </div>
    </div>
</div>
<!--  Tipo Destinatario - FIM  -->

<!-- DIV UNIDADE -->
<div class="row">
    <div class="<?= $col_pesq ?>">
        <label id="lblDestinatario" name="lblDestinatario" for="txtDestinatario" class="inputSelect infraLabelObrigatorio"> Destinatários: </label>
        <input type="text" id="txtDestinatario" name="txtDestinatario" class="infraText form-control"
               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
    </div>
</div>
<div class="row">
    <div class="<?= $col_combo ?>">
        <div class="form-group">
            <div class="input-group mb-3">
                <select id="selDestinatario" name="selDestinatario" size="6" multiple="multiple"
                        class="infraSelect form-control mr-1" tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
                </select>

                <div id="divOpcoesDestinatario">
                    <img id="imgLupaDestinatario" onclick="objLupaDestinatarios.selecionar(700,500);"
                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal(); ?>/pesquisar.svg?<?= Icone::VERSAO ?>"
                         alt="Selecionar Destinatários"
                         title="Selecionar Destinatários" class="infraImg"/>
                    <br>
                    <img id="imgExcluirDestinatario" onclick="objLupaDestinatarios.remover();"
                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal(); ?>/remover.svg?<?= Icone::VERSAO ?>"
                         alt="Remover Destinatários Selecionados"
                         title="Remover Destinatários Selecionados" class="infraImg"/>
                </div>

                <input type="hidden" id="hdnDestinatario" name="hdnDestinatario" value="<?= $_POST['hdnDestinatario'] ?>"/>
                <input type="hidden" id="hdnIdDestinatario" name="hdnIdDestinatario" value="<?= $_POST['hdnIdDestinatario'] ?>"/>
            </div>
        </div>
    </div>
</div>
<!-- FIM DIV DESTINATARIO -->

<div class="row">
    <div class="<?= $col_combo ?>">
        <div class="form-group">
            <label id="lblSituacao" name="lblSituacao" for="selSituacao" class="infraLabelObrigatorio">
                Situação da Intimação:
            </label>
            <select size="6" id="selSituacao" name="selSituacao" class="infraSelect form-control" multiple="" rows="7"
                    tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
                <?php echo $strSelSituacao; ?>
            </select>
        </div>
    </div>
</div>

<input type="hidden" name="hdnIdsSituacao" id="hdnIdsSituacao"
       value=<?php echo array_key_exists('hdnIdsSituacao', $_POST) ? $_POST['hdnIdsSituacao'] : null ?>>

<!-- Hiddens Gráficos -->
<input type="hidden" name="hdnTipoGrafico1" id="hdnTipoGrafico1" value="<?php echo $strUrlGrafico1 ?>"/>
<input type="hidden" name="hdnTipoGrafico2" id="hdnTipoGrafico2" value="<?php echo $strUrlGrafico2 ?>"/>
<input type="hidden" name="hdnTipoGrafico3" id="hdnTipoGrafico3" value="<?php echo $strUrlGrafico3 ?>"/>
<input type="hidden" name="hdnTipoGrafico4" id="hdnTipoGrafico4" value="<?php echo $strUrlGrafico4 ?>"/>
<input type="hidden" name="hdnTipoGrafico5" id="hdnTipoGrafico5" value="<?php echo $strUrlGrafico5 ?>"/>

<input type="hidden" name="hdnIsGrafico" id="hdnIsGrafico" value="<?php echo $tipoGrafico; ?>"/>
<input type="hidden" name="hdnExcel" id="hdnExcel" value="<?php echo $strUrlExcel; ?>"/>
<input type="hidden" name="hdnIdSitTodas" id="hdnIdSitTodas" value="<?php echo MdPetIntimacaoRN::$TODAS ?>"/>
