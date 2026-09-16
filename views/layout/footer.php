</main>
<footer class="xp-footer">
  <div class="xp-footer-inner">
    <p>© <?= date('Y') ?> Xprod Fit · Sistema de Gestión</p>
    <nav class="xp-footer-links" aria-label="Links legales">
      <a href="<?= APP_URL ?>/legal/privacidad">Privacidad</a>
      <span aria-hidden="true">·</span>
      <a href="<?= APP_URL ?>/legal/terminos">Términos</a>
      <span aria-hidden="true">·</span>
      <button type="button" id="xp-manage-cookies" class="xp-footer-cookie-btn">
        🍪 Gestionar cookies
      </button>
    </nav>
  </div>
</footer>

<!-- ── Scripts de la app ── -->
<script src="<?= APP_URL ?>/assets/js/app.js?v=1.2" defer></script>

<!-- ── Banner de Cookies (GDPR/LOPD) ── -->
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/cookies.css">
<script src="<?= APP_URL ?>/assets/js/cookies.js" defer></script>

<script>
/* ─── Global Theme Toggle ─────────────────────────────── */
function toggleTheme() {
  const html = document.getElementById('htmlRoot');
  const current = html.getAttribute('data-theme') || 'dark';
  const next = current === 'dark' ? 'light' : 'dark';
  html.setAttribute('data-theme', next);
  localStorage.setItem('xp_theme', next);
}

document.addEventListener('DOMContentLoaded', function() {
  // SweetAlert2 theme sync
  const applySwallTheme = () => {
    // Customize SweetAlert2 to match current theme if modal opens in future
  };
  applySwallTheme();

  // ── Botón "Gestionar cookies" en footer ──
  const manageBtn = document.getElementById('xp-manage-cookies');
  if (manageBtn) {
    manageBtn.addEventListener('click', () => {
      // Limpiar consentimiento existente para forzar re-aparición del banner
      try { localStorage.removeItem('xpfit_cookie_consent'); } catch(e) {}
      // Recargar la página para mostrar el banner nuevamente
      window.location.reload();
    });
  }
});
</script>
<style>
.xp-footer-inner{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;}
.xp-footer-links{display:flex;align-items:center;gap:10px;font-size:.8rem;}
.xp-footer-links a{color:rgba(255,255,255,.4);text-decoration:none;transition:color .2s;}
.xp-footer-links a:hover{color:rgba(255,255,255,.7);}
.xp-footer-links span{color:rgba(255,255,255,.2);}
.xp-footer-cookie-btn{background:none;border:none;color:rgba(255,255,255,.4);font-size:.8rem;cursor:pointer;font-family:inherit;padding:0;transition:color .2s;}
.xp-footer-cookie-btn:hover{color:rgba(255,255,255,.7);}
</style>
</body>
</html>
