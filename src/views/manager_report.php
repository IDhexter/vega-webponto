<main class="content">
    <?php
        renderTitle(
            'Relatório Gerencial',
            'Resumo das horas trabalhadas dos funcionários',
            'icofont-chart-histogram'
        );
    ?>

    <div class="summary-boxes">
        <div class="summary-box bg-primary">
            <i class="icon icofont-users"></i>
            <p class="title">Qtde de Funcionários</p>
            <h3 class="value"><?= $activeUsersCount ?></h3>
        </div>

        <div class="summary-box bg-success">
            <i class="icon icofont-sand-clock"></i>
            <p class="title">Total de Horas no Mês</p>
            <h3 class="value"><?= $hoursInMonth ?></h3>
        </div>
    </div>
    
    <div class="mt-5 text-center">
        <a href="export_csv.php" class="btn btn-lg btn-secondary">
            <i class="icofont-download mr-2"></i>
            Exportar Relatório Mensal (.CSV)
        </a>
    </div>
</main>