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

                    $arr = PaginaSEI::getInstance()->getArrItensTabelaDinamica($_POST['hdnInfraAreaDados']);

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
                $objReciboPeticionamentoDTO = new ReciboPeticionamentoDTO();
                $objReciboPeticionamentoDTO->setNumIdReciboPeticionamento($_GET['id_md_pet_rel_recibo_protoc']);
                $objReciboPeticionamentoDTO->retNumIdProtocolo();
                $objReciboPeticionamentoDTO->retDblIdDocumento();
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
                
                $documentoRN = new DocumentoRN();
                $documentoReciboDTO = new DocumentoDTO();
                $documentoReciboDTO->retStrProtocoloDocumentoFormatado();
                $documentoReciboDTO->setDblIdDocumento( $objReciboPeticionamentoDTO->getDblIdDocumento() );
                $documentoReciboDTO = $documentoRN->consultarRN0005( $documentoReciboDTO );
                
                if( $documentoReciboDTO != null ){
                  $strTitulo = 'Recibo Eletrônico de Protocolo - SEI n° ' . $documentoReciboDTO->getStrProtocoloDocumentoFormatado();
                } else {
                	$strTitulo = 'Recibo Eletrônico de Protocolo';
                }
                
                break;

            default:
                throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
        }

        $arrComandos   = array();
		// Botão SALVAR EM PDF desativado temporariamente até resolver a falta de tratamento HTML
		// $arrComandos[] = '<button type="button" accesskey="s" id="btnSalvarPDF" value="Salvar em PDF" onclick="salvarPDF();" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar em PDF</button>';
        $arrComandos[] = '<button type="button" accesskey="i" id="btnImprimir" value="Imprimir" onclick="imprimir();" class="infraButton"><span class="infraTeclaAtalho">I</span>mprimir</button>';
        $arrComandos[] = '<button type="button" accesskey="c" id="btnFechar" value="Fechar" onclick="location.href=\'' . PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?id_md_pet_rel_recibo_protoc=' . $_GET['id_md_pet_rel_recibo_protoc'] . '&acao=' . PaginaSEIExterna::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'])) . '\'" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

        $objReciboPeticionamentoDTO = new ReciboPeticionamentoDTO();
        $objReciboPeticionamentoDTO->retTodos();
        $objReciboPeticionamentoDTO->retStrDscUnidadeGeradora();
        
        $objReciboDocumentoAnexoPeticionamentoDTO = new ReciboDocumentoAnexoPeticionamentoDTO();
        $objReciboDocumentoAnexoPeticionamentoDTO->retTodos();
        $objReciboDocumentoAnexoPeticionamentoDTO->retStrNumeroDocumento();
        $objReciboDocumentoAnexoPeticionamentoDTO->retStrNomeSerie();
        $objReciboDocumentoAnexoPeticionamentoDTO->retStrProtocoloFormatado();

        if (isset($_GET['id_md_pet_rel_recibo_protoc']) && $_GET['id_md_pet_rel_recibo_protoc'] != "") {
            $objReciboPeticionamentoDTO->setNumIdReciboPeticionamento($_GET['id_md_pet_rel_recibo_protoc']);
        }
		
        //usuarios so podem ver peticionamentos feitos por ele mesmo
        $objReciboPeticionamentoDTO->setNumIdUsuario( SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );

        $objReciboPeticionamentoRN = new ReciboPeticionamentoRN();
        $objReciboDocumentoAnexoPeticionamentoRN = new ReciboDocumentoAnexoPeticionamentoRN();
        
        $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
        $strVersaoModuloPeticionamento = $objInfraParametro->getValor('VERSAO_MODULO_PETICIONAMENTO', false);
        
        if ($strVersaoModuloPeticionamento != '1.1.0') {
        	$objReciboPeticionamentoDTO->unRetDblIdProtocoloRelacionado();
        }
        
        $objReciboPeticionamentoDTO = $objReciboPeticionamentoRN->consultar($objReciboPeticionamentoDTO);

        //obtendo a lista de documentos vinculados ao recibo
        $objReciboDocumentoAnexoPeticionamentoDTO->setNumIdReciboPeticionamento(
 		  $objReciboPeticionamentoDTO->getNumIdReciboPeticionamento()
 		);
        
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
        $protocoloDTO->retNumIdOrgaoUnidadeGeradora();
        $protocoloDTO->setDblIdProtocolo($objReciboPeticionamentoDTO->getNumIdProtocolo());
        $protocoloDTO = $protocoloRN->consultarRN0186($protocoloDTO);

        //obter interessados (do tipo interessado, nao os do tipo rementente)
        $objParticipanteDTO = new ParticipanteDTO();
        $objParticipanteDTO->setDblIdProtocolo($objReciboPeticionamentoDTO->getNumIdProtocolo());
        $objParticipanteDTO->setStrStaParticipacao( ParticipanteRN::$TP_INTERESSADO );
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
		// Botão SALVAR EM PDF desativado temporariamente até resolver a falta de tratamento HTML
		// document.getElementById('btnSalvarPDF').style.display = 'none';
        infraImprimirDiv('divInfraAreaTelaD');

        self.setTimeout(function () {
            document.getElementById('btnFechar').style.display = '';
            document.getElementById('btnImprimir').style.display = '';
			// Botão SALVAR EM PDF desativado temporariamente até resolver a falta de tratamento HTML
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
	                s += '±';
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
                <td style="font-weight: bold; width: 400px;">Usuário Externo (signatário):</td>
                <td><?= $usuarioDTO->getStrNome() ?></td>
            </tr>

            <tr>
                <td style="font-weight: bold;">IP utilizado:</td>
                <td><?= $objReciboPeticionamentoDTO->getStrIpUsuario() ?></td>
            </tr>

            <tr>
                <td style="font-weight: bold;">Data e Horário:</td>
                <td><?= $objReciboPeticionamentoDTO->getDthDataHoraRecebimentoFinal() ?></td>
            </tr>
			
            <tr>
                <td style="font-weight: bold;">Tipo de Peticionamento:</td>
                <td><?= $objReciboPeticionamentoDTO->getStrStaTipoPeticionamentoFormatado() ?></td>
            </tr>

            <tr>
                <td style="font-weight: bold;">Número do Processo:</td>
                <td><?= $protocoloDTO->getStrProtocoloFormatado() ?></td>
            </tr>

            <?php if ($arrInteressados != null && is_array($arrInteressados) && count($arrInteressados) > 0) : ?>
                <tr>
                    <td style="font-weight: bold;" colspan="2">Interessados:</td>
                </tr>
                <?php foreach ($arrInteressados as $interessado) : ?>
                    <tr>
                        <td colspan="2">&nbsp&nbsp&nbsp&nbsp<?= PaginaSEI::tratarHTML($interessado->getStrNome()) ?></td>
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
                            <td>&nbsp&nbsp&nbsp&nbsp- <?= $documento->getStrNomeSerie() ?> <?= $documento->getStrNumeroDocumento() ?> </td>
                            <td> <?= $documento->getStrProtocoloFormatado() ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>

            <?php endif; ?>

            <!-- lista DOC ESSENCIAL -->
            <?php 
            if ($arrDocumentos != null && is_array($arrDocumentos) && count($arrDocumentos) > 0){
             	$documentoExiste=false;
				foreach ($arrDocumentos as $documento) {
					// E-ESSENCIAL
					if ($documento->getStrClassificacaoDocumento() == "E") { 
						if ($documentoExiste==false){
							echo "                <tr>";
							echo "                    <td style='font-weight: bold;'>- Documentos Essenciais:</td>";
							echo "                    <td></td>";
							echo "                </tr>";
							$documentoExiste=true;
						}
						echo "                <tr>";
						echo "                    <td>&nbsp&nbsp&nbsp&nbsp- " . $documento->getStrNomeSerie() . "&nbsp" . $documento->getStrNumeroDocumento() . "</td>";
						echo "                    <td> " . $documento->getStrProtocoloFormatado() . "</td>";
						echo "                </tr>";
                    }
				}
            }					
			?>

            <!--  lista DOC COMPLEMENTAR -->
            <?php 
            if ($arrDocumentos != null && is_array($arrDocumentos) && count($arrDocumentos) > 0){
             	$documentoExiste=false;
				foreach ($arrDocumentos as $documento) {
					// C-COMPLEMENTAR
					if ($documento->getStrClassificacaoDocumento() == "C") { 
						if ($documentoExiste==false){
							echo "                <tr>";
							echo "                    <td style='font-weight: bold;'>- Documentos Complementares:</td>";
							echo "                    <td></td>";
							echo "                </tr>";
							$documentoExiste=true;
						}
						echo "                <tr>";                        
						echo "                    <td>&nbsp&nbsp&nbsp&nbsp- " . $documento->getStrNomeSerie() . "&nbsp" . $documento->getStrNumeroDocumento() . "</td>";
						echo "                    <td> " . $documento->getStrProtocoloFormatado() . "</td>";
						echo "                </tr>";
                    }
				}
            }					
			?>

        </table>
        <br/>
        <br/>
        
        <p><label>O Usuário Externo acima identificado foi previamente avisado que o peticionamento importa na aceitação dos termos e condições que regem o processo eletrônico, além do disposto no credenciamento prévio, e na assinatura dos documentos nato-digitais e declaração de que são autênticos os digitalizados, sendo responsável civil, penal e administrativamente pelo uso indevido. Ainda, foi avisado que os níveis de acesso indicados para os documentos estariam condicionados à análise por servidor público, que poderá, motivadamente, alterá-los a qualquer momento sem necessidade de prévio aviso, e de que são de sua exclusiva responsabilidade:</label></p>
			<ul><label>
				<li>a conformidade entre os dados informados e os documentos;</li>
				<li>a conservação dos originais em papel de documentos digitalizados até que decaia o direito de revisão dos atos praticados no processo, para que, caso solicitado, sejam apresentados para qualquer tipo de conferência;</li>
				<li>a realização por meio eletrônico de todos os atos e comunicações processuais com o próprio Usuário Externo ou, por seu intermédio, com a entidade porventura representada;</li>
				<li>a observância de que os atos processuais se consideram realizados no dia e hora do recebimento pelo SEI, considerando-se tempestivos os praticados até as 23h59min59s do último dia do prazo, considerado sempre o horário oficial de Brasília, independente do fuso horário em que se encontre;</li>
				<li>a consulta periódica ao SEI, a fim de verificar o recebimento de intimações eletrônicas.</li>
			</label></ul>
		<p><label>A existência deste Recibo, do processo e dos documentos acima indicados pode ser conferida no Portal na Internet do(a) <?= $OrgaoDTO->getStrDescricao() ?>.</label></p>

    </div>

    <input type="text" id=hdnInfraBarraLocalizacao name=hdnInfraBarraLocalizacao style="visibility: hidden;"/>
    <input type="text" id=hdnInfraAreaDados name=hdnInfraAreaDados style="visibility: hidden;"/>
    <input type="text" id=hdnRodape name=hdnRodape style="visibility: hidden;"/>

</form>
<?
    PaginaSEIExterna::getInstance()->fecharBody();
    PaginaSEIExterna::getInstance()->fecharHtml();
?>