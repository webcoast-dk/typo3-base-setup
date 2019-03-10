<?php

namespace WEBcoast\Typo3BaseSetup\ViewHelpers\ApplicationData;

use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

class SetViewHelper extends AbstractViewHelper
{
    use CompileWithContentArgumentAndRenderStatic;

    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('value', 'mixed', 'The value to set into the given key');
        $this->registerArgument('key', 'string', 'The key to set the value for', true);
    }

    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $value = $renderChildrenClosure();
        $key = $arguments['key'];

        if ($key === 'indexedDocTitle') {
            self::getTyposcriptFrontendController()->indexedDocTitle = $value;
        } else {
            self::setValueInPath($key, $value);
        }
    }

    /**
     * Traverse the given path and set the value
     *
     * @param string $path
     * @param mixed $value
     */
    private static function setValueInPath($path, $value)
    {
        $pathElements = explode('.', $path);
        $data = &self::getTyposcriptFrontendController()->applicationData;
        $pathElementCount = count($pathElements);
        foreach ($pathElements as $index => $key) {
            if ($index < $pathElementCount - 1) {
                if(!is_array($data[$key])) {
                    $data[$key] = [];
                }
                $data = &$data[$key];
            } else {
                $data[$key] = $value;
            }
        }
    }

    /**
     * @return TypoScriptFrontendController
     */
    private static function getTyposcriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }
}
