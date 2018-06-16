<fieldset id="field3" class="infraFieldset sizeFieldset">
    <legend class="infraLegend">&nbsp; Resposta &nbsp;</legend>
    <div class="bloco">
        <label>
            Peticionamento: <?= $strTipoProcessoPeticionamento ?>
        </label>
    </div>
    <div class="clear"></div>
    <div class="bloco" style="width: 100%;">
        <label class="infraLabelObrigatorio" for="selTipoResposta">Tipo de Resposta: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipTipoResposta) ?> alt="Ajuda" class="infraImg"/></label>
        <select class="infraSelect" style="min-width: 55%;" name="selTipoResposta" id="selTipoResposta" onchange="exibirFieldsetDocumentos(this)" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">
            <?= $strSelectTipoResposta ?>
        </select>
    </div>
</fieldset>
<br/>