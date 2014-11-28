<?php
/**
 *
 */

namespace SimpleThings\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author David Badura <d.a.badura@gmail.com>
 *
 */
trait Timestampable
{

    /**
     * @var \DateTime();
     *
     * @ORM\Column(type="datetime")
     */
    protected $createAt;

    /**
     * @var \DateTime();
     *
     * @ORM\Column(type="datetime")
     */
    protected $updateAt;

    /**
     * @return \DateTime
     */
    public function getCreateAt()
    {
        return $this->createAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdateAt()
    {
        return $this->updateAt;
    }

    /**
     *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function update()
    {
        if (!$this->createAt) {
            $this->createAt = new \DateTime();
        }

        $this->updateAt = new \DateTime();
    }
} 