<?php
$strSelectTipoDocumento = MdPetVinculoINT::montarSelectTipoDocumento('null', ' ', 'null');
$strSelectNivelAcesso = MdPetTipoProcessoINT::montarSelectNivelAcesso('null', '', 'null', $idTipoProcesso);
$strSelectTipoConferencia = MdPetIntercorrenteINT::montarSelectTipoConferencia('null', '', 'null');
$selHipoteseLegal = MdPetVinculoINT::montarSelectHipoteseLegal();

$disabledConsultar = $stConsultar ? 'disabled="disabled"' : null;
?>
<fieldset id="fieldDocumentos"
          class="infraFieldset form-control sizeFieldset" <?php echo !$stAlterar ? 'style="display: none; width: auto;"' : 'style="width: auto;"' ?>>
    <legend class="infraLegend">&nbsp; <?php echo $stAlterar ? 'Atualização de ' : null ?>Atos Constitutivos&nbsp;
    </legend>
    <form method="post" id="frmDocumentoAto" enctype="multipart/form-data" action="<?= $strLinkUploadArquivo ?>">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <label>Os documentos devem ser carregados abaixo, sendo de sua exclusiva responsabilidade a conformidade
                    entre os dados informados e os documentos. Os Níveis de Acesso que forem indicados abaixo estarão
                    condicionados à análise por servidor público, que poderá alterá-los a qualquer momento sem
                    necessidade de prévio aviso.</label>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <label class="infraLabelObrigatorio" for="fileArquivo">Documento (tamanho
                    máximo: <?php echo is_int($tamanhoMaximo) ? $tamanhoMaximo . 'Mb' : $tamanhoMaximo; ?>):</label>
                <input type="file" name="fileArquivo" id="fileArquivo"
                    <?php echo $disabledConsultar ?>
                       tabindex="<?php echo PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-5 col-lg-3 col-xl-2">
                <label class="infraLabelObrigatorio" for="selTipoDocumento">Tipo de Documento:
                    <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                         name="ajuda" <?php echo PaginaSEI::montarTitleTooltip($strMsgTooltipTipoDocumento, 'Ajuda') ?>
                         alt="Ajuda"
                         class="infraImg"/>
                </label>
                <select id="selTipoDocumento" class="infraSelect"
                    <?php echo $disabledConsultar ?>
                        tabindex="<?php echo PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
                    <?= $strSelectTipoDocumento ?>
                </select>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-5 col-xl-3">
                <label class="infraLabelObrigatorio" for="txtComplementoTipoDocumento">Complemento do Tipo de Documento:
                    <img src="<?php echo PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                         name="ajuda" <?php echo PaginaSEI::montarTitleTooltip($strMsgTooltipComplementoTipoDocumento, 'Ajuda') ?>
                         alt="Ajuda"
                         class="infraImg"/>
                </label>
                <input type="text" class="infraText form-control" id="txtComplementoTipoDocumento"
                    <?php echo $disabledConsultar ?>
                       name="txtComplementoTipoDocumento" maxlength="40"
                       onkeypress="return infraMascaraTexto(this,event,40);"
                       tabindex="<?php echo PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-4 col-lg-3 col-xl-2">
                <label class="infraLabelObrigatorio" for="selNivelAcesso">Nível de Acesso:
                    <img src="<?php echo PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                         name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipNivelAcesso, 'Ajuda') ?> alt="Ajuda"
                         class="infraImg"/></label>
                <div id="divNivelAcesso"></div>

                <?php if (!isset($arrHipoteseNivel['nivelAcesso'])) { ?>
                    <select name="selNivelAcesso" id="selNivelAcesso" class="infraSelect"
                        <?php echo $disabledConsultar ?>
                            tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
                        <?php echo $strSelectNivelAcesso ?>
                    </select>
                <?php } else { ?>
                    <label class="infraLabelRadio" id="selNivelAcesso">
                        <?php echo $arrHipoteseNivel['nivelAcesso']['descricao'] ?>
                    </label>
                <?php } ?>
                <input type="hidden" name="hdnNivelAcesso" id="hdnNivelAcesso" class="hdnNivelAcesso"
                    <?php echo $disabledConsultar ?>
                       value="<?php echo isset($arrHipoteseNivel['nivelAcesso']) ? $arrHipoteseNivel['nivelAcesso']['id'] : '' ?>"/>
            </div>
            <div class="col-sm-12 col-md-5 col-lg-4 col-xl-2">
                <?php if ($exibirHipoteseLegal): ?>
                    <div class="bloco"
                         id="divBlcHipoteseLegal" <?= !isset($arrHipoteseNivel['hipoteseLegal']) ? 'style="display: none;"' : '' ?>>
                        <label class="infraLabelObrigatorio" for="selHipoteseLegal">Hipótese Legal:
                            <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                 name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipHipoteseLegal, 'Ajuda') ?>
                                 alt="Ajuda"
                                 class="infraImg"/></label>
                        <?php if (!isset($arrHipoteseNivel['hipoteseLegal'])): ?>
                            <div id="divHipoteseLegal">
                                <?php echo $selHipoteseLegal; ?>
                            </div>
                        <?php else: ?>
                            <label class="infraLabelRadio" id="selHipoteseLegal">
                                <?php echo $arrHipoteseNivel['hipoteseLegal']['descricao'] ?>
                            </label>

                        <?php endif; ?>
                        <input type="hidden" name="hdnHipoteseLegal" id="hdnHipoteseLegal" class="hdnHipoteseLegal"
                               value="<?php echo isset($arrHipoteseNivel['hipoteseLegal']) ? $arrHipoteseNivel['hipoteseLegal']['id'] : '' ?>"/>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-6 col-lg-3 col-xl-3">
                <label class="infraLabelObrigatorio">Formato:
                    <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                         name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipFormato) ?>
                         class="infraImg"/></label><br />
                <input type="radio" class="infraRadio" id="rdoNatoDigital" name="rdoFormato" style="margin-left: 5%;"
                    <?php echo $disabledConsultar ?>
                       value="N" onclick="exibirTipoConferencia();"
                       tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>
                <label for="rdoNatoDigital" id="labelrdoNatoDigital" class="infraLabelRadio">Nato-Digital</label>
                <input type="radio" class="infraRadio" id="rdoDigitalizado" name="rdoFormato" style="margin-left: 5%;"
                    <?php echo $disabledConsultar ?>
                       value="D" onclick="exibirTipoConferencia();"
                       tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>
                <label for="rdoDigitalizado" id="labelrdoDigitalizado" class="infraLabelRadio">Digitalizado</label>
            </div>
            <div class="col-sm-12 col-md-3 col-lg-3">
                <div id="divTipoConferencia" style="display: none; ">
                    <label class="infraLabelObrigatorio" for="selTipoConferencia">Conferência com o documento
                        digitalizado:</label>
                    <select id="selTipoConferencia" name="selTipoConferencia" class="infraSelect"
                        <?php echo $disabledConsultar ?>
                            tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"><?= $strSelectTipoConferencia ?></select>
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-12">
                <button type="button" class="infraButton" id="btnAdicionarDocumento"
                    <?php
                    echo $disabledConsultar;
                    if ($disabledConsultar == null) {
                        echo ' accesskey="A"';
                    }
                    ?> onclick="adicionarDocumento();"
                        tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"><span
                            class="infraTeclaAtalho">A</span>dicionar
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <?php $arrArquivo = null; ?>
                <table width="99%" class="infraTable" summary="Documento" id="tbDocumento">
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
                    <?php

                    if (!is_null($arrArquivo)) {
                        ?>
                        <?php foreach ($arrArquivo as $chave => $arquivo) { ?>
                            <tr class="infraTrClara">
                                <td class="infraTd" style="display: none;">
                                    <div><?php echo $chave + 1 ?></div>
                                </td>
                                <td class="infraTd" style="display: none;">
                                    <div><?php echo $arquivo->getNumIdSerie(); ?></div>
                                </td>
                                <td class="infraTd" style="display: none;">
                                    <div><?php echo $arquivo->getStrNumeroDocumento(); ?></div>
                                </td>
                                <td class="infraTd" style="display: none;">
                                    <div><?php echo $arquivo->getStrStaNivelAcesso(); ?></div>
                                </td>
                                <td class="infraTd" style="display: none;">
                                    <div><?php echo $arquivo->getNumIdHipoteseLegal(); ?></div>
                                </td>
                                <td class="infraTd" style="display: none;">
                                    <div></div>
                                </td>
                                <td class="infraTd" style="display: none;">
                                    <div><?php echo $arquivo->getNumIdTipoConferencia(); ?></div>
                                </td>
                                <td class="infraTd">
                                    <div style="text-align:center;"><?php echo $arquivo->getStrNomeArquivoAnexo(); ?></div>
                                </td>
                                <td class="infraTd">
                                    <div style="text-align:center;"><?php echo $arquivo->getDthDataArquivoAnexo(); ?></div>
                                </td>
                                <td class="infraTd">
                                    <div style="text-align:center;"><?php echo $arquivo->getNumTamanhoArquivoAnexo() / 1000; ?>
                                        Kb
                                    </div>
                                </td>
                                <td class="infraTd">
                                    <div style="text-align:center;"><?php echo $arquivo->getStrNomeSerieProtocolo() . ' ' . $arquivo->getStrNumeroDocumento(); ?></div>
                                </td>
                                <td class="infraTd">
                                    <div style="text-align:center;"><?php echo $arrDescricaoNivelAcesso[$arquivo->getStrStaNivelAcesso()]; ?></div>
                                </td>
                                <td class="infraTd">
                                    <div style="text-align:center;"><?php echo is_null($arquivo->getNumIdTipoConferencia()) ? 'Nato-Digital' : 'Digitalizado'; ?></div>
                                </td>
                                <td align="center" valign="center"></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </table>
            </div>
        </div>
        <input type="hidden" name="hdnTbDocumento" value="" id="hdnTbDocumento"/>
        <input type="hidden" name="hdnIdsDocObrigatorios" id="hdnIdsDocObrigatorios"
               value='<?php echo $strDocsObrigatorios; ?>'/>
        <input type="hidden" name="hdnIsAlteracao" id="hdnIsAlteracao" value="<?php echo $stAlterar ? '1' : '0' ?>"/>
    </form>
</fieldset>
<br id="fieldDocumentos_BR" style="display: none">