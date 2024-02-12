<?
$strDesabilitar = '';

$arrComandos = array();

$strUrlAjaxValidarNivelAcesso = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_tipo_processo_nivel_acesso_validar');

//Tipo Processo
$strLinkTipoProcessoSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=tipo_procedimento_selecionar&tipo_selecao=2&id_object=objLupaTipoProcesso');
$strLinkAjaxTipoProcesso = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_intercorrente_tipo_processo_auto_completar&listaLimpa=1');
if ($_GET['acao'] == 'md_pet_intercorrente_criterio_alterar') {
    $strLinkTipoProcessoSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=tipo_procedimento_selecionar&tipo_selecao=1&id_object=objLupaTipoProcesso');
}

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
$sinCriterioPadrao = '';
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
$strTipoProcesso = '';
$valorParametroHipoteseLegal = '';
$strItensSelNivelAcesso = '';
$strItensSelHipoteseLegal = '';
?>