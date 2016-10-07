<?
/**
* ANATEL
*
* 29/04/2016 - criado por jaqueline.mendes@cast.com.br - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class IndisponibilidadeAnexoPeticionamentoINT extends InfraINT {

 public static function processarAnexo($strAnexos){
     	
 	$arrAnexos = PaginaSEI::getInstance()->getArrItensTabelaDinamica($strAnexos);
 	$arrObjAnexoDTO = array();
    
    foreach($arrAnexos as $anexo){
      $objIndisponibilidadeAnexoDTO = new IndisponibilidadeAnexoPeticionamentoDTO();
      $objIndisponibilidadeAnexoDTO->setNumIdAnexoPeticionamento($anexo[0]);
      //$objIndisponibilidadeAnexoDTO->setNumIdAnexoPeticionamento( null );
      $objIndisponibilidadeAnexoDTO->setStrHash($anexo[0]);
      $objIndisponibilidadeAnexoDTO->setStrNome($anexo[1]);
      $objIndisponibilidadeAnexoDTO->setDthInclusao($anexo[2]);
      $objIndisponibilidadeAnexoDTO->setNumTamanho($anexo[3]);
      $objIndisponibilidadeAnexoDTO->setStrSiglaUsuario($anexo[5]);
      $objIndisponibilidadeAnexoDTO->setStrSiglaUnidade($anexo[6]);
      $objIndisponibilidadeAnexoDTO->setNumIdUsuario(SessaoSEI::getInstance()->getNumIdUsuario());
      $arrObjAnexoDTO[] = $objIndisponibilidadeAnexoDTO;
    }
    
    return $arrObjAnexoDTO;
  }
  
  public static function montarAnexosIndisponibilidade($numIdIndisponibilidade, $bolAcaoDownload, &$arrAcoesDownload, $bolAcaoRemoverAnexo, &$arrAcoesRemover){
  	
  	if ($numIdIndisponibilidade!=null){
  		$objIndisponibilidadeAnexoRN  = new IndisponibilidadeAnexoPeticionamentoRN();
  		$objIndisponibilidadeAnexoDTO = new IndisponibilidadeAnexoPeticionamentoDTO();
  		$objIndisponibilidadeAnexoDTO->retTodos();
  		
  		$objIndisponibilidadeAnexoDTO->retStrSiglaUsuario();
  		$objIndisponibilidadeAnexoDTO->retStrSiglaUnidade();
  		
  		$objIndisponibilidadeAnexoDTO->setNumIdIndisponibilidade($numIdIndisponibilidade);
  		$objIndisponibilidadeAnexoDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);
  		$objIndisponibilidadeAnexoDTO->setOrdDthInclusao(InfraDTO::$TIPO_ORDENACAO_DESC);
  
  		$arrObjIndisponibilidadeAnexoDTO = $objIndisponibilidadeAnexoRN->listar($objIndisponibilidadeAnexoDTO);
  
  		$arr = array();
  		$arrAcoesDownload = array();
  		$arrAcoesRemover = array();
  
  		foreach($arrObjIndisponibilidadeAnexoDTO as $objIndisponibilidadeAnexoDTO){
  
  			$arr[] = array($objIndisponibilidadeAnexoDTO->getNumIdAnexoPeticionamento(),
  					PaginaSEI::getInstance()->formatarXHTML($objIndisponibilidadeAnexoDTO->getStrNome()),
  					$objIndisponibilidadeAnexoDTO->getDthInclusao(),
  					$objIndisponibilidadeAnexoDTO->getNumTamanho(),
  					InfraUtil::formatarTamanhoBytes($objIndisponibilidadeAnexoDTO->getNumTamanho()),
  					$objIndisponibilidadeAnexoDTO->getStrSiglaUsuario(),
  					$objIndisponibilidadeAnexoDTO->getStrSiglaUnidade());
  			 
  			if ($bolAcaoDownload){
  				$arrAcoesDownload[$objIndisponibilidadeAnexoDTO->getNumIdAnexoPeticionamento()] = '<a href="'.PaginaSEI::getInstance()->formatarXHTML(SessaoSEI::getInstance()->assinarLink('controlador.php?acao=base_conhecimento_download_anexo&id_anexo='.$objIndisponibilidadeAnexoDTO->getNumIdAnexoPeticionamento())).'" target="_blank"><img src="imagens/download.gif" title="Baixar Anexo" alt="Baixar Anexo" class="infraImg" /></a> ';
  			}
  
  			if ($bolAcaoRemoverAnexo && $objIndisponibilidadeAnexoDTO->getStrSiglaUnidade()==SessaoSEI::getInstance()->getStrSiglaUnidadeAtual()){
  				$arrAcoesRemover[$objIndisponibilidadeAnexoDTO->getNumIdAnexoPeticionamento()] = true;
  			}
  		}
  
  		return PaginaSEI::getInstance()->gerarItensTabelaDinamica($arr);
  	}
  }
  
  public static function montarAnexosIndisponibilidadeExterno($numIdIndisponibilidade, $bolAcaoDownload, &$arrAcoesDownload, $bolAcaoRemoverAnexo, &$arrAcoesRemover){
  	 
  	if ($numIdIndisponibilidade!=null){
  		
  		$objIndisponibilidadeAnexoRN  = new IndisponibilidadeAnexoPeticionamentoRN();
  		$objIndisponibilidadeAnexoDTO = new IndisponibilidadeAnexoPeticionamentoDTO();
  		$objIndisponibilidadeAnexoDTO->retTodos();
  
  		$objIndisponibilidadeAnexoDTO->retStrSiglaUsuario();
  		$objIndisponibilidadeAnexoDTO->retStrSiglaUnidade();
  
  		$objIndisponibilidadeAnexoDTO->setNumIdIndisponibilidade($numIdIndisponibilidade);
  		$objIndisponibilidadeAnexoDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);
  		$objIndisponibilidadeAnexoDTO->setOrdDthInclusao(InfraDTO::$TIPO_ORDENACAO_DESC);
  
  		$arrObjIndisponibilidadeAnexoDTO = $objIndisponibilidadeAnexoRN->listar($objIndisponibilidadeAnexoDTO);
  
  		$arr = array();
  		$arrAcoesDownload = array();
  		$arrAcoesRemover = array();
  
  		foreach($arrObjIndisponibilidadeAnexoDTO as $objIndisponibilidadeAnexoDTO){
  
  			$arr[] = array($objIndisponibilidadeAnexoDTO->getNumIdAnexoPeticionamento(),
  					PaginaSEI::getInstance()->formatarXHTML($objIndisponibilidadeAnexoDTO->getStrNome()),
  					$objIndisponibilidadeAnexoDTO->getDthInclusao(),
  					$objIndisponibilidadeAnexoDTO->getNumTamanho(),
  					InfraUtil::formatarTamanhoBytes($objIndisponibilidadeAnexoDTO->getNumTamanho()),
  					$objIndisponibilidadeAnexoDTO->getStrSiglaUsuario(),
  					$objIndisponibilidadeAnexoDTO->getStrSiglaUnidade());
  
  			if ($bolAcaoDownload){
  				$arrAcoesDownload[$objIndisponibilidadeAnexoDTO->getNumIdAnexoPeticionamento()] = '<a href="' 
  					 .PaginaSEIExterna::getInstance()->formatarXHTML(
  					 SessaoSEIExterna::getInstance()->assinarLink('controlador.php?acao=base_conhecimento_download_anexo&id_anexo='
  					 .$objIndisponibilidadeAnexoDTO->getNumIdAnexoPeticionamento())).'" target="_blank"><img src="imagens/download.gif" title="Baixar Anexo" alt="Baixar Anexo" class="infraImg" /></a> ';
  			}
  
  			if ($bolAcaoRemoverAnexo && $objIndisponibilidadeAnexoDTO->getStrSiglaUnidade()==SessaoSEIExterna::getInstance()->getStrSiglaUnidadeAtual()){
  				$arrAcoesRemover[$objIndisponibilidadeAnexoDTO->getNumIdAnexoPeticionamento()] = true;
  			}
  		}
  
  		return PaginaSEIExterna::getInstance()->gerarItensTabelaDinamica($arr);
  	}
  }


}