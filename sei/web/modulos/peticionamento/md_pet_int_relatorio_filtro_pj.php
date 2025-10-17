<div class="row">
    <div class="col-sm-9 col-md-9 col-lg-7 col-xl-7">
        <label id="lblDestinatarioPJ" name="lblDestinatarioPJ" for="txtDestinatarioPJ" class="inputSelect"> Pessoa Jurídica: </label>
        <input type="text" id="txtDestinatarioPJ" name="txtDestinatarioPJ" class="infraText form-control" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-lg-10 col-xl-10">
        <div class="form-group">
            <div class="input-group mb-3">
                <select id="selDestinatarioPJ" name="selDestinatarioPJ" size="6" multiple="multiple" class="infraSelect form-select mr-1" tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
                </select>

                <div id="divOpcoesDestinatario">
                    <img id="imgLupaDestinatarioPJ" onclick="objLupaDestinatarioPJ.selecionar(700,500);"
                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal(); ?>/pesquisar.svg?<?= Icone::VERSAO ?>"
                         alt="Selecionar Pessoas Jurícas"
                         title="Selecionar Pessoas Jurícas" class="infraImg"/>
                    <br>
                    <img id="imgExcluirDestinatarioPJ" onclick="objLupaDestinatarioPJ.remover();"
                         src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal(); ?>/remover.svg?<?= Icone::VERSAO ?>"
                         alt="Remover Pessoas Jurícas Selecionados"
                         title="Remover Pessoas Jurícas Selecionados" class="infraImg"/>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    
    objAutoCompletarDestinatarioPJ = new infraAjaxAutoCompletar('hdnIdDestinatario' , 'txtDestinatarioPJ',  '<?= SessaoSEI::getInstance()->assinarLink('modulos/peticionamento/controlador_ajax.php?acao_ajax=contato_auto_completar&origem=relatorios') ?>');
    objAutoCompletarDestinatarioPJ.limparCampo = true;
    objAutoCompletarDestinatarioPJ.tamanhoMinimo = 3;
    objAutoCompletarDestinatarioPJ.prepararExecucao = function(){
        return 'palavras_pesquisa='+document.getElementById('txtDestinatarioPJ').value+'&tipo_contato=J';
    };

    objAutoCompletarDestinatarioPJ.processarResultado = function(id,nome,complemento){

        if (id!=''){
            var options = document.getElementById('selDestinatarioPJ').options;

            if(options != null){
                for(var i=0;i < options.length;i++){
                    if (options[i].value == id){
                        alert('Pessoa Jurídica já consta na lista.');
                        break;
                    }
                }
            }

            if (i==options.length){

                for(i=0;i < options.length;i++){
                    options[i].selected = false;
                }

                opt = infraSelectAdicionarOption(document.getElementById('selDestinatarioPJ'),nome,id);
                objLupaDestinatarioPJ.atualizar();
                opt.selected = true;
            }
            document.getElementById('txtDestinatarioPJ').value = '';
            document.getElementById('txtDestinatarioPJ').focus();
        }
    }

    // PessoaJuridica
    objLupaDestinatarioPJ = new infraLupaSelect('selDestinatarioPJ','hdnDestinatario','<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_pessoa_juridica&tipo_selecao=2&id_object=objLupaDestinatarioPJ&origem=relatorio&select=selDestinatarioPJ&hidden=hdnDestinatario') ?>');
    
</script>