# Módulo Peticionamento e Intimação Eletrônicos

## Requisitos:
- SEI 3.0.2 instalado/atualizado ou versão superior (verificar valor da constante de versão do SEI no arquivo /sei/web/SEI.php).
	- **IMPORTANTE**, no caso de atualização do presente módulo: A atualização do SEI 2.6 para 3.0 alterou diversas tabelas que as tabelas do módulo relacionava. Dessa forma, alertamos que, imediatamente ANTES de executar o script de atualização do SEI 3.0 é necessário executar o script abaixo no banco do SEI para que a atualização do SEI 3.0 possa ocorrer sem erro:
		
		ALTER TABLE `md_pet_rel_tp_ctx_contato` DROP FOREIGN KEY `fk_md_pet_rel_tp_ctx_cont_1`;
		
- Antes de executar os scripts de instalação/atualização (itens 4 e 5 abaixo), o usuário de acesso aos bancos de dados do SEI e do SIP, constante nos arquivos ConfiguracaoSEI.php e ConfiguracaoSip.php, deverá ter permissão de acesso total ao banco de dados, permitindo, por exemplo, criação e exclusão de tabelas.

## Procedimentos para Instalação:

1. Antes, fazer backup dos bancos de dados do SEI e do SIP.

2. Carregar no servidor os arquivos do módulo localizados na pasta "/sei/web/modulos/peticionamento" e os scripts de instalação/atualização "/sei/scripts/sei_atualizar_versao_modulo_peticionamento.php" e "/sip/scripts/sip_atualizar_versao_modulo_peticionamento.php".

3. Editar o arquivo "/sei/config/ConfiguracaoSEI.php", tomando o cuidado de usar editor que não altere o charset do arquivo, para adicionar a referência à classe de integração do módulo e seu caminho relativo dentro da pasta "/sei/web/modulos" na array 'Modulos' da chave 'SEI':

		'SEI' => array(
			'URL' => 'http://[Servidor_PHP]/sei',
			'Producao' => false,
			'RepositorioArquivos' => '/var/sei/arquivos',
			'Modulos' => array('PeticionamentoIntegracao' => 'peticionamento',)
			),

4. Rodar o script de banco "/sei/scripts/sei_atualizar_versao_modulo_peticionamento.php" em linha de comando no servidor do SEI, verificando se não houve erro em sua execução, em que ao final do log deverá ser informado "FIM". Exemplo de comando de execução:

		/usr/bin/php -c /etc/php.ini /opt/sei/scripts/sei_atualizar_versao_modulo_peticionamento.php > atualizacao_modulo_peticionamento_sei.log

5. Rodar o script de banco "/sip/scripts/sip_atualizar_versao_modulo_peticionamento.php" em linha de comando no servidor do SIP, verificando se não houve erro em sua execução, em que ao final do log deverá ser informado "FIM". Exemplo de comando de execução:

		/usr/bin/php -c /etc/php.ini /opt/sip/scripts/sip_atualizar_versao_modulo_peticionamento.php > atualizacao_modulo_peticionamento_sip.log

6. Após a execução com sucesso, com um usuário com permissão de Administrador no SEI, seguir os passos dispostos no tópico Orientações Negociais, abaixo.

7. **IMPORTANTE**: Na execução dos dois scripts acima, ao final deve constar o termo "FIM". Do contrário, o script não foi executado até o final e algum dado não foi inserido/atualizado no banco de dados correspondente, devendo recuperar o backup do banco pertinente e repetir o procedimento.
		- Constando o termo "FIM" ao final da execução significa que foi executado com sucesso. Verificar no SEI e no SIP no menu Infra > Parâmetros se consta o parâmetro "VERSAO_MODULO_PETICIONAMENTO" com o valor da última versão do módulo.

8. Em caso de erro durante a execução do script verificar (lendo as mensagens de erro e no menu Infra > Log do SEI e do SIP) se a causa é algum problema na infra-estrutura local. Neste caso, após a correção, deve recuperar o backup do banco pertinente e repetir o procedimento, especialmente a execução dos scripts indicados nos itens 4 e 5 acima.
	- Caso não seja possível identificar a causa, entrar em contato com: Nei Jobson - neijobson@anatel.gov.br

## Orientações Negociais:

1. Imediatamente após a instalação com sucesso, com usuário com permissão de "Administrador" do SEI, é necessário realizar as parametrizações do módulo no menu Administração > Peticionamento Eletrônico, para que o módulo seja utilizado adequadamente pelos Usuários Externos na tela de Acesso Externo do SEI:

		http://[Servidor_PHP]/sei/controlador_externo.php?acao=usuario_externo_logar&id_orgao_acesso_externo=0

2. Ainda com usuário com permissão de "Administrador" do SEI, é necessário cadastrar os "Cargos", "Tratamentos", "Vocativos" e "Tipos" no menu Administração > Contatos.
	- Os "Cargos" serão utilizados pelos Usuários Externos na seleção do "Cargo/Função" na assinatura de cada Peticionamento e também no cadastro de novos Interessados.
	- Os demais registros acima serão utilizados no cadastro de novos Interessados pelos Usuários Externos.
	- Caso a instalação do SEI do órgão não possua parametrizações acima, sugerimos como exemplo a lista disponibilizada no link a seguir: https://goo.gl/NqikRu

3. Outro ponto importante é a parametrização do menu Administração > Peticionamento Eletrônico > Hipóteses Legais Permitidas. Contudo, antes, exige que no menu Administração > Hipóteses Legais > Lista (core do SEI) já contenha uma lista bem definida de uso pelo órgão, com todas as opções legais existentes aplicáveis, inclusive em razão de Lei próprio do órgão, pois, na medida que se disponibiliza as Hipóteses aplicáveis, melhor serão as indicações de Restrição segundo opção legal própria.
	- Caso a instalação do SEI do órgão não possua lista de Hipóteses Legais em uso ou a lista tenha poucas opções, sugerimos como exemplo a lista disponibilizada no link a seguir: https://goo.gl/JzycpM

4. Destacamos que a janela de Cadastro de Interessado na tela de Peticionamento de Processo Novo é aberta ao Validar CPF ou CPNJ em duas situações: (i) quando o CPF ou CNPJ não existir na tabela "contato" no banco do SEI ou (ii) quando existir mais de um registro na referida tabela com o mesmo CPF ou CNPJ. A segunda regra visa a priorizar o cadastro novo feito por meio do módulo pelo próprio Usuário Externo, que geralmente possui mais dados sobre o Interessado.
	- **IMPORTANTE**: sugere-se que o órgão faça uma extração da tabela "contato" e faça análises para levantar os cadastros com CPF ou CNPJ duplicados, para resolver as duplicações, mantendo um só cadastro por CPF ou CNPJ.

5. Peticionamento Intercorrente:
	- Os Usuários Externos somente visualizarão o menu Peticionamento > Intercorrente depois que na Administração for configurado pelo menos o "Intercorrente Padrão".
	- A abertura de processo novo relacionado ao processo de fato indicado pelo Usuário Externo ocorrerá quando este corresponder a processo: 1) de Tipo de Processo sem Critério Intercorrente parametrizado; 2) com Nível de Acesso "Sigiloso"; 3) Sobrestado, Anexado ou Bloqueado; ou 4) de Tipo de Processo desativado.
		- Em todos os casos acima a forma de indicação de Nível de Acesso pelo Usuário Externo será a indicada em Administração > Peticionamento Eletrônico > Critérios para Intercorrente > botão "Intercorrente Padrão". Somente no caso 4 é que o Tipo de Processo também será o indicado para "Intercorrente Padrão".
	- Se TODAS as Unidades por onde o processo indicado tenha tramitado estiverem Desativadas no SEI, o Usuário Externo será avisado que o Peticionamento Intercorrente não é possível e que deverá utilizar a funcionalidade de Peticionamento de Processo Novo.

6. Não foi possível fazer um Manual do Usuário Externo genérico para qualquer órgão, em razão das especificidades de cada órgão quanto aos procedimentos de credenciamento dos Usuários Externos e até mesmo de parametrização do Módulo. De qualquer forma, segue link para o Manual do Usuário Externo do SEI elaborado pela Anatel que pode ser quase que completamente aproveitado para elaboração de outros Manuais: https://goo.gl/eyJr12

7. Acesse o Manual de Administração do Módulo (ainda em construção): https://goo.gl/pqIoZY

8. Ainda, conforme pode ser observado no Manual do Usuário Externo disponibilizado no item 7 acima, é extremamente recomendado que o órgão tenham bem definido procedimento para cadastro e liberação de Usuários Externos no SEI, preferencialmente com assinatura e entrega de "Termo de Declaração de Concordância e Veracidade" e mais um documento que contenha número de CPF.
	- Segue link para exemplo de Termo que pode ser utilizado, desde que ajuste a indicação do endereço para envio da correspondência. Sugerimos que seja disponibilizado em formato PDF: https://docs.google.com/document/d/1ZvoDA5Jpx2VwNKvva6V9v3d3j7hQdbJUXBJ81wEbITg/edit?usp=sharing

9. Por fim, não é aconselhável dar publicidade a registros de indisponibilidades do SEI até que o módulo possua funcionalidades afetas a Intimação Eletrônica, prevista para a versão 2.0. De qualquer forma, segue URL da página pública que lista os cadastrados realizados no menu Administração > Peticionamento Eletrônico > Indisponibilidades do SEI:

		http://[Servidor_PHP]/sei/modulos/peticionamento/md_pet_usu_ext_indisponibilidade_lista.php?acao_externa=md_pet_usu_ext_indisponibilidade_listar&id_orgao_acesso_externo=0