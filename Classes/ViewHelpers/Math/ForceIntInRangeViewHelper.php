<?php

namespace WEBcoast\Typo3DefaultSetup\ViewHelpers\Math;


use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class ForceIntInRangeViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('value', 'int', 'The input value to be force into range', true);
        $this->registerArgument('min', 'int', 'The minimum allowed value', true);
        $this->registerArgument('max', 'int', 'The minimum allowed value', true);
        $this->registerArgument('defaultValue', 'int', 'The minimum allowed value', true);
    }

    public function render()
    {
        return MathUtility::forceIntegerInRange($this->arguments['value'], $this->arguments['min'], $this->arguments['max'] ?? PHP_INT_MAX, $this->arguments['defaultValue']);
    }
}
