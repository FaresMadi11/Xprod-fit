# Graph Report - Xprod Fit  (2026-10-16)

## Corpus Check
- cluster-only mode — file stats not available

## Summary
- 1472 nodes · 2498 edges · 175 communities (33 shown, 142 thin omitted)
- Extraction: 88% EXTRACTED · 12% INFERRED · 0% AMBIGUOUS · INFERRED: 306 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `747bbaec`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- controllers/ClienteController.php
- PHPMailer
- PHPMailer
- SMTP
- SimpleXLSX
- SimpleXLSX
- SMTP
- views/auth/login.php
- views/entrenador/crear_rutina.php
- proyecto/controllers/EntrenadorController.php
- _views/cliente/estadisticas.php
- views/cliente/estadisticas.php
- redirect
- getDB
- views/cliente/rutina_dia.php
- .lang
- views/cliente/perfil.php
- views/entrenador/perfil_cliente.php
- controllers/AuthController.php
- views/entrenador/configuracion.php
- _views/entrenador/configuracion.php
- .lang
- _views/cliente/perfil.php
- UsuarioModel
- authDoLogin
- views/entrenador/asistencias.php
- .strpos
- core/security.php
- assets/js/cookies.js
- _assets/js/cookies.js
- UsuarioModel
- test_silueta.php
- _views/cliente/rutina_dia.php
- _views/auth/recuperar.php
- views/auth/recuperar.php
- views/entrenador/dashboard.php
- MetodoPagoModel
- _views/entrenador/dashboard.php
- ClienteModel
- MetodoPagoModel
- ClienteModel
- _views/entrenador/crear_rutina.php
- assets/js/app.js
- SesionAsistenciaModel
- _assets/js/app.js
- SesionAsistenciaModel
- ObjetivoModel
- PesoHistorialModel
- RutinaModel
- ObjetivoModel
- PesoHistorialModel
- RutinaModel
- _views/auth/login.php
- PagoModel
- PagoModel
- _views/entrenador/metodos_pago.php
- positionRegWheel
- views/entrenador/metodos_pago.php
- entApiPagosCliente
- MetaModel
- UsuarioSistemaModel
- MetaModel
- UsuarioSistemaModel
- _views/cliente/modificar_rutina.php
- views/cliente/modificar_rutina.php
- views/entrenador/clientes.php
- getDB
- registrarNotificacion
- asignar_rutina.php
- entConfiguracion
- clearRecoveryErrors

## God Nodes (most connected - your core abstractions)
1. `PHPMailer` - 131 edges
2. `PHPMailer` - 130 edges
3. `getDB()` - 81 edges
4. `SMTP` - 46 edges
5. `SimpleXLSX` - 45 edges
6. `SimpleXLSX` - 45 edges
7. `SMTP` - 44 edges
8. `jsonOut()` - 36 edges
9. `csrfCheck()` - 30 edges
10. `Exception` - 24 edges

## Surprising Connections (you probably didn't know these)
- `cliApiTasaBcv()` --calls--> `obtenerTasaBCV()`  [INFERRED]
  controllers/ClienteController.php → core/helpers.php
- `cliPerfil()` --calls--> `obtenerTasaBCV()`  [INFERRED]
  controllers/ClienteController.php → core/helpers.php
- `entApiTiendaGet()` --calls--> `getDB()`  [INFERRED]
  proyecto/controllers/EntrenadorController.php → proyecto/config/database.php
- `entApiTiendaSecciones()` --calls--> `getDB()`  [INFERRED]
  proyecto/controllers/EntrenadorController.php → proyecto/config/database.php
- `entAsistencias()` --calls--> `getDB()`  [INFERRED]
  proyecto/controllers/EntrenadorController.php → proyecto/config/database.php

## Import Cycles
- None detected.

## Communities (175 total, 142 thin omitted)

### Community 0 - "controllers/ClienteController.php"
Cohesion: 0.05
Nodes (23): cliApiDiaDetalle(), cliApiTasaBcv(), cliEjerciciosProgramados(), cliEstadisticas(), cliGuardarSerie(), cliHistorialDia(), cliInferirEquipamiento(), cliJson() (+15 more)

### Community 8 - "views/auth/login.php"
Cohesion: 0.04
Nodes (33): btnPesoMinus, btnPesoPlus, btnRecovery, btnRulerMinus, btnRulerPlus, btnStandbyToLogin, inpCedula, lblPregunta (+25 more)

### Community 9 - "views/entrenador/crear_rutina.php"
Cohesion: 0.08
Nodes (37): addExercise(), addSet(), cancelarReordenarCompacto(), catalogoEjercicios, categoriasDB, catsDrillBack(), catsDrillInto(), catsFilterExercises() (+29 more)

### Community 10 - "proyecto/controllers/EntrenadorController.php"
Cohesion: 0.11
Nodes (35): entActualizarMedidas(), entApiCatalogoAgregar(), entApiCatalogoBorrar(), entApiCatalogoEditar(), entApiCatalogoList(), entApiLeerNotificaciones(), entApiRutinaLive(), entApiTiendaAgregar() (+27 more)

### Community 11 - "_views/cliente/estadisticas.php"
Cohesion: 0.08
Nodes (27): e(), abrirHistorialPeso(), abrirModalDia(), cambiarMetrica(), cambiarTren(), CHART_BG, CHART_COLORS, DIAS_ES (+19 more)

### Community 12 - "views/cliente/estadisticas.php"
Cohesion: 0.09
Nodes (27): abrirHistorialPeso(), abrirModalDia(), asegurarPortalBody(), badgeSerieTexto(), badgeSerieTitulo(), cambiarMetrica(), cambiarTren(), CHART_BG (+19 more)

### Community 13 - "redirect"
Cohesion: 0.10
Nodes (26): authGoogleCallback(), authGoogleRedirect(), authLogin(), authLogout(), authRegistro(), csrfField(), csrfToken(), cliRutinaLista() (+18 more)

### Community 14 - "getDB"
Cohesion: 0.11
Nodes (25): getDB(), cliApiCambiarPassword(), cliApiDiaDetalle(), cliApiEditarPerfil(), cliApiEditarPeso(), cliApiGuardarPregunta(), cliApiNuevoPeso(), cliApiPesoHistorial() (+17 more)

### Community 15 - "views/cliente/rutina_dia.php"
Cohesion: 0.11
Nodes (16): abrirModalReemplazoCliente(), actualizarEstadoEjercicio(), actualizarProgreso(), cancelarReordenarCompacto(), cerrarTodosModalesCliente(), confirmarReemplazo(), ejMap, eliminadosEnReordenar (+8 more)

### Community 18 - "views/cliente/perfil.php"
Cohesion: 0.11
Nodes (13): copiarDatosPago(), draw(), fbCopy(), init(), mostrarToast(), open(), resetSegModal(), scrollToWheelDate() (+5 more)

### Community 19 - "views/entrenador/perfil_cliente.php"
Cohesion: 0.11
Nodes (14): abrirModalCalendarioRT(), actualizarTextoRango(), api(), aplicarFiltroCalendario(), calFechaActual, cambiarMesCalendario(), clickDiaCalendario(), formatearFechaISO() (+6 more)

### Community 20 - "controllers/AuthController.php"
Cohesion: 0.11
Nodes (11): authDoRegistro(), authGoogleCallback(), authGoogleRedirect(), authLogin(), authRecuperar(), authRegistro(), authRegistroError(), googleCallback() (+3 more)

### Community 21 - "views/entrenador/configuracion.php"
Cohesion: 0.15
Nodes (15): abrirCategoria(), abrirFormulario(), agregarPaso(), buscarGlobalCMS(), catalogoCompleto, closePanel(), closePwdModal(), crearCardEjercicio() (+7 more)

### Community 23 - "_views/entrenador/configuracion.php"
Cohesion: 0.14
Nodes (13): abrirCategoria(), abrirFormulario(), agregarPaso(), buscarGlobalCMS(), catalogoCompleto, closePwdModal(), crearCardEjercicio(), editarEjercicio() (+5 more)

### Community 25 - "_views/cliente/perfil.php"
Cohesion: 0.12
Nodes (7): copiarDatosPago(), draw(), fbCopy(), init(), resetSegModal(), segOverlay, switchTab()

### Community 28 - "authDoLogin"
Cohesion: 0.19
Nodes (13): authDoLogin(), authDoRegistro(), getClientIp(), rateLimitCheck(), _rateLimitDeny(), rateLimitFail(), rateLimitReset(), assertClienteBelongsToEntrenador() (+5 more)

### Community 29 - "views/entrenador/asistencias.php"
Cohesion: 0.20
Nodes (10): actualizarBadges(), avatarBg(), cargarDatosPorFecha(), elegirFechaCalendario(), esc(), iniciarPolling(), PALETTE, renderPanelAlertas() (+2 more)

### Community 31 - "core/security.php"
Cohesion: 0.17
Nodes (8): csrfCheck(), csrfValidate(), requireCSRF(), jsonOut(), assertClienteBelongsToEntrenador(), assertOwnership(), _denyAccess(), sanitizeId()

### Community 32 - "assets/js/cookies.js"
Cohesion: 0.30
Nodes (12): applyConsent(), buildBanner(), buildModal(), getDefaultPreferences(), hideBanner(), init(), loadAnalyticsScripts(), loadConsent() (+4 more)

### Community 33 - "_assets/js/cookies.js"
Cohesion: 0.30
Nodes (12): applyConsent(), buildBanner(), buildModal(), getDefaultPreferences(), hideBanner(), init(), loadAnalyticsScripts(), loadConsent() (+4 more)

### Community 36 - "test_silueta.php"
Cohesion: 0.22
Nodes (12): addBlock(), chipsRow, EXDB, ezBlocks, hintTxt, makeOpts(), muscInp, renderChips() (+4 more)

### Community 38 - "_views/cliente/rutina_dia.php"
Cohesion: 0.20
Nodes (5): actualizarEstadoEjercicio(), actualizarProgreso(), ejMap, guardarSerie(), style

### Community 39 - "_views/auth/recuperar.php"
Cohesion: 0.40
Nodes (8): animateTransition(), procesarPaso1(), procesarPaso2(), procesarPaso3(), procesarPaso4(), setButtonLoading(), showAlert(), volverPaso()

### Community 40 - "views/auth/recuperar.php"
Cohesion: 0.40
Nodes (8): animateTransition(), procesarPaso1(), procesarPaso2(), procesarPaso3(), procesarPaso4(), setButtonLoading(), showAlert(), volverPaso()

### Community 42 - "MetodoPagoModel"
Cohesion: 0.20
Nodes (3): entEliminarMetodo(), entGuardarMetodo(), MetodoPagoModel

### Community 49 - "_views/entrenador/crear_rutina.php"
Cohesion: 0.36
Nodes (5): addBlock(), bindSelectLogic(), catalogo, getOpts(), slug()

### Community 50 - "assets/js/app.js"
Cohesion: 0.29
Nodes (5): clienteForm, loginForm, medidasForm, regForm, Xp

### Community 52 - "_assets/js/app.js"
Cohesion: 0.29
Nodes (5): clienteForm, loginForm, medidasForm, regForm, Xp

### Community 66 - "_views/entrenador/metodos_pago.php"
Cohesion: 0.60
Nodes (3): editMethod(), resetForm(), toggleFields()

### Community 67 - "positionRegWheel"
Cohesion: 0.40
Nodes (5): ensureWheelPositioned(), initRegWheel(), positionRegWheel(), syncRegWheelDate(), updateRegWheelCol()

### Community 68 - "views/entrenador/metodos_pago.php"
Cohesion: 0.60
Nodes (3): editMethod(), resetForm(), toggleFields()

### Community 69 - "entApiPagosCliente"
Cohesion: 0.50
Nodes (4): entApiGuardarMensualidad(), entApiPagosCliente(), entComprobanteUrl(), entPagosJson()

### Community 79 - "registrarNotificacion"
Cohesion: 0.67
Nodes (3): entActualizarMedidas(), entGuardarRutina(), registrarNotificacion()

## Knowledge Gaps
- **142 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `PHPMailer` connect `PHPMailer` to `.smtpConnect`, `.addOrEnqueueAnAddress`, `redirect`, `.lang`, `.strlen`, `.preSend`, `.strpos`?**
  _High betweenness centrality (0.126) - this node is a cross-community bridge._
- **Are the 79 inferred relationships involving `getDB()` (e.g. with `env()` and `authDoRegistro()`) actually correct?**
  _`getDB()` has 79 INFERRED edges - model-reasoned connections that need verification._
- **Should `controllers/ClienteController.php` be split into smaller, more focused modules?**
  _Cohesion score 0.05429864253393665 - nodes in this community are weakly interconnected._
- **Why does `SMTP` connect `SMTP` to `PHPMailer`?**
  _High betweenness centrality (0.086) - this node is a cross-community bridge._
- **Should `PHPMailer` be split into smaller, more focused modules?**
  _Cohesion score 0.05203619909502263 - nodes in this community are weakly interconnected._
- **Why does `SimpleXLSX` connect `SimpleXLSX` to `.strlen`, `.strpos`?**
  _High betweenness centrality (0.074) - this node is a cross-community bridge._
- **Should `PHPMailer` be split into smaller, more focused modules?**
  _Cohesion score 0.05254901960784314 - nodes in this community are weakly interconnected._