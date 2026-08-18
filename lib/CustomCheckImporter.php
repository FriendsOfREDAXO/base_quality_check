<?php

namespace FriendsOfRedaxo\BaseQualityCheck;

use InvalidArgumentException;
use JsonException;
use rex;
use rex_sql;

final class CustomCheckImporter
{
    private const MAX_FILE_SIZE = 2_000_000;

    /** @param array<string, mixed> $file */
    public static function importFile(array $file, string $user): int
    {
        if (UPLOAD_ERR_OK !== ($file['error'] ?? UPLOAD_ERR_NO_FILE)) {
            throw new InvalidArgumentException('Die Datei konnte nicht hochgeladen werden.');
        }
        if ((int) ($file['size'] ?? 0) > self::MAX_FILE_SIZE) {
            throw new InvalidArgumentException('Die Datei darf höchstens 2 MB groß sein.');
        }

        $path = (string) ($file['tmp_name'] ?? '');
        $extension = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
        if (!is_uploaded_file($path) || !in_array($extension, ['json', 'csv'], true)) {
            throw new InvalidArgumentException('Erlaubt sind JSON- und CSV-Dateien.');
        }

        return self::importRows('json' === $extension ? self::parseJson($path) : self::parseCsv($path), $user, 'imported');
    }

    /** @param list<array<string, mixed>> $rows */
    public static function importRows(array $rows, string $user, string $auditAction = 'created'): int
    {
        $count = 0;
        foreach ($rows as $index => $row) {
            $title = trim((string) ($row['title'] ?? ''));
            if ('' === $title) {
                throw new InvalidArgumentException('Datensatz ' . ($index + 1) . ': Das Feld "title" fehlt.');
            }

            $groupId = self::resolveGroup($row['group'] ?? 'frontend');
            $subgroupId = self::resolveSubgroup($row['subgroup'] ?? 13);
            $prio = (int) ($row['prio'] ?? 0);
            if ($prio <= 0) {
                $result = rex_sql::factory()->getArray(
                    'SELECT COALESCE(MAX(prio), 0) + 1 AS prio FROM ' . rex::getTable('base_quality_check') . ' WHERE `group` = :group',
                    ['group' => $groupId],
                );
                $prio = (int) $result[0]['prio'];
            }

            $check = BaseQualityCheck::create();
            $check->setTitle(mb_substr($title, 0, 191));
            $check->setSecondTitle(mb_substr(trim((string) ($row['second_title'] ?? $title)), 0, 191));
            $check->setValue('group', $groupId);
            $check->setValue('subgroup', $subgroupId);
            $check->setValue('required_addons', mb_substr(trim((string) ($row['required_addons'] ?? '')), 0, 191));
            $check->setDescription((string) ($row['description'] ?? ''));
            $check->setSource((string) ($row['source'] ?? ''));
            $check->setLinks((string) ($row['links'] ?? ''));
            $check->setValue('prio', $prio);
            $check->setStatus(self::toBool($row['status'] ?? true));
            $check->setCheck(false);
            $check->setComment((string) ($row['comment'] ?? ''));

            if (!$check->save()) {
                throw new InvalidArgumentException('Datensatz ' . ($index + 1) . ' konnte nicht gespeichert werden.');
            }
            AuditLog::record($check->getId(), $auditAction, $user);
            ++$count;
        }

        return $count;
    }

    /** @return list<array<string, mixed>> */
    private static function parseJson(string $path): array
    {
        try {
            $data = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new InvalidArgumentException('Ungültiges JSON: ' . $exception->getMessage(), 0, $exception);
        }
        $rows = isset($data['checks']) ? $data['checks'] : $data;
        if (!is_array($rows) || !array_is_list($rows)) {
            throw new InvalidArgumentException('JSON muss eine Liste oder ein Objekt mit dem Schlüssel "checks" enthalten.');
        }

        return array_values(array_filter($rows, 'is_array'));
    }

    /** @return list<array<string, mixed>> */
    private static function parseCsv(string $path): array
    {
        $handle = fopen($path, 'rb');
        if (false === $handle) {
            throw new InvalidArgumentException('CSV-Datei konnte nicht gelesen werden.');
        }

        $firstLine = (string) fgets($handle);
        rewind($handle);
        $delimiter = substr_count($firstLine, ';') >= substr_count($firstLine, ',') ? ';' : ',';
        $header = fgetcsv($handle, 0, $delimiter);
        if (false === $header) {
            fclose($handle);
            throw new InvalidArgumentException('CSV-Datei enthält keine Kopfzeile.');
        }
        $header = array_map(
            static fn (string $value): string => strtolower(trim($value, "\xEF\xBB\xBF \t\n\r\0\x0B")),
            $header,
        );

        $rows = [];
        while (false !== ($values = fgetcsv($handle, 0, $delimiter))) {
            if ([null] === $values || [] === array_filter($values, static fn ($value): bool => '' !== trim((string) $value))) {
                continue;
            }
            $values = array_pad($values, count($header), '');
            $row = array_combine($header, array_slice($values, 0, count($header)));
            if (false !== $row) {
                $rows[] = $row;
            }
        }
        fclose($handle);

        return $rows;
    }

    private static function resolveGroup(mixed $value): int
    {
        $normalized = strtolower(trim((string) $value));
        if (ctype_digit($normalized) && null !== BaseQualityCheckGroup::get((int) $normalized)) {
            return (int) $normalized;
        }
        $legacyNames = [
            'frontend' => 'Frontend & UX',
            'backend' => 'Backend & REDAXO',
            'live' => 'Livegang & Betrieb',
        ];
        $name = $legacyNames[$normalized] ?? trim((string) $value);
        $rows = rex_sql::factory()->getArray(
            'SELECT id FROM ' . rex::getTable('base_quality_check_group') . ' WHERE LOWER(`group`) = LOWER(:name)',
            ['name' => $name],
        );
        if ([] === $rows) {
            throw new InvalidArgumentException('Unbekannte Gruppe: ' . $normalized);
        }

        return (int) $rows[0]['id'];
    }

    private static function resolveSubgroup(mixed $value): int
    {
        if (is_numeric($value) && null !== BaseQualityCheckSubGroup::get((int) $value)) {
            return (int) $value;
        }
        $subgroup = BaseQualityCheckSubGroup::query()->where('subgroup', trim((string) $value))->findOne();
        if (null === $subgroup) {
            throw new InvalidArgumentException('Unbekannter Bereich: ' . (string) $value);
        }

        return $subgroup->getId();
    }

    private static function toBool(mixed $value): bool
    {
        return !in_array(strtolower(trim((string) $value)), ['0', 'false', 'no', 'nein', 'inaktiv'], true);
    }
}
