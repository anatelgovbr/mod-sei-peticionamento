<?
/**
* ANATEL
*
* 23/06/2016 - criado por marcelo.bezerra@cast.com.br - CAST
*
*/

try {
	
  require_once dirname(__FILE__).'/../../SEI.php';
  
  session_start();
  
  //////////////////////////////////////////////////////////////////////////////
  InfraDebug::getInstance()->setBolLigado(false);
  InfraDebug::getInstance()->setBolDebugInfra(false);
  InfraDebug::getInstance()->limpar();
  //////////////////////////////////////////////////////////////////////////////

  PaginaSEIExterna::getInstance()->setTipoPagina( InfraPagina::$TIPO_PAGINA_SIMPLES );  

  switch($_GET['acao']){
    
  	case 'peticionamento_interessado_cadastro':
  		
  		if( !isset( $_GET['edicao']) && !isset( $_POST['hdnIdEdicaoAuxiliar'])  ){
  		   $strTitulo = 'Cadastro de Interessado';
  		} else {
  			$strTitulo = 'Alterar Interessado';
  		}
  		
  		$janelaSelecaoPorNome = SessaoSEIExterna::getInstance()->getAtributo('janelaSelecaoPorNome');
  		if( $janelaSelecaoPorNome != null && $janelaSelecaoPorNome != "" ) { 
  			
  		} else if( isset( $_GET['cpf']) ){
  			$strTitulo .= ' - Pessoa Física';
  		} else if( isset( $_GET['cnpj']) ){
  			$strTitulo .= ' - Pessoa Jurídica';
  		}
  		
  		$strPrimeiroItemValor = 'null';
  		$strPrimeiroItemDescricao = '&nbsp;';
  		$strValorItemSelecionado = null;
  		$strTipo = 'Cadastro';
  		
      $strItensSelSiglaEstado = UfINT::montarSelectSiglaRI0416('null','&nbsp;',null);
      $strItensSelCidade = CidadeINT::montarSelectNomeNome('null','&nbsp;','null', null);
		  $strItensSelTipoInteressado = GerirTipoContextoPeticionamentoINT::montarSelectTipoInteressado($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $strTipo);
		  $strLinkAjaxCargo = SessaoSEIExterna::getInstance()->assinarLink('modulos/peticionamento/controlador_ajax_externo.php?acao_ajax_externo=cargo_montar_select_genero');
		  $strLinkAjaxDadosCargo = SessaoSEIExterna::getInstance()->assinarLink('modulos/peticionamento/controlador_ajax_externo.php?acao_ajax_externo=cargo_dados');

		  //setando dados no contato que esta sendo cadastrado ou editado
		  if( isset( $_POST['hdnCadastrar'] ) ){
			  
        //TODO: Avaliar se é realmente necessário retornar todas as informações de contato
        $objContatoDTO = new ContatoDTO();
			  $objContatoDTO->retTodos();

			  $numIdTipoContextoContato = $_POST['tipoInteressado'];

			  if( !isset( $_POST['hdnIdEdicao'] ) || $_POST['hdnIdEdicao'] == ""  ){
				  $objContatoDTO->setNumIdContato(null);
				  $objContatoDTO->retNumIdTipoContato();
			  } else {
				  $objContatoRN = new ContatoRN();

  				$objContatoDTO = new ContatoDTO();
  				$objContatoDTO->retNumIdTipoContato();
				  $objContatoDTO->retStrMatricula();
	  			$objContatoDTO->retDblRg();
	  			$objContatoDTO->retStrOrgaoExpedidor();
	  			$objContatoDTO->retStrTelefoneFixo();
	  			$objContatoDTO->retStrEmail();
	  			$objContatoDTO->retStrSitioInternet();
	  			$objContatoDTO->retStrEndereco();
	  			$objContatoDTO->retStrBairro();
	  			$objContatoDTO->retStrSiglaUf();
	  			$objContatoDTO->retStrNomeCidade();
	  			$objContatoDTO->retStrNomePais();
	  			$objContatoDTO->retStrCep();
	  			$objContatoDTO->retStrObservacao();
	  			$objContatoDTO->retNumIdContato();  			
  				$objContatoDTO->setNumIdContato( $_POST['hdnIdEdicao'] );
  				$objContatoDTO = $objContatoRN->consultarRN0324($objContatoDTO);  			    
  			}
  					
			  $objContatoDTO->setNumIdCargo($_POST['cargo']);  			

  			if(isset($_POST['txtNome']) && $_POST['txtNome'] != ""){  			  
          $objContatoDTO->setStrNome($_POST['txtNome']);
          $objContatoDTO->setStrStaNatureza( ContatoRN::$TN_PESSOA_FISICA );
  			}
  			
  			else if( isset($_POST['txtRazaoSocial']) && $_POST['txtRazaoSocial'] != "" ){  				
          $objContatoDTO->setStrNome($_POST['txtRazaoSocial']);
          $objContatoDTO->setStrStaNatureza( ContatoRN::$TN_PESSOA_JURIDICA );
  			}
  			
  			$objContatoDTO->setDtaNascimento('');
  			$objContatoDTO->setStrSigla('');
        $objContatoDTO->setStrStaGenero($_POST['rdoStaGenero']);
        $objContatoDTO->setStrMatriculaOab($_POST['numeroOab']);
			
  			//campos manipulados apenas no cadastro (nao na ediçao)
  			if(!isset( $_POST['hdnIdEdicao'] ) || $_POST['hdnIdEdicao'] == "") {  			  
  			  $objContatoDTO->setDblCpf($_POST['txtCPF']);
  			  $objContatoDTO->setDblCnpj($_POST['txtCNPJ']);
  			  $objContatoDTO->setStrSinAtivo('S');
			  
			    if(isset ( $_POST['hdnIdContextoContato'] ) && $_POST['hdnIdContextoContato'] != "") {
			   	  $objContatoDTO->setNumIdContato( $_POST['hdnIdContextoContato']);
			    }

			    //PF sem vinculo com PJ
			    if($_POST['tipoPessoaPF'] == '0'){  			  
  			  	
            $strSinContexto = 'S';
  			  	unset($_POST['hdnIdContextoContato']);
            $objContatoDTO->setNumIdTipoContato($numIdTipoContextoContato);
  			  		
  			  	//PF com vinculo com PJ
  			  } else if($_POST['tipoPessoaPF'] == '1'){
  			  
  			  	$strSinContexto = 'N';
            $objContatoDTO->setNumIdTipoContato($numIdTipoContextoContato);
			    }  			  
  			  //PJ
  			  else {  			  
  			  	$strSinContexto = 'S';
  			  	unset( $_POST['hdnIdContextoContato'] );
            $objContatoDTO->setNumIdTipoContato($numIdTipoContextoContato);
  			  }
  			}
  			
        $objContatoDTO->setStrMatricula('');
  			$objContatoDTO->setDblRg($_POST['rg']);
  			$objContatoDTO->setStrOrgaoExpedidor($_POST['orgaoExpedidor']);
        $objContatoDTO->setStrTelefoneFixo($_POST['telefone']);
        $objContatoDTO->setStrTelefoneCelular(null);  			
        $objContatoDTO->setStrComplemento(null);                        
  			$objContatoDTO->setStrEmail($_POST['email']);
  			$objContatoDTO->setStrSitioInternet($_POST['sitioInternet']);
  			$objContatoDTO->setStrEndereco($_POST['endereco']);
  			$objContatoDTO->setStrBairro($_POST['bairro']); 
  			$objContatoDTO->setStrNomeCidade($_POST['selCidade']);
  			$objContatoDTO->setStrNomePais( $_POST['pais']);
  			$objContatoDTO->setStrCep($_POST['cep']);
  			$objContatoDTO->setStrObservacao('');
                        
        $paisDTO = new PaisDTO();
        $paisRN = new PaisRN();
        $paisDTO->retTodos();
        $paisDTO->setStrNome( $_POST['pais'] );                        
        $paisDTO = $paisRN->consultar( $paisDTO );
                                                
        $objContatoDTO->setNumIdPais(  $paisDTO->getNumIdPais() );
        $objContatoDTO->setNumIdUf( $_POST['selEstado'] );
        $objContatoDTO->setNumIdCidade( $_POST['selCidade'] );
        $objContatoDTO->setStrSinEnderecoAssociado('N');
                         			
  			//necessario para preencher o campo id_usuario_cadastro ao salvar o contato
  			SessaoSEI::getInstance()->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());  			
  			
        $objContatoRN = new ContatoRN();
  			
			  //verificando se é cadastro ou ediçao de contato
			  if(!isset( $_POST['hdnIdEdicao'] ) || $_POST['hdnIdEdicao'] == ""){
  			  $objContatoDTO->setNumIdContatoAssociado($_POST['hdnIdContextoContato']);
  			  $objContatoDTO->setStrStaNaturezaContatoAssociado( null );
  			  $objContatoDTO = $objContatoRN->cadastrarRN0322($objContatoDTO);
  			  $idContatoCadastro = $objContatoDTO->getNumIdContato();

			  } else if( $_POST['hdnIdEdicao'] != "" ) {				
			    $idContatoCadastro = $objContatoDTO->getNumIdContato();
  			  $objContatoRN->alterarRN0323($objContatoDTO);  			  
  			}
  			
  			//nome / razao social
  			if( isset($_POST['txtNome']) && $_POST['txtNome'] != "" ){
  				$nome = $_POST['txtNome'];
  			} else if( isset($_POST['txtRazaoSocial']) && $_POST['txtRazaoSocial'] != "" ){
  				$nome = $_POST['txtRazaoSocial'];
  			}
  			
  			//cpf/cnpj
  			if( isset($_POST['txtCPF']) && $_POST['txtCPF'] != "" ){
  				$cpfCnpjEditado = $_POST['txtCPF'];
  			} else if( isset($_POST['txtCNPJ']) && $_POST['txtCNPJ'] != "" ){
  				$cpfCnpjEditado = $_POST['txtCNPJ'];
  			}
  			  			
  			//após cadastrar o contato fechar janela modal e preencher campos necessarios  			
  			if(!isset($_POST['hdnIdEdicao'] ) || $_POST['hdnIdEdicao'] == ""){  				
  				$janelaSelecaoPorNome = SessaoSEIExterna::getInstance()->getAtributo('janelaSelecaoPorNome');
  				
  				echo "<script>";          
  				if( $janelaSelecaoPorNome == null || $janelaSelecaoPorNome == "" ){
  				  echo "window.opener.document.getElementById('txtNomeRazaoSocial').value = '" . str_replace("/", "\/", $nome) . "'; ";
				    echo "window.opener.document.getElementById('txtNomeRazaoSocialTratadoHTML').value = '" .PaginaSEIExterna::tratarHTML($nome) . "'; ";
  				  echo "window.opener.document.getElementById('hdnCustomizado').value = 'true'; ";
  				  echo "window.opener.document.getElementById('hdnIdInteressadoCadastrado').value = " . $objContatoDTO->getNumIdContato() . "; ";
  				} else {
  					SessaoSEIExterna::getInstance()->removerAtributo('janelaSelecaoPorNome');
  				}
  				
  				echo "window.close();";
  				echo "</script>";
  				die;
  				
  			} else {
  				
  				echo "<script>";
  				echo "window.opener.atualizarNomeRazaoSocial('". $cpfCnpjEditado ."', '". PaginaSEIExterna::tratarHTML($nome) ."');";
  		    echo "window.close();";
  		    echo "</script>";
				die;
			}

		} 

		//obtendo dados do contato que estiver sendo editado
		else if( isset( $_POST['hdnIdEdicaoAuxiliar'] )  ){
			$objContatoRN = new ContatoRN();			
			$objContatoDTO = new ContatoDTO();
			$objContatoDTO->retTodos(true);
			$objContatoDTO->setNumIdContato( $_POST['hdnIdEdicaoAuxiliar'] );
			$objContatoDTO = $objContatoRN->consultarRN0324($objContatoDTO);
			$strItensSelSiglaEstado = UfINT::montarSelectSiglaRI0416('null','&nbsp;', $objContatoDTO->getNumIdUf());
 			$strItensSelCidade = CidadeINT::montarSelectIdCidadeNome('null','&nbsp;', $objContatoDTO->getNumIdCidade() , $objContatoDTO->getNumIdUf());
 			
 			if( isset( $_GET['cpf'] )) {
 			  $_POST['txtNome'] = $objContatoDTO->getStrNome();
 			}
  			
 			if( isset( $_GET['cnpj'] )) {
 			  $_POST['txtRazaoSocial'] = $objContatoDTO->getStrNome();
 			}
  			
 			$_POST['numeroOab'] = $objContatoDTO->getStrMatriculaOab();
 			$_POST['txtCPF'] = $objContatoDTO->getDblCpf();
 			$_POST['txtCNPJ'] = $objContatoDTO->getDblCnpj();
 			$_POST['rg'] = $objContatoDTO->getDblRg();
 			$_POST['orgaoExpedidor'] = $objContatoDTO->getStrOrgaoExpedidor();
 			$_POST['telefone'] = $objContatoDTO->getStrTelefoneFixo();  			
 			$_POST['email'] = $objContatoDTO->getStrEmail();
 			$_POST['sitioInternet'] = $objContatoDTO->getStrSitioInternet();
 			$_POST['endereco'] = $objContatoDTO->getStrEndereco();
 			$_POST['bairro'] = $objContatoDTO->getStrBairro();  			
 			$_POST['estado'] = $objContatoDTO->getStrSiglaUfContatoAssociado();  		
 			$_POST['cidade'] = $objContatoDTO->getStrNomeCidade();
 			$_POST['pais'] = $objContatoDTO->getStrNomePais();
 			$_POST['cep'] = $objContatoDTO->getStrCep();
 			$_POST['tratamento'] = $objContatoDTO->getNumIdTratamentoCargo();
 			$_POST['vocativo'] = $objContatoDTO->getNumIdVocativoCargo();  			
 			$_POST['cargo'] = $objContatoDTO->getNumIdCargo();
 			$_POST['hdnIdEdicao'] = $_POST['hdnIdEdicaoAuxiliar'];
 			$_POST['hdnIdContextoContato'] = $objContatoDTO->getNumIdContato();
  			
			$objContatoPJVinculadaDTO = new ContatoDTO();
			$objContatoPJVinculadaDTO->retNumIdContato();
			$objContatoPJVinculadaDTO->retStrNome();
			$objContatoPJVinculadaDTO->retNumIdTipoContato(); 				
			$objContatoPJVinculadaDTO->setNumIdContato( $_POST['hdnIdContextoContato']  );  			
			$objContatoPJVinculadaDTO = $objContatoRN->consultarRN0324( $objContatoPJVinculadaDTO );
			$_POST['tipoInteressado'] = $objContatoDTO->getNumIdTipoContato();
  				
			if( $objContatoDTO->getStrStaNaturezaContatoAssociado() == ContatoRN::$TN_PESSOA_JURIDICA ){
			  $_POST['txtPjVinculada'] = $objContatoDTO->getStrNomeContatoAssociado();
			} else {
			  $_POST['txtPjVinculada'] = "";
			}
  				
			$numIdTipoContextoContato = $_POST['tipoInteressado']; 
			$strItensSelTipoInteressado = GerirTipoContextoPeticionamentoINT::montarSelectTipoInteressado($strPrimeiroItemValor, $strPrimeiroItemDescricao, $numIdTipoContextoContato, $strTipo);
  			
			if( isset( $_GET['cpf'] )) {
			  $strItensSelTratamento = TratamentoINT::montarSelectExpressaoRI0467('null','&nbsp;', $_POST['tratamento'] ) ;
			  $strItensSelVocativo = VocativoINT::montarSelectExpressaoRI0469('null','&nbsp;', $_POST['vocativo'] );
			}
		}

		break;
		
    default:
      throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
  }

}catch(Exception $e){
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
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
PaginaSEIExterna::getInstance()->fecharJavaScript();
?>
<style type="text/css">
#field1 {height: auto; width: 97%; margin-bottom: 11px;}
#field2 {height: auto; width: 97%; margin-bottom: 11px;}
.sizeFieldset {height:auto; width: 88%;}
.fieldsetClear {border:none !important;}
</style>
<?php 
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
$urlBaseLink = "";
$arrComandos = array();
$arrComandos[] = '<button type="button" accesskey="s" name="Salvar" value="Salvar" onclick="salvar()" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
$arrComandos[] = '<button type="button" accesskey="c" name="btnFechar" value="Fechar" onclick="window.close();" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

$strLinkBaseFormEdicao = 'controlador_externo.php?edicaoExibir=true&acao='.$_GET['acao'];

if( isset( $_GET['cpf'] )) {
	$strLinkBaseFormEdicao .= '&cpf=true';
} else if( isset( $_GET['cnpj'] )) {
	$strLinkBaseFormEdicao .= '&cnpj=true';
}

$strLinkEdicaHash = PaginaSEIExterna::getInstance()->formatarXHTML(
		SessaoSEIExterna::getInstance()->assinarLink( $strLinkBaseFormEdicao ));

?> 

<!-- Formulario usado para viabilizar fluxo de edição de contato -->
<?php if( isset( $_GET['edicao'] )) { ?>

	<form id="frmEdicaoAuxiliar" 
	      name="frmEdicaoAuxiliar" 
	      method="post" 
	      action="<?= $strLinkEdicaHash ?>">
		
		<input type="hidden" name="hdnIdEdicaoAuxiliar" id="hdnIdEdicaoAuxiliar" value="" /> 
	
	</form>

<?php } else { ?>

	<form id="frmCadastro" name="frmCadastro" 
	      method="post" onsubmit="return OnSubmitForm();"  
	      action="<?=PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">
	<?php
	PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
	PaginaSEIExterna::getInstance()->abrirAreaDados('auto');
	?>
	
	 <fieldset id="field1" class="infraFieldset sizeFieldset">

		<legend class="infraLegend">&nbsp; Natureza &nbsp;</legend>       
		
		<?php if( isset( $_GET['cpf'] )) { ?>
		
		<input type="radio" name="tipoPessoa" value="pf" id="rdPF" 
		tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"
		onclick="selecionarPF()" />
		<label for="rdPF" class="infraLabelRadio">Pessoa Física</label> <br/>
		
		    <input type="radio" name="tipoPessoaPF" value="0" id="rdPF1" 
		      style="display: none; margin-left: 20px;" 
		      tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"
		      onclick="selecionarPF1()" />
		    <label for="rdPF1" id="lblrdPF1" class="infraLabelRadio" style="display: none;">Sem vínculo com Pessoa Jurídica<br/></label>
		
		    <input type="radio" name="tipoPessoaPF" value="1" id="rdPF2" 
		      style="display: none; margin-left: 20px;" onclick="selecionarPF2()"
          tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>" />
		    <label for="rdPF2" id="lblrdPF2" class="infraLabelRadio" style="display: none;">Com vínculo com Pessoa Jurídica<br/></label>
		
		<?php } ?>
		
		<?php if( isset( $_GET['cnpj'] )) { ?>
		  <input type="radio" name="tipoPessoa" value="pj" id="rdPJ" 
		  tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"
		  onclick="selecionarPJ()" />
		  <label for="rdPJ" class="infraLabelRadio">Pessoa Jurídica</label>
		<?php } ?>
				
	 </fieldset>
	  
	 <fieldset id="field2" class="infraFieldset sizeFieldset">
	    
	    <legend class="infraLegend">&nbsp; Formulário de Cadastro &nbsp;</legend>
	    
	    <br/>
		<label class="infraLabelObrigatorio">Tipo de Interessado:</label><br/>
	    <select class="infraSelect" width="380" id="tipoInteressado" 
	         tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"
	         name="tipoInteressado" 
	         onchange="selecionarTipoInteressado()" style="width:380px;" >
	        <?=$strItensSelTipoInteressado?>
	    </select> <br/>
	    
	    <label id="lblNome" class="infraLabelObrigatorio" style="display:none;">Nome Completo:<br/>
	    <input type="text" id="txtNome" name="txtNome" 
	          class="infraText" style="width: 580px;" 
	           value="<?=PaginaSEIExterna::tratarHTML($_POST['txtNome']) ?>" 
	       onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250" 
	       tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>" />
	    <br/><br/>
	    </label>
	    
	    <label id="lblRazaoSocial" class="infraLabelObrigatorio" style="display:none;">Razão Social:<br/>
	    <input type="text" id="txtRazaoSocial" name="txtRazaoSocial" 
	          class="infraText" style="width: 580px;" 
	       value="<?=PaginaSEIExterna::tratarHTML($_POST['txtRazaoSocial']) ?>"
	       onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250" 
	       tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>" />
	    <br/><br/>
	    </label>
	    
	    
	    <?php if( $_POST['hdnIdContextoContato'] == '') {?>
	    
	    <label id="lblPjVinculada" style="display: none;" class="infraLabelObrigatorio">Razão Social da Pessoa Jurídica vinculada:<br/>
	    
	    <input type="text" class="infraText" 
	      tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"
	      onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250"
	      name="txtPjVinculada" id="txtPjVinculada" 
	      autocomplete="off" style="width: 580px; display: none;" />
	      
	      <input type="hidden" name="hdnIdContextoContato" id="hdnIdContextoContato" 
	           value="<?php echo $_POST['hdnIdContextoContato'];  ?>" />
	           
	    <br/><br/>
	    </label>
	    
	    <?php } else if( $_POST['txtPjVinculada'] != "" ) { ?>
	    
	    <label id="lblPjVinculada" style="display: none;" class="infraLabelObrigatorio">Razão Social da Pessoa Jurídica vinculada:<br/>
	    
	    <input type="text" class="infraText" 
	      value="<?=PaginaSEIExterna::tratarHTML($_POST['txtPjVinculada']) ?>"
	      tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"
	      onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250"
	      name="txtPjVinculada" id="txtPjVinculada" 
	      autocomplete="off" style="width: 580px;" />
	      
	      <input type="hidden" name="hdnIdContextoContato" id="hdnIdContextoContato" 
	           value="<?=$_POST['hdnIdContextoContato'] ?>" />
	           
	    <br/><br/>
	    </label>
	      	    
	    <?php } ?>
	           
	    <label id="lblCPF" style="display: none;" class="infraLabelObrigatorio">CPF:<br/>
	    <input type="text" class="infraText" name="txtCPF" id="txtCPF" 
	      value="<?=PaginaSEIExterna::tratarHTML($_POST['txtCPF']) ?>"
	      readonly="readonly"
	      onkeypress="return infraMascaraCpf(this, event)"
	      style="width: 280px;" />
	    <br/><br/>
	    </label>
	    
	    <label id="lblCNPJ" style="display: none;" class="infraLabelObrigatorio">CNPJ:<br/>
	    <input type="text" class="infraText" name="txtCNPJ" id="txtCNPJ"
	      value="<?=PaginaSEIExterna::tratarHTML($_POST['txtCNPJ']) ?>" 
	      readonly="readonly" onkeypress="return infraMascaraCnpj(this, event)"
	      style="width: 280px;" />
	    <br/><br/>
	    </label>
	    
	    <div id="div1" style="float:left; width: auto; display: none;">
	        
	        <div id="div1_2" style="float:left; width: auto;">
	        <label class="infraLabelObrigatorio">RG:</label><br/>
	        <input type="text" class="infraText" 
	          value="<?=PaginaSEIExterna::tratarHTML($_POST['rg']) ?>"
	          tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"
	          onkeypress="return infraMascaraNumero(this,event, 15);" 
	          name="rg" id="rg" />
	        </div>
	        
	        <div id="div1_3" style="float:left; margin-left:20px; width: auto;">
	        <label class="infraLabelObrigatorio">Órgão Expedidor do RG:</label><br/>
	        <input type="text" class="infraText" name="orgaoExpedidor"
	           tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"
	           value="<?=PaginaSEIExterna::tratarHTML($_POST['orgaoExpedidor']) ?>"
	           onkeypress="return infraMascaraTexto(this,event, 50);"
	           id="orgaoExpedidor" />
	        </div>
	        
	        <div id="div1_1" style="float:left; margin-left:20px; width: auto;">
	        <label class="infraLabel">Número da OAB:</label><br/>
	        <input type="text" class="infraText" 
	          tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"
	          value="<?=PaginaSEIExterna::tratarHTML($_POST['numeroOab']) ?>"
	          onkeypress="return infraMascaraTexto(this,event,10);" maxlength="10"
	          name="numeroOab" id="numeroOab" />
	        </div>
	                        
	    </div>  

    <div id="divPessoaFisicaPublico1" class="infraAreaDados">
      <div style="clear: both;"></br></div>

      <div style="float:left; width: 23%;">
      <fieldset id="fldStaGenero" class="infraFieldset">
        <legend class="infraLegend">&nbsp;Gênero&nbsp;</legend>

        <div id="divOptFeminino" class="infraDivRadio">
          <input type="radio" name="rdoStaGenero" id="optFeminino" value="F" <?=($objContatoDTO && $objContatoDTO->getStrStaGenero()==ContatoRN::$TG_FEMININO?'checked="checked"':'')?> class="infraRadio" onchange="trocarGenero()" />
          <label id="lblFeminino" for="optFeminino" class="infraLabelRadio" tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>">Feminino</label>
        </div>
        </br>
        <div id="divOptMasculino" class="infraDivRadio">
          <input type="radio" name="rdoStaGenero" id="optMasculino" value="M" <?=($objContatoDTO && $objContatoDTO->getStrStaGenero()==ContatoRN::$TG_MASCULINO?'checked="checked"':'')?> class="infraRadio" onchange="trocarGenero()" />
          <label id="lblMasculino" for="optMasculino" class="infraLabelRadio" tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>">Masculino</label>
        </div>
      </fieldset>
      </div>

      <div style="float:left; width: 2%;">&nbsp;</div>

      <div style="float:left; width: 74%;">

      <div style="float:left; width: 100%;">
      <label id="lblIdCargo" for="cargo" class="infraLabelObrigatorio">Cargo:</label>
      <select id="cargo" name="cargo" class="infraSelect" tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>" style="width: 100%; align:left">
        <?/*=$strItensSelCargo*/?>
      </select>
      </div>

      <div style="float:left; width: 50%;">
      <label id="lblTratamento" for="tratamento" class="infraLabelObrigatorio">Tratamento:</label>
      <input type="text" id="tratamento" name="tratamento" disabled="disabled" class="infraText infraReadOnly" style="width: 93%;" value="<?/*=PaginaSEI::tratarHTML($objContatoDTO->getStrExpressaoTratamentoCargo())*/?>" tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>" />
      </div>


      <div style="float:left; width: 50%;">
      <label id="lblVocativo" for="txtVocativo"  class="infraLabelObrigatorio">Vocativo:</label>
      <input type="text" id="vocativo" name="vocativo" disabled="disabled" class="infraText infraReadOnly" style="width: 98%;" value="<?/*=PaginaSEI::tratarHTML($objContatoDTO->getStrExpressaoVocativoCargo())*/?>" tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>" />
      </div>

      </div>
      
      <div style="clear: both;"></br></div>
    </div>

	    <label class="infraLabelObrigatorio">Telefone:</label><br/>
	    <input type="text" class="infraText" name="telefone" 
	          tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"
	          value="<?=PaginaSEIExterna::tratarHTML($_POST['telefone']) ?>"
	          onkeydown="return infraMascaraTelefone(this,event);" maxlength="25"
	          id="telefone" /><br/>
	        
	    <div style="clear: both;"></div>
	    
	    <div class="div2" style="float:left; width: auto;">
	    	
	    	<br/>    		
	        <div id="div2_1" style="float:left; width: 280px;">
	          <label class="infraLabel">E-mail:</label><br/>
	          <input type="text" class="infraText" 
	            tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"
	            value="<?=PaginaSEIExterna::tratarHTML($_POST['email']) ?>"
	            onkeypress="return infraMascaraTexto(this,event,50);" maxlength="50" 
	            name="email" id="email" style="width: 280px;" />
	        </div>
	        
	        <div id="div2_2" style="float:left; margin-left:20px; width: 280px;">
	          <label class="infraLabel">Sítio na Internet:</label><br/>
	          <input type="text" class="infraText" style="width: 280px;" 
	            tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"
	            value="<?=PaginaSEIExterna::tratarHTML($_POST['sitioInternet']) ?>"
	            onkeypress="return infraMascaraTexto(this,event,50);" maxlength="50" 
	            name="sitioInternet" id="sitioInternet" />
	        </div>
	    
	    </div>  
	    
	    <div style="clear: both;"></div>
	    
	    <div class="div3" style="float:left; width: auto;">
	    	
	    	<br/>
	    	
	        <div id="div3_1" style="float:left; width: 280px;">
	        <label class="infraLabelObrigatorio">Endereço:</label><br/>
	        <input type="text" class="infraText" style="width: 280px;" 
	          tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"
	          value="<?=PaginaSEIExterna::tratarHTML($_POST['endereco']) ?>"
	          name="endereco" id="endereco" />
	        </div>
	        
	        <div id="div3_2" style="float:left; margin-left:20px; width: 280px;">
	        <label class="infraLabelObrigatorio">Bairro:</label><br/>
	        <input type="text" class="infraText" style="width: 280px;" 
	           tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"
	           value="<?=PaginaSEIExterna::tratarHTML($_POST['bairro']) ?>"
	           name="bairro" id="bairro" />
	        </div>
	    
	    </div>  
	    
	    <div style="clear: both;"></div>
	    
	    <div class="div4" style="float:left; width: auto;">
	    	
	    	<br/>
	    	
	        <div id="div4_1" style="float:left; width: auto; display: none;">
	        <label class="infraLabelObrigatorio">País:</label><br/>
	        <input type="text" class="infraText" 
	          onkeyup="paisEstadoCidade(this);" value="Brasil" 
	          onkeypress="return infraMascaraTexto(this,event,50);" 
	          maxlength="50" name="pais" id="pais" />
	        </div>
	        
	        <div id="div4_2" style="float:left; width: auto;">
	        <label class="infraLabelObrigatorio">Estado:</label><br/>
	        
	        <select class="infraSelect" tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>" 
	          name="selEstado" id="selEstado">
	        <?=$strItensSelSiglaEstado?>
	        </select>  
	           	         	         
	        </div>
	        
	        <div id="div4_3" style="float:left; margin-left:20px; width: auto;">
	        <label class="infraLabelObrigatorio">Cidade:</label><br/>
	         
	         <select class="infraSelect" tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>" 
	           name="selCidade" id="selCidade">
	         <?= $strItensSelCidade ?>
	         </select>
	        </div>
	        
	        <div id="div4_4" style="float:left; margin-left:20px; width: auto;">
	        <label class="infraLabelObrigatorio">CEP:</label><br/>
	        <input type="text" class="infraText" 
	          onkeypress="return infraMascaraCEP(this,event);"
		      maxlength="15"
	          tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"
	          value="<?=PaginaSEIExterna::tratarHTML($_POST['cep']) ?>"
	          name="cep" id="cep" />
	        </div>
	    
	    </div>  
	    
	    <div style="clear: both;"></div>
	  
	  </fieldset>  
	    
	  <input type="hidden" name="hdnCadastrar" value="" />
	  <input type="hidden" name="hdnIdEdicao" id="hdnIdEdicao" 
	         value="<?php echo $_POST['hdnIdEdicao']; ?>" /> 
	</form>

<?php } ?>

<?php

PaginaSEIExterna::getInstance()->montarAreaDebug();
PaginaSEIExterna::getInstance()->fecharAreaDados();
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();

//incluindo arquivo com funções JavaScript da página
require_once 'peticionamento_interessado_cadastro_js.php';
?>