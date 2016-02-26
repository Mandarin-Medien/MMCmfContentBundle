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

    public function saveAction(Request $request)
    {
        $json_nodes = $request->get('nodes');

        $this->em = $this->getDoctrine()->getManager();

        foreach ($json_nodes as $id => $obj) {

            $contentNode = $this->em->find(ContentNode::class, $id);

            if ($contentNode) {

                $className = $obj['class'];
                unset($obj['class']);

                $metaData = $this->getClassMetaData($className);

                dump($metaData);

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


                            dump($fieldMetaData['type'], $value);
                        }

                        $contentNode->$method($value);
                    }
                }

                $this->em->persist($contentNode);
            }
        }

        $this->em->flush();

        return new JsonResponse(array('status' => 'saved'));
    }

    private function getClassMetaData($className)
    {
        return $this->em->getClassMetadata($className);
    }
}