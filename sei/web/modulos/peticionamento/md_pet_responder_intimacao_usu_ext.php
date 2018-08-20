<?php
try {
/**
   * @author André Luiz <andre.luiz@castgroup.com.br>
   * @since  09/03/2017
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
    require_once 'md_pet_responder_intimacao_usu_ext_inicializar.php';
    require_once 'md_pet_responder_intimacao_usu_ext_acoes.php';

} catch (Exception $e) {
   PaginaSEIExterna::getInstance()->processarExcecao($e);
}

PaginaSEIExterna::getInstance()->montarDocType();
PaginaSEIExterna::getInstance()->abrirHtml();
PaginaSEIExterna::getInstance()->abrirHead();
PaginaSEIExterna::getInstance()->montarMeta();
PaginaSEIExterna::getInstance()->montarTitle(':: ' . PaginaSEIExterna::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaSEIExterna::getInstance()->montarStyle();
PaginaSEIExterna::getInstance()->abrirStyle();
require_once 'md_pet_responder_intimacao_usu_ext_css.php';
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
require_once 'md_pet_responder_intimacao_usu_ext_js.php';
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"'); 
?>
<form id="frmResponderIntimacao"/>
<?php
    PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
    PaginaSEIExterna::getInstance()->abrirAreaDados('auto');
    require_once 'md_pet_responder_intimacao_usu_ext_bloco_orientacoes.php';
    require_once 'md_pet_responder_intimacao_usu_ext_bloco_intimacao.php';
    require_once 'md_pet_responder_intimacao_usu_ext_bloco_tipo_resposta.php';
    require_once 'md_pet_responder_intimacao_usu_ext_bloco_documento.php';
    PaginaSEIExterna::getInstance()->fecharAreaDados();
    PaginaSEIExterna::getInstance()->montarBarraComandosInferior($arrComandos); ?>
    <input type="hidden" id="hdnNomeTipoResposta" name="hdnNomeTipoResposta"/>
    <input type="hidden" name="hdnTbDocumento" value="" id="hdnTbDocumento"/>
    <input type="hidden" value="0" id="hdnIdDocumento"/>
    <input type="hidden" name="hdnIdProcedimento" id="hdnIdProcedimento" value="<?= $idProcedimento ?>"/>
    
    <input type="hidden" name="hdnIdProcedimento" id="hdnIdProcedimento" value="<?= $idProcedimento ?>"/>
    <input type="hidden" name="hdnIdMdPetIntimacao" id="hdnIdMdPetIntimacao" value="<?= $idMdPetIntimacao ?>"/>
    <input type="hidden" name="hdnIdMdPetIntAceite" id="hdnIdMdPetIntAceite" value="<?= $idMdPetIntAceite ?>"/>
    <input type="hidden" name="hdnIdTipoProcedimento" id="hdnIdTipoProcedimento" value="<?= $idTipoProcedimento ?>"/>
    </form>
<?php
PaginaSEIExterna::getInstance()->montarAreaDebug();
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHead();
?>