<?php
/**
 * ANATEL
 *
 * Avisa o administrador do SEI do rollback físico da conclusão do Peticionamento Externo (ADR-PET-009): um e-mail quando
 * o rollback é acionado e termina, outro quando ele falha. O texto é fixo e leva o fluxo, o processo, o motivo informado
 * ao usuário, a classe e o local da causa e o UUID do rastro. Na falha técnica, em que o usuário recebe a mensagem
 * genérica, leva também a mensagem da causa, cortada, e nunca a trilha ou o POST.
 *
 * 05/11/2026 - criado por ANATEL
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetAvisoRollbackRN extends InfraRN
{
    /** Tamanho máximo da mensagem da causa técnica no e-mail (RT-PET-021). */
    public const TAMANHO_MAXIMO_ERRO_TECNICO = 500;

    protected function inicializarObjInfraIBanco()
    {
        return BancoSEI::getInstance();
    }

    /**
     * Avisa que o rollback foi acionado e terminou.
     *
     * @param string $strFluxo nome do fluxo do peticionamento
     * @param string $strReferencia processo ou vínculo desfeito
     * @param Throwable $objErroEtapa erro que acionou o rollback
     * @param string $strMotivo mensagem que o usuário recebeu
     * @return void
     */
    public function avisarAcionamento(string $strFluxo, string $strReferencia, Throwable $objErroEtapa, string $strMotivo): void
    {
        $this->enviar(
            'acionado',
            'SEI Peticionamento: rollback acionado (' . $strFluxo . ')',
            "O rollback foi acionado e terminou: a operação foi desfeita.\n\n"
            . 'Fluxo: ' . $strFluxo . "\n"
            . 'Referência: ' . $strReferencia . "\n"
            . 'Motivo: ' . $strMotivo . "\n"
            . $this->descreverErroTecnico($objErroEtapa)
            . 'Causa: ' . MdPetRastreioINT::descreverCausa($objErroEtapa) . "\n"
        );
    }

    /**
     * Avisa que o rollback falhou e que o que foi gravado precisa de correção manual.
     *
     * @param string $strFluxo nome do fluxo do peticionamento
     * @param string $strReferencia processo ou vínculo que ficou gravado
     * @param Throwable $objErroEtapa erro que acionou o rollback
     * @param Throwable $objErroRollback erro do rollback
     * @param string $strMotivo mensagem que o usuário receberia pela falha da etapa
     * @return void
     */
    public function avisarFalha(string $strFluxo, string $strReferencia, Throwable $objErroEtapa, Throwable $objErroRollback, string $strMotivo): void
    {
        $this->enviar(
            'falhou',
            'SEI Peticionamento: rollback falhou (' . $strFluxo . ')',
            "O rollback falhou: o que foi gravado continua no banco e precisa de correção manual.\n\n"
            . 'Fluxo: ' . $strFluxo . "\n"
            . 'Referência: ' . $strReferencia . "\n"
            . 'Motivo da falha na etapa: ' . $strMotivo . "\n"
            . $this->descreverErroTecnico($objErroEtapa)
            . 'Causa: ' . MdPetRastreioINT::descreverCausa($objErroEtapa) . "\n"
            . 'Erro do rollback: ' . self::descreverErro($objErroRollback) . "\n"
            . 'Causa da falha no rollback: ' . MdPetRastreioINT::descreverCausa($objErroRollback) . "\n"
        );
    }

    /**
     * Linha do erro técnico, só quando a falha é técnica: na validação e na recusa conhecida do core, o motivo já diz o
     * que aconteceu.
     *
     * @param Throwable $objErroEtapa erro que acionou o rollback
     * @return string
     */
    private function descreverErroTecnico(Throwable $objErroEtapa): string
    {
        if (!MdPetExcecaoINT::isFalhaTecnica($objErroEtapa)) {
            return '';
        }

        return 'Erro técnico: ' . self::descreverErro($objErroEtapa) . "\n";
    }

    /**
     * Classe e mensagem da causa raiz, em uma linha e cortada no tamanho máximo, sem a trilha.
     *
     * @param Throwable $objErro erro, com as causas encadeadas
     * @return string
     */
    private static function descreverErro(Throwable $objErro): string
    {
        $objCausa = $objErro;
        while ($objCausa->getPrevious() !== null) {
            $objCausa = $objCausa->getPrevious();
        }

        $strMensagem = trim((string) preg_replace('/\s+/', ' ', $objCausa->getMessage()));
        if (strlen($strMensagem) > self::TAMANHO_MAXIMO_ERRO_TECNICO) {
            $strMensagem = substr($strMensagem, 0, self::TAMANHO_MAXIMO_ERRO_TECNICO) . ' (mensagem cortada)';
        }

        return get_class($objCausa) . ': ' . $strMensagem;
    }

    /**
     * Envia o aviso. A falha no envio fica no rastro e não muda o resultado da operação, que já terminou.
     *
     * @param string $strSituacao situação do rollback, para o rastro
     * @param string $strAssunto assunto do e-mail
     * @param string $strDetalhes início do corpo do e-mail
     * @return void
     */
    private function enviar(string $strSituacao, string $strAssunto, string $strDetalhes): void
    {
        try {
            $objInfraParametro = new InfraParametro(BancoSEI::getInstance());
            $strRastreio = 'pet=' . MdPetRastreioINT::obterUuid();

            $this->enviarEmail(
                $objInfraParametro->getValor('SEI_EMAIL_SISTEMA'),
                $objInfraParametro->getValor('SEI_EMAIL_ADMINISTRADOR'),
                $strAssunto,
                $strDetalhes . 'Rastreio: ' . $strRastreio . "\n\nOs passos da requisição estão no log do SEI. Pesquise por " . $strRastreio . '.'
            );

            MdPetRastreioINT::registrar('Aviso enviado por e-mail ao administrador do SEI: rollback ' . $strSituacao . '.');
        } catch (Throwable $objErroAviso) {
            MdPetRastreioINT::registrarFalha('O aviso por e-mail ao administrador do SEI (rollback ' . $strSituacao . ') não foi enviado.', $objErroAviso);
        }
    }

    /**
     * @param string $strDe remetente
     * @param string $strPara destinatário
     * @param string $strAssunto assunto
     * @param string $strCorpo corpo em texto puro
     * @return void
     */
    protected function enviarEmail(string $strDe, string $strPara, string $strAssunto, string $strCorpo): void
    {
        InfraMail::enviarConfigurado(ConfiguracaoSEI::getInstance(), $strDe, $strPara, null, null, $strAssunto, $strCorpo);
    }
}
