<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 07.01.16
 * Time: 23:20
 */

namespace ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;;

class BaseControllerTest extends WebTestCase
{
    /**
     * @param string $uri
     * @param Client $client
     */
    protected function testFromJson($uri, Client $client)
    {
        $action = str_replace('/', '.', $uri);

        $directoryName = $client->getContainer()->get('kernel')->getRootDir() . '/../tests/ApiBundle/Controller/test_cases/';
        $testData = json_decode(
            file_get_contents($directoryName . $action . '.json'),
            true
        );

        foreach ($testData as $caseName => $data) {
            $request = isset($data['request']) ? $data['request'] : [];

            $client->request($data['method'], '/'.$uri, $request);
            $expectedResponse = $data['response'];
            $errorMessage = "Failed $caseName.\nExpected response: " . json_encode($expectedResponse) . ".\nActual response: {$client->getResponse()->getContent()}.";
            $this->assertEquals(
                $expectedResponse['status'],
                $client->getResponse()->getStatusCode(),
                $errorMessage
            );

            $actualResponse = json_decode($client->getResponse()->getContent(), true);

            if (isset($expectedResponse['errors'])) {
                $this->assertEmpty(array_diff_assoc($expectedResponse['errors'],
                    $actualResponse['errors']),
                    $errorMessage);
                $this->assertEmpty(array_diff_assoc($actualResponse['errors'],
                    $expectedResponse['errors']),
                    $errorMessage);
            }

            if (isset($expectedResponse['data'])) {
                $this->assertEmpty(array_diff_assoc($expectedResponse['data'],
                    $actualResponse['data']), $errorMessage);
                $this->assertEmpty(array_diff_assoc($actualResponse['data'],
                    $expectedResponse['data']), $errorMessage);
            }

            if (isset($data['session'])) {
                foreach ($data['session'] as $key => $expectedValue) {
                    if (is_array($expectedValue)) {
                        $actualValue = (array)$client->getContainer()->get('session')->get($key);
                        $this->assertEmpty(array_diff_assoc($expectedValue, $actualValue),
                            'session is not correct');
                        $this->assertEmpty(array_diff_assoc($actualValue, $expectedValue),
                            'session is not correct');
                    } else {
                        $this->assertEquals($expectedValue,
                            $client->getContainer()->get('session')->get($key));
                    }
                }
            }
        }
    }
}