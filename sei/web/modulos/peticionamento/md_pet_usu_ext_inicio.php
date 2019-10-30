<?
/**
* ANATEL
*
* 23/06/2016 - criado por marcelo.bezerra@cast.com.br - CAST
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

  SessaoSEIExterna::getInstance()->validarLink();
  SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);


  //=====================================================
  //INICIO - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
  //=====================================================
  
  //tipo de processo escolhido
  $txtTipoProcessoEscolhido = "nome do tipo de processo escolhido";
  
  //texto de orientacoes
  $objMdPetTpProcessoOrientacoesDTO2 = new MdPetTpProcessoOrientacoesDTO();
  $objMdPetTpProcessoOrientacoesDTO2->setNumIdTipoProcessoOrientacoesPeticionamento(MdPetTpProcessoOrientacoesRN::$ID_FIXO_TP_PROCESSO_ORIENTACOES);
  $objMdPetTpProcessoOrientacoesDTO2->retTodos();
  
  $objMdPetTpProcessoOrientacoesRN  = new MdPetTpProcessoOrientacoesRN();
  $objLista = $objMdPetTpProcessoOrientacoesRN->listar($objMdPetTpProcessoOrientacoesDTO2);
  $alterar = count($objLista) > 0;
  
  $txtOrientacoes ='';
  $unidadesFiltradas = array();
  $id_conjunto_estilos = null;
  if($alterar){
  	$txtOrientacoes = $objLista[0]->getStrOrientacoesGerais();
  	$id_conjunto_estilos = $objLista[0]->getNumIdConjuntoEstilos();
  }


  //Recuperando Oragao
  $selectOrgao        = MdPetTipoProcessoINT::montarSelectOrgaoTpProcesso();
  $classe = ''; 
  
  $hidden = '';
  $orgao = '';
 

  if(count($selectOrgao[0]) > 1){
    $hidden = "";
  }else{
    $hiddenOrgao = "display:none;";
    $orgaoUnico = "U";
    $idOrgaoUnico = $selectOrgao[0][0];
    $selectUf        = MdPetTipoProcessoINT::montarSelectUf(null,$idOrgaoUnico);

    if(count($selectUf[0]) > 1){
      $hiddenUF = "";
    }else{
      $hiddenUF = "display:none;";
      $idUfUnica = $selectUf[0][0];

      $selectCidade        = MdPetTipoProcessoINT::montarSelectCidade(null,$idOrgaoUnico,$idUfUnica);
      if(count($selectCidade[0]) > 1){
      $hiddenCidade = "";
      }else{
      $hiddenCidade = "display:none;";
      }
      
    }

  }

  //Escondendo so Campos somente com 1 Elemento
  if(count($selectCidade[0]) > 1){
    $hiddenCidade = "";
    }else{
    $hiddenCidade = "display:none;";
    }

    if(count($selectUf[0]) > 1){
      $hiddenUF = "";
    }else{
      $hiddenUF = "display:none;";
    }

  //$hiddenUF
   
  
//Validação Cidade Unica
  $objTipoProcessoDTO = new MdPetTipoProcessoDTO();
  $objTipoProcessoDTO->retNumIdTipoProcessoPeticionamento();
  $objTipoProcessoDTO->retStrNomeProcesso();
  $objTipoProcessoDTO->retNumIdProcedimento();
  $objTipoProcessoDTO->retStrOrientacoes();
  $objTipoProcessoDTO->setStrSinAtivo('S');
  $objTipoProcessoDTO->setOrdStrNomeProcesso(InfraDTO::$TIPO_ORDENACAO_ASC);
   
  $objTipoProcedimentoRN = new MdPetTipoProcessoRN();
  $arrObjTipoProcedimentoFiltroDTO = $objTipoProcedimentoRN->listar($objTipoProcessoDTO);
  $arrObjTipoProcedimentoRestricaoDTO = InfraArray::converterArrInfraDTO($arrObjTipoProcedimentoFiltroDTO, 'IdProcedimento');
  //Tipo Processo Peticionamento
  $arrObjTipoProcessoPeticionamentoDTO = InfraArray::converterArrInfraDTO($arrObjTipoProcedimentoFiltroDTO, 'IdTipoProcessoPeticionamento');


  $arrTipoProcessoOrgaoCidade = array();
  $arrIdTipoProcesso = array();
  foreach ($arrObjTipoProcedimentoFiltroDTO as $key => $tpProc) {
    if(!in_array($tpProc->getNumIdTipoProcessoPeticionamento(), $arrIdTipoProcesso)){
      array_push($arrIdTipoProcesso, $tpProc->getNumIdTipoProcessoPeticionamento());
    }
  }
 
  $objMdPetRelTpProcessoUnidRN = new MdPetRelTpProcessoUnidRN();
  $objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
  $objMdPetRelTpProcessoUnidDTO->setNumIdTipoProcessoPeticionamento($arrIdTipoProcesso,InfraDTO::$OPER_IN);
  $objMdPetRelTpProcessoUnidDTO->retNumIdUnidade();
  $objMdPetRelTpProcessoUnidDTO->retNumIdOrgaoUnidade();
  $objMdPetRelTpProcessoUnidDTO->retNumIdCidadeContato();
  $objMdPetRelTpProcessoUnidDTO->retNumIdTipoProcessoPeticionamento();
  $objMdPetRelTpProcessoUnidDTO->retStrStaTipoUnidade();
  $arrobjMdPetRelTpProcessoUnidDTO = $objMdPetRelTpProcessoUnidRN->listar($objMdPetRelTpProcessoUnidDTO);

  foreach ($arrobjMdPetRelTpProcessoUnidDTO as $key => $objDTO) {
    //print_r($objDTO->getNumIdTipoProcessoPeticionamento()); die;
    if(!key_exists($objDTO->getNumIdTipoProcessoPeticionamento(), $arrTipoProcessoOrgaoCidade)){
      $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()] = array();
    }
    if(!key_exists($objDTO->getNumIdOrgaoUnidade(), $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()])){
      $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()][$objDTO->getNumIdOrgaoUnidade()] = array();  
    }

    if (!key_exists($objDTO->getNumIdCidadeContato(), $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()][$objDTO->getNumIdOrgaoUnidade()])) {
      $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()][$objDTO->getNumIdOrgaoUnidade()][$objDTO->getNumIdCidadeContato()] = 1;
    } else {
      $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()][$objDTO->getNumIdOrgaoUnidade()][$objDTO->getNumIdCidadeContato()] = $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()][$objDTO->getNumIdOrgaoUnidade()][$objDTO->getNumIdCidadeContato()] + 1;
    }


  }
  $arrIdsTpProcesso = array_keys($arrTipoProcessoOrgaoCidade);
  //verificando se existe algum tipo de processo com divergencia de orgao e cidade iguais
  if ($arrTipoProcessoOrgaoCidade) {
    $tipoProcessoDivergencia = false;
    foreach ($arrTipoProcessoOrgaoCidade as $key => $dados) {
        foreach ($dados as $cidade) {
          foreach($cidade as $qnt){          
            if ($qnt > 1) {
              foreach ($arrObjTipoProcedimentoFiltroDTO as $chaveTpProc => $tpProc) {
                if($tpProc->getNumIdTipoProcessoPeticionamento()== $key){
                  unset($arrObjTipoProcedimentoFiltroDTO[$chaveTpProc]);
                  $chaveRemover = array_search($key, $arrIdsTpProcesso);
                  unset($arrIdsTpProcesso[$chaveRemover]);
                }
              }
            }
          }
        }
        
    }
  }
//Fim validação cidade Unica

//Restrição
  $arrRestricao = array();
  foreach ($arrObjTipoProcedimentoFiltroDTO as $key => $tpProc) {
   
    //Verifica se existe restrição para o tipo de processo
    $objTipoProcedRestricaoRN = new TipoProcedRestricaoRN();
    $objTipoProcedRestricaoDTO = new TipoProcedRestricaoDTO();
    $objTipoProcedRestricaoDTO->retNumIdOrgao();
    $objTipoProcedRestricaoDTO->retNumIdUnidade();
    $objTipoProcedRestricaoDTO->setNumIdTipoProcedimento($tpProc->getNumIdProcedimento());
    $arrObjTipoProcedRestricaoDTO = $objTipoProcedRestricaoRN->listar($objTipoProcedRestricaoDTO);

    $idOrgaoRestricao = InfraArray::converterArrInfraDTO($arrObjTipoProcedRestricaoDTO, 'IdOrgao');
    $idUnidadeRestricao = InfraArray::converterArrInfraDTO($arrObjTipoProcedRestricaoDTO, 'IdUnidade');
    
    $objMdPetRelTpProcessoUnidRN = new MdPetRelTpProcessoUnidRN();
    $objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
    $objMdPetRelTpProcessoUnidDTO->retTodos();
    $objMdPetRelTpProcessoUnidDTO->retStrsiglaUnidade();
    $objMdPetRelTpProcessoUnidDTO->retStrStaTipoUnidade();
    $objMdPetRelTpProcessoUnidDTO->retStrdescricaoUnidade();
    $objMdPetRelTpProcessoUnidDTO->retNumIdUnidade();
    $objMdPetRelTpProcessoUnidDTO->retNumIdOrgaoUnidade();
    $objMdPetRelTpProcessoUnidDTO->retStrDescricaoOrgao();
    $objMdPetRelTpProcessoUnidDTO->retStrSiglaOrgao();
    $objMdPetRelTpProcessoUnidDTO->retNumIdCidadeContato();
    $objMdPetRelTpProcessoUnidDTO->setNumIdTipoProcessoPeticionamento($tpProc->getNumIdTipoProcessoPeticionamento());
    $arrobjMdPetRelTpProcessoUnidDTO = $objMdPetRelTpProcessoUnidRN->listar($objMdPetRelTpProcessoUnidDTO);
    

      foreach ($arrobjMdPetRelTpProcessoUnidDTO as $objDTO) {
      
        //Verifica se tem alguma unidade ou órgão diferente dos restritos
        if(($idOrgaoRestricao && $idOrgaoRestricao[0] != null) && !in_array($objDTO->getNumIdOrgaoUnidade(), $idOrgaoRestricao)){
          $arrRestricao [] = $tpProc->getNumIdProcedimento();
        }
        if(($idUnidadeRestricao && $idUnidadeRestricao[0] != null) && !in_array($objDTO->getNumIdUnidade(), $idUnidadeRestricao)){
          $arrRestricao [] = $tpProc->getNumIdProcedimento();
        }

      }

  }
 
  //Fim restrição
  
  $objTipoProcessoDTO = new MdPetTipoProcessoDTO();
  $objTipoProcessoDTO->setNumIdTipoProcessoPeticionamento($arrIdsTpProcesso,infraDTO::$OPER_IN);
  $objTipoProcessoDTO->retNumIdTipoProcessoPeticionamento();
  $objTipoProcessoDTO->retStrNomeProcesso();
  if(count($arrRestricao)){
    $objTipoProcessoDTO->setNumIdProcedimento($arrRestricao,infraDTO::$OPER_NOT_IN);
  }
  $objTipoProcessoDTO->retStrOrientacoes();
  $objTipoProcessoDTO->setStrSinAtivo('S');
  $objTipoProcessoDTO->setOrdStrNomeProcesso(InfraDTO::$TIPO_ORDENACAO_ASC);
  $objTipoProcedimentoRN = new MdPetTipoProcessoRN();
  $arrObjTipoProcedimentoFiltroDTO = $objTipoProcedimentoRN->listar($objTipoProcessoDTO);
  
  

   $objEditorRN = new EditorRN();
   
   if ($_GET['iframe']!=''){
      PaginaSEIExterna::getInstance()->abrirStyle();
      echo $objEditorRN->montarCssEditor($id_conjunto_estilos);
      PaginaSEIExterna::getInstance()->fecharStyle();
      echo $txtOrientacoes;
      die();	
   }
     
  
  //=====================================================
  //FIM - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
  //=====================================================
  
  switch($_GET['acao']){
    
  	case 'md_pet_usu_ext_iniciar':
  		$strTitulo = 'Peticionamento de Processo Novo';
  		break;
  		
    default:
      throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
  }

}catch(Exception $e){
  PaginaSEIExterna::getInstance()->processarExcecao($e);
}

$hashAnexo = "";
$idAnexo = "";

PaginaSEIExterna::getInstance()->montarDocType();
PaginaSEIExterna::getInstance()->abrirHtml();
PaginaSEIExterna::getInstance()->abrirHead();
PaginaSEIExterna::getInstance()->montarMeta();
PaginaSEIExterna::getInstance()->montarTitle(':: '.PaginaSEIExterna::getInstance()->getStrNomeSistema().' - '.$strTitulo.' ::');
PaginaSEIExterna::getInstance()->montarStyle();
PaginaSEIExterna::getInstance()->abrirStyle();
$objEditorRN = new EditorRN();
echo $objEditorRN->montarCssEditor(null);
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
PaginaSEIExterna::getInstance()->fecharJavaScript();
?>
<style type="text/css">
#field1 {height: auto; width: 96%; margin-bottom: 11px;}
.sizeFieldset {height:auto; width: 86%;}
.fieldsetClear {border:none !important;}
</style>
<?php 
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');

?> 
<form id="frmIndisponibilidadeCadastro" method="post" onsubmit="return OnSubmitForm();"  
      action="<?=PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao']))?>">
<?
PaginaSEIExterna::getInstance()->abrirAreaDados('auto');
?>
 <br />
 <fieldset id="field1" class="infraFieldset sizeFieldset" style="width:auto">
 <legend class="infraLegend">&nbsp; Orientações Gerais &nbsp;</legend>
   <? 
   echo '<iframe id=ifrConteudoHTML name=ifrConteudoHTML style="height:100%;width:100%" frameborder="0" marginheight="0" marginwidth="0" src="' . SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usu_ext_iniciar&iframe=S') . '"></iframe>'; 
   ?>
 </fieldset>


<br>

 <div id="divInfraAreaDadosDinamica" class="infraAreaDadosDinamica" style="width:95%;">


 <table class="tg">
  <tr>
    <th class="tg-0lax">
    
    <label class="infraLabelOpcional" style="font-size:12px;">Tipo do Processo:</label>
<br />
	<input type="text" id="txtFiltro" onkeypress="filtro()" class="infraAutoCompletar" autocomplete="off" style="width:200px;" value="<?if (isset($_POST['txtFiltro'])) echo $_POST['txtFiltro'];?>">

    </th>
    <th></th><th></th>
    <th style="<?php echo $hiddenOrgao ?>" id="OrgaoHidd">
    
    <label id="lblOrgao" for="selOrgao" style="font-size:12px;"  class="infraLabelOpcional">Orgão: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" align="top" style="width:16px;height:16px;" <?= PaginaSEI::montarTitleTooltip("Por meio deste campo é possível filtrar a lista de Tipos de Processos que podem ser abertos em determinado Órgão.") ?> alt="Ajuda" class="infraImg"/></label>
  <select onchange="pesquisarUF(this)"  style="width:120px;"  id="selOrgao" name="selOrgao" class="infraSelect" >
  <?php if($hiddenOrgao != "disabled"){ ?>
  <option value="">Todos</option>
  <?php } ?>
  <?= 
  $idOrgao = $selectOrgao[0];
  $orgao = $selectOrgao[1];
  for ($i=0; $i < count($idOrgao) ; $i++) { 
    echo '<option value="' . $idOrgao[$i] . '">' . $orgao[$i] . '</option>';
  }
  
  ?>
  </select> 

    
    </th>
    <th></th><th></th>
    <th style=" <?php echo $hiddenUF; ?> " id="UFHidd" >
    
    <label id="lblUF" for="selUF" style="font-size:12px;" class="infraLabelOpcional">UF: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" align="top" style="width:16px;height:16px;" <?= PaginaSEI::montarTitleTooltip("Por meio deste campo é possível filtrar a lista de Tipos de Processos que podem ser abertos em determinada UF.") ?> alt="Ajuda" class="infraImg"/></label>
  <select onchange="pesquisarCidade(this)" style="width:80px;"  id="selUF" name="selUF" class="infraSelect" >
  <option value="">Todos</option>
        <?php if($orgaoUnico == "U"){ ?>
          <?= 
          
          $idUf = $selectUf[0];
          $uf = $selectUf[1];
            for ($i=0; $i < count($idUf) ; $i++) { 
              echo '<option value="' . $idUf[$i] . '">' . $uf[$i] . '</option>';
            }
          
      
          ?>
    <?php } ?>
  </select>
    
    </th>
    <th></th><th></th>
    <th  style="<?php echo $hiddenCidade ?>" id="cidadeHidd">
    
    <label id="lblCidade" for="selCidade" style="font-size:12px;" class="infraLabelOpcional">Cidade: <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" align="top" style="width:16px;height:16px;" <?= PaginaSEI::montarTitleTooltip("Por meio deste campo é possível filtrar a lista de Tipos de Processos que podem ser abertos em determinada Cidade.") ?> alt="Ajuda" class="infraImg"/></label>
  <select onchange="pesquisarFinal(this)" style="width:120px;"  id="selCidade" name="selCidade" class="infraSelect" >
  <option value="">Todos</option>
      <?= 
              
              $idCidade = $selectCidade[0];
              $cidade = $selectCidade[1];
                for ($i=0; $i < count($idCidade) ; $i++) { 
                  echo '<option value="' . $idCidade[$i] . '">' . $cidade[$i] . '</option>';
                }
              
          
        ?>
  </select>
    
    </th>
  </tr>
</table>

<br>

<label class="infraLabelObrigatorio" style="font-size:1.7em;">Escolha o Tipo do Processo que deseja iniciar:</label>
<br />
</div>

<div id="divInfraAreaTabela" class="infraAreaTabela" style="width:90%;">

<table class="infraTable" id="tblTipoProcedimento" style="background-color:white;" summary="Tabela de Tipos de Processo">
<div id="hiddeTable">

<?php  if(count($arrObjTipoProcedimentoFiltroDTO)){ ?>
  <? foreach($arrObjTipoProcedimentoFiltroDTO as $itemDTO){ ?>

  <? if($_GET['id_tipo_procedimento'] == $itemDTO->getNumIdTipoProcessoPeticionamento() ){ ?>
  <? $classe = 'infraTrClara infraTrAcessada'; ?>  
  <? }else{
    $classe = 'infraTrClara';
  } ?>

<tr class="<? echo $classe; ?>" data-desc="'<?php echo strtolower(InfraString::excluirAcentos($itemDTO->getStrNomeProcesso())); ?>'"> 
  <td >
	<? $link = PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_usu_ext_cadastrar&id_tipo_procedimento=' . $itemDTO->getNumIdTipoProcessoPeticionamento() )); ?>
	<a href="<?= $link ?>" 
	   title="<?= $itemDTO->getStrOrientacoes() ?>" 
	   class="ancoraOpcao">
	<?= $itemDTO->getStrNomeProcesso() ?>
	</a>
	</td>
</tr>
<? } ?>
<?php } ?>
<div>
</table>
<input type="hidden" id="hdnIdOrgao" name="hdnIdOrgao" value="" />
<input type="hidden" id="hdnIdUf" name="hdnIdUf" value="" />
<input type="hidden" id="hdnIdCidade" name="hdnIdCidade" value="" />
<input type="hidden" id="hdnIdTipoProcedimentoRetorno" name="hdnIdTipoProcedimentoRetorno" value="<?php echo $_GET['id_tipo_procedimento'] ?>" />
<input type="hidden" id="hdnIdOrgaoUnico" name="hdnIdOrgaoUnico" value="<?php echo $orgaoUnico ?>" />
<input type="hidden" id="hdnIdOrgaoUnicoId" name="hdnIdOrgaoUnicoId" value="<?php echo $idOrgaoUnico ?>" />
<input type="hidden" id="hdnInfraNroItens" name="hdnInfraNroItens" value="" />
<input type="hidden" id="hdnInfraItemId" name="hdnInfraItemId" value="" />
<input type="hidden" id="hdnInfraItensSelecionados" name="hdnInfraItensSelecionados" value="" />
<input type="hidden" id="hdnInfraSelecoes" name="hdnInfraSelecoes" value="Infra" />

</div>
   
</form>

<? 
PaginaSEIExterna::getInstance()->fecharAreaDados();
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
?>

<script type="text/javascript">


function pesquisarUF(idOrgao){
  document.getElementById("selUF").disabled = false;
  document.getElementById('hdnIdOrgao').value = idOrgao.value;
  document.getElementById('hdnIdUf').value = '';
  document.getElementById('hdnIdCidade').value = '';
  document.getElementById("selCidade").disabled = false;
  
  if(document.getElementById("selOrgao").value == ""){
    //Inserindo 'Todos' nas combos UF e Cidade
    //Escondendo as Combos UF e Cidade
    document.getElementById("UFHidd").style.display = "none";
    document.getElementById("cidadeHidd").style.display = "none";

    document.getElementById('hdnIdOrgao').value = '';
    document.getElementById('hdnIdUf').value = '';
    document.getElementById('hdnIdCidade').value = '';
   
    //Travando as combos caso o orgão esteja na opção TOdos
    document.getElementById("selUF").disabled = true;
    document.getElementById("selCidade").disabled = true;
  }

  mudarTpProcesso();
  infraSelectLimpar('selUF');
  infraSelectLimpar('selCidade');

  if(document.getElementById("selOrgao").value == ""){

    document.getElementById("UFHidd").style.display = "none";
    document.getElementById("cidadeHidd").style.display = "none";

    //Inserindo 'Todos' nas combos UF e Cidade
    var selectMultiple = document.getElementById('selUF');
    var opt = document.createElement('option');
     opt.value = "";
     opt.innerHTML = "Todos";
     selectMultiple.appendChild(opt);

     var selectMultipleCidade = document.getElementById('selCidade');
    var optCidade = document.createElement('option');
    optCidade.value = "";
    optCidade.innerHTML = "Todos";
     selectMultipleCidade.appendChild(optCidade);
  }
  
  
  //Setando orgão
  if(document.getElementById("selOrgao").value != ""){
  $.ajax({
             dataType: 'xml',
             method: 'POST',
             url: '<?php echo SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_consultar_tipo_processo_uf');?>',
             data: {
                 'idOrgao': idOrgao.value
             },
             error: function (dados) {
                 console.log(dados);
             },
             success: function (data) {
              
              var selectMultiple = document.getElementById('selUF');
                   
              try {
                var count=$(data).find("item").size();
                    if(count > 1){
                var opt = document.createElement('option');
                    opt.value = "";
                  opt.innerHTML = "Todos";
                  selectMultiple.appendChild(opt);

                  
                    }

              $.each($(data).find('item'),function (i,j) {

                //Caso tenha somente uma uf vinculado com o orgão.
                var count=$(data).find("item").size();

                    if(count < 2){
                        document.getElementById("UFHidd").style.display = "none";
                      }else{
                        document.getElementById("UFHidd").style.display = "";
                      }

                    if(count < 2){
                      document.getElementById('hdnIdUf').value = $(j).attr("id");
                      if(document.getElementById("selOrgao").value == ""){
                       document.getElementById('hdnIdUf').value = '';
                       document.getElementById('hdnIdCidade').value = '';

                      //Travando as combos caso o orgão esteja na opção TOdos
                      document.getElementById("selUF").disabled = true;
                      document.getElementById("selCidade").disabled = true;
                          
                            }

                      mudarTpProcesso();
                      $.ajax({
                        dataType: 'xml',
                        method: 'POST',
                        url: '<?php echo SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_consultar_tipo_processo_cidade');?>',
                        data: {
                            'idUf':$(j).attr("id"),
                            'idOrgao':idOrgao.value
                        },
                        error: function (dados) {
                            console.log(dados);
                        },
                        success: function (data) {
                          
                          var selectMultiple = document.getElementById('selCidade');
                              
                              try {
                                
                                var count=$(data).find("item").size();
                                if(count < 2){
                                  document.getElementById("cidadeHidd").style.display = "none";
                                }else{
                                  document.getElementById("cidadeHidd").style.display = "";
                                }

                                if(count < 2){
                                  document.getElementById("selCidade").disabled = true;
                                }
                                //Caso retorne mais de uma Cidade
                                if(count > 1){
                                  var opt = document.createElement('option');
                                    opt.value = "";
                                  opt.innerHTML = "Todos";
                                  selectMultiple.appendChild(opt);
                                }
                               
                              $.each($(data).find('item'),function (i,j) {
                                //Atribuindo o Id da cidade caso haja somente uma cidade
                                var count=$(data).find("item").size();
                                if(count < 2){
                                    document.getElementById('hdnIdCidade').value = $(j).attr("id");
                                    //mudarTpProcesso();
                                }
                          
                              var opt = document.createElement('option');
                                    opt.value = $(j).attr("id");
                                  opt.innerHTML = $(j).attr("descricao");
                                  selectMultiple.appendChild(opt);
                            });
                
                            var div = document.getElementById('selCidade');
                                div.appendChild(selectMultiple); 

                                
                
                                    }
                                    catch(err) {
                
                                    }
                          
                        }
                        
                    });

                    document.getElementById("selUF").disabled = true;

                    }
              
                    if(document.getElementById("selOrgao").value != ""){

                    var opt = document.createElement('option');
                    opt.value = $(j).attr("id");
                    opt.innerHTML = $(j).attr("descricao");
                    selectMultiple.appendChild(opt);

                    }
            });

            var div = document.getElementById('selUF');
                div.appendChild(selectMultiple); 

                    }
                    catch(err) {

                    }
                   
             }
             
         });
  }




}


//Uf
function pesquisarCidade(idUf){
  
  document.getElementById("selCidade").disabled = false;
  document.getElementById('hdnIdUf').value = idUf.value;
  document.getElementById('hdnIdCidade').value = '';
  
  mudarTpProcesso();
  infraSelectLimpar('selCidade');
  
  if(document.getElementById("selUF").value == ""){
     document.getElementById("cidadeHidd").style.display = "none";
//Inserindo 'Todos' na combo  Cidade

    var selectMultipleCidade = document.getElementById('selCidade');
    var optCidade = document.createElement('option');
    optCidade.value = "";
    optCidade.innerHTML = "Todos";
    selectMultipleCidade.appendChild(optCidade);
    document.getElementById("selCidade").disabled = true;
}

 
  $.ajax({
             dataType: 'xml',
             method: 'POST',
             url: '<?php echo SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_consultar_tipo_processo_cidade');?>',
             data: {
                 'idOrgao': document.getElementById('hdnIdOrgao').value,
                 'idUf':idUf.value
             },
             error: function (dados) {
                 console.log(dados);
             },
             success: function (data) {
              
               var selectMultiple = document.getElementById('selCidade');
                   
                   //Coloca vazio caso seja mais de um
                   try {
                    var count=$(data).find("item").size();
                    if(count > 1){
                var opt = document.createElement('option');
                    opt.value = "";
                  opt.innerHTML = "Todos";
                  selectMultiple.appendChild(opt);
                    }
      
                   $.each($(data).find('item'),function (i,j) {



                    var count=$(data).find("item").size();
                    //Escondendo Elemento caso retorne somente um Elemento
                    
                      if(count < 2){
                        document.getElementById("cidadeHidd").style.display = "none";
                      }else{
                        document.getElementById("cidadeHidd").style.display = "";
                      }


                    if(count < 2){
                    
                      //Caso a Uf retorne somente uma cidade
                      document.getElementById('hdnIdCidade').value = $(j).attr("id");
                      mudarTpProcesso();
                      $.ajax({
                        dataType: 'xml',
                        method: 'POST',
                        url: '<?php echo SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_consultar_tipo_processo_cidade');?>',
                        data: {
                            'idUf':$(j).attr("id")
                            
                            
                        },
                        error: function (dados) {
                            console.log(dados);
                        },
                        success: function (data) {
                          
                          var selectMultiple = document.getElementById('selCidade');
                              
                              try {
                  
                              $.each($(data).find('item'),function (i,j) {
                                //Atribuindo o Id da cidade caso haja somente uma cidade
                                var count=$(data).find("item").size();
                                if(count < 2){
                                    document.getElementById('hdnIdCidade').value = $(j).attr("id");
                                }
                          
                              var opt = document.createElement('option');
                                    opt.value = $(j).attr("id");
                                  opt.innerHTML = $(j).attr("descricao");
                                  selectMultiple.appendChild(opt);
                            });
                
                            var div = document.getElementById('selCidade');
                                div.appendChild(selectMultiple); 
                
                                    }
                                    catch(err) {
                
                                    }
                          
                        }
                        
                    });
                    
                    document.getElementById("selCidade").disabled = true;
                    }
              
                   var opt = document.createElement('option');
                         opt.value = $(j).attr("id");
                       opt.innerHTML = $(j).attr("descricao");
                       selectMultiple.appendChild(opt);
                 });
     
                 var div = document.getElementById('selCidade');
                     div.appendChild(selectMultiple); 
     
                         }
                         catch(err) {
     
                         }
               
             }
             
         });

}

function pesquisarFinal(idCidade){
  document.getElementById('hdnIdCidade').value = idCidade.value;
  mudarTpProcesso();

  

}

function mudarTpProcesso(){

//Somente se o usuário escolher opção todos
  if(document.getElementById('hdnIdOrgao').value == '' && document.getElementById('hdnIdUf').value == '' && document.getElementById('hdnIdCidade').value == ''){
    var filtros = {};
  }

  
  if(document.getElementById('hdnIdOrgao').value != '' && document.getElementById('hdnIdUf').value == ''){
    var filtros = {orgao:document.getElementById('hdnIdOrgao').value};
  }else if(document.getElementById('hdnIdOrgao').value != '' && document.getElementById('hdnIdUf').value != ''){
    var filtros = {orgao:document.getElementById('hdnIdOrgao').value,uf:document.getElementById('hdnIdUf').value};
  }
  if(document.getElementById('hdnIdOrgao').value != '' && document.getElementById('hdnIdUf').value != '' && document.getElementById('hdnIdCidade').value != ''){
    var filtros = {orgao:document.getElementById('hdnIdOrgao').value,uf:document.getElementById('hdnIdUf').value,cidade:document.getElementById('hdnIdCidade').value}; 
  }

  //Somente se o usuário escolher a UF quando entrar na tela
  if(document.getElementById('hdnIdUf').value != '' && document.getElementById('hdnIdOrgao').value == '' && document.getElementById('hdnIdCidade').value == '' ){
    var filtros = {uf:document.getElementById('hdnIdUf').value};
    //Uf com unica cidade
  }

  //Somente se o usuário escolher a Cidade quando entrar na tela
  if(document.getElementById('hdnIdUf').value == '' && document.getElementById('hdnIdOrgao').value == '' && document.getElementById('hdnIdCidade').value != '' ){
    var filtros = {cidade:document.getElementById('hdnIdCidade').value};
    
  }

  //Somente se o usuário escolher a Uf e Cidade quando entrar na tela
  if(document.getElementById('hdnIdUf').value != '' && document.getElementById('hdnIdOrgao').value == '' && document.getElementById('hdnIdCidade').value != '' ){
    var filtros = {cidade:document.getElementById('hdnIdCidade').value,uf:document.getElementById('hdnIdUf').value};
    
  }
  console.log(filtros);
 $.ajax({
            dataType: 'xml',
            method: 'POST',
            url: '<?php echo SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_consultar_tipo_processo_externo');?>',
            data: filtros,
            error: function (dados) {
                console.log(dados);
            },
            success: function (data) {
              var url = '';
              var conteudo = '';
              var urlFinal = '';
                  try {
                    
                    $.each($(data).find('item'),function (i,j) {

                     url = $(j).attr("id");
                     
                     //Url dinâmica
                     
                    if(document.getElementById('hdnIdUf').value != '' && document.getElementById('hdnIdOrgao').value == '' && document.getElementById('hdnIdCidade').value == '' ){
                   //  var troca = url.split("&")[2];
                   // urlFinal =  url.replace("id_uf=x",troca.replace("x",document.getElementById('hdnIdUf').value));
                      
                    }
                      
                     conteudo += '<tr class="infraTrClara" id="'+$(j).attr("descricao")+'" onmouseover="classChange(this)" onmouseout="removeClass(this)"  data-desc="'+$(j).attr("descricao").toLowerCase()+'"><td><a href="'+$(j).attr("id")+'"  title="'+$(j).attr("complemento")+'" class="ancoraOpcao">'+$(j).attr("descricao")+'</a> </td></tr>';
                   
                 });

                 document.getElementById('tblTipoProcedimento').innerHTML = conteudo
    
                      }
                      
                  catch(err) {
    
                 }
    
            }
            
        });

        


}

function classChange(id){
  document.getElementById(id.id).className = "infraTrSelecionada";
}

function removeClass(id){
  document.getElementById(id.id).classList.remove("infraTrSelecionada");
}

function filtro(){
  
  seiPrepararFiltroTabela(document.getElementById('tblTipoProcedimento'),document.getElementById('txtFiltro'));

}

function inicializar(){
  infraEfeitoTabelas();
  
  if(document.getElementById("hdnIdOrgaoUnico").value == "U" ){
  document.getElementById("selUF").disabled = false;
  }else{
    document.getElementById("selUF").disabled = true;
  }
  document.getElementsByTagName("BODY")[0].onresize = function() {resizeIFramePorConteudo()};
}

//Filtro JS

function seiPrepararFiltroTabela(objTabela,objInput){
  $(objInput).on('keyup',objTabela,seiFiltrarTabela);
  $(objInput).focus();
  var tbody=$(objTabela).find('tbody');
  tbody.find('tr').each(function(){
    $(this).removeAttr('onmouseover').removeAttr('onmouseout');
  });
  tbody.on('mouseenter','tr',function(e){
    $('.infraTrSelecionada').removeClass('infraTrSelecionada');
    $(e.currentTarget).addClass('infraTrSelecionada').find('.ancoraOpcao').focus();
  });
  $(document).on('keydown',function(e){
    if(e.which!=40 && e.which!=38) return;
    var sel=$('.infraTrSelecionada');
    if(sel.length==0) {
      sel=tbody.find('tr:visible:first').addClass('infraTrSelecionada');
    } else if(e.which==40) {
      if (sel.nextAll('tr:visible').length != 0) {
        sel.removeClass('infraTrSelecionada');
        sel=sel.nextAll('tr:visible:first').addClass('infraTrSelecionada');
      }
    } else {
      if (sel.prevAll('tr:visible').length != 0) {
        sel.removeClass('infraTrSelecionada');
        sel=sel.prevAll('tr:visible:first').addClass('infraTrSelecionada');
      }
    }
    sel.find('.ancoraOpcao').focus();
    e.preventDefault();
  })
}


function seiFiltrarTabela(event){
  var tbl= $(event.data).find('tbody');
  var filtro=$(this).val();
  
  if (filtro.length>0){
    $('.infraTrSelecionada:hidden').removeClass('infraTrSelecionada');
    filtro=infraRetirarAcentos(filtro).toLowerCase();
    tbl.find('tr').each(function(){
      var ancora=$(this).find('.ancoraOpcao');
      var descricao=$(this).attr('data-desc');
      
      var i=descricao.indexOf(filtro);
      if(i==-1)
        $(this).hide();
      else {
        $(this).show();
        $(this).val();
        var text=ancora.text();
        var html='';
        var ini=0;
        while (i!=-1) {
          html+=text.substring(ini,i);
          html+='<span class="infraSpanRealce">';
          html+=text.substr(i,filtro.length);
          html+='</span>';
          ini=i+filtro.length;
          i=descricao.indexOf(filtro,ini);
        }
        html+=text.substr(ini);
        ancora.html(html);
      }
    });
  } else {
    tbl.find('tr').show();
    tbl.find('.ancoraOpcao').each(function(){$(this).html($(this).text());});
  }
}



function OnSubmitForm() {
	return true;
}

function resizeIFramePorConteudo(){
	var id = 'ifrConteudoHTML';
	var ifrm = document.getElementById(id);
	ifrm.style.visibility = 'hidden';
	ifrm.style.height = "10px"; 

	var doc = ifrm.contentDocument? ifrm.contentDocument : ifrm.contentWindow.document;
	doc = doc || document;
	var body = doc.body, html = doc.documentElement;

	var width = Math.max( body.scrollWidth, body.offsetWidth, 
	                      html.clientWidth, html.scrollWidth, html.offsetWidth );
	ifrm.style.width='100%';

	var height = Math.max( body.scrollHeight, body.offsetHeight, 
	                       html.clientHeight, html.scrollHeight, html.offsetHeight );
	ifrm.style.height=height+'px';

	ifrm.style.visibility = 'visible';
}

document.getElementById('ifrConteudoHTML').onload = function() {
	resizeIFramePorConteudo();
}

</script>