<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
/**
 * PhvStudy
 *
 * @ORM\Table(name="phv_study", uniqueConstraints={@ORM\UniqueConstraint(name="currency", columns={"currency"})}, indexes={@ORM\Index(name="sponser_id", columns={"sponser_id"}), @ORM\Index(name="created_by", columns={"created_by"}), @ORM\Index(name="cro_id", columns={"cro_id"})})
 * @ORM\Entity(repositoryClass="\Application\Repositories\PhvStudyRepository")
 * @ORM\HasLifecycleCallbacks
 */
class PhvStudy extends Entity {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="protocol", type="string", length=255, nullable=true)
     */
    protected $protocol;
    
    /**
     * @var string
     *
     * @ORM\Column(name="drugname", type="string", length=255, nullable=true)
     */
    protected $drugname;
    
    /**
     * @var string
     *
     * @ORM\Column(name="chinese_drugname", type="string", length=255, nullable=true)
     */
    protected $chineseDrugname;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime", nullable=true)
     */
    protected $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     */
    protected $endDate;

    /**
     * @var string
     *
     * @ORM\Column(name="submit_to_cfda", type="string", nullable=false)
     */
    protected $submitToCfda = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="submit_to_cioms", type="string", nullable=false)
     */
    protected $submitToCioms = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="submit_to_usfda", type="string", nullable=false)
     */
    protected $submitToUsfda = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    protected $status = '1';

    /**
     * @var \PhvUser
     *
     * @ORM\ManyToOne(targetEntity="PhvUser")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     */
    protected $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_dt", type="datetime", nullable=true)
     */
    protected $createdDt;

    /**
     * @var PhvCompany
     *
     * @ORM\ManyToOne(targetEntity="PhvCompany")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sponser_id", referencedColumnName="id")
     * })
     */
    protected $sponser;
    
    /**
     * @var PhvCurrency
     *
     * @ORM\ManyToOne(targetEntity="PhvCurrency")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="currency", referencedColumnName="id")
     * })
     */
    protected $currency;
    
    
    /**
      * @ORM\OneToMany(targetEntity="Application\Entity\PhvStudyPricing", mappedBy="study",cascade={"persist","remove"})
      */
     protected $phvStudyPricing;
     
    /**
      * @ORM\OneToMany(targetEntity="Application\Entity\PhvSubmittedSae", mappedBy="study",cascade={"persist","remove"})
      */
     protected $phvSubmittedSae;
     
    /**
     * @ORM\ManyToMany(targetEntity="PhvCompanyPis")
     * @ORM\JoinTable(name="phv_study_pi", 
     *              joinColumns={@ORM\JoinColumn(name="study_id", referencedColumnName = "id")}, 
     *              inverseJoinColumns={@ORM\JoinColumn(name="company_pis_id", referencedColumnName="id")})
     *              inverseJoinColumns={@ORM\JoinColumn(name="company_pis_id", referencedColumnName="id")})
     */
     protected $phvCompanyPis;
    
    public function __construct(){
        $this->phvStudyPricing = new ArrayCollection();
        $this->phvSubmittedSae = new ArrayCollection();
        $this->phvCompanyPis = new ArrayCollection();
    }
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
	return $this->id;
    }

    /**
     * Set protocol
     *
     * @param string $protocol
     *
     * @return PhvStudy
     */
    public function setProtocol($protocol) {
	$this->protocol = $protocol;

	return $this;
    }

    /**
     * Get protocol
     *
     * @return string
     */
    public function getProtocol() {
	return $this->protocol;
    }
    
    /**
     * Set drugname
     *
     * @param string $drugname
     *
     * @return PhvStudy
     */
    public function setDrugname($drugname) {
	$this->drugname = $drugname;
	return $this;
    }

    /**
     * Get drugname
     *
     * @return string
     */
    public function getDrugname() {
	return $this->drugname;
    }
    
    /**
     * Set chineseDrugname
     *
     * @param string $chineseDrugname
     *
     * @return PhvStudy
     */
    public function setChineseDrugname($chineseDrugname) {
	$this->chineseDrugname = $chineseDrugname;
	return $this;
    }

    /**
     * Get drugname
     *
     * @return string
     */
    public function getChineseDrugname() {
	return $this->chineseDrugname;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return PhvStudy
     */
    public function setStartDate($startDate) {
	$this->startDate = $startDate;

	return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate() {
	return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return PhvStudy
     */
    public function setEndDate($endDate) {
	$this->endDate = $endDate;

	return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate() {
	return $this->endDate;
    }

    /**
     * Set submitToCfda
     *
     * @param boolean $submitToCfda
     *
     * @return PhvStudy
     */
    public function setSubmitToCfda($submitToCfda) {
	$this->submitToCfda = $submitToCfda;

	return $this;
    }

    /**
     * Get submitToCfda
     *
     * @return boolean
     */
    public function getSubmitToCfda() {
	return $this->submitToCfda;
    }

    /**
     * Set submitToCioms
     *
     * @param boolean $submitToCioms
     *
     * @return PhvStudy
     */
    public function setSubmitToCioms($submitToCioms) {
	$this->submitToCioms = $submitToCioms;

	return $this;
    }

    /**
     * Get submitToCioms
     *
     * @return boolean
     */
    public function getSubmitToCioms() {
	return $this->submitToCioms;
    }

    /**
     * Set submitToUsfda
     *
     * @param boolean $submitToUsfda
     *
     * @return PhvStudy
     */
    public function setSubmitToUsfda($submitToUsfda) {
	$this->submitToUsfda = $submitToUsfda;

	return $this;
    }

    /**
     * Get submitToUsfda
     *
     * @return boolean
     */
    public function getSubmitToUsfda() {
	return $this->submitToUsfda;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return PhvStudy
     */
    public function setStatus($status) {
	
	$this->status = $status;

	return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus() {
	return $this->status;
    }

    /**
     * Set createBy
     *
     * @param PhvUser $createdBy
     *
     * @return PhvStudy
     */
    public function setCreateBy(PhvUser $createdBy = null) {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return PhvUser
     */
    public function getCreateBy() {
        return $this->createdBy;
    }
    /**
     * Set createdDt
     *
     * @param \DateTime $createdDt
     *
     * @return PhvStudy
     */
    public function setCreatedDt($createdDt) {
	$this->createdDt = $createdDt;

	return $this;
    }

    /**
     * Get createdDt
     *
     * @return \DateTime
     */
    public function getCreatedDt() {
	return $this->createdDt;
    }

    /**
     * Set sponser
     *
     * @param PhvCompany $sponser
     *
     * @return PhvStudy
     */
    public function setSponser(PhvCompany $sponser = null) {
	$this->sponser = $sponser;

	return $this;
    }

    /**
     * Get sponser
     *
     * @return PhvCompany
     */
    public function getSponser() {
	return $this->sponser;
    }

    /**
     * Set currency
     *
     * @param PhvCurrency $currency
     *
     * @return PhvStudy
     */
    public function setCurrency(PhvCurrency $currency = null) {
	$this->currency = $currency;

	return $this;
    }

    /**
     * Get currency
     *
     * @return PhvCurrency
     */
    public function getCurrency() {
	return $this->currency;
    }
    
    /**
    * @param array $phvStudyPricing
    * @return PhvStudyPricing
    */
    public function setPhvStudyPricing($phvStudyPricing) {
	foreach ($phvStudyPricing as $child) {
	   $child->setStudy($this);
	   $this->phvStudyPricing->add($child);
       }
       return $this;
    }

    /**
     * @return array
     */
    public function getPhvStudyPricing() {
       return $this->phvStudyPricing;
    }
    /**
    * @param array $phvCompanyPis
    * @return PhvCompanyPis
    */
    public function setPhvCompanyPis(PhvCompanyPis $phvCompanyPis) {
	$this->phvCompanyPis = $phvCompanyPis;
	return $this;
    }

    /**
     * @return array
     */
    public function getPhvCompanyPis() {
       return $this->phvCompanyPis;
    }
    
    /**
    * @param array $phvSubmittedSae
    * @return PhvSubmittedSae
    */
    public function setPhvSubmittedSae($phvSubmittedSae) {
	foreach ($phvSubmittedSae as $child) {
	   $child->setStudy($this);
	   $this->phvSubmittedSae->add($child);
       }
       return $this;
    }

    /**
     * @return array
     */
    public function getPhvSubmittedSae() {
       return $this->phvSubmittedSae;
    }
    
    /**
    * Get an array copy of object
    *
    * @return array
    */
    public function getArrayCopy() {
        return get_object_vars($this);
    }
    /**
     * @param Collection $children
     */
    public function addPhvStudyPricing(Collection $phvStudyPricing) {
        foreach ($phvStudyPricing as $child) {
            $child->setStudy($this);
            $this->phvStudyPricing->add($child);
        }
    }

   /**
     * @param Collection $children
     */
    public function removePhvStudyPricing(Collection $phvStudyPricing) {
        foreach ($phvStudyPricing as $child) {
            $child->setStudy(null);
            $this->phvStudyPricing->removeElement($child);
        }
    }
    /**
     * @param Collection $children
     */
    public function addPhvCompanyPis(Collection $phvCompanyPis) {
	
        foreach ($phvCompanyPis as $child) {
            $this->phvCompanyPis->add($child);
        }
    }
    
   /**
     * @param Collection $children
     */
    public function removePhvCompanyPis(Collection $phvCompanyPis) {
	$currentUser = $this->getCurrentUser();
        $currentCompanyId = $currentUser->getCompany()->getId();
        foreach ($phvCompanyPis as $child) {
	    if($child->getCompany()->getId() == $currentCompanyId) {
		$this->phvCompanyPis->removeElement($child);
	    }
        }
    }
    /**
     * Method to fetch data from db table
     * 
     * @return \Doctrine\ORM\QueryBuilder
    */
    public function listQuery() {
        $qb = new \Doctrine\ORM\QueryBuilder($this->getEntityManager());
        $companyObj = new PhvCompany();
        $studyPricingObj = new PhvStudyPricing();
        $qb->select('s.*', 'c.company_name','sp.id as pricingid', 'sp.cro_id')
                ->from($studyPricingObj->getTableName(), 'sp')
                ->leftJoin($this->getTableName(), 's', "ON", 's.id=sp.study_id')
                ->leftJoin($companyObj->getTableName(), 'c', "ON", 'c.id=s.sponser_id');
	
        return $qb;
    }
    
    /**
     * Get All Assigned study
     * 
     * return $study
     */
    public function getStudy() {
        $em = $this->getEntityManager();
        $studyArr = $em->getRepository(get_class($this))->findBy(array('status'=>1));
        $study = array();
        foreach ($studyArr as $val){     
           $study[$val->getId()] = $val->getProtocol();
        }
        return $study;
    } 
    
    /**
     * Get All CRO Assigned study
     * 
     * @param integer $studyId 
     * return $croList
     */
    public function getCroList($studyId) {
	$studyPricingObj = new PhvStudyPricing();
        $em = $this->getEntityManager();
        $studyPricingArr = $em->getRepository(get_class($studyPricingObj))->findBy(['study'=>$studyId]);
        $croList = [];
        foreach ($studyPricingArr as $val){
	    if(sizeof($croList) < 3){
		$croList[] = $val->getCro()->getCompanyName();
	    }else{
		break;
	    }
        }
        return $croList;
    } 
    
    /**
     * 
     * @param integer $status
     * @return string
     */
    public function setStatusText($status) {
	switch ($status) {
	    case 1 : return 'Active';
	    case 2 : return 'Closed';
	    case 3 : return 'Archived';
	    default : return 'N/A';
		
	}
    }
    /**
     * Method to get count of study for protocol
     * 
     * @param string $protocol
     * @param integer $id
     * @return integer
     */
    public function checkExistProtocol($protocol = null, $id=null) {
	$studyCount = $this->getEntityManager()
		->getRepository('Application\Entity\PhvStudy')
		->existProtocol($protocol, $id);
	return $studyCount;
    }

    /**
     * @ORM\PrePersist 
     */
    public function prePersist() {
        $this->createdDt = new \DateTime(CUR_DATE_TIME);
        $this->createdBy = $this->getCurrentUser();
    }
    
    /**
     * Method to get data from DB
     * 
     * @param type $fields
     * @return type
     */
    public function getSelectData($fields) {
	$qb = new \Doctrine\ORM\QueryBuilder($this->getEntityManager());
	if($fields == 'sponser_id'){
	    $companyObj = new PhvCompany();
	    $query = $qb->select('s.sponser_id', 'c.company_name')
			->from($this->getTableName(), 's')
			->leftJoin($companyObj->getTableName(), 'c', "ON", 'c.id=s.sponser_id');
	    $fields = 'company_name';
	}else {
	    $query = $qb->select($fields)
                ->from($this->getTableName(), 's');
	}
	$result = $this->getEntityManager()->getConnection()->executeQuery($query)->fetchAll();
        $tempArr = array();
        foreach ($result as $val) {
            $tempArr[$val[$fields]] = $val[$fields];
        }
        return $tempArr;
    }
    
    /**
     * Get All PI company Assigned study
     * 
     * @param type $studyId
     * @param type $companyId
     * @return type
     */
    public function getPiCompanyList($studyId, $companyId) {
	$studyPiObj = new PhvStudyPi();
	$companyObj = new PhvCompany();
	$companyPiObj = new PhvCompanyPis();
	$query = new \Doctrine\ORM\QueryBuilder($this->getEntityManager());
	$query = $query->select('cp.id as compPid','sp.id', 'c.company_name')
			->from($studyPiObj->getTableName(), 'sp')
			->Join($companyPiObj->getTableName(), 'cp', "ON", 'cp.id=sp.company_pis_id')
			->leftJoin($companyObj->getTableName(), 'c', "ON", 'c.id=cp.pi_id')
			->where($query->expr()->eq('sp.study_id', $studyId))
			->andWhere($query->expr()->eq('cp.company_id', $companyId));
	
	$result = $this->getEntityManager()->getConnection()->executeQuery($query)->fetchAll();
        $tempArr = array();
	if(count($result) > 0) {
	    foreach ($result as $val) {
		$tempArr[] = $val['company_name'];
	    }
	}
        return $tempArr;
    }
    
    /**
    * Get Study to which it is submitted
    * 
    * @param integer $studyId 
    * return $assinged study list
    */
    public function getStudySubmittedTo($studyId) {
	$em = $this->getEntityManager();
        $studyArr = $em->getRepository(get_class($this))->findBy(array('id'=>$studyId, 'status'=>1));
        $study = array();
        foreach ($studyArr as $val){
           
            if($val->getSubmitToCfda() !=0) {
                $study['submit_to_cfda'] = $val->getSubmitToCfda();
            }
            if($val->getSubmitToCioms() != 0) {
                $study['submit_to_cioms'] = $val->getSubmitToCioms();
            }
            if($val->getSubmitToUsfda() != 0) {
                $study['submit_to_usfda'] = $val->getSubmitToUsfda();
            }
        }
        return $study;
    }
    
    public function getStudySettings( $studyId ) {
        
        if( empty($studyId))
            return;
        
        
    }
    
    public function getStudySetting($studyId, $key ) {
        $objProtocolDatetimeFormat = new PhvProtocolDatetimeFormat();
        $setting =  $objProtocolDatetimeFormat->getStudyDateFormat($studyId);
//        pr($setting);echo count($setting);
        if( count($setting) > 0 ){ 
          //  echo "1"; exit;
            // pr($setting);   exit;
            return $setting;
        } else {
            //echo "2"; exit;
            $objDefaulsetting = new PhvProtocolDefaultSetting();
            $setting = $objDefaulsetting->getDefaultSettings();
            if( $key == 'date_time_format' ){
                $setting = array(0=>array($key=>$setting[0]['option_value'], 'timezone'=>$setting[3]['option_value']));
            }           
            return $setting;
        }        
    }
}   

