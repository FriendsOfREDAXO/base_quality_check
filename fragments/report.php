<?php

/** @var rex_fragment $this */

/** @var list<array{name: string, checks: rex_yform_manager_collection, progress: array{checked: int, total: int, quota: int}}> $groups */
$groups = $this->getVar('groups', []);
/** @var array{checked: int, total: int, quota: int} $progress */
$progress = $this->getVar('progress');
/** @var list<array{id: int|string, task_id: int|string, action: string, user_login: string, createdate: string, title: string}> $history */
$history = $this->getVar('history', []);
$open = max(0, $progress['total'] - $progress['checked']);
?>
<div class="bqc-print-report">
    <header class="bqc-report-header">
        <div class="bqc-report-heading">
            <div>
                <h2><?= rex_i18n::msg('base_quality_check_report') ?></h2>
                <p><?= rex_i18n::msg('base_quality_check_report_created') ?>: <?= rex_escape(date('d.m.Y H:i')) ?></p>
            </div>
            <button type="button" class="btn btn-primary bqc-report-actions" data-bqc-print>
                <i class="rex-icon fa-print" aria-hidden="true"></i>
                <?= rex_i18n::msg('base_quality_check_print') ?>
            </button>
        </div>

        <div class="bqc-report-summary">
            <div class="bqc-report-stat">
                <span><?= rex_i18n::msg('base_quality_check_total') ?></span>
                <strong><?= $progress['total'] ?></strong>
            </div>
            <div class="bqc-report-stat bqc-report-stat-done">
                <span><?= rex_i18n::msg('base_quality_check_done') ?></span>
                <strong><?= $progress['checked'] ?></strong>
            </div>
            <div class="bqc-report-stat bqc-report-stat-open">
                <span><?= rex_i18n::msg('base_quality_check_open') ?></span>
                <strong><?= $open ?></strong>
            </div>
            <div class="bqc-report-progress">
                <div>
                    <span><?= rex_i18n::msg('base_quality_check_progress') ?></span>
                    <strong><?= $progress['quota'] ?> %</strong>
                </div>
                <div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= $progress['quota'] ?>">
                    <div class="progress-bar <?= $progress['quota'] >= 100 ? 'progress-bar-success' : 'progress-bar-info' ?>" style="width: <?= $progress['quota'] ?>%"></div>
                </div>
            </div>
        </div>
    </header>

    <?php foreach ($groups as $group): ?>
        <section class="panel panel-default bqc-report-group">
            <header class="panel-heading bqc-report-group-heading">
                <div>
                    <h3 class="panel-title"><?= rex_escape($group['name']) ?></h3>
                    <span><?= $group['progress']['checked'] ?> / <?= $group['progress']['total'] ?> <?= rex_i18n::msg('base_quality_check_done') ?></span>
                </div>
                <div class="progress" aria-hidden="true">
                    <div class="progress-bar <?= $group['progress']['quota'] >= 100 ? 'progress-bar-success' : 'progress-bar-info' ?>" style="width: <?= $group['progress']['quota'] ?>%"></div>
                </div>
            </header>
            <div class="table-responsive">
                <table class="table bqc-report-table">
                    <thead>
                        <tr>
                            <th scope="col"><?= rex_i18n::msg('base_quality_check_status') ?></th>
                            <th scope="col"><?= rex_i18n::msg('base_quality_check_check') ?></th>
                            <th scope="col"><?= rex_i18n::msg('base_quality_check_comment') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($group['checks'] as $check): ?>
                            <tr class="<?= $check->isCompleted() ? 'bqc-report-row-done' : 'bqc-report-row-open' ?>">
                                <td class="bqc-report-status">
                                    <span class="bqc-status-pill <?= $check->isCompleted() ? 'is-done' : 'is-open' ?>">
                                        <i class="rex-icon <?= $check->isCompleted() ? 'fa-check' : 'fa-clock-o' ?>" aria-hidden="true"></i>
                                        <?= rex_i18n::msg($check->isCompleted() ? 'base_quality_check_done' : 'base_quality_check_open') ?>
                                    </span>
                                    <?php if ($check->isCompleted() && '' !== $check->getCheckedBy()): ?>
                                        <small class="bqc-report-checked-by">
                                            <?= rex_i18n::msg('base_quality_check_checked_by') ?>
                                            <strong><?= rex_escape($check->getCheckedBy()) ?></strong>
                                            <?php if ('' !== $check->getCheckedAt()): ?>
                                                <br><?= rex_escape(date('d.m.Y H:i', strtotime($check->getCheckedAt()))) ?>
                                            <?php endif; ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td class="bqc-report-check">
                                    <strong><?= rex_escape($check->getTitle()) ?></strong>
                                    <span><?= rex_escape($check->getSecondTitle()) ?></span>
                                </td>
                                <td class="bqc-report-comment">
                                    <?php if ('' !== trim($check->getComment())): ?>
                                        <?= nl2br(rex_escape($check->getComment())) ?>
                                    <?php else: ?>
                                        <span class="text-muted">&mdash;</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php endforeach; ?>

    <section class="bqc-audit-log hidden-print">
        <button class="btn btn-default btn-block bqc-audit-toggle collapsed" type="button" data-toggle="collapse" data-target="#bqc-audit-content" aria-expanded="false" aria-controls="bqc-audit-content">
            <span>
                <i class="rex-icon fa-history" aria-hidden="true"></i>
                <?= rex_i18n::msg('base_quality_check_audit_log') ?>
                <span class="badge"><?= count($history) ?></span>
            </span>
            <i class="rex-icon fa-angle-down bqc-audit-chevron" aria-hidden="true"></i>
        </button>
        <div class="collapse" id="bqc-audit-content">
            <div class="bqc-audit-content">
                <?php if ([] === $history): ?>
                    <p><?= rex_i18n::msg('base_quality_check_audit_empty') ?></p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col"><?= rex_i18n::msg('base_quality_check_date') ?></th>
                                    <th scope="col"><?= rex_i18n::msg('base_quality_check_user') ?></th>
                                    <th scope="col"><?= rex_i18n::msg('base_quality_check_action') ?></th>
                                    <th scope="col"><?= rex_i18n::msg('base_quality_check_check') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($history as $entry): ?>
                                    <tr>
                                        <td><?= rex_escape(date('d.m.Y H:i', strtotime($entry['createdate']))) ?></td>
                                        <td><?= rex_escape($entry['user_login']) ?></td>
                                        <td><?= rex_i18n::msg('base_quality_check_action_' . $entry['action']) ?></td>
                                        <td><?= rex_escape($entry['title']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>
