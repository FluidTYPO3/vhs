<?php
namespace FluidTYPO3\Vhs\View;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\ContentObject\AbstractContentObject;

class UncacheContentObject extends AbstractContentObject
{
    public function callUserFunction(string $function, array $conf): string
    {
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var UncacheTemplateView $uncacheTemplateView */
        $uncacheTemplateView = $objectManager->get(UncacheTemplateView::class);
        return (string) $uncacheTemplateView->callUserFunction('', $conf, '');
    }

    public function render($conf = [])
    {
        return $this->callUserFunction('', $conf);
    }
}
