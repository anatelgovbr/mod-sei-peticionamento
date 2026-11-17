<?php
/**
 * ANATEL
 *
 * Converte exceções em mensagem de validação para o usuário, mantendo a exceção original para o log (ADR-PET-007):
 * as exceções conhecidas do core, no ponto de saída das páginas, depois do rollback, e a falha técnica da conclusão do
 * Peticionamento Externo, na RN, depois do desfazimento. Também informa a mensagem do usuário e o tipo da falha ao
 * rastro e ao aviso do rollback (ADR-PET-009).
 *
 * 28/10/2026 - criado por gabrielg.colab - ANATEL
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetExcecaoINT extends InfraINT
{
    /**
     * Trecho da mensagem do core => mensagem exibida ao usuário. Revisar a cada atualização do SEI.
     *
     * @return array<string, string>
     */
    private static function listarExcecoesConhecidas(): array
    {
        return [
            SeiINT::$MSG_ERRO_XSS => 'O documento possui conteúdo não permitido por restrição de segurança. Revise o conteúdo e tente novamente.',
        ];
    }

    /**
     * Devolve a exceção a ser processada pela página: exceção conhecida do core vira validação com a mensagem da lista
     * e a original anexada para o log; validação e exceção fora da lista voltam sem mudança.
     *
     * @param Throwable $e
     * @return Throwable
     */
    public static function converter(Throwable $e): Throwable
    {
        if ($e instanceof InfraException && $e->contemValidacoes()) {
            return $e;
        }

        $strMensagem = self::buscarMensagem($e);

        if ($strMensagem === null) {
            return $e;
        }

        return self::montarValidacao($strMensagem, $e);
    }

    /**
     * Devolve a exceção a ser processada depois do desfazimento: validação e exceção conhecida do core mantêm a mensagem
     * própria, que diz o que corrigir; a falha técnica vira validação com a mensagem informada e a original anexada para
     * o log.
     *
     * @param Throwable $objExceptionEtapa exceção da etapa que falhou
     * @param string $strMensagem mensagem da falha técnica, que diz o que aconteceu com a operação
     * @return Throwable
     */
    public static function converterFalhaTecnica(Throwable $objExceptionEtapa, string $strMensagem): Throwable
    {
        $objExceptionConvertida = self::converter($objExceptionEtapa);

        if ($objExceptionConvertida instanceof InfraException && $objExceptionConvertida->contemValidacoes()) {
            return $objExceptionConvertida;
        }

        return self::montarValidacao($strMensagem, $objExceptionEtapa);
    }

    /**
     * Indica se a exceção é falha técnica, que chega ao usuário com a mensagem genérica: nem validação nem exceção
     * conhecida do core (ADR-PET-009).
     *
     * @param Throwable $e exceção da etapa que falhou
     * @return bool
     */
    public static function isFalhaTecnica(Throwable $e): bool
    {
        $objExceptionConvertida = self::converter($e);

        return !($objExceptionConvertida instanceof InfraException && $objExceptionConvertida->contemValidacoes());
    }

    /**
     * Texto que a tela mostra para a exceção: as validações em sequência, ou a mensagem da exceção sem validação.
     *
     * @param Throwable $e exceção devolvida por converter ou converterFalhaTecnica
     * @return string
     */
    public static function lerMensagem(Throwable $e): string
    {
        if (!$e instanceof InfraException || !$e->contemValidacoes()) {
            return $e->getMessage();
        }

        $arrMensagens = [];
        foreach ($e->getArrObjInfraValidacao() as $objInfraValidacao) {
            $arrMensagens[] = $objInfraValidacao->getStrDescricao();
        }

        return implode(' ', $arrMensagens);
    }

    /**
     * Monta a validação com a exceção original anexada, sem lançá-la.
     *
     * @param string $strMensagem mensagem exibida ao usuário
     * @param Throwable $objExceptionOriginal exceção original, para o log
     * @return InfraException
     */
    private static function montarValidacao(string $strMensagem, Throwable $objExceptionOriginal): InfraException
    {
        $objInfraException = new InfraException();

        try {
            $objInfraException->lancarValidacao($strMensagem, null, $objExceptionOriginal);
        } catch (InfraException $objValidacao) {
            return $objValidacao;
        }

        return $objInfraException;
    }

    /**
     * Procura um trecho da lista na mensagem da exceção e das exceções encadeadas nela.
     *
     * @param Throwable $e
     * @return string|null
     */
    private static function buscarMensagem(Throwable $e): ?string
    {
        for ($objAtual = $e; $objAtual !== null; $objAtual = $objAtual->getPrevious()) {
            foreach (self::listarExcecoesConhecidas() as $strTrecho => $strMensagem) {
                if (strpos($objAtual->getMessage(), $strTrecho) !== false) {
                    return $strMensagem;
                }
            }
        }

        return null;
    }
}
