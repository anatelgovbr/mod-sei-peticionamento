<?
/**
* ANATEL
*
* 12/04/2016 - criado por jaqueline.mendes - CAST
* 26/08/2024 - Atualização por gabrielg.colab - SPASSU
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIndisponibilidadeDTO extends InfraDTO  {

	public function getStrNomeTabela() {
		return 'md_pet_indisponibilidade';
	}
	
	
	public function getDthDataInicioFormatada(){
		
		$dateInicio = "";
		
		if( $this->isSetAtributo('DataInicio') ){
		  
		  $dateInicio = $this->getDthDataInicio();
		  $tamanhoSubstr = strlen("01/04/2016 00:00:00");
		  $tamanhoEncontrado = strlen($dateInicio);
		
		  if( $tamanhoSubstr == $tamanhoEncontrado){
		    $dateInicio = substr($dateInicio, 0, -3);
		  }
		  
		}
		
		return $dateInicio;
		
	}
	
	public function getDthDataFimFormatada(){
	    
		$dateFim = $this->getDthDataFim();
		$tamanhoSubstr = strlen("01/04/2016 00:00:00");
		$tamanhoEncontrado = strlen($dateFim);
		
		if( $tamanhoSubstr == $tamanhoEncontrado){
		  $dateFim = substr($dateFim, 0, -3);
		}
		

		return $dateFim;
	}
	
	public function montar() {
	
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'IdIndisponibilidade',
				'id_md_pet_indisponibilidade');
	
	
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH,
				'DataInicio',
				'dth_inicio');
	
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH,
				'DataFim',
				'dth_fim');
		
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'SinAtivo',
				'sin_ativo');
	
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'ResumoIndisponibilidade',
				'resumo_indisponibilidade');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'SinProrrogacao',
				'sin_prorrogacao');
		
		$this->adicionarAtributo(InfraDTO::$PREFIXO_ARR, 'ObjAnexoDTO');
		$this->configurarPK('IdIndisponibilidade',InfraDTO::$TIPO_PK_NATIVA);
	
	}}
?>