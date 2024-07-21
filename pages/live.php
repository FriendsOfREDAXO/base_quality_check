<?php

$group = 3;
$page = rex_be_controller::getCurrentPageObject();
$pagetitle = $page->getTitle();

$page_fragment = new rex_fragment();
$page_fragment->setVar('group',$group,false);
$page_fragment->setVar('pagetitle',$pagetitle,false);

$fragment = new \rex_fragment();
$fragment->setVar('title', $pagetitle, false);
$fragment->setVar('body', $page_fragment->parse('pagecontent.php'), false);
echo $fragment->parse('core/page/section.php');

