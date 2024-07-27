<?php

use FriendsOfRedaxo\BaseQualityCheck\BaseQualityCheck;
use FriendsOfRedaxo\BaseQualityCheck\BaseQualityCheckGroup;
use FriendsOfRedaxo\BaseQualityCheck\BaseQualityCheckSubGroup;
use FriendsOfRedaxo\BaseQualityCheck\BqcTools;

$addon = rex_addon::get('base_quality_check');

/**
 * Alles nur sinnvoll im BE. Wenn FE direkt abbrechen.
 */
if (rex::isFrontend()) {
    return;
}

/**
 * CSS benötigen wir so oder so.
 *
 * Kleine Vorarbeit on Demand: SCSS neu kompilieren
 * Auslöser ist die Property 'compile' auf «true».
 */
rex_extension::register('PACKAGES_INCLUDED', static function () {
    $addon = rex_addon::get('base_quality_check');
    if (true === $addon->getProperty('compile', false)) {
        $compiler = new rex_scss_compiler();
        $scss_files = rex_extension::registerPoint(new rex_extension_point('BE_STYLE_SCSS_FILES', [$addon->getPath('scss/bqc.scss')]));
        $compiler->setScssFile($scss_files);
        $compiler->setCssFile($addon->getPath('assets/bqc.css'));
        $compiler->compile();
        rex_file::copy($addon->getPath('assets/bqc.css'), $addon->getAssetsPath('bqc.css'));
        $addon->removeProperty('compile');
    }
});

rex_view::addCssFile($addon->getAssetsUrl('bqc.css'));

/**
 * automatisch erzeugten Titel für die eigene Navigationsgruppe "base_addon"
 * entfernen durch "Bereitstellen" eines leeren Textes. CSS sorgt dann für die
 * Optik. (klappt nur so, nicht per .lang-Datei)
 *
 * STAN: RexStan meckert hier an, dass der Text eigentlich nicht leer sein darf.
 * @phpstan-ignore-next-line
 */
rex_i18n::addMsg('navigation_base_addon', '');

/**
 * ModelClasses zuweisen.
 */
rex_yform_manager_dataset::setModelClass(
    'rex_base_quality_check',
    BaseQualityCheck::class,
);
rex_yform_manager_dataset::setModelClass(
    'rex_base_quality_check_group',
    BaseQualityCheckGroup::class,
);
rex_yform_manager_dataset::setModelClass(
    'rex_base_quality_check_sub_group',
    BaseQualityCheckSubGroup::class,
);

/**
 * Erst nachdem alle Packages geladen sind können diese beiden Aktionen ablaufen
 * 1) Aus dem Aufruf der Seite ggf. einen Check-Haken setzen/entfernen
 * 2) Menüpunkt im Hauptmenu erweitern und stylen (Füllstandsanzeige).
 */
rex_extension::register('PAGES_PREPARED', static function ($ep) {
    /**
     * Ggf. in der URL stehende Parameter auswerten und verarbeiten
     * func=checktask bzw. func=unchecktask   "check" auf 1 oder 0 setzen
     * id=satznummer                          Satznummer in BaseQualityCheck.
     */
    $func = rex_request::request('func', 'string', '');
    if ('checktask' === $func || 'unchecktask' === $func) {
        $id = rex_request::request('id', 'int', 0);
        $data = BaseQualityCheck::get($id);
        if (null !== $data) {
            $data->setCheck('checktask' === $func ? 1 : 0);
            $data->save();
        }
    }

    /**
     * Füllstand berechnen und den Menütitel um die Anzeige
     * erweitern.
     */
    $status = BaseQualityCheck::query()
        ->resetSelect()
        ->select('id')
        ->select('check')
        ->selectRaw('COUNT(id)', 'ct')
        ->where('status', 1)
        ->groupBy('check')
        ->find()
        ->toKeyValue('check', 'ct');
    $sum = array_sum($status);
    $checked = $status[1] ?? 0;
    $quota = round($checked / $sum * 100, 0);

    $page = rex_be_controller::getPageObject('base_quality_check');
    $name = sprintf(
        '%s <span class="bqc-badge %s">%d %%</span>',
        $page->getTitle(),
        BqcTools::quotaClass($quota),
        $quota,
    );
    $page->setTitle($name);
});

/**
 * Die weiteren Aktionen (im BE) sind nur notwendig, wenn die Addon-Seite selbst
 * aufgerufen wird.
 */
if (rex_be_controller::getCurrentPagePart(1) !== $addon->getName()) {
    return;
}

/**
 * JS für die BE-Seite des Addons einbinden
 */
rex_view::addJsFile($addon->getAssetsUrl('bqc.js'));
