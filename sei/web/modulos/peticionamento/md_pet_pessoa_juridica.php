<?
/**
* ANATEL
*
* 31/01/2019 - criada por Renato Chaves - CAST
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
    PaginaSEI::getInstance()->prepararSelecao('md_pet_pessoa_juridica');
    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);
	
    switch($_GET['acao']){
   
        case 'md_pet_pessoa_juridica':
            $strTitulo = PaginaSEI::getInstance()->getTituloSelecao('Selecionar Destinatario','Selecionar Destinatarios');
        break;

        default:
            throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
    }

    $arrComandos = [];
	$arrComandos[] = '<input type="button" id="btnToggleLote" value="Selecionar em Lote" class="infraButton btnToggleLote" style="cursor: pointer" />';
    $arrComandos[] = '<button type="submit" accesskey="P" id="sbmPesquisar" value="Pesquisar" class="infraButton submitSearchForm"><span class="infraTeclaAtalho">P</span>esquisar</button>';

    if ($_GET['acao'] == 'md_pet_pessoa_juridica'){
        $arrComandos[] = '<button type="button" accesskey="T" id="btnTransportarSelecao" value="Transportar" onclick="infraTransportarSelecao();" class="infraButton"><span class="infraTeclaAtalho">T</span>ransportar</button>';
        $arrComandos[] = '<button type="button" accesskey="C" name="sbmFechar" id="btnFecharSelecao" s  onclick="window.close();" value="Fechar" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
    }

    $objDTOVinculo = new MdPetVincRepresentantDTO();
    $objDTOVinculo->retNumIdContatoVinc();
    $objDTOVinculo->retStrIdxContato();

    if(!empty($_POST['txtRazao'])){
        $objDTOVinculo->setStrRazaoSocialNomeVinc('%'.$_POST['txtRazao'].'%', InfraDTO::$OPER_LIKE);
    }

    if(!empty($_POST['txtCnpj'])){
        $objDTOVinculo->setStrCNPJ(InfraUtil::retirarFormatacao($_POST['txtCnpj']));
    }

    $objDTOVinculo->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
    $objDTOVinculo->setStrTpVinc(MdPetVincRepresentantRN::$NT_JURIDICA);
    $objDTOVinculo->setDistinct(true);
    $objDTOVinculo->retStrRazaoSocialNomeVinc();
    $objDTOVinculo->retStrCNPJ();
    $objRNVinculo = new MdPetVincRepresentantRN();

    PaginaSEI::getInstance()->prepararOrdenacao($objDTOVinculo, 'RazaoSocialNomeVinc', InfraDTO::$TIPO_ORDENACAO_ASC);
    PaginaSEI::getInstance()->prepararPaginacao($objDTOVinculo);

    $arrJuridicas = $objRNVinculo->listar($objDTOVinculo);

	$contatosPagina = InfraArray::converterArrInfraDTO($arrJuridicas, 'IdContatoVinc');

    //Ordenação
    PaginaSEI::getInstance()->processarPaginacao($objDTOVinculo);

    // Destinatarios em massa: Pega os Usuarios Externos que ja receberam o documento principal por intimacao:
    $arrContatosIntimados = [];
    $idDocumento = '';
    if(isset($_REQUEST['id_documento']) && !empty($_REQUEST['id_documento'])){
        $idDocumento = $_REQUEST['id_documento'];
        $arrContatosIntimados = array_column((new MdPetIntimacaoRN())->buscaIntimacoesCadastradasJuridico($idDocumento), 'Id');
    }

    $numRegistros = count($arrJuridicas);

    if ($numRegistros > 0){

        $bolCheck = false;

        if ($_GET['acao']=='md_pet_pessoa_juridica'){
            $strCaptionTabela = 'Pessoas Jurídicas';
            $bolCheck = true;
        }

        $strResultado = '';

        $strResultado .= '<table width="100%" class="infraTable" summary="'.$strSumarioTabela.'">'."\n";
        $strResultado .= '<caption class="infraCaption">'.PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela,$numRegistros).'</caption>';
        $strResultado .= '<tr>';

        if ($bolCheck) {
            $strResultado .= '<th class="infraTh"><div style="width:20px">'.PaginaSEI::getInstance()->getThCheck().'</div></th>'."\n";
        }

        $strResultado .= '<th class="infraTh"><div>'.PaginaSEI::getInstance()->getThOrdenacao($objDTOVinculo,'Razão Social','RazaoSocialNomeVinc',$arrJuridicas).'</div></th>'."\n";
        $strResultado .= '<th class="infraTh"><div style="width:150px" class="text-center">'.PaginaSEI::getInstance()->getThOrdenacao($objDTOVinculo,'CNPJ','CNPJ',$arrJuridicas).'</div></th>'."\n";
        $strResultado .= '<th class="infraTh"><div style="width:50px" class="text-center">Ações</div></th>'."\n";
        $strResultado .= '</tr>'."\n";
        $strResultado .= '<tbody>';

		$strCssTr = 'Clara';

        for($i = 0;$i < $numRegistros; $i++){

			$avisoIntimacaoAnterior = in_array($arrJuridicas[$i]->getNumIdContatoVinc(), $arrContatosIntimados) ? 'Este Destinatário já recebeu este documento principal em intimação anterior. Verifique lista de intimações do processo.' : '';

            $strResultado .= '<tr class="infraTr'.$strCssTr.'" title="'.$avisoIntimacaoAnterior.'">';

            if ($bolCheck){
                // Destinatarios em massa: Pre-seleciona o checkbox caso o Usuario Esterno ja tenha recebido o documento em outra intimacao:
                $strAtributos = in_array($arrJuridicas[$i]->getNumIdContatoVinc(), $arrContatosIntimados) ? 'disabled checked="checked"' : '';

                $strTdTitulo = $arrJuridicas[$i]->getStrRazaoSocialNomeVinc().' - '.InfraUtil::formatarCpfCnpj($arrJuridicas[$i]->getStrCNPJ());
                $strResultado .= '<td valign="top">'.PaginaSEI::getInstance()->getTrCheck($i, $arrJuridicas[$i]->getNumIdContatoVinc(), $strTdTitulo, $strValor = 'N', $strNomeSelecao = 'Infra', $strAtributos).'</td>';
            }

            $strResultado .= '<td>'.PaginaSEI::tratarHTML($arrJuridicas[$i]->getStrRazaoSocialNomeVinc()).'</td>';
            $strResultado .= '<td class="text-center">'.InfraUtil::formatarCpfCnpj($arrJuridicas[$i]->getStrCNPJ()).'</td>';
            $strResultado .= '<td class="text-center">';

            // Destinatarios em massa: Suprime o botao de transporte caso o Usuario Esterno ja tenha recebido o documento em outra intimacao:
            if(!in_array($arrJuridicas[$i]->getNumIdContatoVinc(), $arrContatosIntimados)){
                $strResultado .= PaginaSEI::getInstance()->getAcaoTransportarItem($i,$arrJuridicas[$i]->getNumIdContatoVinc());
            }

            $strResultado .= '</td></tr>'."\n";

			$strCssTr = $strCssTr == 'Clara' ? 'Escura' : 'Clara';

        }

        $strResultado .= '</tbody>';
        $strResultado .= '</table>';

    }
  
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
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');

PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaSEI::getInstance()->abrirAreaDados();
?>

<div class="row pesquisa-destinatarios-lote my-2" style="display: none">
    <div class="col">
        <div class="wrapper bg-light py-3 px-4 mb-3">
            <div class="mb-3">
                <div class="cnpj-validation-check">
                    <div class="row">
                        <div class="col-7">
                            <div class="form-group">
                                <label for="cnpjList">Seleção em lote por CNPJ:
                                    <img src="/infra_css/svg/ajuda.svg" name="ajuda" id="imgAjudaUsuario" onmouseover="return infraTooltipMostrar('Cole no campo abaixo a lista de CNPJs válidos para pesquisa em lote, contento um CNPJ por linha, com ou sem formatação conforme os exemplos abaixo: \n\n 12345678000100 ou 12.345.678/0001-00','Ajuda');" onmouseout="return infraTooltipOcultar();" class="infraImgModulo">
                                </label><br>
                                <textarea class="infraTextarea" id="cnpjList" rows="5" style="width: 100%"></textarea>
                            </div>
                        </div>
                        <div class="col-5 pt-4">
                            <button type="button" class="infraButton mt-1" id="validateBtn" style="display: inline-block">Pesquisar em lote</button>
                            <button type="button" class="infraButton mt-3" id="transportBtn" style="display: none">Transportar Localizados</button>
                        </div>
                    </div>
                </div>
                <div class="cnpj-validation-result" style="display:none">
                    <div class="mt-3" style="position: absolute; margin-left -999999999px; height: 0px; width: 0px; overflow: hidden; opacity: 0;">
                        <div class="row">
                            <div class="col-12">
                                <label for="">Lista de CNPJs Localizados:</label>
                                <textarea class="infraTextarea" id="foundCnpjs" rows="5" style="width: 100%" readonly></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="row">
                            <div class="col-7">
                                <label for="">Lista de CNPJs Não localizados:</label><br>
                                <textarea class="infraTextarea" id="notFoundCnpjs" rows="5" style="width: 100%" readonly></textarea>
                            </div>
                            <div class="col-5 pt-1">
                                <!--  <button type="button" class="infraButton mt-3" id="downloadBtn">Exportar lista de CPFs não localizados</button>-->
                                <button type="button" class="infraButton mt-3 clipboardBtn" id="copyNotFounds" data-clipboard-target="#notFoundCnpjs">Copiar lista de CPFs não localizados</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="frmSerieLista" method="post" action="<?=PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao='.$_GET['acao'].'&filtro='. $_GET['filtro'].'&tipoDoc='.$_GET['tipoDoc'].'&acao_origem='.$_GET['acao'].'&id_documento='.$idDocumento))?>">
    <input type="submit" class="d-none" />

    <div class="row">
        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-5 col-6">
            <div class="form-group">
                <label for="txtRazao" class="infraLabelOpcional">Razão Social:</label>
                <input type="text" id="txtRazao" name="txtRazao"  class="infraText form-control" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"  value="<?php echo array_key_exists('txtRazao', $_POST) ? $_POST['txtRazao'] : '' ?>" />
            </div>
        </div>
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-5 col-6">
            <div class="form-group">
                <label id="lblCnpj" for="txtCnpj" accesskey="" class="infraLabelOpcional">CNPJ:</label>
                <input type="text" value="<?php echo array_key_exists('txtCnpj', $_POST) ? $_POST['txtCnpj'] : '' ?>" id="txtCnpj" name="txtCnpj" onkeypress="return infraMascaraCnpj(this, event)"  class="infraText form-control" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
            </div>
        </div>
    </div>
	
	<? PaginaSEI::getInstance()->fecharAreaDados(); ?>
    <div class="row">
        <div class="col-12">
            <div>
                <!-- Destinatarios em massa: Melhorando a usabilidade para usuario saber que os Usuarios Externos ticados ja foram selecionados ou ja receberam o documento em outra intimacao: -->
				<? if(!empty($arrContatosIntimados) && !empty(array_intersect($contatosPagina, $arrContatosIntimados))): ?>
                    <p class="alert alert-warning">Os Destinatários pré-selecionados já receberam o documento principal em Intimação Eletrônica anterior. Para verificar a lista de destinatários que já receberam o documento principal, consulte "Ver intimações do processo".</p>
				<? endif; ?>
				<? PaginaSEI::getInstance()->montarAreaTabela($strResultado,$numRegistros); ?>
            </div>
        </div>
    </div>
	
	<?
	
	PaginaSEI::getInstance()->montarAreaDebug();
	PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
	$strLinkDestinatariosMassa = SessaoSEI::getInstance()->assinarLink('modulos/peticionamento/controlador_ajax.php?acao_ajax=md_pet_verifica_destinatarios_intimacao&id_documento='.$idDocumento);
	
	?>

</form>

<script>
    
    function inicializar(){
    
        if ('<?=$_GET['acao']?>'=='md_pet_pessoa_juridica'){
            infraReceberSelecao();
            document.getElementById('btnFecharSelecao').focus();
        }else{
            document.getElementById('btnFechar').focus();
        }
    
        infraEfeitoTabelas();
    
    }
    
    $(document).ready(function() {

        $('.submitSearchForm').off('click').on('click', function(e) {
            e.preventDefault(); e.stopPropagation();
            $('form#frmSerieLista')[0].submit();
        });

        let timer;

        $('#cnpjList').off('input paste keyup').on('input paste keyup', function() {
            $('.cnpj-validation-result textarea').val('');
            $('.cnpj-validation-result, #transportBtn, #clipboardFoundCnpjs').hide();
            $('#validateBtn').prop('disabled', false).html('Pesquisar em lote');
        });

        $('#downloadBtn').click(function() {
            var notFoundCnpjs = $('#notFoundCnpjs').val();
            if (notFoundCnpjs.trim() !== '') {
                download('SEI-ANATEL-CNPJs-destinatarios-intimacao-nao-localizados.txt', notFoundCnpjs);
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

        // Função para validar CNPJs
        function validarCNPJs() {

            const cnpjsTexto = $('#cpfList').val().trim();
            const cnpjs = cpfsTexto.split('\n');
            const cnpjRegex = /^(\d{3}\.?\d{3}\.?\d{3}-?\d{2}|\d{11})$/;

            let cnpjsInvalidos = [];

            for (let cnpj of cnpjs) {

                let cnpjClean = cnpj.replace(/\D/g, '');

                if (/[^0-9.-]/.test(cnpj)) {
                    cnpjsInvalidos.push(cnpj + ' - Caracteres inválidos');
                    continue; // Pula para o próximo CNPJ
                }

                if(cnpjClean.trim().length != 11){
                    cnpjsInvalidos.push(cnpj + ' - Tamanho inválido');
                    continue; // Pula para o próximo CNPJ
                }

                if (/^(\d)\1{10}$/.test(cnpjClean)) {
                    cnpjsInvalidos.push(cnpj + ' - Sequëncia inválida');
                    continue; // Pula para o próximo CNPJ
                }

                if (!cnpjRegex.test(cnpj)) {
                    cnpjsInvalidos.push(cnpj + ' -  Formato inválido');
                    continue; // Pula para o próximo CNPJ
                }

            }

            if (cpfsInvalidos.length > 0) {
                alert('Os seguintes CNPJs são inválidos:\n\n' + cnpjsInvalidos.join('\n'));
            } else {
                $('#validateBtn').fadeIn(100);
            }

        }

        $('body').on('click', '.btnToggleLote', function(){
            
            $('.pesquisa-destinatarios-lote').fadeToggle(200);

            let mode = $('#divInfraBarraLocalizacao').html() == 'Selecionar Destinatarios' ? 'normal' : 'lote';

            if(mode == 'normal'){
                
                $('#divInfraBarraLocalizacao').html('Selecionar Destinatarios em Lote');
                $('.btnToggleLote').val('Fechar seleção em Lote');
                $('.fecharLote').css('display', 'none');
                $('#cnpjList').focus();
                
            }else{
                
                $('#divInfraBarraLocalizacao').html('Selecionar Destinatarios');
                $('.btnToggleLote').val('Selecionar em Lote');
                $('form.fecharLote, div.fecharLote').fadeIn(100);
                $('input.fecharLote, button.fecharLote').css('display', 'inline-block');
                
            }

        });

        $('body').off('click', '#validateBtn').on('click', '#validateBtn', function() {

            var btnClicked = $(this);
            var cnpjList = $('#cnpjList').val().split('\n').filter(Boolean); // Filtrar linhas vazias

            if(cnpjList.length > 0){

                $.ajax({
                    url: '<?= $strLinkDestinatariosMassa ?>',
                    method: 'POST',
                    data: { cnpjList: cnpjList },
                    beforeSend: function(){

                        btnClicked.prop('disabled', true).html('Pesquisando em lote ('+ cnpjList.length +')...');
                        $('#foundCnpjs, #notFoundCnpjs').val('');
                        $('.cnpj-validation-result, #transportBtn').hide();

                    },
                    success: function(response) {

                        var data = JSON.parse(response);

                        if(data.foundCnpjs.length > 0){

                            $('#foundCnpjs').val(data.foundCnpjs.join('\n'));
                            $('#transportBtn').html('Transportar Localizados ('+data.foundCnpjs.length+')').fadeIn(100);
                            $('#copyFounds').html('Copiar lista de localizados ('+data.foundCnpjs.length+')').fadeIn(100);

                        }else{

                            $('#transportBtn').hide();

                            let text = 'Na lista de CNPJs informada não foram localizados Destinatários que possam ser adicionados como remetentes da intimação. ';

                            if(data.notAbleCnpjs.length > 0){
                                let i = 0;
                                text = text+'\n\nOs seguintes Destinatários encontrados já receberam este documento em intimação anterior:\n\n';

                                while (i < data.notAbleCnpjs.length) {
                                    text = text + data.notAbleCnpjs[i] + '\n';
                                    i++;
                                }
                            }

                            text = text+'\n\nPara verificar a lista de Destinatários que já receberam o documento principal, consulte "Ver intimações do processo".';
                            // alert(text);

                        }

                        if(data.notFoundCnpjs.length > 0){
                            $('.cnpj-validation-result').show();
                            $('#notFoundCnpjs').val(data.notFoundCnpjs.join('\n'));
                            $('#copyNotFounds').html('Copiar lista de não localizados ('+data.notFoundCnpjs.length+')').show();
                        }else{
                            $('#copyNotFounds, #downloadBtn, #notFoundCnpjs, .cnpj-validation-result').hide();
                        }

                        btnClicked.prop('disabled', false).html('Pesquisar em lote ('+ cnpjList.length +')');

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
                alert('Informe a lista de CNPJs válidos.');
                $('#cnpjList').focus();
            }
            
        });

        $('body').on('click', '#transportBtn', function(){

            let i = 0;
            let toTransport = $('#foundCnpjs').val().split('\n');
            let contextChanges = window.top.document.getElementById('ifrVisualizacao').contentWindow.document;

            while (i < toTransport.length) {

                let valuesTransport = toTransport[i].split("|");
                let valueExist = $("#selDadosUsuario2 option[value='"+valuesTransport[0]+"']", contextChanges).length;

                if(valueExist == 0){

                    // Adicionando o novo valor no Select
                    $('#selDadosUsuario2', contextChanges)
                        .append($('<option>', {
                            value: valuesTransport[0],
                            text : valuesTransport[1]
                        }));

                    $('#selDadosUsuario2 option', contextChanges).attr('selected', 'selected');

                    // Adicionando o novo valor ao campo oculto
                    let hiddenField = $('#hdnDadosUsuario2', contextChanges);
                    let complement = hiddenField.val() != '' ? '¥' : '';

                    hiddenField.val(hiddenField.val()+complement+valuesTransport[0]+'±'+valuesTransport[1]);

                }

                i++;
            }

            if($('#hdnDadosUsuario2', contextChanges).val() != ''){
                $('#conteudoHide2, #hiddeAll2', contextChanges).show();
            }

            $('#btnFecharSelecao').trigger('click');

        });

    });

</script>
<?
    PaginaSEI::getInstance()->fecharBody();
    PaginaSEI::getInstance()->fecharHtml();
?>

