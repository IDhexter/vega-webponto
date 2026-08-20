<main class="content">
    <?php 
        renderTitle(
            "Registrar Ponto",
            "Mantenha seu ponto consistente!",
            "icofont-check-alt"
        );

        include(TEMPLATE_PATH . "/messages.php");
    ?>    

    <div class="card">
        <div class="card-header">
            <h3><?= $today ?></h3>
            <p class="mb-0">Registros feitos hoje</p>
        </div>

        <div class="card-body">
            <div class="d-flex m-5 justify-content-around">
                <span class="record">Entrada: <?= $workingHours->time1 ?? "---" ?></span>
                <span class="record">Saída: <?= $workingHours->time2 ?? "---" ?></span>
            </div>
        </div>

        <form action="innout.php" method="post" class="card-footer d-flex justify-content-between align-items-center">
            <input type="hidden" name="lat" id="lat" value="">
            <input type="hidden" name="lon" id="lon" value="">
            <input type="text" name="obs" class="form-control w-50" placeholder="OBS: Justificativa (opcional)">
            <div>
                <?php if (isset($workingHours) && $workingHours->time1): ?> 
                    <a href="clear_innout.php" class="btn btn-danger btn-lg mr-3" onclick="return confirm('Tem certeza que deseja desfazer seu último registro?');"> 
                        <i class="icofont-undo mr-1"></i> Limpar Ponto
                    </a> 
                <?php endif; ?> 
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="icofont-check mr-1"></i>
                    Bater o Ponto
                </button>
            </div>
        </form>
    </div>
</main>

<script>
    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById('lat').value = position.coords.latitude;
            document.getElementById('lon').value = position.coords.longitude;
        }, function(error) {
            console.log("Geolocalização não permitida ou indisponível.");
        });
    }
</script>