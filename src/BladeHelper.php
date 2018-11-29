<?php

namespace ImLiam\BladeHelper;

use Illuminate\View\Compilers\BladeCompiler;

class BladeHelper
{
    /**
     * All the custom "helper directive" callbacks.
     *
     * @var array
     */
    protected $customDirectives = [];

    protected $compiler;

    public function __construct(BladeCompiler $compiler)
    {
        $this->compiler = $compiler;
    }

    /**
     * Create a helper directive for a regular function.
     *
     * @param string          $directiveName
     * @param string|callable $function
     * @param bool            $shouldEcho
     */
    public function directive(string $directiveName, $function = null, bool $shouldEcho = true)
    {
        $echo = $shouldEcho ? 'echo ' : '';

        if (! is_string($function) && is_callable($function)) {
            $this->customDirectives[$directiveName] = $function;

            $this->compiler->directive($directiveName, function ($expression) use ($directiveName, $echo) {
                return "<?php {$echo}app('blade.helper')->getDirective('{$directiveName}', {$expression}); ?>";
            });

            return;
        }

        $functionName = $function ?? $directiveName;

        $this->compiler->directive($directiveName, function ($expression) use ($functionName, $echo) {
            return "<?php {$echo}{$functionName}({$expression}); ?>";
        });
    }

    /**
     * Get and execute a callback helper directive.
     *
     * @param string $name
     * @param mixed  ...$arguments
     *
     * @return mixed
     */
    public function getDirective(string $name, ...$arguments)
    {
        return $this->customDirectives[$name](...$arguments);
    }
}
