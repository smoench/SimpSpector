<?php

namespace SimpleThings\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/hooks")
 */
class HookController extends Controller
{
    /**
     * @Route("/gitlab")
     * @Template()
     */
    public function gitlabAction(Request $request)
    {
        $event = json_decode($request->getContent(), true);

        syslog(LOG_INFO, 'hello!');

        if (\igorw\get_in($event, ['object_kind']) === 'merge_request') {

            syslog(LOG_INFO, 'hello!2');

            $client = $this->get('gitlab_api');
            $client->api('merge_requests')->addComment(
                $event['object_attributes']['source_project_id'],
                $event['object_attributes']['id'],
                $request->getContent()
            );
        } else {

        }

        return new Response('hello!');
    }
}
