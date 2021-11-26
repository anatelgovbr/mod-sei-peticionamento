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
            $strTitulo = 'Nova Procura��o Eletr�nica';
            $arrComandos[] = '<button type="button" onclick="peticionar()"  name="sbmPeticionar" id="sbmPeticionar" value="Peticionar" accesskey="P"  class="infraButton"><span class="infraTeclaAtalho">P</span>eticionar</button>';
            $arrComandos[] = '<button type="button" accesskey="C" id="btnCancelar" value="Cancelar" onclick="location.href=\''.PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinc_usu_ext_pe_listar&acao_origem='.$_GET['acao'])).'\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';
            $arrComandosInferior[] = '<button type="button" onclick="peticionar()"  name="sbmPeticionar" id="sbmPeticionarInferior" value="Peticionar" accesskey="P"  class="infraButton"><span class="infraTeclaAtalho">P</span>eticionar</button>';
            $arrComandosInferior[] = '<button type="button" accesskey="C" id="btnCancelarInferior" value="Cancelar" onclick="location.href=\''.PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinc_usu_ext_pe_listar&acao_origem='.$_GET['acao'])).'\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';
            break;
        default:
            throw new InfraException("A��o '" . $_GET['acao'] . "' n�o reconhecida.");
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
$usuarioDTO->retDblCpfContato();
$usuarioDTO->setNumIdUsuario($idUsuarioExterno);
$contatoExterno = $usuarioRN->consultarRN0489($usuarioDTO);

$idContatoExterno = $contatoExterno->getNumIdContato();
$cpfContato = $contatoExterno->getDblCpfContato();

//consultar org�o externo
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
    //Listando Todos os tipos de poderes
    $objMdPetTipoPoderLegalDTO = new MdPetTipoPoderLegalDTO();
    $objMdPetTipoPoderLegalDTO->retTodos(true);
    $objMdPetTipoPoderLegalDTO->setOrdStrNome(infraDTO::$TIPO_ORDENACAO_ASC);
    $objMdPetTipoPoderLegalDTO->setStrSinAtivo("S");
    $objMdPetTipoPoderLegalRN = new MdPetTipoPoderLegalRN();
    $arrObjMdPetTipoPoderLegalDTO = $objMdPetTipoPoderLegalRN->listar($objMdPetTipoPoderLegalDTO);

    $arrObjMdPetTipoPoderLegalDTONovo = array();

    foreach ($arrObjMdPetTipoPoderLegalDTO as $itemObjMdPetTipoPoderLegalDTO){
        if($itemObjMdPetTipoPoderLegalDTO->getNumIdTipoPoderLegal() == 1){
            array_unshift($arrObjMdPetTipoPoderLegalDTONovo, $itemObjMdPetTipoPoderLegalDTO);
        } else {
            array_push($arrObjMdPetTipoPoderLegalDTONovo, $itemObjMdPetTipoPoderLegalDTO);
        }
    }
    $arrObjMdPetTipoPoderLegalDTO = $arrObjMdPetTipoPoderLegalDTONovo;
    //Verificando a Existencia de Vinculo como Responsavel Legal
    //Respons�vel Legal
    $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
    $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();

    $objMdPetVincRepresentantDTO->retNumIdMdPetVinculo();
    $objMdPetVincRepresentantDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL);
    $objMdPetVincRepresentantDTO->setNumIdContato($idContatoExterno);
    $objMdPetVincRepresentantDTO->setStrSinAtivo('S');
    $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
    $existenciaVinculo = true;
    $arrObjMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);
    if(count($arrObjMdPetVincRepresentantDTO) > 0){
        $existenciaVinculo = false;  
    }
    //Verificando se o Usu�rio � Procurador Esp�cial em alguma PRocura��o
    $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
    $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();

    $objMdPetVincRepresentantDTO->setNumIdContato($idContatoExterno);
    $objMdPetVincRepresentantDTO->setStrStaEstado('A');
    $objMdPetVincRepresentantDTO->setStrSinAtivo('S');
    $objMdPetVincRepresentantDTO->setStrTipoRepresentante('E');
    $objMdPetVincRepresentantDTO->retNumIdContato();
    //Existencia Vinculo PRocura��o Especial
    $objMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);
    $existeEspecialVinculo = true;
    if(count($objMdPetVincRepresentantDTO) > 0){
        $existeEspecialVinculo = false;
    }

    //Verificar as parametriza��es de Vincula��o de pessoa F�sica
    $mdPetVincTpProcessoRN = new MdPetVincTpProcessoRN();
    $objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();
    $objMdPetVincTpProcessoDTO->setNumIdMdPetVincTpProcesso(MdPetVincTpProcessoRN::$ID_FIXO_MD_PET_VINCULO_USU_EXT);
    $objMdPetVincTpProcessoDTO->setStrSinAtivo('S');
    $objMdPetVincTpProcessoDTO->retTodos();
    $objMdPetVincTpProcessoDTO = $mdPetVincTpProcessoRN->consultar($objMdPetVincTpProcessoDTO);


    if(($existenciaVinculo == false || $existeEspecialVinculo == false) && $objMdPetVincTpProcessoDTO){
        $bloqueioRadio = "false";
    }else{
        $bloqueioRadio = "true";

    }

    $data = new DateTime();
    $data->add(new DateInterval('P1D'));
    $dataAtual = $data->format('d/m/Y');

    //Verificar as parametriza��es de Vincula��o de pessoa F�sica
    $mdPetVincTpProcessoRN = new MdPetVincTpProcessoRN();
    $objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();
    $objMdPetVincTpProcessoDTO->setNumIdMdPetVincTpProcesso(MdPetVincTpProcessoRN::$ID_FIXO_MD_PET_VINCULO_USU_EXT_PF);
    $objMdPetVincTpProcessoDTO->setStrSinAtivo('S');
    $objMdPetVincTpProcessoDTO->retTodos();
    $objMdPetVincTpProcessoDTO = $mdPetVincTpProcessoRN->consultar($objMdPetVincTpProcessoDTO);
    if($objMdPetVincTpProcessoDTO){
        $bloqueioRadioPF = false;
    } else {
        $bloqueioRadioPF = true;
    }


    //Existencia 

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
   

        <div class="bloco" style="padding-right:10px;">
            <label class="infraLabelObrigatorio" 
                   for="selTipoProcuracao">
                Tipo de Procura��o:
            </label><img align="top" style="height:16px; width:16px;" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" 
                 name="ajuda"
                 
                 <?= PaginaSEI::montarTitleTooltip('Para emitir Procura��o Eletr�nica Especial com todos os poderes previstos no Sistema, antes � necess�rio que voc� seja Respons�vel Legal de alguma Pessoa Jur�dica. \n \n Se for o caso e o tipo "Procura��o Eletr�nica Especial" n�o est� listado, acesse o menu "Respons�vel Legal de Pessoa Jur�dica" e, em seguida, o bot�o "Novo Respons�vel Legal" para realizar o cadastro.')?> 
                 class="infraImg"/>
            <br/>
            
            <select name="selTipoProcuracao" 
                    id="selTipoProcuracao"
                    onchange="pegaInfo(this);" 
                    tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>">
                <option value=""
                        selected="selected">
                        Selecione
                </option>
                <!-- Caso n�o exista vinculo do usu�rio externo como responsavel legal, n�o mostrar op��o Procura��o Especial  -->
                <?php if($existenciaVinculo == false){?>
                <option value="<?php echo MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL?>"
                        >
                    Procura��o Eletronica Especial
                </option>
                <?php }?>
                <option value="<?php echo MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES?>"
                        >
                    Procura��o Eletronica Simples
                </option>
            </select>
           
        </div>
        
        <!-- Outorgante PF/PJ  -->
        <div id="PFoutorgante">
        <div id="hiddenOutorgante" style="display:none;" >
       
         <label for="lblOutorgante" style="padding-left:6px;" 
                   class="infraLabelObrigatorio">
                Outorgante: <img align="top" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" 
                 name="ajuda"
                style="width:16px;height:16px;"
                 <?= PaginaSEI::montarTitleTooltip('A op��o "Pessoa Jur�dica" estar� habilitada somente se voc� for o Respons�vel Legal com situa��o Ativa ou quando possuir Procura��o Eletr�nica Especial vigente de alguma Pessoa Jur�dica.')?>
                 class="infraImg"/>
        </label>
      
        <!-- Outorgante PF/PJ  - FIM -->


                 <label class="infraLabelObrigatorio" style="padding-left:170px;"
                   for="selPessoaJuridica" id="lvbPJProSimples">
                Pessoa Jur�dica Outorgante:
                <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" 
                align="top"
                style="width:16px;height:16px;"
                 name="ajuda"
                 id="imgPj"
                 <?= PaginaSEI::montarTitleTooltip('Neste campo s�o listadas as Pessoas Jur�dicas que voc� � o Respons�vel Legal com situa��o Ativa ou que possua Procura��o Eletr�nica Especial vigente. \n \n Se voc� � o Responsavel Legal e a Pessoa Jur�dica n�o foi listada, ent�o confira se a vincula��o est� cadastrada no menu "Respons�vel Legal de Pessoa Jur�dica": \n \n 1.1. Se a Pessoa Jur�dica n�o for listada no citado menu, clique no bot�o "Novo Respons�vel Legal" e realize o cadastro. \n 1.2. Se a Pessoa Jur�dica foi listada no citado menu, mas n�o est� com situa��o Ativa, regularize a situa��o juntamente ao �rg�o.')?>
                 class="infraImg"/>
            </label>
          
        <br>

        <input type="radio" onchange="showPessoaOutorganteHidden();" name="Outorgante"
        id="rbOutorgante1" <?php echo $bloqueioRadioPF ? "disabled='disabled' style='display:none'" : ""; ?>checked="checked"
        value="PF">

          <label  <?php echo $bloqueioRadioPF ? "style='display:none'" : ""; ?> for="rbOutorgante1" id="lvbFisica" class="infraLabelRadio">Pessoa F�sica </label><img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif"
                 name="ajuda"
                 style="width:16px;height:16px; <?php echo $bloqueioRadioPF ? "display:none" : ""; ?>"
                 <?= PaginaSEI::montarTitleTooltip('Ao selecionar a op��o Pessoa F�sica, a Procura��o Eletr�nica Simples ter� como objetivo definir algu�m para representar voc�, enquanto Pessoa F�sica. \n \n Ou seja, ser� uma Procura��o de Usu�rio Externo para Usu�rio Externo.')?>
                 class="infraImg"/>
       <input type="radio" onchange="showPessoaOutorgante();" name="Outorgante"
        id="rbOutorgante2"
        value="PJ">

         <label for="rbOutorgante2" id="lvbJuridica" class="infraLabelRadio">Pessoa Jur�dica </label><img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" 
                 name="ajuda"
                 style="width:16px;height:16px;margin-right:17px;"
                 <?= PaginaSEI::montarTitleTooltip('Ao selecionar a op��o Pessoa Jur�dica, a Procura��o Eletr�nica ter� como objetivo definir algu�m para representar a Pessoa Jur�dica que voc� j� representa como Respons�vel Legal ou como Procurador Especial.')?>
                 class="infraImg" id="ajudaPJ"/>

                    <select style="width: 370px;"
                            name="selPessoaJuridicaProcSimples"
                            id="selPessoaJuridicaProcSimples" 
                            tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
                        <?php echo $selectPjOutorgante; ?>
                    </select>    

    </div>
    
        </div>
 
       <!-- Outorgante - fIM -->
       <div id="procuracaoEspecial">
        
            <label class="infraLabelObrigatorio"
                   for="selPessoaJuridica">
                Pessoa Jur�dica Outorgante:
            </label><img align="top" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" 
                 name="ajuda"
                 <?= PaginaSEI::montarTitleTooltip('Neste campo s�o listadas as Pessoas Jur�dicas que voc� � o Respons�vel Legal com situa��o Ativa, pois somente o Respons�vel Legal de Pessoa Jur�dica pode emitir Procura��o Eletr�nica Especial. \n \n Se voc� � o Responsavel Legal e a Pessoa Jur�dica n�o foi listada, ent�o confira se a vincula��o est� cadastrada no menu "Respons�vel Legal de Pessoa Jur�dica": \n \n 1.1. Se a Pessoa Jur�dica n�o for listada no citado menu, clique no bot�o "Novo Respons�vel Legal" e realize o cadastro. \n 1.2. Se a Pessoa Jur�dica foi listada no citado menu, mas n�o est� com situa��o Ativa, regularize a situa��o juntamente ao �rg�o.')?>
                 class="infraImg"/>
                 
            <br/>
            <select class="infraSelect" style="width:30%;"
                    name="selPessoaJuridica"
                    style="padding-left:60px;" 
                    id="selPessoaJuridica" 
                    tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
                <?php echo $selectPjOutorgante; ?>
            </select>
        
        
        </div>
       
        <div id="txtExplicativo"></div>            
        <div id="procuracaoEspecialTable">
        <div class="bloco">
            <label for="txtCpf" 
                   class="infraLabelObrigatorio">
                CPF do Usu�rio Externo:
            </label>
            <img align="top" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" 
                 name="ajuda"
                 <?= PaginaSEI::montarTitleTooltip('A pesquisa � realizada somente sobre Usu�rios Externos liberados.  \n \n A consulta somente pode ser efetuada pelo CPF do Usu�rio Externo.')?>
                 class="infraImg"/><br/>

            <input name="txtNumeroCpfProcurador" 
                   id="txtNumeroCpfProcurador"
                   maxlength="14"
                   type="text"
                   style="width:138px;" 
                   class="infraText campoPadrao"
                   tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"
                   onkeypress="return infraMascaraCPF(this);" 
                   onkeyup="return infraMascaraCPF(this);" 
                   onkeydown="return infraMascaraCPF(this);"/>

            <button type="button" 
                    accesskey="l" 
                    name="btnValidar" 
                    id="btnValidarEspecial"
                    disabled
                    class="infraButton btnProc" 
                    onclick="consultarUsuarioExternoValido();"
                    tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
                Va<span class="infraTeclaAtalho">l</span>idar
            </button>
        </div>

        <div class="bloco" style="width: 30%">
            <label for="txtNomeProcurador" class="infraLabelObrigatorio">Nome do Usu�rio Externo: <img align="top" style="height:16px; width:16px;" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('Caso seja listado mais de um cadastro de Usu�rio Externo utilizando o mesmo CPF, escolha neste campo o nome do cadastro correto.')?> class="infraImg"/></label>
            <br/>
            <!-- Combo Usu�rio Externo -->
            <select name="selUsuario" style="width:250px;" onchange="alterarHidden(this);"
                    id="selUsuario"
                    onchange="" 
                    tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>">
               
            </select>

            <button type="button" 
                    accesskey="o" 
                    class="infraButton btnProc" 
                    id="btnAdicionarProcurador"
                    onclick="criarRegistroTabelaProcuracao();"
                    tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
                Adici<span class="infraTeclaAtalho">o</span>nar
            </button>
        </div>

        <div>
            <table width="99%" class="infraTable" summary="Procura��es" id="tbUsuarioProcuracao" style="display:none;">
                <caption class="infraCaption">&nbsp;</caption>
                <tr>
                    <th class="infraTh" width="0" style="display:none;">ID</th>
                    <th class="infraTh" width="140px">CPF</th>
                    <th class="infraTh" width="0">Usu�rio Externo</th>
                    <th class="infraTh" width="60px">A��es</th>
                </tr>
            </table>
        </div>
    </div>
    </div>

    <div id="procuracaoSimplesFieldSet">
    <fieldset id="fldResposta" style="height:100%;">
            <legend class="infraLegend"> Dados da Procura��o </legend>
                   
            <div class="bloco">
            <label for="txtCpf" 
                   class="infraLabelObrigatorio">
                CPF do Usu�rio Externo:  <img align="top"  src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" 
                 name="ajuda"
                 <?= PaginaSEI::montarTitleTooltip('A pesquisa � realizada somente sobre Usu�rios Externos liberados.  \n \n A consulta somente pode ser efetuada pelo CPF do Usu�rio Externo.')?>
                 class="infraImg"/>
            </label>
          <br/>

            <input name="txtNumeroCpfProcuradorSimples" 
                   id="txtNumeroCpfProcuradorSimples"
                   maxlength="14"
                   style="width:138px;"
                   type="text" 
                   class="infraText campoPadrao"
                   tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>"
                   onkeypress="return infraMascaraCPF(this);" 
                   onkeyup="return infraMascaraCPF(this);" 
                   onkeydown="return infraMascaraCPF(this);"/>

            <button type="button" 
                    accesskey="V" 
                    name="btnValidar" 
                    id="btnValidarSimples"
                    disabled
                    class="infraButton btnProc" 
                    onclick="consultarUsuarioExternoValidoSimples();"
                    tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados(); ?>">
                <span class="infraTeclaAtalho">V</span>alidar
            </button>
    

        </div>

         <div class="bloco">
            <label for="txtNomeProcurador" class="infraLabelObrigatorio">Nome do Usu�rio Externo: <img align="top" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('Caso seja listado mais de um cadastro de Usu�rio Externo utilizando o mesmo CPF, escolha neste campo o nome do cadastro correto.')?> class="infraImg"/></label>
            <br/>
            <!-- Combo Usu�rio Externo -->
            <select name="selUsuarioSimples" style="width:250px;" onchange="alterarHidden(this);"
                    id="selUsuarioSimples"
                    onchange="" 
                    tabindex="<?=PaginaSEIExterna::getInstance()->getProxTabDados()?>">
            </select>
            <br>
            
        </div>
    <br><br><br><br><br>
    <div class="bloco">
         <!-- Combo Tipo de Poderes -->
         <label for="lblTipoPoder" class="infraLabelObrigatorio">Poderes: <img align="top" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('Escolha os Poderes sobre os quais o Procurador representar� o Outorgante. \n \n Somente se for concedido o poder para "Receber, Cumprir e Responder Intima��o Eletr�nica" (destacado abaixo) o Procurador receber� Intima��es Eletr�nicas destinadas ao Outorgante e participar� de todo o fluxo subsequente a respeito da intima��o recebida.')?> class="infraImg"/></label>
        <br>
        <!-- Alterar para multiplo -->
        <div id="listaPoderes">
        <select onchange="" style="width:460px;"   id="selTpPoder" name="selTpPoder[]" class="infraSelect multipleSelect" >
                <?php
                    foreach ($arrObjMdPetTipoPoderLegalDTO as $key => $value) {
                        if($value->getNumIdTipoPoderLegal() == 1){
                            echo '<option value="' . $value->getNumIdTipoPoderLegal() . '">*' . $value->getStrNome() . '</option>';
                        }else{
                            echo '<option value="' . $value->getNumIdTipoPoderLegal() . '">' . $value->getStrNome() . '</option>';
                        }
                    }
                ?>
        </select>
        </div>
        <br><br>
        <label for="lblValidade" 
                   class="infraLabelObrigatorio">
                Validade:
        </label>
        <label id="lblDt" for="lvlDt" style="padding-left:190px;display:none;" class="infraLabelObrigatorio">Data Limite:</label> <br>
        <!-- Radio Validade  -->
        <input type="radio" onchange="showDataNot();" name="Validade"
        id="rbValidade"
        value="1">
        
        <label  id="lvbIndeterminado" name="lvbIndeterminado" for="rbValidade" class="infraLabelRadio">Indeterminado </label> <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" 
                 name="ajuda"
                 style="width:16px;height:16px;"
                 <?= PaginaSEI::montarTitleTooltip('Ao selecionar esta op��o, o Procurador representar� o Outorgante por prazo indeterminado. \n \n Contudo, a qualquer momento o Outorgante poder� Revogar a Procura��o ou o pr�prio Outorgado poder� Renunciar a Procura��o.')?>
                 class="infraImg"/>

        <input type="radio" onchange="showData();" name="Validade" id="rbValidade2" value="2"">

        <label for="rbValidade2" id="lblUsuExterno" class="infraLabelRadio">Determinado  </label> <img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" 
                 name="ajuda"
                 style="width:16px;height:16px;"
                 <?= PaginaSEI::montarTitleTooltip('Ao selecionar esta op��o, o Procurador representar� o Outorgante at� a Data Limite indicada no campo ao lado. \n \n Contudo, a qualquer momento o Outorgante poder� Revogar a Procura��o ou o pr�prio Outorgado poder� Renunciar a Procura��o.')?>
                 class="infraImg"/>

                <!-- Data - FIM  -->
                
                
                <input type="text" name="txtDt" id="txtDt"  style="display:none;margin-left:10px;width:80px;" 
                value="" 
                onkeypress="return infraMascara(this, event, '##/##/####');" class="infraText" />
                <img src="<?=PaginaSEI::getInstance()->getDiretorioImagensGlobal()?>/calendario.gif" id="imgDt" 
                    title="Selecionar Data" style="display:none;"
                    alt="Selecionar Data" class="infraImg" 
                    onclick="infraCalendario('txtDt',this,false,'<?=InfraData::getStrDataAtual().' 00:00'?>');" />
                   
       <!-- Radio Validade - FIM  -->


<br><br>
         <!-- Abrang�ncia  -->
        <label for="lblAbrangencia" 
                   class="infraLabelObrigatorio">
                Abrang�ncia:
        </label>
        <br>
        <!-- Radio Qualquer  -->
        <input type="radio" name="abrangencia"
        id="rbAbrangencia1"
        onchange="showTable1()";
        value="Q">

        <label for="rbAbrangencia1" id="lvbIndeterminado" class="infraLabelRadio">Qualquer Processo em Nome do Outorgante </label><img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" 
                 name="ajuda"
                 style="width:16px;height:16px;"
                 <?= PaginaSEI::montarTitleTooltip('Ao selecionar esta op��o, o Procurador representar� o Outorgante em qualquer Processo.')?> 
                 class="infraImg"/>
        <br>
          <!-- Especifico  -->          
        <input type="radio" name="abrangencia"
        id="rbAbrangencia"
        onchange="showTable2()";
        value="E">

        <label for="rbAbrangencia" id="lvbIndeterminado" class="infraLabelRadio">Processos Espec�ficos </label><img src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" 
                 name="ajuda"
                 style="width:16px;height:16px;"
                 <?= PaginaSEI::montarTitleTooltip('Ao selecionar esta op��o, o Procurador representar� o Outorgante somente em processos espec�ficos, que devem ser adionados na lista abaixo. \n \n Ap�s marcar esta op��o, torna-se necess�rio informar o n�mero v�lido de cada processo sobre os quais o Procurador poder� atuar.')?> 
                 class="infraImg"/>
    
        <!-- INICIO NUMERO DO PROCESSO -->
        <div id="procDados" style="display:none;">
        <div id="espec" style="padding:15px;">
        <label id="lblNumeroSei" for="txtNumeroProcesso" accesskey="n" class="infraLabelObrigatorio"><span class="infraTeclaAtalho">N</span>�mero do Processo:</label><br>
        <input type="text" id="txtNumeroProcesso" name="txtNumeroProcesso" class="infraText" maxlength="100" style="width: 200px;" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" value="<?= PaginaSEI::tratarHTML($txtNumeroProcesso) ?>"/>
        <button tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" accesskey="a"  type="button" id="btnValidarProcesso"  onclick="validarNumeroProcesso()" disabled class="infraButton">V<span class="infraTeclaAtalho">a</span>lidar</button>

         <input type="text" id="txtTipo"  name="txtTipo"  class="infraText" readonly="readonly" style="width: 400px;" value="<?= PaginaSEI::tratarHTML($txtTipo) ?>" disabled/>
        <button type="button" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" accesskey="i" style="display:none;" onclick="adicionarProcesso();" disabled  id="btnAdicionar" class="infraButton">Ad<span class="infraTeclaAtalho">i</span>cionar</button>
        </div>
		</div>
        
 <!-- TABELA DE PROCESSOS -->
        <div>
		<table width="99%" class="infraTable" summary="Processos" id="tbProcessos" style="display:none;">
                <caption class="infraCaption">&nbsp;</caption>
                <tr>
                    <th class="infraTh" width="0" style="display:none;">ID</th>
                    <th class="infraTh" width="220px">Processo</th>
                    <th class="infraTh" width="0">Tipo de Processo</th>
                    <th class="infraTh" width="60px">A��es</th>
                </tr>
            </table>
        </div>
    <!-- FIM NUMERO DO PROCESSO -->

    <!-- INICIO TIPO DO PROCESSO VALIDADO -->
   
    </div>

                </fieldset>
    
        <!-- Hidden dos Campos de Procura��o Simples -->
        <input type="hidden" name="hdnIdProcedimento" id="hdnIdProcedimento"/>
        <input type="hidden" name="hdnTbProcessos" id="hdnTbProcessos"/>
        <input type="hidden" name="hdnTbNumeroProc" id="hdnTbNumeroProc"/>
        <input type="hidden" name="hdnRbValidate" id="hdnRbValidate"/>
        <input type="hidden" name="hdnCpf" id="hdnCpf"/>
        <input type="hidden" name="hdnExiteProc" id="hdnExiteProc"/>
        <input type="hidden" name="hdnRbAbrangencia" id="hdnRbAbrangencia"/>
        <input type="hidden" name="hdnRbOutorgante" id="hdnRbOutorgante"/>
        <input type="hidden" name="hdnTpPoderes" id="hdnTpPoderes"/> 
        <input type="hidden" name="hdnBloqueioRadio" id="hdnBloqueioRadio" value="<?php echo $bloqueioRadio; ?>"/>
        <input type="hidden" name="hdnDtAtual" id="hdnDtAtual" value="<?php echo $dataAtual; ?>"/>
        <!-- Hidden dos Campos de Procura��o Simples - FIM -->
       

    <input type="hidden" name="hdnCPF" id="hdnCPF"/>
    <input type="hidden" name="hdnIdUsuarioProcuracao" id="hdnIdUsuarioProcuracao"/>
    <input type="hidden" name="hdnIdUsuario" id="hdnIdUsuario"/>
    
    <input type="hidden" name="hdnTbUsuarioProcuracao" id="hdnTbUsuarioProcuracao"/>
    <input type="hidden" name=hdnIdContExterno" id="hdnIdContExterno" value="<?=$idContatoExterno?>"/>
    <input type="hidden" name=hdnCpfContExterno" id="hdnCpfContExterno" value="<?= InfraUtil::formatarCpf($cpfContato)?>"/>
  
    <br/>
<? PaginaSEIExterna::getInstance()->fecharAreaDados();?>
</form>
 <?
require_once  'md_pet_procuracao_especial_cadastro_js.php'; 
//PaginaSEIExterna::getInstance()->montarAreaDebug();
PaginaSEIExterna::getInstance()->montarBarraComandosInferior($arrComandosInferior);
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();