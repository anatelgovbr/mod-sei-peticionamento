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

  SessaoSEI::getInstance()->validarLink();
  SessaoSEI::getInstance()->validarPermissao($_GET['acao']);
  
//  PaginaSEIExterna::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);
  $strLinkAjaxValidacoesNumeroSEI = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_validar_num_sei');
  
  switch ($_GET['acao']) {

    case 'md_pet_vinc_responsavel_cadastrar':
      $strTitulo = 'Alterar o Respons�vel Legal';
      $janelaSelecaoPorNome = SessaoSEIExterna::getInstance()->getAtributo('janelaSelecaoPorNome');
      $strPrimeiroItemValor = 'null';
      $strPrimeiroItemDescricao = '&nbsp;';
      $strValorItemSelecionado = null;
      $strTipo = 'Cadastro';

      $idVinculo = isset($_GET['idVinculo']) ? $_GET['idVinculo'] : $_POST['hdnIdVinculo'];

      //Recuperar dados para Pessoa Juridica.
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

      	$mdPetIntegracaoRN = new MdPetIntegracaoRN();
        $mdPetIntegracaoDTO = new MdPetIntegracaoDTO();
        $mdPetIntegracaoDTO->retNumIdMdPetIntegracao();
        $mdPetIntegracaoDTO->retStrEnderecoWsdl();
        $mdPetIntegracaoDTO->retStrOperacaoWsdl();
        $mdPetIntegracaoDTO->setNumIdMdPetIntegFuncionalid(MdPetIntegFuncionalidRN::$ID_FUNCIONALIDADE_CNPJ_RECEITA_FEDERAL);

        $arrFuncionalidadeCadastrada = $mdPetIntegracaoRN->consultar($mdPetIntegracaoDTO);

        $qtdFuncionalidade = $mdPetIntegracaoRN->contar($mdPetIntegracaoDTO);

        $cpf = InfraUtil::retirarFormatacao($_POST['txtCpfNovo']);

        if($qtdFuncionalidade > 0){
          $cpf = str_pad($cpf, '11', '0', STR_PAD_LEFT);
          $strUrlWebservice = $arrFuncionalidadeCadastrada->getStrEnderecoWsdl();
          $strMetodoWebservice = $arrFuncionalidadeCadastrada->getStrOperacaoWsdl();
          $cnpj = InfraUtil::retirarFormatacao($objMdPetVinculoDTO->getDblCNPJ());
          $cnpj =  str_pad(InfraUtil::retirarFormatacao($objMdPetVinculoDTO->getDblCNPJ()), 14, '0', STR_PAD_LEFT);

          $objMdPetSoapClienteRN = new MdPetSoapClienteRN($strUrlWebservice, 'wsdl');
          

         //Recuperando meses - alterado
    $objMdPetIntegParametroDTO = new MdPetIntegParametroDTO();
    $objMdPetIntegParametroDTO->retStrValorPadrao();
    $objMdPetIntegParametroDTO->setStrTpParametro(MdPetIntegParametroRN::$TIPO_PARAMETRO);
    $objMdPetIntegParametroDTO->retStrTpParametro();
    $objMdPetIntegParametroDTO->retStrNome();
    $objMdPetIntegParametroDTO->setNumIdMdPetIntegracao($objMdPetIntegracao->getNumIdMdPetIntegracao());
    $objMdPetIntegParametroRN = new MdPetIntegParametroRN();
    $arrObjMdPetIntegParametroRN = $objMdPetIntegParametroRN->consultar($objMdPetIntegParametroDTO);

    if(count($arrObjMdPetIntegParametroRN)){
      //Convertendo
      $mes = (int) $arrObjMdPetIntegParametroRN->getStrValorPadrao();

      $parametro = [
        $strMetodoWebservice => [
          'cnpj' => $cnpj
          , 'cpfUsuario' => $cpfUsuarioLogado
          , $arrObjMdPetIntegParametroRN->getStrNome() => $mes
        ]
      ];

    }else{

      $parametro = [
        $strMetodoWebservice => [
          'cnpj' => $cnpj
          , 'cpfUsuario' => $cpfUsuarioLogado
          
        ]
      ];

    }


          $consulta = $objMdPetSoapClienteRN->consultarWsdl($strMetodoWebservice, $parametro);
          $cpfResponsavelLegalReceita = $consulta['PessoaJuridica']['responsavel']['cpf'];


          /*if($cpfResponsavelLegalReceita != $cpf){
            echo "<script>";
            echo "alert('Em consulta junto � base de dados da Receita Federal do Brasil (RFB), verificou-se que o CPF informado n�o consta como Respons�vel Legal pelo presente CNPJ, o que impede a formaliza��o da nova vincula��o. � necess�rio entrar em contato com a RFB para verificar a situa��o cadastral das partes aqui referidas e regularizar eventuais pend�ncias');";
            echo "</script>";
            break;
          }*/

          $objMdPetVinculoUsuExtRN  = new MdPetVinculoUsuExtRN();

          $dados = $_POST;

          $dados['isAlteracaoCrud'] = true;
//          $idContato = $objMdPetVinculoUsuExtRN->salvarDadosContatoCnpj($dados);

          $objMdPetVinculoUsuExtRN = new MdPetVinculoUsuExtRN();
          $arrObjMdPetVinculoUsuExtRN = $objMdPetVinculoUsuExtRN->buscarVinculoDados($_POST['hdnIdVinculo']);

          if (count($arrObjMdPetVinculoUsuExtRN)>0){
              $dados['idContato'] = $arrObjMdPetVinculoUsuExtRN[0]->getNumIdContato();
          }

          $url = '';

          $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
          $idRecibo = '';
          $idRecibo = $objMdPetVincRepresentantRN->realizarProcessosAlteracaoResponsavelLegal($dados);
          
        }

        $contatoRN = new ContatoRN();
        $contatoDTO = new ContatoDTO();
        $contatoDTO->retNumIdContato();
        $contatoDTO->setDblCpf($cpf);

        $objMdPetContatoRN = new MdPetContatoRN();
        $idTipoContato = $objMdPetContatoRN->getIdTipoContatoUsExt();
        if(!empty($idTipoContato)){
            $contatoDTO->setNumIdTipoContato($idTipoContato);
        }
        $arrContato = $contatoRN->consultarRN0324($contatoDTO);

      }
      break;

    default:
      throw new InfraException("A��o '" . $_GET['acao'] . "' n�o reconhecida.");
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
require_once 'md_pet_vinc_responsavel_cadastro_js.php';
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
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
		
		#txtInformativo1{
        font-size: 13px;
		}
		
		#txtInformativo2{
           margin-top: 2%;
           font-size: 13px;
           margin-left: 2%;
           font-weight: bold;
		}
    </style>
<?php
$urlBaseLink = "";
$arrComandos = array();
$arrComandos[] = '<button type="button" accesskey="s" name="Salvar" value="Salvar" onclick="salvar()" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
$arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\''.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao']).'\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';


//$strLinkBaseFormEdicao = 'controlador_externo.php?edicaoExibir=true&acao=' . $_GET['acao'];
$strLinkBaseFormEdicao = 'controlador.php?edicaoExibir=true&acao=' . $_GET['acao'];
$strLinkEdicaHash = PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink($strLinkBaseFormEdicao));
?>

    <!-- Formulario usado para viabilizar fluxo de edi��o de contato -->

    <form id="frmEdicaoAuxiliar"
          name="frmEdicaoAuxiliar"
          method="post"
          action="<?= $strLinkEdicaHash ?>">

      <?php
      PaginaSEI::getInstance()->abrirAreaDados('auto');
      ?>
        <br/><br/>
        <fieldset  style="min-width:400px; width:70%">
            <legend>ATEN��O</legend>
            <p id="txtInformativo1">Os dados aqui dispostos dizem respeito ao Respons�vel Legal pela Pessoa Jur�dica indicada, conforme constante no SEI.</p>
            <p id="txtInformativo1">Informe abaixo o CPF do Usu�rio Externo que deseja indicar como novo Respons�vel Legal por esta Pessoa Jur�dica.</p>
        </fieldset> 
        <br/><br/>
        <label id="lblCnpj" class="infraLabelObrigatorio" style="display: inline-block; min-width:180px;  width: 16.5%">CNPJ:<br/>
            <input type="text" id="txtCnpj" name="txtCnpj"
                   class="infraText"
                   disabled="disabled"
                   value="<?= PaginaSEI::tratarHTML(InfraUtil::formatarCnpj($objMdPetVinculoDTO->getDblCNPJ())) ?>"
                   onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250"
                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
            <br/><br/>
        </label>
        <label id="lblRazaoSocial" class="infraLabelObrigatorio" style="display: inline-block; min-width:120px;  width: 60%">Raz�o Social:<br/>
            <input type="text" id="txtRazaoSocial" name="txtRazaoSocial"
                   class="infraText" style="min-width:300px;  width: 91.5%"
                   disabled="disabled"
                   value="<?= PaginaSEI::tratarHTML($objMdPetVinculoDTO->getStrRazaoSocialNomeVinc()) ?>"
                   onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250"
                   tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
            <br/><br/>
        </label>

            <br/>
            <label id="lblCnpj" class="infraLabelObrigatorio" style="display: inline-block;  min-width:180px;  width: 16.5%">CPF do Respons�vel Legal:<br/>
                <input type="text" id="txtCpf" name="txtCpf"
                       class="infraText" 
                       disabled="disabled"
                       value="<?= PaginaSEI::tratarHTML(InfraUtil::formatarCpf($objMdPetVinculoDTO->getStrCpfContatoRepresentante())) ?>"
                       onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250"
                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                <br/><br/>
            </label>
            <label id="txtRazaoSocial" class="infraLabelObrigatorio" style="display: inline-block;  min-width:120px;  width: 60%">Nome do Respons�vel Legal:<br/>
                <input type="text" id="txtNome" name="txtNome"
                       disabled="disabled"
                       class="infraText" style="min-width:300px;  width: 91.5%"
                       value="<?= PaginaSEI::tratarHTML($objMdPetVinculoDTO->getStrNomeContatoRepresentante()) ?>"
                       onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250"
                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                <br/><br/>
            </label>
            <br/>
            
            <label class="infraLabelObrigatorio" style="display: inline-block;  min-width:180px;  width: 16.5%">CPF do Usu�rio Externo:<br/>
                <input type="text" id="txtCpfNovo" name="txtCpfNovo" 
                       class="infraText"
                       onfocusout="buscarCpf(this)"
                       onkeypress="return infraMascaraCPF(this,event);" maxlength="14"
                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                <br/><br/>
            </label>
            <label class="infraLabelObrigatorio" style="display: inline-block; width: 60% ">Nome do Usu�rio Externo:<br/>
                <input type="text" id="txtNomeNovo" name="txtNomeNovo"
                       class="infraText" style="min-width:300px;  width: 91.5%"
                       readonly="readonly"
                       onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250"
                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                <br/><br/>
            </label>
            <br/>
            <label class="infraLabelObrigatorio" style="display: inline-block; min-width:180px;  width: 16.5%">N�mero SEI da Justificativa:<br/>
                <input type="text" id="txtNumeroSei" name="txtNumeroSei" onblur="validarNumeroSEI();" 
                       class="infraText"
                       value=""
                       onkeypress="return infraMascaraTexto(this,event,250);" maxlength="10"
                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
            </label>
            <label class="infraLabelObrigatorio" style="display: inline-block; width: 60% "><br/>
                <input type="text" id="txtTipo" name="txtTipo" class="infraText" style="min-width:300px;  width: 91.5%"
                       readonly="readonly"
                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" value="<?= $txtTipo ?>"/>
            </label>
            <br/><br/>
            <input type="hidden" name="hdnIdVinculo" id="hdnIdVinculo" value="<?php echo $idVinculo?>"/>
            <input type="hidden" name="hdnIdContatoNovo" id="hdnIdContatoNovo"  value=""/>
            <input type="hidden" name="hdnIdContato" id="hdnIdContato"  value="<?= $objMdPetVinculoDTO->getNumIdContatoRepresentante() ?>"/>
            <input type="hidden" name="hdnIdDocumento" id="hdnIdDocumento"  value=""/>
            <input type="hidden" name="hdnValidarNumSEI" id="hdnValidarNumSEI"  value=""/>
    </form>

<?php
PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
//PaginaSEI::getInstance()->montarAreaDebug();
PaginaSEI::getInstance()->fecharAreaDados();
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>