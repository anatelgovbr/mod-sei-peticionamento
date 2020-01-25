<?
/**
* ANATEL
*
* 21/06/2019 - criado por REnato Chaves - CAST
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
	SessaoSEI::getInstance()->validarPermissao($_GET['acao']);
	
	switch($_GET['acao']){
		case 'md_pet_tipo_poder_excluir':
        try {
             //Excluindo Tipo de Poder
            //Recuperando id do registro escolhido
             $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();

             foreach ($arrStrIds as $key => $value) {
              
             $objMdPetTipoPoderLegalDTO = new MdPetTipoPoderLegalDTO();
             $objMdPetTipoPoderLegalDTO->retNumIdTipoPoderLegal();
             $objMdPetTipoPoderLegalDTO->setNumIdTipoPoderLegal($value);
             $objMdPetTipoPoderLegalDTO->setDtaDtaCadastro(InfraData::getStrDataHoraAtual());
             $objMdPetTipoPoderLegalDTO->setStrStaSistema(null);
             $objMdPetTipoPoderLegalRN = new MdPetTipoPoderLegalRN();
             $arrObjMdPetTipoPoderLegalDTO = $objMdPetTipoPoderLegalRN->excluir($objMdPetTipoPoderLegalDTO);
                }

            } catch (Exception $e) {
                PaginaSEI::getInstance()->processarExcecao($e);
            }
            header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao_origem'] . '&acao_origem=' . $_GET['acao']));  

			die;

		case 'md_pet_tipo_poder_desativar':
            //Desativando Tipo de Poder
            
                $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();

                foreach ($arrStrIds as $key => $value) {
                 
                $objMdPetTipoPoderLegalDTO = new MdPetTipoPoderLegalDTO();
                $objMdPetTipoPoderLegalDTO->retNumIdTipoPoderLegal();
                $objMdPetTipoPoderLegalDTO->setNumIdTipoPoderLegal($value);
                $objMdPetTipoPoderLegalDTO->setDtaDtaCadastro(InfraData::getStrDataHoraAtual());
                $objMdPetTipoPoderLegalDTO->setStrStaSistema(null);
                $objMdPetTipoPoderLegalRN = new MdPetTipoPoderLegalRN();
                $arrObjMdPetTipoPoderLegalDTO = $objMdPetTipoPoderLegalRN->desativar($objMdPetTipoPoderLegalDTO);
                header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_tipo_poder_listar&acao_origem=' . $_GET['acao']));
                    }
                
        

			die;

        case 'md_pet_tipo_poder_reativar':
            //Reativando Tipo de Poder
            if($_GET['acao_confirmada'] == "sim"){
                $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();

                foreach ($arrStrIds as $key => $value) {
                 
                $objMdPetTipoPoderLegalDTO = new MdPetTipoPoderLegalDTO();
                $objMdPetTipoPoderLegalDTO->retNumIdTipoPoderLegal();
                $objMdPetTipoPoderLegalDTO->setNumIdTipoPoderLegal($value);
                $objMdPetTipoPoderLegalDTO->setDtaDtaCadastro(InfraData::getStrDataHoraAtual());
                $objMdPetTipoPoderLegalDTO->setStrStaSistema(null);
                $objMdPetTipoPoderLegalRN = new MdPetTipoPoderLegalRN();
                $arrObjMdPetTipoPoderLegalDTO = $objMdPetTipoPoderLegalRN->reativar($objMdPetTipoPoderLegalDTO);
                header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_tipo_poder_listar&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($value)));
                    }
                }
			
			die;
			
		case 'md_pet_tipo_poder_listar':

            $strTitulo = 'Tipos de Poderes Legais';
                
            
            
			break;
	
		default:
			throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
	}

    $arrComandos = array();
    
    $objMdPetTipoPoderLegalDTO = new MdPetTipoPoderLegalDTO();
    $objMdPetTipoPoderLegalDTO->retTodos(true);
    $objMdPetTipoPoderLegalDTO->setOrdNumIdTipoPoderLegal(infraDTO::$TIPO_ORDENACAO_ASC);


    //Campo de Pesquisa
    
    //NomeProcesso
    $txtTipoProcesso = '';
    if(!(InfraString::isBolVazia($_POST['txtTipoPoder']))){
        $txtTipoProcesso = $_POST ['txtTipoProcesso'];
        $objMdPetTipoPoderLegalDTO->setStrNome('%'.$_POST ['txtTipoPoder'] . '%',InfraDTO::$OPER_LIKE);
    }
    

    PaginaSEI::getInstance()->prepararPaginacao($objMdPetTipoPoderLegalDTO);
    PaginaSEI::getInstance()->prepararOrdenacao($objMdPetTipoPoderLegalDTO, 'Nome', InfraDTO::$TIPO_ORDENACAO_ASC);

    $objMdPetTipoPoderLegalRN = new MdPetTipoPoderLegalRN();
    $arrObjMdPetTipoPoderLegalDTO = $objMdPetTipoPoderLegalRN->listar($objMdPetTipoPoderLegalDTO);

    PaginaSEI::getInstance()->processarPaginacao($objMdPetTipoPoderLegalDTO);

    $numRegistros = count($arrObjMdPetTipoPoderLegalDTO);

    $strLinkPesquisar = PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] .'&acao_origem='.$_GET['acao'].'&acao_retorno=md_pet_tipo_poder_listar'));
    $arrComandos[] = '<button type="button" accesskey="p" id="btnPesquisar" value="Pesquisar" onclick="pesquisar();" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';
        //CRUD
        $arrComandos[] = '<button type="button" accesskey="n" id="btnNovo" value="Novo" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_tipo_poder_cadastrar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'])).'\'" class="infraButton"><span class="infraTeclaAtalho">N</span>ovo</button>';
    
        $arrComandos[] = '<button type="button" accesskey="i" id="btnImprimir" value="Imprimir" onclick="infraImprimirTabela();" class="infraButton"><span class="infraTeclaAtalho">I</span>mprimir</button>';
   
        $arrComandos[] = '<button type="button" accesskey="c" name="btnFechar" id="btnFechar" value="Fechar" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSei::getInstance()->assinarLink('controlador.php?acao=procedimento_controlar&acao_origem=' . $_GET['acao'])) . '\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
    

    if ($numRegistros > 0){
       

        
        $bolAcaoDesativar = true;//SessaoSEI::getInstance()->verificarPermissao('md_pet_intercorrente_criterio_desativar');
        $bolAcaoImprimir  = false;
        $bolCheck         = true;
        
        //Links para redirecionamento
        $strLinkDesativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_tipo_poder_desativar&acao_origem='.$_GET['acao']);
        $strLinkReativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_tipo_poder_reativar&acao_origem='.$_GET['acao'].'&acao_confirmada=sim');
        $strLinkExcluir = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_tipo_poder_excluir&acao_origem='.$_GET['acao']);
        
        $strResultado = '';

        $strResultado .= '<table width="99%" class="infraTable" summary="Lista de Tipos de Poderes Legais">'."\n";
        $strResultado .= '<caption class="infraCaption">'.PaginaSEI::getInstance()->gerarCaptionTabela("Tipos de Poderes Legais",$numRegistros).'</caption>';
        $strResultado .= '<tr>';
        $strResultado .= '<th class="infraTh" width="1%">'.PaginaSEI::getInstance()->getThCheck().'</th>'."\n";
        

        $strResultado .= '<th class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objMdPetTipoPoderLegalDTO,'Nome do Tipo de Poder Legal','Nome',$arrObjMdPetTipoPoderLegalDTO).'</th>'."\n";
        $strResultado .= '<th class="infraTh" width="15%">Ações</th>'."\n";
        $strResultado .= '</tr>'."\n";
        $strCssTr='';
        for($i = 0;$i < $numRegistros; $i++){
            $strId = $arrObjMdPetTipoPoderLegalDTO[$i]->getNumIdTipoPoderLegal();
            $strCssTr ='<tr class="trVermelha">';
            if( $arrObjMdPetTipoPoderLegalDTO[$i]->getStrSinAtivo() == 'S' ){
                $strCssTr = ($strCssTr=='<tr class="infraTrClara">')?'<tr class="infraTrEscura">':'<tr class="infraTrClara">';
            } if($arrObjMdPetTipoPoderLegalDTO[$i]->getNumIdTipoPoderLegal() == 1){
                $strCssTr ='<tr style="background-color:#8BB690;">';
            }
            if($_GET['id_md_pet_tipo_poder'] == $arrObjMdPetTipoPoderLegalDTO[$i]->getNumIdTipoPoderLegal() ){
                $strCssTr ='<tr class="infraTrAcessada">';
            }
            

            $strResultado .= $strCssTr;

            if ($bolCheck){
                $strResultado .= '<td valign="middle">'.PaginaSEI::getInstance()->getTrCheck($i,$arrObjMdPetTipoPoderLegalDTO[$i]->getNumIdTipoPoderLegal(), $arrObjMdPetTipoPoderLegalDTO[$i]->getNumIdTipoPoderLegal()).'</td>';
            }

            //Caso o Id do registro seja igual á 1, destacar em verde e sumir com os icones.
            //if($arrObjMdPetTipoPoderLegalDTO[$i]->getNumIdTipoPoderLegal() == 1 ){
            $strResultado .= '<td valign="middle">'.$arrObjMdPetTipoPoderLegalDTO[$i]->getStrNome().'</td>';
            //}
        
            $strResultado .= '<td align="center" valign="middle">';
            if($arrObjMdPetTipoPoderLegalDTO[$i]->getNumIdTipoPoderLegal() == 1 ){
                $strResultado .= "<img src='/infra_css/imagens/ajuda.gif' onmouseover='return infraTooltipMostrar(\"O Tipo de Poder Legal Recebimento e Cumprimento de Intimação Eletrônica não pode ser editado, desativado ou excluído em razão de sua dependência em outros recursos do sistema.\",\"\");' onmouseout='return infraTooltipOcultar();'/>&nbsp;";

            }
            $strResultado .= '<a href="'.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_tipo_poder_consultar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'].'&IdTipoPoderLegal='.$arrObjMdPetTipoPoderLegalDTO[$i]->getNumIdTipoPoderLegal())).'" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/consultar.gif" title="Consultar Tipo de Poder" alt="Consultar Tipo de Poder" class="infraImg" /></a>&nbsp;';
            if($arrObjMdPetTipoPoderLegalDTO[$i]->getNumIdTipoPoderLegal() != 1 ){
            $strResultado .= '<a href="'.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_tipo_poder_alterar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'].'&IdTipoPoderLegal='.$arrObjMdPetTipoPoderLegalDTO[$i]->getNumIdTipoPoderLegal())).'" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/alterar.gif" title="Alterar Tipo de Poder" alt="Alterar Tipo de Poder" class="infraImg" /></a>&nbsp;';
            }
                $strDescricao = PaginaSEI::getInstance()->formatarParametrosJavaScript(PaginaSEI::tratarHTML($arrObjMdPetTipoPoderLegalDTO[$i]->getStrNome()));
                if($arrObjMdPetTipoPoderLegalDTO[$i]->getNumIdTipoPoderLegal() != 1 ){
                if ($bolAcaoDesativar && $arrObjMdPetTipoPoderLegalDTO[$i]->getStrSinAtivo() == 'S'){
                    $strResultado .= '<a href="'.PaginaSEI::getInstance()->montarAncora($strId).'" onclick="acaoDesativar(\''.$strId.'\',\''.$strDescricao.'\');" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/desativar.gif" title="Desativar Tipo de Poder" alt="Desativar Tipo de Poder" class="infraImg" /></a>&nbsp;';
                } else {
                    $strResultado .= '<a href="'.PaginaSEI::getInstance()->montarAncora($strId).'" onclick="acaoReativar(\''.$strId.'\',\''.$strDescricao.'\');" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/reativar.gif" title="Reativar Tipo de Poder" alt="Reativar Tipo de Poder" class="infraImg" /></a>&nbsp;';
                }
                    $strResultado .= '<a href="'.PaginaSEI::getInstance()->montarAncora($strId).'" onclick="acaoExcluir(\''.$strId.'\',\''.$strDescricao.'\');" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/excluir.gif" title="Excluir Tipo de Poder" alt="Excluir Tipo de Poder" class="infraImg" /></a>&nbsp;';
            }
            $strResultado .= '</td></tr>'."\n";
        }
        $strResultado .= '</table>';
    }
    
} catch(Exception $e){
	 PaginaSEI::getInstance()->processarExcecao($e);
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: '. PaginaSEI::getInstance()->getStrNomeSistema().' - '.$strTitulo.' ::');
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();


?>

    function inicializar(){
    if ('<?=$_GET['acao']?>'=='md_pet_tipo_poder_listar'){
    infraReceberSelecao();
    
    }else{
   
    }
    infraEfeitoTabelas();
    }


    function acaoDesativar(id,desc){
    if (confirm("Confirma desativação do Tipo de Poder Legal \""+desc+"\"?")){
    document.getElementById('hdnInfraItemId').value=id;
    document.getElementById('frmLista').action='<?=$strLinkDesativar?>';
    document.getElementById('frmLista').submit();
    }
    }

   


    function acaoReativar(id,desc){
    if (confirm("Confirma reativação do Tipo de Poder Legal \""+desc+"\"?")){
    document.getElementById('hdnInfraItemId').value=id;
    document.getElementById('frmLista').action='<?=$strLinkReativar?>';
    document.getElementById('frmLista').submit();
    }
    }

    
    function acaoExcluir(id,desc){
    if (confirm("Confirma exclusão do Tipo de Poder Legal \""+desc+"\"?")){
    document.getElementById('hdnInfraItemId').value=id;
    document.getElementById('frmLista').action='<?=$strLinkExcluir?>';
    document.getElementById('frmLista').submit();
    }
    }


    
    function pesquisar(){
    document.getElementById('frmLista').action='<?=$strLinkPesquisar?>';
    document.getElementById('frmLista').submit();
    }
<?


PaginaSEI::getInstance()->fecharJavaScript();
?>

<style type="text/css">

#lblTipoPoder {position:absolute;left:0%;top:0%;width:20%;}
#txtTipoPoder {position:absolute;left:0%;top:40%;width:20%;}

#lblTipo {position:absolute;left:23%;top:0%;width:20%;}
#selTipo {position:absolute;left:23%;top:40%;width:20%;}

</style>

<?
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
        <!--  Nome do Menu -->
        <label id="lblTipoProcesso" for="txtTipoPoder" class="infraLabelOpcional">Nome do Tipo de Poder Legal:</label>
        <input type="text" name="txtTipoPoder" id="txtTipoPoder" class="infraText" />
    </div>
    <?
    PaginaSEI::getInstance()->montarAreaTabela($strResultado,$numRegistros);
    PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
    ?>

</form>

<?php 
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>