<?php

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
        } elseif ($quota < 99) {
            $class = 'bqc-badge-info';
        } elseif (100 == $quota) {
            $class = 'bqc-badge-success';
        }
        return $class;
    }
}