<?php

/**
 * Beim Übergang auf Version 1.7 oder später hat sich die Datenbank geändert.
 * Das Feld "source" ist nun inhaltlich als Markdown formatiert, nicht mehr HTML
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
 * @var rex_addon $this
 */

/**
 * Überspringen wenn nicht erforderlich
 */
if (rex_version::compare($this->getVersion(), '1.7', '>=')) {
    return;
}

/**
 * Variablen vorbelegen
 */
$table = rex::getTable('rex_base_quality_check');
$sourceField = 'source';
$jsonFilename = rex_path::addon('base_quality_check','install/upgrade-01-07-00.json');

/**
 * Zu ändernde Daten einlesen (JSON-Datei) und die Tabelle anpassen
 */
$pool = rex_file::get($jsonFilename,'[]');
/** @var list<array{id:int,source:string}> $datapool */
$datapool = json_decode($pool,true);

$sql = rex_sql::factory();
foreach( $datapool as $item) {
    $sql->setTable('rex_base_quality_check');
    $sql->setValue('source',$item[$sourceField]);
    $sql->setWhere('id = :id',[':id'=>$item['id']]);
    $sql->update();
}

/**
 * In der YForm-Konfiguration das Feld "attribut" des Source-Feldes
 * aus '' setzen
 */
\rex_yform_manager_table_api::setTableField(
    'rex_base_quality_check',
    [
        'table_name' => $table,
        'type_id' => 'value',
        'name' => $sourceField,
        'attributes' => '',
        'notice' => 'Bitte Markdown-Code eingeben; Code-Blöcke z.B. mit «```php» beginnen (oder html, js, yml, ...)',
    ]
);
