<?php

namespace FriendsOfRedaxo\BaseQualityCheck;

/**
 * Service-Klasse mit Tools
 */
class BqcTools {

    /**
     * Übersetzt einen %-Satz (Erreichnungsgrad, Füllgrad) in CSS-Klassen
     * zur farblichen Darstellung
     */
    static public function quotaClass(int|float $quota) : string {
        if ($quota < 25) {
            $class = 'bqc-badge-danger';
        } elseif ($quota < 50) {
            $class = 'bqc-badge-warning';
        } elseif ($quota < 75) {
            $class = 'bqc-badge-primary';
        } elseif ($quota < 100) {
            $class = 'bqc-badge-info';
        } else {
            $class = 'bqc-badge-success';
        }
        return $class;
    }
}