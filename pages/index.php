<?php

/**
 * Zuerst werden aus der Datenbank die Informationen über abgehakte bzw. insg.
 * vorhandene Prüfobjekte ermittelt (gruppiert nach Gruppe und ja/nein).
 *
 * Die zugehörigen Seitentitel werden um die Ergebnisse ergänzt.
 *
 * Dann erst wird die Addon-Seite aufgebaut
 */

use FriendsOfRedaxo\BaseQualityCheck\BqcTools;
use FriendsOfRedaxo\BaseQualityCheck\ChecklistExport;
use FriendsOfRedaxo\BaseQualityCheck\ChecklistService;

/** @var rex_addon $this */

/**
 * Für alle Gruppen die Checks abfragen; gruppiert wird über Gruppe und Check
 * Daraus ein Status-Array erstellen: [gruppe][check] = anzahl.
 */
$export = rex_request::get('bqc_export', 'string');
if (in_array($export, ['json', 'csv'], true)) {
    $exportToken = rex_csrf_token::factory('base_quality_check_export');
    if (!$exportToken->isValid()) {
        throw new rex_exception(rex_i18n::msg('csrf_token_invalid'));
    }
    ChecklistExport::send($export);
}

$error = ChecklistService::handleAction();

$page = rex_be_controller::getCurrentPageObject();
$subPages = $page->getParent()->getSubpages();

/**
 * In den relevanten SubPages des Addons den Titel um die Anzahl der erfolgten
 * Checks und der Gesamtzahl eintragen. Entsprechend des Füllgrades werden
 * unterschiedliche Farben gesetzt.
 */
$title4reset = [];
foreach (ChecklistService::groups() as $group) {
    $groupId = $group->getId();
    $groupPageName = ChecklistService::pageKey($groupId);
    if (!isset($subPages[$groupPageName])) {
        continue;
    }
    $progress = ChecklistService::progress($groupId);

    $groupPage = $subPages[$groupPageName];
    $name = $groupPage->getTitle();
    $title4reset[$name] = $groupPage;
    $name = sprintf(
        '%s <span class="bqc-badge %s">%d | %d</span>',
        $name,
        BqcTools::quotaClass($progress['quota']),
        $progress['checked'],
        $progress['total'],
    );
    $groupPage->setTitle($name);
}

/**
 * Ausgabe der Seiten:
 *
 * 1) Seitenheader aufbauen, anschließend die Titel wieder auf den
 *    Stand ohne Zähler zurücksetzen
 * 2) Den jeweiligen Content ausgeben
 * 3) Alles in einen <DIV> mit einer für das CSS identifizieren Klasse einpacken
 */
echo '<div class="bqc-addon">';
$version = rex_escape($this->getVersion());
echo rex_view::title(rex_i18n::msg('base_quality_check_title') . ' <small class="bqc-version">v' . $version . '</small>');
foreach ($title4reset as $name => $groupPage) {
    $groupPage->setTitle($name);
}
if (null !== $error) {
    echo rex_view::error($error);
}
rex_be_controller::includeCurrentPageSubPath();
echo '</div>';
