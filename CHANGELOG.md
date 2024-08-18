
Base Quality Check - Changelog
================================================================================

## Version 1.8.1 xx.xx.2024 (WiP)

- Optimiert die Darstellung der Füllstandsanzeige (siehe #34)
...

## Version 1.8.0 14.08.2024

Datentypen in der Datenbank optimert (`int` statt `text`); Versionen vor 1.8.0 werden
automatisch aktualisiert.

Fehlende Klasse `form-control` im YForm-Formular ergänzt.

Umstellung auf rex_loader-API und Redaxo-Mindestversion 5.17.0 (passend zu YForm)

## Version 1.7.0 06.08.2024

Source-Code in der Detailbeschreibung einzelner Checks wird nun sprachspezifisch farbig
dargestellt (Syntax-Hervorhebung). Dazu werden beim ersten Update auf eine Version 1.7+
die jeweiligen Tests im Feld "source" aktualisiert (#26).

Die Bindung der Textfelder im Formular zur Tabelle rex_base_quality_check an den
CKE5-Editor wird automatisch aufgehoben, wenn das CKE5-Addon nicht verfügbar ist. (#21)

## Version 1.6.0. 22.07.2024

add installer action, publish to redaxo.org. Danke an [skerbis](https://github.com/skerbis)


## Version 1.5.0  - 22.07.2024

- Füllstandsanzeige nur mit PHP, kein JS. Danke an [christophboecker](https://github.com/christophboecker)

## Version 1.4.0  - 21.07.2024

- Namespace / PSR angleichen. Danke an [christophboecker](https://github.com/christophboecker)

## Version 1.3.2 - 17.07.2024

- Fehlerbehbung
- LICENSE geändert 

## Version 1.3.1 - 15.07.2024

- Fehlerbehbung
- Änderung der README

## Version 1.3.0 - 15.07.2024

- Nutzung von Namespaces. Danke an [skerbis](https://github.com/skerbis)
- Anpassung Light Mode

## Version 1.2.3 - 15.07.2024

- weitere Checks
- Vorbereitung für den Umzug zu FOR

## Version 1.2.2 - 15.07.2024

- weitere Checks

## Version 1.2.1 - 13.07.2024

- kleinere Änderungen
- neue Status Icons

## Version 1.2.0 - 12.07.2024

- Hover und Title Text bei den Status Icons angepasst. Danke an [Norbert](https://github.com/tyrant88)
- Readme angepasst

## Version 1.1.0 - 12.07.2024

- weitere Test hinzugefügt
- Nutzung eines Fragments bei den Seite 
- Codeoptimierungen
- live_mode: false hinzugefügt. Danke an [skerbis](https://github.com/skerbis)
- Readme umgeschrieben
- Changelog via Tab anzeigbar

## Version 1.0.0 - 11.07.2024

- erste Version