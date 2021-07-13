<?
/*
 * @author Alan Campos <alan.campos@castgroup.com.br>
 * 
 * */

try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    SessaoSei::getInstance()->validarLink();
    SessaoSei::getInstance()->validarPermissao($_GET['acao']);

} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}

$objMdPetExtensoesArquivoDTO = new MdPetExtensoesArquivoDTO();
$arrExtPrincipalBD = array();
$arrExtComplementarBD = array();

if ($_POST) {

    $objMdPetExtensoesArquivoRN = new MdPetExtensoesArquivoRN();
    $objInfraException = new InfraException();


    // excluindo registros anteriores
    $objMdPetExtensoesArquivoDTO->retTodos();
    $objMdPetExtensoesArquivoRN = new MdPetExtensoesArquivoRN();
    $arrExtensoes = $objMdPetExtensoesArquivoRN->listar($objMdPetExtensoesArquivoDTO);

    if ($arrExtensoes) {
        foreach ($arrExtensoes as $extensoes) {
            if ($extensoes->getStrSinPrincipal() == 'S')
                $arrExtPrincipalBD[] = $extensoes->getNumIdArquivoExtensao();
            else
                $arrExtComplementarBD[] = $extensoes->getNumIdArquivoExtensao();
        }
    }

    $arrPrincipal = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnPrincipal']);
    $arrComplementar = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnComplementar']);

    if (!$arrPrincipal) {
        $objInfraException->adicionarValidacao('Informe pelo menos uma extensão para documento principal.', PaginaSEI::$TIPO_MSG_ERRO);
    }

    if (!$arrComplementar) {
        $objInfraException->adicionarValidacao('Informe pelo menos uma extensão para documento complementar.', PaginaSEI::$TIPO_MSG_ERRO);
    }

    $objInfraException->lancarValidacoes();

    $arrObjMdPetExtensoesArquivoDTO = array();

    if ($arrPrincipal) {
        $objMdPetExtensoesArquivoDTO = new MdPetExtensoesArquivoDTO();
        $objMdPetExtensoesArquivoDTO->setStrSinAtivo('S');
        $objMdPetExtensoesArquivoDTO->setNumIdArquivoExtensao($arrPrincipal, infraDTO::$OPER_NOT_IN);
        $objMdPetExtensoesArquivoDTO->setStrSinPrincipal('S');
        $objMdPetExtensoesArquivoDTO->retTodos();
        $objMdPetExtensoesArquivoRN = new MdPetExtensoesArquivoRN();
        $listaExtensoesPrincipal = $objMdPetExtensoesArquivoRN->listar($objMdPetExtensoesArquivoDTO);
        if (count($listaExtensoesPrincipal)) {
            $objMdPetExtensoesArquivoRN->excluir($listaExtensoesPrincipal);
        }
    }

    foreach ($arrPrincipal as $numPrincipal) {
        if (!in_array($numPrincipal, $arrExtPrincipalBD)) {
            // criando os novos
            $objMdPetExtensoesArquivoDTO = new MdPetExtensoesArquivoDTO();
            $objMdPetExtensoesArquivoDTO->setStrSinAtivo('S');
            $objMdPetExtensoesArquivoDTO->setNumIdArquivoExtensao($numPrincipal);
            $objMdPetExtensoesArquivoDTO->setStrSinPrincipal('S');
            array_push($arrObjMdPetExtensoesArquivoDTO, $objMdPetExtensoesArquivoDTO);
            $sinCadastrar = true;
        }
    }

    if ($arrComplementar) {
        $objMdPetExtensoesArquivoDTO = new MdPetExtensoesArquivoDTO();
        $objMdPetExtensoesArquivoDTO->setStrSinAtivo('S');
        $objMdPetExtensoesArquivoDTO->setNumIdArquivoExtensao($arrComplementar, infraDTO::$OPER_NOT_IN);
        $objMdPetExtensoesArquivoDTO->setStrSinPrincipal('N');
        $objMdPetExtensoesArquivoDTO->retTodos();
        $objMdPetExtensoesArquivoRN = new MdPetExtensoesArquivoRN();
        $listaExtensoesComplementar = $objMdPetExtensoesArquivoRN->listar($objMdPetExtensoesArquivoDTO);
        if (count($listaExtensoesComplementar)) {
            $objMdPetExtensoesArquivoRN->excluir($listaExtensoesComplementar);
        }
    }

    foreach ($arrComplementar as $numComplementar) {
        if (!in_array($numComplementar, $arrExtComplementarBD)) {
            $objMdPetExtensoesArquivoDTO = new MdPetExtensoesArquivoDTO();
            $objMdPetExtensoesArquivoDTO->setStrSinAtivo('S');
            $objMdPetExtensoesArquivoDTO->setNumIdArquivoExtensao($numComplementar);
            $objMdPetExtensoesArquivoDTO->setStrSinPrincipal('N');
            array_push($arrObjMdPetExtensoesArquivoDTO, $objMdPetExtensoesArquivoDTO);
            $sinCadastrar = true;
        }
    }

    if ($sinCadastrar) {
        $objMdPetExtensoesArquivoDTO = $objMdPetExtensoesArquivoRN->cadastrar($arrObjMdPetExtensoesArquivoDTO);
    }

    PaginaSEI::getInstance()->adicionarMensagem("Os dados foram salvos com sucesso.", PaginaSEI::$TIPO_MSG_AVISO);
}

$strSelExtensoesPrin = MdPetExtensoesArquivoINT::montarSelectExtensoes(null, null, null, 'S');
$strSelExtensoesComp = MdPetExtensoesArquivoINT::montarSelectExtensoes(null, null, null, 'N');


$strTitulo = "Peticionamento - Extensões de Arquivos Permitidas";

$arrComandos[] = '<button type="submit" accesskey="s" name="sbmCadastrarGrupoUnidade" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
$arrComandos[] = '<button type="button" accesskey="c" name="btnFechar" id="btnFechar" value="Fechar" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSei::getInstance()->assinarLink('controlador.php?acao=procedimento_controlar&acao_origem=' . $_GET['acao'])) . '\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: ' . PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
require_once 'md_pet_extensoes_arquivo_cadastro_css.php';
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
    <form id="frmGrupoCadastro" method="post" onsubmit="return OnSubmitForm();"
          action="<?= PaginaSEI::getInstance()->formatarXHTML(SessaoSei::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'])) ?>">
        <?
        PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
        PaginaSEI::getInstance()->abrirAreaDados('45em');
        ?>

        <div class="infraAreaDados" id="divInfraAreaDados">
            <div class="row">
                <div id="divLblPrincipal" class="col-sm-6 col-md-7 col-lg-6 col-xl-4">
                    <label id="lblPrincipal" for="txtPrincipal" accesskey="P" class="infraLabelObrigatorio">Documento
                        Principal (Processo Novo):
                        <img id="imgAjudaTxtPrincipal"
                             src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" alt=""
                             onmouseover="return infraTooltipMostrar('Define as Extensões de Arquivos Permitidas no Peticionamento de Processo Novo somente do Documento Principal, que geralmente é de tamanho menor que os demais documentos, pois tende a ser Nato Digital. \n \n ATENÇÃO: permitir apenas extensões que comportem texto, evitando, por exemplo. zip, mp4 ou mp3 para Documento Principal', 'Ajuda');"
                             onmouseout="return infraTooltipOcultar();" class="infraImgModulo">
                    </label>
                    <input type="text" id="txtPrincipal" name="txtPrincipal" class="infraText form-control"
                           onkeypress="return infraMascaraTexto(this,event,50);" maxlength="50"
                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                </div>
            </div>
            <div class="row">
                <div id="divSelPrincipal" class="col-sm-10 col-md-10 col-lg-8 col-xl-6">
                    <div class="input-group mb-3">
                        <select id="selPrincipal" name="selPrincipal" size="8" multiple="multiple"
                                class="infraSelect form-control"
                                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                            <?= $strSelExtensoesPrin; ?>
                        </select>
                        <div id="divIconesPrincipal">
                            <img id="imgLupaPrincipal" onclick="objLupaPrincipal.selecionar(700,500);"
                                 onkeypress="objLupaPrincipal.selecionar(700,500);"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/pesquisar.svg"
                                 alt="Selecionar Extensões" title="Selecionar Extensões" class="infraImg"
                                 tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                            <img id="imgExcluirPrincipal" onclick="objLupaPrincipal.remover();"
                                 onkeypress="objLupaPrincipal.remover();"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg"
                                 alt="Remover Extensões Selecionadas"
                                 title="Remover Extensões Selecionadas" class="infraImg"
                                 tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <!--  Nome do Menu -->
                <div id="divLblComplementar" class="col-sm-6 col-md-7 col-lg-6 col-xl-4">
                    <label id="lblComplementar" for="txtComplementar" class="infraLabelObrigatorio">Demais Documentos:
                        <img id="imgAjudaTxtComplementar"
                             src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" alt=""
                             onmouseover="return infraTooltipMostrar('Define as Extensões de Arquivos Permitidas no Peticionamento de Processo Novo especificamente sobre os Documentos Essenciais e Complementares, no Peticionamento Intercorrente, no Peticionamento de Resposta a Intimação e no Peticionamento de Responsável Legal de Pessoa Jurídica', 'Ajuda');"
                             onmouseout="return infraTooltipOcultar();" class="infraImgModulo">
                    </label>
                    <input type="text" id="txtComplementar" name="txtComplementar" class="infraText form-control"
                           onkeypress="return infraMascaraTexto(this,event,50);" maxlength="50"
                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                </div>
            </div>
            <div class="row">
                <div id="divSelComplementar" class="col-sm-10 col-md-10 col-lg-8 col-xl-6">
                    <div class="input-group mb-3">
                        <select id="selComplementar" name="selComplementar" size="12" multiple="multiple"
                                class="infraSelect form-control"
                                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                            <?= $strSelExtensoesComp; ?>
                        </select>
                        <div id="divIconesComplementar">
                            <img id="imgLupaComplementar" onclick="objLupaComplementar.selecionar(700,500);"
                                 onkeypress="objLupaComplementar.selecionar(700,500);"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/pesquisar.svg"
                                 alt="Selecionar Extensões" title="Selecionar Extensões" class="infraImg"
                                 tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                            <img id="imgExcluirComplementar" onclick="objLupaComplementar.remover();"
                                 onkeypress="objLupaComplementar.remover();"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg"
                                 alt="Remover Extensões Selecionadas" title="Remover Extensões Selecionadas"
                                 class="infraImg"
                                 tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" id="hdnIdPrincipal" name="hdnIdPrincipal" class="infraText" value=""/>
        <input type="hidden" id="hdnPrincipal" name="hdnPrincipal" value=""/>

        <input type="hidden" id="hdnIdComplementar" name="hdnIdComplementar" class="infraText" value=""/>
        <input type="hidden" id="hdnComplementar" name="hdnComplementar" value=""/>
        <?
        PaginaSEI::getInstance()->fecharAreaDados();
        ?>
    </form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
require_once 'md_pet_extensoes_arquivo_cadastro_js.php';
?>