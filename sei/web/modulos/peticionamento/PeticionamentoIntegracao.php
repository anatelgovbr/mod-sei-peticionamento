<?

/**
 * ANATEL
 *
 * 23/11/2016 - criado por marcelo.bezerra - CAST
 *
 */
require_once dirname(__FILE__) . '/util/MdPetDataUtils.php';

class PeticionamentoIntegracao extends SeiIntegracao
{

    //variavel de instancia, usada para identificar quais documentos tiveram seu acesso autorizado pelo modulo
    private $arrDocumentosLiberados = array();
    private $arrProcessosLiberados = array();
    static $arrProtocoloAcessoExterno = array();
    public static $INTIMACAO_NAO_CUMPRIDA = 0;
    public static $INTIMACAO_CUMPRIDA_PARCIAL = 1;
    public static $INTIMACAO_CUMPRIDA = 2;
    public static $INTIMACAO_NEGADA = 3;

    public function __construct()
    {

    }

    public function getNome()
    {
        return 'SEI Peticionamento, Intimação e Procuração';
    }

    public function getVersao()
    {
        return '4.1.3';
    }

    public function getInstituicao()
    {
        return 'Anatel - Agência Nacional de Telecomunicações';
    }

    public function inicializar($strVersaoSEI)
    {

    }

    public function processarControlador($strAcao)
    {

        switch ($strAcao) {

            case 'md_pet_extensoes_arquivo_cadastrar' :
                require_once dirname(__FILE__) . '/md_pet_extensoes_arquivo_cadastro.php';
                return true;

            case 'md_pet_tamanho_arquivo_cadastrar' :
                require_once dirname(__FILE__) . '/md_pet_tamanho_arquivo_cadastro.php';
                return true;

            case 'md_pet_indisponibilidade_listar' :
            case 'md_pet_indisponibilidade_desativar' :
            case 'md_pet_indisponibilidade_reativar' :
            case 'md_pet_indisponibilidade_excluir' :
                require_once dirname(__FILE__) . '/md_pet_indisponibilidade_lista.php';
                return true;

            case 'md_pet_indisponibilidade_cadastrar':
            case 'md_pet_indisponibilidade_consultar':
            case 'md_pet_indisponibilidade_alterar':
            case 'md_pet_indisponibilidade_upload_anexo':
            case 'md_pet_indisponibilidade_download':
                require_once dirname(__FILE__) . '/md_pet_indisponibilidade_cadastro.php';
                return true;

            case 'md_pet_tipo_processo_listar' :
            case 'md_pet_tipo_processo_desativar' :
            case 'md_pet_tipo_processo_reativar':
            case 'md_pet_tipo_processo_excluir':
                require_once dirname(__FILE__) . '/md_pet_tipo_processo_lista.php';
                return true;

            case 'md_pet_tipo_processo_cadastrar':
            case 'md_pet_tipo_processo_alterar':
            case 'md_pet_tipo_processo_consultar':
            case 'md_pet_tipo_processo_salvar':
                require_once dirname(__FILE__) . '/md_pet_tipo_processo_cadastro.php';
                return true;

            case 'md_pet_tipo_processo_cadastrar_orientacoes':
                require_once dirname(__FILE__) . '/md_pet_tipo_processo_cadastro_orientacoes.php';
                return true;

            case 'md_pet_serie_selecionar':
                require_once dirname(__FILE__) . '/md_pet_serie_lista.php';
                return true;

            case 'md_pet_pessoa_fisica':
                require_once dirname(__FILE__) . '/md_pet_pessoa_fisica.php';
                return true;

            case 'md_pet_pessoa_juridica':
                require_once dirname(__FILE__) . '/md_pet_pessoa_juridica.php';
                return true;

            case 'md_pet_menu_usu_ext_listar' :
            case 'md_pet_menu_usu_ext_desativar' :
            case 'md_pet_menu_usu_ext_reativar':
            case 'md_pet_menu_usu_ext_excluir':
                require_once dirname(__FILE__) . '/md_pet_menu_usu_ext_lista.php';
                return true;

            case 'md_pet_menu_usu_ext_cadastrar':
            case 'md_pet_menu_usu_ext_alterar':
            case 'md_pet_menu_usu_ext_consultar':
                require_once dirname(__FILE__) . '/md_pet_menu_usu_ext_cadastro.php';
                return true;

            case 'md_pet_pagina_conteudo_externo':
                require_once dirname(__FILE__) . '/md_pet_pagina_conteudo_externo.php';
                return true;

            case 'md_pet_tp_ctx_contato_cadastrar':
                require_once dirname(__FILE__) . '/md_pet_tp_ctx_contato_cadastro.php';
                return true;

            case 'md_pet_hipotese_legal_nl_acesso_cadastrar':
                require_once dirname(__FILE__) . '/md_pet_hipotese_legal_nl_acesso_cadastro.php';
                return true;

            case 'md_pet_hipotese_legal_selecionar':
                require_once dirname(__FILE__) . '/md_pet_hipotese_legal_lista.php';
                return true;

            case 'md_pet_intercorrente_criterio_listar' :
            case 'md_pet_intercorrente_criterio_desativar' :
            case 'md_pet_intercorrente_criterio_reativar':
            case 'md_pet_intercorrente_criterio_excluir':
                require_once dirname(__FILE__) . '/md_pet_intercorrente_criterio_lista.php';
                return true;

            case 'md_pet_intercorrente_criterio_cadastrar':
            case 'md_pet_intercorrente_criterio_alterar':
            case 'md_pet_intercorrente_criterio_consultar':
                require_once dirname(__FILE__) . '/md_pet_intercorrente_criterio_cadastro.php';
                return true;

            case 'md_pet_intercorrente_criterio_padrao':
                require_once dirname(__FILE__) . '/md_pet_intercorrente_criterio_padrao.php';
                return true;

            case 'md_pet_arquivo_extensao_selecionar':
                require_once dirname(__FILE__) . '/md_pet_arquivo_extensao_lista.php';
                return true;

            case 'md_pet_int_prazo_tacita_cadastrar':
            case 'md_pet_int_prazo_tacita_alterar':
                require_once dirname(__FILE__) . '/md_pet_int_prazo_tacita_cadastro.php';
                return true;

            case 'md_pet_int_serie_cadastrar':
                require_once dirname(__FILE__) . '/md_pet_int_serie_cadastro.php';
                return true;

            case 'md_pet_int_tipo_resp_cadastrar':
            case 'md_pet_int_tipo_resp_alterar':
            case 'md_pet_int_tipo_resp_consultar':
                require_once dirname(__FILE__) . '/md_pet_int_tipo_resp_cadastro.php';
                return true;

            case 'md_pet_int_tipo_resp_listar':
            case 'md_pet_int_tipo_resp_desativar':
            case 'md_pet_int_tipo_resp_reativar':
            case 'md_pet_int_tipo_resp_excluir':
            case 'md_pet_int_tipo_resp_selecionar':
                require_once dirname(__FILE__) . '/md_pet_int_tipo_resp_lista.php';
                return true;

            case 'md_pet_int_tipo_intimacao_cadastrar':
            case 'md_pet_int_tipo_intimacao_alterar':
            case 'md_pet_int_tipo_intimacao_consultar':
                require_once dirname(__FILE__) . '/md_pet_int_tipo_intimacao_cadastro.php';
                return true;

            case 'md_pet_int_tipo_intimacao_selecionar':
            case 'md_pet_int_tipo_intimacao_listar':
            case 'md_pet_int_tipo_intimacao_desativar':
            case 'md_pet_int_tipo_intimacao_reativar':
            case 'md_pet_int_tipo_intimacao_excluir':
                require_once dirname(__FILE__) . '/md_pet_int_tipo_intimacao_lista.php';
                return true;

            case 'md_pet_orientacoes_tipo_destinatario':
                require_once dirname(__FILE__) . '/md_pet_vinc_orientacoes_tipo_destinatario.php';
                return true;

            case 'md_pet_intimacao_cadastrar':
                require_once dirname(__FILE__) . '/md_pet_intimacao_cadastro.php';
                return true;

            case 'md_pet_intimacao_cadastro_confirmar':
                require_once dirname(__FILE__) . '/md_pet_intimacao_cadastro_confirmar.php';
                return true;

            case 'md_pet_intimacao_consultar':
                require_once dirname(__FILE__) . '/md_pet_intimacao_consulta.php';
                return true;

            case 'md_pet_intimacao_eletronica_listar':
                require_once dirname(__FILE__) . '/md_pet_intimacao_eletronica_lista.php';
                return true;

            case 'md_pet_intimacao_consulta':
                require_once dirname(__FILE__) . '/md_pet_intimacao_consulta.php';
                return true;

            case 'md_pet_int_relatorio_listar':
                require_once dirname(__FILE__) . '/md_pet_int_relatorio_acoes.php';
                return true;

            case 'md_pet_int_relatorio_ht_listar':
                require_once dirname(__FILE__) . '/md_pet_int_relatorio_ht_lista.php';
                return true;

            case 'md_pet_int_relatorio_exp_excel':
                require_once dirname(__FILE__) . '/md_pet_int_relatorio_exp_excel.php';
                return true;

            case 'md_pet_vinc_tp_processo_cadastrar':
                require_once dirname(__FILE__) . '/md_pet_vinc_tp_processo_cadastro.php';
                return true;

            case 'md_pet_integracao_alterar':
                require_once dirname(__FILE__) . '/md_pet_integracao_cadastro.php';
                return true;

            case 'md_pet_integracao_cadastrar':
                require_once dirname(__FILE__) . '/md_pet_integracao_cadastro.php';
                return true;

            case 'md_pet_integracao_consultar':
                require_once dirname(__FILE__) . '/md_pet_integracao_cadastro.php';
                return true;

            case 'md_pet_integracao_listar':
                require_once dirname(__FILE__) . '/md_pet_integracao_lista.php';
                return true;

            case 'md_pet_integracao_desativar':
                require_once dirname(__FILE__) . '/md_pet_integracao_lista.php';
                return true;

            case 'md_pet_integracao_excluir':
                require_once dirname(__FILE__) . '/md_pet_integracao_lista.php';
                return true;

            case 'md_pet_integracao_reativar':
                require_once dirname(__FILE__) . '/md_pet_integracao_lista.php';
                return true;

            case 'md_pet_vinculo_procuracao_visualizar':
                require_once dirname(__FILE__) . '/md_pet_vinculo_procuracao_visualizacao.php';
                return true;

            case 'md_pet_adm_vinc_listar':
            case 'md_pet_adm_vinc_consultar':
                require_once dirname(__FILE__) . '/md_pet_adm_vinc_lista.php';
                return true;

            case 'md_pet_vinpj_doc_procuracao_consultar':
                require_once dirname(__FILE__) . '/md_pet_vinc_doc_procuracao_consulta.php';
                return true;

            case 'md_pet_vinc_listar':
                require_once dirname(__FILE__) . '/md_pet_vinc_lista.php';
                return true;

            case 'md_pet_vinc_responsavel_cadastrar':
                require_once dirname(__FILE__) . '/md_pet_vinc_responsavel_cadastro.php';
                return true;

            case 'md_pet_vinc_usu_ext_pe_cadastrar':
                require_once dirname(__FILE__) . '/md_pet_vinc_responsavel_cadastro.php';
                return true;

            case 'md_pet_vinc_pessoa_cadastrar':
                require_once dirname(__FILE__) . '/md_pet_vinc_pessoa_cadastro.php';
                return true;

            case 'md_pet_vinc_suspender_restabelecer_concluir':
            case 'md_pet_vinc_responsavel_concluir_alt':
                require_once dirname(__FILE__) . '/md_pet_vinc_pe_suspender_restabelecer_concluir.php';
                return true;

            case 'md_pet_vinc_suspender_restabelecer':
                require_once dirname(__FILE__) . '/md_pet_vinc_pe_suspender_restabelecer.php';
                return true;

            case 'md_pet_int_orientacoes_destinatario':
                require_once dirname(__FILE__) . '/md_pet_int_orientacoes_tp_destinatario.php';
                return true;

            case 'md_pet_intimacao_cadastro_fisica':
                require_once dirname(__FILE__) . '/md_pet_intimacao_cadastro_fisica.php';
                return true;

            case 'md_pet_intimacao_cadastro_juridica':
                require_once dirname(__FILE__) . '/md_pet_intimacao_cadastro_juridica.php';
                return true;

            //Tipo de Poderes Legais
            case 'md_pet_tipo_poder_listar':
            case 'md_pet_tipo_poder_desativar':
            case 'md_pet_tipo_poder_reativar':
            case 'md_pet_tipo_poder_excluir':

                require_once dirname(__FILE__) . '/md_pet_tipo_poder.php';
                return true;

            //Tipo de Poderes Legais - Cadastrar,Consultar e Alterar
            case 'md_pet_tipo_poder_cadastrar':
            case 'md_pet_tipo_poder_consultar':
            case 'md_pet_tipo_poder_alterar':

                require_once dirname(__FILE__) . '/md_pet_tipo_poder_cadastro.php';
                return true;

            case 'md_pet_acesso_externo_protocolos':
              require_once 'md_pet_acesso_externo_protocolos.php';
              return true;
        }

        return false;
    }

    public function processarControladorAjax($strAcao)
    {

        $xml = null;

        switch ($_GET['acao_ajax']) {

            case 'md_pet_serie_auto_completar':
                $arrObjSerieDTO = MdPetSerieINT::autoCompletarSeries($_POST['palavras_pesquisa'], $_POST['tipoDoc']);
                $xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjSerieDTO, 'IdSerie', 'Nome');
                break;

            case 'md_pet_tipo_processo_auto_completar':
                $arrObjTipoProcessoDTO = TipoProcedimentoINT::autoCompletarTipoProcedimento($_POST['palavras_pesquisa']);
                $xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjTipoProcessoDTO, 'IdTipoProcedimento', 'Nome');
                break;

            case 'md_pet_tipo_processo_auto_completar_lote':
                $arrObjTipoProcessoDTO = TipoProcedimentoINT::autoCompletarTipoProcedimentoLote($_REQUEST);
                $xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjTipoProcessoDTO, 'IdTipoProcedimento', 'Nome');
                break;

            case 'md_pet_intercorrente_tipo_processo_auto_completar':

				$escaparTiposProcesso = [];

				$listaLimpa = ((isset($_GET['listaLimpa']) && $_GET['listaLimpa'] == 1) || (isset($_POST['listaLimpa']) && $_POST['listaLimpa'] == 1)) ? true : false;

				if($listaLimpa){

					$objMdPetCriterioDTO = new MdPetCriterioDTO();
					$objMdPetCriterioDTO->retNumIdTipoProcedimento();
					$objMdPetCriterioDTO->setStrSinCriterioPadrao('N');
					$objMdPetCriterioDTO->setDistinct(true);
					$arrObjMdPetCriterioDTO = (new MdPetCriterioRN())->listar($objMdPetCriterioDTO);

					if(!empty($arrObjMdPetCriterioDTO)){

						$escaparTiposProcesso =  InfraArray::converterArrInfraDTO($arrObjMdPetCriterioDTO,'IdTipoProcedimento');

						if(is_array($_POST['itens_selecionados']) && count($_POST['itens_selecionados']) > 0){
							$escaparTiposProcesso = array_merge($_POST['itens_selecionados'], $escaparTiposProcesso);
						}

					}

				}

                $arrObjTipoProcessoDTO = MdPetTipoProcessoINT::autoCompletarTipoProcedimento($_POST['palavras_pesquisa'], $escaparTiposProcesso);
                $xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjTipoProcessoDTO, 'IdTipoProcedimento', 'Nome');

                break;

            case 'md_pet_unidade_auto_completar':
                $arrObjUnidadeDTO = UnidadeINT::autoCompletarUnidades($_POST['palavras_pesquisa'], true, '');
                $xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjUnidadeDTO, 'IdUnidade', 'Sigla');
                break;

            case 'md_pet_tipo_processo_nivel_acesso_auto_completar':
                $arrObjNivelAcessoDTO = MdPetTipoProcessoINT::montarSelectNivelAcesso(null, null, null, $_POST['idTipoProcesso']);
                $xml = InfraAjax::gerarXMLSelect($arrObjNivelAcessoDTO);
                break;

            case 'md_pet_tipo_processo_nivel_acesso_validar':
                $xml = MdPetTipoProcessoINT::validarNivelAcesso($_POST);
                break;

            case 'md_pet_tp_ctx_contato_listar':
                $arrObjTipoContextoDTO = MdPetTpCtxContatoINT::montarSelectNome(null, null, $_POST['extensao']);
                $xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjTipoContextoDTO, 'IdTipoContato', 'Nome');
                break;

            case 'md_pet_hipotese_rest_auto_completar':
                $arrObjHipoteseLegalDTO = MdPetHipoteseLegalINT::autoCompletarHipoteseLegal($_POST['palavras_pesquisa'], ProtocoloRN::$NA_RESTRITO);
                $xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjHipoteseLegalDTO, 'IdHipoteseLegal', 'Nome');
                break;

            case 'md_pet_arquivo_extensao_listar_todos':
                $arrObjMdPetArquivoExtensaoDTO = MdPetArquivoExtensaoINT::autoCompletarExtensao($_POST['extensao']);
                $xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjMdPetArquivoExtensaoDTO, 'IdArquivoExtensao', 'Extensao');
                break;

            case 'integracao_tipo_resposta':
                $strOptions = MdPetIntTipoRespINT::montarSelectTipoRespostaEU8612($_POST['tipoResposta']);
                $xml = InfraAjax::gerarXMLSelect($strOptions);
                break;

            case 'busca_tipo_resposta':
                $xml = MdPetIntTipoRespINT::buscaTipoResposta($_POST['id']);
                break;

            case 'confirma_restricao_tipo_processo':
                $xml = MdPetVincTpProcessoINT::confirmarRestricao($_POST['idTipoProcesso'], $_POST['idOrgaoUnidadeMultipla'], $_POST['idUnidadeMultipla']);
                break;

            case 'confirma_restricao_tipo_processo_salvar':
                $xml = MdPetVincTpProcessoINT::confirmarRestricaoSalvar($_POST['idTipoProcesso'], $_POST['idUnidadeMultipla']);
                break;

            case 'retorna_dados_unidade':
                $xml = MdPetVincTpProcessoINT::retornaDadosUnidade($_POST['idUnidadeMultipla']);
                break;

            case 'confirmar_webservice_consultarCnpj':
                $xml = MdPetIntegracaoINT::confirmarWsConsultaDadosCNPJReceitaFederal();
                break;

            case 'md_pet_int_usuario_auto_completar_juridica':
                $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
                $arrContatosDTO = $objMdPetIntimacaoRN->filtrarContatosPesquisaIntimacaoJuridica($_POST);
                
                $xml = ($arrContatosDTO > 0) ? MdPetContatoINT::getContatosNomeAutoCompleteJuridico($arrContatosDTO) : '';
                
                break;

            case 'md_pet_int_usuario_auto_completar_juridica_lote':

                $arrObjContatoDTO = ( new MdPetIntimacaoRN())->filtrarContatosPesquisaIntimacaoJuridicaLote($_REQUEST);
                $xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjContatoDTO,'IdContato', 'Nome');
                break;

            case 'md_pet_int_usuario_auto_completar':
                $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
                $arrContatosDTO = $objMdPetIntimacaoRN->filtrarContatosPesquisaIntimacao($_POST);

                $xml = ($arrContatosDTO > 0) ? MdPetContatoINT::getContatosNomeAutoCompletePF($arrContatosDTO) : '';

                break;

            case 'md_pet_int_usuario_auto_completar_lote':
                $arrObjContatoDTO = (new MdPetIntimacaoRN())->filtrarContatosPesquisaIntimacaoLote($_REQUEST);
                $xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjContatoDTO,'IdContato', 'Nome');
                break;

            case 'md_pet_intimacao_validar_duplicidade':
                $xml = (new MdPetIntimacaoRN())->verificaIntimacaoExistente($_POST);
                break;

            case 'busca_tipo_resposta_intimacao':
                $xml = MdPetIntTipoIntimacaoINT::montaSelectTipoRespostaIntimacao($_POST['paramsBusca']);
                break;

            case 'usuario_dados_tabela':
                $xml = $total = MdPetContatoINT::getDadosContatos($_POST['paramsBusca'], $_POST['paramsIdDocumento']);
                break;

            case 'usuario_dados_tabela_lote':
                $xml = $total = MdPetContatoINT::getDadosContatos($_POST['paramsBusca'], $_POST['paramsIdDocumento']);
                break;

            case 'usuario_dados_tabela_juridica':
                $xml = $total = MdPetContatoINT::getDadosContatosJuridico($_POST['paramsBusca'], $_POST['paramsIdDocumento']);
                break;

            case 'usuario_dados_tabela_juridica_lote':
                $xml = $total = MdPetContatoINT::getDadosContatosJuridicoLote($_POST['paramsBusca'], $_POST['paramsIdDocumento']);
                break;

            case 'md_pet_int_serie_auto_completar':
                $arrObjSerieDTO = MdPetSerieINT::autoCompletarSeriesIntimacao($_POST['palavras_pesquisa']);
                $xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjSerieDTO, 'IdSerie', 'Nome');
                break;

            case 'md_pet_int_validar_cadastro':
                $xml = MdPetIntimacaoINT::validarCadastro(array($_POST['hdnDadosUsuario'], $_POST['tpAcessoSelecao'], $_POST['idProcedimento'], $_POST['stringDocAnex'], $_POST['idDocumento'], $_POST['tipoPessoa']));
                break;

            case 'md_pet_indisp_validar_num_sei':
                $xml = MdPetIndisponibilidadeINT::validarNumeroSEI($_POST['numeroSEI']);
                break;

            case 'md_pet_indisp_validar_periodo':
                $xml = MdPetIndisponibilidadeINT::validarPeriodoIndisp($_POST['dataInicio'], $_POST['dataFim']);
                break;

            case 'md_pet_tp_int_auto_completar':
                $arrObjDTO = MdPetIntTipoIntimacaoINT::autoCompletarTipoIntimacao($_POST['palavras_pesquisa'], true, '');
                $xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjDTO, 'IdMdPetIntTipoIntimacao', 'Nome');
                break;

            case 'md_pet_integracao_busca_operacao_wsdl':
                $xml = MdPetIntegracaoINT::montarXMLBuscarOperacaoWSDL($_POST['endereco_wsdl']);
                break;
            case 'md_pet_integracao_busca_parametro_wsdl':
                $xml = MdPetIntegracaoINT::montarXMLBuscarOperacaoWSDLParametro($_POST['endereco_wsdl'], $_POST['operacao_wsdl'], $_POST['tipo_parametro']);
                break;

            case 'md_pet_vinc_responsavellegal_cpf_consultar' :
                $xml = MdPetIntegracaoRN::consultarContatoCpf($_POST);
                break;

            case 'confirma_restricao_tipo_processo' :
                $xml = MdPetVincTpProcessoINT::confirmarRestricao($_POST['idTipoProcesso'], $_POST['idOrgaoUnidadeMultipla'], $_POST['idUnidadeMultipla']);
                break;

            case 'md_pet_validar_num_sei':
                $xml = MdPetIndisponibilidadeINT::validarNumeroSEI($_POST['numeroSEI']);
                break;

            case 'md_pet_tipo_documento_auto_completar':
                $arrObjSerieDTO = MdPetForcarNivelAcessoDocINT::autoCompletarTipoDocumento(utf8_decode(urldecode($_POST['palavras_pesquisa'])), implode(',', [SerieRN::$TA_EXTERNO, SerieRN::$TA_INTERNO_EXTERNO]));
                $xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjSerieDTO, 'IdSerie', 'Nome');
                break;

            case 'criar_link_assinado_doc_selecionado':
                $url = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_acesso_externo_protocolos&tipo_selecao=2&tipo_protocolo=disponibilizado&id_object=objLupaProtocolosDisponibilizados&id_procedimento=' . $_POST['idProcedimento'] . '&ids_documento=' . implode(",",$_POST['arrIdsDocumentosSelecionados']));
                $xml = '<Documento>';
                $xml .= '<Url>' . $url . '</Url>';
                $xml .= '</Documento>';
                break;
        }

        return $xml;
    }

    public function processarControladorPublicacoes($strAcao)
    {

        switch ($strAcao) {

            case 'abc_publicacao_exemplo':
                require_once dirname(__FILE__) . '/publicacao_exemplo.php';
                return true;
        }

        return false;
    }

    public function processarControladorAjaxExterno($strAcaoAjax)
    {
        $xml = null;

        switch ($strAcaoAjax) {
            case 'md_pet_intercorrente_usu_ext_remover_upload_arquivo':
                $xml = MdPetIntercorrenteProcessoRN::removerArquivoIntecorrenteTemp($_POST['hdnTbDocumento']);
                break;
            case 'md_pet_usu_ext_remover_upload_arquivo':
                $xml = MdPetIntercorrenteProcessoRN::removerArquivoTemp($_POST['hdnTbDocumento']);
                break;
            case 'md_pet_vinc_usu_ext_consulta_receita' :
                $objMdPetIntegracaoRN = new MdPetIntegracaoRN();
                $xml = $objMdPetIntegracaoRN->consultarReceitaWsResponsavelLegal($_POST);
                break;
            case 'md_pet_vinc_usu_ext_consulta_vinculo' :
                $xml = MdPetVinculoINT::validarExistenciaVinculoCnpj($_POST);
                break;
            case 'md_pet_vinc_usu_ext_dados_usuario' :
                $xml = MdPetVincUsuarioExternoINT::consultarDadosUsuario($_POST);
                break;
            case 'md_pet_vinc_usu_ext_dados_usuario_externo' :
                $xml = MdPetVincUsuarioExternoINT::consultarDadosUsuarioExterno($_POST);
                break;

            case 'md_pet_vinc_usu_ext_dados_usuario_externo_procuracao' :
                $xml = MdPetVincUsuarioExternoINT::consultarDadosUsuarioExternoProcuracao($_POST);
                break;
            //Verificaçã de Existencia de Procuração
            case 'md_pet_vinc_usu_ext_dados_usuario_externo_validar_procuracao' :
                $xml = MdPetVincUsuarioExternoINT::validarExistenciaProcuracao($_POST);
                break;
            case 'md_pet_vinc_usu_ext_autocompletar' :
                $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
                $post = $_POST;
                $post['sessao_externa'] = 1;
                $arrContatosDTO = $objMdPetIntimacaoRN->filtrarContatosPesquisaIntimacao($post);
                if ($arrContatosDTO > 0) {
                    $strOptions = MdPetContatoINT::getContatosNomeAutoComplete($arrContatosDTO);

                    $xml = InfraAjax::gerarXMLItensArrInfraDTO($strOptions, 'IdContato', 'Nome', 'Email');
                } else {
                    $xml = '';
                }
                break;
            case 'md_pet_vinc_usu_ext_consulta_contato' :
                $objMdPetContatoRN = new MdPetContatoRN();
                $xml = $objMdPetContatoRN->consultarContato($_POST);
                break;
            case 'md_pet_vinc_usu_ext_usuario_externo' :
                $xml = MdPetIntegracaoRN::consultarContatoCpf($_POST);
                break;
            case 'md_pet_vinc_validar_representante' :
                $xml = MdPetVincRepresentantINT::validarUsuarioResponsavelLegal($_POST);
                break;
            case 'md_pet_vinc_consulta_usuext_valido' :
                $xml = MdPetVincUsuarioExternoINT::consultarUsuarioValido($_POST);
                break;
            case 'md_pet_vinc_consulta_usuext_validacao' :
                $xml = MdPetVincUsuarioExternoINT::verificarUsuarioValido($_POST);
                break;
            case 'md_pet_vinc_consulta_usuext_valido_procuracao' :
                $xml = MdPetVincUsuarioExternoINT::consultarUsuarioValidoProcuracao($_POST);
                break;
            case 'md_pet_validar_resposta':
                $xml = MdPetIntRelTipoRespINT::montarSelectTipoRespostaAjax($_POST);
                $xml = InfraAjax::gerarXMLItensArrInfraDTO($xml, 'IdMdPetIntRelTipoResp', 'Nome');
                break;
            case 'md_pet_consultar_contato_intimacao':
                $xml = MdPetIntRelDestinatarioINT::consultarIntimacao($_POST);
                //$xml = InfraAjax::gerarXMLItensArrInfraDTO($xml, 'IdContato');

                break;
            case 'md_pet_consultar_tipo_processo_uf':
                //Novo
                $xml = MdPetTipoProcessoINT::montarSelectOrgaoTpProcessoOrgaoUf($_POST);
                $xml = InfraAjax::gerarXMLItensArrInfraDTO($xml, 'IdUf', 'SiglaUf');
                break;

            case 'md_pet_consultar_tipo_processo_cidade':
                //Novo
                if (!isset($_POST['idTpProc'])) {
                    $xml = MdPetTipoProcessoINT::montarSelectOrgaoTpProcessoOrgaoCidade($_POST);
                    $xml = InfraAjax::gerarXMLItensArrInfraDTO($xml, 'IdCidade', 'NomeCidade');
                } else {
                    $xml = MdPetTipoProcessoINT::montarSelectOrgaoTpProcessoCidadePetNovo($_POST);
                }
                break;

            case 'md_pet_consultar_tipo_processo_externo':
                //Novo
                $xml = MdPetTipoProcessoINT::montarSelectOrgaoTpProcessoExterno($_POST);
                //$xml = InfraAjax::gerarXMLItensArrInfraDTO($xml, 'IdTipoProcessoPeticionamento','NomeProcesso','Orientacoes');
                break;
            case 'md_pet_validar_procuracao':
                $xml = MdPetIntimacaoINT::validarProcuracao($_POST['idRelDest'], $_POST['idProcedimento']);
                break;

            case 'md_pet_validar_assinatura':
                $xml = MdPetProcessoINT::validarSenhaAssinatura($_POST['strSenha']);
                break;
        }

        return $xml;
    }

    public function processarControladorExterno($strAcao)
    {

        switch ($strAcao) {

            case 'md_pet_pagina_conteudo_externo':
                require_once dirname(__FILE__) . '/md_pet_pagina_conteudo_externo.php';
                return true;

            case 'md_pet_usu_ext_indisponibilidade_listar' :
                require_once dirname(__FILE__) . '/md_pet_usu_ext_indisponibilidade_lista.php';
                return true;

            case 'md_pet_usu_ext_indisponibilidade_consultar':
            case 'md_pet_usu_ext_indisponibilidade_download':
                require_once dirname(__FILE__) . '/md_pet_usu_ext_indisponibilidade_cadastro.php';
                return true;

            case 'md_pet_intercorrente_usu_ext_cadastrar':
                require_once dirname(__FILE__) . '/md_pet_intercorrente_usu_ext_cadastro.php';
                return true;
            case 'md_pet_vinc_usu_ext_negado' :
                require_once dirname(__FILE__) . '/md_pet_vinc_usu_externo_negado.php';
                return true;
            case 'md_pet_intercorrente_usu_ext_concluir':
            case 'md_pet_intercorrente_usu_ext_assinar':
            case 'md_pet_responder_intimacao_usu_ext_concluir':
            case 'md_pet_responder_intimacao_usu_ext_assinar':

            case 'md_pet_vinc_usu_ext_concluir':
            case 'md_pet_vinc_usu_ext_assinar':
                require_once dirname(__FILE__) . '/md_pet_intercorrente_usu_ext_concluir.php';
                return true;

            case 'md_pet_usu_ext_iniciar':
                require_once dirname(__FILE__) . '/md_pet_usu_ext_inicio.php';
                return true;

            case 'md_pet_usu_ext_cadastrar':
                require_once dirname(__FILE__) . '/md_pet_usu_ext_cadastro.php';
                return true;

            case 'peticionamento_usuario_externo_concluir':
                require_once dirname(__FILE__) . '/md_pet_usu_ext_concluir.php';
                return true;

            case 'md_pet_intercorrente_usu_ext_concluir':
                require_once dirname(__FILE__) . '/md_pet_intercorrente_usu_ext_conclusao.php';
                return true;

            case 'md_pet_usu_ext_recibo_listar':
                require_once dirname(__FILE__) . '/md_pet_usu_ext_recibo_lista.php';
                return true;

            case 'md_pet_usu_ext_recibo_consultar':
                require_once dirname(__FILE__) . '/md_pet_usu_ext_recibo_consulta.php';
                return true;

            case 'md_pet_intercorrente_usu_ext_recibo_consultar':
                require_once dirname(__FILE__) . '/md_pet_intercorrente_usu_ext_recibo_consulta.php';
                return true;

            case 'md_pet_interessado_cadastro':
                require_once dirname(__FILE__) . '/md_pet_interessado_cadastro.php';
                return true;

            case 'md_pet_contato_selecionar':
                require_once dirname(__FILE__) . '/md_pet_contato_selecionar.php';
                return true;

            case 'md_pet_usu_ext_upload_anexo':
                if (isset($_FILES['fileArquivoEssencial'])) {
	                LimiteSEI::getInstance()->configurarNivel3();
	                PaginaSEIExterna::getInstance()->processarUpload('fileArquivoEssencial', DIR_SEI_TEMP, true);
                }
                die;

            case 'md_pet_usu_ext_upload_doc_principal':

                if (isset($_FILES['fileArquivoPrincipal'])) {
	                LimiteSEI::getInstance()->configurarNivel3();
                    PaginaSEIExterna::getInstance()->processarUpload('fileArquivoPrincipal', DIR_SEI_TEMP, false);
                }
                die;

            case 'md_pet_usu_ext_upload_doc_essencial':

                if (isset($_FILES['fileArquivoEssencial'])) {
	                LimiteSEI::getInstance()->configurarNivel3();
                    PaginaSEIExterna::getInstance()->processarUpload('fileArquivoEssencial', DIR_SEI_TEMP, false);
                }
                die;

            case 'md_pet_usu_ext_upload_doc_complementar':

                if (isset($_FILES['fileArquivoComplementar'])) {
	                LimiteSEI::getInstance()->configurarNivel3();
                    PaginaSEIExterna::getInstance()->processarUpload('fileArquivoComplementar', DIR_SEI_TEMP, false);
                }
                die;

            case 'md_pet_usu_ext_download':
                require_once dirname(__FILE__) . '/md_pet_usu_ext_cadastro.php';
                return true;

            case 'md_pet_editor_montar':
            case 'md_pet_editor_imagem_upload':
                require_once dirname(__FILE__) . '/md_pet_editor_usuario_externo_processar.php';
                return true;

            case 'md_pet_validar_documento_principal':

                $conteudo = "";

                if (SessaoSEIExterna::getInstance()->isSetAtributo('docPrincipalConteudoHTML')) {
                    $conteudo = SessaoSEIExterna::getInstance()->getAtributo('docPrincipalConteudoHTML');
                }

                echo $conteudo;
                return true;

            case 'md_pet_contato_cpf_cnpj':

                $cpfcnpj = $_POST['cpfcnpj'];
                $cpfcnpj = str_replace(".", "", $cpfcnpj);
                $cpfcnpj = str_replace("-", "", $cpfcnpj);
                $cpfcnpj = str_replace("/", "", $cpfcnpj);

                $objContextoContatoDTO = MdPetContatoINT::getTotalContatoByCPFCNPJ($cpfcnpj);

                $qtdObjContextoContatoDTO = (is_array($objContextoContatoDTO) ? count($objContextoContatoDTO) : 0);
                if ($qtdObjContextoContatoDTO > 0) {
                    $objContato = new stdClass();
                    $objContato->usuario = $objContextoContatoDTO[0]->getNumIdUsuarioCadastro();
                    $objContato->nome = utf8_encode($objContextoContatoDTO[0]->getStrNome());
                    $objContato->id = utf8_encode($objContextoContatoDTO[0]->getNumIdContato());
                    $objContato->nomeTratado = utf8_encode(PaginaSEI::tratarHTML($objContextoContatoDTO[0]->getStrNome()));
                    $json = json_encode($objContato, JSON_FORCE_OBJECT);
                } else {
                    $json = null;
                }

                echo $json;
                return true;
                break;

            case 'md_pet_processo_validar_numero':
                $xml = MdPetIntercorrenteINT::gerarXMLvalidacaoNumeroProcesso($_POST['txtNumeroProcesso']);
                echo $xml;

                return true;

            case 'md_pet_processo_validar_numero_procuracao_simples':
                $xml = MdPetVincRepresentantINT::gerarXMLvalidacaoNumeroProcesso($_POST['txtNumeroProcesso'], $_POST['hdnUsuarioExterno']);
                echo $xml;

                return true;

            case 'md_pet_usu_ext_upload_arquivo':
                if (isset($_FILES['fileArquivo'])) {
	                LimiteSEI::getInstance()->configurarNivel3();
                    PaginaSEIExterna::getInstance()->processarUpload('fileArquivo', DIR_SEI_TEMP, false);
                }
                die;

            case 'md_pet_montar_select_tipo_documento':
                $strOptions = MdPetIntercorrenteINT::montarSelectTipoDocumento('null', ' ', 'null');
                $xml = InfraAjax::gerarXMLSelect($strOptions);
                InfraAjax::enviarXML($xml);

                return true;

            case 'md_pet_montar_select_nivel_acesso':
                $strOptions = MdPetTipoProcessoINT::montarSelectNivelAcesso('null', '', 'null', $_POST['id_tipo_procedimento']);
                $xml = InfraAjax::gerarXMLSelect($strOptions);
                InfraAjax::enviarXML($xml);

                return true;

            case 'md_pet_verificar_criterio_intercorrente':
                $arrNivelAcessoHipoteseLegal = MdPetIntercorrenteINT::verificarCriterioIntercorrente($_POST['idTipoProcedimento']);
                echo json_encode($arrNivelAcessoHipoteseLegal);
                return true;

            //INTIMACAO Inicio
            case 'md_pet_intimacao_usu_ext_listar':
                require_once dirname(__FILE__) . '/md_pet_intimacao_usu_ext_lista.php';

                return true;

            case 'md_pet_responder_intimacao_usu_ext':
                require_once dirname(__FILE__) . '/md_pet_responder_intimacao_usu_ext.php';

                return true;

            case 'md_pet_usu_ext_resposta_upload_anexo':
                if (isset($_FILES['fileArquivo'])) {
                	LimiteSEI::getInstance()->configurarNivel3();
                    PaginaSEIExterna::getInstance()->processarUpload('fileArquivo', DIR_SEI_TEMP, true);
                }
                die;

            case 'md_pet_vinc_usu_ext_upload_anexo':
                if (isset($_FILES['fileArquivo'])) {
	                LimiteSEI::getInstance()->configurarNivel3();
                    PaginaSEIExterna::getInstance()->processarUpload('fileArquivo', DIR_SEI_TEMP, true);
                }
                die;

            case 'md_pet_intimacao_usu_ext_confirmar_aceite':
                require_once dirname(__FILE__) . '/md_pet_intimacao_usu_ext_confirmar_aceite.php';
                return true;

            //INTIMACAO Fim

            case 'md_pet_vinculacao_listar':
                require_once dirname(__FILE__) . '/md_pet_vinculacao_lista.php';
                return true;

            case 'md_pet_vinc_usu_ext_cadastrar':
            case 'md_pet_vinc_usu_ext_alterar':
            case 'md_pet_vinc_usu_ext_consultar':
            case 'md_pet_vinc_usu_ext_salvar':
                require_once dirname(__FILE__) . '/md_pet_vinc_usu_ext_cadastro.php';
                return true;

            case 'md_pet_vinc_pessoa_cadastrar':
                require_once dirname(__FILE__) . '/md_pet_vinc_pessoa_cadastro.php';
                return true;

            case 'md_pet_vinc_usu_ext_pe_listar':
                require_once dirname(__FILE__) . '/md_pet_vinc_usu_ext_lista.php';
                return true;

            case 'md_pet_vinc_usu_ext_pe_cadastrar':
                require_once dirname(__FILE__) . '/md_pet_procuracao_especial_cadastro.php';
                return true;

            case 'processo_eletronico_responder_motivo_revogar':
            case 'processo_eletronico_responder_motivo_renunciar':

                require_once dirname(__FILE__) . '/md_pet_processo_eletronico_motivo.php';
                return true;

            case 'md_pet_vinc_pe_desvinculo_concluir':
                require_once dirname(__FILE__) . '/md_pet_vinc_pe_desvinculo_concluir.php';
                return true;

            case 'md_pet_vinpj_doc_procuracao_consultar':
                require_once dirname(__FILE__) . '/md_pet_vinc_doc_procuracao_consulta.php';
                return true;

            /**
             * Tela de conclusão
             */
            case 'md_pet_usuario_ext_vinc_pj_concluir_cad':
            case 'md_pet_usuario_ext_vinc_pj_concluir_alt':
            case 'md_pet_usuario_ext_vinc_pj_concluir_atos':
                require_once dirname(__FILE__) . '/md_pet_vinc_usu_externo_concluir.php';
                return true;

            case 'peticionamento_usuario_externo_vinc_pe':
                require_once dirname(__FILE__) . '/md_pet_vinc_pe_usu_externo_concluir.php';
                return true;
            //Validação PRocuração 
            case 'peticionamento_usuario_externo_vinc_validacao_procuracao':
                require_once dirname(__FILE__) . '/md_pet_vinc_pe_usu_externo_existencia_proc.php';
                return true;

            case 'md_pet_vincpf_usu_ext_bloco_orientacoes':
                require_once dirname(__FILE__) . '/md_pet_vinc_usu_ext_bloco_orientacoes.php';
                return true;

            case 'md_pet_intimacao_usu_ext_negar_resposta':
                require_once dirname(__FILE__) . '/md_pet_intimacao_usu_ext_negar_resposta.php';
                return true;

            case 'md_pet_intimacao_usu_ext_negar_cumprir':
                require_once dirname(__FILE__) . '/md_pet_intimacao_usu_ext_negar_cumprir.php';
                return true;

            case 'md_pet_intimacao_usu_ext_negar_resposta_peticionar':
                require_once dirname(__FILE__) . '/md_pet_intimacao_usu_ext_negar_resposta_peticionar.php';
                return true;
        }

        return false;
    }

    public function processarControladorWebServices($strServico)
    {

        $strArq = null;

        switch ($strServico) {

            case 'wspeticionamento':
                $strArq = 'wspeticionamento.wsdl';
                break;

            default:
                break;
        }

        if ($strArq != null) {
            $strArq = dirname(__FILE__) . '/ws/' . $strArq;
        }

        return $strArq;
    }

    public function montarIconeProcesso(ProcedimentoAPI $objProcedimentoAPI)
    {
        $arrObjArvoreAcaoItemAPI = array();
        $dblIdProcedimento = $objProcedimentoAPI->getIdProcedimento();
        $arrRetDadosIcones = $this->retornarArrDadosParaIcones($dblIdProcedimento);

        $qtdArrRetDadosIcones = (is_array($arrRetDadosIcones) ? count($arrRetDadosIcones) : 0);
        if ($qtdArrRetDadosIcones > 0) {
            $recibo = $arrRetDadosIcones['recibo'];

            $title = "";
            $icone = "";
            $id = "";
            $tipo = "";
            //recibo mais atual é de resposta a intimaçao
            if (in_array(MdPetReciboRN::$TP_RECIBO_RESPOSTA_INTIMACAO, $recibo)) {

                $reciboIntercorrenteDTO = new MdPetReciboDTO();
                $reciboRN = new MdPetReciboRN();
                $reciboIntercorrenteDTO->retNumIdProtocolo();
                $reciboIntercorrenteDTO->retDblIdDocumento();
                $reciboIntercorrenteDTO->setStrStaTipoPeticionamento(MdPetReciboRN::$TP_RECIBO_RESPOSTA_INTIMACAO);
                $reciboIntercorrenteDTO->retDthDataHoraRecebimentoFinal();
                $reciboIntercorrenteDTO->setNumMaxRegistrosRetorno(1);
                $reciboIntercorrenteDTO->retStrTextoDocumentoPrincipalIntimac();
                $reciboIntercorrenteDTO->setNumIdProtocolo($dblIdProcedimento);
                $reciboIntercorrenteDTO->setOrd('DataHoraRecebimentoFinal', InfraDTO::$TIPO_ORDENACAO_DESC);
                $arrRecibosResposta = $reciboRN->consultar($reciboIntercorrenteDTO);

                $numeroDocPrincipal = $arrRecibosResposta->getStrTextoDocumentoPrincipalIntimac();
                $data = MdPetDataUtils::setFormat($arrRecibosResposta->getDthDataHoraRecebimentoFinal(), 'dd/mm/yyyy');

                $tipoRESP = 'PETICIONAMENTO';
                $idRESP = 'PET' . $dblIdProcedimento;
                $titleRESP = 'Peticionamento Eletrônico\nResposta a Intimação: ' . $data . '\nDocumento Principal: SEI nº ' . $numeroDocPrincipal . '';
                $iconeRESP = 'modulos/peticionamento/imagens/svg/peticionamento_resposta_a_intimacao.svg?'.Icone::VERSAO;

                $objArvoreAcaoItemAPI = new ArvoreAcaoItemAPI();
                $objArvoreAcaoItemAPI->setTipo($tipoRESP);
                $objArvoreAcaoItemAPI->setId($idRESP);
                $objArvoreAcaoItemAPI->setIdPai($dblIdProcedimento);
                $objArvoreAcaoItemAPI->setTitle($titleRESP);
                $objArvoreAcaoItemAPI->setIcone($iconeRESP);
                $objArvoreAcaoItemAPI->setTarget('style');
                $objArvoreAcaoItemAPI->setHref('javascript:;');
                $objArvoreAcaoItemAPI->setSinHabilitado('S');
                $arrObjArvoreAcaoItemAPI[] = $objArvoreAcaoItemAPI;

            } //recibo mais atual é de pet intercorrente
            if (in_array(MdPetReciboRN::$TP_RECIBO_INTERCORRENTE, $recibo)) {

                $reciboIntercorrenteDTO = new MdPetReciboDTO();
                $reciboRN = new MdPetReciboRN();
                $reciboIntercorrenteDTO->retNumIdProtocolo();
                $reciboIntercorrenteDTO->retDblIdDocumento();
                $reciboIntercorrenteDTO->setNumMaxRegistrosRetorno(1);
                $reciboIntercorrenteDTO->setStrStaTipoPeticionamento(MdPetReciboRN::$TP_RECIBO_INTERCORRENTE);
                $reciboIntercorrenteDTO->retDthDataHoraRecebimentoFinal();
                $reciboIntercorrenteDTO->retStrTextoDocumentoPrincipalIntimac();
                $reciboIntercorrenteDTO->setNumIdProtocolo($dblIdProcedimento);
                $reciboIntercorrenteDTO->setOrd('DataHoraRecebimentoFinal', InfraDTO::$TIPO_ORDENACAO_DESC);
                $arrRecibosResposta = $reciboRN->consultar($reciboIntercorrenteDTO);
                $data = MdPetDataUtils::setFormat($arrRecibosResposta->getDthDataHoraRecebimentoFinal(), 'dd/mm/yyyy');


                $titleINT = 'Peticionamento Eletrônico\nIntercorrente: ' . $data;
                $tipoINT = 'PETICIONAMENTO';
                $idINT = 'PET' . $dblIdProcedimento;
                $iconeINT = 'modulos/peticionamento/imagens/svg/peticionamento_intercorrente.svg?'.Icone::VERSAO;

                $objArvoreAcaoItemAPI = new ArvoreAcaoItemAPI();
                $objArvoreAcaoItemAPI->setTipo($tipoINT);
                $objArvoreAcaoItemAPI->setId($idINT);
                $objArvoreAcaoItemAPI->setIdPai($dblIdProcedimento);
                $objArvoreAcaoItemAPI->setTitle($titleINT);
                $objArvoreAcaoItemAPI->setIcone($iconeINT);
                $objArvoreAcaoItemAPI->setTarget(null);
                $objArvoreAcaoItemAPI->setHref('javascript:;');
                $objArvoreAcaoItemAPI->setSinHabilitado('S');
                $arrObjArvoreAcaoItemAPI[] = $objArvoreAcaoItemAPI;
            } //recibo mais atual é de pet de processo novo
            if (in_array(MdPetReciboRN::$TP_RECIBO_NOVO, $recibo)) {

                $reciboIntercorrenteDTO = new MdPetReciboDTO();
                $reciboRN = new MdPetReciboRN();
                $reciboIntercorrenteDTO->retNumIdProtocolo();
                $reciboIntercorrenteDTO->retDblIdDocumento();
                $reciboIntercorrenteDTO->setStrStaTipoPeticionamento(MdPetReciboRN::$TP_RECIBO_NOVO);
                $reciboIntercorrenteDTO->retDthDataHoraRecebimentoFinal();
                $reciboIntercorrenteDTO->retStrTextoDocumentoPrincipalIntimac();
                $reciboIntercorrenteDTO->setNumIdProtocolo($dblIdProcedimento);
                $reciboIntercorrenteDTO->setOrd('DataHoraRecebimentoFinal', InfraDTO::$TIPO_ORDENACAO_DESC);
                $arrRecibosResposta = $reciboRN->consultar($reciboIntercorrenteDTO);
                $data = MdPetDataUtils::setFormat($arrRecibosResposta->getDthDataHoraRecebimentoFinal(), 'dd/mm/yyyy');


                $titleNOVO = 'Peticionamento Eletrônico\nProcesso Novo: ' . $data;
                $tipoNOVO = 'PETICIONAMENTO';
                $idNOVO = 'PET' . $dblIdProcedimento;
                $iconeNOVO = 'modulos/peticionamento/imagens/svg/peticionamento_processo_novo.svg?'.Icone::VERSAO;

                $objArvoreAcaoItemAPI = new ArvoreAcaoItemAPI();
                $objArvoreAcaoItemAPI->setTipo($tipoNOVO);
                $objArvoreAcaoItemAPI->setId($idNOVO);
                $objArvoreAcaoItemAPI->setIdPai($dblIdProcedimento);
                $objArvoreAcaoItemAPI->setTitle($titleNOVO);
                $objArvoreAcaoItemAPI->setIcone($iconeNOVO);
                $objArvoreAcaoItemAPI->setTarget(null);
                $objArvoreAcaoItemAPI->setHref('javascript:;');
                $objArvoreAcaoItemAPI->setSinHabilitado('S');
                $arrObjArvoreAcaoItemAPI[] = $objArvoreAcaoItemAPI;
            }
            $qtdArrRetDadosIcones = (is_array($arrRetDadosIcones['arrMdPetVincRepresentantPJRN']) ? count($arrRetDadosIcones['arrMdPetVincRepresentantPJRN']) : 0);
            if ($qtdArrRetDadosIcones > 0) {

                $titlePJ = $arrRetDadosIcones['textoSeparado'];
                $tipoPJ = 'PETICIONAMENTO';
                $idPJ = 'PET' . $dblIdProcedimento;
                $iconePJ = 'modulos/peticionamento/imagens/svg/peticionamento_processo_novo_azul.svg?'.Icone::VERSAO;

                $objArvoreAcaoItemAPI = new ArvoreAcaoItemAPI();
                $objArvoreAcaoItemAPI->setTipo($tipoPJ);
                $objArvoreAcaoItemAPI->setId($idPJ);
                $objArvoreAcaoItemAPI->setIdPai($dblIdProcedimento);
                $objArvoreAcaoItemAPI->setTitle($titlePJ);
                $objArvoreAcaoItemAPI->setIcone($iconePJ);
                $objArvoreAcaoItemAPI->setTarget(null);
                $objArvoreAcaoItemAPI->setHref('javascript:;');
                $objArvoreAcaoItemAPI->setSinHabilitado('S');
                $arrObjArvoreAcaoItemAPI[] = $objArvoreAcaoItemAPI;
            }

            if ($arrRetDadosIcones['acesso']) {

                $data = $arrRetDadosIcones['dataPF'];
                $titlePF = $arrRetDadosIcones['textoSeparado'];
                $tipoPF = 'PETICIONAMENTO';
                $idPF = 'PET' . $dblIdProcedimento;
                $iconePF = 'modulos/peticionamento/imagens/svg/peticionamento_processo_novo_cinza_.svg?'.Icone::VERSAO;

                $objArvoreAcaoItemAPI = new ArvoreAcaoItemAPI();
                $objArvoreAcaoItemAPI->setTipo($tipoPF);
                $objArvoreAcaoItemAPI->setId($idPF);
                $objArvoreAcaoItemAPI->setIdPai($dblIdProcedimento);
                $objArvoreAcaoItemAPI->setTitle($titlePF);
                $objArvoreAcaoItemAPI->setIcone($iconePF);
                $objArvoreAcaoItemAPI->setTarget(null);
                $objArvoreAcaoItemAPI->setHref('javascript:;');
                $objArvoreAcaoItemAPI->setSinHabilitado('S');
                $arrObjArvoreAcaoItemAPI[] = $objArvoreAcaoItemAPI;

            }


        }

        return $arrObjArvoreAcaoItemAPI;
    }

    //método geral para apoio na montagem de icones para as 3 telas (Controle de Processos, Tela interna/arvore do processo e Acompanhamento Especial)
    private function retornarArrDadosParaIcones($idProcedimento)
    {

        $reciboRN = new MdPetReciboRN();
        $arrDados = array();
        $acesso = false;

        //pegar o recibo mais atual disponivel (caso haja um) e verificar se é de resposta a intimação, intercorrente ou de peticionamento de processo novo e aplicar icone+tooltip correspondente

        $reciboIntercorrenteDTO = new MdPetReciboDTO();
        //$reciboIntercorrenteDTO->setNumMaxRegistrosRetorno(1);
        $reciboIntercorrenteDTO->retNumIdProtocolo();
        $reciboIntercorrenteDTO->retDblIdDocumento();
        $reciboIntercorrenteDTO->retStrStaTipoPeticionamento();
        $reciboIntercorrenteDTO->retDthDataHoraRecebimentoFinal();
        $reciboIntercorrenteDTO->retStrTextoDocumentoPrincipalIntimac();
        $reciboIntercorrenteDTO->setNumIdProtocolo($idProcedimento);
        $reciboIntercorrenteDTO->setOrd('DataHoraRecebimentoFinal', InfraDTO::$TIPO_ORDENACAO_DESC);
        $arrRecibosResposta = $reciboRN->listar($reciboIntercorrenteDTO);

        $tipoPetTodos = InfraArray::converterArrInfraDTO($arrRecibosResposta, 'StaTipoPeticionamento');
        $tipoPet = array($tipoPetTodos[0]);
        $textoSeparado = '';

        $documentos = InfraArray::converterArrInfraDTO($arrRecibosResposta, 'IdDocumento');

        $qtdArrRecibosResposta = (is_array($arrRecibosResposta) ? count($arrRecibosResposta) : 0);

        if ($arrRecibosResposta != null && $qtdArrRecibosResposta > 0) {

            //Vinculo com PF
            $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
            $objMdPetVincRepresentantDTO->setDblIdDocumento($documentos, infraDTO::$OPER_IN);
            $objMdPetVincRepresentantDTO->retNumIdContatoVinc();
            $objMdPetVincRepresentantDTO->retDblIdDocumento();
            $objMdPetVincRepresentantDTO->setDblIdProcedimentoVinculo($idProcedimento);
            $objMdPetVincRepresentantDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES);
            $objMdPetVincRepresentantDTO->setNumMaxRegistrosRetorno(1);
            $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
            $arrMdPetVincRepresentantRN = $objMdPetVincRepresentantRN->consultar($objMdPetVincRepresentantDTO);

            if ($arrMdPetVincRepresentantRN) {

                $reciboIntercorrentePFDTO = new MdPetReciboDTO();
                $reciboPFRN = new MdPetReciboRN();
                $reciboIntercorrentePFDTO->retNumIdProtocolo();
                $reciboIntercorrentePFDTO->setDblIdDocumento($arrMdPetVincRepresentantRN->getDblIdDocumento());
                $reciboIntercorrentePFDTO->setStrStaTipoPeticionamento(MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_EMISSAO);
                $reciboIntercorrentePFDTO->retDthDataHoraRecebimentoFinal();
                $reciboIntercorrentePFDTO->retStrTextoDocumentoPrincipalIntimac();
                $reciboIntercorrentePFDTO->setNumIdProtocolo($idProcedimento);
                $reciboIntercorrentePFDTO->setOrd('DataHoraRecebimentoFinal', InfraDTO::$TIPO_ORDENACAO_DESC);
                $arrRecibosRespostaPF = $reciboPFRN->consultar($reciboIntercorrentePFDTO);

                $objContatoDTO = new ContatoDTO();
                $objContatoDTO->setNumIdContato($arrMdPetVincRepresentantRN->getNumIdContatoVinc());
                $objContatoDTO->setBolExclusaoLogica(false);
                $objContatoDTO->retDblCpf();
                $objContatoDTO->retStrNome();
                $objContatoDTO->retDblCnpj();
                $objContatoRN = new ContatoRN();
                $arrObjContatoRN = $objContatoRN->consultarRN0324($objContatoDTO);

                if (!is_null($arrObjContatoRN->getDblCpf())) {
                    $dataPF = MdPetDataUtils::setFormat($arrRecibosRespostaPF->getDthDataHoraRecebimentoFinal(), 'dd/mm/yyyy');
                    $cpf = $arrObjContatoRN->getDblCpf();
                    $nome = $arrObjContatoRN->getStrNome();
                    $acesso = true;
                }

            }

            //Vinculo com PF - FIM
            $linhaDeCima = '';
            $linhaDeBaixo = '';
            $img = '';

            //Tipos de recibos para pegar a data do "Último Peticionamento de Atualização".
            $arrTipoReciboVinculacao = array(
                MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_EMISSAO,
                MdPetReciboRN::$TP_RECIBO_ATUALIZACAO_ATOS_CONSTITUTIVOS,
                MdPetReciboRN::$TP_RECIBO_RESPONSAVEL_LEGAL_ALTERACAO,
                MdPetReciboRN::$TP_RECIBO_RESPONSAVEL_LEGAL_INICIAL,
                MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_REVOGACAO,
                MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_RENUNCIA
            );

            //recibo mais atual é de resposta a intimaçao
            if (in_array(MdPetReciboRN::$TP_RECIBO_RESPOSTA_INTIMACAO, $tipoPet)) {

                $reciboIntercorrenteDTO = new MdPetReciboDTO();
                $reciboIntercorrenteDTO->retNumIdProtocolo();
                $reciboIntercorrenteDTO->retStrStaTipoPeticionamento();
                $reciboIntercorrenteDTO->setNumMaxRegistrosRetorno(1);
                $reciboIntercorrenteDTO->retDthDataHoraRecebimentoFinal();
                $reciboIntercorrenteDTO->retStrTextoDocumentoPrincipalIntimac();
                $reciboIntercorrenteDTO->setStrStaTipoPeticionamento(MdPetReciboRN::$TP_RECIBO_RESPOSTA_INTIMACAO);
                $reciboIntercorrenteDTO->setOrd('DataHoraRecebimentoFinal', InfraDTO::$TIPO_ORDENACAO_DESC);
                $reciboIntercorrenteDTO->setNumIdProtocolo($idProcedimento);
                $arrRecibosResposta = $reciboRN->consultar($reciboIntercorrenteDTO);

                $data = MdPetDataUtils::setFormat($arrRecibosResposta->getDthDataHoraRecebimentoFinal(), 'dd/mm/yyyy');
                $numeroDocPrincipal = $arrRecibosResposta->getStrTextoDocumentoPrincipalIntimac();
                $linhaDeCima = '"Peticionamento Eletrônico"';
                $linhaDeBaixo = '"Resposta a Intimação: ' . $data . '\nDocumento Principal: SEI nº ' . $numeroDocPrincipal . '"';
                $img .= "<img src='modulos/peticionamento/imagens/svg/peticionamento_resposta_a_intimacao.svg?".Icone::VERSAO."' onmouseout='return infraTooltipOcultar();' onmouseover='return infraTooltipMostrar(" . $linhaDeBaixo . "," . $linhaDeCima . ");' style='width:24px;' />";

            } //recibo mais atual é de peticionamento intercorrente

            if (in_array(MdPetReciboRN::$TP_RECIBO_INTERCORRENTE, $tipoPet)) {

                $reciboIntercorrenteDTO = new MdPetReciboDTO();
                $reciboIntercorrenteDTO->retNumIdProtocolo();
                $reciboIntercorrenteDTO->setNumMaxRegistrosRetorno(1);
                $reciboIntercorrenteDTO->retStrStaTipoPeticionamento();
                $reciboIntercorrenteDTO->retDthDataHoraRecebimentoFinal();
                $reciboIntercorrenteDTO->retStrTextoDocumentoPrincipalIntimac();
                $reciboIntercorrenteDTO->setStrStaTipoPeticionamento(MdPetReciboRN::$TP_RECIBO_INTERCORRENTE);
                $reciboIntercorrenteDTO->setNumIdProtocolo($idProcedimento);
                $reciboIntercorrenteDTO->setOrd('DataHoraRecebimentoFinal', InfraDTO::$TIPO_ORDENACAO_DESC);
                $arrRecibosResposta = $reciboRN->consultar($reciboIntercorrenteDTO);

                $data = MdPetDataUtils::setFormat($arrRecibosResposta->getDthDataHoraRecebimentoFinal(), 'dd/mm/yyyy');
                $linhaDeCima = '"Peticionamento Eletrônico"';
                $linhaDeBaixo = '"Intercorrente: ' . $data . '"';
                $img .= "<img src='modulos/peticionamento/imagens/svg/peticionamento_intercorrente.svg?".Icone::VERSAO."' onmouseout='return infraTooltipOcultar();' onmouseover='return infraTooltipMostrar(" . $linhaDeBaixo . "," . $linhaDeCima . ");' style='width:24px;' />";

            } //recibo mais atual é de peticionamento de processo novo

            if (in_array(MdPetReciboRN::$TP_RECIBO_NOVO, $tipoPet)) {

                $reciboIntercorrenteDTO = new MdPetReciboDTO();
                $reciboIntercorrenteDTO->retNumIdProtocolo();
                $reciboIntercorrenteDTO->retStrStaTipoPeticionamento();
                $reciboIntercorrenteDTO->retDthDataHoraRecebimentoFinal();
                $reciboIntercorrenteDTO->retStrTextoDocumentoPrincipalIntimac();
                $reciboIntercorrenteDTO->setStrStaTipoPeticionamento(MdPetReciboRN::$TP_RECIBO_NOVO);
                $reciboIntercorrenteDTO->setOrd('DataHoraRecebimentoFinal', InfraDTO::$TIPO_ORDENACAO_DESC);
                $reciboIntercorrenteDTO->setNumIdProtocolo($idProcedimento);
                $arrRecibosResposta = $reciboRN->consultar($reciboIntercorrenteDTO);

                $linhaDeCima = '"Peticionamento Eletrônico"';
                $linhaDeBaixo = '"Processo Novo: ' . MdPetDataUtils::setFormat($arrRecibosResposta->getDthDataHoraRecebimentoFinal(), 'dd/mm/yyyy') . '"';
                $img .= "<img src='modulos/peticionamento/imagens/svg/peticionamento_processo_novo.svg?".Icone::VERSAO."' onmouseout='return infraTooltipOcultar();' onmouseover='return infraTooltipMostrar(" . $linhaDeBaixo . "," . $linhaDeCima . ");' style='width:24px;'  />";

            }

            if (in_array(MdPetReciboRN::$TP_RECIBO_RESPONSAVEL_LEGAL_INICIAL, $tipoPetTodos)) {

                //Vinculo PJ
                $reciboIntercorrenteDTO = new MdPetReciboDTO();
                $reciboIntercorrenteDTO->retNumIdProtocolo();
                $reciboIntercorrenteDTO->retDblIdDocumento();
                $reciboIntercorrenteDTO->retStrStaTipoPeticionamento();
                $reciboIntercorrenteDTO->retDthDataHoraRecebimentoFinal();
                $reciboIntercorrenteDTO->retStrTextoDocumentoPrincipalIntimac();
                //$reciboIntercorrenteDTO->setOrd('DataHoraRecebimentoFinal', InfraDTO::$TIPO_ORDENACAO_DESC);
                $reciboIntercorrenteDTO->setStrStaTipoPeticionamento(MdPetReciboRN::$TP_RECIBO_RESPONSAVEL_LEGAL_INICIAL);
                $reciboIntercorrenteDTO->setNumIdProtocolo($idProcedimento);
                $arrRecibosResposta = $reciboRN->listar($reciboIntercorrenteDTO);

                $objMdPetVincRepresentantPJDTO = new MdPetVincRepresentantDTO();
                $objMdPetVincRepresentantPJDTO->setDblIdDocumento(InfraArray::converterArrInfraDTO($arrRecibosResposta, 'IdDocumento'), infraDTO::$OPER_IN);
                $objMdPetVincRepresentantPJDTO->retNumIdContatoVinc();
                $objMdPetVincRepresentantPJDTO->retDthDataCadastro();
                $objMdPetVincRepresentantPJDTO->retNumIdMdPetVinculoRepresent();
                $objMdPetVincRepresentantPJDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL);
                $objMdPetVincRepresentantPJRN = new MdPetVincRepresentantRN();
                $arrMdPetVincRepresentantPJRN = $objMdPetVincRepresentantPJRN->listar($objMdPetVincRepresentantPJDTO);

                $qtdArrMdPetVincRepresentantPJRN = (is_array($arrMdPetVincRepresentantPJRN) ? count($arrMdPetVincRepresentantPJRN) : 0);

                if ($qtdArrMdPetVincRepresentantPJRN > 0) {

                    $objMdPetVincDocumentoRN = new MdPetVincDocumentoRN();
                    $objMdPetVincDocumentoDTO = new MdPetVincDocumentoDTO();
                    $objMdPetVincDocumentoDTO->setNumIdMdPetVinculoRepresent(InfraArray::converterArrInfraDTO($arrMdPetVincRepresentantPJRN, 'IdMdPetVinculoRepresent'), infraDTO::$OPER_IN);
                    $objMdPetVincDocumentoDTO->retDblIdDocumento();
                    $objMdPetVincDocumentoDTO->retStrProtocoloFormatadoProtocolo();
                    //Mudar para Constante
                    $objMdPetVincDocumentoDTO->setNumIdSerie(381);
                    $objMdPetVincDocumentoDTO->retNumIdMdPetVinculo();
                    $objMdPetVincDocumentoDTO->retStrNomeSerieProtocolo();
                    $objMdPetVincDocumentoDTO->retStrTipoDocumento();
                    $objMdPetVincDocumentoDTO->retNumIdMdPetVinculoRepresent();
                    $objMdPetVincDocumentoDTO->setNumMaxRegistrosRetorno(1);
                    $arrObjMdPetVincDocumentoDTO = $objMdPetVincDocumentoRN->consultar($objMdPetVincDocumentoDTO);

                    $qtdArrMdPetVincRepresentantPJRN = (is_array($arrMdPetVincRepresentantPJRN) ? count($arrMdPetVincRepresentantPJRN) : 0);

                    if ($qtdArrMdPetVincRepresentantPJRN > 0) {

                        $reciboIntercorrenteDTO = new MdPetReciboDTO();
                        $reciboIntercorrenteDTO->retNumIdProtocolo();
                        $reciboIntercorrenteDTO->retDblIdDocumento();
                        $reciboIntercorrenteDTO->retStrStaTipoPeticionamento();
                        $reciboIntercorrenteDTO->setNumMaxRegistrosRetorno(1);
                        $reciboIntercorrenteDTO->retDthDataHoraRecebimentoFinal();
                        $reciboIntercorrenteDTO->setOrd('DataHoraRecebimentoFinal', InfraDTO::$TIPO_ORDENACAO_DESC);
                        $reciboIntercorrenteDTO->retStrTextoDocumentoPrincipalIntimac();
                        $reciboIntercorrenteDTO->setStrStaTipoPeticionamento($arrTipoReciboVinculacao, InfraDTO::$OPER_IN);
                        $reciboIntercorrenteDTO->setNumIdProtocolo($idProcedimento);
                        $arrRecibosResposta = $reciboRN->consultar($reciboIntercorrenteDTO);

                        $qtdArrRecibosResposta = (is_array($arrRecibosResposta) ? count($arrRecibosResposta) : 0);
                        if ($qtdArrRecibosResposta > 0) {
                            $dataPJ = MdPetDataUtils::setFormat($arrRecibosResposta->getDthDataHoraRecebimentoFinal(), 'dd/mm/yyyy Y:i:s');
                        } else {
                            $dataPJ = MdPetDataUtils::setFormat($arrMdPetVincRepresentantPJRN[0]->getDthDataCadastro(), 'dd/mm/yyyy Y:i:s');
                        }

                        $objContatoDTO = new ContatoDTO();
                        $objContatoDTO->setNumIdContato(InfraArray::converterArrInfraDTO($arrMdPetVincRepresentantPJRN, 'IdContatoVinc'), infraDTO::$OPER_IN);
                        $objContatoDTO->setBolExclusaoLogica(false);
                        $objContatoDTO->retStrNome();
                        $objContatoDTO->retDblCnpj();
                        $objContatoDTO->setNumMaxRegistrosRetorno(1);
                        $objContatoRN = new ContatoRN();
                        $arrObjContatoRN = $objContatoRN->consultarRN0324($objContatoDTO);

                        $linhaDeCimaPJ = '"Controle de Representação de Pessoa Jurídica"';
                        $linhaDeBaixoPJ .= '"' . PaginaSEI::tratarHTML($arrObjContatoRN->getStrNome()) . ' (' . infraUtil::formatarCnpj($arrObjContatoRN->getDblCnpj()) . ')<br/><br/> Último Peticionamento de Atualização: ' . $dataPJ . '"';
                        $linhaDeCimaTxt = 'Controle de Representação de Pessoa Jurídica\n' . PaginaSEI::tratarHTML($arrObjContatoRN->getStrNome()) . ' (' . infraUtil::formatarCnpj($arrObjContatoRN->getDblCnpj()) . ')';
                        $linhaDeBaixoTxt = 'Último Peticionamento de Atualização: ' . $dataPJ;
                        $textoSeparado = $linhaDeCimaTxt . ' \n' . $linhaDeBaixoTxt;

                    }

                    $img .= "<img src='modulos/peticionamento/imagens/svg/peticionamento_processo_novo_azul.svg?".Icone::VERSAO."' onmouseout='return infraTooltipOcultar();' onmouseover='return infraTooltipMostrar(" . $linhaDeBaixoPJ . "," . $linhaDeCimaPJ . ");' style='width:24px' />";

                }

            }

            if ($acesso) {

                $reciboIntercorrenteDTO = new MdPetReciboDTO();
                $reciboIntercorrenteDTO->retNumIdProtocolo();
                $reciboIntercorrenteDTO->retDblIdDocumento();
                $reciboIntercorrenteDTO->retStrStaTipoPeticionamento();
                $reciboIntercorrenteDTO->setNumMaxRegistrosRetorno(1);
                $reciboIntercorrenteDTO->retDthDataHoraRecebimentoFinal();
                $reciboIntercorrenteDTO->setOrd('DataHoraRecebimentoFinal', InfraDTO::$TIPO_ORDENACAO_DESC);
                $reciboIntercorrenteDTO->retStrTextoDocumentoPrincipalIntimac();
                $reciboIntercorrenteDTO->setStrStaTipoPeticionamento($arrTipoReciboVinculacao, InfraDTO::$OPER_IN);
                $reciboIntercorrenteDTO->setNumIdProtocolo($idProcedimento);
                $arrRecibosResposta = $reciboRN->consultar($reciboIntercorrenteDTO);

                $linhaDeCimaPF = '"Controle de Representação de Pessoa Física"';
                $linhaDeBaixoPF .= '"' . PaginaSEI::tratarHTML($nome) . ' (' . infraUtil::formatarCpf($cpf) . ')<br/><br/>Último Peticionamento de Atualização: ' . MdPetDataUtils::setFormat($arrRecibosResposta->getDthDataHoraRecebimentoFinal(), 'dd/mm/yyyy Y:i:s') . '"';
                $linhaDeCimaTexto = 'Controle de Representação de Pessoa Física\n' . PaginaSEI::tratarHTML($nome) . ' (' . infraUtil::formatarCpf($cpf) . ')';
                $linhaDeBaixoTexto .= 'Último Peticionamento de Atualização: ' . MdPetDataUtils::setFormat($arrRecibosResposta->getDthDataHoraRecebimentoFinal(), 'dd/mm/yyyy Y:i:s');
                $textoSeparado = $linhaDeCimaTexto . ' \n' . $linhaDeBaixoTexto;
                $img .= "<img src='modulos/peticionamento/imagens/svg/peticionamento_processo_novo_cinza_.svg?".Icone::VERSAO."' onmouseout='return infraTooltipOcultar();' onmouseover='return infraTooltipMostrar(" . $linhaDeBaixoPF . "," . $linhaDeCimaPF . ");' style='width:24px;' />";

            }

            $arrDados['dataPJ'] = $dataPJ;
            $arrDados['dataPF'] = $dataPF;
            $arrDados['vincPF'] = $acesso;
            $arrDados['recibo'] = $tipoPet;
            $arrDados['data'] = $data;
            $arrDados['linhaDeCima'] = $linhaDeCima;
            $arrDados['linhaDeBaixo'] = $linhaDeBaixo;
            $arrDados['arrMdPetVincRepresentantPJRN'] = $arrMdPetVincRepresentantPJRN;
            $arrDados['acesso'] = $acesso;
            $arrDados['img'] = $img;
            $arrDados['textoSeparado'] = $textoSeparado;

        }

        return $arrDados;

    }

    //Icone exibido na tela "Controle de Processos"
    public function montarIconeControleProcessos($arrObjProcedimentoDTO)
    {

        $arrParam = array();
        $qtdArrObjProcedimentoDTO = (is_array($arrObjProcedimentoDTO) ? count($arrObjProcedimentoDTO) : 0);
        if ($arrObjProcedimentoDTO != null && $qtdArrObjProcedimentoDTO > 0) {

            foreach ($arrObjProcedimentoDTO as $objProcedimentoAPI) {

                $arrRetDadosIcones = $this->retornarArrDadosParaIcones($objProcedimentoAPI->getIdProcedimento());
                $qtdArrRetDadosIcones = (is_array($arrRetDadosIcones) ? count($arrRetDadosIcones) : 0);
                if ($qtdArrRetDadosIcones > 0) {
                    $arrParam[$objProcedimentoAPI->getIdProcedimento()] = array($arrRetDadosIcones['img']);
                }
            }
        } //fim do foor de ArrProcedimentoApi

        return $arrParam;
    }

    //Icone exibido na tela "Acompanhamento Especial"
    public function montarIconeAcompanhamentoEspecial($arrObjProcedimentoDTO)
    {

        $arrParam = array();
        $qtdArrObjProcedimentoDTO = (is_array($arrObjProcedimentoDTO) ? count($arrObjProcedimentoDTO) : 0);
        if ($arrObjProcedimentoDTO != null && $qtdArrObjProcedimentoDTO > 0) {

            foreach ($arrObjProcedimentoDTO as $procDTO) {

                $arrRetDadosIcones = $this->retornarArrDadosParaIcones($procDTO->getIdProcedimento());
                $qtdArrRetDadosIcones = (is_array($arrRetDadosIcones) ? count($arrRetDadosIcones) : 0);
                if ($qtdArrRetDadosIcones > 0) {
                    $arrParam[$procDTO->getIdProcedimento()] = array($arrRetDadosIcones['img']);
                }
            }
        }

        return $arrParam;
    }

    public function montarMenuUsuarioExterno()
    {
        $menuExternoRN = new MdPetMenuUsuarioExternoRN();
        $menuExternoDTO = new MdPetMenuUsuarioExternoDTO();
        $menuExternoDTO->retTodos();
        $menuExternoDTO->setStrSinAtivo('S');

        $menuExternoDTO->setOrd("Nome", InfraDTO::$TIPO_ORDENACAO_ASC);

        $objLista = $menuExternoRN->listar($menuExternoDTO);
        $numRegistros = (is_array($objLista) ? count($objLista) : 0);

        //utilizado para ordenação
        $urlBase = ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL');
        $arrMenusNomes = array();

        //Configurado: Tipo de Processo de Vinculação Jurídica
        $objMdPetVincTpProcessoRN = new MdPetVincTpProcessoRN();
        $objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();
        $objMdPetVincTpProcessoDTO->retNumIdMdPetVincTpProcesso();
        $objMdPetVincTpProcessoDTO->setNumMaxRegistrosRetorno(1);
        $objMdPetVincTpProcessoDTO->setStrSinAtivo('S');
        $objMdPetVincTpProcessoDTO->setStrTipoVinculo('J');
        $objMdPetVincUsuExtPj = $objMdPetVincTpProcessoRN->consultar($objMdPetVincTpProcessoDTO);

        //Configurado: Tipo de Processo de Vinculação Física
        $objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();
        $objMdPetVincTpProcessoDTO->retNumIdMdPetVincTpProcesso();
        $objMdPetVincTpProcessoDTO->setNumMaxRegistrosRetorno(1);
        $objMdPetVincTpProcessoDTO->setStrSinAtivo('S');
        $objMdPetVincTpProcessoDTO->setStrTipoVinculo('F');
        $objMdPetVincUsuExtPf = $objMdPetVincTpProcessoRN->consultar($objMdPetVincTpProcessoDTO);

        //Configurado: "Integração" com funcionalidade "Consultar Dados CNPJ Receita Federal"
        $objMdPetIntegFuncionalidRN = new MdPetIntegFuncionalidRN();
        $arrIdMdPetIntegFuncionalidUtilizado = $objMdPetIntegFuncionalidRN->verificarMdPetIntegFuncionalidUtilizado(null, MdPetIntegFuncionalidRN::$ID_FUNCIONALIDADE_CNPJ_RECEITA_FEDERAL, null);

	    $arrMenusNomes["Pessoas Jurídicas"] = '';

        $arrMenusNomes["Peticionamento"] = $urlBase . '/controlador_externo.php?acao=md_pet_usu_ext_iniciar';

        $arrMenusNomes["Recibos Eletrônicos de Protocolo"] = $urlBase . '/controlador_externo.php?acao=md_pet_usu_ext_recibo_listar';

        $arrMenusNomes['Intimações Eletrônicas'] = $urlBase . '/controlador_externo.php?acao=md_pet_intimacao_usu_ext_listar';

        if (is_array($objLista) && $numRegistros > 0) {

            for ($i = 0; $i < $numRegistros; $i++) {

                $item = $objLista[$i];

                if ($item->getStrTipo() == MdPetMenuUsuarioExternoRN::$TP_EXTERNO) {
                    $link = "javascript:";
                    $link .= "var a = document.createElement('a'); ";
                    $link .= "a.href='" . $item->getStrUrl() . "'; ";
                    $link .= "a.target = '_blank'; ";
                    $link .= "document.body.appendChild(a); ";
                    $link .= "a.click(); ";
                    $arrMenusNomes[$item->getStrNome()] = $link;
                } else if ($item->getStrTipo() == MdPetMenuUsuarioExternoRN::$TP_CONTEUDO_HTML) {
                    $idItem = $item->getNumIdMenuPeticionamentoUsuarioExterno();
                    $strLinkMontado = SessaoSEIExterna::getInstance()->assinarLink($urlBase . '/controlador_externo.php?acao=md_pet_pagina_conteudo_externo&id_md_pet_usu_externo_menu=' . $idItem);
                    $arrMenusNomes[$item->getStrNome()] = $strLinkMontado;
                }
            }
        }


        $arrLink = array();
        $numRegistrosMenu = (is_array($arrMenusNomes) ? count($arrMenusNomes) : 0);

        $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
        $objMdPetTipoProcessoDTO = new MdPetTipoProcessoDTO();
        $objMdPetTipoProcessoDTO->setStrSinAtivo('S');
        $objMdPetTipoProcessoDTO->retTodos();

        $arrObjMdPetTipoProcessoDTO = $objMdPetTipoProcessoRN->listar($objMdPetTipoProcessoDTO);

        $qtdArrObjMdPetTipoProcessoDTO = (is_array($arrObjMdPetTipoProcessoDTO) ? count($arrObjMdPetTipoProcessoDTO) : 0);
        $objMdPetTipoProcessoDTO = $qtdArrObjMdPetTipoProcessoDTO > 0 ? current($arrObjMdPetTipoProcessoDTO) : null;

        $objMdPetCriterioRN = new MdPetCriterioRN();
        $objMdPetCriterioDTO = new MdPetCriterioDTO();
        $objMdPetCriterioDTO->setStrSinCriterioPadrao('S');
        $objMdPetCriterioDTO->setStrSinAtivo('S');
        $objMdPetCriterioDTO->retTodos();
        $arrObjMdPetCriterioDTO = $objMdPetCriterioRN->listar($objMdPetCriterioDTO);
        $qtdArrObjMdPetCriterioDTO = (is_array($arrObjMdPetCriterioDTO) ? count($arrObjMdPetCriterioDTO) : 0);
        $objMdPetCriterioDTO = $qtdArrObjMdPetCriterioDTO > 0 ? current($arrObjMdPetCriterioDTO) : null;

        if (is_array($arrMenusNomes) && $numRegistrosMenu > 0) {

            foreach ($arrMenusNomes as $key => $value) {

                $urlLink = $arrMenusNomes[$key];
                $nomeMenu = $key;

                switch ($nomeMenu) {
                    case 'Peticionamento' :
                        $urlLinkIntercorrente = $urlBase . '/controlador_externo.php?acao=md_pet_intercorrente_usu_ext_cadastrar';
                        if ($objMdPetTipoProcessoDTO || !is_null($objMdPetCriterioDTO)) {
                            $arrLink[] = '-^#^^' . $nomeMenu . '^';
                        };

	                    if ($qtdArrObjMdPetTipoProcessoDTO > 0) {
		                    $arrLink[] = '--^' . $urlLink . '^^' . 'Processo Novo' . '^';
	                    }

                        if (!is_null($objMdPetCriterioDTO)) {
                            $arrLink[] = '--^' . $urlLinkIntercorrente . '^^' . 'Intercorrente' . '^';
                        }
                        break;
                    case 'Pessoas Jurídicas' :
                        if (!is_null($objMdPetVincUsuExtPj) > 0 && !is_null($arrIdMdPetIntegFuncionalidUtilizado)) {
                            $arrLink[] = '-^' . $urlBase . '/controlador_externo.php?acao=md_pet_vinculacao_listar' . '^^' . 'Responsável Legal de Pessoa Jurídica' . '^';
                            $arrLink[] = '-^' . $urlBase . '/controlador_externo.php?acao=md_pet_vinc_usu_ext_pe_listar' . '^^' . 'Procurações Eletrônicas' . '^';
                        } elseif (!is_null($objMdPetVincUsuExtPf)) {
                            $arrLink[] = '-^' . $urlBase . '/controlador_externo.php?acao=md_pet_vinc_usu_ext_pe_listar' . '^^' . 'Procurações Eletrônicas' . '^';
                        }
                        break;
                    default :
                        $arrLink[] = '-^' . $urlLink . '^^' . $nomeMenu . '^';
                        break;
                }
            }
        }

        return $arrLink;
    }

    public function montarBotaoAcessoExternoAutorizado(ProcedimentoAPI $objProcedimentoAPI)
    {

        $array = array();
        $idAcessoExterno = $_GET['id_acesso_externo'];

        $id_usuario_externo = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();

        //o botao so aparece se houver usuario externo logado (usuario de acesso externo avulso nao visualiza o botao)
        if ($id_usuario_externo != null && $id_usuario_externo != "" && $idAcessoExterno) {

            $strParam = 'acao=md_pet_intercorrente_usu_ext_cadastrar&id_orgao_acesso_externo=0';
            $hash = md5($strParam . '#' . SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() . '@' . SessaoSEIExterna::getInstance()->getAtributo('RAND_USUARIO_EXTERNO'));

            $urlBase = ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL');

            $link = $urlBase . '/controlador_externo.php?acao=md_pet_intercorrente_usu_ext_cadastrar&id_orgao_acesso_externo=0&infra_hash=' . $hash;
            $id_procedimento = isset($_GET['id_procedimento']) ? $_GET['id_procedimento'] : $objProcedimentoAPI->getIdProcedimento();

            $array[] = "<script> function criarForm(){ 
            var f = document.createElement(\"form\");
            f.setAttribute('method',\"post\");
            f.setAttribute('action',\"$link\");
            
            var i = document.createElement(\"input\"); 
            i.setAttribute('type',\"hidden\");
            i.setAttribute('name',\"id_procedimento\");
            i.setAttribute('value',\"$id_procedimento\");
        
            f.appendChild(i);
            document.getElementsByTagName('body')[0].appendChild(f);
            f.submit();
            }
            </script>";

            $objMdPetCriterioRN = new MdPetCriterioRN();
            $objMdPetCriterioDTO = new MdPetCriterioDTO();
            $objMdPetCriterioDTO->setStrSinCriterioPadrao('S');
            $objMdPetCriterioDTO->setStrSinAtivo('S');
            $objMdPetCriterioDTO->retTodos();
            $arrObjMdPetCriterioDTO = $objMdPetCriterioRN->listar($objMdPetCriterioDTO);
            $qtdArrObjMdPetCriterioDTO = (is_array($arrObjMdPetCriterioDTO) ? count($arrObjMdPetCriterioDTO) : 0);
            $objMdPetCriterioDTO = $qtdArrObjMdPetCriterioDTO > 0 ? current($arrObjMdPetCriterioDTO) : null;
            if ($objMdPetCriterioDTO != null) {
                $array[] = '<button type="button" accesskey="i" name="btnPetIntercorrente" value="Peticionamento Intercorrente" onclick="criarForm();" class="infraButton">Peticionamento <span class="infraTeclaAtalho">I</span>ntercorrente</button>';
            }
        }

        return $array;
    }

    public function montarTipoTarjaAssinaturaCustomizada()
    {
        $objArrTipoDTO = array();

        $objTipoDTO = new TipoDTO();
        $objTipoDTO->setStrStaTipo(MdPetAssinaturaRN::$TT_ASSINATURA_SENHA_PETICIONAMENTO);
        $objTipoDTO->setStrDescricao('Assinatura Eletrônica por Usuários Externos');
        $objArrTipoDTO[] = $objTipoDTO;

        return $objArrTipoDTO;
    }

    /**
     * Valida se o Documento que está sendo cancelado foi peticionado
     *
     * @access public
     * @param DocumentoAPI $objDocumentoAPI
     * @return mixed
     * @author Jaqueline Mendes <jaqueline.mendes@castgroup.com.br>
     */
    public function cancelarDocumento(DocumentoAPI $objDocumentoAPI)
    {
        $numRecibo = '';
        $idDoc = $_GET['id_documento'];


        $objReciboDocAnexPetDTO = new MdPetRelReciboDocumentoAnexoDTO();
        $objReciboDocAnexPetDTO->setNumIdDocumento($idDoc);

        $objReciboDocAnexPetRN = new MdPetRelReciboDocumentoAnexoRN();
        $cont = $objReciboDocAnexPetRN->contar($objReciboDocAnexPetDTO);

        if ($cont > 0) {
            $objReciboDocAnexPetDTO->retNumIdReciboPeticionamento();
            $objReciboDocAnexPetDTO = $objReciboDocAnexPetRN->consultar($objReciboDocAnexPetDTO);

            $objReciboPetDTO = new MdPetReciboDTO();
            $objReciboPetDTO->setNumIdReciboPeticionamento($objReciboDocAnexPetDTO->getNumIdReciboPeticionamento());
            $objReciboPetDTO->retStrNumeroProcessoFormatadoDoc();

            $objReciboPetRN = new MdPetReciboRN();
            $objReciboPetDTO = $objReciboPetRN->consultar($objReciboPetDTO);

            if ($objReciboPetDTO) {
                $numRecibo = $objReciboPetDTO->getStrNumeroProcessoFormatadoDoc();
            }

            $msg = 'Não é permitido cancelar este documento, pois ele é oriundo de Peticionamento Eletrônico, conforme Recibo Eletrônico de Protocolo SEI nº ' . $numRecibo . '.';
            $objInfraException = new InfraException();
            $objInfraException->adicionarValidacao($msg);
            $objInfraException->lancarValidacoes();
            return null;
        }

        // Rotina para verificar se o documento é objeto de intimação e impedir o cancelamento, caso o seja
        $dto = new MdPetIntProtocoloDTO();
        $dto->retTodos();
        $dto->setDblIdDocumento($objDocumentoAPI->getIdDocumento());

        $rn = new MdPetIntProtocoloRN();
        $total = $rn->contar($dto);

        if ($total > 0) {

            $msg = 'Não é permitido cancelar este documento, pois ele faz parte de Intimação Eletrônica.';
            $objInfraException = new InfraException();
            $objInfraException->adicionarValidacao($msg);
            $objInfraException->lancarValidacoes();
            return null;
        }

        $mdPetRegrasGeraisRN = new MdPetRegrasGeraisRN();
        $msg = $mdPetRegrasGeraisRN->verificarDocumentoIndisponibilidade(array($objDocumentoAPI, 'cancelar'));

        // condição para saber  o documento está sendo utilizado em um indisponibilidade
        if ($msg != '') {
            $objInfraException = new InfraException();
            $objInfraException->lancarValidacao($msg);
            return null;
        }

        return parent::cancelarDocumento($objDocumentoAPI);
    }

    //nao permite mover documento que compoe intimacao (doc principal, doc anexo E doc de resposta a intimação incluindo certidoes e recibos)
    public function moverDocumento(DocumentoAPI $objDocumentoAPI, ProcedimentoAPI $objProcedimentoAPIOrigem, ProcedimentoAPI $objProcedimentoAPIDestino)
    {

        //procura entre docs principais
        $dto = new MdPetIntProtocoloDTO();
        $dto->retTodos();
        $dto->setDblIdProtocolo($objDocumentoAPI->getIdDocumento());

        $rn = new MdPetIntProtocoloRN();
        $total = $rn->contar($dto);

        //procura entre docs anexos
        $dtoDocDisponivel = new MdPetIntProtDisponivelDTO();
        $dtoDocDisponivel->retTodos();
        $dtoDocDisponivel->setDblIdProtocolo($objDocumentoAPI->getIdDocumento());

        $rnDocDisponivel = new MdPetIntProtDisponivelRN();
        $totalDocDisponivel = $rnDocDisponivel->contar($dtoDocDisponivel);

        if ($total > 0 || $totalDocDisponivel > 0) {

            $msg = 'Não é permitido mover este documento, pois ele faz parte de Intimação Eletrônica.';
            $objInfraException = new InfraException();
            $objInfraException->adicionarValidacao($msg);
            $objInfraException->lancarValidacoes();
            return null;
        } else {

            return parent::moverDocumento($objDocumentoAPI, $objProcedimentoAPIOrigem, $objProcedimentoAPIDestino);
        }
    }

    //Intimacao Eletronica - Monta icone na pagina inicial do Usuario Externo
    public function montarAcaoControleAcessoExterno($arrObjAcessoExternoAPI)
    {

        $arrIcones = array();
        $objUsuarioDTO = new UsuarioDTO();
        $objUsuarioDTO->retNumIdContato();
        $objUsuarioDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
        $objUsuarioRN = new UsuarioRN();
        $objUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

        foreach ($arrObjAcessoExternoAPI as $objAcessoExternoAPI) {
            $conteudoHtml = '';
            $idAcessoExt = $objAcessoExternoAPI->getIdAcessoExterno();

            $objMdPetIntRelDestRN = new MdPetIntRelDestinatarioRN();
            $objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
            $objMdPetIntRelDestDTO->setNumIdAcessoExterno($idAcessoExt);
            $objMdPetIntRelDestDTO->setNumIdContatoParticipante($objUsuarioDTO->getNumIdContato());
            $objMdPetIntRelDestDTO->retNumIdMdPetIntimacao();
            $objMdPetIntRelDestDTO->retNumIdMdPetIntRelDestinatario();
            $objMdPetIntRelDestDTO->retDblIdDocumento();
            $objMdPetIntRelDestDTO->setStrSinPrincipalDoc('S');
            $objMdPetIntRelDestDTO->retDblIdProcedimento();
            $objMdPetIntRelDestDTO->retDblIdProtocolo();
            $objMdPetIntRelDestDTO->retStrProtocoloFormatadoDocumento();
            $objMdPetIntRelDestDTO->retDthDataCadastro();
            $objMdPetIntRelDestDTO->setDblIdProcedimento($objAcessoExternoAPI->getProcedimento()->getIdProcedimento());
            $objMdPetIntRelDestDTO->setProcedimentoDocTIPOFK(InfraDTO::$TIPO_FK_OBRIGATORIA);

            $arrObjMdPetIntRelDestDTO = $objMdPetIntRelDestRN->listar($objMdPetIntRelDestDTO);

            SessaoSEIExterna::getInstance()->configurarAcessoExterno($objAcessoExternoAPI->getIdAcessoExterno());

            foreach ($arrObjMdPetIntRelDestDTO as $objMdPetIntRelDestDTO) {
                $strLink = '';
                if (is_array(explode(' ', $objMdPetIntRelDestDTO->getDthDataCadastro()))) {
                    $dtIntimacao = explode(' ', $objMdPetIntRelDestDTO->getDthDataCadastro());
                    $dtIntimacao = $dtIntimacao[0];
                } else {
                    $dtIntimacao = $objMdPetIntRelDestDTO->getDthDataCadastro();
                }

                $numeroDocumento = $objMdPetIntRelDestDTO->getStrProtocoloFormatadoDocumento();

                //Obter informacoes do Documento Principal da Intimacao
                $objMdPetIntDocumentoRN = new MdPetIntProtocoloRN();
                $objMdPetIntDocumentoDTO = new MdPetIntProtocoloDTO();
                $objMdPetIntDocumentoDTO->retTodos();
                $objMdPetIntDocumentoDTO->retDblIdProtocolo();
                $objMdPetIntDocumentoDTO->retDblIdDocumento();
                $objMdPetIntDocumentoDTO->retStrNumeroDocumento();
                $objMdPetIntDocumentoDTO->retNumIdSerie();
                $objMdPetIntDocumentoDTO->retStrNomeSerie();
                $objMdPetIntDocumentoDTO->retStrProtocoloFormatadoDocumento();

                $objMdPetIntDocumentoDTO->setNumIdMdPetIntimacao($objMdPetIntRelDestDTO->getNumIdMdPetIntimacao());
                $objMdPetIntDocumentoDTO->setStrSinPrincipal('S');
                $objMdPetIntDocumentoDTO->setNumMaxRegistrosRetorno(1);
                $objMdPetIntDocumentoDTO = $objMdPetIntDocumentoRN->consultar($objMdPetIntDocumentoDTO);

                //Icone Sinalizador do Processo com Intimacao Eletronica
                $strMsgTooltipSinalizadorProcesso = "Intimação Eletrônica";
                $strMsgTooltipTextoSinalizadorProcesso = "Expedida em {$dtIntimacao}\n";
                $strMsgTooltipTextoSinalizadorProcesso .= "Documento Principal: ";
                $strMsgTooltipTextoSinalizadorProcesso .= $objMdPetIntDocumentoDTO->getStrNomeSerie() . ' ';
                if ($objMdPetIntDocumentoDTO->getStrNumeroDocumento()) {
                    $strMsgTooltipTextoSinalizadorProcesso .= $objMdPetIntDocumentoDTO->getStrNumeroDocumento() . ' ';
                }
                $strMsgTooltipTextoSinalizadorProcesso .= "(SEI nº {$numeroDocumento})\n\n";

                $strMsgTooltipTextoSinalizadorProcesso .= 'Clique para acessar o processo e consultar a Intimação.';

                $strLinkProcedimento = SessaoSEIExterna::getInstance()->assinarLink('processo_acesso_externo_consulta.php?id_acesso_externo=' . $objAcessoExternoAPI->getIdAcessoExterno());

                $strLink = '<a href="javascript:void(0);" onclick="window.open(\'' . $strLinkProcedimento . '\');"><img src="modulos/peticionamento/imagens/svg/intimacao_controle_de_acessos_externos_destaque.svg?'.Icone::VERSAO.'" class="infraImg" style="width:24px" ';
                $strLink .= str_replace('\n', '<br/>', PaginaSEI::montarTitleTooltip($strMsgTooltipTextoSinalizadorProcesso, $strMsgTooltipSinalizadorProcesso));
                $strLink .= '/></a>&nbsp;';

                $conteudoHtml .= ' ' . $strLink;
            }

            $arrIcones[$objAcessoExternoAPI->getIdAcessoExterno()][] = $conteudoHtml;
        }

        return $arrIcones;
    }

    public function montarBotaoProcesso(ProcedimentoAPI $objProcedimentoAPI)
    {

        $arrBotoes = array();

        if (!(SessaoSEI::getInstance()->verificarPermissao('md_pet_int_documento_listar'))) {
            return $arrBotoes;
        }

        $objMdPetIntDocumentoDTO = new MdPetIntProtocoloDTO();
        $objMdPetIntDocumentoDTO->setDblIdProcedimento($objProcedimentoAPI->getIdProcedimento());
        $objMdPetIntDocumentoDTO->retTodos();
        $objMdPetIntDocumentoRN = new MdPetIntProtocoloRN();
        $intQntdIntimacao = $objMdPetIntDocumentoRN->contar($objMdPetIntDocumentoDTO);

        $intUnidadeGeradora = $objProcedimentoAPI->getIdUnidadeGeradora();
        $intIdUnidadeAtual = SessaoSEI::getInstance()->getNumIdUnidadeAtual();

        //encontrou o tipo de documento na parametrizacao do sistema e o perfil possui o recurso
        if ($intQntdIntimacao > 0 && SessaoSEI::getInstance()->verificarPermissao('md_pet_intimacao_eletronica_listar')) {
            $arrBotoes[] = '<a href="' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_intimacao_eletronica_listar&id_procedimento=' . $objProcedimentoAPI->getIdProcedimento()) . '" class="botaoSEI" tabindex="' . PaginaSEI::getInstance()->getProxTabBarraComandosSuperior() . '"><img src="modulos/peticionamento/imagens/svg/intimacao_eletronica_ver.svg?'.Icone::VERSAO.'" class="infraCorBarraSistema" alt="Ver Intimações Eletrônicas" title="Ver Intimações Eletrônicas" widtt="30"/></a>';
        }

        if (!SessaoSEI::getInstance()->verificarPermissao('md_pet_adm_vinc_consultar') && $objProcedimentoAPI->getCodigoAcesso() > 0)
            return array();

        // Vinculação à Pessoa Jurídica
        $objMdPetVinculoRN = new MdPetVinculoRN();
        $arrObjMdPetVinculoDTO = $objMdPetVinculoRN->consultarProcedimentoVinculo(array($objProcedimentoAPI->getIdProcedimento(), 'retornoDTO' => true, 'isAtivos' => false));
        $qtdArrObjMdPetVinculoDTO = (is_array($arrObjMdPetVinculoDTO) ? count($arrObjMdPetVinculoDTO) : 0);
        if ($arrObjMdPetVinculoDTO && $qtdArrObjMdPetVinculoDTO > 0) {
            $arrBotoes [] = '<a href="' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_adm_vinc_consultar&acao_origem=procedimento_visualizar&acao_retorno=arvore_visualizar&id_procedimento=' . $objProcedimentoAPI->getIdProcedimento() . '&arvore=1') . '" tabindex="' . PaginaSEI::getInstance()->getProxTabBarraComandosSuperior() . '" class="botaoSEI"><img class="infraCorBarraSistema" src="modulos/peticionamento/imagens/svg/visualizar_vinculacoes.svg?11" alt="Visualizar Vinculações e Procurações Eletrônicas" title="Visualizar Vinculações e Procurações Eletrônicas" style="width: 38px;" /></a>';
        }

        return $arrBotoes;
    }

    //encapsulamento da logica de inclusao de botoes na coluna "Ações" da tela de processo do usuario externo
    //a mesma logica aqui é chamada pelo ponto de extensao dos documentos autorizados (ponto de ext antigo) e
    //pelo ponto de extensao dos documentos negados (ponto de ext novo, adicionado no SEI 3.0.7)
    private function montarBotaoAcessoExternoPeticionamento($arrObjProtocoloAPI2, $isProcedimento = false)
    {
        $objMdPetAcessoExtDocRN = new MdPetIntAcessoExternoDocumentoRN();

        $idAcessoExterno = $_GET['id_acesso_externo'];
        $idOrgAcessoExterno = $_GET['id_orgao_acesso_externo'];
        $idProcedimento = $_GET['id_procedimento'];
        $idProcAnex = $_GET['id_procedimento_anexado'];
        $arrIcones = array();

        $id_usuario_externo = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();

        //os botoes so aparecem se houver usuario externo logado (usuario de acesso externo avulso nao visualiza esses botoes)
        if ($id_usuario_externo != null && $id_usuario_externo != "" && $idAcessoExterno) {
            //lista os documentos do SEI
            $arrIdsProtocolo = $objMdPetAcessoExtDocRN->getArrDocumentosAPI(array($idAcessoExterno, $idProcAnex, $isProcedimento));
            if ($arrIdsProtocolo) {
                foreach ($arrIdsProtocolo as $idProtocolo) {
                    $mdPetIntProtocoloRN = new MdPetIntProtocoloRN();
                    $objMdPetIntProtocoloDTO = new MdPetIntProtocoloDTO();
                    $objMdPetIntProtocoloDTO->setDblIdProtocolo($idProtocolo);
                    $objMdPetIntProtocoloDTO->retNumIdMdPetIntimacao();
                    $objMdPetIntProtocoloDTO = $mdPetIntProtocoloRN->listar($objMdPetIntProtocoloDTO);
                    $conteudoHtml = '';
                    if ($objMdPetIntProtocoloDTO) {
                        $conteudoHtml .= $this->montarAcaoBotaoCumprir($idProtocolo, $idAcessoExterno, $idProcedimento, $isProcedimento);
                        $conteudoHtml .= $this->montarAcaoBotaoCertidao($idProtocolo, $idAcessoExterno);
                        $conteudoHtml .= $this->montarAcaoBotaoResposta($idProtocolo, $idAcessoExterno, $idProcedimento);
                        $conteudoHtml .= $this->montarAcaoBotaoRecibo($idProtocolo, $idAcessoExterno, $idProcedimento);
                    }
                    $arrIcones[$idProtocolo][] = $conteudoHtml . '<br/>';

                    if ($isProcedimento) {
                        $this->arrProcessosLiberados[] = $idProtocolo;
                    } else {
                        $this->arrDocumentosLiberados[] = $idProtocolo;
                    }
                }
            }
        }
        return $arrIcones;
    }

    public function montarAcaoBotaoRecibo($idProtocolo, $idAcessoExterno, $idProcedimento)
    {
        $objMdPetIntRN = new MdPetIntimacaoRN();
        $objMdPetIntDocumentoDTO = new MdPetIntProtocoloDTO();
        $objMdPetIntRelDestRN = new MdPetIntRelDestinatarioRN();
        $objMdPetIntReciboRN = new MdPetIntReciboRN();
        $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
        $objMdPetAcessoExtDocRN = new MdPetIntAcessoExternoDocumentoRN();
        $objMdPetIntProtocoloRN = new MdPetIntProtocoloRN();

        $objMdPetIntDocumentoDTO->setDblIdProtocolo($idProtocolo);
        $objMdPetIntDocumentoDTO->retDblIdDocumento();
        $objMdPetIntDocumentoDTO->retStrSinPrincipal();
        $objMdPetIntDocumentoDTO->retNumIdMdPetIntimacao();
        $listaDocs = $objMdPetIntProtocoloRN->listar($objMdPetIntDocumentoDTO);
        foreach ($listaDocs as $objRelIntDoc) {
            $recibos = [];
            $existeInt = false;
            $idIntimacao = $objRelIntDoc ? $objRelIntDoc->getNumIdMdPetIntimacao() : null;
            $objContato = $objMdPetIntAceiteRN->retornaObjContatoIdUsuario(array(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()));
            $arrDados = null;
            $arrDados = $objMdPetIntAceiteRN->existeAceiteIntimacaoAcao(array($idIntimacao, true));
            $qtdArrDados = (is_array($arrDados) ? count($arrDados) : 0);
            if ($qtdArrDados > 0) {
                foreach ($arrDados as $aceite) {
                    $existeInt = $aceite['INT'];

                    $objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
                    $objMdPetIntRelDestDTO->setNumIdMdPetIntimacao($idIntimacao);
                    $objMdPetIntRelDestDTO->retDthDataCadastro();
                    $objMdPetIntRelDestDTO->retNumIdMdPetIntRelDestinatario();
                    $objMdPetIntRelDestDTO->setNumIdContatoParticipante($objContato->getNumIdContato());
                    $objMdPetIntRelDestDTO->retStrStaSituacaoIntimacao();
                    $objMdPetIntRelDestDTO->retStrSinPessoaJuridica();
                    $objMdPetIntRelDestDTO->retNumIdContato();
                    $objMdPetIntRelDestDTO->retDblCnpjContato();
                    $objMdPetIntRelDestDTO->retStrNomeContato();

                    $idAcessoExternoValido = $objMdPetAcessoExtDocRN->verificarAcessoExternoValido(array($idIntimacao, $objContato->getNumIdContato(), $idAcessoExterno));
                    if (!is_null($idAcessoExternoValido)) {
                        $objMdPetIntRelDestDTO->setNumIdAcessoExterno($idAcessoExterno);
                    }

                    $objMdPetIntRelDestDTO = $objMdPetIntRelDestRN->listar($objMdPetIntRelDestDTO);
                    if (!empty($objMdPetIntRelDestDTO)) {
                        $arrDados = array($objRelIntDoc->getNumIdMdPetIntimacao());
                        $retorno = $objMdPetIntRN->retornaDadosDocPrincipalIntimacao($arrDados);

                        if (!is_null($retorno)) {
                            $docPrinc = $retorno[0];
                            $docTipo = $retorno[1];
                            $docNum = $retorno[4];
                        }

                        if ($existeInt) {
                            foreach ($objMdPetIntRelDestDTO as $obj) {
                                //Botão
                                $idMdPetDest = $obj->getNumIdMdPetIntRelDestinatario();
                                $sitIntimacao = $obj->getStrStaSituacaoIntimacao();

                                $objDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
                                $objDestinatarioDTO->retTodos();
                                $objDestinatarioDTO->setNumIdMdPetIntRelDestinatario($idMdPetDest);
                                $objDestinatarioDTO->setNumIdMdPetIntimacao($idIntimacao);
                                $objDestinatarioRN = new MdPetIntRelDestinatarioRN();
                                $arrDestinatarioDTO = $objDestinatarioRN->listar($objDestinatarioDTO);
                                $arrContatos = InfraArray::converterArrInfraDTO($arrDestinatarioDTO, 'IdContato');

                                $objUsuarioDTO = new UsuarioDTO();
                                $objUsuarioDTO->retNumIdContato();
                                $objUsuarioDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
                                $objUsuarioRN = new UsuarioRN();
                                $arrIds = $objUsuarioRN->listarRN0490($objUsuarioDTO);

                                if ($sitIntimacao == MdPetIntimacaoRN::$INTIMACAO_RESPONDIDA) {
                                    $objMdPetReciboDTO = new MdPetReciboDTO();
                                    $objMdPetReciboDTO->retTodos();

                                    $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
                                    $strVersaoModuloPeticionamento = $objInfraParametro->getValor('VERSAO_MODULO_PETICIONAMENTO', false);

                                    if ($obj->getStrSinPessoaJuridica() == MdPetIntRelDestinatarioRN::$PESSOA_JURIDICA) {
                                        $mdPetVinculoRN = new MdPetVinculoRN();
                                        $mdPetVinculoDTO = new MdPetVinculoDTO();
                                        $mdPetVinculoDTO->setNumIdContato($obj->getNumIdContato());
                                        $mdPetVinculoDTO->retNumIdContatoRepresentante();
                                        $mdPetVinculoDTO->retNumIdUsuario();
                                        $mdPetVinculoDTO->retStrStaEstado();
                                        $mdPetVinculoDTO->retDthDataEncerramento();
                                        $arrMdPetVinculoDTO = $mdPetVinculoRN->listar($mdPetVinculoDTO);
                                        $arrRepresentantes = InfraArray::converterArrInfraDTO($arrMdPetVinculoDTO, 'IdUsuario');
                                    } else {
                                        $arrRepresentantes = array(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
                                    }

                                    $objMdPetReciboDTO->setNumIdUsuario($arrRepresentantes, InfraDTO::$OPER_IN);
                                    $objMdPetReciboDTO->setStrStaTipoPeticionamento(MdPetReciboRN::$TP_RECIBO_RESPOSTA_INTIMACAO);

                                    $objMdPetReciboRN = new MdPetReciboRN();

                                    //Próprio Processo
                                    $objMdPetReciboDTO->setNumIdProtocolo($idProcedimento);
                                    $objMdPetReciboDTO->unSetDblIdProtocoloRelacionado();

                                    $arrObjMdPetReciboDTO = $objMdPetReciboRN->listar($objMdPetReciboDTO);
                                    $qtdArrObjMdPetReciboDTO = (is_array($arrObjMdPetReciboDTO) ? count($arrObjMdPetReciboDTO) : 0);
                                    if ($qtdArrObjMdPetReciboDTO == 0) {
                                        //Relacionado
                                        $objMdPetReciboDTO->unSetNumIdProtocolo();
                                        $objMdPetReciboDTO->setDblIdProtocoloRelacionado($idProcedimento);
                                        $arrObjMdPetReciboDTO = $objMdPetReciboRN->listar($objMdPetReciboDTO);
                                    }

                                    $nuProtocolo = null;
                                    foreach ($arrObjMdPetReciboDTO as $objMdPetReciboDTO) {
                                        $usuarioDTO = new UsuarioDTO();
                                        $usuarioRN = new UsuarioRN();
                                        $usuarioDTO->retNumIdUsuario();
                                        $usuarioDTO->retNumIdContato();
                                        $usuarioDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
                                        $usuarioDTO = $usuarioRN->consultarRN0489($usuarioDTO);

                                        $emailDestinatario = SessaoSEIExterna::getInstance()->getStrSiglaUsuarioExterno();
                                        $acessoExtRN = new AcessoExternoRN();
                                        $acessoExtDTO = new AcessoExternoDTO();
                                        $acessoExtDTO->retTodos();
                                        $acessoExtDTO->setOrd("IdAcessoExterno", InfraDTO::$TIPO_ORDENACAO_DESC);
                                        $acessoExtDTO->retDblIdProtocoloAtividade();
                                        $acessoExtDTO->retNumIdContatoParticipante();

                                        //trazer acesso externo  mais recente, deste processo, para este usuario externo, que estejam dentro da data de validade
                                        $acessoExtDTO->setDblIdProtocoloAtividade($objMdPetReciboDTO->getNumIdProtocolo());

                                        $acessoExtDTO->setNumIdContatoParticipante($usuarioDTO->getNumIdContato());
                                        $acessoExtDTO->setStrStaTipo(AcessoExternoRN::$TA_USUARIO_EXTERNO);
                                        $acessoExtDTO->setStrSinAtivo('S');
                                        $acessoExtDTO->setDtaValidade(InfraData::getStrDataHoraAtual(), InfraDTO::$OPER_MAIOR_IGUAL);
                                        $arrAcessosExternos = $acessoExtRN->listar($acessoExtDTO);

                                        $id_acesso_ext_link = $idAcessoExterno;
                                        $docLink = "documento_consulta_externa.php?id_acesso_externo=" . $id_acesso_ext_link;
                                        $docLink .= "&id_documento=" . $objMdPetReciboDTO->getDblIdDocumento();
                                        $docLink .= "&id_orgao_acesso_externo=0";
                                        SessaoSEIExterna::getInstance()->configurarAcessoExterno($id_acesso_ext_link);

                                        //se nao configurar acesso externo  ANTES, a assinatura do link falha
                                        $linkAssinado = PaginaSEIExterna::getInstance()->formatarXHTML(SessaoSEIExterna::getInstance()->assinarLink($docLink));
                                        if ($obj->getStrSinPessoaJuridica() == MdPetIntRelDestinatarioRN::$PESSOA_JURIDICA) {
                                            $arrIdReciboPeticionamento = array();
                                            foreach ($arrMdPetVinculoDTO as $objMdPetVinculoDTO) {
                                                $mostraIcone = true;
                                                if (!is_null($objMdPetVinculoDTO->getDthDataEncerramento())) {
                                                    $dtEncerramento = DateTime::createFromFormat('d/m/Y H:i:s', $objMdPetVinculoDTO->getDthDataEncerramento());
                                                    $dtRecibo = DateTime::createFromFormat('d/m/Y H:i:s', $objMdPetReciboDTO->getDthDataHoraRecebimentoFinal());
                                                    $interval = $dtRecibo->diff($dtEncerramento);
                                                    if ($interval->format('%R') == '-') {
                                                        $mostraIcone = false;
                                                    }
                                                }

                                                if ($objMdPetVinculoDTO->getDthDataEncerramento() == NULL && $mostraIcone == true && !in_array($objMdPetReciboDTO->getNumIdReciboPeticionamento(), $arrIdReciboPeticionamento)) {
                                                    if(!in_array($objMdPetReciboDTO->getDblIdDocumento(), $recibos)){
                                                        $conteudoHtml .= $objMdPetIntReciboRN->addIconeRecibo(array($objMdPetReciboDTO->getDthDataHoraRecebimentoFinal(), $docPrinc, $docTipo, $docNum, $linkAssinado, $objMdPetReciboDTO->getDblIdDocumento(), $idMdPetDest, $id_acesso_ext_link));
                                                        array_push($recibos, $objMdPetReciboDTO->getDblIdDocumento());
                                                    }
                                                    array_push($arrIdReciboPeticionamento, $objMdPetReciboDTO->getNumIdReciboPeticionamento());
                                                }
                                            }
                                        } else {
                                            if(!in_array($objMdPetReciboDTO->getDblIdDocumento(), $recibos)){
                                                $conteudoHtml .= $objMdPetIntReciboRN->addIconeRecibo(array($objMdPetReciboDTO->getDthDataHoraRecebimentoFinal(), $docPrinc, $docTipo, $docNum, $linkAssinado, $objMdPetReciboDTO->getDblIdDocumento(), $idMdPetDest));
                                                array_push($recibos, $objMdPetReciboDTO->getDblIdDocumento());
                                            }
                                        }

                                        $nuProtocolo = $objMdPetReciboDTO->getNumIdProtocolo();

                                    }

                                    //necessario fazer  isso para nao quebrar a navegaçao (se nao fizer isso e tem clicar em qualquer outro link do usuario externo, quebra a sessao e usuario é enviado de volta para a tela de login externo (trata-se de funcionamento incorporado ao Core do SEI)
                                    SessaoSEIExterna::getInstance()->configurarAcessoExterno($idAcessoExterno);
                                }
                            }
                        }
                    }
                }
            }
        }

        return $conteudoHtml;
    }

    public function montarAcaoBotaoCumprir($idProtocolo, $idAcessoExterno, $idProcedimento, $isProcedimento)
    {
        $htmlImgIntCumpridaPrinc = '<img src="modulos/peticionamento/imagens/svg/intimacao_cumprida_doc_principal.svg?'.Icone::VERSAO.'" style="width: 24px;">';
        $htmlImgIntNaoCumpPrinc = '<img src="modulos/peticionamento/imagens/svg/intimacao_nao_cumprida_doc_principal.svg?'.Icone::VERSAO.'" style="width: 24px;">';
        $htmlImgIntCumpridaAnex = '<img src="modulos/peticionamento/imagens/svg/intimacao_cumprida_doc_anexo.svg?'.Icone::VERSAO.'" style="width: 24px;">';
        $htmlImgIntNaoCumprAnex = '<img src="modulos/peticionamento/imagens/svg/intimacao_nao_cumprida_doc_anexo.svg?'.Icone::VERSAO.'" style="width: 24px;">';
        $htmlImgIntAguardandoCumprGeralPrinc = '<img src="modulos/peticionamento/imagens/svg/intimacao_aguardando_cumprimento_geral.svg?'.Icone::VERSAO.'" style="width: 24px;">';
        $htmlImgIntAguardandoCumprGeralAnex = '<img src="modulos/peticionamento/imagens/svg/intimacao_aguardando_cumprimento_geral_anexo.svg?'.Icone::VERSAO.'" style="width: 24px;">';
        $htmlImgIntNaoCumprVinculoInativoPrinc = '<img src="modulos/peticionamento/imagens/svg/intimacao_nao_cumprida_vinculo_inativo.svg?'.Icone::VERSAO.'" style="width: 24px;">';
        $htmlImgIntNaoCumprVinculoInativoAnex = '<img src="modulos/peticionamento/imagens/svg/intimacao_nao_cumprida_vinculo_inativo_anexo.svg?'.Icone::VERSAO.'" style="width: 24px;">';
        
        $objContato = (new MdPetIntAceiteRN())->retornaObjContatoIdUsuario(array(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()));
        
        $arrPessoaJuridica = $arrPessoaFisica = [];
        $vinculoRepresentanteInativo = false;

        //Recupera os documentos da Intimação.
        $objMdPetIntDocumentoDTO = new MdPetIntProtocoloDTO();
        $objMdPetIntDocumentoDTO->setDblIdProtocolo($idProtocolo);
        $objMdPetIntDocumentoDTO->retDblIdDocumento();
        $objMdPetIntDocumentoDTO->retStrSinPrincipal();
        $objMdPetIntDocumentoDTO->retNumIdMdPetIntimacao();
        $listaDocs = (new MdPetIntProtocoloRN())->listar($objMdPetIntDocumentoDTO);

        if (count($listaDocs) > 0) {

            $idIntimacaoBotao   = InfraArray::converterArrInfraDTO($listaDocs, 'IdMdPetIntimacao');
            $idIntimacaoBtnlink = [];

            foreach ($listaDocs as $objRelIntDoc) {

                $img = '';
                $js = '';
                $strLink = '';
                $existeInt = false;
                $isMain = false;
                $aguardandoCumprirGeral = false;
                $idIntimacao = $objRelIntDoc ? $objRelIntDoc->getNumIdMdPetIntimacao() : null;
                $idDocumento = $objRelIntDoc->getDblIdDocumento();
                
                $arrAceites = null;
                $idAceite = null;
                $dataAceite = null;
                $idDestinatarioAceite = null;

                if ($objRelIntDoc) {

                    $arrAceites = (new MdPetIntAceiteRN())->existeAceiteIntimacaoAcao(array($idIntimacao, true));

                    if ($arrAceites) {
                        foreach ($arrAceites as $aceite) {
                            $idAceite[] = $aceite['ID_ACEITE'];
                            $idDestinatarioAceite[] = $aceite['ID_DESTINATARIO'];
                            if (is_array(explode(' ', $aceite['DATA_ACEITE']))) {
                                $dataAceite = explode(' ', $aceite['DATA_ACEITE']);
                                $dataAceite = $dataAceite[0];
                            } else {
                                $dataAceite = $aceite['DATA_ACEITE'];
                            }
                            $existeInt = true;
                        }
                    }
                }

                $arrObjDestinatariosIntimacoes = (new MdPetIntRelDestinatarioRN())->retornarDestinatariosIntimacao($idIntimacao, $objContato->getNumIdContato(), $idAcessoExterno);
                
                $idIntimacaoBtnlink = array_merge($idIntimacaoBtnlink, array_diff(array_unique(InfraArray::converterArrInfraDTO($arrObjDestinatariosIntimacoes, 'IdMdPetIntimacao')), $idIntimacaoBtnlink));

                $arrObjDestinatariosIntimacoesCopia = $arrObjDestinatariosIntimacoes;
                $arrObjDestinatarios = array();
                $arrObjDestinatariosUnicosIntimacao = array();
                $arrObjDestinatariosUnicosIntimacaoComProcuracao = array();
                $qtdDestinatariosIntimacao = 0;

                foreach ($arrObjDestinatariosIntimacoesCopia as $chave => $itemObjMdPetIntRelDestDTOTratado) {

                    $arrProcuracoesAtivasDestinatario = (new MdPetVincRepresentantRN())->retornarProcuradoresComPoderCumprirResponder($itemObjMdPetIntRelDestDTOTratado->getNumIdContato(), $idProtocolo, $objContato->getNumIdContato());
                    
                    if (!empty($arrProcuracoesAtivasDestinatario)) {
                        $arrObjDestinatarios[$chave]['objeto']      = $itemObjMdPetIntRelDestDTOTratado;
                        $arrObjDestinatarios[$chave]['procuracoes'] = $arrProcuracoesAtivasDestinatario;
                        if (!key_exists($itemObjMdPetIntRelDestDTOTratado->getNumIdMdPetIntRelDestinatario(), $arrObjDestinatariosUnicosIntimacaoComProcuracao)) {
                            $arrObjDestinatariosUnicosIntimacaoComProcuracao[$itemObjMdPetIntRelDestDTOTratado->getNumIdMdPetIntRelDestinatario()] = $itemObjMdPetIntRelDestDTOTratado;
                        }
                    }

                    if (!key_exists($itemObjMdPetIntRelDestDTOTratado->getNumIdMdPetIntRelDestinatario(), $arrObjDestinatariosUnicosIntimacao)) {
                        $arrObjDestinatariosUnicosIntimacao[$itemObjMdPetIntRelDestDTOTratado->getNumIdMdPetIntRelDestinatario()] = $itemObjMdPetIntRelDestDTOTratado;
                        $qtdDestinatariosIntimacao++;
                    }

                }

                $qtdAceites = is_array($arrAceites) ? count($arrAceites) : 0;

                // Inicializa a situacao da intimacao
                $situacao = $this::$INTIMACAO_NAO_CUMPRIDA;

                if ($qtdAceites == $qtdDestinatariosIntimacao) {

                    $situacao = $this::$INTIMACAO_CUMPRIDA;

                } else if ($qtdAceites < $qtdDestinatariosIntimacao) {

                    $temProcuracao = null;

                    if($arrObjDestinatarios) {

                        foreach ($arrObjDestinatarios as $chaveDestinatarios => $itemDestinatario) {

                            $objDestinatario    = $itemDestinatario['objeto'];
                            $arrProcuracoes     = $itemDestinatario['procuracoes'];
                            $temAceite          = false;

                            if ($arrAceites) {
                                foreach ($arrAceites as $chaveAceite => $itemAceite) {
                                    if ($objDestinatario->getNumIdMdPetIntRelDestinatario() == $itemAceite['ID_DESTINATARIO']) {
                                        $temAceite = true;
                                        break;
                                    }
                                }
                            }

                            if (!$temAceite) {
                                $temProcuracao = count($arrProcuracoes) > 0 ? true : false;
                            }

                        }

                    } else {

                        foreach ($arrObjDestinatariosUnicosIntimacao as $objDestinatario){

                            $procNegado = true;

                            if($objContato->getNumIdContato() == $objDestinatario->getNumIdContato()){

                                $temProcuracao = true;
                                $procNegado = false;
                                break;

                            } else {

                                $objMdPetVinculoDTO = new MdPetVinculoDTO();
                                $objMdPetVinculoDTO->retNumIdMdPetVinculo();
                                $objMdPetVinculoDTO->retNumIdContato();
                                $objMdPetVinculoDTO->setNumIdContato($objDestinatario->getNumIdContato());
                                $arrObjMdPetVinculoDTO = (new MdPetVinculoRN())->consultar($objMdPetVinculoDTO);

                                $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
                                $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
                                $objMdPetVincRepresentantDTO->retStrStaAbrangencia();
                                $objMdPetVincRepresentantDTO->setNumIdContato($objContato->getNumIdContato());
                                $objMdPetVincRepresentantDTO->setNumIdMdPetVinculo($arrObjMdPetVinculoDTO->getNumIdMdPetVinculo());
                                $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
                                $objMdPetVincRepresentantDTO->setNumIdMdPetRelPoder(1);
                                $objMdPetVincRepresentantDTO = (new MdPetVincRepresentantRN())->listar($objMdPetVincRepresentantDTO);

                                if(!empty($objMdPetVincRepresentantDTO)) {

                                    foreach ($objMdPetVincRepresentantDTO as $objProcNegado) {

                                        if ($objProcNegado->getStrStaAbrangencia() == 'E') {

                                            $objMdPetRelVincRepProtocDTO = new MdPetRelVincRepProtocDTO();
                                            $objMdPetRelVincRepProtocDTO->retNumIdProtocolo();
                                            $objMdPetRelVincRepProtocDTO->setNumIdVincRepresent($objProcNegado->getNumIdMdPetVinculoRepresent());
                                            $arrMdPetRelVincRepProtocDTO = (new MdPetRelVincRepProtocRN())->consultar($objMdPetRelVincRepProtocDTO);

                                            if ($arrMdPetRelVincRepProtocDTO->getNumIdProtocolo() == $idProtocolo) {
                                                $procNegado = false;
                                            }

                                        }

                                    }

                                }

                            }

                        }

                    }

                    if(is_null($temProcuracao)){
                        $situacao = $this::$INTIMACAO_NEGADA;
                    }else if($temProcuracao && $qtdAceites == 0){
                        $situacao = $this::$INTIMACAO_NAO_CUMPRIDA;
                    }else if($temProcuracao && ($qtdAceites > 0 && $qtdAceites < $qtdDestinatariosIntimacao)){
                        $situacao = $this::$INTIMACAO_CUMPRIDA_PARCIAL;
                    }else if($temProcuracao && ($qtdAceites == $qtdDestinatariosIntimacao)){
                        $situacao = $this::$INTIMACAO_CUMPRIDA;
                    }

                }

                if (!empty($arrObjDestinatariosIntimacoes)) {

                    if (($existeInt && $situacao == $this::$INTIMACAO_CUMPRIDA) || $situacao == $this::$INTIMACAO_CUMPRIDA) {

                        $isValido   = (new MdPetIntCertidaoRN())->verificaDocumentoEAnexoIntimacaoNaoCumprida(array($idProtocolo, $idAcessoExterno));
                        $strLink    = (new MdPetIntCertidaoRN())->retornaLinkAcessoDocumento($idProtocolo, $idAcessoExterno, $isProcedimento);

                        $initMsg    = $isProcedimento ? 'Processo' : 'Documento';
                        $alertMsg   = $initMsg . ' bloqueado, pois está vinculado a uma Intimação ainda não Cumprida.';
                        $js         = $isValido ? 'window.open(\'' . $strLink . '\');' : 'alert(\'' . $alertMsg . '\')';

                        if ($objRelIntDoc->getStrSinPrincipal() == 'S') {

                            $isMain = true;
                            if ($situacao == $this::$INTIMACAO_CUMPRIDA) {
                                $img = $htmlImgIntCumpridaPrinc;
                            } else if ($situacao == $this::$INTIMACAO_NAO_CUMPRIDA) {
                                $img = $htmlImgIntNaoCumpPrinc;
                            }

                        } else {

                            if ($situacao == $this::$INTIMACAO_CUMPRIDA) {
                                $img = $htmlImgIntCumpridaAnex;
                            } else if ($situacao == $this::$INTIMACAO_NAO_CUMPRIDA) {
                                $img = $htmlImgIntNaoCumprAnex;
                            }

                        }

                    } else {

                        $urlBase = ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL');
                        $linkIdIntimacao = '';

                        foreach ($idIntimacaoBtnlink as $id) {
                            $linkIdIntimacao .= '&id_intimacao[]=' . $id;
                        }

                        $strLink = SessaoSEIExterna::getInstance()->assinarLink($urlBase . '/controlador_externo.php?acao=md_pet_intimacao_usu_ext_confirmar_aceite&id_acesso_externo=' . $idAcessoExterno . '&id_documento=' . $idProtocolo . $linkIdIntimacao);
                        $js = "infraAbrirJanelaModal('" . $strLink . "', 900, 400);";

                        $informeTooltipACG = null;
                        
                        if ($objRelIntDoc->getStrSinPrincipal() == 'S') {

                            if ($situacao == $this::$INTIMACAO_CUMPRIDA_PARCIAL) {
                                $img = $htmlImgIntAguardandoCumprGeralPrinc;
                                $informeTooltipACG = "Observe que esta Intimação possui destaque, pois envolve pelo menos um Destinatário que você representa em comum e outro Representante já cumpriu a Intimação. Ao consultar esta Intimação você estará cumprindo-a para os Destinatários ainda pendentes.";
                            } else {
                                $img = ($situacao == $this::$INTIMACAO_CUMPRIDA) ? $htmlImgIntCumpridaPrinc : $htmlImgIntNaoCumpPrinc;
                            }

                        } else {

                            if ($situacao == $this::$INTIMACAO_CUMPRIDA_PARCIAL) {
                                $img = $htmlImgIntAguardandoCumprGeralAnex;
                                $informeTooltipACG = "Observe que esta Intimação possui destaque, pois envolve pelo menos um Destinatário que você representa em comum e outro Representante já cumpriu a Intimação. Ao consultar esta Intimação você estará cumprindo-a para os Destinatários ainda pendentes.";
                            } else {
                                $img = ($situacao == $this::$INTIMACAO_CUMPRIDA) ? $htmlImgIntCumpridaAnex : $htmlImgIntNaoCumprAnex;
                            }

                        }

                    }

                    if ($procNegado) {
                        $vinculoRepresentanteInativo = true;
                        $img = ($objRelIntDoc->getStrSinPrincipal() == 'S') ? $htmlImgIntNaoCumprVinculoInativoPrinc : $htmlImgIntNaoCumprVinculoInativoAnex;
                    }

                    // Start Gabriel 20.06.2022

                    $retorno = (new MdPetIntimacaoRN())->retornaDadosDocPrincipalIntimacao((array) $objRelIntDoc->getNumIdMdPetIntimacao());

                    if (!is_null($retorno)) {
                        $docPrinc   = $retorno[0];
                        $docTipo    = $retorno[1];
                        $docNum     = $retorno[4];
                    }

                    // Verifico a situação do usuario perante os destinatarios da intimacao:
                    $situacao = (new MdPetIntRelDestinatarioRN())->getSituacaoUsuarioIntimacao($idProtocolo, $idAcessoExterno);

                    $urlBase = ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL');
                    $informeTooltipACG = null;

                    // Verificando a situação e determinando botões e as ações dos links

                    $vinculoRepresentanteInativo = true;
                    $img = $objRelIntDoc->getStrSinPrincipal() == 'S' ? $htmlImgIntNaoCumprVinculoInativoPrinc : $htmlImgIntNaoCumprVinculoInativoAnex;

                    if(in_array($situacao['btn_cumprir'], ['nao_cumprida'])){
                        
                        $vinculoRepresentanteInativo = false;
                        $img = $objRelIntDoc->getStrSinPrincipal() == 'S' ? $htmlImgIntNaoCumpPrinc : $htmlImgIntNaoCumprAnex;
                        $idsIntsCumprir = '&id_intimacao[]=' . implode('&id_intimacao[]=', array_unique($situacao['int_cumprir']));
                        $strLink = SessaoSEIExterna::getInstance()->assinarLink($urlBase . '/controlador_externo.php?acao=md_pet_intimacao_usu_ext_confirmar_aceite&id_acesso_externo=' . $idAcessoExterno . '&id_documento=' . $idProtocolo . $idsIntsCumprir);
                        $js = "infraAbrirJanelaModal('" . $strLink . "', 900, 400);";
                    
                    }else if(in_array($situacao['btn_cumprir'], ['cumprida_parcial'])){
                        
                        $vinculoRepresentanteInativo = false;
                        $img = $objRelIntDoc->getStrSinPrincipal() == 'S' ? $htmlImgIntAguardandoCumprGeralPrinc : $htmlImgIntAguardandoCumprGeralAnex;
                        $idsIntsCumprir = '&id_intimacao[]=' . implode('&id_intimacao[]=', array_unique($situacao['int_cumprir']));
                        $informeTooltipACG = 'Observe que esta Intimação possui destaque, pois envolve pelo menos um Destinatário que você representa em comum e outro Representante já cumpriu a Intimação. Ao consultar esta Intimação você estará cumprindo-a para os Destinatários ainda pendentes.';
                        $strLink = SessaoSEIExterna::getInstance()->assinarLink($urlBase . '/controlador_externo.php?acao=md_pet_intimacao_usu_ext_confirmar_aceite&id_acesso_externo=' . $idAcessoExterno . '&id_documento=' . $idProtocolo . $idsIntsCumprir);
                        $js = "infraAbrirJanelaModal('" . $strLink . "', 900, 400);";

                    }else if(in_array($situacao['btn_cumprir'], ['cumprida_geral'])){
                            
                        $vinculoRepresentanteInativo = false;
                        $strLink = SessaoSEIExterna::getInstance()->assinarLink($urlBase . '/documento_consulta_externa.php?id_acesso_externo=' . $idAcessoExterno . '&id_documento=' . $idProtocolo);
                        $js = 'window.open(\'' . $strLink . '\')';
                        $img = $objRelIntDoc->getStrSinPrincipal() == 'S' ? $htmlImgIntCumpridaPrinc : $htmlImgIntCumpridaAnex;
                    
                    }

                    // End Gabriel 20.06.2022

                    if (is_array($arrObjDestinatariosIntimacoesCopia)) {

                        $objMdPetIntRelDestDTO2 = $arrObjDestinatariosIntimacoesCopia;
                        $dtIntimacao = '';

                        foreach ($objMdPetIntRelDestDTO2 as $obj) {

                            if ($obj->getStrSinPessoaJuridica() == 'S') {
                                if (!key_exists(InfraUtil::formatarCpfCnpj($obj->getDblCnpjContato()), $arrPessoaJuridica)) {
                                    $arrPessoaJuridica[InfraUtil::formatarCpfCnpj($obj->getDblCnpjContato())] = $obj->getStrNomeContato() . ' (' . InfraUtil::formatarCpfCnpj($obj->getDblCnpjContato()) . ')';
                                }
                            } else {
                                if (!key_exists(InfraUtil::formatarCpfCnpj($obj->getDblCpfContato()), $arrPessoaFisica)) {
                                    $arrPessoaFisica[InfraUtil::formatarCpfCnpj($obj->getDblCpfContato())] = $obj->getStrNomeContato() . ' (' . InfraUtil::formatarCpfCnpj($obj->getDblCpfContato()) . ')';
                                }
                            }

                            $objMdPetIntAceiteDTO = new MdPetIntAceiteDTO();
                            $objMdPetIntAceiteDTO->setNumIdMdPetIntRelDestinatario($obj->getNumIdMdPetIntRelDestinatario());
                            $objMdPetIntAceiteDTO->retTodos();
                            $objMdPetIntAceiteDTO = (new MdPetIntAceiteRN())->consultar($objMdPetIntAceiteDTO);

                            if (is_null($objMdPetIntAceiteDTO)) {
                                //data para exibir na modal do cumprir
                                if (($dtIntimacao && strtotime($dtIntimacao) < strtotime($obj->getDthDataCadastro())) || !$dtIntimacao) {
                                    if (is_array(explode(' ', $obj->getDthDataCadastro()))) {
                                        $dtIntimacao = explode(' ', $obj->getDthDataCadastro());
                                        $dtIntimacao = $dtIntimacao[0];
                                    } else {
                                        $dtIntimacao = $obj->getDthDataCadastro();
                                    }
                                } else {
                                    $dtIntimacao = explode(' ', $obj->getDthDataCadastro());
                                    $dtIntimacao = $dtIntimacao[0];
                                }
                            } else {
                                $dtIntimacao = explode(' ', $objMdPetIntAceiteDTO->getDthDataConsultaDireta());
                                $dtIntimacao = $dtIntimacao[0];
                            }

                        }

                    }

                    //Preparar Texto Exibição Tool Tip
                    if ($existeInt && !$vinculoRepresentanteInativo) {

                        $tooltip = (new MdPetIntimacaoRN())->getTextoTolTipIntimacaoEletronicaCumprida(array($dataAceite, $docPrinc, $docTipo, $docNum, $objRelIntDoc->getStrSinPrincipal(), $arrPessoaJuridica, $arrPessoaFisica, $informeTooltipACG));
                    
                    } elseif ($vinculoRepresentanteInativo) {

                        $idContatoDestinatario = InfraArray::converterArrInfraDTO($arrObjDestinatariosIntimacoesCopia, 'IdContato');
                        
                        if (is_iterable($idContatoDestinatario)) {
                            foreach ($idContatoDestinatario as $id) {
                                $linkIdDestinatario .= '&id_contato[]=' . $id;
                            }
                        }

                        $tooltip = (new MdPetIntimacaoRN())->getTextoTolTipIntimacaoEletronicaVinculoInativo($dtIntimacao, $docPrinc, $docTipo, $docNum, $objRelIntDoc->getStrSinPrincipal(), $objContato->getNumIdContato(), $idContatoDestinatario, $arrPessoaJuridica, $arrPessoaFisica);
                        
                        $urlBase = ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL');
                        $strLink = SessaoSEIExterna::getInstance()->assinarLink($urlBase . '/controlador_externo.php?acao=md_pet_intimacao_usu_ext_negar_cumprir&id_documento=' . $idDocumento . '&id_acesso_externo=' . $idAcessoExterno . $linkIdDestinatario . '&id_destinatario=' . $objContato->getNumIdContato());

                        $js = "infraAbrirJanelaModal('" . $strLink . "', 700, 250);";
                        
                    } else {

                        $tooltip = (new MdPetIntimacaoRN())->getTextoTolTipIntimacaoEletronica(array($dtIntimacao, $docPrinc, $docTipo, $docNum, $objRelIntDoc->getStrSinPrincipal(), $arrPessoaJuridica, $arrPessoaFisica, $informeTooltipACG));
                    
                    }

                    $arr = array();
                    $arr[0] = $js;
                    $arr[1] = $img;
                    $arr[2] = count($tooltip) > 0 ? $tooltip[0] : '';
                    $arr[3] = count($tooltip) > 0 ? $tooltip[1] : '';

                    $conteudoHtml = (new MdPetIntimacaoRN())->retornaLinkCompletoIconeIntimacao($arr);

                }

            }

            return $conteudoHtml;

        }

    }

    public function montarAcaoBotaoCertidao($idProtocolo, $idAcessoExterno)
    {

        $objMdPetCertidaoRN = new MdPetIntCertidaoRN();
        $objMdPetIntRN = new MdPetIntimacaoRN();
        $objMdPetIntRelDestRN = new MdPetIntRelDestinatarioRN();
        $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
        $objMdPetIntProtocoloRN = new MdPetIntProtocoloRN();
        $objMdPetAcessoExtDocRN = new MdPetIntAcessoExternoDocumentoRN();

        $objMdPetIntDocumentoDTO = new MdPetIntProtocoloDTO();
        $objMdPetIntDocumentoDTO->setDblIdProtocolo($idProtocolo);
        $objMdPetIntDocumentoDTO->retDblIdDocumento();
        $objMdPetIntDocumentoDTO->retStrSinPrincipal();
        $objMdPetIntDocumentoDTO->retNumIdMdPetIntimacao();
        $listaDocs = $objMdPetIntProtocoloRN->listar($objMdPetIntDocumentoDTO);

        $qtdListaDocs = (is_array($listaDocs) ? count($listaDocs) : 0);

        $arrExibidos = [];

        if ($qtdListaDocs > 0) {

            foreach ($listaDocs as $objRelIntDoc) {

                $existeInt      = false;
                $arrDados       = null;

                $idIntimacao    = $objRelIntDoc ? $objRelIntDoc->getNumIdMdPetIntimacao() : null;

                $objContato     = $objMdPetIntAceiteRN->retornaObjContatoIdUsuario(array(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()));
                $arrDados       = $objMdPetIntAceiteRN->existeAceiteIntimacaoAcao(array($idIntimacao, true));

                //Será exibida uma certidão para cada intimação cumprida por pessoa (Física ou Jurídica)

                if ($arrDados) {

                    foreach ($arrDados as $aceite) {

                        $existeInt = $aceite['INT'];

                        $objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
                        $objMdPetIntRelDestDTO->setNumIdMdPetIntimacao($idIntimacao);
                        $objMdPetIntRelDestDTO->retDthDataCadastro();
                        $objMdPetIntRelDestDTO->retNumIdMdPetIntRelDestinatario();
                        $objMdPetIntRelDestDTO->setNumIdMdPetIntRelDestinatario($aceite['ID_DESTINATARIO']);
                        // $objMdPetIntRelDestDTO->setNumIdContatoParticipante($objContato->getNumIdContato());
                        $objMdPetIntRelDestDTO->retStrStaSituacaoIntimacao();
                        $objMdPetIntRelDestDTO->retStrSinPessoaJuridica();
                        $objMdPetIntRelDestDTO->retNumIdContato();
                        $objMdPetIntRelDestDTO->retDblCnpjContato();
                        $objMdPetIntRelDestDTO->retDblCpfContato();
                        $objMdPetIntRelDestDTO->retStrNomeContato();

                        $idAcessoExternoValido = $objMdPetAcessoExtDocRN->verificarAcessoExternoValido(array($idIntimacao, $objContato->getNumIdContato(), $idAcessoExterno));

                        if (!is_null($idAcessoExternoValido)) {
                            $objMdPetIntRelDestDTO->setNumIdAcessoExterno($idAcessoExterno);
                        }

                        $objMdPetIntRelDestDTO = $objMdPetIntRelDestRN->listar($objMdPetIntRelDestDTO);

                        if (is_array($objMdPetIntRelDestDTO) && count($objMdPetIntRelDestDTO) > 0) {

                            $objMdPetIntRelDestDTO2 = $objMdPetIntRelDestDTO;
                            $dtIntimacao = '';
                            $qntDest = (is_array($objMdPetIntRelDestDTO2) ? count($objMdPetIntRelDestDTO2) : 0);
                            $qntDestRevogado = 0;

                            foreach ($objMdPetIntRelDestDTO2 as $obj) {
                                $arrDestinatarios = array();

                                $retorno = $objMdPetIntRN->retornaDadosDocPrincipalIntimacao((array)$idIntimacao);
                                if (!is_null($retorno)) {
                                    $docPrinc = $retorno[0];
                                }

                                $cpfCnpj = $obj->getStrSinPessoaJuridica() == 'S' ? $obj->getDblCnpjContato() : $obj->getDblCpfContato();

                                $arrDestinatarios[] = $obj->getStrNomeContato() . ' (' . InfraUtil::formatarCpfCnpj($cpfCnpj) . ')';

                                if ($existeInt && !in_array($cpfCnpj, $arrExibidos)) {
                                    //Botão
                                    $conteudoHtml .= $objMdPetCertidaoRN->addIconeAcessoCertidao(array($docPrinc, $idIntimacao, $idAcessoExterno, $aceite['ID_DOCUMENTO_CERTIDAO'], $arrDestinatarios, $aceite['DATA_ACEITE']));
                                    $arrExibidos[] = $cpfCnpj;
                                }

                            }

                        }

                    } // end foreach
                }
            }
        }
        return $conteudoHtml;
    }

    public function montarAcaoBotaoResposta($idProtocolo, $idAcessoExterno, $idProcedimento)
    {

        $objMdPetCertidaoRN = new MdPetIntCertidaoRN();
        $objMdPetIntRN = new MdPetIntimacaoRN();
        $objMdPetIntRelDestRN = new MdPetIntRelDestinatarioRN();
        $objMdPetIntProtocoloRN = new MdPetIntProtocoloRN();
        $objMdPetAcessoExtDocRN = new MdPetIntAcessoExternoDocumentoRN();
        $objMdPetRespostaRN = new MdPetIntRespostaRN();

        $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
        $objContato = $objMdPetIntAceiteRN->retornaObjContatoIdUsuario(array(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()));

        $objMdPetIntDocumentoDTO = new MdPetIntProtocoloDTO();
        $objMdPetIntDocumentoDTO->setDblIdProtocolo($idProtocolo);
        $objMdPetIntDocumentoDTO->retDblIdDocumento();
        $objMdPetIntDocumentoDTO->retStrSinPrincipal();
        $objMdPetIntDocumentoDTO->retNumIdMdPetIntimacao();
        $listaDocs = $objMdPetIntProtocoloRN->listar($objMdPetIntDocumentoDTO);

        $existeInt = false;
        $qtdListaDocs = (is_array($listaDocs) ? count($listaDocs) : 0);

        if ($qtdListaDocs > 0) {

            $idIntimacaoBotao = InfraArray::converterArrInfraDTO($listaDocs, 'IdMdPetIntimacao');
            $idIntimacaoBtnlink = [];
            $qntDoc = (is_array($listaDocs) ? count($listaDocs) : 0);
            $documentos = 0;
            $idAceite = null;
            $idDestinatario = null;
            $arrRevogado = [];
            $arrPessoaJuridica = array();
            $arrPessoaFisica = array();

            foreach ($listaDocs as $objRelIntDoc) {

                $documentos++;
                $idIntimacao = $objRelIntDoc ? $objRelIntDoc->getNumIdMdPetIntimacao() : null;
                $arrDados = null;

                if ($objRelIntDoc) {
                    $arrDados = $objMdPetIntAceiteRN->existeAceiteIntimacaoAcao(array($idIntimacao, true));
                    if ($arrDados) {
                        foreach ($arrDados as $aceite) {
                            $existeInt = $aceite['INT'];
                            $idAceite[] = $aceite['ID_ACEITE'];
                            $idDestinatario = $aceite['ID_DESTINATARIO'];
                        }
                    }
                }

                $objMdPetIntRelDestDTO = (new MdPetIntRelDestinatarioRN())->retornarDestinatariosIntimacao($idIntimacao, $objContato->getNumIdContato(), $idAcessoExterno);
                $idIntimacaoBtnlink = array_merge($idIntimacaoBtnlink, array_diff(array_unique(InfraArray::converterArrInfraDTO($objMdPetIntRelDestDTO, 'IdMdPetIntimacao')), $idIntimacaoBtnlink));

                if (is_array($objMdPetIntRelDestDTO) && count($objMdPetIntRelDestDTO) > 0) {

                    $objMdPetIntRelDestDTO2 = $objMdPetIntRelDestDTO;

                    $dtIntimacao = '';

                    $qntDest = is_array($objMdPetIntRelDestDTO2) ? count($objMdPetIntRelDestDTO2) : 0;
                    $qntDestRevogado = 0;

                    foreach ($objMdPetIntRelDestDTO2 as $obj) {

                        $existeAceite = false;
                        $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
                        $objMdPetVincRepresentantDTO->setNumIdContato($objContato->getNumIdContato());
                        $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
                        $objMdPetVincRepresentantDTO->setNumIdContatoVinc($obj->getNumIdContato());
                        $objMdPetVincRepresentantDTO->retNumIdContatoVinc();
                        $objMdPetVincRepresentantDTO->retNumIdContato();
                        $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
                        $objMdPetVincRepresentantDTO->retStrTipoRepresentante();
                        $objMdPetVincRepresentantDTO->retStrStaEstado();
                        $objMdPetVincRepresentantDTO->retDthDataLimite();
                        $objMdPetVincRepresentantDTO->retStrStaAbrangencia();

                        $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
                        $contarobjMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->contar($objMdPetVincRepresentantDTO);
                        $objMdPetVinculoDTO = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);
                        $procuracaoSimplesValida = true;

                        //Caso seja uma procuração simples é verificada se a mesma está valida
                        foreach ($objMdPetVinculoDTO as $chaveVinculo => $itemObjMdPetVinculoDTO) {
                            if ($itemObjMdPetVinculoDTO->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES) {
                                $rnMdPetIntimacaoRN = new MdPetIntimacaoRN();
                                $verificacaoCriteriosProcuracaoSimples = $rnMdPetIntimacaoRN->_verificarCriteriosProcuracaoSimples($itemObjMdPetVinculoDTO->getNumIdMdPetVinculoRepresent(), $itemObjMdPetVinculoDTO->getStrStaEstado(), $itemObjMdPetVinculoDTO->getDthDataLimite(), $obj->getDblIdDocumento(), $itemObjMdPetVinculoDTO->getStrStaAbrangencia());

                                if (!$verificacaoCriteriosProcuracaoSimples) {
                                    $procuracaoSimplesValida = false;
                                }
                            }
                        }

                        //Verifica se a situação da vinculação/intimacao é diferente de ativa
                        if ((!$procuracaoSimplesValida || $contarobjMdPetVincRepresentantDTO == 0) && $objContato->getNumIdContato() != $obj->getNumIdContato()) {
                            $qntDestRevogado++;
                        }

                        if ($obj->getStrSinPessoaJuridica() == 'S') {
                            if (!key_exists($obj->getDblCnpjContato(), $arrPessoaJuridica)) {
                                $arrPessoaJuridica[$obj->getDblCnpjContato()] = $obj->getStrNomeContato() . ' (' . InfraUtil::formatarCpfCnpj($obj->getDblCnpjContato()) . ')';
                            }
                        } else {
                            if (!key_exists($obj->getDblCpfContato(), $arrPessoaFisica)) {
                                $arrPessoaFisica[$obj->getDblCpfContato()] = $obj->getStrNomeContato() . ' (' . InfraUtil::formatarCpfCnpj($obj->getDblCpfContato()) . ')';
                            }
                        }

                        //data para exibir na modal do cumprir
                        if (($dtIntimacao && strtotime($dtIntimacao) < strtotime($obj->getDthDataCadastro())) || !$dtIntimacao) {
                            if (is_array(explode(' ', $obj->getDthDataCadastro()))) {
                                $dtIntimacao = explode(' ', $obj->getDthDataCadastro());
                                $dtIntimacao = $dtIntimacao[0];
                            } else {
                                $dtIntimacao = $obj->getDthDataCadastro();
                            }
                        }
                    }

                    //se a quantidade de intimações for igual a quantidade de vinculos diferente de ativo é exibido o ícone de vinculo inativo
                    if ($qntDest == $qntDestRevogado) {
                        $vinculoRepresentanteInativo = true;
                        $img = $htmlImgIntNaoCumprVinculoInativoPrinc;
                    }
                }

                $mdPetVinculoRN = new MdPetVinculoRN();
                $objMdPetIntRelDestDTOTratado = $objMdPetIntRelDestDTO;

                foreach ($objMdPetIntRelDestDTOTratado as $chave => $itemObjMdPetIntRelDestDTOTratado) {

                    $objMdPetVinculoDTO = new MdPetVinculoDTO();
                    $objMdPetVinculoDTO->setNumIdContato($itemObjMdPetIntRelDestDTOTratado->getNumIdContato());
                    $objMdPetVinculoDTO->setNumIdContatoRepresentante($objContato->getNumIdContato());
                    $objMdPetVinculoDTO->retStrStaEstado();
                    $objMdPetVinculoDTO->retNumIdMdPetVinculoRepresent();
                    $objMdPetVinculoDTO->retStrTipoRepresentante();
                    $objMdPetVinculoDTO->retDthDataLimite();
                    $objMdPetVinculoDTO->retStrStaAbrangencia();
                    $objMdPetVinculoDTO->retStrStaEstado();
                    $objMdPetVinculoDTO = $mdPetVinculoRN->listar($objMdPetVinculoDTO);

                    $removerRevogado = true;

                    foreach ($objMdPetVinculoDTO as $chaveVinculo => $itemObjMdPetVinculoDTO) {
                        if ($itemObjMdPetVinculoDTO->getStrStaEstado() == MdPetVincRepresentantRN::$RP_ATIVO && $itemObjMdPetVinculoDTO->getStrTipoRepresentante() != MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES) {
                            $removerRevogado = false;
                        }
                        if ($itemObjMdPetVinculoDTO->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES) {
                            $rnMdPetIntimacaoRN = new MdPetIntimacaoRN();
                            $verificacaoCriteriosProcuracaoSimples = $rnMdPetIntimacaoRN->_verificarCriteriosProcuracaoSimples($itemObjMdPetVinculoDTO->getNumIdMdPetVinculoRepresent(), $itemObjMdPetVinculoDTO->getStrStaEstado(), $itemObjMdPetVinculoDTO->getDthDataLimite(), $itemObjMdPetIntRelDestDTOTratado->getDblIdDocumento(), $itemObjMdPetVinculoDTO->getStrStaAbrangencia());
                            if ($verificacaoCriteriosProcuracaoSimples) {
                                $removerRevogado = false;
                            }
                        }
                    }

                    if ($removerRevogado) {
                        unset($objMdPetIntRelDestDTOTratado[$chave]);
                    }

                }

                $qntAceite = is_array($arrDados) ? count($arrDados) : 0;

                if (!empty($objMdPetIntRelDestDTO)) {

                    $idMdPetDest = InfraArray::converterArrInfraDTO($objMdPetIntRelDestDTO, 'IdMdPetIntRelDestinatario');

                    if ($existeInt) {

                        //Botão
                        $objDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
                        $objDestinatarioDTO->retTodos();
                        $objDestinatarioDTO->setNumIdMdPetIntRelDestinatario($idMdPetDest, InfraDTO::$OPER_IN);
                        $objDestinatarioDTO->setNumIdMdPetIntimacao($idIntimacao);
                        $arrDestinatarioDTO = (new MdPetIntRelDestinatarioRN())->listar($objDestinatarioDTO);

                        $arrContatos = InfraArray::converterArrInfraDTO($arrDestinatarioDTO, 'IdContato');

                        $objUsuarioDTO = new UsuarioDTO();
                        $objUsuarioDTO->retNumIdContato();
                        $objUsuarioDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
                        $arrIds = (new UsuarioRN())->listarRN0490($objUsuarioDTO);

                        if ($arrContatos) {

                            $dtoMdPetVincReptDTO = new MdPetVincRepresentantDTO();
                            $dtoMdPetVincReptDTO->setNumIdContatoVinc($arrContatos, InfraDTO::$OPER_IN);
                            $dtoMdPetVincReptDTO->retNumIdContatoVinc();
                            $dtoMdPetVincReptDTO->retStrNomeProcurador();
                            $dtoMdPetVincReptDTO->retStrRazaoSocialNomeVinc();
                            $dtoMdPetVincReptDTO->retStrTipoRepresentante();
                            $dtoMdPetVincReptDTO->retStrCNPJ();
                            $dtoMdPetVincReptDTO->retStrEmail();
                            $dtoMdPetVincReptDTO->setNumIdContatoProcurador($arrIds[0]->getNumIdContato());
                            $dtoMdPetVincReptDTO->retNumIdContatoProcurador();
                            $dtoMdPetVincReptDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
                            $dtoMdPetVincReptDTO->retStrStaEstado();
                            $arrMdPetVincRepRN = (new MdPetVincRepresentantRN())->listar($dtoMdPetVincReptDTO);

                            $qtdArrMdPetVincRepRN = is_array($arrMdPetVincRepRN) ? count($arrMdPetVincRepRN) : 0;

                            if ($qtdArrMdPetVincRepRN > 0) {
                                foreach ($arrMdPetVincRepRN as $vinculo) {
                                    if ($vinculo->getStrStaEstado() != MdPetVincRepresentantRN::$RP_ATIVO) {
                                        $arrRevogado[] = $vinculo->getStrCNPJ();
                                    }
                                }
                            }else{
                                $arrRevogado[] = time();
                            }
                            
                        } // EndIf $arrContatos
                        
                    } // EndIf $existeInt
                    
                } // !empty($objMdPetIntRelDestDTO)
                
            } // EndForeach
            
        } // EndIf

        $idContatoDestinatario = array_unique(InfraArray::converterArrInfraDTO($objMdPetIntRelDestDTO, 'IdContato'));

        if ($existeInt) {

            // Gabriel em 20.06.2022

            $situacao = (new MdPetIntRelDestinatarioRN())->getSituacaoUsuarioIntimacao($idProtocolo, $idAcessoExterno);

            if(in_array($situacao['btn_responder'], ['cumprida_geral', 'cumprida_parcial'])){

                $arrPrazoResposta = (new MdPetIntPrazoRN())->retornarTipoRespostaValido([$situacao['int_responder'][0], $idMdPetDest]);

                if(is_array($arrPrazoResposta) && count($arrPrazoResposta) > 0){
                    $dtPrazoResposta = !is_null($arrPrazoResposta[0]->getDthDataProrrogada()) ? $arrPrazoResposta[0]->getDthDataProrrogada() : $arrPrazoResposta[0]->getDthDataLimite();
                    if(!empty($dtPrazoResposta) && InfraData::compararDatas(date('d/m/Y'), $dtPrazoResposta) >= 0){
                        $conteudoHtml .= $objMdPetRespostaRN->addIconeRespostaAcao(array($idIntimacaoBtnlink, $idAcessoExterno, $idProcedimento, $idAceite, $idMdPetDest, $arrPessoaJuridica, $arrPessoaFisica));
                    }
                }

            }else if(in_array($situacao['btn_responder'], ['com_impedimento'])){
                $conteudoHtml .= $objMdPetRespostaRN->addIconeRespostaNegada(array($idIntimacaoBtnlink, $idAcessoExterno, $idProcedimento, $idAceite, $idMdPetDest, $objContato->getNumIdContato(), $idContatoDestinatario, $arrPessoaJuridica, $arrPessoaFisica, $idProtocolo));
            }

        }
        
        return $conteudoHtml;

    }

    public function montarAcaoDocumentoAcessoExternoAutorizado($arrObjDocumentoAPI)
    {
        return $this->montarBotaoAcessoExternoPeticionamento($arrObjDocumentoAPI);
    }

    public function montarAcaoProcessoAnexadoAcessoExternoAutorizado($arrObjProcedimentAPI)
    {

        return $this->montarBotaoAcessoExternoPeticionamento($arrObjProcedimentAPI, true);
    }

    public function verificarAcessoProtocoloExterno($arrObjProcedimentoAPI, $arrObjDocumentoAPI)
    {
        if (self::$arrProtocoloAcessoExterno) {
            return self::$arrProtocoloAcessoExterno;
        }

        $id_usuario_externo = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();

        $idAcessoExterno = $_GET['id_acesso_externo'];

        //so verifica o acesso se houver usuario externo logado (usuario de acesso externo avulso nao verifica nada)
        if ($id_usuario_externo == null || $id_usuario_externo == "" || is_null($idAcessoExterno)) {
            //retorna array vazio para nao interferir na verificacao de nenhum protocolo/documento
            return array();
        } else {

            $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
            $objMdPetIntAcExtRN = new MdPetIntAcessoExternoDocumentoRN();
            $objMdPetAceiteRN = new MdPetIntAceiteRN();
            $objInfraParametro = new InfraParametro(BancoSEI::getInstance());

            //ids de todos os docs do processo
            $idProtocoloDoProcesso = array();

            foreach ($arrObjDocumentoAPI as $objDocumentoAPI) {
                $idProtocoloDoProcesso[] = $objDocumentoAPI->getIdDocumento();
            }

            //add ids dos processos anexados pois podem ser anexados no mesmo nível do documento e adicionado como anexo da Intimação

            foreach ($arrObjProcedimentoAPI as $objProcedimentoAPI) {
                $idProtocoloDoProcesso[] = $objProcedimentoAPI->getIdProcedimento();
            }

            //se o processo nao tiver documentos ja pode passar direto a verificação
            if (count($idProtocoloDoProcesso) == 0) {
                return array();
            }

            /*
             * saber exatamente quais docs estao envolvidos com a intimação
             * (e com a resposta a intimaçao? por enquanto ignorar esses):
             * aqueles que nao estiverem, nao adicionar no array de retorno
             * para que o controle de acesso a eles siga para a logica
             * padrao aplicada pelo Core do SEI
             * @todo precisa ser refatorado o codigo para ser mais perfomatico, pode ocorrer despecho de memoria
             * */
            //Get ids Intimacao Contato
            $arrIntimacoesContato = $objMdPetIntimacaoRN->getIntimacoesPorContato($idProtocoloDoProcesso);

            if ($arrIntimacoesContato) {
                //ids de documentos envolvidos na intimação
                $idDocumentosEnvolvidosNaIntimacao = array();

                $objIntimacaoDocDTO = new MdPetIntProtocoloDTO();
                $objIntimacaoDocDTO->setDblIdProtocolo($idProtocoloDoProcesso, InfraDTO::$OPER_IN);
                $objIntimacaoDocDTO->setNumIdMdPetIntimacao($arrIntimacoesContato, InfraDTO::$OPER_IN);
                $objIntimacaoDocDTO->retDblIdProtocolo();
                $arrObjIntimacaoDocDTO = (new MdPetIntProtocoloRN())->listar($objIntimacaoDocDTO);

                $objIntimacaoDocDisponivelDTO = new MdPetIntProtDisponivelDTO();
                $objIntimacaoDocDisponivelDTO->retDblIdProtocolo();
                $objIntimacaoDocDisponivelDTO->setDblIdProtocolo($idProtocoloDoProcesso, InfraDTO::$OPER_IN);
                $objIntimacaoDocDisponivelDTO->setNumIdMdPetIntimacao($arrIntimacoesContato, InfraDTO::$OPER_IN);
                $arrIntimacaoDocDisponivelDTO = (new MdPetIntProtDisponivelRN())->listar($objIntimacaoDocDisponivelDTO);

                foreach ($arrObjIntimacaoDocDTO as $docIntimacaoDTO) {
                    $idDocumentosEnvolvidosNaIntimacao[] = $docIntimacaoDTO->getDblIdProtocolo();
                }

                foreach ($arrIntimacaoDocDisponivelDTO as $docIntimacaoDisponivelDTO) {
                    $idDocumentosEnvolvidosNaIntimacao[] = $docIntimacaoDisponivelDTO->getDblIdProtocolo();
                }

                $ret = array();
                foreach ($idDocumentosEnvolvidosNaIntimacao as $idProtocoloDaIntimacao) {

                    $objDocumentoAPIComIntimacao = null;
                    if (key_exists($idProtocoloDaIntimacao, $ret)) {
                        continue;
                    }
                    foreach ($arrObjDocumentoAPI as $objProtocolo) {
                        if ($objProtocolo->getIdDocumento() == $idProtocoloDaIntimacao) {
                            $objProtocoloAPIComIntimacao = $objDocumentoAPI;
                            $isDocumento = true;
                            break;
                        }

                        if ($objProtocolo->getIdProcedimento() == $idProtocoloDaIntimacao) {
                            $objProtocoloAPIComIntimacao = $objProcedimentoAPI;
                            $isDocumento = false;
                            break;
                        }
                    }

                    $permissao = SeiIntegracao::$TAM_NEGADO;

                    //Verifica se o documento possui intimações
                    $arrRetIntimacao = $objMdPetIntimacaoRN->retornaIntimacoesVinculadasDocumento($idProtocoloDaIntimacao);

                    $arrIntimacao = $arrRetIntimacao['I'];
                    $idSerieCertidao = $objInfraParametro->getValor(MdPetIntCertidaoRN::$STR_ID_SERIE_CERTIDAO);
                    $isCertidao = $isDocumento ? $objProtocoloAPIComIntimacao->getIdSerie() == $idSerieCertidao : false;

                    $objMdPetIntRelDestExternoDTO = new MdPetRelIntDestExternoDTO();
                    $objMdPetIntRelDestExternoDTO->setNumIdAcessoExterno($idAcessoExterno);
                    $objMdPetIntRelDestExternoDTO->retNumIdMdPetIntRelDestinatario();
                    $arrObjMdPetRelIntDestExternoDTO = (new MdPetRelIntDestExternoRN())->contar($objMdPetIntRelDestExternoDTO);

                    if ($arrObjMdPetRelIntDestExternoDTO == 0) {
                        $permissao = SeiIntegracao::$TAM_PERMITIDO;
                    } else {
                        //Se possui intimação realiza verificações
                        $qtdArrIntimacao = (is_array($arrIntimacao) ? count($arrIntimacao) : 0);
                        if ($qtdArrIntimacao > 0) {

                            $isAnexoDisponib = $arrRetIntimacao['T'] == 2;
                            $disp = $arrRetIntimacao['T'] == 1;

                            //se for somente disponibilizado permite visualização
                            if ($disp) {
                                $permissao = SeiIntegracao::$TAM_PERMITIDO;

                                //Se for disponibilizado e anexo, ignora os disponiblizados e  verifica se os anexos já aestão aceitos
                                //Se for anexo, verifica os aceites
                            } else {
                                //Verifica se todos os documentos  anexo possuem aceite
                                $intAnexo = $arrRetIntimacao['A'];
                                $todasIntAceit = $objMdPetAceiteRN->todasIntimacoesAceitas($intAnexo);

                                //Se todas as intimações possuem aceite para o anexo  permite visualização
                                if ($todasIntAceit['todasAceitas']) {
                                    $permissao = SeiIntegracao::$TAM_PERMITIDO;
                                } else {

                                    if (empty($idProtocoloDaIntimacao) || empty($idAcessoExterno)) {
                                        throw new InfraException("Parâmetros IdProtocoloDaIntimacao e IdAcessoExterno não podem ser nulos");
                                    } else {
                                        // Verifica a situação do usuário logado perante os destinatários e o cumprimento das intimações as quais o documento está vinculado
                                        $situacao = (new MdPetIntRelDestinatarioRN())->getSituacaoUsuarioIntimacao($idProtocoloDaIntimacao, $idAcessoExterno);
                                        if (in_array($situacao['btn_cumprir'], ['cumprida_geral'])) {
                                            $permissao = SeiIntegracao::$TAM_PERMITIDO;
                                        }
                                    }
                                }
                            }
                        } else {
                            //Se não possuir intimações e o acesso for integral permite visualização.
                            //Se for parcial, não permite.
                            $isIntAcessoExt = $objMdPetIntAcExtRN->getTipoConcessaoAcesso($_GET['id_acesso_externo']);
                            if ($isIntAcessoExt == MdPetIntAcessoExternoDocumentoRN::$ACESSO_INTEGRAL) {
                                $permissao = SeiIntegracao::$TAM_PERMITIDO;
                            }

                            if ($isCertidao) {
                                $permissao = SeiIntegracao::$TAM_PERMITIDO;
                            }
                        }
                    }
                    //Preenche o array de documento com o retorno
                    $ret[$idProtocoloDaIntimacao] = $permissao;
                }
            } else {
                $ret = array();
            }
            self::$arrProtocoloAcessoExterno = $ret;
            return $ret;
        }
    }

    public function montarBotaoDocumento(ProcedimentoAPI $objProcedimentoAPI, $arrObjDocumentoAPI)
    {

        $arrBotoes = array();

        if (!(SessaoSEI::getInstance()->verificarPermissao('md_pet_int_serie_listar')) || !(SessaoSEI::getInstance()->verificarPermissao('md_pet_vinc_documento_listar'))) {
            return $arrBotoes;
        }

        $objSessaoSEI = SessaoSEI::getInstance();

        $dblIdProcedimento = $_GET['id_procedimento'];

        foreach ($arrObjDocumentoAPI as $objDocumentoAPI) {

            $dblIdDocumento = $objDocumentoAPI->getIdDocumento();

            // Gerar Intimação
            if ($objProcedimentoAPI->getCodigoAcesso() > 0 && $objProcedimentoAPI->getSinAberto() == 'S') {
                if ($objDocumentoAPI->getCodigoAcesso() > 0) {

                    $objRelProtocoloProtocoloDTO = new RelProtocoloProtocoloDTO();
                    $objRelProtocoloProtocoloDTO->retTodos();
                    $objRelProtocoloProtocoloDTO->setDblIdProtocolo1($_GET['id_procedimento']);
                    $objRelProtocoloProtocoloDTO->setDblIdProtocolo2($dblIdDocumento);

                    $objRelProtocoloProtocoloRN = new RelProtocoloProtocoloRN();
                    $objRelProtocoloProtocoloDTO = $objRelProtocoloProtocoloRN->listarRN0187($objRelProtocoloProtocoloDTO);

                    $arrIdRelProtocoloProtocolo = array();

                    foreach ($objRelProtocoloProtocoloDTO as $objRelProtocoloProtocolo) {
                        $arrIdRelProtocoloProtocolo[] = $objRelProtocoloProtocolo->getDblIdRelProtocoloProtocolo();
                    }

                    $RelProtocoloProtocoloDTO = new RelProtocoloProtocoloDTO();
                    $RelProtocoloProtocoloDTO->setDblIdProtocolo2($dblIdDocumento);
                    $RelProtocoloProtocoloDTO->retStrStaAssociacao();
                    $arrRelProtocoloProtocoloDTO = (new RelProtocoloProtocoloRN())->listarRN0187($RelProtocoloProtocoloDTO);
                    foreach ($arrRelProtocoloProtocoloDTO as $objRelProtocoloProtocoloDTO) {
                        if ($objRelProtocoloProtocoloDTO->getStrStaAssociacao() == RelProtocoloProtocoloRN::$TA_DOCUMENTO_ASSOCIADO) {

                            $strSinAssinado = $objDocumentoAPI->getSinAssinado();

                            $arrBotoes[$dblIdDocumento] = array();

                            $strStaDocumento = $objDocumentoAPI->getTipo();
                            $idSerie = $objDocumentoAPI->getIdSerie();

                            //TODO: Ajuste local para a Anatel no if acima para exibir o botão da Gerar Intimação para os tipos de documento de id 184 (Comunicado de Cobrança) e 186 (Notificação de Lançamento). Para ativar, descomentar a linha abaixo e comentar a linha acima
                            if (($strSinAssinado == 'S' && $strStaDocumento <> 'X') || $idSerie == 184 || $idSerie == 186) {

                                $rnPetIntSerie = new MdPetIntSerieRN();
                                $dtoPetIntSerie = new MdPetIntSerieDTO();
                                $dtoPetIntSerie->retTodos();
                                $dtoPetIntSerie->setNumIdSerie($idSerie);

                                $arrDtoPetIntSerie = $rnPetIntSerie->listar($dtoPetIntSerie);

                                //encontrou o tipo de documento na parametrizacao do sistema e o perfil possui o recurso
                                $qtdArrDtoPetIntSerie = (is_array($arrDtoPetIntSerie) ? count($arrDtoPetIntSerie) : 0);
                                if ($qtdArrDtoPetIntSerie > 0 && $objSessaoSEI->verificarPermissao('md_pet_intimacao_cadastrar')) {
                                    $arrBotoes[$dblIdDocumento][] = '<a href="' . $objSessaoSEI->assinarLink('controlador.php?acao=md_pet_intimacao_cadastrar&acao_origem=arvore_visualizar&acao_retorno=arvore_visualizar&id_procedimento=' . $dblIdProcedimento . '&id_documento=' . $dblIdDocumento . '&arvore=1') . '" tabindex="' . PaginaSEI::getInstance()->getProxTabBarraComandosSuperior() . '" class="botaoSEI"><img class="infraCorBarraSistema" src="modulos/peticionamento/imagens/svg/intimacao_eletronica_gerar.svg?'.Icone::VERSAO.'" alt="Gerar Intimação Eletrônica" title="Gerar Intimação Eletrônica" style="width: 38px" /></a>';
                                }
                            }
                        }
                    }
                }
            }
        }

        return $arrBotoes;
    }

    public function montarAcaoDocumentoAcessoExternoNegado($arrObjDocumentoAPI)
    {

        //só é necessário adicionar botao por este ponto SE o processo estiver numa situaçao em que nao foi adicionado nenhum
        // botao pelo ponto de extensao do acesso externo autorizado (o que deixaria em alguns casos
        // a coluna "Ações" sem quaisquer icones
        // Exemplo: Processo com apenas 1 documento assinado, com intimaçao gerada do tipo integral e sem anexos
        $qtdArrDocumentosLiberados = (is_array($this->arrDocumentosLiberados) ? count($this->arrDocumentosLiberados) : 0);
        if ($qtdArrDocumentosLiberados == 0) {
            return $this->montarBotaoAcessoExternoPeticionamento($arrObjDocumentoAPI);
        } else {
            return null;
        }
    }

    public function montarAcaoProcessoAnexadoAcessoExternoNegado($arrObjProcedimentoAPI)
    {

        //só é necessário adicionar botao por este ponto SE o processo estiver numa situaçao em que nao foi adicionado nenhum
        // botao pelo ponto de extensao do acesso externo autorizado (o que deixaria em alguns casos
        // a coluna "Ações" sem quaisquer icones
        // Exemplo: Processo com apenas 1 documento assinado, com intimaçao gerada do tipo integral e sem anexos
        $qtdArrProcessosLiberados = (is_array($this->arrProcessosLiberados) ? count($this->arrProcessosLiberados) : 0);
        if ($qtdArrProcessosLiberados == 0) {
            return $this->montarBotaoAcessoExternoPeticionamento($arrObjProcedimentoAPI, true);
        } else {
            return null;
        }
    }

    public function cancelarDisponibilizacaoAcessoExterno($arrObjAcessoExternoAPI)
    {
        $objMdPetRegrasGeraisRN = new MdPetRegrasGeraisRN();
        $objMdPetAcessoExternoRN = new MdPetAcessoExternoRN();

        if ($_REQUEST['acao'] != 'md_pet_intimacao_cadastrar') {
            foreach ($arrObjAcessoExternoAPI as $objAcessoExternoAPI) {
                $idAcessoExt = $objAcessoExternoAPI->getIdAcessoExterno();
                $isModuloAcessoExt = $objMdPetAcessoExternoRN->verificaIdAcessoExternoModulo($idAcessoExt);

                if ($isModuloAcessoExt) {
                    $docTipoIntegral = $objMdPetRegrasGeraisRN->verificarDocumentoTipoIntegral($idAcessoExt);

                    if (!$docTipoIntegral) {
                        $objInfraException = new InfraException();
                        $objInfraException->adicionarValidacao('Não é permitido cancelar a disponibilização para esse usuário, pois existem vinculos no módulo Peticionamento e Intimação Eletrônicos.');
                        $objInfraException->lancarValidacoes();
                        return null;
                    } else {

                        $cumprimentoValido = $objMdPetRegrasGeraisRN->verificarCumprimentoIntimacao($idAcessoExt);
                        //TODO: Se forem comentadas as linhas 3230 ate 3248, ou seja, este if com o seu else (segunda chave depois do segunto return null), suspende a regra que impossibilita o cancelamento de Acesso Externo quando a intimacao ainda esta em curso
                        /*if (!$cumprimentoValido) {
                            $objInfraException = new InfraException();
                            $objInfraException->adicionarValidacao('Não é permitido cancelar esta disponibilização de Acesso Externo, pois existem Intimações Eletrônicas destinadas ao Usuário Externo ainda não cumpridas.');
                            $objInfraException->lancarValidacoes();
                            return null;
                        } else {
                            $objProcedimento = $objAcessoExternoAPI->getProcedimento();
                            $idProcedimento = $objProcedimento->getIdProcedimento();

                            $objRN = new MdPetIntimacaoRN();
                            $isRespIntPeriodo = $objRN->existeIntimacaoPrazoValido(array($idProcedimento, $idAcessoExt));

                            if ($isRespIntPeriodo) {
                                $objInfraException = new InfraException();
                                $objInfraException->adicionarValidacao('Não é permitido cancelar esta disponibilização de Acesso Externo, pois existem Intimações Eletrônicas destinadas ao Usuário Externo com Prazo Externo ainda vigente para Responder a Intimação.');
                                $objInfraException->lancarValidacoes();
                                return null;
                            }
                        } */
                    }

                    $objMdPetAcessoExternoRN->corrigirDadosPosCancelamentoAcessoIntegral($idAcessoExt);

                    return true;
                }
            }
        }

        return true;
    }

    /**
     * Valida se o Processo onde está realizando a anexação de processo possui Vínculo com Intimação
     */
    public function anexarProcesso(ProcedimentoAPI $objProcedimentoAPIPrincipal, ProcedimentoAPI $objProcedimentoAPIAnexado)
    {
        $idProcedimento = $objProcedimentoAPIAnexado->getIdProcedimento();

        $objRN = new MdPetIntimacaoRN();
        $isRespIntPeriodo = $objRN->existeIntimacaoPrazoValido($idProcedimento);

        if ($isRespIntPeriodo) {
            $msg = 'Não é permitido anexar este processo, pois o mesmo possui Intimação Eletrônica ainda em curso.';

            $objInfraException = new InfraException();
            $objInfraException->adicionarValidacao($msg);
            $objInfraException->lancarValidacoes();
        }

        return parent::anexarProcesso($objProcedimentoAPIPrincipal, $objProcedimentoAPIAnexado);
    }

    /**
     * Valida se o Processo onde está realizando a anexação de processo possui Vínculo com Intimação
     */
    public function sobrestarProcesso(ProcedimentoAPI $objProcedimentoAPI, $objProcedimentoAPIVinculado)
    {

        $idProcedimento = $objProcedimentoAPI->getIdProcedimento();

        $objRN = new MdPetIntimacaoRN();
        $isRespIntPeriodo = $objRN->existeIntimacaoPrazoValido($idProcedimento);

        if ($isRespIntPeriodo) {
            $msg = 'Não é permitido sobrestar este processo, pois o mesmo possui Intimação Eletrônica ainda em curso.';


            $objInfraException = new InfraException();
            $objInfraException->adicionarValidacao($msg);
            $objInfraException->lancarValidacoes();
        }

        return parent::sobrestarProcesso($objProcedimentoAPI, $objProcedimentoAPIVinculado);
    }

    /**
     * Valida se o Processo onde está realizando o bloqueio de processo possui Vínculo com Intimação
     */
    public function bloquearProcesso($objProcedimentoAPI)
    {

        $idProcedimento = $objProcedimentoAPI[0]->getIdProcedimento();

        $objRN = new MdPetIntimacaoRN();
        $isRespIntPeriodo = $objRN->existeIntimacaoPrazoValido($idProcedimento);

        if ($isRespIntPeriodo) {
            $msg = 'Não é permitido Bloquear este processo, pois o mesmo possui Intimação Eletrônica ainda em curso.';


            $objInfraException = new InfraException();
            $objInfraException->adicionarValidacao($msg);
            $objInfraException->lancarValidacoes();
        }

        return parent::bloquearProcesso($objProcedimentoAPI);
    }

    /**
     * Valida se o Processo que está sendo desanexado está como anexo de uma Intimação
     */
    public function desanexarProcesso(ProcedimentoAPI $objProcedimentoAPIPrincipal, ProcedimentoAPI $objProcedimentoAPIAnexado)
    {

        $idProcedimento = $objProcedimentoAPIAnexado->getIdProcedimento();

        $objRN = new MdPetIntProtocoloRN();
        $objRNDisp = new MdPetIntProtDisponivelRN();
        $isIntimacao = $objRN->verificaProcessoEAnexoIntimacao($idProcedimento);
        $isIntimacaoDisp = $objRNDisp->verificaProcessoEDocDisponivelIntimacao($idProcedimento);

        if ($isIntimacao || $isIntimacaoDisp) {
            if ($isIntimacao) {
                $msg = 'Não é permitido desanexar este processo, pois o mesmo é anexo de Documento Principal de Intimação Eletrônica neste processo.';
            } else {
                $msg = 'Não é permitido desanexar este processo, pois o mesmo é Documento Disponível de Intimação Eletrônica neste processo.';
            }

            $objInfraException = new InfraException();
            $objInfraException->adicionarValidacao($msg);
            $objInfraException->lancarValidacoes();
            return null;
        } else {

            return parent::desanexarProcesso($objProcedimentoAPIPrincipal, $objProcedimentoAPIAnexado);
        }
    }

    public function permitirAndamentoConcluido(AndamentoAPI $objAndamentoAPI)
    {

        $permitirCancelamento = array_key_exists(MdPetIntAtualizarAcessoExternoRN::$ID_PET_LIBERAR_ANDAMENTOS_CONCLUIDOS, $_SESSION) ? true : false;
        if ($objAndamentoAPI->getIdTarefa() == TarefaRN::$TI_CANCELAMENTO_LIBERACAO_ACESSO_EXTERNO && $permitirCancelamento) {
            return true;
        }

        return false;
    }

    public function desativarUnidade($arrObjUnidadeAPI)
    {

        $mdPetRegrasGeraisRN = new MdPetRegrasGeraisRN();
        $msg = $mdPetRegrasGeraisRN->verificarExistenciaUnidade(array($arrObjUnidadeAPI, 'desativar'));
        if ($msg != '') {
            $objInfraException = new InfraException();
            $objInfraException->lancarValidacao($msg);
        } else {
            return $arrObjUnidadeAPI;
        }
    }

    public function desativarTipoProcesso($arrObjTipoProcessoDTO)
    {

        $mdPetRegrasGeraisRN = new MdPetRegrasGeraisRN();
        $msg = $mdPetRegrasGeraisRN->verificarExistenciaTipoProcesso(array($arrObjTipoProcessoDTO, 'desativar'));

        //verifica se existe um processo sendo utilizado
        if ($msg != "") {
            $objInfraException = new InfraException();
            $objInfraException->lancarValidacao($msg);
        } else {
            return $arrObjTipoProcessoDTO;
        }
    }

    public function desativarTipoDocumento($arrObjSerieAPI)
    {

        $mdPetRegrasGeraisRN = new MdPetRegrasGeraisRN();
        $msg = $mdPetRegrasGeraisRN->verificarExistenciaTipoDocumento(array($arrObjSerieAPI, 'desativar'));

        // condição para saber se existe um documento sendo usado
        if ($msg != '') {
            $objInfraException = new InfraException();
            $objInfraException->lancarValidacao($msg);
        } else {
            $msg = $mdPetRegrasGeraisRN->verificarParametroTipoDocumento(array($arrObjSerieAPI, 'desativar'));
            if ($msg != '') {
                $objInfraException = new InfraException();
                $objInfraException->lancarValidacao($msg);
            } else {
                return $arrObjSerieAPI;
            }
        }
    }

	public function excluirUsuario($arrObjUsuarioAPI)
	{
        $msg = (new MdPetRegrasGeraisRN())->verificarExcluirDesativarUsuarioExterno([$arrObjUsuarioAPI, 'excluir']);
		if ($msg != '') {
			(new InfraException())->lancarValidacao($msg);
		}
	}

	/*
	 * TODO: Desativado à espera do novo evento no SeiIntegracao.php previsto para o SEI v4.1
	 */
//	public function desativarUsuario($arrObjUsuarioAPI)
//	{
//		$msg = (new MdPetRegrasGeraisRN())->verificarExcluirDesativarUsuarioExterno([$arrObjUsuarioAPI, 'desativar']);
//		if ($msg != '') {
//			(new InfraException())->lancarValidacao($msg);
//		}
//	}

    public function excluirUnidade($arrObjUnidadeAPI)
    {

        $mdPetRegrasGeraisRN = new MdPetRegrasGeraisRN();
        $msg = $mdPetRegrasGeraisRN->verificarExistenciaUnidade(array($arrObjUnidadeAPI, 'excluir'));
        if ($msg != '') {
            $objInfraException = new InfraException();
            $objInfraException->lancarValidacao($msg);
        } else {
            return $arrObjUnidadeAPI;
        }
    }

    public function excluirTipoProcesso($arrObjTipoProcessoDTO)
    {

        $mdPetRegrasGeraisRN = new MdPetRegrasGeraisRN();
        $msg = $mdPetRegrasGeraisRN->verificarExistenciaTipoProcesso(array($arrObjTipoProcessoDTO, 'excluir'));

        //verifica se existe um processo sendo utilizado
        if ($msg != '') {
            $objInfraException = new InfraException();
            $objInfraException->lancarValidacao($msg);
        } else {
            return $arrObjTipoProcessoDTO;
        }


        //return parent::desativarTipoProcesso($arrObjTipoProcedimentoDTO); // TODO: Change the autogenerated stub
    }

    public function excluirTipoDocumento($arrObjSerieAPI)
    {

        $mdPetRegrasGeraisRN = new MdPetRegrasGeraisRN();
        $msg = $mdPetRegrasGeraisRN->verificarExistenciaTipoDocumento(array($arrObjSerieAPI, 'excluir'));

        // condição para saber se existe um documento sendo usado
        if ($msg != '') {
            $objInfraException = new InfraException();
            $objInfraException->lancarValidacao($msg);
        } else {
            $msg = $mdPetRegrasGeraisRN->verificarParametroTipoDocumento(array($arrObjSerieAPI, 'excluir'));
            if ($msg != '') {
                $objInfraException = new InfraException();
                $objInfraException->lancarValidacao($msg);
            } else {
                return $arrObjSerieAPI;
            }
        }
    }

    public function excluirDocumento(DocumentoAPI $objDocumentoAPI)
    {
        $mdPetRegrasGeraisRN = new MdPetRegrasGeraisRN();
        $msg = $mdPetRegrasGeraisRN->verificarDocumentoIndisponibilidade(array($objDocumentoAPI, 'excluir'));

        // condição para saber  o documento está sendo utilizado em um indisponibilidade
        if ($msg != '') {
            $objInfraException = new InfraException();
            $objInfraException->lancarValidacao($msg);
        } else {
            return $objDocumentoAPI;
        }
    }

    public function excluirContato($arrObjContatoAPI)
    {
        $msg = (new MdPetRegrasGeraisRN())->verificarExcluirDesativarContato([$arrObjContatoAPI, 'excluir']);
        if ($msg != '') {
            (new InfraException())->lancarValidacao($msg);
        }
    }

    public function desativarContato($arrObjContatoAPI)
    {
        $msg = (new MdPetRegrasGeraisRN())->verificarExcluirDesativarContato([$arrObjContatoAPI, 'desativar']);
		if ($msg != '') {
            (new InfraException())->lancarValidacao($msg);
		}
    }

    public function verificarAcessoProtocolo($arrObjProcedimentoAPI, $arrObjDocumentoAPI)
    {

        $ret = null;
        $objInfraParametro = new InfraParametro(BancoSEI::getInstance());

        $arrObjInfraParametro = $objInfraParametro->listarValores();

        $arrParametros = array(
            'MODULO_PETICIONAMENTO_ID_SERIE_RECIBO_PETICIONAMENTO',
            'MODULO_PETICIONAMENTO_ID_SERIE_CERTIDAO_INTIMACAO_CUMPRIDA',
            'MODULO_PETICIONAMENTO_ID_SERIE_VINC_FORMULARIO',
            'MODULO_PETICIONAMENTO_ID_SERIE_PROCURACAO_ELETRONICA_ESPECIAL',
            'MODULO_PETICIONAMENTO_ID_SERIE_PROCURACAO_REVOGACAO',
            'MODULO_PETICIONAMENTO_ID_SERIE_PROCURACAO_RENUNCIA',
            'MODULO_PETICIONAMENTO_ID_SERIE_VINC_SUSPENSAO',
            'MODULO_PETICIONAMENTO_ID_SERIE_VINC_RESTABELECIMENTO',
            'MODULO_PETICIONAMENTO_ID_SERIE_PROCURACAO_ELETRONICA_SIMPLES',
            'MODULO_PETICIONAMENTO_ID_SERIE_AR'
        );

        $arrDocsLiberados = array();

        foreach ($arrObjInfraParametro as $chave => $objInfra) {
            if (in_array($chave, $arrParametros)) {
                $arrDocsLiberados[] = $objInfra;
            }
        }

        $arrTipoDocumento = array(
            DocumentoRN::$TD_EDITOR_INTERNO,
            DocumentoRN::$TD_FORMULARIO_AUTOMATICO
        );

        foreach ($arrObjDocumentoAPI as $objDocumentoAPI) {
            if (in_array($objDocumentoAPI->getIdSerie(), $arrDocsLiberados) && in_array($objDocumentoAPI->getSubTipo(), $arrTipoDocumento) && $objDocumentoAPI->getNivelAcesso() != ProtocoloRN::$NA_SIGILOSO) {
                $ret[$objDocumentoAPI->getIdDocumento()] = SeiIntegracao::$TAM_PERMITIDO;
            }

            // Forca a liberacao de acesso interno a todas as Unidades, independentemente do Nivel de Acesso dos documentos
            // ou do processo, para os documentos anexados a Peticionamentos do tipo V, A e C, ou seja, peticionamentos sobre
            // Responsavel Legal de Pessoa Juridica

            $objMdPetReciboRN = new MdPetReciboRN();
            $objMdPetReciboDTO = new MdPetReciboDTO;
            $objMdPetReciboDTO->setDblIdDocumento($objDocumentoAPI->getIdDocumento());
            $objMdPetReciboDTO->setNumIdProtocolo($objDocumentoAPI->getIdProcedimento());
            $objMdPetReciboDTO->setStrStaTipoPeticionamento(array('V','A','C'), InfraDTO::$OPER_IN);
            $objMdPetReciboDTO->retNumIdReciboPeticionamento();

            $objRecibo = current($objMdPetReciboRN->listar($objMdPetReciboDTO));

            if ($objRecibo) {
                $objMdPetRelReciboDocumentoAnexoRN = new MdPetRelReciboDocumentoAnexoRN();
                $objMdPetRelReciboDocumentoAnexoDTO = new MdPetRelReciboDocumentoAnexoDTO();
                $objMdPetRelReciboDocumentoAnexoDTO->setNumIdReciboPeticionamento($objRecibo->getNumIdReciboPeticionamento());
                $objMdPetRelReciboDocumentoAnexoDTO->retNumIdDocumento();
                $arrDocumentos = $objMdPetRelReciboDocumentoAnexoRN->listar($objMdPetRelReciboDocumentoAnexoDTO);
                foreach ($arrDocumentos as $documentos) {
                    $ret[$documentos->getNumIdDocumento()] = SeiIntegracao::$TAM_PERMITIDO;
                }
            }
        }

        return $ret;
    }

    public function alterarContato(ContatoAPI $objContatoAPI)
    {

        $isMesmaUnidade = false;

        $objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();
        $objMdPetVincTpProcessoDTO->setNumIdMdPetVincTpProcesso(MdPetVincTpProcessoRN::$ID_FIXO_MD_PET_VINCULO_USU_EXT);
        $objMdPetVincTpProcessoDTO->retNumIdUnidade();
        $objMdPetVincTpProcessoDTO = (new MdPetVincTpProcessoRN())->consultar($objMdPetVincTpProcessoDTO);

        if (!empty($objMdPetVincTpProcessoDTO) && $objMdPetVincTpProcessoDTO->getNumIdUnidade() == SessaoSEI::getInstance()->getNumIdUnidadeAtual()) {
            $isMesmaUnidade = true;
        }

        // Se nao for da mesma unidade realiza as verificacoes para realizar o bloqueio cajo haja vinculos ativos:
        if ($isMesmaUnidade == false) {

            $idContato = $objContatoAPI->getIdContato();
            $msg = $msgVinc = $msgInt = $msgIntVinc = '';

            if($objContatoAPI->getStaNatureza() == 'F'){

                // Pega os Vinculos onde o contato é o Outorgado:
                $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
                $objMdPetVincRepresentantDTO->setNumIdContato($idContato);
                $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
                $objMdPetVincRepresentantDTO->setStrTipoRepresentante([MdPetVincRepresentantRN::$PE_AUTORREPRESENTACAO], InfraDTO::$OPER_NOT_IN);
                $objMdPetVincRepresentantDTO->retTodos();
                $objMdPetVincRepresentantDTO->retNumIdMdPetVinculo();
                $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
                $objMdPetVincRepresentantDTO->retStrTipoRepresentante();
                $objMdPetVincRepresentantDTO->retStrStaEstado();
                $objMdPetVincRepresentantDTO->retStrCpfProcurador();
                $objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
                $objMdPetVincRepresentantDTO->retStrCNPJ();
                $objMdPetVincRepresentantDTO->retStrTpVinc();
                $objMdPetVincRepresentantDTO->retNumIdContatoVinc();
                $objMdPetVincRepresentantDTO->retStrNomeProcurador();
                $objMdPetVincRepresentantDTO->retDblIdProcedimentoVinculo();
                $arrObjVinculosOutorgado = (new MdPetVincRepresentantRN())->listar($objMdPetVincRepresentantDTO);

                // Pega os Vinculos onde o contato é o Outorgante:
                $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
                $objMdPetVincRepresentantDTO->setNumIdContatoOutorg($idContato);
                $objMdPetVincRepresentantDTO->setNumIdContatoVinc($idContato);
                $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
                $objMdPetVincRepresentantDTO->setStrTipoRepresentante([MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL, MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES], InfraDTO::$OPER_IN);
                $objMdPetVincRepresentantDTO->retTodos();
                $objMdPetVincRepresentantDTO->retNumIdMdPetVinculo();
                $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
                $objMdPetVincRepresentantDTO->retStrTipoRepresentante();
                $objMdPetVincRepresentantDTO->retStrStaEstado();
                $objMdPetVincRepresentantDTO->retStrCpfProcurador();
                $objMdPetVincRepresentantDTO->retStrRazaoSocialNomeVinc();
                $objMdPetVincRepresentantDTO->retStrCNPJ();
                $objMdPetVincRepresentantDTO->retStrTpVinc();
                $objMdPetVincRepresentantDTO->retNumIdContatoVinc();
                $objMdPetVincRepresentantDTO->retStrNomeProcurador();
                $objMdPetVincRepresentantDTO->retDblIdProcedimentoVinculo();
                $arrObjVinculosOutorgante = (new MdPetVincRepresentantRN())->listar($objMdPetVincRepresentantDTO);

                if(!empty($arrObjVinculosOutorgante)){
                    foreach($arrObjVinculosOutorgante as $vinculo){
                        $msgVinc .= '- Outorgou '.$vinculo->getStrNomeProcurador().' como '.$vinculo->getStrNomeTipoRepresentante().'\n';
                    }
                }

                if(!empty($arrObjVinculosOutorgado)){
                    foreach($arrObjVinculosOutorgado as $vinculo){
                        $msgVinc .= '- Outorgado por '.$vinculo->getStrRazaoSocialNomeVinc().' como '.$vinculo->getStrNomeTipoRepresentante().'\n';
                    }
                }

            }

            if($objContatoAPI->getStaNatureza() == 'J'){

                $objVinculoDTO = new MdPetVinculoDTO();
                $objVinculoDTO->setNumIdContato($objContatoAPI->getIdContato());
                $objVinculoDTO->retNumIdMdPetVinculo();
                $arrIdMdPetVinculo = InfraArray::converterArrInfraDTO((new MdPetVinculoBD(BancoSEI::getInstance()))->listar($objVinculoDTO),'IdMdPetVinculo');

                if(count($arrIdMdPetVinculo) > 0){

                    $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
                    $objMdPetVincRepresentantDTO->setStrTipoRepresentante([MdPetVincRepresentantRN::$PE_AUTORREPRESENTACAO], InfraDTO::$OPER_NOT_IN);
                    $objMdPetVincRepresentantDTO->setNumIdMdPetVinculo((array) $arrIdMdPetVinculo, InfraDTO::$OPER_IN);
                    $objMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);

                    $objMdPetVincRepresentantDTO->retStrTipoRepresentante();
                    $objMdPetVincRepresentantDTO->retStrCpfProcurador();
                    $objMdPetVincRepresentantDTO->retStrNomeProcurador();
                    $arrObjVinculos = (new MdPetVincRepresentantBD(BancoSEI::getInstance()))->listar($objMdPetVincRepresentantDTO);

                }

                if(!empty($arrObjVinculos)){
                    foreach($arrObjVinculos as $vinculo){
                        $msgVinc .= '- Outorgado por '.$vinculo->getStrNomeProcurador().' como '.$vinculo->getStrNomeTipoRepresentante().'\n';
                    }
                }

            }

            if(!empty($msgVinc)){
                $preMsg = 'Não é permitido alterar Contato que possua registro de Vinculação ou Procuração Eletrônica ativa.\n\n';
                $msg .= ''. $objContatoAPI->getNome() . ' ainda possui as seguintes Vinculações ou Procurações ativas:\n\n'.$msgVinc.'\n';
                if(!empty($msg)){ (new InfraException())->lancarValidacao(substr($preMsg.$msg, 0, -2)); }
            }

        }

    }

    public static function validarXssFormulario($arrPost, $arrayCampos, $objInfraException)
    {
        //Validação Xss
        $retorno = false;
        foreach ($arrPost as $chave => $elemento) {
            try {
                SeiINT::validarXss($elemento);
            } catch (Exception $e) {
                $retorno = true;
                if (strpos($e->__toString(), SeiINT::$MSG_ERRO_XSS) !== false) {
                    $objInfraException->adicionarValidacao('O texto do campo ' . $arrayCampos[$chave] . ' possui conteúdo não permitido.');
                } else {
                    throw $e;
                }
            }
        }
        return $retorno;
    }

}

?>