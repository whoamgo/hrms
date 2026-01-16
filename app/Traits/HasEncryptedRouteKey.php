<?php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;

trait HasEncryptedRouteKey
{
    /**
     * Get the value of the model's route key.
     *
     * @return mixed
     */
    public function getRouteKey()
    {
        return Crypt::encryptString($this->getAttribute($this->getRouteKeyName()));
    }

    /**
     * Retrieve the model for bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        try {
            // Try to decrypt the value
            $decryptedValue = Crypt::decryptString($value);
            
            // Find the model
            $model = $this->where($field ?? $this->getRouteKeyName(), $decryptedValue)->first();
            
            // If model not found, return null (Laravel will handle 404)
            if (!$model) {
                return null;
            }
            
            return $model;
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // Invalid encrypted value - return null to trigger 404
            return null;
        } catch (\Exception $e) {
            // Other exceptions - log and return null
            \Log::warning('Error resolving route binding: ' . $e->getMessage());
            return null;
        }
    }
}

