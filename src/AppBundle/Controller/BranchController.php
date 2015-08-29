<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Branch;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Framework;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Framework\Route("/branch")
 *
 * @author Robin Willig <robin@dragonito.net>
 */
class BranchController extends Controller
{
    /**
     * @Framework\Route("/{id}/show", name="branch_show")
     * @Framework\Template()
     *
     * @param Branch $branch
     *
     * @return Response
     */
    public function showAction(Branch $branch)
    {
        return ['branch' => $branch, 'commits' => $branch->getCommits()];
    }
}
