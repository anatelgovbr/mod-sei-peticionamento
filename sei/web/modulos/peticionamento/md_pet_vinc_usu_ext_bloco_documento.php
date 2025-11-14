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
                <label class="d-block my-3">
                    Os documentos devem ser carregados abaixo, sendo de sua exclusiva responsabilidade a conformidade
                    entre os dados informados e os documentos. Os Níveis de Acesso que forem indicados abaixo estarão
                    condicionados à análise por servidor público, que poderá alterá-los a qualquer momento sem
                    necessidade de prévio aviso.
                </label>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div id="divArquivo" class="form-group infraAreaDados mb-4">
                    <label class="infraLabelObrigatorio" for="fileArquivo">
                        Documento (tamanho máximo: <?php echo is_int($tamanhoMaximo) ? $tamanhoMaximo . 'Mb' : $tamanhoMaximo; ?>):
                    </label><br>
                    <input type="file" name="fileArquivo" id="fileArquivo" <?php echo $disabledConsultar ?> tabindex="<?php echo PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-5 col-lg-4 col-xl-3">
                <div class="form-group">
                    <label class="infraLabelObrigatorio" for="selTipoDocumento">Tipo de Documento:
                        <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" name="ajuda" <?php echo PaginaSEI::montarTitleTooltip($strMsgTooltipTipoDocumento, 'Ajuda') ?> alt="Ajuda" class="infraImg"/>
                    </label>
                    <select id="selTipoDocumento" class="infraSelect form-control" <?php echo $disabledConsultar ?> tabindex="<?php echo PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
                        <?= $strSelectTipoDocumento ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-12 col-md-7 col-lg-8 col-xl-9">
                <div class="form-group">
                    <label class="infraLabelObrigatorio" for="txtComplementoTipoDocumento">
                        Complemento do Tipo de Documento:
                        <img src="<?php echo PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" name="ajuda" <?php echo PaginaSEI::montarTitleTooltip($strMsgTooltipComplementoTipoDocumento, 'Ajuda') ?> alt="Ajuda" class="infraImg"/>
                    </label>
                    <input type="text" class="infraText form-control" id="txtComplementoTipoDocumento"
                        <?php echo $disabledConsultar ?>
                        name="txtComplementoTipoDocumento" maxlength="40"
                        onkeypress="return infraMascaraTexto(this,event,40);"
                        tabindex="<?php echo PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-5 col-lg-4 col-xl-3">
                <div class="form-group">
                    <label class="infraLabelObrigatorio" for="selNivelAcesso">
                        Nível de Acesso:
                        <img src="<?php echo PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipNivelAcesso, 'Ajuda') ?> alt="Ajuda" class="infraImg"/>
                    </label>
                    <div id="divNivelAcesso"></div>
                    <?php if (!isset($arrHipoteseNivel['nivelAcesso'])) { ?>
                        <select name="selNivelAcesso" id="selNivelAcesso" class="infraSelect form-control" <?php echo $disabledConsultar ?> tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
                            <?php echo $strSelectNivelAcesso ?>
                        </select>
                    <?php } else { ?>
                        <select name="selNivelAcesso" id="selNivelAcesso" class="infraSelect form-control unclear" <?php echo $disabledConsultar ?> tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>" disabled >
                            <option value="<?= $arrHipoteseNivel['nivelAcesso']['id'] ?>"><?php echo $arrHipoteseNivel['nivelAcesso']['descricao'] ?></option>
                        </select>
                    <?php } ?>
                    <input type="hidden" name="hdnNivelAcesso" id="hdnNivelAcesso" class="hdnNivelAcesso" <?php echo $disabledConsultar ?> value="<?php echo isset($arrHipoteseNivel['nivelAcesso']) ? $arrHipoteseNivel['nivelAcesso']['id'] : '' ?>"/>
                </div>
            </div>

            <?php if ($exibirHipoteseLegal): ?>
                <div class="col-sm-12 col-md-7 col-lg-8 col-xl-9">
                    <div id="divBlcHipoteseLegal" <?= !isset($arrHipoteseNivel['hipoteseLegal']) ? 'style="display: none;"' : '' ?>>
                        <div class="form-group">
                            <label class="infraLabelObrigatorio" for="selHipoteseLegal">Hiptese Legal:
                                <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipHipoteseLegal, 'Ajuda') ?> alt="Ajuda" class="infraImg"/>
                            </label>
                            <?php if (!isset($arrHipoteseNivel['hipoteseLegal'])): ?>
                            <div id="divHipoteseLegal">
                                <?php echo $selHipoteseLegal; ?>
                            </div>
                            <?php else: ?>
                                <select id="selHipoteseLegal" class="infraSelect form-control unclear"  tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>" disabled>
                                    <option value="<?= $arrHipoteseNivel['hipoteseLegal']['id'] ?>"><?php echo $arrHipoteseNivel['hipoteseLegal']['descricao'] ?></option>
                                </select>
                            <?php endif; ?>
                            <input type="hidden" name="hdnHipoteseLegal" id="hdnHipoteseLegal" class="hdnHipoteseLegal" value="<?php echo isset($arrHipoteseNivel['hipoteseLegal']) ? $arrHipoteseNivel['hipoteseLegal']['id'] : '' ?>"/>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-5 col-lg-4 col-xl-3">

                <div class="form-group">
                    <label class="infraLabelObrigatorio">
                        Formato:
                        <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipFormato, 'Ajuda') ?> class="infraImgModulo"/>
                    </label><br />
                    <div class="form-check form-check-inline m-0 p-0 ml-0 mr-1">
                        <input type="radio" class="form-check-input infraRadio" id="rdoNatoDigital" name="rdoFormato" style="position: absolute" value="N" onclick="exibirTipoConferencia();" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">
                        <label for="rdoNatoDigital" id="labelrdoNatoDigital" class="infraLabelRadio mt-1">Nato-Digital</label>
                    </div>
                    <div class="form-check form-check-inline m-0 p-0 ml-0 mr-0">
                        <input type="radio" class="form-check-input infraRadio" id="rdoDigitalizado" name="rdoFormato" style="position: absolute" value="D" onclick="exibirTipoConferencia();" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">
                        <label for="rdoDigitalizado" id="labelrdoDigitalizado" class="infraLabelRadio mt-1">Digitalizado</label>
                    </div>
                </div>

            </div>
            <div class="col-sm-12 col-md-7 col-lg-8 col-xl-9">
                <div class="form-group">
                    <div id="divTipoConferencia" style="display: none;">
                        <label class="infraLabelObrigatorio" for="selTipoConferencia">Conferência com o documento digitalizado:</label><br/>
                        <div class="input-group">
                            <select id="selTipoConferencia" name="selTipoConferencia" class="infraSelect form-control mr-1" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">
                                <?= $strSelectTipoConferencia ?>
                            </select>
                            <div class="input-group-append">
                                <button type="button" accesskey="A" class="infraButton" id="btnAdicionarDocumento" onclick="adicionarDocumento();" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">
                                    <span class="infraTeclaAtalho">A</span>dicionar
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="divTipoConferenciaBotao">
                        <button type="button" accesskey="A" class="infraButton mt-4" id="btnAdicionarDocumento" onclick="adicionarDocumento();" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">
                            <span class="infraTeclaAtalho">A</span>dicionar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <?php $arrArquivo = null; ?>
                <div class="table-responsive">
                    <table class="infraTable" summary="Documento" id="tbDocumento" style="width: 100%">
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
        </div>
        <input type="hidden" name="hdnTbDocumento" value="" id="hdnTbDocumento"/>
        <input type="hidden" name="hdnIdsDocObrigatorios" id="hdnIdsDocObrigatorios"
               value='<?php echo $strDocsObrigatorios; ?>'/>
        <input type="hidden" name="hdnIsAlteracao" id="hdnIsAlteracao" value="<?php echo $stAlterar ? '1' : '0' ?>"/>
    </form>
</fieldset>
<br id="fieldDocumentos_BR" style="display: none">
