<?php

namespace MikeKBurke\ExperianAutocheck;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\FileCookieJar;

/**
 * Class ExperianAutocheckApi
 *
 * @category API
 *
 * @author   Mike Burke <mkburke@hotmail.co.uk>
 * @license  MIT https://opensource.org/licenses/MIT
 *
 * @link     https://github.com/mike-k-burke/experian-autocheck
 */
class ExperianAutocheckApi
{
    /**
     * URL
     *
     * @var string
     */
    public static $urlBase = 'https://www.autocheck.uk.experian.com';

    /**
     * Log onto the API
     *
     * @param array $credentials Credentials
     * @return CookieJar|bool
     */
    public static function authenticate(array $credentials)
    {
        $client = new Client(['cookies' => true]);
        $jar = new CookieJar();

        $client->post(
            self::$urlBase . '/Account/SignIn',
            [
                'query'   => $credentials,
                'cookies' => $jar,
                'verify'  => false
            ]
        );

        $auth_cookie = $jar->getCookieByName('.ASPXAUTH');
        if ($auth_cookie) {
            return $jar;
        }

        return false;
    }

    /**
     * Call the API
     *
     * @param array     $call_data  Params
     * @param CookieJar $jar        Api cookies
     * @return ExperianAutocheckEntity|ExperianAutocheckError|null
     */
    public static function callApi(array $call_data, CookieJar $jar)
    {
        $client = new Client(['cookies' => true]);

        $responseObject = $client->post(
            self::$urlBase . '/VehicleDescriptionValuation/SubmitValuationRequest',
            [
                'query'  => array_merge(
                    $call_data,
                    [
                        'IsCAPRequired' => 'true'
                    ]
                ),
                'cookies' => $jar,
                'verify' => false
            ]
        );

        $html = $responseObject->getBody()->getContents();

        $return_entity = ExperianAutocheckConvert::htmlToEntity($html);

        ExperianAutocheckCacheFile::cacheFilePut($return_entity);

        return $return_entity;
    }

    /**
     * Type of api call
     *
     * @param array $call_data Params
     *
     * @return string
     */
    public static function callType(array $call_data)
    {
        return !empty($call_data['vrm']) ? 'vrm' : 'vin';
    }

    /**
     * Get the search term from the params
     *
     * @param array $call_data Params
     * @return string
     */
    public static function getSearchTerm(array $call_data)
    {
        return !empty($call_data['vrm']) ? $call_data['vrm'] : $call_data['vin'];
    }

    /**
     * Set cookie jar
     *
     * @return CookieJar;
     */
    public static function setCookieJar()
    {
        return new CookieJar();
    }
}
