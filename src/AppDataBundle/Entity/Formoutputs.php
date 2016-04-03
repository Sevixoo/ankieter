<?php

namespace AppDataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Formoutputs
 *
 * @ORM\Table(name="FormOutputs", indexes={@ORM\Index(name="FK_FormOutputs_Forms_id", columns={"form_id"}), @ORM\Index(name="FK_FormOutputs_Subscribers_id", columns={"subscriber_id"})})
 * @ORM\Entity
 */
class Formoutputs
{
    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=64, nullable=false)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="answer", type="text", length=65535, nullable=true)
     */
    private $answer;

    /**
     * @var string
     *
     * @ORM\Column(name="pre_output", type="string", length=255, nullable=true)
     */
    private $preOutput;

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
     *   @ORM\JoinColumn(name="subscriber_id", referencedColumnName="id")
     * })
     */
    private $subscriber;

    /**
     * @var \AppDataBundle\Entity\Forms
     *
     * @ORM\ManyToOne(targetEntity="AppDataBundle\Entity\Forms")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="form_id", referencedColumnName="id")
     * })
     */
    private $form;



    /**
     * Set token
     *
     * @param string $token
     * @return Formoutputs
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set answer
     *
     * @param string $answer
     * @return Formoutputs
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer
     *
     * @return string 
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set preOutput
     *
     * @param string $preOutput
     * @return Formoutputs
     */
    public function setPreOutput($preOutput)
    {
        $this->preOutput = $preOutput;

        return $this;
    }

    /**
     * Get preOutput
     *
     * @return string 
     */
    public function getPreOutput()
    {
        return $this->preOutput;
    }

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
     * Set subscriber
     *
     * @param \AppDataBundle\Entity\Subscribers $subscriber
     * @return Formoutputs
     */
    public function setSubscriber(\AppDataBundle\Entity\Subscribers $subscriber = null)
    {
        $this->subscriber = $subscriber;

        return $this;
    }

    /**
     * Get subscriber
     *
     * @return \AppDataBundle\Entity\Subscribers 
     */
    public function getSubscriber()
    {
        return $this->subscriber;
    }

    /**
     * Set form
     *
     * @param \AppDataBundle\Entity\Forms $form
     * @return Formoutputs
     */
    public function setForm(\AppDataBundle\Entity\Forms $form = null)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Get form
     *
     * @return \AppDataBundle\Entity\Forms 
     */
    public function getForm()
    {
        return $this->form;
    }
}
