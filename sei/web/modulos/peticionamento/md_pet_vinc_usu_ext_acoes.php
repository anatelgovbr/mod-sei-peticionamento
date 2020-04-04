<?php
switch ($_GET['acao']) {

  case 'md_pet_vinc_usu_ext_cadastrar':
    $stAlterar = false;
    $strTitulo = "Novo Responsável Legal de Pessoa Jurídica";
    $strTipo = 'Cadastro';
    $strItensSelTipoInteressado = MdPetTpCtxContatoINT::montarSelectTipoInteressado($strPrimeiroItemValor, $strPrimeiroItemDescricao, $numIdTipoContextoContato, 'Cadastro');

    $arrComandos[] = '<button type="button" accesskey="P" name="btnResponder"  onclick = "peticionar()" class="infraButton"><span class="infraTeclaAtalho">P</span>eticionar</button>';
    $arrComandos[] = '<button type="button" accesskey="C" id="btnFechar" class="infraButton" onclick="fechar()"><span class="infraTeclaAtalho">C</span>ancelar</button>';

    $objMdPetVincRelSerieRN = new MdPetVincRelSerieRN();
    $strDocsObrigatorios = $objMdPetVincRelSerieRN->buscarDocumentosObrigatorios();
    $slUf = UfINT::montarSelectSiglaRI0416('NULL', '', 'NULL');

    $objUsuarioDTO = new UsuarioDTO();
    $objUsuarioDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
    $objUsuarioDTO->retNumIdContato();
    $objUsuarioDTO = (new UsuarioRN)->consultarRN0489($objUsuarioDTO);

    $objContatoDTO = new ContatoDTO();
    $objContatoDTO->setNumIdContato($objUsuarioDTO->getNumIdContato());
    $objContatoDTO->retDblCpf();
    $objContatoDTO = (new ContatoRN)->consultarRN0324($objContatoDTO);


    $strItensSelCidade = CidadeINT::montarSelectIdCidadeNome('null','&nbsp;', null , null);
    break;
  
  case 'md_pet_vinc_usu_ext_consultar' :
  case 'md_pet_vinc_usu_ext_alterar' :

    if ($_GET['acao']=='md_pet_vinc_usu_ext_consultar'){
      $strTitulo = "Consultar Cadastro da Pessoa Jurídica";
      $stConsultar = true;
    }else{
      $strTitulo = "Atualizar Atos Constitutivos da Pessoa Jurídica";
      $stAlterar = true;
      $strTipo = 'Alteração';
    }

    $idMdPetVinculo = $_GET['id_vinculo'];

    //Recuperar dados para Pessoa Juridica.
    $objMdPetVinculoRN = new MdPetVinculoRN();
    $objMdPetVinculoDTO = new MdPetVinculoDTO();
    $objMdPetVinculoDTO->retNumIdMdPetVinculo();
    $objMdPetVinculoDTO->retDblCNPJ();
    $objMdPetVinculoDTO->retStrRazaoSocialNomeVinc();
    $objMdPetVinculoDTO->retStrCpfContatoRepresentante();
    $objMdPetVinculoDTO->retStrNomeContatoRepresentante();
    $objMdPetVinculoDTO->retStrSinValidado();
    $objMdPetVinculoDTO->retStrSinWebService();
    $objMdPetVinculoDTO->retNumIdContato();
    $objMdPetVinculoDTO->retNumIdContatoRepresentante();
    $objMdPetVinculoDTO->setNumIdMdPetVinculo($idMdPetVinculo);
    $objMdPetVinculoDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_RESPONSAVEL_LEGAL);
    $objMdPetVinculoDTO->setStrStaResponsavelLegal('S');
    $objMdPetVinculoDTO->setDistinct(true);
    $arrDadosPessoaJuridicaVinculo = $objMdPetVinculoRN->consultar($objMdPetVinculoDTO);

    $numIdUsuarioLogado = SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno();
    $usuarioRN = new UsuarioRN();
    $usuarioDTO = new UsuarioDTO();
    $usuarioDTO->retNumIdContato();
    $usuarioDTO->setNumIdUsuario($numIdUsuarioLogado);
    $arrContato = $usuarioRN->consultarRN0489($usuarioDTO);

    //Verifica se quem acessa é o Responsável Legal
    if($arrDadosPessoaJuridicaVinculo->getNumIdContatoRepresentante() != $arrContato->getNumIdContato()){
      $stConsultar = true;
    }

    $stGravadoWebService =  $objMdPetVinculoDTO->retStrSinWebService();

    //Dados do contato Representante
    $idContatoRepresentante =    $arrDadosPessoaJuridicaVinculo->getNumIdContato();
    $contatoRN = new ContatoRN();
    $contatoDTO = new ContatoDTO();
    $contatoDTO->retNumIdTipoContato();
    $contatoDTO->retStrSiglaUf();
    $contatoDTO->retStrComplemento();
    $contatoDTO->retStrCep();
    $contatoDTO->retStrEndereco();
    $contatoDTO->retStrBairro();
    $contatoDTO->retNumIdUf();
    $contatoDTO->retNumIdCidade();
    $contatoDTO->retNumIdPais();
    $contatoDTO->retNumIdTipoContato();

    $contatoDTO->setNumIdContato($idContatoRepresentante);
    $arrContatoDTO = $contatoRN->consultarRN0324($contatoDTO);

    if(is_null($arrContatoDTO)){
        $contatoDTO->setStrSinAtivo('S');
        $contatoRN->reativarRN0452(array($contatoDTO));
        $arrContatoDTO = $contatoRN->consultarRN0324($contatoDTO);
    }

    $cepObj = $arrContatoDTO->getStrCep();
    $cep = strpos($cepObj, '-') ? $cepObj :  substr($cepObj, 0, 5) . '-' . substr($cepObj, 5, 3);
    $arrContatoDTO->setStrCep($cep);

    $slUf = UfINT::montarSelectSiglaRI0416(null,  $arrContatoDTO->getNumIdUf(), $arrContatoDTO->getNumIdUf());

    //Montar Select Cidade
    $idUf              = $arrContatoDTO ? $arrContatoDTO->getNumIdUf() : null;
    $idCidade          = $arrContatoDTO ? $arrContatoDTO->getNumIdCidade() : null;
    $idPais            = $arrContatoDTO ? $arrContatoDTO->getNumIdPais() : null;
    $strItensSelCidade = CidadeINT::montarSelectIdCidadeNome(null, $idCidade,$idCidade, $idUf , $idPais);

    //Montar Select Tipo de Interessado
    $numIdTipoContextoContato   = $arrContatoDTO->getNumIdTipoContato();
    $strItensSelTipoInteressado = MdPetTpCtxContatoINT::montarSelectTipoInteressado($strPrimeiroItemValor, $strPrimeiroItemDescricao, $numIdTipoContextoContato, 'Cadastro');

    //Arquivos imputados
    $objMdPetVincDocumentoRN = new MdPetVincDocumentoRN();
    $objMdPetVincDocumentoDTO = new MdPetVincDocumentoDTO();
    $objMdPetVincDocumentoDTO->retDblIdDocumento();
    $objMdPetVincDocumentoDTO->retStrNomeArquivoAnexo();
    $objMdPetVincDocumentoDTO->retNumIdSerie();
    $objMdPetVincDocumentoDTO->retStrNomeSerieProtocolo();
    $objMdPetVincDocumentoDTO->retNumIdHipoteseLegal();
    $objMdPetVincDocumentoDTO->retDthDataArquivoAnexo();
    $objMdPetVincDocumentoDTO->retStrStaNivelAcesso();
    $objMdPetVincDocumentoDTO->retStrNumeroDocumento();
    $objMdPetVincDocumentoDTO->retNumIdTipoConferencia();
    $objMdPetVincDocumentoDTO->retNumTamanhoArquivoAnexo();
    //$objMdPetVincDocumentoDTO->setNumIdMdPetVinculacaoPj($idMdPetVinculacaoPj);
    $objMdPetVincDocumentoDTO->setStrTipoDocumento(MdPetVincDocumentoRN::$TP_PROTOCOLO_ATOS);

    $arrArquivo = $objMdPetVincDocumentoRN->listar($objMdPetVincDocumentoDTO);

    //Procurações
    $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();
    $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
    $objMdPetVincRepresentantDTO->retStrNomeProcurador();
    $objMdPetVincRepresentantDTO->retStrCpfProcurador();
    $objMdPetVincRepresentantDTO->retStrEmail();
    $objMdPetVincRepresentantDTO->retNumIdContato();
    $objMdPetVincRepresentantDTO->setNumIdMdPetVinculo($iIdMdPetVinculo);
    $objMdPetVincRepresentantDTO->setStrTipoRepresentante(MdPetVincRepresentantRN::$PE_PROCURADOR_ESPECIAL);

    $arrRepresentante = $objMdPetVincRepresentantRN->listar($objMdPetVincRepresentantDTO);

    //Separando número e endereço
    $strEnderecoCompleto = !is_null($arrContatoDTO) ? $arrContatoDTO->getStrEndereco() : null;
    $arrEndereco         = !is_null($strEnderecoCompleto) ? explode(',', $strEnderecoCompleto) : null;
    $intUltimaPosition   = count($arrEndereco) > 1 ? count($arrEndereco) - 1 : null;
    $strVlultimaPosition = !is_null($intUltimaPosition) ? trim($arrEndereco[$intUltimaPosition]) : null;

    $strEndereco = '';
        /*if(!is_null($intUltimaPosition)) {
            for ($i = 0; $i < $intUltimaPosition; $i++) {
                $strEndereco .= $arrEndereco[$i];
                if ($intUltimaPosition != ($i + 1)) {
                    $strEndereco .= ',';
                }
            }

      $strNumero = !is_null($strVlultimaPosition) ? $strVlultimaPosition : '';
    }else{*/
      $strEndereco = $strEnderecoCompleto;
   // }

    $arrDescricaoNivelAcesso = ['0' => 'Público', '1' => 'Restrito', '2' => 'Sigiloso'];
    if(!$stConsultar){
    $arrComandos[] = '<button type="button" accesskey="P" name="btnResponder"  onclick = "peticionar()" class="infraButton"><span class="infraTeclaAtalho">P</span>eticionar</button>';
    $arrComandos[] = '<button type="button" accesskey="C" id="btnFechar" class="infraButton" onclick="fechar()"><span class="infraTeclaAtalho">C</span>ancelar</button>';

    }else {
        $arrComandos[] = '<button type="button" accesskey="C" id="btnFechar" class="infraButton" onclick="fechar()">Fe<span class="infraTeclaAtalho">c</span>har</button>';
    }
    break;

  default:
    throw new InfraException("Ação '" . $_GET['acao'] . "' não reconhecida.");

}


$mdPetIntegracaoRN = new MdPetIntegracaoRN();
$mdPetIntegracaoDTO = new MdPetIntegracaoDTO();
$mdPetIntegracaoDTO->retNumIdMdPetIntegracao();
$mdPetIntegracaoDTO->retStrStaUtilizarWs();
$mdPetIntegracaoDTO->setNumIdMdPetIntegFuncionalid(MdPetIntegFuncionalidRN::$ID_FUNCIONALIDADE_CNPJ_RECEITA_FEDERAL);

$qtdFuncionalidadeCadastrada = $mdPetIntegracaoRN->contar($mdPetIntegracaoDTO);
$mdPetIntegracaoDTO = $mdPetIntegracaoRN->consultar($mdPetIntegracaoDTO);
/**
 * Verifica a existencia do Webservice
 */
$stWebService = false;
if ($qtdFuncionalidadeCadastrada > 0) {
    if($mdPetIntegracaoDTO->getStrStaUtilizarWs() == 'S'){
        $stWebService = true;
    }
}

///////////////////////////////
$objMdPetVincTpProcessoDTO = new MdPetVincTpProcessoDTO();
$objMdPetVincTpProcessoRN = new MdPetVincTpProcessoRN();

$objMdPetVincTpProcessoDTO->retNumIdTipoProcedimento();
$objMdPetVincTpProcessoDTO->retStrSinNaUsuarioExterno();
$objMdPetVincTpProcessoDTO->retStrSinNaPadrao();
$objMdPetVincTpProcessoDTO->setStrTipoVinculo('J');
$objMdPetVincTpProcessoDTO->retStrStaNivelAcesso();
$objMdPetVincTpProcessoDTO->retNumIdHipoteseLegal();
$objMdPetVincTpProcessoDTO->retStrOrientacoes();
$objMdPetVincUsuExtPj = $objMdPetVincTpProcessoRN->consultar($objMdPetVincTpProcessoDTO);

$verificaNivelAcessoTipoProcesso = $objMdPetVincUsuExtPj->getStrSinNaUsuarioExterno();
$verificaNivelAcessoPadrao = $objMdPetVincUsuExtPj->getStrSinNaPadrao();

//Tipo de Processo para vinculo
$idTipoProcesso = $objMdPetVincUsuExtPj->getNumIdTipoProcedimento();

//Usuário Externo indica diretamente
$arrDescricaoNivelAcesso = ['0' => 'Público', '1' => 'Restrito', '2' => 'Sigiloso'];

$objMdPetIntDestRespostaRN = new MdPetIntDestRespostaRN();
$exibirHipoteseLegal = $objMdPetIntDestRespostaRN->verificarHipoteseLegal();

if ($verificaNivelAcessoTipoProcesso == 'S') {
  $objNivelAcessoPermitidoRN = new NivelAcessoPermitidoRN();
  $objNivelAcessoPermitidoDTO = new NivelAcessoPermitidoDTO();
  $objNivelAcessoPermitidoDTO->retStrStaNivelAcesso();
  $objNivelAcessoPermitidoDTO->setNumIdTipoProcedimento($idTipoProcesso);

  $arrObjNivelAcesso = $objNivelAcessoPermitidoRN->listar($objNivelAcessoPermitidoDTO);

  $strSelectNivelAcesso = [];
  foreach ($arrObjNivelAcesso as $dadosNivelAcesso) {
    $nivelAcesso = $dadosNivelAcesso->getStrStaNivelAcesso();
    $strSelectNivelAcesso[$nivelAcesso] = $arrDescricaoNivelAcesso[$nivelAcesso];
  }

} else if ($verificaNivelAcessoPadrao == 'S') { // Padrao
  $idHipoteseLegal = $objMdPetVincUsuExtPj->getNumIdHipoteseLegal();
  $staNivelAcesso = $objMdPetVincUsuExtPj->getStrStaNivelAcesso();


      $arrHipoteseNivel = [];

      $arrHipoteseNivel['nivelAcesso']['id'] = $staNivelAcesso;
      $arrHipoteseNivel['nivelAcesso']['descricao'] = $arrDescricaoNivelAcesso[$staNivelAcesso];
      if($staNivelAcesso>0) {
          $objHipoteseLegalRN = new HipoteseLegalRN();
          $objHipoteseLegalDTO = new HipoteseLegalDTO();
          $objHipoteseLegalDTO->retStrNome();
          $objHipoteseLegalDTO->setNumIdHipoteseLegal($idHipoteseLegal);

          $arrObjHipoteseLegal = $objHipoteseLegalRN->consultar($objHipoteseLegalDTO);

          $arrHipoteseNivel['hipoteseLegal']['descricao'] = $arrObjHipoteseLegal->getStrNome();
          $arrHipoteseNivel['hipoteseLegal']['id'] = $idHipoteseLegal;
        }

}
$orientacoes = $objMdPetVincUsuExtPj->getStrOrientacoes();


//SessaoSEIExterna::getInstance()->configurarAcessoExterno(null);