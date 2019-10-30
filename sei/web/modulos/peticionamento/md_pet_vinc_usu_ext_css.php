#container{
  width: 100%;
}

.clear {
  clear: both;
}

.bloco {
  float: left;
  margin-top: 1%;
  margin-right: 0.5%;
}

label[for^=txt] {
  display: block;
  white-space: nowrap;
}
label[for^=s] {
  display: block;
  white-space: nowrap;
}
label[for^=file] {
  display: block;
  white-space: nowrap;
}

img[name=ajuda] {
  margin-bottom: -4px;
  width: 16px !important;
  height: 16px !important;
}

#txtNumeroCnpj{
  width: 120px;
}
.txtNumeroCnpjAlt{
  width: 155px !important;
}
#txtRazaoSocial{
  width:640px;
}
#txtRazaoSocialAlt{
  width:460px;
}
#txtNumeroCpfResponsavel{
  width: 166px;
}
#txtNumeroCpfResponsavelAlt{
  width: 155px;
}
#txtNomeResponsavelLegal{
  width:460px;
}
#txtNomeResponsavelLegalAlt{
  width:460px;
}
#txtLogradouro{
  width:640px;
}
#txtBairro{
  width:248px;
}
#slUf{
  width: 50px;
}
#selCidade{
  width: 248px;
}
#txtNumeroCEP{
  width: 60px;
}
#selTipoDocumento{
  min-width: 200px;
  max-width: 330px;
}
#txtComplementoTipoDocumento{
  width: 230px;
}
#selNivelAcesso{
  min-width: 120px;
  max-width: 120px;
}
#selTipoConferencia{
  min-width: 200px;
  max-width: 330px;
}
#btnAdicionarDocumento {
  margin-top: 15px;
  margin-left: -5px;
}

#txtRazaoSocialWsdl{
    margin-left: 0.2%;
}

#imgNivelAcesso {height: 1.3em !important; width: 1.3em !important; margin-bottom: -4px;}
#selNivelAcesso {min-width: 120px !important; max-width: 120px !important;}
#imgHipoteseLegal {height: 1.3em !important; width: 1.3em !important; margin-bottom: -4px;}
#selHipoteseLegal {float: left; max-width:100% !important;}

ol.Numerada { counter-reset: item; margin-top: 0.1% }
li.Numerada { display: block }
li.Numerada:before { content: counters(item, ".") ". "; counter-increment: item }