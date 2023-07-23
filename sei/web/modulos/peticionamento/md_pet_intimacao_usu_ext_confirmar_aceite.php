<?php
/**
 * ANATEL
 *
 * 03/04/2017 - criado por jaqueline.mendes@castgroup.com.br - CAST
 *
 */
require_once dirname(__FILE__) . '/../../SEI.php';
SessaoSEIExterna::getInstance()->validarLink();
SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);
PaginaSEIExterna::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);

$arrComandos = array();
$texto = '';
$objMdPetIntDestRN = new MdPetIntRelDestinatarioRN();
$objMdPetIntAceiteRN = new MdPetIntAceiteRN();
$idIntimacao = $_GET['id_intimacao'];

switch ($_GET['acao']) {

    case 'md_pet_intimacao_usu_ext_confirmar_aceite':

        try {

            $strTitulo = 'Consultar Intima��o Eletr�nica';

            $arrComandos[] = '<button type="submit" accesskey="I" name="sbmAceitarIntimacao" id="sbmAceitarIntimacao" value="Confirmar Consulta � Intima��o" class="infraButton">Confirmar Consulta � <span class="infraTeclaAtalho">I</span>ntima��o</button>';
            $arrComandos[] = '<button type="button" accesskey="C" name="sbmFechar" id="sbmFechar"  onclick="infraFecharJanelaSelecao();" value="Fechar" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

            $texto = '<div style="padding-top: 10px; padding-bottom: 10px;"><p>Para visualizar os documentos da Intima��o Eletr�nica referente ao ';
            $texto .= 'Documento Principal SEI n� @numero_documento@, al�m de poder efetivar sua resposta, se faz necess�rio confirmar a consulta � intima��o.</p>';
            $texto .= '<p>Lembramos que, considerar-se-� cumprida a intima��o com a presente consulta no sistema ou, n�o efetuada a consulta, em ';
            $texto .= '@prazo_intimacao_tacita@ dias ap�s a data de sua expedi��o.</p>';

            $idDoc = (isset($_POST['hdnIdDocumento']) && !is_null($_POST['hdnIdDocumento'])) ? $_POST['hdnIdDocumento'] : $_GET['id_documento'];
            $idIntimacao = (isset($_POST['hdnIdIntimacao']) && !is_null($_POST['hdnIdIntimacao'])) ? $_POST['hdnIdIntimacao'] :  $_GET['id_intimacao'];

            if (isset($idDoc) && !is_null($idDoc)) {

                $idDocPrincipal = $idDoc;

                //Get Documento Formatado
                $objDocumentoDTO = new DocumentoDTO();
                $objDocumentoDTO->retStrProtocoloDocumentoFormatado();
                $objDocumentoDTO->setDblIdDocumento($idDocPrincipal);
                $objDocumentoRN = new DocumentoRN();
                $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);
                $numDocFormat = isset($objDocumentoDTO) && !is_null($objDocumentoDTO) ? $objDocumentoDTO->getStrProtocoloDocumentoFormatado() : '';

                //Get Prazo T�cito
                $objPrazoTacitoDTO = new MdPetIntPrazoTacitaDTO();
                $objPrazoTacitoDTO->retNumNumPrazo();
                $objPrazoTacitoRN = new MdPetIntPrazoTacitaRN();
                $retLista = $objPrazoTacitoRN->listar($objPrazoTacitoDTO);
                $objPrazoTacitoDTO = !is_null($retLista) && count($retLista) > 0 ? current($retLista) : null;
                $numPrazo = !is_null($objPrazoTacitoDTO) ? $objPrazoTacitoDTO->getNumNumPrazo() : null;

                $possuiIntimacaoJuridica = false;

                if ($idIntimacao) {
                    $dtIntimacao = null;
                    foreach ($idIntimacao as $id) {
                        //Data Expedi��o Intima��o
                        $objMdPetIntDestDTO = $objMdPetIntDestRN->consultarDadosIntimacao($id, true);
                        if(!is_null($objMdPetIntDestDTO)) {
                            $mdPetIntAceiteRN = new MdPetIntAceiteRN();
                            $objMdPetIntAceiteDTO = new MdPetIntAceiteDTO();
                            $objMdPetIntAceiteDTO->setNumIdMdPetIntRelDestinatario($objMdPetIntDestDTO->getNumIdMdPetIntRelDestinatario());
                            $objMdPetIntAceiteDTO->retTodos();
                            $objMdPetIntAceiteDTO = $mdPetIntAceiteRN->consultar($objMdPetIntAceiteDTO);
                            if (is_null($objMdPetIntAceiteDTO)) {
                                $dtHrIntimacao = !is_null($objMdPetIntDestDTO) ? $objMdPetIntDestDTO->getDthDataCadastro() : null;
                                if (is_null($dtIntimacao)) {
                                    $dtIntimacao = !is_null($dtHrIntimacao) ? explode(' ', $dtHrIntimacao) : null;
                                    $dtIntimacao = count($dtIntimacao) > 0 ? $dtIntimacao[0] : null;
                                }
                            }
                        }
                        //Calcular Data Final do Prazo T�cito
                        $dataFimPrazoTacito = '';
                        $objMdPetIntPrazoRN = new MdPetIntPrazoRN();
                        $dataFimPrazoTacito = $objMdPetIntPrazoRN->calcularDataPrazo($numPrazo, $dtIntimacao);

                        if ($objMdPetIntDestDTO && $objMdPetIntDestDTO->getStrSinPessoaJuridica() == 'S') {
                            $possuiIntimacaoJuridica = true;
                        }
                    }                   
                }
                if ($possuiIntimacaoJuridica) {
                    //se for pessoa Juridica ser� adicionado esse paragrafo a mais
                    $texto .= '<p>Por se tratar de Intima��o Eletr�nica destinada a Pessoa Jur�dica, esta ser� considerada cumprida caso seja confirmada a consulta por qualquer Representante formalmente vinculado � Pessoa Jur�dica (Respons�vel Legal e, caso existam, Procuradores com poderes de receber Intima��es por meio de Procura��es Eletr�nicas geradas no pr�prio SEI).</p>';
                }

            }

            //Realizar o Aceite
            if (isset($_POST['sbmAceitarIntimacao']) && $_POST['sbmAceitarIntimacao'] == true) {

                $objMdPetIntAceiteRN = new MdPetIntAceiteRN();

                try {

                    $objInfraException = new InfraException();
                    $todasIntimacoesAceitas = $objMdPetIntAceiteRN->todasIntimacoesAceitas($_POST['hdnIdIntimacao']);

                    if ($todasIntimacoesAceitas['todasAceitas']) {
                        if($todasIntimacoesAceitas['qntDestinatario'] == 0){
                            $objInfraException->adicionarValidacao('Voc� n�o possui mais permiss�o para cumprir a Intima��o Eletr�nica.');
                        }else{
                            $objInfraException->adicionarValidacao('Esta intima��o j� foi cumprida.');
                        }
                    }

                    $objInfraException->lancarValidacoes();

                    if(!$objInfraException->contemValidacoes()){
                        $mdPetIntProtocoloRN = new MdPetIntProtocoloRN();
                        $objMdPetIntProtocoloDTO = new MdPetIntProtocoloDTO();
                        $objMdPetIntProtocoloDTO->setDblIdProtocolo($_POST['hdnIdDocumento']);
                        $objMdPetIntProtocoloDTO->setNumIdMdPetIntimacao($_POST['hdnIdIntimacao'], InfraDTO::$OPER_IN);
                        $objMdPetIntProtocoloDTO->setStrSinPrincipal('S');
                        $objMdPetIntProtocoloDTO->retTodos();
                        $objMdPetIntProtocoloDTO = $mdPetIntProtocoloRN->listar($objMdPetIntProtocoloDTO);

                        if ($objMdPetIntProtocoloDTO) {
                            $_POST['hdnIdIntimacao'] = [];
                            foreach ($objMdPetIntProtocoloDTO as $item) {
                                $_POST['hdnIdIntimacao'][] = $item->getNumIdMdPetIntimacao();
                            }
                        }

                        //chamando a RN que executa os processos de aceite manual da intima��o
                        $arrParametrosAceite = $_POST;
                        $arrParametrosAceite['id_documento'] = $_GET['id_documento'];
                        $arrParametrosAceite['id_acesso_externo'] = $_GET['id_acesso_externo'];

                        $objAceiteDTO = $objMdPetIntAceiteRN->processarAceiteManual($arrParametrosAceite);

	                    echo "<script>";
	                    echo "var captionDocumentos = window.top.document.getElementById('tblDocumentos').getElementsByTagName('caption')[0];";
	                    echo "window.top.document.getElementById('tblDocumentos').getElementsByTagName('tbody')[0].style.display = 'none';";
	                    echo "captionDocumentos.innerHTML = 'Atualizando lista de Protocolos...';";
	                    echo "captionDocumentos.style.cssText = 'color: #dc4909; text-align: center !important; border-bottom: none';";

	                    echo "var captionHistorico = window.top.document.getElementById('tblHistorico').getElementsByTagName('caption')[0];";
	                    echo "window.top.document.getElementById('tblHistorico').getElementsByTagName('tbody')[0].style.display = 'none';";
	                    echo "captionHistorico.innerHTML = 'Atualizando lista de Andamentos...';";
	                    echo "captionHistorico.style.cssText = 'color: #dc4909; text-align: center !important; border-bottom: none';";

	                    echo "window.top.location.reload(true);";
	                    echo "parent.infraFecharJanelaModal();";
	                    echo "</script>";

                    }

                } catch (Exception $e) {
                    PaginaSEIExterna::getInstance()->processarExcecao($e);
                }

            }

            $texto .= '<p>Como a presente Intima��o foi expedida em @data_expedicao_intimacao@ e em conformidade com as regras de contagem ';
            $texto .= 'de prazo dispostas no art. 66 da Lei n� 9.784/1999, mesmo se n�o ocorrer a consulta acima indicada, a Intima��o ser� ';
            $texto .= 'considerada cumprida por decurso do prazo t�cito ao final do dia @data_final_prazo_intimacao_tacita@.</p></div>';

            //Documento
            $texto = str_replace('@numero_documento@', $numDocFormat, $texto);
            $texto = str_replace('@prazo_intimacao_tacita@', $numPrazo, $texto);
            $texto = str_replace('@data_expedicao_intimacao@', $dtIntimacao, $texto);
            $texto = str_replace('@data_final_prazo_intimacao_tacita@', $dataFimPrazoTacito, $texto);

        } catch (Exception $e) {
            PaginaSEIExterna::getInstance()->processarExcecao($e);
        }

        break;
    default:
        throw new InfraException("A��o '" . $_GET['acao'] . "' n�o reconhecida.");
}

PaginaSEIExterna::getInstance()->montarDocType();
PaginaSEIExterna::getInstance()->abrirHtml();
PaginaSEIExterna::getInstance()->abrirHead();
PaginaSEIExterna::getInstance()->montarMeta();

PaginaSEIExterna::getInstance()->montarTitle(':: ' . PaginaSEIExterna::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');

PaginaSEIExterna::getInstance()->montarStyle();
PaginaSEIExterna::getInstance()->abrirStyle();
?>
    div.infraBarraComandos { text-align: center !important }
    .clear { clear: both }
    p { font-size: 0.875rem }
    h4 { font-size: 1.4rem;font-weight: 600 }
<?php
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
PaginaSEIExterna::getInstance()->fecharJavaScript();
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody();
SessaoSEIExterna::getInstance()->configurarAcessoExterno($_GET['id_acesso_externo']);
?>

    <form action="<?php echo SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_intimacao_usu_ext_confirmar_aceite&id_procedimento=' . $_GET['id_procedimento'] . '&id_acesso_externo=' . $_GET['id_acesso_externo'] . '&id_documento=' . $_GET['id_documento']); ?>" method="post" id="frmMdPetIntimacaoConfirmarAceite" name="frmMdPetIntimacaoConfirmarAceite">
        <div class="row">
            <div class="col-12">
                <h4 class="mt-2"><?= $strTitulo ?></h4>
	            <?php PaginaSEIExterna::getInstance()->montarBarraComandosSuperior([]); ?>
                <div class="textoIntimacaoEletronica">
                    <h4><?= $texto; ?></h4>
	                <?php
	                if ($idIntimacao) {
		                foreach ($idIntimacao as $id) {
			                echo '<input type="hidden" name="hdnIdIntimacao[]" id="hdnIdIntimacao" value="' . $id . '"/>';
		                }
	                }
	                ?>
                    <input type="hidden" name="hdnIdDocumento" value="<?= $idDocPrincipal; ?>">
                    <input type="hidden" name="sbmAceitarIntimacao" value="true">
                </div>
	            <?php PaginaSEIExterna::getInstance()->montarBarraComandosInferior($arrComandos); ?>
            </div>
        </div>
    </form>

    <script>
        $(document).ready(function() {
            $('button#sbmAceitarIntimacao').off('click').one('click', function(e){
                e.preventDefault(); e.stopPropagation();
                $(this).prop('disabled', true).html('Aguarde, confirmando a consulta � intima��o...');
                $('form#frmMdPetIntimacaoConfirmarAceite')[0].submit();
            });
        });
    </script>

<?php
SessaoSEIExterna::getInstance()->configurarAcessoExterno($_GET['id_acesso_externo']);
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
SessaoSEIExterna::getInstance()->configurarAcessoExterno(0);
?>