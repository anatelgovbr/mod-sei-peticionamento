<?
/**
 * ANATEL
 *
 * 
 * 22/07/2016 - criado por marcelo.bezerra@cast.com.br - CAST
 * Arquivo para realizar controle requisição ajax de usuario externo no modulo peticionamento.
 */

try{
    require_once dirname(__FILE__).'/../../SEI.php';
    session_start();
    
    InfraAjax::decodificarPost();
  
 switch($_GET['acao_ajax']){

	case 'md_pet_int_relatorio_grafico':

         $arrRetorno    = [];
         $tamanho       = MdPetIntRelatorioRN::$GRAFICO_TAMANHO_PADRAO;
         $arrSituacao   = MdPetIntRelatorioINT::retornaArraySituacaoRelatorio();

         foreach($arrSituacao as $key => $value){

             $objMdPetIntRelDestDTO = new MdPetIntRelDestinatarioDTO();
             $objMdPetIntRelDestDTO->setProcedimentoDocTIPOFK(InfraDTO::$TIPO_FK_OBRIGATORIA);
             $objMdPetIntRelDestDTO->setAceiteTIPOFK(InfraDTO::$TIPO_FK_OPCIONAL);
             $objMdPetIntRelDestDTO->setStrStaSituacaoIntimacao($key);
             $objMdPetIntRelDestDTO->setStrSinPrincipalDoc('S');
             $objMdPetIntRelDestDTO = (new MdPetIntRelatorioRN())->_addFiltroListagem($objMdPetIntRelDestDTO);

             $valor = (new MdPetIntRelDestinatarioRN())->contar($objMdPetIntRelDestDTO);

             if($valor > 0){
                 array_push($arrRetorno, [
                     'valor'    => $valor,
                     'cor'      => MdPetIntRelatorioINT::retornaArrayCorGrafico($label),
                     'label'    => $value
                 ]);
             }

         }

         echo empty($arrRetorno) ? 'Nenhum registro encontrado.' : MdPetIntRelatorioINT::_retornaHtmlGrafico($_POST['tipoGrafico'], $arrRetorno, $_POST['idTipoIntimacao'], $tamanho);

         break;

	default:
      throw new InfraException("Ação '".$_GET['acao_ajax']."' não reconhecida pelo controlador AJAX do Peticionamento.");
  }
  
}catch(Exception $e){
  InfraAjax::processarExcecao($e);
}
