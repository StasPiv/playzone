<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 07.01.16
 * Time: 23:20
 */

namespace ApiBundle\Tests\Controller;

use ApiBundle\Exception\Tests\Controller\BaseControllerTestException;
use Doctrine\Common\DataFixtures\Executor\AbstractExecutor;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class BaseControllerTest
 * @package ApiBundle\Tests\Controller
 *
 * @method AbstractExecutor loadFixtures(array $fixtures)
 */
abstract class BaseControllerTest extends WebTestCase
{
    protected $fixtures;

    /**
     * @param string $baseUri
     * @throws \Exception
     */
    protected function assertFromJson($baseUri)
    {
        $client = static::createClient();
        $action = preg_replace('/\/\{\w+\}/', '', $baseUri);
        $action = str_replace('/', '.', $action);

        $directoryName = $client->getContainer()->get('kernel')->getRootDir() . '/../tests/ApiBundle/Controller/test_cases/';

        $fullPathToTestFile = $directoryName . $action . '.json';
        if (!file_exists($fullPathToTestFile)) {
            throw new BaseControllerTestException("File $fullPathToTestFile is required for this test");
        }

        $testData = json_decode(
            file_get_contents($fullPathToTestFile),
            true
        );

        if (isset($testData["fixtures"])) {
            $this->fixtures = $this->loadFixtures($testData["fixtures"])->getReferenceRepository();
            unset($testData["fixtures"]);
        }

        if (empty($testData)) {
            throw new BaseControllerTestException("There are not test cases in $fullPathToTestFile");
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

                $requestUri = str_replace('{' . $name . '}', $value, $requestUri, $count);
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

            if (intval($expectedResponse['status'] / 100) == 4) { // error responses
                $this->assertEmpty(array_diff_assoc($expectedResponse['errors'],
                    $actualResponse),
                    $errorMessage);
                $this->assertEmpty(array_diff_assoc($actualResponse,
                    $expectedResponse['errors']),
                    $errorMessage);
            }

            if (intval($expectedResponse['status'] / 100) == 2) { // successful responses
                $this->assertNotEmpty($actualResponse, $errorMessage);
                try {
                    $this->assertActualContainsExpected($actualResponse, $expectedResponse['data'], $errorMessage);
                } catch (\Exception $e) {
                    file_put_contents(__DIR__ . '/../../../../var/logs/last_fail_actual_response.json',
                        $client->getResponse()->getContent());
                    throw $e;
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
            } else {
                $this->assertEquals($expectedChunk, $actualData[$key], $errorMessage . PHP_EOL . "`$key` is not correct");
            }
        }

        if ($multiDimensional) {
            return;
        }

        $unFoundArray = array_diff_assoc($expectedData, $actualData);
        $this->assertEmpty($unFoundArray, $errorMessage . "\n" . print_r($unFoundArray, true));
    }
}