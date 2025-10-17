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
  $strLinkAjaxVerificarSenha = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_validar_assinatura');

  //=====================================================
  //FIM - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
  //=====================================================
  switch($_GET['acao']){

  	case 'peticionamento_usuario_externo_assinar':

  		$objMdPetProcessoRN = new MdPetProcessoRN();
		$objMdPetProcessoRN->processarCadastro( $_POST );
  		break;

  	case 'peticionamento_usuario_externo_concluir':

		$objMdPetProcessoRN = new MdPetProcessoRN();
  		$strTitulo = 'Concluir Peticionamento - Assinatura Eletrônica';

		// Manipulacao para assinatura com Gov.br:
		$bolAssinaturaSso = (new InfraParametro(BancoSEI::getInstance()))->getValor('SEI_GOV_BR_EXTERNO_ASSINATURA', false, '0');
		$isNivelAlto = SessaoSEIExterna::getInstance()->getBolLoginSso() && !InfraString::isBolVazia(SessaoSEIExterna::getInstance()->getStrStaNivelConfiabilidadeSso()) && in_array(SessaoSEI::getInstance()->getStrStaNivelConfiabilidadeSso(), [InfraSip::$SSO_NIVEL_CONFIABILIDADE_OURO, InfraSip::$SSO_NIVEL_CONFIABILIDADE_PRATA]);

		if($bolAssinaturaSso){
			$agrupador = sha1(time());
			$objInfraSip = new InfraSip(SessaoSEIExterna::getInstance());
			$strUrlAssinaturaSso = $objInfraSip->obterUrlAssinaturaSso() . "?token=" . $agrupador . "&id_sistema=" . SessaoSEIExterna::getInstance()->getNumIdSistema() . "&id_login_sso=" . SessaoSEIExterna::getInstance()->getStrIdLoginSso();
			$strLinkVerificacaoAssinatura = SessaoSEIExterna::getInstance()->assinarLink( 'controlador_ajax_externo.php?acao_ajax=assinatura_verificar_confirmacao&agrupador=' . $agrupador );
		}

		if(MdPetUsuarioExternoRN::usuarioSsoSemSenha()){
			(new InfraException())->lancarValidacao("Você ainda não possui uma senha registrada no sistema.\nPara assinatura com senha acesse a opção Gerar Senha no menu.", InfraPagina::$TIPO_MSG_AVISO);
		}
		// Final da manipulacao para assinatura com Gov.br.

  		if( isset( $_POST['pwdsenhaSEI'] ) ){

  			//documento montado no editor rico do SEI
  			if( SessaoSEIExterna::getInstance()->isSetAtributo('docPrincipalConteudoHTML') ){
  				$_POST['docPrincipalConteudoHTML'] = SessaoSEIExterna::getInstance()->getAtributo('docPrincipalConteudoHTML');
  			}

  			//obtendo a unidade de abertura do processo
  			$idTipoProc = $_POST['id_tipo_procedimento'];


  			//obtendo a unidade do tipo de processo selecionado - Pac 10 - pode ser uma ou MULTIPLAS unidades selecionadas
  			$objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
  			$objMdPetRelTpProcessoUnidDTO->retTodos();
  			$objMdPetRelTpProcessoUnidRN = new MdPetRelTpProcessoUnidRN();
  			$objMdPetRelTpProcessoUnidDTO->setNumIdTipoProcessoPeticionamento( $idTipoProc );
  			$arrMdPetRelTpProcessoUnidDTO = $objMdPetRelTpProcessoUnidRN->listar( $objMdPetRelTpProcessoUnidDTO );

  			$arrUnidadeUFDTO = null;
  			$idUnidadeTipoProcesso = null;

  			//==============================================================
  			//UNIDADES MULTIPLAS - Pegar unidade selecionada na combo de UF
  			//==============================================================
  			if( $arrMdPetRelTpProcessoUnidDTO != null && count( $arrMdPetRelTpProcessoUnidDTO ) > 1 ) {
				$idUnidadeTipoProcesso = $arrMdPetRelTpProcessoUnidDTO[0]->getNumIdUnidade();
			}

  			$arrParam = array();
  			$arrParam['pwdsenhaSEI'] = $_POST['pwdsenhaSEI'];
			$objMdPetProcessoRN->validarSenha( $arrParam );
            $params['pwdsenhaSEI'] = '***********';
            $_POST['pwdsenhaSEI'] = '***********';
			$arrDadosProcessoComRecibo = $objMdPetProcessoRN->gerarProcedimento( $_POST );
			$idRecibo = $arrDadosProcessoComRecibo[0]->getNumIdReciboPeticionamento();

			//realizar classificacao da metas ODS - IA
			if( PeticionamentoIntegracao::verificaSeModIAVersaoMinima() && PeticionamentoIntegracao::permitirClassificacaoODSUsuarioExterno()){
                PeticionamentoIntegracao::classificarMetaOds($arrDadosProcessoComRecibo[1]->getDblIdProcedimento());
            }

			// Temporários apagando
			$arquivos_enviados = array();
			if( isset( $_POST['hdnDocPrincipal'] ) ){
				$arquivos_enviados = array_merge ($arquivos_enviados, PaginaSEIExterna::getInstance()->getArrItensTabelaDinamica($_POST['hdnDocPrincipal']));
			}
			if( isset( $_POST['hdnDocEssencial'] ) ){
				$arquivos_enviados = array_merge ($arquivos_enviados, PaginaSEIExterna::getInstance()->getArrItensTabelaDinamica($_POST['hdnDocEssencial']));
			}
			if( isset( $_POST['hdnDocComplementar'] ) ){
				$arquivos_enviados = array_merge ($arquivos_enviados, PaginaSEIExterna::getInstance()->getArrItensTabelaDinamica($_POST['hdnDocComplementar']));
			}

			foreach ($arquivos_enviados as $arquivo_enviado) {
				unlink(DIR_SEI_TEMP.'/'.$arquivo_enviado[8]);
			}

			//executar javascript para fechar janela filha e redirecionar janela pai para a tela de detalhes do recibo que foi gerado
			$url = "controlador_externo.php?id_md_pet_rel_recibo_protoc=" . $idRecibo ."&acao=md_pet_usu_ext_recibo_listar&acao_origem=md_pet_usu_ext_recibo_consultar";

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
?>
<form id="frmConcluir" method="post" onsubmit="return assinar();"
      action="<?=PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?id_tipo_procedimento=' . $_GET['id_tipo_procedimento'] .'&acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">
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
    <div class="row mt-3">
        <div class="col-12 col-sm-10 col-md-8 col-lg-8 col-xl-8">
            <div class="form-group">
                <label class="infraLabelObrigatorio">Cargo/Função:</label>
                <select id="selCargo" name="selCargo" class="infraSelect form-select">
                    <option value="">Selecione Cargo/Função</option>
                    <? foreach ($arrObjCargoDTO as $expressao => $cargo): ?>
                    <option value="<?= $cargo ?>" <?= $_POST['selCargo'] == $cargo ? 'selected="selected"' : '' ?>><?= $expressao ?></option>
                    <? endforeach ?>
                </select>
            </div>
        </div>
    </div>
    <div class="row my-3">

		<div class="col-6 col-sm-5 col-md-3 col-lg-3 col-xl-3">
			<div class="form-group">
				<label class="infraLabelObrigatorio"><span class="infraTeclaAtalho">S</span>enha:</label>
				<input type="password" name="pwdsenhaSEI" id="pwdsenhaSEI" class="infraText form-control" autocomplete="off"/>
			</div>
		</div>

		<?php if($bolAssinaturaSso && 1==2): ?>
			<div class="col-6 col-sm-4 col-md-4 col-lg-4 col-xl-4">
				<label id="lblOu" class="infraLabelOpcional" tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>">ou</label>&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="#" id="ancAssinaturaSso" class="d-inline-block mt-3" onclick="assinarSso();" tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>">
					<img id="imgAssinarGovBr" src="imagens/assinatura-gov-br.png" title="Assinar com gov.br" style="width:150px" />
				</a>&nbsp;
			</div>
		<?php endif; ?>
				
	</div>

    <!--  Campos Hidden para preencher com valores da janela pai -->
    <input type="hidden" id="txtEspecificacaoDocPrincipal" name="txtEspecificacaoDocPrincipal" />
    <input type="hidden" id="nivelAcessoDocPrincipal" name="nivelAcessoDocPrincipal" />
    <input type="hidden" id="grauSigiloDocPrincipal" name="grauSigiloDocPrincipal" />
    <input type="hidden" id="hdnListaInteressados" name="hdnListaInteressados" />
    <input type="hidden" id="hdnListaInteressadosIndicados" name="hdnListaInteressadosIndicados" />

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
	var senha = document.getElementById("pwdsenhaSEI").value;

	if( cargo == ""){
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

	if( isValido() ){

		var textoEspecificacao = window.parent.document.getElementById('txtEspecificacao').value;
		var nivelAcesso = window.parent.document.getElementById('nivelAcesso1').value;

		var campoHipLegal1 = window.parent.document.getElementById('hipoteseLegal1');
		var campoHipLegal2 = window.parent.document.getElementById('hipoteseLegal2');
		var campoHipLegal3 = window.parent.document.getElementById('hipoteseLegal3');

		var hipoteseLegal = null;
		var hipoteseLegal2 = null;
		var hipoteseLegal3 = null;

		if( campoHipLegal1 != null && campoHipLegal1 != undefined ){
		  hipoteseLegal = campoHipLegal1.value;
		}

		if( campoHipLegal2 != null && campoHipLegal2 != undefined ){
		  hipoteseLegal2 = window.parent.document.getElementById('hipoteseLegal2');
		}

		if( campoHipLegal3 != null && campoHipLegal3 != undefined ){
		  hipoteseLegal3 = window.parent.document.getElementById('hipoteseLegal3');
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
		var selInteressados = window.parent.document.getElementById('selInteressados');
		var hdnSelInteressadosIndicados = window.parent.document.getElementById('hdnListaInteressadosIndicados');
		var selInteressadosSelecionadosTxt = '';

		//verificar se esta a combo de UF (Unidades multiplas)
		var selUFAberturaProcesso = window.parent.document.getElementById('selUFAberturaProcesso');

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

			document.getElementById('hdnListaInteressados').value = selInteressadosSelecionadosTxt;

		}

		//lista de interessados indicados por CPF/CNPJ
		if( hdnSelInteressadosIndicados != null && hdnSelInteressadosIndicados != "") {

			var hdnListaInteressadosIndicados = hdnSelInteressadosIndicados.value;

			//caractere de quebra de linha
			var arrHash = hdnListaInteressadosIndicados.split('¥');
		    var quantidadeRegistro = arrHash.length;

			if( quantidadeRegistro == 0){

				//caractere de quebra de coluna
				var arrLocal = hdnListaInteressadosIndicados.split('±');
				var idContato = arrLocal[0];

				if( selInteressadosSelecionadosTxt != ''){
			    	selInteressadosSelecionadosTxt += ',';
				}

			    selInteressadosSelecionadosTxt += idContato;

			} else if( quantidadeRegistro > 0 ){

				for(var i = 0; i < quantidadeRegistro ; i++ ){

					var arrLocal = arrHash[i].split('±');
					var idContato = arrLocal[0];

					if( selInteressadosSelecionadosTxt != ''){
				    	selInteressadosSelecionadosTxt += ',';
					}

				    selInteressadosSelecionadosTxt += idContato;

				}

			}

			document.getElementById('hdnListaInteressados').value = selInteressadosSelecionadosTxt;

		}

		//obtendo valores das grids de documentos principais, essenciais e complementares
		var hdnDocPrincipal = window.parent.document.getElementById('hdnDocPrincipal');
		var hdnDocEssencial = window.parent.document.getElementById('hdnDocEssencial');
		var hdnDocComplementar = window.parent.document.getElementById('hdnDocComplementar');

		if( hdnDocPrincipal != null && hdnDocPrincipal != undefined){
		  document.getElementById('hdnDocPrincipal').value = hdnDocPrincipal.value;
		}

		if( hdnDocEssencial != null && hdnDocEssencial != undefined){
		  document.getElementById('hdnDocEssencial').value = hdnDocEssencial.value;
		}

		if( hdnDocComplementar != null && hdnDocComplementar != undefined){
		  document.getElementById('hdnDocComplementar').value = hdnDocComplementar.value;
		}

		processando();

		document.getElementById('frmConcluir').submit();

		return true;

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
}

function fecharJanela(){
	if (window.parent != null && !window.parent.closed) {
        window.parent.focus();
    }

    window.close();
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
