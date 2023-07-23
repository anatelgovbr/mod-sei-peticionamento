<?php
/**
 * ANATEL
 *
 * 15/08/2018 - criado por CAST
 *
 */
require_once dirname(__FILE__) . '/../../SEI.php';

SessaoSEI::getInstance()->validarLink();
SessaoSEI::getInstance()->validarPermissao('md_pet_intimacao_cadastrar');
PaginaSEI::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);

$arrComandos = array();
$texto = '';

switch($_GET['acao']) {
	
    case 'md_pet_intimacao_cadastro_confirmar':
        try {
            $strTitulo = 'Gerar Intimação Eletrônica - Confirmação';

            $arrComandos[] = '<button type="button" accesskey="G" name="sbmConfirmarIntimacao" id="sbmConfirmarIntimacao" value="Ciente e Gerar Intimação" class="infraButton">Ciente e <span class="infraTeclaAtalho">G</span>erar Intimação</button>';
            $arrComandos[] = '<button type="button" accesskey="C" name="sbmFechar" id="sbmFechar" onclick="infraFecharJanelaModal();" value="Cancelar" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            $objMdPetIntPrazoTacitaDTO = new MdPetIntPrazoTacitaDTO();
            $objMdPetIntPrazoTacitaDTO->setBolExclusaoLogica(false);
            $objMdPetIntPrazoTacitaDTO->retNumNumPrazo();
            $objMdPetIntPrazoTacitaDTO = (new MdPetIntPrazoTacitaRN())->consultar($objMdPetIntPrazoTacitaDTO);

	        $numNumPrazo = !empty($objMdPetIntPrazoTacitaDTO) ? $objMdPetIntPrazoTacitaDTO->getNumNumPrazo() : 0;

            $divParcial = ($_GET["tipo"] == 'parcial') ? 'block' : 'none';
            $divIntegral = ($_GET["tipo"]== 'integral') ? 'block' : 'none';

            //Integral
            $texto  = "<div id=divIntegral style='display: " . $divIntegral . "; padding-top: 15px; padding-bottom: 15px; '>";
            $texto .= "<p style='text-align:justify'> ";
            $texto .= "Após a geração da Intimação Eletrônica, esta ficará imediatamente disponível para que o ";
            $texto .= "Destinatário realize o seu cumprimento com a consulta ao Documento Principal ou, se indicados, "; 
            $texto .= "a qualquer um dos Protocolos dos Anexos da Intimação. Caso a consulta não seja efetuada em "; 
            $texto .= "até " . $numNumPrazo . " dias corridos da data de geração da Intimação Eletrônica, ";
            $texto .= "automaticamente ocorrerá seu Cumprimento por Decurso do Prazo Tácito. ";

            $texto .= "<p style='text-align:justify'> ";
            $texto .= "O Documento Principal e possíveis Anexos terão o acesso ao seu teor protegidos até o ";
            $texto .= "cumprimento da Intimação.";

            $texto .= "<p style='text-align:justify'> ";
            $texto .= "<span style='font-weight:bold'>Atenção: </span> ";
            $texto .= "Toda Intimação Eletrônica ocorre por meio da funcionalidade de Disponibilização de ";
            $texto .= "Acesso Externo do SEI. Com o Tipo de Acesso Externo <span style='font-weight:bold'>Integral</span>, TODOS os Protocolos constantes ";
            $texto .= "no processo serão disponibilizados ao Destinatário, independentemente de seus Níveis de ";
            $texto .= "Acesso, incluindo Protocolos futuros que forem adicionados ao processo.";
            $texto .= "</p> ";

            $texto .= "<p style='text-align:justify'> ";
            $texto .= "Para que não ocorra nulidade da Intimação, o Acesso Externo Integral somente poderá ser ";
            $texto .= "cancelado depois de cumprida a Intimação e concluído o Prazo Externo correspondente (se ";
            $texto .= "indicado para possível Resposta). Caso posteriormente o Acesso Externo Integral utilizado pela ";
            $texto .= "Intimação Eletrônica seja cancelado, ele será automaticamente substituído por um Acesso ";
            $texto .= "Externo Parcial abrangendo o Documento Principal e possíveis Anexos da Intimação, além de ";
            $texto .= "Documentos peticionados pelo próprio Usuário Externo.";
            $texto .= "</p> ";
            $texto .= "</div>";
            
			// Parcial
            $texto .= "<div id=divParcial style='display:" . $divParcial . "'>";
            $texto .= "<p style='text-align:justify'> ";
            $texto .= "Após a geração da Intimação Eletrônica, esta ficará imediatamente disponível para que o "; 
            $texto .= "Destinatário realize o seu cumprimento com a consulta ao Documento Principal ou, se indicados, "; 
            $texto .= "a qualquer um dos Protocolos dos Anexos da Intimação. Caso a consulta não seja efetuada em "; 
            $texto .= "até " . $numNumPrazo . " dias corridos da data de geração da Intimação Eletrônica, ";
            $texto .= "automaticamente ocorrerá seu Cumprimento por Decurso do Prazo Tácito.";
            $texto .= "</p>";

            $texto .= "<p style='text-align:justify'> ";
            $texto .= "O Documento Principal e possíveis Anexos terão o acesso ao seu teor protegidos até o cumprimento da Intimação.";
            $texto .= "</p>";

            $texto .= "<p style='text-align:justify'> ";
            $texto .= "<span style='font-weight:bold'>Atenção: </span> ";
            $texto .= "Toda Intimação Eletrônica ocorre por meio da funcionalidade de Disponibilização de ";
            $texto .= "Acesso Externo do SEI. Com o Tipo de Acesso Externo <span style='font-weight:bold'>Parcial</span>, SOMENTE serão disponibilizados "; 
            $texto .= "ao Destinatário o Documento Principal, os Protocolos dos Anexos da Intimação (se indicados) e os "; 
            $texto .= "Protocolos adicionados no Acesso Parcial (se indicados). O Documento Principal e Protocolos "; 
            $texto .= "dos Anexos serão automaticamente incluídos no Acesso Parcial.";
            $texto .= "</p>";

            $texto .= "<p style='text-align:justify'> ";
            $texto .= "Para que não ocorra nulidade da Intimação, o Acesso Externo Parcial não poderá ser alterado ";
            $texto .= "nem cancelado. Todos os Protocolos incluídos no Acesso Externo Parcial poderão ser ";
            $texto .= "visualizados pelo Destinatário, independentemente de seus Níveis de Acesso, não abrangendo "; 
            $texto .= "Protocolos futuros que forem adicionados ao processo.";
            $texto .= "</p>";
            $texto .= "</div>";

        } catch (Exception $e) {
            PaginaSEI::getInstance()->processarExcecao($e);
        }
        
    break;
    default:
        throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();

PaginaSEI::getInstance()->montarTitle(':: '. PaginaSEI::getInstance()->getStrNomeSistema() .' - '.$strTitulo.' ::');

PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
?>
.clear {clear: both;}
p{
    font-size: 0.875rem;
}
<?php
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>

    $(document).ready(function() {
        $('button#sbmConfirmarIntimacao').off('click').one('click', function(e){
            e.preventDefault();
            var sendForm = false;
            var btn = $("button#sbmConfirmarIntimacao");
            var form = $('#frmMdPetIntimacaoCadastro', window.parent.ifrVisualizacao.document);
            $.ajax({
                url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_intimacao_validar_duplicidade') ?>',
                type: form.attr('method'),
                data: form.serialize(),
                dataType: 'xml',
                beforeSend: function(){
                    btn.prop('disabled', true).html('Aguarde, gerando intimação...');
                },
                success: function (r) {
                    var msg = $(r).find('message').text();
                    if(msg != ''){
                        alert(msg);
                        $('button#sbmFechar').click();
                    }else{
                        sendForm = true;
                    }
                },
                complete: function (e) {
                    if(sendForm){
                        form.submit();
                    }
                },
                error: function (e) {
                    console.error('Erro ao processar requisição');
                }
            });
        });
    });

<?php
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'');
?>
    <div class="clear"></div>
    <div class="textoIntimacaoEletronica">
        <h2>
        <?php echo $texto; ?>
        </h2>
    </div>
    <div style="padding-right: 40%">
<?php 
PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>