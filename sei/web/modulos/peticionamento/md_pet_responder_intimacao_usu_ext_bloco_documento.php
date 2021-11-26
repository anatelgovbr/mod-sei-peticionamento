<?php
    $strSelectTipoDocumento   = MdPetIntercorrenteINT::montarSelectTipoDocumento('null', ' ', 'null');
    $strSelectNivelAcesso     = MdPetTipoProcessoINT::montarSelectNivelAcesso('null', '', 'null', $idTipoProcedimento);
    $strSelectTipoConferencia = MdPetIntercorrenteINT::montarSelectTipoConferencia('null', '', 'null');
?>

<fieldset id="fieldDocumentos" class="infraFieldset sizeFieldset" style="display: none">
    <legend class="infraLegend">&nbsp; Documentos &nbsp;</legend>
    <div class="bloco">
        <label>
            Os documentos devem ser carregados abaixo, sendo de sua exclusiva responsabilidade a conformidade entre os dados informados e os documentos. Os Níveis de Acesso que forem indicados abaixo estarão condicionados à análise por servidor público, que poderá alterá-los a qualquer momento sem necessidade de prévio aviso.
        </label>
    </div>

    <div class="bloco">
        <label class="infraLabelObrigatorio" for="fileArquivo">Documento (tamanho máximo: <?= is_int($tamanhoMaximo) ? $tamanhoMaximo . 'Mb' : $tamanhoMaximo; ?>):</label>
        <input type="file" name="fileArquivo" id="fileArquivo" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"/>
    </div>

    <div class="clear"></div>

    <div class="bloco">
        <label class="infraLabelObrigatorio" for="selTipoDocumento">Tipo de Documento: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipTipoDocumento) ?> alt="Ajuda" class="infraImg"/></label>
        <select id="selTipoDocumento" class="infraSelect" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">
            <?= $strSelectTipoDocumento ?>
        </select>
    </div>

    <div class="bloco">
        <label class="infraLabelObrigatorio" for="txtComplementoTipoDocumento">Complemento do Tipo de Documento: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipComplementoTipoDocumento) ?> alt="Ajuda" class="infraImg"/></label>

        <input type="text" class="infraText" id="txtComplementoTipoDocumento"
               name="txtComplementoTipoDocumento" maxlength="40"
               onkeypress="return infraMascaraTexto(this,event,40);"
               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"/>
    </div>

    <div class="clear"></div>
    <div class="bloco" style="float: left;" id="divBlcNivelAcesso">
        <label class="infraLabelObrigatorio" for="selNivelAcesso">Nível de Acesso: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipNivelAcesso) ?> alt="Ajuda" class="infraImg"/></label>
        <div id="divNivelAcesso"></div>

    <?php if (!isset($arrHipoteseNivel['nivelAcesso'])): ?>
            <select name="selNivelAcesso" id="selNivelAcesso" class="infraSelect" style="min-width: 120px; max-width: 120px;"  tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">
                <?= $strSelectNivelAcesso ?>
            </select>
        <?php else: ?>
            <label class="infraLabelRadio" id="selNivelAcesso">
                <?= $arrHipoteseNivel['nivelAcesso']['descricao'] ?>
            </label>
    <?php endif; ?>
    </div>

        <input type="hidden" name="hdnNivelAcesso" id="hdnNivelAcesso"
               value="<?= isset($arrHipoteseNivel['nivelAcesso']) ? $arrHipoteseNivel['nivelAcesso']['id'] : '' ?>"/>

    <?php if ($exibirHipoteseLegal): ?>
        <div class="bloco" id="divBlcHipoteseLegal" <?= !isset($arrHipoteseNivel['hipoteseLegal']) ? 'style="float: left; width: 70%; display: none;"' : '' ?>>
            <label class="infraLabelObrigatorio" for="selHipoteseLegal">Hipótese Legal: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipHipoteseLegal) ?> alt="Ajuda" class="infraImg"/></label>
            <?php if (!isset($arrHipoteseNivel['hipoteseLegal'])): ?>
                <div id="divHipoteseLegal">
                <?php echo $selHipoteseLegal; ?>
                </div>
            <?php else: ?>
                <label class="infraLabelRadio" id="selHipoteseLegal">
                    <?= utf8_decode($arrHipoteseNivel['hipoteseLegal']['descricao']) ?>
                </label>

            <?php endif; ?>
            <input type="hidden" name="hdnHipoteseLegal" id="hdnHipoteseLegal"
                   value="<?= isset($arrHipoteseNivel['hipoteseLegal']) ? $arrHipoteseNivel['hipoteseLegal']['id'] : '' ?>"/>
        </div>
    <?php endif; ?>

    <div class="clear"></div>
    <div class="bloco" style="width: 290px; margin: 23px 0 11px 0;">
        <label class="infraLabelObrigatorio">Formato: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipFormato) ?> class="infraImg"/></label>
        <input type="radio" class="infraRadio" id="rdoNatoDigital" name="rdoFormato" style="margin-left: 5%;" value="N" onclick="exibirTipoConferencia();" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">
        <label for="rdoNatoDigital" class="infraLabelRadio">Nato-Digital</label>
        <input type="radio" class="infraRadio" id="rdoDigitalizado" name="rdoFormato" style="margin-left: 5%;" value="D" onclick="exibirTipoConferencia();" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">
        <label for="rdoDigitalizado" class="infraLabelRadio">Digitalizado</label>
    </div>

    <div class="bloco" id="divTipoConferencia">
        <label class="infraLabelObrigatorio" for="selTipoConferencia">Conferência com o documento digitalizado:</label>
        <select id="selTipoConferencia" name="selTipoConferencia" class="infraSelect" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"><?= $strSelectTipoConferencia ?></select>
    </div>
    <div class="bloco">
        <button type="button" accesskey="A" class="infraButton" id="btnAdicionarDocumento" onclick="adicionarDocumento();" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"><span class="infraTeclaAtalho">A</span>dicionar</button>
    </div>

    <table width="99%" class="infraTable" summary="Documento" id="tbDocumento" style="display: none">
        <caption class="infraCaption">&nbsp;</caption>
        <tr>
            <th class="infraTh" width="0" style="display: none;">ID Linha</th> <!--0-->
            <th class="infraTh" width="0" style="display: none;">ID Tipo Documento</th> <!--1-->
            <th class="infraTh" width="0" style="display: none;">Complemento Tipo Documento</th> <!--2-->
            <th class="infraTh" width="0" style="display: none;">ID Nivel Acesso</th> <!--3-->
            <th class="infraTh" width="0" style="display: none;">ID Hipotese Legal</th> <!--4-->
            <th class="infraTh" width="0" style="display: none;">ID Formato</th> <!--5-->
            <th class="infraTh" width="0" style="display: none;">ID Tipo Conferencia</th> <!--6-->
            <th class="infraTh" width="0" style="display: none;">Nome Arquivo Hash</th> <!--7-->
            <th class="infraTh" width="0" style="display: none;">Tamanho Arquivo</th> <!--8-->
            <th class="infraTh" align="center" width="25%">Nome do Arquivo</th> <!--9-->
            <th class="infraTh" align="center" width="15%">Data</th> <!--10-->
            <th class="infraTh" align="center" width="10%">Tamanho</th> <!--11-->
            <th class="infraTh" align="center" width="25%">Documento</th> <!--12-->
            <th class="infraTh" align="center" width="10%">Nível de Acesso</th> <!--13-->
            <th class="infraTh" align="center" width="10%">Formato</th> <!--14-->
            <th class="infraTh" align="center" width="5%">Ações</th> <!--15-->
        </tr>
    </table>

</fieldset>