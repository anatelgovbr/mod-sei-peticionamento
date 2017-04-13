<?
/**
* ANATEL
*
* 15/06/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetMenuUsuarioExternoRN extends InfraRN {
	
	public static $TP_EXTERNO = 'E';
	public static $TP_CONTEUDO_HTML = 'H';
	
	public function __construct() {
		parent::__construct ();
	}
	
	protected function inicializarObjInfraIBanco() {
		return BancoSEI::getInstance ();
	}
	
	/**
	 * Short description of method listarConectado
	 *
	 * @access protected
	 * @author Marcelo Bezerra <marcelo.bezerra@cast.com.br>
	 * @param $objMdPetMenuUsuarioExternoDTO
	 * @return mixed
	 */
	protected function listarConectado(MdPetMenuUsuarioExternoDTO $objMdPetMenuUsuarioExternoDTO) {
	
		try {
					
			$objMdPetMenuUsuarioExternoBD = new MdPetMenuUsuarioExternoBD($this->getObjInfraIBanco());
			$ret = $objMdPetMenuUsuarioExternoBD->listar($objMdPetMenuUsuarioExternoDTO);
	
			return $ret;
		} catch (Exception $e) {
			throw new InfraException ('Erro listando Menu Peticionamento.', $e);
		}
	}
	
	/**
	 * Short description of method consultarConectado
	 *
	 * @access protected
	 * @author Marcelo Bezerra <marcelo.bezerra@cast.com.br>
	 * @param  $objMdPetMenuUsuarioExternoDTO
	 * @return mixed
	 */
	protected function consultarConectado(MdPetMenuUsuarioExternoDTO $objMdPetMenuUsuarioExternoDTO) {
		try {
				
			// Valida Permissao
				
			$objMdPetMenuUsuarioExternoBD = new MdPetMenuUsuarioExternoBD($this->getObjInfraIBanco());
			$ret = $objMdPetMenuUsuarioExternoBD->consultar($objMdPetMenuUsuarioExternoDTO);
				
			return $ret;
		} catch ( Exception $e ) {
			throw new InfraException('Erro consultando Menu Peticionamento.', $e);
		}
	}
	
	/**
	 * Short description of method desativarControlado
	 *
	 * @access protected
	 * @author Marcelo Bezerra <marcelo.bezerra@cast.com.br>
	 * @param  $arrMdPetMenuUsuarioExternoDTO
	 * @return void
	 */
	protected function desativarControlado($arrMdPetMenuUsuarioExternoDTO) {
	
		try {
	
			SessaoSEI::getInstance ()->validarAuditarPermissao('menu_peticionamento_usuario_externo_desativar', __METHOD__, $arrMdPetMenuUsuarioExternoDTO);
				
			$objMdPetMenuUsuarioExternoBD = new MdPetMenuUsuarioExternoBD($this->getObjInfraIBanco());
			for($i = 0; $i < count($arrMdPetMenuUsuarioExternoDTO); $i ++) {
				$objMdPetMenuUsuarioExternoBD->desativar($arrMdPetMenuUsuarioExternoDTO[$i]);
			}
	
		} catch(Exception $e) {
			throw new InfraException ('Erro desativando Menu Peticionamento.', $e );
		}
	}
	
	/**
	 * Short description of method reativarControlado
	 *
	 * @access protected
	 * @author Marcelo Bezerra <marcelo.bezerra@cast.com.br>
	 * @param  $arrMdPetMenuUsuarioExternoDTO
	 * @return void
	 */
	protected function reativarControlado($arrMdPetMenuUsuarioExternoDTO) {
	
		try {
	
			SessaoSEI::getInstance ()->validarAuditarPermissao('menu_peticionamento_usuario_externo_reativar', __METHOD__, $arrMdPetMenuUsuarioExternoDTO);
	
			$objMdPetMenuUsuarioExternoBD = new MdPetMenuUsuarioExternoBD($this->getObjInfraIBanco());
			for($i = 0; $i < count($arrMdPetMenuUsuarioExternoDTO); $i ++) {
				$objMdPetMenuUsuarioExternoBD->reativar($arrMdPetMenuUsuarioExternoDTO[$i]);
			}
	
		} catch(Exception $e) {
			throw new InfraException ('Erro reativando Menu Peticionamento.', $e );
		}
	}
	
	/**
	 * Short description of method excluirControlado
	 *
	 * @access protected
	 * @author Marcelo Bezerra <marcelo.bezerra@cast.com.br>
	 * @param  $arrMdPetMenuUsuarioExternoDTO
	 * @return void
	 */
	protected function excluirControlado($arrMdPetMenuUsuarioExternoDTO) {
	
		try {
	
			SessaoSEI::getInstance ()->validarAuditarPermissao('menu_peticionamento_usuario_externo_excluir', __METHOD__, $arrMdPetMenuUsuarioExternoDTO);
				
			$objMdPetMenuUsuarioExternoBD = new MdPetMenuUsuarioExternoBD($this->getObjInfraIBanco());
				
			for($i = 0; $i < count($arrMdPetMenuUsuarioExternoDTO); $i ++) {
	
				//removendo menu
				$objMdPetMenuUsuarioExternoBD->excluir($arrMdPetMenuUsuarioExternoDTO[$i] );
			}
	
		} catch(Exception $e) {
			throw new InfraException ('Erro excluindo Menu Peticionamento.', $e );
		}
	}
	
	/**
	 * Short description of method cadastrarControlado
	 *
	 * @access protected
	 * @author Alan Campos <alan.campos@castgroup.com.br>
	 * @param  $objMdPetMenuUsuarioExternoDTO
	 * @return mixed
	 */
	protected function cadastrarControlado(MdPetMenuUsuarioExternoDTO $objMdPetMenuUsuarioExternoDTO) {
		try {
			// Valida Permissao
			SessaoSEI::getInstance ()->validarAuditarPermissao ('menu_peticionamento_usuario_externo_cadastrar', __METHOD__, $objMdPetMenuUsuarioExternoDTO );
	
			$objInfraException = new InfraException();
				
			$this->_validarCamposObrigatorios($objMdPetMenuUsuarioExternoDTO, $objInfraException);
			$this->_validarDuplicidade($objMdPetMenuUsuarioExternoDTO, $objInfraException, true);
			$objInfraException->lancarValidacoes();
				
			$objMdPetMenuUsuarioExternoBD = new MdPetMenuUsuarioExternoBD($this->getObjInfraIBanco());
            $objMdPetMenuUsuarioExternoDTO->setStrSinAtivo('S');
			$ret = $objMdPetMenuUsuarioExternoBD->cadastrar($objMdPetMenuUsuarioExternoDTO);
	
			return $ret;
		} catch ( Exception $e ) {
			throw new InfraException ('Erro cadastrando Menu.', $e );
		}
	}
	
	/**
	 * Short description of method alterarControlado
	 *
	 * @access protected
	 * @author Alan Campos <alan.campos@castgroup.com.br>
	 * @param  $objMdPetMenuUsuarioExternoDTO
	 * @return mixed
	 */
	protected function alterarControlado(MdPetMenuUsuarioExternoDTO $objMdPetMenuUsuarioExternoDTO) {
		try {
			// Valida Permissao
			SessaoSEI::getInstance ()->validarAuditarPermissao ('menu_peticionamento_usuario_externo_alterar', __METHOD__, $objMdPetMenuUsuarioExternoDTO );
	
			$objInfraException = new InfraException();
	
			$this->_validarCamposObrigatorios($objMdPetMenuUsuarioExternoDTO, $objInfraException);
			$this->_validarDuplicidade($objMdPetMenuUsuarioExternoDTO, $objInfraException, false);
			$objInfraException->lancarValidacoes();
	
			$objMdPetMenuUsuarioExternoBD = new MdPetMenuUsuarioExternoBD($this->getObjInfraIBanco());
			$ret = $objMdPetMenuUsuarioExternoBD->alterar($objMdPetMenuUsuarioExternoDTO);
	
			return $ret;
		} catch ( Exception $e ) {
			throw new InfraException ('Erro cadastrando Menu.', $e );
		}
	}
	
	/**
	 * Short description of method _validarCamposObrigatorios
	 *
	 * @access private
	 * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
	 * @param  $objMdPetMenuUsuarioExternoDTO
	 * @param  $objInfraException
	 * @return mixed
	 */
	private function _validarCamposObrigatorios($objMdPetMenuUsuarioExternoDTO, $objInfraException){
	
		//Nome do Menu
		if (InfraString::isBolVazia ($objMdPetMenuUsuarioExternoDTO->getStrNome())) {
			$objInfraException->adicionarValidacao('Nome do Menu não informado.');
		} else if( strlen($objMdPetMenuUsuarioExternoDTO->getStrNome()) > 30 ){
			$objInfraException->adicionarValidacao('Nome do Menu possui tamanho superior a 30 caracteres.');
		}
		
		//Tipo de Menu
		if (InfraString::isBolVazia ($objMdPetMenuUsuarioExternoDTO->getStrTipo())) {
			$objInfraException->adicionarValidacao('Tipo de Menu não informado.');
		}elseif ($objMdPetMenuUsuarioExternoDTO->getStrTipo()=='E'){
			//Url
			if (InfraString::isBolVazia ($objMdPetMenuUsuarioExternoDTO->getStrUrl())) {
					$objInfraException->adicionarValidacao('URL de Link Externo não informado.');
			} else {
				// RN10 - Validando
				require_once dirname(__FILE__).'/../util/MdPetUrlUtils.php';
				$UrlRetorno = MdPetUrlUtils::validarStrURL($objMdPetMenuUsuarioExternoDTO->getStrUrl(),$objInfraException,'Tamanho do campo excedido (máximo 2083 caracteres).', 'URL do Link Externo inválido.');
				if($UrlRetorno!== true){
					$objInfraException->adicionarValidacao($UrlRetorno);
				}
			} 
		}elseif ($objMdPetMenuUsuarioExternoDTO->getStrTipo()=='H'){
			//ConteudoHtml
			if (InfraString::isBolVazia ($objMdPetMenuUsuarioExternoDTO->getStrConteudoHtml())) {
				$objInfraException->adicionarValidacao('Conteúdo HTML não informado.');
			}
		}
		
	}
	
	/**
	 * Short description of method _validarDuplicidade
	 *
	 * @access private
	 * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
	 * @param  $objMdPetMenuUsuarioExternoDTO
	 * @param  $objInfraException
	 * @param  $cadastrar
	 * @return mixed
	 */
	private function _validarDuplicidade(MdPetMenuUsuarioExternoDTO $objMdPetMenuUsuarioExternoDTO, InfraException $objInfraException, $cadastrar){
		
		// VALIDA DUPLICAÇÃO
		// VALIDACAO A SER EXECUTADA NA INSERÇAO DE NOVOS REGISTROS
		//nao permitir nome duplicado
		
		$msg = 'Já existe Menu cadastrado.';
		$objMdPetMenuUsuarioExternoDTO2 = new MdPetMenuUsuarioExternoDTO();
		$objMdPetMenuUsuarioExternoDTO2->setStrNome($objMdPetMenuUsuarioExternoDTO->getStrNome());
		$objMdPetMenuUsuarioExternoBD = new MdPetMenuUsuarioExternoBD($this->getObjInfraIBanco());
	
		if ($cadastrar) {
			
			$ret = $objMdPetMenuUsuarioExternoBD->contar($objMdPetMenuUsuarioExternoDTO2);
	
			if ($ret > 0) {
				$objInfraException->adicionarValidacao ($msg);
			}
	
		} else {
	
			$dtoValidacao = new MdPetMenuUsuarioExternoDTO();
			$dtoValidacao->setStrNome($objMdPetMenuUsuarioExternoDTO->getStrNome(), InfraDTO::$OPER_IGUAL);
			$dtoValidacao->setNumIdMenuPeticionamentoUsuarioExterno( $objMdPetMenuUsuarioExternoDTO->getNumIdMenuPeticionamentoUsuarioExterno(), InfraDTO::$OPER_DIFERENTE );
	
			$retDuplicidade = $objMdPetMenuUsuarioExternoBD->contar( $dtoValidacao );
	
			if ($retDuplicidade > 0) {
				$objInfraException->adicionarValidacao($msg);
			}
		}
	}
}
?>