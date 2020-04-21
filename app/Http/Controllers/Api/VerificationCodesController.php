<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\VerificationCodeRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;

class VerificationCodesController extends Controller
{
    public function store(VerificationCodeRequest $request, EasySms $easySms)
    {
        $captchaData = Cache::get($request->captcha_key);

        if (!$captchaData) {
            abort(403, '图片验证码已失效');
        }
        if (!hash_equals($captchaData['code'], $request->captcha_code)) {
            Cache::forget($request->captcha_key);
            throw new AuthenticationException('验证码错误');
        }
        $phone = $captchaData['phone'];
        if (!app()->environment('production')) {
            $code = '1234';
        } else {
            // 生成4位随机数，左侧补0
            $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);

            try {
                $result = $easySms->send($phone, [
                    'template' => config('easysms.gateways.aliyun.templates.register'),
                    'data' => [
                        'code' => $code
                    ]
                ]);
            } catch (NoGatewayAvailableException $e) {
                $message = $e->getException('aliyun')->getMessage();
                abort(500,$message?:'短息异常');
            }
        }

        $key = 'verificationCode_'.Str::random(15);
        $expiredAt = now()->addMinute(5);
        // 缓存验证码 5分钟过期
        Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);
        // 清除图片验证码缓存
        Cache::forget($request->captcha_key);

        return response()->json([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString()
        ])->setStatusCode(201);
    }
}
