<main class="content">
    <?php
        renderTitle(
            'Cadastro de Usuários',
            'Mantenha os dados dos usuários atualizados',
            'icofont-users'
        );

        include(TEMPLATE_PATH . "/messages.php");
    ?>

    <a 
        class="btn btn-lg btn-primary mb-3"
        href="save_user.php"
        style="border-radius: 6px;"
    >
        <i class="icofont-plus mr-2"></i>Novo Usuário
    </a>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0" style="background-color: #fff; color: #333;">
                <thead style="background-color: #f8f9fa;">
                    <tr>
                        <th style="border-bottom: none;">Nome</th>
                        <th style="border-bottom: none;">Email</th>
                        <th style="border-bottom: none;">Data de Admissão</th>
                        <th style="border-bottom: none;">Data de Desligamento</th>
                        <th style="border-bottom: none;">Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($users as $user): ?>
                        <tr>
                            <td class="align-middle"><?= htmlspecialchars($user->name) ?></td>
                            <td class="align-middle"><?= htmlspecialchars($user->email) ?></td>
                            <td class="align-middle"><?= $user->start_date ?></td>
                            <td class="align-middle"><?= $user->end_date ?></td>
                            <td class="align-middle">
                                <a 
                                    href="save_user.php?update=<?= $user->id ?>" 
                                    class="btn btn-warning rounded-circle mr-2"
                                    title="Editar"
                                >
                                    <i class="icofont-edit"></i>
                                </a>

                                <a 
                                    href="?delete=<?= $user->id ?>"
                                    class="btn btn-danger rounded-circle"
                                    title="Excluir"
                                >
                                    <i class="icofont-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach?>
                </tbody>
            </table>
        </div>
    </div>
</main>