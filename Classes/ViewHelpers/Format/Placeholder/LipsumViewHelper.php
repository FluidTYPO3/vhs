<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Claus Due <claus@namelesscoder.net>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Lipsum ViewHelper
 *
 * Renders Lorem Ipsum text according to provided arguments.
 *
 * @author Claus Due
 * @package Vhs
 * @subpackage ViewHelpers\Format\Placeholder
 */
class Tx_Vhs_ViewHelpers_Format_Placeholder_LipsumViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var string
	 */
	protected $lipsum;

	/**
	 * @var	\TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
	 */
	protected $contentObject;

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 */
	protected $configurationManager;

	/**
	 * @return void
	 */
	public function initialize() {
		$this->lipsum = $this->getDefaultLoremIpsum();
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
	 * @return void
	 */
	public function injectConfigurationManager(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager) {
		$this->configurationManager = $configurationManager;
		$this->contentObject = $this->configurationManager->getContentObject();
	}

	/**
	 * Initialize arguments
	 */
	public function initializeArguments() {
		$this->registerArgument('paragraphs', 'integer', 'Number of paragraphs to output');
		$this->registerArgument('wordsPerParagraph', 'integer', 'Number of words per paragraph');
		$this->registerArgument('skew', 'integer', 'Amount in number of words to vary the number of words per paragraph');
		$this->registerArgument('html', 'boolean', 'If TRUE, renders output as HTML paragraph tags in the same way an RTE would');
		$this->registerArgument('parseFuncTSPath', 'string', 'If you want another parseFunc for HTML processing, enter the TS path here');
	}

	/**
	 * Renders Lorem Ipsum paragraphs. If $lipsum is provided it
	 * will be used as source text. If not provided as an argument
	 * or as inline argument, $lipsum is fetched from TypoScript settings.
	 *
	 * @param string $lipsum String of paragraphs file path or EXT:myext/path/to/file
	 * @return string
	 */
	public function render($lipsum = NULL) {
		if (strlen($lipsum) === 0) {
			$lipsum = $this->lipsum;
		}
		if ((strlen($lipsum) < 255 && !preg_match('/[^a-z0-9_\.\:\/]/i', $lipsum)) || 0 === strpos($lipsum, 'EXT:')) {
				// argument is most likely a file reference.
			$sourceFile = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($lipsum);
			if (file_exists($sourceFile) === TRUE) {
				$lipsum = file_get_contents($sourceFile);
			} else {
				\TYPO3\CMS\Core\Utility\GeneralUtility::sysLog('Vhs LipsumViewHelper was asked to load Lorem Ipsum from a file which does not exist. ' .
					'The file was: ' . $sourceFile, 'Vhs');
				$lipsum = $this->lipsum;
			}
		}
		$lipsum = preg_replace('/[\\r\\n]{1,}/i', "\n", $lipsum);
		$paragraphs = explode("\n", $lipsum);
		$paragraphs = array_slice($paragraphs, 0, intval($this->arguments['paragraphs']));
		foreach ($paragraphs as $index => $paragraph) {
			$length = $this->arguments['wordsPerParagraph'] + rand(0 - intval($this->arguments['skew']), intval($this->arguments['skew']));
			$words = explode(' ', $paragraph);
			$paragraphs[$index] = implode(' ', array_slice($words, 0, $length));
		}

		$lipsum = implode("\n", $paragraphs);
		if ((boolean) $this->arguments['html'] === TRUE) {
			$tsParserPath = (FALSE === empty($this->arguments['parseFuncTSPath']) ? '< ' . $this->arguments['parseFuncTSPath'] : NULL);
			$lipsum = $this->contentObject->parseFunc($lipsum, array(), $tsParserPath);
		}
		return $lipsum;
	}

	/**
	 * Get the default Lorem Ipsum. The compressed block (which is
	 * of course cleaned thoroughly to avoid any injection) contains
	 * 20 full paragraphs of Lorem Ipsum in standard latin. No bells
	 * and whistles there.
	 *
	 * @return string
	 */
	protected function getDefaultLoremIpsum() {
			// Note: this MAY look suspicious but it really is just a whole lot of Lipsum
			// in a compressed state. Just to make sure that you trust the block, we run
			// strip_tags and htmlentities on the string before it is returned. This is not
			// done on custom Lipsum - but it is done here at no risk, since we know the
			// Lipsum to contain zero HTML and zero special characters, 100% ASCII. Source
			// of the Lipsum text is http://www.lipsum.com set at 20 paragraphs, compressed
			// through a small shell script.
		$lipsum = <<<LIPSUM
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
LIPSUM;
		$uncompressed = gzuncompress(base64_decode($lipsum));
		$safe = htmlentities(strip_tags($uncompressed));
		return $safe;
	}

}
