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
            $texto .= '<p>Como a presente Intimação foi expedida em @data_expedicao_intimacao@ e em conformidade com as regras de contagem ';
            $texto .= 'de prazo dispostas no art. 66 da Lei nº 9.784/1999, mesmo se não ocorrer a consulta acima indicada, a Intimação será ';
            $texto .= 'considerada cumprida por decurso do prazo tácito ao final do dia @data_final_prazo_intimacao_tacita@.</p></div>';


            $idDoc = $_GET['id_documento'];


            if (isset($idDoc) && !is_null($idDoc)) {
                $idIntimacao = $_GET['id_intimacao'];
                $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
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

                $possuiIntimacaoJuridica = FALSE;
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
                            $possuiIntimacaoJuridica = TRUE;
                        }
                    }                   
                }
                if ($possuiIntimacaoJuridica) {
                    $texto = '<div style="padding-top: 10px; padding-bottom: 10px;"><p>Para visualizar os documentos da Intimação Eletrônica referente ao Documento Principal SEI nº @numero_documento@, além de poder efetivar sua resposta, se faz necessário confirmar a consulta à intimação.</p>';
                    $texto .= '<p>Lembramos que, considerar-se-á cumprida a intimação com a presente consulta no sistema ou, não efetuada a consulta, em ';
                    $texto .= '@prazo_intimacao_tacita@ dias após a data de sua expedição.</p>';
                    //se for pessoa Juridica será adicionado esse paragrafo a mais
                    $texto .= '<p>Por se tratar de Intimação Eletrônica destinada a Pessoa Jurídica, esta será considerada cumprida caso seja confirmada a consulta por qualquer Representante formalmente vinculado à Pessoa Jurídica (Responsável Legal e, caso existam, Procuradores com poderes de receber Intimações por meio de Procurações Eletrônicas geradas no próprio SEI).</p>';
                    $texto .= '<p>Como a presente Intimação foi expedida em @data_expedicao_intimacao@ e em conformidade com as regras de contagem ';
                    $texto .= 'de prazo dispostas no art. 66 da Lei nº 9.784/1999, mesmo se não ocorrer a consulta acima indicada, a Intimação será ';
                    $texto .= 'considerada cumprida por decurso do prazo tácito ao final do dia @data_final_prazo_intimacao_tacita@.</p></div>';
                }

                //Documento
                $texto = str_replace('@numero_documento@', $numDocFormat, $texto);
                $texto = str_replace('@prazo_intimacao_tacita@', $numPrazo, $texto);
                $texto = str_replace('@data_expedicao_intimacao@', $dtIntimacao, $texto);
                $texto = str_replace('@data_final_prazo_intimacao_tacita@', $dataFimPrazoTacito, $texto);
            }
            //Realizar o Aceite
            if (isset($_POST['sbmAceitarIntimacao'])) {
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
                } catch (Exception $e) {
                    PaginaSEIExterna::getInstance()->processarExcecao($e);
                }

                echo "<script>";
                echo "window.parent.location.reload();";
                echo "window.close();";
                echo "</script>";
                die;
            }
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
    div.infraBarraComandos{text-align: center !important;}

    .textoIntimacaoEletronica {}
.clear {clear: both;}
    p{
    font-size: 0.875rem;
    }
<?php
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
PaginaSEIExterna::getInstance()->fecharJavaScript();
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo, 'onload=""');
SessaoSEIExterna::getInstance()->configurarAcessoExterno($_GET['id_acesso_externo']);
?>
<form action="<?php echo SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_intimacao_usu_ext_confirmar_aceite&id_procedimento=' . $_GET['id_procedimento'] . '&id_acesso_externo=' . $_GET['id_acesso_externo'] . '&id_documento=' . $_GET['id_documento']); ?>" method="post" id="frmMdPetIntimacaoConfirmarAceite" name="frmMdPetIntimacaoConfirmarAceite">

    <div class="clear"></div>
    <div class="textoIntimacaoEletronica">
        <h4>
            <?php echo $texto; ?>
        </h4>
        <?php
        if ($idIntimacao) {
            foreach ($idIntimacao as $id) {
                echo '<input type="hidden" name="hdnIdIntimacao[]" id="hdnIdIntimacao" value="' . $id . '"/>';
            }
        }
        ?>
        <input type="hidden" name="hdnIdDocumento" value="<?= $idDocPrincipal; ?>">
    </div>
        <?php PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos); ?>

</form>
<?php
SessaoSEIExterna::getInstance()->configurarAcessoExterno($_GET['id_acesso_externo']);
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
SessaoSEIExterna::getInstance()->configurarAcessoExterno(0);
?>