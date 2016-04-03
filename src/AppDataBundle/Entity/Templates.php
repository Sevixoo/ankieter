<?php

namespace AppDataBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Templates
 *
 * @ORM\Table(name="Templates")
 * @ORM\Entity
 */
class Templates
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="creator_id", type="integer", nullable=false)
     */
    private $creatorId;

    /**
     * @var string
     *
     * @ORM\Column(name="fields_schema", type="text", length=65535, nullable=false)
     */
    private $fieldsSchema;

    /**
     * @var string
     *
     * @ORM\Column(name="output_schema", type="text", length=65535, nullable=false)
     */
    private $outputSchema;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="date", nullable=false)
     */
    private $createDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set name
     *
     * @param string $name
     * @return Templates
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set creatorId
     *
     * @param integer $creatorId
     * @return Templates
     */
    public function setCreatorId($creatorId)
    {
        $this->creatorId = $creatorId;

        return $this;
    }

    /**
     * Get creatorId
     *
     * @return integer 
     */
    public function getCreatorId()
    {
        return $this->creatorId;
    }

    /**
     * Set fieldsSchema
     *
     * @param string $fieldsSchema
     * @return Templates
     */
    public function setFieldsSchema($fieldsSchema)
    {
        $this->fieldsSchema = $fieldsSchema;

        return $this;
    }

    /**
     * Get fieldsSchema
     *
     * @return string 
     */
    public function getFieldsSchema()
    {
        return $this->fieldsSchema;
    }

    /**
     * Set outputSchema
     *
     * @param string $outputSchema
     * @return Templates
     */
    public function setOutputSchema($outputSchema)
    {
        $this->outputSchema = $outputSchema;

        return $this;
    }

    /**
     * Get outputSchema
     *
     * @return string 
     */
    public function getOutputSchema()
    {
        return $this->outputSchema;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return Templates
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
