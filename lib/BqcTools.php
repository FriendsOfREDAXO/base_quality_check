<?php

namespace FriendsOfRedaxo\BaseQualityCheck;

/**
 * Service-Klasse mit Tools.
 */
class BqcTools
{
    /**
     * Übersetzt einen %-Satz (Erreichnungsgrad, Füllgrad) in CSS-Klassen
     * zur farblichen Darstellung.
     */
    public static function quotaClass(int|float $quota): string
    {
        if ($quota < 25) {
            $class = 'bqc-badge-0';
        } elseif ($quota < 50) {
            $class = 'bqc-badge-25';
        } elseif ($quota < 75) {
            $class = 'bqc-badge-50';
        } elseif ($quota < 100) {
            $class = 'bqc-badge-75';
        } else {
            $class = 'bqc-badge-100';
        }
        return $class;
    }
}
