
# Benjamin Connect

Benjamin é uma plataforma que aproxima pequenos artistas de restaurantes, bares, casas de show e organizadores de eventos. Nosso app permite que artistas ofereçam seus serviços como freelancers, e que estabelecimentos encontrem talento de forma rápida, prática e personalizada, criando oportunidades para todos brilharem.

## Instalação do Docker (Pré requisito)
Sua maquina local precisa ter instalado o Docker Desktop para conseguir rodar o projeto.

 - [Site para instalar o Docker Desktop](https://www.docker.com/get-started)

### Passos para instalação

- Acesse o link acima e na sessão "**Get Started with Docker**".
- Clique no botão "**Download Docker Desktop**.
- Selecione a opção "**Download for Windows – ARM64**".
- O download será iniciado.

### **Atenção**
**Após instalação sera necessário criar uma conta para usar o Docker**.
Durante o processo de instalação será criada esta conta.

## Teste Docker

Para testar se o Docker instalou corretamente abra o seu terminal (CMD) e digite:

```bash
  docker -v
```
e será exibido a versão do Docker.  

## Clone do Projeto
Agora vamos trazer o projeto Benjamin Connect para dentro da sua maquina. Abra uma pasta no seu VS Code, acesse o terminal e digite:
```bash
  git clone https://github.com/Moisesvictors/BenjaminConnect.git
```

Entre no diretório do projeto

```bash
  cd BenjaminConnect
```


## Variáveis de Ambiente

Para seu container com o Mysql funcionar você precisa rodar o seguinte comando:

```bash
  copy .env.example .env
```
isso irá criar um arquivo chamado .env na sua pasta e copiar todo o conteúdo de .env.example para dentro dele.  
### Arquivo .env
Agora na sua pasta do projeto, terá sido criado um arquivo com o nome .env, ele armazena as credenciais com senha e usuario para o banco de dados. Altere apenas **ESTE** arquivo conforme o passo a passo contido nele.

**Atenção**  
NUNCA altere o arquivo .env.example, ele é apenas um molde para a configuração das variáveis de ambiente utilizadas pelo Docker, e é totalmento publico.


## Subir Containers
Depois de alterar o arquivo .env, o projeto já esta pronto para rodar. Deixe o aplicativo Docker Desktop aberto e siga o seguinte passo:

- Com o VS Code aberto, digite o seguinte comando no terminal:

```bash
  docker compose up -d
```
Neste momento o Docker irá baixar os arquivos necessarios para rodar e deixar a aplicação pronta para ser acessada pelo navegador.
## Passo Final
Depois de subir os containers, abra seu navegador e acesse o **localhost** na barra de pesquisa. Será exibido a pagina index.hmtl do projeto.