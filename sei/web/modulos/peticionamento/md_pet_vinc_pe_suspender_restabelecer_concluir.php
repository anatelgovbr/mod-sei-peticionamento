<?
/*
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 15/09/2008 - criado por marcio_db
*
*
*/

try {
  require_once dirname(__FILE__) . '/../../SEI.php';

  session_start();

  //////////////////////////////////////////////////////////////////////////////
  InfraDebug::getInstance()->setBolLigado(false);
  InfraDebug::getInstance()->setBolDebugInfra(false);
  InfraDebug::getInstance()->limpar();
  //////////////////////////////////////////////////////////////////////////////

  SessaoSEI::getInstance()->validarLink();

  //SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

  //PaginaSEI::getInstance()->salvarCamposPost(array('selCargoFuncao'));

  PaginaSEI::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);

  $strParametros = '';

  $idVinculo = isset($_GET['idVinculo']) ? $_GET['idVinculo'] : $_POST['hdnIdVinculo'];
  $idContato = isset($_GET['idContato']) ? $_GET['idContato'] : $_POST['hdnIdContato'];
  $numeroSEI = isset($_GET['numeroSEI']) ? $_GET['numeroSEI'] : $_POST['hdnNumeroSei'];

  $txtNumeroCpfResponsavel = isset($_POST['txtNumeroCpfResponsavel']) ? $_POST['txtNumeroCpfResponsavel'] : $_GET['txtNumeroCpfResponsavel'];
  $hdnNomeNovo = $_POST['hdnNomeNovo'];

  $bolAssinaturaOK = false;
  $bolPermiteAssinaturaLogin=false;
  $bolPermiteAssinaturaCertificado=false;
  $bolAutenticacao = false;

  switch($_GET['acao']){

    case 'md_pet_vinc_responsavel_concluir_alt':

      $objInfraParametro=new InfraParametro(BancoSEI::getInstance());
      $tipoAssinatura=$objInfraParametro->getValor('SEI_TIPO_ASSINATURA_INTERNA');

      $strTitulo = 'Assinatura de Documento';

      switch ($tipoAssinatura){
        case 1:
          $bolPermiteAssinaturaCertificado=true;
          $bolPermiteAssinaturaLogin=true;
          break;
        case 2:
          $bolPermiteAssinaturaLogin=true;
          break;
        case 3:
          $bolPermiteAssinaturaCertificado=true;
      }

      $objAssinaturaDTO = new AssinaturaDTO();
      $objAssinaturaDTO->setStrStaFormaAutenticacao($_POST['hdnFormaAutenticacao']);

      if (!isset($_POST['hdnFlagAssinatura'])){
        $objAssinaturaDTO->setNumIdOrgaoUsuario(SessaoSEI::getInstance()->getNumIdOrgaoUsuario());
      }else{
        $objAssinaturaDTO->setNumIdOrgaoUsuario($_POST['selOrgao']);
      }

      $objAssinaturaDTO->setNumIdUsuario($_POST['hdnIdUsuario']);
      $objAssinaturaDTO->setStrSenhaUsuario($_POST['pwdSenha']);

      //$objAssinaturaDTO->setStrCargoFuncao(PaginaSEI::getInstance()->recuperarCampo('selCargoFuncao'));

      $objInfraDadoUsuario = new InfraDadoUsuario(SessaoSEI::getInstance());

      $strChaveDadoUsuarioAssinatura = 'ASSINATURA_CARGO_FUNCAO_'.SessaoSEI::getInstance()->getNumIdUnidadeAtual();

      if (!isset($_POST['selCargoFuncao'])){
        $objAssinaturaDTO->setStrCargoFuncao($objInfraDadoUsuario->getValor($strChaveDadoUsuarioAssinatura));
      }else{
        $objAssinaturaDTO->setStrCargoFuncao($_POST['selCargoFuncao']);

        if ($objAssinaturaDTO->getNumIdUsuario()==SessaoSEI::getInstance()->getNumIdUsuario()) {
          $objInfraDadoUsuario->setValor($strChaveDadoUsuarioAssinatura, $_POST['selCargoFuncao']);
        }
      }
      if ($_POST['hdnFormaAutenticacao'] != null){

        try{

          $objMdPetProcessoRN = new MdPetProcessoRN();

          $objInfraSip = new InfraSip(SessaoSEI::getInstance());
          $objInfraSip->autenticar(SessaoSEI::getInstance()->getNumIdOrgaoUsuario(), null, SessaoSEI::getInstance()->getStrSiglaUsuario(), $_POST['pwdSenha']);

          // POST

          // Responsavel - Novo
          $objMdPetContatoRN = new MdPetContatoRN();
          $idTipoContatoUsExt = $objMdPetContatoRN->getIdTipoContatoUsExt();

          $objContatoRN = new MdPetContatoRN();
          $objContatoDTO = new ContatoDTO();
          $objContatoDTO->setDblCpf(InfraUtil::retirarFormatacao($_POST['txtNumeroCpfResponsavel']));
          $objContatoDTO->setNumIdTipoContato($idTipoContatoUsExt);
          $objContatoDTO->retNumIdContato();
          $arrObjContatoDTO = $objContatoRN->listarRN0325($objContatoDTO);

          if(count($arrObjContatoDTO) > 0) {
            $_POST['hdnIdContatoNovo'] = $arrObjContatoDTO[0]->getNumIdContato();
          }

          $objMdPetVinculoDTO = new MdPetVinculoDTO();
          $objMdPetVinculoDTO->setNumIdMdPetVinculo($idVinculo);
          $objMdPetVinculoDTO->setDistinct(true);
          $objMdPetVinculoDTO->retNumIdContato();
          $objMdPetVinculoDTO->retStrRazaoSocialNomeVinc();
          $objMdPetVinculoDTO->retStrNomeContatoRepresentante();
          $objMdPetVinculoDTO->retStrCpfContatoRepresentante();
          $objMdPetVinculoDTO->retDblCNPJ();

//          $objMdPetVinculoDTO->retNumIdMdPetVinculo();
//          $objMdPetVinculoDTO->retNumIdUfContatoPJ();
//          $objMdPetVinculoDTO->retNumIdCidadeContatoPJ();
//          $objMdPetVinculoDTO->retTodos(true);

          $objMdPetVinculoRN = new MdPetVinculoRN();
          $arrObjMdPetVinculoDTO = $objMdPetVinculoRN->listar($objMdPetVinculoDTO);

          if (!empty($arrObjMdPetVinculoDTO)){

            $_POST['txtNumeroCnpj'] = InfraUtil::formatarCpfCnpj($arrObjMdPetVinculoDTO[0]->getDblCNPJ());
            $_POST['txtNomeResponsavelAntigo'] = $arrObjMdPetVinculoDTO[0]->getStrNomeContatoRepresentante();
            $_POST['txtCpfResponsavelAntigo'] = $arrObjMdPetVinculoDTO[0]->getStrCpfContatoRepresentante();
            $razaoSocial = $arrObjMdPetVinculoDTO[0]->getStrRazaoSocialNomeVinc();
            $nomeContato = $_POST['hdnNomeNovo'];

//	   		$cpfResponsavel = InfraUtil::formatarCpf($_POST['txtNumeroCpfResponsavel']);
            $numero = '0';
//            $arrObjMdPetVinculoDTO[0]->getNumIdUfContatoPJ();
//            $arrObjMdPetVinculoDTO[0]->getNumIdCidadeContatoPJ();

            $objMdPetContatoRN = new MdPetContatoRN();

            $objContatoRN = new MdPetContatoRN();
            $objContatoDTO = new ContatoDTO();
            $objContatoDTO->setNumIdContato($arrObjMdPetVinculoDTO[0]->getNumIdContato());
            $objContatoDTO->retNumIdContato();
            $objContatoDTO->retStrComplemento();
            $objContatoDTO->retStrCep();
            $objContatoDTO->retStrEndereco();
            $objContatoDTO->retStrBairro();

            $arrObjContatoDTO = $objContatoRN->listarRN0325($objContatoDTO);
            if(count($arrObjContatoDTO) > 0) {
	           $complemento = $arrObjContatoDTO[0]->getStrComplemento();
	           $cep = $arrObjContatoDTO[0]->getStrCep();
	           $logradouro = $arrObjContatoDTO[0]->getStrEndereco();
	           $bairro = $arrObjContatoDTO[0]->getStrBairro();
	           $_POST['hdnInformacaoPj'] = $razaoSocial.'±'.$nomeContato.'±±±'.$numero.'±'.$complemento.'±'.$cep.'±'.$logradouro.'±'.$bairro;
            }
          }

          $_POST['pwdsenhaSEI'] = $_POST['pwdSenha'];
          $dados = $_POST;

          $idRecibo = '';
          $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
          $idRecibo = $objMdPetVincRepresentantRN->realizarProcessosAlteracaoResponsavelLegal($dados);

          //$url = "controlador_externo.php?id_md_pet_rel_recibo_protoc=" . $idRecibo ."&acao=md_pet_usu_ext_recibo_listar&acao_origem=md_pet_usu_ext_recibo_consultar";

          echo "<script>";
          echo "parent.infraFecharJanelaModal();";
          echo "parent.location.reload();";
          echo "</script>";
          die();
          //$bolAssinaturaOK = true;
        }catch(Exception $e){
          PaginaSEI::getInstance()->processarExcecao($e, true);
        }

      }

    case 'md_pet_vinc_suspender_restabelecer_concluir':

      $objInfraParametro=new InfraParametro(BancoSEI::getInstance());
      $tipoAssinatura=$objInfraParametro->getValor('SEI_TIPO_ASSINATURA_INTERNA');

      $strTitulo = 'Assinatura de Documento';

      switch ($tipoAssinatura){
        case 1:
          $bolPermiteAssinaturaCertificado=true;
          $bolPermiteAssinaturaLogin=true;
          break;
        case 2:
          $bolPermiteAssinaturaLogin=true;
          break;
        case 3:
          $bolPermiteAssinaturaCertificado=true;
      }

      $objAssinaturaDTO = new AssinaturaDTO();
      $objAssinaturaDTO->setStrStaFormaAutenticacao($_POST['hdnFormaAutenticacao']);

      if (!isset($_POST['hdnFlagAssinatura'])){
        $objAssinaturaDTO->setNumIdOrgaoUsuario(SessaoSEI::getInstance()->getNumIdOrgaoUsuario());
      }else{
        $objAssinaturaDTO->setNumIdOrgaoUsuario($_POST['selOrgao']);
      }

      $objAssinaturaDTO->setNumIdUsuario($_POST['hdnIdUsuario']);
      $objAssinaturaDTO->setStrSenhaUsuario($_POST['pwdSenha']);

      //$objAssinaturaDTO->setStrCargoFuncao(PaginaSEI::getInstance()->recuperarCampo('selCargoFuncao'));

      $objInfraDadoUsuario = new InfraDadoUsuario(SessaoSEI::getInstance());

      $strChaveDadoUsuarioAssinatura = 'ASSINATURA_CARGO_FUNCAO_'.SessaoSEI::getInstance()->getNumIdUnidadeAtual();

      if (!isset($_POST['selCargoFuncao'])){
        $objAssinaturaDTO->setStrCargoFuncao($objInfraDadoUsuario->getValor($strChaveDadoUsuarioAssinatura));
      }else{
        $objAssinaturaDTO->setStrCargoFuncao($_POST['selCargoFuncao']);

        if ($objAssinaturaDTO->getNumIdUsuario()==SessaoSEI::getInstance()->getNumIdUsuario()) {
          $objInfraDadoUsuario->setValor($strChaveDadoUsuarioAssinatura, $_POST['selCargoFuncao']);
        }
      }

      if ($_POST['hdnFormaAutenticacao'] != null){
        try{

          $objMdPetProcessoRN = new MdPetProcessoRN();

          $objInfraSip = new InfraSip(SessaoSEI::getInstance());
          $objInfraSip->autenticar(SessaoSEI::getInstance()->getNumIdOrgaoUsuario(), null, SessaoSEI::getInstance()->getStrSiglaUsuario(), $_POST['pwdSenha']);

          // POST

          $objMdPetVinculoRepresentRN = new MdPetVincRepresentantRN();
          $arrParam = array();
          $arrParam[] = $idVinculo;

          $_POST['pwdsenhaSEI'] = $_POST['pwdSenha'];
          $dados = $_POST;

          $params['dados'] = $dados;

          $objMdPetVinculoRepresentRN->realizarProcessoSuspensaoRestabelecimentoVinculo($params);
          $urlAssinada = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_adm_vinc_listar&acao_origem='.$_GET['acao']);
          echo "<script>";
          echo " window.parent.location = '" . $urlAssinada . "';";
          echo " window.parent.close();";
          echo " window.close();";
          echo "</script>";
          die();
//          $bolAssinaturaOK = true;
        }catch(Exception $e){
//          PaginaSEI::getInstance()->processarExcecao($e, true);
        }

      }
      break;

    default:
      throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
  }

  $arrComandos = array();


//  if ($numRegistros) {
//    if ($bolPermiteAssinaturaCertificado && $objAssinaturaDTO->getStrStaFormaAutenticacao() == AssinaturaRN::$TA_CERTIFICADO_DIGITAL){
//      $arrComandos[] = '<button type="button" accesskey="A" onclick="assinarCertificadoDigital();" id="btnAssinar" name="btnAssinar" value="Assinar" class="infraButton" style="visibility:hidden">&nbsp;<span class="infraTeclaAtalho">A</span>ssinar&nbsp;</button>';
//    }else if ($bolPermiteAssinaturaLogin ) {
      $arrComandos[] = '<button type="button" accesskey="A" onclick="assinarSenha();" id="btnAssinar" name="btnAssinar" value="Assinar" class="infraButton">&nbsp;<span class="infraTeclaAtalho">A</span>ssinar&nbsp;</button>';
//    }
//  }

  if (!isset($_POST['hdnIdUsuario'])){
    $strIdUsuario = SessaoSEI::getInstance()->getNumIdUsuario();
    $strNomeUsuario = SessaoSEI::getInstance()->getStrNomeUsuario();
  }else{
    $strIdUsuario = $_POST['hdnIdUsuario'];
    $strNomeUsuario = $_POST['txtUsuario'];
  }

  if ($bolAssinaturaOK){
    $strDisplayAutenticacao = 'display:none;';
  }else{
    $strDisplayAutenticacao = '';
  }


  $strLinkAjaxUsuarios = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=usuario_assinatura_auto_completar');
  $strItensSelOrgaos = OrgaoINT::montarSelectSiglaRI1358('null','&nbsp;',$objAssinaturaDTO->getNumIdOrgaoUsuario());
  $strLinkAjaxContexto = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=contexto_carregar_nome');
  $strItensSelCargoFuncao = AssinanteINT::montarSelectCargoFuncaoUnidadeUsuarioRI1344('null','&nbsp;', $objAssinaturaDTO->getStrCargoFuncao(), $strIdUsuario);
  $strLinkAjaxCargoFuncao = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=assinante_carregar_cargo_funcao');

//  $strIdDocumentos = implode(',',$arrIdDocumentos);
//  $strHashDocumentos = md5($strIdDocumentos);

  $idVinculo = isset($_GET['idVinculo']) ? $_GET['idVinculo'] : $_POST['hdnIdVinculo'];


}catch(Exception $e){
  PaginaSEI::getInstance()->processarExcecao($e);
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(PaginaSEI::getInstance()->getStrNomeSistema().' - '.$strTitulo);
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
?>

#divContexto {<?=$strDisplayContexto?>}
#divAutenticacao {<?=$strDisplayAutenticacao?>}

#lblOu {<?=((PaginaSEI::getInstance()->isBolIpad() || PaginaSEI::getInstance()->isBolAndroid())?'visibility:hidden;':'')?>}
#lblCertificadoDigital {<?=((PaginaSEI::getInstance()->isBolIpad() || PaginaSEI::getInstance()->isBolAndroid())?'visibility:hidden;':'')?>}
#divAjudaAssinaturaDigital {display:inline;<?=((PaginaSEI::getInstance()->isBolIpad() || PaginaSEI::getInstance()->isBolAndroid())?'visibility:hidden;':'')?>}
#ancAjudaAssinaturaDigital {position:absolute;}

<?
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>

//<script>

var objAjaxContexto = null;
var objAutoCompletarUsuario = null;
var objAjaxCargoFuncao = null;
var bolAssinandoSenha = false;


function inicializar(){

  <?/*if ($numRegistros==0){?>
    alert('Nenhum documento informado.');
    return;
  <?}*/?>

  <?/*if ($bolDocumentoNaoEncontrado){?>
    alert('Documento não encontrado.');
    return;
  <?}*/?>

  //se realizou assinatura
  <?if ($bolAssinaturaOK){ ?>

    <?if ($objAssinaturaDTO->getStrStaFormaAutenticacao() == AssinaturaRN::$TA_CERTIFICADO_DIGITAL) {?>
        infraExibirAviso(false);
    <?} else {?>
       finalizar();
    <?}?>

    return;

  <?}else{?>

    if (document.getElementById('selCargoFuncao').options.length==2){
      document.getElementById('selCargoFuncao').options[1].selected = true;
    }

    objAjaxCargoFuncao = new infraAjaxMontarSelect('selCargoFuncao','<?=$strLinkAjaxCargoFuncao?>');
    //objAjaxCargoFuncao.mostrarAviso = true;
    //objAjaxCargoFuncao.tempoAviso = 2000;
    objAjaxCargoFuncao.prepararExecucao = function(){

      if (document.getElementById('hdnIdUsuario').value==''){
        return false;
      }

      return 'id_usuario=' + document.getElementById('hdnIdUsuario').value;
    }

    objAutoCompletarUsuario = new infraAjaxAutoCompletar('hdnIdUsuario','txtUsuario','<?=$strLinkAjaxUsuarios?>');
    //objAutoCompletarUsuario.maiusculas = true;
    //objAutoCompletarUsuario.mostrarAviso = true;
    //objAutoCompletarUsuario.tempoAviso = 1000;
    //objAutoCompletarUsuario.tamanhoMinimo = 3;
    objAutoCompletarUsuario.limparCampo = true;
    //objAutoCompletarUsuario.bolExecucaoAutomatica = false;

    objAutoCompletarUsuario.prepararExecucao = function(){

      if (!infraSelectSelecionado(document.getElementById('selOrgao'))){
        alert('Selecione um Órgão.');
        document.getElementById('selOrgao').focus();
        return false;
      }

      return 'id_orgao=' + document.getElementById('selOrgao').value + '&palavras_pesquisa='+document.getElementById('txtUsuario').value + '&inativos=0';
    };

    objAutoCompletarUsuario.processarResultado = function(id,descricao,complemento){
      if (id!=''){
        document.getElementById('hdnIdUsuario').value = id;
        document.getElementById('txtUsuario').value = descricao;
        objAjaxCargoFuncao.executar();
        window.status='Finalizado.';
      }
    }

    //infraSelecionarCampo(document.getElementById('txtUsuario'));

    <? if($bolPermiteAssinaturaLogin) { ?>
    document.getElementById('pwdSenha').focus();
    <?}?>
	$('#hdnNumeroSei').val(window.parent.document.getElementById('txtNumeroSei').value);
    if (window.parent.document.getElementById('txtCpfNovo')!=null){
    	$('#txtNumeroCpfResponsavel').val(window.parent.document.getElementById('txtCpfNovo').value);
    	$('#hdnNomeNovo').val(window.parent.document.getElementById('txtNomeNovo').value);
    	$('#hdnIdContato').val(window.parent.document.getElementById('hdnIdContato').value);
    }
    if (window.parent.document.getElementById('hdnOperacao')!=null){
        $('#hdnOperacao').val(window.parent.document.getElementById('hdnOperacao').value);
    }
  <?}?>
}

function OnSubmitForm() {

  if (!infraSelectSelecionado(document.getElementById('selOrgao'))){
    alert('Selecione um Órgão.');
    document.getElementById('selOrgao').focus();
    return false;
  }

  if (infraTrim(document.getElementById('hdnIdUsuario').value)==''){
    alert('Informe um Assinante.');
    document.getElementById('txtUsuario').focus();
    return false;
  }

  if (!infraSelectSelecionado(document.getElementById('selCargoFuncao'))){
    alert('Selecione um Cargo/Função.');
    document.getElementById('selCargoFuncao').focus();
    return false;
  }
  /*
  if ('<?=$numRegistros?>'=='0'){
    alert('Nenhum documento informado para assinatura.');
    return false;
  }
  */
  return true;
}

function trocarOrgaoUsuario(){
  objAutoCompletarUsuario.limpar();
  objAjaxContexto.executar();
  objAjaxCargoFuncao.executar();
}
<? if($bolPermiteAssinaturaLogin) { ?>
function assinarSenha(){
  if (infraTrim(document.getElementById('pwdSenha').value)==''){
    alert('Senha não informada.');
    document.getElementById('pwdSenha').focus();
  }else{
    document.getElementById('hdnFormaAutenticacao').value = '<?=AssinaturaRN::$TA_SENHA?>';
    if (OnSubmitForm()){
      infraExibirAviso(false);
      document.getElementById('frmAssinaturas').submit();
      return true;
    }
  }
  return false;
}

function tratarSenha(ev){
  if (!bolAssinandoSenha && infraGetCodigoTecla(ev)==13){
    bolAssinandoSenha = true;
    if (!assinarSenha()){
	    bolAssinandoSenha = false;
    }
  }
}
<? } ?>
<? if($bolPermiteAssinaturaCertificado) { ?>
function tratarCertificadoDigital(){
  document.getElementById('hdnFormaAutenticacao').value = '<?=AssinaturaRN::$TA_CERTIFICADO_DIGITAL?>';
  if (OnSubmitForm()){
    infraExibirAviso(false);
    document.getElementById('frmAssinaturas').submit();
  }
}
<? } ?>

function finalizar(){

  //se realizou assinatura
  <?if ($bolAssinaturaOK){ ?>

     window.opener.infraFecharJanelaModal();
     self.setTimeout('window.close()',500);

  <?}?>
}

//</script>
<?
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
?>

<form id="frmAssinaturas" method="post" onsubmit="return OnSubmitForm();" action="<?=SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao'].'&acao_retorno='.PaginaSEI::getInstance()->getAcaoRetorno().'&hash_documentos='.$strHashDocumentos.$strParametros)?>">

    <?
    //PaginaSEI::getInstance()->montarBarraLocalizacao($strTitulo);
    PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
    //PaginaSEI::getInstance()->montarAreaValidacao();
    ?>
    <div class="row">
        <div class="col-12 col-sm-10 col-md-8 col-lg-8 col-xl-8">
            <div id="divOrgao" class="infraAreaDados">
                <div class="form-group">
                    <label id="lblOrgao" for="selOrgao" accesskey="r" class="infraLabelObrigatorio">Ó<span class="infraTeclaAtalho">r</span>gão do Assinante:</label>
                    <select id="selOrgao" name="selOrgao" onchange="trocarOrgaoUsuario();" class="infraSelect form-control" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>">
                    <?=$strItensSelOrgaos?>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-sm-10 col-md-8 col-lg-8 col-xl-8">
            <div id="divUsuario" class="infraAreaDados">
                <div class="form-group">
                    <label id="lblUsuario" for="txtUsuario" accesskey="e" class="infraLabelObrigatorio">Assinant<span class="infraTeclaAtalho">e</span>:</label>
                    <input type="text" id="txtUsuario" name="txtUsuario" class="infraText form-control" value="<?=$strNomeUsuario?>" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
                    <input type="hidden" id="hdnIdUsuario" name="hdnIdUsuario" value="<?=$strIdUsuario?>" />
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-sm-10 col-md-8 col-lg-8 col-xl-8">
            <div id="divCargoFuncao" class="infraAreaDados">
                <div class="form-group">
                    <label id="lblCargoFuncao" for="selCargoFuncao" accesskey="F" class="infraLabelObrigatorio">Cargo / <span class="infraTeclaAtalho">F</span>unção:</label>
                    <select id="selCargoFuncao" name="selCargoFuncao" class="infraSelect form-control" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>">
                    <?=$strItensSelCargoFuncao?>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6 col-sm-5 col-md-6 col-lg-6 col-xl-6">
            <div id="divAutenticacao" class="infraAreaDados">
            <? if($bolPermiteAssinaturaLogin) { ?>
                <div class="form-group">
                    <label id="lblSenha" for="pwdSenha" accesskey="S" class="infraLabelRadio infraLabelObrigatorio" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"><span class="infraTeclaAtalho">S</span>enha</label>&nbsp;&nbsp;
                    <input type="password" id="pwdSenha" name="pwdSenha" autocomplete="off" class="infraText form-control" onkeypress="return tratarSenha(event);" value="" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />&nbsp;&nbsp;&nbsp;&nbsp;
                </div>
            <? }
                if($bolPermiteAssinaturaLogin && $bolPermiteAssinaturaCertificado) { ?>
                    <label id="lblOu" class="infraLabelOpcional" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>">ou</label>&nbsp;&nbsp;&nbsp;
            <? }
                if($bolPermiteAssinaturaCertificado) { ?>
                <label id="lblCertificadoDigital" onclick="tratarCertificadoDigital();" accesskey="" for="optCertificadoDigital" class="infraLabelRadio infraLabelObrigatorio" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"><?=((!$bolPermiteAssinaturaLogin)?(!$bolAutenticacao?'Assinar com ':'Autenticar com '):'')?>Certificado Digital</label>&nbsp;
                <div id="divAjudaAssinaturaDigital"><a id="ancAjudaAssinaturaDigital" href="<?=SessaoSEI::getInstance()->assinarLink('controlador.php?acao=assinatura_digital_ajuda&acao_origem='.$_GET['acao'])?>" target="janAjudaAssinaturaDigital" title="Instruções para Configuração da Assinatura Digital" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"><img src="<?=PaginaSEI::getInstance()->getDiretorioImagensLocal()?>/sei_informacao.png" class="infraImg" /></a></div>
            <? } ?>
            </div>
        </div>
    </div>
    <?=$strDivCertificacao?>
    <input type="hidden" id="hdnFormaAutenticacao" name="hdnFormaAutenticacao" value="" />
    <input type="hidden" id="hdnLinkRetorno" name="hdnLinkRetorno" value="<?=$strLinkRetorno?>" />
    <input type="hidden" id="hdnFlagAssinatura" name="hdnFlagAssinatura" value="1" />
    <input type="hidden" id="hdnIdDocumentos" name="hdnIdDocumentos" value="<?=$strIdDocumentos?>" />

    <input type="hidden" id="hdnOperacao" name="hdnOperacao" value="" />
    <input type="hidden" id="hdnIdVinculo" name="hdnIdVinculo" value="<?=$idVinculo?>" />
    <input type="hidden" id="hdnIdContato" name="hdnIdContato" value="<?=$idContato?>" />
    <input type="hidden" id="txtNumeroCpfResponsavel" name="txtNumeroCpfResponsavel" value="<?=$txtNumeroCpfResponsavel?>" />
    <input type="hidden" id="hdnNomeNovo" name="hdnNomeNovo" value="<?=$hdnNomeNovo?>" />
    <input type="hidden" id="hdnNumeroSei" name="hdnNumeroSei" value="<?=$numeroSEI?>" />
    <?
    //PaginaSEI::getInstance()->fecharAreaDados();
    //PaginaSEI::getInstance()->montarAreaDebug();
    //PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
    ?>
</form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>
