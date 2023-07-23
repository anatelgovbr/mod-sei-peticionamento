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

  PaginaSEIExterna::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);

  switch ($_GET['acao']) {

    case 'md_pet_vinc_pessoa_cadastrar':
      $strTitulo = 'Alterar Respons�vel Legal por Pessoa Jur�dica';
      break;

    default:
      throw new InfraException("A��o '" . $_GET['acao'] . "' n�o reconhecida.");
  }

} catch (Exception $e) {
  PaginaSEIExterna::getInstance()->processarExcecao($e);
}

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
?>
    <style type="text/css">
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
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
$urlBaseLink = "";
$arrComandos = array();
$arrComandos[] = '<button type="button" accesskey="F" name="btnFechar" value="Fechar" onclick="window.close();" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';

PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaSEIExterna::getInstance()->abrirAreaDados('auto');
?>

<p><label class="infraLabelObrigatorio">Aten��o: </label><label>Somente por meio da tela anterior, acessando o bot�o "Novo Respons�vel Legal" por Usu�rio Externo que j� conste como Respons�vel Legal junto � Receita Federal que � poss�vel a altera��o do Respons�vel Legal da Pessoa Jur�dica </label><label id=lblPessoaJuridica></label><label>.</label></p>
<br/>

<script>
txtNumeroCnpj = window.opener.document.getElementById('txtNumeroCnpj').value;
txtRazaoSocial = window.opener.document.getElementById('txtRazaoSocial').value;
document.getElementById('lblPessoaJuridica').innerHTML = txtRazaoSocial + ' (CNPJ: ' + txtNumeroCnpj + ')'; 
</script>
<?php
//PaginaSEIExterna::getInstance()->montarAreaDebug();
PaginaSEIExterna::getInstance()->fecharAreaDados();
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();

?>