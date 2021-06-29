<?
/*
 * @author Gilvan Junior <gilvan.junior@hominus.com.br>
 * 
 * */

$strLinkAjaxPrincipal = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_tp_ctx_contato_listar');
$strLinkPrincipalSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=tipo_contato_selecionar&tipo_selecao=2&id_object=objLupaPrincipal');

$strLinkAjaxPrincipal2 = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_tp_ctx_contato_listar');
$strLinkPrincipalSelecao2 = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=tipo_contato_selecionar&tipo_selecao=2&id_object=objLupaPrincipal2');
?>