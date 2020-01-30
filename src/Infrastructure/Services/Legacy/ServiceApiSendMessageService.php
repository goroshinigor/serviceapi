<?php

namespace App\Infrastructure\Services\Legacy;

use App\Domain\DTO\ServiceApiResponseDTO;
use App\Infrastructure\Services\Api\ApiService;
use App\Infrastructure\Services\Provider\SmsProvider;
use App\Infrastructure\Services\Provider\SMSProvider\SmsProviderParameters;
use App\Infrastructure\Services\Validation\ServiceValidatePhone;

class ServiceApiSendMessageService
{

    private $translit;

    private $validatePhoneService;

    private $smsProvider;

    public function __construct(ServiceValidatePhone $validatePhoneService, SmsProvider $smsProvider)
    {
        $this->validatePhoneService = $validatePhoneService;
        $this->smsProvider = $smsProvider;
    }

    public function run(ApiService $apiService)
    {
        $data = (array)$apiService->getRequestParams();
        if ($this->validation($data)) {
            $this->smsProvider = $this->smsProvider->getProvider();
            if (1 == $this->translit) $this->convertToTranslit($data['data']->text);
            $providerParameters = new SmsProviderParameters($data['data']->phone, $data['data']->text);
            $response = (object)$this->smsProvider->send(1, $providerParameters);
            if (isset($response->resultCode) && $response->resultCode == 1) return true;
            return false;
        }
    }

    private function validation($data)
    {
        if (isset($data['data']->phone)) {
            $this->validatePhoneService->validate($data['data']->phone);
        } else throw new \Exception('Не указан data.phone++Не вказаний data.phone++data.phone not specified', 00000);
        if (empty($data['data']->text)) {
            throw  new \Exception('Не указан текст сообщения++Не вказаний текст повідомлення++Message text not specified', 60302);
        }
        if (isset($data['data']->convert_to_translit) && is_numeric($val = $data['data']->convert_to_translit)) {
            $this->translit = $val;
        } else {
            throw new \Exception('Не верно указан data.convert_to_translit++Не вірно вказаний data.convert_to_translit++data.convert_to_translit is wrong', 00000);
        }
        return true;
    }

    /**
     * @param $text string
     * @return string
     */
    private function convertToTranslit(&$text)
    {
        $alf = [
            'а' => 'a', 'б' => 'b', 'в' => 'v',
            'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
            'и' => 'i', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
            'ь' => '', 'ы' => 'y', 'ъ' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
            'і' => 'i', 'ї' => 'i', 'ґ' => 'g',
            'є' => 'ie',

            'А' => 'A', 'Б' => 'B', 'В' => 'V',
            'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
            'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
            'И' => 'I', 'Й' => 'Y', 'К' => 'K',
            'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R',
            'С' => 'S', 'Т' => 'T', 'У' => 'U',
            'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
            'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
            'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
            'Є' => 'Ye',
        ];
        $text = strtr($text, $alf);
        return;
    }
}