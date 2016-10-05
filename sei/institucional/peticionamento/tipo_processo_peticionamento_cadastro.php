<?
/**
* ANATEL
*
* 15/04/2016 - criado por jaqueline.mendes@cast.com.br - CAST
*
*/

try {
  require_once dirname(__FILE__).'/../../SEI.php';

  session_start();

  SessaoSEI::getInstance()->validarLink();

  PaginaSEI::getInstance()->verificarSelecao('tipo_processo_peticionamento_selecionar');

  SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

  $strDesabilitar = '';

  $arrComandos = array();
  //Tipo Processo - Nivel de Acesso
  $strLinkAjaxNivelAcesso     = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=nivel_acesso_auto_completar');
  
  //Tipo Documento Complementar
  $strLinkTipoDocumentoSelecao  = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=serie_peticionamento_selecionar&filtro=1&tipo_selecao=2&id_object=objLupaTipoDocumento&tipoDoc=E');
  $strLinkAjaxTipoDocumento     = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=serie_peticionamento_auto_completar');
  
  //Tipo de Documento Essencial
  $strLinkTipoDocumentoEssencialSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=serie_peticionamento_selecionar&filtro=1&tipo_selecao=2&id_object=objLupaTipoDocumentoEssencial&tipoDoc=E');
  
  //Tipo Processo
  $strLinkTipoProcessoSelecao   = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=tipo_procedimento_selecionar&tipo_selecao=1&id_object=objLupaTipoProcesso');
  $strLinkAjaxTipoProcesso      =  SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=tipo_processo_auto_completar');
  
  //Unidade
  $strLinkUnidadeSelecao        = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=unidade_selecionar_todas&tipo_selecao=1&id_object=objLupaUnidade');
  $strLinkUnidadeMultiplaSelecao= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=unidade_selecionar_todas&tipo_selecao=1&id_object=objLupaUnidadeMultipla');
  $strLinkAjaxUnidade           =  SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=unidade_auto_completar');
  
  //Tipo Documento Principal
  $strLinkTipoDocPrincSelecao  = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=serie_peticionamento_selecionar&filtro=1&tipoDoc=E&tipo_selecao=1&id_object=objLupaTipoDocPrinc');
  $strLinkAjaxTipoDocPrinc     =  SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=serie_peticionamento_auto_completar');
  
  //Preparar Preenchimento Alteração
  $idMdPetTipoProcesso = '';
  $nomeTipoProcesso = '';
  $idTipoProcesso   = '';
  $orientacoes      = '';
  $idUnidade        = '';
  $nomeUnidade      = '';
  $sinIndIntUsExt   = '';
  $sinIndIntIndIndir= '';
  $sinIndIntIndConta= '';
  $sinIndIntIndCpfCn= '';
  $sinNAUsuExt      = '';
  $sinNAPadrao      = '';
  $hipoteseLegal    = 'style="display:none;"';
  $gerado           = '';
  $externo          = '';
  $nomeSerie        = '';
  $idSerie          = '';
  $strItensSelSeries= '';
  $unica            = false;
  $mutipla          = false;
  $arrObjUnidadesMultiplas = array();
  $alterar          = false;
  
  $strItensSelNivelAcesso = '';
  $strItensSelHipoteseLegal  = '';
  //$strItensSelHipoteseLegal  = TipoProcessoPeticionamentoINT::montarSelectHipoteseLegal(null, null, ProtocoloRN::$NA_RESTRITO );
  
  //Preencher Array de Unidades para buscar posteriormente
  $objUnidadeDTO = new UnidadeDTO();
  $objUnidadeDTO = new UnidadeDTO();
  $objUnidadeDTO->retNumIdUnidade();
  $objUnidadeDTO->retStrSigla();
  $objUnidadeDTO->retStrDescricao();
  $objUnidadeDTO->retStrSiglaUf();
  
  $objUnidadeRN = new UnidadeRN();

  $arrObjUnidadeDTO = $objUnidadeRN->listarTodasComFiltro($objUnidadeDTO);
  
  foreach($arrObjUnidadeDTO as  $key => $objUnidadeDTO){
  		$arrObjUnidadeDTOFormatado[$objUnidadeDTO->getNumIdUnidade()]['sigla'] = $objUnidadeDTO->getStrSigla();
  		$arrObjUnidadeDTOFormatado[$objUnidadeDTO->getNumIdUnidade()]['descricao'] = utf8_encode($objUnidadeDTO->getStrDescricao());
  		$arrObjUnidadeDTOFormatado[$objUnidadeDTO->getNumIdUnidade()]['uf'] = $objUnidadeDTO->getStrSiglaUf();
  }
  
  
  $objInfraParametroDTO = new InfraParametroDTO();
  $objInfraParametroRN  = new InfraParametroRN();
  $objInfraParametroDTO->retTodos();
  $objInfraParametroDTO->setStrNome('SEI_HABILITAR_HIPOTESE_LEGAL');
  $objInfraParametroDTO = $objInfraParametroRN->consultar($objInfraParametroDTO);
  $valorParametroHipoteseLegal = $objInfraParametroDTO->getStrValor();

  if($_GET['acao'] === 'tipo_processo_peticionamento_consultar' || $_GET['acao'] === 'tipo_processo_peticionamento_alterar'){
  
  if (isset($_GET['id_tipo_processo_peticionamento'])){
  	$alterar = true;
  	$objTipoProcessoPeticionamentoDTO = new TipoProcessoPeticionamentoDTO();
  	$objTipoProcessoPeticionamentoDTO->setNumIdTipoProcessoPeticionamento($_GET['id_tipo_processo_peticionamento']);
  	$objTipoProcessoPeticionamentoDTO->retTodos();
  	$objTipoProcessoPeticionamentoDTO->retStrNomeProcesso();
  	//$objTipoProcessoPeticionamentoDTO->retStrSiglaUnidade();
  	$objTipoProcessoPeticionamentoDTO->retStrNomeSerie();
  	 
  	$objTipoProcessoPeticionamentoRN = new TipoProcessoPeticionamentoRN();
  	$objTipoProcessoPeticionamentoDTO = $objTipoProcessoPeticionamentoRN->consultar($objTipoProcessoPeticionamentoDTO);	
  	
  	
  	$strItensSelHipoteseLegal  = TipoProcessoPeticionamentoINT::montarSelectHipoteseLegal(null, null, $objTipoProcessoPeticionamentoDTO->getNumIdHipoteseLegal() );
  	
  	//Carregando Unidades
  $objRelTipoProcessoUnidadePeticionamentoDTO = new RelTipoProcessoUnidadePeticionamentoDTO();
  $objRelTipoProcessoUnidadePeticionamentoDTO->setNumIdTipoProcessoPeticionamento($_GET['id_tipo_processo_peticionamento']);
  $objRelTipoProcessoUnidadePeticionamentoDTO->retTodos();
  	
  $objRelTipoProcessoUnidadePeticionamentoRN = new RelTipoProcessoUnidadePeticionamentoRN();
  $arrObjRelTipoProcessoUnidadePeticionamentoDTO = $objRelTipoProcessoUnidadePeticionamentoRN->listar($objRelTipoProcessoUnidadePeticionamentoDTO);
 
  if(count($arrObjRelTipoProcessoUnidadePeticionamentoDTO) > 0){
  $unica    = $arrObjRelTipoProcessoUnidadePeticionamentoDTO[0]->getStrStaTipoUnidade() === TipoProcessoPeticionamentoRN::$UNIDADE_UNICA ? true : false;
  $multipla = $arrObjRelTipoProcessoUnidadePeticionamentoDTO[0]->getStrStaTipoUnidade() === TipoProcessoPeticionamentoRN::$UNIDADES_MULTIPLAS ? true : false; 
  
  $objUnidadeRN = new UnidadeRN();
  	if($unica){
  		$idUnidade      = $arrObjRelTipoProcessoUnidadePeticionamentoDTO[0]->getNumIdUnidade();
  		$objUnidadeDTO = new UnidadeDTO();
  		$objUnidadeDTO->setNumIdUnidade($idUnidade);
  		$objUnidadeDTO->retTodos();
  		$objUnidadeDTO = $objUnidadeRN->consultarRN0125($objUnidadeDTO);
  		$nomeUnidade = $objUnidadeDTO->getStrSigla() . ' - ' . $objUnidadeDTO->getStrDescricao(); 
  		
  	}
  	
  	if($multipla){
  		foreach($arrObjRelTipoProcessoUnidadePeticionamentoDTO as $objRelUnidade){
  			$idUnidade = $objRelUnidade->getNumIdUnidade();
  			$objUnidadeDTO = new UnidadeDTO();
  			$objUnidadeDTO->setNumIdUnidade($idUnidade);
  			$objUnidadeDTO->retTodos();
  			$objUnidadeDTO->retStrSiglaUf();
  			$objUnidadeDTO  = $objUnidadeRN->consultarRN0125($objUnidadeDTO);
  			$arrObjUnidadesMultiplas[] = $objUnidadeDTO;
  		}
  	
  	}
  	
  }
  	
  	$idMdPetTipoProcesso   = $_GET['id_tipo_processo_peticionamento'];
  	$nomeTipoProcesso = $objTipoProcessoPeticionamentoDTO->getStrNomeProcesso();
  	$idTipoProcesso   = $objTipoProcessoPeticionamentoDTO->getNumIdProcedimento();
  	$orientacoes      = $objTipoProcessoPeticionamentoDTO->getStrOrientacoes();
  	$idUnidade        = $unica ? $arrObjRelTipoProcessoUnidadePeticionamentoDTO[0]->getNumIdUnidade() : null;
  	//$nomeUnidade      = $objTipoProcessoPeticionamentoDTO->getStrSiglaUnidade();
  	$sinIndIntUsExt   = $objTipoProcessoPeticionamentoDTO->getStrSinIIProprioUsuarioExterno() == 'S' ? 'checked = checked' : '';
  	$sinIndIntIndIndir= $objTipoProcessoPeticionamentoDTO->getStrSinIIIndicacaoDireta() == 'S' ? 'checked = checked' : '';
  	$sinIndIntIndConta= $objTipoProcessoPeticionamentoDTO->getStrSinIIIndicacaoDiretaContato() == 'S' ? 'checked = checked' : '';
  	$sinIndIntIndCpfCn= $objTipoProcessoPeticionamentoDTO->getStrSinIIIndicacaoDiretaCpfCnpj() == 'S' ? 'checked = checked' : '';
  	$sinNAUsuExt      = $objTipoProcessoPeticionamentoDTO->getStrSinNaUsuarioExterno() == 'S' ? 'checked = checked' : '';
  	$sinNAPadrao      = $objTipoProcessoPeticionamentoDTO->getStrSinNaPadrao() == 'S' ? 'checked = checked' : '';
  	$gerado           = $objTipoProcessoPeticionamentoDTO->getStrSinDocGerado() == 'S' ? 'checked = checked' : '';
  	$externo          = $objTipoProcessoPeticionamentoDTO->getStrSinDocExterno() == 'S' ? 'checked = checked' : '';
  	$nomeSerie        = $objTipoProcessoPeticionamentoDTO->getStrNomeSerie();
  	$idSerie          = $objTipoProcessoPeticionamentoDTO->getNumIdSerie();
  	
  	$hipoteseLegal    = $objTipoProcessoPeticionamentoDTO->getStrStaNivelAcesso() === ProtocoloRN::$NA_RESTRITO && $valorParametroHipoteseLegal != '0' ? 'style="display:inherit"' : 'style="display:none"';
  	 
  	$strItensSelNivelAcesso  = TipoProcessoPeticionamentoINT::montarSelectNivelAcesso(null, null, $objTipoProcessoPeticionamentoDTO->getStrStaNivelAcesso(), $idTipoProcesso);
  	  	
  	$objRelTipoProcessoSerieRN = new RelTipoProcessoSeriePeticionamentoRN();
  	
  	$objRelTipoProcessoSerieDTO = new RelTipoProcessoSeriePeticionamentoDTO();
  	$objRelTipoProcessoSerieDTO->retTodos();
  	$objRelTipoProcessoSerieDTO->retStrNomeSerie();
  	$objRelTipoProcessoSerieDTO->setStrStaTipoDoc(RelTipoProcessoSeriePeticionamentoRN::$DOC_COMPLEMENTAR);
  	$objRelTipoProcessoSerieDTO->setNumIdTipoProcessoPeticionamento($_GET['id_tipo_processo_peticionamento']);
  	$objRelTipoProcessoSerieDTO->setOrdStrNomeSerie(InfraDTO::$TIPO_ORDENACAO_ASC);
  	
  	$arrSeries = $objRelTipoProcessoSerieRN->listar( $objRelTipoProcessoSerieDTO );
  	$objTipoProcessoPeticionamentoDTO->setArrObjRelTipoProcessoSerieDTO( $arrSeries );
  	 
  	$strItensSelSeries = "";
  	for($x = 0;$x<count($arrSeries);$x++){
  		$strItensSelSeries .= "<option value='" . $arrSeries[$x]->getNumIdSerie() .  "'>" . $arrSeries[$x]->getStrNomeSerie(). "</option>";
  	}
  	
  	//documento essencial 
    $objRelTipoProcessoSerieEssDTO = new RelTipoProcessoSeriePeticionamentoDTO();
  	$objRelTipoProcessoSerieEssDTO->retTodos();
  	$objRelTipoProcessoSerieEssDTO->retStrNomeSerie();
  	$objRelTipoProcessoSerieEssDTO->setStrStaTipoDoc(RelTipoProcessoSeriePeticionamentoRN::$DOC_ESSENCIAL);
  	$objRelTipoProcessoSerieEssDTO->setNumIdTipoProcessoPeticionamento($_GET['id_tipo_processo_peticionamento']);
  	$objRelTipoProcessoSerieEssDTO->setOrdStrNomeSerie(InfraDTO::$TIPO_ORDENACAO_ASC);
  	
  	$arrSeriesEss = $objRelTipoProcessoSerieRN->listar( $objRelTipoProcessoSerieEssDTO );
  	$objTipoProcessoPeticionamentoDTO->setArrObjRelTipoProcessoSerieEssDTO( $arrSeriesEss );
  	
  	$strItensSelSeriesEss = "";
  	for($x = 0;$x<count($arrSeriesEss);$x++){
  		$strItensSelSeriesEss .= "<option value='" . $arrSeriesEss[$x]->getNumIdSerie() .  "'>" . $arrSeriesEss[$x]->getStrNomeSerie(). "</option>";
  	}
  }
 }
  
  switch($_GET['acao']){
    
  	case 'tipo_processo_peticionamento_cadastrar':

  	 $strItensSelHipoteseLegal  = TipoProcessoPeticionamentoINT::montarSelectHipoteseLegal(null, null, null );
  		
     //Carregando campos select
     $strItensSelTipoProcesso = TipoProcessoPeticionamentoINT::montarSelectTipoProcesso(null, null, $_POST['selTipoProcesso']);
     $strItensSelUnidades     = UnidadeINT::montarSelectSiglaDescricao(null, null, $_POST['selUnidade']);
     
     $strItensSelDoc          = SerieINT::montarSelectNomeRI0802(null, null, $_POST['selDocumento']);
    
     $objTipoProcessoPeticionamentoDTO = new TipoProcessoPeticionamentoDTO();
	 $objTipoProcessoPeticionamentoRN = new TipoProcessoPeticionamentoRN();
	
     $strTitulo = 'Novo Tipo de Processo para Peticionamento';

	  $arrComandos[] = '<button type="submit" accesskey="s" name="sbmCadastrarTpProcessoPeticionamento" id="sbmCadastrarTpProcessoPeticionamento" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
      $arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'])).'\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';
     
      $objTipoProcessoPeticionamentoDTO->setNumIdProcedimento($_POST['hdnIdTipoProcesso']);
      $objTipoProcessoPeticionamentoDTO->setStrOrientacoes($_POST['txtOrientacoes']);
      //$objTipoProcessoPeticionamentoDTO->setNumIdUnidade($_POST['hdnIdUnidade']);
      
      $objTipoProcessoPeticionamentoDTO->setStrSinIIProprioUsuarioExterno('N');
      $objTipoProcessoPeticionamentoDTO->setStrSinIIIndicacaoDireta('N');
      $objTipoProcessoPeticionamentoDTO->setStrSinIIIndicacaoDiretaCpfCnpj('N');
      $objTipoProcessoPeticionamentoDTO->setStrSinIIIndicacaoDiretaContato('N');
      $objTipoProcessoPeticionamentoDTO->setStrSinNaUsuarioExterno('N');
      $objTipoProcessoPeticionamentoDTO->setStrSinNaPadrao('N');
      $objTipoProcessoPeticionamentoDTO->setStrSinDocGerado('N');
      $objTipoProcessoPeticionamentoDTO->setStrSinDocExterno('N');
      
      // indicacao interessado
      if($_POST['indicacaoInteressado'][0] == 1)
      	$objTipoProcessoPeticionamentoDTO->setStrSinIIProprioUsuarioExterno('S');
      if($_POST['indicacaoInteressado'][0] == 2)
      	$objTipoProcessoPeticionamentoDTO->setStrSinIIIndicacaoDireta('S');
      if($_POST['indicacaoIndireta'][0] == 3)
      	$objTipoProcessoPeticionamentoDTO->setStrSinIIIndicacaoDiretaCpfCnpj('S');
      if($_POST['indicacaoIndireta'][0] == 4)
      	$objTipoProcessoPeticionamentoDTO->setStrSinIIIndicacaoDiretaContato('S');
      
      // nivel de acesso
      if($_POST['rdNivelAcesso'][0] == 1)
      	$objTipoProcessoPeticionamentoDTO->setStrSinNaUsuarioExterno('S');
      if($_POST['rdNivelAcesso'][0] == 2)
      	$objTipoProcessoPeticionamentoDTO->setStrSinNaPadrao('S');
      
      $objTipoProcessoPeticionamentoDTO->setNumIdHipoteseLegal(null);
      
      if($_POST['selNivelAcesso'] != '') {
      	
      	$objTipoProcessoPeticionamentoDTO->setStrStaNivelAcesso($_POST['selNivelAcesso']);
      	
      	if( $_POST['selNivelAcesso'] == ProtocoloRN::$NA_RESTRITO && $valorParametroHipoteseLegal != '0'){
      		$objTipoProcessoPeticionamentoDTO->setNumIdHipoteseLegal($_POST['selHipoteseLegal']);
      	} else {
      		$objTipoProcessoPeticionamentoDTO->setNumIdHipoteseLegal(null);
      	}
      	
      } 
      
      //Tipo de Documento Principal
      if($_POST['rdDocPrincipal'][0] == 1) { // campos: modelo, tipo de documento principal
      	$objTipoProcessoPeticionamentoDTO->setStrSinDocGerado('S');
        $objTipoProcessoPeticionamentoDTO->setStrSinDocExterno('N');
              
      } else if($_POST['rdDocPrincipal'][0] == 2) { //campos: tipo de documento principal
      	$objTipoProcessoPeticionamentoDTO->setStrSinDocGerado('N');
      	$objTipoProcessoPeticionamentoDTO->setStrSinDocExterno('S'); 
      }
      
      $objTipoProcessoPeticionamentoDTO->setNumIdSerie($_POST['hdnIdTipoDocPrinc']);      
      $objTipoProcessoPeticionamentoDTO->setStrSinAtivo('S');
      										
      if (isset($_POST['sbmCadastrarTpProcessoPeticionamento'])) {
        try{
        	$arrIdTipoDocumento          = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnSerie']);
        	$arrIdTipoDocumentoEssencial = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnSerieEssencial']);
        	$arrIdUnidadesSelecionadas   = $_POST['hdnUnidadesSelecionadas'] != '' ? json_decode($_POST['hdnUnidadesSelecionadas']) : array();
        	//para nao limpar os campos em caso de erro de duplicidade
        	//print_r( $_POST ); die();
        	$tipoUnidade     =  is_array($_POST['rdUnidade']) ? current($_POST['rdUnidade']) : array();
        	$nomeTipoProcesso = $_POST['txtTipoProcesso'];
        	$idTipoProcesso   = $objTipoProcessoPeticionamentoDTO->getNumIdProcedimento();
        	$orientacoes      = $objTipoProcessoPeticionamentoDTO->getStrOrientacoes();
        	$nomeUnidade      = $_POST['txtUnidade'];
        	//$nomeUnidade     = $objTipoProcessoPeticionamentoDTO->getStrSiglaUnidade();
        	$sinIndIntUsExt   = $objTipoProcessoPeticionamentoDTO->getStrSinIIProprioUsuarioExterno() == 'S' ? 'checked = checked' : '';
        	$sinIndIntIndIndir= $objTipoProcessoPeticionamentoDTO->getStrSinIIIndicacaoDireta() == 'S' ? 'checked = checked' : '';
        	$sinIndIntIndConta= $objTipoProcessoPeticionamentoDTO->getStrSinIIIndicacaoDiretaContato() == 'S' ? 'checked = checked' : '';
        	$sinIndIntIndCpfCn= $objTipoProcessoPeticionamentoDTO->getStrSinIIIndicacaoDiretaCpfCnpj() == 'S' ? 'checked = checked' : '';
        	$sinNAUsuExt      = $objTipoProcessoPeticionamentoDTO->getStrSinNaUsuarioExterno() == 'S' ? 'checked = checked' : '';
        	$sinNAPadrao      = $objTipoProcessoPeticionamentoDTO->getStrSinNaPadrao() == 'S' ? 'checked = checked' : '';
        	$gerado           = $objTipoProcessoPeticionamentoDTO->getStrSinDocGerado() == 'S' ? 'checked = checked' : '';
        	$externo          = $objTipoProcessoPeticionamentoDTO->getStrSinDocExterno() == 'S' ? 'checked = checked' : '';
        	$nomeSerie        = $_POST['txtTipoDocPrinc'];
        	//$nomeSerie        = $objTipoProcessoPeticionamentoDTO->getStrNomeSerie();
        	$idSerie          = $objTipoProcessoPeticionamentoDTO->getNumIdSerie();
        	$multipla         = $tipoUnidade == 'M' ? true : false;
        	$unica            = $tipoUnidade == 'U' ? true : false;
        	$hdnCorpoTabela   = isset($_POST['hdnCorpoTabela']) ? $_POST['hdnCorpoTabela'] : '';
        	$idUnidade        = $unica ? $_POST['hdnIdUnidade'] : null;
        	$numTipoProcessoPeticionamento = $objTipoProcessoPeticionamentoRN->cadastrar($objTipoProcessoPeticionamentoDTO)->getNumIdTipoProcessoPeticionamento();
        	
        	$objRelTipoProcessoSeriePeticionamentoRN = new RelTipoProcessoSeriePeticionamentoRN();
        
        	//Tipo de Documento Essencial
        	foreach($arrIdTipoDocumentoEssencial as $numIdTipoDocumentoEss){
        		$objRelTipoProcessoSeriePeticionamentoEssDTO = new RelTipoProcessoSeriePeticionamentoDTO();
        	
        		$objRelTipoProcessoSeriePeticionamentoEssDTO->setNumIdTipoProcessoPeticionamento($numTipoProcessoPeticionamento);
        		$objRelTipoProcessoSeriePeticionamentoEssDTO->setNumIdSerie($numIdTipoDocumentoEss);
        		$objRelTipoProcessoSeriePeticionamentoEssDTO->setStrStaTipoDoc(RelTipoProcessoSeriePeticionamentoRN::$DOC_ESSENCIAL);
        			
        		$objRelTipoProcSerieEssPetDTO = $objRelTipoProcessoSeriePeticionamentoRN->cadastrar($objRelTipoProcessoSeriePeticionamentoEssDTO);
        	}
        	
        	//Tipo de Documento Complementar
	        foreach($arrIdTipoDocumento as $numIdTipoDocumento){
	        	$objRelTipoProcessoSeriePeticionamentoDTO = new RelTipoProcessoSeriePeticionamentoDTO();
	        	
				$objRelTipoProcessoSeriePeticionamentoDTO->setNumIdTipoProcessoPeticionamento($numTipoProcessoPeticionamento);
				$objRelTipoProcessoSeriePeticionamentoDTO->setNumIdSerie($numIdTipoDocumento);
				$objRelTipoProcessoSeriePeticionamentoDTO->setStrStaTipoDoc(RelTipoProcessoSeriePeticionamentoRN::$DOC_COMPLEMENTAR);

				$objRelTipoProcSeriePetDTO = $objRelTipoProcessoSeriePeticionamentoRN->cadastrar($objRelTipoProcessoSeriePeticionamentoDTO);
			}
			
			//Unidade
			$objRelTipoProcessoUnidadePeticionamentoRN = new RelTipoProcessoUnidadePeticionamentoRN();
			
			
			if($tipoUnidade === TipoProcessoPeticionamentoRN::$UNIDADES_MULTIPLAS)
			{
				foreach($arrIdUnidadesSelecionadas as $idUnidadeSelecionada){
					$objRelTipoProcessoUnidadePeticionamentoDTO = new RelTipoProcessoUnidadePeticionamentoDTO();
					
					$objRelTipoProcessoUnidadePeticionamentoDTO->setNumIdTipoProcessoPeticionamento($numTipoProcessoPeticionamento);
					$objRelTipoProcessoUnidadePeticionamentoDTO->setNumIdUnidade($idUnidadeSelecionada);
					$objRelTipoProcessoUnidadePeticionamentoDTO->setStrStaTipoUnidade(TipoProcessoPeticionamentoRN::$UNIDADES_MULTIPLAS);
					$objRelTipoProcessoUnidadePeticionamentoRN->cadastrar($objRelTipoProcessoUnidadePeticionamentoDTO);
				}	
			}else
			{
				$objRelTipoProcessoUnidadePeticionamentoDTO = new RelTipoProcessoUnidadePeticionamentoDTO();
				$objRelTipoProcessoUnidadePeticionamentoDTO->setNumIdTipoProcessoPeticionamento($numTipoProcessoPeticionamento);
				$objRelTipoProcessoUnidadePeticionamentoDTO->setNumIdUnidade($_POST['hdnIdUnidade']);
				$objRelTipoProcessoUnidadePeticionamentoDTO->setStrStaTipoUnidade(TipoProcessoPeticionamentoRN::$UNIDADE_UNICA);
		
			    $objRelTipoProcessoUnidadePeticionamentoRN->cadastrar($objRelTipoProcessoUnidadePeticionamentoDTO);
			}
			
			header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'].'&id_tipo_processo_peticionamento='.$objTipoProcessoPeticionamentoDTO->getNumIdTipoProcessoPeticionamento().PaginaSEI::getInstance()->montarAncora($objTipoProcessoPeticionamentoDTO->getNumIdTipoProcessoPeticionamento())));
			
        }catch(Exception $e){
          PaginaSEI::getInstance()->processarExcecao($e);
        }
      }
      break;
      
    case 'tipo_processo_peticionamento_alterar':
      $strTitulo = 'Alterar Tipo de Processo para Peticionamento';
      $strDesabilitar = 'disabled="disabled"';
      
      $objTipoProcessoPeticionamentoDTO = new TipoProcessoPeticionamentoDTO();
      $objTipoProcessoPeticionamentoRN = new TipoProcessoPeticionamentoRN();
      
      $strItensSelTipoProcesso = TipoProcessoPeticionamentoINT::montarSelectTipoProcesso(null, null, $_POST['selTipoProcesso']);
      $strItensSelUnidades     = UnidadeINT::montarSelectSiglaDescricao(null, null, $_POST['selUnidade']);
      $strItensSelDoc          = SerieINT::montarSelectNomeRI0802(null, null, $_POST['selDocumento']);
      
      $arrComandos[] = '<button type="submit" accesskey="s" name="sbmAlterarTipoPeticionamento" id="sbmAlterarTipoPeticionamento" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
	  $arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'].PaginaSEI::getInstance()->montarAncora($_GET['id_tipo_processo_peticionamento']))).'\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';
      
      $objTipoProcessoPeticionamentoDTO->setNumIdTipoProcessoPeticionamento($_POST['hdnIdMdPetTipoProcesso']);
      $objTipoProcessoPeticionamentoDTO->setNumIdProcedimento($_POST['hdnIdTipoProcesso']);
      $objTipoProcessoPeticionamentoDTO->setStrOrientacoes($_POST['txtOrientacoes']);
      //$objTipoProcessoPeticionamentoDTO->setNumIdUnidade($_POST['hdnIdUnidade']);
      
      $objTipoProcessoPeticionamentoDTO->setStrSinIIProprioUsuarioExterno('N');
      $objTipoProcessoPeticionamentoDTO->setStrSinIIIndicacaoDireta('N');
      $objTipoProcessoPeticionamentoDTO->setStrSinIIIndicacaoDiretaCpfCnpj('N');
      $objTipoProcessoPeticionamentoDTO->setStrSinIIIndicacaoDiretaContato('N');
      $objTipoProcessoPeticionamentoDTO->setStrSinNaUsuarioExterno('N');
      $objTipoProcessoPeticionamentoDTO->setStrSinNaPadrao('N');
      $objTipoProcessoPeticionamentoDTO->setStrSinDocGerado('N');
      $objTipoProcessoPeticionamentoDTO->setStrSinDocExterno('N');
      
      // indicacao interessado
      if($_POST['indicacaoInteressado'][0] == 1)
      	$objTipoProcessoPeticionamentoDTO->setStrSinIIProprioUsuarioExterno('S');
      if($_POST['indicacaoInteressado'][0] == 2)
      	$objTipoProcessoPeticionamentoDTO->setStrSinIIIndicacaoDireta('S');
      if($_POST['indicacaoIndireta'][0] == 3)
      	$objTipoProcessoPeticionamentoDTO->setStrSinIIIndicacaoDiretaCpfCnpj('S');
      if($_POST['indicacaoIndireta'][0] == 4)
      	$objTipoProcessoPeticionamentoDTO->setStrSinIIIndicacaoDiretaContato('S');
      
      // nivel de acesso
      if($_POST['rdNivelAcesso'][0] == 1)
      	$objTipoProcessoPeticionamentoDTO->setStrSinNaUsuarioExterno('S');
      if($_POST['rdNivelAcesso'][0] == 2)
      	$objTipoProcessoPeticionamentoDTO->setStrSinNaPadrao('S');
      if($_POST['selNivelAcesso'] != ''){
      	$objTipoProcessoPeticionamentoDTO->setStrStaNivelAcesso($_POST['selNivelAcesso']);
      }
      //documento principal
      if($_POST['rdDocPrincipal'][0] == 1) {
      	$objTipoProcessoPeticionamentoDTO->setStrSinDocGerado('S');
        $objTipoProcessoPeticionamentoDTO->setStrSinDocExterno('N'); 
      }
      else if($_POST['rdDocPrincipal'][0] == 2) {
      	$objTipoProcessoPeticionamentoDTO->setStrSinDocGerado('N');
      	$objTipoProcessoPeticionamentoDTO->setStrSinDocExterno('S');
      }
      
      $objTipoProcessoPeticionamentoDTO->setNumIdSerie($_POST['hdnIdTipoDocPrinc']);
      
      $objTipoProcessoPeticionamentoDTO->setStrSinAtivo('S');
      
   
   	if (isset($_POST['sbmAlterarTipoPeticionamento'])) {
   		
        try{
        	
        	$arrIdTipoDocumento = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnSerie']);
        	$arrIdTipoDocumentoEssencial = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnSerieEssencial']);
        	$arrIdUnidadesSelecionadas   = $_POST['hdnUnidadesSelecionadas'] != '' ? json_decode($_POST['hdnUnidadesSelecionadas']) : array();
			
        	//para nao limpar os campos em caso de erro de duplicidade
        	$idMdPetTipoProcesso = $_POST['hdnIdMdPetTipoProcesso'];
        	$nomeTipoProcesso = $_POST['txtTipoProcesso'];
        	$tipoUnidade      =  is_array($_POST['rdUnidade']) ? current($_POST['rdUnidade']) : array();
        	$idTipoProcesso   = $objTipoProcessoPeticionamentoDTO->getNumIdProcedimento();
        	$orientacoes      = $objTipoProcessoPeticionamentoDTO->getStrOrientacoes();
        	$idUnidade        = //$objTipoProcessoPeticionamentoDTO->getNumIdUnidade();
        	$nomeUnidade = $_POST['txtUnidade'];
        	//$nomeUnidade      = $objTipoProcessoPeticionamentoDTO->getStrSiglaUnidade();
        	$sinIndIntUsExt   = $objTipoProcessoPeticionamentoDTO->getStrSinIIProprioUsuarioExterno() == 'S' ? 'checked = checked' : '';
        	$sinIndIntIndIndir= $objTipoProcessoPeticionamentoDTO->getStrSinIIIndicacaoDireta() == 'S' ? 'checked = checked' : '';
        	$sinIndIntIndConta= $objTipoProcessoPeticionamentoDTO->getStrSinIIIndicacaoDiretaContato() == 'S' ? 'checked = checked' : '';
        	$sinIndIntIndCpfCn= $objTipoProcessoPeticionamentoDTO->getStrSinIIIndicacaoDiretaCpfCnpj() == 'S' ? 'checked = checked' : '';
        	$sinNAUsuExt      = $objTipoProcessoPeticionamentoDTO->getStrSinNaUsuarioExterno() == 'S' ? 'checked = checked' : '';
        	$sinNAPadrao      = $objTipoProcessoPeticionamentoDTO->getStrSinNaPadrao() == 'S' ? 'checked = checked' : '';
        	$gerado           = $objTipoProcessoPeticionamentoDTO->getStrSinDocGerado() == 'S' ? 'checked = checked' : '';
        	$externo          = $objTipoProcessoPeticionamentoDTO->getStrSinDocExterno() == 'S' ? 'checked = checked' : '';
        	$nomeSerie = $_POST['txtTipoDocPrinc'];
        	//$nomeSerie        = $objTipoProcessoPeticionamentoDTO->getStrNomeSerie();
        	$idSerie          = $objTipoProcessoPeticionamentoDTO->getNumIdSerie();
        	
        	$multipla         = $tipoUnidade == 'M' ? true : false;
        	$unica            = $tipoUnidade == 'U' ? true : false;
        	$hdnCorpoTabela   = isset($_POST['hdnCorpoTabela']) ? $_POST['hdnCorpoTabela'] : '';
        	
        	$objTipoProcessoPeticionamentoDTO->setNumIdHipoteseLegal(null);
        	
        	if($_POST['selNivelAcesso'] != '') {
        		 
        		$objTipoProcessoPeticionamentoDTO->setStrStaNivelAcesso($_POST['selNivelAcesso']);
        		 
        		if( $_POST['selNivelAcesso'] === ProtocoloRN::$NA_RESTRITO){
        			$objTipoProcessoPeticionamentoDTO->setNumIdHipoteseLegal($_POST['selHipoteseLegal']);
        		} else {
        			$objTipoProcessoPeticionamentoDTO->setNumIdHipoteseLegal(null);
        		}
        		 
        	} else {
        			$objTipoProcessoPeticionamentoDTO->setStrStaNivelAcesso(null);
        		}
        	
        	$objAlterado = $objTipoProcessoPeticionamentoRN->alterar($objTipoProcessoPeticionamentoDTO);
   
        	if($objAlterado){
        	//EXCLUSÕES DAS RNS
        	
        	//Exclusão de Tipo de Documento Essencial e Complementar
        	$numIdTpProcessoPet = isset($_GET['id_tipo_processo_peticionamento']) &&  $_GET['id_tipo_processo_peticionamento'] != '' ?  $_GET['id_tipo_processo_peticionamento'] : $_POST['hdnIdMdPetTipoProcesso'];
        	$objRelTipoProcessoSeriePeticionamentoRN = new RelTipoProcessoSeriePeticionamentoRN();
        	$arrRelTipoProcessoSeriePeticionamentoDTO = array();
        	$objRelTipoProcessoSeriePeticionamentoDTO = new RelTipoProcessoSeriePeticionamentoDTO();
        	$objRelTipoProcessoSeriePeticionamentoDTO->setNumIdTipoProcessoPeticionamento($numIdTpProcessoPet);
        	$arrRelTipoProcessoSeriePeticionamentoDTO[] = $objRelTipoProcessoSeriePeticionamentoDTO;
        	
        	$objRelTipoProcessoSeriePeticionamentoDTO->retTodos();
        	$arrListarTipoProcessoSeriePeticionamentoDTO = $objRelTipoProcessoSeriePeticionamentoRN->listar($objRelTipoProcessoSeriePeticionamentoDTO);
        	
        	if( is_array( $arrListarTipoProcessoSeriePeticionamentoDTO ) && count( $arrListarTipoProcessoSeriePeticionamentoDTO ) > 0 ){
        		$objRelTipoProcessoSeriePeticionamentoRN->excluir( $arrListarTipoProcessoSeriePeticionamentoDTO );
        	}
        	
        	//Exclusão de Unidade
        	$arrRelTipoProcessoUnidadePeticionamentoDTO = array();
        	$objRelTipoProcessoUnidadePeticionamentoRN = new RelTipoProcessoUnidadePeticionamentoRN();
        	$objRelTipoProcessoUnidadePeticionamentoDTO = new RelTipoProcessoUnidadePeticionamentoDTO();
        	$objRelTipoProcessoUnidadePeticionamentoDTO->setNumIdTipoProcessoPeticionamento($numIdTpProcessoPet);
        	$objRelTipoProcessoUnidadePeticionamentoDTO->retTodos();
        	$arrRelTipoProcessoUnidadePeticionamentoDTO = $objRelTipoProcessoUnidadePeticionamentoRN->listar($objRelTipoProcessoUnidadePeticionamentoDTO);
        	
        	if( is_array( $arrRelTipoProcessoUnidadePeticionamentoDTO ) && count( $arrRelTipoProcessoUnidadePeticionamentoDTO ) > 0 ){
        		$objRelTipoProcessoUnidadePeticionamentoRN->excluir( $arrRelTipoProcessoUnidadePeticionamentoDTO );
        	}
        	
        	//CADASTROS RNS
			
			//Cadastro de Unidade
			$objRelTipoProcessoUnidadePeticionamentoRN = new RelTipoProcessoUnidadePeticionamentoRN();
				
			if($tipoUnidade === TipoProcessoPeticionamentoRN::$UNIDADES_MULTIPLAS)
			{
				foreach($arrIdUnidadesSelecionadas as $idUnidadeSelecionada){
					$objRelTipoProcessoUnidadePeticionamentoDTO = new RelTipoProcessoUnidadePeticionamentoDTO();
						
					$objRelTipoProcessoUnidadePeticionamentoDTO->setNumIdTipoProcessoPeticionamento($numIdTpProcessoPet);
					$objRelTipoProcessoUnidadePeticionamentoDTO->setNumIdUnidade($idUnidadeSelecionada);
					$objRelTipoProcessoUnidadePeticionamentoDTO->setStrStaTipoUnidade(TipoProcessoPeticionamentoRN::$UNIDADES_MULTIPLAS);
					$objRelTipoProcessoUnidadePeticionamentoRN->cadastrar($objRelTipoProcessoUnidadePeticionamentoDTO);
				}
			}else
			{
				$objRelTipoProcessoUnidadePeticionamentoDTO = new RelTipoProcessoUnidadePeticionamentoDTO();
				$objRelTipoProcessoUnidadePeticionamentoDTO->setNumIdTipoProcessoPeticionamento($numIdTpProcessoPet);
				$objRelTipoProcessoUnidadePeticionamentoDTO->setNumIdUnidade($_POST['hdnIdUnidade']);
				$objRelTipoProcessoUnidadePeticionamentoDTO->setStrStaTipoUnidade(TipoProcessoPeticionamentoRN::$UNIDADE_UNICA);
			
				$objRelTipoProcessoUnidadePeticionamentoRN->cadastrar($objRelTipoProcessoUnidadePeticionamentoDTO);
			}
			
			
			//Tipo de Documento Essencial
			if(count($arrIdTipoDocumentoEssencial)> 0){
			foreach($arrIdTipoDocumentoEssencial as $numIdTipoDocumentoEss){
		
				$objRelTipoProcessoSeriePeticionamentoEssDTO = new RelTipoProcessoSeriePeticionamentoDTO();
				$objRelTipoProcessoSeriePeticionamentoEssDTO->setNumIdRelTipoProcessoSeriePeticionamento(null);
				$objRelTipoProcessoSeriePeticionamentoEssDTO->setNumIdTipoProcessoPeticionamento($numIdTpProcessoPet);
				$objRelTipoProcessoSeriePeticionamentoEssDTO->setNumIdSerie($numIdTipoDocumentoEss);
				$objRelTipoProcessoSeriePeticionamentoEssDTO->setStrStaTipoDoc(RelTipoProcessoSeriePeticionamentoRN::$DOC_ESSENCIAL);
				 
				$objRelTipoProcSerieEssPetDTO = $objRelTipoProcessoSeriePeticionamentoRN->cadastrar($objRelTipoProcessoSeriePeticionamentoEssDTO);
			}
			}
			 
			//Tipo de Documento Complementar
			if(count($arrIdTipoDocumento)> 0){
			foreach($arrIdTipoDocumento as $numIdTipoDocumento){
				$objRelTipoProcessoSeriePeticionamentoDTO = new RelTipoProcessoSeriePeticionamentoDTO();
				$objRelTipoProcessoSeriePeticionamentoDTO->setNumIdRelTipoProcessoSeriePeticionamento(null);
				$objRelTipoProcessoSeriePeticionamentoDTO->setNumIdTipoProcessoPeticionamento($numIdTpProcessoPet);
				$objRelTipoProcessoSeriePeticionamentoDTO->setNumIdSerie($numIdTipoDocumento);
				$objRelTipoProcessoSeriePeticionamentoDTO->setStrStaTipoDoc(RelTipoProcessoSeriePeticionamentoRN::$DOC_COMPLEMENTAR);
			
				$objRelTipoProcSeriePetDTO = $objRelTipoProcessoSeriePeticionamentoRN->cadastrar($objRelTipoProcessoSeriePeticionamentoDTO);
			}
        	}
        	}
        	
        	header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'].'&id_tipo_processo_peticionamento='.$objTipoProcessoPeticionamentoDTO->getNumIdTipoProcessoPeticionamento().PaginaSEI::getInstance()->montarAncora($objTipoProcessoPeticionamentoDTO->getNumIdTipoProcessoPeticionamento())));

        }catch(Exception $e){
          PaginaSEI::getInstance()->processarExcecao($e);
       }
   	}
      break;

    case 'tipo_processo_peticionamento_consultar':
      $strTitulo = 'Consultar Tipo de Processo para Peticionamento';
      $arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'].PaginaSEI::getInstance()->montarAncora($_GET['id_tipo_processo_peticionamento']))).'\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
      
      $strItensSelTipoProcesso = TipoProcessoPeticionamentoINT::montarSelectTipoProcesso(null, null, $_POST['selTipoProcesso']);
      $strItensSelUnidades     = UnidadeINT::montarSelectSiglaDescricao(null, null, $_POST['selUnidade']);
      //$strItensSelNivelAcesso  = TipoProcessoPeticionamentoINT::montarSelectNivelAcesso(null, null, $_POST['selNivelProcesso']);
      $strItensSelDoc      = SerieINT::montarSelectNomeRI0802(null, null, $_POST['selDocumento']);
      
   //   $objIndisponibilidadePeticionamentoDTO->setNumIdIndisponibilidade($_GET['id_tipo_processo_peticionamento']);
     // $objIndisponibilidadePeticionamentoDTO->setBolExclusaoLogica(false);
      //$objIndisponibilidadePeticionamentoDTO->retTodos();
      
      //$objIndisponibilidadePeticionamentoRN = new IndisponibilidadePeticionamentoRN();
      //$objIndisponibilidadePeticionamentoDTO = $objIndisponibilidadePeticionamentoRN->consultar($objIndisponibilidadePeticionamentoDTO);
      //if ($objIndisponibilidadePeticionamentoDTO===null){
        //throw new InfraException("Registro não encontrado.");
      //}
      break;
      
      case 'tipo_processo_peticionamento_salvar':
      	
      	
      break;
   

    default:
      throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
  }


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

PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>


<?
PaginaSEI::getInstance()->fecharJavaScript();
?>


<style type="text/css">
<?php 
$browser = $_SERVER['HTTP_USER_AGENT'];
$firefox = strpos($browser, 'Firefox') ? true : false;
?>

#lblTipoProcesso {position:absolute;left:0%;top:2px;width:50%;}
#txtTipoProcesso {position:absolute;left:0%;top:18px;width:50%;}

#fldProrrogacao {height: 20%; width: 86%;}

<?php if($firefox){ ?>

.sizeFieldset {height: 30%; width: 86%;}
.tamanhoFieldset{height:auto; width:86%;}

#divIndicacaoInteressado {}
#divUnidade {margin-top:138px!important;}

#imgLupaTipoProcesso {position:absolute;left:51%;top:18px;}
#imgExcluirTipoProcesso {position:absolute;left:53.6%;top:18px;}

#lblUnidade {position:absolute;left:0%;width:50%;}
#txtUnidade {left:12px;width:65%;margin-top: 0.5%;}
#imgLupaUnidade {position:absolute;left:51%;margin-top: 0.5%;}
#imgExcluirUnidade {position:absolute;left:52.7%;margin-top: 0.5%;}

#txtUnidadeMultipla {left:12px;width:65%;margin-top: 0.5%;}
#imgLupaUnidadeMultipla {position:absolute;left:51%;margin-top: 0.5%;}
#sbmAdicionarUnidade {position:absolute;left:53.7%;margin-top: 0.5%;}

#lblOrientacoes {position:absolute;left:0%;top:50px;width:20%;}
#txtOrientacoes {position:absolute;left:0%;top:66px;width:75%;}

#lblNivelAcesso {width:50%;}
#selNivelAcesso {width:20%;}

#lblHipoteseLegal {width:50%;}
#selHipoteseLegal {width:50%;}

#lblModelo {width:50%;}
#selModelo {width:40%;}

#lblTipoDocPrincipal {width:50%;}
#txtTipoDocPrinc {width:39.5%;}
#imgLupaTipoDocPrinc {top:198%}
#imgExcluirTipoDocPrinc {top:198%}

#txtSerie {width:50%;}
#lblDescricao {width:50%;}
#selDescricao {width:75%;}
#imgLupaTipoDocumento { margin-top: 2px; margin-left: 4px;}

#txtSerieEssencial {width:50%;}
#lblDescricaoEssencial {width:50%;}
#selDescricaoEssencial {width:75%;}
#imgLupaTipoDocumentoEssencial { margin-top: 2px; margin-left: 4px;}

.fieldNone{border:none !important;}

.sizeFieldset#fldDocPrincipal {height: 50%!important;}

<?php }else { ?>
.sizeFieldset {height: 30%; width: 86%;}
.tamanhoFieldset{height:auto; width:86%;}

#divIndicacaoInteressado {}
#imgLupaTipoProcesso {position:absolute;left:51%;top:18px;}
#imgExcluirTipoProcesso {position:absolute;left:53.1%;top:18px;}

#divUnidade {margin-top:111px!important;}

#lblUnidade {left:0%;top:15.7%;width:65%;}
#txtUnidade {left:0%;top:17.6%;width:65%;,margin-top:0.5%}
#imgLupaUnidade {position:absolute;left:50.4%;}
#imgExcluirUnidade {position:absolute;left:52.1%;}

#txtUnidadeMultipla {left:12px;width:65%;margin-top: 0.5%;}
#imgLupaUnidadeMultipla {position:absolute;left:50.5%;margin-top: 0.5%;}
#sbmAdicionarUnidade {position:absolute;left:53.2%;margin-top: 0.5%;}

#lblOrientacoes {position:absolute;left:0%;top:50px;width:20%;}
#txtOrientacoes {position:absolute;left:0%;top:66px;width:75%;}

#lblNivelAcesso {width:50%;}
#selNivelAcesso {width:20%;}

#lblHipoteseLegal {width:50%;}
#selHipoteseLegal {width:50%;}

#lblModelo {width:50%;}
#selModelo {width:40%;}

#lblTipoDocPrincipal {width:50%;}
#txtTipoDocPrinc {width:39.5%;}
#imgLupaTipoDocPrinc {top:198%}
#imgExcluirTipoDocPrinc {top:198%}

#txtSerie {width:50%;}
#lblDescricao {width:50%;}
#selDescricao {width:75%;}

#imgLupaTipoDocumento { 
  margin-top: 2px; 
  margin-left: 4px;
}

#imgExcluirTipoDocumento {
}

.fieldNone{border:none !important;}

.sizeFieldset#fldDocPrincipal {height: 50%!important;}

#txtSerieEssencial {width:50%;}
#lblDescricaoEssencial {width:50%;}
#selDescricaoEssencial {width:75%;}
#imgLupaTipoDocumentoEssencial { margin-top: 2px; margin-left: 4px;}

<?php } ?>

.fieldsetClear {border:none !important;}
.rdIndicacaoIndiretaHide  {margin-left:2.8%!important;}
</style>
<?php 
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
?>
<form id="frmTipoProcessoCadastro" method="post" onsubmit="return OnSubmitForm();" 
action="<?=PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">
<?
PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaSEI::getInstance()->abrirAreaDados('98%');
?>
 
 <input type="hidden" name="hdnParametroHipoteseLegal" id="hdnParametroHipoteseLegal" value="<?php echo $valorParametroHipoteseLegal; ?>"/>
 <!--  Tipo de Processo  -->
 <div class="fieldsetClear">
  <label id="lblTipoProcesso" for="txtTipoProcesso" class="infraLabelObrigatorio">
  Tipo de Processo:
  </label>
  <input type="text" onchange="removerProcessoAssociado(0);" id="txtTipoProcesso" name="txtTipoProcesso" class="infraText" value="<?php echo $nomeTipoProcesso; ?>" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
  <input type="hidden" id="hdnIdTipoProcesso" name="hdnIdTipoProcesso" value="<?php echo $idTipoProcesso ?>" />
  <input type="hidden" id="hdnIdMdPetTipoProcesso" name="hdnIdMdPetTipoProcesso" value="<?php echo $idMdPetTipoProcesso ?>" />
  <img id="imgLupaTipoProcesso" onclick="objLupaTipoProcesso.selecionar(700,500);" src="/infra_css/imagens/lupa.gif" alt="Selecionar Tipo de Processo" title="Selecionar Tipo de Processo" class="infraImg" />
  <img id="imgExcluirTipoProcesso" onclick="removerProcessoAssociado(0);objLupaTipoProcesso.remover();" src="/infra_css/imagens/remover.gif" alt="Remover Tipo de Processo" title="Remover Tipo de Processo" class="infraImg" />
   
 </div>
 <!--  Fim do Tipo de Processo -->
    
    <div style="clear:both;">&nbsp;</div>
    
 <!-- Orientações -->
  <div class="fieldsetClear">
  <label id="lblOrientacoes" for="txtOrientacoes" class="infraLabelObrigatorio">
  Orientações:
  </label>
  <textarea type="text" id="txtOrientacoes" rows="3" name="txtOrientacoes" class="infraText" onkeypress="return infraMascaraTexto(this,event,500);" maxlength="500" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"><?php echo $orientacoes;?></textarea>
  </div>
 <!--  Fim das Orientações  -->

<!--  Unidade -->  
  <div id="divUnidade">
  <fieldset class="infraFieldset" style="width:75%;">
  <legend class="infraLegend">&nbsp;Unidade para Abertura do Processo&nbsp;</legend>
 <!-- Unidade única --> 
  <?php $divUnidadeUnica = $unica ? 'style="display:inherit;margin-bottom: 6px"' : 'style="display:none;margin-bottom: 6px"';
        $checkUnidadeUnica = $unica ? 'checked="checked";' : '';
   ?>
 
  <input <?php echo $checkUnidadeUnica; ?> type="radio" id="rdUnidadeUnica" name="rdUnidade[]" onchange="changeUnidade()" value="U">
  <label id="lblUnidadeUnica" name="lblUnidadeUnica" for="rdUnidadeUnica" class="infraLabelOpcional infraLabelRadio">
  Unidade Única
  </label>
  <br/>
 
  <div id="divCpUnidadeUnica" <?php echo $divUnidadeUnica;?>>
  <input type="text" id="txtUnidade" name="txtUnidade" class="infraText" value="<?=$nomeUnidade?>" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
  <input type="hidden" id="hdnIdUnidade" name="hdnIdUnidade" value="<?=$idUnidade?>" />
  <img id="imgLupaUnidade" onclick="objLupaUnidade.selecionar(700,500);" src="/infra_css/imagens/lupa.gif" alt="Selecionar Unidade" title="Selecionar Unidade" class="infraImg" />
  <img id="imgExcluirUnidade" onclick="objLupaUnidade.remover();" src="/infra_css/imagens/remover.gif" alt="Remover Unidade" title="Remover Unidade" class="infraImg" />
  </div>
 <!--  Fim da Unidade Única --> 

  <!--  Múltiplas Unidades -->
  <?php
  $divUnidadeMultipla   = $multipla ? 'style="display:inherit;"' : 'style="display:none;"'; 
  $checkUnidadeMultipla = $multipla ? 'checked="checked;"' : '';
  ?>
  
  <input <?php echo $checkUnidadeMultipla; ?> type="radio" id="rdUnidadeMultipla" name="rdUnidade[]" onchange="changeUnidade()" value="M">
  <label id="lblUnidadeMultipla" name="lblUnidadeMultipla" for="rdUnidadeMultipla" class="infraLabelOpcional infraLabelRadio">
  Múltiplas Unidades
  </label>
  
 <div id="divCpUnidadeMultipla" <?php echo $divUnidadeMultipla; ?>>
   <input type="text" id="txtUnidadeMultipla" name="txtUnidadeMultipla" class="infraText" value="<?=$nomeUnidadeMultipla?>" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
   <input type="hidden" id="hdnIdUnidadeMultipla" name="hdnIdUnidadeMultipla" value="<?=$idUnidadeMultipla?>" />
   <input type="hidden" id="hdnUfUnidadeMultipla" name="hdnUfUnidadeMultipla" value="" />
   
   <img id="imgLupaUnidadeMultipla" onclick="objLupaUnidadeMultipla.selecionar(700,500);" src="/infra_css/imagens/lupa.gif" alt="Selecionar Unidade" title="Selecionar Unidade" class="infraImg" />
    <?php  if ($_GET['acao'] != 'tipo_processo_peticionamento_consultar'){ ?>
   <button type="button" accesskey="a" name="sbmAdicionarUnidade" onclick="addUnidade();" id="sbmAdicionarUnidade" value="Adicionar" class="infraButton" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"><span class="infraTeclaAtalho">A</span>dicionar</button>
   <?php } ?>
   <!-- Tabela Múltiplas Unidades -->

<div class="infraAreaTabela" id="divTableMultiplasUnidades" <?php echo $divUnidadeMultipla; ?>>
<table width="99%" summary="Tabela de Unidades" class="infraTable">
<caption class="infraCaption">Lista de Unidades (<span id="qtdRegistros"><?php echo count($arrObjUnidadesMultiplas)>0 ? count($arrObjUnidadesMultiplas) : '0';  ?> </span> registros):</caption>
<tbody>
<tr>
<th width="30%" class="infraTh"><table class="infraTableOrdenacao">
<tbody>
<tr>
<td valign="center" class="infraTdRotuloOrdenacao">Sigla</td>
</tr>
</tbody>
</th>
</table>
<th class="infraTh">
<table class="infraTableOrdenacao">
<tbody>
<tr>
<td valign="center" class="infraTdRotuloOrdenacao">Descrição</td></tr>
</tbody></table>
</th>

<th class="infraTh">
<table class="infraTableOrdenacao">
<tbody>
<tr>
<td width="6%" valign="center" class="infraTdRotuloOrdenacao">UF</td></tr>
</tbody></table>
</th>
 <?php  if ($_GET['acao'] != 'tipo_processo_peticionamento_consultar'){ ?>
<th width="9%" class="infraTh">Ações</th>
<?php } ?>
<tbody id="corpoTabela">
<?php 
if($multipla && isset($hdnCorpoTabela)){
	echo $hdnCorpoTabela;
}
?>
<?php if($multipla){
	if(count($arrObjUnidadesMultiplas)> 0){
foreach ($arrObjUnidadesMultiplas as $cadaObjUnidadeDTO){
$idTabela =	'tabNomeUnidade_' .$cadaObjUnidadeDTO->getNumIdUnidade();
?>
<tr class="infraTrClara linhas" id="<?php echo $idTabela;?>">
<td id="tabNomeUnidade"><?php echo $cadaObjUnidadeDTO->getStrSigla();?></td>
<td><?php echo $cadaObjUnidadeDTO->getStrDescricao();?></td>
<td class="ufsSelecionadas"><?php echo $cadaObjUnidadeDTO->getStrSiglaUf(); ?></td>
<?php  if ($_GET['acao'] != 'tipo_processo_peticionamento_consultar'){ ?>
<td align="center">
<a>
<img class="infraImg" title="Remover Unidade" alt="Remover Unidade" src="/infra_css/imagens/remover.gif" onclick="removerUnidade('<?php echo $idTabela;?>');" id="imgExcluirProcessoSobrestado">
</a>
</td>
<?php } ?>
</tr>
<?php }}} ?>

</tbody>

</table>

</div>

   <!--  Fim Tabela Múltiplas Unidades -->
   
 </div>
 
  <!--  Fim das Múltiplas Unidades -->
</fieldset>
 </div>
   
 <!--  Fim da Unidade -->
   
    <br/>
    
  <!--  Indicação de Interessados -->
   <div id="divIndicacaoInteressado">
   <fieldset class="infraFieldset" style="width:75%;">
  	<legend class="infraLegend">&nbsp;Indicação de Interessado&nbsp;</legend>
   
   <input onclick="changeIndicacaoInteressado()" type="radio" id="rdUsuExterno" name="indicacaoInteressado[]" value="1" <?php echo $sinIndIntUsExt ?>> 
   <label for="rdUsuExterno" id="lblUsuExterno" class="infraLabelRadio">
   Próprio Usuário Externo
   </label>
 	<br/>
   <input onclick="changeIndicacaoInteressado()" type="radio" name="indicacaoInteressado[]" id="rdIndicacaoIndireta" value="2"  <?php echo $sinIndIntIndIndir ?>> 
   <label name="lblIndicacaoIndireta" id="lblIndicacaoIndireta" for="rdIndicacaoIndireta" class="infraLabelRadio"> 
   Indicação Direta
   </label>
   
   <br/>
  <div id="divRdIndicacaoIndiretaHide" <?php echo $sinIndIntIndIndir != '' ? 'style="display: inherit;"' : 'style="display: none;"'?> >
  <input <?php echo $sinIndIntIndCpfCn; ?>  type="radio" name="indicacaoIndireta[]" id="indicacaoIndireta1" class="rdIndicacaoIndiretaHide" value="3"> 
   <label  name="lblInformandoCpfCnpj" for="indicacaoIndireta1" id="lblInformandoCpfCnpj" 
   class="lblIndicacaoIndiretaHide infraLabelRadio"> 
   Informando CPF ou CNPJ
   </label>
   <br/>
 
   <input <?php echo $sinIndIntIndConta;  ?> type="radio" name="indicacaoIndireta[]"  id="indicacaoIndireta2" class="rdIndicacaoIndiretaHide" value="4"> 
   <label for="indicacaoIndireta2" id="lblContatosJaExistentes" name="lblContatosJaExistentes" 
   class="lblIndicacaoIndiretaHide infraLabelRadio"> 
   Digitando nome de Contatos já existentes
   </label>
   </div>
   </fieldset>
   </div>
   
</br>
  
  <!--  Fim da Indicação de Interessados -->
  
  <div>
   <fieldset class="infraFieldset" style="width:75%;">
   <legend class="infraLegend">&nbsp;Nível de Acesso dos Documentos&nbsp;</legend>
   <div>
   <input <?php echo $sinNAUsuExt; ?> type="radio" name="rdNivelAcesso[]" id="rdUsuExternoIndicarEntrePermitidos" onclick="changeNivelAcesso();" value="1"> 
   
   <label for="rdUsuExternoIndicarEntrePermitidos" id="lblUsuExterno" class="infraLabelRadio">
   Usuário Externo indicar diretamente
   </label>
   <br/>
   
   <input <?php echo $sinNAPadrao; ?> type="radio" name="rdNivelAcesso[]"  id="rdPadrao" onclick="changeNivelAcesso();" value="2"> 
   <label name="lblPadrao" id="lblPadrao" for="rdPadrao" class="infraLabelRadio">
   Padrão pré definido
   </label>
     
   <div id="divNivelAcesso"  <?php echo $sinNAPadrao != '' ? 'style="display: inherit;"' : 'style="display: none;"'?>>
	   <div style="clear:both;">&nbsp;</div>
	   <label name="lblNivelAcesso" id="lblNivelAcesso" for="selNivelAcesso" class="infraLabelObrigatorio">
	   Nível de Acesso:
	   </label>
	   <br/>
	   <select id="selNivelAcesso" name="selNivelAcesso" onchange="changeSelectNivelAcesso()">
	     <?=$strItensSelNivelAcesso?>
	   </select>
   </div>
   
   <div id="divHipoteseLegal" <?php echo $hipoteseLegal //$sinNAPadrao != '' ? 'style="display: inherit;"' : 'style="display: none;"'?> >
	   <div style="clear:both;">&nbsp;</div>
	   <label name="lblHipoteseLegal" id="lblHipoteseLegal" for="selHipoteseLegal" class="infraLabelObrigatorio">
	   Hipótese Legal:
	   </label>
	   <br/>
	   <select id="selHipoteseLegal" name="selHipoteseLegal">
	   <?=$strItensSelHipoteseLegal?>
	   </select>
   
   </div>
   
   </div>
   </fieldset>
  </div>
   
   <div style="clear:both;">&nbsp;</div>
      
   <fieldset id="fldDocPrincipal" class="infraFieldset tamanhoFieldset" style="top:110%; width: 75%;">
       
       <legend class="infraLegend">&nbsp;Documento Principal&nbsp;</legend>
       
	   <input type="radio" name="rdDocPrincipal[]" id="rdDocGerado" onclick="changeDocPrincipal();" value="1" <?php echo $gerado ?>> 
	   <label for="rdDocGerado" id="lblDocGerado" class="infraLabelRadio">
	   Gerado (Editor e Modelo)
	   </label>
	   <br/>
	   
	   <input type="radio" name="rdDocPrincipal[]"  id="rdDocExterno" onclick="changeDocPrincipal();" value="2" <?php echo $externo ?>> 
	   <label name="lblDocExterno" id="lblDocExterno" for="rdDocExterno" class="infraLabelRadio">
	   Externo (Anexação de Arquivo)
	   </label>
	   <br/>
	   
	  <div  <?php echo $gerado != '' || $externo != '' ? 'style="display: inherit;"' : 'style="display: none;"'?>  id="divDocPrincipal">
	   <div class="clear:both;">&nbsp;</div>
	  <div>
	  <label name="lblTipoDocPrincipal" id="lblTipoDocPrincipal" for="txtTipoDocPrinc" class="infraLabelObrigatorio">
	  Tipo do Documento Principal:
	  </label>
	  </div>
	  <input type="text" id="txtTipoDocPrinc" name="txtTipoDocPrinc" class="infraText" value="<?=$nomeSerie?>" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
	   
	  <input type="hidden" id="hdnIdTipoDocPrinc" name="hdnIdTipoDocPrinc" value="<?=$idSerie?>" />
	  <img id="imgLupaTipoDocPrinc" onclick="carregarComponenteLupaTpDocPrinc('S');" src="/infra_css/imagens/lupa.gif" alt="Selecionar Tipo de Documento" title="Selecionar Tipo de Documento" class="infraImg" />      
	  <img id="imgExcluirTipoDocPrinc" onclick="carregarComponenteLupaTpDocPrinc('R')" src="/infra_css/imagens/remover.gif" alt="Remover Tipo de Documento" title="Remover Tipo de Documento" class="infraImg" />   
	       
	  </div>
	   
   </fieldset>
   <!--  Documento Essencial -->
   <? 
   //$divDocs = $alterar || $gerado || $externo ? 'style="display: inherit;"' : 'style="display: none;"'
   $divDocs = 'style="display: inherit;"';
   ?> 
    <fieldset <?php echo $divDocs;?> id="fldDocEssenciais" class="sizeFieldset tamanhoFieldset fieldNone">
      <div>
     <div style="clear:both;">&nbsp;</div>
     <div>
      <label id="lblDescricaoEssencial" for="selDescricaoEssencial" class="infraLabelOpcional">
	  Tipos dos Documentos Essenciais:
	  </label>
	</div>
	<div>
	  <input type="text" id="txtSerieEssencial" name="txtSerieEssencial" class="infraText" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
	   
	   </div>
	   <div style="margin-top: 5px;">
	  <select style="float: left;" id="selDescricaoEssencial" name="selDescricaoEssencial" size="8" multiple="multiple" class="infraSelect">
	  <?=$strItensSelSeriesEss; ?>
	  </select>
	
	  <img id="imgLupaTipoDocumentoEssencial" onclick="objLupaTipoDocumentoEssencial.selecionar(700,500)" src="/infra_css/imagens/lupa.gif" 
	    alt="Localizar Tipo de Documento" 
	    title="Localizar Tipo de Documento" class="infraImg" />	
	  
	  <img id="imgExcluirTipoDocumentoEssencial" onclick="objLupaTipoDocumentoEssencial.remover();" src="/infra_css/imagens/remover.gif" 
	    alt="Remover Tipos de Documentos" 
	    title="Remover Tipos de Documentos" class="infraImg" />
	  
	 </div>
	  
	  </div>
    </fieldset>
    <!--  Fim do Documento Essencial -->
   
   <!--  Documento Complementar  -->
   <fieldset <?php echo $divDocs;?> id="fldDocComplementar" class="sizeFieldset tamanhoFieldset fieldNone">
      <div>
     <div style="clear:both;">&nbsp;</div>
     <div>
      <label id="lblDescricao" for="txtDescricao" class="infraLabelOpcional">
	  Tipos dos Documentos Complementares:
	  </label>
	</div>
	<div>
	  <input type="text" id="txtSerie" name="txtSerie" class="infraText" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
	   
	   </div>
	   <div style="margin-top: 5px;">
	  <select style="float: left;" id="selDescricao" name="selDescricao" size="16" multiple="multiple" class="infraSelect">
	  <?=$strItensSelSeries?>
	  </select>
	
	  <img id="imgLupaTipoDocumento" onclick="carregarComponenteLupaTpDocComplementar('S');" src="/infra_css/imagens/lupa.gif" 
	    alt="Localizar Tipo de Documento" 
	    title="Localizar Tipo de Documento" class="infraImg" />	
	  
	  <img id="imgExcluirTipoDocumento" onclick="carregarComponenteLupaTpDocComplementar('R');" src="/infra_css/imagens/remover.gif" 
	    alt="Remover Tipos de Documentos" 
	    title="Remover Tipos de Documentos" class="infraImg" />
	  
	 </div>
	  
	  </div>
    </fieldset>
    <!--  Fim do Documento Complementar -->
  
  <input type="hidden" id="hdnCorpoTabela" name="hdnCorpoTabela" value=""/>
  <input type="hidden" id="hdnUnidadesSelecionadas" name="hdnUnidadesSelecionadas" value=""/>
  <input type="hidden" id="hdnTodasUnidades" name="hdnTodasUnidades" value='<?= json_encode($arrObjUnidadeDTOFormatado);?>' />
  <input type="hidden" id="hdnIdTipoDocumento" name="hdnIdTipoDocumento" value="" />
  <input type="hidden" id="hdnSerie" name="hdnSerie" value="<?=$_POST['hdnSerie']?>" />
  <input type="hidden" id="hdnIdTipoDocumento" name="hdnIdTipoDocumento" value="<?=$_POST['hdnIdTipoDocumento']?>" />  
  <input type="hidden" id="hdnIdIndisponibilidadePeticionamento" name="hdnIdIndisponibilidadePeticionamento" value="" />
  <input type="hidden" id="hdnIdSerie" name="hdnIdSerie" value="<?=$_POST['hdnIdSerie']?>" />
  <input type="hidden" id="hdnIdSerieEssencial" name="hdnIdSerieEssencial" value="<?=$_POST['hdnIdSerieEssencial']?>" />
  <input type="hidden" id="hdnSerieEssencial" name="hdnSerieEssencial" value="<?=$_POST['hdnSerieEssencial']?>" /> 
   
  <?
  PaginaSEI::getInstance()->fecharAreaDados();
  ?>
</form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>

<script type="text/javascript">
//Processo
var objLupaTipoProcesso = null;
var objAutoCompletarTipoProcesso = null;

//Docs
var objLupaTipoDocumento = null;
var objAutoCompletarTipoDocumento = null;

var objLupaTipoDocPrinc = null;
var objAutoCompletarTipoDocPrinc = null;

var objLupaTipoDocumentoEssencial = null	
var objAutoCompletarTipoDocumentoEssencial = null;

//Unidades
var objLupaUnidade = null;
var objAutoCompletarUnidade = null;

var objLupaUnidadeMultipla = null;
var objAutoCompletarUnidadeMutipla = null;

function addUnidade(){
	var idUnidadeSelect  = document.getElementById('hdnIdUnidadeMultipla').value;

	if(idUnidadeSelect != ''){
	var idLinhaTabela    = 'tabNomeUnidade_' + idUnidadeSelect;
	var existeUnidade    =  document.getElementById(idLinhaTabela);

	var valueCodUnidades = document.getElementById('hdnTodasUnidades').value; 
	if(valueCodUnidades != ''){
	var objUnidades      = $.parseJSON(valueCodUnidades);

	if(!registroDuplicado(objUnidades[idUnidadeSelect].uf)){
	
	qtdLinhas = document.getElementsByClassName('linhas').length;

	var html = '';
	if(qtdLinhas > 0){
		html = document.getElementById('corpoTabela').innerHTML;
    }
    
	html += '<tr class="infraTrClara linhas" id="'+idLinhaTabela+'"><td>';
	html += objUnidades[idUnidadeSelect].sigla;
	html += ' </td>';
	html += '<td>'+objUnidades[idUnidadeSelect].descricao+ '</td>';
	html += '<td class="ufsSelecionadas">'+objUnidades[idUnidadeSelect].uf +'</td>';
	html += '<td align="center">';
	html += '<a><img class="infraImg" title="Remover Unidade" alt="Remover Unidade" src="/infra_css/imagens/remover.gif" onclick="removerUnidade(\''+idLinhaTabela+'\');" id="imgExcluirProcessoSobrestado"></a></td></tr>';
	
	//Adiciona Conteúdo da Tabela no HTML
	document.getElementById('corpoTabela').innerHTML = '';
	document.getElementById('corpoTabela').innerHTML = html;

	// Mostra a tabela
	document.getElementById('divTableMultiplasUnidades').style.display = "inherit";

	//Zera os campos, após adicionar
	document.getElementById('txtUnidadeMultipla').value = '';
	document.getElementById('hdnIdUnidadeMultipla').value = '';
	
	document.getElementById('qtdRegistros').innerHTML = qtdLinhas + 1;
	
	}
	}
	}

}

function removerUnidade(idObj){

	document.getElementById(idObj).remove();
	qtdLinhas = document.getElementsByClassName('linhas').length;
	document.getElementById('qtdRegistros').innerHTML = qtdLinhas;
	
	if(qtdLinhas == 0){
		document.getElementById('divTableMultiplasUnidades').style.display = "none";
    }
	
}

function registroDuplicado(uf){
	var todasUfs = document.getElementsByClassName('ufsSelecionadas');
	var ufAdd = (uf.trim()).toUpperCase();
	
	if(todasUfs.length > 0){
	for (i = 0; i < todasUfs.length; i++) { 
	  var ufGrid = ((todasUfs[i].innerHTML).trim()).toUpperCase();
		if(ufGrid == ufAdd)
			{
				alert('Não é permitido adicionar mais de uma Unidade de abertura para a mesma UF.');
				return true;
			}
	}
	}
	
  return false;	
}



function changeUnidade(){
   //Limpando tabela de unidades Múltiplas e campos vinculados as unidades multiplas
    document.getElementById("corpoTabela").innerHTML = '';
    document.getElementById('txtUnidadeMultipla').value = '';
	document.getElementById('hdnIdUnidadeMultipla').value = '';
	document.getElementById('divTableMultiplasUnidades').style.display = "none";

  //Limpando campos vinculados a unidade Única
    document.getElementById("txtUnidade").value = '';
    document.getElementById("hdnIdUnidade").value = '';
	
	var unidUnic = document.getElementsByName('rdUnidade[]')[0].checked;

	document.getElementById("divCpUnidadeUnica").style.display = "none";
	document.getElementById("divCpUnidadeMultipla").style.display = "none";
	
	unidUnic ? document.getElementById("divCpUnidadeUnica").style.display = "inherit" : document.getElementById("divCpUnidadeMultipla").style.display = "inherit";
}

function changeIndicacaoInteressado(){
	var indIndireta = document.getElementsByName('indicacaoInteressado[]')[1].checked;
	document.getElementById('divRdIndicacaoIndiretaHide').style.display = "none";

	document.getElementsByName('indicacaoIndireta[]')[0].checked = false;
	document.getElementsByName('indicacaoIndireta[]')[0].checked = '';

	document.getElementsByName('indicacaoIndireta[]')[1].checked = false;
	document.getElementsByName('indicacaoIndireta[]')[1].checked = '';
    
	var elementLupa =   document.getElementById('imgLupaTipoDocumento');
	var percentLupa = getPercentTopStyle(elementLupa);
	
	if(indIndireta)
	{
	// var novoValor  = percentLupa + 12;
	 //novoValor = novoValor + '%';
	 //document.getElementById('imgLupaTipoDocumento').style.top =  novoValor
	 document.getElementById('divRdIndicacaoIndiretaHide').style.display = "inherit";
	}
	
}

function removerProcessoAssociado(remover){

	document.getElementById('selNivelAcesso').innerHTML = '';
	document.getElementById('divHipoteseLegal').style.display = "none";
console.log(remover);
	if(remover === '1'){
		objLupaTipoProcesso.remover();
	}
} 

function changeNivelAcesso(){

	document.getElementById('divNivelAcesso').style.display = "none";
	var padrao = document.getElementsByName('rdNivelAcesso[]')[1].checked;

	document.getElementById('selNivelAcesso').value = '';
	document.getElementById('selNivelAcesso').value = '';
	document.getElementById('selHipoteseLegal').value = '';
	document.getElementById('divHipoteseLegal').style.display = 'none';
	
	if(padrao){
		document.getElementById('divNivelAcesso').style.display = "inherit";
	}
	
}

function changeSelectNivelAcesso(){
	document.getElementById('selHipoteseLegal').value = '';
	
	var valorSelectNivelAcesso = document.getElementById('selNivelAcesso').value; 
	var valorHipoteseLegal     = document.getElementById('hdnParametroHipoteseLegal').value;
	
	if( valorSelectNivelAcesso == '<?= ProtocoloRN::$NA_RESTRITO ?>' && valorHipoteseLegal != '0'){

		document.getElementById('divHipoteseLegal').style.display = 'inherit';
	       
	}	else {

		document.getElementById('divHipoteseLegal').style.display = 'none';
		
	}
}


function changeDocPrincipal(){

    var gerado = document.getElementsByName('rdDocPrincipal[]')[0].checked;
    var tipo   = '';

    //REMOVIDO MODELO A PEDIDO DO CLIENTE
	//document.getElementById('divModelo').style.display = "none";
	document.getElementById('divDocPrincipal').style.display = "inherit";
	document.getElementById('fldDocEssenciais').style.display = "inherit";
	document.getElementById('fldDocComplementar').style.display = "inherit";
	
	if(objLupaTipoDocPrinc != null){
        	objLupaTipoDocPrinc.remover();
	}

	if(gerado){

		tipo = 'G';

		//REMOVIDO MODELO A PEDIDO DO CLIENTE
		//document.getElementById('selModelo').value = '';
		
		document.getElementById('txtTipoDocPrinc').value = '';

		//REMOVIDO MODELO A PEDIDO DO CLIENTE
		//document.getElementById('divModelo').style.display = "inherit";
		
		document.getElementsByName("rdDocPrincipal[]")[0].focus();
		//document.getElementById('fldDocComplementar').style.display = "inherit";
    }else{
    	tipo = 'E';
    	//REMOVIDO MODELO A PEDIDO DO CLIENTE
    	//document.getElementById('selModelo').value = '';
		document.getElementById('txtTipoDocPrinc').value = '';
    	document.getElementsByName("rdDocPrincipal[]")[1].focus();
    	//document.getElementById('fldDocComplementar').style.display = "inherit";
    }

	carregarComponenteAutoCompleteTpDocPrinc(tipo);

	
	//rdDocPrincipal
}

function changeDocPrincipalEdicao(){

    var gerado = document.getElementsByName('rdDocPrincipal[]')[0].checked;
    var tipo   = '';

 	document.getElementById('divDocPrincipal').style.display = "inherit";
	
	if(gerado){
		tipo = 'G';
		document.getElementsByName("rdDocPrincipal[]")[0].focus();
    }else{
    	tipo = 'E';
    	document.getElementsByName("rdDocPrincipal[]")[1].focus();
    }

	carregarComponenteAutoCompleteTpDocPrinc(tipo);
	
}


function inicializar(){

  inicializarTela();
  verificarQtdRegistrosUndMultipla();

  if ('<?=$_GET['acao']?>'!='tipo_processo_peticionamento_consultar'){
    carregarComponenteTipoDocumento(); //Doc Complementares - Seleção Múltipla
    carregarComponenteTipoProcesso(); // Seleção Única
    carregarComponenteUnidade();  // Seleção Única
    carregarComponenteUnidadeMultipla(); // Seleção única (Múltipla Tabela)
    carregarComponenteTipoDocumentoEssencial(); // Seleção Múltipla
    carregarDependenciaNivelAcesso();
  }

  
  if ('<?=$_GET['acao']?>'=='tipo_processo_peticionamento_cadastrar'){
    document.getElementById('txtTipoProcesso').focus();
  } else if ('<?=$_GET['acao']?>'=='tipo_processo_peticionamento_consultar'){
    infraDesabilitarCamposAreaDados();
  }else{
    document.getElementById('btnCancelar').focus();
  }
  infraEfeitoTabelas();

  if ('<?=$_GET['acao']?>' == 'tipo_processo_peticionamento_alterar'){
      changeDocPrincipalEdicao();
  }
  
}

function verificarQtdRegistrosUndMultipla(){
  var multiplasUnidades = document.getElementById('rdUnidadeMultipla').checked;

  if(multiplasUnidades){
	  var qtdRegistros =  document.getElementById('qtdRegistros').innerHTML;
	  var linhas       = (document.getElementsByClassName('linhas')).length;
	  if(qtdRegistros != linhas)
	  {
		 document.getElementById('qtdRegistros').innerHTML = linhas;
	  }
   }
}

function carregarDependenciaNivelAcesso(){
	  //Ajax para carregar os niveis de acesso após a escolha do tipo de processo
	  objAjaxIdNivelAcesso = new infraAjaxMontarSelectDependente('txtTipoProcesso','selNivelAcesso','<?=$strLinkAjaxNivelAcesso?>');
	  objAjaxIdNivelAcesso.prepararExecucao = function(){
	    document.getElementById('selNivelAcesso').innerHTML  = '';
	    return infraAjaxMontarPostPadraoSelect('null','','null') + '&idTipoProcesso='+document.getElementById('hdnIdTipoProcesso').value;
	  }
}

function inicializarTela(){
	//document.getElementById('divRdIndicacaoIndiretaHide').style.display = "none";
	//document.getElementById('divNivelAcesso').style.display = "none";
	//document.getElementById('divModelo').style.display = "none";
	
}

function carregarComponenteUnidadeMultipla(){
	objLupaUnidadeMultipla = new infraLupaText('txtUnidadeMultipla','hdnIdUnidadeMultipla','<?=$strLinkUnidadeMultiplaSelecao?>');

	objLupaUnidadeMultipla.finalizarSelecao = function(){
		objAutoCompletarUnidadeMultipla.selecionar(document.getElementById('hdnIdUnidadeMultipla').value,document.getElementById('txtUnidadeMultipla').value,  document.getElementById('hdnUfUnidadeMultipla').value);
	  }
	  
	objAutoCompletarUnidadeMultipla = new infraAjaxAutoCompletar('hdnIdUnidadeMultipla','txtUnidadeMultipla','<?=$strLinkAjaxUnidade?>');
	objAutoCompletarUnidadeMultipla.limparCampo = false;

	objAutoCompletarUnidadeMultipla.prepararExecucao = function(){
	  return 'palavras_pesquisa='+document.getElementById('txtUnidadeMultipla').value;
	  };

	  objAutoCompletarUnidadeMultipla.processarResultado = function(id,descricao,uf){
	  if (id!=''){
	  document.getElementById('hdnIdUnidadeMultipla').value = id;
	  document.getElementById('txtUnidadeMultipla').value = descricao;
	  document.getElementById('hdnUfUnidadeMultipla').value = uf;
	  }
	  }
	  objAutoCompletarUnidadeMultipla.selecionar('<?=$strIdUnidade?>','<?=PaginaSEI::getInstance()->formatarParametrosJavascript($nomeUnidadeMultipla);?>');
}


function carregarComponenteUnidade(){
	objLupaUnidade = new infraLupaText('txtUnidade','hdnIdUnidade','<?=$strLinkUnidadeSelecao?>');

	objLupaUnidade.finalizarSelecao = function(){
		objAutoCompletarUnidade.selecionar(document.getElementById('hdnIdUnidade').value,document.getElementById('txtUnidade').value);
	  }

	  
	objAutoCompletarUnidade = new infraAjaxAutoCompletar('hdnIdUnidade','txtUnidade','<?=$strLinkAjaxUnidade?>');
	objAutoCompletarUnidade.limparCampo = false;

	objAutoCompletarUnidade.prepararExecucao = function(){
	  return 'palavras_pesquisa='+document.getElementById('txtUnidade').value;
	  };

	  objAutoCompletarUnidade.processarResultado = function(id,descricao,complemento){
	  if (id!=''){
	  document.getElementById('hdnIdUnidade').value = id;
	  document.getElementById('txtUnidade').value = descricao;
	  }
	  }
	  objAutoCompletarUnidade.selecionar('<?=$strIdUnidade?>','<?=PaginaSEI::getInstance()->formatarParametrosJavascript($strNomeRemetente);?>');
}

function carregarComponenteLupaTpDocPrinc(acaoComponente){

	var gerado = document.getElementsByName('rdDocPrincipal[]')[0].checked;
	var tipo   = gerado ? 'G' : 'E';
	var link = '<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=serie_peticionamento_selecionar&filtro=1&tipoDoc=E&tipo_selecao=1&id_object=objLupaTipoDocPrinc'); ?>';
	
	if(gerado)
    {
	 link   = '<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=serie_peticionamento_selecionar&filtro=1&tipoDoc=G&tipo_selecao=1&id_object=objLupaTipoDocPrinc'); ?>';
	}
	
	objLupaTipoDocPrinc = new infraLupaText('txtTipoDocPrinc','hdnIdTipoDocPrinc', link);

	objLupaTipoDocPrinc.finalizarSelecao = function(){
		objAutoCompletarTipoDocPrinc.selecionar(document.getElementById('hdnIdTipoDocPrinc').value,document.getElementById('txtTipoDocPrinc').value);
	  }

	acaoComponente == 'S' ? objLupaTipoDocPrinc.selecionar(700,500) : objLupaTipoDocPrinc.remover();
}

function carregarComponenteLupaTpDocComplementar(acaoComponente){
	acaoComponente == 'S' ? objLupaTipoDocumento.selecionar(700,500) : objLupaTipoDocumento.remover();
}

function returnLinkModificado(link, tipo){
		var arrayLink = link.split('&filtro=1');

		var linkFim = '';
		if(arrayLink.length == 2){
		  linkFim = arrayLink[0] + '&filtro=1&tipoDoc=' +tipo + arrayLink[1];
		}else{
		 linkFim = link;
		}
		
  return linkFim;
}


function carregarComponenteAutoCompleteTpDocPrinc(tipo){
	
	  objAutoCompletarTipoDocPrinc = new infraAjaxAutoCompletar('hdnIdTipoDocPrinc','txtTipoDocPrinc','<?=$strLinkAjaxTipoDocPrinc?>');
	  objAutoCompletarTipoDocPrinc.limparCampo = true;

	  objAutoCompletarTipoDocPrinc.prepararExecucao = function(){
	  return 'palavras_pesquisa='+document.getElementById('txtTipoDocPrinc').value + '&tipoDoc=' + tipo;
	  };

	  objAutoCompletarTipoDocPrinc.processarResultado = function(id,descricao,complemento){
	  if (id!=''){
	  document.getElementById('hdnIdTipoDocPrinc').value = id;
	  document.getElementById('txtTipoDocPrinc').value = descricao;
	  }
	  }
	  objAutoCompletarTipoDocPrinc.selecionar('<?=$strIdTipoDocPrinc?>','<?=PaginaSEI::getInstance()->formatarParametrosJavascript($strNomeRemetente);?>');
}



function carregarComponenteTipoProcesso(){
	 objLupaTipoProcesso = new infraLupaText('txtTipoProcesso','hdnIdTipoProcesso','<?=$strLinkTipoProcessoSelecao?>');

	  objLupaTipoProcesso.finalizarSelecao = function(){
	  objAutoCompletarTipoProcesso.selecionar(document.getElementById('hdnIdTipoProcesso').value,document.getElementById('txtTipoProcesso').value);
		  objAjaxIdNivelAcesso.executar();
	  }

	  objAutoCompletarTipoProcesso = new infraAjaxAutoCompletar('hdnIdTipoProcesso','txtTipoProcesso','<?=$strLinkAjaxTipoProcesso?>');
	  //objAutoCompletarTipoProcesso.maiusculas = true;
	  //objAutoCompletarTipoProcesso.mostrarAviso = true;
	  //objAutoCompletarTipoProcesso.tempoAviso = 1000;
	  //objAutoCompletarTipoProcesso.tamanhoMinimo = 3;
	  objAutoCompletarTipoProcesso.limparCampo = false;
	  //objAutoCompletarTipoProcesso.bolExecucaoAutomatica = false;
	  

	  objAutoCompletarTipoProcesso.prepararExecucao = function(){
	  return 'palavras_pesquisa='+document.getElementById('txtTipoProcesso').value;
	  };

	  objAutoCompletarTipoProcesso.processarResultado = function(id,descricao,complemento){
	  if (id!=''){
	  document.getElementById('hdnIdTipoProcesso').value = id;
	  document.getElementById('txtTipoProcesso').value = descricao;
	  objAjaxIdNivelAcesso.executar();
	  }
	  }
	  objAutoCompletarTipoProcesso.selecionar('<?=$strIdTipoProcesso?>','<?=PaginaSEI::getInstance()->formatarParametrosJavascript($strNomeRemetente);?>');
	 
}

//Carrega o documento para o documento complementar
function carregarComponenteTipoDocumento(){
		
	objAutoCompletarTipoDocumento = new infraAjaxAutoCompletar('hdnIdSerie', 'txtSerie','<?=$strLinkAjaxTipoDocumento?>');
	objAutoCompletarTipoDocumento.limparCampo = true;
		
	objAutoCompletarTipoDocumento.prepararExecucao = function(){
		var gerado = document.getElementsByName('rdDocPrincipal[]')[0].checked;
	    //var tipo   = gerado ? 'G' : 'E';
	    //20160908 - Essencial e Complementar SEMPRE EXTERNO
	    var tipo  = 'E';
	    return 'palavras_pesquisa='+document.getElementById('txtSerie').value + '&tipoDoc=' + tipo;
		};
		  
		objAutoCompletarTipoDocumento.processarResultado = function(id,nome,complemento){
		    
		    if (id!=''){
		      var options = document.getElementById('selDescricao').options;

		      if(options != null){
		      for(var i=0;i < options.length;i++){
		        if (options[i].value == id){
		          alert('Tipo de Documento já consta na lista.');
		          break;
		        }
		      }
		      }
		      
		      if (i==options.length){
		      
		        for(i=0;i < options.length;i++){
		         options[i].selected = false; 
		        }
		      
		        opt = infraSelectAdicionarOption(document.getElementById('selDescricao'),nome,id);
		        
		        objLupaTipoDocumento.atualizar();
		        
		        opt.selected = true;
		      }
		                  
		      document.getElementById('txtSerie').value = '';
		      document.getElementById('txtSerie').focus();
		      
		    }
		  };
	    
		  objLupaTipoDocumento = new infraLupaSelect('selDescricao' , 'hdnSerie',  '<?=$strLinkTipoDocumentoSelecao?>'); 
}

//Carrega o documento para o documento essencial
function carregarComponenteTipoDocumentoEssencial(){
		
	objAutoCompletarTipoDocumentoEssencial = new infraAjaxAutoCompletar('hdnIdSerieEssencial', 'txtSerieEssencial','<?=$strLinkAjaxTipoDocumento?>');
	objAutoCompletarTipoDocumentoEssencial.limparCampo = true;
		
	objAutoCompletarTipoDocumentoEssencial.prepararExecucao = function(){
		var gerado = document.getElementsByName('rdDocPrincipal[]')[0].checked;
	    //var tipo   = gerado ? 'G' : 'E';
	    //20160908 - Essencial e Complementar SEMPRE EXTERNO	    
	    var tipo = 'E';
	    return 'palavras_pesquisa='+document.getElementById('txtSerieEssencial').value + '&tipoDoc=' + tipo;
		};
		  
		objAutoCompletarTipoDocumentoEssencial.processarResultado = function(id,nome,complemento){
		    
		    if (id!=''){
		      var options = document.getElementById('selDescricaoEssencial').options;

		      if(options != null){
		      for(var i=0;i < options.length;i++){
		        if (options[i].value == id){
		          alert('Tipo de Documento já consta na lista.');
		          break;
		        }
		      }
		      }
		      
		      if (i==options.length){
		      
		        for(i=0;i < options.length;i++){
		         options[i].selected = false; 
		        }
		      
		        opt = infraSelectAdicionarOption(document.getElementById('selDescricaoEssencial'),nome,id);
		        
		        objLupaTipoDocumentoEssencial.atualizar();
		        
		        opt.selected = true;
		      }
		                  
		      document.getElementById('txtSerieEssencial').value = '';
		      document.getElementById('txtSerieEssencial').focus();
		      
		    }
		  };
	    
		  objLupaTipoDocumentoEssencial = new infraLupaSelect('selDescricaoEssencial' , 'hdnSerieEssencial',  '<?=$strLinkTipoDocumentoEssencialSelecao?>'); 
}


function validarCadastro() {

	var valorHipoteseLegal     = document.getElementById('hdnParametroHipoteseLegal').value;
	
  if (infraTrim(document.getElementById('txtTipoProcesso').value)=='') {
    alert('Informe o Tipo de Processo.');
    document.getElementById('txtTipoProcesso').focus();
	return false;
 }

  if (infraTrim(document.getElementById('txtOrientacoes').value)=='') {
	    alert('Informe as Orientações.');
	    document.getElementById('txtOrientacoes').focus();
	    return false;
	}

//Validar Unidade SM - EU6155
var unidUnic = document.getElementsByName('rdUnidade[]')[0].checked;
var multUnic = document.getElementsByName('rdUnidade[]')[1].checked;

if(unidUnic){
	if (infraTrim(document.getElementById('hdnIdUnidade').value)=='') {
		    alert('Informe a Unidade para abertura do processo.');
		    document.getElementById('txtUnidade').focus();
		    return false;
	}
}

if(multUnic){
	var objUndSelecionadas = document.getElementsByClassName('linhas');
	if(objUndSelecionadas.length == 0){
		   alert('É necessário informar ao menos uma Unidade para Abertura de Processo.');
		   document.getElementById('txtUnidadeMultipla').focus();
		   return false;
	}
}

if(!multUnic && !unidUnic){
    alert('Informe a Unidade para abertura do processo.');
    document.getElementById('txtUnidade').focus();
    return false;
}


  //Validar Rádio Indicação de Interessado
  var elemsIndInt = document.getElementsByName("indicacaoInteressado[]");

  validoIndInt = false;
  for (var i=0; i<elemsIndInt.length; i++) {
  if (elemsIndInt[i].checked === true){
	  validoIndInt = true;
  }
  }

  if(!validoIndInt){
	    alert('Informe a Indicação de Interessado.');
	    document.getElementById('rdUsuExterno').focus();
	    return false;
  }

//Validar Rádio Indicação de Interessado
var indicacaoIndireta = document.getElementById('rdIndicacaoIndireta').checked;

if(indicacaoIndireta)
 {
   var elemsIndInd = document.getElementsByName("indicacaoIndireta[]");

   validoIndInd = false;
   for (var i=0; i<elemsIndInd.length; i++) 
 	{
   	   if (elemsIndInd[i].checked === true)
   	   {
   		validoIndInd = true;
       }
    }

  if(!validoIndInd){
	    alert('Informe a Indicação de Interessado.');
	    document.getElementsByName('indicacaoIndireta[]')[0].focus();
	    return false;
  }
}

//Validar Nível Acesso
var elemsNA = document.getElementsByName("rdNivelAcesso[]");

validoNA = false;
for (var i=0; i<elemsNA.length; i++) {
if (elemsNA[i].checked === true){
	validoNA = true;
}
}

if (((infraTrim(document.getElementById('selNivelAcesso').value)=='') && document.getElementById('rdPadrao').checked) || (!validoNA)) {
	 alert('Informe o Nível de Acesso.');
	 document.getElementById('rdUsuExterno').focus();
    return false;
  } 

else if( document.getElementById('selNivelAcesso').value == <?= ProtocoloRN::$NA_RESTRITO ?> && valorHipoteseLegal != '0'){

	//validar hipotese legal
	if( document.getElementById('selHipoteseLegal').value == '' ){
		alert('Informe a Hipótese legal padrão.');
		document.getElementById('selHipoteseLegal').focus();
	    return false;
	}
	   	
} 
  
//Documento Principal
var elemsDP = document.getElementsByName("rdDocPrincipal[]");

validoDP = false;

for (var i=0; i<elemsDP.length; i++) {

  if (elemsDP[i].checked == true){
	validoDP = true;
  }
  
}

if (!validoDP) {
	alert('Informe o Documento Principal.');
	 document.getElementById('rdDocGerado').focus();
    return false;
  }

//REMOVIDO MODELO A PEDIDO DO CLIENTE
/*
if (elemsDP[0].checked == true && infraTrim(document.getElementById('selModelo').value)=='') {
	 alert('Informe o Modelo.');
   document.getElementById('selModelo').focus();
   return false;
}
*/

if (infraTrim(document.getElementById('txtTipoDocPrinc').value)=='') {
	 alert('Informe o Tipo de Documento Principal.');
    document.getElementById('txtOrientacoes').focus();
    return false;
}
  
  return true;
}

function OnSubmitForm() {
  preencherUnidadesMultiplas();
  return validarCadastro();
}

function preencherUnidadesMultiplas(){
	var arrayIdsBd          = new Array();
	var objUndSelecionadas = document.getElementsByClassName('linhas');

	for(var i=0;i < objUndSelecionadas.length;i++)
	{
	  idTabela = (objUndSelecionadas[i].id).split('_')[1];
	  arrayIdsBd.push(idTabela);
	}

   document.getElementById("hdnUnidadesSelecionadas").value = JSON.stringify(arrayIdsBd);
   document.getElementById("hdnCorpoTabela").value = document.getElementById('corpoTabela').innerHTML; 
}

function getPercentTopStyle(element) {
	    var parent = element.parentNode,
	        computedStyle = getComputedStyle(element),
	        value;
	    parent.style.display = 'none';
	    value = computedStyle.getPropertyValue('top');
	    parent.style.removeProperty('display');
	  
	  if(value != ''){
	    valor = value.replace('%','');
	    return parseInt(valor);
	  }
	  
	    return false;
	}

</script>