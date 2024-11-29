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
        $strTitulo = 'Responsável Legal de Pessoa Jurídica';
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
      $jsAlerta = 'alert(\'Tipo de Processo para Vinculação de Usuário Externo não parametrizado. Contate o Administrador!\');';
      $arrComandos[] = '<button type="button" accesskey="N" id="btnNovoResponsavelLegal" value="NovoResponsavelLegal" onclick="'.$jsAlerta. '" class="infraButton"><span class="infraTeclaAtalho">N</span>ovo Responsável Legal</button>';
  }else{
      $arrComandos[] = '<button type="button" accesskey="N" id="btnNovoResponsavelLegal" value="NovoResponsavelLegal" onclick="location.href=\'' . SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinc_usu_ext_cadastrar&acao_origem=' . $_GET['acao']) . '\';" class="infraButton"><span class="infraTeclaAtalho">N</span>ovo Responsável Legal</button>';
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

$objMdPetVinculoDTO->adicionarCriterio(
        array('IdContatoRepresentante'),
        array(InfraDTO::$OPER_IGUAL),
        array($idContatoRepresentante),
        null,
        'responsavellegal'
        );
if (!empty($arrObjMdPetVincRepresentantProcEspecDTO)){
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
  $strSumarioTabela = 'Vinculação a Pessoas Jurídicas como Responsável Legal';
  $strCaptionTabela = 'Vinculação a Pessoas Jurídicas como Responsável Legal';
  $strResultado .= '<table class="infraTable" summary="' . $strSumarioTabela . '" width="100%">';
  $strResultado .= '<caption class="infraCaption">' . PaginaSEIExterna::getInstance()->gerarCaptionTabela($strCaptionTabela, $numRegistros) . '</caption>';

  $strResultado .= '<tr>';
  $strResultado .= '<th class="infraTh" align="center" style="min-width: 200px;">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVinculoDTO, 'Número do Processo', 'ProtocoloFormatado', $arrRegistro) . '</th>';
  $strResultado .= '<th class="infraTh" align="center"  style="min-width: 160px;">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVinculoDTO, 'CNPJ', 'CNPJ', $arrRegistro) . '</th>';
  $strResultado .= '<th class="infraTh" align="center">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVinculoDTO, 'Razão Social', 'RazaoSocialNomeVinc', $arrRegistro) . '</th>';
  $strResultado .= '<th class="infraTh" align="center">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVinculoDTO, 'Responsável Legal', 'NomeContatoRepresentante', $arrRegistro) .'</th>';
  $strResultado .= '<th class="infraTh" align="center" style="min-width: 110px;">' . PaginaSEIExterna::getInstance()->getThOrdenacao($objMdPetVinculoDTO, 'Situação', 'StaEstado', $arrRegistro) .'</th>';
  $strResultado .= '<th class="infraTh" align="center"  style="min-width: 90px;">Ações</th>';
  $strResultado .= '</tr>';
//Populando obj para tabela

  $arrStaTipoVinculo = [
    MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL => 'Procurador Especial',
    MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL => 'Responsável Legal'
  ];

  $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();

  foreach ($arrRegistro as $registro) {

    //Acesso Externo
    $objUsuarioDTO = new UsuarioDTO();
    $objUsuarioDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
    $objUsuarioDTO->retNumIdContato();

    $objUsuarioRN = new UsuarioRN();
    $arrObjUsuarioDTO = $objUsuarioRN->listarRN0490($objUsuarioDTO);

    if (!empty($arrObjUsuarioDTO)){
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
    $strResultado .= '<td class="text-center"><a href="javascript:void(0);" onclick="window.open(\'' . $strLinkProcedimento . '\');" alt="' . PaginaSEIExterna::tratarHTML($registro->getStrProtocoloFormatado()) . '" title="' . PaginaSEIExterna::tratarHTML($registro->getStrNomeTipoProcedimento()) . '" class="ancoraPadraoAzul">' . PaginaSEIExterna::tratarHTML($registro->getStrProtocoloFormatado()) . '</a></td>';
    $strResultado .= '<td class="text-center">' . InfraUtil::formatarCnpj($registro->getDblCNPJ()) . '</td>';
    $strResultado .= '<td class="text-center">' . PaginaSEI::tratarHTML($registro->getStrRazaoSocialNomeVinc()) . '</td>';
    $strResultado .= '<td class="text-center">' . $registro->getStrNomeContatoRepresentante() . '</td>';

	$strSituacao = '';

	switch ($registro->getStrStaEstado()) {
		case MdPetVincRepresentantRN::$RP_ATIVO: $strSituacao = 'Ativa'; break;
		case MdPetVincRepresentantRN::$RP_SUSPENSO: $strSituacao = 'Suspensa'; break;
		case MdPetVincRepresentantRN::$RP_REVOGADA: $strSituacao = 'Revogada'; break;
		case MdPetVincRepresentantRN::$RP_RENUNCIADA: $strSituacao = 'Renunciada'; break;
		case MdPetVincRepresentantRN::$RP_VENCIDA: $strSituacao = 'Vencida'; break;
		case MdPetVincRepresentantRN::$RP_SUBSTITUIDA: $strSituacao = 'Substituída'; break;
		case MdPetVincRepresentantRN::$RP_INATIVO: $strSituacao = 'Inativa'; break;
	}

    $strResultado .= '<td class="text-center">'. $strSituacao . '</td>';

    $iconeConsulta = '<a href="' . SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinc_usu_ext_consultar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'].'&id_vinculo='.$registro->getNumIdMdPetVinculo().'&id_representante='.$registro->getNumIdMdPetVinculoRepresent().'&estado='.$registro->getStrStaEstado()) . '"><img src="' . PaginaSEIExterna::getInstance()->getDiretorioSvgGlobal() . '/consultar.svg?'.Icone::VERSAO.'" title="Consultar Cadastro da Pessoa Jurídica" alt="Consultar Cadastro da Pessoa Jurídica" class="infraImg" /></a>';
    if($registro->getStrTipoRepresentante()==MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL && $registro->getStrStaEstado()==MdPetVincRepresentantRN::$RP_ATIVO) {
        $iconeAlterar = '<a href="' . SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinc_usu_ext_alterar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&id_vinculo=' . $registro->getNumIdMdPetVinculo().'&id_representante='.$registro->getNumIdMdPetVinculoRepresent().'&estado='.$registro->getStrStaEstado()) . '"><img src="' . PaginaSEIExterna::getInstance()->getDiretorioSvgGlobal() . '/alterar.svg?'.Icone::VERSAO.'" title="Atualizar Atos Constitutivos da Pessoa Jurídica" alt="Atualizar Atos Constitutivos da Pessoa Jurídica" class="infraImg" /></a>';
    }
    $strResultado .= '<td class="text-center">' . $iconeConsulta . $iconeAlterar . '</td>';
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
<form id="frmPesquisa" method="post" action="">

<?
        PaginaSEIExterna::getInstance()->montarBarraComandosSuperior($arrComandos);
        PaginaSEIExterna::getInstance()->abrirAreaDados('auto');
        ?>
  <div class="row">
    <div class="col-xl-4 col-lg-4 col-md-5 col-sm-6 col-xs-12">
      <div class="form-group">
        <label id="lblNumeroProcesso"
               for="txtNumeroProcesso"
               class="infraLabelOpcional">Número do Processo:</label>
        <input type="text"
               id="txtNumeroProcesso"
               name="txtNumeroProcesso"
               class="infraText form-control"
               value="<?= PaginaSEIExterna::tratarHTML($_POST['txtNumeroProcesso']) ?>"
               maxlength="100"
               tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"/>
      </div>
    </div>
    <div class="col-xl-4 col-lg-4 col-md-5 col-sm-6 col-xs-12">
      <div class="form-group">
      <label id="lblCnpj"
               for="txtCnpj"
               class="infraLabelOpcional">CNPJ:</label>
        <input type="text"
               id="txtCnpj"
               name="txtCnpj"
               class="infraText form-control"
               onkeypress="return infraMascaraCnpj(this, event)"
               value="<?= PaginaSEIExterna::tratarHTML($_POST['txtCnpj']) ?>" maxlength="100"
               tabindex="<?= PaginaSEIExterna::getInstance()->getProxTabDados() ?>"/>
      </div>
    </div>
  </div>
  <div class="col-12">

    <?
PaginaSEIExterna::getInstance()->fecharAreaDados();
echo '<div class="table-responsive">';
PaginaSEIExterna::getInstance()->montarAreaTabela($strResultado, $numRegistros);
echo '</div>';
//PaginaSEIExterna::getInstance()->montarAreaDebug();
PaginaSEIExterna::getInstance()->montarBarraComandosInferior($arrComandos);
?>
</div>
</form>
<?
PaginaSEIExterna::getInstance()->fecharBody();
PaginaSEIExterna::getInstance()->fecharHtml();
?>
