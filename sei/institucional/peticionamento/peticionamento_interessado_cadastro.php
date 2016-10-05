<?php
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
  		
  		if( isset( $_GET['cpf']) ){
  			$strTitulo .= ' - Pessoa Física';
  		}
  		
  		else if( isset( $_GET['cnpj']) ){
  			$strTitulo .= ' - Pessoa Jurídica';
  		}
  		
  		$strPrimeiroItemValor = 'null';
  		$strPrimeiroItemDescricao = '&nbsp;';
  		$strValorItemSelecionado = null;
  		$strTipo = 'Cadastro';
  		
  		$strItensSelSiglaEstado = UfINT::montarSelectSiglaRI0892('null','&nbsp;',null);
  		//$strItensSelCidade = CidadeINT::montarSelectNomeRI0506('null','&nbsp;','null',$objContatoDTO->getStrSiglaEstado());
  		$strItensSelCidade = CidadeINT::montarSelectNomeRI0506('null','&nbsp;','null', null);
  		$strItensSelTipoInteressado = GerirTipoContextoPeticionamentoINT::montarSelectTipoInteressado($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $strTipo);
  		$strItensSelTratamento = TratamentoINT::montarSelectExpressaoRI0467('null','&nbsp;', null ) ;
  		$strItensSelCargo = CargoINT::montarSelectExpressaoRI0468('null','&nbsp;', null );
  		$strItensSelVocativo = VocativoINT::montarSelectExpressaoRI0469('null','&nbsp;', null );
  		
  		//setando dados no contato que esta sendo cadastrado ou editado
  		if( isset( $_POST['hdnCadastrar'] ) ){
  			
  			$objContatoDTO = new ContatoDTO();
  			$objContatoDTO->retTodos();
  			
  			$numIdTipoContextoContato = $_POST['tipoInteressado'];
  			
  			if( !isset( $_POST['hdnIdEdicao'] ) || $_POST['hdnIdEdicao'] == ""  ){
  			  $objContatoDTO->setNumIdContato(null);
  			  
  			} else {
  				  				
  				$objContatoRN = new ContatoRN();  					
  				$objContatoDTO = new ContatoDTO();
  				$objContatoDTO->retStrSinContexto();
  				$objContatoDTO->retNumMatricula();
	  			$objContatoDTO->retDblRg();
	  			$objContatoDTO->retStrOrgaoExpedidor();
	  			$objContatoDTO->retStrTelefone();
	  			$objContatoDTO->retStrFax();
	  			$objContatoDTO->retStrEmail();
	  			$objContatoDTO->retStrSitioInternet();
	  			$objContatoDTO->retStrEndereco();
	  			$objContatoDTO->retStrBairro();
	  			$objContatoDTO->retStrSiglaEstado();
	  			$objContatoDTO->retStrNomeCidade();
	  			$objContatoDTO->retStrNomePais();
	  			$objContatoDTO->retStrCep();
	  			$objContatoDTO->retStrObservacao();
	  			$objContatoDTO->retStrSinEnderecoContexto();
	  			$objContatoDTO->retDblIdPessoaRh();
	  			$objContatoDTO->retNumIdCarreira();
	  			$objContatoDTO->retNumIdNivelFuncao();
	  			$objContatoDTO->retNumIdContato();
  			
  				$objContatoDTO->setNumIdContato( $_POST['hdnIdEdicao'] );
  				$objContatoDTO = $objContatoRN->consultarRN0324($objContatoDTO);
  			    
  			}
  					
  			$objContatoDTO->setNumIdTratamento($_POST['tratamento']);
  			$objContatoDTO->setNumIdVocativo($_POST['vocativo']);
  			$objContatoDTO->setNumIdCargo($_POST['cargo']);
  			$objContatoDTO->setNumIdTitulo('');
  			
  			if( isset($_POST['txtNome']) && $_POST['txtNome'] != "" ){
  			  $objContatoDTO->setStrNome($_POST['txtNome']);
  			}
  			
  			else if( isset($_POST['txtRazaoSocial']) && $_POST['txtRazaoSocial'] != "" ){
  				$objContatoDTO->setStrNome($_POST['txtRazaoSocial']);
  			}
  			
  			$objContatoDTO->setDtaNascimento('');
  			$objContatoDTO->setStrPalavrasChave('');
  			$objContatoDTO->setStrSigla('');
  			$objContatoDTO->setStrGenero('');
  			$objContatoDTO->setStrMatriculaOab($_POST['numeroOab']);
  			
  			//campos manipulados apenas no cadastro (nao na ediçao)
  			if( !isset( $_POST['hdnIdEdicao'] ) || $_POST['hdnIdEdicao'] == "" ) {
  			  
  			  $objContatoDTO->setDblCpf($_POST['txtCPF']);
  			  $objContatoDTO->setDblCnpj($_POST['txtCNPJ']);
  			  $objContatoDTO->setStrSinAtivo('S');
  			  
  			  if( isset ( $_POST['hdnIdContextoContato'] ) && $_POST['hdnIdContextoContato'] != "") {
  			   	$objContatoDTO->setNumIdContextoContato( $_POST['hdnIdContextoContato'] );
  			  }
  			  
  			  //PF sem vinculo com PJ
  			  if( $_POST['tipoPessoaPF'] == '0' ){
  			  
  			  	$strSinContexto = 'S';
  			  	unset( $_POST['hdnIdContextoContato'] );
  			  	$objContatoDTO->setNumIdTipoContextoContato($numIdTipoContextoContato);
  			  		
  			  	//PF com vinculo com PJ
  			  } else if( $_POST['tipoPessoaPF'] == '1' ){
  			  
  			  	$strSinContexto = 'N';
  			  	$objContatoDTO->setNumIdTipoContextoContato('');
  			  
  			  } 
  			  
  			  //PJ
  			  else {
  			  	
  			  	$strSinContexto = 'S';
  			  	unset( $_POST['hdnIdContextoContato'] );
  			  	$objContatoDTO->setNumIdTipoContextoContato($numIdTipoContextoContato);
  			  }
  			  
  			  $objContatoDTO->setStrSinContexto($strSinContexto);
  			}
  			
  			$objContatoDTO->setNumMatricula('');
  			$objContatoDTO->setDblRg($_POST['rg']);
  			$objContatoDTO->setStrOrgaoExpedidor($_POST['orgaoExpedidor']);
  			$objContatoDTO->setStrTelefone($_POST['telefone']);
  			$objContatoDTO->setStrFax('');
  			$objContatoDTO->setStrEmail($_POST['email']);
  			$objContatoDTO->setStrSitioInternet($_POST['sitioInternet']);
  			$objContatoDTO->setStrEndereco($_POST['endereco']);
  			$objContatoDTO->setStrBairro($_POST['bairro']);
  			$objContatoDTO->setStrSiglaEstado($_POST['selEstado']);
  			$objContatoDTO->setStrNomeCidade($_POST['selCidade']);
  			$objContatoDTO->setStrNomePais($_POST['pais']);
  			$objContatoDTO->setStrCep($_POST['cep']);
  			$objContatoDTO->setStrObservacao('');
  			$objContatoDTO->setStrSinEnderecoContexto('N');
  			$objContatoDTO->setDblIdPessoaRh(null);
  			$objContatoDTO->setNumIdCarreira(null);
  			$objContatoDTO->setNumIdNivelFuncao(null);
  			
  			//necessario para preencher o campo id_usuario_cadastro ao salvar o contato
  			SessaoSEI::getInstance()->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
  			
  			//chamando RN para finalizar o cadastro, fechar a janela setando parametros
  			//na janela "pai" (caso o cadastro seja corretamente executado)
  			$objContatoRN = new ContatoRN();
  			
  			//verificando se é cadastro ou ediçao de contato
  			if( !isset( $_POST['hdnIdEdicao'] ) || $_POST['hdnIdEdicao'] == ""  ){
  			  
  			  $objContatoDTO = $objContatoRN->cadastrarRN0322($objContatoDTO);
  			  $idContatoCadastro = $objContatoDTO->getNumIdContato();
  			  
  			} else if( $_POST['hdnIdEdicao'] != "" ) {
  				
  			  $idContatoCadastro = $objContatoDTO->getNumIdContato();
  			  $objContatoRN->alterarRN0323($objContatoDTO);
  			  
  			}
  			
  			//nome / razao social
  			if( isset($_POST['txtNome']) && $_POST['txtNome'] != "" ){
  				$nome = $_POST['txtNome'];
  			}
  				
  			else if( isset($_POST['txtRazaoSocial']) && $_POST['txtRazaoSocial'] != "" ){
  				$nome = $_POST['txtRazaoSocial'];
  			}
  			
  			//cpf/cnpj
  			if( isset($_POST['txtCPF']) && $_POST['txtCPF'] != "" ){
  				$cpfCnpjEditado = $_POST['txtCPF'];
  			}
  			
  			else if( isset($_POST['txtCNPJ']) && $_POST['txtCNPJ'] != "" ){
  				$cpfCnpjEditado = $_POST['txtCNPJ'];
  			}
  			  			
  			//após cadastrar o contato fechar janela modal e preencher campos necessarios  			
  			if( !isset( $_POST['hdnIdEdicao'] ) || $_POST['hdnIdEdicao'] == ""  ){
  				
  				echo "<script>";
  				echo "window.opener.document.getElementById('txtNomeRazaoSocial').value = '" . $nome . "';";
  				echo "window.opener.document.getElementById('hdnCustomizado').value = 'true';";
  				echo "window.opener.document.getElementById('hdnIdInteressadoCadastrado').value = " . $objContatoDTO->getNumIdContato() . ";";
  				echo " window.close();";
  				echo "</script>";
  				die;
  				
  			} else {
  				
  				echo "<script>";
  				echo "window.opener.atualizarNomeRazaoSocial('". $cpfCnpjEditado ."', '". $nome ."');";
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
  			
  			$strItensSelSiglaEstado = UfINT::montarSelectSiglaRI0892('null','&nbsp;', $objContatoDTO->getStrSiglaEstado());
  			$strItensSelCidade = CidadeINT::montarSelectNomeRI0506('null','&nbsp;', $objContatoDTO->getStrNomeCidade() ,$objContatoDTO->getStrSiglaEstado());
  			
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
  			$_POST['telefone'] = $objContatoDTO->getStrTelefone();
  			$_POST['email'] = $objContatoDTO->getStrEmail();
  			$_POST['sitioInternet'] = $objContatoDTO->getStrSitioInternet();
  			$_POST['endereco'] = $objContatoDTO->getStrEndereco();
  			$_POST['bairro'] = $objContatoDTO->getStrBairro();
  			$_POST['estado'] = $objContatoDTO->getStrSiglaEstado();
  			$_POST['cidade'] = $objContatoDTO->getStrNomeCidade();
  			$_POST['pais'] = $objContatoDTO->getStrNomePais();
  			$_POST['cep'] = $objContatoDTO->getStrCep();
  			
  			$_POST['tratamento'] = $objContatoDTO->getNumIdTratamento();
  			$_POST['vocativo'] = $objContatoDTO->getNumIdVocativo();
  			$_POST['cargo'] = $objContatoDTO->getNumIdCargo();
  			$_POST['hdnIdEdicao'] = $_POST['hdnIdEdicaoAuxiliar'];
  			
  			$_POST['hdnIdContextoContato'] = $objContatoDTO->getNumIdContextoContato();
  			  			
  			if( $_POST['hdnIdContextoContato'] != "" && $_POST['hdnIdContextoContato'] != null ){
  				
  				$objContatoPJVinculadaDTO = new ContatoDTO();
  				$objContatoPJVinculadaDTO->retNumIdContato();
  				$objContatoPJVinculadaDTO->retStrNome();
  				$objContatoPJVinculadaDTO->retNumIdTipoContextoContato();
  				$objContatoPJVinculadaDTO->setNumIdContato( $_POST['hdnIdContextoContato']  );
  				
  				$objContatoPJVinculadaDTO = $objContatoRN->consultarRN0324( $objContatoPJVinculadaDTO );
  				$_POST['tipoInteressado'] = $objContatoPJVinculadaDTO->getNumIdTipoContextoContato();  				
  				$numIdTipoContextoContato = $_POST['tipoInteressado']; 
  				$_POST['txtPjVinculada'] = $objContatoPJVinculadaDTO->getStrNome();
  				
  			}
  			  			
  			$strItensSelTipoInteressado = GerirTipoContextoPeticionamentoINT::montarSelectTipoInteressado($strPrimeiroItemValor, $strPrimeiroItemDescricao, $numIdTipoContextoContato, $strTipo);
  			
  			if( isset( $_GET['cpf'] )) {
  			  $strItensSelTratamento = TratamentoINT::montarSelectExpressaoRI0467('null','&nbsp;', $_POST['tratamento'] ) ;
  			  $strItensSelCargo = CargoINT::montarSelectExpressaoRI0468('null','&nbsp;', $_POST['cargo'] );
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
	    
	    <legend class="infraLegend">&nbsp; Interessado &nbsp;</legend>       
		
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
		      style="display: none; margin-left: 20px;" onclick="selecionarPF2()" />
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
	         name="tipoInteressado" style="width:380px;" >
	        <?=$strItensSelTipoInteressado?>
	    </select> <br/>
	    
	    <label id="lblNome" class="infraLabelObrigatorio" style="display:none;">Nome Completo:<br/>
	    <input type="text" id="txtNome" name="txtNome" 
	          class="infraText" style="width: 580px;" 
	           value="<?php echo $_POST['txtNome']; ?>" 
	       onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250" 
	       tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>" />
	    <br/><br/>
	    </label>
	    
	    <label id="lblRazaoSocial" class="infraLabelObrigatorio" style="display:none;">Razão Social:<br/>
	    <input type="text" id="txtRazaoSocial" name="txtRazaoSocial" 
	          class="infraText" style="width: 580px;" 
	       value="<?php echo $_POST['txtRazaoSocial']; ?>"
	       onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250" 
	       tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
	    <br/><br/>
	    </label>
	    
	    <label id="lblPjVinculada" style="display: none;" class="infraLabelObrigatorio">Razão Social da Pessoa Jurídica vinculada:<br/>
	    
	    <?php if( $_POST['hdnIdContextoContato'] == '') {?>
	    
	    <input type="text" class="infraText" 
	    tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"
	    name="txtPjVinculada" id="txtPjVinculada" 
	           autocomplete="off" style="width: 580px; display: none;" />
	    
	    <?php } else { ?>
	    
	    <input type="text" class="infraText" 
	      value="<?php echo $_POST['txtPjVinculada']; ?>"
	      tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"
	      name="txtPjVinculada" id="txtPjVinculada" 
	           autocomplete="off" style="width: 580px;" />
	    
	    <?php } ?>
	           
	    <input type="hidden" name="hdnIdContextoContato" id="hdnIdContextoContato" 
	           value="<?php echo $_POST['hdnIdContextoContato'];  ?>" />
	           
	    <br/><br/>
	    </label>
	    
	    <label id="lblCPF" style="display: none;" class="infraLabelObrigatorio">CPF:<br/>
	    <input type="text" class="infraText" name="txtCPF" id="txtCPF" 
	      value="<?php echo $_POST['txtCPF']; ?>"
	      readonly="readonly"
	      onkeypress="return infraMascaraCpf(this, event)"
	      style="width: 280px;" />
	    <br/><br/>
	    </label>
	    
	    <label id="lblCNPJ" style="display: none;" class="infraLabelObrigatorio">CNPJ:<br/>
	    <input type="text" class="infraText" name="txtCNPJ" id="txtCNPJ"
	      value="<?php echo $_POST['txtCNPJ']; ?>" 
	      readonly="readonly" onkeypress="return infraMascaraCnpj(this, event)"
	      style="width: 280px;" />
	    <br/><br/>
	    </label>
	    
	    <div id="div1" style="float:left; width: auto; display: none;">
	        
	        <div id="div1_2" style="float:left; width: auto;">
	        <label class="infraLabelObrigatorio">RG:</label><br/>
	        <input type="text" class="infraText" 
	          value="<?php echo $_POST['rg']; ?>"
	          tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"
	          onkeypress="return infraMascaraNumero(this,event, 10);" 
	          name="rg" id="rg" />
	        </div>
	        
	        <div id="div1_3" style="float:left; margin-left:20px; width: auto;">
	        <label class="infraLabelObrigatorio">Órgão Expedidor do RG:</label><br/>
	        <input type="text" class="infraText" name="orgaoExpedidor"
	           tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"
	           value="<?php echo $_POST['orgaoExpedidor']; ?>"
	           onkeypress="return infraMascaraTexto(this,event, 50);"
	           id="orgaoExpedidor" />
	        </div>
	        
	        <div id="div1_1" style="float:left; margin-left:20px; width: auto;">
	        <label class="infraLabel">Número da OAB:</label><br/>
	        <input type="text" class="infraText" 
	          tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"
	          value="<?php echo $_POST['numeroOab']; ?>"
	          onkeypress="return infraMascaraTexto(this,event,10);" maxlength="10"
	          name="numeroOab" id="numeroOab" />
	        </div>
	                        
	    </div>  
	    
	    <div style="clear: both;"></div>
	    
	    <label class="infraLabelObrigatorio" id="lblTratamento" style="display: none;"> 
	    <br/>Tratamento:<br/>
	    <select class="infraSelect" width="380" id="tratamento" 
	    tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"
	    name="tratamento" style="width:380px;">
	        <?=$strItensSelTratamento?>
	    </select> <br/>
	    </label>
	    
	    <label class="infraLabelObrigatorio" id="lblCargo" style="display: none;"> Cargo:<br/>
	    <select class="infraSelect" 
	    tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"
	    width="380" id="cargo" name="cargo" style="width:380px;" >
	        <?=$strItensSelCargo?>
	    </select> <br/>
	    </label>
	    
	    <label class="infraLabelObrigatorio" id="lblVocativo" style="display: none;"> Vocativo:<br/>
	    <select class="infraSelect" width="380" id="vocativo" 
	    tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"
	    name="vocativo" style="width:380px;" >
	        <?=$strItensSelVocativo?>
	    </select><br/> 
	    </label>
	        
	    <label class="infraLabelObrigatorio">Telefone:</label><br/>
	    <input type="text" class="infraText" name="telefone" 
	          tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"
	          value="<?php echo $_POST['telefone']; ?>"
	          onkeydown="return infraMascaraTelefone(this,event);" maxlength="25"
	          id="telefone" /><br/>
	        
	    <div style="clear: both;"></div>
	    
	    <div class="div2" style="float:left; width: auto;">
	    	
	    	<br/>    		
	        <div id="div2_1" style="float:left; width: 280px;">
	          <label class="infraLabel">E-mail:</label><br/>
	          <input type="text" class="infraText" 
	            tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"
	            value="<?php echo $_POST['email']; ?>"
	            onkeypress="return infraMascaraTexto(this,event,50);" maxlength="50" 
	            name="email" id="email" style="width: 280px;" />
	        </div>
	        
	        <div id="div2_2" style="float:left; margin-left:20px; width: 280px;">
	          <label class="infraLabel">Sítio na Internet:</label><br/>
	          <input type="text" class="infraText" style="width: 280px;" 
	            tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"
	            value="<?php echo $_POST['sitioInternet']; ?>"
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
	          value="<?php echo $_POST['endereco']; ?>"
	          name="endereco" id="endereco" />
	        </div>
	        
	        <div id="div3_2" style="float:left; margin-left:20px; width: 280px;">
	        <label class="infraLabelObrigatorio">Bairro:</label><br/>
	        <input type="text" class="infraText" style="width: 280px;" 
	           tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"
	           value="<?php echo $_POST['bairro']; ?>"
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
	          value="<?php echo $_POST['cep']; ?>"
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