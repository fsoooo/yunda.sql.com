<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     * 全局中间件，最先调用
     * @var array
     */
    protected $middleware = [
        // 检测是否进入[维护模式]
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        // 检测请求的数据是否过大
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        // 对提交的请求参数进行 php 函数 `trim()` 处理
        \App\Http\Middleware\TrimStrings::class,
        // 将提交的请求参数中空子串转换为 null
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     * 定义中间件组
     * @var array
     */
    protected $middlewareGroups = [
        // web 中间件组，应用于 route/web.php 路由文件
        'web' => [
            // cookie 加密解密
            \App\Http\Middleware\EncryptCookies::class,
            // 将 cookie 添加到响应中
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            // 开启会话
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            // 检验 CSRF ,防止跨站请求伪造的安全威胁
            \App\Http\Middleware\VerifyCsrfToken::class,
            // 处理路由绑定
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    // API 中间件组，应用于 routes/api.php 路由文件
        'api' => [
            // 使用别名来调中间件
            'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     * 中间件别名设置，允许你使用别名调用中间件，例如上面的 api 中间件组调用
     * @var array
     */
    protected $routeMiddleware = [
        // 只有登录用户才能访问，我们在控制器的构造方法中大量使用
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        // HTTP Basic Auth 认证
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        // 用户授权功能
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'agent'=>\App\Http\Middleware\IsAgent::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        //my middleware
        'admin.login' => \App\Http\Middleware\AdminLogin::class,
        'admin.role' => \App\Http\Middleware\AdminRole::class,
        'owner.role' => \App\Http\Middleware\OwnerRole::class,
        'worker.role' => \App\Http\Middleware\WorkerRole::class,
        'check.channels.api.sign' => \App\Http\Middleware\CheckChannelsApiSign::class,
        'check.channels.api.token' => \App\Http\Middleware\CheckChannelsApiAccessToken::class,
        //role
        'role' => \Zizaco\Entrust\Middleware\EntrustRole::class,
        'permission' => \Zizaco\Entrust\Middleware\EntrustPermission::class,
        'ability' => \Zizaco\Entrust\Middleware\EntrustAbility::class,
        //Dingo,JWT
        'account.change' => \App\Http\Middleware\AccountChange::class,
        'jwt.auth' => \Tymon\JWTAuth\Middleware\GetUserFromToken::class,
        'jwt.refresh' => \Tymon\JWTAuth\Middleware\RefreshToken::class,
    ];
}
