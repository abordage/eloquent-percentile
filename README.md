## Laravel Eloquent withMedian(), withPercentile() for PostgeSQL

The package provides several aggregate functions that work in the same way as `withAvg()`, `withMax()`

 <img alt="Eloquent withMedian, withPercentile" src="https://github.com/abordage/eloquent-percentile/blob/master/docs/images/eloquent-with-median-percentile.png?raw=true">

<p style="text-align: center;" align="center">

<a href="https://packagist.org/packages/abordage/eloquent-percentile" title="Packagist version">
    <img alt="Packagist Version" src="https://img.shields.io/packagist/v/abordage/eloquent-percentile">
</a>

<a href="https://coveralls.io/github/abordage/eloquent-percentile" title="Coverage Status">
    <img alt="Coverage Status" src="https://img.shields.io/coverallsCoverage/github/abordage/eloquent-percentile">
</a>

<a href="https://github.com/abordage/eloquent-percentile/actions/workflows/tests.yml" title="GitHub Tests Status">
    <img alt="GitHub Tests Status" src="https://img.shields.io/github/actions/workflow/status/abordage/eloquent-percentile/tests.yml?label=tests">
</a>

<a href="https://github.com/abordage/eloquent-percentile/actions/workflows/php-cs-fixer.yml" title="GitHub Code Style Status">
    <img alt="GitHub Code Style Status" src="https://img.shields.io/github/actions/workflow/status/abordage/eloquent-percentile/php-cs-fixer.yml?label=code%20style">
</a>

<a href="https://www.php.net/" title="PHP version">
    <img alt="PHP Version Support" src="https://img.shields.io/packagist/php-v/abordage/eloquent-percentile">
</a>

<a href="https://github.com/abordage/eloquent-percentile/blob/master/LICENSE.md" title="License">
    <img alt="License" src="https://img.shields.io/github/license/abordage/eloquent-percentile">
</a>

</p>

## Requirements
- PHP 7.4 or higher
- Laravel 8+

## Supports:
- PostgreSQL

## Installation

You can install the package via composer:

```bash
composer require abordage/eloquent-percentile
```

## Usage
### Aggregating Related Models

The method `withMedian()` will place a `{relation}_median_{column}` attribute on your resulting models:

```php
use App\Models\Post;
 
$posts = Post::withMedian('comments', 'votes')->get();
 
foreach ($posts as $post) {
    echo $post->comments_median_votes;
}
```

The method `withPercentile()` will place a `{relation}_percentile{percentile*100}_{column}` attribute on your resulting models:

```php
use App\Models\Post;
 
$posts = Post::withPercentile('comments', 'votes', 0.85)->get();
 
foreach ($posts as $post) {
    echo $post->comments_percentile85_votes;
}
```

### Retrieving Aggregates
When interacting with Eloquent models, you may also use the `percentile` and `median` aggregate methods. 
As you might expect, these methods return a scalar value instead of an Eloquent model instance:
```php
$median = Comment::where('active', 1)->median('votes');
 
$percentile95 = Comment::where('active', 1)->percentile('votes', 0.95);
```

## Automatic PHPDocs for models
If you are using the [ide-helper](https://github.com/barryvdh/laravel-ide-helper) you can describe the attributes with the 
[Model Hooks](https://github.com/barryvdh/laravel-ide-helper#model-hooks). For example:
```php
<?php

namespace App\Support\IdeHelper;

use App\Models\Post;
use Barryvdh\LaravelIdeHelper\Console\ModelsCommand;
use Barryvdh\LaravelIdeHelper\Contracts\ModelHookInterface;
use Illuminate\Database\Eloquent\Model;

class PostHook implements ModelHookInterface
{
    public function run(ModelsCommand $command, Model $model): void
    {
        if (!$model instanceof Post) {
            return;
        }

        $command->setProperty('comments_median_votes', 'float|null', true, false);
        $command->setProperty('comments_percentile80_votes', 'float|null', true, false);
        $command->setProperty('comments_percentile95_votes', 'float|null', true, false);
    }
}
```



## Testing
Before running the tests, rename the `phpunit.xml.dist` to `phpunit.xml` and specify your database connection settings:
```xml
<php>
    <env name="DB_CONNECTION_POSTGRES" value="pgsql"/>
    <env name="DB_HOST_POSTGRES" value="postgres"/>
    <env name="DB_PORT_POSTGRES" value="5432"/>
    <env name="DB_DATABASE_POSTGRES" value="eloquent_percentile_test"/>
    <env name="DB_USERNAME_POSTGRES" value="default"/>
    <env name="DB_PASSWORD_POSTGRES" value="secret"/>
</php>
```

Next run:

```bash
composer test:all
```

or

```bash
composer test:phpunit
composer test:phpstan
composer test:phpcsf
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/abordage/.github/blob/master/CONTRIBUTING.md) for details.

## Security

Please review [our security policy](https://github.com/abordage/.github/security/policy) on how to report
security vulnerabilities.

## Feedback
Find a bug or have a feature request? Open an issue, or better yet, submit a pull request - contribution welcome!

## Credits

- [Pavel Bychko](https://github.com/abordage)
- [All Contributors](https://github.com/abordage/eloquent-percentile/graphs/contributors)

## Thanks to
The original idea comes from the [tailslide-php](https://github.com/ankane/tailslide-php), so many thanks to its author!

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
