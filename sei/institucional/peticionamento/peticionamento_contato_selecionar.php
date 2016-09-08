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
  PaginaSEIExterna::getInstance()->prepararSelecao('peticionamento_contato_selecionar');
  SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);
  PaginaSEIExterna::getInstance()->salvarCamposPost(array('txtPalavrasPesquisaContatos','selGrupoContato','txtNascimentoInicio','txtNascimentoFim', 'selTipoContextoContato'));
   
  //trazer os tipos de contatos parametrizados na administraçao do modulo para com isso aplicar:
  //- na combo trazer apenas os tipos parametrizados
  //- na lista de registros trazer apenas aqueles que pertençam a algum dos tipos parametrizados do modulo  
  $idTipoProc = $_GET['id_tipo_processo_peticionamento'];
  $objRelTipoContextoPeticionamentoDTO = new RelTipoContextoPeticionamentoDTO();
  $objRelTipoContextoPeticionamentoRN = new GerirTipoContextoPeticionamentoRN();
  $objRelTipoContextoPeticionamentoDTO->retTodos();
  $objRelTipoContextoPeticionamentoDTO->setStrSinSelecaoInteressado('S');
  $arrobjRelTipoContextoPeticionamentoDTO = $objRelTipoContextoPeticionamentoRN->listar( $objRelTipoContextoPeticionamentoDTO );
  //print_r( $arrobjRelTipoContextoPeticionamentoDTO ); die();  
  
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

    case 'peticionamento_contato_selecionar':
      
      if ($_GET['acao']=='peticionamento_contato_selecionar'){
        $strTitulo = PaginaSEIExterna::getInstance()->getTituloSelecao('Selecionar Contato','Selecionar Contatos');
      } 
      
      break;
       
    case 'peticionamento_contato_listar':
            
       $strTitulo = 'Contatos';
       break;

    default:
      throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
  }

  $objRelUnidadeTipoContContatoRN = new RelUnidadeTipoContContatoRN();
  $objRelUnidadeTipoContContatoDTO = new RelUnidadeTipoContContatoDTO();
  $objRelUnidadeTipoContContatoDTO->retNumIdTipoContextoContato();
  $objRelUnidadeTipoContContatoDTO->setNumIdUnidade(SessaoSEIExterna::getInstance()->getNumIdUnidadeAtual());
  $arrTiposContextosUnidade = InfraArray::converterArrInfraDTO($objRelUnidadeTipoContContatoRN->listarRN0547($objRelUnidadeTipoContContatoDTO),'IdTipoContextoContato');

  $objContatoDTO = new ContatoDTO();
  $objContatoDTO->retNumIdContato();
  $objContatoDTO->retNumIdContextoContato();
  $objContatoDTO->retNumIdTipoContextoContatoContato();
  $objContatoDTO->retStrNomeContextoContato();
  $objContatoDTO->retStrSiglaContextoContato();
  $objContatoDTO->retStrSinAtivoContextoContato();
  $objContatoDTO->retStrNome();
  $objContatoDTO->retStrSigla();
  $objContatoDTO->retStrEmail();
  $objContatoDTO->retStrExpressaoVocativo();
  $objContatoDTO->retStrExpressaoTratamento();
  $objContatoDTO->retStrExpressaoTitulo();
  $objContatoDTO->retStrExpressaoCargo();
  $objContatoDTO->retStrSinContatosTipoContextoContato();
  $objContatoDTO->retStrSinContatosTipoContextoContatoContato();
  $objContatoDTO->retStrSinContexto();

  if (count($arrTiposContextosUnidade)) {
    $objContatoDTO->adicionarCriterio(array('SinLiberadoTipoContextoContatoContato', 'IdTipoContextoContatoContato'),
        array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IN),
        array('S', $arrTiposContextosUnidade),
        InfraDTO::$OPER_LOGICO_OR);
  }else{
    $objContatoDTO->setStrSinLiberadoTipoContextoContatoContato('S');
  }

  $objContatoDTO->setStrSinAtivoTipoContextoContatoContato('S');
  $objContatoDTO->setOrdStrNomeContextoContato(InfraDTO::$TIPO_ORDENACAO_ASC);
  $objContatoDTO->setOrdStrSinContexto(InfraDTO::$TIPO_ORDENACAO_DESC);
  $objContatoDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);

  $arrComandos = array();
 
  $arrComandos[] = '<button type="submit" accesskey="P" id="btnPesquisar" name="btnPesquisar" value="Pesquisar" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';        
  
  if (PaginaSEIExterna::getInstance()->isBolPaginaSelecao()){
      $arrComandos[] = '<button type="button" accesskey="T" id="btnTransportarSelecao" value="Transportar" onclick="infraTransportarSelecao();" class="infraButton"><span class="infraTeclaAtalho">T</span>ransportar</button>';
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
        $objContatoDTO->setNumIdTipoContextoContatoContato($numTipoContextoContato);
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
      $arrComandos[] = '<button type="button" accesskey="I" id="btnImprimir" value="Imprimir" onclick="infraImprimirTabela();" class="infraButton"><span class="infraTeclaAtalho">I</span>mprimir</button>';
    }

    $strCaptionTabela = '';
    if ($_GET['acao'] == 'peticionamento_contato_listar'){
      $strSumarioTabela = 'Tabela de Contatos.';
      $strCaptionTabela .= 'Contatos';
    }else{
      $strSumarioTabela = 'Tabela de Contatos.';
      $strCaptionTabela .= 'Contatos';
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
      
        if($dto->getStrSinContexto()=='S'){
          
          $arrContextos[] = $dto->getNumIdContato();
          
        }else{
          
          //se não mostrou contexto deste contato
          if (!in_array($dto->getNumIdContextoContato(),$arrContextos)){
            
            $arrContextos[] = $dto->getNumIdContextoContato();            
            $strResultado .= $strCssTr;      
            $strNomeSiglaContextoContato = ContatoINT::formatarNomeSiglaRI1224($dto->getStrNomeContextoContato(),$dto->getStrSiglaContextoContato());
            
            if ($bolCheck){
              $strResultado .= '<td align="center">'.PaginaSEIExterna::getInstance()->getTrCheck($n,$dto->getNumIdContextoContato(),$strNomeSiglaContextoContato).'</td>';
            }
            
        	$strResultado .= '<td>'.$strNegritoContextoIni.PaginaSEIExterna::getInstance()->formatarXHTML($strNomeSiglaContextoContato).$strNegritoContextoFim.'</td>';            
            $strResultado .= '<td align="center">';            
            $strResultado .= PaginaSEIExterna::getInstance()->getAcaoTransportarItem($n++,$dto->getNumIdContextoContato());

            $strId = $dto->getNumIdContextoContato();
            $strDescricao = PaginaSEIExterna::getInstance()->formatarParametrosJavaScript($strNomeSiglaContextoContato);
            
            $bolTipoContextoUnidade = in_array($dto->getNumIdTipoContextoContatoContato(),$arrTiposContextosUnidade);
            
            //se aceita contatos
            if($dto->getStrSinContatosTipoContextoContatoContato()=='S'){
    	      	if($_GET['acao']=='peticionamento_contato_selecionar' || $_GET['acao']=='peticionamento_contato_listar'){
    	      		if($bolAcaoCadastrar && $bolTipoContextoUnidade){	      			
    	      			$strResultado .= '<a href="'.PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao_externa=contato_cadastrar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'].'&sin_contexto=N&id_tipo_contexto_contato='.$dto->getNumIdTipoContextoContatoContato().'&id_contexto_contato='.$dto->getNumIdContextoContato())).'" tabindex="'.PaginaSEIExterna::getInstance()->getProxTabTabela().'"><img src="/infra_css/imagens/mais.gif" title="Adicionar Contato" alt="Adicionar Contato" class="infraImg" /></a>&nbsp;';
    	      		}
    	      	}
    	      }
    	      
    	      if ($bolAcaoConsultarContexto){
    	        $strResultado .= '<a href="'.PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao_externa=contexto_consultar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'].'&id_tipo_contexto_contato='.$dto->getNumIdTipoContextoContatoContato().'&id_contato='.$dto->getNumIdContextoContato().'&sin_contexto=S')).'" tabindex="'.PaginaSEIExterna::getInstance()->getProxTabTabela().'"><img src="imagens/consultar.gif" title="Consultar Contexto" alt="Consultar Contexto" class="infraImg" /></a>&nbsp;';
    	      }
      		
    		   $strResultado .= '</td></tr>'."\n";
          }
        }
            
      $strResultado .= $strCssTr;
      $strTitle = '';
      $strNomeSigla = ContatoINT::formatarNomeSiglaRI1224($dto->getStrNome(),$dto->getStrSigla());
      $strTitle = $strNomeSigla;
      
      if ($bolCheck){
        $strResultado .= '<td align="center">'.PaginaSEIExterna::getInstance()->getTrCheck($n,$dto->getNumIdContato(),$strTitle).'</td>';
      }
          
     $strResultado .= '<td>';

      if($dto->getStrSinContexto()=='S'){
        $strResultado .= $strNegritoContextoIni;
      }

      $strResultado .= PaginaSEIExterna::getInstance()->formatarXHTML($strNomeSigla);
      
      if($dto->getStrSinContexto()=='S'){
        $strResultado .= $strNegritoContextoFim;
      }
      
      $strResultado .= '</td>';            
      $strResultado .= '<td align="center">';      
      $strResultado .= PaginaSEIExterna::getInstance()->getAcaoTransportarItem($n++,$dto->getNumIdContato());
      
      $strId = $dto->getNumIdContato();
      $strDescricao = PaginaSEIExterna::getInstance()->formatarParametrosJavaScript($strNomeSigla);
      
      if($dto->getStrSinContexto()=='S'){
	      
        $bolTipoContextoUnidade = in_array($dto->getNumIdTipoContextoContatoContato(),$arrTiposContextosUnidade);
        
        //se aceita contatos
        if($dto->getStrSinContatosTipoContextoContato()=='S'){
	      	if($_GET['acao']=='peticionamento_contato_selecionar' || $_GET['acao']=='peticionamento_contato_listar'){
	      		if($bolAcaoCadastrar && $bolTipoContextoUnidade){	      			
	      			$strResultado .= '<a href="'.PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao_externa=contato_cadastrar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'].'&sin_contexto=N&id_tipo_contexto_contato='.$dto->getNumIdTipoContextoContatoContato().'&id_contexto_contato='.$dto->getNumIdContextoContato())).'" tabindex="'.PaginaSEIExterna::getInstance()->getProxTabTabela().'"><img src="/infra_css/imagens/mais.gif" title="Adicionar Contato" alt="Adicionar Contato" class="infraImg" /></a>&nbsp;';
	      		}
	      	}
	      }
	      	      
	    if( $_GET['acao']=='peticionamento_contato_listar'){ 
	    	  
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
	      
	      if ($bolAcaoConsultarContexto){
	        $strResultado .= '<a href="'.PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao_externa=contexto_consultar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'].'&id_tipo_contexto_contato='.$dto->getNumIdTipoContextoContatoContato().'&id_contato='.$dto->getNumIdContextoContato().'&sin_contexto=S')).'" tabindex="'.PaginaSEIExterna::getInstance()->getProxTabTabela().'"><img src="imagens/consultar.gif" title="Consultar Contexto" alt="Consultar Contexto" class="infraImg" /></a>&nbsp;';
	      }
     				
	    }else {
	      
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
	    	
		    if ($bolAcaoConsultar){
		      $strResultado .= '<a href="'.PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao_externa=contato_consultar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'].'&id_tipo_contexto_contato='.$dto->getNumIdTipoContextoContatoContato().'&id_contexto_contato='.$dto->getNumIdContextoContato().'&id_contato='.$dto->getNumIdContato().'&sin_contexto=N')).'" tabindex="'.PaginaSEIExterna::getInstance()->getProxTabTabela().'"><img src="imagens/consultar.gif" title="Consultar Contato" alt="Consultar Contato" class="infraImg" /></a>&nbsp;';
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
  
  $arrComandos[] = '<button type="button" accesskey="F" id="btnFechar" value="Fechar" onclick="window.close();" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';
  
  $strItensSelGrupoContato = GrupoContatoINT::ConjuntoPorUnidadeRI0515('null','&nbsp;',$numIdGrupoContato);
  $strItensSelTipoContextoContato = TipoContextoContatoINT::montarSelectNomeRI0518('null','&nbsp;',$numTipoContextoContato);

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
?>

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
  if (document.getElementById('chkMaisOpcoesContatos').checked){
      
    var dataNascIni = infraTrim(document.getElementById('txtNascimentoInicio').value);
    var dataNascFim = infraTrim(document.getElementById('txtNascimentoFim').value);
    
    if (dataNascIni!='' || dataNascFim!=''){
      if (dataNascIni==''){
        alert('Informe data inicial do período de nascimento.');
        document.getElementById('txtNascimentoInicio').focus();
        return false;
      }
    }
  }
  return true;
}

function selecionarTipoContato(){
	document.getElementById('frmContatoLista').submit();
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
  
  <label id="lblPalavrasPesquisaContatos" for="txtPalavrasPesquisaContatos" accesskey="" class="infraLabelOpcional">Palavras-chave para pesquisa:</label>
  <input type="text" id="txtPalavrasPesquisaContatos" name="txtPalavrasPesquisaContatos" class="infraText" value="<?=$strPalavrasPesquisa;?>" tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>" />

  <label id="lblTipoContextoContato" for="selTipoContextoContato" accesskey="" class="infraLabelOpicional">Tipo de Contato:</label>
  <select id="selTipoContextoContato" name="selTipoContextoContato" class="infraSelect" 
          onchange="selecionarTipoContato()" 
          tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>" >
  <option></option>
  <? 
  $objTipoContextoContatoRN = new TipoContextoContatoRN();
  foreach( $arrobjRelTipoContextoPeticionamentoDTO as $item ){ 
       $dto = new TipoContextoContatoDTO();
	   $dto->setNumIdTipoContextoContato( $item->getNumIdTipoContextoContato() );
	   $dto->retTodos();
	   $dto = $objTipoContextoContatoRN->consultarRN0336( $dto );
	   $selected = "";
	   
	   if( $_POST['selTipoContextoContato'] == $item->getNumIdTipoContextoContato() ){
		   $selected = " selected='selected' ";
	   }
	   
	   //print_r( $dto ); die();
  ?>
	 <option value="<?= $item->getNumIdTipoContextoContato() ?>" <?= $selected ?> >
     <?= $dto->getStrNome() ?>
     </option>	  
  <? } ?>
  
  <?//=$strItensSelTipoContextoContato?>
  </select>
  
  <?
  PaginaSEIExterna::getInstance()->fecharAreaDados();
  ?>
  
  <input type="hidden" name="hdnFlag" value="1" />  
  
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