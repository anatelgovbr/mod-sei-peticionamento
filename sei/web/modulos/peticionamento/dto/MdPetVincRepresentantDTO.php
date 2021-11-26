<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4� REGI�O
 *
 * 02/04/2018 - criado por jose vieira
 *
 * Vers�o do Gerador de C�digo: 1.41.0
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

    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'NomeTipoVincula��o');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdContato', 'id_contato');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_NUM, 'IdContatoOutorg', 'id_contato_outorg');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'TipoRepresentante', 'tipo_representante');

    $this->adicionarAtributo(InfraDTO::$PREFIXO_STR, 'NomeTipoRepresentante');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'SinAtivo', 'sin_ativo');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'DataCadastro', 'data_cadastro');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'DataEncerramento', 'data_encerramento');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'StaEstado', 'sta_estado');

    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'Motivo', 'motivo');
    //Novas Colunas
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_STR, 'StaAbrangencia', 'sta_abrangencia');
    $this->adicionarAtributoTabela(InfraDTO::$PREFIXO_DTH, 'DataLimite', 'data_limite');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdMdPetRelPoder', 'relpoder.id_md_pet_tipo_poder', 'md_pet_rel_vincrep_tipo_poder relpoder');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdMdPetRelProtocolo', 'relproc.id_protocolo', 'md_pet_rel_vincrep_protoc relproc');



    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdContatoProcurador', 'c.id_contato', 'contato c');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'NomeProcurador', 'c.nome', 'contato c');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'CpfProcurador', 'c.cpf', 'contato c');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'Email', 'c.email', 'contato c');

    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdContatoVinc', 'vinc.id_contato','md_pet_vinculo vinc');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'TpVinc', 'vinc.tp_vinculo','md_pet_vinculo vinc');
    //Juridico
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'IdxContato', 'contvinc.idx_contato','contato contvinc');

    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'RazaoSocialNomeVinc', 'contvinc.nome', 'contato contvinc');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_NUM, 'IdContatoVinc', 'contvinc.id_contato', 'contato contvinc');
    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'CNPJ', 'contvinc.cnpj', 'contato contvinc');

    $this->adicionarAtributoTabelaRelacionada(InfraDTO::$PREFIXO_STR, 'CPF', 'contvinc.cpf', 'contato contvinc');

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

    //Constraint de Poder Legal
    $this->configurarFK('IdMdPetVinculoRepresent','md_pet_rel_vincrep_tipo_poder relpoder','relpoder.id_md_pet_vinculo_represent');
    //Constraint de Poder Legal - Fim
    //Constraint de Processos 
    $this->configurarFK('IdMdPetVinculoRepresent','md_pet_rel_vincrep_protoc relproc','relproc.id_md_pet_vinculo_represent');
    //Constraint de Processos - Fim

    $this->configurarFK('IdMdPetVinculoRepresent','md_pet_vinculo_documento vinc_doc','vinc_doc.id_md_pet_vinculo_represent');

//    $this->configurarExclusaoLogica('SinAtivo', 'N');

  }
  public function getStrNomeOutorgado($nome = null){
      
      $objContatoDTO = new ContatoDTO();
      $objContatoDTO->retStrNome();
      $objContatoDTO->setNumIdContato($this->getNumIdContato());
      $objContatoRN = new ContatoRN();
      $arrObjContatoRN =  $objContatoRN->consultarRN0324($objContatoDTO);
      
      return $arrObjContatoRN->getStrNome();
  }

  public function getStrTipoPoderes(){
      $retorno = "";
    if($this->getStrTipoRepresentante() == "E"){
      $retorno = "Todos os Poderes Legais";
    }else{

        $objMdPetRelVincRepTpPoderDTO = new MdPetRelVincRepTpPoderDTO();
        $objMdPetRelVincRepTpPoderDTO->retNumIdTipoPoderLegal();
        $objMdPetRelVincRepTpPoderDTO->setNumIdVinculoRepresent($this->getNumIdMdPetVinculoRepresent());
        $objMdPetRelVincRepTpPoderRN = new MdPetRelVincRepTpPoderRN();
        $tpPoderes = InfraArray::converterArrInfraDTO($objMdPetRelVincRepTpPoderRN->listar($objMdPetRelVincRepTpPoderDTO),'IdTipoPoderLegal');
        
        $objMdPetTipoPoderDTO = new MdPetTipoPoderLegalDTO();
        $objMdPetTipoPoderDTO->setNumIdTipoPoderLegal($tpPoderes,infraDTO::$OPER_IN);
        $objMdPetTipoPoderDTO->retStrNome();
        $objMdPetTipoPoderRN = new MdPetTipoPoderLegalRN();
        $tpPoderes = InfraArray::converterArrInfraDTO($objMdPetTipoPoderRN->listar($objMdPetTipoPoderDTO),'Nome');
        $retorno = implode(',',$tpPoderes);
    }
    
    return $retorno;
}


    public function getStrNomeTipoRepresentante($tipo_representante=null)
    {
        $retorno = '';
        $tipo_representante = is_null($tipo_representante) ? $this->getStrTipoRepresentante() : $tipo_representante;

        switch ($tipo_representante){
          case MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL :
            $retorno = 'Procura��o Eletr�nica Especial';
            break;
          case MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL :
            $retorno = 'Respons�vel Legal';
            break;
          case MdPetVincRepresentantRN::$PE_PROCURADOR :
            $retorno = 'Procurador';
            break;
          case MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES :
            $retorno = 'Procura��o Eletr�nica Simples';
            break;
        }

    return $retorno;
  }

  public function getStrStaAbrangenciaTipo($sta_abrangencia=null)
    {
        
        $sta_abrangencia =  $this->getStrStaAbrangencia();

        switch ($sta_abrangencia){
          case MdPetVincRepresentantRN::$PR_ESPECIFICO :
            $retorno = 'Processos Espec�ficos';
            break;
          case MdPetVincRepresentantRN::$PR_QUALQUER :
            $retorno = 'Qualquer Processo em Nome do Outorgante';
            break;
        case null :
            $retorno = 'Qualquer Processo em Nome do Outorgante';
            break;
        }

    return $retorno;
  }

  public function getDthDataLimiteValidade($data=null)
    {
        
        $data =  $this->getDthDataLimite();
        $dataFormatada = explode(" ",$data);
        if($data == null){
            $retorno = "Indeterminado";
        }else{
            
            $retorno = "Determinado (Data Lim�te:".$dataFormatada[0].")";
        }
        

    return $retorno;
  }

  public function getStrNomeTipoVinculacao($tipo_representante=null)
  {
    $retorno = '';
    $tipo_representante = is_null($tipo_representante) ? $this->getStrTipoRepresentante() : $tipo_representante;
    
    switch ($tipo_representante){
      case MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL :
          $retorno = 'Procura��o Eletr�nica Especial';
          break;
    }
    
    return $retorno;
  }

  public function getStrStaEstadoTipo($estado = null){
      $estado = "";
//    if($this->getStrStaEstado() == MdPetVincRepresentantRN::$RP_ATIVO){
//        if(infraData::compararDatas(infraData::getStrDataAtual(),$this->getDthDataLimite()) < 0){
//        $estado = "Vencida";
//        }else{
//        $estado = "Ativa";
//        }
//    }
    
    if($this->getStrStaEstado() == MdPetVincRepresentantRN::$RP_SUSPENSO){
        $estado = "Suspensa";
    }
    if($this->getStrStaEstado() == MdPetVincRepresentantRN::$RP_REVOGADA){
        $estado = "Revogada";
    }
    if($this->getStrStaEstado() == MdPetVincRepresentantRN::$RP_RENUNCIADA){
        $estado = "Renunciada";
    }
    if($this->getStrStaEstado() == MdPetVincRepresentantRN::$RP_VENCIDA){
        $estado = "Vencida";
    }
    if($this->getStrStaEstado() == MdPetVincRepresentantRN::$RP_SUBSTITUIDA){
        $estado = "Substitu�da";
    }

    return $estado;
  }

    public function getArrSerieSituacao($idSituacao = null,$tipoProcuracao = null)
    {
        if(!$idSituacao){
            $idSituacao = $this->getStrStaEstado();
        }
        $objInfraParametro = new InfraParametro(BancoSEI::getInstance());

        switch ($idSituacao){
            case MdPetVincRepresentantRN::$RP_ATIVO :
            
            if($tipoProcuracao != null){
            if($tipoProcuracao == MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL ){
                $idSerieFormulario = $objInfraParametro->getValor(
                    MdPetIntSerieRN::$MD_PET_ID_SERIE_PROCURACAOE
                );
                
            }else if($tipoProcuracao == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES){
                $idSerieFormulario = $objInfraParametro->getValor(
                    MdPetIntSerieRN::$MD_PET_ID_SERIE_PROCURACAOS
                );
               
            }
            }else{
                        $idSerieFormulario = $objInfraParametro->getValor(
                            MdPetIntSerieRN::$MD_PET_ID_SERIE_PROCURACAOE
                    );
            }
                $retorno = array('strSituacao'=>'Ativa', 'numSerie'=>$idSerieFormulario);
            break;
            case MdPetVincRepresentantRN::$RP_SUSPENSO :
                $idSerieFormulario = $objInfraParametro->getValor(
                    MdPetIntSerieRN::$MD_PET_ID_SERIE_VINC_SUSPENSAO
                );

                $retorno = array('strSituacao'=>'Suspensa', 'numSerie'=>$idSerieFormulario);
            break;
            case MdPetVincRepresentantRN::$RP_REVOGADA :
                $idSerieFormulario = $objInfraParametro->getValor(
                    MdPetIntSerieRN::$MD_PET_ID_SERIE_REVOGACAO
                );

                $retorno = array('strSituacao'=>'Revogada', 'numSerie'=>$idSerieFormulario);
            break;
            case MdPetVincRepresentantRN::$RP_RENUNCIADA :
                $idSerieFormulario = $objInfraParametro->getValor(
                    MdPetIntSerieRN::$MD_PET_ID_SERIE_RENUNCIA
                );

                $retorno = array('strSituacao'=>'Renunciada', 'numSerie'=>$idSerieFormulario);
            break;
            case MdPetVincRepresentantRN::$RP_VENCIDA :
                $idSerieFormulario = $objInfraParametro->getValor(
                    MdPetIntSerieRN::$MD_PET_ID_SERIE_RECIBO
                );

                $retorno = array('strSituacao'=>'Vencida', 'numSerie'=>$idSerieFormulario);
            break;case MdPetVincRepresentantRN::$RP_SUBSTITUIDA :
                $idSerieFormulario = $objInfraParametro->getValor(
                    MdPetIntSerieRN::$MD_PET_ID_SERIE_RECIBO
                );

                $retorno = array('strSituacao'=>'Substitu�da', 'numSerie'=>$idSerieFormulario);
            break;
        }

        return $retorno;
    }
}
