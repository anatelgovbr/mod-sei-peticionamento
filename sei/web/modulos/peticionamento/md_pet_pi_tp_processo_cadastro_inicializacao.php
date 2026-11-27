<?php
/**
 * ANATEL
 *
 * Links assinados e variaveis de tela do cadastro do Tipo de Processo do Peticionamento Interno.
 *
 * 08/09/2026 - criado por emerson_b.goncalves - ANATEL
 */

$arrComandos = [];
$strDesabilitar = '';

// Tipo de Processo
$strLinkTipoProcessoSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=tipo_procedimento_selecionar&tipo_selecao=1&id_object=objLupaTipoProcesso');
$strLinkAjaxTipoProcesso = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_pi_tp_processo_auto_completar');
$strLinkAjaxAssuntoValidar = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_pi_tp_processo_assunto_validar');
$strLinkAjaxNivelAcesso = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_pi_nivel_acesso_auto_completar');

// Modelo por Protocolo
$strLinkAjaxModeloProtocolo = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_pi_modelo_protocolo_validar');

// Restricao por Orgaos e Unidades
$strLinkOrgaoSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=orgao_selecionar&tipo_selecao=2&id_object=objLupaOrgaos');
$strLinkAjaxOrgao = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=orgao_auto_completar');
$strLinkAjaxUnidade = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=unidade_auto_completar_todas');
$arrLinksUnidadePorOrgao = MdPetPiTpProcessoINT::montarLinksSelecaoUnidadePorOrgao();

// Preenchimento da tela
$numIdMdPetPiTpProcesso = null;
$numIdTipoProcesso = null;
$strNomeTipoProcesso = '';
$strOrientacoes = '';
$strOrientacoesPosPet = '';
$strStaIndicacaoInteressado = MdPetPiTpProcessoRN::$II_INTERESSE_UNIDADE;
$strStaAplicabilidadeDocPrinc = '';

$arrDocPrinc = [];
$arrAnexoObrig = [];
$arrDocAuto = [];
$arrRestricao = ['orgaos' => [], 'unidades' => []];

$strItensSelNivelAcesso = '';
$strItensSelHipoteseLegal = '';
$strItensSelSerieGerado = '';
$strItensSelSerieExterno = '';
$strItensSelFormulario = '';

$bolAlterar = false;
$bolConsultar = false;
