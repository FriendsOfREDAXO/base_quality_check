<?php

namespace FriendsOfRedaxo\BaseQualityCheck;

use rex;
use rex_sql;

final class ChecklistExport
{
    public static function send(string $format): never
    {
        if (!rex::getUser()?->isAdmin()) {
            throw new \RuntimeException('Nur Administratoren dürfen Prüfpunkte exportieren.');
        }

        $rows = self::rows();
        $filename = 'base-quality-check-' . date('Y-m-d') . '.' . $format;
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('X-Content-Type-Options: nosniff');

        if ('json' === $format) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['checks' => $rows], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            exit;
        }

        if ('csv' !== $format) {
            throw new \InvalidArgumentException('Unbekanntes Exportformat.');
        }

        header('Content-Type: text/csv; charset=utf-8');
        $stream = fopen('php://output', 'wb');
        if (false === $stream) {
            throw new \RuntimeException('Der Export konnte nicht geöffnet werden.');
        }
        fwrite($stream, "\xEF\xBB\xBF");
        $columns = ['title', 'second_title', 'group', 'subgroup', 'required_addons', 'description', 'source', 'links', 'prio', 'status'];
        fputcsv($stream, $columns, ';');
        foreach ($rows as $row) {
            fputcsv($stream, array_map(static fn (string $column): int|string => $row[$column], $columns), ';');
        }
        fclose($stream);
        exit;
    }

    /** @return list<array<string, int|string>> */
    private static function rows(): array
    {
        return rex_sql::factory()->getArray(
            'SELECT task.title, task.second_title, LOWER(grp.`group`) AS `group`, sub.subgroup, '
            . 'task.required_addons, task.description, task.source, task.links, task.prio, task.status '
            . 'FROM ' . rex::getTable('base_quality_check') . ' AS task '
            . 'INNER JOIN ' . rex::getTable('base_quality_check_group') . ' AS grp ON grp.id = task.`group` '
            . 'INNER JOIN ' . rex::getTable('base_quality_check_sub_group') . ' AS sub ON sub.id = task.subgroup '
            . 'ORDER BY grp.prio, sub.prio, task.prio, task.id',
        );
    }
}
