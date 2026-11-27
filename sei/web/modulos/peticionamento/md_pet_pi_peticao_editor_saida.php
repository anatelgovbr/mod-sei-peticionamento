<?php
/**
 * ANATEL
 *
 * Saida do script e da configuracao do editor do Documento Principal.
 * Conteudo montado por MdPetPiPeticaoINT::montarEditorCompletoDocPrincipal (CKEditor 5) ou por
 * MdPetPiPeticaoINT::montarEditorDocumentoPrincipal (CKEditor 4), emitido sem escape
 * por construcao.
 *
 * 17/09/2026 - criado por emerson_b.goncalves - ANATEL
 */

if ($staTipoEditor === EditorRN::$VE_CK5) {
    echo $objEditorDTO->getStrCss();
    echo $objEditorDTO->getStrJs();
} else {
    echo $objEditorDTO->getStrInicializacao();
}
