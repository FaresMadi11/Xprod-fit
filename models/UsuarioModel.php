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
            "SELECT id, nombre, email, password_hash, rol, google_id, auth_provider, avatar_url
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
            "SELECT id, nombre, email, password_hash, rol, google_id, auth_provider, avatar_url
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
            "SELECT id, nombre, email, rol, google_id, auth_provider, avatar_url
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
     * Crea o actualiza un usuario de Google OAuth.
     */
    public function createOrUpdateFromGoogle(array $googleData): int {
        $email     = strtolower(trim($googleData['email'] ?? ''));
        $googleId  = $googleData['sub'] ?? '';
        $nombre    = $googleData['name'] ?? $email;
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
            "INSERT INTO usuarios (nombre, email, password_hash, rol, google_id, auth_provider, avatar_url)
             VALUES (?, ?, '', 'cliente', ?, 'google', ?)"
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
