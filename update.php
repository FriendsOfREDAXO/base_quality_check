<?php

/**
 * Anpassungen in der Datenbank bzw. der YForm-Felddefinition vornehmen
 * 
 * ab Version 1.8: Feldtypen optimiert
 * ab Version 1.7: Feld "source" als Markdown 
 * 
 * @var \rex_addon $this
 */


/**
 * Update 1: Feldtypen optimiert
 * 
 * Mit Version 1.8 ist die Tabellenstruktur angepasst. Feldtypen wurden passender
 * gewählt als z.B. von be_manager_relation per Default vorgeschlagen.
 * 
 * Die Änderungen betreffen sowohl die Tabelle selbst als auch die YForm-Konfiguration
 * 
 * Anpassung nur für Versionen vor 1.8
 */
if (!\rex_version::compare($this->getVersion(), '1.8', '<')) {
    return;
}

$table = \rex::getTable('base_quality_check');
\rex_sql_table::get($table)
    ->ensureColumn(new \rex_sql_column('group', 'int(11)'),'second_title')
    ->ensureColumn(new \rex_sql_column('subgroup', 'int(11)'),'group')
    ->ensureColumn(new \rex_sql_column('status', 'int(11)'),'prio')
    ->ensure();
\rex_yform_manager_table_api::setTableField(
    $table,
    [
        'type_id' => 'value',
        'db_type' => 'int',
        'name' => 'group',
    ]
);

\rex_yform_manager_table_api::setTableField(
    $table,
    [
        'type_id' => 'value',
        'db_type' => 'int',
        'name' => 'subgroup',
    ]
);
\rex_yform_manager_table_api::setTableField(
    $table,
    [
        'type_id' => 'value',
        'db_type' => 'int',
        'name' => 'status',
    ]
);

$table = \rex::getTable('base_quality_check_group');
\rex_sql_table::get($table)
    ->ensureColumn(new \rex_sql_column('status', 'int(11)'),'group')
    ->ensure();
\rex_yform_manager_table_api::setTableField(
    $table,
    [
        'type_id' => 'value',
        'db_type' => 'int',
        'name' => 'status',
    ]
);

$table = \rex::getTable('base_quality_check_sub_group');
\rex_sql_table::get($table)
    ->ensureColumn(new \rex_sql_column('status', 'int(11)'),'prio')
    ->ensure();
\rex_yform_manager_table_api::setTableField(
    $table,
    [
        'type_id' => 'value',
        'db_type' => 'int',
        'name' => 'status',
    ]
);


/**
 * Beim Übergang auf Version 1.7 oder später hat sich die Datenbank geändert.
 * Das Feld "source" ist nun inhaltlich als Markdown formatiert, nicht mehr HTML.
 *
 * Annahme: es gibt wohl niemanden, der eigene Prüfpunkte eingebaut oder die
 * Texte verändert hat.
 *
 * Daher wird in der vorhandenen Tabelle "base_quality_check" einfach die Spalte
 * "source" mit den angepassten Werten überschrieben.
 *
 * Zusätzich muss in der YForm-Definition des Tabellenfeldes der Attribut-String
 * bzgl. CKE5 entfernt werden.
 * 
 * Anpassung nur für Versionen vor 1.7
 */
if (!\rex_version::compare($this->getVersion(), '1.7', '<')) {
    return;
}

/**
 * Variablen vorbelegen.
 */
$table = \rex::getTable('base_quality_check');
$sourceField = 'source';
$jsonFilename = __DIR__ . '/install/upgrade-01-07-00.json';

/**
 * Zu ändernde Daten einlesen (JSON-Datei) und die Tabelle anpassen.
 */
$pool = \rex_file::get($jsonFilename, '[]');
/** @var list<array{id:int,source:string}> $datapool */
$datapool = json_decode($pool, true);

$sql = \rex_sql::factory();
foreach ($datapool as $item) {
    $sql->setTable($table);
    $sql->setValue($sourceField, $item[$sourceField]);
    $sql->setWhere('id = :id', [':id' => $item['id']]);
    $sql->update();
}

/**
 * In der YForm-Konfiguration das Feld "attribut" des Source-Feldes
 * auf '' setzen und einen Eingabehinweis ("notice") hinzufügen.
 */
\rex_yform_manager_table_api::setTableField(
    $table,
    [
        'type_id' => 'value',
        'name' => $sourceField,
        'attributes' => '',
        'notice' => 'Bitte Markdown-Code eingeben; Code-Blöcke z.B. mit «```php» beginnen (oder html, js, yml, ...)',
    ],
);
