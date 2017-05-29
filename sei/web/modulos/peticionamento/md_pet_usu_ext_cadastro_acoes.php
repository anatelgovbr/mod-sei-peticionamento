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

  	case 'md_pet_usu_ext_cadastrar':
  		$strTitulo = 'Peticionar Processo Novo';
  		break;
  		
    default:
      throw new InfraException("Aчуo '".$_GET['acao']."' nуo reconhecida.");
  }
?>