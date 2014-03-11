<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Cv\Entity;

use Core\Entity\AbstractIdentifiableEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\EmbeddedDocument
 */
class Employment extends AbstractIdentifiableEntity
{
    /**
     * @ODM\String
     * @var unknown
     */
    protected $startDate;
    /**
     * 
     * @var unknown
     * @ODM\String
     */
    protected $endDate;
    
    /**
     * @ODM\Boolean
     * @var unknown
     */
    protected $currentIndicator;
    
    /**
     * 
     * @var unknown
     * @ODM\String
     */
    protected $description;
    
    /**
     *
     * @var string Organisation Name
     * @ODM\String
     */
    protected $organizationName;
    
    public function setStartDate($startDate)
    {
        $this->startDate = (string) $startDate;
        return $this;
    }
    
    public function getStartDate()
    {
        return $this->startDate;
    }
    
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
        return $this;
    }
    
    public function getCurrentIndicator()
    {
    	return $this->currentIndicator;
    }
    
    public function setCurrentIndicator($currentIndicator)
    {
    	$this->currentIndicator = $currentIndicator;
    	return $this;
    }
     
    
    public function getEndDate()
    {
        return $this->endDate;
    }
    
    public function setOrganizationName($value)
    {
    	$this->organizationName = $value;
    	return $this;
    }
    
    public function getOrganizationName()
    {
    	return $this->organizationName;
    }
    
    public function setDescription($value)
    {
    	$this->description = $value;
    	return $this;
    }
    
    public function getDescription()
    {
    	return $this->description;
    }
}