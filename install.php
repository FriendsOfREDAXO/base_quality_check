<?php


if (rex_addon::get('yform') && rex_addon::get('yform')->isAvailable()) {
    rex_yform_manager_table_api::importTablesets(rex_file::get(__DIR__ . '/install/tableset.json'));

    $query = rex_file::get(__DIR__ . '/install/install.sql');
    rex_sql::factory()->setQuery($query);

}



