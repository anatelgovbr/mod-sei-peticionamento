<?php
/**
 * ANATEL
 *
 * 30/04/2018 - criado por renato.monteiro@castgroup.com.br - CAST
 *
 */
require_once dirname(__FILE__) . '/../../SEI.php';
SessaoSEIExterna::getInstance()->validarLink();
SessaoSEIExterna::getInstance()->validarPermissao($_GET['acao']);
PaginaSEIExterna::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);

$arrComandos = array();
$texto = '';
$idAcessoExterno = $_GET['id_acesso_externo'];
$idDocumento = $_GET['id_documento'];

switch ($_GET['acao']) {

    case 'md_pet_intimacao_usu_ext_negar_cumprir':
        try {
            
            $strTitulo = 'Cumprir Intima��o Eletr�nica n�o Permitida';
            $arrComandos[] = '<button type="button" accesskey="C" name="sbmFechar" id="sbmFechar"  onclick="window.close();" value="Fechar" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

            $arrUsuarioSituacao = (new MdPetIntRelDestinatarioRN())->getSituacaoUsuarioIntimacao($idDocumento, $idAcessoExterno);
            $arrImpedimentos = array_merge(array_unique($arrUsuarioSituacao['int_impedido']), array_unique($arrUsuarioSituacao['int_incapaz']));

            if(is_array($arrImpedimentos) && count($arrImpedimentos) > 0){
                
                $texto = 'Voc� n�o possui mais permiss�o para cumprir a Intima��o Eletr�nica conforme abaixo:<br><br>Destinat�rios n�o permitidos:<br>';
                foreach($arrImpedimentos as $impedimento){
                    $texto .= '&nbsp;&nbsp;&nbsp;&nbsp;- '. $impedimento['nomeDestinatario'] . ' (' . InfraUtil::formatarCpfCnpj($impedimento['cpfCnpjDestinatario']) . '), verifique seus Poderes de Representa��o.<br>';
                }
            
            }

        } catch (Exception $e) {
            PaginaSEIExterna::getInstance()->processarExcecao($e);
        }

        break;
    default:
        throw new InfraException("A��o '" . $_GET['acao'] . "' n�o reconhecida.");
}

PaginaSEIExterna::getInstance()->montarDocType();
PaginaSEIExterna::getInstance()->abrirHtml();
PaginaSEIExterna::getInstance()->abrirHead();
PaginaSEIExterna::getInstance()->montarMeta();

PaginaSEIExterna::getInstance()->montarTitle(':: ' . PaginaSEIExterna::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');

PaginaSEIExterna::getInstance()->montarStyle();
PaginaSEIExterna::getInstance()->abrirStyle();
?>
p{ font-size: 0.875rem; }
<?php
PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
PaginaSEIExterna::getInstance()->abrirJavaScript();
PaginaSEIExterna::getInstance()->fecharJavaScript();
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody();
?>
<form action="<?php echo SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_intimacao_usu_ext_confirmar_aceite&id_procedimento=' . $_GET['id_procedimento'] . '&id_acesso_externo=' . $_GET['id_acesso_externo'] . '&id_documento=' . $_GET['id_documento']); ?>" method="post" id="frmMdPetIntimacaoConfirmarAceite" name="frmMdPetIntimacaoConfirmarAceite">

    <div class="row">
        <div class="col-12">
            <h4 class="mt-4"><?= $strTitulo ?></h4>
        </div>
        <div class="col-12">
            <p><?= $texto ?></p>
        </div>
    </div>

</form>
<?php
SessaoSEIExterna::getInstance()->configurarAcessoExterno($_GET['id_acesso_externo']);
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
SessaoSEIExterna::getInstance()->configurarAcessoExterno(0);
?>