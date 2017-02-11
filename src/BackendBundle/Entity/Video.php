<?php

namespace BackendBundle\Entity;

/**
 * Video
 */
class Video
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $videoIdentifier;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $videoSource;

    /**
     * @var string
     */
    private $videoImage;

    /**
     * @var string
     */
    private $status;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var \BackendBundle\Entity\User
     */
    private $user;


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
     * Set videoIdentifier
     *
     * @param string $videoIdentifier
     *
     * @return Video
     */
    public function setVideoIdentifier($videoIdentifier)
    {
        $this->videoIdentifier = $videoIdentifier;

        return $this;
    }

    /**
     * Get videoIdentifier
     *
     * @return string
     */
    public function getVideoIdentifier()
    {
        return $this->videoIdentifier;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Video
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Video
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set videoSource
     *
     * @param string $videoSource
     *
     * @return Video
     */
    public function setVideoSource($videoSource)
    {
        $this->videoSource = $videoSource;

        return $this;
    }

    /**
     * Get videoSource
     *
     * @return string
     */
    public function getVideoSource()
    {
        return $this->videoSource;
    }

    /**
     * Set videoImage
     *
     * @param string $videoImage
     *
     * @return Video
     */
    public function setVideoImage($videoImage)
    {
        $this->videoImage = $videoImage;

        return $this;
    }

    /**
     * Get videoImage
     *
     * @return string
     */
    public function getVideoImage()
    {
        return $this->videoImage;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Video
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Video
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Video
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set user
     *
     * @param \BackendBundle\Entity\User $user
     *
     * @return Video
     */
    public function setUser(\BackendBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \BackendBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}

