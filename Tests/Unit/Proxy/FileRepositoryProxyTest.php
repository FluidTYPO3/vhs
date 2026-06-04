<?php
namespace FluidTYPO3\Vhs\Tests\Unit\Proxy;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Proxy\FileRepositoryProxy;
use FluidTYPO3\Vhs\Tests\Unit\AbstractTestCase;

class FileRepositoryProxyTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function findByRelationKeepsOptionalWorkspaceIdArgument(): void
    {
        $method = new \ReflectionMethod(FileRepositoryProxy::class, 'findByRelation');
        $parameters = $method->getParameters();

        $this->assertCount(4, $parameters);
        $this->assertSame('workspaceId', $parameters[3]->getName());
        $this->assertTrue($parameters[3]->allowsNull());
        $this->assertTrue($parameters[3]->isDefaultValueAvailable());
        $this->assertNull($parameters[3]->getDefaultValue());
    }
}
