<style>
    #descritivo{
        text-align: justify;
        line-height: 1.6;
    }
    table.comBordaSimples {
        border-collapse: collapse;/* CSS2 */

    }
    table.comBordaSimples td {
        text-align: left;
        padding: 8px;
    }
    table.comBordaSimples th {
        border: 1px solid #b2b2b2;
        background: #cccccc;
        text-align: left;
        padding: 8px;
    }
    td  {
        font-weight: normal;
        background: #ffffff;
    }
    td.cinza  {
        font-weight: bold;
    }

    .Texto {
        font-size: 12pt;
        font-family: Calibri;
        word-wrap: normal;
    }
    .Texto_Justificado {
        font-size: 12pt;
        font-family: Calibri;
        text-align: justify;
        word-wrap: normal;
        text-indent: 0;
        margin: 6pt;
    }
    .Texto_Justificado_Negrito {
        font-weight: bold;
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
        margin: 6pt;
    }
</style>

<div id="descritivo">
<p class="Texto_Justificado">@vinculacao_substituicao@</p>
</div>

<div id="descritivo">
<p class="Texto_Justificado">O Usuário Externo declarou ser o Responsável Legal pela Pessoa Jurídica e ter ciência de que o ato de inserir ou fazer inserir declaração falsa ou diversa da que devia ser escrita é crime, conforme disposto no art. 299 do Código Penal Brasileiro. Com isso, concordou que terá poderes para:</p>
<label>
    <ol>
        <li><p class="Texto_Justificado">Gerenciar o cadastro da Pessoa Jurídica;</p></li>
		<li><p class="Texto_Justificado">Receber Intimações Eletrônicas e realizar Peticionamento Eletrônico em nome da Pessoa Jurídica, com todos os poderes previstos no sistema;</p></li>
        <li><p class="Texto_Justificado">Conceder Procurações Eletrônicas Especiais a outros Usuários Externos, bem como revogá-las quando lhe convier;</p></li>
        <li><p class="Texto_Justificado">Conceder Procurações Eletrônicas Simples a outros Usuários Externos, em âmbito geral ou para processos específicos, conforme poderes estabelecidos, para representação da Pessoa Jurídica Outorgante, bem como revogá-las quando lhe convier.</p></li>
    </ol>
</label>
</div>

<p class="Texto_Justificado_Negrito" style="margin-left: 6pt;@p_estilo_substituido">Usuário Externo Substituído como Responsável Legal:</p>
<table class="comBordaSimples Tabela_Justificado_Negrito" width="100%" style="font-weight: bold;@table_estilo_substituido">
   <tr>
     <td class="cinza" style="width: 20%; padding-left: 35px;">Nome:</td>
     <td>@nomeSubstituido</td>
   </tr>
</table>

<p class="Texto_Justificado_Negrito" style="margin-left: 6pt;margin-top: 1.5%">Usuário Externo indicado como Responsável Legal:</p>
<table class="comBordaSimples Tabela_Justificado_Negrito" width="100%" style="font-weight: bold;">
   <tr>
     <td class="cinza" style="width: 20%; padding-left: 35px;">Nome:</td>
     <td>@nome</td>
   </tr>
</table>

<p class="Texto_Justificado_Negrito" style="margin-left: 6pt;margin-top: 1.5%">Pessoa Jurídica:</p>
<table class="comBordaSimples Tabela_Justificado_Negrito" width="100%" >
    <tbody>
    <tr>
        <td class="cinza" style="width: 20%; padding-left: 35px;">CNPJ:</td>
        <td>@cnpjVinculo</td>
    </tr>
    <tr >
        <td class="cinza" style="padding-left: 35px;">Razão Social:</td>
        <td>@razaoSocial</td>
    </tr>
    <tr>
        <td class="cinza" style="padding-left: 35px;">UF:</td>
        <td>@uf</td>
    </tr>
    <tr >
        <td class="cinza" style="padding-left: 35px;">Cidade:</td>
        <td>@cidade</td>
    </tr>
    </tbody>
</table>
<p class="Texto_Justificado">Os atos constitutivos anexados ao presente documento de Vinculação pelo o Usuário Externo para comprovação dos poderes a ele concedidos para atuar em nome da Pessoa Jurídica constam no correspondente Recibo Eletrônico de Protocolo gerado.</p>
@motivo