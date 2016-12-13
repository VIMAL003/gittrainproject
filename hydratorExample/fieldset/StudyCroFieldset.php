<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Form\Fieldset;

use Application\Entity\PhvStudyPricing;
use Zend\Form\Fieldset,
    Zend\InputFilter\InputFilterProviderInterface,
    Zend\ServiceManager\ServiceManager;
 use DoctrineORMModule\Stdlib\Hydrator\DoctrineEntity as DoctrineHydrator;

class StudyCroFieldset extends Fieldset implements InputFilterProviderInterface {

    protected $_curency = null;
    protected $inputFilter;
    private $em;
    public function __construct(ServiceManager $serviceManager) {
       //parent::__construct($name, $options);
	parent::__construct('phv_study_pricing');
	$this->em = $serviceManager->get('Doctrine\ORM\EntityManager');
	$this->setHydrator(new DoctrineHydrator($this->em, 'Application\Entity\PhvStudyPricing'))
             ->setObject(new PhvStudyPricing());
	 
//	$this->setLabel('Category');
	
        $this->add(array(
            'name' => 'cro',
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'attributes' => array(
                'id' => 'cro_id_0',
                'class' => 'form-control croStudy select select2',
                //'required' => true
            ),
            'options' => array(
                'label' => 'CRO Name',
		'empty_option' => 'Select CRO',
                'options' => $this->_getCro(),
		'object_manager' => $this->em,
		'target_class' => 'Application\Entity\PhvStudyPricing',
            )
        ));
        $this->add(array(
            'name' => 'currency',
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'attributes' => array(
                'id' => 'currency_id_0',
                'class' => 'form-control croCurrency',
               // 'required' => true,
		'placeholder' => 'Currency'
            ),
            'options' => array(
                'label' => 'Currency',
		'empty_option' => 'Select Currency',
                'options' => $this->_getCurrency(),
		'object_manager' => $this->em,
		'target_class' => 'Application\Entity\PhvStudyPricing',
            )
        ));
	$this->add(array(
            'name' => 'cfdaPrice',
            'type' => 'text',
            'attributes' => array(
                'id' => 'cfda_price',
                'class' => 'form-control maxlength-simple credit_limit',
                'placeholder' => 'CFDA',
		'maxlength' => 7
            ),
            'options' => array(
                'label' => 'CFDA',
            )
        ));
	$this->add(array(
            'name' => 'usFdaPrize',
            'type' => 'text',
            'attributes' => array(
                'id' => 'us_fda_prize',
                'class' => 'form-control maxlength-simple mininum_balance_limit',
                'placeholder' => 'US FDA',
		'maxlength' => 7
            ),
            'options' => array(
                'label' => 'US FDA',
            ),
        ));
        $this->add(array(
            'name' => 'ciomsPrize',
            'type' => 'text',
            'attributes' => array(
                'id' => 'cioms_prize',
                'class' => 'form-control maxlength-simple credit_limit',
                'placeholder' => 'CIOMS',
		'maxlength' => 7
            ),
            'options' => array(
                'label' => 'CIOMS',
            )
        ));
    }
    
    /**
     * Method to get cro
     * 
     * @return array
     */
    private function _getCro() {
        if ($this->_curency == null) {
            $companyObj = new \Application\Entity\PhvCompany();
            $cro = $companyObj->getCroList();
        }
        return $cro;
    }
    /**
     * Method to get currency
     * 
     * @return array
     */
    private function _getCurrency() {
        if ($this->_curency == null) {
            $currency = new \Application\Entity\PhvCurrency();
            $this->_curency = $currency->getCurrency();
        }
        return $this->_curency;
    }
    
    /**
     * Method to verify Same CRO
     * 
     * @return boolean
     */
    private function checkExistCro($cro) {
	$postData = $_POST['study'];
	$companyIds = [];
	foreach ($postData['phvStudyPricing'] as $key => $val) {
	    $companyIds[] = $val['cro'];
	}
	$croKeyCount = array_count_values($companyIds);
	if($croKeyCount[$cro] >= 2) {
	    return FALSE;
	}
	return TRUE;
    }

    public function getInputFilterSpecification() {
	
	return array(
             'cro' => array(
		'required' => true,
		'validators' => array( $this->setNotEmptyValidation('CRO', 'select'),
		    array(
			'name' => 'Callback',
			'break_chain_on_failure' => true,
			'options' => array(
			    'messages' => array(
				\Zend\Validator\Callback::INVALID_VALUE => 'You can\'t select more than one same CRO.',
			    ),
			    'Callback' => function($cro, $context = array()) {
				return $this->checkExistCro($cro);
			    }
			),
			
		   )
	       ),
             ),
             'currency' => array(
		'required' => true,
		'validators' => array( 
		    $this->setNotEmptyValidation('currency', 'select'),
		    array(
			'name' => 'Digits',
			'break_chain_on_failure' => true,
			'options' => array(
			    'messages' => array(
				\Zend\Validator\Digits::NOT_DIGITS => 'Please enter digit only',
			    ),
			),
		    ),
		    array(
			'name' => 'Callback',
			'options' => array(
			    'messages' => array(
				\Zend\Validator\Callback::INVALID_VALUE => 'Please select valid currency',
			    ),
			    'Callback' => function($currency, $context = array()) {
				$cro_id = $context['cro'];
				$companyCurrencyObj = new \Application\Entity\PhvCompanyCurrencyCredit();
				$currencyData = $companyCurrencyObj->getCompanyCurrency($cro_id);
				if (isset($currencyData) && in_array($currencyData[$currency], $currencyData)) {
				    return true;
				}
				return false;
			    }
			),
		    )
		),
             ),
             'cfdaPrice' => array(
		    'required' => true,
		    'validators' => array(
			$this->setNotEmptyValidation('CFDA price', 'enter'),
			array(
			    'name' => 'Float',
			    'break_chain_on_failure' => true,
			    'options' => array(
				'messages' => array(
				    \Zend\I18n\Validator\Float::NOT_FLOAT => 'Please enter decimal only'
				),
			    ),
			)
		    )
             ),
             'usFdaPrize' => array(
		    'required' => true,
		    'validators' => array( 
			$this->setNotEmptyValidation('US FDA price'),
			array(
			    'name' => 'Float',
			    'break_chain_on_failure' => true,
			    'options' => array(
				'messages' => array(
				    \Zend\I18n\Validator\Float::NOT_FLOAT => 'Please enter decimal only'
				),
			    ),
			)
		   ),
             ),
             'ciomsPrize' => array(
                 'required' => true,
		 'validators' => array( 
			$this->setNotEmptyValidation('CIOMS price'),
			array(
			    'name' => 'Float',
			    'break_chain_on_failure' => true,
			    'options' => array(
				'messages' => array(
				    \Zend\I18n\Validator\Float::NOT_FLOAT => 'Please enter decimal only'
				),
			    ),
			)
		     ),
             ),
             
         );
        
    }
    private function setNotEmptyValidation($label=null, $subLabel="enter") {
	
	    return array(
		   'name' => 'NotEmpty',
		   'options' => array(
		       'messages' => array(
			   \Zend\Validator\NotEmpty::IS_EMPTY => 'Please '.$subLabel." ".$label
		       )
		   ),
		   'break_chain_on_failure' => true
	    );
	   
    }
}
