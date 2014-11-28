<?php
/**
 * @author Lars Wallenborn <lars@wallenborn.net>
 */

namespace SimpleThings\AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route
use SimpleThings\AppBundle\Entity\MergeRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/image")
 */
class ImageController extends Controller
{
    /**
     * @Route("/{id}", name="image")
     */
    public function show(MergeRequest $mergeRequest)
    {
        return new Response('ABC');
    }
}
