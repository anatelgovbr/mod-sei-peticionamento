<?
/**
 * ANATEL
 *
 * 23/06/2016 - criado por marcelo.bezerra@cast.com.br - CAST
 *
 */

try {

    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    //////////////////////////////////////////////////////////////////////////////
    InfraDebug::getInstance()->setBolLigado(false);
    InfraDebug::getInstance()->setBolDebugInfra(false);
    InfraDebug::getInstance()->limpar();
    //////////////////////////////////////////////////////////////////////////////

    SessaoSEI::getInstance()->validarLink();
    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

//  PaginaSEIExterna::getInstance()->setTipoPagina(InfraPagina::$TIPO_PAGINA_SIMPLES);
    $strLinkAjaxValidacoesNumeroSEI = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_validar_num_sei');

    switch ($_GET['acao']) {

        case 'md_pet_vinc_responsavel_cadastrar':
            $strTitulo = 'Alterar o Responsável Legal';
            $janelaSelecaoPorNome = SessaoSEIExterna::getInstance()->getAtributo('janelaSelecaoPorNome');
            $strPrimeiroItemValor = 'null';
            $strPrimeiroItemDescricao = '&nbsp;';
            $strValorItemSelecionado = null;
            $strTipo = 'Cadastro';

            $idVinculo = isset($_GET['idVinculo']) ? $_GET['idVinculo'] : $_POST['hdnIdVinculo'];

            //Recuperar dados para Pessoa Juridica.
            $objMdPetVinculoRN = new MdPetVinculoRN();
            $objMdPetVinculoDTO = new MdPetVinculoDTO();
            $objMdPetVinculoDTO->retNumIdMdPetVinculo();
            $objMdPetVinculoDTO->retDblCNPJ();
            $objMdPetVinculoDTO->retStrRazaoSocialNomeVinc();
            $objMdPetVinculoDTO->retNumIdContatoRepresentante();
            $objMdPetVinculoDTO->retStrCpfContatoRepresentante();
            $objMdPetVinculoDTO->retStrNomeContatoRepresentante();
            $objMdPetVinculoDTO->setNumIdMdPetVinculo($idVinculo);
            $objMdPetVinculoDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL);
            $objMdPetVinculoDTO->setDistinct(true);
            $objMdPetVinculoDTO = $objMdPetVinculoRN->consultar($objMdPetVinculoDTO);

            if (!empty($_POST)) {

                $mdPetIntegracaoRN = new MdPetIntegracaoRN();
                $mdPetIntegracaoDTO = new MdPetIntegracaoDTO();
                $mdPetIntegracaoDTO->retNumIdMdPetIntegracao();
                $mdPetIntegracaoDTO->retStrEnderecoWsdl();
                $mdPetIntegracaoDTO->retStrOperacaoWsdl();
                $mdPetIntegracaoDTO->setNumIdMdPetIntegFuncionalid(MdPetIntegFuncionalidRN::$ID_FUNCIONALIDADE_CNPJ_RECEITA_FEDERAL);

                $arrFuncionalidadeCadastrada = $mdPetIntegracaoRN->consultar($mdPetIntegracaoDTO);

                $qtdFuncionalidade = $mdPetIntegracaoRN->contar($mdPetIntegracaoDTO);

                $cpf = InfraUtil::retirarFormatacao($_POST['txtCpfNovo']);

                if ($qtdFuncionalidade > 0) {
                    $cpf = str_pad($cpf, '11', '0', STR_PAD_LEFT);
                    $strUrlWebservice = $arrFuncionalidadeCadastrada->getStrEnderecoWsdl();
                    $strMetodoWebservice = $arrFuncionalidadeCadastrada->getStrOperacaoWsdl();
                    $cnpj = InfraUtil::retirarFormatacao($objMdPetVinculoDTO->getDblCNPJ());
                    $cnpj = str_pad(InfraUtil::retirarFormatacao($objMdPetVinculoDTO->getDblCNPJ()), 14, '0', STR_PAD_LEFT);

                    $objMdPetSoapClienteRN = new MdPetSoapClienteRN($strUrlWebservice, 'wsdl');


                    //Recuperando meses - alterado
                    $objMdPetIntegParametroDTO = new MdPetIntegParametroDTO();
                    $objMdPetIntegParametroDTO->retStrValorPadrao();
                    $objMdPetIntegParametroDTO->setStrTpParametro(MdPetIntegParametroRN::$TIPO_PARAMETRO);
                    $objMdPetIntegParametroDTO->retStrTpParametro();
                    $objMdPetIntegParametroDTO->retStrNome();
                    $objMdPetIntegParametroDTO->setNumIdMdPetIntegracao($objMdPetIntegracao->getNumIdMdPetIntegracao());
                    $objMdPetIntegParametroRN = new MdPetIntegParametroRN();
                    $arrObjMdPetIntegParametroRN = $objMdPetIntegParametroRN->consultar($objMdPetIntegParametroDTO);

                    if ($arrObjMdPetIntegParametroRN) {
                        //Convertendo
                        $mes = (int)$arrObjMdPetIntegParametroRN->getStrValorPadrao();

                        $parametro = [
                            $strMetodoWebservice => [
                                'cnpj' => $cnpj
                                , 'cpfUsuario' => $cpfUsuarioLogado
                                , $arrObjMdPetIntegParametroRN->getStrNome() => $mes
                            ]
                        ];

                    } else {

                        $parametro = [
                            $strMetodoWebservice => [
                                'cnpj' => $cnpj
                                , 'cpfUsuario' => $cpfUsuarioLogado

                            ]
                        ];

                    }


                    $consulta = $objMdPetSoapClienteRN->consultarWsdl($strMetodoWebservice, $parametro);
                    $cpfResponsavelLegalReceita = $consulta['PessoaJuridica']['responsavel']['cpf'];

                    $objMdPetVinculoUsuExtRN = new MdPetVinculoUsuExtRN();

                    $dados = $_POST;

                    $dados['isAlteracaoCrud'] = true;
//          $idContato = $objMdPetVinculoUsuExtRN->salvarDadosContatoCnpj($dados);

                    $objMdPetVinculoUsuExtRN = new MdPetVinculoUsuExtRN();
                    $arrObjMdPetVinculoUsuExtRN = $objMdPetVinculoUsuExtRN->buscarVinculoDados($_POST['hdnIdVinculo']);

                    if (!empty($arrObjMdPetVinculoUsuExtRN)) {
                        $dados['idContato'] = $arrObjMdPetVinculoUsuExtRN[0]->getNumIdContato();
                    }

                    $url = '';

                    $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
                    $idRecibo = '';
                    $idRecibo = $objMdPetVincRepresentantRN->realizarProcessosAlteracaoResponsavelLegal($dados);

                }

                $contatoRN = new ContatoRN();
                $contatoDTO = new ContatoDTO();
                $contatoDTO->retNumIdContato();
                $contatoDTO->setDblCpf($cpf);

                $objMdPetContatoRN = new MdPetContatoRN();
                $idTipoContato = $objMdPetContatoRN->getIdTipoContatoUsExt();
                if (!empty($idTipoContato)) {
                    $contatoDTO->setNumIdTipoContato($idTipoContato);
                }
                $arrContato = $contatoRN->consultarRN0324($contatoDTO);

            }
            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }

} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}

$hashAnexo = "";
$idAnexo = "";

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo);
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
require_once 'md_pet_vinc_responsavel_cadastro_css.php';
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
require_once 'md_pet_vinc_responsavel_cadastro_js.php';
PaginaSEI::getInstance()->abrirJavaScript();
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');

$urlBaseLink = "";
$arrComandos = array();
$arrComandos[] = '<button type="button" accesskey="s" name="Salvar" value="Salvar" onclick="salvar()" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
$arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';


//$strLinkBaseFormEdicao = 'controlador_externo.php?edicaoExibir=true&acao=' . $_GET['acao'];
$strLinkBaseFormEdicao = 'controlador.php?edicaoExibir=true&acao=' . $_GET['acao'];
$strLinkEdicaHash = PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink($strLinkBaseFormEdicao));
?>

    <!-- Formulario usado para viabilizar fluxo de edição de contato -->

    <form id="frmEdicaoAuxiliar"
          name="frmEdicaoAuxiliar"
          method="post"
          action="<?= $strLinkEdicaHash ?>">

        <?php
        PaginaSEI::getInstance()->abrirAreaDados('auto');
        PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
        ?>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-10 col-xl-10">
                ATENÇÃO
                <p id="txtInformativo1">Os dados aqui dispostos dizem respeito ao Responsável Legal pela Pessoa Jurídica
                    indicada, conforme constante no SEI.<br/>
                    Informe abaixo o CPF do Usuário Externo que deseja indicar como novo Responsável
                    Legal por esta Pessoa Jurídica.</p>

            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
                <label id="lblCnpj" class="infraLabelObrigatorio">CNPJ:</label>
                <input type="text" id="txtCnpj" name="txtCnpj"
                       class="infraText form-control"
                       disabled="disabled"
                       value="<?= PaginaSEI::tratarHTML(InfraUtil::formatarCnpj($objMdPetVinculoDTO->getDblCNPJ())) ?>"
                       onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250"
                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-5 col-xl-5">
                <label id="lblRazaoSocial" class="infraLabelObrigatorio">Razão Social:</label>
                <input type="text" id="txtRazaoSocial" name="txtRazaoSocial"
                       class="infraText form-control"
                       disabled="disabled"
                       value="<?= PaginaSEI::tratarHTML($objMdPetVinculoDTO->getStrRazaoSocialNomeVinc()) ?>"
                       onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250"
                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
                <label id="lblCnpj" class="infraLabelObrigatorio">CPF do Responsável Legal:</label>
                <input type="text" id="txtCpf" name="txtCpf"
                       class="infraText form-control"
                       disabled="disabled"
                       value="<?= PaginaSEI::tratarHTML(InfraUtil::formatarCpf($objMdPetVinculoDTO->getStrCpfContatoRepresentante())) ?>"
                       onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250"
                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-5 col-xl-5">
                <label id="txtRazaoSocial" class="infraLabelObrigatorio">Nome do Responsável Legal:</label>
                <input type="text" id="txtNome" name="txtNome"
                       disabled="disabled"
                       class="infraText form-control"
                       value="<?= PaginaSEI::tratarHTML($objMdPetVinculoDTO->getStrNomeContatoRepresentante()) ?>"
                       onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250"
                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
                <label class="infraLabelObrigatorio">CPF do Usuário Externo:</label>
                <div class="input-group mb-3 zerarFormatacao">
                    <input type="text" id="txtCpfNovo" name="txtCpfNovo" style="width: 60%"
                           class="infraText form-control"
                           onkeypress="return infraMascaraCPF(this,event);" maxlength="14"
                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                    <button type="button" accesskey="V" style="margin-left: 5px"
                            id="btnValidar" onclick="buscarCpf()" class="infraButton">
                        <span class="infraTeclaAtalho">V</span>alidar CPF
                    </button>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-5 col-xl-5">
                <label class="infraLabelObrigatorio">Nome do Usuário Externo:</label>
                <input type="text" id="txtNomeNovo" name="txtNomeNovo"
                       class="infraText form-control"
                       readonly="readonly"
                       onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250"
                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
                <label class="infraLabelObrigatorio">Número SEI da Justificativa: </label>
                <div class="input-group mb-3 zerarFormatacao">
                    <input type="text" id="txtNumeroSei" name="txtNumeroSei"
                           class="infraText form-control"
                           value=""
                           onkeypress="return infraMascaraTexto(this,event,250);" maxlength="10"
                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                    <button type="button" accesskey="V" style="margin-left: 5px"
                            id="btnValidar" onclick="validarNumeroSEI();" class="infraButton">
                        <span class="infraTeclaAtalho">V</span>alidar
                    </button>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 col-lg-5 col-xl-5">
                <label class="infraLabelObrigatorio" style="display: inline-block; width: 60% "></label>
                <input type="text" id="txtTipo" name="txtTipo" class="infraText form-control" readonly="readonly"
                       tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" value="<?= $txtTipo ?>"/>
            </div>
        </div>
        <input type="hidden" name="hdnIdVinculo" id="hdnIdVinculo" value="<?php echo $idVinculo ?>"/>
        <input type="hidden" name="hdnIdContatoNovo" id="hdnIdContatoNovo" value=""/>
        <input type="hidden" name="hdnIdContato" id="hdnIdContato"
               value="<?= $objMdPetVinculoDTO->getNumIdContatoRepresentante() ?>"/>
        <input type="hidden" name="hdnIdDocumento" id="hdnIdDocumento" value=""/>
        <input type="hidden" name="hdnValidarNumSEI" id="hdnValidarNumSEI" value=""/>
    </form>

<?php
PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
//PaginaSEI::getInstance()->montarAreaDebug();
PaginaSEI::getInstance()->fecharAreaDados();
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>