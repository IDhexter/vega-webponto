# Vega WebPonto

Sistema  de controle de ponto e gestão de horas trabalhas, desenvolvido para atender a Vegacon, com suporte a Escalas Mistas e Integração com Active Directory.


## Principais Funcionalidades

- **Autenticação LDAP (Active Directory):** Os funcionários acessam o sistema usando as mesmas credenciais do Windows. O perfil do usuário é criado automaticamente no primeiro acesso.
- **Batimento Flexível (Escalas Mistas):** Focado em cenários reais, exige apenas 1 Entrada e 1 Saída por dia. Dias não trabalhados não geram saldo negativo ("dívidas").
- **Auditoria de Localização (IP e GPS):**
  - Captura o IP Interno e resolve o **Hostname** da máquina no momento do batimento.
  - Registra separadamente o IP da Entrada e o IP da Saída (ex: `Ent: 10.x.x.x - HOST1 | Sai: 10.x.x.y - HOST2`).
  - **Geolocalização Satélite:** Se acessado via HTTPS, captura as coordenadas GPS (Latitude e Longitude) e as anexa ao IP para verificação no Google Maps.
- **Painel Gerencial ao Vivo:** Dashboard exclusivo para administradores, exibindo:
  - Funcionários operando em tempo real (Trabalhando Agora).
  - Alertas automáticos de Ponto Incompleto (pessoas que não registraram a saída em dias anteriores).
  - Resumo imediato de horas mensais.
- **Edição Direta e "Planilha Inteligente":** Administradores podem corrigir horários de funcionários diretamente na tela de Relatório Mensal através de campos editáveis (estilo Excel), com recálculo imediato de horas.
- **Interface Dark Mode e Vanguarda:** Tema escuro consistente ("Pixel Perfect"), botões padronizados e vídeo institucional no fundo da tela de login.

---

## Instalação Passo a Passo

O sistema é homologado para servidores Linux (ex: Rocky Linux, AlmaLinux, CentOS) rodando Apache, PHP 8.3 e MariaDB.

### 1. Preparação do Servidor

```bash
# Atualize o sistema e instale os repositórios EPEL/Remi (para PHP 8.3)
dnf update -y
dnf install -y epel-release
dnf install -y https://rpms.remirepo.net/enterprise/remi-release-10.rpm
dnf module reset php -y
dnf module enable php:remi-8.3 -y

# Instalar Servidor Web, Banco de Dados e PHP
dnf install -y httpd mariadb-server mariadb
dnf install -y php php-cli php-fpm php-mysqlnd php-pdo php-gd php-mbstring php-ldap php-zip
```

### 2. Configuração do Banco de Dados

```bash
# Inicie o banco de dados
systemctl enable --now mariadb

# Crie a estrutura de dados inicial
mysql -u root

CREATE DATABASE INNOUT;
CREATE USER 'seu_usuario'@'localhost' IDENTIFIED BY 'sua_senha_segura';
GRANT ALL PRIVILEGES ON INNOUT.* TO 'seu_usuario'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

> **Atenção:** Após criar o banco, é necessário importar o esquema de tabelas (arquivo `.sql` disponibilizado separadamente) para criar as tabelas `users` e `working_hours`.

### 3. Configurando o Ambiente e Deploy

1. Envie todos os arquivos deste repositório para o diretório raiz do Apache: `/var/www/html/`
2. **Configuração do `.env`:** 
   - Copie o arquivo `src/env.sample.ini` para `src/env.ini`.
   - Preencha suas credenciais do Banco de Dados e as informações do seu Servidor Active Directory.
3. **Permissões Corretas:**
   ```bash
   chown -R apache:apache /var/www/html/
   ```
4. **Apache e Serviços:**
   - Edite o `/etc/httpd/conf/httpd.conf` para permitir `.htaccess`. Mude o `AllowOverride None` para `AllowOverride All` dentro do `<Directory "/var/www/html">`.
   - Ative os serviços:
   ```bash
   systemctl enable --now httpd
   systemctl enable --now php-fpm
   ```

### 4. Resolução de Nomes e Rede

- Libere o Apache no Firewall local:
  ```bash
  firewall-cmd --permanent --add-service=http
  firewall-cmd --reload
  ```
- Para a autenticação LDAP (Active Directory) funcionar no CentOS/Rocky, libere a política do SELinux:
  ```bash
  setsebool -P httpd_can_network_connect 1
  ```
- Para a captura de Hostnames via IP funcionar, certifique-se de que o servidor Linux utilize o DNS primário da rede local (ex: o próprio AD). Edite as configurações de rede via `nmcli` ou `/etc/resolv.conf`.
