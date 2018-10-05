<?
/**
 * ANATEL
 *
 * 06/12/2016 - criado por marcelo.bezerra - CAST
 *
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetContatoRN extends InfraRN {
	
	public static $STR_CONTATO_SISTEMA      = 'Sistemas';
	public static $STR_NOME_CONTATO_MODULO  = 'Usuário Automático do Sistema: Módulo de Peticionamento e Intimação Eletrônicos';
	
	public static $STR_SIGLA_CONTATO_MODULO = 'Usuario_Peticionamento';
	public static $STR_INFRA_PARAMETRO_SIGLA_CONTATO = 'MODULO_PETICIONAMENTO_ID_USUARIO_SISTEMA';
	
	public static $STR_PAIS_CONTATO_MODULO = 'Brasil';
	public static $SIM = 'S';
	public static $NAO = 'N';
	
	public function __construct() {
		parent::__construct ();
	}
	
	protected function inicializarObjInfraIBanco() {
		return BancoSEI::getInstance ();
	}
	
	protected function pesquisarConectado(ContatoDTO $objContatoDTO){
		
		try {
			
			//Regras de Negocio
			$objInfraException = new InfraException();
			
			
			if ($objContatoDTO->isSetNumIdGrupoContato()){
				if ($objContatoDTO->getNumIdGrupoContato()==null) {
					$objContatoDTO->unSetNumIdGrupoContato();
				}else{
					$objRelGrupoContatoDTO = new RelGrupoContatoDTO();
					$objRelGrupoContatoRN = new RelGrupoContatoRN();
					
					$objRelGrupoContatoDTO->retNumIdContato();
					$objRelGrupoContatoDTO->setNumIdGrupoContato($objContatoDTO->getNumIdGrupoContato());
					$arrRelGrupoContatoDTO = $objRelGrupoContatoRN->listarRN0463($objRelGrupoContatoDTO);
					$arr = array();
					
					for ($i=0;$i<count($arrRelGrupoContatoDTO);$i++){
						$arr[$i] = $arrRelGrupoContatoDTO[$i]->getNumIdContato();
					}
					
					if (count($arr)>0){
						$objContatoDTO->setNumIdContato($arr,InfraDTO::$OPER_IN);
					}else{
						$objContatoDTO->setNumIdContato(null);
					}
				}
			}
			
			if ($objContatoDTO->isSetStrPalavrasPesquisa()){
				if (trim($objContatoDTO->getStrPalavrasPesquisa())!=''){
					
					$strPalavrasPesquisa = InfraString::prepararIndexacao($objContatoDTO->getStrPalavrasPesquisa(),false);
					
					$arrPalavrasPesquisa = explode(' ',$strPalavrasPesquisa);
					
					$numPalavrasPesquisa = count($arrPalavrasPesquisa);
					
					if ($numPalavrasPesquisa){
						for($i=0;$i<$numPalavrasPesquisa;$i++){
							$arrPalavrasPesquisa[$i] = '%'.$arrPalavrasPesquisa[$i].'%';
						}
						
						if ($numPalavrasPesquisa==1){
							$objContatoDTO->setStrIdxContato($arrPalavrasPesquisa[0],InfraDTO::$OPER_LIKE);
						}else{
							$a = array_fill(0,$numPalavrasPesquisa,'IdxContato');
							$b = array_fill(0,$numPalavrasPesquisa,InfraDTO::$OPER_LIKE);
							$d = array_fill(0,$numPalavrasPesquisa-1,InfraDTO::$OPER_LOGICO_AND);
							$objContatoDTO->adicionarCriterio($a,$b,$arrPalavrasPesquisa,$d);
						}
					}
				}else{
					$objContatoDTO->unSetStrPalavrasPesquisa();
				}
			}
			
			if ($objContatoDTO->isSetNumIdTipoContato()){
				if ($objContatoDTO->getNumIdTipoContato()==null) {
					$objContatoDTO->unSetNumIdTipoContato();
				}
			}
			
			//Se informou pelo menos uma data
			if ($objContatoDTO->isSetDtaNascimentoInicio() || $objContatoDTO->isSetDtaNascimentoFim()){
				
				if (!$objContatoDTO->isSetDtaNascimentoInicio() || InfraString::isBolVazia($objContatoDTO->getDtaNascimentoInicio())){
					$objInfraException->lancarValidacao('Data inicial do período de nascimento não informada.');
				}
				
				if (!$objContatoDTO->isSetDtaNascimentoFim() || InfraString::isBolVazia($objContatoDTO->getDtaNascimentoFim())){
					$objInfraException->lancarValidacao('Data final do período de nascimento não informada.');
				}
				
				$strAnoAtual = Date("Y");
				$strDataInicio = $objContatoDTO->getDtaNascimentoInicio().'/'.$strAnoAtual;
				
				if (!InfraData::validarData($strDataInicio)){
					$objInfraException->lancarValidacao('Data inicial do período de nascimento inválida.');
				}
				
				$strDataFim = $objContatoDTO->getDtaNascimentoFim().'/'.$strAnoAtual;
				if (!InfraData::validarData($strDataFim)){
					$objInfraException->lancarValidacao('Data final do período de nascimento inválida.');
				}
				
				if (InfraData::compararDatas($strDataInicio,$strDataFim)<0){
					$objInfraException->lancarValidacao('Período de datas de nascimento inválido.');
				}
				
				$objContatoDTO->setDtaNascimento(null,InfraDTO::$OPER_DIFERENTE);
				
				$dto = new ContatoDTO();
				$dto->setDistinct(true);
				$dto->retDtaNascimento();
				$dto->setDtaNascimento(null,InfraDTO::$OPER_DIFERENTE);
				$arr = $this->listarRN0325($dto);
				
				
				$arrCriterios = array();
				foreach($arr as $dto){
					$strAno = substr($dto->getDtaNascimento(),6,4);
					if (!in_array($strAno,$arrCriterios)){
						//Adiciona critério com o nome igual ao do ano
						
						$strDataIni = $objContatoDTO->getDtaNascimentoInicio().'/'.$strAno;
						$strDataFim = $objContatoDTO->getDtaNascimentoFim().'/'.$strAno;
						
						if (!InfraData::validarData($strDataIni)){
							if (substr($strDataIni,0,5)=='29/02'){
								$strDataIni = '01/03/'.$strAno;
							}else{
								throw new InfraException('Data inicial inválida.');
							}
						}
						if (!InfraData::validarData($strDataFim)){
							if (substr($strDataFim,0,5)=='29/02'){
								$strDataFim = '28/02/'.$strAno;
							}else{
								throw new InfraException('Data final inválida.');
							}
						}
						
						$objContatoDTO->adicionarCriterio(array('Nascimento','Nascimento'),
								array(InfraDTO::$OPER_MAIOR_IGUAL,InfraDTO::$OPER_MENOR_IGUAL),
								array($strDataIni,$strDataFim),
								array(InfraDTO::$OPER_LOGICO_AND),
								$strAno);
						$arrCriterios[] = $strAno;
					}
				}
				
				$arrOperadores = array_fill(0,count($arrCriterios)-1,InfraDTO::$OPER_LOGICO_OR);
				$objContatoDTO->agruparCriterios($arrCriterios,$arrOperadores);
				
			}
			
			$objInfraException->lancarValidacoes();
			return $this->listarRN0325($objContatoDTO);
			
			//Auditoria
		}catch(Exception $e){
			throw new InfraException('Erro pesquisando Contato.',$e);
		}
	}
	
	protected function listarRN0325Conectado(ContatoDTO $objContatoDTO) {
		
		try {
			
			$objContatoBD = new ContatoBD($this->getObjInfraIBanco());
			$ret = $objContatoBD->listar($objContatoDTO);
			
			//Auditoria
			return $ret;
			
		}catch(Exception $e){
			throw new InfraException('Erro listando Contatos.',$e);
		}
	}
	
	protected function inserirContatoModuloPetControlado(){
		try {
			$objContatoDTO = null;
			$idTpContato   = $this->_getTipoContatoSistema();
			$idPaisContato = $this->_getIdBrasil();
			
			$numProxSeq = $this->getObjInfraIBanco()->getValorSequencia('seq_contato');
			$idxContato    = $this->getIdxContatoUsuario();
			
			if($idTpContato && $numProxSeq){
				$objContatoDTO = new ContatoDTO();
				$objContatoDTO->setNumIdContato($numProxSeq);
				$objContatoDTO->setNumIdContatoAssociado($numProxSeq);
				$objContatoDTO->setNumIdTipoContato($idTpContato);
				$objContatoDTO->setDthCadastro(InfraData::getStrDataHoraAtual());
				$objContatoDTO->setStrStaNatureza(ContatoRN::$TN_PESSOA_JURIDICA);
				$objContatoDTO->setStrNome(MdPetContatoRN::$STR_NOME_CONTATO_MODULO);
				$objContatoDTO->setStrSigla(MdPetContatoRN::$STR_SIGLA_CONTATO_MODULO);
				$objContatoDTO->setNumIdPais($idPaisContato);
				$objContatoDTO->setStrSinEnderecoAssociado(MdPetContatoRN::$NAO);
				$objContatoDTO->setStrSinAtivo(MdPetContatoRN::$SIM);
				$objContatoDTO->setStrIdxContato($idxContato);
				$this->_setCamposNullsContato($objContatoDTO);
				
				$objContatoDTO = $this->cadastrarControlado($objContatoDTO);
			}
			
			return $objContatoDTO;
			
		}catch (Exception $e)
		{
			throw new InfraException('Erro inserindo Contato.',$e);
		}
	}
	
	public function getIdxContatoUsuario(){
		$strIndexacao = '';
		$strIndexacao .= ' '.MdPetContatoRN::$STR_SIGLA_CONTATO_MODULO;
		$strIndexacao .= ' '.MdPetContatoRN::$STR_NOME_CONTATO_MODULO;
		$strIndexacao = InfraString::prepararIndexacao($strIndexacao);
		
		return $strIndexacao;
	}
	
	private function _setCamposNullsContato(&$objContatoDTO){
		
		$objContatoDTO->setStrStaGenero(null);
		$objContatoDTO->setDblCpf(null);
		$objContatoDTO->setDblRg(null);
		$objContatoDTO->setDblCnpj(null);
		$objContatoDTO->setNumIdCargo(null);
		$objContatoDTO->setStrOrgaoExpedidor(null);
		$objContatoDTO->setStrMatricula(null);
		$objContatoDTO->setStrMatriculaOab(null);
		$objContatoDTO->setDtaNascimento(null);
		$objContatoDTO->setStrTelefoneFixo(null);
		$objContatoDTO->setStrTelefoneCelular(null);
		$objContatoDTO->setStrEmail(null);
		$objContatoDTO->setStrSitioInternet(null);
		$objContatoDTO->setStrEndereco(null);
		$objContatoDTO->setStrComplemento(null);
		$objContatoDTO->setStrBairro(null);
		$objContatoDTO->setStrCep(null);
		$objContatoDTO->setStrObservacao(null);
		$objContatoDTO->setNumIdUf(null);
		$objContatoDTO->setNumIdCidade(null);
	}
	
	private function _getIdBrasil(){
		$objPaisRN = new PaisRN();
		
		$objPaisDTO = new PaisDTO();
		$objPaisDTO->setStrNome(MdPetContatoRN::$STR_PAIS_CONTATO_MODULO);
		$objPaisDTO->retNumIdPais();
		$objPaisDTO = $objPaisRN->consultar($objPaisDTO);
		
		$idPais = !is_null($objPaisDTO) ? $objPaisDTO->getNumIdPais() : null;
		
		return $idPais;
	}
	
	private function _getTipoContatoSistema(){
		
		//RN
		$objTipoContatoRN  = new TipoContatoRN();
		
		$objTipoContatoDTO = new TipoContatoDTO();
		$objTipoContatoDTO->setStrNome(MdPetContatoRN::$STR_CONTATO_SISTEMA);
		$objTipoContatoDTO->retNumIdTipoContato();
		
		$objTipoContatoDTO = $objTipoContatoRN->consultarRN0336($objTipoContatoDTO);
		$idTpContato =  !is_null($objTipoContatoDTO) ? $objTipoContatoDTO->getNumIdTipoContato() : null;
		
		return $idTpContato;
	}
	
	
	protected function cadastrarControlado($objContatoDTO)
	{
		try {
			$objContatoBD = new ContatoBD($this->getObjInfraIBanco());
			$objContatoDTO = $objContatoBD->cadastrar($objContatoDTO);
			
			return $objContatoDTO;
		} catch (Exception $e) {
			throw new InfraException('Erro cadastrando Contato.', $e);
		}
	}
	
	protected function getObjContatoVinculadoUsuarioPetConectado(){
		$objMdPetIntAceiteRN = new MdPetIntAceiteRN();
		$objUsuarioPetRN     = new MdPetIntUsuarioRN();
		$objUsuarioPetDTO    = $objUsuarioPetRN->getObjUsuarioPeticionamento();
		
		$objContatoDTO = $objMdPetIntAceiteRN->retornaObjContatoIdUsuario(array($objUsuarioPetDTO->getNumIdUsuario()));
		
		return $objContatoDTO;
	}
	
}
?>