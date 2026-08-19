# Vega WebPonto (InNOut PHP Modificado)

Sistema de Ponto Eletrônico InNOut, modificado exclusivamente para o **Grupo Vega**.

## Principais Modificações Realizadas

1.  **Remoção de Almoço (4 batimentos para 2)**
    - O sistema agora exige apenas 1 Entrada e 1 Saída diárias (sem pausa para o almoço no sistema).
2.  **Captura de IP e Hostname Real**
    - A captura via IP público (geolocalização externa) e botão de pausa foram removidos.
    - O sistema agora captura o IP interno (`10.x.x.x` ou `192.168.x.x`) e resolve o Hostname local da máquina que está batendo o ponto. O formato salvo no relatório mensal é `IP LOCAL - HOSTNAME`.
3.  **Bloqueio de Horário Manual (Forced Time)**
    - O funcionário não pode mais editar o horário do ponto. O sistema força e utiliza exclusivamente a hora do servidor (Linux/PHP).
4.  **Layout e Identidade Visual (Dark Theme)**
    - Logo do Grupo Vega adicionada ao painel de login e ao cabeçalho interno.
    - Interface adaptada para **Tema Escuro (Dark Theme)**.
    - Efeito *Glassmorphism* (fundo desfocado) e um vídeo institucional em loop como plano de fundo da página de login.
    - A tabela de relatório mensal foi mantida na cor branca para facilitar a leitura e impressão.
    - O campo "E-mail" no login foi alterado visualmente para "Usuário".
5.  **Integração com LDAP (Active Directory)**
    - Autenticação direta no Active Directory da empresa.
    - Ao invés de checar a senha localmente, o PHP faz o *bind* diretamente com o AD (`vega.local`).
    - Se o usuário não existir na base de dados do Ponto (primeiro acesso), o sistema busca o Nome no AD e **cria o perfil automaticamente**, sem necessidade de cadastro manual prévio.

## Como Instalar do Zero (Guia de Reinstalação)

Se você precisar subir este sistema em um novo servidor Rocky/Alma/CentOS Linux, siga os passos abaixo:

### 1. Pré-Requisitos do Servidor

```bash
# Atualize o sistema e instale o EPEL/Remi para PHP 8.3
dnf update -y
dnf install -y epel-release
dnf install -y https://rpms.remirepo.net/enterprise/remi-release-10.rpm
dnf module reset php -y
dnf module enable php:remi-8.3 -y

# Instalar Apache, MariaDB e PHP
dnf install -y httpd mariadb-server mariadb
dnf install -y php php-cli php-fpm php-mysqlnd php-pdo php-gd php-mbstring php-ldap php-zip
```

### 2. Configurar o Banco de Dados

```bash
systemctl enable --now mariadb
mysql -u root

# Dentro do MySQL:
CREATE DATABASE INNOUT;
CREATE USER 'innout'@'localhost' IDENTIFIED BY 'innout';
GRANT ALL PRIVILEGES ON INNOUT.* TO 'innout'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```
Restaure o esquema do banco de dados (que está na pasta do projeto original) ou copie os arquivos deste backup que já estão com as tabelas do sistema.

### 3. Configurar Apache e Permissões

1. Mova todos os arquivos deste repositório para `/var/www/html/`
2. Garanta que o Apache tem permissão de leitura e gravação nas pastas necessárias:
```bash
chown -R apache:apache /var/www/html/
```
3. Edite o `/etc/httpd/conf/httpd.conf` para permitir `.htaccess`. Mude o `AllowOverride None` para `AllowOverride All` dentro do `<Directory "/var/www/html">`.
4. Ative os serviços no boot:
```bash
systemctl enable --now httpd
systemctl enable --now php-fpm
```

### 4. Firewall e SELinux
```bash
firewall-cmd --permanent --add-service=http
firewall-cmd --reload
# Se o SELinux estiver bloqueando o acesso LDAP ou de rede:
setsebool -P httpd_can_network_connect 1
```

## Arquivos Mais Importantes Alterados

- `src/controllers/innout.php`: Contém a lógica nova de captura de IP Local e Hostname.
- `src/models/WorkingHours.php`: Ajustado para apenas 2 batimentos.
- `src/views/day_records.php`: Remoção da interface de entrada 2 e saída 2.
- `src/models/Login.php`: Contém a lógica de autenticação **LDAP/Active Directory** e fallback local.
- `src/views/login.php` e `public/assets/css/template.css`: Contém a customização em Dark Mode e vídeo de background.
