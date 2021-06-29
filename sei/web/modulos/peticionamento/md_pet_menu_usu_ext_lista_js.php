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
      if (confirm("Confirma desativação do Menu \""+desc+"\"?")){
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
      if (confirm("Confirma a desativação dos Menus selecionados?")){
        document.getElementById('hdnInfraItemId').value='';
        document.getElementById('frmLista').action='<?=$strLinkDesativar?>';
        document.getElementById('frmLista').submit();
      }
    }

    function acaoReativar(id,desc){
      if (confirm("Confirma reativação do Menu \""+desc+"\"?")){
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
      if (confirm("Confirma a reativação dos Menus selecionados?")){
        document.getElementById('hdnInfraItemId').value='';
        document.getElementById('frmLista').action='<?=$strLinkReativar?>';
        document.getElementById('frmLista').submit();
      }
    }

    function acaoExcluir(id,desc){
      if (confirm("Confirma exclusão do Menu \""+desc+"\"?")){
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
      if (confirm("Confirma a exclusão dos Menus selecionadas?")){
        document.getElementById('hdnInfraItemId').value='';
        document.getElementById('frmLista').action='<?=$strLinkExcluir?>';
        document.getElementById('frmLista').submit();
      }
    }

    function pesquisar(){

       document.getElementById('frmLista').submit();

    }
</script>