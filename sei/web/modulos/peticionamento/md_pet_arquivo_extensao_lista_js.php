<script type="text/javascript">

function inicializar(){
  if ('<?=$_GET['acao']?>'=='md_pet_arquivo_extensao_selecionar'){
    infraReceberSelecao();
    document.getElementById('btnFecharSelecao').focus();
  }else{
    document.getElementById('btnFechar').focus();
  }
  infraEfeitoTabelas();
}

<? if ($bolAcaoDesativar){ ?>
function acaoDesativar(id,desc){
  if (confirm("Confirma desativa��o da Extens�o de Arquivo \""+desc+"\"?")){
    document.getElementById('hdnInfraItemId').value=id;
    document.getElementById('frmArquivoExtensaoLista').action='<?=$strLinkDesativar?>';
    document.getElementById('frmArquivoExtensaoLista').submit();
  }
}

function acaoDesativacaoMultipla(){
  if (document.getElementById('hdnInfraItensSelecionados').value==''){
    alert('Nenhuma Extens�o de Arquivo selecionada.');
    return;
  }
  if (confirm("Confirma desativa��o das Extens�es de Arquivos selecionadas?")){
    document.getElementById('hdnInfraItemId').value='';
    document.getElementById('frmArquivoExtensaoLista').action='<?=$strLinkDesativar?>';
    document.getElementById('frmArquivoExtensaoLista').submit();
  }
}
<? } ?>

<? if ($bolAcaoReativar){ ?>
function acaoReativar(id,desc){
  if (confirm("Confirma reativa��o da Extens�o de Arquivo \""+desc+"\"?")){
    document.getElementById('hdnInfraItemId').value=id;
    document.getElementById('frmArquivoExtensaoLista').action='<?=$strLinkReativar?>';
    document.getElementById('frmArquivoExtensaoLista').submit();
  }
}

function acaoReativacaoMultipla(){
  if (document.getElementById('hdnInfraItensSelecionados').value==''){
    alert('Nenhuma Extens�o de Arquivo selecionada.');
    return;
  }
  if (confirm("Confirma reativa��o das Extens�es de Arquivos selecionadas?")){
    document.getElementById('hdnInfraItemId').value='';
    document.getElementById('frmArquivoExtensaoLista').action='<?=$strLinkReativar?>';
    document.getElementById('frmArquivoExtensaoLista').submit();
  }
}
<? } ?>

<? if ($bolAcaoExcluir){ ?>
function acaoExcluir(id,desc){
  if (confirm("Confirma exclus�o da Extens�o de Arquivo \""+desc+"\"?")){
    document.getElementById('hdnInfraItemId').value=id;
    document.getElementById('frmArquivoExtensaoLista').action='<?=$strLinkExcluir?>';
    document.getElementById('frmArquivoExtensaoLista').submit();
  }
}

function acaoExclusaoMultipla(){
  if (document.getElementById('hdnInfraItensSelecionados').value==''){
    alert('Nenhuma Extens�o de Arquivo selecionada.');
    return;
  }
  if (confirm("Confirma exclus�o das Extens�es de Arquivos selecionadas?")){
    document.getElementById('hdnInfraItemId').value='';
    document.getElementById('frmArquivoExtensaoLista').action='<?=$strLinkExcluir?>';
    document.getElementById('frmArquivoExtensaoLista').submit();
  }
}
<? } ?>
</script>