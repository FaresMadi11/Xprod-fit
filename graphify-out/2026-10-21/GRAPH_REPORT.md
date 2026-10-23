# Graph Report - Xprod Fit  (2026-10-21)

## Corpus Check
- 101 files · ~179,606 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 8 file(s) not represented in the graph (top: (none) 5, .css 2, .conf 1)

## Summary
- 892 nodes · 1784 edges · 72 communities (25 shown, 47 thin omitted)
- Extraction: 77% EXTRACTED · 23% INFERRED · 0% AMBIGUOUS · INFERRED: 407 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `eeb104e9`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- controllers/ClienteController.php
- redirect
- PHPMailer
- SMTP
- EntrenadorController.php
- SimpleXLSX
- progreso.php
- views/auth/login.php
- views/entrenador/crear_rutina.php
- .addOrEnqueueAnAddress
- views/cliente/estadisticas.php
- views/cliente/rutina_dia.php
- views/cliente/perfil.php
- views/entrenador/perfil_cliente.php
- UsuarioModel
- views/entrenador/configuracion.php
- views/entrenador/asistencias.php
- assets/js/cookies.js
- .strpos
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
2. `getDB()` - 99 edges
3. `jsonOut()` - 46 edges
4. `SMTP` - 46 edges
5. `SimpleXLSX` - 45 edges
6. `csrfCheck()` - 42 edges
7. `Exception` - 24 edges
8. `UsuarioModel` - 19 edges
9. `authDoRegistro()` - 16 edges
10. `redirect()` - 14 edges

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

## Communities (72 total, 47 thin omitted)

### Community 0 - "controllers/ClienteController.php"
Cohesion: 0.08
Nodes (31): cliApiCambiarPassword(), cliApiDiaDetalle(), cliApiEditarPerfil(), cliApiEditarPeso(), cliApiGuardarMeta(), cliApiGuardarPregunta(), cliApiNuevoPeso(), cliApiPesoHistorial() (+23 more)

### Community 1 - "redirect"
Cohesion: 0.18
Nodes (12): authLogout(), cliModificarRutina(), cliRutinaLista(), entApiAprobarAtleta(), entAsignarRutina(), entEstadisticasCliente(), entGraficaEjercicio(), entPerfilCliente() (+4 more)

### Community 4 - "EntrenadorController.php"
Cohesion: 0.09
Nodes (20): entActualizarMedidas(), entApiDiaDetalle(), entApiGuardarMensualidad(), entApiPagosCliente(), entApiProgresoEjercicios(), entApiProgresoHistorial(), entApiTiendaSecciones(), entAsistencias() (+12 more)

### Community 6 - "progreso.php"
Cohesion: 0.32
Nodes (10): {closure#2}(), inferirEquipamientoProgreso(), inferirGrupoMuscularProgreso(), obtenerDetalleDia(), obtenerEjerciciosProgramados(), obtenerFechasEntrenadasMes(), obtenerHistorialDia(), obtenerProgresoEjercicios() (+2 more)

### Community 8 - "views/auth/login.php"
Cohesion: 0.05
Nodes (37): boxMetaOtros, boxSubHipertrofia, btnPesoMinus, btnPesoPlus, btnRecovery, btnRulerMinus, btnRulerPlus, btnSkipGoals (+29 more)

### Community 9 - "views/entrenador/crear_rutina.php"
Cohesion: 0.08
Nodes (39): actualizarHintMancuerna(), addExercise(), addSet(), cambiarImplemento(), cancelarReordenarCompacto(), catalogoEjercicios, categoriasDB, catsDrillBack() (+31 more)

### Community 12 - "views/cliente/estadisticas.php"
Cohesion: 0.09
Nodes (28): e(), abrirHistorialPeso(), abrirModalDia(), asegurarPortalBody(), badgeSerieTexto(), badgeSerieTitulo(), cambiarSerie(), cambiarTren() (+20 more)

### Community 15 - "views/cliente/rutina_dia.php"
Cohesion: 0.10
Nodes (16): abrirModalReemplazoCliente(), actualizarEstadoEjercicio(), actualizarProgreso(), cancelarReordenarCompacto(), cerrarTodosModalesCliente(), confirmarReemplazo(), ejMap, eliminadosEnReordenar (+8 more)

### Community 18 - "views/cliente/perfil.php"
Cohesion: 0.11
Nodes (13): copiarDatosPago(), draw(), fbCopy(), init(), mostrarToast(), open(), resetSegModal(), scrollToWheelDate() (+5 more)

### Community 19 - "views/entrenador/perfil_cliente.php"
Cohesion: 0.09
Nodes (27): abrirModalDia(), api(), asegurarPortalBody(), badgeSerieTexto(), badgeSerieTitulo(), cambiarSerie(), cambiarTren(), currentBarrasPesos (+19 more)

### Community 20 - "UsuarioModel"
Cohesion: 0.06
Nodes (31): authDoLogin(), authDoRegistro(), authGoogleCallback(), authGoogleRedirect(), authLogin(), authRecuperar(), authRegistro(), authRegistroError() (+23 more)

### Community 21 - "views/entrenador/configuracion.php"
Cohesion: 0.15
Nodes (15): abrirCategoria(), abrirFormulario(), agregarPaso(), buscarGlobalCMS(), catalogoCompleto, closePanel(), closePwdModal(), crearCardEjercicio() (+7 more)

### Community 29 - "views/entrenador/asistencias.php"
Cohesion: 0.17
Nodes (15): actualizarBadges(), avatarBg(), cargarDatosPorFecha(), elegirFechaCalendario(), esc(), getGrupoIcono(), getImplementoBadge(), iniciarPolling() (+7 more)

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
Cohesion: 0.13
Nodes (20): cliApiLeerNotificaciones(), cliApiMarcarNotificacion(), cliApiNotificaciones(), cliNotificaciones(), entApiLeerNotificaciones(), entApiMarcarNotificacion(), entApiNotificaciones(), entNotificaciones() (+12 more)

### Community 79 - "getDB"
Cohesion: 0.13
Nodes (35): getDB(), authDoRecuperarCambiar(), authDoRecuperarIdentificar(), authDoRecuperarVerificarRespuesta(), cliGuardarModificacion(), cliGuardarPago(), cliGuardarSesion(), cliMarcarRutina() (+27 more)

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
- **47 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `getDB()` connect `getDB` to `controllers/ClienteController.php`, `redirect`, `EntrenadorController.php`, `core/helpers.php`, `MetaModel`, `UsuarioSistemaModel`, `ClienteModel`, `MetodoPagoModel`, `SesionAsistenciaModel`, `UsuarioModel`, `ObjetivoModel`, `PesoHistorialModel`, `RutinaModel`, `PagoModel`?**
  _High betweenness centrality (0.138) - this node is a cross-community bridge._
- **Are the 97 inferred relationships involving `getDB()` (e.g. with `env()` and `authDoRecuperarCambiar()`) actually correct?**
  _`getDB()` has 97 INFERRED edges - model-reasoned connections that need verification._
- **Should `controllers/ClienteController.php` be split into smaller, more focused modules?**
  _Cohesion score 0.07823613086770982 - nodes in this community are weakly interconnected._
- **Why does `PHPMailer` connect `PHPMailer` to `.strpos`, `SMTP`, `.normalizeBreaks`, `.addOrEnqueueAnAddress`, `.preSend`, `.lang`?**
  _High betweenness centrality (0.105) - this node is a cross-community bridge._
- **Are the 45 inferred relationships involving `jsonOut()` (e.g. with `authDoLogin()` and `authDoRecuperarCambiar()`) actually correct?**
  _`jsonOut()` has 45 INFERRED edges - model-reasoned connections that need verification._
- **Should `PHPMailer` be split into smaller, more focused modules?**
  _Cohesion score 0.06341463414634146 - nodes in this community are weakly interconnected._
- **Why does `sendEmail()` connect `.addOrEnqueueAnAddress` to `.lang`, `PHPMailer`, `UsuarioModel`, `core/helpers.php`?**
  _High betweenness centrality (0.062) - this node is a cross-community bridge._