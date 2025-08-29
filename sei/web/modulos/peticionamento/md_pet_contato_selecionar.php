<?
/**
* ANATEL - Módulo Peticionamento Eletronico
*
* 21/07/2016 - criado por marcelo.bezerra@cast.com.br
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
   
  SessaoSEIExterna::getInstance()->validarLink();
  PaginaSEIExterna::getInstance()->prepararSelecao('md_pet_contato_selecionar');
  SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);
  PaginaSEIExterna::getInstance()->salvarCamposPost(array('txtPalavrasPesquisaContatos','selGrupoContato','txtNascimentoInicio','txtNascimentoFim', 'selTipoContextoContato'));
   
  //trazer os tipos de contatos parametrizados na administraçao do modulo para com isso aplicar:
  //- na combo trazer apenas os tipos parametrizados
  //- na lista de registros trazer apenas aqueles que pertençam a algum dos tipos parametrizados do modulo  
  $idTipoProc = $_GET['id_tipo_processo_peticionamento'];
  $objMdPetRelTpCtxContatoDTO = new MdPetRelTpCtxContatoDTO();
  $objMdPetTpCtxContatoRN = new MdPetTpCtxContatoRN();
  $objMdPetRelTpCtxContatoDTO->retTodos();
  $objMdPetRelTpCtxContatoDTO->setStrSinSelecaoInteressado('S');
  $arrobjMdPetRelTpCtxContatoDTO = $objMdPetTpCtxContatoRN->listar( $objMdPetRelTpCtxContatoDTO );
  
  if (isset($_POST['hdnFlag'])){
	  PaginaSEIExterna::getInstance()->salvarCampo('chkMaisOpcoesContatos',(isset($_POST['chkMaisOpcoesContatos']) ? PaginaSEIExterna::getInstance()->getCheckbox($_POST['chkMaisOpcoesContatos']) : 'N'));
  }
	
	//link de acesso que preenche os critérios
  if (isset($_GET['palavras_pesquisa'])){
    PaginaSEIExterna::getInstance()->salvarCampo('txtPalavrasPesquisaContatos',$_GET['palavras_pesquisa']);
  }
  
  if (isset($_GET['mais_opcoes'])){
	  PaginaSEIExterna::getInstance()->salvarCampo('chkMaisOpcoesContatos',$_GET['mais_opcoes']);
  }

  if (isset($_GET['id_tipo_contexto_contato'])){
    PaginaSEIExterna::getInstance()->salvarCampo('selTipoContextoContato',$_GET['id_tipo_contexto_contato']);
  }
	   
  switch($_GET['acao']){

    case 'md_pet_contato_selecionar':
      
      if ($_GET['acao']=='md_pet_contato_selecionar'){
        $strTitulo = PaginaSEIExterna::getInstance()->getTituloSelecao('Selecionar Interessado','Selecionar Interessados');
      } 
      
      break;
       
    case 'peticionamento_contato_listar':
            
       $strTitulo = 'Interessados';
       break;

    default:
      throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
  }

  //alteracoes seiv3
  $objRelUnidadeTipoContContatoRN = new RelUnidadeTipoContatoRN();
  $objRelUnidadeTipoContContatoDTO = new RelUnidadeTipoContatoDTO();

  $objRelUnidadeTipoContContatoDTO->retNumIdTipoContato();
  $objRelUnidadeTipoContContatoDTO->setNumIdUnidade(SessaoSEIExterna::getInstance()->getNumIdUnidadeAtual());
  $arrTiposContextosUnidade = InfraArray::converterArrInfraDTO($objRelUnidadeTipoContContatoRN->listarRN0547($objRelUnidadeTipoContContatoDTO),'IdTipoContato');

  $objContatoDTO = new ContatoDTO();
  $objContatoDTO->retNumIdContato();
  $objContatoDTO->retStrNome();
  $objContatoDTO->retStrSigla();
  $objContatoDTO->retStrEmail();

  //alteracoes seiv3
  $objContatoDTO->retNumIdContato();
  $objContatoDTO->retNumIdTipoContato();
  $objContatoDTO->retStrStaNatureza();
  $objContatoDTO->retNumIdUsuarioCadastro();
  
  $objContatoDTO->retStrExpressaoVocativoCargo();
  $objContatoDTO->retStrExpressaoTratamentoCargo();

  $objContatoDTO->retStrExpressaoCargo();

  if ($arrTiposContextosUnidade) {
    //Verificar se novo campo SinAtivoTipoContato ou outro substitui anterior SinLiberadoTipoContextoContatoContato
    $objContatoDTO->adicionarCriterio(
        array('IdTipoContato'),
        array(InfraDTO::$OPER_IN),
        array($arrTiposContextosUnidade)
        );
  }

  $objContatoDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);

  SessaoSEIExterna::getInstance()->setAtributo('janelaSelecaoPorNome', 'true');
  $arrComandos = array();
  $arrComandos[] = '<button type="button" accesskey="n" id="btnCadastrar" name="btnCadastrar" onclick="cadastrarNovoInteressado()" value="Cadastrar Novo Interessado" class="infraButton">Cadastrar <span class="infraTeclaAtalho">N</span>ovo Interessado</button>';
  $arrComandos[] = '<button type="button" accesskey="p" id="btnPesquisar" name="btnPesquisar" onclick="pesquisar();" value="Pesquisar" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';
  
  if (PaginaSEIExterna::getInstance()->isBolPaginaSelecao()){
      $arrComandos[] = '<button type="button" accesskey="t" id="btnTransportarSelecao" name="btnTransportarSelecao" onclick="infraTransportarSelecao();" value="Transportar" class="infraButton"><span class="infraTeclaAtalho">T</span>ransportar</button>';
  }
      
  if($_GET['acao']=='contexto_selecionar' || 
       $_GET['acao']=='contexto_selecionar_unidade' ||  
       $_GET['acao']=='contexto_selecionar_usuario' ||  
       $_GET['acao']=='contexto_selecionar_email' ||  
       $_GET['acao']=='contexto_listar'){
    	$objContatoDTO->setStrSinContexto('S');
  }

  $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
    
  if ($_GET['acao']=='contexto_selecionar_unidade' || $_GET['acao']=='contexto_selecionar_usuario'){
      $objContatoDTO->setNumIdTipoContextoContatoContato($_GET['id_tipo_contexto_contato']);
  }
    
  $strPalavrasPesquisa = PaginaSEIExterna::getInstance()->recuperarCampo('txtPalavrasPesquisaContatos');

  if ($strPalavrasPesquisa!=''){
      $objContatoDTO->setStrPalavrasPesquisa($strPalavrasPesquisa);
  }

    $numTipoContextoContato = PaginaSEIExterna::getInstance()->recuperarCampo('selTipoContextoContato');
    if ($numTipoContextoContato!='' && $numTipoContextoContato!='null'){
        $objContatoDTO->setNumIdTipoContato($numTipoContextoContato);
    }else if(!empty($arrobjMdPetRelTpCtxContatoDTO)){
        $arrId = array();
        foreach($arrobjMdPetRelTpCtxContatoDTO as $item){
            array_push($arrId, $item->getNumIdTipoContextoContato());
        }
        
        //alteracoes seiv3
        $objContatoDTO->adicionarCriterio(array('IdTipoContato'),
            array(InfraDTO::$OPER_IN),
            array($arrId));
    }

    $objContatoDTO->setStrMaisOpcoes(PaginaSEIExterna::getInstance()->recuperarCampo('chkMaisOpcoesContatos'));
      
    //Somente adiciona ANTES da consulta se é para utilizar como filtro
    if ($objContatoDTO->getStrMaisOpcoes()=='S'){
  
        $strDataInicio = PaginaSEIExterna::getInstance()->recuperarCampo('txtNascimentoInicio');
        if ($strDataInicio!=''){
          $objContatoDTO->setDtaNascimentoInicio($strDataInicio);
        }
        
        $strDataFim = PaginaSEIExterna::getInstance()->recuperarCampo('txtNascimentoFim');
        if ($strDataFim!=''){
          $objContatoDTO->setDtaNascimentoFim($strDataFim);
        }
  }

  $numRegistrosPorPagina = 200;
  
  PaginaSEIExterna::getInstance()->prepararPaginacao($objContatoDTO,$numRegistrosPorPagina);
  
  $objContatoRN = new ContatoRN();
  $arrObjContatoDTO = $objContatoRN->pesquisarRN0471($objContatoDTO);
  
  PaginaSEIExterna::getInstance()->processarPaginacao($objContatoDTO);

  $numRegistros = count($arrObjContatoDTO);

  if ($numRegistros > 0){
    //Adiciona no DTO DEPOIS da consulta somente para salvar e mostrar os valores
    if ($objContatoDTO->getStrMaisOpcoes()=='N'){
      $objContatoDTO->setDtaNascimentoInicio(PaginaSEIExterna::getInstance()->recuperarCampo('txtNascimentoInicio'));
      $objContatoDTO->setDtaNascimentoFim(PaginaSEIExterna::getInstance()->recuperarCampo('txtNascimentoFim'));
    }
   
    $strNegritoContextoIni = '<b>';          
    $strNegritoContextoFim = '</b>';
    $bolCheck = false;

    if (PaginaSEIExterna::getInstance()->isBolPaginaSelecao()){
      $bolAcaoConsultar = SessaoSEIExterna::getInstance()->verificarPermissao('peticionamento_contato_consultar');
      $bolAcaoImprimir = false;
      $bolCheck = true;
    }else{
      $bolAcaoConsultar = SessaoSEIExterna::getInstance()->verificarPermissao('peticionamento_contato_consultar');      
      $bolAcaoImprimir = true;
    }
    
    if ($bolAcaoImprimir){
      $bolCheck = true;
      $arrComandos[] = '<button type="button" accesskey="i" id="btnImprimir" onclick="infraImprimirTabela();" value="Imprimir" class="infraButton"><span class="infraTeclaAtalho">I</span>mprimir</button>';
    }

    $strCaptionTabela = '';
    if ($_GET['acao'] == 'peticionamento_contato_listar'){
      $strSumarioTabela = 'Tabela de Interessados.';
      $strCaptionTabela .= 'Interessados';
    }else{
      $strSumarioTabela = 'Tabela de Interessados.';
      $strCaptionTabela .= 'Interessados';
    }
    
    $strResultado = '';

    $strResultado .= '<tr>';
    if ($bolCheck) {
      $strResultado .= '<th class="infraTh" width="1%">'.PaginaSEIExterna::getInstance()->getThCheck().'</th>'."\n";
    }
    $strResultado .= '<th class="infraTh" width="50%">'.$strCaptionTabela.'</th>'."\n";
        
    $strResultado .= '<th class="infraTh" width="15%">Ações</th>'."\n";
    $strResultado .= '</tr>'."\n";
    $strCssTr='';
    
    $arrContextos = array();
    $n = 0;
    
    foreach($arrObjContatoDTO as $dto){
        
      $strCssTr='<tr class="infraTrClara">';

      $strResultado .= $strCssTr;
      $strTitle = '';
      $strNomeSigla = $dto->getStrNome();
      $strTitle = $strNomeSigla;
      
      if ($bolCheck){
        $strResultado .= '<td align="center">'.PaginaSEIExterna::getInstance()->getTrCheck($n,$dto->getNumIdContato(),$strTitle).'</td>';
      }

      $strResultado .= '<td>';

      $strResultado .= PaginaSEIExterna::tratarHTML($strNomeSigla);

      $strResultado .= '</td>';

      $strResultado .= '<td align="center">';

      //Alteração

      if (SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()==$dto->getNumIdUsuarioCadastro()){
      	$strResultado .= "<a href='javascript:;' onclick=\"abrirCadastroInteressadoAlterar('".$dto->getNumIdContato()."', '".$dto->getStrStaNatureza()."')\"><img title='Alterar Interessado' alt='Alterar Interessado' src='/infra_css/imagens/alterar.gif' class='infraImg' /></a>";
      }

      $strResultado .= PaginaSEIExterna::getInstance()->getAcaoTransportarItem($n++,$dto->getNumIdContato());

      $strId = $dto->getNumIdContato();
      $strDescricao = PaginaSEIExterna::getInstance()->formatarParametrosJavaScript(PaginaSEIExterna::tratarHTML($strNomeSigla));

	    if($_GET['acao']=='contato_selecionar' ||  $_GET['acao']=='peticionamento_contato_listar'){ 

	      $balao = '';
	      if (!InfraString::isBolVazia($dto->getStrExpressaoVocativo())){
	      	$balao .= $dto->getStrExpressaoVocativo().'\n';
	      }
	      
	      if (!InfraString::isBolVazia($dto->getStrExpressaoTratamento())){
	      		$balao .= $dto->getStrExpressaoTratamento().'\n';
	      }

	      if (!InfraString::isBolVazia($dto->getStrExpressaoTitulo())){
	      	$balao .= $dto->getStrExpressaoTitulo().'\n';
	      }
	      
	      if (!InfraString::isBolVazia($dto->getStrExpressaoCargo())){
	      	$balao .= $dto->getStrExpressaoCargo();
	      }
	      if ($balao!=''){
	  			$strResultado .= '<a onmouseover="return infraTooltipMostrar(\''.$balao.'\');" onmouseout="return infraTooltipOcultar();"><img src="/infra_css/imagens/balao.gif" class="infraImg" tabindex="'.PaginaSEIExterna::getInstance()->getProxTabTabela().'" /></a>&nbsp;';    	
	      }
    	}

      $strResultado .= '</td></tr>'."\n";
    }
    
    if ( $objContatoDTO->getNumTotalRegistros() > $objContatoDTO->getNumRegistrosPaginaAtual()){
      $strCaptionTabela .= ' ('.$objContatoDTO->getNumTotalRegistros().' '.(($objContatoDTO->getNumTotalRegistros()==1)?'registro':'registros').' página '.($objContatoDTO->getNumPaginaAtual()+1).' de '.ceil($objContatoDTO->getNumTotalRegistros()/$numRegistrosPorPagina).')';
    }else{
      $strCaptionTabela .= ' ('.$n.' '.(($n==1)?'registro':'registros').')';
    }
    
    $strResultado = '<table width="99%" class="infraTable" summary="'.$strSumarioTabela.'">'."\n".
                    '<caption class="infraCaption">Lista de '.$strCaptionTabela.'</caption>'."\n".
                    $strResultado.
                    '</table>'."\n";
  }
  
  $arrComandos[] = '<button type="button" accesskey="c" id="btnFechar" name="btnFechar" onclick="$(window.top.document).find(\'div[id^=divInfraSparklingModalClose]\').click();" value="Fechar" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
  
  $strItensSelGrupoContato = GrupoContatoINT::ConjuntoPorUnidadeRI0515('null','&nbsp;', '');
  
  // buscanco primeira unidade para simular login para conseguir fazer a "montarSelectNomeRI0518"
  SessaoSEIExterna::getInstance();
  $seiRN = new SeiRN();
  $objEntradaConsultarDocumentoAPI = new EntradaListarUnidadesAPI();
  $objSaidaConsultarDocumentoAPI = $seiRN->listarUnidades($objEntradaConsultarDocumentoAPI);
  SessaoSEI::getInstance()->simularLogin(null, null, SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno(), $objSaidaConsultarDocumentoAPI[0]->getIdUnidade());
  
  //alteracoes seiv3
  if( $numTipoContextoContato != null && $numTipoContextoContato != "" ){
    $strItensSelTipoContextoContato = TipoContatoINT::montarSelectNomeRI0518('null','&nbsp;',$numTipoContextoContato);
  }
  else {
  	$strItensSelTipoContextoContato = TipoContatoINT::montarSelectNomeRI0518('null','&nbsp;','null');
  }
  
  $strLinkAdicionarSelecao = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=contato_adicionar_selecao&acao_origem='.$_GET['acao']);
  $strLinkVisualizarSelecao = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=contato_visualizar_selecao&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao']);
  $strLinkDesfazerSelecao = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=contato_desfazer_selecao&acao_origem='.$_GET['acao']);
  
}catch(Exception $e){
  PaginaSEIExterna::getInstance()->processarExcecao($e);
} 

PaginaSEIExterna::getInstance()->montarDocType();
PaginaSEIExterna::getInstance()->abrirHtml();
PaginaSEIExterna::getInstance()->abrirHead();
PaginaSEIExterna::getInstance()->montarMeta();
PaginaSEIExterna::getInstance()->montarTitle(':: '.PaginaSEIExterna::getInstance()->getStrNomeSistema().' - '.$strTitulo.' ::');
PaginaSEIExterna::getInstance()->montarStyle();
PaginaSEIExterna::getInstance()->abrirStyle();
?>

#lblPalavrasPesquisaContatos {position:absolute;left:0%;top:0%;width:45%;}
#txtPalavrasPesquisaContatos {position:absolute;left:0%;top:22%;width:45%;}
#lblTipoContextoContato {position:absolute;left:50%;top:0%;width:49%;}
#selTipoContextoContato {position:absolute;left:50%;top:22%;width:49%;}
	
<?
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
$linkCadastroInteressado = PaginaSEIExterna::getInstance()->formatarXHTML( SessaoSEIExterna::getInstance()->assinarLink("controlador_externo.php?acao=md_pet_interessado_cadastro&tipo_selecao=2&cpf=&cnpj=&id_orgao_acesso_externo=0"));
?>

function cadastrarNovoInteressado(){
  document.location.href = '<?= $linkCadastroInteressado ?>';
}

function inicializar(){
  if ('<?=PaginaSEIExterna::getInstance()->isBolPaginaSelecao()?>'!=''){
    infraReceberSelecao();
  }else {
  	if ('<?=$_GET['acao']?>' =='contato_visualizar_selecao'){  
      document.getElementById('divInfraAreaDados').style.display='none';
    }
    
 }
 	
 if (infraGetAnchor()==null){
	  try{
 	    document.getElementById('txtPalavrasPesquisaContatos').focus();
	  }catch(controleIndisponivel){}
 }
 	
 infraEfeitoTabelas();
  
}

function OnSubmitForm() {
  return validarPesquisaRI0570();
}

function validarPesquisaRI0570() {
  return true;
}

function selecionarTipoContato(){
	document.getElementById('frmContatoLista').submit();
}

function pesquisar(){
   document.getElementById('frmContatoLista').submit();
}


function abrirCadastroInteressadoAlterar(id, tipo){

	//charmar janela para cadastrar um novo interessado
	$('#txtNomeRazaoSocial').val('');
	$('#hdnCustomizado').val('');
	$('#hdnIdEdicao').val( id );

	<?php 
	$strLinkEdicaoPF = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?edicao=true&acao=md_pet_interessado_cadastro&tipo_selecao=2&cpf=true&id_orgao_acesso_externo=0');
	$strLinkEdicaoPJ = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?edicao=true&acao=md_pet_interessado_cadastro&tipo_selecao=2&cnpj=true&id_orgao_acesso_externo=0');
	?>

	if( tipo == '<?= ContatoRN::$TN_PESSOA_FISICA ?>' ){
		var str = '<?= $strLinkEdicaoPF ?>';
	}

	else if( tipo == '<?= ContatoRN::$TN_PESSOA_JURIDICA ?>' ){
		var str = '<?= $strLinkEdicaoPJ ?>';
	}

    infraAbrirJanelaModal(str, 900, 900); //modal
	return;
	
}
function atualizarNomeRazaoSocial( cpfEditado , nomeEditado ){
	location.href=location.href;
}


<?
PaginaSEIExterna::getInstance()->fecharJavaScript();
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
?>
<form id="frmContatoLista" method="post" onsubmit="return OnSubmitForm();" 
      action="<?=PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">
  <?
  PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
  PaginaSEIExterna::getInstance()->abrirAreaDados('6.5em');
  ?>
  
  <label id="lblPalavrasPesquisaContatos" for="txtPalavrasPesquisaContatos" class="infraLabelOpcional">Palavras-chave para pesquisa:</label>
  <input type="text" id="txtPalavrasPesquisaContatos" name="txtPalavrasPesquisaContatos" class="infraText" value="<?=PaginaSEI::tratarHTML($strPalavrasPesquisa);?>" tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>" />

  <label id="lblTipoContextoContato" for="selTipoContextoContato" class="infraLabelOpicional">Tipo de Interessado:</label>
  <select id="selTipoContextoContato" name="selTipoContextoContato" class="infraSelect" 
          onchange="selecionarTipoContato()" 
          tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>" >
  <option></option>
  <?
	if(!empty($arrobjMdPetRelTpCtxContatoDTO)){
		
		$objTipoContatoDTO = new TipoContatoDTO();
		$objTipoContatoRN = new TipoContatoRN();

		$objTipoContatoDTO->retStrSinSistema();
		$objTipoContatoDTO->retNumIdTipoContato();
		$objTipoContatoDTO->retStrNome();

		// Tipos permitidos
		$arrRelTipoContextoPeticionamento = InfraArray::converterArrInfraDTO($arrobjMdPetRelTpCtxContatoDTO,'IdTipoContextoContato');

		$objTipoContatoDTO->adicionarCriterio(array('IdTipoContato'),
			array(InfraDTO::$OPER_IN),
			array($arrRelTipoContextoPeticionamento)
		);
		$objTipoContatoDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);
		$objTipoContatoDTO = $objTipoContatoRN->listarRN0337( $objTipoContatoDTO );
		foreach( $objTipoContatoDTO as $item ){
			$selected = ''; 
			if( $_POST['selTipoContextoContato'] == $item->getNumIdTipoContato() ){
				$selected = 'SELECTED';
			}
			if( $item->getStrSinSistema != "S" ) { 
				echo "<option value='" . $item->getNumIdTipoContato() . "' " . $selected . ">" . $item->getStrNome() . "</option>";
			}
		}
	}
  ?>
  </select>
  
  <?
  PaginaSEIExterna::getInstance()->fecharAreaDados();
  ?>
  
  <input type="hidden" name="hdnFlag" value="1" />
  <input type="hidden" name="hdnIdEdicao" id="hdnIdEdicao" value="" />  
  
  <?
  PaginaSEIExterna::getInstance()->montarAreaTabela($strResultado,$numRegistros);
  PaginaSEIExterna::getInstance()->montarAreaDebug();
  PaginaSEIExterna::getInstance()->montarBarraComandosInferior($arrComandos);

  ?>
</form>
<?
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
?>