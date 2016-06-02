<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Returns an icon font CSS class for a given file extension.
 * The default mimetype map provides the class suffix for FontAwesome.
 *
 * ### Examples
 *
 *     <v:media.mimetypeIcon extension="png" map="{png: 'file-image-o', jpg: 'file-image-o', default: 'file-o'}" />
 *     <i class="fa fa-{v:media.mimetypeIcon(extension: 'png')}"></i>
 *
 * @author Daniel Kestler <daniel.kestler@medienreaktor.de>, medienreaktor GmbH
 * @package Vhs
 * @subpackage ViewHelpers\Media
 */
class MimetypeIconViewHelper extends AbstractViewHelper {

    /**
	 * @var array
	 */
	protected $mimeTypesMap = array(
        'pdf' => 'file-pdf-o',
        'php' => 'file-code-o',
        'mpg' => 'file-video-o',
        'mpeg' => 'file-video-o',
        'mp4' => 'file-video-o',
        'h264' => 'file-video-o',
        'mov' => 'file-video-o',
        'avi' => 'file-video-o',
        'wav' => 'file-audio-o',
        'aiff' => 'file-audio-o',
        'mp3' => 'file-audio-o',
        'aac' => 'file-audio-o',
        'zip' => 'file-archive-o',
        'tar' => 'file-archive-o',
        'rar' => 'file-archive-o',
        'dmg' => 'file-archive-o',
        'jpg' => 'file-image-o',
        'jpeg' => 'file-image-o',
        'png' => 'file-image-o',
        'gif' => 'file-image-o',
        'ppt' => 'file-powerpoint-o',
        'pptx' => 'file-powerpoint-o',
        'xls' => 'file-excel-o',
        'xlsx' => 'file-excel-o',
        'doc' => 'file-word-o',
        'docx' => 'file-word-o',
        'txt' => 'file-text-o',
        'rtf' => 'file-text-o',
        'default' => 'file-o'
    );

	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		$this->registerArgument('extension', 'string', 'A file extension like "jpg", "pdf" or "docx".', TRUE);
        $this->registerArgument('map', 'array', 'A custom mimetypes map array.', FALSE);
	}

	/**
	 * @return string
	 */
	public function render() {
		$extension = $this->arguments['extension'];

        if (NULL !== $this->arguments['map']) {
            $this->mimeTypesMap = array_merge($this->mimeTypesMap, $this->arguments['map']);
        }

        if (FALSE === array_key_exists($extension, $this->mimeTypesMap)) {
            return $this->mimeTypesMap['default'];
        }

        return $this->mimeTypesMap[$extension];
	}

}
