<?php

namespace ImLiam\BladeHelper\Tests\Unit;

use Illuminate\View\Compilers\BladeCompiler;
use ImLiam\BladeHelper\BladeHelper;
use ImLiam\BladeHelper\Tests\TestCase;
use Mockery as m;

class BladeHelperTest extends TestCase
{
    protected $compiler;

    protected $helper;

    public function setUp(): void
    {
        $this->compiler = new BladeCompiler(m::mock('Illuminate\Filesystem\Filesystem'), __DIR__);
        $this->helper = new BladeHelper($this->compiler);
        parent::setUp();
    }

    public function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }

    /** @test */
    public function custom_named_helpers_compile_correctly()
    {
        $this->assertCount(0, $this->compiler->getCustomDirectives());
        $this->helper->directive('uppercase', 'strtoupper');
        $this->assertCount(1, $this->compiler->getCustomDirectives());
        $string = '@uppercase("Hello world.")';
        $expected = '<?php echo strtoupper("Hello world."); ?>';
        $this->assertEquals($expected, $this->compiler->compileString($string));
    }

    /** @test */
    public function custom_unnamed_helpers_compile_correctly()
    {
        $this->assertCount(0, $this->compiler->getCustomDirectives());
        $this->helper->directive('join');
        $this->assertCount(1, $this->compiler->getCustomDirectives());
        $string = '@join("|", ["Hello", "world"])';
        $expected = '<?php echo join("|", ["Hello", "world"]); ?>';
        $this->assertEquals($expected, $this->compiler->compileString($string));
    }

    /** @test */
    public function custom_helpers_without_echo_compile_correctly()
    {
        $this->assertCount(0, $this->compiler->getCustomDirectives());
        $this->helper->directive('join', null, false);
        $this->assertCount(1, $this->compiler->getCustomDirectives());
        $string = '@join("|", ["Hello", "world"])';
        $expected = '<?php join("|", ["Hello", "world"]); ?>';
        $this->assertEquals($expected, $this->compiler->compileString($string));
    }

    /** @test */
    public function custom_helper_callbacks_compile_correctly()
    {
        $this->assertCount(0, $this->compiler->getCustomDirectives());
        $this->helper->directive('example', function ($a, $b, $c = 'give', $d = 'you') {
            return "$a $b $c $d up";
        });
        $this->assertCount(1, $this->compiler->getCustomDirectives());
        $string = '@example("Never", "gonna")';
        $expected = '<?php echo app(\'blade.helper\')->getDirective(\'example\', "Never", "gonna"); ?>';
        $this->assertEquals($expected, $this->compiler->compileString($string));
    }

    /** @test */
    public function custom_if_helpers_can_be_registered()
    {
        $this->assertCount(0, $this->compiler->getCustomDirectives());
        $this->helper->if('largestFirst', function ($a, $b) {
            return $a > $b;
        });
        $this->assertCount(3, $this->compiler->getCustomDirectives());

        $string = <<<EOL
@largestFirst(1, 2)
    Lorem ipsum
@elseLargestFirst(5, 3)
    dolor sit amet
@else
    consectetur adipiscing elit
@endLargestFirst
EOL;

        $expected = <<<EOL
<?php if (app('blade.helper')->getDirective('largestFirst', 1, 2)): ?>
    Lorem ipsum
<?php elseif (app('blade.helper')->getDirective('largestFirst', 5, 3)): ?>
    dolor sit amet
<?php else: ?>
    consectetur adipiscing elit
<?php endif; ?>
EOL;

        $this->assertEquals($expected, $this->compiler->compileString($string));
    }
}
