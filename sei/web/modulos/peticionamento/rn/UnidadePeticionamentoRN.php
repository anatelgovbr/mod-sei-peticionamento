<?
/**
 * ANATEL
 *
 * 26/07/2016 - criado por marcelo.bezerra@cast.com.br
 *
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class UnidadePeticionamentoRN extends UnidadeRN
{

	public function obterHierarquiaUnidade(UnidadeDTO $objUnidadeDTO){
		
		try {
			
			SessaoSEI::getInstance(false);				
			SessaoSEI::getInstance()->simularLogin(null, null, SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno() , $objUnidadeDTO->getNumIdUnidade() );
			
			//$objServicoDTO = $this->obterServico($SiglaSistema,$IdentificacaoServico);
			//$objUnidadeDTO = $this->obterUnidade($IdUnidade);
			//$this->validarAcessoAutorizado(explode(',',str_replace(' ','',$objServicoDTO->getStrServidor())));
			//SessaoSEI::getInstance()->simularLogin(null, null, $objServicoDTO->getNumIdUsuario(), $IdUnidade);
			
			$strHierarquiaUnidade = SessaoSEI::getInstance()->getAtributo('HIERARQUIA_'.$objUnidadeDTO->getStrSiglaOrgao().'_'.$objUnidadeDTO->getStrSigla());
			 
			if (InfraString::isBolVazia($strHierarquiaUnidade)){
				 
				$objInfraSip = new InfraSip(SessaoSEI::getInstance());
				$ret = $objInfraSip->carregarUnidades(SessaoSEI::getInstance()->getNumIdSistema());
	
				$arrUnidadesSip = array();
	
				foreach($ret as $uni){
					$numIdUnidade = $uni[InfraSip::$WS_UNIDADE_ID];
					$arrUnidadesSip[$numIdUnidade] = array();
					//$arrUnidadesSip[$numIdUnidade][self::$POS_UNIDADE_ORGAO_ID] = $uni[InfraSip::$WS_UNIDADE_ORGAO_ID];
					$arrUnidadesSip[$numIdUnidade][self::$POS_UNIDADE_SIGLA] = $uni[InfraSip::$WS_UNIDADE_SIGLA];
					//$arrUnidadesSip[$numIdUnidade][self::$POS_UNIDADE_DESCRICAO] = $uni[InfraSip::$WS_UNIDADE_DESCRICAO];
					//$arrUnidadesSip[$numIdUnidade][self::$POS_UNIDADE_SUBUNIDADES] = $uni[InfraSip::$WS_UNIDADE_SUBUNIDADES];
					$arrUnidadesSip[$numIdUnidade][self::$POS_UNIDADE_UNIDADES_SUPERIORES] = $uni[InfraSip::$WS_UNIDADE_UNIDADES_SUPERIORES];
				}
	
				if (isset($arrUnidadesSip[$objUnidadeDTO->getNumIdUnidade()])){
					 
					$arrUnidadesSuperiores = $arrUnidadesSip[$objUnidadeDTO->getNumIdUnidade()][self::$POS_UNIDADE_UNIDADES_SUPERIORES];
					$arrUnidadesSuperiores[] = $objUnidadeDTO->getNumIdUnidade();
					foreach($arrUnidadesSuperiores as $numIdUnidadeSuperior){
						if ($strHierarquiaUnidade!=''){
							$strHierarquiaUnidade .= '/';
						}
						$strHierarquiaUnidade .= $arrUnidadesSip[$numIdUnidadeSuperior][UnidadeRN::$POS_UNIDADE_SIGLA];
					}
	
					SessaoSEI::getInstance()->setAtributo('HIERARQUIA_'.$objUnidadeDTO->getStrSiglaOrgao().'_'.$objUnidadeDTO->getStrSigla(),$strHierarquiaUnidade);
				}
			}
	
			return $strHierarquiaUnidade;
	
		}catch(Exception $e){
			//var_dump($e->getTraceAsString());
			throw new InfraException('Erro obtendo hierarquia da unidade.',$e);
		}
	}


}
