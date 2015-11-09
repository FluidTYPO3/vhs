<?php
namespace FluidTYPO3\Vhs\ViewHelpers\Media;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Traits\TagViewHelperTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;

/**
 * Renders HTML code to embed a HTML5 audio player. NOTICE: This is
 * all HTML5 and won't work on browsers like IE8 and below. Include
 * some helper library like kolber.github.io/audiojs/ if you need to suport those.
 * Source can be a single file, a CSV of files or an array of arrays
 * with multiple sources for different audio formats. In the latter
 * case provide array keys 'src' and 'type'. Providing an array of
 * sources (even for a single source) is preferred as you can set
 * the correct mime type of the audio which is otherwise guessed
 * from the filename's extension.
 *
 * @author Xaver Maierhofer <xaver.maierhofer@xwissen.info>
 * @package Vhs
 * @subpackage ViewHelpers\Media
 */
class AudioViewHelper extends AbstractMediaViewHelper {

	use TagViewHelperTrait;

	/**
	 * @var string
	 */
	protected $tagName = 'audio';

	/**
	 * @var array
	 */
	protected $validTypes = array('mp3', 'ogg', 'oga', 'wav');

	/**
	 * @var array
	 */
	protected $mimeTypesMap = array('mp3' => 'audio/mpeg', 'ogg' => 'audio/ogg', 'oga' => 'audio/ogg', 'wav' => 'audio/wav');

	/**
	 * @var array
	 */
	protected $validPreloadModes = array('auto', 'metadata', 'none');

	/**
	 * Initialize arguments.
	 *
	 * @return void
	 * @api
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerUniversalTagAttributes();
		$this->registerArgument('width', 'integer', 'Sets the width of the audio player in pixels.', TRUE);
		$this->registerArgument('height', 'integer', 'Sets the height of the audio player in pixels.', TRUE);
		$this->registerArgument('autoplay', 'boolean', 'Specifies that the audio will start playing as soon as it is ready.', FALSE, FALSE);
		$this->registerArgument('controls', 'boolean', 'Specifies that audio controls should be displayed (such as a play/pause button etc).', FALSE, FALSE);
		$this->registerArgument('loop', 'boolean', 'Specifies that the audio will start over again, every time it is finished.', FALSE, FALSE);
		$this->registerArgument('muted', 'boolean', 'Specifies that the audio output of the audio should be muted.', FALSE, FALSE);
		$this->registerArgument('poster', 'string', 'Specifies an image to be shown while the audio is downloading, or until the user hits the play button.', FALSE, NULL);
		$this->registerArgument('preload', 'string', 'Specifies if and how the author thinks the audio should be loaded when the page loads. Can be "auto", "metadata" or "none".', FALSE, 'auto');
		$this->registerArgument('unsupported', 'string', 'Add a message for old browsers like Internet Explorer 9 without audio support.', FALSE);
	}

	/**
	 * Render method
	 *
	 * @throws Exception
	 * @return string
	 */
	public function render() {
		$sources = $this->getSourcesFromArgument();
		if (0 === count($sources)) {
			throw new Exception('No audio sources provided.', 1359382189);
		}

		foreach ($sources as $source) {
			if (TRUE === is_string($source)) {
				if (FALSE !== strpos($source, '//')) {
					$src = $source;
					$type = substr($source, strrpos($source, '.') + 1);
				} else {
					$src = substr(GeneralUtility::getFileAbsFileName($source), strlen(PATH_site));
					$type = pathinfo($src, PATHINFO_EXTENSION);
				}
			} elseif (TRUE === is_array($source)) {
				if (FALSE === isset($source['src'])) {
					throw new Exception('Missing value for "src" in sources array.', 1359381250);
				}
				$src = $source['src'];
				if (FALSE === isset($source['type'])) {
					throw new Exception('Missing value for "type" in sources array.', 1359381255);
				}
				$type = $source['type'];
			} else {
				// skip invalid source
				continue;
			}
			$type = strtolower($type);
			if (FALSE === in_array($type, $this->validTypes)) {
					throw new Exception('Invalid audio type "' . $type . '".', 1359381260);
			}
			$type = $this->mimeTypesMap[$type];
			$src = $this->preprocessSourceUri($src);
			$this->renderChildTag('source', array('src' => $src, 'type' => $type), FALSE, 'append');
		}
		$tagAttributes = array(
			'width'   => $this->arguments['width'],
			'height'  => $this->arguments['height'],
			'preload' => 'auto',
		);
		if (TRUE === (boolean) $this->arguments['autoplay']) {
			$tagAttributes['autoplay'] = 'autoplay';
		}
		if (TRUE === (boolean) $this->arguments['controls']) {
			$tagAttributes['controls'] = 'controls';
		}
		if (TRUE === (boolean) $this->arguments['loop']) {
			$tagAttributes['loop'] = 'loop';
		}
		if (TRUE === (boolean) $this->arguments['muted']) {
			$tagAttributes['muted'] = 'muted';
		}
		if (TRUE === in_array($this->arguments['preload'], $this->validPreloadModes)) {
			$tagAttributes['preload'] = 'preload';
		}
		if (NULL !== $this->arguments['poster']) {
			$tagAttributes['poster'] = $this->arguments['poster'];
		}
		$this->tag->addAttributes($tagAttributes);
		if (NULL !== $this->arguments['unsupported']) {
			$this->tag->setContent($this->tag->getContent() . LF . $this->arguments['unsupported']);
		}
		return $this->tag->render();
	}

}
