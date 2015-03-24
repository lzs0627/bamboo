<?php
namespace IQnote\Bamboo;

/**
 * 配列でglobal変数を保存する
 * Class Config
 * @package IQnote\Bamboo
 */
class Config
{

    protected static $dataStore = array();

    protected static $dataCache = array();

    /**
     *
     * @param  String $key "."で繋ぐ文字列
     * @param  mixed $default
     * @return mixed  Description
     */
    public static function get($key, $default = null)
    {
        if (is_null($key)) {
            return static::$dataStore;
        }

        if (isset(static::$dataStore[$key])) {
            return static::$dataStore[$key];
        } elseif (isset(static::$dataCache[$key])) {
            return static::$dataCache[$key];
        }

        $keys = explode('.', $key);
        $arr = static::$dataStore;

        foreach ($keys as $key) {
            if (!isset($arr[$key])) {
                $arr = $default;
                break;
            } else {
                $arr = $arr[$key];
            }
        }

        static::$dataCache[$key] = $arr;

        return $arr;
    }

    /**
     * @param string $key "."で繋ぐ文字列
     * @param mixed $value 保存する値
     */
    public static function set($key, $value)
    {
        if (is_string($key)) {

            if (isset(static::$dataCache[$key])) {
                unset(static::$dataCache[$key]);
            }

            $keys = explode('.', $key);
            $arr = &static::$dataStore;

            while (count($keys) > 1) {
                $key = array_shift($keys);

                if (!isset($arr[$key]) || !is_array($arr[$key])) {
                    $arr[$key] = array();
                }

                $arr = &$arr[$key];
            }

            $arr[array_shift($keys)] = $value;
        } else {
            throw new \InvalidArgumentException("key must be a dot-notated string.");
        }
    }


    /**
     * configフォルダに保存している設定ファイルを読み込む
     * @param string $configDir
     * @param string $env
     * @throws \Exception
     */
    public static function setup($configDir, $env = 'Product')
    {
        if (!is_dir($configDir)) {
            throw new \Exception('コンフィグフォルダが見つけません');
        }

        $defaultConfig = array();
        $defaultFiles = new \DirectoryIterator($configDir);


        foreach ($defaultFiles as $f) {
            if ($f->isFile() && $f->getExtension() == 'php') {
                $k = $f->getBasename('.php');
                $defaultConfig[strtolower($k)] = include $f->getPathname();
            }
        }

        $envConfig = array();
        if (is_dir($configDir . DIRECTORY_SEPARATOR . ucfirst($env))) {

            $envFiles = new \DirectoryIterator($configDir . DIRECTORY_SEPARATOR . ucfirst($env));

            foreach ($envFiles as $f) {
                if ($f->isFile() && $f->getExtension() == 'php') {
                    $k = $f->getBasename('.php');
                    $envConfig[strtolower($k)] = include $f->getPathname();
                }
            }

        }

        foreach ($defaultConfig as $k => $config) {
            if (isset($envConfig[$k])) {
                $config = array_replace_recursive($config, $envConfig[$k]);
                static::set($k, $config);
                unset($envConfig[$k]);
            } else {
                static::set($k, $config);
            }
        }

        if (count($envConfig) > 0) {
            foreach ($envConfig as $k => $config) {
                static::set($k, $config);
            }
        }

    }
}
