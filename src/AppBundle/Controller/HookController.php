<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/hooks")
 */
class HookController extends Controller
{
    /**
     * @Route("/gitlab")
     */
    public function gitlabAction(Request $request)
    {
        $this->get('simpspector.app.gitlab.request_handler')->handle($request);

        return new Response();
    }
}
