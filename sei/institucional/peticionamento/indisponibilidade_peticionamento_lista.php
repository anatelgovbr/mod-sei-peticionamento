<?
/**
* ANATEL
*
* 16/02/2016 - criado por jaqueline.mendes@cast.com.br - CAST
*
*/


try {
  require_once dirname(__FILE__).'/../../SEI.php';

  session_start();

  SessaoSEI::getInstance()->validarLink();

  PaginaSEI::getInstance()->prepararSelecao('indisponibilidade_peticionamento_selecionar');

  SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

  switch($_GET['acao']){
    case 'indisponibilidade_peticionamento_excluir':
      try{
        $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
        $arrObjIndisponibilidadePeticionamentoDTO = array();
  for ($i=0;$i<count($arrStrIds);$i++){
          $objIndisponibilidadePeticionamentoDTO = new IndisponibilidadePeticionamentoDTO();
          $objIndisponibilidadePeticionamentoDTO->setNumIdIndisponibilidade($arrStrIds[$i]);
          $arrObjIndisponibilidadePeticionamentoDTO[] = $objIndisponibilidadePeticionamentoDTO;
        }
        $objIndisponibilidadePeticionamentoRN = new IndisponibilidadePeticionamentoRN();
        $objIndisponibilidadePeticionamentoRN->excluir($arrObjIndisponibilidadePeticionamentoDTO);
        
      }catch(Exception $e){
        PaginaSEI::getInstance()->processarExcecao($e);
      } 
      header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao_origem'].'&acao_origem='.$_GET['acao']));
      die;

    case 'indisponibilidade_peticionamento_desativar':
      try{
        $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
        $arrObjIndisponibilidadePeticionamentoDTO = array();
        for ($i=0;$i<count($arrStrIds);$i++){
          $objIndisponibilidadePeticionamentoDTO = new IndisponibilidadePeticionamentoDTO();
          $objIndisponibilidadePeticionamentoDTO->setNumIdIndisponibilidade($arrStrIds[$i]);
          $arrObjIndisponibilidadePeticionamentoDTO[] = $objIndisponibilidadePeticionamentoDTO;
        }
        $objIndisponibilidadePeticionamentoRN = new IndisponibilidadePeticionamentoRN();
        $objIndisponibilidadePeticionamentoRN->desativar($arrObjIndisponibilidadePeticionamentoDTO);
      }catch(Exception $e){
        PaginaSEI::getInstance()->processarExcecao($e);
      } 
      header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao_origem'].'&acao_origem='.$_GET['acao']));
      die;

    case 'indisponibilidade_peticionamento_reativar':
      
      //print_r($_GET); die();
    	
      $strTitulo = 'Reativar Indisponibilidade Peticionamento';

      if ($_GET['acao_confirmada']=='sim'){
        
    try{
        $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
        $arrObjIndisponibilidadePeticionamentoDTO = array();
        $idReativado = 0;
        for ($i=0;$i<count($arrStrIds);$i++){
          $objIndisponibilidadePeticionamentoDTO = new IndisponibilidadePeticionamentoDTO();
          $objIndisponibilidadePeticionamentoDTO->setNumIdIndisponibilidade($arrStrIds[$i]);
          $idReativado = $arrStrIds[$i];
          $arrObjIndisponibilidadePeticionamentoDTO[] = $objIndisponibilidadePeticionamentoDTO;
        }
        $objIndisponibilidadePeticionamentoRN = new IndisponibilidadePeticionamentoRN();
        $objIndisponibilidadePeticionamentoRN->reativar($arrObjIndisponibilidadePeticionamentoDTO);
        }catch(Exception $e){
          PaginaSEI::getInstance()->processarExcecao($e);
        } 
        
        $acaoLinhaAmarela = '';
        
        if( $idReativado != 0) {
          $acaoLinhaAmarela = '&id_indisponibilidade_peticionamento='. $idReativado.PaginaSEI::getInstance()->montarAncora($idReativado);
        }
        
        header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao_origem'].'&acao_origem='.$_GET['acao'] . $acaoLinhaAmarela));
        die;
      } 
      break;

    case 'indisponibilidade_peticionamento_selecionar':
      $strTitulo = PaginaSEI::getInstance()->getTituloSelecao('Selecionar Indisponibilidades','Selecionar Indisponibilidades');

      //Se cadastrou alguem
      if ($_GET['acao_origem']=='indisponibilidade_peticionamento_cadastrar'){
        if (isset($_GET['id_indisponibilidade_peticionamento'])){
          PaginaSEI::getInstance()->adicionarSelecionado($_GET['id_indisponibilidade_peticionamento']);
        }
      }
      break;

    case 'indisponibilidade_peticionamento_listar':
        
      $strTitulo = 'Peticionamento - Indisponibilidades do SEI';
      break;

    default:
      throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
  }

  //TODO: Marcelo, qual é a utilidade dessa funcionalidade de Transportar seleção neste tela?
  $arrComandos = array();
  if ($_GET['acao'] == 'indisponibilidade_peticionamento_selecionar'){
    $arrComandos[] = '<button type="button" accesskey="T" id="btnTransportarSelecao" value="Transportar" onclick="infraTransportarSelecao();" class="infraButton"><span class="infraTeclaAtalho">T</span>ransportar</button>';
  }

    
  $objIndisponibilidadePeticionamentoDTO = new IndisponibilidadePeticionamentoDTO();
  $objIndisponibilidadePeticionamentoDTO->retTodos();

  if($_POST['txtDtInicio']){
  	$objIndisponibilidadePeticionamentoDTO->setDthDataInicio($_POST['txtDtInicio'].':00');
  }
  
  if($_POST['txtDtFim']){
  	$objIndisponibilidadePeticionamentoDTO->setDthDataFim($_POST['txtDtFim'].':00');
  }
  
  if($_POST['selSinProrrogacao'] && $_POST['selSinProrrogacao']!= 'null'){
  	$objIndisponibilidadePeticionamentoDTO->setStrSinProrrogacao($_POST['selSinProrrogacao']);
  }
  

  PaginaSEI::getInstance()->prepararOrdenacao($objIndisponibilidadePeticionamentoDTO, 'DataInicio', InfraDTO::$TIPO_ORDENACAO_DESC);
  PaginaSEI::getInstance()->prepararPaginacao($objIndisponibilidadePeticionamentoDTO, 200);
  
  
  $objIndisponibilidadePeticionamentoRN = new IndisponibilidadePeticionamentoRN();
  
  $arrObjIndisponibilidadePeticionamentoDTO = $objIndisponibilidadePeticionamentoRN->listar($objIndisponibilidadePeticionamentoDTO);

  PaginaSEI::getInstance()->processarPaginacao($objIndisponibilidadePeticionamentoDTO);
  $numRegistros = count($arrObjIndisponibilidadePeticionamentoDTO);

  $strLinkPesquisar = PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] .'&acao_origem='.$_GET['acao']));
  $arrComandos[] = '<button type="button" accesskey="p" id="btnPesquisar" value="Pesquisar" onclick="pesquisar();" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';
  
  $bolAcaoCadastrar = SessaoSEI::getInstance()->verificarPermissao('indisponibilidade_peticionamento_cadastrar');
  if ($bolAcaoCadastrar){
  	$arrComandos[] = '<button type="button" accesskey="n" id="btnNovo" value="Nova" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=indisponibilidade_peticionamento_cadastrar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'])).'\'" class="infraButton"><span class="infraTeclaAtalho">N</span>ova</button>';
  }
  
  if ($numRegistros > 0){
  	
    $bolCheck = false;

    if ($_GET['acao']=='indisponibilidade_peticionamento_selecionar'){
      $bolAcaoReativar = false;
      $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('indisponibilidade_peticionamento_consultar');
      $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('indisponibilidade_peticionamento_alterar');
      $bolAcaoImprimir = false;
      $bolAcaoExcluir = false;
      $bolAcaoDesativar = false;
      $bolCheck = true;
     }else if ($_GET['acao']=='indisponibilidade_peticionamento_reativar'){
      $bolAcaoReativar = SessaoSEI::getInstance()->verificarPermissao('indisponibilidade_peticionamento_reativar');
      $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('indisponibilidade_peticionamento_consultar');
      $bolAcaoAlterar = false;
      $bolAcaoImprimir = true;
      $bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('indisponibilidade_peticionamento_excluir');
      $bolAcaoDesativar = false;
     }else{
      $bolAcaoReativar = false;
      $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('indisponibilidade_peticionamento_consultar');
      $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('indisponibilidade_peticionamento_alterar');
      $bolAcaoImprimir = true;
      $bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('indisponibilidade_peticionamento_excluir');
      $bolAcaoDesativar = SessaoSEI::getInstance()->verificarPermissao('indisponibilidade_peticionamento_desativar');
    }

    //TODO: Marcelo, se não vai ter o botão de Desativação em lote, melhor retirar todo este bloco de código.
	if ($bolAcaoDesativar){
      $bolCheck = true;
    //  $arrComandos[] = '<button type="button" accesskey="t" id="btnDesativar" value="Desativar" onclick="acaoDesativacaoMultipla();" class="infraButton">Desa<span class="infraTeclaAtalho">t</span>ivar</button>';
      $strLinkDesativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=indisponibilidade_peticionamento_desativar&acao_origem='.$_GET['acao']);
    }

     $strLinkReativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=indisponibilidade_peticionamento_reativar&acao_origem='.$_GET['acao'].'&acao_confirmada=sim');

    //TODO: Marcelo, se não vai ter o botão de Excluir em lote, melhor retirar todo este bloco de código.
	if ($bolAcaoExcluir){
      $bolCheck = true;
      //$arrComandos[] = '<button type="button" accesskey="E" id="btnExcluir" value="Excluir" onclick="acaoExclusaoMultipla();" class="infraButton"><span class="infraTeclaAtalho">E</span>xcluir</button>';
      $strLinkExcluir = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=indisponibilidade_peticionamento_excluir&acao_origem='.$_GET['acao']);
    }

    $strResultado = '';

    if ($_GET['acao']!='indisponibilidade_peticionamento_reativar'){ 
      $strSumarioTabela = 'Tabela de Indisponibilidades.';
      $strCaptionTabela = 'Indisponibilidades';
    }else{
      $strSumarioTabela = 'Tabela de Indisponibilidades Inativos.';
      $strCaptionTabela = 'Indisponibilidades Inativos';
    }

    $strResultado .= '<table width="99%" class="infraTable" summary="'.$strSumarioTabela.'">'."\n";
    $strResultado .= '<caption class="infraCaption">'.PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela,$numRegistros).'</caption>';
    $strResultado .= '<tr>';
    if ($bolCheck) {
      $strResultado .= '<th class="infraTh" width="1%">'.PaginaSEI::getInstance()->getThCheck().'</th>'."\n";
    }
    $strResultado .= '<th class="infraTh" width="30%">'.PaginaSEI::getInstance()->getThOrdenacao($objIndisponibilidadePeticionamentoDTO,'Início','DataInicio',$arrObjIndisponibilidadePeticionamentoDTO).'</th>'."\n";
    $strResultado .= '<th class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objIndisponibilidadePeticionamentoDTO,'Fim','DataFim',$arrObjIndisponibilidadePeticionamentoDTO).'</th>'."\n";
    $strResultado .= '<th class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objIndisponibilidadePeticionamentoDTO,'Prorrogação Automática dos Prazos','SinProrrogacao',$arrObjIndisponibilidadePeticionamentoDTO).'</th>'."\n";
    $strResultado .= '<th class="infraTh" width="15%">Ações</th>'."\n";
    $strResultado .= '</tr>'."\n";
    $strCssTr='';
    for($i = 0;$i < $numRegistros; $i++){
		
      if( $arrObjIndisponibilidadePeticionamentoDTO[$i]->getStrSinAtivo()=='S' ){
         $strCssTr = ($strCssTr=='<tr class="infraTrClara">')?'<tr class="infraTrEscura">':'<tr class="infraTrClara">';
      } else {
         $strCssTr ='<tr class="trVermelha">';
     }
       
      $strResultado .= $strCssTr;

      if ($bolCheck){
        $strResultado .= '<td valign="top">'.PaginaSEI::getInstance()->getTrCheck($i,$arrObjIndisponibilidadePeticionamentoDTO[$i]->getNumIdIndisponibilidade(),$arrObjIndisponibilidadePeticionamentoDTO[$i]->getStrSinProrrogacao()).'</td>';
      }
      
      $dataInicio = isset($arrObjIndisponibilidadePeticionamentoDTO[$i]) && $arrObjIndisponibilidadePeticionamentoDTO[$i]->getDthDataInicio() != '' ? str_replace(' ', ' - ',substr($arrObjIndisponibilidadePeticionamentoDTO[$i]->getDthDataInicio(), 0, -3))  :  '';
      $dataFim    = isset($arrObjIndisponibilidadePeticionamentoDTO[$i]) && $arrObjIndisponibilidadePeticionamentoDTO[$i]->getDthDataFim() != '' ? str_replace(' ', ' - ',substr($arrObjIndisponibilidadePeticionamentoDTO[$i]->getDthDataFim(), 0, -3))  :  '';
      
	  $sinProrrogacao =  $arrObjIndisponibilidadePeticionamentoDTO[$i]->getStrSinProrrogacao() === 'S' ? 'Sim' : 'Não';     
      
      $strResultado .= '<td>'.$dataInicio.'</td>';
      $strResultado .= '<td>'.$dataFim.'</td>';
      $strResultado .= '<td>'.$sinProrrogacao.'</td>';
      $strResultado .= '<td align="center">';

	  
      if ($bolAcaoConsultar){
        $strResultado .= '<a href="'.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=indisponibilidade_peticionamento_consultar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'].'&id_indisponibilidade_peticionamento='.$arrObjIndisponibilidadePeticionamentoDTO[$i]->getNumIdIndisponibilidade())).'" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/consultar.gif" title="Consultar Indisponibilidade" alt="Consultar Indisponibilidade" class="infraImg" /></a>&nbsp;';
      }

      if ($bolAcaoAlterar){
        
        $strResultado .= '<a href="'.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=indisponibilidade_peticionamento_alterar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'].'&id_indisponibilidade_peticionamento='.$arrObjIndisponibilidadePeticionamentoDTO[$i]->getNumIdIndisponibilidade())).'" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/alterar.gif" title="Alterar Indisponibilidade" alt="Alterar Indisponibilidade" class="infraImg" /></a>&nbsp;';
      }	

      if ($bolAcaoDesativar || $bolAcaoReativar || $bolAcaoExcluir){
        $strId = $arrObjIndisponibilidadePeticionamentoDTO[$i]->getNumIdIndisponibilidade();
        $dataIni = $arrObjIndisponibilidadePeticionamentoDTO[$i]->getDthDataInicio() != '' ? substr($arrObjIndisponibilidadePeticionamentoDTO[$i]->getDthDataInicio(), 0 , -3) : '';
        $dataFim = $arrObjIndisponibilidadePeticionamentoDTO[$i]->getDthDataFim() != '' ? substr($arrObjIndisponibilidadePeticionamentoDTO[$i]->getDthDataFim(), 0 , -3) : '';
        
        $dataIni = str_replace(' ', ' - ', $dataIni);
        $dataFim = str_replace(' ', ' - ', $dataFim);
      }
 
      if ($bolAcaoDesativar && $arrObjIndisponibilidadePeticionamentoDTO[$i]->getStrSinAtivo() == 'S'){
        $strResultado .= '<a href="'.PaginaSEI::getInstance()->montarAncora($strId).'" onclick="acaoDesativar(\''.$strId.'\',\''.$dataIni.'\',\''.$dataFim.'\');" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/desativar.gif" title="Desativar Indisponibilidade" alt="Desativar Indisponibilidade" class="infraImg" /></a>&nbsp;';
      } else {
	    $strResultado .= '<a href="'.PaginaSEI::getInstance()->montarAncora($strId).'" onclick="acaoReativar(\''.$strId.'\',\''.$dataIni.'\',\''.$dataFim.'\');" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/reativar.gif" title="Reativar Indisponibilidade" alt="Reativar Indisponibilidade" class="infraImg" /></a>&nbsp;';
      }

      if ($bolAcaoExcluir){
        $strResultado .= '<a href="'.PaginaSEI::getInstance()->montarAncora($strId).'" onclick="acaoExcluir(\''.$strId.'\',\''.$dataIni.'\',\''.$dataFim.'\', \''. $arrObjIndisponibilidadePeticionamentoDTO[$i]->getStrSinProrrogacao(). '\');" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/excluir.gif" title="Excluir Indisponibilidade" alt="Excluir Indisponibilidade" class="infraImg" /></a>&nbsp;';
      }
      

      $strResultado .= '</td></tr>'."\n";
    }
    $strResultado .= '</table>';
  }
  
	
  if( $bolAcaoImprimir ) {
    $arrComandos[] = '<button type="button" accesskey="i" id="btnImprimir" value="Imprimir" onclick="infraImprimirTabela();" class="infraButton"><span class="infraTeclaAtalho">I</span>mprimir</button>';
  }
  
  if ($_GET['acao'] == 'indisponibilidade_peticionamento_reativar'){
    $arrComandos[] = '<button type="button" accesskey="c" id="btnFecharSelecao" value="Fechar" onclick="window.close();" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
  }else{
    $arrComandos[] = '<button type="button" accesskey="c" id="btnFechar" value="Fechar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'])).'\'" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
  }
  
  //txtDtInicio
  //txtDtFim
  $strDtInicio = '';
  $strDtFim = '';
  
  if( isset( $_POST['txtDtInicio']) ){
  	$strDtInicio = $_POST['txtDtInicio'];
  }
  
  if( isset( $_POST['txtDtFim']) ){
  	$strDtFim = $_POST['txtDtFim'];
  }
  
  $valorComboProrrogacao = '';
  
  if( isset( $_POST['selSinProrrogacao']) ){
  	$valorComboProrrogacao = $_POST['selSinProrrogacao'];
  }
  
  $strItensSelSinProrrogacaoAutomatica = IndisponibilidadePeticionamentoINT::montarSelectProrrogacaoAutomaticaPrazos('','Todos',$valorComboProrrogacao);

}catch(Exception $e){
  PaginaSEI::getInstance()->processarExcecao($e);
} 

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: '.PaginaSEI::getInstance()->getStrNomeSistema().' - '.$strTitulo.' ::');
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
?>
<?
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>

function inicializar(){
  if ('<?=$_GET['acao']?>'=='indisponibilidade_litigioso_selecionar'){
    infraReceberSelecao();
    document.getElementById('btnFecharSelecao').focus();
  }else{
    document.getElementById('btnFechar').focus();
  }
  infraEfeitoTabelas();
}


function validDate(valor) {
		
	var campo = (valor === 'I') ?  document.getElementById('txtDtInicio') : campo = document.getElementById('txtDtFim');
    var tamanhoCampo = parseInt((campo.value).length);
    
	if(tamanhoCampo < 16 || tamanhoCampo === 18){
		campo.focus();
		campo.value = "";
		alert('Data/Hora Inválida');
		return false;
	}
	
	var datetime = (campo.value).split(" ");
	var date = datetime[0];
	
	var ardt=new Array;
	var ExpReg=new RegExp("(0[1-9]|[12][0-9]|3[01])/(0[1-9]|1[012])/[12][0-9]{3}");
	ardt=date.split("/");
	erro=false;
	if ( date.search(ExpReg)==-1){
		erro = true;
		}
	else if (((ardt[1]==4)||(ardt[1]==6)||(ardt[1]==9)||(ardt[1]==11))&&(ardt[0]>30)){
		erro = true;
	}else if ( ardt[1]==2) {
		if ((ardt[0]>28)&&((ardt[2]%4)!=0))
			erro = true;
		if ((ardt[0]>29)&&((ardt[2]%4)==0))
			erro = true;
	}
	
	if (erro) {
		alert("Data/Hora Inválida");
		campo.focus();
		campo.value = "";
		return false;
	}else{

		var arrayHoras = datetime[1].split(':')
		var horas      = arrayHoras[0];
		var minutos    = arrayHoras[1];
		var segundos   = arrayHoras[2];
		if(horas > 23 || minutos > 59 || segundos > 59){
		alert('Data/Hora Inválida');
		campo.focus();
		campo.value = "";
		return false
		}
		
	}

	if(document.getElementById('txtDtInicio').value != '' && document.getElementById('txtDtFim').value != ''){
		var dataInicial = returnDateTime(document.getElementById('txtDtInicio').value);
		var dataFinal   = returnDateTime(document.getElementById('txtDtFim').value);
		
		var valido = (dataInicial.getTime() < dataFinal.getTime());

		if(!valido)
	    {
    		document.getElementById('txtDtInicio').value = '';
	    	document.getElementById('txtDtFim').value = '';
			alert('A Data/Hora Inicio deve ser menor que a Data/Hora Fim');
			return false;
		 }
	}

	
	return true;
}

function returnDateTime(valor){

	valorArray = valor != '' ? valor.split(" ") : '';

	if(Array.isArray(valorArray)){
	var data = valorArray[0]
	data = data.split('/');
	var mes = parseInt(data[1]) - 1; 
    var horas = valorArray[1].split(':');

    var segundos = typeof horas[2] != 'undefined' ?  horas[2] : 00;
	var dataCompleta = new Date(data[2], mes  ,data[0], horas[0] , horas[1] , segundos);
	return dataCompleta;
	}

	return false;
}


<? if ($bolAcaoDesativar){ ?>
function acaoDesativar(id, dataInicio, dataFim){
  if (confirm("Confirma desativação da Indisponibilidade referente ao período \""+dataInicio+ " a " + dataFim+"\"?")){
    document.getElementById('hdnInfraItemId').value=id;
    document.getElementById('frmIndisponibilidadePeticionamentoLista').action='<?=$strLinkDesativar?>';
    document.getElementById('frmIndisponibilidadePeticionamentoLista').submit();
  }
}

function acaoDesativacaoMultipla(){
  if (document.getElementById('hdnInfraItensSelecionados').value==''){
    alert('Nenhuma Indisponibilidade selecionada.');
    return;
  }
  if (confirm("Confirma a desativação das Indisponibilidades selecionadas?")){
    document.getElementById('hdnInfraItemId').value='';
    document.getElementById('frmIndisponibilidadePeticionamentoLista').action='<?=$strLinkDesativar?>';
    document.getElementById('frmIndisponibilidadePeticionamentoLista').submit();
  }
}
<? } ?>

function acaoReativar(id, dataInicio, dataFim){
if (confirm("Confirma reativação da Indisponibilidade referente ao período \""+dataInicio+ " a " + dataFim+"\"?")){
    document.getElementById('hdnInfraItemId').value=id;
    document.getElementById('frmIndisponibilidadePeticionamentoLista').action='<?=$strLinkReativar?>';
    document.getElementById('frmIndisponibilidadePeticionamentoLista').submit();
  }
}

function acaoReativacaoMultipla(){
  if (document.getElementById('hdnInfraItensSelecionados').value==''){
    alert('Nenhuma Indisponibilidade selecionada.');
    return;
  }
  if (confirm("Confirma a reativação das Indisponibilidades selecionadas?")){
    document.getElementById('hdnInfraItemId').value='';
    document.getElementById('frmIndisponibilidadePeticionamentoLista').action='<?=$strLinkReativar?>';
    document.getElementById('frmIndisponibilidadePeticionamentoLista').submit();
  }
}

<? if ($bolAcaoExcluir){ ?>
function acaoExcluir(id, dataInicio, dataFim, prorrogacao){

 if(prorrogacao === 'S') {
    alert('A exclusão da Indisponibilidade não é permitida, pois a indisponibilidade justificou prorrogação automática de prazos.');
 } 
 else if (confirm("Confirma exclusão da Indisponibilidade referente ao período \""+dataInicio+ " a " + dataFim+"\"?")){
    document.getElementById('hdnInfraItemId').value=id;
    document.getElementById('frmIndisponibilidadePeticionamentoLista').action='<?=$strLinkExcluir?>';
    document.getElementById('frmIndisponibilidadePeticionamentoLista').submit();
 }  
  
}

function acaoExclusaoMultipla(){
  if (document.getElementById('hdnInfraItensSelecionados').value==''){
    alert('Nenhuma Indisponibilidade selecionada.');
    return;
  }
  if (confirm("Confirma a exclusão das Indisponibilidades selecionadas?")){
    document.getElementById('hdnInfraItemId').value='';
    document.getElementById('frmIndisponibilidadePeticionamentoLista').action='<?=$strLinkExcluir?>';
    document.getElementById('frmIndisponibilidadePeticionamentoLista').submit();
  }
}

<? } ?>

function pesquisar(){
    document.getElementById('frmIndisponibilidadePeticionamentoLista').action='<?=$strLinkPesquisar?>';
    document.getElementById('frmIndisponibilidadePeticionamentoLista').submit();
}



<?
PaginaSEI::getInstance()->fecharJavaScript();
?>

<style type="text/css">

#lblDtInicio {position:absolute;left:0%;top:0%;width:10%;}
#txtDtInicio {position:absolute;left:0%;top:40%;width:10%;}
#imgDtInicio {position:absolute;left:11%;top:40%;}

#lblDtFim {position:absolute;left:15%;top:0%;width:10%;}
#txtDtFim {position:absolute;left:15%;top:40%;width:10%;}
#imgDtFim {position:absolute;left:26%;top:40%;}


#lblSinProrrogacao {position:absolute;left:29%;top:0%;width:30%;}
#selSinProrrogacao {position:absolute;left:29%;top:40%;width:20%;}

</style>

<?php 
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
?>
<form id="frmIndisponibilidadePeticionamentoLista" method="post" action="<?=PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">

<?php  PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos); ?>

  <div style="height:4.5em; margin-top: 11px;" class="infraAreaDados" id="divInfraAreaDados">
  
  <!--  Data Inicio  -->
  	<label id="lblDtInicio" for="txtDtInicio" class="infraLabelOpcional">Início:</label>
    <input type="text" name="txtDtInicio" id="txtDtInicio" onchange="validDate('I');" 
    value="<?= $strDtInicio ?>" 
    onkeypress="return infraMascara(this, event, '##/##/#### ##:##');" class="infraText" />
 	<img src="<?=PaginaSEI::getInstance()->getDiretorioImagensGlobal()?>/calendario.gif" id="imgDtInicio" 
 	     title="Selecionar Data/Hora Inicial" 
 	     alt="Selecionar Data/Hora Inicial" class="infraImg" 
 	     onclick="infraCalendario('txtDtInicio',this,true,'<?=InfraData::getStrDataAtual().' 00:00'?>');" />
    
  <!--  Data Fim  -->
  	<label id="lblDtFim" for="txtDtFim" class="infraLabelOpcional">Fim:</label>
    <input type="text" name="txtDtFim" onchange="validDate('F');" id="txtDtFim" 
    value="<?= $strDtFim ?>"  
    onchange="validDate('F');" onkeypress="return infraMascara(this, event, '##/##/#### ##:##');" maxlength="16" class="infraText"/>
    <img src="<?=PaginaSEI::getInstance()->getDiretorioImagensGlobal()?>/calendario.gif" id="imgDtFim" 
         title="Selecionar Data/Hora Final" 
         alt="Selecionar Data/Hora Final" 
         class="infraImg" onclick="infraCalendario('txtDtFim',this,true,'<?=InfraData::getStrDataAtual().' 23:59'?>');" />

<!--  Select Prorrogacao -->

 <label id="lblSinProrrogacao" for="selSinProrrogacao" class="infraLabelOpcional">Prorrogação Automática dos Prazos:</label>
  <select onchange="pesquisar()" id="selSinProrrogacao" name="selSinProrrogacao" class="infraSelect" >
  <?=$strItensSelSinProrrogacaoAutomatica?>
  </select> 
  
    <input type="submit" style="visibility: hidden;" />
 </div>   
  <?
 
  PaginaSEI::getInstance()->montarAreaTabela($strResultado,$numRegistros);
  PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
  ?>

</form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>