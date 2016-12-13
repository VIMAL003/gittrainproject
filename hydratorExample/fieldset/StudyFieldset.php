<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Form\Fieldset;

use Application\Entity\PhvStudy;
use Zend\Form\Fieldset,
    Zend\InputFilter\InputFilterProviderInterface,
    Zend\ServiceManager\ServiceManager;
use DoctrineORMModule\Stdlib\Hydrator\DoctrineEntity as DoctrineHydrator;

class StudyFieldset extends Fieldset implements InputFilterProviderInterface {

    protected $inputFilter;
    protected $dbAdapter;
    private $_curency;
    private $_sponser;
    private $id;
    private $em;
    
    /**
     * Constructor of company class
     * @param integer $id
     * @param string $name
     */
    public function __construct(ServiceManager $serviceManager, $id=NULL) {

	parent::__construct('study');
	
	$this->id = $id;
	$this->em = $serviceManager->get('Doctrine\ORM\EntityManager');
	$this->setHydrator(new DoctrineHydrator($this->em, 'Application\Entity\PhvStudy'))
		->setObject(new PhvStudy());
	
	$this->__addElements();
	$this->add(array(
	    'type' => 'Zend\Form\Element\Collection',
	    'name' => 'phvStudyPricing',
	    'options' => array(
		'label' => 'CRO',
		'count' => 1,
		'should_create_template' => true,
		'object_manager' => $serviceManager,
		'allow_add' => true,
		'target_element' => new StudyCroFieldset($serviceManager),
	    ),
	    'attribute' => array('class' => 'croFieldset')
	));
	$this->setLabel('Study');
    }

    /**
     * Initialize the from element
     * @return void
     */
    private function __addElements() {
	$sponserClass = 'form-control select select2';
	$this->add(array(
	    'name' => 'protocol',
	    'type' => 'text',
	    'attributes' => array(
		'id' => 'protocol',
		'class' => 'form-control maxlength-simple',
		'placeholder' => 'Protocol',
		'required' => true
	    ),
	    'options' => array(
		'label' => 'Protocol',
	    )
	));
	$this->add(array(
	    'name' => 'drugname',
	    'type' => 'text',
	    'attributes' => array(
		'id' => 'drugname',
		'class' => 'form-control maxlength-simple',
		'placeholder' => 'Drug English Name',
		'required' => true
	    ),
	    'options' => array(
		'label' => 'Drug English Name',
	    )
	));
	$this->add(array(
	    'name' => 'chineseDrugname',
	    'type' => 'text',
	    'attributes' => array(
		'id' => 'chineseDrugname',
		'class' => 'form-control maxlength-simple',
		'placeholder' => 'Drug Chinese Name',
		'required' => true
	    ),
	    'options' => array(
		'label' => 'Drug Chinese Name',
	    )
	));
	
	$this->add(array(
	    'name' => 'sponser',
	    'type' => 'DoctrineModule\Form\Element\ObjectSelect',
	    'attributes' => array(
		'id' => 'sponser_id',
		'class' => $sponserClass,
		'required' => true
	    ),
	    'options' => array(
		'label' => 'Sponsor',
		'empty_option' => 'Select Sponsor',
		'options' => $this->_getSponser(),
		'object_manager' => $this->em,
		'target_class' => 'Application\Entity\PhvStudy',
	    )
	));
	$this->add(array(
	    'name' => 'currency',
	    'type' => 'DoctrineModule\Form\Element\ObjectSelect',
	    'attributes' => array(
		'id' => 'currency',
		'class' => 'form-control select select2',
		'required' => true,
	    ),
	    'options' => array(
		'label' => 'Currency',
		'empty_option' => 'Select Currency',
		'options' => $this->_getCurrency(),
		'object_manager' => $this->em,
		'target_class' => 'Application\Entity\PhvStudy',
	    )
	));

	$this->add(array(
	    'name' => 'startDate',
	    'type' => 'Zend\Form\Element\Date',
	    'attributes' => array(
		'class' => 'form-control',
		'id' => 'start_date',
		'custom-type' => 'date',
		'data-lable' => 'MM/DD/Y',
		'data-minDate' => date('m/d/Y'),
		'required' => true,
	    ),
	    'options' => array(
		'label' => 'Start Date',
	    )
	));
	$this->add(array(
	    'name' => 'endDate',
	    'type' => 'Zend\Form\Element\Date',
	    'attributes' => array(
		'class' => 'form-control',
		'id' => 'end_date',
		'custom-type' => 'date',
		'data-lable' => 'MM/DD/Y',
		'data-minDate' => date('m/d/Y')
	    ),
	    'options' => array(
		'label' => 'End Date',
	    )
	));
	$this->add(array(
	    'name' => 'status',
	    'type' => 'select',
	    'attributes' => array(
		'id' => 'status',
		'class' => 'form-control',
		'required' => true,
	    ),
	    'options' => array(
		'label' => 'Status',
		'options' => $this->_getStatus(),
	    )
	));
	$this->add(array(
	    'name' => 'submitToCfda',
	    'type' => 'Zend\Form\Element\Checkbox',
	    'options' => array(
		'label' => 'CFDA',
		'use_hidden_element' => true,
		'checked_value' => \Application\Entity\PhvOrganisation::CFDA,
		'unchecked_value' => 0,
	    ),
	    'attributes' => array(
		'id' => 'submit_to_cfda'
	    )
	));
	$this->add(array(
	    'name' => 'submitToUsfda',
	    'type' => 'Zend\Form\Element\Checkbox',
	    'options' => array(
		'label' => 'US FDA',
		'use_hidden_element' => true,
		'checked_value' => \Application\Entity\PhvOrganisation::USFDA,
		'unchecked_value' => 0,
	    ),
	    'attributes' => array(
		'id' => 'submit_to_usfda'
	    )
	));
	$this->add(array(
	    'name' => 'submitToCioms',
	    'type' => 'Zend\Form\Element\Checkbox',
	    'options' => array(
		'label' => 'CIOMS',
		'use_hidden_element' => true,
		'checked_value' => \Application\Entity\PhvOrganisation::CIOMS,
		'unchecked_value' => 0,
	    ),
	    'attributes' => array(
		'id' => 'submit_to_cioms'
	    )
	));
	$this->add(array(
	    'name' => 'check_all',
	    'type' => 'Zend\Form\Element\Checkbox',
	    'options' => array(
		'label' => 'All',
		'use_hidden_element' => true,
		'checked_value' => 1,
		'unchecked_value' => 0,
		'required' => false
	    ),
	    'attributes' => array(
		'id' => 'check_all'
	    )
	));
	 $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
                'id' => 'study_id',
            ),
        ));
	$this->add(array(
	    'name' => 'submit',
	    'attributes' => array(
		'type' => 'submit',
		'value' => 'Save',
		'class' => 'btn btn-inline btn-primary btn-rounded',
		'id' => 'formSubmit'
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

    /**
     * Input filter function which is not used
     * 
     * @param InputFilterInterface $inputFilter
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter) {
	throw new \Exception("Not used");
    }

    /**
     * Add fileter and validator for form
     * 
     * @return array
     */
    public function getInputFilterSpecification() {
	return array(
	    'protocol' => array(
		'required' => true,
		'validators' =>
		array(
		    $this->setNotEmptyValidation('protocol'),
		    array(
			'name' => 'StringLength',
			'options' => array(
			    'encoding' => 'UTF-8',
			    'max' => 100
			),
			'break_chain_on_failure' => true
		    ),
		    array(
			'name' => 'Regex',
			'options' => array(
			    'pattern' => '/^[a-zA-Z0-9\s]+$/',
			    'messages' => array(
				\Zend\Validator\Regex::NOT_MATCH => 'Please use alphanumeric characters or spaces only'
			    )
			),
			'break_chain_on_failure' => true
		    ),
		    array(
			'name' => 'Callback',
			'options' => array(
			    'messages' => array(
				\Zend\Validator\Callback::INVALID_VALUE => 'Protocol already exists',
			    ),
			    'Callback' => function($currency, $context = array()) {
				$protocol = $context['protocol'];
				$tabelObj = new \Application\Entity\PhvStudy();
				$datas = $tabelObj->checkExistProtocol($protocol, $this->id);
				if ($datas == 0) {
				    return true;
				}
				return false;
			    }
			),
		    )
		)
	    
	    ),
	    'status' => array(
		'required' => true,
		'validators' =>
		array(
		    $this->setNotEmptyValidation('status','select'),
		)
	    
	    ),
	    'drugname' => array(
		'required' => true,
		'validators' =>
		array(
		    $this->setNotEmptyValidation('drug english name'),
		    array(
			'name' => 'StringLength',
			'options' => array(
			    'encoding' => 'UTF-8',
			    'max' => 100
			),
			'break_chain_on_failure' => true
		    ),
		    array(
			'name' => 'Regex',
			'options' => array(
			    'pattern' => '/^[a-zA-Z0-9\s]+$/',
			    'messages' => array(
				\Zend\Validator\Regex::NOT_MATCH => 'Please use alphanumeric characters or spaces only'
			    )
			),
			'break_chain_on_failure' => true
		    ),
		)
	    
	    ),
	    'chineseDrugname' => array(
		'required' => true,
		'validators' =>
		array(
		    $this->setNotEmptyValidation('drug chinese name'),
		    array(
			'name' => 'StringLength',
			'options' => array(
			    'encoding' => 'UTF-8',
			    'max' => 100
			),
			'break_chain_on_failure' => true
		    ),
		)
	    
	    ),
	    'sponser' => array(
		'required' => true,
		'validators' => array($this->setNotEmptyValidation('sponsor', 'select'),
		    array(
			'name' => 'InArray',
			'options' => array(
			    'haystack' => array_flip($this->_getSponser()),
			    'messages' => array(
				'notInArray' => 'Please select valid sponsor'
			    ),
			),
		    ),
		)
	    ),
	    'currency' => array(
		'required' => true,
		'validators' => array($this->setNotEmptyValidation('currency', 'select'),
		    array(
			'name' => 'Callback',
			'options' => array(
			    'messages' => array(
				\Zend\Validator\Callback::INVALID_VALUE => 'Please select valid currency',
			    ),
			    'Callback' => function($currency, $context = array()) {
				$sponser_id = $context['sponser'];
				$companyCurrencyObj = new \Application\Entity\PhvCompanyCurrencyCredit();
				$currencyData = $companyCurrencyObj->getCompanyCurrency($sponser_id);
				if (isset($currencyData) && in_array($currencyData[$currency], $currencyData)) {
				    return true;
				}
				return false;
			    }
			),
		    )
		)
	    ),
	    'submitToUsfda' => array(
		'required' => true,
		'validators' => array(
		    array(
			'name' => 'Callback',
			'options' => array(
			    'messages' => array(
				\Zend\Validator\Callback::INVALID_VALUE => 'Please select any one option',
			    ),
			    'callback' => function($value, $context = array()) {
				if ($value == 0 && $context['submitToCioms'] == 0 && $context['submitToCfda'] == 0) {
				    return FALSE;
				}
				return TRUE;
			    },
			),
		    ),
		    array(
			'name' => 'Digits',
			'break_chain_on_failure' => true,
			'options' => array(
			    'messages' => array(
				\Zend\Validator\Digits::NOT_DIGITS => 'Please select USFDA',
			    ),
			),
		    )
		    
		)
	    ),
	    'submitToCioms' => array(
		'required' => true,
		'validators' => array(
		    array(
			'name' => 'Callback',
			'options' => array(
			    'messages' => array(
				\Zend\Validator\Callback::INVALID_VALUE => '',
			    ),
			    'callback' => function($value, $context = array()) {
				if ($value == 0 && $context['submitToUsfda'] == 0 && $context['submitToCfda'] == 0) {
				    return FALSE;
				}
				return TRUE;
			    },
			),
		    ),
		    array(
			'name' => 'Digits',
			'break_chain_on_failure' => true,
			'options' => array(
			    'messages' => array(
				\Zend\Validator\Digits::NOT_DIGITS => 'Please select CIOMS',
			    ),
			),
		    ),
		)
	    ),
	    'submitToCfda' => array(
		'required' => true,
		'validators' => array(
		    array(
			'name' => 'Callback',
			'options' => array(
			    'messages' => array(
				\Zend\Validator\Callback::INVALID_VALUE => '',
			    ),
			    'callback' => function($value, $context = array()) {
				if ($value == 0 && $context['submitToUsfda'] == 0 && $context['submitToCioms'] == 0) {
				    return FALSE;
				}
				return true;
			    },
			),
		    ),
		    array(
			'name' => 'Digits',
			'break_chain_on_failure' => true,
			'options' => array(
			    'messages' => array(
				\Zend\Validator\Digits::NOT_DIGITS => 'Please select CFDA',
			    ),
			),
		    )
		    
		)
	    ),
	    'startDate' => array(
		'required' => true,
		'validators' => array($this->setNotEmptyValidation('StartDate', 'select'),
		    array(
			'name' => 'Callback',
			'options' => array(
			    'messages' => array(
				\Zend\Validator\Callback::INVALID_VALUE => 'Start date should be less than end date',
			    ),
			    'callback' => function($value, $context = array()) {
			$startDate = $value;
			$endDate = $context['endDate'];
			$isValid = false;
			if ($endDate == "" || strtotime($startDate) <= strtotime($endDate)) {
			    $isValid = true;
			}
			return $isValid;
		    },
			),
		    ),
		)
	    ),
	    'endDate' => array(
		'required' => false,
		'validators' => array(
		    array(
			'name' => 'Callback',
			'options' => array(
			    'messages' => array(
				\Zend\Validator\Callback::INVALID_VALUE => 'End date should be greater than start date',
			    ),
			    'callback' => function($value, $context = array()) {
				$startDate = $context['startDate'];
				$endDate = $value;
				$isValid = false;
				if ($endDate == "" || strtotime($endDate) >= strtotime($startDate)) {
				    $isValid = true;
				} else if (strtotime($endDate) > time()) {
				    $isValid = true;
				}
				return $isValid;
			    },
			),
		    ),
		),
	    )
	);
    }

    private function setNotEmptyValidation($label = null, $subLabel = "enter") {
	return array(
	    'name' => 'NotEmpty',
	    'options' => array(
		'messages' => array(
		    \Zend\Validator\NotEmpty::IS_EMPTY => 'Please ' . $subLabel . ' ' . $label
		)
	    ),
	    'break_chain_on_failure' => true
	);
    }

    private function setCommonValidationText($field) {
	$label = array_keys($field);
	$length = array_values($field);
	return array($this->setNotEmptyValidation($label[0]),
	    array(
		'name' => 'StringLength',
		'options' => array(
		    'encoding' => 'UTF-8',
		    'max' => $length[0]
		),
		'break_chain_on_failure' => true
	    ),
	    array(
		'name' => 'Regex',
		'options' => array(
		    'pattern' => '/^[a-zA-Z0-9\s]+$/',
		    'messages' => array(
			\Zend\Validator\Regex::NOT_MATCH => 'Please use alphanumeric characters or spaces only'
		    )
		),
		'break_chain_on_failure' => true
	    )
	);
    }

    /**
     * Method to get currency from currency
     * @return type
     */
    private function _getCurrency() {
	if ($this->_curency == null) {
	    $currency = new \Application\Entity\PhvCurrency();
	    $this->_curency = $currency->getCurrency();
	}
	return $this->_curency;
    }

    /**
     * Method to get sponsor from currency
     * @return type
     */
    private function _getSponser() {
	if ($this->_sponser == null) {
	    $companyObj = new \Application\Entity\PhvCompany();
	    $this->_sponser = $companyObj->getCompanyList(\Application\Entity\PhvCompanyType::SPONSER);
	}
	return $this->_sponser;
    }

    /**
     * Method to get status
     * 
     * @return type
     */
    private function _getStatus() {
	return array(
	    '1' => "Active",
	    '2'=> "Closed"
	);
    }

}
