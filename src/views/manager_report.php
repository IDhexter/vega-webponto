<main class="content">
    <?php
        renderTitle(
            'Relatório Gerencial',
            'Resumo das horas trabalhadas dos funcionários',
            'icofont-chart-histogram'
        );
    ?>

    <!-- Top Cards (Resumo numérico) -->
    <div class="row mt-4">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="card-title mb-0"><i class="icofont-users mr-2 text-primary"></i> Qtde de Funcionários</h4>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center" style="min-height: 120px;">
                    <h3 class="mb-0 font-weight-bold" style="font-size: 3.5rem; color: #EEEEEE;"><?= $activeUsersCount ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="card-title mb-0"><i class="icofont-sand-clock mr-2 text-success"></i> Total de Horas no Mês</h4>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center" style="min-height: 120px;">
                    <h3 class="mb-0 font-weight-bold" style="font-size: 3.5rem; color: #EEEEEE;"><?= $hoursInMonth ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Middle Cards (Listas dinâmicas) -->
    <div class="row">
        <!-- Trabalhando Agora -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h4 class="card-title mb-0"><i class="icofont-live-support mr-2 text-success"></i> Trabalhando Agora</h4>
                </div>
                <div class="card-body">
                    <?php if (count($workingNow) > 0): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($workingNow as $worker): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center" style="background-color: transparent; border-color: rgba(255,255,255,0.1);">
                                    <span style="color: #EEEEEE;">🟢 <?= htmlspecialchars($worker['name']) ?></span>
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
                <div class="card-header">
                    <h4 class="card-title mb-0"><i class="icofont-warning mr-2 text-danger"></i> Alertas de Ponto Incompleto</h4>
                </div>
                <div class="card-body" style="max-height: 250px; overflow-y: auto;">
                    <?php if (count($incompletePunches) > 0): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($incompletePunches as $alert): ?>
                                <li class="list-group-item" style="background-color: transparent; border-color: rgba(255,255,255,0.1); color: #EEEEEE;">
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

    <!-- Bottom Card (Resumo de Horas no Mês) -->
    <div class="card mb-4">
        <div class="card-header">
            <h4 class="card-title mb-0"><i class="icofont-chart-bar-graph mr-2 text-primary"></i> Horas Trabalhadas no Mês por Funcionário</h4>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0" style="background-color: #fff; color: #333;">
                <thead style="background-color: #f8f9fa;">
                    <tr>
                        <th style="border-bottom: none;">Funcionário</th>
                        <th class="text-center" style="border-bottom: none;">Horas no Mês</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($hoursByEmployee as $emp): ?>
                        <tr>
                            <td class="align-middle"><?= htmlspecialchars($emp['name']) ?></td>
                            <td class="text-center align-middle"><strong><?= getTimeStringFromSeconds($emp['total_seconds']) ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mb-5 text-center">
        <a href="export_csv.php" class="btn btn-lg" style="background-color: #D7DF35; color: #0A0A0A; font-weight: bold; border-color: #D7DF35; border-radius: 6px;">
            <i class="icofont-download mr-2"></i>
            Exportar Relatório Mensal (.CSV)
        </a>
    </div>
</main>