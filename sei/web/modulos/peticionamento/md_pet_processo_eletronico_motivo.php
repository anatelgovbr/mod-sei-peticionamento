<?php
/**
 * Created by PhpStorm.
 * User: jhon.carvalho
 * Date: 06/04/2018
 * Time: 11:13
 */
require_once dirname(__FILE__) . '/../../SEI.php';

session_start();

SessaoSEIExterna::getInstance()->validarLink();

SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);

switch ($_GET['acao']) {
    case 'processo_eletronico_responder_motivo_renunciar':
        $strTitulo = 'Renunciar Procuração Eletrônica';
        $strMotivo = 'Motivo da Renúncia (constará no teor do documento de Renúncia que será gerado):';
        break;
    case 'processo_eletronico_responder_motivo_revogar':
        $strTitulo = 'Revogar Procuração Eletrônica';
        $strMotivo = 'Motivo da Revogação (constará no teor do documento de Revogação que será gerado):';
        break;

}
$strLinkRetorno = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinc_usu_ext_pe_listar&acao_origem=' . $_GET['acao'] . '#ID-' . $_GET['id_vinculacao']);
$arrComandos = array();
$arrComandos[] = '<button type="button" accesskey="P" name="btnPeticionar" onclick="onSubmitForm()" class="infraButton"><span class="infraTeclaAtalho">P</span>eticionar</button>';
$arrComandos[] = '<button type="button" accesskey="c" name="btnFechar" value="Fechar" onclick="location.href=\''. $strLinkRetorno . '\'" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';
$id_contato_vin = $_GET['id_contato_vinc'];
$id_procedimento = $_GET['id_procedimento'];
$cpfProcurador = $_GET['cpf'];
$idProcuracao = $_GET['id_documento'];
$id_vinculacao = $_GET['id_vinculacao'];
$tpDocumento = $_GET['tpDocumento'];
$motivoOk = false;
$tipoVinculo = $_GET['tpVinculo'];
$tipoProcuracao = $_GET['tpProc'];

if($tpDocumento == 'revogar'){
    $termo = "Revogação";
} else {
    $termo = "Renúncia";
}

$mdPetVincRepresentantRN = new MdPetVincRepresentantRN();
$objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
$objMdPetVincRepresentantDTO->retTodos();
$objMdPetVincRepresentantDTO->retStrNomeTipoRepresentante();
$objMdPetVincRepresentantDTO->retDthDataLimite();
$objMdPetVincRepresentantDTO->retStrNomeProcurador();
$objMdPetVincRepresentantDTO->retStrCpfProcurador();
$objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
$objMdPetVincRepresentantDTO->retStrCNPJ();
$objMdPetVincRepresentantDTO->retStrCPF();
$objMdPetVincRepresentantDTO->retStrNomeTipoRepresentante();
$objMdPetVincRepresentantDTO->setNumIdMdPetVinculoRepresent($id_vinculacao);
$objMdPetVincRepresentantDTO = (new MdPetVincRepresentantRN)->consultar($objMdPetVincRepresentantDTO);

$tpProcuracao = '';
if ($objMdPetVincRepresentantDTO->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL) {
    $tpProcuracao = 'Procuração Eletrônica Especial';
} else if ($objMdPetVincRepresentantDTO->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES) {
    $tpProcuracao = 'Procuração Eletrônica Simples';
}

$situacao = "";
if ($objMdPetVincRepresentantDTO->getStrStaEstado() == MdPetVincRepresentantRN::$RP_ATIVO) {
    $situacao = "Ativa";
}


$objMdPetRelVincRepProtocDTO = new MdPetRelVincRepProtocDTO();
$objMdPetRelVincRepProtocDTO->retTodos();
$objMdPetRelVincRepProtocDTO->setNumIdVincRepresent($id_vinculacao);
$arrObjMdPetRelVincRepProtocDTO = (new MdPetRelVincRepProtocRN)->listar($objMdPetRelVincRepProtocDTO);

$arrProtocolosAbrangencia = [];

if ($arrObjMdPetRelVincRepProtocDTO) {
    foreach ($arrObjMdPetRelVincRepProtocDTO as $objMdPetRelVincRepProtoc) {
        $objProtocoloDTO = new ProtocoloDTO();
        $objProtocoloDTO->retStrProtocoloFormatado();
        $objProtocoloDTO->setDblIdProtocolo($objMdPetRelVincRepProtoc->getNumIdProtocolo());
        $objProtocoloDTO = (new ProtocoloRN())->consultarRN0186($objProtocoloDTO);
        if ($objProtocoloDTO) {
            array_push($arrProtocolosAbrangencia, $objProtocoloDTO->getStrProtocoloFormatado());
        }
    }
}

if (isset($_POST['txtJustificativa']) && $_POST['txtJustificativa'] != '') {

    /*$mdPetVinUsuExtProcRN = new MdPetVinUsuExtProcRN();
    $mdPetVinUsuExtProc = $mdPetVinUsuExtProcRN->gerarProcedimentoVinculoProcuracaoMotivo($_POST);
    $motivoOk = $mdPetVinUsuExtProc;*/
}

//PaginaSEIExterna::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);

PaginaSEIExterna::getInstance()->montarDocType();
PaginaSEIExterna::getInstance()->abrirHtml();
PaginaSEIExterna::getInstance()->abrirHead();
PaginaSEIExterna::getInstance()->montarMeta();
PaginaSEIExterna::getInstance()->montarTitle(PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo);
PaginaSEIExterna::getInstance()->montarStyle();
PaginaSEIExterna::getInstance()->abrirStyle();

PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
if (0){
?>
<script type="text/javascript">
    <?} ?>
    function onSubmitForm() {

        var txtMotivo = document.getElementById('txtJustificativa').value;
        if (txtMotivo == '') {
            alert('Informe o Motivo da <?php echo $termo; ?>');
            return false;
        } else {

            parent.infraAbrirJanelaModal('<?php echo PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinc_pe_desvinculo_concluir&tipoDocumento=' . $tpDocumento . '&tpVinc=' . $tipoVinculo . '&acao_origem=' . $_GET['acao']))?>',
                800,
                500,
                '', //options
                false); //modal
            //document.getElementById('frmPesquisa').submit();
        }
    }
    <?php

    if(0){ ?>
</script>
<?
}
PaginaSEIExterna::getInstance()->fecharJavaScript();
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo);
?>
<style>

</style>
<form id="frmPesquisa" method="post"
      action="<?= SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">
    <?
    PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
    PaginaSEIExterna::getInstance()->abrirAreaDados("12em");
    ?>

    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <fieldset class="infraFieldset form-control" style="height: auto">
                <legend class="infraLegend">Informações sobre a Procuração</legend>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <label class="infraLabelObrigatorio">CPF / CNPJ
                            Outorgante: </label><?php echo (is_null($objMdPetVincRepresentantDTO->getStrCNPJ())) ? InfraUtil::formatarCpf($objMdPetVincRepresentantDTO->getStrCPF()) : InfraUtil::formatarCnpj($objMdPetVincRepresentantDTO->getStrCNPJ()); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <label class="infraLabelObrigatorio">Nome / Razão Social do
                            Outorgante: </label><?php echo $objMdPetVincRepresentantDTO->getStrRazaoSocialNomeVinc(); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <label class="infraLabelObrigatorio">CPF
                            Outorgado: </label><?php echo InfraUtil::formatarCpf($objMdPetVincRepresentantDTO->getStrCpfProcurador()); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <label class="infraLabelObrigatorio">Nome do
                            Outorgado: </label><?php echo $objMdPetVincRepresentantDTO->getStrNomeProcurador(); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <label class="infraLabelObrigatorio">Tipo de Procuração: </label> <?php echo $tpProcuracao; ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <label class="infraLabelObrigatorio">Validade: </label><?php echo (is_null($objMdPetVincRepresentantDTO->getDthDataLimiteValidade())) ? "Indeterminado" : $objMdPetVincRepresentantDTO->getDthDataLimiteValidade(); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <label class="infraLabelObrigatorio">Situação: </label><?php echo $situacao; ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <label class="infraLabelObrigatorio">Abrangência: </label><br />
                        <?php
                            if(count($arrProtocolosAbrangencia) > 0){
                                foreach ($arrProtocolosAbrangencia as $protocoloAbrangencia){
                                    echo "&nbsp;&nbsp;&nbsp;- " . $protocoloAbrangencia . "<br />";
                                }
                            } else {
                                echo  "&nbsp;&nbsp;&nbsp;- Qualquer Processo em Nome do Outorgante";
                            }

                        ?>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
    <br/>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <label id="lblJustificativa" for="txtJustificativa"
                   class="infraLabelObrigatorio"><?= $strMotivo ?> </label>
            <textarea name="txtJustificativa" id="txtJustificativa" maxlength=250 type="text"
                      class="infraText form-control"
                      rows="5" onkeypress="return infraMascaraTexto(this,event,250);"></textarea>
        </div>
    </div>
    <input type="hidden" id="hdnIdContatoVinc" name="hdnIdContatoVinc" value="<?= $id_contato_vin ?>">
    <input type="hidden" id="hdnCpfProcurador" name="hdnCpfProcurador" value="<?= $cpfProcurador ?>">
    <input type="hidden" id="hdnIdProcuracao" name="hdnIdProcuracao" value="<?= $idProcuracao ?>">
    <input type="hidden" id="hdnIdProcedimento" name="hdnIdProcedimento" value="<?= $id_procedimento ?>">
    <input type="hidden" id="hdnIdVinculacao" name="hdnIdVinculacao" value="<?= $id_vinculacao ?>">
    <input type="hidden" id="hdnTpDocumento" name="hdnTpDocumento" value="<?= $tpDocumento ?>">
    <input type="hidden" id="hdnTpVinculo" name="hdnTpVinculo" value="<?= $tipoVinculo ?>">
    <input type="hidden" id="hdnTpProcuracao" name="hdnTpProcuracao" value="<?= $tipoProcuracao ?>">
    <?
    PaginaSEIExterna::getInstance()->fecharAreaDados();
    ?>
</form>
<?
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
?>
