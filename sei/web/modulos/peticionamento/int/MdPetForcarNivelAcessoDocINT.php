<?
/**
* ANATEL
*
* 30/08/2016 - criado por jaqueline.mendes@castgroup.com.br - CAST
* 26/08/2024 - Atualização por gabrielg.colab - SPASSU
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetForcarNivelAcessoDocINT extends InfraINT {

    public static function forcarNivelAcessoDocumento($tipoPeticionamento){

        if(!in_array($tipoPeticionamento, ['N','I'])){
            throw new InfraException('Parâmetro "$tipoPeticionamento" fora valor esperado.');
        }

        if(isset($_POST['hdnForcarNivelAcessoDoc']) && $_POST['hdnForcarNivelAcessoDoc'] == 'S'){

            $objMdPetForcarNivelAcessoDocDTO2 = new MdPetForcarNivelAcessoDocDTO();
            $objMdPetForcarNivelAcessoDocDTO2->setStrTipoPeticionamento($tipoPeticionamento);
            $objMdPetForcarNivelAcessoDocDTO2->retTodos();
            $objMdPetForcarNivelAcessoDocDTO2 = (new MdPetForcarNivelAcessoDocRN())->consultar($objMdPetForcarNivelAcessoDocDTO2);

            preg_match_all('/\d+/', $_POST['hdnTipoDocumento'], $matches);

            $idsTiposDocumentos = implode(',', $matches[0]);
            $idHipoteseLegal    = $_POST['staNivelAcesso'] == 'R' ? $_POST['idHipoteseLegal'] : null;

            if(!empty($objMdPetForcarNivelAcessoDocDTO2)){
                if(empty($_POST['staNivelAcesso']) && empty($idsTiposDocumentos)){
                    (new MdPetForcarNivelAcessoDocRN())->excluir($objMdPetForcarNivelAcessoDocDTO2);
                }else{
                    $objMdPetForcarNivelAcessoDocDTO2->setStrNivelAcesso($_POST['staNivelAcesso']);
                    $objMdPetForcarNivelAcessoDocDTO2->setStrTipoPeticionamento($tipoPeticionamento);
                    $objMdPetForcarNivelAcessoDocDTO2->setNumIdHipoteseLegal($idHipoteseLegal);
                    $objMdPetForcarNivelAcessoDocDTO2->setStrIdsTiposDocumento($idsTiposDocumentos);
                    (new MdPetForcarNivelAcessoDocRN())->alterar($objMdPetForcarNivelAcessoDocDTO2);
                }
            }else{
                if($_POST['staNivelAcesso'] != '' && !empty($idsTiposDocumentos)) {
                    $objMdPetForcarNivelAcessoDocDTO = new MdPetForcarNivelAcessoDocDTO();
                    $objMdPetForcarNivelAcessoDocDTO->setStrNivelAcesso($_POST['staNivelAcesso']);
                    $objMdPetForcarNivelAcessoDocDTO->setStrTipoPeticionamento($tipoPeticionamento);
                    $objMdPetForcarNivelAcessoDocDTO->setNumIdHipoteseLegal($idHipoteseLegal);
                    $objMdPetForcarNivelAcessoDocDTO->setStrIdsTiposDocumento($idsTiposDocumentos);
                    (new MdPetForcarNivelAcessoDocRN())->cadastrar($objMdPetForcarNivelAcessoDocDTO);
                }
            }

        }

    }

    public static function getDadosForcarNivelAcessoDoc($tipoPeticionamento){

        $retorno = [];

        // Forcando Nivel de Acesso parametrizado na Administracao:
        $objMdPetForcarNivelAcessoDocDTO = new MdPetForcarNivelAcessoDocDTO();
        $objMdPetForcarNivelAcessoDocDTO->setStrTipoPeticionamento($tipoPeticionamento);
        $objMdPetForcarNivelAcessoDocDTO->retTodos();
        $objForcaNivel = (new MdPetForcarNivelAcessoDocRN())->consultar($objMdPetForcarNivelAcessoDocDTO);

        if(!empty($objForcaNivel)){
            $retorno = [
                'nivel' => $objForcaNivel->getStrNivelAcesso() == 'R' ? 1 : 0,
                'hipotese' => $objForcaNivel->getNumIdHipoteseLegal(),
                'documentos' => explode(',', $objForcaNivel->getStrIdsTiposDocumento())
            ];
        }

        return $retorno;

    }

    public static function autoCompletarTipoDocumento($strPalavrasPesquisa, $strStaAplicabilidade = null){
        $objSerieDTO = new SerieDTO();
        $objSerieDTO->retNumIdSerie();
        $objSerieDTO->retStrNome();
        if ($strStaAplicabilidade != null){
            $objSerieDTO->setStrStaAplicabilidade(explode(',', $strStaAplicabilidade), InfraDTO::$OPER_IN);
        }
        $objSerieDTO->setOrdStrNome(InfraDTO::$TIPO_ORDENACAO_ASC);
        $arrObjSerieDTO = (new SerieRN())->listarRN0646($objSerieDTO);

        $strPalavrasPesquisa = strtolower(InfraString::excluirAcentos(trim($strPalavrasPesquisa)));
        if ($strPalavrasPesquisa != ''){
            $ret = [];
            foreach($arrObjSerieDTO as $objSerieDTO){
                if (strpos(strtolower(InfraString::excluirAcentos($objSerieDTO->getStrNome())), $strPalavrasPesquisa) !== false){
                    $ret[] = $objSerieDTO;
                }
            }
        }else{
            $ret = $arrObjSerieDTO;
        }
        return $ret;
    }
	
}