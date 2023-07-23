<style>
    .clear { clear: both; line-height: 0.5; }
    fieldset{ border-radius: 10px; }
    .trEspaco { height: 1.5em; padding-top: 6px; width: 220px; }
    .tdTabulacao { padding-left: 15px; width: 220px }
    .Texto_Justificado { font-size: 12pt; font-family: Calibri; text-align: justify; word-wrap: normal; text-indent: 0; margin: 6pt; }
    .Tabela_Justificado_Negrito { font-weight: bold; font-size: 12pt; font-family: Calibri; text-align: justify; word-wrap: normal; text-indent: 0; margin-bottom: 20px; }
    ul.poderesLista, ul.abrangenciaLista { padding-left: 0px; margin: 0px; list-style-type: disc; list-style-position: inside; }
    .Tabela_Justificado_Negrito tr td { vertical-align: top; }
</style>
<p class="Texto_Justificado">Procuração Eletrônica @procuracaoTipo nº @procuracaoDocNum, emitida por @outorganteTipoPessoa, que por meio deste ato é restabelecida:</p>
<div>
    <table align="center" style="width: 90%" class="Tabela_Justificado_Negrito" border="0">
        <tbody>
            <tr><td colspan="2" class="clear">&nbsp;</td></tr>

            @procuracaoDados

            <tr class="trEspaco">
                <td colspan="2">Representado:</td>
            </tr>

            @outorganteDados
            
            <tr><td colspan="2" class="clear">&nbsp;</td></tr>

            <tr class="trEspaco">
                <td colspan="2">Procurador:</td>
            </tr>
            <tr>
                <td class="tdTabulacao">Nome:</td>
                <td style="font-weight: normal">@outorgadoNome</td>
            </tr>

            <tr><td colspan="2" class="clear">&nbsp;</td></tr>

            <tr class="trEspaco">
                <td>Documento de Justificativa:</td>
                <td style="font-weight: normal">@numeroSEI</td>
            </tr>    
        </tbody>
    </table>
</div>
<p class="Texto_Justificado">Pelo presente instrumento de Restabelecimento de Procuração Eletrônica, a Administração do SEI-@sigla_orgao@ restabeleceu os poderes de representação do Procurador acima identificado no que tange à Procuração Eletrônica @procuracaoTipo nº @procuracaoDocNum, de acordo com o Documento de Justificativa acima enumerado. </p>
<p class="Texto_Justificado">Assim, comunicamos que ficam restabelecidos os poderes de representação conforme consta na citada Procuração Eletrônica @procuracaoTipo nº @procuracaoDocNum.</p>
<p class="Texto_Justificado">A existência deste instrumento de Restabelecimento de Procuração, do processo e dos documentos acima indicados pode ser conferida no Portal na Internet do(a) @descricao_orgao@.</p>