<?php
/**
 * ANATEL
 *
 * 18/09/2026 - criado por emerson_b.goncalves - ANATEL
 */
?>
var bolConfirmacaoEmAndamento = false;

function redimensionarOrientacoes(ifr) {
    var doc = ifr.contentDocument ? ifr.contentDocument : ifr.contentWindow.document;

    if (!doc || !doc.body) {
        return;
    }

    ifr.style.height = '10px';
    ifr.style.height = Math.max(doc.body.scrollHeight, doc.documentElement.scrollHeight) + 'px';
}

function inicializarConfirmacao() {
    document.addEventListener('keydown', function (event) {
        var bolEnterSenha = event.key === 'Enter' && event.target.id === 'pwdSenha';
        var bolAtalhoConfirmar = event.altKey && !event.ctrlKey && !event.metaKey && event.key.toLowerCase() === 'c';

        if (!bolEnterSenha && !bolAtalhoConfirmar) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();

        var btnConfirmar = document.getElementById('btnConfirmar');
        if (btnConfirmar === null || btnConfirmar.disabled || event.repeat) {
            return;
        }

        btnConfirmar.click();
    }, true);
    if (document.getElementById('pwdSenha') !== null) {
        new MaskedPassword(document.getElementById('pwdSenha'), '\u25CF');
        document.getElementById('pwdSenha').focus();
    }
}

function confirmarPeticionamento() {
    if (bolConfirmacaoEmAndamento) {
        return;
    }

    var strCargoFuncao = '';
    var strSenha = '';
    var selCargoFuncao = document.getElementById('selCargoFuncao');

    if (selCargoFuncao !== null) {
        strCargoFuncao = selCargoFuncao.value;

        if (strCargoFuncao === '' || strCargoFuncao === 'null') {
            avisarAssinatura('Informe o Cargo/Fun\u00e7\u00e3o.');
            selCargoFuncao.focus();
            return;
        }
    }

    var pwdSenha = document.querySelector('input[name="pwdSenha"]');

    if (pwdSenha !== null) {
        strSenha = pwdSenha.value;

        if (strSenha === '') {
            avisarAssinatura('Informe a senha.');
            document.getElementById('pwdSenha').focus();
            return;
        }
    }

    var btnConfirmar = document.getElementById('btnConfirmar');
    var btnCancelar = document.getElementById('btnCancelar');
    var strRotuloConfirmar = btnConfirmar.innerHTML;
    var bolEnvioIniciado = false;

    bolConfirmacaoEmAndamento = true;
    btnConfirmar.disabled = true;
    btnCancelar.disabled = true;

    try {
        if (pwdSenha !== null) {
            var strMensagem = conferirSenhaAssinatura(strSenha);

            if (strMensagem !== '') {
                avisarAssinatura(strMensagem);
                limparSenhaAssinatura();
                return;
            }
        }

        if (window.parent && typeof window.parent.concluirPeticionamentoInterno === 'function') {
            btnConfirmar.textContent = 'Aguarde, peticionando...';
            window.parent.concluirPeticionamentoInterno(strCargoFuncao, strSenha);
            bolEnvioIniciado = true;
            return;
        }

        alert('Tela de peticionamento nao encontrada. Feche esta janela e tente novamente.');
    } finally {
        if (!bolEnvioIniciado) {
            bolConfirmacaoEmAndamento = false;
            btnConfirmar.disabled = false;
            btnConfirmar.innerHTML = strRotuloConfirmar;
            btnCancelar.disabled = false;
        }
    }
}

function conferirSenhaAssinatura(strSenha) {
    var strMensagem = 'N\u00e3o foi poss\u00edvel conferir a senha. Tente novamente.';
    var objAjax = new infraAjaxComplementar(null, '<?= $strLinkAjaxSenha ?>');

    objAjax.limparCampo = false;
    objAjax.mostrarAviso = false;
    objAjax.async = false;

    objAjax.prepararExecucao = function () {
        return 'pwdSenha=' + encodeURIComponent(strSenha);
    };

    objAjax.processarResultado = function (arr) {
        if (arr && arr['Valida'] === 'S') {
            strMensagem = '';
        } else if (arr && 'MensagemValidacao' in arr) {
            strMensagem = arr['MensagemValidacao'];
        }
    };

    objAjax.executar();

    return strMensagem;
}

// Caixa de mensagem da infra (a mesma do aviso de valida\u00e7\u00e3o do servidor), logo abaixo do t\u00edtulo do modal,
// porque a barra de comandos daqui fica embaixo
function avisarAssinatura(strMensagem) {
    infraAlerta(strMensagem, TIPO_MSG_ERRO);

    var divMensagens = document.getElementById('divInfraMensagens');
    var divLocalizacao = document.getElementById('divInfraBarraLocalizacao');

    if (divMensagens !== null && divLocalizacao !== null) {
        divLocalizacao.parentNode.insertBefore(divMensagens, divLocalizacao.nextSibling);
    }
}

function limparSenhaAssinatura() {
    document.getElementById('pwdSenha').value = '';
    document.querySelector('input[name="pwdSenha"]').value = '';
    document.getElementById('pwdSenha').focus();
}

function cancelarPeticionamento() {
    if (bolConfirmacaoEmAndamento) {
        return;
    }

    infraFecharJanelaModal();
}
