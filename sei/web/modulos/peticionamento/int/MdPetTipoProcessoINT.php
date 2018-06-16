<?
/**
* ANATEL
*
* 30/03/2016 - criado por jaqueline.mendes@cast.com.br - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetTipoProcessoINT extends InfraINT {

	public static function montarSelectIndicacaoInteressadoPeticionamento($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado){
		$objMdPetTipoProcessoRN      = new MdPetTipoProcessoRN();
	
		$arrObjIndicacaoInteressadaDTO = $objMdPetTipoProcessoRN->listarValoresIndicacaoInteressado();
	
		return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjIndicacaoInteressadaDTO, 'SinIndicacao', 'Descricao');
	
	}
	
	
	public static function montarSelectTipoDocumento($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado){
		$objMdPetTipoProcessoRN      = new MdPetTipoProcessoRN();
	
		$arrObjTipoDocumentoPeticionamentDTO = $objMdPetTipoProcessoRN->listarValoresTipoDocumento();

		return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjTipoDocumentoPeticionamentDTO, 'TipoDoc', 'Descricao');
	
	}
	
	public static function montarSelectTipoProcesso($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado){
		$objTipoProcedimentoRN  = new TipoProcedimentoRN();
		
		$objTipoProcedimento      = new TipoProcedimentoDTO();
		$objTipoProcedimento->retTodos();
		//listarRN0244Conectado
		$arrObjTiposProcessoDTO = $objTipoProcedimentoRN->listarRN0244($objTipoProcedimento);
		
		return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjTiposProcessoDTO, 'IdTipoProcedimento', 'Nome');
		
	}
		
	public static function montarSelectHipoteseLegal($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado ){

		$peticionamento = false;
		$objMdPetHipoteseLegalDTO = new MdPetHipoteseLegalDTO();
		$objMdPetHipoteseLegalRN  = new MdPetHipoteseLegalRN();
		$objMdPetHipoteseLegalDTO->retTodos();
		$objMdPetHipoteseLegalDTO->setOrd('Nome', InfraDTO::$TIPO_ORDENACAO_ASC);
		$countHipotesesPeticionamento = $objMdPetHipoteseLegalRN->contar($objMdPetHipoteseLegalDTO);

        if($countHipotesesPeticionamento > 0)
        {
            $peticionamento = true;
            $objMdPetHipoteseLegalDTO->retStrNome();
            $objMdPetHipoteseLegalDTO->retStrBaseLegal();
            $arrHipoteses = $objMdPetHipoteseLegalRN->listar($objMdPetHipoteseLegalDTO);
            $stringFim = '<option value=""> </option>';
            if(count($arrHipoteses) > 0 ){

                foreach($arrHipoteses as $objHipoteseLegalDTO){

                    $idHipoteseLegal = $peticionamento ? $objHipoteseLegalDTO->getNumIdHipoteseLegalPeticionamento() : $objHipoteseLegalDTO->getNumIdHipoteseLegal();

                    if(!is_null($strValorItemSelecionado) &&  $strValorItemSelecionado == $idHipoteseLegal){
                        $stringFim .= '<option value="' . $idHipoteseLegal . '" selected="selected">' . $objHipoteseLegalDTO->getStrNome() . ' (' . $objHipoteseLegalDTO->getStrBaseLegal() .')';
                    } else {
                        $stringFim .= '<option value="' . $idHipoteseLegal . '">' . $objHipoteseLegalDTO->getStrNome() . ' (' . $objHipoteseLegalDTO->getStrBaseLegal() .  ')';
                    }
                    $stringFim .= '</option>';

                }
            }
        }
        else
        {
            $objEntradaListarHipotesesLegaisAPI = new EntradaListarHipotesesLegaisAPI();
            $objEntradaListarHipotesesLegaisAPI->setNivelAcesso(ProtocoloRN::$NA_RESTRITO);

            $objSeiRN = new SeiRN();
            $arrHipoteseLegalAPI = $objSeiRN->listarHipotesesLegais($objEntradaListarHipotesesLegaisAPI);

            $stringFim = '<option value=""> </option>';
            if(count($arrHipoteseLegalAPI) > 0 ){
                foreach($arrHipoteseLegalAPI as $hipoteseLegalAPI){

                    $idHipoteseLegal = $hipoteseLegalAPI->getIdHipoteseLegal();

                    if(!is_null($strValorItemSelecionado) &&  $strValorItemSelecionado == $idHipoteseLegal){
                        $stringFim .= '<option value="' . $idHipoteseLegal . '" selected="selected">' . $hipoteseLegalAPI->getNome() . ' (' . $hipoteseLegalAPI->getBaseLegal() .')';
                    } else {
                        $stringFim .= '<option value="' . $idHipoteseLegal . '">' . $hipoteseLegalAPI->getNome() . ' (' . $hipoteseLegalAPI->getBaseLegal() .  ')';
                    }
                    $stringFim .= '</option>';

                }
            }
        }
		
		return $stringFim;
	}

	public static function validarNivelAcesso($params)
    {
        $objNivelAcessoRN  = new NivelAcessoPermitidoRN();
        $objNivelAcessoDTO = new NivelAcessoPermitidoDTO();
        $objNivelAcessoDTO->retTodos();
        $objNivelAcessoDTO->setOrd('StaNivelAcesso', InfraDTO::$TIPO_ORDENACAO_ASC);
        $arrayDescricoes = array(
            'P' => 'Público',
            'I' => 'Restrito'
        );
        $msg = '';
        $xml = '<Validacao>';
        $staTipoNivelAcesso = 1;
        if ($params['selNivelAcesso'] == 'P') {
            $staTipoNivelAcesso = 0;
        }
        if($params['hdnIdTipoProcesso'] != '' ){
            $arrTipoProcedimento = PaginaSEI::getInstance()->getArrItensTabelaDinamica($params['hdnIdTipoProcesso']);
            foreach($arrTipoProcedimento as $tipoProcedimento) {
                $objNivelAcessoDTO->setNumIdTipoProcedimento($tipoProcedimento[0]);
                $objNivelAcessoDTO->setStrStaNivelAcesso($staTipoNivelAcesso);
                $contador = $objNivelAcessoRN->contar($objNivelAcessoDTO);
                if($contador <= 0){
                    $msg .= $tipoProcedimento[1] . "\r\n";
                }
            }
        } else {
            $objNivelAcessoDTO->setNumIdTipoProcedimento($params['selTipoProcesso']);
            $objNivelAcessoDTO->setStrStaNivelAcesso($staTipoNivelAcesso);
            $contador = $objNivelAcessoRN->contar($objNivelAcessoDTO);
            if($contador <= 0){
                $objTipoProcedimentoConsultaDTO = new TipoProcedimentoDTO();
                $objTipoProcedimentoConsultaDTO->setNumIdTipoProcedimento($params['selTipoProcesso']);
                $objTipoProcedimentoConsultaDTO->retTodos();
                $objTipoProcedimentoRN = new TipoProcedimentoRN();
                $objTipoProcedimentoDTO = $objTipoProcedimentoRN->consultarRN0267($objTipoProcedimentoConsultaDTO);
                $msg .= $objTipoProcedimentoDTO->getStrNome() . "\r\n";
            }
        }

        if($msg != ''){
            $msg = 'O Critério não pôde ser cadastrado, pois os Tipo de Processo abaixo não permite o Nível de Acesso [ ' . $arrayDescricoes[$params['selNivelAcesso']] . " ]\n\r" . $msg;
            $xml .= '<MensagemValidacao>' . $msg . '</MensagemValidacao>';
        }

        $xml .= '</Validacao>';
        return $xml;
    }
	
	public static function montarSelectNivelAcesso($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $idTipoProcedimento = null){
		$objNivelAcessoRN  = new NivelAcessoPermitidoRN();
	
		$objNivelAcessoDTO = new NivelAcessoPermitidoDTO();
		$objNivelAcessoDTO->retTodos();
		$objNivelAcessoDTO->setOrd('StaNivelAcesso', InfraDTO::$TIPO_ORDENACAO_ASC);

        if($idTipoProcedimento != null ){
            if(!is_int($idTipoProcedimento)){
                $arrTipoProcedimento = PaginaSEI::getInstance()->getArrItensTabelaDinamica($idTipoProcedimento);
                $arrIdTipoProcedimento = array();
                foreach($arrTipoProcedimento as $tipoProcedimento) {
                    $arrIdTipoProcedimento[] = $tipoProcedimento[0];
                }

                $objNivelAcessoDTO->adicionarCriterio(array('IdTipoProcedimento'), array( InfraDTO::$OPER_IN), array($arrIdTipoProcedimento));
                $objNivelAcessoDTO->setDistinct(true);
            } else {
                $objNivelAcessoDTO->setNumIdTipoProcedimento($idTipoProcedimento);
            }
        }

		$arrObjNivelAcessoDTO = $objNivelAcessoRN->listar($objNivelAcessoDTO);

        // removendo as duplicidades na colecao de objetos
        $arrObjNivelAcessoUnicoDTO = array();
        foreach($arrObjNivelAcessoDTO  as $objNivelAcessoDTO){
            $arrObjNivelAcessoUnicoDTO[$objNivelAcessoDTO->getStrStaNivelAcesso()] = $objNivelAcessoDTO;
        }

		//montarItemSelect
		$stringFim = '';
		$arrayDescricoes = array();
		$arrayDescricoes[ProtocoloRN::$NA_PUBLICO] = 'Público';
		$arrayDescricoes[ProtocoloRN::$NA_RESTRITO] = 'Restrito';
		$arrayDescricoes[''] = '';
		
		$stringFim = '<option value=""> </option>';
		
		if(count($arrObjNivelAcessoUnicoDTO) > 0 ){
			foreach($arrObjNivelAcessoUnicoDTO as $objNivelAcessoDTO){
			  
			  if( $objNivelAcessoDTO->getStrStaNivelAcesso() != ProtocoloRN::$NA_SIGILOSO ){	
			  	
				  $stringFim .= '<option value="'.$objNivelAcessoDTO->getStrStaNivelAcesso().'"';
				  
				  if(!is_null($strValorItemSelecionado) &&  ($strValorItemSelecionado == $objNivelAcessoDTO->getStrStaNivelAcesso())){
				  	$stringFim .= 'selected = selected';
				  }
				  
				  $stringFim .= '>';
				  $stringFim .= $arrayDescricoes[$objNivelAcessoDTO->getStrStaNivelAcesso()];
				  
				  $stringFim .= '</option>';
			  }
			  
			}
		}
	
		return $stringFim;
	}

	public static function validarTipoProcessoComAssunto($params){
        $msg = '';
        $xml = '<Validacao>';
        $relTipoProcedimentoDTO = new RelTipoProcedimentoAssuntoDTO();
        $relTipoProcedimentoDTO->retTodos();
        $relTipoProcedimentoRN = new RelTipoProcedimentoAssuntoRN();

        if($params['hdnIdTipoProcesso'] != '' ){
            $arrTipoProcedimento = PaginaSEI::getInstance()->getArrItensTabelaDinamica($params['hdnIdTipoProcesso']);
            foreach($arrTipoProcedimento as $tipoProcedimento) {
                $relTipoProcedimentoDTO->setNumIdTipoProcedimento($tipoProcedimento[0]);
                $arrLista = $relTipoProcedimentoRN->listarRN0192( $relTipoProcedimentoDTO );

                if( !is_array( $arrLista ) || count( $arrLista ) == 0 ){
                    $objTipoProcedimentoConsultaDTO = new TipoProcedimentoDTO();
                    $objTipoProcedimentoConsultaDTO->setNumIdTipoProcedimento($tipoProcedimento[0]);
                    $objTipoProcedimentoConsultaDTO->retTodos();
                    $objTipoProcedimentoRN = new TipoProcedimentoRN();
                    $objTipoProcedimentoDTO = $objTipoProcedimentoRN->consultarRN0267($objTipoProcedimentoConsultaDTO);
                    $msg .= $objTipoProcedimentoDTO->getStrNome() . "\r\n";
                }
            }
        } else {
            $relTipoProcedimentoDTO->setNumIdTipoProcedimento($params['selTipoProcesso']);
            $arrLista = $relTipoProcedimentoRN->listarRN0192( $relTipoProcedimentoDTO );
            if( !is_array( $arrLista ) || count( $arrLista ) == 0 ){
                $objTipoProcedimentoConsultaDTO = new TipoProcedimentoDTO();
                $objTipoProcedimentoConsultaDTO->setNumIdTipoProcedimento($params['selTipoProcesso']);
                $objTipoProcedimentoConsultaDTO->retTodos();
                $objTipoProcedimentoRN = new TipoProcedimentoRN();
                $objTipoProcedimentoDTO = $objTipoProcedimentoRN->consultarRN0267($objTipoProcedimentoConsultaDTO);
                $msg .= $objTipoProcedimentoDTO->getStrNome() . "\r\n";
            }
        }

        if($msg != ''){
            $msg = 'O Critério não pôde ser cadastrado, pois existe Tipo de Processo que não possue indicação de pelo menos uma sugestão de assunto' . "\n\r" . $msg;
            $xml .= '<MensagemValidacao>' . $msg . '</MensagemValidacao>';
        }

        $xml .= '</Validacao>';
        return $xml;
    }

    public static function autoCompletarTipoProcedimento($strPalavrasPesquisa, $itensSelecionados = null){
		$objTipoProcedimentoDTO = new TipoProcedimentoDTO();
		$objTipoProcedimentoDTO->retNumIdTipoProcedimento();
		$objTipoProcedimentoDTO->retStrNome();
		$objTipoProcedimentoDTO->setOrd('Nome', InfraDTO::$TIPO_ORDENACAO_ASC);
		
		$objTipoProcedimentoRN = new TipoProcedimentoRN();
		$arrObjTipoProcedimentoDTO = $objTipoProcedimentoRN->listarRN0244($objTipoProcedimentoDTO);


        if ($strPalavrasPesquisa != '' || $itensSelecionados != null) {
            $ret = array();
            $strPalavrasPesquisa = strtolower($strPalavrasPesquisa);
            foreach($arrObjTipoProcedimentoDTO as $objTipoProcedimentoDTO){
                if($itensSelecionados != null && in_array($objTipoProcedimentoDTO->getNumIdTipoProcedimento(), $itensSelecionados)){
                	continue;
                }
                if ($strPalavrasPesquisa != '' && strpos(strtolower($objTipoProcedimentoDTO->getStrNome()),$strPalavrasPesquisa)===false){
                	continue;
                }

                //checando se o tipo de processo informado possui sugestao de assunto
                
                $rnAssunto = new RelTipoProcedimentoAssuntoRN();
                $dto = new RelTipoProcedimentoAssuntoDTO();
                $dto->retTodos();
                $dto->setNumIdTipoProcedimento( $objTipoProcedimentoDTO->getNumIdTipoProcedimento() );

                $arrAssuntos = $rnAssunto->listarRN0192( $dto );
                
                if( is_array( $arrAssuntos ) && count( $arrAssuntos ) > 0 ){
                   $ret[] = $objTipoProcedimentoDTO;
                }
            }
        }
        return $ret;
    }

    public static function gerarXMLItensArrInfraApi($arr, $strAtributoId, $strAtributoDescricao, $strAtributoComplemento=null, $strAtributoGrupo=null){
        $metodoAtributoId = "get{$strAtributoId}";
        $metodoAtributoDescricao = "get{$strAtributoDescricao}";
        $metodoAtributoComplemento = "get{$strAtributoComplemento}";
        $metodoAtributoGrupo = "get{$strAtributoGrupo}";

        $xml = '';
        $xml .= '<itens>';
        if ($arr !== null ){
            foreach($arr as $dto){
                $xml .= '<item id="'.self::formatarXMLAjax($dto->$metodoAtributoId()).'"';
                $xml .= ' descricao="'.self::formatarXMLAjax($dto->$metodoAtributoDescricao()).'"';

                if ($strAtributoComplemento!==null){
                    $xml .= ' complemento="'.self::formatarXMLAjax($dto->$metodoAtributoComplemento()).'"';
                }

                if ($strAtributoGrupo!==null){
                    $xml .= ' grupo="'.self::formatarXMLAjax($dto->$metodoAtributoGrupo()).'"';
                }

                $xml .= '></item>';
            }
        }
        $xml .= '</itens>';
        return $xml;
    }

    private static function formatarXMLAjax($str){
        if (!is_numeric($str)){
            $str = str_replace('&','&amp;',$str);
            $str = str_replace('<','&amp;lt;',$str);
            $str = str_replace('>','&amp;gt;',$str);
            $str = str_replace('\"','&amp;quot;',$str);
            $str = str_replace('"','&amp;quot;',$str);
            //$str = str_replace("\n",'_',$str);
        }
        return $str;
    }

}