<?
/**
 * ANATEL
 *
 * 
 * 22/07/2016 - criado por marcelo.bezerra@cast.com.br - CAST
 * Arquivo para realizar controle requisiчуo ajax de usuario externo no modulo peticionamento.
 */

try{
    require_once dirname(__FILE__).'/../../SEI.php';
    session_start();
    
    InfraAjax::decodificarPost();
  
 switch($_GET['acao_ajax_externo']){

	case 'md_pet_contato_pj_vinculada':

		// buscando primeira unidade para simular login para conseguir fazer a "pesquisarRN0471"
		SessaoSEIExterna::getInstance();
		$seiRN = new SeiRN();
		$objEntradaConsultarDocumentoAPI = new EntradaListarUnidadesAPI();
		$objSaidaConsultarDocumentoAPI = $seiRN->listarUnidades($objEntradaConsultarDocumentoAPI);
		SessaoSEI::getInstance()->simularLogin(null, null, SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno(), $objSaidaConsultarDocumentoAPI[0]->getIdUnidade());

		if( ($_POST['id_tipo_contexto_contato'] != null && $_POST['id_tipo_contexto_contato'] != "" )
			&&
			($_POST['palavras_pesquisa'] != null && $_POST['palavras_pesquisa'] != "")
		) {

			$objContatoRN = new ContatoRN();
			$objContextoContatoDTO = new ContatoDTO();

			$objContextoContatoDTO->retNumIdContato();
			$objContextoContatoDTO->retStrNome();

			//trazer todos que sejam empresas (CNPJ diferente de null), estejam ativos,
			//e atenda ao filtro por nome e tipo de contexto informado na tela

            $objContextoContatoDTO->setStrPalavrasPesquisa($_POST['palavras_pesquisa']);

            $objContextoContatoDTO->adicionarCriterio(
					//alteracoes seiv3
					array('SinAtivo', 'IdTipoContato'),
					array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL ),
					array('S', $_POST['id_tipo_contexto_contato'] ),
					array(InfraDTO::$OPER_LOGICO_AND )
			);

			$objContextoContatoDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);

			$arrObjContatoDTO = $objContatoRN->pesquisarRN0471( $objContextoContatoDTO );
			$xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjContatoDTO,'IdContato', 'Nome');
			InfraAjax::enviarXML($xml);

		}
		break;

	case 'md_pet_contato_auto_completar_contexto_pesquisa':

        
		//alterado para atender anatel exibir apenas nome contato
		$objContatoDTO = new ContatoDTO();
  		$objContatoDTO->retNumIdContato();
  		$objContatoDTO->retStrSigla();
  		$objContatoDTO->retStrNome();  		
  		$objContatoDTO->setStrPalavrasPesquisa($_POST['extensao']);

  		$objContatoDTO->setNumMaxRegistrosRetorno(50);
  		$objContatoDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);

        $objMdPetRelTpCtxContatoDTO = new MdPetRelTpCtxContatoDTO();
        $objMdPetTpCtxContatoRN = new MdPetTpCtxContatoRN();
        $objMdPetRelTpCtxContatoDTO->setStrSinSelecaoInteressado('S');
        $objMdPetRelTpCtxContatoDTO->setStrSinSistema('N');
        $objMdPetRelTpCtxContatoDTO->retTodos();
        $arrobjMdPetRelTpCtxContatoDTO = $objMdPetTpCtxContatoRN->listar( $objMdPetRelTpCtxContatoDTO );

        if(!empty($arrobjMdPetRelTpCtxContatoDTO)){
            
        	$arrId = array();
            
            foreach($arrobjMdPetRelTpCtxContatoDTO as $item){
                array_push($arrId, $item->getNumIdTipoContextoContato());
            }
            //alteracoes seiv3
            $objContatoDTO->adicionarCriterio(array('IdTipoContato', 'SinAtivo'),
                array(InfraDTO::$OPER_IN, InfraDTO::$OPER_IGUAL),
                array($arrId, 'S'),
            	array(InfraDTO::$OPER_LOGICO_AND));
        } else {
            $objContatoDTO->adicionarCriterio(
                array('SinAtivo'),
                array(InfraDTO::$OPER_IGUAL),
                array('S')
            );
        }

        $objMdPetContatoRN = new MdPetContatoRN();
        $arrObjContatoDTO = $objMdPetContatoRN->pesquisar($objContatoDTO);
        $xml = InfraAjax::gerarXMLItensArrInfraDTO($arrObjContatoDTO,'IdContato', 'Nome');
        InfraAjax::enviarXML($xml);
        break;

	case 'md_pet_cargo_montar_select_genero':
		// para uso com usuсrio externo - clone de controlador.ajax->cargo_montar_select_genero
		SessaoSEIExterna::getInstance();

		$strOptions = MdPetTpCtxContatoINT::montarSelectGeneroComTratamentoEVocativo($_POST['primeiroItemValor'],$_POST['primeiroItemDescricao'],$_POST['valorItemSelecionado'],$_POST['staGenero']);

		$xml = InfraAjax::gerarXMLSelect($strOptions);

		InfraAjax::enviarXML($xml);
		break;

	case 'md_pet_cargo_dados':
		// para uso com usuсrio externo - clone de controlador.ajax->cargo_dados 
		SessaoSEIExterna::getInstance();

		$objCargoDTO = new CargoDTO();
		$objCargoDTO->setBolExclusaoLogica(false);
		$objCargoDTO->retStrExpressaoTratamento();
		$objCargoDTO->retStrExpressaoVocativo();
		$objCargoDTO->setNumIdCargo($_POST['id_cargo']);

		$objCargoRN = new CargoRN();
		$objCargoDTO = $objCargoRN->consultarRN0301($objCargoDTO);

		if ($objCargoDTO!=null){
			$xml = InfraAjax::gerarXMLComplementosArrInfraDTO($objCargoDTO,array('ExpressaoTratamento','ExpressaoVocativo'));
		}

		InfraAjax::enviarXML($xml);
		break;

	default:
      throw new InfraException("Aчуo '".$_GET['acao_ajax_externo']."' nуo reconhecida pelo controlador AJAX externo.");
  }
  
}catch(Exception $e){
  InfraAjax::processarExcecao($e);
}
?>