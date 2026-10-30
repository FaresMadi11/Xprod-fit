<?php
// models/UsuarioModel.php
declare(strict_types=1);

class UsuarioModel {
    private PDO $db;

    // Campos que se pueden actualizar via update() — whitelist estricta
    private const UPDATABLE_FIELDS = [
        'nombre', 'email', 'password_hash', 'rol',
        'avatar_url', 'google_id', 'auth_provider', 'activo',
    ];

    /** Mensajes del Onboarding Gate según el estado de la cuenta. */
    public const MSG_PENDIENTE = 'En espera de que tu entrenador active tu cuenta';
    public const MSG_RECHAZADO = 'Tu solicitud de acceso no fue aprobada. Contacta a tu entrenador.';

    /**
     * Preguntas de seguridad permitidas en el registro. Se guarda el TEXTO (no una clave)
     * porque api/recuperar_password.php lo muestra tal cual al recuperar la contraseña.
     * Fuente única: la vista pinta el <select> desde aquí y el servidor valida contra aquí.
     */
    public const PREGUNTAS_SEGURIDAD = [
        'Nombre de tu primera mascota',
        'Ciudad de nacimiento',
        'Nombre de tu colegio',
        'Apellido de soltera de tu madre',
        'Equipo deportivo favorito',
    ];

    /**
     * Onboarding Gate: devuelve el mensaje de bloqueo si el usuario NO puede entrar,
     * o null si tiene acceso. Los entrenadores nunca se bloquean (evita lock-out del admin).
     */
    public static function mensajeBloqueo(array $user): ?string {
        if (($user['rol'] ?? '') === 'entrenador') return null;
        return match ($user['estado_cuenta'] ?? 'pendiente') {
            'activo'    => null,
            'rechazado' => self::MSG_RECHAZADO,
            default     => self::MSG_PENDIENTE,   // 'pendiente' o valor desconocido → denegar
        };
    }

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Busca un usuario por email exacto (case-insensitive).
     * Usa prepared statement estricto — sin LIKE, sin bypass parcial.
     */
    public function findByEmail(string $email): ?array {
        $clean = strtolower(trim($email));
        if ($clean === '') return null;

        $s = $this->db->prepare(
            "SELECT id, nombre, email, password_hash, rol, estado_cuenta, google_id, auth_provider, avatar_url
             FROM usuarios
             WHERE LOWER(TRIM(email)) = ?
             LIMIT 1"
        );
        $s->execute([$clean]);
        return $s->fetch() ?: null;
    }

    /**
     * Busca usuario por nombre de usuario o email (para login flexible).
     * Limita los intentos de match para evitar abuso.
     */
    public function findByUsernameOrEmail(string $identifier): ?array {
        $clean = trim($identifier);
        if ($clean === '') return null;

        // Primero intentar por email exacto (más seguro)
        $byEmail = $this->findByEmail($clean);
        if ($byEmail) return $byEmail;

        // Luego por nombre exacto (case-insensitive)
        $s = $this->db->prepare(
            "SELECT id, nombre, email, password_hash, rol, estado_cuenta, google_id, auth_provider, avatar_url
             FROM usuarios
             WHERE LOWER(TRIM(nombre)) = LOWER(?)
             LIMIT 1"
        );
        $s->execute([$clean]);
        return $s->fetch() ?: null;
    }

    /**
     * Busca usuario por ID.
     */
    public function findById(int $id): ?array {
        $s = $this->db->prepare(
            "SELECT id, nombre, email, rol, estado_cuenta, google_id, auth_provider, avatar_url
             FROM usuarios WHERE id = ? LIMIT 1"
        );
        $s->execute([$id]);
        return $s->fetch() ?: null;
    }

    /**
     * Busca usuario por Google ID (para OAuth).
     */
    public function findByGoogleId(string $googleId): ?array {
        if (empty($googleId)) return null;

        $s = $this->db->prepare(
            "SELECT id, nombre, email, rol, google_id, auth_provider, avatar_url
             FROM usuarios WHERE google_id = ? LIMIT 1"
        );
        $s->execute([$googleId]);
        return $s->fetch() ?: null;
    }

    /**
     * Crea un nuevo usuario.
     */
    public function create(string $nombre, string $email, string $hash, string $rol = 'cliente'): int {
        // Validar rol permitido
        if (!in_array($rol, ['cliente', 'entrenador'], true)) {
            throw new \InvalidArgumentException("Rol inválido: {$rol}");
        }

        $s = $this->db->prepare(
            "INSERT INTO usuarios (nombre, email, password_hash, rol, auth_provider)
             VALUES (?, ?, ?, ?, 'local')"
        );
        $s->execute([$nombre, strtolower(trim($email)), $hash, $rol]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Normaliza una cédula/documento a formato 'V-12345678'.
     * Acepta números puros de 5 a 8 dígitos (ej: '28123456' -> 'V-28123456')
     * o con prefijo (ej: 'E12345678' -> 'E-12345678', 'V-12345678', etc.).
     * Devuelve null si no cumple el formato o supera 8 dígitos.
     */
    public static function normalizarCedula(string $raw): ?string {
        $limpia = strtoupper(preg_replace('/[\s.\-]+/', '', trim($raw)) ?? '');
        // Caso 1: Solo números (5 a 8 dígitos) -> asigna prefijo venezolano por defecto
        if (preg_match('/^\d{5,8}$/', $limpia)) {
            return 'V-' . $limpia;
        }
        // Caso 2: Con prefijo explícito (V, E, J, G, P) + 5 a 8 dígitos
        if (preg_match('/^([VEJGP])(\d{5,8})$/', $limpia, $m)) {
            return $m[1] . '-' . $m[2];
        }
        return null;
    }

    public function emailExists(string $email): bool {
        $s = $this->db->prepare('SELECT 1 FROM usuarios WHERE LOWER(TRIM(email)) = ? LIMIT 1');
        $s->execute([strtolower(trim($email))]);
        return (bool)$s->fetchColumn();
    }

    public function cedulaExists(string $cedula): bool {
        $s = $this->db->prepare('SELECT 1 FROM clientes WHERE cedula = ? LIMIT 1');
        $s->execute([$cedula]);
        return (bool)$s->fetchColumn();
    }

    /**
     * Alta atómica de un atleta: usuarios (estado_cuenta='pendiente') + clientes + historial + metas.
     * Si algo falla se revierte todo: no quedan usuarios huérfanos sin perfil de atleta.
     *
     * @return array{usuario_id:int, cliente_id:int}
     */
    public function registrarAtleta(
        string  $nombre,
        string  $email,
        string  $passwordHash,
        string  $cedula,
        string  $pregunta,
        string  $respuestaHash,
        ?int    $entrenadorId,
        ?string $fechaNac = null,
        ?float  $pesoKg = null,
        ?float  $alturaCm = null,
        array   $metas = [],
        ?array  $metaDetalle = null
    ): array {
        $this->db->beginTransaction();
        try {
            $s = $this->db->prepare(
                "INSERT INTO usuarios
                    (nombre, email, password_hash, rol, auth_provider, estado_cuenta, pregunta_seguridad, respuesta_seguridad)
                 VALUES (?, ?, ?, 'cliente', 'local', 'pendiente', ?, ?)"
            );
            $s->execute([$nombre, strtolower(trim($email)), $passwordHash, $pregunta, $respuestaHash]);
            $uid = (int)$this->db->lastInsertId();

            $metaDetalleJson = $metaDetalle ? json_encode($metaDetalle, JSON_UNESCAPED_UNICODE) : null;
            $objetivoTexto = !empty($metaDetalle['custom_texto'])
                ? $metaDetalle['custom_texto']
                : (!empty($metaDetalle['titulo']) ? $metaDetalle['titulo'] : (!empty($metas) ? implode(', ', $metas) : null));

            $c = $this->db->prepare(
                'INSERT INTO clientes (usuario_id, cedula, entrenador_id, peso_kg, altura_cm, fecha_nac, objetivo, meta_detalle)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $c->execute([$uid, $cedula, $entrenadorId, $pesoKg, $alturaCm, $fechaNac ?: null, $objetivoTexto, $metaDetalleJson]);
            $cid = (int)$this->db->lastInsertId();

            // Guardar peso inicial en el historial si se proveyó
            if ($pesoKg !== null && $pesoKg > 0) {
                $h = $this->db->prepare(
                    "INSERT INTO peso_historial (cliente_id, peso_kg, fecha, tipo) VALUES (?, ?, CURDATE(), 'manual')"
                );
                $h->execute([$cid, $pesoKg]);
            }

            // Sincronizar metas y objetivos del atleta
            if (!empty($metas)) {
                $stCat = $this->db->prepare(
                    "SELECT id, nombre, icono, color FROM objetivos_catalogo WHERE LOWER(TRIM(nombre)) = LOWER(TRIM(?)) LIMIT 1"
                );
                $stInsCO = $this->db->prepare(
                    "INSERT IGNORE INTO cliente_objetivos (cliente_id, objetivo_id, prioridad) VALUES (?, ?, ?)"
                );
                $stInsMeta = $this->db->prepare(
                    "INSERT INTO metas (cliente_id, titulo, icono, color, target_peso, fecha_inicio, semanas_duracion, estado)
                     VALUES (?, ?, ?, ?, ?, CURDATE(), 8, 'activa')"
                );

                $prioridad = 1;
                foreach ($metas as $mTitulo) {
                    $mTitulo = trim((string)$mTitulo);
                    if ($mTitulo === '') continue;

                    $stCat->execute([$mTitulo]);
                    $cat = $stCat->fetch(\PDO::FETCH_ASSOC);
                    $objId = $cat['id'] ?? null;
                    $icono = $cat['icono'] ?? 'ti-target';
                    $color = $cat['color'] ?? '#8a2be2';

                    if (!$objId) {
                        $insCat = $this->db->prepare("INSERT INTO objetivos_catalogo (nombre, icono, color) VALUES (?, ?, ?)");
                        $insCat->execute([$mTitulo, $icono, $color]);
                        $objId = (int)$this->db->lastInsertId();
                    }

                    if ($objId) {
                        $stInsCO->execute([$cid, $objId, $prioridad]);
                    }

                    try {
                        $stInsMeta->execute([$cid, $mTitulo, $icono, $color, $pesoKg]);
                    } catch (\Throwable $eMeta) {
                        // Failsafe
                    }

                    $prioridad++;
                }
            }

            $this->db->commit();
            return ['usuario_id' => $uid, 'cliente_id' => $cid];
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Crea o actualiza un usuario de Google OAuth.
     */
    public function createOrUpdateFromGoogle(array $googleData): int {
        $email     = strtolower(trim($googleData['email'] ?? ''));
        $googleId  = $googleData['sub'] ?? '';
        $nombre = trim(($googleData['given_name'] ?? '') . ' ' . ($googleData['family_name'] ?? ''));
        if (empty($nombre)) {
            $nombre = $googleData['name'] ?? $email;
        }
        $avatarUrl = $googleData['picture'] ?? null;

        // Verificar si ya existe por google_id
        $existing = $this->findByGoogleId($googleId);
        if ($existing) {
            // Actualizar avatar si cambió
            if ($avatarUrl && $existing['avatar_url'] !== $avatarUrl) {
                $this->update((int)$existing['id'], ['avatar_url' => $avatarUrl]);
            }
            return (int)$existing['id'];
        }

        // Verificar si existe por email (vincular cuenta existente)
        $byEmail = $this->findByEmail($email);
        if ($byEmail) {
            $this->update((int)$byEmail['id'], [
                'google_id'     => $googleId,
                'auth_provider' => 'google',
                'avatar_url'    => $avatarUrl,
            ]);
            return (int)$byEmail['id'];
        }

        // Crear nueva cuenta
        $s = $this->db->prepare(
            "INSERT INTO usuarios (nombre, email, password_hash, rol, google_id, auth_provider, avatar_url, estado_cuenta)
             VALUES (?, ?, '', 'cliente', ?, 'google', ?, 'pendiente')"
        );
        $s->execute([$nombre, $email, $googleId, $avatarUrl]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Actualiza campos específicos de un usuario con whitelist estricta.
     */
    public function update(int $id, array $data): void {
        if (empty($data)) return;

        // Filtrar solo campos permitidos (whitelist)
        $safeData = array_filter(
            $data,
            fn($key) => in_array($key, self::UPDATABLE_FIELDS, true),
            ARRAY_FILTER_USE_KEY
        );

        if (empty($safeData)) {
            throw new \InvalidArgumentException('Ningún campo válido para actualizar.');
        }

        $sets   = implode(', ', array_map(fn($k) => "`{$k}` = ?", array_keys($safeData)));
        $values = array_values($safeData);
        $values[] = $id;

        $this->db->prepare("UPDATE usuarios SET {$sets} WHERE id = ?")->execute($values);
    }

    /**
     * Actualiza solo el hash de contraseña (para rehashing automático).
     */
    public function updatePasswordHash(int $id, string $newHash): void {
        $s = $this->db->prepare("UPDATE usuarios SET password_hash = ? WHERE id = ?");
        $s->execute([$newHash, $id]);
    }
}
