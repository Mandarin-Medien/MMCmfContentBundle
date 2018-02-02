<?php

namespace MandarinMedien\MMCmfContentBundle\Controller;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use MandarinMedien\MMCmfContentBundle\Entity\ContentNode;
use MandarinMedien\MMCmfContentBundle\Form\ContentNodeType;
use MandarinMedien\MMCmfNodeBundle\Entity\Node;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ContentNodeController extends Controller
{
    /**
     * @var $em EntityManager
     */
    private $em;

    function __construct()
    {

    }

    /**
     * AJAX save endpoint, not related to the CRUD
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function saveAction(Request $request)
    {
        $json_nodes = $request->get('nodes');

        if ($json_nodes) {
            $this->em = $this->getDoctrine()->getManager();

            foreach ($json_nodes as $id => $obj) {

                $contentNode = $this->em->find(ContentNode::class, $id);

                if ($contentNode) {

                    $className = $obj['class'];
                    unset($obj['class']);

                    $metaData = $this->getClassMetaData($className);

                    foreach ($obj as $key => $value) {

                        $method = 'set' . ucfirst($key);

                        if (method_exists($contentNode, $method)) {

                            if (!empty($metaData->associationMappings[$key])) {

                                $fieldMetaData = $metaData->associationMappings[$key];
                                $repo = $this->em->getRepository($fieldMetaData['targetEntity']);


                                //check if its just a standard relation
                                if (
                                in_array(
                                    $fieldMetaData['type'],
                                    array(
                                        ClassMetadataInfo::ONE_TO_MANY,
                                        ClassMetadataInfo::MANY_TO_MANY
                                    )
                                )
                                )
                                    $value = $repo->findById($value);
                                else
                                    $value = $repo->findOneById($value);

                            }

                            $contentNode->$method($value);
                        }
                    }

                    $this->em->persist($contentNode);
                }
            }

            $this->em->flush();

            return new JsonResponse(array('status' => 'saved'));
        } else {
            return new JsonResponse(array('status' => 'failed', 'msg' => 'Nothing to update.'));
        }

    }

    /**
     * return the general ContentNode Form
     *
     * @param ContentNode $contentNode
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(ContentNode $contentNode)
    {
        $contentNodeParser = $this->get('mm_cmf_content.content_parser');
        $icon = $contentNodeParser->getIcon($contentNode);

        return $this->render('@MMCmfContent/Form/general_form.html.twig', array(
            'form' => $this->getEditForm($contentNode)->createView(),
            'node_icon' => $icon
        ));
    }

    /**
     * return the general ContentNode Form
     *
     * @param Request $request
     * @param ContentNode $contentNode
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function simpleEditAction(Request $request, ContentNode $contentNode)
    {
        $contentNodeClassName = get_class($contentNode);

        $contentNodeParser = $this->get('mm_cmf_content.content_parser');
        $simpleFormData = $contentNodeParser->getSimpleForm($contentNodeClassName);

        $repository = $this->getDoctrine()->getRepository('MMCmfNodeBundle:Node');


        /**
         * load root node if set
         */
        $rootNode = null;
        if ((int)$request->get('root_node')) {
            $rootNode = $repository->find((int)$request->get('root_node'));
        }

        //set FormTemplate
        if (isset($simpleFormData['template']))
            $simpleFormTemplate = $simpleFormData['template'];
        else
            $simpleFormTemplate = '@MMCmfContent/Form/general_form.html.twig';

        $simpleFormType = $this->createSimpleEditForm($contentNode, $rootNode);

        // load icon
        $icon = $contentNodeParser->getIcon($contentNodeClassName);

        //generates html-dom-node-id
        $modal_dom_id = 'modal_content_node_' . $contentNode->getId();

        $deleteForm = $this->createDeleteForm($contentNode->getId());

        // render form
        return $this->render($simpleFormTemplate, array(
            'form' => $simpleFormType->createView(),
            'delete_form' => $deleteForm->createView(),
            'node_icon' => $icon,
            'modal_id' => $modal_dom_id
        ));
    }


    /**
     * Creates a form to delete a Menu entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('mm_cmf_content_node_delete', array('id' => $id)))
            ->setMethod('DELETE')
            //->add('submit', SubmitType::class, array('label' => 'Delete'))
            ->getForm();
    }


    public function deleteAction(Request $request, $id)
    {


        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        //if ($form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('MMCmfContentBundle:ContentNode')->find($id);

        if (!$entity) {
            return new JsonResponse(array('success' => false, 'msg' => 'Unable to find Node-Entity.'), 404);
        }

        if ($parent = $entity->getParent()) {
            $parent->removeNode($entity);
        }

        $em->remove($entity);
        $em->flush();
        //}

        return new JsonResponse(array('success' => true));
    }


    /**
     * validates the simple ContentNode Form
     *
     * @param Request $request
     * @param ContentNode $contentNode
     * @return JsonResponse
     */
    public function simpleUpdateAction(Request $request, ContentNode $contentNode)
    {
        return $this->updateAction($request, $contentNode, true);
    }

    /**
     * @param Request $request
     * @param ContentNode $parent_node
     * @param $contentNode_type
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function simpleNewChildAction(Request $request, ContentNode $parent_node, $contentNode_type)
    {
        return $this->newChildAction($request, $parent_node, $contentNode_type, true);
    }

    /**
     * @param Request $request
     * @param ContentNode $parent_node
     * @param $contentNode_type
     * @param bool $isSimpleForm
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newChildAction(Request $request, ContentNode $parent_node, $contentNode_type, $isSimpleForm = false)
    {
        $factory = $this->get('mm_cmf_content.content_node_factory');
        $repository = $this->getDoctrine()->getRepository('MMCmfNodeBundle:Node');

        //$parent_node = null;
        $entity = $factory->createContentNode($contentNode_type);

        $entity->setParent($parent_node);

        //check if node need "simple" validation
        if ($isSimpleForm)
            $form = $this->createSimpleCreateForm($entity);
        else
            $form = $this->createCreateForm($entity);

        return $this->render('@MMCmfContent/Modal/Form/content_node_new.html.twig', array(
            'entity' => $entity,
            'form' => $form->createView(),
            'modal_id' => ''
        ));
    }

    public function simpleCreateChildAction(Request $request, ContentNode $parent_node, $contentNode_type)
    {
        return $this->createChildAction($request, $parent_node, $contentNode_type, true);
    }

    /**
     * @param Request $request
     * @param ContentNode $parent_node
     * @param $contentNode_type
     * @return JsonResponse
     */
    public function createChildAction(Request $request, ContentNode $parent_node, $contentNode_type, $isSimpleForm = false)
    {

        $factory = $this->get('mm_cmf_content.content_node_factory');

        $entity = $factory->createContentNode($contentNode_type);

        $entity->setParent($parent_node);

        //check if node need "simple" validation
        if ($isSimpleForm)
            $form = $this->createSimpleCreateForm($entity);
        else
            $form = $this->createCreateForm($entity);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $markup = $this->get('mm_cmf_content.mm_cmf_parse_twig_extension')->cmfParse(
                $this->get('twig'),
                $entity
            );
            return new JsonResponse(array(
                    'success' => true,
                    'data' => array(
                        'id' => $entity->getId(),
                        'append_id' => $entity->getParent()->getId(),
                        'markup' => $markup
                    ))
            );
        }

        return new JsonResponse(array('success' => false));
    }


    /**
     * @param ContentNode $entity
     * @return \Symfony\Component\Form\Form
     */
    private function createCreateForm(ContentNode $entity)
    {
        $form = $this->createForm(
            ContentNodeType::class,
            $entity,
            array(
                'root_node' => $entity->getParent(),
                'action' => $this->generateUrl('mm_cmf_content_node_create_child',
                    array(
                        'contentNode_type' => $this->get('mm_cmf_content.content_node_factory')->getDiscriminatorByClass($entity),
                        'parent_node' => $entity->getParent()->getId()
                    )),
                'method' => 'POST',
            )
        );

        $form->add('submit', SubmitType::class, array('label' => 'Create'));

        return $form;
    }

    public function createSimpleCreateForm(ContentNode $contentNode)
    {
        $contentNodeClassName = get_class($contentNode);

        $contentNodeParser = $this->get('mm_cmf_content.content_parser');

        $simpleFormData = $contentNodeParser->getSimpleForm($contentNodeClassName);

        //set FormType
        if (isset($simpleFormData['type']) && class_exists($simpleFormData['type']))
            $simpleFormType = $simpleFormData['type'];
        else
            $simpleFormType = ContentNodeType::class;

        //get fields to hide
        $hiddenFields = $contentNodeParser->getHiddenFields($contentNodeClassName);

        $templateVars = array(
            'hiddenFields' => $hiddenFields,
            'action' => $this->get('router')->generate('mm_cmf_content_node_simple_create_child', array(
                'parent_node' => $contentNode->getParent()->getId(),
                'contentNode_type' => $this->get('mm_cmf_content.content_node_factory')->getDiscriminatorByClass($contentNode),
            ))
        );

        if ($contentNode->getParent())
            $templateVars['root_node'] = $contentNode->getParent();

        $form = $this->createForm(
            $simpleFormType,
            $contentNode,
            $templateVars
        );

        $form->add('submit', SubmitType::class, array('label' => 'Create'));

        return $form;
    }

    /**
     * validates the general ContentNode Form
     *
     * @param Request $request
     * @param ContentNode $contentNode
     * @param bool $isSimpleForm
     * @return JsonResponse
     */
    public function updateAction(Request $request, ContentNode $contentNode, $isSimpleForm = false)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()->getRepository('MMCmfNodeBundle:Node');

        $rootNode = null;

        if ((int)$request->get('root_node')) {
            $rootNode = $repository->find((int)$request->get('root_node'));
        }

        //check if node need "simple" validation
        if ($isSimpleForm)
            $editForm = $this->createSimpleEditForm($contentNode, $rootNode);
        else
            $editForm = $this->createEditForm($contentNode, $rootNode);

        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $markup = $this->get('mm_cmf_content.mm_cmf_parse_twig_extension')->cmfParse(
                $this->get('twig'),
                $contentNode
            );
            return new JsonResponse(array(
                    'success' => true,
                    'data' => array(
                        'id' => $contentNode->getId(),
                        'markup' => $markup
                    ))
            );
        }

        return new JsonResponse(array('success' => false));
    }

    /**
     * Returns the configured Simple Form for Frontend Editing purpose
     *
     * @param ContentNode $contentNode
     * @param Node|null $rootNode
     * @return \Symfony\Component\Form\Form
     */
    public function createSimpleEditForm(ContentNode $contentNode, Node $rootNode = null)
    {
        $contentNodeClassName = get_class($contentNode);

        $contentNodeParser = $this->get('mm_cmf_content.content_parser');

        $simpleFormData = $contentNodeParser->getSimpleForm($contentNodeClassName);
        //set FormType
        if (isset($simpleFormData['type']) && class_exists($simpleFormData['type'])) {
            $simpleFormType = $simpleFormData['type'];
        } else
            $simpleFormType = ContentNodeType::class;


        //get fields to hide
        $hiddenFields = $contentNodeParser->getHiddenFields($contentNodeClassName);

        $templateVars = array(
            'hiddenFields' => $hiddenFields,
            'action' => $this->get('router')->generate('mm_cmf_content_node_simple_update', array(
                'id' => $contentNode->getId()
            ))
        );

        if ($rootNode)
            $templateVars['root_node'] = $rootNode;

        return $this->createForm(
            $simpleFormType,
            $contentNode,
            $templateVars
        );
    }

    /**
     * @param ContentNode $contentNode
     * @param Node|null $rootNode
     *
     * @return \Symfony\Component\Form\Form
     */
    public function createEditForm(ContentNode $contentNode, Node $rootNode = null)
    {

        $templateVars = array(
            'action' => $this->get('router')->generate('mm_cmf_content_node_update', array(
                'id' => $contentNode->getId()
            ))
        );

        if ($rootNode)
            $templateVars['root_node'] = $rootNode;

        return $this->createForm(
            ContentNodeType::class,
            $contentNode,
            $templateVars
        );
    }

    /**
     * @param ContentNode $contentNode
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getDiscriminatorModalAction(ContentNode $contentNode)
    {
        $classes = $this->get('mm_cmf_content.content_parser')->getContentNodeTypes();


        return $this->render('@MMCmfContent/Modal/Form/content_node_select.html.twig', array(
            'classes' => $classes,
            'contentNode' => $contentNode
        ));
    }

    /**
     * @param $className
     * @return mixed
     */
    private function getClassMetaData($className)
    {
        return $this->em->getClassMetadata($className);
    }

}