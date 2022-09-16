<script type="text/javascript">
    function pesquisarUF(idOrgao) {
        document.getElementById("selUF").disabled = false;
        document.getElementById('hdnIdOrgao').value = idOrgao.value;
        document.getElementById('hdnIdUf').value = '';
        document.getElementById('hdnIdCidade').value = '';
        document.getElementById("selCidade").disabled = false;
        document.getElementById("cidadeHidd").style.display = "none";

        if (document.getElementById("selOrgao").value == "") {
            //Inserindo 'Todos' nas combos UF e Cidade
            //Escondendo as Combos UF e Cidade
            document.getElementById("UFHidd").style.display = "none";
            document.getElementById("cidadeHidd").style.display = "none";

            document.getElementById('hdnIdOrgao').value = '';
            document.getElementById('hdnIdUf').value = '';
            document.getElementById('hdnIdCidade').value = '';

            //Travando as combos caso o orgão esteja na opção TOdos
            document.getElementById("selUF").disabled = true;
            document.getElementById("selCidade").disabled = true;
        }
        mudarTpProcesso();
        infraSelectLimpar('selUF');
        infraSelectLimpar('selCidade');

        if (document.getElementById("selOrgao").value == "") {

            document.getElementById("UFHidd").style.display = "none";
            document.getElementById("cidadeHidd").style.display = "none";

            //Inserindo 'Todos' nas combos UF e Cidade
            var selectMultiple = document.getElementById('selUF');
            var opt = document.createElement('option');
            opt.value = "";
            opt.innerHTML = "Todos";
            selectMultiple.appendChild(opt);

            var selectMultipleCidade = document.getElementById('selCidade');
            var optCidade = document.createElement('option');
            optCidade.value = "";
            optCidade.innerHTML = "Todos";
            selectMultipleCidade.appendChild(optCidade);
        }


        //Setando orgão
        if (document.getElementById("selOrgao").value != "") {

            $.ajax({
                dataType: 'xml',
                method: 'POST',
                url: '<?php echo SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_consultar_tipo_processo_uf');?>',
                data: {
                    'idOrgao': idOrgao.value
                },
                error: function (dados) {
                    console.log(dados);
                },
                success: function (data) {
                    var selectMultiple = document.getElementById('selUF');

                    try {
                        var count = $(data).find("item").length;
                        if (count > 1) {
                            var opt = document.createElement('option');
                            opt.value = "";
                            opt.innerHTML = "Todos";
                            selectMultiple.appendChild(opt);

                        }

                        $.each($(data).find('item'), function (i, j) {

                            //Caso tenha somente uma uf vinculado com o orgão.
                            var count = $(data).find("item").length;

                            if (count < 2) {
                                document.getElementById("UFHidd").style.display = "none";
                            } else {
                                document.getElementById("UFHidd").style.display = "";
                            }

                            if (count < 2) {
                                document.getElementById('hdnIdUf').value = $(j).attr("id");
                                if (document.getElementById("selOrgao").value == "") {
                                    document.getElementById('hdnIdUf').value = '';
                                    document.getElementById('hdnIdCidade').value = '';

                                    //Travando as combos caso o orgão esteja na opção TOdos
                                    document.getElementById("selUF").disabled = true;
                                    document.getElementById("selCidade").disabled = true;

                                }

                                mudarTpProcesso();

                                $.ajax({
                                    dataType: 'xml',
                                    method: 'POST',
                                    url: '<?php echo SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_consultar_tipo_processo_cidade');?>',
                                    data: {
                                        'idUf': $(j).attr("id"),
                                        'idOrgao': idOrgao.value
                                    },
                                    error: function (dados) {
                                        console.log(dados);
                                    },
                                    success: function (data) {

                                        var selectMultiple = document.getElementById('selCidade');

                                        try {

                                            var count = $(data).find("item").length;
                                            if (count < 2) {
                                                document.getElementById("cidadeHidd").style.display = "none";
                                            } else {
                                                document.getElementById("cidadeHidd").style.display = "";
                                            }

                                            if (count < 2) {
                                                document.getElementById("selCidade").disabled = true;
                                            }
                                            //Caso retorne mais de uma Cidade
                                            if (count > 1) {
                                                var opt = document.createElement('option');
                                                opt.value = "";
                                                opt.innerHTML = "Todos";
                                                selectMultiple.appendChild(opt);
                                            }

                                            $.each($(data).find('item'), function (i, j) {
                                                //Atribuindo o Id da cidade caso haja somente uma cidade
                                                var count = $(data).find("item").length;
                                                if (count < 2) {
                                                    document.getElementById('hdnIdCidade').value = $(j).attr("id");
                                                    //mudarTpProcesso();
                                                }

                                                var opt = document.createElement('option');
                                                opt.value = $(j).attr("id");
                                                opt.innerHTML = $(j).attr("descricao");
                                                selectMultiple.appendChild(opt);
                                            });

                                            var div = document.getElementById('selCidade');
                                            div.appendChild(selectMultiple);


                                        } catch (err) {

                                        }

                                    }

                                });

                                document.getElementById("selUF").disabled = true;

                            }

                            if (document.getElementById("selOrgao").value != "") {
                                var opt = document.createElement('option');
                                opt.value = $(j).attr("id");
                                opt.innerHTML = $(j).attr("descricao");
                                selectMultiple.appendChild(opt);

                            }
                        });

                        var div = document.getElementById('selUF');
                        div.appendChild(selectMultiple);

                    } catch (err) {

                    }

                }

            });
        }


    }


    //Uf
    function pesquisarCidade(idUf) {
        document.getElementById("selCidade").disabled = false;
        document.getElementById('hdnIdUf').value = idUf.value;
        document.getElementById('hdnIdCidade').value = '';
        mudarTpProcesso();
        infraSelectLimpar('selCidade');

        if (document.getElementById("selUF").value == "") {
            document.getElementById("cidadeHidd").style.display = "none";

            //Inserindo 'Todos' na combo  Cidade
            var selectMultipleCidade = document.getElementById('selCidade');
            var optCidade = document.createElement('option');
            optCidade.value = "";
            optCidade.innerHTML = "Todos";
            selectMultipleCidade.appendChild(optCidade);
            document.getElementById("selCidade").disabled = true;
        }


        $.ajax({
            dataType: 'xml',
            method: 'POST',
            url: '<?php echo SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_consultar_tipo_processo_cidade');?>',
            data: {
                'idOrgao': document.getElementById('hdnIdOrgao').value,
                'idUf': idUf.value
            },
            error: function (dados) {
                console.log(dados);
            },
            success: function (data) {

                var selectMultiple = document.getElementById('selCidade');

                //Coloca vazio caso seja mais de um
                try {
                    var count = $(data).find('item').length;
                    if (count > 1) {
                        var opt = document.createElement('option');
                        opt.value = "";
                        opt.innerHTML = "Todos";
                        selectMultiple.appendChild(opt);
                    }

                    $.each($(data).find('item'), function (i, j) {

                        var count = $(data).find("item").length;
                        //Escondendo Elemento caso retorne somente um Elemento

                        if (count < 2) {
                            document.getElementById("cidadeHidd").style.display = "none";
                        } else {
                            document.getElementById("cidadeHidd").style.display = "";
                        }


                        if (count < 2) {

                            //Caso a Uf retorne somente uma cidade
                            document.getElementById('hdnIdCidade').value = $(j).attr("id");
                            mudarTpProcesso();
                            $.ajax({
                                dataType: 'xml',
                                method: 'POST',
                                url: '<?php echo SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_consultar_tipo_processo_cidade');?>',
                                data: {
                                    'idUf': $(j).attr("id")
                                },
                                error: function (dados) {
                                    console.log(dados);
                                },
                                success: function (data) {

                                    var selectMultiple = document.getElementById('selCidade');
                                    try {
                                        $.each($(data).find('item'), function (i, j) {
                                            //Atribuindo o Id da cidade caso haja somente uma cidade
                                            var count = $(data).find("item").length;
                                            if (count < 2) {
                                                document.getElementById('hdnIdCidade').value = $(j).attr("id");
                                            }
                                            var opt = document.createElement('option');
                                            opt.value = $(j).attr("id");
                                            opt.innerHTML = $(j).attr("descricao");
                                            selectMultiple.appendChild(opt);
                                        });

                                        var div = document.getElementById('selCidade');
                                        div.appendChild(selectMultiple);

                                    } catch (err) {

                                    }

                                }

                            });

                            document.getElementById("selCidade").disabled = true;
                        }

                        var opt = document.createElement('option');
                        opt.value = $(j).attr("id");
                        opt.innerHTML = $(j).attr("descricao");
                        selectMultiple.appendChild(opt);
                    });

                    var div = document.getElementById('selCidade');
                    div.appendChild(selectMultiple);

                } catch (err) {

                }

            }

        });

    }

    function pesquisarFinal(idCidade) {
        document.getElementById('hdnIdCidade').value = idCidade.value;
        mudarTpProcesso();


    }

    function mudarTpProcesso() {

//Somente se o usuário escolher opção todos
        if (document.getElementById('hdnIdOrgao').value == '' && document.getElementById('hdnIdUf').value == '' && document.getElementById('hdnIdCidade').value == '') {
            var filtros = {};
        }


        if (document.getElementById('hdnIdOrgao').value != '' && document.getElementById('hdnIdUf').value == '') {
            var filtros = {orgao: document.getElementById('hdnIdOrgao').value};
        } else if (document.getElementById('hdnIdOrgao').value != '' && document.getElementById('hdnIdUf').value != '') {
            var filtros = {
                orgao: document.getElementById('hdnIdOrgao').value,
                uf: document.getElementById('hdnIdUf').value
            };
        }
        if (document.getElementById('hdnIdOrgao').value != '' && document.getElementById('hdnIdUf').value != '' && document.getElementById('hdnIdCidade').value != '') {
            var filtros = {
                orgao: document.getElementById('hdnIdOrgao').value,
                uf: document.getElementById('hdnIdUf').value,
                cidade: document.getElementById('hdnIdCidade').value
            };
        }

        //Somente se o usuário escolher a UF quando entrar na tela
        if (document.getElementById('hdnIdUf').value != '' && document.getElementById('hdnIdOrgao').value == '' && document.getElementById('hdnIdCidade').value == '') {
            var filtros = {uf: document.getElementById('hdnIdUf').value};
            //Uf com unica cidade
        }

        //Somente se o usuário escolher a Cidade quando entrar na tela
        if (document.getElementById('hdnIdUf').value == '' && document.getElementById('hdnIdOrgao').value == '' && document.getElementById('hdnIdCidade').value != '') {
            var filtros = {cidade: document.getElementById('hdnIdCidade').value};

        }

        //Somente se o usuário escolher a Uf e Cidade quando entrar na tela
        if (document.getElementById('hdnIdUf').value != '' && document.getElementById('hdnIdOrgao').value == '' && document.getElementById('hdnIdCidade').value != '') {
            var filtros = {
                cidade: document.getElementById('hdnIdCidade').value,
                uf: document.getElementById('hdnIdUf').value
            };

        }

        $.ajax({
            dataType: 'xml',
            method: 'POST',
            url: '<?php echo SessaoSEIExterna::getInstance()->assinarLink('controlador_ajax_externo.php?acao_ajax=md_pet_consultar_tipo_processo_externo');?>',
            data: filtros,
            error: function (dados) {
                console.log(dados);
            },
            success: function (data) {
                var url = '';
                var conteudo = '';
                var urlFinal = '';
                try {

                    $.each($(data).find('item'), function (i, j) {

                        url = $(j).attr("id");

                        //Url dinâmica

                        if (document.getElementById('hdnIdUf').value != '' && document.getElementById('hdnIdOrgao').value == '' && document.getElementById('hdnIdCidade').value == '') {
                            //  var troca = url.split("&")[2];
                            // urlFinal =  url.replace("id_uf=x",troca.replace("x",document.getElementById('hdnIdUf').value));

                        }

                        conteudo += '<tr class="infraTrClara" id="' + $(j).attr("descricao") + '" onmouseover="classChange(this)" onmouseout="removeClass(this)"  data-desc="' + $(j).attr("descricao").toLowerCase() + '"><td><a href="' + $(j).attr("id") + '"  title="' + $(j).attr("complemento") + '" class="ancoraOpcao">' + $(j).attr("descricao") + '</a> </td></tr>';

                    });

                    document.getElementById('tblTipoProcedimento').innerHTML = conteudo

                } catch (err) {

                }

            }

        });


    }

    function classChange(id) {
        document.getElementById(id.id).className = "infraTrSelecionada";
    }

    function removeClass(id) {
        document.getElementById(id.id).classList.remove("infraTrSelecionada");
    }

    function filtro() {

        seiPrepararFiltroTabela(document.getElementById('tblTipoProcedimento'), document.getElementById('txtFiltro'));

    }

    function inicializar() {
        infraEfeitoTabelas();

        if (document.getElementById("hdnIdOrgaoUnico").value == "U") {
            document.getElementById("selUF").disabled = false;
        } else {
            document.getElementById("selUF").disabled = true;
        }
        document.getElementsByTagName("BODY")[0].onresize = function () {
            resizeIFramePorConteudo()
        };
    }

    //Filtro JS

    function seiPrepararFiltroTabela(objTabela, objInput) {
        $(objInput).on('keyup', objTabela, seiFiltrarTabela);
        $(objInput).focus();
        var tbody = $(objTabela).find('tbody');
        tbody.find('tr').each(function () {
            $(this).removeAttr('onmouseover').removeAttr('onmouseout');
        });
        tbody.on('mouseenter', 'tr', function (e) {
            $('.infraTrSelecionada').removeClass('infraTrSelecionada');
            $(e.currentTarget).addClass('infraTrSelecionada').find('.ancoraOpcao').focus();
        });
        $(document).on('keydown', function (e) {
            if (e.which != 40 && e.which != 38) return;
            var sel = $('.infraTrSelecionada');
            if (sel.length == 0) {
                sel = tbody.find('tr:visible:first').addClass('infraTrSelecionada');
            } else if (e.which == 40) {
                if (sel.nextAll('tr:visible').length != 0) {
                    sel.removeClass('infraTrSelecionada');
                    sel = sel.nextAll('tr:visible:first').addClass('infraTrSelecionada');
                }
            } else {
                if (sel.prevAll('tr:visible').length != 0) {
                    sel.removeClass('infraTrSelecionada');
                    sel = sel.prevAll('tr:visible:first').addClass('infraTrSelecionada');
                }
            }
            sel.find('.ancoraOpcao').focus();
            e.preventDefault();
        })
    }


    function seiFiltrarTabela(event) {
        var tbl = $(event.data).find('tbody');
        var filtro = $(this).val();

        if (filtro.length > 0) {
            $('.infraTrSelecionada:hidden').removeClass('infraTrSelecionada');
            filtro = infraRetirarAcentos(filtro).toLowerCase();
            tbl.find('tr').each(function () {
                var ancora = $(this).find('.ancoraOpcao');
                var descricao = $(this).attr('data-desc');

                var i = descricao.indexOf(filtro);
                if (i == -1)
                    $(this).hide();
                else {
                    $(this).show();
                    $(this).val();
                    var text = ancora.text();
                    var html = '';
                    var ini = 0;
                    while (i != -1) {
                        html += text.substring(ini, i);
                        html += '<span class="infraSpanRealce">';
                        html += text.substr(i, filtro.length);
                        html += '</span>';
                        ini = i + filtro.length;
                        i = descricao.indexOf(filtro, ini);
                    }
                    html += text.substr(ini);
                    ancora.html(html);
                }
            });
        } else {
            tbl.find('tr').show();
            tbl.find('.ancoraOpcao').each(function () {
                $(this).html($(this).text());
            });
        }
    }


    function OnSubmitForm() {
        return true;
    }

    function resizeIFramePorConteudo() {
        var id = 'ifrConteudoHTML';
        var ifrm = document.getElementById(id);
        ifrm.style.visibility = 'hidden';
        ifrm.style.height = "10px";

        var doc = ifrm.contentDocument ? ifrm.contentDocument : ifrm.contentWindow.document;
        doc = doc || document;
        var body = doc.body, html = doc.documentElement;

        var width = Math.max(body.scrollWidth, body.offsetWidth,
            html.clientWidth, html.scrollWidth, html.offsetWidth);
        ifrm.style.width = '100%';

        var height = Math.max(body.scrollHeight, body.offsetHeight,
            html.clientHeight, html.scrollHeight, html.offsetHeight);
        ifrm.style.height = height + 'px';

        ifrm.style.visibility = 'visible';
    }

    document.getElementById('ifrConteudoHTML').onload = function () {
        resizeIFramePorConteudo();
    }

</script>