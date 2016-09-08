<?
    /**
     * ANATEL
     *
     * 28/06/2016 - criado por marcelo.bezerra - CAST
     *
     */

    try {
        require_once dirname(__FILE__) . '/../../SEI.php';

        session_start();
        SessaoSEIExterna::getInstance()->validarLink();
        SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);

        switch ($_GET['acao']) {

            case 'recibo_peticionamento_usuario_externo_consultar':

                // PDF - Gerando
                if ($_POST['hdnInfraBarraLocalizacao'] != '' || $_POST['hdnInfraAreaDados'] != '') {

                    //$arr = PaginaSEI::getInstance()->getArrItensTabelaDinamica($_POST['hdnInfraBarraLocalizacao']);
                    $arr = PaginaSEI::getInstance()->getArrItensTabelaDinamica($_POST['hdnInfraAreaDados']);

                    //$pdf = new InfraEtiquetasPDF('contato', 'mm', $_POST['txtColuna'], $_POST['txtLinha']);
                    //$pdf = new InfraEtiquetasPDF('contato', 'mm', 1, 1);
                    require_once dirname(__FILE__) . '/util/InfraReciboPDF.php';
                    $pdf = new InfraReciboPDF('contato', 'mm', 1, 1);


                    $pdf->Open();

                    $pdf->SetFont("arial", "B", "13");
                    $pdf->Set_Font_Size(13);
                    $pdf->Add_PDF_Row($_POST['hdnInfraBarraLocalizacao'], '', 'L', 'V', 1);

                    $pdf->Set_Font_Size(8);
                    $pdf->Add_PDF_Row(' ', '', 'J', 'V', 0);
                    $pdf->Add_PDF_Row(' ', '', 'J', 'V', 1);
                    $pdf->Set_Font_Size(8);
                    $pdf->Add_PDF_Row(' ', '', 'J', 'V', 0);
                    $pdf->Add_PDF_Row(' ', '', 'J', 'V', 1);


                    for ($i = 0; $i < count($arr); $i++) {
                        $recibo = $arr[$i];
                        //$recibolinhas = '';
                        for ($j = 0; $j < count($recibo); $j++) {
                            //$recibolinhas = $recibolinhas . $recibo[$j] . "\n";
                            $recibolinha = explode("|", $recibo[$j]);
                            //TD - primeira
                            $pdf->SetFont("", (strrpos($recibolinha[1], "<b>") > -1 ? "b" : ""), "8");
                            $pdf->Set_Font_Size(8);
                            $pdf->Add_PDF_Row(str_replace('<b>', '', $recibolinha[1]), '', 'J', 'V', 0);
                            //TD - segunda

                            $recibolinha[0] = str_replace('&nbsp;', ' ',$recibolinha[0]);
                            $pdf->SetFont("helvetica", (strrpos($recibolinha[0], "<b>") > -1 ? "b" : ""), "8");
                            $pdf->Set_Font_Size(8);
                            $pdf->Add_PDF_Row(str_replace('<b>', '', $recibolinha[0]), '', 'J', 'V', 1);
                        }
                        //$pdf->Add_PDF_Row($recibolinhas, 'T', 'J', 'V');

                    }
                    $pdf->Set_Font_Size(8);
                    $pdf->Add_PDF_Row(' ', '', 'J', 'V', 0);
                    $pdf->Add_PDF_Row($_POST['hdnRodape'], '', 'L', 'V', 1);
                    $pdf->Output($_POST['hdnInfraBarraLocalizacao'] . '.pdf', 'D');
                    die();

                }
                // PDF - Gerando - FIM


                //Titulo do Protocolo
                $objReciboPeticionamentoDTO = new ReciboPeticionamentoDTO();
                $objReciboPeticionamentoDTO->setNumIdReciboPeticionamento($_GET['id_md_pet_rel_recibo_protoc']);
                $objReciboPeticionamentoDTO->retNumIdProtocolo();
                $objReciboPeticionamentoRN  = new ReciboPeticionamentoRN();
                $objReciboPeticionamentoDTO = $objReciboPeticionamentoRN->consultar($objReciboPeticionamentoDTO);

                $objProtocoloDTO = new ProtocoloDTO();
                $objProtocoloDTO->retDblIdProtocolo();
                $objProtocoloDTO->setDblIdProtocolo($objReciboPeticionamentoDTO->getNumIdProtocolo());
                $objProtocoloDTO->retStrProtocoloFormatado();
                $objProtocoloRN  = new ProtocoloRN();
                $objProtocoloDTO = $objProtocoloRN->consultarRN0186($objProtocoloDTO);
                
                $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
                $idSerieParam = $objInfraParametro->getValor('ID_SERIE_RECIBO_MODULO_PETICIONAMENTO');
                
                //agora vamos obter o documento recibo que estará vinculado ao processo em questão
                $docReciboDTO = new DocumentoDTO();
                $docRN = new DocumentoRN();
                $docReciboDTO->retTodos();
                $docReciboDTO->setDblIdProcedimento( $objProtocoloDTO->getDblIdProtocolo() );
                $docReciboDTO->setNumIdSerie( $idSerieParam );
                $docReciboDTO = $docRN->consultarRN0005( $docReciboDTO );
                //print_r( $docReciboDTO ); die();
                
                if( $docReciboDTO != null ){
                  $strTitulo = 'Recibo Eletrônico de Protocolo - SEI n° ' . $docReciboDTO->getStrNumero();
                } else {
                	$strTitulo = 'Recibo Eletrônico de Protocolo';
                }
                break;


                break;


            default:
                throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
        }

        $arrComandos   = array();
        $arrComandos[] = '<button type="button" id="btnSalvarPDF" value="Salvar em PDF" onclick="salvarPDF();" class="infraButton">Salvar em PDF</button>';
        $arrComandos[] = '<button type="button" id="btnImprimir" value="Imprimir" onclick="imprimir();" class="infraButton">Imprimir</button>';
        $arrComandos[] = '<button type="button" accesskey="F" id="btnFechar" value="Fechar" onclick="location.href=\'' . PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?id_md_pet_rel_recibo_protoc=' . $_GET['id_md_pet_rel_recibo_protoc'] . '&acao=' . PaginaSEIExterna::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'])) . '\'" class="infraButton"><span class="infraTeclaAtalho">F</span>echar</button>';

        $objReciboPeticionamentoDTO = new ReciboPeticionamentoDTO();
        $objReciboPeticionamentoDTO->retTodos();
        $objReciboPeticionamentoDTO->retStrDscUnidadeGeradora();
        
        $objReciboDocumentoAnexoPeticionamentoDTO = new ReciboDocumentoAnexoPeticionamentoDTO();
        $objReciboDocumentoAnexoPeticionamentoDTO->retTodos();
        $objReciboDocumentoAnexoPeticionamentoDTO->retStrNomeSerie();
        $objReciboDocumentoAnexoPeticionamentoDTO->retStrProtocoloFormatado();

        if (isset($_GET['id_md_pet_rel_recibo_protoc']) && $_GET['id_md_pet_rel_recibo_protoc'] != "") {
            $objReciboPeticionamentoDTO->setNumIdReciboPeticionamento($_GET['id_md_pet_rel_recibo_protoc']);
        }

        $objReciboPeticionamentoRN               = new ReciboPeticionamentoRN();
        $objReciboDocumentoAnexoPeticionamentoRN = new ReciboDocumentoAnexoPeticionamentoRN();
        $objReciboPeticionamentoDTO              = $objReciboPeticionamentoRN->consultar($objReciboPeticionamentoDTO);

        //obtendo a lista de documentos vinculados ao recibo
        $objReciboDocumentoAnexoPeticionamentoDTO->setNumIdReciboPeticionamento($objReciboPeticionamentoDTO->getNumIdReciboPeticionamento());
        $arrDocumentos = $objReciboDocumentoAnexoPeticionamentoRN->listar($objReciboDocumentoAnexoPeticionamentoDTO);
		        
        $idUsuario  = $objReciboPeticionamentoDTO->getNumIdUsuario();
        $usuarioDTO = new UsuarioDTO();
        $usuarioRN  = new UsuarioRN();
        $usuarioDTO->retNumIdUsuario();
        $usuarioDTO->retStrNome();
        $usuarioDTO->setNumIdUsuario($idUsuario);
        $usuarioDTO = $usuarioRN->consultarRN0489($usuarioDTO);

        $protocoloRN  = new ProtocoloRN();
        $protocoloDTO = new ProtocoloDTO();
        $protocoloDTO->retDblIdProtocolo();
        $protocoloDTO->retStrProtocoloFormatado();
        $protocoloDTO->setDblIdProtocolo($objReciboPeticionamentoDTO->getNumIdProtocolo());
        $protocoloDTO = $protocoloRN->consultarRN0186($protocoloDTO);

        //obter documentos principais

        //obter documentos essenciais e complementares


        //obter interessados
        $objParticipanteDTO = new ParticipanteDTO();
        $objParticipanteDTO->setDblIdProtocolo($objReciboPeticionamentoDTO->getNumIdProtocolo());
        $objParticipanteDTO->retNumIdContato();
        $objParticipanteRN     = new ParticipanteRN();
        $arrObjParticipanteDTO = $objParticipanteRN->listarRN0189($objParticipanteDTO);

        foreach ($arrObjParticipanteDTO as $objParticipanteDTO) {
            $objContatoDTO = new ContatoDTO();
            $objContatoDTO->setNumIdContato($objParticipanteDTO->getNumIdContato());
            $objContatoDTO->retStrNome();
            $objContatoRN      = new ContatoRN();
            $arrInteressados[] = $objContatoRN->consultarRN0324($objContatoDTO);
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
?>
    function imprimir() {
        document.getElementById('btnFechar').style.display = 'none';
        document.getElementById('btnImprimir').style.display = 'none';
        document.getElementById('btnSalvarPDF').style.display = 'none';
        infraImprimirDiv('divInfraAreaTelaD');

        self.setTimeout(function () {
            document.getElementById('btnFechar').style.display = '';
            document.getElementById('btnImprimir').style.display = '';
            document.getElementById('btnSalvarPDF').style.display = '';
        }, 1000);
    }

    function inicializar() {

        if ('<?= $_GET['acao'] ?>' == 'recibo_peticionamento_usuario_externo_selecionar') {
            infraReceberSelecao();
            document.getElementById('btnFecharSelecao').focus();
        } else {
            document.getElementById('btnFechar').focus();
        }

        infraEfeitoTabelas();
    }

    function pesquisar() {
        document.getElementById('frmLista').submit();
    }

    function salvarPDF() {
        document.getElementById('hdnInfraBarraLocalizacao').value = document.getElementById('divInfraBarraLocalizacao').innerHTML;
        document.getElementById('hdnRodape').value = document.getElementById('divRodape').innerHTML.trim();

//document.getElementById('hdnInfraAreaDados').value = document.getElementById('divInfraAreaDados').innerHTML;
        var tabela = document.getElementById('divInfraAreaDados').getElementsByTagName('TABLE');
        if (tabela.length > 0) {
            var tabl = tabela[0]; // console.log(tabl);

            var l = tabl.rows.length; //console.log(l);
            var s = '';
            var td1maior = 0;
            var td2maior = 0;

            for (var i = 0; i < l; i++) {
                var tr = tabl.rows[i];
                if (tr.childNodes[1].style.fontWeight.indexOf('bold') > -1) {
                    s += '<b>';
                }
                s += tr.childNodes[1].innerHTML + '|'
                if (tr.childNodes[3].style.fontWeight.indexOf('bold') > -1) {
                    s += '<b>';
                }
                s += tr.childNodes[3].innerHTML + '±';
            }
            document.getElementById('hdnInfraAreaDados').value = s;
        }

        document.getElementById('frmLista').submit();
    }

<?
    PaginaSEIExterna::getInstance()->fecharJavaScript();
    PaginaSEIExterna::getInstance()->fecharHead();
    PaginaSEIExterna::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
<form id="frmLista" method="post"
      action="<?= PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=' . $_GET['acao'])) ?>">

    <? PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos); ?>

    <div style="height:auto; margin-top: 11px;" class="infraAreaDados" id="divInfraAreaDados">

        <table width="80%" style="width: 80%" border="0">

            <tr>
                <td style="font-weight: bold; width: 280px;" width="280">Usuário Externo (signatário):</td>
                <td><?= $usuarioDTO->getStrNome() ?></td>
            </tr>

            <tr>
                <td style="font-weight: bold;">IP utilizado:</td>
                <td><?= $objReciboPeticionamentoDTO->getStrIpUsuario() ?></td>
            </tr>

            <tr>
                <td style="font-weight: bold;">Tipo de Peticionamento:</td>
                <td><?= $objReciboPeticionamentoDTO->getStrTipoPeticionamento() ?></td>
            </tr>

            <tr>
                <td style="font-weight: bold;">Data e horário (recebimento final pelo SEI):</td>
                <td><?= $objReciboPeticionamentoDTO->getDthDataHoraRecebimentoFinal() ?></td>
            </tr>

            <tr>
                <td style="font-weight: bold;">Número do processo:</td>
                <td><?= $protocoloDTO->getStrProtocoloFormatado() ?></td>
            </tr>

            <?php if ($arrInteressados != null && is_array($arrInteressados) && count($arrInteressados) > 0) : ?>
                <tr>
                    <td style="font-weight: bold;">Interessados:</td>
                    <td></td>
                </tr>
                <?php foreach ($arrInteressados as $interessado) : ?>
                    <tr>
                        <td>&nbsp&nbsp&nbsp&nbsp<?= $interessado->getStrNome() ?> </td>
                        <td></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

            <tr>
                <td style="font-weight: bold;">Protocolos dos Documentos (Número SEI):</td>
                <td></td>
            </tr>


            <!--  lista DOC PRINCIPAL -->
            <?php if ($arrDocumentos != null && is_array($arrDocumentos) && count($arrDocumentos) > 0): ?>
                <tr>
                    <td style="font-weight: bold;">- Documento Principal:</td>
                    <td></td>
                </tr>
                <?php foreach ($arrDocumentos as $documento) : ?>

                    <?php if ($documento->getStrClassificacaoDocumento() == "P"): ?>
                        <tr>
                            <td>&nbsp&nbsp&nbsp&nbsp- <?= $documento->getStrNomeSerie() ?> </td>
                            <td> <?= $documento->getStrProtocoloFormatado() ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>

            <?php endif; ?>

            <!-- lista DOC ESSENCIAL -->
            <?php if ($arrDocumentos != null && is_array($arrDocumentos) && count($arrDocumentos) > 0): ?>
                <tr>
                    <td style="font-weight: bold;">- Documentos Essenciais:</td>
                    <td></td>
                </tr>

                <?php foreach ($arrDocumentos as $documento) : ?>
                    <!-- E-ESSENCIAL-->
                    <?php if ($documento->getStrClassificacaoDocumento() == "E") : ?>
                        <tr>
                            <td>&nbsp&nbsp&nbsp&nbsp- <?= $documento->getStrNomeSerie() ?> </td>
                            <td> <?= $documento->getStrProtocoloFormatado() ?></td>
                        </tr>
                    <?php endif ?>
                <?php endforeach; ?>
            <?php endif; ?>

            <!--  lista DOC COMPLEMENTAR -->
            <?php if ($arrDocumentos != null && is_array($arrDocumentos) && count($arrDocumentos) > 0): ?>
                <tr>
                    <td style="font-weight: bold;">- Documentos Complementares:</td>
                    <td></td>
                </tr>
                <?php foreach ($arrDocumentos as $documento) : ?>
                    <!--C = COMPLEMENTAR-->
                    <?php if ($documento->getStrClassificacaoDocumento() == "C") : ?>
                        <tr>
                            <td>&nbsp&nbsp&nbsp&nbsp- <?= $documento->getStrNomeSerie() ?> </td>
                            <td> <?= $documento->getStrProtocoloFormatado() ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>

        </table>
        <br/>
        <br/>
        <label>
            A existência deste Recibo, do processo e dos documentos acima indicados pode ser conferida na Página Eletrônica do(a) <?= htmlentities($objReciboPeticionamentoDTO->getStrDscUnidadeGeradora()); ?>
        </label>

    </div>

    <input type="text" id=hdnInfraBarraLocalizacao name=hdnInfraBarraLocalizacao style="visibility: hidden;"/>
    <input type="text" id=hdnInfraAreaDados name=hdnInfraAreaDados style="visibility: hidden;"/>
    <input type="text" id=hdnRodape name=hdnRodape style="visibility: hidden;"/>

</form>
<?
    PaginaSEIExterna::getInstance()->fecharBody();
    PaginaSEIExterna::getInstance()->fecharHtml();
?>
