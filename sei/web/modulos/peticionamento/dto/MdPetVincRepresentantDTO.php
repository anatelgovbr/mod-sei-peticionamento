<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 02/04/2018 - criado por jose vieira
 *
 * Versão do Gerador de Código: 1.41.0
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetVincRepresentantDTO extends InfraDTO
{

  private $ProtocoloTIPOFK = null;

  public function __construct(){
    $this->ProtocoloTIPOFK = InfraDTO::$TIPO_FK_OPCIONAL;
    parent::__construct();
  }

  public function getStrNomeTabela()
  {
    return 'md_pet_vinculo_represent';
  }

  public function montar()
  {

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetVinculoRepresent', 'id_md_pet_vinculo_represent');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdMdPetVinculo', 'id_md_pet_vinculo');

    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'NomeTipoVinculação');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdContato', 'id_contato');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdContatoOutorg', 'id_contato_outorg');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'TipoRepresentante', 'tipo_representante');
    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'NomeTipoRepresentante');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinAtivo', 'sin_ativo');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'DataCadastro', 'data_cadastro');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'DataEncerramento', 'data_encerramento');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'StaEstado', 'sta_estado');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'Motivo', 'motivo');

    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdContatoProcurador', 'c.id_contato', 'contato c');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeProcurador', 'c.nome', 'contato c');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'CpfProcurador', 'c.cpf', 'contato c');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'Email', 'c.email', 'contato c');

    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdContatoVinc', 'vinc.id_contato','md_pet_vinculo vinc');
    //Juridico
      $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'IdxContato', 'contvinc.idx_contato','contato contvinc');

    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'RazaoSocialNomeVinc', 'contvinc.nome', 'contato contvinc');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdContatoVinc', 'contvinc.id_contato', 'contato contvinc');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'CNPJ', 'contvinc.cnpj', 'contato contvinc');

    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_DBL, 'IdProcedimentoVinculo', 'vinc.id_procedimento', 'md_pet_vinculo vinc');

    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_DBL, 'IdDocumento', 'vinc_doc.id_documento', 'md_pet_vinculo_documento vinc_doc');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'TipoDocumento', 'vinc_doc.tipo_documento', 'md_pet_vinculo_documento vinc_doc');


    // Constraint's
    $this->configurarPK('IdMdPetVinculoRepresent', InfraDTO::$TIPO_PK_NATIVA);

    $this->configurarFK('IdAcessoExterno', 'acesso_externo a', 'a.id_acesso_externo');

    $this->configurarFK('IdMdPetVinculo', 'md_pet_vinculo vinc', 'vinc.id_md_pet_vinculo');

    $this->configurarFK('IdContato', 'contato c', 'c.id_contato');
    $this->configurarFK('IdContatoOutorg', 'contato c2', 'c2.id_contato');
    $this->configurarFK('IdContatoVinc', 'contato contvinc', 'contvinc.id_contato');

    $this->configurarFK('IdMdPetVinculoRepresent','md_pet_vinculo_documento vinc_doc','vinc_doc.id_md_pet_vinculo_represent');

//    $this->configurarExclusaoLogica('SinAtivo', 'N');

  }

    public function getStrNomeTipoRepresentante($tipo_representante=null)
    {
        $retorno = '';
        $tipo_representante = is_null($tipo_representante) ? $this->getStrTipoRepresentante() : $tipo_representante;

        switch ($tipo_representante){
          case MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL :
            $retorno = 'Procurador Especial';
            break;
          case MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL :
            $retorno = 'Responsável Legal';
            break;
          case MdPetVincRepresentantRN::$PE_PROCURADOR :
            $retorno = 'Procurador';
            break;
          case MdPetVincRepresentantRN::$PE_PROCURADOR_SUBSTALECIDO :
            $retorno = 'Procurador Substalecido';
            break;
        }

    return $retorno;
  }

  public function getStrNomeTipoVinculacao($tipo_representante=null)
  {
    $retorno = '';
    $tipo_representante = is_null($tipo_representante) ? $this->getStrTipoRepresentante() : $tipo_representante;
    
    switch ($tipo_representante){
      case MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL :
          $retorno = 'Procuração Eletrônica Especial';
          break;
    }
    
    return $retorno;
  }

    public function getArrSerieSituacao($idSituacao = null)
    {
        if(!$idSituacao){
            $idSituacao = $this->getStrStaEstado();
        }
        $objInfraParametro = new InfraParametro(BancoSEI::getInstance());

        switch ($idSituacao){
            case MdPetVincRepresentantRN::$RP_ATIVO :
                $idSerieFormulario = $objInfraParametro->getValor(
                        MdPetAtualizadorSeiRN::$MD_PET_ID_SERIE_PROCURACAOE
                );

                $retorno = array('strSituacao'=>'Ativa', 'numSerie'=>$idSerieFormulario);
            break;
            case MdPetVincRepresentantRN::$RP_SUSPENSO :
                $idSerieFormulario = $objInfraParametro->getValor(
                    MdPetAtualizadorSeiRN::$MD_PET_ID_SERIE_VINC_SUSPENSAO
                );

                $retorno = array('strSituacao'=>'Suspensa', 'numSerie'=>$idSerieFormulario);
            break;
            case MdPetVincRepresentantRN::$RP_REVOGADA :
                $idSerieFormulario = $objInfraParametro->getValor(
                    MdPetAtualizadorSeiRN::$MD_PET_ID_SERIE_REVOGACAO
                );

                $retorno = array('strSituacao'=>'Revogada', 'numSerie'=>$idSerieFormulario);
            break;
            case MdPetVincRepresentantRN::$RP_RENUNCIADA :
                $idSerieFormulario = $objInfraParametro->getValor(
                    MdPetAtualizadorSeiRN::$MD_PET_ID_SERIE_RENUNCIA
                );

                $retorno = array('strSituacao'=>'Renunciada', 'numSerie'=>$idSerieFormulario);
            break;
            case MdPetVincRepresentantRN::$RP_VENCIDA :
                $idSerieFormulario = $objInfraParametro->getValor(
                    MdPetAtualizadorSeiRN::$MD_PET_ID_SERIE_RECIBO
                );

                $retorno = array('strSituacao'=>'Vencida', 'numSerie'=>$idSerieFormulario);
            break;case MdPetVincRepresentantRN::$RP_SUBSTITUIDA :
                $idSerieFormulario = $objInfraParametro->getValor(
                    MdPetAtualizadorSeiRN::$MD_PET_ID_SERIE_RECIBO
                );

                $retorno = array('strSituacao'=>'Substituída', 'numSerie'=>$idSerieFormulario);
            break;
        }

        return $retorno;
    }
}
