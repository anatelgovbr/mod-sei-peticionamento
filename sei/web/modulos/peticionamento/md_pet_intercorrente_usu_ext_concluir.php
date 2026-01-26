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
  $strLinkAjaxVerificarSenha = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_validar_assinatura');

  switch($_GET['acao']){

  	case 'md_pet_intercorrente_usu_ext_assinar':
        break;

  	case 'md_pet_responder_intimacao_usu_ext_assinar':
  		break;

    //INICIO - finalizacao do resposta a intimacao
  	case 'md_pet_responder_intimacao_usu_ext_concluir':

  		$_POST['isRespostaIntimacao'] = true;
  		$objMdPetIntercorrenteProcessoRN = new MdPetIntercorrenteProcessoRN();
  		$resultado = $objMdPetIntercorrenteProcessoRN->cadastrar($_POST);

        //realizar classificacao da metas ODS - IA
        if( PeticionamentoIntegracao::verificaSeModIAVersaoMinima() && PeticionamentoIntegracao::permitirClassificacaoODSUsuarioExterno()){
            PeticionamentoIntegracao::classificarMetaOds($_POST['id_procedimento']);
        }

  		if ($resultado===false){
  			echo 'Erro ao cadastrar';
  		}
  		else{

  			$objReciboDTO = $resultado['recibo'];
  			$documentoDTO = $resultado['documento'];

  			$url = "controlador_externo.php?id_md_pet_rel_recibo_protoc=" . $objReciboDTO->getNumIdReciboPeticionamento() ."&acao=md_pet_usu_ext_recibo_listar&acao_origem=md_pet_usu_ext_recibo_consultar&id_orgao_acesso_externo=0";

  			$urlAssinada = SessaoSEIExterna::getInstance()->assinarLink($url);

  			echo "<script>";
  			echo "window.parent.location = '" . $urlAssinada . "';";
  			echo " window.parent.focus();";
  			echo " window.close();";
  			echo "</script>";
  			die;
  		}

  		break;
  	//FIM - finalizacao do resposta a intimacao

    //INICIO - finalizacao do peticionamento intercorrente
  	case 'md_pet_intercorrente_usu_ext_concluir':
		$_POST['isRespostaIntercorrente'] = true;
  		$objMdPetIntercorrenteProcessoRN = new MdPetIntercorrenteProcessoRN();
        $resultado = $objMdPetIntercorrenteProcessoRN->cadastrar($_POST);

        //realizar classificacao da metas ODS - IA
        if( PeticionamentoIntegracao::verificaSeModIAVersaoMinima() && PeticionamentoIntegracao::permitirClassificacaoODSUsuarioExterno()){
            PeticionamentoIntegracao::classificarMetaOds($_POST['id_procedimento']);
        }

        if ($resultado===false){
        	echo 'Erro ao cadastrar';
        }else{

        	$objReciboDTO = $resultado['recibo'];
	        $documentoDTO = $resultado['documento'];

			$url = "controlador_externo.php?id_md_pet_rel_recibo_protoc=" . $objReciboDTO->getNumIdReciboPeticionamento() . "&id_documento=" . $documentoDTO->getDblIdDocumento() . "&acao=md_pet_usu_ext_recibo_listar&acao_origem=md_pet_usu_ext_recibo_listar&acao_retorno=md_pet_usu_ext_recibo_listar&id_orgao_acesso_externo=0";


	        $urlAssinada = SessaoSEIExterna::getInstance()->assinarLink($url);

	        echo "<script>";
	        echo "window.parent.location = '" . $urlAssinada . "';";
	        echo " window.parent.focus();";
	        echo " window.close();";
	        echo "</script>";
	        die;
        }

	    break;

    default:
      throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
  }

}catch(Exception $e){

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

$arrComandos[] = '<button tabindex="-1" type="button" accesskey="c" name="btnFechar" value="Fechar" onclick="infraFecharJanelaModal()" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

//url para assinar o intercorrente
$urlAssinada = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_intercorrente_usu_ext_concluir&id_procedimento='.$_REQUEST['id_procedimento'].'&id_tipo_procedimento='.$_REQUEST['id_tipo_procedimento'].'&acao_origem='.$_GET['acao']);

//url para assinar o resposta
$urlAssinadaRespostaIntimacao = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_responder_intimacao_usu_ext_concluir&acao_origem='.$_GET['acao']);

$urlFormAssinar = "";

//intercorrente
if( $_GET['acao'] == "md_pet_intercorrente_usu_ext_assinar"){
	$urlFormAssinar = $urlAssinada;
}

//resposta a intimacao
else if( $_GET['acao'] == "md_pet_responder_intimacao_usu_ext_assinar" ) {
	$urlFormAssinar = $urlAssinadaRespostaIntimacao;
}

?>
<form id="frmConcluir" method="post" onsubmit="return assinar();" action="<?=PaginaSEIExterna::getInstance()->formatarXHTML( $urlFormAssinar )?>">
<?
PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaSEIExterna::getInstance()->abrirAreaDados('auto');
?>
    <div class="row">
        <div class="col-12">
            <p class="text-justify">A confirmação de sua senha importa na aceitação dos termos e condições que regem o processo eletrônico, além do disposto no credenciamento prévio, e na assinatura dos documentos nato-digitais e declaração de que são autênticos os digitalizados, sendo responsável civil, penal e administrativamente pelo uso indevido. Ainda, são de sua exclusiva responsabilidade: a conformidade entre os dados informados e os documentos; a conservação dos originais em papel de documentos digitalizados até que decaia o direito de revisão dos atos praticados no processo, para que, caso solicitado, sejam apresentados para qualquer tipo de conferência; a realização por meio eletrônico de todos os atos e comunicações processuais com o próprio Usuário Externo ou, por seu intermédio, com a entidade porventura representada; a observância de que os atos processuais se consideram realizados no dia e hora do recebimento pelo SEI, considerando-se tempestivos os praticados até as 23h59min59s do último dia do prazo, considerado sempre o horário oficial de Brasília, independente do fuso horário em que se encontre; a consulta periódica ao SEI, a fim de verificar o recebimento de intimações eletrônicas.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-sm-10 col-md-8 col-lg-8 col-xl-8">
            <div class="form-group">
                <label class="infraLabelObrigatorio">Usuário Externo:</label>
                <input type="text" name="loginUsuarioExterno"
                    value="<?= PaginaSEIExterna::tratarHTML(SessaoSEIExterna::getInstance()->getStrNomeUsuarioExterno()) ?>"
                    readonly="readonly" id="loginUsuarioExterno" class="infraText form-control" autocomplete="off" disabled />
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-sm-10 col-md-8 col-lg-8 col-xl-8">
            <div class="form-group">
                <label class="infraLabelObrigatorio">Cargo/Função:</label>
                <select id="selCargo" name="selCargo" class="infraSelect form-control">
                    <option value="">Selecione Cargo/Função</option>
                    <? foreach ($arrObjCargoDTO as $expressao => $cargo): ?>
                    <option value="<?= $cargo ?>" <?= $_POST['selCargo'] == $cargo ? 'selected="selected"' : '' ?>><?= $expressao ?></option>
                    <? endforeach ?>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6 col-sm-5 col-md-6 col-lg-6 col-xl-6">
            <div class="form-group">
                <label class="infraLabelObrigatorio">Senha de Acesso ao SEI:</label>
                <input type="password" name="pwdsenhaSEI" id="pwdsenhaSEI" class="infraText form-control" autocomplete="off"/>
            </div>
        </div>
    </div>
    <input type="hidden" id="id_tipo_procedimento" name="id_tipo_procedimento" value="<?= $_REQUEST['id_tipo_procedimento'] ?>" />
    <input type="hidden" id="id_procedimento" name="id_procedimento" value="<?= $_REQUEST['id_procedimento'] ?>" />

    <?php
    //campos hidden especificos de uso do resposta a intimacao
    if( $_GET['acao'] == "md_pet_responder_intimacao_usu_ext_assinar" || $_GET['acao']  == "md_pet_responder_intimacao_usu_ext_concluir") { ?>

    <input type="hidden" id="id_intimacao" name="id_intimacao" value="" />
    <input type="hidden" id="id_aceite" name="id_aceite" value="" />
    <input type="hidden" id="id_tipo_resposta" name="id_tipo_resposta" value="" />
    <input type="hidden" id="id_int_rel_dest" name="id_int_rel_dest" value="" />
    <input type="hidden" id="id_contato" name="id_contato" value="" />
	<?php } ?>

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

function validacoesArquivosUpload() {

    var arquivoErro = "";
    
    var tbDocumento = window.parent.document.getElementById('tbDocumento');

    if (tbDocumento) {
        for (var i = 1; i < tbDocumento.rows.length; i++) {
            var nomeArquivo = tbDocumento.rows[i].cells[9].innerText;
            if (nomeArquivo.indexOf('#') !== -1 || nomeArquivo.indexOf('&') !== -1){
                arquivoErro += "O nome do arquivo '" + nomeArquivo + "' possui caracteres especiais.\n";
                break;
            } else if (nomeArquivo.length > 255) {
                arquivoErro += "O nome do arquivo '" + nomeArquivo + "' possui tamanho superior a 255 caracteres.\n";
                break;
            }

        }
    }

    return arquivoErro;
}
function isValido(){

	var cargo = document.getElementById("selCargo").value;
	var senha = document.getElementById("pwdsenhaSEI").value;

	var arquivoErro = validacoesArquivosUpload();
  
  if (arquivoErro != "") {
      alert(arquivoErro);
      parent.infraFecharJanelaModal();
      return false;
  } else if( cargo == ""){
		alert('Favor informe o Cargo/Função.');
		document.getElementById("selCargo").focus();
		return false;
	} else if( senha == ""){
		alert('Favor informe a Senha.');
		document.getElementById("pwdsenhaSEI").focus();
		return false;
	} else {
        $.ajax({
            async: false,
            type: "POST",
            url: "<?= $strLinkAjaxVerificarSenha ?>",
            dataType: "json",
            data: {
                strSenha: btoa(senha)
            },
            success: function (result) {
                var strRetorno = result.responseText;
                var retorno = strRetorno.split('"?>\n');
                document.getElementById("pwdsenhaSEI").value = retorno[1];
            },
            error: function (msgError) {},
            complete: function (result) {
                var strRetorno = result.responseText;
                var retorno = strRetorno.split('"?>\n');
                document.getElementById("pwdsenhaSEI").value = retorno[1];
            }
        });
        return true;
	}

}

function assinar(){

	if( isValido() ) {

        //setando valor de campos hidden para o resposta a intimacao
		<?php if( $_GET['acao'] == "md_pet_responder_intimacao_usu_ext_assinar" || $_GET['acao']  == "md_pet_responder_intimacao_usu_ext_concluir") { ?>

		//pegando valores dos campos que ja existem na janela pai
		var hdnIdProcedimentoJanelaPai = window.parent.document.getElementById('hdnIdProcedimento');
		var hdnIdMdPetIntimacaoJanelaPai = window.parent.document.getElementById('hdnIdMdPetIntimacao');
		var hdnIdMdPetIntAceiteJanelaPai = window.parent.document.getElementById('hdnIdMdPetIntAceite');
		var hdnIdTipoProcedimentoJanelaPai  = window.parent.document.getElementById('hdnIdTipoProcedimento');
		var selTipoResposta = window.parent.document.getElementById('selTipoResposta');
        var selIntRelDest = window.parent.document.getElementById('hdnIdMdPetIntRelDest');
        var selIdContato = window.parent.document.getElementById('hdnIdContato');
		//setando nos campos hidden da janela local do assinar antes de submeter o formulario
		document.getElementById('id_procedimento').value = hdnIdProcedimentoJanelaPai.value;
		document.getElementById('id_tipo_procedimento').value = hdnIdTipoProcedimentoJanelaPai.value;
		document.getElementById('id_aceite').value = hdnIdMdPetIntAceiteJanelaPai.value;
		document.getElementById('id_intimacao').value = hdnIdMdPetIntimacaoJanelaPai.value;
        document.getElementById('id_tipo_resposta').value = selTipoResposta.value;
        document.getElementById('id_contato').value = selIdContato.value;
		document.getElementById('id_int_rel_dest').value = selIntRelDest.value;
		<?php } ?>

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
    if (document.getElementById('selCargo')!=null){
        document.getElementById('selCargo').focus();
    }
}

function fecharJanela(){
    if (window.parent != null && !window.parent.closed) {
        window.parent.focus();
    }

    window.close();
}

function OnSubmitForm() {
	return isValido();
}

function carregarTabelaDocumento() {
    var hdnTbDocumento = document.createElement('input');
    var frm = document.getElementById('frmConcluir');
    var hdnTbDocumentoPai = window.parent.document.getElementById('hdnTbDocumento');

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
