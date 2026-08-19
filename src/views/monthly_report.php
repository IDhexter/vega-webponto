<main class="content">
    <?php
        renderTitle(
            'Relatório Mensal',
            'Acompanhe seu saldo de horas',
            'icofont-ui-calendar'
        );
    ?>

    <div>
        <form class="mb-4" action="#" method="post">
			<div class="input-group">
				<?php if($user->is_admin): ?>
					<select name="user" class="form-control mr-2" placeholder="Selecione o usuário...">
						<option value="">Selecione o usuário</option>
                        <?php
							foreach ($users as $userOption) {
								$selected = $userOption->id === $selectedUserId ? 'selected' : '';
                                echo "<option value='{$userOption->id}' {$selected}>{$userOption->name}</option>";
							}
						?>
					</select>
				<?php endif ?>

				<select name="period" class="form-control" placeholder="Selecione o período...">
					<?php
						foreach ($periods as $key => $month) {
							$selected = $key === $selectedPeriod ? 'selected' : '';
							echo "<option value='{$key}' {$selected}>{$month}</option>";
						}
					?>
				</select>

				<button class="btn btn-primary ml-2">
					<i class="icofont-search"></i>
				</button>
			</div>
		</form>

        <form action="#" method="post">
            <input type="hidden" name="user" value="<?= $selectedUserId ?>">
            <input type="hidden" name="period" value="<?= $selectedPeriod ?>">

            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <th style="width: 10%;">Dia</th>
                    <th>Entrada</th>
                    <th>Obs. Entrada</th>
                    <th>Saída</th>
                    <th>Obs. Saída</th>
                    <th>Saldo</th>
                    <th>IP</th>
                </thead>

                <tbody>
                    <?php foreach($report as $registry): ?>
                        <tr>
                            <td class="align-middle"><?= formatDateWithLocale($registry->work_date, 'd/m/Y') ?></td>
                            
                            <?php if($user->is_admin): ?>
                                <td class="align-middle p-1">
                                    <input type="time" name="punches[<?= $registry->work_date ?>][time1]" class="form-control form-control-sm text-center" step="1" style="background-color: transparent !important; color: #333 !important; border: none !important; box-shadow: none !important; font-family: inherit !important; font-size: inherit !important; font-weight: normal !important;" value="<?= $registry->time1 ?>">
                                </td>
                                <td class="align-middle"><?= $registry->obs_time1 ? htmlspecialchars($registry->obs_time1) : '' ?></td>
                                <td class="align-middle p-1">
                                    <input type="time" name="punches[<?= $registry->work_date ?>][time2]" class="form-control form-control-sm text-center" step="1" style="background-color: transparent !important; color: #333 !important; border: none !important; box-shadow: none !important; font-family: inherit !important; font-size: inherit !important; font-weight: normal !important;" value="<?= $registry->time2 ?>">
                                </td>
                                <td class="align-middle"><?= $registry->obs_time2 ? htmlspecialchars($registry->obs_time2) : '' ?></td>
                            <?php else: ?>
                                <td class="align-middle"><?= $registry->time1 ?></td>
                                <td class="align-middle"><?= $registry->obs_time1 ? htmlspecialchars($registry->obs_time1) : '' ?></td>
                                <td class="align-middle"><?= $registry->time2 ?></td>
                                <td class="align-middle"><?= $registry->obs_time2 ? htmlspecialchars($registry->obs_time2) : '' ?></td>
                            <?php endif; ?>

                            <td class="align-middle"><?= $registry->getBalance() ?></td>
                            <td class="align-middle"><?= $registry->last_ip ?></td>
                        </tr>
                    <?php endforeach ?>

                    <tr class="bg-primary text-white font-weight-bold">
                        <td>Horas Trabalhadas</td>
                        <td colspan="4"><?= $sumOfWorkedTime ?></td>
                        <td>Saldo Mensal</td>
                        <td><?= $balance ?></td>
                    </tr>
                </tbody>
            </table>

            <?php if($user->is_admin): ?>
                <div class="mt-4 mb-5 d-flex justify-content-end">
                    <button type="submit" class="btn btn-lg btn-success">
                        <i class="icofont-save mr-2"></i> Salvar Correções de Ponto
                    </button>
                </div>
            <?php endif; ?>
        </form>
    </div>
</main>

