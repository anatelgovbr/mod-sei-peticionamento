<?php
$strSelectTipoDocumento = MdPetIntercorrenteINT::montarSelectTipoDocumento('null', ' ', 'null');
$strSelectNivelAcesso = MdPetTipoProcessoINT::montarSelectNivelAcesso('null', '', 'null', $idTipoProcedimento);
$strSelectTipoConferencia = MdPetIntercorrenteINT::montarSelectTipoConferencia('null', '', 'null');
?>

<fieldset id="fieldDocumentos" class="infraFieldset form-control" style="display: none;">
    <legend class="infraLegend">&nbsp; Documentos &nbsp;</legend>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <label>
                Os documentos devem ser carregados abaixo, sendo de sua exclusiva responsabilidade a conformidade entre
                os dados
                informados e os documentos. Os Níveis de Acesso que forem indicados abaixo estarão condicionados à
                análise por
                servidor público, que poderá alterá-los a qualquer momento sem necessidade de prévio aviso.
            </label>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
            <label class="infraLabelObrigatorio" for="fileArquivo">Documento (tamanho
                máximo: <?= is_int($tamanhoMaximo) ? $tamanhoMaximo . 'Mb' : $tamanhoMaximo; ?>):</label>
            <input type="file" name="fileArquivo" id="fileArquivo"
                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"/>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-4 col-lg-4 col-xl-3">
            <label class="infraLabelObrigatorio" for="selTipoDocumento">Tipo de Documento: <img
                        src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                        name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipTipoDocumento, 'Ajuda') ?>
                        alt="Ajuda"
                        class="infraImgModulo"/></label>
            <select id="selTipoDocumento" class="infraSelect form-control"
                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">
                <?= $strSelectTipoDocumento ?>
            </select>
        </div>
        <div class="col-sm-12 col-md-7 col-lg-6 col-xl-5">
            <label class="infraLabelObrigatorio" for="txtComplementoTipoDocumento">Complemento do Tipo de Documento:
                <img
                        src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                        name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipComplementoTipoDocumento, 'Ajuda') ?>
                        alt="Ajuda" class="infraImgModulo"/></label>
            <input type="text" class="infraText form-control" id="txtComplementoTipoDocumento"
                   name="txtComplementoTipoDocumento" maxlength="40"
                   onkeypress="return infraMascaraTexto(this,event,40);"
                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"/>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-4 col-lg-4 col-xl-3">
            <label class="infraLabelObrigatorio" for="selNivelAcesso">Nível de Acesso: <img
                        src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                        name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipNivelAcesso, 'Ajuda') ?>
                        alt="Ajuda"
                        class="infraImgModulo"/></label>

            <?php if (!isset($arrHipoteseNivel['nivelAcesso'])): ?>
                <select name="selNivelAcesso" id="selNivelAcesso" class="infraSelect form-control"
                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">
                    <?= $strSelectNivelAcesso ?>
                </select>
            <?php else: ?>
                <label class="infraLabelRadio" id="selNivelAcesso">
                    <?= $arrHipoteseNivel['nivelAcesso']['descricao'] ?>
                </label>
            <?php endif; ?>
            <input type="hidden" name="hdnNivelAcesso" id="hdnNivelAcesso"
                   value="<?= isset($arrHipoteseNivel['nivelAcesso']) ? $arrHipoteseNivel['nivelAcesso']['id'] : '' ?>"/>
            <div id="divNivelAcesso"></div>
        </div>
        <?php if ($exibirHipoteseLegal): ?>
            <div class="col-sm-12 col-md-7 col-lg-6 col-xl-5"
                 id="divBlcHipoteseLegal" <?= !isset($arrHipoteseNivel['hipoteseLegal']) ? 'style="display: none;"' : '' ?>>
                <label class="infraLabelObrigatorio" for="selHipoteseLegal">Hipótese Legal: <img
                            src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                            name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipHipoteseLegal, 'Ajuda') ?>
                            alt="Ajuda"
                            class="infraImgModulo"/></label>
                <?php if (!isset($arrHipoteseNivel['hipoteseLegal'])): ?>
                        <?php echo $selHipoteseLegal; ?>
                <?php else: ?>
                    <label class="infraLabelRadio" id="selHipoteseLegal">
                        <?= utf8_decode($arrHipoteseNivel['hipoteseLegal']['descricao']) ?>
                    </label>
                <?php endif; ?>
                <input type="hidden" name="hdnHipoteseLegal" id="hdnHipoteseLegal"
                       value="<?= isset($arrHipoteseNivel['hipoteseLegal']) ? $arrHipoteseNivel['hipoteseLegal']['id'] : '' ?>"/>
            </div>
        <?php endif; ?>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4">
            <label class="infraLabelObrigatorio">Formato: <img
                        src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                        name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipFormato, 'Ajuda') ?>
                        class="infraImgModulo"/></label>
            <br />
            <input type="radio" class="infraRadio" id="rdoNatoDigital" name="rdoFormato"
                   style="margin-left: 5%;" value="N" onclick="exibirTipoConferencia();"
                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">
            <label class="infraRadioLabel" for="rdoNatoDigital"></label>
            <label for="rdoNatoDigital" id="labelrdoNatoDigital" class="infraLabelRadio">Nato-Digital</label>
            <input type="radio" class="infraRadio" id="rdoDigitalizado" name="rdoFormato"
                   value="D" onclick="exibirTipoConferencia();"
                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">
            <label class="infraRadioLabel" for="rdoDigitalizado"></label>
            <label for="rdoDigitalizado" id="labelrdoDigitalizado" class="infraLabelRadio">Digitalizado</label>


        </div>
        <div class="col-sm-12 col-md-8 col-lg-6 col-xl-5" id="divTipoConferencia">
            <label class="infraLabelObrigatorio" for="selTipoConferencia">Conferência com o documento
                digitalizado:</label>
            <select id="selTipoConferencia" name="selTipoConferencia" class="infraSelect form-control"
                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"><?= $strSelectTipoConferencia ?></select>
        </div>
        <div class="col-sm-12 col-md-2 col-lg-2 col-xl-2">
            <button type="button" accesskey="A" class="infraButton" id="btnAdicionarDocumento"
                    onclick="adicionarDocumento();"
                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"><span
                        class="infraTeclaAtalho">A</span>dicionar
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">

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
        </div>
    </div>
</fieldset>