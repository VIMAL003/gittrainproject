<?php

namespace Application\Form;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ChildParentsForm
 *
 * @author ASHISH
 */
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use DoctrineORMModule\Stdlib\Hydrator\DoctrineEntity as DoctrineHydrator;
use Zend\ServiceManager\ServiceManager;

class AddParentStudyForm extends SubAbstractForm {
    protected $id;
    protected $studyObj;
    public function __construct(ServiceManager $serviceManager, $id=NULL, $type='admin', $studyObj=NULL) {
	
	parent::__construct('create-study-form');
	$this->id = $id;
	$this->studyObj = $studyObj;
	$entityManager = $serviceManager->get('Doctrine\ORM\EntityManager');
	$this->entity = new \Application\Entity\PhvStudy();

	$this->setAttribute('method', 'post');
	$this->setAttribute('class', 'edit-prompt');
	$this->setAttribute('novalidate', TRUE);
	if($type == 'cro') {
	    // Add the cro study fieldset, and set it as the base fieldset
	    $studyFieldset = new Fieldset\CroStudyFieldset($serviceManager, $this->id, $this->studyObj);
	    $studyFieldset->setUseAsBaseFieldset(true);
	} elseif ($type == 'sponsor') {
	    // Add the cro study fieldset, and set it as the base fieldset
	    $studyFieldset = new Fieldset\SponsorStudyFieldset($serviceManager, $this->id, $this->studyObj);
	    $studyFieldset->setUseAsBaseFieldset(true);
	} else {
	    // Add the study fieldset, and set it as the base fieldset
	    $studyFieldset = new Fieldset\StudyFieldset($serviceManager, $this->id);
	    $studyFieldset->setUseAsBaseFieldset(true);
	}
	$this->add($studyFieldset);
	$this->add(array(
	    'type' => 'submit',
	    'name' => 'submit',
	    'attributes' => array(
		'value' => 'Save'
	    )
	));
	$this->add(array(
            'name' => $this->csrfToken,
            'attributes' => array(
                'type' => 'hidden',
                'value' => $this->csrfTokenValue
            ),
        ));
    }
}
?>
