<?

	class PeticionamentoControlador implements ISeiControlador {

		public function processar($strAcao){
			
		  switch ($strAcao){
			
		  case 'gerir_extensoes_arquivo_peticionamento_cadastrar' :
				require_once dirname ( __FILE__ ) . '/gerir_extensoes_arquivo_peticionamento_cadastro.php';
				return true;
				
		   case 'gerir_tamanho_arquivo_peticionamento_cadastrar' :
					require_once dirname ( __FILE__ ) . '/gerir_tamanho_arquivo_peticionamento_cadastro.php';
					return true;
					
			case 'indisponibilidade_peticionamento_listar' :
			case 'indisponibilidade_peticionamento_desativar' :
			case 'indisponibilidade_peticionamento_reativar' :
			case 'indisponibilidade_peticionamento_excluir' :
			    	require_once dirname ( __FILE__ ) . '/indisponibilidade_peticionamento_lista.php';
					return true;
					
		    case 'indisponibilidade_peticionamento_cadastrar':
		    case 'indisponibilidade_peticionamento_consultar':
		    case 'indisponibilidade_peticionamento_alterar':
		    case 'indisponibilidade_peticionamento_upload_anexo':
		    case 'indisponibilidade_peticionamento_download':
					require_once dirname ( __FILE__ ) . '/indisponibilidade_peticionamento_cadastro.php';
					return true;
					
		    case 'tipo_processo_peticionamento_listar' :
		    case 'tipo_processo_peticionamento_desativar' :
		    case 'tipo_processo_peticionamento_reativar':
		    case 'tipo_processo_peticionamento_excluir':
		    	require_once dirname ( __FILE__ ) . '/tipo_processo_peticionamento_lista.php';
		    	return true;	
		    	
		    case 'tipo_processo_peticionamento_cadastrar':
		    case 'tipo_processo_peticionamento_alterar':
		    case 'tipo_processo_peticionamento_consultar':
		    case 'tipo_processo_peticionamento_salvar':
		    	require_once dirname ( __FILE__ ) . '/tipo_processo_peticionamento_cadastro.php';
		    	return true;
		    	
		   case 'tipo_processo_peticionamento_cadastrar_orientacoes':
		   		require_once dirname ( __FILE__ ) . '/tipo_processo_peticionamento_cadastro_orientacoes.php';
		   		return true;
		    	
		   case 'tipo_procedimento_selecionar':
		      		require_once dirname ( __FILE__ ) . '/tipo_procedimento_lista.php';
		    		return true;
		    		
		   case 'serie_peticionamento_selecionar':
		    	require_once dirname ( __FILE__ ) . '/serie_peticionamento_lista.php';
		    	return true;
		    	
		   case 'menu_peticionamento_usuario_externo_listar' :
	    	case 'menu_peticionamento_usuario_externo_desativar' :
	    	case 'menu_peticionamento_usuario_externo_reativar':
	    	case 'menu_peticionamento_usuario_externo_excluir':
	    		require_once dirname ( __FILE__ ) . '/menu_peticionamento_usuario_externo_lista.php';
	    		return true;
	    		 
	    	case 'menu_peticionamento_usuario_externo_cadastrar':
	    	case 'menu_peticionamento_usuario_externo_alterar':
	    	case 'menu_peticionamento_usuario_externo_consultar':
	    		require_once dirname ( __FILE__ ) . '/menu_peticionamento_usuario_externo_cadastro.php';
	    		return true;
	    		
	    	case 'pagina_conteudo_externo_peticionamento':
	    		require_once dirname ( __FILE__ ) . '/pagina_conteudo_externo_peticionamento.php';
	    		return true;
	    			
	    	case 'gerir_tipo_contexto_peticionamento_cadastrar':
	    		require_once dirname ( __FILE__ ) . '/gerir_tipo_contexto_peticionamento_cadastro.php';
	    		return true;
	    			
	    	case 'hipotese_legal_nl_acesso_peticionamento_cadastrar':
	    		require_once dirname ( __FILE__ ).'/hipotese_legal_nl_acesso_peticionamento_cadastro.php';
	    		return true;
	    		
	       case 'hipotese_legal_peticionamento_selecionar':
	       	  require_once dirname ( __FILE__ ).'/hipotese_legal_peticionamento_lista.php';
	    	  return true;

	       case 'arquivo_extensao_peticionamento_selecionar':
				require_once dirname ( __FILE__ ).'/arquivo_extensao_peticionamento_lista.php';
				break;
	    	  
		}
			return false;
		}
		
	}
?>