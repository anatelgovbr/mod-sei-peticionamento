<?
/**
 * ANATEL
 *
 * 15/06/2016 - criado por marcelo.bezerra - CAST
 *
 */

try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    SessaoSEI::getInstance()->validarLink();

    PaginaSEI::getInstance()->prepararSelecao('menu_peticionamento_usuario_externo_selecionar');

    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    switch ($_GET['acao']) {
        case 'md_pet_menu_usu_ext_excluir':

            try {
                $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
                $arrObjMdPetMenuUsuarioExternoDTO = array();

                for ($i = 0; $i < count($arrStrIds); $i++) {
                    $objMdPetMenuUsuarioExternoDTO = new MdPetMenuUsuarioExternoDTO();
                    $objMdPetMenuUsuarioExternoDTO->setNumIdMenuPeticionamentoUsuarioExterno($arrStrIds[$i]);
                    $arrObjMdPetMenuUsuarioExternoDTO[] = $objMdPetMenuUsuarioExternoDTO;
                }

                $objMdPetMenuUsuarioExternoRN = new MdPetMenuUsuarioExternoRN();
                $objMdPetMenuUsuarioExternoRN->excluir($arrObjMdPetMenuUsuarioExternoDTO);
                PaginaSEI::getInstance()->adicionarMensagem('Operação realizada com sucesso.');

            } catch (Exception $e) {
                PaginaSEI::getInstance()->processarExcecao($e);
            }
            header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?id_menu_peticionamento_usuario_externo=' . $_GET['id_menu_peticionamento_usuario_externo'] . '&acao=' . $_GET['acao_origem'] . '&acao_origem=' . $_GET['acao']));
            die;

        case 'md_pet_menu_usu_ext_desativar':
            try {
                $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
                $arrObjMdPetMenuUsuarioExternoDTO = array();
                for ($i = 0; $i < count($arrStrIds); $i++) {
                    $objMdPetMenuUsuarioExternoDTO = new MdPetMenuUsuarioExternoDTO();
                    $objMdPetMenuUsuarioExternoDTO->setNumIdMenuPeticionamentoUsuarioExterno($arrStrIds[$i]);
                    $objMdPetMenuUsuarioExternoDTO->setStrSinAtivo('N');
                    $arrObjMdPetMenuUsuarioExternoDTO[] = $objMdPetMenuUsuarioExternoDTO;
                }
                $objMdPetMenuUsuarioExternoRN = new MdPetMenuUsuarioExternoRN();
                $objMdPetMenuUsuarioExternoRN->desativar($arrObjMdPetMenuUsuarioExternoDTO);
            } catch (Exception $e) {
                PaginaSEI::getInstance()->processarExcecao($e);
            }
            header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?id_menu_peticionamento_usuario_externo=' . $_GET['id_menu_peticionamento_usuario_externo'] . '&acao=' . $_GET['acao_origem'] . '&acao_origem=' . $_GET['acao']));
            die;

        case 'md_pet_menu_usu_ext_reativar':

            $strTitulo = 'Reativar Menus';

            if ($_GET['acao_confirmada'] == 'sim') {

                try {
                    $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
                    $arrObjMdPetMenuUsuarioExternoDTO = array();
                    for ($i = 0; $i < count($arrStrIds); $i++) {
                        $objMdPetMenuUsuarioExternoDTO = new MdPetMenuUsuarioExternoDTO();
                        $objMdPetMenuUsuarioExternoDTO->setNumIdMenuPeticionamentoUsuarioExterno($arrStrIds[$i]);
                        $objMdPetMenuUsuarioExternoDTO->setStrSinAtivo('S');
                        $arrObjMdPetMenuUsuarioExternoDTO[] = $objMdPetMenuUsuarioExternoDTO;
                    }
                    $objMdPetMenuUsuarioExternoRN = new MdPetMenuUsuarioExternoRN();
                    $objMdPetMenuUsuarioExternoRN->reativar($arrObjMdPetMenuUsuarioExternoDTO);
                    PaginaSEI::getInstance()->adicionarMensagem('Operação realizada com sucesso.');
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
                header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao_origem'] . '&acao_origem=' . $_GET['acao'] . '&id_menu_peticionamento_usuario_externo=' . $objMdPetMenuUsuarioExternoDTO->getNumIdMenuPeticionamentoUsuarioExterno()));
                die;
            }
            break;

        case 'menu_peticionamento_usuario_externo_selecionar':

            $strTitulo = PaginaSEI::getInstance()->getTituloSelecao('Selecionar Menu', 'Selecionar Menus');

            //Se cadastrou alguem
            if ($_GET['acao_origem'] == 'md_pet_menu_usu_ext_cadastrar') {
                if (isset($_GET['id_menu_peticionamento_usuario_externo'])) {
                    PaginaSEI::getInstance()->adicionarSelecionado($_GET['id_menu_peticionamento_usuario_externo']);
                }
            }
            break;

        case 'md_pet_menu_usu_ext_listar':

            $strTitulo = 'Cadastro de Menus';
            //continue;
            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }

    $arrComandos = array();
    $arrComandos[] = '<button type="button" accesskey="p" id="btnPesquisar" value="Pesquisar" onclick="pesquisar();" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';

    //TODO: Marcelo, qual é a utilidade dessa funcionalidade de Transportar seleção neste tela?
    if ($_GET['acao'] == 'menu_peticionamento_usuario_externo_selecionar') {
        $arrComandos[] = '<button type="button" accesskey="T" id="btnTransportarSelecao" value="Transportar" onclick="infraTransportarSelecao();" class="infraButton"><span class="infraTeclaAtalho">T</span>ransportar</button>';
    }

    $bolAcaoCadastrar = SessaoSEI::getInstance()->verificarPermissao('md_pet_menu_usu_ext_cadastrar');

    if ($bolAcaoCadastrar) {
        $arrComandos[] = '<button type="button" accesskey="n" id="btnNovo" value="Nova" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_menu_usu_ext_cadastrar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'])) . '\'" class="infraButton"><span class="infraTeclaAtalho">N</span>ovo</button>';
    }

    $objMdPetMenuUsuarioExternoDTO = new MdPetMenuUsuarioExternoDTO();
    $objMdPetMenuUsuarioExternoDTO->retNumIdMenuPeticionamentoUsuarioExterno();
    $objMdPetMenuUsuarioExternoDTO->retStrNome();
    $objMdPetMenuUsuarioExternoDTO->retStrTipo();
    $objMdPetMenuUsuarioExternoDTO->retStrSinAtivo();

    if (isset($_POST['txtNome'])) {
        $objMdPetMenuUsuarioExternoDTO->setStrNome('%' . $_POST['txtNome'] . '%', InfraDTO::$OPER_LIKE);
    }

    if (isset($_POST['selTipo']) && $_POST['selTipo'] != "") {
        $objMdPetMenuUsuarioExternoDTO->setStrTipo($_POST['selTipo']);
    }

    PaginaSEI::getInstance()->prepararOrdenacao($objMdPetMenuUsuarioExternoDTO, 'Nome', InfraDTO::$TIPO_ORDENACAO_ASC);
    PaginaSEI::getInstance()->prepararPaginacao($objMdPetMenuUsuarioExternoDTO, 200);

    if (isset($_POST['id_menu_peticionamento_usuario_externo'])) {
        $objMdPetMenuUsuarioExternoDTO->setNumIdTipoControleLitigioso($_POST['id_menu_peticionamento_usuario_externo']);
    }

    $objMdPetMenuUsuarioExternoRN = new MdPetMenuUsuarioExternoRN();

    $arrObjMdPetMenuUsuarioExternoDTO = $objMdPetMenuUsuarioExternoRN->listar($objMdPetMenuUsuarioExternoDTO);

    PaginaSEI::getInstance()->processarPaginacao($objMdPetMenuUsuarioExternoDTO);
    $numRegistros = count($arrObjMdPetMenuUsuarioExternoDTO);

    if ($numRegistros > 0) {

        $bolCheck = false;

        if ($_GET['acao'] == 'menu_peticionamento_usuario_externo_selecionar') {
            $bolAcaoReativar = false;
            $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pet_menu_usu_ext_consultar');
            $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('md_pet_menu_usu_ext_alterar');
            $bolAcaoImprimir = false;
            $bolAcaoExcluir = false;
            $bolAcaoDesativar = false;
            $bolCheck = true;
        } else if ($_GET['acao'] == 'md_pet_menu_usu_ext_reativar') {
            $bolAcaoReativar = SessaoSEI::getInstance()->verificarPermissao('md_pet_menu_usu_ext_reativar');
            $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pet_menu_usu_ext_consultar');
            $bolAcaoAlterar = false;
            $bolAcaoImprimir = true;
            $bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('md_pet_menu_usu_ext_excluir');
            $bolAcaoDesativar = false;
        } else {
            $bolAcaoReativar = false;
            $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pet_menu_usu_ext_consultar');
            $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('md_pet_menu_usu_ext_alterar');
            $bolAcaoImprimir = true;
            $bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('md_pet_menu_usu_ext_excluir');
            $bolAcaoDesativar = SessaoSEI::getInstance()->verificarPermissao('md_pet_menu_usu_ext_desativar');
        }

        //TODO: Marcelo, se não vai ter o botão de Desativar em lote, melhor retirar todo este bloco de código.
        if ($bolAcaoDesativar) {
            $bolCheck = true;
            $strLinkDesativar = SessaoSEI::getInstance()->assinarLink('controlador.php?id_menu_peticionamento_usuario_externo=' . $_GET['id_menu_peticionamento_usuario_externo'] . '&acao=md_pet_menu_usu_ext_desativar&acao_origem=' . $_GET['acao']);
        }

        $strLinkReativar = SessaoSEI::getInstance()->assinarLink('controlador.php?id_menu_peticionamento_usuario_externo=' . $_GET['id_menu_peticionamento_usuario_externo'] . '&acao=md_pet_menu_usu_ext_reativar&acao_origem=' . $_GET['acao'] . '&acao_confirmada=sim');

        //TODO: Marcelo, se não vai ter o botão de Excluir em lote, melhor retirar todo este bloco de código.
        if ($bolAcaoExcluir) {
            $bolCheck = true;
            $strLinkExcluir = SessaoSEI::getInstance()->assinarLink('controlador.php?id_menu_peticionamento_usuario_externo=' . $_GET['id_menu_peticionamento_usuario_externo'] . '&acao=md_pet_menu_usu_ext_excluir&acao_origem=' . $_GET['acao']);
        }

        if ($bolAcaoImprimir) {
            $arrComandos[] = '<button type="button" accesskey="i" id="btnImprimir" value="Imprimir" onclick="infraImprimirTabela();" class="infraButton"><span class="infraTeclaAtalho">I</span>mprimir</button>';
        }

        $strResultado = '';

        if ($_GET['acao'] != 'md_pet_menu_usu_ext_reativar') {
            $strSumarioTabela = 'Tabela de Menus.';
            $strCaptionTabela = 'Menus';
        } else {
            $strSumarioTabela = 'Tabela de Menus Inativos.';
            $strCaptionTabela = 'Menus Inativos';
        }

        $strResultado .= '<table width="99%" class="infraTable" summary="' . $strSumarioTabela . '">' . "\n";
        $strResultado .= '<caption class="infraCaption">' . PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela, $numRegistros) . '</caption>';
        $strResultado .= '<tr>';
        if ($bolCheck) {
            $strResultado .= '<th class="infraTh" width="1%">' . PaginaSEI::getInstance()->getThCheck() . '</th>' . "\n";
        }
        $strResultado .= '<th class="infraTh thLeft" width="50%">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetMenuUsuarioExternoDTO, 'Nome do Menu', 'Nome', $arrObjMdPetMenuUsuarioExternoDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh thLeft">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetMenuUsuarioExternoDTO, 'Tipo de Menu', 'Tipo', $arrObjMdPetMenuUsuarioExternoDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh" width="15%">Ações</th>' . "\n";
        $strResultado .= '</tr>' . "\n";
        $strCssTr = '';
        for ($i = 0; $i < $numRegistros; $i++) {

            if (isset($_GET['id_menu_peticionamento_usuario_externo']) && $_GET['id_menu_peticionamento_usuario_externo'] == $arrObjMdPetMenuUsuarioExternoDTO[$i]->getNumIdMenuPeticionamentoUsuarioExterno()) {
                $strCssTr = '<tr class="infraTrAcessada">';
            } else if ($arrObjMdPetMenuUsuarioExternoDTO[$i]->getStrSinAtivo() == 'S') {
                $strCssTr = ($strCssTr == '<tr class="infraTrClara">') ? '<tr class="infraTrEscura">' : '<tr class="infraTrClara">';
            } else {
                $strCssTr = '<tr class="trVermelha">';
            }

            $strResultado .= $strCssTr;

            if ($bolCheck) {
                $strResultado .= '<td valign="top"  style="vertical-align:middle">' . PaginaSEI::getInstance()->getTrCheck($i, $arrObjMdPetMenuUsuarioExternoDTO[$i]->getNumIdMenuPeticionamentoUsuarioExterno(), $arrObjMdPetMenuUsuarioExternoDTO[$i]->getStrNome()) . '</td>';
            }
            $strResultado .= '<td>' . PaginaSEI::tratarHTML($arrObjMdPetMenuUsuarioExternoDTO[$i]->getStrNome()) . '</td>';

            $strDescricaoTipo = "";

            if ($arrObjMdPetMenuUsuarioExternoDTO[$i]->getStrTipo() == MdPetMenuUsuarioExternoRN::$TP_EXTERNO) {
                $strDescricaoTipo = "Link Externo";
            } else if ($arrObjMdPetMenuUsuarioExternoDTO[$i]->getStrTipo() == MdPetMenuUsuarioExternoRN::$TP_CONTEUDO_HTML) {
                $strDescricaoTipo = "Conteúdo HTML";
            }

            $strResultado .= '<td>' . PaginaSEI::tratarHTML($strDescricaoTipo) . '</td>';

            $strResultado .= '<td align="center">';

            $strResultado .= PaginaSEI::getInstance()->getAcaoTransportarItem($i, $arrObjMdPetMenuUsuarioExternoDTO[$i]->getNumIdMenuPeticionamentoUsuarioExterno());

            if ($bolAcaoConsultar) {
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?id_menu_peticionamento_usuario_externo=' . $_GET['id_menu_peticionamento_usuario_externo'] . '&acao=md_pet_menu_usu_ext_consultar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&id_menu_peticionamento_usuario_externo=' . $arrObjMdPetMenuUsuarioExternoDTO[$i]->getNumIdMenuPeticionamentoUsuarioExterno())) . '" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getIconeConsultar() . '" title="Consultar Menu" alt="Consultar Menu" class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoAlterar) {
                $idMenu = $arrObjMdPetMenuUsuarioExternoDTO[$i]->getNumIdMenuPeticionamentoUsuarioExterno();
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?id_menu_peticionamento_usuario_externo=' . $idMenu . '&acao=md_pet_menu_usu_ext_alterar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&id_menu_peticionamento_usuario_externo=' . $arrObjMdPetMenuUsuarioExternoDTO[$i]->getNumIdMenuPeticionamentoUsuarioExterno())) . '" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getIconeAlterar() . '" title="Alterar Menu" alt="Alterar Menu" class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoDesativar || $bolAcaoReativar || $bolAcaoExcluir) {
                $strId = $arrObjMdPetMenuUsuarioExternoDTO[$i]->getNumIdMenuPeticionamentoUsuarioExterno();
                $strDescricao = "'" . PaginaSEI::getInstance()->formatarParametrosJavaScript(PaginaSEI::tratarHTML($arrObjMdPetMenuUsuarioExternoDTO[$i]->getStrNome(), true)) . "'";
            }

            if ($bolAcaoDesativar && $arrObjMdPetMenuUsuarioExternoDTO[$i]->getStrSinAtivo() == 'S') {
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="acaoDesativar(\'' . $strId . '\', ' . $strDescricao . ');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/desativar.svg" title="Desativar Menu" alt="Desativar Menu" class="infraImg" /></a>&nbsp;';
            } else {
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="acaoReativar(\'' . $strId . '\', ' . $strDescricao . ');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/reativar.svg" title="Reativar Menu" alt="Reativar Menu" class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoExcluir) {
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="acaoExcluir(\'' . $strId . '\', ' . $strDescricao . ');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/excluir.svg" title="Excluir Menu" alt="Excluir Menu" class="infraImg" /></a>&nbsp;';
            }

            $strResultado .= '</td></tr>' . "\n";
        }
        $strResultado .= '</table>';
    }
    if ($_GET['acao'] == 'menu_peticionamento_usuario_externo_selecionar') {
        $arrComandos[] = '<button type="button" accesskey="c" id="btnFecharSelecao" value="Fechar" onclick="window.close();" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
    } else {
        $arrComandos[] = '<button type="button" accesskey="c" id="btnFechar" value="Fechar" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?id_menu_peticionamento_usuario_externo=' . $_GET['id_menu_peticionamento_usuario_externo'] . '&acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'])) . '\'" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
    }

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
require_once 'md_pet_menu_usu_ext_lista_css.php';
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
$strNome = $_POST['txtNome'];
$strTipo = $_POST['selTipo'];;
?>
    <form id="frmLista" method="post"
          action="<?= PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?id_menu_peticionamento_usuario_externo=' . $_GET['id_menu_peticionamento_usuario_externo'] . '&acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'])) ?>">

        <? PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos); ?>

        <div class="infraAreaDados" id="divInfraAreaDados">
            <div class="row">
                <!--  Nome do Menu -->
                <div class="col-sm-6 col-md-5 col-lg-5 col-xl-4">
                    <div class="form-group">
                        <label id="lblNome" for="txtNome" class="infraLabelOpcional"         value=>Nome do Menu:</label>
                        <input type="text" name="txtNome" id="txtNome" maxlength="30"
                    <?= PaginaSEI::tratarHTML($strNome) ?> class="infraText form-control"/>
                    </div>
                </div>
                <!--  Tipo do Menu -->
                <div class="col-sm-6 col-md-5 col-lg-5 col-xl-4">
                    <div class="form-group">
                        <label id="lblTipo" for="selTipo" class="infraLabelOpcional">Tipo de Menu:</label>

                        <select onchange="pesquisar()" id="selTipo" name="selTipo" class="infraSelect form-control">
                            <option value="" <? if ($strTipo == "") echo " selected='selected' "; ?> > Todos
                            </option>
                            <option value="E" <? if ($strTipo == "E") echo " selected='selected' "; ?> >Link Externo
                            </option>
                            <option value="H" <? if ($strTipo == "H") echo " selected='selected' "; ?> >Conteúdo HTML
                            </option>
                        </select>
                        <input type="submit" style="visibility: hidden;"/>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <?
                    PaginaSEI::getInstance()->montarAreaTabela($strResultado, $numRegistros);
                    PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
                    ?>
                </div>
            </div>
        </div>


    </form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
require_once('md_pet_menu_usu_ext_lista_js.php');
?>
