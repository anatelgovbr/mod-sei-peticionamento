<?php

require_once dirname(__FILE__).'/../../../SEI.php';

class ProcedimentoPeticionamentoRN extends ProcedimentoRN {
	
	//METODO SOBRESCRITO DA RN ORIGINAL POR CONTA DE USUARIO EXTERNO
	 public function gerarRN0156(ProcedimentoDTO $objProcedimentoDTO){

	    $bolAcumulacaoPrevia = FeedSEIProtocolos::getInstance()->isBolAcumularFeeds();
	
	    FeedSEIProtocolos::getInstance()->setBolAcumularFeeds(true);
	
	    $objProcedimentoDTO = $this->gerarRN0156Interno($objProcedimentoDTO);
	    
	    $objIndexacaoDTO = new IndexacaoDTO();
	    $objIndexacaoRN = new IndexacaoRN();
	
	    $objProtocoloDTO = new ProtocoloDTO();
	    $objProtocoloDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());
	    $objIndexacaoDTO->setArrObjProtocoloDTO(array($objProtocoloDTO));
	    $objIndexacaoDTO->setStrStaOperacao(IndexacaoRN::$TO_GERACAO_PROTOCOLO);
	    
	    $objIndexacaoRN->indexarProtocolo($objIndexacaoDTO);
	
	    if (!$bolAcumulacaoPrevia){
	      FeedSEIProtocolos::getInstance()->setBolAcumularFeeds(false);
	      FeedSEIProtocolos::getInstance()->indexarFeeds();
	    }
	
	    return $objProcedimentoDTO;
  }
  
  //METODO SOBRESCRITO DA RN ORIGINAL POR CONTA DE USUARIO EXTERNO
  protected function gerarRN0156InternoControlado(ProcedimentoDTO $objProcedimentoDTO) {

      //Valida Permissao
      //SessaoSEI::getInstance()->validarAuditarPermissao('procedimento_gerar',__METHOD__,$objProcedimentoDTO);
     try {
     	
      //Regras de Negocio
      $objInfraException = new InfraException();
      
      $this->validarNumIdTipoProcedimentoRN0204($objProcedimentoDTO, $objInfraException);
      $this->validarStrSinGerarPendenciaRN0901($objProcedimentoDTO, $objInfraException);
      $this->validarAnexosRN0751($objProcedimentoDTO, $objInfraException);
      $this->validarNivelAcesso($objProcedimentoDTO, $objInfraException); //valida somente no cadastro
      $this->validarProcessoIndividual($objProcedimentoDTO, $objInfraException);
      $objInfraException->lancarValidacoes();
  
      $objProtocoloRN = new ProtocoloPeticionamentoRN();
      
      $objProtocoloDTO = $objProcedimentoDTO->getObjProtocoloDTO();
      
      if (!$objProtocoloDTO->isSetStrProtocoloFormatado()){
        $objProtocoloDTO->setStrProtocoloFormatado(null);
      }
      
      $objProtocoloDTO->setStrStaProtocolo(ProtocoloRN::$TP_PROCEDIMENTO);
      //(TESTE COMENTADO)
      //$objProtocoloDTO->setNumIdUnidadeGeradora(SessaoSEI::getInstance()->getNumIdUnidadeAtual());      
      //$objProtocoloDTO->setNumIdUsuarioGerador(SessaoSEI::getInstance()->getNumIdUsuario());
    	
      if (!$objProtocoloDTO->isSetDtaGeracao() || InfraString::isBolVazia($objProtocoloDTO->getDtaGeracao()))
        $objProtocoloDTO->setDtaGeracao(InfraData::getStrDataAtual());
      
      $objProcedimentoDTO->setObjProtocoloDTO($objProtocoloDTO);
      //print_r( $objProcedimentoDTO->getObjProtocoloDTO() ); die();
      
      $objProtocoloDTOGerado = $objProtocoloRN->gerarRN0154($objProcedimentoDTO->getObjProtocoloDTO());
      
      $objProcedimentoDTO->setDblIdProcedimento($objProtocoloDTOGerado->getDblIdProtocolo());

      $arrObjAtributoAndamentoDTO = array();
      $objAtributoAndamentoDTO = new AtributoAndamentoDTO();
      $objAtributoAndamentoDTO->setStrNome('NIVEL_ACESSO');
      $objAtributoAndamentoDTO->setStrValor(null);
      $objAtributoAndamentoDTO->setStrIdOrigem($objProcedimentoDTO->getObjProtocoloDTO()->getStrStaNivelAcessoLocal());
      $arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;      

      if( $objProcedimentoDTO->getObjProtocoloDTO()->isSetNumIdHipoteseLegal() && !InfraString::isBolVazia($objProcedimentoDTO->getObjProtocoloDTO()->getNumIdHipoteseLegal())){
        $objAtributoAndamentoDTO = new AtributoAndamentoDTO();
        $objAtributoAndamentoDTO->setStrNome('HIPOTESE_LEGAL');
        $objAtributoAndamentoDTO->setStrValor(null);
        $objAtributoAndamentoDTO->setStrIdOrigem($objProcedimentoDTO->getObjProtocoloDTO()->getNumIdHipoteseLegal());
        $arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;
      }
      
      if (!InfraString::isBolVazia($objProcedimentoDTO->getObjProtocoloDTO()->getStrStaGrauSigilo())){
        $objAtributoAndamentoDTO = new AtributoAndamentoDTO();
        $objAtributoAndamentoDTO->setStrNome('GRAU_SIGILO');
        $objAtributoAndamentoDTO->setStrValor(null);
        $objAtributoAndamentoDTO->setStrIdOrigem($objProcedimentoDTO->getObjProtocoloDTO()->getStrStaGrauSigilo());
        $arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;
      }
      
      if ($objProcedimentoDTO->getObjProtocoloDTO()->getDtaGeracao()!=InfraData::getStrDataAtual()){
        $objAtributoAndamentoDTO = new AtributoAndamentoDTO();
        $objAtributoAndamentoDTO->setStrNome('DATA_AUTUACAO');
        $objAtributoAndamentoDTO->setStrValor($objProcedimentoDTO->getObjProtocoloDTO()->getDtaGeracao());
        $objAtributoAndamentoDTO->setStrIdOrigem(null);
        $arrObjAtributoAndamentoDTO[] = $objAtributoAndamentoDTO;
      }
            
      $objAtividadeDTO = new AtividadeDTO();
      $objAtividadeDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());      
      $objAtividadeDTO->setNumIdUnidade($objProtocoloDTO->getNumIdUnidadeGeradora());
      $objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_GERACAO_PROCEDIMENTO);
      $objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO);
      
      $numUnidadeGeradora = $objProtocoloDTO->getNumIdUnidadeGeradora();
	  $objAtividadeRN = new AtividadePeticionamentoRN();
	  
	  $param = array();
	  $param[0] = $objAtividadeDTO;
	  $param[1] = $numUnidadeGeradora;
	  
	  $ret = $objAtividadeRN->gerarInternaRN0727Customizado( $param );
 
      //Associar o processo e seus documentos com esta unidade
	  $objAssociarDTO = new AssociarDTO();	  	
	  $objAssociarDTO->setDblIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
	  $objAssociarDTO->setDblIdDocumento(null);
	  $objAssociarDTO->setNumIdUnidade($objProtocoloDTO->getNumIdUnidadeGeradora());
	  $objAssociarDTO->setNumIdUsuario($objProtocoloDTO->getNumIdUsuarioGerador());
	  $objAssociarDTO->setStrStaNivelAcessoGlobal($objProtocoloDTOGerado->getStrStaNivelAcessoGlobal());
	  $objProtocoloRN->associarRN0982($objAssociarDTO); 					  
	
	  if ($objProcedimentoDTO->getStrSinGerarPendencia()=='N'){
	    $objAtividadeRN->concluirRN0726(array($ret));
	  }
	
	  $objProcedimentoDTO->setStrStaOuvidoria(ProcedimentoRN::$TFO_NENHUM);
	  $objProcedimentoDTO->setStrSinCiencia('N');
			
      $objProcedimentoBD = new ProcedimentoBD($this->getObjInfraIBanco());
      $objProcedimentoBD->cadastrar($objProcedimentoDTO);
      
      $objProcedimentoDTO->setStrStaNivelAcessoGlobalProtocolo($objProtocoloDTOGerado->getStrStaNivelAcessoGlobal());
      $this->lancarAcessoControleInterno($objProcedimentoDTO);
      
      $objTipoProcedimentoEscolhaDTO = new TipoProcedimentoEscolhaDTO();
      $objTipoProcedimentoEscolhaDTO->setNumIdTipoProcedimento($objProcedimentoDTO->getNumIdTipoProcedimento());
      $objTipoProcedimentoEscolhaDTO->setNumIdUnidade( $objProtocoloDTO->getNumIdUnidadeGeradora()  );
      
      $objTipoProcedimentoEscolhaRN = new TipoProcedimentoEscolhaRN();
      
      if ($objTipoProcedimentoEscolhaRN->contar($objTipoProcedimentoEscolhaDTO)==0){
      	$objTipoProcedimentoEscolhaRN->cadastrar($objTipoProcedimentoEscolhaDTO);
      }

      if ($objProcedimentoDTO->isSetArrObjRelProtocoloProtocoloDTO()){

        $arrObjProtocoloProtocoloDTO = $objProcedimentoDTO->getArrObjRelProtocoloProtocoloDTO();

        foreach($arrObjProtocoloProtocoloDTO as $objRelProtocoloProtocoloDTO){
          $objRelProtocoloProtocoloDTO->setDblIdProtocolo2($objProtocoloDTOGerado->getDblIdProtocolo());
          $objRelProtocoloProtocoloDTO->setStrStaAssociacao(RelProtocoloProtocoloRN::$TA_PROCEDIMENTO_RELACIONADO);
          $this->relacionarProcedimentoRN1020($objRelProtocoloProtocoloDTO);
        }
      }
      
      $objProcedimentoDTO = new ProcedimentoDTO(true);
	  $objProcedimentoDTO->setDblIdProcedimento($objProtocoloDTOGerado->getDblIdProtocolo());
	  $objProcedimentoDTO->setStrProtocoloProcedimentoFormatado($objProtocoloDTOGerado->getStrProtocoloFormatado());
      
      return $objProcedimentoDTO;

     } catch(Exception $e){
         throw new InfraException('Erro gerando Processo.',$e);
     }
  }
  
  //MИTODOS SOBRESCRITOS PORQUE NO PAI ESTAVA COMO PRIVATE E NAO PERMITIA CHAMAR
  private function validarNumIdTipoProcedimentoRN0204(ProcedimentoDTO $objProcedimentoDTO, InfraException $objInfraException){
  	if (InfraString::isBolVazia($objProcedimentoDTO->getNumIdTipoProcedimento())){
  		$objInfraException->adicionarValidacao('Tipo do Processo nгo informado.');
  	}
  }
  
  //MИTODOS SOBRESCRITOS PORQUE NO PAI ESTAVA COMO PRIVATE E NAO PERMITIA CHAMAR
  private function validarStrSinGerarPendenciaRN0901(ProcedimentoDTO $objProcedimentoDTO, InfraException $objInfraException){
  	if (InfraString::isBolVazia($objProcedimentoDTO->getStrSinGerarPendencia())){
  		$objInfraException->adicionarValidacao('Sinalizador de geraзгo de andamento automбtico nгo informado.');
  	}else{
  		if (!InfraUtil::isBolSinalizadorValido($objProcedimentoDTO->getStrSinGerarPendencia())){
  			$objInfraException->adicionarValidacao('Sinalizador de geraзгo de andamento automбtico invбlido.');
  		}
  	}
  }
  
  //MИTODOS SOBRESCRITOS PORQUE NO PAI ESTAVA COMO PRIVATE E NAO PERMITIA CHAMAR
  private function validarStrSinCiencia(ProcedimentoDTO $objProcedimentoDTO, InfraException $objInfraException){
  	if (InfraString::isBolVazia($objProcedimentoDTO->getStrSinCiencia())){
  		$objInfraException->adicionarValidacao('Sinalizador de ciкncia nгo informado.');
  	}else{
  		if (!InfraUtil::isBolSinalizadorValido($objProcedimentoDTO->getStrSinCiencia())){
  			$objInfraException->adicionarValidacao('Sinalizador de ciкncia invбlido.');
  		}
  	}
  }
  
  //MИTODOS SOBRESCRITOS PORQUE NO PAI ESTAVA COMO PRIVATE E NAO PERMITIA CHAMAR
  private function validarAnexosRN0751(ProcedimentoDTO $objProcedimentoDTO, InfraException $objInfraException){
  
  }
  
  //MИTODOS SOBRESCRITOS PORQUE NO PAI ESTAVA COMO PRIVATE E NAO PERMITIA CHAMAR
  private function validarNivelAcesso(ProcedimentoDTO $objProcedimentoDTO, InfraException $objInfraException){
  	 
  	$objProtocoloRN = new ProtocoloPeticionamentoRN();
  	$objProtocoloRN->validarStrStaNivelAcessoLocalRN0685($objProcedimentoDTO->getObjProtocoloDTO(),$objInfraException);
  	 
  	$objNivelAcessoPermitidoDTO = new NivelAcessoPermitidoDTO();
  	$objNivelAcessoPermitidoDTO->setNumIdTipoProcedimento($objProcedimentoDTO->getNumIdTipoProcedimento());
  	$objNivelAcessoPermitidoDTO->setStrStaNivelAcesso($objProcedimentoDTO->getObjProtocoloDTO()->getStrStaNivelAcessoLocal());
  
  	$objNivelAcessoPermitidoRN = new NivelAcessoPermitidoRN();
  	if ($objNivelAcessoPermitidoRN->contar($objNivelAcessoPermitidoDTO)==0){
  		$objInfraException->adicionarValidacao('Nнvel de acesso nгo permitido para o tipo de procedimento.');
  	}
  }
  
  //MИTODOS SOBRESCRITOS PORQUE NO PAI ESTAVA COMO PRIVATE E NAO PERMITIA CHAMAR
  private function validarProcessoIndividual(ProcedimentoDTO $parObjProcedimentoDTO, InfraException $objInfraException){
  
  	$objTipoProcedimentoDTO = new TipoProcedimentoDTO();
  	$objTipoProcedimentoDTO->setBolExclusaoLogica(false);
  	$objTipoProcedimentoDTO->retStrNome();
  	$objTipoProcedimentoDTO->retStrSinIndividual();
  	$objTipoProcedimentoDTO->setNumIdTipoProcedimento($parObjProcedimentoDTO->getNumIdTipoProcedimento());
  
  	$objTipoProcedimentoRN = new TipoProcedimentoRN();
  	$objTipoProcedimentoDTO = $objTipoProcedimentoRN->consultarRN0267($objTipoProcedimentoDTO);
  
  	if ($objTipoProcedimentoDTO->getStrSinIndividual()=='S'){
  
  		if ($parObjProcedimentoDTO->isSetObjProtocoloDTO() && $parObjProcedimentoDTO->getObjProtocoloDTO()->isSetArrObjParticipanteDTO()){
  			$arrObjParticipanteDTO = $parObjProcedimentoDTO->getObjProtocoloDTO()->getArrObjParticipanteDTO();
  		}else{
  			$objParticipanteDTO = new ParticipanteDTO();
  			$objParticipanteDTO->retNumIdContato();
  			$objParticipanteDTO->setDblIdProtocolo($parObjProcedimentoDTO->getDblIdProcedimento());
  			$objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_INTERESSADO);
  
  			$objParticipanteRN = new ParticipanteRN();
  			$arrObjParticipanteDTO = $objParticipanteRN->listarRN0189($objParticipanteDTO);
  		}
  
  		$numInteressadosUsuario = 0;
  		$objParticipanteDTOUsuario = null;
  		foreach($arrObjParticipanteDTO as $objParticipanteDTO){
  			if ($objParticipanteDTO->getStrStaParticipacao()==ParticipanteRN::$TP_INTERESSADO){
  				$objParticipanteDTOUsuario = $objParticipanteDTO;
  				$numInteressadosUsuario++;
  			}
  		}
  
  		if ($numInteressadosUsuario==0){
  			$objInfraException->adicionarValidacao('Interessado nгo informado.');
  		}else if ($numInteressadosUsuario > 1){
  			$objInfraException->adicionarValidacao('Mais de um Interessado informado.');
  		}else{
  
  			//pode haver compartilhamento de contato
  			$objUsuarioDTO = new UsuarioDTO();
  			$objUsuarioDTO->setBolExclusaoLogica(false);
  			$objUsuarioDTO->setDistinct(true);
  			$objUsuarioDTO->retDblIdPessoaRh();
  			$objUsuarioDTO->retNumIdContato();
  			$objUsuarioDTO->retStrSigla();
  			$objUsuarioDTO->retStrNome();
  			$objUsuarioDTO->setNumIdContato($objParticipanteDTOUsuario->getNumIdContato());
  			$objUsuarioDTO->setOrdNumIdContato(InfraDTO::$TIPO_ORDENACAO_DESC);
  
  			$objUsuarioRN = new UsuarioRN();
  			$arrObjUsuarioDTO = $objUsuarioRN->listarRN0490($objUsuarioDTO);
  
  			if (count($arrObjUsuarioDTO)==0){
  				$objInfraException->lancarValidacao('Interessado nгo й um usuбrio.');
  			}
  
  			$arrIdPessoaRh = array();
  			foreach($arrObjUsuarioDTO as $objUsuarioDTOContato){
  				if ($objUsuarioDTOContato->getDblIdPessoaRh()!=null && !in_array($objUsuarioDTOContato->getDblIdPessoaRh(),$arrIdPessoaRh)){
  					$arrIdPessoaRh[] = $objUsuarioDTOContato->getDblIdPessoaRh();
  				}
  			}
  
  			if (count($arrIdPessoaRh)>1){
  				throw new InfraException('Usuбrio '.$arrObjUsuarioDTO[0]->getStrNome().' nгo contйm identificador do RH ъnico.');
  
  				//Um ou mais ID PESSOA RH nulo
  			}else if (count($arrIdPessoaRh) == 0){
  
  				//pega primeiro
  				$arrIdContatos = array($arrObjUsuarioDTO[0]->getNumIdContato());
  
  				//ID PESSOA RH nгo nulo
  			}else{
  
  				//busca todos os contatos com o mesmo IdPessoaRh
  				$objUsuarioDTOContatos = new UsuarioDTO();
  				$objUsuarioDTOContatos->setBolExclusaoLogica(false);
  				$objUsuarioDTOContatos->retNumIdContato();
  				$objUsuarioDTOContatos->setDblIdPessoaRh($arrIdPessoaRh[0]);
  				$arrIdContatos = InfraArray::converterArrInfraDTO($objUsuarioRN->listarRN0490($objUsuarioDTOContatos),'IdContato');
  			}
  
  			$objUsuarioDTO = $arrObjUsuarioDTO[0];
  
  			if ($parObjProcedimentoDTO->getDblIdProcedimento()!=null){
  				$objProtocoloDTO = new ProtocoloDTO();
  				$objProtocoloDTO->retNumIdOrgaoUnidadeGeradora();
  				$objProtocoloDTO->retStrSiglaOrgaoUnidadeGeradora();
  				$objProtocoloDTO->setDblIdProtocolo($parObjProcedimentoDTO->getDblIdProcedimento());
  
  				$objProtocoloRN = new ProtocoloPeticionamentoRN();
  				$objProtocoloDTO = $objProtocoloRN->consultarRN0186($objProtocoloDTO);
  
  				$numIdOrgaoProtocolo = $objProtocoloDTO->getNumIdOrgaoUnidadeGeradora();
  				$strSiglaOrgaoProtocolo = $objProtocoloDTO->getStrSiglaOrgaoUnidadeGeradora();
  			}else{
  				$numIdOrgaoProtocolo = SessaoSEI::getInstance()->getNumIdOrgaoUnidadeAtual();
  				$strSiglaOrgaoProtocolo = SessaoSEI::getInstance()->getStrSiglaOrgaoUnidadeAtual();
  			}
  
  			$objProtocoloDTO = new ProtocoloDTO();
  			$objProtocoloDTO->retDblIdProtocolo();
  			$objProtocoloDTO->retStrProtocoloFormatado();
  			$objProtocoloDTO->setNumTipoFkProcedimento(InfraDTO::$TIPO_FK_OBRIGATORIA);
  			$objProtocoloDTO->setNumIdContatoParticipante($arrIdContatos, InfraDTO::$OPER_IN);
  			$objProtocoloDTO->setStrStaParticipacaoParticipante(ParticipanteRN::$TP_INTERESSADO);
  			$objProtocoloDTO->setNumIdTipoProcedimentoProcedimento($parObjProcedimentoDTO->getNumIdTipoProcedimento());
  			$objProtocoloDTO->setDblIdProtocolo($parObjProcedimentoDTO->getDblIdProcedimento(),InfraDTO::$OPER_DIFERENTE);
  			$objProtocoloDTO->setNumIdOrgaoUnidadeGeradora($numIdOrgaoProtocolo);
  			$objProtocoloDTO->setOrdDblIdProtocolo(InfraDTO::$TIPO_ORDENACAO_DESC);
  
  			$objProtocoloRN = new ProtocoloPeticionamentoRN();
  			$arrObjProtocoloDTOTemp = $objProtocoloRN->listarRN0668($objProtocoloDTO);
  
  			$arrObjProtocoloDTO = array();
  			$objParticipanteRN = new ParticipanteRN();
  			foreach($arrObjProtocoloDTOTemp as $objProtocoloDTO){
  
  				$objParticipanteDTO = new ParticipanteDTO();
  				$objParticipanteDTO->setDblIdProtocolo($objProtocoloDTO->getDblIdProtocolo());
  				$objParticipanteDTO->setStrStaParticipacao(ParticipanteRN::$TP_INTERESSADO);
  
  				if ($objParticipanteRN->contarRN0461($objParticipanteDTO)==1){
  					$arrObjProtocoloDTO[] = $objProtocoloDTO;
  				}
  			}
  
  			$numProtocolos = count($arrObjProtocoloDTO);
  			if ($numProtocolos==1){
  				$objInfraException->adicionarValidacao('Jб existe um processo no уrgгo "'.$strSiglaOrgaoProtocolo.'" do tipo "'.$objTipoProcedimentoDTO->getStrNome().'" para o interessado "'.$objUsuarioDTO->getStrNome().'" com o nє '.$arrObjProtocoloDTO[0]->getStrProtocoloFormatado().'.');
  			}else if ($numProtocolos>1){
  				$strMsg = 'Existem '.$numProtocolos.' processos no уrgгo "'.$strSiglaOrgaoProtocolo.'" do tipo "'.$objTipoProcedimentoDTO->getStrNome().'" para o interessado "'.$objUsuarioDTO->getStrNome().'":\n';
  				foreach($arrObjProtocoloDTO as $objProtocoloDTO){
  					$strMsg .= $objProtocoloDTO->getStrProtocoloFormatado().'\n';
  				}
  				$objInfraException->adicionarValidacao($strMsg);
  			}
  		}
  	}
  }
}   
?>