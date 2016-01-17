<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 07.01.16
 * Time: 23:20
 */

namespace ApiBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

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

        if (isset($testData["fixtures"])) {
            $this->loadFixtures($testData["fixtures"]);
            unset($testData["fixtures"]);
        }

        foreach ($testData as $caseName => $data) {
            $request = isset($data['request']) ? $data['request'] : [];
            $setSession = isset($data['set_session']) ? $data['set_session'] : [];

            foreach ($setSession as $name => $value) {
                $client->getContainer()->get("session")->set($name, $value);
            }

            $client->request($data['method'], '/' . $uri, $request);
            $expectedResponse = $data['response'];
            $errorMessage = "Failed $caseName.\nExpected response: " . json_encode($expectedResponse) . ".\nActual response: {$client->getResponse()->getContent()}.";
            $this->assertEquals(
                $expectedResponse['status'],
                $client->getResponse()->getStatusCode(),
                $errorMessage
            );

            $actualResponse = json_decode($client->getResponse()->getContent(), true);

            if (isset($expectedResponse['errors'])) {
                $this->assertArrayHasKey("errors", $actualResponse, $errorMessage);

                $this->assertEmpty(array_diff_assoc($expectedResponse['errors'],
                    $actualResponse['errors']),
                    $errorMessage);
                $this->assertEmpty(array_diff_assoc($actualResponse['errors'],
                    $expectedResponse['errors']),
                    $errorMessage);
            }

            if (isset($expectedResponse['data'])) {
                $this->assertArrayHasKey("data", $actualResponse, $errorMessage);

                $expectedData = $expectedResponse['data'];
                $actualData = $actualResponse['data'];

                $this->assertActualContainsExpected($actualData, $expectedData, $errorMessage);
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

    /**
     * @param array $actualData
     * @param array $expectedData
     * @param string $errorMessage
     */
    private function assertActualContainsExpected(array $actualData, array $expectedData, $errorMessage)
    {
        $multiDimensional = false;

        foreach ($expectedData as $key => $expectedChunk) {
            $this->assertArrayHasKey($key, $actualData, $errorMessage . PHP_EOL . json_encode($actualData));
            if (is_array(($expectedChunk))) {
                $this->assertActualContainsExpected($actualData[$key], $expectedChunk, $errorMessage);
                $multiDimensional = true;
            }
        }

        if ($multiDimensional) {
            return;
        }

        $unFoundArray = array_diff_assoc($expectedData, $actualData);
        $this->assertEmpty($unFoundArray, $errorMessage . "\n" . print_r($unFoundArray, true));
    }
}