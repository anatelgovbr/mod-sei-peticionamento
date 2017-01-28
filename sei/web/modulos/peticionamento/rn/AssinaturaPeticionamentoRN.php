<?
/**
* ANATEL
*
* 02/01/2017 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class AssinaturaPeticionamentoRN extends AssinaturaRN {

	public static $TA_CERTIFICADO_DIGITAL = 'C';
	public static $TA_SENHA = 'S';

	public static $TA_SIMPLES = 'S';
	public static $TA_COMPLETA = 'C';
	
	public static $TT_ASSINATURA_SENHA_PETICIONAMENTO = "P";

	public function __construct(){
		parent::__construct();
	}

	protected function inicializarObjInfraIBanco(){
		return BancoSEI::getInstance();
	}
	
	private function validarDblIdDocumentoRN1311(AssinaturaDTO $objAssinaturaDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objAssinaturaDTO->getDblIdDocumento())){
			$objInfraException->adicionarValidacao('Documento não informado.');
		}
	}

	private function validarNumIdUsuarioRN1312(AssinaturaDTO $objAssinaturaDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objAssinaturaDTO->getNumIdUsuario())){
			$objInfraException->adicionarValidacao('Usuário não informado.');
		}
	}

	private function validarNumIdUnidadeRN1313(AssinaturaDTO $objAssinaturaDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objAssinaturaDTO->getNumIdUnidade())){
			$objInfraException->adicionarValidacao('Unidade não informada.');
		}
	}

	private function validarNumIdAtividade(AssinaturaDTO $objAssinaturaDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objAssinaturaDTO->getNumIdAtividade())){
			$objAssinaturaDTO->setNumIdAtividade(null);
		}
	}

	private function validarStrNomeRN1314(AssinaturaDTO $objAssinaturaDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objAssinaturaDTO->getStrNome())){
			$objInfraException->adicionarValidacao('Nome não informado.');
		}else{
			$objAssinaturaDTO->setStrNome(trim($objAssinaturaDTO->getStrNome()));

			if (strlen($objAssinaturaDTO->getStrNome())>100){
				$objInfraException->adicionarValidacao('Nome possui tamanho superior a 100 caracteres.');
			}
		}
	}

	private function validarStrTratamentoRN1315(AssinaturaDTO $objAssinaturaDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objAssinaturaDTO->getStrTratamento())){
			$objInfraException->adicionarValidacao('Tratamento não informado.');
		}else{
			$objAssinaturaDTO->setStrTratamento(trim($objAssinaturaDTO->getStrTratamento()));

			if (strlen($objAssinaturaDTO->getStrTratamento())>100){
				$objInfraException->adicionarValidacao('Tratamento possui tamanho superior a 100 caracteres.');
			}
		}
	}

	private function validarDblCpfRN1316(AssinaturaDTO $objAssinaturaDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objAssinaturaDTO->getDblCpf())){
			$objAssinaturaDTO->setDblCpf(null);
		}
	}

	private function validarStrStaFormaAutenticacao(AssinaturaDTO $objAssinaturaDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objAssinaturaDTO->getStrStaFormaAutenticacao())){
			$objInfraException->adicionarValidacao('Forma de Autenticação não informada.');
		}else{
			if ($objAssinaturaDTO->getStrStaFormaAutenticacao()!=self::$TA_CERTIFICADO_DIGITAL && $objAssinaturaDTO->getStrStaFormaAutenticacao()!=self::$TA_SENHA){
				$objInfraException->adicionarValidacao('Forma de Autenticação inválida.');
			}
		}
	}

	private function validarStrP7sBase64(AssinaturaDTO $objAssinaturaDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objAssinaturaDTO->getStrP7sBase64())){
			$objAssinaturaDTO->setStrP7sBase64(null);
		}
	}

	private function validarStrSinAtivo(AssinaturaDTO $objAssinaturaDTO, InfraException $objInfraException){
		if (InfraString::isBolVazia($objAssinaturaDTO->getStrSinAtivo())){
			$objInfraException->adicionarValidacao('Sinalizador de Exclusão Lógica não informado.');
		}else{
			if (!InfraUtil::isBolSinalizadorValido($objAssinaturaDTO->getStrSinAtivo())){
				$objInfraException->adicionarValidacao('Sinalizador de Exclusão Lógica inválido.');
			}
		}
	}

	protected function montarTarjasConectado(DocumentoDTO $objDocumentoDTO) {
				
		try {

			$strRet = '';

			$objAssinaturaDTO = new AssinaturaDTO();
			$objAssinaturaDTO->retStrNome();
			$objAssinaturaDTO->retNumIdAssinatura();
			$objAssinaturaDTO->retNumIdTarjaAssinatura();
			$objAssinaturaDTO->retStrTratamento();
			$objAssinaturaDTO->retStrStaFormaAutenticacao();
			$objAssinaturaDTO->retStrNumeroSerieCertificado();
			$objAssinaturaDTO->retDthAberturaAtividade();

			$objAssinaturaDTO->setDblIdDocumento($objDocumentoDTO->getDblIdDocumento());
			 
			$objAssinaturaDTO->setOrdNumIdAssinatura(InfraDTO::$TIPO_ORDENACAO_ASC);
			 
			$arrObjAssinaturaDTO = $this->listarRN1323($objAssinaturaDTO);

			if (count($arrObjAssinaturaDTO)) {

				$objTarjaAssinaturaDTO = new TarjaAssinaturaDTO();
				$objTarjaAssinaturaDTO->setBolExclusaoLogica(false);
				$objTarjaAssinaturaDTO->retNumIdTarjaAssinatura();
				$objTarjaAssinaturaDTO->retStrStaTarjaAssinatura();
				$objTarjaAssinaturaDTO->retStrTexto();
				$objTarjaAssinaturaDTO->retStrLogo();
				$objTarjaAssinaturaDTO->setNumIdTarjaAssinatura(array_unique(InfraArray::converterArrInfraDTO($arrObjAssinaturaDTO,'IdTarjaAssinatura')),InfraDTO::$OPER_IN);

				$objTarjaAssinaturaRN = new TarjaAssinaturaRN();
				$arrObjTarjaAssinaturaDTO = InfraArray::indexarArrInfraDTO($objTarjaAssinaturaRN->listar($objTarjaAssinaturaDTO),'IdTarjaAssinatura');

				foreach ($arrObjAssinaturaDTO as $objAssinaturaDTO) {

					if (!isset($arrObjTarjaAssinaturaDTO[$objAssinaturaDTO->getNumIdTarjaAssinatura()])) {
						throw new InfraException('Tarja associada com a assinatura "' . $objAssinaturaDTO->getNumIdAssinatura() . '" não encontrada.');
					}

					$objTarjaAutenticacaoDTOAplicavel = $arrObjTarjaAssinaturaDTO[$objAssinaturaDTO->getNumIdTarjaAssinatura()];

					$strTarja = $objTarjaAutenticacaoDTOAplicavel->getStrTexto();
					$strTarja = preg_replace("/@logo_assinatura@/s", '<img alt="logotipo" src="data:image/png;base64,' . $objTarjaAutenticacaoDTOAplicavel->getStrLogo() . '" />', $strTarja);
					$strTarja = preg_replace("/@nome_assinante@/s", $objAssinaturaDTO->getStrNome(), $strTarja);
					$strTarja = preg_replace("/@tratamento_assinante@/s", $objAssinaturaDTO->getStrTratamento(), $strTarja);
					$strTarja = preg_replace("/@data_assinatura@/s", substr($objAssinaturaDTO->getDthAberturaAtividade(), 0, 10), $strTarja);
					$strTarja = preg_replace("/@hora_assinatura@/s", substr($objAssinaturaDTO->getDthAberturaAtividade(), 11, 5), $strTarja);
					$strTarja = preg_replace("/@codigo_verificador@/s", $objDocumentoDTO->getStrProtocoloDocumentoFormatado(), $strTarja);
					$strTarja = preg_replace("/@crc_assinatura@/s", $objDocumentoDTO->getStrCrcAssinatura(), $strTarja);
					$strTarja = preg_replace("/@numero_serie_certificado_digital@/s", $objAssinaturaDTO->getStrNumeroSerieCertificado(), $strTarja);
					
					$strTarja = preg_replace("/@tipo_conferencia@/s", "do próprio documento nato-digital", $strTarja);
					
					$strRet .= $strTarja;
				}

				$objTarjaAssinaturaDTO = new TarjaAssinaturaDTO();
				$objTarjaAssinaturaDTO->retStrTexto();
				$objTarjaAssinaturaDTO->setStrStaTarjaAssinatura(TarjaAssinaturaRN::$TT_INSTRUCOES_VALIDACAO);

				$objTarjaAssinaturaDTO = $objTarjaAssinaturaRN->consultar($objTarjaAssinaturaDTO);

				if ($objTarjaAssinaturaDTO != null){

					$strLinkAcessoExterno = '';
					if (strpos($objTarjaAssinaturaDTO->getStrTexto(),'@link_acesso_externo_processo@')!==false){
						$objEditorRN = new EditorRN();
						$strLinkAcessoExterno = $objEditorRN->recuperarLinkAcessoExterno($objDocumentoDTO);
					}

					$strTarja = $objTarjaAssinaturaDTO->getStrTexto();
					$strTarja = preg_replace("/@qr_code@/s", '<img align="center" alt="QRCode Assinatura" title="QRCode Assinatura" src="data:image/png;base64,' . $objDocumentoDTO->getStrQrCodeAssinatura() . '" />', $strTarja);
					$strTarja = preg_replace("/@codigo_verificador@/s", $objDocumentoDTO->getStrProtocoloDocumentoFormatado(), $strTarja);
					$strTarja = preg_replace("/@crc_assinatura@/s", $objDocumentoDTO->getStrCrcAssinatura(), $strTarja);
					$strTarja = preg_replace("/@link_acesso_externo_processo@/s", $strLinkAcessoExterno, $strTarja);
					$strRet .= $strTarja;
				}
			}

			return EditorRN::converterHTML($strRet);

		} catch (Exception $e) {
			throw new InfraException('Erro montando tarja de assinatura.',$e);
		}
	}

}
?>