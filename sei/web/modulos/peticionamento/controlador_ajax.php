<?
/**
 * ANATEL
 *
 * 
 * 22/07/2016 - criado por marcelo.bezerra@cast.com.br - CAST
 * Arquivo para realizar controle requisição ajax de usuario externo no modulo peticionamento.
 */

try{
    require_once dirname(__FILE__).'/../../SEI.php';
    session_start();
    
    InfraAjax::decodificarPost();
  
 switch($_GET['acao_ajax']){

	case 'md_pet_int_relatorio_grafico':

         $arrRetorno    = [];
         $tamanho       = MdPetIntRelatorioRN::$GRAFICO_TAMANHO_PADRAO;
         $arrSituacao   = MdPetIntRelatorioINT::retornaArraySituacaoRelatorio();

         foreach($arrSituacao as $key => $value){

             $objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
             $objMdPetIntRelDestDTO->setProcedimentoDocTIPOFK(InfraDTO::$TIPO_FK_OBRIGATORIA);
             $objMdPetIntRelDestDTO->setAceiteTIPOFK(InfraDTO::$TIPO_FK_OPCIONAL);
             $objMdPetIntRelDestDTO->setStrStaSituacaoIntimacao($key);
             $objMdPetIntRelDestDTO->setStrSinPrincipalDoc('S');
             $objMdPetIntRelDestDTO = (new MdPetIntRelatorioRN())->_addFiltroListagem($objMdPetIntRelDestDTO);

             $valor = (new MdPetIntRelDestinatarioRN())->contar($objMdPetIntRelDestDTO);

             if($valor > 0){
                 array_push($arrRetorno, [
                     'valor'    => $valor,
                     'cor'      => MdPetIntRelatorioINT::retornaArrayCorGrafico($label),
                     'label'    => $value
                 ]);
             }

         }

         echo empty($arrRetorno) ? 'Nenhum registro encontrado.' : MdPetIntRelatorioINT::_retornaHtmlGrafico($_POST['tipoGrafico'], $arrRetorno, $_POST['idTipoIntimacao'], $tamanho);

         break;
         
		case 'md_pet_verifica_usuarios_intimacao':
			
			// Busca os contatos ja intimados para o documento
			$arrContatosIntimados = [];
			$idDocumento = '';
			
			$cpfList = $_POST['cpfList'];
			$foundCpfs = $notFoundCpfs = $notAbleCpfs = [];
			
			if(isset($_REQUEST['id_documento']) && !empty($_REQUEST['id_documento'])){
				$idDocumento = $_REQUEST['id_documento'];
				$arrContatosIntimados = array_column((new MdPetIntimacaoRN())->buscaIntimacoesCadastradas($_REQUEST['id_documento']), 'Id');
			}
			
			$cpfRegex = '/^(\d{3}\.?\d{3}\.?\d{3}-?\d{2}|\d{11})$/';
			
			foreach($cpfList as $cpf){
				
				$cpfOriginal = $cpf;
				
				// Pega sempre o primeiro dado
				if (strpos($cpf, ' ') !== false) {
					$cpf = explode(' ', $cpf)[0];
				}
				
				// Completa com zeros a esquerda
				if(is_numeric($cpf) && strlen($cpf) < 11){
					$cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
				}
				
				if (preg_match('/[^0-9.-]/', $cpf)) {
					$notFoundCpfs[] = utf8_encode($cpf . ' - Caracteres inválidos');
					continue;
				}
				
				if (!preg_match($cpfRegex, $cpf)) {
					$notFoundCpfs[] = utf8_encode($cpf . ' - Formato inválido');
					continue;
				}
				
				$cpf = trim(preg_replace('/\D/', '', $cpf));
				
				if (preg_match('/^(\d)\1*$/', substr(preg_replace('/\D/', '', $cpf), 0, 9))) {
					$notFoundCpfs[] = utf8_encode($cpf . ' - Sequência inválida');
					continue;
				}
				
				if(InfraUtil::validarCpf($cpf)){
					
					$objUsuarioDTO = new UsuarioDTO();
					$objUsuarioDTO->retNumIdUsuario();
					$objUsuarioDTO->retNumIdContato();
					$objUsuarioDTO->retStrSigla();
					$objUsuarioDTO->retStrNome();
					$objUsuarioDTO->retDblCpfContato();
					$objUsuarioDTO->retStrStaTipo();
					$objUsuarioDTO->setDblCpfContato(InfraUtil::retirarFormatacao($cpf));
					$objUsuarioDTO->adicionarCriterio(['StaTipo', 'StaTipo'], [InfraDTO::$OPER_IGUAL, InfraDTO::$OPER_IGUAL], [UsuarioRN::$TU_EXTERNO, UsuarioRN::$TU_EXTERNO], [InfraDTO::$OPER_LOGICO_OR]);
					$arrObjUsuarioDTO = (new UsuarioRN())->pesquisar($objUsuarioDTO);
					
					if(!empty($arrObjUsuarioDTO)){
						for($i=0;$i<count($arrObjUsuarioDTO);$i++){
							if (in_array($arrObjUsuarioDTO[$i]->getNumIdContato(), $arrContatosIntimados)) {
								$notFoundCpfs[] = utf8_encode(InfraUtil::formatarCpfCnpj($cpf) . ' - Já intimado');
								$notAbleCpfs[] = utf8_encode(InfraUtil::formatarCpfCnpj($cpf) . ' - ' . $arrObjUsuarioDTO[$i]->getStrNome());
							} else {
								$foundCpfs[] = utf8_encode($arrObjUsuarioDTO[$i]->getNumIdContato().'|'.$arrObjUsuarioDTO[$i]->getStrNome().'|'.$arrObjUsuarioDTO[$i]->getStrSigla().'|'.InfraUtil::formatarCpfCnpj($cpf));
							}
						}
					}else{
						$notFoundCpfs[] = utf8_encode($cpfOriginal . ' - Não localizado');
					}
					
				}else{
					$notFoundCpfs[] = utf8_encode(InfraUtil::formatarCpfCnpj($cpf) . ' - CPF inválido');
				}
				
			}
			
			$response = [
				'foundCpfs'     => $foundCpfs,
				'notFoundCpfs'  => $notFoundCpfs,
				'notAbleCpfs'   => $notAbleCpfs
			];
			
			echo json_encode($response);
		
		break;
		
	 case 'md_pet_verifica_destinatarios_intimacao':
	 	
	 	 // Busca os contatos ja intimados para o documento
		 $arrContatosIntimados = [];
		 $idDocumento = '';
		
		 $cnpjList = $_POST['cnpjList'];
		 $foundCnpjs = $notFoundCnpjs = $notAbleCnpjs = [];
		
		 if(isset($_REQUEST['id_documento']) && !empty($_REQUEST['id_documento'])){
			 $idDocumento = $_REQUEST['id_documento'];
			 $arrContatosIntimados = array_column((new MdPetIntimacaoRN())->buscaIntimacoesCadastradasJuridico($_REQUEST['id_documento']), 'Id');
		 }
		
		 $cnpjRegex = '/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$|^\d{14}$/';
		
		 foreach($cnpjList as $cnpj){
			
			
			 $cnpjOriginal = $cnpj;
			
			 // Pega sempre o primeiro dado
			 if (strpos($cnpj, ' ') !== false) {
				 $cnpj = explode(' ', $cnpj)[0];
			 }
			
			 // Completa com zeros a esquerda
			 if(is_numeric($cnpj) && strlen($cnpj) < 14){
				 $cnpj = str_pad($cnpj, 14, '0', STR_PAD_LEFT);
			 }
			
			 if (!preg_match('/^[0-9.\-\/]+$/', $cnpj)) {
				 $notFoundCnpjs[] = utf8_encode($cnpjOriginal . ' - Caracteres inválidos');
				 continue;
			 }
			
			 if (!preg_match($cnpjRegex, $cnpj)) {
				 $notFoundCnpjs[] = utf8_encode($cnpjOriginal . ' - Formato inválido');
				 continue;
			 }
			
			 $cnpj = trim(preg_replace('/\D/', '', $cnpj));
			
			 if (preg_match('/^(\d)\1*$/', substr(preg_replace('/\D/', '', $cnpj), 0, 12))) {
				 $notFoundCnpjs[] = utf8_encode($cnpjOriginal . ' - Sequência inválida');
				 continue;
			 }
			
			 if(InfraUtil::validarCnpj($cnpj)){
				
				 $dtoMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
				 $dtoMdPetVincRepresentantDTO->retNumIdMdPetVinculo();
				 $dtoMdPetVincRepresentantDTO->retNumIdContatoVinc();
				 $dtoMdPetVincRepresentantDTO->retNumIdContatoProcurador();
				 $dtoMdPetVincRepresentantDTO->setDistinct(true);
				 $dtoMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
				 $dtoMdPetVincRepresentantDTO->setStrTpVinc(MdPetVincRepresentantRN::$NT_JURIDICA);
				 $dtoMdPetVincRepresentantDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_ATIVO);
				 $dtoMdPetVincRepresentantDTO->setStrIdxContato('%' . InfraUtil::retirarFormatacao($cnpj) . '%', InfraDTO::$OPER_LIKE);
				 $arrObjMdPetVincRepresentantDTO = (new MdPetVincRepresentantRN())->listar($dtoMdPetVincRepresentantDTO);
				
				 $arrRepres = array_unique(InfraArray::converterArrInfraDTO($arrObjMdPetVincRepresentantDTO, 'IdContatoVinc'));
				
				 if(is_iterable($arrRepres) && count($arrRepres) > 0){
					
					 $objContatoDTO = new ContatoDTO();
					 $objContatoDTO->retStrNome();
					 $objContatoDTO->retDblCnpj();
					 $objContatoDTO->retNumIdContato();
					 $objContatoDTO->setNumIdContato($arrRepres, infraDTO::$OPER_IN);
					 $arrObjContatoDTO = (new ContatoRN())->listarRN0325($objContatoDTO);
					
					 if(!empty($arrObjContatoDTO) && count($arrObjContatoDTO) > 0){
					 	
						 $arrTemp = [];
						 foreach($arrObjContatoDTO as $contatoDTO){
							 if ($contatoDTO->get('Nome') != null && $contatoDTO->get('Cnpj') != null) {
								 $strChave = strtolower($contatoDTO->get('Nome').'-'.$contatoDTO->get('Cnpj'));
								 if (!isset($arrTemp[$strChave])) {
									 $arrTemp[$strChave] = array($contatoDTO);
								 } else {
									 $arrTemp[$strChave][] = $contatoDTO;
								 }
							 }
						 }
						
						 foreach($arrTemp as $arr){
							 if (count($arr) == 1){
								 $arr[0]->setStrNome($arr[0]->get('Nome').' - '.InfraUtil::formatarCpfCnpj($arr[0]->get('Cnpj')));
							 }else{
								 foreach($arr as $dto){
									 $dto->setStrNome($dto->get('Nome').' - '.InfraUtil::formatarCpfCnpj($dto->get('Cnpj')));
								 }
							 }
						 }
						
					 }
					
				 }
				
				 if(!empty($arrObjContatoDTO)){
				 	
					 for($i=0;$i<count($arrObjContatoDTO);$i++){
						 if (in_array($arrObjContatoDTO[$i]->getNumIdContato(), $arrContatosIntimados)) {
							 $notFoundCnpjs[] = utf8_encode(InfraUtil::formatarCpfCnpj($cnpj) . ' - Já intimado');
							 $notAbleCnpjs[] = utf8_encode(InfraUtil::formatarCpfCnpj($cnpj) . ' - ' . $arrObjContatoDTO[$i]->getStrNome());
						 } else {
							 $foundCnpjs[] = utf8_encode($arrObjContatoDTO[$i]->getNumIdContato().'|'.$arrObjContatoDTO[$i]->getStrNome().'|'.InfraUtil::formatarCpfCnpj($cnpj));
						 }
					 }
					 
				 }else{
					 $notFoundCnpjs[] = utf8_encode($cnpjOriginal . ' - Não localizado');
				 }
				
			 }else{
				 $notFoundCnpjs[] = utf8_encode(InfraUtil::formatarCpfCnpj($cnpj) . ' - CNPJ inválido');
			 }
			
		 }
		
		 $response = [
			 'foundCnpjs'     => $foundCnpjs,
			 'notFoundCnpjs'  => $notFoundCnpjs,
			 'notAbleCnpjs'   => $notAbleCnpjs
		 ];
		
		 echo json_encode($response);
	 	
	 	break;

	default:
      throw new InfraException("Ação '".$_GET['acao_ajax']."' não reconhecida pelo controlador AJAX do Peticionamento.");
  }
  
}catch(Exception $e){
  InfraAjax::processarExcecao($e);
}
