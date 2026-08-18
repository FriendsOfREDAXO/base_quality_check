<?php

/** @var rex_addon $this */

try {
    $tables = [
        rex::getTable('base_quality_check'),
        rex::getTable('base_quality_check_group'),
        rex::getTable('base_quality_check_sub_group'),
        rex::getTable('base_quality_check_log'),
    ];

    foreach ($tables as $table) {
        rex_yform_manager_table_api::removeTable($table);
        rex_sql_table::get($table)->drop();
    }
} catch (RuntimeException $exception) {
    $this->setProperty('installmsg', $exception->getMessage());
}
