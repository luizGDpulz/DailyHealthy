# DailyHealthy - Sistema de Hábitos Gamificado

DailyHealthy é um sistema web completo para gerenciamento gamificado de hábitos saudáveis, desenvolvido com PHP, MySQL e Tailwind CSS.

## 🚀 Características

- ✅ Sistema de login/cadastro seguro
- ✅ CRUD completo de hábitos
- ✅ Gamificação com pontos e badges
- ✅ Sistema de streaks
- ✅ Ranking global
- ✅ Dashboard interativo
- ✅ Calendário de atividades
- ✅ Interface responsiva
- ✅ Dark mode

## 📋 Requisitos

- PHP 8+
- MySQL 8.0+
- Apache/Nginx
- Composer (opcional)

## 🛠️ Instalação

1. Clone o repositório:
```bash
git clone https://github.com/seu-usuario/DailyHealthy.git
```

2. Configure o banco de dados:
   - Crie um banco de dados MySQL
   - Configure as credenciais em `config/config.php`

3. Execute as migrations:
```bash
php migrations/migrate.php
```

4. Configure o servidor web:
   - Aponte o DocumentRoot para a pasta `public/`
   - Ou use o servidor embutido do PHP:
```bash
php -S localhost:8000 -t public
```

5. Acesse:
```
http://localhost:8000
```

## 👤 Login Padrão

- Email: admin@dailyhealthy.com
- Senha: admin123

## 🎮 Sistema de Pontuação

- **Pontos base**: 5-20 pontos por hábito
- **Bônus de streak**: +2 pontos por dia consecutivo
- **Multiplicadores por nível**:
  - Bronze: 1x
  - Prata: 1.2x
  - Ouro: 1.5x
  - Diamante: 2x

## 🏆 Conquistas

- Primeiro Passo: Complete seu primeiro hábito
- Uma Semana!: Mantenha uma streak de 7 dias
- Disciplinado: Mantenha uma streak de 30 dias
- Criativo: Crie 10 hábitos diferentes
- Milionário: Acumule 1000 pontos

## 💻 Estrutura do Projeto

```
DailyHealthy/
│
├── config/
│   └── config.php              # Configurações do banco
│
├── migrations/
│   ├── 001_create_users.php
│   ├── 002_create_habits.php
│   └── ...
│
├── app/
│   ├── models/
│   ├── controllers/
│   └── helpers/
│
└── public/
    ├── assets/
    │   ├── css/
    │   ├── js/
    │   └── images/
    └── index.php
```

## 🔒 Segurança

- Senhas hash com `password_hash()`
- Proteção CSRF
- Sanitização de inputs
- XSS Prevention
- SQL Injection Prevention
- Rate Limiting

## 📱 Responsividade

O sistema é totalmente responsivo, adaptando-se a diferentes tamanhos de tela:
- Desktop
- Tablet
- Mobile

## 🎨 Temas

- Light Mode (padrão)
- Dark Mode (opcional)

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## ✨ Contribuição

1. Fork o projeto
2. Crie sua Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a Branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request
