<?php

namespace WEBcoast\Typo3BaseSetup\ViewHelpers\ApplicationData;

use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class GetViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('key', 'string', 'The key to get the value for', true);
    }

    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $key = $arguments['key'];

        return self::getValueFromPath($key);
    }

    /**
     * Traverse the path and return the value, if found.
     *
     * @param string $path
     *
     * @return null|mixed
     */
    private static function getValueFromPath($path)
    {
        $pathElements = explode('.', $path);
        $data = &self::getTyposcriptFrontendController()->applicationData;
        $pathElementCount = count($pathElements);
        $value = null;
        foreach ($pathElements as $index => $key) {
            if ($data === null) {
                // Stop here to avoid array access on null
                break;
            }
            if ($index < $pathElementCount - 1) {
                if (is_array($data[$key])) {
                    $data = &$data[$key];
                } else {
                    $data = null;
                }
            } else {
                $value = $data[$key];
            }
        }

        return $value;
    }

    /**
     * @return TypoScriptFrontendController
     */
    private static function getTyposcriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }
}
