<?php
namespace App\Services\Cors;

use Spatie\Cors\CorsProfile\DefaultProfile;

class DefaultCorsProfile extends DefaultProfile
{
    public function allowOrigins(): array
    {
        // write the allowed domains
        return [];
    }
}
