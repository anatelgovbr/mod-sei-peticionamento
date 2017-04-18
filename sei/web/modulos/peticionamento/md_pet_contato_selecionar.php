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
  
  //seiv2
  //$objRelUnidadeTipoContContatoRN = new RelUnidadeTipoContContatoRN();
  //$objRelUnidadeTipoContContatoDTO = new RelUnidadeTipoContContatoDTO();
  //$objRelUnidadeTipoContContatoDTO->retNumIdTipoContextoContato();
  
  $objRelUnidadeTipoContContatoDTO->retNumIdTipoContato();
  $objRelUnidadeTipoContContatoDTO->setNumIdUnidade(SessaoSEIExterna::getInstance()->getNumIdUnidadeAtual());
  //$arrTiposContextosUnidade = InfraArray::converterArrInfraDTO($objRelUnidadeTipoContContatoRN->listarRN0547($objRelUnidadeTipoContContatoDTO),'IdTipoContextoContato');
  $arrTiposContextosUnidade = InfraArray::converterArrInfraDTO($objRelUnidadeTipoContContatoRN->listarRN0547($objRelUnidadeTipoContContatoDTO),'IdTipoContato');

  $objContatoDTO = new ContatoDTO();
  $objContatoDTO->retNumIdContato();
  //seiv2
  //$objContatoDTO->retNumIdContextoContato();
  //$objContatoDTO->retNumIdTipoContextoContatoContato();
  //$objContatoDTO->retStrNomeContextoContato();
  //$objContatoDTO->retStrSiglaContextoContato();
  //$objContatoDTO->retStrSinAtivoContextoContato();
  $objContatoDTO->retStrNome();
  $objContatoDTO->retStrSigla();
  $objContatoDTO->retStrEmail();
  
  //seiv2
  //$objContatoDTO->retStrExpressaoVocativo();
  //$objContatoDTO->retStrExpressaoTratamento();
  
  //alteracoes seiv3
  $objContatoDTO->retNumIdContato();
  $objContatoDTO->retNumIdTipoContato();
  $objContatoDTO->retNumIdUsuarioCadastro();
  
  $objContatoDTO->retStrExpressaoVocativoCargo();
  $objContatoDTO->retStrExpressaoTratamentoCargo();
  
  //seiv2
  //$objContatoDTO->retStrExpressaoTitulo();
  
  $objContatoDTO->retStrExpressaoCargo();
  
  //seiv2
  //$objContatoDTO->retStrSinContatosTipoContextoContato();
  //$objContatoDTO->retStrSinContatosTipoContextoContatoContato();
  //$objContatoDTO->retStrSinContexto();

  if (count($arrTiposContextosUnidade)) {
  	//seiv2
    //$objContatoDTO->adicionarCriterio(array('SinLiberadoTipoContextoContatoContato', 'IdTipoContextoContatoContato'),
    //    array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IN),
    //    array('S', $arrTiposContextosUnidade),
    //    InfraDTO::$OPER_LOGICO_OR);
    
  	//Verificar se novo campo SinAtivoTipoContato ou outro substitui anterior SinLiberadoTipoContextoContatoContato
    $objContatoDTO->adicionarCriterio(
        array('IdTipoContato'),
        array(InfraDTO::$OPER_IN),
        array($arrTiposContextosUnidade)
        );
  //seiv2
  //}else{
  //  $objContatoDTO->setStrSinLiberadoTipoContextoContatoContato('S');
  }

  //seiv2
  //$objContatoDTO->setStrSinAtivoTipoContextoContatoContato('S');
  //$objContatoDTO->setOrdStrNomeContextoContato(InfraDTO::$TIPO_ORDENACAO_ASC);
  //$objContatoDTO->setOrdStrSinContexto(InfraDTO::$TIPO_ORDENACAO_DESC);
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
        //seiv2
        //$objContatoDTO->adicionarCriterio(array('IdTipoContextoContato'),
        
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

/*
        //seiv2
        //if($dto->getStrSinContexto()=='S'){
          
          //$arrContextos[] = $dto->getNumIdContato();
          
        //}else{
          
          //se não mostrou contexto deste contato
          //seiv2
          //if (!in_array($dto->getNumIdContextoContato(),$arrContextos)){
          //alteracoes seiv3
          if (!in_array($dto->getNumIdContato(),$arrContextos)){
        
            //seiv2
            //$arrContextos[] = $dto->getNumIdContextoContato();            
            
          	//alteracoes seiv3
          	$arrContextos[] = $dto->getNumIdContato();
          	
          	$strResultado .= $strCssTr;      
            //seiv2
            //$strNomeSiglaContextoContato = ContatoINT::formatarNomeSiglaRI1224($dto->getStrNomeContextoContato(),$dto->getStrSiglaContextoContato());
            
          	//alteracoes seiv3
          	$strNomeSiglaContextoContato = ContatoINT::formatarNomeSiglaRI1224($dto->getStrNome(),$dto->getStrSigla());
          	
            if ($bolCheck){
              //seiv2
              //$strResultado .= '<td align="center">'.PaginaSEIExterna::getInstance()->getTrCheck($n,$dto->getNumIdContextoContato(),$strNomeSiglaContextoContato).'</td>';
              //alteracoes seiv3
              $strResultado .= '<td align="center">'.PaginaSEIExterna::getInstance()->getTrCheck($n,$dto->getNumIdContato(),$strNomeSiglaContato).'</td>';
            }

            $strResultado .= '<td>'.$strNegritoContextoIni.PaginaSEIExterna::getInstance()->formatarXHTML($strNomeSiglaContextoContato).$strNegritoContextoFim.'</td>';            
            $strResultado .= '<td align="center">';            

            //seiv2
            //$strResultado .= PaginaSEIExterna::getInstance()->getAcaoTransportarItem($n++,$dto->getNumIdContextoContato());
            
            //alteracoes seiv3
            $strResultado .= PaginaSEIExterna::getInstance()->getAcaoTransportarItem($n++,$dto->getNumIdContato());

            //seiv2
            //$strId = $dto->getNumIdContextoContato();
            
            //alteracoes seiv3
            $strId = $dto->getNumIdContato();
            
            $strDescricao = PaginaSEIExterna::getInstance()->formatarParametrosJavaScript($strNomeSiglaContextoContato);
            
            //seiv2
            //$bolTipoContextoUnidade = in_array($dto->getNumIdTipoContextoContatoContato(),$arrTiposContextosUnidade);
            
            //alteracoes seiv3
            $bolTipoContextoUnidade = in_array($dto->getNumIdTipoContato(),$arrTiposContextosUnidade);
            
            //se aceita contatos
            //seiv2

            //if($dto->getStrSinContatosTipoContextoContatoContato()=='S'){
            //  	if($_GET['acao']=='md_pet_contato_selecionar' || $_GET['acao']=='peticionamento_contato_listar'){
            //  		if($bolAcaoCadastrar && $bolTipoContextoUnidade){	      			
            //  			$strResultado .= '<a href="'.PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao_externa=contato_cadastrar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'].'&sin_contexto=N&id_tipo_contexto_contato='.$dto->getNumIdTipoContextoContatoContato().'&id_contexto_contato='.$dto->getNumIdContextoContato())).'" tabindex="'.PaginaSEIExterna::getInstance()->getProxTabTabela().'"><img src="/infra_css/imagens/mais.gif" title="Adicionar Contato" alt="Adicionar Contato" class="infraImg" /></a>&nbsp;';
            //  		}
            //  	}
            // } 

            $strResultado .= '</td></tr>'."\n";
          }
          
        //seiv2  
        //}
*/            
      $strResultado .= $strCssTr;
      $strTitle = '';
      $strNomeSigla = $dto->getStrNome();
      $strTitle = $strNomeSigla;
      
      if ($bolCheck){
        $strResultado .= '<td align="center">'.PaginaSEIExterna::getInstance()->getTrCheck($n,$dto->getNumIdContato(),$strTitle).'</td>';
      }
          
     $strResultado .= '<td>';
      
      //seiv2
      //if($dto->getStrSinContexto()=='S'){
        //$strResultado .= $strNegritoContextoIni;
      //}

      $strResultado .= PaginaSEIExterna::tratarHTML($strNomeSigla);

      //seiv2
      //if($dto->getStrSinContexto()=='S'){
        //$strResultado .= $strNegritoContextoFim;
      //}

      $strResultado .= '</td>';



      $strResultado .= '<td align="center">';

      //Alteração

      if (SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()==$dto->getNumIdUsuarioCadastro()){
      	//$strResultado .= "<a href='javascript:;' onclick=\"abrirCadastroInteressadoAlterar('" + arrDadosInteressado[0] +"', '" + arrDadosInteressado[1] +"', '"+ arrDadosInteressado[2] +"')\"><img title='Alterar Interessado' alt='Alterar Interessado' src='/infra_css/imagens/alterar.gif' class='infraImg' /></a>"	
      	$strResultado .= "<a href='javascript:;' onclick=\"abrirCadastroInteressadoAlterar('".$dto->getNumIdContato()."', 'Pessoa Física', '123.456.789-09')\"><img title='Alterar Interessado' alt='Alterar Interessado' src='/infra_css/imagens/alterar.gif' class='infraImg' /></a>";
      }

      $strResultado .= PaginaSEIExterna::getInstance()->getAcaoTransportarItem($n++,$dto->getNumIdContato());

      $strId = $dto->getNumIdContato();
      $strDescricao = PaginaSEIExterna::getInstance()->formatarParametrosJavaScript($strNomeSigla);
      
      //comentar
      /*
      if($dto->getStrSinContexto()=='S'){
	      
        $bolTipoContextoUnidade = in_array($dto->getNumIdTipoContextoContatoContato(),$arrTiposContextosUnidade);
        
        //se aceita contatos
        if($dto->getStrSinContatosTipoContextoContato()=='S'){
	      	if($_GET['acao']=='md_pet_contato_selecionar' || $_GET['acao']=='peticionamento_contato_listar'){
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
     				
	    }
	    */
      
	    //comentar
	    //else {
	      
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
	    	
	   // }   // comentar
	    
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
  
  $arrComandos[] = '<button type="button" accesskey="c" id="btnFechar" name="btnFechar" onclick="window.close();" value="Fechar" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
  
  $strItensSelGrupoContato = GrupoContatoINT::ConjuntoPorUnidadeRI0515('null','&nbsp;',$numIdGrupoContato);
  
  //seiv2
  //$strItensSelTipoContextoContato = TipoContextoContatoINT::montarSelectNomeRI0518('null','&nbsp;',$numTipoContextoContato);

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
$linkCadastroInteressado = PaginaSEIExterna::getInstance()->formatarXHTML( SessaoSEIExterna::getInstance()->assinarLink("controlador_externo.php?acao=md_pet_interessado_cadastro&tipo_selecao=2&cpf=true&cnpj=true&id_orgao_acesso_externo=0"));
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
  
  /*
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
  */
  
  return true;
}

function selecionarTipoContato(){
	document.getElementById('frmContatoLista').submit();
}

function pesquisar(){
   document.getElementById('frmContatoLista').submit();
}


function abrirCadastroInteressadoAlterar( id, tipo, cpfcnpj){

	//charmar janela para cadastrar um novo interessado
	$('#txtNomeRazaoSocial').val('');
	$('#hdnCustomizado').val('');
	$('#hdnIdEdicao').val( id );

	<?php 
	$strLinkEdicaoPF = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?edicao=true&acao=md_pet_interessado_cadastro&tipo_selecao=2&cpf=true&id_orgao_acesso_externo=0');
	$strLinkEdicaoPJ = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?edicao=true&acao=md_pet_interessado_cadastro&tipo_selecao=2&cnpj=true&id_orgao_acesso_externo=0');
	?>

	if( tipo == 'Pessoa Física' ){
		var str = '<?= $strLinkEdicaoPF ?>';
	}

	else if( tipo == 'Pessoa Jurídica' ){
		var str = '<?= $strLinkEdicaoPJ ?>';
	}
	
	infraAbrirJanela( str, 'cadastrarInteressado', 900, 900, '', false); //modal 
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
  <input type="text" id="txtPalavrasPesquisaContatos" name="txtPalavrasPesquisaContatos" class="infraText" value="<?=$strPalavrasPesquisa;?>" tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>" />

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