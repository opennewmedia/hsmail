<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'ONM.Hsmail',
            'Form',
            'LLL:EXT:hsmail/Resources/Private/Language/locallang_db.xlf:tx_hsmail_form.name'
        );

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('hsmail', 'Configuration/TypoScript', 'Hsmail');
        if(TYPO3\CMS\Core\Utility\VersionNumberUtility::getNumericTypo3Version() < 10) {
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
                'ONM.Hsmail',
                'web', // Make module a submodule of 'web'
                'newsletters', // Submodule key
                '', // Position
                ['Form' => 'newsletter, addNewForms'],
                [
                    'access' => 'user,group',
                    'icon'   => 'EXT:hsmail/Resources/Public/Icons/newsletter.png',
                    'labels' => 'LLL:EXT:hsmail/Resources/Private/Language/locallang_mod.xlf',
                ]
            );
        } else {
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
                'Hsmail',
                'web', // Make module a submodule of 'web'
                'newsletters', // Submodule key
                '', // Position
                [\ONM\Hsmail\Controller\FormController::class => 'newsletter, addNewForms'],
                [
                    'access' => 'user,group',
                    'icon'   => 'EXT:hsmail/Resources/Public/Icons/newsletter.png',
                    'labels' => 'LLL:EXT:hsmail/Resources/Private/Language/locallang_mod.xlf',
                ]
            );
        }

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_hsmail_domain_model_formconfig', 'EXT:hsmail/Resources/Private/Language/locallang_csh_tx_hsmail_domain_model_formconfig.xlf');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_hsmail_domain_model_formconfig');

    }
);
