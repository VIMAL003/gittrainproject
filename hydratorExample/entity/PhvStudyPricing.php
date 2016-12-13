<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
/**
 * PhvStudyPricing
 *
 * @ORM\Table(name="phv_study_pricing", indexes={@ORM\Index(name="study_id", columns={"study_id"}), @ORM\Index(name="currency_id", columns={"currency_id"}), @ORM\Index(name="cro_id", columns={"cro_id"})})
 * @ORM\Entity
 */
class PhvStudyPricing extends Entity {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var float
     *
     * @ORM\Column(name="cfda_price", type="float", precision=10, scale=0, nullable=true)
     */
    protected $cfdaPrice;

    /**
     * @var float
     *
     * @ORM\Column(name="us_fda_prize", type="float", precision=10, scale=0, nullable=true)
     */
    protected $usFdaPrize;

    /**
     * @var float
     *
     * @ORM\Column(name="cioms_prize", type="float", precision=10, scale=0, nullable=true)
     */
    protected $ciomsPrize;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=false)
     */
    protected $status = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */
    protected $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    protected $createdOn;

    /**
     * @var \PhvCompany
     *
     * @ORM\ManyToOne(targetEntity="PhvCompany")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cro_id", referencedColumnName="id")
     * })
     */
    protected $cro;

    /**
     * @var \PhvStudy
     *
     * @ORM\ManyToOne(targetEntity="PhvStudy" , inversedBy="PhvStudyPricing")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="study_id", referencedColumnName="id")
     * })
     */
    protected $study;

    /**
     * @var \PhvCurrency
     *
     * @ORM\ManyToOne(targetEntity="PhvCurrency")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
     * })
     */
    protected $currency;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
	return $this->id;
    }

    /**
     * Set cfdaPrice
     *
     * @param float $cfdaPrice
     *
     * @return PhvStudyPricing
     */
    public function setCfdaPrice($cfdaPrice) {
	$this->cfdaPrice = $cfdaPrice;

	return $this;
    }

    /**
     * Get cfdaPrice
     *
     * @return float
     */
    public function getCfdaPrice() {
	return $this->cfdaPrice;
    }

    /**
     * Set usFdaPrize
     *
     * @param float $usFdaPrize
     *
     * @return PhvStudyPricing
     */
    public function setUsFdaPrize($usFdaPrize) {
	$this->usFdaPrize = $usFdaPrize;

	return $this;
    }

    /**
     * Get usFdaPrize
     *
     * @return float
     */
    public function getUsFdaPrize() {
	return $this->usFdaPrize;
    }

    /**
     * Set ciomsPrize
     *
     * @param float $ciomsPrize
     *
     * @return PhvStudyPricing
     */
    public function setCiomsPrize($ciomsPrize) {
	$this->ciomsPrize = $ciomsPrize;

	return $this;
    }

    /**
     * Get ciomsPrize
     *
     * @return float
     */
    public function getCiomsPrize() {
	return $this->ciomsPrize;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return PhvStudyPricing
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
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return PhvStudyPricing
     */
    public function setCreatedBy($createdBy) {
	$this->createdBy = $createdBy;

	return $this;
    }

    /**
     * Get createdBy
     *
     * @return integer
     */
    public function getCreatedBy() {
	return $this->createdBy;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return PhvStudyPricing
     */
    public function setCreatedOn($createdOn) {
	$this->createdOn = $createdOn;

	return $this;
    }

    /**
     * Get createdOn
     *
     * @return \DateTime
     */
    public function getCreatedOn() {
	return $this->createdOn;
    }

    /**
     * Set cro
     *
     * @param PhvCompany $cro
     *
     * @return PhvStudyPricing
     */
    public function setCro(PhvCompany $cro = null) {
	$this->cro = $cro;

	return $this;
    }

    /**
     * Get cro
     *
     * @return \PhvCompany
     */
    public function getCro() {
	return $this->cro;
    }

    /**
     * Set study
     *
     * @param PhvStudy $study
     *
     * @return PhvStudyPricing
     */
    public function setStudy(PhvStudy $study = null) {
	return $this->study = $study;

	//return $this;
    }

    /**
     * Get study
     *
     * @return \PhvStudy
     */
    public function getStudy() {
	return $this->study;
    }

    /**
     * Set currency
     *
     * @param PhvCurrency $currency
     *
     * @return PhvStudyPricing
     */
    public function setCurrency(PhvCurrency $currency = null) {
	$this->currency = $currency;

	return $this;
    }

    /**
     * Get currency
     *
     * @return \PhvCurrency
     */
    public function getCurrency() {
	return $this->currency;
    }
    /**
     * Method to get data from DB
     * 
     * @param type $fields
     * @return type
     */
    public function getSelectData($fields, $parameter="") {
	$qb = new \Doctrine\ORM\QueryBuilder($this->getEntityManager());
	$companyObj = new PhvCompany();
	$studyObj = new PhvStudy();
	$query = $qb->select('s.id', 'c.company_name','c.id as comId')
		    ->from($this->getTableName(), 's')
		    ->leftJoin($companyObj->getTableName(), 'c', "ON", 'c.id=s.cro_id');
	if($parameter != "") {
	    $currentUser = $this->getCurrentUser();
	    $query->leftJoin($studyObj->getTableName(), 'st', "ON", 'st.id=s.study_id')
		->where($query->expr()->like('c.company_name', $query->expr()->literal('%' . $parameter . '%')))
		->andWhere($query->expr()->eq('st.sponser_id', intval($currentUser->getCompany()->getId())));
	}
	$result = $this->getEntityManager()->getConnection()->executeQuery($query)->fetchAll();
        $tempArr = array();
        foreach ($result as $val) {
	   
            $tempArr[$val['comId']] = $val['company_name'];
        }
        return array_unique($tempArr);
    }
    
    /**
     *Method to set data for search CRO
     *  
     * @param type $parameter
     */
    public function getSearchCro($parameter) {
	return $this->getSelectData('cro_id',$parameter);
    }
    
    /**
     * Method to verify logdin CRO study display
     * 
     * @param study $id
     */
    public function verifyLogdinCro($id) {
	$currentUser = $this->getCurrentUser();
	$query = $this->getEntityManager()->createQueryBuilder();
	$query->select(array("sp.id"))
                ->from($this->getTableName(), "sp")
		->where($query->expr()->eq('sp.cro_id', intval($currentUser->getCompany()->getId())))
		->andWhere($query->expr()->eq('sp.study_id', intval($id)));
	$result = $this->getEntityManager()->getConnection()->executeQuery($query)->fetchAll();
	return count($result);
			
    }
}
