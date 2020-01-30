<?php


namespace App\Tests\Infrastructure\Services\Legacy;


use App\Domain\DTO\ServiceApiResponseDTO;
use App\Infrastructure\Controller\ApiController;
use App\Infrastructure\Services\Api\ApiService;
use PHPUnit\Framework\TestCase;
use App\Infrastructure\Services\Legacy\ServiceAPIClientVerifyPhone;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Flex\Response;

class ServiceApiClientVerifyPhoneTest extends TestCase
{
    /**
     * @var string
     */
    const API_KEY = 'asdasdasdasdasdasdasdasdasdasdadasdasd';
    /**
     * @var string Sign for request
     */
    private $sign;

    /**
     * @var array Request
     */
    private $request;

    private static $counter = 0;

    /**
     * @param $request
     * @dataProvider provideRequest
     */
    public function testRun($request)
    {
        $this->request = $request;
        $this->formSign($this->request);
        $this->formRequest($this->request);
        $apiServiceStub = $this->createMock(ApiService::class);
        $apiServiceStub->method('getRequestParams')->willReturn($this->request);
        $serviceApiClientVerifyPhone = new ServiceAPIClientVerifyPhone();
        if (0 === self::$counter) {
            $result = $serviceApiClientVerifyPhone->run($apiServiceStub);
            $this->assertEquals(1, $result['status']);
            self::$counter++;
        } elseif (1 === self::$counter) {
            $this->expectExceptionCode(60201);
            $serviceApiClientVerifyPhone->run($apiServiceStub);
        }

    }

    /**☺
     * @return array Request body
     */
    public function provideRequest(): array
    {
        return [
            //positive data
            ['{"method":"client_verify_phone","data":{"phone":"'.$_ENV['PHONE_NUMBER'].'"},"login":"test","sign":"","datetime":""}'],
            //invalid phone number
            ['{"method":"client_verify_phone","data":{"phone":"'.$_ENV['PHONE_NUMBER'].'00"},"login":"test","sign":"","datetime":""}']
        ];
    }

    private function formSign($data)
    {
        $date = date("Y-m-d h:m:s");
        $json = json_decode($data);
        $json->datetime = $date;
        $str = json_encode($json);
        $str = $str . self::API_KEY;
        $sign = bin2hex(sha1($str));
        $this->sign = $sign;
    }

    private function formRequest($data)
    {
        $json = json_decode($data);
        $json->sign = $this->sign;
        $this->request = $json;
    }
}