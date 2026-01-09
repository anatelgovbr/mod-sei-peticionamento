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

// Inicializando variaveis e controles de visibilidade dos combos de Orgao, UF e Cidade do formulario

$hashAnexo = $idAnexo = $selectOrgaoDisabled = '';
$selectOrgaoHidden = $selectUfHidden = $selectCidadeHidden = false;

$idOrgao            = isset($_GET['id_orgao']) ? $_GET['id_orgao'] : (isset($_GET['id_orgao_acesso_externo']) ? $_GET['id_orgao_acesso_externo'] : null);
$idUF               = isset($_GET['id_uf']) ? $_GET['id_uf'] : null;
$idCidade           = isset($_GET['id_cidade']) ? $_GET['id_cidade'] : null;
$idTipoProcedimento = isset($_GET['id_tipo_procedimento']) ? $_GET['id_tipo_procedimento'] : null;

if (!empty($idCidade)) {

    $objCidadeDTO = new CidadeDTO();
    $objCidadeDTO->setNumIdCidade($idCidade);
    $objCidadeDTO->retStrNome();
    $objCidadeDTO->retNumCodigoIbge();
    $objCidadeDTO = (new CidadeRN())->consultarRN0409($objCidadeDTO);
    
    $nomeCidade = $objCidadeDTO->getStrNome();
    
}

$selectOrgao    = MdPetTipoProcessoINT::montarSelectOrgaoTpProcesso($idTipoProcedimento, $idOrgao);
$selectUf       = MdPetTipoProcessoINT::montarSelectUf($idTipoProcedimento, $idOrgao);
$selectCidade   = MdPetTipoProcessoINT::montarSelectCidade($idTipoProcedimento, $idOrgao, $idUF);

// Caso haja apenas um Orgao nao precisa mostrar a combo:
if (count($selectOrgao[0]) == 1) {
	$selectOrgaoDisabled    = 'disabled';
	$selectOrgaoHidden      = true;
    $idOrgaoUnico           = $selectOrgao[0][0];
    $orgaoDuplo             = false;
}

// Caso o combo retorne somente uma opcao, esconder a combo:
$selectUfHidden     = count($selectUf[0]) == 1 ? true : false;
$selectCidadeHidden = (empty($idCidade) && empty($idUF)) ? true : false;

// Retorna opcao da Hipotese Legal
$valorParametroHipoteseLegal = (new InfraParametro(BancoSEI::getInstance()))->getValor('SEI_HABILITAR_HIPOTESE_LEGAL');

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
PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaSEIExterna::getInstance()->abrirAreaDados('auto');
?>

<!--  tela terá multiplos forms por conta dos uploads, logo nao fará sentido ter um form geral -->
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
                <label><?= $txtOrientacoes ?></label>
            </fieldset>
        </div>
    </div>
    <fieldset id="formularioPeticionamento" class="infraFieldset sizeFieldset form-control">
        <legend class="infraLegend px-3">Formulário de Peticionamento</legend>
        <div class="row">
            <div class="col-sm-12 col-md-8 col-lg-6 col-xl-6">
                <div class="form-group">
                    <label class="infraLabelObrigatorio">Especificação (resumo limitado a 100 caracteres):</label>
                    <input type="text" class="infraText form-control" name="txtEspecificacao" id="txtEspecificacao" maxlength="100"  tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>" autofocus/>
                </div>
            </div>
        </div>

        <!-- Settar unidade no lugar da idCidade -->
        <!-- Validação para quando deve aparecer as 3 combos -->
        <? if ($arrUnidadeUFDTO != null && count($arrUnidadeUFDTO) > 1): ?>
            <!-- Orgão -->
            <?php $displayMode = $selectOrgaoHidden ? 'float: inherit;' : 'float: inherit; padding-right:10px;'; ?>

            <div class="row" style="<?= $displayMode ?>">
                <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                    <div class="form-group">
                        <label id="lblPublico" style="<?= $selectOrgaoHidden ? 'display:none' : '' ?>" class="infraLabelObrigatorio">
                            Órgão:
                            <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" name="ajuda" <?= PaginaSEI::montarTitleTooltip("Neste campo somente são listados os Órgãos em que é possível abrir Processo Novo para o Tipo de Processo selecionado. \n \n Selecione abaixo o Órgão no qual deseja que este Processo seja aberto. ", 'Ajuda') ?> alt="Ajuda" class="infraImgModulo"/>
                        </label>
                        
                        <select onchange="pesquisarUF(this)" style="<?= $selectOrgaoHidden ? 'display:none' : '' ?>" id="selOrgao" name="selOrgao" class="infraSelect form-control" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">
					        <?php
              
						        if (($orgaoDuplo && empty($idOrgao)) || (!$orgaoDuplo && count($selectOrgao[0]) > 1)){
							        echo '<option value=""></option>';
						        }
						
						        $orgaoId    = $selectOrgao[0];
						        $orgaoNome  = $selectOrgao[1];
						
						        for ($i = 0; $i < count($orgaoId); $i++) {
							        $selectedOrgao = !empty($idOrgao) && $orgaoId[$i] == $idOrgao ? 'selected="selected"' : '';
							        echo '<option value="' . $orgaoId[$i] . '" ' . $selectedOrgao . '>' . $orgaoNome[$i] . '</option>';
						        }
						        
					        ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-sm-12 col-md-2 col-lg-2 col-xl-2" style="<?= $selectUfHidden ? 'display:none' : '' ?>" id="ufHidden">
                    <div class="form-group">
                        <label id="lblPublico" class="infraLabelObrigatorio">
                            UF:
                            <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" name="ajuda" <?= PaginaSEI::montarTitleTooltip("Neste campo somente são listadas as UFs em que é possível abrir Processo Novo para o Tipo de Processo selecionado. \n \n Selecione abaixo a UF na qual deseja que este Processo seja aberto. ", 'Ajuda') ?> alt="Ajuda" class="infraImgModulo"/>
                        </label><br/>

                        <select onchange="pesquisarCidade(this)" id="selUF" name="selUF" class="infraSelect form-control" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>" >
                            <? if(count($selectUf[0]) == 1): ?>
                                <option value="<?= $selectUf[0][0] ?>" selected="selected"><?= $selectUf[1][0] ?></option>
                            <? elseif(count($selectUf[0]) > 1): ?>
                                <option value=""></option>
	                            <? for($i = 0; $i < count($selectUf[0]); $i++): ?>
		                            <? $selectedUF = !empty($idUF) && $selectUf[0][$i] == $idUF ? 'selected="selected"' : '' ?>
                                    <option value="<?= $selectUf[0][$i] ?>" <?= $selectedUF ?>><?= $selectUf[1][$i] ?></option>
	                            <? endfor; ?>
                            <? endif; ?>
                        </select>
                    </div>
                </div>

                <div class="col-sm-12 col-md-6 col-lg-5 col-xl-4" style="<?= $selectCidadeHidden ? 'display:none' : '' ?>" id="cidadeHidden">
                    <div class="form-group">
                        <label id="lblPublico" class="infraLabelObrigatorio">
                            Cidade:
                            <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" name="ajuda" <?= PaginaSEI::montarTitleTooltip("Neste campo somente são listadas as Cidades em que é possível abrir Processo Novo para o Tipo de Processo selecionado. \n \n Selecione abaixo a Cidade na qual deseja que este Processo seja aberto.", 'Ajuda') ?> alt="Ajuda" class="infraImgModulo"/>
                        </label><br/>
                        
                        <select onchange="pesquisarFinal(this)" id="selUFAberturaProcesso" name="selUFAberturaProcesso" class="infraSelect form-control" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>" >
	                        <? if(count($selectCidade[0]) == 1): ?>
                                <option value="<?= $selectCidade[0][0] ?>" selected="selected"><?= $selectCidade[1][0] ?></option>
	                        <? elseif(count($selectCidade[0]) > 1): ?>
                                <option value=""></option>
		                        <? for($i = 0; $i < count($selectCidade[0]); $i++): ?>
                                    <? $selectedCidade = !empty($nomeCidade) && $selectCidade[1][$i] == $nomeCidade ? 'selected="selected"' : ''; ?>
                                    <option value="<?= $selectCidade[0][$i] ?>" <?= $selectedCidade ?>><?= $selectCidade[1][$i] ?></option>
		                        <? endfor; ?>
	                        <? endif; ?>
                        </select>
                    </div>
                </div>
            </div>

            <input type="hidden" name="hdnIdUfTelaAnterior" id="hdnIdUfTelaAnterior" value="<?= $idUF ?>" tabindex="-1"/>
            <input type="hidden" name="hdnIdCidadeTelaAnterior" id="hdnIdCidadeTelaAnterior" value="<?= $idCidade ?>" tabindex="-1"/>
            <input type="hidden" name="hdnIdOrgaoTelaAnterior" id="hdnIdOrgaoTelaAnterior" value="<?= $idOrgao ?>" tabindex="-1"/>
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
    <!--  INICIO FIELDSET OBJETIVOS DE DESENVOLVIMENTO SUSTENTÁVEL DA ONU -->
    <!-- =========================== -->
    <? require_once('md_pet_obj_desenv_sust_onu.php'); ?>
    <!-- =========================== -->
    <!--  FIM FIELDSET OBJETIVOS DE DESENVOLVIMENTO SUSTENTÁVEL DA ONU -->
    <!-- =========================== -->

    <!-- =========================== -->
    <!--  INICIO FIELDSET DOCUMENTOS -->
    <!-- =========================== -->
    <? require_once('md_pet_usu_ext_cadastro_bloco_documentos.php'); ?>
    <!-- =========================== -->
    <!--  FIM FIELDSET DOCUMENTOS -->
    <!-- =========================== -->

    <input type="hidden" id="hdnInteressados" name="hdnInteressados" value="<?= $_POST['hdnInteressados'] ?>"/>
    <input type="hidden" id="hdnIdInteressado" name="hdnIdInteressado" class="infraText" value=""/>

    <input type="hidden" id="hdnArquivosPermitidos" name="hdnArquivosPermitidos" value='<?= isset($jsonExtPermitidas) && (!is_null($jsonExtPermitidas)) ? $jsonExtPermitidas : '' ?>'/>
    <input type="hidden" id="hdnArquivosPermitidosEssencialComplementar"
           name="hdnArquivosPermitidosEssencialComplementar"
           value='<?= isset($jsonExtEssencialComplementarPermitidas) && (!is_null($jsonExtEssencialComplementarPermitidas)) ? $jsonExtEssencialComplementarPermitidas : '' ?>'/>

    <input type="hidden" id="hdnAnexos" name="hdnAnexos" value="<?= $_POST['hdnAnexos'] ?>"/>
    <input type="hidden" id="hdnAnexosInicial" name="hdnAnexosInicial" value="<?= $_POST['hdnAnexosInicial'] ?>"/>

    <input type="hidden" id="hdnNomeArquivoDownload" name="hdnNomeArquivoDownload" value=""/>
    <input type="hidden" id="hdnNomeArquivoDownloadReal" name="hdnNomeArquivoDownloadReal" value=""/>
    <input type="hidden" id="hdnIdOrgaoDisabled" name="hdnIdOrgaoDisabled" value="<?= $selectOrgaoDisabled ?>"/>
    <input type="hidden" id="hdnIdOrgaoUnico" name="hdnIdOrgaoUnico" value="<?= $idOrgaoUnico ?>"/>
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
<input type="hidden" id="hdnTpProcesso" name="hdnTpProcesso" value="<?= $idTipoProcedimento ?>"/>
<input type="hidden" id="id_tipo_procedimento" name="id_tipo_procedimento" value="<?= $idTipoProcedimento ?>"/>
