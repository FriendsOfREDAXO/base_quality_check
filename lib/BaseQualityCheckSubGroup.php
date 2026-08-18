<?php

namespace FriendsOfRedaxo\BaseQualityCheck;

use rex_yform_manager_dataset;

class BaseQualityCheckSubGroup extends rex_yform_manager_dataset
{
    /* Untergruppe */
    /** @api */
    public function getSubgroup(): string
    {
        return (string) $this->getValue('subgroup');
    }

    /** @api */
    public function setSubgroup(?string $value): self
    {
        $this->setValue('subgroup', $value);
        return $this;
    }

    /* Status */
    /** @api */
    public function getStatus(): int
    {
        return (int) $this->getValue('status');
    }

    /** @api */
    public function setStatus(int|bool $value): self
    {
        $this->setValue('status', (int) $value);
        return $this;
    }
}
