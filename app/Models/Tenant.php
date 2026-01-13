<?php

namespace App\Models;

use Spatie\Multitenancy\Models\Tenant as BaseTenant;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

// class Tenant extends Model
class Tenant extends BaseTenant
{
    protected $connection = 'landlord';

    protected $fillable = [
        'name',
        'path',
        'domain',
        'database',
        'db_username',
        'db_password_encrypted',
        'api_token_hash',
        'status',
    ];

    /**
     * Return the decrypted tenant DB password or null if unavailable.
     */
    public function getDecryptedDbPassword(): ?string
    {
        if (empty($this->db_password_encrypted)) {
            return null;
        }

        try {
            return decrypt($this->db_password_encrypted);
        } catch (\Throwable $e) {
            // If decryption fails, return null so callers can fallback
            return null;
        }
    }

    public function users()
    {
        return $this->hasMany(\App\Models\User::class, 'tenant_id');
    }

    /**
     * Generar y conservar un nuevo token de API para este tenant.
     * Devuelve el token simple (mostrar al administrador solo una vez).
     */
    public function generateApiToken(): string
    {
        // El tenant debe tener una id
        if (empty($this->id)) {
            throw new \RuntimeException('Tenant must be persisted before generating API token.');
        }

        $random = Str::random(60);
        // Guardar el hash del token aleatorio
        $this->forceFill(['api_token_hash' => Hash::make($random)])->save();

        // Devuelve un token con el id del tenant y la parte aleatoria.
        return $this->id . '|' . $random;
    }

    /**
     * Verificar un token simple (formato: "{tenant_id}|{random}")
     */
    public function verifyApiToken(string $plainToken): bool
    {
        // Se divide el token en 2 partes
        $parts = explode('|', $plainToken, 2);

        if (count($parts) !== 2) {
            return false;
        }

        // Extraer id y parte aleatoria
        [$id, $random] = $parts;

        // Si el id no coincide o no hay hash guardado, falla
        if ((string) $this->id !== (string) $id) {
            return false;
        }

        // Verificar la parte aleatoria contra el hash guardado
        if (empty($this->api_token_hash)) {
            return false;
        }

        // Devolver el resultado de la verificación
        return Hash::check($random, $this->api_token_hash);
    }
}
