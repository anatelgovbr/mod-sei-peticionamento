<?
/**
* ANATEL
*
* 25/06/2016 - criado por marcelo.bezerra@cast.com.br - CAST
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
  SessaoSEIExterna::getInstance()->validarLink();
  SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);
  
  //=====================================================
  //INICIO - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
  //=====================================================
     
  //preenche a combo Função
  $objCargoDTO = new CargoDTO();
  $objCargoDTO->retTodos();  
  $objCargoDTO->setOrdStrExpressao(InfraDTO::$TIPO_ORDENACAO_ASC);
   
  $objCargoRN = new CargoRN();
  $arrObjCargoDTO = $objCargoRN->listarRN0302($objCargoDTO);
  
  //=====================================================
  //FIM - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
  //=====================================================
  
  switch($_GET['acao']){
    
  	case 'peticionamento_usuario_externo_assinar':
  		
  		$processoPeticionamentoRN = new ProcessoPeticionamentoRN();
  		$processoPeticionamentoRN->processarCadastro( $_POST );  		
  		break;
  		
  	case 'peticionamento_usuario_externo_concluir':
  		
  		$processoPeticionamentoRN = new ProcessoPeticionamentoRN();
  		$strTitulo = 'Concluir Peticionamento - Assinatura Eletrônica';
  		
  		if( isset( $_POST['senhaSEI'] ) ){
  			
  			//documento montado no editor rico do SEI
  			if( SessaoSEIExterna::getInstance()->isSetAtributo('docPrincipalConteudoHTML') ){
  				$_POST['docPrincipalConteudoHTML'] = SessaoSEIExterna::getInstance()->getAtributo('docPrincipalConteudoHTML');
  			} 
  			
  			//obtendo a unidade de abertura do processo
  			$idTipoProc = $_POST['id_tipo_procedimento'];
  			$objTipoProcDTO = new TipoProcessoPeticionamentoDTO();
  			$objTipoProcDTO->retTodos(true);
  			$objTipoProcDTO->setNumIdTipoProcessoPeticionamento( $idTipoProc );
  			$objTipoProcRN = new TipoProcessoPeticionamentoRN();
  			$objTipoProcDTO = $objTipoProcRN->consultar( $objTipoProcDTO );
  			
  			//obtendo a unidade do tipo de processo selecionado - Pac 10 - pode ser uma ou MULTIPLAS unidades selecionadas
  			$relTipoProcUnidadeDTO = new RelTipoProcessoUnidadePeticionamentoDTO();
  			$relTipoProcUnidadeDTO->retTodos();
  			$relTipoProcUnidadeRN = new RelTipoProcessoUnidadePeticionamentoRN();
  			$relTipoProcUnidadeDTO->setNumIdTipoProcessoPeticionamento( $idTipoProc );
  			$arrRelTipoProcUnidadeDTO = $relTipoProcUnidadeRN->listar( $relTipoProcUnidadeDTO );
  			
  			$arrUnidadeUFDTO = null;
  			$idUnidadeTipoProcesso = null;
  			
  			//==============================================================
  			//UNIDADES MULTIPLAS - Pegar unidade selecionada na combo de UF
  			//==============================================================
  			if( $arrRelTipoProcUnidadeDTO != null && count( $arrRelTipoProcUnidadeDTO ) > 1 ) {
  				$idUnidadeTipoProcesso = $arrRelTipoProcUnidadeDTO[0]->getNumIdUnidade();
  				//echo $idUnidadeTipoProcesso; die();
  			}
  			
  			$arrParam = array();
  			$arrParam['senhaSEI'] = $_POST['senhaSEI'];
  			$processoPeticionamentoRN->validarSenha( $arrParam );
			$arrDadosProcessoComRecibo = $processoPeticionamentoRN->gerarProcedimento( $_POST );
			$idRecibo = $arrDadosProcessoComRecibo[0]->getNumIdReciboPeticionamento();
			
			//executar javascript para fechar janela filha e redirecionar janela pai para a tela de detalhes do recibo que foi gerado
			$url = "controlador_externo.php?id_md_pet_rel_recibo_protoc=" . $idRecibo . "&acao=recibo_peticionamento_usuario_externo_consultar&acao_origem=recibo_peticionamento_usuario_externo_listar&acao_retorno=recibo_peticionamento_usuario_externo_listar&id_orgao_acesso_externo=0";
			$urlAssinada = SessaoSEIExterna::getInstance()->assinarLink( $url );
			
			//removendo atributos da sessao
			if( SessaoSEIExterna::getInstance()->isSetAtributo('docPrincipalConteudoHTML') ){
				SessaoSEIExterna::getInstance()->removerAtributo('docPrincipalConteudoHTML');
			}
			
			if( SessaoSEIExterna::getInstance()->isSetAtributo('arrIdAnexoPrincipal') ){
				SessaoSEIExterna::getInstance()->removerAtributo('arrIdAnexoPrincipal');
			}
			
			if( SessaoSEIExterna::getInstance()->isSetAtributo('arrIdAnexoEssencial') ){
				SessaoSEIExterna::getInstance()->removerAtributo('arrIdAnexoEssencial');
			}
			
			if( SessaoSEIExterna::getInstance()->isSetAtributo('arrIdAnexoComplementar') ){
				SessaoSEIExterna::getInstance()->removerAtributo('arrIdAnexoComplementar');
			}
			
			if( SessaoSEIExterna::getInstance()->isSetAtributo('idDocPrincipalGerado') ){
				SessaoSEIExterna::getInstance()->removerAtributo('idDocPrincipalGerado');
			}
									
			echo "<script>";
  			echo "window.opener.location = '" . $urlAssinada . "';";
  			echo " window.opener.focus();";
  			echo " window.close();";
  			echo "</script>";
  			die;
  		}
  		
  		break;
  		
    default:
      throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
  }

}catch(Exception $e){
	
	//removendo atributos da sessao
	if( SessaoSEIExterna::getInstance()->isSetAtributo('docPrincipalConteudoHTML') ){
		SessaoSEIExterna::getInstance()->removerAtributo('docPrincipalConteudoHTML');
	}
		
	if( SessaoSEIExterna::getInstance()->isSetAtributo('arrIdAnexoPrincipal') ){
		SessaoSEIExterna::getInstance()->removerAtributo('arrIdAnexoPrincipal');
	}
		
	if( SessaoSEIExterna::getInstance()->isSetAtributo('arrIdAnexoEssencial') ){
		SessaoSEIExterna::getInstance()->removerAtributo('arrIdAnexoEssencial');
	}
		
	if( SessaoSEIExterna::getInstance()->isSetAtributo('arrIdAnexoComplementar') ){
		SessaoSEIExterna::getInstance()->removerAtributo('arrIdAnexoComplementar');
	}
	
	if( SessaoSEIExterna::getInstance()->isSetAtributo('idDocPrincipalGerado') ){
		SessaoSEIExterna::getInstance()->removerAtributo('idDocPrincipalGerado');
	}
	
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
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');

$arrComandos = array();
$arrComandos[] = '<button type="button" accesskey="a" name="Assinar" value="Assinar" onclick="assinar()" class="infraButton"><span class="infraTeclaAtalho">A</span>ssinar</button>';
$arrComandos[] = '<button type="button" accesskey="c" name="btnFechar" value="Fechar" onclick="fecharJanela()" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
?> 
<form id="frmConcluir" method="post" onsubmit="return assinar();"  
      action="<?=PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?id_tipo_procedimento=' . $_GET['id_tipo_procedimento'] .'&acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">
<?
PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaSEIExterna::getInstance()->abrirAreaDados('auto');
?>
    
	<p>
	<label>A confirmação de sua senha de acesso iniciará o peticionamento e importa na aceitação dos termos e condições que regem o processo eletrônico, além do disposto no credenciamento prévio, e na assinatura dos documentos nato-digitais e declaração de que são autênticos os digitalizados, sendo responsável civil, penal e administrativamente pelo uso indevido. Ainda, são de sua exclusiva responsabilidade: a conformidade entre os dados informados e os documentos; a conservação dos originais em papel de documentos digitalizados até que decaia o direito de revisão dos atos praticados no processo, para que, caso solicitado, sejam apresentados para qualquer tipo de conferência; a realização por meio eletrônico de todos os atos e comunicações processuais com o próprio Usuário Externo ou, por seu intermédio, com a entidade porventura representada; a observância de que os atos processuais se consideram realizados no dia e hora do recebimento pelo SEI, considerando-se tempestivos os praticados até as 23h59min59s do último dia do prazo, considerado sempre o horário oficial de Brasília, independente do fuso horário em que se encontre; a consulta periódica ao SEI, a fim de verificar o recebimento de intimações eletrônicas.</label>
    </p>
    
    <p> 
    <label class="infraLabelObrigatorio">Usuário Externo:</label> <br/>
    <input type="text" name="loginUsuarioExterno" style="width:60%;"
           value="<?= SessaoSEIExterna::getInstance()->getStrNomeUsuarioExterno() ?> "
           readonly="readonly" 
           id="loginUsuarioExterno" class="infraText" autocomplete="off" />
    </p>
    
    <p> 
    <label class="infraLabelObrigatorio">Cargo/Função:</label><br/>
    <select id="selCargo" name="selCargo" class="infraSelect" style="width:60%;">
    <option value="">Selecione Cargo/Função</option>
    <? foreach( $arrObjCargoDTO as $cargo ){
    	
    	if( $_POST['selCargo'] != $cargo->getNumIdCargo() ){
    	   echo "<option value='" . $cargo->getNumIdCargo() . "'>";	
    	}
    	else{
    	  echo "<option selected='selected' value='" . $cargo->getNumIdCargo() . "'>";
    	}
    	
    	echo $cargo->getStrExpressao();
    	echo "</option>";
    	
    } ?>
    </select>
    </p>
    
    <p>
    <label class="infraLabelObrigatorio"> Senha de Acesso ao SEI: </label> <br/>
    <input type="password" name="senhaSEI" id="senhaSEI" class="infraText" autocomplete="off" style="width:60%;" />
    </p>
    
    <!--  Campos Hidden para preencher com valores da janela pai -->
    <input type="hidden" id="txtEspecificacaoDocPrincipal" name="txtEspecificacaoDocPrincipal" />
    <input type="hidden" id="nivelAcessoDocPrincipal" name="nivelAcessoDocPrincipal" />
    <input type="hidden" id="grauSigiloDocPrincipal" name="grauSigiloDocPrincipal" />
    <input type="hidden" id="hdnListaInteressados" name="hdnListaInteressados" />
    
    <input type="hidden" id="hipoteseLegalDocPrincipal" name="hipoteseLegalDocPrincipal" />
    <input type="hidden" id="hipoteseLegalDocEssencial" name="hipoteseLegalDocEssencial" />
    <input type="hidden" id="hipoteseLegalDocComplementar" name="hipoteseLegalDocComplementar" />
    
    <input type="hidden" id="id_tipo_procedimento" name="id_tipo_procedimento" value="<?= $_GET['id_tipo_procedimento'] ?>" />
    
    <!-- Listas de documentos principais (se for externo), essencial e complementar -->
    <input type="hidden" id="hdnDocPrincipal" name="hdnDocPrincipal" />
    <input type="hidden" id="hdnDocEssencial" name="hdnDocEssencial" />
    <input type="hidden" id="hdnDocComplementar" name="hdnDocComplementar" />
    
    <!-- Unidade selecionada via combo de UF -->
    <input type="hidden" id="hdnIdUnidadeMultiplaSelecionada" name="hdnIdUnidadeMultiplaSelecionada" />
    
    <input type="submit" name="btSubMit" value="Salvar" style="display:none;"  />
    
</form>

<? 
PaginaSEIExterna::getInstance()->fecharAreaDados();
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
?>
<script type="text/javascript">

function isValido(){

	var cargo = document.getElementById("selCargo").value;
	var senha = document.getElementById("senhaSEI").value;

	if( cargo == ""){
		alert('Favor informe o cargo.');
		document.getElementById("selCargo").focus();
		return false;
	} else if( senha == ""){
		alert('Favor informe a senha');
		document.getElementById("senhaSEI").focus();
		return false;
	} else {
		return true;
	}
	
}

function assinar(){
	
	if( isValido() ){

		var textoEspecificacao = window.opener.document.getElementById('txtEspecificacao').value;
		var nivelAcesso = window.opener.document.getElementById('nivelAcesso1').value;

		var campoHipLegal1 = window.opener.document.getElementById('hipoteseLegal1');
		var campoHipLegal2 = window.opener.document.getElementById('hipoteseLegal2');
		var campoHipLegal3 = window.opener.document.getElementById('hipoteseLegal3');

		var hipoteseLegal = null;
		var hipoteseLegal2 = null;
		var hipoteseLegal3 = null;
		
		
		if( campoHipLegal1 != null && campoHipLegal1 != undefined ){
		  hipoteseLegal = campoHipLegal1.value;
		}

		if( campoHipLegal2 != null && campoHipLegal2 != undefined ){
		  hipoteseLegal2 = window.opener.document.getElementById('hipoteseLegal2');
		}

		if( campoHipLegal3 != null && campoHipLegal3 != undefined ){
		  hipoteseLegal3 = window.opener.document.getElementById('hipoteseLegal3');
		}
		
		if( hipoteseLegal2 != null && hipoteseLegal2 != undefined ){
			document.getElementById('hipoteseLegalDocEssencial').value = hipoteseLegal2.value;
		}
		
		if( hipoteseLegal3 != null && hipoteseLegal3 != undefined ){
			document.getElementById('hipoteseLegalDocComplementar').value = hipoteseLegal3.value;
		}		
		
		document.getElementById('txtEspecificacaoDocPrincipal').value = textoEspecificacao;
		document.getElementById('nivelAcessoDocPrincipal').value = nivelAcesso;
		document.getElementById('grauSigiloDocPrincipal').value = nivelAcesso;
		document.getElementById('hipoteseLegalDocPrincipal').value = hipoteseLegal;

		//verificar se esta vindo uma lista de interessados
		var selInteressados = window.opener.document.getElementById('selInteressados');
		var selInteressadosSelecionadosTxt = '';
		//alert(selInteressadosSelecionados);
		
		//verificar se esta a combo de UF (Unidades multiplas)
		var selUFAberturaProcesso = window.opener.document.getElementById('selUFAberturaProcesso');

		if( selUFAberturaProcesso != null ) {
			document.getElementById('hdnIdUnidadeMultiplaSelecionada').value = selUFAberturaProcesso.value;			
		}

		// loop through options in select list
		if( selInteressados != null ) {

			for (var i=0, len=selInteressados.options.length; i<len; i++) {
	
		        opt = selInteressados.options[i];
	
			    // add to array of option elements to return from this function
			    if( selInteressadosSelecionadosTxt != ''){
			    	selInteressadosSelecionadosTxt += ','; 
				}
				
			    selInteressadosSelecionadosTxt += opt.value;
			        
			}

			//alert( selInteressadosSelecionadosTxt );
			document.getElementById('hdnListaInteressados').value = selInteressadosSelecionadosTxt;

		}

		//obtendo valores das grids de documentos principais, essenciais e complementares
		var hdnDocPrincipal = window.opener.document.getElementById('hdnDocPrincipal');
		var hdnDocEssencial = window.opener.document.getElementById('hdnDocEssencial');
		var hdnDocComplementar = window.opener.document.getElementById('hdnDocComplementar');
		
		if( hdnDocPrincipal != null && hdnDocPrincipal != undefined){
		  document.getElementById('hdnDocPrincipal').value = hdnDocPrincipal.value;
		}

		if( hdnDocEssencial != null && hdnDocEssencial != undefined){
		  document.getElementById('hdnDocEssencial').value = hdnDocEssencial.value;
		}

		if( hdnDocComplementar != null && hdnDocComplementar != undefined){
		  document.getElementById('hdnDocComplementar').value = hdnDocComplementar.value;
		}

		document.getElementById('frmConcluir').submit();
				
	} 
	
}

function callback(opt) {
	selInteressadosSelecionados + ', ';
}

//arguments: reference to select list, callback function (optional)
function getSelectedOptions(sel, fn) {

    var opts = [], opt;
    
    // loop through options in select list
    for (var i=0, len=sel.options.length; i<len; i++) {
        opt = sel.options[i];
        
        // check if selected
        if ( opt.selected ) {
            // add to array of option elements to return from this function
            opts.push(opt);
            
            // invoke optional callback function if provided
            if (fn) {
                fn(opt);
            }
        }
    }
    
    // return array containing references to selected option elements
    return opts;
}

function inicializar(){
    infraEfeitoTabelas();  
}

function fecharJanela(){
	
	if (window.opener != null && !window.opener.closed) {
        window.opener.focus();
    }

    window.close();
}

function OnSubmitForm() {
	return isValido();
}	 
</script>