<?php

try {

	require_once dirname(__FILE__).'/../../SEI.php';
  
  	session_start();
  
  	PaginaSEIExterna::getInstance()->setBolXHTML(false);
  	//////////////////////////////////////////////////////////////////////////////
  	//InfraDebug::getInstance()->setBolLigado(false);
  	//InfraDebug::getInstance()->setBolDebugInfra(true);  
  	//InfraDebug::getInstance()->limpar();
  	//////////////////////////////////////////////////////////////////////////////
  
  	SessaoSEIExterna::getInstance()->validarLink();
  	PaginaSEIExterna::getInstance()->setTipoPagina( InfraPagina::$TIPO_PAGINA_SIMPLES );
  	SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);
  
  	$arrComandos = array();
  	$strTitulo = '';
  
	switch($_GET['acao']){
		
		case 'md_pet_editor_imagem_upload':
			
			if (isset($_FILES['filArquivo'])){
				PaginaSEIExterna::getInstance()->processarUpload('filArquivo', DIR_SEI_TEMP, false);
			}
			die;
		
		case 'md_pet_editor_montar':
			
			$serieDTO = new SerieDTO();
			$serieDTO->retTodos();
			$serieDTO->setNumIdSerie( $_GET['id_serie'] );
			$serieDTO = (new SerieRN())->consultarRN0644( $serieDTO );
			
			//recupera seções do modelo
			$objSecaoModeloRN = new SecaoModeloRN();
			$objSecaoModeloDTO = new SecaoModeloDTO();
			$objSecaoModeloDTO->retNumIdSecaoModelo();
			$objSecaoModeloDTO->retStrNome();
			$objSecaoModeloDTO->retStrSinSomenteLeitura();
			$objSecaoModeloDTO->retStrSinAssinatura();
			$objSecaoModeloDTO->retStrSinPrincipal();
			$objSecaoModeloDTO->retStrSinDinamica();
			$objSecaoModeloDTO->retStrSinCabecalho();
			$objSecaoModeloDTO->retStrSinRodape();
			$objSecaoModeloDTO->retStrSinHtml();
			$objSecaoModeloDTO->retStrConteudo();
			$objSecaoModeloDTO->retNumOrdem();
			$objSecaoModeloDTO->setStrSinSomenteLeitura('N');
			$objSecaoModeloDTO->setNumIdModelo($serieDTO->getNumIdModelo());
			$objSecaoModeloDTO->setOrdNumOrdem(InfraDTO::$TIPO_ORDENACAO_ASC);
			
			$arrObjSecaoModeloDTO = $objSecaoModeloRN->listar($objSecaoModeloDTO);
					
			if (empty($arrObjSecaoModeloDTO)) {
				throw new InfraException('Modelo do documento não contém seções.');
			}
			
			//======================= INICIO APLICANDO ESTILOS
			$conjuntoEstilosRN = new ConjuntoEstilosRN();
			$conjuntoEstilosDTO = new ConjuntoEstilosDTO();
			$conjuntoEstilosDTO->setStrSinUltimo('S');
			$conjuntoEstilosDTO->retTodos();
			$conjuntoEstilosDTO = $conjuntoEstilosRN->consultar($conjuntoEstilosDTO);

			//busca os estilos permitidos por seção-modelo
			$objRelSecaoModCjEstilosItemDTO = new RelSecaoModCjEstilosItemDTO();
			$objRelSecaoModCjEstilosItemDTO->retNumIdSecaoModelo();
			$objRelSecaoModCjEstilosItemDTO->retStrNomeEstilo();
			$objRelSecaoModCjEstilosItemDTO->retStrFormatacao();
			$objRelSecaoModCjEstilosItemDTO->setNumIdSecaoModelo(InfraArray::converterArrInfraDTO($arrObjSecaoModeloDTO, 'IdSecaoModelo'), InfraDTO::$OPER_IN);
			$objRelSecaoModCjEstilosItemDTO->setOrdStrNomeEstilo(InfraDTO::$TIPO_ORDENACAO_ASC);
			$objRelSecaoModCjEstilosItemDTO->setNumIdConjuntoEstilos($conjuntoEstilosDTO->getNumIdConjuntoEstilos());
			$objRelSecaoModCjEstilosItemRN = new RelSecaoModCjEstilosItemRN();
			$arrObjRelSecaoModCjEstilosItemDTO = InfraArray::indexarArrInfraDTO($objRelSecaoModCjEstilosItemRN->listar($objRelSecaoModCjEstilosItemDTO), 'IdSecaoModelo', true);

			$strFormatos = "";
			foreach ($arrObjSecaoModeloDTO as $objSecaoModeloDTO) {
				if (isset($arrObjRelSecaoModCjEstilosItemDTO[$objSecaoModeloDTO->getNumIdSecaoModelo()])) {
					foreach ($arrObjRelSecaoModCjEstilosItemDTO[$objSecaoModeloDTO->getNumIdSecaoModelo()] as $objRelSecaoModCjEstilosItemDTO) {
						$strFormatos .= $objRelSecaoModCjEstilosItemDTO->getStrNomeEstilo() . "|";
					}
				}
				$strFormatos = rtrim($strFormatos, '|');
			}

			$objImagemFormatoDTO = new ImagemFormatoDTO();
			$objImagemFormatoDTO->retStrFormato();
			$objImagemFormatoDTO->setBolExclusaoLogica(false);
			
			$objImagemFormatoRN = new ImagemFormatoRN();
			$arrImagemPermitida = InfraArray::converterArrInfraDTO($objImagemFormatoRN->listar($objImagemFormatoDTO), 'Formato');
			if (in_array('jpg', $arrImagemPermitida) && !in_array('jpeg', $arrImagemPermitida)) $arrImagemPermitida[] = 'jpeg';
					
			$txtConteudo = '';
			
			if( SessaoSEIExterna::getInstance()->isSetAtributo('docPrincipalConteudoHTML')) {
				
				$txtConteudo = SessaoSEIExterna::getInstance()->getAtributo('docPrincipalConteudoHTML');
				
			} else {  			
				//gera copia das secoes do modelo, ja formatando o conteudo com a formatacao padrao
				foreach ($arrObjSecaoModeloDTO as $objSecaoModeloDTO) {  					
					$txtConteudo .= $objSecaoModeloDTO->getStrConteudo();
				}  			
			}
					
			//======================= FIM APLICANDO ESTILOS
					
			$objEditorRN = new MdPetEditorUsuarioExternoRN();

			$objEditorDTO = new EditorDTO();  		 
			$objEditorDTO->setStrNomeCampo('txaConteudo');  		
			$objEditorDTO->setStrSinSomenteLeitura('N');  		 
					
			$strConteudoCss = $objEditorRN->montarCssEditor( $conjuntoEstilosDTO->getNumIdConjuntoEstilos()  );

			$objEditorDTO->setStrConteudoCss( $strConteudoCss );
			$objEditorDTO->setStrCss( $strConteudoCss );
			$objEditorDTO->setStrSinEstilos('S');
			
			$retEditor = $objEditorRN->montarSimples($objEditorDTO);  	

			$retEditor->setStrConteudoCss( $strConteudoCss );
			$retEditor->setStrCss( $strConteudoCss );
			$retEditor->setStrSinEstilos('S');
					
			if (isset($_POST['hdnSubmit'])) {

				try{
					//TODO: Possível risco de consumo excessivo de memória do servidor
					SessaoSEIExterna::getInstance()->setAtributo('docPrincipalConteudoHTML', $_POST['txaConteudo']);
					SessaoSEIExterna::getInstance()->setAtributo('idConjuntoEstilo', $_POST['idConjuntoEstilo']);
					$txtConteudo = $_POST['txaConteudo'];
					
				}catch(Exception $e){
					PaginaSEIExterna::getInstance()->processarExcecao($e);
				}

			}
			break;
			
			default:
				throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
		break;
		
	}
}
catch(Exception $e){
	PaginaSEIExterna::getInstance()->processarExcecao($e);
}

  		
PaginaSEIExterna::getInstance()->montarDocType();
PaginaSEIExterna::getInstance()->abrirHtml();
PaginaSEIExterna::getInstance()->abrirHead();
PaginaSEIExterna::getInstance()->montarMeta();
PaginaSEIExterna::getInstance()->montarTitle(':: '.PaginaSEIExterna::getInstance()->getStrNomeSistema().' - '.$strTitulo.' ::');
PaginaSEIExterna::getInstance()->montarStyle();
EditorINT::montarCss();
PaginaSEIExterna::getInstance()->abrirStyle();
?>
#lblNome {position:absolute;left:0%;top:0%;width:30%;}
#txtNome {position:absolute;left:0%;top:14%;width:30%;}
#lblDescricao {position:absolute;left:0%;top:40%;width:95%;}
#txtDescricao {position:absolute;left:0%;top:54%;width:95%;}
#lblConteudo {position:absolute;left:0%;top:25%;width:95%;}
#txaConteudo {height:300px;}
.cke_contents#cke_1_contents {height:300px;}
<?
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
?>
function inicializar(){
	infraEfeitoTabelas(); 
	infraAdicionarEvento(window,'resize',redimensionar);
	CKEDITOR.on('instanceReady', function( evt ) { redimensionar();});
}

function redimensionar() {
	setTimeout(function(){

		var tamComandos=document.getElementById('divComandos').offsetHeight;
		var divEd=document.getElementById('divEditores');
		if (tamComandos>divEd.offsetHeight) tamComandos-=divEd.offsetHeight;
		var tamEditor=infraClientHeight()- tamComandos - 20;
		divEd.style.height = (tamEditor>0?tamEditor:1) +'px';

	},0);
}

<?
PaginaSEIExterna::getInstance()->fecharJavaScript();
echo $retEditor->getStrInicializacao();
PaginaSEIExterna::getInstance()->fecharHead();
?>
<body onload="inicializar();" style="margin: 5px;overflow: hidden">  

	<form id="frmTextoPadraoInternoCadastro" method="post" onsubmit="return OnSubmitForm();" 
      action="<?=PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?id_serie=' . $_GET['id_serie'] . '&acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">

    	<div id="divComandos" style="margin:0px;"></div>
    	<? if (PaginaSEIExterna::getInstance()->getNumTipoBrowser()==InfraPagina::$TIPO_BROWSER_IE7 ) echo '<br style="margin:0;font-size:1px;"/>'; ?>

		<div id="divEditores" style="overflow: auto;border-top:2px solid;border-bottom:0px;">
			<textarea id="txaConteudo" name="txaConteudo" rows="10" class="infraTextarea" tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>"><?=PaginaSEI::tratarHTML($txtConteudo)?></textarea>
			<script type="text/javascript">
			CKEDITOR.replace('txaConteudo',{ 'autoGrow_onStartup':'true', "stylesheetParser_validSelectors":/^(p).(<?=$strFormatos?>)$/i, 
			'toolbar':[["Save"],["Find","Replace","-","RemoveFormat","Bold","Italic","Underline","Strike","Subscript","Superscript","Maiuscula","Minuscula","TextColor","BGColor"],["Cut","Copy","PasteFromWord","PasteText","-","Undo","Redo","ShowBlocks","Symbol","Scayt"],["NumberedList","BulletedList","-","Outdent","Indent","base64image"],["Table","SpecialChar","SimpleLink","Extenso","Zoom"],["Styles"]]});
			</script>
		</div>
  
		<input type="hidden" id="hdnVersao" name="hdnVersao" value="0" />
		<input type="hidden" id="hdnIdConjuntoEstilos" name="hdnIdConjuntoEstilos" value="<?= $conjuntoEstilosDTO->getNumIdConjuntoEstilos() ?>" />
		<input type="hidden" id="hdnIgnorarNovaVersao" name="hdnIgnorarNovaVersao" value="N" />
		<input type="hidden" name="hdnSubmit" />

  	</form>
  
<?
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
?>