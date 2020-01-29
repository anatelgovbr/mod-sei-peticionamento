<style>

    .clear {
        clear: both;
        line-height: 0.5;
    }

    fieldset{
        border-radius: 10px;
    }
    
    .trEspaco {
        height: 1.5em;
        padding-top: 6px;
    }
    .tdTabulacao {
        padding-left: 15px;
    }
    .tdTabulacao2 {
        padding-left: 30px;

    }

    #descritivo{
        text-align: justify;
        line-height: 1.6;
    }

    .Texto_Justificado {
        font-size: 12pt;
        font-family: Calibri;
        text-align: justify;
        word-wrap: normal;
        text-indent: 0;
        margin: 6pt;
    }
    .Tabela_Justificado_Negrito {
        font-weight: bold;
        font-size: 12pt;
        font-family: Calibri;
        text-align: justify;
        word-wrap: normal;
        text-indent: 0;
    }
</style>

<div>
    <table align="center" style="width: 90%;" class="Tabela_Justificado_Negrito" border=0>
        <tbody>
        <tr>
            <td style="width: 220px" class="tdTabulacao">Pessoa Jurídica Outorgante:</td>
            <td style="font-weight: normal;"> @RazaoSocial </td>
        </tr>
        <tr>
            <td class="tdTabulacao">CNPJ:</td>
            <td style="font-weight: normal;"> @Cnpj </td>
        </tr>
        <tr>
            <td class="tdTabulacao">Responsavel Legal:</td>
            <td style="font-weight: normal;">  @nomeRespLegal </td>
        </tr>
        <tr>
            <td class="tdTabulacao">Outorgado:</td>
            <td style="font-weight: normal;">  @nomeOutorgado </td>
        </tr>

        <tr>
            <td class="tdTabulacao" style="vertical-align:top;">Poderes:</td>
            <td style="font-weight: normal;"> @tpPoderes </td>
        </tr>
        <tr>
            <td class="tdTabulacao">Validade:</td>
            <td style="font-weight: normal;"> @validade </td>
        </tr>
        <tr>
            <td class="tdTabulacao">Abrangência:</td>
            <td style="font-weight: normal;">@abrangencia</td>
        </tr>
        <tr>
            <td class="tdTabulacao"></td>
            <td style="font-weight: normal;">@tabela_processos_procuracao</td>
        </tr>
        </tbody>
    </table>

</div>
<div>
    <p class="Texto_Justificado">No âmbito do(a) @sigla_orgao@, a presente Procuração Eletrônica concede ao Outorgado os Poderes expressamente estabelecidos e em conformidade com a Validade e Abrangência definidos acima.</p>
    <p class="Texto_Justificado">O Outorgante declarou ciente de que:</p>
    <label>
        <ul>
            <li><p class="Texto_Justificado" style="margin: 0">Poderá, a qualquer tempo, por meio do SEI-@sigla_orgao@, revogar a Procuração Eletrônica;</p></li>
            <li><p class="Texto_Justificado" style="margin: 0">O Outorgado poderá, a qualquer tempo, por meio do SEI-@sigla_orgao@, renunciar a Procuração Eletrônica;</p></li>
            <li><p class="Texto_Justificado" style="margin: 0">A validade desta Procuração está circunscrita ao(à) @sigla_orgao@ e em conformidade com os Poderes, Validade e Abrangência definidos, salvo se revogada ou renunciada, de modo  que  ela  não  pode ser  usada  para  convalidar quaisquer atos praticados pelo Outorgado em representação do Outorgante no âmbito de outros órgãos ou entidades.</p></li>
        </ul>
    </label>
</div>
<p class="Texto_Justificado">A existência e validade desta Procuração Eletrônica pode ser conferida no Portal na Internet do(a) @descricao_orgao@.</p>
