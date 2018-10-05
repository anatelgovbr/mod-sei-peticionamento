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

				if(count($arrDocumentoIntimacao) > 0){
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
			$xml .= '<Nome>'. $arrContextoContatoDTO->getStrNome() .'</Nome>';
			$xml .= '<Email>'. $arrContextoContatoDTO->getStrEmail() .'</Email>';
			$xml .= '<Cpf>'. $arrContextoContatoDTO->getDblCpf() .'</Cpf>';
			$xml .= '<Data>'. substr($arrContextoContatoDTO->getDthCadastro(),0,10) .'</Data>';
			$xml .= '<Situacao>'. $situacao .'</Situacao>';
			$xml .= '<Intimacao>'. $possuiIntimacao .'</Intimacao>';
			$xml .= '<Url>'.$montaLink.'</Url>';
			$xml .= '<DataIntimacao>'. $dataIntimacao .'</DataIntimacao>';
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
	
}
?>