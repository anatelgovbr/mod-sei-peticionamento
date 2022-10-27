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

            $strTitulo = 'Consultar Intimação Eletrônica';

            $arrComandos[] = '<button type="submit" accesskey="I" name="sbmAceitarIntimacao" id="sbmAceitarIntimacao" value="Confirmar Consulta à Intimação" class="infraButton">Confirmar Consulta à <span class="infraTeclaAtalho">I</span>ntimação</button>';
            $arrComandos[] = '<button type="button" accesskey="C" name="sbmFechar" id="sbmFechar"  onclick="infraFecharJanelaSelecao();" value="Fechar" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

            $texto = '<div style="padding-top: 10px; padding-bottom: 10px;"><p>Para visualizar os documentos da Intimação Eletrônica referente ao ';
            $texto .= 'Documento Principal SEI nº @numero_documento@, além de poder efetivar sua resposta, se faz necessário confirmar a consulta à intimação.</p>';
            $texto .= '<p>Lembramos que, considerar-se-á cumprida a intimação com a presente consulta no sistema ou, não efetuada a consulta, em ';
            $texto .= '@prazo_intimacao_tacita@ dias após a data de sua expedição.</p>';

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

                //Get Prazo Tácito
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
                        //Data Expedição Intimação
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
                        //Calcular Data Final do Prazo Tácito
                        $dataFimPrazoTacito = '';
                        $objMdPetIntPrazoRN = new MdPetIntPrazoRN();
                        $dataFimPrazoTacito = $objMdPetIntPrazoRN->calcularDataPrazo($numPrazo, $dtIntimacao);

                        if ($objMdPetIntDestDTO && $objMdPetIntDestDTO->getStrSinPessoaJuridica() == 'S') {
                            $possuiIntimacaoJuridica = true;
                        }
                    }                   
                }
                if ($possuiIntimacaoJuridica) {
                    //se for pessoa Juridica será adicionado esse paragrafo a mais
                    $texto .= '<p>Por se tratar de Intimação Eletrônica destinada a Pessoa Jurídica, esta será considerada cumprida caso seja confirmada a consulta por qualquer Representante formalmente vinculado à Pessoa Jurídica (Responsável Legal e, caso existam, Procuradores com poderes de receber Intimações por meio de Procurações Eletrônicas geradas no próprio SEI).</p>';
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
                            $objInfraException->adicionarValidacao('Você não possui mais permissão para cumprir a Intimação Eletrônica.');
                        }else{
                            $objInfraException->adicionarValidacao('Já havia ocorrido o cumprimento da presente intimação.');
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

                        //chamando a RN que executa os processos de aceite manual da intimação
                        $arrParametrosAceite = $_POST;
                        $arrParametrosAceite['id_documento'] = $_GET['id_documento'];
                        $arrParametrosAceite['id_acesso_externo'] = $_GET['id_acesso_externo'];

                        $objAceiteDTO = $objMdPetIntAceiteRN->processarAceiteManual($arrParametrosAceite);
                    }

                } catch (Exception $e) {
                    PaginaSEIExterna::getInstance()->processarExcecao($e);
                }

            }

            $texto .= '<p>Como a presente Intimação foi expedida em @data_expedicao_intimacao@ e em conformidade com as regras de contagem ';
            $texto .= 'de prazo dispostas no art. 66 da Lei nº 9.784/1999, mesmo se não ocorrer a consulta acima indicada, a Intimação será ';
            $texto .= 'considerada cumprida por decurso do prazo tácito ao final do dia @data_final_prazo_intimacao_tacita@.</p></div>';

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
        throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
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

<?php if(isset($_POST['sbmAceitarIntimacao'])): ?>
<script>window.top.location.reload();</script>
<?php else: ?>
    <form action="<?php echo SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_intimacao_usu_ext_confirmar_aceite&id_procedimento=' . $_GET['id_procedimento'] . '&id_acesso_externo=' . $_GET['id_acesso_externo'] . '&id_documento=' . $_GET['id_documento']); ?>" method="post" id="frmMdPetIntimacaoConfirmarAceite" name="frmMdPetIntimacaoConfirmarAceite">
        <div class="row">
            <div class="col-12">
                <h4 class="mt-2"><?= $strTitulo ?></h4>
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
                <?php PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos); ?>
            </div>
        </div>
    </form>
<?php endif ?>
    <script>
        $(document).ready(function() {
            $('button#sbmAceitarIntimacao').off('click').one('click', function(e){
                $(this).prop('disabled', true).html('Aguarde, confirmando a consulta à intimação...');
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