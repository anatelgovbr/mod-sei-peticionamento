<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 31/03/2017 - criado por Marcelo Bezerra - CAST
 * 26/08/2024 - Atualização por gabrielg.colab - SPASSU
 *
 * Versão do Gerador de Código: 1.40.0
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetIntAcessoExternoDocumentoDTO extends InfraDTO {

    public function montar()
    {
    	
    	$this->adicionarAtributo(InfraDTO::$PREFIXO_NUM, 'IdUsuarioExterno');
    	$this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'NomeUsuarioExterno');
    	$this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'EmailUsuarioExterno');
    	$this->adicionarAtributo(InfraDTO::$PREFIXO_NUM, 'IdUnidade');
    	$this->adicionarAtributo(InfraDTO::$PREFIXO_NUM, 'IdParticipante');
    	$this->adicionarAtributo(InfraDTO::$PREFIXO_DBL, 'IdProtocoloProcesso');
    	$this->adicionarAtributo(InfraDTO::$PREFIXO_ARR, 'IdDocumentos');
    	$this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'SinVisualizacaoIntegral');
		$this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'StaConcessao');
		$this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'Motivo');

    }
    
    public function getStrNomeTabela() {
    	return null;
    }
    
}
?>