<?php
/**
 * Created by PhpStorm.
 * User: jhon.carvalho
 * Date: 05/03/2018
 * Time: 10:16
 */
try {
    require_once dirname(__FILE__) . '/../../SEI.php';
    session_start();
    switch ($_GET['acao']) {
        case 'md_pet_vinc_usu_ext_pe_cadastrar':
            $strTitulo = 'Nova Procuração Eletrônica';
            $arrComandos[] = '<button type="button" onclick="peticionar()"  name="sbmPeticionar" id="sbmPeticionar" value="Peticionar" accesskey="P"  class="infraButton"><span class="infraTeclaAtalho">P</span>eticionar</button>';
            $arrComandos[] = '<button type="button" accesskey="C" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinc_usu_ext_pe_listar&acao_origem=' . $_GET['acao'])) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';
            $arrComandosInferior[] = '<button type="button" onclick="peticionar()"  name="sbmPeticionar" id="sbmPeticionarInferior" value="Peticionar" accesskey="P"  class="infraButton"><span class="infraTeclaAtalho">P</span>eticionar</button>';
            $arrComandosInferior[] = '<button type="button" accesskey="C" id="btnCancelarInferior" value="Cancelar" onclick="location.href=\'' . PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinc_usu_ext_pe_listar&acao_origem=' . $_GET['acao'])) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';
            break;
        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }


} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}
$urlDoc1 = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=documento_vinculacao&tipo=1');
$urlDoc2 = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=documento_vinculacao&tipo=2');
$urlDoc3 = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=documento_vinculacao&tipo=3');
$selectPjOutorgante = MdPetVincRepresentantINT::montarSelectOutorgante(null, null, null);

$idUsuarioExterno = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();
$usuarioDTO = new UsuarioDTO();
$usuarioRN = new UsuarioRN();
$usuarioDTO->retNumIdContato();
$usuarioDTO->retDblCpfContato();
$usuarioDTO->setNumIdUsuario($idUsuarioExterno);
$contatoExterno = $usuarioRN->consultarRN0489($usuarioDTO);

$idContatoExterno = $contatoExterno->getNumIdContato();
$cpfContato = $contatoExterno->getDblCpfContato();

//consultar orgão externo
$siglaOrgao = SessaoSEIExterna::getInstance()->getStrSiglaOrgaoUsuarioExterno();

if (isset($_POST['hdnIdUsuario']) && $_POST['hdnIdUsuario'] != '') {
    $dados = $_POST;

    $idsUsuarios = $_POST['hdnIdUsuario'];
    $id = explode('+', $idsUsuarios);

    $idContatoVinc = $_POST['selPessoaJuridica'];
    $dados['idContato'] = $idContatoVinc;
    $dados['chkDeclaracao'] = 'S';
    $dados['idContatoExterno'] = $idContatoExterno;

    $mdPetVinUsuExtProcRN = new MdPetVinUsuExtProcRN();
    $mdPetVinUsuExtProcRN->gerarProcedimentoVinculoProcuracao($dados);
    header('Location: ' . SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinc_usu_ext_pe_listar&acao_origem=' . $_GET['acao']));
    die;
}
//Listando Todos os tipos de poderes
$objMdPetTipoPoderLegalDTO = new MdPetTipoPoderLegalDTO();
$objMdPetTipoPoderLegalDTO->retTodos(true);
$objMdPetTipoPoderLegalDTO->setOrdStrNome(infraDTO::$TIPO_ORDENACAO_ASC);
$objMdPetTipoPoderLegalDTO->setStrSinAtivo("S");
$objMdPetTipoPoderLegalRN = new MdPetTipoPoderLegalRN();
$arrObjMdPetTipoPoderLegalDTO = $objMdPetTipoPoderLegalRN->listar($objMdPetTipoPoderLegalDTO);

$arrObjMdPetTipoPoderLegalDTONovo = array();

foreach ($arrObjMdPetTipoPoderLegalDTO as $itemObjMdPetTipoPoderLegalDTO) {
    if ($itemObjMdPetTipoPoderLegalDTO->getNumIdTipoPoderLegal() == 1) {
        array_unshift($arrObjMdPetTipoPoderLegalDTONovo, $itemObjMdPetTipoPoderLegalDTO);
    } else {
        array_push($arrObjMdPetTipoPoderLegalDTONovo, $itemObjMdPetTipoPoderLegalDTO);
    }
}
$arrObjMdPetTipoPoderLegalDTO = $arrObjMdPetTipoPoderLegalDTONovo;
//Verificando a Existencia de Vinculo como Responsavel Legal
//Responsável Legal
$objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
$objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();

$objMdPetVincRepresentantDTO->retNumIdMdPetVinculo();
$objMdPetVincRepresentantDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL);
$objMdPetVincRepresentantDTO->setNumIdContato($idContatoExterno);
$objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
$existenciaVinculo = true;
$arrObjMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);
if (!empty($arrObjMdPetVincRepresentantDTO)) {
    $existenciaVinculo = false;
}
//Verificando se o Usuário é Procurador Espécial em alguma PRocuração
$objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
$objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();

$objMdPetVincRepresentantDTO->setNumIdContato($idContatoExterno);
$objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
$objMdPetVincRepresentantDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL);
$objMdPetVincRepresentantDTO->retNumIdContato();
//Existencia Vinculo PRocuração Especial
$objMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);
$existeEspecialVinculo = true;
if (!empty($objMdPetVincRepresentantDTO)) {
    $existeEspecialVinculo = false;
}

//Verificar as parametrizações de Vinculação de pessoa Física
$mdPetVincTpProcessoRN = new MdPetVincTpProcessoRN();
$objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();
$objMdPetVincTpProcessoDTO->setNumIdMdPetVincTpProcesso(MdPetVincTpProcessoRN::$ID_FIXO_MD_PET_VINCULO_USU_EXT);
$objMdPetVincTpProcessoDTO->setStrSinAtivo('S');
$objMdPetVincTpProcessoDTO->retTodos();
$objMdPetVincTpProcessoDTO = $mdPetVincTpProcessoRN->consultar($objMdPetVincTpProcessoDTO);


if (($existenciaVinculo == false || $existeEspecialVinculo == false) && $objMdPetVincTpProcessoDTO) {
    $bloqueioRadio = "false";
} else {
    $bloqueioRadio = "true";

}

$data = new DateTime();
$data->add(new DateInterval('P1D'));
$dataAtual = $data->format('d/m/Y');

//Verificar as parametrizações de Vinculação de pessoa Física
$mdPetVincTpProcessoRN = new MdPetVincTpProcessoRN();
$objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();
$objMdPetVincTpProcessoDTO->setNumIdMdPetVincTpProcesso(MdPetVincTpProcessoRN::$ID_FIXO_MD_PET_VINCULO_USU_EXT_PF);
$objMdPetVincTpProcessoDTO->setStrSinAtivo('S');
$objMdPetVincTpProcessoDTO->retTodos();
$objMdPetVincTpProcessoDTO = $mdPetVincTpProcessoRN->consultar($objMdPetVincTpProcessoDTO);
if ($objMdPetVincTpProcessoDTO) {
    $bloqueioRadioPF = false;
} else {
    $bloqueioRadioPF = true;
}


//Existencia

PaginaSEIExterna::getInstance()->montarDocType();
PaginaSEIExterna::getInstance()->abrirHtml();
PaginaSEIExterna::getInstance()->abrirHead();
PaginaSEIExterna::getInstance()->montarMeta();
PaginaSEIExterna::getInstance()->montarTitle(':: ' . PaginaSEIExterna::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaSEIExterna::getInstance()->montarStyle();
PaginaSEIExterna::getInstance()->abrirStyle();
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
require_once 'md_pet_procuracao_especial_cadastro_css.php';
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
    <form id="frmPeticionarProcesso"
          method="post"
          action="<?= PaginaSEIExterna::getInstance()
              ->formatarXHTML(SessaoSEIExterna::getInstance()
                  ->assinarLink('controlador_externo.php?acao=' . $_GET['acao'] .
                      '&acao_origem=' . $_GET['acao'])) ?>">

        <?
        PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
        PaginaSEIExterna::getInstance()->abrirAreaDados('auto');

        ?>

        <div class="row">
            <div class="col-sm-12 col-md-6 col-lg-5 col-xl-4">
                <div class="form-group">
                    <label class="infraLabelObrigatorio" for="selTipoProcuracao">
                        Tipo de Procuração:
                    </label>
                    <img align="top" name="ajuda"
                        src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>"
                        <?= PaginaSEI::montarTitleTooltip('Para emitir Procuração Eletrônica Especial com todos os poderes previstos no Sistema, antes é necessário que você seja Responsável Legal de alguma Pessoa Jurídica. \n \n Se for o caso e o tipo "Procuração Eletrônica Especial" não está listado, acesse o menu "Responsável Legal de Pessoa Jurídica" e, em seguida, o botão "Novo Responsável Legal" para realizar o cadastro.', 'Ajuda') ?>
                        class="infraImgModulo"/>
                    <br/>
                    <select name="selTipoProcuracao" id="selTipoProcuracao" class="infraSelect form-select"
                            onchange="pegaInfo(this);" tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>">
                        <option value="" selected="selected">
                            Selecione
                        </option>
                        <!-- Caso não exista vinculo do usuário externo como responsavel legal, não mostrar opção Procuração Especial  -->
                        <?php if ($existenciaVinculo == false) { ?>
                            <option value="<?php echo MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL ?>">
                                Procuração Eletronica Especial
                            </option>
                        <?php } ?>
                        <option value="<?php echo MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES ?>">
                            Procuração Eletronica Simples
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-sm-12 col-md-9 col-lg-7 col-xl-6">
                <div id="hiddenOutorgante" style="display:none;">
                    <div class="form-group">
                        <!-- Procuracao Simples -->
                        <label for="lblOutorgante" class="infraLabelObrigatorio">
                            Outorgante:
                            <img name="ajuda"
                                src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>"
                                <?= PaginaSEI::montarTitleTooltip('A opção "Pessoa Jurídica" estará habilitada somente se você for o Responsável Legal com situação Ativa ou quando possuir Procuração Eletrônica Especial vigente de alguma Pessoa Jurídica.', 'Ajuda') ?>
                                class="infraImgModulo"/>
                        </label>
                        <br>

                        <div class="form-check form-check-inline">
                            <input type="radio" onchange="showPessoaOutorganteHidden();" name="Outorgante" id="rbOutorgante1" class="infraRadio" <?php echo $bloqueioRadioPF ? "disabled='disabled' style='display:none'" : ""; ?> checked="checked" value="PF">
                            <label <?php echo $bloqueioRadioPF ? "style='display:none'" : ""; ?>
                                    for="rbOutorgante1"
                                    id="lvbFisica"
                                    class="infraLabelRadi mt-2">
                                Pessoa Física
                            </label>
                            <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>"
                                name="ajuda" style="<?php echo $bloqueioRadioPF ? "display:none" : ""; ?>"
                                <?= PaginaSEI::montarTitleTooltip('Ao selecionar a opção Pessoa Física, a Procuração Eletrônica Simples terá como objetivo definir alguém para representar você, enquanto Pessoa Física. \n \n Ou seja, será uma Procuração de Usuário Externo para Usuário Externo.', 'Ajuda') ?>
                                class="infraImgModulo mx-2 mt-n2"/>
                        </div>

                        <div class="form-check form-check-inline radioJuridica">
                            <input type="radio" onchange="showPessoaOutorgante();" name="Outorgante" id="rbOutorgante2" class="infraRadio" value="PJ">
                            <label for="rbOutorgante2" id="lvbJuridica" class="infraLabelRadio mt-2">
                                Pessoa Jurídica
                            </label>
                            <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>"
                                name="ajuda"
                                <?= PaginaSEI::montarTitleTooltip('Ao selecionar a opção Pessoa Jurídica, a Procuração Eletrônica terá como objetivo definir alguém para representar a Pessoa Jurídica que você já representa como Responsável Legal ou como Procurador Especial.', 'Ajuda') ?>
                                class="infraImgModulo ml-2 mt-n2" id="ajudaPJ"/>
                        </div>
                    </div>
                </div>
                <!-- Procuracao Simples -->

                <!-- Procuração Especial -->
                <div id="procuracaoEspecial">
                    <div class="form-group">
                        <label class="infraLabelObrigatorio" for="selPessoaJuridica">
                            Pessoa Jurídica Outorgante:
                        </label>
                        <img align="top" src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>"
                            name="ajuda"
                            <?= PaginaSEI::montarTitleTooltip('Neste campo são listadas as Pessoas Jurídicas que você é o Responsável Legal com situação Ativa, pois somente o Responsável Legal de Pessoa Jurídica pode emitir Procuração Eletrônica Especial. \n \n Se você é o Responsavel Legal e a Pessoa Jurídica não foi listada, então confira se a vinculação está cadastrada no menu "Responsável Legal de Pessoa Jurídica": \n \n 1.1. Se a Pessoa Jurídica não for listada no citado menu, clique no botão "Novo Responsável Legal" e realize o cadastro. \n 1.2. Se a Pessoa Jurídica foi listada no citado menu, mas não está com situação Ativa, regularize a situação juntamente ao Órgão.') ?>
                            class="infraImgModulo"/>
                        <br/>
                        <select class="infraSelect form-selec"
                                name="selPessoaJuridica"
                                id="selPessoaJuridica"
                                tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
                            <?php echo $selectPjOutorgante; ?>
                        </select>
                    </div>
                </div>
                <!-- Procuração Especial -->
            </div>
            <div class="col-sm-12 col-md-9 col-lg-10 col-xl-8">
                <div id="PessoaJuridicaOutorgante" style="display: none">
                    <div class="form-group">
                        <label class="infraLabelObrigatorio" for="selPessoaJuridica" id="lvbPJProSimples">
                            Pessoa Jurídica Outorgante:
                            <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>"
                                align="top"
                                name="ajuda"
                                id="imgPj"
                                <?= PaginaSEI::montarTitleTooltip('Neste campo são listadas as Pessoas Jurídicas que você é o Responsável Legal com situação Ativa ou que possua Procuração Eletrônica Especial vigente. \n \n Se você é o Responsavel Legal e a Pessoa Jurídica não foi listada, então confira se a vinculação está cadastrada no menu "Responsável Legal de Pessoa Jurídica": \n \n 1.1. Se a Pessoa Jurídica não for listada no citado menu, clique no botão "Novo Responsável Legal" e realize o cadastro. \n 1.2. Se a Pessoa Jurídica foi listada no citado menu, mas não está com situação Ativa, regularize a situação juntamente ao Órgão.', 'Ajuda') ?>
                                class="infraImgModulo"/>
                        </label>
                        <br/>
                        <select style="display: inherit"
                                name="selPessoaJuridicaProcSimples"
                                class="infraSelect form-control"
                                id="selPessoaJuridicaProcSimples"
                                tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
                            <?php echo $selectPjOutorgante; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <div id="txtExplicativo"></div>
            </div>
        </div>
        <div id="procuracaoEspecialTable">
            <div class="row">
                <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
                    <label for="txtCpf"
                           class="infraLabelObrigatorio">
                        CPF do Usuário Externo:
                    </label>
                    <img align="top" src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>"
                         name="ajuda"
                        <?= PaginaSEI::montarTitleTooltip('A pesquisa é realizada somente sobre Usuários Externos liberados.  \n \n A consulta somente pode ser efetuada pelo CPF do Usuário Externo.') ?>
                         class="infraImgModulo"/><br/>
                    <div class="input-group">
                        <input name="txtNumeroCpfProcurador"
                               id="txtNumeroCpfProcurador"
                               maxlength="14"
                               type="text"
                               class="infraText form-control"
                               tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"
                               onkeypress="return infraMascaraCPF(this);"
                               onkeyup="return infraMascaraCPF(this);"
                               onkeydown="return infraMascaraCPF(this);"/>
                        <button type="button"
                                accesskey="l"
                                name="btnValidar"
                                id="btnValidarEspecial"
                                disabled
                                class="infraButton btnProc"
                                onclick="consultarUsuarioExternoValido();"
                                tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
                            Va<span class="infraTeclaAtalho">l</span>idar
                        </button>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                    <label for="txtNomeProcurador" class="infraLabelObrigatorio">Nome do Usuário Externo: <img
                                align="top"
                                src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>"
                                name="ajuda" <?= PaginaSEI::montarTitleTooltip('Caso seja listado mais de um cadastro de Usuário Externo utilizando o mesmo CPF, escolha neste campo o nome do cadastro correto.') ?>
                                class="infraImgModulo"/></label>
                    <br/>
                    <!-- Combo Usuário Externo -->
                    <div class="input-group">
                        <select name="selUsuario" onchange="alterarHidden(this);"
                                id="selUsuario"
                                class="infraSelect form-control"
                                onchange=""
                                tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>">
                        </select>
                        <button type="button" accesskey="o" class="infraButton btnProc"
                                id="btnAdicionarProcurador" onclick="criarRegistroTabelaProcuracao();"
                                tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
                            Adici<span class="infraTeclaAtalho">o</span>nar
                        </button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <table width="99%" class="infraTable" summary="Procurações" id="tbUsuarioProcuracao"
                           style="display:none;">
                        <caption class="infraCaption">&nbsp;</caption>
                        <tr>
                            <th class="infraTh" width="0" style="display:none;">ID</th>
                            <th class="infraTh" width="140px">CPF</th>
                            <th class="infraTh" width="0">Usuário Externo</th>
                            <th class="infraTh" width="60px">Ações</th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div id="procuracaoSimplesFieldSet" class="rowFieldSet">
            <fieldset id="fldResposta"class="infraFieldset form-control fieldset-comum">
                <legend class="infraLegend"> Dados da Procuração</legend>
                <div class="row">
                    <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4 mb-4">
                        <div class="form-gruop">
                            <label for="txtCpf" class="infraLabelObrigatorio">
                                CPF do Usuário Externo:
                                <img align="top"
                                    src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>"
                                    name="ajuda"
                                    <?= PaginaSEI::montarTitleTooltip('A pesquisa é realizada somente sobre Usuários Externos liberados.  \n \n A consulta somente pode ser efetuada pelo CPF do Usuário Externo.', 'Ajuda') ?>
                                    class="infraImgModulo"/>
                            </label>
                            <br/>
                            <div class="input-group">
                                <input name="txtNumeroCpfProcuradorSimples" id="txtNumeroCpfProcuradorSimples"
                                    maxlength="14" type="text" class="infraText form-control"
                                    tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"
                                    onkeypress="return infraMascaraCPF(this);"
                                    onkeyup="return infraMascaraCPF(this);"
                                    onkeydown="return infraMascaraCPF(this);"/>
                                <button type="button" accesskey="V" name="btnValidar"
                                        id="btnValidarSimples" disabled class="infraButton btnProc"
                                        onclick="consultarUsuarioExternoValidoSimples();"
                                        tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
                                    <span class="infraTeclaAtalho">V</span>alidar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-8 col-lg-8 col-xl-8">
                        <div class="form-group">
                            <label for="txtNomeProcurador" class="infraLabelObrigatorio">
                                Nome do Usuário Externo:
                                <img align="top" src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>"
                                    name="ajuda" <?= PaginaSEI::montarTitleTooltip('Caso seja listado mais de um cadastro de Usuário Externo utilizando o mesmo CPF, escolha neste campo o nome do cadastro correto.', 'Ajuda') ?>
                                    class="infraImgModulo"/>
                            </label>
                            <br/>
                            <!-- Combo Usuário Externo -->
                            <select name="selUsuarioSimples" onchange="alterarHidden(this);"
                                    id="selUsuarioSimples"
                                    class="infraSelect form-control"
                                    onchange=""
                                    tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>">
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row pt-0">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <div class="form-group">
                            <!-- Combo Tipo de Poderes -->
                            <label for="lblTipoPoder" class="infraLabelObrigatorio">Poderes:
                                <img align="top"
                                    src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>"
                                    name="ajuda" <?= PaginaSEI::montarTitleTooltip('Escolha os Poderes sobre os quais o Procurador representará o Outorgante. \n \n Somente se for concedido o poder para "Receber, Cumprir e Responder Intimação Eletrônica" (destacado abaixo) o Procurador receberá Intimações Eletrônicas destinadas ao Outorgante e participará de todo o fluxo subsequente a respeito da intimação recebida.', 'Ajuda') ?>
                                    class="infraImgModulo"/></label>

                            <!-- Alterar para multiplo -->
                            <div id="listaPoderes">
                                <select onchange="" id="selTpPoder" name="selTpPoder[]" multiple="multiple" class="infraSelect">
                                    <?php
                                    foreach ($arrObjMdPetTipoPoderLegalDTO as $key => $value) {
                                        if ($value->getNumIdTipoPoderLegal() == 1) {
                                            echo '<option value="' . $value->getNumIdTipoPoderLegal() . '">* ' . $value->getStrNome() . '</option>';
                                        } else {
                                            echo '<option value="' . $value->getNumIdTipoPoderLegal() . '">' . $value->getStrNome() . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row pt-0">
                    <div class="col-7 col-sm-7 col-md-7 col-lg-7 col-xl-7">
                        <div class="form-group">
                            <label for="lblValidade" class="infraLabelObrigatorio">
                                Validade:
                            </label>
                            <div>
                                <!-- Radio Validade  -->
                                <input type="radio" onchange="showDataNot();" name="Validade"
                                    id="rbValidade" class="infraRadio" value="1">

                                <label id="lvbIndeterminado" name="lvbIndeterminado" for="rbValidade" class="infraLabelRadio">Indeterminado </label>

                                <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>"
                                    name="ajuda" class="infraImgModulo mr-2"
                                    <?= PaginaSEI::montarTitleTooltip('Ao selecionar esta opção, o Procurador representará o Outorgante por prazo indeterminado. \n \n Contudo, a qualquer momento o Outorgante poderá Revogar a Procuração ou o próprio Outorgado poderá Renunciar a Procuração.', 'Ajuda') ?>/>

                                <input type="radio" onchange="showData();" class="infraRadio" name="Validade" id="rbValidade2" value="2"">

                                <label for="rbValidade2" id="lblUsuExterno" class="infraLabelRadio">Determinado </label> <img
                                        src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>"
                                        name="ajuda" class="infraImgModulo"
                                    <?= PaginaSEI::montarTitleTooltip('Ao selecionar esta opção, o Procurador representará o Outorgante até a Data Limite indicada no campo ao lado. \n \n Contudo, a qualquer momento o Outorgante poderá Revogar a Procuração ou o próprio Outorgado poderá Renunciar a Procuração.', 'Ajuda') ?>/>
                            </div>
                        </div>
                    </div>
                    <div class="col-5 col-sm-5 col-md-5 col-lg-5 col-xl-5" id="dvDataLimite" style="display:none;">
                        <div class="form-group">
                            <label id="lblDt" for="lvlDt" style="display:none;"
                                class="infraLabelObrigatorio">Data Limite:</label> <br>
                            <div class="input-group">
                                <input type="text" name="txtDt" id="txtDt" style="display:none;"
                                    value=""
                                    onkeypress="return infraMascara(this, event, '##/##/####');"
                                    class="infraText form-control"/>
                                <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/calendario.svg?<?= Icone::VERSAO ?>"
                                    id="imgDt"
                                    title="Selecionar Data" style="display:none;"
                                    alt="Selecionar Data" class="infraImg"
                                    onclick="infraCalendario('txtDt',this,false,'<?= InfraData::getStrDataAtual() . ' 00:00' ?>');"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row pt-0">
                    <div class="col-sm-12 col-md-8 col-lg-6 col-xl-5">
                        <div class="form-group">
                            <!-- Abrangência  -->
                            <label for="lblAbrangencia" class="infraLabelObrigatorio">
                                Abrangência:
                            </label>
                            <div style="margin-top: -6px;">
                                <!-- Radio Qualquer  -->
                                <input type="radio" name="abrangencia"
                                    id="rbAbrangencia1"
                                    class="infraRadio"
                                    onchange="showTable1()" ;
                                    value="Q">

                                <label for="rbAbrangencia1" id="lvbIndeterminado" class="infraLabelRadio">
                                    Qualquer Processo em Nome do Outorgante
                                </label>
                                <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>"
                                    name="ajuda"
                                    <?= PaginaSEI::montarTitleTooltip('Ao selecionar esta opção, o Procurador representará o Outorgante em qualquer Processo.', 'Ajuda') ?>
                                    class="infraImgModulo"/>
                                <br>
                                <!-- Especifico  -->
                                <input type="radio" name="abrangencia"
                                    id="rbAbrangencia"
                                    class="infraRadio"
                                    onchange="showTable2()" ;
                                    value="E">

                                <label for="rbAbrangencia" id="lvbIndeterminado" class="infraLabelRadio">
                                    Processos Específicos
                                </label>
                                <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg?<?= Icone::VERSAO ?>"
                                    name="ajuda"
                                    <?= PaginaSEI::montarTitleTooltip('Ao selecionar esta opção, o Procurador representará o Outorgante somente em processos específicos, que devem ser adionados na lista abaixo. \n \n Após marcar esta opção, torna-se necessário informar o número válido de cada processo sobre os quais o Procurador poderá atuar.', 'Ajuda') ?>
                                    class="infraImgModulo"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="procDados" style="display:none;">
                    <div class="row">
                        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4">
                            <div class="form-group">
                                <!-- INICIO NUMERO DO PROCESSO -->
                                <label id="lblNumeroSei" for="txtNumeroProcesso" accesskey="n"
                                    class="infraLabelObrigatorio"><span class="infraTeclaAtalho">N</span>úmero do
                                    Processo:</label><br>
                                <div class="input-group">
                                    <input type="text" id="txtNumeroProcesso" name="txtNumeroProcesso"
                                        class="infraText form-control" maxlength="100"
                                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"
                                        value="<?= PaginaSEI::tratarHTML($txtNumeroProcesso) ?>"/>
                                    <button tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" accesskey="a"
                                            type="button" id="btnValidarProcesso" onclick="validarNumeroProcesso()"
                                            disabled
                                            class="infraButton">V<span class="infraTeclaAtalho">a</span>lidar
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-8 col-xl-8">
                            <div class="dadosProcesso">
                                <div class="input-group">
                                    <input type="text" id="txtTipo" name="txtTipo" class="infraText form-control"
                                           readonly="readonly"
                                           value="<?= PaginaSEI::tratarHTML($txtTipo) ?>" disabled/>
                                    <button type="button" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"
                                            accesskey="i" style="display:none;" onclick="adicionarProcesso();" disabled
                                            id="btnAdicionar" class="infraButton">Ad<span
                                                class="infraTeclaAtalho">i</span>cionar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <!-- TABELA DE PROCESSOS -->

                            <table width="100%" class="infraTable" summary="Processos" id="tbProcessos" style="display:none;">
                                <caption class="infraCaption">&nbsp;</caption>
                                <tr>
                                    <th class="infraTh" style="display:none;">ID</th>
                                    <th class="infraTh"><div style="width: 180px">Processo</div></th>
                                    <th class="infraTh"><div style="width: 180px">Tipo de Processo</div></th>
                                    <th class="infraTh"><div>Ações</div></th>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <!-- FIM NUMERO DO PROCESSO -->

                    <!-- INICIO TIPO DO PROCESSO VALIDADO -->

                </div>

            </fieldset>

            <!-- Hidden dos Campos de Procuração Simples -->
            <input type="hidden" name="hdnIdProcedimento" id="hdnIdProcedimento"/>
            <input type="hidden" name="hdnTbProcessos" id="hdnTbProcessos"/>
            <input type="hidden" name="hdnTbNumeroProc" id="hdnTbNumeroProc"/>
            <input type="hidden" name="hdnRbValidate" id="hdnRbValidate"/>
            <input type="hidden" name="hdnCpf" id="hdnCpf"/>
            <input type="hidden" name="hdnExiteProc" id="hdnExiteProc"/>
            <input type="hidden" name="hdnRbAbrangencia" id="hdnRbAbrangencia"/>
            <input type="hidden" name="hdnRbOutorgante" id="hdnRbOutorgante"/>
            <input type="hidden" name="hdnTpPoderes" id="hdnTpPoderes"/>
            <input type="hidden" name="hdnBloqueioRadio" id="hdnBloqueioRadio" value="<?php echo $bloqueioRadio; ?>"/>
            <input type="hidden" name="hdnDtAtual" id="hdnDtAtual" value="<?php echo $dataAtual; ?>"/>
            <!-- Hidden dos Campos de Procuração Simples - FIM -->


            <input type="hidden" name="hdnCPF" id="hdnCPF"/>
            <input type="hidden" name="hdnIdUsuarioProcuracao" id="hdnIdUsuarioProcuracao"/>
            <input type="hidden" name="hdnIdUsuario" id="hdnIdUsuario"/>

            <input type="hidden" name="hdnTbUsuarioProcuracao" id="hdnTbUsuarioProcuracao"/>
            <input type="hidden" name="hdnIdContExterno" id="hdnIdContExterno" value="<?= $idContatoExterno ?>"/>
            <input type="hidden" name="hdnCpfContExterno" id="hdnCpfContExterno" value="<?= InfraUtil::formatarCpf($cpfContato) ?>"/>

            <br/>
            <? PaginaSEIExterna::getInstance()->fecharAreaDados(); ?>
    </form>
<?
require_once 'md_pet_procuracao_especial_cadastro_js.php';
//PaginaSEIExterna::getInstance()->montarAreaDebug();
PaginaSEIExterna::getInstance()->montarBarraComandosInferior($arrComandosInferior);
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
