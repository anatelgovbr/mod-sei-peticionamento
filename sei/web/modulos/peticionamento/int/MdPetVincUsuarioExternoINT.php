<?
/**
 * TRIBUNAL REGIONAL FEDERAL DA 4ª REGIÃO
 *
 * 08/02/2012 - criado por bcu
 *
 * Versão do Gerador de Código: 1.32.1
 *
 * Versão no CVS: $Id$
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetVincUsuarioExternoINT extends InfraINT
{

  public static function consultarDadosUsuario($dados)
  {
      $existeVinculo = false;
      $xml = '';

      if(isset($dados['hdnSelPessoaJuridica'])) {
          //verifica se o procurador indicado já possui uma procuração naquele mesmo vinculo
          $existeVinculo = self::_consultarExistenciaVinculo($dados);

          if($existeVinculo) {
              $xml .= '<dados>';
              $xml .= '<sucesso>0</sucesso>';
              $xml .= '</dados>';
              return $xml;
          }
      }

      $idContato = $dados['hdnIdUsuarioProcuracao'];

      $objUsuarioRN = new UsuarioRN();
      $objUsuarioDTO = new UsuarioDTO();
      $objUsuarioDTO->retNumIdContato();
      $objUsuarioDTO->retStrNomeContato();
      $objUsuarioDTO->retStrSigla();
      $objUsuarioDTO->retDblCpfContato();
      $objUsuarioDTO->setNumIdContato($idContato);

      $arrContato = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

      if (count($arrContato) > 0) {
          $xml .= '<dados>';
          $xml .= '<no-usuario>' . $arrContato->getStrNomeContato() . '</no-usuario>';
          $xml .= '<ds-email>' . $arrContato->getStrSigla() . '</ds-email>';
          $xml .= '<nu-cpf>' . InfraUtil::formatarCpf($arrContato->getDblCpfContato()) . '</nu-cpf>';
          $xml .= '<sucesso>1</sucesso>';
          $xml .= '</dados>';
      }

      return $xml;
  }

  public static function consultarDadosUsuarioExterno($dados)
  {
    $existeVinculo = false;
    $xml = '';

    if(isset($dados['hdnSelPessoaJuridica'])) {
        //verifica se o procurador indicado já possui uma procuração naquele mesmo vinculo
        $existeVinculo = self::_consultarExistenciaVinculo($dados);

       if($existeVinculo) {
            $xml .= '<dados>';
            $xml .= '<sucesso>0</sucesso>';
            $xml .= '</dados>';
            return $xml;
       }
    }

    $idContato = $dados['hdnIdUsuarioProcuracao'];

    $objUsuarioRN = new UsuarioRN();
    $objUsuarioDTO = new UsuarioDTO();
    $objUsuarioDTO->retNumIdContato();
    $objUsuarioDTO->retStrNomeContato();
    $objUsuarioDTO->retStrSigla();
    $objUsuarioDTO->retDblCpfContato();
    $objUsuarioDTO->setNumIdContato($idContato);

    $arrContato = $objUsuarioRN->consultarRN0489($objUsuarioDTO);

    if (count($arrContato) > 0) {
        $xml .= '<dados>';
        $xml .= '<nu-cpf>' . InfraUtil::formatarCpf($arrContato->getDblCpfContato()) . '</nu-cpf>';
        $xml .= '<no-usuario>' . $arrContato->getStrNomeContato() . '</no-usuario>';
        $xml .= '<sucesso>1</sucesso>';
        $xml .= '</dados>';
    }

    return $xml;
  }


  private static function _consultarExistenciaVinculo($dados)
  {

      $objMdPetVincRepresentantDTO = new MdPetVincRepresentantDTO();
      $objMdPetVincRepresentantRN = new MdPetVincRepresentantRN();

      $objMdPetVincRepresentantDTO->setNumIdContato($dados['hdnIdUsuarioProcuracao']);
      $objMdPetVincRepresentantDTO->setNumIdContatoVinc($dados['hdnSelPessoaJuridica']);
      $objMdPetVincRepresentantDTO->setStrStaEstado('A');
      $objMdPetVincRepresentantDTO->setStrSinAtivo('S');
      $objMdPetVincRepresentantDTO->retNumIdContato();

      $objMdPetVincRepresentantDTO = $objMdPetVincRepresentantRN->contar($objMdPetVincRepresentantDTO)>0;

      return $objMdPetVincRepresentantDTO;
  }


  public static function consultarUsuarioValido($params){
	
    $xml  = '<dados>';

    $cpf = array_key_exists('cpf', $params) ? $params['cpf'] : null;

    $objContatoDTO = new ContatoDTO();
    $objContatoDTO->retNumIdUsuarioCadastro();
    $objContatoDTO->retNumIdContato();
    $objContatoDTO->retStrNome();

    $objMdPetContatoRN = new MdPetContatoRN();
//    $objContatoDTO->setNumIdTipoContato($objMdPetContatoRN->getIdTipoContatoUsExt());
    $objContatoDTO->setDblCpf(InfraUtil::retirarFormatacao($cpf));
//    if($cpf) {        
//        if (is_numeric(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno())){
//            $objUsuarioRN = new UsuarioRN();
//            $objUsuarioDTO = new UsuarioDTO();
//            $objUsuarioDTO->retNumIdContato();
//            $objUsuarioDTO->setDblCpfContato(InfraUtil::retirarFormatacao($cpf));
////            $objUsuarioDTO->setNumIdUsuario(SessaoSEIExterna::getInstance()->getNumIdUsuarioExterno());
//
//            $arrObjUsuarioDTO = $objUsuarioRN->listarRN0490($objUsuarioDTO);
//
//            if (count($arrObjUsuarioDTO)>0){
//                $objContatoDTO->setDblCpf(0);
//            }else{
//                $objContatoDTO->setDblCpf(InfraUtil::retirarFormatacao($cpf));
//            }
//        }
//    }else{
//        $objContatoDTO->setDblCpf(0);
//    }


    $objContatoRN = new ContatoRN();
    $arrObjContatoDTO = $objContatoRN->listarRN0325($objContatoDTO);

    if (count($arrObjContatoDTO)>0){

        
        $arrIdContato = InfraArray::converterArrInfraDTO($arrObjContatoDTO, 'IdContato');

        $objUsuarioDTO = new UsuarioDTO();
        $objUsuarioDTO->setNumIdContato($arrIdContato, InfraDTO::$OPER_IN);
        $objUsuarioDTO->retStrStaTipo();
        $objUsuarioDTO->retStrNome();
        $objUsuarioDTO->retStrSinAtivo();
        $objUsuarioDTO->retStrSigla();
        $objUsuarioDTO->retNumIdContato();

        $objUsuarioRN = new UsuarioRN();
        $arrObjUsuarioDTO = $objUsuarioRN->listarRN0490($objUsuarioDTO);

        if (count($arrObjUsuarioDTO)>0){
            foreach ($arrObjUsuarioDTO as $objUsuarioDTO){
                $xml .= '<contato>';
                if ($objUsuarioDTO->getStrStaTipo()==UsuarioRN::$TU_EXTERNO && $objUsuarioDTO->getStrSinAtivo() == 'S'){
                    $xml .= '<nu-cpf>' . $params['cpf'] . '</nu-cpf>';
                    $xml .= '<no-usuario>' . $objUsuarioDTO->getStrNome() . '</no-usuario>';
                    $xml .= '<nu-contato>' . $objUsuarioDTO->getNumIdContato() . '</nu-contato>';
                    $xml .= '<sg-contato>' . $objUsuarioDTO->getStrSigla() . '</sg-contato>';
                    $xml .= '<sucesso>1</sucesso>';
                }elseif ($objUsuarioDTO->getStrStaTipo()==UsuarioRN::$TU_EXTERNO_PENDENTE){  
                    $xml .= '<nu-cpf>' . $params['cpf'] . '</nu-cpf>';
                    $xml .= '<no-usuario>' . $objUsuarioDTO->getStrNome() . '</no-usuario>';
                    $xml .= '<nu-contato>' . $objUsuarioDTO->getNumIdContato() . '</nu-contato>';
                    $xml .= '<sg-contato>' . $objUsuarioDTO->getStrSigla() . '</sg-contato>';
                    $xml .= '<mensagem>pendente</mensagem>';
                    $xml .= '<sucesso>1</sucesso>';
                }
                $xml .= '</contato>';
            }
        }
    }

    $xml .= '</dados>';

    return $xml;

  }

}
