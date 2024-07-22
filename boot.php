<?php

use FriendsOfRedaxo\BaseQualityCheck\BaseQualityCheck;
use FriendsOfRedaxo\BaseQualityCheck\BaseQualityCheckGroup;
use FriendsOfRedaxo\BaseQualityCheck\BaseQualityCheckSubGroup;

$addon = rex_addon::get('base_quality_check');

if (rex_addon::get('yform')->isAvailable() && !rex::isSafeMode()) {
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
}

rex_extension::register('PACKAGES_INCLUDED', function () {
    if (rex::getUser() && true === $this->getProperty('compile')) {
        $compiler = new rex_scss_compiler();
        $scss_files = rex_extension::registerPoint(new rex_extension_point('BE_STYLE_SCSS_FILES', [$this->getPath('scss/bqc.scss')]));
        $compiler->setScssFile($scss_files);
        $compiler->setCssFile($this->getPath('assets/bqc.css'));
        $compiler->compile();
        rex_file::copy($this->getPath('assets/bqc.css'), $this->getAssetsPath('bqc.css'));
    }
});

rex_view::addCssFile($addon->getAssetsUrl('bqc.css'));

/**
 * automatisch erzeugten Titel für die eigene Navigationsgruppe "base_addon"
 * entfernen durch bereitstellen eines leeren Textes. CSS sorgt dann für die Optik.
 */
rex_i18n::addMsg('navigation_base_addon', '');


if (isset($_GET['page']) && is_string($_GET['page']) && preg_match('/base_quality_check/', $_GET['page'])) {
    rex_view::addJsFile($addon->getAssetsUrl('bqc.js'));

    rex_extension::register('OUTPUT_FILTER', static function (rex_extension_point $ep) {
        $ep->setSubject(str_replace('class="rex-page-main', 'class="bqc-addon rex-page-main', $ep->getSubject()));
    });
}

$func = rex_request('func', 'string');
$id = rex_request('id', 'int');

if ('' !== $func && '' !== $id) {
    $sql = rex_sql::factory();
    $sql->setTable('rex_base_quality_check');
    $sql->setWhere('id = :id', ['id' => $id]);
    $sql->setValue('check', 'checktask' == $func ? 1 : 0);
    $sql->update();
}

/**
 * Menüpunkt im Hauptmenu mit zusätzlichem HTML-Container für den Füllstand
 * versehen und per Query den Füllstand ermitteln und eintragen.
 */
rex_extension::register('PAGES_PREPARED', static function ($ep) {

    $pages = $ep->getSubject();
    if (!isset($pages['base_quality_check'])) {
        return;
    }

    // Füllstand berechnen
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

    // Menüpunkt markieren
    $page = $pages['base_quality_check'];
    $name = $page->getTitle();
    $name = sprintf(
        '%s <span class="bqc-badge %s">%d %%</span>',
        $name,
        BqcTools::quotaClass($quota),
        $quota,
    );
    $page->setTitle($name);
});
