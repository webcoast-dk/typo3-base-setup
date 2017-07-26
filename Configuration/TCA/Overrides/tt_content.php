<?php

$GLOBALS['TCA']['tt_content']['types']['image']['showitem'] = preg_replace(
    '/--palette--;[^;]+?;mediaAdjustments/',
    '--palette--;LLL:EXT:typo3_base_setup/Resources/Private/Language/locallang_backend.xlf:tt_content.palette.crop;mediaAdjustments',
    $GLOBALS['TCA']['tt_content']['types']['image']['showitem']
);
