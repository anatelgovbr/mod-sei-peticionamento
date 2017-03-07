<?
/**
* ANATEL
*
* 28/06/2016 - criado por marcelo.bezerra - CAST
*
*/

try {
	
	require_once dirname(__FILE__).'/../../SEI.php';

	//Data
	require_once dirname(__FILE__).'/util/DataUtils.php';
	
	session_start();
	
	//////////////////////////////////////////////////////////////////////////////
	InfraDebug::getInstance()->setBolLigado(false);
	InfraDebug::getInstance()->setBolDebugInfra(false);
	InfraDebug::getInstance()->limpar();
	//////////////////////////////////////////////////////////////////////////////
	
	SessaoSEIExterna::getInstance()->validarLink();
	PaginaSEIExterna::getInstance()->prepararSelecao('recibo_peticionamento_usuario_externo_selecionar');
	SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);

	switch($_GET['acao']){

		case 'recibo_peticionamento_usuario_externo_selecionar':
			
			$strTitulo = PaginaSEIExterna::getInstance()->getTituloSelecao('Selecionar Recibo','Selecionar Recibos');

			//Se cadastrou alguem
			if ($_GET['acao_origem']=='recibo_peticionamento_usuario_externo_cadastrar'){
				
				if (isset($_GET['id_md_pet_rel_recibo_protoc'])){
					PaginaSEIExterna::getInstance()->adicionarSelecionado($_GET['id_md_pet_rel_recibo_protoc']);
				}
			}
			
			break;

		case 'recibo_peticionamento_usuario_externo_listar':
			
			$strTitulo = 'Recibos Eletrônicos de Protocolo';	
			break;

		default:
			throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
	}

	$arrComandos = array();
	$arrComandos[] = '<button type="button" accesskey="p" id="btnPesquisar" value="Pesquisar" onclick="pesquisar();" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';
	$arrComandos[] = '<button type="button" accesskey="c" id="btnFechar" value="Fechar" onclick="location.href=\''.PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=usuario_externo_controle_acessos&acao_origem='.$_GET['acao'])).'\'" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

	$bolAcaoCadastrar = SessaoSEIExterna::getInstance()->verificarPermissao('recibo_peticionamento_usuario_externo_cadastrar');

	$objReciboPeticionamentoDTO = new ReciboPeticionamentoDTO();
	$objReciboPeticionamentoDTO->retTodos( );
	$objReciboPeticionamentoDTO->retStrNumeroProcessoFormatadoDoc();

    $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
    $strVersaoModuloPeticionamento = $objInfraParametro->getValor('VERSAO_MODULO_PETICIONAMENTO', false);

    if ($strVersaoModuloPeticionamento != '1.1.0') {
        $objReciboPeticionamentoDTO->unRetDblIdProtocoloRelacionado();
    }

	$objReciboPeticionamentoDTO->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
	
	//txtDataInicio
	if( isset( $_POST['txtDataInicio'] ) && $_POST['txtDataInicio'] != ""){
		$objReciboPeticionamentoDTO->setDthInicial( $_POST['txtDataInicio'] );
	}
	
	//txtDataFim
	if( isset( $_POST['txtDataFim'] ) && $_POST['txtDataFim'] != ""){
		$objReciboPeticionamentoDTO->setDthFinal( $_POST['txtDataFim'] );
	}

	if( isset( $_POST['selTipo'] ) && $_POST['selTipo'] != ""){
		$objReciboPeticionamentoDTO->setStrStaTipoPeticionamento( $_POST['selTipo'] );
	}
	
	//$objReciboPeticionamentoDTO->setOrd('DataHoraRecebimentoFinal', InfraDTO::$TIPO_ORDENACAO_DESC );
	$objReciboPeticionamentoDTO->setOrdDthDataHoraRecebimentoFinal(InfraDTO::$TIPO_ORDENACAO_DESC);
	
	$objReciboPeticionamentoRN = new ReciboPeticionamentoRN();
	//PaginaSEIExterna::getInstance()->prepararOrdenacao($objReciboPeticionamentoDTO, 'DataHoraRecebimentoFinal', InfraDTO::$TIPO_ORDENACAO_DESC);
	$arrObjReciboPeticionamentoDTO = $objReciboPeticionamentoRN->listar($objReciboPeticionamentoDTO);
	$numRegistros = count($arrObjReciboPeticionamentoDTO);
	
	if ($numRegistros > 0){
        
		PaginaSEIExterna::getInstance()->prepararPaginacao($objReciboPeticionamentoDTO,1);
		PaginaSEIExterna::getInstance()->processarPaginacao($objReciboPeticionamentoDTO);

		$bolAcaoConsultar = SessaoSEIExterna::getInstance()->verificarPermissao('recibo_peticionamento_usuario_externo_consultar');

		$strResultado = '';
		$strSumarioTabela = 'Tabela de Recibos.';
		$strCaptionTabela = 'Recibos';
		
		$strResultado .= '<table width="99%" class="infraTable" summary="'.$strSumarioTabela.'">'."\n";
		$strResultado .= '<caption class="infraCaption">'.PaginaSEIExterna::getInstance()->gerarCaptionTabela($strCaptionTabela,$numRegistros).'</caption>';
		$strResultado .= '<tr>';
				
		$strResultado .= '<th class="infraTh" width="15%">'.PaginaSEIExterna::getInstance()->getThOrdenacao($objReciboPeticionamentoDTO,'Data e Horário','DataHoraRecebimentoFinal',$arrObjReciboPeticionamentoDTO).'</th>'."\n";
		$strResultado .= '<th class="infraTh" width="20%">'.PaginaSEIExterna::getInstance()->getThOrdenacao($objReciboPeticionamentoDTO,'Número do Processo','NumeroProcessoFormatado',$arrObjReciboPeticionamentoDTO).'</th>'."\n";
		$strResultado .= '<th class="infraTh" width="15%">'.PaginaSEIExterna::getInstance()->getThOrdenacao($objReciboPeticionamentoDTO,'Recibo','NumeroProcessoFormatadoDoc',$arrObjReciboPeticionamentoDTO).'</th>'."\n";
		$strResultado .= '<th class="infraTh" width="30%">'.PaginaSEIExterna::getInstance()->getThOrdenacao($objReciboPeticionamentoDTO,'Tipo de Peticionamento','TipoPeticionamento',$arrObjReciboPeticionamentoDTO).'</th>'."\n";
		$strResultado .= '<th class="infraTh" width="15%">Ações</th>'."\n";
		$strResultado .= '</tr>'."\n";
		$strCssTr='';
		
		$protocoloRN = new ProtocoloRN();
		
		for($i = 0;$i < $numRegistros; $i++){
			
			$protocoloDTO = new ProtocoloDTO();
			$protocoloDTO->retDblIdProtocolo();
			$protocoloDTO->retStrProtocoloFormatado();			
			$protocoloDTO->setDblIdProtocolo( $arrObjReciboPeticionamentoDTO[$i]->getNumIdProtocolo() );			
			$protocoloDTO = $protocoloRN->consultarRN0186( $protocoloDTO );
						
		   	if( isset( $_GET['id_md_pet_rel_recibo_protoc'] ) && $_GET['id_md_pet_rel_recibo_protoc'] == $arrObjReciboPeticionamentoDTO[$i]->getNumIdReciboPeticionamento()){
		    		$strCssTr = '<tr class="infraTrAcessada">';
			}else{
				if( $arrObjReciboPeticionamentoDTO[$i]->getStrSinAtivo()=='S' ){
					$strCssTr = ($strCssTr=='<tr class="infraTrClara">')?'<tr class="infraTrEscura">':'<tr class="infraTrClara">';
				} else {
					$strCssTr ='<tr class="trVermelha">';
				}
			}
			
			$strResultado .= $strCssTr;
			$data = '';
			
			if( $arrObjReciboPeticionamentoDTO[$i] != null && $arrObjReciboPeticionamentoDTO[$i]->getDthDataHoraRecebimentoFinal() != "" ) {
			  $data = $arrObjReciboPeticionamentoDTO[$i]->getDthDataHoraRecebimentoFinal();
			}
			
			$strResultado .= '<td>' . $data .'</td>';
			
			if( $protocoloDTO != null && $protocoloDTO->isSetStrProtocoloFormatado() ){
			  $strResultado .= '<td>'. $protocoloDTO->getStrProtocoloFormatado() .'</td>';
			} else {
			  $strResultado .= '<td></td>';
			}
			
			$strResultado .= '<td>' . $arrObjReciboPeticionamentoDTO[$i]->getStrNumeroProcessoFormatadoDoc() .'</td>';
			
			$strResultado .= '<td>' . $arrObjReciboPeticionamentoDTO[$i]->getStrStaTipoPeticionamentoFormatado() .'</td>';
			
			$strResultado .= '<td align="center">';

			//$strResultado .= PaginaSEIExterna::getInstance()->getAcaoTransportarItem($i,$arrObjReciboPeticionamentoDTO[$i]->getNumIdReciboPeticionamento());
			$intercorrente = $arrObjReciboPeticionamentoDTO[$i]->isSetStrStaTipoPeticionamento() && $arrObjReciboPeticionamentoDTO[$i]->getStrStaTipoPeticionamento() == 'I' ? true : false;

			if($bolAcaoConsultar && $intercorrente)
			 {
				 $acao = $_GET['acao'];
				 $urlLink = 'controlador_externo.php?&acao=recibo_pet_intercorrente_usuario_externo_consultar&acao_origem='. $acao .'&acao_retorno='.$acao.'&id_md_pet_rel_recibo_protoc='. $arrObjReciboPeticionamentoDTO[$i]->getNumIdReciboPeticionamento();
				 $linkAssinado = PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink($urlLink ));
				 $strResultado .= '<a href="'. $linkAssinado . '"><img src="'.PaginaSEIExterna::getInstance()->getDiretorioImagensGlobal().'/consultar.gif" title="Consultar Recibo" alt="Consultar Recibo" class="infraImg" /></a>';
			 }
			else
			 {
				if ($bolAcaoConsultar)
				{
					$acao = $_GET['acao'];
					$urlLink = 'controlador_externo.php?id_md_pet_rel_recibo_protoc='. $arrObjReciboPeticionamentoDTO[$i]->getNumIdReciboPeticionamento() .'&acao=recibo_peticionamento_usuario_externo_consultar&acao_origem='. $acao .'&acao_retorno='.$acao;
					$linkAssinado = PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink($urlLink ));
					$strResultado .= '<a href="'. $linkAssinado . '"><img src="'.PaginaSEIExterna::getInstance()->getDiretorioImagensGlobal().'/consultar.gif" title="Consultar Recibo" alt="Consultar Recibo" class="infraImg" /></a>';
				}
			 }

			$strResultado .= '</td></tr>'."\n";
		}
		
		$strResultado .= '</table>';
	}
	
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

#lblDataInicio {position:absolute;left:0%;top:0%;width:110px;}
#txtDataInicio {position:absolute;left:0%;top:40%;width:100px;}
#imgDtInicio {position:absolute;left:105px;top:40%;}

#lblDataFim {position:absolute;left:140px;top:0%;width:110px;}
#txtDataFim {position:absolute;left:140px;top:40%;width:100px;}
#imgDtFim {position:absolute;left:245px;top:40%;}

#lblTipo {position:absolute;left:280px;top:0%;width:30%;}
#selTipo {position:absolute;left:280px;top:40%;width:20%;}

<?
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
?>

function inicializar(){
  
  if ('<?=$_GET['acao']?>'=='recibo_peticionamento_usuario_externo_selecionar'){
    infraReceberSelecao();
    document.getElementById('btnFecharSelecao').focus();
  }else{
    document.getElementById('btnFechar').focus();
  }
  
  infraEfeitoTabelas();
}

function pesquisar(){   
   document.getElementById('frmLista').submit();   
}

<?
PaginaSEIExterna::getInstance()->fecharJavaScript();
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
$strTipo = $_POST['selTipo'];
?>
<form id="frmLista" method="post" action="<?=PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao='.$_GET['acao']))?>">
    
<? PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos); ?>
  
<div style="height:4.5em; margin-top: 11px;" class="infraAreaDados" id="divInfraAreaDados">
  
<!--  Inicio -->
<label id="lblDataInicio" for="txtDataInicio" class="infraLabelOpcional">Início:</label>
<input type="text" name="txtDataInicio" id="txtDataInicio" maxlength="16" 
       value="<?= PaginaSEIExterna::tratarHTML( $_POST['txtDataInicio'] ) ?>" 
       class="infraText" 
 onchange="validDate('F');" onkeypress="return infraMascara(this, event, '##/##/#### ##:##');" maxlength="16" 
/>

<img src="<?=PaginaSEIExterna::getInstance()->getDiretorioImagensGlobal()?>/calendario.gif" 
         id="imgDtInicio" 
 	     title="Selecionar Data/Hora Inicial" 
 	     alt="Selecionar Data/Hora Inicial" class="infraImg" 
 	     onclick="infraCalendario('txtDataInicio',this,true,'<?=InfraData::getStrDataAtual().' 00:00'?>');" />

<!-- Fim -->
<label id="lblDataFim" for="txtDataFim" class="infraLabelOpcional">Fim:</label>
<input type="text" name="txtDataFim" id="txtDataFim" 
       value="<?= PaginaSEIExterna::tratarHTML( $_POST['txtDataFim'] ) ?>" 
       class="infraText" 
 onchange="validDate('F');" onkeypress="return infraMascara(this, event, '##/##/#### ##:##');" maxlength="16" 
/>

<img src="<?=PaginaSEIExterna::getInstance()->getDiretorioImagensGlobal()?>/calendario.gif" id="imgDtFim" 
         title="Selecionar Data/Hora Final" 
         alt="Selecionar Data/Hora Final" 
         class="infraImg" onclick="infraCalendario('txtDataFim',this,true,'<?=InfraData::getStrDataAtual().' 23:59'?>');" />

<!--  Tipo do Menu -->
<label id="lblTipo" for="selTipo" class="infraLabelOpcional">Tipo de Peticionamento:</label>
<select onchange="pesquisar()" id="selTipo" name="selTipo" class="infraSelect" >
  <option <? if( $_POST['selTipo'] == ""){ ?> selected="selected" <? } ?> value="">Todos</option>
  <option <? if( $_POST['selTipo'] == "N"){ ?> selected="selected" <? } ?> value="<?= ReciboPeticionamentoRN::$TP_RECIBO_NOVO ?>">Processo Novo</option>
  <option <? if( $_POST['selTipo'] == "I"){ ?> selected="selected" <? } ?> value="<?= ReciboPeticionamentoRN::$TP_RECIBO_INTERCORRENTE ?>">Intercorrente</option>
</select> 
  
<input type="submit" style="visibility: hidden;" />

</div>
  
<?  
PaginaSEIExterna::getInstance()->montarAreaTabela($strResultado,$numRegistros);
PaginaSEIExterna::getInstance()->montarBarraComandosInferior($arrComandos);
PaginaSEIExterna::getInstance()->montarAreaDebug();
?>

</form>
<?
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
?>