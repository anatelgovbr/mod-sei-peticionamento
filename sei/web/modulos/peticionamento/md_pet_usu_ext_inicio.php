<?
/**
 * ANATEL
 *
 * 23/06/2016 - criado por marcelo.bezerra@cast.com.br - CAST
 *
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


    //=====================================================
    //INICIO - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
    //=====================================================

    //tipo de processo escolhido
    $txtTipoProcessoEscolhido = "nome do tipo de processo escolhido";

    //texto de orientacoes
    $objMdPetTpProcessoOrientacoesDTO2 = new MdPetTpProcessoOrientacoesDTO();
    $objMdPetTpProcessoOrientacoesDTO2->setNumIdTipoProcessoOrientacoesPet(MdPetTpProcessoOrientacoesRN::$ID_FIXO_TP_PROCESSO_ORIENTACOES);
    $objMdPetTpProcessoOrientacoesDTO2->retTodos();

    $objMdPetTpProcessoOrientacoesRN = new MdPetTpProcessoOrientacoesRN();
    $objLista = $objMdPetTpProcessoOrientacoesRN->listar($objMdPetTpProcessoOrientacoesDTO2);
    $alterar = count($objLista) > 0;

    $txtOrientacoes = '';
    $unidadesFiltradas = array();
    $id_conjunto_estilos = null;
    if ($alterar) {
        $txtOrientacoes = $objLista[0]->getStrOrientacoesGerais();
        $id_conjunto_estilos = $objLista[0]->getNumIdConjuntoEstilos();
    }


    //Recuperando Oragao
    $selectOrgao = MdPetTipoProcessoINT::montarSelectOrgaoTpProcesso();
    $classe = '';

    $hidden = '';
    $orgao = '';


    if (count($selectOrgao[0]) > 1) {
        $hidden = "";
    } else {
        $hiddenOrgao = "display:none;";
        $orgaoUnico = "U";
        $idOrgaoUnico = $selectOrgao[0][0];
        $selectUf = MdPetTipoProcessoINT::montarSelectUf(null, $idOrgaoUnico);
        $qtdSelectUf = isset($selectUf[0]) ? count($selectUf[0]) : 0;
        if ($qtdSelectUf > 1) {
            $hiddenUF = "";
        } else {
            $hiddenUF = "display:none;";
            $idUfUnica = $selectUf[0][0];

            $selectCidade = MdPetTipoProcessoINT::montarSelectCidade(null, $idOrgaoUnico, $idUfUnica);
            $qtdSelectCidade = isset($selectCidade[0]) ? count($selectCidade[0]) : 0;
            if ($qtdSelectCidade > 1) {
                $hiddenCidade = "";
            } else {
                $hiddenCidade = "display:none;";
            }

        }

    }

    //Escondendo so Campos somente com 1 Elemento
    if ($qtdSelectUf > 1) {
        $hiddenCidade = "display:none;";
    } else {
        $hiddenCidade = "display:none;";
    }

    if ($qtdSelectUf > 1) {
        $hiddenUF = "";
    } else {
        $hiddenUF = "display:none;";
    }

    //$hiddenUF


//Validação Cidade Unica
    $objTipoProcessoDTO = new MdPetTipoProcessoDTO();
    $objTipoProcessoDTO->retNumIdTipoProcessoPeticionamento();
    $objTipoProcessoDTO->retStrNomeProcesso();
    $objTipoProcessoDTO->retNumIdProcedimento();
    $objTipoProcessoDTO->retStrOrientacoes();
    $objTipoProcessoDTO->setStrSinAtivo('S');
    $objTipoProcessoDTO->setOrdStrNomeProcesso(InfraDTO::$TIPO_ORDENACAO_ASC);

    $objTipoProcedimentoRN = new MdPetTipoProcessoRN();
    $arrObjTipoProcedimentoFiltroDTO = $objTipoProcedimentoRN->listar($objTipoProcessoDTO);
    $arrObjTipoProcedimentoRestricaoDTO = InfraArray::converterArrInfraDTO($arrObjTipoProcedimentoFiltroDTO, 'IdProcedimento');
    //Tipo Processo Peticionamento
    $arrObjTipoProcessoPeticionamentoDTO = InfraArray::converterArrInfraDTO($arrObjTipoProcedimentoFiltroDTO, 'IdTipoProcessoPeticionamento');


    $arrTipoProcessoOrgaoCidade = array();
    $arrIdTipoProcesso = array();
    foreach ($arrObjTipoProcedimentoFiltroDTO as $key => $tpProc) {
        if (!in_array($tpProc->getNumIdTipoProcessoPeticionamento(), $arrIdTipoProcesso)) {
            array_push($arrIdTipoProcesso, $tpProc->getNumIdTipoProcessoPeticionamento());
        }
    }

    $objMdPetRelTpProcessoUnidRN = new MdPetRelTpProcessoUnidRN();
    $objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
    $objMdPetRelTpProcessoUnidDTO->setNumIdTipoProcessoPeticionamento($arrIdTipoProcesso, InfraDTO::$OPER_IN);
    $objMdPetRelTpProcessoUnidDTO->retNumIdUnidade();
    $objMdPetRelTpProcessoUnidDTO->retNumIdOrgaoUnidade();
    $objMdPetRelTpProcessoUnidDTO->retNumIdCidadeContato();
    $objMdPetRelTpProcessoUnidDTO->retNumIdTipoProcessoPeticionamento();
    $objMdPetRelTpProcessoUnidDTO->retStrStaTipoUnidade();
    $arrobjMdPetRelTpProcessoUnidDTO = $objMdPetRelTpProcessoUnidRN->listar($objMdPetRelTpProcessoUnidDTO);

    foreach ($arrobjMdPetRelTpProcessoUnidDTO as $key => $objDTO) {
        //print_r($objDTO->getNumIdTipoProcessoPeticionamento()); die;
        if (!key_exists($objDTO->getNumIdTipoProcessoPeticionamento(), $arrTipoProcessoOrgaoCidade)) {
            $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()] = array();
        }
        if (!key_exists($objDTO->getNumIdOrgaoUnidade(), $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()])) {
            $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()][$objDTO->getNumIdOrgaoUnidade()] = array();
        }

        if (!key_exists($objDTO->getNumIdCidadeContato(), $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()][$objDTO->getNumIdOrgaoUnidade()])) {
            $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()][$objDTO->getNumIdOrgaoUnidade()][$objDTO->getNumIdCidadeContato()] = 1;
        } else {
            $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()][$objDTO->getNumIdOrgaoUnidade()][$objDTO->getNumIdCidadeContato()] = $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()][$objDTO->getNumIdOrgaoUnidade()][$objDTO->getNumIdCidadeContato()] + 1;
        }


    }
    $arrIdsTpProcesso = array_keys($arrTipoProcessoOrgaoCidade);
    //verificando se existe algum tipo de processo com divergencia de orgao e cidade iguais
    if ($arrTipoProcessoOrgaoCidade) {
        $tipoProcessoDivergencia = false;
        foreach ($arrTipoProcessoOrgaoCidade as $key => $dados) {
            foreach ($dados as $cidade) {
                foreach ($cidade as $qnt) {
                    if ($qnt > 1) {
                        foreach ($arrObjTipoProcedimentoFiltroDTO as $chaveTpProc => $tpProc) {
                            if ($tpProc->getNumIdTipoProcessoPeticionamento() == $key) {
                                unset($arrObjTipoProcedimentoFiltroDTO[$chaveTpProc]);
                                $chaveRemover = array_search($key, $arrIdsTpProcesso);
                                unset($arrIdsTpProcesso[$chaveRemover]);
                            }
                        }
                    }
                }
            }

        }
    }
//Fim validação cidade Unica

//Restrição
    $arrRestricao = array();
    foreach ($arrObjTipoProcedimentoFiltroDTO as $key => $tpProc) {

        //Verifica se existe restrição para o tipo de processo
        $objTipoProcedRestricaoRN = new TipoProcedRestricaoRN();
        $objTipoProcedRestricaoDTO = new TipoProcedRestricaoDTO();
        $objTipoProcedRestricaoDTO->retNumIdOrgao();
        $objTipoProcedRestricaoDTO->retNumIdUnidade();
        $objTipoProcedRestricaoDTO->setNumIdTipoProcedimento($tpProc->getNumIdProcedimento());
        $arrObjTipoProcedRestricaoDTO = $objTipoProcedRestricaoRN->listar($objTipoProcedRestricaoDTO);

        $idOrgaoRestricao = InfraArray::converterArrInfraDTO($arrObjTipoProcedRestricaoDTO, 'IdOrgao');
        $idUnidadeRestricao = InfraArray::converterArrInfraDTO($arrObjTipoProcedRestricaoDTO, 'IdUnidade');

        $objMdPetRelTpProcessoUnidRN = new MdPetRelTpProcessoUnidRN();
        $objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
        $objMdPetRelTpProcessoUnidDTO->retTodos();
        $objMdPetRelTpProcessoUnidDTO->retStrsiglaUnidade();
        $objMdPetRelTpProcessoUnidDTO->retStrStaTipoUnidade();
        $objMdPetRelTpProcessoUnidDTO->retStrdescricaoUnidade();
        $objMdPetRelTpProcessoUnidDTO->retNumIdUnidade();
        $objMdPetRelTpProcessoUnidDTO->retNumIdOrgaoUnidade();
        $objMdPetRelTpProcessoUnidDTO->retStrDescricaoOrgao();
        $objMdPetRelTpProcessoUnidDTO->retStrSiglaOrgao();
        $objMdPetRelTpProcessoUnidDTO->retNumIdCidadeContato();
        $objMdPetRelTpProcessoUnidDTO->setNumIdTipoProcessoPeticionamento($tpProc->getNumIdTipoProcessoPeticionamento());
        $arrobjMdPetRelTpProcessoUnidDTO = $objMdPetRelTpProcessoUnidRN->listar($objMdPetRelTpProcessoUnidDTO);


        foreach ($arrobjMdPetRelTpProcessoUnidDTO as $objDTO) {

            //Verifica se tem alguma unidade ou ?rg?o diferente dos restritos
            if (($idOrgaoRestricao && $idOrgaoRestricao[0] != null) && !in_array($objDTO->getNumIdOrgaoUnidade(), $idOrgaoRestricao)) {
                $arrRestricao [] = $tpProc->getNumIdProcedimento();
            }
            if (($idUnidadeRestricao && $idUnidadeRestricao[0] != null) && !in_array($objDTO->getNumIdUnidade(), $idUnidadeRestricao)) {
                $arrRestricao [] = $tpProc->getNumIdProcedimento();
            }

        }

    }

    //Fim restrição

    $objTipoProcessoDTO = new MdPetTipoProcessoDTO();
    $objTipoProcessoDTO->setNumIdTipoProcessoPeticionamento($arrIdsTpProcesso, infraDTO::$OPER_IN);
    $objTipoProcessoDTO->retNumIdTipoProcessoPeticionamento();
    $objTipoProcessoDTO->retStrNomeProcesso();
    $objTipoProcessoDTO->retNumIdProcedimento();

    if (count($arrRestricao)) {
        $objTipoProcessoDTO->setNumIdProcedimento($arrRestricao, infraDTO::$OPER_NOT_IN);
    }
    $objTipoProcessoDTO->retStrOrientacoes();
    $objTipoProcessoDTO->setStrSinAtivo('S');
    $objTipoProcessoDTO->setOrdStrNomeProcesso(InfraDTO::$TIPO_ORDENACAO_ASC);
    $objTipoProcedimentoRN = new MdPetTipoProcessoRN();
    $arrObjTipoProcedimentoFiltroDTO = $objTipoProcedimentoRN->listar($objTipoProcessoDTO);

    foreach ($arrObjTipoProcedimentoFiltroDTO as $chave => $objTipoProcedimentoFiltroDTO) {
        $objNivelAcessoPermitidoDTO = new NivelAcessoPermitidoDTO();
        $objNivelAcessoPermitidoDTO->retStrStaNivelAcesso();
        $objNivelAcessoPermitidoDTO->setNumIdTipoProcedimento($objTipoProcedimentoFiltroDTO->getNumIdProcedimento());
        $objNivelAcessoPermitidoRN = new NivelAcessoPermitidoRN();
        $arrObjNivelAcessoPermitidoDTO = $objNivelAcessoPermitidoRN->listar($objNivelAcessoPermitidoDTO);

        $arrDadosNivelAcessoPermitido = array();
        foreach ($arrObjNivelAcessoPermitidoDTO as $ObjNivelAcessoPermitido){
            $arrDadosNivelAcessoPermitido[] = $ObjNivelAcessoPermitido->getStrStaNivelAcesso();
        }

        if(!in_array(ProtocoloRN::$NA_PUBLICO, $arrDadosNivelAcessoPermitido)){
            unset($arrObjTipoProcedimentoFiltroDTO[$chave]);
        }
    }

    $objEditorRN = new EditorRN();

    if ($_GET['iframe'] != '') {
        PaginaSEIExterna::getInstance()->abrirStyle();
        echo $objEditorRN->montarCssEditor($id_conjunto_estilos);
        PaginaSEIExterna::getInstance()->fecharStyle();
        echo $txtOrientacoes;
        die();
    }


    //=====================================================
    //FIM - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
    //=====================================================

    switch ($_GET['acao']) {

        case 'md_pet_usu_ext_iniciar':
            $strTitulo = 'Peticionamento de Processo Novo';
            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }

} catch (Exception $e) {
    PaginaSEIExterna::getInstance()->processarExcecao($e);
}

$hashAnexo = "";
$idAnexo = "";

PaginaSEIExterna::getInstance()->montarDocType();
PaginaSEIExterna::getInstance()->abrirHtml();
PaginaSEIExterna::getInstance()->abrirHead();
PaginaSEIExterna::getInstance()->montarMeta();
PaginaSEIExterna::getInstance()->montarTitle(':: ' . PaginaSEIExterna::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaSEIExterna::getInstance()->montarStyle();
PaginaSEIExterna::getInstance()->abrirStyle();
$objEditorRN = new EditorRN();
echo $objEditorRN->montarCssEditor(null);
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
PaginaSEIExterna::getInstance()->fecharJavaScript();
require_once 'md_pet_usu_ext_inicio_css.php';
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');

?>
<div class="infraAreaDados">
    <form id="frmIndisponibilidadeCadastro" method="post" onsubmit="return OnSubmitForm();"
          action="<?= PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'])) ?>">
        <?
        PaginaSEIExterna::getInstance()->abrirAreaDados('auto');
        ?>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <fieldset id="orientacoesGerais" class="infraFieldset sizeFieldset form-control">
                    <legend class="infraLegend">&nbsp; Orientações Gerais &nbsp;</legend>
                    <?
                    echo '<iframe id=ifrConteudoHTML name=ifrConteudoHTML style="height:100%;width:100%" frameborder="0" marginheight="0" marginwidth="0" src="' . SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usu_ext_iniciar&iframe=S') . '"></iframe>';
                    ?>
                </fieldset>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-8 col-lg-4 col-xl-3 pesquisaTipoNovo">
                <label class="infraLabelOpcional">Tipo do Processo:</label>
                <br/>
                <input type="text" id="txtFiltro" onkeypress="filtro()" class="infraText form-control"
                       autocomplete="off"
                       value="<? if (isset($_POST['txtFiltro'])) echo $_POST['txtFiltro']; ?>">
            </div>
            <div class="col-sm-12 col-md-4 col-lg-3 col-xl-2" style="<?php echo $hiddenOrgao ?>" id="OrgaoHidd">
                <label id="lblOrgao" for="selOrgao" class="infraLabelOpcional">
                    ?rg?o:
                    <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>"
                         name="ajuda" align="top"
                        <?= PaginaSEI::montarTitleTooltip("Por meio deste campo ? poss?vel filtrar a lista de Tipos de Processos que podem ser abertos em determinado ?rg?o.", 'Ajuda') ?>
                         alt="Ajuda" class="infraImgModulo"/>
                </label>
                <select onchange="pesquisarUF(this)" id="selOrgao" name="selOrgao"
                        class="infraSelect form-control">
                    <?php if ($hiddenOrgao != "disabled") { ?>
                        <option value="">Todos</option>
                    <?php } ?>
                    <?=
                    $idOrgao = $selectOrgao[0];
                    $orgao = $selectOrgao[1];
                    for ($i = 0; $i < count($idOrgao); $i++) {
                        echo '<option value="' . $idOrgao[$i] . '">' . $orgao[$i] . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="col-sm-12 col-md-4 col-lg-2 col-xl-2" style=" <?php echo $hiddenUF; ?> " id="UFHidd">
                <label id="lblUF" for="selUF" style="font-size:12px;" class="infraLabelOpcional">
                    UF:
                    <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>"
                         name="ajuda"
                        <?= PaginaSEI::montarTitleTooltip("Por meio deste campo ? poss?vel filtrar a lista de Tipos de Processos que podem ser abertos em determinada UF.", 'Ajuda') ?>
                         alt="Ajuda" class="infraImgModulo"/>
                </label>
                <select onchange="pesquisarCidade(this)" id="selUF" name="selUF" class="infraSelect form-control">
                    <option value="">Todos</option>
                    <?php if ($orgaoUnico == "U") { ?>
                        <?=

                        $idUf = $selectUf[0];
                        $qtdUfs = isset($selectUf[0]) ? count($selectUf[0]) : 0;
                        $uf = $selectUf[1];
                        for ($i = 0; $i < $qtdUfs; $i++) {
                            echo '<option value="' . $idUf[$i] . '">' . $uf[$i] . '</option>';
                        }


                        ?>
                    <?php } ?>
                </select>
            </div>
            <div class="col-sm-12 col-md-8 col-lg-3 col-xl-5" style="<?php echo $hiddenCidade ?>" id="cidadeHidd">
                <label id="lblCidade" for="selCidade" class="infraLabelOpcional">Cidade:
                    <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>"
                         name="ajuda" align="top"
                        <?= PaginaSEI::montarTitleTooltip("Por meio deste campo ? poss?vel filtrar a lista de Tipos de Processos que podem ser abertos em determinada Cidade.", 'Ajuda') ?>
                         alt="Ajuda" class="infraImgModulo"/></label>
                <select onchange="pesquisarFinal(this)" id="selCidade" name="selCidade"
                        class="infraSelect form-control">
                    <option value="">Todos</option>
                    <?=

                    $idCidade = $selectCidade[0];
                    $qtdCidade = isset($selectCidade[0]) ? count($selectCidade[0]) : 0;
                    $cidade = $selectCidade[1];

                    for ($i = 0; $i < $qtdCidade; $i++) {
                        echo '<option value="' . $idCidade[$i] . '">' . $cidade[$i] . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <label class="infraLabelObrigatorio" style="font-size:1.7em;">Escolha o Tipo do Processo que deseja
                    iniciar:</label>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <table class="infraTable" id="tblTipoProcedimento" style="background-color:white;"
                       summary="Tabela de Tipos de Processo">
                    <?php
                    $qtdArrObjTipoProcedimentoFiltroDTO = isset($arrObjTipoProcedimentoFiltroDTO) ? count($arrObjTipoProcedimentoFiltroDTO) : 0;
                    if ($qtdArrObjTipoProcedimentoFiltroDTO > 0) {

                        ?>
                        <? foreach ($arrObjTipoProcedimentoFiltroDTO as $itemDTO) { ?>

                            <? if ($_GET['id_tipo_procedimento'] == $itemDTO->getNumIdTipoProcessoPeticionamento()) { ?>
                                <? $classe = 'infraTrClara infraTrAcessada'; ?>
                            <? } else {
                                $classe = 'infraTrClara';
                            } ?>

                            <tr class="<? echo $classe; ?>"
                                data-desc="'<?php echo strtolower(InfraString::excluirAcentos($itemDTO->getStrNomeProcesso())); ?>'">
                                <td>
                                    <? $link = PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usu_ext_cadastrar&id_tipo_procedimento=' . $itemDTO->getNumIdTipoProcessoPeticionamento())); ?>
                                    <a href="<?= $link ?>"
                                       title="<?= $itemDTO->getStrOrientacoes() ?>"
                                       class="ancoraOpcao">
                                        <?= $itemDTO->getStrNomeProcesso() ?>
                                    </a>
                                </td>
                            </tr>
                        <? } ?>
                    <?php } ?>
                </table>
            </div>
        </div>
        <input type="hidden" id="hdnIdOrgao" name="hdnIdOrgao" value=""/>
        <input type="hidden" id="hdnIdUf" name="hdnIdUf" value=""/>
        <input type="hidden" id="hdnIdCidade" name="hdnIdCidade" value=""/>
        <input type="hidden" id="hdnIdTipoProcedimentoRetorno" name="hdnIdTipoProcedimentoRetorno"
               value="<?php echo $_GET['id_tipo_procedimento'] ?>"/>
        <input type="hidden" id="hdnIdOrgaoUnico" name="hdnIdOrgaoUnico" value="<?php echo $orgaoUnico ?>"/>
        <input type="hidden" id="hdnIdOrgaoUnicoId" name="hdnIdOrgaoUnicoId" value="<?php echo $idOrgaoUnico ?>"/>
        <input type="hidden" id="hdnInfraNroItens" name="hdnInfraNroItens" value=""/>
        <input type="hidden" id="hdnInfraItemId" name="hdnInfraItemId" value=""/>
        <input type="hidden" id="hdnInfraItensSelecionados" name="hdnInfraItensSelecionados" value=""/>
        <input type="hidden" id="hdnInfraSelecoes" name="hdnInfraSelecoes" value="Infra"/>
    </form>
</div>
<?
require_once 'md_pet_usu_ext_inicio_js.php';
PaginaSEIExterna::getInstance()->fecharAreaDados();
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
?>
