<?
/**
* ANATEL
*
* 23/06/2016 - criado por marcelo.bezerra@cast.com.br - CAST
* 
* ========================================================================================================
* Página principal do cadastro de peticionamento, ela invoca páginas auxiliares (via require) contendo:
* 
*  - variaveis e consultas de inicializacao da pagina
*  - switch case controlador de ações principais da página
*  - funções JavaScript
*  - área / bloco de documentos
* ===========================================================================================================
*/

try {
	
  require_once dirname(__FILE__).'/../../SEI.php';

  session_start();
  
  //////////////////////////////////////////////////////////////////////////////
  InfraDebug::getInstance()->setBolLigado( true );
  InfraDebug::getInstance()->setBolDebugInfra( true );
  InfraDebug::getInstance()->limpar();
  //////////////////////////////////////////////////////////////////////////////
   
  SessaoSEIExterna::getInstance()->validarLink();
  SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);
  SessaoSEIExterna::getInstance()->removerAtributo('docPrincipalConteudoHTML');
  
  //=====================================================
  //INICIO - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
  //=====================================================
  
  require_once('peticionamento_usuario_externo_cadastro_inicializacao.php');
  
  //=====================================================
  //FIM - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
  //=====================================================
  
  //inclusao de script com o controle das ações principais da tela
  require_once('peticionamento_usuario_externo_cadastro_acoes.php');

} catch(Exception $e){
    PaginaSEIExterna::getInstance()->processarExcecao($e);
}

$hashAnexo = "";
$idAnexo = "";

PaginaSEIExterna::getInstance()->montarDocType();
PaginaSEIExterna::getInstance()->abrirHtml();
PaginaSEIExterna::getInstance()->abrirHead();
PaginaSEIExterna::getInstance()->montarMeta();
PaginaSEIExterna::getInstance()->montarTitle(':: '.PaginaSEIExterna::getInstance()->getStrNomeSistema().' - '.$strTitulo.' ::');
PaginaSEIExterna::getInstance()->montarStyle();
PaginaSEIExterna::getInstance()->abrirStyle();
$objEditorRN = new EditorRN();
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
PaginaSEIExterna::getInstance()->fecharJavaScript();
?>
<style type="text/css">
#field1 {height: auto; width: 96%; margin-bottom: 11px;}
#field2 {height: auto; width: 96%; margin-bottom: 11px;}
#field3 {height: auto; width: 96%; margin-bottom: 11px;}
.sizeFieldset {height:auto; width: 86%;}
.fieldsetClear {border:none !important;}

#lblPublico img[name=ajuda] {height: 1.3em; width: 1.3em; margin-bottom: -4px;}

</style>
<? 
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
?> 
<!--  tela terá multiplos forms por conta dos uploads, logo nao fará sentido ter um form geral -->
<?
PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaSEIExterna::getInstance()->abrirAreaDados('auto');
?>
 <p>
   <label class="infraLabelObrigatorio">Tipo de Processo:</label> 
   <label><?= $txtTipoProcessoEscolhido ?></label> 
 </p>
 <br/>
 
 <fieldset id="field1" class="infraFieldset sizeFieldset">
 <legend class="infraLegend">&nbsp; Orientações sobre o Tipo de Processo &nbsp;</legend>
	<br />
	<label>
	<?= $txtOrientacoes ?>
	</label>
	<br /><br />
 </fieldset>
  
 <fieldset id="field2" class="infraFieldset sizeFieldset">
 <legend class="infraLegend">&nbsp; Formulário de Peticionamento &nbsp;</legend>
   <br/>
   <label class="infraLabelObrigatorio">Especificação (resumo limitado a 50 caracteres):</label>
   <br/>
   <input type="text" class="infraText" name="txtEspecificacao" id="txtEspecificacao" style="width: 360px;" maxlength="50" /> <br/><br/>
   
   <? if( $arrUnidadeUFDTO != null && count( $arrUnidadeUFDTO ) > 1 ){ ?>
   
     <label class="infraLabelObrigatorio">UF em que o processo deve ser aberto:</label>
     <br/>
     
     <select id="selUFAberturaProcesso" name="selUFAberturaProcesso">
     
       <option value=""></option>
     
       <? foreach( $arrUnidadeUFDTO as $itemUnidadeDTO ){ ?>
         <option value="<?= $itemUnidadeDTO->getNumIdUnidade() ?>">
         <? // seiv2 $itemUnidadeDTO->getStrSiglaUf() ?>
         <?php 
         //alteracoes seiv3
         $contatoAssociadoDTO = new ContatoDTO();
         $contatoAssociadoRN = new ContatoRN();
         $contatoAssociadoDTO->retStrSiglaUf();
         $contatoAssociadoDTO->retNumIdContato();
         $contatoAssociadoDTO->setNumIdContato( $itemUnidadeDTO->getNumIdContato() );
         
         $contatoAssociadoDTO = $contatoAssociadoRN->consultarRN0324( $contatoAssociadoDTO );
         echo $contatoAssociadoDTO->getStrSiglaUf();
         ?>
         </option>
       <? } ?>
     
     </select> <br/><br/>
   
   <? } ?>	
   	
   <? if( $objTipoProcDTO->getStrSinIIProprioUsuarioExterno() == 'S') { ?>
   
   <!--  CASO 1 -->
	<div id="divOptPublico" class="infraDivRadio">
		<span id="spnPublico0">
			<label id="lblPublico" class="infraLabelObrigatorio">Interessado: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipInteressadoProprioUsuarioExterno) ?> alt="Ajuda" class="infraImg"/></label>
		</span>
		<label> <?= SessaoSEIExterna::getInstance()->getStrNomeUsuarioExterno() ?> </label>
	</div>
   
   <? } else if( $objTipoProcDTO->getStrSinIIIndicacaoDiretaCpfCnpj() == 'S') { ?>
   
   <!--  CASO 2 -->
	<div id="divOptPublico" class="infraDivRadio">
		<span id="spnPublico0">
			<label id="lblPublico" class="infraLabelObrigatorio">Interessados: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipInteressadoInformandoCPFeCNPJ) ?> alt="Ajuda" class="infraImg"/></label>
		</span>
		<input name="rdoTipoPessoa" id="optTipoPessoaFisica" onclick="selecionarPF()" name="tipoPessoa" value="pf" class="infraRadio" type="radio"/>
		<span id="spnPublico"><label id="lblPublico" for="optTipoPessoaFisica" class="infraLabelRadio">Pessoa Física</label></span>
		<input name="rdoTipoPessoa" id="optTipoPessoaJuridica" onclick="selecionarPJ()" name="tipoPessoa" value="pj" class="infraRadio" type="radio"/>
		<span id="spnPublico"><label id="lblPublico2" for="optTipoPessoaJuridica" class="infraLabelRadio">Pessoa Jurídica</label></span>
	</div>
   
   <br/><br/>
   
   <div style="width: 98%;" id="divSel0">
       
       <div id="divSel1" style=" float: left; display: none;"> 
              
         <label id="descTipoPessoa" class="infraLabelObrigatorio"> </label> <br/> 
         
         <input type="text" id="txtCPF" class="infraText" name="txtCPF" onkeyup="return alterandoCPF(this, event)" style="width:120px; display:none;" maxlength="14"/>
         
         <input type="text" id="txtCNPJ" class="infraText" name="txtCNPJ" onkeyup="return alterandoCNPJ(this, event)" style="width:120px; display:none;"/ maxlength="18"/>
         
         <input type="button" id="btValidarCPFCNPJ" class="infraText" value="Validar" style="visibility: hidden; margin-left: 2px;" onclick="abrirCadastroInteressado()"/>
                         
       </div> 
       
       <div id="divSel2" style="float: left; margin-left: 15px; display: none;">
       
         <label id="descNomePessoa" class="infraLabelObrigatorio"> </label> <br/>
         <input type="text" name="txtNomeRazaoSocial" id="txtNomeRazaoSocial" readonly="readonly" maxlength="250" style="width: 300px; display: none;">
         
         <input type="button" id="btAdicionarInteressado" class="infraText" value="Adicionar" style="margin-left: 2px; display: none;" onclick="adicionarInteressadoValido()" />
         
        </div>
               
       <div style="width: auto;">
       
       <input type="hidden" name="hdnIdInteressadoCadastrado" id="hdnIdInteressadoCadastrado" value="" />
       <input type="hidden" name="hdnListaInteressadosIndicados" id="hdnListaInteressadosIndicados" value="" />
       <input type="hidden" name="hdnCustomizado" id="hdnCustomizado" value="" />
       <input type="hidden" name="hdnUsuarioCadastro" id="hdnUsuarioCadastro" value="" />
       <input type="hidden" name="hdnUsuarioLogado" id="hdnUsuarioLogado" value="<? echo SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno(); ?>" />
       <input type="hidden" name="hdnIdEdicao" id="hdnIdEdicao" value="" />
       
       <table id="tbInteressadosIndicados" class="infraTable" width="98%" align="left" summary="Lista de Interessados" >
          
          <caption class="infraCaption"> &nbsp; </caption>
          
           <tr>
               <th class="infraTh" style="display: none;" > ID Contato </th>
               <th class="infraTh" width="100" id="tdDescTipoPessoaSelecao" > Tipo </th>
               <th class="infraTh" width="120" id="tdDescTipoPessoa" > CPF/CNPJ </th>
               <th class="infraTh"  id="tdDescNomePessoa" > Nome/Razão Social </th>
               <th align="center" class="infraTh" style="width:50px;"> Ações </th>               
           </tr>
           
           <tbody> 
           
           </tbody>
           
       </table>
       
       </div>
       
   </div>
   
   <? } else if( $objTipoProcDTO->getStrSinIIIndicacaoDiretaContato() == 'S') { 
       
		$strLinkAjaxInteressado = SessaoSEIExterna::getInstance()->assinarLink('modulos/peticionamento/controlador_ajax_externo.php?acao_ajax_externo=contato_auto_completar_contexto_pesquisa&id_orgao_acesso_externo=0');

		$strLinkInteressadosSelecao = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?id_tipo_processo_peticionamento=' . $objTipoProcDTO->getNumIdTipoProcessoPeticionamento() .'&acao=peticionamento_contato_selecionar&tipo_selecao=2&id_object=objLupaInteressados');

   ?>
   
   <!--  CASO 3 -->
	<div id="divOptPublico" class="infraDivRadio">
		<span id="spnPublico0">
			<label id="lblPublico" class="infraLabelObrigatorio">Interessados: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip($strMsgTooltipInteressadoDigitadoNomeExistente) ?> alt="Ajuda" class="infraImg"/></label>
		</span>
	</div>
   
   <div style="clear: both;"></div>
   
   <input type="text" name="txtInteressado" id="txtInteressado" maxlength="250" value="" style="width: 50%" class="infraText" autocomplete="off" /> <br/>
   
   <div style="margin-top: 5px;">
	  
	  <select style="float: left; width: 75%; margin-right: 5px;" id="selInteressados" name="selInteressados" 
	          size="4" multiple="multiple" class="infraSelect">

	    <? 
            if( is_array( $arrContatosInteressados ) && count( $arrContatosInteressados ) > 0 ) { 
                 foreach( $arrContatosInteressados as $itemObj ) { 
            ?>
                <option value="<?= $itemObj->getNumIdContato() ?>">
                <?= $itemObj->getStrNome() ?>
                </option>
            <?    } 
               } ?>
	  
	  </select>
	
	  <img id="imgLupaTipoDocumento" onclick="carregarComponenteLupaInteressados('S');" src="/infra_css/imagens/lupa.gif" alt="Localizar Interessados" title="Localizar Interessados" class="infraImg">	
	  <img id="imgExcluirTipoDocumento" onclick="carregarComponenteLupaInteressados('R');" src="/infra_css/imagens/remover.gif" alt="Remover Interessados" title="Remover Interessados" class="infraImg">
	  <br/>
	  <img id="imgAssuntosAcima" onclick="objLupaInteressados.moverAcima();" src="<?=PaginaSEI::getInstance()->getDiretorioImagensGlobal()?>/seta_acima_select.gif" alt="Mover Acima Assunto Selecionado" title="Mover Acima Selecionado" class="infraImg" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />	
      <img id="imgAssuntosAbaixo" onclick="objLupaInteressados.moverAbaixo();" src="<?=PaginaSEI::getInstance()->getDiretorioImagensGlobal()?>/seta_abaixo_select.gif" alt="Mover Abaixo Assunto Selecionado" title="Mover Abaixo Selecionado" class="infraImg" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
  </div>
  
  <? } ?> 
  
  </fieldset>  
 
 <!-- =========================== -->
 <!--  INICIO FIELDSET DOCUMENTOS -->
 <!-- =========================== -->
 <? require_once('peticionamento_usuario_externo_cadastro_bloco_documentos.php'); ?>
 <!-- =========================== -->
 <!--  FIM FIELDSET DOCUMENTOS -->
 <!-- =========================== -->
 
 <input type="hidden" id="hdnInteressados" name="hdnInteressados" value="<?=$_POST['hdnInteressados']?>" />
 <input type="hidden" id="hdnIdInteressado" name="hdnIdInteressado" class="infraText" value="" />

 <input type="hidden" id="hdnArquivosPermitidos" name="hdnArquivosPermitidos" value='<?php echo isset($jsonExtPermitidas) && (!is_null($jsonExtPermitidas)) ? $jsonExtPermitidas : ''?>'/>
 <input type="hidden" id="hdnArquivosPermitidosEssencialComplementar" name="hdnArquivosPermitidosEssencialComplementar" value='<?php echo isset($jsonExtEssencialComplementarPermitidas) && (!is_null($jsonExtEssencialComplementarPermitidas)) ? $jsonExtEssencialComplementarPermitidas : ''?>'/>
 
 <input type="hidden" id="hdnAnexos" name="hdnAnexos" value="<?=$_POST['hdnAnexos']?>"/>
 <input type="hidden" id="hdnAnexosInicial" name="hdnAnexosInicial" value="<?=$_POST['hdnAnexosInicial']?>"/>
  
 <input type="hidden" id="hdnNomeArquivoDownload" name="hdnNomeArquivoDownload" value="" />
 <input type="hidden" id="hdnNomeArquivoDownloadReal" name="hdnNomeArquivoDownloadReal" value="" />

<?
PaginaSEIExterna::getInstance()->montarBarraComandosInferior($arrComandos);  
PaginaSEIExterna::getInstance()->fecharAreaDados();
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();

//inclusao de conteudos JavaScript adicionais
require_once('peticionamento_usuario_externo_cadastro_js.php');
?>