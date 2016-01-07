<?php
/**
 * Created by PhpStorm.
 * User: stas
 * Date: 06.01.16
 * Time: 21:44
 */

namespace ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;;

class UserControllerTest extends WebTestCase
{
    public function testRegister()
    {
        $client = static::createClient();

        $testData = json_decode(
                file_get_contents(__DIR__ . '/test_cases/UserControllerTest::testRegister.json'),
                true
            );

        foreach ($testData as $caseName => $data) {
            $request = $data['request'];

            $client->request($data['method'],'/user/register', $request);
            $expectedResponse = $data['response'];
            $errorMessage = "Failed $caseName.\nExpected response: " . json_encode($expectedResponse) . ".\nActual response: {$client->getResponse()->getContent()}.";
            $this->assertEquals(
                $expectedResponse['status'],
                $client->getResponse()->getStatusCode(),
                $errorMessage
            );

            $actualResponse = json_decode($client->getResponse()->getContent(), true);

            if (isset($expectedResponse['errors'])) {
                $this->assertEmpty(array_diff_assoc($expectedResponse['errors'], $actualResponse['errors']), $errorMessage);
                $this->assertEmpty(array_diff_assoc($actualResponse['errors'], $expectedResponse['errors']), $errorMessage);
            }

            if (isset($expectedResponse['data'])) {
                $this->assertEmpty(array_diff_assoc($expectedResponse['data'], $actualResponse['data']), $errorMessage);
                $this->assertEmpty(array_diff_assoc($actualResponse['data'], $expectedResponse['data']), $errorMessage);
            }
        }

    }
}