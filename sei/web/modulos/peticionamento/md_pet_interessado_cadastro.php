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

    PaginaSEIExterna::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);
    $objInfraException = new InfraException();
    switch ($_GET['acao']) {

        case 'md_pet_interessado_cadastro':

            if (!isset($_GET['edicao']) && !isset($_POST['hdnIdEdicaoAuxiliar'])) {
                $strTitulo = 'Cadastro de Interessado';
            } else {
                $strTitulo = 'Alterar Interessado';
            }

            $janelaSelecaoPorNome = SessaoSEIExterna::getInstance()->getAtributo('janelaSelecaoPorNome');
            if ($janelaSelecaoPorNome != null && $janelaSelecaoPorNome != "") {

            } else if (isset($_GET['cpf'])) {
                $strTitulo .= ' - Pessoa Física';
            } else if (isset($_GET['cnpj'])) {
                $strTitulo .= ' - Pessoa Jurídica';
            }
            $strPrimeiroItemValor = 'null';
            $strPrimeiroItemDescricao = '&nbsp;';
            $strValorSiglaEstadoSelecionado = isset($_POST['selEstado']) ? $_POST['selEstado'] : null;
            $strValorSelCidadeSelecionado = isset($_POST['selCidade']) ? $_POST['selCidade'] : null;
            $strValorItemSelecionado = isset($_POST['tipoInteressado']) ? $_POST['tipoInteressado'] : null;
            $strTipo = 'Cadastro';

            $strItensSelSiglaEstado = UfINT::montarSelectSiglaRI0416('null', '&nbsp;', $strValorSiglaEstadoSelecionado);
            $strItensSelCidade = CidadeINT::montarSelectNomeNome('null', '&nbsp;', $strValorSelCidadeSelecionado, null);
            $strItensSelTipoInteressado = MdPetTpCtxContatoINT::montarSelectTipoInteressado($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $strTipo);
            $strLinkAjaxCargo = SessaoSEIExterna::getInstance()->assinarLink('modulos/peticionamento/controlador_ajax_externo.php?acao_ajax_externo=md_pet_cargo_montar_select_genero');
            $strLinkAjaxDadosCargo = SessaoSEIExterna::getInstance()->assinarLink('modulos/peticionamento/controlador_ajax_externo.php?acao_ajax_externo=md_pet_cargo_dados');

            $arrCampos = array(
                'txtNome' => 'Nome Completo',
                'orgaoExpedidor' => 'Órgão Expedidor do RG',
                'numeroOab' => 'Número da OAB',
                'endereco' => 'Endereço',
                'bairro' => 'Bairro',
                'sitioInternet' => 'Sítio na Internet',
                'txtRazaoSocial' => 'Razão Social',
            );

            $validadorXss = false;

            if (isset($_SESSION['validarXss'])) {
                $validadorXss = $_SESSION['validarXss'];
            }

            if (PeticionamentoIntegracao::validarXssFormulario($_POST, $arrCampos, $objInfraException) && $validadorXss == false) {
                $_SESSION['validarXss'] = true;
                $validadorXss = true;
                echo "<script>";
                echo "window.location.reload();";
                echo "</script>";
            } else {
                $_SESSION['validarXss'] = false;
            }

            //setando dados no contato que esta sendo cadastrado ou editado
            if (isset($_POST['hdnCadastrar']) && $validadorXss == false) {
                unset($_SESSION['validarXss']);
                //TODO: Avaliar se é realmente necessário retornar todas as informações de contato
                $objContatoDTO = new ContatoDTO();
                $objContatoDTO->retTodos();

                $numIdTipoContextoContato = $_POST['tipoInteressado'];

                if (!isset($_POST['hdnIdEdicao']) || $_POST['hdnIdEdicao'] == "") {
                    $objContatoDTO->setNumIdContato(null);
                    $objContatoDTO->retNumIdTipoContato();
                } else {
                    $objContatoRN = new ContatoRN();

                    $objContatoDTO = new ContatoDTO();
                    $objContatoDTO->retNumIdTipoContato();
                    $objContatoDTO->retStrMatricula();
                    $objContatoDTO->retDblRg();
                    $objContatoDTO->retStrOrgaoExpedidor();
                    $objContatoDTO->retStrTelefoneComercial();
                    $objContatoDTO->retStrEmail();
                    $objContatoDTO->retStrSitioInternet();
                    $objContatoDTO->retStrEndereco();
                    $objContatoDTO->retStrBairro();
                    $objContatoDTO->retStrSiglaUf();
                    $objContatoDTO->retStrNomeCidade();
                    $objContatoDTO->retStrNomePais();
                    $objContatoDTO->retStrCep();
                    $objContatoDTO->retStrObservacao();
                    $objContatoDTO->retNumIdContato();
                    $objContatoDTO->setNumIdContato($_POST['hdnIdEdicao']);
                    $objContatoDTO = $objContatoRN->consultarRN0324($objContatoDTO);
                }

                $objContatoDTO->setNumIdCargo($_POST['cargo']);

                if (isset($_POST['txtNome']) && $_POST['txtNome'] != "") {
                    $objContatoDTO->setStrNome($_POST['txtNome']);
                    $objContatoDTO->setStrStaNatureza(ContatoRN::$TN_PESSOA_FISICA);
                } else if (isset($_POST['txtRazaoSocial']) && $_POST['txtRazaoSocial'] != "") {
                    $objContatoDTO->setStrNome($_POST['txtRazaoSocial']);
                    $objContatoDTO->setStrStaNatureza(ContatoRN::$TN_PESSOA_JURIDICA);
                }

                $objContatoDTO->setDtaNascimento('');
                $objContatoDTO->setStrSigla('');
                $objContatoDTO->setStrStaGenero($_POST['rdoStaGenero']);
                $objContatoDTO->setStrMatriculaOab($_POST['numeroOab']);

                //campos manipulados apenas no cadastro (nao na ediçao)
                if (!isset($_POST['hdnIdEdicao']) || $_POST['hdnIdEdicao'] == "") {

                    // Pessoa Física
                    if (isset($_POST['txtNome']) && $_POST['txtNome'] != "" && isset($_POST['txtCPF']) && $_POST['txtCPF'] != "") {
                        $objContatoDTO->setStrSigla(InfraUtil::formatarCpf($_POST['txtCPF']));
                        // Pessoa Jurídica
                    } else if (isset($_POST['txtRazaoSocial']) && $_POST['txtRazaoSocial'] != "" && isset($_POST['txtCNPJ']) && $_POST['txtCNPJ'] != "") {
                        $objContatoDTO->setStrSigla(InfraUtil::formatarCnpj($_POST['txtCNPJ']));
                    }
                    $objContatoDTO->setDblCpf($_POST['txtCPF']);
                    $objContatoDTO->setDblCnpj($_POST['txtCNPJ']);
                    $objContatoDTO->setStrSinAtivo('S');

                    if (isset ($_POST['hdnIdContextoContato']) && $_POST['hdnIdContextoContato'] != "") {
                        $objContatoDTO->setNumIdContato($_POST['hdnIdContextoContato']);
                    }

                    //PF sem vinculo com PJ
                    if ($_POST['tipoPessoaPF'] == '0') {
                        $strSinContexto = 'S';
                        unset($_POST['hdnIdContextoContato']);
                        $objContatoDTO->setNumIdTipoContato($numIdTipoContextoContato);
                        //PF com vinculo com PJ
                    } else if ($_POST['tipoPessoaPF'] == '1') {
                        $strSinContexto = 'N';
                        $objContatoDTO->setNumIdTipoContato($numIdTipoContextoContato);
                        //PJ
                    } else {
                        $strSinContexto = 'S';
                        unset($_POST['hdnIdContextoContato']);
                        $objContatoDTO->setNumIdTipoContato($numIdTipoContextoContato);
                    }
                }

                $objContatoDTO->setStrMatricula('');
                $objContatoDTO->setDblRg($_POST['rg']);
                $objContatoDTO->setStrOrgaoExpedidor($_POST['orgaoExpedidor']);
                $objContatoDTO->setStrTelefoneComercial($_POST['telefone']);
                $objContatoDTO->setStrTelefoneCelular(null);
                $objContatoDTO->setStrComplemento(null);
                $objContatoDTO->setStrEmail($_POST['email']);
                $objContatoDTO->setStrSitioInternet($_POST['sitioInternet']);
                $objContatoDTO->setStrEndereco($_POST['endereco']);
                $objContatoDTO->setStrBairro($_POST['bairro']);
                $objContatoDTO->setStrNomeCidade($_POST['selCidade']);
                $objContatoDTO->setStrNomePais($_POST['pais']);
                $objContatoDTO->setStrCep($_POST['cep']);
                $objContatoDTO->setStrObservacao('');
                $objContatoDTO->setStrNumeroPassaporte(null);
                $objContatoDTO->setNumIdPaisPassaporte(null);

                $paisDTO = new PaisDTO();
                $paisRN = new PaisRN();
                $paisDTO->retTodos();
                $paisDTO->setStrNome($_POST['pais']);
                $paisDTO = $paisRN->consultar($paisDTO);

                $objContatoDTO->setNumIdPais($paisDTO->getNumIdPais());
                $objContatoDTO->setNumIdUf($_POST['selEstado']);
                $objContatoDTO->setNumIdCidade($_POST['selCidade']);
                $objContatoDTO->setStrSinEnderecoAssociado('N');

                //necessario para preencher o campo id_usuario_cadastro ao salvar o contato
                SessaoSEI::getInstance()->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());

                $objContatoRN = new ContatoRN();

                //verificando se é cadastro ou ediçao de contato
                if (!isset($_POST['hdnIdEdicao']) || $_POST['hdnIdEdicao'] == "") {
                    $objContatoDTO->setNumIdContatoAssociado($_POST['hdnIdContextoContato']);
                    $objContatoDTO->setStrStaNaturezaContatoAssociado(null);
                    $objContatoDTO = $objContatoRN->cadastrarRN0322($objContatoDTO);
                    $idContatoCadastro = $objContatoDTO->getNumIdContato();
                } else if ($_POST['hdnIdEdicao'] != "") {
                    $idContatoCadastro = $objContatoDTO->getNumIdContato();
                    $objContatoRN->alterarRN0323($objContatoDTO);
                }

                //nome / razao social
                if (isset($_POST['txtNome']) && $_POST['txtNome'] != "") {
                    $nome = $_POST['txtNome'];
                } else if (isset($_POST['txtRazaoSocial']) && $_POST['txtRazaoSocial'] != "") {
                    $nome = $_POST['txtRazaoSocial'];
                }

                //cpf/cnpj
                if (isset($_POST['txtCPF']) && $_POST['txtCPF'] != "") {
                    $cpfCnpjEditado = $_POST['txtCPF'];
                } else if (isset($_POST['txtCNPJ']) && $_POST['txtCNPJ'] != "") {
                    $cpfCnpjEditado = $_POST['txtCNPJ'];
                }

                //após cadastrar o contato fechar janela modal e preencher campos necessarios
                if (!isset($_POST['hdnIdEdicao']) || $_POST['hdnIdEdicao'] == "") {
                    $janelaSelecaoPorNome = SessaoSEIExterna::getInstance()->getAtributo('janelaSelecaoPorNome');

                    echo "<script>";
                    if ($janelaSelecaoPorNome == null || $janelaSelecaoPorNome == "") {
                        echo "window.top.document.getElementById('txtNomeRazaoSocial').value = '" . str_replace("/", "\/", $nome) . "'; ";
                        echo "window.top.document.getElementById('txtNomeRazaoSocialTratadoHTML').value = '" . PaginaSEIExterna::tratarHTML($nome) . "'; ";
                        echo "window.top.document.getElementById('hdnCustomizado').value = 'true'; ";
                        echo "window.top.document.getElementById('hdnIdInteressadoCadastrado').value = " . $objContatoDTO->getNumIdContato() . "; ";
                    } else {
                        SessaoSEIExterna::getInstance()->removerAtributo('janelaSelecaoPorNome');
                    }

                    echo "$(window.top.document).find('div[id^=divInfraSparklingModalClose]').click();";
                    echo "</script>";
                    die;

                } else {
                    echo "<script>";
                    echo "atualizarNomeRazaoSocial('" . $cpfCnpjEditado . "', '" . PaginaSEIExterna::tratarHTML($nome) . "');";
                    echo "$(window.top.document).find('div[id^=divInfraSparklingModalClose]').click();";
                    echo "</script>";
                    die;
                }

            } //obtendo dados do contato que estiver sendo editado
            else if (isset($_POST['hdnIdEdicaoAuxiliar'])) {
                $objContatoRN = new ContatoRN();
                $objContatoDTO = new ContatoDTO();
                $objContatoDTO->retTodos(true);
                $objContatoDTO->setNumIdContato($_POST['hdnIdEdicaoAuxiliar']);
                $objContatoDTO = $objContatoRN->consultarRN0324($objContatoDTO);
                $strItensSelSiglaEstado = UfINT::montarSelectSiglaRI0416('null', '&nbsp;', $objContatoDTO->getNumIdUf());
                $strItensSelCidade = CidadeINT::montarSelectIdCidadeNome('null', '&nbsp;', $objContatoDTO->getNumIdCidade(), $objContatoDTO->getNumIdUf());

                if (isset($_GET['cpf'])) {
                    $_POST['txtNome'] = $objContatoDTO->getStrNome();
                }

                if (isset($_GET['cnpj'])) {
                    $_POST['txtRazaoSocial'] = $objContatoDTO->getStrNome();
                }

                $_POST['numeroOab'] = $objContatoDTO->getStrMatriculaOab();
                $_POST['txtCPF'] = $objContatoDTO->getDblCpf();
                $_POST['txtCNPJ'] = $objContatoDTO->getDblCnpj();
                $_POST['rg'] = $objContatoDTO->getDblRg();
                $_POST['orgaoExpedidor'] = $objContatoDTO->getStrOrgaoExpedidor();
                $_POST['telefone'] = $objContatoDTO->getStrTelefoneComercial();
                $_POST['email'] = $objContatoDTO->getStrEmail();
                $_POST['sitioInternet'] = $objContatoDTO->getStrSitioInternet();
                $_POST['endereco'] = $objContatoDTO->getStrEndereco();
                $_POST['bairro'] = $objContatoDTO->getStrBairro();
                $_POST['estado'] = $objContatoDTO->getStrSiglaUfContatoAssociado();
                $_POST['cidade'] = $objContatoDTO->getStrNomeCidade();
                $_POST['pais'] = $objContatoDTO->getStrNomePais();
                $_POST['cep'] = $objContatoDTO->getStrCep();
                $_POST['tratamento'] = $objContatoDTO->getNumIdTratamentoCargo();
                $_POST['vocativo'] = $objContatoDTO->getNumIdVocativoCargo();
                $_POST['cargo'] = $objContatoDTO->getNumIdCargo();
                $_POST['hdnIdEdicao'] = $_POST['hdnIdEdicaoAuxiliar'];
                $_POST['hdnIdContextoContato'] = $objContatoDTO->getNumIdContato();

                $objContatoPJVinculadaDTO = new ContatoDTO();
                $objContatoPJVinculadaDTO->retNumIdContato();
                $objContatoPJVinculadaDTO->retStrNome();
                $objContatoPJVinculadaDTO->retNumIdTipoContato();
                $objContatoPJVinculadaDTO->setNumIdContato($_POST['hdnIdContextoContato']);
                $objContatoPJVinculadaDTO = $objContatoRN->consultarRN0324($objContatoPJVinculadaDTO);
                $_POST['tipoInteressado'] = $objContatoDTO->getNumIdTipoContato();

                if ($objContatoDTO->getStrStaNaturezaContatoAssociado() == ContatoRN::$TN_PESSOA_JURIDICA) {
                    $_POST['txtPjVinculada'] = $objContatoDTO->getStrNomeContatoAssociado();
                } else {
                    $_POST['txtPjVinculada'] = "";
                }

                $numIdTipoContextoContato = $_POST['tipoInteressado'];
                $strItensSelTipoInteressado = MdPetTpCtxContatoINT::montarSelectTipoInteressado($strPrimeiroItemValor, $strPrimeiroItemDescricao, $numIdTipoContextoContato, $strTipo);

                if (isset($_GET['cpf'])) {
                    $strItensSelTratamento = TratamentoINT::montarSelectExpressaoRI0467('null', '&nbsp;', $_POST['tratamento']);
                    $strItensSelVocativo = VocativoINT::montarSelectExpressaoRI0469('null', '&nbsp;', $_POST['vocativo']);
                }
            }

            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }

    if (!is_null($objInfraException->getArrObjInfraValidacao())) {
        $objInfraException->lancarValidacoes();
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
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
PaginaSEIExterna::getInstance()->fecharJavaScript();
?>
<style type="text/css">
    #field1 {
        height: auto;
        width: 97%;
        margin-bottom: 11px;
    }

    #field2 {
        height: auto;
        width: 97%;
        margin-bottom: 11px;
    }

    .sizeFieldset {
        height: auto;
        width: 88%;
    }

    .fieldsetClear {
        border: none !important;
    }
</style>

<?php
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
$urlBaseLink = "";
$arrComandos = array();
$arrComandos[] = '<button type="button" accesskey="s" name="Salvar" value="Salvar" onclick="salvar()" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
$arrComandos[] = '<button type="button" accesskey="c" name="btnFechar" value="Fechar" onclick="$(window.top.document).find(\'div[id^=divInfraSparklingModalClose]\').click();" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

$strLinkBaseFormEdicao = 'controlador_externo.php?edicaoExibir=true&acao=' . $_GET['acao'];

if (isset($_GET['cpf'])) {
    $strLinkBaseFormEdicao .= '&cpf=true';
} else if (isset($_GET['cnpj'])) {
    $strLinkBaseFormEdicao .= '&cnpj=true';
}

$strLinkEdicaHash = PaginaSEIExterna::getInstance()->formatarXHTML(
    SessaoSEIExterna::getInstance()->assinarLink($strLinkBaseFormEdicao));

?>
<div class="infraAreaDados">
    <!-- Formulario usado para viabilizar fluxo de edição de contato -->
    <?php if (isset($_GET['edicao'])) { ?>

        <form id="frmEdicaoAuxiliar"
              name="frmEdicaoAuxiliar"
              method="post"
              action="<?= $strLinkEdicaHash ?>">

            <input type="hidden" name="hdnIdEdicaoAuxiliar" id="hdnIdEdicaoAuxiliar" value=""/>

        </form>

    <?php } else { ?>

        <?php
        $parametrosUrl = "&tipo_selecao=2";
        if (isset($_GET['cpf'])) {
            $parametrosUrl .= "&cpf=true";
        } else {
            $parametrosUrl .= "&cnpj=true";
        }
        $parametrosUrl .= "&cadastro=true&id_orgao_acesso_externo=0";
        ?>

        <form id="frmCadastro" name="frmCadastro"
              method="post" onsubmit="return OnSubmitForm();"
              action="<?= PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'] . $parametrosUrl)) ?>">
            <?php
            PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
            PaginaSEIExterna::getInstance()->abrirAreaDados('auto');
            ?>

            <fieldset id="field1" class="infraFieldset sizeFieldset form-control-lg">

                <legend class="infraLegend">&nbsp; Natureza &nbsp;</legend>

                <?php if (isset($_GET['cpf'])) { ?>
                    <label for="rdPF" class="infraLabelRadio">
                        <input type="radio" name="tipoPessoa" value="pf" id="rdPF" class="infraRadio"
                               tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"
                               onclick="selecionarPF()"/>
                        Pessoa Física
                        <br>
                    </label>
                    <label for="rdPF1" id="lblrdPF1" class="infraLabelRadio" style="display: none; left: 25px">
                        <input type="radio" name="tipoPessoaPF" value="0" id="rdPF1"
                               class="infraRadio"
                               tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"
                               onclick="selecionarPF1()"/>
                        Sem Vinculação com
                        Pessoa Jurídica
                        <br>
                    </label>
                    <label for="rdPF2" id="lblrdPF2" class="infraLabelRadio" style="display: none;; left: 25px">
                        <input type="radio" name="tipoPessoaPF" value="1" id="rdPF2" class="infraRadio"
                               onclick="selecionarPF2()"
                               tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"/>
                        Com vínculo com
                        Pessoa
                        Jurídica
                        <br>
                    </label>

                <?php } ?>

                <?php if (isset($_GET['cnpj'])) { ?>
                    <label for="rdPJ" class="infraLabelRadio">
                        <input type="radio" name="tipoPessoa" value="pj" id="rdPJ" class="infraRadio"
                               tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"
                               onclick="selecionarPJ()"/>
                        <label for="rdPJ" class="infraLabelRadio">Pessoa Jurídica</label>
                    </label>
                <?php } ?>

            </fieldset>

            <fieldset id="field2" class="infraFieldset sizeFieldset form-control-lg">

                <legend class="infraLegend">&nbsp; Formulário de Cadastro &nbsp;</legend>

                <div class="row">
                    <div class="col-sm-12 col-md-8 col-lg-8 col-xl-8">
                        <label class="infraLabelObrigatorio">Tipo de Interessado:</label>
                        <select class="infraSelect form-control" id="tipoInteressado"
                                tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"
                                name="tipoInteressado"
                                value="<?= PaginaSEIExterna::tratarHTML($_POST['tipoInteressado']) ?>"
                                onchange="selecionarTipoInteressado()">
                            <?= $strItensSelTipoInteressado ?>
                        </select>
                    </div>
                </div>
                <div id="nome" style="display:none;" class="row">
                    <div class="col-sm-12 col-md-8 col-lg-8 col-xl-8">
                        <label id="lblNome" class="infraLabelObrigatorio">Nome Completo:</label>                 </label>
                        <input type="text" id="txtNome" name="txtNome"
                               class="infraText form-control"
                               value="<?= PaginaSEIExterna::tratarHTML($_POST['txtNome']) ?>"
                               onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250"
                               tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"/>
                    </div>
                </div>
                <div id="razaoSocial" style="display:none;" class="row">
                    <div class="col-sm-12 col-md-8 col-lg-8 col-xl-8">
                        <label id="lblRazaoSocial" class="infraLabelObrigatorio">Razão Social:<br/></label>
                        <input type="text" id="txtRazaoSocial" name="txtRazaoSocial"
                               class="infraText form-control"
                               value="<?= PaginaSEIExterna::tratarHTML($_POST['txtRazaoSocial']) ?>"
                               onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250"
                               tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"/>
                    </div>
                </div>

                <?php if ($_POST['hdnIdContextoContato'] == '') { ?>
                    <div id="pjVinculada" style="display: none;" class="row">
                        <div class="col-sm-12 col-md-8 col-lg-8 col-xl-8">
                            <label id="lblPjVinculada" class="infraLabelObrigatorio">Razão Social da
                                Pessoa
                                Jurídica vinculada:<br/> </label>
                            <input type="text" class="infraText form-control"
                                   tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"
                                   onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250"
                                   name="txtPjVinculada" id="txtPjVinculada"
                                   autocomplete="off" style="display: none;"/>

                            <input type="hidden" name="hdnIdContextoContato" id="hdnIdContextoContato"
                                   value="<?php echo $_POST['hdnIdContextoContato']; ?>"/>
                        </div>
                    </div>
                <?php } else if ($_POST['txtPjVinculada'] != "") { ?>
                    <div id="pjVinculada" style="display: none;" class="row">
                        <div class="col-sm-12 col-md-8 col-lg-8 col-xl-8">
                            <label id="lblPjVinculada" class="infraLabelObrigatorio">Razão Social da
                                Pessoa
                                Jurídica vinculada:<br/></label>
                            <input type="text" class="infraText form-control"
                                   value="<?= PaginaSEIExterna::tratarHTML($_POST['txtPjVinculada']) ?>"
                                   tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"
                                   onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250"
                                   name="txtPjVinculada" id="txtPjVinculada"
                                   autocomplete="off" />
                            <input type="hidden" name="hdnIdContextoContato" id="hdnIdContextoContato"
                                   value="<?= $_POST['hdnIdContextoContato'] ?>"/>
                        </div>
                    </div>
                <?php } ?>
                <div class="row" id="CPF" style="display: none;">
                    <div class="col-sm-12 col-md-8 col-lg-8 col-xl-8">
                        <label id="lblCPF" class="infraLabelObrigatorio">CPF:</label>
                        <input type="text" class="infraText form-control" name="txtCPF" id="txtCPF"
                                   value="<?= PaginaSEIExterna::tratarHTML($_POST['txtCPF']) ?>"
                                   readonly="readonly"
                                   onkeypress="return infraMascaraCpf(this, event)"/>

                    </div>
                </div>
                <div class="row" id="CNPJ" style="display: none;">
                    <div class="col-sm-12 col-md-8 col-lg-8 col-xl-8">
                        <label id="lblCNPJ" class="infraLabelObrigatorio">CNPJ:</label>
                        <input type="text" class="infraText form-control" name="txtCNPJ" id="txtCNPJ"
                                   value="<?= PaginaSEIExterna::tratarHTML($_POST['txtCNPJ']) ?>"
                                   readonly="readonly" onkeypress="return infraMascaraCnpj(this, event)"/>
                    </div>
                </div>
                <div id="camposIdentificacao" style="display: none;" class="row">
                    <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4">
                        <label class="infraLabelObrigatorio">RG:</label>
                        <input type="text" class="infraText form-control"
                               value="<?= PaginaSEIExterna::tratarHTML($_POST['rg']) ?>"
                               tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"
                               onkeypress="return infraMascaraNumero(this,event, 15);"
                               name="rg" id="rg"/>
                    </div>
                    <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4">
                        <label class="infraLabelObrigatorio">Órgão Expedidor do RG:</label>
                        <input type="text" class="infraText form-control" name="orgaoExpedidor"
                               tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"
                               value="<?= PaginaSEIExterna::tratarHTML($_POST['orgaoExpedidor']) ?>"
                               onkeypress="return infraMascaraTexto(this,event, 50);"
                               id="orgaoExpedidor"/>
                    </div>
                    <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4">
                        <label class="infraLabel">Número da OAB:</label>
                        <input type="text" class="infraText form-control"
                               tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"
                               value="<?= PaginaSEIExterna::tratarHTML($_POST['numeroOab']) ?>"
                               onkeypress="return infraMascaraTexto(this,event,10);" maxlength="10"
                               name="numeroOab" id="numeroOab"/>
                    </div>
                </div>
                <div id="divPessoaFisicaPublico1" class="infraAreaDados">
                    <br>
                    <div class="row">
                        <div class="col-sm-12 col-md-3 col-lg-3 col-xl-3">
                            <fieldset id="fldStaGenero" class="infraFieldset form-control" style="height: 125px">
                                <br/>
                                <legend class="infraLegend">&nbsp;Gênero&nbsp;</legend>
                                <div id="divOptFeminino" class="infraDivRadio" style="">
                                    <input type="radio" name="rdoStaGenero" id="optFeminino"
                                           value="F" <?= ($objContatoDTO && $objContatoDTO->getStrStaGenero() == ContatoRN::$TG_FEMININO ? 'checked="checked"' : '') ?>
                                        <?= ($_POST['rdoStaGenero'] == ContatoRN::$TG_FEMININO ? 'checked="checked"' : '') ?>
                                           class="infraRadio" onchange="trocarGenero()"/>
                                    <label id="lblFeminino" for="optFeminino" class="infraLabelRadio"
                                           tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>">Feminino</label>
                                </div>
                                <div id="divOptMasculino" class="infraDivRadio">
                                    <input type="radio" name="rdoStaGenero" id="optMasculino"
                                           value="M" <?= ($objContatoDTO && $objContatoDTO->getStrStaGenero() == ContatoRN::$TG_MASCULINO ? 'checked="checked"' : '') ?>
                                        <?= ($_POST['rdoStaGenero'] == ContatoRN::$TG_MASCULINO ? 'checked="checked"' : '') ?>
                                           class="infraRadio" onchange="trocarGenero()"/>
                                    <label id="lblMasculino" for="optMasculino" class="infraLabelRadio"
                                           tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>">Masculino</label>
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-sm-12 col-md-9 col-lg-9 col-xl-9">
                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                    <label id="lblIdCargo" for="cargo" class="infraLabelObrigatorio">Cargo:</label>
                                    <select id="cargo" name="cargo" class="infraSelect form-control"
                                            tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>">
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                    <label id="lblTratamento" for="tratamento" class="infraLabelObrigatorio">Tratamento:</label>
                                    <input type="text" id="tratamento" name="tratamento" disabled="disabled"
                                           class="infraText infraReadOnly form-control"
                                           value="<? /*=PaginaSEI::tratarHTML($objContatoDTO->getStrExpressaoTratamentoCargo())*/ ?>"
                                           tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"/>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                    <label id="lblVocativo" for="txtVocativo"
                                           class="infraLabelObrigatorio">Vocativo:</label><br/>
                                    <input type="text" id="vocativo" name="vocativo" disabled="disabled"
                                           class="infraText infraReadOnly form-control"
                                           value="<? /*=PaginaSEI::tratarHTML($objContatoDTO->getStrExpressaoVocativoCargo())*/ ?>"
                                           tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                        <label class="infraLabelObrigatorio">Telefone:</label>
                        <input type="text" class="infraText form-control" name="telefone"
                               tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"
                               value="<?= PaginaSEIExterna::tratarHTML($_POST['telefone']) ?>"
                               onkeydown="return infraMascaraTelefone(this,event);" maxlength="25"
                               id="telefone"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                        <label class="infraLabel">E-mail:</label>
                        <input type="text" class="infraText form-control"
                               tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"
                               value="<?= PaginaSEIExterna::tratarHTML($_POST['email']) ?>"
                               onkeypress="return infraMascaraTexto(this,event,50);" maxlength="50"
                               name="email" id="email"/>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                        <label class="infraLabel">Sítio na Internet:</label>
                        <input type="text" class="infraText form-control"
                               tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"
                               value="<?= PaginaSEIExterna::tratarHTML($_POST['sitioInternet']) ?>"
                               onkeypress="return infraMascaraTexto(this,event,50);" maxlength="50"
                               name="sitioInternet" id="sitioInternet"/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                        <label class="infraLabelObrigatorio">Endereço:</label>
                        <input type="text" class="infraText form-control"
                               tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"
                               value="<?= PaginaSEIExterna::tratarHTML($_POST['endereco']) ?>"
                               name="endereco" id="endereco"/>
                    </div>
                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                        <label class="infraLabelObrigatorio">Bairro:</label>
                        <input type="text" class="infraText form-control"
                               tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"
                               value="<?= PaginaSEIExterna::tratarHTML($_POST['bairro']) ?>"
                               name="bairro" id="bairro"/>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4" style="display: none">
                        <label class="infraLabelObrigatorio">País:</label><br/>
                        <input type="text" class="infraText"
                               onkeyup="paisEstadoCidade(this);" value="Brasil"
                               onkeypress="return infraMascaraTexto(this,event,50);"
                               maxlength="50" name="pais" id="pais"/>
                    </div>
                    <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4">
                        <label class="infraLabelObrigatorio">Estado:</label>
                        <select class="infraSelect form-control" tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"
                                name="selEstado" id="selEstado">
                            <?= $strItensSelSiglaEstado ?>
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4">
                        <label class="infraLabelObrigatorio">Cidade:</label>
                        <select class="infraSelect form-control" tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"
                                name="selCidade" id="selCidade">
                            <?= $strItensSelCidade ?>
                        </select>
                    </div>
                    <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4">
                        <label class="infraLabelObrigatorio">CEP:</label>
                        <input type="text" class="infraText form-control"
                               onkeypress="return infraMascaraCEP(this,event);"
                               maxlength="15"
                               tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"
                               value="<?= PaginaSEIExterna::tratarHTML($_POST['cep']) ?>"
                               name="cep" id="cep"/>
                    </div>
                </div>
                <br>
            </fieldset>

            <input type="hidden" name="hdnCadastrar" value=""/>
            <input type="hidden" name="hdnIdEdicao" id="hdnIdEdicao"
                   value="<?php echo $_POST['hdnIdEdicao']; ?>"/>
        </form>

    <?php } ?>
</div>
<?php

PaginaSEIExterna::getInstance()->montarAreaDebug();
PaginaSEIExterna::getInstance()->fecharAreaDados();
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();

//incluindo arquivo com funções JavaScript da página
require_once 'md_pet_interessado_cadastro_js.php';
?>
