# Projeto de implementação de logoff no Captive Portal do firewall da Palo Alto

### Palo Alto
Palo Alto é uma empresa que atua no mercado oferecendo soluções na áre de segurança cibernética. Um de seus produtos é o Firewall de última geração. Para mais informações, acesse : 

https://www.paloaltonetworks.com

### Autenticação Captive Portal
O PAN-OS, sistema operacional do firewall da Palo Alto, oferece algumas formas de controlar autenticação na rede. Uma delas é utilizando a autenticação via **Captive Portal** que trabalha com requisição web, normalmente usada em organizações que não possuem AD (Active Directory).

Essa autenticação não possui, nativamente, uma solução para desconectar o usuário de sua sessão, o que pode ocorrer problemas em instituições que possuem terminais públicos e compartilháveis.

### Proposta de Logoff para o Captive Portal
De forma a contonar o problema anteriormente descrito, criamos uma solução que acessa a API do PAN-OS que possuem comandos que possibilitam remover um usuário de sua sessão. 
Para isso, estudamos a API ( https://docs.paloaltonetworks.com/pan-os/7-1/pan-os-panorama-api/get-started-with-the-pan-os-xml-api/explore-the-api ) e verificamos que dois comandos podem ser executados para a remoçao de um usuário de sua sessão:

IMAGEM DE COMANDOS

Em **type=op** informa que o tipode de requisição é *operation*. O parâmetro **key=key** recebe a key da API gerada, de preferencia gerada por um usuário com permissões mínimas. E por último os comandos atribuídos para **cmd=**. O comando executa a desconexão do IP de número informado em **ipnumber**.
  
### Funcionalidades

- Capta IP da rede interna do usuário que executa o *logoff*.
- Com o IP executa a requisição via API solicitando o *logoff* daquele IP.

### Configurações

 - O arquivo de configurações config.ini possui as variáveis para informar a API key e o IP da caixa do palo alto na qual ocorre o login.
 - Algumas exceções podem ocorrer em caso de organizações separadas geograficamente e conectadas via VPN, com caixas do firewall separadas. Nesse caso, pode verificar o código comentando no arquivo desconecta.php, analisar e adequar de acordo com a necessidade.


