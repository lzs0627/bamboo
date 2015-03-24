<?php
namespace IQnote\Bamboo;

/**
 * [PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)に従う
 * Class AutoLoader
 * @package IQnote\Bamboo
 */
class AutoLoader
{
    
    protected static $namespaces = array();
    protected static $classes = array();
    
    /**
     *
     * @param String $namespace
     * @param String $path
     */
    public static function addNamespace($namespace, $path)
    {
        static::$namespaces[$namespace] = $path;
    }
    /**
     *
     * @param type $class
     * @param type $path
     */
    public static function addClass($class, $path)
    {
        static::$classes[$class] = $path;
    }
    /**
     * autoload登録する
     */
    public static function register()
    {
        spl_autoload_register('\\IQnote\\Bamboo\\Autoloader::load', true, true);
    }
    /**
     * PSR-0,PSR-4の規則に従う.
     * https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
     * https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader-examples.md
     * @param  type   $class
     * @return boolean
     */
    public static function load($class)
    {
       
        if (strpos($class, 'static::') === 0) {
            //既にLoadしたから
            return true;
        }
        
        $class = ltrim($class, '\\');

        //static::$classesが優先チェック
        if (isset(static::$classes[$class])) {
            include realpath(static::$classes[$class]);
            
            return true;
            
        }
        
        foreach (static::$namespaces as $ns => $baseDir) {
            $ns = ltrim($ns, '\\');
            $len = strlen($ns);

            //namespaceが一致するかをチェック；大文字小文字を区別する
            if (strncmp($ns, $class, $len) !== 0) {
                continue;
            }
            //namespaceを削除する
            $class = substr($class, $len);
            //'_'をフォルダに変更
            $classRelativePath = str_replace('_', DIRECTORY_SEPARATOR, str_replace('\\', DIRECTORY_SEPARATOR, rtrim($class, '\\')));
            $classRealPath = realpath($baseDir . DIRECTORY_SEPARATOR . $classRelativePath. '.php');

            if ($classRealPath && file_exists($classRealPath)) {
                require $classRealPath;
                
                return true;
            }
        }

        return false;
    }
}
