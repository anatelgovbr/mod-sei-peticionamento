<?php
/**
 * Created by PhpStorm.
 * User: jhon.carvalho
 * Date: 22/06/2018
 * Time: 09:51
 */

try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();
    SessaoSEI::getInstance()->validarLink();
    //SessaoSEI::getInstance()->validarPermissao($_GET['acao']);
    $arvoreVincListar = false;
    
    $isAdm = false;
    
    switch ($_GET['acao']) {

        case 'md_pet_adm_vinc_listar':
            $strTitulo = 'Administrar Vinculações a Pessoas Jurídicas e Procurações Eletrônicas';
            $strColuna10 = 'CPF/CNPJ do Outorgante';
            $strColuna11 = 'CNPJ';
            $strColuna20 = 'Nome/Razão Social do Outorgante';
            $strColuna21 = 'RazaoSocialNomeVinc';
            $isAdm = true;
            break;

        case 'md_pet_adm_vinc_consultar':
            $strTitulo = 'Vinculações a Pessoas Jurídicas e Procurações Eletrônicas';
            $strColuna10 = 'CPF/CNPJ do Outorgante';
            $strColuna11 = 'CNPJ';
            $strColuna20 = 'Nome/Razão Social do Outorgante';
            $strColuna21 = 'RazaoSocialNomeVinc';
            if(isset($_POST['hdnIdProcedimento'])) {
                $idProcedimento = $_POST['hdnIdProcedimento'];
                $arvoreVincListar = true;
            } else if(isset($_GET['id_procedimento'])) {
                $idProcedimento = $_GET['id_procedimento'];
                $arvoreVincListar = true;
            } else {
                $idProcedimento = $_GET['id_procedimento'];
            }
            break;

    }

    $strColuna30 = 'CPF do Outorgado';
    $strColuna31 = 'CpfProcurador';
    $strColuna40 = 'Nome do Outorgado';
    $strColuna41 = 'NomeProcurador';
    $strColuna50 = 'Tipo de Vínculo';
    $strColuna51 = 'TipoRepresentante';
    $strColuna60 = 'Situação';
    $strColuna61 = 'StaEstado';

    $strCpf='';
    $strRazaoSocial='';
    $strCnpj='';
    $strNome='';
    $strStatus='';
    $strSlTipoViculo='';

    if(!$arvoreVincListar) {
        PaginaSEI::getInstance()->salvarCamposPost(array('txtCnpj', 'txtRazaoSocial', 'txtCpf', 'txtNomeProcurador','selStatus','slTipoViculo'));
        $strLinkConsultaDocumento = SessaoSEI::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinc_doc_procuracao_consultar&id_documento=');
        $strCnpj = trim(PaginaSEI::getInstance()->recuperarCampo('txtCnpj'));
        $strCnpj = InfraUtil::retirarFormatacao($strCnpj);
        if ($strCnpj){
            $intCnpj = intval($strCnpj);
        }

        $strRazaoSocial = trim(PaginaSEI::getInstance()->recuperarCampo('txtRazaoSocial'));

        $strCpf = trim(PaginaSEI::getInstance()->recuperarCampo('txtCpf'));
        $strCpf = InfraUtil::retirarFormatacao($strCpf);
        if ($strCpf){
            $intCpf = intval($strCpf);
        }

        $strNome = trim(PaginaSEI::getInstance()->recuperarCampo('txtNomeProcurador'));
        $strStatus = trim(PaginaSEI::getInstance()->recuperarCampo('selStatus'));
        $strSlTipoViculo = trim(PaginaSEI::getInstance()->recuperarCampo('slTipoViculo'));

        $arrComandos = array();
        $arrComandos[] = '<button type="submit" accesskey="p" id="btnPesquisar" value="Pesquisar" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';
        $arrComandos[] = '<button type="button" accesskey="c" name="btnFechar"  onclick="location.href=\''.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao']).'\'" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
    }

    $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
    $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();

    if ($intCpf > 0) {
        $objMdPetVincRepresentantDTO->setStrCpfProcurador('%'.$intCpf.'%',InfraDTO::$OPER_LIKE);
    }

    if ($strNome != '') {
        $objMdPetVincRepresentantDTO->setStrNomeProcurador('%'.$strNome.'%',InfraDTO::$OPER_LIKE);
    }

    if ($strRazaoSocial != '') {
        $objMdPetVincRepresentantDTO->setStrRazaoSocialNomeVinc('%'.$strRazaoSocial.'%',InfraDTO::$OPER_LIKE);
    }

    if ($intCnpj > 0) {
        if(strlen($intCnpj) <= 11){
            $objMdPetVincRepresentantDTO->setStrCPF('%' . $intCnpj . '%', InfraDTO::$OPER_LIKE);
        } else {
            $objMdPetVincRepresentantDTO->setStrCNPJ('%' . $intCnpj . '%', InfraDTO::$OPER_LIKE);
        }
    }

    if($strStatus != '' && $strStatus != 'null'){
        $objMdPetVincRepresentantDTO->setStrStaEstado($strStatus);
    }
    
    if($strSlTipoViculo != '' && $strSlTipoViculo != 'null'){
        $objMdPetVincRepresentantDTO->setStrTipoRepresentante($strSlTipoViculo);
    }
    
    //Validação para verificar se a unidade atual é compatival com a unidade de configuração do vinculo
    $idUnidadeAtual = SessaoSEI::getInstance()->getNumIdUnidadeAtual();

    $mdPetVincTpProcessoRN = new MdPetVincTpProcessoRN();
    $objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();
    
    $objMdPetVincTpProcessoDTO->retNumIdUnidade();
    $objMdPetVincTpProcessoDTO->setNumIdMdPetVincTpProcesso(MdPetVincTpProcessoRN::$ID_FIXO_MD_PET_VINCULO_USU_EXT);
    $objMdPetVincTpProcessoDTO = $mdPetVincTpProcessoRN->consultar($objMdPetVincTpProcessoDTO);
    $bolAcoes = true;
    if($objMdPetVincTpProcessoDTO) {
        if($objMdPetVincTpProcessoDTO->getNumIdUnidade() != $idUnidadeAtual) {
            $bolAcoes = false;
        }
    }

    $objMdPetVincRepresentantDTO->retTodos(); 
    
    //$objMdPetVincRepresentantDTO->setNumIdContatoOutorg();
    $objMdPetVincRepresentantDTO->retNumIdMdPetVinculo();
    $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
    $objMdPetVincRepresentantDTO->retStrTipoRepresentante();
    $objMdPetVincRepresentantDTO->retStrStaEstado();
    $objMdPetVincRepresentantDTO->retStrCpfProcurador();
    $objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
    $objMdPetVincRepresentantDTO->retStrCNPJ();
    $objMdPetVincRepresentantDTO->retNumIdContatoVinc();
    $objMdPetVincRepresentantDTO->retStrNomeProcurador();
    $objMdPetVincRepresentantDTO->retDblIdProcedimentoVinculo();

    // chamada através do botão do Processo ou do Documento
    if($arvoreVincListar){

        //Recuperar Ids que contem vinculos
        $objMdPetVinculoRN = new MdPetVinculoRN();
        $arrObjMdPetVinculoDTO = $objMdPetVinculoRN->consultarProcedimentoVinculo( array($idProcedimento, 'retornoDTO' => true) );

        // PJ - Representantes
        if (count($arrObjMdPetVinculoDTO)>0){

            $idRepresentantes = InfraArray::converterArrInfraDTO($arrObjMdPetVinculoDTO,'IdMdPetVinculoRepresent');

            if (count($idRepresentantes)>0){
                $objMdPetVincRepresentantDTO->setNumIdMdPetVinculoRepresent($idRepresentantes, InfraDTO::$OPER_IN);
            }else{
                $objMdPetVincRepresentantDTO->setNumIdMdPetVinculoRepresent(0);
            }
        }else{
            $objMdPetVincRepresentantDTO->setNumIdMdPetVinculoRepresent(0);
        }

        PaginaSEI::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);
    }else{

        $idUnidadeAtual = SessaoSEI::getInstance()->getNumIdUnidadeAtual();
        $objMdPetVinculoRN = new MdPetVinculoRN();
        if($_GET['acao'] == 'md_pet_adm_vinc_listar'){
//            $arrIdsProcedimento = $objMdPetVinculoRN->consultarIdProcedimentoByUnidade($idUnidadeAtual);
//            if(count($arrIdsProcedimento)>0) {
//                $objMdPetVincRepresentantDTO->setDblIdProcedimentoVinculo($arrIdsProcedimento, InfraDTO::$OPER_IN);
//            }else{
//                $objMdPetVincRepresentantDTO->setDblIdProcedimentoVinculo(0);
//            } 
        }        
    }

    PaginaSEI::getInstance()->prepararOrdenacao($objMdPetVincRepresentantDTO, 'CpfProcurador', InfraDTO::$TIPO_ORDENACAO_ASC);
    PaginaSEI::getInstance()->prepararPaginacao($objMdPetVincRepresentantDTO);

    $arrObjMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);

    PaginaSEI::getInstance()->processarPaginacao($objMdPetVincRepresentantDTO);

    $strCnpj = trim(PaginaSEI::getInstance()->recuperarCampo('txtCnpj'));
    $strCpf = trim(PaginaSEI::getInstance()->recuperarCampo('txtCpf'));

} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}

// Recuperando os documentos da procuração
foreach ($arrObjMdPetVincRepresentantDTO as $objMdPetVincRepresentantDTO){
    $arrIdMdPetVinculoRepresent[] = $objMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent();
}

if(count($arrIdMdPetVinculoRepresent)>0) {
    $objMdPetVincDocumentoRN = new MdPetVincDocumentoRN();
    $objMdPetVincDocumentoDTO = new MdPetVincDocumentoDTO();
    $objMdPetVincDocumentoDTO->setNumIdMdPetVinculoRepresent($arrIdMdPetVinculoRepresent, InfraDTO::$OPER_IN);
    $objMdPetVincDocumentoDTO->retDblIdDocumento();
    $objMdPetVincDocumentoDTO->retStrProtocoloFormatadoProtocolo();
    $objMdPetVincDocumentoDTO->retNumIdMdPetVinculoRepresent();
    $objMdPetVincDocumentoDTO->adicionarCriterio(array('TipoDocumento','TipoDocumento'),
        array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),
        array('E','P'),
        array(InfraDTO::$OPER_LOGICO_OR));

    $arrObjMdPetVincDocumentoDTO = $objMdPetVincDocumentoRN->listar($objMdPetVincDocumentoDTO);
    $arrDocumento = "";
    foreach ($arrObjMdPetVincDocumentoDTO as $objMdPetVincDocumentoDTO) {
        $arrDocumento[$objMdPetVincDocumentoDTO->getNumIdMdPetVinculoRepresent()] = $objMdPetVincDocumentoDTO;
    }
}
$arrSelectTipoVinculo = array(
    MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL => 'Procurador Especial',
    MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL => 'Responsável Legal',
);
$numRegistros = count($arrObjMdPetVincRepresentantDTO);

if ($numRegistros > 0) {

    $strResultado = '';
    $strSumarioTabela = 'Vinculações a Pessoas Jurídicas e Procurações Eletrônicas';
    $strCaptionTabela = 'Vinculações a Pessoas Jurídicas e Procurações Eletrônicas';
    $strResultado .= '<table width="99%" class="infraTable" summary="' . $strSumarioTabela . '">';
    $strResultado .= '<caption class="infraCaption">' . PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela, $numRegistros) . '</caption>';
    $strResultado .= '<tr>';
    $strResultado .= '<th class="infraTh" style="width:140px">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, $strColuna10, $strColuna11, $arrObjMdPetVincRepresentantDTO) . '</th>';
    $strResultado .= '<th class="infraTh">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, $strColuna20, $strColuna21, $arrObjMdPetVincRepresentantDTO) . '</th>';
    $strResultado .= '<th class="infraTh" style="width:120px">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, $strColuna30, $strColuna31, $arrObjMdPetVincRepresentantDTO) . '</th>';
    $strResultado .= '<th class="infraTh" style="width:150px">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, $strColuna40, $strColuna41, $arrObjMdPetVincRepresentantDTO) . '</th>';
    $strResultado .= '<th class="infraTh" style="width:120px">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, $strColuna50, $strColuna51, $arrObjMdPetVincRepresentantDTO) . '</th>';
    $strResultado .= '<th class="infraTh" style="width:80px">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetVincRepresentantDTO, $strColuna60, $strColuna61, $arrObjMdPetVincRepresentantDTO) . '</th>';
    $strResultado .= '<th class="infraTh" align="center" style="width:75px">Ações</th>';
    $strResultado .= '</tr>';
    //Populando obj para tabela

    for ($i = 0; $i < $numRegistros; $i++) {
        $strTipoRepresentante = MdPetVincRepresentantDTO::getStrNomeTipoRepresentante(
            $arrObjMdPetVincRepresentantDTO[$i]->getStrTipoRepresentante()
        );

        $arrSerieSituacao = MdPetVincRepresentantDTO::getArrSerieSituacao(
             $arrObjMdPetVincRepresentantDTO[$i]->getStrStaEstado()
        );
        $strLabelSituacao = $arrSerieSituacao['strSituacao'];

        //Buscar documento da procuração
        $idVinculacao = $arrObjMdPetVincRepresentantDTO[$i]->getNumIdMdPetVinculo();
        //$idDocumentoFormatado = $arrDocumento[$arrObjMdPetVincRepresentantDTO[$i]->getNumIdMdPetVinculoRepresent()]->getStrProtocoloFormatadoProtocolo();
        
        $idDocumento = $arrDocumento[$arrObjMdPetVincRepresentantDTO[$i]->getNumIdMdPetVinculoRepresent()]->getDblIdDocumento();
        if (!in_array($arrObjMdPetVincRepresentantDTO[$i]->getStrTipoRepresentante(), $arrSelectTipoVinculo)){
            $arrSelectTipoVinculo[$arrObjMdPetVincRepresentantDTO[$i]->getStrTipoRepresentante()] = $arrObjMdPetVincRepresentantDTO[$i]->getStrNomeTipoRepresentante();
        }
        $strResultado .= '<tr class="infraTrClara">';
        //Recuperando Contato do Vinculo
        $objContatoDTO = new ContatoDTO();
        $objContatoDTO->setNumIdContato($arrObjMdPetVincRepresentantDTO[$i]->getNumIdContatoVinc());
        $objContatoDTO->setBolExclusaoLogica(false);
        $objContatoDTO->retDblCnpj();
        $objContatoDTO->retDblCpf();
        $objContatoRN = new ContatoRN();
        $objContatoRN = $objContatoRN->consultarRN0324($objContatoDTO);
        if($objContatoRN->getDblCpf() == null){
        $strResultado .= '<td>'. InfraUtil::formatarCnpj($objContatoRN->getDblCnpj()) . '</td>';
        }else{
        $strResultado .= '<td>'. InfraUtil::formatarCpf($objContatoRN->getDblCpf()) . '</td>';
        }
        $strResultado .= '<td>'. PaginaSEI::tratarHTML($arrObjMdPetVincRepresentantDTO[$i]->getStrRazaoSocialNomeVinc()) . '</td>';
        $strResultado .= '<td>'. InfraUtil::formatarCpf($arrObjMdPetVincRepresentantDTO[$i]->getStrCpfProcurador()) . '</td>';
        $strResultado .= '<td>'. $arrObjMdPetVincRepresentantDTO[$i]->getStrNomeProcurador() .'</td>';
        $strResultado .= '<td>'. $strTipoRepresentante . '</td>';
        $strResultado .= '<td align="center">' . $strLabelSituacao . '</td>';

        if ($arrObjMdPetVincRepresentantDTO[$i]->getStrTipoRepresentante()==MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL){
            $title = 'Consultar Vinculação';
        }else{
            $title = 'Consultar Procuração';
        }
        $acaoConsulta='';
        $strLinkConsultaDocumento = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=documento_visualizar&id_documento=' . $idDocumento . '&arvore=1');
        $iconeConsulta = '<img style="width:16px;"  src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/consultar.gif" title="' . $title . '" alt="' . $title . '" class="infraImg" />';
        $acaoConsulta  = '<a target="_blank" href="'.$strLinkConsultaDocumento.'">'.$iconeConsulta.'</a>';

        $acaoResponsavel = '';
        if ($isAdm){               
            if ($arrObjMdPetVincRepresentantDTO[$i]->getStrTipoRepresentante()==MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL){
                $acaoResponsavel = '';
                if($arrObjMdPetVincRepresentantDTO[$i]->getStrStaEstado() == MdPetVincRepresentantRN::$RP_ATIVO) {
                    $strLinkSuspenderVinc = 'javascript:suspenderRestabelecerPE(\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_vinc_suspender_restabelecer&acao_origem=' . $_GET['acao'] . '&acao_retorno='.$_GET['acao'] . '&idVinculo=' . $idVinculacao . '&operacao=' . MdPetVincRepresentantRN::$RP_SUSPENSO) . '\')';
                    $iconeResponsavel = '<img style="width:16px;" src="modulos/peticionamento/imagens/suspender_responsavel_legal.png" title="Suspender Responsável Legal" alt="Suspender Responsável Legal" class="infraImg" />';
                    
                    $acaoResponsavel  = '<a href="' . $strLinkSuspenderVinc . '">'. $iconeResponsavel . '</a>';
                    if($bolAcoes) {
                        $acaoResponsavel  = '<a href="' . $strLinkSuspenderVinc . '">'. $iconeResponsavel . '</a>';
                    } else {
                        $acaoResponsavel  = '<a onclick="mostrarExcessao();">'. $iconeResponsavel . '</a>';
                    }
                    $strLinkResponsavelVinc = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_vinc_responsavel_cadastrar&acao_origem=' . $_GET['acao'] . '&acao_retorno='.$_GET['acao'] . '&idVinculo=' . $arrObjMdPetVincRepresentantDTO[$i]->getNumIdMdPetVinculo());
                    $iconeResponsavel = '<img style="width:16px;" src="imagens/alterar.gif" title="Alterar o Responsável Legal" alt="Alterar o Responsável Legal" class="infraImg" />';
                    if($bolAcoes) {
                        $acaoResponsavel  .= '<a href="' . $strLinkResponsavelVinc . '">'. $iconeResponsavel . '</a>';
                    } else {
                        $acaoResponsavel  .= '<a onclick="mostrarExcessao();">'. $iconeResponsavel . '</a>';
                    }
                }else if($arrObjMdPetVincRepresentantDTO[$i]->getStrStaEstado() == MdPetVincRepresentantRN::$RP_SUSPENSO) {
                    $strLinkRestabelecerVinc = 'javascript:suspenderRestabelecerPE(\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_vinc_suspender_restabelecer&acao_origem=' . $_GET['acao'] . '&acao_retorno='.$_GET['acao'] . '&idVinculo=' . $idVinculacao . '&operacao=' . MdPetVincRepresentantRN::$RP_ATIVO) . '\')';
                    $iconeResponsavel = '<img style="width:16px;" src="modulos/peticionamento/imagens/retirarSuspensao.png" title="Restabelecer Responsável Legal" alt="Restabelecer Responsável Legal" class="infraImg" />';
                    if($bolAcoes) {
                        $acaoResponsavel  = '<a href="' . $strLinkRestabelecerVinc . '">'. $iconeResponsavel . '</a>';
                    } else {
                        $acaoResponsavel  = '<a onclick="mostrarExcessao();">'. $iconeResponsavel . '</a>';
                    }
                }
            }
        }
        $strResultado .= '<td align="center">' . $acaoConsulta . $acaoResponsavel . '</td>';

        $strResultado .= '</tr>';


    }
    $strResultado .= '</table>';

}

$strSelStatus = MdPetVinculoINT::montarSelectStaEstado('null','&nbsp;',$strStatus);
PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo);
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();

PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
require_once 'md_pet_adm_vinc_lista_js.php';
PaginaSEI::getInstance()->abrirJavaScript();
?>
function desvincularProcuracao(link){
infraAbrirJanela(link,'janelaDesvinculo',700,450,'location=0,status=1,resizable=1,scrollbars=1');
}

function inicializar(){
infraEfeitoTabelas();
}
<?php
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');

?>
<style type="text/css">
#container{
  width: 100%;
}
.clear {
  clear: both;
}

.bloco {
  float: left;
  margin-top: 1%;
  margin-right: 1%;
}

label[for^=txt] {
  display: block;
  white-space: nowrap;
}
label[for^=s] {
  display: block;
  white-space: nowrap;
}

p{
  font-size: 1.2em;
}

#txtCnpj{
  width:140px;
}
</style>

<form id="frmPesquisa" method="post"
      action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">
    <?
    if($arvoreVincListar) {
    PaginaSEI::getInstance()->abrirAreaDados("auto");
    ?>
    <input type="hidden" id="hdnIdProcedimento" name="hdnIdProcedimento" value="<?=$idProcedimento?>">
    <p>A tabela abaixo exibe as Vinculações a Pessoas Jurídicas como Responsável Legal, Procurador Especial e Procurador Simples relacionados aos Interessados do presente processo.</p>
    <?}else {
        PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);

        if ($isAdm){ ?>
        <p>A Vinculação a Pessoa Jurídica como Responsável Legal pode ser Suspensa, a partir de indício de falsidade ou de prática de ato ilícito, devendo indicar o Número SEI de documento com registro do indício e da diligência para sua investigação, afetando o vínculo do Usuário Externo com a Pessoa Jurídica e Procurações Eletrônicas por ele geradas em sua representação. Em situações que o Usuário Externo não consiga realizar diretamente, também é possível Alterar o Responsável Legal.</p>
        <? } else { ?>
        <p>Este relatório permite visualizar as Vinculações a Pessoas Jurídicas como Responsável Legal, Procurador Especial e Procurador Simples concedidas no âmbito do SEI.</p>
        <? } ?>
        <div id="camposForm">

        <div class="bloco" style="min-width:140px; width:10%">
            <label id="lblCnpj" for="txtCnpj" class="infraLabelOpcional"><?= $strColuna10?>:</label>
            <input type="text" id="txtCnpj" name="txtCnpj" class="infraText"
                   value="<?= $strCnpj ?>" maxlength="18"
                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"
                   onkeydown="return mascararCampoCnpjCpf(this);"
            />
        </div>

        <div class="bloco" style="min-width:190px; width:10%">
            <label id="lblRazaoSocial" for="txtRazaoSocial" class="infraLabelOpcional"><?= $strColuna20?>:</label>
            <input type="text" id="txtRazaoSocial" name="txtRazaoSocial" class="infraText" style="width: 190px;"
                   value="<?= PaginaSEI::tratarHTML($strRazaoSocial) ?>" maxlength="100"
                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
        </div>

        <div class="bloco" style="min-width:145px; width:10%">
            <label id="lblCpf" for="txtCpf" class="infraLabelOpcional"><?= $strColuna30?>:</label>
            <input type="text" id="txtCpf" name="txtCpf" class="infraText"
                   value="<?= $strCpf ?>" maxlength="100"
                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"
                   onkeypress="return infraMascaraCPF(this,event,250);"/>
        </div>
        <div class="bloco">
            <label id="lblNomeProcurador" for="txtNomeProcurador" class="infraLabelOpcional"><?= $strColuna40?>:</label>
            <input type="text" id="txtNomeProcurador" name="txtNomeProcurador" class="infraText"
                   value="<?= PaginaSEI::tratarHTML($strNome) ?>" maxlength="100"
                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
        </div>
        <div class="bloco" style="min-width:120px; width:10%">
            <label id="lblTipoVinculo"
                   for="slTipoVinculo"
                   class="infraLabelOpcional">Tipo de Vínculo:</label>
            <select name="slTipoViculo" id="slTipoViculo" style="width:120px;" tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>">
                <option value=""></option>
            <?php if ($arrSelectTipoVinculo) : ?>
            <?php   foreach ($arrSelectTipoVinculo as $chaveTipoVinculo => $itemTipoVinculo) : ?>
                <option value="<?php echo $chaveTipoVinculo; ?>"
                        <?php if($chaveTipoVinculo == $strSlTipoViculo){?>
                        selected="selected"
                        <?php }?>>
                    <?php echo $itemTipoVinculo; ?>
                </option>
            <?php   endforeach; ?>
            <?php endif; ?>
            </select>
       </div>    
        
        <?php //if($_GET['acao'] == 'md_pet_adm_vinc_listar'){ ?>
        <div class="bloco" style="min-width:80px; width:10%">
            <label id="lblStatus" for="selStatus" class="infraLabelOpcional"><?= $strColuna60?></label>
            <select id="selStatus" name="selStatus" onchange="this.form.submit()" class="infraSelect" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>">
                <?=$strSelStatus?>
            </select>
        </div>
        <?php  //}
        PaginaSEI::getInstance()->fecharAreaDados();
    }
    PaginaSEI::getInstance()->montarAreaTabela($strResultado, $numRegistros);
    //PaginaSEI::getInstance()->montarAreaDebug();
    PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
    ?>
</form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>
