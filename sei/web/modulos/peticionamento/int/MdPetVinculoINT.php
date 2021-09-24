<?
/**
* TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
*
* 02/04/2018 - criado por jose vieira
*
* Versão do Gerador de Código: 1.41.0
*/

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetVinculoINT extends InfraINT {

  public static function montarSelectIdMdPetVinculo($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado){
    $objMdPetVinculoDTO = new MdPetVinculoDTO();
    $objMdPetVinculoDTO->retNumIdMdPetVinculo();

    $objMdPetVinculoDTO->setOrdNumIdMdPetVinculo(InfraDTO::$TIPO_ORDENACAO_ASC);

    $objMdPetVinculoRN = new MdPetVinculoRN();
    $arrObjMdPetVinculoDTO = $objMdPetVinculoRN->listar($objMdPetVinculoDTO);

    return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrObjMdPetVinculoDTO, '', 'IdMdPetVinculo');
  }

  /**
   * Função responsável por montar os options do select "Hipótese Legal" para a tela de vinculacao
   * @return string
   */
  public static function montarSelectHipoteseLegal()
  {
    $objMdPetHipoteseLegalRN = new MdPetHipoteseLegalRN();
    $objMdPetHipoteseLegalDTO = new MdPetHipoteseLegalDTO();
    $objMdPetHipoteseLegalDTO->retNumIdHipoteseLegalPeticionamento();
    $objMdPetHipoteseLegalDTO->retStrNome();
    $objMdPetHipoteseLegalDTO->retStrBaseLegal();

    $arrObjHipoteseLegal = $objMdPetHipoteseLegalRN->listar($objMdPetHipoteseLegalDTO);

    $strOptions  = '<select id="selHipoteseLegal" class="infraSelect form-control" onchange="salvarValorHipoteseLegal(this)"
                        tabindex="'. PaginaSEIExterna::getInstance()->getProxTabDados() . '"><option value=""> </option>';

    if( is_array( $arrObjHipoteseLegal ) && count( $arrObjHipoteseLegal ) > 0){

      foreach ($arrObjHipoteseLegal as $objHipoteseLegalDTO) {
        $nomeBaseLegal = $objHipoteseLegalDTO->getStrNome() . ' (' . $objHipoteseLegalDTO->getStrBaseLegal() . ')';
        $strOptions .= '<option value="' . $objHipoteseLegalDTO->getNumIdHipoteseLegalPeticionamento() . '">';
        $strOptions .= $nomeBaseLegal;
        $strOptions .= '</option>';
      }
    }

    $strOptions .= '</select>';

    return $strOptions;
  }

  /**
   * Função responsável por montar os options do select "Tipo de Documento"
   * @param $strPrimeiroItemValor
   * @param $strPrimeiroItemDescricao
   * @param $strValorItemSelecionado
   * @return string
   * @since  11/05/2018
   * @author Jaqueline Mendes <jaqueline.mendes@castgroup.com.br>
   */
  public static function montarSelectTipoDocumento($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado)
  {
    $objMdPetVincRelSerieRN  = new MdPetVincRelSerieRN();
    $objMdPetVincRelSerieDTO = new MdPetVincRelSerieDTO();
    $objMdPetVincRelSerieDTO->retTodos(true);
    $objMdPetVincRelSerieDTO->setOrdStrNomeSerie(InfraDTO::$TIPO_ORDENACAO_ASC);
    $objMdPetVincRelSerieDTO->adicionarCriterio(array('StaAplicabilidadeSerie'),
        array(InfraDTO::$OPER_IN),
        array(array(SerieRN::$TA_INTERNO_EXTERNO, SerieRN::$TA_EXTERNO)));

    $arrObjMdPetVincRelSerieDTO = $objMdPetVincRelSerieRN->listar($objMdPetVincRelSerieDTO);

    $idsAdd = array();
    $arrRetornoDTO = array();

    //Remove a duplicação caso seja salvo o mesmo tipo de documento como obrigatório e não obrigatório.
    foreach($arrObjMdPetVincRelSerieDTO as $objMdPetVincRelSerieDTO){
     if(!in_array($objMdPetVincRelSerieDTO->getNumIdSerie(), $idsAdd)) {
       $idsAdd[] = $objMdPetVincRelSerieDTO->getNumIdSerie();
       array_push($arrRetornoDTO, $objMdPetVincRelSerieDTO);
     }
    }

    return parent::montarSelectArrInfraDTO($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arrRetornoDTO, 'IdSerie', 'NomeSerie');
  }

  /**
   * Função responsável por montar os options do select "Estado"
   * @param $strPrimeiroItemValor
   * @param $strPrimeiroItemDescricao
   * @param $strValorItemSelecionado
   * @return string
   * @since  11/05/2018
   * @author Jaqueline Mendes <jaqueline.mendes@castgroup.com.br>
   */
  public static function montarSelectStaEstado($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado){
    $arr = array(MdPetVincRepresentantRN::$RP_ATIVO => 'Ativa', MdPetVincRepresentantRN::$RP_SUSPENSO=>'Suspensa', MdPetVincRepresentantRN::$RP_REVOGADA =>'Revogada', MdPetVincRepresentantRN::$RP_RENUNCIADA =>'Renunciada', MdPetVincRepresentantRN::$RP_VENCIDA =>'Vencida',  MdPetVincRepresentantRN::$RP_SUBSTITUIDA =>'Substituída' );
    return InfraINT::montarSelectArray($strPrimeiroItemValor, $strPrimeiroItemDescricao, $strValorItemSelecionado, $arr);
  }

  public static function validarExistenciaVinculoCnpj($dados){


      $dadosCaptcha = hash('SHA512',$dados['txtCaptcha']);
      $strCaptcha   =  PaginaSEIExterna::getInstance()->recuperarCampo('captchaPeticionamentoRL');
      $cnpj = InfraUtil::retirarFormatacao($dados['txtNumeroCnpj']);
      $idUsuarioLogado = isset($dados['idUsuarioLogado']) ? $dados['idUsuarioLogado'] : SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();

      $xml = "";

      if($strCaptcha != $dadosCaptcha){
          $xml = "<dados-pj>";
          $xml .= "<success>false</success>\n";
          $xml .= "<procuracao>false</procuracao>\n";
          $xml .= "<msg>Código de confirmação inválido.</msg>\n";
          $xml .= "</dados-pj>";
          return $xml;
      }

      $contatoRN = new ContatoRN();
      $contatoDTO = new ContatoDTO();
      $contatoDTO->setDblCnpj($cnpj);
      $contatoDTO->retDblCnpj();
      $contatoDTO->retNumIdContato();
      $arrIdContato = InfraArray::converterArrInfraDTO($contatoRN->listarRN0325($contatoDTO), 'IdContato');
      $xml = "<dados-pj>";
      $xml .= "</dados-pj>";
      if(count($arrIdContato)>0) {
          $objMdPetVinculoDTO = new MdPetVinculoDTO();
          $objMdPetVinculoRN = new MdPetVinculoRN();
          $objMdPetVinculoDTO->retNumIdMdPetVinculo();
          $objMdPetVinculoDTO->retNumIdContato();
          $objMdPetVinculoDTO->setNumIdContato($arrIdContato, InfraDTO::$OPER_IN);
          $arrObjMdPetVinculoDTO = $objMdPetVinculoRN->listar($objMdPetVinculoDTO);

          if (count($arrObjMdPetVinculoDTO) > 0) {
              $xml = "<dados-pj>";
              $xml .= "<success>true</success>\n";
              $xml .= "<procuracao>false</procuracao>\n";
              $xml .= "<idVinculo>" . $arrObjMdPetVinculoDTO[0]->getNumIdMdPetVinculo() . "</idVinculo>\n";
              $xml .= "</dados-pj>";
              $idVinculo = $arrObjMdPetVinculoDTO[0]->getNumIdMdPetVinculo();
              // Representante Legal
              $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
              $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
              $objMdPetVincRepresentantDTO->retNumIdContato();
              $objMdPetVincRepresentantDTO->setNumIdMdPetVinculo($arrObjMdPetVinculoDTO[0]->getNumIdMdPetVinculo());
              $objMdPetVincRepresentantDTO->retStrTipoRepresentante();
              $objMdPetVincRepresentantDTO->setStrStaEstado(array(MdPetVincRepresentantRN::$RP_ATIVO), InfraDTO::$OPER_IN);
              $objMdPetVincRepresentantDTO->retStrCpfProcurador();
              $objMdPetVincRepresentantDTO->retDthDataLimite();
              $objMdPetVincRepresentantDTO->retNumIdMdPetVinculoRepresent();
              $arrObjMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);

              if (count($arrObjMdPetVincRepresentantDTO) > 0) {
                    foreach ($arrObjMdPetVincRepresentantDTO as $itemObjMdPetVincRepresentantDTO) {
                        $objUsuarioRN = new UsuarioRN();
                        $objUsuarioDTO = new UsuarioDTO();
                        $objUsuarioDTO->retNumIdUsuario();
                        $objUsuarioDTO->setNumIdContato($itemObjMdPetVincRepresentantDTO->getNumIdContato());

                        $arrObjUsuarioDTO = $objUsuarioRN->consultarRN0489($objUsuarioDTO);
                        if (count($arrObjUsuarioDTO) > 0) {
                            if ($idUsuarioLogado == $arrObjUsuarioDTO->getNumIdUsuario() && $itemObjMdPetVincRepresentantDTO->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL) {
                                $xml = "<dados-pj>";
                                $xml .= "<success>false</success>\n";
                                $xml .= "<procuracao>false</procuracao>\n";
                                $xml .= "<msg>Este CNPJ já está vinculado a um Responsável Legal.</msg>\n";
                                $xml .= "</dados-pj>";
                            }
                            
                            if(!is_null($itemObjMdPetVincRepresentantDTO->getDthDataLimite())){
                                $dataAtual = date("Y-m-d");
                                $dataLimite = explode(' ',$itemObjMdPetVincRepresentantDTO->getDthDataLimite());
                                $dataLimite = $dataLimite[0];
                                $anoLimite = substr($dataLimite, 6);
                                $mesLimite = substr($dataLimite, 3, -5);
                                $diaLimite = substr($dataLimite, 0, -8);
                                $dataLimite = $anoLimite . "-" . $mesLimite . "-" . $diaLimite;
                            }                    

                            if  ($idUsuarioLogado == $arrObjUsuarioDTO->getNumIdUsuario() &&
                                    ($itemObjMdPetVincRepresentantDTO->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL ||
                                        ($itemObjMdPetVincRepresentantDTO->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES && (!$dataLimite || ($dataLimite && strtotime($dataAtual) <= strtotime($dataLimite))))
                                    )
                                ) {
                                $xml = "<dados-pj>";
                                $xml .= "<success>false</success>\n";
                                $xml .= "<procuracao>true</procuracao>\n";
                                $xml .= "<url>" . base64_encode(SessaoSEIExterna::getInstance()->assinarLink('controlador_externo.php?acao=md_pet_vinc_usu_ext_negado&idVinculo=' . $idVinculo)) . "</url>\n";
                                $xml .= "</dados-pj>";
                            }elseif($idUsuarioLogado == $arrObjUsuarioDTO->getNumIdUsuario() && $itemObjMdPetVincRepresentantDTO->getStrTipoRepresentante() == MdPetVincRepresentantRN::$PE_PROCURADOR_SIMPLES && $dataLimite && strtotime($dataAtual) > strtotime($dataLimite)){
                                $dtoMdPetVincReptDTO = new MdPetVincRepresentantDTO();
                                $dtoMdPetVincReptDTO->setNumIdMdPetVinculoRepresent($itemObjMdPetVincRepresentantDTO->getNumIdMdPetVinculoRepresent());
                                $dtoMdPetVincReptDTO->setStrStaEstado(MdPetVincRepresentantRN::$RP_VENCIDA);
                                $rnMdPetVincRepRN = new MdPetVincRepresentantRN();
                                $arrObjMdPetVincRepresentantDTO = $rnMdPetVincRepRN->alterar($dtoMdPetVincReptDTO);
                            }
                        }
                    }
                }
            }
      }

      return $xml;

    }

}
