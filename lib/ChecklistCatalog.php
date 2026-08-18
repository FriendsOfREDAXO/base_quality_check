<?php

namespace FriendsOfRedaxo\BaseQualityCheck;

use JsonException;
use rex;
use rex_file;
use rex_path;
use rex_sql;
use RuntimeException;

final class ChecklistCatalog
{
    /** @var array<int, string> Letzte ausgelieferte Fragen der verbleibenden Standardprüfungen. */
    private const PREVIOUS_QUESTIONS = [
        7 => 'Ist der Debug-Modus im Livebetrieb ausgeschaltet?',
        8 => 'Sind alle im Livebetrieb nicht benötigten Add-ons deaktiviert oder entfernt?',
        9 => 'Ist ein Backup Cronjob eingerichtet?',
        10 => 'Ist das Error Log leer?',
        11 => 'Sind die URLs richtig eingetragen (System, YRewrite, URL)?',
        12 => 'Ist der Mailversand vollständig konfiguriert und mit realen Empfängern getestet?',
        13 => 'Sind öffentliche Formulare angemessen gegen automatisierten Missbrauch geschützt?',
        14 => 'Sind alle Blindtexte entfernt?',
        15 => 'Funktionieren alle Formulare?',
        16 => 'Sind einwilligungspflichtige Dienste vor Zustimmung blockiert und alle Datenschutzangaben korrekt?',
        20 => 'Haben informative Bilder einen passenden Alternativtext und dekorative Bilder alt=""?',
        23 => 'Werden passende Bildformate, Abmessungen und responsive Varianten eingesetzt?',
        24 => 'Bilder Copyright',
        26 => 'Doctype, HTML, Head, Body ',
        27 => 'Ist die Sprache (HTML lang) richtig angegeben?',
        28 => 'Hat jede Seite einen eindeutigen, verständlichen und zum Inhalt passenden Seitentitel?',
        29 => 'Wird die sitemap.xml vollständig und wie vorgesehen erzeugt?',
        34 => 'Sind Benutzerrollen und Benutzeraccounts eingerichtet und getestet?',
        35 => 'Gibt es eine hilfreiche Fehlerseite, die tatsächlich den HTTP-Status 404 liefert?',
        37 => 'Ist der Viewport richtig angegeben?',
        39 => 'Funktionieren alle Links (Linkchecker)?',
        43 => 'Sind Open-Graph- und Social-Card-Angaben vollständig und mit echten Zielplattformen getestet?',
        45 => 'Sind die notwendigen HTTP-Sicherheitsheader projektspezifisch eingerichtet und getestet?',
        46 => 'Sind alle Funktionen per Tastatur erreichbar und ist der Fokus jederzeit sichtbar und logisch geführt?',
        47 => 'Sind Landmarken, Überschriften, Listen, Links und Bedienelemente semantisch korrekt eingesetzt?',
        50 => 'Sind Ladezeit, Core Web Vitals und Laufzeitverhalten auf realistischen Geräten und Verbindungen geprüft?',
        52 => 'Bleiben Inhalte bei Kontrastanforderungen, Textvergrößerung, Reflow und angepassten Textabständen lesbar und bedienbar?',
        53 => 'Sind Videos, Audios, Animationen und automatisch bewegte Inhalte barrierearm umgesetzt?',
        56 => 'Sind zentrale Inhalte eindeutig, aktuell, belegbar und so strukturiert, dass Menschen und Suchsysteme sie zuverlässig einordnen können?',
        59 => 'Sind REDAXO, Add-ons und weitere produktive Abhängigkeiten aktuell und auf bekannte Schwachstellen geprüft?',
    ];

    /** @var array<int, string> Ursprüngliche Fragen aus Version 1.x, sofern abweichend. */
    private const LEGACY_QUESTIONS = [
        7 => 'Ist der Debug Modus ausgeschaltet?',
        8 => 'Sind alle ungenutzen AddOns deinstalliert und gelöscht?',
        12 => 'Ist im AddOn PHPMailer der Mailer Typ auf SMTP eingestellt, die Zugangsdaten eingegeben und wurde alles und getestet?',
        13 => 'Sind YForm Formulare gegen SPAM geschützt?',
        16 => 'Muß auf Cookies hingewiesen werden?',
        20 => 'Werden Alt und Title Tag richtig genutzt?',
        23 => 'Werden - wenn möglich -  moderne Formate wie z.B. WEBP genutzt?',
        28 => 'Wird das Title-Tag richtig genutzt?',
        29 => 'Wir die sitemap.xml wie gewünscht erzeugt?',
        35 => 'Ist eine 404er Seite eingerichtet?',
        43 => 'Sind die Social Media Meta-Tags richtig?',
    ];

    /** @var array<int, list<string>> Zwischenstände aus der Entwicklung von Version 2.0. */
    private const INTERIM_QUESTIONS = [
        43 => [
            'Sind vorgesehene Social-Media-Vorschauen vollständig und plausibel?',
        ],
        56 => [
            'Sind zentrale Inhalte klar, aktuell, glaubwürdig und logisch miteinander verknüpft?',
        ],
    ];

    /** @var array<int, string> Ersetzte ausgelieferte Standardprüfungen. */
    private const RETIRED_QUESTIONS = [
        17 => 'Gibt es ungenutzte Module die gelöscht werden können?',
        18 => 'Gibt es ungenutzte Templates die gelöscht werden können?',
        19 => 'Sind alle ungenutzten Dateien (Medienpool / Server) entfernt?',
        25 => 'Ist der Live Mode aktiviert?',
        30 => 'Ist die robots.txt korrekt und blockiert sie keine benötigten Ressourcen?',
        31 => 'Vorsicht bei der Kopie eines Projektes. Bist Du sicher, dass keine "alten" Inhalte mehr vorhanden sind?',
        32 => 'Ist der Wartungsmodus für den Livebetrieb ausgeschaltet?',
        33 => 'Soll optional eine humans.txt veröffentlicht werden?',
        36 => 'Funktioniert die Website bei unterschiedlichen Viewports, Zoomstufen und Eingabemethoden?',
        38 => 'Hat jede relevante Seite eine aussagekräftige und passende Meta-Beschreibung?',
        40 => 'Funktionieren alle Kontaktlinks (Telefon / E-Mail)?',
        41 => 'Verwenden dekorative Bilder ein leeres alt-Attribut?',
        42 => 'Wird das Canonical-Tag richtig genutzt?',
        44 => 'Ist eine aktuelle security.txt unter /.well-known/security.txt erreichbar?',
        48 => 'Sind externe Schriften, Skripte, Karten, Videos und APIs technisch und datenschutzrechtlich geprüft?',
        51 => 'Sind Beschriftungen, Pflichtfelder, Fehlermeldungen und Statusmeldungen für alle Nutzenden verständlich und programmatisch erkennbar?',
        54 => 'Wurde die Website zusätzlich zu automatisierten Tests manuell mit Tastatur und mindestens einer assistiven Technologie geprüft?',
        55 => 'Sind strukturierte Daten valide, für den sichtbaren Inhalt zutreffend und auf die tatsächlich unterstützten Typen beschränkt?',
        57 => 'Sind Namen, Adressen, Kontaktangaben, Profile und organisatorische Informationen überall konsistent und aktuell?',
        58 => 'Werden alle produktiven Inhalte ausschließlich sicher ausgeliefert und sind Weiterleitungen, Zertifikate und Mixed Content geprüft?',
    ];

    /** @var array<int, string> Ursprüngliche Fragen entfallener Prüfungen aus Version 1.x. */
    private const RETIRED_LEGACY_QUESTIONS = [
        30 => 'Wir die robots.txt wie gewünscht erzeugt?',
        32 => 'Ist der Wartrungsmodus ausgeschaltet?',
        33 => 'Ist eine humans.txt angelegt',
        36 => 'Ist die Website responsive?',
        38 => 'Wird das Description-Tag richtig genutzt?',
        41 => 'Verwenden dekorative Bilder das Null-alt (leere) Attribut verwenden.',
    ];

    /** Synchronize bundled IDs without touching imported or customized checks. */
    public static function sync(bool $freshInstall = false): void
    {
        $table = rex::getTable('base_quality_check');

        foreach (self::data() as $item) {
            $rows = rex_sql::factory()->getArray(
                'SELECT id, second_title FROM ' . $table . ' WHERE id = :id',
                ['id' => $item['id']],
            );
            $knownBundledQuestions = [self::PREVIOUS_QUESTIONS[$item['id']], $item['second_title']];
            if (isset(self::LEGACY_QUESTIONS[$item['id']])) {
                $knownBundledQuestions[] = self::LEGACY_QUESTIONS[$item['id']];
            }
            if (isset(self::INTERIM_QUESTIONS[$item['id']])) {
                array_push($knownBundledQuestions, ...self::INTERIM_QUESTIONS[$item['id']]);
            }
            if ([] !== $rows && !$freshInstall && !in_array($rows[0]['second_title'], $knownBundledQuestions, true)) {
                continue;
            }

            $values = [
                'title' => $item['title'],
                'second_title' => $item['second_title'],
                'group' => self::findRelationId('base_quality_check_group', 'group', $item['group']),
                'subgroup' => self::findRelationId('base_quality_check_sub_group', 'subgroup', $item['subgroup']),
                'description' => $item['description'],
                'source' => $item['source'],
                'links' => $item['links'],
                'prio' => $item['prio'],
                'required_addons' => $item['required_addons'],
            ];

            $sql = rex_sql::factory()->setTable($table)->setValues($values);
            if ([] === $rows) {
                $sql->setValue('id', $item['id'])->setValue('status', 1)->setValue('check', 0)->insert();
            } else {
                $sql->setWhere('id = :id', ['id' => $item['id']])->update();
            }
        }

        $deletedIds = [];
        foreach (self::RETIRED_QUESTIONS as $id => $question) {
            $rows = rex_sql::factory()->getArray(
                'SELECT second_title FROM ' . $table . ' WHERE id = :id',
                ['id' => $id],
            );
            $knownBundledQuestions = [$question];
            if (isset(self::RETIRED_LEGACY_QUESTIONS[$id])) {
                $knownBundledQuestions[] = self::RETIRED_LEGACY_QUESTIONS[$id];
            }
            if ([] !== $rows && ($freshInstall || in_array($rows[0]['second_title'], $knownBundledQuestions, true))) {
                $deletedIds[] = $id;
            }
        }

        if ([] !== $deletedIds) {
            $ids = implode(',', $deletedIds);
            rex_sql::factory()->setQuery(
                'DELETE FROM ' . rex::getTable('base_quality_check_log') . ' WHERE task_id IN (' . $ids . ')',
            );
            rex_sql::factory()->setQuery('DELETE FROM ' . $table . ' WHERE id IN (' . $ids . ')');
        }

        self::removeUnusedLegacySubgroup('Testen');
    }

    /** @return list<array{id:int,title:string,second_title:string,group:string,subgroup:string,description:string,source:string,links:string,prio:int,required_addons:string}> */
    private static function data(): array
    {
        $filename = rex_path::addon('base_quality_check', 'install/checklist.json');
        $json = rex_file::get($filename);
        if (null === $json) {
            throw new RuntimeException('Bundled checklist not found: ' . $filename);
        }

        try {
            /** @var list<array{id:int,title:string,second_title:string,group:string,subgroup:string,description:string,source:string,links:string,prio:int,required_addons:string}> $catalog */
            $catalog = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('Bundled checklist is not valid JSON.', 0, $exception);
        }

        return $catalog;
    }

    private static function findRelationId(string $tableName, string $field, string $value): int
    {
        $rows = rex_sql::factory()->getArray(
            'SELECT id FROM ' . rex::getTable($tableName) . ' WHERE `' . $field . '` = :value LIMIT 1',
            ['value' => $value],
        );
        if ([] === $rows) {
            throw new RuntimeException(sprintf('Checklist relation "%s" is missing in %s.', $value, $tableName));
        }

        return (int) $rows[0]['id'];
    }

    private static function removeUnusedLegacySubgroup(string $name): void
    {
        $subgroupTable = rex::getTable('base_quality_check_sub_group');
        $taskTable = rex::getTable('base_quality_check');
        $rows = rex_sql::factory()->getArray(
            'SELECT subgroup.id FROM ' . $subgroupTable . ' subgroup'
            . ' LEFT JOIN ' . $taskTable . ' task ON task.subgroup = subgroup.id'
            . ' WHERE subgroup.subgroup = :name AND task.id IS NULL',
            ['name' => $name],
        );
        if ([] !== $rows) {
            rex_sql::factory()->setQuery(
                'DELETE FROM ' . $subgroupTable . ' WHERE id = :id',
                ['id' => $rows[0]['id']],
            );
        }
    }
}
