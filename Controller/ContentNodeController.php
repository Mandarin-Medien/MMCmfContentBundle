<?php

namespace MandarinMedien\MMCmfContentBundle\Controller;


use Doctrine\ORM\Mapping\ClassMetadataInfo;
use MandarinMedien\MMCmfContentBundle\Entity\ContentNode;
use MandarinMedien\MMCmfNodeBundle\Entity\Node;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ContentNodeController extends Controller
{

    private $em;

    function __construct()
    {

    }

    /**
     * AJAX save endpoint
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
     */
    public function getFormAction(ContentNode $contentNode)
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
    public function getSimpleFormAction(Request $request,ContentNode $contentNode)
    {
        $contentNodeClassName = get_class($contentNode);

        $contentNodeParser = $this->get('mm_cmf_content.content_parser');
        $simpleFormData = $contentNodeParser->getSimpleForm($contentNodeClassName);

        $repository = $this->getDoctrine()->getRepository('MMCmfNodeBundle:Node');


        /**
         * load root node if set
         */
        $rootNode = null ;
        if((int) $request->get('root_node')) {
            $rootNode = $repository->find((int)$request->get('root_node'));
        }

        //set FormTemplate
        if (isset($simpleFormData['template']))
            $simpleFormTemplate = $simpleFormData['template'];
        else
            $simpleFormTemplate = '@MMCmfContent/Form/general_form.html.twig';

        $simpleFormType = $this->getSimpleEditForm($contentNode,$rootNode);

        // load icon
        $icon = $contentNodeParser->getIcon($contentNodeClassName);

        //generates html-dom-node-id
        $modal_dom_id = 'modal_content_node_' . $contentNode->getId();

        // render form
        return $this->render($simpleFormTemplate, array(
            'form' => $simpleFormType->createView(),
            'node_icon' => $icon,
            'modal_id' => $modal_dom_id
        ));
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

        if((int) $request->get('root_node')) {
            $rootNode = $repository->find((int)$request->get('root_node'));
        }

        //check if node need "simple" validation
        if ($isSimpleForm)
            $editForm = $this->getSimpleEditForm($contentNode, $rootNode);
        else
            $editForm = $this->getEditForm($contentNode, $rootNode);

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
     * @return \Symfony\Component\Form\Form
     */
    public function getSimpleEditForm(ContentNode $contentNode, Node $rootNode = null)
    {
        $contentNodeClassName = get_class($contentNode);

        $contentNodeParser = $this->get('mm_cmf_content.content_parser');

        $simpleFormData = $contentNodeParser->getSimpleForm($contentNodeClassName);

        //set FormType
        if (isset($simpleFormData['type']))
            $simpleFormType = $simpleFormData['type'];
        else
            $simpleFormType = $this->get('mm_cmf_content.form_type.content_node');


        //get fields to hide
        $hiddenFields = $contentNodeParser->getHiddenFields($contentNodeClassName);

        foreach ($hiddenFields as $field) {
            $simpleFormType->addHiddenField($field);
        }

        $templateVars = array(
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
    public function getEditForm(ContentNode $contentNode, Node $rootNode = null)
    {

        $templateVars = array(
            'action' => $this->get('router')->generate('mm_cmf_content_node_update', array(
                'id' => $contentNode->getId()
            ))
        );

        if ($rootNode)
            $templateVars['root_node'] = $rootNode;

        return $this->createForm(
            $this->get('mm_cmf_content.form_type.content_node'),
            $contentNode,
            $templateVars
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getDiscrimatorsModalAction()
    {
        return $this->render('@MMCmfContent/Modal/Form/content_node_select.html.twig', array(
            'discrimators' => $this->get('mm_cmf_content.content_node_factory')->getDiscrimators()
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