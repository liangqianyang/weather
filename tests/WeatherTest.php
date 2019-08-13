<?php
/**
 * Created by PhpStorm.
 * User: bb
 * Date: 2019/8/12
 * Time: 16:51
 */

namespace Amap\Weather\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Mockery\Matcher\AnyArgs;
use Amap\Weather\Exceptions\HttpException;
use Amap\Weather\Exceptions\InvalidArgumentException;
use Amap\Weather\Weather;
use PHPUnit\Framework\TestCase;

class WeatherTest extends TestCase
{
    /**
     * 测试错误的参数
     * @throws HttpException
     * @throws InvalidArgumentException
     */
    public function testGetWeatherWithInvalidType()
    {
        $w = new Weather('mock-key');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid type value(live/forecast): foo');

        $w->getWeather('深圳', 'foo');

        $this->fail('Failed to assert getWeather throw exception with invalid argument.');
    }

    /**
     * 测试错误的返回格式
     * @throws HttpException
     * @throws InvalidArgumentException
     */
    public function testGetWeatherWithInvalidFormat()
    {
        $w = new Weather('mock-key');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid response format: array');

        $w->getWeather('深圳', 'live', 'array');

        $this->fail('Failed to assert getWeather throw exception with invalid argument.');
    }

    /**
     * 测试请求异常
     */
    public function testGetWeatherWithGuzzleRuntimeException()
    {
        $client = \Mockery::mock(Client::class);
        $client->allows()
            ->get(new AnyArgs())
            ->andThrow(new \Exception('request timeout'));

        $w = \Mockery::mock(Weather::class, ['mock-key'])->makePartial();
        $w->allows()->getHttpClient()->andReturn($client);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('request timeout');

        $w->getWeather('深圳');
    }

    /**
     * 测试发起请求
     */
    public function testGetHttpClient()
    {
        $w = new Weather('mock-key');

        // 断言返回结果为 GuzzleHttp\ClientInterface 实例
        $this->assertInstanceOf(ClientInterface::class, $w->getHttpClient());
    }

    /**
     * 测试设置请求参数
     */
    public function testSetGuzzleOptions()
    {
        $w = new Weather('mock-key');

        // 设置参数前，timeout 为 null
        $this->assertNull($w->getHttpClient()->getConfig('timeout'));

        // 设置参数
        $w->setGuzzleOptions(['timeout' => 5000]);

        // 设置参数后，timeout 为 5000
        $this->assertSame(5000, $w->getHttpClient()->getConfig('timeout'));
    }

    /**
     * 测试获取实时天气
     */
    public function testGetLiveWeather()
    {
        // 将 getWeather 接口模拟为返回固定内容，以测试参数传递是否正确
        $w = \Mockery::mock(Weather::class, ['mock-key'])->makePartial();
        $w->expects()->getWeather('深圳', 'base', 'json')->andReturn(['success' => true]);

        // 断言正确传参并返回
        $this->assertSame(['success' => true], $w->getLiveWeather('深圳'));
    }

    /**
     * 测试获取天气预报
     */
    public function testGetForecastsWeather()
    {
        // 将 getWeather 接口模拟为返回固定内容，以测试参数传递是否正确
        $w = \Mockery::mock(Weather::class, ['mock-key'])->makePartial();
        $w->expects()->getWeather('深圳', 'all', 'json')->andReturn(['success' => true]);

        // 断言正确传参并返回
        $this->assertSame(['success' => true], $w->getForecastsWeather('深圳'));
    }
}