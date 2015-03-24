<?php
namespace IQnote\Bamboo\Controller;

/**
 * MVCパターンのController
 * Class Controller
 * @package IQnote\Bamboo\Controller
 */
class Controller
{
    public $actionId;
    public $params;
    public $actionPath;
    public $layout;

    /**
     * @param string $actionPath
     * @param string $actionId
     * @param array $routeParams
     * @throws \Exception
     */
    public function __construct($actionPath, $actionId, $routeParams)
    {
        if (! is_file($actionPath)) {
            throw new \Exception($actionPath. ' not found.');
        }

        $this->actionPath = $actionPath;
        $this->actionId = $actionId;
        $this->params = $routeParams;
    }

    /**
     * ページ内容をresponeする
     */
    public function reponse()
    {
        $actionContent = $this->renderAction();

        if ($this->layout === false) {
            echo $actionContent;
        }

        if (! $this->layout) {
            //Default set
            $this->layout = APP_LAYOUT_ROOT . DIRECTORY_SEPARATOR . 'default.php';
        }

        $this->renderLayout($actionContent);
    }

    /**
     * actionファイルをrenderする
     * @param string $actionPath
     * @return string
     */
    public function renderAction($actionPath = null)
    {
        if (! $actionPath) {
            $actionPath = $this->actionPath;
        }

        ob_start();
        require $actionPath;
        $actionContent = ob_get_clean();

        return $actionContent;
    }

    /**
     * Layoutをrenderする
     * @param string $actionContent
     */
    public function renderLayout($actionContent)
    {
        $this->actionContent = $actionContent;
        require $this->layout;
    }

    public function __get($name)
    {
        return "";
    }
}