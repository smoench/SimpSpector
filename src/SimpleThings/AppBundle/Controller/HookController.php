<?php

namespace SimpleThings\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use SimpleThings\AppBundle\Entity\MergeRequest;
use SimpleThings\AppBundle\Entity\Project;
use SimpleThings\AppBundle\Entity\Push;
use SimpleThings\AppBundle\Git\CheckoutService;
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
        $event = json_decode($request->getContent(), true);

        syslog(LOG_INFO, 'hello!');

        if ($event && \igorw\get_in($event, ['object_kind']) === 'merge_request') {

            syslog(LOG_INFO, 'hello!2');

            $client = $this->get('gitlab_api');
            $client->api('merge_requests')->addComment(
                $event['object_attributes']['source_project_id'],
                $event['object_attributes']['id'],
                $request->getContent()
            );
        } else {
        }

        $push = new Push();
        $mergeRequest = new MergeRequest();
        $project = new Project();
        $project->setRemoteId(79);
        $mergeRequest->setProject($project);
        $push->setMergeRequest($mergeRequest);
        $push->setRevision('d3defef153b63ce2d10b9d1177ab089a45fe7c65');
        $this->getCheckoutService()->create($push);

        return new Response('hello!');
    }

    /**
     * @return CheckoutService
     */
    public function getCheckoutService()
    {
        return $this->get('simple_things_app.git.checkout_service');
    }
}
