<?
/**
* ANATEL
*
* 06/12/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetContatoRN extends InfraRN {
	
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
	
	
}
?>