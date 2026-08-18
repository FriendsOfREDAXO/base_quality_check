<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\BaseQualityCheck\BaseQualityCheck;
use FriendsOfRedaxo\BaseQualityCheck\ChecklistService;

/** @var rex_yform_manager_collection<BaseQualityCheck> $tasklist */
$tasklist = $this->getVar('tasklist');
$canEdit = rex::getUser()?->isAdmin() ?? false;
$csrfField = $canEdit ? rex_csrf_token::factory(ChecklistService::CSRF_ID)->getHiddenField() : '';
$markdown = rex_markdown::factory();
$markdownOptions = [
    rex_markdown::SOFT_LINE_BREAKS => false,
    rex_markdown::HIGHLIGHT_PHP => false,
];

if (0 === $tasklist->count()) {
    echo rex_view::info(rex_i18n::msg('base_quality_check_empty'));
    return;
}
?>
<div class="table-responsive">
    <table class="table table-striped table-hover bqc-table">
        <thead>
            <tr>
                <th scope="col"><?= rex_i18n::msg('base_quality_check_subgroup') ?></th>
                <th scope="col" class="bqc-status-column"><?= rex_i18n::msg('base_quality_check_status') ?></th>
                <th scope="col"><?= rex_i18n::msg('base_quality_check_check') ?></th>
                <th scope="col"><?= rex_i18n::msg('base_quality_check_details') ?></th>
            </tr>
        </thead>
        <tbody>
        <?php $currentSubgroup = null; ?>
        <?php foreach ($tasklist as $task): ?>
            <?php
            $completed = $task->isCompleted();
            $subgroup = (string) $task->getValue('subgroupname');
            $showSubgroup = $currentSubgroup !== $subgroup;
            $currentSubgroup = $subgroup;
            $detailsId = 'bqc-details-' . $task->getId();
            $details = [];

            if ('' !== trim((string) $task->getDescription())) {
                $details[] = '<h4>' . rex_i18n::msg('base_quality_check_information') . '</h4>'
                    . '<div class="bqc-richtext">' . $task->getDescription() . '</div>';
            }
            if ('' !== trim((string) $task->getSource())) {
                $details[] = '<h4>' . rex_i18n::msg('base_quality_check_code') . '</h4>'
                    . '<div class="bqc-source">' . $markdown->parse((string) $task->getSource(), $markdownOptions) . '</div>';
            }
            if ('' !== trim((string) $task->getLinks())) {
                $details[] = '<h4>' . rex_i18n::msg('base_quality_check_links') . '</h4>'
                    . '<div class="bqc-richtext">' . $task->getLinks() . '</div>';
            }
            ?>
            <tr class="<?= $completed ? 'bqc-completed' : '' ?>">
                <td><?= $showSubgroup ? rex_escape($subgroup) : '' ?></td>
                <td class="bqc-status-column">
                    <?php if ($canEdit): ?>
                        <form method="post" action="<?= rex_url::currentBackendPage() ?>" class="bqc-toggle-form">
                            <?= $csrfField ?>
                            <input type="hidden" name="bqc_action" value="toggle">
                            <input type="hidden" name="task_id" value="<?= $task->getId() ?>">
                            <button type="submit" class="btn btn-link bqc-toggle" title="<?= rex_i18n::msg($completed ? 'base_quality_check_mark_open' : 'base_quality_check_mark_done') ?>">
                                <i class="rex-icon <?= $completed ? 'fa-check-square' : 'fa-square-o' ?>" aria-hidden="true"></i>
                                <span class="sr-only"><?= rex_i18n::msg($completed ? 'base_quality_check_mark_open' : 'base_quality_check_mark_done') ?></span>
                            </button>
                        </form>
                    <?php else: ?>
                        <i class="rex-icon <?= $completed ? 'fa-check-square' : 'fa-square-o' ?>" aria-hidden="true"></i>
                        <span class="sr-only"><?= rex_i18n::msg($completed ? 'base_quality_check_done' : 'base_quality_check_open') ?></span>
                    <?php endif; ?>
                </td>
                <td>
                    <strong><?= rex_escape((string) $task->getTitle()) ?></strong>
                    <?php if ('' !== $task->getComment()): ?>
                        <span class="bqc-comment-indicator" title="<?= rex_i18n::msg('base_quality_check_comment_present') ?>">
                            <i class="rex-icon fa-comment" aria-hidden="true"></i>
                            <span class="sr-only"><?= rex_i18n::msg('base_quality_check_comment_present') ?></span>
                        </span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ([] === $details && !$canEdit && '' === $task->getComment()): ?>
                        <?= rex_escape((string) $task->getSecondTitle()) ?>
                    <?php else: ?>
                        <button type="button" class="btn btn-link bqc-details-toggle collapsed" data-toggle="collapse" data-target="#<?= $detailsId ?>" aria-expanded="false" aria-controls="<?= $detailsId ?>">
                            <i class="rex-icon fa-angle-right" aria-hidden="true"></i>
                            <?= rex_escape((string) $task->getSecondTitle()) ?>
                        </button>
                        <div id="<?= $detailsId ?>" class="collapse bqc-details">
                            <?= implode('', $details) ?>
                            <?php if ($canEdit): ?>
                                <h4><?= rex_i18n::msg('base_quality_check_comment') ?></h4>
                                <form method="post" action="<?= rex_url::currentBackendPage() ?>" class="bqc-comment-form">
                                    <?= $csrfField ?>
                                    <input type="hidden" name="bqc_action" value="save_comment">
                                    <input type="hidden" name="task_id" value="<?= $task->getId() ?>">
                                    <label class="sr-only" for="bqc-comment-<?= $task->getId() ?>"><?= rex_i18n::msg('base_quality_check_comment') ?></label>
                                    <textarea id="bqc-comment-<?= $task->getId() ?>" name="comment" class="form-control" rows="3"><?= rex_escape($task->getComment()) ?></textarea>
                                    <button type="submit" class="btn btn-save"><?= rex_i18n::msg('base_quality_check_save_comment') ?></button>
                                </form>
                            <?php elseif ('' !== $task->getComment()): ?>
                                <h4><?= rex_i18n::msg('base_quality_check_comment') ?></h4>
                                <p><?= nl2br(rex_escape($task->getComment())) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($completed && '' !== $task->getCheckedBy()): ?>
                        <div class="bqc-check-meta">
                            <?= rex_i18n::msg('base_quality_check_checked_by') ?>
                            <strong><?= rex_escape($task->getCheckedBy()) ?></strong>
                            <?php if ('' !== $task->getCheckedAt()): ?>
                                · <?= rex_escape(date('d.m.Y H:i', strtotime($task->getCheckedAt()))) ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
