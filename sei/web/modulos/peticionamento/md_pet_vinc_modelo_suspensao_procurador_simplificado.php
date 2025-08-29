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
<div>
    <table align="center" style="width: 90%" class="Tabela_Justificado_Negrito" border="0">
        <tbody>
            <tr><td colspan="2" class="clear">&nbsp;</td></tr>

            <tr class="trEspaco">
                <td colspan="2">Representado:</td>
            </tr>

            @outorganteDados

            <tr><td colspan="2" class="clear">&nbsp;</td></tr>
            <tr class="trEspaco">
                <td colspan="2">Representante:</td>
            </tr>

            @outorgadoDados

            <tr><td colspan="2" class="clear">&nbsp;</td></tr>

            @numeroSEI

            <tr><td colspan="2" class="clear">&nbsp;</td></tr>
            <tr>
                <td class="tdTabulacao" style="font: bold 12pt Calibri, sans-serif">Data:</td>
                <td style="font-weight: normal">@data_suspensao@</td>
            </tr>
            <tr>
                <td class="tdTabulacao" style="font: bold 12pt Calibri, sans-serif">Processo:</td>
                <td style="font-weight: normal">@protocolo_formatado@</td>
            </tr>

        </tbody>
    </table>
</div>
<p class="Texto_Justificado">Pelo presente instrumento de Suspensão de Procuração Eletrônica, a Administração do SEI-@sigla_orgao@ suspendeu o cadastro do Usuário Externo acima indicado como Representado e, assim, todas as Procurações Eletrônicas concedidas para representá-lo restam igualmente suspensas até que o seu cadastro seja restabelecido.</p>
<p class="Texto_Justificado">A existência deste instrumento de Suspensão de Procuração, do processo e dos documentos acima indicados pode ser conferida no Portal na Internet do(a) Agência Nacional de Telecomunicações.</p>
