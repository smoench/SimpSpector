<?php
/*
 * @author Tobias Olry <tobias.olry@gmail.com>
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as Framework;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller
{
    /**
     * @Framework\Route("/", name="homepage")
     * @Framework\Template()
     */
    public function indexAction()
    {
        $commitRepository = $this->get('simpspector.app.repository.commit');

        return ['commits' => $commitRepository->findGlobalCommits(10)];
    }
}
