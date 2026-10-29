# Graph Report - Xprod Fit  (2026-10-29)

## Corpus Check
- 107 files · ~218,298 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 12 file(s) not represented in the graph (top: (none) 7, .css 2, .conf 1)

## Summary
- 1001 nodes · 2072 edges · 81 communities (30 shown, 51 thin omitted)
- Extraction: 77% EXTRACTED · 23% INFERRED · 0% AMBIGUOUS · INFERRED: 481 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `968fe53a`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- controllers/ClienteController.php
- pwa-sync.js
- PHPMailer
- SMTP
- security.php
- SimpleXLSX
- progreso.php
- views/auth/login.php
- views/entrenador/crear_rutina.php
- redirect
- .addOrEnqueueAnAddress
- views/cliente/estadisticas.php
- sw.js
- AuthController.php
- views/cliente/rutina_dia.php
- guardarMediaPermanente
- views/cliente/perfil.php
- views/entrenador/perfil_cliente.php
- UsuarioModel
- configuracion.php
- .lang
- drawFrame
- views/entrenador/asistencias.php
- assets/js/cookies.js
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
- registrar_cliente.php
- submitRegistration

## God Nodes (most connected - your core abstractions)
1. `PHPMailer` - 131 edges
2. `getDB()` - 111 edges
3. `jsonOut()` - 55 edges
4. `csrfCheck()` - 51 edges
5. `SMTP` - 46 edges
6. `SimpleXLSX` - 45 edges
7. `Exception` - 24 edges
8. `UsuarioModel` - 20 edges
9. `redirect()` - 17 edges
10. `authDoRegistro()` - 16 edges

## Surprising Connections (you probably didn't know these)
- `cliApiEditarPerfil()` --calls--> `getDB()`  [INFERRED]
  controllers/ClienteController.php → config/database.php
- `cliApiGuardarPregunta()` --calls--> `getDB()`  [INFERRED]
  controllers/ClienteController.php → config/database.php
- `cliApiPesoHistorial()` --calls--> `getDB()`  [INFERRED]
  controllers/ClienteController.php → config/database.php
- `cliPago()` --calls--> `getDB()`  [INFERRED]
  controllers/ClienteController.php → config/database.php
- `authLogin()` --calls--> `getFlash()`  [INFERRED]
  controllers/AuthController.php → core/helpers.php

## Import Cycles
- None detected.

## Communities (81 total, 51 thin omitted)

### Community 0 - "controllers/ClienteController.php"
Cohesion: 0.09
Nodes (29): cliApiCambiarPassword(), cliApiDiaDetalle(), cliApiEditarPerfil(), cliApiEditarPeso(), cliApiGuardarPregunta(), cliApiNuevoPeso(), cliApiPesoHistorial(), cliApiProgresoEjercicios() (+21 more)

### Community 1 - "pwa-sync.js"
Cohesion: 0.37
Nodes (13): checkInstalledAsync(), dequeue(), desinstalarXprodApp(), enqueue(), ensurePillInDOM(), flushQueue(), getQueue(), initSW() (+5 more)

### Community 4 - "security.php"
Cohesion: 0.16
Nodes (16): authLogout(), getGoogleRedirectUri(), googleDecodeIdToken(), googleExchangeCode(), googleHttpGet(), googleHttpPost(), googleRedirect(), env() (+8 more)

### Community 6 - "progreso.php"
Cohesion: 0.22
Nodes (15): coincideGrupoMuscularProgreso(), construirSnapshotEstadisticasOffline(), inferirEquipamientoProgreso(), inferirGrupoMuscularProgreso(), normalizarTextoProgreso(), obtenerDetalleDia(), obtenerEjerciciosProgramados(), obtenerEstadoDiasSemanaAtleta() (+7 more)

### Community 8 - "views/auth/login.php"
Cohesion: 0.05
Nodes (37): boxMetaOtros, boxSubHipertrofia, btnPesoMinus, btnPesoPlus, btnRecovery, btnRulerMinus, btnRulerPlus, btnSkipGoals (+29 more)

### Community 9 - "views/entrenador/crear_rutina.php"
Cohesion: 0.06
Nodes (45): actualizarHintMancuerna(), addExercise(), addSet(), cambiarImplemento(), cancelarReordenarCompacto(), cargarDiasAtleta(), catalogoEjercicios, categoriasDB (+37 more)

### Community 10 - "redirect"
Cohesion: 0.17
Nodes (13): {closure#1}(), cliModificarRutina(), cliRutinaLista(), entApiAprobarAtleta(), entAsignarRutina(), entEstadisticasCliente(), entGraficaEjercicio(), entPerfilCliente() (+5 more)

### Community 12 - "views/cliente/estadisticas.php"
Cohesion: 0.08
Nodes (34): e(), abrirHistorialPeso(), abrirModalDia(), aplicarDatosHistorialEjercicio(), asegurarPortalBody(), badgeSerieTexto(), badgeSerieTitulo(), cambiarSerie() (+26 more)

### Community 14 - "AuthController.php"
Cohesion: 0.14
Nodes (15): authGoogleCallback(), authGoogleRedirect(), authLogin(), authRecuperar(), authRegistro(), authRegistroError(), {closure#3}(), {closure#4}() (+7 more)

### Community 15 - "views/cliente/rutina_dia.php"
Cohesion: 0.07
Nodes (40): abrirModalAgregarEjercicioAtleta(), abrirModalConfirmarRevertir(), abrirModalPosponerRutina(), abrirModalReemplazoCliente(), actualizarBarraCambios(), actualizarBotonesModoEdicion(), actualizarEstadoEjercicio(), actualizarProgreso() (+32 more)

### Community 17 - "guardarMediaPermanente"
Cohesion: 0.25
Nodes (12): cliApiSubirFoto(), entApiCatalogoAgregar(), entApiCatalogoEditar(), entApiTiendaAgregar(), entApiTiendaBorrar(), entApiTiendaEditar(), comprimirImagenGD(), eliminarMediaPermanente() (+4 more)

### Community 18 - "views/cliente/perfil.php"
Cohesion: 0.11
Nodes (13): copiarDatosPago(), draw(), fbCopy(), init(), mostrarToast(), open(), resetSegModal(), scrollToWheelDate() (+5 more)

### Community 19 - "views/entrenador/perfil_cliente.php"
Cohesion: 0.08
Nodes (31): renderDiasMiniBarPhp(), abrirModalDia(), api(), aplicarHistorialEjercicioEnt(), asegurarPortalBody(), badgeSerieTexto(), badgeSerieTitulo(), cambiarSerie() (+23 more)

### Community 20 - "UsuarioModel"
Cohesion: 0.14
Nodes (14): authDoLogin(), authDoRecuperarCambiar(), authDoRecuperarIdentificar(), authDoRecuperarVerificarRespuesta(), authDoRegistro(), entGuardarCliente(), googleCallback(), getClientIp() (+6 more)

### Community 21 - "configuracion.php"
Cohesion: 0.24
Nodes (4): closePanel(), closePwdModal(), eliminarAtleta(), submitPwd()

### Community 26 - "drawFrame"
Cohesion: 0.67
Nodes (3): animate(), drawFrame(), resize()

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
Nodes (20): cliApiLeerNotificaciones(), cliApiMarcarNotificacion(), cliApiNotificaciones(), cliIniciarRutina(), cliNotificaciones(), entApiLeerNotificaciones(), entApiMarcarNotificacion(), entApiNotificaciones() (+12 more)

### Community 79 - "getDB"
Cohesion: 0.08
Nodes (52): getDB(), cliGuardarDias(), cliGuardarModificacion(), cliGuardarModificacionDirecta(), cliGuardarPago(), cliGuardarSesion(), cliMarcarRutina(), cliPosponerRutina() (+44 more)

### Community 93 - "footer.php"
Cohesion: 0.40
Nodes (3): closeLegalModal(), guardarPreferenciasCookies(), LEGAL_CONTENTS

### Community 94 - "views/layout/header.php"
Cohesion: 0.30
Nodes (10): closeXpNotifDropdown(), toggleXpNotifDropdown(), xpClickNotifItem(), xpEscapeHtml(), xpFetchNotificaciones(), xpMarcarTodoLeido(), xpNotifsCache, xpPositionDropdown() (+2 more)

### Community 170 - "registrar_cliente.php"
Cohesion: 0.22
Nodes (11): actualizarAltura(), actualizarPeso(), ajustarAltura(), ajustarPeso(), clearErrors(), setError(), submitAtletaForm(), switchStep() (+3 more)

### Community 178 - "submitRegistration"
Cohesion: 0.29
Nodes (7): clearRegErrors(), normalizarCedula(), postApi(), setBtn(), setRegError(), submitRegistration(), switchView()

## Knowledge Gaps
- **51 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `getDB()` connect `getDB` to `controllers/ClienteController.php`, `security.php`, `core/helpers.php`, `MetaModel`, `UsuarioSistemaModel`, `redirect`, `AuthController.php`, `MetodoPagoModel`, `ClienteModel`, `guardarMediaPermanente`, `SesionAsistenciaModel`, `UsuarioModel`, `ObjetivoModel`, `PesoHistorialModel`, `RutinaModel`, `PagoModel`?**
  _High betweenness centrality (0.119) - this node is a cross-community bridge._
- **Are the 109 inferred relationships involving `getDB()` (e.g. with `env()` and `authDoRecuperarCambiar()`) actually correct?**
  _`getDB()` has 109 INFERRED edges - model-reasoned connections that need verification._
- **Should `controllers/ClienteController.php` be split into smaller, more focused modules?**
  _Cohesion score 0.0873440285204991 - nodes in this community are weakly interconnected._
- **Why does `PHPMailer` connect `PHPMailer` to `SMTP`, `.strpos`, `.encodeString`, `.addOrEnqueueAnAddress`, `.preSend`, `.preSend`, `.lang`?**
  _High betweenness centrality (0.094) - this node is a cross-community bridge._
- **Are the 54 inferred relationships involving `jsonOut()` (e.g. with `authDoLogin()` and `authDoRecuperarCambiar()`) actually correct?**
  _`jsonOut()` has 54 INFERRED edges - model-reasoned connections that need verification._
- **Should `PHPMailer` be split into smaller, more focused modules?**
  _Cohesion score 0.056429232192414434 - nodes in this community are weakly interconnected._
- **Why does `sendEmail()` connect `.addOrEnqueueAnAddress` to `PHPMailer`, `security.php`, `core/helpers.php`?**
  _High betweenness centrality (0.055) - this node is a cross-community bridge._