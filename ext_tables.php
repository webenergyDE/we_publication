<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_extMgm::allowTableOnStandardPages('tx_wepublication_docs');
t3lib_extMgm::addToInsertRecords('tx_wepublication_docs');

$TCA['tx_wepublication_docs'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'versioningWS' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l18n_parent',
		'transOrigDiffSourceField' => 'l18n_diffsource',
		'sortby' => 'sorting',
		'delete' => 'deleted',
		'enablecolumns' => Array (
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_wepublication_docs.gif',
	),
	/*  @todo: More flexible group-selection for authors
	'feInterface' => Array (
		'fe_admin_fieldList' => 'sys_language_uid, l18n_parent, l18n_diffsource, hidden, starttime, endtime, fe_group, title, magazine, year, status, issue, page_start, page_end, abstract, cover, url, documents, doi, pacs, authors, author_grp_external, author_grp_internal, comment',
	)
	*/
	'feInterface' => Array (
		'fe_admin_fieldList' => 'sys_language_uid, l18n_parent, l18n_diffsource, hidden, starttime, endtime, fe_group, title, magazine, year, status, issue, page_start, page_end, abstract, cover, url_href, url_title, document_1_file, document_1_title, document_2_file, document_2_title, document_3_file, document_3_title, doi, pacs, authors, comment',
	)
);
$TCA['tx_wepublication_magazine'] = array (
    'ctrl' => array (
        'title'     => 'LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_magazine',
        'label'     => 'title',
        'tstamp'    => 'tstamp',
        'crdate'    => 'crdate',
        'cruser_id' => 'cruser_id',
        'languageField'            => 'sys_language_uid',
        'transOrigPointerField'    => 'l18n_parent',
        'transOrigDiffSourceField' => 'l18n_diffsource',
        'sortby' => 'sorting',
        'delete' => 'deleted',
        'enablecolumns' => array (
            'disabled' => 'hidden',
        ),
        'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
        'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_wepublication_magazine.gif',
    ),
    'feInterface' => array (
        'fe_admin_fieldList' => 'sys_language_uid, l18n_parent, l18n_diffsource, hidden, title',
    )
);

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';

// you add pi_flexform to be renderd when your plugin is shown
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';

t3lib_extMgm::addPlugin(Array('LLL:EXT:we_publication/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');
t3lib_extMgm::addStaticFile($_EXTKEY,'pi1/static/','Publications');

// now, add your flexform xml-file
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY . '/flexform_ds_pi1.xml');

if (TYPO3_MODE=='BE') {
    $TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_wepublication_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_wepublication_pi1_wizicon.php';
}
?>