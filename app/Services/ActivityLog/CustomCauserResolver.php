<?php

namespace App\Services\ActivityLog;

use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\HtmlString;
use Spatie\Activitylog\CauserResolver;

class CustomCauserResolver extends CauserResolver
{
    public function __construct(Config $config, AuthManager $auth)
    {
        parent::__construct($config, $auth);

        $this->authDriver = $auth->guard('candidato')->check() ? 'candidato' : 'web';
    }
}
