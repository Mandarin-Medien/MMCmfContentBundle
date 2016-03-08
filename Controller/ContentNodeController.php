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

        if($json_nodes)
        {
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
        }else
        {
            return new JsonResponse(array('status' => 'failed','msg'=> 'Nothing to update.'));
        }

    }

    /**
     * return the general ContentNode Form
     */
    public function getFormAction(ContentNode $contentNode)
    {
        return $this->render('@MMCmfContent/Form/general_form.html.twig', array(
            'form' => $this->getEditForm($contentNode)->createView()
        ));
    }

    /**
     * return the general ContentNode Form
     */
    public function getSimpleFormAction(ContentNode $contentNode)
    {
        return $this->render('@MMCmfContent/Form/general_form.html.twig', array(
            'form' => $this->getSimpleEditForm($contentNode)->createView()
        ));
    }

    /**
     * return the general ContentNode Form
     */
    public function updateAction(Request $request, ContentNode $contentNode)
    {
        $em = $this->getDoctrine()->getManager();
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
        $simpleForm = $this->get('mm_cmf_content.form_type.content_node');

        $contentNodeParser = $this->get('mm_cmf_content.content_parser');
        $hiddenFields = $contentNodeParser->getHiddenFields($contentNode);

        foreach($hiddenFields as $field)
        {
            $simpleForm->addHiddenField($field);
        }

        return $this->createForm(
            $simpleForm,
            $contentNode,
            array(
                'action' => $this->get('router')->generate('mm_cmf_content_node_update', array(
                    'id' => $contentNode->getId()
                ))
            )
        );
    }


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