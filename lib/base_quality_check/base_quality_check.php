<?php 
namespace FriendsOfRedaxo\BaseQualityCheck;

use rex_yform_manager_dataset;


class BaseQualityCheckDataset extends rex_yform_manager_dataset {
	
    /* Status */
    /** @api */
    public function getCheck(bool $asBool = false) : mixed {
        if($asBool) {
            return (bool) $this->getValue("check");
        }
        return $this->getValue("check");
    }
    /** @api */
    public function setCheck(int $value = 1) : self {
        $this->setValue("check", $value);
        return $this;
    }
            
    /* Titel */
    /** @api */
    public function getTitle() : ?string {
        return $this->getValue("title");
    }
    /** @api */
    public function setTitle(mixed $value) : self {
        $this->setValue("title", $value);
        return $this;
    }

    /* Titel Ergänzung */
    /** @api */
    public function getSecondTitle() : ?string {
        return $this->getValue("second_title");
    }
    /** @api */
    public function setSecondTitle(mixed $value) : self {
        $this->setValue("second_title", $value);
        return $this;
    }

    /* Gruppe */
    /** @api */
    public function getGroup() : ?rex_yform_manager_dataset {
        return $this->getRelatedDataset("group");
    }

    /* Untergruppe */
    /** @api */
    public function getSubgroup() : ?rex_yform_manager_dataset {
        return $this->getRelatedDataset("subgroup");
    }

    /* Beschreibung */
    /** @api */
    public function getDescription(bool $asPlaintext = false) : ?string {
        if($asPlaintext) {
            return strip_tags($this->getValue("description"));
        }
        return $this->getValue("description");
    }
    /** @api */
    public function setDescription(mixed $value) : self {
        $this->setValue("description", $value);
        return $this;
    }
            
    /* Quellcode */
    /** @api */
    public function getSource(bool $asPlaintext = false) : ?string {
        if($asPlaintext) {
            return strip_tags($this->getValue("source"));
        }
        return $this->getValue("source");
    }
    /** @api */
    public function setSource(mixed $value) : self {
        $this->setValue("source", $value);
        return $this;
    }
            
    /* Links */
    /** @api */
    public function getLinks(bool $asPlaintext = false) : ?string {
        if($asPlaintext) {
            return strip_tags($this->getValue("links"));
        }
        return $this->getValue("links");
    }
    /** @api */
    public function setLinks(mixed $value) : self {
        $this->setValue("links", $value);
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

    // Beispiel einer getPrio Methode
    public function getPrio() {
        // Angenommen, es gibt eine Eigenschaft $prio, die die Priorität speichert
        return $this->prio;
    }


}?>