<?
/**
* ANATEL
*
* 21/10/2016 - criado por marcelo.bezerra - CAST
* 26/08/2024 - Atualização por gabrielg.colab - SPASSU
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetCriterioDTO extends InfraDTO  {

	public function getStrNomeTabela() {
		return 'md_pet_criterio';
	}
	
	public function montar() {
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'IdCriterioIntercorrentePeticionamento',
				'id_md_pet_criterio');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'IdHipoteseLegal',
				'id_hipotese_legal');

		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
				'IdTipoProcedimento',
				'id_tipo_procedimento');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'StaNivelAcesso',
				'sta_nivel_acesso');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'StaTipoNivelAcesso',
				'sta_tipo_nivel_acesso');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'SinAtivo',
				'sin_ativo');
		
		$this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
				'SinCriterioPadrao',
				'sin_criterio_padrao');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
            'SinIntercorrenteSigiloso',
            'sin_intercorrente_sigiloso');


        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeProcesso', 'tipo_proc.nome', 'tipo_procedimento tipo_proc');
        
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'TipoProcessoSinAtivo', 'tipo_proc.sin_ativo', 'tipo_procedimento tipo_proc');
        
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeHipoteseLegal', 'hl.nome', 'hipotese_legal hl');
		$this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'BaseLegalHipoteseLegal', 'hl.base_legal', 'hipotese_legal hl');
		
		$this->configurarPK('IdCriterioIntercorrentePeticionamento',InfraDTO::$TIPO_PK_NATIVA);
		
		$this->configurarFK('IdHipoteseLegal', 'hipotese_legal hip', 'hip.id_hipotese_legal');
		
		$this->configurarFK('IdTipoProcedimento', 'tipo_procedimento tipo_proc', 'tipo_proc.id_tipo_procedimento');
        $this->configurarFK('IdHipoteseLegal', 'hipotese_legal hl', 'hl.id_hipotese_legal', InfraDTO::$TIPO_FK_OPCIONAL);

        $this->adicionarAtributo(InfraDTO::$PREFIXO_ARR,'IdsUnidadesLiberadas');
	}

}
?>