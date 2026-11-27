<?php
/**
 * ANATEL
 *
 * Listagem dos Tipos de Processo parametrizados para o Peticionamento Interno.
 *
 * 03/09/2026 - criado por emerson_b.goncalves - ANATEL
 */
try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    SessaoSEI::getInstance()->validarLink();

    $strAcao = MdPetPiEntradaINT::getStr('acao');
    $strAcaoOrigem = MdPetPiEntradaINT::getStr('acao_origem');
    $strAcaoConfirmada = MdPetPiEntradaINT::getStr('acao_confirmada');

    SessaoSEI::getInstance()->validarPermissao($strAcao);

    $arrIndicacaoInteressado = MdPetPiTpProcessoINT::listarValoresIndicacaoInteressado();
    $arrDocPrincipal = MdPetPiTpProcessoINT::listarValoresDocPrincipal();

    $strFiltroTipoProcesso = MdPetPiEntradaINT::postStr('txtTipoProcesso');

    $strFiltroIndicacao = MdPetPiEntradaINT::postEnum('selIndicacaoInteressado', $arrIndicacaoInteressado);

    $arrOpcoesDocPrincipal = [];
    $strFiltroDocPrincipal = '';

    $bolAcaoReativar = false;
    $bolAcaoConsultar = false;
    $bolAcaoAlterar = false;
    $bolAcaoImprimir = false;
    $bolAcaoExcluir = false;
    $bolAcaoDesativar = false;
    $bolCheck = false;
    $strResultado = '';
    $strLinkDesativar = '';
    $strLinkReativar = '';
    $strLinkExcluir = '';

    switch ($strAcao) {

        case 'md_pet_pi_tp_processo_excluir':
            try {
                $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
                $arrObjMdPetPiTpProcessoDTO = [];

                for ($i = 0; $i < count($arrStrIds); $i++) {
                    $objMdPetPiTpProcessoDTO = new MdPetPiTpProcessoDTO();
                    $objMdPetPiTpProcessoDTO->setNumIdMdPetPiTpProcesso((int) $arrStrIds[$i]);
                    $arrObjMdPetPiTpProcessoDTO[] = $objMdPetPiTpProcessoDTO;
                }

                (new MdPetPiTpProcessoRN())->excluir($arrObjMdPetPiTpProcessoDTO);

                PaginaSEI::getInstance()->adicionarMensagem('Operação realizada com sucesso.');
            } catch (Exception $e) {
                PaginaSEI::getInstance()->processarExcecao($e);
            }

            header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $strAcaoOrigem . '&acao_origem=' . $strAcao));
            die;

        case 'md_pet_pi_tp_processo_desativar':
            try {
                $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
                $arrObjMdPetPiTpProcessoDTO = [];

                for ($i = 0; $i < count($arrStrIds); $i++) {
                    $objMdPetPiTpProcessoDTO = new MdPetPiTpProcessoDTO();
                    $objMdPetPiTpProcessoDTO->setNumIdMdPetPiTpProcesso((int) $arrStrIds[$i]);
                    $objMdPetPiTpProcessoDTO->setStrSinAtivo('N');
                    $arrObjMdPetPiTpProcessoDTO[] = $objMdPetPiTpProcessoDTO;
                }

                (new MdPetPiTpProcessoRN())->desativar($arrObjMdPetPiTpProcessoDTO);

                PaginaSEI::getInstance()->adicionarMensagem('Operação realizada com sucesso.');
            } catch (Exception $e) {
                PaginaSEI::getInstance()->processarExcecao($e);
            }

            header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $strAcaoOrigem . '&acao_origem=' . $strAcao));
            die;

        case 'md_pet_pi_tp_processo_reativar':

            $strTitulo = 'Reativar Tipo de Processo para Peticionamento Interno';

            if ($strAcaoConfirmada === 'sim') {
                $numIdReativado = 0;

                try {
                    $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
                    $arrObjMdPetPiTpProcessoDTO = [];

                    for ($i = 0; $i < count($arrStrIds); $i++) {
                        $objMdPetPiTpProcessoDTO = new MdPetPiTpProcessoDTO();
                        $objMdPetPiTpProcessoDTO->setNumIdMdPetPiTpProcesso((int) $arrStrIds[$i]);
                        $objMdPetPiTpProcessoDTO->setStrSinAtivo('S');
                        $numIdReativado = (int) $arrStrIds[$i];
                        $arrObjMdPetPiTpProcessoDTO[] = $objMdPetPiTpProcessoDTO;
                    }

                    (new MdPetPiTpProcessoRN())->reativar($arrObjMdPetPiTpProcessoDTO);

                    PaginaSEI::getInstance()->adicionarMensagem('Operação realizada com sucesso.');
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }

                $strAncora = '';
                if ($numIdReativado !== 0) {
                    $strAncora = '&id_md_pet_pi_tp_processo=' . $numIdReativado . PaginaSEI::getInstance()->montarAncora($numIdReativado);
                }

                header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $strAcaoOrigem . '&acao_origem=' . $strAcao . $strAncora));
                die;
            }
            break;

        case 'md_pet_pi_tp_processo_listar':
            $strTitulo = 'Tipos para Peticionamento Interno';
            break;

        default:
            throw new InfraException("Ação '" . $strAcao . "' não reconhecida.");
    }

    $arrComandos = [];

    $arrOpcoesDocPrincipal = MdPetPiTpProcessoINT::listarOpcoesFiltroDocPrincipal();
    $strFiltroDocPrincipal = MdPetPiEntradaINT::postEnum('selDocPrincipal', $arrOpcoesDocPrincipal);

    $objMdPetPiTpProcessoDTO = new MdPetPiTpProcessoDTO();
    $objMdPetPiTpProcessoDTO->retTodos();
    $objMdPetPiTpProcessoDTO->retStrNomeTipoProcedimento();
    $objMdPetPiTpProcessoDTO->retStrStaAplicabilidadeDocPrinc();

    if ($strFiltroTipoProcesso !== '') {
        $objMdPetPiTpProcessoDTO->setStrNomeTipoProcedimento('%' . $strFiltroTipoProcesso . '%', InfraDTO::$OPER_LIKE);
    }

    if ($strFiltroIndicacao !== '') {
        $objMdPetPiTpProcessoDTO->setStrStaIndicacaoInteressado($strFiltroIndicacao);
    }

    if ($strFiltroDocPrincipal !== '') {
        $objMdPetPiTpProcessoDTO->setStrStaAplicabilidadeDocPrinc($strFiltroDocPrincipal);
    }

    // Lista ativos e inativos, com os inativos destacados, como nas listas do core
    $objMdPetPiTpProcessoDTO->setBolExclusaoLogica(false);

    if ($strAcao === 'md_pet_pi_tp_processo_reativar') {
        $objMdPetPiTpProcessoDTO->setStrSinAtivo('N');
    }

    PaginaSEI::getInstance()->prepararOrdenacao($objMdPetPiTpProcessoDTO, 'NomeTipoProcedimento', InfraDTO::$TIPO_ORDENACAO_ASC);
    PaginaSEI::getInstance()->prepararPaginacao($objMdPetPiTpProcessoDTO, 200);

    $objMdPetPiTpProcessoRN = new MdPetPiTpProcessoRN();
    $arrObjMdPetPiTpProcessoDTO = $objMdPetPiTpProcessoRN->listar($objMdPetPiTpProcessoDTO);

    PaginaSEI::getInstance()->processarPaginacao($objMdPetPiTpProcessoDTO);

    $numRegistros = count($arrObjMdPetPiTpProcessoDTO);

    // Conferencia de assunto em lote: o assunto pode ter sido removido apos o cadastro e nao ha
    // evento de modulo que intercepte alteracao de Tipo de Processo.
    $arrIdTipoProcedimentoComAssunto = [];
    if ($numRegistros > 0) {
        $arrIdTipoProcedimento = InfraArray::converterArrInfraDTO($arrObjMdPetPiTpProcessoDTO, 'IdTipoProcedimento');
        $arrIdTipoProcedimentoComAssunto = $objMdPetPiTpProcessoRN->listarIdsTipoProcedimentoComAssunto($arrIdTipoProcedimento);
    }

    $strLinkPesquisar = PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $strAcao . '&acao_origem=' . $strAcao . '&acao_retorno=md_pet_pi_tp_processo_listar'));
    $arrComandos[] = '<button type="button" accesskey="p" id="btnPesquisar" value="Pesquisar" onclick="pesquisar();" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';

    if (SessaoSEI::getInstance()->verificarPermissao('md_pet_pi_orientacao_cadastrar')) {
        $arrComandos[] = '<button type="button" accesskey="o" id="btnOrientacoes" value="Orientações Gerais" class="infraButton" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_pi_orientacao_cadastrar&acao_origem=' . $strAcao . '&acao_retorno=' . $strAcao)) . '\'"><span class="infraTeclaAtalho">O</span>rientações Gerais</button>';
    }

    if (SessaoSEI::getInstance()->verificarPermissao('md_pet_pi_tp_processo_cadastrar')) {
        $arrComandos[] = '<button type="button" accesskey="n" id="btnNovo" value="Novo" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_pi_tp_processo_cadastrar&acao_origem=' . $strAcao . '&acao_retorno=' . $strAcao)) . '\'" class="infraButton"><span class="infraTeclaAtalho">N</span>ovo</button>';
    }

    if ($numRegistros > 0) {

        if ($strAcao === 'md_pet_pi_tp_processo_reativar') {
            $bolAcaoReativar = SessaoSEI::getInstance()->verificarPermissao('md_pet_pi_tp_processo_reativar');
            $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pet_pi_tp_processo_consultar');
            $bolAcaoImprimir = true;
            $bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('md_pet_pi_tp_processo_excluir');
        } else {
            $bolAcaoReativar = SessaoSEI::getInstance()->verificarPermissao('md_pet_pi_tp_processo_reativar');
            $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pet_pi_tp_processo_consultar');
            $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('md_pet_pi_tp_processo_alterar');
            $bolAcaoImprimir = true;
            $bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('md_pet_pi_tp_processo_excluir');
            $bolAcaoDesativar = SessaoSEI::getInstance()->verificarPermissao('md_pet_pi_tp_processo_desativar');
        }

        if ($bolAcaoDesativar) {
            $bolCheck = true;
            $strLinkDesativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_pi_tp_processo_desativar&acao_origem=' . $strAcao);
        }

        if ($bolAcaoExcluir) {
            $bolCheck = true;
            $strLinkExcluir = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_pi_tp_processo_excluir&acao_origem=' . $strAcao);
        }

        $strLinkReativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_pi_tp_processo_reativar&acao_origem=' . $strAcao . '&acao_confirmada=sim');

        if ($strAcao === 'md_pet_pi_tp_processo_reativar') {
            $strSumarioTabela = 'Tabela de Tipos de Processo Inativos para Peticionamento Interno';
            $strCaptionTabela = 'Tipos de Processo Inativos';
        } else {
            $strSumarioTabela = 'Tabela de Tipos de Processo para Peticionamento Interno';
            $strCaptionTabela = 'Tipos de Processo para Peticionamento Interno';
        }

        $strResultado .= '<table width="99%" class="infraTable" summary="' . $strSumarioTabela . '">' . "\n";
        $strResultado .= '<caption class="infraCaption">' . PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela, $numRegistros) . '</caption>';
        $strResultado .= '<tr>';

        if ($bolCheck) {
            $strResultado .= '<th class="infraTh" width="1%">' . PaginaSEI::getInstance()->getThCheck() . '</th>' . "\n";
        }

        $strResultado .= '<th class="infraTh thLeft" width="45%">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetPiTpProcessoDTO, 'Tipo de Processo', 'NomeTipoProcedimento', $arrObjMdPetPiTpProcessoDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh thLeft" width="20%">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetPiTpProcessoDTO, 'Indicação de Interessado', 'StaIndicacaoInteressado', $arrObjMdPetPiTpProcessoDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh thLeft" width="20%">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetPiTpProcessoDTO, 'Espécie de Documento Principal', 'StaAplicabilidadeDocPrinc', $arrObjMdPetPiTpProcessoDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh" width="15%">Ações</th>' . "\n";
        $strResultado .= '</tr>' . "\n";

        $strCssTr = '';

        for ($i = 0; $i < $numRegistros; $i++) {

            $objDTO = $arrObjMdPetPiTpProcessoDTO[$i];
            $numId = $objDTO->getNumIdMdPetPiTpProcesso();
            $bolSemAssunto = !in_array($objDTO->getNumIdTipoProcedimento(), $arrIdTipoProcedimentoComAssunto);

            $bolAtivo = $objDTO->getStrSinAtivo() === 'S';

            if (!$bolAtivo) {
                $strResultado .= '<tr class="trVermelha">';
            } elseif ($bolSemAssunto) {
                $strResultado .= '<tr bgcolor="#F4A460">';
            } else {
                $strCssTr = ($strCssTr === '<tr class="infraTrClara">') ? '<tr class="infraTrEscura">' : '<tr class="infraTrClara">';
                $strResultado .= $strCssTr;
            }

            if ($bolCheck) {
                $strResultado .= '<td valign="middle">' . PaginaSEI::getInstance()->getTrCheck($i, $numId, $objDTO->getStrNomeTipoProcedimento()) . '</td>';
            }

            $strResultado .= '<td valign="middle">' . PaginaSEI::tratarHTML($objDTO->getStrNomeTipoProcedimento()) . '</td>';
            $strResultado .= '<td valign="middle">' . PaginaSEI::tratarHTML(MdPetPiTpProcessoINT::obterDescricaoIndicacaoInteressado($objDTO->getStrStaIndicacaoInteressado())) . '</td>';
            $strResultado .= '<td valign="middle">' . PaginaSEI::tratarHTML($arrOpcoesDocPrincipal[$objDTO->getStrStaAplicabilidadeDocPrinc()] ?? '') . '</td>';
            $strResultado .= '<td align="center" valign="middle">';

            if ($bolSemAssunto) {
                $strResultado .= '<img src="modulos/peticionamento/imagens/svg/icone_restricao.svg?' . Icone::VERSAO . '" style="width: 21px" onmouseover=\'return infraTooltipMostrar("Este Tipo de Processo não é mais exibido no Peticionamento Interno, pois a Sugestão de Assuntos foi removida do Tipo de Processo depois deste cadastro.&lt;br&gt;&lt;br&gt;Cadastre novamente a Sugestão de Assuntos no menu Administração &gt; Tipos de Processos &gt; Listar.","");\' onmouseout="return infraTooltipOcultar();" />&nbsp;';
            }

            $strDescricao = PaginaSEI::getInstance()->formatarParametrosJavaScript(PaginaSEI::tratarHTML($objDTO->getStrNomeTipoProcedimento()));

            if ($bolAcaoConsultar) {
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_pi_tp_processo_consultar&acao_origem=' . $strAcao . '&acao_retorno=' . $strAcao . '&id_md_pet_pi_tp_processo=' . $numId)) . '" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/consultar.svg?' . Icone::VERSAO . '" title="Consultar Tipo de Processo" alt="Consultar Tipo de Processo" class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoAlterar) {
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_pi_tp_processo_alterar&acao_origem=' . $strAcao . '&acao_retorno=' . $strAcao . '&id_md_pet_pi_tp_processo=' . $numId)) . '" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/alterar.svg?' . Icone::VERSAO . '" title="Alterar Tipo de Processo" alt="Alterar Tipo de Processo" class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoDesativar && $bolAtivo) {
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($numId) . '" onclick="acaoDesativar(\'' . $numId . '\',\'' . $strDescricao . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/desativar.svg?' . Icone::VERSAO . '" title="Desativar Tipo de Processo" alt="Desativar Tipo de Processo" class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoReativar && !$bolAtivo) {
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($numId) . '" onclick="acaoReativar(\'' . $numId . '\',\'' . $strDescricao . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/reativar.svg?' . Icone::VERSAO . '" title="Reativar Tipo de Processo" alt="Reativar Tipo de Processo" class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoExcluir) {
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($numId) . '" onclick="acaoExcluir(\'' . $numId . '\',\'' . $strDescricao . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/excluir.svg?' . Icone::VERSAO . '" title="Excluir Tipo de Processo" alt="Excluir Tipo de Processo" class="infraImg" /></a>&nbsp;';
            }

            $strResultado .= '</td></tr>' . "\n";
        }

        $strResultado .= '</table>';
    }

    if ($bolAcaoImprimir) {
        $arrComandos[] = '<button type="button" accesskey="i" id="btnImprimir" value="Imprimir" onclick="infraImprimirTabela();" class="infraButton"><span class="infraTeclaAtalho">I</span>mprimir</button>';
    }

    if ($strAcao === 'md_pet_pi_tp_processo_reativar') {
        $arrComandos[] = '<button type="button" accesskey="c" id="btnFecharSelecao" value="Fechar" onclick="window.close();" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
    } else {
        $arrComandos[] = '<button type="button" accesskey="c" name="btnFechar" id="btnFechar" value="Fechar" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=procedimento_controlar&acao_origem=' . $strAcao)) . '\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
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
require_once 'md_pet_pi_tp_processo_lista_css.php';
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
require_once 'md_pet_pi_tp_processo_lista_js.php';
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
<form id="frmMdPetPiTpProcessoLista" method="post"
      action="<?= PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $strAcao . '&acao_origem=' . $strAcao)) ?>">

    <?php PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos); ?>

    <div class="row">
        <div class="col-sm-12 col-md-5 col-lg-5 col-xl-4">
            <label id="lblTipoProcesso" for="txtTipoProcesso" class="infraLabelOpcional">Tipo de Processo:</label>
            <input type="text" name="txtTipoProcesso" id="txtTipoProcesso"
                   value="<?= PaginaSEI::tratarHTML($strFiltroTipoProcesso) ?>"
                   class="infraText form-control"/>
        </div>
        <div class="col-sm-6 col-md-4 col-lg-4 col-xl-4">
            <label id="lblIndicacaoInteressado" for="selIndicacaoInteressado" class="infraLabelOpcional">Indicação de Interessado:</label>
            <select onchange="pesquisar();" id="selIndicacaoInteressado" name="selIndicacaoInteressado"
                    class="infraSelect form-select">
                <option value="">Todos</option>
                <?php foreach ($arrIndicacaoInteressado as $strChave => $strDescricao) { ?>
                    <option value="<?= PaginaSEI::tratarHTML($strChave) ?>" <?= $strFiltroIndicacao === $strChave ? 'selected="selected"' : '' ?>><?= PaginaSEI::tratarHTML($strDescricao) ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-sm-6 col-md-3 col-lg-3 col-xl-4">
            <label id="lblDocPrincipal" for="selDocPrincipal" class="infraLabelOpcional">Espécie de Documento Principal:</label>
            <select onchange="pesquisar();" id="selDocPrincipal" name="selDocPrincipal"
                    class="infraSelect form-select">
                <option value="">Todos</option>
                <?php foreach ($arrOpcoesDocPrincipal as $strChave => $strDescricao) { ?>
                    <option value="<?= PaginaSEI::tratarHTML($strChave) ?>" <?= $strFiltroDocPrincipal === $strChave ? 'selected="selected"' : '' ?>><?= PaginaSEI::tratarHTML($strDescricao) ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <?php
    PaginaSEI::getInstance()->montarAreaTabela($strResultado, $numRegistros);
    PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
    ?>

</form>
<?php
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
