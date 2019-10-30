  <?php 
  try {
 require_once dirname(__FILE__).'/../../SEI.php';
  
  	session_start();
  
  	PaginaSEI::getInstance()->setBolXHTML(false);
  	//////////////////////////////////////////////////////////////////////////////
  	//InfraDebug::getInstance()->setBolLigado(false);
  	//InfraDebug::getInstance()->setBolDebugInfra(true);
  
  	//InfraDebug::getInstance()->limpar();
  	//////////////////////////////////////////////////////////////////////////////
  
  	SessaoSEI::getInstance()->validarLink();
  
  	PaginaSEI::getInstance()->verificarSelecao('tipo_processo_peticionamento_selecionar_orientacoes');
  
  	SessaoSEI::getInstance()->validarPermissao($_GET['acao']);
  
  	$arrComandos = array();
  
  switch($_GET['acao']){
  	case 'md_pet_orientacoes_tipo_destinatario':
  		$strTitulo = "Orientações Tipo de Destinatário";
  		$arrComandos[] = '<button type="submit" accesskey="S" name="sbmCadastrarOrientacoesPetIndisp" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
  		$arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao'])).'\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';
  
  		$objEditorRN=new EditorRN();
  		$objEditorDTO=new EditorDTO();
  		 
  		$objEditorDTO->setStrNomeCampo('txaConteudo');
  		
  		$objEditorDTO->setStrSinSomenteLeitura('N');
  		 
  		$retEditor = $objEditorRN->montarSimples($objEditorDTO);
  		
  		
  		$objMdPetIntOrientacoesDTO2 = new MdPetIntOrientacoesDTO();
  		$objMdPetIntOrientacoesDTO2->setNumIdIntOrientacoesTipoDestinatario(MdPetIntOrientacoesRN::$ID_FIXO_INT_ORIENTACOES);
  		$objMdPetIntOrientacoesDTO2->retTodos();
 
  		$objMdPetIntOrientacoesRN  = new MdPetIntOrientacoesRN();
  		$objLista = $objMdPetIntOrientacoesRN->listar($objMdPetIntOrientacoesDTO2);
  		$alterar = count($objLista) > 0;
  		
  		$txtConteudo =''; 
  		if($alterar){
  			$txtConteudo = $objLista[0]->getStrOrientacoesTipoDestinatario();
  		}
  		
		$objMdPetIntOrientacoesDTO = new MdPetIntOrientacoesDTO();
		$objMdPetIntOrientacoesDTO->setStrOrientacoesTipoDestinatario($_POST['txaConteudo']);  				
		$objMdPetIntOrientacoesDTO->setNumIdIntOrientacoesTipoDestinatario(MdPetIntOrientacoesRN::$ID_FIXO_INT_ORIENTACOES);
		
  		if (isset($_POST['sbmCadastrarOrientacoesPetIndisp'])) {
  			try{
  				$objEditorRN->validarTagsCriticas(array('jpg','png'), $_POST['txaConteudo']);
  				$objMdPetIntOrientacoesDTO2->setStrOrientacoesTipoDestinatario($_POST['txaConteudo']);

				//Estilo
				$conjuntoEstilosRN = new ConjuntoEstilosRN();
		  		$conjuntoEstilosDTO = new ConjuntoEstilosDTO();
		  		$conjuntoEstilosDTO->setStrSinUltimo('S');
		  		$conjuntoEstilosDTO->retNumIdConjuntoEstilos();
		  		$conjuntoEstilosDTO = $conjuntoEstilosRN->consultar( $conjuntoEstilosDTO );
		  		$objMdPetIntOrientacoesDTO2->setNumIdConjuntoEstilos( $conjuntoEstilosDTO->getNumIdConjuntoEstilos() );
					
                $objMdPetIntOrientacoesDTO =  $alterar ? $objMdPetIntOrientacoesRN->alterar($objMdPetIntOrientacoesDTO2) : $objMdPetIntOrientacoesRN->cadastrar($objMdPetIntOrientacoesDTO);
  				header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao']));
  				die;
  			}catch(Exception $e){
  				PaginaSEI::getInstance()->processarExcecao($e);
  			}
  		}
  		break;
  		
  		default:
  			throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
  	break;
  	
  }
  }
  		catch(Exception $e){
  			PaginaSEI::getInstance()->processarExcecao($e);
  		}
  		
  		
  		PaginaSEI::getInstance()->montarDocType();
  		PaginaSEI::getInstance()->abrirHtml();
  		PaginaSEI::getInstance()->abrirHead();
  		PaginaSEI::getInstance()->montarMeta();
  		PaginaSEI::getInstance()->montarTitle(':: '.PaginaSEI::getInstance()->getStrNomeSistema().' - '.$strTitulo.' ::');
  		PaginaSEI::getInstance()->montarStyle();
  		EditorINT::montarCss();
  		PaginaSEI::getInstance()->abrirStyle();
  		?>
  		#lblNome {position:absolute;left:0%;top:0%;width:30%;}
  		#txtNome {position:absolute;left:0%;top:14%;width:30%;}
  		
  		#lblDescricao {position:absolute;left:0%;top:40%;width:95%;}
  		#txtDescricao {position:absolute;left:0%;top:54%;width:95%;}
  		
  		#lblConteudo {position:absolute;left:0%;top:25%;width:95%;}
  		
  		.cke_contents#cke_1_contents {height:290px !important;}
  		<?
  		PaginaSEI::getInstance()->fecharStyle();
  		PaginaSEI::getInstance()->montarJavaScript();
  		PaginaSEI::getInstance()->abrirJavaScript();
  		?>
  function inicializar(){
    infraEfeitoTabelas(); 
  }
  		<?php 
  		PaginaSEI::getInstance()->fecharJavaScript();
  		echo $retEditor->getStrInicializacao();
  		PaginaSEI::getInstance()->fecharHead();
  		PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
  ?>
  
  <form id="frmTextoPadraoInternoCadastro" method="post" onsubmit="return OnSubmitForm();" action="<?=PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">
  <?
PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaSEI::getInstance()->abrirAreaDados('3em');
?>

<label id="lblConteudo" for="txaConteudo" accesskey="" class="infraLabelObrigatorio">Conteúdo:
    <img align="top" style="height:16px; width:16px;" id="imgAjuda" src="/infra_css/imagens/ajuda.gif" 
         name="ajuda" onmouseover="return infraTooltipMostrar('As orientações descritas abaixo serão exibidas na tela Gerar Intimação Eletrônica para os Usuários internos.');" onmouseout="return infraTooltipOcultar();" alt="Ajuda" class="infraImg">   
</label>
<?php 
PaginaSEI::getInstance()->fecharAreaDados();

?>
  <table style="width: 100%">
    <td style="width: 95%">
      <div id="divEditores" style="overflow: auto;">
        <textarea id="txaConteudo" name="txaConteudo" rows="10" class="infraTextarea" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"><?=PaginaSEI::tratarHTML($txtConteudo)?></textarea>
        <script type="text/javascript">
          <?=$retEditor->getStrEditores();?>
        </script>
      </div>
    </td>
  </table>
  </form>
  
  <?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>