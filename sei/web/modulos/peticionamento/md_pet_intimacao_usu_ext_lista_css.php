/**
 * ANATEL
 *
 * 26/08/2024 - Atualização por gabrielg.colab - SPASSU
 *
 */
 
#frmIntimacaoEletronicaLista label[for^=txt],
#frmIntimacaoEletronicaLista label[for^=sel] {display: block;white-space: nowrap;}
#frmIntimacaoEletronicaLista img[id^='imgData'] {vertical-align: middle;}
#frmIntimacaoEletronicaLista input[id^='txtData'] {width: 33%;}
#frmIntimacaoEletronicaLista .selectPadrao {min-width: 200px;max-width: 330px;border: .1em solid #666;}
.bloco {float: left;margin-top: 1%;margin-right: 1%;}
.clear {clear: both;}
.infraLabelOpcional { margin-bottom: 0px !important }

.text-placeholder {
    display: inline-block;
    background-color: #444;
    height: 20px;
    margin: 0;
    border-radius: 4px;
    min-width: 20px;
    opacity: .1;
    animation: fading 1.5s infinite;
}

@keyframes fading {
    0% { opacity: .1; }
    50% { opacity: .25; }
    100% { opacity: .1; }
}