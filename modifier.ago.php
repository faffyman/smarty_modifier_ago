<?php
/**
 * Smarty plugin
 *
 * @package App
 * @subpackage SmartyPluginsModifier
 */

/**
 * Smarty timestamp modifier
 *
 * Type:     modifier
 * Name:     ago
 * Purpose:  allows you to present a date timestamp as X (minutes|hours|days|weeks|months) Ago
 * Usage:   {$dTimestamp|ago}  or specifying a unit {dTimestamp|ago:'week'}
 *
 * @link
 * @author faffyman@ahoo.com>
 * @param int $sTimestamp
 * @param string $sUnits preferred units specified in the singular - 'minute','hour','day','week','month','year'. This will over-ride the default
 * @return string
 */
function smarty_modifier_ago($sTimestamp, $sUnits=null)
{

    $nNow = time();
    $nThen = strtotime($sTimestamp);
    //duration between two timestamps
    $nHowLongAgo = $nNow - $nThen;

    //if user specified seconds just return the difference
    if($sUnits=='second') {
      return $nHowLongAgo;
    }


    if($nHowLongAgo < 60) { // seconds in minute
      return 'Just now';
    }


    $aTimes['minute'] = array('nDivideBy'=>60            ,'sLabel'=>'minute' ,'nMax'=>3600);
    $aTimes['hour']   = array('nDivideBy'=>3600          ,'sLabel'=>'hour'   ,'nMax'=>86400);
    $aTimes['day']    = array('nDivideBy'=>86400         ,'sLabel'=>'day'    ,'nMax'=>604800);
    $aTimes['week']   = array('nDivideBy'=>604800        ,'sLabel'=>'week'   ,'nMax'=>(86400 * 30.4));
    $aTimes['month']  = array('nDivideBy'=>(86400 * 30.4),'sLabel'=>'month'  ,'nMax'=>(86400 * 365)); //30.4 days in a month (on average)
    $aTimes['year']   = array('nDivideBy'=>(86400 * 365) ,'sLabel'=>'year'   ,'nMax'=>( (86400 * 365.25) * 999)); //thousand years ought to be more than enough!



    if (!empty($sUnits) && in_array($sUnits,array('minute','hour','day','week','month','year')) ) {
      return getItemAgeFromSettings($aTimes[$sUnits],$nHowLongAgo);
    }

    //User hasn't specified a return unit, so work out the best unit
    foreach ($aTimes as $sLabel => $aSettings) {
      if($nHowLongAgo < $aSettings['nMax']) {
       return getItemAgeFromSettings($aSettings,$nHowLongAgo);
      }
    }
    // last resort - use the last possible setting - i.e. years - should never actually get this far though
    $aSettings = end($aTimes);
    $sItemAge = getItemAgeFromSettings($aSettings,$nHowLongAgo);
    return $sItemAge;
  }



  /**
   * Helper for getItemAge
   *
   * @param array $aSettings
   * @param int $nHowLongAgo
   * @return string
   */
  function getItemAgeFromSettings($aSettings,$nHowLongAgo)
  {
        $nUnitsAgo = floor($nHowLongAgo / $aSettings['nDivideBy']);
        $sStr = $nUnitsAgo.' '.$aSettings['sLabel'].($nUnitsAgo > 1 ? 's' : '').' ago ';
        return $sStr;
  }


