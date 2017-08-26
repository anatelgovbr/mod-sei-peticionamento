<?
/**
* ANATEL
*
* 16/02/2016 - criado por jaqueline.mendes@cast.com.br - CAST
*
*/


try {
  require_once dirname(__FILE__).'/../../SEI.php';

  session_start();

  SessaoSEI::getInstance()->validarLink();

  PaginaSEI::getInstance()->prepararSelecao('tipo_processo_peticionamento_selecionar');

  SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

  switch($_GET['acao']){
    case 'md_pet_tipo_processo_excluir':
      try{
        $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
        $arrObjMdPetTipoProcessoDTO = array();
     //   $arrobjMdPetRelTpProcessoUnidDTO = array();
        
        // varrendo Tipos de Processos para Peticionamento selecionados
        for ($i=0;$i<count($arrStrIds);$i++){
          $objMdPetTipoProcessoDTO = new MdPetTipoProcessoDTO();
          $objMdPetTipoProcessoDTO->setNumIdTipoProcessoPeticionamento($arrStrIds[$i]);
          $arrObjMdPetTipoProcessoDTO[] = $objMdPetTipoProcessoDTO;
        }

        
        //Tipos de Processos para Peticionamento
        $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
        $objMdPetTipoProcessoRN->excluir($arrObjMdPetTipoProcessoDTO);
        PaginaSEI::getInstance()->adicionarMensagem('Opera��o realizada com sucesso.');
        
      }catch(Exception $e){
        PaginaSEI::getInstance()->processarExcecao($e);
      } 
      header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao_origem'].'&acao_origem='.$_GET['acao']));
      die;

    case 'md_pet_tipo_processo_desativar':
      try{
        $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
        $arrObjMdPetTipoProcessoDTO = array();
        for ($i=0;$i<count($arrStrIds);$i++){
          $objMdPetTipoProcessoDTO = new MdPetTipoProcessoDTO();
          $objMdPetTipoProcessoDTO->setNumIdTipoProcessoPeticionamento($arrStrIds[$i]);
          $arrObjMdPetTipoProcessoDTO[] = $objMdPetTipoProcessoDTO;
        }
        $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
        
        $objMdPetTipoProcessoRN->desativar($arrObjMdPetTipoProcessoDTO);
      }catch(Exception $e){
        PaginaSEI::getInstance()->processarExcecao($e);
      } 
      header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao_origem'].'&acao_origem='.$_GET['acao']));
      die;

    case 'md_pet_tipo_processo_reativar':
            
      $strTitulo = 'Reativar Tipo de Processo';

      if ($_GET['acao_confirmada']=='sim'){
        
        try{
          $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
          $arrObjMdPetTipoProcessoDTO = array();
          $idReativado = 0;
          for ($i=0;$i<count($arrStrIds);$i++){
            $objMdPetTipoProcessoDTO = new MdPetTipoProcessoDTO();
            $objMdPetTipoProcessoDTO->setNumIdTipoProcessoPeticionamento($arrStrIds[$i]);
            $idReativado = $arrStrIds[$i];
            $arrObjMdPetTipoProcessoDTO[] = $objMdPetTipoProcessoDTO;
          }
          $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
          $objMdPetTipoProcessoRN->reativar($arrObjMdPetTipoProcessoDTO);
          PaginaSEI::getInstance()->adicionarMensagem('Opera��o realizada com sucesso.');
        }catch(Exception $e){
          PaginaSEI::getInstance()->processarExcecao($e);
        } 
        
        $acaoLinhaAmarela = '';
        
        if( $idReativado != 0) {
        	$acaoLinhaAmarela = '&id_tipo_processo_peticionamento='. $idReativado.PaginaSEI::getInstance()->montarAncora($idReativado);
        }
        
        header('Location: '.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao_origem'].'&acao_origem='.$_GET['acao'] . $acaoLinhaAmarela ));
        die;
      } 
      break;

    case 'tipo_processo_peticionamento_selecionar':
      $strTitulo = PaginaSEI::getInstance()->getTituloSelecao('Selecionar Tipo de Processo','Selecionar Tipo de Processo');

      //Se cadastrou alguem
      if ($_GET['acao_origem']=='md_pet_tipo_processo_cadastrar'){
        if (isset($_GET['id_tipo_processo_peticionamento'])){
          PaginaSEI::getInstance()->adicionarSelecionado($_GET['id_tipo_processo_peticionamento']);
        }
      }
      break;

    case 'md_pet_tipo_processo_listar':
        
      $strTitulo = 'Tipos de Processos para Peticionamento';
      break;

    default:
      throw new InfraException("A��o '".$_GET['acao']."' n�o reconhecida.");
  }

  $arrComandos = array();
  if ($_GET['acao'] == 'tipo_processo_peticionamento_selecionar'){
    $arrComandos[] = '<button type="button" accesskey="t" id="btnTransportarSelecao" value="Transportar" onclick="infraTransportarSelecao();" class="infraButton"><span class="infraTeclaAtalho">T</span>ransportar</button>';
  }
    
  $objMdPetTipoProcessoDTO = new MdPetTipoProcessoDTO();
  $objMdPetTipoProcessoDTO->retTodos();
  $objMdPetTipoProcessoDTO->retStrNomeProcesso();
    
  //NomeProcesso
  if(!(InfraString::isBolVazia($_POST['txtTipoProcesso']))){
  	$objMdPetTipoProcessoDTO->setStrNomeProcesso('%'.$_POST ['txtTipoProcesso'] . '%',InfraDTO::$OPER_LIKE);
  }
  
  //Indica��o Interessado
  if(($_POST['selIndicacaoInteressado'] != '') && $_POST['selIndicacaoInteressado'] != 'null'){
  $vlIndicacaoDireta  = $_POST['selIndicacaoInteressado'] == MdPetTipoProcessoRN::$INDICACAO_DIRETA ? 'S' :   'N';
  $vlIndicacaoPrUExt  = $_POST['selIndicacaoInteressado'] == MdPetTipoProcessoRN::$PROPRIO_USUARIO_EXTERNO ? 'S' :   'N';
  
   $objMdPetTipoProcessoDTO->setStrSinIIProprioUsuarioExterno($vlIndicacaoPrUExt);
   $objMdPetTipoProcessoDTO->setStrSinIIIndicacaoDireta($vlIndicacaoDireta);
   
  }
  
  //Documento Principal
  if(($_POST['selDocumentoPrincipal'] != '') && $_POST['selDocumentoPrincipal'] != 'null'){
  $vlIndicacaoDocGerado  = $_POST['selDocumentoPrincipal'] == MdPetTipoProcessoRN::$DOC_GERADO ? 'S' : 'N';
  $vlIndicacaoDocExterno = $_POST['selDocumentoPrincipal'] == MdPetTipoProcessoRN::$DOC_EXTERNO ? 'S' : 'N';
  
   $objMdPetTipoProcessoDTO->setStrSinDocGerado($vlIndicacaoDocGerado);
   $objMdPetTipoProcessoDTO->setStrSinDocExterno($vlIndicacaoDocExterno);
   
  }

  PaginaSEI::getInstance()->prepararOrdenacao($objMdPetTipoProcessoDTO, 'NomeProcesso', InfraDTO::$TIPO_ORDENACAO_ASC);
  PaginaSEI::getInstance()->prepararPaginacao($objMdPetTipoProcessoDTO, 200);
  
  
  $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
  
  $arrObjMdPetTipoProcessoDTO = $objMdPetTipoProcessoRN->listar($objMdPetTipoProcessoDTO);
  
  PaginaSEI::getInstance()->processarPaginacao($objMdPetTipoProcessoDTO);

  $numRegistros = count($arrObjMdPetTipoProcessoDTO);

  
  $strLinkPesquisar = PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] .'&acao_origem='.$_GET['acao'].'&acao_retorno=md_pet_tipo_processo_listar'));
  $arrComandos[] = '<button type="button" accesskey="p" id="btnPesquisar" value="Pesquisar" onclick="pesquisar();" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';
  
  $arrComandos[] = '<button type="button" accesskey="o" id="btnOrientacoesGerais" value="Orienta��es Gerais" class="infraButton" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_tipo_processo_cadastrar_orientacoes&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'])).'\'"><span class="infraTeclaAtalho">O</span>rienta��es Gerais</button>';
  
  $bolAcaoCadastrar = SessaoSEI::getInstance()->verificarPermissao('md_pet_tipo_processo_cadastrar');
  if ($bolAcaoCadastrar){
  	$arrComandos[] = '<button type="button" accesskey="n" id="btnNovo" value="Novo" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_tipo_processo_cadastrar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'])).'\'" class="infraButton"><span class="infraTeclaAtalho">N</span>ovo</button>';
  }
  
  if ($numRegistros > 0){
  	
    $bolCheck = false;

    if ($_GET['acao']=='tipo_processo_peticionamento_selecionar'){
      $bolAcaoReativar = false;
      $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pet_tipo_processo_consultar');
      $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('md_pet_tipo_processo_alterar');
      $bolAcaoImprimir = false;
      $bolAcaoExcluir = false;
      $bolAcaoDesativar = false;
      $bolCheck = true;
     }else if ($_GET['acao']=='md_pet_tipo_processo_reativar'){
      $bolAcaoReativar = SessaoSEI::getInstance()->verificarPermissao('md_pet_tipo_processo_reativar');
      $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pet_tipo_processo_consultar');
      $bolAcaoAlterar = false;
      $bolAcaoImprimir = true;
      $bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('md_pet_tipo_processo_excluir');
      $bolAcaoDesativar = false;
     }else{
      $bolAcaoReativar = false;
      $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pet_tipo_processo_consultar');
      $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('md_pet_tipo_processo_alterar');
      $bolAcaoImprimir = true;
      $bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('md_pet_tipo_processo_excluir');
      $bolAcaoDesativar = SessaoSEI::getInstance()->verificarPermissao('md_pet_tipo_processo_desativar');
    }

    if ($bolAcaoDesativar){
      $bolCheck = true;
     //$arrComandos[] = '<button type="button" accesskey="t" id="btnDesativar" value="Desativar" onclick="acaoDesativacaoMultipla();" class="infraButton">Desa<span class="infraTeclaAtalho">t</span>ivar</button>';
      $strLinkDesativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_tipo_processo_desativar&acao_origem='.$_GET['acao']);
    }

     $strLinkReativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_tipo_processo_reativar&acao_origem='.$_GET['acao'].'&acao_confirmada=sim');

    if ($bolAcaoExcluir){
      $bolCheck = true;
      //$arrComandos[] = '<button type="button" accesskey="E" id="btnExcluir" value="Excluir" onclick="acaoExclusaoMultipla();" class="infraButton"><span class="infraTeclaAtalho">E</span>xcluir</button>';
      $strLinkExcluir = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_tipo_processo_excluir&acao_origem='.$_GET['acao']);
    }

    $strResultado = '';

    if ($_GET['acao']!='md_pet_tipo_processo_reativar'){ 
      $strSumarioTabela = 'Tabela de Tipo de Processos para Peticionamento';
      $strCaptionTabela = 'Tipos de Processos para Peticionamento';
    }else{
      $strSumarioTabela = 'Tabela de Tipo de Processos Inativos.';
      $strCaptionTabela = 'Tipos de Processos Inativos';
    }

    $strResultado .= '<table width="99%" class="infraTable" summary="'.$strSumarioTabela.'">'."\n";
    $strResultado .= '<caption class="infraCaption">'.PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela,$numRegistros).'</caption>';
    $strResultado .= '<tr>';
    if ($bolCheck) {
      $strResultado .= '<th class="infraTh" width="1%">'.PaginaSEI::getInstance()->getThCheck().'</th>'."\n";
    }
    
    $strResultado .= '<th class="infraTh" width="30%">'.PaginaSEI::getInstance()->getThOrdenacao($objMdPetTipoProcessoDTO,'Tipo de Processo','NomeProcesso',$arrObjMdPetTipoProcessoDTO).'</th>'."\n";
    $strResultado .= '<th class="infraTh">'.'Unidade para Abertura'.'</th>'."\n";
    $strResultado .= '<th class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objMdPetTipoProcessoDTO,'Indica��o de Interessado', 'SinIIIndicacaoDireta',$arrObjMdPetTipoProcessoDTO).'</th>'."\n";
    $strResultado .= '<th class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objMdPetTipoProcessoDTO,'Documento Principal', 'SinDocExterno',$arrObjMdPetTipoProcessoDTO).'</th>'."\n";
    $strResultado .= '<th class="infraTh" width="15%">A��es</th>'."\n";
    $strResultado .= '</tr>'."\n";
    $strCssTr='';
    for($i = 0;$i < $numRegistros; $i++){
		
      if( $arrObjMdPetTipoProcessoDTO[$i]->getStrSinAtivo()=='S' ){
         $strCssTr = ($strCssTr=='<tr class="infraTrClara">')?'<tr class="infraTrEscura">':'<tr class="infraTrClara">';
      } else {
         $strCssTr ='<tr class="trVermelha">';
     }
       
      $strResultado .= $strCssTr;

      if ($bolCheck){
        $strResultado .= '<td valign="middle">'.PaginaSEI::getInstance()->getTrCheck($i,$arrObjMdPetTipoProcessoDTO[$i]->getNumIdTipoProcessoPeticionamento(), $arrObjMdPetTipoProcessoDTO[$i]->getStrNomeProcesso()).'</td>';
      }
      
      $indicacaoInteressado = $arrObjMdPetTipoProcessoDTO[$i]->getStrSinIIProprioUsuarioExterno() === 'S' ? 'Pr�prio Usu�rio Externo' : 'Indica��o Direta';
      $docExterno          = $arrObjMdPetTipoProcessoDTO[$i]->getStrSinDocExterno() === 'S' ? 'Externo' : 'Gerado';
      $strResultado .= '<td valign="middle">'.$arrObjMdPetTipoProcessoDTO[$i]->getStrNomeProcesso().'</td>';
      
      //Unidade(s)
      $strUnidades = '';
      
      $objMdPetRelTpProcessoUnidRN = new MdPetRelTpProcessoUnidRN();
      $objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
      $objMdPetRelTpProcessoUnidDTO->retTodos();
      $objMdPetRelTpProcessoUnidDTO->retStrsiglaUnidade();
      $objMdPetRelTpProcessoUnidDTO->retStrStaTipoUnidade();
      $objMdPetRelTpProcessoUnidDTO->setNumIdTipoProcessoPeticionamento($arrObjMdPetTipoProcessoDTO[$i]->getNumIdTipoProcessoPeticionamento());
      $arrobjMdPetRelTpProcessoUnidDTO = $objMdPetRelTpProcessoUnidRN->listar($objMdPetRelTpProcessoUnidDTO);
    	
      foreach($arrobjMdPetRelTpProcessoUnidDTO as $objDTO){
            $siglaUnidade = '';
            $siglaUnidade = $objDTO->getStrsiglaUnidade() != null ? $objDTO->getStrsiglaUnidade() : '';
            /*if ($strUnidades!=''){
				$strUnidades .= '<br/>';            	
            }*/
            $tpUnidadeMult = $objDTO->getStrStaTipoUnidade() == MdPetTipoProcessoRN::$UNIDADES_MULTIPLAS ? true : false;
            
            if($tpUnidadeMult){
            	$strUnidades  = 'M�ltiplas';
            }else{
            $strUnidades = $siglaUnidade;
            }
      }
      
      //#6155 - removido. Agora ser�o n Unidades
      //$strResultado .= '<td>'.$arrObjMdPetTipoProcessoDTO[$i]->getStrSiglaUnidade().'</td>';
      
      $strResultado .= '<td valign="middle">'.$strUnidades.'</td>';
      
      $strResultado .= '<td valign="middle">'.$indicacaoInteressado.'</td>';
      $strResultado .= '<td valign="middle">'.$docExterno.'</td>';
      $strResultado .= '<td align="center" valign="middle">';

	  
      if ($bolAcaoConsultar){
        $strResultado .= '<a href="'.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_tipo_processo_consultar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'].'&id_tipo_processo_peticionamento='.$arrObjMdPetTipoProcessoDTO[$i]->getNumIdTipoProcessoPeticionamento())).'" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/consultar.gif" title="Consultar Tipo de Processo" alt="Consultar Tipo de Processo" class="infraImg" /></a>&nbsp;';
      }

      if ($bolAcaoAlterar){
        
        $strResultado .= '<a href="'.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_tipo_processo_alterar&acao_origem='.$_GET['acao'].'&acao_retorno='.$_GET['acao'].'&id_tipo_processo_peticionamento='.$arrObjMdPetTipoProcessoDTO[$i]->getNumIdTipoProcessoPeticionamento())).'" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/alterar.gif" title="Alterar Tipo de Processo" alt="Alterar Tipo de Processo" class="infraImg" /></a>&nbsp;';
      }	

      if ($bolAcaoDesativar || $bolAcaoReativar || $bolAcaoExcluir){
        $strId = $arrObjMdPetTipoProcessoDTO[$i]->getNumIdTipoProcessoPeticionamento();
        $strDescricao = PaginaSEI::getInstance()->formatarParametrosJavaScript($arrObjMdPetTipoProcessoDTO[$i]->getStrNomeProcesso());
      }
 
      if ($bolAcaoDesativar && $arrObjMdPetTipoProcessoDTO[$i]->getStrSinAtivo() == 'S'){
        $strResultado .= '<a href="'.PaginaSEI::getInstance()->montarAncora($strId).'" onclick="acaoDesativar(\''.$strId.'\',\''.$strDescricao.'\');" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/desativar.gif" title="Desativar Tipo de Processo" alt="Desativar Tipo de Processo" class="infraImg" /></a>&nbsp;';
      } else {
	    $strResultado .= '<a href="'.PaginaSEI::getInstance()->montarAncora($strId).'" onclick="acaoReativar(\''.$strId.'\',\''.$strDescricao.'\');" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/reativar.gif" title="Reativar Tipo de Processo" alt="Reativar Tipo de Processo" class="infraImg" /></a>&nbsp;';
      }

      if ($bolAcaoExcluir){
        $strResultado .= '<a href="'.PaginaSEI::getInstance()->montarAncora($strId).'" onclick="acaoExcluir(\''.$strId.'\',\''.$strDescricao.'\');" tabindex="'.PaginaSEI::getInstance()->getProxTabTabela().'"><img src="'.PaginaSEI::getInstance()->getDiretorioImagensGlobal().'/excluir.gif" title="Excluir Tipo de Processo" alt="Excluir Tipo de Processo" class="infraImg" /></a>&nbsp;';
      }

      $strResultado .= '</td></tr>'."\n";
    }
    $strResultado .= '</table>';
  }
  
  
  if( $bolAcaoImprimir ) {
    $arrComandos[] = '<button type="button" accesskey="i" id="btnImprimir" value="Imprimir" onclick="infraImprimirTabela();" class="infraButton"><span class="infraTeclaAtalho">I</span>mprimir</button>';
  }
  
  if ($_GET['acao'] == 'md_pet_tipo_processo_reativar'){
    $arrComandos[] = '<button type="button" accesskey="c" id="btnFecharSelecao" value="Fechar" onclick="window.close();" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
  }else{	
	$arrComandos[] = '<button type="button" accesskey="c" name="btnFechar" id="btnFechar" value="Fechar" onclick="location.href=\''.PaginaSEI::getInstance()->formatarXHTML(SessaoSei::getInstance()->assinarLink('controlador.php?acao=procedimento_controlar&acao_origem='.$_GET['acao'])).'\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
  }
  
 // $strItensSelSinProrrogacaoAutomatica = MdPetIndisponibilidadeINT::montarSelectProrrogacaoAutomaticaPrazos('null','&nbsp;','');
 
  $strItensSelIndicacaoInteressado = MdPetTipoProcessoINT::montarSelectIndicacaoInteressadoPeticionamento('','Todos',$_POST['selIndicacaoInteressado']);
  $strItensSelTipoDocumento        = MdPetTipoProcessoINT::montarSelectTipoDocumento('','Todos',$_POST['selDocumentoPrincipal']);
  
  
}catch(Exception $e){
  PaginaSEI::getInstance()->processarExcecao($e);
} 

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: '.PaginaSEI::getInstance()->getStrNomeSistema().' - '.$strTitulo.' ::');
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
?>
<?
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>

function inicializar(){
  if ('<?=$_GET['acao']?>'=='tipo_processo_peticionamento_selecionar'){
    infraReceberSelecao();
    document.getElementById('btnFecharSelecao').focus();
  }else{
    document.getElementById('btnFechar').focus();
  }
  infraEfeitoTabelas();
}

<? if ($bolAcaoDesativar){ ?>
function acaoDesativar(id,desc){
  if (confirm("Confirma desativa��o do Tipo de Processo para Peticionamento \""+desc+"\"?")){
    document.getElementById('hdnInfraItemId').value=id;
    document.getElementById('frmTipoProcessoPeticionamentoLista').action='<?=$strLinkDesativar?>';
    document.getElementById('frmTipoProcessoPeticionamentoLista').submit();
  }
}

function acaoDesativacaoMultipla(){
  if (document.getElementById('hdnInfraItensSelecionados').value==''){
    alert('Nenhum Tipo de Processo selecionado.');
    return;
  }
  if (confirm("Confirma a desativa��o dos Tipos de Processo selecionados?")){
    document.getElementById('hdnInfraItemId').value='';
    document.getElementById('frmTipoProcessoPeticionamentoLista').action='<?=$strLinkDesativar?>';
    document.getElementById('frmTipoProcessoPeticionamentoLista').submit();
  }
}
<? } ?>

function acaoReativar(id,desc){
  if (confirm("Confirma reativa��o do Tipo de Processo para Peticionamento \""+desc+"\"?")){
    document.getElementById('hdnInfraItemId').value=id;
    document.getElementById('frmTipoProcessoPeticionamentoLista').action='<?=$strLinkReativar?>';
    document.getElementById('frmTipoProcessoPeticionamentoLista').submit();
  }
}

function acaoReativacaoMultipla(){
  if (document.getElementById('hdnInfraItensSelecionados').value==''){
    alert('Nenhum Tipo de Processo selecionado.');
    return;
  }
  if (confirm("Confirma a reativa��o dos Tipo de Processos selecionadas?")){
    document.getElementById('hdnInfraItemId').value='';
    document.getElementById('frmTipoProcessoPeticionamentoLista').action='<?=$strLinkReativar?>';
    document.getElementById('frmTipoProcessoPeticionamentoLista').submit();
  }
}

<? if ($bolAcaoExcluir){ ?>
function acaoExcluir(id,desc){
  if (confirm("Confirma exclus�o do Tipo de Processo para Peticionamento \""+desc+"\"?")){
    document.getElementById('hdnInfraItemId').value=id;
    document.getElementById('frmTipoProcessoPeticionamentoLista').action='<?=$strLinkExcluir?>';
    document.getElementById('frmTipoProcessoPeticionamentoLista').submit();
  }
}


function acaoExclusaoMultipla(){
  if (document.getElementById('hdnInfraItensSelecionados').value==''){
    alert('Nenhuma Tipo de Processo selecionado.');
    return;
  }
  if (confirm("Confirma a exclus�o dos Tipos de Processo selecionados?")){
    document.getElementById('hdnInfraItemId').value='';
    document.getElementById('frmTipoProcessoPeticionamentoLista').action='<?=$strLinkExcluir?>';
    document.getElementById('frmTipoProcessoPeticionamentoLista').submit();
  }
}

<? } ?>

function pesquisar(){
    document.getElementById('frmTipoProcessoPeticionamentoLista').action='<?=$strLinkPesquisar?>';
    document.getElementById('frmTipoProcessoPeticionamentoLista').submit();
}
<?
PaginaSEI::getInstance()->fecharJavaScript();
?>

<style type="text/css">

#lblTipoProcesso {position:absolute;left:0%;top:0%;width:20%;}
#txtTipoProcesso {position:absolute;left:0%;top:40%;width:20%;}

#lblIndicacaoInteressado {position:absolute;left:23%;top:0%;width:20%;}
#selIndicacaoInteressado {position:absolute;left:23%;top:40%;width:20%;}

#lblDocumentoPrincipal {position:absolute;left:46%;top:0%;width:30%;}
#selDocumentoPrincipal {position:absolute;left:46%;top:40%;width:20%;}

</style>

<?php 
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
?>
<form id="frmTipoProcessoPeticionamentoLista" method="post" action="<?=PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">

<?php  PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos); ?>
  <div style="height:4.5em; margin-top: 11px;" class="infraAreaDados" id="divInfraAreaDados">
  
  <!--  Tipo de Processo -->
  	<label id="lblTipoProcesso" for="txtTipoProcesso" class="infraLabelOpcional">Tipo de Processo:</label>
    <input type="text" name="txtTipoProcesso" id="txtTipoProcesso" value="<?php echo isset($_POST['txtTipoProcesso']) ? $_POST['txtTipoProcesso'] : ''?>"  class="infraText" />
    
  <!--  Indica��o de Interessado -->
  	<label id="lblIndicacaoInteressado" for="selIndicacaoInteressado" class="infraLabelOpcional">Indica��o de Interessado:</label>
   <select onchange="pesquisar();" id="selIndicacaoInteressado" name="selIndicacaoInteressado" class="infraSelect" >
  <?=$strItensSelIndicacaoInteressado?>
  </select> 

<!--  Select Documento Principal -->

 <label id="lblDocumentoPrincipal" for="selDocumentoPrincipal" class="infraLabelOpcional">Documento Principal:</label>
  <select onchange="pesquisar();"  id="selDocumentoPrincipal" name="selDocumentoPrincipal" class="infraSelect" >
  <?=$strItensSelTipoDocumento?>
  </select> 
  
    
 </div>   
  <?
 
  PaginaSEI::getInstance()->montarAreaTabela($strResultado,$numRegistros);
  PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
  ?>

</form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>