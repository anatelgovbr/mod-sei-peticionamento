<?
/**
* ANATEL
*
* 01/08/2016 - criado por marcelo.bezerra@cast.com.br - CAST
*
* Controle de aчѕes principais do cadastro de peticionamento
*
*/
  
  switch($_GET['acao']){
    
  	//TODO migrar aчѕes de download para serem tratadas diretamente no controlador, como foi feito com upload
  	case 'peticionamento_usuario_externo_download':
  		
  		$file = DIR_SEI_TEMP . '/' . $_POST['hdnNomeArquivoDownload'];
  		 
  		if (file_exists($file)) {
  	
  			header('Pragma: public');
  			header("Cache-Control: private, no-cache, no-store, post-check=0, pre-check=0");
  			header('Expires: 0');
  			header('Content-Description: File Transfer');
  			header('Content-Type: application/octet-stream');
  			header('Content-Disposition: attachment; filename="'. $_POST['hdnNomeArquivoDownloadReal'] .'"');
  			header('Content-Length: ' . filesize($file));
  			readfile($file, true);
  			exit;
  		}
  		 
  		die;
  	
  	//aчѕes de upload serao tratadas diretamente pelo controlador
  			
  	case 'peticionamento_usuario_externo_cadastrar':
  		$strTitulo = 'Peticionar Processo Novo';
  		break;
  		
    default:
      throw new InfraException("Aчуo '".$_GET['acao']."' nуo reconhecida.");
  }
?>