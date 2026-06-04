<?php
namespace FluidTYPO3\Vhs\Tests\Fixtures\Classes;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface;

class DummyViewHelperNode
{
    private readonly ViewHelperNode $node;

    public function __construct(ViewHelperInterface $viewHelper)
    {
        $this->node = $this->buildNode($viewHelper);
    }

    public function getNode(): ViewHelperNode
    {
        return $this->node;
    }

    public function setArguments(array $arguments): void
    {
        $this->node->setArguments($arguments);
    }

    public function addChildNode(\TYPO3Fluid\Fluid\Core\Parser\SyntaxTree\NodeInterface $node): void
    {
        $this->node->addChildNode($node);
    }

    public function getArguments(): array
    {
        return $this->node->getArguments();
    }

    private function buildNode(ViewHelperInterface $viewHelper): ViewHelperNode
    {
        $viewHelperNode = (new \ReflectionClass(ViewHelperNode::class))->newInstanceWithoutConstructor();
        $viewHelperNodeReflection = new \ReflectionClass(ViewHelperNode::class);

        foreach ([
            'namespace' => 'vhs',
            'name' => 'viewHelper',
            'viewHelperClassName' => \get_class($viewHelper),
            'arguments' => [],
            'argumentDefinitions' => [],
        ] as $propertyName => $value) {
            $property = $viewHelperNodeReflection->getProperty($propertyName);
            $property->setAccessible(true);
            $property->setValue($viewHelperNode, $value);
        }

        $property = $viewHelperNodeReflection->getProperty('childNodes');
        $property->setAccessible(true);
        $property->setValue($viewHelperNode, []);

        $property = $viewHelperNodeReflection->getProperty('uninitializedViewHelper');
        $property->setAccessible(true);
        $property->setValue($viewHelperNode, $viewHelper);

        return $viewHelperNode;
    }
}
