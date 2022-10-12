<?
/**
 * ANATEL
 *
 * 28/06/2016 - criado por marcelo.bezerra - CAST
 *
 */

try {

    require_once dirname(__FILE__) . '/../../SEI.php';

    //Classe utilitaria para formataçao e calculo de Datas
    require_once dirname(__FILE__) . '/util/MdPetDataUtils.php';

    session_start();

    //////////////////////////////////////////////////////////////////////////////
    InfraDebug::getInstance()->setBolLigado(false);
    InfraDebug::getInstance()->setBolDebugInfra(false);
    InfraDebug::getInstance()->limpar();
    //////////////////////////////////////////////////////////////////////////////

    SessaoSEIExterna::getInstance()->validarLink();
    PaginaSEIExterna::getInstance()->prepararSelecao('recibo_peticionamento_usuario_externo_selecionar');
    SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);

    switch ($_GET['acao']) {

        case 'recibo_peticionamento_usuario_externo_selecionar':

            $strTitulo = PaginaSEIExterna::getInstance()->getTituloSelecao('Selecionar Recibo', 'Selecionar Recibos');
            break;

        case 'md_pet_usu_ext_recibo_listar':

            $strTitulo = 'Recibos Eletrônicos de Protocolo';
            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }

    $objMdPetCertidaoRN = new MdPetIntCertidaoRN();
    $arrComandos = array();
    $arrComandos[] = '<button type="button" accesskey="p" id="btnPesquisar" value="Pesquisar" onclick="pesquisar();" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';
    $arrComandos[] = '<button type="button" accesskey="c" id="btnFechar" value="Fechar" onclick="location.href=\'' . PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=usuario_externo_controle_acessos&acao_origem=' . $_GET['acao'])) . '\'" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

    $objMdPetReciboDTO = new MdPetReciboDTO();
    $objMdPetReciboDTO->retTodos();
    $objMdPetReciboDTO->retStrNumeroProcessoFormatadoDoc();

    $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
    $strVersaoModuloPeticionamento = $objInfraParametro->getValor('VERSAO_MODULO_PETICIONAMENTO', false);

    if ($strVersaoModuloPeticionamento != '1.1.0') {
        $objMdPetReciboDTO->unRetDblIdProtocoloRelacionado();
    }

    $objMdPetReciboDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());

    //txtDataInicio
    if (isset($_POST['txtDataInicio']) && $_POST['txtDataInicio'] != "") {
        $objMdPetReciboDTO->setDthInicial($_POST['txtDataInicio']);
    }

    //txtDataFim
    if (isset($_POST['txtDataFim']) && $_POST['txtDataFim'] != "") {
        $objMdPetReciboDTO->setDthFinal($_POST['txtDataFim']);
    }

    if (isset($_POST['selTipo']) && $_POST['selTipo'] != "") {
        $objMdPetReciboDTO->setStrStaTipoPeticionamento($_POST['selTipo']);
    }

    PaginaSEIExterna::getInstance()->prepararOrdenacao($objMdPetReciboDTO, 'DataHoraRecebimentoFinal', InfraDTO::$TIPO_ORDENACAO_DESC);

    PaginaSEIExterna::getInstance()->prepararPaginacao($objMdPetReciboDTO, 200);

    $objMdPetReciboRN = new MdPetReciboRN();
    $arrObjMdPetReciboDTO = $objMdPetReciboRN->listar($objMdPetReciboDTO);

    PaginaSEIExterna::getInstance()->processarPaginacao($objMdPetReciboDTO);

    $numRegistros = count($arrObjMdPetReciboDTO);

    if ($numRegistros > 0) {

        $bolAcaoConsultar = SessaoSEIExterna::getInstance()->verificarPermissao('md_pet_usu_ext_recibo_consultar');

        $strResultado = '';
        $strSumarioTabela = 'Tabela de Recibos.';
        $strCaptionTabela = 'Recibos';

        $strResultado .= '<table width="100%" class="infraTable" summary="' . $strSumarioTabela . '">' . "\n";
        $strResultado .= '<caption class="infraCaption">' . PaginaSEIExterna::getInstance()->gerarCaptionTabela($strCaptionTabela, $numRegistros) . '</caption>';
        $strResultado .= '<tr>';

        $strResultado .= '<th class="infraTh" width="15%">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetReciboDTO, 'Data e Horário', 'DataHoraRecebimentoFinal', $arrObjMdPetReciboDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh" width="20%" style="min-width:160px">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetReciboDTO, 'Número do Processo', 'NumeroProcessoFormatado', $arrObjMdPetReciboDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh" style="width:100px ">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetReciboDTO, 'Recibo', 'NumeroProcessoFormatadoDoc', $arrObjMdPetReciboDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh" width="58%">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetReciboDTO, 'Tipo de Peticionamento', 'StaTipoPeticionamento', $arrObjMdPetReciboDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh" width="15px">Ações</th>' . "\n";
        $strResultado .= '</tr>' . "\n";
        $strCssTr = '';

        $protocoloRN = new ProtocoloRN();

        for ($i = 0; $i < $numRegistros; $i++) {
            $protocoloDTO = new ProtocoloDTO();
            $protocoloDTO->retDblIdProtocolo();
            $protocoloDTO->retStrProtocoloFormatado();
            $protocoloDTO->setDblIdProtocolo($arrObjMdPetReciboDTO[$i]->getNumIdProtocolo());
            $protocoloDTO = $protocoloRN->consultarRN0186($protocoloDTO);

            if (isset($_GET['id_md_pet_rel_recibo_protoc']) && $_GET['id_md_pet_rel_recibo_protoc'] == $arrObjMdPetReciboDTO[$i]->getNumIdReciboPeticionamento()) {
                $strCssTr = '<tr class="infraTrAcessada">';
            } else {
                if ($arrObjMdPetReciboDTO[$i]->getStrSinAtivo() == 'S') {
                    $strCssTr = ($strCssTr == '<tr class="infraTrClara">') ? '<tr class="infraTrEscura">' : '<tr class="infraTrClara">';
                } else {
                    $strCssTr = '<tr class="trVermelha">';
                }
            }

            $strResultado .= $strCssTr;
            $data = '';

            if ($arrObjMdPetReciboDTO[$i] != null && $arrObjMdPetReciboDTO[$i]->getDthDataHoraRecebimentoFinal() != "") {
                $data = $arrObjMdPetReciboDTO[$i]->getDthDataHoraRecebimentoFinal();
            }

            $strResultado .= '<td>' . $data . '</td>';

            if ($protocoloDTO != null && $protocoloDTO->isSetStrProtocoloFormatado()) {
                $strResultado .= '<td>' . $protocoloDTO->getStrProtocoloFormatado() . '</td>';
            } else {
                $strResultado .= '<td></td>';
            }

            $strResultado .= '<td>' . $arrObjMdPetReciboDTO[$i]->getStrNumeroProcessoFormatadoDoc() . '</td>';

            $strResultado .= '<td>' . $arrObjMdPetReciboDTO[$i]->getStrStaTipoPeticionamentoFormatado() . '</td>';

            $strResultado .= '<td align="center">';

            $intercorrente = $arrObjMdPetReciboDTO[$i]->isSetStrStaTipoPeticionamento() && ($arrObjMdPetReciboDTO[$i]->getStrStaTipoPeticionamento() == MdPetReciboRN::$TP_RECIBO_INTERCORRENTE) ? true : false;

            $isResposta = $arrObjMdPetReciboDTO[$i]->isSetStrStaTipoPeticionamento() && ($arrObjMdPetReciboDTO[$i]->getStrStaTipoPeticionamento() == MdPetReciboRN::$TP_RECIBO_RESPOSTA_INTIMACAO) ? true : false;

            $isValido = $objMdPetCertidaoRN->verificaDocumentoEAnexoIntimacaoNaoCumprida(array($arrObjMdPetReciboDTO[$i]->getDblIdDocumento(), false, SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()));

            $iconeConsulta = '<img src="' . PaginaSEIExterna::getInstance()->getDiretorioSvgGlobal() . '/consultar.svg?'.Icone::VERSAO.'" title="Consultar Recibo" alt="Consultar Recibo" class="infraImg" />';

            if ($isValido) {
                $linkAssinado = '';
                if ($bolAcaoConsultar) {
                    $objMdPetReciboRN = new MdPetReciboRN();
                    $linkAssinado = $objMdPetReciboRN->getUrlRecibo(array($intercorrente, $arrObjMdPetReciboDTO[$i]));
                }

                if ($linkAssinado != '') {
                    $strResultado .= '<a target="_blank" href="' . $linkAssinado . '">' . $iconeConsulta . '</a>';
                }
            } else {

                $linkAssinado = "javascript:;";
                $msg = 'Recibo Eletrônico bloqueado, pois está vinculado a uma Intimação ainda não Cumprida.';
                $strResultado .= '<a onclick="alert(\'' . $msg . '\')" href="' . $linkAssinado . '">' . $iconeConsulta . '</a>';
            }


            $strResultado .= '</td></tr>' . "\n";
        }

        $strResultado .= '</table>';
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
?>


<?
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
?>

    function inicializar(){

    if ('<?= $_GET['acao'] ?>'=='recibo_peticionamento_usuario_externo_selecionar'){
    infraReceberSelecao();
    document.getElementById('btnFecharSelecao').focus();
    }else{
    document.getElementById('btnFechar').focus();
    }

    infraEfeitoTabelas();
    }

    function pesquisar(){
    document.getElementById('frmLista').submit();
    }

<?
PaginaSEIExterna::getInstance()->fecharJavaScript();
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
$strTipo = $_POST['selTipo'];
?>
    <form id="frmLista" method="post"
          action="<?= PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=' . $_GET['acao'])) ?>">

        <? PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos); ?>

        <div style="height:4.5em; margin-top: 11px;" class="infraAreaDados" id="divInfraAreaDados">

            <!--  Inicio -->
            <div class="divGeral">
                <div class="row justify-content-md-left">
                    <div class="col-sm-12 col-md-4 col-lg-2 col-xl-2">
                        <div class="form-group">
                            <label for="txtDataInicio" class="infraLabelOpcional">Início: </label>
                            <div class="input-group mb-3">
                                <input type="text" name="txtDataInicio" id="txtDataInicio" maxlength="16"
                                       value="<?= PaginaSEIExterna::tratarHTML($_POST['txtDataInicio']) ?>"
                                       class="infraText form-control" onchange="validDate('F');"
                                       onkeypress="return infraMascara(this, event, '##/##/#### ##:##');"
                                       maxlength="16"/>
                                <img src="<?= PaginaSEIExterna::getInstance()->getDiretorioSvgGlobal(); ?>/calendario.svg?<?= Icone::VERSAO ?>"
                                     id="imgDtInicio"
                                     title="Selecionar Data/Hora Inicial"
                                     alt="Selecionar Data/Hora Inicial" class="infraImg"
                                     onclick="infraCalendario('txtDataInicio',this,true,'<?= InfraData::getStrDataAtual() . ' 00:00' ?>');"/>

                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4 col-lg-2 col-xl-2">
                        <div class="form-group">
                            <label for="txtDataFim" class="infraLabelOpcional">Fim:</label>
                            <div class="input-group mb-3">
                                <input type="text" name="txtDataFim" id="txtDataFim"
                                       value="<?= PaginaSEIExterna::tratarHTML($_POST['txtDataFim']) ?>"
                                       class="infraText form-control" onchange="validDate('F');"
                                       onkeypress="return infraMascara(this, event, '##/##/#### ##:##');"
                                       maxlength="16"/>
                                <img src="<?= PaginaSEIExterna::getInstance()->getDiretorioSvgGlobal(); ?>/calendario.svg?<?= Icone::VERSAO ?>"
                                     id="imgDtFim"
                                     title="Selecionar Data/Hora Final"
                                     alt="Selecionar Data/Hora Final"
                                     class="infraImg"
                                     onclick="infraCalendario('txtDataFim',this,true,'<?= InfraData::getStrDataAtual() . ' 23:59' ?>');"/>

                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-4 col-lg-5 col-xl-5">
                        <div class="form-group">
                            <label for="selTipo" class="infraLabelOpcional">Tipo de Peticionamento:</label>
                            <select onchange="pesquisar()" id="selTipo" name="selTipo" class="infraSelect form-control">
                                <option <? if ($_POST['selTipo'] == ""){ ?> selected="selected" <? } ?>"
                                value="">Todos</option>
                                <option <? if ($_POST['selTipo'] == MdPetReciboRN::$TP_RECIBO_NOVO) { ?> selected="selected" <? } ?>
                                        value="<?= MdPetReciboRN::$TP_RECIBO_NOVO ?>">Processo Novo
                                </option>
                                <option <? if ($_POST['selTipo'] == MdPetReciboRN::$TP_RECIBO_INTERCORRENTE) { ?> selected="selected" <? } ?>
                                        value="<?= MdPetReciboRN::$TP_RECIBO_INTERCORRENTE ?>">Intercorrente
                                </option>
                                <option <? if ($_POST['selTipo'] == MdPetReciboRN::$TP_RECIBO_RESPOSTA_INTIMACAO) { ?> selected="selected" <? } ?>
                                        value="<?= MdPetReciboRN::$TP_RECIBO_RESPOSTA_INTIMACAO ?>">Resposta a Intimação
                                </option>
                                <option <? if ($_POST['selTipo'] == MdPetReciboRN::$TP_RECIBO_RESPONSAVEL_LEGAL_INICIAL) { ?> selected="selected" <? } ?>
                                        value="<?= MdPetReciboRN::$TP_RECIBO_RESPONSAVEL_LEGAL_INICIAL ?>">Responsável
                                    Legal - Inicial
                                </option>
                                <option <? if ($_POST['selTipo'] == MdPetReciboRN::$TP_RECIBO_RESPONSAVEL_LEGAL_ALTERACAO) { ?> selected="selected" <? } ?>
                                        value="<?= MdPetReciboRN::$TP_RECIBO_RESPONSAVEL_LEGAL_ALTERACAO ?>">Responsável
                                    Legal - Alteração
                                </option>
                                <option <? if ($_POST['selTipo'] == MdPetReciboRN::$TP_RECIBO_ATUALIZACAO_ATOS_CONSTITUTIVOS) { ?> selected="selected" <? } ?>
                                        value="<?= MdPetReciboRN::$TP_RECIBO_ATUALIZACAO_ATOS_CONSTITUTIVOS ?>">
                                    Atualização de Atos Constitutivos
                                </option>
                                <option <? if ($_POST['selTipo'] == MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_EMISSAO) { ?> selected="selected" <? } ?>
                                        value="<?= MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_EMISSAO ?>">
                                    Procuração Eletrônica - Emissão
                                </option>
                                <option <? if ($_POST['selTipo'] == MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_RENUNCIA) { ?> selected="selected" <? } ?>
                                        value="<?= MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_RENUNCIA ?>">
                                    Procuração Eletrônica - Renúncia
                                </option>
                                <option <? if ($_POST['selTipo'] == MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_REVOGACAO) { ?> selected="selected" <? } ?>
                                        value="<?= MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_REVOGACAO ?>">
                                    Procuração Eletrônica - Revogação
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <input type="submit" style="visibility: hidden;"/>
            <div class="row justify-content-md-left">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <?
                    PaginaSEIExterna::getInstance()->montarAreaTabela($strResultado, $numRegistros);
                    PaginaSEIExterna::getInstance()->montarBarraComandosInferior($arrComandos);
                    PaginaSEIExterna::getInstance()->montarAreaDebug();
                    ?>
                </div>
            </div>
        </div>
    </form>
<?
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
?>
