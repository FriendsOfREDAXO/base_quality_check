<?php

namespace FriendsOfRedaxo\BaseQualityCheck;

use rex_addon;
use rex_yform;
use rex_yform_manager_dataset;

class BaseQualityCheck extends rex_yform_manager_dataset
{
    /**
     * BaseQualityCheck unterstellt (ohne dies in der package.yml abzuprüfen)
     * dass das Addon CKEditor-5 installiert ist. Wenn nicht, erfolgt das Fallback
     * auf textarea-Standard.
     * Dazu werden die Feldattribute ($e[5] beim textarea-Feld) entfernt.
     */
    public function getForm(): rex_yform
    {
        $yform = parent::getForm();

        if (!rex_addon::get('cke5')->isAvailable()) {
            foreach ($yform->objparams['form_elements'] as $k => &$e) {
                if ('textarea' === $e[0] && str_contains($e[5], 'cke5-editor')) {
                    $e[5] = '';
                }
            }
        }

        return $yform;
    }

    /* Status */
    /** @api */
    public function getCheck(bool $asBool = false): mixed
    {
        if ($asBool) {
            return (bool) $this->getValue('check');
        }
        return $this->getValue('check');
    }

    /** @api */
    public function setCheck(int $value = 1): self
    {
        $this->setValue('check', $value);
        return $this;
    }

    /* Titel */
    /** @api */
    public function getTitle(): ?string
    {
        return $this->getValue('title');
    }

    /** @api */
    public function setTitle(mixed $value): self
    {
        $this->setValue('title', $value);
        return $this;
    }

    /* Titel Ergänzung */
    /** @api */
    public function getSecondTitle(): ?string
    {
        return $this->getValue('second_title');
    }

    /** @api */
    public function setSecondTitle(mixed $value): self
    {
        $this->setValue('second_title', $value);
        return $this;
    }

    /* Gruppe */
    /** @api */
    public function getGroup(): ?BaseQualityCheckGroup
    {
        return $this->getRelatedDataset('group');
    }

    /* Untergruppe */
    /** @api */
    public function getSubgroup(): ?BaseQualityCheckSubGroup
    {
        return $this->getRelatedDataset('subgroup');
    }

    /* Beschreibung */
    /** @api */
    public function getDescription(bool $asPlaintext = false): ?string
    {
        if ($asPlaintext) {
            return strip_tags($this->getValue('description'));
        }
        return $this->getValue('description');
    }

    /** @api */
    public function setDescription(mixed $value): self
    {
        $this->setValue('description', $value);
        return $this;
    }

    /* Quellcode */
    /** @api */
    public function getSource(bool $asPlaintext = false): ?string
    {
        if ($asPlaintext) {
            return strip_tags($this->getValue('source'));
        }
        return $this->getValue('source');
    }

    /** @api */
    public function setSource(mixed $value): self
    {
        $this->setValue('source', $value);
        return $this;
    }

    /* Links */
    /** @api */
    public function getLinks(bool $asPlaintext = false): ?string
    {
        if ($asPlaintext) {
            return strip_tags($this->getValue('links'));
        }
        return $this->getValue('links');
    }

    /** @api */
    public function setLinks(mixed $value): self
    {
        $this->setValue('links', $value);
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

    // Beispiel einer getPrio Methode
    /** @api */
    public function getPrio(): int
    {
        // Angenommen, es gibt eine Eigenschaft $prio, die die Priorität speichert
        return $this->prio;
    }
}
