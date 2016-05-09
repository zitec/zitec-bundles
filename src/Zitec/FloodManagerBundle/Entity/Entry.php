<?php

namespace Zitec\FloodManagerBundle\Entity;

/**
 * Represents a flood entry. Flood entries will be used to determine flood attempts on certain user-defined events.
 */
class Entry
{
    /**
     * The unique identifier of the flood entry. 
     * 
     * @var int
     */
    protected $id;
    
    /**
     * The event identifier. Each event has a unique name and reffers to an action in the application
     * (e.g. user login, user registration).
     * 
     * @var string
     */
    protected $event;
    
    /**
     * The source identifier (e.g. the client's IP address or hostname).
     * 
     * @var string
     */
    protected $source;
    
    /**
     * The entry creation time.
     * 
     * @var \DateTime
     */
    protected $created;
    
    /**
     * The moment when the entry will expire.
     * 
     * @var \DateTime
     */
    protected $expires;
    
    /**
     * The id getter.
     * 
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * The event identifier getter.
     * 
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }
    
    /**
     * The event identifier setter.
     * 
     * @param string $event
     * 
     * @return Entry
     */
    public function setEvent($event)
    {
        $this->event = $event;
        
        return $this;
    }
    
    /**
     * The source identifier getter.
     * 
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }
    
    /**
     * The source identifier setter.
     * 
     * @param string $source
     * 
     * @return Entry
     */
    public function setSource($source)
    {
        $this->source = $source;
        
        return $this;
    }
    
    /**
     * The creation time getter.
     * 
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }
    
    /**
     * The creation time setter.
     * 
     * @param \DateTime $created
     * 
     * @return Entry
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;

        return $this;
    }
    
    /**
     * The expiration moment getter.
     * 
     * @return \DateTime
     */
    public function getExpires()
    {
        return $this->expires;
    }
    
    /**
     * The expiration moment setter.
     * 
     * @param \DateTime $expires
     * 
     * @return Entry
     */
    public function setExpires(\DateTime $expires)
    {
        $this->expires = $expires;
        
        return $this;
    }
}
