</main>
<footer class="xp-footer">
  <p>© <?= date('Y') ?> Xprod Fit · Sistema de Gestión</p>
</footer>
<script src="<?= APP_URL ?>/assets/js/app.js?v=1.1"></script>
<script>
/* ─── Global Theme Toggle ─────────────────────────────── */
function toggleTheme() {
  const html = document.getElementById('htmlRoot');
  const current = html.getAttribute('data-theme') || 'dark';
  const next = current === 'dark' ? 'light' : 'dark';
  html.setAttribute('data-theme', next);
  localStorage.setItem('xp_theme', next);
}
// SweetAlert2 theme sync
document.addEventListener('DOMContentLoaded', function() {
  const applySwallTheme = () => {
    const isDark = document.getElementById('htmlRoot').getAttribute('data-theme') === 'dark';
    // Customize SweetAlert2 to match current theme if modal opens in future
  };
  applySwallTheme();
});
</script>
</body>
</html>
