<?
/**
 * ANATEL
 *
 * Controlador responsavel por açoes do usuario externo
 * 21/06/2016 - criado por marcelo.bezerra@cast.com.br - CAST
 *
 */
 class PeticionamentoControladorExterno implements ISeiControlador {

		public function processar($strAcao){
			
		  switch ($strAcao){
		    
		    case 'pagina_conteudo_externo_peticionamento':
		  		require_once dirname ( __FILE__ ) . '/pagina_conteudo_externo_peticionamento.php';
		  		return true;
		  		
		  	case 'indisponibilidade_peticionamento_usuario_externo_listar' :
		  		require_once dirname ( __FILE__ ) . '/indisponibilidade_peticionamento_usuario_externo_lista.php';
		  		return true;
		  			
  		    case 'indisponibilidade_peticionamento_usuario_externo_consultar':
		    case 'indisponibilidade_peticionamento_usuario_externo_download':
		  		require_once dirname ( __FILE__ ) . '/indisponibilidade_peticionamento_usuario_externo_cadastro.php';
		  		return true;
		    
		  	//novo peticionamento - 5152	
		  		case 'peticionamento_usuario_externo_iniciar':
		  			require_once dirname ( __FILE__ ) . '/peticionamento_usuario_externo_inicio.php';
		  			return true;
		  	
		  	case 'peticionamento_usuario_externo_cadastrar':
		  		require_once dirname ( __FILE__ ) . '/peticionamento_usuario_externo_cadastro.php';
		  		return true;
		  		
		  	case 'peticionamento_interessado_usuario_externo_cadastrar':
		  			require_once dirname ( __FILE__ ) . '/peticionamento_interessado_usuario_externo_cadastro.php';
		  			return true;

		  	case 'peticionamento_usuario_externo_concluir':
		  		require_once dirname ( __FILE__ ) . '/peticionamento_usuario_externo_concluir.php';
		  		return true;

		  	//consulta de recibo - 5153	
		  	case 'recibo_peticionamento_usuario_externo_listar':
		  		require_once dirname ( __FILE__ ) . '/recibo_peticionamento_usuario_externo_lista.php';
		  		return true;
		  	
		  	case 'recibo_peticionamento_usuario_externo_consultar':
		  			require_once dirname ( __FILE__ ) . '/recibo_peticionamento_usuario_externo_consulta.php';
		  			return true;
		  			
  			case 'peticionamento_interessado_cadastro':
  				    require_once dirname ( __FILE__ ) . '/peticionamento_interessado_cadastro.php';
  				    return true;
  				    
  		    case 'peticionamento_contato_selecionar':
  		    		require_once dirname ( __FILE__ ) . '/peticionamento_contato_selecionar.php';
  				    return true;  	
  				    
  			case 'peticionamento_usuario_externo_upload_anexo':
		  		if (isset($_FILES['fileArquivoEssencial'])){
		  			PaginaSEIExterna::getInstance()->processarUpload('fileArquivoEssencial', DIR_SEI_TEMP, true);
		  		}
		  		die;
		  	
		  	case 'peticionamento_usuario_externo_upload_doc_principal':
		  		
		  		if (isset($_FILES['fileArquivoPrincipal'])){
		  			
		  			PaginaSEIExterna::getInstance()->processarUpload('fileArquivoPrincipal', DIR_SEI_TEMP, true);
		  		}
		  		die;
		  		
		  	case 'peticionamento_usuario_externo_upload_doc_essencial':
		  		
		  		if (isset($_FILES['fileArquivoEssencial'])){
		  			
		  			PaginaSEIExterna::getInstance()->processarUpload('fileArquivoEssencial', DIR_SEI_TEMP, true);
		  		}
		  		die;

			case 'peticionamento_usuario_externo_upload_doc_complementar':
		  		
		  		if (isset($_FILES['fileArquivoComplementar'])){
		  			
		  			PaginaSEIExterna::getInstance()->processarUpload('fileArquivoComplementar', DIR_SEI_TEMP, true);
		  		}
		  		die;
  				
  			case 'peticionamento_usuario_externo_upload_anexo':
  			case 'peticionamento_usuario_externo_download':
  					require_once dirname ( __FILE__ ) . '/peticionamento_usuario_externo_cadastro.php';
  					return true;
  					  				
  			case 'editor_peticionamento_montar':
  			case 'editor_peticionamento_imagem_upload':
  			    	//case 'editor_salvar': enviada diretamente para a página editor_processar.php para tratatamento de troca de unidade com documento aberto
  			    	require_once dirname ( __FILE__ ) . '/editor_peticionamento_processar.php';
  			    	return true;
  			
  			case 'validar_documento_principal':
  				
  				  $conteudo = "";
  				  
  				  if( SessaoSEIExterna::getInstance()->isSetAtributo('docPrincipalConteudoHTML') ){
  				  	$conteudo = SessaoSEIExterna::getInstance()->getAtributo('docPrincipalConteudoHTML');
  				  }
  				  
  				  echo $conteudo;
  				  return true;
  			    	
  			case 'contato_cpf_cnpj':
  			    
  				$cpfcnpj =  $_POST['cpfcnpj'];
  				$cpfcnpj = str_replace(".","", $cpfcnpj );
  				$cpfcnpj = str_replace("-","", $cpfcnpj );
  				$cpfcnpj = str_replace("/","", $cpfcnpj );
  				
  				$total = ContatoPeticionamentoINT::getTotalContatoByCPFCNPJ( $cpfcnpj );
  				$json = null;
  				
  				if( $total == 1 ) {
  				
	  				$objContatoDTO = ContatoPeticionamentoINT::getContatoByCPFCNPJ( $cpfcnpj );
	  				
	  				if( $objContatoDTO != null){
	  				  $objContato = new stdClass();
	  				  $objContato->nome =  utf8_encode( $objContatoDTO->getStrNome() );
	  				  $objContato->id = utf8_encode( $objContatoDTO->getNumIdContato() );  				
	  			      $json = json_encode( $objContato , JSON_FORCE_OBJECT);
	  				}
  				
  				}
  				
  				echo $json;
  			    
  			    return true;
		  }
		  
		  return false;
		
		}
		
	}
?>