<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Site;
use AppBundle\Form\SiteType;
use AppBundle\Form\PropertyType;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Site controller.
 *
 * @Route("/sites")
 */
class SiteController extends Controller
{

    /**
     * Lists all Site entities.
     *
     * @Route("/", name="sites")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Site')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Site entity.
     *
     * @Route("/", name="sites_create")
     * @Method("POST")
     * @Template("AppBundle:Site:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Site();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->addFlash('success', sprintf('Created %s', $entity->getName()));
            return $this->redirect($this->generateUrl('sites_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Site entity.
     *
     * @param Site $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Site $entity)
    {
        $form = $this->createForm(new SiteType(), $entity, array(
            'action' => $this->generateUrl('sites_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Create Site',
            'attr' => array('class' => 'btn btn-default')
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Site entity.
     *
     * @Route("/new", name="sites_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Site();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Site entity.
     *
     * @Route("/{id}", name="sites_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Site')->findAllJoinsById($id);

        if (!$entity) {
            $this->addFlash('danger', sprintf('Site %d not found.', $id));
            return $this->redirect($this->generateUrl('sites'));
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Site entity.
     *
     * @Route("/{id}/edit", name="sites_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Site')->find($id);

        if (!$entity) {
            $this->addFlash('danger', sprintf('Site %d not found.', $id));
            return $this->redirect($this->generateUrl('sites'));
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Site entity.
    *
    * @param Site $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Site $entity)
    {
        $form = $this->createForm(new SiteType(), $entity, array(
            'action' => $this->generateUrl('sites_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $properties_form = $form->get('properties');
        $properties_form->remove('site');
        $properties_form->remove('images');

        $form->add('submit', 'submit', array(
                    'label' => 'Update Site',
                    'attr' => array('class' => 'btn btn-default')));

        return $form;
    }
    /**
     * Edits an existing Site entity.
     *
     * @Route("/{id}", name="sites_update")
     * @Method("PUT")
     * @Template("AppBundle:Site:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Site')->find($id);

        if (!$entity) {
            $this->addFlash('danger', sprintf('Site %d not found.', $id));
            return $this->redirect($this->generateUrl('sites'));
        }

        // Get the properties originally associated with this site.
        $originalProperties = new ArrayCollection();
        foreach($entity->getProperties() as $property){
            $originalProperties->add($property);
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            foreach($originalProperties as $property){
                if(false === $entity->getProperties()->contains($property)){
                    foreach($property->getImages() as $image){
                        $em->remove($image);
                    }
                    $em->remove($property);
                }
            }

            $em->persist($entity);
            $em->flush();

            $this->addFlash('success', sprintf('Updated %s', $entity->getName()));
            return $this->redirect($this->generateUrl('sites_show', array('id' => $entity->getId())));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Site entity.
     *
     * @Route("/{id}/delete", name="sites_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, $id)
    {
        if ($this->isCsrfTokenValid('delete_site_intention')) {

            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Site')->find($id);

            if (!$entity) {
                $this->addFlash('danger', sprintf('Site %d not found.', $id));
                return $this->redirect($this->generateUrl('sites'));
            }
            foreach($entity->getProperties() as $property){
                $em->remove($property);
            }
            $em->remove($entity);
            $em->flush();

            $this->addFlash('success', sprintf('Deleted %s', $entity->getName()));
            return $this->redirect($this->generateUrl('sites'));
        } else {
            $this->addFlash('danger', 'Invalid delete action.');
            return $this->redirect($this->generateUrl('sites'));
        }
    }

    /**
     * Creates a form to delete a Site entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sites_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array(
                'label' => 'Delete Site',
                'attr' => array('class' => 'btn btn-danger')
            ))
            ->getForm()
        ;
    }

    public function isCsrfTokenValid($intention, $token_name = '_token')
    {
        return $this->get('form.csrf_provider')
            ->isCsrfTokenValid($intention, $this->getRequest()->get($token_name));
    }
}