<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\BaseQualityCheck\BaseQualityCheck;

/**
 * Variablen für das Fragment.
 */
/** @var int $group */
$group = $this->getVar('group', 0);

/** @var rex_yform_manager_collection<BaseQualityCheck> $tasklist */
$tasklist = $this->getVar('tasklist', []);

/**
 * Markdown-Formatierer vorbereiten.
 */
$markdown = rex_markdown::factory();
$markdownOptions = [
    rex_markdown::SOFT_LINE_BREAKS => false,
    rex_markdown::HIGHLIGHT_PHP => false,
];

$currentSubGroup = '';
$rows = [];
foreach ($tasklist as $task) {
    /**
     * Ermitteln, ob die Task erlefigt ist
     * Die Zeile der Tabelle entsprechend markieren.
     */
    $isCompleted = 1 == $task->getCheck();

    $column = [];
    $column[] = '<tr class="' . ($isCompleted ? 'bqc-completed' : '') . '">';

    /**
     * Spalte 1: Name der Untergruppe mit Gruppenwechsel (nur in der ersten Zeile).
     * Also für die Folgezeilen keinen Gruppentitel mehr ausgeben.
     * Es wird unterstellt, dass es die SubGroup gibt!
     */
    $subGroup = $task->getValue('subgroupname');
    if ($currentSubGroup !== $subGroup) {
        $currentSubGroup = $subGroup;
        $column[] = '<td>' . $currentSubGroup . '</td>';
    } else {
        $column[] = '<td></td>';
    }

    /**
     * Spalte 2: Check-Element.
     */
    $action = $isCompleted ? 'unchecktask' : 'checktask';
    $label = $isCompleted ? 'Als unerledigt markieren' : 'Als erledigt markieren';
    $link = '<a class="tasklink" href="' . rex_url::currentBackendPage(['func' => $action, 'id' => $task->getId()]) . '" title="' . $label . '">
                <svg  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round" >
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 3m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                        <path class="checkmark" d="M9 12l2 2l4 -4" />
                </svg>
             </a>';
    $column[] = '<td>' . $link . '</td>';

    /**
     * Spalte 3: Titel des Prüfelements.
     */
    $column[] = '<td>' . $task->getTitle() . '</td>';

    /**
     * Spalte 4: Komplexes Feld mit inhaltlicher Beschreibung des Prüfelements.
     */
    $infoset = [];

    $content = $task->getDescription();
    if ('' < $content) {
        $infoset[] = '<header class="panel-heading">Info</header>
            <div class="panel-body">' . $content . '</div>';
    }

    $content = $task->getSource();
    if ('' < $content) {
        $content = $markdown->parse($content, $markdownOptions);
        $infoset[] = '<header class="panel-heading">Code</header>
            <div class="panel-body">' . $content . '</div>';
        }

    $content = $task->getLinks();
    if ('' < $content) {
        $infoset[] = '<header class="panel-heading">Links</header>
            <div class="panel-body">' . $content . '</div>';
        }

    if (0 === count($infoset)) {
        $column[] = '<td><div class="bqc-tasktitle">' . $task->getSecondTitle() . '</div></td>';
    } else {
        $column[] = '<td>
            <div class="collapsed bqc-tasktitle" data-toggle="collapse" data-target="#bqc-collapse-' . $task->getId() . '" aria-expanded="false">
                ' . $task->getSecondTitle() . '
            </div>
            <div id="bqc-collapse-' . $task->getId() . '" class="panel panel-info collapse" aria-expanded="false">
                ' . implode('', $infoset) . '
            </div></td>';
    }

    $column[] = '</tr>';

    $rows[] = implode('', $column);
}

?>
<table class="table table-striped table-hover bqc-table">
    <colgroup>
        <col class="bqc-table-col1" />
        <col class="bqc-table-col2" />
        <col class="bqc-table-col3" />
        <col class="bqc-table-col4" />
    </colgroup>
    <thead>
        <tr>
            <th>Gruppe</th>
            <th>Status</th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?= implode('', $rows) ?>
    </tbody>
</table>
