<?php

namespace FriendsOfRedaxo\BaseQualityCheck;

use rex;
use rex_sql;

final class AuditLog
{
    public static function record(int $taskId, string $action, string $user, string $details = ''): void
    {
        rex_sql::factory()
            ->setTable(rex::getTable('base_quality_check_log'))
            ->setValues([
                'task_id' => $taskId,
                'action' => $action,
                'user_login' => $user,
                'details' => mb_substr($details, 0, 255),
                'createdate' => date('Y-m-d H:i:s'),
            ])
            ->insert();
    }

    /** @return list<array{id: int|string, task_id: int|string, action: string, user_login: string, createdate: string, title: string}> */
    public static function findRecent(int $limit = 250): array
    {
        $limit = max(1, min(1000, $limit));

        return rex_sql::factory()->getArray(
            'SELECT log.id, log.task_id, log.action, log.user_login, log.createdate, '
            . 'COALESCE(task.title, NULLIF(log.details, ""), CONCAT("#", log.task_id)) AS title '
            . 'FROM ' . rex::getTable('base_quality_check_log') . ' AS log '
            . 'LEFT JOIN ' . rex::getTable('base_quality_check') . ' AS task ON task.id = log.task_id '
            . 'ORDER BY log.createdate DESC, log.id DESC LIMIT ' . $limit,
        );
    }
}
