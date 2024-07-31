<?php

/** @var rex_fragment $this */

use FriendsOfRedaxo\BaseQualityCheck\BaseQualityCheck;

/**
 * Variablen für das Fragment
 */
/** @var int $group */
$group = $this->getVar('group', 0);

/** @var rex_yform_manager_collection<BaseQualityCheck>|array{} $tasklist */
$tasklist = $this->getVar('tasklist',[]);

$currentSubGroup = '';
$rows = [];
foreach ($tasklist as $task) {
    $column = [];
    $column[] = '<tr>';

    /**
     * Spalte 1: Name der Untergruppe mit Gruppenwechsel (nur in der ersten Zeile).
     * Also für die Folgezeilen keinen Gruppentitel mehr ausgeben.
     * Es wird unterstellt, dass es die SubGroup gibt!
     * 
     */
    $subGroup = $task->getSubgroup()->getSubgroup();
    if ($currentSubGroup !== $subGroup) {
        $currentSubGroup = $subGroup;
        $column[] = '<td>' . $currentSubGroup . '</td>';
    } else {
        $column[] = '<td></td>';
    }

    /**
     * Spalte 2: Check-Element.
     */
    if (1 == $task->getCheck()) {
        $checked = 'checked';
        $link = '<a class="tasklink" href="' . rex_url::currentBackendPage(['func' => 'unchecktask', 'id' => $task->getId()]) . '" title="Als unerledigt markieren">
                    <svg  xmlns="http://www.w3.org/2000/svg" class="size-6" viewBox="0 0 24 24" fill="none"  stroke="#62A959" stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round" >
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 3m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                            <path d="M9 12l2 2l4 -4" />
                    </svg>

                    </a>';
    } else {
        $checked = '';
        $link = '<a class="tasklink" href="' . rex_url::currentBackendPage(['func' => 'checktask', 'id' => $task->getId()]) . '" title="Als erledigt markieren">
                    <svg  xmlns="http://www.w3.org/2000/svg" class="size-6" viewBox="0 0 24 24"  fill="none"  stroke="darkgrey"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round" >
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 3m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                    </svg>
                </a>';
    }
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
        $content = rex_string::highlight($content);
        $infoset[] = '<header class="panel-heading">Code</header>
            <div class="panel-body">' . $content . '</div>';
        }

    $content = $task->getLinks();
    if ('' < $content) {
        $infoset[] = '<header class="panel-heading">Links</header>
            <div class="panel-body">' . $content . '</div>';
        }

    if (0 === count($infoset)) {
        $column[] = '<td><div class="panel-title ' . $checked . ' pl-title">' . $task->getSecondTitle() . '</div></td>';
    } else {
        $column[] = '<td>
            <header class="collapsed ' . $checked . '" data-toggle="collapse" data-target="#bqc-collapse-' . $task->getId() . '" aria-expanded="false">
                <div class="panel-title">
                    <i class="fa fa-caret-up"></i>
                ' . $task->getSecondTitle() . '
                </div>
            </header>
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
