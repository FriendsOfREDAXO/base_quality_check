<?php

use FriendsOfRedaxo\BaseQualityCheck\BaseQualityCheck;

$group = 2;
$page = rex_be_controller::getCurrentPageObject();
$pagetitle = $page->getTitle();

$tasklist = BaseQualityCheck::query()
    ->where('status', 1, '=')
    ->where('group', $group, '=')
    ->orderBy('prio', 'ASC')
    ->find();

$page_fragment = new rex_fragment();
$page_fragment->setVar('group', $group, false);
$page_fragment->setVar('tasklist', $tasklist);

$fragment = new \rex_fragment();
$fragment->setVar('title', $pagetitle, false);
$fragment->setVar('body', $page_fragment->parse('pagecontent.php'), false);
echo $fragment->parse('core/page/section.php');

