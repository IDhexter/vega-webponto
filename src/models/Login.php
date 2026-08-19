<?php

class Login extends Model {
    public function validate() {
        $errors = [];

        if (!$this->email) {
            $errors['email'] = 'Usuário é um campo obrigatório.';
        }

        if (!$this->password) {
            $errors['password'] = 'Por favor, informe a senha.';
        }

        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }
    }

    public function checkLogin() {
        $this->validate();

        $username = $this->email;
        $password = $this->password;
        
        // Se o usuário não digitou o @, assumimos o domínio da rede
        if (strpos($username, '@') === false) {
            $username .= '@vega.local';
        }

        // 1. Tentar LDAP
        $ldap_host = "10.10.1.5";
        $ldap_base_dn = "OU=Users and Groups,DC=vega,DC=local";
        
        $ldap_conn = @ldap_connect($ldap_host, 389);
        if ($ldap_conn) {
            ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);
            
            // Tenta o Bind com o usuário e senha (ex: joao@vega.local)
            $bind = @ldap_bind($ldap_conn, $username, $password);
            
            if ($bind) {
                // Autenticação LDAP com sucesso!
                $user = User::getOne(['email' => $username]);
                
                if (!$user) {
                    // Buscar o nome completo do AD
                    $sAMAccountName = explode('@', $username)[0];
                    $filter = "(sAMAccountName=$sAMAccountName)";
                    $result = @ldap_search($ldap_conn, $ldap_base_dn, $filter);
                    $name = $sAMAccountName; // fallback name
                    
                    if ($result) {
                        $entries = @ldap_get_entries($ldap_conn, $result);
                        if ($entries && $entries["count"] > 0) {
                            if (isset($entries[0]["displayname"])) {
                                $name = $entries[0]["displayname"][0];
                            } elseif (isset($entries[0]["givenname"])) {
                                $name = $entries[0]["givenname"][0];
                            }
                        }
                    }
                    
                    // Auto-criar usuário no banco local para o ponto
                    $newUser = new User([
                        'name' => $name,
                        'email' => $username,
                        'password' => $password, // Será hasheada no update ou inserimos aqui? Wait, insert não faz hash!
                        'confirm_password' => $password,
                        'start_date' => date('Y-m-d'),
                        'end_date' => null,
                        'is_admin' => 0
                    ]);
                    
                    // O insert não faz hash da senha, mas para auto-criação do AD não importa muito a senha do banco
                    // Vamos preencher o hash para segurança
                    $newUser->password = password_hash($password, PASSWORD_DEFAULT);
                    $newUser->confirm_password = $newUser->password; 
                    
                    $newUser->insert();
                    $user = User::getOne(['email' => $username]);
                }
                
                if ($user->end_date) {
                    throw new AppException('Usuário está desligado da empresa.');
                }
                return $user;
            }
        }

        // 2. Fallback para banco local (ex: admin local do sistema)
        $user = User::getOne(['email' => $this->email]); // Usa exatamente o que foi digitado
        if ($user) {
            if ($user->end_date) {
                throw new AppException('Usuário está desligado da empresa.');
            }

            if (password_verify($password, $user->password)) {
                return $user;
            }
        }

        throw new AppException('Usuário e/ou senha inválidos.');
    }
}