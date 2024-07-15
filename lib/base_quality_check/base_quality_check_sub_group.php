<?php 
namespace FriendsOfRedaxo\BaseQualityCheck;

use rex_yform_manager_dataset;

class BaseQualityCheckSubGroup extends rex_yform_manager_dataset {
	
    /* Untergruppe */
    /** @api */
    public function getSubgroup() : ?string {
        return $this->getValue("subgroup");
    }
    /** @api */
    public function setSubgroup(mixed $value) : self {
        $this->setValue("subgroup", $value);
        return $this;
    }

    /* Status */
    /** @api */
    public function getStatus() : ?string {
        return $this->getValue("status");
    }
    /** @api */
    public function setStatus(mixed $value) : self {
        $this->setValue("status", $value);
        return $this;
    }

}?>