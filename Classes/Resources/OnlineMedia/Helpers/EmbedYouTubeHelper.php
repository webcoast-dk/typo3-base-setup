<?php

namespace WEBcoast\Typo3BaseSetup\Resources\OnlineMedia\Helpers;


use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\OnlineMedia\Helpers\YouTubeHelper;

class EmbedYouTubeHelper extends YouTubeHelper
{
    /**
     * @param \TYPO3\CMS\Core\Resource\File $file
     * @param bool                          $relativeToCurrentScript
     *
     * @return string
     */
    public function getPublicUrl(File $file, $relativeToCurrentScript = false)
    {
        $videoId = $this->getOnlineMediaId($file);

        return sprintf('https://www.youtube.com/embed/%s', $videoId);
    }

}
