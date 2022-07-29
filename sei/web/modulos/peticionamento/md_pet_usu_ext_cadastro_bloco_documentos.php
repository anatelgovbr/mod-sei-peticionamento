<?

    /**
     * ANATEL
     *
     * 01/08/2016 - criado por marcelo.bezerra@cast.com.br - CAST
     *
     * Página contendo área "Documentos" da página
     * Contém todo o FIELDSET da área Documentos, englobando Documentos Principais, Essenciais e Complementares
     * Essa página é incluida na página principal do cadastro de peticionamento
     */
    //Acao para upload de documento principal
    $strLinkUploadDocPrincipal = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usu_ext_upload_doc_principal');

    //Acao para upload de documento essencial
    $strLinkUploadDocEssencial = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usu_ext_upload_doc_essencial');

    //Acao para upload de documento complementar
    $strLinkUploadDocComplementar = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usu_ext_upload_doc_complementar');

    function tooltipAjuda($helpMessage, $helpTitle = 'Ajuda'){
        return '<img src="'.PaginaSEI::getInstance()->getDiretorioSvgGlobal().'/ajuda.svg" name="ajuda"'. PaginaSEI::montarTitleTooltip($helpMessage, $helpTitle).' alt="'.$helpTitle.'" class="infraImg"/>';
    }

?>
<!-- =========================== -->
<!--  INÍCIO BLOCO / ÁREA DOCUMENTOS -->
<!-- =========================== -->
<fieldset id="field3" class="infraFieldset sizeFieldset">

    <legend class="infraLegend">&nbsp; Documentos &nbsp;</legend>

    <form method="post" id="frmDocumentoPrincipal" enctype="multipart/form-data" action="<?= $strLinkUploadDocPrincipal ?>">

        <input type="hidden" id="hdnDocPrincipal" name="hdnDocPrincipal" value="<?= $_POST['hdnDocPrincipal'] ?>"/>
        <input type="hidden" id="hdnDocPrincipalInicial" name="hdnDocPrincipalInicial" value="<?= $_POST['hdnDocPrincipalInicial'] ?>"/>

        <label class="d-block mb-3">
            Os documentos devem ser carregados abaixo, sendo de sua exclusiva responsabilidade a conformidade entre
            os dados informados e os documentos. Os Níveis de Acesso que forem indicados abaixo estarão condicionados à
            análise por servidor público, que poderá alterá-los a qualquer momento sem necessidade de prévio
            aviso.
        </label>

        <?

            $objTamanhoMaximoDTO = new MdPetTamanhoArquivoDTO();
            $objTamanhoMaximoDTO->setStrSinAtivo('S');
            $objTamanhoMaximoDTO->retTodos();

            $objTamanhoMaximoRN = new MdPetTamanhoArquivoRN();
            $arrTamanhoMaximo   = $objTamanhoMaximoRN->listarTamanhoMaximoConfiguradoParaUsuarioExterno($objTamanhoMaximoDTO);

            $strTamanhoMaximoPrincipal = "Limite não configurado na Administração do Sistema.";
            $strTamanhoMaximoComplementar = $strTamanhoMaximoPrincipal;

            if (is_array($arrTamanhoMaximo) && count($arrTamanhoMaximo) > 0) {

                $numValorTamanhoMaximo = $arrTamanhoMaximo[0]->getNumValorDocPrincipal();
                $numValorTamanhoMaximoComplementar = $arrTamanhoMaximo[0]->getNumValorDocComplementar();

                if ($numValorTamanhoMaximo != null && $numValorTamanhoMaximo > 0) {
                    $strTamanhoMaximoPrincipal = $numValorTamanhoMaximo . " Mb";
                }

                if ($numValorTamanhoMaximoComplementar != null && $numValorTamanhoMaximoComplementar > 0) {
                    $strTamanhoMaximoComplementar = $numValorTamanhoMaximoComplementar . " Mb";
                }

            }

            //checando se Documento Principal está parametrizado para "Externo (Anexação de Arquivo) ou Gerador (editor do SEI)
            $gerado     = $ObjMdPetTipoProcessoDTO->getStrSinDocGerado();
            $externo    = $ObjMdPetTipoProcessoDTO->getStrSinDocExterno();

            if ($externo == 'S') {

        ?>

            <div class="row">
                <div class="col-sm-12 col-md-10 col-lg-10 col-xl-10">
                    <div id="divArquivo" class="form-group infraAreaDados mb-4">
                        <div class="form-group">
                            <label class="infraLabelObrigatorio" for="fileArquivoPrincipal">
                                Documento Principal (<?= $strTamanhoMaximoPrincipal?>):
                                <input type="hidden" name="hdnTamArquivoPrincipal" id="hdnTamArquivoPrincipal" value="<?= $strTamanhoMaximoPrincipal ?>" tabindex="-1">
                            </label><br/>
                            <input type="file" name="fileArquivoPrincipal" class="form-control-file" id="fileArquivoPrincipal" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-4 col-lg-3 col-xl-3">
                    <div class="form-group">
                        <label id="lblPublico" class="infraLabelObrigatorio">
                            Tipo de Documento: <?= tooltipAjuda($strMsgTooltipTipoDocumentoPrincipal) ?>
                        </label><br/>
                        <select class="form-control infraSelect" id="tipoDocumentoPrincipal" disabled>
                            <option value="<?= $serieDTO->getNumIdSerie() ?>"><?= $strTipoDocumentoPrincipal ?></option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-12 col-md-8 col-lg-8 col-xl-9">
                    <div class="form-group">
                        <label id="lblPublico" class="infraLabelObrigatorio">
                            Complemento do Tipo de Documento: <?= tooltipAjuda($strMsgTooltipComplementoTipoDocumento) ?>
                        </label><br/>
                        <input type="text" name="complementoPrincipal" class="form-control infraText" id="complementoPrincipal" maxlength="40" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                    </div>
                </div>
            </div>

        <? } ?>

        <? if ($gerado == 'S'): ?>

            <!-- DOCUMENTO PRINCIPAL DO TIPO GERADO -->

            <div class="row">
                <div class="col-sm-12 col-md-10 col-lg-10 col-xl-10">
                    <div class="form-group mb-3">
                        <div class="input-group">
                            <label class="infraLabelObrigatorio pr-2">
                                Documento Principal:
                            </label><br/>
                            <label class="infraLabel" onclick="abrirJanelaDocumento()" style="cursor: pointer" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgLocal() ?>/documento_formulario2.svg" name="formulario" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipTipoDocumentoPrincipalFormulario) ?> alt="Formulário" style="vertical-align: middle"/>
                                <?= $strTipoDocumentoPrincipal ?>
                                (clique aqui para editar conteúdo)
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12 col-md-4 col-lg-3 col-xl-3">
                    <div class="form-group">
                        <? if ($isUsuarioExternoPodeIndicarNivelAcesso == 'S'): ?>
                            <label id="lblPublico" class="infraLabelObrigatorio">
                                Nível de Acesso: <?= tooltipAjuda($strMsgTooltipNivelAcesso) ?>
                            </label><br/>
                            <select name="nivelAcesso1" class="form-control infraSelect" id="nivelAcesso1" onchange="selectNivelAcesso('nivelAcesso1', 'hipoteseLegal1')" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                <?= $strItensSelNivelAcesso ?>
                            </select>
                        <? elseif ($isNivelAcessoPadrao == 'S'): ?>
                            <label id="lblPublico" class="infraLabelObrigatorio">
                                Nível de Acesso: <?= tooltipAjuda($strMsgTooltipNivelAcessoPadraoPreDefinido) ?>
                            </label><br/>
                            <select class="form-control infraSelect" disabled tabindex="-1">
                                <option value=""><?= $strNomeNivelAcessoPadrao ?></option>
                            </select>
                            <input type="hidden" name="nivelAcesso1" id="nivelAcesso1" value="<?= $nivelAcessoPadrao ?>"/>
                        <? endif ?>
                    </div>
                </div>
                <div class="col-sm-12 col-md-8 col-lg-8 col-xl-9">
                    <div class="form-group" id="divhipoteseLegal1" style="display: <?= ($isNivelAcessoPadrao == 'S' && $nivelAcessoPadrao == "1") ? 'block' : 'none' ?>">

                        <? if ($isConfigHipoteseLegal && $isNivelAcessoPadrao != 'S'): ?>

                            <label id="lblPublico" class="infraLabelObrigatorio">
                                Hipótese Legal: <?= tooltipAjuda($strMsgTooltipHipoteseLegal) ?>
                            </label><br/>
                            <select name="hipoteseLegal1" class="form-control infraSelect" id="hipoteseLegal1" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                <option value=""></option>
                                <?
                                    if ($isConfigHipoteseLegal && is_array($arrHipoteseLegal) && count($arrHipoteseLegal) > 0) {
                                        foreach ($arrHipoteseLegal as $itemObj) {
                                            echo '<option value="'.$itemObj->getNumIdHipoteseLegal().'">'.$itemObj->getStrNome().'('.$itemObj->getStrBaseLegal().')</option>';
                                        }
                                    }
                                ?>
                            </select>

                        <? elseif ($isConfigHipoteseLegal && $isNivelAcessoPadrao == 'S' && $nivelAcessoPadrao == "1"): ?>

                            <label id="lblPublico" class="infraLabelObrigatorio">
                                Hipótese Legal: <?= tooltipAjuda($strMsgTooltipHipoteseLegalPadraoPreDefinido) ?>
                            </label><br/>
                            <select class="form-control infraSelect" disabled tabindex="-1">
                                <option value=""><?= $strHipoteseLegalPadrao ?></option>
                            </select>
                            <input type="hidden" name="hipoteseLegal1" id="hipoteseLegal1" value="<?= $idHipoteseLegalPadrao ?>" tabindex="-1"/>

                        <? endif ?>

                        <? if ($externo == 'S'): ?>
                            <input type="button" class="infraButton" value="Adicionar" name="btAddDocumentos" onclick="validarUploadArquivo('1')" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        <? endif ?>
                    </div>
                </div>
            </div>

        <? else: ?>

            <!-- DOCUMENTO PRINCIPAL DO TIPO EXTERNO -->
            <div class="row">
                <div class="col-sm-12 col-md-4 col-lg-3 col-xl-3">
                    <div class="form-group">
                        <? if ($isUsuarioExternoPodeIndicarNivelAcesso == 'S'): ?>
                            <label id="lblPublico" class="infraLabelObrigatorio">
                                Nível de Acesso: <?= tooltipAjuda($strMsgTooltipNivelAcesso) ?>
                            </label><br/>
                            <select class="form-control infraSelect" id="nivelAcesso1" name="nivelAcesso1" onchange="selectNivelAcesso('nivelAcesso1', 'hipoteseLegal1')" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                <?= $strItensSelNivelAcesso ?>
                            </select>
                        <? elseif ($isNivelAcessoPadrao == 'S'): ?>
                            <label id="lblPublico" class="infraLabelObrigatorio">
                                Nível de Acesso: <?= tooltipAjuda($strMsgTooltipNivelAcessoPadraoPreDefinido) ?>
                            </label><br/>
                            <select class="form-control infraSelect" disabled tabindex="-1">
                                <option value=""><?= $strNomeNivelAcessoPadrao ?></option>
                            </select>
                            <input type="hidden" name="nivelAcesso1" id="nivelAcesso1" value="<?= $nivelAcessoPadrao ?>" tabindex="-1"/>
                        <? endif ?>
                    </div>
                </div>
                <div class="col-sm-12 col-md-8 col-lg-8 col-xl-9">
                    <div id="divhipoteseLegal1" class="form-group" style="display: <?= ($isNivelAcessoPadrao == 'S' && $nivelAcessoPadrao == "1") ? 'block' : 'none' ?>">
                        <? if ($isConfigHipoteseLegal && $isNivelAcessoPadrao != 'S'): ?>

                            <label id="lblPublico" class="infraLabelObrigatorio">
                                Hipótese Legal: <?= tooltipAjuda($strMsgTooltipHipoteseLegal) ?>
                            </label><br/>
                            <select class="form-control infraSelect" id="hipoteseLegal1" name="hipoteseLegal1" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                <option value=""></option>
                                <?
                                    if ($isConfigHipoteseLegal && is_array($arrHipoteseLegal) && count($arrHipoteseLegal) > 0) {
                                        foreach ($arrHipoteseLegal as $itemObj) {
                                            echo '<option value="'.$itemObj->getNumIdHipoteseLegal().'">'.$itemObj->getStrNome().'('.$itemObj->getStrBaseLegal().')</option>';
                                        }
                                    }
                                ?>
                            </select>

                        <? elseif ($isConfigHipoteseLegal && $isNivelAcessoPadrao == 'S' && $nivelAcessoPadrao == "1"): ?>

                            <label id="lblPublico" class="infraLabelObrigatorio">
                                Hipótese Legal: <?= tooltipAjuda($strMsgTooltipHipoteseLegalPadraoPreDefinido) ?>
                            </label><br/>
                            <select class="form-control infraSelect" disabled tabindex="-1">
                                <option value=""><?= $strHipoteseLegalPadrao ?></option>
                            </select>
                            <input type="hidden" name="hipoteseLegal1" id="hipoteseLegal1" value="<?= $idHipoteseLegalPadrao ?>" tabindex="-1"/>

                        <? endif ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12 col-md-4 col-lg-3 col-xl-3">
                    <div class="form-group">
                        <label id="lblPublico" class="infraLabelObrigatorio">
                            Formato: <?= tooltipAjuda($strMsgTooltipFormato) ?>
                        </label><br/>
                        <div class="form-check form-check-inline mr-1">
                            <input class="form-check-input infraRadio" type="radio" style="position: absolute" name="formatoDocumentoPrincipal" id="rdNato1_1" value="nato" onclick="selecionarFormatoNatoDigitalPrincipal()" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                            <label class="form-check-label" for="rdNato1_1">Nato-digital</label>
                        </div>
                        <div class="form-check form-check-inline mr-0">
                            <input class="form-check-input infraRadio" type="radio" style="position: absolute" name="formatoDocumentoPrincipal" id="rdDigitalizado1_2" value="digitalizado" onclick="selecionarFormatoDigitalizadoPrincipal()">
                            <label class="form-check-label" for="rdDigitalizado1_2">Digitalizado</label>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-8 col-lg-8 col-xl-9">
                    <div class="form-group">
                        <div id="camposDigitalizadoPrincipal" style="display: none">
                            <label class="infraLabelObrigatorio">Conferência com o documento digitalizado:</label><br/>
                            <div class="input-group">
                                <select name="TipoConferenciaPrincipal" class="form-control infraSelect" id="TipoConferenciaPrincipal" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                    <option value=""></option>
                                    <?
                                        foreach ($arrTipoConferencia as $tipoConferencia){
                                            echo '<option value="'.$tipoConferencia->getNumIdTipoConferencia().'">'.$tipoConferencia->getStrDescricao().'</option>';
                                        }
                                    ?>
                                </select>
                                <div class="input-group-append">
                                    <input type="button" class="infraButton" value="Adicionar" onclick="validarUploadArquivo('1')" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                </div>
                            </div>
                        </div>
                        <div id="camposDigitalizadoPrincipalBotao">
                            <input type="button" class="infraButton mt-3" value="Adicionar" onclick="validarUploadArquivo('1')" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                        </div>
                    </div>
                </div>
            </div>

        <? endif ?>

        <? if ($externo == 'S'): ?>

            <div class="table-responsive">
                <table id="tbDocumentoPrincipal" name="tbDocumentoPrincipal" class="infraTable" style="width:100%;">

                    <tr>
                        <th class="infraTh" style="width:25%;">Nome do Arquivo</th>
                        <th class="infraTh" style="width:80px;" align="center">Data</th>
                        <th class="infraTh" style="width:80px;" align="center">Tamanho</th>
                        <th class="infraTh" style="width:25%;" align="center">Documento</th>
                        <th class="infraTh" style="width:120px;" align="center">Nível de Acesso</th>

                        <!--  colunas nao exibidas na tela, usadas apenas para guardar valor na grid (note que estao com display:none) -->
                        <th class="infraTh" style="display: none;">Hipótese Legal</th>
                        <th class="infraTh" style="display: none;">Formato</th>
                        <th class="infraTh" style="display: none;">Tipo de Conferência</th>
                        <th class="infraTh" style="display: none;">Nome Upload servidor</th>
                        <th class="infraTh" style="display: none;">ID Tipo de Documento</th>
                        <th class="infraTh" style="display: none;">Complemento</th>
                        <th class="infraTh" style="width: 120px;" align="center">Formato</th>

                        <!-- Coluna de ações (Baixar, remover) da grid -->
                        <th align="center" class="infraTh" style="width:50px;">Ações</th>
                    </tr>

                </table>
            </div>

        <? endif ?>
    </form>
    <!-- ================================== FIM DOCUMENTO PRINCIPAL  =============================================== -->

    <form action="<?= $strLinkUploadDocEssencial ?>" method="post" id="frmDocumentosEssenciais" enctype="multipart/form-data">

        <input type="hidden" id="hdnDocEssencial" name="hdnDocEssencial" value="<?= $_POST['hdnDocEssencial'] ?>"/>
        <input type="hidden" id="hdnDocEssencialInicial" name="hdnDocEssencialInicial" value="<?= $_POST['hdnDocEssencialInicial'] ?>"/>

        <!-- ================================== INICIO DOCUMENTOS ESSENCIAIS  =============================================== -->
        <?

            $objMdPetRelTpProcSerieDTO = new MdPetRelTpProcSerieDTO();
            $objMdPetRelTpProcSerieDTO->retTodos();
            $objMdPetRelTpProcSerieDTO->setStrStaTipoDoc(MdPetRelTpProcSerieRN::$DOC_ESSENCIAL);
            $objMdPetRelTpProcSerieDTO->setNumIdTipoProcessoPeticionamento($objTipoProcDTO->getNumIdTipoProcessoPeticionamento());
            $objMdPetRelTpProcSerieRN = new MdPetRelTpProcSerieRN();

            $arrMdPetRelTpProcSerieDTO = $objMdPetRelTpProcSerieRN->listar($objMdPetRelTpProcSerieDTO);

            if (is_array($arrMdPetRelTpProcSerieDTO) && count($arrMdPetRelTpProcSerieDTO) > 0){

        ?>
        <div class="row">
            <div class="col-12">
                <hr style="border:none; padding:0; margin: 5px 1px 12px 1px; border-top:medium double #333; margin-bottom: 30px"/>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-10 col-lg-10 col-xl-10">
                <div id="divArquivo" class="form-group infraAreaDados mb-4">
                    <label class="infraLabelObrigatorio" for="fileArquivoEssencial">
                        Documento Essencial (<?= $strTamanhoMaximoComplementar?>):
                        <input type="hidden" name="hdnTamArquivoEssencial" id="hdnTamArquivoEssencial" value="<?= $strTamanhoMaximoComplementar ?>">
                    </label><br/>
                    <input type="file" name="fileArquivoEssencial" class="form-control-file" id="fileArquivoEssencial" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-4 col-lg-3 col-xl-3">
                <div class="form-group">

                    <label id="lblPublico" class="infraLabelObrigatorio">
                        Tipo de Documento: <?= tooltipAjuda($strMsgTooltipTipoDocumento) ?>
                    </label><br/>

                    <select name="tipoDocumentoEssencial" class="form-control infraSelect" id="tipoDocumentoEssencial" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                        <option value=""></option>
                        <?

                            if (is_array($arrMdPetRelTpProcSerieDTO) && count($arrMdPetRelTpProcSerieDTO) > 0) {

                                foreach ($arrMdPetRelTpProcSerieDTO as $item){

                                    $serieDTO = new SerieDTO();
                                    $serieDTO->retTodos();
                                    $serieDTO->setNumIdSerie($item->getNumIdSerie());
                                    $serieDTO = $serieRN->consultarRN0644($serieDTO);

                                    echo '<option value="'.$item->getNumIdSerie().'">'.$serieDTO->getStrNome().'</option>';

                                }
                            }

                        ?>
                    </select>

                </div>
            </div>
            <div class="col-sm-12 col-md-8 col-lg-8 col-xl-9">
                <div class="form-group">
                    <label id="lblPublico" class="infraLabelObrigatorio">
                        Complemento do Tipo de Documento: <?= tooltipAjuda($strMsgTooltipComplementoTipoDocumento) ?>
                    </label><br/>
                    <input type="text" name="complementoEssencial" class="form-control infraText" id="complementoEssencial" maxlength="40" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-4 col-lg-3 col-xl-3">
                <div class="form-group">
                    <? if ($isUsuarioExternoPodeIndicarNivelAcesso == 'S'): ?>
                        <label id="lblPublico" class="infraLabelObrigatorio">
                            Nível de Acesso: <?= tooltipAjuda($strMsgTooltipNivelAcesso) ?>
                        </label><br/>
                        <select name="nivelAcesso2" class="infraSelect form-control" id="nivelAcesso2" onchange="selectNivelAcesso('nivelAcesso2', 'hipoteseLegal2')" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                            <?= $strItensSelNivelAcesso ?>
                        </select>
                    <? else: ?>
                        <label id="lblPublico" class="infraLabelObrigatorio">
                            Nível de Acesso: <?= tooltipAjuda($strMsgTooltipNivelAcessoPadraoPreDefinido) ?>
                        </label><br/>
                        <select class="infraSelectform-group" disabled tabindex="-1">
                            <option value=""><?= $strNomeNivelAcessoPadrao ?></option>
                        </select>
                        <input type="hidden" value="<?= $nivelAcessoPadrao ?>" id="nivelAcesso2" name="nivelAcesso2" tabindex="-1"/>
                    <? endif ?>
                </div>
            </div>
            <div class="col-sm-12 col-md-8 col-lg-8 col-xl-9">
                <div class="form-group" id="divhipoteseLegal2" style="display: <?= ($isNivelAcessoPadrao == 'S' && $nivelAcessoPadrao == ProtocoloRN::$NA_RESTRITO) ? 'block' : 'none' ?>">

                    <? if ($isConfigHipoteseLegal && $isNivelAcessoPadrao != 'S') { ?>

                        <label class="infraLabelObrigatorio" id="lblPublico">
                            Hipótese Legal: <?= tooltipAjuda($strMsgTooltipHipoteseLegal) ?>
                        </label><br/>
                        <select name="hipoteseLegal2" class="form-control infraSelect" id="hipoteseLegal2" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                            <option value=""></option>
                            <?
                                if ($isConfigHipoteseLegal && is_array($arrHipoteseLegal) && count($arrHipoteseLegal) > 0) {
                                    foreach ($arrHipoteseLegal as $itemObj) {
                                        echo '<option value="'.$itemObj->getNumIdHipoteseLegal().'">'.$itemObj->getStrNome().' ('.$itemObj->getStrBaseLegal().')</option>';
                                    }
                                }
                            ?>
                        </select>

                    <? } else if ($isConfigHipoteseLegal && $isNivelAcessoPadrao == 'S' && $nivelAcessoPadrao == "1") { ?>

                        <label id="lblPublico" class="infraLabelObrigatorio">
                            Hipótese Legal: <?= tooltipAjuda($strMsgTooltipHipoteseLegalPadraoPreDefinido) ?>
                        </label><br/>
                        <select class="form-group infraSelect" disabled tabindex="-1">
                            <option value=""><?= $strHipoteseLegalPadrao ?></option>
                        </select>
                        <input type="hidden" name="hipoteseLegal2" id="hipoteseLegal2" value="<?= $idHipoteseLegalPadrao ?>" tabindex="-1"/>

                    <? } ?>

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-4 col-lg-3 col-xl-3">
                <div class="form-group">
                    <label id="lblPublico" class="infraLabelObrigatorio">
                        Formato: <?= tooltipAjuda($strMsgTooltipFormato) ?>
                    </label><br/>
                    <div class="form-check form-check-inline mr-1">
                        <input class="form-check-input infraRadio" type="radio" style="position: absolute" name="formatoDocumentoEssencial" id="rdNato2_1" value="nato" onclick="selecionarFormatoNatoDigitalEssencial()" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                        <label class="form-check-label" for="rdNato2_1">Nato-digital</label>
                    </div>
                    <div class="form-check form-check-inline mr-0">
                        <input class="form-check-input infraRadio" type="radio" style="position: absolute" name="formatoDocumentoEssencial" id="rdDigitalizado2_2" value="digitalizado" onclick="selecionarFormatoDigitalizadoEssencial()">
                        <label class="form-check-label" for="rdDigitalizado2_2">Digitalizado</label>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-8 col-lg-8 col-xl-9">
                <div class="form-group">
                    <div id="camposDigitalizadoEssencial" style="display: none;">
                        <label class="infraLabelObrigatorio">Conferência com o documento digitalizado:</label><br/>
                        <div class="input-group">
                            <select class="form-control infraSelect" id="TipoConferenciaEssencial" name="TipoConferenciaEssencial"  tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                <option value=""></option>
                                <?
                                    foreach ($arrTipoConferencia as $tipoConferencia) {
                                        echo "<option value='".$tipoConferencia->getNumIdTipoConferencia()."'>".$tipoConferencia->getStrDescricao()."</option>";
                                    }
                                ?>
                            </select>
                            <div class="input-group-append">
                                <input type="button" class="infraButton" value="Adicionar" onclick="validarUploadArquivo('2')" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                            </div>
                        </div>
                    </div>

                    <div id="camposDigitalizadoEssencialBotao">
                        <input type="button" class="infraButton mt-3" value="Adicionar" onclick="validarUploadArquivo('2')" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table id="tbDocumentoEssencial" name="tbDocumentoEssencial" class="infraTable" style="width:100%;">
                        <tr>
                            <th class="infraTh" style="width:25%;">Nome do Arquivo</th>
                            <th class="infraTh" style="width:80px;" align="center">Data</th>
                            <th class="infraTh" style="width:80px;" align="center">Tamanho</th>
                            <th class="infraTh" style="width:25%;" align="center">Documento</th>
                            <th class="infraTh" style="width:120px;" align="center">Nível de Acesso</th>

                            <!--  colunas nao exibidas na tela, usadas apenas para guardar valor na grid (note que estao com display:none) -->
                            <th class="infraTh" style="display: none;">Hipótese Legal</th>
                            <th class="infraTh" style="display: none;">Formato</th>
                            <th class="infraTh" style="display: none;">Tipo de Conferência</th>
                            <th class="infraTh" style="display: none;">Nome Upload servidor</th>
                            <th class="infraTh" style="display: none;">ID Tipo de Documento</th>
                            <th class="infraTh" style="display: none;">Complemento</th>
                            <th class="infraTh" style="width: 120px;" align="center">Formato</th>

                            <!-- Coluna de ações (Baixar, remover) da grid -->
                            <th align="center" class="infraTh" style="width:50px;">Ações</th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <? } ?>
    </form>
    <!-- ================================== FIM DOCUMENTOS ESSENCIAIS  =============================================== -->

    <form action="<?= $strLinkUploadDocComplementar ?>" method="post" id="frmDocumentosComplementares" enctype="multipart/form-data">

        <input type="hidden" id="hdnDocComplementar" name="hdnDocComplementar" value="<?= $_POST['hdnDocComplementar'] ?>"/>
        <input type="hidden" id="hdnDocComplementarInicial" name="hdnDocComplementarInicial" value="<?= $_POST['hdnDocComplementarInicial'] ?>"/>

        <!-- ================================== INICIO DOCUMENTOS COMPLEMENTARES  =============================================== -->
        <?php

            //o bloco de seleçao de documento essencial pode sumir da tela
            // conforme parametrizaçao da Administraçao do modulo
            $objMdPetRelTpProcSerieDTO = new MdPetRelTpProcSerieDTO();
            $objMdPetRelTpProcSerieDTO->retTodos();
            $objMdPetRelTpProcSerieDTO->setStrStaTipoDoc(MdPetRelTpProcSerieRN::$DOC_COMPLEMENTAR);
            $objMdPetRelTpProcSerieDTO->setNumIdTipoProcessoPeticionamento($objTipoProcDTO->getNumIdTipoProcessoPeticionamento());
            $objMdPetRelTpProcSerieRN = new MdPetRelTpProcSerieRN();

            $arrMdPetRelTpProcSerieDTO = $objMdPetRelTpProcSerieRN->listar($objMdPetRelTpProcSerieDTO);

            if (is_array($arrMdPetRelTpProcSerieDTO) && count($arrMdPetRelTpProcSerieDTO) > 0){

        ?>

        <div class="row">
            <div class="col-12">
                <hr style="border:none; padding:0; margin: 5px 1px 12px 1px; border-top:medium double #333; margin-bottom: 30px"/>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-10 col-lg-10 col-xl-10">
                <div id="divArquivo" class="form-group infraAreaDados mb-4">
                    <label class="" for="fileArquivoPrincipal">
                        Documentos Complementares (<?= $strTamanhoMaximoComplementar ?>):
                        <input type="hidden" name="hdnTamArquivoComplementar" id="hdnTamArquivoComplementar" value="<?= $strTamanhoMaximoPrincipal ?>" tabindex="-1">
                    </label><br/>
                    <input type="file" name="fileArquivoComplementar" class="form-control-file" id="fileArquivoComplementar"  tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-4 col-lg-3 col-xl-3">
                <div class="form-group">
                    <label id="lblPublico" class="infraLabelObrigatorio">
                        Tipo de Documento: <?= tooltipAjuda($strMsgTooltipTipoDocumento) ?>
                    </label><br/>
                    <select name="tipoDocumentoComplementar" class="form-control infraSelect" id="tipoDocumentoComplementar" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                        <option value=""></option>
                        <?
                            if (is_array($arrMdPetRelTpProcSerieDTO) && count($arrMdPetRelTpProcSerieDTO) > 0) {

                                foreach ($arrMdPetRelTpProcSerieDTO as $item) {

                                    $serieDTO = new SerieDTO();
                                    $serieDTO->retTodos();
                                    $serieDTO->setNumIdSerie($item->getNumIdSerie());
                                    $serieDTO = $serieRN->consultarRN0644($serieDTO);
                                    echo '<option value="'.$item->getNumIdSerie().'">'.$serieDTO->getStrNome().'</option>';

                                }
                            }
                        ?>
                    </select>
                </div>
            </div>

            <div class="col-sm-12 col-md-8 col-lg-8 col-xl-9">
                <div class="form-group">
                    <label id="lblPublico" class="infraLabelObrigatorio">
                        Complemento do Tipo de Documento: <?= tooltipAjuda($strMsgTooltipComplementoTipoDocumento) ?>
                    </label><br/>
                    <input type="text" name="complementoComplementar" class="form-control infraText" id="complementoComplementar" maxlength="40" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-4 col-lg-3 col-xl-3">
                <div class="form-group">
                    <? if ($isUsuarioExternoPodeIndicarNivelAcesso == 'S'): ?>
                        <label id="lblPublico" class="infraLabelObrigatorio">
                            Nível de Acesso: <?= tooltipAjuda($strMsgTooltipNivelAcesso) ?>
                        </label><br/>
                        <select class="infraSelect form-control" id="nivelAcesso3" name="nivelAcesso3" onchange="selectNivelAcesso('nivelAcesso3', 'hipoteseLegal3')" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                            <?= $strItensSelNivelAcesso ?>
                        </select>
                    <? else: ?>
                        <label id="lblPublico" class="infraLabelObrigatorio">
                            Nível de Acesso: <?= tooltipAjuda($strMsgTooltipNivelAcessoPadraoPreDefinido) ?>
                        </label><br/>
                        <select class="infraSelect form-control" disabled tabindex="-1">
                            <option value=""><?= $strNomeNivelAcessoPadrao ?></option>
                        </select>
                        <input type="hidden" value="<?= $nivelAcessoPadrao ?>" id="nivelAcesso3" name="nivelAcesso3" tabindex="-1"/>
                    <? endif ?>
                </div>
            </div>
            <div class="col-sm-12 col-md-8 col-lg-8 col-xl-9">

                <div id="divhipoteseLegal3" style="display: <?= ($isNivelAcessoPadrao == 'S' && $nivelAcessoPadrao == ProtocoloRN::$NA_RESTRITO) ? 'block' : 'none' ?>">
                <? if ($isConfigHipoteseLegal && $isNivelAcessoPadrao != 'S'): ?>

                    <div class="form-group">
                        <label id="lblPublico" class="infraLabelObrigatorio">
                            Hipótese Legal: <?= tooltipAjuda($strMsgTooltipHipoteseLegal) ?>
                        </label><br/>

                        <select class="form-control infraSelect" id="hipoteseLegal3" name="hipoteseLegal3" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                            <option value=""></option>
                            <?
                                if ($isConfigHipoteseLegal && is_array($arrHipoteseLegal) && count($arrHipoteseLegal) > 0) {
                                    foreach ($arrHipoteseLegal as $itemObj) {
                                        echo '<option value="'.$itemObj->getNumIdHipoteseLegal().'">'.$itemObj->getStrNome().' ('.$itemObj->getStrBaseLegal().')</option>';
                                    }
                                }
                            ?>
                        </select>
                    </div>

                <? elseif ($isConfigHipoteseLegal && $isNivelAcessoPadrao == 'S' && $nivelAcessoPadrao == "1"): ?>

                    <div class="form-group">
                        <label id="lblPublico" class="infraLabelObrigatorio">
                            Hipótese Legal: <?= tooltipAjuda($strMsgTooltipHipoteseLegalPadraoPreDefinido) ?>
                        </label><br/>
                        <select class="form-control infraSelect" disabled tabindex="-1">
                            <option value=""><?= $strHipoteseLegalPadrao ?></option>
                        </select>
                        <input type="hidden" name="hipoteseLegal3" id="hipoteseLegal3" value="<?= $idHipoteseLegalPadrao ?>" tabindex="-1"/>
                    </div>

                <? endif ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-4 col-lg-3 col-xl-3">
                <div class="form-group">
                    <label id="lblPublico" class="infraLabelObrigatorio">
                        Formato:
                        <?= tooltipAjuda($strMsgTooltipFormato) ?>
                    </label><br/>
                    <div class="form-check form-check-inline mr-1">
                        <input class="form-check-input infraRadio" type="radio" style="position: absolute" name="formatoDocumentoComplementar" id="rdNato3_1" value="nato" onclick="selecionarFormatoNatoDigitalComplementar()" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                        <label class="form-check-label" for="rdNato3_1">Nato-digital</label>
                    </div>
                    <div class="form-check form-check-inline mr-0">
                        <input class="form-check-input infraRadio" type="radio" style="position: absolute" name="formatoDocumentoComplementar" id="rdDigitalizado3_2" value="digitalizado" onclick="selecionarFormatoDigitalizadoComplementar()">
                        <label class="form-check-label" for="rdDigitalizado3_2">Digitalizado</label>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-8 col-lg-8 col-xl-9">
                <div class="form-group">
                    <div id="camposDigitalizadoComplementar" style="display: none;">
                        <label class="infraLabelObrigatorio">Conferência com o documento digitalizado:</label><br/>
                        <div class="input-group">
                            <select class="form-control infraSelect" id="TipoConferenciaComplementar" name="TipoConferenciaComplementar" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                <option value=""></option>
                                <?
                                    foreach ($arrTipoConferencia as $tipoConferencia) {
                                        echo "<option value='".$tipoConferencia->getNumIdTipoConferencia()."'>".$tipoConferencia->getStrDescricao()."</option>";
                                    }
                                ?>
                            </select>
                            <div class="input-group-append">
                                <input type="button" class="infraButton" value="Adicionar" onclick="validarUploadArquivo('3')" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                            </div>
                        </div>
                    </div>

                    <div id="camposDigitalizadoComplementarBotao">
                        <input type="button" class="infraButton mt-3" value="Adicionar" onclick="validarUploadArquivo('3')" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table id="tbDocumentoComplementar" name="tbDocumentoComplementar" class="infraTable" style="width:100%;">
                        <tr>
                            <th class="infraTh" style="width:25%;">Nome do Arquivo</th>
                            <th class="infraTh" style="width:80px;" align="center">Data</th>
                            <th class="infraTh" style="width:80px;" align="center">Tamanho</th>
                            <th class="infraTh" style="width:25%;" align="center">Documento</th>
                            <th class="infraTh" style="width:120px;" align="center">Nível de Acesso</th>

                            <!--  colunas nao exibidas na tela, usadas apenas para guardar valor na grid (note que estao com display:none) -->
                            <th class="infraTh" style="display: none;">Hipótese Legal</th>
                            <th class="infraTh" style="display: none;">Formato</th>
                            <th class="infraTh" style="display: none;">Tipo de Conferência</th>
                            <th class="infraTh" style="display: none;">Nome Upload servidor</th>
                            <th class="infraTh" style="display: none;">ID Tipo de Documento</th>
                            <th class="infraTh" style="display: none;">Complemento</th>
                            <th class="infraTh" style="width: 120px;" align="center">Formato</th>

                            <!-- Coluna de ações (Baixar, remover) da grid -->
                            <th align="center" class="infraTh" style="width:50px;">Ações</th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <? } ?>
    </form>
    <!-- ================================== FIM DOCUMENTOS COMPLEMENTARES  =============================================== -->

</fieldset>

<!-- =========================== -->
<!--  FIM BLOCO / ÁREA DOCUMENTOS -->
<!-- =========================== -->
