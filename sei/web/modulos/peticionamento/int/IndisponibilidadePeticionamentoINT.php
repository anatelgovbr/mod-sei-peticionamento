<?
/**
* ANATEL
*
* 30/03/2016 - criado por jaqueline.mendes@cast.com.br - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class IndisponibilidadePeticionamentoINT extends InfraINT {

	public static function montarSelectProrrogacaoAutomaticaPrazos($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado){
		$objIndisponibilidadePeticionamentoRN = new IndisponibilidadePeticionamentoRN();
	
		$arrObjProrrogacaoAutomaticaPrazoDTO = $objIndisponibilidadePeticionamentoRN->listarValoresProrrogacao();
	
		return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjProrrogacaoAutomaticaPrazoDTO, 'SinProrrogacao', 'Descricao');
	
	}
	
	
	public static function formatarData($data, $fim){
		$dataArray = split(' ',$data);
		
		$data1 = split('/',$dataArray[0]); 
		$data2 = $data1[2] + '/' + $data1[1] + '/' + $data1[0];

		$date = new DateTime($data2);

		$hours = split(':', $dataArray[1]);
		
		$date->setTime($hours[0], $hours[1]);
		
		$value =  $date->format('d-m-Y - H:i');

		return $value;
	}

	
}