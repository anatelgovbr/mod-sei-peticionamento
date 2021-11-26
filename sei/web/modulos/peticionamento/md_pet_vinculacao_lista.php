<?php
/**
 * Created by PhpStorm.
 * User: jhon.carvalho
 * Date: 08/02/2018
 * Time: 11:16
 */
try {
  require_once dirname(__FILE__) . '/../../SEI.php';

  session_start();
  $objMdPetVincUsuExtPj = null;

  switch ($_GET['acao']) {

    case 'md_pet_vinculacao_listar':
        $strTitulo = 'Respons�vel Legal de Pessoa Jur�dica';
        $objMdPetVincTpProcessoRN  = new MdPetVincTpProcessoRN();
        $objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();
        $objMdPetVincTpProcessoDTO->retNumIdTipoProcedimento();
        $objMdPetVincTpProcessoDTO->retStrSinNaUsuarioExterno();
        $objMdPetVincTpProcessoDTO->retStrSinNaPadrao();
        $objMdPetVincTpProcessoDTO->retStrStaNivelAcesso();
        $objMdPetVincTpProcessoDTO->retNumIdHipoteseLegal();
        $objMdPetVincTpProcessoDTO->retStrOrientacoes();
        $objMdPetVincTpProcessoDTO->setNumMaxRegistrosRetorno(1);
        $objMdPetVincUsuExtPj = $objMdPetVincTpProcessoRN->consultar($objMdPetVincTpProcessoDTO);

      break;
  }

  $arrComandos = array();
  
  $arrComandos[] = '<button type="submit" accesskey="p" id="btnPesquisar" value="Pesquisar" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';
  if(is_null($objMdPetVincUsuExtPj)) {
      $jsAlerta = 'alert(\'Tipo de Processo para Vincula��o de Usu�rio Externo n�o parametrizado. Contate o Administrador!\');';
      $arrComandos[] = '<button type="button" accesskey="N" id="btnNovoResponsavelLegal" value="NovoResponsavelLegal" onclick="'.$jsAlerta. '" class="infraButton"><span class="infraTeclaAtalho">N</span>ovo Respons�vel Legal</button>';
  }else{
      $arrComandos[] = '<button type="button" accesskey="N" id="btnNovoResponsavelLegal" value="NovoResponsavelLegal" onclick="location.href=\'' . SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinc_usu_ext_cadastrar&acao_origem=' . $_GET['acao']) . '\';" class="infraButton"><span class="infraTeclaAtalho">N</span>ovo Respons�vel Legal</button>';
  }
  $arrComandos[] = '<button type="button" accesskey="c" name="btnFechar" onclick="window.history.back();" class="infraButton" >Fe<span class="infraTeclaAtalho">c</span>har</button>';
  

} catch (Exception $e) {
  PaginaSEIExterna::getInstance()->processarExcecao($e);
}


$idUsuarioExterno = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();

$usuarioRN = new UsuarioRN();
$usuarioDTO = new UsuarioDTO();

$usuarioDTO->retNumIdContato();
$usuarioDTO->setNumIdUsuario($idUsuarioExterno);
$arrUsuarioRepresentante = $usuarioRN->consultarRN0489($usuarioDTO);

$idContatoRepresentante = $arrUsuarioRepresentante->getNumIdContato();


$objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();

$objMdPetVincRepresentantProcEspecDTO = new MdPetVincRepresentantDTO();
$objMdPetVincRepresentantProcEspecDTO->retNumIdMdPetVinculo();
$objMdPetVincRepresentantProcEspecDTO->setNumIdContato($idContatoRepresentante);
$objMdPetVincRepresentantProcEspecDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL);
$objMdPetVincRepresentantProcEspecDTO->setStrSinAtivo('S');
$objMdPetVincRepresentantProcEspecDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);

$arrObjMdPetVincRepresentantProcEspecDTO = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantProcEspecDTO);


$objMdPetVinculoRN = new MdPetVinculoRN();
$objMdPetVinculoDTO = new MdPetVinculoDTO();

$objMdPetVinculoDTO->retNumIdMdPetVinculo();
$objMdPetVinculoDTO->retStrNomeTipoProcedimento();
$objMdPetVinculoDTO->retDblIdProtocolo();
$objMdPetVinculoDTO->retStrProtocoloFormatado();
$objMdPetVinculoDTO->retDblCNPJ();
$objMdPetVinculoDTO->retStrRazaoSocialNomeVinc();
$objMdPetVinculoDTO->retStrTipoRepresentante();
$objMdPetVinculoDTO->retStrNomeContatoRepresentante();
$objMdPetVinculoDTO->retNumIdMdPetVinculoRepresent();
$objMdPetVinculoDTO->retStrStaEstado();
$objMdPetVinculoDTO->setDistinct(true);
$objMdPetVinculoDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL);
$objMdPetVinculoDTO->setStrStaResponsavelLegal('S');

$objMdPetVinculoDTO->adicionarCriterio(
        array('IdContatoRepresentante'),
        array(InfraDTO::$OPER_IGUAL),
        array($idContatoRepresentante),
        null,
        'responsavellegal'
        );
if (count($arrObjMdPetVincRepresentantProcEspecDTO)>0){
    $objMdPetVinculoDTO->adicionarCriterio(
            array('IdMdPetVinculo'),
            array(InfraDTO::$OPER_IN),
            array(InfraArray::converterArrInfraDTO($arrObjMdPetVincRepresentantProcEspecDTO,'IdMdPetVinculo')),
            null,
            'procuradorespecial'
            );

	$objMdPetVinculoDTO->agruparCriterios(array('responsavellegal','procuradorespecial'), InfraDTO::$OPER_LOGICO_OR);
}


if(!empty($_POST)){
    if(!empty($_POST['txtNumeroProcesso'])){
        $strNumeroProcesso = InfraUtil::retirarFormatacao(trim($_POST['txtNumeroProcesso']));
        if ($strNumeroProcesso){
            $intNumeroProcesso = intval($strNumeroProcesso);
        }
        $objMdPetVinculoDTO->setStrProtocoloFormatadoPesquisa('%'.$intNumeroProcesso.'%',InfraDTO::$OPER_LIKE);
    }
    if(!empty($_POST['txtCnpj'])){
        $strCnpj = InfraUtil::retirarFormatacao(trim($_POST['txtCnpj']));
        if ($strCnpj){
            $intCnpj = intval($strCnpj);
        }
        $objMdPetVinculoDTO->setStrCNPJPesquisa('%'.$intCnpj.'%',InfraDTO::$OPER_LIKE);
    }
}

PaginaSEIExterna::getInstance()->prepararOrdenacao($objMdPetVinculoDTO, 'RazaoSocialNomeVinc', InfraDTO::$TIPO_ORDENACAO_ASC, true);
PaginaSEIExterna::getInstance()->prepararPaginacao($objMdPetVinculoDTO);

$arrRegistro = $objMdPetVinculoRN->listar($objMdPetVinculoDTO);
$numRegistros = count($arrRegistro);

PaginaSEIExterna::getInstance()->processarPaginacao($objMdPetVinculoDTO);

if ($numRegistros > 0) {

  $strResultado = '';
  $strSumarioTabela = 'Vincula��o a Pessoas Jur�dicas como Respons�vel Legal';
  $strCaptionTabela = 'Vincula��o a Pessoas Jur�dicas como Respons�vel Legal';
  $strResultado .= '<table width="99%" class="infraTable" summary="' . $strSumarioTabela . '">';
  $strResultado .= '<caption class="infraCaption">' . PaginaSEIExterna::getInstance()->gerarCaptionTabela($strCaptionTabela, $numRegistros) . '</caption>';

  $strResultado .= '<tr>';
  $strResultado .= '<th class="infraTh" style="width:170px">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVinculoDTO, 'N�mero do Processo', 'ProtocoloFormatado', $arrRegistro) . '</th>';
  $strResultado .= '<th class="infraTh" style="width:130px">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVinculoDTO, 'CNPJ', 'CNPJ', $arrRegistro) . '</th>';
  $strResultado .= '<th class="infraTh">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVinculoDTO, 'Raz�o Social', 'RazaoSocialNomeVinc', $arrRegistro) . '</th>';
  $strResultado .= '<th class="infraTh">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVinculoDTO, 'Respons�vel Legal', 'NomeContatoRepresentante', $arrRegistro) .'</th>';
  $strResultado .= '<th class="infraTh" style="width:80px">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVinculoDTO, 'Situa��o', 'StaEstado', $arrRegistro) .'</th>';
  $strResultado .= '<th class="infraTh" style="width:50px">A��es</th>';
  $strResultado .= '</tr>';
//Populando obj para tabela

  $arrStaTipoVinculo = [
    MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL => 'Procurador Especial',
    MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL => 'Respons�vel Legal'
  ];
  
  $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
  
  foreach ($arrRegistro as $registro) {

    if (!($objMdPetVincRepresentantDTO instanceof MdPetVincRepresentantDTO)) {
        //$objMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->getResponsavelLegal(array('idVinculo' => $registro->getNumIdMdPetVinculo())); 
    }
    
    //Acesso Externo
    $objUsuarioDTO = new UsuarioDTO();
    $objUsuarioDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
    $objUsuarioDTO->retNumIdContato();

    $objUsuarioRN = new UsuarioRN();
    $arrObjUsuarioDTO = $objUsuarioRN->listarRN0490($objUsuarioDTO);

    if (count($arrObjUsuarioDTO)>0){
         $idContato = $arrObjUsuarioDTO[0]->getNumIdContato();
    }
    
    $idProcedimento = $registro->getDblIdProtocolo();
    $objMdPetAcessoExternoRN = new MdPetAcessoExternoRN();
    $idAcessoExterno = $objMdPetAcessoExternoRN->_getUltimaConcessaoAcessoExternoModulo($idProcedimento, $idContato, true);
    SessaoSEIExterna::getInstance()->configurarAcessoExterno($idAcessoExterno);
    
    $strLinkProcedimento = SessaoSEIExterna::getInstance()->assinarLink('processo_acesso_externo_consulta.php?id_acesso_externo=' . $idAcessoExterno);
    //Acesso Externo - fim

    SessaoSEIExterna::getInstance()->configurarAcessoExterno(null);

    $iconeAlterar = "";
    $strResultado .= '<tr class="infraTrClara">';
    $strResultado .= '<td align="center"><a href="javascript:void(0);" onclick="window.open(\'' . $strLinkProcedimento . '\');" alt="' . PaginaSEIExterna::tratarHTML($registro->getStrProtocoloFormatado()) . '" title="' . PaginaSEIExterna::tratarHTML($registro->getStrNomeTipoProcedimento()) . '" class="ancoraPadraoAzul">' . PaginaSEIExterna::tratarHTML($registro->getStrProtocoloFormatado()) . '</a></td>';
    $strResultado .= '<td>' . InfraUtil::formatarCnpj($registro->getDblCNPJ()) . '</td>';
    $strResultado .= '<td>' . PaginaSEI::tratarHTML($registro->getStrRazaoSocialNomeVinc()) . '</td>';
    $strResultado .= '<td>' . $registro->getStrNomeContatoRepresentante() . '</td>';

    $tpSit = $registro->getStrStaEstado();
    if($tpSit == MdPetVincRepresentantRN::$RP_ATIVO){
        $strSituacao = 'Ativa';
    }else if($tpSit == MdPetVincRepresentantRN::$RP_SUSPENSO){
        $strSituacao = 'Suspensa';
    }else if($tpSit == MdPetVincRepresentantRN::$RP_REVOGADA){
        $strSituacao = 'Revogada';
    }else if($tpSit == MdPetVincRepresentantRN::$RP_RENUNCIADA){
        $strSituacao = 'Renunciada';
    } else if($tpSit == MdPetVincRepresentantRN::$RP_VENCIDA){
        $strSituacao = 'Vencida';
    }
    $strResultado .= '<td>' . $strSituacao . '</td>';

    $strResultado .= '<input type="hidden" value="'.$url.'" id="urlLinkDesativar'.$registro->getNumIdMdPetVinculoRepresent().'"/>';
    $iconeConsulta = '<a href="' . SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinc_usu_ext_consultar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'].'&id_vinculo='.$registro->getNumIdMdPetVinculo()) . '"><img style="width:16px;"  src="' . PaginaSEIExterna::getInstance()->getDiretorioImagensGlobal() . '/consultar.gif" title="Consultar Cadastro da Pessoa Jur�dica" alt="Consultar Cadastro da Pessoa Jur�dica" class="infraImg" /></a>';
    if($registro->getStrTipoRepresentante()==MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL && $registro->getStrStaEstado()==MdPetVincRepresentantRN::$RP_ATIVO) {
        $iconeAlterar = '<a href="' . SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinc_usu_ext_alterar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&id_vinculo=' . $registro->getNumIdMdPetVinculo()) . '"><img style="width:16px;"  src="' . PaginaSEIExterna::getInstance()->getDiretorioImagensGlobal() . '/alterar.gif" title="Atualizar Atos Constitutivos da Pessoa Jur�dica" alt="Atualizar Atos Constitutivos da Pessoa Jur�dica" class="infraImg" /></a>';
    }
    $strResultado .= '<td align="center">' . $iconeConsulta . $iconeAlterar . '</td>';
    $strResultado .= '</tr>';

  }
  $strResultado .= '</table>';

}
PaginaSEIExterna::getInstance()->montarDocType();
PaginaSEIExterna::getInstance()->abrirHtml();
PaginaSEIExterna::getInstance()->abrirHead();
PaginaSEIExterna::getInstance()->montarMeta();
PaginaSEIExterna::getInstance()->montarTitle(PaginaSEIExterna::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo);
PaginaSEIExterna::getInstance()->montarStyle();
PaginaSEIExterna::getInstance()->abrirStyle();

PaginaSEIExterna::getInstance()->fecharStyle();
PaginaSEIExterna::getInstance()->montarJavaScript();
require_once 'md_pet_vinc_usu_ext_lista_js.php';
PaginaSEIExterna::getInstance()->abrirJavaScript();
?>
function inicializar(){
    infraEfeitoTabelas();
}
<?
PaginaSEIExterna::getInstance()->fecharJavaScript();
PaginaSEIExterna::getInstance()->fecharHead();
PaginaSEIExterna::getInstance()->abrirBody($strTitulo,'onload="inicializar();"');

?>
<style type="text/css">
#container{
  width: 100%;
}
.clear {
  clear: both;
}

.bloco {
  float: left;
  margin-top: 1%;
  margin-right: 1%;
}

label[for^=txt] {
  display: block;
  white-space: nowrap;
}

#txtNumeroProcesso{
  width:98%;
}
#txtCnpj{
  width:98%;
}
</style>
<form id="frmPesquisa" method="post" action="">
  <?
  PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
  PaginaSEIExterna::getInstance()->abrirAreaDados('auto');
  ?>

    <div class="bloco" style="width:155px">
        <label id="lblNumeroProcesso" 
               for="txtNumeroProcesso" 
               class="infraLabelOpcional">N�mero do Processo:</label>
        <input type="text" 
               id="txtNumeroProcesso" 
               name="txtNumeroProcesso" 
               class="infraText"
               value="<?= PaginaSEIExterna::tratarHTML($_POST['txtNumeroProcesso']) ?>" 
               maxlength="100"
               tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"/>
    </div>

    <div class="bloco" style="width:155px">
        <label id="lblCnpj" 
               for="txtCnpj" 
               class="infraLabelOpcional">CNPJ:</label>
        <input type="text" 
               id="txtCnpj" 
               name="txtCnpj" 
               class="infraText" 
               onkeypress="return infraMascaraCnpj(this, event)"
               value="<?= PaginaSEIExterna::tratarHTML($_POST['txtCnpj']) ?>" maxlength="100"
               tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"/>
    </div>
<?
PaginaSEIExterna::getInstance()->fecharAreaDados();
PaginaSEIExterna::getInstance()->montarAreaTabela($strResultado, $numRegistros);
//PaginaSEIExterna::getInstance()->montarAreaDebug();
PaginaSEIExterna::getInstance()->montarBarraComandosInferior($arrComandos);
?>
</form>
<?
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
?>
