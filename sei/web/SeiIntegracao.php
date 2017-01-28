<?
abstract class SeiIntegracao {

  //TAM = Tipo Acesso Modulo
  public static $TAM_PERMITIDO = 'P';
  public static $TAM_NEGADO = 'N';

  public abstract function getNome();
  public abstract function getVersao();
  public abstract function getInstituicao();
  public function inicializar($strVersaoSEI) {return null;}
  public function montarBotaoControleProcessos(){return null;}
  public function montarIconeControleProcessos($arrObjProcedimentoAPI){return null;}
  public function montarIconeAcompanhamentoEspecial($arrObjProcedimentoAPI){return null;}
  public function montarBotaoProcesso(ProcedimentoAPI $objProcedimentoAPI){return null;}
  public function montarIconeProcesso(ProcedimentoAPI $objProcedimentoAPI){return null;}
  public function montarBotaoDocumento(ProcedimentoAPI $objProcedimentoAPI, $arrObjDocumentoAPI){return null;}
  public function montarMensagemProcesso(ProcedimentoAPI $objProcedimentoAPI){return null;}
  public function alterarIconeArvoreDocumento(ProcedimentoAPI $objProcedimentoAPI, $arrObjDocumentoAPI){return null;}
  public function montarIconeDocumento(ProcedimentoAPI $objProcedimentoAPI, $arrObjDocumentoAPI){return null;}
  public function atualizarConteudoDocumento(DocumentoAPI $objDocumentoAPI){return null;}
  public function gerarProcesso(ProcedimentoAPI $objProcedimentoAPI){return null;}
  public function concluirProcesso($arrObjProcedimentoAPI){return null;}
  public function reabrirProcesso(ProcedimentoAPI $objProcedimentoAPI){return null;}
  public function sobrestarProcesso(ProcedimentoAPI $objProcedimentoAPI, $objProcedimentoAPIVinculado){return null;}
  public function removerSobrestamentoProcesso(ProcedimentoAPI $objProcedimentoAPI, $objProcedimentoAPIVinculado){return null;}
  public function anexarProcesso(ProcedimentoAPI $objProcedimentoAPIPrincipal, ProcedimentoAPI $objProcedimentoAPIAnexado){return null;}
  public function desanexarProcesso(ProcedimentoAPI $objProcedimentoAPIPrincipal, ProcedimentoAPI $objProcedimentoAPIAnexado){return null;}
  public function relacionarProcesso(ProcedimentoAPI $objProcedimentoAPI1, ProcedimentoAPI $objProcedimentoAPI2){return null;}
  public function removerRelacionamentoProcesso(ProcedimentoAPI $objProcedimentoAPI1, ProcedimentoAPI $objProcedimentoAPI2){return null;}
  public function bloquearProcesso($arrObjProcedimentoAPI){return null;}
  public function desbloquearProcesso($arrObjProcedimentoAPI){return null;}
  public function excluirProcesso(ProcedimentoAPI $objProcedimentoAPI){return null;}
  public function enviarProcesso($arrObjProcedimentoAPI, $arrObjUnidadeAPI){return null;}
  public function gerarDocumento(DocumentoAPI $objDocumentoAPI){return null;}
  public function excluirDocumento(DocumentoAPI $objDocumentoAPI){return null;}
  public function moverDocumento(DocumentoAPI $objDocumentoAPI, ProcedimentoAPI $objProcedimentoAPIOrigem, ProcedimentoAPI $objProcedimentoAPIDestino){return null;}
  public function cancelarDocumento(DocumentoAPI $objDocumentoAPI){return null;}
  public function assinarDocumento($arrObjDocumentoAPI){return null;}
  public function verificarAcessoProtocolo($arrObjProcedimentoAPI, $arrObjDocumentoAPI) {return null;}
  public function verificarAcessoProtocoloExterno($arrObjProcedimentoAPI, $arrObjDocumentoAPI) {return null;}
  public function permitirAndamentoConcluido(AndamentoAPI $objAndamentoAPI){return null;}
  public function montarMenuPublicacoes(){return null;}
  public function montarMenuUsuarioExterno(){return null;}
  public function montarBotaoControleAcessoExterno(){return null;}
  public function montarAcaoControleAcessoExterno($arrObjAcessoExternoAPI){return null;}
  public function montarBotaoAcessoExternoAutorizado(ProcedimentoAPI $objProcedimentoAPI){return null;}
  public function montarAcaoDocumentoAcessoExternoAutorizado($arrObjDocumentoAPI){return null;}
  public function montarAcaoProcessoAnexadoAcessoExternoAutorizado($arrObjProcedimentAPI){return null;}
  public function excluirUsuario($arrObjUsuarioAPI){return null;}
  public function excluirUnidade($arrObjUnidadeAPI){return null;}
  public function processarControlador($strAcao){return null;}
  public function processarControladorAjax($strAcaoAjax){return null;}
  public function processarControladorExterno($strAcao){return null;}
  public function processarControladorPublicacoes($strAcao){return null;}
  public function processarControladorAjaxExterno($strAcaoAjax){return null;}
  public function processarControladorWebServices($strServico){return null;}
  
  //TODO: ponto de extensao temporario para complementar a lista de tipos de tarja de assinatura disponiveis no sistema
  public function montarTipoTarjaAssinaturaCustomizada(){
  	return null;
  }

  public function executar($func, ...$params) {
    try {
      $ret = call_user_func_array(array($this, $func), $params);
    }catch(Exception $e){
      throw new InfraException('Erro processando operaзгo "'.$func.'" no mуdulo "'.$this->getNome().'".', $e);
    }
    return $ret;
  }
}
?>