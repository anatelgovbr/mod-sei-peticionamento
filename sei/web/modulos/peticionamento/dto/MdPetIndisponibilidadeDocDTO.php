<?
/**
 * ANATEL
 *
 * 12/12/2017 - criado por jaqueline.mendes - CAST
 * 26/08/2024 - Atualização por gabrielg.colab - SPASSU
 *
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIndisponibilidadeDocDTO extends InfraDTO {

  public function getStrNomeTabela() {
  	 return 'md_pet_indisp_doc';
  }

  public function montar() {

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
                                   'IdProtPeticionamento',
                                   'id_md_pet_indisp_doc');
    
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
    		'IdIndisponibilidade',
    		'id_md_pet_indisponibilidade');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
                                   'IdUnidade',
                                   'id_unidade');
    
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
    		'IdUsuario',
    		'id_usuario');

      $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DBL,
          'IdDocumento',
          'id_documento');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM,
        'IdAcessoExterno',
        'id_acesso_externo');


    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH,
                                   'Inclusao',
                                   'dth_inclusao');
    
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR,
                                   'SinAtivo',
                                   'sin_ativo');
    
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
         'SiglaUnidade',
         'sigla',
         'unidade');

      $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
          'DescricaoUnidade',
          'descricao',
          'unidade');
     
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
        'SiglaUsuario',
        'sigla',
        'usuario');

    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
          'SiglaUsuario',
          'sigla',
          'usuario');

      $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
          'Numero',
          'd.numero',
          'documento d');

    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 
        'ProtocoloFormatadoDocumento',
        'pd.protocolo_formatado',
        'protocolo pd');

    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM,
        'IdSerie',
        'd.id_serie',
        'documento d');

    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_DBL,
          'IdProtocoloDocumento',
          'd.id_documento',
          'documento d');

    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_DBL, 
        'IdProtocoloProcedimento',
        'd.id_procedimento',
        'documento d');

      //Serie
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,
           'NomeSerie',
           's.nome',
           'serie s');


    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'NomeDocFormatado');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'NomeUnidadeFormatada');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_DTA, 'InclusaoDta');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'UrlDocumento');

    $this->configurarFK('IdUnidade', 'unidade', 'id_unidade');
    $this->configurarFK('IdUsuario', 'usuario', 'id_usuario');
    $this->configurarFK('IdDocumento', 'documento d', 'd.id_documento', InfraDTO::$TIPO_FK_OPCIONAL);
    $this->configurarFK('IdAcessoExterno', 'acesso_externo ac', 'ac.id_acesso_externo', InfraDTO::$TIPO_FK_OPCIONAL);
    $this->configurarFK('IdProtocoloDocumento','protocolo pd','pd.id_protocolo');
    $this->configurarFK('IdProtocoloProcedimento', 'protocolo pp', 'pp.id_protocolo');
    $this->configurarFK('IdSerie', 'serie s', 's.id_serie');
    $this->configurarPK('IdProtPeticionamento', InfraDTO::$TIPO_PK_NATIVA);
  }
}
?>