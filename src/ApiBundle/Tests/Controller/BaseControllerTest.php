<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 07.01.16
 * Time: 23:20
 */

namespace ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;;

class BaseControllerTest extends WebTestCase
{
    /**
     * @param string $uri
     */
    protected function testFromJson($uri)
    {
        $client = static::createClient();
        $action = str_replace('/', '.', $uri);

        $directoryName = $client->getContainer()->get('kernel')->getRootDir() . '/../tests/ApiBundle/Controller/test_cases/';
        $testData = json_decode(
            file_get_contents($directoryName . $action . '.json'),
            true
        );

        foreach ($testData as $caseName => $data) {
            $request = isset($data['request']) ? $data['request'] : [];
            $setSession = isset($data['set_session']) ? $data['set_session'] : [];

            foreach ($setSession as $name => $value) {
                $client->getContainer()->get("session")->set($name, $value);
            }

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
                $expectedData = $expectedResponse['data'];
                $actualData = $actualResponse['data'];

                if (key($expectedData) !== 0) {
                    $expectedData = [$expectedData];
                    $actualData = [$actualData];
                }

                foreach ($expectedData as $key => $expectedChunk) {
                    $this->assertEmpty(array_diff_assoc($expectedData[$key], $actualData[$key]), $errorMessage);
                    $this->assertEmpty(array_diff_assoc($actualData[$key], $expectedData[$key]), $errorMessage);
                }
            }

            if (isset($data['session'])) {
                foreach ($data['session'] as $name => $expectedValue) {
                    if (is_array($expectedValue)) {
                        $actualValue = (array)$client->getContainer()->get('session')->get($name);
                        $this->assertEmpty(array_diff_assoc($expectedValue, $actualValue),
                            'session is not correct');
                        $this->assertEmpty(array_diff_assoc($actualValue, $expectedValue),
                            'session is not correct');
                    } else {
                        $this->assertEquals($expectedValue,
                            $client->getContainer()->get('session')->get($name));
                    }
                }
            }
        }
    }
}