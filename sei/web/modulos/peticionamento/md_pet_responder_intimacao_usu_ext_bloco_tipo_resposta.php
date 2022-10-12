<fieldset id="field3" class="infraFieldset sizeFieldset form-control" style="height:auto;">
    <legend class="infraLegend">&nbsp; Resposta &nbsp;</legend>
    <div class="row">
        <div class="col-12">
            <label class="d-block my-2">
                Peticionamento: <?= $strTipoProcessoPeticionamento ?>
            </label>
        </div>
    </div>
    <?php if ($contador == 2) { ?>
        <div class="row">
            <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                <div class="form-group">
                    <label class="infraLabelObrigatorio" for="selRazaoSocial">Responder em nome de:
                        <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>"
                            name="ajuda" class="infraImgModulo"
                        <?= PaginaSEI::montarTitleTooltip("Esta Intimação possui como destinatários você e/ou as Pessoas Jurídicas listadas neste campo. \n \n Selecione seu registro para responder a Intimação em seu nome próprio ou a Pessoa Jurídica que representa para responder em nome da mesma.", 'Ajuda') ?>>
                    </label>
                    <select class="infraSelect form-control"
                            onchange="mudarSelect(this);exibirFieldsetDocumentos(this);alterarHidden(this);"
                            name="selRazaoSocial" id="selRazaoSocial"
                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>" autofocus>
                        <?= $strSelectEmpresa ?>
                    </select>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php if ($contador == 2) { ?>
        <div class="row">
            <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                <div class="form-group">
                    <label class="infraLabelObrigatorio" for="selTipoResposta">Tipo de Resposta: <img
                                src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>"
                                name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipTipoResposta, 'Ajuda') ?>
                                alt="Ajuda" class="infraImgModulo"/></label>
                    <select class="infraSelect form-control" name="selTipoResposta" id="selTipoResposta"
                            onchange="exibirFieldsetDocumentos(this)"
                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>" autofocus>
                    </select>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php if ($contador != 2) { ?>
        <div class="row">
            <div class="col-sm-12 col-md-10 col-lg-8 col-xl-6">
                <div class="form-group">
                    <label class="infraLabelObrigatorio" for="selTipoResposta">Tipo de Resposta: <img
                                src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>"
                                name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipTipoResposta, 'Ajuda') ?>
                                alt="Ajuda" class="infraImgModulo"/></label>
                    <select class="infraSelect form-control" name="selTipoResposta" id="selTipoResposta"
                            onchange="exibirFieldsetDocumentos(this)"
                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>" autofocus>
                        <?= $strSelectTipoResposta ?>
                    </select>
                </div>
            </div>
        </div>
    <?php } ?>
</fieldset>
<br/>
<script>
    document.getElementById('selectTipoResp').style.display = 'none';
    //document.getElementById('resp').style.display = 'none';
</script>