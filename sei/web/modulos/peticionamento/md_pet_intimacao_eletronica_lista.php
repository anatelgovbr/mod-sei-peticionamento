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

//Acao única
$acaoPrincipal = 'md_pet_intimacao_eletronica_listar';

//URL Base
$strUrlPadrao = 'controlador.php?acao=' . $acaoPrincipal;

$strTitulo = 'Ver Intimações Eletrônicas';

switch ($_GET['acao']) {

    //region Listar
    case $acaoPrincipal:
        break;
    //endregion

    //region Erro
    default:
        throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    //endregion
}


//Botões de ação do topo
$arrComandos[] = '<button type="submit" accesskey="p" id="btnPesquisar" value="Pesquisar" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';
$arrComandos[] = '<button type="button" accesskey="c" id="btnFecharSelecao" value="Fechar" onclick="fechar();" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';


//Consulta
$idProcedimento = isset($_POST['hdnIdProcedimento']) ? $_POST['hdnIdProcedimento'] : $_GET['id_procedimento'];
$arrDadosAll = (new MdPetIntRelDestinatarioRN())->listarDadosUsuInternoSemFiltro($idProcedimento);
$arrDados = (new MdPetIntRelDestinatarioRN())->listarDadosUsuInterno($idProcedimento);

$arrObjIntimacao    = $arrDados[0];
$objMdPetIntDestDTO = $arrDados[1];
$arrDadosAnexo      = $arrDados[2];
$arrIds             = InfraArray::converterArrInfraDTO($arrObjIntimacao, 'IdMdPetIntRelDestinatario');

$arrStrSituacao     = MdPetIntRelDestinatarioINT::getArraySituacaoRelatorio();

// CAMPOS PARA FILTRAGEM:
// Tipos de Intimação:
$arrObjMdPetIntTipoIntimacaoDTO = new MdPetIntTipoIntimacaoDTO();
$arrObjMdPetIntTipoIntimacaoDTO->retTodos();
$arrObjMdPetIntTipoIntimacaoDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);
$arrObjMdPetIntTipoIntimacaoDTO = (new MdPetIntTipoIntimacaoRN())->listar($arrObjMdPetIntTipoIntimacaoDTO);

$arrTiposIntimacaoCadastrados = [];
foreach($arrDadosAll[0] as $tipoIntimacao){
	array_push($arrTiposIntimacaoCadastrados, $tipoIntimacao->getNumIdMdPetTipoIntimacao() );
}
$arrTiposIntimacaoCadastrados = array_unique($arrTiposIntimacaoCadastrados);

$tipoIntimacaoOptions = '';
if(!empty($arrObjMdPetIntTipoIntimacaoDTO)) {
	foreach($arrObjMdPetIntTipoIntimacaoDTO as $objMdPetIntTipoIntimacaoDTO){
	    if(in_array($objMdPetIntTipoIntimacaoDTO->getNumIdMdPetIntTipoIntimacao(), $arrTiposIntimacaoCadastrados)) {
	        $selected = isset($_POST['selTipoIntimacao']) && $_POST['selTipoIntimacao'] == $objMdPetIntTipoIntimacaoDTO->getNumIdMdPetIntTipoIntimacao() ? 'selected="selected"' : '';
		    $tipoIntimacaoOptions .= '<option value="'.$objMdPetIntTipoIntimacaoDTO->getNumIdMdPetIntTipoIntimacao().'" '.$selected.'>'.$objMdPetIntTipoIntimacaoDTO->getStrNome().'</option>';
        }
	}
}

// Situação da Intimação
$situacaoIntimacaoOptions = '';
$arrStrSituacoesCadastradas = [];
foreach($arrDadosAll[0] as $staSituacao){
	array_push($arrStrSituacoesCadastradas, $staSituacao->getStrStaSituacaoIntimacao() );
}
$arrStrSituacoesCadastradas = array_unique($arrStrSituacoesCadastradas);

if(!empty($arrStrSituacoesCadastradas)) {
	foreach($arrStrSituacoesCadastradas as $strSituacoesCadastradas){
		$selected = isset($_POST['selSituacaoIntimacao']) && $_POST['selSituacaoIntimacao'] == $strSituacoesCadastradas ? 'selected="selected"' : '';
		$situacaoIntimacaoOptions .= '<option value="'.$strSituacoesCadastradas.'" '.$selected.'>'.$arrStrSituacao[$strSituacoesCadastradas].'</option>';
	}
}

//Configuração da Paginação

PaginaSEI::getInstance()->prepararOrdenacao($objMdPetIntDestDTO, 'NomeSerie', InfraDTO::$TIPO_ORDENACAO_ASC);
PaginaSEI::getInstance()->prepararPaginacao($objMdPetIntDestDTO, 200);

PaginaSEI::getInstance()->processarPaginacao($objMdPetIntDestDTO);
$numRegistros = count($arrObjIntimacao);
$strResultado = '';
//Tabela de resultado.
if ($numRegistros > 0) {

    $strResultado .= '<table width="100%" class="infraTable" summary="Serviços">';
    $strResultado .= '<caption class="infraCaption">';
    $strResultado .= PaginaSEI::getInstance()->gerarCaptionTabela('Intimações Eletrônicas', $numRegistros);
    $strResultado .= '</caption>';
    //Cabeçalho da Tabela

    $strResultado .= '<tr style="height: 25px;">';

    //Documento Principal
    $strResultado .= '<th class="infraTh" width="124px">Documento Principal</th>';

    //Anexos
    $strResultado .= '<th class="infraTh" width="50px">Anexos</th>';

    //Tipo de Destinatário
    $strResultado .= '<th class="infraTh">Tipo de Destinatário</th>';

    //Destinatário
    $strResultado .= '<th class="infraTh text-left">Destinatário</th>';

    //Nome Tipo de Intimação
    $strResultado .= '<th class="infraTh" width="20%">Tipo de Intimação</th>';

    //Data de Geração
    $strResultado .= '<th class="infraTh" width="66px">Data da Geração</th>';

    //Situação da Intimação
    $strResultado .= '<th class="infraTh text-left" width="215px">Situação da Intimação</th>';
	
	//Data de Cumprimento
	$strResultado .= '<th class="infraTh text-left" width="66px">Data de Cumprimento</th>';

    $strResultado .= '<th class="infraTh" width="40px">Ações</th>';
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
	
	    $strLinkDocumento = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=documento_visualizar&id_documento='.$arrObjIntimacao[$i]->getDblIdProtocolo());
	    $strNomeDocPrincipal    .= ' (<a href="'.$strLinkDocumento.'" class="ancoraPadraoAzul" target="_blank">'.$arrObjIntimacao[$i]->getStrProtocoloFormatadoDocumento().'</a>)';
	    
        $strResultado .= '<td class="text-center" width="150">';
        $strResultado .= $strNomeDocPrincipal;
        $strResultado .= '</td>';

        //Linha Anexo
        $strAnexo     =  count($arrDadosAnexo) > 0 && in_array($strId, $arrDadosAnexo) ? MdPetIntRelDestinatarioRN::$SIM_ANEXO : MdPetIntRelDestinatarioRN::$NAO_ANEXO;
        $strResultado .= '<td class="text-center" width="90">';
        $strResultado .= $strAnexo;
        $strResultado .= '</td>';

        //Tipo de Destinatário
        $strResultado .= '<td class="text-center" width="170">';
        $strResultado .= PaginaSEI::tratarHTML($arrObjIntimacao[$i]->getStrSinPessoaJuridica() == "S" ? "Pessoa Jurídica" : "Pessoa Física");
        $strResultado .= '</td>';
        
        //Destinatário
        $strResultado .= '<td>';
        $strResultado .= PaginaSEI::tratarHTML($arrObjIntimacao[$i]->getStrNomeContato());
        
        if($arrObjIntimacao[$i]->getStrSinPessoaJuridica() == 'S'){
            if(!empty($arrObjIntimacao[$i]->getStrCnpjContato())){
	            $strResultado .= ' (' . infraUtil::formatarCnpj($arrObjIntimacao[$i]->getStrCnpjContato()).')';
            }
        }else{
	        if(!empty($arrObjIntimacao[$i]->getDblCpfContato())){
		        $strResultado .= ' (' . infraUtil::formatarCpf($arrObjIntimacao[$i]->getDblCpfContato()).')';
	        }else{
		        $strResultado .= ' (Contato não possui CPF)';
            }
        }
	   
	    $strResultado .= '</td>';

        //Tipo de Intimação
        $strResultado .= '<td class="text-center" width="200">';
        $strResultado .= PaginaSEI::tratarHTML($arrObjIntimacao[$i]->getStrNomeTipoIntimacao());
        $strResultado .= '</td>';

        //Data de Cadastro
        $arrDt = explode(' ',$arrObjIntimacao[$i]->getDthDataCadastro());
        $strResultado .= '<td class="text-center" width="120">';
        $strResultado .= $arrDt[0];
        $strResultado .= '</td>';

        //Situação da Intimação
        $strSituacao =   !is_null($arrObjIntimacao[$i]->getStrStaSituacaoIntimacao()) && $arrObjIntimacao[$i]->getStrStaSituacaoIntimacao() != 0 ? $arrStrSituacao[$arrObjIntimacao[$i]->getStrStaSituacaoIntimacao()] : MdPetIntimacaoRN::$STR_SITUACAO_NAO_CADASTRADA;
        $strResultado .= '<td class="text-center" width="170">';
        $strResultado .= PaginaSEI::tratarHTML($strSituacao);
        $strResultado .= '</td>';
	
	    //Data de Cumprimento
	    $dtAceite = !is_null($arrObjIntimacao[$i]->getDthDataAceite()) ? explode(' ', $arrObjIntimacao[$i]->getDthDataAceite())[0] : '';
	    $strResultado .= '<td class="text-center" width="120">';
	    $strResultado .= $dtAceite;
	    $strResultado .= '</td>';

        $strResultado .= '<td align="center">';
        //Ação Consulta
        $strUrlConsulta = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_intimacao_consulta&arvore=1&id_documento='.$arrObjIntimacao[$i]->getDblIdDocumento().'&lista_int=1&id_intimacao='.$arrObjIntimacao[$i]->getNumIdMdPetIntimacao().'&id_contato='.$arrObjIntimacao[$i]->getNumIdContato());
        $strResultado .= '<a tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . '/consultar.svg?'.Icone::VERSAO.'" onclick="abrirModal(\''. $strUrlConsulta .'\', '.$strId.');" title="Consultar Intimação" alt="Consultar Intimação" class="infraImg" /></a>&nbsp;';
        $strResultado .= '</td>';
        $strResultado .= '</tr>';

    }
    $strResultado .= '</table>';
}else{
    $strResultado = '<p class="alert alert-warning text-center my-5">Nenhuma Intimação Eletrônica encontrada neste processo.</p>';
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: ' . PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
require_once('md_pet_intimacao_eletronica_lista_css.php');
PaginaSEI::getInstance()->fecharStyle();
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
        var janela = infraAbrirJanelaModal( url, 900, 900, '', false); //modal
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

    $('body').on('submit', '#frmIntimacoesLista', function(e){

        e.preventDefault(); e.stopPropagation();

        var dataInicialPreenchida = (infraTrim(document.getElementById('txtDataInicio').value)!='') && (infraTrim(document.getElementById('txtDataFim').value)=='');
        var dataFinalPreenchida = (infraTrim(document.getElementById('txtDataInicio').value)=='') && (infraTrim(document.getElementById('txtDataFim').value)!='');
    
        //Validações de Data
        if (dataInicialPreenchida || dataFinalPreenchida){
            alert('O período da geração está incompleto.');
            document.getElementById('txtDataInicio').focus();
            return false;
        }
    
        if (infraTrim(document.getElementById('txtDataInicio').value)!='' && infraTrim(document.getElementById('txtDataFim').value)!='') {
            if (!infraValidarData(document.getElementById('txtDataInicio'))) {
                return false;
            }
        
            if (!infraValidarData(document.getElementById('txtDataFim'))) {
                return false;
            }
        
            if (infraCompararDatas(document.getElementById('txtDataInicio').value, document.getElementById('txtDataFim').value) < 0) {
                alert('Data Final deve ser igual ou superior a Data Inicial.');
                document.getElementById('txtDataInicio').focus();
                return false;
            }
        }
    
        $('#frmIntimacoesLista')[0].submit();

    })

<?php PaginaSEI::getInstance()->fecharJavaScript(); ?>


<?php
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
    <form id="frmIntimacoesLista" method="post" action="<?= PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'])) ?>">
        
        <? PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos); ?>
        
        <div class="row">
            <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3 mb-3">
                <div class="form-group">
                    <label id="lblTermoPesquisa" for="txtTermoPesquisa" class="infraLabelOpcional">Destinatário:</label>
                    <input type="text" id="txtTermoPesquisa" name="txtTermoPesquisa" class="infraText form-control"
                           value="<?= (isset($_POST['txtTermoPesquisa']) && !empty($_POST['txtTermoPesquisa'])) ? $_POST['txtTermoPesquisa'] : '' ?>"
                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"
                           onkeydown="return mascararCampoCnpjCpf(this);" autofocus/>
                </div>
            </div>

            <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3 mb-3">
                <div class="form-group">
                    <label id="lblNaturezaVinculo" for="selNaturezaVinculo" class="infraLabelOpcional">Tipo de Destinatário:</label>
                    <select id="selNaturezaVinculo" name="selNaturezaVinculo" class="infraSelect form-control" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                        <option value="">Todos</option>
                        <option value="F" <?= isset($_POST['selNaturezaVinculo']) && $_POST['selNaturezaVinculo'] == 'F' ? 'selected="selected"' : '' ?> >Pessoa Física</option>
                        <option value="J" <?= isset($_POST['selNaturezaVinculo']) && $_POST['selNaturezaVinculo'] == 'J' ? 'selected="selected"' : '' ?> >Pessoa Jurídica</option>
                    </select>
                </div>
            </div>

            <div class="col-sm-12 col-md-6 col-lg-3 col-xl-3 mb-3">
                <div class="form-group">
                    <label id="lblTipoIntimacao" for="selTipoIntimacao" class="infraLabelOpcional">Tipo de Intimação:</label>
                    <select id="selTipoIntimacao" name="selTipoIntimacao" class="infraSelect form-control" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                        <option value="">Todos</option>
	                    <?= $tipoIntimacaoOptions ?>
                    </select>
                </div>
            </div>

            <div class="col-sm-12 col-md-6 col-lg-4 col-xl-3 mb-3">
                <div class="form-group">
                    <label id="lblSituacaoIntimacao" for="selSituacaoIntimacao" class="infraLabelOpcional">Situação da Intimação:</label>
                    <select id="selSituacaoIntimacao" name="selSituacaoIntimacao" class="infraSelect form-control" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                        <option value="">Todos</option>
	                    <?= $situacaoIntimacaoOptions ?>
                    </select>
                </div>
            </div>
            
            <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4">
                <div class="form-group">
                    <label id="lblPeriodo" class="infraLabelOpcional">Período de Geração:</label>
                    <div class="input-group input-group-sm mb-6 mb-3 d-flex">
                        <div class="input-group-prepend">
                            <span class="input-group-text-modificado">De</span>
                        </div>
                        <input class="infraText form-control" type="text" name="txtDataInicio" id="txtDataInicio"
                               onkeypress="return infraMascaraData(this, event);" maxlength="10"
                               tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"
                               value="<?php echo array_key_exists('txtDataInicio', $_POST) ? PaginaSEI::tratarHTML($_POST['txtDataInicio']) : '' ?>"/>

                        <img class="imgCalendario"
                             src="<?= PaginaSEIExterna::getInstance()->getDiretorioSvgGlobal() ?>/calendario.svg?<?= Icone::VERSAO ?>"
                             id="imgDataInicio"
                             title="Selecionar Data Inicial"
                             alt="Selecionar Data Inicial" class="infraImg"
                             onclick="infraCalendario('txtDataInicio',this,false,'<?= InfraData::getStrDataAtual() ?>');"/>

                        <div class="input-group-prepend ml-2">
                            <span class="input-group-text-modificado">Até</span>
                        </div>

                        <input class="infraText form-control" type="text" id="txtDataFim" name="txtDataFim"
                               value="<?php echo array_key_exists('txtDataFim', $_POST) ? PaginaSEI::tratarHTML($_POST['txtDataFim']) : '' ?>"
                               onkeypress="return infraMascaraData(this, event);" maxlength="10"
                               tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>

                        <img class="imgCalendario"
                             src="<?= PaginaSEIExterna::getInstance()->getDiretorioSvgGlobal() ?>/calendario.svg?<?= Icone::VERSAO ?>"
                             id="imgDataFim"
                             title="Selecionar Data Final"
                             alt="Selecionar Data Final" class="infraImg"
                             onclick="infraCalendario('txtDataFim',this,false,'<?= InfraData::getStrDataAtual() ?>');"/>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" id="hdnIdProcedimento" name="hdnIdProcedimento" value="<?= $idProcedimento ?>">

        <?php

            if($numRegistros > 0){

                PaginaSEI::getInstance()->abrirAreaDados('auto');
                PaginaSEI::getInstance()->montarAreaTabela($strResultado, $numRegistros);
                PaginaSEI::getInstance()->fecharAreaDados();

            }else {

                echo $strResultado;

            }

        ?>

        <? PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos); ?>

    </form>

<?php
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();






