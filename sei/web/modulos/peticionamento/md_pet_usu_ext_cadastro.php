<?
/**
 * ANATEL
 *
 * 23/06/2016 - criado por marcelo.bezerra@cast.com.br - CAST
 *
 * ========================================================================================================
 * Página principal do cadastro de peticionamento, ela invoca páginas auxiliares (via require) contendo:
 *
 *  - variaveis e consultas de inicializacao da pagina
 *  - switch case controlador de ações principais da página
 *  - funções JavaScript
 *  - área / bloco de documentos
 * ===========================================================================================================
 */

try {

    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    //////////////////////////////////////////////////////////////////////////////
    InfraDebug::getInstance()->setBolLigado(false);
    InfraDebug::getInstance()->setBolDebugInfra(false);
    InfraDebug::getInstance()->limpar();
    //////////////////////////////////////////////////////////////////////////////

    SessaoSEIExterna::getInstance()->validarLink();
    SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);
    SessaoSEIExterna::getInstance()->removerAtributo('docPrincipalConteudoHTML');

    //=====================================================
    //INICIO - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
    //=====================================================

    require_once('md_pet_usu_ext_cadastro_inicializacao.php');

    //=====================================================
    //FIM - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
    //=====================================================

    //inclusao de script com o controle das ações principais da tela
    require_once('md_pet_usu_ext_cadastro_acoes.php');

} catch (Exception $e) {
    PaginaSEIExterna::getInstance()->processarExcecao($e);
}

$hashAnexo = "";
$idAnexo = "";

$oragao = '';
$uf = '';
$cidadeHidden = '';

if (isset($_GET['id_orgao'])) {
    $oragao = $_GET['id_orgao'];
}
if (isset($_GET['id_uf'])) {
    $uf = $_GET['id_uf'];
}
if (isset($_GET['id_cidade'])) {
    $cidadeHidden = $_GET['id_cidade'];

    $objCidadeDTO = new CidadeDTO();
    $objCidadeDTO->setNumIdCidade($cidadeHidden);
    $objCidadeDTO->retStrNome();
    $objCidadeRN = new CidadeRN();
    $objCidadeDTO = $objCidadeRN->consultarRN0409($objCidadeDTO);
    $cidadeHidden = $objCidadeDTO->getStrNome();
}


//combo disabled
$disabled = '';
//Option vazio
//Recuperando Oragao

$selectOrgao = MdPetTipoProcessoINT::montarSelectOrgaoTpProcesso($_GET['id_tipo_procedimento'], $oragao);
if (($cidadeHidden != "" || $uf != "") || ($oragao != "" && count($selectOrgao[0]) > 1 && $cidadeHidden != "")) {
    $selectCidade = MdPetTipoProcessoINT::montarSelectCidade($_GET['id_tipo_procedimento'], $oragao, $uf, $cidadeHidden);
}

if (count($selectOrgao[0]) < 2) {
    $disabled = "disabled";
    $unicoOrgao = $selectOrgao[0];
    $orgaoDuplo = false;
}

if (($uf != "" && $orgaoDuplo == false) || ($oragao != "" && count($selectOrgao[0]) > 0)) {
    $selectUf = MdPetTipoProcessoINT::montarSelectUf($_GET['id_tipo_procedimento'], $oragao, $uf, $cidadeHidden);
} else {
    $selectUf = MdPetTipoProcessoINT::montarSelectUf($_GET['id_tipo_procedimento']);
}

$hiddUF = "";
//Caso o usuário tenha selecionado na uf e retorne somente uma, esconder combo
$qtdSelectUf = isset($selectUf[0]) ? count($selectUf[0]) : 0;
if ($qtdSelectUf < 2) {

    $hiddUF = "display:none";

}
if ($selectCidade == null) {

    $selectCidade = MdPetTipoProcessoINT::montarSelectCidade($_GET['id_tipo_procedimento'], $oragao, $selectUf[0][0], $cidadeHidden);
}
//Caso retorne somente uma cidade
$cidadeHidde = "display:none";
$qtdSelectCidade = isset($selectCidade[0]) ? count($selectCidade[0]) : 0;
if ($qtdSelectCidade == 1) {
    $cidadeHidde = "display:none";

} else if ($qtdSelectCidade > 1) {
    $cidadeHidde = "";
}

$objInfraParametroDTO = new InfraParametroDTO();
$objMdPetParametroRN = new MdPetParametroRN();
$objInfraParametroDTO->retTodos();
$objInfraParametroDTO->setStrNome('SEI_HABILITAR_HIPOTESE_LEGAL');
$objInfraParametroDTO = $objMdPetParametroRN->consultar($objInfraParametroDTO);
$valorParametroHipoteseLegal = $objInfraParametroDTO->getStrValor();

PaginaSEIExterna::getInstance()->montarDocType();
PaginaSEIExterna::getInstance()->abrirHtml();
PaginaSEIExterna::getInstance()->abrirHead();
PaginaSEIExterna::getInstance()->montarMeta();
PaginaSEIExterna::getInstance()->montarTitle(':: ' . PaginaSEIExterna::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaSEIExterna::getInstance()->montarStyle();
PaginaSEIExterna::getInstance()->abrirStyle();
$objEditorRN = new EditorRN();
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
PaginaSEIExterna::getInstance()->fecharJavaScript();
require_once('md_pet_usu_ext_cadastro_css.php');
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
<!--  tela terá multiplos forms por conta dos uploads, logo nao fará sentido ter um form geral -->
<?
PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaSEIExterna::getInstance()->abrirAreaDados('auto');
?>
<div class="infraAreaGlobal">
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <p>
                <label class="infraLabelObrigatorio">Tipo de Processo:</label>
                <label><?= $txtTipoProcessoEscolhido ?></label>
            </p>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <fieldset id="orientacoesTipoProcesso" class="infraFieldset sizeFieldset form-control">
                <legend class="infraLegend">&nbsp; Orientações sobre o Tipo de Processo &nbsp;</legend>
                <label>
                    <?= $txtOrientacoes ?>
                </label>
            </fieldset>
        </div>
    </div>
    <fieldset id="formularioPeticionamento" class="infraFieldset sizeFieldset form-control">
        <legend class="infraLegend px-3">Formulário de Peticionamento</legend>
        <div class="row">
            <div class="col-sm-12 col-md-8 col-lg-6 col-xl-6">
                <div class="form-group">
                    <label class="infraLabelObrigatorio">Especificação (resumo limitado a 50 caracteres):</label>
                    <input type="text" class="infraText form-control" name="txtEspecificacao" id="txtEspecificacao" maxlength="50"  tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>" autofocus/>
                </div>
            </div>
        </div>

        <?php

            if (count($selectOrgao[0]) < 2) {
                $hiddenOrgao    = "display:none;";
                $unicoOrgao     = $selectOrgao[0];
                $orgaoDuplo     = false;
            }
            if (count($selectOrgao[0]) > 1 && $_GET['id_orgao'] == "") {
                $cidadeHidde    = "display:none;";
            }
        ?>

        <!-- Settar unidade no lugar da idCidade -->
        <!-- Validação para quando deve aparecer as 3 combos -->
        <? if ($arrUnidadeUFDTO != null && count($arrUnidadeUFDTO) > 1): ?>
            <!-- Orgão -->
            <?php $display = ($hiddenOrgao == "display:none;") ? "float: inherit;" : "float: inherit; padding-right:10px;"; ?>

            <div class="row" style="<?php echo $display ?>">
                <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                    <div class="form-group">
                        <label id="lblPublico" style="<?php echo $hiddenOrgao; ?>" class="infraLabelObrigatorio">
                            Órgão:
                            <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" name="ajuda" <?= PaginaSEI::montarTitleTooltip("Neste campo somente são listados os Órgãos em que é possível abrir Processo Novo para o Tipo de Processo selecionado. \n \n Selecione abaixo o Órgão no qual deseja que este Processo seja aberto. ", 'Ajuda') ?> alt="Ajuda" class="infraImgModulo"/>
                        </label>
                        <select onchange="pesquisarUF(this)" style="<?php echo $hiddenOrgao; ?>" id="selOrgao" name="selOrgao" class="infraSelect form-control" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">
                            <?php
                                if (($orgaoDuplo && empty($oragao)) || ($orgaoDuplo == false && count($selectOrgao[0]) > 1)){
                                    echo '<option value=""></option>';
                                }

                                $idOrgao    = $selectOrgao[0];
                                $orgao      = $selectOrgao[1];

                                for ($i = 0; $i < count($idOrgao); $i++) {
                                    echo '<option value="' . $idOrgao[$i] . '">' . $orgao[$i] . '</option>';
                                }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-2 col-lg-2 col-xl-2" style="<?php echo $hiddUF; ?>" id="ufHidden">
                    <!-- UF -->
                    <div class="form-group">
                        <label id="lblPublico" class="infraLabelObrigatorio">
                            UF:
                            <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" name="ajuda" <?= PaginaSEI::montarTitleTooltip("Neste campo somente são listadas as UFs em que é possível abrir Processo Novo para o Tipo de Processo selecionado. \n \n Selecione abaixo a UF na qual deseja que este Processo seja aberto. ", 'Ajuda') ?> alt="Ajuda" class="infraImgModulo"/>
                        </label><br/>

                        <select onchange="pesquisarCidade(this)" id="selUF" name="selUF" class="infraSelect form-control" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>" >
                            <?php
                                if ($_GET['id_uf'] != null || $_GET['id_orgao'] != null) {
                                    $qtdSelectUf = isset($selectUf[0]) ? count($selectUf[0]) : 0;

                                    if ($qtdSelectUf > 1) {
                                        echo '<option value=""></option>';
                                    }
                                    $idUf   = $selectUf[0];
                                    $uf     = $selectUf[1];

                                    for ($i = 0; $i < $qtdSelectUf; $i++) {
                                        echo '<option value="' . $idUf[$i] . '">' . $uf[$i] . '</option>';
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="col-sm-12 col-md-6 col-lg-5 col-xl-4" style="<?php echo $cidadeHidde; ?>" id="cidadeHidden">
                    <div class="form-group">
                        <!-- Cidade -->
                        <label id="lblPublico" class="infraLabelObrigatorio">
                            Cidade:
                            <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" name="ajuda" <?= PaginaSEI::montarTitleTooltip("Neste campo somente são listadas as Cidades em que é possível abrir Processo Novo para o Tipo de Processo selecionado. \n \n Selecione abaixo a Cidade na qual deseja que este Processo seja aberto.", 'Ajuda') ?> alt="Ajuda" class="infraImgModulo"/>
                        </label><br/>
                        <select onchange="pesquisarFinal(this)" id="selUFAberturaProcesso" name="selUFAberturaProcesso" class="infraSelect form-control" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>" >
                            <?php
                                $qtdSelectCidade = isset($selectCidade[0]) ? count($selectCidade[0]) : 0;
                                if ($qtdSelectCidade > 1) {
                                    echo '<option value=""></option>';
                                }

                                $unidade    = $selectCidade[0];
                                $cidade     = $selectCidade[1];

                                for ($i = 0; $i < $qtdSelectCidade; $i++) {
                                    echo '<option value="' . $unidade[$i] . '">' . $cidade[$i] . '</option>';
                                }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <input type="hidden" name="hdnIdUfTelaAnterior" id="hdnIdUfTelaAnterior" value="<?php echo $_GET['id_uf'] ?>" tabindex="-1"/>
            <input type="hidden" name="hdnIdCidadeTelaAnterior" id="hdnIdCidadeTelaAnterior" value="<?php echo $cidadeHidden; ?>" tabindex="-1"/>
            <input type="hidden" name="hdnIdOrgaoTelaAnterior" id="hdnIdOrgaoTelaAnterior" value="<?php echo $_GET['id_orgao'] ?>" tabindex="-1"/>
            <input type="hidden" name="hdnIdUfUnico" id="hdnIdUfUnico" value="" tabindex="-1"/>

        <? endif ?>

        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <? if ($objTipoProcDTO->getStrSinIIProprioUsuarioExterno() == 'S') { ?>

                    <!--  CASO 1 -->
                    <div class="form-group infraDivRadio" id="divOptPublico">
		                <span id="spnPublico0">
			                <label id="lblPublico" class="infraLabelObrigatorio">
                                Interessado:
                                <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipInteressadoProprioUsuarioExterno, 'Ajuda') ?> alt="Ajuda" class="infraImg"/>
                            </label>
		                </span>
                        <label> <?= SessaoSEIExterna::getInstance()->getStrNomeUsuarioExterno() ?> </label>
                    </div>

                <? } else if ($objTipoProcDTO->getStrSinIIIndicacaoDiretaCpfCnpj() == 'S') { ?>

                    <!--  CASO 2 -->
                    <div class="form-group" aria-checked="true">
                        <label id="lblPublico" class="infraLabelObrigatorio">
                            Interessados:
                            <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipInteressadoInformandoCPFeCNPJ, 'Ajuda') ?> alt="Ajuda" class="infraImgModulo"/>
                        </label><br/>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input infraRadio" style="position: absolute" type="radio" name="tipoPessoa" id="optTipoPessoaFisica" onclick="selecionarPF()" value="pf" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                            <label class="form-check-label" for="optTipoPessoaFisica">Pessoa Física</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input infraRadio" style="position: absolute" type="radio" name="tipoPessoa" id="optTipoPessoaJuridica" onclick="selecionarPJ()" value="pj">
                            <label class="form-check-label" for="optTipoPessoaJuridica">Pessoa Jurídica</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-4 col-lx-4" id="divSel1" style="display: none;">
                            <div class="form-group">
                                <label id="descTipoPessoa" class="infraLabelObrigatorio"> </label><br/>
                                <div class="input-group mb-3 divClean">
                                    <input type="text" class="form-control infraText" id="txtCPF" name="txtCPF" onkeyup="return alterandoCPF(this, event)" style="display:none;" maxlength="14" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"/>
                                    <input type="text" class="form-control infraText" id="txtCNPJ" name="txtCNPJ" onkeyup="return alterandoCNPJ(this, event)" style="display:none;" maxlength="18" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"/>
                                    <input type="button" id="btValidarCPFCNPJ" class="infraText" value="Validar" onclick="abrirCadastroInteressado()" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-8 col-lx-4" id="divSel2" style="display: none;">
                            <div class="form-group">
                                <label id="descNomePessoa" class="infraLabelObrigatorio"> </label><br/>
                                <div class="input-group mb-3 divClean">
                                    <input type="text" class="infraText form-control" name="txtNomeRazaoSocial" id="txtNomeRazaoSocial" readonly="readonly" style="display: none;" disabled tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">
                                    <input type="button" id="btAdicionarInteressado" class="infraText" value="Adicionar" style="display: none;" onclick="adicionarInteressadoValido()" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-lx-12" id="divSel0">
                            <div class="form-group">
                                <table id="tbInteressadosIndicados" class="infraTable" width="100%" style="margin-top: 0px" summary="Lista de Interessados">
                                    <tr>
                                        <th class="infraTh" style="display: none;"> ID Contato</th>
                                        <th class="infraTh" width="15%" id="tdDescTipoPessoaSelecao"> Natureza</th>
                                        <th class="infraTh" width="15%" id="tdDescTipoPessoa"> CPF/CNPJ</th>
                                        <th class="infraTh" id="tdDescNomePessoa"> Nome/Razão Social</th>
                                        <th align="center" class="infraTh" style="width:100px;"> Ações</th>
                                    </tr>
                                    <tbody></tbody>
                                </table>
                            </div>

                            <input type="hidden" name="txtNomeRazaoSocialTratadoHTML" id="txtNomeRazaoSocialTratadoHTML" value="" tabindex="-1"/>
                            <input type="hidden" name="hdnIdInteressadoCadastrado" id="hdnIdInteressadoCadastrado" value="" tabindex="-1"/>
                            <input type="hidden" name="hdnListaInteressadosIndicados" id="hdnListaInteressadosIndicados" value="" tabindex="-1"/>
                            <input type="hidden" name="hdnCustomizado" id="hdnCustomizado" value="" tabindex="-1"/>
                            <input type="hidden" name="hdnUsuarioCadastro" id="hdnUsuarioCadastro" value="" tabindex="-1"/>
                            <input type="hidden" name="hdnUsuarioLogado" id="hdnUsuarioLogado" value="<? echo SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno(); ?>" tabindex="-1"/>
                            <input type="hidden" name="hdnIdEdicao" id="hdnIdEdicao" value="" tabindex="-1"/>

                        </div>

                    </div>

                <? } else if ($objTipoProcDTO->getStrSinIIIndicacaoDiretaContato() == 'S') {

                    $strLinkAjaxInteressado     = SessaoSEIExterna::getInstance()->assinarLink('modulos/peticionamento/controlador_ajax_externo.php?acao_ajax_externo=md_pet_contato_auto_completar_contexto_pesquisa&id_orgao_acesso_externo=0');
                    $strLinkInteressadosSelecao = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?id_tipo_processo_peticionamento=' . $objTipoProcDTO->getNumIdTipoProcessoPeticionamento() . '&acao=md_pet_contato_selecionar&tipo_selecao=2&id_object=objLupaInteressados');

                ?>

                    <!--  CASO 3 -->
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-lx-12" id="divSel0">
                            <div class="form-group">
                                <div id="divOptPublico" class="infraDivRadio">
                                    <span id="spnPublico0">
                                        <label id="lblPublico" class="infraLabelObrigatorio">
                                            Interessado:
                                            <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipInteressadoDigitadoNomeExistente, 'Ajuda') ?> alt="Ajuda" class="infraImg"/>
                                        </label><br/>
                                    </span>
                                </div>

                                <div style="clear: both;"></div>

                                <input type="text" name="txtInteressado" class="infraText" id="txtInteressado" maxlength="250" value="" autocomplete="off" style="width: 50%"/> <br/>

                                <div style="margin-top: 5px;">

                                    <select name="selInteressados" size="4" multiple="multiple" class="infraSelect form-control" id="selInteressados" style="float: left; width: 75%; margin-right: 5px;" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">
                                        <?
                                            if (is_array($arrContatosInteressados) && count($arrContatosInteressados) > 0){
                                                foreach ($arrContatosInteressados as $itemObj){
                                                    echo '<option value="'.$itemObj->getNumIdContato().'">'.$itemObj->getStrNome().'</option>';
                                                }
                                            }
                                        ?>
                                    </select>

                                    <img id="imgLupaTipoDocumento" onclick="carregarComponenteLupaInteressados('S');" src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/consultar.svg" alt="Localizar Interessados" title="Localizar Interessados" class="infraImg">
                                    <img id="imgExcluirTipoDocumento" onclick="carregarComponenteLupaInteressados('R');" src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg" alt="Remover Interessados" title="Remover Interessados" class="infraImg">
                                    <br/>
                                    <img id="imgAssuntosAcima" onclick="objLupaInteressados.moverAcima();" src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/mover_acima.svg" alt="Mover Acima Assunto Selecionado" title="Mover Acima Selecionado" class="infraImg" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                    <img id="imgAssuntosAbaixo" onclick="objLupaInteressados.moverAbaixo();" src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/mover_abaixo.svg" alt="Mover Abaixo Assunto Selecionado" title="Mover Abaixo Selecionado" class="infraImg" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                </div>
                            </div>
                        </div>
                    </div>

                <? } ?>
            </div>
        </div>
    </fieldset>

    <!-- =========================== -->
    <!--  INICIO FIELDSET DOCUMENTOS -->
    <!-- =========================== -->
    <? require_once('md_pet_usu_ext_cadastro_bloco_documentos.php'); ?>
    <!-- =========================== -->
    <!--  FIM FIELDSET DOCUMENTOS -->
    <!-- =========================== -->

    <input type="hidden" id="hdnInteressados" name="hdnInteressados" value="<?= $_POST['hdnInteressados'] ?>"/>
    <input type="hidden" id="hdnIdInteressado" name="hdnIdInteressado" class="infraText" value=""/>

    <input type="hidden" id="hdnArquivosPermitidos" name="hdnArquivosPermitidos"
           value='<?php echo isset($jsonExtPermitidas) && (!is_null($jsonExtPermitidas)) ? $jsonExtPermitidas : '' ?>'/>
    <input type="hidden" id="hdnArquivosPermitidosEssencialComplementar"
           name="hdnArquivosPermitidosEssencialComplementar"
           value='<?php echo isset($jsonExtEssencialComplementarPermitidas) && (!is_null($jsonExtEssencialComplementarPermitidas)) ? $jsonExtEssencialComplementarPermitidas : '' ?>'/>

    <input type="hidden" id="hdnAnexos" name="hdnAnexos" value="<?= $_POST['hdnAnexos'] ?>"/>
    <input type="hidden" id="hdnAnexosInicial" name="hdnAnexosInicial" value="<?= $_POST['hdnAnexosInicial'] ?>"/>

    <input type="hidden" id="hdnNomeArquivoDownload" name="hdnNomeArquivoDownload" value=""/>
    <input type="hidden" id="hdnNomeArquivoDownloadReal" name="hdnNomeArquivoDownloadReal" value=""/>
    <input type="hidden" id="hdnIdOrgaoDisabled" name="hdnIdOrgaoDisabled" value="<?php echo $disabled ?>"/>
    <input type="hidden" id="hdnIdOrgaoUnico" name="hdnIdOrgaoUnico" value="<?php echo $unicoOrgao[0] ?>"/>
</div>
<?
PaginaSEIExterna::getInstance()->montarBarraComandosInferior($arrComandos);
PaginaSEIExterna::getInstance()->montarAreaDebug();
PaginaSEIExterna::getInstance()->fecharAreaDados();
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();

//inclusao de conteudos JavaScript adicionais
require_once('md_pet_usu_ext_cadastro_js.php');
?>

<input type="hidden" id="hdnIdOrgao" name="hdnIdOrgao" value=""/>
<input type="hidden" id="hdnIdUf" name="hdnIdUf" value=""/>
<input type="hidden" id="hdnIdCidade" name="hdnIdCidade" value=""/>
<input type="hidden" id="hdnTpProcesso" name="hdnTpProcesso" value="<?php echo $_GET['id_tipo_procedimento'] ?>"/>
<input type="hidden" id="id_tipo_procedimento" name="id_tipo_procedimento" value="<?php echo $_GET['id_tipo_procedimento'] ?>"/>
