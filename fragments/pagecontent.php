<?php
use FriendsOfRedaxo\BaseQualityCheck\BaseQualityCheckDataset;

$group      = $this->getVar('group');
$pagetitle  = $this->getVar('pagetitle');   


$tasks = array();
$currentSubGroup = '';

$all_tasks = BaseQualityCheckDataset::query()
    ->where('status', 1, "=")
    ->where('group', $group, "=")
    ->orderBy('prio', 'ASC');


if ($all_tasks) {
    foreach ($all_tasks as $task) {
        if ($currentSubGroup != $task->getSubgroup()->subgroup) {
            $currentSubGroup = $task->getSubgroup()->subgroup;

        } else {
            $currentSubGroup = '';
        }

        $checked = '';
        if ($task->getCheck() == 1) {
    
            $checked = 'checked';
            $link = '<a class="tasklink" href="' . rex_url::currentBackendPage(['func' => 'unchecktask', 'id' => $task->getId()]) . '" title="Als unerledigt markieren">
                        <svg  xmlns="http://www.w3.org/2000/svg" class="size-6" viewBox="0 0 24 24" fill="none"  stroke="#62A959" stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round" >
                              <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 3m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                              <path d="M9 12l2 2l4 -4" />
                        </svg>

                     </a>';
        } else {
            $link = '<a class="tasklink" href="' . rex_url::currentBackendPage(['func' => 'checktask', 'id' => $task->getId()]) . '" title="Als erledigt markieren">
                        <svg  xmlns="http://www.w3.org/2000/svg" class="size-6" viewBox="0 0 24 24"  fill="none"  stroke="darkgrey"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round" >
                              <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 3m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                        </svg>                        
                    </a>';
        }

        $tasks[] = '
            <tr>
                <td style="width: 120px;">
                    ' . $currentSubGroup . '
                </td>
                <td style="width: 40px; text-align:center;">
                    ' . $link . '
                </td>
                <td style="width: 250px;">
                    <b>' . $task->getTitle() . '</b>
                </td>
                <td>';

        if ($task->getDescription() || $task->getSource() || $task->getLinks()) {
            $tasks[] = '
                <header class="collapsed ' . $checked . '" data-toggle="collapse" data-target="#collapse-' . $task->getId() . '" aria-expanded="false">
                    <div class="panel-title">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    ' . $task->getSecondTitle() . ' 
                    </div>
                </header>
                <div id="collapse-' . $task->getId() . '" class="panel-collapse collapse" aria-expanded="false" style="height: 0px; padding: 16px 8px 16px 0;">';

            if ($task->getDescription()) {
                $tasks[] = '
                    <h6 style="margin-top: 20px;opacity:50%;"><b>Infos</b></h6>
                    ' . $task->getDescription();
            }
            if ($task->getSource()) {
                $tasks[] = '
                    <h6 style="margin-top: 30px;opacity:50%;"><b>Code</b></h6>
                    '.rex_string::highlight($task->getSource());
            }
            if ($task->getLinks()) {
                $tasks[] = '
                    <h6 style="margin-top: 30px;opacity:50%;"><b>Links</b></h6>
                    ' . $task->getLinks();
            }
        } else {
            $tasks[] = '<div class="panel-title ' . $checked . ' pl-title">'.$task->getSecondTitle() . '</div>';
        }

        if ($task->getDescription() || $task->getSource() || $task->getLinks()) {
            $tasks[] = '
                </div>
            </div>';
        }

        $tasks[] = '</td>
            </tr>';

        $currentSubGroup = $task->getSubgroup()->subgroup;
    }

    echo  '
            <table class="table table-striped table-hover bqc-table">
                <thead>
                    <tr>
                        <th style="width: 120px;">Gruppe</th>
                        <th  style="width: 40px; text-align:center;">Status</th>
                        <th style="width: 250px;"></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    ' . implode($tasks) . '
                </tbody>
            </table>
        ';
}
