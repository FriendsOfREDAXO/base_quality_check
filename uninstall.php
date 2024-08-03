<?php
/**

 *  @var rex_addon $this
 */

try {

    $tables = [
        \rex::getTable('base_quality_check'),
        \rex::getTable('base_quality_check_group'),
        \rex::getTable('base_quality_check_sub_group')        
    ];

    // Tabellen löschen
    foreach ( $tables as $table ){
        \rex_yform_manager_table_api::removeTable($table);
        \rex_sql_table::get( $table )->drop();
    }

} catch (\RuntimeException $e) {

    $this->setProperty('installmsg', $e->getMessage() );

}
