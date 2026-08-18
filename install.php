<?php

/** @var rex_addon $this */

use FriendsOfRedaxo\BaseQualityCheck\ChecklistCatalog;
use FriendsOfRedaxo\BaseQualityCheck\ChecklistStructure;
use FriendsOfRedaxo\BaseQualityCheck\Schema;

rex_yform_manager_table_api::importTablesets(rex_file::get(__DIR__ . '/install/tableset.json'));

$query = rex_file::get(__DIR__ . '/install/install.sql');
$query = str_replace('`rex_', '`' . rex::getTablePrefix(), $query);
rex_sql::factory()->setQuery($query);

Schema::ensureVersion2();
ChecklistStructure::ensureCurrent();
ChecklistCatalog::sync(true);
