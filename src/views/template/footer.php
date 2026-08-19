    <footer class="footer">
        <span class="text-muted">Horário Oficial do Servidor: <strong id="server-clock"><?= (new DateTime())->format('H:i:s') ?></strong></span>
    </footer>

    <script src="assets/js/app.js?v=<?= time() ?>"></script>
    
    </body>
</html>