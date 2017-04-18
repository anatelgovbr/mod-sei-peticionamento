<?
	try{
	
    require_once dirname(__FILE__).'/../web/SEI.php';

    session_start();
		
		SessaoSEI::getInstance(false);
		
		$objVersaoRN = new MdPetAtualizadorRN();
		$objVersaoRN->atualizarVersao();
        exit;

	}catch(Exception $e){
		echo(InfraException::inspecionar($e));
		try{LogSEI::getInstance()->gravar(InfraException::inspecionar($e));	}catch (Exception $e){}
	}
?>