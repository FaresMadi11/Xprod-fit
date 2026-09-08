<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Rutina - Zonas Musculares</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #0a0c12;
            color: #e8eaf0;
            min-height: 100vh;
            padding: 1.5rem 1rem 4rem;
        }

        .page-wrap { max-width: 900px; margin: 0 auto; }

        header {
            display: flex; align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem; padding: 0 0.25rem;
        }
        header h1 { font-size: 1.1rem; font-weight: 600; }
        .btn-ghost { background: none; border: none; cursor: pointer; color: #6b7280; font-size: 0.875rem; font-weight: 500; transition: color 0.2s; }
        .btn-ghost:hover { color: #e8eaf0; }

        .card {
            background: rgba(18,20,32,0.95);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 1.25rem;
            backdrop-filter: blur(14px);
            padding: 2rem;
        }

        hr.div { border: none; border-top: 1px solid rgba(255,255,255,0.07); margin: 2rem 0; }

        /* Inputs */
        .field { position: relative; margin-top: 1.5rem; }
        .field:first-child { margin-top: 0; }
        .field label {
            position: absolute; top: -0.55rem; left: 0.75rem;
            font-size: 0.7rem; font-weight: 600; color: #6b7280;
            background: rgba(18,20,32,0.95); padding: 0 0.3rem;
            border-radius: 3px; pointer-events: none;
        }
        .field input, .field select {
            width: 100%; background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 0.75rem; padding: 0.9rem 1rem;
            color: #e8eaf0; font-family: inherit; font-size: 0.9rem;
            outline: none; appearance: none; -webkit-appearance: none;
            transition: border-color 0.2s;
        }
        .field input:focus, .field select:focus {
            border-color: #2dd4bf;
            box-shadow: 0 0 0 1px #2dd4bf;
        }
        .field:focus-within label { color: #2dd4bf; }

        /* Días */
        .days-row { display: flex; gap: 0.5rem; margin-top: 0.75rem; flex-wrap: wrap; }
        .day-btn {
            width: 2.4rem; height: 2.4rem; border-radius: 50%;
            border: 1px solid rgba(255,255,255,0.08);
            background: rgba(255,255,255,0.05); color: #6b7280;
            font-size: 0.8rem; font-weight: 600; cursor: pointer;
            transition: all 0.2s; display: flex; align-items: center; justify-content: center;
        }
        .day-btn:hover { background: rgba(255,255,255,0.1); color: #e8eaf0; }
        .day-btn.active { background: rgba(45,212,191,0.15); border-color: #2dd4bf; color: #2dd4bf; }

        /* Layout principal zonas */
        .zones-layout {
            display: flex; flex-direction: column; gap: 1.5rem; align-items: flex-start;
        }
        @media(min-width: 580px) { .zones-layout { flex-direction: row; align-items: flex-start; } }

        /* ─── SVG (la magia) ─── */
        .svg-wrap {
            flex-shrink: 0;
            width: 180px;
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 1rem;
            padding: 0.75rem;
        }
        @media(min-width: 580px) { .svg-wrap { width: 190px; } }

        #body-svg { width: 100%; height: auto; display: block; overflow: visible; }

        /* BASE del cuerpo: color bien visible */
        .b { fill: #3a4560; stroke: #5a6a8a; stroke-width: 1.5; stroke-linejoin: round; }

        /* Zonas musculares: ligeramente más claras para distinguirse */
        .mz {
            fill: #4a5570;
            stroke: #6a7a9a;
            stroke-width: 1.8;
            stroke-linejoin: round;
            cursor: pointer;
            transition: fill 0.2s, stroke 0.2s, filter 0.2s;
        }
        .mz:hover {
            fill: #1e6a60;
            stroke: #2dd4bf;
            filter: drop-shadow(0 0 5px rgba(45,212,191,0.5));
        }
        .mz.sel {
            fill: rgba(45,212,191,0.5);
            stroke: #2dd4bf;
            filter: drop-shadow(0 0 8px rgba(45,212,191,0.7));
        }

        /* Panel ejercicios */
        .exercises-col { flex: 1; min-width: 0; }

        .label-mini { font-size: 0.72rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 0.6rem; display: block; }

        .chips-row { display: flex; flex-wrap: wrap; gap: 0.5rem; min-height: 2rem; }
        .chip {
            display: inline-flex; align-items: center; gap: 0.4rem;
            padding: 0.25rem 0.65rem; border-radius: 9999px;
            font-size: 0.72rem; font-weight: 600;
            border: 1px solid #2dd4bf;
            background: rgba(45,212,191,0.12); color: #2dd4bf;
            animation: popIn 0.2s cubic-bezier(0.34,1.56,0.64,1);
        }
        .chip button { background:none; border:none; cursor:pointer; color: inherit; line-height:1; display:flex; padding: 0; }
        .chip button:hover { color: #ef4444; }
        @keyframes popIn { 0%{transform:scale(0.7);opacity:0} 100%{transform:scale(1);opacity:1} }

        .hint { font-size: 0.82rem; color: #4b5563; font-style: italic; }

        /* Bloques de ejercicios */
        .ez-block {
            margin-top: 1rem;
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 0.875rem; overflow: hidden;
            animation: slideDown 0.25s ease;
        }
        @keyframes slideDown { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }

        .ez-block-head {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.65rem 1rem;
            background: rgba(45,212,191,0.07);
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }
        .ez-block-head span { font-size: 0.78rem; font-weight: 700; color: #2dd4bf; text-transform: uppercase; letter-spacing: .04em; }

        .ez-row { display: flex; align-items: center; gap: 0.65rem; padding: 0.65rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.04); }
        .ez-row:last-child { border-bottom: none; }
        .ez-row select {
            flex: 1; background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08); border-radius: 0.5rem;
            padding: 0.5rem 0.65rem; color: #e8eaf0; font-family: inherit;
            font-size: 0.82rem; outline: none; appearance: none;
            transition: border-color 0.2s;
        }
        .ez-row select:focus { border-color: #2dd4bf; }
        .btn-del { background:none; border:none; color:#4b5563; cursor:pointer; padding:0.25rem; border-radius:0.35rem; transition: all 0.2s; display:flex; }
        .btn-del:hover { background:rgba(239,68,68,0.12); color:#ef4444; }
        .btn-add-ex {
            display: block; width: calc(100% - 2rem); margin: 0.5rem 1rem;
            padding: 0.45rem; background: none; border: 1px dashed rgba(45,212,191,0.25);
            border-radius: 0.5rem; color: #2dd4bf; font-size: 0.78rem;
            font-family: inherit; cursor: pointer; transition: all 0.2s;
        }
        .btn-add-ex:hover { background: rgba(45,212,191,0.07); border-style: solid; }

        /* Tooltip */
        #tip {
            position: fixed; background: rgba(8,10,18,0.95);
            border: 1px solid #2dd4bf; padding: 3px 9px;
            border-radius: 5px; font-size: 0.7rem; font-weight: 600; color: #2dd4bf;
            pointer-events: none; opacity: 0; transition: opacity 0.15s; z-index: 9999; white-space: nowrap;
        }

        .btn-confirm {
            display: block; width: 100%; margin-top: 2rem;
            padding: 1rem; background: #2dd4bf; color: #020d0c;
            font-weight: 700; font-size: 0.95rem; font-family: inherit;
            border: none; border-radius: 0.875rem; cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 0 20px rgba(45,212,191,0.25);
        }
        .btn-confirm:hover { filter: brightness(1.08); transform: translateY(-1px); }
    </style>
</head>
<body>
<div id="tip"></div>
<div class="page-wrap">

    <header>
        <button class="btn-ghost" type="button">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        </button>
        <h1>Crear Rutina</h1>
        <button class="btn-ghost" type="button">Guardar</button>
    </header>

    <div class="card">
      <form id="rf" novalidate>

        <div class="field">
            <select id="atleta"><option value="">— Seleccionar —</option><option>Atleta A</option><option>Atleta B</option></select>
            <label>Atleta asignado *</label>
        </div>
        <div class="field">
            <input type="text" id="rname" placeholder="Ej: Hipertrofia Tren Superior">
            <label>Nombre de la rutina</label>
        </div>

        <hr class="div">

        <span class="label-mini">Días de la semana</span>
        <div class="days-row">
            <div class="day-btn active">L</div>
            <div class="day-btn">M</div>
            <div class="day-btn">X</div>
            <div class="day-btn">J</div>
            <div class="day-btn">V</div>
            <div class="day-btn">S</div>
            <div class="day-btn">D</div>
        </div>
        <input type="hidden" name="dias" id="diasInp" value="0">

        <hr class="div">

        <span class="label-mini">Zonas a entrenar — toca la silueta</span>

        <div class="zones-layout">

          <!-- ══════════ SVG SILUETA MUSCULAR ══════════ -->
          <div class="svg-wrap">
            <!--
              viewBox = 0 0 100 260
              Todo con colores SOLIDOS y explícitos (sin CSS variables)
            -->
            <svg id="body-svg" viewBox="0 0 100 260" xmlns="http://www.w3.org/2000/svg">

              <!-- ▓▓ CUERPO BASE (silueta sólida visible) ▓▓ -->

              <!-- Cabeza -->
              <ellipse cx="50" cy="14" rx="12" ry="14" fill="#3a4560" stroke="#5a6a8a" stroke-width="1.5"/>
              <!-- Cuello -->
              <rect x="44" y="26" width="12" height="9" rx="2" fill="#3a4560" stroke="#5a6a8a" stroke-width="1.5"/>
              <!-- Trapecios / hombros -->
              <path d="M44,30 L28,32 L18,40 L16,52 L26,52 L32,40 L44,36Z" fill="#3a4560" stroke="#5a6a8a" stroke-width="1.5" stroke-linejoin="round"/>
              <path d="M56,30 L72,32 L82,40 L84,52 L74,52 L68,40 L56,36Z" fill="#3a4560" stroke="#5a6a8a" stroke-width="1.5" stroke-linejoin="round"/>
              <!-- Torso superior (pecho) -->
              <path d="M26,52 L74,52 L76,80 L50,86 L24,80Z" fill="#3a4560" stroke="#5a6a8a" stroke-width="1.5" stroke-linejoin="round"/>
              <!-- Torso inferior (abdomen) -->
              <path d="M24,80 L50,86 L76,80 L74,118 L50,122 L26,118Z" fill="#3a4560" stroke="#5a6a8a" stroke-width="1.5" stroke-linejoin="round"/>
              <!-- Pelvis -->
              <path d="M26,118 L50,122 L74,118 L72,130 L50,134 L28,130Z" fill="#3a4560" stroke="#5a6a8a" stroke-width="1.5" stroke-linejoin="round"/>
              <!-- Brazo sup izq -->
              <path d="M16,40 L8,44 L4,74 L6,90 L16,90 L18,62 L18,52Z" fill="#3a4560" stroke="#5a6a8a" stroke-width="1.5" stroke-linejoin="round"/>
              <!-- Antebrazo izq -->
              <path d="M4,92 L2,122 L4,138 L14,138 L16,120 L16,92Z" fill="#3a4560" stroke="#5a6a8a" stroke-width="1.5" stroke-linejoin="round"/>
              <!-- Brazo sup der -->
              <path d="M84,40 L92,44 L96,74 L94,90 L84,90 L82,62 L82,52Z" fill="#3a4560" stroke="#5a6a8a" stroke-width="1.5" stroke-linejoin="round"/>
              <!-- Antebrazo der -->
              <path d="M96,92 L98,122 L96,138 L86,138 L84,120 L84,92Z" fill="#3a4560" stroke="#5a6a8a" stroke-width="1.5" stroke-linejoin="round"/>
              <!-- Pierna izq sup (cuad) -->
              <path d="M28,130 L50,134 L48,176 L26,176 L24,148Z" fill="#3a4560" stroke="#5a6a8a" stroke-width="1.5" stroke-linejoin="round"/>
              <!-- Pierna der sup (cuad) -->
              <path d="M72,130 L50,134 L52,176 L74,176 L76,148Z" fill="#3a4560" stroke="#5a6a8a" stroke-width="1.5" stroke-linejoin="round"/>
              <!-- Pierna izq inf (panto) -->
              <path d="M26,178 L48,178 L46,218 L28,220 L24,200Z" fill="#3a4560" stroke="#5a6a8a" stroke-width="1.5" stroke-linejoin="round"/>
              <!-- Pierna der inf (panto) -->
              <path d="M74,178 L52,178 L54,218 L72,220 L76,200Z" fill="#3a4560" stroke="#5a6a8a" stroke-width="1.5" stroke-linejoin="round"/>
              <!-- Pies -->
              <ellipse cx="36" cy="223" rx="10" ry="4" fill="#3a4560" stroke="#5a6a8a" stroke-width="1"/>
              <ellipse cx="64" cy="223" rx="10" ry="4" fill="#3a4560" stroke="#5a6a8a" stroke-width="1"/>

              <!-- ▓▓ ZONAS INTERACTIVAS (encima del cuerpo base) ▓▓ -->

              <!-- Hombros -->
              <path d="M28,32 L18,40 L16,52 L26,52 L32,40 L44,36 L44,30Z"
                    class="mz" data-m="Hombros" fill="#4a5570" stroke="#6a7a9a" stroke-width="1.8"/>
              <path d="M72,32 L82,40 L84,52 L74,52 L68,40 L56,36 L56,30Z"
                    class="mz" data-m="Hombros" fill="#4a5570" stroke="#6a7a9a" stroke-width="1.8"/>

              <!-- Pecho izq -->
              <path d="M26,52 L50,58 L50,86 L24,80 L24,56Z"
                    class="mz" data-m="Pecho" fill="#4a5570" stroke="#6a7a9a" stroke-width="1.8"/>
              <!-- Pecho der -->
              <path d="M74,52 L50,58 L50,86 L76,80 L76,56Z"
                    class="mz" data-m="Pecho" fill="#4a5570" stroke="#6a7a9a" stroke-width="1.8"/>

              <!-- Abdomen -->
              <path d="M28,82 L50,88 L72,82 L70,118 L50,122 L30,118Z"
                    class="mz" data-m="Abdomen" fill="#4a5570" stroke="#6a7a9a" stroke-width="1.8"/>

              <!-- Oblicuos -->
              <path d="M24,80 L28,82 L30,118 L26,118 L22,100Z"
                    class="mz" data-m="Oblicuos" fill="#4a5570" stroke="#6a7a9a" stroke-width="1.8"/>
              <path d="M76,80 L72,82 L70,118 L74,118 L78,100Z"
                    class="mz" data-m="Oblicuos" fill="#4a5570" stroke="#6a7a9a" stroke-width="1.8"/>

              <!-- Bíceps -->
              <path d="M8,44 L4,74 L6,90 L10,80 L14,60 L12,44Z"
                    class="mz" data-m="Bíceps" fill="#4a5570" stroke="#6a7a9a" stroke-width="1.8"/>
              <path d="M92,44 L96,74 L94,90 L90,80 L86,60 L88,44Z"
                    class="mz" data-m="Bíceps" fill="#4a5570" stroke="#6a7a9a" stroke-width="1.8"/>

              <!-- Tríceps -->
              <path d="M16,52 L18,62 L16,90 L6,90 L6,76 L10,54Z"
                    class="mz" data-m="Tríceps" fill="#4a5570" stroke="#6a7a9a" stroke-width="1.8"/>
              <path d="M84,52 L82,62 L84,90 L94,90 L94,76 L90,54Z"
                    class="mz" data-m="Tríceps" fill="#4a5570" stroke="#6a7a9a" stroke-width="1.8"/>

              <!-- Antebrazos -->
              <path d="M2,94 L2,122 L4,138 L14,138 L14,120 L4,94Z"
                    class="mz" data-m="Antebrazos" fill="#4a5570" stroke="#6a7a9a" stroke-width="1.8"/>
              <path d="M98,94 L98,122 L96,138 L86,138 L86,120 L96,94Z"
                    class="mz" data-m="Antebrazos" fill="#4a5570" stroke="#6a7a9a" stroke-width="1.8"/>

              <!-- Cuádriceps -->
              <path d="M28,130 L50,134 L48,176 L26,176 L24,148Z"
                    class="mz" data-m="Cuádriceps" fill="#4a5570" stroke="#6a7a9a" stroke-width="1.8"/>
              <path d="M72,130 L50,134 L52,176 L74,176 L76,148Z"
                    class="mz" data-m="Cuádriceps" fill="#4a5570" stroke="#6a7a9a" stroke-width="1.8"/>

              <!-- Isquiotibiales (parte trasera visual) -->
              <path d="M26,176 L48,176 L46,196 L30,196Z"
                    class="mz" data-m="Isquiotibiales" fill="#4a5570" stroke="#6a7a9a" stroke-width="1.8"/>
              <path d="M74,176 L52,176 L54,196 L70,196Z"
                    class="mz" data-m="Isquiotibiales" fill="#4a5570" stroke="#6a7a9a" stroke-width="1.8"/>

              <!-- Pantorrillas -->
              <path d="M24,198 L30,198 L28,220 L24,220Z"
                    class="mz" data-m="Pantorrillas" fill="#4a5570" stroke="#6a7a9a" stroke-width="1.8"/>
              <path d="M44,198 L48,178 L46,220 L30,220 L30,198Z"
                    class="mz" data-m="Pantorrillas" fill="#4a5570" stroke="#6a7a9a" stroke-width="1.8"/>
              <path d="M76,198 L70,198 L72,220 L76,220Z"
                    class="mz" data-m="Pantorrillas" fill="#4a5570" stroke="#6a7a9a" stroke-width="1.8"/>
              <path d="M56,198 L52,178 L54,220 L70,220 L70,198Z"
                    class="mz" data-m="Pantorrillas" fill="#4a5570" stroke="#6a7a9a" stroke-width="1.8"/>

              <!-- Etiquetas de ayuda dentro del SVG -->
              <text x="50" y="12" text-anchor="middle" font-size="4" fill="#8899bb" pointer-events="none" font-family="Inter,sans-serif">Cabeza</text>
              <text x="50" y="68" text-anchor="middle" font-size="4" fill="#8899bb" pointer-events="none" font-family="Inter,sans-serif">Pecho</text>
              <text x="50" y="103" text-anchor="middle" font-size="4" fill="#8899bb" pointer-events="none" font-family="Inter,sans-serif">Abd</text>
              <text x="9" y="68"  text-anchor="middle" font-size="3.5" fill="#8899bb" pointer-events="none" font-family="Inter,sans-serif">Bíc</text>
              <text x="91" y="68" text-anchor="middle" font-size="3.5" fill="#8899bb" pointer-events="none" font-family="Inter,sans-serif">Bíc</text>
              <text x="37" y="154" text-anchor="middle" font-size="3.5" fill="#8899bb" pointer-events="none" font-family="Inter,sans-serif">Cuad</text>
              <text x="63" y="154" text-anchor="middle" font-size="3.5" fill="#8899bb" pointer-events="none" font-family="Inter,sans-serif">Cuad</text>
              <text x="37" y="208" text-anchor="middle" font-size="3.5" fill="#8899bb" pointer-events="none" font-family="Inter,sans-serif">Pant</text>
              <text x="63" y="208" text-anchor="middle" font-size="3.5" fill="#8899bb" pointer-events="none" font-family="Inter,sans-serif">Pant</text>

            </svg>
          </div>
          <!-- ═══════════════════════════════════════════ -->

          <!-- Panel derecho -->
          <div class="exercises-col">
            <span class="label-mini">Zonas seleccionadas</span>
            <div class="chips-row" id="chipsRow">
                <span class="hint" id="hintTxt">Ninguna — toca la figura</span>
            </div>
            <div id="ezBlocks"></div>
            <input type="hidden" name="musculos" id="muscInp">
          </div>
        </div>

        <button type="submit" class="btn-confirm">Confirmar Rutina</button>
      </form>
    </div>
</div>

<script>
const EXDB = {
  "Hombros":             ["Press militar", "Elevaciones laterales", "Face pull", "Vuelos posteriores", "Press Arnold", "Elevaciones frontales"],
  "Pecho":               ["Press banca plano", "Press banca inclinado", "Aperturas con mancuernas", "Fondos en paralelas", "Crossover en polea", "Push-up con lastre"],
  "Abdomen":             ["Crunch abdominal", "Plancha frontal", "Rueda abdominal", "Elevación de piernas colgado", "Crunch en polea", "Crunches invertidos"],
  "Oblicuos":            ["Twist ruso", "Cable Wood Chop", "Crunch oblicuo", "Plancha lateral", "Elevación lateral de piernas"],
  "Bíceps":              ["Curl con barra", "Curl martillo", "Curl en polea baja", "Curl concentrado", "Curl 21", "Curl en banco inclinado"],
  "Tríceps":             ["Extensión en polea", "Press francés", "Fondos en banco", "Extensión sobre la cabeza", "Kickback con mancuerna", "Close-grip bench press"],
  "Antebrazos":          ["Curl de muñeca", "Curl inverso de muñeca", "Farmer walk", "Grippers", "Remo prono"],
  "Cuádriceps":          ["Sentadilla", "Prensa de piernas", "Extensión de cuádriceps", "Sentadilla frontal", "Zancada", "Hack squat"],
  "Isquiotibiales":      ["Curl femoral tumbado", "Curl femoral sentado", "Peso muerto rumano", "Buenos días", "Curl nórdico"],
  "Pantorrillas":        ["Elevación de talones de pie", "Elevación de talones sentado", "Prensa de pantorrilla", "Salto a la cuerda"],
};

const tip         = document.getElementById('tip');
const chipsRow    = document.getElementById('chipsRow');
const hintTxt     = document.getElementById('hintTxt');
const ezBlocks    = document.getElementById('ezBlocks');
const muscInp     = document.getElementById('muscInp');

let sel = new Set();

/* ── Días ── */
document.querySelectorAll('.day-btn').forEach((btn,i) => {
    btn.addEventListener('click', () => btn.classList.toggle('active'));
});

/* ── Tooltip ── */
document.addEventListener('mousemove', e => { tip.style.left=(e.clientX+13)+'px'; tip.style.top=(e.clientY+13)+'px'; });

/* ── Toggle zona ── */
function toggle(name) {
    if (sel.has(name)) {
        sel.delete(name);
        document.querySelectorAll(`.mz[data-m="${name}"]`).forEach(el => el.classList.remove('sel'));
        const b = document.getElementById('b_'+slug(name));
        if (b) b.remove();
    } else {
        sel.add(name);
        document.querySelectorAll(`.mz[data-m="${name}"]`).forEach(el => el.classList.add('sel'));
        addBlock(name);
    }
    renderChips();
    muscInp.value = [...sel].join(', ');
}
window.toggle = toggle;

/* ── Chips ── */
function renderChips() {
    chipsRow.innerHTML = '';
    if (!sel.size) { chipsRow.appendChild(hintTxt); return; }
    sel.forEach(z => {
        const s = document.createElement('span');
        s.className = 'chip';
        s.innerHTML = `${z}<button type="button" onclick="toggle('${z}')"><svg width="9" height="9" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>`;
        chipsRow.appendChild(s);
    });
}

/* ── Bloque ejercicios ── */
function makeOpts(zone) {
    return (EXDB[zone] || ['Sin ejercicios predefinidos']).map(e => `<option>${e}</option>`).join('');
}

function addBlock(zone) {
    const div = document.createElement('div');
    div.className = 'ez-block'; div.id = 'b_'+slug(zone);
    div.innerHTML = `
        <div class="ez-block-head"><span>${zone}</span></div>
        <div class="ez-rows">
            <div class="ez-row">
                <select name="ej[${slug(zone)}][]">${makeOpts(zone)}</select>
                <button type="button" class="btn-del" onclick="delRow(this)"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
            </div>
        </div>
        <button type="button" class="btn-add-ex" onclick="addRow(this,'${zone}')">+ Añadir ejercicio</button>
    `;
    ezBlocks.appendChild(div);
}

window.addRow = function(btn, zone) {
    const wrap = btn.previousElementSibling;
    const row  = document.createElement('div');
    row.className = 'ez-row';
    row.innerHTML = `<select name="ej[${slug(zone)}][]">${makeOpts(zone)}</select><button type="button" class="btn-del" onclick="delRow(this)"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>`;
    wrap.appendChild(row);
};

window.delRow = function(btn) {
    const row = btn.closest('.ez-row'), wrap = row.parentElement;
    if (wrap.querySelectorAll('.ez-row').length > 1) row.remove();
};

function slug(s) { return s.toLowerCase().replace(/\s+/g,'-').replace(/[^a-z0-9\-]/g,''); }

/* ── SVG Interacción ── */
document.querySelectorAll('.mz').forEach(el => {
    const name = el.getAttribute('data-m');
    el.addEventListener('click', () => toggle(name));
    el.addEventListener('mouseenter', () => {
        tip.style.opacity = '1'; tip.textContent = name;
        document.querySelectorAll(`.mz[data-m="${name}"]`).forEach(p => {
            if (!p.classList.contains('sel')) { p.style.fill='#1e6a60'; p.style.stroke='#2dd4bf'; }
        });
    });
    el.addEventListener('mouseleave', () => {
        tip.style.opacity = '0';
        document.querySelectorAll(`.mz[data-m="${name}"]`).forEach(p => {
            if (!p.classList.contains('sel')) { p.style.fill=''; p.style.stroke=''; }
        });
    });
});

/* ── Submit ── */
document.getElementById('rf').addEventListener('submit', e => {
    e.preventDefault();
    if (!sel.size) {
        Swal.fire({icon:'warning',title:'Faltan zonas',text:'Selecciona al menos una zona muscular.',background:'#12131f',color:'#e8eaf0'});
        return;
    }
    Swal.fire({icon:'success',title:'Rutina confirmada',text:'Zonas: '+[...sel].join(', '),background:'#12131f',color:'#e8eaf0',confirmButtonColor:'#2dd4bf'});
});
</script>
</body>
</html>
