# Laravel Blade Helper

[![Latest Version on Packagist](https://img.shields.io/packagist/v/imliam/laravel-blade-helper.svg)](https://packagist.org/packages/imliam/laravel-blade-helper)
[![Total Downloads](https://img.shields.io/packagist/dt/imliam/laravel-blade-helper.svg)](https://packagist.org/packages/imliam/laravel-blade-helper)
[![License](https://img.shields.io/github/license/imliam/laravel-blade-helper.svg)](LICENSE.md)

An easier way to define custom Blade directives.

When creating new custom Blade directives using the `Blade::directive(…)` method, the only parameter made available to manipulate is the expression passed through from the .blade.php file as a raw string. It seems to be rare that developers actually parse the contents of the expression itself within the directive, opting instead to pass the entire expression as arguments to a helper function or a method on another class. For example:

```php
Illuminate\Support\Facades\Blade::directive('uppercase', function($expression) {
    return "<?php echo strtoupper($expression); ?>";
});
```

As this seems to be the most common use case, this package attempts to help make these helper functions that little bit easier to define without the boilerplate of returning the string or having to consider what an expression may be when creating a directive.

<!-- TOC -->

- [Laravel Blade Helper](#laravel-blade-helper)
    - [💾 Installation](#💾-installation)
    - [📝 Usage](#📝-usage)
        - [Example Helper Directive](#example-helper-directive)
        - [Custom "if" Directive](#custom-if-directive)
    - [✅ Testing](#✅-testing)
    - [🔖 Changelog](#🔖-changelog)
    - [⬆️ Upgrading](#⬆️-upgrading)
    - [🎉 Contributing](#🎉-contributing)
        - [🔒 Security](#🔒-security)
    - [👷 Credits](#👷-credits)
    - [♻️ License](#♻️-license)

<!-- /TOC -->

## 💾 Installation

You can install the package with [Composer](https://getcomposer.org/) using the following command:

```bash
composer require imliam/laravel-blade-helper:^1.0
```

## 📝 Usage

The BladeHelper object is bound to Laravel's service container with the name `blade.helper` and can be used by resolving that. A Facade is also made available for convenience. To define a helper, the `directive(…)` method is used:

```php
app('blade.helper')->directive(…);

\ImLiam\BladeHelper\Facades\BladeHelper::directive(…);
```

This method accepts two arguments; the first is the name of the directive, and the second is the function that the directive should call:

```php
// Define the helper directive
BladeHelper::directive('uppercase', 'strtoupper');

// Use it in a view
@uppercase('Hello world.')

// Get the compiled result
<?php echo strtoupper('Hello world.'); ?>

// See what's echoed
"HELLO WORLD."
```

If no second argument is supplied, the directive will attempt to call a function of the same name:

```php
// Define the helper directive
BladeHelper::directive('join');

// Use it in a view
@join('|', ['Hello', 'world'])

// Get the compiled result
<?php echo join('|', ['Hello', 'world']); ?>

// See what's echoed
"Hello|world"
```

The second argument can also take a callback. The advantage of a callback here over the typical `Blade::directive(…)` method Laravel offers is that the callback given can have specific parameters defined instead of just getting raw expression as a string. This brings several advantages to the process of creating a Blade helper directive:

- Type hint the arguments for the callback
- Manipulate and use the individual arguments when the directive is called, instead of the raw expression as a string
- Define a directive without having to only use it as a proxy to a helper function or class in another part of the application

```php
// Define the helper directive
BladeHelper::directive('example', function($a, $b, $c = 'give', $d = 'you') {
    return "$a $b $c $d up";
});

// Use it in a view
@example('Never', 'gonna')

// Get the compiled result
<?php echo app('blade.helper')->getDirective('example', 'Never', 'gonna'); ?>

// See what's echoed
"Never gonna give you up"
```

By default, all of the helper directives will echo out their contents to the view when used. This can be disabled by passing `false` as the third argument:

```php
// Define the helper directive
BladeHelper::directive('log', null, false);

// Use it in a view
@log('View loaded…')

// Get the compiled result
<?php log('View loaded…'); ?>

// Nothing is echoed
```

### Example Helper Directive

One example of a custom Blade helper is to wrap around [FontAwesome 4](https://fontawesome.com/v4.7.0/) icons to make it more convenient to add alternate text for the sake of accessibility:

```php
// Define the helper directive
BladeHelper::directive('fa', function(string $iconName, string $text = null, $classes = '') {
    if (is_array($classes)) {
        $classes = join(' ', $classes);
    }

    $text = $text ?? $iconName;

    return "<i class='fa fa-{$iconName} {$classes}' aria-hidden='true' title='{$text}'></i><span class='sr-only'>{$text}</span>";
});

// Use it in a view
@fa('email', 'Envelope')
```

### Custom "if" Directive

Laravel Blade offers [a handy way](https://laravel.com/docs/5.8/blade#custom-if-statements) to define custom "if" statement directives. The Blade Helper package offers an additional method to generate these directives, with `if`, `elseif` and `endif` variants all automatically generated.

An if statement can be defined in the same way as the directive method, but must be given a callable as its second argument:

```php
BladeHelper::if('largestFirst', function(int $a, int $b): bool {
    return $a > $b;
});
```

Once defined, the helpers can be used directly in your Blade templates:

```html
@largestFirst(1, 2)
    Lorem ipsum
@elseLargestFirst(5, 3)
    dolor sit amet
@else
    consectetur adipiscing elit
@endLargestFirst
```

## ✅ Testing

``` bash
composer test
```

## 🔖 Changelog

Please see [the changelog file](CHANGELOG.md) for more information on what has changed recently.

## ⬆️ Upgrading

Please see the [upgrading file](UPGRADING.md) for details on upgrading from previous versions.

## 🎉 Contributing

Please see the [contributing file](CONTRIBUTING.md) and [code of conduct](CODE_OF_CONDUCT.md) for details on contributing to the project.

### 🔒 Security

If you discover any security related issues, please email liam@liamhammett.com instead of using the issue tracker.

## 👷 Credits

- [Liam Hammett](https://github.com/imliam)
- [@bhuvidya](https://github.com/bhuvidya) for initially extracting the [original PR](https://github.com/laravel/framework/pull/24923) to [a package](https://github.com/bhuvidya/laravel-blade-helper)
- [All Contributors](../../contributors)

## ♻️ License

The MIT License (MIT). Please see the [license file](LICENSE.md) for more information.
