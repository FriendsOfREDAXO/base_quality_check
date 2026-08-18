# Base Quality Check

Das Projekt ist fertig. Prima – und wer schaut jetzt noch einmal drüber? Der Entwickler, die Redaktion oder am besten eine dritte Person?

Base Quality Check hilft dabei, diesen letzten Qualitätscheck fest in den alltäglichen Projektablauf einzubauen. Das Add-on führt technische und inhaltliche Prüfungen übersichtlich im REDAXO-Backend zusammen und dokumentiert den jeweiligen Bearbeitungsstand. Eigene, agentur- oder projektspezifische Prüfungen können ergänzt und bestehende Prüfinhalte bei Bedarf angepasst werden.

Der mitgelieferte Basiskatalog umfasst bewusst nur **30 Prüfungen** und erhebt keinen Anspruch darauf, jeden Fachbereich bis ins kleinste Detail abzudecken. Ziel ist eine solide, zeitgemäße Website, die guten Gewissens an den Kunden übergeben werden kann – kein bis ins Letzte optimiertes Meisterstück. Das reine Durchsehen aller Prüfungen sollte bei einer normalen Website höchstens etwa **zwei Stunden** dauern; notwendige Korrekturen kommen anschließend hinzu.

Vielleicht nutzt ihr das Add-on auch einfach zur eigenen Kontrolle. Wie auch immer: Es lohnt sich vermutlich, die Checkliste bei jedem Projekt einmal durchzugehen.

Viel Spaß damit!  
Oliver

Die Checkliste unterstützt die Qualitätssicherung, ersetzt aber keine fachliche oder rechtliche Prüfung. Welche Punkte erforderlich sind, hängt vom Projekt, den eingesetzten Add-ons und den jeweiligen Rahmenbedingungen ab.

## Verwendung

Die mitgelieferten Prüfungen sind in **Frontend & UX**, **Backend & REDAXO**, **Barrierefreiheit**, **SEO & Auffindbarkeit**, **Sicherheit & Datenschutz** sowie **Livegang & Betrieb** gegliedert. Ein Klick auf das Statussymbol markiert einen Punkt als erledigt oder offen. Ausführliche Hinweise, Beispiele und Quellen lassen sich direkt am Prüfpunkt aufklappen. Zu jeder Prüfung kann ein projektbezogener Kommentar hinterlegt werden.

Der Bereich **Prüfbericht** fasst Status, prüfende Person, Prüfzeitpunkt und Kommentare aller anwendbaren Prüfungen zusammen und bietet eine druckoptimierte Ansicht.

Nur Administratoren können den Bearbeitungsstand verändern. Die Änderung erfolgt per POST und ist durch ein CSRF-Token geschützt.

Statusänderungen, Kommentare sowie importierte oder manuell gepflegte Prüfpunkte werden mit REDAXO-Login und Zeitstempel in `rex_base_quality_check_log` protokolliert. Beim Abhaken erscheinen Benutzername und Zeitpunkt zusätzlich direkt am Prüfpunkt; das vollständige Änderungsprotokoll steht im Prüfbericht.

## Wie prüfe ich eine Website?

Lege vor dem Start eine kleine, repräsentative Stichprobe fest: normalerweise die Startseite, eine typische Inhaltsseite, eine Formularseite und – falls vorhanden – einen besonderen Seitentyp wie Suche, Veranstaltung oder Produkt. Nicht jede einzelne URL muss manuell wiederholt geprüft werden.

Ein praktikabler Ablauf ohne Fehlerbehebung:

1. **10–15 Minuten automatisiert:** Lighthouse mobil auf zwei Seitentypen ausführen und einen Linkcheck starten.
2. **15 Minuten Frontend und Barrierefreiheit:** Smartphone- und Desktopansicht, 200&nbsp;% Zoom sowie eine wichtige Seite nur per Tastatur prüfen.
3. **10 Minuten Backend:** URLs, Mailversand, Backup und aktuelle Fehlerprotokolle kontrollieren.
4. **10 Minuten Auffindbarkeit:** Seitentitel, Beschreibungen, Sitemap, robots.txt, Canonicals und 404-Status stichprobenartig ansehen.
5. **10–15 Minuten Sicherheit und Livegang:** HTTPS, externe Dienste, Benutzerrechte, Produktionsmodus und sichtbare Inhaltsreste prüfen.

Automatische Tests liefern Hinweise und keine Abnahme. Eine Lighthouse-Punktzahl von 100 ist ausdrücklich nicht erforderlich. Entscheidend sind ungeklärte schwere Befunde, die tatsächliche Bedienbarkeit und ein zum Projekt passendes Ergebnis. Ist eine Prüfung mangels entsprechender Funktion nicht anwendbar, kann sie als geprüft markiert und mit einem kurzen Kommentar wie „nicht vorhanden“ dokumentiert werden.

### Nützliche Werkzeuge

- [Lighthouse und PageSpeed Insights](https://pagespeed.web.dev/) für Performance, grundlegende Barrierefreiheit, Best Practices und SEO.
- [W3C Markup Validator](https://validator.w3.org/) und [W3C Link Checker](https://validator.w3.org/checklink) für Markup und Links.
- [axe DevTools](https://www.deque.com/axe/devtools/) oder [WAVE](https://wave.webaim.org/) als ergänzender automatischer Barrierefreiheitstest.
- [Mozilla Observatory](https://observatory.mozilla.org/) und [SSL Labs](https://www.ssllabs.com/ssltest/) für HTTPS und Sicherheitsheader.
- Bei installiertem Security-Add-on dessen Seite **Security → Checks** als Sammlung weiterer externer Prüfdienste.

Für Vertiefungen eignen sich das [REDAXO-Barrierefreiheits-Cheatsheet](https://friendsofredaxo.github.io/tricks/a11y/cheatsheet), die umfangreiche [Front-End Checklist](https://github.com/thedaviddias/front-end-checklist) und die [SEO-Checkliste von Semrush](https://www.semrush.com/blog/seo-checklist/). Diese Quellen gehen bewusst deutlich tiefer als der kompakte Basiskatalog.

## Eigene und agenturspezifische Prüfpunkte

Unter **Prüfungen verwalten** können Administratoren einzelne Prüfungen manuell anlegen oder mehrere Datensätze aus JSON beziehungsweise CSV importieren. CSV-Dateien lassen sich mit Excel, LibreOffice und vergleichbaren Tabellenprogrammen bearbeiten. Das Add-on liefert für beide Formate Beispieldateien mit.

Unter **SEO & Auffindbarkeit** wird außerdem geprüft, ob der REDAXO JSON-LD Manager installiert und für die zentralen Inhalte plausibel eingerichtet ist. Strukturierte Daten ergänzen klare, aktuelle Inhalte; sie ersetzen diese nicht.

Alle Prüfpunkte können komfortabel über den verlinkten YForm Table Manager weiter gepflegt werden. Der ausgelieferte Basiskatalog liegt zentral in `install/checklist.json`; dadurch sind Inhalte, Reihenfolge und Kategorien leicht prüf- und versionierbar. JSON- und CSV-Exporte enthalten die aktuell gepflegten Prüfinhalte in stabiler Reihenfolge. Für eine allgemeine Verbesserung des Add-ons sollte ein geprüfter JSON-Export zusammen mit einer kurzen Begründung als Grundlage eines GitHub-Pull-Requests dienen. Projektstatus, Kommentare, Prüfer und Auditdaten werden bewusst nicht exportiert.

Importierte Inhalte werden wie manuell gepflegte Admin-Inhalte behandelt. HTML in Beschreibung und Links sowie Markdown im Codefeld sollten daher nur aus vertrauenswürdigen Quellen übernommen werden. Wiederholte Importe legen bewusst neue Prüfpunkte an.

## Datenmodell und laufende Pflege

Prüfungen, Hauptbereiche und Unterkategorien werden als YForm-Manager-Tabellen angelegt:

- `rex_base_quality_check`
- `rex_base_quality_check_group`
- `rex_base_quality_check_sub_group`

Die Tabellen sind standardmäßig ausgeblendet. Administratoren können sie bei Bedarf über den YForm Table Manager einblenden und projektspezifisch ergänzen. Beschreibungen und Links dürfen redaktionell gepflegtes HTML enthalten; das Feld „Quellcode“ verwendet Markdown. Die Textfelder sind bewusst nicht fest an einen WYSIWYG-Editor gekoppelt.

Über das Feld `required_addons` können Prüfungen an Add-ons gebunden werden. Kommagetrennte Namen müssen alle installiert sein, mit `|` getrennte Namen gelten als Alternativen. Beispiel: `maintenance|maintenance2,cronjob`. Nicht anwendbare Prüfungen erscheinen weder in den Listen noch in der Fortschrittsberechnung.

## Voraussetzungen

- PHP 8.3 oder neuer
- REDAXO 5.20 oder neuer
- YForm 5.0.1 oder neuer

## Inhaltliche Leitlinien

Die mitgelieferten Hinweise orientieren sich bevorzugt an Primärquellen wie [W3C/WAI](https://www.w3.org/WAI/), [MDN](https://developer.mozilla.org/), [Google Search Central](https://developers.google.com/search/docs) und der [REDAXO-Dokumentation](https://redaxo.org/doku/).

Rechtliche Hinweise – insbesondere zu Datenschutz, Cookies, Tracking und Medienrechten – sind keine Rechtsberatung und müssen für das jeweilige Projekt geprüft werden.

## Mitwirken

Fehlende, unklare oder veraltete Prüfungen können als [GitHub-Issue](https://github.com/FriendsOfREDAXO/base_quality_check/issues) gemeldet werden.

## Offene GitHub-Issues in Version 2.0

Die funktionalen Issues [#13](https://github.com/FriendsOfREDAXO/base_quality_check/issues/13), [#17](https://github.com/FriendsOfREDAXO/base_quality_check/issues/17), [#18](https://github.com/FriendsOfREDAXO/base_quality_check/issues/18), [#38](https://github.com/FriendsOfREDAXO/base_quality_check/issues/38), [#39](https://github.com/FriendsOfREDAXO/base_quality_check/issues/39), [#40](https://github.com/FriendsOfREDAXO/base_quality_check/issues/40), [#41](https://github.com/FriendsOfREDAXO/base_quality_check/issues/41) und [#42](https://github.com/FriendsOfREDAXO/base_quality_check/issues/42) sind umgesetzt. Ein aktueller Screenshot für [#7](https://github.com/FriendsOfREDAXO/base_quality_check/issues/7) wird nach Abschluss der visuellen Abnahme ergänzt.

## Vollständiger Reset

Eine eigene Einstellungsseite ist derzeit nicht nötig. Wer alle Status, Kommentare, eigenen Prüfpunkte und Protokolle verwerfen und den Auslieferungszustand wiederherstellen möchte, kann das Add-on **deinstallieren und anschließend neu installieren**.

> **Achtung:** Die Deinstallation löscht die Tabellen des Add-ons dauerhaft. Vorher sollte bei Bedarf ein Datenbank-Backup erstellt werden. Eine normale Reinstallation oder Aktualisierung ohne vorherige Deinstallation ist kein vollständiger Reset.

## Assets

Das eigene Stylesheet wird nur im REDAXO-Backend geladen. Das kleine JavaScript des Add-ons wird ausschließlich für die Druckfunktion im Prüfbericht eingebunden. Codebeispiele werden ohne zusätzliche Syntaxhervorhebungsbibliothek als native Codeblöcke ausgegeben.

## Lizenz

MIT-Lizenz, siehe [LICENSE](LICENSE).

## Autor

[Friends Of REDAXO](https://github.com/FriendsOfREDAXO)  
Projektleitung: [Oliver Kreischer](https://github.com/olien)
