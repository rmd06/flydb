<?php

namespace Fly\FlydbBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\ArrayAdapter;

use Elastica_Searchable;
use Elastica_Query;

use Fly\FlydbBundle\Entity\Flyline;
use Fly\FlydbBundle\Form\FlylineType;

/**
 * Flyline controller.
 *
 */
class FlylineController extends Controller
{
    /**
     * Lists all Flyline entities.
     */
    public function indexAction($page=null, $maxPerPage=10)
    {
        $em = $this->getDoctrine()->getManager();
        $securityContext = $this->get('security.context');
        $user = $securityContext->getToken()->getUser();
        
        if ($securityContext->isGranted('ROLE_USER'))
        {
            if ($user->getMaxPerPage())
                $maxPerPage = $user->getMaxPerPage();
        }
        
        $entities = $em->getRepository('FlyFlydbBundle:Flyline')->getLatestFlylines();

        /* pagination */
        $adapter = new ArrayAdapter($entities);
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage($maxPerPage);
        
        if (!$page)
        {
            $page = 1;
        }
        
        try
        {
            $pager->setCurrentPage($page);
        }
        catch (NotValidCurrentPageException $e)
        {
            throw new NotFoundHttpException();
        }
        
        return $this->render('FlyFlydbBundle:Flyline:index.html.twig', array(
            'pager' => $pager,
        ));
    }

    /**
     * Lists all Flyline entities for management.
     */
    public function manageAction($page=null, $maxPerPage=15)
    {
        $securityContext = $this->get('security.context');
        $user = $securityContext->getToken()->getUser();
        
        if (false === $securityContext->isGranted('ROLE_USER'))
        {
            throw new AccessDeniedException();
        }
        
        $em = $this->getDoctrine()->getManager();
        
        if ($securityContext->isGranted('ROLE_ADMIN'))
        {
            $entities = $em->getRepository('FlyFlydbBundle:Flyline')->findBy(array(), array('cared' => 'ASC'));
        } else {
            $entities = $em->getRepository('FlyFlydbBundle:Flyline')->findBy(array('owner' => $user),
                                                                        array('cared' => 'ASC'));
        }
        
        if ($user->getMaxPerPage())
            $maxPerPage = $user->getMaxPerPage();

        /* pagination */
        $adapter = new ArrayAdapter($entities);
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage($maxPerPage);
        
        if (!$page)
        {
            $page = 1;
        }
        
        try
        {
            $pager->setCurrentPage($page);
        }
        catch (NotValidCurrentPageException $e)
        {
            throw new NotFoundHttpException();
        }
        
        return $this->render('FlyFlydbBundle:Flyline:manage.html.twig', array(
            'pager' => $pager,
        ));
    }
    
    /**
     * Search, typically from a form submit. 
     * Will redirect to searchResultAction.
     */
    public function searchAction(Request $request)
    {
        $searchTerm = $request->query->get('searchTerm');
        
        return $this->redirect($this->generateUrl('flydb_search_result', array('searchTerm' => $searchTerm)));
    }
    
    /**
     * Search, can be from any GET request
     */
    public function searchResultAction($searchTerm=null, $page=null, $maxPerPage=10)
    {
        $securityContext = $this->get('security.context');
        $user = $securityContext->getToken()->getUser();

        if ($securityContext->isGranted('ROLE_USER'))
        {
            if ($user->getMaxPerPage())
                $maxPerPage = $user->getMaxPerPage();
        }
        
        $finder = $this->get('foq_elastica.finder.flydb.flyline');
        
        $pager = $finder->findPaginated($searchTerm); 
        
/*        $flylines = $finder->find($searchTerm);
        $adapter = new ArrayAdapter($flylines);
        $pager = new Pagerfanta($adapter);
*/        
        $pager->setMaxPerPage($maxPerPage);
        if (!$page)
        {
            $page = 1;
        }
        
        try
        {
            $pager->setCurrentPage($page);
        }
        catch (NotValidCurrentPageException $e)
        {
            throw new NotFoundHttpException();
        }
        
        return $this->render('FlyFlydbBundle:Flyline:index.html.twig', array(
            'pager' => $pager,
        ));
    }
        
    /**
     * Search and then manage, typically from a form submit. 
     * Will redirect to searchManageResultAction.
     */
    public function searchManageAction(Request $request)
    {
        $searchTerm = $request->query->get('searchTerm');
        
        return $this->redirect($this->generateUrl('flydb_search_manage_result', array('searchTerm' => $searchTerm)));
    }
    
    /**
     * Search and then manage, can be from any GET request
     */
    public function searchManageResultAction($searchTerm=null, $page=null, $maxPerPage=15)
    {        
        $securityContext = $this->get('security.context');
        $user = $securityContext->getToken()->getUser();
        
        if (false === $securityContext->isGranted('ROLE_USER'))
        {
            throw new AccessDeniedException();
        }
        
        $finder = $this->get('foq_elastica.finder.flydb.flyline');
        
        $flylines = $finder->find($searchTerm,10000);

        foreach ($flylines as $key => $flyline)
        {
            if (false === $securityContext->isGranted('EDIT', $flyline))
            {
                unset($flylines[$key]);
            }
        }
        
        if ($user->getMaxPerPage())
            $maxPerPage = $user->getMaxPerPage();

        /* pagination */
        $pager = new Pagerfanta(new ArrayAdapter($flylines));
        $pager->setMaxPerPage($maxPerPage);
        
        if (!$page)
        {
            $page = 1;
        }
        
        try
        {
            $pager->setCurrentPage($page);
        }
        catch (NotValidCurrentPageException $e)
        {
            throw new NotFoundHttpException();
        }
        
        return $this->render('FlyFlydbBundle:Flyline:manage.html.twig', array(
            'pager' => $pager,
        ));
    }
    
    public function exportUserCsvAction()
    {
        $securityContext = $this->get('security.context');
        $user = $securityContext->getToken()->getUser();

        if (false === $securityContext->isGranted('ROLE_USER'))
        {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $flylines = $em->getRepository('FlyFlydbBundle:Flyline')->findByOwner($user);
        
        $filename = "flylines_".$user."_".date("Y_m_d_His").".csv";
        $response = $this->render('FlyFlydbBundle:Flyline:exportCsv.html.twig', array(
            'flylines' => $flylines,
        ));
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename='.$filename);
        
        return $response;
    }
    
    public function exportCsvAction()
    {
        $securityContext = $this->get('security.context');
        $user = $securityContext->getToken()->getUser();

        if (false === $securityContext->isGranted('ROLE_USER'))
        {
            throw new AccessDeniedException();
        }
        
        $em = $this->getDoctrine()->getManager();
        $flylines = $em->getRepository('FlyFlydbBundle:Flyline')->findAll();
        
        $filename = "flylines_".date("Y_m_d_His").".csv";
        $response = $this->render('FlyFlydbBundle:Flyline:exportCsv.html.twig', array(
            'flylines' => $flylines,
        ));
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename='.$filename);
        
        return $response;
    }

    /**
     * "Care" (look after) a fly line.
     */
    public function careAction(Request $request, $id)
    {
        $securityContext = $this->get('security.context');
       
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

        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    /**
     * "Care" (look after) many fly lines, typically from a form submit.
     */
    public function careMultipleAction(Request $request)
    {
        $securityContext = $this->get('security.context');
        $flyline_ids = $request->request->get('flyline_checkbox');
        
        if ($flyline_ids)
        {
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
        }
        
        return $this->redirect($this->getRequest()->headers->get('referer'));
        
    }

    /**
     * Copy a fly line, then edit
     */
    public function copyAction($id)
    {
        $securityContext = $this->get('security.context');
        $user = $securityContext->getToken()->getUser();

        if (false === $securityContext->isGranted('ROLE_USER'))
        {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $flyline = $em->getRepository('FlyFlydbBundle:Flyline')->find($id);
        
        if (!$flyline) {
            throw $this->createNotFoundException('Unable to find specified fly line.');
        }

        $new_flyline = new Flyline();
        $new_flyline->setName($flyline->getName());
        $new_flyline->setGenotype($flyline->getGenotype());
        $new_flyline->setOrigin($flyline->getOrigin());
        $new_flyline->setTag($flyline->getTag());
        $new_flyline->setNote($flyline->getNote());
        $new_flyline->setCreated(new \DateTime());
        $new_flyline->setUpdated($new_flyline->getCreated());
        $new_flyline->setCared($new_flyline->getCreated());
        $new_flyline->setOwner($user);
        $new_flyline->setLocation($flyline->getLocation());

        $copyForm = $this->createForm(new FlylineType(), $new_flyline);

        return $this->render('FlyFlydbBundle:Flyline:new.html.twig', array(
            'entity' => $new_flyline,            
            'form'   => $copyForm->createView(),
        ));
    }

    /**
     * Copy many fly lines, typically from a form submit.
     */
    public function copyMultipleAction(Request $request)
    {
        //TODO: add this action
    }

    /**
     * Finds and displays a fly line.
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FlyFlydbBundle:Flyline')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find specified fly line.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('FlyFlydbBundle:Flyline:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new fly line.
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
            throw $this->createNotFoundException('Unable to find the Flyline.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new FlylineType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('flyline_show', array('id' => $id)));
        }

        return $this->redirect($this->generateUrl('flymanage_edit', array('id' => $id)));
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
