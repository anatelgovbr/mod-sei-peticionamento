<?
/**
 *
 * 19/04/2016 - criado por Lino - GT1 Tecnologia <felipe.silva@gt1tecnologia.com.br>
 * 26/08/2024 - Atualização por gabrielg.colab - SPASSU
 *
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetProRepresWhitINT extends InfraINT
{
    /**
     * Função responsável pela transformação da senha SEI, Recebe ela em base64, decodifica, converte em MD5
     * e devolve o MD5 em base64 novamente.
     * @param String $strSenhaSEI
     * @return string
     * @author Lino - GT1 Tecnologia <felipe.silva@gt1tecnologia.com.br>
     * @since  01/12/2020
     */
    public static function validarProcessoRepresentacao($strProtocolo){
        // valida se processo existe
        $objMdPetVinculoDTO = new MdPetVinculoDTO();
        $objMdPetVinculoRN = new MdPetVinculoRN();

        $objMdPetVinculoDTO->retStrProtocoloFormatado();
        $objMdPetVinculoDTO->retStrNomeTipoProcedimento();
        $objMdPetVinculoDTO->retDblIdProtocolo();
        $objMdPetVinculoDTO->setDistinct(true);
        $objMdPetVinculoDTO->setStrProtocoloFormatado($strProtocolo);
        $arrObjMdPetVinculoDTO = $objMdPetVinculoRN->listar($objMdPetVinculoDTO);

        $xml = '<dados>';
        if (is_null($arrObjMdPetVinculoDTO) || count($arrObjMdPetVinculoDTO) == 0) {
            $xml .= "<success>false</success>\n";
            $xml .= "<msg>O processo não existe ou não corresponde a um processo de representação.</msg>\n";
            $xml .= '</dados>';
            return $xml;
        }
        $xml .= "<success>true</success>\n";
        $xml .= "<idProcesso>" . $arrObjMdPetVinculoDTO[0]->getDblIdProtocolo() . "</idProcesso>\n";
        $xml .= "<tipoProcedimento>" . $arrObjMdPetVinculoDTO[0]->getStrNomeTipoProcedimento() . "</tipoProcedimento>\n";
        $xml .= '</dados>';

        return $xml;

    }
}

?>