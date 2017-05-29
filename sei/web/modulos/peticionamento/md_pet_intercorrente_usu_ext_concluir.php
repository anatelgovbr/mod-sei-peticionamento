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
  $objMdPetCargoRN = new MdPetCargoRN();
  $arrObjCargoDTO = $objMdPetCargoRN->listarDistintos();  

  //=====================================================
  //FIM - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
  //=====================================================

  $strTitulo = 'Concluir Peticionamento - Assinatura Eletrônica';

  switch($_GET['acao']){
    
  	case 'md_pet_intercorrente_usu_ext_assinar':
        break;
  	case 'md_pet_intercorrente_usu_ext_concluir':
        $objMdPetIntercorrenteProcessoRN = new MdPetIntercorrenteProcessoRN();
        $resultado = $objMdPetIntercorrenteProcessoRN->cadastrar($_POST);
        if ($resultado===false){
        	//PaginaSEIExterna::getInstance()->processarExcecao('Erro ao cadastrar');
        	echo 'Erro ao cadastrar';
        }else{
	        $objReciboDTO = $resultado['recibo'];
	        $documentoDTO = $resultado['documento'];

	        $url = "controlador_externo.php?id_md_pet_rel_recibo_protoc=" . $objReciboDTO->getNumIdReciboPeticionamento() . "&id_documento=" . $documentoDTO->getDblIdDocumento() . "&acao=md_pet_intercorrente_usu_ext_recibo_consultar&acao_origem=md_pet_usu_ext_recibo_listar&acao_retorno=md_pet_usu_ext_recibo_listar&id_orgao_acesso_externo=0";
	        $urlAssinada = SessaoSEIExterna::getInstance()->assinarLink($url);

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
	//if( SessaoSEIExterna::getInstance()->isSetAtributo('docPrincipalConteudoHTML') ){
		//SessaoSEIExterna::getInstance()->removerAtributo('docPrincipalConteudoHTML');
	//}
		
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
$arrComandos[] = '<button tabindex="-1" type="button" accesskey="a" name="Assinar" value="Assinar" onclick="assinar()" class="infraButton"><span class="infraTeclaAtalho">A</span>ssinar</button>';
$arrComandos[] = '<button tabindex="-1" type="button" accesskey="c" name="btnFechar" value="Fechar" onclick="fecharJanela()" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
$urlAssinada = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_intercorrente_usu_ext_concluir&id_procedimento='.$_REQUEST['id_procedimento'].'&id_tipo_procedimento='.$_REQUEST['id_tipo_procedimento'].'&acao_origem='.$_GET['acao']);
?>
<form id="frmConcluir" method="post" onsubmit="return assinar();" action="<?=PaginaSEIExterna::getInstance()->formatarXHTML($urlAssinada)?>">
<?
PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaSEIExterna::getInstance()->abrirAreaDados('auto');
?>
    
    <p>
    <label>A confirmação de sua senha de acesso iniciará o peticionamento e importa na aceitação dos termos e condições que regem o processo eletrônico, além do disposto no credenciamento prévio, e na assinatura dos documentos nato-digitais e declaração de que são autênticos os digitalizados, sendo responsável civil, penal e administrativamente pelo uso indevido. Ainda, são de sua exclusiva responsabilidade: a conformidade entre os dados informados e os documentos; a conservação dos originais em papel de documentos digitalizados até que decaia o direito de revisão dos atos praticados no processo, para que, caso solicitado, sejam apresentados para qualquer tipo de conferência; a realização por meio eletrônico de todos os atos e comunicações processuais com o próprio Usuário Externo ou, por seu intermédio, com a entidade porventura representada; a observância de que os atos processuais se consideram realizados no dia e hora do recebimento pelo SEI, considerando-se tempestivos os praticados até as 23h59min59s do último dia do prazo, considerado sempre o horário oficial de Brasília, independente do fuso horário em que se encontre; a consulta periódica ao SEI, a fim de verificar o recebimento de intimações eletrônicas.</label>
    </p>
    
    <p>
    <label class="infraLabelObrigatorio">Usuário Externo:</label> <br/>
    <input type="text" name="loginUsuarioExterno" style="width: 60%;" value="<?= PaginaSEIExterna::tratarHTML( SessaoSEIExterna::getInstance()->getStrNomeUsuarioExterno() ) ?>" readonly="readonly" id="loginUsuarioExterno" class="infraText" autocomplete="off" disabled/>
    </p>
    
    <p>
    <label class="infraLabelObrigatorio">Cargo/Função:</label> <br/>
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
    <label class="infraLabelObrigatorio">Senha de Acesso ao SEI:</label> <br/>
    <input type="password" name="senhaSEI" id="senhaSEI" class="infraText" autocomplete="off" style="width: 60%;" />
    </p>

    <input type="hidden" id="id_tipo_procedimento" name="id_tipo_procedimento" value="<?= $_REQUEST['id_tipo_procedimento'] ?>" />
    <input type="hidden" id="id_procedimento" name="id_procedimento" value="<?= $_REQUEST['id_procedimento'] ?>" />
    <input type="hidden" id="hdnSubmit" name="hdnSubmit" value=""/>
    
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
		alert('Favor informe o Cargo/Função.');
		document.getElementById("selCargo").focus();
		return false;
	} else if( senha == ""){
		alert('Favor informe a Senha.');
		document.getElementById("senhaSEI").focus();
		return false;
	} else {
		return true;
	}
	
}

function assinar(){
	if( isValido() ) {
        document.getElementById('hdnSubmit').value = '1';
        processando();
		document.getElementById('frmConcluir').submit();
	}
	return false;
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
    //Carrega os valores da tabela documento da tela pai.
    carregarTabelaDocumento();
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

function carregarTabelaDocumento() {
    var hdnTbDocumento = document.createElement('input');
    var frm = document.getElementById('frmConcluir');
    var hdnTbDocumentoPai = window.opener.document.getElementById('hdnTbDocumento');

    hdnTbDocumento.type = 'hidden';
    hdnTbDocumento.id = 'hdnTbDocumento';
    hdnTbDocumento.name = 'hdnTbDocumento';
    hdnTbDocumento.value = hdnTbDocumentoPai.value;
    frm.appendChild(hdnTbDocumento);

}

function exibirBotaoCancelarAviso(){

	var div = document.getElementById('divInfraAvisoFundo');

	if (div!=null && div.style.visibility == 'visible'){

		var botaoCancelar = document.getElementById('btnInfraAvisoCancelar');

		if (botaoCancelar != null){
			botaoCancelar.style.display = 'block';
		}
	}
}

function exibirAvisoEditor(){

  var divFundo = document.getElementById('divInfraAvisoFundo');

  if (divFundo==null){
    divFundo = infraAviso(false, 'Processando...');
  }else{
    document.getElementById('btnInfraAvisoCancelar').style.display = 'none';
    document.getElementById('imgInfraAviso').src='/infra_css/imagens/aguarde.gif';
  }

  if (INFRA_IE==0 || INFRA_IE>=7){
    divFundo.style.position = 'fixed';
  }

  var divAviso = document.getElementById('divInfraAviso');

  divAviso.style.top = Math.floor(infraClientHeight()/3) + 'px';
  divAviso.style.left = Math.floor((infraClientWidth()-200)/2) + 'px';
  divAviso.style.width = '200px';
  divAviso.style.border = '1px solid black';

  divFundo.style.width = screen.width*2 + 'px';
  divFundo.style.height = screen.height*2 + 'px';
  divFundo.style.visibility = 'visible';

}

function processando() {

	exibirAvisoEditor();
	timeoutExibirBotao = self.setTimeout('exibirBotaoCancelarAviso()',30000);

	if (INFRA_IE>0) {
	  window.tempoInicio=(new Date()).getTime();
	} else {
	  console.time('s'); 
	}	

}
</script>