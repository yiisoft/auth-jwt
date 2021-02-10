<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://yiisoft.github.io/docs/images/yii_logo.svg" height="100px">
    </a>
    <h1 align="center">Yii Auth JWT</h1>
    <br>
</p>

[![Latest Stable Version](https://poser.pugx.org/yiisoft/auth-jwt/v/stable.png)](https://packagist.org/packages/yiisoft/auth-jwt)
[![Total Downloads](https://poser.pugx.org/yiisoft/auth-jwt/downloads.png)](https://packagist.org/packages/yiisoft/auth-jwt)
[![Build status](https://github.com/yiisoft/auth-jwt/workflows/build/badge.svg)](https://github.com/yiisoft/auth-jwt/actions)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yiisoft/auth-jwt/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/auth-jwt/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/yiisoft/auth-jwt/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yiisoft/auth-jwt/?branch=master)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyiisoft%2Fauth-jwt%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/yiisoft/auth-jwt/master)
[![static analysis](https://github.com/yiisoft/auth-jwt/workflows/static%20analysis/badge.svg)](https://github.com/yiisoft/auth-jwt/actions?query=workflow%3A%22static+analysis%22)
[![type-coverage](https://shepherd.dev/github/yiisoft/auth-jwt/coverage.svg)](https://shepherd.dev/github/yiisoft/auth-jwt)

The package provides [JWT authentication](https://tools.ietf.org/html/rfc7519) method for [Yii Auth](https://github.com/yiisoft/auth/).

## Requirements

- PHP 7.4 or higher.

## Installation

The package could be installed with composer:

```
composer require yiisoft/auth-jwt --prefer-dist
```

## General usage

### Configuring within Yii

1. Set JWT parameters in your `params.php` config file:
    ```php
    'yiisoft/auth-jwt' => [
        'algorithms' => [
            // your signature algorithms
        ],
        'serializers' => [
            // your token serializers
        ],
        'key' => [
            'secret' => 'your-secret',
            'file' => 'your-certificate-file',
        ],
    ],
    ```
2. Setup definitions, required for `\Yiisoft\Auth\Middleware\Authentication` middleware in a config, for example,
   in `config/web/auth.php`:
   ```php   
   <?php
   
   declare(strict_types=1);
   
   /** @var array $params */
   
   use Yiisoft\Auth\Jwt\TokenManagerInterface;
   use Yiisoft\Auth\Jwt\TokenManager;
   use Yiisoft\Auth\AuthenticationMethodInterface;
   use Yiisoft\Auth\Jwt\JwtMethod;
   
   return [
       KeyFactoryInterface::class => [
           '__class' => FromSecret::class,
           '__construct()' => [
               $params['yiisoft/auth-jwt']['key']['secret']
           ],
       ],
       
       AuthenticationMethodInterface::class => JwtMethod::class,
   ];
   ```
   > Note: Don't forget to declare your implementations of `\Yiisoft\Auth\IdentityInterface` and `\Yiisoft\Auth\IdentityRepositoryInterface`.

3. Use `Yiisoft\Auth\Middleware\Authentication` middleware.
   Read more about middlewares in the [middleware guide](https://github.com/yiisoft/docs/blob/master/guide/en/structure/middleware.md). 

### Configuring independently

You can configure `Authentication` middleware manually:

```php
/** @var \Yiisoft\Auth\IdentityRepositoryInterface $identityRepository */
$identityRepository = getIdentityRepository();

$tokenRepository = $container->get(\Yiisoft\Auth\Jwt\TokenRepositoryInterface::class);

$authenticationMethod = new \Yiisoft\Auth\Jwt\JwtMethod($identityRepository, $tokenRepository);

$middleware = new \Yiisoft\Auth\Middleware\Authentication(
    $authenticationMethod,
    $responseFactory, // PSR-17 ResponseFactoryInterface
    $failureHandler // optional, \Yiisoft\Auth\Handler\AuthenticationFailureHandler by default
);
```

## Unit testing

The package is tested with [PHPUnit](https://phpunit.de/). To run tests:

```shell
./vendor/bin/phpunit
```

## Mutation testing

The package tests are checked with [Infection](https://infection.github.io/) mutation framework with
[Infection Static Analysis Plugin](https://github.com/Roave/infection-static-analysis-plugin). To run it:

```shell
./vendor/bin/roave-infection-static-analysis-plugin
```

## Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/). To run static analysis:

```shell
./vendor/bin/psalm
```

## License

The Yii Auth JWT is free software. It is released under the terms of the BSD License.
Please see [`LICENSE`](./LICENSE.md) for more information.

Maintained by [Yii Software](https://www.yiiframework.com/).
