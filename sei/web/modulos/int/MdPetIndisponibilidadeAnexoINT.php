<?
/**
* ANATEL
*
* 29/04/2016 - criado por jaqueline.mendes@cast.com.br - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIndisponibilidadeAnexoINT extends InfraINT {

 public static function processarAnexo($strAnexos){
     	
    $arrAnexos = PaginaSEI::getInstance()->getArrItensTabelaDinamica($strAnexos);
    $arrObjAnexoDTO = array();
    
    foreach($arrAnexos as $anexo){
      $objMdPetIndisponibilidadeAnexoDTO = new MdPetIndisponibilidadeAnexoDTO();
      $objMdPetIndisponibilidadeAnexoDTO->setNumIdAnexoPeticionamento($anexo[0]);
      //$objMdPetIndisponibilidadeAnexoDTO->setNumIdAnexoPeticionamento( null );
      $objMdPetIndisponibilidadeAnexoDTO->setStrHash($anexo[0]);
      $objMdPetIndisponibilidadeAnexoDTO->setStrNome($anexo[1]);
      $objMdPetIndisponibilidadeAnexoDTO->setDthInclusao($anexo[2]);
      $objMdPetIndisponibilidadeAnexoDTO->setNumTamanho($anexo[3]);
      $objMdPetIndisponibilidadeAnexoDTO->setStrSiglaUsuario($anexo[5]);
      $objMdPetIndisponibilidadeAnexoDTO->setStrSiglaUnidade($anexo[6]);
      $objMdPetIndisponibilidadeAnexoDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
      $arrObjAnexoDTO[] = $objMdPetIndisponibilidadeAnexoDTO;
    }
    
    return $arrObjAnexoDTO;
  }
  
  public static function montarAnexosIndisponibilidade($numIdIndisponibilidade, $bolAcaoDownload, &$arrAcoesDownload, $bolAcaoRemoverAnexo, &$arrAcoesRemover){
  	
  	if ($numIdIndisponibilidade!=null){
  		$objMdPetIndisponibilidadeAnexoRN  = new MdPetIndisponibilidadeAnexoRN();
  		$objMdPetIndisponibilidadeAnexoDTO = new MdPetIndisponibilidadeAnexoDTO();
  		$objMdPetIndisponibilidadeAnexoDTO->retTodos();
  		
  		$objMdPetIndisponibilidadeAnexoDTO->retStrSiglaUsuario();
  		$objMdPetIndisponibilidadeAnexoDTO->retStrSiglaUnidade();
  		
  		$objMdPetIndisponibilidadeAnexoDTO->setNumIdIndisponibilidade($numIdIndisponibilidade);
  		$objMdPetIndisponibilidadeAnexoDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);
  		$objMdPetIndisponibilidadeAnexoDTO->setOrdDthInclusao(InfraDTO::$TIPO_ORDENACAO_DESC);
  
  		$arrObjMdPetIndisponibilidadeAnexoDTO = $objMdPetIndisponibilidadeAnexoRN->listar($objMdPetIndisponibilidadeAnexoDTO);
  
  		$arr = array();
  		$arrAcoesDownload = array();
  		$arrAcoesRemover = array();
  
  		foreach($arrObjMdPetIndisponibilidadeAnexoDTO as $objMdPetIndisponibilidadeAnexoDTO){
  
  			$arr[] = array($objMdPetIndisponibilidadeAnexoDTO->getNumIdAnexoPeticionamento(),
  					PaginaSEI::getInstance()->formatarXHTML($objMdPetIndisponibilidadeAnexoDTO->getStrNome()),
  					$objMdPetIndisponibilidadeAnexoDTO->getDthInclusao(),
  					$objMdPetIndisponibilidadeAnexoDTO->getNumTamanho(),
  					InfraUtil::formatarTamanhoBytes($objMdPetIndisponibilidadeAnexoDTO->getNumTamanho()),
  					$objMdPetIndisponibilidadeAnexoDTO->getStrSiglaUsuario(),
  					$objMdPetIndisponibilidadeAnexoDTO->getStrSiglaUnidade());
  			 
  			if ($bolAcaoDownload){
  				$arrAcoesDownload[$objMdPetIndisponibilidadeAnexoDTO->getNumIdAnexoPeticionamento()] = '<a href="'.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=base_conhecimento_download_anexo&id_anexo='.$objMdPetIndisponibilidadeAnexoDTO->getNumIdAnexoPeticionamento())).'" target="_blank"><img src="imagens/download.gif" title="Baixar Anexo" alt="Baixar Anexo" class="infraImg" /></a> ';
  			}
  
  			if ($bolAcaoRemoverAnexo && $objMdPetIndisponibilidadeAnexoDTO->getStrSiglaUnidade()==SessaoSEI::getInstance()->getStrSiglaUnidadeAtual()){
  				$arrAcoesRemover[$objMdPetIndisponibilidadeAnexoDTO->getNumIdAnexoPeticionamento()] = true;
  			}
  		}
  
  		return PaginaSEI::getInstance()->gerarItensTabelaDinamica($arr);
  	}
  }
  
  public static function montarAnexosIndisponibilidadeExterno($numIdIndisponibilidade, $bolAcaoDownload, &$arrAcoesDownload, $bolAcaoRemoverAnexo, &$arrAcoesRemover){
  	 
  	if ($numIdIndisponibilidade!=null){
  		
  		$objMdPetIndisponibilidadeAnexoRN  = new MdPetIndisponibilidadeAnexoRN();
  		$objMdPetIndisponibilidadeAnexoDTO = new MdPetIndisponibilidadeAnexoDTO();
  		$objMdPetIndisponibilidadeAnexoDTO->retTodos();
  
  		$objMdPetIndisponibilidadeAnexoDTO->retStrSiglaUsuario();
  		$objMdPetIndisponibilidadeAnexoDTO->retStrSiglaUnidade();
  
  		$objMdPetIndisponibilidadeAnexoDTO->setNumIdIndisponibilidade($numIdIndisponibilidade);
  		$objMdPetIndisponibilidadeAnexoDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);
  		$objMdPetIndisponibilidadeAnexoDTO->setOrdDthInclusao(InfraDTO::$TIPO_ORDENACAO_DESC);
  
  		$arrObjMdPetIndisponibilidadeAnexoDTO = $objMdPetIndisponibilidadeAnexoRN->listar($objMdPetIndisponibilidadeAnexoDTO);
  
  		$arr = array();
  		$arrAcoesDownload = array();
  		$arrAcoesRemover = array();
  
  		foreach($arrObjMdPetIndisponibilidadeAnexoDTO as $objMdPetIndisponibilidadeAnexoDTO){
  
  			$arr[] = array($objMdPetIndisponibilidadeAnexoDTO->getNumIdAnexoPeticionamento(),
  					PaginaSEI::getInstance()->formatarXHTML($objMdPetIndisponibilidadeAnexoDTO->getStrNome()),
  					$objMdPetIndisponibilidadeAnexoDTO->getDthInclusao(),
  					$objMdPetIndisponibilidadeAnexoDTO->getNumTamanho(),
  					InfraUtil::formatarTamanhoBytes($objMdPetIndisponibilidadeAnexoDTO->getNumTamanho()),
  					$objMdPetIndisponibilidadeAnexoDTO->getStrSiglaUsuario(),
  					$objMdPetIndisponibilidadeAnexoDTO->getStrSiglaUnidade());
  
  			if ($bolAcaoDownload){
  				$arrAcoesDownload[$objMdPetIndisponibilidadeAnexoDTO->getNumIdAnexoPeticionamento()] = '<a href="'
  					 .PaginaSEIExterna::getInstance()->formatarXHTML(
  					 SessaoSEIExterna::getInstance()->assinarLink('controlador.php?acao=base_conhecimento_download_anexo&id_anexo='
  					 .$objMdPetIndisponibilidadeAnexoDTO->getNumIdAnexoPeticionamento())).'" target="_blank"><img src="imagens/download.gif" title="Baixar Anexo" alt="Baixar Anexo" class="infraImg" /></a> ';
  			}
  
  			if ($bolAcaoRemoverAnexo && $objMdPetIndisponibilidadeAnexoDTO->getStrSiglaUnidade()==SessaoSEIExterna::getInstance()->getStrSiglaUnidadeAtual()){
  				$arrAcoesRemover[$objMdPetIndisponibilidadeAnexoDTO->getNumIdAnexoPeticionamento()] = true;
  			}
  		}
  
  		return PaginaSEIExterna::getInstance()->gerarItensTabelaDinamica($arr);
  	}
  }


}