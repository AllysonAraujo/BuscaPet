# BuscaPet

Sistema de busca e gerenciamento de pets para adoção, desenvolvido em PHP com integração à API Petfinder.

## 🐾 Características

- **Sistema de Autenticação**: Login seguro com sessões PHP
- **CRUD Completo**: Gerenciamento completo de pets (Criar, Listar, Editar, Excluir)
- **Integração API Petfinder**: Busca de pets de organizações externas
- **Interface Responsiva**: Design moderno com Bootstrap 5
- **Banco de Dados MySQL**: Armazenamento seguro de dados
- **Dashboard Administrativo**: Painel de controle completo

## 🚀 Instalação

### Pré-requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor web (Apache/Nginx)
- Extensões PHP: PDO, cURL

### Passos de Instalação

1. **Clone o repositório**
   ```bash
   git clone https://github.com/AllysonAraujo/BuscaPet.git
   cd BuscaPet
   ```

2. **Configure o banco de dados**
   - Crie um banco MySQL chamado `buscapet_db`
   - Ajuste as configurações em `config/database.php` se necessário

3. **Execute o setup inicial**
   - Acesse `http://seudominio.com/setup.php` no navegador
   - Isso criará as tabelas e o usuário administrador padrão

4. **Configure a API Petfinder (Opcional)**
   - Registre-se em https://www.petfinder.com/developers/
   - Obtenha suas chaves API
   - Configure as variáveis de ambiente ou edite `config/petfinder_api.php`

## 🔑 Acesso Inicial

- **Usuário**: admin
- **Senha**: admin123

## 📁 Estrutura do Projeto

```
BuscaPet/
├── config/
│   ├── database.php          # Configuração do banco de dados
│   └── petfinder_api.php     # Configuração da API Petfinder
├── assets/
│   ├── css/
│   │   └── style.css         # Estilos personalizados
│   └── js/
│       └── main.js           # JavaScript principal
├── includes/
│   ├── auth.php              # Sistema de autenticação
│   ├── header.php            # Cabeçalho comum
│   └── footer.php            # Rodapé comum
├── api/
│   └── petfinder.php         # Interface para busca na API
├── admin/
│   ├── dashboard.php         # Painel administrativo
│   └── crud/
│       ├── create.php        # Cadastrar pets
│       ├── read.php          # Listar pets
│       ├── update.php        # Editar pets
│       └── delete.php        # Excluir pets
├── index.php                 # Página inicial
├── login.php                 # Página de login
└── setup.php                 # Script de instalação
```

## 🛠️ Funcionalidades

### Sistema de Autenticação
- Login/logout seguro
- Gerenciamento de sessões
- Proteção de páginas administrativas

### CRUD de Pets
- **Criar**: Cadastro de novos pets com informações completas
- **Listar**: Visualização de todos os pets com paginação e filtros
- **Editar**: Atualização de informações dos pets
- **Excluir**: Remoção segura com confirmação

### API Petfinder
- Busca de pets em organizações externas
- Filtros por tipo, localização e nome
- Exibição de informações detalhadas
- Contato direto com organizações

### Dashboard Administrativo
- Estatísticas do sistema
- Ações rápidas
- Pets recentes
- Status do sistema

## 🎨 Interface

- Design responsivo com Bootstrap 5
- Ícones Font Awesome
- Tema personalizado
- Experiência mobile-friendly

## 🔧 Configuração Avançada

### Banco de Dados
Edite `config/database.php` para ajustar:
- Host do banco
- Nome do banco
- Usuário e senha
- Configurações de conexão

### API Petfinder
Configure em `config/petfinder_api.php`:
- API Key
- Secret Key
- URL base (já configurada)

### Variáveis de Ambiente
Para produção, configure:
```bash
PETFINDER_API_KEY=sua_api_key
PETFINDER_SECRET=seu_secret
```

## 🐛 Solução de Problemas

### Erro de Conexão com Banco
- Verifique se o MySQL está rodando
- Confirme as credenciais em `config/database.php`
- Certifique-se de que o banco `buscapet_db` existe

### API Petfinder Não Funciona
- Dados de demonstração são exibidos se a API não estiver configurada
- Configure as chaves da API para funcionalidade completa
- Verifique se o cURL está habilitado no PHP

### Problemas de Permissão
- Verifique permissões de escrita para logs
- Configure o servidor web adequadamente

## 📝 Uso

1. **Acesso Inicial**: Entre com admin/admin123
2. **Cadastrar Pets**: Use o menu Dashboard → Novo Pet
3. **Gerenciar Pets**: Visualize, edite ou exclua pets existentes
4. **Buscar Externamente**: Use a funcionalidade de busca da API
5. **Administrar**: Monitore estatísticas no dashboard

## 🤝 Contribuição

1. Faça um fork do projeto
2. Crie sua feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para detalhes.

## 📞 Suporte

Para suporte, abra uma issue no GitHub ou entre em contato através do e-mail do projeto.

---

Desenvolvido com ❤️ para ajudar pets a encontrar seus lares.