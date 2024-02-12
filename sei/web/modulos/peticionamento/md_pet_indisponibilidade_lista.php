<?
/**
 * ANATEL
 *
 * 16/02/2016 - criado por jaqueline.mendes@cast.com.br - CAST
 *
 */


try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    SessaoSEI::getInstance()->validarLink();

    PaginaSEI::getInstance()->prepararSelecao('indisponibilidade_peticionamento_selecionar');

    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    switch ($_GET['acao']) {
        case 'md_pet_indisponibilidade_excluir':
            try {
                $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
                $arrObjMdPetIndisponibilidadeDTO = array();
                for ($i = 0; $i < count($arrStrIds); $i++) {
                    $objMdPetIndisponibilidadeDTO = new MdPetIndisponibilidadeDTO();
                    $objMdPetIndisponibilidadeDTO->setNumIdIndisponibilidade($arrStrIds[$i]);
                    $arrObjMdPetIndisponibilidadeDTO[] = $objMdPetIndisponibilidadeDTO;
                }
                $objMdPetIndisponibilidadeRN = new MdPetIndisponibilidadeRN();
                $objMdPetIndisponibilidadeRN->excluir($arrObjMdPetIndisponibilidadeDTO);

            } catch (Exception $e) {
                PaginaSEI::getInstance()->processarExcecao($e);
            }
            header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao_origem'] . '&acao_origem=' . $_GET['acao']));
            die;

        case 'md_pet_indisponibilidade_desativar':
            try {
                $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
                $arrObjMdPetIndisponibilidadeDTO = array();
                for ($i = 0; $i < count($arrStrIds); $i++) {
                    $objMdPetIndisponibilidadeDTO = new MdPetIndisponibilidadeDTO();
                    $objMdPetIndisponibilidadeDTO->setNumIdIndisponibilidade($arrStrIds[$i]);
                    $objMdPetIndisponibilidadeDTO->setStrSinAtivo('N');
                    $arrObjMdPetIndisponibilidadeDTO[] = $objMdPetIndisponibilidadeDTO;
                }
                $objMdPetIndisponibilidadeRN = new MdPetIndisponibilidadeRN();
                $objMdPetIndisponibilidadeRN->desativar($arrObjMdPetIndisponibilidadeDTO);
            } catch (Exception $e) {
                PaginaSEI::getInstance()->processarExcecao($e);
            }
            header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao_origem'] . '&acao_origem=' . $_GET['acao']));
            die;

        case 'md_pet_indisponibilidade_reativar':

            $strTitulo = 'Reativar Indisponibilidade Peticionamento';

            if ($_GET['acao_confirmada'] == 'sim') {

                try {
                    $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
                    $arrObjMdPetIndisponibilidadeDTO = array();
                    $idReativado = 0;
                    for ($i = 0; $i < count($arrStrIds); $i++) {
                        $objMdPetIndisponibilidadeDTO = new MdPetIndisponibilidadeDTO();
                        $objMdPetIndisponibilidadeDTO->setNumIdIndisponibilidade($arrStrIds[$i]);
                        $objMdPetIndisponibilidadeDTO->setStrSinAtivo('S');
                        $idReativado = $arrStrIds[$i];
                        $arrObjMdPetIndisponibilidadeDTO[] = $objMdPetIndisponibilidadeDTO;
                    }
                    $objMdPetIndisponibilidadeRN = new MdPetIndisponibilidadeRN();
                    $objMdPetIndisponibilidadeRN->reativar($arrObjMdPetIndisponibilidadeDTO);
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }

                $acaoLinhaAmarela = '';

                if ($idReativado != 0) {
                    $acaoLinhaAmarela = '&id_indisponibilidade_peticionamento=' . $idReativado . PaginaSEI::getInstance()->montarAncora($idReativado);
                }

                header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao_origem'] . '&acao_origem=' . $_GET['acao'] . $acaoLinhaAmarela));
                die;
            }
            break;

        case 'indisponibilidade_peticionamento_selecionar':
            $strTitulo = PaginaSEI::getInstance()->getTituloSelecao('Selecionar Indisponibilidades', 'Selecionar Indisponibilidades');

            //Se cadastrou alguem
            if ($_GET['acao_origem'] == 'md_pet_indisponibilidade_cadastrar') {
                if (isset($_GET['id_indisponibilidade_peticionamento'])) {
                    PaginaSEI::getInstance()->adicionarSelecionado($_GET['id_indisponibilidade_peticionamento']);
                }
            }
            break;

        case 'md_pet_indisponibilidade_listar':

            $strTitulo = 'Peticionamento - Indisponibilidades do SEI';
            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }

    //TODO: Marcelo, qual é a utilidade dessa funcionalidade de Transportar seleção neste tela?
    $arrComandos = array();
    if ($_GET['acao'] == 'indisponibilidade_peticionamento_selecionar') {
        $arrComandos[] = '<button type="button" accesskey="T" id="btnTransportarSelecao" value="Transportar" onclick="infraTransportarSelecao();" class="infraButton"><span class="infraTeclaAtalho">T</span>ransportar</button>';
    }


    $objMdPetIndisponibilidadeDTO = new MdPetIndisponibilidadeDTO();
    $objMdPetIndisponibilidadeDTO->retTodos();

    if ($_POST['txtDtInicio']) {
        $objMdPetIndisponibilidadeDTO->setDthDataInicio($_POST['txtDtInicio'] . ':00');
    }

    if ($_POST['txtDtFim']) {
        $objMdPetIndisponibilidadeDTO->setDthDataFim($_POST['txtDtFim'] . ':00');
    }

    if ($_POST['selSinProrrogacao'] && $_POST['selSinProrrogacao'] != 'null') {
        $objMdPetIndisponibilidadeDTO->setStrSinProrrogacao($_POST['selSinProrrogacao']);
    }


    PaginaSEI::getInstance()->prepararOrdenacao($objMdPetIndisponibilidadeDTO, 'DataInicio', InfraDTO::$TIPO_ORDENACAO_DESC);
    PaginaSEI::getInstance()->prepararPaginacao($objMdPetIndisponibilidadeDTO, 200);


    $objMdPetIndisponibilidadeRN = new MdPetIndisponibilidadeRN();

    $arrObjMdPetIndisponibilidadeDTO = $objMdPetIndisponibilidadeRN->listar($objMdPetIndisponibilidadeDTO);

    PaginaSEI::getInstance()->processarPaginacao($objMdPetIndisponibilidadeDTO);
    $numRegistros = count($arrObjMdPetIndisponibilidadeDTO);

    $strLinkPesquisar = PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']));
    $arrComandos[] = '<button type="button" accesskey="p" id="btnPesquisar" value="Pesquisar" onclick="pesquisar();" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';

    $bolAcaoCadastrar = SessaoSEI::getInstance()->verificarPermissao('md_pet_indisponibilidade_cadastrar');
    if ($bolAcaoCadastrar) {
        $arrComandos[] = '<button type="button" accesskey="n" id="btnNovo" value="Nova" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_indisponibilidade_cadastrar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'])) . '\'" class="infraButton"><span class="infraTeclaAtalho">N</span>ova</button>';
    }

    if ($numRegistros > 0) {

        $bolCheck = false;

        if ($_GET['acao'] == 'indisponibilidade_peticionamento_selecionar') {
            $bolAcaoReativar = false;
            $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pet_indisponibilidade_consultar');
            $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('md_pet_indisponibilidade_alterar');
            $bolAcaoImprimir = false;
            $bolAcaoExcluir = false;
            $bolAcaoDesativar = false;
            $bolCheck = true;
        } else if ($_GET['acao'] == 'md_pet_indisponibilidade_reativar') {
            $bolAcaoReativar = SessaoSEI::getInstance()->verificarPermissao('md_pet_indisponibilidade_reativar');
            $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pet_indisponibilidade_consultar');
            $bolAcaoAlterar = false;
            $bolAcaoImprimir = true;
            $bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('md_pet_indisponibilidade_excluir');
            $bolAcaoDesativar = false;
        } else {
            $bolAcaoReativar = false;
            $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pet_indisponibilidade_consultar');
            $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('md_pet_indisponibilidade_alterar');
            $bolAcaoImprimir = true;
            $bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('md_pet_indisponibilidade_excluir');
            $bolAcaoDesativar = SessaoSEI::getInstance()->verificarPermissao('md_pet_indisponibilidade_desativar');
        }

        //TODO: Marcelo, se não vai ter o botão de Desativação em lote, melhor retirar todo este bloco de código.
        if ($bolAcaoDesativar) {
            $bolCheck = true;
            $strLinkDesativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_indisponibilidade_desativar&acao_origem=' . $_GET['acao']);
        }

        $strLinkReativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_indisponibilidade_reativar&acao_origem=' . $_GET['acao'] . '&acao_confirmada=sim');

        //TODO: Marcelo, se não vai ter o botão de Excluir em lote, melhor retirar todo este bloco de código.
        if ($bolAcaoExcluir) {
            $bolCheck = true;
            $strLinkExcluir = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_indisponibilidade_excluir&acao_origem=' . $_GET['acao']);
        }

        $strResultado = '';

        if ($_GET['acao'] != 'md_pet_indisponibilidade_reativar') {
            $strSumarioTabela = 'Tabela de Indisponibilidades.';
            $strCaptionTabela = 'Indisponibilidades';
        } else {
            $strSumarioTabela = 'Tabela de Indisponibilidades Inativos.';
            $strCaptionTabela = 'Indisponibilidades Inativos';
        }

        $strResultado .= '<table width="99%" class="infraTable" summary="' . $strSumarioTabela . '">' . "\n";
        $strResultado .= '<caption class="infraCaption">' . PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela, $numRegistros) . '</caption>';
        $strResultado .= '<tr>';
        if ($bolCheck) {
            $strResultado .= '<th class="infraTh" width="1%">' . PaginaSEI::getInstance()->getThCheck() . '</th>' . "\n";
        }
        $strResultado .= '<th class="infraTh text-center" width="30%">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetIndisponibilidadeDTO, 'Início', 'DataInicio', $arrObjMdPetIndisponibilidadeDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh text-center">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetIndisponibilidadeDTO, 'Fim', 'DataFim', $arrObjMdPetIndisponibilidadeDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh text-center">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetIndisponibilidadeDTO, 'Prorrogação Automática dos Prazos', 'SinProrrogacao', $arrObjMdPetIndisponibilidadeDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh" width="15%">Ações</th>' . "\n";
        $strResultado .= '</tr>' . "\n";
        $strCssTr = '';
        for ($i = 0; $i < $numRegistros; $i++) {

            if ($arrObjMdPetIndisponibilidadeDTO[$i]->getStrSinAtivo() == 'S') {
                $strCssTr = ($strCssTr == '<tr class="infraTrClara">') ? '<tr class="infraTrEscura">' : '<tr class="infraTrClara">';
            } else {
                $strCssTr = '<tr class="trVermelha">';
            }

            $strResultado .= $strCssTr;

            if ($bolCheck) {
                $strResultado .= '<td valign="top">' . PaginaSEI::getInstance()->getTrCheck($i, $arrObjMdPetIndisponibilidadeDTO[$i]->getNumIdIndisponibilidade(), $arrObjMdPetIndisponibilidadeDTO[$i]->getStrSinProrrogacao()) . '</td>';
            }

            $dataInicio = isset($arrObjMdPetIndisponibilidadeDTO[$i]) && $arrObjMdPetIndisponibilidadeDTO[$i]->getDthDataInicio() != '' ? str_replace(' ', ' - ', substr($arrObjMdPetIndisponibilidadeDTO[$i]->getDthDataInicio(), 0, -3)) : '';
            $dataFim = isset($arrObjMdPetIndisponibilidadeDTO[$i]) && $arrObjMdPetIndisponibilidadeDTO[$i]->getDthDataFim() != '' ? str_replace(' ', ' - ', substr($arrObjMdPetIndisponibilidadeDTO[$i]->getDthDataFim(), 0, -3)) : '';

            $sinProrrogacao = $arrObjMdPetIndisponibilidadeDTO[$i]->getStrSinProrrogacao() === 'S' ? 'Sim' : 'Não';

            $strResultado .= '<td class="text-center">' . $dataInicio . '</td>';
            $strResultado .= '<td class="text-center">' . $dataFim . '</td>';
            $strResultado .= '<td class="text-center">' . $sinProrrogacao . '</td>';
            $strResultado .= '<td align="center">';


            if ($bolAcaoConsultar) {
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_indisponibilidade_consultar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&id_indisponibilidade_peticionamento=' . $arrObjMdPetIndisponibilidadeDTO[$i]->getNumIdIndisponibilidade())) . '" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/consultar.svg?'.Icone::VERSAO.'" title="Consultar Indisponibilidade" alt="Consultar Indisponibilidade" class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoAlterar) {
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_indisponibilidade_alterar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&id_indisponibilidade_peticionamento=' . $arrObjMdPetIndisponibilidadeDTO[$i]->getNumIdIndisponibilidade())) . '" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/alterar.svg?'.Icone::VERSAO.'" title="Alterar Indisponibilidade" alt="Alterar Indisponibilidade" class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoDesativar || $bolAcaoReativar || $bolAcaoExcluir) {
                $strId = $arrObjMdPetIndisponibilidadeDTO[$i]->getNumIdIndisponibilidade();
                $dataIni = $arrObjMdPetIndisponibilidadeDTO[$i]->getDthDataInicio() != '' ? substr($arrObjMdPetIndisponibilidadeDTO[$i]->getDthDataInicio(), 0, -3) : '';
                $dataFim = $arrObjMdPetIndisponibilidadeDTO[$i]->getDthDataFim() != '' ? substr($arrObjMdPetIndisponibilidadeDTO[$i]->getDthDataFim(), 0, -3) : '';

                $dataIni = str_replace(' ', ' - ', $dataIni);
                $dataFim = str_replace(' ', ' - ', $dataFim);
            }

            if ($bolAcaoDesativar && $arrObjMdPetIndisponibilidadeDTO[$i]->getStrSinAtivo() == 'S') {
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="acaoDesativar(\'' . $strId . '\',\'' . $dataIni . '\',\'' . $dataFim . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/desativar.svg?'.Icone::VERSAO.'" title="Desativar Indisponibilidade" alt="Desativar Indisponibilidade" class="infraImg" /></a>&nbsp;';
            } else {
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="acaoReativar(\'' . $strId . '\',\'' . $dataIni . '\',\'' . $dataFim . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/reativar.svg?'.Icone::VERSAO.'" title="Reativar Indisponibilidade" alt="Reativar Indisponibilidade" class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoExcluir) {
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="acaoExcluir(\'' . $strId . '\',\'' . $dataIni . '\',\'' . $dataFim . '\', \'' . $arrObjMdPetIndisponibilidadeDTO[$i]->getStrSinProrrogacao() . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/excluir.svg?'.Icone::VERSAO.'" title="Excluir Indisponibilidade" alt="Excluir Indisponibilidade" class="infraImg" /></a>&nbsp;';
            }


            $strResultado .= '</td></tr>' . "\n";
        }
        $strResultado .= '</table>';
    }


    if ($bolAcaoImprimir) {
        $arrComandos[] = '<button type="button" accesskey="i" id="btnImprimir" value="Imprimir" onclick="infraImprimirTabela();" class="infraButton"><span class="infraTeclaAtalho">I</span>mprimir</button>';
    }

    if ($_GET['acao'] == 'md_pet_indisponibilidade_reativar') {
        $arrComandos[] = '<button type="button" accesskey="c" id="btnFecharSelecao" value="Fechar" onclick="window.close();" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
    } else {
        $arrComandos[] = '<button type="button" accesskey="c" name="btnFechar" id="btnFechar" value="Fechar" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSei::getInstance()->assinarLink('controlador.php?acao=procedimento_controlar&acao_origem=' . $_GET['acao'])) . '\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
    }

    //txtDtInicio
    //txtDtFim
    $strDtInicio = '';
    $strDtFim = '';

    if (isset($_POST['txtDtInicio'])) {
        $strDtInicio = $_POST['txtDtInicio'];
    }

    if (isset($_POST['txtDtFim'])) {
        $strDtFim = $_POST['txtDtFim'];
    }

    $valorComboProrrogacao = '';

    if (isset($_POST['selSinProrrogacao'])) {
        $valorComboProrrogacao = $_POST['selSinProrrogacao'];
    }

    $strItensSelSinProrrogacaoAutomatica = MdPetIndisponibilidadeINT::montarSelectProrrogacaoAutomaticaPrazos('', 'Todos', $valorComboProrrogacao);

} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: ' . PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
require_once('md_pet_indisponibilizade_lista_css.php');
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
?>


<?php
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
    <form id="frmIndisponibilidadePeticionamentoLista" method="post"
          action="<?= PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'])) ?>">

        <?php PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos); ?>


        <div class="row">
            <div class="col-sm-12 col-md-3 col-lg-3 e col-xl-2">
                <label id="lblDtInicio" for="txtDtInicio" class="infraLabelOpcional">Início:</label>
                <div class="input-group mb-3">
                    <input type="text" name="txtDtInicio" id="txtDtInicio" onchange="validDate('I');"
                           value="<?= PaginaSEI::tratarHTML($strDtInicio) ?>"
                           onkeypress="return infraMascara(this, event, '##/##/#### ##:##');"
                           class="infraText form-control"/>
                    <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/calendario.svg?<?= Icone::VERSAO ?>"
                         id="imgDtInicio"
                         title="Selecionar Data/Hora Inicial"
                         alt="Selecionar Data/Hora Inicial" class="infraImg"
                         onclick="infraCalendario('txtDtInicio',this,true,'<?= InfraData::getStrDataAtual() . ' 00:00' ?>');"/>
                </div>
            </div>
            <div class="col-sm-12 col-md-3 col-lg-3 e col-xl-2">
                <label id="lblDtFim" for="txtDtFim" class="infraLabelOpcional">Fim:</label>
                <div class="input-group mb-3">
                    <input type="text" name="txtDtFim" onchange="validDate('F');" id="txtDtFim"
                           value="<?= PaginaSEI::tratarHTML($strDtFim) ?>"
                           onchange="validDate('F');" onkeypress="return infraMascara(this, event, '##/##/#### ##:##');"
                           maxlength="16" class="infraText form-control"/>
                    <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/calendario.svg?<?= Icone::VERSAO ?>" id="imgDtFim"
                         title="Selecionar Data/Hora Final"
                         alt="Selecionar Data/Hora Final"
                         class="infraImg"
                         onclick="infraCalendario('txtDtFim',this,true,'<?= InfraData::getStrDataAtual() . ' 23:59' ?>');"/>
                </div>
            </div>
            <div class="col-sm-12 col-md-5 col-lg-5 e col-xl-4">
                <label id="lblSinProrrogacao" for="selSinProrrogacao" class="infraLabelOpcional">Prorrogação Automática
                    dos
                    Prazos:</label><br/>
                <select onchange="pesquisar()" id="selSinProrrogacao" name="selSinProrrogacao"
                        class="infraSelect form-control">
                    <?= $strItensSelSinProrrogacaoAutomatica ?>
                </select>
            </div>
        </div>
        <input type="submit" style="visibility: hidden;"/>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12 e col-xl-12">
                <?

                PaginaSEI::getInstance()->montarAreaTabela($strResultado, $numRegistros);
                ?>
            </div>
        </div>
        <?
        PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
        ?>

    </form>
<?
require_once('md_pet_indisponibilizade_lista_js.php');
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>