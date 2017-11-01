<?php

namespace MikeKBurke\ExperianAutocheck;

/**
 * Class ExperianAutocheckCacheFile
 *
 * @category API
 *
 * @author   Mike Burke <mkburke@hotmail.co.uk>
 * @license  MIT https://opensource.org/licenses/MIT
 *
 * @link     https://github.com/mike-k-burke/experian-autocheck
 */
class ExperianAutocheckCacheFile
{
    /**
     * Location of Cache Directory.
     *
     * @var null|string
     */
    protected static $cache_directory = null;

    /**
     * GET from Cache.
     *
     * @param string $search VRM / VIN
     * @return null|ExperianAutocheckEntity
     */
    public static function cacheFileGet($search)
    {
        if (!self::$cache_directory) {
            return;
        }

        if (file_exists(self::$cache_directory.$search)) {
            try {
                $html = file_get_contents(self::$cache_directory.$search);
            } catch (\Exception $e) {
                $xml = null;
            }

            $entity = ExperianAutocheckConvert::htmlToEntity($html);
            $entity->dataSource = 'file';

            return $entity;
        }
    }

    /**
     * PUT to Cache
     *
     * @param ExperianAutocheckEntity|ExperianAutocheckError $entity Input class
     * @return void
     */
    public static function cacheFilePut($entity)
    {
        if (self::$cache_directory) {
            if (!empty($entity->vrm)) {
                $sub_dir = (file_exists(self::$cache_directory.'/vrm')) ? '/vrm/' : '';
                file_put_contents(self::$cache_directory . $sub_dir . $entity->vrm, $entity->html);
            }
            if (!empty($entity->vin)) {
                $sub_dir = (file_exists(self::$cache_directory.'/vin')) ? '/vin/' : '';
                file_put_contents(self::$cache_directory . $sub_dir . $entity->vin, $entity->html);
            }
        }
    }

    /**
     * Set Cache Directory.
     *
     * @param string $directory Directory
     * @throws \ErrorException
     * @return void
     */
    public static function setCacheDirectory($directory)
    {
        if (!is_writable($directory)) {
            throw new \ErrorException('Directory not writable | '.$directory);
        }
        $directory = rtrim($directory.'/').'/';
        self::$cache_directory = $directory;
    }
}
