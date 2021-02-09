<?php
namespace ONM\Hsmail\Domain\Model;


/***
 *
 * This file is part of the "Hsmail" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2021 
 *
 ***/
/**
 * Formconfig
 */
class Formconfig extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * id
     * 
     * @var string
     */
    protected $id = '';

    /**
     * randomId
     * 
     * @var string
     */
    protected $randomId = '';

    /**
     * title
     * 
     * @var string
     */
    protected $title = '';

    /**
     * Returns the id
     * 
     * @return string $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the id
     * 
     * @param string $id
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns the randomId
     * 
     * @return string $randomId
     */
    public function getRandomId()
    {
        return $this->randomId;
    }

    /**
     * Sets the randomId
     * 
     * @param string $randomId
     * @return void
     */
    public function setRandomId($randomId)
    {
        $this->randomId = $randomId;
    }

    /**
     * Returns the title
     * 
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     * 
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
}
