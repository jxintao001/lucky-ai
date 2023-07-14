<?php

namespace App\Providers;

use EasyWeChat\Factory;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Monolog\Logger;
use Wythe\Logistics\Logistics;
use Yansongda\Pay\Pay;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Carbon::setLocale('zh');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        // 往服务容器中注入一个名为 alipay 的单例对象
        $this->app->singleton('alipay', function () {
            $config = config('pay.alipay');
            $config['notify_url'] = app('Dingo\Api\Routing\UrlGenerator')->version('v1')->route('payment.alipay.notify');
            $config['return_url'] = app('Dingo\Api\Routing\UrlGenerator')->version('v1')->route('payment.alipay.return');
            // 判断当前项目运行环境是否为线上环境
            if (app()->environment() !== 'production') {
                //$config['mode']         = 'dev';
                $config['log']['level'] = Logger::DEBUG;
            } else {
                $config['log']['level'] = Logger::WARNING;
            }
            // 调用 Yansongda\Pay 来创建一个支付宝支付对象
            return Pay::alipay($config);
        });

        $this->app->singleton('wechat_pay', function () {
            $config = config('pay.wechat');
            $config['notify_url'] = app('Dingo\Api\Routing\UrlGenerator')->version('v1')->route('payment.wechat.notify');
            if (app()->environment() !== 'production') {
                $config['log']['level'] = Logger::DEBUG;
                $config['log']['type'] = 'single';
            } else {
                $config['log']['level'] = Logger::INFO;
                $config['log']['type'] = 'single';
            }
            // 调用 Yansongda\Pay 来创建一个微信支付对象
            return Pay::wechat($config);
        });

        $this->app->singleton('logistics', function () {
            $config = config('logistics');
            return new Logistics($config);
        });
        
        // 往服务容器中注入一个名为 official_account（公众号）的单例对象
        $this->app->singleton('official_account', function () {
            $config = config('wechat.official_account.default');
            return Factory::officialAccount($config);
        });
    }
}
