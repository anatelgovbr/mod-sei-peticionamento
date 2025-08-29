<?
/**
* ANATEL
*
* 17/05/2023 - criado por michaelr.colab@anatel.gov.br - SPASSU
*
*/

try {
    
    require_once dirname(__FILE__).'/../../SEI.php';

    session_start();

    //////////////////////////////////////////////////////////////////////////////
    InfraDebug::getInstance()->setBolLigado(false);
    InfraDebug::getInstance()->setBolDebugInfra(false);
    InfraDebug::getInstance()->limpar();
    //////////////////////////////////////////////////////////////////////////////

    SessaoSEI::getInstance()->validarLink();

    PaginaSEI::getInstance()->prepararSelecao('md_pet_formulario_selecionar');

    // SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    PaginaSEI::getInstance()->salvarCamposPost(array('selModeloPesquisa', 'selModeloEdocPesquisa','txtNomeSeriePesquisa'));

    switch($_GET['acao']){

        case 'md_pet_formulario_selecionar':
            $strTitulo = PaginaSEI::getInstance()->getTituloSelecao('Selecionar Formulário','Selecionar Formulários');
        break;

        default:
            throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
    
    }

    $arrComandos = ['<button type="submit" accesskey="P" id="sbmPesquisar" value="Pesquisar" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>'];

    if ($_GET['acao'] == 'md_pet_formulario_selecionar'){
        $arrComandos[] = '<button type="button" accesskey="T" id="btnTransportarSelecao" value="Transportar" onclick="infraTransportarSelecao();" class="infraButton"><span class="infraTeclaAtalho">T</span>ransportar</button>';
    }

    $objSerieDTO = new SerieDTO();
    $objSerieDTO->retTodos();
    $objSerieDTO->retStrNome();
    $objSerieDTO->retStrNomeGrupoSerie();
    $objSerieDTO->setStrStaAplicabilidade(SerieRN::$TA_FORMULARIO);

    PaginaSEI::getInstance()->prepararOrdenacao($objSerieDTO, 'Nome', InfraDTO::$TIPO_ORDENACAO_ASC);
    PaginaSEI::getInstance()->prepararPaginacao($objSerieDTO);

    $arrObjSerieDTO = (new SerieRN())->listarRN0646($objSerieDTO);
    
    PaginaSEI::getInstance()->processarPaginacao($objSerieDTO);

  $numRegistros = count($arrObjSerieDTO);
  if ($numRegistros > 0){

    $bolCheck = false;
    $strResultado = '';
    $strSumarioTabela = 'Tabela de Formulários';
    $strCaptionTabela = 'Tipos de Formulários';

    $strResultado .= '<table width="99%" class="infraTable" summary="'.$strSumarioTabela.'">'."\n";
    $strResultado .= '<caption class="infraCaption">'.PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela,$numRegistros).'</caption>';
    $strResultado .= '<tr>';
    $strResultado .= '<th class="infraTh" width="1%">'.PaginaSEI::getInstance()->getThCheck().'</th>'."\n";
    $strResultado .= '<th class="infraTh">'.PaginaSEI::getInstance()->getThOrdenacao($objSerieDTO,'Nome','Nome',$arrObjSerieDTO).'</th>'."\n";
    $strResultado .= '<th class="infraTh" width="15%">Ações</th>'."\n";
    $strResultado .= '</tr>'."\n";
    $strCssTr='';
    for($i = 0;$i < $numRegistros; $i++){

      $strCssTr = ($strCssTr=='<tr class="infraTrClara">')?'<tr class="infraTrEscura">':'<tr class="infraTrClara">';
      $strResultado .= $strCssTr;

      $strResultado .= '<td valign="top">'.PaginaSEI::getInstance()->getTrCheck($i,$arrObjSerieDTO[$i]->getNumIdSerie(),$arrObjSerieDTO[$i]->getStrNome()).'</td>';
      $strResultado .= '<td>'.PaginaSEI::getInstance()->formatarXHTML($arrObjSerieDTO[$i]->getStrNome()).'</td>';
      $strResultado .= '<td align="center">';
      $strResultado .= PaginaSEI::getInstance()->getAcaoTransportarItem($i,$arrObjSerieDTO[$i]->getNumIdTipoFormulario());
      $strResultado .= '</td></tr>'."\n";
    }
    $strResultado .= '</table>';
  }

  $strDisplaySelModeloEdoc = 'display:none;';

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
#lblGrupoSerie {position:absolute;left:0%;top:0%;width:20%;}
#selGrupoSerie {position:absolute;left:0%;top:40%;width:20%;}

#lblNomeSeriePesquisa {position:absolute;left:0%;top:0%;width:20%;}
#txtNomeSeriePesquisa {position:absolute;left:0%;top:40%;width:50%;}

#lblModeloPesquisa {position:absolute;left:50%;top:0%;width:24%;}
#selModeloPesquisa {position:absolute;left:50%;top:40%;width:24%;}

#lblModeloEdocPesquisa {position:absolute;left:75%;top:0%;width:24%;<?=$strDisplaySelModeloEdoc?>}
#selModeloEdocPesquisa {position:absolute;left:75%;top:40%;width:24%;<?=$strDisplaySelModeloEdoc?>}

<?
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>

function inicializar(){
    if ('<?=$_GET['acao']?>'=='md_pet_formulario_selecionar'){
        infraReceberSelecao();
    }
    infraEfeitoTabelas();
}

<?
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
?>
<form id="frmSerieLista" method="post" action="<?=PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].'&filtro='. $_GET['filtro'].'&tipoDoc='.$_GET['tipoDoc'].'&acao_origem='.$_GET['acao']))?>">
  <?
  PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
  PaginaSEI::getInstance()->abrirAreaDados('4.5em');
  ?>
  <label id="lblGrupoSerie" for="selGrupoSerie" accesskey="" class="infraLabelOpcional" style="display:none!important">Grupo:</label>
  <select id="selGrupoSerie" name="selGrupoSerie" onchange="this.form.submit();" class="infraSelect" style="display:none!important" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" >
<!--  --><?//=$strItensSelGrupoSerie?>
  </select>
 
  <label id="lblNomeSeriePesquisa" for="txtNomeSeriePesquisa" accesskey="" class="infraLabelOpcional">Nome:</label>
  <input type="text" id="txtNomeSeriePesquisa" name="txtNomeSeriePesquisa" value="<?=PaginaSEI::tratarHTML((isset($strNomeSeriePesquisa) ? $strNomeSeriePesquisa : ''))?>" class="infraText" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />

  <label id="lblModeloPesquisa" for="selModeloPesquisa" accesskey="" class="infraLabelOpcional" style="display:none!important">Modelo:</label>
  <select id="selModeloPesquisa" name="selModeloPesquisa" onchange="this.form.submit();" class="infraSelect" style="display:none!important" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" >
<!--  --><?//=$strItensSelModelo?>
  </select>

  <label id="lblModeloEdocPesquisa" for="selModeloEdocPesquisa" accesskey="" class="infraLabelOpcional">Modelo e-Doc:</label>
  <select id="selModeloEdocPesquisa" name="selModeloEdocPesquisa" onchange="this.form.submit();" class="infraSelect" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" >
<!--  --><?//=$strItensSelModeloEdoc?>
  </select>
   
  <?
  PaginaSEI::getInstance()->fecharAreaDados();
  PaginaSEI::getInstance()->montarAreaTabela($strResultado,$numRegistros);
  PaginaSEI::getInstance()->montarAreaDebug();
  PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
  ?>
</form>
    
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>