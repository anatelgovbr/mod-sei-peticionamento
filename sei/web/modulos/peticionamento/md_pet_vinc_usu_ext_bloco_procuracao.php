<?php
$disabledConsultar = $stConsultar  ? 'disabled="disabled"' : null;
?>
<style type="text/css">
#fieldUsuarioProcuracao {height: auto; width: 96%; margin-bottom: 11px;}
.sizeFieldset {height:auto; width: 86%;}
.fieldsetClear {border:none !important;}
</style>

<fieldset  id="fieldUsuarioProcuracao" class="infraFieldset sizeFieldset" style="display: none; width: auto;">
    <legend class="infraLegend"> Procura��es Eletr�nicas Especiais</legend>
    <div class="clear"></div>
    <div class="bloco" style="margin-top: 2%;">
        <label class="infraLabel">A Procura��o Eletr�nica Especial concede, no �mbito do(a) <?=$descricaoOrgao?>, ao Usu�rio Externo poderes para:</label>
        <label>
            <ol style="margin-top: 0%;">
                <li>Gerenciar o cadastro da Pessoa Jur�dica Outorgante (exceto alterar o Respons�vel Legal ou outros Procuradores Especiais).</li>
                <li>Receber Intima��es Eletr�nicas e realizar Peticionamento Eletr�nico em nome da Pessoa Jur�dica Outorgante, com todos os poderes previstos no sistema.</li>
                <li>Conceder Procura��es Eletr�nicas a outros Usu�rios Externos, em �mbito geral ou para processos espec�ficos, conforme poderes estabelecidos, para representa��o da Pessoa Jur�dica Outorgante.</li>
            </ol>
        </label>
        <label class="infraLabel">Ao conceder a Procura��o Eletr�nica Especial, voc� se declara ciente de que:</label>
        <label>
            <ol style="margin-top: 0%;">
                <li style="list-style-type: square">Poder�, a qualquer tempo, por meio do SEI-<?=$siglaOrgao?>, revogar a Procura��o Eletr�nica Especial;</li>
                <li style="list-style-type: square">O Outorgado poder�, a qualquer tempo, por meio do SEI-<?=$siglaOrgao?>, renunciar a Procura��o Eletr�nica Especial;</li>
                <li style="list-style-type: square">A validade desta Procura��o est� circunscrita ao(�) <?=$siglaOrgao?> e por tempo indeterminado, salvo se revogada ou renunciada, de modo que ela n�o pode ser usada para convalidar quaisquer atos praticados pelo Outorgado em representa��o da Pessoa Jur�dica no �mbito de outros �rg�os ou entidades</li>
            </ol>
        </label>
        <label class="infraLabel">Caso concorde com os termos apresentados, indique abaixo o Usu�rio Externo para o qual deseja conceder Procura��o Eletr�nica Especial.</label>
        <label class="infraLabelObrigatorio"><br><br>Aten��o: </label><label class="infraLabel">Para poder receber uma Procura��o Eletr�nica o Usu�rio Externo j� deve possuir cadastro no SEI-<?=$siglaOrgao?> liberado.</label>
    </div>

    <div class="clear"></div>
    <div class="container" style="margin-top: 2%">
        <div class="bloco" id="blcProcCPFUsuario">
            <label for="txtNumeroCpfProcuracao" class="infraLabelObrigatorio">CPF do Usu�rio Externo: 
               <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" id="imgAjudaUsuario" <?= PaginaSEI::montarTitleTooltip('A pesquisa � realizada somente sobre Usu�rios Externos liberados. \n \n A pesquisa � efetuada pelo CPF do Usu�rio Externo.') ?> class="infraImg"/>
            </label>  
            <input type="text" class="infraText" 
                id="txtNumeroCpfProcurador" name="txtNumeroCpfProcurador" maxlength="14" size=21 
                   tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"
                   onkeypress="return infraMascaraCPF(this, event);" 
                   onchange="validaCpf(this)"/>
            &nbsp;&nbsp;<button type="button" name="btnValidarUsuario" id="btnValidarUsuario" value="Validar"
                    onclick="consultarUsuarioExternoValido();"
                    class="infraButton" tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">Validar
            </button>
        </div>
        <div class="bloco" id="blcProcNomeUsuario">
            <label for="txtEmail" class="infraLabelObrigatorio">Nome do Usu�rio Externo: </label>
            <input <?php echo $disabledConsultar?> name="txtNomeProcurador" id="txtNomeProcurador" type="text" class="infraText campoPadrao infraAutoCompletar" disabled="disabled" onkeypress="return infraMascaraTexto(this,event,50); " size=60 tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"/>
        </div>
        <div class="bloco" id="blcProcBtnAdicionar">
            <label for="txt" class="infraLabelObrigatorio">&nbsp;</label>
            <button type="button" class="infraButton" name="btnAdicionarProcurador" id="btnAdicionarProcurador"
              <?php echo $disabledConsultar?> style="display:none"
                    onclick="criarRegistroTabelaProcuracao();"
                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados(); ?>">Adicionar
            </button>
        </div>
    </div>

    <div class="clear"></div>
    <div class="bloco" style="width: 100%;">
        <table width="99%" class="infraTable" summary="Procura��es" id="tbUsuarioProcuracao" style="display: none" >
            <caption class="infraCaption">&nbsp;</caption>
            <tr>
                <th class="infraTh" width="0" style="display: none;">ID Usuario Externo</th>
                <th class="infraTh" width="0">CPF</th>
                <th class="infraTh" width="0">Nome do Usu�rio</th>
                <th class="infraTh" width="5%">A��es</th>
            </tr>
            <?php if(!is_null($arrRepresentante)){?>
                <?php foreach ($arrRepresentante as $representante){?>
                    <tr class="infraTrClara">
                        <td class="infraTd" style="display: none;"><div><?php echo $representante->getNumIdContato()?></div></td>
                        <td class="infraTd"><div><?php echo InfraUtil::formatarCpf($representante->getStrCpfProcurador())?></div></td>
                        <td class="infraTd"><div><?php echo $representante->getStrNomeProcurador()?></div></td>
                        <td align="center" valign="center"></td>
                    </tr>
                <?php }?>
            <?php }?>
        </table>
    </div>
    <input type="hidden" name="hdnCPF" id="hdnCPF"/>
    <input type="hidden" name="hdnIdUsuarioProcuracao" id="hdnIdUsuarioProcuracao"/>
    <input type="hidden" name="hdnTbUsuarioProcuracao" id="hdnTbUsuarioProcuracao"/>
</fieldset>
<BR id="fieldUsuarioProcuracao_BR" style="display: none;">

<script>
    var objTabelaDinamicaUsuarioProcuracao = null;
    iniciarTabelaDinamicaUsuarioProcuracao();

    function iniciarTabelaDinamicaUsuarioProcuracao() {
        objTabelaDinamicaUsuarioProcuracao = new infraTabelaDinamica('tbUsuarioProcuracao', 'hdnTbUsuarioProcuracao', false, true);
        objTabelaDinamicaUsuarioProcuracao.gerarEfeitoTabela = true;
        objTabelaDinamicaUsuarioProcuracao.remover = function () {
            verificaTabelaProcuracao(1);
            return true;
        };
    }

    function criarRegistroTabelaProcuracao() {
        var noUsuario = document.getElementById('txtNomeProcurador').value.trim();

        if (noUsuario.length == 0) {
            alert('Nome do Usu�rio � de Preenchimento Obrig�torio.')
            return false;
        }

        var hdnIdUsuarioProcuracao = document.getElementById('hdnIdUsuarioProcuracao').value;

        $.ajax({
            dataType: 'xml',
            method: 'POST',
            url: '<?php echo $strLinkConsultaDadosUsuarioExterno?>',
            data: {
                'hdnIdUsuarioProcuracao': hdnIdUsuarioProcuracao
            },
            success: function (data) {
                var dados = [];
                dados.push(hdnIdUsuarioProcuracao);
                $('dados', data).children().each(function () {
                    var valor = $(this).context.innerHTML;
                    dados.push(valor);
                })

                objTabelaDinamicaUsuarioProcuracao.adicionar(dados);
                $("#tbUsuarioProcuracao").show();
                document.getElementById('txtNumeroCpfProcurador').value = '';
                document.getElementById('txtNomeProcurador').value = '';
                document.getElementById('btnAdicionarProcurador').style.display='none';
            }
        })

    }

    function verificaTabelaProcuracao(qtdLinha) {
        var tbUsuarioProcuracao = document.getElementById('tbUsuarioProcuracao');
        var ultimoRegistro = tbDocumento.rows.length == qtdLinha;
        if (ultimoRegistro) {
            tbUsuarioProcuracao.style.display = 'none';
        }
    }
</script>