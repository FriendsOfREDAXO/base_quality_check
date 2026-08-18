<?php

namespace FriendsOfRedaxo\BaseQualityCheck;

use rex_fragment;
use rex_i18n;

final class ChecklistReport
{
    public static function render(): void
    {
        $groups = [];
        foreach (ChecklistService::groups() as $group) {
            $groups[] = [
                'name' => $group->getGroup(),
                'checks' => $group->taskList(),
                'progress' => ChecklistService::progress($group->getId()),
            ];
        }

        $content = new rex_fragment();
        $content->setVar('groups', $groups, false);
        $content->setVar('progress', ChecklistService::progress(), false);
        $content->setVar('history', AuditLog::findRecent(), false);

        $section = new rex_fragment();
        $section->setVar('title', rex_i18n::msg('base_quality_check_report'), false);
        $section->setVar('body', $content->parse('report.php'), false);
        echo $section->parse('core/page/section.php');
    }
}
