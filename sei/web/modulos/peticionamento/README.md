
# M�dulo Peticionamento e Intima��o Eletr�nicos

## Requisitos
- Requisito M�nimo � o SEI 5.0.3 instalado/atualizado - N�o � compat�vel com vers�es anteriores e em vers�es mais recentes � necess�rio conferir antes se possui compatibilidade.
   - Verificar valor da constante de vers�o no arquivo /sei/web/SEI.php ou, ap�s logado no sistema, parando o mouse sobre a logo do SEI no canto superior esquerdo.
- Antes de executar os scripts de instala��o/atualiza��o, o usu�rio de acesso aos bancos de dados do SEI e do SIP, constante nos arquivos ConfiguracaoSEI.php e ConfiguracaoSip.php, dever� ter permiss�o de acesso total ao banco de dados, permitindo, por exemplo, cria��o e exclus�o de tabelas.
- Os c�digos-fonte do M�dulo podem ser baixados a partir do link a seguir, devendo sempre utilizar a vers�o mais recente: [https://github.com/anatelgovbr/mod-sei-peticionamento/releases](https://github.com/anatelgovbr/mod-sei-peticionamento/releases "Clique e acesse")
- Se j� tiver instalado vers�o principal com a execu��o dos scripts de banco do m�dulo no SEI e no SIP, **em vers�es intermedi�rias basta sobrescrever os c�digos** e n�o precisa executar os scripts de banco novamente.
   - Atualiza��es de vers�es intermedi�rias s�o melhorias apenas de c�digo e s�o identificadas com o incremento somente do terceiro d�gito da vers�o (p. ex. v4.1.1, v4.1.2) e n�o envolve execu��o de scripts de banco.

## Procedimentos para Instala��o
1. Fazer backup dos bancos de dados do SEI e do SIP.
2. Carregar no servidor os arquivos do m�dulo nas pastas correspondentes nos servidores do SEI e do SIP.
   - **Caso se trate de atualiza��o de vers�o anterior do M�dulo**, antes de copiar os c�digos-fontes para a pasta "/sei/web/modulos/peticionamento", � necess�rio excluir os arquivos anteriores pr� existentes na mencionada pasta, para n�o manter arquivos de c�digos que foram renomeados ou descontinuados.
3. Editar o arquivo "/sei/config/ConfiguracaoSEI.php", tomando o cuidado de usar editor que n�o altere o charset do arquivo, para adicionar a refer�ncia � classe de integra��o do m�dulo e seu caminho relativo dentro da pasta "/sei/web/modulos" na array 'Modulos' da chave 'SEI':

		'SEI' => array(
			...
			'Modulos' => array(
				'PeticionamentoIntegracao' => 'peticionamento',
				),
			),

4. Antes de seguir para os pr�ximos passos, � importante conferir se o M�dulo foi corretamente declarado no arquivo "/sei/config/ConfiguracaoSEI.php". Acesse o menu **Infra > M�dulos** e confira se consta a linha correspondente ao M�dulo, pois, realizando os passos anteriores da forma correta, independente da execu��o do script de banco, o M�dulo j� deve ser reconhecido na tela aberta pelo menu indicado.
5. Rodar o script de banco "/sip/scripts/sip_atualizar_versao_modulo_peticionamento.php" em linha de comando no servidor do SIP, verificando se n�o houve erro em sua execu��o, em que ao final do log dever� ser informado "FIM". Exemplo de comando de execu��o:

		/usr/bin/php -c /etc/php.ini /opt/sip/scripts/sip_atualizar_versao_modulo_peticionamento.php 2>&1 > atualizacao_peticionamento_sip.log

6. Rodar o script de banco "/sei/scripts/sei_atualizar_versao_modulo_peticionamento.php" em linha de comando no servidor do SEI, verificando se n�o houve erro em sua execu��o, em que ao final do log dever� ser informado "FIM". Exemplo de comando de execu��o:

		/usr/bin/php -c /etc/php.ini /opt/sei/scripts/sei_atualizar_versao_modulo_peticionamento.php 2>&1 > atualizacao_peticionamento_sei.log

7. **IMPORTANTE**: Na execu��o dos dois scripts de banco acima, ao final deve constar o termo "FIM", o "TEMPO TOTAL DE EXECU��O" e a informa��o de que a instala��o/atualiza��o foi realizada com sucesso na base de dados correspondente (SEM ERROS). Do contr�rio, o script n�o foi executado at� o final e algum dado n�o foi inserido/atualizado no respectivo banco de dados, devendo recuperar o backup do banco e repetir o procedimento.
   - Constando ao final da execu��o do script as informa��es indicadas, pode logar no SEI e SIP e verificar no menu **Infra > Par�metros** dos dois sistemas se consta o par�metro "VERSAO_MODULO_PETICIONAMENTO" com o valor da �ltima vers�o do m�dulo.
8. Em caso de erro durante a execu��o do script, verificar (lendo as mensagens de erro e no menu Infra > Log do SEI e do SIP) se a causa � algum problema na infraestrutura local ou ajustes indevidos na estrutura de banco do core do sistema. Neste caso, ap�s a corre��o, deve recuperar o backup do banco pertinente e repetir o procedimento, especialmente a execu��o dos scripts de banco indicados acima.
9. Ap�s a execu��o com sucesso, com um usu�rio com permiss�o de Administrador no SEI, seguir os passos dispostos no t�pico "Orienta��es Negociais" mais abaixo.

## Orienta��es Negociais
1. Imediatamente ap�s a instala��o com sucesso, com usu�rio com permiss�o de "Administrador" do SEI, acessar os menus de administra��o do M�dulo pelo seguinte caminho: Administra��o > Peticionamento Eletr�nico. Somente com tudo parametrizado adequadamente ser� poss�vel o uso do m�dulo pelos Usu�rios Externos por meio da tela de Acesso Externo do SEI:

		http://[Servidor_PHP]/sei/controlador_externo.php?acao=usuario_externo_logar&id_orgao_acesso_externo=0

2. O script de banco do SIP j� cria todos os Recursos e Menus e os associam automaticamente ao Perfil "B�sico" ou ao Perfil "Administrador".
	- Independente da cria��o de outros Perfis, os recursos indicados para o Perfil "B�sico" ou "Administrador" devem manter correspond�ncia com os Perfis dos Usu�rios internos que utilizar�o o M�dulo e dos Usu�rios Administradores do M�dulo.
	- O SIP n�o controla Perfil pr�prio para os Usu�rios Externos, cabendo diretamente ao c�digo do M�dulo o controle devido junto aos Recursos e Menus criados pelo M�dulo para os Usu�rios Externos.
	- T�o quanto ocorre com as atualiza��es do SEI, vers�es futuras deste M�dulo continuar�o a atualizar e criar Recursos e associ�-los apenas aos Perfis "B�sico" e "Administrador".
	- Todos os recursos do M�dulo iniciam pelo sufix **"md_pet_"**.
3. Acesse o [Manual do Webservice do M�dulo Peticionamento](https://github.com/anatelgovbr/mod-sei-peticionamento/blob/master/sei/web/modulos/peticionamento/ws/manual_ws_peticionamento.md).
4. Acesse o [Manual de Administra��o] http://bit.ly/SEI_Mod_Pet_Admin.
5. Acesse o [Manual do Usu�rio Interno] http://bit.ly/SEI_Mod_Pet_Interno.
6. Acesse o [Manual do Usu�rio Externo] http://bit.ly/SEI_Usuario_Externo.
	- N�o foi poss�vel fazer um Manual do Usu�rio Externo gen�rico para qualquer �rg�o, em raz�o das especificidades de cada �rg�o quanto aos procedimentos de credenciamento dos Usu�rios Externos e at� mesmo de parametriza��o do M�dulo. De qualquer forma, o Manual do Usu�rio Externo do SEI elaborado pela Anatel pode ser quase que completamente aproveitado.

## Erros ou Sugest�es
1. [Abrir Issue](https://github.com/anatelgovbr/mod-sei-peticionamento/issues) no reposit�rio do GitHub do m�dulo se ocorrer erro na execu��o dos scripts de banco do m�dulo no SEI ou no SIP acima.
2. [Abrir Issue](https://github.com/anatelgovbr/mod-sei-peticionamento/issues) no reposit�rio do GitHub do m�dulo se ocorrer erro na opera��o do m�dulo.
3. Na abertura da Issue utilizar o modelo **"1 - Reportar Erro"**.
