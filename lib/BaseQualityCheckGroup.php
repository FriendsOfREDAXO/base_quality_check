<?php

namespace FriendsOfRedaxo\BaseQualityCheck;

use rex_yform_manager_collection;
use rex_yform_manager_dataset;

class BaseQualityCheckGroup extends rex_yform_manager_dataset
{
    /* Gruppe */
    /** @api */
    public function getGroup(): ?string
    {
        return $this->getValue('group');
    }

    /** @api */
    public function setGroup(mixed $value): self
    {
        $this->setValue('group', $value);
        return $this;
    }

    /* Status */
    /** @api */
    public function getStatus(): ?string
    {
        return $this->getValue('status');
    }

    /** @api */
    public function setStatus(mixed $value): self
    {
        $this->setValue('status', $value);
        return $this;
    }

    /**
     * Abruf der Task/Check-Datensätze für diese Gruppe
     * - Suchkriterium ist die GruppenID
     * - Gefiltert wird ggf. nach dem Status (status==1 bedeutet aktiv und ist
     *   voreingestellt)
     * - Sortierung nach Priorität.
     *
     * @api
     * @return rex_yform_manager_collection<BaseQualityCheck>
     */
    public function taskList(bool $activeOnly = true): rex_yform_manager_collection
    {
        $query = BaseQualityCheck::query();
        $alias = $query->getTableAlias();

        $query
            ->joinRelation('subgroup', 'sg')
            ->select('sg.subgroup', 'subgroupname')
            ->where($alias . '.group', $this->getId())
            ->orderBy($alias . '.prio');

        if ($activeOnly) {
            $query->where($alias . '.status', 1);
        }

        return $query->find();
    }

    /**
     * Abruf der Task/Check-Datensätze für die angegebene Gruppen-Id
     * Wenn die zugehörige Gruppe nicht existiert, wird eine leere
     * Collection zurückgegeben.
     *
     * @api
     * @return rex_yform_manager_collection<BaseQualityCheck>
     */
    public static function fetchByGroup(int $groupId, bool $activeOnly = true): rex_yform_manager_collection
    {
        $group = self::get($groupId);
        if (null === $group) {
            $taskList = rex_yform_manager_collection::fromArray([]);
        } else {
            $taskList = $group->taskList($activeOnly);
        }
        return $taskList;
    }
}
