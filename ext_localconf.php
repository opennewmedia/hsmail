<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'ONM.Hsmail',
            'Form',
            [
                'Form' => 'index'
            ],
            // non-cacheable actions
            [
                
            ],
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
        );

        // wizards
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
            'mod {
                wizards.newContentElement.wizardItems.forms {
                    elements {
                        form {
                            iconIdentifier = hsmail-form
                            title = LLL:EXT:hsmail/Resources/Private/Language/locallang_db.xlf:tx_hsmail_form.name
                            description = LLL:EXT:hsmail/Resources/Private/Language/locallang_db.xlf:tx_hsmail_form.description
                            tt_content_defValues {
                                CType = hsmail_form
                            }
                        }
                    }
                    show = *
                }
           }'
        );
		$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
		
        $iconRegistry->registerIcon(
            'hsmail-form',
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:hsmail/Resources/Public/Icons/newsletter.svg']
        );
        
        // Custom log for hsmail
        $GLOBALS['TYPO3_CONF_VARS']['LOG']['ONM']['Hsmail']['Controller']['writerConfiguration'] = [
            // configuration for WARNING severity, including all
            // levels with higher severity (ERROR, CRITICAL, EMERGENCY)
            \TYPO3\CMS\Core\Log\LogLevel::INFO => [
                // add a FileWriter
                \TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
                    // configuration for the writer
                    'logFile' => 'typo3temp/var/log/typo3_hsmail.log'
                ]
            ],
        ];
		
    }
);
