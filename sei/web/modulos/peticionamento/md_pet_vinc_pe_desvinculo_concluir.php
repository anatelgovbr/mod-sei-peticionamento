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

  //=====================================================
  //FIM - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
  //=====================================================

    if($_GET['tipoDocumento']=='revogar'){
        $strTitulo = 'Concluir Peticionamento - Assinatura Eletrônica';

    }else{
        $strTitulo = 'Concluir Peticionamento - Assinatura Eletrônica';

    }

  switch ($_GET['acao']) {


    case 'md_pet_vinc_pe_desvinculo_concluir':

      $objMdPetProcessoRN = new MdPetProcessoRN();

      if (isset($_POST['pwdsenhaSEI'])) {
          if(isset($_POST['txtJustificativa']) && $_POST['txtJustificativa']!=''){

              $arrParam = array();
              $arrParam['pwdsenhaSEI'] = $_POST['pwdsenhaSEI'];
              $objMdPetProcessoRN->validarSenha($arrParam);

              $dados= $_POST;

              $idsUsuarios=$_POST['hdnIdUsuario'];
              $id = explode('+',$idsUsuarios);

              $idContatoVinc = $_POST['selPessoaJuridica'];
              $dados['idContato']= $idContatoVinc;
              $dados['chkDeclaracao'] = 'S';
              $dados['idContatoExterno']= $_POST['hdnIdContExterno'];

              $mdPetVinUsuExtProcRN = new MdPetVinUsuExtProcRN();
              $mdPetVinUsuExtProc = $mdPetVinUsuExtProcRN->gerarProcedimentoVinculoProcuracaoMotivo($dados);
              $motivoOk = $mdPetVinUsuExtProc;

              $urlAssinada = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinc_usu_ext_pe_listar&acao_origem='.$_GET['acao']);

              echo "<script>";
              echo " window.opener.location = '" . $urlAssinada . "';";
              echo " window.opener.focus();";
              echo " window.opener.parent.opener.parent.location.reload();";
              echo " window.opener.close();";
              echo " window.close();";
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
$arrComandos[] = '<button tabindex="-1" type="button" accesskey="c" name="btnFechar" value="Fechar" onclick="fecharJanela()" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
?>
<form id="frmConcluir" method="post" onsubmit="return assinar();"
      action="<?= PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'])) ?>">
  <?
  PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
  PaginaSEIExterna::getInstance()->abrirAreaDados('auto');
  ?>

    <p>
        <label>A confirmação de sua senha de acesso iniciará o peticionamento e importa na aceitação dos termos e
            condições que regem o processo eletrônico, além do disposto no credenciamento prévio, e na assinatura dos
            documentos nato-digitais e declaração de que são autênticos os digitalizados, sendo responsável civil, penal
            e administrativamente pelo uso indevido. Ainda, são de sua exclusiva responsabilidade: a conformidade entre
            os dados informados e os documentos; a conservação dos originais em papel de documentos digitalizados até
            que decaia o direito de revisão dos atos praticados no processo, para que, caso solicitado, sejam
            apresentados para qualquer tipo de conferência; a realização por meio eletrônico de todos os atos e
            comunicações processuais com o próprio Usuário Externo ou, por seu intermédio, com a entidade porventura
            representada; a observância de que os atos processuais se consideram realizados no dia e hora do recebimento
            pelo SEI, considerando-se tempestivos os praticados até as 23h59min59s do último dia do prazo, considerado
            sempre o horário oficial de Brasília, independente do fuso horário em que se encontre; a consulta periódica
            ao SEI, a fim de verificar o recebimento de intimações eletrônicas.</label>
    </p>

    <p>
        <label class="infraLabelObrigatorio">Usuário Externo:</label> <br/>
        <input type="text" name="loginUsuarioExterno" style="width: 60%;"
               value="<?= PaginaSEIExterna::tratarHTML(SessaoSEIExterna::getInstance()->getStrNomeUsuarioExterno()) ?>"
               readonly="readonly" id="loginUsuarioExterno" class="infraText" autocomplete="off" disabled/>
    </p>

    <p>
        <label class="infraLabelObrigatorio">Cargo/Função:</label> <br/>
        <select id="selCargo" name="selCargo" class="infraSelect" style="width: 60%;">
            <option value="">Selecione Cargo/Função</option>
          <? foreach ($arrObjCargoDTO as $expressao => $cargo) {
            if ($_POST['selCargo'] != $cargo) {
              echo "<option value='" . $cargo . "'>";
            } else {
              echo "<option selected='selected' value='" . $cargo . "'>";
            }

            echo $expressao;
            echo "</option>";

          } ?>
        </select>
    </p>

    <p>
        <label class="infraLabelObrigatorio">Senha de Acesso ao SEI:</label> <br/>
        <input type="password" name="pwdsenhaSEI" id="pwdsenhaSEI" class="infraText" autocomplete="off" style="width: 60%;"/>
    </p>

    <!--  Campos Hidden para preencher com valores da janela pai -->
    <input type="hidden" id="hdnCpfProcuradorPai" name="hdnCpfProcurador">
    <input type="hidden" id="hdnIdProcuracao" name="hdnIdProcuracao">
    <input type="hidden" id="hdnIdProcedimentoPai" name="hdnIdProcedimento">
    <input type="hidden" id="hdnIdVinculacaoPai" name="hdnIdVinculacao">
    <input type="hidden" id="hdnTpDocumentoPai" name="hdnTpDocumento">
    <input type="hidden" id="txtJustificativaPai" name="txtJustificativa">


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
            return true;
        }

    }

    function assinar() {
        if (isValido()) {
            document.getElementById('hdnCpfProcuradorPai').value = window.opener.document.getElementById('hdnCpfProcurador').value;
            document.getElementById('hdnIdProcuracao').value = window.opener.document.getElementById('hdnIdProcuracao').value;
            document.getElementById('hdnIdProcedimentoPai').value = window.opener.document.getElementById('hdnIdProcedimento').value;
            document.getElementById('hdnIdVinculacaoPai').value = window.opener.document.getElementById('hdnIdVinculacao').value;
            document.getElementById('hdnTpDocumentoPai').value = window.opener.document.getElementById('hdnTpDocumento').value;
            document.getElementById('txtJustificativaPai').value = window.opener.document.getElementById('txtJustificativa').value;

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