<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Cv\Form;

use Core\Form\Container;
use Core\Form\ViewPartialProviderInterface;
use Core\Form\ViewPartialProviderTrait;

/**
 * CV form container
 */
class CvContainer extends Container implements ViewPartialProviderInterface
{
    use ViewPartialProviderTrait;

    /**
     * @var string
     */
    protected $defaultPartial = 'cv/form/cv-container';
    
    public function init()
    {
        $this->setName('cv-form');
        
        $this->setForms(array(
            'contact' => array(
                'type' => 'Auth/UserInfo',
                'property' => 'contact'
            ),
            'image' => array(
                'type' => 'CvContactImage',
                'property' => 'contact',
                'use_files_array' => true
            ),
            'cvForm' => array(
                'type' => 'CvForm',
                'property' => true,
                'options' => array(
                    'enable_descriptions' => true,
                    'description' => /*@translate*/ 'Please enter ...'
                )
            )
        ));
    }
}
