<?php

namespace MandarinMedien\MMCmfContentBundle\Controller;


use Doctrine\ORM\Mapping\ClassMetadataInfo;
use MandarinMedien\MMCmfContentBundle\Entity\ContentNode;
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
     * @param ContentNode $contentNode
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getSimpleFormAction(ContentNode $contentNode)
    {
        $contentNodeParser = $this->get('mm_cmf_content.content_parser');
        $simpleFormData = $contentNodeParser->getSimpleForm($contentNode);

        //set FormTemplate
        if (isset($simpleFormData['template']))
            $simpleFormTemplate = $simpleFormData['template'];
        else
            $simpleFormTemplate = '@MMCmfContent/Form/general_form.html.twig';

        $simpleFormType = $this->getSimpleEditForm($contentNode);

        // load icon
        $icon = $contentNodeParser->getIcon($contentNode);

        //generates html-dom-node-id
        $modal_dom_id = 'modal_content_node_'.$contentNode->getId();

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
        return $this->updateAction($request,$contentNode,true);
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

        //check if node need "simple" validation
        if($isSimpleForm)
            $editForm = $this->getSimpleEditForm($contentNode);
        else
            $editForm = $this->getEditForm($contentNode);

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
    public function getSimpleEditForm(ContentNode $contentNode)
    {
        $contentNodeParser = $this->get('mm_cmf_content.content_parser');

        $simpleFormData = $contentNodeParser->getSimpleForm($contentNode);

        //set FormType
        if (isset($simpleFormData['type']))
            $simpleFormType = $simpleFormData['type'];
        else
            $simpleFormType = $this->get('mm_cmf_content.form_type.content_node');


        //get fields to hide
        $hiddenFields = $contentNodeParser->getHiddenFields($contentNode);

        foreach ($hiddenFields as $field) {
            $simpleFormType->addHiddenField($field);
        }

        return $this->createForm(
            $simpleFormType,
            $contentNode,
            array(
                'action' => $this->get('router')->generate('mm_cmf_content_node_simple_update', array(
                    'id' => $contentNode->getId()
                ))
            )
        );
    }

    /**
     * @param ContentNode $contentNode
     * @return \Symfony\Component\Form\Form
     */
    public function getEditForm(ContentNode $contentNode)
    {
        return $this->createForm(
            $this->get('mm_cmf_content.form_type.content_node'),
            $contentNode,
            array(
                'action' => $this->get('router')->generate('mm_cmf_content_node_update', array(
                    'id' => $contentNode->getId()
                ))
            )
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