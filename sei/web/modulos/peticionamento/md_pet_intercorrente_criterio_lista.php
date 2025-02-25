<?
/**
* ANATEL
*
* 20/10/2016 - criado por marcelo.bezerra - CAST
*
*/

try {
	
	require_once dirname(__FILE__).'/../../SEI.php';
	session_start();
	PaginaSEI::getInstance()->setBolXHTML(false);
	
	//////////////////////////////////////////////////////////////////////////////
	InfraDebug::getInstance()->setBolLigado(false);
	InfraDebug::getInstance()->setBolDebugInfra(false);
	InfraDebug::getInstance()->limpar();
	//////////////////////////////////////////////////////////////////////////////
	
	SessaoSEI::getInstance()->validarLink();
	PaginaSEI::getInstance()->prepararSelecao('criterio_peticionamento_intercorrente_selecionar');
	SessaoSEI::getInstance()->validarPermissao($_GET['acao']);
	
	switch($_GET['acao']){
		case 'md_pet_intercorrente_criterio_excluir':
			try{
				$arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
				$arrObjMdPetCriterioDTO = array();
				for ($i=0;$i<count($arrStrIds);$i++){
					$objMdPetCriterioDTO = new MdPetCriterioDTO();
					$objMdPetCriterioDTO->setNumIdCriterioIntercorrentePeticionamento($arrStrIds[$i]);
					$arrObjMdPetCriterioDTO[] = $objMdPetCriterioDTO;
				}
				$objMdPetCriterioRN = new MdPetCriterioRN();
				$objMdPetCriterioRN->excluir($arrObjMdPetCriterioDTO);
	
			}catch(Exception $e){
				PaginaSEI::getInstance()->processarExcecao($e);
			}
			header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao_origem'].'&acao_origem='.$_GET['acao']));
			die;

		case 'md_pet_intercorrente_criterio_desativar':
			try{
				$arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
				$arrObjMdPetCriterioDTO = array();
				for ($i=0;$i<count($arrStrIds);$i++){
					$objMdPetCriterioDTO = new MdPetCriterioDTO();
					$objMdPetCriterioDTO->setNumIdCriterioIntercorrentePeticionamento($arrStrIds[$i]);
					$objMdPetCriterioDTO->setStrSinAtivo('N');
					$arrObjMdPetCriterioDTO[] = $objMdPetCriterioDTO;
				}
				$objMdPetCriterioRN = new MdPetCriterioRN();
				$objMdPetCriterioRN->desativar($arrObjMdPetCriterioDTO);
			}catch(Exception $e){
				PaginaSEI::getInstance()->processarExcecao($e);
			}
			header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao_origem'].'&acao_origem='.$_GET['acao']));
			die;

		case 'md_pet_intercorrente_criterio_reativar':
	
			$strTitulo = 'Reativar Indisponibilidade Peticionamento';

			if ($_GET['acao_confirmada']=='sim'){

                try{
                    $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
                    $arrObjMdPetCriterioDTO = array();
                    for ($i=0;$i<count($arrStrIds);$i++){
                        $objMdPetCriterioDTO = new MdPetCriterioDTO();
                        $objMdPetCriterioDTO->setNumIdCriterioIntercorrentePeticionamento($arrStrIds[$i]);
                        $objMdPetCriterioDTO->setStrSinAtivo('S');
                        $arrObjMdPetCriterioDTO[] = $objMdPetCriterioDTO;
                    }
                    $objMdPetCriterioRN = new MdPetCriterioRN();
                    $objMdPetCriterioRN->reativar($arrObjMdPetCriterioDTO);
                }catch(Exception $e){
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
	
				$acaoLinhaAmarela = '';
	
				if( $idReativado != 0) {
					$acaoLinhaAmarela = '&id_criterio_intercorrente_peticionamento='. $idReativado.PaginaSEI::getInstance()->montarAncora($idReativado);
				}
	
				header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao_origem'].'&acao_origem='.$_GET['acao'] . $acaoLinhaAmarela));
				die;
			}
			break;

		case 'indisponibilidade_peticionamento_selecionar':
			$strTitulo = PaginaSEI::getInstance()->getTituloSelecao('Selecionar Indisponibilidades','Selecionar Indisponibilidades');
	
			//Se cadastrou alguem
			if ($_GET['acao_origem']=='md_pet_indisponibilidade_cadastrar'){
				if (isset($_GET['id_indisponibilidade_peticionamento'])){
					PaginaSEI::getInstance()->adicionarSelecionado($_GET['id_indisponibilidade_peticionamento']);
				}
			}
			break;

		case 'md_pet_intercorrente_criterio_listar':

			$strTitulo = 'Critérios para Intercorrente';
			break;
	
		default:
			throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
	}

    $arrComandos = array();
    if ($_GET['acao'] == 'criterio_intercorrente_peticionamento_selecionar'){
        $arrComandos[] = '<button type="button" accesskey="t" id="btnTransportarSelecao" value="Transportar" onclick="infraTransportarSelecao();" class="infraButton"><span class="infraTeclaAtalho">T</span>ransportar</button>';
    }

    $objMdPetCriterioDTO = new MdPetCriterioDTO();
    $objMdPetCriterioDTO->setStrSinCriterioPadrao('N');
    $objMdPetCriterioDTO->retTodos(true);

    //NomeProcesso
    $txtTipoProcesso = '';
    if(!(InfraString::isBolVazia($_POST['txtTipoProcesso']))){
        $txtTipoProcesso = $_POST ['txtTipoProcesso'];
        $objMdPetCriterioDTO->setStrNomeProcesso('%'.$_POST ['txtTipoProcesso'] . '%',InfraDTO::$OPER_LIKE);
    }
    $strTipo = '';
    if(!InfraString::isBolVazia($_POST['selTipo'])){
        $strTipo = $_POST['selTipo'];
        list($nivelAcesso, $tipoNivelAcesso) = explode('-',$_POST['selTipo']);
        $objMdPetCriterioDTO->setStrStaNivelAcesso($nivelAcesso);
        if ($tipoNivelAcesso){
        	$objMdPetCriterioDTO->setStrStaTipoNivelAcesso($tipoNivelAcesso);
        }
    }

    PaginaSEI::getInstance()->prepararPaginacao($objMdPetCriterioDTO);
    PaginaSEI::getInstance()->prepararOrdenacao($objMdPetCriterioDTO, 'NomeProcesso', InfraDTO::$TIPO_ORDENACAO_ASC);

    $objMdPetCriterioRN = new MdPetCriterioRN();
    $arrObjMdPetCriterioDTO = $objMdPetCriterioRN->listar($objMdPetCriterioDTO);

    PaginaSEI::getInstance()->processarPaginacao($objMdPetCriterioDTO);

    $numRegistros = count($arrObjMdPetCriterioDTO);

    $strLinkPesquisar = PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] .'&acao_origem='.$_GET['acao'].'&acao_retorno=md_pet_intercorrente_criterio_listar'));
    $arrComandos[] = '<button type="button" accesskey="p" id="btnPesquisar" value="Pesquisar" onclick="pesquisar();" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';
    $arrComandos[] = '<button type="button" accesskey="e" id="btnIntercorrentePadrao" value="IntercorentePadrao" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_intercorrente_criterio_padrao&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'])).'\'" class="infraButton">Int<span class="infraTeclaAtalho">e</span>rcorrente Padr&atilde;o</button>';

    $bolAcaoCadastrar = SessaoSEI::getInstance()->verificarPermissao('md_pet_intercorrente_criterio_cadastrar');
    if ($bolAcaoCadastrar){
        $arrComandos[] = '<button type="button" accesskey="n" id="btnNovo" value="Novo Critério" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_intercorrente_criterio_cadastrar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'])).'\'" class="infraButton"><span class="infraTeclaAtalho">N</span>ovo Critério</button>';
    }

    if( $bolAcaoImprimir ||  $bolAcaoCadastrar) {
        $arrComandos[] = '<button type="button" accesskey="i" id="btnImprimir" value="Imprimir" onclick="infraImprimirTabela();" class="infraButton"><span class="infraTeclaAtalho">I</span>mprimir</button>';
    }

    if ($_GET['acao'] == 'md_pet_intercorrente_criterio_reativar'){
        $arrComandos[] = '<button type="button" accesskey="c" id="btnFecharSelecao" value="Fechar" onclick="window.close();" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
    }else{
        $arrComandos[] = '<button type="button" accesskey="c" name="btnFechar" id="btnFechar" value="Fechar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSei::getInstance()->assinarLink('controlador.php?acao=procedimento_controlar&acao_origem='.$_GET['acao'])).'\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
    }

    if ($numRegistros > 0){
        $bolCheck = false;

        $bolAcaoReativar  = SessaoSEI::getInstance()->verificarPermissao('md_pet_intercorrente_criterio_reativar');
        $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pet_intercorrente_criterio_consultar');
        $bolAcaoAlterar   = SessaoSEI::getInstance()->verificarPermissao('md_pet_intercorrente_criterio_alterar');
        $bolAcaoExcluir   = SessaoSEI::getInstance()->verificarPermissao('md_pet_intercorrente_criterio_excluir');
        $bolAcaoDesativar = true;//SessaoSEI::getInstance()->verificarPermissao('md_pet_intercorrente_criterio_desativar');
        $bolAcaoImprimir  = false;
        $bolCheck         = true;
        if ($_GET['acao']=='criterio_intercorrente_peticionamento_selecionar'){
            $bolAcaoReativar  = false; $bolAcaoExcluir   = false; $bolAcaoDesativar = false;
        }else if ($_GET['acao']=='md_pet_intercorrente_criterio_reativar'){
            $bolAcaoAlterar = false; $bolAcaoImprimir = true; $bolAcaoDesativar = false;
        }else{
            $bolAcaoReativar = false; $bolAcaoImprimir = true;
        }
        if ($bolAcaoDesativar){
            $strLinkDesativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_intercorrente_criterio_desativar&acao_origem='.$_GET['acao']);
        }

        $strLinkReativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_intercorrente_criterio_reativar&acao_origem='.$_GET['acao'].'&acao_confirmada=sim');

        if ($bolAcaoExcluir){
            $strLinkExcluir = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_intercorrente_criterio_excluir&acao_origem='.$_GET['acao']);
        }

        $strResultado = '';

        $strSumarioTabela = 'Lista de Critérios para intercorrente.';
        $strCaptionTabela = 'Critérios para intercorrente Inativos';
        if ($_GET['acao']!='md_pet_intercorrente_criterio_reativar'){
            $strSumarioTabela = 'Lista de Critérios para intercorrente';
            $strCaptionTabela = 'Critérios para intercorrente';
        }

        $strResultado .= '<table width="99%" class="infraTable" summary="'.$strSumarioTabela.'">'."\n";
        $strResultado .= '<caption class="infraCaption">'.PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela,$numRegistros).'</caption>';
        $strResultado .= '<tr>';
        if ($bolCheck) {
            $strResultado .= '<th class="infraTh" width="1%">'.PaginaSEI::getInstance()->getThCheck().'</th>'."\n";
        }

        $strResultado .= '<th class="infraTh text-left">'.PaginaSEI::getInstance()->getThOrdenacao($objMdPetCriterioDTO,'Tipo de Processo','NomeProcesso',$arrObjMdPetCriterioDTO).'</th>'."\n";
        $strResultado .= '<th class="infraTh text-left" width="30%">Nível de Acesso dos Documentos</th>'."\n";
        $strResultado .= '<th class="infraTh" width="15%">Ações</th>'."\n";
        $strResultado .= '</tr>'."\n";
        $strCssTr='';
        for($i = 0;$i < $numRegistros; $i++){
            $strId = $arrObjMdPetCriterioDTO[$i]->getNumIdCriterioIntercorrentePeticionamento();
            $strCssTr ='<tr class="trVermelha">';
            if( $arrObjMdPetCriterioDTO[$i]->getStrSinAtivo() == 'S' ){
                $strCssTr = ($strCssTr=='<tr class="infraTrClara">')?'<tr class="infraTrEscura">':'<tr class="infraTrClara">';
            }

            $strResultado .= $strCssTr;

            if ($bolCheck){
                $strResultado .= '<td valign="middle">'.PaginaSEI::getInstance()->getTrCheck($i,$arrObjMdPetCriterioDTO[$i]->getNumIdCriterioIntercorrentePeticionamento(), $arrObjMdPetCriterioDTO[$i]->getNumIdCriterioIntercorrentePeticionamento()).'</td>';
            }

            $indicacaoInteressado = $arrObjMdPetCriterioDTO[$i]->getNumIdCriterioIntercorrentePeticionamento() === 'S' ? 'Próprio Usuário Externo' : 'Indicação Direta';
            $docExterno          = $arrObjMdPetCriterioDTO[$i]->getNumIdCriterioIntercorrentePeticionamento() === 'S' ? 'Externo' : 'Gerado';
            $strResultado .= '<td valign="middle">'.$arrObjMdPetCriterioDTO[$i]->getStrNomeProcesso().'</td>';

            $strStaNivelAcesso = 'Usuário Externo indicar diretamente';

            if($arrObjMdPetCriterioDTO[$i]->getStrStaNivelAcesso() == 2){
                $strStaNivelAcesso = 'Padrão pré definido';
                $strStaTipoNivelAcesso = ' - Restrito';
                if($arrObjMdPetCriterioDTO[$i]->getStrStaTipoNivelAcesso() == 'P'){
                    $strStaTipoNivelAcesso = ' - Público';
                }
                $strStaNivelAcesso .= $strStaTipoNivelAcesso;
            }
            $strResultado .= '<td valign="middle">'.$strStaNivelAcesso.'</td>';
            $strResultado .= '<td align="center" valign="middle">';

            if ($bolAcaoConsultar){
                $strResultado .= '<a href="'.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_intercorrente_criterio_consultar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'].'&id_criterio_intercorrente_peticionamento='.$arrObjMdPetCriterioDTO[$i]->getNumIdCriterioIntercorrentePeticionamento())).'" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioSvgGlobal().'/consultar.svg?'.Icone::VERSAO.'" title="Consultar Critério Intercorrente" alt="Consultar Critério Intercorrente" class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoAlterar){
                $strResultado .= '<a href="'.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_intercorrente_criterio_alterar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'].'&id_criterio_intercorrente_peticionamento='.$arrObjMdPetCriterioDTO[$i]->getNumIdCriterioIntercorrentePeticionamento())).'" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioSvgGlobal().'/alterar.svg?'.Icone::VERSAO.'" title="Alterar Critério Intercorrente" alt="Alterar Critério Intercorrente" class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoDesativar || $bolAcaoReativar || $bolAcaoExcluir){
                $strDescricao = PaginaSEI::getInstance()->formatarParametrosJavaScript(PaginaSEI::tratarHTML($arrObjMdPetCriterioDTO[$i]->getStrNomeProcesso()));
                if ($bolAcaoDesativar && $arrObjMdPetCriterioDTO[$i]->getStrSinAtivo() == 'S'){
                    $strResultado .= '<a href="'.PaginaSEI::getInstance()->montarAncora($strId).'" onclick="acaoDesativar(\''.$strId.'\',\''.$strDescricao.'\');" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioSvgGlobal().'/desativar.svg?'.Icone::VERSAO.'" title="Desativar Critério Intercorrente" alt="Desativar Critério Intercorrente" class="infraImg" /></a>&nbsp;';
                } else {
                    $strResultado .= '<a href="'.PaginaSEI::getInstance()->montarAncora($strId).'" onclick="acaoReativar(\''.$strId.'\',\''.$strDescricao.'\');" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioSvgGlobal().'/reativar.svg?'.Icone::VERSAO.'" title="Reativar Critério Intercorrente" alt="Reativar Critério Intercorrente" class="infraImg" /></a>&nbsp;';
                }

                if ($bolAcaoExcluir){
                    $strResultado .= '<a href="'.PaginaSEI::getInstance()->montarAncora($strId).'" onclick="acaoExcluir(\''.$strId.'\',\''.$strDescricao.'\');" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioSvgGlobal().'/excluir.svg?'.Icone::VERSAO.'" title="Excluir Critério Intercorrente" alt="Excluir Critério Intercorrente" class="infraImg" /></a>&nbsp;';
                }
            }

            $strResultado .= '</td></tr>'."\n";
        }
        $strResultado .= '</table>';
    }
    $strItensSelIndicacaoInteressado = MdPetTipoProcessoINT::montarSelectIndicacaoInteressadoPeticionamento('','Todos',$_POST['selIndicacaoInteressado']);
    $strItensSelTipoDocumento        = MdPetTipoProcessoINT::montarSelectTipoDocumento('','Todos',$_POST['selDocumentoPrincipal']);
} catch(Exception $e){
	 PaginaSEI::getInstance()->processarExcecao($e);
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: '. PaginaSEI::getInstance()->getStrNomeSistema().' - '.$strTitulo.' ::');
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');

$arrNivelAcesso = array(
    '1-' => 'Usuário Externo indicar diretamente',
    '2-I' => 'Padrão pré definido - Restrito',
    '2-P' => 'Padrão pré definido - Público',
);
?>

<form id="frmLista" method="post" action="<?=PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?id_menu_peticionamento_usuario_externo='. $_GET['id_menu_peticionamento_usuario_externo'] .'&acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">

    <? PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos); ?>
  
    <div style="height:4.5em; margin-top: 11px;" class="infraAreaDados" id="divInfraAreaDados">

        <div class="row">
            <!--  Nome do Menu -->
            <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5">
                <label id="lblTipoProcesso" for="txtTipoProcesso" class="infraLabelOpcional">Tipo de Processo:</label>
                <input type="text" name="txtTipoProcesso" id="txtTipoProcesso"
                       value="<?= PaginaSEI::tratarHTML($txtTipoProcesso) ?>" class="infraText form-control"/>
            </div>
            <!--  Tipo do Menu -->
            <div class="col-sm-10 col-md-8 col-lg-6 col-xl-4">
                <label id="lblTipo" for="selTipo" class="infraLabelOpcional">Nível de Acesso dos Documentos:</label>
                <select onchange="pesquisar()" id="selTipo" name="selTipo" class="infraSelect form-control" >
                    <option value="" <?if( $strTipo == "" ) { echo " selected='selected' "; } ?> > Todos </option>
                    <?php foreach($arrNivelAcesso as $i=>$nivelAcesso):
                        $selected = ($strTipo == $i) ? ' selected="selected" ' : '';
                        ?>
                        <option value="<?= $i;?>" <?=$selected?>><?=$nivelAcesso; ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="submit" style="visibility: hidden;" />
            </div>
        </div>
        <div class="row">
            <!--  Nome do Menu -->
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <?
                PaginaSEI::getInstance()->montarAreaTabela($strResultado,$numRegistros);
                PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
                ?>
            </div>
        </div>
    </div>
    <div class="clear">&nbsp;</div>


</form>

<?php 
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
require_once 'md_pet_intercorrente_criterio_lista_js.php';
?>