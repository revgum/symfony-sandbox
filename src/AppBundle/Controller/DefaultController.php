<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Site;

/**
 * Main controller.
 *
 * @Route("/")
 */
class DefaultController extends Controller
{
    /**
     * Browse sites, properties, images
     *
     * @Route("/", name="homepage")
     * @Route("/browse/{letter}", name="browse", defaults={"letter" = "ALL"})
     * @Route("/browse/", name="browse_base")
     * @Method("GET")
     * @Template()
     */
    public function indexAction($letter = 'ALL', Request $request)
    {
        $page = $request->query->get('page', 1);
        $per_page = $request->query->get('per_page', 25);

        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('AppBundle:Site')->findAllStartsWith($letter, $page, $per_page);

        $total = count($sites);
        $pages_count = ceil($total / $per_page);
        
        if($page > $pages_count && $pages_count > 0){
            $starting_with = 'All Sites.';
            if($letter != 'ALL'){
                $starting_with = sprintf('Sites starting with %s.', $letter);
            }
            $this->addFlash('info', 
                sprintf('Page %d not found, redirecting to page 1 for %s', $page, $starting_with)
            );
            return $this->redirectToRoute('browse',
                array(
                    'letter' => $letter, 
                    'per_page' => $per_page, 
                    'page' => 1), 
                302
            );
        }

        return array(
            'sites' => $sites,
            'letter' => $letter,
            'page' => $page,
            'per_page' => $per_page,
            'total' => $total,
            'pages_count' => $pages_count
        );
    }
}