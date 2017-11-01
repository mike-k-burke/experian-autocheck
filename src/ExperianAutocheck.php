<?php

namespace MikeKBurke\ExperianAutocheck;

use BespokeSupport\Reg\Reg;
use GuzzleHttp\Cookie\CookieJar;

/**
 * Class ExperianAutocheck
 *
 * @category API
 *
 * @author   Mike Burke <mkburke@hotmail.co.uk>
 * @license  MIT https://opensource.org/licenses/MIT
 *
 * @link     https://github.com/mike-k-burke/experian-autocheck
 */
class ExperianAutocheck
{
    /**
     * Required credentials
     *
     * @var array
     */
    protected $credentials = [
        'EmailAddress' => null,
        'Password' => null,

    ];

    /**
     * Authentication key
     *
     * @var string
     */
    protected $api_cookies = null;

    /**
     * Constructor
     *
     * @param string $email_address  email_address
     * @param string $password  Password
     * @throws \InvalidArgumentException|\ErrorException
     */
    public function __construct($email_address, $password)
    {
        if (empty($email_address) || empty($password)) {
            throw new \InvalidArgumentException(ExperianAutocheckError::ERROR_AUTH_PARAMS);
        }

        $this->credentials['EmailAddress'] = $email_address;
        $this->credentials['Password'] = $password;

        $this->api_cookies = ExperianAutocheckApi::authenticate($this->credentials, $this->api_cookies);
        if (!$this->api_cookies) {
            throw new \ErrorException(ExperianAutocheckError::ERROR_AUTH);
        }
    }

    /**
     * Main search
     *
     * @param string  $search  Search
     * @param integer $mileage Mileage
     * @return ExperianAutocheckEntity|ExperianAutocheckError|null
     */
    public function lookup($search, $mileage = null)
    {
        $search = preg_replace('/[^A-Z0-9]/', '', strtoupper($search));

        if (!strlen($search)) {
            throw new \InvalidArgumentException('Unknown search term');
        }

        $return = ExperianAutocheckCacheFile::cacheFileGet($search);
        if ($return) {
            return $return;
        }

        $search_type = self::isVRM($search) ? 'VRM' : 'VIN';
        $entity = ExperianAutocheckApi::callApi(
            [
                $search_type => $search,
                'CurrentMileage' => $mileage
            ],
            $this->api_cookies
        );

        return $entity;
    }

    /**
     * Check if the search term is a VRM
     *
     * @param string $search Search term
     * @return boolean
     */
    public static function isVRM($search)
    {
        if (strlen($search) != 17) {
            $reg = Reg::create($search);
            if ($reg) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set cache directory
     *
     * @param string $cache_directory DIR
     * @return void
     */
    public function setCacheFileDirectory($cache_directory)
    {
        ExperianAutocheckCacheFile::setCacheDirectory($cache_directory);
    }

    /**
     * Build URL
     *
     * @param string $endpoint API
     * @return string
     */
    protected function getApiUrl($endpoint)
    {
        return ExperianAutocheckApi::$urlBase . $endpoint;
    }
}