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
 
  //print_r( ContatoPeticionamentoINT::getContatoByCPFCNPJ('707.230.281-68') ); die();
  
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
//echo $objEditorRN->montarCssEditor(null);
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
</style>
<? 
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
?> 
<!--  tela terá multiplos forms por conta dos uploads, logo nao fará sentido ter um form geral -->
<!-- 
<form id="frmPeticionamentoCadastro" method="post" 
      onsubmit="return OnSubmitForm();"  
      action="<?=PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">
-->
<?
PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaSEIExterna::getInstance()->abrirAreaDados('auto');
?>
 <p>
   <label style="font-weight: bold;">Tipo de Processo:</label> 
   <label><?= $txtTipoProcessoEscolhido ?></label> 
 </p>
 <br/>
 
 <fieldset id="field1" class="infraFieldset sizeFieldset">
 <legend class="infraLegend">&nbsp; Orientações sobre o Tipo de Processo &nbsp;</legend>
   <label> 
   <?= $txtOrientacoes ?>
   </label>
 </fieldset>
  
 <fieldset id="field2" class="infraFieldset sizeFieldset">
 <legend class="infraLegend">&nbsp; Formulário de Peticionamento &nbsp;</legend>
   
   <!--  $objTipoProcDTO   
   [RN1]	O sistema deve recuperar o que foi informado no campo “Indicação de Interessado” da funcionalidade “Gerir Tipo de Processo para Peticionamento”:
     a.	Caso tenha sido marcada a opção “Próprio Usuário Externo”, não deve apresentar nenhum campo adicional, devendo vincular o novo peticionamento ao usuário externo logado no sistema.
     b.	Caso tenha sido marcada a opção “Indicação Direta > Informando CPF ou CNPJ” deve apresentar os campos “CPF/CNPJ” e “Nome/Razão Social”;
     c.	Caso tenha sido marcada a opção “Contatos já existentes com opção de incluir um novo” deve apresentar a lista de opções com o nome dos interessados.
   [RN2]	O campo CPF somente será apresentado caso seja selecionado a opção “Pessoa Física” do campo “Interessado”.
   [RN3]	O campo CNPJ somente será apresentado caso seja selecionado a opção “Pessoa Jurídica” do campo “Interessado”.   
   -->

   <br/>
   <label style="font-weight: bold;"> Especificação (resumo limitado a 50 caracteres): </label>
   <br/>
   <input type="text" name="txtEspecificacao" maxlength="50" id="txtEspecificacao" style="width:360px;" class="infraText" value="" /> <br/><br/>
   
   <? if( $arrUnidadeUFDTO != null && count( $arrUnidadeUFDTO ) > 1 ){ ?>
   
     <label style="font-weight: bold;"> UF em que o processo deve ser aberto: </label>
     <br/>
     
     <select id="selUFAberturaProcesso" name="selUFAberturaProcesso">
     
       <option value=""></option>
     
       <? foreach( $arrUnidadeUFDTO as $itemUnidadeDTO ){ ?>
         <option value="<?= $itemUnidadeDTO->getNumIdUnidade() ?>"><?= $itemUnidadeDTO->getStrSiglaUf() ?></option>
       <? } ?>
     
     </select> <br/><br/>
   
   <? } ?>	
   	
   <? if( $objTipoProcDTO->getStrSinIIProprioUsuarioExterno() == 'S') { ?>
   
   <!--  CASO 1 -->
   <div id="divOptPublico" class="infraDivRadio">
   
	   <span id="spnPublico0">
	        <label id="lblPublico" class="infraLabelObrigatorio">Interessado:</label>
	      </span>
	   
	   <span id="spnPublico">
	      <label id="lblPublico" class="infraLabelRadio">
	      <img src="/infra_css/imagens/ajuda.gif" title="Ajuda" alt="Ajuda" class="infraImg" onclick="exibirAjudaCaso1()"/> 
	      </label>
	    </span>
	   
	   <label> <?= SessaoSEIExterna::getInstance()->getStrNomeUsuarioExterno() ?> </label>
	   
   </div>
   
   <? } else if( $objTipoProcDTO->getStrSinIIIndicacaoDiretaCpfCnpj() == 'S') { ?>
   
   <!--  CASO 2 -->
   <div id="divOptPublico" class="infraDivRadio">
      
      <span id="spnPublico0">
        <label id="lblPublico" class="infraLabelObrigatorio">Interessado:</label>
      </span>
            
      <span id="spnPublico">
      <label id="lblPublico" class="infraLabelRadio">
      <img src="/infra_css/imagens/ajuda.gif" title="Ajuda" alt="Ajuda" class="infraImg" onclick="exibirAjudaCaso2()"/> 
      </label>
      </span>
      
      <input name="rdoTipoPessoa" id="optTipoPessoaFisica" onclick="selecionarPF()" name="tipoPessoa" value="pf" class="infraRadio" type="radio"/>
      <span id="spnPublico"><label id="lblPublico" for="optTipoPessoaFisica" class="infraLabelRadio">Pessoa física</label></span>
       
       <input name="rdoTipoPessoa" id="optTipoPessoaJuridica" onclick="selecionarPJ()" name="tipoPessoa" value="pj" class="infraRadio" type="radio"/>
       <span id="spnPublico"><label id="lblPublico2" for="optTipoPessoaJuridica" class="infraLabelRadio">Pessoa jurídica</label></span>
       
    </div>
   
   <br/><br/>
   
   <div style="width: 98%;" id="divSel0">
       
       <div id="divSel1" style=" float: left; display: none;"> 
              
         <label id="descTipoPessoa" style="font-weight: bold;"> </label> <br/> 
         
         <input type="text" id="txtCPF" class="infraText" name="txtCPF" width="280" onkeypress="return infraMascaraCpf(this, event)" 
                style="width:280px; display:none;"/>
         
         <input type="text" id="txtCNPJ" class="infraText" name="txtCNPJ" width="280" onkeypress="return infraMascaraCnpj(this, event)" 
                style="width:280px; display:none;"/> 
         
         <input type="text" readonly="readonly" id="btValidarCPFCNPJ" class="infraText" value=" Validar " 
                style="visibility: hidden; width: 45px; margin-left: 2px; " 
                onclick="abrirCadastroInteressado()" />      
                         
       </div> 
       
       <div id="divSel2" style=" float: left; margin-left: 10px; display: none;"> 
       
         <label id="descNomePessoa" style="font-weight: bold;"> </label> <br/>
         <input type="text" name="txtNomeRazaoSocial" id="txtNomeRazaoSocial" maxlength="250" width="280" style="width:280px; display: none;"> 
         
         <input type="text" readonly="readonly" id="btAdicionarInteressado" class="infraText" value=" Adicionar " 
                onclick="adicionarInteressadoValido()" style="width: 60px; margin-left: 2px; display: none;" />   
         
        </div>
               
       <div style="margin-left: 35px; width: auto;">
       
       <table id="tbInteressado" class="infraTable" width="95%" align="right" summary="Lista de Interessados" >
          
          <caption class="infraCaption">Lista de Interessados:</caption>       
          
           <tr>
               <th class="infraTh" id="tdDescTipoPessoa" > CPF/CNPJ </th>
               <th class="infraTh"  id="tdDescNomePessoa" > Nome/Razão social </th>
               <th align="center" class="infraTh" style="width:70px;"> Ações </th>               
           </tr>
           
           <tbody> 
           
           <!--  
           <tr class="infraTrClara">
               <td class="infraTdSetaOrdenacao"> CPF/CNPJ </td>
               <td class="infraTdSetaOrdenacao"> Razão social </td>
               
               <td align="center" class="infraTdSetaOrdenacao"> 
                 <img src="/infra_css/imagens/alterar.gif" title="Alterar" alt="Alterar" class="infraImg"/>
                 <img src="/infra_css/imagens/remover.gif" alt="Remover" onclick="deleteRow(this)" title="Remover" class="infraImg"/>
               </td>               
           </tr>
           -->
           
           </tbody>
           
       </table>
       
       </div>
       
   </div>
   
   <? } else if( $objTipoProcDTO->getStrSinIIIndicacaoDiretaContato() == 'S') { 
       
   	    $strLinkAjaxInteressado = SessaoSEIExterna::getInstance()->assinarLink('/sei/institucional/peticionamento/controlador_ajax_externo.php?acao_ajax_externo=contato_auto_completar_contexto_pesquisa&id_orgao_acesso_externo=0');
   	    //$strLinkAjaxInteressado = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=tipo_contexto_contato_listar');
   	    $strLinkInteressadosSelecao = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?id_tipo_processo_peticionamento=' . $objTipoProcDTO->getNumIdTipoProcessoPeticionamento() .'&acao=peticionamento_contato_selecionar&tipo_selecao=2&id_object=objLupaInteressados');
   	
   ?>
   
   <!--  CASO 3 -->
   <div id="divOptPublico" class="infraDivRadio">
   
	   <span id="spnPublico0">
	        <label id="lblPublico" class="infraLabelObrigatorio">Interessado:</label>
	      </span>
	   
	   <span id="spnPublico">
	      <label id="lblPublico" class="infraLabelRadio">
	      <img src="/infra_css/imagens/ajuda.gif" title="Ajuda" alt="Ajuda" class="infraImg" onclick="exibirAjudaCaso3()"/> 
	      </label>
	    </span>
	   
   </div>
   
   <div style="clear: both;"></div>
   
   <input type="text" name="txtInteressado" id="txtInteressado" maxlength="50" value="" style="width: 50%" class="infraText" autocomplete="off" /> <br/>
   
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