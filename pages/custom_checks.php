<?php

use FriendsOfRedaxo\BaseQualityCheck\BaseQualityCheckGroup;
use FriendsOfRedaxo\BaseQualityCheck\BaseQualityCheckSubGroup;
use FriendsOfRedaxo\BaseQualityCheck\CustomCheckImporter;

$csrfToken = rex_csrf_token::factory('base_quality_check_custom_checks');
$message = null;
$error = null;
$action = rex_request::post('bqc_custom_action', 'string');

if (in_array($action, ['import', 'add'], true)) {
    if (!rex::getUser()?->isAdmin()) {
        $error = rex_i18n::msg('base_quality_check_error_permission');
    } elseif (!$csrfToken->isValid()) {
        $error = rex_i18n::msg('csrf_token_invalid');
    } else {
        try {
            $user = (string) rex::requireUser()->getLogin();
            if ('import' === $action) {
                $count = CustomCheckImporter::importFile(rex_request::files('check_file', 'array', []), $user);
                $message = sprintf(rex_i18n::msg('base_quality_check_custom_saved'), $count);
            } elseif ('add' === $action) {
                $count = CustomCheckImporter::importRows([[
                    'title' => rex_request::post('title', 'string'),
                    'second_title' => rex_request::post('second_title', 'string'),
                    'group' => rex_request::post('group', 'string'),
                    'subgroup' => rex_request::post('subgroup', 'int'),
                    'required_addons' => rex_request::post('required_addons', 'string'),
                    'description' => rex_request::post('description', 'string'),
                    'source' => rex_request::post('source', 'string'),
                    'links' => rex_request::post('links', 'string'),
                    'status' => 1,
                ]], $user);
                $message = sprintf(rex_i18n::msg('base_quality_check_custom_saved'), $count);
            }
        } catch (Throwable $exception) {
            $error = $exception->getMessage();
        }
    }
}

if (null !== $message) {
    echo rex_view::success($message);
}
if (null !== $error) {
    echo rex_view::error($error);
}

$subgroups = BaseQualityCheckSubGroup::query()->orderBy('prio')->find();
$groups = BaseQualityCheckGroup::query()->where('status', 1)->orderBy('prio')->find();
$groupOptions = '';
foreach ($groups as $group) {
    $groupOptions .= '<option value="' . $group->getId() . '">' . rex_escape($group->getGroup()) . '</option>';
}
$subgroupOptions = '';
foreach ($subgroups as $subgroup) {
    $subgroupOptions .= '<option value="' . $subgroup->getId() . '">' . rex_escape($subgroup->getSubgroup()) . '</option>';
}

$importBody = '
    <p>' . rex_i18n::msg('base_quality_check_import_help') . '</p>
    <p>
        <a class="btn btn-default" href="' . rex_url::addonAssets('base_quality_check', 'examples/base-quality-check-example.json') . '" download>' . rex_i18n::msg('base_quality_check_example_json') . '</a>
        <a class="btn btn-default" href="' . rex_url::addonAssets('base_quality_check', 'examples/base-quality-check-example.csv') . '" download>' . rex_i18n::msg('base_quality_check_example_csv') . '</a>
    </p>
    <form method="post" enctype="multipart/form-data" class="form-horizontal">
        ' . $csrfToken->getHiddenField() . '
        <input type="hidden" name="bqc_custom_action" value="import">
        <div class="form-group">
            <label class="col-sm-3 control-label" for="bqc-check-file">' . rex_i18n::msg('base_quality_check_file') . '</label>
            <div class="col-sm-9"><input id="bqc-check-file" class="form-control" type="file" name="check_file" accept=".json,.csv" required></div>
        </div>
        <div class="form-group"><div class="col-sm-offset-3 col-sm-9"><button class="btn btn-primary" type="submit">' . rex_i18n::msg('base_quality_check_import') . '</button></div></div>
    </form>';

$section = new rex_fragment();
$section->setVar('title', rex_i18n::msg('base_quality_check_import_title'), false);
$section->setVar('body', $importBody, false);
$importSection = $section->parse('core/page/section.php');

$exportToken = rex_csrf_token::factory('base_quality_check_export');
$exportBody = '<p>' . rex_i18n::msg('base_quality_check_export_help') . '</p><p>'
    . '<a class="btn btn-primary" href="' . rex_url::currentBackendPage(['bqc_export' => 'json'] + $exportToken->getUrlParams()) . '">' . rex_i18n::msg('base_quality_check_export_json') . '</a> '
    . '<a class="btn btn-default" href="' . rex_url::currentBackendPage(['bqc_export' => 'csv'] + $exportToken->getUrlParams()) . '">' . rex_i18n::msg('base_quality_check_export_csv') . '</a> '
    . '<a class="btn btn-default" href="' . rex_url::backendPage('yform/manager/data_edit', ['table_name' => rex::getTable('base_quality_check')]) . '">' . rex_i18n::msg('base_quality_check_edit_all') . '</a>'
    . '</p>';

$section = new rex_fragment();
$section->setVar('title', rex_i18n::msg('base_quality_check_export_title'), false);
$section->setVar('body', $exportBody, false);
$exportSection = $section->parse('core/page/section.php');

$manualBody = '
    <form method="post" class="form-horizontal">
        ' . $csrfToken->getHiddenField() . '
        <input type="hidden" name="bqc_custom_action" value="add">
        <div class="form-group"><label class="col-sm-3 control-label" for="bqc-title">' . rex_i18n::msg('base_quality_check_field_title') . '</label><div class="col-sm-9"><input id="bqc-title" class="form-control" name="title" required maxlength="191"></div></div>
        <div class="form-group"><label class="col-sm-3 control-label" for="bqc-question">' . rex_i18n::msg('base_quality_check_field_question') . '</label><div class="col-sm-9"><input id="bqc-question" class="form-control" name="second_title" required maxlength="191"></div></div>
        <div class="form-group"><label class="col-sm-3 control-label" for="bqc-group">' . rex_i18n::msg('base_quality_check_field_group') . '</label><div class="col-sm-9"><select id="bqc-group" class="form-control selectpicker" name="group">' . $groupOptions . '</select></div></div>
        <div class="form-group"><label class="col-sm-3 control-label" for="bqc-subgroup">' . rex_i18n::msg('base_quality_check_subgroup') . '</label><div class="col-sm-9"><select id="bqc-subgroup" class="form-control selectpicker" name="subgroup">' . $subgroupOptions . '</select></div></div>
        <div class="form-group"><label class="col-sm-3 control-label" for="bqc-addons">' . rex_i18n::msg('base_quality_check_field_addons') . '</label><div class="col-sm-9"><input id="bqc-addons" class="form-control" name="required_addons" maxlength="191"><p class="help-block">' . rex_i18n::msg('base_quality_check_field_addons_help') . '</p></div></div>
        <div class="form-group"><label class="col-sm-3 control-label" for="bqc-description">' . rex_i18n::msg('base_quality_check_information') . '</label><div class="col-sm-9"><textarea id="bqc-description" class="form-control" name="description" rows="5"></textarea></div></div>
        <div class="form-group"><label class="col-sm-3 control-label" for="bqc-source">' . rex_i18n::msg('base_quality_check_code') . '</label><div class="col-sm-9"><textarea id="bqc-source" class="form-control" name="source" rows="5"></textarea></div></div>
        <div class="form-group"><label class="col-sm-3 control-label" for="bqc-links">' . rex_i18n::msg('base_quality_check_links') . '</label><div class="col-sm-9"><textarea id="bqc-links" class="form-control" name="links" rows="4"></textarea></div></div>
        <div class="form-group"><div class="col-sm-offset-3 col-sm-9"><button class="btn btn-save" type="submit">' . rex_i18n::msg('base_quality_check_add') . '</button></div></div>
    </form>';

$section = new rex_fragment();
$section->setVar('title', rex_i18n::msg('base_quality_check_add_title'), false);
$section->setVar('body', $manualBody, false);
$manualSection = $section->parse('core/page/section.php');

echo '<div class="bqc-manage-layout">';
echo '  <div class="bqc-manage-column bqc-manage-main">' . $manualSection . '</div>';
echo '  <aside class="bqc-manage-column bqc-manage-transfer">' . $importSection . $exportSection . '</aside>';
echo '</div>';
