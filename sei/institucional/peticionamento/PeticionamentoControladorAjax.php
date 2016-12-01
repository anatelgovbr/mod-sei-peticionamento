<?
	class PeticionamentoControladorAjax implements ISeiControladorAjax {

	public function processar($strAcaoAjax){
	  
		$xml = null;
		
		switch($_GET['acao_ajax']){
		
			case 'serie_peticionamento_auto_completar':
				$arrObjSerieDTO = SeriePeticionamentoINT::autoCompletarSeries( $_POST['palavras_pesquisa'] , $_POST['tipoDoc']);
				$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjSerieDTO,'IdSerie', 'Nome');
				break;
				
			case 'serie_auto_completar':
				$arrObjSerieDTO = SeriePeticionamentoINT::autoCompletarSeries( $_POST['palavras_pesquisa'] , false);
				$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjSerieDTO,'IdSerie', 'Nome');
				break;
				
			case 'tipo_processo_auto_completar':
				$arrObjTipoProcessoDTO = TipoProcedimentoINT::autoCompletarTipoProcedimento($_POST['palavras_pesquisa'] );
				$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjTipoProcessoDTO,'IdTipoProcedimento', 'Nome');
				break;
									
			case 'unidade_auto_completar':
				$arrObjUnidadeDTO = UnidadeINT::autoCompletarUnidades($_POST['palavras_pesquisa'], true, '');
				$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjUnidadeDTO,'IdUnidade', 'Sigla');
				break;
				
		   case 'nivel_acesso_auto_completar':
				$arrObjNivelAcessoDTO = TipoProcessoPeticionamentoINT::montarSelectNivelAcesso(null, null,  null, $_POST['idTipoProcesso']);
				$xml = InfraAjax::gerarXMLSelect($arrObjNivelAcessoDTO);
				//$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjUnidadeDTO,'IdUnidade', 'Sigla');
				break;
			
			case 'tipo_contexto_contato_listar':
				$arrObjTipoContextoDTO = GerirTipoContextoPeticionamentoINT::montarSelectNome(null, null, $_POST['txtPrincipal']);
				$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjTipoContextoDTO, 'IdTipoContextoContato', 'Nome');
				break;
		//EU6912
			case 'hipotese_legal_rest_peticionamento_auto_completar':
				$arrObjHipoteseLegalDTO = HipoteseLegalPeticionamentoINT::autoCompletarHipoteseLegal($_POST['palavras_pesquisa'], ProtocoloRN::$NA_RESTRITO);
				$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjHipoteseLegalDTO, 'IdHipoteseLegal', 'Nome');
				break;

			//EU3396	
			case 'arquivo_extensao_peticionamento_listar_todos':
				$arrObjArquivoExtensaoPeticionamentoDTO = ArquivoExtensaoPeticionamentoINT::autoCompletarExtensao($_POST['extensao']);
				$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjArquivoExtensaoPeticionamentoDTO,'IdArquivoExtensao', 'Extensao');
				break;
		
		}					
		
    	return $xml;
	}

}
?>