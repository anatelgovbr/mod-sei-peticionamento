<?php

require_once dirname(__FILE__).'/../../../SEI.php';

class ProtocoloPeticionamentoRN extends ProtocoloRN {
	
	//METODO PRECISOU SER SOBRESCRITO DA RN ORIGINAL POR CONTA DE USUARIO EXTERNO
	private function validarNumIdUnidadeGeradoraRN0213(ProtocoloDTO $objProtocoloDTO, InfraException $objInfraException){
		
		if (InfraString::isBolVazia($objProtocoloDTO->getNumIdUnidadeGeradora())){
			$objInfraException->adicionarValidacao('Identificação da unidade geradora não informada.');
		}
	
	}
	
	//METODO PRECISOU SER SOBRESCRITO DA RN ORIGINAL POR CONTA DE USUARIO EXTERNO
	public function gerarNumeracaoProcessoExternoConectado( $objUnidadeDTO ){
		
		try{
	
			$ret = null;
	
			$objInfraSequencia = new InfraSequencia(BancoSEI::getInstance());
	
			$objOrgaoDTO = new OrgaoDTO();
			$objOrgaoDTO->retNumIdOrgao();
			$objOrgaoDTO->retStrSigla();
			$objOrgaoDTO->retStrNumeracao();
			$objOrgaoDTO->retStrCodigoSei();
			$objOrgaoDTO->setNumIdOrgao( SessaoSEIExterna::getInstance()->getNumIdOrgaoUsuarioExterno() );
	
			$objOrgaoRN = new OrgaoRN();
			$objOrgaoDTO = $objOrgaoRN->consultarRN1352($objOrgaoDTO);
	
			$strNumeracao = $objOrgaoDTO->getStrNumeracao();
	
			if (InfraString::isBolVazia($strNumeracao)){
				throw new InfraException('Formato da numeração não configurado para o órgão '.$objOrgaoDTO->getStrSigla().'.');
			}
		
			//Padrao SEI
			//@ano_2d@.@cod_orgao_sip@.@seq_anual_cod_orgao_sip_09d@-@dv_mod11_1d@
	
			//Executivo Federal
			//@cod_orgao_sip_05d@.@seq_anual_cod_orgao_sip_06d@/@ano_4d@-@dv_mod11_executivo_federal_2d@
	
			//Executivo Federal - Novo NUP
			//http://www.comprasgovernamentais.gov.br/paginas/comunicacoes-administrativas/numero-unico-de-protocolo-nup
			//@cod_unidade_sei_07d@.@seq_anual_cod_unidade_sei_08d@/@ano_4d@-@dv_mod97_base10_executivo_federal_2d@
	
			//Padrao CNJ/Justica Federal - Quarta Regiao
			//@seq_anual_cod_orgao_sip_07d@-@dv_mod97_base10_cnj_2d@.@ano_4d@.4.04.8000
	
			$strNumeracao = str_replace('@ano_2d@', substr(InfraData::getStrDataAtual(),-2), $strNumeracao);
			$strNumeracao = str_replace('@ano_4d@', substr(InfraData::getStrDataAtual(),-4), $strNumeracao);
	
			if (strpos($strNumeracao,'@cod_orgao_sip')!==false){
				$strNumeracao = str_replace('@cod_orgao_sip@', SessaoSEI::getInstance()->getNumIdOrgaoUnidadeAtual(), $strNumeracao);
				$strNumeracao = str_replace('@cod_orgao_sip_02d@',sprintf("%02s",SessaoSEIExterna::getInstance()->getNumIdOrgaoUsuarioExterno()), $strNumeracao);
				$strNumeracao = str_replace('@cod_orgao_sip_03d@',sprintf("%03s",SessaoSEIExterna::getInstance()->getNumIdOrgaoUsuarioExterno()), $strNumeracao);
				$strNumeracao = str_replace('@cod_orgao_sip_04d@',sprintf("%04s",SessaoSEIExterna::getInstance()->getNumIdOrgaoUsuarioExterno()), $strNumeracao);
				$strNumeracao = str_replace('@cod_orgao_sip_05d@',sprintf("%05s",SessaoSEIExterna::getInstance()->getNumIdOrgaoUsuarioExterno()), $strNumeracao);
			}
	
			if (strpos($strNumeracao,'@cod_orgao_sei')!==false){
	
				if (InfraString::isBolVazia($objOrgaoDTO->getStrCodigoSei())){
					throw new InfraException('Código SEI não configurado para o órgão '.$objOrgaoDTO->getStrSigla().'.');
				}
	
				$strNumeracao = str_replace('@cod_orgao_sei@', $objOrgaoDTO->getStrCodigoSei(), $strNumeracao);
				$strNumeracao = str_replace('@cod_orgao_sei_02d@',sprintf("%02s",$objOrgaoDTO->getStrCodigoSei()), $strNumeracao);
				$strNumeracao = str_replace('@cod_orgao_sei_03d@',sprintf("%03s",$objOrgaoDTO->getStrCodigoSei()), $strNumeracao);
				$strNumeracao = str_replace('@cod_orgao_sei_04d@',sprintf("%04s",$objOrgaoDTO->getStrCodigoSei()), $strNumeracao);
				$strNumeracao = str_replace('@cod_orgao_sei_05d@',sprintf("%05s",$objOrgaoDTO->getStrCodigoSei()), $strNumeracao);
			}
						
			if (strpos($strNumeracao,'@cod_unidade_sip')!==false){
				$strNumeracao = str_replace('@cod_unidade_sip@', $objUnidadeDTO->getNumIdUnidade(), $strNumeracao);
				$strNumeracao = str_replace('@cod_unidade_sip_02d@',sprintf("%02s",$objUnidadeDTO->getNumIdUnidade()), $strNumeracao);
				$strNumeracao = str_replace('@cod_unidade_sip_03d@',sprintf("%03s",$objUnidadeDTO->getNumIdUnidade()), $strNumeracao);
				$strNumeracao = str_replace('@cod_unidade_sip_04d@',sprintf("%04s",$objUnidadeDTO->getNumIdUnidade()), $strNumeracao);
				$strNumeracao = str_replace('@cod_unidade_sip_05d@',sprintf("%05s",$objUnidadeDTO->getNumIdUnidade()), $strNumeracao);
				$strNumeracao = str_replace('@cod_unidade_sip_06d@',sprintf("%06s",$objUnidadeDTO->getNumIdUnidade()), $strNumeracao);
				$strNumeracao = str_replace('@cod_unidade_sip_07d@',sprintf("%07s",$objUnidadeDTO->getNumIdUnidade()), $strNumeracao);
				$strNumeracao = str_replace('@cod_unidade_sip_08d@',sprintf("%08s",$objUnidadeDTO->getNumIdUnidade()), $strNumeracao);
				$strNumeracao = str_replace('@cod_unidade_sip_09d@',sprintf("%09s",$objUnidadeDTO->getNumIdUnidade()), $strNumeracao);
				$strNumeracao = str_replace('@cod_unidade_sip_010d@',sprintf("%010s",$objUnidadeDTO->getNumIdUnidade()), $strNumeracao);
			}
	
			if (strpos($strNumeracao,'@cod_unidade_sei')!==false){
								
				if (InfraString::isBolVazia($objUnidadeDTO->getStrCodigoSei())){
					throw new InfraException('Código SEI não configurado para a unidade '.$objUnidadeDTO->getStrSigla().' / '.$objOrgaoDTO->getStrSigla().'.');
				}
	
				$strNumeracao = str_replace('@cod_unidade_sei@', $objUnidadeDTO->getStrCodigoSei(), $strNumeracao);
				$strNumeracao = str_replace('@cod_unidade_sei_02d@',sprintf("%02s",$objUnidadeDTO->getStrCodigoSei()), $strNumeracao);
				$strNumeracao = str_replace('@cod_unidade_sei_03d@',sprintf("%03s",$objUnidadeDTO->getStrCodigoSei()), $strNumeracao);
				$strNumeracao = str_replace('@cod_unidade_sei_04d@',sprintf("%04s",$objUnidadeDTO->getStrCodigoSei()), $strNumeracao);
				$strNumeracao = str_replace('@cod_unidade_sei_05d@',sprintf("%05s",$objUnidadeDTO->getStrCodigoSei()), $strNumeracao);
				$strNumeracao = str_replace('@cod_unidade_sei_06d@',sprintf("%06s",$objUnidadeDTO->getStrCodigoSei()), $strNumeracao);
				$strNumeracao = str_replace('@cod_unidade_sei_07d@',sprintf("%07s",$objUnidadeDTO->getStrCodigoSei()), $strNumeracao);
				$strNumeracao = str_replace('@cod_unidade_sei_08d@',sprintf("%08s",$objUnidadeDTO->getStrCodigoSei()), $strNumeracao);
				$strNumeracao = str_replace('@cod_unidade_sei_09d@',sprintf("%09s",$objUnidadeDTO->getStrCodigoSei()), $strNumeracao);
				$strNumeracao = str_replace('@cod_unidade_sei_010d@',sprintf("%010s",$objUnidadeDTO->getStrCodigoSei()), $strNumeracao);
			}
						
			if (strpos($strNumeracao,'@seq_anual_cod_orgao_sip')!==false){
	
				$strNomeSequencia = 'seq_'.substr(InfraData::getStrDataAtual(),6).'_org_sip_'.SessaoSEIExterna::getInstance()->getNumIdOrgaoUsuarioExterno();
	
				if (!$objInfraSequencia->verificarSequencia($strNomeSequencia)){
					$objInfraSequencia->criarSequencia($strNomeSequencia,1,0,9999999999);
				}
				$numSequencial = $objInfraSequencia->obterProximaSequencia($strNomeSequencia);
	
				$strNumeracao = str_replace('@seq_anual_cod_orgao_sip_05d@', sprintf("%05s", $numSequencial), $strNumeracao);
				$strNumeracao = str_replace('@seq_anual_cod_orgao_sip_06d@', sprintf("%06s", $numSequencial), $strNumeracao);
				$strNumeracao = str_replace('@seq_anual_cod_orgao_sip_07d@', sprintf("%07s", $numSequencial), $strNumeracao);
				$strNumeracao = str_replace('@seq_anual_cod_orgao_sip_08d@', sprintf("%08s", $numSequencial), $strNumeracao);
				$strNumeracao = str_replace('@seq_anual_cod_orgao_sip_09d@', sprintf("%09s", $numSequencial), $strNumeracao);
				$strNumeracao = str_replace('@seq_anual_cod_orgao_sip_010d@', sprintf("%010s", $numSequencial), $strNumeracao);
			}
	
			if (strpos($strNumeracao,'@seq_anual_cod_orgao_sei')!==false){
	
				if (InfraString::isBolVazia($objOrgaoDTO->getStrCodigoSei())){
					throw new InfraException('Código SEI não configurado para o órgão '.$objOrgaoDTO->getStrSigla().'.');
				}
	
				$strNomeSequencia = 'seq_'.substr(InfraData::getStrDataAtual(),6).'_org_sei_'.$objOrgaoDTO->getStrCodigoSei();
	
				if (!$objInfraSequencia->verificarSequencia($strNomeSequencia)){
					$objInfraSequencia->criarSequencia($strNomeSequencia,1,0,9999999999);
				}
				$numSequencial = $objInfraSequencia->obterProximaSequencia($strNomeSequencia);
	
				$strNumeracao = str_replace('@seq_anual_cod_orgao_sei_05d@', sprintf("%05s", $numSequencial), $strNumeracao);
				$strNumeracao = str_replace('@seq_anual_cod_orgao_sei_06d@', sprintf("%06s", $numSequencial), $strNumeracao);
				$strNumeracao = str_replace('@seq_anual_cod_orgao_sei_07d@', sprintf("%07s", $numSequencial), $strNumeracao);
				$strNumeracao = str_replace('@seq_anual_cod_orgao_sei_08d@', sprintf("%08s", $numSequencial), $strNumeracao);
				$strNumeracao = str_replace('@seq_anual_cod_orgao_sei_09d@', sprintf("%09s", $numSequencial), $strNumeracao);
				$strNumeracao = str_replace('@seq_anual_cod_orgao_sei_010d@', sprintf("%010s", $numSequencial), $strNumeracao);
			}
			
			
			if (strpos($strNumeracao,'@seq_anual_cod_unidade_sip')!==false){
				$strNomeSequencia = 'seq_'.substr(InfraData::getStrDataAtual(),6).'_uni_sip_'.$objUnidadeDTO->getNumIdUnidade();
	
				if (!$objInfraSequencia->verificarSequencia($strNomeSequencia)){
					$objInfraSequencia->criarSequencia($strNomeSequencia,1,0,9999999999);
				}
				$numSequencial = $objInfraSequencia->obterProximaSequencia($strNomeSequencia);
	
				$strNumeracao = str_replace('@seq_anual_cod_unidade_sip_05d@', sprintf("%05s", $numSequencial), $strNumeracao);
				$strNumeracao = str_replace('@seq_anual_cod_unidade_sip_06d@', sprintf("%06s", $numSequencial), $strNumeracao);
				$strNumeracao = str_replace('@seq_anual_cod_unidade_sip_07d@', sprintf("%07s", $numSequencial), $strNumeracao);
				$strNumeracao = str_replace('@seq_anual_cod_unidade_sip_08d@', sprintf("%08s", $numSequencial), $strNumeracao);
				$strNumeracao = str_replace('@seq_anual_cod_unidade_sip_09d@', sprintf("%09s", $numSequencial), $strNumeracao);
				$strNumeracao = str_replace('@seq_anual_cod_unidade_sip_010d@', sprintf("%010s", $numSequencial), $strNumeracao);
			}
	
			if (strpos($strNumeracao,'@seq_anual_cod_unidade_sei')!==false){
	
				if (InfraString::isBolVazia($objUnidadeDTO->getStrCodigoSei())){
					throw new InfraException('Código SEI não configurado para a unidade '.$objUnidadeDTO->getStrSigla().' / '.$objOrgaoDTO->getStrSigla().'.');
				}
	
				$strNomeSequencia = 'seq_'.substr(InfraData::getStrDataAtual(),6).'_uni_sei_'.$objUnidadeDTO->getStrCodigoSei();
	
				if (!$objInfraSequencia->verificarSequencia($strNomeSequencia)){
					$objInfraSequencia->criarSequencia($strNomeSequencia,1,0,9999999999);
				}
				$numSequencial = $objInfraSequencia->obterProximaSequencia($strNomeSequencia);
	
				$strNumeracao = str_replace('@seq_anual_cod_unidade_sei_05d@', sprintf("%05s", $numSequencial), $strNumeracao);
				$strNumeracao = str_replace('@seq_anual_cod_unidade_sei_06d@', sprintf("%06s", $numSequencial), $strNumeracao);
				$strNumeracao = str_replace('@seq_anual_cod_unidade_sei_07d@', sprintf("%07s", $numSequencial), $strNumeracao);
				$strNumeracao = str_replace('@seq_anual_cod_unidade_sei_08d@', sprintf("%08s", $numSequencial), $strNumeracao);
				$strNumeracao = str_replace('@seq_anual_cod_unidade_sei_09d@', sprintf("%09s", $numSequencial), $strNumeracao);
				$strNumeracao = str_replace('@seq_anual_cod_unidade_sei_010d@', sprintf("%010s", $numSequencial), $strNumeracao);
			}
		
			$strNumeracaoDv = $strNumeracao;
			$strNumeracaoDv = str_replace('@dv_mod97_base10_cnj_2d@','',$strNumeracaoDv);
			$strNumeracaoDv = str_replace('@dv_mod11_1d@','',$strNumeracaoDv);
			$strNumeracaoDv = str_replace('@dv_mod11_executivo_federal_2d@','',$strNumeracaoDv);
			$strNumeracaoDv = str_replace('@dv_mod97_base10_executivo_federal_2d@','',$strNumeracaoDv);
	
			$strNumeracaoDv = InfraUtil::retirarFormatacao($strNumeracaoDv);
	
			if (strpos($strNumeracao,'@dv_mod11_1d@')!==false){
				$strNumeracao = str_replace('@dv_mod11_1d@', InfraUtil::calcularModulo11($strNumeracaoDv), $strNumeracao);
			}else if (strpos($strNumeracao,'@dv_mod11_executivo_federal_2d@')!==false){
				
				$dv1 = $this->calcularMod11ExecutivoFederal($strNumeracaoDv);
				$dv2 = $this->calcularMod11ExecutivoFederal($strNumeracaoDv.$dv1);
				$strNumeracao = str_replace('@dv_mod11_executivo_federal_2d@',(string)$dv1.(string)$dv2, $strNumeracao);				
				
			}else if (strpos($strNumeracao,'@dv_mod97_base10_cnj_2d@')!==false){
				$strNumeracao = str_replace('@dv_mod97_base10_cnj_2d@', $this->calcularMod97Base10Cnj($strNumeracaoDv), $strNumeracao);
			}else if (strpos($strNumeracao,'@dv_mod97_base10_executivo_federal_2d@')!==false){
				$strNumeracao = str_replace('@dv_mod97_base10_executivo_federal_2d@', $this->calcularMod97Base10ExecutivoFederal($strNumeracaoDv), $strNumeracao);
			}
				
			return $strNumeracao;
		  
		} catch(Exception $e){
			throw new InfraException('Erro gerando numeração de processo.',$e);
		}
	}
	
	private function calcularMod11ExecutivoFederal($strValor){
		
		$soma = 0; // acumulador
		$peso = 2; // peso inicial
	
		for ($i = strlen($strValor) - 1; $i >= 0; $i--) {
			//InfraDebug::getInstance()->gravar(substr($strValor, $i, 1).' * '.$peso.' = '.intval(substr($strValor, $i, 1)) * $peso);
			$soma += intval(substr($strValor, $i, 1)) * $peso++;
		}
			
		//InfraDebug::getInstance()->gravar('SOMA='.$soma);
	
		$resto = $soma % 11;
		$dv = 11 - $resto;
			
		//11 - 10 =  1
		//11 -  9 =  2
		//11 -  8 =  3
		//11 -  7 =  4
		//11 -  6 =  5
		//11 -  5 =  6
		//11 -  4 =  7
		//11 -  3 =  8
		//11 -  2 =  9
		//11 -  1 = 10
		//11 -  0 = 11
	
		if ($dv == 10){
			$dv = 0;
		}elseif ($dv > 10){
			$dv = 1;
		}
		
		return $dv;
	}
	
	protected function gerarRN0154Controlado(ProtocoloDTO $objProtocoloDTO) {
		
		try{

			$numIdUnidade = $objProtocoloDTO->getNumIdUnidadeGeradora();
			
			//Valida Permissao
			//SessaoSEI::getInstance()->validarAuditarPermissao('protocolo_gerar',__METHOD__,$objProtocoloDTO);
	
			//Regras de Negocio
			$objInfraException = new InfraException();
	
			if ($objProtocoloDTO->isSetDblIdProtocoloAgrupador()){
				$objInfraException->adicionarValidacao('Número do protocolo agrupador não pode ser informado na geração.');
			}
	
			$this->validarStrStaProtocoloRN0212($objProtocoloDTO, $objInfraException);
			$this->validarArrRelProtocoloAssuntoRN0216($objProtocoloDTO, $objInfraException);
	
			if ($objProtocoloDTO->isSetArrObjRelProtocoloAtributoDTO()){
				$this->validarArrObjRelProtocoloAtributoDTO($objProtocoloDTO, $objInfraException);
			}
			
			$this->validarArrParticipanteRN0572($objProtocoloDTO, $objInfraException);
			$this->validarArrObjObservacaoRN0573($objProtocoloDTO, $objInfraException);
			$this->validarArrAnexoRN0227($objProtocoloDTO,$objInfraException);
			$this->validarNumIdUnidadeGeradoraRN0213($objProtocoloDTO, $objInfraException);
			$this->validarNumIdUsuarioGeradorRN0214($objProtocoloDTO, $objInfraException);
			$this->validarDtaGeracaoRN0215($objProtocoloDTO, $objInfraException);
			$this->validarStrDescricaoRN1229($objProtocoloDTO, $objInfraException);
	
			$this->validarStrStaNivelAcessoLocalRN0685($objProtocoloDTO, $objInfraException);
	
			if (!$objProtocoloDTO->isSetStrStaGrauSigilo()){
				$objProtocoloDTO->setStrStaGrauSigilo(null);
			}
			$this->validarStrStaGrauSigilo($objProtocoloDTO, $objInfraException);
						
			if ( $objProtocoloDTO->getStrStaNivelAcessoLocal() != ProtocoloRN::$NA_PUBLICO && !$objProtocoloDTO->isSetNumIdHipoteseLegal()){
				$objProtocoloDTO->setNumIdHipoteseLegal(null);
			} 
			
			$this->validarNumIdHipoteseLegal($objProtocoloDTO, $objInfraException);
	
			$objProtocoloDTO->setStrStaEstado(self::$TE_NORMAL);
			$objProtocoloDTO->setStrStaArquivamento(ProtocoloRN::$TA_NAO_ARQUIVADO);
			$objProtocoloDTO->setNumIdLocalizador(null);
			$objProtocoloDTO->setNumIdUnidadeArquivamento(null);
			$objProtocoloDTO->setNumIdUsuarioArquivamento(null);
			$objProtocoloDTO->setDthArquivamento(null);
	
			$objProtocoloDTO->setDblIdProtocolo($this->gerarNumeracaoInterna());
	
			if (!InfraString::isBolVazia($objProtocoloDTO->getStrProtocoloFormatado())){
				if ($objProtocoloDTO->getStrStaProtocolo()==ProtocoloRN::$TP_PROCEDIMENTO){
					$objProcedimentoRN = new ProcedimentoRN();
					if (!$objProcedimentoRN->verificarLiberacaoNumeroProcesso()){
						$objInfraException->adicionarValidacao('Não é possível informar o número do processo.');
					}else{
						$this->validarProtocoloInformado($objProtocoloDTO,$objInfraException);
					}
				}else{
					$objInfraException->adicionarValidacao('Protocolo do documento não pode ser informado na geração.');
				}
			}else{
				if ($objProtocoloDTO->getStrStaProtocolo()==self::$TP_PROCEDIMENTO){
					$objProtocoloDTO->setStrProtocoloFormatado($this->gerarNumeracaoProcesso());
				}else{
					$objProtocoloDTO->setStrProtocoloFormatado($this->gerarNumeracaoDocumento());
				}
			}
	
			$this->validarStrProtocoloFormatadoRN0211($objProtocoloDTO, $objInfraException);
	
			$objProtocoloDTO->setStrProtocoloFormatadoPesquisa(preg_replace("/[^0-9a-zA-Z]+/", '',$objProtocoloDTO->getStrProtocoloFormatado()));
	
			if ($objProtocoloDTO->getStrStaProtocolo()==self::$TP_PROCEDIMENTO){
				$objProtocoloDTO->setStrStaNivelAcessoGlobal($objProtocoloDTO->getStrStaNivelAcessoLocal());
			}else{
				
				SessaoSEI::getInstance(false);
				SessaoSEI::getInstance()->simularLogin(null, null, SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() , $numIdUnidade );
				
				$objMudarNivelAcessoDTO = new MudarNivelAcessoDTO();
				$objMudarNivelAcessoDTO->setStrStaOperacao(self::$TMN_CADASTRO);
				$objMudarNivelAcessoDTO->setDblIdProtocolo($objProtocoloDTO->getDblIdProcedimento());
				$objMudarNivelAcessoDTO->setStrStaNivel($objProtocoloDTO->getStrStaNivelAcessoLocal());
				$objProtocoloDTO->setStrStaNivelAcessoGlobal($this->mudarNivelAcesso($objMudarNivelAcessoDTO));
			}
	
			$objProtocoloDTO->setStrStaNivelAcessoOriginal(null);
	
			$objInfraException->lancarValidacoes();
	
			InfraCodigoBarras::gerar($objProtocoloDTO->getStrProtocoloFormatadoPesquisa(), DIR_SEI_TEMP, InfraCodigoBarras::$TIPO_CODE39, InfraCodigoBarras::$COR_PRETO, 1, 26, 0, 13*strlen($objProtocoloDTO->getStrProtocoloFormatadoPesquisa())+30, 30, InfraCodigoBarras::$FORMATO_PNG);
			$strArquivoCodigoBarras = DIR_SEI_TEMP.'/code39_'.$objProtocoloDTO->getStrProtocoloFormatadoPesquisa().'.png';
			$fp = fopen($strArquivoCodigoBarras, "r");
			$imgCodigoBarras = fread($fp, filesize($strArquivoCodigoBarras));
			fclose($fp);
			unlink($strArquivoCodigoBarras);
			$objProtocoloDTO->setStrCodigoBarras(base64_encode($imgCodigoBarras));
	
			$objProtocoloDTO->setDblIdProtocoloAgrupador($objProtocoloDTO->getDblIdProtocolo());
	
			$objProtocoloBD = new ProtocoloBD($this->getObjInfraIBanco());
			$objProtocoloBD->cadastrar($objProtocoloDTO);
	
			$arrObjObservacaoDTO = $objProtocoloDTO->getArrObjObservacaoDTO();
			foreach($arrObjObservacaoDTO as $objObservacaoDTO){
				if (!InfraString::isBolVazia($objObservacaoDTO->getStrDescricao())){
					$objObservacaoDTO->setDblIdProtocolo($objProtocoloDTO->getDblIdProtocolo());
					$objObservacaoDTO->setNumIdUnidade($numIdUnidade);
					$objObservacaoRN = new ObservacaoRN();
					$objObservacaoRN->cadastrarRN0222($objObservacaoDTO);
				}
			}
	
			$objParticipanteRN = new ParticipanteRN();
			$arrParticipantes = $objProtocoloDTO->getArrObjParticipanteDTO();
			for ($i=0;$i<count($arrParticipantes);$i++){
				$arrParticipantes[$i]->setDblIdProtocolo($objProtocoloDTO->getDblIdProtocolo());
				$arrParticipantes[$i]->setNumIdUnidade( $numIdUnidade );
				$objParticipanteRN->cadastrarRN0170($arrParticipantes[$i]);
			}
	
			$objRelProtocoloAssuntoRN = new RelProtocoloAssuntoRN();
			$arrAssuntos = $objProtocoloDTO->getArrObjRelProtocoloAssuntoDTO();
			for ($i=0;$i<count($arrAssuntos);$i++){
				$arrAssuntos[$i]->setDblIdProtocolo($objProtocoloDTO->getDblIdProtocolo());
				$arrAssuntos[$i]->setNumIdUnidade( $numIdUnidade );
				$objRelProtocoloAssuntoRN->cadastrarRN0171($arrAssuntos[$i]);
			}
	
			if ($objProtocoloDTO->isSetArrObjRelProtocoloAtributoDTO()){
	
				$objRelProtocoloAtributoRN = new RelProtocoloAtributoRN();
				$arrRelProtocoloAtributoDTO = $objProtocoloDTO->getArrObjRelProtocoloAtributoDTO();
				for ($i=0;$i<count($arrRelProtocoloAtributoDTO);$i++){
					$arrRelProtocoloAtributoDTO[$i]->setDblIdProtocolo($objProtocoloDTO->getDblIdProtocolo());
					$objRelProtocoloAtributoRN->cadastrar($arrRelProtocoloAtributoDTO[$i]);
				}
	
			}
	
			$objAnexoRN = new AnexoRN();
			$arrAnexos = $objProtocoloDTO->getArrObjAnexoDTO();
			
			for($i=0;$i<count($arrAnexos);$i++){
				$arrAnexos[$i]->setDblIdProtocolo($objProtocoloDTO->getDblIdProtocolo());
				$arrAnexos[$i]->setNumIdBaseConhecimento(null);
				$arrAnexos[$i]->setNumIdProjeto(null);
				$arrAnexos[$i]->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() );
				$arrAnexos[$i]->setNumIdUnidade( $numIdUnidade );
				$arrAnexos[$i]->setStrSinAtivo('S');
				$objAnexoRN->cadastrarRN0172($arrAnexos[$i]);
			}
	
			//Auditoria
			$ret = new ProtocoloDTO();
			$ret->setDblIdProtocolo($objProtocoloDTO->getDblIdProtocolo());
			$ret->setStrProtocoloFormatado($objProtocoloDTO->getStrProtocoloFormatado());
			$ret->setStrStaNivelAcessoGlobal($objProtocoloDTO->getStrStaNivelAcessoGlobal());
	
			return $ret;
	
		}catch(Exception $e){
			throw new InfraException('Erro gerando protocolo.',$e);
		}
	}
	
	private function validarStrStaProtocoloRN0212(ProtocoloDTO $objProtocoloDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objProtocoloDTO->getStrStaProtocolo())){
			$objInfraException->adicionarValidacao('Tipo do protocolo não informado.');
		}else{
			$arr = InfraArray::converterArrInfraDTO($this->listarTiposRN0684(),'StaTipo');
			if (!in_array($objProtocoloDTO->getStrStaProtocolo(),$arr)){
				$objInfraException->adicionarValidacao('Tipo do protocolo inválido.');
			}
		}
	}
	
	private function validarArrRelProtocoloAssuntoRN0216(ProtocoloDTO $objProtocoloDTO, InfraException $objInfraException){
		 
		if(($objProtocoloDTO->getArrObjRelProtocoloAssuntoDTO()===null || count($objProtocoloDTO->getArrObjRelProtocoloAssuntoDTO())==0)){
			if ($objProtocoloDTO->getStrStaProtocolo() == ProtocoloRN::$TP_PROCEDIMENTO){
				$objInfraException->adicionarValidacao('Nenhum assunto foi informado.');
			}
		}else{
			 
			$objProtocoloDTO->setArrObjRelProtocoloAssuntoDTO(InfraArray::distinctArrInfraDTO($objProtocoloDTO->getArrObjRelProtocoloAssuntoDTO(),'IdAssunto'));
	
			$objAssuntoRN = new AssuntoRN();
			$objAssuntoDTO = new AssuntoDTO();
			$objAssuntoDTO->setNumIdAssunto(InfraArray::converterArrInfraDTO($objProtocoloDTO->getArrObjRelProtocoloAssuntoDTO(),'IdAssunto'),InfraDTO::$OPER_IN);
			$objAssuntoDTO->setStrSinSuficiente('S');
	
			if ($objAssuntoRN->contarRN0249($objAssuntoDTO)==0){
				$objInfraException->adicionarValidacao('Assuntos não são suficientes para classificação.');
			}
		}
	}
	
	private function validarArrParticipanteRN0572(ProtocoloDTO $objProtocoloDTO, InfraException $objInfraException){
	
		$arrParticipantes = $objProtocoloDTO->getArrObjParticipanteDTO();
		$arrDuplicados = array();
	
		if (count($arrParticipantes) > 0){
			//usando FOREACH para limpar
			foreach($arrParticipantes as $objParticipanteDTO){
	
				if (!$objParticipanteDTO->isSetNumIdContato()){
					$objInfraException->lancarValidacao('Identificador do participante não informado.');
				}
		   
				if (!$objParticipanteDTO->isSetStrStaParticipacao()){
					$objInfraException->lancarValidacao('Tipo de participação do participante não informada.');
				}
		   
				$arrDuplicados[] = $objParticipanteDTO->getNumIdContato().'-'.$objParticipanteDTO->getStrStaParticipacao();
			}
		}
	
		if(count($arrDuplicados) != count(array_unique($arrDuplicados))){
			$objInfraException->adicionarValidacao('Foram encontrados participantes duplicados.');
		}
	}
	
	private function validarArrObjObservacaoRN0573(ProtocoloDTO $objProtocoloDTO, InfraException $objInfraException){
		if (count($objProtocoloDTO->getArrObjObservacaoDTO())>1){
			$objInfraException->adicionarValidacao('Mais de uma observação informada para a unidade.');
		}
	}
	
	private function validarArrAnexoRN0227(ProtocoloDTO $objProtocoloDTO, InfraException $objInfraException){
		//TODO checar se pode remover este método
		//Nada a validar
	}
	
	private function validarNumIdUsuarioGeradorRN0214(ProtocoloDTO $objProtocoloDTO, InfraException $objInfraException){
		
		if (InfraString::isBolVazia($objProtocoloDTO->getNumIdUsuarioGerador())){
			$objInfraException->adicionarValidacao('Identificação do usuário gerador não informada.');
		}
	}
	
	private function validarDtaGeracaoRN0215(ProtocoloDTO $objProtocoloDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objProtocoloDTO->getDtaGeracao())){
			$objInfraException->adicionarValidacao('Data do protocolo não informada.');
		}else{
			if (!InfraData::validarData($objProtocoloDTO->getDtaGeracao())){
				$objInfraException->adicionarValidacao('Data do protocolo inválida.');
			}
		}
		if (InfraData::compararDatas(InfraData::getStrDataHoraAtual(),$objProtocoloDTO->getDtaGeracao())>0){
			$objInfraException->adicionarValidacao('Data do protocolo não pode estar no futuro.');
		}
	}
	
	private function validarStrDescricaoRN1229(ProtocoloDTO $objProtocoloDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objProtocoloDTO->getStrDescricao())){
			$objProtocoloDTO->setStrDescricao(null);
		}
	
		$objProtocoloDTO->setStrDescricao(trim($objProtocoloDTO->getStrDescricao()));
	
		if ($objProtocoloDTO->getStrStaProtocolo()==ProtocoloRN::$TP_PROCEDIMENTO){
			if (strlen($objProtocoloDTO->getStrDescricao())>50){
				$objInfraException->adicionarValidacao('Especificação possui tamanho superior a 50 caracteres.');
			}
		}else{
			if (strlen($objProtocoloDTO->getStrDescricao())>250){
				$objInfraException->adicionarValidacao('Descrição possui tamanho superior a 250 caracteres.');
			}
		}
	}
	
	private function validarStrMotivoCancelamento(ProtocoloDTO $objProtocoloDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objProtocoloDTO->getStrMotivoCancelamento())){
			$objInfraException->adicionarValidacao('Motivo não informado.');
		}
	}
	
	private function validarStrStaGrauSigilo(ProtocoloDTO $objProtocoloDTO, InfraException $objInfraException){
		
		$objInfraParametro = new InfraParametro(BancoSEI::getInstance());
		$numHabilitarGrauSigilo = $objInfraParametro->getValor('SEI_HABILITAR_GRAU_SIGILO');
	
		if ($numHabilitarGrauSigilo){
	
			if ($objProtocoloDTO->getStrStaNivelAcessoLocal()==ProtocoloRN::$NA_SIGILOSO){
	
				if ($numHabilitarGrauSigilo==2 && InfraString::isBolVazia($objProtocoloDTO->getStrStaGrauSigilo())){
					$objInfraException->adicionarValidacao('Grau do sigilo não informado.');
				}
	
				if (!InfraString::isBolVazia($objProtocoloDTO->getStrStaGrauSigilo()) && !in_array($objProtocoloDTO->getStrStaGrauSigilo(),InfraArray::converterArrInfraDTO(self::listarGrausSigiloso(),'StaGrau'))){
					$objInfraException->adicionarValidacao('Grau do sigilo inválido.');
				}
	
			}else{
				 
				if (!InfraString::isBolVazia($objProtocoloDTO->getStrStaGrauSigilo())){
					$objInfraException->adicionarValidacao('Grau do sigilo não aplicável ao protocolo.');
				}
	
			}
		}else{
			$objProtocoloDTO->setStrStaGrauSigilo(null);
		}
	}
	
	private function validarNumIdHipoteseLegal(ProtocoloDTO $objProtocoloDTO, InfraException $objInfraException){
		
		$objInfraParametro = new InfraParametro(BancoSEI::getInstance());
		$numHabilitarHipoteseLegal = $objInfraParametro->getValor('SEI_HABILITAR_HIPOTESE_LEGAL');
	
		if ($numHabilitarHipoteseLegal){
	
			if($objProtocoloDTO->getStrStaNivelAcessoLocal()==ProtocoloRN::$NA_SIGILOSO || $objProtocoloDTO->getStrStaNivelAcessoLocal()==ProtocoloRN::$NA_RESTRITO){
								
				if ($numHabilitarHipoteseLegal==2 && InfraString::isBolVazia($objProtocoloDTO->getNumIdHipoteseLegal())){
					$objInfraException->adicionarValidacao('Hipótese Legal não informada.');
				}
	
				if (!InfraString::isBolVazia($objProtocoloDTO->getNumIdHipoteseLegal())){
	
					$objHipoteseLegalDTO = new HipoteseLegalDTO();
					$objHipoteseLegalDTO->retStrStaNivelAcesso();
					$objHipoteseLegalDTO->setNumIdHipoteseLegal($objProtocoloDTO->getNumIdHipoteseLegal());
	
					$objHipoteseLegalRN = new HipoteseLegalRN();
					$objHipoteseLegalDTO = $objHipoteseLegalRN->consultar($objHipoteseLegalDTO);
	
					if ($objHipoteseLegalDTO==null){
						$objInfraException->adicionarValidacao('Hipótese Legal não encontrada.');
					}
				}
	
			}else{
								
				if ( $objProtocoloDTO->isSetNumIdHipoteseLegal() && !InfraString::isBolVazia($objProtocoloDTO->getNumIdHipoteseLegal())){
					$objInfraException->adicionarValidacao('Hipótese Legal não aplicável ao protocolo.');
				}
	
			}
		}else{
			$objProtocoloDTO->setNumIdHipoteseLegal(null);
		}
	}
	
	public function gerarNumeracaoInterna() {
		try{
	
			return $this->getObjInfraIBanco()->getValorSequencia('seq_protocolo');
	
		}catch(Exception $e){
			throw new InfraException('Erro gerando numeração interna.',$e);
		}
	}
	
	private function validarProtocoloInformado(ProtocoloDTO $objProtocoloDTO, InfraException $objInfraException){
	
		$objInfraParametro = new InfraParametro(BancoSEI::getInstance());
		$strMascara = $objInfraParametro->getValor('SEI_MASCARA_NUMERO_PROCESSO_INFORMADO');
	
		if(InfraString::isBolVazia($strMascara)) return;
	
		if (!InfraUtil::validarMascara($objProtocoloDTO->getStrProtocoloFormatado(),$strMascara)) {
			$objInfraException->adicionarValidacao("Número de processo informado inválido.");
		}
	}
	
	private function validarStrProtocoloFormatadoRN0211(ProtocoloDTO $objProtocoloDTO, InfraException $objInfraException){
		
		if (InfraString::isBolVazia($objProtocoloDTO->getStrProtocoloFormatado())){
			$objInfraException->adicionarValidacao('Número do protocolo não informado.');
		}else{
	
			$objProtocoloDTO->setStrProtocoloFormatado(trim($objProtocoloDTO->getStrProtocoloFormatado()));
	
			if (strlen($objProtocoloDTO->getStrProtocoloFormatado())>40){
				$objInfraException->adicionarValidacao('Número do protocolo possui tamanho superior a 40 caracteres.');
			}
	
			$objProtocoloDTOBanco = new ProtocoloDTO();
			$objProtocoloDTOBanco->retStrStaProtocolo();
			$objProtocoloDTOBanco->setStrProtocoloFormatado($objProtocoloDTO->getStrProtocoloFormatado());
			$objProtocoloDTOBanco = $this->consultarRN0186($objProtocoloDTOBanco);
	
			if ($objProtocoloDTOBanco!=null){
				if ($objProtocoloDTOBanco->getStrStaProtocolo()==ProtocoloRN::$TP_PROCEDIMENTO){
					$objInfraException->adicionarValidacao('Existe um processo utilizando este número de protocolo: '.$objProtocoloDTO->getStrProtocoloFormatado());
				}else{
					$objInfraException->adicionarValidacao('Existe um documento utilizando este número de protocolo: '.$objProtocoloDTO->getStrProtocoloFormatado());
				}
			}
		}
	}
	
	private function validarArrObjRelProtocoloAtributoDTO(ProtocoloDTO $objProtocoloDTO, InfraException $objInfraException){
	
		return;
	
		if (!$objProtocoloDTO->isSetStrStaProtocolo() || InfraString::isBolVazia($objProtocoloDTO->getStrStaProtocolo())){
			if ($objProtocoloDTO->getDblIdProtocolo()==null){
				$objInfraException->adicionarValidacao('Tipo do protocolo não informado manipulando atributos.');
			}else{
				$dto = new ProtocoloDTO(true);
	
				$dto->retStrStaProtocolo();
				$dto->setDblIdProtocolo($objProtocoloDTO->getDblIdProtocolo());
				$dto = $this->consultarRN0186($dto);
				$objProtocoloDTO->setStrStaProtocolo($dto->getStrStaProtocolo());
			}
		}
	
		$objAplicabilidadeAtributoDTO = new AplicabilidadeAtributoDTO();
		$objAplicabilidadeAtributoDTO->retNumIdAtributo();
	
	
		if(!$objProtocoloDTO->isSetNumIdSerieDocumento()){
	
			$objAplicabilidadeAtributoDTO->setNumIdSerie(null);
			$objAplicabilidadeAtributoDTO->setNumIdTipoProcedimento($objProtocoloDTO->getNumIdTipoProcedimentoProcedimento());
			$objAplicabilidadeAtributoDTO->adicionarCriterio(array('IdUnidade','IdUnidade'),
					array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),
					array(SessaoSEI::getInstance()->getNumIdUnidadeAtual(),null), InfraDTO::$OPER_LOGICO_OR);
			 
		}else{
			 
			$objAplicabilidadeAtributoDTO->setNumIdSerie($objProtocoloDTO->getNumIdSerieDocumento());
	
			if($objProtocoloDTO->isSetNumIdTipoProcedimentoProcedimento()){
				$objAplicabilidadeAtributoDTO->adicionarCriterio(array('IdTipoProcedimento','IdTipoProcedimento'),
						array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),
						array($objProtocoloDTO->getNumIdTipoProcedimentoProcedimento(),null), InfraDTO::$OPER_LOGICO_OR);
			}else{
				$objAplicabilidadeAtributoDTO->setNumIdTipoProcedimento(null);
			}
		  
			$objAplicabilidadeAtributoDTO->adicionarCriterio(array('IdUnidade','IdUnidade'),
					array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),
					array(SessaoSEI::getInstance()->getNumIdUnidadeAtual(),null), InfraDTO::$OPER_LOGICO_OR);
	
		}
	
		$objAplicabilidadeAtributoRN = new AplicabilidadeAtributoRN();
		$arrObjAplicabilidadeAtributoDTO = $objAplicabilidadeAtributoRN->listar($objAplicabilidadeAtributoDTO);
	
		$arrObjAtributoDTO = array();
	
		foreach($arrObjAplicabilidadeAtributoDTO as $objAplicabilidadeAtributoDTO){
			 
			$objAtributoDTO = new AtributoDTO();
			$objAtributoDTO->retStrNome();
			$objAtributoDTO->retNumIdAtributo();
			$objAtributoDTO->retStrSinObrigatorio();
			$objAtributoDTO->setNumIdAtributo($objAplicabilidadeAtributoDTO->getNumIdAtributo());
	
			$objAtributoRN = new AtributoRN();
			$objAtributoDTO = $objAtributoRN->consultarRN0115($objAtributoDTO);
	
			$arrObjAtributoDTO[] = $objAtributoDTO;
	
		}
		 
		$arrObjRelProtocoloAtributoDTO = $objProtocoloDTO->getArrObjRelProtocoloAtributoDTO();
	
	
		for ($i=0;$i<count($arrObjAtributoDTO);$i++){
				
			//Se é um atributo obrigatório
			if($arrObjAtributoDTO[$i]->getStrSinObrigatorio()=='S'){
	
				$flag = 0;
	
				foreach($arrObjRelProtocoloAtributoDTO as $objRelProtocoloAtributoDTO){
					 
					if($objRelProtocoloAtributoDTO->getNumIdAtributo() == $arrObjAtributoDTO[$i]->getNumIdAtributo()){
						if(!InfraString::isBolVazia($objRelProtocoloAtributoDTO->getStrValor())){
							$flag = 1;
						}
					}
				}
				if($flag == 0){
					$objInfraException->adicionarValidacao('Atributo \"'.$arrObjAtributoDTO[$i]->getStrNome().'\" obrigatório não informado.');
				}
			}
		}
	}
}
?>