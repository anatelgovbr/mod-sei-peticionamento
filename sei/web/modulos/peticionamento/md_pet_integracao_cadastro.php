<?

/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 25/01/2018 - criado por Usuário
 *
 * Versão do Gerador de Código: 1.41.0
 *
 * Versão no SVN: $Id$
 */

try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    //////////////////////////////////////////////////////////////////////////////
    //InfraDebug::getInstance()->setBolLigado(false);
    //InfraDebug::getInstance()->setBolDebugInfra(true);
    //InfraDebug::getInstance()->limpar();
    ///////////////////////////////////////////////////////////////////////pree///////

    SessaoSEI::getInstance()->validarLink();

    PaginaSEI::getInstance()->verificarSelecao('md_pet_integracao_selecionar');

    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    PaginaSEI::getInstance()->salvarCamposPost(array('selMdPetIntegFuncionalid'));


    $objMdPetIntegracaoDTO = new MdPetIntegracaoDTO();

    //Recuperando prazo se existir
    $mes = array();
    $objMdPetIntegParametroDTO = new MdPetIntegParametroDTO();
    $objMdPetIntegParametroDTO->retTodos();
    $objMdPetIntegParametroDTO->setNumIdMdPetIntegracao($_GET['id_md_pet_integracao']);
    $objMdPetIntegParametroRN = new MdPetIntegParametroRN();
    $arrMdPetIntegParametroRN = $objMdPetIntegParametroRN->listar($objMdPetIntegParametroDTO);
    $mes = InfraArray::converterArrInfraDTO($arrMdPetIntegParametroRN, 'ValorPadrao');

    $strDesabilitar = '';

    $arrComandos = array();

    switch ($_GET['acao']) {
        case 'md_pet_integracao_cadastrar':
            $strTitulo = 'Nova Integração';
            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmCadastrarMdPetIntegracao" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao']) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            $objMdPetIntegracaoDTO->setNumIdMdPetIntegracao(null);
            $numIdMdPetIntegFuncionalid = PaginaSEI::getInstance()->recuperarCampo('selMdPetIntegFuncionalid');
            if ($numIdMdPetIntegFuncionalid !== '') {
                $objMdPetIntegracaoDTO->setNumIdMdPetIntegFuncionalid($numIdMdPetIntegFuncionalid);
            } else {
                $objMdPetIntegracaoDTO->setNumIdMdPetIntegFuncionalid(null);
            }

            $objMdPetIntegracaoDTO->setStrNome($_POST['txtNome']);
            $objMdPetIntegracaoDTO->setStrStaUtilizarWs($_POST['rdStaUtilizarWs']);
            if ($_POST['rdStaUtilizaWs'] == 'N') {
                $objMdPetIntegracaoDTO->setStrEnderecoWsdl('');
                $objMdPetIntegracaoDTO->setStrOperacaoWsdl('');
                $objMdPetIntegracaoDTO->setStrSinCache('');
                $objMdPetIntegracaoDTO->setStrSinAtivo('S');
            } else {
                $objMdPetIntegracaoDTO->setStrTpClienteWs($_POST['rdStaTpClienteWs']);
                $objMdPetIntegracaoDTO->setDblNuVersao($_POST['selNuVersao']);
                $objMdPetIntegracaoDTO->setStrEnderecoWsdl($_POST['txtEnderecoWsdl']);
                $objMdPetIntegracaoDTO->setStrOperacaoWsdl($_POST['selOperacaoWsdl']);
                $objMdPetIntegracaoDTO->setStrCodReceitaSuspAuto($_POST['txtCodRFBSuspensaoAutomatica']);
                $objMdPetIntegracaoDTO->setStrSinCache('');
                $objMdPetIntegracaoDTO->setStrSinAtivo('S');
            }


            if (isset($_POST['sbmCadastrarMdPetIntegracao'])) {
                try {

                    $objMdPetIntegracaoRN = new MdPetIntegracaoRN();
                    $objMdPetIntegracaoDTO->setStrSinCache(PaginaSEI::getInstance()->getCheckbox($_POST['chkSinCache']));
                    $objMdPetIntegracaoDTO->setStrSinTpLogradouro(PaginaSEI::getInstance()->getCheckbox($_POST['chkSinTipo']));
                    $objMdPetIntegracaoDTO->setStrSinNuLogradouro(PaginaSEI::getInstance()->getCheckbox($_POST['chkSinNumero']));
                    $objMdPetIntegracaoDTO->setStrSinCompLogradouro(PaginaSEI::getInstance()->getCheckbox($_POST['chkSinTipo']));
                    $objMdPetIntegracaoDTO->setStrSinCompLogradouro(PaginaSEI::getInstance()->getCheckbox($_POST['chkSinComplemento']));
                    $objMdPetIntegracaoDTO = $objMdPetIntegracaoRN->cadastrarCompleto($objMdPetIntegracaoDTO);

                    PaginaSEI::getInstance()->adicionarMensagem('Integração "' . $objMdPetIntegracaoDTO->getStrNome() . '" cadastrada com sucesso.');
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . '&id_md_pet_integracao=' . $objMdPetIntegracaoDTO->getNumIdMdPetIntegracao() . PaginaSEI::getInstance()->montarAncora($objMdPetIntegracaoDTO->getNumIdMdPetIntegracao())));
                    die;
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }
            break;

        case 'md_pet_integracao_alterar':

            $strTitulo = 'Alterar Integração';
            $arrComandos[] = '<button type="submit" accesskey="S" name="sbmAlterarMdPetIntegracao" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $strDesabilitar = 'disabled="disabled"';

            if (isset($_GET['id_md_pet_integracao'])) {
                $objMdPetIntegracaoDTO->setNumIdMdPetIntegracao($_GET['id_md_pet_integracao']);
                $objMdPetIntegracaoDTO->setBolExclusaoLogica(false);
                $objMdPetIntegracaoDTO->retTodos();
                $objMdPetIntegracaoRN = new MdPetIntegracaoRN();
                $objMdPetIntegracaoDTO = $objMdPetIntegracaoRN->consultar($objMdPetIntegracaoDTO);
                if ($objMdPetIntegracaoDTO == null) {
                    throw new InfraException("Registro não encontrado.");
                }
            } else {
                $objMdPetIntegracaoDTO->setNumIdMdPetIntegracao($_POST['hdnIdMdPetIntegracao']);
                $objMdPetIntegracaoDTO->setNumIdMdPetIntegFuncionalid($_POST['selMdPetIntegFuncionalid']);
                $objMdPetIntegracaoDTO->setStrNome($_POST['txtNome']);
                $objMdPetIntegracaoDTO->setStrStaUtilizarWs($_POST['rdStaUtilizarWs']);

                # Independente do input StaUtilizarWs ser [S e N], preenche todos os dados abaixo, de acordo com a estória: 5838
                $objMdPetIntegracaoDTO->setStrEnderecoWsdl($_POST['txtEnderecoWsdl']);
                $objMdPetIntegracaoDTO->setStrOperacaoWsdl($_POST['selOperacaoWsdl']);
                $objMdPetIntegracaoDTO->setStrCodReceitaSuspAuto($_POST['txtCodRFBSuspensaoAutomatica']);
                $objMdPetIntegracaoDTO->setStrSinCache(PaginaSEI::getInstance()->getCheckbox($_POST['chkSinCache']));
                $objMdPetIntegracaoDTO->setStrSinAtivo('S');

                $objMdPetIntegracaoDTO->setStrTpClienteWs($_POST['rdStaTpClienteWs']);
                $objMdPetIntegracaoDTO->setDblNuVersao($_POST['selNuVersao']);
                $objMdPetIntegracaoDTO->setStrSinTpLogradouro(PaginaSEI::getInstance()->getCheckbox($_POST['chkSinTipo']));
                $objMdPetIntegracaoDTO->setStrSinNuLogradouro(PaginaSEI::getInstance()->getCheckbox($_POST['chkSinNumero']));
                $objMdPetIntegracaoDTO->setStrSinCompLogradouro(PaginaSEI::getInstance()->getCheckbox($_POST['chkSinComplemento']));
            }

            $arrComandos[] = '<button type="button" accesskey="C" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($objMdPetIntegracaoDTO->getNumIdMdPetIntegracao())) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            if (isset($_POST['sbmAlterarMdPetIntegracao'])) {

                try {
                    $objMdPetIntegracaoRN = new MdPetIntegracaoRN();
                    $objMdPetIntegracaoRN->alterarCompleto($objMdPetIntegracaoDTO);
                    PaginaSEI::getInstance()->adicionarMensagem('Integração "' . $objMdPetIntegracaoDTO->getStrNome() . '" alterada com sucesso.');
                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($objMdPetIntegracaoDTO->getNumIdMdPetIntegracao())));
                    die;
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }
            break;

        case 'md_pet_integracao_consultar':
            $strTitulo = 'Consultar Integração';
            $bolIsConsultar = true;
            $arrComandos[] = '<button type="button" accesskey="c" name="btnFechar" value="Fechar" onclick="location.href=\'' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($_GET['id_md_pet_integracao'])) . '\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
            $objMdPetIntegracaoDTO->setNumIdMdPetIntegracao($_GET['id_md_pet_integracao']);
            $objMdPetIntegracaoDTO->setBolExclusaoLogica(false);
            $objMdPetIntegracaoDTO->retTodos();
            $objMdPetIntegracaoRN = new MdPetIntegracaoRN();
            $objMdPetIntegracaoDTO = $objMdPetIntegracaoRN->consultar($objMdPetIntegracaoDTO);
            if ($objMdPetIntegracaoDTO === null) {
                throw new InfraException("Registro não encontrado.");
            }
            break;

        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }
    $numNuVersao = 1.2;
    $tpLogradouro = '';
    $nuLogradouro = '';
    $compLogradouro = '';
    if ($objMdPetIntegracaoDTO) {
        if ($objMdPetIntegracaoDTO->getStrStaUtilizarWs() == 'N') {
            $staUtilizarWsNao = "checked='checked'";
            $staUtilizarWsSim = "";
        } elseif ($objMdPetIntegracaoDTO->getStrStaUtilizarWs() == 'S') {
            $staUtilizarWsNao = "";
            $staUtilizarWsSim = "checked='checked'";
        }

        if ($objMdPetIntegracaoDTO->getStrTpClienteWs() == 'S') {
            $staTpClienteWs = "checked='checked'";
        }

        $numNuVersao = $objMdPetIntegracaoDTO->getDblNuVersao();
        $codReceitaSuspAuto = $objMdPetIntegracaoDTO->getStrCodReceitaSuspAuto();

        if ($objMdPetIntegracaoDTO->isSetStrSinTpLogradouro()) {
            $tpLogradouro = $objMdPetIntegracaoDTO->getStrSinTpLogradouro() == 'S' ? $objMdPetIntegracaoDTO->getStrSinTpLogradouro() : '';
        } else {
            $objMdPetIntegracaoDTO->setStrSinTpLogradouro('N');
        }

        if ($objMdPetIntegracaoDTO->isSetStrSinNuLogradouro()) {
            $nuLogradouro = $objMdPetIntegracaoDTO->getStrSinNuLogradouro() == 'S' ? $objMdPetIntegracaoDTO->getStrSinNuLogradouro() : '';
        } else {
            $objMdPetIntegracaoDTO->setStrSinNuLogradouro('N');
        }

        if ($objMdPetIntegracaoDTO->isSetStrSinCompLogradouro()) {
            $compLogradouro = $objMdPetIntegracaoDTO->getStrSinCompLogradouro() == 'S' ? $objMdPetIntegracaoDTO->getStrSinCompLogradouro() : '';
        } else {
            $objMdPetIntegracaoDTO->setStrSinCompLogradouro('N');
        }

        $objMdPetIntegParametroDTO = new MdPetIntegParametroDTO;
        $objMdPetIntegParametroDTO->setNumIdMdPetIntegracao($objMdPetIntegracaoDTO->getNumIdMdPetIntegracao());
        //        $objMdPetIntegParametroDTO->setStrTpParametro('P');
        $objMdPetIntegParametroDTO->setBolExclusaoLogica(false);
        $objMdPetIntegParametroDTO->retTodos();
        $objMdPetIntegParametroRN = new MdPetIntegParametroRN();
        $arrObjMdPetIntegParametroDTO = $objMdPetIntegParametroRN->listar($objMdPetIntegParametroDTO);
        $arrParametrosCadastrados = array();
        if (!empty($arrObjMdPetIntegParametroDTO)) {
            foreach ($arrObjMdPetIntegParametroDTO as $item) {
                $arrParametrosCadastrados[] = array(
                    'nome' => $item->getStrNome(),
                    'campo_nome' => $item->getStrNomeCampo(),
                    'valor' => $item->getStrValorPadrao()
                );

                if (trim($item->getStrNomeCampo()) == 'PrazoExpiracao') {
                    $strItensSelCachePrazoExpiracao = $item->getStrNome();
                    $strItensSelCacheDataArmazenamento = $item->getStrValorPadrao();
                }

                if (trim($item->getStrNome()) == 'cpfPessoa' && $item->getStrTpParametro() == 'E') {
                    $strItensSelCpfPessoa = $item->getStrNomeCampo();
                }

                if (trim($item->getStrNome()) == 'cpfUsuario' && $item->getStrTpParametro() == 'E') {
                    $strItensSelCpfUsuario = $item->getStrNomeCampo();
                }

                if (trim($item->getStrNome()) == 'cnpjEmpresa' && $item->getStrTpParametro() == 'E') {
                    $strItensSelCnpjEmpresa = $item->getStrNomeCampo();
                }
            }
        }

        $strItensSelMdPetIntegFuncionalid = MdPetIntegFuncionalidINT::montarSelectNomeNaoUtilizado('null', '&nbsp;', $objMdPetIntegracaoDTO->getNumIdMdPetIntegFuncionalid(), $objMdPetIntegracaoDTO->getNumIdMdPetIntegracao());

    } else {
        $strItensSelMdPetIntegFuncionalid = MdPetIntegFuncionalidINT::montarSelectNomeNaoUtilizado('null', '&nbsp;', $objMdPetIntegracaoDTO->getNumIdMdPetIntegFuncionalid());
        $staUtilizarWsNao = "";
        $staUtilizarWsSim = "";
        $staTpClienteWs = "";
    }

    // Aqui são listados tanto parâmetros da integração de CNPJ quanto da CPF:

    $arrParametrosEntradas = array(
        'cnpjEmpresa' => 'CNPJ da Pessoa Jurídica',
        'cpfPessoa' => 'CPF da Pessoa Física',
        'cpfUsuario' => 'CPF Usuário',
        'identificacaoOrigem' => 'Identificação Origem',
        'periodoCache' => 'Período de Expiração do Cache',
    );

    $arrParametrosEntradaObrig = [
        'cnpjEmpresa',
        'cpfPessoa',
        'periodoCache',
    ];

    $arrParametrosSaida = array(
        'cnpjEmpresa' => 'CNPJ da Pessoa Jurídica',
        'cpfPessoa' => 'CPF da Pessoa Física',
        'cpfUsuario' => 'CPF Usuário',
        'razaoSocial' => 'Razão Social',
        'codSituacaoCadastral' => 'Código da Situação Cadastral',
        'descSituacaoCadastral' => 'Descrição da Situação Cadastral',
        'dtUltAltSituacaoCadastral' => 'Data da Última Alteração da Situação Cadastral',
        'tpLogradouro' => 'Tipo Logradouro do Endereço',
        'logradouro' => 'Logradouro do Endereço',
        'numero' => 'Número do Endereço',
        'complemento' => 'Complemento do Endereço',
        'cep' => 'CEP do Endereço',
        'bairro' => 'Bairro do Endereço',
        'codIbgeMunicipio' => 'Código IBGE do Município do Endereço',
        'cpfRespLegal' => 'CPF do Responsável Legal',
        'nomeRespLegal' => 'Nome do Responsável Legal',
    );
    
    $arrParametrosSaidaObrig = [
        'cnpjEmpresa',
        'codSituacaoCadastral',
        'descSituacaoCadastral',
        'cpfRespLegal',
        'nomeRespLegal',
    ];

    $strSumarioTabelaEntrada = 'Tabela de configuração dos dados de entrada do web-service.';
    $strCaptionTabelaEntrada = 'Dados de entrada';

    $strResultadoParamEntrada .= '<table width="100%" id="tableParametroEntrada" class="infraTable" summary="' . $strSumarioTabelaEntrada . '">' . "\n";
    $strResultadoParamEntrada .= '<tr>';
    $strResultadoParamEntrada .= '<th class="infraTh" width="50%">&nbsp;Campo de Origem no SEI&nbsp;</th>' . "\n";
    $strResultadoParamEntrada .= '<th class="infraTh" width="50%">&nbsp;Dados de Entrada no Webservice&nbsp;</th>' . "\n";
    $strResultadoParamEntrada .= '</tr>' . "\n";

    $strCssTr = '';
    $i = 0;

    foreach ($arrParametrosEntradas as $chave => $itemParametroEntrada) {

        $idLinha = $i;
        $strCssTr = '<tr id="paramEntradaTable_' . $chave . '" class="infraTrClara">';

        $strResultadoParamEntrada .= $strCssTr;
        $strResultadoParamEntrada .= "<td id='campo_{$chave}'  style='padding: 8px;' >";
        $strResultadoParamEntrada .= "<input type='hidden' name='hdnArrayDadosEntrada[" . $chave . "]' value='" . $itemParametroEntrada . "' />";
        $strResultadoParamEntrada .= '<span style="font-weight:'.(in_array($chave, $arrParametrosEntradaObrig) ? 'bold' : 'normal' ).'">'.PaginaSEI::tratarHTML($itemParametroEntrada).'</span>';
        $strResultadoParamEntrada .= "</td>";
        $strResultadoParamEntrada .= "<td align='left'  style='padding: 8px;' >";

        if ($chave == 'periodoCache') {
            $strResultadoParamEntrada .= "<select id='selCachePrazoExpiracao' style='width:52%; float: left' name='selCachePrazoExpiracao' class='infraSelect form-control' tabindex='" . PaginaSEI::getInstance()->getProxTabDados() . "' ".(in_array($chave, $arrParametrosEntradaObrig) ? 'data-obrigatorio="true"' : '' )."></select> <img src='" . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . "/ajuda.svg' name='ajuda' " . PaginaSEI::montarTitleTooltip("Selecione o campo de entrada da Operação que define o Prazo de Expiração do Cache das informações da Receita Federal.", 'Ajuda') . " alt='Ajuda' style='margin-left: 0% !important; margin-right: 3%' class='infraImgModulo' />";
            $strResultadoParamEntrada .= "<input type='text' id='txtPrazo' style='width:25%;' name='txtPrazo' class='infraText' value='" . $strItensSelCacheDataArmazenamento . "' onkeypress='return infraMascaraNumero(this,event,2);' maxlength='30' tabindex='" . PaginaSEI::getInstance()->getProxTabDados() . "'/><img src='" . PaginaSEI::getInstance()->getDiretorioSvgGlobal() . "/ajuda.svg' name='ajuda' " . PaginaSEI::montarTitleTooltip('Defina a quantidade de meses que o SEI deve considerar as informações em cache atualizadas. Se atribuído valor igual a 0 (zero), o SEI irá ignorar o cache e obterá as informações direto da Receita Federal.', 'Ajuda') . " alt='Ajuda' class='infraImgModulo' />";
        } else {
            $strResultadoParamEntrada .= "<select id='nomeFuncionalDadosEntrada_$chave' class='infraSelect selParametrosS form-control' name='nomeFuncionalDadosEntrada[$chave]' ".(in_array($chave, $arrParametrosEntradaObrig) ? 'data-obrigatorio="true"' : '' )."></select>";
        }

        $strResultadoParamEntrada .= '</td>';
        $strResultadoParamEntrada .= '</tr>' . "\n";
        $i++;

    }

    $strResultadoParamEntrada .= '</table>';

    $strSumarioTabelaSaida = 'Tabela de configuração dos dados de Saida do web-service.';
    $strCaptionTabelaSaida = 'Dados de Saida';

    $strResultadoParamSaida .= '<table width="100%" id="tableParametroSaida" class="infraTable" summary="' . $strSumarioTabelaSaida . '">' . "\n";
    $strResultadoParamSaida .= '<tr>';

    $strResultadoParamSaida .= '<th class="infraTh" width="50%">&nbsp;Campo de Destino no SEI &nbsp;</th>' . "\n";
    $strResultadoParamSaida .= '<th class="infraTh" width="50%">&nbsp;Dados de Saida no Webservice&nbsp;</th>' . "\n";
    $strResultadoParamSaida .= '</tr>' . "\n";
    $strCssTr = '';
    $i = 0;
    $tag = '';
    $mostrar = '';

    foreach ($arrParametrosSaida as $chave => $itemParametroSaida) {

        switch ($chave) {
            case 'tpLogradouro': $mostrar = $tpLogradouro == "" ? "display: none" : ""; break;
            case 'numero': $mostrar = $nuLogradouro == "" ? "display: none" : ""; break;
            case 'complemento': $mostrar = $compLogradouro == "" ? "display: none" : ""; break;
            default:  $mostrar = "";
        }

        $idLinha = $i;
        $strCssTr = '<tr id="paramSaidaTable_' . $chave . '" class="infraTrClara" style="' . $mostrar . '">';

        $strResultadoParamSaida .= $strCssTr;
        $strResultadoParamSaida .= "<td id='campo_{$chave}' style='padding: 8px;' >";
        $strResultadoParamSaida .= "<input type='hidden' name='hdnArrayDadosSaida[" . $chave . "]' value='" . $itemParametroSaida . "' />";
        $strResultadoParamSaida .= '<span style="font-weight:'.(in_array($chave, $arrParametrosSaidaObrig) ? 'bold' : 'normal' ).'">'.PaginaSEI::tratarHTML($itemParametroSaida).'</span>';
        $strResultadoParamSaida .= "</td>";
        $strResultadoParamSaida .= "<td align='left' style='padding: 8px;' ><select id='nomeFuncionalDadosSaida_$chave' class='infraSelect selParametrosS  form-control' name='nomeFuncionalDadosSaida[$chave]' ".(in_array($chave, $arrParametrosSaidaObrig) ? 'data-obrigatorio="true"' : '' )."></select></td>";

        $strResultadoParamSaida .= '</tr>' . "\n";
        $i++;

    }

    $strResultadoParamSaida .= '</table>';

} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo);
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
require_once "md_pet_integracao_cadastro_css.php";
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
    <form id="frmMdPetIntegracaoCadastro" method="post" onsubmit="return OnSubmitForm();"
          action="<?= SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao']) ?>">
        <?
        PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
        PaginaSEI::getInstance()->abrirAreaDados('auto');
        ?>

        <div class="row">
            <div class="col-12 col-xl-10">
                <div class="form-group">
                    <label class="infraLabelObrigatorio" for="selMdPetIntegFuncionalid" id="lblMdPetIntegFuncionalid">Funcionalidade:</label>
                    <select id="selMdPetIntegFuncionalid" name="selMdPetIntegFuncionalid" class="infraSelect form-control"
                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                        <?= $strItensSelMdPetIntegFuncionalid ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="row initHidden" id="blcUsarIntegracaoWs" style="display: none">
            <div class="col-12 col-xl-10">
                <div class="form-group">
                    <label class="infraLabelObrigatorio" for="txtNome" id="lblNome">Nome:</label>
                    <input type="text" id="txtNome" name="txtNome" class="infraText form-control"
                           value="<?= PaginaSEI::tratarHTML($objMdPetIntegracaoDTO->getStrNome()); ?>"
                           onkeypress="return infraMascaraTexto(this,event,30);" maxlength="30"
                           tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                </div>
            </div>
            <div class="col-12 col-xl-10">
                <fieldset class="infraFieldset fieldSetIntegracao form-control">
                    <legend class="infraLegend">&nbsp;Indicação de Integração com a Receita Federal&nbsp; <img
                                id="imgAjuda2"
                                src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif"
                                name="ajuda" <?= PaginaSEI::montarTitleTooltip('É extremamente recomendado que se utilize Integração com a base de dados da Receita Federal para validar se o CPF do Usuário Externo que está formalizando a vinculação como Responsável Legal de Pessoa Jurídica é de fato do Responsável Legal pelo CNPJ constante na Receita Federal. \n \n Caso opte por ativar as funcionalidades afetas a Pessoa Jurídica e Procuração Eletrônica para os Usuários Externos Sem Integração com a base da Receita Federal, os Usuários Externos continuarão a declarar a responsabilidade, até penal, sobre as informações prestadas, mas poderão ocorrer contradição e, caso necessite, Suspensão e Alteração da vinculação podem ser efetivadas pelo menu Administração > Peticionamento Eletrônico > Vinculações e Procurações Eletrônicas.', 'Ajuda') ?>
                                alt="Ajuda" class="infraImgFielset"/></legend>
                    <div class="form-group">
                        <span>
                            <input <?php echo $staUtilizarWsNao; ?> type="radio" name="rdStaUtilizarWs" class="infraRadio"
                                                                    id="rdStaUtilizarWsNao" value="N"
                                                                    onclick="habilitaWs()">
                            <label for="rdStaUtilizarWsNao" id="lblStaUtilizarWsNao" class="infraLabelRadio">Sem Integração
                                <img id="imgAjuda3"
                                     src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                     name="ajuda" <?= PaginaSEI::montarTitleTooltip('Ao selecionar esta opção, não ocorrerá qualquer validação se o CPF do Usuário Externo que está formalizando a vinculação como Responsável Legal de Pessoa Jurídica é de fato do Responsável Legal pelo CNPJ constante na Receita Federal, ficando exclusivamente sob responsabilidade, até penal, da auto declaração efetivada pelo Usuário Externo e documentos que anexar no Peticionamento de formalização.', 'Ajuda') ?>
                                    alt="Ajuda" class="infraImgModulo"/></label>
                        </span>
                        <span>
                            <input <?php echo $staUtilizarWsSim; ?> type="radio" name="rdStaUtilizarWs" class="infraRadio"
                                                                    id="rdStaUtilizarWsSim" value="S"
                                                                    onclick="habilitaWs()">
                            <label name="rdStaUtilizarWsSim" id="lblStaUtilizarWsSim" for="rdStaUtilizarWsSim"
                               class="infraLabelRadio">Com Integração
                            <img id="imgAjuda4"
                                 src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                 name="ajuda" <?= PaginaSEI::montarTitleTooltip('Ao selecionar esta opção, o CPF do Usuário Externo que está formalizando a vinculação como Responsável Legal de Pessoa Jurídica será validado por integração configurada abaixo se é de fato do Responsável Legal pelo CNPJ constante na Receita Federal. \n \n Se não ocorrer a validação o Usuário Externo não poderá prosseguir com o Peticionamento inicial de Responsável Legal de Pessoa Jurídica.', 'Ajuda') ?>
                                alt="Ajuda" class="infraImgModulo"/></label>
                        </span>
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="row initHidden" id="blcTextoSemWs" style="display: none;">
            <div class="col-12 col-xl-10">
                <p style="font-size: 12px; padding-top: 10px">
                    <span STYLE="color: red; font-weight: bold">ATENÇAO</span>: É extremamente recomendado que se
                    utilize Integração com a base de dados da Receita Federal para validar se o CPF do Usuário Externo
                    que está formalizando a vinculação como Responsável Legal de Pessoa Jurídica é de fato do
                    Responsável Legal pelo CNPJ constante na Receita Federal.<br/>
                    <br/>
                    Caso opte por ativar as funcionalidades afetas a Pessoa Jurídica e Procuração Eletrônica para os
                    Usuários Externos Sem Integração com a base da Receita Federal, não ocorrerá qualquer validação se o
                    CPF do Usuário Externo que está formalizando a vinculação como Responsável Legal de Pessoa Jurídica
                    é de fato do Responsável Legal pelo CNPJ constante na Receita Federal, ficando exclusivamente sob
                    responsabilidade, até penal, da auto declaração efetivada pelo Usuário Externo e documentos que
                    anexar no Peticionamento de formalização.<br/>
                    <br/>
                    Ao selecionar a opção Sem Integração, contradições podem ocorrer e, caso necessite, Suspensão e
                    Alteração da vinculação podem ser efetivadas pelo menu Administração > Peticionamento Eletrônico >
                    Vinculações e Procurações Eletrônicas.
                </p>
            </div>
        </div>
        <div class="row initHidden" id="blcTipoClienteWs"  style="display: none;">
           <div class="col-12">
               <div class="row">
                   <div class="col-6 col-xl-5">
                       <div class="form-group">
                           <label id="lblStaTpClienteWs" for="txtStaTpClienteWs" class="infraLabelObrigatorio">Tipo Cliente WS:</label><br/>
                           <input <?php echo $staTpClienteWs; ?> type="radio" name="rdStaTpClienteWs" id="rdStaTpClienteWsSoap" class="infraRadio" value="S">
                           <label for="rdStaTpClienteWsSoap" id="lblStaTpClienteWsSoap" class="infraLabelRadio">SOAP</label>
                       </div>
                   </div>
                   <div class="col-6 col-xl-5">
                       <div class="form-group">
                           <label id="lblNuVersao" for="txtNuVersao" class="infraLabelObrigatorio">Versão SOAP:</label>
                           <select id="selNuVersao" name="selNuVersao" class="infraSelect form-control" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                               <option value=""></option>
                               <option value="1.1" <?php echo ($numNuVersao == "1,1") ? 'selected' : ''; ?> >1.1</option>
                               <option value="1.2" <?php echo ($numNuVersao == "1,2") ? 'selected' : ''; ?> >1.2</option>
                           </select>
                       </div>
                   </div>
               </div>
           </div>
        </div>
        <div class="row initHidden" id="blcEnderecoWs"  style="display: none;">
            <div class="col-12 col-xl-10">
                <div class="form-group">
                    <label id="lblEnderecoWsdl" for="txtEnderecoWsdl" class="infraLabelObrigatorio">Endereço do Webservice:</label>
                    <div class="input-group mb-3">
                        <input type="text" id="txtEnderecoWsdl" name="txtEnderecoWsdl" class="infraText form-control"
                            value="<?= PaginaSEI::tratarHTML($objMdPetIntegracaoDTO->getStrEnderecoWsdl()); ?>"
                            onkeypress="return infraMascaraTexto(this,event,250);" maxlength="250"
                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        <button type="button" accesskey="V" name="btnValidar" id="btnValidar" value="Validar" class="infraButton" onclick="validarWsdl();">
                            <span class="infraTeclaAtalho">V</span>alidar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row initHidden" id="blcOperacaoWs" style="display: none;">
            <div class="col-12 col-xl-10">
                <div class="form-group">
                    <label id="lblOperacaoWsdl" for="selOperacaoWsdl" class="infraLabelObrigatorio">Operação:</label>
                    <select id="selOperacaoWsdl" name="selOperacaoWsdl" onchange="operacaoSelecionar()" class="infraSelect form-control" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"></select>
                    <select id="selParametrosE" name="selParametrosE[]" multiple style="left:400px;display:none"></select>
                    <select id="selParametrosS" name="selParametrosS[]" multiple style="left:500px;display:none"></select>
                </div>
            </div>
        </div>
        <div class="row initHidden" id="blcParamsSuspensaoAutomatica" style="display: none;">
            <div class="col-12 col-xl-10">
                <div class="form-group">
                    <label id="lbltxtCodRFBSuspensaoAutomatica" for="txtCodRFBSuspensaoAutomatica" class="infraLabelObrigatorio">Códigos de Situação Cadastral que identifica Pessoas Físicas Inativas na Receita:
                        <img id="imgAjuda5" src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                             name="ajuda" <?= PaginaSEI::montarTitleTooltip('Lista de códigos numéricos que representam as situações na Receita Federal que irão implicar na desativação do Usuário Externo ativo e liberado. Os códigos precisar ser separados por ponto e vírgula ( ; ). \n\n\n Por exemplo, para a situação "Cancelada por Encerramento de Espólio" o webservice retorna o código "1" e a situação "Cancelada por Óbito sem Espolio" o webservice retorna o código "3". A lista nesse campo deve ser "1;3".', 'Ajuda') ?>
                             alt="Ajuda" class="infraImgModulo"/>
                    </label>
                    <input type="text" id="txtCodRFBSuspensaoAutomatica" name="txtCodRFBSuspensaoAutomatica" class="infraText form-control" value="<?= $codReceitaSuspAuto ?>"
                           maxlength="30" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                </div>
            </div>
        </div>
        <div class="row initHidden" id="blcCacheWs"  style="display: none;">
            <div class="col-12 col-xl-10">
                <div class="form-group" id="expiracacaoCache">
                    <input type="checkbox" id="chkSinCache" name="chkSinCache" onchange="cacheMarcaDesmarca(this);"
                        class="infraCheckbox" <?= PaginaSEI::getInstance()->setCheckbox($objMdPetIntegracaoDTO->getStrSinCache()) ?>
                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                    <label id="lblSinCache" for="chkSinCache" class="infraLabelCheckbox">Marque caso seu Webservice tenha
                        controle de expiração de cache</label>
                    <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" name="ajuda"
                        <?= PaginaSEI::montarTitleTooltip('Marque caso a operação selecionada acima utilize controle de expiração de cache das informações recuperadas da Receita Federal.', 'Ajuda') ?> alt="Ajuda"
                        class="infraImgModulo"/>
                </div>
                <div class="form-group" id="tipoLogradouro">
                    <input type="checkbox" id="chkSinTipo" name="chkSinTipo" onchange="tipoMarcaDesmarca(this);"
                        class="infraCheckbox" <?= PaginaSEI::getInstance()->setCheckbox($objMdPetIntegracaoDTO->getStrSinTpLogradouro()) ?>
                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                    <label id="lblSinTipo" for="chkSinTipo" class="infraLabelCheckbox">Marque caso seu Webservice tenha o
                        Tipo do Logradouro separado do próprio Logradouro</label>
                    <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" name="ajuda"
                        <?= PaginaSEI::montarTitleTooltip('Ao marcar esta opção, o Tipo de Logradouro será agrupado com o Logradouro. \n \nIsso é válido somente para Operações que possuem em sua estrutura as duas informações separadamente.') ?>alt="Ajuda"
                        class="infraImgModulo"/>
                </div>
                <div class="form-group" id="numeroLogradouro">
                    <input type="checkbox" id="chkSinNumero" name="chkSinNumero" onchange="numeroMarcaDesmarca(this);"
                        class="infraCheckbox" <?= PaginaSEI::getInstance()->setCheckbox($objMdPetIntegracaoDTO->getStrSinNuLogradouro()) ?>
                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                    <label id="lblSinNumero" for="chkSinNumero" class="infraLabelCheckbox">Marque caso seu Webservice tenha
                        o Número do Logradouro separado do próprio Logradouro</label>
                    <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" name="ajuda"
                        <?= PaginaSEI::montarTitleTooltip('Ao marcar esta opção, o Número de Logradouro será agrupado com o Logradouro. \n \nIsso é válido somente para Operações que possuem em sua estrutura as duas informações separadamente.') ?>alt="Ajuda"
                        class="infraImgModulo"/>
                </div>
                <div class="form-group" id="complementoLogradouro">
                    <input type="checkbox" id="chkSinComplemento" name="chkSinComplemento"
                        onchange="complementoMarcaDesmarca(this);"
                        class="infraCheckbox" <?= PaginaSEI::getInstance()->setCheckbox($objMdPetIntegracaoDTO->getStrSinCompLogradouro()) ?>
                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                    <label id="lblSinComplemento" for="chkSinComplemento" class="infraLabelCheckbox">Marque caso seu
                        Webservice tenha o Complemento do Logradouro separado do próprio Logradouro</label>
                    <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" name="ajuda"
                        <?= PaginaSEI::montarTitleTooltip('Ao marcar esta opção, o Complemento de Logradouro será agrupado com o Logradouro. \n \nIsso é válido somente para Operações que possuem em sua estrutura as duas informações separadamente.') ?>alt="Ajuda"
                        class="infraImgModulo"/>
                </div>
            </div>
        </div>
        <div class="row initHidden" id="blcEntradaWs"  style="display: none;">
            <div class="col-12 col-xl-10">
                <? PaginaSEI::getInstance()->montarAreaTabela($strResultadoParamEntrada, 1); ?>
            </div>
        </div>
        <div class="row initHidden" id="blcSaidaWs"  style="display: none;">
            <div class="col-12 col-xl-10">
                <? PaginaSEI::getInstance()->montarAreaTabela($strResultadoParamSaida, 1); ?>
            </div>
        </div>
        <div class="container initHidden"  style="display: none;">
            <fieldset style="display:none" id="fldParametrosCache" class="infraFieldset">
                <legend class="infraLegend">&nbsp;Parâmetros do Cache&nbsp;</legend>
                <div class="container">
                    <div class="bloco" style="display:none;">
                        <div class="form-group">
                            <label id="lblCacheDataArmazenamento" for="selCacheDataArmazenamento" class="infraLabelObrigatorio">Campo de Retorno da Data de Armazenamento: <img src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg" name="ajuda" <?= PaginaSEI::montarTitleTooltip('Selecione o campo da Operação que retorna a Data de Armazenamento do cache da informações da Receita Federal e que foi utilizado na validação do período de expiração definido nos campo abaixo.', 'Ajuda') ?>alt="Ajuda" class="infraImgModulo" /></label>
                            <select id="selCacheDataArmazenamento" style="width:300px;" name="selCacheDataArmazenamento" class="infraSelect form-control" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"></select>
                        </div>
                    </div>

                    <div class="clear">&nbsp;</div>

                    <div class="bloco">
                        <label id="lblCachePrazoExpiracao" for="selCachePrazoExpiracao" class="infraLabelObrigatorio">Campo de Entrada para Prazo de Expiração do Cache:</label>
                    </div>

                    <div class="bloco">
                        <label class="infraLabelObrigatorio" for="txtPrazo" id="lblPrazo">Parâmetro de Meses de Expiração: </label>
                    </div>

                    <div class="clear">&nbsp;</div>

                </div>
            </fieldset>
        </div>
    <?
    PaginaSEI::getInstance()->fecharAreaDados();
    ?>
    <input type="hidden" id="hdnIdMdPetIntegracao" name="hdnIdMdPetIntegracao" value="<?= $objMdPetIntegracaoDTO->getNumIdMdPetIntegracao(); ?>" />
    <input type="hidden" id="bolIsConsultar" value="<?= isset($bolIsConsultar) ? 'S' : 'N'?>">
</form>
<?
require_once "md_pet_integracao_cadastro_js.php";
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
