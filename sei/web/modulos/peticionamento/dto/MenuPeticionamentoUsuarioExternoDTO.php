<?
/**
* ANATEL
*
* 15/06/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MenuPeticionamentoUsuarioExternoDTO extends InfraDTO  {

	public function getStrNomeTabela() {
		return 'md_pet_usu_externo_menu';
	}

	public function montar() {
	
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'IdMenuPeticionamentoUsuarioExterno',
				'id_md_pet_usu_externo_menu');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'IdConjuntoEstilos',
				'id_conjunto_estilos');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'Nome',
				'nome');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'Url',
				'url');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'ConteudoHtml',
				'conteudo_html');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'Tipo',
				'tipo');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'SinAtivo',
				'sin_ativo');
		
		$this->configurarPK('IdMenuPeticionamentoUsuarioExterno', InfraDTO::$TIPO_PK_NATIVA);
	
	}}
?>