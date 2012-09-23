<?php

namespace Fly\FlydbBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use Fly\FlydbBundle\Entity\Flyline;
use Fly\FlydbBundle\Form\FlylineType;

/**
 * Flyline controller.
 *
 */
class FlylineController extends Controller
{
    public function careAction(Request $request, $id)
    {
        $securityContext = $this->get('security.context');
       
        $form = $this->createCareForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $flyline = $em->getRepository('FlyFlydbBundle:Flyline')->find($id);

            if (!$flyline) {
                throw $this->createNotFoundException('Unable to find specified fly line.');
            }
            
            if (false === $securityContext->isGranted('EDIT', $flyline))
            {
                throw new AccessDeniedException();
            }
            
            $flyline->setCared(new \DateTime());
            $em->persist($flyline);
            $em->flush();
        }
        
        return $this->redirect($this->getRequest()->headers->get('referer'));
        //return $this->redirect($this->generateUrl('flyline_show', array('id' => $id)));
    }
    
    private function createCareForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }    
    
    /**
     * Lists all Flyline entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('FlyFlydbBundle:Flyline')->findAll();

        return $this->render('FlyFlydbBundle:Flyline:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Lists all Flyline entities for management.
     *
     */
    public function manageAction()
    {
        $securityContext = $this->get('security.context');
        $user = $securityContext->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        
        if ($securityContext->isGranted('ROLE_ADMIN'))
        {
            $entities = $em->getRepository('FlyFlydbBundle:Flyline')->findAll();
        } else {
            $entities = $em->getRepository('FlyFlydbBundle:Flyline')->findByOwner($user);
        }
        
        return $this->render('FlyFlydbBundle:Flyline:manage.html.twig', array(
            'entities' => $entities,
        ));
    }
    
    public function careMultipleAction(Request $request)
    {
        $securityContext = $this->get('security.context');
        $flyline_ids = $request->request->get('flylines');
        
        $em = $this->getDoctrine()->getManager();
        
        foreach ( $flyline_ids as $id )
        {
            $flyline = $em->getRepository('FlyFlydbBundle:Flyline')->find($id);
            
            if ( $flyline && (true === $securityContext->isGranted('EDIT', $flyline)) ) 
            {
                $flyline->setCared(new \DateTime());
                $em->persist($flyline);
                $em->flush();
            }
        }
        
        return $this->redirect($this->getRequest()->headers->get('referer'));

        //return $this->render('FlyFlydbBundle:Flyline:test.html.twig', array(
            //'testout' => $flyline_ids,
        //));
    }

    /**
     * Finds and displays a Flyline entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlyFlydbBundle:Flyline')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find specified fly line.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $careForm = $this->createCareForm($id);

        return $this->render('FlyFlydbBundle:Flyline:show.html.twig', array(
            'entity'      => $entity,
            'care_form'   => $careForm->createView(),
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new Flyline entity.
     *
     */
    public function newAction()
    {
        $entity = new Flyline();
        $form   = $this->createForm(new FlylineType(), $entity);

        return $this->render('FlyFlydbBundle:Flyline:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Flyline entity.
     *
     */
    public function createAction(Request $request)
    {
        $securityContext = $this->get('security.context');
        $user = $securityContext->getToken()->getUser();
        
        $entity  = new Flyline();
        $entity->setOwner($user);
        
        $form = $this->createForm(new FlylineType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            
            // creating the ACL
            $aclProvider = $this->get('security.acl.provider');
            $objectIdentity = ObjectIdentity::fromDomainObject($entity);
            $acl = $aclProvider->createAcl($objectIdentity);
            
            // retrieving the security identity of the currently logged-in user
            $securityIdentity = UserSecurityIdentity::fromAccount($user);

            // grant owner access
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
            
            // grant admin access
            $roleSecurityIdentity = new RoleSecurityIdentity('ROLE_ADMIN');
            $acl->insertObjectAce($roleSecurityIdentity, MaskBuilder::MASK_MASTER);
            
            $aclProvider->updateAcl($acl);
            
            return $this->redirect($this->generateUrl('flymanage_show', array('id' => $entity->getId())));
        }

        return $this->render('FlyFlydbBundle:Flyline:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Flyline entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlyFlydbBundle:Flyline')->find($id);
        
        $securityContext = $this->get('security.context');

        // check for edit access
        if (false === $securityContext->isGranted('EDIT', $entity))
        {
            throw new AccessDeniedException();
        }
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Flyline entity.');
        }

        $editForm = $this->createForm(new FlylineType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FlyFlydbBundle:Flyline:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Flyline entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlyFlydbBundle:Flyline')->find($id);
        
        $securityContext = $this->get('security.context');
        // check for edit access
        if (false === $securityContext->isGranted('EDIT', $entity))
        {
            throw new AccessDeniedException();
        }
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Flyline entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new FlylineType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('flyline_edit', array('id' => $id)));
        }

        return $this->render('FlyFlydbBundle:Flyline:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Flyline entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $securityContext = $this->get('security.context');

        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FlyFlydbBundle:Flyline')->find($id);
            
            // check for delete access
            if (false === $securityContext->isGranted('DELETE', $entity))
            {
                throw new AccessDeniedException();
            }

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Flyline entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('flymanage'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
