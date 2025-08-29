<style>
    .clear { clear: both; line-height: 0.5; }
    fieldset{ border-radius: 10px; }
    .trEspaco { height: 1.5em; padding-top: 6px; width: 220px; }
    .tdTabulacao { padding-left: 15px; width: 220px }
    .Texto_Justificado { font-size: 12pt; font-family: Calibri, sans-serif; text-align: justify; word-wrap: normal; text-indent: 0; margin: 6pt; }
    .Tabela_Justificado_Negrito { font-weight: bold; font-size: 12pt; font-family: Calibri; text-align: justify; word-wrap: normal; text-indent: 0; margin-bottom: 20px; }
    ul.poderesLista, ul.abrangenciaLista { padding-left: 0px; margin: 0px; list-style-type: disc; list-style-position: inside; }
    .Tabela_Justificado_Negrito tr td { vertical-align: top; }
</style>
<p class="Texto_Justificado">Procuração Eletrônica @procuracaoTipo nº @procuracaoDocNum, emitida por @outorganteTipoPessoa, que por meio deste ato é suspensa:</p>
<div>
    <table align="center" style="font: normal 12pt Calibri, sans-serif; width: 90%" class="Tabela_Justificado_Negrito" border="0">
        <tbody>
            <tr><td colspan="2" class="clear">&nbsp;</td></tr>

            @procuracaoDados

            <tr class="trEspaco">
                <td colspan="2" style="font: bold 12pt Calibri, sans-serif">Representado:</td>
            </tr>

            @outorganteDados
            
            <tr><td colspan="2" class="clear">&nbsp;</td></tr>

            <tr class="trEspaco">
                <td colspan="2" style="font: bold 12pt Calibri, sans-serif">Procurador:</td>
            </tr>
            <tr>
                <td class="tdTabulacao" style="font: bold 12pt Calibri, sans-serif">Nome:</td>
                <td style="font-weight: normal">@outorgadoNome</td>
            </tr>

            <tr><td colspan="2" class="clear">&nbsp;</td></tr>

            <tr class="trEspaco">
                <td style="font: bold 12pt Calibri, sans-serif">Justificativa:</td>
                <td style="font-weight: normal">@justificativa</td>
            </tr>  
        </tbody>
    </table>
</div>
<p class="Texto_Justificado">Pelo presente instrumento de Suspensão de Procuração Eletrônica, a Administração do SEI-@sigla_orgao@ suspendeu os poderes de representação do Procurador acima identificado no que tange à Procuração Eletrônica @procuracaoTipo nº @procuracaoDocNum, de acordo com a Justificativa acima.</p>
<p class="Texto_Justificado">A existência deste instrumento de Suspensão de Procuração, do processo e dos documentos acima indicados pode ser conferida no Portal na Internet do(a) @descricao_orgao@.</p>