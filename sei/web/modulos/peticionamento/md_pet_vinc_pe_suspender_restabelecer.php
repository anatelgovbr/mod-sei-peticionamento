<?
/**
 * ANATEL
 *
 * 23/06/2016 - criado por marcelo.bezerra@cast.com.br - CAST
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

  PaginaSEI::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);
  $strLinkAjaxValidacoesNumeroSEI = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_validar_num_sei');
  
  switch ($_GET['acao']) {

  	case 'md_pet_vinc_suspender_restabelecer':

      $strTitulo = $_GET['operacao']=='A'?'Restabelecer':'Suspender';
      $strTitulo .= ' Responsável Legal';
      $strAtenção = $_GET['operacao']=='A'?'O Restabelecimento':'A Suspensão';
      $janelaSelecaoPorNome = SessaoSEIExterna::getInstance()->getAtributo('janelaSelecaoPorNome');
      $strPrimeiroItemValor = 'null';
      $strPrimeiroItemDescricao = '&nbsp;';
      $strValorItemSelecionado = null;
      $strTipo = 'Cadastro';

      $operacao = isset($_GET['operacao']) ? $_GET['operacao'] : $_POST['hdnOperacao'];
      $idVinculo = isset($_GET['idVinculo']) ? $_GET['idVinculo'] : $_POST['hdnIdVinculo'];

      //Recuperar dados da Pessoa Juridica
      $objMdPetVinculoRN = new MdPetVinculoRN();
      $objMdPetVinculoDTO = new MdPetVinculoDTO();
      $objMdPetVinculoDTO->retNumIdMdPetVinculo();
      $objMdPetVinculoDTO->retDblCNPJ();
      $objMdPetVinculoDTO->retStrRazaoSocialNomeVinc();
      $objMdPetVinculoDTO->retNumIdContatoRepresentante();
      $objMdPetVinculoDTO->retStrCpfContatoRepresentante();
      $objMdPetVinculoDTO->retStrNomeContatoRepresentante();
      $objMdPetVinculoDTO->setNumIdMdPetVinculo($idVinculo);
      $objMdPetVinculoDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL);
      $objMdPetVinculoDTO->setStrStaResponsavelLegal('S');
      $objMdPetVinculoDTO->setDistinct(true);
      $objMdPetVinculoDTO = $objMdPetVinculoRN->consultar($objMdPetVinculoDTO);

      if (!empty($_POST)) {
      }
      break;

    default:
      throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
  }

} catch (Exception $e) {
  PaginaSEI::getInstance()->processarExcecao($e);
}

$hashAnexo = "";
$idAnexo = "";

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo);
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();

PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
require_once 'md_pet_vinc_pe_suspender_restabelecer_js.php';
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
    <style type="text/css">
        #txtInformativo {
            font-size: 13px;
        }

        #field1 {
            height: auto;
            width: 97%;
            margin-bottom: 11px;
        }

        #field2 {
            height: auto;
            width: 97%;
            margin-bottom: 11px;
        }

        .sizeFieldset {
            height: auto;
            width: 88%;
        }

        .fieldsetClear {
            border: none !important;
        }
    </style>
<?php
$urlBaseLink = "";
$arrComandos = array();
$arrComandos[] = '<button type="button" accesskey="s" name="Salvar" value="Salvar" onclick="salvar()" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
$arrComandos[] = '<button type="button" accesskey="c" name="btnFechar" value="Fechar" onclick="window.close();" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

//$strLinkBaseFormEdicao = 'controlador_externo.php?edicaoExibir=true&acao=' . $_GET['acao'];
$strLinkBaseFormEdicao = 'controlador.php?edicaoExibir=true&acao=' . $_GET['acao'];
$strLinkEdicaHash = PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink($strLinkBaseFormEdicao));
?>

    <!-- Formulario usado para viabilizar fluxo de edição de contato -->

    <form id="frmEdicaoAuxiliar"
          name="frmEdicaoAuxiliar"
          method="post"
          action="<?= $strLinkEdicaHash ?>">

      <?php
      PaginaSEI::getInstance()->abrirAreaDados('auto');
      ?>
        <br/><br/>
            <fieldset>
            <legend>ATENÇÃO</legend>
            <p id=txtInformativo><?
            if ($_GET['operacao']==MdPetVincRepresentantRN::$RP_ATIVO){?>
            O Restabelecimento do Responsável Legal deve ser motivado em documento específico, a ser indicado no campo abaixo. Todas as Procurações Eletrônicas geradas serão restabelecidas, o Usuário Externo será notificado e este ato constará no processo referente à Pessoa Jurídica.</p>
            <? }else{ ?>
            A Suspensão do Responsável Legal deve ser motivado em documento específico, a ser indicado no campo abaixo. Todas as Procurações Eletrônicas geradas pelo Responsável Legal serão suspensas, o Usuário Externo será notificado e este ato constará no processo referente à Pessoa Jurídica. A suspensão como Responsável Legal não impede o Usuário Externo de peticionar em próprio nome.</p>
            <? } ?>
            </fieldset>

            <br/><br/>
            <label id="lblCnpj" class="infraLabelObrigatorio" style="display: inline-block;  min-width:180px;  width: 16.5%">CNPJ:<br/>
                <input type="text" id="txtCnpj" name="txtCnpj"
                       class="infraText"
                       disabled="disabled"
                       value="<?= PaginaSEI::tratarHTML(InfraUtil::formatarCnpj($objMdPetVinculoDTO->getDblCNPJ())) ?>"
                       onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250"
                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                <br/><br/>
            </label>
            <label id="lblRazaoSocial" class="infraLabelObrigatorio" style="display: inline-block; min-width:120px;  width: 60%">Razão Social:<br/>
                <input type="text" id="txtRazaoSocial" name="txtRazaoSocial"
                       class="infraText" style="min-width:300px;  width: 123.5%"
                       disabled="disabled"
                       value="<?= PaginaSEI::tratarHTML($objMdPetVinculoDTO->getStrRazaoSocialNomeVinc()) ?>"
                       onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250"
                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                <br/><br/>
            </label>

            <br/>
            <label id="lblCnpj" class="infraLabelObrigatorio" style="display: inline-block; min-width:180px;  width: 16.5%">CPF do Responsável Legal:<br/>
                <input type="text" id="txtCpf" name="txtCpf"
                       class="infraText"
                       disabled="disabled"
                       value="<?= PaginaSEI::tratarHTML(InfraUtil::formatarCpf($objMdPetVinculoDTO->getStrCpfContatoRepresentante())) ?>"
                       onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250"
                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                <br/><br/>
            </label>
            <label id="txtRazaoSocial" class="infraLabelObrigatorio" style="display: inline-block; min-width:120px;  width: 60%">Nome do Responsável Legal:<br/>
                <input type="text" id="txtNome" name="txtNome"
                       disabled="disabled"
                       class="infraText" style="min-width:300px;  width: 123.5%"
                       value="<?= PaginaSEI::tratarHTML($objMdPetVinculoDTO->getStrNomeContatoRepresentante()) ?>"
                       onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250"
                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                <br/><br/>
            </label>

            <br/>
            <label class="infraLabelObrigatorio" style="display: inline-block; min-width:180px;  width: 16.5%">Número SEI da Justificativa:<br/>
                <input type="text" id="txtNumeroSei" name="txtNumeroSei" onblur="validarNumeroSEI();" 
                       class="infraText"
                       value=""
                       onkeypress="return infraMascaraTexto(this,event,250);" maxlength="10"
                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
            </label>
            <label class="infraLabelObrigatorio" style="display: inline-block; width: 60% "><br/>
                <input type="text" id="txtTipo" name="txtTipo" class="infraText" style="min-width:300px;  width: 123.5%"
                       readonly="readonly"
                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" value="<?= $txtTipo ?>"/>
            </label>
            <br/><br/>
            <input type="hidden" name="hdnIdVinculo" id="hdnIdVinculo" value="<?php echo $idVinculo?>"/>
            <input type="hidden" name="hdnOperacao" id="hdnOperacao" value="<?php echo $operacao?>"/>
            <input type="hidden" name="hdnIdContatoNovo" id="hdnIdContatoNovo"  value=""/>
            <input type="hidden" name="hdnIdContato" id="hdnIdContato"  value="<?= $objMdPetVinculoDTO->getNumIdContatoRepresentante() ?>"/>
            <input type="hidden" name="hdnIdDocumento" id="hdnIdDocumento"  value=""/>
    </form>

<?php
PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
//PaginaSEI::getInstance()->montarAreaDebug();
PaginaSEI::getInstance()->fecharAreaDados();
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>