</main> <!-- Penutup tag main-content dari header.php -->

    <!-- Popper.js dan Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
    <script>
    (function () {
        const root = document.documentElement;
        const button = document.getElementById('themeToggle');
        const saved = localStorage.getItem('sip-theme');
        const preferred = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        function applyTheme(theme) {
            const dark = theme === 'dark';
            root.setAttribute('data-theme', dark ? 'dark' : 'light');
            if (button) {
                button.innerHTML = dark ? '<i class="fas fa-sun" aria-hidden="true"></i>' : '<i class="fas fa-moon" aria-hidden="true"></i>';
                button.setAttribute('aria-label', dark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap');
                button.title = dark ? 'Gunakan mode terang' : 'Gunakan mode gelap';
            }
        }
        applyTheme(saved || (preferred ? 'dark' : 'light'));
        if (button) button.addEventListener('click', function () {
            const next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            localStorage.setItem('sip-theme', next);
            applyTheme(next);
        });
    })();
    </script>
</body>
</html>