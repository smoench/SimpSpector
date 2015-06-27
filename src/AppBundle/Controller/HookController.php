<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/hooks")
 *
 * @author David Badura <d.a.badura@gmail.com>
 */
class HookController extends Controller
{
    /**
     * @Route("")
     * @Route("/gitlab")
     *
     * @param Request $request
     * @return Response
     */
    public function hookAction(Request $request)
    {
        $event = $this->get('simpspector.app.webhook.event_factory')->create($request);

        if (!$event) {
            throw $this->createNotFoundException();
        }

        $this->get('simpspector.app.webhook.handler')->handle($event);

        return new Response();
    }
}
