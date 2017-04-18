<?
/**
* ANATEL
*
* 07/04/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetTipoProcessoRN extends InfraRN { 
	
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
	 * @param $objMdPetTipoProcessoDTO
	 * @return mixed
	 */
	protected function listarConectado(MdPetTipoProcessoDTO $objMdPetTipoProcessoDTO) {
	
		try {
	
			//Regras de Negocio
			//$objInfraException = new InfraException();
	
			//$objInfraException->lancarValidacoes();
			
			$objMdPetTipoProcessoBD = new MdPetTipoProcessoBD($this->getObjInfraIBanco());
			$ret = $objMdPetTipoProcessoBD->listar($objMdPetTipoProcessoDTO);
				
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
		$objArrMdPetIndicacaoInteressadoDTO = array();
		
		$objMdPetIndicacaoInteressadoDTO = new MdPetIndicacaoInteressadoDTO();
		$objMdPetIndicacaoInteressadoDTO->setStrSinIndicacao(self::$PROPRIO_USUARIO_EXTERNO);
		$objMdPetIndicacaoInteressadoDTO->setStrDescricao('Próprio Usuário Externo');
		$objArrMdPetIndicacaoInteressadoDTO[] = $objMdPetIndicacaoInteressadoDTO;
		
		$objMdPetIndicacaoInteressadoDTO = new MdPetIndicacaoInteressadoDTO();
		$objMdPetIndicacaoInteressadoDTO->setStrSinIndicacao(self::$INDICACAO_DIRETA);
		$objMdPetIndicacaoInteressadoDTO->setStrDescricao('Indicação Direta');
		$objArrMdPetIndicacaoInteressadoDTO[] = $objMdPetIndicacaoInteressadoDTO;
		
		return $objArrMdPetIndicacaoInteressadoDTO;
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
			$objArrMdPetTipoDocumentoDTO = array();
	
			$objMdPetTipoDocumentoDTO = new MdPetTipoDocumentoDTO();
			$objMdPetTipoDocumentoDTO->setStrTipoDoc(self::$DOC_GERADO);
			$objMdPetTipoDocumentoDTO->setStrDescricao('Gerado');
			$objArrMdPetTipoDocumentoDTO[] = $objMdPetTipoDocumentoDTO;
	
			$objMdPetTipoDocumentoDTO = new MdPetTipoDocumentoDTO();
			$objMdPetTipoDocumentoDTO->setStrTipoDoc(self::$DOC_EXTERNO);
			$objMdPetTipoDocumentoDTO->setStrDescricao('Externo');
			$objArrMdPetTipoDocumentoDTO[] = $objMdPetTipoDocumentoDTO;
			
			return $objArrMdPetTipoDocumentoDTO;
		}catch(Exception $e){
			throw new InfraException('Erro listando valores de Documento Principal.',$e);
		}
	}
	
	
/**
	 * Short description of method consultarConectado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param  $objMdPetTipoProcessoDTO
	 * @return mixed
	 */
	protected function consultarConectado(MdPetTipoProcessoDTO $objMdPetTipoProcessoDTO) {
		try {
			
			// Valida Permissao
			
		    $objMdPetTipoProcessoBD = new MdPetTipoProcessoBD($this->getObjInfraIBanco());
			$ret = $objMdPetTipoProcessoBD->consultar($objMdPetTipoProcessoDTO);
			
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
	 * @param  $arrMdPetTipoProcessoDTO
	 * @return void
	 */
	protected function desativarControlado($arrMdPetTipoProcessoDTO) {
	
		try {

			SessaoSEI::getInstance ()->validarAuditarPermissao('md_pet_tipo_processo_desativar', __METHOD__, $arrMdPetTipoProcessoDTO);

			$objMdPetTipoProcessoBD = new MdPetTipoProcessoBD($this->getObjInfraIBanco());
			for($i = 0; $i < count($arrMdPetTipoProcessoDTO); $i ++) {
				$objMdPetTipoProcessoBD->desativar($arrMdPetTipoProcessoDTO[$i]);
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
	 * @param  $arrMdPetTipoProcessoDTO
	 * @return void
	 */
	protected function reativarControlado($arrMdPetTipoProcessoDTO) {
	
		try {

			SessaoSEI::getInstance ()->validarAuditarPermissao('md_pet_tipo_processo_reativar', __METHOD__, $arrMdPetTipoProcessoDTO);

			$objMdPetTipoProcessoBD = new MdPetTipoProcessoBD($this->getObjInfraIBanco());
			for($i = 0; $i < count($arrMdPetTipoProcessoDTO); $i ++) {
				$objMdPetTipoProcessoBD->reativar($arrMdPetTipoProcessoDTO[$i]);
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
	 * @param  $arrMdPetTipoProcessoDTO
	 * @return void
	 */
	protected function excluirControlado($arrMdPetTipoProcessoDTO) {
	
		try {

			SessaoSEI::getInstance ()->validarAuditarPermissao('md_pet_tipo_processo_excluir', __METHOD__, $arrMdPetTipoProcessoDTO);
			$objMdPetRelTpProcSerieRN = new MdPetRelTpProcSerieRN();
			$objMdPetRelTpProcessoUnidRN = new MdPetRelTpProcessoUnidRN();
			
			$objMdPetTipoProcessoBD = new MdPetTipoProcessoBD($this->getObjInfraIBanco());
			
			for($i = 0; $i < count($arrMdPetTipoProcessoDTO); $i ++) {
				
				//removendo dependencias TipoProcessoPeticionamentoSerie
				$dtoFiltro = new MdPetRelTpProcSerieDTO();
				$dtoFiltro->retTodos();
				$dtoFiltro->setNumIdTipoProcessoPeticionamento( $arrMdPetTipoProcessoDTO[$i]->getNumIdTipoProcessoPeticionamento() , InfraDTO::$OPER_IGUAL);
				
			    $arrSeriePetiocionamento = $objMdPetRelTpProcSerieRN->listar( $dtoFiltro );	
				$objMdPetRelTpProcSerieRN->excluir( $arrSeriePetiocionamento );
				
				
				//removendo dependência com TipoProcessoPeticionamentoUnidade
				$objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
				$objMdPetRelTpProcessoUnidDTO->setNumIdTipoProcessoPeticionamento($arrMdPetTipoProcessoDTO[$i]->getNumIdTipoProcessoPeticionamento() , InfraDTO::$OPER_IGUAL);
				$objMdPetRelTpProcessoUnidDTO->retTodos();
				
				$arrObjMdPetRelTpProcessoUnidDTO = $objMdPetRelTpProcessoUnidRN->listar( $objMdPetRelTpProcessoUnidDTO );
				$objMdPetRelTpProcessoUnidRN->excluir($arrObjMdPetRelTpProcessoUnidDTO);
				
				//removendo tipo processo peticionamento'
				$objMdPetTipoProcessoBD->excluir($arrMdPetTipoProcessoDTO[$i] );
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
	 * @param  $objMdPetTipoProcessoDTO
	 * @return mixed
	 */
	protected function cadastrarControlado(MdPetTipoProcessoDTO $objMdPetTipoProcessoDTO) {
		try {
			// Valida Permissao
			SessaoSEI::getInstance ()->validarAuditarPermissao ('md_pet_tipo_processo_cadastrar', __METHOD__, $objMdPetTipoProcessoDTO );

			$objInfraException = new InfraException();
			
			$this->_validarCamposObrigatorios($objMdPetTipoProcessoDTO, $objInfraException);
			$this->_validarDuplicidade($objMdPetTipoProcessoDTO, $objInfraException, true);
			$this->_validarTipoProcessoAssociado($objMdPetTipoProcessoDTO, $objInfraException);
			
			$objInfraException->lancarValidacoes();
			
			$objMdPetTipoProcessoBD = new MdPetTipoProcessoBD($this->getObjInfraIBanco());
			$ret = $objMdPetTipoProcessoBD->cadastrar($objMdPetTipoProcessoDTO);
	
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
	 * @param  $objMdPetTipoProcessoDTO
	 * @return mixed
	 */
	protected function alterarControlado(MdPetTipoProcessoDTO $objMdPetTipoProcessoDTO) {
		try {
			// Valida Permissao
			SessaoSEI::getInstance ()->validarAuditarPermissao ('md_pet_tipo_processo_alterar', __METHOD__, $objMdPetTipoProcessoDTO );

			$objInfraException = new InfraException();
				
			$this->_validarCamposObrigatorios($objMdPetTipoProcessoDTO, $objInfraException);
			$this->_validarDuplicidade($objMdPetTipoProcessoDTO, $objInfraException, false);
				
			$objInfraException->lancarValidacoes();
				
			$objMdPetTipoProcessoBD = new MdPetTipoProcessoBD($this->getObjInfraIBanco());
			$ret = $objMdPetTipoProcessoBD->alterar($objMdPetTipoProcessoDTO);
	
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
	 * @param  $objMdPetTipoProcessoDTO
	 * @param  $objInfraException
	 * @return mixed
	 */
	private function _validarCamposObrigatorios($objMdPetTipoProcessoDTO, $objInfraException){
		$valorParametroHipoteseLegal = $this->_retornaValorParametroHipoteseLegal();
		//Tipo de Processo
		if (InfraString::isBolVazia ($objMdPetTipoProcessoDTO->getNumIdProcedimento())) {
			$objInfraException->adicionarValidacao('Tipo de Processo Associado não informado.');
		}
		
		if (InfraString::isBolVazia ($objMdPetTipoProcessoDTO->getStrOrientacoes())) {
			$objInfraException->adicionarValidacao('Orientações não informada.');
		}
		
		else if ( strlen($objMdPetTipoProcessoDTO->getStrOrientacoes()) > 500 ) {
			$objInfraException->adicionarValidacao('Orientações possui tamanho superior a 500 caracteres.');
		}
		
/*		if (InfraString::isBolVazia ($objMdPetTipoProcessoDTO->getNumIdUnidade())) {
			$objInfraException->adicionarValidacao('Unidade não informada.');
		}*/
		
		if (($objMdPetTipoProcessoDTO->getStrSinIIProprioUsuarioExterno() == 'N' && $objMdPetTipoProcessoDTO->getStrSinIIIndicacaoDireta() == 'N')) {
			$objInfraException->adicionarValidacao('Indicação de Interessado não informada.');
		}
		
		if (($objMdPetTipoProcessoDTO->getStrSinNaPadrao() == 'S' && InfraString::isBolVazia($objMdPetTipoProcessoDTO->getStrStaNivelAcesso()))) {
			$objInfraException->adicionarValidacao('Nível de Acesso não informado.');
		} 
		
		//se informar nivel de acesso E o nivel for restrito ou sigiloso, PRECISA informar hipotese legal padrao
		else if($objMdPetTipoProcessoDTO->getStrSinNaPadrao() == 'S' && $objMdPetTipoProcessoDTO->getStrStaNivelAcesso() == ProtocoloRN::$NA_RESTRITO && $valorParametroHipoteseLegal != '0'){
			
			if( InfraString::isBolVazia( $objMdPetTipoProcessoDTO->getNumIdHipoteseLegal() ) ){
				$objInfraException->adicionarValidacao('Hipótese legal não informada.');
			}
			//$objInfraException->adicionarValidacao('Nível de Acesso não informado.');
		}
		
		if (($objMdPetTipoProcessoDTO->getStrSinDocGerado() == 'N' && $objMdPetTipoProcessoDTO->getStrSinDocExterno() == 'N')) {
			$objInfraException->adicionarValidacao('Documento Principal não informado.');
		}
		
		if (($objMdPetTipoProcessoDTO->getStrSinDocGerado() == 'S' || $objMdPetTipoProcessoDTO->getStrSinDocExterno() == 'S')) {
			if (InfraString::isBolVazia ($objMdPetTipoProcessoDTO->getNumIdSerie())) {
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
	 * @param  $objMdPetTipoProcessoDTO
	 * @param  $objInfraException
	 * @param  $cadastrar
	 * @return mixed
	 */
	private function _validarDuplicidade(MdPetTipoProcessoDTO $objMdPetTipoProcessoDTO, InfraException $objInfraException, $cadastrar){
	// VALIDA DUPLICAÇÃO
		// VALIDACAO A SER EXECUTADA NA INSERÇAO DE NOVOS REGISTROS
		
		$msg = 'Este Tipo de Processo já possui cadastro para peticionamento. Não é possível fazer dois cadastros de peticionamento para o mesmo Tipo de Processo.';
		$objMdPetTipoProcessoDTO2 = new MdPetTipoProcessoDTO();
		$objMdPetTipoProcessoDTO2->setNumIdProcedimento($objMdPetTipoProcessoDTO->getNumIdProcedimento());
		
		$objMdPetTipoProcessoBD = new MdPetTipoProcessoBD($this->getObjInfraIBanco());
		
		
		if ($cadastrar) {
			$ret = $objMdPetTipoProcessoBD->contar($objMdPetTipoProcessoDTO2);
				
			if ($ret > 0) {
				$objInfraException->adicionarValidacao ($msg);
			} // VALIDACAO A SER EXECUTADA QUANDO É FEITO UPDATE DE REGISTROS
				
		} else {
				
			$dtoValidacao = new MdPetTipoProcessoDTO();
			$dtoValidacao->setNumIdProcedimento($objMdPetTipoProcessoDTO->getNumIdProcedimento(), InfraDTO::$OPER_IGUAL);
			$dtoValidacao->setNumIdTipoProcessoPeticionamento( $objMdPetTipoProcessoDTO->getNumIdTipoProcessoPeticionamento(), InfraDTO::$OPER_DIFERENTE );
				
			$retDuplicidade = $objMdPetTipoProcessoBD->contar( $dtoValidacao );
				
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
	 * @param  $objMdPetTipoProcessoDTO
	 * @param  $objInfraException
	 * @return mixed
	 */
	private function _validarTipoProcessoAssociado(MdPetTipoProcessoDTO $objMdPetTipoProcessoDTO, InfraException $objInfraException){

		//VALIDA NOVA REGRA ADICIONADA
		// somente aceita tipo de processo que na parametrização do SEI tenha
		//indicação de pelo menos uma sugestao de assunto
		
		$relTipoProcedimentoDTO = new RelTipoProcedimentoAssuntoDTO();
		$relTipoProcedimentoDTO->retTodos();
		$relTipoProcedimentoDTO->setNumIdTipoProcedimento( $objMdPetTipoProcessoDTO->getNumIdProcedimento() );
		
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