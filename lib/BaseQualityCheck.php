<?php

namespace FriendsOfRedaxo\BaseQualityCheck;

use rex_addon;
use rex_yform_manager_dataset;

class BaseQualityCheck extends rex_yform_manager_dataset
{
    /* Status */
    /** @api */
    public function getCheck(bool $asBool = false): int|bool
    {
        if ($asBool) {
            return (bool) $this->getValue('check');
        }
        return (int) $this->getValue('check');
    }

    /** @api */
    public function setCheck(int|bool $value = true): self
    {
        $this->setValue('check', (int) $value);
        return $this;
    }

    public function isCompleted(): bool
    {
        return (bool) $this->getValue('check');
    }

    /* Titel */
    /** @api */
    public function getTitle(): string
    {
        return (string) $this->getValue('title');
    }

    /** @api */
    public function setTitle(?string $value): self
    {
        $this->setValue('title', $value);
        return $this;
    }

    /* Titel Ergänzung */
    /** @api */
    public function getSecondTitle(): string
    {
        return (string) $this->getValue('second_title');
    }

    /** @api */
    public function setSecondTitle(?string $value): self
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
    public function getDescription(bool $asPlaintext = false): string
    {
        if ($asPlaintext) {
            return strip_tags((string) $this->getValue('description'));
        }
        return (string) $this->getValue('description');
    }

    /** @api */
    public function setDescription(?string $value): self
    {
        $this->setValue('description', $value);
        return $this;
    }

    /* Quellcode */
    /** @api */
    public function getSource(bool $asPlaintext = false): string
    {
        if ($asPlaintext) {
            return strip_tags((string) $this->getValue('source'));
        }
        return (string) $this->getValue('source');
    }

    /** @api */
    public function setSource(?string $value): self
    {
        $this->setValue('source', $value);
        return $this;
    }

    /* Links */
    /** @api */
    public function getLinks(bool $asPlaintext = false): string
    {
        if ($asPlaintext) {
            return strip_tags((string) $this->getValue('links'));
        }
        return (string) $this->getValue('links');
    }

    /** @api */
    public function setLinks(?string $value): self
    {
        $this->setValue('links', $value);
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

    public function isActive(): bool
    {
        return (bool) $this->getValue('status');
    }

    /** @api */
    public function getPrio(): int
    {
        return (int) $this->getValue('prio');
    }

    public function getComment(): string
    {
        return (string) $this->getValue('comment');
    }

    public function setComment(?string $value): self
    {
        $this->setValue('comment', $value);
        return $this;
    }

    public function getRequiredAddons(): string
    {
        return trim((string) $this->getValue('required_addons'));
    }

    public function getCheckedBy(): string
    {
        return (string) $this->getValue('checked_by');
    }

    public function getCheckedAt(): string
    {
        return (string) $this->getValue('checked_at');
    }

    public function setCheckedBy(?string $user, ?string $date): self
    {
        $this->setValue('checked_by', $user);
        $this->setValue('checked_at', $date);
        return $this;
    }

    public function appliesToInstallation(): bool
    {
        $condition = $this->getRequiredAddons();
        if ('' === $condition) {
            return true;
        }

        foreach (explode(',', $condition) as $requiredGroup) {
            $matches = false;
            foreach (explode('|', $requiredGroup) as $addonName) {
                $addonName = trim($addonName);
                if ('' !== $addonName && rex_addon::exists($addonName) && rex_addon::get($addonName)->isInstalled()) {
                    $matches = true;
                    break;
                }
            }
            if (!$matches) {
                return false;
            }
        }

        return true;
    }
}
