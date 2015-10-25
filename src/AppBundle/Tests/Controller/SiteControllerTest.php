<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SiteControllerTest extends WebTestCase
{
    public function testIndexAndCreate()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/sites/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /sites/");
        $crawler = $client->click($crawler->selectLink('Add a new Site')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create Site')->form(array(
            'appbundle_site[name]'  => 'Test Foo',
            'appbundle_site[description]'  => 'Test Bar',
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

        // Create a new entry in the database
        $crawler = $client->request('GET', '/sites/1/edit');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /sites/1/edit");
    }

    public function testDelete()
    {
        // Create a new client to browse the application
        $client = static::createClient();
        $crawler = $client->request('GET', '/sites/52/edit');

        $csrfToken = $client->getContainer()->get('form.csrf_provider')->generateCsrfToken('delete_site_intention');
    
        $crawler = $client->request('GET', '/sites/52/delete', array(
            'appbundle_site' => array(
                '_token' => $csrfToken,
            ),
        ));
        $this->assertEquals(302, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /sites/52/delete");
    }
}