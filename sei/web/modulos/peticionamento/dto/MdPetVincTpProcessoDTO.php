<?php

/**
 * Created by PhpStorm.
 * User: jhon.carvalho
 * Date: 20/12/2017
 * Time: 11:00
 * 26/08/2024 - Atualização por gabrielg.colab - SPASSU
 */
require_once dirname(__FILE__).'/../../../SEI.php';
class MdPetVincTpProcessoDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_pet_adm_vinc_tp_proced';
    }

    public function montar()
    {
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdMdPetVincTpProcesso',
            'id_md_pet_adm_vinc_tp_proced');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdTipoProcedimento',
            'id_tipo_procedimento');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
            'Orientacoes',
            'orientacoes');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdUnidade',
            'id_unidade');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
            'SinNaUsuarioExterno',
            'sin_na_usuario_externo');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
            'SinNaPadrao',
            'sin_na_padrao');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
            'Especificacao',
            'especificacao');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
            'StaNivelAcesso',
            'sta_nivel_acesso');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdHipoteseLegal',
            'id_hipotese_legal');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
            'SinAtivo',
            'sin_ativo');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
            'TipoVinculo',
            'tipo_vinculo');
        
        // Nome processo
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR ,'NomeProcedimento', 'tipo.nome','tipo_procedimento tipo');

        // Nome Unidade
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR ,'SiglaUnidade', 'u.sigla','unidade u');
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR ,'DescricaoUnidade', 'u.descricao','unidade u');

        $this->configurarPK('IdMdPetVincTpProcesso', InfraDTO::$TIPO_PK_INFORMADO);

        $this->configurarFK('IdTipoProcedimento', 'tipo_procedimento tipo', 'tipo.id_tipo_procedimento', InfraDTO::$TIPO_FK_OPCIONAL);
        $this->configurarFK('IdUnidade', 'unidade u','u.id_unidade', InfraDTO::$TIPO_FK_OPCIONAL);
        $this->configurarFK('IdHipoteseLegal', 'hipotese_legal hl', 'hl.id_hipotese_legal');




        // TODO: Implement montar() method.
    }



}