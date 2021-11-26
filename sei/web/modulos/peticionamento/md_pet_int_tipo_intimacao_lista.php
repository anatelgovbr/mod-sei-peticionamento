<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
 *
 * 08/12/2016 - criado por Marcelo Bezerra - CAST
 *
 * Vers�o do Gerador de C�digo: 1.39.0
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

    PaginaSEI::getInstance()->prepararSelecao('md_pet_int_tipo_intimacao_selecionar');

    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);
    switch ($_GET['acao']) {

        case 'md_pet_int_tipo_intimacao_excluir':
            try {
                $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
                $arrObjMdPetIntTipoIntimacaoDTO = array();
                for ($i = 0; $i < count($arrStrIds); $i++) {
                    $objMdPetIntTipoIntimacaoDTO = new MdPetIntTipoIntimacaoDTO();
                    $objMdPetIntTipoIntimacaoDTO->setNumIdMdPetIntTipoIntimacao($arrStrIds[$i]);
                    $arrObjMdPetIntTipoIntimacaoDTO[] = $objMdPetIntTipoIntimacaoDTO;
                }
                $objMdPetIntTipoIntimacaoRN = new MdPetIntTipoIntimacaoRN();
                $objMdPetIntTipoIntimacaoRN->excluir($arrObjMdPetIntTipoIntimacaoDTO);
                PaginaSEI::getInstance()->adicionarMensagem('Opera��o realizada com sucesso.');
            } catch (Exception $e) {
                PaginaSEI::getInstance()->processarExcecao($e);
            }
            header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao_origem'] . '&acao_origem=' . $_GET['acao']));
            die;

        case 'md_pet_int_tipo_intimacao_desativar':
            try {
                $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
                $arrObjMdPetIntTipoIntimacaoDTO = array();
                for ($i = 0; $i < count($arrStrIds); $i++) {
                    $objMdPetIntTipoIntimacaoDTO = new MdPetIntTipoIntimacaoDTO();
                    $objMdPetIntTipoIntimacaoDTO->setNumIdMdPetIntTipoIntimacao($arrStrIds[$i]);
                    $arrObjMdPetIntTipoIntimacaoDTO[] = $objMdPetIntTipoIntimacaoDTO;
                }
                $objMdPetIntTipoIntimacaoRN = new MdPetIntTipoIntimacaoRN();
                $objMdPetIntTipoIntimacaoRN->desativar($arrObjMdPetIntTipoIntimacaoDTO);
                PaginaSEI::getInstance()->adicionarMensagem('Opera��o realizada com sucesso.');
            } catch (Exception $e) {
                PaginaSEI::getInstance()->processarExcecao($e);
            }
            header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao_origem'] . '&acao_origem=' . $_GET['acao']));
            die;

        case 'md_pet_int_tipo_intimacao_reativar':
            $strTitulo = 'Reativar ';
            if ($_GET['acao_confirmada'] == 'sim') {
                try {
                    $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
                    $arrObjMdPetIntTipoIntimacaoDTO = array();
                    $idReativado = 0;
                    for ($i = 0; $i < count($arrStrIds); $i++) {
                        $objMdPetIntTipoIntimacaoDTO = new MdPetIntTipoIntimacaoDTO();
                        $objMdPetIntTipoIntimacaoDTO->setNumIdMdPetIntTipoIntimacao($arrStrIds[$i]);
                        $idReativado = $arrStrIds[$i];
                        $arrObjMdPetIntTipoIntimacaoDTO[] = $objMdPetIntTipoIntimacaoDTO;
                    }
                    $objMdPetIntTipoIntimacaoRN = new MdPetIntTipoIntimacaoRN();
                    $objMdPetIntTipoIntimacaoRN->reativar($arrObjMdPetIntTipoIntimacaoDTO);
                    PaginaSEI::getInstance()->adicionarMensagem('Opera��o realizada com sucesso.');
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
                $acaoLinhaAmarela = '';

                if( $idReativado != 0) {
                    $acaoLinhaAmarela = '&id_tipo_processo_peticionamento='. $idReativado.PaginaSEI::getInstance()->montarAncora($idReativado);
                }

                header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao_origem'] . '&acao_origem=' . $_GET['acao'] . $acaoLinhaAmarela));
                die;
            }
            break;

        case 'md_pet_int_tipo_intimacao_selecionar':
            $strTitulo = PaginaSEI::getInstance()->getTituloSelecao('Selecionar Tipos de Intima��o Eletr�nica', 'Selecionar Tipo de Intima��o Eletr�nica');

            //Se cadastrou alguem
            if ($_GET['acao_origem'] == 'md_pet_int_tipo_intimacao_cadastrar') {
                if (isset($_GET['id_md_pet_int_tipo_intimacao'])) {
                    PaginaSEI::getInstance()->adicionarSelecionado($_GET['id_md_pet_int_tipo_intimacao']);
                }
            }
            break;

        case 'md_pet_int_tipo_intimacao_listar':
            $strTitulo = 'Tipos de Intima��o Eletr�nica';
            break;

        case 'md_pet_int_tipo_intimacao_selecionar ':
            $strTitulo = 'Selecionar Tipos de Intima��o Eletr�nica';
            break;

        default:
            throw new InfraException("A��o '" . $_GET['acao'] . "' n�o reconhecida.");
    }

    $arrComandos = array();
    if ($_GET['acao'] == 'md_pet_int_tipo_intimacao_listar') {
        $bolAcaoCadastrar = SessaoSEI::getInstance()->verificarPermissao('md_pet_int_tipo_intimacao_cadastrar');
        $arrComandos[] = '<button type="submit" accesskey="P" id="sbmPesquisar" value="Pesquisar" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';
        $arrComandos[] = '<button type="button" accesskey="O" id="btnOrientacao" value="Orientacoes" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_orientacoes_tipo_destinatario&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao']) . '\'" class="infraButton"><span class="infraTeclaAtalho">O</span>rienta��es Tipo de Destinat�rio</button>';
        $arrComandos[] = '<button type="button" accesskey="T" id="btnNovo" value="Novo" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_int_tipo_resp_listar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao']) . '\'" class="infraButton"><span class="infraTeclaAtalho">T</span>ipos de Resposta</button>';
        if ($bolAcaoCadastrar) {
            $arrComandos[] = '<button type="button" accesskey="N" id="btnNov" value="Nov" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_int_tipo_intimacao_cadastrar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao']) . '\'" class="infraButton"><span class="infraTeclaAtalho">N</span>ovo</button>';
        }
    }

    if ($_GET['acao'] == 'md_pet_int_tipo_intimacao_selecionar'){
        $arrComandos[] = '<button type="button" accesskey="T" id="btnTransportarSelecao" value="Transportar" onclick="infraTransportarSelecao();" class="infraButton"><span class="infraTeclaAtalho">T</span>ransportar</button>';
    }

    if($_GET['acao'] == 'md_pet_int_tipo_intimacao_selecionar'){
        $arrComandos[] = '<button type="submit" accesskey="P" id="sbmPesquisar" value="Pesquisar" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';
    }


    $objMdPetIntTipoIntimacaoDTO = new MdPetIntTipoIntimacaoDTO();
    $objMdPetIntTipoIntimacaoDTO->retTodos(true);

    if ($_GET['acao'] == 'md_pet_int_tipo_intimacao_reativar') {
        //Lista somente inativos
        $objMdPetIntTipoIntimacaoDTO->setBolExclusaoLogica(false);
        $objMdPetIntTipoIntimacaoDTO->setStrSinAtivo('N');
    }

    $objMdPetIntTipoIntimacaoDTO->setStrSinAtivo(array('S', 'N') , InfraDTO::$OPER_IN);

    if(!empty($_POST)){
        if ($_POST['txtTipoIntimacao'] != '') {
            $objMdPetIntTipoIntimacaoDTO->setStrNome('%'.$_POST['txtTipoIntimacao'].'%',InfraDTO::$OPER_LIKE);
        }
    }

    PaginaSEI::getInstance()->prepararOrdenacao($objMdPetIntTipoIntimacaoDTO, 'Nome', InfraDTO::$TIPO_ORDENACAO_ASC);
    PaginaSEI::getInstance()->prepararPaginacao($objMdPetIntTipoIntimacaoDTO, 200);

    $objMdPetIntTipoIntimacaoRN = new MdPetIntTipoIntimacaoRN();
    $arrObjMdPetIntTipoIntimacaoDTO = $objMdPetIntTipoIntimacaoRN->listar($objMdPetIntTipoIntimacaoDTO);

    PaginaSEI::getInstance()->processarPaginacao($objMdPetIntTipoIntimacaoDTO);

    $numRegistros = count($arrObjMdPetIntTipoIntimacaoDTO);

    if ($numRegistros != 0) {

        $bolCheck = false;
        if ($_GET['acao'] == 'md_pet_int_tipo_intimacao_selecionar') {
            $bolCheck         = true;
            $bolAcaoReativar  = false;
            $bolAcaoConsultar = false;
            $bolAcaoAlterar   = false;
            $bolAcaoImprimir  = false;
            $bolAcaoExcluir   = false;
            $bolAcaoDesativar = false;
        } else if ($_GET['acao'] == 'md_pet_int_tipo_intimacao_reativar') {
            $bolAcaoReativar = SessaoSEI::getInstance()->verificarPermissao('md_pet_int_tipo_intimacao_reativar');
            $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pet_int_tipo_intimacao_consultar');
            $bolAcaoAlterar = false;
            $bolAcaoImprimir = true;
            $bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('md_pet_int_tipo_intimacao_excluir');
            $bolAcaoDesativar = false;
        } else {
            $strTitulo = 'Tipos de Intima��o Eletr�nica';
            $bolAcaoReativar = true;
            $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pet_int_tipo_intimacao_consultar');
            $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('md_pet_int_tipo_intimacao_alterar');
            $bolAcaoImprimir = true;
            $bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('md_pet_int_tipo_intimacao_excluir');
            $bolAcaoDesativar = SessaoSEI::getInstance()->verificarPermissao('md_pet_int_tipo_intimacao_desativar');
        }

        if ($bolAcaoDesativar) {
            $bolCheck = true;
            $strLinkDesativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_int_tipo_intimacao_desativar&acao_origem=' . $_GET['acao']);
        }

        if ($bolAcaoReativar) {
            $bolCheck = true;
            $strLinkReativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_int_tipo_intimacao_reativar&acao_origem=' . $_GET['acao'] . '&acao_confirmada=sim');
        }

        if ($bolAcaoExcluir) {
            $bolCheck = true;
            $strLinkExcluir = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_int_tipo_intimacao_excluir&acao_origem=' . $_GET['acao']);
        }

        if ($bolAcaoImprimir) {
            $bolCheck = true;
            $arrComandos[] = '<button type="button" accesskey="I" id="btnImprimir" value="Imprimir" onclick="infraImprimirTabela();" class="infraButton"><span class="infraTeclaAtalho">I</span>mprimir</button>';
        }

        $strResultado = '';

        if ($_GET['acao'] != 'md_pet_int_tipo_intimacao_reativar') {
            $strSumarioTabela = 'Tabela de .';
            $strCaptionTabela = 'Tipos de Intima��o Eletr�nica';
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
        $strResultado .= '<th class="infraTh">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetIntTipoIntimacaoDTO, 'Tipo de Intima��o Eletr�nica', 'Nome', $arrObjMdPetIntTipoIntimacaoDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh" style="text-align: left; padding-left: 6px;">Tipo de Resposta</th>' . "\n";
        $strResultado .= '<th class="infraTh">A��es</th>' . "\n";
        $strResultado .= '</tr>' . "\n";
        $strCssTr = '';

        for ($i = 0; $i < $numRegistros; $i++) {

            $linha = '';

            if( $arrObjMdPetIntTipoIntimacaoDTO[$i]->getStrSinAtivo()=='S' ){
                $strCssTr = ($strCssTr=='<tr class="infraTrClara">')?'<tr class="infraTrEscura">':'<tr class="infraTrClara">';
            } else {
                $strCssTr ='<tr class="trVermelha">';
            }

            $linha .= $strCssTr;

            if ($bolCheck) {
                $linha .= '<td valign="top">' . PaginaSEI::getInstance()->getTrCheck($i, $arrObjMdPetIntTipoIntimacaoDTO[$i]->getNumIdMdPetIntTipoIntimacao(), $arrObjMdPetIntTipoIntimacaoDTO[$i]->getStrNome()) . '</td>';
            }
            $linha .= '<td>' . PaginaSEI::tratarHTML($arrObjMdPetIntTipoIntimacaoDTO[$i]->getStrNome()) . '</td>';

            $objMdPetIntRelIntimRespDTO = new MdPetIntRelIntimRespDTO();
            $objMdPetIntRelIntimRespDTO->setNumIdMdPetIntTipoIntimacao($arrObjMdPetIntTipoIntimacaoDTO[$i]->getNumIdMdPetIntTipoIntimacao());
            $objMdPetIntRelIntimRespDTO->retTodos(true);

            $objMdPetIntRelIntimRespRN = new MdPetIntRelIntimRespRN();
            $arrObjMdPetIntRelIntimRespDTO = $objMdPetIntRelIntimRespRN->listar($objMdPetIntRelIntimRespDTO);

            $linha .= '<td>';
            if (count($arrObjMdPetIntRelIntimRespDTO) == 0) {
                if( $arrObjMdPetIntTipoIntimacaoDTO[$i]->getStrTipoRespostaAceita() == 'S' ){
                    $montaSelectTipoResposta[9998] = "Sem Resposta";
                    $linha .= "Sem Resposta";
                    $id = 9998;
                }
            } else if (count($arrObjMdPetIntRelIntimRespDTO) == 1) {
                for ($x = 0; $x < count($arrObjMdPetIntRelIntimRespDTO); $x++) {
                    if ($arrObjMdPetIntRelIntimRespDTO[$x]->getStrTipoPrazoExternoMdPetIntTipoResp() == 'N') {
                        $prazo = '(N�o Possui Prazo Externo)';
                    } else{
                        $prazo = '(' . $arrObjMdPetIntRelIntimRespDTO[$x]->getNumValorPrazoExternoMdPetIntTipoResp();
                        if($arrObjMdPetIntRelIntimRespDTO[$x]->getStrTipoPrazoExternoMdPetIntTipoResp() == 'D'){
                            $tipoDia = null;
                            if($arrObjMdPetIntRelIntimRespDTO[$x]->getStrTipoDia() == 'U'){
                                if($arrObjMdPetIntRelIntimRespDTO[$x]->getNumValorPrazoExternoMdPetIntTipoResp() > 1){
                                    $tipoDia = ' �teis';
                                }else{
                                    $tipoDia = ' �til';
                                }
                            }
                            $prazo .= $arrObjMdPetIntRelIntimRespDTO[$x]->getNumValorPrazoExternoMdPetIntTipoResp() > 1 ?  ' Dias'.$tipoDia.')' : ' Dia'.$tipoDia.')';
                        }else if($arrObjMdPetIntRelIntimRespDTO[$x]->getStrTipoPrazoExternoMdPetIntTipoResp() == 'M'){
                            $prazo .= $arrObjMdPetIntRelIntimRespDTO[$x]->getNumValorPrazoExternoMdPetIntTipoResp() > 1 ?  ' Meses)' : ' M�s)';
                        }else if($arrObjMdPetIntRelIntimRespDTO[$x]->getStrTipoPrazoExternoMdPetIntTipoResp() == 'A'){
                            $prazo .= $arrObjMdPetIntRelIntimRespDTO[$x]->getNumValorPrazoExternoMdPetIntTipoResp() > 1 ?  ' Anos)' : ' Ano)';
                        }
                    }

                    if ($arrObjMdPetIntRelIntimRespDTO[$x]->getStrTipoRespostaAceitaMdPetIntTipoResp() == 'E') {
                        $resposta = 'Exige Resposta';
                    } else {
                        $resposta = 'Resposta Facultativa';
                    }

                    $tipoResposta = $arrObjMdPetIntRelIntimRespDTO[$x]->getStrNomeMdPetIntTipoResp() . ' ' . $prazo . ' - ' . $resposta;
                    $linha .= PaginaSEI::tratarHTML($tipoResposta);

                    $montaSelectTipoResposta[$arrObjMdPetIntRelIntimRespDTO[$x]->getNumIdMdPetIntTipoRespMdPetIntTipoResp()] = $tipoResposta;
                    $id = (int)$arrObjMdPetIntRelIntimRespDTO[$x]->getNumIdMdPetIntTipoRespMdPetIntTipoResp();
                }
            } else {

                if( $arrObjMdPetIntTipoIntimacaoDTO[$i]->getStrTipoRespostaAceita() == 'E' ){
                    $strTipoRespostaComplemento = "Exige resposta";
                } else if( $arrObjMdPetIntTipoIntimacaoDTO[$i]->getStrTipoRespostaAceita() == 'F' ){
                    $strTipoRespostaComplemento = "Facultativa";
                }

                $montaSelectTipoResposta[9999] = 'M�ltiplos';
                $linha .= 'M�ltiplos - ' . $strTipoRespostaComplemento;
            }

            $linha .= '</td>';
            $linha .= '<td align="center">';
            $linha .= PaginaSEI::getInstance()->getAcaoTransportarItem($i, $arrObjMdPetIntTipoIntimacaoDTO[$i]->getNumIdMdPetIntTipoIntimacao());

            if ($bolAcaoConsultar) {
                $linha .= '<a href="' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_int_tipo_intimacao_consultar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&id_md_pet_int_tipo_intimacao=' . $arrObjMdPetIntTipoIntimacaoDTO[$i]->getNumIdMdPetIntTipoIntimacao()) . '" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioImagensGlobal() . '/consultar.gif" title="Consultar Tipo de Intima��o Eletronica" alt="Consultar " class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoAlterar) {
                $linha .= '<a href="' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_int_tipo_intimacao_alterar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&id_md_pet_int_tipo_intimacao=' . $arrObjMdPetIntTipoIntimacaoDTO[$i]->getNumIdMdPetIntTipoIntimacao()) . '" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioImagensGlobal() . '/alterar.gif" title="Alterar Tipo de Intima��o Eletronica" alt="Alterar " class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoDesativar || $bolAcaoReativar || $bolAcaoExcluir) {
                $strId = $arrObjMdPetIntTipoIntimacaoDTO[$i]->getNumIdMdPetIntTipoIntimacao();
                $strDescricao = PaginaSEI::getInstance()->formatarParametrosJavaScript(PaginaSEI::tratarHTML($arrObjMdPetIntTipoIntimacaoDTO[$i]->getStrNome()));
            }

            if ($bolAcaoDesativar && $arrObjMdPetIntTipoIntimacaoDTO[$i]->getStrSinAtivo() == 'S') {
                $linha .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="acaoDesativar(\'' . $strId . '\',\'' . $strDescricao . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioImagensGlobal() . '/desativar.gif" title="Desativar Tipo de Intima��o Eletronica" alt="Desativar " class="infraImg" /></a>&nbsp;';
            }else{
                if($bolAcaoReativar) {
                    $linha .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="acaoReativar(\'' . $strId . '\',\'' . $strDescricao . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioImagensGlobal() . '/reativar.gif" title="Reativar Tipo de Intima��o Eletronica" alt="Reativar " class="infraImg" /></a>&nbsp;';
                }
            }

            if ($bolAcaoExcluir) {
                $linha .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="acaoExcluir(\'' . $strId . '\',\'' . $strDescricao . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioImagensGlobal() . '/excluir.gif" title="Excluir Tipo de Intima��o Eletronica" alt="Excluir " class="infraImg" /></a>&nbsp;';
            }

            $linha .= '</td></tr>' . "\n";

            if($_POST['selTipoResposta'] == 'null' || is_null($_POST['selTipoResposta'])){
                $strResultado .= $linha;
            }else if(count($arrObjMdPetIntRelIntimRespDTO) == 0 || count($arrObjMdPetIntRelIntimRespDTO) == 1){
                if($_POST['selTipoResposta'] == $id)
                    $strResultado .= $linha;
            }else if(count($arrObjMdPetIntRelIntimRespDTO) > 1 && $_POST['selTipoResposta'] == 9999){
                $strResultado .= $linha;
            }
        }
        $strResultado .= '</table>';
        $strTipoResposta = MdPetIntTipoIntimacaoINT::montaSelectTipoResposta8612($montaSelectTipoResposta, (int)$_POST['selTipoResposta']);

    } else {
        if ($_GET['acao'] != 'md_pet_int_tipo_intimacao_selecionar') {
            $arrComandos[] = '<button type="button" accesskey="I" id="btnImprimir" value="Imprimir" onclick="infraImprimirTabela();" class="infraButton"><span class="infraTeclaAtalho">I</span>mprimir</button>';
        }
    }
    if ($_GET['acao'] == 'md_pet_int_tipo_intimacao_selecionar') {
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
?>
    #lblTipoIntimacao {position:absolute;left:0%;top:25%;width:20%;}
    #txtTipoIntimacao {position:absolute;left:0%;top:50%;width:20%;}

    #lblTipodeResposta {position:absolute;left:23%;top:25%;width:30%;}
    #selTipoResposta {position:absolute;left:23%;top:50%;width:20%;}
<?
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>

    function inicializar() {
    if ('<?=$_GET['acao']?>' == 'md_pet_int_tipo_intimacao_selecionar') {
    infraReceberSelecao();
    document.getElementById('btnFecharSelecao').focus();
    } else {
    document.getElementById('btnFechar').focus();
    }

    infraEfeitoTabelas();
    }

<? if ($bolAcaoDesativar){ ?>
    function acaoDesativar(id, desc) {
    desc = $("<pre>").html(desc).text();
    if (confirm("Confirma desativa��o do Tipo de Intima��o Eletr�nica  \"" + desc + "\"?")) {
    document.getElementById('hdnInfraItemId').value = id;
    document.getElementById('frmMdPetIntTipoIntimacaoLista').action = '<?=$strLinkDesativar?>';
    document.getElementById('frmMdPetIntTipoIntimacaoLista').submit();
    }
    }

    function acaoDesativacaoMultipla() {
    if (document.getElementById('hdnInfraItensSelecionados').value == '') {
    alert('Nenhuma  selecionada.');
    return;
    }
    if (confirm("Confirma desativa��o ds  selecionads?")) {
    document.getElementById('hdnInfraItemId').value = '';
    document.getElementById('frmMdPetIntTipoIntimacaoLista').action = '<?=$strLinkDesativar?>';
    document.getElementById('frmMdPetIntTipoIntimacaoLista').submit();
    }
    }
<? } ?>

<? if ($bolAcaoReativar){ ?>
    function acaoReativar(id, desc) {
    desc = $("<pre>").html(desc).text();
    if (confirm("Confirma reativa��o do Tipo de Intima��o Eletr�nica  \"" + desc + "\"?")) {
    document.getElementById('hdnInfraItemId').value = id;
    document.getElementById('frmMdPetIntTipoIntimacaoLista').action = '<?=$strLinkReativar?>';
    document.getElementById('frmMdPetIntTipoIntimacaoLista').submit();
    }
    }

    function acaoReativacaoMultipla() {
    if (document.getElementById('hdnInfraItensSelecionados').value == '') {
    alert('Nenhuma  selecionada.');
    return;
    }
    if (confirm("Confirma reativa��o ds  selecionads?")) {
    document.getElementById('hdnInfraItemId').value = '';
    document.getElementById('frmMdPetIntTipoIntimacaoLista').action = '<?=$strLinkReativar?>';
    document.getElementById('frmMdPetIntTipoIntimacaoLista').submit();
    }
    }
<? } ?>

<? if ($bolAcaoExcluir){ ?>
    function acaoExcluir(id, desc) {
    desc = $("<pre>").html(desc).text();
    if (confirm("Confirma exclus�o do Tipo de Intima��o Eletr�nica \"" + desc + "\"?")) {
    document.getElementById('hdnInfraItemId').value = id;
    document.getElementById('frmMdPetIntTipoIntimacaoLista').action = '<?=$strLinkExcluir?>';
    document.getElementById('frmMdPetIntTipoIntimacaoLista').submit();
    }
    }

    function acaoExclusaoMultipla() {
    if (document.getElementById('hdnInfraItensSelecionados').value == '') {
    alert('Nenhuma  selecionada.');
    return;
    }
    if (confirm("Confirma exclus�o ds  selecionads?")) {
    document.getElementById('hdnInfraItemId').value = '';
    document.getElementById('frmMdPetIntTipoIntimacaoLista').action = '<?=$strLinkExcluir?>';
    document.getElementById('frmMdPetIntTipoIntimacaoLista').submit();
    }
    }
<? } ?>

<?
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
    <form id="frmMdPetIntTipoIntimacaoLista" method="post" action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">
        <? PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
        PaginaSEI::getInstance()->abrirAreaDados('6em');
        ?>
        <label id="lblTipoIntimacao" for="txtTipoIntimacao" accesskey="" class="infraLabelOpcional">Tipo de Intima��o:</label>
        <input type="text" id="txtTipoIntimacao" name="txtTipoIntimacao" class="infraText" maxlength="100" value="<? echo (PaginaSEI::tratarHTML($_POST['txtTipoIntimacao']) != '' ? PaginaSEI::tratarHTML($_POST['txtTipoIntimacao']) : '' )?>" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>

        <label id="lblTipodeResposta" for="lblTipodeResposta" accesskey="" class="infraLabelOpcional">Tipo de Resposta:</label>
        <select id="selTipoResposta" name="selTipoResposta" onchange="this.form.submit()" class="infraSelect" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>">
            <?=$strTipoResposta?>
        </select>

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