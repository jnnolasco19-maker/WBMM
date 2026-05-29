<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Security extends BaseConfig
{
    /**
     * CSRF Protection Method
     * Options: 'cookie' or 'session'
     */
    public string $csrfProtection = 'cookie';

    /**
     * Regenerate CSRF token on every submission
     */
    public bool $regenerate = true;

    /**
     * CSRF Token Randomization
     */
    public bool $tokenRandomize = false;

    /**
     * CSRF Token Name
     */
    public string $tokenName = 'csrf_token_name';

    /**
     * CSRF Header Name
     */
    public string $headerName = 'X-CSRF-TOKEN';

    /**
     * CSRF Cookie Name
     */
    public string $cookieName = 'csrf_cookie_name';

    /**
     * CSRF Expires — 7200 seconds (2 hours)
     */
    public int $expires = 7200;

    /**
     * CSRF SameSite cookie setting
     * Options: '' | 'None' | 'Lax' | 'Strict'
     */
    public string $samesite = 'Lax';

    /**
     * Redirect to previous page on CSRF failure
     * instead of throwing an exception
     */
    public bool $redirect = true;
}
