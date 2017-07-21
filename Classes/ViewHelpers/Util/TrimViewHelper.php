<?php

namespace WEBcoast\Typo3DefaultSetup\ViewHelpers\Util;


use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class TrimViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('input', 'string', 'The input to be trimmed');
        $this->registerArgument('charList', 'string', 'The characters to remove');
        $this->registerArgument('mode', 'string', 'Normal (null/empty), right or left trim');
    }

    public function render()
    {
        $value = $this->arguments['input'];
        if ($value === null) {
            $value = $this->renderChildren();
        }

        switch ($this->arguments['mode']) {
            case 'left':
                if (!empty($this->arguments['charList'])) {
                    $value = ltrim($value, $this->arguments['charList']);
                } else {
                    $value = ltrim($value);
                }
                break;
            case 'right':
                if (!empty($this->arguments['charList'])) {
                    $value = rtrim($value, $this->arguments['charList']);
                } else {
                    $value = rtrim($value);
                }
                break;
            default:
                if (!empty($this->arguments['charList'])) {
                    $value = trim($value, $this->arguments['charList']);
                } else {
                    $value = trim($value);
                }
                break;
        }

        return $value;
    }
}
