<script type="text/javascript">

    document.getElementById('txtUsuario2').focus();

    var objAutoCompletarInteressadoUsuario = null;
    var objAutoCompletarInteressadoRI1225 = null;
    var arrInteressadosNegados = [];
    var objLupaInteressados = null;

    objLupaInteressados = new infraLupaSelect('selDadosUsuario2', 'hdnDadosUsuario2', '<?= $strLinkTipoProcessoSelecaoFLote ?>');

    objAutoCompletarInteressadoRI1225 = new infraAjaxAutoCompletar('hdnIdDadosUsuario2', 'txtUsuario2', '<?= $strLinkAjaxDestinatarios ?>');
    objAutoCompletarInteressadoRI1225.limparCampo = false;

    objAutoCompletarInteressadoRI1225.prepararExecucao = function(){
        return 'txtUsuario='+encodeURIComponent(document.getElementById('txtUsuario2').value);
    };

    infraAdicionarEvento(document.getElementById('txtUsuario2'), 'keyup', tratarEnterUsuarioLote);
    function tratarEnterUsuarioLote(ev){
        var key = infraGetCodigoTecla(ev);
    }

    objAutoCompletarInteressadoRI1225.processarResultado = function(id, descricao, complemento){

        if (id!=''){

            var paramsAjax = {
                paramsBusca: document.getElementById('hdnIdDadosUsuario2').value,
                paramsIdDocumento: document.getElementById('hdnIdDocumento').value
            };

            $.ajax({
                url: '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=usuario_dados_tabela_lote') ?>',
                type: 'POST',
                dataType: 'XML',
                data: paramsAjax,
                beforeSend: function(){
                    infraExibirAviso(false, 'Processando...');
                },
                success: function (r) {

                    infraOcultarAviso();

                    console.log(r);

                    if ($(r).find('Quantidade').text() >= 1 && $(r).find('Cadastro').text() > 0) {
                        alert("A Pessoa Física selecionada já foi intimada a partir deste documento principal por meio do destinatário: \n" + $(r).find('Vinculo').text() + "\n\nPara verificar a lista de destinatários que já receberam o documento principal, consulte \"Ver intimações do processo\".");
                        document.getElementById('hdnIdDadosUsuario2').value = '';
                        document.getElementById('txtUsuario2').value = '';
                        return;
                    }

                    objLupaInteressados.adicionar(id, descricao, document.getElementById('txtUsuario2'));

                    document.getElementById('hdnIdDadosUsuario2').value = '';
                    document.getElementById('txtUsuario2').value = '';

                    showHideConteudo();

                },
                error: function(xhr, status, error) {
                    infraOcultarAviso();
                    console.error('Erro ao processar o XML do SEI: ' + error.responseText);
                },
                always: function(){
                    infraOcultarAviso();
                }
            });

        }
    };

    function showHideConteudo(){

        if($('input[name="hdnDadosUsuario2"]').val().trim() != ''){
            document.getElementById('conteudoHide').style.display = '';
            document.getElementById('lblTipodeResposta').style.display = 'none';

        }else{

            document.getElementById('selTipoIntimacao').value = '0';
            document.getElementById('conteudoHide').style.display = 'none';
            document.getElementById('lblTipodeResposta').style.display = 'none';

        }

    }

    objLupaInteressados.processarRemocao = function(itens){
        for(var i=0;i < itens.length;i++){
            for(var j=0;j < arrInteressadosNegados.length; j++){
                if (itens[i].value == arrInteressadosNegados[j].id_contato) {
                    alert('Interessado \"' + itens[i].text + '\" não pode ser removido porque foi adicionado pela unidade ' + arrInteressadosNegados[j].sigla_unidade + '.');
                    return false;
                }
            }
        }
        return true;
    }

    objLupaInteressados.finalizarRemocao = function(){
        showHideConteudo();
    }

    objLupaInteressados.finalizarSelecao = function(){
        showHideConteudo();
    }

</script>