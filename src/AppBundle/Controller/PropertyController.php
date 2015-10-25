<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Property;
use AppBundle\Form\ImageType;
use AppBundle\Form\PropertyType;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Property controller.
 *
 * @Route("/properties")
 */
class PropertyController extends Controller
{

    /**
     * Lists all Property entities.
     *
     * @Route("/", name="properties")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Property')->findAllJoins();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Property entity.
     *
     * @Route("/", name="properties_create")
     * @Method("POST")
     * @Template("AppBundle:Property:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Property();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->addFlash('success', sprintf('Created %s', $entity->getName()));
            return $this->redirect($this->generateUrl('properties_show', array('id' => $entity->getId())));
        } else {
            foreach($form->getErrors(true) as $error){
                $this->addFlash('danger', $error->getMessage());
            }
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Property entity.
     *
     * @param Property $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Property $entity)
    {
        $form = $this->createForm(new PropertyType(), $entity, array(
            'action' => $this->generateUrl('properties_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Create',
            'attr' => array('class' => 'btn btn-default')
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Property entity.
     *
     * @Route("/new", name="properties_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Property();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Property entity.
     *
     * @Route("/{id}", name="properties_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Property')->getByIdJoins($id);

        VarDumper::dump($entity);

        if (!$entity) {
            $this->addFlash('danger', sprintf('Property %d not found.', $id));
            return $this->redirect($this->generateUrl('properties'));
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Property entity.
     *
     * @Route("/{id}/edit", name="properties_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Property')->getByIdJoins($id);

        if (!$entity) {
            $this->addFlash('danger', sprintf('Property %d not found.', $id));
            return $this->redirect($this->generateUrl('properties'));
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
    * Creates a form to edit a Property entity.
    *
    * @param Property $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Property $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $choices = $em->getRepository('AppBundle:Site')->findAll();

        $form = $this->createForm(new PropertyType($choices), $entity, array(
            'action' => $this->generateUrl('properties_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array(
                    'label' => 'Update',
                    'attr' => array('class' => 'btn btn-default')));

        return $form;
    }
    /**
     * Edits an existing Property entity.
     *
     * @Route("/{id}", name="properties_update")
     * @Method("PUT")
     * @Template("AppBundle:Property:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Property')->getByIdJoins($id);

        if (!$entity) {
            $this->addFlash('danger', sprintf('Property %d not found.', $id));
            return $this->redirect($this->generateUrl('properties'));
        }

        // Get the images originally associated with this property.
        $originalImages = new ArrayCollection();
        foreach($entity->getImages() as $image){
            $originalImages->add($image);
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            foreach($originalImages as $image){
                if(false === $entity->getImages()->contains($image)){
                    $em->remove($image);
                }
            }

            $em->persist($entity);
            $em->flush();

            $this->addFlash('success', sprintf('Updated %s', $entity->getName()));
            return $this->redirect($this->generateUrl('properties_show', array('id' => $entity->getId())));
        } else {
            foreach($editForm->getErrors(true) as $error){
                $this->addFlash('danger', $error->getMessage());
            }
            return $this->redirect($this->generateUrl('properties_edit', array('id' => $entity->getId())));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Property entity.
     *
     * @Route("/{id}/delete", name="properties_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, $id)
    {
        if ($this->isCsrfTokenValid('delete_property_intention')) {
            
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Property')->getByIdJoins($id);

            if (!$entity) {
                $this->addFlash('danger', sprintf('Property %d not found.', $id));
                return $this->redirect($this->generateUrl('properties'));
            }
            foreach($entity->getImages() as $image){
                $em->remove($image);
            }
            $em->remove($entity);
            $em->flush();

            $this->addFlash('success', sprintf('Deleted %s', $entity->getName()));
            return $this->redirect($this->generateUrl('properties'));
        } else {
            $this->addFlash('danger', 'Invalid delete action.');
            return $this->redirect($this->generateUrl('properties'));
        }
    }

    /**
     * Creates a form to delete a Property entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('properties_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array(
                'label' => 'Delete',
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