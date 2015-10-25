<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');
        $this->evaluateIndex($client, $crawler);
        $crawler = $client->request('GET', '/browse');
        $this->evaluateIndex($client, $crawler);
    }

    private function evaluateIndex($client, $crawler){
    	//Page returns
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        //Navbar is rendered
        $this->assertContains('Browse All Sites', $crawler->filter('li.active a')->text());

        //Letter navigation defaults to All
        $this->assertContains('All', $crawler->filter('.letter-navigation a.active')->text());

        //More than one Site is displayed in the accordion
        $this->assertGreaterThan(0, $crawler->filter('#accordion .panel')->count());
    }
}
