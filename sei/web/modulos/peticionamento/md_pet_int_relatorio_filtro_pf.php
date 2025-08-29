<div class="row">
    <div class="col-sm-9 col-md-9 col-lg-7 col-xl-7">
        <label id="lblDestinatarioPF" name="lblDestinatarioPF" for="txtDestinatarioPF" class="inputSelect"> Usuário Externo: </label>
        <input type="text" id="txtDestinatarioPF" name="txtDestinatarioPF" class="infraText form-control"
               tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-lg-10 col-xl-10>
        <div class="form-group">
            <div class="input-group mb-3">
                <select id="selDestinatarioPF" name="selDestinatarioPF" size="6" multiple="multiple" class="infraSelect form-control mr-1" tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
                </select>

                <div id="divOpcoesDestinatario">
                    <img id="imgLupaDestinatarioPF" onclick="objLupaDestinatarioPF.selecionar(700,500);"
                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal(); ?>/pesquisar.svg?<?= Icone::VERSAO ?>"
                         alt="Selecionar Usuários Externos"
                         title="Selecionar Usuários Externos" class="infraImg"/>
                    <br>
                    <img id="imgExcluirDestinatarioPF" onclick="objLupaDestinatarioPF.remover();"
                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal(); ?>/remover.svg?<?= Icone::VERSAO ?>"
                         alt="Remover Usuários Externos Selecionados"
                         title="Remover Usuários Externos Selecionados" class="infraImg"/>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    
    objAutoCompletarDestinatarioPF = new infraAjaxAutoCompletar('hdnIdDestinatario' , 'txtDestinatarioPF',  '<?= SessaoSEI::getInstance()->assinarLink('modulos/peticionamento/controlador_ajax.php?acao_ajax=contato_auto_completar') ?>');
    objAutoCompletarDestinatarioPF.limparCampo = true;
    objAutoCompletarDestinatarioPF.tamanhoMinimo = 3;
    objAutoCompletarDestinatarioPF.prepararExecucao = function(){
        return 'palavras_pesquisa='+document.getElementById('txtDestinatarioPF').value+'&tipo_contato=F';
    };

    objAutoCompletarDestinatarioPF.processarResultado = function(id,nome,complemento){

        if (id!=''){
            var options = document.getElementById('selDestinatarioPF').options;

            if(options != null){
                for(var i=0;i < options.length;i++){
                    if (options[i].value == id){
                        alert('Pessoa Física já consta na lista.');
                        break;
                    }
                }
            }

            if (i==options.length){

                for(i=0;i < options.length;i++){
                    options[i].selected = false;
                }

                opt = infraSelectAdicionarOption(document.getElementById('selDestinatarioPF'),nome,id);
                objLupaDestinatarioPF.atualizar();
                opt.selected = true;
            }
            document.getElementById('txtDestinatarioPF').value = '';
            document.getElementById('txtDestinatarioPF').focus();
        }
    }

    // PessoaJuridica
    objLupaDestinatarioPF = new infraLupaSelect('selDestinatarioPF','hdnDestinatario','<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_pessoa_fisica&tipo_selecao=2&id_object=objLupaDestinatarioPF&origem=relatorio&select=selDestinatarioPF&hidden=hdnDestinatario') ?>');
    
</script>