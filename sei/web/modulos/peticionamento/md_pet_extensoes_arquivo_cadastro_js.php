<?php
$strLinkAjaxPrincipal = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_arquivo_extensao_listar_todos');
$strLinkPrincipalSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_arquivo_extensao_selecionar&tipo_selecao=2&id_object=objLupaPrincipal');
$strLinkComplementarSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_arquivo_extensao_selecionar&tipo_selecao=2&id_object=objLupaComplementar');
?>

<script type="text/javascript">

var objLupaPrincipal = null;
var objAutoCompletarPrincipal = null;
var objLupaComplementar= null;
var objAutoCompletarComplementar = null;

  function inicializar(){
     objLupaPrincipal = new infraLupaSelect('selPrincipal','hdnPrincipal','<?=$strLinkPrincipalSelecao?>');
     objLupaComplementar = new infraLupaSelect('selComplementar','hdnComplementar','<?=$strLinkComplementarSelecao?>');

    objAutoCompletarPrincipal = new infraAjaxAutoCompletar('hdnIdPrincipal','txtPrincipal','<?=$strLinkAjaxPrincipal?>');
    objAutoCompletarPrincipal.limparCampo = true;
    
    objAutoCompletarComplementar = new infraAjaxAutoCompletar('hdnIdComplementar','txtComplementar','<?=$strLinkAjaxPrincipal?>');
    objAutoCompletarComplementar.limparCampo = true;
    objAutoCompletarComplementar.tamanhoMinimo = 3;
    objAutoCompletarPrincipal.tamanhoMinimo = 3;
    objAutoCompletarPrincipal.prepararExecucao = function(){
      return 'extensao='+document.getElementById('txtPrincipal').value;
    };
    
    objAutoCompletarComplementar.prepararExecucao = function(){
      return 'extensao='+document.getElementById('txtComplementar').value;
    };

    objAutoCompletarPrincipal.processarResultado = function(id,descricao,complemento){
      if (id!=''){
        var options = document.getElementById('selPrincipal').options;

        for(var i=0;i < options.length;i++){
          if (options[i].value == id){
            self.setTimeout('alert(\'Extensão já consta na lista.\')',100);
            break;
          }
        }

        if (i==options.length){

        for(i=0;i < options.length;i++){
          options[i].selected = false;
        }

        opt = infraSelectAdicionarOption(document.getElementById('selPrincipal'),descricao,id);

        objLupaPrincipal.atualizar();

        opt.selected = true;
      }

      document.getElementById('txtPrincipal').value = '';
      document.getElementById('txtPrincipal').focus();
    }};
    
    objAutoCompletarComplementar.processarResultado = function(id,descricao,complemento){
      if (id!=''){
        var options = document.getElementById('selComplementar').options;

        for(var i=0;i < options.length;i++){
          if (options[i].value == id){
            self.setTimeout('alert(\'Extensão já consta na lista.\')',100);
            break;
          }
        }

        if (i==options.length){

        for(i=0;i < options.length;i++){
          options[i].selected = false;
        }

        opt = infraSelectAdicionarOption(document.getElementById('selComplementar'),descricao,id);

        objLupaComplementar.atualizar();

        opt.selected = true;
      }

      document.getElementById('txtComplementar').value = '';
      document.getElementById('txtComplementar').focus();
    }};

    objAutoCompletarPrincipal.selecionar('<?=$strIdUnidade?>','<?=PaginaSEI::getInstance()->formatarParametrosJavascript(PaginaSEI::tratarHTML($strDescricaoUnidade))?>');
    objAutoCompletarComplementar.selecionar('<?=$strIdUnidade?>','<?=PaginaSEI::getInstance()->formatarParametrosJavascript(PaginaSEI::tratarHTML($strDescricaoUnidade))?>');

    infraEfeitoTabelas();
}

function OnSubmitForm() {
  return validarCadastro();
}

function validarCadastro() {
  if (infraTrim(document.getElementById('hdnPrincipal').value)=='') {
    alert('Informe pelo menos uma extensão para documento principal.');
    return false;
  }
   if (infraTrim(document.getElementById('hdnComplementar').value)=='') {
    alert('Informe pelo menos uma extensão para documento complementar.');
    return false;
  }
  return true;
}

</script>