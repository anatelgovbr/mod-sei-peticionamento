<?php

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
    private $strModoAcionamentoLazyLoadBotoes = 'viewport'; // valores: viewport | manual
    static $arrProtocoloAcessoExterno = array();
    public static $INTIMACAO_NAO_CUMPRIDA = 0;
    public static $INTIMACAO_CUMPRIDA_PARCIAL = 1;
    public static $INTIMACAO_CUMPRIDA = 2;
    public static $INTIMACAO_NEGADA = 3;
    public static $ICONS_VERSION = 19;
    public const MSG_XSS_COMPLEMENTO = 'A operação não foi realizada por envolver conteúdo não permitido por restrição de segurança no texto do campo Complemento do Tipo de Documento. Revise o que está inserindo e tente novamente.';
    public const MSG_XSS_ESPECIFICACAO = 'A operação não foi realizada por envolver conteúdo não permitido por restrição de segurança no texto do campo Especificação. Revise o que está inserindo e tente novamente.';
    public const MSG_XSS_ARQUIVO = "A operação não foi realizada por envolver conteúdo não permitido por restrição de segurança no arquivo '%s'. Revise o que está inserindo e tente novamente.";
    public const MSG_XSS_CAMPO = 'A operação não foi realizada por envolver conteúdo não permitido por restrição de segurança no texto do campo %s. Revise o que está inserindo e tente novamente.';
    public const MSG_CONTEUDO_NAO_PERMITIDO = 'A operação não foi realizada por envolver conteúdo não permitido por restrição de segurança. Revise o que está inserindo e tente novamente.';
    public const MSG_ARQUIVO_INDISPONIVEL = 'Entre o carregamento dos arquivos e a conclusão passou-se muito tempo e o arquivo carregado não está mais disponível para concluir o Peticionamento. Feche essa janela e repita o procedimento do início.';
    public const MSG_INTIMACAO_PENDENTE_CUMPRIMENTO = "Existe intimação eletrônica dentro desse processo com pendência de cumprimento.\nClique para acessar o processo.";
    public const MSG_INTIMACOES_CUMPRIDAS = "Existe intimação eletrônica dentro desse processo e todas já estão cumpridas. Verifique se precisa Responder Intimação.\nClique para acessar o processo.";
    public const MSG_COMPLEMENTO_CARACTERE_NAO_ACEITO = 'O campo "Complemento do Tipo de Documento" contém caracteres que o SEI não aceita. Tente digitar em vez de copiar e colar.';
    public const MSG_COMPLEMENTO_TAMANHO = 'Complemento do Tipo de Documento possui tamanho superior a 50 caracteres.';
    /** Tamanho do Nome na Árvore do documento (documento.nome_arvore), que recebe o complemento. */
    public const TAMANHO_MAXIMO_COMPLEMENTO = 50;

    /**
     * ADR-PET-001: PDF que o Smalot\PdfParser não consegue ler, por estar protegido ou fora do padrão, é aceito sem
     * verificação no upload externo quando true e recusado quando false.
     *
     * @var bool
     */
    private static $bolAceitarPdfNaoLido = true;

    public function __construct()
    {

    }

    public function getNome()
    {
        return 'SEI Peticionamento, Intimação e Procuração';
    }

    public function getVersao()
    {
        return '4.6.11';
    }

    public static function getIaMenorVersaoRequerida()
    {
        return '1.1.0';
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

          case 'md_pet_formulario_selecionar':
            require_once dirname(__FILE__) . '/md_pet_formulario_lista.php';
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
            case 'md_pet_vinc_suspender_autorepresentacao':
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
	
	        case 'md_pet_int_relatorio_fisica':
		        require_once dirname(__FILE__) . '/md_pet_int_relatorio_filtro_pf.php';
		        return true;
	
	        case 'md_pet_int_relatorio_juridica':
		        require_once dirname(__FILE__) . '/md_pet_int_relatorio_filtro_pj.php';
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
	
	        case 'md_pet_formulario_auto_completar':
		        $arrObjFormDTO =  MdPetSerieINT::autoCompletarFormulario($_POST['palavras_pesquisa'], $_POST['tipoDoc']);
		        $xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjFormDTO, 'IdTipoFormulario', 'Nome');
		        break;

            case 'md_pet_tipo_processo_auto_completar':
                $arrObjTipoProcessoDTO = TipoProcedimentoINT::autoCompletarTipoProcedimento($_POST['palavras_pesquisa']);
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
                
                $xml = (!empty($arrContatosDTO)) ? MdPetContatoINT::getContatosNomeAutoCompleteJuridico($arrContatosDTO) : '';
                
                break;

            case 'md_pet_int_usuario_auto_completar_juridica_lote':

                $arrObjContatoDTO = ( new MdPetIntimacaoRN())->filtrarContatosPesquisaIntimacaoJuridicaLote(['txtUsuario' => (string) PaginaSEI::POST('txtUsuario')]);
                $xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjContatoDTO,'IdContato', 'Nome');
                break;

            case 'md_pet_int_usuario_auto_completar':
                $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
                $arrContatosDTO = $objMdPetIntimacaoRN->filtrarContatosPesquisaIntimacao($_POST);

                $xml = (!empty($arrContatosDTO)) ? MdPetContatoINT::getContatosNomeAutoCompletePF($arrContatosDTO) : '';

                break;

            case 'md_pet_int_usuario_auto_completar_lote':
                $arrObjContatoDTO = (new MdPetIntimacaoRN())->filtrarContatosPesquisaIntimacaoLote(['txtUsuario' => (string) PaginaSEI::POST('txtUsuario')]);
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
                $xml = MdPetIntegracaoINT::montarXMLBuscarOperacaoWSDL($_POST['endereco_wsdl'], $_POST['versao']);
                break;
            case 'md_pet_integracao_busca_parametro_wsdl':
                $xml = MdPetIntegracaoINT::montarXMLBuscarOperacaoWSDLParametro($_POST['endereco_wsdl'], $_POST['operacao_wsdl'], $_POST['tipo_parametro'], $_POST['versao']);
                break;

            case 'md_pet_vinc_responsavellegal_cpf_consultar' :
                $cpfNormalizado = $this->normalizarCpfCampo(isset($_POST['nuCpf']) ? $_POST['nuCpf'] : null);
                if ($cpfNormalizado === null) {
                    return '<dados-pf><success>false</success><msg>CPF informado inválido.</msg></dados-pf>';
                }
                $_POST['nuCpf'] = $cpfNormalizado;
                $xml = MdPetIntegracaoRN::consultarContatoCpf($_POST);
                break;

            case 'md_pet_validar_num_sei':
                $xml = MdPetIndisponibilidadeINT::validarNumeroSEI($_POST['numeroSEI']);
                break;

            case 'md_pet_validar_numero_processo_vinculacao':
                $xml = MdPetProRepresWhitINT::validarProcessoRepresentacao($_POST['protocolo']);
                break;

            case 'md_pet_tipo_documento_auto_completar':
                $arrObjSerieDTO = MdPetForcarNivelAcessoDocINT::autoCompletarTipoDocumento(utf8_decode(urldecode($_POST['palavras_pesquisa'])), implode(',', [SerieRN::$TA_EXTERNO, SerieRN::$TA_INTERNO_EXTERNO]));
                $xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjSerieDTO, 'IdSerie', 'Nome');
                break;

            case 'criar_link_assinado_doc_selecionado':
                $idProcedimento = $this->normalizarInteiroPositivo(isset($_POST['idProcedimento']) ? $_POST['idProcedimento'] : null);
                $arrIdsDocumentosSelecionados = $this->normalizarListaInteirosPositivos(isset($_POST['arrIdsDocumentosSelecionados']) ? $_POST['arrIdsDocumentosSelecionados'] : null);

                if ($idProcedimento === null || empty($arrIdsDocumentosSelecionados)) {
                    return '<Documento><Erro>Payload inválido para assinatura de link.</Erro></Documento>';
                }

                $url = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_acesso_externo_protocolos&tipo_selecao=2&tipo_protocolo=disponibilizado&id_object=objLupaProtocolosDisponibilizados&id_procedimento=' . $idProcedimento . '&ids_documento=' . implode(",", $arrIdsDocumentosSelecionados));
                $xml = '<Documento>';
                $xml .= '<Url>' . $url . '</Url>';
                $xml .= '</Documento>';
                break;
        }

        return $xml;
    }


    private function normalizarInteiroPositivo($valor)
    {
        if ($valor === null || $valor === '' || !ctype_digit((string) $valor)) {
            return null;
        }

        $valor = (int) $valor;
        return $valor > 0 ? $valor : null;
    }

    private function normalizarListaInteirosPositivos($valores)
    {
        if (!is_array($valores) || empty($valores)) {
            return [];
        }

        $normalizados = [];

        foreach ($valores as $valor) {
            $inteiro = $this->normalizarInteiroPositivo($valor);
            if ($inteiro === null) {
                return [];
            }
            $normalizados[] = $inteiro;
        }

        return array_values(array_unique($normalizados));
    }

    private function normalizarCpfCampo($valor)
    {
        if ($valor === null) {
            return null;
        }

        $cpf = str_pad(InfraUtil::retirarFormatacao($valor), 11, '0', STR_PAD_LEFT);
        if (!preg_match('/^\d{11}$/', $cpf) || !InfraUtil::validarCpf($cpf)) {
            return null;
        }

        return $cpf;
    }

    private function normalizarCnpjCampo($valor)
    {
        if ($valor === null) {
            return null;
        }

        $cnpj = InfraUtil::prepararCnpj($valor);
        if (!preg_match('/^[0-9A-Z]{12}[0-9]{2}$/', $cnpj) || !InfraUtil::validarCnpj($cnpj)) {
            return null;
        }

        return $cnpj;
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
                $cnpjNormalizado = $this->normalizarCnpjCampo(isset($_POST['txtNumeroCnpj']) ? $_POST['txtNumeroCnpj'] : null);
                if ($cnpjNormalizado === null) {
                    return '<dados-pj><success>false</success><msg>CNPJ informado inválido.</msg></dados-pj>';
                }
                $_POST['txtNumeroCnpj'] = $cnpjNormalizado;
                $objMdPetIntegracaoRN = new MdPetIntegracaoRN();
                $xml = $objMdPetIntegracaoRN->consultarReceitaWsResponsavelLegal($_POST);
                break;
            case 'md_pet_vinc_usu_ext_consulta_vinculo' :
                $cnpjNormalizado = $this->normalizarCnpjCampo(isset($_POST['txtNumeroCnpj']) ? $_POST['txtNumeroCnpj'] : null);
                if ($cnpjNormalizado === null) {
                    return '<dados-pj><success>false</success><msg>CNPJ informado inválido.</msg></dados-pj>';
                }
                $_POST['txtNumeroCnpj'] = $cnpjNormalizado;
                $xml = MdPetVinculoINT::validarExistenciaVinculoCnpj($_POST);
                break;
	        case 'md_pet_vinc_usu_ext_consulta_vinculo_mesmo_cpf' :
		        $cpfNormalizado = $this->normalizarCpfCampo(isset($_POST['cpfUsuarioLogado']) ? $_POST['cpfUsuarioLogado'] : null);
		        $cnpjNormalizado = $this->normalizarCnpjCampo(isset($_POST['cnpjNovaVinculacao']) ? $_POST['cnpjNovaVinculacao'] : null);
		        if ($cpfNormalizado === null || $cnpjNormalizado === null) {
		            return '<dados-pj><success>false</success><msg>CPF/CNPJ informado inválido.</msg></dados-pj>';
		        }
		        $_POST['cpfUsuarioLogado'] = $cpfNormalizado;
		        $_POST['cnpjNovaVinculacao'] = $cnpjNormalizado;
		        $xml = MdPetVinculoINT::validarExistenciaVinculoCnpjOutroUsuarioMesmoCPF($_POST);
		        break;
            case 'md_pet_vinc_usu_ext_dados_usuario' :
                $xml = MdPetVincUsuarioExternoINT::consultarDadosUsuario($_POST);
                break;
            case 'md_pet_vinc_usu_ext_dados_usuario_externo' :
                $xml = MdPetVincUsuarioExternoINT::consultarDadosUsuarioExterno($_POST);
                break;

            case 'md_pet_vinc_usu_ext_dados_usuario_externo_procuracao' :
                $numIdContatoProcuracao = $this->normalizarInteiroPositivo(isset($_POST['hdnIdUsuarioProcuracao']) ? $_POST['hdnIdUsuarioProcuracao'] : null);
                $numIdPessoaJuridica = $this->normalizarInteiroPositivo(isset($_POST['hdnSelPessoaJuridica']) ? $_POST['hdnSelPessoaJuridica'] : null);
                $cpfNormalizado = $this->normalizarCpfCampo(isset($_POST['hdnCpfUsuarioProcuracao']) ? $_POST['hdnCpfUsuarioProcuracao'] : null);

                if ($numIdContatoProcuracao === null || $numIdPessoaJuridica === null || $cpfNormalizado === null) {
                    return '<dados><sucesso>0</sucesso></dados>';
                }

                $xml = MdPetVincUsuarioExternoINT::consultarDadosUsuarioExternoProcuracao(array(
                    'hdnIdUsuarioProcuracao' => $numIdContatoProcuracao,
                    'hdnSelPessoaJuridica' => $numIdPessoaJuridica,
                    'hdnCpfUsuarioProcuracao' => $cpfNormalizado
                ));
                break;
            //Verificação de Existencia de Procuração
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
                $cpfNormalizado = $this->normalizarCpfCampo(isset($_POST['nuCpf']) ? $_POST['nuCpf'] : null);
                if ($cpfNormalizado === null) {
                    return '<dados-pf><success>false</success><msg>CPF informado inválido.</msg></dados-pf>';
                }
                $_POST['nuCpf'] = $cpfNormalizado;
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
	                echo self::processarUploadExterno('fileArquivoEssencial', true);
                }
                die;

            case 'md_pet_usu_ext_upload_doc_principal':

                if (isset($_FILES['fileArquivoPrincipal'])) {
	                LimiteSEI::getInstance()->configurarNivel3();
                    echo self::processarUploadExterno('fileArquivoPrincipal', false, 'complementoPrincipal');
                }
                die;

            case 'md_pet_usu_ext_upload_doc_essencial':

                if (isset($_FILES['fileArquivoEssencial'])) {
	                LimiteSEI::getInstance()->configurarNivel3();
                    echo self::processarUploadExterno('fileArquivoEssencial', false, 'complementoEssencial');
                }
                die;

            case 'md_pet_usu_ext_upload_doc_complementar':

                if (isset($_FILES['fileArquivoComplementar'])) {
	                LimiteSEI::getInstance()->configurarNivel3();
                    echo self::processarUploadExterno('fileArquivoComplementar', false, 'complementoComplementar');
                }
                die;

            case 'md_pet_usu_ext_download':
                require_once dirname(__FILE__) . '/md_pet_usu_ext_cadastro.php';
                return true;

            case 'md_pet_editor_montar':
            case 'md_pet_editor_imagem_upload':
                require_once dirname(__FILE__) . '/md_pet_editor_usuario_externo_processar.php';
                return true;

            case 'md_pet_formulario_gerar':
                require_once dirname(__FILE__) . '/md_pet_formulario.php';
                return true;

            case 'md_pet_validar_documento_principal':

                $conteudo = '';

                if (SessaoSEIExterna::getInstance()->isSetAtributo('docPrincipalConteudoHTML')) {
                    $conteudo = SessaoSEIExterna::getInstance()->getAtributo('docPrincipalConteudoHTML');
                }

                echo !empty($conteudo) ? 'validado' : '';
                return true;

            case 'md_pet_contato_cpf_cnpj':

                $tipoPessoa = $_POST['tipo_pessoa'] == 'pj' ? 'J' : 'F';
                $cpfcnpj = str_replace(['.', '-', '/'], '', $_POST['cpfcnpj']);

                $objContextoContatoDTO = MdPetContatoINT::getTotalContatoByCPFCNPJ($cpfcnpj, $tipoPessoa);

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

            case 'md_pet_processo_validar_numero':
                $xml = MdPetIntercorrenteINT::gerarXMLvalidacaoNumeroProcesso($_POST['txtNumeroProcesso'], false, $_POST['validaProcessoRepresentacao'] ?? false);
                echo $xml;

                return true;

            case 'md_pet_processo_validar_numero_procuracao_simples':
                $xml = MdPetVincRepresentantINT::gerarXMLvalidacaoNumeroProcesso($_POST['txtNumeroProcesso'], $_POST['hdnUsuarioExterno']);
                echo $xml;

                return true;

            case 'md_pet_usu_ext_upload_arquivo':
                if (isset($_FILES['fileArquivo'])) {
	                LimiteSEI::getInstance()->configurarNivel3();
                    echo self::processarUploadExterno('fileArquivo', false, 'txtComplementoTipoDocumento');
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
                    echo self::processarUploadExterno('fileArquivo', true, 'txtComplementoTipoDocumento');
                }
                die;

            case 'md_pet_vinc_usu_ext_upload_anexo':
                if (isset($_FILES['fileArquivo'])) {
	                LimiteSEI::getInstance()->configurarNivel3();
                    echo self::processarUploadExterno('fileArquivo', true, 'txtComplementoTipoDocumento');
                }
                die;

            case 'md_pet_intimacao_usu_ext_confirmar_aceite':
                require_once dirname(__FILE__) . '/md_pet_intimacao_usu_ext_confirmar_aceite.php';
                return true;

            //INTIMACAO Fim

            case 'md_pet_usu_ext_bloqueio_senha_sso':
                require_once dirname(__FILE__) . '/md_pet_usu_ext_bloqueio_senha_sso.php';
                return true;

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
            //Validação Procuração 
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

            case 'md_pet_classificar_ods':
                require_once dirname(__FILE__) . '/md_pet_classificar_ods.php';
                return true;

            case 'md_pet_ods_consultar_objetivos_selecionados_ajax':
                $json = MdIaAdmObjetivoOdsINT::consultarObjetivoSelecionados($_POST);
                InfraAjax::enviarJSON(json_encode($json));
                exit(0);
            case 'md_pet_ods_salvar_metas_selecionadas_sessao_ajax':
                $json = MdIaAdmObjetivoOdsINT::salvarMetasSelecionadasSessao($_POST);
                InfraAjax::enviarJSON(json_encode($json));
                exit(0);
            case 'md_pet_ods_consultar_metas_selecionadas_sessao_ajax':
                echo MdIaAdmObjetivoOdsINT::consultarMetasSelecionadasSessao();
                return true;
            case 'md_pet_ods_consultar_metas_ods_ajax':
                $json = MdIaAdmObjetivoOdsINT::consultarObjetivoParaClassificacaoUsuExt($_POST);
                InfraAjax::enviarJSON(json_encode($json));
                exit();
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
            //recibo mais atual é de resposta a intimação
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
                $objContatoDTO->retStrCnpj();
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

            //Tipos de recibos para pegar a data do "último Peticionamento de Atualização".
            $arrTipoReciboVinculacao = array(
                MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_EMISSAO,
                MdPetReciboRN::$TP_RECIBO_ATUALIZACAO_ATOS_CONSTITUTIVOS,
                MdPetReciboRN::$TP_RECIBO_RESPONSAVEL_LEGAL_ALTERACAO,
                MdPetReciboRN::$TP_RECIBO_RESPONSAVEL_LEGAL_INICIAL,
                MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_REVOGACAO,
                MdPetReciboRN::$TP_RECIBO_PROCURACAO_ELETRONICA_RENUNCIA
            );

            //recibo mais atual é de resposta a intimação
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
                        $objContatoDTO->retStrCnpj();
                        $objContatoDTO->setNumMaxRegistrosRetorno(1);
                        $objContatoRN = new ContatoRN();
                        $arrObjContatoRN = $objContatoRN->consultarRN0324($objContatoDTO);

                        $linhaDeCimaPJ      = '"Controle de Representação de Pessoa Jurídica"';
                        $linhaDeBaixoPJ     = '"' . PaginaSEI::tratarHTML($arrObjContatoRN->getStrNome()) . ' (' . infraUtil::formatarCnpj($arrObjContatoRN->getStrCnpj()) . ')<br/><br/> Último Peticionamento de Atualização: ' . $dataPJ . '"';
                        $linhaDeCimaTxt     = 'Controle de Representação de Pessoa Jurídica\n' . PaginaSEI::tratarHTML($arrObjContatoRN->getStrNome()) . ' (' . infraUtil::formatarCnpj($arrObjContatoRN->getStrCnpj()) . ')';
                        $linhaDeBaixoTxt    = 'Último Peticionamento de Atualização: ' . $dataPJ;
                        $textoSeparado      = $linhaDeCimaTxt . ' \n' . $linhaDeBaixoTxt;

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
        $numRegistrosMenu = count($arrMenusNomes);

        $objMdPetTipoProcessoRN = new MdPetTipoProcessoRN();
        $objMdPetTipoProcessoDTO = new MdPetTipoProcessoDTO();
        $objMdPetTipoProcessoDTO->setStrSinAtivo('S');
        $objMdPetTipoProcessoDTO->retTodos();

        $arrObjMdPetTipoProcessoDTO = $objMdPetTipoProcessoRN->listar($objMdPetTipoProcessoDTO);

        $qtdArrObjMdPetTipoProcessoDTO = (is_array($arrObjMdPetTipoProcessoDTO) ? count($arrObjMdPetTipoProcessoDTO) : 0);
        $objMdPetTipoProcessoDTO = $qtdArrObjMdPetTipoProcessoDTO > 0 ? current($arrObjMdPetTipoProcessoDTO) : null;

        $objMdPetCriterioDTO = new MdPetCriterioDTO();
        $objMdPetCriterioDTO->setStrSinCriterioPadrao('S');
        $objMdPetCriterioDTO->setStrSinAtivo('S');
        $objMdPetCriterioDTO->retTodos();
        $arrObjMdPetCriterioDTO = (new MdPetCriterioRN())->listar($objMdPetCriterioDTO);
        $qtdArrObjMdPetCriterioDTO = (is_array($arrObjMdPetCriterioDTO) ? count($arrObjMdPetCriterioDTO) : 0);
        $objMdPetCriterioDTO = $qtdArrObjMdPetCriterioDTO > 0 ? current($arrObjMdPetCriterioDTO) : null;
	
        // Trazendo dados para mostrar o menu de Processo Novo
	    $objMdPetTpProcessoOrientacoesDTO2 = new MdPetTpProcessoOrientacoesDTO();
	    $objMdPetTpProcessoOrientacoesDTO2->setNumIdTipoProcessoOrientacoesPet(MdPetTpProcessoOrientacoesRN::$ID_FIXO_TP_PROCESSO_ORIENTACOES);
	    $objMdPetTpProcessoOrientacoesDTO2->retTodos();
	    $objMdPetTpProcessoOrientacoesDTO2 = (new MdPetTpProcessoOrientacoesRN())->listar($objMdPetTpProcessoOrientacoesDTO2);

        if (is_array($arrMenusNomes) && $numRegistrosMenu > 0) {

            foreach ($arrMenusNomes as $key => $value) {

                $urlLink = $arrMenusNomes[$key];
                $nomeMenu = $key;

                switch ($nomeMenu) {
                    case 'Peticionamento' :
                        $urlLinkIntercorrente = $urlBase . '/controlador_externo.php?acao=md_pet_intercorrente_usu_ext_cadastrar';
                        if (!is_null($objMdPetCriterioDTO) || (!empty($objMdPetTpProcessoOrientacoesDTO2) && $objMdPetTpProcessoOrientacoesDTO2[0]->getStrSinAtivoMenuExt() == 'S')) {
                            $arrLink[] = '-^#^^' . $nomeMenu . '^';
                        };

	                    if (!empty($objMdPetTpProcessoOrientacoesDTO2) && $objMdPetTpProcessoOrientacoesDTO2[0]->getStrSinAtivoMenuExt() == 'S') {
		                    $arrLink[] = '--^' . $urlLink . '^^' . 'Processo Novo' . '^';
	                    }

                        if (!is_null($objMdPetCriterioDTO)) {
                            $arrLink[] = '--^' . $urlLinkIntercorrente . '^^' . 'Intercorrente' . '^';
                        }
                        break;
                    case 'Pessoas Jurídicas' :
                        if (!is_null($objMdPetVincUsuExtPj) && !is_null($arrIdMdPetIntegFuncionalidUtilizado)) {
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

            $strParam = 'acao=md_pet_intercorrente_usu_ext_cadastrar&id_orgao_acesso_externo='.$_GET['id_orgao_acesso_externo'];
            $hash = md5($strParam . '#' . SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() . '@' . SessaoSEIExterna::getInstance()->getAtributo('RAND_USUARIO_EXTERNO'));

            $urlBase = ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL');

            $link = $urlBase . '/controlador_externo.php?acao=md_pet_intercorrente_usu_ext_cadastrar&id_orgao_acesso_externo='.$_GET['id_orgao_acesso_externo'].'&infra_hash=' . $hash;
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
	 *  Verifica se está na última conclusão
	 * @param $idProtocolo
	 * @return bool
	 */
	public function verificaUltimaConclusao($idProtocolo)
	{
		$objAtividadeDTO = new AtividadeDTO();
		$objAtividadeDTO->setDblIdProtocolo($idProtocolo);
		$objAtividadeDTO->setDthConclusao(null);
		$objAtividadeDTO->retNumIdUnidade();
		$objAtividadeDTO = (new AtividadeRN())->contarRN0035($objAtividadeDTO);
		
		return ($objAtividadeDTO > 0) ? false : true;
	}
	
	/**
	 * @param $idProcedimento
	 * @return mixed
	 * @throws InfraException
	 */
	public function listarDocumentos($idProcedimento)
	{
		
		if (!isset($idProcedimento)) {
			throw new InfraException('Parâmetro $idProcedimento não informado.');
		}
		
		$objDocumentoDTO = new DocumentoDTO();
		$objDocumentoDTO->retDblIdDocumento();
		$objDocumentoDTO->retNumIdSerie();
		$objDocumentoDTO->retDblIdProcedimento();
		$objDocumentoDTO->retStrStaNivelAcessoLocalProtocolo();
		$objDocumentoDTO->setDblIdProcedimento($idProcedimento);
		return (new DocumentoRN())->listarRN0008($objDocumentoDTO);
		
	}
	
	/**
	 * Lista processos anexados ao processo principal
	 * @param $idProcedimento
	 * @return mixed
	 * @throws InfraException
	 */
	public function listarProcessosAnexado($idProcedimento)
	{
		if (!isset($idProcedimento)) {
			throw new InfraException('Parâmetro $idProcedimento não informado.');
		}
		
		$objRelProtocoloProtocoloRN = new RelProtocoloProtocoloRN();
		$objRelProtocoloProtocoloDTO = new RelProtocoloProtocoloDTO();
		$objRelProtocoloProtocoloDTO->retDblIdProtocolo2();
		$objRelProtocoloProtocoloDTO->setDblIdProtocolo1($idProcedimento);
		$objRelProtocoloProtocoloDTO->setStrStaAssociacao(RelProtocoloProtocoloRN::$TA_PROCEDIMENTO_ANEXADO);
		$objRelProtocoloProtocoloDTO = $objRelProtocoloProtocoloRN->listarRN0187($objRelProtocoloProtocoloDTO);
		
		return $objRelProtocoloProtocoloDTO;
	}

    /**
     * Valida se o Documento que está sendo cancelado foi peticionado
     *
     * @access public
     * @param DocumentoAPI $objDocumentoAPI
     * @return mixed
     * @author Jaqueline Mendes <jaqueline.mendes@castgroup.com.br>
     */
	
	
	/**
	 * Valida se o Documento que está sendo cancelado foi peticionado
	 *
	 * @access public
	 * @param DocumentoAPI $objDocumentoAPI
	 * @return mixed
	 */
	public function cancelarDocumento(DocumentoAPI $objDocumentoAPI)
	{
		$numRecibo  = '';
		$idDoc      = $_GET['id_documento'];
		
		// INICIO MIGRACAO PARAMETROS DO UTILIDADES PARA O PETICIONAMENTO
		$paramPet           = 'MODULO_PETICIONAMENTO_ID_TIPO_DOCUMENTO_EXIGIDO_CANCELAR';
		$idProcedimento     = $_GET['id_procedimento'];
		$idSerieDocumento   = (new InfraParametro(BancoSEI::getInstance()))->getValor($paramPet, false);
		$usuarioLogado      = SessaoSEI::getInstance()->getStrSiglaUsuario();
		
		if ((!empty($idSerieDocumento) && is_numeric($idSerieDocumento)) && $usuarioLogado != SessaoSEI::$USUARIO_SEI) {
			$serieRN = new SerieRN();
			$objSerieDTO = new SerieDTO();
			$objSerieDTO->retNumIdSerie();
			$objSerieDTO->retStrNome();
			$objSerieDTO->setStrSinAtivo('S');
			$objSerieDTO->setStrStaAplicabilidade(array(SerieRN::$TA_INTERNO_EXTERNO, SerieRN::$TA_INTERNO), InfraDTO::$OPER_IN);
			$objSerieDTO->setNumIdSerie($idSerieDocumento);
			
			$arrObjSerieDTO = $serieRN->listarRN0646($objSerieDTO);
			
			if ($arrObjSerieDTO) {
				$semTermoCancelamento = true;
				$documentos = $this->listarDocumentos($idProcedimento);
				
				foreach ($documentos as $documento) {
					if ($documento->getNumIdSerie() == $idSerieDocumento) {
						$objPesquisaProtocoloDTO = new PesquisaProtocoloDTO();
						$objPesquisaProtocoloDTO->setStrStaTipo(ProtocoloRN::$TPP_DOCUMENTOS_GERADOS);
						$objPesquisaProtocoloDTO->setStrStaAcesso(ProtocoloRN::$TAP_TODOS);
						$objPesquisaProtocoloDTO->setDblIdProtocolo($documento->getDblIdDocumento());
						
						$objProtocoloRN = new ProtocoloRN();
						$objProtocoloDTO = $objProtocoloRN->pesquisarRN0967($objPesquisaProtocoloDTO);
						
						if ($objProtocoloDTO) {
							if ($objProtocoloDTO[0]->getStrSinAssinado() == 'S') {
								$semTermoCancelamento = false;
							}
						}
					}
					
				}
				
				if ($semTermoCancelamento) {
					$nomeSerie = $arrObjSerieDTO[0]->getStrNome();
					
					$msg = $this->getNome() . ': Não é possível Cancelar o Documento, pois no processo não consta ' . $nomeSerie . ' devidamente formalizado.';
					$objInfraException = new InfraException();
					$objInfraException->adicionarValidacao($msg);
					$objInfraException->lancarValidacoes();
					return null;
				}
			}
		}
		// FIM MIGRACAO PARAMETROS DO UTILIDADES PARA O PETICIONAMENTO
		
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
			
			$msg = $this->getNome() . ': Não é permitido cancelar este documento, pois ele é oriundo de Peticionamento Eletrônico, conforme Recibo Eletrônico de Protocolo SEI nº ' . $numRecibo . '.';
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
			
			$msg = $this->getNome() . ': Não é permitido cancelar este documento, pois ele faz parte de Intimação Eletrônica.';
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

    //não permite mover documento que compoe intimação (doc principal, doc anexo E doc de resposta a intimação incluindo certidões e recibos)
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

            $msg = $this->getNome() . ': Não é permitido mover este documento, pois ele faz parte de Intimação Eletrônica.';
            $objInfraException = new InfraException();
            $objInfraException->adicionarValidacao($msg);
            $objInfraException->lancarValidacoes();
            return null;
        } else {

            return parent::moverDocumento($objDocumentoAPI, $objProcedimentoAPIOrigem, $objProcedimentoAPIDestino);
        }
    }

    /**
     * Intimação Eletrônica na página inicial do usuário externo: um único ícone por processo com intimação, em tons
     * vermelhos quando alguma intimação do processo está pendente de cumprimento (RF-PET-044). As intimações de todos os
     * processos da página vêm de uma única consulta.
     *
     * @param AcessoExternoAPI[] $arrObjAcessoExternoAPI acessos externos listados na página
     * @return array<int, list<string>> HTML do ícone por acesso externo, só dos processos com intimação
     */
    public function montarAcaoControleAcessoExterno($arrObjAcessoExternoAPI): array
    {
        $arrIdProcedimentoAcesso = [];
        foreach ($arrObjAcessoExternoAPI as $objAcessoExternoAPI) {
            $arrIdProcedimentoAcesso[$objAcessoExternoAPI->getIdAcessoExterno()] = $objAcessoExternoAPI->getProcedimento()->getIdProcedimento();
        }

        if ($arrIdProcedimentoAcesso === []) {
            return [];
        }

        $objUsuarioDTO = new UsuarioDTO();
        $objUsuarioDTO->retNumIdContato();
        $objUsuarioDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
        $objUsuarioDTO = (new UsuarioRN())->consultarRN0489($objUsuarioDTO);

        $objMdPetIntRelDestinatarioDTOPesquisa = new MdPetIntRelDestinatarioDTO();
        $objMdPetIntRelDestinatarioDTOPesquisa->setNumIdAcessoExterno(array_keys($arrIdProcedimentoAcesso), InfraDTO::$OPER_IN);
        $objMdPetIntRelDestinatarioDTOPesquisa->setDblIdProcedimento(array_values(array_unique($arrIdProcedimentoAcesso)), InfraDTO::$OPER_IN);
        $objMdPetIntRelDestinatarioDTOPesquisa->setNumIdContatoParticipante($objUsuarioDTO->getNumIdContato());
        $objMdPetIntRelDestinatarioDTOPesquisa->setStrSinPrincipalDoc('S');
        $objMdPetIntRelDestinatarioDTOPesquisa->setProcedimentoDocTIPOFK(InfraDTO::$TIPO_FK_OBRIGATORIA);
        $objMdPetIntRelDestinatarioDTOPesquisa->retNumIdAcessoExterno();
        $objMdPetIntRelDestinatarioDTOPesquisa->retDblIdProcedimento();
        $objMdPetIntRelDestinatarioDTOPesquisa->retStrStaSituacaoIntimacao();

        $arrPendenciaAcesso = [];
        foreach ((new MdPetIntRelDestinatarioRN())->listar($objMdPetIntRelDestinatarioDTOPesquisa) as $objMdPetIntRelDestinatarioDTO) {
            $numIdAcessoExterno = $objMdPetIntRelDestinatarioDTO->getNumIdAcessoExterno();
            if (($arrIdProcedimentoAcesso[$numIdAcessoExterno] ?? null) != $objMdPetIntRelDestinatarioDTO->getDblIdProcedimento()) {
                continue;
            }

            $arrPendenciaAcesso[$numIdAcessoExterno] = ($arrPendenciaAcesso[$numIdAcessoExterno] ?? false)
                || self::isIntimacaoPendenteCumprimento($objMdPetIntRelDestinatarioDTO->getStrStaSituacaoIntimacao());
        }

        $arrIcones = [];
        foreach ($arrPendenciaAcesso as $numIdAcessoExterno => $bolPendenteCumprimento) {
            $arrIcones[$numIdAcessoExterno][] = self::montarIconeIntimacaoControleAcessoExterno($numIdAcessoExterno, $bolPendenteCumprimento);
        }

        return $arrIcones;
    }

    /**
     * Intimação ainda não cumprida: situação Pendente ou sem situação gravada.
     */
    private static function isIntimacaoPendenteCumprimento(?string $strStaSituacaoIntimacao): bool
    {
        return in_array($strStaSituacaoIntimacao, [null, '', '0', MdPetIntimacaoRN::$INTIMACAO_PENDENTE], true);
    }

    /**
     * Ícone da intimação no Controle de Acessos Externos, com o link do processo assinado para o acesso externo, como a
     * linha do core assina o próprio link. Nos esquemas Preto e Cinza Escuro, o ícone fica sobre fundo branco para manter
     * o contraste com a linha escura.
     */
    private static function montarIconeIntimacaoControleAcessoExterno(int $numIdAcessoExterno, bool $bolPendenteCumprimento): string
    {
        SessaoSEIExterna::getInstance()->configurarAcessoExterno($numIdAcessoExterno);
        $strLinkProcedimento = SessaoSEIExterna::getInstance()->assinarLink('processo_acesso_externo_consulta.php?id_acesso_externo=' . $numIdAcessoExterno);
        $strImagem = $bolPendenteCumprimento ? 'intimacao_controle_de_acessos_externos_pendente.svg' : 'intimacao_controle_de_acessos_externos_destaque.svg';
        $strDescricao = $bolPendenteCumprimento ? 'Intimação eletrônica pendente de cumprimento' : 'Intimação eletrônica cumprida';
        $strTexto = $bolPendenteCumprimento ? self::MSG_INTIMACAO_PENDENTE_CUMPRIMENTO : self::MSG_INTIMACOES_CUMPRIDAS;
        $strEstilo = 'width:24px';
        if (in_array(PaginaSEIExterna::getInstance()->getStrEsquemaCores(), [InfraPaginaEsquema3::$ESQUEMA_PRETO, InfraPaginaEsquema3::$ESQUEMA_CINZA_ESCURO], true)) {
            $strEstilo .= ';background-color:#fff;border-radius:4px;padding:2px';
        }

        return ' <a href="javascript:void(0);" onclick="window.open(\'' . $strLinkProcedimento . '\');"><img src="modulos/peticionamento/imagens/svg/'
            . $strImagem . '?' . Icone::VERSAO . '" class="infraImg" style="' . $strEstilo . '" alt="' . $strDescricao . '" '
            . str_replace('\n', '<br/>', PaginaSEI::montarTitleTooltip($strTexto, 'Intimação Eletrônica')) . '/></a>&nbsp;';
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
            $arrBotoes[] = '<a href="' . SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_intimacao_eletronica_listar&id_procedimento=' . $objProcedimentoAPI->getIdProcedimento()) . '" class="botaoSEI" tabindex="' . PaginaSEI::getInstance()->getProxTabBarraComandosSuperior() . '"><img src="modulos/peticionamento/imagens/svg/intimacao_eletronica_ver.svg?'.Icone::VERSAO.'" class="infraCorBarraSistema" alt="Ver Intimação Eletrônica" title="Ver Intimação Eletrônica" width="30"/></a>';
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

        $idAcessoExterno    = $_GET['id_acesso_externo'];
        $idOrgAcessoExterno = $_GET['id_orgao_acesso_externo'];
        $idProcedimento     = $_GET['id_procedimento'];
        $idProcAnex         = $_GET['id_procedimento_anexado'];
        $arrIcones          = array();
        $useLazyToButtons   = true;
        $lazyStartsAt       = 1;

        $id_usuario_externo = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();

        //os botoes so aparecem se houver usuario externo logado (usuario de acesso externo avulso nao visualiza esses botoes)
        if ($id_usuario_externo != null && $id_usuario_externo != "" && $idAcessoExterno) {
            //lista os documentos do SEI
            $arrIdsProtocolo = $objMdPetAcessoExtDocRN->getArrDocumentosAPI(array($idAcessoExterno, $idProcAnex, $isProcedimento));
            
            if (!empty($arrIdsProtocolo)) {
            
                $objMdPetIntProtocoloDTO = new MdPetIntProtocoloDTO();
                $objMdPetIntProtocoloDTO->setDblIdProtocolo($arrIdsProtocolo, InfraDTO::$OPER_IN);
                $objMdPetIntProtocoloDTO->retNumIdMdPetIntimacao();
                $objMdPetIntProtocoloDTO->retDblIdDocumento();
                $objMdPetIntProtocoloDTO->retDblIdProcedimento();
                $objMdPetIntProtocoloDTO->retStrSinPrincipal();
                $objMdPetIntProtocoloDTO->retDblIdProtocolo();
                $arrObjMdPetIntProtocoloDTO = (new MdPetIntProtocoloRN())->listar($objMdPetIntProtocoloDTO);
                $arrIdsProtocoloRenderizados = array();

                foreach ($arrObjMdPetIntProtocoloDTO as $item) {

                    $idProcedimento = !empty($idProcedimento) ? $idProcedimento : $item->getDblIdProcedimento();

                    $id = $item->getDblIdProtocolo();
                    if (in_array($id, $arrIdsProtocoloRenderizados, true)) {
                        continue;
                    }

                    if(count($arrIdsProtocolo) >= $lazyStartsAt && $useLazyToButtons){

                        // Se o numero de documentos do processo passar do setado em $lazyStartsAt ele carrega os placeholders

                        $arrIdsProtocoloRenderizados[] = $id;

                        $arrIcones[$id][] = $this->renderLazyPlaceholder(
                            $id,
                            $idAcessoExterno,
                            $idProcedimento,
                            $isProcedimento
                        );

                        if ($isProcedimento) {
                            $this->arrProcessosLiberados[] = $item->getDblIdProtocolo();
                        } else {
                            $this->arrDocumentosLiberados[] = $item->getDblIdProtocolo();
                        }

                    }else{

                        $conteudoHtml = [];
                        $conteudoHtml[] = $this->montarAcaoBotaoCumprir($item, $idAcessoExterno, $idProcedimento, $isProcedimento);
                        $conteudoHtml[] = $this->montarAcaoBotaoCertidao($item, $idAcessoExterno);
                        $conteudoHtml[] = $this->montarAcaoBotaoResposta($item, $idAcessoExterno, $idProcedimento);
                        $conteudoHtml[] = $this->montarAcaoBotaoRecibo($item, $idAcessoExterno, $idProcedimento);
                        $arrIcones[$item->getDblIdProtocolo()][] = implode('', $conteudoHtml) . '<br/>';

                    }

                }

            }

        }

        return $arrIcones;

    }

    private function renderLazyPlaceholder($id, $idAcesso, $idProcedimento, $isProcedimento)
    {
        static $scriptInjected = true;
        $modoLazyLoad = $this->strModoAcionamentoLazyLoadBotoes === 'manual' ? 'manual' : 'viewport';
        $html = '
        <style>.md-pet-acao-lazy{display:inline-block;background-color:#444;height:20px;margin:0;border-radius:4px;min-width:20px;opacity:.1;animation:1.5s infinite fading}@keyframes fading{0%,100%{opacity:.1}50%{opacity:.25}}.md-pet-acao-trigger{display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;padding:0;border:0;background:transparent;cursor:pointer;font-size:14px;line-height:1;color:#666}</style>';
        if ($modoLazyLoad === 'manual') {
            $html .= '
        <span class="md-pet-acao-lazy-wrapper">
            <button type="button"
                class="md-pet-acao-trigger"
                style="cursor:pointer"
                onclick="mdPetCarregarPlaceholder(\'md-pet-'.$id.'\'); return false;"
                title="Carregar ações disponíveis para o documento"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-plus-square" viewBox="0 0 16 16">
  <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
  <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
</svg></button>
            <div id="md-pet-'.$id.'"
                class="md-pet-acao-lazy"
                data-id="'.$id.'"
                data-acesso="'.$idAcesso.'"
                data-procedimento="'.$idProcedimento.'"
                data-isproc="'.($isProcedimento ? 1 : 0).'"
                data-lazy-mode="'.$modoLazyLoad.'"
                style="display:none;"></div>
        </span>';
        } else {
            $html .= '
        <div id="md-pet-'.$id.'"
            class="md-pet-acao-lazy d-inline-block"
            data-id="'.$id.'"
            data-acesso="'.$idAcesso.'"
            data-procedimento="'.$idProcedimento.'"
            data-isproc="'.($isProcedimento ? 1 : 0).'"
            data-lazy-mode="'.$modoLazyLoad.'"></div>';
        }
        $html .= $this->renderLazyScript($id, $idAcesso, $idProcedimento, $modoLazyLoad);
        return $html;
    }
    private function renderLazyScript($id, $idAcesso, $idProcedimento, $modoLazyLoad = 'viewport')
    {
        $strUrlAcaoLinhas = SessaoSEIExterna::getInstance()->assinarLink('modulos/peticionamento/controlador_ajax_externo.php?acao_ajax_externo=get_acoes_protocolo_lista&acao_origem=' . $_GET['acao'].'&id_acesso_externo='.$idAcesso.'&id_procedimento='.$idProcedimento);
        $modoLazyLoad = $modoLazyLoad === 'manual' ? 'manual' : 'viewport';
        
        return '
        <script>
        (function(){
            if (window.__mdPetLoader) return;
            window.__mdPetLoader = true;
            var mdPetUrl = "'.$strUrlAcaoLinhas.'";
            function limparEspacosColunaAcoes(el) {
                const td = el && el.closest ? el.closest("td.colunaAcoes") : null;

                if (!td) return;

                const walker = document.createTreeWalker(td, NodeFilter.SHOW_TEXT);
                const nodes = [];
                let currentNode = walker.nextNode();

                while (currentNode) {
                    nodes.push(currentNode);
                    currentNode = walker.nextNode();
                }

                nodes.forEach(node => {
                    const texto = (node.textContent || "").replace(/\u00a0/g, "").trim();

                    if (texto === "" && node.parentNode) {
                        node.parentNode.removeChild(node);
                    }
                });
            }
            function removerPlaceholderLazy(el) {
                const wrapper = el && el.closest ? el.closest(".md-pet-acao-lazy-wrapper") : null;

                if (wrapper) {
                    wrapper.remove();
                    return;
                }

                if (el) {
                    el.remove();
                }
            }
            function sinalizarErroLazy(el, trigger, mensagem) {
                const id = el ? (el.dataset.id || el.id.replace("md-pet-", "")) : null;
                const erro = mensagem || "Falha ao montar os botoes de acao.";

                console.error("[md-pet-lazy] Falha ao montar botoes.", { id, erro });

                if (trigger && el && el.dataset.lazyMode === "manual") {
                    trigger.style.display = "inline-flex";
                    trigger.style.color = "#b71c1c";
                    trigger.title = erro;
                    el.style.display = "none";
                    el.classList.remove("md-pet-acao-lazy");
                    return;
                }

                if (!el) return;

                el.classList.remove("md-pet-acao-lazy");
                el.style.display = "inline-flex";
                el.style.alignItems = "center";
                el.style.justifyContent = "center";
                el.style.width = "20px";
                el.style.height = "20px";
                el.style.minWidth = "20px";
                el.style.backgroundColor = "transparent";
                el.style.opacity = "1";
                el.style.border = "2px solid #b71c1c";
                el.style.borderRadius = "50%";
                el.style.align = "center";
                el.style.color = "#b71c1c";
                el.style.fontSize = "12px";
                el.style.fontWeight = "900";
                el.title = erro;
                el.innerHTML = "!";
            }
            function renderBotao(el, html, trigger) {
                if (html.trim() === "") {
                    removerPlaceholderLazy(el);
                    return;
                }
                el.innerHTML = html;
                limparEspacosColunaAcoes(el);
                el.classList.remove("md-pet-acao-lazy");
            }
            function carregarPlaceholder(el) {
                if (!el || el.dataset.loaded) return Promise.resolve();
                const wrapper = el.closest ? el.closest(".md-pet-acao-lazy-wrapper") : null;
                const trigger = wrapper ? wrapper.querySelector(".md-pet-acao-trigger") : null;
                if (trigger && el.dataset.lazyMode === "manual") {
                    trigger.style.display = "none";
                    el.style.display = "inline-block";
                }
                limparEspacosColunaAcoes(el);
                el.dataset.loaded = "1";
                const itens = [{
                    id: el.dataset.id,
                    acesso: el.dataset.acesso,
                    procedimento: el.dataset.procedimento,
                    isproc: el.dataset.isproc
                }];
                return fetch(mdPetUrl, {
                    method: "POST",
                    body: JSON.stringify({ itens })
                })
                .then(r => r.text())
                .then(t => {
                    const data = JSON.parse(t);
                    const id = el.dataset.id || el.id.replace("md-pet-", "");
                    if (data && data.erro) {
                        sinalizarErroLazy(el, trigger, data.erro);
                        return;
                    }
                    if (!Object.prototype.hasOwnProperty.call(data, id)) {
                        sinalizarErroLazy(el, trigger, "A resposta do lazy load não trouxe o item solicitado.");
                        return;
                    }
                    renderBotao(el, atob(data[id] || ""), trigger);
                })
                .catch((error) => {
                    sinalizarErroLazy(el, trigger, error && error.message ? error.message : "Erro inesperado ao carregar as ações.");
                });
            }
            function carregarTodosDesc() {
                const els = Array.from(document.querySelectorAll(".md-pet-acao-lazy:not([data-loaded])"));
                if (!els.length) return;
                els.sort(function(a, b) {
                    return parseInt(b.dataset.id, 10) - parseInt(a.dataset.id, 10);
                });
                els.reduce(function(cadeia, el) {
                    return cadeia.then(function() { return carregarPlaceholder(el); });
                }, Promise.resolve());
            }
            window.mdPetCarregarPlaceholder = function(id) {
                const el = document.getElementById(id);
                if (!el) return;
                carregarPlaceholder(el);
            };
            function carregar() {
                if ("'.$modoLazyLoad.'" === "manual") return;
                carregarTodosDesc();
            }
            if (document.readyState === "loading") {
                document.addEventListener("DOMContentLoaded", carregar);
            } else {
                setTimeout(carregar, 0);
            }
        })();
        </script>
        ';
    }
    public function montarBotoesReais($item, $idAcessoExterno, $idProcedimento, $isProcedimento)
    {
        if (!empty($idAcessoExterno)) {
            SessaoSEIExterna::getInstance()->configurarAcessoExterno($idAcessoExterno);
        }

        $conteudoHtml = [];

        $conteudoHtml[] = $this->montarAcaoBotaoCumprir($item, $idAcessoExterno, $idProcedimento, $isProcedimento);
        $conteudoHtml[] = $this->montarAcaoBotaoCertidao($item, $idAcessoExterno);
        $conteudoHtml[] = $this->montarAcaoBotaoResposta($item, $idAcessoExterno, $idProcedimento);
        $conteudoHtml[] = $this->montarAcaoBotaoRecibo($item, $idAcessoExterno, $idProcedimento);

        return implode('', $conteudoHtml);
    }

    public function montarBotoesReaisPorId($id, $idAcesso, $idProcedimento, $isProcedimento)
    {

        $objDTO = new MdPetIntProtocoloDTO();
        $objDTO->setDblIdProtocolo($id);
        $objDTO->retNumIdMdPetIntimacao();
        $objDTO->retDblIdDocumento();
        $objDTO->retStrSinPrincipal();
        $objDTO->retDblIdProtocolo();
        $objDTO->retDblIdProcedimento();
        $arrItens = (new MdPetIntProtocoloRN())->listar($objDTO);

        if (empty($arrItens)) {
            return "";
        }

        $conteudoHtml = array();

        foreach ($arrItens as $item) {
            $idProcedimentoItem = !empty($idProcedimento) ? $idProcedimento : $item->getDblIdProcedimento();
            $conteudoHtml[] = $this->montarBotoesReais($item, $idAcesso, $idProcedimentoItem, $isProcedimento);
        }

        return implode("", $conteudoHtml);
    }

    public function montarAcaoBotaoRecibo($item, $idAcessoExterno, $idProcedimento)
    {

        $idIntimacao = $item->getNumIdMdPetIntimacao();

        $arrDados       = (new MdPetIntAceiteRN())->existeAceiteIntimacaoAcao(array($idIntimacao, true));

        if (!empty($arrDados)) {

            $recibos                = [];
            $existeInt              = false;

            $idUsuarioExterno       = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();

            $objMdPetIntRN          = new MdPetIntimacaoRN();
            $objMdPetIntRelDestRN   = new MdPetIntRelDestinatarioRN();
            $objMdPetIntReciboRN    = new MdPetIntReciboRN();
            $objMdPetAcessoExtDocRN = new MdPetIntAcessoExternoDocumentoRN();

            $objContato             = (new MdPetIntAceiteRN())->retornaObjContatoIdUsuario(array($idUsuarioExterno));

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
                $objMdPetIntRelDestDTO->retStrCnpjContato();
                $objMdPetIntRelDestDTO->retStrNomeContato();

                $idAcessoExternoValido = $objMdPetAcessoExtDocRN->verificarAcessoExternoValido(array($idIntimacao, $objContato->getNumIdContato(), $idAcessoExterno));
                if (!is_null($idAcessoExternoValido)) {
                    $objMdPetIntRelDestDTO->setNumIdAcessoExterno($idAcessoExterno);
                }

                $objMdPetIntRelDestDTO = $objMdPetIntRelDestRN->listar($objMdPetIntRelDestDTO);

                if (!empty($objMdPetIntRelDestDTO)) {

                    $arrDados   = array($item->getNumIdMdPetIntimacao());
                    $retorno    = $objMdPetIntRN->retornaDadosDocPrincipalIntimacao($arrDados);

                    if (!is_null($retorno)) {
                        $docPrinc   = $retorno[0];
                        $docTipo    = $retorno[1];
                        $docNum     = $retorno[4];
                    }

                    if ($existeInt) {
                        
                        foreach ($objMdPetIntRelDestDTO as $obj) {

                            //Botão
                            $idMdPetDest    = $obj->getNumIdMdPetIntRelDestinatario();
                            $sitIntimacao   = $obj->getStrStaSituacaoIntimacao();

                            if ($sitIntimacao == MdPetIntimacaoRN::$INTIMACAO_RESPONDIDA) {

                                $objMdPetReciboDTO = new MdPetReciboDTO();
                                $objMdPetReciboDTO->retTodos();

                                if ($obj->getStrSinPessoaJuridica() == MdPetIntRelDestinatarioRN::$PESSOA_JURIDICA) {

                                    $mdPetVinculoDTO = new MdPetVinculoDTO();
                                    $mdPetVinculoDTO->setNumIdContato($obj->getNumIdContato());
                                    $mdPetVinculoDTO->retNumIdContatoRepresentante();
                                    $mdPetVinculoDTO->retNumIdUsuario();
                                    $mdPetVinculoDTO->retStrStaEstado();
                                    $mdPetVinculoDTO->retDthDataEncerramento();
                                    $arrMdPetVinculoDTO = (new MdPetVinculoRN())->listar($mdPetVinculoDTO);

                                    $arrRepresentantes = InfraArray::converterArrInfraDTO($arrMdPetVinculoDTO, 'IdUsuario');

                                } else {

                                    $arrRepresentantes = array($idUsuarioExterno);

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

                                foreach ($arrObjMdPetReciboDTO as $objMdPetReciboDTO) {

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

                                }

                                //necessario fazer  isso para nao quebrar a navegação (se nao fizer isso e tem clicar em qualquer outro link do usuario externo, quebra a sessao e usuario é enviado de volta para a tela de login externo (trata-se de funcionamento incorporado ao Core do SEI)
                                SessaoSEIExterna::getInstance()->configurarAcessoExterno($idAcessoExterno);
                            }
                        }
                    }
                }
            }
        }

        return $conteudoHtml;
    }

    public function montarAcaoBotaoCumprir($item, $idAcessoExterno, $idProcedimento, $isProcedimento)
    {
        $htmlImgIntCumpridaPrinc = '<img src="modulos/peticionamento/imagens/svg/intimacao_cumprida_doc_principal.svg?'.self::$ICONS_VERSION.'" style="width: 24px;">';
        $htmlImgIntNaoCumpPrinc = '<img src="modulos/peticionamento/imagens/svg/intimacao_nao_cumprida_doc_principal.svg?'.self::$ICONS_VERSION.'" style="width: 24px;">';
        $htmlImgIntCumpridaAnex = '<img src="modulos/peticionamento/imagens/svg/intimacao_cumprida_doc_anexo.svg?'.self::$ICONS_VERSION.'" style="width: 24px;">';
        $htmlImgIntNaoCumprAnex = '<img src="modulos/peticionamento/imagens/svg/intimacao_nao_cumprida_doc_anexo.svg?'.self::$ICONS_VERSION.'" style="width: 24px;">';
        $htmlImgIntAguardandoCumprGeralPrinc = '<img src="modulos/peticionamento/imagens/svg/intimacao_aguardando_cumprimento_geral.svg?'.self::$ICONS_VERSION.'" style="width: 24px;">';
        $htmlImgIntAguardandoCumprGeralAnex = '<img src="modulos/peticionamento/imagens/svg/intimacao_aguardando_cumprimento_geral_anexo.svg?'.self::$ICONS_VERSION.'" style="width: 24px;">';
        $htmlImgIntNaoCumprVinculoInativoPrinc = '<img src="modulos/peticionamento/imagens/svg/intimacao_nao_cumprida_vinculo_inativo.svg?'.self::$ICONS_VERSION.'" style="width: 24px;">';
        $htmlImgIntNaoCumprVinculoInativoAnex = '<img src="modulos/peticionamento/imagens/svg/intimacao_nao_cumprida_vinculo_inativo_anexo.svg?'.self::$ICONS_VERSION.'" style="width: 24px;">';
        
        $objContato = (new MdPetIntAceiteRN())->retornaObjContatoIdUsuario(array(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()));
        
        $arrPessoaJuridica = $arrPessoaFisica = [];
        $vinculoRepresentanteInativo = false;

        if (!empty($item)) {

            $idIntimacaoBtnlink = [];

            $img = '';
            $js = '';
            $strLink = '';
            $existeInt = false;
            $idIntimacao = $item ? $item->getNumIdMdPetIntimacao() : null;
            $idDocumento = $item->getDblIdDocumento();
            
            $arrAceites = null;
            $idAceite = null;
            $dataAceite = null;
            $idDestinatarioAceite = null;

            $arrAceites = (new MdPetIntAceiteRN())->existeAceiteIntimacaoAcao(array($idIntimacao, true));

            if ($arrAceites) {
                
                foreach ($arrAceites as $aceite) {

                    $idAceite[]             = $aceite['ID_ACEITE'];
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

            $arrObjDestinatariosIntimacoes = (new MdPetIntRelDestinatarioRN())->retornarDestinatariosIntimacao($idIntimacao, $objContato->getNumIdContato(), $idAcessoExterno);
            
            $idIntimacaoBtnlink = array_merge($idIntimacaoBtnlink, array_diff(array_unique(InfraArray::converterArrInfraDTO($arrObjDestinatariosIntimacoes, 'IdMdPetIntimacao')), $idIntimacaoBtnlink));

            $arrObjDestinatariosIntimacoesCopia = $arrObjDestinatariosIntimacoes;
            $arrObjDestinatarios = array();
            $arrObjDestinatariosUnicosIntimacao = array();
            $arrObjDestinatariosUnicosIntimacaoComProcuracao = array();
            $qtdDestinatariosIntimacao = 0;

            foreach ($arrObjDestinatariosIntimacoesCopia as $chave => $itemObjMdPetIntRelDestDTOTratado) {

                $arrProcuracoesAtivasDestinatario = (new MdPetVincRepresentantRN())->retornarProcuradoresComPoderCumprirResponder($itemObjMdPetIntRelDestDTOTratado->getNumIdContato(), $item->getDblIdProtocolo(), $objContato->getNumIdContato());
                
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
            $procNegado = true;

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

                    $isValido   = (new MdPetIntCertidaoRN())->verificaDocumentoEAnexoIntimacaoNaoCumprida(array($item->getDblIdProtocolo(), $idAcessoExterno));
                    $strLink    = (new MdPetIntCertidaoRN())->retornaLinkAcessoDocumento($item->getDblIdProtocolo(), $idAcessoExterno, $isProcedimento);

                    $initMsg    = $isProcedimento ? 'Processo' : 'Documento';
                    $alertMsg   = $initMsg . ' bloqueado, pois está vinculado a uma Intimação ainda não Cumprida.';
                    $js         = $isValido ? 'window.open(\'' . $strLink . '\');' : 'alert(\'' . $alertMsg . '\')';

                    if ($item->getStrSinPrincipal() == 'S') {

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

                    $strLink = SessaoSEIExterna::getInstance()->assinarLink($urlBase . '/controlador_externo.php?acao=md_pet_intimacao_usu_ext_confirmar_aceite&id_acesso_externo=' . $idAcessoExterno . '&id_documento=' . $item->getDblIdProtocolo() . $linkIdIntimacao);
                    $js = "infraAbrirJanelaModal('" . $strLink . "', 900, 470);";

                    $informeTooltipACG = null;
                    
                    if ($item->getStrSinPrincipal() == 'S') {

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
                    $img = ($item->getStrSinPrincipal() == 'S') ? $htmlImgIntNaoCumprVinculoInativoPrinc : $htmlImgIntNaoCumprVinculoInativoAnex;
                }

                // Start Gabriel 20.06.2022

                $retorno = (new MdPetIntimacaoRN())->retornaDadosDocPrincipalIntimacao((array) $item->getNumIdMdPetIntimacao());

                if (!is_null($retorno)) {
                    $docPrinc   = $retorno[0];
                    $docTipo    = $retorno[1];
                    $docNum     = $retorno[4];
                }

                // Verifico a situação do usuario perante os destinatarios da intimacao:
                $situacao = (new MdPetIntRelDestinatarioRN())->getSituacaoUsuarioIntimacao($item->getDblIdProtocolo(), $idAcessoExterno);

                $urlBase = ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL');
                $informeTooltipACG = null;

                // Verificando a situação e determinando botões e as ações dos links

                $vinculoRepresentanteInativo = true;
                $img = $item->getStrSinPrincipal() == 'S' ? $htmlImgIntNaoCumprVinculoInativoPrinc : $htmlImgIntNaoCumprVinculoInativoAnex;

                if(in_array($situacao['btn_cumprir'], ['nao_cumprida'])){
                    
                    $vinculoRepresentanteInativo = false;
                    $img = $item->getStrSinPrincipal() == 'S' ? $htmlImgIntNaoCumpPrinc : $htmlImgIntNaoCumprAnex;
                    $idsIntsCumprir = '&id_intimacao[]=' . implode('&id_intimacao[]=', array_unique($situacao['int_cumprir']));
                    $strLink = SessaoSEIExterna::getInstance()->assinarLink($urlBase . '/controlador_externo.php?acao=md_pet_intimacao_usu_ext_confirmar_aceite&id_acesso_externo=' . $idAcessoExterno . '&id_documento=' . $item->getDblIdProtocolo() . $idsIntsCumprir);
                    $js = "infraAbrirJanelaModal('" . $strLink . "', 900, 470);";
                
                }else if(in_array($situacao['btn_cumprir'], ['cumprida_parcial'])){
                    
                    $vinculoRepresentanteInativo = false;
                    $img = $item->getStrSinPrincipal() == 'S' ? $htmlImgIntAguardandoCumprGeralPrinc : $htmlImgIntAguardandoCumprGeralAnex;
                    $idsIntsCumprir = '&id_intimacao[]=' . implode('&id_intimacao[]=', array_unique($situacao['int_cumprir']));
                    $informeTooltipACG = 'Observe que esta Intimação possui destaque, pois envolve pelo menos um Destinatário que você representa em comum e outro Representante já cumpriu a Intimação. Ao consultar esta Intimação você estará cumprindo-a para os Destinatários ainda pendentes.';
                    $strLink = SessaoSEIExterna::getInstance()->assinarLink($urlBase . '/controlador_externo.php?acao=md_pet_intimacao_usu_ext_confirmar_aceite&id_acesso_externo=' . $idAcessoExterno . '&id_documento=' . $item->getDblIdProtocolo() . $idsIntsCumprir);
                    $js = "infraAbrirJanelaModal('" . $strLink . "', 900, 470);";

                }else if(in_array($situacao['btn_cumprir'], ['cumprida_geral'])){
                        
                    $vinculoRepresentanteInativo = false;
                    $strLink = SessaoSEIExterna::getInstance()->assinarLink($urlBase . '/documento_consulta_externa.php?id_acesso_externo=' . $idAcessoExterno . '&id_documento=' . $item->getDblIdProtocolo());
                    $js = 'window.open(\'' . $strLink . '\')';
                    $img = $item->getStrSinPrincipal() == 'S' ? $htmlImgIntCumpridaPrinc : $htmlImgIntCumpridaAnex;
                
                }

                // End Gabriel 20.06.2022

                if (is_array($arrObjDestinatariosIntimacoesCopia)) {

                    $objMdPetIntRelDestDTO2 = $arrObjDestinatariosIntimacoesCopia;
                    $dtIntimacao = '';

                    foreach ($objMdPetIntRelDestDTO2 as $obj) {

                        if ($obj->getStrSinPessoaJuridica() == 'S') {
                            if (!key_exists(InfraUtil::formatarCpfCnpj($obj->getStrCnpjContato()), $arrPessoaJuridica)) {
                                $arrPessoaJuridica[InfraUtil::formatarCpfCnpj($obj->getStrCnpjContato())] = $obj->getStrNomeContato() . ' (' . InfraUtil::formatarCpfCnpj($obj->getStrCnpjContato()) . ')';
                            }
                        } else {
                            if (!key_exists(InfraUtil::formatarCpfCnpj($obj->getDblCpfContato()), $arrPessoaFisica)) {
                                if(!empty($obj->getDblCpfContato())){
                                    $arrPessoaFisica[InfraUtil::formatarCpfCnpj($obj->getDblCpfContato())] = $obj->getStrNomeContato() . ' (' . InfraUtil::formatarCpfCnpj($obj->getDblCpfContato()) . ')';
                                }else{
                                    $arrPessoaFisica[InfraUtil::formatarCpfCnpj($obj->getDblCpfContato())] = $obj->getStrNomeContato() . ' (Usuário não possui CPF)';
                                }
                                
                            }
                        }

                        $objMdPetIntAceiteDTO = new MdPetIntAceiteDTO();
                        $objMdPetIntAceiteDTO->setNumIdMdPetIntRelDestinatario($obj->getNumIdMdPetIntRelDestinatario());
                        $objMdPetIntAceiteDTO->setOrdDthData(InfraDTO::$TIPO_ORDENACAO_ASC);
                        $objMdPetIntAceiteDTO->setNumMaxRegistrosRetorno(1);
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

                    $tooltip = (new MdPetIntimacaoRN())->getTextoTolTipIntimacaoEletronicaCumprida(array($dataAceite, $docPrinc, $docTipo, $docNum, $item->getStrSinPrincipal(), $arrPessoaJuridica, $arrPessoaFisica, $informeTooltipACG));
                
                } elseif ($vinculoRepresentanteInativo) {

                    $idContatoDestinatario = InfraArray::converterArrInfraDTO($arrObjDestinatariosIntimacoesCopia, 'IdContato');
                    
                    if (is_iterable($idContatoDestinatario)) {
                        foreach ($idContatoDestinatario as $id) {
                            $linkIdDestinatario .= '&id_contato[]=' . $id;
                        }
                    }

                    $tooltip = (new MdPetIntimacaoRN())->getTextoTolTipIntimacaoEletronicaVinculoInativo($dtIntimacao, $docPrinc, $docTipo, $docNum, $item->getStrSinPrincipal(), $objContato->getNumIdContato(), $idContatoDestinatario, $arrPessoaJuridica, $arrPessoaFisica);
                    
                    $urlBase = ConfiguracaoSEI::getInstance()->getValor('SEI', 'URL');
                    $strLink = SessaoSEIExterna::getInstance()->assinarLink($urlBase . '/controlador_externo.php?acao=md_pet_intimacao_usu_ext_negar_cumprir&id_documento=' . $idDocumento . '&id_acesso_externo=' . $idAcessoExterno . $linkIdDestinatario . '&id_destinatario=' . $objContato->getNumIdContato());

                    $js = "infraAbrirJanelaModal('" . $strLink . "', 700, 250);";
                    
                } else {

                    $tooltip = (new MdPetIntimacaoRN())->getTextoTolTipIntimacaoEletronica(array($dtIntimacao, $docPrinc, $docTipo, $docNum, $item->getStrSinPrincipal(), $arrPessoaJuridica, $arrPessoaFisica, $informeTooltipACG));
                
                }

                $arr = array();
                $arr[0] = $js;
                $arr[1] = $img;
                $arr[2] = count($tooltip) > 0 ? $tooltip[0] : '';
                $arr[3] = count($tooltip) > 0 ? $tooltip[1] : '';

                $conteudoHtml = (new MdPetIntimacaoRN())->retornaLinkCompletoIconeIntimacao($arr);

            }

            return $conteudoHtml;

        }

    }

    public function montarAcaoBotaoCertidao($item, $idAcessoExterno)
    {

        $arrExibidos = [];
        $idUsuarioExterno = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();
        $conteudoHtml = '';

        if (!empty($item)) {

            $objMdPetAcessoExtDocRN = new MdPetIntAcessoExternoDocumentoRN();
            $objMdPetIntRelDestRN = new MdPetIntRelDestinatarioRN();
            $objMdPetCertidaoRN = new MdPetIntCertidaoRN();
            $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
            $objMdPetIntRN = new MdPetIntimacaoRN();

            $existeInt      = false;

            $idIntimacao    = $item ? $item->getNumIdMdPetIntimacao() : null;

            $objContato     = $objMdPetIntAceiteRN->retornaObjContatoIdUsuario(array($idUsuarioExterno));
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
                    $objMdPetIntRelDestDTO->retStrCnpjContato();
                    $objMdPetIntRelDestDTO->retDblCpfContato();
                    $objMdPetIntRelDestDTO->retStrNomeContato();

                    $idAcessoExternoValido = $objMdPetAcessoExtDocRN->verificarAcessoExternoValido(array($idIntimacao, $objContato->getNumIdContato(), $idAcessoExterno));

                    if (!is_null($idAcessoExternoValido)) {
                        $objMdPetIntRelDestDTO->setNumIdAcessoExterno($idAcessoExterno);
                    }

                    $objMdPetIntRelDestDTO = $objMdPetIntRelDestRN->listar($objMdPetIntRelDestDTO);

                    if (is_array($objMdPetIntRelDestDTO) && count($objMdPetIntRelDestDTO) > 0) {

                        $objMdPetIntRelDestDTO2 = $objMdPetIntRelDestDTO;

                        foreach ($objMdPetIntRelDestDTO2 as $obj) {
                            
                            $arrDestinatarios = array();
                            $retorno = $objMdPetIntRN->retornaDadosDocPrincipalIntimacao((array)$idIntimacao);
                            if (!is_null($retorno)) {
                                $docPrinc = $retorno[0];
                            }

                            $cpfCnpj = $obj->getStrSinPessoaJuridica() == 'S' ? $obj->getStrCnpjContato() : $obj->getDblCpfContato();

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
        return $conteudoHtml;
    }

    public function montarAcaoBotaoResposta($item, $idAcessoExterno, $idProcedimento)
    {

        $objMdPetIntAceiteRN = new MdPetIntAceiteRN();
        $objContato = $objMdPetIntAceiteRN->retornaObjContatoIdUsuario(array(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno()));

        $objMdPetRespostaRN = new MdPetIntRespostaRN();

        $existeInt = false;
        $conteudoHtml = '';
        $idIntimacao = null;
        $idAceite = null;
        $idMdPetDest = array();
        $objMdPetIntRelDestDTO = array();
        $arrPessoaJuridica = array();
        $arrPessoaFisica = array();

        if ($item) {

            $arrRevogado = [];

            $idIntimacao = $item->getNumIdMdPetIntimacao();
            $arrDados = null;

            $arrDados = $objMdPetIntAceiteRN->existeAceiteIntimacaoAcao(array($idIntimacao, true));
            if ($arrDados) {
                foreach ($arrDados as $aceite) {
                    $existeInt = $aceite['INT'];
                    $idAceite[] = $aceite['ID_ACEITE'];
                }
            }

            $objMdPetIntRelDestDTO = (new MdPetIntRelDestinatarioRN())->retornarDestinatariosIntimacao($idIntimacao, $objContato->getNumIdContato(), $idAcessoExterno);

            if (is_array($objMdPetIntRelDestDTO) && count($objMdPetIntRelDestDTO) > 0) {

                $objMdPetIntRelDestDTO2 = $objMdPetIntRelDestDTO;

                $dtIntimacao = '';

                $qntDest = is_array($objMdPetIntRelDestDTO2) ? count($objMdPetIntRelDestDTO2) : 0;
                $qntDestRevogado = 0;

                foreach ($objMdPetIntRelDestDTO2 as $obj) {

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

                    //Verifica se a situação da vinculação/intimação é diferente de ativa
                    if ((!$procuracaoSimplesValida || $contarobjMdPetVincRepresentantDTO == 0) && $objContato->getNumIdContato() != $obj->getNumIdContato()) {
                        $qntDestRevogado++;
                    }

                    if ($obj->getStrSinPessoaJuridica() == 'S') {
                        if (!key_exists($obj->getStrCnpjContato(), $arrPessoaJuridica)) {
                            $arrPessoaJuridica[$obj->getStrCnpjContato()] = $obj->getStrNomeContato() . ' (' . InfraUtil::formatarCpfCnpj($obj->getStrCnpjContato()) . ')';
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

                //se a quantidade de intimações for igual a quantidade de vínculos diferente de ativo é exibido o ícone de vínculo inativo
                // if ($qntDest == $qntDestRevogado) {
                //     $vinculoRepresentanteInativo = true;
                //     $img = $htmlImgIntNaoCumprVinculoInativoPrinc;
                // }
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
            
        } // EndIf

        $idContatoDestinatario = array_unique(InfraArray::converterArrInfraDTO($objMdPetIntRelDestDTO, 'IdContato'));

        if ($existeInt) {

            // Gabriel em 20.06.2022

            $situacao = (new MdPetIntRelDestinatarioRN())->getSituacaoUsuarioIntimacao($item->getDblIdProtocolo(), $idAcessoExterno);

            // A situação retornada é agregada por protocolo. Como atualmente é emitida uma intimação por
            // destinatário, o botão precisa ser montado a partir da situação da intimação da iteração atual,
            // caso contrário apenas a primeira intimação do documento geraria botão.
            $situacaoIntimacao = (!is_null($situacao) && isset($situacao['por_intimacao'][$idIntimacao])) ? $situacao['por_intimacao'][$idIntimacao] : null;

            if(!is_null($situacaoIntimacao) && in_array($situacaoIntimacao['btn_responder'], ['cumprida_geral', 'cumprida_parcial'])){

                $arrPrazoResposta = (new MdPetIntPrazoRN())->retornarTipoRespostaValido([$idIntimacao, $idMdPetDest]);

                if(is_array($arrPrazoResposta) && count($arrPrazoResposta) > 0){
                    $dtPrazoResposta = !is_null($arrPrazoResposta[0]->getDthDataProrrogada()) ? $arrPrazoResposta[0]->getDthDataProrrogada() : $arrPrazoResposta[0]->getDthDataLimite();
                    if(!empty($dtPrazoResposta) && InfraData::compararDatas(date('d/m/Y'), $dtPrazoResposta) >= 0){
                        $conteudoHtml .= $objMdPetRespostaRN->addIconeRespostaAcao(array(array($idIntimacao), $idAcessoExterno, $idProcedimento, $idAceite, $idMdPetDest, $arrPessoaJuridica, $arrPessoaFisica));
                    }
                }

            }else if(!is_null($situacaoIntimacao) && in_array($situacaoIntimacao['btn_responder'], ['com_impedimento'])){
                $conteudoHtml .= $objMdPetRespostaRN->addIconeRespostaNegada(array(array($idIntimacao), $idAcessoExterno, $idProcedimento, $idAceite, $idMdPetDest, $objContato->getNumIdContato(), $idContatoDestinatario, $arrPessoaJuridica, $arrPessoaFisica, $item->getDblIdProtocolo()));
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

        $idAcessoExterno = isset($_GET['id_acesso_externo']) ? $_GET['id_acesso_externo'] : null;

        //so verifica o acesso se houver usuario externo logado (usuario de acesso externo avulso nao verifica nada)
        if ($id_usuario_externo == null || $id_usuario_externo == "" || is_null($idAcessoExterno)) {
            //retorna array vazio para nao interferir na verificacao de nenhum protocolo/documento
            return array();
        } else {

            $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
            $objMdPetIntAcExtRN = new MdPetIntAcessoExternoDocumentoRN();
            $objMdPetAceiteRN = new MdPetIntAceiteRN();
            $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
            $objMdPetIntRelDestinatarioRN = new MdPetIntRelDestinatarioRN();

            //ids de todos os docs do processo
            $idProtocoloDoProcesso = array();
            $arrDocumentoPorId = array();
            $arrDocumentoPesquisaPublicaPorId = array();
            $arrProcedimentoPorId = array();

            foreach ($arrObjDocumentoAPI as $objDocumentoAPI) {
                $idDocumento = $objDocumentoAPI->getIdDocumento();
                $idProtocoloDoProcesso[$idDocumento] = $idDocumento;
                $arrDocumentoPorId[$idDocumento] = $objDocumentoAPI;
                $arrDocumentoPesquisaPublicaPorId[$idDocumento] = $objDocumentoAPI;
            }

            //add ids dos processos anexados pois podem ser anexados no mesmo nível do documento e adicionado como anexo da Intimação

            foreach ($arrObjProcedimentoAPI as $objProcedimentoAPI) {
                $idProcedimento = $objProcedimentoAPI->getIdProcedimento();
                $idProtocoloDoProcesso[$idProcedimento] = $idProcedimento;
                $arrProcedimentoPorId[$idProcedimento] = $objProcedimentoAPI;
            }

            $idProtocoloDoProcesso = array_values($idProtocoloDoProcesso);

            //se o processo nao tiver documentos ja pode passar direto a verificação
            if (count($idProtocoloDoProcesso) == 0) {
                return array();
            }

            /*
             * saber exatamente quais docs estao envolvidos com a intimação
             * (e com a resposta a intimação? por enquanto ignorar esses):
             * aqueles que nao estiverem, nao adicionar no array de retorno
             * para que o controle de acesso a eles siga para a lógica
             * padrão aplicada pelo Core do SEI
             * @todo precisa ser refatorado o código para ser mais performático, pode ocorrer despecho de memoria
             * */
            //Get ids Intimação Contato
            $arrIntimacoesContato = $objMdPetIntimacaoRN->getIntimacoesPorContato($idProtocoloDoProcesso);
            $ret = array();
            $arrModulos = ConfiguracaoSEI::getInstance()->getValor('SEI','Modulos');
            $isPesquisaPublicaHabilitada = is_array($arrModulos) && array_key_exists('PesquisaIntegracao', $arrModulos) && self::verificaSeModPesquisaPublicaVersaoMinima();

            if ($arrIntimacoesContato) {
                $arrIntimacoesAnexoPorProtocolo = array();
                $arrIntimacoesDisponivelPorProtocolo = array();

                $objIntimacaoDocDTO = new MdPetIntProtocoloDTO();
                $objIntimacaoDocDTO->setDblIdProtocolo($idProtocoloDoProcesso, InfraDTO::$OPER_IN);
                $objIntimacaoDocDTO->setNumIdMdPetIntimacao($arrIntimacoesContato, InfraDTO::$OPER_IN);
                $objIntimacaoDocDTO->retDblIdProtocolo();
                $objIntimacaoDocDTO->retNumIdMdPetIntimacao();
                $arrObjIntimacaoDocDTO = (new MdPetIntProtocoloRN())->listar($objIntimacaoDocDTO);

                $objIntimacaoDocDisponivelDTO = new MdPetIntProtDisponivelDTO();
                $objIntimacaoDocDisponivelDTO->retDblIdProtocolo();
                $objIntimacaoDocDisponivelDTO->retNumIdMdPetIntimacao();
                $objIntimacaoDocDisponivelDTO->setDblIdProtocolo($idProtocoloDoProcesso, InfraDTO::$OPER_IN);
                $objIntimacaoDocDisponivelDTO->setNumIdMdPetIntimacao($arrIntimacoesContato, InfraDTO::$OPER_IN);
                $arrIntimacaoDocDisponivelDTO = (new MdPetIntProtDisponivelRN())->listar($objIntimacaoDocDisponivelDTO);

                foreach ($arrObjIntimacaoDocDTO as $docIntimacaoDTO) {
                    $idProtocolo = $docIntimacaoDTO->getDblIdProtocolo();
                    $idIntimacao = $docIntimacaoDTO->getNumIdMdPetIntimacao();
                    if (!isset($arrIntimacoesAnexoPorProtocolo[$idProtocolo])) {
                        $arrIntimacoesAnexoPorProtocolo[$idProtocolo] = array();
                    }
                    $arrIntimacoesAnexoPorProtocolo[$idProtocolo][$idIntimacao] = $idIntimacao;
                }

                foreach ($arrIntimacaoDocDisponivelDTO as $docIntimacaoDisponivelDTO) {
                    $idProtocolo = $docIntimacaoDisponivelDTO->getDblIdProtocolo();
                    $idIntimacao = $docIntimacaoDisponivelDTO->getNumIdMdPetIntimacao();
                    if (!isset($arrIntimacoesDisponivelPorProtocolo[$idProtocolo])) {
                        $arrIntimacoesDisponivelPorProtocolo[$idProtocolo] = array();
                    }
                    $arrIntimacoesDisponivelPorProtocolo[$idProtocolo][$idIntimacao] = $idIntimacao;
                }

                $idDocumentosEnvolvidosNaIntimacao = array_unique(array_merge(array_keys($arrIntimacoesAnexoPorProtocolo), array_keys($arrIntimacoesDisponivelPorProtocolo)));
                $idSerieCertidao = $objInfraParametro->getValor(MdPetIntCertidaoRN::$STR_ID_SERIE_CERTIDAO);

                $objMdPetIntRelDestExternoDTO = new MdPetRelIntDestExternoDTO();
                $objMdPetIntRelDestExternoDTO->setNumIdAcessoExterno($idAcessoExterno);
                $objMdPetIntRelDestExternoDTO->retNumIdMdPetIntRelDestinatario();
                $qtdMdPetRelIntDestExterno = (new MdPetRelIntDestExternoRN())->contar($objMdPetIntRelDestExternoDTO);

                $isIntAcessoExt = null;
                $arrAceitePorIntimacoes = array();

                foreach ($idDocumentosEnvolvidosNaIntimacao as $idProtocoloDaIntimacao) {

                    if (array_key_exists($idProtocoloDaIntimacao, $ret)) {
                        continue;
                    }

                    $isDocumento = isset($arrDocumentoPorId[$idProtocoloDaIntimacao]);
                    $objProtocoloAPIComIntimacao = $isDocumento ? $arrDocumentoPorId[$idProtocoloDaIntimacao] : (isset($arrProcedimentoPorId[$idProtocoloDaIntimacao]) ? $arrProcedimentoPorId[$idProtocoloDaIntimacao] : null);

                    if ($objProtocoloAPIComIntimacao == null) {
                        continue;
                    }

                    $permissao = SeiIntegracao::$TAM_NEGADO;

                    $arrIntAnexo = isset($arrIntimacoesAnexoPorProtocolo[$idProtocoloDaIntimacao]) ? array_values($arrIntimacoesAnexoPorProtocolo[$idProtocoloDaIntimacao]) : array();
                    $arrIntDisponivel = isset($arrIntimacoesDisponivelPorProtocolo[$idProtocoloDaIntimacao]) ? array_values($arrIntimacoesDisponivelPorProtocolo[$idProtocoloDaIntimacao]) : array();
                    $arrIntimacao = array_unique(array_merge($arrIntAnexo, $arrIntDisponivel));
                    $isCertidao = $isDocumento ? $objProtocoloAPIComIntimacao->getIdSerie() == $idSerieCertidao : false;

                    if ($qtdMdPetRelIntDestExterno == 0) {
                        $permissao = SeiIntegracao::$TAM_PERMITIDO;
                    } else {
                        //Se possui intimação realiza verificações
                        $qtdArrIntimacao = (is_array($arrIntimacao) ? count($arrIntimacao) : 0);
                        if ($qtdArrIntimacao > 0) {

                            $disp = count($arrIntDisponivel) > 0 && count($arrIntAnexo) == 0;

                            //se for somente disponibilizado permite visualização
                            if ($disp) {
                                $permissao = SeiIntegracao::$TAM_PERMITIDO;

                                //Se for disponibilizado e anexo, ignora os disponibilizados e  verifica se os anexos já estão aceitos
                                //Se for anexo, verifica os aceites
                            } else {
                                //Verifica se todos os documentos anexo possuem aceite
                                $intAnexo = $arrIntAnexo;
                                $chaveIntAnexo = implode(',', $intAnexo);
                                if (!isset($arrAceitePorIntimacoes[$chaveIntAnexo])) {
                                    $arrAceitePorIntimacoes[$chaveIntAnexo] = $objMdPetAceiteRN->todasIntimacoesAceitas($intAnexo);
                                }
                                $todasIntAceit = $arrAceitePorIntimacoes[$chaveIntAnexo];

                                //Se todas as intimações possuem aceite para o anexo  permite visualização
                                if ($todasIntAceit['todasAceitas']) {
                                    $permissao = SeiIntegracao::$TAM_PERMITIDO;
                                } else {

                                    if (empty($idProtocoloDaIntimacao) || empty($idAcessoExterno)) {
                                        throw new InfraException("Parâmetros IdProtocoloDaIntimacao e IdAcessoExterno não podem ser nulos");
                                    } else {
                                        // Verifica a situação do usuário logado perante os destinatários e o cumprimento das intimações as quais o documento está vinculado
                                        $situacao = $objMdPetIntRelDestinatarioRN->getSituacaoUsuarioIntimacao($idProtocoloDaIntimacao, $idAcessoExterno);
                                        if (in_array($situacao['btn_cumprir'], array('cumprida_geral'))) {
                                            $permissao = SeiIntegracao::$TAM_PERMITIDO;
                                        }
                                    }
                                }
                            }
                        } else {
                            //Se não possuir intimações e o acesso for integral permite visualização.
                            //Se for parcial, não permite.
                            if ($isIntAcessoExt === null) {
                                $isIntAcessoExt = $objMdPetIntAcExtRN->getTipoConcessaoAcesso($idAcessoExterno);
                            }
                            if ($isIntAcessoExt == MdPetIntAcessoExternoDocumentoRN::$ACESSO_INTEGRAL) {
                                $permissao = SeiIntegracao::$TAM_PERMITIDO;
                            }

                            if ($isCertidao) {
                                $permissao = SeiIntegracao::$TAM_PERMITIDO;
                            }

                            if ($permissao != SeiIntegracao::$TAM_PERMITIDO && $isDocumento && $isPesquisaPublicaHabilitada && isset($arrDocumentoPesquisaPublicaPorId[$idProtocoloDaIntimacao])) {
                                $isDocumentoPublicoPesquisaPublica = $this->validaDocumentoPublicoPesquisaPublica($idProtocoloDaIntimacao);
                                unset($arrDocumentoPesquisaPublicaPorId[$idProtocoloDaIntimacao]);

                                if ($isDocumentoPublicoPesquisaPublica) {
                                    $permissao = SeiIntegracao::$TAM_PERMITIDO;
                                }
                            }
                        }
                    }
                    //Preenche o array de documento com o retorno
                    $ret[$idProtocoloDaIntimacao] = $permissao;
                    if ($isDocumento) {
                        unset($arrDocumentoPesquisaPublicaPorId[$idProtocoloDaIntimacao]);
                    }
                }
            }
            
            // valida se o módulo pesquisa esta habilitado para validação
            if($isPesquisaPublicaHabilitada){
                foreach ($arrDocumentoPesquisaPublicaPorId as $documentoId => $objDocumentoAPI) {
                    
                    if ($this->validaDocumentoPublicoPesquisaPublica($documentoId)) {
                        $ret[$documentoId] = SeiIntegracao::$TAM_PERMITIDO;
                    }
                }
            }

            self::$arrProtocoloAcessoExterno = $ret;
            return $ret;
        }
    }

    private function validaDocumentoPublicoPesquisaPublica($idDocumento)
    {
        // Consultar protocolo do documento
        $objProtocoloDTO = new ProtocoloDTO();
        $objProtocoloDTO->setDblIdProtocolo($idDocumento);
        $objProtocoloDTO->retStrStaNivelAcessoLocal();
        $objProtocoloDTO->retStrStaNivelAcessoGlobal();
        $objProtocoloDTO->retStrStaProtocolo();
        $objProtocoloDTO->retDtaInclusao();
        $objProtocoloDTO->retDblIdProtocolo();
        $objProtocoloDTO = (new ProtocoloRN())->consultarRN0186($objProtocoloDTO);

        if (empty($objProtocoloDTO) 
            || $objProtocoloDTO->getStrStaNivelAcessoLocal() != ProtocoloRN::$NA_PUBLICO 
            || $objProtocoloDTO->getStrStaNivelAcessoGlobal() == ProtocoloRN::$NA_SIGILOSO) {
            return false;
        }

        // Consultar dados do documento
        $objDocumentoDTO = new DocumentoDTO();
        $objDocumentoDTO->setDblIdDocumento($idDocumento);
        $objDocumentoDTO->retDblIdDocumento();
        $objDocumentoDTO->retDblIdProcedimento();
        $objDocumentoDTO->retStrStaDocumento();
        $objDocumentoDTO = (new DocumentoRN())->consultarRN0005($objDocumentoDTO);

        if (empty($objDocumentoDTO)) {
            return false;
        }

        // Verificar processo pai do documento - deve ter nivel de acesso local publico
        $idProcedimento = $objDocumentoDTO->getDblIdProcedimento();
        $objProcessoDTO = new ProtocoloDTO();
        $objProcessoDTO->setDblIdProtocolo($idProcedimento);
        $objProcessoDTO->setStrStaProtocolo(ProtocoloRN::$TP_PROCEDIMENTO);
        $objProcessoDTO->retStrStaNivelAcessoLocal();
        $objProcessoDTO->retStrStaNivelAcessoGlobal();
        $objProcessoDTO = (new ProtocoloRN())->consultarRN0186($objProcessoDTO);

        if (empty($objProcessoDTO) || $objProcessoDTO->getStrStaNivelAcessoLocal() != ProtocoloRN::$NA_PUBLICO) {
            return false;
        }

        // Verificar se o processo pai esta anexado a processo restrito
        $objRelProtocoloProtocoloDTO = new RelProtocoloProtocoloDTO();
        $objRelProtocoloProtocoloDTO->retDblIdProtocolo1();
        $objRelProtocoloProtocoloDTO->setDblIdProtocolo2($idProcedimento);
        $objRelProtocoloProtocoloDTO->setStrStaAssociacao(RelProtocoloProtocoloRN::$TA_PROCEDIMENTO_ANEXADO);
        $listaProcessosAnexadores = (new RelProtocoloProtocoloRN())->listarRN0187($objRelProtocoloProtocoloDTO);

        if (!empty($listaProcessosAnexadores)) {
            foreach ($listaProcessosAnexadores as $processoAnexador) {
                $objProtAnexadorDTO = new ProtocoloDTO();
                $objProtAnexadorDTO->setDblIdProtocolo($processoAnexador->getDblIdProtocolo1());
                $objProtAnexadorDTO->retStrStaNivelAcessoGlobal();
                $objProcessoAnexador = (new ProtocoloRN())->consultarRN0186($objProtAnexadorDTO);

                if (!empty($objProcessoAnexador) && $objProcessoAnexador->getStrStaNivelAcessoGlobal() != ProtocoloRN::$NA_PUBLICO) {
                    return false;
                }
            }
        }

        // Documentos gerados devem ser assinados (exceto formularios automaticos)
        if ($objProtocoloDTO->getStrStaProtocolo() == ProtocoloRN::$TP_DOCUMENTO_GERADO 
            && $objDocumentoDTO->getStrStaDocumento() != DocumentoRN::$TD_FORMULARIO_AUTOMATICO) {
                
            $objAssinaturaDTO = new AssinaturaDTO();
            $objAssinaturaDTO->setDblIdDocumento($idDocumento);
            $objAssinaturaDTO->retNumIdAssinatura();
            $objAssinaturaDTO->setNumMaxRegistrosRetorno(1);
            $arrObjAssinaturaDTO = (new AssinaturaRN())->listarRN1323($objAssinaturaDTO);

            if (empty($arrObjAssinaturaDTO)) {
                return false;
            }
        }

        // Verificar data de corte da pesquisa
        $dtaParamCortePesquisa = (new MdPesqParametroPesquisaRN())->existeDataCortePesquisa();
        if ($dtaParamCortePesquisa) {
            $dtaCorteDoc = $objProtocoloDTO->getDtaInclusao();

            // Para documentos gerados (editor interno ou formulario gerado), usar data da primeira assinatura
            if ($objProtocoloDTO->getStrStaProtocolo() == ProtocoloRN::$TP_DOCUMENTO_GERADO
                && in_array($objDocumentoDTO->getStrStaDocumento(), [DocumentoRN::$TD_EDITOR_INTERNO, DocumentoRN::$TD_FORMULARIO_GERADO])) {

                $objAssinDTO = new AssinaturaDTO();
                $objAssinDTO->retDthAberturaAtividade();
                $objAssinDTO->setDblIdDocumento($idDocumento);
                $objAssinDTO->setOrdNumIdAssinatura(InfraDTO::$TIPO_ORDENACAO_ASC);
                $objAssinDTO->setNumMaxRegistrosRetorno(1);
                $arrObjAssinDTO = (new AssinaturaRN())->listarRN1323($objAssinDTO);

                if (!empty($arrObjAssinDTO) && $arrObjAssinDTO[0] != null && $arrObjAssinDTO[0]->isSetDthAberturaAtividade()) {
                    $dtaCorteDoc = substr($arrObjAssinDTO[0]->getDthAberturaAtividade(), 0, 10);
                }
            }

            $dtaCorteDocFormatada = date('Y-m-d', strtotime(str_replace('/', '-', $dtaCorteDoc)));
            if ($dtaParamCortePesquisa > $dtaCorteDocFormatada) {
                return false;
            }
        }

        // Verificar intimacao nao cumprida
        if (PesquisaIntegracao::verificaSeModPeticionamentoVersaoMinima()) {
            if (!(new MdPetIntCertidaoRN())->verificaDocumentoEAnexoIntimacaoNaoCumprida([$idDocumento, false, false, true])) {
                return false;
            }
        }

        return true;
    }

    public static function verificaSeModPesquisaPublicaVersaoMinima()
    {
        $arrModulos = ConfiguracaoSEI::getInstance()->getValor('SEI','Modulos');

        if(is_array($arrModulos) && array_key_exists('PesquisaIntegracao', $arrModulos)){
            $objInfraParametroDTO = new InfraParametroDTO();
            $objInfraParametroDTO->setStrNome('VERSAO_MODULO_PESQUISA_PUBLICA');
            $objInfraParametroDTO->retStrValor();

            $objInfraParametroBD = new InfraParametroBD(BancoSEI::getInstance());
            $arrObjInfraParametroDTO = $objInfraParametroBD->consultar($objInfraParametroDTO);

            if(!empty($arrObjInfraParametroDTO)){
                return version_compare($arrObjInfraParametroDTO->getStrValor(), '4.3.0', '>=');
            }
        }

        return false;
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
        
        //só é necessário adicionar botao por este ponto SE o processo estiver numa situação em que nao foi adicionado nenhum
        // botao pelo ponto de extensao do acesso externo autorizado (o que deixaria em alguns casos
        // a coluna "Ações" sem quaisquer icones
        // Exemplo: Processo com apenas 1 documento assinado, com intimação gerada do tipo integral e sem anexos
        $qtdArrDocumentosLiberados = (is_array($this->arrDocumentosLiberados) ? count($this->arrDocumentosLiberados) : 0);
        if ($qtdArrDocumentosLiberados == 0) {
            return $this->montarBotaoAcessoExternoPeticionamento($arrObjDocumentoAPI);
        } else {
            return null;
        }
    }

    public function montarAcaoProcessoAnexadoAcessoExternoNegado($arrObjProcedimentoAPI)
    {
        //só é necessário adicionar botao por este ponto SE o processo estiver numa situação em que nao foi adicionado nenhum
        // botao pelo ponto de extensao do acesso externo autorizado (o que deixaria em alguns casos
        // a coluna "Ações" sem quaisquer icones
        // Exemplo: Processo com apenas 1 documento assinado, com intimação gerada do tipo integral e sem anexos
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
                        $objInfraException->adicionarValidacao($this->getNome() . ': Não é permitido cancelar a disponibilização para esse usuário, pois existem vínculos no módulo Peticionamento e Intimação Eletrônicos.');
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
     * Valida se o Processo onde está realizando a anexação de processo possui Vínculo com Intimação ou se possui Documento não assinado
     */
    public function anexarProcesso(ProcedimentoAPI $objProcedimentoAPIPrincipal, ProcedimentoAPI $objProcedimentoAPIAnexado)
    {
	
	    $existeIntimacaoPrazoValido = (new MdPetIntimacaoRN())->existeIntimacaoPrazoValido($objProcedimentoAPIAnexado->getIdProcedimento());

        if ($existeIntimacaoPrazoValido) {
            $objInfraException = new InfraException();
            $objInfraException->adicionarValidacao($this->getNome() . ': Não é permitido anexar este processo, pois o mesmo possui Intimação Eletrônica ainda em curso.');
            $objInfraException->lancarValidacoes();
        }
	
	    // INICIO MIGRACAO PARAMETROS DO UTILIDADES PARA O PETICIONAMENTO
	    $paramPet = 'MODULO_PETICIONAMENTO_BLOQUEAR_ANEXAR_PROCESSO_COM_DOCUMENTO_NAO_ASSINADO';
	    $blockAnexarProcDocNaoAssinado = (new InfraParametro(BancoSEI::getInstance()))->getValor($paramPet, false);
	
	    if (!empty($blockAnexarProcDocNaoAssinado) && $blockAnexarProcDocNaoAssinado == '1') {
	    	
		    $processos      = $this->listarDocumentos($objProcedimentoAPIAnexado->getIdProcedimento());
		    $listaProcessos = '';
		    $docNaoAssinado = false;
		
		    foreach ($processos as $processo) {
		    	
			    $objPesquisaProtocoloDTO = new PesquisaProtocoloDTO();
			    $objPesquisaProtocoloDTO->setStrStaTipo(ProtocoloRN::$TPP_DOCUMENTOS_GERADOS);
			    $objPesquisaProtocoloDTO->setStrStaAcesso(ProtocoloRN::$TAP_TODOS);
			    $objPesquisaProtocoloDTO->setDblIdProtocolo($processo->getDblIdDocumento());
			    $arrObjProtocoloDTO = (new ProtocoloRN())->pesquisarRN0967($objPesquisaProtocoloDTO);
			
			    if ($arrObjProtocoloDTO) {
				    $staDocumento   = $arrObjProtocoloDTO[0]->getStrStaDocumentoDocumento();
				    $sinAssinado    = $arrObjProtocoloDTO[0]->getStrSinAssinado();
				    $staProtocolo   = $arrObjProtocoloDTO[0]->getStrStaProtocolo();
				    $staEstado      = $arrObjProtocoloDTO[0]->getStrStaEstado();
				
				    if ($staProtocolo === ProtocoloRN::$TP_DOCUMENTO_GERADO && $staDocumento != DocumentoRN::$TD_FORMULARIO_AUTOMATICO && $staEstado != ProtocoloRN::$TE_DOCUMENTO_CANCELADO && $sinAssinado == 'N') {
					    $listaProcessos = $listaProcessos . '- '.$arrObjProtocoloDTO[0]->getStrNomeSerieDocumento().' '.$arrObjProtocoloDTO[0]->getStrNumeroDocumento().' ('.$arrObjProtocoloDTO[0]->getStrProtocoloFormatado(). ")\n";
					    $docNaoAssinado = true;
				    }
			    }
			    
		    }
		
		    if ($docNaoAssinado) {
			    return (new InfraException())->lancarValidacao("Não é possível anexar o processo indicado, pois nele ainda constam documentos gerados não assinados: \n" . $listaProcessos);
		    }
		    
	    }
	    // FIM MIGRACAO PARAMETROS DO UTILIDADES PARA O PETICIONAMENTO
	
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
            $msg = $this->getNome() . ': Não é permitido sobrestar este processo, pois o mesmo possui Intimação Eletrônica ainda em curso.';


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
            $msg = $this->getNome() . ': Não é permitido Bloquear este processo, pois o mesmo possui Intimação Eletrônica ainda em curso.';


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
                $msg = $this->getNome() . ': Não é permitido desanexar este processo, pois o mesmo é anexo de Documento Principal de Intimação Eletrônica neste processo.';
            } else {
                $msg = $this->getNome() . ': Não é permitido desanexar este processo, pois o mesmo é Documento Disponível de Intimação Eletrônica neste processo.';
            }

            $objInfraException = new InfraException();
            $objInfraException->adicionarValidacao($msg);
            $objInfraException->lancarValidacoes();
            return null;
        } else {

            return parent::desanexarProcesso($objProcedimentoAPIPrincipal, $objProcedimentoAPIAnexado);
        }
    }
	
	// FUNCTION ADICIONADAS PARA ATENDER A MIGRAÇÃO DOS PARAMETROS DO MÓDULO UTILIDADES PARA O PETICIONAMENTO
	public function concluirProcesso($arrObjProcedimentoAPI)
	{
		
		// INICIO MIGRACAO PARAMETROS DO UTILIDADES PARA O PETICIONAMENTO
		$paramPet = 'MODULO_PETICIONAMENTO_BLOQUEAR_CONCLUIR_PROCESSO_COM_DOCUMENTO_NAO_ASSINADO';
		$ultimaConclusao = $this->verificaUltimaConclusao($arrObjProcedimentoAPI[0]->getIdProcedimento());
		$blockConcluirProcDocNaoAssinado = (new InfraParametro(BancoSEI::getInstance()))->getValor($paramPet, false);
		
		if (!empty($blockConcluirProcDocNaoAssinado) && $blockConcluirProcDocNaoAssinado == '1' && $ultimaConclusao) {
			
			$protocolosDocumentosProcesso = $this->listarDocumentos($arrObjProcedimentoAPI[0]->getIdProcedimento());
			$listaProtocolosDocumentosNaoAssinados = '';
			$docNaoAssinado = false;
			
			foreach ($protocolosDocumentosProcesso as $documentosProcesso) {
				
				$objPesquisaProtocoloDTO = new PesquisaProtocoloDTO();
				$objPesquisaProtocoloDTO->setStrStaTipo(ProtocoloRN::$TPP_DOCUMENTOS_GERADOS);
				$objPesquisaProtocoloDTO->setStrStaAcesso(ProtocoloRN::$TAP_TODOS);
				$objPesquisaProtocoloDTO->setDblIdProtocolo($documentosProcesso->getDblIdDocumento());
				$arrObjProtocoloDTO = (new ProtocoloRN())->pesquisarRN0967($objPesquisaProtocoloDTO);
				
				if ($arrObjProtocoloDTO) {
					
					$staDocumento   = $arrObjProtocoloDTO[0]->getStrStaDocumentoDocumento();
					$sinAssinado    = $arrObjProtocoloDTO[0]->getStrSinAssinado();
					$staProtocolo   = $arrObjProtocoloDTO[0]->getStrStaProtocolo();
					$staEstado      = $arrObjProtocoloDTO[0]->getStrStaEstado();
					
					if ($staProtocolo === ProtocoloRN::$TP_DOCUMENTO_GERADO && $staDocumento != DocumentoRN::$TD_FORMULARIO_AUTOMATICO && $staEstado != ProtocoloRN::$TE_DOCUMENTO_CANCELADO && $sinAssinado == 'N') {
						$listaProtocolosDocumentosNaoAssinados = $listaProtocolosDocumentosNaoAssinados . '- '.$arrObjProtocoloDTO[0]->getStrNomeSerieDocumento().' '.$arrObjProtocoloDTO[0]->getStrNumeroDocumento().' ('.$arrObjProtocoloDTO[0]->getStrProtocoloFormatado(). ")\n";
						$docNaoAssinado = true;
					}
					
				}
				
			}
			
			if ($docNaoAssinado) {
				$objInfraException = new InfraException();
				$msg = "Não é possível concluir o processo, pois nele ainda constam documentos gerados não assinados: \n\n" . $listaProtocolosDocumentosNaoAssinados;
				return $objInfraException->lancarValidacao($msg);
			}
			
		}
		// FIM MIGRACAO PARAMETROS DO UTILIDADES PARA O PETICIONAMENTO


        (new MdPetIntimacaoRN())->bloquearUltimaConclusaoProcessoSigilosoIntimacaoEmCurso($arrObjProcedimentoAPI[0]->getIdProcedimento());
		
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
		
	/**
	 * @param $arrValor
	 * @return bool
	 */
	public function verificaExistenciaIdGrupo($arrValor)
	{
		$objGrupoContatoRN = new GrupoContatoRN();
		foreach ($arrValor as $item) {
			$objGrupoContatoDTO = new GrupoContatoDTO();
			$objGrupoContatoDTO->setNumIdGrupoContato($item);
			$objGrupoContatoDTO->setStrSinAtivo('S');
			$objGrupoContatoDTO->retNumIdGrupoContato();
			
			$objGrupoContatoDTO = $objGrupoContatoRN->listarRN0477($objGrupoContatoDTO);
			
			if ($objGrupoContatoDTO) {
				return true;
			}
		}
		
		return false;
	}
		
	public function desativarContato($arrObjContatoAPI)
	{
		
		// INICIO MIGRACAO PARAMETROS DO UTILIDADES PARA O PETICIONAMENTO
		$paramPet  = 'MODULO_PETICIONAMENTO_ID_GRUPOS_CONTATO_TRAVAR_CONTATOS';
		$strValor  = (new InfraParametro(BancoSEI::getInstance()))->getValor($paramPet, false);
		$arrValor  = [];
		
		if (!empty($strValor) && preg_match('/^\d+(,\d+)*$/', $strValor) && $_GET['acao'] === 'contato_desativar' && $_GET['acao_origem'] === 'contato_listar') {
			$arrValor = array_merge($arrValor, explode(',', $strValor));
			$idExistente = $this->verificaExistenciaIdGrupo($arrValor);
			
			if ($idExistente) {
				
				$objRelGrupoContatoDTO = new RelGrupoContatoDTO();
				$objRelGrupoContatoDTO->retNumIdGrupoContato();
				$objRelGrupoContatoDTO->setNumIdContato($arrObjContatoAPI[0]->getIdContato());
				$objRelGrupoContatoDTO->setOrd('IdGrupoContato', 'desc');
				$objRelGrupoContatoDTO = (new RelGrupoContatoRN())->listarRN0463($objRelGrupoContatoDTO);
				
				if ($objRelGrupoContatoDTO) {
					
					foreach ($objRelGrupoContatoDTO as $objContato) {
						
						if (in_array($objContato->getNumIdGrupoContato(), $arrValor)) {
							$objGrupoContatoRN = new GrupoContatoRN();
							$objDTO = new GrupoContatoDTO();
							$objDTO->setNumIdGrupoContato($objContato->getNumIdGrupoContato());
							$objDTO->setStrSinAtivo('S');
							$objDTO->retNumIdUnidade();
							$objDTO->retStrNome();
							$objDTO = $objGrupoContatoRN->listarRN0477($objDTO);
							
							// Verifica se a unidade do usuário logado é a mesma unidade do grupo
							if ($objDTO && SessaoSEI::getInstance()->getNumIdUnidadeAtual() != $objDTO[0]->getNumIdUnidade()) {
								$objUnidadeRN  = new UnidadeRN();
								$objUnidadeDTO = new UnidadeDTO();
								$objUnidadeDTO->retStrDescricao();
								$objUnidadeDTO->retStrSigla();
								$objUnidadeDTO->setNumIdUnidade($objDTO[0]->getNumIdUnidade());
								$objUnidadeDTO = $objUnidadeRN->consultarRN0125($objUnidadeDTO);
								
								$msg = $this->getNome() . ': Não é possível Desativar o Contato, pois ele está no Grupo de Contato ' . $objDTO[0]->getStrNome() .
									' sob controle centralizado da Unidade ' . $objUnidadeDTO->getStrDescricao() . ' ('. $objUnidadeDTO->getStrSigla() . ').';
								$objInfraException = new InfraException();
								$objInfraException->adicionarValidacao($msg);
								$objInfraException->lancarValidacoes();
								return null;
							}
						}
					}
				}
			}
		}
		// FIM MIGRACAO PARAMETROS DO UTILIDADES PARA O PETICIONAMENTO
		
		$msg = (new MdPetRegrasGeraisRN())->verificarExcluirDesativarContato([$arrObjContatoAPI, 'desativar']);
		if ($msg != '') {
			(new InfraException())->lancarValidacao($msg);
		}
		
	}

    public function verificarAcessoProtocolo($arrObjProcedimentoAPI, $arrObjDocumentoAPI)
    {

        $ret = null;

        $arrDocsLiberados = array_values(
            (new InfraParametro(BancoSEI::getInstance()))->listarValores(
                array(
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
                ),
                false
            )
        );

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
    
    public function verificaGrupoContatoTravarContato(ContatoAPI $objContatoAPI){
		
		// Verifica se o Contato pertence a algum Grupo de Contato:
	    $objRelGrupoContatoDTO = new RelGrupoContatoDTO();
	    $objRelGrupoContatoDTO->retNumIdGrupoContato();
	    $objRelGrupoContatoDTO->setNumIdContato($objContatoAPI->getIdContato());
	    $objRelGrupoContatoDTO->setOrd('IdGrupoContato', 'desc');
	    $objRelGrupoContatoDTO = (new RelGrupoContatoRN())->listarRN0463($objRelGrupoContatoDTO);
	
	    if ($objRelGrupoContatoDTO) {
		
		    // Ids dos Grupos de Contato aos quais o Contato pertence:
		    $arrIdGruposContatoUsuario = InfraArray::converterArrInfraDTO($objRelGrupoContatoDTO, 'IdGrupoContato');
		
	    	// Busca os Grupos de Contato parametrizados:
		    $arrIdGruposContatoBloquear = array_map('trim', explode(',', (new InfraParametro(BancoSEI::getInstance()))->getValor('MODULO_PETICIONAMENTO_ID_GRUPOS_CONTATO_TRAVAR_CONTATOS', false)));
		    
		    // Verifica se os Grupos do Contato fazem parte dos Grupos de Contato parametrizados para bloqueio:
		    $intersectedGruposContato   = array_values(array_intersect($arrIdGruposContatoUsuario, $arrIdGruposContatoBloquear));
		    
		    if(!empty($intersectedGruposContato)){
			
			    $msg = $this->getNome() . ': Não é possível Atualizar o Contato pois o mesmo está sob controle apenas das Unidades gestoras dos Grupos de Contato abaixo:\n\n';
			    $idUnidadesPodemEditar = [];
		    	
			    for ($i = 0; $i < count($intersectedGruposContato); $i++){
				
				    $objGrupoContatoDTO = new GrupoContatoDTO();
				    $objGrupoContatoDTO->setNumIdGrupoContato($intersectedGruposContato[$i]);
				    $objGrupoContatoDTO->setStrSinAtivo('S');
				    $objGrupoContatoDTO->retNumIdUnidade();
				    $objGrupoContatoDTO->retStrNome();
				    $objGrupoContatoDTO = (new GrupoContatoRN())->consultarRN0474($objGrupoContatoDTO);
				
				    $objUnidadeDTO = new UnidadeDTO();
				    $objUnidadeDTO->retStrDescricao();
				    $objUnidadeDTO->retStrSigla();
				    $objUnidadeDTO->setNumIdUnidade($objGrupoContatoDTO->getNumIdUnidade());
				    $objUnidadeDTO = (new UnidadeRN())->consultarRN0125($objUnidadeDTO);
				
				    array_push($idUnidadesPodemEditar, $objGrupoContatoDTO->getNumIdUnidade());
				
				    $msg .= '- ' . $objGrupoContatoDTO->getStrNome() . ' - Sob controle da Unidade: ' . $objUnidadeDTO->getStrDescricao() . ' ('. $objUnidadeDTO->getStrSigla() . ').\n';
				
			    }
			
			    if(!in_array(SessaoSEI::getInstance()->getNumIdUnidadeAtual(), $idUnidadesPodemEditar)){
				
				    $objInfraException = new InfraException();
				    $objInfraException->adicionarValidacao($msg);
				    $objInfraException->lancarValidacoes();
				
				    return null;
				
			    }
			    
		    }
		    
	    }
	   
    }

    public function alterarContato(ContatoAPI $objContatoAPI)
    {

        // Verifica se o Usuario fez login com o GOV.br
        if(!isset($_GET['id_login_sso']) && isset($_GET['acao']) && $_GET['acao'] != 'usuario_externo_logar'){
	
            $this->verificaGrupoContatoTravarContato($objContatoAPI);

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

    }

    /**
     * @param array<string, mixed> $arrPost
     * @param array<string, string> $arrayCampos
     */
    public static function validarXssFormulario(array $arrPost, array $arrayCampos, InfraException $objInfraException): bool
    {
        $bolRecusado = false;
        foreach ($arrPost as $chave => $elemento) {
            $strRotulo = $arrayCampos[$chave] ?? null;

            if (is_array($elemento)) {
                $arrRotulos = $strRotulo === null ? [] : array_fill_keys(array_keys($elemento), $strRotulo);
                if (self::validarXssFormulario($elemento, $arrRotulos, $objInfraException)) {
                    $bolRecusado = true;
                }
                continue;
            }

            if (!is_string($elemento)) {
                throw new InfraException('Valor de formulário inválido para validação XSS.');
            }
            if (!self::isConteudoXss($elemento)) {
                continue;
            }

            $bolRecusado = true;
            $objInfraException->adicionarValidacao($strRotulo === null
                ? self::MSG_CONTEUDO_NAO_PERMITIDO
                : sprintf(self::MSG_XSS_CAMPO, PaginaSEI::tratarHTML($strRotulo)));
        }
        return $bolRecusado;
    }

    /** Mantém o retorno do infraUpload seguro quando o nome contém HTML ou o separador do protocolo. */
    private static function mensagemXssArquivo(string $strNome): string
    {
        if ($strNome === '' || preg_match('/[#<&\\x00-\\x1F\\x7F]/', $strNome)) {
            return self::MSG_CONTEUDO_NAO_PERMITIDO;
        }

        return sprintf(self::MSG_XSS_ARQUIVO, $strNome);
    }

    /** Consulta o filtro do core para entrada recebida em UTF-8 pelo Ajax. */
    public static function consultarXssTextoAjax(mixed $mixTexto): string
    {
        if (!is_string($mixTexto) || !mb_check_encoding($mixTexto, 'UTF-8')) {
            throw new InfraException('Texto de validação XSS inválido.');
        }

        $strTexto = mb_encode_numericentity($mixTexto, [0x100, 0x10FFFF, 0, 0x1FFFFF], 'UTF-8');
        $strTexto = mb_convert_encoding($strTexto, 'ISO-8859-1', 'UTF-8');

        return self::isConteudoXss($strTexto) ? 'RECUSADO' : 'OK';
    }

    /** Consulta em lote os campos de texto enviados pelo Ajax. */
    public static function consultarXssTextosAjax(mixed $mixTextos): string
    {
        if (!is_array($mixTextos) || !array_is_list($mixTextos) || count($mixTextos) === 0 || count($mixTextos) > 32) {
            throw new InfraException('Lista de textos para validação XSS inválida.');
        }

        foreach ($mixTextos as $mixTexto) {
            if (self::consultarXssTextoAjax($mixTexto) === 'RECUSADO') {
                return 'RECUSADO';
            }
        }

        return 'OK';
    }

    /** Confere a especificação antes da assinatura e antes de abrir o processo. */
    public static function validarXssEspecificacao(?string $strValor, InfraException $objInfraException): void
    {
        if ($strValor === null || $strValor === '' || !self::isConteudoXss($strValor)) {
            return;
        }

        $objInfraException->adicionarValidacao(self::MSG_XSS_ESPECIFICACAO);
    }

    /**
     * Confere o complemento antes do upload e novamente antes da gravação: filtro XSS do core, caractere que o SEI não
     * aceita e tamanho do Nome na Árvore do documento.
     */
    public static function validarXssComplemento(?string $strValor, InfraException $objInfraException): bool
    {
        $strRecusaComplemento = self::lerRecusaComplemento($strValor);
        if ($strRecusaComplemento === null) {
            return false;
        }

        $objInfraException->adicionarValidacao($strRecusaComplemento);
        return true;
    }

    /**
     * O navegador envia o caractere que não existe na codificação da página como referência numérica (&#NNN;). A
     * referência ocupa de 6 a 9 posições do Nome na Árvore e aparece como está na árvore do processo.
     *
     * @param string|null $strComplemento
     * @return string|null mensagem da recusa, ou null quando o complemento é aceito
     */
    private static function lerRecusaComplemento(?string $strComplemento): ?string
    {
        if ($strComplemento === null || $strComplemento === '') {
            return null;
        }

        if (self::isConteudoXss($strComplemento)) {
            return self::MSG_XSS_COMPLEMENTO;
        }

        preg_match_all('/&#(\d+);/', $strComplemento, $arrReferenciasNumericas);
        foreach ($arrReferenciasNumericas[1] as $strCodigoCaractere) {
            if ((int) $strCodigoCaractere > 255) {
                return self::MSG_COMPLEMENTO_CARACTERE_NAO_ACEITO;
            }
        }

        if (strlen(trim($strComplemento)) > self::TAMANHO_MAXIMO_COMPLEMENTO) {
            return self::MSG_COMPLEMENTO_TAMANHO;
        }

        return null;
    }

    /** @return string|null retorno de erro do infraUpload ou null quando o complemento é permitido */
    public static function validarXssComplementoUpload(string $strCampo): ?string
    {
        $strValor = PaginaSEI::POST($strCampo);
        if (!is_string($strValor)) {
            throw new InfraException('Complemento do tipo de documento não informado ou inválido.');
        }

        $strRecusaComplemento = self::lerRecusaComplemento($strValor);

        return $strRecusaComplemento === null ? null : 'ERRO#' . $strRecusaComplemento;
    }

    /** Recusa conteúdo de texto não permitido com a mensagem genérica do módulo. */
    public static function validarXssTextoGenerico(string $strTexto): void
    {
        if (!self::isConteudoXss($strTexto)) {
            return;
        }

        (new InfraException())->lancarValidacao(self::MSG_CONTEUDO_NAO_PERMITIDO);
    }

    /** Recusa o motivo da revogação ou da renúncia antes de gerar o documento (ADR-PET-005). */
    public static function validarXssMotivoProcuracao(string $strMotivo): void
    {
        self::validarXssTextoGenerico($strMotivo);
    }

    /**
     * Confere o texto salvo no editor do Documento Principal do Peticionamento Externo, antes da conclusão.
     *
     * @param string $strConteudo
     * @return void
     * @throws InfraException validação para o usuário quando o filtro XSS do core acusa o conteúdo
     */
    public static function validarXssEditor(string $strConteudo): void
    {
        if (self::isConteudoXss($strConteudo)) {
            (new InfraException())->lancarValidacao(self::MSG_CONTEUDO_NAO_PERMITIDO);
        }
    }

    /**
     * Confere um valor informado pelo usuário com o filtro XSS do core e registra a recusa como validação do campo, como
     * os métodos validarStr* das RNs do core (ADR-PET-005). Valor nulo ou vazio não é conferido.
     *
     * @param string|null $strValor
     * @param string $strRotulo nome do campo na mensagem
     * @param InfraException $objInfraException
     * @return void
     */
    public static function validarXssCampo(?string $strValor, string $strRotulo, InfraException $objInfraException): void
    {
        if ($strValor === null || $strValor === '') {
            return;
        }

        if (self::isConteudoXss($strValor)) {
            $objInfraException->adicionarValidacao(sprintf(self::MSG_XSS_CAMPO, $strRotulo));
        }
    }

    /**
     * Recebe o arquivo pelo upload do core e confere o conteúdo antes da conclusão do Peticionamento Externo.
     *
     * @param string $strCampoArquivo
     * @param bool $bolArquivoTemporarioIdentificado
     * @param string|null $strCampoComplemento campo de texto enviado junto ao arquivo
     * @return string retorno do infraUpload: "nome_upload#nome#tipo#tamanho#data#" ou "ERRO#mensagem"
     */
    public static function processarUploadExterno(string $strCampoArquivo, bool $bolArquivoTemporarioIdentificado, ?string $strCampoComplemento = null): string
    {
        if ($strCampoComplemento !== null) {
            $strErro = self::validarXssComplementoUpload($strCampoComplemento);
            if ($strErro !== null) {
                return $strErro;
            }
        }

        ob_start();
        PaginaSEIExterna::getInstance()->processarUpload($strCampoArquivo, DIR_SEI_TEMP, $bolArquivoTemporarioIdentificado);

        return self::validarTamanhoUpload(self::validarXssUpload((string) ob_get_clean(), DIR_SEI_TEMP), DIR_SEI_TEMP);
    }

    /**
     * O nome original do arquivo passa pelo filtro XSS do core (ADR-PET-005). Arquivo identificado como HTML passa pelo
     * filtro, como na conclusão (AnexoRN::validarConteudo), e o PDF passa pela verificação da ADR-PET-001. O arquivo
     * recusado é apagado da área temporária e o retorno passa a ser de erro.
     *
     * @param string $strRetornoUpload retorno do InfraPagina::processarUpload
     * @param string $strDiretorio diretório em que o upload gravou o arquivo
     * @return string
     */
    public static function validarXssUpload(string $strRetornoUpload, string $strDiretorio): string
    {
        if (strpos($strRetornoUpload, 'ERRO#') === 0) {
            return $strRetornoUpload;
        }

        [$strArquivo, $strNome] = self::lerArquivoDoRetornoUpload($strRetornoUpload, $strDiretorio);

        if (self::isConteudoXss($strNome)) {
            unlink($strArquivo);

            return 'ERRO#' . self::MSG_CONTEUDO_NAO_PERMITIDO;
        }

        if (!self::isArquivoNaoPermitido($strArquivo)) {
            return $strRetornoUpload;
        }

        unlink($strArquivo);

        return 'ERRO#' . self::mensagemXssArquivo($strNome);
    }

    /**
     * ADR-PET-002: antes da primeira etapa da conclusão, confere o texto do editor do Documento Principal e os arquivos
     * temporários com as regras do salvar no editor e do upload, para recusar sem abrir processo. Arquivo inexistente
     * ou fora do diretório fica para as validações do core.
     *
     * @param string|null $strConteudoEditor texto do editor do Documento Principal, quando houver
     * @param array<string, string> $arrArquivos nome do arquivo temporário => nome exibido ao usuário
     * @param string $strDiretorio diretório dos arquivos temporários
     * @return void
     * @throws InfraException validação para o usuário quando o conteúdo não é permitido
     */
    public static function validarXssConclusao(?string $strConteudoEditor, array $arrArquivos, string $strDiretorio): void
    {
        $objInfraException = new InfraException();

        if ($strConteudoEditor !== null && self::isConteudoXss($strConteudoEditor)) {
            $objInfraException->adicionarValidacao(self::MSG_CONTEUDO_NAO_PERMITIDO);
        }

        foreach ($arrArquivos as $strNomeTemporario => $strNome) {
            $strArquivo = $strDiretorio . '/' . $strNomeTemporario;

            if (realpath(dirname($strArquivo)) !== realpath($strDiretorio) || !is_file($strArquivo)) {
                continue;
            }

            if (self::isArquivoNaoPermitido($strArquivo)) {
                $objInfraException->adicionarValidacao(self::mensagemXssArquivo($strNome));
            }
        }

        $objInfraException->lancarValidacoes();
    }

    /**
     * RT-PET-026: antes da primeira etapa da conclusão, confere se cada arquivo carregado ainda está na área temporária. A
     * limpeza da área temporária pode apagar o arquivo entre o carregamento e a conclusão; sem a conferência, a leitura do
     * arquivo falhava depois de gravar, e a conclusão era desfeita.
     *
     * @param array<string, string> $arrArquivos nome do arquivo temporário => nome exibido ao usuário
     * @param string $strDiretorio diretório dos arquivos temporários
     * @return void
     * @throws InfraException validação para o usuário quando algum arquivo não está mais disponível
     */
    public static function validarArquivosDisponiveisConclusao(array $arrArquivos, string $strDiretorio): void
    {
        foreach (array_keys($arrArquivos) as $strNomeTemporario) {
            $strCaminhoArquivo = $strDiretorio . '/' . $strNomeTemporario;
            clearstatcache(true, $strCaminhoArquivo);

            if ($strNomeTemporario === '' || realpath(dirname($strCaminhoArquivo)) !== realpath($strDiretorio) || !is_file($strCaminhoArquivo)) {
                (new InfraException())->lancarValidacao(self::MSG_ARQUIVO_INDISPONIVEL);
            }
        }
    }

    /**
     * ADR-PET-010: recusa o arquivo enviado acima do limite do SEI para a extensão dele, antes de entrar na grade, com o
     * tamanho real. O arquivo recusado é apagado da área temporária, e o retorno passa a ser de erro.
     *
     * @param string $strRetornoUpload retorno do InfraPagina::processarUpload, já conferido pelo filtro XSS
     * @param string $strDiretorio diretório em que o upload gravou o arquivo
     * @return string
     */
    public static function validarTamanhoUpload(string $strRetornoUpload, string $strDiretorio): string
    {
        if (strpos($strRetornoUpload, 'ERRO#') === 0) {
            return $strRetornoUpload;
        }

        [$strArquivo, $strNome] = self::lerArquivoDoRetornoUpload($strRetornoUpload, $strDiretorio);
        $arrRecusas = (new MdPetTamanhoArquivoRN())->conferirTamanho([['nome' => $strNome, 'tamanho' => (int) filesize($strArquivo)]]);

        if ($arrRecusas === []) {
            return $strRetornoUpload;
        }

        unlink($strArquivo);

        return 'ERRO#' . $arrRecusas[0];
    }

    /**
     * ADR-PET-010: antes da primeira etapa da conclusão, confere o tamanho dos arquivos temporários com o limite do SEI,
     * junto da verificação XSS. Arquivo inexistente ou fora do diretório fica para as validações do core.
     *
     * @param array<string, string> $arrArquivos nome do arquivo temporário => nome exibido ao usuário
     * @param string $strDiretorio diretório dos arquivos temporários
     * @return void
     * @throws InfraException validação para o usuário, uma por arquivo recusado
     */
    public static function validarTamanhoConclusao(array $arrArquivos, string $strDiretorio): void
    {
        $arrArquivosMedidos = [];

        foreach ($arrArquivos as $strNomeTemporario => $strNome) {
            $strArquivo = $strDiretorio . '/' . $strNomeTemporario;

            if (realpath(dirname($strArquivo)) !== realpath($strDiretorio) || !is_file($strArquivo)) {
                continue;
            }

            $arrArquivosMedidos[] = ['nome' => (string) $strNome, 'tamanho' => (int) filesize($strArquivo)];
        }

        if ($arrArquivosMedidos === []) {
            return;
        }

        $objInfraException = new InfraException();
        foreach ((new MdPetTamanhoArquivoRN())->conferirTamanho($arrArquivosMedidos) as $strRecusa) {
            $objInfraException->adicionarValidacao($strRecusa);
        }

        $objInfraException->lancarValidacoes();
    }

    /**
     * ADR-PET-010: limites do SEI e textos da recusa por tamanho para o JavaScript da tela, que confere o arquivo antes da
     * transferência sem requisição ao servidor. Sai em ASCII, com os acentos em \u, e com marcação, aspas e "&" escapados, para
     * entrar no script da página.
     *
     * @return string JSON com recusa, geral, extensoes e configuracoesInvalidas
     */
    public static function montarLimitesDeTamanhoParaTela(): string
    {
        return json_encode(self::converterParaUtf8((new MdPetTamanhoArquivoRN())->obterLimitesParaTela()), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
    }

    /**
     * Textos e chaves de Latin-1 para UTF-8, como o json_encode exige.
     *
     * @param mixed $mixValor
     * @return mixed
     */
    private static function converterParaUtf8(mixed $mixValor): mixed
    {
        if (is_string($mixValor)) {
            return mb_convert_encoding($mixValor, 'UTF-8', 'ISO-8859-1');
        }

        if (!is_array($mixValor)) {
            return $mixValor;
        }

        $arrConvertido = [];
        foreach ($mixValor as $mixChave => $mixItem) {
            $arrConvertido[self::converterParaUtf8($mixChave)] = self::converterParaUtf8($mixItem);
        }

        return $arrConvertido;
    }

    /**
     * Caminho do arquivo temporário e nome original, lidos do retorno do upload. O nome pode ter "#", o separador dos campos.
     *
     * @param string $strRetornoUpload retorno do InfraPagina::processarUpload, sem erro
     * @param string $strDiretorio diretório em que o upload gravou o arquivo
     * @return array{string, string}
     */
    private static function lerArquivoDoRetornoUpload(string $strRetornoUpload, string $strDiretorio): array
    {
        $arrRetornoUpload = explode('#', $strRetornoUpload);
        $strNome = count($arrRetornoUpload) > 6
            ? implode('#', array_slice($arrRetornoUpload, 1, -4))
            : ($arrRetornoUpload[1] ?? '');

        return [$strDiretorio . '/' . $arrRetornoUpload[0], $strNome];
    }

    /**
     * @param string $strArquivo
     * @return bool
     */
    private static function isArquivoNaoPermitido(string $strArquivo): bool
    {
        $strMime = (string) mime_content_type($strArquivo);

        if (strpos($strMime, 'text/html') !== false) {
            return self::isConteudoXss((string) file_get_contents($strArquivo));
        }

        if ($strMime === 'application/pdf') {
            return self::isPdfNaoPermitido($strArquivo);
        }

        return false;
    }

    /**
     * ADR-PET-001: recusa o PDF com JavaScript ou com ação de abrir programa, enviar ou importar dados, ou cujo texto o
     * filtro XSS do core acusa. O nível N do bloco XSS desliga a verificação, e o PDF que o parser não lê segue a flag
     * $bolAceitarPdfNaoLido.
     *
     * @param string $strArquivo
     * @return bool
     */
    private static function isPdfNaoPermitido(string $strArquivo): bool
    {
        if (ConfiguracaoSEI::getInstance()->getValor('XSS', 'NivelVerificacao', false, 'A') === 'N') {
            return false;
        }

        try {
            $objDocumento = (new \Smalot\PdfParser\Parser())->parseFile($strArquivo);

            foreach ($objDocumento->getObjects() as $objPdf) {
                if (self::contemAcaoAtivaPdf($objPdf->getDetails())) {
                    return true;
                }
            }

            $strTexto = $objDocumento->getText();
        } catch (Throwable $e) {
            return !self::$bolAceitarPdfNaoLido;
        }

        return self::isConteudoXss($strTexto);
    }

    /**
     * Procura chave JS ou ação S de JavaScript, Launch, SubmitForm ou ImportData no dicionário do objeto e nos
     * dicionários e listas embutidos nele. O objeto referenciado é verificado quando chega a vez dele.
     *
     * @param array<mixed> $arrDetalhes
     * @return bool
     */
    private static function contemAcaoAtivaPdf(array $arrDetalhes): bool
    {
        if (array_key_exists('JS', $arrDetalhes) || in_array($arrDetalhes['S'] ?? null, ['JavaScript', 'Launch', 'SubmitForm', 'ImportData'], true)) {
            return true;
        }

        foreach ($arrDetalhes as $valor) {
            if (is_array($valor) && self::contemAcaoAtivaPdf($valor)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Passa o conteúdo pelo SeiINT::validarXss, com o nível e as listas da ConfiguracaoSEI. Outro erro é repassado.
     *
     * @param string $strConteudo
     * @return bool
     */
    private static function isConteudoXss(string $strConteudo): bool
    {
        try {
            SeiINT::validarXss($strConteudo);
        } catch (Exception $e) {
            if (strpos($e->__toString(), SeiINT::$MSG_ERRO_XSS) === false) {
                throw $e;
            }

            return true;
        }

        return false;
    }
		
		public static function verificaSeModIAVersaoMinima()
		{
            $bolVersaoValida = false;
            $arrModulos = ConfiguracaoSEI::getInstance()->getValor('SEI','Modulos');

            if(is_array($arrModulos) && array_key_exists('IaIntegracao', $arrModulos)){
                $strVersaoIaInstalada = (new InfraParametro(BancoSEI::getInstance()))->getValor('VERSAO_MODULO_IA');
                $bolVersaoValida = version_compare($strVersaoIaInstalada, self::getIaMenorVersaoRequerida(), '>=');
            }
            return $bolVersaoValida;
		}

        public static function permitirClassificacaoODSUsuarioExterno(){

          $bolPermitir = false;

          $objMdIaAdmOdsOnuDTO = new MdIaAdmOdsOnuDTO();
          $objMdIaAdmOdsOnuDTO->setNumIdMdIaAdmOdsOnu(1);
          $objMdIaAdmOdsOnuDTO->retStrSinClassificacaoExterno();
          $objMdIaAdmOdsOnuDTO->retStrSinExibirFuncionalidade();
          $objMdIaAdmOdsOnuDTO = (new MdIaAdmOdsOnuRN())->consultar($objMdIaAdmOdsOnuDTO);

          if(!empty($objMdIaAdmOdsOnuDTO) && $objMdIaAdmOdsOnuDTO->getStrSinExibirFuncionalidade() == 'S' && $objMdIaAdmOdsOnuDTO->getStrSinClassificacaoExterno() == 'S'){
              $bolPermitir = true;
          }

          return $bolPermitir;

        }

        public static function classificarMetaOds($idProtocolo)
        {
            
            $arrMetasSelecionadas = SessaoSEIExterna::getInstance()->getAtributo('METAS_SELECIONADAS');
            $idUsuario = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();
            $staTipoUsuario = 'E';

            if(!empty($arrMetasSelecionadas)){

                foreach ($arrMetasSelecionadas as $metaSelecionada) {
                    //recupera a meta para cadastrar na classificação da meta
                    $objMdIaAdmMetaOdsDTO = new MdIaAdmMetaOdsDTO();
                    $objMdIaAdmMetaOdsDTO->setNumIdMdIaAdmMetaOds($metaSelecionada);
                    $objMdIaAdmMetaOdsDTO->retStrIdentificacaoMeta();
                    $objMdIaAdmMetaOdsDTO = (new MdIaAdmMetaOdsRN())->consultar($objMdIaAdmMetaOdsDTO);

                    MdIaAdmObjetivoOdsINT::classificarOdsWS($idProtocolo, $objMdIaAdmMetaOdsDTO->getStrIdentificacaoMeta(), $idUsuario, $staTipoUsuario );
                }

                // após realizar a classificação deve limpar os dados da sessão
                if( SessaoSEIExterna::getInstance()->isSetAtributo('arrMetasSelecionadas') ){
                    SessaoSEIExterna::getInstance()->removerAtributo('arrMetasSelecionadas');
                }

            }

        }

}

?>