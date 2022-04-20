<?
/**
 * ANATEL
 *
 * 22/04/2016 - criado por Marcus Dionisio - ORLE
 *
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetWsUsuarioExternoDTO extends InfraDTO {

	public function __construct(){
		parent::__construct();
	}

	public function getStrNomeTabela() {
		return 'usuario';
	}

	public function montar() {
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,'IdUsuario','id_usuario');
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,'Sigla','sigla');		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,'Nome','nome');
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,'SinAtivo','sin_ativo');		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,'StaTipo','sta_tipo');
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,'IdContato','id_contato');

		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_DBL,'RgContato','rg','contato');
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,'OrgaoExpedidorContato','orgao_expedidor','contato');
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,'Cpf','cpf','contato');
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,'TelefoneFixo','telefone_fixo','contato');
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,'TelefoneCelular','telefone_celular','contato');
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,'EnderecoContato','endereco','contato');
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,'BairroContato','bairro','contato');
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,'NomeCidadeContato','nome_cidade','contato');
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,'CepContato','cep','contato');		
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_DTH,'DataCadastroContato','dth_cadastro','contato');

		$this->configurarPK('IdUsuario',InfraDTO::$TIPO_PK_INFORMADO);
		$this->configurarFK('IdContato','contato','id_contato');

    }
}
?>