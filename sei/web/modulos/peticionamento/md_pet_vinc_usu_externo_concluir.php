<?
/**
 * ANATEL
 *
 * 25/06/2016 - criado por marcelo.bezerra@cast.com.br - CAST
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
  SessaoSEIExterna::getInstance()->validarLink();
  SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);

  //=====================================================
  //INICIO - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
  //=====================================================

  //preenche a combo Fun��o
  $objMdPetCargoRN            = new MdPetCargoRN();
  $arrObjCargoDTO             = $objMdPetCargoRN->listarDistintos();
  $objMdPetVinculoUsuExtRN    = new MdPetVinculoUsuExtRN();
  $objMdPetVinculoRepresentRN = new MdPetVincRepresentantRN();
  $strLinkAjaxVerificarSenha = SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_validar_assinatura');

  //=====================================================
  //FIM - VARIAVEIS PRINCIPAIS E LISTAS DA PAGINA
  //=====================================================
  switch ($_GET['acao']) {

    case 'md_pet_usuario_ext_vinc_pj_concluir_cad':

      $objMdPetProcessoRN = new MdPetProcessoRN();
      $strTitulo = 'Concluir Peticionamento - Assinatura Eletr�nica';

      if (isset($_POST['pwdsenhaSEI'])) {
        $arrParam = array();
        $arrParam['pwdsenhaSEI'] = $_POST['pwdsenhaSEI'];
        $objMdPetProcessoRN->validarSenha($arrParam);
        $params['pwdsenhaSEI'] = '***********';
        $_POST['pwdsenhaSEI'] = '***********';
        // organizando tabela procuradores
        if ($_POST['hdnTbUsuarioProcuracao']!=''){
            $dadosProcuradorTemp = PaginaSEIExterna::getInstance()->getArrItensTabelaDinamica($_POST['hdnTbUsuarioProcuracao']);
            $dadosProcurador = array();
            for ($i=0;$i<count($dadosProcuradorTemp);$i++){
                $procurador = array();
                $procurador[0] = $dadosProcuradorTemp[$i][0];
                $procurador[1] = $dadosProcuradorTemp[$i][2];
                $procurador[2] = '';
                $procurador[3] = $dadosProcuradorTemp[$i][1];
                $dadosProcurador[] = $procurador;
            }
            $_POST['hdnTbUsuarioProcuracao'] = PaginaSEIExterna::getInstance()->gerarItensTabelaDinamica($dadosProcurador);
        }

        $dados = $_POST;
        $idContato = $objMdPetVinculoUsuExtRN->salvarDadosContatoCnpj($dados);
        $dados['idContato'] = $idContato;
        
        $reciboGerado = $objMdPetVinculoUsuExtRN->gerarProcedimentoVinculo($dados);
//        $vincularProcuracao = $objMdPetVinculoUsuExtRN->gerarProcuracao($_POST);

//        var_dump($reciboGerado);
//        die;

        $idRecibo = $reciboGerado->getNumIdReciboPeticionamento();

	// Tempor�rios apagando
        $arquivos_enviados = PaginaSEIExterna::getInstance()->getArrItensTabelaDinamica($_POST['hdnTbDocumento']);

        foreach ($arquivos_enviados as $arquivo_enviado) {
            unlink(DIR_SEI_TEMP.'/'.$arquivo_enviado[7]);
        }

        //executar javascript para fechar janela filha e redirecionar janela pai para a tela de detalhes do recibo que foi gerado
//        $url = "controlador_externo.php?id_md_pet_rel_recibo_protoc=" . $idRecibo ."&acao=md_pet_usu_ext_recibo_listar&acao_origem=md_pet_usu_ext_recibo_consultar";
//          $url = "controlador_externo.php?id_md_pet_rel_recibo_protoc=" . $idRecibo ."&acao=md_pet_usu_ext_recibo_listar&acao_origem=md_pet_usu_ext_recibo_consultar&id_orgao_acesso_externo=0&infra_hash=6f611cf79074bde0d5430b580497d37a"

        $url = "controlador_externo.php?id_md_pet_rel_recibo_protoc=" . $idRecibo ."&acao=md_pet_usu_ext_recibo_listar&acao_origem=md_pet_usu_ext_recibo_consultar";
        $urlAssinada = SessaoSEIExterna::getInstance()->assinarLink( $url );

//          //removendo atributos da sessao
//          if( SessaoSEIExterna::getInstance()->isSetAtributo('docPrincipalConteudoHTML') ){
//              SessaoSEIExterna::getInstance()->removerAtributo('docPrincipalConteudoHTML');
//          }
//
//          if( SessaoSEIExterna::getInstance()->isSetAtributo('arrIdAnexoPrincipal') ){
//              SessaoSEIExterna::getInstance()->removerAtributo('arrIdAnexoPrincipal');
//          }
//
//          if( SessaoSEIExterna::getInstance()->isSetAtributo('arrIdAnexoEssencial') ){
//              SessaoSEIExterna::getInstance()->removerAtributo('arrIdAnexoEssencial');
//          }
//
//          if( SessaoSEIExterna::getInstance()->isSetAtributo('arrIdAnexoComplementar') ){
//              SessaoSEIExterna::getInstance()->removerAtributo('arrIdAnexoComplementar');
//          }
//
//          if( SessaoSEIExterna::getInstance()->isSetAtributo('idDocPrincipalGerado') ){
//              SessaoSEIExterna::getInstance()->removerAtributo('idDocPrincipalGerado');
//          }

            echo "<script>";
            echo "window.opener.location = '" . $urlAssinada . "';";
            echo " window.opener.focus();";
            echo " window.close();";
            echo "</script>";
            die;
      }

      break;

      case 'md_pet_usuario_ext_vinc_pj_concluir_alt':
          $objMdPetProcessoRN = new MdPetProcessoRN();
          $strTitulo = 'Concluir Peticionamento - Assinatura Eletr�nica';
          
          if (isset($_POST['pwdsenhaSEI'])) {
              $arrParam = array();
              $arrParam['pwdsenhaSEI'] = $_POST['pwdsenhaSEI'];
              $dados = $_POST;
              
              $objMdPetProcessoRN->validarSenha($arrParam);
              $dados       = $_POST;
              $dados['isAlteracaoCrud'] = true;
              $idContato   = $objMdPetVinculoUsuExtRN->salvarDadosContatoCnpj($dados);
              $dados['idContato'] = $idContato;

              $url = '';

              $idRecibo = '';
              $idRecibo = $objMdPetVinculoRepresentRN->realizarProcessosAlteracaoResponsavelLegal($dados);

              $url = "controlador_externo.php?id_md_pet_rel_recibo_protoc=" . $idRecibo ."&acao=md_pet_usu_ext_recibo_listar&acao_origem=md_pet_usu_ext_recibo_consultar";

              //  }else{
              //    $url = "controlador_externo.php?acao=md_pet_usu_ext_recibo_listar&acao_origem=md_pet_usu_ext_recibo_consultar";
              //}


              $urlAssinada = SessaoSEIExterna::getInstance()->assinarLink( $url );

              echo "<script>";
              echo "window.opener.location = '" . $urlAssinada . "';";
              echo " window.opener.focus();";
              echo " window.close();";
              echo "</script>";
              die;
          }

      break;

          default:
      throw new InfraException("A��o '" . $_GET['acao'] . "' n�o reconhecida.");
  }

} catch (Exception $e) {

  //removendo atributos da sessao
  //if( SessaoSEIExterna::getInstance()->isSetAtributo('docPrincipalConteudoHTML') ){
  //SessaoSEIExterna::getInstance()->removerAtributo('docPrincipalConteudoHTML');
  //}

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
        <label>A confirma��o de sua senha de acesso iniciar� o peticionamento e importa na aceita��o dos termos e
            condi��es que regem o processo eletr�nico, al�m do disposto no credenciamento pr�vio, e na assinatura dos
            documentos nato-digitais e declara��o de que s�o aut�nticos os digitalizados, sendo respons�vel civil, penal
            e administrativamente pelo uso indevido. Ainda, s�o de sua exclusiva responsabilidade: a conformidade entre
            os dados informados e os documentos; a conserva��o dos originais em papel de documentos digitalizados at�
            que decaia o direito de revis�o dos atos praticados no processo, para que, caso solicitado, sejam
            apresentados para qualquer tipo de confer�ncia; a realiza��o por meio eletr�nico de todos os atos e
            comunica��es processuais com o pr�prio Usu�rio Externo ou, por seu interm�dio, com a entidade porventura
            representada; a observ�ncia de que os atos processuais se consideram realizados no dia e hora do recebimento
            pelo SEI, considerando-se tempestivos os praticados at� as 23h59min59s do �ltimo dia do prazo, considerado
            sempre o hor�rio oficial de Bras�lia, independente do fuso hor�rio em que se encontre; a consulta peri�dica
            ao SEI, a fim de verificar o recebimento de intima��es eletr�nicas.</label>
    </p>

    <p>
        <label class="infraLabelObrigatorio">Usu�rio Externo:</label> <br/>
        <input type="text" name="loginUsuarioExterno" style="width: 60%;"
               value="<?= PaginaSEIExterna::tratarHTML(SessaoSEIExterna::getInstance()->getStrNomeUsuarioExterno()) ?>"
               readonly="readonly" id="loginUsuarioExterno" class="infraText" autocomplete="off" disabled/>
    </p>

    <p>
        <label class="infraLabelObrigatorio">Cargo/Fun��o:</label> <br/>
        <select id="selCargo" name="selCargo" class="infraSelect" style="width: 60%;">
            <option value="">Selecione Cargo/Fun��o</option>
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
    <input type="hidden" id="txtNumeroCnpjPai" name="txtNumeroCnpj"/>
    <input type="hidden" id="chkDeclaracaoPai" name="chkDeclaracao"/>
    <input type="hidden" id="slTipoInteressadoPai" name="slTipoInteressado"/>
    <input type="hidden" id="hdnInformacaoPjPai" name="hdnInformacaoPj"/>
    <input type="hidden" id="hdnTbDocumentoPai" name="hdnTbDocumento"/>
    <input type="hidden" id="hdnTbUsuarioProcuracaoPai" name="hdnTbUsuarioProcuracao"/>
    <input type="hidden" id="hdnStWebservicePai" name="hdnStaWebService"/>
    <input type="hidden" id="hdnIdVinculoPai" name="hdnIdVinculo"/>
    <input type="hidden" id="txtNumeroCpfResponsavelPai" name="txtNumeroCpfResponsavel"/>
    <input type="hidden" id="hdnIdContatoNovoPai" name="hdnIdContatoNovo"/>
    <input type="hidden" id="txtMotivo" name="txtMotivo" value=""/>

    <input type="submit" name="btSubMit" value="Salvar" style="display:none;"/>

</form>

<?
PaginaSEIExterna::getInstance()->fecharAreaDados();
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
?>
<script type="text/javascript">

    
    function assinar() {
        if (isValido()) {

            document.getElementById('txtNumeroCnpjPai').value = window.opener.document.getElementById('txtNumeroCnpj').value;

            var obj1 = window.opener.document.getElementById('chkDeclaracao');
            if(obj1) {
                document.getElementById('chkDeclaracaoPai').value = window.opener.document.getElementById('chkDeclaracao').value;
            }

            document.getElementById('hdnIdVinculoPai').value = window.opener.document.getElementById('hdnIdVinculo').value;
            document.getElementById('slTipoInteressadoPai').value = window.opener.document.getElementById('slTipoInteressado').value;
            document.getElementById('hdnInformacaoPjPai').value = window.opener.document.getElementById('hdnInformacaoPj').value;
            document.getElementById('hdnTbDocumentoPai').value = window.opener.document.getElementById('hdnTbDocumento').value;
//            document.getElementById('hdnTbUsuarioProcuracaoPai').value = window.opener.document.getElementById('hdnTbUsuarioProcuracao').value;
            document.getElementById('hdnStWebservicePai').value = window.opener.document.getElementById('hdnStaWebService').value;
            document.getElementById('hdnIdVinculoPai').value = window.opener.document.getElementById('hdnIdVinculo').value;
            document.getElementById('txtNumeroCpfResponsavelPai').value = window.opener.document.getElementById('txtNumeroCpfResponsavel').value;


            var obj2 = window.opener.document.getElementById('hdnIdContatoNovo');
            if(obj2) {
                document.getElementById('hdnIdContatoNovoPai').value = window.opener.document.getElementById('hdnIdContatoNovo').value;
            }

            var obj3 = window.opener.document.getElementById('txtMotivoAlteracaoRespLegal');
            if(obj3){
                document.getElementById('txtMotivo').value = window.opener.document.getElementById('txtMotivoAlteracaoRespLegal').value;
            }

            processando();
            document.getElementById('frmConcluir').submit();
            return true;
        }
        return false;
    }

function isValido() {

        var cargo = document.getElementById("selCargo").value;
        var senha = document.getElementById("pwdsenhaSEI").value;

        if (cargo == "") {
            alert('Por favor informe o Cargo/Fun��o.');
            document.getElementById("selCargo").focus();
            return false;
        } else if (senha == "") {
            alert('Por favor informe a Senha.');
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
        if (document.getElementById('selCargo')!=null){
            document.getElementById('selCargo').focus();
        }
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