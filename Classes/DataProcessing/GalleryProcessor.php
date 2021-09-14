<?php

namespace WEBcoast\Typo3BaseSetup\DataProcessing;

class GalleryProcessor extends \TYPO3\CMS\Frontend\DataProcessing\GalleryProcessor
{
    protected $mediaScaling = 1;
    /**
     * Calculate the width/height of the media elements
     *
     * Based on the width of the gallery, defined equal width or height by a user, the spacing between columns and
     * the use of a border, defined by user, where the border width and padding are taken into account
     *
     * File objects MUST already be filtered. They need a height and width to be shown in the gallery
     */
    protected function calculateMediaWidthsAndHeights()
    {
        $columnSpacingTotal = ($this->galleryData['count']['columns'] - 1) * $this->columnSpacing;

        $galleryWidthMinusBorderAndSpacing = max($this->galleryData['width'] - $columnSpacingTotal, 1);

        if ($this->borderEnabled) {
            $borderPaddingTotal = ($this->galleryData['count']['columns'] * 2) * $this->borderPadding;
            $borderWidthTotal = ($this->galleryData['count']['columns'] * 2) * $this->borderWidth;
            $galleryWidthMinusBorderAndSpacing = $galleryWidthMinusBorderAndSpacing - $borderPaddingTotal - $borderWidthTotal;
        }

        if ($this->equalMediaHeight) {
            // User entered a predefined height

            // Determine
            if (($this->processorConfiguration['useHeightOfSmallestImage'] ?? false)) {
                for ($row = 1; $row <= $this->galleryData['count']['rows']; $row++) {
                    if ($this->processorConfiguration['equalHeightPerRow'] ?? false) {
                        $this->galleryData['rows'][$row]['equalMediaHeight'] = $this->equalMediaHeight;
                    }
                    for ($column = 1; $column <= $this->galleryData['count']['columns']; $column++) {
                        $fileKey = (($row - 1) * $this->galleryData['count']['columns']) + $column - 1;
                        if ($fileKey > $this->galleryData['count']['files'] - 1) {
                            continue;
                        }
                        $fileHeight = $this->getCroppedDimensionalProperty($this->fileObjects[$fileKey], 'height');
                        if ($this->processorConfiguration['equalHeightPerRow'] ?? false) {
                            if ($fileHeight > 0 && $fileHeight < $this->galleryData['rows'][$row]['equalMediaHeight']) {
                                $this->galleryData['rows'][$row]['equalMediaHeight'] = $fileHeight;
                            }
                        } else {
                            if ($fileHeight > 0 && $fileHeight < $this->equalMediaHeight) {
                                $this->equalMediaHeight = $fileHeight;
                            }
                        }
                    }
                    $this->galleryData['rows'][$row]['cumulatedWidth'] = 0;
                }
            }

            // Calculate the scaling correction when the total of media elements is wider than the gallery width
            for ($row = 1; $row <= $this->galleryData['count']['rows']; $row++) {
                $totalRowWidth = 0;
                $actualColumns = 0;
                $equalMediaHeight = ($this->processorConfiguration['equalHeightPerRow'] ?? false) ? ($this->galleryData['rows'][$row]['equalMediaHeight'] ?? $this->equalMediaHeight) : $this->equalMediaHeight;
                for ($column = 1; $column <= $this->galleryData['count']['columns']; $column++) {
                    $fileKey = (($row - 1) * $this->galleryData['count']['columns']) + $column - 1;
                    if ($fileKey > $this->galleryData['count']['files'] - 1) {
                        continue;
                    }
                    $currentMediaScaling = $equalMediaHeight / max($this->getCroppedDimensionalProperty($this->fileObjects[$fileKey], 'height'), 1);
                    $totalRowWidth += $this->getCroppedDimensionalProperty($this->fileObjects[$fileKey], 'width') * $currentMediaScaling;
                    $actualColumns++;
                }
                $columnSpacingTotal = ($actualColumns - 1) * $this->columnSpacing;
                $galleryWidthMinusBorderAndSpacing = max($this->galleryData['width'] - $columnSpacingTotal, 1);
                if ($this->borderEnabled) {
                    $borderPaddingTotal = $actualColumns * 2 * $this->borderPadding;
                    $borderWidthTotal = $actualColumns * 2 * $this->borderWidth;
                    $galleryWidthMinusBorderAndSpacing = $galleryWidthMinusBorderAndSpacing - $borderPaddingTotal - $borderWidthTotal;
                }
                $this->galleryData['rows'][$row]['maxWidth'] = $galleryWidthMinusBorderAndSpacing;
                if ($totalRowWidth > $galleryWidthMinusBorderAndSpacing) {
                    $mediaScaling = $totalRowWidth / $galleryWidthMinusBorderAndSpacing;
                    if ($mediaScaling > $this->mediaScaling) {
                        $this->mediaScaling = $mediaScaling;
                    }
                    if ($this->processorConfiguration['equalHeightPerRow'] ?? false) {
                        $this->galleryData['rows'][$row]['scaling'] = $mediaScaling;
                    }
                } elseif ($this->processorConfiguration['equalHeightPerRow'] ?? false) {
                    $this->galleryData['rows'][$row]['scaling'] = 1;
                }
            }

            // Set the corrected dimensions for each media element
            foreach ($this->fileObjects as $key => $fileObject) {
                $rowNumber = ceil(($key + 1) / $this->galleryData['count']['columns']);
                if ($this->processorConfiguration['equalHeightPerRow'] ?? false) {
                    $equalMediaHeight = $this->galleryData['rows'][$rowNumber]['equalMediaHeight'];
                    $mediaScaling = $this->galleryData['rows'][$rowNumber]['scaling'];
                } else {
                    $equalMediaHeight = $this->equalMediaHeight;
                    $mediaScaling = $this->mediaScaling;
                }
                $mediaHeight = round($equalMediaHeight / $mediaScaling);
                $mediaWidth = ceil(
                    $this->getCroppedDimensionalProperty($fileObject, 'width') * ($mediaHeight / max($this->getCroppedDimensionalProperty($fileObject, 'height'), 1))
                );
                $this->mediaDimensions[$key] = [
                    'width' => $mediaWidth,
                    'height' => $mediaHeight
                ];
                $this->galleryData['rows'][$rowNumber]['cumulatedWidth'] += $mediaWidth;
            }

            // Check if rows are wider than allowed
            foreach ($this->galleryData['rows'] as $rowNumber => $rowData) {
                if ($rowData['cumulatedWidth'] > $rowData['maxWidth']) {
                    // We want the absolute difference
                    $difference = abs($rowData['maxWidth'] - $rowData['cumulatedWidth']);
                    // How much do we need to cut off each element
                    $cutPerElement = floor($difference / $this->galleryData['count']['columns']);
                    // How much do we need to cut extra off the widest elements
                    $additionalCutForWidestElements = ceil($difference / $this->galleryData['count']['columns']) - $cutPerElement;

                    $elementsToCrop = [];

                    // Collect file object keys for the current row
                    foreach ($this->fileObjects as $key => $fileObject) {
                        if ($key >= ($rowNumber - 1) * $this->galleryData['count']['columns'] && $key < $rowNumber * $this->galleryData['count']['columns']) {
                            $elementsToCrop[] = [
                                'width' => $this->mediaDimensions[$key]['width'],
                                'key' => $key
                            ];
                        }
                    }

                    // Sort by width in descending order
                    usort($elementsToCrop, function($a, $b) {
                        if ($a['width'] === $b['width']) {
                            return 0;
                        }

                        return ($a['width'] > $b['width']) ? -1 : 1;
                    });

                    // Cut the default off each element
                    if ($cutPerElement > 0) {
                        foreach ($elementsToCrop as $element) {
                            $this->mediaDimensions[$element['key']]['width'] = $this->mediaDimensions[$element['key']]['width'] - min($difference, $cutPerElement);
                            $difference -= min($difference, $cutPerElement);
                        }
                    }
                    // Cut the extra off each element, until the difference is 0
                    foreach ($elementsToCrop as $element) {
                        if ($difference > 0) {
                            $this->mediaDimensions[$element['key']]['width'] = $this->mediaDimensions[$element['key']]['width'] - min($difference, $additionalCutForWidestElements);
                            $difference -= min($difference, $additionalCutForWidestElements);
                        }
                    }
                }
            }
        } elseif ($this->equalMediaWidth) {
            // User entered a predefined width
            $mediaScalingCorrection = 1;

            // Calculate the scaling correction when the total of media elements is wider than the gallery width
            $totalRowWidth = $this->galleryData['count']['columns'] * $this->equalMediaWidth;
            $mediaInRowScaling = $totalRowWidth / $galleryWidthMinusBorderAndSpacing;
            $mediaScalingCorrection = max($mediaInRowScaling, $mediaScalingCorrection);

            // Set the corrected dimensions for each media element
            foreach ($this->fileObjects as $key => $fileObject) {
                $mediaWidth = floor($this->equalMediaWidth / $mediaScalingCorrection);
                $mediaHeight = floor(
                    $this->getCroppedDimensionalProperty($fileObject, 'height') * ($mediaWidth / max($this->getCroppedDimensionalProperty($fileObject, 'width'), 1))
                );
                $this->mediaDimensions[$key] = [
                    'width' => $mediaWidth,
                    'height' => $mediaHeight
                ];
            }

            // Recalculate gallery width
            $this->galleryData['width'] = floor($totalRowWidth / $mediaScalingCorrection);

            // Automatic setting of width and height
        } else {
            $maxMediaWidth = (int)($galleryWidthMinusBorderAndSpacing / $this->galleryData['count']['columns']);
            foreach ($this->fileObjects as $key => $fileObject) {
                $croppedWidth = $this->getCroppedDimensionalProperty($fileObject, 'width');
                $mediaWidth = $croppedWidth > 0 ? min($maxMediaWidth, $croppedWidth) : $maxMediaWidth;
                $mediaHeight = floor(
                    $this->getCroppedDimensionalProperty($fileObject, 'height') * ($mediaWidth / max($this->getCroppedDimensionalProperty($fileObject, 'width'), 1))
                );
                $this->mediaDimensions[$key] = [
                    'width' => $mediaWidth,
                    'height' => $mediaHeight
                ];
            }
        }
    }
}
