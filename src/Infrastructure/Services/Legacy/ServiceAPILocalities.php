<?php


namespace App\Infrastructure\Services\Legacy;


use App\Domain\DTO\ServiceApiResponseResultDTO;
use App\Infrastructure\Entity\ServiceapiCity;
use App\Infrastructure\Entity\ServiceapiRegion;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use App\Infrastructure\Services\Api\ApiService;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class ServiceAPILocalities
{
    const BASE_URL = 'http://openapi.justin.ua/localities/';

    /**
     * @var Client
     * @since 1.0
     * */
    protected $client;

    /**
     * @var Connection
     * @since 1.0
     * */
    protected $connection;

    /**
     * List error messages
     *
     * @since 1.0
     * */
    private $errors = [];

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->connection = $entityManager->getConnection();
        $this->client = new Client(['base_uri' => self::BASE_URL]);
    }

    /**
     * @param ApiService $apiService
     * @return ServiceApiResponseResultDTO
     * @throws \Exception
     * @since 1.0
     */
    public function get(ApiService $apiService)
    {
        $query = (array)$apiService->getRequestParams();

        if (isset($query['filter'])) {
            $path = $query['filter'];

            $this->validate($path);

            $result = $this->getFromOpenApi($path);
        } else {
            $result = $this->getFromOpenApi();
        }

        return new ServiceApiResponseResultDTO($result);
    }

    /**
     * Validate method
     *
     * @param  $data
     * @throws \Exception
     */
    public function validate($data)
    {
        $validator = Validation::createValidator();

        // Validate number
        $simpleConstraints = [
            new Assert\NotBlank(),
            new Assert\Type(['type' => 'string']),
            new Assert\Choice(['all', 'activity'])
        ];

        $this->errors[] = (string)$validator->validate(
            $data,
            $simpleConstraints
        );

        foreach ($this->errors as $error) {
            if (empty($error))
                continue;
            throw new \Exception('Недопустимый параметр++Неприпустимий параметр++Invalid parameter', 60430);
        }
    }

    /**
     * @param string $method
     * @return array|string
     * @throws \Exception
     * @since 1.0
     */
    protected function getFromOpenApi($method = 'all')
    {
        try {
            $response = $this->client->request('GET', $method);
            $cod = $response->getStatusCode();
        } catch (\Exception $ex) {
            throw new \Exception('Произошел системный сбой платформы. Обратитесь в службу поддержки++Стався системний збій платформи. Зверніться в службу підтримки', 60020);
        }

        if ($response instanceof ResponseInterface && 200 == $cod) {
            $body = $response->getBody();
            $content = $body->getContents();
            $result = $this->isJson($content) ? json_decode($content) : $content;
            return $result->result;
        } else {
            throw new \Exception('Произошел системный сбой платформы. Обратитесь в службу поддержки++Стався системний збій платформи. Зверніться в службу підтримки', 60020);
        }
    }

    /**
     * @param string $filter
     * @return array|null
     * @throws \Doctrine\DBAL\DBALException
     * @since 1.0
     */
    protected function getFromDB($filter = 'all')
    {
        return $this->entityManager
            ->getRepository(ServiceapiCity::class)
            ->get($filter);
    }

    /**
     * @param $string
     * @return bool
     * @since 1.0
     */
    function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}