<?php
$strDesabilitar = '';

$arrComandos = array();

//Tipo Processo - Nivel de Acesso
$strLinkAjaxNivelAcesso = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_tipo_processo_nivel_acesso_auto_completar');

//Tipo Documento Complementar
$strLinkTipoDocumentoSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_serie_selecionar&filtro=1&tipo_selecao=2&id_object=objLupaTipoDocumento&tipoDoc=E');
$strLinkAjaxTipoDocumento = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_serie_auto_completar');

//Tipo de Documento Essencial
$strLinkTipoDocumentoEssencialSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_serie_selecionar&filtro=1&tipo_selecao=2&id_object=objLupaTipoDocumentoEssencial&tipoDoc=E');

//Tipo Processo
$strLinkTipoProcessoSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=tipo_procedimento_selecionar&tipo_selecao=1&id_object=objLupaTipoProcesso');
$strLinkAjaxTipoProcesso = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_tipo_processo_auto_completar');

//Unidade
$strLinkUnidadeSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=unidade_selecionar_todas&tipo_selecao=1&id_object=objLupaUnidade');
$strLinkAjaxUnidade = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_unidade_auto_completar');

//Orgao
$strLinkOrgaoMultiplaSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=orgao_selecionar&tipo_selecao=1&id_object=objLupaOrgaoUnidadeMultipla');
$strLinkAjaxOrgao = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=orgao_auto_completar');
$strLinkAjaxConfirmaRestricao = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=confirma_restricao_tipo_processo');
$strLinkAjaxRetornaDadosUnidade = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=retorna_dados_unidade');
$strLinkAjaxConfirmaRestricaoSalvar = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=confirma_restricao_tipo_processo_salvar');

//Tipo Documento Principal
$strLinkTipoDocPrincExternoSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_serie_selecionar&filtro=1&tipoDoc=E&tipo_selecao=1&id_object=objLupaTipoDocPrinc');
$strLinkTipoDocPrincGeradoSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_serie_selecionar&filtro=1&tipoDoc=G&tipo_selecao=1&id_object=objLupaTipoDocPrinc');
$strLinkTipoDocPrincFormularioSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_formulario_selecionar&filtro=1&tipoDoc=F&tipo_selecao=1&id_object=objLupaTipoDocPrinc');
$strLinkAjaxTipoDocPrinc = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_serie_auto_completar');
$strLinkTipoDocPrincFormularioAutoCompletar = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_formulario_auto_completar');

//Preparar Preenchimento Alteração
$idMdPetTipoProcesso = '';
$nomeTipoProcesso = '';
$idTipoProcesso = '';
$orientacoes = '';
$idUnidade = '';
$nomeUnidade = '';
$sinIndIntUsExt = '';
$sinIndIntIndIndir = '';
$sinIndIntIndConta = '';
$sinIndIntIndCpfCn = '';
$sinNAUsuExt = '';
$sinNAPadrao = '';
$hipoteseLegal = 'style="display:none;"';
$gerado = '';
$externo = '';
$nomeSerie = '';
$idSerie = '';
$strItensSelSeries = '';
$unica = false;
$mutipla = false;
$arrObjUnidadesMultiplas = array();
$alterar = false;

$strItensSelNivelAcesso = '';
$strItensSelHipoteseLegal = '';
?>