<?php

namespace FriendsOfRedaxo\BaseQualityCheck;

use rex;
use rex_sql;

final class ChecklistStructure
{
    public static function ensureCurrent(): void
    {
        self::ensureGroup('Frontend & UX', 1, 1, 'Frontend');
        self::ensureGroup('Backend & REDAXO', 2, 2, 'Backend');
        self::ensureGroup('Barrierefreiheit', 3);
        self::ensureGroup('SEO & Auffindbarkeit', 4);
        self::ensureGroup('Sicherheit & Datenschutz', 5);
        self::ensureGroup('Livegang & Betrieb', 6, 3, 'Live');

        foreach ([
            'HTML' => 1,
            'Bilder' => 2,
            'Semantik' => 3,
            'Performance' => 4,
            'Bedienung' => 4,
            'Formulare' => 5,
            'Technisches SEO' => 5,
            'SEO' => 6,
            'Sicherheit' => 5,
            'Inhalte & GEO' => 6,
            'Datenschutz' => 6,
            'REDAXO' => 7,
            'Inhalte' => 8,
            'Sonstiges' => 9,
        ] as $name => $priority) {
            self::ensureSubgroup($name, $priority);
        }
    }

    private static function ensureGroup(string $name, int $priority, ?int $legacyId = null, ?string $legacyName = null): void
    {
        $table = rex::getTable('base_quality_check_group');
        if (null !== $legacyId && null !== $legacyName) {
            rex_sql::factory()->setQuery(
                'UPDATE ' . $table . ' SET `group` = :name, prio = :prio, status = 1 WHERE id = :id AND `group` = :legacy',
                ['name' => $name, 'prio' => $priority, 'id' => $legacyId, 'legacy' => $legacyName],
            );
        }

        $exists = rex_sql::factory()->getArray('SELECT id FROM ' . $table . ' WHERE `group` = :name', ['name' => $name]);
        if ([] === $exists) {
            rex_sql::factory()->setTable($table)->setValues([
                'group' => $name,
                'prio' => $priority,
                'status' => 1,
            ])->insert();
        } else {
            rex_sql::factory()->setTable($table)->setValues([
                'prio' => $priority,
                'status' => 1,
            ])->setWhere('id = :id', ['id' => $exists[0]['id']])->update();
        }
    }

    private static function ensureSubgroup(string $name, int $priority): void
    {
        $table = rex::getTable('base_quality_check_sub_group');
        $exists = rex_sql::factory()->getArray('SELECT id FROM ' . $table . ' WHERE subgroup = :name', ['name' => $name]);
        if ([] === $exists) {
            rex_sql::factory()->setTable($table)->setValues([
                'subgroup' => $name,
                'prio' => $priority,
                'status' => 1,
            ])->insert();
        } else {
            rex_sql::factory()->setTable($table)->setValues([
                'prio' => $priority,
                'status' => 1,
            ])->setWhere('id = :id', ['id' => $exists[0]['id']])->update();
        }
    }
}
