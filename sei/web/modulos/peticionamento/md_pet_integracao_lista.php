<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
 *
 * 25/01/2018 - criado por Usu�rio
 *
 * Vers�o do Gerador de C�digo: 1.41.0
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

    PaginaSEI::getInstance()->prepararSelecao('md_pet_integracao_selecionar');

    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    PaginaSEI::getInstance()->salvarCamposPost(array('txtNome', 'selMdPetIntegFuncionalid'));

    switch ($_GET['acao']) {
        case 'md_pet_integracao_excluir':
            try {
                $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
                $arrObjMdPetIntegracaoDTO = array();
                for ($i = 0; $i < count($arrStrIds); $i++) {
                    $objMdPetIntegracaoDTO = new MdPetIntegracaoDTO();
                    $objMdPetIntegracaoDTO->setNumIdMdPetIntegracao($arrStrIds[$i]);
                    $arrObjMdPetIntegracaoDTO[] = $objMdPetIntegracaoDTO;
                }
                $objMdPetIntegracaoRN = new MdPetIntegracaoRN();
                $objMdPetIntegracaoRN->excluirCompleto($arrObjMdPetIntegracaoDTO);
                PaginaSEI::getInstance()->adicionarMensagem('Opera��o realizada com sucesso.');
            } catch (Exception $e) {
                PaginaSEI::getInstance()->processarExcecao($e);
            }
            header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao_origem'] . '&acao_origem=' . $_GET['acao']));
            die;

        case 'md_pet_integracao_desativar':
            try {
                $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
                $arrObjMdPetIntegracaoDTO = array();
                for ($i = 0; $i < count($arrStrIds); $i++) {
                    $objMdPetIntegracaoDTO = new MdPetIntegracaoDTO();
                    $objMdPetIntegracaoDTO->setNumIdMdPetIntegracao($arrStrIds[$i]);
                    $objMdPetIntegracaoDTO->setStrSinAtivo('N');
                    $arrObjMdPetIntegracaoDTO[] = $objMdPetIntegracaoDTO;
                }
                $objMdPetIntegracaoRN = new MdPetIntegracaoRN();
                $objMdPetIntegracaoRN->desativar($arrObjMdPetIntegracaoDTO);
                PaginaSEI::getInstance()->adicionarMensagem('Opera��o realizada com sucesso.');
            } catch (Exception $e) {
                PaginaSEI::getInstance()->processarExcecao($e);
            }
            header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao_origem'] . '&acao_origem=' . $_GET['acao']));
            die;

        case 'md_pet_integracao_reativar':
            $strTitulo = 'Reativar Integra��o';
            if ($_GET['acao_confirmada'] == 'sim') {
                try {
                    $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
                    $arrObjMdPetIntegracaoDTO = array();
                    for ($i = 0; $i < count($arrStrIds); $i++) {

                        $objMdPetIntegracaoDTO = new MdPetIntegracaoDTO();
                        $objMdPetIntegracaoDTO->setNumIdMdPetIntegracao($arrStrIds[$i]);
                        $objMdPetIntegracaoDTO->setBolExclusaoLogica(false);
                        $objMdPetIntegracaoDTO->retNumIdMdPetIntegracao();
                        $objMdPetIntegracaoDTO->retNumIdMdPetIntegFuncionalid();
                        $objMdPetIntegracaoRN = new MdPetIntegracaoRN();
                        $objMdPetIntegracaoDTO = $objMdPetIntegracaoRN->consultar($objMdPetIntegracaoDTO);
                        $objMdPetIntegracaoDTO->setStrSinAtivo('S');
                        $arrObjMdPetIntegracaoDTO[] = $objMdPetIntegracaoDTO;
                    }

                    $objMdPetIntegracaoRN = new MdPetIntegracaoRN();
                    $objMdPetIntegracaoRN->reativar($arrObjMdPetIntegracaoDTO);
                    PaginaSEI::getInstance()->adicionarMensagem('Opera��o realizada com sucesso.');
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
                header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao_origem'] . '&acao_origem=' . $_GET['acao']));
                die;
            }
            break;


        case 'md_pet_integracao_selecionar':
            $strTitulo = PaginaSEI::getInstance()->getTituloSelecao('Selecionar Integra��o', 'Selecionar Integra��es');

            //Se cadastrou alguem
            if ($_GET['acao_origem'] == 'md_pet_integracao_cadastrar') {
                if (isset($_GET['id_md_pet_integracao'])) {
                    PaginaSEI::getInstance()->adicionarSelecionado($_GET['id_md_pet_integracao']);
                }
            }
            break;

        case 'md_pet_integracao_listar':
            $strTitulo = 'Integra��es';
            break;

        default:
            throw new InfraException("A��o '" . $_GET['acao'] . "' n�o reconhecida.");
    }

    $arrComandos = array();
    $arrComandos[] = '<button type="submit" accesskey="P" id="sbmPesquisar" value="Pesquisar" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';

    if ($_GET['acao'] == 'md_pet_integracao_selecionar') {
        $arrComandos[] = '<button type="button" accesskey="T" id="btnTransportarSelecao" value="Transportar" onclick="infraTransportarSelecao();" class="infraButton"><span class="infraTeclaAtalho">T</span>ransportar</button>';
    }

    if ($_GET['acao'] == 'md_pet_integracao_listar' || $_GET['acao'] == 'md_pet_integracao_selecionar') {
        $bolAcaoCadastrar = SessaoSEI::getInstance()->verificarPermissao('md_pet_integracao_cadastrar');
        if ($bolAcaoCadastrar) {
            $arrComandos[] = '<button type="button" accesskey="N" id="btnNovo" value="Novo" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_integracao_cadastrar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao']) . '\'" class="infraButton"><span class="infraTeclaAtalho">N</span>ovo</button>';
        }
    }

    $objMdPetIntegracaoDTO = new MdPetIntegracaoDTO();
    $objMdPetIntegracaoDTO->retNumIdMdPetIntegracao();
    $objMdPetIntegracaoDTO->retStrNome();
    //$objMdPetIntegracaoDTO->retStrEnderecoWsdl();
    //$objMdPetIntegracaoDTO->retStrOperacaoWsdl();
    //$objMdPetIntegracaoDTO->retStrSinCache();
    $objMdPetIntegracaoDTO->retStrSinAtivo();
    $objMdPetIntegracaoDTO->retStrTpClienteWs();
    $objMdPetIntegracaoDTO->retStrSinAtivo();
    $objMdPetIntegracaoDTO->retStrNomeMdPetIntegFuncionalid();
    $numIdMdPetIntegFuncionalid = PaginaSEI::getInstance()->recuperarCampo('selMdPetIntegFuncionalid');
    if ($numIdMdPetIntegFuncionalid !== '') {
        $objMdPetIntegracaoDTO->setNumIdMdPetIntegFuncionalid($numIdMdPetIntegFuncionalid);
    }
    $Nome = PaginaSEI::getInstance()->recuperarCampo('txtNome');
    if ($Nome !== '') {
        $objMdPetIntegracaoDTO->setStrNome('%' . $Nome . '%', InfraDTO::$OPER_LIKE);
    }

    $objMdPetIntegracaoDTO->setBolExclusaoLogica(false);

    if ($_GET['acao'] == 'md_pet_integracao_reativar') {
        //Lista somente inativos
        $objMdPetIntegracaoDTO->setBolExclusaoLogica(false);
        $objMdPetIntegracaoDTO->setStrSinAtivo('N');
    }

    PaginaSEI::getInstance()->prepararOrdenacao($objMdPetIntegracaoDTO, 'Nome', InfraDTO::$TIPO_ORDENACAO_ASC);
    //PaginaSEI::getInstance()->prepararPaginacao($objMdPetIntegracaoDTO);

    $objMdPetIntegracaoRN = new MdPetIntegracaoRN();
    $arrObjMdPetIntegracaoDTO = $objMdPetIntegracaoRN->listar($objMdPetIntegracaoDTO);

    //PaginaSEI::getInstance()->processarPaginacao($objMdPetIntegracaoDTO);
    $numRegistros = count($arrObjMdPetIntegracaoDTO);

    if ($numRegistros > 0) {

        $bolCheck = false;

        if ($_GET['acao'] == 'md_pet_integracao_selecionar') {
            $bolAcaoReativar = false;
            $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pet_integracao_consultar');
            $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('md_pet_integracao_alterar');
            $bolAcaoImprimir = false;
            //$bolAcaoGerarPlanilha = false;
            $bolAcaoExcluir = false;
            $bolAcaoDesativar = false;
            $bolCheck = true;
        } else if ($_GET['acao'] == 'md_pet_integracao_reativar') {
            $bolAcaoReativar = SessaoSEI::getInstance()->verificarPermissao('md_pet_integracao_reativar');
            $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pet_integracao_consultar');
            $bolAcaoAlterar = false;
            $bolAcaoImprimir = true;
            //$bolAcaoGerarPlanilha = SessaoSEI::getInstance()->verificarPermissao('infra_gerar_planilha_tabela');
            $bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('md_pet_integracao_excluir');
            $bolAcaoDesativar = false;
        } else {
            //$bolAcaoReativar = false;
            $bolAcaoReativar = SessaoSEI::getInstance()->verificarPermissao('md_pet_integracao_reativar');
            $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pet_integracao_consultar');
            $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('md_pet_integracao_alterar');
            $bolAcaoImprimir = true;
            //$bolAcaoGerarPlanilha = SessaoSEI::getInstance()->verificarPermissao('infra_gerar_planilha_tabela');
            $bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('md_pet_integracao_excluir');
            $bolAcaoDesativar = SessaoSEI::getInstance()->verificarPermissao('md_pet_integracao_desativar');
        }


        if ($bolAcaoDesativar) {
            $bolCheck = true;
//      $arrComandos[] = '<button type="button" accesskey="t" id="btnDesativar" value="Desativar" onclick="acaoDesativacaoMultipla();" class="infraButton">Desa<span class="infraTeclaAtalho">t</span>ivar</button>';
            $strLinkDesativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_integracao_desativar&acao_origem=' . $_GET['acao']);
        }

        if ($bolAcaoReativar) {
            $bolCheck = true;
//      $arrComandos[] = '<button type="button" accesskey="R" id="btnReativar" value="Reativar" onclick="acaoReativacaoMultipla();" class="infraButton"><span class="infraTeclaAtalho">R</span>eativar</button>';
            $strLinkReativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_integracao_reativar&acao_origem=' . $_GET['acao'] . '&acao_confirmada=sim');
        }

        if ($bolAcaoImprimir) {
            $bolCheck = true;
            $arrComandos[] = '<button type="button" accesskey="I" id="btnImprimir" value="Imprimir" onclick="infraImprimirTabela();" class="infraButton"><span class="infraTeclaAtalho">I</span>mprimir</button>';
        }

        if ($bolAcaoExcluir) {
            $bolCheck = true;
//      $arrComandos[] = '<button type="button" accesskey="E" id="btnExcluir" value="Excluir" onclick="acaoExclusaoMultipla();" class="infraButton"><span class="infraTeclaAtalho">E</span>xcluir</button>';
            $strLinkExcluir = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_integracao_excluir&acao_origem=' . $_GET['acao']);
        }

//    if ($bolAcaoGerarPlanilha){
//      $bolCheck = true;
//      $arrComandos[] = '<button type="button" accesskey="P" id="btnGerarPlanilha" value="Gerar Planilha" onclick="infraGerarPlanilhaTabela(\''.SessaoSEI::getInstance()->assinarLink('controlador.php?acao=infra_gerar_planilha_tabela').'\');" class="infraButton">Gerar <span class="infraTeclaAtalho">P</span>lanilha</button>';
//    }

        $strResultado = '';

        if ($_GET['acao'] != 'md_pet_integracao_reativar') {
            $strSumarioTabela = 'Tabela de Integra��es.';
            $strCaptionTabela = 'Integra��es';
        } else {
            $strSumarioTabela = 'Tabela de Integra��es Inativas.';
            $strCaptionTabela = 'Integra��es Inativas';
        }

        $strResultado .= '<table width="99%" class="infraTable" summary="' . $strSumarioTabela . '">' . "\n";
        $strResultado .= '<caption class="infraCaption">' . PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela, $numRegistros) . '</caption>';
        $strResultado .= '<tr>';
        if ($bolCheck) {
            $strResultado .= '<th class="infraTh" width="1%">' . PaginaSEI::getInstance()->getThCheck() . '</th>' . "\n";
        }
        $strResultado .= '<th class="infraTh">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetIntegracaoDTO, 'Nome', 'Nome', $arrObjMdPetIntegracaoDTO) . '</th>' . "\n";
        //$strResultado .= '<th class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objMdPetIntegracaoDTO,'Endere�o do Webservice','EnderecoWsdl',$arrObjMdPetIntegracaoDTO).'</th>'."\n";
        //$strResultado .= '<th class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objMdPetIntegracaoDTO,'Opera��o','OperacaoWsdl',$arrObjMdPetIntegracaoDTO).'</th>'."\n";
        //$strResultado .= '<th class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objMdPetIntegracaoDTO,'Marque caso seu Webservice tenha controle de expira��o de cache','SinCache',$arrObjMdPetIntegracaoDTO).'</th>'."\n";
        $strResultado .= '<th class="infraTh">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetIntegracaoDTO, 'Funcionalidade', 'NomeMdPetIntegFuncionalid', $arrObjMdPetIntegracaoDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetIntegracaoDTO, 'Tipo Cliente WS', 'TpClienteWs', $arrObjMdPetIntegracaoDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh" style="width:140px">A��es</th>' . "\n";
        $strResultado .= '</tr>' . "\n";
        $strCssTr = '';
        for ($i = 0; $i < $numRegistros; $i++) {

            if ($arrObjMdPetIntegracaoDTO[$i]->getStrSinAtivo() == 'N') {
                $strCssTr = '<tr class="trVermelha">';
            } else {
                $strCssTr = ($strCssTr == '<tr class="infraTrClara">') ? '<tr class="infraTrEscura">' : '<tr class="infraTrClara">';
            }

            $strResultado .= $strCssTr;

            if ($bolCheck) {
                $strResultado .= '<td valign="top">' . PaginaSEI::getInstance()->getTrCheck($i, $arrObjMdPetIntegracaoDTO[$i]->getNumIdMdPetIntegracao(), $arrObjMdPetIntegracaoDTO[$i]->getStrNome()) . '</td>';
            }
            $strTpClienteWs = $arrObjMdPetIntegracaoDTO[$i]->getStrTpClienteWs() == 'S' ? 'SOAP' : 'REST';
            $strResultado .= '<td>' . PaginaSEI::tratarHTML($arrObjMdPetIntegracaoDTO[$i]->getStrNome()) . '</td>';
            //$strResultado .= '<td>'.PaginaSEI::tratarHTML($arrObjMdPetIntegracaoDTO[$i]->getStrEnderecoWsdl()).'</td>';
            //$strResultado .= '<td>'.PaginaSEI::tratarHTML($arrObjMdPetIntegracaoDTO[$i]->getStrOperacaoWsdl()).'</td>';
            //$strResultado .= '<td>'.PaginaSEI::tratarHTML($arrObjMdPetIntegracaoDTO[$i]->getStrSinCache()).'</td>';
            $strResultado .= '<td align="center">' . PaginaSEI::tratarHTML($arrObjMdPetIntegracaoDTO[$i]->getStrNomeMdPetIntegFuncionalid()) . '</td>';
            $strResultado .= '<td align="center">' . $strTpClienteWs . '</td>';
            $strResultado .= '<td align="center">';

            $strResultado .= PaginaSEI::getInstance()->getAcaoTransportarItem($i, $arrObjMdPetIntegracaoDTO[$i]->getNumIdMdPetIntegracao());

            if ($bolAcaoConsultar) {
                $strResultado .= '<a href="' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_integracao_consultar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&id_md_pet_integracao=' . $arrObjMdPetIntegracaoDTO[$i]->getNumIdMdPetIntegracao()) . '" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/consultar.svg?'.Icone::VERSAO.'" title="Consultar Integra��o" alt="Consultar Integra��o" class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoAlterar) {
                $strResultado .= '<a href="' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_integracao_alterar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&id_md_pet_integracao=' . $arrObjMdPetIntegracaoDTO[$i]->getNumIdMdPetIntegracao()) . '" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/alterar.svg?'.Icone::VERSAO.'" title="Alterar Integra��o" alt="Alterar Integra��o" class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoDesativar || $bolAcaoReativar || $bolAcaoExcluir) {
                $strId = $arrObjMdPetIntegracaoDTO[$i]->getNumIdMdPetIntegracao();
                $strDescricao = PaginaSEI::getInstance()->formatarParametrosJavaScript($arrObjMdPetIntegracaoDTO[$i]->getStrNome());
            }

            if ($bolAcaoDesativar && $arrObjMdPetIntegracaoDTO[$i]->getStrSinAtivo() == 'S') {
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="acaoDesativar(\'' . $strId . '\',\'' . $strDescricao . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/desativar.svg?'.Icone::VERSAO.'" title="Desativar Integra��o" alt="Desativar Integra��o" class="infraImg" /></a>&nbsp;';
            } else {
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="acaoReativar(\'' . $strId . '\',\'' . $strDescricao . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/reativar.svg?'.Icone::VERSAO.'" title="Reativar Integra��o" alt="Reativar Integra��o" class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoExcluir) {
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="acaoExcluir(\'' . $strId . '\',\'' . $strDescricao . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/excluir.svg?'.Icone::VERSAO.'" title="Excluir Integra��o" alt="Excluir Integra��o" class="infraImg" /></a>&nbsp;';
            }

            $strResultado .= '</td></tr>' . "\n";
        }
        $strResultado .= '</table>';
    }
    if ($_GET['acao'] == 'md_pet_integracao_selecionar') {
        $arrComandos[] = '<button type="button" accesskey="c" id="btnFecharSelecao" value="Fechar" onclick="window.close();" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
    } else {
        $arrComandos[] = '<button type="button" accesskey="c" id="btnFechar" value="Fechar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']) . '\'" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
    }

    $strItensSelMdPetIntegFuncionalid = MdPetIntegFuncionalidINT::montarSelectNome('', 'Todos', $numIdMdPetIntegFuncionalid);

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
    function inicializar(){
    if ('<?= $_GET['acao'] ?>'=='md_pet_integracao_selecionar'){
    infraReceberSelecao();
    document.getElementById('btnFecharSelecao').focus();
    }else{
    document.getElementById('btnFechar').focus();
    }
    infraEfeitoTabelas();
    }

<? if ($bolAcaoDesativar) { ?>
    function acaoDesativar(id,desc){
    if (confirm("Confirma desativa��o da Integra��o \""+desc+"\"?")){
    document.getElementById('hdnInfraItemId').value=id;
    document.getElementById('frmMdPetIntegracaoLista').action='<?= $strLinkDesativar ?>';
    document.getElementById('frmMdPetIntegracaoLista').submit();
    }
    }

    //function acaoDesativacaoMultipla(){
    //  if (document.getElementById('hdnInfraItensSelecionados').value==''){
    //    alert('Nenhuma Integra��o selecionada.');
    //    return;
    //  }
    //  if (confirm("Confirma desativa��o das Integra��es selecionadas?")){
    //    document.getElementById('hdnInfraItemId').value='';
    //    document.getElementById('frmMdPetIntegracaoLista').action='<?= $strLinkDesativar ?>';
    //    document.getElementById('frmMdPetIntegracaoLista').submit();
    //  }
    //}
<? } ?>

<? if ($bolAcaoReativar) { ?>
    function acaoReativar(id,desc){
    if (confirm("Confirma reativa��o da Integra��o \""+desc+"\"?")){
    document.getElementById('hdnInfraItemId').value=id;
    document.getElementById('frmMdPetIntegracaoLista').action='<?= $strLinkReativar ?>';
    document.getElementById('frmMdPetIntegracaoLista').submit();
    }
    }

    //function acaoReativacaoMultipla(){
    //  if (document.getElementById('hdnInfraItensSelecionados').value==''){
    //    alert('Nenhuma Integra��o selecionada.');
    //    return;
    //  }
    //  if (confirm("Confirma reativa��o das Integra��es selecionadas?")){
    //    document.getElementById('hdnInfraItemId').value='';
    //    document.getElementById('frmMdPetIntegracaoLista').action='<?= $strLinkReativar ?>';
    //    document.getElementById('frmMdPetIntegracaoLista').submit();
    //  }
    //}
<? } ?>

<?
if ($bolAcaoExcluir) { ?>
    function acaoExcluir(id,desc){
    if (confirm("Confirma exclus�o da Integra��o \""+desc+"\"?")){
    document.getElementById('hdnInfraItemId').value=id;
    document.getElementById('frmMdPetIntegracaoLista').action='<?= $strLinkExcluir ?>';
    document.getElementById('frmMdPetIntegracaoLista').submit();
    }
    }

    //function acaoExclusaoMultipla(){
    //  if (document.getElementById('hdnInfraItensSelecionados').value==''){
    //    alert('Nenhuma Integra��o selecionada.');
    //    return;
    //  }
    //  if (confirm("Confirma exclus�o das Integra��es selecionadas?")){
    //    document.getElementById('hdnInfraItemId').value='';
    //    document.getElementById('frmMdPetIntegracaoLista').action='<?= $strLinkExcluir ?>';
    //    document.getElementById('frmMdPetIntegracaoLista').submit();
    //  }
    //}
<? } ?>
<?
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
    <form id="frmMdPetIntegracaoLista" method="post"
          action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">
        <?
        PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
        PaginaSEI::getInstance()->abrirAreaDados('auto');
        ?>
        <div class="row">
            <div class="col-sm-12 col-md-6 col-lg-5 col-xl-5">
                <div class="mb-2">
                    <label id="lblNome" for="txtNome" accesskey="" class="infraLabelOpcional">Nome:</label>
                    <input type=text id="txtNome" name="txtNome" class="infraText form-control"
                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-5 col-xl-5">
                <label id="lblMdPetIntegFuncionalid" for="selMdPetIntegFuncionalid" accesskey=""
                       class="infraLabelOpcional">Funcionalidade:</label>
                <select id="selMdPetIntegFuncionalid" name="selMdPetIntegFuncionalid" onchange="this.form.submit();"
                        class="infraSelect form-control" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                    <?= $strItensSelMdPetIntegFuncionalid ?>
                </select>
            </div>
        </div>
        <?
        PaginaSEI::getInstance()->fecharAreaDados();
        PaginaSEI::getInstance()->montarAreaTabela($strResultado, $numRegistros);
        ?>
    </form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
