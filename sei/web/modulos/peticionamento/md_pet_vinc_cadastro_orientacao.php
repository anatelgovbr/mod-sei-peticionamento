<?php
/**
 * Created by PhpStorm.
 * User: jhon.carvalho
 * Date: 22/12/2017
 * Time: 11:54
 * 26/08/2024 - Atualização por gabrielg.colab - SPASSU
 */
$staTipoEditor= EditorRN::obterTipoEditorSimples();

$objEditorDTO = new EditorDTO();
$objEditorRN = new EditorRN();

if($staTipoEditor==EditorRN::$VE_CK5){
    $objEditorDTO=new EditorDTO();
    $objEditorDTO->setStrNomeCampo('txaConteudo');
    $objEditorDTO->setStrSinSomenteLeitura('N');
    $objEditorDTO->setNumTamanhoEditor(180);
    $objEditorDTO->setStrConteudoInicial(isset($orientacoes) ? $orientacoes : '');
    EditorCk5RN::montarSimples($objEditorDTO);
} else {
    $objEditorDTO->setStrNomeCampo('txaConteudo');
    $objEditorDTO->setNumTamanhoEditor(180);
    $objEditorDTO->setStrSinSomenteLeitura('N');
    $objEditorDTO = $objEditorRN->montarSimples($objEditorDTO);
}

if($staTipoEditor==EditorRN::$VE_CK5){
    echo $objEditorDTO->getStrCss();
    echo $objEditorDTO->getStrJs();
} else {
    echo $objEditorDTO->getStrInicializacao();
}
?>

<div id="divOritentacao" style="overflow: auto; width:100%; padding-top: 0px">
<table id="tbOrientacao">
    <td>
         <?php
            if($staTipoEditor==EditorRN::$VE_CK5){
                ?>
                    <div id="divEditores" class="infra-editor" style="visibility: visible;">
                        <?= $objEditorDTO->getStrHtml(); ?>
                    </div>
                    <?php
            } else {
                ?>
                    <div id="divEditores" class="mb-0">
                        <textarea id="txaConteudo" name="txaConteudo" rows="10" class="infraTextarea" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>"><?= isset($orientacoes) ? $orientacoes : '' ?></textarea>
                        <script type="text/javascript"> <?= $objEditorDTO->getStrEditores(); ?> </script>
                    </div>
                <?php
            }
            ?>
    </td>
</table>
</div>
