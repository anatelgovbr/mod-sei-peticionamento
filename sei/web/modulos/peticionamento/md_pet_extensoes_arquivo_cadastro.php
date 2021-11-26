<?
/*
 * @author Alan Campos <alan.campos@castgroup.com.br>
 * 
 * */

try {
	require_once dirname(__FILE__).'/../../SEI.php';

	session_start();
	
	SessaoSei::getInstance()->validarLink();
	SessaoSei::getInstance()->validarPermissao($_GET['acao']);
	
	}catch(Exception $e){
		PaginaSEI::getInstance()->processarExcecao($e);
	}
	
	$objMdPetExtensoesArquivoDTO = new MdPetExtensoesArquivoDTO();
	
if($_POST) {
	
	$objMdPetExtensoesArquivoRN = new MdPetExtensoesArquivoRN();
	$objInfraException = new InfraException();
	
	
	// excluindo registros anteriores
	$objMdPetExtensoesArquivoDTO->retTodos();
	$objMdPetExtensoesArquivoRN = new MdPetExtensoesArquivoRN();
	$objMdPetExtensoesArquivoRN->excluir($objMdPetExtensoesArquivoRN->listar($objMdPetExtensoesArquivoDTO));

	$arrPrincipal = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnPrincipal']);
	$arrComplementar = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnComplementar']);
	
	if(!$arrPrincipal) {
		$objInfraException->adicionarValidacao('Informe pelo menos uma extens�o para documento principal.');
	}
	
	if(!$arrComplementar) {
		$objInfraException->adicionarValidacao('Informe pelo menos uma extens�o para documento complementar.');
	}
	
	$objInfraException->lancarValidacoes();

	$arrObjMdPetExtensoesArquivoDTO = array();

	foreach($arrPrincipal as $numPrincipal){
        // criando os novos
        $objMdPetExtensoesArquivoDTO = new MdPetExtensoesArquivoDTO();
        $objMdPetExtensoesArquivoDTO->setStrSinAtivo('S');
		$objMdPetExtensoesArquivoDTO->setNumIdArquivoExtensao($numPrincipal);
		$objMdPetExtensoesArquivoDTO->setStrSinPrincipal('S');
		array_push($arrObjMdPetExtensoesArquivoDTO, $objMdPetExtensoesArquivoDTO);
	}
	
	foreach($arrComplementar as $numComplementar){
        $objMdPetExtensoesArquivoDTO = new MdPetExtensoesArquivoDTO();
        $objMdPetExtensoesArquivoDTO->setStrSinAtivo('S');
        $objMdPetExtensoesArquivoDTO->setNumIdArquivoExtensao($numComplementar);
        $objMdPetExtensoesArquivoDTO->setStrSinPrincipal('N');
        array_push($arrObjMdPetExtensoesArquivoDTO, $objMdPetExtensoesArquivoDTO);
	}

    $objMdPetExtensoesArquivoDTO = $objMdPetExtensoesArquivoRN->cadastrar($arrObjMdPetExtensoesArquivoDTO);
}

$strSelExtensoesPrin = MdPetExtensoesArquivoINT::montarSelectExtensoes(null,null,null,'S');
$strSelExtensoesComp = MdPetExtensoesArquivoINT::montarSelectExtensoes(null,null,null,'N');


$strTitulo = "Peticionamento - Extens�es de Arquivos Permitidas";

$arrComandos[] = '<button type="submit" accesskey="s" name="sbmCadastrarGrupoUnidade" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
$arrComandos[] = '<button type="button" accesskey="c" name="btnFechar" id="btnFechar" value="Fechar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSei::getInstance()->assinarLink('controlador.php?acao=procedimento_controlar&acao_origem='.$_GET['acao'])).'\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
	
PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: '.PaginaSEI::getInstance()->getStrNomeSistema().' - '.$strTitulo.' ::');
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
?>

  #lblPrincipal {position:absolute;left:0%;top:0%;width:70.5%;}
  #txtPrincipal{position:absolute;left:0%;top:4%;width:70.5%;}
  #selPrincipal {position:absolute;left:0%;top:9%;width:81%;}
  #imgLupaPrincipal {position:absolute;left:81.5%;top:9%;}
  #imgExcluirPrincipal {position:absolute;left:81.3%;top:13.5%;}
  
  #lblComplementar {position:absolute;left:0%;top:40%;width:70.5%;}
  #txtComplementar{position:absolute;left:0%;top:44%;width:70.5%;}
  #selComplementar {position:absolute;left:0%;top:49%;width:81%;}
  #imgLupaComplementar {position:absolute;left:81.5%;top:50%;}
  #imgExcluirComplementar {position:absolute;left:81.3%;top:54.5%;}

<?
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();

$strLinkAjaxPrincipal = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_arquivo_extensao_listar_todos');
$strLinkPrincipalSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_arquivo_extensao_selecionar&tipo_selecao=2&id_object=objLupaPrincipal');
$strLinkComplementarSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_arquivo_extensao_selecionar&tipo_selecao=2&id_object=objLupaComplementar');
?>

var objLupaPrincipal = null;
var objAutoCompletarPrincipal = null;
var objLupaComplementar= null;
var objAutoCompletarComplementar = null;

  function inicializar(){
     objLupaPrincipal = new infraLupaSelect('selPrincipal','hdnPrincipal','<?=$strLinkPrincipalSelecao?>');
     objLupaComplementar = new infraLupaSelect('selComplementar','hdnComplementar','<?=$strLinkComplementarSelecao?>');

    objAutoCompletarPrincipal = new infraAjaxAutoCompletar('hdnIdPrincipal','txtPrincipal','<?=$strLinkAjaxPrincipal?>');
    objAutoCompletarPrincipal.limparCampo = true;
    
    objAutoCompletarComplementar = new infraAjaxAutoCompletar('hdnIdComplementar','txtComplementar','<?=$strLinkAjaxPrincipal?>');
    objAutoCompletarComplementar.limparCampo = true;
    objAutoCompletarComplementar.tamanhoMinimo = 3;
    objAutoCompletarPrincipal.tamanhoMinimo = 3;
    objAutoCompletarPrincipal.prepararExecucao = function(){
      return 'extensao='+document.getElementById('txtPrincipal').value;
    };
    
    objAutoCompletarComplementar.prepararExecucao = function(){
      return 'extensao='+document.getElementById('txtComplementar').value;
    };

    objAutoCompletarPrincipal.processarResultado = function(id,descricao,complemento){
      if (id!=''){
        var options = document.getElementById('selPrincipal').options;

        for(var i=0;i < options.length;i++){
          if (options[i].value == id){
            self.setTimeout('alert(\'Extens�o j� consta na lista.\')',100);
            break;
          }
        }

        if (i==options.length){

        for(i=0;i < options.length;i++){
          options[i].selected = false;
        }

        opt = infraSelectAdicionarOption(document.getElementById('selPrincipal'),descricao,id);

        objLupaPrincipal.atualizar();

        opt.selected = true;
      }

      document.getElementById('txtPrincipal').value = '';
      document.getElementById('txtPrincipal').focus();
    }};
    
    objAutoCompletarComplementar.processarResultado = function(id,descricao,complemento){
      if (id!=''){
        var options = document.getElementById('selComplementar').options;

        for(var i=0;i < options.length;i++){
          if (options[i].value == id){
            self.setTimeout('alert(\'Extens�o j� consta na lista.\')',100);
            break;
          }
        }

        if (i==options.length){

        for(i=0;i < options.length;i++){
          options[i].selected = false;
        }

        opt = infraSelectAdicionarOption(document.getElementById('selComplementar'),descricao,id);

        objLupaComplementar.atualizar();

        opt.selected = true;
      }

      document.getElementById('txtComplementar').value = '';
      document.getElementById('txtComplementar').focus();
    }};

    objAutoCompletarPrincipal.selecionar('<?=$strIdUnidade?>','<?=PaginaSEI::getInstance()->formatarParametrosJavascript(PaginaSEI::tratarHTML($strDescricaoUnidade))?>');
    objAutoCompletarComplementar.selecionar('<?=$strIdUnidade?>','<?=PaginaSEI::getInstance()->formatarParametrosJavascript(PaginaSEI::tratarHTML($strDescricaoUnidade))?>');

    infraEfeitoTabelas();
}

function OnSubmitForm() {
  return validarCadastro();
}

function validarCadastro() {
  if (infraTrim(document.getElementById('hdnPrincipal').value)=='') {
    alert('Informe pelo menos uma extens�o para documento principal.');
    return false;
  }
   if (infraTrim(document.getElementById('hdnComplementar').value)=='') {
    alert('Informe pelo menos uma extens�o para documento complementar.');
    return false;
  }
  return true;
}

<?
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
?>
  <form id="frmGrupoCadastro" method="post" onsubmit="return OnSubmitForm();" action="<?=PaginaSEI::getInstance()->formatarXHTML(SessaoSei::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">
    <?
    PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
    PaginaSEI::getInstance()->abrirAreaDados('45em');
    ?>
    
    <label id="lblPrincipal" for="txtPrincipal" accesskey="P" class="infraLabelObrigatorio">Documento Principal (Processo Novo): <img align="top" style="height:16px; width:16px;" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('Define as Extens�es de Arquivos Permitidas no Peticionamento de Processo Novo somente do Documento Principal, que geralmente � de tamanho menor que os demais documentos, pois tende a ser Nato Digital. \n \n ATEN��O: permitir apenas extens�es que comportem texto, evitando, por exemplo. zip, mp4 ou mp3 para Documento Principal.')?> class="infraImg"/></label>
    <input type="text" id="txtPrincipal" name="txtPrincipal" class="infraText"  onkeypress="return infraMascaraTexto(this,event,50);" maxlength="50" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"/>
    <select id="selPrincipal" name="selPrincipal" size="8" multiple="multiple" class="infraSelect" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>">
      <?=$strSelExtensoesPrin; ?>
    </select>
    <img id="imgLupaPrincipal" onclick="objLupaPrincipal.selecionar(700,500);" onkeypress="objLupaPrincipal.selecionar(700,500);" src="/infra_css/imagens/lupa.gif" alt="Selecionar Extens�es" title="Selecionar Extens�es" class="infraImg" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
    <img id="imgExcluirPrincipal" onclick="objLupaPrincipal.remover();" onkeypress="objLupaPrincipal.remover();" src="/infra_css/imagens/remover.gif" alt="Remover Extens�es Selecionadas" title="Remover Extens�es Selecionadas" class="infraImg" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />

	<label id="lblComplementar" for="txtComplementar" class="infraLabelObrigatorio">Demais Documentos: <img align="top" style="height:16px; width:16px;" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('Define as Extens�es de Arquivos Permitidas no Peticionamento de Processo Novo especificamente sobre os Documentos Essenciais e Complementares, no Peticionamento Intercorrente, no Peticionamento de Resposta a Intima��o e no Peticionamento de Respons�vel Legal de Pessoa Jur�dica.')?> class="infraImg"/></label>
    <input type="text" id="txtComplementar" name="txtComplementar" class="infraText"  onkeypress="return infraMascaraTexto(this,event,50);" maxlength="50" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"/>
    <select id="selComplementar" name="selComplementar" size="12" multiple="multiple" class="infraSelect" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>">
      <?=$strSelExtensoesComp; ?>
    </select>
    <img id="imgLupaComplementar" onclick="objLupaComplementar.selecionar(700,500);" onkeypress="objLupaComplementar.selecionar(700,500);" src="/infra_css/imagens/lupa.gif" alt="Selecionar Extens�es" title="Selecionar Extens�es" class="infraImg" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
    <img id="imgExcluirComplementar" onclick="objLupaComplementar.remover();" onkeypress="objLupaComplementar.remover();" src="/infra_css/imagens/remover.gif" alt="Remover Extens�es Selecionadas" title="Remover Extens�es Selecionadas" class="infraImg" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
	
    <input type="hidden" id="hdnIdPrincipal" name="hdnIdPrincipal" class="infraText" value="" />
    <input type="hidden" id="hdnPrincipal" name="hdnPrincipal" value="" />
    
    <input type="hidden" id="hdnIdComplementar" name="hdnIdComplementar" class="infraText" value="" />
    <input type="hidden" id="hdnComplementar" name="hdnComplementar" value="" />
    <?
    PaginaSEI::getInstance()->fecharAreaDados();
    ?>
  </form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>