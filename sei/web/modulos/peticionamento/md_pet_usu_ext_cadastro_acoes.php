<?
/**
* ANATEL
*
* 01/08/2016 - criado por marcelo.bezerra@cast.com.br - CAST
*
* Controle de ações principais do cadastro de peticionamento
*
*/
  
  switch($_GET['acao']){

  	case 'md_pet_usu_ext_cadastrar':
  		$strTitulo = 'Peticionamento de Processo Novo';
  		break;
  		
    default:
      throw new InfraException("Ação '".$_GET['acao']."' não reconhecida.");
  }
?>