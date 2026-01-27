/**
 * ANATEL
 *
 * 26/08/2024 - Atualização por gabrielg.colab - SPASSU
 *
 */
 
#divTipoConferencia {display: none;}
#btnAdicionarDocumento {vertical-align: middle;}
#divTipoConferenciaBotao button#btnAdicionarDocumento {margin-top: 24px}
.infraImgModulo{width:20px; height: 20px;}
#fieldDocumentos{height:auto;}
fieldset .infraCheckboxDiv, fieldset .infraRadioDiv {
    margin-left: 0em;
}

.drop-zone-style {
    width: 100%;
    border: 2px dashed #ccc;
    border-radius: 5px;
    padding: 18px;
    text-align: left;
    font-family: sans-serif;
    color: #555;
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: start;
    justify-content: start;
    min-height: 40px;
    transition: all 0.1s ease-in-out;
}

.drop-zone-style:hover {
    background: #eee;
}

.drop-zone-style::file-selector-button {
    background: #FFF;
    color: #555;
    border: 1px solid #666;
    padding: 4px 10px;
    border-radius: 4px;
    cursor: pointer;
    margin-right: 10pxç
}

.drop-zone-style::file-selector-button:hover {
    background: #0494C7;
    color: white;
}