<script type="text/javascript">
    function inicializar() {
        if ('<?=$_GET['acao']?>' == 'md_pet_int_tipo_intimacao_cadastrar') {
            document.getElementById('txtNome').focus();
        } else if ('<?=$_GET['acao']?>' == 'md_pet_int_tipo_intimacao_consultar') {
            infraDesabilitarCamposAreaDados();
        } else {
            document.getElementById('btnCancelar').focus();
        }
        infraEfeitoTabelas();

        //Ajax para carregar os Tipos de Resposta
        objAjaxIdTipoResposta = new infraAjaxMontarSelectDependente('optTipoRespostaFacultativa', 'selTipoResposta', '<?=$strLinkAjaxTipoResposta?>');
        objAjaxIdTipoResposta.prepararExecucao = function () {
            objTabelaDinamicaTipoResposta.limpar();
            document.getElementById('hdnTipoResposta').value = '';
            document.getElementById('sbmGravarTipoResposta').removeAttribute("disabled");
            return 'tipoResposta=' + document.getElementById('optTipoRespostaFacultativa').value;
        };
        objAjaxIdTipoResposta = new infraAjaxMontarSelectDependente('optTipoRespostaExige', 'selTipoResposta', '<?=$strLinkAjaxTipoResposta?>');
        objAjaxIdTipoResposta.prepararExecucao = function () {
            objTabelaDinamicaTipoResposta.limpar();
            document.getElementById('hdnTipoResposta').value = '';
            document.getElementById('sbmGravarTipoResposta').removeAttribute("disabled");
            return 'tipoResposta=' + document.getElementById('optTipoRespostaExige').value;
        }

        //Insere as linhas de Tipo de Resposta
        objTabelaDinamicaTipoResposta = new infraTabelaDinamica('tblTipoResposta', 'hdnTipoResposta', <?=$strEmailAcoes?>);
        objTabelaDinamicaTipoResposta.alterar = function (arr) {
            document.getElementById('selTipoResposta').value = arr[0];
            document.getElementById('selTipoResposta').value = arr[1];
            document.getElementById('selTipoResposta').value = arr[2];
        };

        objTabelaDinamicaTipoResposta.remover = function (arr) {

            var id = arr[0][0];
            var vinculo = arr[1][0];

            if (objTabelaDinamicaTipoResposta.tbl.rows.length == '2') {
                document.getElementById('divTabelaTipoResposta').style.display = 'none';
            }

            //Adiciona novamente o item ao select
            var id = arr[0];
            $("#selTipoResposta option[value='" + id + "']").show();

            //Habilita o inserir caso o item removido seja Exige Resposta
            if (arr[3] == 'Exige Resposta') {
                document.getElementById('sbmGravarTipoResposta').removeAttribute("disabled");
            }

            return true;

        };

        objTabelaDinamicaTipoResposta.gerarEfeitoTabela = true;

        <? foreach(array_keys($arrAcoes) as $id) { ?>
        objTabelaDinamicaTipoResposta.adicionarAcoes('<?=$id?>', '<?=$arrAcoes[$id]?>');
        <? } ?>

        infraEfeitoTabelas();
        controlarExibicaoTabela();
    }

    function OnSubmitForm() {
        if (infraTrim(document.getElementById('txtNome').value) == '') {
            alert('Informe o Nome.');
            document.getElementById('txtNome').focus();
            return false;
        }

        if (!document.getElementById('optTipoRespostaFacultativa').checked && !document.getElementById('optTipoRespostaExige').checked && !document.getElementById('optTipoSemResposta').checked) {
            alert('Informe o Tipo de Resposta para a Intimação.');
            document.getElementById('selTipoResposta').focus();
            return false;
        }

        if (document.getElementById('optTipoRespostaFacultativa').checked && document.getElementById('optTipoRespostaExige').checked) {
            qtdResp = document.getElementById('tblTipoResposta').rows.length;
            if (qtdResp <= 1) {
                alert('Selecione pelo menos 1(um) Tipo de Resposta.');
                return false;
            }
        }

    }

    function esconderTabelaTipoResposta() {
        document.getElementById('divTabelaTipoResposta').style.display = 'none';

        if (document.getElementById('optTipoRespostaFacultativa').checked || document.getElementById('optTipoRespostaExige').checked) {
            document.getElementById('divInfraAreaDados2').style.display = '';
        } else if (document.getElementById('optTipoSemResposta').checked) {
            document.getElementById('divInfraAreaDados2').style.display = 'none';
        }
        esconderTabela();
    }

    function esconderTabela() {
        document.getElementById('divTabelaTipoResposta').style.display = 'none';
    }

    function controlarExibicaoTabela() {
        var hdnTipoResposta = document.getElementById("hdnTipoResposta").value;
        if (hdnTipoResposta == '') {
            document.getElementById('divTabelaTipoResposta').style.display = 'none';
            if (document.getElementById('optTipoSemResposta').checked) {
                document.getElementById('divInfraAreaDados2').style.display = 'none';
            }
        } else {
            document.getElementById('divTabelaTipoResposta').style.display = '';
        }
    }

    //Transporta os intens do select Para a tabela.
    function transportarTipoResposta() {

        var paramsAjax = {
            id: document.getElementById('selTipoResposta').value
        };

        $.ajax({
            url: '<?=$strUrlBuscaTipoResposta?>',
            type: 'POST',
            dataType: 'XML',
            data: paramsAjax,
            success: function (r) {
                var prazo = $(r).find('Prazo').html();
                var prazoFormatado = prazo != '' ? prazo.replace("(", "") : '';
                prazoFormatado = prazoFormatado != '' ? prazoFormatado.replace(")", "") : '';

                objTabelaDinamicaTipoResposta.adicionar([$(r).find('Id').text(), $(r).find('Vinculado').text(), $("<pre>").text($(r).find('Nome').html()).html(), $("<pre>").text(prazoFormatado).html(), $(r).find('Tipo').text()]);
                controlarExibicaoTabela();
                $("#selTipoResposta option:selected").hide();
                document.getElementById('selTipoResposta').value = '';

                document.getElementById('selTipoResposta').focus();
            },
            error: function (e) {
                console.error('Erro ao processar o XML do SEI: ' + e.responseText);
            }
        });

    }
</script>
