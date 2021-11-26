<?
/**
 * ANATEL
 *
 * 16/02/2016 - criado por jaqueline.mendes@cast.com.br - CAST
 *
 */
try {
    require_once dirname(__FILE__) . '/../../SEI.php';

    session_start();

    SessaoSEI::getInstance()->validarLink();

    PaginaSEI::getInstance()->prepararSelecao('tipo_processo_peticionamento_selecionar');

    SessaoSEI::getInstance()->validarPermissao($_GET['acao']);

    switch ($_GET['acao']) {
        case 'md_pet_tipo_processo_excluir':
            try {
                $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
                $arrObjMdPetTipoProcessoDTO = array();

                // varrendo Tipos de Processos para Peticionamento selecionados
                for ($i = 0; $i < count($arrStrIds); $i++) {
                    $objMdPetTipoProcessoDTO = new MdPetTipoProcessoDTO();
                    $objMdPetTipoProcessoDTO->setNumIdTipoProcessoPeticionamento($arrStrIds[$i]);
                    $arrObjMdPetTipoProcessoDTO[] = $objMdPetTipoProcessoDTO;
                }


                //Tipos de Processos para Peticionamento
                $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
                $objMdPetTipoProcessoRN->excluir($arrObjMdPetTipoProcessoDTO);
                PaginaSEI::getInstance()->adicionarMensagem('Opera��o realizada com sucesso.');
            } catch (Exception $e) {
                PaginaSEI::getInstance()->processarExcecao($e);
            }
            header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao_origem'] . '&acao_origem=' . $_GET['acao']));
            die;

        case 'md_pet_tipo_processo_desativar':
            try {
                $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
                $arrObjMdPetTipoProcessoDTO = array();
                for ($i = 0; $i < count($arrStrIds); $i++) {
                    $objMdPetTipoProcessoDTO = new MdPetTipoProcessoDTO();
                    $objMdPetTipoProcessoDTO->setNumIdTipoProcessoPeticionamento($arrStrIds[$i]);
                    $arrObjMdPetTipoProcessoDTO[] = $objMdPetTipoProcessoDTO;
                }
                $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();

                $objMdPetTipoProcessoRN->desativar($arrObjMdPetTipoProcessoDTO);
            } catch (Exception $e) {
                PaginaSEI::getInstance()->processarExcecao($e);
            }
            header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao_origem'] . '&acao_origem=' . $_GET['acao']));
            die;

        case 'md_pet_tipo_processo_reativar':

            $strTitulo = 'Reativar Tipo de Processo';

            if ($_GET['acao_confirmada'] == 'sim') {

                try {
                    $arrStrIds = PaginaSEI::getInstance()->getArrStrItensSelecionados();
                    $arrObjMdPetTipoProcessoDTO = array();
                    $idReativado = 0;
                    for ($i = 0; $i < count($arrStrIds); $i++) {
                        $objMdPetTipoProcessoDTO = new MdPetTipoProcessoDTO();
                        $objMdPetTipoProcessoDTO->setNumIdTipoProcessoPeticionamento($arrStrIds[$i]);
                        $idReativado = $arrStrIds[$i];
                        $arrObjMdPetTipoProcessoDTO[] = $objMdPetTipoProcessoDTO;
                    }
                    $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
                    $objMdPetTipoProcessoRN->reativar($arrObjMdPetTipoProcessoDTO);
                    PaginaSEI::getInstance()->adicionarMensagem('Opera��o realizada com sucesso.');
                } catch (Exception $e) {
                    PaginaSEI::getInstance()->processarExcecao($e);
                }

                $acaoLinhaAmarela = '';

                if ($idReativado != 0) {
                    $acaoLinhaAmarela = '&id_tipo_processo_peticionamento=' . $idReativado . PaginaSEI::getInstance()->montarAncora($idReativado);
                }

                header('Location: ' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao_origem'] . '&acao_origem=' . $_GET['acao'] . $acaoLinhaAmarela));
                die;
            }
            break;

        case 'tipo_processo_peticionamento_selecionar':
            $strTitulo = PaginaSEI::getInstance()->getTituloSelecao('Selecionar Tipo de Processo', 'Selecionar Tipo de Processo');

            //Se cadastrou alguem
            if ($_GET['acao_origem'] == 'md_pet_tipo_processo_cadastrar') {
                if (isset($_GET['id_tipo_processo_peticionamento'])) {
                    PaginaSEI::getInstance()->adicionarSelecionado($_GET['id_tipo_processo_peticionamento']);
                }
            }
            break;

        case 'md_pet_tipo_processo_listar':

            $strTitulo = 'Tipos de Processos para Peticionamento de Processo Novo';
            break;

        default:
            throw new InfraException("A��o '" . $_GET['acao'] . "' n�o reconhecida.");
    }

    $arrComandos = array();

    if ($_GET['acao'] == 'tipo_processo_peticionamento_selecionar') {
        $arrComandos[] = '<button type="button" accesskey="t" id="btnTransportarSelecao" value="Transportar" onclick="infraTransportarSelecao();" class="infraButton"><span class="infraTeclaAtalho">T</span>ransportar</button>';
    }

    $objMdPetTipoProcessoDTO = new MdPetTipoProcessoDTO();
    $objMdPetTipoProcessoDTO->retTodos();
    $objMdPetTipoProcessoDTO->retStrNomeProcesso();

    //NomeProcesso
    if (!(InfraString::isBolVazia($_POST['txtTipoProcesso']))) {
        $objMdPetTipoProcessoDTO->setStrNomeProcesso('%' . $_POST ['txtTipoProcesso'] . '%', InfraDTO::$OPER_LIKE);
    }

    //�rg�o
    if (($_POST['selOrgao'] != '') && $_POST['selOrgao'] != 'null') {
        $objMdPetTipoProcessoDTO->setDistinct(true);

        if ($_POST['selOrgao'] == MdPetTipoProcessoRN::$UNIDADES_MULTIPLAS) {
            //Caso o filtro Multiplo tenha sido escolhido pelo usuario, pegaremos os ids do tipo de processo com orgao multiplo p incluir na pesquisa
            $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
            $objMdPetTipoProcessoOrgaoMultiploDTO = new MdPetTipoProcessoDTO();
            $objMdPetTipoProcessoOrgaoMultiploDTO->retNumIdOrgaoUnidade();
            $objMdPetTipoProcessoOrgaoMultiploDTO->retStrSiglaOrgaoUnidade();
            $objMdPetTipoProcessoOrgaoMultiploDTO->retNumIdTipoProcessoPeticionamento();
            $objMdPetTipoProcessoOrgaoMultiploDTO->retNumIdProcedimento();
            
            $arrTipoProcessoOrgao = $objMdPetTipoProcessoRN->listar($objMdPetTipoProcessoOrgaoMultiploDTO);

            //Se tiver tipo de processo cadastrado eu crio array para verificar os tipos de processos e orgaos cadastrados
            if ($arrTipoProcessoOrgao) {
                $arrTipoProcessoOrgaoMultiplo = array();
                foreach ($arrTipoProcessoOrgao as $orgaoMultiplo) {
                    if (!key_exists($orgaoMultiplo->getNumIdTipoProcessoPeticionamento(), $arrTipoProcessoOrgaoMultiplo)) {
                        $arrTipoProcessoOrgaoMultiplo[$orgaoMultiplo->getNumIdTipoProcessoPeticionamento()] = array();
                    }
                    if (!in_array($orgaoMultiplo->getNumIdOrgaoUnidade(), $arrTipoProcessoOrgaoMultiplo[$orgaoMultiplo->getNumIdTipoProcessoPeticionamento()])) {
                        $arrTipoProcessoOrgaoMultiplo[$orgaoMultiplo->getNumIdTipoProcessoPeticionamento()][] = $orgaoMultiplo->getNumIdOrgaoUnidade();
                    }
                }
            }
            //Se tiver criado o array eu vejo se para algum tipo de processo existe mais de um orgao
            if ($arrTipoProcessoOrgaoMultiplo) {
                foreach ($arrTipoProcessoOrgaoMultiplo as $key => $orgaoMultiplo) {
                    if (count($orgaoMultiplo) > 1) {
                        $arrTipoProcessoOrgaoM[] = $key;
                    }
                }
            }
            //Caso tenha mais que um nesta situa��o eu incluo no filtro da pesquisa
            if (is_array($arrTipoProcessoOrgaoM)) {
                $objMdPetTipoProcessoDTO->setNumIdTipoProcessoPeticionamento($arrTipoProcessoOrgaoM, InfraDTO::$OPER_IN);
            } else {
                $nenhumOrgaoMultiplo = true;
            }
        } else {
            $objMdPetTipoProcessoDTO->setNumIdOrgaoUnidade($_POST['selOrgao']);
        }
    }

    //caso o filtro de �rg�o mude para todos ou vazio ser� limpado o filtro de unidade.
    if ($_POST['selOrgao'] == 'null') {
        $_POST['selUnidade'] = 'null';
    }

    //Unidade
    if (($_POST['selUnidade'] != '') && $_POST['selUnidade'] != 'null') {
        if ($_POST['selUnidade'] == MdPetTipoProcessoRN::$UNIDADES_MULTIPLAS) {
            $objMdPetTipoProcessoDTO->setDistinct(true);
            $objMdPetTipoProcessoDTO->setStrStaTipoUnidade($_POST['selUnidade']);
        } else {
            $objMdPetTipoProcessoDTO->setNumIdUnidade($_POST['selUnidade']);
        }
    }

    //Indica��o Interessado
    if (($_POST['selIndicacaoInteressado'] != '') && $_POST['selIndicacaoInteressado'] != 'null') {
        $vlIndicacaoDireta = $_POST['selIndicacaoInteressado'] == MdPetTipoProcessoRN::$INDICACAO_DIRETA ? 'S' : 'N';
        $vlIndicacaoPrUExt = $_POST['selIndicacaoInteressado'] == MdPetTipoProcessoRN::$PROPRIO_USUARIO_EXTERNO ? 'S' : 'N';

        $objMdPetTipoProcessoDTO->setStrSinIIProprioUsuarioExterno($vlIndicacaoPrUExt);
        $objMdPetTipoProcessoDTO->setStrSinIIIndicacaoDireta($vlIndicacaoDireta);
    }

    //Documento Principal
    if (($_POST['selDocumentoPrincipal'] != '') && $_POST['selDocumentoPrincipal'] != 'null') {
        $vlIndicacaoDocGerado = $_POST['selDocumentoPrincipal'] == MdPetTipoProcessoRN::$DOC_GERADO ? 'S' : 'N';
        $vlIndicacaoDocExterno = $_POST['selDocumentoPrincipal'] == MdPetTipoProcessoRN::$DOC_EXTERNO ? 'S' : 'N';

        $objMdPetTipoProcessoDTO->setStrSinDocGerado($vlIndicacaoDocGerado);
        $objMdPetTipoProcessoDTO->setStrSinDocExterno($vlIndicacaoDocExterno);
    }

    PaginaSEI::getInstance()->prepararOrdenacao($objMdPetTipoProcessoDTO, 'NomeProcesso', InfraDTO::$TIPO_ORDENACAO_ASC);
    PaginaSEI::getInstance()->prepararPaginacao($objMdPetTipoProcessoDTO, 200);


    $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
    $arrObjMdPetTipoProcessoDTO = $objMdPetTipoProcessoRN->listar($objMdPetTipoProcessoDTO);

    //Caso o filtro orgao seja selecionado com Multiplo e n�o exista nenhum tipo de processo com orgao multiplo a pesquisa deve retornar vazia.
    if ($nenhumOrgaoMultiplo) {
        $arrObjMdPetTipoProcessoDTO = null;
    }

    PaginaSEI::getInstance()->processarPaginacao($objMdPetTipoProcessoDTO);

    $numRegistros = count($arrObjMdPetTipoProcessoDTO);

    $strLinkPesquisar = PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'] . '&acao_retorno=md_pet_tipo_processo_listar'));
    $arrComandos[] = '<button type="button" accesskey="p" id="btnPesquisar" value="Pesquisar" onclick="pesquisar();" class="infraButton"><span class="infraTeclaAtalho">P</span>esquisar</button>';

    $arrComandos[] = '<button type="button" accesskey="o" id="btnOrientacoesGerais" value="Orienta��es Gerais" class="infraButton" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_tipo_processo_cadastrar_orientacoes&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'])) . '\'"><span class="infraTeclaAtalho">O</span>rienta��es Gerais</button>';

    $bolAcaoCadastrar = SessaoSEI::getInstance()->verificarPermissao('md_pet_tipo_processo_cadastrar');
    if ($bolAcaoCadastrar) {
        $arrComandos[] = '<button type="button" accesskey="n" id="btnNovo" value="Novo" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_tipo_processo_cadastrar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'])) . '\'" class="infraButton"><span class="infraTeclaAtalho">N</span>ovo</button>';
    }

    if ($numRegistros > 0) {
        $bolCheck = false;

        if ($_GET['acao'] == 'tipo_processo_peticionamento_selecionar') {
            $bolAcaoReativar = false;
            $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pet_tipo_processo_consultar');
            $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('md_pet_tipo_processo_alterar');
            $bolAcaoImprimir = false;
            $bolAcaoExcluir = false;
            $bolAcaoDesativar = false;
            $bolCheck = true;
        } else if ($_GET['acao'] == 'md_pet_tipo_processo_reativar') {
            $bolAcaoReativar = SessaoSEI::getInstance()->verificarPermissao('md_pet_tipo_processo_reativar');
            $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pet_tipo_processo_consultar');
            $bolAcaoAlterar = false;
            $bolAcaoImprimir = true;
            $bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('md_pet_tipo_processo_excluir');
            $bolAcaoDesativar = false;
        } else {
            $bolAcaoReativar = false;
            $bolAcaoConsultar = SessaoSEI::getInstance()->verificarPermissao('md_pet_tipo_processo_consultar');
            $bolAcaoAlterar = SessaoSEI::getInstance()->verificarPermissao('md_pet_tipo_processo_alterar');
            $bolAcaoImprimir = true;
            $bolAcaoExcluir = SessaoSEI::getInstance()->verificarPermissao('md_pet_tipo_processo_excluir');
            $bolAcaoDesativar = SessaoSEI::getInstance()->verificarPermissao('md_pet_tipo_processo_desativar');
        }

        if ($bolAcaoDesativar) {
            $bolCheck = true;
            $strLinkDesativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_tipo_processo_desativar&acao_origem=' . $_GET['acao']);
        }

        $strLinkReativar = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_tipo_processo_reativar&acao_origem=' . $_GET['acao'] . '&acao_confirmada=sim');

        if ($bolAcaoExcluir) {
            $bolCheck = true;
            $strLinkExcluir = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_tipo_processo_excluir&acao_origem=' . $_GET['acao']);
        }

        $strResultado = '';

        if ($_GET['acao'] != 'md_pet_tipo_processo_reativar') {
            $strSumarioTabela = 'Tabela de Tipos de Processos para Peticionamento de Processo Novo';
            $strCaptionTabela = 'Tipos de Processos para Peticionamento de Processo Novo';
        } else {
            $strSumarioTabela = 'Tabela de Tipo de Processos Inativos.';
            $strCaptionTabela = 'Tipos de Processos Inativos';
        }

        $strResultado .= '<table width="99%" class="infraTable" summary="' . $strSumarioTabela . '">' . "\n";
        $strResultado .= '<caption class="infraCaption">' . PaginaSEI::getInstance()->gerarCaptionTabela($strCaptionTabela, $numRegistros) . '</caption>';
        $strResultado .= '<tr>';
        if ($bolCheck) {
            $strResultado .= '<th class="infraTh" width="1%">' . PaginaSEI::getInstance()->getThCheck() . '</th>' . "\n";
        }

        $strResultado .= '<th class="infraTh" width="30%">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetTipoProcessoDTO, 'Tipo de Processo', 'NomeProcesso', $arrObjMdPetTipoProcessoDTO) . '</th>' . "\n";
//        $strResultado .= '<th class="infraTh">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetTipoProcessoDTO, '�rg�o', 'SinIIIndicacaoDireta', $arrObjMdPetTipoProcessoDTO) . '</th>' . "\n";
//        $strResultado .= '<th class="infraTh">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetTipoProcessoDTO, 'Unidade para Abertura', 'SinDocExterno', $arrObjMdPetTipoProcessoDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh">' . '�rg�o' . '</th>' . "\n";
        $strResultado .= '<th class="infraTh">' . 'Unidade para Abertura' . '</th>' . "\n";
        $strResultado .= '<th class="infraTh">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetTipoProcessoDTO, 'Indica��o de Interessado', 'SinIIIndicacaoDireta', $arrObjMdPetTipoProcessoDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh">' . PaginaSEI::getInstance()->getThOrdenacao($objMdPetTipoProcessoDTO, 'Documento Principal', 'SinDocExterno', $arrObjMdPetTipoProcessoDTO) . '</th>' . "\n";
        $strResultado .= '<th class="infraTh" width="15%">A��es</th>' . "\n";
        $strResultado .= '</tr>' . "\n";
        $strCssTr = '';
        $filtroOrgaoMultiplo = false;

        for ($i = 0; $i < $numRegistros; $i++) {
            //Unidade(s)
            $strUnidades = '';
            $arrUnidades = array();
            $arrOrgao = array();

            //Verifica se existe restri��o para o tipo de processo
            $objTipoProcedRestricaoRN = new TipoProcedRestricaoRN();
            $objTipoProcedRestricaoDTO = new TipoProcedRestricaoDTO();
            $objTipoProcedRestricaoDTO->retNumIdOrgao();
            $objTipoProcedRestricaoDTO->retNumIdUnidade();
            $objTipoProcedRestricaoDTO->setNumIdTipoProcedimento($arrObjMdPetTipoProcessoDTO[$i]->getNumIdProcedimento());
            $arrObjTipoProcedRestricaoDTO = $objTipoProcedRestricaoRN->listar($objTipoProcedRestricaoDTO);

            $idOrgaoRestricao = InfraArray::converterArrInfraDTO($arrObjTipoProcedRestricaoDTO, 'IdOrgao');
            $idUnidadeRestricao = InfraArray::converterArrInfraDTO($arrObjTipoProcedRestricaoDTO, 'IdUnidade');
            
            $objMdPetRelTpProcessoUnidRN = new MdPetRelTpProcessoUnidRN();
            $objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
            $objMdPetRelTpProcessoUnidDTO->retTodos();
            $objMdPetRelTpProcessoUnidDTO->retStrsiglaUnidade();
            $objMdPetRelTpProcessoUnidDTO->retStrStaTipoUnidade();
            $objMdPetRelTpProcessoUnidDTO->retStrdescricaoUnidade();
            $objMdPetRelTpProcessoUnidDTO->retNumIdUnidade();
            $objMdPetRelTpProcessoUnidDTO->retNumIdOrgaoUnidade();
            $objMdPetRelTpProcessoUnidDTO->retStrDescricaoOrgao();
            $objMdPetRelTpProcessoUnidDTO->retStrSiglaOrgao();
            $objMdPetRelTpProcessoUnidDTO->retNumIdCidadeContato();
            $objMdPetRelTpProcessoUnidDTO->setNumIdTipoProcessoPeticionamento($arrObjMdPetTipoProcessoDTO[$i]->getNumIdTipoProcessoPeticionamento());
            $arrobjMdPetRelTpProcessoUnidDTO = $objMdPetRelTpProcessoUnidRN->listar($objMdPetRelTpProcessoUnidDTO);

            if ($nenhumOrgaoMultiplo) {
                $arrobjMdPetRelTpProcessoUnidDTO = null;
            }

            $arrTipoProcessoOrgaoCidade = array();
            $tipoProcessoRestricaoErro = false;
            foreach ($arrobjMdPetRelTpProcessoUnidDTO as $objDTO) {
                $siglaUnidade = '';
                $tdUnidade = null;
                $siglaUnidade = $objDTO->getStrsiglaUnidade() != null ? $objDTO->getStrsiglaUnidade() : '';
                $tpUnidadeMult = $objDTO->getStrStaTipoUnidade() == MdPetTipoProcessoRN::$UNIDADES_MULTIPLAS ? true : false;

                if ($tpUnidadeMult) {
                    $strUnidades = 'M�ltiplas';
                    $tdUnidade = '<td valign="middle">' . $strUnidades . '</td>';
                } else {
                    $strUnidades = $siglaUnidade;
                    $tdUnidade = '<td valign="middle"><a alt="' . $objDTO->getStrdescricaoUnidade() . '" title="' . $objDTO->getStrdescricaoUnidade() . '" class="ancoraSigla">' . $strUnidades . '</a></td>';
                }

                //Cria��o do array de unidade para filtro
                if (!in_array($siglaUnidade, $arrUnidades)) {
                    $arrUnidades[$objDTO->getNumIdUnidade()] = $siglaUnidade;
                }

                //Cria��o do array de orgao para filtro
                if (!in_array($objDTO->getStrSiglaOrgao(), $arrOrgao)) {
                    $arrOrgao[$objDTO->getNumIdOrgaoUnidade()] = $objDTO->getStrSiglaOrgao();
                }           

                //Cria��o do array para confirmar se existe para tipo de processo unidades com o mesmo orgao e cidade
                if (!key_exists($objDTO->getNumIdOrgaoUnidade(), $arrTipoProcessoOrgaoCidade)) {
                    $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdOrgaoUnidade()] = array();
                }
                if (!key_exists($objDTO->getNumIdCidadeContato(), $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdOrgaoUnidade()])) {
                    $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdOrgaoUnidade()][$objDTO->getNumIdCidadeContato()] = 1;
                } else {
                    $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdOrgaoUnidade()][$objDTO->getNumIdCidadeContato()] = $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdOrgaoUnidade()][$objDTO->getNumIdCidadeContato()] + 1;
                }
                
                //Verifica se tem alguma unidade ou �rg�o diferente dos restritos
                if(($idOrgaoRestricao && $idOrgaoRestricao[0] != null) && !in_array($objDTO->getNumIdOrgaoUnidade(), $idOrgaoRestricao)){
                    $tipoProcessoRestricaoErro = true;
                }
                if(($idUnidadeRestricao && $idUnidadeRestricao[0] != null) && !in_array($objDTO->getNumIdUnidade(), $idUnidadeRestricao)){
                    $tipoProcessoRestricaoErro = true;
                }
            }
            //verificando se existe algum tipo de processo com divergencia de orgao e cidade iguais
            if ($arrTipoProcessoOrgaoCidade) {
                $tipoProcessoDivergencia = false;
                foreach ($arrTipoProcessoOrgaoCidade as $key => $dados) {
                    foreach ($dados as $qnt) {
                        if ($qnt > 1) {
                            $tipoProcessoDivergencia = true;
                            break;
                        }
                    }
                }
            }

            if ($arrObjMdPetTipoProcessoDTO[$i]->getStrSinAtivo() == 'S' && !$tipoProcessoDivergencia && !$tipoProcessoRestricaoErro) {
                $strCssTr = ($strCssTr == '<tr class="infraTrClara">') ? '<tr class="infraTrEscura">' : '<tr class="infraTrClara">';
            } elseif ($tipoProcessoDivergencia || $tipoProcessoRestricaoErro) {
                $strCssTr = '<tr bgcolor="#F4A460">';
                //F4A460 
                //FFA54F
            } else {
                $strCssTr = '<tr class="trVermelha">';
            }
            $strResultado .= $strCssTr;

            if ($bolCheck) {
                $strResultado .= '<td valign="middle">' . PaginaSEI::getInstance()->getTrCheck($i, $arrObjMdPetTipoProcessoDTO[$i]->getNumIdTipoProcessoPeticionamento(), $arrObjMdPetTipoProcessoDTO[$i]->getStrNomeProcesso()) . '</td>';
            }

            $indicacaoInteressado = $arrObjMdPetTipoProcessoDTO[$i]->getStrSinIIProprioUsuarioExterno() === 'S' ? 'Pr�prio Usu�rio Externo' : 'Indica��o Direta';
            $docExterno = $arrObjMdPetTipoProcessoDTO[$i]->getStrSinDocExterno() === 'S' ? 'Externo' : 'Gerado';
            $strResultado .= '<td valign="middle">' . $arrObjMdPetTipoProcessoDTO[$i]->getStrNomeProcesso() . '</td>';

            //Caso o array de orgaos do tipo de processa tenha mais de 1 op��o o mesmo � m�ltiplo
            if (count($arrOrgao) > 1) {
                $tdOrgao = '<td valign="middle">M�ltiplos</td>';
                $filtroOrgaoMultiplo = true;
            } else {
                $tdOrgao = '<td valign="middle"><a alt="' . $objDTO->getStrDescricaoOrgao() . '" title="' . $objDTO->getStrDescricaoOrgao() . '" class="ancoraSigla">' . $objDTO->getStrSiglaOrgao() . '</a></td>';
            }

            $strResultado .= $tdOrgao;
            $strResultado .= $tdUnidade;
            $strResultado .= '<td valign="middle">' . $indicacaoInteressado . '</td>';
            $strResultado .= '<td valign="middle">' . $docExterno . '</td>';
            $strResultado .= '<td align="center" valign="middle">';

            if ($tipoProcessoDivergencia || $tipoProcessoRestricaoErro) {
                $strResultado .= "<img src='modulos/peticionamento/imagens/icone_restricao.png' onmouseover='return infraTooltipMostrar(\"Neste Tipo de Peticionamento para Processo Novo constam Unidades que n�o podem utilizar o Tipo de Processo indicado, em raz�o de restri��o de uso do Tipo de Processo configurado pela Administra��o do SEI. Dessa forma, o Usu�rio Externo n�o visualiza a op��o da UF ou Cidade para abertura do Processo correspondente � Unidade do conflito.<br><br> Clique na A��o Editar para ver detalhes e sugest�es de provid�ncias.\",\"\");' onmouseout='return infraTooltipOcultar();'/>&nbsp;";
            }

            if ($bolAcaoConsultar) {
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_tipo_processo_consultar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&id_tipo_processo_peticionamento=' . $arrObjMdPetTipoProcessoDTO[$i]->getNumIdTipoProcessoPeticionamento())) . '" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioImagensGlobal() . '/consultar.gif" title="Consultar Tipo de Processo" alt="Consultar Tipo de Processo" class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoAlterar) {

                $strResultado .= '<a href="' . PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_tipo_processo_alterar&acao_origem=' . $_GET['acao'] . '&acao_retorno=' . $_GET['acao'] . '&id_tipo_processo_peticionamento=' . $arrObjMdPetTipoProcessoDTO[$i]->getNumIdTipoProcessoPeticionamento())) . '" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioImagensGlobal() . '/alterar.gif" title="Alterar Tipo de Processo" alt="Alterar Tipo de Processo" class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoDesativar || $bolAcaoReativar || $bolAcaoExcluir) {
                $strId = $arrObjMdPetTipoProcessoDTO[$i]->getNumIdTipoProcessoPeticionamento();
                $strDescricao = PaginaSEI::getInstance()->formatarParametrosJavaScript(PaginaSEI::tratarHTML($arrObjMdPetTipoProcessoDTO[$i]->getStrNomeProcesso()));
            }

            if ($bolAcaoDesativar && $arrObjMdPetTipoProcessoDTO[$i]->getStrSinAtivo() == 'S') {
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="acaoDesativar(\'' . $strId . '\',\'' . $strDescricao . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioImagensGlobal() . '/desativar.gif" title="Desativar Tipo de Processo" alt="Desativar Tipo de Processo" class="infraImg" /></a>&nbsp;';
            } else {
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="acaoReativar(\'' . $strId . '\',\'' . $strDescricao . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioImagensGlobal() . '/reativar.gif" title="Reativar Tipo de Processo" alt="Reativar Tipo de Processo" class="infraImg" /></a>&nbsp;';
            }

            if ($bolAcaoExcluir) {
                $strResultado .= '<a href="' . PaginaSEI::getInstance()->montarAncora($strId) . '" onclick="acaoExcluir(\'' . $strId . '\',\'' . $strDescricao . '\');" tabindex="' . PaginaSEI::getInstance()->getProxTabTabela() . '"><img src="' . PaginaSEI::getInstance()->getDiretorioImagensGlobal() . '/excluir.gif" title="Excluir Tipo de Processo" alt="Excluir Tipo de Processo" class="infraImg" /></a>&nbsp;';
            }

            $strResultado .= '</td></tr>' . "\n";
        }
        $strResultado .= '</table>';
    }

    if ($bolAcaoImprimir) {
        $arrComandos[] = '<button type="button" accesskey="i" id="btnImprimir" value="Imprimir" onclick="infraImprimirTabela();" class="infraButton"><span class="infraTeclaAtalho">I</span>mprimir</button>';
    }

    if ($_GET['acao'] == 'md_pet_tipo_processo_reativar') {
        $arrComandos[] = '<button type="button" accesskey="c" id="btnFecharSelecao" value="Fechar" onclick="window.close();" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
    } else {
        $arrComandos[] = '<button type="button" accesskey="c" name="btnFechar" id="btnFechar" value="Fechar" onclick="location.href=\'' . PaginaSEI::getInstance()->formatarXHTML(SessaoSei::getInstance()->assinarLink('controlador.php?acao=procedimento_controlar&acao_origem=' . $_GET['acao'])) . '\';" class="infraButton">Fe<span class="infraTeclaAtalho">c</span>har</button>';
    }

    $strItensSelIndicacaoInteressado = MdPetTipoProcessoINT::montarSelectIndicacaoInteressadoPeticionamento('', 'Todos', $_POST['selIndicacaoInteressado']);
    $strItensSelTipoDocumento = MdPetTipoProcessoINT::montarSelectTipoDocumento('', 'Todos', $_POST['selDocumentoPrincipal']);
} catch (Exception $e) {
    PaginaSEI::getInstance()->processarExcecao($e);
}

//Campo de filtro Unidade
$objMdPetTipoProcessoUnidadeDTO = new MdPetTipoProcessoDTO();
$objMdPetTipoProcessoUnidadeDTO->retStrSiglaUnidade();
$objMdPetTipoProcessoUnidadeDTO->retNumIdUnidade();
$objMdPetTipoProcessoUnidadeDTO->retNumIdUnidade();
$objMdPetTipoProcessoUnidadeDTO->retStrStaTipoUnidade();
$objMdPetTipoProcessoUnidadeDTO->setOrdStrSiglaUnidade(InfraDTO::$TIPO_ORDENACAO_ASC);
//Caso o filtro �rg�o tenha sido preenchido a listagem ser� exibida conforme a escolha feita pelo usu�rio
if (($_POST['selOrgao'] != '') && $_POST['selOrgao'] != 'null' && $_POST['selOrgao'] != MdPetTipoProcessoRN::$UNIDADES_MULTIPLAS) {
    $objMdPetTipoProcessoUnidadeDTO->setNumIdOrgaoUnidade($_POST['selOrgao']);
}
$arrFiltroUnidade = $objMdPetTipoProcessoRN->listar($objMdPetTipoProcessoUnidadeDTO);

//Verifica se tem algum tipo de processo com multiplas unidades para adicionar a op��o no filtro de unidade
if ($arrFiltroUnidade) {
    $filtroUnidadeMultiplo = false;
    foreach ($arrFiltroUnidade as $unidadeMultiplo) {
        if ($unidadeMultiplo->getStrStaTipoUnidade() == MdPetTipoProcessoRN::$UNIDADES_MULTIPLAS) {
            $filtroUnidadeMultiplo = true;
            break;
        }
    }
}
$arrFiltroUnidade = InfraArray::distinctArrInfraDTO($arrFiltroUnidade, 'IdUnidade');
//Fim campo de filtro unidade
//Campo de filtro �rg�o
$objMdPetTipoProcessoOrgaoDTO = new MdPetTipoProcessoDTO();
$objMdPetTipoProcessoOrgaoDTO->retNumIdOrgaoUnidade();
$objMdPetTipoProcessoOrgaoDTO->retStrSiglaOrgaoUnidade();
$objMdPetTipoProcessoOrgaoDTO->retNumIdTipoProcessoPeticionamento();
$arrFiltroOrgao = $objMdPetTipoProcessoRN->listar($objMdPetTipoProcessoOrgaoDTO);

//Verifica se tem algum tipo de processo com multiplos orgao para adicionar a op��o no filtro de �rg�o
if ($arrFiltroOrgao) {
    $arrOrgaoMultiplo = array();
    foreach ($arrFiltroOrgao as $orgaoMultiplo) {
        if (!key_exists($orgaoMultiplo->getNumIdTipoProcessoPeticionamento(), $arrOrgaoMultiplo)) {
            $arrOrgaoMultiplo[$orgaoMultiplo->getNumIdTipoProcessoPeticionamento()] = array();
        }
        if (!in_array($orgaoMultiplo->getNumIdOrgaoUnidade(), $arrOrgaoMultiplo[$orgaoMultiplo->getNumIdTipoProcessoPeticionamento()])) {
            $arrOrgaoMultiplo[$orgaoMultiplo->getNumIdTipoProcessoPeticionamento()][] = $orgaoMultiplo->getNumIdOrgaoUnidade();
        }
    }
}
if ($arrOrgaoMultiplo) {
    $filtroOrgaoMultiplo = false;
    foreach ($arrOrgaoMultiplo as $orgaoMultiplo) {
        if (count($orgaoMultiplo) > 1) {
            $filtroOrgaoMultiplo = true;
            break;
        }
    }
}
$arrFiltroOrgao = InfraArray::distinctArrInfraDTO($arrFiltroOrgao, 'IdOrgaoUnidade');
$numRegistrosOrgao = count($arrFiltroOrgao);
//Fim campo de filtro �rg�o

PaginaSEI::getInstance()->montarDocType();
PaginaSEI::getInstance()->abrirHtml();
PaginaSEI::getInstance()->abrirHead();
PaginaSEI::getInstance()->montarMeta();
PaginaSEI::getInstance()->montarTitle(':: ' . PaginaSEI::getInstance()->getStrNomeSistema() . ' - ' . $strTitulo . ' ::');
PaginaSEI::getInstance()->montarStyle();
PaginaSEI::getInstance()->abrirStyle();
?>
<?
PaginaSEI::getInstance()->fecharStyle();
PaginaSEI::getInstance()->montarJavaScript();
PaginaSEI::getInstance()->abrirJavaScript();
?>

function inicializar(){
if ('<?= $_GET['acao'] ?>'=='tipo_processo_peticionamento_selecionar'){
infraReceberSelecao();
document.getElementById('btnFecharSelecao').focus();
}else{
document.getElementById('btnFechar').focus();
}
infraEfeitoTabelas();
}

<? if ($bolAcaoDesativar) { ?>
    function acaoDesativar(id,desc){
    if (confirm("Confirma desativa��o do Tipo de Processo para Peticionamento \""+desc+"\"?")){
    document.getElementById('hdnInfraItemId').value=id;
    document.getElementById('frmTipoProcessoPeticionamentoLista').action='<?= $strLinkDesativar ?>';
    document.getElementById('frmTipoProcessoPeticionamentoLista').submit();
    }
    }

    function acaoDesativacaoMultipla(){
    if (document.getElementById('hdnInfraItensSelecionados').value==''){
    alert('Nenhum Tipo de Processo selecionado.');
    return;
    }
    if (confirm("Confirma a desativa��o dos Tipos de Processo selecionados?")){
    document.getElementById('hdnInfraItemId').value='';
    document.getElementById('frmTipoProcessoPeticionamentoLista').action='<?= $strLinkDesativar ?>';
    document.getElementById('frmTipoProcessoPeticionamentoLista').submit();
    }
    }
<? } ?>

function acaoReativar(id,desc){
if (confirm("Confirma reativa��o do Tipo de Processo para Peticionamento \""+desc+"\"?")){
document.getElementById('hdnInfraItemId').value=id;
document.getElementById('frmTipoProcessoPeticionamentoLista').action='<?= $strLinkReativar ?>';
document.getElementById('frmTipoProcessoPeticionamentoLista').submit();
}
}

function acaoReativacaoMultipla(){
if (document.getElementById('hdnInfraItensSelecionados').value==''){
alert('Nenhum Tipo de Processo selecionado.');
return;
}
if (confirm("Confirma a reativa��o dos Tipo de Processos selecionadas?")){
document.getElementById('hdnInfraItemId').value='';
document.getElementById('frmTipoProcessoPeticionamentoLista').action='<?= $strLinkReativar ?>';
document.getElementById('frmTipoProcessoPeticionamentoLista').submit();
}
}

<? if ($bolAcaoExcluir) { ?>
    function acaoExcluir(id,desc){
    if (confirm("Confirma exclus�o do Tipo de Processo para Peticionamento \""+desc+"\"?")){
    document.getElementById('hdnInfraItemId').value=id;
    document.getElementById('frmTipoProcessoPeticionamentoLista').action='<?= $strLinkExcluir ?>';
    document.getElementById('frmTipoProcessoPeticionamentoLista').submit();
    }
    }


    function acaoExclusaoMultipla(){
    if (document.getElementById('hdnInfraItensSelecionados').value==''){
    alert('Nenhuma Tipo de Processo selecionado.');
    return;
    }
    if (confirm("Confirma a exclus�o dos Tipos de Processo selecionados?")){
    document.getElementById('hdnInfraItemId').value='';
    document.getElementById('frmTipoProcessoPeticionamentoLista').action='<?= $strLinkExcluir ?>';
    document.getElementById('frmTipoProcessoPeticionamentoLista').submit();
    }
    }

<? } ?>

function pesquisar(){
document.getElementById('frmTipoProcessoPeticionamentoLista').action='<?= $strLinkPesquisar ?>';
document.getElementById('frmTipoProcessoPeticionamentoLista').submit();
}
<?
PaginaSEI::getInstance()->fecharJavaScript();

//var_dump(count($arrFiltroUnidade),$numRegistrosOrgao,$_POST['selOrgao']);

if (!$numRegistrosOrgao || $numRegistrosOrgao == 1) {
    ?>
    <style type="text/css">
        #lblTipoProcesso {position:absolute;left:0%;top:0%;width:20%;}
        #txtTipoProcesso {position:absolute;left:0%;top:40%;width:20%;}

        #lblUnidade {position:absolute;left:21%;top:0%;width:16%;}
        #selUnidade {position:absolute;left:21%;top:40%;width:16%;}

        #lblIndicacaoInteressado {position:absolute;left:38%;top:0%;width:16%;}
        #selIndicacaoInteressado {position:absolute;left:38%;top:40%;width:16%;}

        #lblDocumentoPrincipal {position:absolute;left:55%;top:0%;width:20%;}
        #selDocumentoPrincipal {position:absolute;left:55%;top:40%;width:20%;}
    </style>
<? } elseif ($numRegistrosOrgao && $numRegistrosOrgao > 1) {
    ?>
    <style type="text/css">
        #lblTipoProcesso {position:absolute;left:0%;top:0%;width:20%;}
        #txtTipoProcesso {position:absolute;left:0%;top:40%;width:20%;}

        #lblOrgao {position:absolute;left:21%;top:0%;width:16%;}
        #selOrgao {position:absolute;left:21%;top:40%;width:16%;}

        #lblUnidade {position:absolute;left:38%;top:0%;width:16%;}
        #selUnidade {position:absolute;left:38%;top:40%;width:16%;}

        #lblIndicacaoInteressado {position:absolute;left:55%;top:0%;width:20%;}
        #selIndicacaoInteressado {position:absolute;left:55%;top:40%;width:20%;}

        #lblDocumentoPrincipal {position:absolute;left:76%;top:0%;width:20%;}
        #selDocumentoPrincipal {position:absolute;left:76%;top:40%;width:20%;}
    </style>
    <?
}
PaginaSEI::getInstance()->fecharHead();
PaginaSEI::getInstance()->abrirBody($strTitulo, 'onload="inicializar();"');
?>
<form id="frmTipoProcessoPeticionamentoLista" method="post" action="<?= PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=' . $_GET['acao'] . '&acao_origem=' . $_GET['acao'])) ?>">

    <?php PaginaSEI::getInstance()->montarBarraComandosSuperior($arrComandos); ?>
    <div style="height:4.5em; margin-top: 11px;" class="infraAreaDados" id="divInfraAreaDados">

        <!--  Tipo de Processo -->
        <label id="lblTipoProcesso" for="txtTipoProcesso" class="infraLabelOpcional">Tipo de Processo:</label>
        <input type="text" name="txtTipoProcesso" id="txtTipoProcesso" value="<?php echo isset($_POST['txtTipoProcesso']) ? PaginaSEI::tratarHTML($_POST['txtTipoProcesso']) : '' ?>"  class="infraText" />

        <?php if ($numRegistrosOrgao > 1) { ?>
            <!-- �rg�o -->
            <label id="lblOrgao" for="txtOrgao"class="infraLabelOpcional">�rg�o:</label>
            <select id="selOrgao" name="selOrgao" onchange="pesquisar();" class="infraSelect" >
                <?php
                echo "<option value='null'>&nbsp;</option>";
                echo "<option value='null' selected='selected'>Todos</option>";
                if ($filtroOrgaoMultiplo) {
                    if ($_POST['selOrgao'] == MdPetTipoProcessoRN::$UNIDADES_MULTIPLAS) {
                        echo "<option value='M' selected='selected'>M�ltiplos</option>";
                    } else {
                        echo "<option value='M'>M�ltiplos</option>";
                    }
                }
                foreach ($arrFiltroOrgao as $objOrgaoDTO) {
                    $selected = "";
                    if ($objOrgaoDTO->getNumIdOrgaoUnidade() == $_POST['selOrgao']) {
                        $selected = "selected='selected'";
                    }
                    echo "<option value=" . $objOrgaoDTO->getNumIdOrgaoUnidade() . " " . $selected . ">" . $objOrgaoDTO->getStrSiglaOrgaoUnidade() . "</option>";
                }
                ?>
            </select>
        <?php } ?>

        <!-- Unidade -->
        <label id="lblUnidade" for="txtUnidade" class="infraLabelOpcional">Unidade para Abertura:</label>
        <select id="selUnidade" name="selUnidade" onchange="pesquisar();" class="infraSelect">
            <?php
            if ((count($arrFiltroUnidade) >= 1 && $numRegistrosOrgao == 1) || ($numRegistrosOrgao > 1 && ($_POST['selOrgao'] != '') && $_POST['selOrgao'] != 'null' && $_POST['selOrgao'] != 'M')) {
                echo "<option value='null'>&nbsp;</option>";
                echo "<option value='' selected='selected'>Todos</option>";
                if ($filtroUnidadeMultiplo) {
                    if ($_POST['selUnidade'] == MdPetTipoProcessoRN::$UNIDADES_MULTIPLAS) {
                        echo "<option value='M' selected='selected'>M�ltiplas</option>";
                    } else {
                        echo "<option value='M'>M�ltiplas</option>";
                    }
                }
                foreach ($arrFiltroUnidade as $objUnidadeDTO) {
                    $selected = "";
                    if ($objUnidadeDTO->getNumIdUnidade() == $_POST['selUnidade']) {
                        $selected = "selected='selected'";
                    }
                    echo "<option value=" . $objUnidadeDTO->getNumIdUnidade() . " " . $selected . ">" . $objUnidadeDTO->getStrSiglaUnidade() . "</option>";
                }
            }
            ?>
        </select> 

        <!--  Indica��o de Interessado -->
        <label id="lblIndicacaoInteressado" for="selIndicacaoInteressado" class="infraLabelOpcional">Indica��o de Interessado:</label>
        <select onchange="pesquisar();" id="selIndicacaoInteressado" name="selIndicacaoInteressado" class="infraSelect" >
            <?= $strItensSelIndicacaoInteressado ?>
        </select> 

        <!--  Select Documento Principal -->
        <label id="lblDocumentoPrincipal" for="selDocumentoPrincipal" class="infraLabelOpcional">Documento Principal:</label>
        <select onchange="pesquisar();"  id="selDocumentoPrincipal" name="selDocumentoPrincipal" class="infraSelect" >
            <?= $strItensSelTipoDocumento ?>
        </select> 

    </div>   
    <?
    PaginaSEI::getInstance()->montarAreaTabela($strResultado, $numRegistros);
    PaginaSEI::getInstance()->montarBarraComandosInferior($arrComandos);
    ?>

</form>
<?
PaginaSEI::getInstance()->fecharBody();
PaginaSEI::getInstance()->fecharHtml();
?>