<?php

namespace WEBcoast\Typo3BaseSetup\Resources\Rendering;

use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\Rendering\FileRendererInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

class PictureTagRenderer implements FileRendererInterface
{
    /**
     * @var ImageService
     */
    protected $imageService;

    public function __construct()
    {
        $this->imageService = GeneralUtility::makeInstance(ImageService::class);
    }

    public function getPriority()
    {
        return 1;
    }

    public function canRender(FileInterface $file)
    {
        return str_starts_with($file->getMimeType(), 'image/');
    }

    public function render(FileInterface $image, $width, $height, array $options = [], $usedPathsRelativeToCurrentScript = false)
    {
        $cropVariant = $options['cropVariant'] ?: 'default';
        $cropString = $image instanceof FileReference ? $image->getProperty('crop') : '';
        $cropVariantCollection = CropVariantCollection::create((string)$cropString);
        $defaultImage = $this->processImage($image, $width, $height, $cropVariantCollection, $cropVariant);

        $imgTag = new TagBuilder('img');
        if (!$imgTag->hasAttribute('data-focus-area')) {
            $focusArea = $cropVariantCollection->getFocusArea($cropVariant);
            if (!$focusArea->isEmpty()) {
                $imgTag->addAttribute('data-focus-area', $focusArea->makeAbsoluteBasedOnFile($image));
            }
        }
        $imgTag->addAttribute('src', $this->imageService->getImageUri($defaultImage));

        if (isset($options['additionalConfig']['defaultScaleVariants'])) {
            $srcset = [$this->imageService->getImageUri($defaultImage) . ' 1x'];
            foreach($options['additionalConfig']['defaultScaleVariants'] as $scale) {
                if ($scale > 1) {
                    $imgVariant = $this->processImage($image, $width * $scale, $height * $scale, $cropVariantCollection, $cropVariant);
                    $srcset[] = $this->imageService->getImageUri($imgVariant) . ' ' . $scale . 'x';
                }
            }
            // If we have other than the default (1x) variant
            if (count($srcset) > 1) {
                $imgTag->addAttribute('srcset', implode(', ', $srcset));
            }
        }

        if (in_array($options['loading'] ?? '', ['lazy', 'eager', 'auto'], true)) {
            $imgTag->addAttribute('loading', $options['loading']);
        }

        $alt = $image->getProperty('alternative');
        $title = $image->getProperty('title');

        // The alt-attribute is mandatory to have valid html-code, therefore add it even if it is empty
        if (empty($options['alt'])) {
            $imgTag->addAttribute('alt', $alt);
        }
        if (empty($options['title']) && $title) {
            $imgTag->addAttribute('title', $title);
        }
        if (!empty($options['class'])) {
            $imgTag->addAttribute('class', $options['class']);
        }

        if (isset($options['additionalConfig']['sources'])) {
            $pictureTag = new TagBuilder('picture');
            $sourceTags = [];

            foreach($options['additionalConfig']['sources'] as $cropVariant => $sourceDefinition) {
                $sourceTag = new TagBuilder('source');
                if (isset($sourceDefinition['media'])) {
                    $sourceTag->addAttribute('media', $sourceDefinition['media']);
                }
                $srcset = [];
                foreach($sourceDefinition['scaleVariants'] ?? [1] as $scale) {
                    $imgVariant = $this->processImage($image, $sourceDefinition['width'] * $scale, $sourceDefinition['height'] * $scale, $cropVariantCollection, $cropVariant);
                    $srcset[] = $this->imageService->getImageUri($imgVariant) . ' ' . $scale . 'x';
                }
                $sourceTag->addAttribute('srcset', implode(', ', $srcset));
                $sourceTags[] = $sourceTag->render();
            }

            $pictureTag->setContent(sprintf('%s' . PHP_EOL . '%s', implode(PHP_EOL, $sourceTags), $imgTag->render()));

            return $pictureTag->render();
        } else {
            $imgTag->addAttribute('width', $defaultImage->getProperty('width'));
            $imgTag->addAttribute('height', $defaultImage->getProperty('height'));

            return $imgTag->render();
        }
    }

    protected function processImage(FileInterface $image, $width, $height, $cropVariantCollection, $cropVariant)
    {
        $cropArea = $cropVariantCollection->getCropArea($cropVariant);
        $processingInstructions = [
            'width' => $width,
            'height' => $height,
            'crop' => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($image),
        ];
        return $this->imageService->applyProcessingInstructions($image, $processingInstructions);
    }
}
