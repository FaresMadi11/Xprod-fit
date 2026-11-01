<?php
// core/GeminiService.php — Servicio de IA para análisis inteligente de cronología deportiva
declare(strict_types=1);

class GeminiService {

    // Lista de modelos activos en orden de prioridad (con fallback automático)
    private const MODELS = [
        'gemini-2.5-flash',
        'gemini-flash-latest',
        'gemini-3.5-flash',
        'gemini-3.1-flash-lite'
    ];

    /**
     * Envía el documento extraído a Gemini para generar la cronología estructurada
     */
    public static function analizarHistorialAtleta(
        array $docParsed,
        array $catalogoEjercicios,
        string $nombreAtleta = 'Atleta',
        string $fechaReferencia = ''
    ): array {
        $apiKey = env('GEMINI_API_KEY');
        if (empty($apiKey) || $apiKey === 'tu_clave_aqui_pegada') {
            throw new RuntimeException("No se ha configurado la variable GEMINI_API_KEY en el archivo .env.");
        }

        if (empty($fechaReferencia)) {
            $fechaReferencia = date('Y-m-d');
        }

        // Simplificar catálogo para el contexto de la IA (ID, nombre, grupo, equipamiento, tipo)
        $catalogoContexto = [];
        foreach ($catalogoEjercicios as $ej) {
            $catalogoContexto[] = [
                'id' => (int)$ej['id'],
                'nombre' => $ej['nombre'],
                'grupo' => $ej['grupo_muscular'] ?? '',
                'equipamiento' => $ej['equipamiento'] ?? '',
                'implemento' => $ej['tipo_implemento'] ?? 'barra'
            ];
        }

        // Construir el prompt de ingeniería deportiva
        $systemPrompt = <<<PROMPT
Eres un Analista Principal de Ciencias del Deporte y Procesador de Datos para Xprod Fit.
Tu tarea es leer minuciosamente un registro histórico de entrenamientos del atleta "{$nombreAtleta}" (proveniente de un archivo Excel con tablas/hojas, Word con texto/tablas o PDF) y reconstruir su CRONOLOGÍA COMPLETA Y DETALLADA sin omitir semanas ni series.

CATÁLOGO OFICIAL DE EJERCICIOS DEL SISTEMA:
PROMPT;

        $systemPrompt .= "\n" . json_encode(array_slice($catalogoContexto, 0, 180), JSON_UNESCAPED_UNICODE) . "\n\n";

        $systemPrompt .= <<<INSTRUCCIONES
DIRECTIVAS ESTRICTAS DE EXTRACCIÓN Y NORMALIZACIÓN (CERO OMISIONES / CERO RESÚMENES):
1. CRONOLOGÍA DETALLADA (UNA SESIÓN POR CADA DÍA Y SEMANA DE ENTRENAMIENTO):
   - El documento contiene el registro de entrenamiento del atleta a lo largo de muchos meses o años (desde 2024/2025 hasta la actualidad en 2026).
   - REGLA CARDINAL: ESTÁ ESTRICTAMENTE PROHIBIDO CONDENSAR O RESUMIR. NUNCA resumas meses o años enteros en 4 o 5 sesiones.
   - Si el atleta tiene anotadas 40, 60 o 75 semanas de entrenamiento, DEBES generar una sesión individual en el arreglo "sesiones" por CADA DÍA y CADA SEMANA que entrenó (generando tantas sesiones individuales como entrenamientos existan en el documento: 40, 60, 80 o más sesiones).
   - Cómo interpretar las tablas de entrenamiento del entrenador:
     * Si una tabla tiene los ejercicios en filas y las semanas o fechas en columnas (ej: "Semana 1", "Semana 2", "18/03/2025", "25/03/2025", etc.), CADA COLUMNA DE SEMANA O FECHA ES UNA SESIÓN DISTINTA con los pesos y repeticiones que el atleta fue aumentando esa semana.
     * Si hay múltiples hojas (ej: "Mesociclo 1", "Bloque 2", "Enero 2026"), procesa TODAS las hojas y extrae cada entrenamiento semanal individual de cada hoja.
     * Cada elemento del arreglo "sesiones" DEBE tener su propia fecha "YYYY-MM-DD" específica, el día de la semana ("Lunes", "Martes", etc.), el nombre de la rutina (ej: "Pierna A", "Torso Fuerza"), y la lista de ejercicios con las series, repeticiones y pesos realizados en esa fecha exacta.

2. ASIGNACIÓN DE EJERCICIOS CONTRA EL CATÁLOGO:
   - Por cada ejercicio encontrado en el documento, busca en el CATÁLOGO OFICIAL si existe una coincidencia clara (ejemplo: "banca con barra" -> "Press de Banca Plano", "sentadilla libre" -> "Sentadilla Trasera").
   - Si coincide, coloca "catalogo_id" con el ID oficial y "nombre_oficial" con el nombre del catálogo.
   - Si es un ejercicio nuevo que no está en el catálogo, pon "catalogo_id": null y "nombre_oficial": nombre limpio y estandarizado.
   - Infiere "tipo_implemento": "mancuerna", "barra", "polea_maquina" o "sin_peso".

3. SERIES, REPETICIONES Y CARGAS:
   - Extrae cada serie realizada ("numero_serie": 1, 2, 3...).
   - "reps": número entero de repeticiones. Si pusieron rango (ej: "8-10"), toma el límite superior (10) o el promedio.
   - "peso": valor numérico tal como se anotó (ej: si son 225lbs, pon 225.0; si son 5 placas, pon 5.0; si son 35kg, pon 35.0; sin peso pon 0).
   - "unidad": "kg" | "lbs" | "placas" | "ninguna". Detecta estrictamente si viene en libras (lb, lbs), kilogramos (kg), placas o sin peso.
   - "equipamiento": Si se indica "placas grandes" o "placas pequeñas", pon "Placas Grandes" o "Placas Pequeñas".
   - "tipo_serie": "normal" | "calentamiento" | "fallo" | "drop".

4. PESO CORPORAL DEL ATLETA:
   - Si en el documento aparecen anotaciones del peso corporal del atleta a lo largo de las semanas (ej: "Peso: 75.4kg", "Semana 3: 76kg"), extráelas en "medidas_corporales" con su fecha YYYY-MM-DD.

5. FORMATO DE SALIDA ESTRICTO (JSON):
   Debes responder ÚNICAMENTE un objeto JSON válido con este esquema exacto:
{
  "resumen": {
    "total_sesiones": 0,
    "total_series": 0,
    "rango_fechas": "YYYY-MM-DD al YYYY-MM-DD",
    "pesos_corporales_encontrados": 0
  },
  "medidas_corporales": [
    { "fecha": "YYYY-MM-DD", "peso_kg": 75.5 }
  ],
  "sesiones": [
    {
      "fecha": "YYYY-MM-DD",
      "dia_nombre": "Lunes",
      "rutina_nombre": "Torso Hipertrofia",
      "ejercicios": [
        {
          "nombre_detectado": "Press Plano con Barra",
          "catalogo_id": 12,
          "nombre_oficial": "Press de Banca Plano",
          "tipo_implemento": "barra",
          "series": [
            { "numero_serie": 1, "reps": 10, "peso": 60.0, "unidad": "kg", "tipo_serie": "normal" },
            { "numero_serie": 2, "reps": 8,  "peso": 65.0, "unidad": "kg", "tipo_serie": "normal" }
          ]
        }
      ]
    }
  ]
}
INSTRUCCIONES;

        // Construir partes del contenido para Gemini
        $parts = [];
        if ($docParsed['tipo'] === 'pdf_base64') {
            $parts[] = [
                'inlineData' => [
                    'mimeType' => 'application/pdf',
                    'data' => $docParsed['contenido']
                ]
            ];
            $parts[] = [
                'text' => $systemPrompt . "\n\nDOCUMENTO PDF ADJUNTO: Extrae toda la información de este archivo PDF siguiendo las directivas."
            ];
        } else {
            $docTexto = (string)$docParsed['contenido'];
            if (mb_strlen($docTexto) > 350000) {
                $docTexto = mb_substr($docTexto, 0, 350000) . "\n...[Contenido recortado por longitud máxima de seguridad]...";
            }
            $parts[] = [
                'text' => $systemPrompt . "\n\nCONTENIDO DEL DOCUMENTO EXTRAÍDO ({$docParsed['formato']}):\n" . $docTexto
            ];
        }

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => $parts
                ]
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json',
                'temperature' => 0.1,
                'maxOutputTokens' => 65536
            ]
        ];

        // Intentar modelos con fallback automático
        $ultimoError = '';
        foreach (self::MODELS as $modelName) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key=" . urlencode($apiKey);

            try {
                $httpResult = self::ejecutarPeticionHttp($url, $payload);
                $httpCode = $httpResult['code'];
                $responseBody = $httpResult['body'];

                if ($httpCode === 200 && !empty($responseBody)) {
                    $resData = json_decode($responseBody, true);
                    $rawJson = $resData['candidates'][0]['content']['parts'][0]['text'] ?? '';

                    if (!empty($rawJson)) {
                        // Limpiar posibles delimitadores markdown
                        $rawJson = trim($rawJson);
                        if (str_starts_with($rawJson, '```json')) {
                            $rawJson = substr($rawJson, 7);
                        }
                        if (str_starts_with($rawJson, '```')) {
                            $rawJson = substr($rawJson, 3);
                        }
                        if (str_ends_with($rawJson, '```')) {
                            $rawJson = substr($rawJson, 0, -3);
                        }
                        $rawJson = trim($rawJson);

                        $parsedResult = json_decode($rawJson, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($parsedResult)) {
                            if (!isset($parsedResult['sesiones']) || !is_array($parsedResult['sesiones'])) {
                                $parsedResult['sesiones'] = [];
                            }
                            return $parsedResult;
                        }
                    }
                }

                // Si falló por modelo no disponible o spike (404, 503, 429), intentar siguiente modelo
                $errJson = json_decode($responseBody, true);
                $msg = $errJson['error']['message'] ?? "Código HTTP {$httpCode}";
                $ultimoError = "[{$modelName}] {$msg}";
                error_log("[GeminiService] Fallo modelo {$modelName}: {$msg}. Probando siguiente...");

            } catch (\Throwable $e) {
                $ultimoError = "[{$modelName}] " . $e->getMessage();
                error_log("[GeminiService] Excepción modelo {$modelName}: " . $e->getMessage());
            }
        }

        throw new RuntimeException("No se pudo procesar con los modelos de IA disponibles. Último error: " . $ultimoError);
    }

    /**
     * Cliente HTTP universal: usa cURL si está disponible, o stream_context nativo si cURL no está instalado
     */
    private static function ejecutarPeticionHttp(string $url, array $payload): array {
        @set_time_limit(180);
        @ini_set('default_socket_timeout', '120');
        $jsonPayload = json_encode($payload);

        // 1. Usar cURL si está disponible en la instalación de PHP
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonPayload)
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 90);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            $response = curl_exec($ch);
            $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            if (is_resource($ch)) {
                curl_close($ch);
            }

            if ($response !== false && empty($curlError)) {
                return ['code' => $httpCode, 'body' => (string)$response];
            }
            // Si cURL falló por transporte, continuar al fallback nativo
            error_log("[GeminiService] cURL falló ({$curlError}); intentando fallback con file_get_contents.");
        }

        // 2. Fallback universal con stream_context nativo de PHP (sin dependencias externas)
        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n" .
                            "Content-Length: " . strlen($jsonPayload) . "\r\n" .
                            "Connection: close\r\n",
                'content' => $jsonPayload,
                'timeout' => 90,
                'ignore_errors' => true
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ];

        $context = stream_context_create($opts);
        $response = @file_get_contents($url, false, $context);

        $httpCode = 0;
        if (!empty($http_response_header)) {
            if (preg_match('#HTTP/\S+\s+(\d+)#', $http_response_header[0] ?? '', $matches)) {
                $httpCode = (int)$matches[1];
            }
        }

        if ($response === false) {
            throw new RuntimeException("Error en conexión HTTP con el servicio de IA (ambos cURL y stream fallaron).");
        }

        return ['code' => $httpCode, 'body' => (string)$response];
    }
}
