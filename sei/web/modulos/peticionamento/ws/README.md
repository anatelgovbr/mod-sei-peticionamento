# Módulo Webservice Peticionamento do SEI

## Requisitos
- SEI 3.1.3 instalado/atualizado.
   - Verificar valor da constante de versão no arquivo /sei/web/SEI.php ou, após logado no sistema, parando o mouse sobre a logo do SEI no canto superior esquerdo.
- Os códigos-fonte do Módulo podem ser baixados a partir do link a seguir, devendo sempre utilizar a versão mais recente: [https://softwarepublico.gov.br/gitlab/anatel/mod-sei-wspeticionamento/tags](https://softwarepublico.gov.br/gitlab/anatel/mod-sei-wspeticionamento/tags "Clique e acesse")

## Procedimentos para Instalação
1. Carregar no servidor os arquivos do módulo nas pastas correspondentes nos servidores do SEI e do SIP.
2. Editar o arquivo "/sei/config/ConfiguracaoSEI.php", tomando o cuidado de usar editor que não altere o charset do arquivo, para adicionar a referência à classe de integração do módulo e seu caminho relativo dentro da pasta "/sei/web/modulos" na array 'Modulos' da chave 'SEI':

		'SEI' => array(
			'URL' => 'http://[Servidor_PHP]sei',
			'Producao' => false,
			'RepositorioArquivos' => '/var/sei/arquivos',
			'Modulos' => array(
			        'PeticionamentoIntegracao' => 'peticionamento',
			    )
			),

3. O endereço do WSDL do módulo é o seguinte: http://[dominio_servidor]/sei/controlador_ws.php?servico=wspeticionamento
4. Manual e demais orientações constam na Wiki do Projeto Principal: https://softwarepublico.gov.br/gitlab/anatel/mod-sei-wspeticionamento/wikis/home
5. O projeto desse módulo é de desenvolvimento colaborativo, devendo seguir a metodologia definida, especialmente abrindo Issue antes de qualquer desenvolvimento. Link para o projeto principal: https://softwarepublico.gov.br/gitlab/anatel/mod-sei-wspeticionamento