
<h1>Changelog</h1>

Alle relevanten Änderungen an Base Quality Check werden in dieser Datei dokumentiert.

<h2>2.0.0-beta1 – 2026-08-18</h2>

<h3>Hinzugefügt</h3>

- REDAXO-konforme Info-Navigation mit Hilfe, Changelog und Lizenz.
- Versionsanzeige im Seitentitel.
- Deutsche und englische Übersetzungen für die Oberfläche.
- Prüfung einer veröffentlichten `security.txt` innerhalb des kompakten Sicherheitschecks ([#38](https://github.com/FriendsOfREDAXO/base_quality_check/issues/38)).
- Projektkommentare zu einzelnen Prüfungen ([#40](https://github.com/FriendsOfREDAXO/base_quality_check/issues/40)).
- Druckoptimierter Prüfbericht ([#41](https://github.com/FriendsOfREDAXO/base_quality_check/issues/41)).
- Add-on-Abhängigkeiten mit Unterstützung für alternative oder kombinierte Voraussetzungen ([#13](https://github.com/FriendsOfREDAXO/base_quality_check/issues/13)).
- Eigene Seite zum manuellen Anlegen und zum Import agentur- oder projektspezifischer Prüfungen aus JSON und CSV.
- Beispieldateien für JSON sowie Excel-kompatibles CSV.
- Änderungsprotokoll mit REDAXO-Benutzer und Zeitstempel.
- Anzeige „Geprüft von“ in Checkliste und Prüfbericht sowie Kommentarindikator in der Übersicht.
- Einklappbares Änderungsprotokoll außerhalb der Druckausgabe.
- Dynamische Hauptbereiche mit automatischer Tab- und Berichtserzeugung eingeführt.
- Standardstruktur auf Frontend & UX, Backend & REDAXO, Barrierefreiheit, SEO & Auffindbarkeit, Sicherheit & Datenschutz sowie Livegang & Betrieb erweitert.
- Standardkatalog auf 30 klar prüfbare Punkte für eine solide Website und eine Prüfdauer von höchstens etwa zwei Stunden verdichtet.
- Prüfinhalte in einer zentralen, versionierbaren JSON-Datei zusammengeführt.
- Favicons und Social-Media-Vorschauen als gemeinsamen Prüfpunkt aufgenommen; für strukturierte Daten wird die Einrichtung des REDAXO JSON-LD Managers geprüft.
- JSON- und CSV-Export der gepflegten Prüfinhalte als Grundlage für projektübergreifende Aktualisierungen und Pull Requests.

<h3>Geändert</h3>

- Mindestanforderungen auf PHP 8.3, REDAXO 5.20 und YForm 5.0.1 angehoben.
- Backend-Oberfläche vereinheitlicht, responsiv verbessert und tastaturbedienbar gemacht.
- Gemeinsame Seitenlogik für Frontend-, Backend- und Live-Prüfungen eingeführt.
- Model-Methoden typisiert und Namespace-Struktur konsolidiert.
- Feste CKEditor-5-Bindung zugunsten normaler, flexibel konfigurierbarer Textfelder entfernt ([#18](https://github.com/FriendsOfREDAXO/base_quality_check/issues/18)).
- Kompatibilität zu YForm 5 hergestellt ([#42](https://github.com/FriendsOfREDAXO/base_quality_check/issues/42)).
- README und bestehende Prüfinhalte fachlich und sprachlich überarbeitet.
- Veraltete und doppelte Standardprüfungen samt zugehöriger Protokolleinträge entfernt; Codebeispiele auf tatsächlich hilfreiche Fälle reduziert.
- Hinweise zu `robots.txt`, Alternativtexten, Meta-Angaben, Cookies und Livebetrieb präzisiert.
- „Prüfbericht“ direkt hinter den Checklisten und „Prüfungen verwalten“ rechts neben „Info“ angeordnet.
- Verwaltungsseite passend zu ihrem erweiterten Funktionsumfang in „Prüfungen verwalten“ umbenannt.
- Das Druckskript wird nur noch im Prüfbericht geladen; alte ungenutzte Assets wurden entfernt.
- Vollständigen Reset über Deinstallation und Neuinstallation dokumentiert; eine eigene Einstellungsseite ist dafür nicht erforderlich.
- Tabellenlayout der Checklisten stabilisiert, damit beim Öffnen von Detailbereichen weder Spalten noch der horizontale Seitenausschnitt springen.
- Das Prioritätsfeld der Prüfungen zeigt im YForm-Auswahlfeld den jeweiligen Titel an.
- Prism samt Theme entfernt; Codebeispiele nutzen schlanke native Codeblöcke ohne Textschatten.
- Kompakte Tab-Bezeichnungen eingeführt, während Seitenüberschriften und Bericht die vollständigen Bereichsnamen beibehalten.
- Prüfbericht mit Statusübersicht, Fortschrittskarten, klaren Bereichsblöcken und besser erkennbaren Prüfzuständen neu gestaltet.
- Änderungsprotokoll im Bericht standardmäßig eingeklappt und vollständig vom Druck ausgeschlossen.
- „Prüfungen verwalten“ als responsive Zweispalten-Ansicht für das Anlegen sowie den Import und Export von Prüfinhalten neu gegliedert.

<h3>Behoben</h3>

- Statusänderungen sind nur noch per POST, mit CSRF-Schutz und ausschließlich für Administratoren möglich.
- Division durch null bei leeren Prüfgruppen verhindert.
- Dynamische Inhalte und Attribute der Oberfläche werden korrekt maskiert.
- CKEditor-Fallback gegen fehlende oder anders aufgebaute Formelemente abgesichert.
- Falsche bzw. unvollständige Rückgabetypen in den YForm-Modellen korrigiert.
- Statusänderungen auf Administratoren beschränkt ([#39](https://github.com/FriendsOfREDAXO/base_quality_check/issues/39)).
- Rückgabetypen und Fragment-Ausgaben für RexStan bereinigt ([#17](https://github.com/FriendsOfREDAXO/base_quality_check/issues/17)).

<h2>1.8.2 – 2024-09-23</h2>

- Seite nach einer Statusänderung per JavaScript neu geladen ([#33](https://github.com/FriendsOfREDAXO/base_quality_check/issues/33)).

<h2>1.8.1 – 2024</h2>

- Darstellung der Fortschrittsanzeige verbessert ([#32](https://github.com/FriendsOfREDAXO/base_quality_check/issues/32)).
- Live-Mode-Unterstützung ergänzt ([#31](https://github.com/FriendsOfREDAXO/base_quality_check/issues/31)).

<h2>1.8.0 – 2024-08-14</h2>

- Datenbank- und YForm-Feldtypen optimiert.
- Mindestversion auf REDAXO 5.17 angehoben.

<h2>1.7.0 – 2024-08-06</h2>

- Syntaxhervorhebung für Markdown-Codebeispiele ergänzt.
- Fallback für Installationen ohne CKEditor 5 ergänzt.

<h2>1.0.0 – 2024-07-11</h2>

- Erste Veröffentlichung.
