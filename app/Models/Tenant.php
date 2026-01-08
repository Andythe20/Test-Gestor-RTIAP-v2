<?php

namespace App\Models;

use Spatie\Multitenancy\Models\Tenant as BaseTenant;

//class Tenant extends Model
class Tenant extends BaseTenant
{
    protected $connection = 'landlord';

    protected $fillable = [
        'name',
        'domain',
        'database',
        'db_username',
        'db_password_encrypted',
        'status',
    ];

    /**
     * Return the decrypted tenant DB password or null if unavailable.
     *
     * @return string|null
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
}
