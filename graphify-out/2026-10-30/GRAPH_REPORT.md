# Graph Report - Xprod Fit  (2026-10-30)

## Corpus Check
- 107 files · ~223,202 words
- Verdict: corpus is large enough that graph structure adds value.
- Unclassified: 12 file(s) not represented in the graph (top: (none) 7, .css 2, .conf 1)

## Summary
- 974 nodes · 2075 edges · 74 communities (26 shown, 48 thin omitted)
- Extraction: 76% EXTRACTED · 24% INFERRED · 0% AMBIGUOUS · INFERRED: 490 edges (avg confidence: 0.85)
- Token cost: 0 input · 0 output

## Graph Freshness
- Built from commit: `fe7e1039`
- Run `git rev-parse HEAD` and compare to check if the graph is stale.
- Run `graphify update .` after code changes (no API cost).

## Community Hubs (Navigation)
- controllers/ClienteController.php
- pwa-sync.js
- PHPMailer
- SMTP
- redirect
- SimpleXLSX
- progreso.php
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
- .setError
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
2. `getDB()` - 115 edges
3. `jsonOut()` - 55 edges
4. `csrfCheck()` - 54 edges
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

## Communities (74 total, 48 thin omitted)

### Community 0 - "controllers/ClienteController.php"
Cohesion: 0.08
Nodes (30): cliApiCambiarPassword(), cliApiDiaDetalle(), cliApiEditarPerfil(), cliApiEditarPeso(), cliApiGuardarMeta(), cliApiGuardarPregunta(), cliApiNuevoPeso(), cliApiPesoHistorial() (+22 more)

### Community 1 - "pwa-sync.js"
Cohesion: 0.37
Nodes (13): checkInstalledAsync(), dequeue(), desinstalarXprodApp(), enqueue(), ensurePillInDOM(), flushQueue(), getQueue(), initSW() (+5 more)

### Community 4 - "redirect"
Cohesion: 0.20
Nodes (10): {closure#1}(), cliModificarRutina(), cliRutinaLista(), entAsignarRutina(), entEstadisticasCliente(), entPerfilCliente(), entRevisarModificacion(), redirect() (+2 more)

### Community 6 - "progreso.php"
Cohesion: 0.22
Nodes (15): coincideGrupoMuscularProgreso(), construirSnapshotEstadisticasOffline(), inferirEquipamientoProgreso(), inferirGrupoMuscularProgreso(), normalizarTextoProgreso(), obtenerDetalleDia(), obtenerEjerciciosProgramados(), obtenerEstadoDiasSemanaAtleta() (+7 more)

### Community 9 - "views/entrenador/crear_rutina.php"
Cohesion: 0.05
Nodes (65): abrirSheetPlantillas(), actualizarBotonPlantilla(), actualizarCardioSummary(), actualizarHintMancuerna(), actualizarNumerosPasosCardio(), addExercise(), addSet(), agregarPasoCardio() (+57 more)

### Community 10 - "EntrenadorController.php"
Cohesion: 0.08
Nodes (24): entApiAprobarAtleta(), entApiDiaDetalle(), entApiEliminarPlantillaCatalogo(), entApiGuardarMensualidad(), entApiGuardarPlantillaCatalogo(), entApiGuardarTemaSistema(), entApiListarPlantillasCatalogo(), entApiPagosCliente() (+16 more)

### Community 12 - "views/cliente/estadisticas.php"
Cohesion: 0.08
Nodes (33): abrirHistorialPeso(), abrirModalDia(), aplicarDatosHistorialEjercicio(), asegurarPortalBody(), badgeSerieTexto(), badgeSerieTitulo(), cambiarSerie(), cambiarTren() (+25 more)

### Community 15 - "views/cliente/rutina_dia.php"
Cohesion: 0.07
Nodes (40): abrirModalAgregarEjercicioAtleta(), abrirModalConfirmarRevertir(), abrirModalPosponerRutina(), abrirModalReemplazoCliente(), actualizarBarraCambios(), actualizarBotonesModoEdicion(), actualizarEstadoEjercicio(), actualizarProgreso() (+32 more)

### Community 17 - "guardarMediaPermanente"
Cohesion: 0.24
Nodes (13): cliApiSubirFoto(), cliGuardarPago(), entApiCatalogoAgregar(), entApiCatalogoEditar(), entApiTiendaAgregar(), entApiTiendaBorrar(), entApiTiendaEditar(), comprimirImagenGD() (+5 more)

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
Cohesion: 0.21
Nodes (6): actualizarUISelectorTema(), closePanel(), closePwdModal(), eliminarAtleta(), solicitarCambioTema(), submitPwd()

### Community 26 - "login.php"
Cohesion: 0.60
Nodes (4): animate(), drawFrame(), initParticles(), resize()

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

### Community 68 - "views/entrenador/metodos_pago.php"
Cohesion: 0.60
Nodes (3): editMethod(), resetForm(), toggleFields()

### Community 69 - "core/helpers.php"
Cohesion: 0.11
Nodes (21): cliApiLeerNotificaciones(), cliApiMarcarNotificacion(), cliApiNotificaciones(), cliIniciarRutina(), cliNotificaciones(), entApiLeerNotificaciones(), entApiMarcarNotificacion(), entApiNotificaciones() (+13 more)

### Community 79 - "jsonOut"
Cohesion: 0.13
Nodes (36): getDB(), authDoRecuperarCambiar(), cliGuardarDias(), cliGuardarMedidas(), cliGuardarModificacion(), cliGuardarModificacionDirecta(), cliMarcarRutina(), cliPosponerRutina() (+28 more)

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
- **48 thin communities (<3 nodes) omitted from report** — run `graphify query` to explore isolated nodes.

## Suggested Questions
_Questions this graph is uniquely positioned to answer:_

- **Why does `getDB()` connect `jsonOut` to `controllers/ClienteController.php`, `redirect`, `core/helpers.php`, `MetaModel`, `UsuarioSistemaModel`, `EntrenadorController.php`, `ClienteModel`, `MetodoPagoModel`, `guardarMediaPermanente`, `SesionAsistenciaModel`, `UsuarioModel`, `ObjetivoModel`, `PesoHistorialModel`, `RutinaModel`, `PagoModel`?**
  _High betweenness centrality (0.135) - this node is a cross-community bridge._
- **Are the 113 inferred relationships involving `getDB()` (e.g. with `env()` and `authDoRecuperarCambiar()`) actually correct?**
  _`getDB()` has 113 INFERRED edges - model-reasoned connections that need verification._
- **Should `controllers/ClienteController.php` be split into smaller, more focused modules?**
  _Cohesion score 0.08403361344537816 - nodes in this community are weakly interconnected._
- **Why does `PHPMailer` connect `PHPMailer` to `.strpos`, `SMTP`, `.addOrEnqueueAnAddress`, `.createBody`, `.preSend`, `.setError`?**
  _High betweenness centrality (0.093) - this node is a cross-community bridge._
- **Are the 54 inferred relationships involving `jsonOut()` (e.g. with `authDoLogin()` and `authDoRecuperarCambiar()`) actually correct?**
  _`jsonOut()` has 54 INFERRED edges - model-reasoned connections that need verification._
- **Should `PHPMailer` be split into smaller, more focused modules?**
  _Cohesion score 0.06363636363636363 - nodes in this community are weakly interconnected._
- **Why does `sendEmail()` connect `.addOrEnqueueAnAddress` to `PHPMailer`, `UsuarioModel`, `core/helpers.php`?**
  _High betweenness centrality (0.056) - this node is a cross-community bridge._