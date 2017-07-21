<?php

namespace WEBcoast\Typo3DefaultSetup\ViewHelpers;


use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

class AliasViewHelper extends \TYPO3Fluid\Fluid\ViewHelpers\AliasViewHelper
{
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $templateVariableContainer = $renderingContext->getVariableProvider();
        $map = $arguments['map'];
        $backupMap = [];
        foreach ($map as $aliasName => $value) {
            if ($templateVariableContainer->exists($aliasName)) {
                $backupMap[$aliasName] = $templateVariableContainer->get($aliasName);
            }
            $templateVariableContainer->add($aliasName, $value);
        }
        $output = $renderChildrenClosure();
        foreach ($map as $aliasName => $value) {
            $templateVariableContainer->remove($aliasName);
        }
        foreach($backupMap as $aliasName => $value) {
            $templateVariableContainer->add($aliasName, $value);
        }
        return $output;
    }
}
