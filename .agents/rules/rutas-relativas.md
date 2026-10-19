---
trigger: always_on
---

# Regla de Rutas Relativas y Dinámicas
- En todo el desarrollo futuro, aplicar siempre rutas relativas o dinámicas (vía helpers `url()`, `asset()`, constantes `APP_URL`, `BASE_PATH`, `BASE_DIR`, o rutas relativas directas tipo `assets/...`, `./...`, `__DIR__`).
- Queda estrictamente prohibido usar o introducir rutas absolutas fijas dependientes del servidor (ej. `/Xprod%20Fit/...`, `/assets/...`, `/api/...`, `/views/...` o rutas de disco como `C:\...` o `/var/www/...`).
