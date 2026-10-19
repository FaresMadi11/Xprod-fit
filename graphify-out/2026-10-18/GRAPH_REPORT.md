# Graph Report - Xprod Fit  (2026-10-18)

## Corpus Check
- 224 files · ~281,516 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 14 file(s) not represented in the graph (top: (none) 6, .css 6, .conf 2)

## Summary
- 1522 nodes · 2585 edges · 184 communities (38 shown, 146 thin omitted)
- Extraction: 87% EXTRACTED · 13% INFERRED · 0% AMBIGUOUS · INFERRED: 328 edges (avg confidence: 0.85)
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
- controllers/EntrenadorController.php
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
- Product
- _views/auth/login.php
- PagoModel
- PagoModel
- _views/entrenador/metodos_pago.php
- positionRegWheel
- views/entrenador/metodos_pago.php
- core/helpers.php
- MetaModel
- UsuarioSistemaModel
- MetaModel
- UsuarioSistemaModel
- _views/cliente/modificar_rutina.php
- views/cliente/modificar_rutina.php
- views/entrenador/clientes.php
- getDB
- registrarNotificacionSistema
- asignar_rutina.php
- RutinaModel
- clearRecoveryErrors
- views/layout/header.php
- core/rate_limiter.php
- submitRegistration
- .strlen

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
- `cliMarcarRutina()` --calls--> `registrarNotificacionSistema()`  [INFERRED]
  controllers/ClienteController.php → core/helpers.php
- `cliGuardarPago()` --calls--> `registrarNotificacionSistema()`  [INFERRED]
  controllers/ClienteController.php → core/helpers.php
- `cliApiLeerNotificaciones()` --calls--> `marcarTodasNotificacionesLeidas()`  [INFERRED]
  controllers/ClienteController.php → core/helpers.php
- `cliGuardarModificacion()` --calls--> `registrarNotificacionSistema()`  [INFERRED]
  controllers/ClienteController.php → core/helpers.php
- `cliReordenarEjercicios()` --calls--> `registrarNotificacionSistema()`  [INFERRED]
  controllers/ClienteController.php → core/helpers.php

## Import Cycles
- None detected.

## Communities (184 total, 146 thin omitted)

### Community 0 - "controllers/ClienteController.php"
Cohesion: 0.08
Nodes (15): cliApiDiaDetalle(), cliApiProgresoEjercicios(), cliApiProgresoHistorial(), cliApiTasaBcv(), cliEjerciciosProgramados(), cliEstadisticas(), cliGuardarSerie(), cliHistorialDia() (+7 more)

### Community 4 - "controllers/EntrenadorController.php"
Cohesion: 0.04
Nodes (6): entApiGuardarMensualidad(), entApiPagosCliente(), entComprobanteUrl(), entConfiguracion(), entPagosJson(), getAjustesGlobales()

### Community 8 - "views/auth/login.php"
Cohesion: 0.05
Nodes (37): boxMetaOtros, boxSubHipertrofia, btnPesoMinus, btnPesoPlus, btnRecovery, btnRulerMinus, btnRulerPlus, btnSkipGoals (+29 more)

### Community 9 - "views/entrenador/crear_rutina.php"
Cohesion: 0.08
Nodes (37): addExercise(), addSet(), cancelarReordenarCompacto(), catalogoEjercicios, categoriasDB, catsDrillBack(), catsDrillInto(), catsFilterExercises() (+29 more)

### Community 10 - "proyecto/controllers/EntrenadorController.php"
Cohesion: 0.11
Nodes (35): entActualizarMedidas(), entApiCatalogoAgregar(), entApiCatalogoBorrar(), entApiCatalogoEditar(), entApiCatalogoList(), entApiLeerNotificaciones(), entApiRutinaLive(), entApiTiendaAgregar() (+27 more)

### Community 11 - "_views/cliente/estadisticas.php"
Cohesion: 0.08
Nodes (26): abrirHistorialPeso(), abrirModalDia(), cambiarMetrica(), cambiarTren(), CHART_BG, CHART_COLORS, DIAS_ES, GRAFICA_DATA (+18 more)

### Community 12 - "views/cliente/estadisticas.php"
Cohesion: 0.10
Nodes (25): abrirHistorialPeso(), abrirModalDia(), asegurarPortalBody(), badgeSerieTexto(), badgeSerieTitulo(), cambiarMetrica(), cambiarTren(), currentHistorial (+17 more)

### Community 13 - "redirect"
Cohesion: 0.09
Nodes (27): authGoogleCallback(), authGoogleRedirect(), authLogin(), authLogout(), authRegistro(), csrfField(), csrfToken(), cliRutinaLista() (+19 more)

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

### Community 60 - "Product"
Cohesion: 0.18
Nodes (10): Brand Commitments, Capabilities and Constraints, Evidence on Hand, Operating Context, Platform, Positioning, Product, Product Principles (+2 more)

### Community 66 - "_views/entrenador/metodos_pago.php"
Cohesion: 0.60
Nodes (3): editMethod(), resetForm(), toggleFields()

### Community 67 - "positionRegWheel"
Cohesion: 0.40
Nodes (5): ensureWheelPositioned(), initRegWheel(), positionRegWheel(), syncRegWheelDate(), updateRegWheelCol()

### Community 68 - "views/entrenador/metodos_pago.php"
Cohesion: 0.60
Nodes (3): editMethod(), resetForm(), toggleFields()

### Community 69 - "core/helpers.php"
Cohesion: 0.13
Nodes (18): cliApiLeerNotificaciones(), cliApiMarcarNotificacion(), cliApiNotificaciones(), cliNotificaciones(), entApiLeerNotificaciones(), entApiMarcarNotificacion(), entApiNotificaciones(), entNotificaciones() (+10 more)

### Community 79 - "registrarNotificacionSistema"
Cohesion: 0.20
Nodes (10): cliGuardarModificacion(), cliGuardarPago(), cliMarcarRutina(), cliReordenarEjercicios(), entActualizarMedidas(), entAprobarModificacion(), entGuardarPlantillaRutina(), entGuardarRutina() (+2 more)

### Community 94 - "views/layout/header.php"
Cohesion: 0.30
Nodes (10): closeXpNotifDropdown(), toggleXpNotifDropdown(), xpClickNotifItem(), xpEscapeHtml(), xpFetchNotificaciones(), xpMarcarTodoLeido(), xpNotifsCache, xpPositionDropdown() (+2 more)

### Community 175 - "core/rate_limiter.php"
Cohesion: 0.33
Nodes (7): env(), sendEmail(), getClientIp(), rateLimitCheck(), _rateLimitDeny(), rateLimitFail(), rateLimitReset()

### Community 178 - "submitRegistration"
Cohesion: 0.29
Nodes (7): clearRegErrors(), normalizarCedula(), postApi(), setBtn(), setRegError(), submitRegistration(), switchView()

## Knowledge Gaps
- **146 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `PHPMailer` connect `PHPMailer` to `.smtpConnect`, `SMTP`, `.addOrEnqueueAnAddress`, `redirect`, `.lang`, `.strlen`, `.normalizeBreaks`, `.preSend`, `.strpos`?**
  _High betweenness centrality (0.133) - this node is a cross-community bridge._
- **Are the 79 inferred relationships involving `getDB()` (e.g. with `env()` and `authDoRegistro()`) actually correct?**
  _`getDB()` has 79 INFERRED edges - model-reasoned connections that need verification._
- **Should `controllers/ClienteController.php` be split into smaller, more focused modules?**
  _Cohesion score 0.07539118065433854 - nodes in this community are weakly interconnected._
- **Why does `SimpleXLSX` connect `SimpleXLSX` to `.strlen`, `SimpleXLSX`, `.strtoupper`?**
  _High betweenness centrality (0.130) - this node is a cross-community bridge._
- **Should `PHPMailer` be split into smaller, more focused modules?**
  _Cohesion score 0.05254901960784314 - nodes in this community are weakly interconnected._
- **Should `PHPMailer` be split into smaller, more focused modules?**
  _Cohesion score 0.05254901960784314 - nodes in this community are weakly interconnected._
- **Should `SMTP` be split into smaller, more focused modules?**
  _Cohesion score 0.0746606334841629 - nodes in this community are weakly interconnected._