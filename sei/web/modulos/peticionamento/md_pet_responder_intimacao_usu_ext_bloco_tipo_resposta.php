<fieldset id="field3" class="infraFieldset sizeFieldset">
    <legend class="infraLegend">&nbsp; Resposta &nbsp;</legend>
    <div class="bloco">
    <label>Peticionamento: <?= $strTipoProcessoPeticionamento ?></label>
    </div>
    <?php if($contador == 2){ ?>
    <div class="bloco" style="width: 100%;">
        <label class="infraLabelObrigatorio" for="selRazaoSocial">Responder em nome de: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip("Esta Intimação possui como destinatários você e/ou as Pessoas Jurídicas listadas neste campo. \n \n Selecione seu registro para responder a Intimação em seu nome próprio ou a Pessoa Jurídica que representa para responder em nome da mesma.") ?></label>
        <select class="infraSelect" style="min-width: 55%;" onchange="mudarSelect(this);exibirFieldsetDocumentos(this);alterarHidden(this);" name="selRazaoSocial" id="selRazaoSocial" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">
            <?= $strSelectEmpresa ?>
        </select>
    </div>
    <?php } ?>
   
    <div class="clear"></div>
    <?php if($contador == 2){ ?>
    <div id="selectTipoResp">

        <div class="bloco" id="resp" style="width: 100%;">
        <label class="infraLabelObrigatorio" for="selTipoResposta">Tipo de Resposta: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipTipoResposta) ?> alt="Ajuda" class="infraImg"/></label>
        <select class="infraSelect" style="min-width: 55%;" name="selTipoResposta" id="selTipoResposta" onchange="exibirFieldsetDocumentos(this)" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">
        </select>
		</div>
    <?php } ?>
    <?php if($contador != 2){ ?>
	<div class="bloco" style="width: 100%;">
        <label class="infraLabelObrigatorio" for="selTipoResposta">Tipo de Resposta: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipTipoResposta) ?> alt="Ajuda" class="infraImg"/></label>
        <select class="infraSelect" style="min-width: 55%;" name="selTipoResposta" id="selTipoResposta" onchange="exibirFieldsetDocumentos(this)" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">
            <?= $strSelectTipoResposta ?>
        </select>
    </div>
    
    <?php } ?>

</fieldset>
<br/>
<script>
document.getElementById('selectTipoResp').style.display = 'none';
//document.getElementById('resp').style.display = 'none';
</script>