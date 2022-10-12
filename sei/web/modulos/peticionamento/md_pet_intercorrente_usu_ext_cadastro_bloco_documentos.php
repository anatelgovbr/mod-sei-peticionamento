<?php

/**
 * Fieldset Documentos
 * @since  29/11/2016
 * @author André Luiz <andre.luiz@castgroup.com.br>
 */

//Options dos Selects
$strSelectTipoConferencia = MdPetIntercorrenteINT::montarSelectTipoConferencia('null', '', $_POST['selTipoConferencia']);
//Fim Options

?>
<fieldset id="field_documentos" class="infraFieldset sizeFieldset form-control" style="display: none; height: auto">
    <legend class="infraLegend">&nbsp; Documentos &nbsp;</legend>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <label class="d-block my-3">
                Os documentos devem ser carregados abaixo, sendo de sua exclusiva responsabilidade a conformidade
                entre os dados informados e os documentos. Os Níveis de Acesso que forem indicados abaixo estarão
                condicionados à análise por servidor público, que poderá alterá-los a qualquer momento sem necessidade
                de prévio aviso.
            </label>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-10 infraAreaDados" id="divArquivo">
            <div class="form-group mb-4">
                <label class="infraLabelObrigatorio" for="fileArquivo">
                    Documento (tamanho máximo: <?= is_int($tamanhoMaximo) ? $tamanhoMaximo . 'Mb' : $tamanhoMaximo; ?>):
                </label><br/>
                <input type="file" name="fileArquivo" class="form-control-file" id="fileArquivo" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"/>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-5 col-lg-4 col-xl-3">
            <div class="form-group">
                <label class="infraLabelObrigatorio" for="selTipoDocumento">
                    Tipo de Documento:
                    <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipTipoDocumento, "Ajuda") ?> alt="Ajuda" class="infraImgModulo"/>
                </label><br/>
                <select id="selTipoDocumento" class="infraSelect form-control" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"></select>
            </div>
        </div>
        <div class="col-sm-12 col-md-7 col-lg-8 col-xl-9">
            <div class="form-group">
                <label class="infraLabelObrigatorio" for="txtComplementoTipoDocumento">
                    Complemento do Tipo de Documento:
                    <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipComplementoTipoDocumento, "Ajuda") ?> alt="Ajuda" class="infraImgModulo"/>
                </label><br/>
                <input type="text" name="txtComplementoTipoDocumento" class="infraText form-control" id="txtComplementoTipoDocumento" maxlength="40" onkeypress="return infraMascaraTexto(this,event,40);" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"/>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-5 col-lg-4 col-xl-3" id="divBlcNivelAcesso">
            <div class="form-group">
                <label class="infraLabelObrigatorio" for="selNivelAcesso">
                    Nível de Acesso:
                    <img id=imgNivelAcesso name=imgNivelAcesso src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" name="ajuda" onmouseover="" onmouseout="" alt="Ajuda" class="infraImgModulo"/>
                </label>
                <div id="divNivelAcesso"></div>
            </div>
        </div>
        <?php if ($exibirHipoteseLegal): ?>
            <div class="col-sm-12 col-md-7 col-lg-8 col-xl-9" id="divBlcHipoteseLegal" style="display: none;">
                <div class="form-group">
                    <label class="infraLabelObrigatorio" for="selHipoteseLegal">
                        Hipótese Legal:
                        <img id=imgHipoteseLegal name=imgHipoteseLegal src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" name="ajuda" onmouseover="" onmouseout="" alt="Ajuda" class="infraImgModulo"/>
                    </label>
                    <div id="divHipoteseLegal">
                        <select id="selHipoteseLegal" class="infraSelect form-control" onchange="salvarValorHipoteseLegal(this)"  tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">
                            <?= $selHipoteseLegal; ?>
                        </select>
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
                    <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipFormato, "Ajuda") ?> class="infraImgModulo"/>
                </label><br/>
                <div class="form-check form-check-inline mr-1">
                    <input type="radio" class="form-check-input infraRadio" id="rdoNatoDigital" name="rdoFormato" value="N" style="position: absolute" onclick="exibirTipoConferencia();" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() + 2; ?>">
                    <label class="form-check-label" for="rdoNatoDigital">Nato-digital</label>
                </div>
                <div class="form-check form-check-inline mr-0">
                    <input type="radio" class="form-check-input infraRadio" id="rdoDigitalizado" name="rdoFormato" value="D" style="position: absolute" onclick="exibirTipoConferencia();" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() + 2; ?>">
                    <label class="form-check-label" for="rdoDigitalizado">Digitalizado</label>
                </div>
            </div>

        </div>
        <div class="col-sm-12 col-md-7 col-lg-8 col-xl-9">
            <div class="form-group">
                <div id="divTipoConferencia" style="display: none">
                    <label class="infraLabelObrigatorio" for="selTipoConferencia">
                        Conferência com o documento digitalizado:
                    </label><br/>
                    <div class="input-group">
                        <select id="selTipoConferencia" name="selTipoConferencia" class="infraSelect form-control" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() + 2; ?>">
                            <?= $strSelectTipoConferencia ?>
                        </select>
                        <div class="input-group-append ml-1">
                            <input type="button" class="infraButton" value="Adicionar" onclick="adicionarDocumento()" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() + 2; ?>">
                        </div>
                    </div>
                </div>
                <div id="divTipoConferenciaBotao">
                    <input type="button" class="infraButton mt-3" value="Adicionar" onclick="adicionarDocumento()" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() + 2; ?>">
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <div class="table-responsive">
                <table width="100%" class="infraTable mb-4" id="tbDocumento" style="width:100%">
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
            <input type="hidden" name="hdnTbDocumento" value="" id="hdnTbDocumento" disabled tabindex="-1"/>
            <input type="hidden" value="0" id="hdnIdDocumento" disabled tabindex="-1"/>
            <input type="hidden" name="hdnIdProcedimento" id="hdnIdProcedimento" value="" disabled tabindex="-1"/>
        </div>
    </div>
</fieldset>
