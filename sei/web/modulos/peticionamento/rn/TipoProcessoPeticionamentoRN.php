<?
/**
* ANATEL
*
* 07/04/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class TipoProcessoPeticionamentoRN extends InfraRN { 
	
	public static $PROPRIO_USUARIO_EXTERNO = 'U';
	public static $INDICACAO_DIRETA = 'I';
	
	public static $DOC_GERADO = 'G';
	public static $DOC_EXTERNO = 'E';
	
	public static $UNIDADE_UNICA = 'U';
	public static $UNIDADES_MULTIPLAS = 'M';
	
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
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param $objTipoProcessoPeticionamentoDTO
	 * @return mixed
	 */
	protected function listarConectado(TipoProcessoPeticionamentoDTO $objTipoProcessoPeticionamentoDTO) {
	
		try {
	
			//Regras de Negocio
			//$objInfraException = new InfraException();
	
			//$objInfraException->lancarValidacoes();
			
			$objTipoProcessoPeticionamentoBD = new TipoProcessoPeticionamentoBD($this->getObjInfraIBanco());
			$ret = $objTipoProcessoPeticionamentoBD->listar($objTipoProcessoPeticionamentoDTO);
				
			return $ret;
		} catch (Exception $e) {
			throw new InfraException ('Erro listando Tipo de Processo Peticionamento.', $e);
		}
	}
	
	
	/**
	 * Short description of method listarValoresIndicacaoInteressado
	 *
	 * @access public
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @return mixed
	 */
	public function listarValoresIndicacaoInteressado(){
		
		try{
		$objArrIndicacaoInteressadoPeticionamentoDTO = array();
		
		$objIndicacaoInteressadoPeticionamentoDTO = new IndicacaoInteressadoPeticionamentoDTO();
		$objIndicacaoInteressadoPeticionamentoDTO->setStrSinIndicacao(self::$PROPRIO_USUARIO_EXTERNO);
		$objIndicacaoInteressadoPeticionamentoDTO->setStrDescricao('Próprio Usuário Externo');
		$objArrIndicacaoInteressadoPeticionamentoDTO[] = $objIndicacaoInteressadoPeticionamentoDTO;
		
		$objIndicacaoInteressadoPeticionamentoDTO = new IndicacaoInteressadoPeticionamentoDTO();
		$objIndicacaoInteressadoPeticionamentoDTO->setStrSinIndicacao(self::$INDICACAO_DIRETA);
		$objIndicacaoInteressadoPeticionamentoDTO->setStrDescricao('Indicação Direta');
		$objArrIndicacaoInteressadoPeticionamentoDTO[] = $objIndicacaoInteressadoPeticionamentoDTO;
		
		return $objArrIndicacaoInteressadoPeticionamentoDTO;
		}catch(Exception $e){
			throw new InfraException('Erro listando valores de Indicação de Interessado.',$e);
		}
	}
	
	
	/**
	 * Short description of method listarValoresTipoDocumento
	 *
	 * @access public
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @return mixed
	 */
	public function listarValoresTipoDocumento(){
	
		try{
			$objArrTipoDocumentoPeticionamentoDTO = array();
	
			$objTipoDocumentoPeticionamentoDTO = new TipoDocumentoPeticionamentoDTO();
			$objTipoDocumentoPeticionamentoDTO->setStrTipoDoc(self::$DOC_GERADO);
			$objTipoDocumentoPeticionamentoDTO->setStrDescricao('Gerado');
			$objArrTipoDocumentoPeticionamentoDTO[] = $objTipoDocumentoPeticionamentoDTO;
	
			$objTipoDocumentoPeticionamentoDTO = new TipoDocumentoPeticionamentoDTO();
			$objTipoDocumentoPeticionamentoDTO->setStrTipoDoc(self::$DOC_EXTERNO);
			$objTipoDocumentoPeticionamentoDTO->setStrDescricao('Externo');
			$objArrTipoDocumentoPeticionamentoDTO[] = $objTipoDocumentoPeticionamentoDTO;
			
			return $objArrTipoDocumentoPeticionamentoDTO;
		}catch(Exception $e){
			throw new InfraException('Erro listando valores de Documento Principal.',$e);
		}
	}
	
	
/**
	 * Short description of method consultarConectado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param  $objTipoProcessoPeticionamentoDTO
	 * @return mixed
	 */
	protected function consultarConectado(TipoProcessoPeticionamentoDTO $objTipoProcessoPeticionamentoDTO) {
		try {
			
			// Valida Permissao
			
		    $objTipoProcessoPeticionamentoBD = new TipoProcessoPeticionamentoBD($this->getObjInfraIBanco());
			$ret = $objTipoProcessoPeticionamentoBD->consultar($objTipoProcessoPeticionamentoDTO);
			
			return $ret;
		} catch ( Exception $e ) {
			throw new InfraException('Erro consultando Tipo de Processo Peticionamento.', $e);
		}
	}
	
	
	
	/**
	 * Short description of method desativarControlado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param  $arrTipoProcessoPeticionamentoDTO
	 * @return void
	 */
	protected function desativarControlado($arrTipoProcessoPeticionamentoDTO) {
	
		try {
				
			SessaoSEI::getInstance ()->validarAuditarPermissao('tipo_processo_peticionamento_desativar', __METHOD__, $arrTipoProcessoPeticionamentoDTO);
			
			$objTipoProcessoPeticionamentoBD = new TipoProcessoPeticionamentoBD($this->getObjInfraIBanco());
			for($i = 0; $i < count($arrTipoProcessoPeticionamentoDTO); $i ++) {
				$objTipoProcessoPeticionamentoBD->desativar($arrTipoProcessoPeticionamentoDTO[$i]);
			}
				
		} catch(Exception $e) {
			throw new InfraException ('Erro desativando Tipo de Processo Peticionamento.', $e );
		}
	}
	
	/**
	 * Short description of method reativarControlado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param  $arrTipoProcessoPeticionamentoDTO
	 * @return void
	 */
	protected function reativarControlado($arrTipoProcessoPeticionamentoDTO) {
	
		try {
	
			SessaoSEI::getInstance ()->validarAuditarPermissao('tipo_processo_peticionamento_reativar', __METHOD__, $arrTipoProcessoPeticionamentoDTO);
	
			$objTipoProcessoPeticionamentoBD = new TipoProcessoPeticionamentoBD($this->getObjInfraIBanco());
			for($i = 0; $i < count($arrTipoProcessoPeticionamentoDTO); $i ++) {
				$objTipoProcessoPeticionamentoBD->reativar($arrTipoProcessoPeticionamentoDTO[$i]);
			}
	
		} catch(Exception $e) {
			throw new InfraException ('Erro reativando Tipo de Processo Peticionamento.', $e );
		}
	}
	
	
	/**
	 * Short description of method excluirControlado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param  $arrTipoProcessoPeticionamentoDTO
	 * @return void
	 */
	protected function excluirControlado($arrTipoProcessoPeticionamentoDTO) {
	
		try {
	
			SessaoSEI::getInstance ()->validarAuditarPermissao('tipo_processo_peticionamento_excluir', __METHOD__, $arrTipoProcessoPeticionamentoDTO);
			$relPeticionamentoSerieRN = new RelTipoProcessoSeriePeticionamentoRN();
			$objRelTipoProcessoUnidadePeticionamentoRN = new RelTipoProcessoUnidadePeticionamentoRN();
			
			$objTipoProcessoPeticionamentoBD = new TipoProcessoPeticionamentoBD($this->getObjInfraIBanco());
			
			for($i = 0; $i < count($arrTipoProcessoPeticionamentoDTO); $i ++) {
				
				//removendo dependencias TipoProcessoPeticionamentoSerie
				$dtoFiltro = new RelTipoProcessoSeriePeticionamentoDTO();
				$dtoFiltro->retTodos();
				$dtoFiltro->setNumIdTipoProcessoPeticionamento( $arrTipoProcessoPeticionamentoDTO[$i]->getNumIdTipoProcessoPeticionamento() , InfraDTO::$OPER_IGUAL);
				
			    $arrSeriePetiocionamento = $relPeticionamentoSerieRN->listar( $dtoFiltro );	
				$relPeticionamentoSerieRN->excluir( $arrSeriePetiocionamento );
				
				
				//removendo dependência com TipoProcessoPeticionamentoUnidade
				$objRelTipoProcessoUnidadePeticionamentoDTO = new RelTipoProcessoUnidadePeticionamentoDTO();
				$objRelTipoProcessoUnidadePeticionamentoDTO->setNumIdTipoProcessoPeticionamento($arrTipoProcessoPeticionamentoDTO[$i]->getNumIdTipoProcessoPeticionamento() , InfraDTO::$OPER_IGUAL);
				$objRelTipoProcessoUnidadePeticionamentoDTO->retTodos();
				
				$arrObjRelTipoProcessoUnidadePeticionamentoDTO = $objRelTipoProcessoUnidadePeticionamentoRN->listar( $objRelTipoProcessoUnidadePeticionamentoDTO );
				$objRelTipoProcessoUnidadePeticionamentoRN->excluir($arrObjRelTipoProcessoUnidadePeticionamentoDTO);
				
				//removendo tipo processo peticionamento'
				$objTipoProcessoPeticionamentoBD->excluir($arrTipoProcessoPeticionamentoDTO[$i] );
			}
	
		} catch(Exception $e) {
			throw new InfraException ('Erro excluindo Tipo de Processo Peticionamento.', $e );
		}
	}
	
	/**
	 * Short description of method cadastrarControlado
	 *
	 * @access protected
	 * @author Alan Campos <alan.campos@castgroup.com.br>
	 * @param  $objTipoProcessoPeticionamentoDTO
	 * @return mixed
	 */
	protected function cadastrarControlado(TipoProcessoPeticionamentoDTO $objTipoProcessoPeticionamentoDTO) {
		try {
			// Valida Permissao
			SessaoSEI::getInstance ()->validarAuditarPermissao ('tipo_processo_peticionamento_cadastrar', __METHOD__, $objTipoProcessoPeticionamentoDTO );
	
			$objInfraException = new InfraException();
			
			$this->_validarCamposObrigatorios($objTipoProcessoPeticionamentoDTO, $objInfraException);
			$this->_validarDuplicidade($objTipoProcessoPeticionamentoDTO, $objInfraException, true);
			$this->_validarTipoProcessoAssociado($objTipoProcessoPeticionamentoDTO, $objInfraException);
			
			$objInfraException->lancarValidacoes();
			
			$objTipoProcessoPeticionamentoBD = new TipoProcessoPeticionamentoBD($this->getObjInfraIBanco());
			$ret = $objTipoProcessoPeticionamentoBD->cadastrar($objTipoProcessoPeticionamentoDTO);
	
			return $ret;
		} catch ( Exception $e ) {
			throw new InfraException ('Erro cadastrando Tipo de Processo.', $e );
		}
	}
	
	/**
	 * Short description of method alterarControlado
	 *
	 * @access protected
	 * @author Alan Campos <alan.campos@castgroup.com.br>
	 * @param  $objTipoProcessoPeticionamentoDTO
	 * @return mixed
	 */
	protected function alterarControlado(TipoProcessoPeticionamentoDTO $objTipoProcessoPeticionamentoDTO) {
		try {
			// Valida Permissao
			SessaoSEI::getInstance ()->validarAuditarPermissao ('tipo_processo_peticionamento_alterar', __METHOD__, $objTipoProcessoPeticionamentoDTO );
	
			$objInfraException = new InfraException();
				
			$this->_validarCamposObrigatorios($objTipoProcessoPeticionamentoDTO, $objInfraException);
			$this->_validarDuplicidade($objTipoProcessoPeticionamentoDTO, $objInfraException, false);
				
			$objInfraException->lancarValidacoes();
				
			$objTipoProcessoPeticionamentoBD = new TipoProcessoPeticionamentoBD($this->getObjInfraIBanco());
			$ret = $objTipoProcessoPeticionamentoBD->alterar($objTipoProcessoPeticionamentoDTO);
	
			return true;
		} catch ( Exception $e ) {
			throw new InfraException ('Erro cadastrando Tipo de Processo.', $e );
		}
	}
	
	/**
	 * Short description of method _validarCamposObrigatorios
	 *
	 * @access private
	 * @author Jaqueline Mendes <jaqueline.mendes@castgroup.com.br>
	 * @param  $objTipoProcessoPeticionamentoDTO
	 * @param  $objInfraException
	 * @return mixed
	 */
	private function _validarCamposObrigatorios($objTipoProcessoPeticionamentoDTO, $objInfraException){
		$valorParametroHipoteseLegal = $this->_retornaValorParametroHipoteseLegal();
		//Tipo de Processo
		if (InfraString::isBolVazia ($objTipoProcessoPeticionamentoDTO->getNumIdProcedimento())) {
			$objInfraException->adicionarValidacao('Tipo de Processo Associado não informado.');
		}
		
		if (InfraString::isBolVazia ($objTipoProcessoPeticionamentoDTO->getStrOrientacoes())) {
			$objInfraException->adicionarValidacao('Orientações não informada.');
		}
		
		else if ( strlen($objTipoProcessoPeticionamentoDTO->getStrOrientacoes()) > 500 ) {
			$objInfraException->adicionarValidacao('Orientações possui tamanho superior a 500 caracteres.');
		}
		
/*		if (InfraString::isBolVazia ($objTipoProcessoPeticionamentoDTO->getNumIdUnidade())) {
			$objInfraException->adicionarValidacao('Unidade não informada.');
		}*/
		
		if (($objTipoProcessoPeticionamentoDTO->getStrSinIIProprioUsuarioExterno() == 'N' && $objTipoProcessoPeticionamentoDTO->getStrSinIIIndicacaoDireta() == 'N')) {
			$objInfraException->adicionarValidacao('Indicação de Interessado não informada.');
		}
		
		if (($objTipoProcessoPeticionamentoDTO->getStrSinNaPadrao() == 'S' && InfraString::isBolVazia($objTipoProcessoPeticionamentoDTO->getStrStaNivelAcesso()))) {
			$objInfraException->adicionarValidacao('Nível de Acesso não informado.');
		} 
		
		//se informar nivel de acesso E o nivel for restrito ou sigiloso, PRECISA informar hipotese legal padrao
		else if($objTipoProcessoPeticionamentoDTO->getStrSinNaPadrao() == 'S' && $objTipoProcessoPeticionamentoDTO->getStrStaNivelAcesso() == ProtocoloRN::$NA_RESTRITO && $valorParametroHipoteseLegal != '0'){
			
			if( InfraString::isBolVazia( $objTipoProcessoPeticionamentoDTO->getNumIdHipoteseLegal() ) ){
				$objInfraException->adicionarValidacao('Hipótese legal não informada.');
			}
			//$objInfraException->adicionarValidacao('Nível de Acesso não informado.');
		}
		
		if (($objTipoProcessoPeticionamentoDTO->getStrSinDocGerado() == 'N' && $objTipoProcessoPeticionamentoDTO->getStrSinDocExterno() == 'N')) {
			$objInfraException->adicionarValidacao('Documento Principal não informado.');
		}
		
		if (($objTipoProcessoPeticionamentoDTO->getStrSinDocGerado() == 'S' || $objTipoProcessoPeticionamentoDTO->getStrSinDocExterno() == 'S')) {
			if (InfraString::isBolVazia ($objTipoProcessoPeticionamentoDTO->getNumIdSerie())) {
				$objInfraException->adicionarValidacao('Tipo de Documento principal não informada.');
			}
		}
		
	
	}
	
	private function _retornaValorParametroHipoteseLegal(){
		$objInfraParametroDTO = new InfraParametroDTO();
		$objMdPetParametroRN  = new MdPetParametroRN();
		$objInfraParametroDTO->retTodos();
		$objInfraParametroDTO->setStrNome('SEI_HABILITAR_HIPOTESE_LEGAL');
		$objInfraParametroDTO = $objMdPetParametroRN->consultar($objInfraParametroDTO);
		$valorParametroHipoteseLegal = $objInfraParametroDTO->getStrValor();
		return $valorParametroHipoteseLegal;
	}
	
	
	/**
	 * Short description of method _validarDuplicidade
	 *
	 * @access private
	 * @author Jaqueline Mendes <jaqueline.mendes@castgroup.com.br>
	 * @param  $objTipoProcessoPeticionamentoDTO
	 * @param  $objInfraException
	 * @param  $cadastrar
	 * @return mixed
	 */
	private function _validarDuplicidade(TipoProcessoPeticionamentoDTO $objTipoProcessoPeticionamentoDTO, InfraException $objInfraException, $cadastrar){
	// VALIDA DUPLICAÇÃO
		// VALIDACAO A SER EXECUTADA NA INSERÇAO DE NOVOS REGISTROS
		
		$msg = 'Este Tipo de Processo já possui cadastro para peticionamento. Não é possível fazer dois cadastros de peticionamento para o mesmo Tipo de Processo.';
		$objTipoProcessoPeticionamentoDTO2 = new TipoProcessoPeticionamentoDTO();
		$objTipoProcessoPeticionamentoDTO2->setNumIdProcedimento($objTipoProcessoPeticionamentoDTO->getNumIdProcedimento());
		
		$objTipoProcessoPeticionamentoBD = new TipoProcessoPeticionamentoBD($this->getObjInfraIBanco());
		
		
		if ($cadastrar) {
			$ret = $objTipoProcessoPeticionamentoBD->contar($objTipoProcessoPeticionamentoDTO2);
				
			if ($ret > 0) {
				$objInfraException->adicionarValidacao ($msg);
			} // VALIDACAO A SER EXECUTADA QUANDO É FEITO UPDATE DE REGISTROS
				
		} else {
				
			$dtoValidacao = new TipoProcessoPeticionamentoDTO();
			$dtoValidacao->setNumIdProcedimento($objTipoProcessoPeticionamentoDTO->getNumIdProcedimento(), InfraDTO::$OPER_IGUAL);
			$dtoValidacao->setNumIdTipoProcessoPeticionamento( $objTipoProcessoPeticionamentoDTO->getNumIdTipoProcessoPeticionamento(), InfraDTO::$OPER_DIFERENTE );
				
			$retDuplicidade = $objTipoProcessoPeticionamentoBD->contar( $dtoValidacao );
				
			if ($retDuplicidade > 0) {
				$objInfraException->adicionarValidacao($msg);
			}
		}
	}
	
	/**
	 * Short description of method _validarTipoProcessoAssociado
	 *
	 * @access private
	 * @author Marcelo Bezerra <marcelo.cast@castgroup.com.br>
	 * @param  $objTipoProcessoPeticionamentoDTO
	 * @param  $objInfraException
	 * @return mixed
	 */
	private function _validarTipoProcessoAssociado(TipoProcessoPeticionamentoDTO $objTipoProcessoPeticionamentoDTO, InfraException $objInfraException){

		//VALIDA NOVA REGRA ADICIONADA
		// somente aceita tipo de processo que na parametrização do SEI tenha
		//indicação de pelo menos uma sugestao de assunto
		
		$relTipoProcedimentoDTO = new RelTipoProcedimentoAssuntoDTO();
		$relTipoProcedimentoDTO->retTodos();
		$relTipoProcedimentoDTO->setNumIdTipoProcedimento( $objTipoProcessoPeticionamentoDTO->getNumIdProcedimento() );
		
		$relTipoProcedimentoRN = new RelTipoProcedimentoAssuntoRN();
		$arrLista = $relTipoProcedimentoRN->listarRN0192( $relTipoProcedimentoDTO );
		
		if( !is_array( $arrLista ) || count( $arrLista ) == 0 ){
			$msg = "Por favor informe um tipo de processo que na parametrização do SEI tenha indicação de pelo menos uma sugestão de assunto.";
			$objInfraException->adicionarValidacao ($msg);
		}
		
		//TipoProcedimentoDTO - IdProcedimento
		//IdTipoProcedimento
		//ObjRelTipoProcedimentoAssuntoDTO
		
	}
	
}
?>