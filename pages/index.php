<?php

/**
 * Zuerst werden aus der Datenbank die Informationen über abgehakte bzw. insg.
 * vorhandene Prüfobjekte ermittelt (gruppiert nach Gruppe und ja/nein)
 * 
 * Die zugehörigen Seitentitel werden um die Ergebnisse ergänzt.
 * 
 * Dann erst wird die Addon-Seite aufgebaut
 */

use FriendsOfRedaxo\BaseQualityCheck\BaseQualityCheck;

/** @var rex_addon $this */

/**
 * Für alle Gruppen die Checks abfragen; gruppiert wird über Gruppe und Check
 * Daraus ein Status-Array erstellen: [gruppe][check] = anzahl
 */
$data = BaseQualityCheck::query()
    ->resetSelect()
    ->select('id')
    ->select('group')
    ->select('check')
    ->selectRaw('COUNT(id)','ct')
    ->where('status',1)
    ->groupBy('group')
    ->groupBy('check')
    ->find();
$status = [];
foreach( $data as $item) {
    $status[$item->getValue('group')][$item->getValue('check')] = $item->getValue('ct');
}

/**
 * Group und SubPage sind hart verdrahtet.
 * TODO: das müsste mal aufgelöst und flexibilisiert werden.
 */
$group2page = [
    1 => 'frontend',
    2 => 'backend',
    3 => 'live',
];

$page = rex_be_controller::getCurrentPageObject();
$subPages = $page->getParent()->getSubpages();

/**
 * In den relevanten SubPages des Addons den Titel um die Anzahl der erfolgten
 * Checks und der Gesamtzahl eintragen. Entsprechend des Füllgrades werden
 * unterschiedliche Farben gesetzt.
 */
$title4reset = [];
foreach($group2page as $groupId=>$groupPageName) {
    // Komisch! Es gibt die Seite nicht.
    // TODO: ggf. hier eine Developer-Exception werfen
    if( !isset($subPages[$groupPageName])) {
        continue;
    }
    // Komisch! das ist eine Gruppe, zu der es keine Einträge gibt?
    // TODO: ggf. hier eine Developer-Exception werfen
    if( !isset($status[$groupId])) {
        continue;
    }

    $sum = array_sum($status[$groupId]);
    $checked = isset($status[$groupId][1]) ? $status[$groupId][1] : 0;
    $quota = round($checked/$sum*100,0);
    
    $groupPage = $subPages[$groupPageName];
    $name = $groupPage->getTitle();
    $title4reset[$name] = $groupPage;
    $name = sprintf(
        '%s <span class="bqc-badge %s">%d | %d</span>',
        $name,
        BqcTools::quotaClass($quota),
        $checked,
        $sum,
    );
    $groupPage->setTitle($name);
}

/**
 * Seitenheader aufbauen, anschließend die Titel wieder auf den
 * Stand ohne Zähler zurücksetzen
 */
echo rex_view::title('Base Quality Check');
foreach( $title4reset as $name=>$groupPage) {
    $groupPage->setTitle($name);
}
/** */
rex_be_controller::includeCurrentPageSubPath();
