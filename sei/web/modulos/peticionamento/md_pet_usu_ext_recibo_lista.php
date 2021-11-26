<?
/**
* ANATEL
*
* 28/06/2016 - criado por marcelo.bezerra - CAST
*
*/

try {
	
	require_once dirname(__FILE__).'/../../SEI.php';

	//Classe utilitaria para formata�ao e calculo de Datas
	require_once dirname(__FILE__).'/util/MdPetDataUtils.php';
	
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
			break;

		case 'md_pet_usu_ext_recibo_listar':

			$strTitulo = 'Recibos Eletr�nicos de Protocolo';	
			break;

		default:
			throw new InfraException("A��o '".$_GET['acao']."' n�o reconhecida.");
	}

	$objMdPetCertidaoRN = new MdPetIntCertidaoRN();
	$arrComandos = array();
	$arrComandos[] = '<button type="button" accesskey="p" id="btnPesquisar" value="Pesquisar" onclick="pesquisar();" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';
	$arrComandos[] = '<button type="button" accesskey="c" id="btnFechar" value="Fechar" onclick="location.href=\''.PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=usuario_externo_controle_acessos&acao_origem='.$_GET['acao'])).'\'" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

	$objMdPetReciboDTO = new MdPetReciboDTO();
	$objMdPetReciboDTO->retTodos( );
	$objMdPetReciboDTO->retStrNumeroProcessoFormatadoDoc();

    $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
    $strVersaoModuloPeticionamento = $objInfraParametro->getValor('VERSAO_MODULO_PETICIONAMENTO', false);

    if ($strVersaoModuloPeticionamento != '1.1.0') {
        $objMdPetReciboDTO->unRetDblIdProtocoloRelacionado();
    }

	$objMdPetReciboDTO->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
	
	//txtDataInicio
	if( isset( $_POST['txtDataInicio'] ) && $_POST['txtDataInicio'] != ""){
		$objMdPetReciboDTO->setDthInicial( $_POST['txtDataInicio'] );
	}
	
	//txtDataFim
	if( isset( $_POST['txtDataFim'] ) && $_POST['txtDataFim'] != ""){
		$objMdPetReciboDTO->setDthFinal( $_POST['txtDataFim'] );
	}

	if( isset( $_POST['selTipo'] ) && $_POST['selTipo'] != ""){
		$objMdPetReciboDTO->setStrStaTipoPeticionamento( $_POST['selTipo'] );
	}

	PaginaSEIExterna::getInstance()->prepararOrdenacao($objMdPetReciboDTO, 'DataHoraRecebimentoFinal', InfraDTO::$TIPO_ORDENACAO_DESC);

	PaginaSEIExterna::getInstance()->prepararPaginacao($objMdPetReciboDTO,200);

	$objMdPetReciboRN = new MdPetReciboRN();
	$arrObjMdPetReciboDTO = $objMdPetReciboRN->listar($objMdPetReciboDTO);

	PaginaSEIExterna::getInstance()->processarPaginacao($objMdPetReciboDTO);

	$numRegistros = count($arrObjMdPetReciboDTO);

	if ($numRegistros > 0){
        
		$bolAcaoConsultar = SessaoSEIExterna::getInstance()->verificarPermissao('md_pet_usu_ext_recibo_consultar');

		$strResultado = '';
		$strSumarioTabela = 'Tabela de Recibos.';
		$strCaptionTabela = 'Recibos';
		
		$strResultado .= '<table width="99%" class="infraTable" summary="'.$strSumarioTabela.'">'."\n";
		$strResultado .= '<caption class="infraCaption">'.PaginaSEIExterna::getInstance()->gerarCaptionTabela($strCaptionTabela,$numRegistros).'</caption>';
		$strResultado .= '<tr>';
				
		$strResultado .= '<th class="infraTh" width="15%">'.PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetReciboDTO,'Data e Hor�rio','DataHoraRecebimentoFinal',$arrObjMdPetReciboDTO).'</th>'."\n";
		$strResultado .= '<th class="infraTh" width="20%">'.PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetReciboDTO,'N�mero do Processo','NumeroProcessoFormatado',$arrObjMdPetReciboDTO).'</th>'."\n";
		$strResultado .= '<th class="infraTh" width="15%">'.PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetReciboDTO,'Recibo','NumeroProcessoFormatadoDoc',$arrObjMdPetReciboDTO).'</th>'."\n";
		$strResultado .= '<th class="infraTh" width="30%">'.PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetReciboDTO,'Tipo de Peticionamento','StaTipoPeticionamento',$arrObjMdPetReciboDTO).'</th>'."\n";
		$strResultado .= '<th class="infraTh" width="15%">A��es</th>'."\n";
		$strResultado .= '</tr>'."\n";
		$strCssTr='';
		
		$protocoloRN = new ProtocoloRN();
		
		for($i = 0;$i < $numRegistros; $i++){
			$protocoloDTO = new ProtocoloDTO();
			$protocoloDTO->retDblIdProtocolo();
			$protocoloDTO->retStrProtocoloFormatado();			
			$protocoloDTO->setDblIdProtocolo( $arrObjMdPetReciboDTO[$i]->getNumIdProtocolo() );
			$protocoloDTO = $protocoloRN->consultarRN0186( $protocoloDTO );
						
		   	if( isset( $_GET['id_md_pet_rel_recibo_protoc'] ) && $_GET['id_md_pet_rel_recibo_protoc'] == $arrObjMdPetReciboDTO[$i]->getNumIdReciboPeticionamento()){
		    		$strCssTr = '<tr class="infraTrAcessada">';
			}else{
				if( $arrObjMdPetReciboDTO[$i]->getStrSinAtivo()=='S' ){
					$strCssTr = ($strCssTr=='<tr class="infraTrClara">')?'<tr class="infraTrEscura">':'<tr class="infraTrClara">';
				} else {
					$strCssTr ='<tr class="trVermelha">';
				}
			}
			
			$strResultado .= $strCssTr;
			$data = '';
			
			if( $arrObjMdPetReciboDTO[$i] != null && $arrObjMdPetReciboDTO[$i]->getDthDataHoraRecebimentoFinal() != "" ) {
			  $data = $arrObjMdPetReciboDTO[$i]->getDthDataHoraRecebimentoFinal();
			}
			
			$strResultado .= '<td>' . $data .'</td>';
			
			if( $protocoloDTO != null && $protocoloDTO->isSetStrProtocoloFormatado() ){
			  $strResultado .= '<td>'. $protocoloDTO->getStrProtocoloFormatado() .'</td>';
			} else {
			  $strResultado .= '<td></td>';
			}
			
			$strResultado .= '<td>' . $arrObjMdPetReciboDTO[$i]->getStrNumeroProcessoFormatadoDoc() .'</td>';
			
			$strResultado .= '<td>' . $arrObjMdPetReciboDTO[$i]->getStrStaTipoPeticionamentoFormatado() .'</td>';
			
			$strResultado .= '<td align="center">';

			$intercorrente = $arrObjMdPetReciboDTO[$i]->isSetStrStaTipoPeticionamento() && ( $arrObjMdPetReciboDTO[$i]->getStrStaTipoPeticionamento() == MdPetReciboRN::$TP_RECIBO_INTERCORRENTE ) ? true : false;
			
			$isResposta   = $arrObjMdPetReciboDTO[$i]->isSetStrStaTipoPeticionamento() && ( $arrObjMdPetReciboDTO[$i]->getStrStaTipoPeticionamento() == MdPetReciboRN::$TP_RECIBO_RESPOSTA_INTIMACAO ) ? true : false;

			$isValido     = $objMdPetCertidaoRN->verificaDocumentoEAnexoIntimacaoNaoCumprida(array($arrObjMdPetReciboDTO[$i]->getDblIdDocumento(), false, SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()));

			$iconeConsulta = '<img src="' . PaginaSEIExterna::getInstance()->getDiretorioImagensGlobal() . '/consultar.gif" title="Consultar Recibo" alt="Consultar Recibo" class="infraImg" />';

			if($isValido) {
				$linkAssinado = '';
				if ($bolAcaoConsultar) {
					$objMdPetReciboRN = new MdPetReciboRN();
					$linkAssinado = $objMdPetReciboRN->getUrlRecibo(array($intercorrente, $arrObjMdPetReciboDTO[$i]));
				}

				if ($linkAssinado != '') {
					$strResultado .= '<a target="_blank" href="' . $linkAssinado . '">' . $iconeConsulta . '</a>';
				}
			}else {
				
				$linkAssinado = "javascript:;";
				$msg          = 'Recibo Eletr�nico bloqueado, pois est� vinculado a uma Intima��o ainda n�o Cumprida.';
				$strResultado .= '<a onclick="alert(\''.$msg.'\')" href="' . $linkAssinado . '">'.$iconeConsulta.'</a>';
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
<label id="lblDataInicio" for="txtDataInicio" class="infraLabelOpcional">In�cio:</label>
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
  
  <option <? if( $_POST['selTipo'] == MdPetReciboRN::$TP_RECIBO_NOVO ){ ?> selected="selected" <? } ?> value="<?= MdPetReciboRN::$TP_RECIBO_NOVO ?>">Processo Novo</option>
  
  <option <? if( $_POST['selTipo'] == MdPetReciboRN::$TP_RECIBO_INTERCORRENTE){ ?> selected="selected" <? } ?> value="<?= MdPetReciboRN::$TP_RECIBO_INTERCORRENTE ?>">Intercorrente</option>
  
  <option <? if( $_POST['selTipo'] == MdPetReciboRN::$TP_RECIBO_RESPOSTA_INTIMACAO ){ ?> selected="selected" <? } ?> value="<?= MdPetReciboRN::$TP_RECIBO_RESPOSTA_INTIMACAO?>">Resposta a Intima��o</option>
  
  <option <? if( $_POST['selTipo'] == MdPetReciboRN::$TP_RECIBO_RESPONSAVEL_LEGAL_INICIAL ){ ?> selected="selected" <? } ?> value="<?= MdPetReciboRN::$TP_RECIBO_RESPONSAVEL_LEGAL_INICIAL?>">Respons�vel Legal - Inicial</option>
  
  <option <? if( $_POST['selTipo'] == MdPetReciboRN::$TP_RECIBO_RESPONSAVEL_LEGAL_ALTERACAO ){ ?> selected="selected" <? } ?> value="<?= MdPetReciboRN::$TP_RECIBO_RESPONSAVEL_LEGAL_ALTERACAO?>">Respons�vel Legal - Altera��o</option>
  
  <option <? if( $_POST['selTipo'] == MdPetReciboRN::$TP_RECIBO_ATUALIZACAO_ATOS_CONSTITUTIVOS ){ ?> selected="selected" <? } ?> value="<?= MdPetReciboRN::$TP_RECIBO_ATUALIZACAO_ATOS_CONSTITUTIVOS?>">Atualiza��o de Atos Constitutivos</option>
  
  <option <? if( $_POST['selTipo'] == MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_EMISSAO ){ ?> selected="selected" <? } ?> value="<?= MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_EMISSAO?>">Procura��o Eletr�nica - Emiss�o</option>
  
  <option <? if( $_POST['selTipo'] == MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_RENUNCIA ){ ?> selected="selected" <? } ?> value="<?= MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_RENUNCIA?>">Procura��o Eletr�nica - Ren�ncia</option>
  
  <option <? if( $_POST['selTipo'] == MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_REVOGACAO ){ ?> selected="selected" <? } ?> value="<?= MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_REVOGACAO?>">Procura��o Eletr�nica - Revoga��o</option>
  
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