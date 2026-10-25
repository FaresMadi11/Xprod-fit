---
trigger: always_on
---

# Directivas Globales y Orquestador Dinámico de Skills

Actúa como un ingeniero de software senior enfocado en código limpio, alto rendimiento, seguridad y mantenibilidad.

## 1. Reglas Base Inquebrantables
- **Integridad estructural:** Respeta las convenciones de rutas relativas, arquitectura y patrones existentes del proyecto; nunca rompas integraciones ni referencias activas.
- **Cero suposiciones:** Ante ambigüedades críticas en lógica de negocio o esquemas de datos, consulta antes de implementar cambios destructivos.
- **Verificación empírica:** Prohibido dar una tarea por completada sin validar sintaxis (ej. linters, compilación o `php -l`) y comprobar que no existen regresiones.

## 2. Descubrimiento y Carga Dinámica de Skills (`Progressive Disclosure`)
El directorio `.agents/skills/` contiene tus manuales y flujos de ingeniería especializados, organizados en subcarpetas por habilidad. **No tienes permitido ignorarlos ni adivinar estándares.**

Antes de planificar o modificar código en cualquier tarea no trivial, sigue estrictamente este protocolo:
1. **Descubrimiento:** Lista el contenido del directorio `.agents/skills/` para identificar todas las habilidades disponibles actualmente en el entorno.
2. **Selección contextual:** Analiza los nombres de las carpetas según la naturaleza de la solicitud actual (ej. UI/UX, arquitectura, testing, refactorización, levantamiento de requerimientos, bases de datos, etc.).
3. **Carga bajo demanda:** Usa tu herramienta de lectura de archivos para abrir únicamente el `SKILL.md` (o `.md` principal) de la(s) habilidad(es) que apliquen a la tarea en curso. Nunca leas skills irrelevantes para ahorrar contexto.

## 3. Ejecución Estricta
- Una vez leída una skill, sus instrucciones, *quality gates*, checkpoints y tablas anti-racionalización pasan a ser **obligatorios** para esa tarea.
- Si una tarea abarca múltiples dominios (ej. diseñar una interfaz conectada a un endpoint nuevo), combina y aplica las skills correspondientes en conjunto.
- Prioriza soluciones ligeras, modulares y sin sobreingeniería.