<?
/**
 * ANATEL
 *
 * 15/06/2016 - criado por marcelo.bezerra - CAST
 *
 */

try {

    require_once dirname(__FILE__).'/../../SEI.php';
    session_start();
    PaginaSEI::getInstance()->setBolXHTML(false);

    //////////////////////////////////////////////////////////////////////////////
    //InfraDebug::getInstance()->setBolLigado(false);
    //InfraDebug::getInstance()->setBolDebugInfra(true);
    //InfraDebug::getInstance()->limpar();
    //////////////////////////////////////////////////////////////////////////////

    SessaoSEI::getInstance()->validarLink();

    PaginaSEI::getInstance()->verificarSelecao('menu_peticionamento_usuario_externo_selecionar');

    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    $arrComandos = array();

    $disabled = '';

    switch($_GET['acao']){

        case 'md_pet_menu_usu_ext_cadastrar':

            $strTitulo = 'Novo Menu';
            $arrComandos[] = '<button type="submit" accesskey="s" name="sbmCadastrarOrientacoesPetIndisp" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'])).'\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            $objEditorRN=new EditorRN();
            $objEditorDTO=new EditorDTO();

            $objEditorDTO->setStrNomeCampo('txaConteudo');
            $objEditorDTO->setStrSinSomenteLeitura('N');
            $objEditorDTO->setNumTamanhoEditor(400);
            $retEditor = $objEditorRN->montarSimples($objEditorDTO);

            $txtConteudo = $_POST['txaConteudo'];
            $txtUrl = $_POST['txtUrl'];
            $txtNome = $_POST['txtNome'];
            $tipo = $_POST['tipo'];

            $objMdPetMenuUsuarioExternoDTO = new MdPetMenuUsuarioExternoDTO();
            $objMdPetMenuUsuarioExternoDTO->setStrConteudoHtml('');

            if (isset($_POST['hdnIdMenuPeticionamentoUsuarioExterno'])) {

                try{

                    if( $_POST['tipo'] == MdPetMenuUsuarioExternoRN::$TP_EXTERNO ){
                        $_POST['txaConteudo'] = '';
                    }

                    if( $_POST['tipo'] == MdPetMenuUsuarioExternoRN::$TP_CONTEUDO_HTML ){
                        $_POST['txtUrl'] = '';
                    }

                    $objMdPetMenuUsuarioExternoDTO->setStrConteudoHtml($_POST['txaConteudo']);

                    //Estilo
                    $conjuntoEstilosRN = new ConjuntoEstilosRN();
                    $conjuntoEstilosDTO = new ConjuntoEstilosDTO();
                    $conjuntoEstilosDTO->setStrSinUltimo('S');
                    $conjuntoEstilosDTO->retNumIdConjuntoEstilos();
                    $conjuntoEstilosDTO = $conjuntoEstilosRN->consultar( $conjuntoEstilosDTO );
                    $objMdPetMenuUsuarioExternoDTO->setNumIdConjuntoEstilos( $conjuntoEstilosDTO->getNumIdConjuntoEstilos() );

                    $objMdPetMenuUsuarioExternoDTO->setStrUrl($_POST['txtUrl']);
                    $objMdPetMenuUsuarioExternoDTO->setStrNome($_POST['txtNome']);
                    $objMdPetMenuUsuarioExternoDTO->setStrTipo($_POST['tipo']);
                    $objMdPetMenuUsuarioExternoRN  = new MdPetMenuUsuarioExternoRN();
                    $objMdPetMenuUsuarioExternoDTO = $objMdPetMenuUsuarioExternoRN->cadastrar($objMdPetMenuUsuarioExternoDTO);
                    header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'].'&id_menu_peticionamento_usuario_externo='.$objMdPetMenuUsuarioExternoDTO->getNumIdMenuPeticionamentoUsuarioExterno()));
                    die;

                } catch(Exception $e){
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }
            break;

        case 'md_pet_menu_usu_ext_consultar':

            $disabled = '';
            $strTitulo = 'Consultar Menu';
            $disabled = " disabled='disabled' ";

            //TODO: Marcelo ou Herley, a construção dos Cases Alterar e Consultar desta funcionalidade ficou muito diferente da forma que foi construído para Tipos de Processos para Peticionamento e para Indisponibilidades do SEI. Tem que padronizar, para ficar igual as outras duas funcionalidades. Ainda, Consultar tem o botão "Fechar", enquanto que Novo e Alterar tem o botão "Cancelar".
            $arrComandos[] = '<button type="button" accesskey="c" name="btnFechar" id="btnFechar" value="Fechar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'].PaginaSEI::getInstance()->montarAncora($_GET['id_menu_peticionamento_usuario_externo']))).'\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

            $objEditorRN=new EditorRN();
            $objEditorDTO=new EditorDTO();

            $objEditorDTO->setStrNomeCampo('txaConteudo');
            $objEditorDTO->setStrSinSomenteLeitura('N');
            $objEditorDTO->setNumTamanhoEditor(400);
            $retEditor = $objEditorRN->montarSimples($objEditorDTO);

            $objMdPetMenuUsuarioExternoDTO2 = new MdPetMenuUsuarioExternoDTO();
            $objMdPetMenuUsuarioExternoDTO2->retTodos();

            $objMdPetMenuUsuarioExternoRN  = new MdPetMenuUsuarioExternoRN();

            if ( !isset($_POST['hdnIdMenuPeticionamentoUsuarioExterno'])) {
                $objMdPetMenuUsuarioExternoDTO2->setNumIdMenuPeticionamentoUsuarioExterno( $_GET['id_menu_peticionamento_usuario_externo'] );
                $objLista = $objMdPetMenuUsuarioExternoRN->consultar($objMdPetMenuUsuarioExternoDTO2);

                $txtNome = $objLista->getStrNome();
                $tipo = $objLista->getStrTipo();
                $txtConteudo = $objLista->getStrConteudoHtml();
                $txtUrl = $objLista->getStrUrl();
                $sinAtivo = $objLista->getStrSinAtivo();
            } else {
                try{

                    $objMdPetMenuUsuarioExternoDTO2->setNumIdMenuPeticionamentoUsuarioExterno( $_POST['hdnIdMenuPeticionamentoUsuarioExterno'] );
                    $objMdPetMenuUsuarioExternoDTO2 = $objMdPetMenuUsuarioExternoRN->consultar($objMdPetMenuUsuarioExternoDTO2);

                    $txtNome = $_POST['txtNome'];
                    $tipo = $_POST['tipo'];
                    $txtConteudo = $_POST['txaConteudo'];
                    $txtUrl = $_POST['txtUrl'];

                    if( $_POST['tipo'] == MdPetMenuUsuarioExternoRN::$TP_EXTERNO ){
                        $_POST['txaConteudo'] = '';
                    }

                    if( $_POST['tipo'] == MdPetMenuUsuarioExternoRN::$TP_CONTEUDO_HTML ){
                        $_POST['txtUrl'] = '';
                    }

                    $objMdPetMenuUsuarioExternoDTO2->setNumIdMenuPeticionamentoUsuarioExterno( $_POST['hdnIdMenuPeticionamentoUsuarioExterno'] );
                    $objMdPetMenuUsuarioExternoDTO2->setStrConteudoHtml($_POST['txaConteudo']);

                    //Estilo
                    $conjuntoEstilosRN = new ConjuntoEstilosRN();
                    $conjuntoEstilosDTO = new ConjuntoEstilosDTO();
                    $conjuntoEstilosDTO->setStrSinUltimo('S');
                    $conjuntoEstilosDTO->retNumIdConjuntoEstilos();
                    $conjuntoEstilosDTO = $conjuntoEstilosRN->consultar( $conjuntoEstilosDTO );
                    $objMdPetMenuUsuarioExternoDTO2->setNumIdConjuntoEstilos( $conjuntoEstilosDTO->getNumIdConjuntoEstilos() );

                    $objMdPetMenuUsuarioExternoDTO2->setStrUrl($_POST['txtUrl']);
                    $objMdPetMenuUsuarioExternoDTO2->setStrNome($_POST['txtNome']);
                    $objMdPetMenuUsuarioExternoDTO2->setStrTipo($_POST['tipo']);

                    $objMdPetMenuUsuarioExternoDTO =  $objMdPetMenuUsuarioExternoRN->alterar($objMdPetMenuUsuarioExternoDTO2);

                    header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'] . '&id_menu_peticionamento_usuario_externo=' . $_POST['hdnIdMenuPeticionamentoUsuarioExterno']));

                    die;

                } catch(Exception $e){
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }

            break;

        case 'md_pet_menu_usu_ext_alterar':

            $disabled = '';
            $strTitulo = 'Alterar Menu';
            $disabled = '';

            $arrComandos[] = '<button type="submit" accesskey="s" name="sbmCadastrarOrientacoesPetIndisp" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';

            //TODO: Marcelo ou Herley, a construção dos Cases Alterar e Consultar desta funcionalidade ficou muito diferente da forma que foi construído para Tipos de Processos para Peticionamento e para Indisponibilidades do SEI. Tem que padronizar, para ficar igual as outras duas funcionalidades. Ainda, Consultar tem o botão "Fechar", enquanto que Novo e Alterar tem o botão "Cancelar".
            $arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'].PaginaSEI::getInstance()->montarAncora($_GET['id_menu_peticionamento_usuario_externo']))).'\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            $objEditorRN=new EditorRN();
            $objEditorDTO=new EditorDTO();

            $objEditorDTO->setStrNomeCampo('txaConteudo');
            $objEditorDTO->setStrSinSomenteLeitura('N');
            $objEditorDTO->setNumTamanhoEditor(400);
            $retEditor = $objEditorRN->montarSimples($objEditorDTO);

            $objMdPetMenuUsuarioExternoDTO2 = new MdPetMenuUsuarioExternoDTO();
            $objMdPetMenuUsuarioExternoDTO2->retTodos();

            $objMdPetMenuUsuarioExternoRN  = new MdPetMenuUsuarioExternoRN();

            if ( !isset($_POST['hdnIdMenuPeticionamentoUsuarioExterno'])) {
                $objMdPetMenuUsuarioExternoDTO2->setNumIdMenuPeticionamentoUsuarioExterno( $_GET['id_menu_peticionamento_usuario_externo'] );
                $objLista = $objMdPetMenuUsuarioExternoRN->consultar($objMdPetMenuUsuarioExternoDTO2);

                $txtNome = $objLista->getStrNome();
                $tipo = $objLista->getStrTipo();
                $txtConteudo = $objLista->getStrConteudoHtml();
                $txtUrl = $objLista->getStrUrl();
                $sinAtivo = $objLista->getStrSinAtivo();
            } else {
                try{

                    $objMdPetMenuUsuarioExternoDTO2->setNumIdMenuPeticionamentoUsuarioExterno( $_POST['hdnIdMenuPeticionamentoUsuarioExterno'] );
                    $objMdPetMenuUsuarioExternoDTO2 = $objMdPetMenuUsuarioExternoRN->consultar($objMdPetMenuUsuarioExternoDTO2);

                    $txtNome = $_POST['txtNome'];
                    $tipo = $_POST['tipo'];
                    $txtConteudo = $_POST['txaConteudo'];
                    $txtUrl = $_POST['txtUrl'];

                    if( $_POST['tipo'] == MdPetMenuUsuarioExternoRN::$TP_EXTERNO ){
                        $_POST['txaConteudo'] = '';
                    }

                    if( $_POST['tipo'] == MdPetMenuUsuarioExternoRN::$TP_CONTEUDO_HTML ){
                        $_POST['txtUrl'] = '';
                    }

                    $objMdPetMenuUsuarioExternoDTO2->setNumIdMenuPeticionamentoUsuarioExterno( $_POST['hdnIdMenuPeticionamentoUsuarioExterno'] );
                    $objMdPetMenuUsuarioExternoDTO2->setStrConteudoHtml($_POST['txaConteudo']);

                    //Estilo
                    $conjuntoEstilosRN = new ConjuntoEstilosRN();
                    $conjuntoEstilosDTO = new ConjuntoEstilosDTO();
                    $conjuntoEstilosDTO->setStrSinUltimo('S');
                    $conjuntoEstilosDTO->retNumIdConjuntoEstilos();
                    $conjuntoEstilosDTO = $conjuntoEstilosRN->consultar( $conjuntoEstilosDTO );
                    $objMdPetMenuUsuarioExternoDTO2->setNumIdConjuntoEstilos( $conjuntoEstilosDTO->getNumIdConjuntoEstilos() );

                    $objMdPetMenuUsuarioExternoDTO2->setStrUrl($_POST['txtUrl']);
                    $objMdPetMenuUsuarioExternoDTO2->setStrNome($_POST['txtNome']);
                    $objMdPetMenuUsuarioExternoDTO2->setStrTipo($_POST['tipo']);

                    $objMdPetMenuUsuarioExternoDTO =  $objMdPetMenuUsuarioExternoRN->alterar($objMdPetMenuUsuarioExternoDTO2);

                    header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'] . '&id_menu_peticionamento_usuario_externo=' . $_POST['hdnIdMenuPeticionamentoUsuarioExterno']));

                    die;

                } catch(Exception $e){
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }

            break;

        default:
            throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
            break;

    }
}
catch(Exception $e){
    PaginaSEI::getInstance()->processarExcecao($e);
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: '.PaginaSEI::getInstance()->getStrNomeSistema().' - '.$strTitulo.' ::');
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
require_once 'md_pet_menu_usu_ext_cadastro_css.php';
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
echo $retEditor->getStrInicializacao();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
?>

    <form id="frmTextoPadraoInternoCadastro" method="post" onsubmit="return OnSubmitForm();" action="<?=PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">
        <?
        PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);


        PaginaSEI::getInstance()->abrirAreaDados('98%');
        ?>
        <div id="divGeral" class="infraAreaDados">
            <div class="row">
                <div class="col-sm-12 col-md-6 col-lg-6">
                    <label id="lblTipoProcesso" for="txtTipoProcesso" class="infraLabelObrigatorio">
                        Nome do Menu:
                    </label>
                    <img id="imgAjudatTipoExterno"
                         src="<?=PaginaSEI::getInstance()->getDiretorioSvgGlobal()?>/ajuda.svg?<?= Icone::VERSAO ?>" alt=""
                         onmouseover="return infraTooltipMostrar('O menu será listado para o Usuário Externo depois que ele fizer login no Acesso Externo do SEI.', 'Ajuda');"
                         onmouseout="return infraTooltipOcultar();" class="infraImgModulo">
                    <div class="input-group mb-3">
                        <input type="text" id="txtNome" name="txtNome" class="infraText form-control"
                               maxlength="30" <?= $disabled ?> value="<?= PaginaSEI::tratarHTML($txtNome) ?>">
                    </div>
                </div>
            </div>

            <div class="row rowFieldSet">
                <div class="col-sm-12 col-md-10 col-lg-10">
                    <fieldset class="infraFieldset fieldsetCadastroMenu form-control">
                        <legend class="infraLegend">&nbsp;Tipo de Menu &nbsp;</legend>
                        <div id="divOptTipoExterno" class="infraDivRadio divOptTipoExterno">
                            <input type="radio" id="tipoExterno" <?= $disabled ?> name="tipo" value="E" onclick="rdTipo()" <?php if( $tipo == 'E' ){ echo " checked='checked' "; } ?>  class="infraRadio">
                            <label for="tipoExterno" class="infraLabelRadio"> Link Externo
                                <img id="imgAjudatTipoExterno" src="<?=PaginaSEI::getInstance()->getDiretorioSvgGlobal()?>/ajuda.svg?<?= Icone::VERSAO ?>" alt="" onmouseover="return infraTooltipMostrar('O menu abrirá o link externo sempre em nova janela do navegador do Usuário Externo logado.', 'Ajuda');" onmouseout="return infraTooltipOcultar();" class="infraImgModulo">
                            </label>
                        </div>

                        <div id="divOptTipoHTML" class="infraDivRadio divOptTipoExterno">
                            <input type="radio" id="tipoHTML" name="tipo" <?= $disabled ?> value="H" onclick="rdTipo()" <?php if( $tipo == 'H' ){ echo " checked='checked' "; } ?>  class="infraRadio">
                            <label for="tipoHTML" class="infraLabelRadio"> Conteúdo HTML
                                <img id="imgAjudatTipoHTML" src="<?=PaginaSEI::getInstance()->getDiretorioSvgGlobal()?>/ajuda.svg?<?= Icone::VERSAO ?>" alt="" onmouseover="return infraTooltipMostrar('O menu abrirá tela no próprio SEI para o Usuário Externo logado com o texto HTML parametrizado.', 'Ajuda');" onmouseout="return infraTooltipOcultar();" class="infraImgModulo">
                            </label>
                        </div>
                    </fieldset>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-10 col-lg-10">
                    <div id="divTxtUrl" class="infraDivRadio divTxtUrl">
                        <label id="lblUrl" for="txtUrl" class="infraLabelObrigatorio">URL do Link Externo:</label>
                        <input type="text" id="txtUrl" name="txtUrl" maxlength="2083" class="infraText form-control" <?= $disabled ?> value="<?= PaginaSEI::tratarHTML($txtUrl) ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-10 col-lg-10">
                    <div id="divTbConteudo" class="infraDivRadio divTbConteudo">
                        <table id="tbConteudo">
                            <td style="width: 95%">
                                <div id="divEditores" style="">
                                    <label id="lblConteudo" for="txaConteudo" class="infraLabelObrigatorio">Conteúdo HTML:</label>
                                    <textarea id="txaConteudo" name="txaConteudo" class="infraTextarea" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"><?=PaginaSEI::tratarHTML($txtConteudo)?></textarea>
                                    <script type="text/javascript">
                                        <?=$retEditor->getStrEditores();?>
                                    </script>
                                </div>
                            </td>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <?php
        PaginaSEI::getInstance()->fecharAreaDados();
        ?>

        <input type="hidden" id="hdnIdMenuPeticionamentoUsuarioExterno" name="hdnIdMenuPeticionamentoUsuarioExterno"
               value="<?php echo isset($_GET['id_menu_peticionamento_usuario_externo']) ? $_GET['id_menu_peticionamento_usuario_externo'] : '' ?>" />

    </form>

<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
require_once 'md_pet_menu_usu_ext_cadastro_js.php';
?>