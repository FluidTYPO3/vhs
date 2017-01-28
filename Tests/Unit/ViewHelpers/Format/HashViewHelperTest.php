<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Format;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class HashViewHelperTest
 */
class HashViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @test
     */
    public function canHashWithMd5()
    {
        $test = $this->executeViewHelperUsingTagContent('please hash me', ['algorithm' => 'md5']);
        $this->assertSame('50f6980d1002ddfdeb3d8e40bc634d43', $test);
    }

    /**
     * @test
     */
    public function canHashWithSha1()
    {
        $test = $this->executeViewHelperUsingTagContent('please hash me', ['algorithm' => 'sha1']);
        $this->assertSame('8355145bb9c38cf4d829ca3e183f1092313dd55c', $test);
    }

    /**
     * @test
     */
    public function canHashWithSha256()
    {
        $test = $this->executeViewHelperUsingTagContent('please hash me', ['algorithm' => 'sha256']);
        $this->assertSame('49dfc24340b7504472f40c83daae1c3132fa43c29a70a7ad033b60bcd850726a', $test);
    }
}
