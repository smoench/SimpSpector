<?php
/**
 *
 */

namespace SimpleThings\AppBundle\Gadget;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
abstract class AbstractGadget implements GadgetInterface
{
    /**
     * @return int
     */
    public function getPriority()
    {
        return 0;
    }
}