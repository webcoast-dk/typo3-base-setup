<?php

namespace WEBcoast\Typo3BaseSetup\Resources\Rendering;

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperInterface;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\OnlineMediaHelperRegistry;
use TYPO3\CMS\Core\Resource\Rendering\FileRendererInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

class EmbedRenderer implements FileRendererInterface
{

    /**
     * @var StandaloneView
     */
    protected $view;

    /**
     * @var OnlineMediaHelperInterface
     */
    protected $onlineMediaHelper;

    public function getPriority()
    {
        return 1;
    }

    public function canRender(FileInterface $file)
    {
        return (in_array($file->getMimeType(), ['video/youtube', 'video/vimeo']) || in_array($file->getExtension(),
                    ['youtube', 'vimeo'])) && $this->getOnlineMediaHelper($this->getOriginalFile($file)) !== false;
    }

    public function render(FileInterface $file, $width, $height, array $options = [], $usedPathsRelativeToCurrentScript = false)
    {
        $this->initializeView();

        $options = $this->collectOptions($options, $file);
        $src = $this->createEmbedUrl($options, $file);
        $attributes = $this->collectIframeAttributes($file, $width, $height, $options);

        $this->view->assignMultiple([
            'embedUrl' => $src,
            'iframeAttributes' => $attributes,
            'implodedIframeAttributes' => $this->implodeAttributes($attributes),
            'options' => $options,
            'file' => $file
        ]);

        return $this->view->render();
    }

    protected function initializeView()
    {
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);

        $configuration = self::getTypoScriptFrontendController()->tmpl->setup['lib.']['tx_typo3basesetup_embed.'];

        $this->view->setTemplateRootPaths($configuration['templateRootPaths.'] ?? []);
        $this->view->setLayoutRootPaths($configuration['layoutRootPaths.'] ?? []);
        $this->view->setPartialRootPaths($configuration['partialRootPaths.'] ?? []);
        $this->view->setTemplate('embed');
    }

    /**
     * Get online media helper
     *
     * @param FileInterface $file
     *
     * @return bool|OnlineMediaHelperInterface
     */
    protected function getOnlineMediaHelper(FileInterface $file)
    {
        if ($this->onlineMediaHelper === null) {
            if ($file instanceof File) {
                $this->onlineMediaHelper = OnlineMediaHelperRegistry::getInstance()->getOnlineMediaHelper($file);
            } else {
                $this->onlineMediaHelper = false;
            }
        }

        return $this->onlineMediaHelper;
    }

    /**
     * @param array         $options
     * @param FileInterface $file
     *
     * @return array
     */
    protected function collectOptions(array $options, FileInterface $file)
    {
        $embedOptions = ['iframeClass' => 'embed__item', 'wrapperClass' => 'embed embed--16-9'];
        if (isset($options['embed'])) {
            ArrayUtility::mergeRecursiveWithOverrule($embedOptions, $options['embed']);
        }
        $options['embed'] = $embedOptions;
        // Check for an autoplay option at the file reference itself, if not overridden yet.
        if (!isset($options['autoplay']) && $file instanceof FileReference) {
            $autoplay = $file->getProperty('autoplay');
            if ($autoplay !== null) {
                $options['autoplay'] = $autoplay;
            }
        }

        if ($this->isYouTube($file)) {
            $options['controls'] = (int)!empty($options['controls'] ?? 1);
        }

        if (!isset($options['allow'])) {
            $options['allow'] = 'fullscreen';
            if (!empty($options['autoplay'])) {
                $options['allow'] = 'autoplay; fullscreen';
            }
        }

        return $options;
    }

    protected function createEmbedUrl(array $options, FileInterface $file)
    {
        $videoId = $this->getVideoIdFromFile($file);

        $urlParams = [];

        if (!empty($options['autoplay'])) {
            $urlParams[] = 'autoplay=1';
        }
        if (!empty($options['loop'])) {
            if ($this->isYouTube($file)) {
                $urlParams[] = 'loop=1&playlist=' . rawurlencode($videoId);
            } elseif ($this->isVimeo($file)) {
                $urlParams[] = 'loop=1';
            }
        }

        if ($this->isYouTube($file)) {
            $urlParams = ['autohide=1'];
            $urlParams[] = 'controls=' . $options['controls'];
            if (!empty($options['modestbranding'])) {
                $urlParams[] = 'modestbranding=1';
            }

            if (isset($options['relatedVideos'])) {
                $urlParams[] = 'rel=' . (int)(bool)$options['relatedVideos'];
            }
            if (!isset($options['enablejsapi']) || !empty($options['enablejsapi'])) {
                $urlParams[] = 'enablejsapi=1&origin=' . rawurlencode(GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST'));
            }

            return sprintf(
                'https://www.youtube%s.com/embed/%s?%s',
                !isset($options['no-cookie']) || !empty($options['no-cookie']) ? '-nocookie' : '',
                rawurlencode($videoId),
                implode('&', $urlParams)
            );
        } elseif ($this->isVimeo($file)) {
            if (isset($options['api']) && (int)$options['api'] === 1) {
                $urlParams[] = 'api=1';
            }

            $urlParams[] = 'title=' . (int)!empty($options['showinfo']);
            $urlParams[] = 'byline=' . (int)!empty($options['showinfo']);
            $urlParams[] = 'portrait=0';

            if (!isset($options['no-cookie']) || !empty($options['no-cookie'])) {
                $urlParams[] = 'dnt=1';
            }

            return sprintf('https://player.vimeo.com/video/%s?%s', $videoId, implode('&', $urlParams));
        }

        throw new \InvalidArgumentException(sprintf('The given file "%s" is not supported by %s', $file->getIdentifier(), get_class($this)));
    }

    /**
     * @param int|string $width
     * @param int|string $height
     * @param array      $options
     *
     * @return array pairs of key/value; not yet html-escaped
     */
    protected function collectIframeAttributes(FileInterface $file, $width, $height, array $options)
    {
        $attributes = [];
        $attributes['allowfullscreen'] = true;

        if (isset($options['additionalAttributes']) && is_array($options['additionalAttributes'])) {
            $attributes = array_merge($attributes, $options['additionalAttributes']);
        }
        if (isset($options['data']) && is_array($options['data'])) {
            array_walk($options['data'], function (&$value, $key) use (&$attributes) {
                $attributes['data-' . $key] = $value;
            });
        }

        if ((int)$width > 0) {
            $attributes['width'] = (int)$width;
        }
        if ((int)$height > 0) {
            $attributes['height'] = (int)$height;
        }
        if (isset($GLOBALS['TSFE']) && is_object($GLOBALS['TSFE']) && (isset($GLOBALS['TSFE']->config['config']['doctype']) && $GLOBALS['TSFE']->config['config']['doctype'] !== 'html5')) {
            $attributes['frameborder'] = 0;
        }
        $supportedAttributes = ['class', 'dir', 'id', 'lang', 'style', 'title', 'accesskey', 'tabindex', 'onclick', 'allow'];
        if ($this->isYouTube($file)) {
            $supportedAttributes = array_merge($supportedAttributes, ['poster', 'preload']);
        }
        foreach ($supportedAttributes as $key) {
            if (!empty($options[$key])) {
                $attributes[$key] = $options[$key];
            }
        }

        if (isset($options['embed']['iframeClass']) && !empty($options['embed']['iframeClass'])) {
            $classes = GeneralUtility::trimExplode(' ', $attributes['class'] ?? '', true);
            $classes[] = $options['embed']['iframeClass'];
            $attributes['class'] = implode(' ', $classes);
        }

        return $attributes;
    }

    /**
     * @param FileInterface $file
     *
     * @return string
     */
    protected function getVideoIdFromFile(FileInterface $file)
    {
        $orgFile = $this->getOriginalFile($file);

        return $this->getOnlineMediaHelper($orgFile)->getOnlineMediaId($orgFile);
    }

    protected function isYouTube(FileInterface $file)
    {
        return $file->getMimeType() === 'video/youtube' || $file->getExtension() === 'youtube';
    }

    protected function isVimeo(FileInterface $file)
    {
        return $file->getMimeType() === 'video/vimeo' || $file->getExtension() === 'vimeo';
    }

    protected function getOriginalFile($file)
    {
        $orgFile = $file;
        if ($orgFile instanceof FileReference) {
            $orgFile = $orgFile->getOriginalFile();
        }

        return $orgFile;
    }

    /**
     * @param array $attributes
     *
     * @return string
     * @internal
     */
    protected function implodeAttributes(array $attributes): string
    {
        $attributeList = [];
        foreach ($attributes as $name => $value) {
            $name = preg_replace('/[^\p{L}0-9_.-]/u', '', $name);
            if ($value === true) {
                $attributeList[] = $name;
            } else {
                $attributeList[] = $name . '="' . htmlspecialchars($value, ENT_QUOTES | ENT_HTML5) . '"';
            }
        }

        return implode(' ', $attributeList);
    }

    /**
     * @return TypoScriptFrontendController
     */
    protected static function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }
}
