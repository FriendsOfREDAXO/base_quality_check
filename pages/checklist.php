<?php

use FriendsOfRedaxo\BaseQualityCheck\ChecklistPage;
use FriendsOfRedaxo\BaseQualityCheck\ChecklistService;

$groupId = ChecklistService::groupIdForCurrentPage();
if (null === $groupId) {
    echo rex_view::error(rex_i18n::msg('base_quality_check_error_group'));
    return;
}

ChecklistPage::render($groupId);
