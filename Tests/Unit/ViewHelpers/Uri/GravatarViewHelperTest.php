<?php
namespace FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\Uri;

/*
 * This file is part of the FluidTYPO3/Vhs project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FluidTYPO3\Vhs\Tests\Unit\ViewHelpers\AbstractViewHelperTest;

/**
 * Class GravatarViewHelperTest
 */
class GravatarViewHelperTest extends AbstractViewHelperTest
{

    /**
     * @var array
     */
    protected $arguments = array(
        'email' => 'juanmanuel.vergessolanas@gmail.com',
        'secure' => false,
    );

    /**
     * @test
     */
    public function generatesExpectedUriForEmailAddress()
    {
        $expectedSource = 'http://www.gravatar.com/avatar/b1b0eddcbc4468db89f355ebb9cc3007';
        $this->assertSame($expectedSource, $this->executeViewHelper($this->arguments));
        $expectedSource = 'https://secure.gravatar.com/avatar/b1b0eddcbc4468db89f355ebb9cc3007?s=160&d=404&r=pg';
        $this->arguments = array(
            'email' => 'juanmanuel.vergessolanas@gmail.com',
            'size' => 160,
            'imageSet' => '404',
            'maximumRating' => 'pg',
            'secure' => true,
        );
        $this->assertSame($expectedSource, $this->executeViewHelper($this->arguments));
    }
}
