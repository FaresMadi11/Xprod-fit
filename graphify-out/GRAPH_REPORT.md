# Graph Report - Xprod Fit  (2026-10-24)

## Corpus Check
- 104 files · ~205,853 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 9 file(s) not represented in the graph (top: (none) 5, .css 2, .conf 1)

## Summary
- 948 nodes · 1951 edges · 74 communities (26 shown, 48 thin omitted)
- Extraction: 77% EXTRACTED · 23% INFERRED · 0% AMBIGUOUS · INFERRED: 451 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `45c45985`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- controllers/ClienteController.php
- pwa-sync.js
- PHPMailer
- SMTP
- EntrenadorController.php
- SimpleXLSX
- progreso.php
- views/auth/login.php
- views/entrenador/crear_rutina.php
- redirect
- views/cliente/estadisticas.php
- sw.js
- views/cliente/rutina_dia.php
- views/cliente/perfil.php
- views/entrenador/perfil_cliente.php
- UsuarioModel
- configuracion.php
- .lang
- views/entrenador/asistencias.php
- assets/js/cookies.js
- .normalizeBreaks
- views/auth/recuperar.php
- views/entrenador/dashboard.php
- ClienteModel
- MetodoPagoModel
- assets/js/app.js
- SesionAsistenciaModel
- ObjetivoModel
- PesoHistorialModel
- RutinaModel
- Product
- PagoModel
- positionRegWheel
- views/entrenador/metodos_pago.php
- core/helpers.php
- MetaModel
- UsuarioSistemaModel
- views/cliente/modificar_rutina.php
- views/entrenador/clientes.php
- getDB
- clearRecoveryErrors
- footer.php
- views/layout/header.php
- submitRegistration

## God Nodes (most connected - your core abstractions)
1. `PHPMailer` - 131 edges
2. `getDB()` - 107 edges
3. `jsonOut()` - 55 edges
4. `csrfCheck()` - 51 edges
5. `SMTP` - 46 edges
6. `SimpleXLSX` - 45 edges
7. `Exception` - 24 edges
8. `UsuarioModel` - 19 edges
9. `redirect()` - 17 edges
10. `authDoRegistro()` - 16 edges

## Surprising Connections (you probably didn't know these)
- `cliApiEditarPerfil()` --calls--> `getDB()`  [INFERRED]
  controllers/ClienteController.php → config/database.php
- `cliApiGuardarPregunta()` --calls--> `getDB()`  [INFERRED]
  controllers/ClienteController.php → config/database.php
- `cliApiPesoHistorial()` --calls--> `getDB()`  [INFERRED]
  controllers/ClienteController.php → config/database.php
- `cliApiSubirFoto()` --calls--> `getDB()`  [INFERRED]
  controllers/ClienteController.php → config/database.php
- `cliPago()` --calls--> `getDB()`  [INFERRED]
  controllers/ClienteController.php → config/database.php

## Import Cycles
- None detected.

## Communities (74 total, 48 thin omitted)

### Community 0 - "controllers/ClienteController.php"
Cohesion: 0.08
Nodes (31): cliApiDiaDetalle(), cliApiEditarPerfil(), cliApiEditarPeso(), cliApiGuardarMeta(), cliApiGuardarPregunta(), cliApiPesoHistorial(), cliApiProgresoEjercicios(), cliApiProgresoHistorial() (+23 more)

### Community 1 - "pwa-sync.js"
Cohesion: 0.41
Nodes (12): checkInstalledAsync(), dequeue(), desinstalarXprodApp(), enqueue(), ensurePillInDOM(), flushQueue(), getQueue(), initSW() (+4 more)

### Community 4 - "EntrenadorController.php"
Cohesion: 0.08
Nodes (23): cliDashboard(), entActualizarMedidas(), entApiDiaDetalle(), entApiDiasAtleta(), entApiGuardarMensualidad(), entApiPagosCliente(), entApiProgresoEjercicios(), entApiProgresoHistorial() (+15 more)

### Community 6 - "progreso.php"
Cohesion: 0.33
Nodes (10): inferirEquipamientoProgreso(), inferirGrupoMuscularProgreso(), obtenerDetalleDia(), obtenerEjerciciosProgramados(), obtenerFechasEntrenadasMes(), obtenerFechasIncompletasMes(), obtenerHistorialDia(), obtenerProgresoEjercicios() (+2 more)

### Community 8 - "views/auth/login.php"
Cohesion: 0.05
Nodes (37): boxMetaOtros, boxSubHipertrofia, btnPesoMinus, btnPesoPlus, btnRecovery, btnRulerMinus, btnRulerPlus, btnSkipGoals (+29 more)

### Community 9 - "views/entrenador/crear_rutina.php"
Cohesion: 0.07
Nodes (43): actualizarHintMancuerna(), addExercise(), addSet(), cambiarImplemento(), cancelarReordenarCompacto(), cargarDiasAtleta(), catalogoEjercicios, categoriasDB (+35 more)

### Community 10 - "redirect"
Cohesion: 0.17
Nodes (13): {closure#1}(), cliModificarRutina(), cliRutinaLista(), entApiAprobarAtleta(), entAsignarRutina(), entEstadisticasCliente(), entGraficaEjercicio(), entPerfilCliente() (+5 more)

### Community 12 - "views/cliente/estadisticas.php"
Cohesion: 0.09
Nodes (27): abrirHistorialPeso(), abrirModalDia(), asegurarPortalBody(), badgeSerieTexto(), badgeSerieTitulo(), cambiarSerie(), cambiarTren(), cambiarVersion() (+19 more)

### Community 15 - "views/cliente/rutina_dia.php"
Cohesion: 0.07
Nodes (40): abrirModalAgregarEjercicioAtleta(), abrirModalConfirmarRevertir(), abrirModalPosponerRutina(), abrirModalReemplazoCliente(), actualizarBarraCambios(), actualizarBotonesModoEdicion(), actualizarEstadoEjercicio(), actualizarProgreso() (+32 more)

### Community 18 - "views/cliente/perfil.php"
Cohesion: 0.11
Nodes (13): copiarDatosPago(), draw(), fbCopy(), init(), mostrarToast(), open(), resetSegModal(), scrollToWheelDate() (+5 more)

### Community 19 - "views/entrenador/perfil_cliente.php"
Cohesion: 0.09
Nodes (29): renderDiasMiniBarPhp(), abrirModalDia(), api(), asegurarPortalBody(), badgeSerieTexto(), badgeSerieTitulo(), cambiarSerie(), cambiarTren() (+21 more)

### Community 20 - "UsuarioModel"
Cohesion: 0.06
Nodes (35): authDoLogin(), authDoRecuperarIdentificar(), authDoRecuperarVerificarRespuesta(), authDoRegistro(), authGoogleCallback(), authGoogleRedirect(), authLogin(), authLogout() (+27 more)

### Community 21 - "configuracion.php"
Cohesion: 0.24
Nodes (4): closePanel(), closePwdModal(), eliminarAtleta(), submitPwd()

### Community 29 - "views/entrenador/asistencias.php"
Cohesion: 0.14
Nodes (23): abrirOpcionesEjercicioDrawer(), actualizarBadges(), actualizarEjercicioEnCache(), avatarBg(), cargarDatosPorFecha(), cerrarOpcionesDrawer(), ejecutarEditarParametros(), ejecutarEliminarEjercicio() (+15 more)

### Community 32 - "assets/js/cookies.js"
Cohesion: 0.30
Nodes (12): applyConsent(), buildBanner(), buildModal(), getDefaultPreferences(), hideBanner(), init(), loadAnalyticsScripts(), loadConsent() (+4 more)

### Community 40 - "views/auth/recuperar.php"
Cohesion: 0.40
Nodes (8): animateTransition(), procesarPaso1(), procesarPaso2(), procesarPaso3(), procesarPaso4(), setButtonLoading(), showAlert(), volverPaso()

### Community 47 - "MetodoPagoModel"
Cohesion: 0.27
Nodes (3): entEliminarMetodo(), entGuardarMetodo(), MetodoPagoModel

### Community 50 - "assets/js/app.js"
Cohesion: 0.29
Nodes (5): clienteForm, loginForm, medidasForm, regForm, Xp

### Community 60 - "Product"
Cohesion: 0.18
Nodes (10): Brand Commitments, Capabilities and Constraints, Evidence on Hand, Operating Context, Platform, Positioning, Product, Product Principles (+2 more)

### Community 67 - "positionRegWheel"
Cohesion: 0.40
Nodes (5): ensureWheelPositioned(), initRegWheel(), positionRegWheel(), syncRegWheelDate(), updateRegWheelCol()

### Community 68 - "views/entrenador/metodos_pago.php"
Cohesion: 0.60
Nodes (3): editMethod(), resetForm(), toggleFields()

### Community 69 - "core/helpers.php"
Cohesion: 0.12
Nodes (21): cliApiLeerNotificaciones(), cliApiMarcarNotificacion(), cliApiNotificaciones(), cliIniciarRutina(), cliNotificaciones(), entApiLeerNotificaciones(), entApiMarcarNotificacion(), entApiNotificaciones() (+13 more)

### Community 79 - "getDB"
Cohesion: 0.13
Nodes (38): getDB(), authDoRecuperarCambiar(), cliApiNuevoPeso(), cliGuardarModificacion(), cliGuardarModificacionDirecta(), cliGuardarPago(), cliMarcarRutina(), cliReordenarEjercicios() (+30 more)

### Community 93 - "footer.php"
Cohesion: 0.40
Nodes (3): closeLegalModal(), guardarPreferenciasCookies(), LEGAL_CONTENTS

### Community 94 - "views/layout/header.php"
Cohesion: 0.30
Nodes (10): closeXpNotifDropdown(), toggleXpNotifDropdown(), xpClickNotifItem(), xpEscapeHtml(), xpFetchNotificaciones(), xpMarcarTodoLeido(), xpNotifsCache, xpPositionDropdown() (+2 more)

### Community 178 - "submitRegistration"
Cohesion: 0.29
Nodes (7): clearRegErrors(), normalizarCedula(), postApi(), setBtn(), setRegError(), submitRegistration(), switchView()

## Knowledge Gaps
- **48 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `getDB()` connect `getDB` to `controllers/ClienteController.php`, `EntrenadorController.php`, `core/helpers.php`, `.normalizeBreaks`, `MetaModel`, `UsuarioSistemaModel`, `redirect`, `ClienteModel`, `MetodoPagoModel`, `SesionAsistenciaModel`, `UsuarioModel`, `ObjetivoModel`, `PesoHistorialModel`, `RutinaModel`, `PagoModel`?**
  _High betweenness centrality (0.125) - this node is a cross-community bridge._
- **Are the 105 inferred relationships involving `getDB()` (e.g. with `env()` and `authDoRecuperarCambiar()`) actually correct?**
  _`getDB()` has 105 INFERRED edges - model-reasoned connections that need verification._
- **Should `controllers/ClienteController.php` be split into smaller, more focused modules?**
  _Cohesion score 0.08258258258258258 - nodes in this community are weakly interconnected._
- **Why does `PHPMailer` connect `PHPMailer` to `.strpos`, `SMTP`, `.normalizeBreaks`, `.addOrEnqueueAnAddress`, `.preSend`, `.lang`?**
  _High betweenness centrality (0.092) - this node is a cross-community bridge._
- **Are the 54 inferred relationships involving `jsonOut()` (e.g. with `authDoLogin()` and `authDoRecuperarCambiar()`) actually correct?**
  _`jsonOut()` has 54 INFERRED edges - model-reasoned connections that need verification._
- **Should `PHPMailer` be split into smaller, more focused modules?**
  _Cohesion score 0.053544494720965306 - nodes in this community are weakly interconnected._
- **Why does `sendEmail()` connect `PHPMailer` to `.lang`, `UsuarioModel`, `core/helpers.php`?**
  _High betweenness centrality (0.054) - this node is a cross-community bridge._