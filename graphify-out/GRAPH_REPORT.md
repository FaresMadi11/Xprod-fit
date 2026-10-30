# Graph Report - Xprod Fit  (2026-10-30)

## Corpus Check
- 107 files · ~226,748 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 12 file(s) not represented in the graph (top: (none) 7, .css 2, .conf 1)

## Summary
- 1038 nodes · 2177 edges · 77 communities (28 shown, 49 thin omitted)
- Extraction: 77% EXTRACTED · 23% INFERRED · 0% AMBIGUOUS · INFERRED: 497 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `660fbbe8`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- controllers/ClienteController.php
- pwa-sync.js
- PHPMailer
- SMTP
- login.php
- SimpleXLSX
- progreso.php
- .strlen
- views/entrenador/crear_rutina.php
- EntrenadorController.php
- views/cliente/estadisticas.php
- sw.js
- submitRegistration
- views/cliente/rutina_dia.php
- positionRegWheel
- guardarMediaPermanente
- views/cliente/perfil.php
- views/entrenador/perfil_cliente.php
- UsuarioModel
- configuracion.php
- .setError
- clearRecoveryErrors
- login.php
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
- views/entrenador/metodos_pago.php
- core/helpers.php
- MetaModel
- UsuarioSistemaModel
- views/cliente/modificar_rutina.php
- views/entrenador/clientes.php
- jsonOut
- footer.php
- views/layout/header.php
- registrar_cliente.php

## God Nodes (most connected - your core abstractions)
1. `PHPMailer` - 131 edges
2. `getDB()` - 117 edges
3. `csrfCheck()` - 55 edges
4. `jsonOut()` - 55 edges
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
- `getDB()` --calls--> `env()`  [INFERRED]
  config/database.php → core/env.php

## Import Cycles
- None detected.

## Communities (77 total, 49 thin omitted)

### Community 0 - "controllers/ClienteController.php"
Cohesion: 0.09
Nodes (29): cliApiCambiarPassword(), cliApiDiaDetalle(), cliApiEditarPerfil(), cliApiGuardarMeta(), cliApiGuardarPregunta(), cliApiPesoHistorial(), cliApiProgresoEjercicios(), cliApiProgresoHistorial() (+21 more)

### Community 1 - "pwa-sync.js"
Cohesion: 0.37
Nodes (13): checkInstalledAsync(), dequeue(), desinstalarXprodApp(), enqueue(), ensurePillInDOM(), flushQueue(), getQueue(), initSW() (+5 more)

### Community 4 - "login.php"
Cohesion: 0.05
Nodes (37): boxMetaOtros, boxSubHipertrofia, btnPesoMinus, btnPesoPlus, btnRecovery, btnRulerMinus, btnRulerPlus, btnSkipGoals (+29 more)

### Community 6 - "progreso.php"
Cohesion: 0.22
Nodes (15): coincideGrupoMuscularProgreso(), construirSnapshotEstadisticasOffline(), inferirEquipamientoProgreso(), inferirGrupoMuscularProgreso(), normalizarTextoProgreso(), obtenerDetalleDia(), obtenerEjerciciosProgramados(), obtenerEstadoDiasSemanaAtleta() (+7 more)

### Community 9 - "views/entrenador/crear_rutina.php"
Cohesion: 0.05
Nodes (67): abrirSheetPlantillas(), actualizarBotonPlantilla(), actualizarCardioSummary(), actualizarHintMancuerna(), actualizarNumerosPasosCardio(), addExercise(), addSet(), agregarPasoCardio() (+59 more)

### Community 10 - "EntrenadorController.php"
Cohesion: 0.07
Nodes (45): getDB(), cliModificarRutina(), cliRutinaLista(), calcularFirmaPlantilla(), entApiAprobarAtleta(), entApiCatalogoList(), entApiDiaDetalle(), entApiEliminarPlantillaCatalogo() (+37 more)

### Community 12 - "views/cliente/estadisticas.php"
Cohesion: 0.08
Nodes (33): abrirHistorialPeso(), abrirModalDia(), aplicarDatosHistorialEjercicio(), asegurarPortalBody(), badgeSerieTexto(), badgeSerieTitulo(), cambiarSerie(), cambiarTren() (+25 more)

### Community 14 - "submitRegistration"
Cohesion: 0.29
Nodes (7): clearRegErrors(), normalizarCedula(), postApi(), setBtn(), setRegError(), submitRegistration(), switchView()

### Community 15 - "views/cliente/rutina_dia.php"
Cohesion: 0.07
Nodes (40): abrirModalAgregarEjercicioAtleta(), abrirModalConfirmarRevertir(), abrirModalPosponerRutina(), abrirModalReemplazoCliente(), actualizarBarraCambios(), actualizarBotonesModoEdicion(), actualizarEstadoEjercicio(), actualizarProgreso() (+32 more)

### Community 16 - "positionRegWheel"
Cohesion: 0.40
Nodes (5): ensureWheelPositioned(), initRegWheel(), positionRegWheel(), syncRegWheelDate(), updateRegWheelCol()

### Community 17 - "guardarMediaPermanente"
Cohesion: 0.23
Nodes (14): cliApiSubirFoto(), cliGuardarPago(), entApiCatalogoAgregar(), entApiCatalogoEditar(), entApiTiendaAgregar(), entApiTiendaBorrar(), entApiTiendaEditar(), resolverCategoriaId() (+6 more)

### Community 18 - "views/cliente/perfil.php"
Cohesion: 0.11
Nodes (13): copiarDatosPago(), draw(), fbCopy(), init(), mostrarToast(), open(), resetSegModal(), scrollToWheelDate() (+5 more)

### Community 19 - "views/entrenador/perfil_cliente.php"
Cohesion: 0.08
Nodes (30): abrirModalDia(), api(), aplicarHistorialEjercicioEnt(), asegurarPortalBody(), badgeSerieTexto(), badgeSerieTitulo(), cambiarSerie(), cambiarTren() (+22 more)

### Community 20 - "UsuarioModel"
Cohesion: 0.06
Nodes (42): authDoLogin(), authDoRecuperarIdentificar(), authDoRecuperarVerificarRespuesta(), authDoRegistro(), authGoogleCallback(), authGoogleRedirect(), authLogin(), authLogout() (+34 more)

### Community 21 - "configuracion.php"
Cohesion: 0.21
Nodes (6): actualizarUISelectorTema(), closePanel(), closePwdModal(), eliminarAtleta(), solicitarCambioTema(), submitPwd()

### Community 26 - "login.php"
Cohesion: 0.67
Nodes (4): animate(), drawFrame(), initParticles(), resize()

### Community 29 - "views/entrenador/asistencias.php"
Cohesion: 0.13
Nodes (24): abrirOpcionesEjercicioDrawer(), actualizarBadges(), actualizarEjercicioEnCache(), avatarBg(), cargarDatosPorFecha(), cerrarOpcionesDrawer(), ejecutarEditarParametros(), ejecutarEliminarEjercicio() (+16 more)

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

### Community 68 - "views/entrenador/metodos_pago.php"
Cohesion: 0.60
Nodes (3): editMethod(), resetForm(), toggleFields()

### Community 69 - "core/helpers.php"
Cohesion: 0.13
Nodes (18): cliApiLeerNotificaciones(), cliApiMarcarNotificacion(), cliApiNotificaciones(), cliNotificaciones(), entApiLeerNotificaciones(), entApiMarcarNotificacion(), entApiNotificaciones(), entNotificaciones() (+10 more)

### Community 79 - "jsonOut"
Cohesion: 0.11
Nodes (32): authDoRecuperarCambiar(), {closure#1}(), cliApiEditarPeso(), cliApiNuevoPeso(), cliGuardarMedidas(), cliGuardarModificacion(), cliIniciarRutina(), cliMarcarRutina() (+24 more)

### Community 93 - "footer.php"
Cohesion: 0.40
Nodes (3): closeLegalModal(), guardarPreferenciasCookies(), LEGAL_CONTENTS

### Community 94 - "views/layout/header.php"
Cohesion: 0.30
Nodes (10): closeXpNotifDropdown(), toggleXpNotifDropdown(), xpClickNotifItem(), xpEscapeHtml(), xpFetchNotificaciones(), xpMarcarTodoLeido(), xpNotifsCache, xpPositionDropdown() (+2 more)

### Community 170 - "registrar_cliente.php"
Cohesion: 0.22
Nodes (11): actualizarAltura(), actualizarPeso(), ajustarAltura(), ajustarPeso(), clearErrors(), setError(), submitAtletaForm(), switchStep() (+3 more)

## Knowledge Gaps
- **49 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `getDB()` connect `EntrenadorController.php` to `controllers/ClienteController.php`, `core/helpers.php`, `MetaModel`, `UsuarioSistemaModel`, `ClienteModel`, `MetodoPagoModel`, `jsonOut`, `guardarMediaPermanente`, `SesionAsistenciaModel`, `UsuarioModel`, `ObjetivoModel`, `PesoHistorialModel`, `RutinaModel`, `PagoModel`?**
  _High betweenness centrality (0.125) - this node is a cross-community bridge._
- **Are the 115 inferred relationships involving `getDB()` (e.g. with `env()` and `authDoRecuperarCambiar()`) actually correct?**
  _`getDB()` has 115 INFERRED edges - model-reasoned connections that need verification._
- **Should `controllers/ClienteController.php` be split into smaller, more focused modules?**
  _Cohesion score 0.09090909090909091 - nodes in this community are weakly interconnected._
- **Why does `PHPMailer` connect `PHPMailer` to `.mailSend`, `SMTP`, `.strlen`, `.addOrEnqueueAnAddress`, `.preSend`, `.setError`?**
  _High betweenness centrality (0.089) - this node is a cross-community bridge._
- **Are the 52 inferred relationships involving `csrfCheck()` (e.g. with `authDoLogin()` and `authDoRecuperarCambiar()`) actually correct?**
  _`csrfCheck()` has 52 INFERRED edges - model-reasoned connections that need verification._
- **Should `PHPMailer` be split into smaller, more focused modules?**
  _Cohesion score 0.05782312925170068 - nodes in this community are weakly interconnected._
- **Why does `SimpleXLSX` connect `SimpleXLSX` to `.strlen`?**
  _High betweenness centrality (0.057) - this node is a cross-community bridge._