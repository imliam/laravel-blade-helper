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

    /**
     * Register an "if" statement directive.
     *
     * @param string $directiveName
     * @param callable $function
     *
     * @return void
     */
    public function if(string $directiveName, callable $function)
    {
        $this->customDirectives[$directiveName] = $function;

        $this->compiler->directive($directiveName, function ($expression) use ($directiveName) {
            return "<?php if (app('blade.helper')->getDirective('{$directiveName}', {$expression})): ?>";
        });

        $this->compiler->directive('else' . ucfirst($directiveName), function ($expression) use ($directiveName) {
            return "<?php elseif (app('blade.helper')->getDirective('{$directiveName}', {$expression})): ?>";
        });

        $this->compiler->directive('end' . ucfirst($directiveName), function () {
            return "<?php endif; ?>";
        });
    }
}
