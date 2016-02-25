<?php

namespace MandarinMedien\MMCmfContentBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ContentNodeController extends Controller
{
    public function saveAction(Request $request)
    {
        $json_nodes = $request->get('nodes');

        dump($json_nodes);

        return new JsonResponse(array('status'=>'saved'));


    }
}
