<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PropertyControllerTest extends WebTestCase
{
    public function testIndexAndCreate()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/properties/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /properties/");
        $crawler = $client->click($crawler->selectLink('Add a new Property')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form(array(
            'appbundle_property[name]'  => 'Test Foo',
            'appbundle_property[address]'  => 'Test Bar',
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('p.form-control-static:contains("Test Foo")')->count(), 'Missing element p.form-control-static:contains("Test Foo")');
    }


    public function testEdit()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        $crawler = $client->request('GET', '/properties/');
        $crawler = $client->click($crawler->selectLink('edit')->last()->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Update')->form(array(
            'appbundle_property[name]'  => 'Test Foo',
            'appbundle_property[address]'  => 'Test Bar',
        ));

        // Create a new entry in the database
        $crawler = $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testDelete()
    {
        // Create a new client to browse the application
        $client = static::createClient();
        $crawler = $client->request('GET', '/properties/');
        $crawler = $client->click($crawler->selectLink('Test Foo')->last()->link());
        
        $href = $crawler->filter('#delete_button')->attr('data-href');
        var_dump($href);
        $crawler = $client->request('GET', $href);
        $this->assertEquals(302, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET " . $href);
    }
}