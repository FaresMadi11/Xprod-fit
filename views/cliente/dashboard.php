<?php $pageTitle = 'Mi Panel'; require __DIR__ . '/../layout/header.php'; ?>

<?php
$pesoActual = (float)($medidas['peso_kg'] ?? 0);
$objetivo   = $medidas['objetivo'] ?? '';

// Calcular progreso de peso si hay meta y hay historial
$progressPct = 0;
if ($pesoInicial > 0 && $pesoActual > 0 && $pesoInicial !== $pesoActual) {
    $diff = abs($pesoInicial - $pesoActual);
    $progressPct = min(100, round(($diff / 10) * 100));
}

// ── Colección de frases motivadoras diarias (minimalistas) ──
$frasesMotivadoras = [
    ['frase' => 'La disciplina es el puente entre las metas y los logros.', 'autor' => 'Jim Rohn'],
    ['frase' => 'No cuentes los días, haz que los días cuenten.', 'autor' => 'Muhammad Ali'],
    ['frase' => 'Tu único límite es la mente. Si crees que puedes, lo lograrás.', 'autor' => 'Arnold Schwarzenegger'],
    ['frase' => 'La fuerza no viene de la capacidad física, sino de una voluntad indomable.', 'autor' => 'Mahatma Gandhi'],
    ['frase' => 'El dolor que sientes hoy es la fuerza que sentirás mañana.', 'autor' => 'Enfoque Pro'],
    ['frase' => 'La constancia vence lo que la dicha no alcanza.', 'autor' => 'Séneca'],
    ['frase' => 'No te detengas cuando estés cansado, detente cuando hayas terminado.', 'autor' => 'David Goggins'],
    ['frase' => 'El éxito se construye con pequeños esfuerzos repetidos cada día.', 'autor' => 'Robert Collier'],
    ['frase' => 'Convierte tus obstáculos en combustible para tu progreso.', 'autor' => 'Marco Aurelio'],
    ['frase' => 'Cada repetición cuenta. Cada gota de sudor suma.', 'autor' => 'Mentalidad Fit'],
    ['frase' => 'La motivación te pone en marcha, el hábito te mantiene avanzando.', 'autor' => 'Jim Ryun'],
    ['frase' => 'Tu cuerpo puede con casi todo, es tu mente a la que debes convencer.', 'autor' => 'Fuerza Mental'],
    ['frase' => 'Sé más fuerte que tus excusas más convincentes.', 'autor' => 'Xprod Fit'],
    ['frase' => 'El mejor proyecto en el que trabajarás eres tú mismo.', 'autor' => 'Superación'],
    ['frase' => 'Hoy es una nueva oportunidad para superar tu versión de ayer.', 'autor' => 'Constancia'],
    ['frase' => 'La gloria es el resultado de días oscuros de sacrificio silencioso.', 'autor' => 'Kobe Bryant'],
    ['frase' => 'El dolor de la disciplina pesa gramos; el del arrepentimiento, toneladas.', 'autor' => 'Jim Rohn'],
    ['frase' => 'No tienes que ser extremo, solo consistente.', 'autor' => 'Hábito Diario'],
    ['frase' => 'La mente es el músculo más importante en cada entrenamiento.', 'autor' => 'Bruce Lee'],
    ['frase' => 'El único mal entrenamiento es aquel que no sucedió.', 'autor' => 'Actitud'],
    ['frase' => 'Conquista tu mente y conquistarás cualquier reto.', 'autor' => 'Autocontrol'],
    ['frase' => 'La excelencia no es un acto, es un hábito.', 'autor' => 'Aristóteles'],
    ['frase' => 'Si no te desafía, no te cambiará.', 'autor' => 'Fred DeVito'],
    ['frase' => 'Entrena con propósito, descansa con orgullo.', 'autor' => 'Xprod Fit'],
    ['frase' => 'Lo que parece imposible hoy, mañana será tu calentamiento.', 'autor' => 'Progresión'],
    ['frase' => 'La paciencia y el esfuerzo crean obras maestras físicas.', 'autor' => 'Perseverancia'],
    ['frase' => 'No bajes la meta, aumenta el esfuerzo.', 'autor' => 'Determinación'],
    ['frase' => 'La verdadera victoria es la que se gana sobre uno mismo.', 'autor' => 'Filosofía Fit'],
    ['frase' => 'Cada día es una nueva oportunidad para ser un 1% mejor.', 'autor' => 'Kaizen'],
    ['frase' => 'El sudor de hoy es el triunfo del mañana.', 'autor' => 'Disciplina']
];

// Selección determinista de la frase según el día del año (cambia al azar cada día)
$indiceFrase = (intval(date('z')) + intval(date('Y')) * 7) % count($frasesMotivadoras);
$fraseDelDia = $frasesMotivadoras[$indiceFrase];
?>

<style>
/* ── Reset general de la pagina ── */
.xp-main { padding: 0 !important; }

/* ── Header superior ── */
.db-header {
  padding: 20px 20px 14px;
  display: flex; justify-content: space-between; align-items: flex-start;
}
.db-greeting { font-size: .82rem; color: #a3e635; font-weight: 500; margin-bottom: 2px; }
.db-welcome  { font-size: 1.6rem; font-weight: 900; color: #fff; line-height: 1.2; }
.db-actions  { display: flex; gap: 8px; align-items: center; margin-top: 4px; }
.db-icon-btn {
  width: 36px; height: 36px; border-radius: 50%;
  background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.1);
  display: grid; place-items: center; color: #fff; text-decoration: none;
  font-size: 1rem; transition: background .2s; position: relative;
}
.db-icon-btn:hover { background: rgba(255,255,255,0.14); }
.db-notif-dot {
  position: absolute; top: 6px; right: 6px;
  width: 8px; height: 8px; border-radius: 50%; background: #ef4444;
  border: 1.5px solid #0a0a0a;
}

/* ── Stat cards ── */
.db-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; padding: 0 16px 16px; }
@media (max-width: 480px) {
  .db-stats { grid-template-columns: 1fr 1fr; gap: 10px; }
}
.db-stat-card {
  background: var(--card-glass); border-radius: 20px;
  padding: 16px 16px; min-height: 135px;
  display: flex; flex-direction: column; justify-content: space-between;
  border: 1px solid rgba(255,255,255,0.04);
}
.db-stat-top  { display: flex; justify-content: space-between; align-items: center; width: 100%; margin-bottom: 4px; }
.db-stat-icon {
  width: 32px; height: 32px; border-radius: 10px;
  background: rgba(255,255,255,0.07);
  display: grid; place-items: center; font-size: 1rem; color: #9ca3af;
}
.db-edit-weight-btn {
  display: inline-flex; align-items: center; gap: 4px;
  padding: 4px 10px; border-radius: 20px;
  background: rgba(163,230,53,0.12); border: 1px solid rgba(163,230,53,0.3);
  color: #a3e635; font-size: 0.72rem; font-weight: 700;
  text-decoration: none; transition: all 0.2s ease;
}
.db-edit-weight-btn:hover {
  background: #a3e635; color: #000; transform: translateY(-1px);
}
.db-stat-label { font-size: .72rem; color: #6b7280; margin-top: 6px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; }
.db-stat-value {
  font-size: 2.1rem; font-weight: 900; color: #fff;
  line-height: 1.1; letter-spacing: -1px; margin-top: 2px;
}
.db-stat-value small { font-size: 0.95rem; font-weight: 500; color: #9ca3af; letter-spacing: 0; }

/* ── Tarjeta de Frase Motivadora ── */
.db-quote-card {
  background: linear-gradient(145deg, #181818, #131313);
  border: 1px solid rgba(255,255,255,0.06);
  position: relative;
  overflow: hidden;
}
.db-quote-tag {
  display: inline-flex; align-items: center; gap: 5px;
  font-size: 0.68rem; font-weight: 800; color: #a3e635;
  text-transform: uppercase; letter-spacing: 0.5px;
}
.db-quote-shuffle {
  background: none; border: none; color: #6b7280;
  cursor: pointer; padding: 2px 4px; font-size: 0.9rem;
  border-radius: 6px; transition: all 0.2s ease;
}
.db-quote-shuffle:hover { color: #a3e635; transform: rotate(45deg); }
.db-quote-body { margin-top: auto; }
.db-quote-text {
  font-size: 0.77rem; font-weight: 500; color: #e2e8f0;
  line-height: 1.38; font-style: italic; font-family: 'Inter', system-ui, sans-serif;
  letter-spacing: -0.1px; margin: 0;
  display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
}
.db-quote-author {
  font-size: 0.65rem; font-weight: 700; color: #84cc16;
  margin-top: 5px; letter-spacing: 0.3px; text-transform: uppercase;
}

/* ── Metas section ── */
.db-metas-section {
  margin: 4px 16px 16px;
  background: var(--card-glass); border-radius: 20px; padding: 20px;
}
.db-metas-header {
  display: flex; justify-content: space-between; align-items: flex-start;
  margin-bottom: 6px;
}
.db-metas-title { font-size: 1.3rem; font-weight: 800; color: #fff; }
.db-metas-link  { font-size: .82rem; color: #9ca3af; text-decoration: none; white-space: nowrap; }
.db-metas-link:hover { color: #a3e635; }
.db-metas-sub   { font-size: .78rem; color: #6b7280; margin-bottom: 16px; }
.db-metas-bubbles { display: flex; gap: 12px; flex-wrap: wrap; }
.db-meta-bubble {
  width: 50px; height: 50px; border-radius: 50%;
  background: #252525; border: none;
  display: grid; place-items: center;
  font-size: 1.3rem; cursor: pointer;
  transition: transform .18s, background .18s;
  color: var(--meta-color, #a3e635);
}
.db-meta-bubble:hover { transform: scale(1.12); background: rgba(163,230,53,0.12); }
.db-meta-empty {
  font-size: .8rem; color: #6b7280; padding: 10px 0;
}

/* ── Panel de detalle de Meta ── */
.meta-overlay {
  position: fixed; inset: 0; background: rgba(0,0,0,0.7);
  backdrop-filter: blur(4px); z-index: 500;
  display: flex; align-items: flex-end;
  opacity: 0; pointer-events: none; transition: opacity .25s;
}
.meta-overlay.open { opacity: 1; pointer-events: all; }
.meta-panel {
  width: 100%; background: var(--card-glass); border-radius: 24px 24px 0 0;
  padding: 0 0 24px; max-height: 88vh; overflow-y: auto;
  transform: translateY(30px); transition: transform .28s ease;
}
.meta-overlay.open .meta-panel { transform: translateY(0); }

/* Header del panel */
.meta-panel-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 18px 20px 14px; border-bottom: 1px solid rgba(255,255,255,0.06);
  position: sticky; top: 0; background: var(--card-glass); z-index: 10;
}
.meta-panel-back {
  width: 34px; height: 34px; background: #1e1e1e; border-radius: 50%;
  display: grid; place-items: center; cursor: pointer; border: none;
  color: #fff; font-size: 1rem;
}
.meta-panel-title { font-size: 1rem; font-weight: 700; color: #fff; }

/* Tabs */
.meta-tabs {
  display: flex; gap: 0; margin: 16px 20px;
  background: #1e1e1e; border-radius: 10px; padding: 4px;
}
.meta-tab {
  flex: 1; padding: 8px; border: none; background: none;
  border-radius: 8px; font-size: .85rem; font-weight: 600;
  color: #6b7280; cursor: pointer; transition: all .2s;
}
.meta-tab.active { background: #2a2a2a; color: #fff; }

/* Arc de progreso */
.meta-arc-wrap { text-align: center; padding: 24px 20px 8px; }
.meta-goal-name { font-size: 1.4rem; font-weight: 800; color: #fff; margin-bottom: 20px; }
.arc-svg { width: 180px; height: 180px; }
.arc-track { fill: none; stroke: #1e1e1e; stroke-width: 14; stroke-linecap: round; }
.arc-fill  { fill: none; stroke: #5b5fc7; stroke-width: 14; stroke-linecap: round;
             transition: stroke-dashoffset 1s ease; }
.arc-pct   { font-size: 1.6rem; font-weight: 900; fill: #fff; }
.arc-label { font-size: .7rem; fill: #6b7280; }

/* Tabla pesaje */
.meta-pesaje { margin: 16px 20px 0; }
.meta-pesaje-title { font-size: 1rem; font-weight: 700; color: #fff; margin-bottom: 10px; }
.meta-pesaje-table {
  background: var(--card-glass); border-radius: 14px; padding: 14px 16px;
}
.meta-pesaje-cols {
  display: grid; grid-template-columns: 1fr 1fr 1fr;
  gap: 8px; text-align: center;
}
.meta-pesaje-lbl { font-size: .72rem; color: #6b7280; margin-bottom: 4px; }
.meta-pesaje-val { font-size: 1rem; font-weight: 700; color: #fff; }

/* Boton eliminar */
.meta-delete-btn {
  margin: 20px 20px 0;
  width: calc(100% - 40px); padding: 16px;
  background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3);
  border-radius: 14px; color: #ef4444; font-size: .9rem; font-weight: 700;
  cursor: pointer; display: flex; align-items: center; justify-content: center;
  gap: 8px; transition: background .2s;
}
.meta-delete-btn:hover { background: rgba(239,68,68,0.25); }
</style>
<!-- ── HEADER ── -->
<div class="db-header">
  <div>
    <div class="db-greeting">Hola, <?= e($nomUser) ?></div>
    <div class="db-welcome">Bienvenido de<br>vuelta</div>
  </div>
  <div class="db-actions">
    <a href="<?= APP_URL ?>/cliente/notificaciones" class="db-icon-btn" title="Notificaciones">
      <i class="ti ti-bell"></i>
      <?php if($proximoPago): ?><span class="db-notif-dot"></span><?php endif; ?>
    </a>
    <a href="<?= APP_URL ?>/cliente/perfil" class="db-icon-btn" title="Configuracion">
      <i class="ti ti-settings"></i>
    </a>
    <a href="<?= APP_URL ?>/auth/logout" class="db-icon-btn" title="Salir">
      <i class="ti ti-logout"></i>
    </a>
  </div>
</div>

<!-- ── STAT CARDS ── -->
<div class="db-stats">
  <!-- Tarjeta de Frase Motivadora Diaria -->
  <div class="db-stat-card db-quote-card">
    <div class="db-stat-top">
      <div class="db-quote-tag">
        <i class="ti ti-sparkles"></i> <span>Frase del Día</span>
      </div>
      <button type="button" class="db-quote-shuffle" onclick="shuffleQuote()" title="Cambiar frase">
        <i class="ti ti-refresh"></i>
      </button>
    </div>
    <div class="db-quote-body">
      <p class="db-quote-text" id="quoteText">“<?= e($fraseDelDia['frase']) ?>”</p>
      <div class="db-quote-author" id="quoteAuthor">— <?= e($fraseDelDia['autor']) ?></div>
    </div>
  </div>

  <!-- Tarjeta de Peso Actual -->
  <div class="db-stat-card">
    <div class="db-stat-top">
      <div class="db-stat-icon"><i class="ti ti-scale"></i></div>
      <button onclick="promptNuevoPeso()" class="db-edit-weight-btn" title="Añadir registro de peso" style="background:transparent; border:1px solid rgba(163,230,53,0.3); color:#a3e635; padding:4px 10px; border-radius:12px; font-size:0.75rem; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:4px;">
        <i class="ti ti-plus"></i> Añadir
      </button>
    </div>
    <div style="margin-top:10px;display:flex;justify-content:space-between;align-items:flex-end">
      <div>
        <div class="db-stat-label">Peso Actual</div>
        <div class="db-stat-value">
          <?= $pesoActual > 0 ? number_format($pesoActual, 2, '.', '') : '—' ?> <small>kg</small>
        </div>
      </div>
      <?php if($pesoInicial > 0 && $pesoActual > 0): ?>
      <div style="text-align:right">
        <div style="font-size:.7rem;color:#6b7280;font-weight:500">Inicial: <?= number_format($pesoInicial, 2, '.', '') ?> kg</div>
        <div style="font-size:.82rem;font-weight:700;color:<?= ($pesoActual <= $pesoInicial ? '#a3e635' : '#38bdf8') ?>">
          <?= ($pesoActual - $pesoInicial > 0 ? '+' : '') . number_format($pesoActual - $pesoInicial, 2, '.', '') ?> kg
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- ── METAS ── -->
<div class="db-metas-section">
  <div class="db-metas-header">
    <div class="db-metas-title">Metas</div>
    <a href="<?= APP_URL ?>/cliente/perfil" class="db-metas-link">Ver todas →</a>
  </div>
  <div class="db-metas-sub">Tu progreso en cada meta</div>
  <div class="db-metas-bubbles">
    <?php if(empty($objetivos)): ?>
      <p class="db-meta-empty">Sin metas definidas. Edita tu perfil.</p>
    <?php else: ?>
      <?php foreach($objetivos as $obj): ?>
      <button class="db-meta-bubble"
              style="--meta-color: <?= e($obj['color']) ?>"
              onclick="openMetaPanel(<?= htmlspecialchars(json_encode($obj), ENT_QUOTES) ?>)"
              title="<?= e($obj['nombre']) ?>">
        <i class="ti <?= e($obj['icono']) ?>"></i>
      </button>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<!-- ── TIENDA DE CONSUMIBLES ── -->
<div class="tienda-section" id="tiendaSection">
  <button class="tienda-toggle" id="tiendaToggle" onclick="toggleTienda()">
    <span class="tienda-toggle-left">
      <i class="ti ti-shopping-cart tienda-icon"></i>
      <span class="tienda-toggle-title">Tienda de Consumibles</span>
    </span>
    <span class="tienda-toggle-chevron" id="tiendaChevron">
      <i class="ti ti-chevron-down"></i>
    </span>
  </button>

  <div class="tienda-body" id="tiendaBody">
    <!-- Filtros por seccion -->
    <div class="tienda-filters-container">
      <div class="tienda-wrapper" id="tiendaFilters">
        <div class="tienda-option">
          <input checked value="todos" name="tienda-btn" type="radio" class="tienda-input" onchange="filtrarTienda('todos')">
          <div class="tienda-btn">
            <span class="tienda-span">Todos</span>
          </div>
        </div>
        <?php foreach($tiendaSecciones as $sec): ?>
        <div class="tienda-option">
          <input value="<?= $sec['id'] ?>" name="tienda-btn" type="radio" class="tienda-input" onchange="filtrarTienda(<?= $sec['id'] ?>)">
          <div class="tienda-btn">
            <span class="tienda-span"><?= e($sec['nombre']) ?></span>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="tienda-grid" id="tiendaGrid">
      <?php
      $hayProductos = false;
      foreach($tiendaSecciones as $sec):
        $productos = $tiendaProductos[$sec['id']] ?? [];
        if(!empty($productos)) $hayProductos = true;
        foreach($productos as $prod):
          $img = $prod['imagen_path'] ? APP_URL . '/' . $prod['imagen_path'] : '';
          $desc = $prod['descripcion'] ?: 'Sin descripción disponible.';
          $waNum = $ajustesGlobales['whatsapp_numero'] ?? '';
          
          $baseMsg = str_replace('{producto}', $prod['nombre'], $ajustesGlobales['whatsapp_mensaje'] ?? 'Hola, me interesa: {producto}');
          
          // Agregamos el link público del producto para que WhatsApp lea el OpenGraph
          $publicUrl = APP_URL . "/public/producto?id=" . $prod['id'];
          $baseMsg .= "\n\n" . $publicUrl;
          
          $waMsg = urlencode($baseMsg);
          
          $waLink = "https://wa.me/{$waNum}?text={$waMsg}";
      ?>
      <div class="shop-card" data-seccion="<?= $sec['id'] ?>" data-nombre="<?= e($prod['nombre']) ?>" data-precio="<?= $prod['precio'] ?>">
        
        <!-- Estado inicial: Círculo -->
        <div class="container-image">
          <?php if($img): ?>
            <img src="<?= $img ?>" class="image-circle">
          <?php else: ?>
            <div style="color:#9ca3af;font-size:2.5rem;"><i class="ti ti-package"></i></div>
          <?php endif; ?>
        </div>

        <!-- Estado expandido: Tarjeta 3D -->
        <div class="content">
          <div class="flipper">
            <!-- FRENTE -->
            <div class="face front">
              <div class="card-image-area">
                <?php if($img): ?>
                  <img src="<?= $img ?>" class="img-product">
                <?php else: ?>
                  <div style="color:#9ca3af;font-size:2rem;"><i class="ti ti-package"></i></div>
                <?php endif; ?>
              </div>
              <div class="card-info-area">
                <h3 class="product-title"><?= e($prod['nombre']) ?></h3>
                <div class="product-price">$<?= number_format($prod['precio'], 2) ?></div>
                
                <div class="card-actions">
                  <button onclick="event.stopPropagation(); this.closest('.content').classList.add('flipped')" class="btn-card btn-detalles">Ver Detalles</button>
                </div>
              </div>
            </div>
            
            <!-- DORSO (Cliente) -->
            <div class="face back">
              <h3 class="back-title"><?= e($prod['nombre']) ?></h3>
              <div class="back-description"><?= nl2br(e($desc)) ?></div>
              
              <div class="card-actions">
                <button onclick="event.stopPropagation(); this.closest('.content').classList.remove('flipped')" class="btn-card btn-volver"><i class="ti ti-arrow-back-up"></i></button>
                <button onclick="event.stopPropagation(); window.open('<?= $waLink ?>', '_blank')" class="btn-card btn-adquirir"><i class="ti ti-brand-whatsapp"></i> Adquirir</button>
              </div>
            </div>
          </div>
        </div>

      </div>
      <?php 
        endforeach; 
      endforeach;
      ?>
      
      <?php if(!$hayProductos): ?>
        <p style="color:#6b7280;text-align:center;grid-column:1/-1;padding:20px 0;">No hay productos disponibles.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- ── PANEL DETALLE DE META ── -->
<div class="meta-overlay" id="metaOverlay" onclick="if(event.target===this)closeMetaPanel()">
  <div class="meta-panel" id="metaPanel">
    <div class="meta-panel-header">
      <button class="meta-panel-back" onclick="closeMetaPanel()">
        <i class="ti ti-arrow-left"></i>
      </button>
      <span class="meta-panel-title">Detalles de meta</span>
      <div style="width:34px"></div>
    </div>

    <!-- Tabs -->
    <div class="meta-tabs">
      <button class="meta-tab active" id="tabResumen" onclick="switchTab('resumen')">Resumen</button>
      <button class="meta-tab" id="tabHistorial" onclick="switchTab('historial')">Historial</button>
    </div>

    <!-- Contenido Resumen -->
    <div id="tabContentResumen">
      <div class="meta-arc-wrap">
        <div class="meta-goal-name" id="metaGoalName">—</div>
        <!-- SVG Arc -->
        <svg class="arc-svg" viewBox="0 0 180 180">
          <!-- Track arc: 230deg sweep starting at 155deg -->
          <path class="arc-track"
            d="M 28 140 A 62 62 0 1 1 152 140"
            stroke-dasharray="220" stroke-dashoffset="0"/>
          <path class="arc-fill" id="arcFill"
            d="M 28 140 A 62 62 0 1 1 152 140"
            stroke-dasharray="220" stroke-dashoffset="220"/>
          <text class="arc-pct" id="arcPct" x="90" y="96" text-anchor="middle">0%</text>
          <text class="arc-label" x="90" y="115" text-anchor="middle" font-family="Inter,sans-serif">completado</text>
        </svg>
      </div>

      <!-- Tabla Pesaje -->
      <div class="meta-pesaje">
        <div class="meta-pesaje-title" id="pesajeTitulo">Pesaje</div>
        <div class="meta-pesaje-table">
          <div class="meta-pesaje-cols">
            <div>
              <div class="meta-pesaje-lbl">Peso actual</div>
              <div class="meta-pesaje-val" id="metaPesoActual">—</div>
            </div>
            <div>
              <div class="meta-pesaje-lbl">Cambio</div>
              <div class="meta-pesaje-val" id="metaCambio">—</div>
            </div>
            <div>
              <div class="meta-pesaje-lbl">Objetivo</div>
              <div class="meta-pesaje-val" id="metaObjetivo">—</div>
            </div>
          </div>
        </div>
      </div>

      <button class="meta-delete-btn" onclick="closeMetaPanel()">
        <i class="ti ti-trash"></i> Eliminar meta
      </button>
    </div>

    <!-- Contenido Historial -->
    <div id="tabContentHistorial" style="display:none;padding:20px">
      <?php if(!empty($medidas) && $medidas['peso_kg']): ?>
      <div style="text-align:center;padding:30px 0;color:#6b7280;font-size:.9rem">
        <i class="ti ti-chart-line" style="font-size:2rem;display:block;margin-bottom:10px;color:#374151"></i>
        Historial de peso disponible en la seccion de Estadisticas.
      </div>
      <?php else: ?>
      <div style="text-align:center;padding:30px 0;color:#6b7280;font-size:.9rem">Sin datos aun.</div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
const PESO_ACTUAL  = <?= $pesoActual ?: 0 ?>;
const PESO_INICIAL = <?= $pesoInicial ?: 0 ?>;
const META_TEXTO   = <?= json_encode($objetivo ?: '--') ?>;

// Calcula el stroke-dashoffset para el arco (total = 220)
function pctToOffset(pct) {
  return 220 - (220 * Math.min(pct, 100) / 100);
}

function openMetaPanel(obj) {
  const overlay = document.getElementById('metaOverlay');
  document.getElementById('metaGoalName').textContent = obj.nombre;

  // Progreso: diferencia de peso vs ventana de 10 kg
  let pct = 0;
  if (PESO_INICIAL > 0 && PESO_ACTUAL > 0 && PESO_INICIAL !== PESO_ACTUAL) {
    pct = Math.min(100, Math.round((Math.abs(PESO_INICIAL - PESO_ACTUAL) / 10) * 100));
  }

  const arcFill = document.getElementById('arcFill');
  const arcPct  = document.getElementById('arcPct');
  arcFill.style.stroke = obj.color || '#5b5fc7';
  setTimeout(() => {
    arcFill.style.strokeDashoffset = pctToOffset(pct);
    arcPct.textContent = pct + '%';
  }, 80);

  // Tabla pesaje
  document.getElementById('metaPesoActual').textContent = PESO_ACTUAL ? PESO_ACTUAL.toFixed(2) + ' kg' : '—';
  const cambio = (PESO_ACTUAL && PESO_INICIAL) ? (PESO_ACTUAL - PESO_INICIAL).toFixed(2) : '—';
  document.getElementById('metaCambio').textContent = cambio !== '—' ? (cambio > 0 ? '+' : '') + cambio + ' kg' : '—';
  document.getElementById('metaObjetivo').textContent = META_TEXTO;

  // Mostrar panel
  overlay.classList.add('open');
  document.body.style.overflow = 'hidden';
  switchTab('resumen');
}

function closeMetaPanel() {
  document.getElementById('metaOverlay').classList.remove('open');
  document.body.style.overflow = '';
  // Reset arc
  document.getElementById('arcFill').style.strokeDashoffset = 220;
  document.getElementById('arcPct').textContent = '0%';
}

function switchTab(tab) {
  document.getElementById('tabContentResumen').style.display  = tab === 'resumen'   ? 'block' : 'none';
  document.getElementById('tabContentHistorial').style.display = tab === 'historial' ? 'block' : 'none';
  document.getElementById('tabResumen').classList.toggle('active',   tab === 'resumen');
  document.getElementById('tabHistorial').classList.toggle('active', tab === 'historial');
}

// ── Shuffle de Frase Motivadora ──
const MOTIVATIONAL_QUOTES = <?= json_encode($frasesMotivadoras, JSON_UNESCAPED_UNICODE) ?>;
let currentQuoteIdx = <?= $indiceFrase ?>;

function shuffleQuote() {
  if (!MOTIVATIONAL_QUOTES || MOTIVATIONAL_QUOTES.length < 2) return;
  let nextIdx;
  do {
    nextIdx = Math.floor(Math.random() * MOTIVATIONAL_QUOTES.length);
  } while (nextIdx === currentQuoteIdx);
  
  currentQuoteIdx = nextIdx;
  const q = MOTIVATIONAL_QUOTES[nextIdx];
  const qText = document.getElementById('quoteText');
  const qAuthor = document.getElementById('quoteAuthor');
  
  if (qText && qAuthor) {
    qText.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
    qAuthor.style.transition = 'opacity 0.2s ease';
    qText.style.opacity = '0';
    qText.style.transform = 'translateY(4px)';
    qAuthor.style.opacity = '0';
    setTimeout(() => {
      qText.textContent = `“${q.frase}”`;
      qAuthor.textContent = `— ${q.autor}`;
      qText.style.opacity = '1';
      qText.style.transform = 'translateY(0)';
      qAuthor.style.opacity = '1';
    }, 200);
  }
}

// ── Tienda de Consumibles ──────────────────────────────
function toggleTienda() {
  const body    = document.getElementById('tiendaBody');
  const chevron = document.getElementById('tiendaChevron');
  const toggle  = document.getElementById('tiendaToggle');
  const open    = body.classList.toggle('open');
  chevron.classList.toggle('open', open);
  toggle.style.borderRadius = open ? '18px 18px 0 0' : '18px';
}

function filtrarTienda(seccionId) {
  const cards = document.querySelectorAll('#tiendaGrid .shop-card');
  cards.forEach(card => {
    if (seccionId === 'todos' || card.getAttribute('data-seccion') == seccionId) {
      card.style.display = 'flex';
    } else {
      card.style.display = 'none';
    }
  });
}

function agregarAlCarrito(nombre, precio) {
  Xp.toast('success', `${nombre} agregado al carrito 🛒`);
}
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
