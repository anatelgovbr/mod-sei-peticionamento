<?php

/**
 * Created by PhpStorm.
 * User: jhon.carvalho
 * Date: 27/12/2017
 * Time: 08:50
 * 26/08/2024 - Atualização por gabrielg.colab - SPASSU
 */
class MdPetVincRelSerieDTO extends InfraDTO
{

    public function getStrNomeTabela()
    {
        return 'md_pet_adm_vinc_rel_serie';
    }

    public function montar()
    {
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdMdPetVincRelSerie',
            'id_md_pet_adm_vinc_rel_ser');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdMdPetVincTpProcesso',
            'id_md_pet_adm_vinc_tp_proced');

        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
            'IdSerie',
            'id_serie');
        $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
            'SinObrigatorio',
            'sin_obrigatorio');

        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,'NomeSerie','serie.nome', 'serie serie' );
        $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,'StaAplicabilidadeSerie','serie.sta_aplicabilidade', 'serie serie');
        

        $this->configurarPK('IdMdPetVincRelSerie', InfraDTO::$TIPO_PK_NATIVA);
//        $this->configurarFK('IdMdPetVincTpProcesso', 'md_pet_adm_vinc_tp_proced vinc','vinc.id_md_pet_adm_vinc_tp_proced');
        $this->configurarFK('IdMdPetVincTpProcesso', 'md_pet_adm_vinc_tp_proced vinc','vinc.id_md_pet_adm_vinc_tp_proced');
        $this->configurarFK('IdSerie','serie serie','serie.id_serie');


    }
}