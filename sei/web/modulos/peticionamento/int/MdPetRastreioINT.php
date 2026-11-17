<?php
/**
 * ANATEL
 *
 * Rastro da conclusão do Peticionamento Externo no log do SEI. Cada linha leva o UUID da requisição, que recupera todos
 * os passos de um peticionamento, e é escrita para quem não conhece o código: o fluxo, a etapa, o que ela faz e o
 * resultado. Grava identificadores e, na linha de falha, a mensagem que o usuário recebeu (ADR-PET-009), nunca a trilha.
 *
 * O log usa a conexão do BancoSEI: o que é gravado entre as transações sobrevive a qualquer falha, e o que é gravado
 * dentro de uma transação some se ela for cancelada. Por isso os marcos ficam entre as transações, e os passos de uma
 * transação cancelada são gravados de novo depois dela.
 *
 * 05/11/2026 - criado por ANATEL
 */

require_once dirname(__FILE__) . '/../../../SEI.php';

class MdPetRastreioINT extends InfraINT
{
    private static ?string $strUuid = null;

    /** @var list<array{string, string}> passos da transação em andamento, com a hora em que aconteceram */
    private static array $arrPassosDaTransacao = [];

    /** Número do último passo do rollback em andamento. */
    private static int $numPassoDoRollback = 0;

    /**
     * UUID versão 4 da requisição. Vai também para o POST, que o core imprime no log de erro: assim esse log, que não
     * passa por esta classe, também pode ser localizado pelo UUID.
     *
     * @return string
     */
    public static function obterUuid(): string
    {
        if (self::$strUuid !== null) {
            return self::$strUuid;
        }

        $strBytes = random_bytes(16);
        $strBytes[6] = chr((ord($strBytes[6]) & 0x0f) | 0x40);
        $strBytes[8] = chr((ord($strBytes[8]) & 0x3f) | 0x80);
        self::$strUuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($strBytes), 4));
        $_POST['mdPetRastreio'] = self::$strUuid;

        return self::$strUuid;
    }

    /**
     * Grava o passo no log, no nível de informação. Dentro de transação, guarda o passo para regravá-lo se ela falhar.
     *
     * @param string $strPasso frase completa, com identificadores e sem dado pessoal
     * @return void
     */
    public static function registrar(string $strPasso): void
    {
        if (BancoSEI::getInstance()->isBolProcessandoTransacao()) {
            self::$arrPassosDaTransacao[] = [InfraData::getStrHoraAtual(), $strPasso];
        } else {
            self::$arrPassosDaTransacao = [];
        }

        self::gravar($strPasso);
    }

    /**
     * Grava o início do rollback e recomeça a contagem dos passos dele.
     *
     * @param string $strPasso frase completa
     * @return void
     */
    public static function registrarInicioDoRollback(string $strPasso): void
    {
        self::$numPassoDoRollback = 0;

        self::registrar($strPasso);
    }

    /**
     * Grava um passo do rollback com o número dele: "<fluxo>, rollback passo <número>: <passo>".
     *
     * @param string $strFluxo nome do fluxo do peticionamento
     * @param string $strPasso o que o passo desfez, com identificadores
     * @return void
     */
    public static function registrarPassoDoRollback(string $strFluxo, string $strPasso): void
    {
        self::$numPassoDoRollback++;

        self::registrar($strFluxo . ', rollback passo ' . self::$numPassoDoRollback . ': ' . $strPasso);
    }

    /**
     * Depois da falha, regrava os passos que a transação cancelada apagou e registra a falha, com o motivo informado ao
     * usuário, quando houver, e a classe, o arquivo e a linha da causa. A trilha fica só no log de erro do SEI.
     *
     * @param string $strPasso frase completa, com identificadores
     * @param Throwable $objErro erro que interrompeu o fluxo
     * @param string|null $strMotivo mensagem que o usuário recebeu (ADR-PET-009)
     * @return void
     */
    public static function registrarFalha(string $strPasso, Throwable $objErro, ?string $strMotivo = null): void
    {
        foreach (self::$arrPassosDaTransacao as [$strHora, $strPassoCancelado]) {
            self::gravar('Passo das ' . $strHora . ' cancelado pelo banco junto com a transação que falhou: ' . $strPassoCancelado);
        }

        self::$arrPassosDaTransacao = [];

        $strLinha = $strPasso;
        if ($strMotivo !== null && trim($strMotivo) !== '') {
            $strLinha .= ' Motivo informado ao usuário: ' . self::terminarFrase($strMotivo);
        }

        self::gravar($strLinha . ' Local técnico: ' . self::descreverCausa($objErro) . '.');
    }

    /**
     * "processo <número formatado> (id <id>)", ou "processo <id>" enquanto o número formatado não é conhecido.
     *
     * @param int|float|string|null $numIdProcesso id do processo
     * @param string|null $strProtocoloFormatado número do processo, quando já existe
     * @return string
     */
    public static function descreverProcesso($numIdProcesso, ?string $strProtocoloFormatado = null): string
    {
        if ($strProtocoloFormatado === null || $strProtocoloFormatado === '') {
            return 'processo ' . $numIdProcesso;
        }

        return 'processo ' . $strProtocoloFormatado . ' (id ' . $numIdProcesso . ')';
    }

    /**
     * Classe, arquivo e linha da causa, sem a mensagem.
     *
     * @param Throwable $objErro erro, com as causas encadeadas
     * @return string
     */
    public static function descreverCausa(Throwable $objErro): string
    {
        $objCausa = $objErro;
        while ($objCausa->getPrevious() !== null) {
            $objCausa = $objCausa->getPrevious();
        }

        return get_class($objCausa) . ' em ' . basename($objCausa->getFile()) . ':' . $objCausa->getLine();
    }

    /**
     * Texto em uma linha, terminado por ponto quando não termina com pontuação.
     *
     * @param string $strTexto
     * @return string
     */
    private static function terminarFrase(string $strTexto): string
    {
        $strTexto = trim((string) preg_replace('/\s+/', ' ', $strTexto));

        return in_array(substr($strTexto, -1), ['.', '!', '?'], true) ? $strTexto : $strTexto . '.';
    }

    /**
     * A falha ao gravar o rastro não pode interromper o peticionamento.
     *
     * @param string $strTexto
     * @return void
     */
    private static function gravar(string $strTexto): void
    {
        try {
            LogSEI::getInstance()->gravar('[pet=' . self::obterUuid() . '] ' . $strTexto, InfraLog::$INFORMACAO);
        } catch (\Throwable $objErroLog) {
            return;
        }
    }
}
