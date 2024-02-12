<?php
/**
 * Created by PhpStorm.
 * User: jhon.carvalho
 * Date: 22/12/2017
 * Time: 11:54
 */

$objEditorDTO = new EditorDTO();
$objEditorRN = new EditorRN();
$objEditorDTO->setStrNomeCampo('txaConteudo');
$objEditorDTO->setNumTamanhoEditor(180);
$objEditorDTO->setStrSinSomenteLeitura('N');

$retEditor = $objEditorRN->montarSimples($objEditorDTO);

echo $retEditor->getStrInicializacao();
?>

<div id="divOritentacao" style="overflow: auto; width:100%; padding-top: 0px">
<table id="tbOrientacao">
    <td>
        <div id="divEditores" style="overflow: auto; width:100%">
            <textarea type="text" id="txaConteudo" rows="3" name="txtOrientacoes" class="infraText"
                      tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"><?=$orientacoes; ?></textarea>
            <script type="text/javascript">
                <?=$retEditor->getStrEditores();?>
            </script>
        </div>
    </td>
</table>
</div>
