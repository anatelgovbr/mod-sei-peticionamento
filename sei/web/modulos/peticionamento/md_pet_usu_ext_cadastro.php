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
  InfraDebug::getInstance()->setBolLigado( false );
  InfraDebug::getInstance()->setBolDebugInfra( false );
  InfraDebug::getInstance()->limpar();
  //////////////////////////////////////////////////////////////////////////////
   
  SessaoSEIExterna::getInstance()->validarLink();
  SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);
  SessaoSEIExterna::getInstance()->removerAtributo('docPrincipalConteudoHTML');
  
  //=====================================================
  //INICIO - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
  //=====================================================
  
  require_once('md_pet_usu_ext_cadastro_inicializacao.php');
  
  //=====================================================
  //FIM - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
  //=====================================================
  
  //inclusao de script com o controle das ações principais da tela
  require_once('md_pet_usu_ext_cadastro_acoes.php');

} catch(Exception $e){
    PaginaSEIExterna::getInstance()->processarExcecao($e);
}

$hashAnexo = "";
$idAnexo = "";

$oragao = '';
$uf = '';
$cidadeHidden = '';

if(isset($_GET['id_orgao'])){
    $oragao = $_GET['id_orgao'];
}
if(isset($_GET['id_uf'])){
    $uf = $_GET['id_uf'];
}
if(isset($_GET['id_cidade'])){
    $cidadeHidden = $_GET['id_cidade'];

    $objCidadeDTO = new CidadeDTO();
    $objCidadeDTO->setNumIdCidade($cidadeHidden);
    $objCidadeDTO->retStrNome();
    $objCidadeRN = new CidadeRN();
    $objCidadeDTO = $objCidadeRN->consultarRN0409($objCidadeDTO);
    $cidadeHidden = $objCidadeDTO->getStrNome();
}
 

//combo disabled 
$disabled = '';
//Option vazio
 //Recuperando Oragao
 
 $selectOrgao        = MdPetTipoProcessoINT::montarSelectOrgaoTpProcesso($_GET['id_tipo_procedimento'],$oragao);
 if(($cidadeHidden != "" || $uf != "") || ($oragao != "" && count($selectOrgao[0]) > 1 && $cidadeHidden != "" )){
 $selectCidade        = MdPetTipoProcessoINT::montarSelectCidade($_GET['id_tipo_procedimento'],$oragao,$uf,$cidadeHidden);
 }

 if(count($selectOrgao[0]) < 2){
  $disabled = "disabled";
  $unicoOrgao =  $selectOrgao[0];
  $orgaoDuplo = false; 
} 

 if(($uf != "" && $orgaoDuplo == false) || ($oragao != "" && count($selectOrgao[0]) > 1 )){
 $selectUf        = MdPetTipoProcessoINT::montarSelectUf($_GET['id_tipo_procedimento'],$oragao,$uf,$cidadeHidden);
 }

 $hiddUF = "";
 //Caso o usuário tenha selecionado na uf e retorne somente uma, esconder combo
 if(count($selectUf[0]) < 2){
   
  $hiddUF = "display:none";

 }
 //Caso retorne somente uma cidade
 $cidadeHidde = "";
 if(count($selectCidade[0]) == 1 ){
 
  $cidadeHidde = "display:none";

 }else{
  $cidadeHidde = "";
 }

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
   <input type="text" class="infraText" name="txtEspecificacao" id="txtEspecificacao" style="width: 337px;" maxlength="50" /> <br/><br/>
  
   <?php if(count($selectOrgao[0]) < 2){
        $hiddenOrgao = "display:none;";
        $unicoOrgao =  $selectOrgao[0];
        $orgaoDuplo = false; 
    } 

    if(count($selectOrgao[0]) > 1){
      $cidadeHidde = "display:none;";
    }


    ?>

    
    <!-- Settar unidade no lugar da idCidade -->
    <!-- Validação para quando deve aparecer as 3 combos -->
   <? if( $arrUnidadeUFDTO != null && count( $arrUnidadeUFDTO ) > 1 ){ ?>
<!-- Orgão -->
<?php if($hiddenOrgao == "display:none;"){ ?>
      <?php $display = "float: left;";  ?>
<?php }else{
      $display = "float: left;padding-right:10px;";
} ?>


   <div style="<?php echo $display ?>">
		
				<label id="lblPublico" style="<?php echo $hiddenOrgao; ?>" class="infraLabelObrigatorio">Orgão: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip("Neste campo somente são listados os Órgãos em que é possível abrir Processo Novo para o Tipo de Processo selecionado. \n \n Selecione abaixo o Órgão no qual deseja que este Processo seja aberto. ") ?> alt="Ajuda" class="infraImg"/></label> <br/>
       <select onchange="pesquisarUF(this)"  style="width:140px;<?php echo $hiddenOrgao; ?>"  id="selOrgao" name="selOrgao" class="infraSelect" >
       <?php if($orgaoDuplo){ ?>
        <?php if(empty($oragao)){ ?>
          <option value=""></option>
        <?php } ?>
        <?php } ?>
        <?php if($orgaoDuplo == false && count($selectOrgao[0]) > 1 ){ ?>
          <option value=""></option>
        <?php } ?>
        <?= 
        $idOrgao = $selectOrgao[0];
        $orgao = $selectOrgao[1];
        for ($i=0; $i < count($idOrgao) ; $i++) { 
          echo '<option value="' . $idOrgao[$i] . '">' . $orgao[$i] . '</option>';
        }
        
        ?>
        </select> 
		</div>
  
   
    <!-- UF -->
    
    <div style="float: left;padding-right:10px;<?php echo $hiddUF; ?>" id="ufHidden">

				<label id="lblPublico" class="infraLabelObrigatorio">UF: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip("Neste campo somente são listadas as UFs em que é possível abrir Processo Novo para o Tipo de Processo selecionado. \n \n Selecione abaixo a UF na qual deseja que este Processo seja aberto. ") ?> alt="Ajuda" class="infraImg"/></label> <br/>
          <select onchange="pesquisarCidade(this)"  style="width:60px;"  id="selUF" name="selUF" class="infraSelect" >
          <?php if(count($selectUf[0]) > 1){ ?>
          <option value=""></option>
          <?php } ?>
          <?= 
          
          $idUf = $selectUf[0];
          $uf = $selectUf[1];
            for ($i=0; $i < count($idUf) ; $i++) { 
              echo '<option value="' . $idUf[$i] . '">' . $uf[$i] . '</option>';
            }
      
          ?>
          
          </select>

    </div>

    

    <!-- Cidade -->
    <div style="float: left;padding-right:10px;<?php echo $cidadeHidde; ?>" id="cidadeHidden">

				<label id="lblPublico" class="infraLabelObrigatorio">Cidade: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip("Neste campo somente são listadas as Cidades em que é possível abrir Processo Novo para o Tipo de Processo selecionado. \n \n Selecione abaixo a Cidade na qual deseja que este Processo seja aberto.") ?> alt="Ajuda" class="infraImg"/></label> <br/>
        <select onchange="pesquisarFinal(this)"  style="width:271px;"  id="selUFAberturaProcesso" name="selUFAberturaProcesso" class="infraSelect" >
        <?php if(count($selectCidade[0]) > 1){ ?>
          <option value=""></option>
        <?php } ?>
        <?= 
          $unidade = $selectCidade[0];
          $cidade = $selectCidade[1];

          for ($i=0; $i < count($cidade) ; $i++) { 
              echo '<option value="' . $unidade[$i] . '">' . $cidade[$i] . '</option>';
          }
        
        ?>
   
  </select>

    </div>

  
       <input type="hidden" name="hdnIdUfTelaAnterior" id="hdnIdUfTelaAnterior" value="<?php echo $_GET['id_uf'] ?>" />
       <input type="hidden" name="hdnIdCidadeTelaAnterior" id="hdnIdCidadeTelaAnterior" value="<?php echo $cidadeHidden; ?>" />
       <input type="hidden" name="hdnIdOrgaoTelaAnterior" id="hdnIdOrgaoTelaAnterior" value="<?php echo $_GET['id_orgao'] ?>" />
       <input type="hidden" name="hdnIdUfUnico" id="hdnIdUfUnico" value="" />


   <? } ?>
   <div style="clear: both;"> &nbsp; </div>
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
		<input name="rdoTipoPessoa" id="optTipoPessoaFisica" onclick="selecionarPF()" style="margin-right:0px;" name="tipoPessoa" value="pf" class="infraRadio" type="radio"/>
		<span id="spnPublico"><label id="lblPublico" for="optTipoPessoaFisica" style="padding-right:10px;"  class="infraLabelRadio">Pessoa Física</label></span>
		<input name="rdoTipoPessoa" id="optTipoPessoaJuridica" onclick="selecionarPJ()" style="margin-right:0px;" name="tipoPessoa" value="pj" class="infraRadio" type="radio"/>
		<span id="spnPublico"><label id="lblPublico2" for="optTipoPessoaJuridica" class="infraLabelRadio">Pessoa Jurídica</label></span>
	</div>
   
   
   
   <div style="width: 98%;" id="divSel0">
       
       <div id="divSel1" style=" float: left; display: none;"> 
              
         <label id="descTipoPessoa" class="infraLabelObrigatorio"> </label> <br/> 
         
         <input type="text" id="txtCPF" class="infraText" name="txtCPF" onkeyup="return alterandoCPF(this, event)" style="width:120px; display:none;" maxlength="14"/>
         
         <input type="text" id="txtCNPJ" class="infraText" name="txtCNPJ" onkeyup="return alterandoCNPJ(this, event)" style="width:120px; display:none;" maxlength="18"/>
         
         <input type="button" id="btValidarCPFCNPJ" class="infraText" value="Validar" style="visibility: hidden; margin-left: 2px;" onclick="abrirCadastroInteressado()"/>
                         
       </div> 
       
       <div id="divSel2" style="float: left; margin-left: 15px; display: none;">
       
         <label id="descNomePessoa" class="infraLabelObrigatorio"> </label> <br/>
         <input type="text" name="txtNomeRazaoSocial" id="txtNomeRazaoSocial" readonly="readonly" style="width: 300px; display: none;" disabled>
         
         <input type="button" id="btAdicionarInteressado" class="infraText" value="Adicionar" style="margin-left: 2px; display: none;" onclick="adicionarInteressadoValido()" />
         
        </div>
               
       <div style="width: auto;">

       <input type="hidden" name="txtNomeRazaoSocialTratadoHTML" id="txtNomeRazaoSocialTratadoHTML" value=""/>
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
               <th class="infraTh" width="100" id="tdDescTipoPessoaSelecao" > Natureza </th>
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
       
		$strLinkAjaxInteressado = SessaoSEIExterna::getInstance()->assinarLink('modulos/peticionamento/controlador_ajax_externo.php?acao_ajax_externo=md_pet_contato_auto_completar_contexto_pesquisa&id_orgao_acesso_externo=0');

		$strLinkInteressadosSelecao = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?id_tipo_processo_peticionamento=' . $objTipoProcDTO->getNumIdTipoProcessoPeticionamento() .'&acao=md_pet_contato_selecionar&tipo_selecao=2&id_object=objLupaInteressados');

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
 <? require_once('md_pet_usu_ext_cadastro_bloco_documentos.php'); ?>
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
 <input type="hidden" id="hdnIdOrgaoDisabled" name="hdnIdOrgaoDisabled" value="<?php echo $disabled ?>" />
 <input type="hidden" id="hdnIdOrgaoUnico" name="hdnIdOrgaoUnico" value="<?php echo $unicoOrgao[0] ?>" />
<?
PaginaSEIExterna::getInstance()->montarBarraComandosInferior($arrComandos);  
PaginaSEIExterna::getInstance()->montarAreaDebug();
PaginaSEIExterna::getInstance()->fecharAreaDados();
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();

//inclusao de conteudos JavaScript adicionais
require_once('md_pet_usu_ext_cadastro_js.php');
?>

 <input type="hidden" id="hdnIdOrgao" name="hdnIdOrgao" value="" />
 <input type="hidden" id="hdnIdUf" name="hdnIdUf" value="" />
 <input type="hidden" id="hdnIdCidade" name="hdnIdCidade" value="" />
 <input type="hidden" id="hdnTpProcesso" name="hdnTpProcesso" value="<?php echo $_GET['id_tipo_procedimento'] ?>" />
 <input type="hidden" id="id_tipo_procedimento" name="id_tipo_procedimento" value="<?php echo $_GET['id_tipo_procedimento'] ?>" />