<?php
try {
/**
   * @author jose vieira  <jose.vieira@castgroup.com.br>
   * @since  15/03/2018
*/
     require_once dirname(__FILE__) . '/../../SEI.php';
     session_start();

    //////////////////////////////////////////////////////////////////////////////
    InfraDebug::getInstance()->setBolLigado(false);
    InfraDebug::getInstance()->setBolDebugInfra(false);
    InfraDebug::getInstance()->limpar();
    //////////////////////////////////////////////////////////////////////////////

    SessaoSEIExterna::getInstance()->validarLink();
    $possuiPermissao = SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);
    require_once 'md_pet_vinc_usu_ext_inicializar.php';
    require_once 'md_pet_vinc_usu_ext_acoes.php';

} catch (Exception $e) {
   PaginaSEIExterna::getInstance()->processarExcecao($e);
}

PaginaSEIExterna::getInstance()->montarDocType();
PaginaSEIExterna::getInstance()->abrirHtml();
PaginaSEIExterna::getInstance()->abrirHead();
PaginaSEIExterna::getInstance()->montarMeta();
PaginaSEIExterna::getInstance()->montarTitle(':: ' . PaginaSEIExterna::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaSEIExterna::getInstance()->montarStyle();
CaptchaSEI::getInstance()->montarStyle();
PaginaSEIExterna::getInstance()->abrirStyle();
require_once 'md_pet_vinc_usu_ext_css.php';
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
CaptchaSEI::getInstance()->montarJavascript();

PaginaSEIExterna::getInstance()->abrirJavaScript();
?>

function OnSubmitForm(){ 
    <? CaptchaSEI::getInstance()->validarOnSubmit('formCaptcha'); ?>
}

<?php
PaginaSEIExterna::getInstance()->fecharJavaScript();
require_once 'md_pet_vinc_usu_ext_js.php';
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');

PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaSEIExterna::getInstance()->abrirAreaDados('auto');

?>
<style type="text/css">

</style>
<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-12">
        <fieldset id="fldOrientacoes" class="infraFieldset form-control sizeFieldset" style="width:auto">
            <legend class="infraLegend" class="infraLabelObrigatorio"> &nbsp; Orientações &nbsp;</legend>
            <?=PaginaSEI::tratarHTML($txtConteudo)?>
            <?php echo $txtConteudo; ?>
            <?= '<iframe id=ifrConteudoHTML name=ifrConteudoHTML style="height:100%;width:100%" frameborder="0" marginheight="0" marginwidth="0" src="' . SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vincpf_usu_ext_bloco_orientacoes&iframe=S') . '"></iframe>'; ?>
        </fieldset>
    </div>
</div>


<?
//Verificar se é alteração ou cadastro
$blcPessoaJuridica =  'md_pet_vinc_usu_ext_bloco_pessoajuridica.php';
$blcInformacao =  'md_pet_vinc_usu_ext_bloco_informacao_pj.php';

if($stAlterar || $stConsultar) {
    $blcPessoaJuridica = 'md_pet_vinc_usu_ext_bloco_alterar_pessoajuridica.php';
    $blcInformacao =  'md_pet_vinc_usu_ext_bloco_alterar_informacao_pj.php';
}

require_once $blcPessoaJuridica;
require_once $blcInformacao;

if(!$stConsultar) {
    require_once 'md_pet_vinc_usu_ext_bloco_documento.php';
}

?>
<input type="hidden" name="hdnIdDocumento" id="hdnIdDocumento" value="0"/>
<input type="hidden" name="hdnIdVinculo" id="hdnIdVinculo" value="<?= $idMdPetVinculo?>"/>
<input type="hidden" name="hdnStaWebService" id="hdnStaWebService" value="<?= $stWebService ?>"/>
<input type="hidden" name="hdnIsWebServiceHabilitado" id="hdnIsWebServiceHabilitado" value=""/>
<input type="hidden" name="hdnVinculoPreExistente" id="hdnVinculoPreExistente" value='<?= $objVinculosExistentes ?>'>
<input type="hidden" name="hdnStrTipo" id="hdnStrTipo" value="<?= $strTipo ?>"/>
<input type="hidden" name="hdnCpfUsuarioExternoLogado" id="hdnCpfUsuarioExternoLogado" value="<?= $cpfUsuarioExterno ?>"/>
<input type="hidden" name="hdnIdUsuarioExternoLogado" id="hdnIdUsuarioExternoLogado" value="<?= $idUsuarioExterno ?>"/>

<?php
require_once 'js/BloqueioSsoSemSenha_js.php';
PaginaSEIExterna::getInstance()->fecharAreaDados();
PaginaSEIExterna::getInstance()->montarBarraComandosInferior($arrComandos);
//PaginaSEIExterna::getInstance()->montarAreaDebug();
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHead();
?>
<script type="text/javascript">

$('body').on('blur', '#txtNumeroCnpj', function(){
    consultarVinculoExistenteCnpj();
});

$('body').on('submit', '#frmCNPJ', function(){
    consultarVinculoExistenteCnpj();
});

function resizeIFramePorConteudo(){
	var id = 'ifrConteudoHTML';
	var ifrm = document.getElementById(id);
	ifrm.style.visibility = 'hidden';
	ifrm.style.height = "10px"; 

	var doc = ifrm.contentDocument? ifrm.contentDocument : ifrm.contentWindow.document;
	doc = doc || document;
	var body = doc.body, html = doc.documentElement;

	var width = Math.max( body.scrollWidth, body.offsetWidth, 
	                      html.clientWidth, html.scrollWidth, html.offsetWidth );
	ifrm.style.width='100%';

	var height = Math.max( body.scrollHeight, body.offsetHeight, 
	                       html.clientHeight, html.scrollHeight, html.offsetHeight );
	ifrm.style.height=height+'px';

	ifrm.style.visibility = 'visible';
}

document.getElementById('ifrConteudoHTML').onload = function() {
	resizeIFramePorConteudo();
}

</script>