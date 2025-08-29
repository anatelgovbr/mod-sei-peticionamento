<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 08/12/2016 - criado por Marcelo Bezerra -CAST
 *
 * Versão do Gerador de Código: 1.39.0
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

    PaginaSEI::getInstance()->prepararSelecao('md_pet_int_tipo_resp_selecionar');

    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    switch ($_GET['acao']) {
        case 'md_pet_int_tipo_resp_excluir':
            try {
                $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
                $arrObjMdPetIntTipoRespDTO = array();
                for ($i = 0; $i < count($arrStrIds); $i++) {
                    $objMdPetIntTipoRespDTO = new MdPetIntTipoRespDTO();
                    $objMdPetIntTipoRespDTO->setNumIdMdPetIntTipoResp($arrStrIds[$i]);
                    $arrObjMdPetIntTipoRespDTO[] = $objMdPetIntTipoRespDTO;
                }
                $objMdPetIntTipoRespRN = new MdPetIntTipoRespRN();
                $objMdPetIntTipoRespRN->excluir($arrObjMdPetIntTipoRespDTO);
                PaginaSEI::getInstance()->adicionarMensagem('Operação realizada com sucesso.');
            } catch (Exception $e) {
                PaginaSEI::getInstance()->processarExcecao($e);
            }
            header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao_origem'] . '&acao_origem=' . $_GET['acao']));
            die;

        case 'md_pet_int_tipo_resp_desativar':
            try {
                $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
                $arrObjMdPetIntTipoRespDTO = array();
                for ($i = 0; $i < count($arrStrIds); $i++) {
                    $objMdPetIntTipoRespDTO = new MdPetIntTipoRespDTO();
                    $objMdPetIntTipoRespDTO->setNumIdMdPetIntTipoResp($arrStrIds[$i]);
                    $objMdPetIntTipoRespDTO->setStrSinAtivo('N');
                    $arrObjMdPetIntTipoRespDTO[] = $objMdPetIntTipoRespDTO;
                }
                $objMdPetIntTipoRespRN = new MdPetIntTipoRespRN();
                $objMdPetIntTipoRespRN->desativar($arrObjMdPetIntTipoRespDTO);
                PaginaSEI::getInstance()->adicionarMensagem('Operação realizada com sucesso.');
            } catch (Exception $e) {
                PaginaSEI::getInstance()->processarExcecao($e);
            }
            header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao_origem'] . '&acao_origem=' . $_GET['acao']));
            die;

        case 'md_pet_int_tipo_resp_reativar':
            $strTitulo = 'Reativar ';
            if ($_GET['acao_confirmada'] == 'sim') {
                try {
                    $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
                    $arrObjMdPetIntTipoRespDTO = array();
                    $idReativado = 0;
                    for ($i = 0; $i < count($arrStrIds); $i++) {
                        $objMdPetIntTipoRespDTO = new MdPetIntTipoRespDTO();
                        $objMdPetIntTipoRespDTO->setNumIdMdPetIntTipoResp($arrStrIds[$i]);
                        $objMdPetIntTipoRespDTO->setStrSinAtivo('S');
                        $idReativado = $arrStrIds[$i];
                        $arrObjMdPetIntTipoRespDTO[] = $objMdPetIntTipoRespDTO;
                    }
                    $objMdPetIntTipoRespRN = new MdPetIntTipoRespRN();
                    $objMdPetIntTipoRespRN->reativar($arrObjMdPetIntTipoRespDTO);
                    PaginaSEI::getInstance()->adicionarMensagem('Operação realizada com sucesso.');
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
                if ($idReativado != 0) {
                    $acaoLinhaAmarela = '&id_tipo_processo_peticionamento=' . $idReativado . PaginaSEI::getInstance()->montarAncora($idReativado);
                }
                header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao_origem'] . '&acao_origem=' . $_GET['acao'] . $acaoLinhaAmarela));
                die;
            }
            break;

        case 'md_pet_int_tipo_resp_selecionar':
            $strTitulo = PaginaSEI::getInstance()->getTituloSelecao('Selecionar ', 'Selecionar ');

            //Se cadastrou alguem
            if ($_GET['acao_origem'] == 'md_pet_int_tipo_resp_cadastrar') {
                if (isset($_GET['id_md_pet_int_tipo_resp'])) {
                    PaginaSEI::getInstance()->adicionarSelecionado($_GET['id_md_pet_int_tipo_resp']);
                }
            }
            break;

        case 'md_pet_int_tipo_resp_listar':
            $strTitulo = 'Tipos de Respostas';
            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }

    $arrComandos = array();
    if ($_GET['acao'] == 'md_pet_int_tipo_resp_selecionar') {
        $arrComandos[] = '<button type="button" accesskey="T" id="btnTransportarSelecao" value="Transportar" onclick="infraTransportarSelecao();" class="infraButton"><span class="infraTeclaAtalho">T</span>ransportar</button>';
    }

    if ($_GET['acao'] == 'md_pet_int_tipo_resp_listar' || $_GET['acao'] == 'md_pet_int_tipo_resp_selecionar') {
        $arrComandos[] = '<button type="submit" accesskey="P" id="sbmPesquisar" value="Pesquisar" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';
        $bolAcaoCadastrar = SessaoSEI::getInstance()->verificarPermissao('md_pet_int_tipo_resp_cadastrar');
        if ($bolAcaoCadastrar) {
            $arrComandos[] = '<button type="button" accesskey="N" id="btnNov" value="Nov" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_int_tipo_resp_cadastrar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao']) . '\'" class="infraButton"><span class="infraTeclaAtalho">N</span>ovo</button>';
        }
    }

    $objMdPetIntTipoRespDTO = new MdPetIntTipoRespDTO();
    $objMdPetIntTipoRespDTO->retTodos(true);

    //Monta Pesquisa
    if ($_POST['txtTipoResposta'] != '') {
        $objMdPetIntTipoRespDTO->setStrNome('%' . $_POST['txtTipoResposta'] . '%', InfraDTO::$OPER_LIKE);
    }

    if ($_POST['selRespostaUsuarioEx'] != '' && $_POST['selRespostaUsuarioEx'] != 'null') {
        $objMdPetIntTipoRespDTO->setStrTipoRespostaAceita($_POST['selRespostaUsuarioEx']);
    }

    if ($_POST['selPrazoExterno'] != 'null' && $_POST['selPrazoExterno'] != '') {
        $pesq = explode('-', $_POST['selPrazoExterno']);
        $objMdPetIntTipoRespDTO->setStrTipoPrazoExterno($pesq[1]);
        if ($pesq[2] != '') {
            $objMdPetIntTipoRespDTO->setStrTipoDia($pesq[2]);
        }
        $objMdPetIntTipoRespDTO->setNumValorPrazoExterno($pesq[0]);
    }

    if ($_GET['acao'] == 'md_pet_int_tipo_resp_reativar') {
        //Lista somente inativos
        $objMdPetIntTipoRespDTO->setBolExclusaoLogica(false);
        $objMdPetIntTipoRespDTO->setStrSinAtivo('N');
    }

    PaginaSEI::getInstance()->prepararOrdenacao($objMdPetIntTipoRespDTO, 'Nome', InfraDTO::$TIPO_ORDENACAO_ASC);
    PaginaSEI::getInstance()->prepararPaginacao($objMdPetIntTipoRespDTO, 200);

    $objMdPetIntTipoRespRN = new MdPetIntTipoRespRN();
    $arrObjMdPetIntTipoRespDTO = $objMdPetIntTipoRespRN->listar($objMdPetIntTipoRespDTO);

    PaginaSEI::getInstance()->processarPaginacao($objMdPetIntTipoRespDTO);
    $numRegistros = count($arrObjMdPetIntTipoRespDTO);

    if ($numRegistros > 0) {

        $bolCheck = false;
        if ($_GET['acao'] == 'md_pet_int_tipo_resp_selecionar') {
            $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pet_int_tipo_resp_consultar');
            $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('md_pet_int_tipo_resp_alterar');
            $bolAcaoImprimir = false;
            $bolAcaoExcluir = false;
            $bolAcaoDesativar = false;
            $bolCheck = true;
        } else if ($_GET['acao'] == 'md_pet_int_tipo_resp_reativar') {
            $bolAcaoReativar = SessaoSEI::getInstance()->verificarPermissao('md_pet_int_tipo_resp_reativar');
            $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pet_int_tipo_resp_consultar');
            $bolAcaoAlterar = false;
            $bolAcaoImprimir = true;
            $bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('md_pet_int_tipo_resp_excluir');
            $bolAcaoDesativar = false;
        } else {
            $bolAcaoReativar = true;
            $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pet_int_tipo_resp_consultar');
            $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('md_pet_int_tipo_resp_alterar');
            $bolAcaoImprimir = true;
            $bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('md_pet_int_tipo_resp_excluir');
            $bolAcaoDesativar = SessaoSEI::getInstance()->verificarPermissao('md_pet_int_tipo_resp_desativar');
        }

        if ($bolAcaoImprimir) {
            $bolCheck = true;
            $arrComandos[] = '<button type="button" accesskey="I" id="btnImprimir" value="Imprimir" onclick="infraImprimirTabela();" class="infraButton"><span class="infraTeclaAtalho">I</span>mprimir</button>';
        }

        if ($bolAcaoDesativar) {
            $bolCheck = true;
            $strLinkDesativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_int_tipo_resp_desativar&acao_origem=' . $_GET['acao']);
        }

        if ($bolAcaoReativar) {
            $bolCheck = true;
            $strLinkReativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_int_tipo_resp_reativar&acao_origem=' . $_GET['acao'] . '&acao_confirmada=sim');
        }

        if ($bolAcaoExcluir) {
            $bolCheck = true;
            $strLinkExcluir = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_int_tipo_resp_excluir&acao_origem=' . $_GET['acao']);
        }
        $strResultado = '';

        if ($_GET['acao'] != 'md_pet_int_tipo_resp_reativar') {
            $strSumarioTabela = 'Tabela de .';
            $strCaptionTabela = 'Tipos de Respostas';
        } else {
            $strSumarioTabela = 'Tabela de  Inativs.';
            $strCaptionTabela = ' Inativs';
        }

        $strResultado .= '<table width="99%" class="infraTable" summary="' . $strSumarioTabela . '">' . "\n";
        $strResultado .= '<caption class="infraCaption">' . PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela, $numRegistros) . '</caption>';
        $strResultado .= '<tr>';
        if ($bolCheck) {
            $strResultado .= '<th class="infraTh" width="1%">' . PaginaSEI::getInstance()->getThCheck() . '</th>' . "\n";
        }
        $strResultado .= '<th class="infraTh text-left">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetIntTipoRespDTO, 'Tipo de Resposta', 'Nome', $arrObjMdPetIntTipoRespDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh text-left">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetIntTipoRespDTO, 'Prazo Externo', 'TipoPrazoExterno', $arrObjMdPetIntTipoRespDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh text-left">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetIntTipoRespDTO, 'Resposta do Usuário Externo', 'TipoRespostaAceita', $arrObjMdPetIntTipoRespDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh">Ações</th>' . "\n";
        $strResultado .= '</tr>' . "\n";
        $strCssTr = '';
        for ($i = 0; $i < $numRegistros; $i++) {

            if ($arrObjMdPetIntTipoRespDTO[$i]->getStrSinAtivo() == 'S') {
                $strCssTr = ($strCssTr == '<tr class="infraTrClara">') ? '<tr class="infraTrEscura">' : '<tr class="infraTrClara">';
            } else {
                $strCssTr = '<tr class="trVermelha">';
            }

            if ($arrObjMdPetIntTipoRespDTO[$i]->getStrTipoPrazoExterno() == 'N') {
                $prazo = 'Não Possui Prazo Externo';
            } else {
                $prazo = $arrObjMdPetIntTipoRespDTO[$i]->getNumValorPrazoExterno();
                if ($arrObjMdPetIntTipoRespDTO[$i]->getStrTipoPrazoExterno() == 'D') {
                    $tipoDia = null;
                    if ($arrObjMdPetIntTipoRespDTO[$i]->getStrTipoDia() == 'U') {
                        $tipoDia = ' Útil';
                        if ($arrObjMdPetIntTipoRespDTO[$i]->getNumValorPrazoExterno() > 1) {
                            $tipoDia = ' Úteis';
                        }
                    }
                    $prazo .= $arrObjMdPetIntTipoRespDTO[$i]->getNumValorPrazoExterno() > 1 ? ' Dias' . $tipoDia : ' Dia' . $tipoDia;
                } else if ($arrObjMdPetIntTipoRespDTO[$i]->getStrTipoPrazoExterno() == 'M') {
                    $prazo .= $arrObjMdPetIntTipoRespDTO[$i]->getNumValorPrazoExterno() > 1 ? ' Meses' : ' Mês';
                } else if ($arrObjMdPetIntTipoRespDTO[$i]->getStrTipoPrazoExterno() == 'A') {
                    $prazo .= $arrObjMdPetIntTipoRespDTO[$i]->getNumValorPrazoExterno() > 1 ? ' Anos' : ' Ano';
                }
            }

            if ($arrObjMdPetIntTipoRespDTO[$i]->getStrTipoRespostaAceita() == 'E') {
                $resposta = 'Exige Resposta';
            } else {
                $resposta = 'Resposta Facultativa';
            }

            $strResultado .= $strCssTr;

            if ($bolCheck) {
                $nomeTransportado = '';
                $nomeTransportado = $arrObjMdPetIntTipoRespDTO[$i]->getStrNome() . ' (' . $prazo . ') - ' . $resposta;
                $strResultado .= '<td valign="top">' . PaginaSEI::getInstance()->getTrCheck($i, $arrObjMdPetIntTipoRespDTO[$i]->getNumIdMdPetIntTipoResp(), $nomeTransportado) . '</td>';
            }

            $strResultado .= '<td>' . PaginaSEI::tratarHTML($arrObjMdPetIntTipoRespDTO[$i]->getStrNome()) . '</td>';
            $strResultado .= '<td>' . $prazo . '</td>';
            $strResultado .= '<td>' . $resposta . '</td>';
            $strResultado .= '<td align="center">';

            $strResultado .= PaginaSEI::getInstance()->getAcaoTransportarItem($i, $arrObjMdPetIntTipoRespDTO[$i]->getNumIdMdPetIntTipoResp());

            if ($bolAcaoConsultar) {
                $strResultado .= '<a href="' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_int_tipo_resp_consultar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&id_md_pet_int_tipo_resp=' . $arrObjMdPetIntTipoRespDTO[$i]->getNumIdMdPetIntTipoResp()) . '" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/consultar.svg?'.Icone::VERSAO.'" title="Consultar  Tipo de Resposta" alt="Consultar " class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoAlterar) {
                $strResultado .= '<a href="' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_int_tipo_resp_alterar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&id_md_pet_int_tipo_resp=' . $arrObjMdPetIntTipoRespDTO[$i]->getNumIdMdPetIntTipoResp()) . '" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/alterar.svg?'.Icone::VERSAO.'" title="Alterar  Tipo de Resposta" alt="Alterar " class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoDesativar || $bolAcaoReativar || $bolAcaoExcluir) {
                $strId = $arrObjMdPetIntTipoRespDTO[$i]->getNumIdMdPetIntTipoResp();
                $strDescricao = PaginaSEI::tratarHTML($arrObjMdPetIntTipoRespDTO[$i]->getStrNome(), true);
            }

            if ($bolAcaoDesativar && $arrObjMdPetIntTipoRespDTO[$i]->getStrSinAtivo() == 'S') {
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="acaoDesativar(\'' . $strId . '\',\'' . $strDescricao . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/desativar.svg?'.Icone::VERSAO.'" title="Desativar  Tipo de Resposta" alt="Desativar " class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoReativar && $arrObjMdPetIntTipoRespDTO[$i]->getStrSinAtivo() == 'N') {
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="acaoReativar(\'' . $strId . '\',\'' . $strDescricao . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/reativar.svg?'.Icone::VERSAO.'" title="Reativar  Tipo de Resposta" alt="Reativar " class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoExcluir) {
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="acaoExcluir(\'' . $strId . '\',\'' . $strDescricao . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/excluir.svg?'.Icone::VERSAO.'" title="Excluir Tipo de Resposta" alt="Excluir " class="infraImg" /></a>&nbsp;';
            }

            $strResultado .= '</td></tr>' . "\n";
        }
        $strResultado .= '</table>';
    }
    $strItenSelPrazoExterno = MdPetIntTipoRespINT::montaSelectPrazoExterno8612('null', '&nbsp;', $_POST['selPrazoExterno']);
    $strRespostaUsuarioEx = MdPetIntTipoRespINT::montaSelectRespostaUsuario8612('null', '&nbsp;', $_POST['selRespostaUsuarioEx']);
    if ($_GET['acao'] == 'md_pet_int_tipo_resp_selecionar') {
        $arrComandos[] = '<button type="button" accesskey="C" id="btnFecharSelecao" value="Fechar" onclick="window.close();" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
    } else {
        $arrComandos[] = '<button type="button" accesskey="C" id="btnFechar" value="Fechar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']) . '\'" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
    }


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
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>
<? if (0){ ?>
    <script type="text/javascript"><?}?>

        function inicializar() {
            if ('<?=$_GET['acao']?>' == 'md_pet_int_tipo_resp_selecionar') {
                infraReceberSelecao();
                document.getElementById('btnFecharSelecao').focus();

            } else {
                document.getElementById('btnFechar').focus();
            }
            infraEfeitoTabelas();
        }

        <? if ($bolAcaoDesativar){ ?>
        function acaoDesativar(id, desc) {
            desc = $('<pre>').html(desc).text();
            if (confirm("Confirma desativação do Tipo de Resposta \"" + desc + "\"?")) {
                document.getElementById('hdnInfraItemId').value = id;
                document.getElementById('frmMdPetIntTipoRespLista').action = '<?=$strLinkDesativar?>';
                document.getElementById('frmMdPetIntTipoRespLista').submit();
            }
        }

        function acaoDesativacaoMultipla() {
            if (document.getElementById('hdnInfraItensSelecionados').value == '') {
                alert('Nenhuma selecionada.');
                return;
            }
            if (confirm("Confirma desativação dos  selecionados?")) {
                document.getElementById('hdnInfraItemId').value = '';
                document.getElementById('frmMdPetIntTipoRespLista').action = '<?=$strLinkDesativar?>';
                document.getElementById('frmMdPetIntTipoRespLista').submit();
            }
        }
        <? } ?>

        <? if ($bolAcaoReativar){ ?>
        function acaoReativar(id, desc) {
            desc = $('<pre>').html(desc).text();
            if (confirm("Confirma reativação do Tipo de Resposta \"" + desc + "\"?")) {
                document.getElementById('hdnInfraItemId').value = id;
                document.getElementById('frmMdPetIntTipoRespLista').action = '<?=$strLinkReativar?>';
                document.getElementById('frmMdPetIntTipoRespLista').submit();
            }
        }

        function acaoReativacaoMultipla() {
            if (document.getElementById('hdnInfraItensSelecionados').value == '') {
                alert('Nenhuma  selecionada.');
                return;
            }
            if (confirm("Confirma reativação dos  selecionados?")) {
                document.getElementById('hdnInfraItemId').value = '';
                document.getElementById('frmMdPetIntTipoRespLista').action = '<?=$strLinkReativar?>';
                document.getElementById('frmMdPetIntTipoRespLista').submit();
            }
        }
        <? } ?>
        <? if ($bolAcaoExcluir){ ?>
        function acaoExcluir(id, desc) {
            desc = $('<pre>').html(desc).text();
            if (confirm("Confirma exclusão do Tipo de Resposta \"'" + desc + "'\"?")) {
                document.getElementById('hdnInfraItemId').value = id;
                document.getElementById('frmMdPetIntTipoRespLista').action = '<?=$strLinkExcluir?>';
                document.getElementById('frmMdPetIntTipoRespLista').submit();
            }
        }

        function acaoExclusaoMultipla() {
            if (document.getElementById('hdnInfraItensSelecionados').value == '') {
                alert('Nenhuma  selecionada.');
                return;
            }
            if (confirm("Confirma exclusão dos selecionados?")) {
                document.getElementById('hdnInfraItemId').value = '';
                document.getElementById('frmMdPetIntTipoRespLista').action = '<?=$strLinkExcluir?>';
                document.getElementById('frmMdPetIntTipoRespLista').submit();
            }
        }
        <? } ?>

        <? if (0){ ?></script><? } ?>
<?
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
    <style type="text/css"> th.text-left .infraDivOrdenacao { margin-left: 0px } </style>
    <form id="frmMdPetIntTipoRespLista" method="post"
          action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">
        <? PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
        PaginaSEI::getInstance()->abrirAreaDados('6em');
        ?>
        <div class="row">
            <div class="col-sm-12 col-md-3 col-lg-4 col-xl-3">
                <label id="lblTipoResposta" class="infraLabel"
                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">Tipo
                    de Resposta:</label>
                <input type="text" id="txtTipoResposta" name="txtTipoResposta" class="infraText form-control"
                       value="<? echo(PaginaSEI::tratarHTML($_POST['txtTipoResposta']) != '' ? PaginaSEI::tratarHTML($_POST['txtTipoResposta']) : '') ?>"
                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                <input type="hidden" id="hdnTipoResposta" name="hdnTipoResposta"
                       value="<?= isset($numIdContatoAssociadoPesquisa) ? $numIdContatoAssociadoPesquisa : '' ?>"/>
            </div>
            <div class="col-sm-12 col-md-4 col-lg-4 col-xl-3">
                <label id="lblPrazoExterno" for="selPrazoExterno" class="infraLabelOpcional">Prazo Externo:</label>
                <select id="selPrazoExterno" name="selPrazoExterno" onchange="this.form.submit()" class="infraSelect form-control"
                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                    <?= $strItenSelPrazoExterno ?>
                </select>
            </div>
            <div class="col-sm-12 col-md-5 col-lg-4 col-xl-3">
                <label id="lblRespostaUsuarioEx" for="selRespostaUsuarioEx" accesskey="" class="infraLabelOpicional">Resposta
                    do
                    Usuário Externo:</label>
                <select id="selRespostaUsuarioEx" name="selRespostaUsuarioEx" onchange="this.form.submit()"
                        class="infraSelect form-control"
                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                    <?= $strRespostaUsuarioEx ?>
                </select>
            </div>
        </div>


        <?
        PaginaSEI::getInstance()->fecharAreaDados();
        PaginaSEI::getInstance()->montarAreaTabela($strResultado, $numRegistros);
        PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
        ?>
    </form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>