<?php

namespace AppDataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Forms
 *
 * @ORM\Table(name="Forms", indexes={@ORM\Index(name="FK_Forms_Templates_id", columns={"template_id"})})
 * @ORM\Entity
 */
class Forms
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetime", nullable=false)
     */
    private $createDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deadline", type="datetime", nullable=true)
     */
    private $deadline;

    /**
     * @var integer
     *
     * @ORM\Column(name="template_version", type="integer", nullable=true)
     */
    private $templateVersion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=false)
     */
    private $isActive;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppDataBundle\Entity\Templates
     *
     * @ORM\ManyToOne(targetEntity="AppDataBundle\Entity\Templates")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     * })
     */
    private $template;



    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return Forms
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime 
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return Forms
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set deadline
     *
     * @param \DateTime $deadline
     * @return Forms
     */
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;

        return $this;
    }

    /**
     * Get deadline
     *
     * @return \DateTime 
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * Set templateVersion
     *
     * @param integer $templateVersion
     * @return Forms
     */
    public function setTemplateVersion($templateVersion)
    {
        $this->templateVersion = $templateVersion;

        return $this;
    }

    /**
     * Get templateVersion
     *
     * @return integer 
     */
    public function getTemplateVersion()
    {
        return $this->templateVersion;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Forms
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
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
     * Set template
     *
     * @param \AppDataBundle\Entity\Templates $template
     * @return Forms
     */
    public function setTemplate(\AppDataBundle\Entity\Templates $template = null)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return \AppDataBundle\Entity\Templates 
     */
    public function getTemplate()
    {
        return $this->template;
    }
}
