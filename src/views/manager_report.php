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
    
    <div class="mt-4 row">
        <!-- Trabalhando Agora -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h4 class="card-title mb-0"><i class="icofont-live-support mr-2"></i> Trabalhando Agora</h4>
                </div>
                <div class="card-body">
                    <?php if (count($workingNow) > 0): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($workingNow as $worker): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>🟢 <?= htmlspecialchars($worker['name']) ?></span>
                                    <span class="badge badge-success badge-pill">Entrou: <?= $worker['time1'] ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted text-center mt-3">Nenhum funcionário operando no momento.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Alertas de Ponto Incompleto -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-danger text-white">
                    <h4 class="card-title mb-0"><i class="icofont-warning mr-2"></i> Alertas de Ponto Incompleto</h4>
                </div>
                <div class="card-body" style="max-height: 250px; overflow-y: auto;">
                    <?php if (count($incompletePunches) > 0): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($incompletePunches as $alert): ?>
                                <li class="list-group-item">
                                    <strong><?= htmlspecialchars($alert['name']) ?></strong><br>
                                    <small class="text-danger">
                                        Faltou a Saída em: <?= (new DateTime($alert['work_date']))->format('d/m/Y') ?> (Entrou às <?= $alert['time1'] ?>)
                                    </small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted text-center mt-3">Nenhuma pendência encontrada.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumo de Horas no Mês -->
    <div class="card mt-2">
        <div class="card-header bg-dark text-white">
            <h4 class="card-title mb-0"><i class="icofont-chart-bar-graph mr-2"></i> Horas Trabalhadas no Mês por Funcionário</h4>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead>
                    <tr>
                        <th>Funcionário</th>
                        <th class="text-center">Horas no Mês</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($hoursByEmployee as $emp): ?>
                        <tr>
                            <td><?= htmlspecialchars($emp['name']) ?></td>
                            <td class="text-center"><strong><?= getTimeStringFromSeconds($emp['total_seconds']) ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 mb-5 text-center">
        <a href="export_csv.php" class="btn btn-lg btn-secondary">
            <i class="icofont-download mr-2"></i>
            Exportar Relatório Mensal (.CSV)
        </a>
    </div>
</main>
