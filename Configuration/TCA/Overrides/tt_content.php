<?php
defined('TYPO3_MODE') or die();


call_user_func(function () {

    $extensionName = 'hsmail';

    $temporaryColumn = [
        'tx_hsmail_form' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:hsmail/Resources/Private/Language/locallang_db.xlf:tx_hsmail_form',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['Hotelsuite Forms', '--div--'],
                ],
                'foreign_table' => 'tx_hsmail_domain_model_formconfig',
            ],
        ],
        'tx_hsmail_form_mode' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:hsmail/Resources/Private/Language/locallang_db.xlf:tx_hsmail_form_mode',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => '0',
                'items' => [
                    ['LLL:EXT:hsmail/Resources/Private/Language/locallang_db.xlf:tx_hsmail_form_mode', '--div--'],
                    ['LLL:EXT:hsmail/Resources/Private/Language/locallang_db.xlf:tx_hsmail_form_mode.0', '0'],
                    ['LLL:EXT:hsmail/Resources/Private/Language/locallang_db.xlf:tx_hsmail_form_mode.1', '1'],
                ]
            ]
        ]
    ];

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
        'tt_content',
        $temporaryColumn
    );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
        'tt_content',
        'tx_hsmail_form,tx_hsmail_form_mode',
        'hsmail_form'
    );

    $frontendLanguageFilePrefix = 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:';

    // Configure the default backend fields for the content element
    $GLOBALS['TCA']['tt_content']['types']['hsmail_form'] = [
        'showitem' => '
    --palette--;' . $frontendLanguageFilePrefix . 'palette.general;general,
    --palette--;' . $frontendLanguageFilePrefix . 'palette.header;header,
    tx_hsmail_form,tx_hsmail_form_mode,
    --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.frames;frames,--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.appearanceLinks;appearanceLinks,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,--palette--;;language,
    --div--;' . $frontendLanguageFilePrefix . 'tabs.access,
    hidden;' . $frontendLanguageFilePrefix . 'field.default.hidden,
    --palette--;' . $frontendLanguageFilePrefix . 'palette.access;access,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
    --div--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_category.tabs.category,categories,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,rowDescription,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended'
    ];

    $GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['hsmail_form'] = 'hsmail-form';

});
