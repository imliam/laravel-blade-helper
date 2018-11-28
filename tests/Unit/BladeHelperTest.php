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

    public function setUp()
    {
        $this->compiler = new BladeCompiler(m::mock('Illuminate\Filesystem\Filesystem'), __DIR__);
        $this->helper = new BladeHelper($this->compiler);
        parent::setUp();
    }

    public function tearDown()
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
        $expected = '<?php echo \\' . get_class($this->helper) . '::getDirective(\'example\', "Never", "gonna"); ?>';
        $this->assertEquals($expected, $this->compiler->compileString($string));
        echo $expected;
    }
}
