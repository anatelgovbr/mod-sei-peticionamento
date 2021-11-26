<?php
/**
 * ANATEL
 *
 * 15/08/2018 - criado por CAST
 *
 */
require_once dirname(__FILE__) . '/../../SEI.php';

SessaoSEI::getInstance()->validarLink();
SessaoSEI::getInstance()->validarPermissao('md_pet_intimacao_cadastrar');
PaginaSEI::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);

$arrComandos = array();
$texto = '';

switch($_GET['acao']) {
	
    case 'md_pet_intimacao_cadastro_confirmar':
        try {
            $strTitulo = 'Gerar Intima��o Eletr�nica - Confirma��o';

            $arrComandos[] = '<button type="button" accesskey="G" name="sbmConfirmarIntimacao" id="sbmConfirmarIntimacao" onclick="gerarIntimacao();" value="Ciente e Gerar Intima��o" class="infraButton">Ciente e <span class="infraTeclaAtalho">G</span>erar Intima��o</button>';
            $arrComandos[] = '<button type="button" accesskey="C" name="sbmFechar" id="sbmFechar" onclick="window.close();" value="Cancelar" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            $objMdPetIntPrazoTacitaDTO = new MdPetIntPrazoTacitaDTO();
            $objMdPetIntPrazoTacitaDTO->setBolExclusaoLogica(false);
            $objMdPetIntPrazoTacitaDTO->retTodos();

            $objMdPetIntPrazoTacitaRN = new MdPetIntPrazoTacitaRN();
            $objMdPetIntPrazoTacitaDTO = $objMdPetIntPrazoTacitaRN->consultar($objMdPetIntPrazoTacitaDTO);
            if (count($objMdPetIntPrazoTacitaDTO)>0) {
            	$numNumPrazo = $objMdPetIntPrazoTacitaDTO->getNumNumPrazo();
            }

            
            //Integral
            $texto  = "<div id=divIntegral style='display:none'>";
            $texto .= "<p style='text-align:justify'> ";
            $texto .= "Ap�s a gera��o da Intima��o Eletr�nica, esta ficar� imediatamente dispon�vel para que o ";
            $texto .= "Destinat�rio realize o seu cumprimento com a consulta ao Documento Principal ou, se indicados, "; 
            $texto .= "a qualquer um dos Protocolos dos Anexos da Intima��o. Caso a consulta n�o seja efetuada em "; 
            $texto .= "at� " . $numNumPrazo . " dias corridos da data de gera��o da Intima��o Eletr�nica, ";
            $texto .= "automaticamente ocorrer� seu Cumprimento por Decurso do Prazo T�cito. ";

            $texto .= "<p style='text-align:justify'> ";
            $texto .= "O Documento Principal e poss�veis Anexos ter�o o acesso ao seu teor protegidos at� o ";
            $texto .= "cumprimento da Intima��o.";

            $texto .= "<p style='text-align:justify'> ";
            $texto .= "<span style='font-weight:bold'>Aten��o: </span> ";
            $texto .= "Toda Intima��o Eletr�nica ocorre por meio da funcionalidade de Disponibiliza��o de ";
            $texto .= "Acesso Externo do SEI. Com o Tipo de Acesso Externo <span style='font-weight:bold'>Integral</span>, TODOS os Protocolos constantes ";
            $texto .= "no processo ser�o disponibilizados ao Destinat�rio, independentemente de seus N�veis de ";
            $texto .= "Acesso, incluindo Protocolos futuros que forem adicionados ao processo.";
            $texto .= "</p> ";

            $texto .= "<p style='text-align:justify'> ";
            $texto .= "Para que n�o ocorra nulidade da Intima��o, o Acesso Externo Integral somente poder� ser ";
            $texto .= "cancelado depois de cumprida a Intima��o e conclu�do o Prazo Externo correspondente (se ";
            $texto .= "indicado para poss�vel Resposta). Caso posteriormente o Acesso Externo Integral utilizado pela ";
            $texto .= "Intima��o Eletr�nica seja cancelado, ele ser� automaticamente substitu�do por um Acesso ";
            $texto .= "Externo Parcial abrangendo o Documento Principal e poss�veis Anexos da Intima��o, al�m de ";
            $texto .= "Documentos peticionados pelo pr�prio Usu�rio Externo.";
            $texto .= "</p> ";
            $texto .= "</div>";
            
			// Parcial
            $texto .= "<div id=divParcial style='display:none'>";
            $texto .= "<p style='text-align:justify'> ";
            $texto .= "Ap�s a gera��o da Intima��o Eletr�nica, esta ficar� imediatamente dispon�vel para que o "; 
            $texto .= "Destinat�rio realize o seu cumprimento com a consulta ao Documento Principal ou, se indicados, "; 
            $texto .= "a qualquer um dos Protocolos dos Anexos da Intima��o. Caso a consulta n�o seja efetuada em "; 
            $texto .= "at� " . $numNumPrazo . " dias corridos da data de gera��o da Intima��o Eletr�nica, ";
            $texto .= "automaticamente ocorrer� seu Cumprimento por Decurso do Prazo T�cito.";
            $texto .= "</p>";

            $texto .= "<p style='text-align:justify'> ";
            $texto .= "O Documento Principal e poss�veis Anexos ter�o o acesso ao seu teor protegidos at� o cumprimento da Intima��o.";
            $texto .= "</p>";

            $texto .= "<p style='text-align:justify'> ";
            $texto .= "<span style='font-weight:bold'>Aten��o: </span> ";
            $texto .= "Toda Intima��o Eletr�nica ocorre por meio da funcionalidade de Disponibiliza��o de ";
            $texto .= "Acesso Externo do SEI. Com o Tipo de Acesso Externo <span style='font-weight:bold'>Parcial</span>, SOMENTE ser�o disponibilizados "; 
            $texto .= "ao Destinat�rio o Documento Principal, os Protocolos dos Anexos da Intima��o (se indicados) e os "; 
            $texto .= "Protocolos adicionados no Acesso Parcial (se indicados). O Documento Principal e Protocolos "; 
            $texto .= "dos Anexos ser�o automaticamente inclu�dos no Acesso Parcial.";
            $texto .= "</p>";

            $texto .= "<p style='text-align:justify'> ";
            $texto .= "Para que n�o ocorra nulidade da Intima��o, o Acesso Externo Parcial n�o poder� ser alterado ";
            $texto .= "nem cancelado. Todos os Protocolos inclu�dos no Acesso Externo Parcial poder�o ser ";
            $texto .= "visualizados pelo Destinat�rio, independentemente de seus N�veis de Acesso, n�o abrangendo "; 
            $texto .= "Protocolos futuros que forem adicionados ao processo.";
            $texto .= "</p>";
            $texto .= "</div>";

        } catch (Exception $e) {
            PaginaSEI::getInstance()->processarExcecao($e);
        }
        
    break;
    default:
        throw new InfraException("A��o '".$_GET['acao']."' n�o reconhecida.");
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();

PaginaSEI::getInstance()->montarTitle(':: '. PaginaSEI::getInstance()->getStrNomeSistema() .' - '.$strTitulo.' ::');

PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
?>
.clear {clear: both;}
<?php
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>
function inicializar(){
	if (window.opener!=null){
		if(window.opener.document.getElementById('optIntegral').checked){
			document.getElementById('divIntegral').style.display='';
		}
		if(window.opener.document.getElementById('optParcial').checked){
			document.getElementById('divParcial').style.display='';
		}
	}
}
function gerarIntimacao(){
	if (window.opener!=null){
		window.opener.document.getElementById('frmMdPetIntimacaoCadastro').submit();
		window.close();		
	}
}
<?php
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
?>
    <div class="clear"></div>
    <div class="textoIntimacaoEletronica">
        <h2>
        <?php echo $texto; ?>
        </h2>
    </div>
    <div style="padding-right: 40%">
<?php 
PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>