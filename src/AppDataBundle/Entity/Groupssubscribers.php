<?php

namespace AppDataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Groupssubscribers
 *
 * @ORM\Table(name="GroupsSubscribers", indexes={@ORM\Index(name="idGroup", columns={"idGroup"}), @ORM\Index(name="idSubscriber", columns={"idSubscriber"})})
 * @ORM\Entity
 */
class Groupssubscribers
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppDataBundle\Entity\Subscribers
     *
     * @ORM\ManyToOne(targetEntity="AppDataBundle\Entity\Subscribers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idSubscriber", referencedColumnName="id")
     * })
     */
    private $idsubscriber;

    /**
     * @var \AppDataBundle\Entity\Groups
     *
     * @ORM\ManyToOne(targetEntity="AppDataBundle\Entity\Groups")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idGroup", referencedColumnName="id")
     * })
     */
    private $idgroup;



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idsubscriber
     *
     * @param \AppDataBundle\Entity\Subscribers $idsubscriber
     * @return Groupssubscribers
     */
    public function setIdsubscriber(\AppDataBundle\Entity\Subscribers $idsubscriber = null)
    {
        $this->idsubscriber = $idsubscriber;

        return $this;
    }

    /**
     * Get idsubscriber
     *
     * @return \AppDataBundle\Entity\Subscribers 
     */
    public function getIdsubscriber()
    {
        return $this->idsubscriber;
    }

    /**
     * Set idgroup
     *
     * @param \AppDataBundle\Entity\Groups $idgroup
     * @return Groupssubscribers
     */
    public function setIdgroup(\AppDataBundle\Entity\Groups $idgroup = null)
    {
        $this->idgroup = $idgroup;

        return $this;
    }

    /**
     * Get idgroup
     *
     * @return \AppDataBundle\Entity\Groups 
     */
    public function getIdgroup()
    {
        return $this->idgroup;
    }
}
