<?php
try {
require_once dirname(__FILE__).'/../../SEI.php';
session_start();

PaginaSEI::getInstance()->setTipoPagina( InfraPagina::$TIPO_PAGINA_SIMPLES );
SessaoSEI::getInstance()->validarLink();
$strResultado = '';

$strTitulo = '';

switch($_GET['acao']) {

    case 'md_pet_int_relatorio_ht_listar':
        $strTitulo = "Hist�rico da Intima��o Eletr�nica";

        break;

    default:
        throw new InfraException("A��o '".$_GET['acao']."' n�o reconhecida.");
        break;

        
}

}catch(Exception $e) {
echo '<pre>';
var_dump('oi');
exit;
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
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo);

$arrComandos = array();
$arrComandos[] = '<button tabindex="-1" type="button" accesskey="c" name="btnFechar" value="Fechar" onclick="window.close();" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

$objMdPetIntRelatorioRN  = new MdPetIntRelatorioRN();
$objConsultaDTO = $objMdPetIntRelatorioRN->retornaSelectsRelatorio();


//Consulta
$objConsultaDTO->setNumIdMdPetIntRelDestinatario($_GET['md_pet_int_rel']);
$arrDados = $objMdPetIntRelatorioRN->listarDadosModalSituacao($objConsultaDTO);

//Configura��o da Pagina��o
$numRegistros = count($arrDados);

//Tabela de resultado.
if ($numRegistros > 0) {

    $strResultado .= '<table id="tabelaIntimacaoEletronica"  class="infraTable" summary="Intima��o Eletr�nica">';
    $strResultado .= '<caption class="infraCaption">';
    $pluralOrSing  = $numRegistros == 1 ? 'registro' : 'registros';
    $strResultado .=  'Hist�rico da Intima��o Eletr�nica ('.$numRegistros.' '.$pluralOrSing.'):';
    $strResultado .= '</caption>';

    $strResultado .= '<tr>';
    //Data/Hora que alterou pra essa Situa��o
    $strResultado .= '<th class="infraTh" style="text-align:left;" width="140px;">Data/Hora </th>';

    //Usu�rio  Respons�vel pela A��o
    $strResultado .= '<th class="infraTh" style="text-align:left;" width="auto">Usu�rio </th>';

    //Unidade da Intima��o
    $strResultado .= '<th class="infraTh" style="text-align:left;" width="auto"> Unidade </th>';

    //Destinatario
    $strResultado .= '<th class="infraTh" style="text-align:left;" width="auto"> Destinat�rio </th>';

    //Tipo de Destinatario
    $strResultado .= '<th class="infraTh" style="text-align:left;" width="auto">Tipo de Destinat�rio </th>';

    //Situa��o
    $strResultado .= '<th class="infraTh" style="text-align:left;" width="240px;">Situa��o da Intima��o </th>';

    //Tipo de Resposta
    $strResultado .= '<th class="infraTh" style="text-align:left;" width="auto">Tipo de Resposta</th>';

    $strResultado .= '</tr>';

    #Linhas
    $strCssTr = '<tr class="infraTrEscura">';

    foreach ($arrDados as $key => $dado) {

        //vars
        $strId         = $dado['id'];
        $strCssTr      = $strCssTr == '<tr class="infraTrClara">' ? '<tr class="infraTrEscura">' : '<tr class="infraTrClara">';
        $strResultado .= $strCssTr;

        //Coluna Data/Hora
        $strResultado .= '<td>';
        $strResultado .= PaginaSEI::tratarHTML($dado['data']);
        $strResultado .= '</td>';


        //Href Destinat�rio
        $hrefDest  = '<a class="ancoraSigla" style="font-size: 1.0em;" title="'.PaginaSEI::tratarHTML($dado['usuarioNome']).'" >';
        $hrefDest .=  PaginaSEI::tratarHTML($dado['usuarioEmail']);
        $hrefDest .= '</a>';

        //Destinat�rio
        $strResultado .= '<td>';
        $strResultado .=  $hrefDest;
        $strResultado .= '</td>';


        //Href Unidade
        $hrefUnidade  = '<a class="ancoraSigla" style="font-size: 1.0em;" title="'.$dado['unidadeDescricao'].'" >';
        $hrefUnidade .=  PaginaSEI::tratarHTML($dado['unidadeSigla']);
        $hrefUnidade .= '</a>';

        //Unidade Geradora da Intima��o
        $strResultado .= '<td>';
        $strResultado .=  $hrefUnidade;
        $strResultado .= '</td>';

        //Destinatario - Pessoa
        $strResultado .= '<td>';
        $strResultado .=  PaginaSEI::tratarHTML($dado['usuarioNome']);
        $strResultado .= '</td>';

        //Tipo Destinatario
        $strResultado .= '<td>';
        if($dado['tipoPessoa'] == "S"){
        $strResultado .=  "Pessoa Jur�dica";
        }else{
            $strResultado .=  "Pessoa F�sica";
        }
        $strResultado .= '</td>';

        //Coluna Situa��o da Intima��o
        $strResultado .= '<td>';
        $strResultado .= PaginaSEI::tratarHTML($dado['situacao']);
        $strResultado .= '</td>';

        //Tipo de Resposta
        $strResultado .= '<td>';
        $strResultado .=  PaginaSEI::tratarHTML($dado['tipoResp']);
        $strResultado .= '</td>';

        $strResultado .= '</tr>';

    }
    $strResultado .= '</table>';
}

PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaSEI::getInstance()->abrirAreaDados('auto');

echo $strResultado;

PaginaSEI::getInstance()->fecharAreaDados();
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>
