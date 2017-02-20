<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_wepublication_docs'] = Array (
	'ctrl' => $TCA['tx_wepublication_docs']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'sys_language_uid,l18n_parent,l18n_diffsource,hidden,starttime,endtime,fe_group,title,magazine,year,issue,page_end,page_start,abstract,cover,url_href,url_title,doi,pacs,authors,document_1_title,document_1_file,document_2_title,document_2_file,document_3_title,document_3_file,comment'
	),
	'feInterface' => $TCA['tx_wepublication_docs']['feInterface'],
	'columns' => Array (
		'sys_language_uid' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
			'config' => array (
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages',-1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.default_value',0)
				)
			)
		),
		'l18n_parent' => Array (
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', 0),
				),
				'foreign_table' => 'tx_wepublication_docs',
				'foreign_table_where' => 'AND tx_wepublication_docs.pid=###CURRENT_PID### AND tx_wepublication_docs.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => Array (
			'config' => Array (
				'type' => 'passthrough'
			)
		),
		'hidden' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'starttime' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'default' => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'checkbox' => '0',
				'default' => '0',
				'range' => Array (
					'upper' => mktime(0,0,0,12,31,2020),
					'lower' => mktime(0,0,0,date('m')-1,date('d'),date('Y'))
				)
			)
		),
		'fe_group' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', 0),
					Array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
					Array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
					Array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
		'title' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.title',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'max' => '255',
				'eval' => 'required,trim',
			)
		),
		'magazine' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.magazine',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
			)
		),
		'magazine2' => Array (        
            'exclude' => 1,        
            'label' => 'LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.magazine2',        
            'config' => Array (
                'type' => 'select',    
                'items' => Array (
                    Array('',0),
                ),
                'foreign_table' => 'tx_wepublication_magazine',    
                'foreign_table_where' => 'ORDER BY tx_wepublication_magazine.uid',    
                'size' => 1,    
                'minitems' => 0,
                'maxitems' => 1,
            )
        ),
		'year' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.year',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'eval' => 'required,year,nospace',
			)
		),
		'status' => Array (        
            'exclude' => 1,        
            'label' => 'LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.status',        
            'config' => Array (
                'type' => 'select',
                'items' => Array (
                    Array('LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.status.I.0', '0'),
                    Array('LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.status.I.1', '1'),
                    Array('LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.status.I.2', '2'),
                    Array('LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.status.I.3', '3'),
                    Array('LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.status.I.4', '4'),
                ),
                'size' => 1,    
                'maxitems' => 1,
            )
        ),
		'issue' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.issue',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'max' => '10',
				'eval' => 'trim,nospace',
			)
		),
		'page_start' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.page_start',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'max' => '255',
				'eval' => 'trim',
			)
		),
		'page_end' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.page_end',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'max' => '255',
				'eval' => 'trim',
			)
		),
		'abstract' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.abstract',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
			)
		),
		'cover' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.cover',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => 'gif,png,jpeg,jpg',
				'max_size' => 1000,
				'uploadfolder' => 'uploads/tx_wepublication',
				'show_thumbs' => 1,
				'size' => 2,
				'minitems' => 0,
				'maxitems' => 2,
			)
		),
		'url_href' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.url_href',
			'config' => Array (
				'type' => 'input',
				'size' => '15',
				'wizards' => Array(
					'_PADDING' => 2,
					'link' => Array(
						'type' => 'popup',
						'title' => 'Link',
						'icon' => 'link_popup.gif',
						'script' => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
					),
				),
				'eval' => 'trim',
			)
		),
		'url_title' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.url_title',
			'config' => Array (
				'type' => 'input',
				'size' => '15',
				'max' => '255',
				'eval' => 'trim',
			)
		),
		'document_1_file' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.document_1_file',		
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => 'gif,png,jpeg,jpg,pdf,doc,rtf',
				'max_size' => 1000,
				'uploadfolder' => 'uploads/tx_wepublication',
//				'show_thumbs' => 1,
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,	
			)
		),
		'document_1_title' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.document_1_title',
			'config' => Array (
				'type' => 'input',
				'size' => '15',
				'max' => '255',
				'eval' => 'trim',
			)
		),
		'document_2_file' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.document_2_file',		
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => 'gif,png,jpeg,jpg,pdf,doc,rtf',
				'max_size' => 1000,
				'uploadfolder' => 'uploads/tx_wepublication',
//				'show_thumbs' => 1,
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,	
			)
		),
		'document_2_title' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.document_2_title',
			'config' => Array (
				'type' => 'input',
				'size' => '15',
				'max' => '255',
				'eval' => 'trim',
			)
		),
		'document_3_file' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.document_3_file',		
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => 'gif,png,jpeg,jpg,pdf,doc,rtf',
				'max_size' => 1000,
				'uploadfolder' => 'uploads/tx_wepublication',
//				'show_thumbs' => 1,
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,	
			)
		),
		'document_3_title' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.document_3_title',
			'config' => Array (
				'type' => 'input',
				'size' => '15',
				'max' => '255',
				'eval' => 'trim',
			)
		),
		'doi' => Array (		
			'exclude' => 1,	
			'label' => 'LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.doi',		
			'config' => Array (
				'type' => 'input',	
				'size' => '15',
				'max' => '55',	
				'eval' => 'trim',
			)
		),
		'pacs' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.pacs',		
			'config' => Array (
				'type' => 'input',
				'size' => '15',
				'max' => '255',
				'eval' => 'trim',
			)
		),
		'authors' => Array (
			'exclude' => 1,		
			'label' => 'LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.authors',		
			'config' => Array (
				'type' => 'select',	
				'foreign_table' => 'fe_users',	
				'foreign_table_where' => 'ORDER BY fe_users.uid',	
				'size' => 10,	
				'minitems' => 0,
				'maxitems' => 50,	
				'MM' => 'tx_wepublication_docs_authors_mm',	
				'wizards' => Array(
					'_PADDING' => 2,
					'_VERTICAL' => 1,
					'add' => Array(
						'type' => 'script',
						'title' => 'Create new record',
						'icon' => 'add.gif',
						'params' => Array(
							'table'=>'fe_users',
							'pid' => '###CURRENT_PID###',
							'setValue' => 'prepend'
						),
						'script' => 'wizard_add.php',
					),
					'list' => Array(
						'type' => 'script',
						'title' => 'List',
						'icon' => 'list.gif',
						'params' => Array(
							'table'=>'fe_users',
							'pid' => '###CURRENT_PID###',
						),
						'script' => 'wizard_list.php',
					),
				),
			)
		),
		
		
		/*  @todo: More flexible group-selection for authors
		
		'author_grp_external' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.author_grp_external',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'fe_groups',
				'foreign_table_where' => 'ORDER BY fe_groups.uid',
				'size' => 5,
				'minitems' => 0,
				'maxitems' => 5,
				'MM' => 'tx_wepublication_docs_author_grp_external_mm',	
				'wizards' => Array(
					'_PADDING' => 2,
					'_VERTICAL' => 1,
					'list' => Array(
						'type' => 'script',
						'title' => 'List',
						'icon' => 'list.gif',
						'params' => Array(
							'table'=>'fe_groups',
							'pid' => '###CURRENT_PID###',
						),
						'script' => 'wizard_list.php',
					),
				),
			)
		),
		'author_grp_internal' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.author_grp_internal',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'fe_groups',
				'foreign_table_where' => 'ORDER BY fe_groups.uid',
				'size' => 5,	
				'minitems' => 0,
				'maxitems' => 5,
				'MM' => 'tx_wepublication_docs_author_grp_internal_mm',	
				'wizards' => Array(
					'_PADDING' => 2,
					'_VERTICAL' => 1,
					'list' => Array(
						'type' => 'script',
						'title' => 'List',
						'icon' => 'list.gif',
						'params' => Array(
							'table'=>'fe_groups',
							'pid' => '###CURRENT_PID###',
						),
						'script' => 'wizard_list.php',
					),
				),
			)
		),
		
		
		*/
		
		'comment' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.comment',		## WOP:[tables][1][fields][14][title]
			'config' => Array (
				'type' => 'text',
				'cols' => '20',
				'rows' => '5',
			)
		),

	),
	'types' => Array (
	#	'0' => Array('showitem' => 'sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, title;;;;2-2-2, magazine;;;;3-3-3, year, status, issue, page_start, page_end, abstract, cover, url, doi, pacs, documents, authors, author_grp_external, author_grp_internal, comment;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts]')
		'0' => Array(
		  'showitem' => '
		    sys_language_uid;;;;1-1-1,
		    l18n_parent,
		    l18n_diffsource,
		    hidden;;4,
		    title;;;;1-1-1,
		    --palette--;LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.magazine_be_label;8,		     
		    --palette--;LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.details_be_label;6,		     
		    --palette--;LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.magazine_pages_be_label;5, 
		    abstract, 
		    authors, 
		    cover;;;;1-1-1, 
		    --palette--;LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.magazine_details_be_label;6, 
		    --palette--;LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.url_be_label;7,
		    doi;;;;1-1-1, 
		    pacs, 
		    --palette--;LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.document_1_be_label;1;;1-1-1, 
		    --palette--;LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.document_2_be_label;2, 
		    --palette--;LLL:EXT:we_publication/locallang_db.xml:tx_wepublication_docs.document_3_be_label;3, 
		    comment;;;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|image]:rte_transform[mode=ts];1-1-1
		  ',
		  'canNotCollapse' => '1',
    	  )
	),
	'palettes' => Array (
		'1' => Array('showitem' => 'document_1_title,document_1_file', 'canNotCollapse' => '1'),
		'2' => Array('showitem' => 'document_2_title,document_2_file', 'canNotCollapse' => '1'),
		'3' => Array('showitem' => 'document_3_title,document_3_file', 'canNotCollapse' => '1'),
	    '4' => Array('showitem' => 'starttime, endtime'),
	    '5' => Array('showitem' => 'page_start,page_end', 'canNotCollapse' => '1'),
	    '6' => Array('showitem' => 'year,issue,status', 'canNotCollapse' => '1'),
		'7' => Array('showitem' => 'url_href,url_title', 'canNotCollapse' => '1'),
		'8' => Array('showitem' => 'magazine,magazine2', 'canNotCollapse' => '1'),
	    
	)
);

$TCA['tx_wepublication_magazine'] = array (
    'ctrl' => $TCA['tx_wepublication_magazine']['ctrl'],
    'interface' => array (
        'showRecordFieldList' => 'sys_language_uid,l18n_parent,l18n_diffsource,hidden,title'
    ),
    'feInterface' => $TCA['tx_wepublication_magazine']['feInterface'],
    'columns' => array (
        'sys_language_uid' => array (        
            'exclude' => 1,
            'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
            'config' => array (
                'type'                => 'select',
                'foreign_table'       => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => array(
                    array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages', -1),
                    array('LLL:EXT:lang/locallang_general.xml:LGL.default_value', 0)
                )
            )
        ),
        'l18n_parent' => array (        
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude'     => 1,
            'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
            'config'      => array (
                'type'  => 'select',
                'items' => array (
                    array('', 0),
                ),
                'foreign_table'       => 'tx_wepublication_magazine',
                'foreign_table_where' => 'AND tx_wepublication_magazine.pid=###CURRENT_PID### AND tx_wepublication_magazine.sys_language_uid IN (-1,0)',
            )
        ),
        'l18n_diffsource' => array (        
            'config' => array (
                'type' => 'passthrough'
            )
        ),
        'hidden' => array (        
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config'  => array (
                'type'    => 'check',
                'default' => '0'
            )
        ),
        'title' => Array (        
            'exclude' => 1,        
            'label' => 'LLL:EXT:user_test/locallang_db.xml:tx_wepublication_magazine.title',        
            'config' => Array (
                'type' => 'input',    
                'size' => '30',    
                'max' => '255',    
                'eval' => 'required,trim',
            )
        ),
    ),
    'types' => array (
        '0' => array('showitem' => 'sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, title;;;;2-2-2')
    ),
    'palettes' => array (
        '1' => array('showitem' => '')
    )
);
?>