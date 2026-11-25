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
$acao = PaginaSEI::GET('acao');

switch($acao) {
	
    case 'md_pet_intimacao_cadastro_confirmar':
        try {
            $strTitulo = 'Gerar Intimação Eletrônica - Confirmação';

            $arrComandos[] = '<button type="button" accesskey="G" name="sbmConfirmarIntimacao" id="sbmConfirmarIntimacao" value="Ciente e Gerar Intimação" class="infraButton">Ciente e <span class="infraTeclaAtalho">G</span>erar Intimação</button>';
            $arrComandos[] = '<button type="button" accesskey="C" name="sbmFechar" id="sbmFechar" onclick="infraFecharJanelaModal();" value="Cancelar" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            // CODIGO ADD
                $objDocumentoDTO = new DocumentoDTO();
                $objDocumentoDTO->retDblIdDocumento();
                $objDocumentoDTO->retDblIdProcedimento();
                $objDocumentoDTO->retNumIdTipoProcedimentoProcedimento();
                $objDocumentoDTO->setDblIdDocumento(PaginaSEI::GET('id_documento'));

                $objDocumentoRN = new DocumentoRN();
                $objDocumentoDTO = $objDocumentoRN->consultarRN0005($objDocumentoDTO);

                $objMdPetIntPrazoTacitaDTO = ( new MdPetIntPrazoTacitaRN() )->getTipoPrazoTacitoGeralEspecifico( $objDocumentoDTO->getNumIdTipoProcedimentoProcedimento() );
            // FIM

	        $numNumPrazo = !empty($objMdPetIntPrazoTacitaDTO) ? $objMdPetIntPrazoTacitaDTO->getNumNumPrazo() : 0;

            $divParcial = (PaginaSEI::GET('tipo') == 'parcial') ? 'block' : 'none';
            $divIntegral = (PaginaSEI::GET('tipo') == 'integral') ? 'block' : 'none';

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
        throw new InfraException("Ação '".$acao."' não reconhecida.");
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

    var urlArvore = null;
    const LIMITE_DESTINATARIOS = <?= MdPetIntimacaoRN::$LIMITE_DESTINATARIOS_LOTE ?>;

    function atualizarArvoreAposFechar() {
        if (urlArvore) {
            var ifrArvore = window.top.document.getElementById('ifrArvore');
            if (ifrArvore) {
                ifrArvore.src = urlArvore;
            }
        }
    }

    $(document).ready(function() {
        $('button#sbmConfirmarIntimacao').off('click').one('click', function(e){
            e.preventDefault();
            var btn = $("button#sbmConfirmarIntimacao");
            var btnFechar = $("button#sbmFechar");
            var form = $('#frmMdPetIntimacaoCadastro', window.top.document.getElementById('ifrConteudoVisualizacao').contentDocument.getElementById('ifrVisualizacao').contentDocument);
            var campoDestinatarios = form.find('[name="hdnDadosUsuario2"]');
            var valorDestinatarios = campoDestinatarios.val() || '';
            var destinatarios = valorDestinatarios.split('\u00a5').filter(function(item) {
                return $.trim(item) !== '';
            });
            var contadores = {processados: 0, sucessos: 0, ignorados: 0, erros: 0};
            var contadoresNotificacao = {aceitas: 0, falhas: 0, naoSolicitadas: 0};
            var resultados = [];
            var solicitacoesNotificacao = [];
            var dataHoraInicio = null;

            if (destinatarios.length === 0) {
                alert('Nenhum destinatario foi informado.');
                return;
            }

            if (destinatarios.length > LIMITE_DESTINATARIOS) {
                alert('O limite \u00e9 de ' + LIMITE_DESTINATARIOS + ' destinat\u00e1rios por intima\u00e7\u00e3o. Foram selecionados ' + destinatarios.length + '. Para continuar, remova o excesso de destinat\u00e1rios e tente novamente.');
                return;
            }

            dataHoraInicio = new Date();
            btn.prop('disabled', true).html('Aguarde, gerando intimacoes...');
            btnFechar.prop('disabled', true);
            $('#divProgressoLote').show();
            $('.textoIntimacaoEletronica').hide();
            $('.progress-bar').attr('aria-valuenow', 0).css('width', '0%').html('0%');
            $('#qtdTotal').text(destinatarios.length);

            function obterDescricao(item) {
                var partes = item.split('\u00b1');
                return partes.length > 1 ? partes.slice(1).join('\u00b1') : item;
            }

            function montarDados(item) {
                var dados = form.serializeArray().filter(function(campo) {
                    return campo.name !== 'hdnDadosUsuario'
                        && campo.name !== 'hdnDadosUsuario2'
                        && campo.name !== 'hdnIdDadosUsuario'
                        && campo.name !== 'hdnIdDadosUsuario2'
                        && campo.name !== 'hdnIdUsuarios'
                        && campo.name !== 'selDadosUsuario2';
                });
                dados.push({name: 'hdnDadosUsuario', value: item});
                dados.push({name: 'hdnDadosUsuario2', value: item});
                dados.push({name: 'modo_lote_unitario', value: '1'});
                return $.param(dados);
            }

            function atualizarProgresso(descricao) {
                let progressSize = (contadores.processados / destinatarios.length * 100).toFixed(0);
                $('.progress-bar').attr('aria-valuenow', progressSize).css('width', progressSize + '%').html(progressSize + '%');
                $('#qtdProcessados').text(contadores.processados);
                $('#qtdSucessos').text(contadores.sucessos);
                $('#qtdIgnorados').text(contadores.ignorados);
                $('#qtdErros').text(contadores.erros);
                $('#destinatarioAtual').text(descricao);
            }

            function formatarDataHora(data) {
                return data.toLocaleString('pt-BR', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false
                });
            }

            function dispararNotificacao(url) {
                if (!url) {
                    return Promise.resolve({
                        status: 'nao_solicitada',
                        mensagem: 'n\u00e3o solicitada porque a URL de notifica\u00e7\u00e3o n\u00e3o foi informada.'
                    });
                }

                return fetch(url, {
                    method: 'POST',
                    credentials: 'same-origin',
                    keepalive: true
                }).then(function(response) {
                    if (response.status === 202) {
                        return {
                            status: 'aceita',
                            mensagem: 'solicita\u00e7\u00e3o aceita pelo servidor; entrega n\u00e3o confirmada.'
                        };
                    }

                    return {
                        status: 'falha',
                        mensagem: 'falha ao solicitar (HTTP ' + response.status + ').'
                    };
                }).catch(function() {
                    return {
                        status: 'falha',
                        mensagem: 'falha de comunica\u00e7\u00e3o ao solicitar.'
                    };
                });
            }

            function finalizar() {
                $('#destinatarioAtual').html('Finalizando solicita\u00e7\u00f5es de notifica\u00e7\u00e3o...');

                Promise.all(solicitacoesNotificacao).then(function() {
                    var dataHoraConclusao = new Date();
                    $(".infraBarraLocalizacao h1").html('Gerar Intimação Eletrônica - Concluído');
                    $('.progress').hide();
                    $('#destinatarioAtual').addClass('alert alert-success').html('Processamento concluido.').css('padding', '10px 15px');
                    $('#resumoLote').html(
                        '<strong>Resumo final:</strong> ' + contadores.sucessos + ' geradas, '
                        + contadores.ignorados + ' ignoradas e ' + contadores.erros + ' com erro.'
                    ).show();

                    var conteudo = [
                        'RELATORIO DE PROCESSAMENTO DE INTIMACOES',
                        'In\u00edcio do processamento: ' + formatarDataHora(dataHoraInicio),
                        'Conclus\u00e3o do processamento: ' + formatarDataHora(dataHoraConclusao),
                        '',
                        'RESUMO',
                        'Total processado: ' + contadores.processados,
                        'Sucessos: ' + contadores.sucessos,
                        'Ignorados: ' + contadores.ignorados,
                        'Erros: ' + contadores.erros,
                        '',
                        'NOTIFICA\u00c7\u00d5ES POR E-MAIL',
                        'Solicita\u00e7\u00f5es aceitas: ' + contadoresNotificacao.aceitas,
                        'Falhas ao solicitar: ' + contadoresNotificacao.falhas,
                        'N\u00e3o solicitadas (intima\u00e7\u00f5es geradas sem URL): ' + contadoresNotificacao.naoSolicitadas,
                        'Observa\u00e7\u00e3o: o aceite da solicita\u00e7\u00e3o pelo servidor n\u00e3o confirma a entrega do e-mail.',
                        'Itens ignorados ou com erro de gera\u00e7\u00e3o n\u00e3o possuem notifica\u00e7\u00e3o.',
                        '',
                        'DETALHAMENTO',
                        resultados.join('\r\n')
                    ].join('\r\n');
                    var arquivo = new Blob([conteudo], {type: 'text/plain;charset=utf-8'});
                    $('#lnkBaixarRelatorio').attr('href', URL.createObjectURL(arquivo)).show();

                    btn.hide();
                    btnFechar.prop('disabled', false).html('<span class="infraTeclaAtalho">F</span>echar');
                });
            }

            function processar(indice) {
                if (indice >= destinatarios.length) {
                    finalizar();
                    return;
                }

                var item = destinatarios[indice];
                var descricao = obterDescricao(item);
                $('#destinatarioAtual').html('<strong>Processando ' + (indice + 1) + ' de ' + destinatarios.length + '</strong>: ' + descricao);
                
                $(".infraBarraLocalizacao h1").html('Gerar Intimação Eletrônica - Processando ' + (indice + 1) + ' de ' + destinatarios.length);

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: montarDados(item),
                    dataType: 'json'
                }).done(function(resposta) {
                    if (resposta.status === 'sucesso') {
                        var indiceResultado = resultados.length;
                        contadores.sucessos++;
                        resultados.push('');
                        urlArvore = resposta.url_arvore || urlArvore;
                        solicitacoesNotificacao.push(
                            dispararNotificacao(resposta.url_notificacao).then(function(notificacao) {
                                if (notificacao.status === 'aceita') {
                                    contadoresNotificacao.aceitas++;
                                } else if (notificacao.status === 'nao_solicitada') {
                                    contadoresNotificacao.naoSolicitadas++;
                                } else {
                                    contadoresNotificacao.falhas++;
                                }

                                resultados[indiceResultado] = '[SUCESSO] ' + descricao
                                    + ' - Intimacao gerada com sucesso. Notifica\u00e7\u00e3o por e-mail: '
                                    + notificacao.mensagem;
                            })
                        );
                    } else if (resposta.status === 'ignorado') {
                        contadores.ignorados++;
                        resultados.push('[IGNORADO] ' + descricao + ' - ' + (resposta.mensagem || 'Duplicidade identificada.'));
                    } else {
                        contadores.erros++;
                        resultados.push('[ERRO] ' + descricao + ' - ' + (resposta.mensagem || 'Resposta invalida do servidor.'));
                    }
                }).fail(function(xhr) {
                    var mensagem = xhr.responseJSON && xhr.responseJSON.mensagem
                        ? xhr.responseJSON.mensagem
                        : 'Falha de comunicacao com o servidor da aplicacao.';
                    contadores.erros++;
                    resultados.push('[ERRO] ' + descricao + ' - ' + mensagem);
                }).always(function() {
                    contadores.processados++;
                    atualizarProgresso(descricao);
                    processar(indice + 1);
                });
            }

            processar(0);
        });
    });

<?php
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo,'');
?>
    <div class="clear"></div>
    <div class="textoIntimacaoEletronica">
        <h2><?php echo $texto; ?></h2>
    </div>
    <div id="divProgressoLote" style="display:none; margin: 1rem 0;">
        <div class="progress">
            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
        </div>
        <p id="destinatarioAtual" style="margin: .5rem 0;"></p>
        <p>
            Total: <span id="qtdTotal">0</span> |
            Processados: <span id="qtdProcessados">0</span> |
            Sucessos: <span id="qtdSucessos">0</span> |
            Ignorados: <span id="qtdIgnorados">0</span> |
            Erros: <span id="qtdErros">0</span>
        </p>
        <p id="resumoLote" style="display:none;"></p>
        <a id="lnkBaixarRelatorio" href="#" download="relatorio_processamento_intimacoes.txt" style="display:none;" class="btn btn-primary btn-sm">Baixar relat&oacute;rio TXT</a>
    </div>
    <div style="padding-right: 40%">
<?php 
PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>