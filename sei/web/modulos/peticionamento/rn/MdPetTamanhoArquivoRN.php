<?php
/**
* ANATEL
*
* 25/04/2016 - criado por jaqueline.mendes - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetTamanhoArquivoRN extends InfraRN {
	
	public static $ID_FIXO_TAMANHO_ARQUIVO = '1';

	public const PARAMETRO_LIMITE_ANEXOS_EXTERNOS = 'MODULO_PETICIONAMENTO_QTD_LIMITE_ANEXOS_PET_EXTERNO';
	public const LIMITE_PADRAO_ANEXOS_EXTERNOS = '100';

	private const RECUSA_POR_QUANTIDADE = 'Este peticionamento permite no máximo %d arquivo(s). Remova arquivos da lista para continuar.';

	/** Início da recusa por tamanho, com os marcadores que o servidor e a tela trocam pelo nome e pelo tamanho (ADR-PET-010). */
	private const RECUSA_POR_TAMANHO = 'O arquivo "{nome}" tem {tamanho} MB.';

	/** Recusa do arquivo de 0 byte, que o SEI só recusa quando o anexo entra no processo (RT-PET-027). */
	private const RECUSA_ARQUIVO_VAZIO = 'O arquivo "{nome}" está vazio. Escolha um arquivo com conteúdo.';
	
	public function __construct() {
		parent::__construct ();
	}
	
	protected function inicializarObjInfraIBanco() {
		return BancoSEI::getInstance ();
	}
	
	/** Lê o limite de arquivos do Peticionamento Externo. Null indica limite desativado. */
	public static function obterLimiteAnexosExternos(InfraParametro $objInfraParametro, LogSEI $objLogSEI): ?int
	{
		$strValor = $objInfraParametro->getValor(self::PARAMETRO_LIMITE_ANEXOS_EXTERNOS, false);
		if ($strValor === null || $strValor === '') {
			return null;
		}

		if (ctype_digit($strValor)) {
			$numLimite = filter_var(ltrim($strValor, '0') ?: '0', FILTER_VALIDATE_INT);
			if ($numLimite !== false) {
				return $numLimite;
			}
		}

		$strValorLog = preg_replace('/[\x00-\x1f\x7f-\x9f]/', ' ', substr($strValor, 0, 100));
		$objLogSEI->gravar(
			'Parâmetro ' . self::PARAMETRO_LIMITE_ANEXOS_EXTERNOS . ' com valor "' . $strValorLog
			. '": o limite de arquivos do Peticionamento Externo continua desativado.',
			InfraLog::$AVISO
		);
		return null;
	}

	/** Prepara o limite e a mensagem de recusa usados nas telas de Peticionamento Externo. */
	public static function montarLimiteAnexosExternosParaTela(): string
	{
		$numLimite = self::obterLimiteAnexosExternos(new InfraParametro(BancoSEI::getInstance()), LogSEI::getInstance());
		if ($numLimite === null) {
			return 'null';
		}

		return json_encode([
			'limite' => $numLimite,
			'mensagem' => mb_convert_encoding(sprintf(self::RECUSA_POR_QUANTIDADE, $numLimite), 'UTF-8', 'ISO-8859-1'),
		], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
	}

	/** Recusa a quantidade informada antes de gravar o Peticionamento Externo. */
	public static function validarQuantidadeAnexosExternos(
		int $numAnexos,
		InfraParametro $objInfraParametro,
		LogSEI $objLogSEI
	): void {
		$numLimite = self::obterLimiteAnexosExternos($objInfraParametro, $objLogSEI);
		if ($numLimite === null || $numAnexos <= $numLimite) {
			return;
		}

		$objInfraException = new InfraException();
		$objInfraException->adicionarValidacao(sprintf(self::RECUSA_POR_QUANTIDADE, $numLimite));
		$objInfraException->lancarValidacoes();
	}

    /**
	 * Short description of method listarParaUsuarioExternoConectado
	 *
	 * @access protected
	 * @author Marcelo Bezerra <marcelo.bezerra@cast.com.br>
	 * @param  $objCondutaLitigiosoDTO
	 * @return mixed
	 */
	protected function listarTamanhoMaximoConfiguradoParaUsuarioExternoConectado(MdPetTamanhoArquivoDTO $objMdPetTamanhoArquivoDTO) {
	
		try {

			//SessaoSEIExterna::getInstance()->validarAuditarPermissao('md_pet_usu_ext_cadastrar',__METHOD__,$objDTO);
			$objMdPetTamanhoArquivoBD = new MdPetTamanhoArquivoBD($this->getObjInfraIBanco());
			$ret = $objMdPetTamanhoArquivoBD->listar($objMdPetTamanhoArquivoDTO);
			return $ret;

		} catch (Exception $e) {
			throw new InfraException ('Erro listando Tamanho Maximo Peticionamento.', $e);
		}
	}	

	/**
	 * Recusa, com o nome de cada arquivo, o arquivo vazio e o tamanho acima do limite que o SEI aplica quando o anexo entra
	 * no processo (ADR-PET-010 e RT-PET-027). Repete a regra de AnexoRN::validarNumTamanhoRN0867: vale o limite da extensão em Extensões de Arquivos
	 * Permitidas e, sem ele, o parâmetro SEI_TAM_MB_DOC_EXTERNO; o limite da extensão acima do limite geral é recusado como
	 * erro de configuração, como no SEI. Lê o limite pela BD, sem a permissão do usuário interno, porque roda também no
	 * acesso externo.
	 *
	 * @param list<array{nome: string, tamanho: int}> $arrArquivos nome informado pelo usuário e tamanho em bytes
	 * @return list<string> uma mensagem por arquivo recusado
	 */
	protected function conferirTamanhoConectado(array $arrArquivos): array
	{
		$numLimiteGeralMb = (int) (new InfraParametro($this->getObjInfraIBanco()))->getValor('SEI_TAM_MB_DOC_EXTERNO');
		$arrLimitePorExtensao = [];
		$arrRecusas = [];

		foreach ($arrArquivos as $arrArquivo) {
			if ((int) $arrArquivo['tamanho'] === 0) {
				$arrRecusas[] = strtr(self::RECUSA_ARQUIVO_VAZIO, ['{nome}' => self::tratarNomeParaMensagem($arrArquivo['nome'])]);
				continue;
			}

			$strExtensao = self::obterExtensao($arrArquivo['nome']);
			if ($strExtensao === null) {
				continue;
			}

			if (!array_key_exists($strExtensao, $arrLimitePorExtensao)) {
				$arrLimitePorExtensao[$strExtensao] = $this->consultarLimiteDaExtensaoMb($strExtensao);
			}

			$strRecusa = self::descreverRecusaPorTamanho($arrArquivo['nome'], (int) $arrArquivo['tamanho'], $strExtensao, $arrLimitePorExtensao[$strExtensao], $numLimiteGeralMb);
			if ($strRecusa !== null) {
				$arrRecusas[] = $strRecusa;
			}
		}

		return array_values(array_unique($arrRecusas));
	}

	/**
	 * Limites do SEI e textos da recusa por tamanho, para a tela conferir o arquivo no navegador antes de transferi-lo, sem
	 * requisição ao servidor (ADR-PET-010). Vêm da mesma regra e dos mesmos textos de conferirTamanho, que o upload e a RN
	 * repetem com o tamanho real. Para a extensão repetida, vale o primeiro limite, como no SEI.
	 *
	 * @return array{recusa: string, vazio: string, geral: array{bytes: int, frase: string}, extensoes: array<string, array{bytes: int, frase: string}>, configuracoesInvalidas: array<string, string>}
	 */
	protected function obterLimitesParaTelaConectado(): array
	{
		$numLimiteGeralMb = (int) (new InfraParametro($this->getObjInfraIBanco()))->getValor('SEI_TAM_MB_DOC_EXTERNO');
		$arrLimites = [
			'recusa' => self::RECUSA_POR_TAMANHO,
			'vazio' => self::RECUSA_ARQUIVO_VAZIO,
			'geral' => ['bytes' => $numLimiteGeralMb * 1048576, 'frase' => self::montarFraseDoLimiteGeral($numLimiteGeralMb)],
			'extensoes' => [],
			'configuracoesInvalidas' => [],
		];

		$objArquivoExtensaoDTO = new ArquivoExtensaoDTO();
		$objArquivoExtensaoDTO->retStrExtensao();
		$objArquivoExtensaoDTO->retNumTamanhoMaximo();
		$objArquivoExtensaoDTO->setNumTamanhoMaximo(null, InfraDTO::$OPER_DIFERENTE);

		foreach ((new ArquivoExtensaoBD($this->getObjInfraIBanco()))->listar($objArquivoExtensaoDTO) as $objExtensaoDTO) {
			$strExtensao = str_replace(' ', '', InfraString::transformarCaixaBaixa($objExtensaoDTO->getStrExtensao()));
			if (isset($arrLimites['extensoes'][$strExtensao]) || isset($arrLimites['configuracoesInvalidas'][$strExtensao])) {
				continue;
			}

			$numLimiteMb = (int) $objExtensaoDTO->getNumTamanhoMaximo();
			if ($numLimiteMb > $numLimiteGeralMb) {
				$arrLimites['configuracoesInvalidas'][$strExtensao] = self::montarRecusaDeConfiguracao($strExtensao, $numLimiteMb, $numLimiteGeralMb);
				continue;
			}

			$arrLimites['extensoes'][$strExtensao] = ['bytes' => $numLimiteMb * 1048576, 'frase' => self::montarFraseDoLimiteDaExtensao($strExtensao, $numLimiteMb)];
		}

		return $arrLimites;
	}

	/**
	 * Extensão como o SEI a lê: o trecho depois do último ponto, em minúsculas e sem espaços.
	 *
	 * @param string $strNome
	 * @return string|null null quando o nome não tem extensão, caso que o upload do SEI já recusa
	 */
	private static function obterExtensao(string $strNome): ?string
	{
		$arrPartes = explode('.', $strNome);
		if (count($arrPartes) < 2) {
			return null;
		}

		return str_replace(' ', '', InfraString::transformarCaixaBaixa(end($arrPartes)));
	}

	/**
	 * @param string $strExtensao
	 * @return int|null limite em MB da extensão, ou null quando ela não tem limite próprio
	 */
	private function consultarLimiteDaExtensaoMb(string $strExtensao): ?int
	{
		$objArquivoExtensaoDTO = new ArquivoExtensaoDTO();
		$objArquivoExtensaoDTO->retNumTamanhoMaximo();
		$objArquivoExtensaoDTO->setStrExtensao($strExtensao);
		$objArquivoExtensaoDTO->setNumTamanhoMaximo(null, InfraDTO::$OPER_DIFERENTE);
		$objArquivoExtensaoDTO->setNumMaxRegistrosRetorno(1);

		$objArquivoExtensaoDTO = (new ArquivoExtensaoBD($this->getObjInfraIBanco()))->consultar($objArquivoExtensaoDTO);

		return $objArquivoExtensaoDTO === null ? null : (int) $objArquivoExtensaoDTO->getNumTamanhoMaximo();
	}

	/**
	 * @return string|null mensagem de recusa, ou null quando o arquivo cabe no limite
	 */
	private static function descreverRecusaPorTamanho(string $strNome, int $numTamanho, string $strExtensao, ?int $numLimiteExtensaoMb, int $numLimiteGeralMb): ?string
	{
		if ($numLimiteExtensaoMb !== null && $numLimiteExtensaoMb > $numLimiteGeralMb) {
			return self::montarRecusaDeConfiguracao($strExtensao, $numLimiteExtensaoMb, $numLimiteGeralMb);
		}

		if ($numTamanho <= ($numLimiteExtensaoMb ?? $numLimiteGeralMb) * 1024 * 1024) {
			return null;
		}

		// Arredondado para cima: o arquivo recusado nunca aparece com o mesmo tamanho do limite
		$strTamanhoMb = number_format(ceil($numTamanho * 100 / 1048576) / 100, 2, ',', '.');
		// Uma passada só: o nome com um dos marcadores não é trocado de novo
		$strArquivo = strtr(self::RECUSA_POR_TAMANHO, ['{nome}' => self::tratarNomeParaMensagem($strNome), '{tamanho}' => $strTamanhoMb]);
		$strLimite = $numLimiteExtensaoMb !== null
			? self::montarFraseDoLimiteDaExtensao($strExtensao, $numLimiteExtensaoMb)
			: self::montarFraseDoLimiteGeral($numLimiteGeralMb);

		return $strArquivo . ' ' . $strLimite;
	}

	/** Recusa de configuração do SEI, quando o limite da extensão passa do limite geral. */
	private static function montarRecusaDeConfiguracao(string $strExtensao, int $numLimiteExtensaoMb, int $numLimiteGeralMb): string
	{
		return 'Limite de tamanho ' . $numLimiteExtensaoMb . 'Mb configurado para a extensão ' . InfraString::transformarCaixaAlta($strExtensao) . ' é maior que o limite geral de ' . $numLimiteGeralMb . 'Mb.';
	}

	private static function montarFraseDoLimiteDaExtensao(string $strExtensao, int $numLimiteMb): string
	{
		return 'O tamanho máximo permitido para arquivos ' . InfraString::transformarCaixaAlta($strExtensao) . ' é ' . $numLimiteMb . ' MB.';
	}

	private static function montarFraseDoLimiteGeral(int $numLimiteGeralMb): string
	{
		return 'O tamanho máximo geral permitido para documentos externos é ' . $numLimiteGeralMb . ' MB.';
	}

	/**
	 * Nome em uma linha, com até 100 caracteres, sem caractere de controle, sem "#", que separa os campos do retorno do
	 * upload, e sem "<", ">" e "&": o InfraUpload lê o retorno do upload como HTML, e o nome não pode virar marcação.
	 *
	 * @param string $strNome
	 * @return string
	 */
	private static function tratarNomeParaMensagem(string $strNome): string
	{
		$strNome = trim((string) preg_replace('/[#<>&]/', '', (string) preg_replace('/[\x00-\x1F\x7F]+/', ' ', $strNome)));

		return strlen($strNome) > 100 ? substr($strNome, 0, 100) . '...' : $strNome;
	}

    /**
	 * Short description of method listarConectado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param
	 *        	$objMdPetIndisponibilidadeDTO
	 * @return mixed
	 */
	protected function listarConectado(MdPetIndisponibilidadeDTO $objMdPetIndisponibilidadeDTO) {
	
		try {

			SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_tamanho_arquivo_listar',__METHOD__,$objMdPetIndisponibilidadeDTO);

			//Regras de Negocio
			//$objInfraException = new InfraException();
	
			//$objInfraException->lancarValidacoes();

			$objMdPetIndisponibilidadeBD = new MdPetIndisponibilidadeBD($this->getObjInfraIBanco());
			$ret = $objMdPetIndisponibilidadeBD->listar($objMdPetIndisponibilidadeDTO);
			return $ret;

		} catch (Exception $e) {
			throw new InfraException ('Erro listando Indisponibilidade Peticionamento.', $e);
		}
	}
	
	
/**
	 * Short description of method consultarConectado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param  $objMdPetTamanhoArquivoDTO
 * @return mixed
	 */
	protected function consultarConectado(MdPetTamanhoArquivoDTO $objMdPetTamanhoArquivoDTO) {
		try {
			
			// Valida Permissao
			SessaoSEI::getInstance ()->validarAuditarPermissao ('md_pet_tamanho_arquivo_consultar', __METHOD__, $objMdPetTamanhoArquivoDTO );

		    $objMdPetTamanhoArquivoBD = new MdPetTamanhoArquivoBD($this->getObjInfraIBanco());
			$ret = $objMdPetTamanhoArquivoBD->consultar($objMdPetTamanhoArquivoDTO);
			
			return $ret;
		} catch ( Exception $e ) {
			throw new InfraException('Erro consultando Tamanho de Arquivo Permitido Peticionamento.', $e);
		}
	}
	
	
	/**
	 * Short description of method cadastrarControlado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param  $objMdPetTamanhoArquivoDTO
	 * @return mixed
	 */
	protected function cadastrarControlado(MdPetTamanhoArquivoDTO $objMdPetTamanhoArquivoDTO) {
		try {
			// Valida Permissao
			SessaoSEI::getInstance ()->validarAuditarPermissao ('md_pet_tamanho_arquivo_cadastrar', __METHOD__, $objMdPetTamanhoArquivoDTO );

			// Regras de Negocio
			$objInfraException = new InfraException();

			$valido = $this->_validarCamposDocumento($objMdPetTamanhoArquivoDTO->getNumValorDocPrincipal(), 'Valor para Documento Principal', $objInfraException);
			$valido = $this->_validarCamposDocumento($objMdPetTamanhoArquivoDTO->getNumValorDocComplementar(), 'Valor para Documento Complementar', $objInfraException);
			
			if($valido){
				$this->_validarParametroMaxPermitido($objMdPetTamanhoArquivoDTO, $objInfraException);
			}
			
			$objInfraException->lancarValidacoes();

			$objMdPetTamanhoArquivoBD = new MdPetTamanhoArquivoBD ($this->getObjInfraIBanco ());
			$objMdPetTamanhoArquivoDTO = $objMdPetTamanhoArquivoBD->cadastrar($objMdPetTamanhoArquivoDTO);
	
			return $objMdPetTamanhoArquivoDTO;
		} catch ( Exception $e ) {
			throw new InfraException ('Erro cadastrando Tamanho de Arquivo Peticionamento.', $e );
		}
	}
	
	
	private function _validarParametroMaxPermitido($objMdPetTamanhoArquivoDTO, $objInfraException){
		$objInfraParametroDTO = new InfraParametroDTO();
		$objInfraParametroDTO->retStrNome();
		$objInfraParametroDTO->retStrValor();
		$objInfraParametroDTO->setStrNome('SEI_TAM_MB_DOC_EXTERNO');
		
		$objMdPetParametroRN = new MdPetParametroRN();
		$objInfraParametroDTO = $objMdPetParametroRN->consultar($objInfraParametroDTO);
		
		$valor = $objInfraParametroDTO->getStrValor();
		
		$erro = false;
		
		if($valor != ''){
			if($objMdPetTamanhoArquivoDTO->getNumValorDocPrincipal() > $valor){
				$erro = true;
			}
			
			if($objMdPetTamanhoArquivoDTO->getNumValorDocComplementar() > $valor){
				$erro = true;
			}
		}
		
	if($erro){
		$objInfraException->adicionarValidacao('Limite em Mb superior ao limite global do SEI indicado em Infra > Parâmetros. Informar valor menor.');
	}		
		
	}
	
	
	
	/**
	 * Short description of method alterarControlado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param  $objMdPetTamanhoArquivoDTO
	 * @return void
	 */
	protected function alterarControlado(MdPetTamanhoArquivoDTO $objMdPetTamanhoArquivoDTO) {
	
		try {
				
			// Valida Permissao
			SessaoSEI::getInstance()->validarAuditarPermissao ('md_pet_tamanho_arquivo_cadastrar', __METHOD__, $objMdPetTamanhoArquivoDTO );


			// Regras de Negocio
			$objInfraException = new InfraException ();
			$valido = $this->_validarCamposDocumento($objMdPetTamanhoArquivoDTO->getNumValorDocPrincipal(), 'Valor para Documento Principal', $objInfraException);
			$valido = $this->_validarCamposDocumento($objMdPetTamanhoArquivoDTO->getNumValorDocComplementar(), 'Valor para Documento Complementar', $objInfraException);
		
			if($valido){
		    	$this->_validarParametroMaxPermitido($objMdPetTamanhoArquivoDTO, $objInfraException);
			}
			
			$objInfraException->lancarValidacoes();
				
	
			$objMdPetTamanhoArquivoBD = new MdPetTamanhoArquivoBD ($this->getObjInfraIBanco ());
			$objMdPetTamanhoArquivoBD->alterar($objMdPetTamanhoArquivoDTO);
	
				
			// Auditoria
		} catch ( Exception $e ) {
			throw new InfraException ('Erro alterando Tamanho Arquivo Permitido Peticionamento.', $e);
		}
	}
	
	
	/**
	 * Validate fields
	 *
	 * @access private
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @return void
	 */
	private function _validarCamposDocumento($campo, $nomeCampo , $objInfraException) {
		$valido = true;
		
		// VERIFICA SE O CAMPO FOI PREENCHIDO
		if (InfraString::isBolVazia (trim($campo))) {
			$msg1 = $nomeCampo. ' não informado.';
			$objInfraException->adicionarValidacao($msg1);
			$valido = false;
		}
		if (trim ( $campo ) != '')
		{
			if (strlen ($campo) > 11) {
				$msg2 = $nomeCampo .' possui tamanho superior a 11 caracteres.';
				$objInfraException->adicionarValidacao($msg2);
				$valido = false;
			}
		}
		
		return $valido;
		
	
	}	
	
}
?>