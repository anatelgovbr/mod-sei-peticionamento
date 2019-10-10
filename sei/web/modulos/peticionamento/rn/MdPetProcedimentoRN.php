<?php
/**
 * ANATEL
 *
 * 21/07/2016 - criado por marcelo.bezerra - CAST
 *
 */
require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetProcedimentoRN extends ProcedimentoRN {
	
	//METODO SOBRESCRITO DA RN ORIGINAL POR CONTA DE USUARIO EXTERNO
	 public function gerarRN0156(ProcedimentoDTO $objProcedimentoDTO){

	    $bolAcumulacaoPrevia = FeedSEIProtocolos::getInstance()->isBolAcumularFeeds();
	
	    FeedSEIProtocolos::getInstance()->setBolAcumularFeeds(true);	
	    $objProcedimentoDTO = $this->gerarRN0156Interno($objProcedimentoDTO);	    
	    $objIndexacaoDTO = new IndexacaoDTO();
	    $objIndexacaoRN = new IndexacaoRN();
	
	    $objProtocoloDTO = new ProtocoloDTO();
	    $objProtocoloDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());
	    $objIndexacaoDTO->setArrIdProtocolos(array($objProtocoloDTO->getDblIdProtocolo()));

	    //alteracoes seiv3
	    $objIndexacaoDTO->setStrStaOperacao(IndexacaoRN::$TO_PROTOCOLO_METADADOS);

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
  
      $objProtocoloRN = new MdPetProtocoloRN();
      
      $objProtocoloDTO = $objProcedimentoDTO->getObjProtocoloDTO();
      
      if (!$objProtocoloDTO->isSetStrProtocoloFormatado()){
        $objProtocoloDTO->setStrProtocoloFormatado(null);
      }
      
      $objProtocoloDTO->setStrStaProtocolo(ProtocoloRN::$TP_PROCEDIMENTO);
      
      if (!$objProtocoloDTO->isSetDtaGeracao() || InfraString::isBolVazia($objProtocoloDTO->getDtaGeracao()))
        $objProtocoloDTO->setDtaGeracao(InfraData::getStrDataAtual());
      
      $objProcedimentoDTO->setObjProtocoloDTO($objProtocoloDTO);
      
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
	  $objAtividadeRN = new MdPetAtividadeRN();
	  
	  $param = array();
	  $param[0] = $objAtividadeDTO;
	  $param[1] = $numUnidadeGeradora;
	  
	  $ret = $objAtividadeRN->gerarInternaRN0727Customizado( $param );
 
      //Associar o processo e seus documentos com esta unidade
	  $objAssociarDTO = new AssociarDTO();	  	
	  $objAssociarDTO->setDblIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());

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
  
  //MÈTODOS SOBRESCRITOS PORQUE NO PAI ESTAVA COMO PRIVATE E NAO PERMITIA CHAMAR
  private function validarNumIdTipoProcedimentoRN0204(ProcedimentoDTO $objProcedimentoDTO, InfraException $objInfraException){
  	if (InfraString::isBolVazia($objProcedimentoDTO->getNumIdTipoProcedimento())){
  		$objInfraException->adicionarValidacao('Tipo do Processo não informado.');
  	}
  }
  
  //MÈTODOS SOBRESCRITOS PORQUE NO PAI ESTAVA COMO PRIVATE E NAO PERMITIA CHAMAR
  private function validarStrSinGerarPendenciaRN0901(ProcedimentoDTO $objProcedimentoDTO, InfraException $objInfraException){
  	if (InfraString::isBolVazia($objProcedimentoDTO->getStrSinGerarPendencia())){
  		$objInfraException->adicionarValidacao('Sinalizador de geração de andamento automático não informado.');
  	}else{
  		if (!InfraUtil::isBolSinalizadorValido($objProcedimentoDTO->getStrSinGerarPendencia())){
  			$objInfraException->adicionarValidacao('Sinalizador de geração de andamento automático inválido.');
  		}
  	}
  }
  
  //MÈTODOS SOBRESCRITOS PORQUE NO PAI ESTAVA COMO PRIVATE E NAO PERMITIA CHAMAR
  private function validarStrSinCiencia(ProcedimentoDTO $objProcedimentoDTO, InfraException $objInfraException){
  	if (InfraString::isBolVazia($objProcedimentoDTO->getStrSinCiencia())){
  		$objInfraException->adicionarValidacao('Sinalizador de ciência não informado.');
  	}else{
  		if (!InfraUtil::isBolSinalizadorValido($objProcedimentoDTO->getStrSinCiencia())){
  			$objInfraException->adicionarValidacao('Sinalizador de ciência inválido.');
  		}
  	}
  }
  
  //MÈTODOS SOBRESCRITOS PORQUE NO PAI ESTAVA COMO PRIVATE E NAO PERMITIA CHAMAR
  private function validarAnexosRN0751(ProcedimentoDTO $objProcedimentoDTO, InfraException $objInfraException){
  
  }
  
  //MÈTODOS SOBRESCRITOS PORQUE NO PAI ESTAVA COMO PRIVATE E NAO PERMITIA CHAMAR
  private function validarNivelAcesso(ProcedimentoDTO $objProcedimentoDTO, InfraException $objInfraException){
  	 
  	$objMdPetProtocoloRN = new MdPetProtocoloRN();
  	$objMdPetProtocoloRN->validarStrStaNivelAcessoLocalRN0685($objProcedimentoDTO->getObjProtocoloDTO(),$objInfraException);
  	 
  	$objNivelAcessoPermitidoDTO = new NivelAcessoPermitidoDTO();
  	$objNivelAcessoPermitidoDTO->setNumIdTipoProcedimento($objProcedimentoDTO->getNumIdTipoProcedimento());
  	$objNivelAcessoPermitidoDTO->setStrStaNivelAcesso($objProcedimentoDTO->getObjProtocoloDTO()->getStrStaNivelAcessoLocal());
  
  	$objNivelAcessoPermitidoRN = new NivelAcessoPermitidoRN();
  	if ($objNivelAcessoPermitidoRN->contar($objNivelAcessoPermitidoDTO)==0){
  		$objInfraException->adicionarValidacao('Nível de acesso não permitido para o tipo de procedimento.');
  	}
  }
  
  //MÈTODOS SOBRESCRITOS PORQUE NO PAI ESTAVA COMO PRIVATE E NAO PERMITIA CHAMAR
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
  			$objInfraException->adicionarValidacao('Interessado não informado.');
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
  				$objInfraException->lancarValidacao('Interessado não é um usuário.');
  			}
  
  			$arrIdPessoaRh = array();
  			foreach($arrObjUsuarioDTO as $objUsuarioDTOContato){
  				if ($objUsuarioDTOContato->getDblIdPessoaRh()!=null && !in_array($objUsuarioDTOContato->getDblIdPessoaRh(),$arrIdPessoaRh)){
  					$arrIdPessoaRh[] = $objUsuarioDTOContato->getDblIdPessoaRh();
  				}
  			}
  
  			if (count($arrIdPessoaRh)>1){
  				throw new InfraException('Usuário '.$arrObjUsuarioDTO[0]->getStrNome().' não contém identificador do RH único.');
  
  				//Um ou mais ID PESSOA RH nulo
  			}else if (count($arrIdPessoaRh) == 0){
  
  				//pega primeiro
  				$arrIdContatos = array($arrObjUsuarioDTO[0]->getNumIdContato());
  
  				//ID PESSOA RH não nulo
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
  
  				$objProtocoloRN = new MdPetProtocoloRN();
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
  
  			$objProtocoloRN = new MdPetProtocoloRN();
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
  				$objInfraException->adicionarValidacao('Já existe um processo no órgão "'.$strSiglaOrgaoProtocolo.'" do tipo "'.$objTipoProcedimentoDTO->getStrNome().'" para o interessado "'.$objUsuarioDTO->getStrNome().'" com o nº '.$arrObjProtocoloDTO[0]->getStrProtocoloFormatado().'.');
  			}else if ($numProtocolos>1){
  				$strMsg = 'Existem '.$numProtocolos.' processos no órgão "'.$strSiglaOrgaoProtocolo.'" do tipo "'.$objTipoProcedimentoDTO->getStrNome().'" para o interessado "'.$objUsuarioDTO->getStrNome().'":\n';
  				foreach($arrObjProtocoloDTO as $objProtocoloDTO){
  					$strMsg .= $objProtocoloDTO->getStrProtocoloFormatado().'\n';
  				}
  				$objInfraException->adicionarValidacao($strMsg);
  			}
  		}
  	}
  }
  
  //alteracoes seiv3
  //metodo foi removido, esta é uma versao migrada do seiv2
  //inicio
  protected function lancarAcessoControleInternoControlado(ProcedimentoDTO $objProcedimentoDTO){
    try{

      if ($objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo()!=ProtocoloRN::$NA_SIGILOSO){

        $objControleInternoDTO = new ControleInternoDTO();
        $objControleInternoDTO->setDistinct(true);

        //alteracoes seiv3
        $objControleInternoDTO->retNumIdUnidadeControle();
        $objControleInternoDTO->setNumIdTipoProcedimentoControlado( $objProcedimentoDTO->getNumIdTipoProcedimento() );
        $objControleInternoDTO->setNumIdOrgaoControlado(SessaoSEI::getInstance()->getNumIdOrgaoUnidadeAtual());
        $objControleInternoDTO->setNumIdUnidadeControle(SessaoSEI::getInstance()->getNumIdUnidadeAtual(),InfraDTO::$OPER_DIFERENTE);

        $objControleInternoRN = new ControleInternoRN();
        $arrObjControleInternoDTO = $objControleInternoRN->listar($objControleInternoDTO);
        
        $objProtocoloRN = new ProtocoloRN();

        foreach($arrObjControleInternoDTO as $objControleInternoDTO){

          $objAtividadeDTO = new AtividadeDTO();
          $objAtividadeDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());
          $objAtividadeDTO->setNumIdUnidade($objControleInternoDTO->getNumIdUnidadeRelControleInternoUnidade());

          $objAtividadeRN = new AtividadeRN();

          if ($objAtividadeRN->contarRN0035($objAtividadeDTO)==0){

            $objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_ACESSO_AUTOMATICO_AO_PROCESSO);

            $objAtividadeRN->gerarInternaRN0727($objAtividadeDTO);


            //Associar o processo e seus documentos com esta unidade
            $objAssociarDTO = new AssociarDTO();
            $objAssociarDTO->setDblIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
            $objAssociarDTO->setDblIdDocumento(null);
            $objAssociarDTO->setNumIdUnidade($objControleInternoDTO->getNumIdUnidadeRelControleInternoUnidade());
            $objAssociarDTO->setNumIdUsuario(null);
            $objAssociarDTO->setStrStaNivelAcessoGlobal($objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo());

            $objProtocoloRN->associarRN0982($objAssociarDTO);

          }
        }
      }
    }catch(Exception $e){
      throw new InfraException('Erro lançando acesso para o Controle Interno.',$e);
    }
  }
  //fim

  public function concederCredencialControlado($params) {

    try {
        $objProcedimentoDTO     = $params[0];
        $idUnidadeProcesso      = $params[1];
        $numIdUsuarioCredencial = isset($params[4]) ? $params[4] : SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();

        if ($objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo() == ProtocoloRN::$NA_SIGILOSO
            || $objProcedimentoDTO->getStrStaNivelAcessoLocalProtocolo() == ProtocoloRN::$NA_SIGILOSO){

            $objMdPetIntimacaoRN = new MdPetIntimacaoRN;
            $arrAtividadeDTO = $objMdPetIntimacaoRN->verificarUnidadeAberta( array($objProcedimentoDTO, $idUnidadeProcesso) );

            if (count($arrAtividadeDTO)>0){
                $numIdUsuarioAcesso  = $arrAtividadeDTO[0]->getNumIdUsuario();
                $numIdUnidadeAcesso  = $arrAtividadeDTO[0]->getNumIdUnidade();

                // Verificar se a credencial já existe
                $arrObjAtividadeDTO = $this->listarCredenciais( array($objProcedimentoDTO->getDblIdProcedimento(), null, $numIdUnidadeAcesso, $numIdUsuarioCredencial, TarefaRN::getArrTarefasConcessaoCredencial(false)) );

                if (count($arrObjAtividadeDTO)>0){
                    for ($i=0; $i<count($arrObjAtividadeDTO); $i++) {

                        $numIdUsuarioAcesso  = $arrObjAtividadeDTO[$i]->getNumIdUsuarioOrigem();

                        $retorno = array();
                        $retorno[0] = $params[0];
                        $retorno[1] = $params[1];
                        $retorno[2] = $arrObjAtividadeDTO[$i];
                        $retorno[3] = $idUnidade;
                        $retorno[4] = $numIdUsuarioCredencial;
                        $retorno[5] = $numIdUsuarioAcesso;
                        $retorno[6] = $numIdUnidadeAcesso;
                        $retorno[7] = $numIdUsuarioAntesSigiloso;
                        $retorno[8] = $numIdUnidadeAtualAntesSigiloso;
                        return $retorno;
                    }
                }

                // Usuário antes de tratamento de SIGILOSO
                if ( is_numeric(SessaoSEI::getInstance()->getNumIdUsuario()) && is_numeric(SessaoSEI::getInstance()->getNumIdUnidadeAtual()) ){
                    $numIdUsuarioAntesSigiloso = SessaoSEI::getInstance()->getNumIdUsuario();
                    $numIdUnidadeAtualAntesSigiloso = SessaoSEI::getInstance()->getNumIdUnidadeAtual();
                }

                //Simulando para usuário que tem credencial
                SessaoSEI::getInstance()->simularLogin(null, null, $numIdUsuarioAcesso, $numIdUnidadeAcesso);

                $objAtividadeRN = new AtividadeRN();
                $objPesquisaPendenciaDTO = new PesquisaPendenciaDTO();
                $objPesquisaPendenciaDTO->setDblIdProtocolo($objProcedimentoDTO->getDblIdProcedimento());
                $objPesquisaPendenciaDTO->setNumIdUsuario($numIdUsuarioAcesso);
                $objPesquisaPendenciaDTO->setNumIdUnidade($numIdUnidadeAcesso);
                $arrObjProcedimentoDTO = $objAtividadeRN->listarPendenciasRN0754($objPesquisaPendenciaDTO);

                if (count($arrObjProcedimentoDTO)==0){
                    // Usuário antes de tratamento de SIGILOSO - RETORNANDO
                    if ( is_numeric($numIdUsuarioAntesSigiloso) && is_numeric($numIdUnidadeAtualAntesSigiloso) ){
                        SessaoSEI::getInstance()->simularLogin(null, null, $numIdUsuarioAntesSigiloso, $numIdUnidadeAtualAntesSigiloso);
                    }else{
                        // destruir sessão
                    }

                    throw new InfraException('concederCredencial - Processo não encontrado.');
                }

                $arrAtividadesOrigem = InfraArray::converterArrInfraDTO($arrObjProcedimentoDTO[0]->getArrObjAtividadeDTO(),'IdAtividade');

                $objConcederCredencialDTO = new ConcederCredencialDTO();
                $objConcederCredencialDTO->setDblIdProcedimento($objProcedimentoDTO->getDblIdProcedimento());
                $objConcederCredencialDTO->setNumIdUsuario($numIdUsuarioCredencial);
                $objConcederCredencialDTO->setNumIdUnidade($numIdUnidadeAcesso);

                $objConcederCredencialDTO->setArrAtividadesOrigem(InfraArray::gerarArrInfraDTO('AtividadeDTO','IdAtividade',$arrAtividadesOrigem));

                // atribuindo credencial provisória
                $objAtividadeRN = new AtividadeRN();
                $objConcederCredencial = $objAtividadeRN->concederCredencial($objConcederCredencialDTO);

                // Usuário antes de tratamento de SIGILOSO - RETORNANDO
                if ( is_numeric($numIdUsuarioAntesSigiloso) && is_numeric($numIdUnidadeAtualAntesSigiloso) ){
                    SessaoSEI::getInstance()->simularLogin(null, null, $numIdUsuarioAntesSigiloso, $numIdUnidadeAtualAntesSigiloso);
                }else{
                    // destruir sessão
                }

                $retorno = array();
                $retorno[0] = $params[0];
                $retorno[1] = $params[1];
                $retorno[2] = $objConcederCredencial;
                $retorno[3] = $idUnidade; 
                $retorno[4] = $numIdUsuarioCredencial;
                $retorno[5] = $numIdUsuarioAcesso;
                $retorno[6] = $numIdUnidadeAcesso;
                $retorno[7] = $numIdUsuarioAntesSigiloso;
                $retorno[8] = $numIdUnidadeAtualAntesSigiloso;

                return $retorno;
            }

        }

    }catch(Exception $e){
      throw new InfraException('Erro concederCredencial.',$e);
    }
  }
  
  public function cassarCredencialControlado($params) {
    try {
        $objProcedimentoDTO             = $params[0];
        $idUnidadeProcesso              = $params[1];
        $objConcederCredencial          = $params[2];

        if ($objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo() == ProtocoloRN::$NA_SIGILOSO
            || $objProcedimentoDTO->getStrStaNivelAcessoLocalProtocolo() == ProtocoloRN::$NA_SIGILOSO){

            $objAtividadeDTO = new AtividadeDTO();
            $objAtividadeDTO->retTodos();
            $objAtividadeDTO->setNumIdAtividade($objConcederCredencial->getNumIdAtividade());

            $objAtividadeRN = new AtividadeRN();
            $arrObjAtividadeDTO = $objAtividadeRN->listarRN0036($objAtividadeDTO);
            if (count($arrObjAtividadeDTO)){
                $numIdUsuarioExterno = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();
                $numIdUsuarioAcesso  = $arrObjAtividadeDTO[0]->getNumIdUsuarioOrigem();
                $numIdUnidadeAcesso  = $arrObjAtividadeDTO[0]->getNumIdUnidade();
            }

            // Usuário antes de tratamento de SIGILOSO
            if ( is_numeric(SessaoSEI::getInstance()->getNumIdUsuario()) && is_numeric(SessaoSEI::getInstance()->getNumIdUnidadeAtual()) ){
                $numIdUsuarioAntesSigiloso = SessaoSEI::getInstance()->getNumIdUsuario();
                $numIdUnidadeAtualAntesSigiloso = SessaoSEI::getInstance()->getNumIdUnidadeAtual();
            }
            SessaoSEI::getInstance()->simularLogin(null, null, $numIdUsuarioAcesso, $numIdUnidadeAcesso);
            $arrObjAtividadeDTO = InfraArray::gerarArrInfraDTO('AtividadeDTO','IdAtividade',array($objConcederCredencial->getNumIdAtividade()));

            //guardando credencial sem recebido, exceto aquela a ser cassada
            $objAtividadeDTO = new AtividadeDTO();
            $objAtividadeDTO->retNumIdAtividade();
            $objAtividadeDTO->setDblIdProtocolo($objConcederCredencial->getDblIdProtocolo());
            $objAtividadeDTO->setNumIdTarefa(TarefaRN::$TI_PROCESSO_CONCESSAO_CREDENCIAL);
            $objAtividadeDTO->setNumIdAtividade($objConcederCredencial->getNumIdAtividade(), InfraDTO::$OPER_DIFERENTE);
            $objAtividadeRN = new AtividadeRN();
            $arrObjAtividadeConcessaoDTO = $objAtividadeRN->listarRN0036($objAtividadeDTO);

            //cassando credencial usuário externo
            $objAtividadeRN = new AtividadeRN();
            $objAtividadeRN->cassarCredenciais($arrObjAtividadeDTO);
            //$objAtividadeRN->anularCredenciaisProcesso($ret);
            //$objAtividadeRN->renunciarCredenciaisProcesso($objAtividadeDTO);

            //retornando credencial sem recebido
            if (count($arrObjAtividadeConcessaoDTO)>0){
                foreach ($arrObjAtividadeConcessaoDTO as $itemDTO) {
                    $itemDTO->setDthConclusao(null);
                    $objMdPetAtividadeRN = new MdPetAtividadeRN();
                    $objMdPetAtividadeRN->alterar($itemDTO);
                }
            }

            // Usuário antes de tratamento de SIGILOSO - RETORNANDO
            if ($numIdUnidadeAtualAntesSigiloso!='' && $numIdUsuarioAntesSigiloso!=''){
                SessaoSEI::getInstance()->simularLogin(null, null, $numIdUsuarioAntesSigiloso, $numIdUnidadeAtualAntesSigiloso);
            //}else{
				// destruir sessão
            }

            $retorno = array();
            $retorno[0] = $objProcedimentoDTO;
            $retorno[1] = $idUnidadeProcesso;
            $retorno[2] = $objConcederCredencial;
            $retorno[3] = $idUnidade;

        }

    }catch(Exception $e){
      throw new InfraException('Erro cassarCredencial.',$e);
    }
  }

  // baseada na: AtividadeRN->listarCredenciaisConectado
  public function listarCredenciaisConectado($param) {
    try{

      $idProcedimento      = isset($param[0]) ? $param[0] : null;
      $numIdUsuario        = isset($param[1]) ? $param[1] : null;
      $numIdUnidadeAtual   = isset($param[2]) ? $param[2] : SessaoSEI::getInstance()->getNumIdUnidadeAtual();
      $numIdUsuarioDestino = isset($param[3]) ? $param[3] : null;
      $arrTarefas          = isset($param[4]) ? $param[4] : array_merge(TarefaRN::getArrTarefasConcessaoCredencial(false), TarefaRN::getArrTarefasCassacaoCredencial(false));

      $objInfraException = new InfraException(); 

      $objProtocoloDTO = new ProtocoloDTO();
      $objProtocoloDTO->retDblIdProtocolo();
      $objProtocoloDTO->retStrProtocoloFormatado();
      $objProtocoloDTO->retStrStaNivelAcessoGlobal();
      $objProtocoloDTO->setDblIdProtocolo($idProcedimento);

      $objProtocoloRN = new ProtocoloRN();
      $objProtocoloDTO = $objProtocoloRN->consultarRN0186($objProtocoloDTO);

      //Não verifica se usuário tem credencial de acesso

      $objAtividadeDTO = new AtividadeDTO();
      $objAtividadeDTO->retNumIdAtividade();
      $objAtividadeDTO->retNumIdUsuarioOrigem();
      $objAtividadeDTO->retStrSiglaUsuario();
      $objAtividadeDTO->retStrNomeUsuario();
      $objAtividadeDTO->retStrSiglaUnidade();
      $objAtividadeDTO->retStrDescricaoUnidade();
      $objAtividadeDTO->retDthAbertura();
      $objAtividadeDTO->retDthConclusao();
      $objAtividadeDTO->retNumIdTarefa();
      if ($numIdUsuario){
          $objAtividadeDTO->setNumIdUsuarioOrigem($numIdUsuario);
      }
      if ($numIdUsuarioDestino){
          $objAtividadeDTO->setNumIdUsuario($numIdUsuarioDestino);
      }
      $objAtividadeDTO->setDblIdProtocolo($objProtocoloDTO->getDblIdProtocolo());
      $objAtividadeDTO->setNumIdTarefa($arrTarefas, InfraDTO::$OPER_IN);
      $objAtividadeDTO->setOrdNumIdAtividade(InfraDTO::$TIPO_ORDENACAO_DESC);

      $objAtividadeRN = new AtividadeRN();

      $arrObjAtividadeDTO = $objAtividadeRN->listarRN0036($objAtividadeDTO);

      if (count($arrObjAtividadeDTO)){

          $objAtributoAndamentoDTO = new AtributoAndamentoDTO();
          $objAtributoAndamentoDTO->retNumIdAtividade();
          $objAtributoAndamentoDTO->retStrNome();
          $objAtributoAndamentoDTO->retStrValor();
          $objAtributoAndamentoDTO->setNumIdAtividade(InfraArray::converterArrInfraDTO($arrObjAtividadeDTO,'IdAtividade'), InfraDTO::$OPER_IN);

          $objAtributoAndamentoRN = new AtributoAndamentoRN();
          $arrObjAtributoAndamentoDTO = InfraArray::indexarArrInfraDTO($objAtributoAndamentoRN->listarRN1367($objAtributoAndamentoDTO),'IdAtividade',true);

          foreach($arrObjAtividadeDTO as $objAtividadeDTO){
              if (isset($arrObjAtributoAndamentoDTO[$objAtividadeDTO->getNumIdAtividade()])){
                  $objAtividadeDTO->setArrObjAtributoAndamentoDTO($arrObjAtributoAndamentoDTO[$objAtividadeDTO->getNumIdAtividade()]);
              }else{
                  $objAtividadeDTO->setArrObjAtributoAndamentoDTO(array());
              }
          }
      }
      
      return $arrObjAtividadeDTO;

      return array();
    }catch(Exception $e){
      throw new InfraException('Erro listarCredenciais.',$e);
    }
  }

  public function excluirAndamentoCredencialControlado($params) {
    try {
        $objProcedimentoDTO             = $params[0];
        $idUnidadeProcesso              = $params[1];
        $objConcederCredencial          = $params[2];    	
        $idUnidade                      = $params[3];
        $numIdUsuarioExterno            = $params[4];
        $numIdUsuarioAcesso		        = $params[5];
        $numIdUnidadeAcesso		        = $params[6];
        $numIdUsuarioAntesSigiloso      = $params[7];
        $numIdUnidadeAtualAntesSigiloso = $params[8];

        if ($objProcedimentoDTO->getStrStaNivelAcessoGlobalProtocolo() == ProtocoloRN::$NA_SIGILOSO
            || $objProcedimentoDTO->getStrStaNivelAcessoLocalProtocolo() == ProtocoloRN::$NA_SIGILOSO){

            //concessão
            $objAtividadeRN = new AtividadeRN();
            $objAtividadeDTOLiberacao = new AtividadeDTO();
            $objAtividadeDTOLiberacao->retTodos();
            $objAtividadeDTOLiberacao->setDblIdProtocolo( $objProcedimentoDTO->getDblIdProcedimento() );
//          $objAtividadeDTOLiberacao->setNumIdTarefa( array_merge(TarefaRN::getArrTarefasConcessaoCredencial(false), TarefaRN::getArrTarefasCassacaoCredencial(false)) , InfraDTO::$OPER_IN );
            $objAtividadeDTOLiberacao->setNumIdAtividade( $objConcederCredencial->getArrObjAtributoAndamentoDTO()[0]->getNumIdAtividade() );
            $arrDTOAtividades = $objAtividadeRN->listarRN0036( $objAtividadeDTOLiberacao );
            if (count($arrDTOAtividades)>0){
                $objAtividadeRN->excluirRN0034( $arrDTOAtividades );
            }

            //processo recebido
            $objAtividadeRN = new AtividadeRN();
            $objAtividadeDTOLiberacao = new AtividadeDTO();
            $objAtividadeDTOLiberacao->retTodos();
            $objAtividadeDTOLiberacao->setDblIdProtocolo( $objProcedimentoDTO->getDblIdProcedimento() );
            $objAtividadeDTOLiberacao->setNumIdTarefa( TarefaRN::$TI_PROCESSO_RECEBIMENTO_CREDENCIAL );
            $objAtividadeDTOLiberacao->setNumIdUsuario( $numIdUsuarioExterno );
            $arrDTOAtividades = $objAtividadeRN->listarRN0036( $objAtividadeDTOLiberacao );
            if (count($arrDTOAtividades)>0){
                $objAtividadeRN->excluirRN0034( $arrDTOAtividades );
            }

            //conclusão automática
            $objAtividadeRN = new AtividadeRN();
            $objAtividadeDTOLiberacao = new AtividadeDTO();
            $objAtividadeDTOLiberacao->retTodos();
            $objAtividadeDTOLiberacao->setDblIdProtocolo( $objProcedimentoDTO->getDblIdProcedimento() );
            $objAtividadeDTOLiberacao->setNumIdTarefa( TarefaRN::$TI_CONCLUSAO_AUTOMATICA_USUARIO );
            $arrDTOAtividades = $objAtividadeRN->listarRN0036( $objAtividadeDTOLiberacao );
            if (count($arrDTOAtividades)>0){
                $objAtributoAndamentoDTO = new AtributoAndamentoDTO();
                $objAtributoAndamentoDTO->retNumIdAtividade();
                $objAtributoAndamentoDTO->setStrNome('USUARIO');
                $objAtributoAndamentoDTO->setStrIdOrigem($numIdUsuarioExterno);
                $objAtributoAndamentoDTO->setNumIdAtividade(InfraArray::converterArrInfraDTO($arrDTOAtividades, 'IdAtividade'), InfraDTO::$OPER_IN);
                $objAtributoAndamentoDTO->setOrdNumIdAtributoAndamento(InfraDTO::$TIPO_ORDENACAO_DESC);

                $objAtributoAndamentoRN = new AtributoAndamentoRN();
                $arrObjAtributoAndamentoDTO = $objAtributoAndamentoRN->listarRN1367($objAtributoAndamentoDTO);

                $objAtividadeDTOLiberacao = new AtividadeDTO();
                $objAtividadeDTOLiberacao->retTodos();
                $objAtividadeDTOLiberacao->setDblIdProtocolo( $objProcedimentoDTO->getDblIdProcedimento() );
                $objAtividadeDTOLiberacao->setNumIdAtividade(InfraArray::converterArrInfraDTO($arrObjAtributoAndamentoDTO, 'IdAtividade'), InfraDTO::$OPER_IN);
                $arrDTOAtividades = $objAtividadeRN->listarRN0036( $objAtividadeDTOLiberacao );
                if (count($arrDTOAtividades)>0){
                    $objAtividadeRN->excluirRN0034( $arrDTOAtividades );
                }
            }

            //cassacão
            $objAtividadeRN = new AtividadeRN();
            $objAtividadeDTOLiberacao = new AtividadeDTO();
            $objAtividadeDTOLiberacao->retTodos();
            $objAtividadeDTOLiberacao->setDblIdProtocolo( $objProcedimentoDTO->getDblIdProcedimento() );
            $objAtividadeDTOLiberacao->setNumIdTarefa( TarefaRN::$TI_PROCESSO_CASSACAO_CREDENCIAL );
            $arrDTOAtividades = $objAtividadeRN->listarRN0036( $objAtividadeDTOLiberacao );
            if (count($arrDTOAtividades)>0){
                $objAtributoAndamentoDTO = new AtributoAndamentoDTO();
                $objAtributoAndamentoDTO->retNumIdAtividade();
                $objAtributoAndamentoDTO->setStrNome('USUARIO');
                $objAtributoAndamentoDTO->setStrIdOrigem($numIdUsuarioExterno);
                $objAtributoAndamentoDTO->setNumIdAtividade(InfraArray::converterArrInfraDTO($arrDTOAtividades, 'IdAtividade'), InfraDTO::$OPER_IN);
                $objAtributoAndamentoDTO->setOrdNumIdAtributoAndamento(InfraDTO::$TIPO_ORDENACAO_DESC);

                $objAtributoAndamentoRN = new AtributoAndamentoRN();
                $arrObjAtributoAndamentoDTO = $objAtributoAndamentoRN->listarRN1367($objAtributoAndamentoDTO);

                $objAtividadeDTOLiberacao = new AtividadeDTO();
                $objAtividadeDTOLiberacao->retTodos();
                $objAtividadeDTOLiberacao->setDblIdProtocolo( $objProcedimentoDTO->getDblIdProcedimento() );
                $objAtividadeDTOLiberacao->setNumIdAtividade(InfraArray::converterArrInfraDTO($arrObjAtributoAndamentoDTO, 'IdAtividade'), InfraDTO::$OPER_IN);
                $arrDTOAtividades = $objAtividadeRN->listarRN0036( $objAtividadeDTOLiberacao );
                if (count($arrDTOAtividades)>0){
                    $objAtividadeRN->excluirRN0034( $arrDTOAtividades );
                }
            }

            $objMdPetIntimacaoRN = new MdPetIntimacaoRN();
            $arrAtividadeDTO = $objMdPetIntimacaoRN->verificarUnidadeAberta( array($objProcedimentoDTO) );
            if (count($arrAtividadeDTO)==0){
                    // Usuário antes de tratamento de SIGILOSO
                    if ( is_numeric(SessaoSEI::getInstance()->getNumIdUsuario()) && is_numeric(SessaoSEI::getInstance()->getNumIdUnidadeAtual()) ){
                        $numIdUsuarioAntesSigiloso = SessaoSEI::getInstance()->getNumIdUsuario();
                        $numIdUnidadeAtualAntesSigiloso = SessaoSEI::getInstance()->getNumIdUnidadeAtual();
                    }
                    SessaoSEI::getInstance()->simularLogin(null, null, $objConcederCredencial->getNumIdUsuarioOrigem(), $objConcederCredencial->getNumIdUnidadeOrigem());

                    $objReabrirProcessoDTO = new ReabrirProcessoDTO();
                    $objReabrirProcessoDTO->setDblIdProcedimento( $objProcedimentoDTO->getDblIdProcedimento() );
                    $objReabrirProcessoDTO->setNumIdUnidade( $objConcederCredencial->getNumIdUnidadeOrigem() );
                    $objReabrirProcessoDTO->setNumIdUsuario( $objConcederCredencial->getNumIdUsuarioOrigem() );
                    $this->reabrirRN0966Controlado($objReabrirProcessoDTO);

                    // Usuário antes de tratamento de SIGILOSO - RETORNANDO
                    if ($numIdUnidadeAtualAntesSigiloso!='' && $numIdUsuarioAntesSigiloso!=''){
                        SessaoSEI::getInstance()->simularLogin(null, null, $numIdUsuarioAntesSigiloso, $numIdUnidadeAtualAntesSigiloso);
                    //}else{
                    // destruir sessão
                    }
                    
                //}
            }


        }
    }catch(Exception $e){
      throw new InfraException('Erro excluirAndamentoCredencial.',$e);
    }
  }

}
?>