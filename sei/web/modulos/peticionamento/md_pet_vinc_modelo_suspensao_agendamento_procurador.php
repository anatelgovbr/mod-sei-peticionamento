<style>
    .clear { clear: both; line-height: 0.5; }
    fieldset{ border-radius: 10px; }
    .trEspaco { height: 1.5em; padding-top: 6px; width: 220px; }
    .tdTabulacao { padding-left: 15px; width: 250px; font-weight: bold; }
    .Texto_Justificado { font-size: 12pt; font-family: Calibri; text-align: justify; word-wrap: normal; text-indent: 0; margin: 6pt; }
    .Tabela_Justificado_Negrito { font-weight: bold; font-size: 12pt; font-family: Calibri; text-align: justify; word-wrap: normal; text-indent: 0; margin-bottom: 20px; }
    ul.poderesLista, ul.abrangenciaLista { padding-left: 0px; margin: 0px; list-style-type: disc; list-style-position: inside; }
    .Tabela_Justificado_Negrito tr td { vertical-align: top; }
</style>
<p class="Texto_Justificado">Procuração Eletrônica @procuracaoTipo nº @procuracaoDocNum, emitida por @outorganteTipoPessoa, que por meio deste ato é suspensa:</p>
<div>
    <table align="center" style="width: 90%" class="Tabela_Justificado_Negrito" border="0">
        <tbody>
            <tr><td colspan="2" class="clear">&nbsp;</td></tr>

            @procuracaoDados

            <tr class="trEspaco">
                <td colspan="2">Representado</td>
            </tr>

            @outorganteDados
            
            <tr><td colspan="2" class="clear">&nbsp;</td></tr>

            <tr class="trEspaco">
                <td colspan="2">Procurador</td>
            </tr>
            <tr>
                <td class="tdTabulacao">Nome:</td>
                <td style="font-weight: normal">@outorgadoNome</td>
            </tr>

            <tr><td colspan="2" class="clear">&nbsp;</td></tr>

            <tr class="trEspaco">
                <td>Justificativa:</td>
                <td style="font-weight: normal">Suspensão ocorrida conforme formalizado no documento SEI nº @numero_sei@.</td>
            </tr>  
        </tbody>
    </table>
</div>
<p class="Texto_Justificado">Pelo presente instrumento de Suspensão de Procuração Eletrônica, a Administração do SEI-@sigla_orgao@ suspendeu os poderes de representação do Procurador acima identificado no que tange à Procuração Eletrônica @procuracaoTipo nº @procuracaoDocNum.</p>