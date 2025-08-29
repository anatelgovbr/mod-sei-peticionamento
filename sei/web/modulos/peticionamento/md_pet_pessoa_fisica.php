<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 14/04/2008 - criado por mga
*
* Versão do Gerador de Código: 1.14.0
*
* Versão no CVS: $Id$
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

  PaginaSEI::getInstance()->prepararSelecao('md_pet_pessoa_fisica');

  SessaoSEI::getInstance()->validarPermissao($_GET['acao']);
  
  PaginaSEI::getInstance()->salvarCamposPost(array('selOrgao','txtSiglaUsuario','txtNomeUsuario', 'txtCpfUsuario'));

  switch($_GET['acao']){
    case 'md_pet_pessoa_fisica':
      $strTitulo = 'Selecionar Usuários Externos';
      break;

    default:
      throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
  }

  $arrComandos = array();
  $arrComandos[] = '<input type="button" id="btnToggleLote" value="Selecionar em Lote" class="infraButton btnToggleLote" style="cursor: pointer" />';
  $arrComandos[] = '<input type="submit" id="btnPesquisar" value="Pesquisar" class="infraButton submitSearchForm"/>';
  
  if ($_GET['acao'] == 'md_pet_pessoa_fisica'){
    $arrComandos[] = '<button type="button" accesskey="T" id="btnTransportarSelecao" value="Transportar" onclick="infraTransportarSelecao();" class="infraButton"><span class="infraTeclaAtalho">T</span>ransportar</button>';
  }
  $objInfraParametro = new InfraParametro(BancoSEI::getInstance());

  // Destinatarios em massa: Pega os Usuarios Externos que ja receberam o documento principal por intimacao:
  $arrContatosIntimados = [];
  $idDocumento = '';
  if(isset($_REQUEST['id_documento']) && !empty($_REQUEST['id_documento'])){
      $idDocumento = $_REQUEST['id_documento'];
      $arrContatosIntimados = array_column((new MdPetIntimacaoRN())->buscaIntimacoesCadastradas($_REQUEST['id_documento']), 'Id');
  }

  $objUsuarioDTO = new UsuarioDTO();
  $objUsuarioDTO->retNumIdUsuario();
  $objUsuarioDTO->retNumIdContato();
  $objUsuarioDTO->retStrSigla();
  $objUsuarioDTO->retStrNome();
  $objUsuarioDTO->retDblCpfContato();
  $objUsuarioDTO->retStrStaTipo();

  $strSiglaPesquisa = trim(PaginaSEI::getInstance()->recuperarCampo('txtSiglaUsuario'));
  if ($strSiglaPesquisa!==''){
    $objUsuarioDTO->setStrSigla($strSiglaPesquisa);
  }

  $strCpfPesquisa = trim(PaginaSEI::getInstance()->recuperarCampo('txtCpfUsuario'));
  if ($strCpfPesquisa!==''){
    $objUsuarioDTO->setDblCpfContato(InfraUtil::retirarFormatacao($strCpfPesquisa));
  }

  $strNomePesquisa = PaginaSEI::getInstance()->recuperarCampo('txtNomeUsuario');
  if ($strNomePesquisa!==''){
    $objUsuarioDTO->setStrNome($strNomePesquisa);
  }

  
  $objUsuarioDTO->adicionarCriterio(array('StaTipo', 'StaTipo'),
  		array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL),
  		array(UsuarioRN::$TU_EXTERNO, UsuarioRN::$TU_EXTERNO),
  		array(InfraDTO::$OPER_LOGICO_OR));
  
  
  PaginaSEI::getInstance()->prepararOrdenacao($objUsuarioDTO, 'Sigla', InfraDTO::$TIPO_ORDENACAO_ASC);
  
  PaginaSEI::getInstance()->prepararPaginacao($objUsuarioDTO);

  $objUsuarioRN = new UsuarioRN();
  $arrObjUsuarioDTO = $objUsuarioRN->pesquisar($objUsuarioDTO);

  $contatosPagina = InfraArray::converterArrInfraDTO($arrObjUsuarioDTO, 'IdContato');

  PaginaSEI::getInstance()->processarPaginacao($objUsuarioDTO);

  $numRegistros = count($arrObjUsuarioDTO);

  if ($numRegistros > 0){

    $bolCheck = false;

    if ($_GET['acao']=='md_pet_pessoa_fisica'){
      $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pet_pessoa_fisica');
      $bolCheck = true;
    }

    $strResultado = '';

    $strSumarioTabela = 'Tabela de Usuários Externos.';
    $strCaptionTabela = 'Usuários Externos';

    $strResultado .= '<table width="100%" class="infraTable" summary="'.$strSumarioTabela.'">'."\n";
    $strResultado .= '<caption class="infraCaption">'.PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela,$numRegistros).'</caption>';
    $strResultado .= '<tr>';

    if ($bolCheck) {
      $strResultado .= '<th class="infraTh"><div style="width:20px">'.PaginaSEI::getInstance()->getThCheck().'</div></th>'."\n";
    }
    
    $strResultado .= '<th class="infraTh"><div class="text-left">'.PaginaSEI::getInstance()->getThOrdenacao($objUsuarioDTO,'E-mail','Sigla',$arrObjUsuarioDTO,true).'</div></th>'."\n";
    $strResultado .= '<th class="infraTh"><div style="width:150px" class="text-left">'.PaginaSEI::getInstance()->getThOrdenacao($objUsuarioDTO,'Nome','Nome',$arrObjUsuarioDTO,true).'</div></th>'."\n";    
    
    $strResultado .= '<th class="infraTh"><div style="width:50px" class="text-center">Ações</div></th>'."\n";
    $strResultado .= '</tr>'."\n";

    $strCssTr = 'Clara';

    for($i = 0;$i < $numRegistros; $i++){

      $isEmailUsuarioValido = InfraUtil::validarEmail(PaginaSEI::tratarHTML($arrObjUsuarioDTO[$i]->getStrSigla()));
      $avisoIntimacaoAnterior = in_array($arrObjUsuarioDTO[$i]->getNumIdContato(), $arrContatosIntimados) ? 'Este usuário já recebeu este documento principal em intimação anterior. Verifique lista de intimações do processo.' : '';
      $emailInvalido = !$isEmailUsuarioValido ? 'O e-mail deste Usuário Externo não está no formato correto. Verifique o cadastro do mesmo antes de incluí-lo.' : '';

      $title = 'title="';

      if(!empty($emailInvalido)){
          $title .= $emailInvalido;
      }

      if(!empty($avisoIntimacaoAnterior)){
          $title .= $avisoIntimacaoAnterior;
      }

      $title .= '"';

      $strResultado .= '<tr class="infraTr'.$strCssTr.'" '.$title.'>';

      if ($bolCheck){
          // Destinatarios em massa: Pre-seleciona o checkbox caso o Usuario Esterno ja tenha recebido o documento em outra intimacao:
          $strAtributos = in_array($arrObjUsuarioDTO[$i]->getNumIdContato(), $arrContatosIntimados) ? 'disabled checked="checked"' : '';

          if(!$isEmailUsuarioValido && $strAtributos == ''){
              $strAtributos = 'disabled';
          }

          $strTdTitulo = $arrObjUsuarioDTO[$i]->getStrNome().' - '.$arrObjUsuarioDTO[$i]->getStrSigla().' - '.InfraUtil::formatarCpfCnpj($arrObjUsuarioDTO[$i]->getDblCpfContato());
          $strResultado .= '<td valign="top">'.PaginaSEI::getInstance()->getTrCheck($i, $arrObjUsuarioDTO[$i]->getNumIdContato(), $strTdTitulo, $strValor = 'N', $strNomeSelecao = 'Infra', $strAtributos).'</td>';
      }

      //$strResultado .= '<td align="center">'.$arrObjUsuarioDTO[$i]->getNumIdUsuario().'</td>';
      $strResultado .= '<td class="text-left">'.PaginaSEI::tratarHTML($arrObjUsuarioDTO[$i]->getStrSigla()).'</td>';
      $strResultado .= '<td class="text-left">'.PaginaSEI::tratarHTML($arrObjUsuarioDTO[$i]->getStrNome()).'</td>';
      $strResultado .= '<td class="text-center">';

      // Destinatarios em massa: Suprime o botao de transporte caso o Usuario Esterno ja tenha recebido o documento em outra intimacao:
      if(!in_array($arrObjUsuarioDTO[$i]->getNumIdContato(), $arrContatosIntimados) && $isEmailUsuarioValido){
          $strResultado .= PaginaSEI::getInstance()->getAcaoTransportarItem($i,$arrObjUsuarioDTO[$i]->getNumIdContato());
      }

      $strResultado .= '</td></tr>'."\n";
      $strCssTr = $strCssTr == 'Clara' ? 'Escura' : 'Clara';

    }

    $strResultado .= '</table>';

  }
  if ($_GET['acao'] == 'md_pet_pessoa_fisica'){
    $arrComandos[] = '<button type="button" accesskey="C" id="btnFecharSelecao" value="Fechar" onclick="window.close();" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
  }else{
    $arrComandos[] = '<button type="button" accesskey="C" id="btnFechar" value="Fechar" onclick="location.href=\''.SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.PaginaSEI::getInstance()->getAcaoRetorno().'&acao_origem='.$_GET['acao']).'\'" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
  }
  
  $strItensSelOrgao = OrgaoINT::montarSelectSiglaRI1358('','Todos',$numIdOrgao);

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
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>

function inicializar(){
  if ('<?=$_GET['acao']?>'=='md_pet_pessoa_fisica'){
    infraReceberSelecao();
    document.getElementById('btnFecharSelecao').focus();
  }else{
    document.getElementById('btnFechar').focus();
  }
  
  infraEfeitoTabelas();
}

<?
    PaginaSEI::getInstance()->fecharJavaScript();
    PaginaSEI::getInstance()->fecharHead();
    PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');
    //PaginaSEI::getInstance()->montarBarraLocalizacao($strTitulo);
    PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
    PaginaSEI::getInstance()->abrirAreaDados();
?>

    <div class="row pesquisa-usuarios-lote my-2" style="display: none">
        <div class="col">
            <div class="wrapper bg-light py-3 px-4 mb-3">
                <div class="mb-3">
                    <div class="cpf-validation-check">
                        <div class="row">
                            <div class="col-7">
                                <div class="form-group">
                                    <label for="cpfList">Seleção em lote por CPF:
                                        <img src="/infra_css/svg/ajuda.svg" name="ajuda" id="imgAjudaUsuario" onmouseover="return infraTooltipMostrar('Cole no campo abaixo a lista de CPFs válidos para pesquisa em lote, contento um CPF por linha, com ou sem formatação conforme os exemplos abaixo: \n\n 99999999999 ou 777.777.777-77','Ajuda');" onmouseout="return infraTooltipOcultar();" class="infraImgModulo">
                                    </label><br>
                                    <textarea class="infraTextarea" id="cpfList" rows="5" style="width: 100%"></textarea>
                                </div>
                            </div>
                            <div class="col-5 pt-4">
                                <button type="button" class="infraButton mt-1" id="validateBtn" style="display: inline-block">Pesquisar em lote</button>
                                <button type="button" class="infraButton mt-3" id="transportBtn" style="display: none">Transportar Localizados</button>
<!--                                <a role="button" id="copyFounds" class="d-block text-primary mt-2 clipboardBtn" style="cursor:pointer" data-clipboard-target="#foundCpfs"></a>-->
                            </div>
                        </div>
                    </div>
                    <div class="cpf-validation-result" style="display: none">
                        <div class="mt-3" style="position: absolute; margin-left -999999999px; height: 0px; width: 0px; overflow: hidden; opacity: 0;">
                            <div class="row">
                                <div class="col-12">
                                    <label for="">Lista de CPFs Localizados:</label>
                                    <textarea class="infraTextarea" id="foundCpfs" rows="5" style="width: 100%" readonly></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="row">
                                <div class="col-7">
                                    <label for="">Lista de CPFs Não localizados:</label><br>
                                    <textarea class="infraTextarea" id="notFoundCpfs" rows="5" style="width: 100%" readonly></textarea>
                                </div>
                                <div class="col-5 pt-1">
                                    <!--  <button type="button" class="infraButton mt-3" id="downloadBtn">Exportar lista de CPFs não localizados</button>-->
                                    <button type="button" class="infraButton mt-3 clipboardBtn" id="copyNotFounds" data-clipboard-target="#notFoundCpfs">Copiar lista de CPFs não localizados</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Destinatarios em massa: Adicionado parametro id_documento para a validacao funcionar quando realizada a pesquisa dentro da modal:-->
    <form id="frmUsuarioLista" method="post" class="" action="<?=SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].'&acao_origem='.$_GET['acao'].'&id_documento='.$idDocumento)?>">
        <input type="submit" class="d-none" />
        <div class="row">
            <div class="col-xl-3 col-lg-4 col-md-4 col-sm-4 col-6">
                <div class="form-group">
                    <label for="txtSiglaUsuario" class="infraLabelOpcional">E-mail:</label>
                    <input type="text" id="txtSiglaUsuario" name="txtSiglaUsuario" class="infraText form-control" value="<?=PaginaSEI::tratarHTML($strSiglaPesquisa)?>" maxlength="100" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-4 col-sm-4 col-6">
                <div class="form-group">
                    <label for="txtNomeUsuario" accesskey="N" class="infraLabelOpcional"><span class="infraTeclaAtalho">N</span>ome:</label>
                    <input type="text" id="txtNomeUsuario" name="txtNomeUsuario" class="infraText form-control" value="<?=PaginaSEI::tratarHTML($strNomePesquisa)?>" maxlength="50" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-4">
                <div class="form-group">
                    <label for="txtCpfUsuario" class="infraLabelOpcional">CPF:</label>
                    <input type="text" id="txtCpfUsuario" name="txtCpfUsuario" onkeypress="return infraMascaraCpf(this, event)" class="infraText form-control" value="<?=PaginaSEI::tratarHTML($strCpfPesquisa);?>" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
                </div>
            </div>
        </div>
    
    
        <? PaginaSEI::getInstance()->fecharAreaDados(); ?>
        <div class="row">
            <div class="col-12">
                <div class="table-responsive ">
                    <!-- Destinatarios em massa: Melhorando a usabilidade para usuario saber que os Usuarios Externos ticados ja foram selecionados ou ja receberam o documento em outra intimacao: -->
                    <? if(!empty($arrContatosIntimados) && !empty(array_intersect($contatosPagina, $arrContatosIntimados))): ?>
                        <p class="alert alert-warning">Os Usuários Externos pré-selecionados já receberam o documento principal em Intimação Eletrônica anterior. Para verificar a lista de destinatários que já receberam o documento principal, consulte "Ver intimações do processo".</p>
                    <? endif; ?>
                    <? PaginaSEI::getInstance()->montarAreaTabela($strResultado,$numRegistros); ?>
                </div>
            </div>
        </div>
        <?
    
            PaginaSEI::getInstance()->montarAreaDebug();
            PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
            $strLinkUsuariosMassa = SessaoSEI::getInstance()->assinarLink('modulos/peticionamento/controlador_ajax.php?acao_ajax=md_pet_verifica_usuarios_intimacao&id_documento='.$idDocumento);
    
        ?>
    </form>

    <script>
        $(document).ready(function() {

            $('.submitSearchForm').off('click').on('click', function(e) {
                e.preventDefault(); e.stopPropagation();
                $('form#frmUsuarioLista')[0].submit();
            });

            let timer;

            $('#cpfList').off('input paste keyup').on('input paste keyup', function() {
                $('.cpf-validation-result textarea').val('');
                $('.cpf-validation-result, #transportBtn, #clipboardFoundCpfs').hide();
                $('#validateBtn').prop('disabled', false).html('Pesquisar em lote');
            });

            $('#downloadBtn').click(function() {
                var notFoundCpfs = $('#notFoundCpfs').val();
                if (notFoundCpfs.trim() !== '') {
                    download('SEI-ANATEL-CPFs-usuarios-externos-nao-localizados.txt', notFoundCpfs);
                } else {
                    alert('Não há CPFs não encontrados para baixar.');
                }
            });

            $('body').on('click', '.clipboardBtn', function() {
                clearTimeout(timer);
                var textarea = $($(this).data('clipboard-target'));
                textarea.select();
                timer = setTimeout(function() {
                    document.execCommand('copy');
                    alert('Conteúdo copiado para a área de transferência.');
                }, 500);
            });

            function download(filename, text) {
                var element = document.createElement('a');
                element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
                element.setAttribute('download', filename);
                element.style.display = 'none';
                document.body.appendChild(element);
                element.click();
                document.body.removeChild(element);
            }

            // Função para validar CPFs
            function validarCPFs() {
                
                const cpfsTexto = $('#cpfList').val().trim();
                const cpfs = cpfsTexto.split('\n');
                const cpfRegex = /^(\d{3}\.?\d{3}\.?\d{3}-?\d{2}|\d{11})$/;
                
                let cpfsInvalidos = [];
                
                for (let cpf of cpfs) {

                    let cpfClean = cpf.replace(/\D/g, '');

                    if (/[^0-9.-]/.test(cpf)) {
                        cpfsInvalidos.push(cpf + ' - Caracteres inválidos');
                        continue; // Pula para o próximo CPF
                    }
                    
                    if(cpfClean.trim().length != 11){
                        cpfsInvalidos.push(cpf + ' - Tamanho inválido');
                        continue; // Pula para o próximo CPF
                    }

                    if (/^(\d)\1{10}$/.test(cpfClean)) {
                        cpfsInvalidos.push(cpf + ' - Sequëncia inválida');
                        continue; // Pula para o próximo CPF
                    }

                    if (!cpfRegex.test(cpf)) {
                        cpfsInvalidos.push(cpf + ' -  Formato inválido');
                        continue; // Pula para o próximo CPF
                    }
                    
                }

                if (cpfsInvalidos.length > 0) {
                    alert('Os seguintes CPFs são inválidos:\n\n' + cpfsInvalidos.join('\n'));
                } else {
                    $('#validateBtn').fadeIn(100);
                }
                
            }

            $('body').on('click', '.btnToggleLote', function(){
                $('.pesquisa-usuarios-lote').fadeToggle(200);
                
                let mode = $('#divInfraBarraLocalizacao').html() == 'Selecionar Usuários Externos' ? 'normal' : 'lote';
                
                if(mode == 'normal'){
                    $('#divInfraBarraLocalizacao').html('Selecionar Usuários Externos em Lote');
                    $('.btnToggleLote').val('Fechar seleção em Lote');
                    $('.fecharLote').css('display', 'none');
                    $('#cpfList').focus();
                }else{
                    $('#divInfraBarraLocalizacao').html('Selecionar Usuários Externos');
                    $('.btnToggleLote').val('Selecionar em Lote');
                    $('form.fecharLote, div.fecharLote').fadeIn(100);
                    $('input.fecharLote, button.fecharLote').css('display', 'inline-block');
                }
                
            });
            
            $('body').off('click', '#validateBtn').on('click', '#validateBtn', function() {
                
                var btnClicked = $(this);
                var cpfList = $('#cpfList').val().split('\n').filter(Boolean); // Filtrar linhas vazias

                if(cpfList.length > 0){

                    $.ajax({
                        url: '<?= $strLinkUsuariosMassa ?>',
                        method: 'POST',
                        data: { cpfList: cpfList },
                        beforeSend: function(){

                            btnClicked.prop('disabled', true).html('Pesquisando em lote ('+ cpfList.length +')...');
                            $('#foundCpfs, #notFoundCpfs').val('');
                            $('.cpf-validation-result, #transportBtn').hide();

                        },
                        success: function(response) {

                            var data = JSON.parse(response);

                            if(data.foundCpfs.length > 0){

                                $('#foundCpfs').val(data.foundCpfs.join('\n'));
                                $('#transportBtn').html('Transportar Localizados ('+data.foundCpfs.length+')').fadeIn(100);
                                $('#copyFounds').html('Copiar lista de localizados ('+data.foundCpfs.length+')').fadeIn(100);

                            }else{

                                $('#transportBtn').hide();

                                let text = 'Na lista de CPFs informada não foram localizados Usuários Externos que possam ser adicionados como remetentes da intimação. ';

                                if(data.notAbleCpfs.length > 0){
                                    let i = 0;
                                    text = text+'\n\nOs seguintes Usuários Externos encontrados já receberam este documento em intimação anterior:\n\n';

                                    while (i < data.notAbleCpfs.length) {
                                        text = text + data.notAbleCpfs[i] + '\n';
                                        i++;
                                    }
                                }

                                text = text+'\n\nPara verificar a lista de destinatários que já receberam o documento principal, consulte "Ver intimações do processo"';
                                // alert(text);

                            }

                            if(data.notFoundCpfs.length > 0){
                                $('#notFoundCpfs').val(data.notFoundCpfs.join('\n'));
                                $('#copyNotFounds').html('Copiar lista de não localizados ('+data.notFoundCpfs.length+')').show();
                                $('.cpf-validation-result').show();
                            }else{
                                $('#downloadBtn, #notFoundCpfs, .cpf-validation-result').hide();
                            }
                            btnClicked.prop('disabled', false).html('Pesquisar em lote ('+ cpfList.length +')');

                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                            alert(xhr.responseText);
                        },
                        complete: function(){
                            btnClicked.prop('disabled', false).html('Pesquisar em lote');
                        }
                    });
                    
                }else{
                    alert('Informe a lista de CPFs válidos.');
                    $('#cpfList').focus();
                }
                
            });
            
            $('body').on('click', '#transportBtn', function(){

                let i = 0;
                let toTransport = $('#foundCpfs').val().split('\n');
                let contextChanges = window.top.document.getElementById('ifrConteudoVisualizacao').contentWindow.document.getElementById('ifrVisualizacao').contentWindow.document;
                
                while (i < toTransport.length) {
                    
                    let valuesTransport = toTransport[i].split("|");
                    let valueExist = $("#selDadosUsuario2 option[value='"+valuesTransport[0]+"']", contextChanges).length;
                    
                    if(valueExist == 0){
                        
                        // Adicionando o novo valor no Select
                        $('#selDadosUsuario2', contextChanges)
                        .append($('<option>', {
                            value: valuesTransport[0],
                            text : valuesTransport[1]+' - '+valuesTransport[2]+' - '+ valuesTransport[3]
                        }));

                        $('#selDadosUsuario2 option', contextChanges).attr('selected', 'selected');

                        // Adicionando o novo valor ao campo oculto
                        let hiddenField = $('#hdnDadosUsuario2', contextChanges);
                        let complement = hiddenField.val() != '' ? '¥' : '';

                        hiddenField.val(hiddenField.val()+complement+valuesTransport[0]+'±'+valuesTransport[1]+' - '+valuesTransport[2]+' - '+ valuesTransport[3]);
                        
                    }
                    
                    i++;
                }
                
                if($('#hdnDadosUsuario2', contextChanges).val() != ''){
                    $('#conteudoHide', contextChanges).show();
                }

                $('#btnFecharSelecao').trigger('click');
                
            });
            
        });

    </script>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>