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

$arrComandos = [];
$impedidos = [];
$texto = '';
$idAcessoExterno = $_GET['id_acesso_externo'];
$idProtocolo = $_GET['id_protocolo'];

switch ($_GET['acao']) {

    case 'md_pet_intimacao_usu_ext_negar_resposta':
        
        try {

            $strTitulo          = 'Responder Intimação Eletrônica não Permitida';
            $arrComandos[]      = '<button type="button" accesskey="C" name="sbmFechar" id="sbmFechar"  onclick="window.close();" value="Fechar" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
            
            $arrUsuarioSituacao = (new MdPetIntRelDestinatarioRN())->getSituacaoUsuarioIntimacao($idProtocolo, $idAcessoExterno);
            $arrImpedimentos    = $arrUsuarioSituacao['int_impedido'];

            if(is_array($arrImpedimentos) && count($arrImpedimentos) > 0){
                
                $texto = 'Você não possui mais permissão para responder a Intimação Eletrônica conforme abaixo:<br><br>Destinatários não permitidos:<br>';
                foreach($arrImpedimentos as $impedimento){
                    if(!in_array($impedimento['cpfCnpjDestinatario'], $impedidos)){
                        $texto .= '&nbsp;&nbsp;&nbsp;&nbsp;- '. $impedimento['nomeDestinatario'] . ' (' . InfraUtil::formatarCpfCnpj($impedimento['cpfCnpjDestinatario']) . '), verifique seus Poderes de Representação.<br>';
                        $impedidos[] = $impedimento['cpfCnpjDestinatario'];
                    }
                }
            
            }
 
        } catch (Exception $e) {

            PaginaSEIExterna::getInstance()->processarExcecao($e);

        }

        break;
    default:
        throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
}

PaginaSEIExterna::getInstance()->montarDocType();
PaginaSEIExterna::getInstance()->abrirHtml();
PaginaSEIExterna::getInstance()->abrirHead();
PaginaSEIExterna::getInstance()->montarMeta();

PaginaSEIExterna::getInstance()->montarTitle(':: ' . PaginaSEIExterna::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');

PaginaSEIExterna::getInstance()->montarStyle();
PaginaSEIExterna::getInstance()->abrirStyle();
echo 'p { font-size: 0.875rem; margin-bottom: 1rem: }';
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
            <div>
                <p><?= $texto ?></p>
            </div>
        </div>
    </div>

</form>
<?php
SessaoSEIExterna::getInstance()->configurarAcessoExterno($_GET['id_acesso_externo']);
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
SessaoSEIExterna::getInstance()->configurarAcessoExterno(0);
?>