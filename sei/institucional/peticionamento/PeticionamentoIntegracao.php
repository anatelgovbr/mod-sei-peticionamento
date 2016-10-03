<?
/**
 * ANATEL
 *
 * 21/06/2016 - criado por marcelo.bezerra@cast.com.br - CAST
 *
 */

require_once dirname(__FILE__).'/util/DataUtils.php';

class PeticionamentoIntegracao extends SeiIntegracao {
		
	public function __construct(){
	}
			
	//EU 7352 - Icone exibido na tela interna do processo (Controle de Processos -> clicar em algum processo da lista)
	public function montarIconeProcedimento(SeiIntegracaoDTO $objSeiIntegracaoDTO){
		
		$reciboRN = new ReciboPeticionamentoRN();
		$arrSeiNoAcaoDTO = array();
		
		$idProcedimento = null;
		
		if( $objSeiIntegracaoDTO != null && $objSeiIntegracaoDTO->isSetObjProcedimentoDTO() ){
			$idProcedimento = $objSeiIntegracaoDTO->getObjProcedimentoDTO()->getDblIdProcedimento();
		}
		
		//verificar se este processo é de peticionamento
		$reciboDTO = new ReciboPeticionamentoDTO();
		$reciboDTO->retNumIdProtocolo();
		$reciboDTO->retDthDataHoraRecebimentoFinal();
		$reciboDTO->setNumIdProtocolo( $idProcedimento );
		$arrRecibos = $reciboRN->listar( $reciboDTO );
		
		if( $arrRecibos != null && count( $arrRecibos ) > 0){
			
			 $recibo = $arrRecibos[0];
			 $data = DataUtils::setFormat($recibo->getDthDataHoraRecebimentoFinal(), 'dd/mm/yyyy');
			 $title = 'Peticionamento Eletrônico\nProcesso Novo: ' . $data;
			 
			 //$link = SessaoSEI::getInstance()->assinarLink('controlador.php?acao=protocolo_ciencia_listar&acao_origem=procedimento_visualizar&id_procedimento='. $idProcedimento . '&arvore=1');
			 
			 $seiAcaoDTO = new SeiNoAcaoDTO();
			 $seiAcaoDTO->setStrTipo('PETICIONAMENTO');
			 $seiAcaoDTO->setStrId('PET');
			 $seiAcaoDTO->setStrIdPai($idProcedimento);
			 $seiAcaoDTO->setStrHref('javascript:;');
			 $seiAcaoDTO->setStrTarget('ifrVisualizacao');
			 $seiAcaoDTO->setStrTitle( $title );
			 $seiAcaoDTO->setStrIcone('institucional/peticionamento/imagens/peticionamento_processo_novo.png');
			 $seiAcaoDTO->setBolHabilitado(true);
			 $arrSeiNoAcaoDTO[] = $seiAcaoDTO;			 
			
		}
		
		return $arrSeiNoAcaoDTO;
	}
		
	//EU 7352 - Icone exibido na tela "Controle de Processos"
	public function montarIconeControleProcessos($arrObjProcedimentoDTO){
		
		$reciboRN = new ReciboPeticionamentoRN();
		$arrParam = array();
		
		if( $arrObjProcedimentoDTO != null && count( $arrObjProcedimentoDTO ) > 0 ){
			
			foreach( $arrObjProcedimentoDTO as $procDTO ){
				
				//verificar se este processo é de peticionamento
				$reciboDTO = new ReciboPeticionamentoDTO();
				$reciboDTO->retNumIdProtocolo();
				$reciboDTO->retDthDataHoraRecebimentoFinal();
				$reciboDTO->setNumIdProtocolo($procDTO->getDblIdProcedimento());
				$arrRecibos = $reciboRN->listar( $reciboDTO );
				
				if( $arrRecibos != null && count( $arrRecibos ) > 0){
					
				  $recibo = $arrRecibos[0];	
				  $data = DataUtils::setFormat($recibo->getDthDataHoraRecebimentoFinal(), 'dd/mm/yyyy');
				  $linhaDeCima = '"Peticionamento Eletrônico"';
				  $linhaDeBaixo = '"Processo Novo: ' . $data . '"';
				  $arrParam[$procDTO->getDblIdProcedimento()] = array("<img src='institucional/peticionamento/imagens/peticionamento_processo_novo.png' onmouseout='return infraTooltipOcultar();' onmouseover='return infraTooltipMostrar(" . $linhaDeBaixo .  "," . $linhaDeCima .  ");' />");
				}
			}
			
		}
		
		return $arrParam;
	}
	
	//EU 7352 - Icone exibido na tela "Acompanhamento Especial"
	public function montarIconeAcompanhamentoEspecial($arrObjProcedimentoDTO){
		
		$reciboRN = new ReciboPeticionamentoRN();
		$arrParam = array();
		
		if( $arrObjProcedimentoDTO != null && count( $arrObjProcedimentoDTO ) > 0 ){
			
			foreach( $arrObjProcedimentoDTO as $procDTO ){
				
				//verificar se este processo é de peticionamento
				$reciboDTO = new ReciboPeticionamentoDTO();
				$reciboDTO->retNumIdProtocolo();
				$reciboDTO->retDthDataHoraRecebimentoFinal();
				$reciboDTO->setNumIdProtocolo($procDTO->getDblIdProcedimento());
				$arrRecibos = $reciboRN->listar( $reciboDTO );
				
				if( $arrRecibos != null && count( $arrRecibos ) > 0){
					
					$recibo = $arrRecibos[0];
					$data = DataUtils::setFormat($recibo->getDthDataHoraRecebimentoFinal(), 'dd/mm/yyyy');
					
					$linhaDeCima = '"Peticionamento Eletrônico"';
					$linhaDeBaixo = '"Processo Novo: ' . $data . '"';
					$arrParam[$procDTO->getDblIdProcedimento()] = array("<img src='institucional/peticionamento/imagens/peticionamento_processo_novo.png' onmouseout='return infraTooltipOcultar();' onmouseover='return infraTooltipMostrar(" . $linhaDeBaixo .  "," . $linhaDeCima .  ");' />");
					
				}
				
			
			}
			
		}
				
		return $arrParam;
	}
	
	public function montarMenuUsuarioExterno(){ 
				
		$menuExternoRN = new MenuPeticionamentoUsuarioExternoRN();
		$menuExternoDTO = new MenuPeticionamentoUsuarioExternoDTO();
		$menuExternoDTO->retTodos();
		$menuExternoDTO->setStrSinAtivo('S');
		
		$menuExternoDTO->setOrd("Nome", InfraDTO::$TIPO_ORDENACAO_ASC);

		$objLista = $menuExternoRN->listar( $menuExternoDTO );		
		$numRegistros = count($objLista);
		
		//utilizado para ordenação
		$urlBase = ConfiguracaoSEI::getInstance()->getValor('SEI','URL');
		$arrMenusNomes = array();
		
		//$arrMenusNomes["Peticionar Processo Inicio"] = $urlBase .'/controlador_externo.php?acao=peticionamento_usuario_externo_iniciar';
		$arrMenusNomes["Peticionamento"] = $urlBase .'/controlador_externo.php?acao=peticionamento_usuario_externo_iniciar';
		
		$arrMenusNomes["Recibos Eletrônicos de Protocolo"] = $urlBase .'/controlador_externo.php?acao=recibo_peticionamento_usuario_externo_listar';
		
		if( is_array( $objLista ) && $numRegistros > 0 ){
			
			for($i = 0;$i < $numRegistros; $i++){
			
			 $item = $objLista[$i];
			 	
		  	 if( $item->getStrTipo() == MenuPeticionamentoUsuarioExternoRN::$TP_EXTERNO ) {
		  	 	$link = "javascript:";
		  	 	$link .= "var a = document.createElement('a'); ";
				$link .= "a.href='" . $item->getStrUrl() ."'; ";
				$link .= "a.target = '_blank'; ";
				$link .= "document.body.appendChild(a); ";
				$link .= "a.click(); ";
				$arrMenusNomes[$item->getStrNome()] = $link; 
		  	 }
		  	 
		  	 else if( $item->getStrTipo() == MenuPeticionamentoUsuarioExternoRN::$TP_CONTEUDO_HTML ) {
		  	 	
		  	 	$idItem = $item->getNumIdMenuPeticionamentoUsuarioExterno();		  	 	
		  	 	$strLinkMontado = SessaoSEIExterna::getInstance()->assinarLink($urlBase . '/controlador_externo.php?acao=pagina_conteudo_externo_peticionamento&id_md_pet_usu_externo_menu='. $idItem);
		  	 	$arrMenusNomes[$item->getStrNome()] = $strLinkMontado;
		  	 	
		  	 }
		  	
		  }
		}
		
		$arrLink = array();		
		$numRegistrosMenu = count($arrMenusNomes);
		
		if( is_array( $arrMenusNomes ) && $numRegistrosMenu > 0 ){
				
		    foreach ( $arrMenusNomes as $key => $value) {
		    	$urlLink = $arrMenusNomes[ $key ];
		    	$nomeMenu = $key;
		    	if($nomeMenu=='Peticionamento'){
		    		$arrLink[] = '-^#^^' . $nomeMenu .'^';
		    		$arrLink[] = '--^' . $urlLink .'^^' . 'Processo Novo' .'^';	
		    	}else{
		    		$arrLink[] = '-^' . $urlLink .'^^' . $nomeMenu .'^';	
		    	}
		    	
		    }
		}

		return $arrLink; 
	}
}
?>