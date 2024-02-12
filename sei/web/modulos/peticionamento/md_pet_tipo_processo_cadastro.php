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

    require_once 'md_pet_tipo_processo_cadastro_inicializacao.php';

    $objInfraParametroDTO = new InfraParametroDTO();
    $objMdPetParametroRN = new MdPetParametroRN();
    $objInfraParametroDTO->retTodos();
    $objInfraParametroDTO->setStrNome('SEI_HABILITAR_HIPOTESE_LEGAL');
    $objInfraParametroDTO = $objMdPetParametroRN->consultar($objInfraParametroDTO);
    $valorParametroHipoteseLegal = $objInfraParametroDTO->getStrValor();

    $objOrgaoDTO = new OrgaoDTO();
    $objOrgaoRN = new OrgaoRN();
    $objOrgaoDTO->retNumIdOrgao();
    $objOrgaoDTO->setDistinct(true);
    $objOrgaoDTO->retStrSigla();
	$objOrgaoDTO->SinConsultaProcessual('S');
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

            if (!empty($arrObjMdPetRelTpProcessoUnidDTO)) {
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

            $hipoteseLegal = $objMdPetTipoProcessoDTO->getStrStaNivelAcesso() === ProtocoloRN::$NA_RESTRITO && $valorParametroHipoteseLegal != '0' ? 'style="display:inherit; margin-top: 0px"' : 'style="display:none;  margin-top: 0px"';

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
                    $objInfraException = new InfraException();

                    $objNivelAcessoPermitidoDTO = new NivelAcessoPermitidoDTO();
                    $objNivelAcessoPermitidoDTO->retStrStaNivelAcesso();
                    $objNivelAcessoPermitidoDTO->setNumIdTipoProcedimento($objMdPetTipoProcessoDTO->getNumIdProcedimento());
                    $objNivelAcessoPermitidoRN = new NivelAcessoPermitidoRN();
                    $arrObjNivelAcessoPermitidoDTO = $objNivelAcessoPermitidoRN->listar($objNivelAcessoPermitidoDTO);

                    $arrDadosNivelAcessoPermitido = array();
                    foreach ($arrObjNivelAcessoPermitidoDTO as $ObjNivelAcessoPermitido){
                        $arrDadosNivelAcessoPermitido[] = $ObjNivelAcessoPermitido->getStrStaNivelAcesso();
                    }

                    if(!in_array(ProtocoloRN::$NA_PUBLICO, $arrDadosNivelAcessoPermitido)){
                        $objInfraException->lancarValidacao('Tipo de Processo para Peticionamento de Processo Novo não pode ser cadastrado, pois o Nível de Acesso do Tipo de Processo não está configurado como Público.');
                        $objInfraException->lancarValidacoes();
                    } else {
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
                    }
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
                    $objInfraException = new InfraException();

                    $objNivelAcessoPermitidoDTO = new NivelAcessoPermitidoDTO();
                    $objNivelAcessoPermitidoDTO->retStrStaNivelAcesso();
                    $objNivelAcessoPermitidoDTO->setNumIdTipoProcedimento($objMdPetTipoProcessoDTO->getNumIdProcedimento());
                    $objNivelAcessoPermitidoRN = new NivelAcessoPermitidoRN();
                    $arrObjNivelAcessoPermitidoDTO = $objNivelAcessoPermitidoRN->listar($objNivelAcessoPermitidoDTO);

                    $arrDadosNivelAcessoPermitido = array();
                    foreach ($arrObjNivelAcessoPermitidoDTO as $ObjNivelAcessoPermitido){
                        $arrDadosNivelAcessoPermitido[] = $ObjNivelAcessoPermitido->getStrStaNivelAcesso();
                    }

                    if(!in_array(ProtocoloRN::$NA_PUBLICO, $arrDadosNivelAcessoPermitido)){
                        $objInfraException->lancarValidacao('Tipo de Processo para Peticionamento de Processo Novo não pode ser alterado, pois o Nível de Acesso do Tipo de Processo não está configurado como Público.');
                        $objInfraException->lancarValidacoes();
                    } else {
                        $arrIdTipoDocumento = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnSerie']);
                        $arrIdTipoDocumentoEssencial = PaginaSEI::getInstance()->getArrValuesSelect($_POST['hdnSerieEssencial']);
                        $arrIdUnidadesSelecionadas = $_POST['hdnUnidadesSelecionadas'] != '' ? json_decode($_POST['hdnUnidadesSelecionadas']) : array();

                        //para nao limpar os campos em caso de erro de duplicidade
                        $idMdPetTipoProcesso = $_POST['hdnIdMdPetTipoProcesso'];
                        $nomeTipoProcesso = $_POST['txtTipoProcesso'];
                        $tipoUnidade = is_array($_POST['rdUnidade']) ? current($_POST['rdUnidade']) : array();
                        $idTipoProcesso = $objMdPetTipoProcessoDTO->getNumIdProcedimento();
                        $orientacoes = $objMdPetTipoProcessoDTO->getStrOrientacoes();
                        $idUnidade = null /* $objMdPetTipoProcessoDTO->getNumIdUnidade() */
                        ;
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
                            if (!empty($arrIdTipoDocumentoEssencial)) {
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
                            if (!empty($arrIdTipoDocumento)) {
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
                    }
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
require_once 'md_pet_tipo_processo_cadastro_css.php';
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>


<?
PaginaSEI::getInstance()->fecharJavaScript();
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
<form id="frmTipoProcessoCadastro" method="post" onsubmit="return OnSubmitForm();"
      action="<?= PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'])) ?>">
    <?
    PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos);
    PaginaSEI::getInstance()->abrirAreaDados('98%');
    ?>
    <div id="divGeral" class="infraAreaDados">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="form-group">
                    <label id="lblTipoProcesso" for="txtTipoProcesso" class="infraLabelObrigatorio">
                        Tipo de Processo:
                    </label>
                    <div class="input-group mb-3">
                        <input type="text" onchange="removerProcessoAssociado(0);" id="txtTipoProcesso"
                            name="txtTipoProcesso"
                            class="infraText form-control"
                            value="<?php echo PaginaSEI::tratarHTML($nomeTipoProcesso); ?>"
                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        <input type="hidden" id="hdnIdTipoProcesso" name="hdnIdTipoProcesso"
                            value="<?php echo $idTipoProcesso ?>"/>
                        <input type="hidden" id="hdnIdMdPetTipoProcesso" name="hdnIdMdPetTipoProcesso"
                            value="<?php echo $idMdPetTipoProcesso ?>"/>
                        <img id="imgLupaTipoProcesso" onclick="objLupaTipoProcesso.selecionar(700, 500);"
                            onkeypress="objLupaTipoProcesso.selecionar(700, 500);"
                            src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/pesquisar.svg"
                            alt="Selecionar Tipo de Processo" title="Selecionar Tipo de Processo"
                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                        <img id="imgExcluirTipoProcesso"
                            onclick="removerProcessoAssociado(0);objLupaTipoProcesso.remover();"
                            onkeypress="removerProcessoAssociado(0);objLupaTipoProcesso.remover();"
                            src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg"
                            alt="Remover Tipo de Processo" title="Remover Tipo de Processo"
                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <div class="form-group">
                    <label id="lblOrientacoes" for="txtOrientacoes" class="infraLabelObrigatorio">Orientações:
                        <img align="top"
                            src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                            name="ajuda" <?= PaginaSEI::montarTitleTooltip('As orientações descritas abaixo serão exibidas na tela de Peticionamento de Processo Novo depois que o Usuário Externo tiver selecionado este Tipo de Processo para peticionar.', 'Ajuda') ?>
                            class="infraImg"/>
                    </label>
                    <div class="input-group mb-3">
                        <textarea type="text" id="txtOrientacoes" rows="5" name="txtOrientacoes"
                                class="infraText form-control"
                                onkeypress="return infraMascaraTexto(this, event, 1000);" maxlength="1000"
                                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"><?php echo PaginaSEI::tratarHTML($orientacoes); ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3 ml-0">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <fieldset class="infraFieldset fieldsetUnidade">
                    <legend class="infraLegend">&nbsp;Unidade para Abertura do Processo&nbsp;</legend>
                    <div id="divUnidade">
                        <!-- Unidade única -->
                        <?php
                        $divUnidadeUnica = $unica ? 'style="display:inherit;margin-bottom: 6px; margin-top: 10px"' : 'style="display:none;margin-bottom: 6px; margin-top: 10px"';
                        $checkUnidadeUnica = $unica ? 'checked="checked";' : '';
                        ?>
                        <div class="form-group">
                            <div class="divUnidadeUnica">
                                <input <?php echo $checkUnidadeUnica; ?> type="radio" id="rdUnidadeUnica" name="rdUnidade[]"
                                                                        onchange="changeUnidade()" value="U"
                                                                        class="infraRadio"
                                                                        tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                <label id="lblUnidadeUnica" name="lblUnidadeUnica" for="rdUnidadeUnica"
                                    class="infraLabelRadio">Unidade Única <img align="top"
                                                                                                    src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                                                                                    name="ajuda" <?= PaginaSEI::montarTitleTooltip('O Usuário Externo não terá opção de escolha para a abertura do Processo Novo, sendo sempre aberto na Unidade pré definida aqui.', 'Ajuda') ?>
                                                                                                    class="infraImg"/></label>
                                <div id="divCpUnidadeUnica" <?php echo $divUnidadeUnica; ?> class="col-sm-12 col-md-7 col-lg-7 col-xl-7">
                                    <div class="input-group mb-3">
                                        <input type="text" id="txtUnidade" name="txtUnidade" class="infraText form-control"
                                            value="<?= PaginaSEI::tratarHTML($nomeUnidade) ?>"
                                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                        <input type="hidden" id="hdnIdUnidade" name="hdnIdUnidade"
                                            value="<?= $idUnidade ?>"/>
                                        <img id="imgLupaUnidade" onclick="objLupaUnidade.selecionar(700, 500);"
                                            onkeypress="objLupaUnidade.selecionar(700, 500);"
                                            src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/pesquisar.svg"
                                            alt="Selecionar Unidade" title="Selecionar Unidade"
                                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                        <img id="imgExcluirUnidade"
                                            onclick="objLupaUnidade.remover();removerIconeRestricao();"
                                            onkeypress="objLupaUnidade.remover();removerIconeRestricao();"
                                            src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg"
                                            alt="Remover Unidade" title="Remover Unidade"
                                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                        <?php if ($tipoProcessoRestricaoErroUU) { ?>
                                            <div id="divRestricaoUU">
                                                <img id='alertaRestricaoUU' class='alertaRestricao' width="24" height="24"
                                                    src='modulos/peticionamento/imagens/svg/icone_contato.svg?<?= Icone::VERSAO ?>'
                                                    onmouseover='return infraTooltipMostrar("Esta Unidade não pode utilizar o Tipo de Processo indicado, em razão de restrição de uso do Tipo de Processo configurado pela Administração do SEI. Dessa forma, o Usuário Externo não visualiza a opção da UF ou Cidade para abertura do Processo correspondente a esta Unidade. <br><br> Remova a Unidade deste Peticionamento de Processo Novo ou, caso seja pertinente, deve ampliar as restrições de uso do Tipo de Processo para adicionar esta Unidade, no menu Administração > Tipos de Processos > Listar.", "Ajuda");'
                                                    onmouseout='return infraTooltipOcultar();'/>&nbsp;
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <!--  Fim da Unidade Única -->

                            <!--  Múltiplas Unidades -->
                            <?php
                            $divUnidadeMultipla = $multipla ? 'style="display:inherit; margin-top: 15px;"' : 'style="display:none; margin-top: 15px;"';
                            $divUnidadeMultiplaTable = $multipla ? 'style="display:inherit;"' : 'style="display:none;"';
                            $checkUnidadeMultipla = $multipla ? 'checked="checked;"' : '';
                            ?>
                            <div class="divUnidadeMultipla">
                                <input <?php echo $checkUnidadeMultipla; ?> type="radio" id="rdUnidadeMultipla"
                                                                            name="rdUnidade[]" class="infraRadio"
                                                                            onchange="changeUnidade()" value="M"
                                                                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                <label id="lblUnidadeMultipla" name="lblUnidadeMultipla" for="rdUnidadeMultipla"
                                    class="infraLabelRadio">Múltiplas Unidades <img align="top"
                                                                                                        src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                                                                                        name="ajuda" <?= PaginaSEI::montarTitleTooltip('O Usuário Externo terá opção de escolha do Órgão, da UF ou da Cidade onde quer que o Processo Novo seja aberto. \n \n As três opções de escolha que o Usuário Externo verá depende das Unidades aqui adicionadas, quando possuirem diferentes Órgãos, UFs ou Cidades.', 'Ajuda') ?>
                                                                                                        class="infraImg"/></label>
                                <div id="divCpUnidadeMultipla" <?php echo $divUnidadeMultipla; ?>>
                                    <div id="divOrgaoUnidadeMultipla" class="infraAreaDados" style="">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3">
                                                <div class="form-group">
                                                    <label id="lblOrgaos" for="selOrgaos"
                                                        class="infraLabelObrigatorio">Órgão:</label>
                                                    <div class="input-group mb-3">
                                                        <input type="text" id="txtOrgaoUnidadeMultipla"
                                                            name="txtOrgaoUnidadeMultipla"
                                                            class="infraText form-control"
                                                            onchange='criarLupaUnidade(this.value)'/>
                                                        <input type="hidden" id="hdnIdOrgaoUnidadeMultipla"
                                                            name="hdnIdOrgaoUnidadeMultipla"
                                                            class="infraText" value=""/>
                                                        <?= $strHtmlOrgaoUnidades; ?>
                                                        <div id="divOpcoesOrgaos">
                                                            <img id="imgLupaOrgaos"
                                                                onclick="objLupaOrgaoUnidadeMultipla.selecionar(700, 500);"
                                                                onkeypress="objLupaOrgaoUnidadeMultipla.selecionar(700, 500);"
                                                                src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal(); ?>/pesquisar.svg"
                                                                alt="Selecionar Órgão" title="Selecionar Órgão"
                                                                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-7 col-xl-7">
                                                <div class="form-group">
                                                    <label id="lblUnidades" for="selUnidades"
                                                        class="infraLabelObrigatorio">Unidade:</label>
                                                    <div class="input-group mb-3">
                                                        <input type="text" id="txtUnidadeMultipla" name="txtUnidadeMultipla"
                                                            class="infraText form-control"/>
                                                        <input type="hidden" id="hdnIdUnidadeMultipla"
                                                            name="hdnIdUnidadeMultipla"
                                                            value="<?= $idUnidadeMultipla ?>"/>
                                                        <input type="hidden" id="hdnUfUnidadeMultipla"
                                                            name="hdnUfUnidadeMultipla"
                                                            value=""/>
                                                        <div id="divOpcoesUnidades">
                                                            <img id="imgLupaUnidadeMultipla"
                                                                onclick="verificarOrgaoSelecionado();"
                                                                onkeypress="verificarOrgaoSelecionado();"
                                                                src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal(); ?>/pesquisar.svg"
                                                                alt="Selecionar Unidade" title="Selecionar Unidade"
                                                                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-3 col-md-3 col-lg-2 col-xl-2">
                                                <?php if ($_GET['acao'] != 'md_pet_tipo_processo_consultar') { ?>
                                                    <button type="button" accesskey="A" name="sbmAdicionarUnidade"
                                                            onclick="addUnidade();"
                                                            id="sbmAdicionarUnidade" value="Adicionar" class="infraButton"
                                                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"><span
                                                                class="infraTeclaAtalho">A</span>dicionar
                                                    </button>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Tabela Múltiplas Unidades -->
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-12">
                                            <div class="" id="divTableMultiplasUnidades" <?php echo $divUnidadeMultiplaTable; ?>>
                                                <table width="100%" summary="Tabela de Unidades" class="infraTable"
                                                    id="tableTipoUnidade">
                                                    <caption class="infraCaption">Lista de Unidades (<span
                                                                id="qtdRegistros"><?php echo count($arrObjUnidadesMultiplas) > 0 ? count($arrObjUnidadesMultiplas) : '0'; ?> </span>
                                                        registros):
                                                    </caption>
                                                    <thead>
                                                        <tr>
                                                            <th width="15%" class="infraTh">Órgão</th>
                                                            <th class="infraTh">Unidade</th>
                                                            <th width="15%" class="infraTh">UF da Unidade</th>
                                                            <th class="infraTh">Cidade da Unidade</th>
                                                            <th width="80px" class="infraTh">Ações</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="corpoTabela">
                                                    <?php if ($multipla && isset($hdnCorpoTabela)) { echo $hdnCorpoTabela; } ?>
                                                    <?php
                                                    if ($multipla) {
                                                        if (!empty($arrObjUnidadesMultiplas)) {
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
                                                                <td align="center" valign="middle">
                                                                    <a alt="<?php echo $cadaObjUnidadeDTO->getStrDescricaoOrgao(); ?>"
                                                                    title="<?php echo $cadaObjUnidadeDTO->getStrDescricaoOrgao(); ?>"
                                                                    class="ancoraSigla"><?php echo $cadaObjUnidadeDTO->getStrSiglaOrgao(); ?>
                                                                </td>
                                                                <td align="center" id="tabNomeUnidade">
                                                                    <a alt="<?php echo $cadaObjUnidadeDTO->getStrDescricao(); ?>"
                                                                    title="<?php echo $cadaObjUnidadeDTO->getStrDescricao(); ?>"
                                                                    class="ancoraSigla"><?php echo $cadaObjUnidadeDTO->getStrSigla(); ?>
                                                                    </a>
                                                                </td>
                                                                <td align="center" class="ufsSelecionadas">
                                                                    <?php echo $contatoAssociadoDTO->getStrSiglaUf(); //alteracoes seiv3 ?>
                                                                </td>
                                                                <td align="center" class="cidadesSelecionadas">
                                                                    <?php echo $contatoAssociadoDTO->getStrNomeCidade(); //alteracoes seiv3 ?>
                                                                </td>
                                                                <td align="center">
                                                                    <?php if ($tipoProcessoRestricaoErro) { ?>
                                                                        <img id='alertaRestricao' class='alertaRestricao' width="24" height="24"
                                                                            src='modulos/peticionamento/imagens/svg/icone_contato.svg?'<?= Icone::VERSAO ?>
                                                                            onmouseover='return infraTooltipMostrar("Esta Unidade não pode utilizar o Tipo de Processo indicado, em razão de restrição de uso do Tipo de Processo configurado pela Administração do SEI. Dessa forma, o Usuário Externo não visualiza a opção da UF ou Cidade para abertura do Processo correspondente a esta Unidade. <br><br> Remova a Unidade deste Peticionamento de Processo Novo ou, caso seja pertinente, deve ampliar as restrições de uso do Tipo de Processo para adicionar esta Unidade, no menu Administração > Tipos de Processos > Listar.", "Ajuda");'
                                                                            onmouseout='return infraTooltipOcultar();'/>&nbsp;
                                                                    <?php }
                                                                    if ($tipoProcessoDivergencia) { ?>
                                                                        <img id='alertaDivergencia' class='alertaDivergencia' style="width: 24px"
                                                                            src='modulos/peticionamento/imagens/svg/icone_principal.svg'<?= Icone::VERSAO ?>
                                                                            onmouseover='return infraTooltipMostrar("Posteriormente à parametrização original deste Peticionamento devem ter ocorrido alterações no cadastro das Unidades, de forma que constam conflitos de Unidades com mesma UF ou mesma Cidade. Dessa forma, o Usuário Externo não visualiza a opção da UF ou Cidade para abertura do Processo correspondente às Unidades com tais conflitos.<br><br>Remova a Unidade deste Peticionamento de Processo Novo ou, caso seja pertinente, corrija o cadastro das Unidades para ficar com a UF ou a Cidade corretos, no menu Administração > Unidades > Listar.", "Ajuda");'
                                                                            onmouseout='return infraTooltipOcultar();'/>&nbsp;
                                                                    <?php }
                                                                    if ($_GET['acao'] != 'md_pet_tipo_processo_consultar') { ?>
                                                                        <a>
                                                                            <img title="Remover Unidade"
                                                                                alt="Remover Unidade"
                                                                                src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg"
                                                                                onclick="removerUnidade('<?php echo $idTabela; ?>');"
                                                                                id="imgExcluirProcessoSobrestado">
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
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="row mb-3 ml-0">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <fieldset class="infraFieldset fieldsetInteressado">
                    <legend class="infraLegend">&nbsp;Indicação de Interessado&nbsp;</legend>
                    <div id="divInteressado">
                        <div class="form-group">
                            <div class="divIndicacaoInteressadoProprioUsuario">
                                <input onclick="changeIndicacaoInteressado()" type="radio" id="rdUsuExterno"
                                    name="indicacaoInteressado[]" class="infraRadio"
                                    value="1" <?php echo $sinIndIntUsExt ?>
                                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                <label for="rdUsuExterno" id="lblUsuExterno" class="infraLabelRadio">Próprio Usuário Externo
                                    <img
                                            align="top" class="infraImg"
                                            src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                            name="ajuda" <?= PaginaSEI::montarTitleTooltip('O Usuário Externo logado sempre será o Interessado do processo a ser aberto, sem opção de escolha.', 'Ajuda') ?>
                                    /></label>
                            </div>
                            <div class="divIndicacaoInteressadoIndicacaoDireta">
                                <input onclick="changeIndicacaoInteressado()" type="radio" name="indicacaoInteressado[]"
                                    id="rdIndicacaoIndireta" value="2" <?php echo $sinIndIntIndIndir ?>
                                    class="infraRadio"
                                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                <label name="lblIndicacaoIndireta" id="lblIndicacaoIndireta" for="rdIndicacaoIndireta"
                                    class="infraLabelRadio">Indicação Direta
                                    <img align="top" class="infraImg"
                                        src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                        name="ajuda" <?= PaginaSEI::montarTitleTooltip('O Usuário Externo deverá indicar manualmente o Interessado do processo a ser aberto.', 'Ajuda') ?>
                                    />
                                </label>
                            </div>
                        </div>
                        <div id="divRdIndicacaoIndiretaHide" class="form-group" <?php echo $sinIndIntIndIndir != '' ? 'style="display: inherit; margin: 15px"' : 'style="display: none; margin: 15px"' ?> >
                            <div class="divIndicacaoInteressadoIndicacaoDireta">
                                <input <?php echo $sinIndIntIndCpfCn; ?> type="radio" name="indicacaoIndireta[]"
                                                                         id="indicacaoIndireta1"
                                                                         class="rdIndicacaoIndiretaHide infraRadio"
                                                                         value="3"
                                                                         tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                <label name="lblInformandoCpfCnpj" for="indicacaoIndireta1" id="lblInformandoCpfCnpj"
                                       class="lblIndicacaoIndiretaHide infraLabelRadio">Informando CPF ou CNPJ <img
                                            align="top" class="infraImg"
                                            src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                            name="ajuda" <?= PaginaSEI::montarTitleTooltip('O Usuário Externo indicará o Interessado digitando um CPF ou CNPJ válido. \n \n Se o CPF ou CNPJ digitado não constar na lista de Contatos do SEI ou se existir duplicado, então o Usuário Externo será direcionado a uma janela de Cadastro do Contato que será de fato utilizado como Interessado do processo.', 'Ajuda') ?>
                                    /></label>
                            </div>
                            <div class="divIndicacaoInteressadoIndicacaoDireta">
                                <input <?php echo $sinIndIntIndConta; ?> type="radio" name="indicacaoIndireta[]"
                                                                         id="indicacaoIndireta2"
                                                                         class="rdIndicacaoIndiretaHide infraRadio"
                                                                         value="4"
                                                                         tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                <label for="indicacaoIndireta2" id="lblContatosJaExistentes"
                                       name="lblContatosJaExistentes"
                                       class="lblIndicacaoIndiretaHide infraLabelRadio">Digitando nome de Contatos já
                                    existentes
                                    <img
                                            align="top" class="infraImg"
                                            src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                            name="ajuda" <?= PaginaSEI::montarTitleTooltip('O Usuário Externo indicará o Interessado digitando o Nome ou clicando na Lupa para selecioná-lo dentre os Contatos do SEI, respeitadas as parametrizações na Administração > Peticionamento Eletrônico > Tipos de Contatos Permitidos. \n \n ATENÇÃO: Com esta opção, os Usuários Externos poderão acessar toda a lista de Contatos do SEI do Órgão.', 'Ajuda') ?>
                                    /></label>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
        <div class="row mb-3 ml-0">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <fieldset class="infraFieldset fieldsetAcessoDocumentos">
                    <legend class="infraLegend">&nbsp;Nível de Acesso dos Documentos&nbsp;</legend>
                    <div id="divDocumentos">
                        <div class="form-group">
                            <div class="divAcessoDocumentosIndicadoDiretamente">
                                <input <?php echo $sinNAUsuExt; ?> type="radio" name="rdNivelAcesso[]" class="infraRadio"
                                                                id="rdUsuExternoIndicarEntrePermitidos"
                                                                onclick="changeNivelAcesso();" value="1"
                                                                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">

                                <label for="rdUsuExternoIndicarEntrePermitidos" id="lblUsuExterno" class="infraLabelRadio">Usuário
                                    Externo indica diretamente <img align="top" class="infraImg"
                                                                    src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                                                    name="ajuda" <?= PaginaSEI::montarTitleTooltip('O Usuário Externo terá opção de escolha do Nível de Acesso para cada Documento que adicionar.', 'Ajuda') ?>
                                    /></label>
                            </div>
                            <div class="divAcessoDocumentosPreDefinido">
                                <input <?php echo $sinNAPadrao; ?> type="radio" name="rdNivelAcesso[]" id="rdPadrao"
                                                                class="infraRadio"
                                                                onclick="changeNivelAcesso();" value="2"
                                                                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                <label name="lblPadrao" id="lblPadrao" for="rdPadrao" class="infraLabelRadio">Padrão pré
                                    definido <img align="top" class="infraImg"
                                                src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                                name="ajuda" <?= PaginaSEI::montarTitleTooltip('O Usuário Externo não terá opção de escolha do Nível de Acesso para os Documentos, sendo sempre adicionados com o Nível de Acesso pré definido aqui.', 'Ajuda') ?>
                                    /></label>
                            </div>
                        </div>
                        <div class="divNivelAcesso">
                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-lg-5 col-xl-4">
                                    <div id="divNivelAcesso" <?php echo $sinNAPadrao != '' ? 'style="display: inherit; margin-top: 10px"' : 'style="display: none;"' ?>>
                                        <div class="divNivelAcesso">
                                            <div class="form-group">
                                                <label name="lblNivelAcesso" id="lblNivelAcesso" for="selNivelAcesso"
                                                    class="infraLabelObrigatorio">Nível
                                                    de Acesso: <img align="top" class="infraImg"
                                                                    src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                                                    name="ajuda" <?= PaginaSEI::montarTitleTooltip('As opções abaixo dependem dos Níveis de Acesso Permitidos para o Tipo de Processo escolhido acima. \n \n A opção Sigiloso não é suportada para o Peticionamento de Processo Novo.', 'Ajuda') ?>
                                                    /></label>
                                                <br/>
                                                <select id="selNivelAcesso" name="selNivelAcesso"
                                                        class="infraSelect form-control"
                                                        onchange="changeSelectNivelAcesso()">
                                                    <?= $strItensSelNivelAcesso ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-7 col-xl-8">
                                    <div id="divHipoteseLegal" class="form-group" <?php echo $hipoteseLegal //$sinNAPadrao != '' ? 'style="display: inherit; margin-top:3px"' : 'style="display: none; margin-top:3px"' ?> >
                                        <label name="lblHipoteseLegal" id="lblHipoteseLegal" for="selHipoteseLegal"
                                               class="infraLabelObrigatorio">Hipótese Legal: <img align="top"
                                                                                                  src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                                                                                  name="ajuda" <?= PaginaSEI::montarTitleTooltip('As opções abaixo dependem da parametrização na Administração > Peticionamento Eletrônico > Hipóteses Legais Permitidas.', 'Ajuda') ?>
                                                                                                  class="infraImg"/></label>
                                        <br/>
                                        <select id="selHipoteseLegal" name="selHipoteseLegal"
                                                class="infraSelect form-control">
                                            <?= $strItensSelHipoteseLegal ?>
                                        </select>

                                    </div>
                                </div>
                            </div>
                        </div>
                </fieldset>
            </div>
        </div>
        <div class="row mb-3 ml-0">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <fieldset id="fldDocPrincipal" class="infraFieldset fieldsetAcessoDocumentoPrincipal">
                    <legend class="infraLegend">&nbsp;Documento Principal&nbsp;</legend>
                    <div id="divDocumentoPrincipal">
                        <div class="form-group">
                            <div class="divDocumentoPrincipalGerado">
                                <input type="radio" name="rdDocPrincipal[]" id="rdDocGerado" onclick="changeDocPrincipal();"
                                    value="1" <?php echo $gerado ?> class="infraRadio"
                                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                <label for="rdDocGerado" id="lblDocGerado" class="infraLabelRadio">Gerado (Editor e Modelo)
                                    <img
                                            align="top"
                                            src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                            name="ajuda" <?= PaginaSEI::montarTitleTooltip('O Usuário Externo deverá preencher um Documento de modelo pré definido, utilizando o Editor HTML do SEI. \n \n Neste caso, selecione Tipo de Documento parametrizado na Administração com Aplicabilidade de Documentos Internos ou Internos e Externos. \n \n ATENÇÃO: por limitações técnicas, o Usuário Externo somente visualizará e editará a seção Princial (Corpo do Texto) do modelo do Documento.', 'Ajuda') ?>
                                            class="infraImg"/></label>
                            </div>
                            <div class="divDocumentoPrincipalExterno">
                                <input type="radio" name="rdDocPrincipal[]" id="rdDocExterno"
                                    onclick="changeDocPrincipal();"
                                    value="2" <?php echo $externo ?> class="infraRadio"
                                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                <label name="lblDocExterno" id="lblDocExterno" for="rdDocExterno" class="infraLabelRadio">Externo
                                    (Anexação de
                                    Arquivo) <img align="top"
                                                src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                                name="ajuda" <?= PaginaSEI::montarTitleTooltip('O Usuário Externo deverá anexar um Arquivo como Documento Principal, respeitadas as parametrizações na Administração > Peticionamento Eletrônico > Extensão de Arquivos Permitidos e Tamanho Máximo de Arquivos.') ?>
                                                class="infraImg"/></label>
                            </div>
                        </div>
                        <div <?php echo $gerado != '' || $externo != '' ? 'style="display: inherit;"' : 'style="display: none;"' ?>
                                id="divDocPrincipal">
                            <div class="divSelecionarDocumentoPrincipal">
                                <div class="row">
                                    <div class="col-sm-8 col-md-8 col-lg-7 col-xl-7">
                                        <div class="form-group">
                                            <label name="lblTipoDocPrincipal" id="lblTipoDocPrincipal"
                                                   for="txtTipoDocPrinc"
                                                   class="infraLabelObrigatorio">Tipo do Documento Principal:</label>
                                            <div class="input-group mb-3">
                                                <input type="text" id="txtTipoDocPrinc" name="txtTipoDocPrinc"
                                                    class="infraText form-control"
                                                    value="<?= PaginaSEI::tratarHTML($nomeSerie) ?>"
                                                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                                <input type="hidden" id="hdnIdTipoDocPrinc" name="hdnIdTipoDocPrinc"
                                                    value="<?= $idSerie ?>"/>
                                                <img id="imgLupaTipoDocPrinc"
                                                    onclick="carregarComponenteLupaTpDocPrinc('S');"
                                                    onkeypress="carregarComponenteLupaTpDocPrinc('S');"
                                                    src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/pesquisar.svg"
                                                    alt="Selecionar Tipo de Documento" title="Selecionar Tipo de Documento"
                                                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                                <img id="imgExcluirTipoDocPrinc"
                                                    onclick="carregarComponenteLupaTpDocPrinc('R')"
                                                    onkeypress="carregarComponenteLupaTpDocPrinc('R')"
                                                    src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg"
                                                    alt="Remover Tipo de Documento" title="Remover Tipo de Documento"
                                                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
        <?
        $divDocs = 'style="display: inherit;"';
        ?>
        <fieldset <?php echo $divDocs; ?> id="fldDocEssenciais" class="sizeFieldset tamanhoFieldset fieldNone">
            <div class="row">
                <div class="col-sm-8 col-md-8 col-lg-7 col-xl-6">
                    <label id="lblDescricaoEssencial" for="selDescricaoEssencial" class="infraLabelOpcional">Tipos
                        dos
                        Documentos Essenciais: <img align="top"
                                                    src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                                    name="ajuda" <?= PaginaSEI::montarTitleTooltip('Esta opção não é obrigatória na parametrização, mas se for utilizada o Usuário Externo será obrigado a anexar um Arquivo como Documento Essencial para cada Tipo de Documento que for indicado aqui, respeitadas as parametrizações na Administração > Peticionamento Eletrônico > Extensão de Arquivos Permitidos e Tamanho Máximo de Arquivos.', 'Ajuda') ?>
                                                    class="infraImg"/></label>
                    <input type="text" id="txtSerieEssencial" name="txtSerieEssencial"
                            class="infraText form-control"
                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-10 col-lg-12 col-xl-12">
                    <div class="form-group">
                        <div class="input-group mb-3" style="margin-top: 5px;">
                            <select style="float: left; width: calc(100% - 75px);"
                                    id="selDescricaoEssencial" name="selDescricaoEssencial"
                                    size="8"
                                    multiple="multiple" class="infraSelect form-control"
                                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                <?= $strItensSelSeriesEss; ?>
                            </select>
                            <div class="input-group-prepend divBotoes">
                                <span class="input-group-text input-group-text-semBorda">
                                    <div class="btnConsultarDocumento">
                                        <img id="imgLupaTipoDocumentoEssencial"
                                                onclick="objLupaTipoDocumentoEssencial.selecionar(700, 500)"
                                                onkeypress="objLupaTipoDocumentoEssencial.selecionar(700, 500)"
                                                src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/pesquisar.svg"
                                                alt="Selecionar Tipo de Documento" title="Selecionar Tipo de Documento"
                                                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                    </div>
                                    <div class="btnExcluirDocumento">
                                        <img id="imgExcluirTipoDocumentoEssencial"
                                                onclick="objLupaTipoDocumentoEssencial.remover();"
                                                onkeypress="objLupaTipoDocumentoEssencial.remover();"
                                                src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg"
                                                alt="Remover Tipos de Documentos Selecionados"
                                                title="Remover Tipos de Documentos Selecionados"
                                                tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                    </div>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
        <fieldset <?php echo $divDocs; ?> id="fldDocComplementar"
                                          class="sizeFieldset tamanhoFieldset fieldNone">
            <div class="row">
                <div class="col-sm-8 col-md-8 col-lg-7 col-xl-6">
                    <label id="lblDescricao" for="txtDescricao" class="infraLabelOpcional">Tipos dos Documentos
                        Complementares: <img align="top"
                                                src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/ajuda.svg"
                                                name="ajuda" <?= PaginaSEI::montarTitleTooltip('O Usuário Externo não será obrigado a anexar nenhum Documento Complementar, utilizando-os para anexar Documentos que podem variar conforme cada caso, respeitadas as parametrizações na Administração > Peticionamento Eletrônico > Extensão de Arquivos Permitidos e Tamanho Máximo de Arquivos. \n \n É boa prática indicar o máximo de Tipos de Documentos neste campo.', 'Ajuda') ?>
                                                class="infraImg"/></label>
                    <input type="text" id="txtSerie" name="txtSerie" class="infraText form-control"
                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <div class="form-group">
                        <div class="input-group mb-3" style="margin-top: 5px;">
                            <select style="float: left; width: calc(100% - 75px);" id="selDescricao"
                                    name="selDescricao" size="16" multiple="multiple"
                                    class="infraSelect form-control"
                                    tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>">
                                <?= $strItensSelSeries ?>
                            </select>
                            <div class="input-group-prepend divBotoes">
                                <span class="input-group-text input-group-text-semBorda">
                                    <div class="btnConsultarDocumento">
                                        <img id="imgLupaTipoDocumento"
                                            onclick="carregarComponenteLupaTpDocComplementar('S');"
                                            onkeypress="carregarComponenteLupaTpDocComplementar('S');"
                                            src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/pesquisar.svg"
                                            alt="Selecionar Tipo de Documento" title="Selecionar Tipo de Documento"
                                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                    </div>
                                    <div class="btnExcluirDocumento">
                                        <img id="imgExcluirTipoDocumento"
                                            onclick="carregarComponenteLupaTpDocComplementar('R');"
                                            onkeypress="carregarComponenteLupaTpDocComplementar('R');"
                                            src="<?= PaginaSEI::getInstance()->getDiretorioSvgGlobal() ?>/remover.svg"
                                            alt="Remover Tipos de Documentos Selecionados"
                                            title="Remover Tipos de Documentos Selecionados"
                                            tabindex="<?= PaginaSEI::getInstance()->getProxTabDados() ?>"/>
                                    </div>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
    </div>
    <input type="hidden" name="hdnParametroHipoteseLegal" id="hdnParametroHipoteseLegal"
           value="<?php echo $valorParametroHipoteseLegal; ?>"/>
    <input type="hidden" id="hdnCorpoTabela" name="hdnCorpoTabela" value=""/>
    <input type="hidden" id="hdnUnidadesSelecionadas" name="hdnUnidadesSelecionadas" value=""/>
    <input type="hidden" id="hdnTodasUnidades" name="hdnTodasUnidades"
           value='<?= json_encode($arrObjUnidadeDTOFormatado); ?>'/>
    <input type="hidden" id="hdnIdTipoDocumento" name="hdnIdTipoDocumento" value=""/>
    <input type="hidden" id="hdnSerie" name="hdnSerie" value="<?= $_POST['hdnSerie'] ?>"/>
    <input type="hidden" id="hdnIdTipoDocumento" name="hdnIdTipoDocumento" value="<?= $_POST['hdnIdTipoDocumento'] ?>"/>
    <input type="hidden" id="hdnIdIndisponibilidadePeticionamento" name="hdnIdIndisponibilidadePeticionamento"
           value=""/>
    <input type="hidden" id="hdnIdSerie" name="hdnIdSerie" value="<?= $_POST['hdnIdSerie'] ?>"/>
    <input type="hidden" id="hdnIdSerieEssencial" name="hdnIdSerieEssencial"
           value="<?= $_POST['hdnIdSerieEssencial'] ?>"/>
    <input type="hidden" id="hdnSerieEssencial" name="hdnSerieEssencial" value="<?= $_POST['hdnSerieEssencial'] ?>"/>

    <?
    PaginaSEI::getInstance()->fecharAreaDados();
    PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
    ?>
</form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
require_once 'md_pet_tipo_processo_cadastro_js.php';
?>