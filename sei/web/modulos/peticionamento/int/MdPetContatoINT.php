<?
/**
*
* 22/08/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetContatoINT extends ContatoINT {

	public static function getContatoByCPFCNPJ( $cpfcnpj ){
	
		$objContextoContatoDTO = new ContatoDTO();
		
		$objContextoContatoDTO->retStrNome();
		$objContextoContatoDTO->retNumIdContato();
		$objContextoContatoDTO->retStrSigla();
		$objContextoContatoDTO->retStrSinAtivo();
		$objContextoContatoDTO->retNumIdUsuarioCadastro();

		$objContextoContatoDTO->adicionarCriterio(array('Cpf', 'Cnpj', 'SinAtivo'),
				
				array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL),
				
				array( $cpfcnpj, $cpfcnpj,'S'),
				
				array(InfraDTO::$OPER_LOGICO_OR, 
					  InfraDTO::$OPER_LOGICO_AND)
		);
		
		$objContatoRN = new ContatoRN();
		$objContextoContatoDTO = $objContatoRN->consultarRN0324($objContextoContatoDTO);
	
		return $objContextoContatoDTO;
	}
	
	public static function getTotalContatoByCPFCNPJ( $cpfcnpj ){
		//Contato
		$objContextoContatoDTO = new ContatoDTO();
		$objContextoContatoDTO->retStrNome();
		$objContextoContatoDTO->retNumIdContato();
		$objContextoContatoDTO->retNumIdUsuarioCadastro();
		$objContextoContatoDTO->retStrSigla();
		$objContextoContatoDTO->retStrSinAtivo();
		$objContextoContatoDTO->setDistinct(true);
		$objContextoContatoDTO->adicionarCriterio(array('Cpf', 'Cnpj'),
				array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL),
				array( $cpfcnpj, $cpfcnpj),
				array(InfraDTO::$OPER_LOGICO_OR)
		);

		$objContatoRN = new ContatoRN();
		$arrObjContextoContatoDTO = $objContatoRN->listarRN0325($objContextoContatoDTO);

		if (count($arrObjContextoContatoDTO)==0) {
			return null;
		} else if (count($arrObjContextoContatoDTO)>1) {
			$arrObjContextoContato = InfraArray::converterArrInfraDTO($arrObjContextoContatoDTO,'IdUsuarioCadastro');
			$arrObjContextoContato = array_filter($arrObjContextoContato);
			if (count($arrObjContextoContato)>0){
				//Usuário Externo
				$objUsuarioDTO = new UsuarioDTO();
				$objUsuarioDTO->retNumIdUsuario();
				$objUsuarioDTO->setStrStaTipo(UsuarioRN::$TU_EXTERNO);
				$objUsuarioDTO->setDistinct(true);
				$objUsuarioDTO->adicionarCriterio(
						array('IdUsuario'),
						array(InfraDTO::$OPER_IN),
						array($arrObjContextoContato)
				);

				$objUsuarioRN = new UsuarioRN();
				$arrObjUsuarioDTO = $objUsuarioRN->listarRN0490($objUsuarioDTO);

				if (count($arrObjUsuarioDTO)>0) {
					$arrObjUsuario = InfraArray::converterArrInfraDTO($arrObjUsuarioDTO,'IdUsuario');

					//Contato Filtrado
					$objContextoContatoDTO = new ContatoDTO();
					$objContextoContatoDTO->retStrNome();
					$objContextoContatoDTO->retNumIdContato();
					$objContextoContatoDTO->retNumIdUsuarioCadastro();
					$objContextoContatoDTO->retStrSigla();
					$objContextoContatoDTO->retStrSinAtivo();
					$objContextoContatoDTO->setDistinct(true);
					$objContextoContatoDTO->setOrd('IdContato', InfraDTO::$TIPO_ORDENACAO_DESC);
					$objContextoContatoDTO->adicionarCriterio(array('Cpf', 'Cnpj'),
							array(InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL),
							array( $cpfcnpj, $cpfcnpj),
							array(InfraDTO::$OPER_LOGICO_OR)
					);
					$objContextoContatoDTO->adicionarCriterio(array('IdUsuarioCadastro'),
							array(InfraDTO::$OPER_DIFERENTE),
							array(NULL)
					);
					$objContextoContatoDTO->adicionarCriterio(
							array('IdUsuarioCadastro'),
							array(InfraDTO::$OPER_IN),
							array($arrObjUsuario)
					);
					$objContatoRN = new ContatoRN();
					$arrObjContextoContatoDTO = $objContatoRN->listarRN0325($objContextoContatoDTO);

				}else{
					return null;
				}
			}else{
				return null;
			}
		}
		return $arrObjContextoContatoDTO;
	}
	
	public function getContatosNomeAutoComplete($strPalavrasPesquisa){
		
		foreach($strPalavrasPesquisa as $i => $usuarios){
			
			$contatoIntimacao = MdPetContatoINT::getDadosContatos($usuarios->getNumIdContato() ,$_GET['id_documento'], false);
			
			if($contatoIntimacao['Intimacao'] == 0){
			
				$objContextoContatoDTO = new ContatoDTO();
				$objContextoContatoDTO->retTodos();
				$objContextoContatoDTO->setNumIdContato($usuarios->getNumIdContato());
				
				$objContatoRN = new ContatoRN();
				$arrContextoContatoDTO[$i] = $objContatoRN->consultarRN0324($objContextoContatoDTO);
			}
			
		}
		return $arrContextoContatoDTO;
	}
        
        public function getContatosNomeAutoCompletePF($strPalavrasPesquisa){
		
		foreach($strPalavrasPesquisa as $i => $usuarios){
			
			$contatoIntimacao = MdPetContatoINT::getDadosContatos($usuarios->getNumIdContato() ,$_GET['id_documento'], false);
			
			if($contatoIntimacao['Intimacao'] == 0){
			
				$objContextoContatoDTO = new ContatoDTO();
				$objContextoContatoDTO->retTodos();
				$objContextoContatoDTO->setNumIdContato($usuarios->getNumIdContato());
				
				$objContatoRN = new ContatoRN();
				$arrContextoContatoDTO[$i] = $objContatoRN->consultarRN0324($objContextoContatoDTO);
			}
			
		}
		
                $xml = '';
                $xml .= '<itens>';
                if ($arrContextoContatoDTO !== null ){
                  foreach($arrContextoContatoDTO as $dto){
                    $xml .= '<item id="'.$dto->get('IdContato').'"';
                    $xml .= ' descricao="'.$dto->get('Nome').'"';
                    $xml .= ' complemento="'.$dto->get('Email').' - '.InfraUtil::formatarCpf($dto->get('Cpf')).'"';
                    $xml .= '></item>';
                  }
                }
                $xml .= '</itens>';
                
                return $xml;
	}
	
	public function getDadosContatos($idContato, $idDocumento, $xml = true){

		

		$arrSituacao = MdPetIntRelDestinatarioINT::getArraySituacaoRelatorio();
		$possuiIntimacao = 0;
		
		$objContextoContatoDTO = new ContatoDTO();
		$objContextoContatoDTO->retTodos();
		$objContextoContatoDTO->setNumIdContato($idContato);
		
		$objContatoRN = new ContatoRN();
		$arrContextoContatoDTO = $objContatoRN->consultarRN0324($objContextoContatoDTO);
		
		//BuscaDestinatrio
		$objDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
		$objDestinatarioDTO->retTodos();
		$objDestinatarioDTO->setNumIdContato($idContato);
		
		$objDestinatarioRN = new MdPetIntRelDestinatarioRN();
		$arrDestinatarioDTO = $objDestinatarioRN->listar($objDestinatarioDTO);

		if(!empty($arrDestinatarioDTO)){
			//Busca Intimacao Documento Principal
			foreach($arrDestinatarioDTO as $destinatario){
				$objDocumentoIntimacaoDTO = new MdPetIntProtocoloDTO();
				$objDocumentoIntimacaoDTO->retTodos();
				$objDocumentoIntimacaoDTO->setNumIdMdPetIntimacao($destinatario->getNumIdMdPetIntimacao());
				$objDocumentoIntimacaoDTO->setDblIdProtocolo($idDocumento);
				$objDocumentoIntimacaoDTO->setStrSinPrincipal('S');
				
				$objDocumentoRN = new MdPetIntProtocoloRN();
				$arrDocumentoIntimacao = $objDocumentoRN->consultar($objDocumentoIntimacaoDTO);
                $qtdArrDocumentoIntimacao = is_array($arrDocumentoIntimacao) ? count($arrDocumentoIntimacao) : 0;
				if($qtdArrDocumentoIntimacao > 0){
					$possuiIntimacao = $destinatario->getNumIdMdPetIntimacao();
					$situacao = !is_null($destinatario->getStrStaSituacaoIntimacao()) && $destinatario->getStrStaSituacaoIntimacao() != 0 ? $arrSituacao[$destinatario->getStrStaSituacaoIntimacao()] :MdPetIntimacaoRN::$STR_SITUACAO_NAO_CADASTRADA;
					$dataIntimacao = $destinatario->getDthDataCadastro() ? substr($destinatario->getDthDataCadastro(),0,10) : '';
				}
			}
			$objIntimacaoRN = new MdPetIntimacaoRN();
			$idIntimacao = $possuiIntimacao ? $possuiIntimacao : '';

			$montaLink = str_replace('&', '&amp;',SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_intimacao_consulta&arvore=1&id_documento='.$idDocumento.'&id_intimacao='.$possuiIntimacao.'&id_contato='.$arrContextoContatoDTO->getNumIdContato()));
		}else{
			$situacao = 'Pendente';
			$possuiIntimacao = 0;
		}
		
		if($xml){

		//Validação 
			$empresas = array();
			$contato = '';
			$total = null;
			
			$dtoMdPetVincReptDTO = new MdPetVincRepresentantDTO();
			$dtoMdPetVincReptDTO->retNumIdContatoVinc();
			$dtoMdPetVincReptDTO->retStrNomeProcurador();
			$dtoMdPetVincReptDTO->setNumIdContatoProcurador($idContato);
			$dtoMdPetVincReptDTO->retNumIdContatoVinc();
			$dtoMdPetVincReptDTO->retStrEmail();
		  //$dtoMdPetVincReptDTO->setDistinct(true);
			$dtoMdPetVincReptDTO->retNumIdContatoProcurador();
			$dtoMdPetVincReptDTO->setStrSinAtivo('S');
			//$dtoMdPetVincReptDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
            $dtoMdPetVincReptDTO->adicionarCriterio(array('StaEstado','StaEstado'),array(InfraDTO::$OPER_IGUAL,InfraDTO::$OPER_IGUAL),array(MdPetVincRepresentantRN::$RP_ATIVO,MdPetVincRepresentantRN::$RP_REVOGADA),InfraDTO::$OPER_LOGICO_OR);

			$rnMdPetVincRepRN = new MdPetVincRepresentantRN();
			$arrObjMdPetVincRepresentantDTO = $rnMdPetVincRepRN->listar($dtoMdPetVincReptDTO);
			
		   foreach ($arrObjMdPetVincRepresentantDTO as $key => $value) {
				$empresas [] = $value->getNumIdContatoVinc();
		   }
		   if(count($empresas)){

		    $objDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
			$objDestinatarioDTO->retTodos();
			$objDestinatarioDTO->setDblIdDocumento($idDocumento);
			$objDestinatarioDTO->setNumIdContato($empresas,InfraDTO::$OPER_IN);
			
			$objDestinatarioRN = new MdPetIntRelDestinatarioRN();
			$arrDestinatarioDTO = $objDestinatarioRN->listar($objDestinatarioDTO);
			$arrContatos = InfraArray::converterArrInfraDTO($arrDestinatarioDTO, 'IdContato');
			if(count($arrContatos)){
			//Recuperando contato
			$objContextoContatoDTO = new ContatoDTO();
			$objContextoContatoDTO->retTodos();
			$objContextoContatoDTO->setNumIdContato($arrContatos,InfraDTO::$OPER_IN);
			$objContatoRN = new ContatoRN();
			$arrContextoContatoJuridicoDTO = $objContatoRN->listarRN0325($objContextoContatoDTO);

			
			//Concatenando cada uma das empresas
			foreach ($arrContextoContatoJuridicoDTO as  $nome) {
				$contato .= "\n * ";
				$contato .= infraUtil::formatarCnpj($nome->getDblCnpj()). " - ".PaginaSEI::tratarHTML($nome->getStrNome()) ;
			}

			$total = count($arrContextoContatoJuridicoDTO);
		}
			

			if(count($arrDestinatarioDTO) > 0){
				$arrDestinatarioDTO = 1;
			}else{
				$arrDestinatarioDTO = 0;
			}
		   
		   }else{
			$arrDestinatarioDTO = 0;   
		   }
   
		   //Fim validação
		   
			$xml = '<Documento>';
			$xml .= '<Id>'. $arrContextoContatoDTO->getNumIdContato() .'</Id>';
			$xml .= '<Nome>'. $arrContextoContatoDTO->getStrNome() .'</Nome>';
			$xml .= '<Email>'. $arrContextoContatoDTO->getStrEmail() .'</Email>';
			$xml .= '<Cpf>'. $arrContextoContatoDTO->getDblCpf() .'</Cpf>';
			$xml .= '<Data>'. substr($arrContextoContatoDTO->getDthCadastro(),0,10) .'</Data>';
			$xml .= '<Situacao>'. $situacao .'</Situacao>';
			$xml .= '<Intimacao>'. $possuiIntimacao .'</Intimacao>';
			$xml .= '<Url>'.$montaLink.'</Url>';
			$xml .= '<DataIntimacao>'. $dataIntimacao .'</DataIntimacao>';
			$xml .= '<Cadastro>'. $arrDestinatarioDTO .'</Cadastro>';
			$xml .= '<Vinculo>'. $contato .'</Vinculo>';
			$xml .= '<Quantidade>'. $total .'</Quantidade>';
			$xml .= '</Documento>';
		}else{
			$xml['Id'] = $arrContextoContatoDTO->getNumIdContato();
			$xml['Nome'] = $arrContextoContatoDTO->getStrNome();
			$xml['Email'] = $arrContextoContatoDTO->getStrEmail();
			$xml['Cpf'] = $arrContextoContatoDTO->getDblCpf();
			$xml['Data'] = substr($arrContextoContatoDTO->getDthCadastro(),0,10);
			$xml['Situacao'] = $situacao;
			$xml['Intimacao'] = $possuiIntimacao;
			$xml['Url'] = $montaLink;
			$xml['DataIntimacao'] = $dataIntimacao;
		}
		
		return $xml;
	}


	//Juridico



	public function getContatosNomeAutoCompleteJuridico($strPalavrasPesquisa){

		foreach($strPalavrasPesquisa as $i => $usuarios){
			
			$contatoIntimacao = MdPetContatoINT::getDadosContatosJuridico($usuarios->getNumIdContatoVinc() ,$_GET['id_documento'], false);
			
			if($contatoIntimacao['Intimacao'] == 0){
			
				$objContextoContatoDTO = new ContatoDTO();
				$objContextoContatoDTO->retTodos();
				$objContextoContatoDTO->setNumIdContato($usuarios->getNumIdContatoVinc());
				
				$objContatoRN = new ContatoRN();
				$arrContextoContatoDTO[$i] = $objContatoRN->consultarRN0324($objContextoContatoDTO);
			}
			
		}
		
		$xml = '';
		$xml .= '<itens>';
		if ($arrContextoContatoDTO !== null ){
		  foreach($arrContextoContatoDTO as $dto){
			$nome = str_replace("<","lt;",$dto->get('Nome'));
			$xml .= '<item id="'.$dto->get('IdContato').'"';
			$xml .= ' complemento=" '.InfraUtil::formatarCnpj($dto->get('Cnpj')).'"';
			$xml .= ' descricao="'.$nome.'"';
			$xml .= '></item>';
		  }
		}
		$xml .= '</itens>';
		
		return $xml;
	}
	
	public function getDadosContatosJuridico($idContato, $idDocumento, $xml = true){

		$arrSituacao = MdPetIntRelDestinatarioINT::getArraySituacaoRelatorio();
		$possuiIntimacao = 0;


		//Juridic

		
		$dtoMdPetVincReptDTO = new MdPetVincRepresentantDTO();
		$dtoMdPetVincReptDTO->setNumIdContatoVinc($idContato);
		$dtoMdPetVincReptDTO->retNumIdContatoVinc();
	   // $dtoMdPetVincReptDTO->setDistinct(true);
		$dtoMdPetVincReptDTO->retNumIdContatoProcurador();
		$dtoMdPetVincReptDTO->retStrRazaoSocialNomeVinc();
		$dtoMdPetVincReptDTO->setStrSinAtivo('S');
		$rnMdPetVincRepRN = new MdPetVincRepresentantRN();
		$arr = $rnMdPetVincRepRN->listar($dtoMdPetVincReptDTO);

		

		
		$objContextoContatoDTO = new ContatoDTO();
		$objContextoContatoDTO->retTodos();
		$objContextoContatoDTO->setNumIdContato($idContato);
		
		$objContatoRN = new ContatoRN();
		$arrContextoContatoDTO = $objContatoRN->consultarRN0324($objContextoContatoDTO);
		
		//BuscaDestinatrio
		$objDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
		$objDestinatarioDTO->retTodos();
		$objDestinatarioDTO->setNumIdContato($idContato);
		
		$objDestinatarioRN = new MdPetIntRelDestinatarioRN();
		$arrDestinatarioDTO = $objDestinatarioRN->listar($objDestinatarioDTO);

		if(!empty($arrDestinatarioDTO)){
			//Busca Intimacao Documento Principal
			foreach($arrDestinatarioDTO as $destinatario){
				$objDocumentoIntimacaoDTO = new MdPetIntProtocoloDTO();
				$objDocumentoIntimacaoDTO->retTodos();
				$objDocumentoIntimacaoDTO->setNumIdMdPetIntimacao($destinatario->getNumIdMdPetIntimacao());
				$objDocumentoIntimacaoDTO->setDblIdProtocolo($idDocumento);
				$objDocumentoIntimacaoDTO->setStrSinPrincipal('S');
				
				$objDocumentoRN = new MdPetIntProtocoloRN();
				$arrDocumentoIntimacao = $objDocumentoRN->consultar($objDocumentoIntimacaoDTO);
                $qtdArrDocumentoIntimacao = is_array($arrDocumentoIntimacao) ? count($arrDocumentoIntimacao) : 0;
				if($qtdArrDocumentoIntimacao > 0){
					$possuiIntimacao = $destinatario->getNumIdMdPetIntimacao();
					$situacao = !is_null($destinatario->getStrStaSituacaoIntimacao()) && $destinatario->getStrStaSituacaoIntimacao() != 0 ? $arrSituacao[$destinatario->getStrStaSituacaoIntimacao()] :MdPetIntimacaoRN::$STR_SITUACAO_NAO_CADASTRADA;
					$dataIntimacao = $destinatario->getDthDataCadastro() ? substr($destinatario->getDthDataCadastro(),0,10) : '';
				}
			}
			$objIntimacaoRN = new MdPetIntimacaoRN();
			$idIntimacao = $possuiIntimacao ? $possuiIntimacao : '';

			$montaLink = str_replace('&', '&amp;',SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_intimacao_consulta&arvore=1&id_documento='.$idDocumento.'&id_intimacao='.$possuiIntimacao.'&id_contato='.$arrContextoContatoDTO->getNumIdContato()));
		}else{
			$situacao = 'Pendente';
			$possuiIntimacao = 0;
		}
		
		if($xml){
			$xml = '<Documento>';
			$xml .= '<Id>'. $arrContextoContatoDTO->getNumIdContato() .'</Id>';
			$xml .= '<Nome>'. PaginaSEI::tratarHTML($arrContextoContatoDTO->getStrNome()) .'</Nome>';
			$xml .= '<Cnpj>'. InfraUtil::formatarCpfCnpj($arrContextoContatoDTO->getDblCnpj()) .'</Cnpj>';
			$xml .= '<Data>'. substr($arrContextoContatoDTO->getDthCadastro(),0,10) .'</Data>';
			$xml .= '<Situacao>'. $situacao .'</Situacao>';
			$xml .= '<Intimacao>'. $possuiIntimacao .'</Intimacao>';
			$xml .= '<Url>'.$montaLink.'</Url>';
			$xml .= '<DataIntimacao>'. $dataIntimacao .'</DataIntimacao>';
			$xml .= '</Documento>';
		}else{
			$xml['Id'] = $arrContextoContatoDTO->getNumIdContato();
			$xml['Nome'] = PaginaSEI::tratarHTML($arr[0]->getStrRazaoSocialNomeVinc());
			$xml['Cnpj'] = InfraUtil::formatarCpfCnpj($arrContextoContatoDTO->getDblCnpj());
			$xml['Data'] = substr($arrContextoContatoDTO->getDthCadastro(),0,10);
			$xml['Situacao'] = $situacao;
			$xml['Intimacao'] = $possuiIntimacao;
			$xml['Url'] = $montaLink;
			$xml['DataIntimacao'] = $dataIntimacao;
		}
		
		return $xml;
	}






	public function getDadosContatosJuridicoRecuperar($idContato, $idDocumento, $xml = true){

		$arrSituacao = MdPetIntRelDestinatarioINT::getArraySituacaoRelatorio();
		$possuiIntimacao = 0;
		
		
		$objContextoContatoDTO = new ContatoDTO();
		$objContextoContatoDTO->retTodos();
		$objContextoContatoDTO->setNumIdContato($idContato);
		
		$objContatoRN = new ContatoRN();
		$arrContextoContatoDTO = $objContatoRN->consultarRN0324($objContextoContatoDTO);
		
		//BuscaDestinatrio
		$objDestinatarioDTO = new MdPetIntRelDestinatarioDTO();
		$objDestinatarioDTO->retTodos();
		$objDestinatarioDTO->setNumIdContato($idContato);
		
		$objDestinatarioRN = new MdPetIntRelDestinatarioRN();
		$arrDestinatarioDTO = $objDestinatarioRN->listar($objDestinatarioDTO);

		if(!empty($arrDestinatarioDTO)){
			//Busca Intimacao Documento Principal
			foreach($arrDestinatarioDTO as $destinatario){
				$objDocumentoIntimacaoDTO = new MdPetIntProtocoloDTO();
				$objDocumentoIntimacaoDTO->retTodos();
				$objDocumentoIntimacaoDTO->setNumIdMdPetIntimacao($destinatario->getNumIdMdPetIntimacao());
				$objDocumentoIntimacaoDTO->setDblIdProtocolo($idDocumento);
				$objDocumentoIntimacaoDTO->setStrSinPrincipal('S');
				
				$objDocumentoRN = new MdPetIntProtocoloRN();
				$arrDocumentoIntimacao = $objDocumentoRN->consultar($objDocumentoIntimacaoDTO);
				if(is_object($arrDocumentoIntimacao)){
					$possuiIntimacao = $destinatario->getNumIdMdPetIntimacao();
					$situacao = !is_null($destinatario->getStrStaSituacaoIntimacao()) && $destinatario->getStrStaSituacaoIntimacao() != 0 ? $arrSituacao[$destinatario->getStrStaSituacaoIntimacao()] :MdPetIntimacaoRN::$STR_SITUACAO_NAO_CADASTRADA;
					$dataIntimacao = $destinatario->getDthDataCadastro() ? substr($destinatario->getDthDataCadastro(),0,10) : '';
				}
			}
			$objIntimacaoRN = new MdPetIntimacaoRN();
			$idIntimacao = $possuiIntimacao ? $possuiIntimacao : '';

			$montaLink = str_replace('&', '&amp;',SessaoSEI::getInstance()->assinarLink('controlador.php?acao=md_pet_intimacao_consulta&arvore=1&id_documento='.$idDocumento.'&id_intimacao='.$possuiIntimacao.'&id_contato='.$arrContextoContatoDTO->getNumIdContato()));
		}else{
			$situacao = 'Pendente';
			$possuiIntimacao = 0;
		}
		
		if(!$xml){
			$xml['Id'] = $arrContextoContatoDTO->getNumIdContato();
			$xml['Nome'] = PaginaSEI::tratarHTML($arrContextoContatoDTO->getStrNome());
			$xml['Cnpj'] = $arrContextoContatoDTO->getDblCnpj();
			$xml['Data'] = substr($arrContextoContatoDTO->getDthCadastro(),0,10);
			$xml['Situacao'] = $situacao;
			$xml['Intimacao'] = $possuiIntimacao;
			$xml['Url'] = $montaLink;
			$xml['DataIntimacao'] = $dataIntimacao;
		}
		
		return $xml;
	}
	
	
}
?>