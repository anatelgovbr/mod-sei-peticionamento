<?
/**
 * ANATEL
 *
 * 06/12/2016 - criado por jaqueline.mendes@castgroup.com.br - CAST
 *
 */

try {

    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();
    SessaoSEIExterna::getInstance()->validarLink();
    SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);

    switch ($_GET['acao']) {

        case 'md_pet_intercorrente_usu_ext_recibo_consultar':

            // PDF - Gerando
            if ($_POST['hdnInfraBarraLocalizacao'] != '' || $_POST['hdnInfraAreaDados'] != '') {

                $arr = PaginaSEI::getInstance()->getArrItensTabelaDinamica($_POST['hdnInfraAreaDados']);

                require_once dirname(__FILE__) . '/util/MdPetInfraReciboPDF.php';
                $pdf = new MdPetInfraReciboPDF('contato', 'mm', 1, 1);

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

                    for ($j = 0; $j < count($recibo); $j++) {

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

                }
                $pdf->Set_Font_Size(8);
                $pdf->Add_PDF_Row(' ', '', 'J', 'V', 0);
                $pdf->Add_PDF_Row($_POST['hdnRodape'], '', 'L', 'V', 1);
                $pdf->Output($_POST['hdnInfraBarraLocalizacao'] . '.pdf', 'D');
                die();

            }
            // PDF - Gerando - FIM

            //Titulo do Protocolo
            $objMdPetReciboDTO = new MdPetReciboDTO();
            $objMdPetReciboDTO->setNumIdReciboPeticionamento($_GET['id_md_pet_rel_recibo_protoc']);
            $objMdPetReciboDTO->retNumIdProtocolo();
            $objMdPetReciboDTO->retDblIdDocumento();
            $objMdPetReciboRN  = new MdPetReciboRN();
            $objMdPetReciboDTO = $objMdPetReciboRN->consultar($objMdPetReciboDTO);
            
            $objProtocoloDTO = new ProtocoloDTO();
            $objProtocoloDTO->retDblIdProtocolo();
            $objProtocoloDTO->setDblIdProtocolo($objMdPetReciboDTO->getNumIdProtocolo());
            $objProtocoloDTO->retStrProtocoloFormatado();
            $objProtocoloRN  = new ProtocoloRN();
            $objProtocoloDTO = $objProtocoloRN->consultarRN0186($objProtocoloDTO);

            $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
            $idSerieParam = $objInfraParametro->getValor(MdPetIntSerieRN::$MD_PET_ID_SERIE_RECIBO);

            $documentoRN = new DocumentoRN();
            $documentoReciboDTO = new DocumentoDTO();
            $documentoReciboDTO->retStrProtocoloDocumentoFormatado();
            $documentoReciboDTO->setDblIdDocumento( $objMdPetReciboDTO->getDblIdDocumento() );
            
            $documentoReciboDTO = $documentoRN->consultarRN0005( $documentoReciboDTO );
            
            if( $documentoReciboDTO != null ){
                $strTitulo = 'Recibo Eletr�nico de Protocolo - SEI n� ' . $documentoReciboDTO->getStrProtocoloDocumentoFormatado();
            } else {
                $strTitulo = 'Recibo Eletr�nico de Protocolo';
            }

            break;

        default:
            throw new InfraException("A��o '" . $_GET['acao'] . "' n�o reconhecida.");
    }

    $arrComandos   = array();
    // Bot�o SALVAR EM PDF desativado temporariamente at� resolver a falta de tratamento HTML
    $arrComandos[] = '<button type="button" accesskey="i" id="btnImprimir" value="Imprimir" onclick="imprimir();" class="infraButton"><span class="infraTeclaAtalho">I</span>mprimir</button>';
    
    $arrComandos[] = '<button type="button" accesskey="c" id="btnFechar" value="Fechar" onclick="location.href=\'' . PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?id_md_pet_rel_recibo_protoc=' . $_GET['id_md_pet_rel_recibo_protoc'] . '&acao=' . PaginaSEIExterna::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'])) . '\'" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

    $objMdPetReciboDTO = new MdPetReciboDTO();
    $objMdPetReciboDTO->retTodos();
    $objMdPetReciboDTO->retStrDscUnidadeGeradora();

    $objMdPetRelReciboDocumentoAnexoDTO = new MdPetRelReciboDocumentoAnexoDTO();
    $objMdPetRelReciboDocumentoAnexoDTO->retTodos();
    $objMdPetRelReciboDocumentoAnexoDTO->retStrNumeroDocumento();
    $objMdPetRelReciboDocumentoAnexoDTO->retStrNomeSerie();
    $objMdPetRelReciboDocumentoAnexoDTO->retStrProtocoloFormatado();

    if (isset($_GET['id_md_pet_rel_recibo_protoc']) && $_GET['id_md_pet_rel_recibo_protoc'] != "") {
        $objMdPetReciboDTO->setNumIdReciboPeticionamento($_GET['id_md_pet_rel_recibo_protoc']);
    }

    //usuarios so podem ver peticionamentos feitos por ele mesmo
    $objMdPetReciboDTO->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );

    $objMdPetReciboRN = new MdPetReciboRN();
    $objMdPetRelReciboDocumentoAnexoRN = new MdPetRelReciboDocumentoAnexoRN();
    $objMdPetReciboDTO = $objMdPetReciboRN->consultar($objMdPetReciboDTO);

    //obtendo a lista de documentos vinculados ao recibo
    $objMdPetRelReciboDocumentoAnexoDTO->setNumIdReciboPeticionamento($objMdPetReciboDTO->getNumIdReciboPeticionamento());
    $arrDocumentos = $objMdPetRelReciboDocumentoAnexoRN->listar($objMdPetRelReciboDocumentoAnexoDTO);

    $idUsuario  = $objMdPetReciboDTO->getNumIdUsuario();
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
    $protocoloDTO->retNumIdOrgaoUnidadeGeradora();
    $protocoloDTO->setDblIdProtocolo($objMdPetReciboDTO->getNumIdProtocolo());
    $protocoloDTO = $protocoloRN->consultarRN0186($protocoloDTO);

    //obter interessados (do tipo interessado, nao os do tipo rementente)
    $objParticipanteDTO = new ParticipanteDTO();
    $objParticipanteDTO->setDblIdProtocolo($objMdPetReciboDTO->getNumIdProtocolo());
    $objParticipanteDTO->setStrStaParticipacao( ParticipanteRN::$TP_INTERESSADO );
    $objParticipanteDTO->retNumIdContato();
    $objParticipanteRN     = new ParticipanteRN();
    $arrObjParticipanteDTO = $objParticipanteRN->listarRN0189($objParticipanteDTO);

    foreach ($arrObjParticipanteDTO as $objParticipanteDTO) {
        $objContatoDTO = new ContatoDTO();
        $objContatoDTO->setNumIdContato($objParticipanteDTO->getNumIdContato());
        $objContatoDTO->retStrNome();
        $objContatoDTO->setBolExclusaoLogica(false);
        $objContatoRN      = new ContatoRN();
        $arrInteressados[] = $objContatoRN->consultarRN0324($objContatoDTO);
    }

    //obtendo descricao do orgao para o rodape do recibo
    $OrgaoRN = new OrgaoRN();
    $OrgaoDTO = new OrgaoDTO();
    $OrgaoDTO->retTodos();
    $OrgaoDTO->setNumIdOrgao(  $protocoloDTO->getNumIdOrgaoUnidadeGeradora() );
    $OrgaoDTO = $OrgaoRN->consultarRN1352( $OrgaoDTO );

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
    // Bot�o SALVAR EM PDF desativado temporariamente at� resolver a falta de tratamento HTML
    // document.getElementById('btnSalvarPDF').style.display = 'none';
    infraImprimirDiv('divInfraAreaTelaD');

    self.setTimeout(function () {
    document.getElementById('btnFechar').style.display = '';
    document.getElementById('btnImprimir').style.display = '';
    // Bot�o SALVAR EM PDF desativado temporariamente at� resolver a falta de tratamento HTML
    // document.getElementById('btnSalvarPDF').style.display = '';
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
    var tabl = tabela[0];
    var s = '';

    for (var i = 0; i < tabl.rows.length; i++) {
    // linha
    var tr = tabl.rows[i];

    if (tr.cells.length>0){
    for (var j = 0; j < tr.cells.length; j++) {
    if (j>0) {
    s += '|';
    }
    if (tr.cells[j].style.fontWeight.indexOf('bold') > -1) {
    s += '<b>';
    }
    s += tr.cells[j].innerHTML;
    }
    s += '�';
    }
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

            <table align="center" style="width: 95%" border="0">

                <tr>
                    <td style="font-weight: bold; width: 400px;">Usu�rio Externo (signat�rio):</td>
                    <td><?= $usuarioDTO->getStrNome() ?></td>
                </tr>

                <tr>
                    <td style="font-weight: bold;">Data e Hor�rio:</td>
                    <td><?= $objMdPetReciboDTO->getDthDataHoraRecebimentoFinal() ?></td>
                </tr>

                <tr>
                    <td style="font-weight: bold;">Tipo de Peticionamento:</td>
                    <td><?= $objMdPetReciboDTO->getStrStaTipoPeticionamentoFormatado() ?></td>
                </tr>

                <tr>
                    <td style="font-weight: bold;">N�mero do Processo:</td>
                    <td><?= $protocoloDTO->getStrProtocoloFormatado() ?></td>
                </tr>

                <?php
                $idProtocoloPrinc = $objMdPetReciboDTO->getDblIdProtocoloRelacionado();
                
                if(!(InfraString::isBolVazia($idProtocoloPrinc))){

                    $objProtocoloRN = new ProtocoloRN();
                    $objProtocoloDTO = new ProtocoloDTO();

                    $objProtocoloDTO->setDblIdProtocolo($idProtocoloPrinc);
                    $objProtocoloDTO->retTodos();

                    $objProtocoloDTO = $objProtocoloRN->consultarRN0186($objProtocoloDTO);
                    $protocoloRelFormatado = $objProtocoloDTO->getStrProtocoloFormatado(); ?>
                    <tr>
                        <td> &ensp;&nbsp; Relacionado ao Processo Indicado:</td>
                        <td><?= $protocoloRelFormatado ?></td>
                    </tr>
				<?php 
				//se for recibo do resposta acrescenta os campos: Tipo de Resposta, Tipo de Intima��o, Documento Principal da Intima��o
				if( $objMdPetReciboDTO->getStrStaTipoPeticionamento() == MdPetReciboRN::$TP_RECIBO_RESPOSTA_INTIMACAO ) { ?>
                    <tr>
                       <td style="font-weight: bold;">Tipo de Intima��o:</td>
                       <td> <?= $objMdPetReciboDTO->getStrNomeTipoIntimacao() ?> </td>
                    </tr>
                    
                    <tr>
                       <td style="font-weight: bold;">Documento Principal da Intima��o:</td>
                       <td>  <?= $objMdPetReciboDTO->getStrTextoDocumentoPrincipalIntimac() ?> </td>
                    </tr>
                    
                    <tr>
                       <td style="font-weight: bold;">Tipo de Resposta:</td>
                       <td> <?= $objMdPetReciboDTO->getStrNomeTipoResposta() ?> </td>
                    </tr>
				
				<?php } ?>

                <?php } if ($arrInteressados != null && is_array($arrInteressados) && count($arrInteressados) > 0) : ?>
                    <tr>
                        <td style="font-weight: bold;" colspan="2">Interessados:</td>
                    </tr>
                    <?php foreach ($arrInteressados as $interessado) : ?>
                        <tr>
                            <td colspan="2">&nbsp&nbsp&nbsp&nbsp<?= $interessado->getStrNome() ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>

                <tr>
                    <td style="font-weight: bold;">Protocolos dos Documentos (N�mero SEI):</td>
                    <td></td>
                </tr>

                <!--  lista todos os docs -->
                <?php if ($arrDocumentos != null && is_array($arrDocumentos) && count($arrDocumentos) > 0): ?>
                    <?php foreach ($arrDocumentos as $documento) : ?>
                        <tr>
                            <td>&nbsp&nbsp&nbsp&nbsp- <?= $documento->getStrNomeSerie() ?> <?= $documento->getStrNumeroDocumento() ?> </td>
                            <td> <?= $documento->getStrProtocoloFormatado() ?></td>
                        </tr>
                    <?php endforeach; ?>

                <?php endif; ?>

            </table>
            <br/>
            <br/>

            <p><label>O Usu�rio Externo acima identificado foi previamente avisado que o peticionamento importa na aceita��o dos termos e condi��es que regem o processo eletr�nico, al�m do disposto no credenciamento pr�vio, e na assinatura dos documentos nato-digitais e declara��o de que s�o aut�nticos os digitalizados, sendo respons�vel civil, penal e administrativamente pelo uso indevido. Ainda, foi avisado que os n�veis de acesso indicados para os documentos estariam condicionados � an�lise por servidor p�blico, que poder� alter�-los a qualquer momento sem necessidade de pr�vio aviso, e de que s�o de sua exclusiva responsabilidade:</label></p>
            <ul><label>
                    <li>a conformidade entre os dados informados e os documentos;</li>
                    <li>a conserva��o dos originais em papel de documentos digitalizados at� que decaia o direito de revis�o dos atos praticados no processo, para que, caso solicitado, sejam apresentados para qualquer tipo de confer�ncia;</li>
                    <li>a realiza��o por meio eletr�nico de todos os atos e comunica��es processuais com o pr�prio Usu�rio Externo ou, por seu interm�dio, com a entidade porventura representada;</li>
                    <li>a observ�ncia de que os atos processuais se consideram realizados no dia e hora do recebimento pelo SEI, considerando-se tempestivos os praticados at� as 23h59min59s do �ltimo dia do prazo, considerado sempre o hor�rio oficial de Bras�lia, independente do fuso hor�rio em que se encontre;</li>
                    <li>a consulta peri�dica ao SEI, a fim de verificar o recebimento de intima��es eletr�nicas.</li>
                </label></ul>
            <p><label>A exist�ncia deste Recibo, do processo e dos documentos acima indicados pode ser conferida no Portal na Internet do(a) <?= $OrgaoDTO->getStrDescricao() ?>.</label></p>

        </div>

        <input type="text" id=hdnInfraBarraLocalizacao name=hdnInfraBarraLocalizacao style="visibility: hidden;"/>
        <input type="text" id=hdnInfraAreaDados name=hdnInfraAreaDados style="visibility: hidden;"/>
        <input type="text" id=hdnRodape name=hdnRodape style="visibility: hidden;"/>

    </form>
<?
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
?>