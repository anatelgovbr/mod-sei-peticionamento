<?
    /**
     * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
     *
     * 27/12/2023 - criado por sabino.colab
     *
     */

    try {
        require_once dirname(__FILE__) . '/../../SEI.php';

        session_start();

        //////////////////////////////////////////////////////////////////////////////
        //InfraDebug::getInstance()->setBolLigado(false);
        //InfraDebug::getInstance()->setBolDebugInfra(true);
        //InfraDebug::getInstance()->limpar();
        //////////////////////////////////////////////////////////////////////////////

        SessaoSEI::getInstance()->validarLink();

        PaginaSEI::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);

        $strTitulo = 'Classificacao pelos Objetivos de Desenvolvimento Sustentável da ONU';

        $objMdIaAdmObjetivoOdsDTO = new MdIaAdmObjetivoOdsDTO();
        $objMdIaAdmObjetivoOdsDTO->retNumIdMdIaAdmObjetivoOds();
        $objMdIaAdmObjetivoOdsDTO->retNumIdMdIaAdmObjetivoOds();
        $objMdIaAdmObjetivoOdsDTO->retStrIconeOds();
        $arrObjMdIaAdmObjetivoOdsDTO = (new MdIaAdmObjetivoOdsRN())->listar($objMdIaAdmObjetivoOdsDTO);

        $mdIaAdmObjetivoOdsINT = new MdIaAdmObjetivoOdsINT();
        $arrIdsObjetivosForteRelacao = $mdIaAdmObjetivoOdsINT->arrIdsObjetivosForteRelacao();
        $arrIdsMetasForteRelacao = $mdIaAdmObjetivoOdsINT->arrIdsMetasForteRelacao();

        $arrComandos = array();

    } catch (Exception $e) {
        PaginaSEI::getInstance()->processarExcecao($e);
    }

    PaginaSEI::getInstance()->montarDocType();
    PaginaSEI::getInstance()->abrirHtml();
    PaginaSEI::getInstance()->abrirHead();
    PaginaSEI::getInstance()->montarMeta();
    PaginaSEI::getInstance()->montarTitle(PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo);
    PaginaSEI::getInstance()->montarStyle();
    PaginaSEI::getInstance()->abrirStyle();
    include_once('md_pet_classificar_ods_css.php');
    PaginaSEI::getInstance()->fecharStyle();
    PaginaSEI::getInstance()->montarJavaScript();
    PaginaSEI::getInstance()->abrirJavaScript();
    PaginaSEI::getInstance()->fecharJavaScript();
    PaginaSEI::getInstance()->fecharHead();
    PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
    PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
    PaginaSEI::getInstance()->abrirAreaDados('auto');
?>
    <div class="row">
        <div class="col-12">
            <div id="multi-step-form-container" class="px-5 my-4">
                <ul class="form-stepper form-stepper-horizontal text-center mx-auto pl-0 px-5">
                    <!-- Step 1 -->
                    <li class="form-stepper-active text-center form-stepper-list" step="1" onclick="mudarStep(1)">
                        <a class="mx-2">
                            <span class="form-stepper-circle"><span>1</span></span>
                            <div class="label">Selecionar Objetivos</div>
                        </a>
                    </li>
                    <!-- Step 2 -->
                    <li class="form-stepper-unfinished text-center form-stepper-list" step="2" onclick="mudarStep(2)">
                        <a class="mx-2">
                            <span class="form-stepper-circle text-muted"><span>2</span></span>
                            <div class="label text-muted">Classificar Metas</div>
                        </a>
                    </li>
                    <!-- Step 3 -->
                    <li class="form-stepper-unfinished text-center form-stepper-list" step="3" onclick="mudarStep(3)">
                        <a class="mx-2">
                            <span class="form-stepper-circle text-muted"><span>3</span></span>
                            <div class="label text-muted">Resumo</div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="m-4 text-right">
                <button type="button" name="btnNovaClassificacao" id="btnNovaClassificacao" style="display: none" onclick="mudarStep(1)" class="infraButton">Classificar Novo Objetivo</button>
                <button type="button" name="btnProsseguir" id="btnProsseguir" style="display: none" onclick="mudarStep(3)" class="infraButton">Prosseguir</button>
                <button type="button" name="btnSalvar" id="btnSalvar" style="display: none" onclick="fecharModal()" class="infraButton">Concluir Classificação</button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">

            <div style="pt-5">
                <!-- Step 1 Content -->
                <div id="step-1">
                    
                    <div class="row" style="padding: 20px">
                        <div class="col-12">
                            <h5><strong>1. Selecionar Objetivos</strong></h5>
                            <h6 class="font-weight-normal mb-4">
                                Os Objetivos de Desenvolvimento Sustentável da ONU são um apelo global à ação para acabar com a pobreza, proteger o meio ambiente e o clima e garantir que as pessoas, em todos os lugares, possam desfrutar de paz e de prosperidade (<a
                                        href="https://brasil.un.org/pt-br/sdgs" target="_blank">https://brasil.un.org/pt-br/sdgs</a>).</br></br>
                                Acessando os ícones abaixo é possível classificar sua demanda pelos Objetivos de Desenvolvimento Sustentável da ONU.
                            </h6>
                        </div>
                    </div>
                    <div class="row" style="padding: 0px 20px">
                        <div class="col-12">
                            <h6 class="font-weight-bold d-flex align-items-center">
                                <label class="switch">
                                    <input id="btn-checkbox" type="checkbox" checked onclick="atualizarListaObjetivos(this)">
                                    <span class="slider round"></span>
                                </label>
                                Exibir apenas os Objetivos com forte relação temática com o(a) <?= SessaoSEIExterna::getInstance()->getStrSiglaOrgaoUsuarioExterno() ?>
                            </h6>
                        </div>
                    </div>
                    <div class="row" id="todos-objetivos" style="padding: 20px">
                        <?php
                            foreach($arrObjMdIaAdmObjetivoOdsDTO as $objMdIaAdmObjetivoOdsDTO) {
                                $classe = "img-desfoque";
                                $exibirObjetivo = '';
                                if( !in_array($objMdIaAdmObjetivoOdsDTO->getNumIdMdIaAdmObjetivoOds(), $arrIdsObjetivosForteRelacao) ){
                                    $exibirObjetivo = 'display:none';
                                }

                        ?>
                                <div id="<?= $objMdIaAdmObjetivoOdsDTO->getNumIdMdIaAdmObjetivoOds(); ?>" class="col-2 mb-4"
                                     style="<?= $exibirObjetivo ?>"
                                     onclick="exibirMetas(<?= $objMdIaAdmObjetivoOdsDTO->getNumIdMdIaAdmObjetivoOds(); ?>)">
                                    <a><img src='modulos/ia/imagens/Icones_Oficiais_ONU/<?= $objMdIaAdmObjetivoOdsDTO->getStrIconeOds() ?>' class="<?= $classe ?>"/></a>
                                </div>
                        <?php
                            }
                        ?>
                    </div>
                </div>

                <!-- Step 2 Content -->
                <div id="step-2" class="d-none">
                    <div class="row" style="padding: 20px">
                        <div class="col-12">
                            <h5><strong>2. Classificar Metas</strong></h5>
                            <div id="metas-selecionar" style="padding-top: 10px">
                                <h6 class="alert alert-warning">Nenhum Objetivo selecionado. Para classificar sua demanda selecione primeiro um Objetivo no passo anterior.</h6>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3 Content -->
                <div id="step-3" class="d-none">
                    <div class="row" style="padding: 20px">
                        <div class="col-12">
                            <h5><strong>3. Resumo</strong></h5>
                            <div id="metas-selecionadas" style="padding-top: 10px">
                                <h6 class="alert alert-warning">Sua demanda ainda não está contribuindo com os Objetivos de Desenvolvimento Sustentável da ONU.</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <input id="arr-objetivos-forte-relacao" type="hidden" value="<?=implode(",", $arrIdsObjetivosForteRelacao) ?>">

    <?php

        // Pegando as metas pre-selecionadas na sessao:
        $metasPreSelecionadasSessao = '';
        if(SessaoSEIExterna::getInstance()->isSetAtributo('METAS_SELECIONADAS')){
            $metasPreSelecionadasSessao = implode(',', SessaoSEIExterna::getInstance()->getAtributo('METAS_SELECIONADAS'));
        }

    ?>
    <input type='hidden' id='hdnInfraItensSelecionados' name='hdnInfraItensSelecionados' value='<?= $metasPreSelecionadasSessao; ?>'>

<?php

    require_once "md_pet_classificar_ods_js.php";

    PaginaSEI::getInstance()->fecharBody();
    PaginaSEI::getInstance()->fecharHtml();
