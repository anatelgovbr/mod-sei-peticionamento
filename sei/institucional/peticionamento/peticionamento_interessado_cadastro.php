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
  //SessaoSEIExterna::getInstance()->validarLink();
  
  //SessaoSEI::getInstance(false);
  //SessaoSEI::getInstance()->simularLogin(null, null, SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() , SessaoSEIExterna::getInstance()->getNumIdUnidadeAtual() );
  //SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);
  
  //=====================================================
  //INICIO - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
  //=====================================================

  //=====================================================
  //FIM - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
  //=====================================================
  
  switch($_GET['acao']){
    
  	case 'peticionamento_interessado_cadastro':
  		
  		$strTitulo = 'Cadastro de Interessado';
  		
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
  		
  		$strItensSelTipoInteressado = GerirTipoContextoPeticionamentoINT::montarSelectTipoInteressado($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $strTipo);
  		$strItensSelTratamento = TratamentoINT::montarSelectExpressaoRI0467('null','&nbsp;', null ) ;
  		$strItensSelCargo = CargoINT::montarSelectExpressaoRI0468('null','&nbsp;', null );
  		$strItensSelVocativo = VocativoINT::montarSelectExpressaoRI0469('null','&nbsp;', null );
  		
  		if( isset( $_POST['hdnCadastrar'] ) ){
  			
  			$objContatoDTO = new ContatoDTO();
  			$objContatoDTO->retTodos();
  			
  			$objContatoDTO->setNumIdContato(null);
  			$objContatoDTO->setStrSinContexto($strSinContexto);
  			  			
  			if($strSinContexto=='S'){
  				$objContatoDTO->setNumIdTipoContextoContato($numIdTipoContextoContato);
  			} else{
  				$objContatoDTO->setNumIdTipoContextoContato(null);
  				$objContatoDTO->setNumIdContextoContato($numIdContextoContato);
  			}
  			
  			$objContatoDTO->setNumIdTratamento($_POST['tratamento']);
  			$objContatoDTO->setNumIdVocativo($_POST['vocativo']);
  			$objContatoDTO->setNumIdCargo($_POST['cargo']);
  			$objContatoDTO->setNumIdTitulo('');
  			$objContatoDTO->setStrNome($_POST['nome']);
  			$objContatoDTO->setDtaNascimento('');
  			$objContatoDTO->setStrPalavrasChave('');
  			$objContatoDTO->setStrSigla('');
  			$objContatoDTO->setStrGenero('');
  			$objContatoDTO->setStrMatriculaOab($_POST['numeroOab']);
  			$objContatoDTO->setDblCpf($_POST['cpf']);
  			$objContatoDTO->setNumMatricula('');
  			$objContatoDTO->setDblCnpj($_POST['cnpj']);
  			$objContatoDTO->setDblRg($_POST['rg']);
  			$objContatoDTO->setStrOrgaoExpedidor($_POST['orgaoExpedidor']);
  			$objContatoDTO->setStrTelefone($_POST['telefone']);
  			$objContatoDTO->setStrFax('');
  			$objContatoDTO->setStrEmail($_POST['txtEmail']);
  			$objContatoDTO->setStrSitioInternet($_POST['txtSitioInternet']);
  			$objContatoDTO->setStrEndereco($_POST['endereco']);
  			$objContatoDTO->setStrBairro($_POST['txtBairro']);
  			$objContatoDTO->setStrSiglaEstado($_POST['estado']);
  			$objContatoDTO->setStrNomeCidade($_POST['cidade']);
  			$objContatoDTO->setStrNomePais($_POST['pais']);
  			$objContatoDTO->setStrCep($_POST['cep']);
  			$objContatoDTO->setStrObservacao('');
  			$objContatoDTO->setStrSinEnderecoContexto('S');
  			$objContatoDTO->setDblIdPessoaRh(null);
  			$objContatoDTO->setStrSinAtivo('S');
  			$objContatoDTO->setNumIdCarreira(null);
  			$objContatoDTO->setNumIdNivelFuncao(null);
  			
  			$strUsuario = SessaoSEIExterna::getInstance()->getStrSiglaUsuario();
  			$strSiglaUnidade = SessaoSEIExterna::getInstance()->getStrSiglaUnidadeAtual();
  			$numIdUsuario = SessaoSEIExterna::getInstance()->getNumIdUsuario();
  			$numIdUnidade = SessaoSEIExterna::getInstance()->getNumIdUnidadeAtual();
  			
  			$objUnidadeRN = new UnidadeRN();
  			$objUnidadeDTO = new UnidadeDTO();  			
  			$objUnidadeDTO->retTodos(true);
  			$objUnidadeDTO->setNumIdUnidade( SessaoSEIExterna::getInstance()->getNumIdUnidadeAtual() );
  			$objUnidadeDTO = $objUnidadeRN->consultarRN0125( $objUnidadeDTO );
  			
  			SessaoSEI::getInstance(false)->simularLogin(SessaoSEI::$USUARIO_INTERNET,SessaoSEI::$UNIDADE_TESTE);
  			//SessaoSEI::getInstance()->simularLogin($strUsuario, $strSiglaUnidade, $numIdUsuario);
  			
  			//configura sessão
  			SessaoSEI::getInstance()->setNumIdUnidadeAtual( $numIdUnidade );
  			SessaoSEI::getInstance()->setStrSiglaUnidadeAtual( $strSiglaUnidade );
  			SessaoSEI::getInstance()->setNumIdOrgaoUnidadeAtual($objUnidadeDTO->getNumIdOrgao());
  			SessaoSEI::getInstance()->setStrSiglaOrgaoUnidadeAtual($objUnidadeDTO->getStrSiglaOrgao());
  			
  			$objContatoRN = new ContatoRN();
  			$objContatoDTO = $objContatoRN->cadastrarRN0322($objContatoDTO);
  			$idContatoCadastro = $objContatoDTO->getNumIdContato();
  			
  			//após cadastrar o contato fechar janela modal e preencher campos necessarios
  		    echo "<script>";
  			echo " window.close();";
  		    echo "</script>";
  			die;
  			
  		}
  		
  		/*
  		$strItensSelTratamento = TratamentoINT::montarSelectExpressaoRI0467('null','&nbsp;',$objContatoDTO->getNumIdTratamento());
  		$strItensSelCargo = CargoINT::montarSelectExpressaoRI0468('null','&nbsp;',$objContatoDTO->getNumIdCargo());
  		$strItensSelVocativo = VocativoINT::montarSelectExpressaoRI0469('null','&nbsp;',$objContatoDTO->getNumIdVocativo());
  		$strItensSelTitulo = TituloINT::montarSelectExpressaoMasculinaRI0470('null','&nbsp;',$objContatoDTO->getNumIdTitulo());
  		$strItensSelSiglaEstado = UfINT::montarSelectSiglaRI0892('null','&nbsp;',null);
  		$strLinkAjaxNomeCidade = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=cidade_montar_select_nome');
  		$strLinkAjaxContatoRI0571 = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=contato_RI0571');
  		$strLinkAjaxContatoRI0466 = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=contato_RI0466');
  		
  		$strLinkAjaxContatos = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=contato_auto_completar_contexto_pesquisa');
  		$strItensSelCidade = CidadeINT::montarSelectNomeRI0506('null','&nbsp;','null',$objContatoDTO->getStrSiglaEstado());
  		*/
  		
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
$arrComandos[] = '<button type="button" accesskey="S" name="Salvar" value="Salvar" onclick="salvar()" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
$arrComandos[] = '<button type="button" accesskey="F" name="btnFechar" value="Fechar" onclick="location.href=\''.PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador.php?acao='.PaginaSEIExterna::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'].PaginaSEIExterna::getInstance()->montarAncora($_GET['id_indisponibilidade_peticionamento']))).'\';" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';
?> 
<form id="frmCadastro" name="frmCadastro" 
      method="post" onsubmit="return OnSubmitForm();"  
      action="<?=PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">
<?php
PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaSEIExterna::getInstance()->abrirAreaDados('auto');
?>

 <fieldset id="field1" class="infraFieldset sizeFieldset">
    
    <legend class="infraLegend">&nbsp; Interessado &nbsp;</legend>       
	
	<?php if( isset( $_GET['cpf'] )) { ?>
	
	<input type="radio" name="tipoPessoa" value="pf" id="rdPF" onclick="selecionarPF()" />
	<label for="rdPF" class="infraLabelRadio">Pessoa física</label> <br/>
	
	    <input type="radio" name="tipoPessoaPF" value="0" id="rdPF1" 
	      style="display: none; margin-left: 20px;" onclick="selecionarPF1()" />
	    <label for="rdPF1" id="lblrdPF1" class="infraLabelRadio" style="display: none;">
	    Sem vínculo com qualquer Pessoa Jurídica <br/> 
	    </label>
	
	    <input type="radio" name="tipoPessoaPF" value="1" id="rdPF2" 
	      style="display: none; margin-left: 20px;" onclick="selecionarPF2()" />
	    <label for="rdPF2" id="lblrdPF2" class="infraLabelRadio" style="display: none;">
	    Com vínculo com Pessoa Jurídica <br/> 
	    </label> 
	
	<?php } ?>
	
	<?php if( isset( $_GET['cnpj'] )) { ?>
	  <input type="radio" name="tipoPessoa" value="pj" id="rdPJ" onclick="selecionarPJ()" />
	  <label for="rdPJ" class="infraLabelRadio">Pessoa jurídica</label>
	<?php } ?>
			
 </fieldset>
  
 <fieldset id="field2" class="infraFieldset sizeFieldset">
    
    <legend class="infraLegend">&nbsp; Formulário de Cadastro &nbsp;</legend>
    
    <label class="infraLabelObrigatorio"> Tipo de Interessado:</label><br/>
    <select class="infraSelect" width="380" id="tipoInteressado" 
         name="tipoInteressado" style="width:380px;" >
        <?=$strItensSelTipoInteressado?>
    </select> <br/>
    
    <label id="lblNome" class="infraLabelObrigatorio" style="display:none;"> Nome:<br/>
    <input type="text" id="txtNome" name="txtNome" class="infraText" value="" 
       onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250" 
       tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
    <br/><br/>
    </label>
    
    <label id="lblRazaoSocial" class="infraLabelObrigatorio" style="display:none;"> Razão Social:<br/>
    <input type="text" id="txtRazaoSocial" name="txtRazaoSocial" class="infraText" value="" 
       onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250" 
       tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
    <br/><br/>
    </label>
    
    <label id="lblPjVinculada" style="display: none;" class="infraLabelObrigatorio"> 
    Pessoa jurídica a qual o interessado é vinculado:<br/>
    <input type="text" class="infraText" name="txtPjVinculada" id="txtPjVinculada" style="width: 540px; display: none;" />
    <br/><br/>
    </label>
    
    <label id="lblCPF" style="display: none;" class="infraLabelObrigatorio"> CPF:<br/>
    <input type="text" class="infraText" name="txtCPF" id="txtCPF" 
      readonly="readonly"
      onkeypress="return infraMascaraCpf(this, event)"
      style="width: 540px;" />
    <br/><br/>
    </label>
    
    <label id="lblCNPJ" style="display: none;" class="infraLabelObrigatorio"> CNPJ:<br/>
    <input type="text" class="infraText" name="txtCNPJ" id="txtCNPJ" 
      readonly="readonly" onkeypress="return infraMascaraCnpj(this, event)"
      style="width: 540px;" />
    <br/><br/>
    </label>
    
    <div id="div1" style="float:left; width: auto; display: none;">
        
        <div id="div1_2" style="float:left; width: auto;">
        <label class="infraLabelObrigatorio">RG:</label><br/>
        <input type="text" class="infraText" 
          onkeypress="return infraMascaraNumero(this,event, 10);" 
          name="rg" id="rg" />
        </div>
        
        <div id="div1_3" style="float:left; margin-left:20px; width: auto;">
        <label class="infraLabelObrigatorio">Órgão Expedidor do RG:</label><br/>
        <input type="text" class="infraText" name="orgaoExpedidor"
           onkeypress="return infraMascaraTexto(this,event, 50);"
           id="orgaoExpedidor" />
        </div>
        
        <div id="div1_1" style="float:left; margin-left:20px; width: auto;">
        <label class="infraLabel">Número da OAB:</label><br/>
        <input type="text" class="infraText" 
          onkeypress="return infraMascaraTexto(this,event,10);" maxlength="10"
          name="numeroOab" id="numeroOab" />
        </div>
                        
    </div>  
    
    <div style="clear: both;"></div>
    
    <label class="infraLabelObrigatorio" id="lblTratamento" style="display: none;"> 
    <br/>Tratamento:<br/>
    <select class="infraSelect" width="380" id="tratamento" name="tratamento" style="width:380px;">
        <?=$strItensSelTratamento?>
    </select> <br/>
    </label>
    
    <label class="infraLabelObrigatorio" id="lblCargo" style="display: none;"> Cargo:<br/>
    <select class="infraSelect" width="380" id="cargo" name="cargo" style="width:380px;" >
        <?=$strItensSelCargo?>
    </select> <br/>
    </label>
    
    <label class="infraLabelObrigatorio" id="lblVocativo" style="display: none;"> Vocativo:<br/>
    <select class="infraSelect" width="380" id="vocativo" name="vocativo" style="width:380px;" >
        <?=$strItensSelVocativo?>
    </select><br/> 
    </label>
        
    <label class="infraLabelObrigatorio">Telefone:</label><br/>
    <input type="text" class="infraText" name="telefone" 
          onkeypress="return infraMascaraTexto(this,event,50);" maxlength="50"
          id="telefone" /><br/>
        
    <div style="clear: both;"></div>
    
    <div class="div2" style="float:left; width: auto;">
    	
    	<br/>    		
        <div id="div2_1" style="float:left; width: 280px;">
          <label class="infraLabel">Email:</label><br/>
          <input type="text" class="infraText" 
            onkeypress="return infraMascaraTexto(this,event,50);" maxlength="50" 
            name="email" id="email" style="width: 280px;" />
        </div>
        
        <div id="div2_2" style="float:left; margin-left:20px; width: 280px;">
          <label class="infraLabel">Sítio na Internet:</label><br/>
          <input type="text" class="infraText" style="width: 280px;" 
            onkeypress="return infraMascaraTexto(this,event,50);" maxlength="50" 
            name="sitioInternet" id="sitioInternet" />
        </div>
    
    </div>  
    
    <div style="clear: both;"></div>
    
    <div class="div3" style="float:left; width: auto;">
    	
    	<br/>
    	
        <div id="div3_1" style="float:left; width: 280px;">
        <label class="infraLabelObrigatorio">Endereço:</label><br/>
        <input type="text" class="infraText" style="width: 280px;" name="endereco" id="endereco" />
        </div>
        
        <div id="div3_2" style="float:left; margin-left:20px; width: 280px;">
        <label class="infraLabelObrigatorio">Bairro:</label><br/>
        <input type="text" class="infraText" style="width: 280px;" name="bairro" id="bairro" />
        </div>
    
    </div>  
    
    <div style="clear: both;"></div>
    
    <div class="div4" style="float:left; width: auto;">
    	
    	<br/>
    	
        <div id="div4_1" style="float:left; width: auto;">
        <label class="infraLabelObrigatorio">País:</label><br/>
        <input type="text" class="infraText" 
          onkeyup="paisEstadoCidade(this);" value="Brasil" 
          onkeypress="return infraMascaraTexto(this,event,50);" maxlength="50" 
          name="pais" id="pais" />
        </div>
        
        <div id="div4_2" style="float:left; margin-left:20px; width: auto;">
        <label class="infraLabelObrigatorio">Estado:</label><br/>
        <input type="text" class="infraText" name="estado" id="estado" />
        </div>
        
        <div id="div4_2_combo" style="float:left; margin-left:20px; width: auto; display: none;">
            <label class="infraLabelObrigatorio">Estado:</label><br/>
            <select class="infraSelect" name="cbEstado" id="cbEstado">
            </select>
        </div>
        
        <div id="div4_3" style="float:left; margin-left:20px; width: auto;">
        <label class="infraLabelObrigatorio">Cidade:</label><br/>
        <input type="text" class="infraText" name="cidade" id="cidade" />
        </div>
        
        <div id="div4_3_combo" style="float:left; margin-left:20px; width: auto; display: none;">
            <label class="infraLabelObrigatorio">Cidade:</label><br/>
            <select class="infraSelect" name="cbCidade" id="cbCidade">
            </select>
        </div>
        
        <div id="div4_4" style="float:left; margin-left:20px; width: auto;">
        <label class="infraLabelObrigatorio">CEP:</label><br/>
        <input type="text" class="infraText" name="cep" id="cep" />
        </div>
    
    </div>  
    
    <div style="clear: both;"></div>
  
  </fieldset>  
    
</form>

<?php
//PaginaSEIExterna::getInstance()->montarBarraComandosInferior($arrComandos);  
PaginaSEIExterna::getInstance()->montarAreaDebug();
PaginaSEIExterna::getInstance()->fecharAreaDados();
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();

//incluindo arquivo com funções JavaScript da página
require_once 'peticionamento_interessado_cadastro_js.php';
?>