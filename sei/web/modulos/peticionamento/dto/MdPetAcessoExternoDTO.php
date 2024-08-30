<?
/**
* ANATEL
*
* 03/01/2018 - criado por jaqueline.mendes - CAST
* 26/08/2024 - Atualização por gabrielg.colab - SPASSU
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetAcessoExternoDTO extends InfraDTO  {

	public function getStrNomeTabela() {
		return 'md_pet_acesso_externo';
	}

	public function montar() {

		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'IdAcessoExterno',
				'id_acesso_externo');

		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'SinProcessoIntercorrente',
				'sin_proc_intercorrente');

		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'SinProcessoNovo',
				'sin_proc_novo');

		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'SinIntimacao',
				'sin_intimacao');

		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'SinAtivo',
				'sin_ativo');

		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'SinVinculo',
				'sin_vinculo');

		$this->configurarPK('IdAcessoExterno',InfraDTO::$TIPO_PK_INFORMADO);
		$this->configurarFK('IdAcessoExterno', 'acesso_externo a', 'a.id_acesso_externo');
		$this->configurarFK('IdAtividadeAcessoExterno', 'atividade atv', 'atv.id_atividade');
		$this->configurarFK('IdUnidadeAcessoExterno', 'unidade u', 'u.id_unidade');
		$this->configurarFK('IdProtocoloAcessoExterno', 'protocolo p', 'p.id_protocolo');
		$this->configurarFK('IdParticipanteAcessoExterno', 'participante pt', 'pt.id_participante');
		$this->configurarFK('IdContatoAcessoExterno', 'contato c', 'c.id_contato');

		//Atividade do Acesso Externo
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdAtividadeAcessoExterno','a.id_atividade','acesso_externo a');

		//Unidade da Atividade do Acesso Externo
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdUnidadeAcessoExterno','atv.id_unidade','atividade atv');

		//Protocolo do Acesso Externo
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdParticipanteAcessoExterno','a.id_participante','acesso_externo a');
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'SinAtivoAcessoExterno','a.sin_ativo','acesso_externo a');

		//Id Contato Participante
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdContatoAcessoExterno','pt.id_contato','participante pt');

		//Id Protocolo Acesso Externo
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_DBL, 'IdProtocoloAcessoExterno','atv.id_protocolo','atividade atv');
	}

}
?>