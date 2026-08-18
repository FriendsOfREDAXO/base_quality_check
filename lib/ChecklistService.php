<?php

namespace FriendsOfRedaxo\BaseQualityCheck;

use rex;
use rex_be_controller;
use rex_csrf_token;
use rex_i18n;
use rex_request;
use rex_response;
use rex_url;

final class ChecklistService
{
    public const CSRF_ID = 'base_quality_check_toggle';

    private const GROUP_PAGE_PREFIX = 'group_';

    /** @return rex_yform_manager_collection<BaseQualityCheckGroup> */
    public static function groups(): \rex_yform_manager_collection
    {
        return BaseQualityCheckGroup::query()
            ->where('status', 1)
            ->orderBy('prio')
            ->find();
    }

    public static function pageKey(int $groupId): string
    {
        return self::GROUP_PAGE_PREFIX . $groupId;
    }

    public static function navigationTitle(string $title): string
    {
        return match ($title) {
            'Frontend & UX' => 'Frontend',
            'Backend & REDAXO' => 'Backend',
            'SEO & Auffindbarkeit' => 'SEO & GEO',
            'Sicherheit & Datenschutz' => 'Sicherheit',
            'Livegang & Betrieb' => 'Livegang',
            default => $title,
        };
    }

    public static function isGroupPage(string $page): bool
    {
        return str_starts_with($page, self::GROUP_PAGE_PREFIX)
            && ctype_digit(substr($page, strlen(self::GROUP_PAGE_PREFIX)));
    }

    /** @return array{checked: int, total: int, quota: int} */
    public static function progress(?int $groupId = null): array
    {
        $query = BaseQualityCheck::query()->where('status', 1);

        if (null !== $groupId) {
            $query->where('group', $groupId);
        }

        $checks = $query->find()->filter(
            static fn (BaseQualityCheck $check): bool => $check->appliesToInstallation(),
        );
        $total = $checks->count();
        $checked = count(array_filter(
            $checks->toArray(),
            static fn (BaseQualityCheck $check): bool => $check->isCompleted(),
        ));

        return [
            'checked' => $checked,
            'total' => $total,
            'quota' => 0 === $total ? 0 : (int) round($checked / $total * 100),
        ];
    }

    public static function handleAction(): ?string
    {
        $action = rex_request::post('bqc_action', 'string');
        if (!in_array($action, ['toggle', 'save_comment'], true)) {
            return null;
        }

        if (!rex::getUser()?->isAdmin()) {
            return rex_i18n::msg('base_quality_check_error_permission');
        }

        if (!rex_csrf_token::factory(self::CSRF_ID)->isValid()) {
            return rex_i18n::msg('csrf_token_invalid');
        }

        $task = BaseQualityCheck::get(rex_request::post('task_id', 'int'));
        if (null === $task || !$task->isActive()) {
            return rex_i18n::msg('base_quality_check_error_task');
        }

        $user = (string) rex::requireUser()->getLogin();
        if ('toggle' === $action) {
            $completed = !$task->isCompleted();
            $task->setCheck($completed);
            $task->setCheckedBy($completed ? $user : null, $completed ? date('Y-m-d H:i:s') : null);
        } else {
            $task->setComment(trim(rex_request::post('comment', 'string')));
        }
        if (!$task->save()) {
            return rex_i18n::msg('base_quality_check_error_save');
        }

        AuditLog::record($task->getId(), 'toggle' === $action ? ($task->isCompleted() ? 'checked' : 'reopened') : 'comment', $user);

        rex_response::sendRedirect(rex_url::currentBackendPage());
        return null;
    }

    public static function groupIdForCurrentPage(): ?int
    {
        $page = rex_be_controller::getCurrentPagePart(2);
        if (!self::isGroupPage($page)) {
            return null;
        }

        $groupId = (int) substr($page, strlen(self::GROUP_PAGE_PREFIX));

        return null === BaseQualityCheckGroup::get($groupId) ? null : $groupId;
    }
}
