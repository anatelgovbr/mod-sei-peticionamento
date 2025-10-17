<?php
/**
 * Created by PhpStorm.
 * User: jhon.carvalho
 * Date: 21/05/2018
 * Time: 14:03
 */

try {

  require_once dirname(__FILE__) . '/../../SEI.php';

  session_start();

  //////////////////////////////////////////////////////////////////////////////
  InfraDebug::getInstance()->setBolLigado(false);
  InfraDebug::getInstance()->setBolDebugInfra(false);
  InfraDebug::getInstance()->limpar();
  //////////////////////////////////////////////////////////////////////////////
  PaginaSEIExterna::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);
  SessaoSEIExterna::getInstance()->validarLink();
  SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);

  //=====================================================
  //INICIO - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
  //=====================================================

  //preenche a combo Função
  $objMdPetCargoRN = new MdPetCargoRN();
  $arrObjCargoDTO = $objMdPetCargoRN->listarDistintos();
  $strLinkAjaxVerificarSenha = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_validar_assinatura');
  //=====================================================
  //FIM - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
  //=====================================================
  $strTitulo = 'Concluir Peticionamento - Assinatura Eletrônica';

  switch ($_GET['acao']) {


    case 'md_pet_vinc_pe_desvinculo_concluir':

        // Manipulacao para assinatura com Gov.br:
        $bolAssinaturaSso = (new InfraParametro(BancoSEI::getInstance()))->getValor('SEI_GOV_BR_EXTERNO_ASSINATURA', false, '0');
        $isNivelAlto = SessaoSEIExterna::getInstance()->getBolLoginSso() && !InfraString::isBolVazia(SessaoSEIExterna::getInstance()->getStrStaNivelConfiabilidadeSso()) && in_array(SessaoSEI::getInstance()->getStrStaNivelConfiabilidadeSso(), [InfraSip::$SSO_NIVEL_CONFIABILIDADE_OURO, InfraSip::$SSO_NIVEL_CONFIABILIDADE_PRATA]);

        if($bolAssinaturaSso){
            $agrupador = sha1(time());
            $objInfraSip = new InfraSip(SessaoSEIExterna::getInstance());
            $strUrlAssinaturaSso = $objInfraSip->obterUrlAssinaturaSso() . "?token=" . $agrupador . "&id_sistema=" . SessaoSEIExterna::getInstance()->getNumIdSistema() . "&id_login_sso=" . SessaoSEIExterna::getInstance()->getStrIdLoginSso();
            $strLinkVerificacaoAssinatura = SessaoSEIExterna::getInstance()->assinarLink( 'controlador_ajax_externo.php?acao_ajax=assinatura_verificar_confirmacao&agrupador=' . $agrupador );
        }

        if(MdPetUsuarioExternoRN::usuarioSsoSemSenha()){
            (new InfraException())->lancarValidacao("Você ainda não possui uma senha registrada no sistema.\nPara assinatura com senha acesse a opção Gerar Senha no menu.", InfraPagina::$TIPO_MSG_AVISO);
        }
        // Final da manipulacao para assinatura com Gov.br.

      $objMdPetProcessoRN = new MdPetProcessoRN();

      if (isset($_POST['pwdsenhaSEI'])) {
          if(isset($_POST['txtJustificativa']) && $_POST['txtJustificativa']!=''){

              if($_POST['hdnTpDocumento'] == 'revogar'){
                  $arrCampos = [
                      'txtJustificativa' => "'Motivo da Revogação (constará no teor do documento de Revogação que será gerado)'"
                  ];
              } else {
                  $arrCampos = [
                      'txtJustificativa' => "'Motivo da Renúncia (constará no teor do documento de Renúncia que será gerado)'"
                  ];
              }

              $objInfraException = new InfraException();

              if(PeticionamentoIntegracao::validarXssFormulario($_POST, $arrCampos, $objInfraException)) {
                  $objInfraException->lancarValidacoes();
              }

              $arrParam = array();
              $arrParam['pwdsenhaSEI'] = $_POST['pwdsenhaSEI'];
              $objMdPetProcessoRN->validarSenha($arrParam);
              $params['pwdsenhaSEI'] = '***********';
              $_POST['pwdsenhaSEI'] = '***********';
              $dados= $_POST;

              $idsUsuarios=$_POST['hdnIdUsuario'];
              $id = explode('+',$idsUsuarios);

              $idContatoVinc = $_POST['selPessoaJuridica'];
              $dados['idContato']= $idContatoVinc;
              $dados['chkDeclaracao'] = 'S';
              $dados['idContatoExterno']= $_POST['hdnIdContExterno'];
              $dados['tpVinc'] = $_POST['hdnTpVinculo'];
              $dados['tpProc'] = $_POST['hdnTpProcuracao'];

              $mdPetVinUsuExtProcRN = new MdPetVinUsuExtProcRN();
              $mdPetVinUsuExtProc = $mdPetVinUsuExtProcRN->gerarProcedimentoVinculoProcuracaoMotivo($dados);
              $motivoOk = $mdPetVinUsuExtProc;

              $urlAssinada = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinc_usu_ext_pe_listar&acao_origem='.$_GET['acao'].'#ID-'.$_POST['hdnIdVinculacao']);

              echo "<script>";
              echo " window.parent.location = '" . $urlAssinada . "';";
              echo "</script>";
              die;
          }
      }

      break;

    default:
      throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
  }

} catch (Exception $e) {

  if (SessaoSEIExterna::getInstance()->isSetAtributo('arrIdAnexoPrincipal')) {
    SessaoSEIExterna::getInstance()->removerAtributo('arrIdAnexoPrincipal');
  }

  if (SessaoSEIExterna::getInstance()->isSetAtributo('arrIdAnexoEssencial')) {
    SessaoSEIExterna::getInstance()->removerAtributo('arrIdAnexoEssencial');
  }

  if (SessaoSEIExterna::getInstance()->isSetAtributo('arrIdAnexoComplementar')) {
    SessaoSEIExterna::getInstance()->removerAtributo('arrIdAnexoComplementar');
  }

  if (SessaoSEIExterna::getInstance()->isSetAtributo('idDocPrincipalGerado')) {
    SessaoSEIExterna::getInstance()->removerAtributo('idDocPrincipalGerado');
  }

  PaginaSEIExterna::getInstance()->processarExcecao($e);
}

$hashAnexo = "";
$idAnexo = "";

PaginaSEIExterna::getInstance()->montarDocType();
PaginaSEIExterna::getInstance()->abrirHtml();
PaginaSEIExterna::getInstance()->abrirHead();
PaginaSEIExterna::getInstance()->montarMeta();
PaginaSEIExterna::getInstance()->montarTitle(':: ' . PaginaSEIExterna::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaSEIExterna::getInstance()->montarStyle();
PaginaSEIExterna::getInstance()->abrirStyle();
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
PaginaSEIExterna::getInstance()->fecharJavaScript();
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');

$arrComandos = array();
$arrComandos[] = '<button tabindex="-1" type="button" accesskey="a" name="Assinar" value="Assinar" onclick="assinar()" class="infraButton"><span class="infraTeclaAtalho">A</span>ssinar</button>';
$arrComandos[] = '<button tabindex="-1" type="button" accesskey="c" name="btnFechar" value="Fechar" onclick="infraFecharJanelaModal()" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
?>
<form id="frmConcluir" method="post" onsubmit="return assinar();"
      action="<?= PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'])) ?>">
  <?
  PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
  PaginaSEIExterna::getInstance()->abrirAreaDados('auto');
  ?>

    <div class="row">
        <div class="col-12">
            <p class="text-justify">A confirmação de sua senha importa na aceitação dos termos e condições que regem o processo eletrônico, além do disposto no credenciamento prévio, e na assinatura dos documentos nato-digitais e declaração de que são autênticos os digitalizados, sendo responsável civil, penal e administrativamente pelo uso indevido. Ainda, são de sua exclusiva responsabilidade: a conformidade entre os dados informados e os documentos; a conservação dos originais em papel de documentos digitalizados até que decaia o direito de revisão dos atos praticados no processo, para que, caso solicitado, sejam apresentados para qualquer tipo de conferência; a realização por meio eletrônico de todos os atos e comunicações processuais com o próprio Usuário Externo ou, por seu intermédio, com a entidade porventura representada; a observância de que os atos processuais se consideram realizados no dia e hora do recebimento pelo SEI, considerando-se tempestivos os praticados até as 23h59min59s do último dia do prazo, considerado sempre o horário oficial de Brasília, independente do fuso horário em que se encontre; a consulta periódica ao SEI, a fim de verificar o recebimento de intimações eletrônicas.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-sm-10 col-md-8 col-lg-8 col-xl-8">
            <div class="form-group">
                <label class="infraLabelObrigatorio">Usuário Externo:</label>
                <input type="text" name="loginUsuarioExterno"
                    value="<?= PaginaSEIExterna::tratarHTML(SessaoSEIExterna::getInstance()->getStrNomeUsuarioExterno()) ?>"
                    readonly="readonly" id="loginUsuarioExterno" class="infraText form-control" autocomplete="off" disabled />
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12 col-sm-10 col-md-8 col-lg-8 col-xl-8">
            <div class="form-group">
                <label class="infraLabelObrigatorio">Cargo/Função:</label>
                <select id="selCargo" name="selCargo" class="infraSelect form-select">
                    <option value="">Selecione Cargo/Função</option>
                    <? foreach ($arrObjCargoDTO as $expressao => $cargo): ?>
                    <option value="<?= $cargo ?>" <?= $_POST['selCargo'] == $cargo ? 'selected="selected"' : '' ?>><?= $expressao ?></option>
                    <? endforeach ?>
                </select>
            </div>
        </div>
    </div>
    <div class="row my-3">

        <div class="col-6 col-sm-5 col-md-3 col-lg-3 col-xl-3">
            <div class="form-group">
                <label class="infraLabelObrigatorio"><span class="infraTeclaAtalho">S</span>enha:</label>
                <input type="password" name="pwdsenhaSEI" id="pwdsenhaSEI" class="infraText form-control" autocomplete="off"/>
            </div>
        </div>

        <?php if($bolAssinaturaSso && 1==2): ?>
            <div class="col-6 col-sm-4 col-md-4 col-lg-4 col-xl-4">
                <label id="lblOu" class="infraLabelOpcional" tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>">ou</label>&nbsp;&nbsp;&nbsp;&nbsp;
                <a href="#" id="ancAssinaturaSso" class="d-inline-block mt-3" onclick="assinarSso();" tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>">
                    <img id="imgAssinarGovBr" src="imagens/assinatura-gov-br.png" title="Assinar com gov.br" style="width:150px" />
                </a>&nbsp;
            </div>
        <?php endif; ?>
                
    </div>

    <!--  Campos Hidden para preencher com valores da janela pai -->
    <input type="hidden" id="hdnCpfProcuradorPai" name="hdnCpfProcurador">
    <input type="hidden" id="hdnIdProcuracao" name="hdnIdProcuracao">
    <input type="hidden" id="hdnIdProcedimentoPai" name="hdnIdProcedimento">
    <input type="hidden" id="hdnIdVinculacaoPai" name="hdnIdVinculacao">
    <input type="hidden" id="hdnTpDocumentoPai" name="hdnTpDocumento">
    <input type="hidden" id="txtJustificativaPai" name="txtJustificativa">
    <input type="hidden" id="hdnTpVinculo" name="hdnTpVinculo">
    <input type="hidden" id="hdnTpProcuracao" name="hdnTpProcuracao">
    <input type="hidden" id="hdnIdContatoVinc" name="hdnIdContatoVinc">

    <input type="submit" name="btSubMit" value="Salvar" style="display:none;"/>

</form>

<?
PaginaSEIExterna::getInstance()->fecharAreaDados();
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
?>
<script type="text/javascript">

    function isValido() {

        var cargo = document.getElementById("selCargo").value;
        var senha = document.getElementById("pwdsenhaSEI").value;

        if (cargo == "") {
            alert('Favor informe o Cargo/Função.');
            document.getElementById("selCargo").focus();
            return false;
        } else if (senha == "") {
            alert('Favor informe a Senha.');
            document.getElementById("pwdsenhaSEI").focus();
            return false;
        } else {
            $.ajax({
                async: false,
                type: "POST",
                url: "<?= $strLinkAjaxVerificarSenha ?>",
                dataType: "json",
                data: {
                    strSenha: btoa(senha)
                },
                success: function (result) {
                    var strRetorno = result.responseText;
                    var retorno = strRetorno.split('"?>\n');
                    document.getElementById("pwdsenhaSEI").value = retorno[1];
                },
                error: function (msgError) {},
                complete: function (result) {
                    var strRetorno = result.responseText;
                    var retorno = strRetorno.split('"?>\n');
                    document.getElementById("pwdsenhaSEI").value = retorno[1];
                }
            });
            return true;
        }

    }

    function assinar() {
        if (isValido()) {
            $('#hdnCpfProcuradorPai').val(window.parent.document.getElementById('hdnCpfProcurador').value);
            $('#hdnIdProcuracao').val(window.parent.document.getElementById('hdnIdProcuracao').value);
            $('#hdnIdProcedimentoPai').val(window.parent.document.getElementById('hdnIdProcedimento').value);
            $('#hdnIdVinculacaoPai').val(window.parent.document.getElementById('hdnIdVinculacao').value);
            $('#hdnTpDocumentoPai').val(window.parent.document.getElementById('hdnTpDocumento').value);
            $('#txtJustificativaPai').val(window.parent.document.getElementById('txtJustificativa').value);
            $('#hdnTpVinculo').val(window.parent.document.getElementById('hdnTpVinculo').value);
            $('#hdnTpProcuracao').val(window.parent.document.getElementById('hdnTpProcuracao').value);
            $('#hdnIdContatoVinc').val(window.parent.document.getElementById('hdnIdContatoVinc').value);
            processando();
            document.getElementById('frmConcluir').submit();
            return true;
        }
        return false;
    }

    function callback(opt) {
        selInteressadosSelecionados + ', ';
    }

    //arguments: reference to select list, callback function (optional)
    function getSelectedOptions(sel, fn) {

        var opts = [], opt;

        // loop through options in select list
        for (var i = 0, len = sel.options.length; i < len; i++) {
            opt = sel.options[i];

            // check if selected
            if (opt.selected) {
                // add to array of option elements to return from this function
                opts.push(opt);

                // invoke optional callback function if provided
                if (fn) {
                    fn(opt);
                }
            }
        }

        // return array containing references to selected option elements
        return opts;
    }

    function inicializar() {
        infraEfeitoTabelas();
    }

    function fecharJanela() {
        if (window.opener != null && !window.opener.closed) {
            window.opener.focus();
        }

        window.close();
    }

    function exibirBotaoCancelarAviso() {

        var div = document.getElementById('divInfraAvisoFundo');

        if (div != null && div.style.visibility == 'visible') {

            var botaoCancelar = document.getElementById('btnInfraAvisoCancelar');

            if (botaoCancelar != null) {
                botaoCancelar.style.display = 'block';
            }
        }
    }

    function exibirAvisoEditor() {

        var divFundo = document.getElementById('divInfraAvisoFundo');

        if (divFundo == null) {
            divFundo = infraAviso(false, 'Processando...');
        } else {
            document.getElementById('btnInfraAvisoCancelar').style.display = 'none';
            document.getElementById('imgInfraAviso').src = '/infra_css/imagens/aguarde.gif';
        }

        if (INFRA_IE == 0 || INFRA_IE >= 7) {
            divFundo.style.position = 'fixed';
        }

        var divAviso = document.getElementById('divInfraAviso');

        divAviso.style.top = Math.floor(infraClientHeight() / 3) + 'px';
        divAviso.style.left = Math.floor((infraClientWidth() - 200) / 2) + 'px';
        divAviso.style.width = '200px';
        divAviso.style.border = '1px solid black';

        divFundo.style.width = screen.width * 2 + 'px';
        divFundo.style.height = screen.height * 2 + 'px';
        divFundo.style.visibility = 'visible';

    }

    function processando() {

        exibirAvisoEditor();
        timeoutExibirBotao = self.setTimeout('exibirBotaoCancelarAviso()', 30000);

        if (INFRA_IE > 0) {
            window.tempoInicio = (new Date()).getTime();
        } else {
            console.time('s');
        }

    }
</script>
