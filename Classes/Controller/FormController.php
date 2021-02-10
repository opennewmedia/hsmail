<?php
namespace ONM\Hsmail\Controller;
use TYPO3\CMS\Extbase\Annotation as Extbase;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use ONM\Hsmail\Utility\HotelsuiteApi;
use ONM\Hsmail\Domain\Model\Formconfig;
use \TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2021 Usman Ahmad <ua@onm.de>, Open New Media GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * FormController
 */
class FormController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
	 * whether or not the applicant has agreed to the privacy agreement
	 *
	 * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
	 */
    protected $cObj;

    /**
	 *
	 * @var \ONM\Hsmail\Domain\Repository\FormconfigRepository
     * @Extbase\Inject
	 */
    protected $formconfigRepository;

        /**
     * initializeAction
     *
     * @return void
     */
    public function initializeAction()
    {
        $this->cObj = $this->configurationManager->getContentObject();

        /** @var $logger \TYPO3\CMS\Core\Log\Logger */
        $this->logger = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Log\LogManager::class)->getLogger(__CLASS__);
    }

    /**
     * action index
     *
     * @return void
     */
    public function indexAction()
    {
        $this->view->assign('form', $this->formconfigRepository->findByUid($this->cObj->data['tx_hsmail_form']));
        $this->view->assign('mode', $this->cObj->data['tx_hsmail_form_mode']);
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('hsmail');
        $this->view->assign('instance', $extensionConfiguration['instance']);
    }

    /**
     * action newsletter
     *
     * @return void
     */
    public function newsletterAction()
    {
        $pid = $_GET['id'];
        $forms = $this->formconfigRepository->findByPid((int)$pid);
        $this->view->assign('forms', $forms);
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('hsmail');
        $this->view->assign('config', $extensionConfiguration);
    }
    
    /**
     * addNewFormsAction
     *
     * @return void
     */
    public function addNewFormsAction()
    {
        $forms = $this->fetchFormsFromAPI();
        $numberOfNewObjects = 0;
        foreach($forms as $form) {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tx_hsmail_domain_model_formconfig')->createQueryBuilder();
            $count = $queryBuilder
            ->count('uid')
            ->from('tx_hsmail_domain_model_formconfig')
            ->where(
                $queryBuilder->expr()->eq('id', $queryBuilder->createNamedParameter($form->id)),
            )
            ->execute()
            ->fetchColumn(0);
            if($count == 0) {
                $affectedRows = $queryBuilder
                ->insert('tx_hsmail_domain_model_formconfig')
                ->values([
                    'id' => $form->id,
                    'random_id' => $form->random_id,
                    'title' => $form->title,
                    'pid' => $_GET['id']
                ])
                ->execute();
                $numberOfNewObjects++;
            }
        }
        $this->addFlashMessage($numberOfNewObjects . ' ' . \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('module.saved.notice', 'hsmail'));
        $this->redirect('newsletter');
    }
    
    /**
     * fetchFormsFromAPI
     *
     * @return array $forms
     */
    protected function fetchFormsFromAPI()
    {
        $forms = [];
        $api = new HotelsuiteApi();
        $data = $api->fetch('/netupdater_mail/forms');
        $data = json_decode($data);
        if($data->code == 200) {
            $forms = $data->data;
        }
        return $forms;
    }
}