<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
 *
 * 31/03/2017 - criado por Marcelo Bezerra - CAST
 *
 * Vers�o do Gerador de C�digo: 1.40.0
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