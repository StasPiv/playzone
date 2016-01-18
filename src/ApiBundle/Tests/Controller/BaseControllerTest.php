<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 07.01.16
 * Time: 23:20
 */

namespace ApiBundle\Tests\Controller;

use Doctrine\Common\DataFixtures\Executor\AbstractExecutor;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class BaseControllerTest
 * @package ApiBundle\Tests\Controller
 *
 * @method AbstractExecutor loadFixtures(array $fixtures)
 */
class BaseControllerTest extends WebTestCase
{
    protected $fixtures;

    /**
     * @param string $baseUri
     */
    protected function testFromJson($baseUri)
    {
        $client = static::createClient();
        $action = preg_replace('/\/\{\w+\}/', '', $baseUri);
        $action = str_replace('/', '.', $action);

        $directoryName = $client->getContainer()->get('kernel')->getRootDir() . '/../tests/ApiBundle/Controller/test_cases/';
        $testData = json_decode(
            file_get_contents($directoryName . $action . '.json'),
            true
        );

        if (isset($testData["fixtures"])) {
            $this->fixtures = $this->loadFixtures($testData["fixtures"])->getReferenceRepository();
            unset($testData["fixtures"]);
        }

        foreach ($testData as $caseName => $data) {
            $request = isset($data['request']) ? $data['request'] : [];

            if (isset($request["reference"])) {
                $reference = $request["reference"];
                $request[$reference["name"]] = $this->fixtures->getReference($reference["value"])->getId();
                unset($request["reference"]);
            }

            $setSession = isset($data['set_session']) ? $data['set_session'] : [];

            foreach ($setSession as $name => $value) {
                $client->getContainer()->get("session")->set($name, $value);
            }

            $requestUri = $baseUri;

            foreach ($request as $name => $value) {
                if (is_array($value)) {
                    continue;
                }

                $requestUri = str_replace('{' . $name . '}', $value, $baseUri, $count);
                if ($count) {
                    unset($request[$name]);
                }
            }

            $client->request($data['method'], '/' . $requestUri, $request);
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
            $this->assertArrayHasKey($key, $actualData, $errorMessage);
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