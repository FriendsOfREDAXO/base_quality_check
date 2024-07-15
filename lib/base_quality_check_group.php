<?php 

use rex_yform_manager_dataset;

class base_quality_check_group extends rex_yform_manager_dataset {
	
    /* Gruppe */
    /** @api */
    public function getGroup() : ?string {
        return $this->getValue("group");
    }
    /** @api */
    public function setGroup(mixed $value) : self {
        $this->setValue("group", $value);
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