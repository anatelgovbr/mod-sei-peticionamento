<?php

$objMdPetIntRelatorioRN  = new MdPetIntRelatorioRN();
$objConsultaDTO = $objMdPetIntRelatorioRN->retornaSelectsRelatorio();

PaginaSEI::getInstance()->prepararOrdenacao($objConsultaDTO, 'ProtocoloFormatadoProcedimento', InfraDTO::$TIPO_ORDENACAO_ASC);
PaginaSEI::getInstance()->prepararPaginacao($objConsultaDTO, 200);

//Consulta
$arrObjResultDTO = $objMdPetIntRelatorioRN->listarDados($objConsultaDTO);

PaginaSEI::getInstance()->processarPaginacao($objConsultaDTO);

$numRegistros = count($arrObjResultDTO);
//Configura��o da Pagina��o

//Tabela de resultado.
if ($numRegistros > 0) {

    $strResultado .= '<table id="tabelaIntimacaoEletronica"  width="99%" class="infraTable" summary="Intima��es Eletr�nicas">';
    $strResultado .= '<caption class="infraCaption">';
    $strResultado .= PaginaSEI::getInstance()->gerarCaptionTabela('Intima��es Eletr�nicas', $numRegistros);
    $strResultado .= '</caption>';

    $strResultado .= '<tr>';

    //Processo
    $strResultado .= '<th class="infraTh" width="130px" >' .PaginaSEI::getInstance()->getThOrdenacao($objConsultaDTO, 'Processo', 'ProtocoloFormatadoProcedimento', $arrObjResultDTO). '</th>';

    //Documento Principal
    $strResultado .= '<th class="infraTh" width="auto">' .PaginaSEI::getInstance()->getThOrdenacao($objConsultaDTO, 'Documento Principal', 'DocumentoPrincipal', $arrObjResultDTO). '</th>';

    //Anexos
    $strResultado .= '<th class="infraTh" width="auto">' .PaginaSEI::getInstance()->getThOrdenacao($objConsultaDTO, 'Anexos', 'Anexos', $arrObjResultDTO). '</th>';

    //Destinatario
    $strResultado .= '<th class="infraTh" width="auto">' .PaginaSEI::getInstance()->getThOrdenacao($objConsultaDTO, 'Tipo de Destinat�rio', 'SinPessoaJuridica', $arrObjResultDTO). '</th>';

    //Tipo Destinatario
    $strResultado .= '<th class="infraTh" width="300px;">' .PaginaSEI::getInstance()->getThOrdenacao($objConsultaDTO, 'Destinat�rio', 'NomeContato', $arrObjResultDTO). '</th>';

    //Tipo de Intima��o
    $strResultado .= '<th class="infraTh" width="auto">' .PaginaSEI::getInstance()->getThOrdenacao($objConsultaDTO, 'Tipo de Intima��o', 'NomeTipoIntimacao', $arrObjResultDTO). '</th>';

    //Unidade da Intima��o
    $strResultado .= '<th class="infraTh" width="auto"> '.PaginaSEI::getInstance()->getThOrdenacao($objConsultaDTO, 'Unidade Geradora', 'SiglaUnidadeIntimacao', $arrObjResultDTO).' </th>';

    //Data de Expedicao
    $strResultado .= '<th class="infraTh" width="auto">' .PaginaSEI::getInstance()->getThOrdenacao($objConsultaDTO, 'Data da Gera��o', 'DataCadastro', $arrObjResultDTO). '</th>';

    //Situa��o
    $strResultado .= '<th class="infraTh" width="auto">' .PaginaSEI::getInstance()->getThOrdenacao($objConsultaDTO, 'Situa��o da Intima��o', 'SituacaoIntimacao', $arrObjResultDTO).' </th>';

    //Data do Aceite
    $strResultado .= '<th class="infraTh" width="auto">' .PaginaSEI::getInstance()->getThOrdenacao($objConsultaDTO, 'Data de Cumprimento', 'DataAceite', $arrObjResultDTO). '</th>';

    $strResultado .= '<th class="infraTh" width="auto">A��es</th>';
    $strResultado .= '</tr>';


    #Linhas

    $strCssTr = '<tr class="infraTrEscura">';

    for ($i = 0; $i < $numRegistros; $i++) {

        //vars
        $strId         = $arrObjResultDTO[$i]->getNumIdMdPetIntRelDestinatario();
        $strNome       = $arrObjResultDTO[$i]->getStrProtocoloFormatadoProcedimento();
        $strCssTr      = $strCssTr == '<tr class="infraTrClara">' ? '<tr class="infraTrEscura">' : '<tr class="infraTrClara">';
        $strResultado .= $strCssTr;
        $descricaoProces = $arrObjResultDTO[$i]->getStrEspecificacaoProcedimento();

        //Href Process
        $hrefProcesso  = '<a style="font-size: 1.0em;"';
        $hrefProcesso .= 'class="processoVisualizado" onmouseover ="return infraTooltipMostrar(\''.$descricaoProces.'\',\''.$arrObjResultDTO[$i]->getStrNomeTipoProcedimento().'\')"';
        $hrefProcesso .= 'onmouseout="return infraTooltipOcultar()">';
        $hrefProcesso .=  PaginaSEI::tratarHTML($strNome);
        $hrefProcesso .= '</a>';

        //Linha Processo
        $strResultado .= '<td align="left">';
        $strResultado .= $hrefProcesso;
        $strResultado .= '</td>';

        //Linha Documento Principal
        $strResultado .= '<td>';
        $strResultado .= PaginaSEI::tratarHTML($arrObjResultDTO[$i]->getStrDocumentoPrincipal());
        $strResultado .= '</td>';

        //Linha Anexos
        $strResultado .= '<td align="center">';
        $strResultado .= PaginaSEI::tratarHTML($arrObjResultDTO[$i]->getStrAnexos());;
        $strResultado .= '</td>';


        //Href Destinat�rio
        $hrefDest  = '<a class="ancoraSigla" style="font-size: 1.0em;" title="'.PaginaSEI::tratarHTML($arrObjResultDTO[$i]->getStrNomeContato()).'" >';
        $hrefDest .=  PaginaSEI::tratarHTML($arrObjResultDTO[$i]->getStrNomeContato());
        $hrefDest .= '</a>';

        //Tipo de Destinat�rio
        $strResultado .= '<td align="left">';
        $strResultado .=  PaginaSEI::tratarHTML($arrObjResultDTO[$i]->getStrTipoDestinatario());
        $strResultado .= '</td>';

        //Destinat�rio
        $strResultado .= '<td align="left">';
        $strResultado .=  $hrefDest;
        $strResultado .= '</td>';

        //Tipo de Intimacao
        $strResultado .= '<td>';
        $strResultado .= PaginaSEI::tratarHTML($arrObjResultDTO[$i]->getStrNomeTipoIntimacao());
        $strResultado .= '</td>';

        //Href Unidade
        $hrefUnidade  = '<a class="ancoraSigla" style="font-size: 1.0em;" title="'.$arrObjResultDTO[$i]->getStrDescricaoUnidadeIntimacao().'" >';
        $hrefUnidade .=  PaginaSEI::tratarHTML(PaginaSEI::tratarHTML($arrObjResultDTO[$i]->getStrSiglaUnidadeIntimacao()));
        $hrefUnidade .= '</a>';

        //Unidade Geradora da Intimacao
        $strResultado .= '<td align="center">';
        $strResultado .=  $hrefUnidade;
        $strResultado .= '</td>';

        //Data de Expedicao
        $strResultado .= '<td align="center">';
        $strResultado .= PaginaSEI::tratarHTML($arrObjResultDTO[$i]->getDthDataCadastro());
        $strResultado .= '</td>';

        //Situacao da Intimacao
        $strResultado .= '<td>';
        $strResultado .= PaginaSEI::tratarHTML($arrObjResultDTO[$i]->getStrSituacaoIntimacao());
        $strResultado .= '</td>';

        //Data de Cumprimento
        $strResultado .= '<td align="center">';
        $strResultado .= PaginaSEI::tratarHTML($arrObjResultDTO[$i]->getDthDataAceite());
        $strResultado .= '</td>';

        $linkModal     =  SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_int_relatorio_ht_listar&md_pet_int_rel='.$strId);
        $strResultado .= '<td align="center">';
        $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="abrirModalHistorico(\'' . $linkModal . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="imagens/documentos.gif" title="Hist�rico da Intima��o" alt="Hist�rico da Intima��o" class="infraImg" /></a>&nbsp;';
        $strResultado .= '</td>';
        $strResultado .= '</tr>';

    }
    $strResultado .= '</table>';
}








