<?
/**
 *
 * 19/04/2016 - criado por Lino - GT1 Tecnologia <felipe.silva@gt1tecnologia.com.br>
 * 26/08/2024 - Atualização por gabrielg.colab - SPASSU
 *
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetProcessoINT extends InfraINT
{
    /**
     * Função responsável pela transformação da senha SEI, Recebe ela em base64, decodifica, converte em MD5
     * e devolve o MD5 em base64 novamente.
     * @param String $strSenhaSEI
     * @return string
     * @author Lino - GT1 Tecnologia <felipe.silva@gt1tecnologia.com.br>
     * @since  01/12/2020
     */
    public static function validarSenhaAssinatura($strSenhaSEI){

        $strSenha = base64_decode($strSenhaSEI);
        $senhaCodificada = md5($strSenha);
        $strSenha = base64_encode($senhaCodificada);

        return $strSenha;
    }

}

?>