<?php
/**
 * Created by PhpStorm.
 * User: jhon.carvalho
 * Date: 05/03/2018
 * Time: 10:16
 */
try {
    require_once dirname(__FILE__) . '/../../SEI.php';
    session_start();
    switch ($_GET['acao']) {
        case 'md_pet_vinc_usu_ext_pe_cadastrar':
            $strTitulo = 'Nova Procuração Eletrônica';
            $arrComandos[] = '<button type="button" onclick="peticionar()"  name="sbmPeticionar" id="sbmPeticionar" value="Peticionar" accesskey="P"  class="infraButton"><span class="infraTeclaAtalho">P</span>eticionar</button>';
            $arrComandos[] = '<button type="button" accesskey="C" id="btnCancelar" value="Cancelar" onclick="location.href=\''.PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinc_usu_ext_pe_listar&acao_origem='.$_GET['acao'])).'\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';
            break;
        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }


} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}
$urlDoc1 = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=documento_vinculacao&tipo=1');
$urlDoc2 = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=documento_vinculacao&tipo=2');
$urlDoc3 = SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=documento_vinculacao&tipo=3');
$selectPjOutorgante = MdPetVincRepresentantINT::montarSelectOutorgante(null,null,null);

$idUsuarioExterno = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();
$usuarioDTO = new UsuarioDTO();
$usuarioRN = new UsuarioRN();
$usuarioDTO->retNumIdContato();
$usuarioDTO->setNumIdUsuario($idUsuarioExterno);
$contatoExterno = $usuarioRN->consultarRN0489($usuarioDTO);

$idContatoExterno = $contatoExterno->getNumIdContato();

//consultar orgão externo
$siglaOrgao = SessaoSEIExterna::getInstance()->getStrSiglaOrgaoUsuarioExterno();

if(isset($_POST['hdnIdUsuario'])&& $_POST['hdnIdUsuario']!=''){
    $dados= $_POST;

    $idsUsuarios=$_POST['hdnIdUsuario'];
    $id = explode('+',$idsUsuarios);

    $idContatoVinc = $_POST['selPessoaJuridica'];
    $dados['idContato']= $idContatoVinc;
    $dados['chkDeclaracao'] = 'S';
    $dados['idContatoExterno']= $idContatoExterno;

    $mdPetVinUsuExtProcRN = new MdPetVinUsuExtProcRN();
    $mdPetVinUsuExtProcRN->gerarProcedimentoVinculoProcuracao($dados);
    header('Location: '.SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinc_usu_ext_pe_listar&acao_origem='.$_GET['acao']));
    die;
}

PaginaSEIExterna::getInstance()->montarDocType();
PaginaSEIExterna::getInstance()->abrirHtml();
PaginaSEIExterna::getInstance()->abrirHead();
PaginaSEIExterna::getInstance()->montarMeta();
PaginaSEIExterna::getInstance()->montarTitle(':: ' . PaginaSEIExterna::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaSEIExterna::getInstance()->montarStyle();
PaginaSEIExterna::getInstance()->abrirStyle();
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
<form id="frmPeticionarProcesso" 
      method="post"
      action="<?=PaginaSEIExterna::getInstance()
                    ->formatarXHTML(SessaoSEIExterna::getInstance()
                    ->assinarLink('controlador_externo.php?acao='.$_GET['acao'].
                                  '&acao_origem='.$_GET['acao']))?>">
<?
PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
PaginaSEIExterna::getInstance()->abrirAreaDados('auto');
require_once  'md_pet_procuracao_especial_cadastro_css.php'; 
?>
    <div class="container" >
        <div class="bloco">
            <label class="infraLabelObrigatorio" for="selTipoProcuracao">Tipo de Procuração:</label>
            <br/>
            <select name="selTipoProcuracao" id="selTipoProcuracao" onchange="pegaInfo(this)" tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>">
				<option value="<?php echo MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL?>"selected="selected">
					Procuração Eletronica Especial
                </option>
		<!-- <option value="<?php echo MdPetVincRepresentantRN::$PE_PROCURADOR?>">
                    Procuração Eletronica
                </option>
                <option value="<?php echo MdPetVincRepresentantRN::$PE_PROCURADOR_SUBSTALECIDO?>">
                    Procuração de Substabelecimento
                </option>-->
            </select>
        </div>

        <div class="bloco">
            <label class="infraLabelObrigatorio" for="selPessoaJuridica">Pessoa Jurídica Outorgante:</label>
            <br/>
            <select class="infraSelect" name="selPessoaJuridica" id="selPessoaJuridica" tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
				<?php echo $selectPjOutorgante; ?>
            </select>
        </div>

        <div id="txtExplicativo"></div>            

        <div class="bloco">
            <label for="txtCpf" class="infraLabelObrigatorio">CPF do Usuário Externo: <img align="top" style="height:16px; width:16px;" id="imgAjuda" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('A pesquisa é realizada somente sobre Usuários Externos liberados. \n \n A pesquisa é efetuada pelo CPF do Usuário Externo.')?> alt="Ajuda" class="infraImg"/></label>
            <br/>
            <input name="txtNumeroCpfProcurador" id="txtNumeroCpfProcurador" maxlength="14" type="text" class="infraText campoPadrao" tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>" onkeypress="return infraMascaraCPF(this);" onkeyup="return infraMascaraCPF(this);" onkeydown="return infraMascaraCPF(this);" onchange="validaCpf(this)"/>
            <button type="button" accesskey="V" name="btnValidar" id="btnValidar" class="infraButton btnProc" onclick="consultarUsuarioExternoValido();" tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"> <span class="infraTeclaAtalho">V</span>alidar</button>
        </div>

        <div class="bloco">
            <label for="txtNomeProcurador" class="infraLabelObrigatorio">Nome do Usuário Externo:</label>
            <br/>
            <input name="txtNomeProcurador" id="txtNomeProcurador" type="text" class="infraText campoPadrao" disabled="disabled"/>
            <button type="button" accesskey="A" class="infraButton btnProc" id="btnAdicionarProcurador" onclick="criarRegistroTabelaProcuracao();" tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"> <span class="infraTeclaAtalho">A</span>dicionar</button>
        </div>

        <div>
            <table width="99%" class="infraTable" summary="Procurações" id="tbUsuarioProcuracao" style="display:none;">
                <caption class="infraCaption">&nbsp;</caption>
                <tr>
                    <th class="infraTh" width="0">CPF</th>
                    <th class="infraTh" width="0">Usuário Externo</th>
                    <th class="infraTh" width="0">Ações</th>
                </tr>
            </table>
        </div>
    </div>

    <input type="hidden" name="hdnCPF" id="hdnCPF"/>
    <input type="hidden" name="hdnIdUsuarioProcuracao" id="hdnIdUsuarioProcuracao"/>
    <input type="hidden" name="hdnIdUsuario" id="hdnIdUsuario"/>
    
    <input type="hidden" name="hdnTbUsuarioProcuracao" id="hdnTbUsuarioProcuracao"/>
    <input type="hidden" name=hdnIdContExterno" id="hdnIdContExterno" value="<?=$idContatoExterno?>"/>
    <br/>
<? PaginaSEIExterna::getInstance()->fecharAreaDados();?>
</form>
 <?
require_once  'md_pet_procuracao_especial_cadastro_js.php'; 
//PaginaSEIExterna::getInstance()->montarAreaDebug();
PaginaSEIExterna::getInstance()->montarBarraComandosInferior($arrComandos);
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();