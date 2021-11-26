<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
 *
 * 14/03/2017 - criado por pedro.cast
 *
 * Vers�o do Gerador de C�digo: 1.40.0
 */

try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();
    SessaoSEI::getInstance()->validarLink();
    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    //////////////////////////////////////////////////////////////////////////////
    //InfraDebug::getInstance()->setBolLigado(false);
    //InfraDebug::getInstance()->setBolDebugInfra(true);
    //InfraDebug::getInstance()->limpar();
    //////////////////////////////////////////////////////////////////////////////

    //Pessoa F�sica
    $strLinkTipoProcessoSelecaoF = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_pessoa_fisica&tipo_selecao=1&id_object=objLupaTipoProcesso');

    //Juridicos
    $strLinkTipoProcessoSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_pessoa_juridica&tipo_selecao=1&id_object=objLupaJuridico');
    $idDocumento = isset($_GET['id_documento']) ? $_GET['id_documento'] : $_POST['hdnIdDocumento'];

    $strLinkAjaxUsuariosJuridicos = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_int_usuario_auto_completar_juridica&id_documento=' . $idDocumento);
    $strLinkAjaxJuridicos = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=usuario_dados_tabela_juridica');
    $strParametros = '';
    if (isset($_GET['arvore'])) {
        PaginaSEI::getInstance()->setBolArvore($_GET['arvore']);
        $strParametros .= '&arvore=' . $_GET['arvore'];
    }

    //Inits
    $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
    $arrComandos = array();
    $idDocumento = isset($_GET['id_documento']) ? $_GET['id_documento'] : $_POST['hdnIdDocumento'];
    $strLinkAjaxUsuarios = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_int_usuario_auto_completar&id_documento=' . $idDocumento);
    $strLinkAjaxTransportaUsuarios = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=usuario_dados_tabela');
    $strLinkAjaxBuscaTiposRespostaTipoIntimacao = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=busca_tipo_resposta_intimacao');
    $strLinkAjaxValidacoesSubmit = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_int_validar_cadastro');
    $isAlterar = false;
    $countInt  = 0;
    $urlTipoFisica = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_intimacao_cadastro_fisica');
    $urlTipoJuridica = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_intimacao_cadastro_juridica');

    switch ($_GET['acao']) {
        case 'md_pet_intimacao_cadastrar':
    
            
            $strEmailAcoes = array('true', 'true');
            $strTitulo = 'Gerar Intima��o Eletr�nica';

            $arrComandos[] = '<button type="button" onclick="onSubmitForm();" accesskey="G" name="sbmCadastrarMdPetIntimacao" id="sbmCadastrarMdPetIntimacao" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">G</span>erar Intima��o</button>';
            
            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            $objDocumentoDTO = new DocumentoDTO();
            $objDocumentoDTO->retDblIdDocumento();
            $objDocumentoDTO->retDblIdProcedimento();
            $objDocumentoDTO->retNumIdOrgaoUnidadeResponsavel();
            $objDocumentoDTO->retStrProtocoloDocumentoFormatado();
            $objDocumentoDTO->retStrNomeSerie();
            $objDocumentoDTO->retStrNumero();
            $objDocumentoDTO->retNumIdSerie();
            $objDocumentoDTO->setDblIdDocumento($idDocumento);
            $objDocumentoRN = new DocumentoRN();
            $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);
            
            $strProtocoloDocumentoFormatado = !is_null($objDocumentoDTO) ? $objDocumentoDTO->getStrProtocoloDocumentoFormatado() : '';

//            Buscar Intima��es cadastradas.
            $arrIntimacoes = $objMdPetIntimacaoRN->buscaIntimacoesCadastradas($idDocumento);
            $isAlterar = (!empty($arrIntimacoes)) ? true : false;
            
            if (count($_POST) > 0) {
                
            	try {
                    $objMdPetIntimacaoDTO = $objMdPetIntimacaoRN->cadastrarIntimacao($_POST);
                    if ($objMdPetIntimacaoDTO) {
                        $idProcedimento = $objDocumentoDTO->getDblIdProcedimento();

                        //necess�rio para atualizara a arvore do processo e mostra caneta preta de imediato
                        header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=arvore_visualizar&acao_origem=' . $_GET['acao'] . '&id_procedimento=' . $idProcedimento. '&atualizar_arvore=1&id_documento=' . $objDocumentoDTO->getDblIdDocumento() ));
                        die;
                    }

                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
                
            }
            
            $objMdPetIntPrazoTacitaDTO = new MdPetIntPrazoTacitaDTO();
            $objMdPetIntPrazoTacitaDTO->setBolExclusaoLogica(false);
            $objMdPetIntPrazoTacitaDTO->retTodos();
            
            $objMdPetIntPrazoTacitaRN = new MdPetIntPrazoTacitaRN();
            $objMdPetIntPrazoTacitaDTO = $objMdPetIntPrazoTacitaRN->consultar($objMdPetIntPrazoTacitaDTO);
            if (count($objMdPetIntPrazoTacitaDTO)>0) {
            	$numNumPrazo = $objMdPetIntPrazoTacitaDTO->getNumNumPrazo();
            }
            
            break;

        default:
            throw new InfraException("A��o '" . $_GET['acao'] . "' n�o reconhecida.");
    }


} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo);
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();

require_once 'md_pet_intimacao_cadastro_css.php';

PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();

require_once 'md_pet_intimacao_cadastro_js.php';

PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
<style type="text/css">
#fldOrientacoesDestinatarios {height: auto; width: 96%; margin-bottom: 11px;}
#fldDestinatarios {height: auto; width: 96%; margin-bottom: 11px;}
.sizeFieldset {height:auto; width: 86%;}
.fieldsetClear {border:none !important;}
</style>

<form id="frmMdPetIntimacaoCadastro" 
          method="post" 
          action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">
        
        <? PaginaSEI::getInstance()->abrirAreaDados(); ?>
        <? PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos); ?><br>
        <fieldset id="fldOrientacoesDestinatarios" class="infraFieldset sizeFieldset" style="width:auto">
            <legend class="infraLegend" class="infraLabelObrigatorio"> Tipo de Destinat�rio</legend>
            <?=PaginaSEI::tratarHTML($txtConteudo)?>            
            <?php echo $txtConteudo; ?>
            <?= '<iframe id=ifrConteudoHTML name=ifrConteudoHTML style="height:100%;width:100%" frameborder="0" marginheight="0" marginwidth="0" src="' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_int_orientacoes_destinatario&iframe=S') . '"></iframe>'; ?> 
            <div id="divTipoPessoa" class="infraDivRadio">
                <input type="radio" id="tipoPessoaFisica" name="tipoPessoa" value="F" class="infraRadio" onclick="intimacaoTipoPessoa(this.value)" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                <label id="lblFisica" for="tipoPessoaFisica" accesskey="" class="infraLabelRadio">Pessoa F�sica</label><br>
                <input type="radio" id="tipoPessoaJuridica" name="tipoPessoa" value="J" class="infraRadio" onclick="intimacaoTipoPessoa(this.value)" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                <label id="lblJuridica" for="tipoPessoaJuridica" accesskey="" class="infraLabelRadio">Pessoa Jur�dica</label>
            </div>
        </fieldset>    
        
        <div id="div_tipo_destinatario"></div>
        <?php PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos); ?>
        <?php PaginaSEI::getInstance()->fecharAreaDados(); ?>

    </form>
<?php

PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>
<style>

    #divTipoPessoa{
        margin-left:20px;
        
    }

    #lblFisica {
        font-weight: bold;
    }

    #lblJuridica{
        font-weight: bold;
    }

</style>

<script type="text/javascript">


function OnSubmitForm() {
	return true;
}

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


function intimacaoTipoPessoa(tipo){


    try{

    var tbUsuarios = document.getElementById('hdnDadosUsuario');
    var tipoPessoa = null;
    if(tbUsuarios.value != ""){
         
        if(document.getElementById('hdnTipoPessoa').value == "J"){
        
            tipoPessoa = "tipoPessoaJuridica";

        }else{

            tipoPessoa = "tipoPessoaFisica";   
    }


    var r = confirm("Os dados preenchidos ser�o desconsiderados. Deseja Continuar?");
    if(r == false){
        
        document.getElementById(tipoPessoa).checked = true;
        return;
    }

    }
    }catch(err){
        
    }

    
    if(tipo == 'F'){
        url = '<?=$urlTipoFisica?>';
    }else{
        url = '<?=$urlTipoJuridica?>';
    }   
    $.ajax({
        async: true,
        type: "POST",
        url: url ,
        data: {
                id_documento: <?= $idDocumento?>,
                id_procedimento: <?= $_GET['id_procedimento'] ?>
                <?php if($isAlterar){?>
                    ,is_alterar: <?= $isAlterar ?>
                <?php }?>                
              },
        success: function (result) {
            $('#div_tipo_destinatario').html(result);
            if(tipo == 'F'){                
                preparaPessoaFisica();                
            }else{
                preparaPessoaJuridica(tipo);
            }  
        },
        error: function (msgError) {
        msgCommit = "Erro selecionar tipo de destinat�rio: " + msgError.responseText;
        console.log(msgCommit);
        },
        complete: function (result) {
            infraAvisoCancelar();
        }
    });
}
</script>