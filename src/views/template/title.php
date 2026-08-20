<div class="content-title mb-4">
    <?php if ($icon) { ?>
        <i class="icon <?= $icon ?> mr-2"></i>
    <?php } ?>

    <div>
        <h1><?= $title ?></h1>
        <h2><?= $subtitle ?> <span class="mx-2">|</span> Horário do Servidor: <strong id="server-clock"><?= (new DateTime())->format('H:i:s') ?></strong></h2>
    </div>
</div>