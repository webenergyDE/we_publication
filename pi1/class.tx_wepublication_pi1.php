<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Julian Hofmann <typo3.ext.YYYY@webenergy.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
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
 * Plugin 'Publications' for the 'we_publication' extension.
 *
 * @version 1.0.0 (2007-05-05)
 * @author	Julian Hofmann <typo3.ext.YYYY@webenergy.de>
 */


require_once(PATH_tslib.'class.tslib_pibase.php');

class tx_wepublication_pi1 extends tslib_pibase {
    var $cObj; // The backReference to the mother cObj object set at call time
    var $prefixId      = 'tx_wepublication_pi1';		       // Same as class name
    var $scriptRelPath = 'pi1/class.tx_wepublication_pi1.php'; // Path to this script relative to the extension dir.
    var $extKey        = 'we_publication';	                   // The extension key.
    var $theTable      = 'we_publication_docs';	               // Our table's name.
    var $data          = array();                              // Our data-array for this extension
    var $TCA           = array();
    var $allowCaching  = FALSE;
    var $we_publication_uid;    // the uid of the current news record in SINGLE view
    var $conf;                  // the TypoScript configuration array
    var $config;                // the processed TypoScript configuration array

    /**
	 * Init Function: here all the needed configuration values are stored in class variables..
	 *
	 * @param	array		$conf : configuration array from TS
	 * @return	void
	 */
    function init($conf) {
        $this->conf = $conf; //store configuration
        $this->pi_loadLL(); // Loading language-labels
        $this->pi_setPiVarDefaults(); // Set default piVars from TS
        $this->pi_initPIflexForm(); // Init FlexForm configuration for plugin
        $this->we_publication_uid = intval($this->piVars['id']);   // Get the submitted uid of a publication (if any)
        $this->we_publication_aid = intval($this->piVars['aid']);  // Get the submitted aid of a publications (if any)
        $this->we_publication_year= intval($this->piVars['year']); // Get the submitted year of publications (if any)

        // "CODE" decides what is rendered: codes can be set by TS or FF with priority on FF
        $code = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'code', 'sDEF');
        $code = $code ? $code : $this->conf['code'];
        $this->config['code'] = $code ? $code : 'listView';

        // List of publications to show (selecteListView)
        $publicationPidInList = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'publicationPidInList', 's_selectedListView');
        if ($publicationPidInList) {
            $this->config['code'] = 'selectedListView';
        }
        $publicationPidInList = t3lib_div::intExplode(',',$publicationPidInList);
        $this->config['publicationPidInList'] = $publicationPidInList ? $publicationPidInList : t3lib_div::intExplode(',',$this->conf['publicationPidInList'], 1);

        // pid of the page with the single view.
        $singlePid = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'singlePid', 'sDEF');
        $this->config['singlePid'] = $singlePid ? $singlePid : intval($this->cObj->stdWrap($this->conf['singlePid'],$this->conf['singlePid.']));
        $this->config['singlePid'] = intval($this->cObj->stdWrap($this->config['singlePid'],$this->conf['singlePid.']['stdWrap.']));

        // pid of the page with the list view.
        $listPid = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'listPid', 'sDEF');
        $this->config['listPid'] = $listPid ? $listPid : intval($this->cObj->stdWrap($this->conf['listPid'],$this->conf['listPid.']));
        $this->config['listPid'] = intval($this->cObj->stdWrap($this->config['listPid'],$this->conf['listPid.']['stdWrap.']));

        // pid of the page with the internal author view.
        $authorIntPid = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'authorIntPid', 'sDEF');
        $this->config['authorIntPid'] = $authorIntPid ? $authorIntPid : intval($this->cObj->stdWrap($this->conf['authorIntPid'],$this->conf['authorIntPid.']));
        $this->config['authorIntPid'] = intval($this->cObj->stdWrap($this->config['authorIntPid'],$this->conf['authorIntPid.']['stdWrap.']));

        // pid of the page with the external author view.
        $authorExtPid = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'authorExtPid', 'sDEF');
        $this->config['authorExtPid'] = $authorExtPid ? $authorExtPid : intval($this->cObj->stdWrap($this->conf['authorExtPid'],$this->conf['authorExtPid.']));
        $this->config['authorExtPid'] = intval($this->cObj->stdWrap($this->config['authorExtPid'],$this->conf['authorExtPid.']['stdWrap.']));

        // image sizes given from FlexForms
        $this->config['FFimgH'] = intval($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'imageMaxHeight', 's_'.$this->config['code']));
        $this->config['FFimgW'] = intval($this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'imageMaxWidth', 's_'.$this->config['code']));

        # // get fieldnames from the tt_news db-table
        # $this->fieldNames = array_keys($GLOBALS['TYPO3_DB']->admin_get_fields('tt_news'));

        // Configure caching
        $this->allowCaching = $this->conf['allowCaching']?1:0;
        if (!$this->allowCaching) {
            $GLOBALS['TSFE']->set_no_cache();
        }

        // templateFile
        $templateFile = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], $this->config['code'].'Template_file', 's_'.$this->config['code']);
        $templateFile = $templateFile ? $templateFile : $this->cObj->stdWrap($this->conf['templateFile'],$this->conf['templateFile.']);
        $templateFile = $this->cObj->stdWrap($this->conf['templateFile'],$this->conf['templateFile.']['stdWrap.']);
        $this->config['templateFile'] = $templateFile ? $templateFile : 'typo3conf/ext/'.$this->extKey.'/pi1/template.tmpl.html';
        $this->config['templateCode'] = $this->cObj->fileResource($this->config['templateFile']);

        // data sets (to habe ability for wrapping multiple data)
        $dataSet1 = $this->conf[$this->config['code']]['dataSet1'];
        $this->config['dataSet1'] = $dataSet1 ? $dataSet1 : null;

        $dataSet2 = $this->conf[$this->config['code']]['dataSet2'];
        $this->config['dataSet2'] = $dataSet2 ? $dataSet2 : null;

        $dataSet3 = $this->conf[$this->config['code']]['dataSet3'];
        $this->config['dataSet3'] = $dataSet3 ? $dataSet3 : null;

        $dataSet4 = $this->conf[$this->config['code']]['dataSet4'];
        $this->config['dataSet4'] = $dataSet4 ? $dataSet4 : null;

        $dataSet5 = $this->conf[$this->config['code']]['dataSet5'];
        $this->config['dataSet5'] = $dataSet5 ? $dataSet5 : null;


        // Order for publications
        $orderPub = ($this->piVars['orderPub']) ? $this->piVars['orderPub'] : $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'orderPub', 'sDEF');
        $this->config['orderPub'] = $orderPub ? $orderPub : $this->cObj->stdWrap($this->conf['orderPub'],$this->conf['orderPub.']);
        $this->config['orderPub'] = $this->cObj->stdWrap($this->config['orderPub'],$this->conf['orderPub.']['stdWrap.']);
        $orderTypePub = ($this->piVars['orderTypePub']) ? $this->piVars['orderTypePub'] : $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'orderTypePub', 'sDEF');
        $this->config['orderTypePub'] = $orderTypePub ? $orderTypePub : $this->cObj->stdWrap($this->conf['orderTypePub'],$this->conf['orderTypePub.']);
        $this->config['orderTypePub'] = strtolower($this->cObj->stdWrap($this->config['orderTypePub'],$this->conf['orderTypePub.']['stdWrap.']));

        // Order for authors
        $orderAuthors = ($this->piVars['orderAuthors']) ? $this->piVars['orderAuthors'] : $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'orderAuthors', 'sDEF');
        $this->config['orderAuthors'] = $orderAuthors ? $orderAuthors : $this->cObj->stdWrap($this->conf['orderAuthors'],$this->conf['orderAuthors.']);
        $this->config['orderAuthors'] = $this->cObj->stdWrap($this->config['orderAuthors'],$this->conf['orderAuthors.']['stdWrap.']);
        $orderTypeAuthors = ($this->piVars['orderTypeAuthors']) ? $this->piVars['orderTypeAuthors'] : $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'orderTypeAuthors', 'sDEF');
        $this->config['orderTypeAuthors'] = $orderTypeAuthors ? $orderTypeAuthors : $this->cObj->stdWrap($this->conf['orderTypeAuthors'],$this->conf['orderTypeAuthors.']);
        $this->config['orderTypeAuthors'] = strtolower($this->cObj->stdWrap($this->config['orderTypeAuthors'],$this->conf['orderTypeAuthors.']['stdWrap.']));

        // get the table definition
        $GLOBALS['TSFE']->includeTCA();
        $this->TCA = $GLOBALS['TCA'][$this->theTable];

        // Preconfigure the typolink
        $this->initTypolink();

        // Getting FE-usergroup of internal authors
        $authorIntGrpIds = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'authorIntGrpIds', 'sDEF');
        $this->config['authorIntGrpIds'] = $authorIntGrpIds ? $authorIntGrpIds : t3lib_div::trimExplode(',', $this->conf['authorIntGrpIds'], 1);

        // Getting FE-usergroup of external authors
        $authorExtGrpIds = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'authorExtGrpIds', 'sDEF');
        $this->config['authorExtGrpIds'] = $authorExtGrpIds ? $authorExtGrpIds : t3lib_div::trimExplode(',', $this->conf['authorExtGrpIds'], 1);
    }

    /**
	 * Main method
	 *
	 * @param	string		$content: The content of the PlugIn
	 * @param	array		$conf: The PlugIn Configuration
	 * @return	The content that should be displayed on the website
	 */
    function main($content,$conf) {
        $this->local_cObj = t3lib_div::makeInstance('tslib_cObj'); // Local cObj.
        $this->init($conf);
        switch($this->config['code']) {
            case 'singleView':
                $mainSubpart = '###SINGLE_VIEW###';
                $this->getData();
                #$this->getSingleView();
                break;
            case 'selectedListView':
                $mainSubpart = '###SELECTED_LIST_VIEW###';
                $this->getData();
                break;
            case 'authorView':
                break;
            default:
                $mainSubpart = '###LIST_VIEW###';
                $this->getData();
                break;
        }

        $template = $this->cObj->getSubpart($this->config['templateCode'], $mainSubpart);

        switch($this->config['code']) {
            case 'singleView':
                $subpartArray['###ITEM###'] = $this->wrapItemsSingle($template, '###ITEM###', $this->data);
                break;
            case 'selectedListView':
                $subpartArray['###LIST###'] = $this->wrapItemsList($template, '###LIST###', $this->data);
                break;
            case 'authorView':
                break;
            default:
                $subpartArray['###LIST###'] = $this->wrapItemsList($template, '###LIST###', $this->data);
                break;
        }
        $tmp = array();
        preg_match_all("/###(.*)###/siU", $template, $tmp);
        $this->data['usedMarker'] = $tmp[0];
        unset($tmp);
        $content = $this->cObj->substituteMarkerArrayCached($template, $markerArray, $subpartArray, array());
        return $this->pi_wrapInBaseClass($content);
    }

    /**
     * Preconfiguring typolink
     * 
     * @return void
     *
     */
    function initTypolink() {
        $this->cObj->setCurrentVal($GLOBALS["TSFE"]->id);
        $this->typolink_conf                      = $this->conf["typolink."];
        $this->typolink_conf["parameter."]["current"] = 1;
        $this->typolink_conf["additionalParams"]  = $this->cObj->stdWrap( $this->typolink_conf["additionalParams"], $this->typolink_conf["additionalParams."] );
        $this->typolink_conf["additionalParams"]  = $this->cObj->stdWrap( $this->typolink_conf["additionalParams"], $this->typolink_conf["additionalParams."]["stdWrap."] );
        $this->typolink_conf["useCacheHash"]      = $this->allowCaching;
        $this->typolink_conf["no_cache"]          = !$this->allowCaching;
        $this->typolink_conf["target"]            = '';
        $this->typolink_conf["extTarget"]         = '';
        $this->typolink_conf["no_cache"]          = !$this->allowCaching;
        unset($this->typolink_conf["additionalParams."]);
    }

    /**
     * Controls wrapping of datasets
     * 
     * Calls the wrapper method for each dataset.
     *
     * @param array $markerArray
     */
    function wrapDataSets(&$markerArray) {
        for ($j=1; $j<=5; $j++) {
            $this->wrapDataSet($j, &$markerArray);
        }
    }
    /**
     * Wrapping of datasets
     * 
     * Wraps a sets of fields (and labels) if the full set is 
     * not empty by adding stings to the first/last marker of 
     * the dataset. If all fields are empty, no data is changed. 
     *
     * @param int $num  Dataset-ID (dataSet<id>) 
     * @param array $markerArray
     */
    function wrapDataSet($num, &$markerArray) {
        if ($this->conf['dataSet'.$num.'.']['wrap']) {
            $wrap   = t3lib_div::trimExplode('|', $this->conf['dataSet'.$num.'.']['wrap'], 1);
            $fieldNames = t3lib_div::trimExplode(',', $this->config['dataSet'.$num], 1);

            $fMarkerNames = array();
            $lMarkerNames = array();

            for ($i=0;$i<count($fieldNames);$i++) {
                $lMarkerNames[$i] = '###LABEL_'.$this->cObj->caseshift($fieldNames[$i],'lower').'###';
                $fMarkerNames[$i] = '###FIELD_'.$this->cObj->caseshift($fieldNames[$i],'lower').'###';
            }
            // first non-empty marker
            $i = 0;
            while (empty($markerArray[$fMarkerNames[$i]]) && $i<count($fMarkerNames)) {
                $i++;
            }
            if (!empty($markerArray[$fMarkerNames[$i]])) {
                // If label for the first marker is used, change this...
                if (in_array($lMarkerNames[$i], $this->data['usedMarker'])) {
                    $markerArray[$lMarkerNames[$i]] = $wrap[0] . $markerArray[$lMarkerNames[$i]];
                }
                // ...otherwise change the field.
                else {
                    $markerArray[$fMarkerNames[$i]] = $wrap[0] . $markerArray[$fMarkerNames[$i]];
                }
            }
            // last non-empty marker
            $i = count($fMarkerNames);
            while (empty($markerArray[$fMarkerNames[$i]]) && $i>0) {
                $i--;
            }
            if (!empty($markerArray[$fMarkerNames[$i]])) {
                $markerArray[$fMarkerNames[$i]] .= $wrap[1];
            }
        }
    }

    function wrapItemsSingle($templateCode, $part, $data) {
        $wrappedSubpartArray    = array();
        $subpartArray           = array();
        $result                 = '';
        $template               = $this->cObj->getSubpart($templateCode, $part);

        foreach ($data[$this->we_publication_uid] AS $fName=>$val) {
            $this->wrapData($fName, $val, $this->we_publication_uid, &$markerArray, &$subpartArray, &$template);
        }
        $this->wrapDataSets(&$markerArray);
        $result .= $this->cObj->substituteMarkerArrayCached(&$template, $markerArray, $subpartArray, $wrappedSubpartArray );
        return $result;
    }


    function wrapItemsList($templateCode, $part, $data, $itemPart='###ITEM###') {
        $wrappedSubpartArray    = array();
        $subpartArray           = array();
        $result                 = '';

        $template               = $this->cObj->getSubpart($templateCode, $part);
        $template_item          = $this->cObj->getSubpart($template, $itemPart);
        $template_item_row      = '';

        $count                  = count($data);
        $i                      = 0;

        foreach ($data AS $row) {
            $template_item_row                 = $template_item;
            $markerArray['###CLASS###']        = ($i%2==0) ? 'even' : 'odd';
            if ($i==0) {
                $markerArray['###CLASS###']   .= ' first';
            }
            if ($i==$count-1) {
                $markerArray['###CLASS###']   .= ' last';
            }
            foreach ($row AS $fName=>$val) {
                $this->wrapData($fName, $val, $row['pubuid'], &$markerArray, &$subpartArray, &$template_item_row);
            }
            #t3lib_div::debug($subpartArray, 'wrapItemsList: $subpartArray');
            #t3lib_div::debug($template_item_row, 'wrapItemsList: $template_item_row');
            $this->wrapDataSets(&$markerArray);
            $result .= $this->cObj->substituteMarkerArrayCached($template_item_row, $markerArray, $subpartArray, $wrappedSubpartArray );
            $i++;
        }
        return $result;
    }

    function wrapItemsAuthors($templateCode, $part, $data) {
        $wrappedSubpartArray    = array();
        $subpartArray           = array();
        $result                 = '';

        $template               = $this->cObj->getSubpart($templateCode, $part);
        foreach ($data AS $row) {
            if ($row['author_type'] == 'internal') {
                $itemPart                   = '###AUTHORS_INTERNAL_ITEM###';
                $markerArray['###CLASS###'] = 'internal';
            } else {
                $itemPart                   = '###AUTHORS_EXTERNAL_ITEM###';
                $markerArray['###CLASS###'] = 'external';
            }
            $template_item      = $this->cObj->getSubpart($template, $itemPart);
            foreach ($row AS $fName=>$val) {
                $this->wrapData($fName, $val, $row['author_uid'], &$markerArray, &$subpartArray, &$template_item);
            }
            #t3lib_div::debug($markerArray, 'wrapItemsAuthors: $markerArray=');
            $result .= $this->cObj->substituteMarkerArrayCached($template_item, $markerArray, $subpartArray, $wrappedSubpartArray );
        }
        #t3lib_div::debug($result, 'wrapItemsAuthors: $result=');
        return $result;
    }

    /**
     * Controls wrapping of data
     * 
     * Prepares the marker keys and calls the appropriate wrapper method for data.
     * For empty markers, removeUnused() is called to remove the subpart.
     *
     * @param string    $fName
     * @param string    $val
     * @param int       $pubid
     * @param array     $markerArray
     * @param array     $subpartArray
     * @param string    $template
     */
    function wrapData($fName, $val, $id, &$markerArray, &$subpartArray, &$template) {
        $markerKey                  = '###LABEL_'.$this->cObj->caseshift($fName,'lower').'###';
        $markerArray[$markerKey]    = $this->getFieldLabel($fName);
        $markerKey                  = '###FIELD_'.$this->cObj->caseshift($fName,'lower').'###';

        switch ($fName) {
            case 'title':
                if ($this->config['code'] == 'listView' || $this->config['code'] == 'selectedListView') {
                    $additionalParams       .= '&' . $this->prefixId . '[id]=' . $id;
                    $markerArray[$markerKey] = $this->wrapDataLink($fName, $this->config['singlePid'], 0, $additionalParams, $val);
                } else {
                    $markerArray[$markerKey] = $this->cObj->stdWrap($val, $this->conf[$this->config['code'].'.'][$fName.'.']);
                    $markerArray[$markerKey] = $this->cObj->stdWrap($val, $this->conf[$this->config['code'].'.'][$fName.'.']['stdWrap.']);
                }
                break;
            case 'url':
                $markerArray[$markerKey] = $this->wrapDataLink($fName, $val['href'], 0, null, $val['title']);
                break;
            case 'doi':
                $markerArray[$markerKey] = $this->wrapDataLink($fName, $val ? 'http://dx.doi.org/'.$val : $val, 0, array(), $val);
                break;
            case 'author_email':
                $markerArray[$markerKey] = $this->wrapDataLink($fName, $val);
                break;
            case 'author_name':
                $additionalParams       .= '&tx_gsifeuserlist_pi1[showUid]=' . $id;
                $markerArray[$markerKey] = $this->wrapDataLink($fName, $this->config['authorPid'] ? $this->config['authorPid'] : 'this', 0, $additionalParams, $val);
                # stdWrap done within wrapDataLink
                # $markerArray[$markerKey] = $this->cObj->stdWrap($markerArray[$markerKey], $this->conf[$this->config['code'].'.'][$fName.'.']);
                # $markerArray[$markerKey] = $this->cObj->stdWrap($markerArray[$markerKey], $this->conf[$this->config['code'].'.'][$fName.'.']['stdWrap.']);
                break;
            case 'author_www':
                $markerArray[$markerKey] = $this->wrapDataLink($fName, $val);
                break;
            case 'cover':
                $markerArray[$markerKey] = $this->wrapDataImage($fName, $val);
                break;
            case 'document_1':
            case 'document_2':
            case 'document_3':
                $markerArray[$markerKey] = $this->wrapDataFile($fName, $val['file'], 0, null, $val['title']);
                break;
            case 'authors':
                $subpartArray['###AUTHORS###'] = $this->wrapItemsAuthors($template, '###AUTHORS###', $val);
                break;
            case 'status':
                $val = $this->cObj->stdWrap($this->pi_getLL('status.I.' . $val), $this->conf[$this->config['code'].'.'][$fName.'.']);
                $markerArray[$markerKey] = $this->cObj->stdWrap($val, $this->conf[$this->config['code'].'.'][$fName.'.']);
                $markerArray[$markerKey] = $this->cObj->stdWrap($val, $this->conf[$this->config['code'].'.'][$fName.'.']['stdWrap.']);
                break;
            default:
                if (!empty($val)) {
                    $markerArray[$markerKey] = $this->cObj->stdWrap($val, $this->conf[$this->config['code'].'.'][$fName.'.']);
                    $markerArray[$markerKey] = $this->cObj->stdWrap($val, $this->conf[$this->config['code'].'.'][$fName.'.']['stdWrap.']);
                } else {
                    $markerArray[$markerKey] =  $val;
                }
                break;
        }
        if (empty($markerArray[$markerKey]) && $fName != 'authors') {
            $template = $this->removeUnused($template, $fName);
        }
    }

    /**
     * Wrapping of author data
     *
     * @param string    $fName
     * @param string    $val
     * @return object   typolink   
     */
    /*    function wrapDataAuthor($fName, $val) {
    $img_conf   = (!is_array($this->conf[$this->config['code'].'.']["$fName."])) ? array() : $this->conf[$this->config['code'].'.']["$fName."];
    $img_dir    = (empty($this->conf['imgDir.'][$fName])) ? 'uploads/tx_wepublication' : $this->conf['imgDir.'][$fName];
    $img_dir   .= '/';

    list($img)  = split(',',$val);
    $img_conf['file'] = $img_dir.$img;
    return $this->cObj->IMAGE($img_conf);
    }
    */
    /**
     * Wrapping of files/documents data
     *
     * @param string    $fName
     * @param string    $val
     * @param int       $current,..
     * @param array     $additionalParams,..
     * @param string    $linkText,..
     * @return object   typolink   
     */
    function wrapDataFile($fName, $val, $current=0, $additionalParams=null, $linkText=null) {
        $string      = '';
        $file_dir    = (empty($this->conf['docDir.'][$fName])) ? 'uploads/tx_wepublication' : $this->conf['imgDir.'][$fName];
        $file_dir   .= '/';
        $files       = t3lib_div::trimExplode(',', $val, 1);
        foreach($files as $file) {
            $file_conf['file'] = $file_dir.$file;
            $string   = $this->wrapDataLink($fName, $file_conf['file'], 0, $additionalParams, empty($linkText)?$file:$linkText);
        }
        return $string;
    }

    /**
     * Wrapping of linked data
     *
     * @param string    $fName
     * @param string    $val
     * @param int       $current,..
     * @param array     $additionalParams,..
     * @param string    $linkText,..
     * @return object   typolink   
     */
    function wrapDataLink($fName, $val, $current=0, $additionalParams=null, $linkText=null) {
        $temp_conf                          = $this->typolink_conf;
        $temp_conf["parameter"]             = $val;
        $temp_conf["parameter."]["current"] = $current;
        $temp_conf["additionalParams"]     .= $additionalParams;
        $linkText = $linkText ? $linkText : $val;
        $linkText = $this->cObj->stdWrap($linkText, $this->conf[$this->config['code'].'.'][$fName.'.']);
        $linkText = $this->cObj->stdWrap($linkText, $this->conf[$this->config['code'].'.'][$fName.'.']['stdWrap.']);
        return $this->cObj->typolink($linkText, $temp_conf);
    }

    /**
     * Wrapping of image data
     *
     * @param string    $fName
     * @param string    $val
     * @return object   typolink   
     */
    function wrapDataImage($fName, $val) {
        $img_conf   = (!is_array($this->conf[$this->config['code'].'.']["$fName."])) ? array() : $this->conf[$this->config['code'].'.']["$fName."];
        // overwrite image sizes from TS with the values from the content-element if they exist.
        if ($this->config['FFimgH'] || $this->config['FFimgW']) {
            $img_conf['file.']['maxW'] = $this->config['FFimgW'];
            $img_conf['file.']['maxH'] = $this->config['FFimgH'];
        }
        $img_dir    = (empty($this->conf['imgDir.'][$fName])) ? 'uploads/tx_wepublication' : $this->conf['imgDir.'][$fName];
        $img_dir   .= '/';

        list($img)  = split(',',$val);
        $img_conf['file'] = $img_dir.$img;
        return $this->cObj->IMAGE($img_conf);
    }

    function getData() {
        #####################################################################################
        ############ Hier fehlt noch die WHERE-Klausel ###############
        #####################################################################################
        $sql   = "SELECT tx_wepublication_docs.uid AS pubuid,tx_wepublication_docs.title,  
                         IF(tx_wepublication_magazine.uid<>0,tx_wepublication_magazine.title,tx_wepublication_docs.magazine) AS magazine, 
                         tx_wepublication_docs.year, tx_wepublication_docs.status, tx_wepublication_docs.issue,
                         tx_wepublication_docs.page_end, tx_wepublication_docs.page_start, tx_wepublication_docs.abstract, tx_wepublication_docs.cover, tx_wepublication_docs.url_href, tx_wepublication_docs.url_title, tx_wepublication_docs.doi, tx_wepublication_docs.pacs, 
                         tx_wepublication_docs.document_1_file, tx_wepublication_docs.document_1_title, tx_wepublication_docs.document_2_file, tx_wepublication_docs.document_2_title, tx_wepublication_docs.document_3_file, tx_wepublication_docs.document_3_title, tx_wepublication_docs.comment,
                         tx_wepublication_docs_authors_mm.sorting,
                         fe_users.uid AS feuuid, fe_users.usergroup, fe_users.name, fe_users.last_name, fe_users.first_name, fe_users.email, fe_users.company AS organisation, fe_users.www 
                    FROM tx_wepublication_docs
                    LEFT JOIN tx_wepublication_docs_authors_mm ON tx_wepublication_docs.uid = tx_wepublication_docs_authors_mm.uid_local
                    LEFT JOIN fe_users ON fe_users.uid = tx_wepublication_docs_authors_mm.uid_foreign
                    LEFT JOIN tx_wepublication_magazine ON tx_wepublication_magazine.uid=tx_wepublication_docs.magazine2
                   WHERE tx_wepublication_docs.hidden=0
                     AND tx_wepublication_docs.deleted=0
                     AND fe_users.disable=0
                     AND fe_users.deleted=0 ";
        /* ###################################################################################
        *                   @todo: starttime und endtime einbauen
        ################################################################################### */

        $where = "";
        switch($this->config['code']) {
            case 'singleView':
                if ($this->we_publication_uid) {
                    $where  = "tx_wepublication_docs.uid=" .$this->we_publication_uid;
                }
                break;
            case 'selectedListView':
                    $where  = "tx_wepublication_docs.uid IN (" . implode(",", $this->config['publicationPidInList']) . ")";
                break;
            case 'authorView':
                break;                
            default:
                // limitation to specified authors
                if ($this->we_publication_aid) {
                    $uids   = $this->getPublicationFromAuthorUids(t3lib_div::trimExplode(',', $this->we_publication_aid, 1));
                    $where  = "tx_wepublication_docs.uid IN (" . $uids . ")";
                }
                // limitation to specified years
                if ($this->we_publication_year) {
                    $where .= $this->we_publication_aid ? " AND "  : "";
                    $where .= "tx_wepublication_docs.year=" . $this->we_publication_year;
                }
                break;
        }

        if ($where) {
            $sql  .= "AND (" . $where . ") ";
        }
        $order  = "ORDER BY ";
        if(preg_match("/^[a-z0-9_-]+$/i",$this->config['orderPub'])) {
            $order .= "tx_wepublication_docs." . $this->config['orderPub'];
        } else {
            $order .= "tx_wepublication_docs.year ";
        }
        $order .= ($this->config['orderTypePub']=='desc')?' desc':' asc';
        if(preg_match("/^[a-z0-9_-]+$/i",$this->config['orderAuthors'])) {
            $order .= ", fe_users." . $this->config['orderAuthors'];
        } else {
            $order .= ", fe_users.sorting";
        }
        $order .= ($this->config['orderTypeAuthors']=='desc')?' desc':' asc';
        $sql  .= $order;
        #t3lib_div::debug($sql, 'getData: $sql=');
        $this->setData($sql);
    }

    function getWhere() {

    }

    /**
     * Returns the uids of all publications of the given authors
     * 
     * @param mixed     $uids             authors uid(s) as array or int/string 
     * @param boolean   $returnArray,..   Whether the result should be returned asaArray or string
     * @return mixed
     */
    function getPublicationFromAuthorUids($uids, $returnArray=FALSE) {
        $return = array();
        $sql = "SELECT t1.uid
                  FROM `tx_wepublication_docs` t1
             LEFT JOIN tx_wepublication_docs_authors_mm t2 ON t1.uid = t2.uid_local ";
        if (is_array($uids)) {
            $sql .= 'WHERE ';
            #if (count($uids)==1)	{
            #    $sql .=' t2.uid_foreign='.intval($uids[0]);
            #} else {
                $sql .=' t2.uid_foreign IN ('.implode(',',$GLOBALS['TYPO3_DB']->cleanIntArray($uids)).')';
            #}
        } else if (is_string($uids) || is_integer($uids)) {
            $sql .= 'WHERE t2.uid_foreign='.intval($uids[0]);
        }
        $row    = array();
        $res    = $GLOBALS['TYPO3_DB']->sql_query($sql);
        while (false != ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
            $return[] = $row['uid'];
        }
        return $returnArray ? $return : implode(',', $return);
    }

    function setData($sql) {
        $row    = array();
        $res    = $GLOBALS['TYPO3_DB']->sql_query($sql);
        while (false != ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
            $key = $row['pubuid'];
            if (array_key_exists($key, $this->data) == false) {
                $this->data[$key]['pubuid']   = $row['pubuid'];
                $this->data[$key]['title']    = $row['title'];
                $this->data[$key]['magazine'] = $row['magazine'];
                $this->data[$key]['year']     = $row['year'];
                $this->data[$key]['status']   = $row['status'];
                $this->data[$key]['issue']    = $row['issue'];
                $this->data[$key]['page_end'] = $row['page_end'];
                $this->data[$key]['page_start']=$row['page_start'];
                $this->data[$key]['abstract'] = $row['abstract'];
                $this->data[$key]['cover']    = $row['cover'];
                $this->data[$key]['doi']      = $row['doi'];
                $this->data[$key]['pacs']     = $row['pacs'];
                $this->data[$key]['comment']  = $row['comment'];
                $this->data[$key]['authors']  = array();
                $this->data[$key]['url']['href']           = $row['url_href'];
                $this->data[$key]['url']['title']          = $row['url_title'];
                $this->data[$key]['document_1']['title']   = $row['document_1_title'];
                $this->data[$key]['document_1']['file']    = $row['document_1_file'];
                $this->data[$key]['document_2']['title']   = $row['document_2_title'];
                $this->data[$key]['document_2']['file']    = $row['document_2_file'];
                $this->data[$key]['document_3']['title']   = $row['document_3_title'];
                $this->data[$key]['document_3']['file']    = $row['document_3_file'];
            }
            $tmpName                          = split(' ', $row['name'], 2);
            $author                           = array();
            $author['author_uid']             = $row['feuuid'];
            $author['author_name']            = ($row['name']) ? $row['name'] : $row['first_name'] . ' ' . $row['last_name'];
            $author['author_last_name']       = ($row['last_name']) ? $row['last_name'] : $tmpName[1];
            $author['author_first_name']      = ($row['first_name']) ? $row['first_name'] : $tmpName[0];
            $author['author_email']           = $row['email'];
            $author['author_organisation']    = $row['organisation'];
            $author['author_www']             = $row['www'];

            $tmpAuthor_usergroup              = t3lib_div::trimExplode(',', $row['usergroup'], 1);
            if (is_array($this->config['authorIntGrpIds'])) {
                $tmpArray = array_intersect($tmpAuthor_usergroup, $this->config['authorIntGrpIds']);
                if (!empty($tmpArray)) {
                    $author['author_type']    = 'internal';
                } else {
                    $tmpArray = array_intersect($tmpAuthor_usergroup, $this->config['authorExtGrpIds']);
                    if (is_array($this->config['authorExtGrpIds'])) {
                        if (!empty($tmpArray)) {
                            $author['author_type']= 'external';
                        }
                    }
                }
            }
            unset($tmpAuthor_usergroup);
            unset($tmpArray);
            if (isset($author['author_type'])) {
                $this->data[$key]['authors'][] = $author;
            }
        }
        #t3lib_div::debug($this->data,'setData: $this->data=');
    }

    /**
	 * Returns a sorting link for a column header
	 *
	 * @param	string		$fN: Fieldname
	 * @return	The fieldlabel wrapped in link that contains sorting vars
	 */
    function getFieldHeader_sortLink($fN)	{
        return $this->pi_linkTP_keepPIvars($this->getFieldLabel($fN),array('sort'=>$fN.':'.($this->internal['descFlag']?0:1)));
    }

    /**
     * Getting label value for a field
     * 
     * Consides _LOCAL_LANG entries an linkWrap entries dified in TS-setup.
     *
     * @param string    $fN     Field's name
     * @return string   Label
     */
    function getFieldLabel($fN)	{
        switch($this->config['code']) {
            case 'singleView':
                $fN = 'singleFieldLabel_' . $fN;
                break;
            case 'selectedListView':
                $fN = 'listFieldLabel_' . $fN;
                break;
            case 'authorView':
                break;
            default:
                $fN = 'listFieldLabel_' . $fN;
                break;
        }

        $label = $this->pi_getLL($fN);
        // Falls überschrieben (oder absichtlich genullt)
        if (is_array($this->conf['_LOCAL_LANG.'][$this->LLkey.'.']) && array_key_exists($fN, $this->conf['_LOCAL_LANG.'][$this->LLkey.'.'])) {

            $label = !empty($this->conf['_LOCAL_LANG.'][$this->LLkey.'.'][$fN]) ? $this->conf['_LOCAL_LANG.'][$this->LLkey.'.'][$fN] : '';
        } else {
            // system
            if (!$label)	{
                $label = $GLOBALS['TSFE']->sL($this->TCA['columns'][$fN]['label']);
            }
            // fallback
            if (!$label) {
                $label = "[$fN]";
            }
        }
        if ($this->conf['labelWraps.']["$fN."]) {
            $label = $this->cObj->stdWrap($label,$this->conf['labelWraps.']["$fN."]);
        }
        return $label;
    }

    /**
     * Removes unused (or empty) subparts and templates
     *
     * @param  string   $templateCode   Whole template
     * @param  string   $fName,         field's name
     * @return string   template as necessary
     */
    function removeUnused($templateCode, $fName='') {

        // unused fields
        #if (empty($fName)) {
        #    $includedFields = t3lib_div::trimExplode(',', $this->conf[$this->config['code'].'.']['fields'], 1);
        #    foreach ($this->fieldList as $fName) {
        #        if (!in_array(trim($fName), $includedFields)) {
        #            $templateCode = $this->cObj->substituteSubpart($templateCode, '###SUB_INCLUDED_FIELD_'.$fName.'###', '');
        #        }
        #    }
        #}
        // empty fields
        #else {
        $templateCode = $this->cObj->substituteSubpart($templateCode, '###SUB_INCLUDED_FIELD_'.$fName.'###', '');
        #}
        return $templateCode;
    }
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/we_publication/pi1/class.tx_wepublication_pi1.php'])	{
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/we_publication/pi1/class.tx_wepublication_pi1.php']);
}

?>