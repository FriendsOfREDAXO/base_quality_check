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

    // Cronjobs löschen
    $sql = \rex_sql::factory();
    $sql->setTable( \rex::getTable( 'cronjob') );
    $sql->setWhere( 'type=:type', [':type'=>'FriendsOfRedaxo\\Geolocation\\Cronjob'] );
    $sql->delete();

} catch (\RuntimeException $e) {

    $this->setProperty('installmsg', $e->getMessage() );

}
