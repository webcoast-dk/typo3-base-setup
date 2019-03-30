<?php

namespace WEBcoast\Typo3BaseSetup\ViewHelpers\Arrays;

use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class MergeViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('original', 'array', 'The original array', true);
        $this->registerArgument('overrule', 'array', 'The overrule array', true);
    }

    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $original = $arguments['original'];
        ArrayUtility::mergeRecursiveWithOverrule($original, $arguments['overrule']);

        return $original;
    }
}
