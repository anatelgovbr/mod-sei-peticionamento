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
	public static $DOC_FORMULARIO = 'F';

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
		$objMdPetIndicacaoInteressadoDTO->setStrSinIndicacao(self::$INDICACAO_DIRETA);
		$objMdPetIndicacaoInteressadoDTO->setStrDescricao('Indicação Direta');
		$objArrMdPetIndicacaoInteressadoDTO[] = $objMdPetIndicacaoInteressadoDTO;

		$objMdPetIndicacaoInteressadoDTO = new MdPetIndicacaoInteressadoDTO();
		$objMdPetIndicacaoInteressadoDTO->setStrSinIndicacao(self::$PROPRIO_USUARIO_EXTERNO);
		$objMdPetIndicacaoInteressadoDTO->setStrDescricao('Próprio Usuário Externo');
		$objArrMdPetIndicacaoInteressadoDTO[] = $objMdPetIndicacaoInteressadoDTO;
		
		
		
		return $objArrMdPetIndicacaoInteressadoDTO;
		}catch(Exception $e){
			throw new InfraException('Erro listando valores de Indicação de Interessado.',$e);
		}
	}

	protected function contarConectado(MdPetTipoProcessoDTO $objMdPetTipoProcessoDTO){
		$objMdPetTipoProcessoBD = new MdPetTipoProcessoBD($this->getObjInfraIBanco());
		$ret = $objMdPetTipoProcessoBD->contar($objMdPetTipoProcessoDTO);

		return $ret;

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
			$objMdPetTipoDocumentoDTO->setStrTipoDoc(self::$DOC_EXTERNO);
			$objMdPetTipoDocumentoDTO->setStrDescricao('Externo');
			$objArrMdPetTipoDocumentoDTO[] = $objMdPetTipoDocumentoDTO;
			
			$objMdPetTipoDocumentoDTO = new MdPetTipoDocumentoDTO();
			$objMdPetTipoDocumentoDTO->setStrTipoDoc(self::$DOC_GERADO);
			$objMdPetTipoDocumentoDTO->setStrDescricao('Gerado');
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
		
		else if ( strlen($objMdPetTipoProcessoDTO->getStrOrientacoes()) > 1000 ) {
			$objInfraException->adicionarValidacao('Orientações possui tamanho superior a 1000 caracteres.');
		}

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

		}

		if (($objMdPetTipoProcessoDTO->getStrSinDocGerado() == 'N' && $objMdPetTipoProcessoDTO->getStrSinDocExterno() == 'N' && $objMdPetTipoProcessoDTO->getStrSinDocFormulario() == 'N' )) {
			$objInfraException->adicionarValidacao('Documento Principal não informado.');
		}
		
		if (
		    ($objMdPetTipoProcessoDTO->getStrSinDocGerado() == 'S' || $objMdPetTipoProcessoDTO->getStrSinDocExterno() == 'S') &&
        $objMdPetTipoProcessoDTO->getStrSinDocFormulario() == 'N'
    ) {
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


	public function peticionamentoNovoCidadeDuplicada($arrObjTipoProcedimentoFiltroDTO){

  $arrTipoProcessoOrgaoCidade = array();
  $arrIdTipoProcesso = array();
  foreach ($arrObjTipoProcedimentoFiltroDTO as $key => $tpProc) {
    if(!in_array($tpProc->getNumIdTipoProcessoPeticionamento(), $arrIdTipoProcesso)){
      array_push($arrIdTipoProcesso, $tpProc->getNumIdTipoProcessoPeticionamento());
    }
  }
 
  $objMdPetRelTpProcessoUnidRN = new MdPetRelTpProcessoUnidRN();
  $objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
  $objMdPetRelTpProcessoUnidDTO->setNumIdTipoProcessoPeticionamento($arrIdTipoProcesso,InfraDTO::$OPER_IN);
  $objMdPetRelTpProcessoUnidDTO->retNumIdUnidade();
  $objMdPetRelTpProcessoUnidDTO->retNumIdOrgaoUnidade();
  $objMdPetRelTpProcessoUnidDTO->retNumIdCidadeContato();
  $objMdPetRelTpProcessoUnidDTO->retNumIdTipoProcessoPeticionamento();
  $objMdPetRelTpProcessoUnidDTO->retStrStaTipoUnidade();
  $arrobjMdPetRelTpProcessoUnidDTO = $objMdPetRelTpProcessoUnidRN->listar($objMdPetRelTpProcessoUnidDTO);

  foreach ($arrobjMdPetRelTpProcessoUnidDTO as $key => $objDTO) {
    //print_r($objDTO->getNumIdTipoProcessoPeticionamento()); die;
    if(!key_exists($objDTO->getNumIdTipoProcessoPeticionamento(), $arrTipoProcessoOrgaoCidade)){
      $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()] = array();
    }
    if(!key_exists($objDTO->getNumIdOrgaoUnidade(), $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()])){
      $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()][$objDTO->getNumIdOrgaoUnidade()] = array();  
    }

    if (!key_exists($objDTO->getNumIdCidadeContato(), $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()][$objDTO->getNumIdOrgaoUnidade()])) {
      $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()][$objDTO->getNumIdOrgaoUnidade()][$objDTO->getNumIdCidadeContato()] = 1;
    } else {
      $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()][$objDTO->getNumIdOrgaoUnidade()][$objDTO->getNumIdCidadeContato()] = $arrTipoProcessoOrgaoCidade[$objDTO->getNumIdTipoProcessoPeticionamento()][$objDTO->getNumIdOrgaoUnidade()][$objDTO->getNumIdCidadeContato()] + 1;
    }


  }
  $arrIdsTpProcesso = array_keys($arrTipoProcessoOrgaoCidade);
  //verificando se existe algum tipo de processo com divergencia de orgao e cidade iguais
  if ($arrTipoProcessoOrgaoCidade) {
    $tipoProcessoDivergencia = false;
    foreach ($arrTipoProcessoOrgaoCidade as $key => $dados) {
        foreach ($dados as $cidade) {
          foreach($cidade as $qnt){          
            if ($qnt > 1) {
              foreach ($arrObjTipoProcedimentoFiltroDTO as $chaveTpProc => $tpProc) {
                if($tpProc->getNumIdTipoProcessoPeticionamento()== $key){
                  unset($arrObjTipoProcedimentoFiltroDTO[$chaveTpProc]);
                  $chaveRemover = array_search($key, $arrIdsTpProcesso);
                  unset($arrIdsTpProcesso[$chaveRemover]);
                }
              }
            }
          }
        }
        
    }
  }

  	return $arrIdsTpProcesso;
	}

	public function restricaoOrgao(){
		
		$objTipoProcessoDTO = new MdPetTipoProcessoDTO();
  $objTipoProcessoDTO->retNumIdTipoProcessoPeticionamento();
  $objTipoProcessoDTO->retStrNomeProcesso();
  $objTipoProcessoDTO->retNumIdProcedimento();
  $objTipoProcessoDTO->retStrOrientacoes();
  $objTipoProcessoDTO->setStrSinAtivo('S');
  $objTipoProcessoDTO->setOrdStrNomeProcesso(InfraDTO::$TIPO_ORDENACAO_ASC);
   
  $objTipoProcedimentoRN = new MdPetTipoProcessoRN();
  $arrObjTipoProcedimentoFiltroDTO = $objTipoProcedimentoRN->listar($objTipoProcessoDTO);
  $arrObjTipoProcedimentoRestricaoDTO = InfraArray::converterArrInfraDTO($arrObjTipoProcedimentoFiltroDTO, 'IdProcedimento');

  //Restrição
  $arrRestricao = array();

  foreach ($arrObjTipoProcedimentoFiltroDTO as $key => $tpProc) {
   
    //Verifica se existe restrição para o tipo de processo
    $objTipoProcedRestricaoRN = new TipoProcedRestricaoRN();
    $objTipoProcedRestricaoDTO = new TipoProcedRestricaoDTO();
    $objTipoProcedRestricaoDTO->retNumIdOrgao();
    $objTipoProcedRestricaoDTO->retNumIdUnidade();
    $objTipoProcedRestricaoDTO->setNumIdTipoProcedimento($tpProc->getNumIdProcedimento());
    $arrObjTipoProcedRestricaoDTO = $objTipoProcedRestricaoRN->listar($objTipoProcedRestricaoDTO);

    $idOrgaoRestricao = InfraArray::converterArrInfraDTO($arrObjTipoProcedRestricaoDTO, 'IdOrgao');
    $idUnidadeRestricao = InfraArray::converterArrInfraDTO($arrObjTipoProcedRestricaoDTO, 'IdUnidade');
    
    $objMdPetRelTpProcessoUnidRN = new MdPetRelTpProcessoUnidRN();
    $objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
    $objMdPetRelTpProcessoUnidDTO->retTodos();
    $objMdPetRelTpProcessoUnidDTO->retStrsiglaUnidade();
    $objMdPetRelTpProcessoUnidDTO->retStrStaTipoUnidade();
    $objMdPetRelTpProcessoUnidDTO->retStrdescricaoUnidade();
    $objMdPetRelTpProcessoUnidDTO->retNumIdUnidade();
    $objMdPetRelTpProcessoUnidDTO->retNumIdOrgaoUnidade();
    $objMdPetRelTpProcessoUnidDTO->retStrDescricaoOrgao();
    $objMdPetRelTpProcessoUnidDTO->retStrSiglaOrgao();
    $objMdPetRelTpProcessoUnidDTO->retNumIdCidadeContato();
    $objMdPetRelTpProcessoUnidDTO->setNumIdTipoProcessoPeticionamento($tpProc->getNumIdTipoProcessoPeticionamento());
    $arrobjMdPetRelTpProcessoUnidDTO = $objMdPetRelTpProcessoUnidRN->listar($objMdPetRelTpProcessoUnidDTO);
    

      foreach ($arrobjMdPetRelTpProcessoUnidDTO as $objDTO) {
      
        //Verifica se tem alguma unidade ou órgão diferente dos restritos
        if(($idOrgaoRestricao && $idOrgaoRestricao[0] != null) && !in_array($objDTO->getNumIdOrgaoUnidade(), $idOrgaoRestricao)){
          $arrRestricao [] = $tpProc->getNumIdProcedimento();
        }
        if(($idUnidadeRestricao && $idUnidadeRestricao[0] != null) && !in_array($objDTO->getNumIdUnidade(), $idUnidadeRestricao)){
          $arrRestricao [] = $tpProc->getNumIdProcedimento();
        }

      }

  }
  	return $arrRestricao;
  //Fim restrição
	}

	public function validacaoCidadeDuplcada($arrobjMdPetRelTpProcessoUnidDTO){

		//VALIDAÇÃO CIDADE UNICA

		$unidade_cidade = array_map(function ($item) {
			return [
				'idCidade'  => $item->getNumIdCidadeContato(),
				'IdUnidade' => $item->getNumIdUnidade(),
				'tpUnidade' => $item->getStrStaTipoUnidade(),
				'idTpProc'  => $item->getNumIdTipoProcessoPeticionamento()
			];
		}, $arrobjMdPetRelTpProcessoUnidDTO);

		$arrIdTipoProcessoPeticionamento = array_unique(array_column($unidade_cidade, 'idTpProc'));
		$arrIdCidadeContato = array_column($unidade_cidade, 'idCidade');
		$arrIdUnidade = array_column($unidade_cidade, 'IdUnidade');

		$arrIdsUnidade = $arrIdUnidade;
		$unidades = [];

		$arrUnidadesFiltradasNovo = array_unique($arrIdCidadeContato);

	    if(!empty($arrUnidadesFiltradasNovo)){

			$unidadesFiltro = [];

		    foreach ($arrUnidadesFiltradasNovo as $key => $value) {

			    $search = $value;
			    $index = array_keys(
			        array_filter(
				        $unidade_cidade,
					    function ($value) use ($search) {
			                return (strpos($value['idCidade'], $search) !== false);
			            }
				    )
			    );

			    foreach ($index as $key => $value) {
				  $unidadesFiltro[] = $unidade_cidade[$value];
			    }

	            //Verifica se todas as unidades são iguais
		        $status = true;
			    foreach(array_column($unidadesFiltro,'IdUnidade') as $value) {
					if(array_column($unidadesFiltro,'IdUnidade')[0] != $value) {
					    $status = false;
					    break;
					}
				}

				//Caso as Unidades sejam todas iguais

				if($status == true){

				    foreach (array_column($unidadesFiltro,'IdUnidade') as $key => $value) {
						array_push($arrIdsUnidade,$value);
				    }

				}else{

					$objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
			        $objMdPetRelTpProcessoUnidDTO->setNumIdUnidade(array_column($unidadesFiltro, 'IdUnidade'), infraDTO::$OPER_IN);
					$objMdPetRelTpProcessoUnidDTO->retStrStaTipoUnidade();
					$objMdPetRelTpProcessoUnidDTO->retNumIdTipoProcessoPeticionamento();
					$arrobjMdPetRelTpProcessoUnidDTO = (new MdPetRelTpProcessoUnidRN())->listar($objMdPetRelTpProcessoUnidDTO);
					$tpProcsNegados = InfraArray::converterArrInfraDTO($arrobjMdPetRelTpProcessoUnidDTO, 'IdTipoProcessoPeticionamento');

					$objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
					$objMdPetRelTpProcessoUnidDTO->retNumIdUnidade();
					$objMdPetRelTpProcessoUnidDTO->setNumIdTipoProcessoPeticionamento($arrIdTipoProcessoPeticionamento, infraDTO::$OPER_IN);
					$arrobjMdPetRelTpProcessoUnidDTO = (new MdPetRelTpProcessoUnidRN())->listar($objMdPetRelTpProcessoUnidDTO);
					$arrIdsUnidadeUnico = InfraArray::converterArrInfraDTO($arrobjMdPetRelTpProcessoUnidDTO, 'IdUnidade');

					$unidades = $arrIdsUnidadeUnico;

			        foreach ($unidades as $key => $value) {
				        foreach (array_keys($arrIdsUnidade, $value, true) as $key) {
							unset($arrIdsUnidade[$key]);
					    }
					}
				}

				$unidadesFiltro = [];
			    //cidades repetidas - FIM

	        }

			$arrIdsUnidade = $arrIdsUnidade;

		}else{

			$status = true;
			foreach($arrIdsUnidade as $value) {
				if($arrIdsUnidade[0] != $value) {
					$status = false;
					break;
				}
			}

			//se todos os elementos são iguais, adiciona no array
			if($status == true){

				foreach ($arrIdsUnidade as $key => $value) {
					array_push($unidades, $value);
				}

			}else{

				//Limpa todas as unidades com Cidades em comum
			    //Incluindo as para serem removidas tambem.

				$objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
				$objMdPetRelTpProcessoUnidDTO->setNumIdUnidade($unidades, infraDTO::$OPER_IN);
				$objMdPetRelTpProcessoUnidDTO->retStrStaTipoUnidade();
				$objMdPetRelTpProcessoUnidDTO->retNumIdTipoProcessoPeticionamento();
				$arrobjMdPetRelTpProcessoUnidDTO = (new MdPetRelTpProcessoUnidRN())->listar($objMdPetRelTpProcessoUnidDTO);
				$arrIdTipoProcessoPeticionamento = InfraArray::converterArrInfraDTO($arrobjMdPetRelTpProcessoUnidDTO, 'IdTipoProcessoPeticionamento');

				$objMdPetRelTpProcessoUnidDTO = new MdPetRelTpProcessoUnidDTO();
				$objMdPetRelTpProcessoUnidDTO->retNumIdUnidade();
				$objMdPetRelTpProcessoUnidDTO->setNumIdTipoProcessoPeticionamento($arrIdTipoProcessoPeticionamento, infraDTO::$OPER_IN);
				$arrobjMdPetRelTpProcessoUnidDTO = (new MdPetRelTpProcessoUnidRN())->listar($objMdPetRelTpProcessoUnidDTO);
				$arrIdsUnidadeUnico = InfraArray::converterArrInfraDTO($arrobjMdPetRelTpProcessoUnidDTO, 'IdUnidade');

				//$unidades - Unidades que precisam ser removidas
				$unidades = $arrIdsUnidadeUnico;

				foreach ($unidades as $key => $value) {
					foreach (array_keys($arrIdsUnidade, $value, true) as $key) {
						unset($arrIdsUnidade[$key]);
					}
				}
			}
		}

		return array($arrIdsUnidade, $tpProcsNegados);
		//VALIDAÇÃO CIDADE UNICA - FIM

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
	public function _validarTipoProcessoAssociado(MdPetTipoProcessoDTO $objMdPetTipoProcessoDTO, InfraException $objInfraException){

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

	}

}
?>