<?
/**
 * ANATEL
 *
 * Construi a tela de Cadastro, Alteração e Consulta de Tipos de Processos para Peticionamento
 * 15/04/2016 - criado por jaqueline.mendes@cast.com.br - CAST
 *
 */
try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    SessaoSEI::getInstance()->validarLink();

    PaginaSEI::getInstance()->verificarSelecao('tipo_processo_peticionamento_selecionar');

    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    $strDesabilitar = '';

    $arrComandos = array();

    //Tipo Processo - Nivel de Acesso
    $strLinkAjaxNivelAcesso = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_tipo_processo_nivel_acesso_auto_completar');

    //Tipo Documento Complementar
    $strLinkTipoDocumentoSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_serie_selecionar&filtro=1&tipo_selecao=2&id_object=objLupaTipoDocumento&tipoDoc=E');
    $strLinkAjaxTipoDocumento = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_serie_auto_completar');

    //Tipo de Documento Essencial
    $strLinkTipoDocumentoEssencialSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_serie_selecionar&filtro=1&tipo_selecao=2&id_object=objLupaTipoDocumentoEssencial&tipoDoc=E');

    //Tipo Processo
    $strLinkTipoProcessoSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=tipo_procedimento_selecionar&tipo_selecao=1&id_object=objLupaTipoProcesso');
    $strLinkAjaxTipoProcesso = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_tipo_processo_auto_completar');

    //Unidade
    $strLinkUnidadeSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=unidade_selecionar_todas&tipo_selecao=1&id_object=objLupaUnidade');
    $strLinkAjaxUnidade = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_unidade_auto_completar');

    //Orgao
    $strLinkOrgaoMultiplaSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=orgao_selecionar&tipo_selecao=1&id_object=objLupaOrgaoUnidadeMultipla');
    $strLinkAjaxOrgao = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=orgao_auto_completar');
    $strLinkAjaxConfirmaRestricao = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=confirma_restricao_tipo_processo');
    $strLinkAjaxConfirmaRestricaoSalvar = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=confirma_restricao_tipo_processo_salvar');

    //Tipo Documento Principal
    $strLinkTipoDocPrincExternoSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_serie_selecionar&filtro=1&tipoDoc=E&tipo_selecao=1&id_object=objLupaTipoDocPrinc');
    $strLinkTipoDocPrincGeradoSelecao = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_serie_selecionar&filtro=1&tipoDoc=G&tipo_selecao=1&id_object=objLupaTipoDocPrinc');
    $strLinkAjaxTipoDocPrinc = SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=md_pet_serie_auto_completar');

    //Preparar Preenchimento Alteração
    $idMdPetTipoProcesso = '';
    $nomeTipoProcesso = '';
    $idTipoProcesso = '';
    $orientacoes = '';
    $idUnidade = '';
    $nomeUnidade = '';
    $sinIndIntUsExt = '';
    $sinIndIntIndIndir = '';
    $sinIndIntIndConta = '';
    $sinIndIntIndCpfCn = '';
    $sinNAUsuExt = '';
    $sinNAPadrao = '';
    $hipoteseLegal = 'style="display:none;"';
    $gerado = '';
    $externo = '';
    $nomeSerie = '';
    $idSerie = '';
    $strItensSelSeries = '';
    $unica = false;
    $mutipla = false;
    $arrObjUnidadesMultiplas = array();
    $alterar = false;

    $strItensSelNivelAcesso = '';
    $strItensSelHipoteseLegal = '';

    //Preencher Array de Unidades para buscar posteriormente
    $objUnidadeDTO = new UnidadeDTO();
    $objUnidadeDTO = new UnidadeDTO();
    $objUnidadeDTO->retNumIdUnidade();
    //SEIV3 - agora iremos obter a UF pelo contato associado
    $objUnidadeDTO->retNumIdContato();
    $objUnidadeDTO->retStrSigla();
    $objUnidadeDTO->retStrDescricao();
    $objUnidadeDTO->retStrSiglaOrgao();
    $objUnidadeDTO->retNumIdOrgao();
    $objUnidadeDTO->retStrDescricaoOrgao();

    $objUnidadeRN = new UnidadeRN();

    $arrObjUnidadeDTO = $objUnidadeRN->listarTodasComFiltro($objUnidadeDTO);

    foreach ($arrObjUnidadeDTO as $key => $objUnidadeDTO) {

        $arrObjUnidadeDTOFormatado[$objUnidadeDTO->getNumIdUnidade()]['siglaUnidade'] = utf8_encode($objUnidadeDTO->getStrSigla());
        $arrObjUnidadeDTOFormatado[$objUnidadeDTO->getNumIdUnidade()]['descricaoUnidade'] = utf8_encode($objUnidadeDTO->getStrDescricao());
        $arrObjUnidadeDTOFormatado[$objUnidadeDTO->getNumIdUnidade()]['siglaOrgao'] = utf8_encode($objUnidadeDTO->getStrSiglaOrgao());
        $arrObjUnidadeDTOFormatado[$objUnidadeDTO->getNumIdUnidade()]['descricaoOrgao'] = utf8_encode($objUnidadeDTO->getStrDescricaoOrgao());
        $arrObjUnidadeDTOFormatado[$objUnidadeDTO->getNumIdUnidade()]['idOrgao'] = $objUnidadeDTO->getNumIdOrgao();

        $contatoAssociadoDTO = new ContatoDTO();
        $contatoAssociadoRN = new ContatoRN();
        $contatoAssociadoDTO->retStrSiglaUf();
        $contatoAssociadoDTO->retNumIdContato();
        $contatoAssociadoDTO->retStrNomeCidade();
        $contatoAssociadoDTO->retNumIdCidade();
        $contatoAssociadoDTO->setNumIdContato($objUnidadeDTO->getNumIdContato());
        $contatoAssociadoDTO = $contatoAssociadoRN->consultarRN0324($contatoAssociadoDTO);
        //so recuperar caso se trata de unidade que possua UF configurada]
        if ($contatoAssociadoDTO != null && $contatoAssociadoDTO->isSetStrSiglaUf() && $contatoAssociadoDTO->getStrSiglaUf() != null) {
            $arrObjUnidadeDTOFormatado[$objUnidadeDTO->getNumIdUnidade()]['uf'] = utf8_encode($contatoAssociadoDTO->getStrSiglaUf());
            $arrObjUnidadeDTOFormatado[$objUnidadeDTO->getNumIdUnidade()]['cidade'] = utf8_encode($contatoAssociadoDTO->getStrNomeCidade());
            $arrObjUnidadeDTOFormatado[$objUnidadeDTO->getNumIdUnidade()]['idCidade'] = $contatoAssociadoDTO->getNumIdCidade();
        }
    }

    $objInfraParametroDTO = new InfraParametroDTO();
    $objMdPetParametroRN = new MdPetParametroRN();
    $objInfraParametroDTO->retTodos();
    $objInfraParametroDTO->setStrNome('SEI_HABILITAR_HIPOTESE_LEGAL');
    $objInfraParametroDTO = $objMdPetParametroRN->consultar($objInfraParametroDTO);
    $valorParametroHipoteseLegal = $objInfraParametroDTO->getStrValor();

    //Campo de filtro Órgão
//    $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
//    $objMdPetTipoProcessoOrgaoDTO = new MdPetTipoProcessoDTO();
//    $objMdPetTipoProcessoOrgaoDTO->setDistinct(true);
//    $objMdPetTipoProcessoOrgaoDTO->retNumIdOrgaoUnidade();
//    $objMdPetTipoProcessoOrgaoDTO->retStrSiglaOrgaoUnidade();
//    $arrFiltroOrgao = $objMdPetTipoProcessoRN->listar($objMdPetTipoProcessoOrgaoDTO);

    $objOrgaoDTO = new OrgaoDTO();
    $objOrgaoRN = new OrgaoRN();
    $objOrgaoDTO->retNumIdOrgao();
    $objOrgaoDTO->setDistinct(true);
    $objOrgaoDTO->retStrSigla();
    $arrFiltroOrgao = $objOrgaoRN->listarRN1353($objOrgaoDTO);

    $numRegistrosOrgao = count($arrFiltroOrgao);

    if ($numRegistrosOrgao > 0) {
        foreach ($arrFiltroOrgao as $objOrgaoDTO) {
            $strHtmlOrgaoUnidades .= '<input type="hidden" id="hdnOrgao' . $objOrgaoDTO->getNumIdOrgao() . '" name="hdnOrgao' . $objOrgaoDTO->getNumIdOrgao() . '" value="' . $strValor . '" />' . "\n";
            $strHtmlOrgaoUnidades .= '<input type="hidden" id="lnkOrgao' . $objOrgaoDTO->getNumIdOrgao() . '" name="lnkOrgao' . $objOrgaoDTO->getNumIdOrgao() . '" value="' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=unidade_selecionar_orgao&tipo_selecao=1&id_object=objLupaUnidadeMultipla&id_orgao=' . $objOrgaoDTO->getNumIdOrgao()) . '" />' . "\n";
        }
    }

    if ($_GET['acao'] === 'md_pet_tipo_processo_consultar' || $_GET['acao'] === 'md_pet_tipo_processo_alterar') {

        if (isset($_GET['id_tipo_processo_peticionamento'])) {
            $alterar = true;
            $objMdPetTipoProcessoDTO = new MdPetTipoProcessoDTO();
            $objMdPetTipoProcessoDTO->setNumIdTipoProcessoPeticionamento($_GET['id_tipo_processo_peticionamento']);
            $objMdPetTipoProcessoDTO->retTodos();
            $objMdPetTipoProcessoDTO->retStrNomeProcesso();
            $objMdPetTipoProcessoDTO->retStrNomeSerie();

            $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
            $objMdPetTipoProcessoDTO = $objMdPetTipoProcessoRN->consultar($objMdPetTipoProcessoDTO);

            $idTipoProcedimento = $objMdPetTipoProcessoDTO->getNumIdProcedimento();
            $strItensSelHipoteseLegal = MdPetVincTpProcessoINT::montarSelectHipoteseLegal(null, null, $objMdPetTipoProcessoDTO->getNumIdHipoteseLegal());

            //Carregando Unidades
            $objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
            $objMdPetRelTpProcessoUnidDTO->retNumIdOrgaoUnidade();
            $objMdPetRelTpProcessoUnidDTO->retNumIdCidadeContato();
            $objMdPetRelTpProcessoUnidDTO->setOrd('SiglaOrgao', InfraDTO::$TIPO_ORDENACAO_ASC);
            $objMdPetRelTpProcessoUnidDTO->setOrd('siglaUnidade', InfraDTO::$TIPO_ORDENACAO_ASC);
            $objMdPetRelTpProcessoUnidDTO->setOrd('SiglaUf', InfraDTO::$TIPO_ORDENACAO_ASC);
            $objMdPetRelTpProcessoUnidDTO->setOrd('NomeCidade', InfraDTO::$TIPO_ORDENACAO_ASC);
            $objMdPetRelTpProcessoUnidDTO->setNumIdTipoProcessoPeticionamento($_GET['id_tipo_processo_peticionamento']);
            $objMdPetRelTpProcessoUnidDTO->retTodos();

            $objMdPetRelTpProcessoUnidRN = new MdPetRelTpProcessoUnidRN();
            $arrObjMdPetRelTpProcessoUnidDTO = $objMdPetRelTpProcessoUnidRN->listar($objMdPetRelTpProcessoUnidDTO);

            if (count($arrObjMdPetRelTpProcessoUnidDTO) > 0) {
                $unica = $arrObjMdPetRelTpProcessoUnidDTO[0]->getStrStaTipoUnidade() === MdPetTipoProcessoRN::$UNIDADE_UNICA ? true : false;
                $multipla = $arrObjMdPetRelTpProcessoUnidDTO[0]->getStrStaTipoUnidade() === MdPetTipoProcessoRN::$UNIDADES_MULTIPLAS ? true : false;

                $objUnidadeRN = new UnidadeRN();
                if ($unica) {
                    $idUnidade = $arrObjMdPetRelTpProcessoUnidDTO[0]->getNumIdUnidade();
                    $objUnidadeDTO = new UnidadeDTO();
                    $objUnidadeDTO->setNumIdUnidade($idUnidade);
                    $objUnidadeDTO->retTodos();
                    $objUnidadeDTO = $objUnidadeRN->consultarRN0125($objUnidadeDTO);
                    $nomeUnidade = $objUnidadeDTO->getStrSigla() . ' - ' . $objUnidadeDTO->getStrDescricao();
                    $arrObjUnidadesMultiplas[] = $objUnidadeDTO;

                    //Verifica se existe restrição para este tipo de processo
                    $objTipoProcedRestricaoRN = new TipoProcedRestricaoRN();
                    $objTipoProcedRestricaoDTO = new TipoProcedRestricaoDTO();
                    $objTipoProcedRestricaoDTO->retNumIdOrgao();
                    $objTipoProcedRestricaoDTO->retNumIdUnidade();
                    $objTipoProcedRestricaoDTO->setNumIdTipoProcedimento($idTipoProcedimento);
                    $arrObjTipoProcedRestricaoDTO = $objTipoProcedRestricaoRN->listar($objTipoProcedRestricaoDTO);
                    $idOrgaoRestricao = InfraArray::converterArrInfraDTO($arrObjTipoProcedRestricaoDTO, 'IdOrgao');
                    $idUnidadeRestricao = InfraArray::converterArrInfraDTO($arrObjTipoProcedRestricaoDTO, 'IdUnidade');
                    $tipoProcessoRestricaoErroUU = false;

                    foreach ($arrObjUnidadesMultiplas as $cadaObjUnidadeDTO) {
                        //Verifica se tem algum órgão diferente dos restritos, caso exista restrições para o tipo de processo
                        if (($idOrgaoRestricao && $idOrgaoRestricao[0] != null) && !in_array($cadaObjUnidadeDTO->getNumIdOrgao(), $idOrgaoRestricao)) {
                            $tipoProcessoRestricaoErroUU = true;
                        }
                        //Verifica se tem alguma unidade diferente dos restritos, caso exista restrições para o tipo de processo
                        if (($idUnidadeRestricao && $idUnidadeRestricao[0] != null) && !in_array($cadaObjUnidadeDTO->getNumIdUnidade(), $idUnidadeRestricao)) {
                            $tipoProcessoRestricaoErroUU = true;
                        }
                    }
                }

                if ($multipla) {
                    $arrTipoProcessoOrgaoCidade = array();
                    foreach ($arrObjMdPetRelTpProcessoUnidDTO as $objRelUnidade) {
                        $idUnidade = $objRelUnidade->getNumIdUnidade();
                        $objUnidadeDTO = new UnidadeDTO();
                        $objUnidadeDTO->setNumIdUnidade($idUnidade);
                        $objUnidadeDTO->retStrDescricaoOrgao();
                        $objUnidadeDTO->retStrSiglaOrgao();
                        $objUnidadeDTO->retNumIdOrgao();
                        $objUnidadeDTO->retNumIdCidadeContato();
                        $objUnidadeDTO->retTodos();
                        $objUnidadeDTO = $objUnidadeRN->consultarRN0125($objUnidadeDTO);
                        $arrObjUnidadesMultiplas[] = $objUnidadeDTO;

                        //Criação do array para confirmar se existe para tipo de processo unidades com o mesmo orgao e cidade
                        if (!key_exists($objRelUnidade->getNumIdOrgaoUnidade(), $arrTipoProcessoOrgaoCidade)) {
                            $arrTipoProcessoOrgaoCidade[$objRelUnidade->getNumIdOrgaoUnidade()] = array();
                        }
                        if (!key_exists($objRelUnidade->getNumIdCidadeContato(), $arrTipoProcessoOrgaoCidade[$objRelUnidade->getNumIdOrgaoUnidade()])) {
                            $arrTipoProcessoOrgaoCidade[$objRelUnidade->getNumIdOrgaoUnidade()][$objRelUnidade->getNumIdCidadeContato()] = 1;
                        } else {
                            $arrTipoProcessoOrgaoCidade[$objRelUnidade->getNumIdOrgaoUnidade()][$objRelUnidade->getNumIdCidadeContato()] = $arrTipoProcessoOrgaoCidade[$objRelUnidade->getNumIdOrgaoUnidade()][$objRelUnidade->getNumIdCidadeContato()] + 1;
                        }
                    }
                }
            }

            $idMdPetTipoProcesso = $_GET['id_tipo_processo_peticionamento'];
            $nomeTipoProcesso = $objMdPetTipoProcessoDTO->getStrNomeProcesso();
            $idTipoProcesso = $objMdPetTipoProcessoDTO->getNumIdProcedimento();
            $orientacoes = $objMdPetTipoProcessoDTO->getStrOrientacoes();
            $idUnidade = $unica ? $arrObjMdPetRelTpProcessoUnidDTO[0]->getNumIdUnidade() : null;
            $sinIndIntUsExt = $objMdPetTipoProcessoDTO->getStrSinIIProprioUsuarioExterno() == 'S' ? 'checked = checked' : '';
            $sinIndIntIndIndir = $objMdPetTipoProcessoDTO->getStrSinIIIndicacaoDireta() == 'S' ? 'checked = checked' : '';
            $sinIndIntIndConta = $objMdPetTipoProcessoDTO->getStrSinIIIndicacaoDiretaContato() == 'S' ? 'checked = checked' : '';
            $sinIndIntIndCpfCn = $objMdPetTipoProcessoDTO->getStrSinIIIndicacaoDiretaCpfCnpj() == 'S' ? 'checked = checked' : '';
            $sinNAUsuExt = $objMdPetTipoProcessoDTO->getStrSinNaUsuarioExterno() == 'S' ? 'checked = checked' : '';
            $sinNAPadrao = $objMdPetTipoProcessoDTO->getStrSinNaPadrao() == 'S' ? 'checked = checked' : '';
            $gerado = $objMdPetTipoProcessoDTO->getStrSinDocGerado() == 'S' ? 'checked = checked' : '';
            $externo = $objMdPetTipoProcessoDTO->getStrSinDocExterno() == 'S' ? 'checked = checked' : '';
            $nomeSerie = $objMdPetTipoProcessoDTO->getStrNomeSerie();
            $idSerie = $objMdPetTipoProcessoDTO->getNumIdSerie();

            $hipoteseLegal = $objMdPetTipoProcessoDTO->getStrStaNivelAcesso() === ProtocoloRN::$NA_RESTRITO && $valorParametroHipoteseLegal != '0' ? 'style="display:inherit"' : 'style="display:none"';

            $strItensSelNivelAcesso = MdPetTipoProcessoINT::montarSelectNivelAcesso(null, null, $objMdPetTipoProcessoDTO->getStrStaNivelAcesso(), $idTipoProcesso);

            $objRelTipoProcessoSerieRN = new MdPetRelTpProcSerieRN();

            $objMdPetRelTpProcSerieDTO = new MdPetRelTpProcSerieDTO();
            $objMdPetRelTpProcSerieDTO->retTodos();
            $objMdPetRelTpProcSerieDTO->retStrNomeSerie();
            $objMdPetRelTpProcSerieDTO->setStrStaTipoDoc(MdPetRelTpProcSerieRN::$DOC_COMPLEMENTAR);
            $objMdPetRelTpProcSerieDTO->setNumIdTipoProcessoPeticionamento($_GET['id_tipo_processo_peticionamento']);
            $objMdPetRelTpProcSerieDTO->setOrdStrNomeSerie(InfraDTO::$TIPO_ORDENACAO_ASC);

            $arrSeries = $objRelTipoProcessoSerieRN->listar($objMdPetRelTpProcSerieDTO);
            $objMdPetTipoProcessoDTO->setArrObjRelTipoProcessoSerieDTO($arrSeries);

            $strItensSelSeries = "";
            for ($x = 0; $x < count($arrSeries); $x++) {
                $strItensSelSeries .= "<option value='" . $arrSeries[$x]->getNumIdSerie() . "'>" . $arrSeries[$x]->getStrNomeSerie() . "</option>";
            }

            //documento essencial 
            $objMdPetRelTpProcSerieEssDTO = new MdPetRelTpProcSerieDTO();
            $objMdPetRelTpProcSerieEssDTO->retTodos();
            $objMdPetRelTpProcSerieEssDTO->retStrNomeSerie();
            $objMdPetRelTpProcSerieEssDTO->setStrStaTipoDoc(MdPetRelTpProcSerieRN::$DOC_ESSENCIAL);
            $objMdPetRelTpProcSerieEssDTO->setNumIdTipoProcessoPeticionamento($_GET['id_tipo_processo_peticionamento']);
            $objMdPetRelTpProcSerieEssDTO->setOrdStrNomeSerie(InfraDTO::$TIPO_ORDENACAO_ASC);

            $arrSeriesEss = $objRelTipoProcessoSerieRN->listar($objMdPetRelTpProcSerieEssDTO);
            $objMdPetTipoProcessoDTO->setArrObjRelTipoProcessoSerieEssDTO($arrSeriesEss);

            $strItensSelSeriesEss = "";
            for ($x = 0; $x < count($arrSeriesEss); $x++) {
                $strItensSelSeriesEss .= "<option value='" . $arrSeriesEss[$x]->getNumIdSerie() . "'>" . $arrSeriesEss[$x]->getStrNomeSerie() . "</option>";
            }
        }
    }

    switch ($_GET['acao']) {

        case 'md_pet_tipo_processo_cadastrar':

            $strItensSelHipoteseLegal = MdPetVincTpProcessoINT::montarSelectHipoteseLegal(null, null, null);

            //Carregando campos select
            $strItensSelTipoProcesso = MdPetTipoProcessoINT::montarSelectTipoProcesso(null, null, $_POST['selTipoProcesso']);
            $strItensSelUnidades = UnidadeINT::montarSelectSiglaDescricao(null, null, $_POST['selUnidade']);

            $strItensSelDoc = SerieINT::montarSelectNomeRI0802(null, null, $_POST['selDocumento']);

            $objMdPetTipoProcessoDTO = new MdPetTipoProcessoDTO();
            $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();

            $strTitulo = 'Novo Tipo de Processo para Peticionamento de Processo Novo';

            $arrComandos[] = '<button type="submit" accesskey="s" name="sbmCadastrarTpProcessoPeticionamento" id="sbmCadastrarTpProcessoPeticionamento" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'])) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            $objMdPetTipoProcessoDTO->setNumIdProcedimento($_POST['hdnIdTipoProcesso']);
            $objMdPetTipoProcessoDTO->setStrOrientacoes($_POST['txtOrientacoes']);

            $objMdPetTipoProcessoDTO->setStrSinIIProprioUsuarioExterno('N');
            $objMdPetTipoProcessoDTO->setStrSinIIIndicacaoDireta('N');
            $objMdPetTipoProcessoDTO->setStrSinIIIndicacaoDiretaCpfCnpj('N');
            $objMdPetTipoProcessoDTO->setStrSinIIIndicacaoDiretaContato('N');
            $objMdPetTipoProcessoDTO->setStrSinNaUsuarioExterno('N');
            $objMdPetTipoProcessoDTO->setStrSinNaPadrao('N');
            $objMdPetTipoProcessoDTO->setStrSinDocGerado('N');
            $objMdPetTipoProcessoDTO->setStrSinDocExterno('N');

            // indicacao interessado
            if ($_POST['indicacaoInteressado'][0] == 1)
                $objMdPetTipoProcessoDTO->setStrSinIIProprioUsuarioExterno('S');
            if ($_POST['indicacaoInteressado'][0] == 2)
                $objMdPetTipoProcessoDTO->setStrSinIIIndicacaoDireta('S');
            if ($_POST['indicacaoIndireta'][0] == 3)
                $objMdPetTipoProcessoDTO->setStrSinIIIndicacaoDiretaCpfCnpj('S');
            if ($_POST['indicacaoIndireta'][0] == 4)
                $objMdPetTipoProcessoDTO->setStrSinIIIndicacaoDiretaContato('S');

            // nivel de acesso
            if ($_POST['rdNivelAcesso'][0] == 1)
                $objMdPetTipoProcessoDTO->setStrSinNaUsuarioExterno('S');
            if ($_POST['rdNivelAcesso'][0] == 2)
                $objMdPetTipoProcessoDTO->setStrSinNaPadrao('S');

            $objMdPetTipoProcessoDTO->setNumIdHipoteseLegal(null);

            if ($_POST['selNivelAcesso'] != '') {

                $objMdPetTipoProcessoDTO->setStrStaNivelAcesso($_POST['selNivelAcesso']);

                if ($_POST['selNivelAcesso'] == ProtocoloRN::$NA_RESTRITO && $valorParametroHipoteseLegal != '0') {
                    $objMdPetTipoProcessoDTO->setNumIdHipoteseLegal($_POST['selHipoteseLegal']);
                } else {
                    $objMdPetTipoProcessoDTO->setNumIdHipoteseLegal(null);
                }
            }

            //Tipo de Documento Principal
            if ($_POST['rdDocPrincipal'][0] == 1) { // campos: modelo, tipo de documento principal
                $objMdPetTipoProcessoDTO->setStrSinDocGerado('S');
                $objMdPetTipoProcessoDTO->setStrSinDocExterno('N');
            } else if ($_POST['rdDocPrincipal'][0] == 2) { //campos: tipo de documento principal
                $objMdPetTipoProcessoDTO->setStrSinDocGerado('N');
                $objMdPetTipoProcessoDTO->setStrSinDocExterno('S');
            }

            $objMdPetTipoProcessoDTO->setNumIdSerie($_POST['hdnIdTipoDocPrinc']);
            $objMdPetTipoProcessoDTO->setStrSinAtivo('S');

            if (isset($_POST['sbmCadastrarTpProcessoPeticionamento'])) {
                try {
                    $arrIdTipoDocumento = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnSerie']);
                    $arrIdTipoDocumentoEssencial = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnSerieEssencial']);
                    $arrIdUnidadesSelecionadas = $_POST['hdnUnidadesSelecionadas'] != '' ? json_decode($_POST['hdnUnidadesSelecionadas']) : array();
                    //para nao limpar os campos em caso de erro de duplicidade
                    $tipoUnidade = is_array($_POST['rdUnidade']) ? current($_POST['rdUnidade']) : array();
                    $nomeTipoProcesso = $_POST['txtTipoProcesso'];
                    $idTipoProcesso = $objMdPetTipoProcessoDTO->getNumIdProcedimento();
                    $orientacoes = $objMdPetTipoProcessoDTO->getStrOrientacoes();
                    $nomeUnidade = $_POST['txtUnidade'];
                    $sinIndIntUsExt = $objMdPetTipoProcessoDTO->getStrSinIIProprioUsuarioExterno() == 'S' ? 'checked = checked' : '';
                    $sinIndIntIndIndir = $objMdPetTipoProcessoDTO->getStrSinIIIndicacaoDireta() == 'S' ? 'checked = checked' : '';
                    $sinIndIntIndConta = $objMdPetTipoProcessoDTO->getStrSinIIIndicacaoDiretaContato() == 'S' ? 'checked = checked' : '';
                    $sinIndIntIndCpfCn = $objMdPetTipoProcessoDTO->getStrSinIIIndicacaoDiretaCpfCnpj() == 'S' ? 'checked = checked' : '';
                    $sinNAUsuExt = $objMdPetTipoProcessoDTO->getStrSinNaUsuarioExterno() == 'S' ? 'checked = checked' : '';
                    $sinNAPadrao = $objMdPetTipoProcessoDTO->getStrSinNaPadrao() == 'S' ? 'checked = checked' : '';
                    $gerado = $objMdPetTipoProcessoDTO->getStrSinDocGerado() == 'S' ? 'checked = checked' : '';
                    $externo = $objMdPetTipoProcessoDTO->getStrSinDocExterno() == 'S' ? 'checked = checked' : '';
                    $nomeSerie = $_POST['txtTipoDocPrinc'];
                    $idSerie = $objMdPetTipoProcessoDTO->getNumIdSerie();
                    $multipla = $tipoUnidade == 'M' ? true : false;
                    $unica = $tipoUnidade == 'U' ? true : false;
                    $hdnCorpoTabela = isset($_POST['hdnCorpoTabela']) ? $_POST['hdnCorpoTabela'] : '';
                    $idUnidade = $unica ? $_POST['hdnIdUnidade'] : null;
                    $numTipoProcessoPeticionamento = $objMdPetTipoProcessoRN->cadastrar($objMdPetTipoProcessoDTO)->getNumIdTipoProcessoPeticionamento();

                    $objMdPetRelTpProcSerieRN = new MdPetRelTpProcSerieRN();

                    //Tipo de Documento Essencial
                    foreach ($arrIdTipoDocumentoEssencial as $numIdTipoDocumentoEss) {
                        $objMdPetRelTpProcSerieEssDTO = new MdPetRelTpProcSerieDTO();

                        $objMdPetRelTpProcSerieEssDTO->setNumIdTipoProcessoPeticionamento($numTipoProcessoPeticionamento);
                        $objMdPetRelTpProcSerieEssDTO->setNumIdSerie($numIdTipoDocumentoEss);
                        $objMdPetRelTpProcSerieEssDTO->setStrStaTipoDoc(MdPetRelTpProcSerieRN::$DOC_ESSENCIAL);

                        $objRelTipoProcSerieEssPetDTO = $objMdPetRelTpProcSerieRN->cadastrar($objMdPetRelTpProcSerieEssDTO);
                    }

                    //Tipo de Documento Complementar
                    foreach ($arrIdTipoDocumento as $numIdTipoDocumento) {
                        $objMdPetRelTpProcSerieDTO = new MdPetRelTpProcSerieDTO();

                        $objMdPetRelTpProcSerieDTO->setNumIdTipoProcessoPeticionamento($numTipoProcessoPeticionamento);
                        $objMdPetRelTpProcSerieDTO->setNumIdSerie($numIdTipoDocumento);
                        $objMdPetRelTpProcSerieDTO->setStrStaTipoDoc(MdPetRelTpProcSerieRN::$DOC_COMPLEMENTAR);

                        $objRelTipoProcSeriePetDTO = $objMdPetRelTpProcSerieRN->cadastrar($objMdPetRelTpProcSerieDTO);
                    }

                    //Unidade
                    $objMdPetRelTpProcessoUnidRN = new MdPetRelTpProcessoUnidRN();


                    if ($tipoUnidade === MdPetTipoProcessoRN::$UNIDADES_MULTIPLAS) {
                        foreach ($arrIdUnidadesSelecionadas as $idUnidadeSelecionada) {
                            $objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();

                            $objMdPetRelTpProcessoUnidDTO->setNumIdTipoProcessoPeticionamento($numTipoProcessoPeticionamento);
                            $objMdPetRelTpProcessoUnidDTO->setNumIdUnidade($idUnidadeSelecionada);
                            $objMdPetRelTpProcessoUnidDTO->setStrStaTipoUnidade(MdPetTipoProcessoRN::$UNIDADES_MULTIPLAS);
                            $objMdPetRelTpProcessoUnidRN->cadastrar($objMdPetRelTpProcessoUnidDTO);
                        }
                    } else {
                        $objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
                        $objMdPetRelTpProcessoUnidDTO->setNumIdTipoProcessoPeticionamento($numTipoProcessoPeticionamento);
                        $objMdPetRelTpProcessoUnidDTO->setNumIdUnidade($_POST['hdnIdUnidade']);
                        $objMdPetRelTpProcessoUnidDTO->setStrStaTipoUnidade(MdPetTipoProcessoRN::$UNIDADE_UNICA);

                        $objMdPetRelTpProcessoUnidRN->cadastrar($objMdPetRelTpProcessoUnidDTO);
                    }

                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . '&id_tipo_processo_peticionamento=' . $objMdPetTipoProcessoDTO->getNumIdTipoProcessoPeticionamento() . PaginaSEI::getInstance()->montarAncora($objMdPetTipoProcessoDTO->getNumIdTipoProcessoPeticionamento())));
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }
            break;

        case 'md_pet_tipo_processo_alterar':
            $strTitulo = 'Alterar Tipo de Processo para Peticionamento de Processo Novo';
            $strDesabilitar = 'disabled="disabled"';

            $objMdPetTipoProcessoDTO = new MdPetTipoProcessoDTO();
            $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();

            $strItensSelTipoProcesso = MdPetTipoProcessoINT::montarSelectTipoProcesso(null, null, $_POST['selTipoProcesso']);
            $strItensSelUnidades = UnidadeINT::montarSelectSiglaDescricao(null, null, $_POST['selUnidade']);
            $strItensSelDoc = SerieINT::montarSelectNomeRI0802(null, null, $_POST['selDocumento']);

            $arrComandos[] = '<button type="submit" accesskey="s" name="sbmAlterarTipoPeticionamento" id="sbmAlterarTipoPeticionamento" value="Salvar" class="infraButton"><span class="infraTeclaAtalho">S</span>alvar</button>';
            $arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($_GET['id_tipo_processo_peticionamento']))) . '\';" class="infraButton"><span class="infraTeclaAtalho">C</span>ancelar</button>';

            $objMdPetTipoProcessoDTO->setNumIdTipoProcessoPeticionamento($_POST['hdnIdMdPetTipoProcesso']);
            $objMdPetTipoProcessoDTO->setNumIdProcedimento($_POST['hdnIdTipoProcesso']);
            $objMdPetTipoProcessoDTO->setStrOrientacoes($_POST['txtOrientacoes']);

            $objMdPetTipoProcessoDTO->setStrSinIIProprioUsuarioExterno('N');
            $objMdPetTipoProcessoDTO->setStrSinIIIndicacaoDireta('N');
            $objMdPetTipoProcessoDTO->setStrSinIIIndicacaoDiretaCpfCnpj('N');
            $objMdPetTipoProcessoDTO->setStrSinIIIndicacaoDiretaContato('N');
            $objMdPetTipoProcessoDTO->setStrSinNaUsuarioExterno('N');
            $objMdPetTipoProcessoDTO->setStrSinNaPadrao('N');
            $objMdPetTipoProcessoDTO->setStrSinDocGerado('N');
            $objMdPetTipoProcessoDTO->setStrSinDocExterno('N');

            // indicacao interessado
            if ($_POST['indicacaoInteressado'][0] == 1)
                $objMdPetTipoProcessoDTO->setStrSinIIProprioUsuarioExterno('S');
            if ($_POST['indicacaoInteressado'][0] == 2)
                $objMdPetTipoProcessoDTO->setStrSinIIIndicacaoDireta('S');
            if ($_POST['indicacaoIndireta'][0] == 3)
                $objMdPetTipoProcessoDTO->setStrSinIIIndicacaoDiretaCpfCnpj('S');
            if ($_POST['indicacaoIndireta'][0] == 4)
                $objMdPetTipoProcessoDTO->setStrSinIIIndicacaoDiretaContato('S');

            // nivel de acesso
            if ($_POST['rdNivelAcesso'][0] == 1)
                $objMdPetTipoProcessoDTO->setStrSinNaUsuarioExterno('S');
            if ($_POST['rdNivelAcesso'][0] == 2)
                $objMdPetTipoProcessoDTO->setStrSinNaPadrao('S');
            if ($_POST['selNivelAcesso'] != '') {
                $objMdPetTipoProcessoDTO->setStrStaNivelAcesso($_POST['selNivelAcesso']);
            }
            //documento principal
            if ($_POST['rdDocPrincipal'][0] == 1) {
                $objMdPetTipoProcessoDTO->setStrSinDocGerado('S');
                $objMdPetTipoProcessoDTO->setStrSinDocExterno('N');
            } else if ($_POST['rdDocPrincipal'][0] == 2) {
                $objMdPetTipoProcessoDTO->setStrSinDocGerado('N');
                $objMdPetTipoProcessoDTO->setStrSinDocExterno('S');
            }

            $objMdPetTipoProcessoDTO->setNumIdSerie($_POST['hdnIdTipoDocPrinc']);

            $objMdPetTipoProcessoDTO->setStrSinAtivo('S');


            if (isset($_POST['sbmAlterarTipoPeticionamento'])) {

                try {

                    $arrIdTipoDocumento = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnSerie']);
                    $arrIdTipoDocumentoEssencial = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnSerieEssencial']);
                    $arrIdUnidadesSelecionadas = $_POST['hdnUnidadesSelecionadas'] != '' ? json_decode($_POST['hdnUnidadesSelecionadas']) : array();

                    //para nao limpar os campos em caso de erro de duplicidade
                    $idMdPetTipoProcesso = $_POST['hdnIdMdPetTipoProcesso'];
                    $nomeTipoProcesso = $_POST['txtTipoProcesso'];
                    $tipoUnidade = is_array($_POST['rdUnidade']) ? current($_POST['rdUnidade']) : array();
                    $idTipoProcesso = $objMdPetTipoProcessoDTO->getNumIdProcedimento();
                    $orientacoes = $objMdPetTipoProcessoDTO->getStrOrientacoes();
                    $idUnidade = null /* $objMdPetTipoProcessoDTO->getNumIdUnidade() */;
                    $nomeUnidade = $_POST['txtUnidade'];
                    $sinIndIntUsExt = $objMdPetTipoProcessoDTO->getStrSinIIProprioUsuarioExterno() == 'S' ? 'checked = checked' : '';
                    $sinIndIntIndIndir = $objMdPetTipoProcessoDTO->getStrSinIIIndicacaoDireta() == 'S' ? 'checked = checked' : '';
                    $sinIndIntIndConta = $objMdPetTipoProcessoDTO->getStrSinIIIndicacaoDiretaContato() == 'S' ? 'checked = checked' : '';
                    $sinIndIntIndCpfCn = $objMdPetTipoProcessoDTO->getStrSinIIIndicacaoDiretaCpfCnpj() == 'S' ? 'checked = checked' : '';
                    $sinNAUsuExt = $objMdPetTipoProcessoDTO->getStrSinNaUsuarioExterno() == 'S' ? 'checked = checked' : '';
                    $sinNAPadrao = $objMdPetTipoProcessoDTO->getStrSinNaPadrao() == 'S' ? 'checked = checked' : '';
                    $gerado = $objMdPetTipoProcessoDTO->getStrSinDocGerado() == 'S' ? 'checked = checked' : '';
                    $externo = $objMdPetTipoProcessoDTO->getStrSinDocExterno() == 'S' ? 'checked = checked' : '';
                    $nomeSerie = $_POST['txtTipoDocPrinc'];
                    $idSerie = $objMdPetTipoProcessoDTO->getNumIdSerie();

                    $multipla = $tipoUnidade == 'M' ? true : false;
                    $unica = $tipoUnidade == 'U' ? true : false;
                    $hdnCorpoTabela = isset($_POST['hdnCorpoTabela']) ? $_POST['hdnCorpoTabela'] : '';

                    $objMdPetTipoProcessoDTO->setNumIdHipoteseLegal(null);

                    if ($_POST['selNivelAcesso'] != '') {

                        $objMdPetTipoProcessoDTO->setStrStaNivelAcesso($_POST['selNivelAcesso']);

                        if ($_POST['selNivelAcesso'] === ProtocoloRN::$NA_RESTRITO) {
                            $objMdPetTipoProcessoDTO->setNumIdHipoteseLegal($_POST['selHipoteseLegal']);
                        } else {
                            $objMdPetTipoProcessoDTO->setNumIdHipoteseLegal(null);
                        }
                    } else {
                        $objMdPetTipoProcessoDTO->setStrStaNivelAcesso(null);
                    }

                    $objAlterado = $objMdPetTipoProcessoRN->alterar($objMdPetTipoProcessoDTO);

                    if ($objAlterado) {
                        //EXCLUSÕES DAS RNS
                        //Exclusão de Tipo de Documento Essencial e Complementar
                        $numIdTpProcessoPet = isset($_GET['id_tipo_processo_peticionamento']) && $_GET['id_tipo_processo_peticionamento'] != '' ? $_GET['id_tipo_processo_peticionamento'] : $_POST['hdnIdMdPetTipoProcesso'];
                        $objMdPetRelTpProcSerieRN = new MdPetRelTpProcSerieRN();
                        $arrMdPetRelTpProcSerieDTO = array();
                        $objMdPetRelTpProcSerieDTO = new MdPetRelTpProcSerieDTO();
                        $objMdPetRelTpProcSerieDTO->setNumIdTipoProcessoPeticionamento($numIdTpProcessoPet);
                        $arrMdPetRelTpProcSerieDTO[] = $objMdPetRelTpProcSerieDTO;

                        $objMdPetRelTpProcSerieDTO->retTodos();
                        $arrMdPetRelTpProcSerieDTO = $objMdPetRelTpProcSerieRN->listar($objMdPetRelTpProcSerieDTO);

                        if (is_array($arrMdPetRelTpProcSerieDTO) && count($arrMdPetRelTpProcSerieDTO) > 0) {
                            $objMdPetRelTpProcSerieRN->excluir($arrMdPetRelTpProcSerieDTO);
                        }

                        //Exclusão de Unidade
                        $arrMdPetRelTpProcessoUnidDTO = array();
                        $objMdPetRelTpProcessoUnidRN = new MdPetRelTpProcessoUnidRN();
                        $objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
                        $objMdPetRelTpProcessoUnidDTO->setNumIdTipoProcessoPeticionamento($numIdTpProcessoPet);
                        $objMdPetRelTpProcessoUnidDTO->retTodos();
                        $arrMdPetRelTpProcessoUnidDTO = $objMdPetRelTpProcessoUnidRN->listar($objMdPetRelTpProcessoUnidDTO);

                        if (is_array($arrMdPetRelTpProcessoUnidDTO) && count($arrMdPetRelTpProcessoUnidDTO) > 0) {
                            $objMdPetRelTpProcessoUnidRN->excluir($arrMdPetRelTpProcessoUnidDTO);
                        }

                        //CADASTROS RNS
                        //Cadastro de Unidade
                        $objMdPetRelTpProcessoUnidRN = new MdPetRelTpProcessoUnidRN();

                        if ($tipoUnidade === MdPetTipoProcessoRN::$UNIDADES_MULTIPLAS) {
                            foreach ($arrIdUnidadesSelecionadas as $idUnidadeSelecionada) {
                                $objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();

                                $objMdPetRelTpProcessoUnidDTO->setNumIdTipoProcessoPeticionamento($numIdTpProcessoPet);
                                $objMdPetRelTpProcessoUnidDTO->setNumIdUnidade($idUnidadeSelecionada);
                                $objMdPetRelTpProcessoUnidDTO->setStrStaTipoUnidade(MdPetTipoProcessoRN::$UNIDADES_MULTIPLAS);
                                $objMdPetRelTpProcessoUnidRN->cadastrar($objMdPetRelTpProcessoUnidDTO);
                            }
                        } else {
                            $objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
                            $objMdPetRelTpProcessoUnidDTO->setNumIdTipoProcessoPeticionamento($numIdTpProcessoPet);
                            $objMdPetRelTpProcessoUnidDTO->setNumIdUnidade($_POST['hdnIdUnidade']);
                            $objMdPetRelTpProcessoUnidDTO->setStrStaTipoUnidade(MdPetTipoProcessoRN::$UNIDADE_UNICA);

                            $objMdPetRelTpProcessoUnidRN->cadastrar($objMdPetRelTpProcessoUnidDTO);
                        }


                        //Tipo de Documento Essencial
                        if (count($arrIdTipoDocumentoEssencial) > 0) {
                            foreach ($arrIdTipoDocumentoEssencial as $numIdTipoDocumentoEss) {

                                $objMdPetRelTpProcSerieEssDTO = new MdPetRelTpProcSerieDTO();
                                $objMdPetRelTpProcSerieEssDTO->setNumIdRelTipoProcessoSeriePeticionamento(null);
                                $objMdPetRelTpProcSerieEssDTO->setNumIdTipoProcessoPeticionamento($numIdTpProcessoPet);
                                $objMdPetRelTpProcSerieEssDTO->setNumIdSerie($numIdTipoDocumentoEss);
                                $objMdPetRelTpProcSerieEssDTO->setStrStaTipoDoc(MdPetRelTpProcSerieRN::$DOC_ESSENCIAL);

                                $objRelTipoProcSerieEssPetDTO = $objMdPetRelTpProcSerieRN->cadastrar($objMdPetRelTpProcSerieEssDTO);
                            }
                        }

                        //Tipo de Documento Complementar
                        if (count($arrIdTipoDocumento) > 0) {
                            foreach ($arrIdTipoDocumento as $numIdTipoDocumento) {
                                $objMdPetRelTpProcSerieDTO = new MdPetRelTpProcSerieDTO();
                                $objMdPetRelTpProcSerieDTO->setNumIdRelTipoProcessoSeriePeticionamento(null);
                                $objMdPetRelTpProcSerieDTO->setNumIdTipoProcessoPeticionamento($numIdTpProcessoPet);
                                $objMdPetRelTpProcSerieDTO->setNumIdSerie($numIdTipoDocumento);
                                $objMdPetRelTpProcSerieDTO->setStrStaTipoDoc(MdPetRelTpProcSerieRN::$DOC_COMPLEMENTAR);

                                $objRelTipoProcSeriePetDTO = $objMdPetRelTpProcSerieRN->cadastrar($objMdPetRelTpProcSerieDTO);
                            }
                        }
                    }

                    header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . '&id_tipo_processo_peticionamento=' . $objMdPetTipoProcessoDTO->getNumIdTipoProcessoPeticionamento() . PaginaSEI::getInstance()->montarAncora($objMdPetTipoProcessoDTO->getNumIdTipoProcessoPeticionamento())));
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }
            }
            break;

        case 'md_pet_tipo_processo_consultar':
            $strTitulo = 'Consultar Tipo de Processo para Peticionamento de Processo Novo';
            $arrComandos[] = '<button type="button" accesskey="c" name="btnCancelar" id="btnCancelar" value="Cancelar" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . PaginaSEI::getInstance()->getAcaoRetorno() . '&acao_origem=' . $_GET['acao'] . PaginaSEI::getInstance()->montarAncora($_GET['id_tipo_processo_peticionamento']))) . '\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';

            $strItensSelTipoProcesso = MdPetTipoProcessoINT::montarSelectTipoProcesso(null, null, $_POST['selTipoProcesso']);
            $strItensSelUnidades = UnidadeINT::montarSelectSiglaDescricao(null, null, $_POST['selUnidade']);
            $strItensSelDoc = SerieINT::montarSelectNomeRI0802(null, null, $_POST['selDocumento']);

            break;

        case 'md_pet_tipo_processo_salvar':


            break;


        default:
            throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");
    }
} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: ' . PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();

PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>


<?
PaginaSEI::getInstance()->fecharJavaScript();
?>


<style type="text/css">
<?php
$browser = $_SERVER['HTTP_USER_AGENT'];
$firefox = strpos($browser, 'Firefox') ? true : false;
?>

    #lblTipoProcesso {position:absolute;left:0%;top:2px;width:50%;}
    #txtTipoProcesso {position:absolute;left:0%;top:18px;width:50%;}

    #fldProrrogacao {height: 20%; width: 86%;}

<?php if ($firefox) { ?>

        .sizeFieldset {height: 30%; width: 86%;}
        .tamanhoFieldset{height:auto; width:86%;}

        #divIndicacaoInteressado {}
        #divUnidade {margin-top:138px!important;}

        #imgLupaTipoProcesso {position:absolute;left:50.8%;top:18px;}
        #imgExcluirTipoProcesso {position:absolute;left:52.6%;top:18px;}

        #txtUnidade {left:12px;width:65%;margin-top: 0.5%;}
        #imgExcluirUnidade {position:absolute;left:52.2%;margin-top: 0.2%;}
        #alertaRestricaoUU {position:absolute;left:54%;margin-top: -2.1%;}

        #lblOrientacoes {position:absolute;left:0%;top:50px;width:20%;}
        #txtOrientacoes {position:absolute;left:0%;top:66px;width:75%;}

        #lblNivelAcesso {width:50%;}
        #selNivelAcesso {width:20%;}

        #lblHipoteseLegal {width:50%;}
        #selHipoteseLegal {width:50%;}

        #lblModelo {width:50%;}
        #selModelo {width:40%;}

        #lblTipoDocPrincipal {width:50%;}
        #txtTipoDocPrinc {width:39.5%;}
        #imgLupaTipoDocPrinc {top:198%}
        #imgExcluirTipoDocPrinc {top:198%}

        #txtSerie {width:50%;}
        #lblDescricao {width:50%;}
        #selDescricao {width:75%;}
        #imgLupaTipoDocumento { margin-top: 2px; margin-left: 4px;}

        #txtSerieEssencial {width:50%;}
        #lblDescricaoEssencial {width:50%;}
        #selDescricaoEssencial {width:75%;}
        #imgLupaTipoDocumentoEssencial { margin-top: 2px; margin-left: 4px;}

        .fieldNone{border:none !important;}

        .sizeFieldset#fldDocPrincipal {height: 50%!important;}


<?php } else { ?>
        .sizeFieldset {height: 30%; width: 86%;}
        .tamanhoFieldset{height:auto; width:86%;}

        #divIndicacaoInteressado {}
        #imgLupaTipoProcesso {position:absolute;left:50.6%;top:18px;}
        #imgExcluirTipoProcesso {position:absolute;left:52.4%;top:18px;}

        #divUnidade {margin-top:111px!important;}

        #txtUnidade {left:0%;top:17.6%;width:65%;margin-top:0.5%}
        #imgLupaUnidade {position:absolute;left:50.5%;margin-top: 0.4%;}
        #imgExcluirUnidade {position:absolute;left:52.3%;margin-top: 0.4%;}
        #alertaRestricaoUU {position:absolute;left:54%;margin-top: -1.9%;}

        #lblOrientacoes {position:absolute;left:0%;top:50px;width:20%;}
        #txtOrientacoes {position:absolute;left:0%;top:66px;width:75%;}

        #lblNivelAcesso {width:50%;}
        #selNivelAcesso {width:20%;}

        #lblHipoteseLegal {width:50%;}
        #selHipoteseLegal {width:50%;}

        #lblModelo {width:50%;}
        #selModelo {width:40%;}

        #lblTipoDocPrincipal {width:50%;}
        #txtTipoDocPrinc {width:39.5%;}
        #imgLupaTipoDocPrinc {position:absolute;left:31.2%;margin-top: -0.01%;}
        #imgExcluirTipoDocPrinc {position:absolute;left:33%;margin-top: -0.01%;}
        #txtSerie {width:50%;}
        #lblDescricao {width:50%;}
        #selDescricao {width:75%;}

        #imgLupaTipoDocumento {
            margin-top: 2px;
            margin-left: 4px;
        }

        #imgExcluirTipoDocumento {
        }

        .fieldNone{border:none !important;}

        .sizeFieldset#fldDocPrincipal {height: 50%!important;}

        #txtSerieEssencial {width:50%;}
        #lblDescricaoEssencial {width:50%;}
        #selDescricaoEssencial {width:75%;}
        #imgLupaTipoDocumentoEssencial { margin-top: 2px; margin-left: 4px;}

<?php } ?>

    .fieldsetClear {border:none !important;}
    .rdIndicacaoIndiretaHide  {margin-left:2.8%!important;}

    #lblOrgaos {position:absolute;left:0%;top:0%;width:20%;}
    #txtOrgaoUnidadeMultipla {position:absolute;left:0%;top:33%;width:19.5%;}
    #selOrgaos {position:absolute;left:0%;top:45%;width:20%;}
    #divOpcoesOrgaos {position:absolute;left:20.5%;top:33%;}

    #lblUnidades {position:absolute;left:24%;top:0%;}
    #txtUnidadeMultipla {position:absolute;left:24%;top:33%;width:54.5%;}
    #selUnidades {position:absolute;left:25%;top:47%;width:55%;}
    #divOpcoesUnidades {position:absolute;left:79.5%;top:33%;}

    #sbmAdicionarUnidade {position:absolute;left:83%;top:33%;}
</style>
<?php
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
<form id="frmTipoProcessoCadastro" method="post" onsubmit="return OnSubmitForm();"
      action="<?= PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'])) ?>">
          <?
          PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
          PaginaSEI::getInstance()->abrirAreaDados('98%');
          ?>

    <input type="hidden" name="hdnParametroHipoteseLegal" id="hdnParametroHipoteseLegal" value="<?php echo $valorParametroHipoteseLegal; ?>"/>
    <!--  Tipo de Processo  -->
    <div class="fieldsetClear">
        <label id="lblTipoProcesso" for="txtTipoProcesso" class="infraLabelObrigatorio">
            Tipo de Processo:
        </label>
        <input type="text" onchange="removerProcessoAssociado(0);" id="txtTipoProcesso" name="txtTipoProcesso" class="infraText" value="<?php echo PaginaSEI::tratarHTML($nomeTipoProcesso); ?>" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" />
        <input type="hidden" id="hdnIdTipoProcesso" name="hdnIdTipoProcesso" value="<?php echo $idTipoProcesso ?>" />
        <input type="hidden" id="hdnIdMdPetTipoProcesso" name="hdnIdMdPetTipoProcesso" value="<?php echo $idMdPetTipoProcesso ?>" />
        <img id="imgLupaTipoProcesso" onclick="objLupaTipoProcesso.selecionar(700, 500);" onkeypress="objLupaTipoProcesso.selecionar(700, 500);" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/lupa.gif" alt="Selecionar Tipo de Processo" title="Selecionar Tipo de Processo" class="infraImg" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
        <img id="imgExcluirTipoProcesso" onclick="removerProcessoAssociado(0);objLupaTipoProcesso.remover();" onkeypress="removerProcessoAssociado(0);objLupaTipoProcesso.remover();" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/remover.gif" alt="Remover Tipo de Processo" title="Remover Tipo de Processo" class="infraImg" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />

    </div>
    <!--  Fim do Tipo de Processo -->

    <div style="clear:both;">&nbsp;</div>

    <!-- Orientações -->
    <div class="fieldsetClear">
        <label id="lblOrientacoes" for="txtOrientacoes" class="infraLabelObrigatorio">Orientações: <img align="top" style="height:16px; width:16px;" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('As orientações descritas abaixo serão exibidas na tela de Peticionamento de Processo Novo depois que o Usuário Externo tiver selecionado este Tipo de Processo para peticionar.')?> class="infraImg"/></label>
        <textarea type="text" id="txtOrientacoes" rows="3" name="txtOrientacoes" class="infraText" onkeypress="return infraMascaraTexto(this, event, 500);" maxlength="500" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"><?php echo PaginaSEI::tratarHTML($orientacoes); ?></textarea>
    </div>
    <!--  Fim das Orientações  -->

    <!--  Unidade -->
    <div id="divUnidade">
        <fieldset class="infraFieldset" style="width:75%;">
            <legend class="infraLegend">&nbsp;Unidade para Abertura do Processo&nbsp;</legend>
            <!-- Unidade única -->
            <?php
            $divUnidadeUnica = $unica ? 'style="display:inherit;margin-bottom: 6px"' : 'style="display:none;margin-bottom: 6px"';
            $checkUnidadeUnica = $unica ? 'checked="checked";' : '';
            ?>

            <input <?php echo $checkUnidadeUnica; ?> type="radio" id="rdUnidadeUnica" name="rdUnidade[]" onchange="changeUnidade()" value="U" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>">
            <label id="lblUnidadeUnica" name="lblUnidadeUnica" for="rdUnidadeUnica" class="infraLabelOpcional infraLabelRadio">Unidade Única <img align="top" style="height:16px; width:16px;" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('O Usuário Externo não terá opção de escolha para a abertura do Processo Novo, sendo sempre aberto na Unidade pré definida aqui.')?> class="infraImg"/></label>
            <br/>

            <div id="divCpUnidadeUnica" <?php echo $divUnidadeUnica; ?>>
                <input type="text" id="txtUnidade" name="txtUnidade" class="infraText" value="<?= PaginaSEI::tratarHTML($nomeUnidade) ?>" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" />
                <input type="hidden" id="hdnIdUnidade" name="hdnIdUnidade" value="<?= $idUnidade ?>" />
                <img id="imgLupaUnidade" onclick="objLupaUnidade.selecionar(700, 500);" onkeypress="objLupaUnidade.selecionar(700, 500);" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/lupa.gif" alt="Selecionar Unidade" title="Selecionar Unidade" class="infraImg" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
                <img id="imgExcluirUnidade" onclick="objLupaUnidade.remover();removerIconeRestricao();" onkeypress="objLupaUnidade.remover();removerIconeRestricao();" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/remover.gif" alt="Remover Unidade" title="Remover Unidade" class="infraImg" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
                <?php if ($tipoProcessoRestricaoErroUU) { ?>
                    <div id="divRestricaoUU">
                        <img id='alertaRestricaoUU' class='alertaRestricao' src='modulos/peticionamento/imagens/icone_contato.png' onmouseover='return infraTooltipMostrar("Esta Unidade não pode utilizar o Tipo de Processo indicado, em razão de restrição de uso do Tipo de Processo configurado pela Administração do SEI. Dessa forma, o Usuário Externo não visualiza a opção da UF ou Cidade para abertura do Processo correspondente a esta Unidade. <br><br> Remova a Unidade deste Peticionamento de Processo Novo ou, caso seja pertinente, deve ampliar as restrições de uso do Tipo de Processo para adicionar esta Unidade, no menu Administração > Tipos de Processos > Listar.", "");' onmouseout='return infraTooltipOcultar();'/>&nbsp;
                    </div>
                <?php } ?>
            </div>
            <!--  Fim da Unidade Única -->

            <!--  Múltiplas Unidades -->
            <?php
            $divUnidadeMultipla = $multipla ? 'style="display:inherit;"' : 'style="display:none;"';
            $checkUnidadeMultipla = $multipla ? 'checked="checked;"' : '';
            ?>

            <input <?php echo $checkUnidadeMultipla; ?> type="radio" id="rdUnidadeMultipla" name="rdUnidade[]" onchange="changeUnidade()" value="M" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>">
            <label id="lblUnidadeMultipla" name="lblUnidadeMultipla" for="rdUnidadeMultipla" class="infraLabelOpcional infraLabelRadio">Múltiplas Unidades <img align="top" style="height:16px; width:16px;" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('O Usuário Externo terá opção de escolha do Órgão, da UF ou da Cidade onde quer que o Processo Novo seja aberto. \n \n As três opções de escolha que o Usuário Externo verá depende das Unidades aqui adicionadas, quando possuirem diferentes Órgãos, UFs ou Cidades.')?> class="infraImg"/></label>

            <div id="divCpUnidadeMultipla" <?php echo $divUnidadeMultipla; ?>>
                <br>
                <div id="divOrgaoUnidadeMultipla" class="infraAreaDados" style="height:5em;">
                    <label id="lblOrgaos" for="selOrgaos" class="infraLabelObrigatorio">Órgão:</label>
                    <input type="text" id="txtOrgaoUnidadeMultipla" name="txtOrgaoUnidadeMultipla" class="infraText" onchange='criarLupaUnidade(this.value)'/>
                    <input type="hidden" id="hdnIdOrgaoUnidadeMultipla" name="hdnIdOrgaoUnidadeMultipla" class="infraText" value="" />
                    <?= $strHtmlOrgaoUnidades; ?>
                    <div id="divOpcoesOrgaos">
                        <img id="imgLupaOrgaos" onclick="objLupaOrgaoUnidadeMultipla.selecionar(700, 500);" onkeypress="objLupaOrgaoUnidadeMultipla.selecionar(700, 500);" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal(); ?>/lupa.gif" alt="Selecionar Órgão" title="Selecionar Órgão" class="infraImg" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
                    </div>
                    <label id="lblUnidades" for="selUnidades" class="infraLabelObrigatorio">Unidade:</label>
                    <input type="text" id="txtUnidadeMultipla" name="txtUnidadeMultipla" class="infraText"/>
                    <input type="hidden" id="hdnIdUnidadeMultipla" name="hdnIdUnidadeMultipla" value="<?= $idUnidadeMultipla ?>" />
                    <input type="hidden" id="hdnUfUnidadeMultipla" name="hdnUfUnidadeMultipla" value="" />
                    <div id="divOpcoesUnidades">
                        <img id="imgLupaUnidadeMultipla" onclick="verificarOrgaoSelecionado();" onkeypress="verificarOrgaoSelecionado();" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal(); ?>/lupa.gif" alt="Selecionar Unidade" title="Selecionar Unidade" class="infraImg" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
                    </div>
                    <?php if ($_GET['acao'] != 'md_pet_tipo_processo_consultar') { ?>
                        <button type="button" accesskey="A" name="sbmAdicionarUnidade" onclick="addUnidade();" id="sbmAdicionarUnidade" value="Adicionar" class="infraButton" style="font-size: 10px;" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"><span class="infraTeclaAtalho">A</span>dicionar</button>
                    <?php } ?>
                </div>


                <!-- Tabela Múltiplas Unidades -->

                <div class="infraAreaTabela" id="divTableMultiplasUnidades" <?php echo $divUnidadeMultipla; ?>>
                    <table width="99%" summary="Tabela de Unidades" class="infraTable" id="tableTipoUnidade">
                        <caption class="infraCaption">Lista de Unidades (<span id="qtdRegistros"><?php echo count($arrObjUnidadesMultiplas) > 0 ? count($arrObjUnidadesMultiplas) : '0'; ?> </span> registros):</caption>
                        <tbody>
                            <tr>
                                <th width="15%" class="infraTh"><table class="infraTableOrdenacao">
                                        <tbody>
                                            <tr>
                                                <td valign="center" class="infraTdRotuloOrdenacao">
                                                    Órgão
                                                </td>
                                            </tr>
                                        </tbody>
                                </th>
                    </table>
                    <th class="infraTh">
                        <table class="infraTableOrdenacao">
                            <tbody>
                                <tr>
                                    <td valign="center" class="infraTdRotuloOrdenacao">
                                        Unidade
                                    </td>
                                </tr>
                            </tbody></table>
                    </th>
                    <th width="15%" class="infraTh">
                        <table class="infraTableOrdenacao">
                            <tbody>
                                <tr>
                                    <td valign="center" class="infraTdRotuloOrdenacao">
                                        UF da Unidade
                                    </td>
                                </tr>
                            </tbody></table>
                    </th>
                    <th class="infraTh">
                        <table class="infraTableOrdenacao">
                            <tbody>
                                <tr>
                                    <td width="6%" valign="center" class="infraTdRotuloOrdenacao">
                                        Cidade da Unidade
                                    </td>
                                </tr>
                            </tbody></table>
                    </th>
                    <?php //if ($_GET['acao'] != 'md_pet_tipo_processo_consultar') { ?>
                        <th width="80px" class="infraTh">Ações</th>
                    <?php //} ?>
                    <tbody id="corpoTabela">
                        <?php
                        if ($multipla && isset($hdnCorpoTabela)) {
                            echo $hdnCorpoTabela;
                        }
                        ?>
                        <?php
                        if ($multipla) {
                            if (count($arrObjUnidadesMultiplas) > 0) {
                                //Verifica se existe restrição para este tipo de processo
                                $objTipoProcedRestricaoRN = new TipoProcedRestricaoRN();
                                $objTipoProcedRestricaoDTO = new TipoProcedRestricaoDTO();
                                $objTipoProcedRestricaoDTO->retNumIdOrgao();
                                $objTipoProcedRestricaoDTO->retNumIdUnidade();
                                $objTipoProcedRestricaoDTO->setNumIdTipoProcedimento($idTipoProcedimento);
                                $arrObjTipoProcedRestricaoDTO = $objTipoProcedRestricaoRN->listar($objTipoProcedRestricaoDTO);
                                $idOrgaoRestricao = InfraArray::converterArrInfraDTO($arrObjTipoProcedRestricaoDTO, 'IdOrgao');
                                $idUnidadeRestricao = InfraArray::converterArrInfraDTO($arrObjTipoProcedRestricaoDTO, 'IdUnidade');

                                foreach ($arrObjUnidadesMultiplas as $cadaObjUnidadeDTO) {
                                    $tipoProcessoRestricaoErro = false;
                                    $idTabela = 'tabNomeUnidade_' . $cadaObjUnidadeDTO->getNumIdUnidade();

                                    //Verifica se tem algum órgão diferente dos restritos, caso exista restrições para o tipo de processo
                                    if (($idOrgaoRestricao && $idOrgaoRestricao[0] != null) && !in_array($cadaObjUnidadeDTO->getNumIdOrgao(), $idOrgaoRestricao)) {
                                        $tipoProcessoRestricaoErro = true;
                                    }
                                    //Verifica se tem alguma unidade diferente dos restritos, caso exista restrições para o tipo de processo
                                    if (($idUnidadeRestricao && $idUnidadeRestricao[0] != null) && !in_array($cadaObjUnidadeDTO->getNumIdUnidade(), $idUnidadeRestricao)) {
                                        $tipoProcessoRestricaoErro = true;
                                    }

                                    $contatoAssociadoDTO = new ContatoDTO();
                                    $contatoAssociadoRN = new ContatoRN();
                                    $contatoAssociadoDTO->retStrSiglaUf();
                                    $contatoAssociadoDTO->retNumIdContato();
                                    $contatoAssociadoDTO->retStrNomeCidade();
                                    $contatoAssociadoDTO->retNumIdCidade();
                                    $contatoAssociadoDTO->setNumIdContato($cadaObjUnidadeDTO->getNumIdContato());

                                    $contatoAssociadoDTO = $contatoAssociadoRN->consultarRN0324($contatoAssociadoDTO);

                                    //verificando se existe algum tipo de processo com divergencia de orgao e cidade iguais
                                    if ($arrTipoProcessoOrgaoCidade) {
                                        $tipoProcessoDivergencia = false;
                                        foreach ($arrTipoProcessoOrgaoCidade as $key => $dados) {
                                            foreach ($dados as $key2 => $dados2) {
                                                if ($cadaObjUnidadeDTO->getNumIdOrgao() == $key && $cadaObjUnidadeDTO->getNumIdCidadeContato() == $key2 && $dados2 > 1) {
                                                    $tipoProcessoDivergencia = true;
                                                    break;
                                                }
                                            }
                                        }
                                    }

                                    //Caso tenha alguma unidade ou orgao diferente dos restritos ou unidades do mesmo órgão e cidade a tr terá uma cor diferente
                                    if ($tipoProcessoRestricaoErro) {
                                        echo "<tr class='linhas' id='" . $idTabela . "' bgcolor='#F4A460'>";
                                        //Caso tenha unidades de mesmo órgão e cidade a tr terá uma cor diferente
                                    } elseif ($tipoProcessoDivergencia) {
                                        echo "<tr class='linhas' id='" . $idTabela . "' bgcolor='#75AD8D'>";
                                    } else {
                                        echo '<tr class="infraTrClara linhas" id="' . $idTabela . '">';
                                    }
                                    //alteracoes seiv3
                                    ?>
                                <td valign="middle">
                                    <a alt="<?php echo $cadaObjUnidadeDTO->getStrDescricaoOrgao(); ?>" title="<?php echo $cadaObjUnidadeDTO->getStrDescricaoOrgao(); ?>" class="ancoraSigla"><?php echo $cadaObjUnidadeDTO->getStrSiglaOrgao(); ?>
                                </td>
                                <td  id="tabNomeUnidade" >
                                    <a alt="<?php echo $cadaObjUnidadeDTO->getStrDescricao(); ?>" title="<?php echo $cadaObjUnidadeDTO->getStrDescricao(); ?>" class="ancoraSigla"><?php echo $cadaObjUnidadeDTO->getStrSigla(); ?>
                                    </a>
                                </td>
                                <td class="ufsSelecionadas">
                                    <?php
//alteracoes seiv3
                                    echo $contatoAssociadoDTO->getStrSiglaUf();
                                    ?>
                                </td>
                                <td class="cidadesSelecionadas">
                                    <?php
//alteracoes seiv3
                                    echo $contatoAssociadoDTO->getStrNomeCidade();
                                    ?>
                                </td>
                                <td align="center">
                                    <?php if ($tipoProcessoRestricaoErro) { ?>
                                        <img id='alertaRestricao' class='alertaRestricao' src='modulos/peticionamento/imagens/icone_contato.png' onmouseover='return infraTooltipMostrar("Esta Unidade não pode utilizar o Tipo de Processo indicado, em razão de restrição de uso do Tipo de Processo configurado pela Administração do SEI. Dessa forma, o Usuário Externo não visualiza a opção da UF ou Cidade para abertura do Processo correspondente a esta Unidade. <br><br> Remova a Unidade deste Peticionamento de Processo Novo ou, caso seja pertinente, deve ampliar as restrições de uso do Tipo de Processo para adicionar esta Unidade, no menu Administração > Tipos de Processos > Listar.", "");' onmouseout='return infraTooltipOcultar();'/>&nbsp;
                                    <?php } if ($tipoProcessoDivergencia) { ?>
                                        <img id='alertaDivergencia' class='alertaDivergencia' src='modulos/peticionamento/imagens/icone_principal.png' onmouseover='return infraTooltipMostrar("Posteriormente à parametrização original deste Peticionamento devem ter ocorrido alterações no cadastro das Unidades, de forma que constam conflitos de Unidades com mesma UF ou mesma Cidade. Dessa forma, o Usuário Externo não visualiza a opção da UF ou Cidade para abertura do Processo correspondente às Unidades com tais conflitos.<br><br>Remova a Unidade deste Peticionamento de Processo Novo ou, caso seja pertinente, corrija o cadastro das Unidades para ficar com a UF ou a Cidade corretos, no menu Administração > Unidades > Listar.", "");' onmouseout='return infraTooltipOcultar();'/>&nbsp;
                                    <?php }if ($_GET['acao'] != 'md_pet_tipo_processo_consultar') { ?>
                                        <a>
                                            <img class="infraImg" title="Remover Unidade" alt="Remover Unidade" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/remover.gif" onclick="removerUnidade('<?php echo $idTabela; ?>');" id="imgExcluirProcessoSobrestado">
                                        </a>
                                    <?php } ?>
                                </td>
                                </tr>
                                <?php
                            }
                        }
                    }
                    ?>

                    </tbody>
                    </table>
                </div>

                <!--  Fim Tabela Múltiplas Unidades -->

            </div>

            <!--  Fim das Múltiplas Unidades -->
        </fieldset>
    </div>

    <!--  Fim da Unidade -->

    <br/>

    <!--  Indicação de Interessados -->
    <div id="divIndicacaoInteressado">
        <fieldset class="infraFieldset" style="width:75%;">
            <legend class="infraLegend">&nbsp;Indicação de Interessado&nbsp;</legend>

            <input onclick="changeIndicacaoInteressado()" type="radio" id="rdUsuExterno" name="indicacaoInteressado[]" value="1" <?php echo $sinIndIntUsExt ?> tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>">
            <label for="rdUsuExterno" id="lblUsuExterno" class="infraLabelRadio">Próprio Usuário Externo <img align="top" style="height:16px; width:16px;" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('O Usuário Externo logado sempre será o Interessado do processo a ser aberto, sem opção de escolha.')?> class="infraImg"/></label>
            <br/>
            <input onclick="changeIndicacaoInteressado()" type="radio" name="indicacaoInteressado[]" id="rdIndicacaoIndireta" value="2"  <?php echo $sinIndIntIndIndir ?> tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>">
            <label name="lblIndicacaoIndireta" id="lblIndicacaoIndireta" for="rdIndicacaoIndireta" class="infraLabelRadio">Indicação Direta <img align="top" style="height:16px; width:16px;" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('O Usuário Externo deverá indicar manualmente o Interessado do processo a ser aberto.')?> class="infraImg"/></label>
            <br/>

            <div id="divRdIndicacaoIndiretaHide" <?php echo $sinIndIntIndIndir != '' ? 'style="display: inherit;"' : 'style="display: none;"' ?> >
                <input <?php echo $sinIndIntIndCpfCn; ?>  type="radio" name="indicacaoIndireta[]" id="indicacaoIndireta1" class="rdIndicacaoIndiretaHide" value="3" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>">
                <label  name="lblInformandoCpfCnpj" for="indicacaoIndireta1" id="lblInformandoCpfCnpj" class="lblIndicacaoIndiretaHide infraLabelRadio">Informando CPF ou CNPJ <img align="top" style="height:16px; width:16px;" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('O Usuário Externo indicará o Interessado digitando um CPF ou CNPJ válido. \n \n Se o CPF ou CNPJ digitado não constar na lista de Contatos do SEI ou se existir duplicado, então o Usuário Externo será direcionado a uma janela de Cadastro do Contato que será de fato utilizado como Interessado do processo.')?> class="infraImg"/></label>
                <br/>

                <input <?php echo $sinIndIntIndConta; ?> type="radio" name="indicacaoIndireta[]"  id="indicacaoIndireta2" class="rdIndicacaoIndiretaHide" value="4" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>">
                <label for="indicacaoIndireta2" id="lblContatosJaExistentes" name="lblContatosJaExistentes" class="lblIndicacaoIndiretaHide infraLabelRadio">Digitando nome de Contatos já existentes <img align="top" style="height:16px; width:16px;" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('O Usuário Externo indicará o Interessado digitando o Nome ou clicando na Lupa para selecioná-lo dentre os Contatos do SEI, respeitadas as parametrizações na Administração > Peticionamento Eletrônico > Tipos de Contatos Permitidos. \n \n ATENÇÃO: Com esta opção, os Usuários Externos poderão acessar toda a lista de Contatos do SEI do Órgão.')?> class="infraImg"/></label>
            </div>

        </fieldset>
    </div>
    </br>

    <!--  Fim da Indicação de Interessados -->

    <div>
        <fieldset class="infraFieldset" style="width:75%;">
            <legend class="infraLegend">&nbsp;Nível de Acesso dos Documentos&nbsp;</legend>
            <div>
                <input <?php echo $sinNAUsuExt; ?> type="radio" name="rdNivelAcesso[]" id="rdUsuExternoIndicarEntrePermitidos" onclick="changeNivelAcesso();" value="1" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>">

                <label for="rdUsuExternoIndicarEntrePermitidos" id="lblUsuExterno" class="infraLabelRadio">Usuário Externo indica diretamente <img align="top" style="height:16px; width:16px;" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('O Usuário Externo terá opção de escolha do Nível de Acesso para cada Documento que adicionar.')?> class="infraImg"/></label>
                <br/>

                <input <?php echo $sinNAPadrao; ?> type="radio" name="rdNivelAcesso[]"  id="rdPadrao" onclick="changeNivelAcesso();" value="2" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>">
                <label name="lblPadrao" id="lblPadrao" for="rdPadrao" class="infraLabelRadio">Padrão pré definido <img align="top" style="height:16px; width:16px;" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('O Usuário Externo não terá opção de escolha do Nível de Acesso para os Documentos, sendo sempre adicionados com o Nível de Acesso pré definido aqui.')?> class="infraImg"/></label>

                <div id="divNivelAcesso"  <?php echo $sinNAPadrao != '' ? 'style="display: inherit;"' : 'style="display: none;"' ?>>
                    <div style="clear:both;">&nbsp;</div>
                    <label name="lblNivelAcesso" id="lblNivelAcesso" for="selNivelAcesso" class="infraLabelObrigatorio">Nível de Acesso: <img align="top" style="height:16px; width:16px;" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('As opções abaixo dependem dos Níveis de Acesso Permitidos para o Tipo de Processo escolhido acima. \n \n A opção Sigiloso não é suportada para o Peticionamento de Processo Novo.')?> class="infraImg"/></label>
                    <br/>
                    <select id="selNivelAcesso" name="selNivelAcesso" onchange="changeSelectNivelAcesso()">
                        <?= $strItensSelNivelAcesso ?>
                    </select>
                </div>

                <div id="divHipoteseLegal" <?php echo $hipoteseLegal //$sinNAPadrao != '' ? 'style="display: inherit;"' : 'style="display: none;"'                  ?> >
                    <div style="clear:both;">&nbsp;</div>
                    <label name="lblHipoteseLegal" id="lblHipoteseLegal" for="selHipoteseLegal" class="infraLabelObrigatorio">Hipótese Legal: <img align="top" style="height:16px; width:16px;" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('As opções abaixo dependem da parametrização na Administração > Peticionamento Eletrônico > Hipóteses Legais Permitidas.')?> class="infraImg"/></label>
                    <br/>
                    <select id="selHipoteseLegal" name="selHipoteseLegal">
                        <?= $strItensSelHipoteseLegal ?>
                    </select>

                </div>

            </div>
        </fieldset>
    </div>

    <div style="clear:both;">&nbsp;</div>

    <fieldset id="fldDocPrincipal" class="infraFieldset tamanhoFieldset" style="top:110%; width: 75%;">

        <legend class="infraLegend">&nbsp;Documento Principal&nbsp;</legend>

        <input type="radio" name="rdDocPrincipal[]" id="rdDocGerado" onclick="changeDocPrincipal();" value="1" <?php echo $gerado ?> tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>">
        <label for="rdDocGerado" id="lblDocGerado" class="infraLabelRadio">Gerado (Editor e Modelo) <img align="top" style="height:16px; width:16px;" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('O Usuário Externo deverá preencher um Documento de modelo pré definido, utilizando o Editor HTML do SEI. \n \n Neste caso, selecione Tipo de Documento parametrizado na Administração com Aplicabilidade de Documentos Internos ou Internos e Externos. \n \n ATENÇÃO: por limitações técnicas, o Usuário Externo somente visualizará e editará a seção Princial (Corpo do Texto) do modelo do Documento.')?> class="infraImg"/></label>
        <br/>

        <input type="radio" name="rdDocPrincipal[]"  id="rdDocExterno" onclick="changeDocPrincipal();" value="2" <?php echo $externo ?> tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>">
        <label name="lblDocExterno" id="lblDocExterno" for="rdDocExterno" class="infraLabelRadio">Externo (Anexação de Arquivo) <img align="top" style="height:16px; width:16px;" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('O Usuário Externo deverá anexar um Arquivo como Documento Principal, respeitadas as parametrizações na Administração > Peticionamento Eletrônico > Extensão de Arquivos Permitidos e Tamanho Máximo de Arquivos.')?> class="infraImg"/></label>
        <br/>

        <div  <?php echo $gerado != '' || $externo != '' ? 'style="display: inherit;"' : 'style="display: none;"' ?>  id="divDocPrincipal">
            <div class="clear:both;">&nbsp;</div>
            <div>
                <label name="lblTipoDocPrincipal" id="lblTipoDocPrincipal" for="txtTipoDocPrinc" class="infraLabelObrigatorio">Tipo do Documento Principal:</label>
            </div>
            <input type="text" id="txtTipoDocPrinc" name="txtTipoDocPrinc" class="infraText" value="<?= PaginaSEI::tratarHTML($nomeSerie) ?>" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" />

            <input type="hidden" id="hdnIdTipoDocPrinc" name="hdnIdTipoDocPrinc" value="<?= $idSerie ?>" />
            <img id="imgLupaTipoDocPrinc" onclick="carregarComponenteLupaTpDocPrinc('S');" onkeypress="carregarComponenteLupaTpDocPrinc('S');" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/lupa.gif" alt="Selecionar Tipo de Documento" title="Selecionar Tipo de Documento" class="infraImg" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
            <img id="imgExcluirTipoDocPrinc" onclick="carregarComponenteLupaTpDocPrinc('R')" onkeypress="carregarComponenteLupaTpDocPrinc('R')" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/remover.gif" alt="Remover Tipo de Documento" title="Remover Tipo de Documento" class="infraImg" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />

        </div>

    </fieldset>
    <!--  Documento Essencial -->
    <?
    $divDocs = 'style="display: inherit;"';
    ?>
    <fieldset <?php echo $divDocs; ?> id="fldDocEssenciais" class="sizeFieldset tamanhoFieldset fieldNone">
        <div>
            <div style="clear:both;">&nbsp;</div>
            <div>
                <label id="lblDescricaoEssencial" for="selDescricaoEssencial" class="infraLabelOpcional">Tipos dos Documentos Essenciais: <img align="top" style="height:16px; width:16px;" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('Esta opção não é obrigatória na parametrização, mas se for utilizada o Usuário Externo será obrigado a anexar um Arquivo como Documento Essencial para cada Tipo de Documento que for indicado aqui, respeitadas as parametrizações na Administração > Peticionamento Eletrônico > Extensão de Arquivos Permitidos e Tamanho Máximo de Arquivos.')?> class="infraImg"/></label>
            </div>
            <div>
                <input type="text" id="txtSerieEssencial" name="txtSerieEssencial" class="infraText" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" />

            </div>
            <div style="margin-top: 5px;">
                <select style="float: left;" id="selDescricaoEssencial" name="selDescricaoEssencial" size="8" multiple="multiple" class="infraSelect" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>">
                    <?= $strItensSelSeriesEss; ?>
                </select>

                <img id="imgLupaTipoDocumentoEssencial" onclick="objLupaTipoDocumentoEssencial.selecionar(700, 500)" onkeypress="objLupaTipoDocumentoEssencial.selecionar(700, 500)" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/lupa.gif" alt="Selecionar Tipo de Documento" title="Selecionar Tipo de Documento" class="infraImg" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />

                <img id="imgExcluirTipoDocumentoEssencial" onclick="objLupaTipoDocumentoEssencial.remover();" onkeypress="objLupaTipoDocumentoEssencial.remover();" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/remover.gif" alt="Remover Tipos de Documentos Selecionados" title="Remover Tipos de Documentos Selecionados" class="infraImg" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />

            </div>

        </div>
    </fieldset>
    <!--  Fim do Documento Essencial -->

    <!--  Documento Complementar  -->
    <fieldset <?php echo $divDocs; ?> id="fldDocComplementar" class="sizeFieldset tamanhoFieldset fieldNone">
        <div>
            <div style="clear:both;">&nbsp;</div>
            <div>
                <label id="lblDescricao" for="txtDescricao" class="infraLabelOpcional">Tipos dos Documentos Complementares: <img align="top" style="height:16px; width:16px;" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/ajuda.gif" name="ajuda" <?= PaginaSEI::montarTitleTooltip('O Usuário Externo não será obrigado a anexar nenhum Documento Complementar, utilizando-os para anexar Documentos que podem variar conforme cada caso, respeitadas as parametrizações na Administração > Peticionamento Eletrônico > Extensão de Arquivos Permitidos e Tamanho Máximo de Arquivos. \n \n É boa prática indicar o máximo de Tipos de Documentos neste campo.')?> class="infraImg"/></label>
            </div>
            <div>
                <input type="text" id="txtSerie" name="txtSerie" class="infraText" tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>" />

            </div>
            <div style="margin-top: 5px;">
                <select style="float: left;" id="selDescricao" name="selDescricao" size="16" multiple="multiple" class="infraSelect" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>">
                    <?= $strItensSelSeries ?>
                </select>
                <img id="imgLupaTipoDocumento" onclick="carregarComponenteLupaTpDocComplementar('S');" onkeypress="carregarComponenteLupaTpDocComplementar('S');" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/lupa.gif" alt="Selecionar Tipo de Documento" title="Selecionar Tipo de Documento" class="infraImg" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
                <img id="imgExcluirTipoDocumento" onclick="carregarComponenteLupaTpDocComplementar('R');" onkeypress="carregarComponenteLupaTpDocComplementar('R');" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/remover.gif" alt="Remover Tipos de Documentos Selecionados" title="Remover Tipos de Documentos Selecionados" class="infraImg" tabindex="<?=PaginaSEI::getInstance()->getProxTabDados()?>" />
            </div>

        </div>
    </fieldset>
    <!--  Fim do Documento Complementar -->

    <input type="hidden" id="hdnCorpoTabela" name="hdnCorpoTabela" value=""/>
    <input type="hidden" id="hdnUnidadesSelecionadas" name="hdnUnidadesSelecionadas" value=""/>
    <input type="hidden" id="hdnTodasUnidades" name="hdnTodasUnidades" value='<?= json_encode($arrObjUnidadeDTOFormatado); ?>' />
    <input type="hidden" id="hdnIdTipoDocumento" name="hdnIdTipoDocumento" value="" />
    <input type="hidden" id="hdnSerie" name="hdnSerie" value="<?= $_POST['hdnSerie'] ?>" />
    <input type="hidden" id="hdnIdTipoDocumento" name="hdnIdTipoDocumento" value="<?= $_POST['hdnIdTipoDocumento'] ?>" />
    <input type="hidden" id="hdnIdIndisponibilidadePeticionamento" name="hdnIdIndisponibilidadePeticionamento" value="" />
    <input type="hidden" id="hdnIdSerie" name="hdnIdSerie" value="<?= $_POST['hdnIdSerie'] ?>" />
    <input type="hidden" id="hdnIdSerieEssencial" name="hdnIdSerieEssencial" value="<?= $_POST['hdnIdSerieEssencial'] ?>" />
    <input type="hidden" id="hdnSerieEssencial" name="hdnSerieEssencial" value="<?= $_POST['hdnSerieEssencial'] ?>" />

    <?
    PaginaSEI::getInstance()->fecharAreaDados();
    ?>
</form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>

<script type="text/javascript">
    //Processo
    var objLupaTipoProcesso = null;
    var objAutoCompletarTipoProcesso = null;

//Docs
    var objLupaTipoDocumento = null;
    var objAutoCompletarTipoDocumento = null;

    var objLupaTipoDocPrinc = null;
    var objAutoCompletarTipoDocPrinc = null;

    var objLupaTipoDocumentoEssencial = null
    var objAutoCompletarTipoDocumentoEssencial = null;

//Unidades
    var objLupaUnidade = null;
    var objAutoCompletarUnidade = null;

    var objLupaUnidadeMultipla = null;
    var objAutoCompletarUnidadeMutipla = null;

    //Orgao    
    var objLupaOrgaoUnidadeMultipla = null;
    var objAutoCompletarOrgaoUnidadeMutipla = null;

    function criarLupaUnidade(){
        if(document.getElementById('hdnIdOrgaoUnidadeMultipla').value != ''){
            objLupaUnidadeMultipla = null;
            objAutoCompletarUnidadeMutipla = null;
            var link  = document.getElementById('lnkOrgao' + document.getElementById('hdnIdOrgaoUnidadeMultipla').value).value;
            objLupaUnidadeMultipla = new infraLupaText('txtUnidadeMultipla', 'hdnIdUnidadeMultipla', link);

            objLupaUnidadeMultipla.finalizarSelecao = function () {
                objAutoCompletarUnidadeMultipla.selecionar(document.getElementById('hdnIdUnidadeMultipla').value, document.getElementById('txtUnidadeMultipla').value);
            }

            objAutoCompletarUnidadeMultipla = new infraAjaxAutoCompletar('hdnIdUnidadeMultipla', 'txtUnidadeMultipla', '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=unidade_auto_completar_todas'); ?>');
            objAutoCompletarUnidadeMultipla.limparCampo = false;
            objAutoCompletarUnidadeMultipla.tamanhoMinimo = 3;
            objAutoCompletarUnidadeMultipla.prepararExecucao = function () {
                return 'palavras_pesquisa='+document.getElementById('txtUnidadeMultipla').value+'&id_orgao='+document.getElementById('hdnIdOrgaoUnidadeMultipla').value;

            };

            objAutoCompletarUnidadeMultipla.processarResultado = function (id, descricao, uf) {
                if (id != '') {
                    document.getElementById('hdnIdUnidadeMultipla').value = id;
                    document.getElementById('txtUnidadeMultipla').value = descricao;
                    document.getElementById('hdnUfUnidadeMultipla').value = uf;
                }
            }
        }else{
            objLupaUnidadeMultipla = null;
            objAutoCompletarUnidadeMutipla = null;
        }
        document.getElementById('hdnIdUnidadeMultipla').value = '';
        document.getElementById('txtUnidadeMultipla').value = '';
        document.getElementById('hdnUfUnidadeMultipla').value = '';
    }

    function addUnidade() {
        var idUnidadeSelect = document.getElementById('hdnIdUnidadeMultipla').value;

        if (idUnidadeSelect != '') {
            var paramsAjax = {
                idTipoProcesso: document.getElementById('hdnIdTipoProcesso').value,
                idOrgaoUnidadeMultipla: document.getElementById('hdnIdOrgaoUnidadeMultipla').value,
                idUnidadeMultipla: document.getElementById('hdnIdUnidadeMultipla').value
            };

            $.ajax({
                url: '<?=$strLinkAjaxConfirmaRestricao?>',
                type: 'POST',
                dataType: 'XML',
                data: paramsAjax,
                success: function (result) {
                    if($(result).find('valor').text() == 'A'){
                        var idLinhaTabela = 'tabNomeUnidade_' + idUnidadeSelect;
                        var existeUnidade = document.getElementById(idLinhaTabela);
                        var valueCodUnidades = document.getElementById('hdnTodasUnidades').value;

                        if (valueCodUnidades != '') {
                            var objUnidades = $.parseJSON(valueCodUnidades);

                            if (!registroDuplicado(objUnidades[idUnidadeSelect].siglaOrgao, objUnidades[idUnidadeSelect].cidade)) {
                                qtdLinhas = document.getElementsByClassName('linhas').length;
                                var html = '';
                                if (qtdLinhas > 0) {
                                    html = document.getElementById('corpoTabela').innerHTML;
                                }

                                html += '<tr class="infraTrClara linhas" id="' + idLinhaTabela + '"><td>';
                                html += '<a alt="' + objUnidades[idUnidadeSelect].descricaoOrgao + '" title="' + objUnidades[idUnidadeSelect].descricaoOrgao + '" class="ancoraSigla">' + objUnidades[idUnidadeSelect].siglaOrgao + '</a>';
                                html += '</td><td>';
                                html += '<a alt="' + objUnidades[idUnidadeSelect].descricaoUnidade + '" title="' + objUnidades[idUnidadeSelect].descricaoUnidade + '" class="ancoraSigla">' + objUnidades[idUnidadeSelect].siglaUnidade + '</a>';
                                html += '<td class="ufsSelecionadas">' + objUnidades[idUnidadeSelect].uf + '</td>';
                                html += '<td class="ufsSelecionadas">' + objUnidades[idUnidadeSelect].cidade + '</td>';
                                html += '<td align="center">';
                                html += '<a><img class="infraImg" title="Remover Unidade" alt="Remover Unidade" src="<?= PaginaSEI::getInstance()->getDiretorioImagensGlobal() ?>/remover.gif" onclick="removerUnidade(\'' + idLinhaTabela + '\');" id="imgExcluirProcessoSobrestado"></a></td></tr>';

                                //Adiciona Conteúdo da Tabela no HTML
                                document.getElementById('corpoTabela').innerHTML = '';
                                document.getElementById('corpoTabela').innerHTML = html;

                                // Mostra a tabela
                                document.getElementById('divTableMultiplasUnidades').style.display = "inherit";

                                //Zera os campos, após adicionar
                                document.getElementById('txtUnidadeMultipla').value = '';
                                document.getElementById('hdnIdUnidadeMultipla').value = '';
                                document.getElementById('txtOrgaoUnidadeMultipla').value = '';
                                document.getElementById('hdnIdOrgaoUnidadeMultipla').value = '';

                                document.getElementById('qtdRegistros').innerHTML = qtdLinhas + 1;
                            }
                        }
                    }else{
                        alert('Esta Unidade não pode utilizar o Tipo de Processo indicado, em razão de restrição de uso do Tipo de Processo configurado pela Administração do SEI. \n\nCaso seja pertinente, antes deve ampliar as restrições de uso do Tipo de Processo para adicionar esta Unidade, no menu Administração > Tipos de Processos > Listar.');
                        return false;
                    }
                },
                error: function (e) {
                    console.error('Erro ao processar o XML do SEI: ' + e.responseText);
                }
            });
        }
    }

    function verificarOrgaoSelecionado() {
        if (document.getElementById('hdnIdOrgaoUnidadeMultipla').value == '') {
            alert('Nenhum Órgão selecionado.');
            return false;
        } else {
            objLupaUnidadeMultipla.selecionar(700, 500);
        }
    }

    function removerUnidade(idObj) {

        document.getElementById(idObj).remove();
        qtdLinhas = document.getElementsByClassName('linhas').length;
        document.getElementById('qtdRegistros').innerHTML = qtdLinhas;

        if (qtdLinhas == 0) {
            document.getElementById('divTableMultiplasUnidades').style.display = "none";
        }

    }

    function registroDuplicado(orgao, cidade) {
        for (var i = 0; i < document.getElementById('corpoTabela').rows.length; i++) {
            var linha = document.getElementById('corpoTabela').rows[i];
            if (linha.cells[0].innerText.toLowerCase().trim() == orgao.toLowerCase().trim()
                    && linha.cells[3].innerText.toLowerCase().trim() == cidade.toLowerCase().trim()) {
                alert('Não é permitido adicionar mais de uma Unidade para abertura do mesmo Órgão e para a mesma Cidade.');
                return true;
            }
        }
        return false;
    }



    function changeUnidade() {
        //Limpando tabela de unidades Múltiplas e campos vinculados as unidades multiplas
        document.getElementById("corpoTabela").innerHTML = '';
        document.getElementById('txtUnidadeMultipla').value = '';
        document.getElementById('hdnIdUnidadeMultipla').value = '';
        document.getElementById('txtOrgaoUnidadeMultipla').value = '';
        document.getElementById('hdnIdOrgaoUnidadeMultipla').value = '';
        document.getElementById('divTableMultiplasUnidades').style.display = "none";

        //Limpando campos vinculados a unidade Única
        document.getElementById("txtUnidade").value = '';
        document.getElementById("hdnIdUnidade").value = '';

        var unidUnic = document.getElementsByName('rdUnidade[]')[0].checked;

        document.getElementById("divCpUnidadeUnica").style.display = "none";
        document.getElementById("divCpUnidadeMultipla").style.display = "none";

        unidUnic ? document.getElementById("divCpUnidadeUnica").style.display = "inherit" : document.getElementById("divCpUnidadeMultipla").style.display = "inherit";
    }

     function changeUnidadeTipoProcesso() {
        //Limpando tabela de unidades Múltiplas e campos vinculados as unidades multiplas
        document.getElementById("corpoTabela").innerHTML = '';
        document.getElementById('txtUnidadeMultipla').value = '';
        document.getElementById('hdnIdUnidadeMultipla').value = '';
        document.getElementById('txtOrgaoUnidadeMultipla').value = '';
        document.getElementById('hdnIdOrgaoUnidadeMultipla').value = '';
        document.getElementById('divTableMultiplasUnidades').style.display = "none";

        //Limpando campos vinculados a unidade Única
        document.getElementById("txtUnidade").value = '';
        document.getElementById("hdnIdUnidade").value = '';

        document.getElementById("rdUnidadeUnica").checked = false;
        document.getElementById("rdUnidadeMultipla").checked = false;

        document.getElementById("divCpUnidadeUnica").style.display = "none";
        document.getElementById("divCpUnidadeMultipla").style.display = "none";
    }

    function changeIndicacaoInteressado() {
        var indIndireta = document.getElementsByName('indicacaoInteressado[]')[1].checked;
        document.getElementById('divRdIndicacaoIndiretaHide').style.display = "none";

        document.getElementsByName('indicacaoIndireta[]')[0].checked = false;
        document.getElementsByName('indicacaoIndireta[]')[0].checked = '';

        document.getElementsByName('indicacaoIndireta[]')[1].checked = false;
        document.getElementsByName('indicacaoIndireta[]')[1].checked = '';

        var elementLupa = document.getElementById('imgLupaTipoDocumento');
        var percentLupa = getPercentTopStyle(elementLupa);

        if (indIndireta)
        {
            document.getElementById('divRdIndicacaoIndiretaHide').style.display = "inherit";
        }

    }

    function removerProcessoAssociado(remover) {

        document.getElementById('selNivelAcesso').innerHTML = '';
        document.getElementById('divHipoteseLegal').style.display = "none";
        console.log(remover);
        if (remover === '1') {
            objLupaTipoProcesso.remover();
        }
    }

    function changeNivelAcesso() {

        document.getElementById('divNivelAcesso').style.display = "none";
        var padrao = document.getElementsByName('rdNivelAcesso[]')[1].checked;

        document.getElementById('selNivelAcesso').value = '';
        document.getElementById('selNivelAcesso').value = '';
        document.getElementById('selHipoteseLegal').value = '';
        document.getElementById('divHipoteseLegal').style.display = 'none';

        if (padrao) {
            document.getElementById('divNivelAcesso').style.display = "inherit";
        }

    }

    function changeSelectNivelAcesso() {
        document.getElementById('selHipoteseLegal').value = '';

        var valorSelectNivelAcesso = document.getElementById('selNivelAcesso').value;
        var valorHipoteseLegal = document.getElementById('hdnParametroHipoteseLegal').value;

        if (valorSelectNivelAcesso == '<?= ProtocoloRN::$NA_RESTRITO ?>' && valorHipoteseLegal != '0') {

            document.getElementById('divHipoteseLegal').style.display = 'inherit';

        } else {

            document.getElementById('divHipoteseLegal').style.display = 'none';

        }
    }


    function changeDocPrincipal() {

        var gerado = document.getElementsByName('rdDocPrincipal[]')[0].checked;
        var tipo = '';

        document.getElementById('divDocPrincipal').style.display = "inherit";
        document.getElementById('fldDocEssenciais').style.display = "inherit";
        document.getElementById('fldDocComplementar').style.display = "inherit";

        if (objLupaTipoDocPrinc != null) {
            objLupaTipoDocPrinc.remover();
        }

        if (gerado) {
            tipo = 'G';
            document.getElementById('txtTipoDocPrinc').value = '';
            document.getElementsByName("rdDocPrincipal[]")[0].focus();
        } else {
            tipo = 'E';
            document.getElementById('txtTipoDocPrinc').value = '';
            document.getElementsByName("rdDocPrincipal[]")[1].focus();
        }

        carregarComponenteAutoCompleteTpDocPrinc(tipo);


        //rdDocPrincipal
    }

    function changeDocPrincipalEdicao() {

        var gerado = document.getElementsByName('rdDocPrincipal[]')[0].checked;
        var tipo = '';

        document.getElementById('divDocPrincipal').style.display = "inherit";

        if (gerado) {
            tipo = 'G';
            document.getElementsByName("rdDocPrincipal[]")[0].focus();
        } else {
            tipo = 'E';
            document.getElementsByName("rdDocPrincipal[]")[1].focus();
        }

        carregarComponenteAutoCompleteTpDocPrinc(tipo);

    }


    function inicializar() {

        inicializarTela();
        verificarQtdRegistrosUndMultipla();

        if ('<?= $_GET['acao'] ?>' != 'md_pet_tipo_processo_consultar') {
            carregarComponenteTipoDocumento(); //Doc Complementares - Seleção Múltipla
            carregarComponenteTipoProcesso(); // Seleção Única
            carregarComponenteUnidade();  // Seleção Única
            carregarComponenteUnidadeMultipla(); // Seleção única (Múltipla Tabela)
            carregarComponenteOrgaoMultiplo(); // Seleção única (Múltipla Tabela)
            carregarComponenteTipoDocumentoEssencial(); // Seleção Múltipla
            carregarDependenciaNivelAcesso();
        }


        if ('<?= $_GET['acao'] ?>' == 'md_pet_tipo_processo_cadastrar') {
            document.getElementById('txtTipoProcesso').focus();
        } else if ('<?= $_GET['acao'] ?>' == 'md_pet_tipo_processo_consultar') {
            infraDesabilitarCamposAreaDados();
            var itemRestricao = document.getElementsByClassName('alertaRestricao');
            for (i = 0; i < itemRestricao.length; i++) {
                itemRestricao[i].removeAttribute('style');
            }
            var itemDivergencia = document.getElementsByClassName('alertaDivergencia');
            for (i = 0; i < itemDivergencia.length; i++) {
                itemDivergencia[i].removeAttribute('style');
            }
        } else {
            document.getElementById('btnCancelar').focus();
        }
        infraEfeitoTabelas();

        if ('<?= $_GET['acao'] ?>' == 'md_pet_tipo_processo_alterar') {
            changeDocPrincipalEdicao();
        }

    }

    function verificarQtdRegistrosUndMultipla() {
        var multiplasUnidades = document.getElementById('rdUnidadeMultipla').checked;

        if (multiplasUnidades) {
            var qtdRegistros = document.getElementById('qtdRegistros').innerHTML;
            var linhas = (document.getElementsByClassName('linhas')).length;
            if (qtdRegistros != linhas)
            {
                document.getElementById('qtdRegistros').innerHTML = linhas;
            }
        }
    }

    function carregarDependenciaNivelAcesso() {
        //Ajax para carregar os niveis de acesso após a escolha do tipo de processo
        objAjaxIdNivelAcesso = new infraAjaxMontarSelectDependente('txtTipoProcesso', 'selNivelAcesso', '<?= $strLinkAjaxNivelAcesso ?>');
        objAjaxIdNivelAcesso.prepararExecucao = function () {
            document.getElementById('selNivelAcesso').innerHTML = '';
            return infraAjaxMontarPostPadraoSelect('null', '', 'null') + '&idTipoProcesso=' + document.getElementById('hdnIdTipoProcesso').value;
        }
    }

    function inicializarTela() {
    }

   function carregarComponenteUnidadeMultipla() {
        objLupaUnidadeMultipla = new infraLupaText('txtUnidadeMultipla', 'hdnIdUnidadeMultipla', '');

        objLupaUnidadeMultipla.finalizarSelecao = function () {
            objAutoCompletarUnidadeMultipla.selecionar(document.getElementById('hdnIdUnidadeMultipla').value, document.getElementById('txtUnidadeMultipla').value);
        }

        objAutoCompletarUnidadeMultipla = new infraAjaxAutoCompletar('hdnIdUnidadeMultipla', 'txtUnidadeMultipla', '<?= SessaoSEI::getInstance()->assinarLink('controlador_ajax.php?acao_ajax=unidade_auto_completar_todas'); ?>');
        objAutoCompletarUnidadeMultipla.limparCampo = false;
        objAutoCompletarUnidadeMultipla.tamanhoMinimo = 3;
        objAutoCompletarUnidadeMultipla.prepararExecucao = function () {
            if (document.getElementById('hdnIdOrgaoUnidadeMultipla').value == '') {
                alert('Nenhum Órgão selecionado.');
                document.getElementById('txtUnidadeMultipla').value = '';
                return false;
            }
            return 'palavras_pesquisa='+document.getElementById('txtUnidadeMultipla').value+'&id_orgao='+document.getElementById('hdnIdOrgaoUnidadeMultipla').value;
        };

        objAutoCompletarUnidadeMultipla.processarResultado = function (id, descricao, uf) {
            if (id != '') {
                document.getElementById('hdnIdUnidadeMultipla').value = id;
                document.getElementById('txtUnidadeMultipla').value = descricao;
                document.getElementById('hdnUfUnidadeMultipla').value = uf;
            }
        }
    }

    function carregarComponenteOrgaoMultiplo() {
        objLupaOrgaoUnidadeMultipla = new infraLupaText('txtOrgaoUnidadeMultipla', 'hdnIdOrgaoUnidadeMultipla','<?= $strLinkOrgaoMultiplaSelecao ?>');

        objLupaOrgaoUnidadeMultipla.validarSelecionar = function () {
            objLupaOrgaoUnidadeMultipla.limpar();
            return true;
        }

        objLupaOrgaoUnidadeMultipla.processarRemocao = function (itens) {
            objLupaOrgaoUnidadeMultipla.limpar();
            objAutoCompletarUnidadeMultipla.limpar();
            for (var i = 0; i < itens.length; i++) {
                document.getElementById('hdnOrgao' + itens[i].value).value = '';
            }
            return true;
        }

        objLupaOrgaoUnidadeMultipla.finalizarSelecao = function () {
            document.getElementById('hdnIdUnidadeMultipla').value = '';
            document.getElementById('txtUnidadeMultipla').value = '';
            document.getElementById('hdnUfUnidadeMultipla').value = '';
            criarLupaUnidade();
        }

        objAutoCompletarUnidadeOrgaoMultipla = new infraAjaxAutoCompletar('hdnIdOrgaoUnidadeMultipla', 'txtOrgaoUnidadeMultipla','<?= $strLinkAjaxOrgao ?>');
        objAutoCompletarUnidadeOrgaoMultipla.limparCampo = true;
        objAutoCompletarUnidadeOrgaoMultipla.prepararExecucao = function () {
            return 'palavras_pesquisa=' + document.getElementById('txtOrgaoUnidadeMultipla').value;
        };

        objAutoCompletarUnidadeOrgaoMultipla.processarResultado = function (id, descricao, complemento) {
            if (id != '') {
                //objLupaOrgaoUnidadeMultipla.adicionar(id, descricao, document.getElementById('txtOrgaoUnidadeMultipla'));
                //objLupaUnidadeMultipla.limpar();
            }
        }
    }

    function carregarComponenteUnidade() {
        objLupaUnidade = new infraLupaText('txtUnidade', 'hdnIdUnidade', '<?= $strLinkUnidadeSelecao ?>');

        objLupaUnidade.finalizarSelecao = function () {
            objAutoCompletarUnidade.selecionar(document.getElementById('hdnIdUnidade').value, document.getElementById('txtUnidade').value);
        }


        objAutoCompletarUnidade = new infraAjaxAutoCompletar('hdnIdUnidade', 'txtUnidade', '<?= $strLinkAjaxUnidade ?>');
        objAutoCompletarUnidade.limparCampo = false;
        objAutoCompletarUnidade.tamanhoMinimo = 3;
        objAutoCompletarUnidade.prepararExecucao = function () {
            return 'palavras_pesquisa=' + document.getElementById('txtUnidade').value;
        };

        objAutoCompletarUnidade.processarResultado = function (id, descricao, complemento) {
            if (id != '') {
                document.getElementById('hdnIdUnidade').value = id;
                document.getElementById('txtUnidade').value = descricao;
            }
        }
        objAutoCompletarUnidade.selecionar('<?= $strIdUnidade ?>', '<?= PaginaSEI::getInstance()->formatarParametrosJavascript(PaginaSEI::tratarHTML($strNomeRemetente)); ?>');
    }

    function carregarComponenteLupaTpDocPrinc(acaoComponente) {

        var gerado = document.getElementsByName('rdDocPrincipal[]')[0].checked;
        var tipo = gerado ? 'G' : 'E';
        var link = '<?= $strLinkTipoDocPrincExternoSelecao ?>';

        if (gerado) {
            link = '<?= $strLinkTipoDocPrincGeradoSelecao ?>';
        }

        objLupaTipoDocPrinc = new infraLupaText('txtTipoDocPrinc', 'hdnIdTipoDocPrinc', link);

        objLupaTipoDocPrinc.finalizarSelecao = function () {
            objAutoCompletarTipoDocPrinc.selecionar(document.getElementById('hdnIdTipoDocPrinc').value, document.getElementById('txtTipoDocPrinc').value);
        }

        acaoComponente == 'S' ? objLupaTipoDocPrinc.selecionar(700, 500) : objLupaTipoDocPrinc.remover();
    }

    function carregarComponenteLupaTpDocComplementar(acaoComponente) {
        acaoComponente == 'S' ? objLupaTipoDocumento.selecionar(700, 500) : objLupaTipoDocumento.remover();
    }

    function returnLinkModificado(link, tipo) {
        var arrayLink = link.split('&filtro=1');

        var linkFim = '';
        if (arrayLink.length == 2) {
            linkFim = arrayLink[0] + '&filtro=1&tipoDoc=' + tipo + arrayLink[1];
        } else {
            linkFim = link;
        }

        return linkFim;
    }


    function carregarComponenteAutoCompleteTpDocPrinc(tipo) {

        objAutoCompletarTipoDocPrinc = new infraAjaxAutoCompletar('hdnIdTipoDocPrinc', 'txtTipoDocPrinc', '<?= $strLinkAjaxTipoDocPrinc ?>');
        objAutoCompletarTipoDocPrinc.limparCampo = true;
        objAutoCompletarTipoDocPrinc.tamanhoMinimo = 3;
        objAutoCompletarTipoDocPrinc.prepararExecucao = function () {
            return 'palavras_pesquisa=' + document.getElementById('txtTipoDocPrinc').value + '&tipoDoc=' + tipo;
        };

        objAutoCompletarTipoDocPrinc.processarResultado = function (id, descricao, complemento) {
            if (id != '') {
                document.getElementById('hdnIdTipoDocPrinc').value = id;
                document.getElementById('txtTipoDocPrinc').value = descricao;
            }
        }
        objAutoCompletarTipoDocPrinc.selecionar('<?= $strIdTipoDocPrinc ?>', '<?= PaginaSEI::getInstance()->formatarParametrosJavascript(PaginaSEI::tratarHTML($strNomeRemetente)); ?>');
    }



    function carregarComponenteTipoProcesso() {
        objLupaTipoProcesso = new infraLupaText('txtTipoProcesso', 'hdnIdTipoProcesso', '<?= $strLinkTipoProcessoSelecao ?>');

        objLupaTipoProcesso.finalizarSelecao = function () {
            objAutoCompletarTipoProcesso.selecionar(document.getElementById('hdnIdTipoProcesso').value, document.getElementById('txtTipoProcesso').value);
            objAjaxIdNivelAcesso.executar();
            changeUnidadeTipoProcesso();

        }

        objAutoCompletarTipoProcesso = new infraAjaxAutoCompletar('hdnIdTipoProcesso', 'txtTipoProcesso', '<?= $strLinkAjaxTipoProcesso ?>');
        objAutoCompletarTipoProcesso.limparCampo = false;
        objAutoCompletarTipoProcesso.tamanhoMinimo = 3;
        objAutoCompletarTipoProcesso.prepararExecucao = function () {
            return 'palavras_pesquisa=' + document.getElementById('txtTipoProcesso').value;
        };

        objAutoCompletarTipoProcesso.processarResultado = function (id, descricao, complemento) {
            if (id != '') {
                document.getElementById('hdnIdTipoProcesso').value = id;
                document.getElementById('txtTipoProcesso').value = descricao;
                changeUnidadeTipoProcesso();
                objAjaxIdNivelAcesso.executar();
            }
        }
        objAutoCompletarTipoProcesso.selecionar('<?= $strIdTipoProcesso ?>', '<?= PaginaSEI::getInstance()->formatarParametrosJavascript(PaginaSEI::tratarHTML($strNomeRemetente)); ?>');

    }

//Carrega o documento para o documento complementar
    function carregarComponenteTipoDocumento() {

        objAutoCompletarTipoDocumento = new infraAjaxAutoCompletar('hdnIdSerie', 'txtSerie', '<?= $strLinkAjaxTipoDocumento ?>');
        objAutoCompletarTipoDocumento.limparCampo = true;
        objAutoCompletarTipoDocumento.tamanhoMinimo = 3;
        objAutoCompletarTipoDocumento.prepararExecucao = function () {
            var gerado = document.getElementsByName('rdDocPrincipal[]')[0].checked;
            var tipo = 'E';
            return 'palavras_pesquisa=' + document.getElementById('txtSerie').value + '&tipoDoc=' + tipo;
        };

        objAutoCompletarTipoDocumento.processarResultado = function (id, nome, complemento) {

            if (id != '') {
                var options = document.getElementById('selDescricao').options;

                if (options != null) {
                    for (var i = 0; i < options.length; i++) {
                        if (options[i].value == id) {
                            alert('Tipo de Documento já consta na lista.');
                            break;
                        }
                    }
                }

                if (i == options.length) {

                    for (i = 0; i < options.length; i++) {
                        options[i].selected = false;
                    }

                    opt = infraSelectAdicionarOption(document.getElementById('selDescricao'), nome, id);

                    objLupaTipoDocumento.atualizar();

                    opt.selected = true;
                }

                document.getElementById('txtSerie').value = '';
                document.getElementById('txtSerie').focus();

            }
        };

        objLupaTipoDocumento = new infraLupaSelect('selDescricao', 'hdnSerie', '<?= $strLinkTipoDocumentoSelecao ?>');
    }

//Carrega o documento para o documento essencial
    function carregarComponenteTipoDocumentoEssencial() {

        objAutoCompletarTipoDocumentoEssencial = new infraAjaxAutoCompletar('hdnIdSerieEssencial', 'txtSerieEssencial', '<?= $strLinkAjaxTipoDocumento ?>');
        objAutoCompletarTipoDocumentoEssencial.limparCampo = true;
        objAutoCompletarTipoDocumentoEssencial.tamanhoMinimo = 3;
        objAutoCompletarTipoDocumentoEssencial.prepararExecucao = function () {
            var gerado = document.getElementsByName('rdDocPrincipal[]')[0].checked;
            var tipo = 'E';
            return 'palavras_pesquisa=' + document.getElementById('txtSerieEssencial').value + '&tipoDoc=' + tipo;
        };

        objAutoCompletarTipoDocumentoEssencial.processarResultado = function (id, nome, complemento) {

            if (id != '') {
                var options = document.getElementById('selDescricaoEssencial').options;

                if (options != null) {
                    for (var i = 0; i < options.length; i++) {
                        if (options[i].value == id) {
                            alert('Tipo de Documento já consta na lista.');
                            break;
                        }
                    }
                }

                if (i == options.length) {

                    for (i = 0; i < options.length; i++) {
                        options[i].selected = false;
                    }

                    opt = infraSelectAdicionarOption(document.getElementById('selDescricaoEssencial'), nome, id);

                    objLupaTipoDocumentoEssencial.atualizar();

                    opt.selected = true;
                }

                document.getElementById('txtSerieEssencial').value = '';
                document.getElementById('txtSerieEssencial').focus();

            }
        };

        objLupaTipoDocumentoEssencial = new infraLupaSelect('selDescricaoEssencial', 'hdnSerieEssencial', '<?= $strLinkTipoDocumentoEssencialSelecao ?>');
    }


    function validarCadastro() {

        var valorHipoteseLegal = document.getElementById('hdnParametroHipoteseLegal').value;

        if (infraTrim(document.getElementById('txtTipoProcesso').value) == '') {
            alert('Informe o Tipo de Processo.');
            document.getElementById('txtTipoProcesso').focus();
            return false;
        }

        if (infraTrim(document.getElementById('txtOrientacoes').value) == '') {
            alert('Informe as Orientações.');
            document.getElementById('txtOrientacoes').focus();
            return false;
        }

//Validar Unidade SM - EU6155
        var unidUnic = document.getElementsByName('rdUnidade[]')[0].checked;
        var multUnic = document.getElementsByName('rdUnidade[]')[1].checked;

        if (unidUnic) {
            if (infraTrim(document.getElementById('hdnIdUnidade').value) == '') {
                alert('Informe a Unidade para abertura do processo.');
                document.getElementById('txtUnidade').focus();
                return false;
            }
        }

        if (multUnic) {
            var objUndSelecionadas = document.getElementsByClassName('linhas');
            if (objUndSelecionadas.length == 0) {
                alert('É necessário informar ao menos uma Unidade para Abertura de Processo.');
                document.getElementById('txtUnidadeMultipla').focus();
                return false;
            }
        }

        if (!multUnic && !unidUnic) {
            alert('Informe a Unidade para abertura do processo.');
            document.getElementById('txtUnidade').focus();
            return false;
        }


        //Validar Rádio Indicação de Interessado
        var elemsIndInt = document.getElementsByName("indicacaoInteressado[]");

        validoIndInt = false;
        for (var i = 0; i < elemsIndInt.length; i++) {
            if (elemsIndInt[i].checked === true) {
                validoIndInt = true;
            }
        }

        if (!validoIndInt) {
            alert('Informe a Indicação de Interessado.');
            document.getElementById('rdUsuExterno').focus();
            return false;
        }

//Validar Rádio Indicação de Interessado
        var indicacaoIndireta = document.getElementById('rdIndicacaoIndireta').checked;

        if (indicacaoIndireta)
        {
            var elemsIndInd = document.getElementsByName("indicacaoIndireta[]");

            validoIndInd = false;
            for (var i = 0; i < elemsIndInd.length; i++)
            {
                if (elemsIndInd[i].checked === true)
                {
                    validoIndInd = true;
                }
            }

            if (!validoIndInd) {
                alert('Informe a Indicação de Interessado.');
                document.getElementsByName('indicacaoIndireta[]')[0].focus();
                return false;
            }
        }

//Validar Nível Acesso
        var elemsNA = document.getElementsByName("rdNivelAcesso[]");

        validoNA = false;
        for (var i = 0; i < elemsNA.length; i++) {
            if (elemsNA[i].checked === true) {
                validoNA = true;
            }
        }

        if (((infraTrim(document.getElementById('selNivelAcesso').value) == '') && document.getElementById('rdPadrao').checked) || (!validoNA)) {
            alert('Informe o Nível de Acesso.');
            document.getElementById('rdUsuExterno').focus();
            return false;
        } else if (document.getElementById('selNivelAcesso').value == <?= ProtocoloRN::$NA_RESTRITO ?> && valorHipoteseLegal != '0') {

            //validar hipotese legal
            if (document.getElementById('selHipoteseLegal').value == '') {
                alert('Informe a Hipótese legal padrão.');
                document.getElementById('selHipoteseLegal').focus();
                return false;
            }

        }

//Documento Principal
        var elemsDP = document.getElementsByName("rdDocPrincipal[]");

        validoDP = false;

        for (var i = 0; i < elemsDP.length; i++) {

            if (elemsDP[i].checked == true) {
                validoDP = true;
            }

        }

        if (!validoDP) {
            alert('Informe o Documento Principal.');
            document.getElementById('rdDocGerado').focus();
            return false;
        }

        if (infraTrim(document.getElementById('txtTipoDocPrinc').value) == '') {
            alert('Informe o Tipo de Documento Principal.');
            document.getElementById('txtOrientacoes').focus();
            return false;
        }

        var multiplasUnidades = document.getElementById('rdUnidadeMultipla').checked;

        if(multiplasUnidades){
            var paramsAjax = {
                idTipoProcesso: document.getElementById('hdnIdTipoProcesso').value,
                idUnidadeMultipla: document.getElementById('hdnUnidadesSelecionadas').value
            };
        }else{
            var paramsAjax = {
                idTipoProcesso: document.getElementById('hdnIdTipoProcesso').value,
                idUnidadeMultipla: "["+document.getElementById('hdnIdUnidade').value+"]"

            };
        }

        var restricao = false;

            $.ajax({
                    url: '<?=$strLinkAjaxConfirmaRestricaoSalvar?>',
                    type: 'POST',
                    dataType: 'XML',
                    async: false,
                    data: paramsAjax,
                    success: function (result) {
                        if($(result).find('valor').text() == 'R'){
                            alert('Existem conflitos de parametrização na seção Unidade para Abertura do Processo. \n\n Resolva os conflitos antes de salvar.');
                            restricao = true;
                        }
                    },
                    error: function (e) {
                        console.error('Erro ao processar o XML do SEI: ' + e.responseText);
                    }
            });
            if(restricao){
                return false;
            }

        //Verifica a Qtd de Unidades
        var tbUnidades = document.getElementById('tableTipoUnidade');
        if(tbUnidades.rows.length < 3 && multiplasUnidades){
            alert(" Como foi selecionada a opção Múltiplas Unidades para Abertura do Processo, é necessário adicionar mais de uma Unidade na lista");
            restricao = true;
        }
        if(restricao){
            return false;
        }

        return true;
    }

    function OnSubmitForm() {
        preencherUnidadesMultiplas();
        return validarCadastro();
    }

    function preencherUnidadesMultiplas() {
        var arrayIdsBd = new Array();
        var objUndSelecionadas = document.getElementsByClassName('linhas');

        for (var i = 0; i < objUndSelecionadas.length; i++)
        {
            idTabela = (objUndSelecionadas[i].id).split('_')[1];
            arrayIdsBd.push(idTabela);
        }

        document.getElementById("hdnUnidadesSelecionadas").value = JSON.stringify(arrayIdsBd);
        document.getElementById("hdnCorpoTabela").value = document.getElementById('corpoTabela').innerHTML;
    }

    function getPercentTopStyle(element) {
        var parent = element.parentNode,
                computedStyle = getComputedStyle(element),
                value;
        parent.style.display = 'none';
        value = computedStyle.getPropertyValue('top');
        parent.style.removeProperty('display');

        if (value != '') {
            valor = value.replace('%', '');
            return parseInt(valor);
        }

        return false;
    }

    function removerIconeRestricao(){
        document.getElementById('divRestricaoUU').innerHTML = "";
    }

</script>
