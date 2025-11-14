<?
/**
* ANATEL
*
* 30/08/2016 - criado por jaqueline.mendes@castgroup.com.br - CAST GROUP
*
*/

try {
  require_once dirname(__FILE__).'/../../SEI.php';

  session_start();

  SessaoSEI::getInstance()->validarLink();

  PaginaSEI::getInstance()->verificarSelecao('md_pet_hipotese_legal_nl_acesso_cadastrar');

  SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

  $objHipoteseLegal = new HipoteseLegalDTO();

  $strDesabilitar = '';

  $arrComandos = array();
  $strLinkHipoteseLglSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_hipotese_legal_selecionar&tipo_selecao=2&id_object=objLupaHipLegal&nvl_acesso='.ProtocoloRN::$NA_RESTRITO);
  $strLinkAjaxHipLegal       = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_hipotese_rest_auto_completar');

    switch ($_GET['acao']) {
        case 'md_pet_hipotese_legal_nl_acesso_cadastrar':

            $strTitulo = 'Peticionamento - Hipóteses Legais Permitidas';

            $arrComandos[] = '<button type="submit" accesskey="s" name="sbmCadastrarHipoteseLegalRI" id="sbmCadastrarHipoteseLegalRI" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $arrComandos[] = '<button type="button" accesskey="c" name="btnFechar" id="btnFechar" value="Cancelar" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=procedimento_controlar&acao_origem=' . $_GET['acao'])) . '\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

            $arrObjHipoteseLgl = "";

            $objMdPetHipoteseLegalRN = new MdPetHipoteseLegalRN();
            $objMdPetHipoteseLegalDTOAll = new MdPetHipoteseLegalDTO();
            $objMdPetHipoteseLegalDTOAll->retTodos();
            $objMdPetHipoteseLegalDTOAll->retStrNome();
            $objMdPetHipoteseLegalDTOAll->retStrBaseLegal();
            $objMdPetHipoteseLegalDTOAll->retStrSinAtivo();
            $objMdPetHipoteseLegalDTOAll->setStrSinAtivo('S');

            $arrHipotesesLegais = $objMdPetHipoteseLegalRN->listar($objMdPetHipoteseLegalDTOAll);
            $arrHipotesesLegaisBD = array();
            $sinCadastrar = false;

            foreach($arrHipotesesLegais as $hipoteseLegal){
                array_push($arrHipotesesLegaisBD, $hipoteseLegal->getNumIdHipoteseLegalPeticionamento());
            }

            if (isset($_POST['sbmCadastrarHipoteseLegalRI'])) {
                try {

                    $arrObjHipoteseLegalDTOCad = array();
                    $arrHipotesesLegais = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnHipoteseLgl']);

                    $objMdPetHipoteseLegalRN = new MdPetHipoteseLegalRN();
                    $objMdPetHipoteseLegalDTOAll = new MdPetHipoteseLegalDTO();
                    $objMdPetHipoteseLegalDTOAll->retTodos();
                    $objMdPetHipoteseLegalDTOAll->retStrNome();
                    $objMdPetHipoteseLegalDTOAll->retStrBaseLegal();
                    $objMdPetHipoteseLegalDTOAll->setNumIdHipoteseLegalPeticionamento($arrHipotesesLegais, infraDTO::$OPER_NOT_IN);
                    $objMdPetHipoteseLegalDTOAll->setStrSinAtivo('S');

                    $arrHipotesesLgl = $objMdPetHipoteseLegalRN->listar($objMdPetHipoteseLegalDTOAll);

                    $countArrHipotesesLegais = (is_array($arrHipotesesLgl) ? count($arrHipotesesLgl) : 0);
                    if ($countArrHipotesesLegais > 0) {
                        $objMdPetHipoteseLegalRN->excluir($arrHipotesesLgl);
                    }

                    foreach ($arrHipotesesLegais as $hipoteseLegal) {
                        if(!in_array($hipoteseLegal, $arrHipotesesLegaisBD)){
                            // criando os novos
                            $objMdPetHipoteseLegalDTO = new MdPetHipoteseLegalDTO();
                            $objMdPetHipoteseLegalDTO->retTodos();
                            $objMdPetHipoteseLegalDTO->setNumIdHipoteseLegalPeticionamento($hipoteseLegal);
                            $arrObjHipoteseLegalDTOCad[] = $objMdPetHipoteseLegalDTO;
                            $sinCadastrar = true;
                        }
                    }

                    if($sinCadastrar){
                        $objMdPetHipoteseLegalRN->cadastrar($arrObjHipoteseLegalDTOCad);
                    }
                    PaginaSEI::getInstance()->adicionarMensagem("Os dados foram salvos com sucesso.", PaginaSEI::$TIPO_MSG_AVISO);
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao']));
                    die;
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }else{
                for ($x = 0; $x < count($arrHipotesesLegais); $x++) {
                    $strItensSelHipLegal .= "<option value='" . $arrHipotesesLegais[$x]->getNumIdHipoteseLegalPeticionamento() . "'>" . $arrHipotesesLegais[$x]->getStrNome() . ' (' . $arrHipotesesLegais[$x]->getStrBaseLegal() . ')' . "</option>";
                }
            }
            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }


}catch(Exception $e){
  PaginaSEI::getInstance()->processarExcecao($e);
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: '.PaginaSEI::getInstance()->getStrNomeSistema().' - '.$strTitulo.' ::');
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
require_once 'md_pet_hipotese_legal_nl_acesso_cadastro_css.php';
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
?>
<form id="frmHipLglNlAccPermCadastro" method="post" onsubmit="return OnSubmitForm();"
action="<?=PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">
<?
PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaSEI::getInstance()->abrirAreaDados('40em');
?>
    <div class="infraAreaDados" id="divInfraAreaDados">
        <div class="row">
            <div class="col-sm-6 col-md-7 col-lg-6 col-xl-4">
                <label id="lblHipoteseLgl" for="txtHipoteseLgl" accesskey="n" class="infraLabelObrigatorio">Hipóteses
                    Legais: <img align="top"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>"
                                 onmouseover="return infraTooltipMostrar('Nos casos em que o Usuário Externo indicar Nível de Acesso Restrito para Documentos que adicionar, as Hipóteses Legais disponíveis para ele selecionar estarão restringidas às Hipóteses Legais indicadas aqui.', 'Ajuda');"
                                 onmouseout="return infraTooltipOcultar();" class="infraImgModulo">
                </label>
                <input type="text" id="txtHipoteseLgl" name="txtHipoteseLgl" class="infraText form-control"
                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
            </div>
        </div>
        <div class="row">
            <div id="divSelDescricaoHpLegalNvAcesso" class="col-sm-10 col-md-12 col-lg-10 col-xl-10">
                <div class="input-group mb-3">
                    <select id="selDescricaoHpLegalNvAcesso" name="selDescricaoHpLegalNvAcesso" size="16"
                            multiple="multiple" class="infraSelect"
                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                        <?= $strItensSelHipLegal ?>
                    </select>
                    <div id="divIconesHipoteseLgl">
                        <img id="imgLupaHipoteseLgl" onclick="objLupaHipLegal.selecionar(700,500);"
                             onkeypress="objLupaHipLegal.selecionar(700,500);" src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/pesquisar.svg?<?= Icone::VERSAO ?>"
                             alt="Selecionar Hipóteses Legais" title="Selecionar Hipóteses Legais" class="infraImg"
                             tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>

                        <img id="imgExcluirHipoteseLgl" onclick="objLupaHipLegal.remover();"
                             onkeypress="objLupaHipLegal.remover();" src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg?<?= Icone::VERSAO ?>"
                             alt="Remover Hipóteses Legais Selecionadas" title="Remover Hipóteses Legais Selecionados"
                             class="infraImg" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                    </div>

                </div>
            </div>
        </div>
        <div id="divHdnHipoteseLgl">
            <input type="hidden" id="hdnHipoteseLgl" name="hdnHipoteseLgl" value="<?=$_POST['hdnHipoteseLgl']?>" />
            <input type="hidden" id="hdnIdHipoteseLgl" name="hdnIdHipoteseLgl" value="<?=$_POST['hdnIdHipoteseLgl']?>" />
            <input type="hidden" id="hdnHipoteseLglRI" name="hdnHipoteseLglRI" value="<?= $_POST['hdnHipoteseLglRI']?>" />
        </div>
    </div>

    <div id="hipLegalNvlAcessoAssociada" class="infraAreaDados"></div>

  <?
  PaginaSEI::getInstance()->fecharAreaDados();
  ?>
</form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
require_once 'md_pet_hipotese_legal_nl_acesso_cadastro_js.php';
?>