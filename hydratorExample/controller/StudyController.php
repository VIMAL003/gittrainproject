<?php

namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Application\Library\DateFunction;
use Application\Library\CustomConstantsFunction as ConstantFuntion;

class StudyController extends AbstractController {

    const CSRF_EXPIRE_TIME = 1200;

    //Set colums for display or fetch from the database
    public $columns = array(
        array('db' => 's.protocol', 'dt' => 'protocol'),
        array('db' => 'c.company_name', 'dt' => 'sponser'),
        array('db' => 'sp.cro_id', 'dt' => 'cro'),
        array('db' => 's.start_date', 'dt' => 'start_date'),
        array('db' => 's.status', 'dt' => 'status'),
    );
    public $currentUser;

    /**
     * Intialize entity and serach form
     *
     * @return void
     */
    public function __construct() {
        $this->entity = new \Application\Entity\PhvStudy();
        $this->searchForm = new \Application\Form\SearchStudyForm();
        $this->currentUser = \Application\Library\CustomConstantsFunction::getCurrentUser();
    }

    /**
     * Method to display listing
     *
     * @param null 
     * @return ViewModel
     */
    public function indexAction() {
        if ($this->getRequest()->isPost() && $this->getRequest()->isXmlHttpRequest()) {
	    $data = $this->__getData();
            echo json_encode($data);
            exit;
        } else {
            $gridSession = new \Zend\Session\Container('grid');
            $gridSession->getManager()->getStorage()->clear('grid');
        }
	$this->__gridSettings();
        return new ViewModel(array(
            'searchForm' => $this->searchForm,
            'length' => $this->__gridLength,
            'columns' => $this->__gridColumns,
            'searchGridCount' => count($this->__gridData),
            'newSearch' => $this->params('id', 0),
        ));
    }

    /**
     * Method to retrieve data in array format
     *
     * @param null
     * @return array
     */
    public function __getData() {
        $qb = $this->entity->listQuery();
	$qb = $this->setArchiveStatusQuery($qb);
        $data = $this->entity->dtGetData($this->getRequest(), $this->columns, null, $qb);
        foreach ($data['data'] as $key => $val) {
            $id= \Application\Library\CustomConstantsFunction::encryptDecrypt('encrypt', $val['action']['id']);
	    $data['data'][$key]['protocol'] .= '<input type="checkbox" class="unique hide" value="'.$id.'">';
            $data['data'][$key]['start_date'] = (isset($val['start_date'])) ? DateFunction::convertTimeToUserTime($val['start_date']) : '-';
            $studyPricingObj = $this->entity->getEntityObj($val['action']['pricingid'], 'Application\Entity\PhvStudyPricing');
            $data['data'][$key]['cro'] = $studyPricingObj->getCro()->getCompanyName();
            if (empty($data['data'][$key]['cro'])) {
                $data['data'][$key]['cro'] = '-';
            }
            $btnLabel = $this->translate('Archive');
            $textMessage = $this->translate('Are you sure you want to archive study?');
            if ($val['action']['status'] == 3) {
                $btnLabel = $this->translate('Unarchive');
                $textMessage = $this->translate('Are you sure you want to unarchive study?');
            }
            
            if ($data['data'][$key]['status'] != 2 && $this->currentUser->getRole()->getId() == 2) {
                $data['data'][$key]['action'] = "<button class='tabledit-edit-button btn btn-sm btn btn-rounded btn-inline btn-primary-outline' onclick='showModal(\"" . $id . "\",\"" . $textMessage . "\")' type='button' value='1'>" . $btnLabel . "</button>&nbsp;";
            } 
            $data['data'][$key]['status'] = $this->entity->setStatusText($val['action']['status']);
        }

        return $data;
    }
    
    /**
     * Method to prepare query
     * 
     * @param type $qb
     * @return string
     */
    public function setArchiveStatusQuery($qb) {
	$session = new \Zend\Session\Container('grid');
	if (!empty($session->postData)) {
	    if($session->postData['status'] == 3) {
		$qb->andWhere($qb->expr()->eq('s.status', '3'));
	    }else{
		$qb->andWhere($qb->expr()->neq('s.status', '3'));
	    }
	}else{
	    $qb->andWhere($qb->expr()->neq('s.status', '3'));
	}
	return $qb;
    }
    /**
     * Method to add data into study
     *
     * @return none
     */
    public function addAction() {
        $this->form = new \Application\Form\AddParentStudyForm($this->serviceLocator);
        $this->studyDataSave();
        return array('form' => $this->form, 'id' => 0, 'associateCompanyIds' => array(), 'submittedTo' => array());
    }

    /**
     * Method to update data into study
     *
     * @return none
     */
    public function editAction() {
        $id = ConstantFuntion::encryptDecrypt('decrypt', $this->params('id', 0));
        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($request->getPost('studyId') != "") {
                $studyId = ConstantFuntion::encryptDecrypt('decrypt', $request->getPost('studyId'));
                $studyObj = $this->checkValidRequest($studyId);
                if ($studyObj) {
                    $message = $this->doArchived($studyId);
                    $this->flashMessenger()->addSuccessMessage($studyObj->getProtocol() . ' ' . $message);
                    return $this->redirect()->toRoute('study', array('action' => 'index','id'=>1));
                } else {
                    $this->flashMessenger()->addErrorMessage($this->translate('Invalid Request'));
                    return $this->redirect()->toRoute('study', array('action' => 'index'));
                }
            }
        }
        $studyObj = $this->checkValidRequest($id);
        if ($studyObj) {
            $this->form = new \Application\Form\AddParentStudyForm($this->serviceLocator, $id);
            $associateCompanyIds = $this->checkExistCompanySae($id);
            $submittedTo = [];
            if (count($studyObj->getPhvSubmittedSae()) > 0) {
                foreach ($studyObj->getPhvSubmittedSae() as $data) {
                    $submittedTo[] = $data->getSubmittedTo()->getId();
                }
            }
            $this->studyDataSave();
            return array('form' => $this->form, 'id' => $id, 'associateCompanyIds' => $associateCompanyIds, 'submittedTo' => $submittedTo);
        } else {
            $this->flashMessenger()->addErrorMessage($this->translate('Invalid Request'));
            return $this->redirect()->toRoute('study', array('action' => 'index'));
        }
    }

    /**
     * Method to verify valid request
     *
     * @param study $id
     * @return type
     */
    public function checkValidRequest($id) {
        if (isset($id) && !intval($id)) {
            return FALSE;
        }
        if ($this->params('controller') == 'Application\Controller\CroStudy') {
            $studyPricingObj = new \Application\Entity\PhvStudyPricing();
            $resultCount = $studyPricingObj->verifyLogdinCro($id);
            if ($resultCount == 0) {
                return FALSE;
            }
        }
        $studyObj = $this->getStudyObj($id);
        if (count($studyObj) == 0) {
            return FALSE;
        } else {
            return $studyObj;
        }
        return TRUE;
    }

    /**
     * Method to update status of study
     *
     * @param integer $studyId
     * @return none
     * @throws \Exception
     */
    private function doArchived($studyId) {
        $studyObj = $this->getStudyObj($studyId);
        if ($studyObj->getStatus() == '1') {
            $studyObj->setStatus('3');
            $message = $this->translate('study has been archived.');
        } else {
            $studyObj->setStatus('1');
            $message = $this->translate('study has been active.');
        }
        $em = $this->getEntityManager();
        try {
            $em->persist($studyObj);
            $em->flush();
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
        return $message;
    }

    /**
     * Method to get associated company id with Phv_Sae
     *
     * @param type $studyId
     * @return array
     */
    public function checkExistCompanySae($studyId) {
        $associateCompanyIds = $this->getEntityManager()
            ->getRepository('Application\Entity\PhvStudy')
            ->getAssociSaeCompanyIds($studyId);
        return $associateCompanyIds;
    }

    /**
     * Method to get study Object
     *
     * @param type $id
     * @return object
     */
    public function getStudyObj($id = 0) {
        $studyObj = new \Application\Entity\PhvStudy();
        if ($id > 0) {
            $studyObj = $this->getEntityManager()->getRepository('Application\Entity\PhvStudy')->find($id);
        }
        return $studyObj;
    }

    /**
     * Common method to save and edit study data
     * @return type
     * @throws \Exception
     */
    public function studyDataSave() {
        $id = ConstantFuntion::encryptDecrypt('decrypt', $this->params('id', 0));
        $studyObj = $this->getStudyObj($id);
        $this->form->bind($studyObj);
        $request = $this->getRequest();

        if ($request->isPost()) {
            $this->form->setData($request->getPost());
            $postData = $request->getPost('study');
            $newCompanyIds = [];
            if ($id != 0) {
                $newCompanyIds = $this->getNewCompanyIds($postData, $studyObj);
            }
            if ($this->form->isValid()) {
                $em = $this->getEntityManager();
                try {
                    $em->persist($studyObj);
                    $em->flush();
                    if ($id == 0) {
                        $this->saveMessage($studyObj);
                       // $this->saveStudyDocument($studyObj); // this is commented as discussed with devang
                    }
		    if($studyObj->getStatus() == '2') {
			$this->updateMessage($studyObj);
		    }
                    $this->prepareMailToCompany($postData, $newCompanyIds);
                } catch (\Exception $ex) {
                    throw new \Exception($ex->getMessage());
                }
                $this->flashMessenger()->addSuccessMessage($postData['protocol'] . ' ' . $this->translate('study details has been saved.'));
		if ($id == 0) {
		    return $this->redirect()->toRoute('study', array('action' => 'index','id'=>1));
		}
		return $this->redirect()->toRoute('study', array('action' => 'index'));
            }
        }
    }

    /**
     * Method to save study id into message
     *
     * @param type $studyObj
     * @throws \Exception
     */
    public function saveMessage($studyObj) {
        $em = $this->getEntityManager();
        $messageObj = new \Application\Entity\PhvMessage();
        try {
            $messageObj->setStudy($studyObj);
            $messageObj->setCreatedDt(new \DateTime(CUR_DATE_TIME));
            $messageObj->setSubject('New study has been created');
            $em->persist($messageObj);
            $em->flush();
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }
    /**
     * Method to update status into message
     *
     * @param type $studyObj
     * @throws \Exception
     */
    public function updateMessage($studyObj) {
        $em = $this->getEntityManager();
        $messageObj = $em->getRepository('Application\Entity\PhvMessage')
			->findOneBy(array('study'=>$studyObj->getId()));
	if(count($messageObj) > 0) {
	    try {
		$messageObj->setStatus(2);

		$em->persist($messageObj);
		$em->flush();
	    } catch (\Exception $ex) {
		throw new \Exception($ex->getMessage());
	    }
	}
    }

    /**
     * Method to save study id into phvStudyDocument
     *
     * @param type $studyObj
     * @throws \Exception
     */
    public function saveStudyDocument($studyObj) {
        $em = $this->getEntityManager();
        $studyDocumentObj = new \Application\Entity\PhvStudyDocument();
        try {
            $studyDocumentObj->setStudy($studyObj);
            $studyDocumentObj->setCreatedDt(new \DateTime(CUR_DATE_TIME));
            $studyDocumentObj->setDocumentDirId(1);
            $em->persist($studyDocumentObj);
            $em->flush();
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
    }

    /**
     * Method to get new companyIds 
     * @param type $postData
     * @param type $studyObj
     * @return array
     */
    private function getNewCompanyIds($postData, $studyObj) {
        $i = 0;
        foreach ($postData['phvStudyPricing'] as $value) {
            $companyIds[$i] = $value['cro'];
            $i++;
        }
        $j = 0;
        foreach ($studyObj->getPhvStudyPricing() as $key => $studyPricingObj) {
            $studyPricingCroIds[$j] = $studyPricingObj->getCro()->getId();
            $j++;
        }
        $newCompanyIds = array_diff($companyIds, $studyPricingCroIds);
        if ($postData['sponser'] != $studyObj->getSponser()->getId()) {
            $newCompanyIds = array_values($newCompanyIds + array($postData['sponser']));
        }
        return $newCompanyIds;
    }

    /**
     * Method to get prepareemail to send
     *
     * @param type $postData
     * @param type $newCompanyIds
     */
    private function prepareMailToCompany($postData, $newCompanyIds) {
        $companyIds[0] = $postData['sponser'];
        $i = 1;
        foreach ($postData['phvStudyPricing'] as $value) {
            $companyIds[$i] = $value['cro'];
            $i++;
        }
        $id = ConstantFuntion::encryptDecrypt('decrypt', $this->params('id', 0));
        $emailTemplate = 'add-study';
        if ($id > 0) {
            $emailTemplate = 'update-study';
        }
        if (!empty($newCompanyIds)) {
            $existCompanyIds = array_diff($companyIds, $newCompanyIds);
            $this->sendMailToCompany($existCompanyIds, $postData, 'update-study');
            $this->sendMailToCompany($newCompanyIds, $postData, 'add-study');
        } else {
            $this->sendMailToCompany($companyIds, $postData, $emailTemplate);
        }
    }

    /**
     * Method to send mail
     *
     * @param type $companyIds
     * @param type $postData
     * @param type $emailTemplate
     */
    private function sendMailToCompany($companyIds, $postData, $emailTemplate) {
        $companyData = $this->getEntityManager()
            ->getRepository('Application\Entity\PhvCompany')
            ->getCompanyEmails($companyIds);
        foreach ($companyData as $companyEmail => $companyName) {
            $emailArray = array(
                'name' => $companyName,
                'protocol' => $postData['protocol'],
                'startDate' => $postData['startDate'],
                'endDate' => $postData['endDate']
            );
            $recipient['to'][0] = $companyEmail;
            $this->sendMailInQueue($emailArray, $emailTemplate, $recipient);
        }
    }

    /**
     * View study action
     * @return ViewModel
     */
    public function viewAction() {
        $id = ConstantFuntion::encryptDecrypt('decrypt', $this->params('id', 0));
        $url = $this->url()->fromRoute($this->route, array('action'=>'edit', 'id'=>$this->params('id', 0)));
        if($this->isUrlAllow($url) == true){
            return $this->redirect()->toUrl($url);
        }
        $studyObj = $this->checkValidRequest($id);
        if ($studyObj) {
            $this->form = new \Application\Form\AddParentStudyForm($this->serviceLocator, $id);
            $associateCompanyIds = $this->checkExistCompanySae($id);
            $submittedTo = [];
            if (count($studyObj->getPhvSubmittedSae()) > 0) {
                foreach ($studyObj->getPhvSubmittedSae() as $data) {
                    $submittedTo[] = $data->getSubmittedTo()->getId();
                }
            }
            $this->studyDataSave();
            return array('form' => $this->form, 'id' => $id, 'associateCompanyIds' => $associateCompanyIds, 'submittedTo' => $submittedTo);
        } else {
            $this->flashMessenger()->addErrorMessage($this->translate('Invalid Request'));
            return $this->redirect()->toRoute('study', array('action' => 'index'));
        }
    }

}
