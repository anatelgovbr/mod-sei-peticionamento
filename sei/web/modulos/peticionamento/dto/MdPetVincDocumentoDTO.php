<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 02/04/2018 - criado por jose vieira
* 26/08/2024 - Atualização por gabrielg.colab - SPASSU
*
* Versão do Gerador de Código: 1.41.0
*/

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetVincDocumentoDTO extends InfraDTO {

  public function getStrNomeTabela() {
    return 'md_pet_vinculo_documento';
  }

  public function montar() {

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetVincDocumento', 'id_md_pet_vinculo_documento');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DBL, 'IdProtocolo', 'id_documento');
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DBL, 'IdAnexo', 'id_documento');
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DBL, 'IdDocumento', 'id_documento');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'TipoDocumento', 'tipo_documento');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetVinculoRepresent','id_md_pet_vinculo_represent');
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'DataCadastro','data_cadastro');

    $this->configurarPK('IdMdPetVincDocumento', InfraDTO::$TIPO_PK_NATIVA);

    $this->configurarFK('IdProtocolo', 'protocolo p', 'p.id_protocolo');
    $this->configurarFK('IdAnexo', 'anexo a', 'a.id_protocolo');
    $this->configurarFK('IdDocumento', 'documento d', 'd.id_documento');
    $this->configurarFK('IdSerie', 'serie s', 's.id_serie');
    $this->configurarFK('IdMdPetVinculoRepresent','md_pet_vinculo_represent mvr','mvr.id_md_pet_vinculo_represent');
    $this->configurarFK('IdMdPetVinculo', 'md_pet_vinculo vinc', 'vinc.id_md_pet_vinculo');

    //Protocolo
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,'ProtocoloFormatadoProtocolo','p.protocolo_formatado','protocolo p');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM,'IdHipoteseLegal','p.id_hipotese_legal','protocolo p');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,'StaNivelAcesso','p.sta_nivel_acesso_local','protocolo p');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,'NumeroProtocolo','p.numero','protocolo p');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM,'IdSerie','d.id_serie','documento d');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,'NumeroDocumento','d.numero','documento d');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM,'IdTipoConferencia','d.id_tipo_conferencia','documento d');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM,'IdDocumento','p.id_protocolo','protocolo p');

    //Serie
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,'NomeSerieProtocolo','s.nome','serie s');

    //Anexo
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR,'NomeArquivoAnexo','a.nome','anexo a');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_DTH,'DataArquivoAnexo','a.dth_inclusao','anexo a');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM,'TamanhoArquivoAnexo','a.tamanho','anexo a');

    //Representante
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM,'IdMdPetVinculo','mvr.id_md_pet_vinculo','md_pet_vinculo_represent mvr');

    //Vínculação à Pessoa Jurídica
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_DBL,'IdProcedimentoVinculo', 'vinc.id_procedimento', 'md_pet_vinculo vinc');

  }
}
