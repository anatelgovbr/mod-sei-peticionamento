<?php

/**
 * @author Marcelo Emiliano
 * @author Jaqueline Mendes
 * @since  14/03/2017
 */

require_once dirname(__FILE__) . '/../../SEI.php';

session_start();
SessaoSEI::getInstance()->validarLink();
PaginaSEI::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);
ini_set('max_execution_time', '0');
ini_set('memory_limit', '1024M');

//Acao �nica
$acaoPrincipal = 'md_pet_intimacao_eletronica_listar';

//URL Base
$strUrlPadrao = 'controlador.php?acao=' . $acaoPrincipal;


$strTitulo = 'Ver Intima��es Eletr�nicas';

switch ($_GET['acao']) {

    //region Listar
    case $acaoPrincipal:
        break;
    //endregion

    //region Erro
    default:
        throw new InfraException("A��o '" . $_GET['acao'] . "' n�o reconhecida.");
    //endregion
}


//Bot�es de a��o do topo
$arrComandos[] = '<button type="button" accesskey="c" id="btnFecharSelecao" value="Fechar" onclick="fechar();" class="infraButton">
                                    Fe<span class="infraTeclaAtalho">c</span>har
                            </button>';


//Consulta
$idProcedimento = $_GET['id_procedimento'];
$objRN = new MdPetIntRelDestinatarioRN();
$arrDados = $objRN->listarDadosUsuInterno($idProcedimento);
$arrObjIntimacao    = $arrDados[0];
$objMdPetIntDestDTO = $arrDados[1];
$arrDadosAnexo      = $arrDados[2];
$arrIds             = InfraArray::converterArrInfraDTO($arrObjIntimacao, 'IdMdPetIntRelDestinatario');
$arrStrSituacao     = MdPetIntRelDestinatarioINT::getArraySituacaoRelatorio();

//Configura��o da Pagina��o

PaginaSEI::getInstance()->prepararOrdenacao($objMdPetIntDestDTO, 'NomeSerie', InfraDTO::$TIPO_ORDENACAO_ASC);
PaginaSEI::getInstance()->prepararPaginacao($objMdPetIntDestDTO, 200);

PaginaSEI::getInstance()->processarPaginacao($objMdPetIntDestDTO);
$numRegistros = count($arrObjIntimacao);

//Tabela de resultado.
if ($numRegistros > 0) {

    $strResultado .= '<table width="99%" class="infraTable" summary="Servi�os">';
    $strResultado .= '<caption class="infraCaption">';
    $strResultado .= PaginaSEI::getInstance()->gerarCaptionTabela('Intima��es Eletr�nicas', $numRegistros);
    $strResultado .= '</caption>';
    //Cabe�alho da Tabela

    $strResultado .= '<tr style="height: 25px;">';

    //Documento Principal
    $strResultado .= '<th class="infraTh" width="124px">Documento Principal</th>';

    //Anexos
    $strResultado .= '<th class="infraTh" width="50px">Anexos</th>';

    //Tipo de Destinat�rio
    $strResultado .= '<th class="infraTh">Tipo de Destinat�rio</th>';

    //Destinat�rio
    $strResultado .= '<th class="infraTh">Destinat�rio</th>';

    //Nome Tipo de Intima��o
    $strResultado .= '<th class="infraTh" width="20%">Tipo de Intima��o</th>';

    //Data de Cadastro
    $strResultado .= '<th class="infraTh" width="66px">Data de Expedi��o</th>';

    //Situa��o da Intima��o
    $strResultado .= '<th class="infraTh" width="215px">Situa��o da Intima��o</th>';

    $strResultado .= '<th class="infraTh" width="40px">A��es</th>';
    $strResultado .= '</tr>';

    //Linhas
    $strCssTr = '<tr class="infraTrEscura">';

    for ($i = 0; $i < $numRegistros; $i++) {

        //vars
        $strId = $arrObjIntimacao[$i]->getNumIdMdPetIntRelDestinatario();

        $strCssTr = $strCssTr == '<tr class="infraTrClara"' ? '<tr class="infraTrEscura"' : '<tr class="infraTrClara"';
        $strResultado .= $strCssTr.' id="linha_'.$strId.'">';

        //Linha Documento Principal
        $strNomeDocPrincipal = PaginaSEI::tratarHTML($arrObjIntimacao[$i]->getStrNomeSerie());
        if ($arrObjIntimacao[$i]->getStrNumero()){
            $strNomeDocPrincipal .= ' ' . $arrObjIntimacao[$i]->getStrNumero() ;
        }
        $strNomeDocPrincipal .= ' ('.$arrObjIntimacao[$i]->getStrProtocoloFormatadoDocumento().')';
        $strResultado .= '<td>';
        $strResultado .= $strNomeDocPrincipal;
        $strResultado .= '</td>';

        //Linha Anexo
        $strAnexo     =  count($arrDadosAnexo) > 0 && in_array($strId, $arrDadosAnexo) ? MdPetIntRelDestinatarioRN::$SIM_ANEXO : MdPetIntRelDestinatarioRN::$NAO_ANEXO;
        $strResultado .= '<td align="center">';
        $strResultado .= $strAnexo;
        $strResultado .= '</td>';

        //Tipo de Destinat�rio
        $strResultado .= '<td>';
        if($arrObjIntimacao[$i]->getStrSinPessoaJuridica() == "S"){
        $strResultado .= PaginaSEI::tratarHTML("Pessoa Jur�dica");
        }else{
          $strResultado .= PaginaSEI::tratarHTML("Pessoa F�sica");  
        }
        $strResultado .= '</td>';
        
        //Destinat�rio
        $strResultado .= '<td>';
        $strResultado .= PaginaSEI::tratarHTML($arrObjIntimacao[$i]->getStrNomeContato());
        $strResultado .= '</td>';

        //Tipo de Intima��o
        $strResultado .= '<td>';
        $strResultado .= PaginaSEI::tratarHTML($arrObjIntimacao[$i]->getStrNomeTipoIntimacao());
        $strResultado .= '</td>';

        //Data de Cadastro
        $arrDt = explode(' ',$arrObjIntimacao[$i]->getDthDataCadastro());
        $strResultado .= '<td align="center">';
        $strResultado .= $arrDt[0];
        $strResultado .= '</td>';

        //Situa��o da Intima��o
        $strSituacao =   !is_null($arrObjIntimacao[$i]->getStrStaSituacaoIntimacao()) && $arrObjIntimacao[$i]->getStrStaSituacaoIntimacao() != 0 ? $arrStrSituacao[$arrObjIntimacao[$i]->getStrStaSituacaoIntimacao()] : MdPetIntimacaoRN::$STR_SITUACAO_NAO_CADASTRADA;
        $strResultado .= '<td>';
        $strResultado .= PaginaSEI::tratarHTML($strSituacao);
        $strResultado .= '</td>';

        $strResultado .= '<td align="center">';
        //A��o Consulta
        $strUrlConsulta = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_intimacao_consulta&arvore=1&id_documento='.$arrObjIntimacao[$i]->getDblIdDocumento().'&lista_int=1&id_intimacao='.$arrObjIntimacao[$i]->getNumIdMdPetIntimacao().'&id_contato='.$arrObjIntimacao[$i]->getNumIdContato());
        $strResultado .= '<a tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioImagensGlobal() . '/consultar.gif" onclick="abrirModal(\''. $strUrlConsulta .'\', '.$strId.');" title="Consultar Intima��o" alt="Consultar Intima��o" class="infraImg" /></a>&nbsp;';
        $strResultado .= '</td>';
        $strResultado .= '</tr>';

    }
    $strResultado .= '</table>';
}


PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: ' . PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript(); ?>

    function inicializar() {
    if ('<?= $_GET['acao'] ?>' == 'md_pet_intimacao_eletronica_listar') {
    infraReceberSelecao();
    document.getElementById('btnFecharSelecao').focus();
    } else {
    infraEfeitoTabelas();
    }
    }

    function abrirModal(url, idDest)
    {

        removerMarcacoesLinha('infraTrEscura');
        removerMarcacoesLinha('infraTrClara');
        var janela = infraAbrirJanela( url, 'consultarIntimacao', 900, 900, '', false); //modal
        janela.onbeforeunload = function(){
            var idLinha = 'linha_' + idDest;
            document.getElementById(idLinha).className += ' infraTrAcessada';
         }

        return;
    }

    function fechar(){
        window.history.back();
    }

    function removerMarcacoesLinha(nomeClass){
        var objs = document.getElementsByClassName(nomeClass);

        for (var i = 0; i < objs.length; i++) {
            objs[i].className = nomeClass;
        }
    }

<?php PaginaSEI::getInstance()->fecharJavaScript(); ?>


<?php
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
    <form id="frmServicoLista" method="post"
          action="<?= PaginaSEI::getInstance()->formatarXHTML(
              SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'])
          ) ?>">

        <?php PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos); ?>
        <?php PaginaSEI::getInstance()->abrirAreaDados('auto'); ?>

        <?php
        PaginaSEI::getInstance()->montarAreaTabela($strResultado, $numRegistros);
        PaginaSEI::getInstance()->fecharAreaDados();
        PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
        ?>

    </form>

<?php
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();






