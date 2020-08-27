<p align="center">xiaohuyun xhysanctum</p>



## 简介

小狐云 xhysanctum 为spa和简单的api提供了一个轻量级的认证系统。基于官方sanctum开发的。

## 官方文档

文档可以在[Laravel website](https://laravel.com/docs/master/sanctum).


## 行为准则

请审阅并遵守 [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## 安全漏洞

请审阅【我们的安全政策】(https://github.com/feng2009/xhysanctum/security/policy) on how to report security vulnerabilities.

## 许可证

小狐云 xhysanctum 是一个开源软件，根据[MIT许可证]授权 [MIT license](LICENSE.md).

## 安装过程

composer require xiaohuyun/xhysanctum

接下来，你需要使用 vendor:publish Artisan 命令发布 Sanctum 的配置和迁移文件。Sanctum 的配置文件将会保存在 config 文件夹中

php artisan vendor:publish --provider="Xiaohuyun\xhysanctum\SanctumServiceProvider"

最后，你需要执行数据库迁移文件。Sanctum 将创建一个数据库表用于存储 API 令牌：

php artisan migrate

假如你需要使用 Sanctum 来验证 SPA，你需要在 app/Http/Kernel.php 文件中将 Sanctum 的中间件添加到你的 api 中间件组中：

use Xiaohuyun\xhysanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

'api' => [
    EnsureFrontendRequestsAreStateful::class,
    'throttle:60,1',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
