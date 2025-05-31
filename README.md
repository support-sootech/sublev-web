
# OOTech

Sistema de controle e rastreabilidade de produtos com emissão de etiquetas.


## Referência

 - [PHP v8.3.0](https://www.php.net/)
 - [Slim Framework v2](https://www.slimframework.com/docs/v2/)
 - [Bootstrap v5.3.3](https://getbootstrap.com/)
 - [jQuery v3.6](https://ootech.com.br/layout/vendor/jquery/jquery.min.js)
 - [Ícones fontawesome](https://fontawesome.com/v5/search?o=r&m=free)
 - [jQuery Form Plugin](https://ootech.com.br/layout/js/jquery.form.js)
 - [DataTable v2.0.3](https://cdn.datatables.net/2.0.3/js/dataTables.js)
 - [jQuery Mask v1.14.16](https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.js)


## Serviço de impressão

Utilizando:
 
 - nodejs versão 20.16.0
 - npm versão 10.9.0

Controle de start do serviço de impressão
 - pacote pm2 (node) referência: [PM2 - Código Fonte](https://www.youtube.com/watch?v=zi8qHEL-Ilk)

Será necessário executavél na maquina do cliente para receber a impressão (WebSocketServer)


## Ambiente de desenvolvimento local - [Docker Desktop](https://www.docker.com/products/docker-desktop/)

### Arquivos no diretório raiz do projeto
 - apache-conf
 - docker-compose.yaml
 - Dockerfile

### Iniciar o projeto
- A primeira vez do Build da imagem: demora um pouco para baixar a imagem e configurar, com esse comando a primeira vez já iniciar o container
```
docker-compose down && docker-compose up -d --build
```

- Encerrar os containers por terminal: 
```
docker-compose down
```

- Iniciar 
```
docker-compose up -d
```

- Está configurado para porta 8000 (http://localhost:8000)
- Banco de dados está fazendo a conexão a produção
