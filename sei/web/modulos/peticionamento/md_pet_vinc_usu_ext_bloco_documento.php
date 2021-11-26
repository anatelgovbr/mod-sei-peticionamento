<?php
$strSelectTipoDocumento = MdPetVinculoINT::montarSelectTipoDocumento('null', ' ', 'null');
$strSelectNivelAcesso = MdPetTipoProcessoINT::montarSelectNivelAcesso('null', '', 'null', $idTipoProcesso);
$strSelectTipoConferencia = MdPetIntercorrenteINT::montarSelectTipoConferencia('null', '', 'null');
$selHipoteseLegal = MdPetVinculoINT::montarSelectHipoteseLegal();

$disabledConsultar = $stConsultar  ? 'disabled="disabled"' : null;
?>

<style type="text/css">
#fieldDocumentos {height: auto; width: 96%; margin-bottom: 11px;}
.sizeFieldset {height:auto; width: 86%;}
.fieldsetClear {border:none !important;}
</style>

<fieldset id="fieldDocumentos" class="infraFieldset sizeFieldset" <?php echo !$stAlterar ? 'style="display: none; width: auto;"' : 'style="width: auto;"'?>>
    <legend class="infraLegend">&nbsp; <?php echo $stAlterar ? 'Atualiza��o de ' : null?>Atos Constitutivos&nbsp;</legend>
    <form method="post" id="frmDocumentoAto" enctype="multipart/form-data" action="<?= $strLinkUploadArquivo ?>">

        <br/>
        <div class="container">
            <div class="bloco" style="margin-top: 0.6%;">
                <label>Os documentos devem ser carregados abaixo, sendo de sua exclusiva responsabilidade a conformidade entre os dados informados e os documentos. Os N�veis de Acesso que forem indicados abaixo estar�o condicionados � an�lise por servidor p�blico, que poder� alter�-los a qualquer momento sem necessidade de pr�vio aviso.</label>
            </div>

            <div class="clear"></div>

            <div class="bloco" style="margin-bottom: 10px;" id="blcDocDocumento">
                <label class="infraLabelObrigatorio" for="fileArquivo">Documento (tamanho
                    m�ximo: <?php echo is_int($tamanhoMaximo) ? $tamanhoMaximo . 'Mb' : $tamanhoMaximo; ?>):</label>
                <input type="file" name="fileArquivo" id="fileArquivo"
                    <?php echo $disabledConsultar?>
                    tabindex="<?php echo PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>
            </div>

            <div class="clear"></div>

            <div class="bloco" id="blcDocTipoDocumento">
                <label class="infraLabelObrigatorio" for="selTipoDocumento">Tipo de Documento:  
                <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif"
                    name="ajuda" <?php echo PaginaSEI::montarTitleTooltip($strMsgTooltipTipoDocumento) ?> alt="Ajuda"
                    class="infraImg"/>
                </label>
                <select id="selTipoDocumento" class="infraSelect"
                  <?php echo $disabledConsultar?>
                  tabindex="<?php echo PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
                       <?= $strSelectTipoDocumento ?>
                </select>
            </div>
            <div class="bloco" id="blcDocComplemento">
                <label class="infraLabelObrigatorio" for="txtComplementoTipoDocumento">Complemento do Tipo de Documento:
                    <img src="<?php echo PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif"
                         name="ajuda" <?php echo PaginaSEI::montarTitleTooltip($strMsgTooltipComplementoTipoDocumento) ?> alt="Ajuda" 
                         class="infraImg"/>
                </label>
                <input type="text" class="infraText" id="txtComplementoTipoDocumento"
                  <?php echo $disabledConsultar?>
                       name="txtComplementoTipoDocumento" maxlength="40"
                       onkeypress="return infraMascaraTexto(this,event,40);"
                       tabindex="<?php echo PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>
            </div>

            <div class="clear"></div>

            <div class="bloco" id="divBlcNivelAcesso">
                <label class="infraLabelObrigatorio" for="selNivelAcesso">N�vel de Acesso:
                   <img src="<?php echo PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif"
                        name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipNivelAcesso) ?> alt="Ajuda"
                        class="infraImg"/></label>
                <div id="divNivelAcesso"></div>

              <?php if (!isset($arrHipoteseNivel['nivelAcesso'])){ ?>
                  <select name="selNivelAcesso" id="selNivelAcesso" class="infraSelect"
                    <?php echo $disabledConsultar?>
                          tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
                    <?php echo $strSelectNivelAcesso ?>
                  </select>
              <?php } else { ?>
                  <label class="infraLabelRadio" id="selNivelAcesso">
                    <?php echo $arrHipoteseNivel['nivelAcesso']['descricao'] ?>
                  </label>
              <?php } ?>
                <input type="hidden" name="hdnNivelAcesso" id="hdnNivelAcesso" class="hdnNivelAcesso"
                  <?php echo $disabledConsultar?>
                       value="<?php echo isset($arrHipoteseNivel['nivelAcesso']) ? $arrHipoteseNivel['nivelAcesso']['id'] : '' ?>"/>
            </div>

          <?php if ($exibirHipoteseLegal): ?>
              <div class="bloco"
                   id="divBlcHipoteseLegal" <?= !isset($arrHipoteseNivel['hipoteseLegal']) ? 'style="display: none;"' : '' ?>>
                  <label class="infraLabelObrigatorio" for="selHipoteseLegal">Hip�tese Legal: 
                     <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif"
                          name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipHipoteseLegal) ?> alt="Ajuda"
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
            <div class="clear"></div>
            <div class="bloco" id="blcDocFormato" style="width:290px; margin: 23px 0 11px 0;">
                <label class="infraLabelObrigatorio">Formato: 
                   <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif"
                        name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipFormato) ?>
                        class="infraImg"/></label>
                <input type="radio" class="infraRadio" id="rdoNatoDigital" name="rdoFormato" style="margin-left: 5%;"
                  <?php echo $disabledConsultar?>
                       value="N" onclick="exibirTipoConferencia();"
                       tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>
                <label for="rdoNatoDigital" class="infraLabelRadio">Nato-Digital</label>
                <input type="radio" class="infraRadio" id="rdoDigitalizado" name="rdoFormato" style="margin-left: 5%;"
                  <?php echo $disabledConsultar?>
                       value="D" onclick="exibirTipoConferencia();"
                       tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>
                <label for="rdoDigitalizado" class="infraLabelRadio">Digitalizado</label>
            </div>
            
            <div class="bloco">
                <div id="divTipoConferencia" style="display: none; margin-top: -5px;">
                    <label class="infraLabelObrigatorio" for="selTipoConferencia">Confer�ncia com o documento digitalizado:</label>
                    <select id="selTipoConferencia" name="selTipoConferencia" class="infraSelect"
                    <?php echo $disabledConsultar?>
                        tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"><?= $strSelectTipoConferencia ?></select>
                </div>
                <button type="button" class="infraButton" id="btnAdicionarDocumento"
                  <?php
                  echo $disabledConsultar;
                  if ($disabledConsultar==null){
                      echo ' accesskey="A"';
                  }
                  ?> onclick="adicionarDocumento();"
                  tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"><span class="infraTeclaAtalho">A</span>dicionar
                </button>
            </div>
<?php   $arrArquivo = null; ?>
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
                    <th class="infraTh" align="center" width="10%">N�vel de Acesso</th> <!--13-->
                    <th class="infraTh" align="center" width="10%">Formato</th> <!--14-->
                    <th class="infraTh" align="center" width="5%">A��es</th> <!--15-->
                </tr>
                <?php

                if(!is_null($arrArquivo)){?>
                    <?php foreach ($arrArquivo as $chave=>$arquivo) {?>
                    <tr class="infraTrClara">
                        <td class="infraTd" style="display: none;"><div><?php echo $chave+1?></div></td>
                        <td class="infraTd" style="display: none;"><div><?php echo $arquivo->getNumIdSerie();?></div></td>
                        <td class="infraTd" style="display: none;"><div><?php echo $arquivo->getStrNumeroDocumento();?></div></td>
                        <td class="infraTd" style="display: none;"><div><?php echo $arquivo->getStrStaNivelAcesso();?></div></td>
                        <td class="infraTd" style="display: none;"><div><?php echo $arquivo->getNumIdHipoteseLegal();?></div></td>
                        <td class="infraTd" style="display: none;"><div></div></td>
                        <td class="infraTd" style="display: none;"><div><?php echo $arquivo->getNumIdTipoConferencia();?></div></td>
                        <td class="infraTd"><div style="text-align:center;"><?php echo $arquivo->getStrNomeArquivoAnexo();?></div></td>
                        <td class="infraTd"><div style="text-align:center;"><?php echo $arquivo->getDthDataArquivoAnexo();?></div></td>
                        <td class="infraTd"><div style="text-align:center;"><?php echo $arquivo->getNumTamanhoArquivoAnexo()/1000;?> Kb</div></td>
                        <td class="infraTd"><div style="text-align:center;"><?php echo $arquivo->getStrNomeSerieProtocolo().' '.$arquivo->getStrNumeroDocumento();?></div></td>
                        <td class="infraTd"><div style="text-align:center;"><?php echo $arrDescricaoNivelAcesso[$arquivo->getStrStaNivelAcesso()];?></div></td>
                        <td class="infraTd"><div style="text-align:center;"><?php echo is_null($arquivo->getNumIdTipoConferencia()) ? 'Nato-Digital' : 'Digitalizado';?></div></td>
                        <td align="center" valign="center"></td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </table>
        </div>
        <input type="hidden" name="hdnTbDocumento" value="" id="hdnTbDocumento"/>
        <input type="hidden" name="hdnIdsDocObrigatorios" id="hdnIdsDocObrigatorios" value='<?php echo $strDocsObrigatorios; ?>' />
        <input type="hidden" name="hdnIsAlteracao" id="hdnIsAlteracao" value="<?php echo $stAlterar ? '1' : '0' ?>"/>
    </form>
</fieldset>
<br id="fieldDocumentos_BR" style="display: none">