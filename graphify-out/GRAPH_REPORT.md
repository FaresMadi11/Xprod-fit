# Graph Report - Xprod Fit  (2026-10-29)

## Corpus Check
- 107 files · ~222,084 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 12 file(s) not represented in the graph (top: (none) 7, .css 2, .conf 1)

## Summary
- 1024 nodes · 2130 edges · 77 communities (28 shown, 49 thin omitted)
- Extraction: 77% EXTRACTED · 23% INFERRED · 0% AMBIGUOUS · INFERRED: 489 edges (avg confidence: 0.85)
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
- SimpleXLSX
- progreso.php
- views/auth/login.php
- views/entrenador/crear_rutina.php
- EntrenadorController.php
- .addOrEnqueueAnAddress
- views/cliente/estadisticas.php
- sw.js
- views/cliente/rutina_dia.php
- guardarMediaPermanente
- views/cliente/perfil.php
- views/entrenador/perfil_cliente.php
- UsuarioModel
- configuracion.php
- drawFrame
- views/entrenador/asistencias.php
- assets/js/cookies.js
- .strpos
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
- jsonOut
- clearRecoveryErrors
- footer.php
- views/layout/header.php
- registrar_cliente.php
- submitRegistration

## God Nodes (most connected - your core abstractions)
1. `PHPMailer` - 131 edges
2. `getDB()` - 114 edges
3. `jsonOut()` - 55 edges
4. `csrfCheck()` - 53 edges
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
- `entApiTiendaSecciones()` --calls--> `getDB()`  [INFERRED]
  controllers/EntrenadorController.php → config/database.php

## Import Cycles
- None detected.

## Communities (77 total, 49 thin omitted)

### Community 0 - "controllers/ClienteController.php"
Cohesion: 0.09
Nodes (29): cliApiCambiarPassword(), cliApiDiaDetalle(), cliApiEditarPerfil(), cliApiEditarPeso(), cliApiGuardarMeta(), cliApiGuardarPregunta(), cliApiPesoHistorial(), cliApiProgresoEjercicios() (+21 more)

### Community 1 - "pwa-sync.js"
Cohesion: 0.37
Nodes (13): checkInstalledAsync(), dequeue(), desinstalarXprodApp(), enqueue(), ensurePillInDOM(), flushQueue(), getQueue(), initSW() (+5 more)

### Community 6 - "progreso.php"
Cohesion: 0.24
Nodes (13): coincideGrupoMuscularProgreso(), construirSnapshotEstadisticasOffline(), inferirEquipamientoProgreso(), inferirGrupoMuscularProgreso(), normalizarTextoProgreso(), obtenerDetalleDia(), obtenerEjerciciosProgramados(), obtenerFechasEntrenadasMes() (+5 more)

### Community 8 - "views/auth/login.php"
Cohesion: 0.05
Nodes (37): boxMetaOtros, boxSubHipertrofia, btnPesoMinus, btnPesoPlus, btnRecovery, btnRulerMinus, btnRulerPlus, btnSkipGoals (+29 more)

### Community 9 - "views/entrenador/crear_rutina.php"
Cohesion: 0.05
Nodes (65): abrirSheetPlantillas(), actualizarBotonPlantilla(), actualizarCardioSummary(), actualizarHintMancuerna(), actualizarNumerosPasosCardio(), addExercise(), addSet(), agregarPasoCardio() (+57 more)

### Community 10 - "EntrenadorController.php"
Cohesion: 0.07
Nodes (29): cliModificarRutina(), cliRutinaLista(), entApiAprobarAtleta(), entApiDiaDetalle(), entApiEliminarPlantillaCatalogo(), entApiGuardarMensualidad(), entApiGuardarPlantillaCatalogo(), entApiListarPlantillasCatalogo() (+21 more)

### Community 12 - "views/cliente/estadisticas.php"
Cohesion: 0.08
Nodes (33): abrirHistorialPeso(), abrirModalDia(), aplicarDatosHistorialEjercicio(), asegurarPortalBody(), badgeSerieTexto(), badgeSerieTitulo(), cambiarSerie(), cambiarTren() (+25 more)

### Community 15 - "views/cliente/rutina_dia.php"
Cohesion: 0.07
Nodes (40): abrirModalAgregarEjercicioAtleta(), abrirModalConfirmarRevertir(), abrirModalPosponerRutina(), abrirModalReemplazoCliente(), actualizarBarraCambios(), actualizarBotonesModoEdicion(), actualizarEstadoEjercicio(), actualizarProgreso() (+32 more)

### Community 17 - "guardarMediaPermanente"
Cohesion: 0.17
Nodes (20): getDB(), cliApiSubirFoto(), cliDashboard(), cliGuardarPago(), entApiCatalogoAgregar(), entApiCatalogoEditar(), entApiDiasAtleta(), entApiTiendaAgregar() (+12 more)

### Community 18 - "views/cliente/perfil.php"
Cohesion: 0.11
Nodes (13): copiarDatosPago(), draw(), fbCopy(), init(), mostrarToast(), open(), resetSegModal(), scrollToWheelDate() (+5 more)

### Community 19 - "views/entrenador/perfil_cliente.php"
Cohesion: 0.08
Nodes (31): renderDiasMiniBarPhp(), abrirModalDia(), api(), aplicarHistorialEjercicioEnt(), asegurarPortalBody(), badgeSerieTexto(), badgeSerieTitulo(), cambiarSerie() (+23 more)

### Community 20 - "UsuarioModel"
Cohesion: 0.06
Nodes (43): authDoLogin(), authDoRecuperarIdentificar(), authDoRecuperarVerificarRespuesta(), authDoRegistro(), authGoogleCallback(), authGoogleRedirect(), authLogin(), authLogout() (+35 more)

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
Nodes (19): cliApiLeerNotificaciones(), cliApiMarcarNotificacion(), cliApiNotificaciones(), cliNotificaciones(), entApiLeerNotificaciones(), entApiMarcarNotificacion(), entApiNotificaciones(), entCrearRutina() (+11 more)

### Community 79 - "jsonOut"
Cohesion: 0.10
Nodes (38): authDoRecuperarCambiar(), {closure#1}(), cliApiNuevoPeso(), cliGuardarMedidas(), cliGuardarModificacion(), cliGuardarSesion(), cliIniciarRutina(), cliMarcarRutina() (+30 more)

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
- **49 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `getDB()` connect `guardarMediaPermanente` to `controllers/ClienteController.php`, `core/helpers.php`, `MetaModel`, `UsuarioSistemaModel`, `EntrenadorController.php`, `ClienteModel`, `jsonOut`, `MetodoPagoModel`, `SesionAsistenciaModel`, `UsuarioModel`, `ObjetivoModel`, `PesoHistorialModel`, `RutinaModel`, `PagoModel`?**
  _High betweenness centrality (0.115) - this node is a cross-community bridge._
- **Are the 112 inferred relationships involving `getDB()` (e.g. with `env()` and `authDoRecuperarCambiar()`) actually correct?**
  _`getDB()` has 112 INFERRED edges - model-reasoned connections that need verification._
- **Should `controllers/ClienteController.php` be split into smaller, more focused modules?**
  _Cohesion score 0.0873440285204991 - nodes in this community are weakly interconnected._
- **Why does `PHPMailer` connect `PHPMailer` to `.strpos`, `SMTP`, `.addOrEnqueueAnAddress`, `.strlen`, `.preSend`, `.setError`?**
  _High betweenness centrality (0.088) - this node is a cross-community bridge._
- **Are the 54 inferred relationships involving `jsonOut()` (e.g. with `authDoLogin()` and `authDoRecuperarCambiar()`) actually correct?**
  _`jsonOut()` has 54 INFERRED edges - model-reasoned connections that need verification._
- **Should `PHPMailer` be split into smaller, more focused modules?**
  _Cohesion score 0.05975609756097561 - nodes in this community are weakly interconnected._
- **Why does `SimpleXLSX` connect `SimpleXLSX` to `.strlen`, `SMTP`, `.strpos`?**
  _High betweenness centrality (0.060) - this node is a cross-community bridge._