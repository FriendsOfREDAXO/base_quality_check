<?php

namespace FriendsOfRedaxo\BaseQualityCheck;

use rex;
use rex_sql;
use rex_sql_column;
use rex_sql_table;
use rex_yform_manager_table_api;

final class Schema
{
    public static function ensureVersion2(): void
    {
        $table = rex::getTable('base_quality_check');

        rex_sql_table::get($table)
            ->ensureColumn(new rex_sql_column('required_addons', 'varchar(191)', false, ''), 'subgroup')
            ->ensureColumn(new rex_sql_column('comment', 'text', true), 'check')
            ->ensureColumn(new rex_sql_column('checked_by', 'varchar(191)', true), 'comment')
            ->ensureColumn(new rex_sql_column('checked_at', 'datetime', true), 'checked_by')
            ->ensure();

        rex_sql_table::get(rex::getTable('base_quality_check_log'))
            ->ensurePrimaryIdColumn()
            ->ensureColumn(new rex_sql_column('task_id', 'int(10) unsigned', false))
            ->ensureColumn(new rex_sql_column('action', 'varchar(32)', false))
            ->ensureColumn(new rex_sql_column('user_login', 'varchar(191)', false))
            ->ensure();

        // Getrennte ALTER-Schritte sind auch bei bereits vorhandenen Logtabellen stabil.
        rex_sql_table::get(rex::getTable('base_quality_check_log'))
            ->ensureColumn(new rex_sql_column('details', 'varchar(255)', true), 'user_login')
            ->ensure();
        rex_sql_table::get(rex::getTable('base_quality_check_log'))
            ->ensureColumn(new rex_sql_column('createdate', 'datetime', false), 'details')
            ->ensure();

        rex_yform_manager_table_api::setTableField($table, [
            'type_id' => 'value',
            'type_name' => 'text',
            'db_type' => 'varchar(191)',
            'name' => 'required_addons',
            'label' => 'Benötigte Add-ons',
            'notice' => 'Kommagetrennt = alle erforderlich; Pipe = Alternativen, z. B. maintenance|maintenance2,cronjob',
        ]);
        rex_yform_manager_table_api::setTableField($table, [
            'type_id' => 'value',
            'type_name' => 'prio',
            'db_type' => 'int',
            'name' => 'prio',
            'fields' => 'title',
        ]);
        rex_yform_manager_table_api::setTableField($table, [
            'type_id' => 'value',
            'type_name' => 'text',
            'db_type' => 'varchar(191)',
            'name' => 'checked_by',
            'label' => 'Geprüft von',
            'list_hidden' => 1,
        ]);
        rex_yform_manager_table_api::setTableField($table, [
            'type_id' => 'value',
            'type_name' => 'datetime',
            'db_type' => 'datetime',
            'name' => 'checked_at',
            'label' => 'Geprüft am',
            'list_hidden' => 1,
        ]);
        rex_yform_manager_table_api::setTableField($table, [
            'type_id' => 'value',
            'type_name' => 'textarea',
            'db_type' => 'text',
            'name' => 'comment',
            'label' => 'Projektkommentar',
            'attributes' => '{"class":"form-control","rows":"4"}',
        ]);

        foreach (['description', 'links'] as $field) {
            rex_yform_manager_table_api::setTableField($table, [
                'type_id' => 'value',
                'type_name' => 'textarea',
                'db_type' => 'text',
                'name' => $field,
                'attributes' => '{"class":"form-control","rows":"8"}',
            ]);
        }

        rex_yform_manager_table_api::setTableField(rex::getTable('base_quality_check_group'), [
            'type_id' => 'value',
            'type_name' => 'prio',
            'db_type' => 'int',
            'name' => 'prio',
            'fields' => 'group',
        ]);

        $dependencies = [
            12 => 'phpmailer',
            29 => 'yrewrite',
            30 => 'yrewrite',
            32 => 'maintenance',
        ];
        foreach ($dependencies as $id => $addons) {
            rex_sql::factory()
                ->setTable($table)
                ->setValue('required_addons', $addons)
                ->setWhere('(required_addons IS NULL OR required_addons = "") AND id = :id', ['id' => $id])
                ->update();
        }
    }
}
