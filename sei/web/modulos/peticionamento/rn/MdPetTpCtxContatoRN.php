<?
/**
* ANATEL
*
* 29/06/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetTpCtxContatoRN extends InfraRN {
	
	public function __construct() {
		parent::__construct ();
	}
	
	protected function inicializarObjInfraIBanco() {
		return BancoSEI::getInstance ();
	}
		
	/**
	 * Short description of method excluirControlado
	 *
	 * @access protected
	 * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
	 * @param $objDTO
	 * @return void
	 */
	protected function excluirControlado($objDTO){
		
		try {
	
			//Valida Permissao TO DO revisar a tela para não deixar o log duplicado
//			SessaoSEI::getInstance ()->validarAuditarPermissao ('md_pet_tp_ctx_contato_cadastrar', __METHOD__, $objDTO );

			$objExtArqPermBD = new MdPetRelTpCtxContatoBD($this->getObjInfraIBanco());
			for($i=0;$i<count($objDTO);$i++){
				$objExtArqPermBD->excluir($objDTO[$i]);
			}
	
			//Auditoria
	
		}catch(Exception $e){
			throw new InfraException('Erro excluindo Extensão.',$e);
		}
	}
	
	/**
	 * Short description of method listarConectado
	 *
	 * @access protected
	 * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
	 * @param $objDTO
	 * @return mixed
	 */
	protected function listarConectado(MdPetRelTpCtxContatoDTO $objDTO) {

		try {
	
			//Regras de Negocio
			$objMdPetRelTpCtxContatoBD = new MdPetRelTpCtxContatoBD($this->getObjInfraIBanco());
			$ret = $objMdPetRelTpCtxContatoBD->listar($objDTO);				
			return $ret;
			
		} catch (Exception $e) {
			throw new InfraException ('Erro listando Tipo de Interessado.', $e);
		}
	}
		
	/**
	 * Short description of method consultarConectado
	 *
	 * @access protected
	 * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
	 * @param  $objDTO
	 * @return mixed
	 */
	protected function consultarConectado(MdPetRelTpCtxContatoDTO $objDTO) {

		try {
			
			// Valida Permissao			
		    $objTamanhoArquivoBD = new MdPetRelTpCtxContatoBD($this->getObjInfraIBanco());
			return $objTamanhoArquivoBD->consultar($objDTO);
			
		} catch ( Exception $e ) {
			throw new InfraException('Erro consultando Tipo de Interessado.', $e);
		}
	}
	
	/**
	 * Short description of method cadastrarControlado
	 *
	 * @access protected
	 * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
	 * @param  $objDTO
	 * @return mixed
	 */
	protected function cadastrarControlado($arrObjMdPetRelTpCtxContatoDTO) {
		
		try {
            // Valida Permissao
            SessaoSEI::getInstance()->validarAuditarPermissao('md_pet_tp_ctx_contato_cadastrar', __METHOD__, $arrObjMdPetRelTpCtxContatoDTO);
            $retorno = array();
            foreach ($arrObjMdPetRelTpCtxContatoDTO as $chave => $objMdPetRelTpCtxContatoDTO) {
                if( is_a($objMdPetRelTpCtxContatoDTO, 'MdPetRelTpCtxContatoDTO')) {
                    $objExtArqPermBD = new MdPetRelTpCtxContatoBD($this->getObjInfraIBanco());
                    $retorno[$chave] = $objExtArqPermBD->cadastrar($objMdPetRelTpCtxContatoDTO);
                }
            }
			return $retorno;
			
		} catch ( Exception $e ) {
			throw new InfraException ('Erro cadastrando Tipo de Interessado.', $e );
		}
	}
		
	private function validarTiposReservados( $arrMdPetRelTpCtxContatoDTO, InfraException $objInfraException){

		$strMensagem = "";

		if( is_array( $arrMdPetRelTpCtxContatoDTO ) && count( $arrMdPetRelTpCtxContatoDTO ) > 0 ){

			$arrIdTipo = array();

			foreach( $arrMdPetRelTpCtxContatoDTO as $itemDTO ){
				array_push( $arrIdTipo , $itemDTO);
			}

			$objTipoContatoRN = new TipoContatoRN();
			$objTipoContatoDTO = new TipoContatoDTO();
			$objTipoContatoDTO->retTodos();
						
			$objTipoContatoDTO->adicionarCriterio(array('SinSistema', 'IdTipoContato'),
					array( InfraDTO::$OPER_IGUAL , InfraDTO::$OPER_IN ),
					array( 'S', $arrIdTipo ) , 
					InfraDTO::$OPER_LOGICO_AND
			);
						
			$arrTipoContatoReservado = $objTipoContatoRN->listarRN0337( $objTipoContatoDTO );
						
			//se tiver tipos do sistema, monta mensagem de erro
			if( is_array( $arrTipoContatoReservado ) && count( $arrTipoContatoReservado ) > 0 ){
				foreach( $arrTipoContatoReservado as $itemTipoContatoDTO ){
					$strMensagem .= "\t- ". $itemTipoContatoDTO->getStrNome() . "\n" ;
				}
			}
			
		}
				
		if( $strMensagem != ""){
		  $objInfraException->adicionarValidacao( " Não permitido adicionar Tipos de Contatos reservados do Sistema. Os seguintes Tipos não são permitidos: \n\n ". $strMensagem );
		}

	}
	
	protected function cadastrarMultiploControlado( $arrContatos ){
		
		$objInfraException = new InfraException();
		$listaExclusao = array();
        $arrObjMdPetRelTpCtxContatoDTO = array();
		foreach ($arrContatos as $arrPrincipal) {
            // excluindo registros anteriores
            $objDTO = new MdPetRelTpCtxContatoDTO();
            $objDTO->retTodos();
            $cadastro = $arrPrincipal['cadastro'];

            if ($cadastro == 'S') {
                $objDTO->setStrSinCadastroInteressado('S');
                $objDTO->setStrSinSelecaoInteressado('N');

            } else if ($cadastro == 'N') {
                $objDTO->setStrSinCadastroInteressado('N');
                $objDTO->setStrSinSelecaoInteressado('S');
            }

            unset($arrPrincipal['cadastro']);

            $lista = $this->listar($objDTO);
            $listaExclusao = array_merge($listaExclusao, $lista);
            //quando for Cadastro, impedir tipos reservados, quando for seleçao, nao deve impedir
            if ($cadastro == 'S') {
                $this->validarTiposReservados($arrPrincipal, $objInfraException);
            }

            if (!$arrPrincipal) {
                $objInfraException->adicionarValidacao('Informe pelo menos um Tipo de Contato.');
            }

            $objInfraException->lancarValidacoes();


            foreach ($arrPrincipal as $numPrincipal) {

                $objDTO = new MdPetRelTpCtxContatoDTO();
                $objDTO->setNumIdTipoContextoContato($numPrincipal);

                if ($cadastro == 'S') {
                    $objDTO->setStrSinCadastroInteressado('S');
                    $objDTO->setStrSinSelecaoInteressado('N');

                } else if ($cadastro == 'N') {
                    $objDTO->setStrSinCadastroInteressado('N');
                    $objDTO->setStrSinSelecaoInteressado('S');
                }
                array_push($arrObjMdPetRelTpCtxContatoDTO, $objDTO);

            }
        }
        $this->excluir($listaExclusao);
        $objDTO = $this->cadastrar($arrObjMdPetRelTpCtxContatoDTO);
	}
	
}
?>