# BuscaPet

Sistema completo de busca e adoção de pets com funcionalidades modernas e design responsivo.

## 🐾 Características

### Sistema de Autenticação
- ✅ Login e registro de usuários
- ✅ Proteção de senhas com hash (bcrypt)
- ✅ Sistema de sessões seguro
- ✅ Validação de formulários

### Banco de Dados
- ✅ Tabelas: users, pets, favorites, logs
- ✅ Relacionamentos bem estruturados
- ✅ Conexão segura com PDO
- ✅ Sistema de logs de atividades

### Frontend Responsivo
- ✅ Design moderno com Bootstrap 5
- ✅ Interface intuitiva e amigável
- ✅ Busca e filtros dinâmicos
- ✅ Sistema de paginação
- ✅ Totalmente responsivo

### Funcionalidades dos Pets
- ✅ CRUD completo (Create, Read, Update, Delete)
- ✅ Upload de imagens
- ✅ Sistema de favoritos
- ✅ Busca por nome, raça e espécie
- ✅ Filtros por espécie

### APIs RESTful
- ✅ Endpoints para autenticação
- ✅ APIs para gerenciamento de pets
- ✅ Sistema de favoritos via API
- ✅ Integração com Petfinder API (com fallback)

### Segurança e Qualidade
- ✅ Validação de dados
- ✅ Prevenção de SQL Injection
- ✅ Sanitização de inputs
- ✅ Sistema de logs
- ✅ Mensagens de feedback

## 🚀 Instalação

### Pré-requisitos
- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor web (Apache/Nginx)

### Passo a passo

1. **Clone o repositório**
   ```bash
   git clone https://github.com/AllysonAraujo/BuscaPet.git
   cd BuscaPet
   ```

2. **Configure o banco de dados**
   - Crie um banco de dados MySQL chamado `buscapet`
   - Execute o script SQL em `config/schema.sql`
   ```sql
   mysql -u root -p < config/schema.sql
   ```

3. **Configure a conexão com o banco**
   - Edite `config/database.php` com suas credenciais:
   ```php
   private $host = 'localhost';
   private $db_name = 'buscapet';
   private $username = 'seu_usuario';
   private $password = 'sua_senha';
   ```

4. **Configure permissões**
   ```bash
   chmod 755 assets/images/pets/
   chmod 644 config/database.php
   ```

5. **Acesse o sistema**
   - Coloque os arquivos no diretório do servidor web
   - Acesse via navegador: `http://localhost/BuscaPet`

### Usuários padrão
- **Admin**: usuario: `admin`, senha: `password`
- **Usuário**: usuario: `usuario1`, senha: `password`

## 📁 Estrutura do Projeto

```
BuscaPet/
├── api/                    # APIs RESTful
│   ├── auth.php           # Autenticação
│   ├── pets.php           # Gerenciamento de pets
│   ├── favorites.php      # Sistema de favoritos
│   └── profile.php        # Perfil do usuário
├── assets/                # Recursos estáticos
│   ├── css/style.css      # Estilos personalizados
│   ├── js/main.js         # JavaScript principal
│   └── images/            # Imagens do sistema
├── config/                # Configurações
│   ├── database.php       # Conexão com banco
│   └── schema.sql         # Schema do banco
├── src/                   # Classes PHP
│   ├── Auth.php           # Classe de autenticação
│   ├── Pet.php            # Classe de pets
│   ├── Favorite.php       # Classe de favoritos
│   └── PetfinderAPI.php   # Integração Petfinder
├── index.php              # Página inicial
├── login.php              # Página de login
├── register.php           # Página de cadastro
├── add-pet.php           # Adicionar pet
├── edit-pet.php          # Editar pet
├── my-pets.php           # Meus pets
├── favorites.php         # Favoritos
├── profile.php           # Perfil do usuário
└── README.md             # Este arquivo
```

## 🔧 Funcionalidades Técnicas

### Tecnologias Utilizadas
- **Backend**: PHP 7.4+, MySQL, PDO
- **Frontend**: HTML5, CSS3, JavaScript ES6, Bootstrap 5
- **APIs**: RESTful, JSON
- **Segurança**: Password hashing, SQL prepared statements

### Recursos Implementados
1. **Autenticação completa** com hash de senhas
2. **CRUD de pets** com upload de imagens
3. **Sistema de favoritos** dinâmico
4. **Busca e filtros** em tempo real
5. **Paginação** eficiente
6. **Design responsivo** para mobile
7. **Sistema de logs** para auditoria
8. **Validação** client-side e server-side
9. **APIs RESTful** bem estruturadas
10. **Integração externa** (Petfinder API)

## 🎨 Screenshots

O sistema possui uma interface moderna e responsiva com:
- Página inicial com busca avançada
- Cards de pets atraentes
- Sistema de favoritos intuitivo
- Painel administrativo para pets
- Formulários validados
- Design mobile-first

## 🤝 Contribuição

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📝 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para detalhes.

## 👨‍💻 Autor

**Allyson Araujo**
- GitHub: [@AllysonAraujo](https://github.com/AllysonAraujo)

---

⭐ Se este projeto te ajudou, não esqueça de dar uma estrela!