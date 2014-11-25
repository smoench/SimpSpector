<?php
/**
 *
 */

namespace SimpleThings\AppBundle\Gadget;

/**
 * @author David Badura <d.a.badura@gmail.com>
 */
class Repository
{
    /**
     * @var GadgetInterface[]
     */
    private $gadgets = [];

    /**
     * @var GadgetInterface[]|null
     */
    private $sortedGadgets;

    /**
     * @param GadgetInterface $gadget
     * @throws \Exception
     */
    public function add(GadgetInterface $gadget)
    {
        if ($this->has($gadget->getName())) {
            throw new \Exception(sprintf('gadget with the name "%s" exists already', $gadget->getName()));
        }

        $this->gadgets[$gadget->getName()] = $gadget;
        $this->sortedGadgets = null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        if (isset($this->gadgets[$name])) {
            return true;
        }

        return false;
    }

    /**
     * @param string $name
     * @return GadgetInterface
     * @throws \Exception
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            throw new \Exception(sprintf('gadget with the name "%s" not exists', $name));
        }

        return $this->gadgets[$name];
    }

    /**
     * @return GadgetInterface[]
     */
    public function getGadgets()
    {
        return $this->gadgets;
    }

    /**
     * @return GadgetInterface[]
     */
    public function getSortedGadgets()
    {
        if ($this->sortedGadgets) {
            $this->sortedGadgets;
        }

        $this->sortedGadgets = array_values($this->gadgets);

        // todo sort

        return $this->sortedGadgets;
    }
} 