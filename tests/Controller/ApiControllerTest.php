<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ApiControllerTest
 * @package App\Tests\Controller
 * @group api
 */
class ApiControllerTest extends WebTestCase
{
    public function testGetCard()
    {
        $client = static::createClient();
        $client->request('GET', '/api/public/card/01001');
        $response = $client->getResponse();
        $json = $response->getContent();
        $this->assertJson($json);
        $data = json_decode($json, true);
        $this->assertNotNull($data);
        $this->assertInternalType('array', $data);
        $this->assertNotEmpty($data);
        $this->assertArrayHasKey("code", $data);
        $this->assertEquals("01001", $data['code']);
    }

    public function testListCards()
    {
        $client = static::createClient();
        $client->request('GET', '/api/public/cards/');
        $response = $client->getResponse();
        $json = $response->getContent();
        $this->assertJson($json);
        $data = json_decode($json, true);
        $this->assertNotNull($data);
        $this->assertInternalType('array', $data);
        $this->assertNotEmpty($data);
    }

    public function testListCardsByPack()
    {
        $client = static::createClient();
        $client->request('GET', '/api/public/cards/Core');
        $response = $client->getResponse();
        $json = $response->getContent();
        $this->assertJson($json);
        $data = json_decode($json, true);
        $this->assertNotNull($data);
        $this->assertInternalType('array', $data);
        $this->assertNotEmpty($data);
        foreach ($data as $item) {
            $this->assertInternalType('array', $item);
            $this->assertArrayHasKey("pack_code", $item);
            $this->assertEquals("Core", $item['pack_code']);
        }
    }

    public function testListPacks()
    {
        $client = static::createClient();
        $client->request('GET', '/api/public/packs/');
        $response = $client->getResponse();
        $json = $response->getContent();
        $this->assertJson($json);
        $data = json_decode($json, true);
        $this->assertNotNull($data);
        $this->assertInternalType('array', $data);
        $this->assertNotEmpty($data);
    }

    public function testGetDecklist()
    {
        $client = static::createClient();
        $client->request('GET', '/api/public/decklist/1');
        $response = $client->getResponse();
        $json = $response->getContent();
        $this->assertJson($json);
        $data = json_decode($json, true);
        $this->assertNotNull($data);
        $this->assertInternalType('array', $data);
        $this->assertArrayHasKey("id", $data);
        $this->assertEquals(1, $data['id']);
    }
}
