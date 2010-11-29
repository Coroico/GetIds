<?php
/**
 * getIds
 *
 * A general purpose snippet to get a list of resource ids for MODx 2.0.
 *
 * @author Coroico
 * @copyright Copyright 2010, Coroico
 * @version 1.0.0-pl - November 27, 2010
 *
 * [[!GetIds? &depth=`5` &ids=`8,-c10,12,3,c3`]]
 *
 * depth - (Opt) Integer value indicating depth to search for resources from each parent
 *
 * ids - Comma-delimited list of resource ids serving as parents, child or resource
 *
 * ids as [ [+| |-] [c| |p]integer ] where:
 *
 *  - : exclude ids             + or '' : include ids (default)
 *  p : parents resources       c : children resources      '': current resource
 *
 * e.g:
 *
 * &ids=`18, c18, -c21, 34` : include #18 and children of #18, exclude chidren of #21 but keep #34
 *
 * &ids=`p12, -p3, -1, 2`   : include all parents of #12, exclude parents of #3 but keep #2
 *
 * &ids=`18, c18, p3, -p2`  : include #8 and children of #8, include parents of #3, exclude parent of #2
 *
 *
 * IMPORTANT: take care of the order of arguments. To be excluded the id should be already in the list
 * &ids=`18, 19, -19, 20` => '18,20'          but &ids=`18, -19, 19, 20` => '18,19,20'
 *
 */

/* set default properties */
$ids = (!empty($ids) || $ids === '0') ? explode(',', $ids) : array($modx->resource->get('id'));
$depth = isset($depth) ? (integer) $depth : $scriptProperties['depth'];

$ids = array_map('trim',$ids);
$resIds = array();

foreach ($ids as $id) {
    if (intval($id)) {  // specified without any prefix
        $id = ($id > 0) ? "+n".abs($id) : "-n".abs($id);
    }
    $len = strlen($id);
    $digit1 = substr( $id, 0, 1); // p,n or c
    $str = substr($id,1,strlen($id)-1);

    if ($len >= 3){
        if (intval($str)) $id = '+' . $digit1 . abs($str);
        else if ($digit1 != '+' && $digit1 != '-') $id = substr($id,1,1) . $digit1 . substr($id,2,strlen($id)-2);
    }
    else if ($len == 2) {
        if (intval($str)) $id = '+' . $digit1 . $str;
        else $id = '';
    }
    else if ($len == 1){
        if (intval($str)) $id = '+' . 'n' . $id;
        else $id = '';
    }

    $digit1 = strtolower(substr( $id, 0, 1));
    $digit2 = strtolower(substr( $id, 1, 1));
    $rid = substr($id, 2, strlen($id)-2);

    switch($digit2){
        case "n":  // simple node
            $tmp = array($rid);
            break;
        case "c":  // children
            $tmp = $modx->getChildIds($rid, $depth);
            break;
        case "p":   // parents
            $tmp = $modx->getParentIds($rid, $depth);
            break;
    }

    if ($digit1 == '+') $resIds = array_merge($resIds,$tmp);  // add ids
    else if ($digit1 == '-') $resIds = array_values(array_diff($resIds,$tmp));  // remove excluded ids
}

$resIds = array_values(array_unique($resIds));  // remove duplicated ids
$lstIds = implode(',',$resIds);

return $lstIds;