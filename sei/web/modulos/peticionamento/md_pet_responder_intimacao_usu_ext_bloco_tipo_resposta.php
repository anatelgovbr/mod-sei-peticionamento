<fieldset id="field3" class="infraFieldset sizeFieldset form-control" style="min-height: 180px">
    <legend class="infraLegend">&nbsp; Resposta &nbsp;</legend>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <label>Peticionamento: <?= $strTipoProcessoPeticionamento ?></label>
        </div>
    </div>
    <?php if ($contador == 2) { ?>
        <div class="row">
            <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                <label class="infraLabelObrigatorio" for="selRazaoSocial">Responder em nome de:
                    <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                         name="ajuda" class="infraImgModulo"
                    <?= PaginaSEI::montarTitleTooltip("Esta Intimação possui como destinatários você e/ou as Pessoas Jurídicas listadas neste campo. \n \n Selecione seu registro para responder a Intimação em seu nome próprio ou a Pessoa Jurídica que representa para responder em nome da mesma.", 'Ajuda') ?>
                </label>
                <select class="infraSelect form-control"
                        onchange="mudarSelect(this);exibirFieldsetDocumentos(this);alterarHidden(this);"
                        name="selRazaoSocial" id="selRazaoSocial"
                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">
                    <?= $strSelectEmpresa ?>
                </select>
            </div>
        </div>
    <?php } ?>
    <?php if ($contador == 2) { ?>
        <div class="row">
            <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                <label class="infraLabelObrigatorio" for="selTipoResposta">Tipo de Resposta: <img
                            src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                            name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipTipoResposta, 'Ajuda') ?>
                            alt="Ajuda" class="infraImgModulo"/></label>
                <select class="infraSelect form-control" name="selTipoResposta" id="selTipoResposta"
                        onchange="exibirFieldsetDocumentos(this)"
                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">
                </select>
            </div>
        </div>
    <?php } ?>
    <?php if ($contador != 2) { ?>
        <div class="row">
            <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                <label class="infraLabelObrigatorio" for="selTipoResposta">Tipo de Resposta: <img
                            src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                            name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipTipoResposta, 'Ajuda') ?>
                            alt="Ajuda" class="infraImgModulo"/></label>
                <select class="infraSelect form-control" name="selTipoResposta" id="selTipoResposta"
                        onchange="exibirFieldsetDocumentos(this)"
                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">
                    <?= $strSelectTipoResposta ?>
                </select>
            </div>
        </div>
    <?php } ?>
</fieldset>
<br/>
<script>
    document.getElementById('selectTipoResp').style.display = 'none';
    //document.getElementById('resp').style.display = 'none';
</script>