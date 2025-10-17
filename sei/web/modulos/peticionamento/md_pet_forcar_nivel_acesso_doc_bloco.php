<?php

$strLinkAjaxTiposDocumentos = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_tipo_documento_auto_completar');
$strLinkTipoDocumentoSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_serie_selecionar&tipo_selecao=2&filtro=3&id_object=objLupaTipoDocumento');

$staTipoPeticinamento = isset($staTipoPeticinamento) ? $staTipoPeticinamento : 'N';

$objMdPetForcarNivelAcessoDocDTO2 = new MdPetForcarNivelAcessoDocDTO();
$objMdPetForcarNivelAcessoDocDTO2->setStrTipoPeticionamento($staTipoPeticinamento);
$objMdPetForcarNivelAcessoDocDTO2->retTodos();
$objMdPetForcarNivelAcessoDocDTO2 = (new MdPetForcarNivelAcessoDocRN())->consultar($objMdPetForcarNivelAcessoDocDTO2);

$idHipoteseLegal = !is_null($objMdPetForcarNivelAcessoDocDTO2) ? $objMdPetForcarNivelAcessoDocDTO2->getNumIdHipoteseLegal() : null;
$selHipoteseLegal= MdPetHipoteseLegalINT::montarSelectHipoteseLegal($booOptionsOnly = true, $idHipoteseLegal);

$strItensSelTipoDocumento = '';

if(!empty($objMdPetForcarNivelAcessoDocDTO2)){

    $idsTiposDocumento = explode(',',$objMdPetForcarNivelAcessoDocDTO2->getStrIdsTiposDocumento());

    $arrObjSerieDTO = new SerieDTO();
    $arrObjSerieDTO->setNumIdSerie($idsTiposDocumento, InfraDTO::$OPER_IN);
    $arrObjSerieDTO->retNumIdSerie();
    $arrObjSerieDTO->retStrNome();
    $arrObjSerieDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);
    $arrObjSerieDTO = (new SerieRN())->listarRN0646($arrObjSerieDTO);

    if(count($arrObjSerieDTO) > 0){
        foreach($arrObjSerieDTO as $objSerieDTO){
            $strItensSelTipoDocumento .= '<option value="'.$objSerieDTO->getNumIdSerie().'">'.$objSerieDTO->getStrNome().'</option>';
        }
    }

}

?>
<div class="row">
    <div class="col-12">
        <fieldset id="fldForcarNivelAcessoDocumentosExternos" class="infraFieldset">
            <legend class="infraLegend px-2">Forçar Nível de Acesso em Documentos Externos Específicos</legend>
            <div class="row">
                <div class="col-3">
                    <div class="form-group">
                        <label id="" for="" accesskey="" class="infraLabelObrigatorio">Nível de Acesso:</label>
                        <select name="staNivelAcesso" id="staNivelAcesso" class="infraSelect form-select" onchange="changeStaNivelAcesso()" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>">
                            <option value="">Selecione</option>
                            <option value="P" <?= (!empty($objMdPetForcarNivelAcessoDocDTO2) && $objMdPetForcarNivelAcessoDocDTO2->getStrNivelAcesso() == 'P') ? 'selected="selected"' : '' ?>>Público</option>
                            <option value="R" <?= (!empty($objMdPetForcarNivelAcessoDocDTO2) && $objMdPetForcarNivelAcessoDocDTO2->getStrNivelAcesso() == 'R') ? 'selected="selected"' : '' ?>>Restrito</option>
                        </select>
                    </div>
                </div>
                <?php if((new InfraParametro(BancoSEI::getInstance()))->getValor('SEI_HABILITAR_HIPOTESE_LEGAL') == '2'): ?>
                    <div class="col-9" id="forcarHipoteseLegal" style="display: <?= (!empty($objMdPetForcarNivelAcessoDocDTO2) && $objMdPetForcarNivelAcessoDocDTO2->getStrNivelAcesso() == 'R') ? 'block' : 'none' ?>">
                        <div class="form-group">
                            <label id="lbIdHipoteseLegal" for="idHipoteseLegal" accesskey="" class="infraLabelObrigatorio">Hipótese Legal:</label>
                            <select name="idHipoteseLegal" id="idHipoteseLegal" class="infraSelect form-select" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>">
                                <?= $selHipoteseLegal; ?>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <div class="form-group">
                        <div class="row">
                            <div id="divTipoDocumento" class="col-xs-5 col-sm-8 col-md-8 col-lg-6">
                                <label id="lblTipoDocumento" for="selTipoDocumento" accesskey="" class="infraLabelObrigatorio">Tipos de Documentos:</label>
                                <input type="text" id="txtTipoDocumento" name="txtTipoDocumento" class="infraText form-control" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-10 col-md-10 col-lg-9">
                                <div class="input-group">
                                    <select id="selTipoDocumento" name="selTipoDocumento" size="8" multiple="multiple" class="infraSelect form-control <?= isset($strDesabilitar) && $strDesabilitar != '' ? '' : 'mr-1'?>"  tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>">
                                        <?= $strItensSelTipoDocumento ?>
                                    </select>
                                    <div id="_divOpcoesTipoDocumento" style="<?= $strDesabilitar ?>" class="ml-1">
                                        <img id="imgLupaTipoDocumento" onclick="objLupaTipoDocumento.selecionar(700,500);" src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/pesquisar.svg'?>" alt="Selecionar Tipo de Documento" title="Selecionar Tipo de Documento" class="infraImg" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
                                        <br>
                                        <img id="imgExcluirTipoDocumento" onclick="objLupaTipoDocumento.remover();" src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/remover.svg'?>" alt="Remover Tipo de Documento Selecionado" title="Remover Tipo de Documento Selecionado" class="infraImg mb-4" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
                                        <img onclick="limparForcarNivelAcessoDoc()" title="Limpar definições desta seção" alt="Limpar definições desta seção" src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/excluir.svg' ?>" class="infraImg d-block mt-5"  tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"/>
                                    </div>
                                </div>
                                <input type="hidden" class="form-control" id="hdnIdTipoDocumento" name="hdnIdTipoDocumento" value=""/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" class="form-control" id="hdnTipoDocumento" name="hdnTipoDocumento" value="<?= $_POST['hdnTipoDocumento'] ?>" />
        </fieldset>
        <input type="hidden" class="form-control" id="hdnForcarNivelAcessoDoc" name="hdnForcarNivelAcessoDoc" value="S" />
    </div>
</div>
