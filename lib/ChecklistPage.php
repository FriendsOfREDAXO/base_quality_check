<?php

namespace FriendsOfRedaxo\BaseQualityCheck;

use rex_fragment;

final class ChecklistPage
{
    public static function render(int $groupId): void
    {
        $group = BaseQualityCheckGroup::get($groupId);
        if (null === $group) {
            return;
        }

        $content = new rex_fragment();
        $content->setVar('tasklist', $group->taskList(), false);

        $section = new rex_fragment();
        $section->setVar('title', $group->getGroup(), false);
        $section->setVar('body', $content->parse('checklist.php'), false);
        echo $section->parse('core/page/section.php');
    }
}
