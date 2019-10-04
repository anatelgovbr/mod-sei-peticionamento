<?
/**
* ANATEL
*
* 14/04/2016 - criado por jaqueline.mendes - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetTipoProcessoDTO extends InfraDTO  {

	public function getStrNomeTabela() {
		return 'md_pet_tipo_processo';
	}
	
	public function montar() {
	
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'IdTipoProcessoPeticionamento',
				'id_md_pet_tipo_processo');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'IdProcedimento',
				'id_tipo_procedimento');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'IdSerie',
				'id_serie');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'IdHipoteseLegal',
				'id_hipotese_legal');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'StaNivelAcesso',
				'sta_nivel_acesso');
	
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'SinIIProprioUsuarioExterno',
				'sin_ii_proprio_usuario_externo');
	
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'SinIIIndicacaoDireta',
				'sin_ii_indicacao_direta');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'SinIIIndicacaoDiretaCpfCnpj',
				'sin_ii_indic_direta_cpf_cnpj');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'SinIIIndicacaoDiretaContato',
				'sin_ii_indic_direta_contato');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'SinNaUsuarioExterno',
				'sin_na_usuario_externo');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'SinNaPadrao',
				'sin_na_padrao');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'SinDocGerado',
				'sin_doc_gerado');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'SinDocExterno',
				'sin_doc_externo');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'SinAtivo',
				'sin_ativo');
				
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'Orientacoes',
				'orientacoes');
				
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeProcesso', 'tipo.nome', 'tipo_procedimento tipo');
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeHipoteseLegal', 'hl.nome', 'hipotese_legal hl');
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'BaseLegalHipoteseLegal', 'hl.base_legal', 'hipotese_legal hl');
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeSerie', 's.nome', 'serie s');
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdUnidade', 'md.id_unidade', 'md_pet_rel_tp_processo_unid md');
                $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdOrgaoUnidade', 'u.id_orgao', 'unidade u');
                $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'SiglaUnidade', 'u.sigla', 'unidade u');
                $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'SiglaOrgaoUnidade', 'o.sigla', 'orgao o');
                $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'StaTipoUnidade', 'md.sta_tp_unidade', 'md_pet_rel_tp_processo_unid md');

		$this->configurarPK('IdTipoProcessoPeticionamento',InfraDTO::$TIPO_PK_NATIVA);
		
		$this->configurarFK('IdProcedimento', 'tipo_procedimento tipo', 'tipo.id_tipo_procedimento');
		$this->configurarFK('IdSerie', 'serie s', 's.id_serie', InfraDTO::$TIPO_FK_OPCIONAL);
		$this->configurarFK('IdHipoteseLegal', 'hipotese_legal hl', 'hl.id_hipotese_legal', InfraDTO::$TIPO_FK_OPCIONAL);
		$this->configurarFK('IdTipoProcessoPeticionamento', 'md_pet_rel_tp_processo_unid md', 'md.id_md_pet_tipo_processo');
                $this->configurarFK('IdUnidade', 'unidade u', 'u.id_unidade');
                $this->configurarFK('IdOrgaoUnidade', 'orgao o', 'o.id_orgao');

		$this->adicionarAtributo(InfraDTO::$PREFIXO_ARR,'ObjRelTipoProcessoSerieDTO');
		
		$this->adicionarAtributo(InfraDTO::$PREFIXO_ARR,'ObjRelTipoProcessoSerieEssDTO');
                
                
	}}
?>