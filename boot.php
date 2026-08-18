<?php

use FriendsOfRedaxo\BaseQualityCheck\BaseQualityCheck;
use FriendsOfRedaxo\BaseQualityCheck\BaseQualityCheckGroup;
use FriendsOfRedaxo\BaseQualityCheck\BaseQualityCheckSubGroup;
use FriendsOfRedaxo\BaseQualityCheck\AuditLog;
use FriendsOfRedaxo\BaseQualityCheck\BqcTools;
use FriendsOfRedaxo\BaseQualityCheck\ChecklistService;

$addon = rex_addon::get('base_quality_check');

if (rex::isFrontend()) {
    return;
}

rex_view::addCssFile($addon->getAssetsUrl('bqc-v2.css'));

rex_yform_manager_dataset::setModelClass(
    'rex_base_quality_check',
    BaseQualityCheck::class,
);

// Auch Änderungen über den YForm Table Manager nachvollziehbar halten.
if (rex::getUser()?->isAdmin() && str_starts_with(rex_be_controller::getCurrentPage(), 'yform/manager')) {
    foreach (['YFORM_DATA_ADDED' => 'created', 'YFORM_DATA_UPDATED' => 'updated', 'YFORM_DATA_DELETED' => 'deleted'] as $extensionPoint => $action) {
        rex_extension::register($extensionPoint, static function (rex_extension_point $ep) use ($action): void {
            $table = (string) $ep->getParam('table');
            $data = $ep->getParam('data');
            $user = (string) rex::requireUser()->getLogin();

            if (rex::getTable('base_quality_check') === $table && $data instanceof BaseQualityCheck) {
                AuditLog::record($data->getId(), $action, $user, $data->getTitle());
            } elseif (rex::getTable('base_quality_check_group') === $table && $data instanceof BaseQualityCheckGroup) {
                AuditLog::record(0, 'group', $user, rex_i18n::msg('base_quality_check_field_group') . ': ' . $data->getGroup());
            } elseif (rex::getTable('base_quality_check_sub_group') === $table && $data instanceof BaseQualityCheckSubGroup) {
                AuditLog::record(0, 'category', $user, rex_i18n::msg('base_quality_check_subgroup') . ': ' . $data->getSubgroup());
            }
        });
    }
}
rex_yform_manager_dataset::setModelClass(
    'rex_base_quality_check_group',
    BaseQualityCheckGroup::class,
);
rex_yform_manager_dataset::setModelClass(
    'rex_base_quality_check_sub_group',
    BaseQualityCheckSubGroup::class,
);

rex_extension::register('PAGES_PREPARED', static function (): void {
    $page = rex_be_controller::getPageObject('base_quality_check');
    if (null === $page) {
        return;
    }

    $staticPages = $page->getSubpages();
    $subpages = [];
    foreach (ChecklistService::groups() as $group) {
        $key = ChecklistService::pageKey($group->getId());
        $subpages[] = (new rex_be_page($key, rex_escape(ChecklistService::navigationTitle($group->getGroup()))))
            ->setSubPath(rex_path::addon('base_quality_check', 'pages/checklist.php'));
    }
    foreach (['report', 'info', 'custom_checks'] as $key) {
        if (isset($staticPages[$key])) {
            $subpages[] = $staticPages[$key];
        }
    }
    $page->setSubpages($subpages);

    $progress = ChecklistService::progress();

    $name = sprintf(
        '%s <span class="bqc-badge %s">%d %%</span>',
        $page->getTitle(),
        BqcTools::quotaClass($progress['quota']),
        $progress['quota'],
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

$subpage = rex_be_controller::getCurrentPagePart(2);

if ('report' === $subpage) {
    rex_view::addJsFile($addon->getAssetsUrl('bqc-v2.js'));
}
