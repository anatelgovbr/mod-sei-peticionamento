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

    // PaginaSEI::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);
    $strLinkAjaxValidacoesNumeroSEI = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_validar_num_sei');

    switch ($_GET['acao']) {

        case 'md_pet_vinc_suspender_restabelecer':

            $operacao                   = isset($_GET['operacao']) ? $_GET['operacao'] : $_POST['hdnOperacao'];
            $idVinculo                  = isset($_GET['idVinculo']) ? $_GET['idVinculo'] : $_POST['hdnIdVinculo'];

            $idVinculoRepresent         = isset($_GET['idVinculoRepresent']) ? $_GET['idVinculoRepresent'] : $_POST['hdnIdVinculoRepresent'];
            $strTipoVinculo             = isset($_GET['tipoVinculo']) ? $_GET['tipoVinculo'] : $_POST['hdnTipoVinculo'];
            $hdnIdDocumentoRepresent    = isset($_GET['idDocumentoRepresent']) ? $_GET['idDocumentoRepresent'] : $_POST['hdnIdDocumentoRepresent'];

            //Recuperar dados da Pessoa Juridica
            $objMdPetVinculoDTO = new MdPetVinculoDTO();
            $objMdPetVinculoDTO->retNumIdMdPetVinculo();
            $objMdPetVinculoDTO->retDblCNPJ();
            $objMdPetVinculoDTO->retStrRazaoSocialNomeVinc();
            $objMdPetVinculoDTO->retNumIdContatoRepresentante();
            $objMdPetVinculoDTO->retStrCpfContatoRepresentante();
            $objMdPetVinculoDTO->retStrNomeContatoRepresentante();
            $objMdPetVinculoDTO->setNumIdMdPetVinculo($idVinculo);
	        $objMdPetVinculoDTO->setDistinct(true);
	        $objMdPetVinculoDTO->setNumMaxRegistrosRetorno(1);
            $objMdPetVinculoDTO = (new MdPetVinculoRN())->consultar($objMdPetVinculoDTO);

            // RETORNANDO DADOS DO V�NCULO
            $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
            $objMdPetVincRepresentantDTO->retTodos();
            $objMdPetVincRepresentantDTO->retNumIdMdPetVinculo();
            $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
            $objMdPetVincRepresentantDTO->retStrTipoRepresentante();
            $objMdPetVincRepresentantDTO->retStrStaEstado();
            $objMdPetVincRepresentantDTO->retStrCpfProcurador();
            $objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
            $objMdPetVincRepresentantDTO->retStrCNPJ();
            $objMdPetVincRepresentantDTO->retStrCPF();
            $objMdPetVincRepresentantDTO->retStrTpVinc();
            $objMdPetVincRepresentantDTO->retNumIdContatoVinc();
            $objMdPetVincRepresentantDTO->retStrNomeProcurador();
            $objMdPetVincRepresentantDTO->retNumIdContatoProcurador();
            $objMdPetVincRepresentantDTO->retDblIdProcedimentoVinculo();
            $objMdPetVincRepresentantDTO->retStrStaAbrangencia();
            $objMdPetVincRepresentantDTO->setNumIdMdPetVinculoRepresent($idVinculoRepresent);
            $objMdPetVincRepresentantDTO = (new MdPetVincRepresentantRN())->consultar($objMdPetVincRepresentantDTO);

            if(empty($objMdPetVincRepresentantDTO)){
                throw new InfraException("V�nculo (".$idVinculoRepresent.") n�o encontrado na tabela md_pet_vinculo_represent.");
            }

            $strTipoRepresentante = (new MdPetVincRepresentantDTO())->getStrNomeTipoRepresentante( $objMdPetVincRepresentantDTO->getStrTipoRepresentante() );

            // RETORNANDO DADOS DO DOCUMENTO DA VINCULA��O:
            $objMdPetVincDocumentoDTO = new MdPetVincDocumentoDTO();
            $objMdPetVincDocumentoDTO->setNumIdMdPetVinculoRepresent($objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent());
            $objMdPetVincDocumentoDTO->retDblIdDocumento();
            $objMdPetVincDocumentoDTO->retStrProtocoloFormatadoProtocolo();
            $objMdPetVincDocumentoDTO->retNumIdMdPetVinculoRepresent();
            $objMdPetVincDocumentoDTO->adicionarCriterio(
                array('TipoDocumento', 'TipoDocumento', 'TipoDocumento'),
                array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL),
                array('E', 'P', 'N'),
                array(InfraDTO::$OPER_LOGICO_OR, InfraDTO::$OPER_LOGICO_OR)
            );
            $arrObjMdPetVincDocumentoDTO = (new MdPetVincDocumentoRN())->consultar($objMdPetVincDocumentoDTO);

            // INICIALIZANDO VARI�VEIS:
            $strTitulo = ($_GET['operacao'] == 'A' ? 'Restabelecer ' : 'Suspender ') . $strTipoRepresentante;
            $strAten��o = $_GET['operacao'] == 'A' ? 'O Restabelecimento' : 'A Suspens�o';
            $janelaSelecaoPorNome = SessaoSEIExterna::getInstance()->getAtributo('janelaSelecaoPorNome');
            $strPrimeiroItemValor = 'null';
            $strPrimeiroItemDescricao = '&nbsp;';
            $strValorItemSelecionado = null;
            $strTipo = 'Cadastro';

            break;

        default:
            throw new InfraException("A��o '" . $_GET['acao'] . "' n�o reconhecida.");
    }

} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}

$hashAnexo = "";
$idAnexo = "";

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
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
    <style type="text/css">
    #txtInformativo { font-size: 13px; }
    #field1 { height: auto; width: 97%; margin-bottom: 11px; }
    #field2 { height: auto; width: 97%; margin-bottom: 11px; }
    .sizeFieldset { height: auto; width: 88%; }
    .fieldsetClear { border: none !important; }
    .infraLabelCheckbox { font-size: .875rem; }
    </style>

<?php

$urlBaseLink = "";
$arrComandos = array();
$arrComandos[] = '<button type="button" accesskey="s" name="Salvar" value="Salvar" onclick="salvar()" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
$arrComandos[] = '<button type="button" accesskey="c" name="btnFechar" value="Fechar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

//$strLinkBaseFormEdicao = 'controlador_externo.php?edicaoExibir=true&acao=' . $_GET['acao'];
$strLinkBaseFormEdicao = 'controlador.php?edicaoExibir=true&acao=' . $_GET['acao'];
$strLinkEdicaHash = PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink($strLinkBaseFormEdicao));

$titleConsultar = 'Consultar ' . (($objMdPetVincRepresentantDTO->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL) ? 'Vincula��o' : 'Procura��o');
$strLinkConsultaDocumento = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=documento_visualizar&id_documento=' . $arrObjMdPetVincDocumentoDTO->getDblIdDocumento() . '&arvore=1');

?>

    <!-- Formulario usado para viabilizar fluxo de edi��o de contato -->

    <form id="frmEdicaoAuxiliar" name="frmEdicaoAuxiliar" method="post" action="<?= $strLinkEdicaHash ?>">

        <?php
            PaginaSEI::getInstance()->abrirAreaDados('auto');
            PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
        ?>

        <div class="row">
            <div class="col-12">
                <p>ATEN��O</p>
                <p id="txtInformativo">
                    <? if($strTipoVinculo == 'L'): ?>
                        <? if ($_GET['operacao'] == MdPetVincRepresentantRN::$RP_ATIVO): ?>
                            O Restabelecimento do Respons�vel Legal deve ser motivado em documento espec�fico, a ser indicado no
                            campo abaixo. Todas as Procura��es Eletr�nicas geradas ser�o restabelecidas, o Usu�rio Externo
                            ser� notificado e este ato constar� no processo referente � Pessoa Jur�dica.
                        <? else: ?>
                            A Suspens�o do Respons�vel Legal deve ser motivado em documento espec�fico, a ser indicado no campo abaixo. Todas as Procura��es Eletr�nicas geradas pelo Respons�vel Legal ser�o suspensas, o Usu�rio Externo ser� notificado e este ato constar� no processo referente � Pessoa Jur�dica. A suspens�o como Respons�vel Legal n�o impede o Usu�rio Externo de peticionar em pr�prio nome.
                        <? endif; ?>
                    <? else: ?>
                        <? if ($_GET['operacao'] == MdPetVincRepresentantRN::$RP_ATIVO): ?>
                            O Restabelecimento do Respons�vel Legal deve ser motivado em documento espec�fico, a ser indicado no
                            campo abaixo. Todas as Procura��es Eletr�nicas geradas ser�o restabelecidas, o Usu�rio Externo
                            ser� notificado e este ato constar� no processo referente � Pessoa Jur�dica.
                        <? else: ?>
                            A Suspens�o do <?= $strTipoRepresentante ?> deve ser motivada em documento espec�fico, a ser indicado no campo abaixo. A suspens�o como <?= $strTipoRepresentante ?> n�o impede o Usu�rio Externo de peticionar em pr�prio nome.
                        <? endif; ?>
                    <? endif ?>
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-5 col-lg-4 col-xl-3">
                <div class="form-group">
                    <label id="lblCnpj" class="infraLabelObrigatorio">CPF/CNPJ Outorgante:</label><br/>
                    <input type="text" id="txtCnpj" name="txtCnpj"
                    class="infraText form-control"
                    disabled="disabled"
                    value="<?= PaginaSEI::tratarHTML(InfraUtil::formatarCpfCnpj(($objMdPetVincRepresentantDTO->getStrTpVinc() == 'F' ? $objMdPetVincRepresentantDTO->getStrCPF() : $objMdPetVincRepresentantDTO->getStrCNPJ()))) ?>"
                    onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250"
                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                </div>
            </div>
            <div class="col-sm-12 col-md-7 col-lg-6 col-xl-9">
                <div class="form-group">
                    <label id="lblRazaoSocial" class="infraLabelObrigatorio">Nome/Raz�o Social Outorgante:</label><br/>
                    <input type="text" id="txtRazaoSocial" name="txtRazaoSocial"
                    class="infraText form-control"
                    disabled="disabled"
                    value="<?= PaginaSEI::tratarHTML($objMdPetVincRepresentantDTO->getStrRazaoSocialNomeVinc()) ?>"
                    onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250"
                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-4 col-lg-4 col-xl-3">
                <div class="form-group">
                    <label id="lblCnpj" class="infraLabelObrigatorio">CPF Outorgado:</label><br/>
                    <input type="text" id="txtCpf" name="txtCpf"
                    class="infraText form-control"
                    disabled="disabled"
                    value="<?= PaginaSEI::tratarHTML(InfraUtil::formatarCpf($objMdPetVincRepresentantDTO->getStrCpfProcurador())) ?>"
                    onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250"
                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                </div>
            </div>
            <div class="col-sm-12 col-md-4 col-lg-4 col-xl-6">
                <div class="form-group">
                    <label id="txtRazaoSocial" class="infraLabelObrigatorio">Nome Outorgado:</label><br/>
                    <input type="text" id="txtNome" name="txtNome"
                    disabled="disabled"
                    class="infraText form-control"
                    value="<?= PaginaSEI::tratarHTML($objMdPetVincRepresentantDTO->getStrNomeProcurador()) ?>"
                    onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250"
                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                </div>
            </div>
            <div class="col-sm-12 col-md-4 col-lg-4 col-xl-3">
                <div class="form-group">
                    <label id="txtRazaoSocial" class="infraLabelObrigatorio">Tipo do V�nculo:</label>
                    <div class="input-group mb-3">
                        <input type="text" id="txtTipoVinculo" name="txtTipoVinculo" disabled="disabled" class="infraText form-control rounded" value="<?= $strTipoRepresentante ?>" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        <div class="input-group-append">
                            <a target="_blank" href="<?= $strLinkConsultaDocumento ?>">
                                <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/consultar.svg" title="<?= $titleConsultar ?>" alt="<?= $titleConsultar ?>" class="infraImg mt-1" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-md-5 col-lg-4 col-xl-3">
                <div class="form-group">
                    <label class="infraLabelObrigatorio">N�mero SEI da Justificativa: </label><br/>
                    <div class="input-group mb-3">
                        <input type="text" id="txtNumeroSei" name="txtNumeroSei" class="infraText form-control" value=""
                            onkeypress="return infraMascaraTexto(this,event,250);" maxlength="10"
                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        <button type="button" accesskey="V" style="margin-left: 5px" id="btnValidar" onclick="controlarNumeroSEI();" class="infraButton">
                            <span class="infraTeclaAtalho">V</span>alidar
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-5 col-lg-6 col-xl-9">
                <div class="form-group">
                    <label class="infraLabelObrigatorio"></label><br/>
                    <div class="input-group mb-3">
                        <input type="text" id="txtTipo" name="txtTipo" class="infraText form-control rounded" readonly="readonly" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" value="<?= $txtTipo ?>"/>
                        <div class="input-group-append">
                            <a target="_blank" href="" id="txtTipoLink" style="display:none">
                                <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/consultar.svg" title="Consultar documento da Justificativa" alt="<?= $titleConsultar ?>" class="infraImg mt-1" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Checkbox para suspens�o ou restabelecimento em cascata -->
        <?php if($strTipoVinculo == 'L'): ?>
            <div class="row">
                <div class="col-12">
                    <div class="infraCheckboxDiv">
                        <input type="checkbox" name="rdoCascata" class="infraCheckboxInput" id="optCascata" value="S" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        <label class="infraCheckboxLabel" for="optCascata"></label>
                    </div>
                    <label id="lblCascata" for="optCascata" accesskey="" class="infraLabelCheckbox">
                        Tamb�m <?= $operacao == 'S' ? 'suspender' : 'restabelecer' ?> os atuais Procuradores <?= $operacao == 'S' ? 'ativos' : 'suspensos' ?>
                    </label>
                </div>
            </div>
        <?php endif ?>

        <input type="hidden" name="hdnIdVinculo" id="hdnIdVinculo" value="<?= $idVinculo ?>"/>
        <input type="hidden" name="hdnOperacao" id="hdnOperacao" value="<?= $operacao ?>"/>
        <input type="hidden" name="hdnIdContatoNovo" id="hdnIdContatoNovo" value=""/>
        <input type="hidden" name="hdnIdContato" id="hdnIdContato" value="<?= $objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent() ?>"/>
        <input type="hidden" name="hdnIdDocumento" id="hdnIdDocumento" value=""/>

        <input type="hidden" name="hdnIdContatoProc" id="hdnIdContatoProc" value="<?= $objMdPetVincRepresentantDTO->getNumIdContatoProcurador() ?>"/>
        <input type="hidden" name="hdnIdVinculoRepresent" id="hdnIdVinculoRepresent" value="<?= $idVinculoRepresent ?>"/>
        <input type="hidden" name="hdnStrTipoVinculo" id="hdnStrTipoVinculo" value="<?= $strTipoVinculo ?>"/>
        <input type="hidden" name="hdnIdDocumentoRepresent" id="hdnIdDocumentoRepresent" value="<?= $hdnIdDocumentoRepresent ?>"/>

    </form>

<?php
PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
//PaginaSEI::getInstance()->montarAreaDebug();
PaginaSEI::getInstance()->fecharAreaDados();
require_once 'md_pet_vinc_pe_suspender_restabelecer_js.php';
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>
