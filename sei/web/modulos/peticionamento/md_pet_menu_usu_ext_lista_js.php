<script type="text/javascript">
    function inicializar(){
      if ('<?=$_GET['acao']?>'=='menu_peticionamento_usuario_externo_selecionar'){
        infraReceberSelecao();
        document.getElementById('btnFecharSelecao').focus();
      }else{
        document.getElementById('btnFechar').focus();
      }
      infraEfeitoTabelas();
    }

    function acaoDesativar(id,desc){
      if (confirm("Confirma desativa��o do Menu \""+desc+"\"?")){
        document.getElementById('hdnInfraItemId').value=id;
        document.getElementById('frmLista').action='<?=$strLinkDesativar?>';
        document.getElementById('frmLista').submit();
      }
    }

    function acaoDesativacaoMultipla(){
      if (document.getElementById('hdnInfraItensSelecionados').value==''){
        alert('Nenhum menu selecionado.');
        return;
      }
      if (confirm("Confirma a desativa��o dos Menus selecionados?")){
        document.getElementById('hdnInfraItemId').value='';
        document.getElementById('frmLista').action='<?=$strLinkDesativar?>';
        document.getElementById('frmLista').submit();
      }
    }

    function acaoReativar(id,desc){
      if (confirm("Confirma reativa��o do Menu \""+desc+"\"?")){
        document.getElementById('hdnInfraItemId').value=id;
        document.getElementById('frmLista').action='<?=$strLinkReativar?>';
        document.getElementById('frmLista').submit();
      }
    }

    function acaoReativacaoMultipla(){
      if (document.getElementById('hdnInfraItensSelecionados').value==''){
        alert('Nenhum Menu selecionado.');
        return;
      }
      if (confirm("Confirma a reativa��o dos Menus selecionados?")){
        document.getElementById('hdnInfraItemId').value='';
        document.getElementById('frmLista').action='<?=$strLinkReativar?>';
        document.getElementById('frmLista').submit();
      }
    }

    function acaoExcluir(id,desc){
      if (confirm("Confirma exclus�o do Menu \""+desc+"\"?")){
        document.getElementById('hdnInfraItemId').value=id;
        document.getElementById('frmLista').action='<?=$strLinkExcluir?>';
        document.getElementById('frmLista').submit();
      }
    }

    function acaoExclusaoMultipla(){
      if (document.getElementById('hdnInfraItensSelecionados').value==''){
        alert('Nenhum menu selecionado.');
        return;
      }
      if (confirm("Confirma a exclus�o dos Menus selecionadas?")){
        document.getElementById('hdnInfraItemId').value='';
        document.getElementById('frmLista').action='<?=$strLinkExcluir?>';
        document.getElementById('frmLista').submit();
      }
    }

    function pesquisar(){

       document.getElementById('frmLista').submit();

    }
</script>