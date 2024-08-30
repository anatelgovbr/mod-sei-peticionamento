/**
 * ANATEL
 *
 * 26/08/2024 - Atualização por gabrielg.colab - SPASSU
 *
 */

#container{
  width: 100%;
}

.clear {
  clear: both;
}

.bloco {
  float: left;
}

img[name=ajuda] {
  margin-bottom: -4px;
  width: 20px !important;
  height: 20px !important;
}

#txtNumeroCnpj{
  width: 100%;
}
.txtNumeroCnpjAlt{
  width: 155px !important;
}
#txtRazaoSocial{width:100%}
#txtRazaoSocialAlt{width:100%;}
#txtNumeroCpfResponsavel{width:100%}
#txtNumeroCpfResponsavelAlt{width:100%}
#txtNomeResponsavelLegal{width:100%}
#txtNomeResponsavelLegalAlt{width:100%}
#txtLogradouro{width:100%}
#txtBairro{width:100%;}
#slUf{width: 100%;}
#selCidade{width: 100%;}
#txtNumeroCEP{width: 100%;}
#selTipoDocumento{width:100%;}
#selNivelAcesso{width: 100%;}


#txtRazaoSocialWsdl{
    margin-left: 0.2%;
}

#imgNivelAcesso {height: 1.3em !important; width: 1.3em !important; margin-bottom: -4px;}
#imgHipoteseLegal {height: 1.3em !important; width: 1.3em !important; margin-bottom: -4px;}

ol.Numerada { counter-reset: item; margin-top: 0.1% }
li.Numerada { display: block }
li.Numerada:before { content: counters(item, ".") ". "; counter-increment: item }

.textoRegistroPessoaFisica{margin-top:20;}

#fldOrientacoes {height: auto; width: 96%; margin-bottom: 15px; padding: 20px}
#fieldsetPessoaJuridicaConsulta {height: auto; width: 96%; padding: 20px;}
#informacaoPJ {height: auto; width: 100%; margin-bottom: 11px; padding: 20px;}
#registroPessoaJuridica {height: auto; width: 100%; margin-bottom: 15px; padding: 20px}
#informacaoPJAlterar {height: auto; width: 96%;  margin-bottom: 15px; padding: 20px}
#fieldDocumentos {height: auto; width: 96%; margin-bottom: 11px; padding: 20px}
.sizeFieldset {height:auto; width: 86%;}
.fieldsetClear {border:none !important;}
#lblCaptcha{margin-top:15px; margin-right:5px}
#txtCaptcha{width:130px}

fieldset .infraCheckboxDiv, fieldset .infraRadioDiv {
    margin-left: 0em;
}
button {
    margin-left: 5px;
}
.card {
    width: 14rem; 
    margin-bottom: 15px;
}
.card-body {
    padding: 10px;
}
