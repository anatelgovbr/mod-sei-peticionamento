<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 08/12/2016 - criado por Marcelo Bezerra - CAST
 *
 * Versão do Gerador de Código: 1.39.0
 *
 */
try {
    require_once dirname(__FILE__) . '/../../SEI.php';
    session_start();

    //////////////////////////////////////////////////////////////////////////////
//    InfraDebug::getInstance()->setBolLigado(false);
//    InfraDebug::getInstance()->setBolDebugInfra(true);
//    InfraDebug::getInstance()->limpar();
    //////////////////////////////////////////////////////////////////////////////

    SessaoSEI::getInstance()->validarLink();

    PaginaSEI::getInstance()->verificarSelecao('md_pet_int_tipo_intimacao_selecionar');

    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    $objMdPetIntTipoIntimacaoDTO = new MdPetIntTipoIntimacaoDTO();
    $objMdPetIntRelIntimRespDTO = new MdPetIntRelIntimRespDTO();
    $objMdPetIntTipoRespDTO = new MdPetIntTipoRespDTO();

    $strDesabilitar = '';
    $arrComandos = array();
    $arrAcoes = array();

    switch ($_GET['acao']) {
        case 'md_pet_int_tipo_intimacao_cadastrar':
            $strTitulo = 'Novo Tipo de Intimação Eletrônica ';
            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmCadastrarMdPetIntTipoIntimacao" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            $objMdPetIntTipoIntimacaoDTO->setNumIdMdPetIntTipoIntimacao(null);
            $objMdPetIntTipoIntimacaoDTO->setStrNome($_POST['txtNome']);
            $objMdPetIntTipoIntimacaoDTO->setStrTipoRespostaAceita($_POST['rdoResposta']);
            $objMdPetIntTipoIntimacaoDTO->setStrSinAtivo('S');

            $arr = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnTipoResposta']);
            $arrTiposResposta = array();

            $objMdPetIntTipoIntimacaoDTO->setArrObjRelIntRespostaDTO($arr);

            $strTipoResposta = $_POST['hdnTipoResposta'];
            $strEmailAcoes = 'false, true';
            if (isset($_POST['sbmCadastrarMdPetIntTipoIntimacao'])) {
                try {
                    $objMdPetIntTipoIntimacaoRN = new MdPetIntTipoIntimacaoRN();
                    $objMdPetIntTipoIntimacaoDTO = $objMdPetIntTipoIntimacaoRN->cadastrar($objMdPetIntTipoIntimacaoDTO);

                    PaginaSEI::getInstance()->adicionarMensagem(' "' . $objMdPetIntTipoIntimacaoDTO->getNumIdMdPetIntTipoIntimacao() . '" cadastrada com sucesso.');
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . '&id_md_pet_int_tipo_intimacao=' . $objMdPetIntTipoIntimacaoDTO->getNumIdMdPetIntTipoIntimacao() . PaginaSEI::getInstance()->montarAncora($objMdPetIntTipoIntimacaoDTO->getNumIdMdPetIntTipoIntimacao())));
                    die;
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }
            break;

        case 'md_pet_int_tipo_intimacao_alterar':
            $strTitulo = 'Alterar Tipo de Intimação Eletrônica';
            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmAlterarMdPetIntTipoIntimacao" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $strDesabilitar = 'disabled="disabled"';

            $arrMdPetIntRelIntimRespDTO = array();
            $optionTipoResposta = '';
            $strEmailAcoes = 'false, true';
            if (isset($_GET['id_md_pet_int_tipo_intimacao'])) {
                $objMdPetIntTipoIntimacaoDTO->setNumIdMdPetIntTipoIntimacao($_GET['id_md_pet_int_tipo_intimacao']);
                $objMdPetIntTipoIntimacaoDTO->retTodos();
                $objMdPetIntTipoIntimacaoRN = new MdPetIntTipoIntimacaoRN();
                $objMdPetIntTipoIntimacaoDTO = $objMdPetIntTipoIntimacaoRN->consultar($objMdPetIntTipoIntimacaoDTO);

                if ($objMdPetIntTipoIntimacaoDTO == null) {
                    throw new InfraException("Registro não encontrado.");
                }
            } else {
                $strTipoResposta = $_POST['hdnTipoResposta'];
                $objMdPetIntTipoIntimacaoDTO->setNumIdMdPetIntTipoIntimacao($_POST['hdnIdMdPetIntTipoIntimacao']);
                $objMdPetIntTipoIntimacaoDTO->setStrNome($_POST['txtNome']);
                $objMdPetIntTipoIntimacaoDTO->setStrTipoRespostaAceita($_POST['rdoResposta']);
                $objMdPetIntTipoIntimacaoDTO->setStrSinAtivo('S');

                $arr = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnTipoResposta']);
                $arrTiposResposta = array();
                $objMdPetIntTipoIntimacaoDTO->setArrObjRelIntRespostaDTO($arr);

            }

            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($objMdPetIntTipoIntimacaoDTO->getNumIdMdPetIntTipoIntimacao())) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            $strTipoResposta = $_POST['hdnTipoResposta'];
            if (isset($_POST['sbmAlterarMdPetIntTipoIntimacao'])) {
                try {
                    $objMdPetIntTipoIntimacaoRN = new MdPetIntTipoIntimacaoRN();
                    $objMdPetIntTipoIntimacaoRN->alterar($objMdPetIntTipoIntimacaoDTO);
                    PaginaSEI::getInstance()->adicionarMensagem(' "' . $objMdPetIntTipoIntimacaoDTO->getNumIdMdPetIntTipoIntimacao() . '" alterad com sucesso.');
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($objMdPetIntTipoIntimacaoDTO->getNumIdMdPetIntTipoIntimacao())));
                    die;
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }
            break;

        case 'md_pet_int_tipo_intimacao_consultar':
            $strTitulo = 'Consultar Tipo de Intimação Eletrônica';
            $arrComandos[] = '<button type="button" accesskey="C" name="btnFechar" value="Fechar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($_GET['id_md_pet_int_tipo_intimacao'])) . '\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
            $objMdPetIntTipoIntimacaoDTO->setNumIdMdPetIntTipoIntimacao($_GET['id_md_pet_int_tipo_intimacao']);
            $objMdPetIntTipoIntimacaoDTO->setBolExclusaoLogica(false);
            $objMdPetIntTipoIntimacaoDTO->retTodos();
            $objMdPetIntTipoIntimacaoRN = new MdPetIntTipoIntimacaoRN();
            $objMdPetIntTipoIntimacaoDTO = $objMdPetIntTipoIntimacaoRN->consultar($objMdPetIntTipoIntimacaoDTO);
            $strEmailAcoes = 'false, false';
            if ($objMdPetIntTipoIntimacaoDTO === null) {
                throw new InfraException("Registro não encontrado.");
            }
            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }

    if ($_GET['acao'] === 'md_pet_int_tipo_intimacao_alterar' || $_GET['acao'] === 'md_pet_int_tipo_intimacao_consultar') {
        $objMdPetIntRelTipoRespRN = new MdPetIntRelTipoRespRN();
        $objMdPetIntRelIntimRespRN = new MdPetIntRelIntimRespRN();

        $objMdPetIntRelIntimRespDTO->setNumIdMdPetIntTipoIntimacao($_GET['id_md_pet_int_tipo_intimacao']);
        $objMdPetIntRelIntimRespDTO->retTodos(true);
        $objMdPetIntRelIntimRespDTO->setOrdStrNomeMdPetIntTipoResp(InfraDTO::$TIPO_ORDENACAO_ASC);
        $arrMdPetIntRelIntimRespDTO = $objMdPetIntRelIntimRespRN->listar($objMdPetIntRelIntimRespDTO);

        $arrTiposResposta = array();
        foreach ($arrMdPetIntRelIntimRespDTO as $arrDados) {
            if ($arrDados->getStrTipoPrazoExternoMdPetIntTipoResp() == 'N') {
                $prazo = 'Não Possui Prazo Externo';
            } else if ($arrDados->getStrTipoPrazoExternoMdPetIntTipoResp() == 'D') {
              $tipoDia = null;
              if($arrDados->getStrTipoDia() == 'U'){
                $tipoDia = 'Útil';
                if($arrDados->getNumValorPrazoExternoMdPetIntTipoResp() > 1){
                  $tipoDia = 'Úteis';
                }
              }
                $prazo = $arrDados->getNumValorPrazoExternoMdPetIntTipoResp() . ' Dias '.$tipoDia ;
            } else if ($arrDados->getStrTipoPrazoExternoMdPetIntTipoResp() == 'M') {
                $prazo = $arrDados->getNumValorPrazoExternoMdPetIntTipoResp() . ' Meses';
            } else if ($arrDados->getStrTipoPrazoExternoMdPetIntTipoResp() == 'A') {
                $prazo = $arrDados->getNumValorPrazoExternoMdPetIntTipoResp() . ' Anos';
            }

            if ($arrDados->getStrTipoRespostaAceitaMdPetIntTipoResp() == 'E') {
                $resposta = 'Exige Resposta';
            } else {
                $resposta = 'Resposta Facultativa';
            }

            $isVinculado = $objMdPetIntRelTipoRespRN->validarExclusaoTipoResposta($arrDados->getNumIdMdPetIntTipoRespMdPetIntTipoResp());

            $arrTiposResposta[] = array($arrDados->getNumIdMdPetIntTipoRespMdPetIntTipoResp(), $isVinculado, PaginaSEI::getInstance()->formatarParametrosJavaScript( PaginaSEI::tratarHTML( $arrDados->getStrNomeMdPetIntTipoResp() ) ), $prazo, $resposta);
        }

        if (isset($_GET['id_md_pet_int_tipo_intimacao'])) {
            $strTipoResposta = PaginaSEI::getInstance()->gerarItensTabelaDinamica($arrTiposResposta);
        }

        $strItenSelPrazoExterno = MdPetIntTipoRespINT::montarSelectTipoRespostaEU8612($objMdPetIntTipoIntimacaoDTO->getStrTipoRespostaAceita());
    }
    $strLinkAjaxTipoResposta  = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=integracao_tipo_resposta');
    $strUrlBuscaTipoResposta  = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=busca_tipo_resposta');

} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo);
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
?>
<? if (0){ ?>
    <style><?}?>
        #lblNome { position: absolute; left: 0%; top: 0%; width: 30%; }
        #txtNome { position: absolute; left: 0%; top: 45%; width: 40%; }
        #nomeAjuda { position: absolute; left: 31%; top: 45%; width: 2%; }
        #imgAjudaUsuario {position:absolute;left:40px;top:0%; }
        #imgAjudaInputUsuario {position:absolute;left:41%;top:45%; }

        #fldResposta { position: absolute; left: 0%; top: 11%; width: 65%; }
        #selTipoResposta { position: absolute; left: 0%; top: 5%; width: 45%; }
        #imgAjudaTipoResposta {position:absolute;left:115px;top:0%; }

        #sbmGravarTipoResposta { position: absolute; left: 45.5%; top: 5%; width: 55px; }
        #divTabelaTipoResposta { position: absolute; left: 0%; top: 10%; width: 100%; }
        <? if (0){ ?></style><? } ?>
<?
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>
<? if (0){ ?>
    <script type="text/javascript"><?}?>

        function inicializar() {
            if ('<?=$_GET['acao']?>' == 'md_pet_int_tipo_intimacao_cadastrar') {
                document.getElementById('txtNome').focus();
            } else if ('<?=$_GET['acao']?>' == 'md_pet_int_tipo_intimacao_consultar') {
                infraDesabilitarCamposAreaDados();
            } else {
                document.getElementById('btnCancelar').focus();
            }
            infraEfeitoTabelas();

            //Ajax para carregar os Tipos de Resposta
            objAjaxIdTipoResposta = new infraAjaxMontarSelectDependente('optTipoRespostaFacultativa', 'selTipoResposta', '<?=$strLinkAjaxTipoResposta?>');
            objAjaxIdTipoResposta.prepararExecucao = function () {
                objTabelaDinamicaTipoResposta.limpar();
                document.getElementById('hdnTipoResposta').value = '';
                document.getElementById('sbmGravarTipoResposta').removeAttribute("disabled");
                return 'tipoResposta=' + document.getElementById('optTipoRespostaFacultativa').value;
            };
            objAjaxIdTipoResposta = new infraAjaxMontarSelectDependente('optTipoRespostaExige', 'selTipoResposta', '<?=$strLinkAjaxTipoResposta?>');
            objAjaxIdTipoResposta.prepararExecucao = function () {
                objTabelaDinamicaTipoResposta.limpar();
                document.getElementById('hdnTipoResposta').value = '';
                document.getElementById('sbmGravarTipoResposta').removeAttribute("disabled");
                return 'tipoResposta=' + document.getElementById('optTipoRespostaExige').value;
            }

            //Insere as linhas de Tipo de Resposta
            objTabelaDinamicaTipoResposta = new infraTabelaDinamica('tblTipoResposta', 'hdnTipoResposta', <?=$strEmailAcoes?>);
            objTabelaDinamicaTipoResposta.alterar = function (arr) {
                document.getElementById('selTipoResposta').value = arr[0];
                document.getElementById('selTipoResposta').value = arr[1];
                document.getElementById('selTipoResposta').value = arr[2];
            };

            objTabelaDinamicaTipoResposta.remover = function (arr) {

                var id     = arr[0][0];
                var vinculo = arr[1][0];

                if (objTabelaDinamicaTipoResposta.tbl.rows.length == '2') {
                    document.getElementById('divTabelaTipoResposta').style.display = 'none';
                }

                //Adiciona novamente o item ao select
                var id = arr[0];
                $("#selTipoResposta option[value='" + id + "']").show();

                //Habilita o inserir caso o item removido seja Exige Resposta
                if (arr[3] == 'Exige Resposta') {
                    document.getElementById('sbmGravarTipoResposta').removeAttribute("disabled");
                }

                return true;

            };

            objTabelaDinamicaTipoResposta.gerarEfeitoTabela = true;

            <? foreach(array_keys($arrAcoes) as $id) { ?>
            objTabelaDinamicaTipoResposta.adicionarAcoes('<?=$id?>', '<?=$arrAcoes[$id]?>');
            <? } ?>

            infraEfeitoTabelas();
            controlarExibicaoTabela();
        }

        function OnSubmitForm(){
            if (infraTrim(document.getElementById('txtNome').value)=='') {
                alert('Informe o Nome.');
                document.getElementById('txtNome').focus();
                return false;
            }

            if (!document.getElementById('optTipoRespostaFacultativa').checked && !document.getElementById('optTipoRespostaExige').checked && !document.getElementById('optTipoSemResposta').checked) {
                alert('Informe o Tipo de Resposta para a Intimação.');
                document.getElementById('selTipoResposta').focus();
                return false;
            }

            if (document.getElementById('optTipoRespostaFacultativa').checked && document.getElementById('optTipoRespostaExige').checked) {
                qtdResp = document.getElementById('tblTipoResposta').rows.length;
                if(qtdResp <= 1){
                    alert('Selecione pelo menos 1(um) Tipo de Resposta.');
                    return false;
                }
            }

        }

        function esconderTabelaTipoResposta(){
            document.getElementById('divTabelaTipoResposta').style.display = 'none';

            if (document.getElementById('optTipoRespostaFacultativa').checked || document.getElementById('optTipoRespostaExige').checked) {
                document.getElementById('divInfraAreaDados2').style.display = '';
            }else if (document.getElementById('optTipoSemResposta').checked) {
                document.getElementById('divInfraAreaDados2').style.display = 'none';
            }
            esconderTabela();
        }

        function esconderTabela(){
            document.getElementById('divTabelaTipoResposta').style.display = 'none';
        }

        function controlarExibicaoTabela(){
            var hdnTipoResposta = document.getElementById("hdnTipoResposta").value;
            if(hdnTipoResposta == ''){
                document.getElementById('divTabelaTipoResposta').style.display = 'none';
                if (document.getElementById('optTipoSemResposta').checked) {
                    document.getElementById('divInfraAreaDados2').style.display = 'none';
                }
            }else{
                document.getElementById('divTabelaTipoResposta').style.display = '';
            }
        }

        //Transporta os intens do select Para a tabela.
        function transportarTipoResposta(){

            var paramsAjax = {
                id: document.getElementById('selTipoResposta').value
            };

            $.ajax({
                url: '<?=$strUrlBuscaTipoResposta?>',
                type: 'POST',
                dataType: 'XML',
                data: paramsAjax,
                success: function (r) {
                    var prazo = $(r).find('Prazo').html();
                    var prazoFormatado = prazo != '' ? prazo.replace("(", "") : '';
                    prazoFormatado = prazoFormatado != '' ? prazoFormatado.replace(")", "") : '';

                    objTabelaDinamicaTipoResposta.adicionar([$(r).find('Id').text(), $(r).find('Vinculado').text(), $("<pre>").text($(r).find('Nome').html()).html(), $("<pre>").text(prazoFormatado).html(), $(r).find('Tipo').text()]);
                    controlarExibicaoTabela();
                    $("#selTipoResposta option:selected").hide();
                    document.getElementById('selTipoResposta').value = '';

                    document.getElementById('selTipoResposta').focus();
                },
                error: function (e) {
                    console.error('Erro ao processar o XML do SEI: ' + e.responseText);
                }
            });

        }

        <? if (0){ ?></script><? } ?>
<?
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
    <form id="frmMdPetIntTipoIntimacaoCadastro" method="post" action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>"  onsubmit="return OnSubmitForm();">

        <? PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
        PaginaSEI::getInstance()->abrirAreaDados('4em'); ?>
        <label id="lblNome" for="txtNome" accesskey="" class="infraLabelObrigatorio">Nome:</label>
        <input type="text" id="txtNome" name="txtNome" class="infraText" value="<?= PaginaSEI::tratarHTML($objMdPetIntTipoIntimacaoDTO->getStrNome()); ?>" onkeypress="return infraMascaraTexto(this,event,70);" maxlength="70" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
        <img style="padding-left: 2px;" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" id="imgAjudaInputUsuario" <?= PaginaSEI::montarTitleTooltip('Escrever nome que reflita o documento ou decisão que motiva a intimação e não a possível resposta do Usuário Externo. Exemplos: Descisão de 1ª Instância, Decisão de Inadmissibilidade de Recurso, Exigência para Complementação de Informações, Decisão sobre Recurso.') ?> class="infraImg"/>
        <? PaginaSEI::getInstance()->fecharAreaDados();
        PaginaSEI::getInstance()->abrirAreaDados('11em'); ?>
        <fieldset id="fldResposta">
            <legend class="infraLegend"> Tipo de Intimação Aceita Tipo de Resposta</legend>
            <div id="divOptAno" class="infraDivRadio">
                <span id="spnAno"><label id="lblAno" class="infraLabelRadio">
                    <input type="radio" onclick="esconderTabelaTipoResposta()" name="rdoResposta" id="optTipoRespostaFacultativa" value="F" <?= ($objMdPetIntTipoIntimacaoDTO->getStrTipoRespostaAceita() === 'F' ? 'checked="checked"' : '') ?> class="infraRadio" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>Facultativa</label>
                </span>
            </div>
            <br>
            <div id="divOptAno" class="infraDivRadio">
                <span id="spnExige"><label id="lblExige" class="infraLabelRadio">
                    <input type="radio" onclick="esconderTabelaTipoResposta()" name="rdoResposta" id="optTipoRespostaExige" value="E" <?= ($objMdPetIntTipoIntimacaoDTO->getStrTipoRespostaAceita() === 'E' ? 'checked="checked"' : '') ?> class="infraRadio" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>Exige Resposta</label>
                </span> <br>
            </div>
            <br>
            <div id="divOptAno" class="infraDivRadio">
                <span id="spnExige"><label id="lblExige" class="infraLabelRadio">
                    <input type="radio" onclick="esconderTabelaTipoResposta()" name="rdoResposta" id="optTipoSemResposta" value="S" <?= ($objMdPetIntTipoIntimacaoDTO->getStrTipoRespostaAceita() === 'S' ? 'checked="checked"' : '') ?> class="infraRadio" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>Sem Resposta</label>
                </span> <br>
            </div>
        </fieldset>
        <? PaginaSEI::getInstance()->fecharAreaDados();
        PaginaSEI::getInstance()->abrirAreaDados('50em'); ?>
        <label id="lblTipoResposta" for="txtTipoResposta" accesskey="" class="infraLabelObrigatorio">Tipos de Resposta:</label>
        <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" id="imgAjudaTipoResposta" <?= PaginaSEI::montarTitleTooltip('É possível indicar mais de um Tipo de Resposta com Resposta Facultativa pelo Usuário Externo. Somente é possível indicar um Tipo de Resposta que Exige Resposta pelo Usuário Externo.') ?> class="infraImg"/>
        <br>
        <select id="selTipoResposta" name="selTipoResposta" class="infraSelect" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"> <option id=""></option> <?=$strItenSelPrazoExterno ?> </select>
        <button type="button" accesskey="A" name="sbmGravarTipoResposta" id="sbmGravarTipoResposta" value="Adicionar Tipo Resposta" onclick="transportarTipoResposta();" class="infraButton"><span class="infraTeclaAtalho">A</span>dicionar</button>
        <input type="hidden" id="hdnIdTipoResposta" name="hdnIdTipoResposta" value=""/>


        <div id="divTabelaTipoResposta" class="infraAreaTabela" style="<?php echo ($strTipoResposta == '') ? 'display: none' : '';?>" />
            <table id="tblTipoResposta" width="85%" class="infraTable" summary="Lista de Tipos de Respostas">
                <caption class="infraCaption"> Lista de Tipos de Respostas</caption>
                <tr>
                    <th style="display:none;">ID</th>
                    <th style="display:none;">VINCULADO</th>
                    <th class="infraTh" width="60%">Tipo de Resposta</th>
                    <th class="infraTh" width="20px">Prazo Externo</th>
                    <th class="infraTh" width="25px">Resposta do Usuário Externo</th>
                    <th class="infraTh" width="15px">Ações</th>
                </tr>
            </table>
            <input type="hidden" id="hdnIdTipoResposta" name="hdnIdTipoResposta" value=""/>

            <input type="hidden" id="hdnTipoResposta" name="hdnTipoResposta" value="<?= $strTipoResposta; ?>"/>
        </div>

        <? PaginaSEI::getInstance()->fecharAreaDados(); ?>
        <input type="hidden" id="hdnIdMdPetIntTipoIntimacao" name="hdnIdMdPetIntTipoIntimacao" value="<?= $objMdPetIntTipoIntimacaoDTO->getNumIdMdPetIntTipoIntimacao(); ?>"/>
    </form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>