# Empfohlene Kurse Block

## Beschreibung
Dieses Moodle-Plugin stellt einen Block zur Verfügung, der ausgewählte Kurse anzeigt, in die der Benutzer noch nicht eingeschrieben ist. Der Block kann zum Moodle-Dashboard hinzugefügt werden und zeigt empfohlene Kurse in einem interaktiven Slider an.

## Features
- Anzeige von ausgewählten Kursen in einem Slider
- Der Hauptkurs wird mit Bild, Titel, Beschreibung und Einschreibe-Button angezeigt
- Zusätzlich werden drei weitere Kurse als Kurskarten angezeigt
- Navigation über Pfeile links und rechts
- Konfigurierbar durch Administratoren:
  - Auswahl der anzuzeigenden Kurse über eine Suchfunktion
  - Anpassung des Block-Titels
  - Auswahl der Titelausrichtung (links, rechts, zentriert)
  - Anpassung des Button-Textes

## Installation
1. Laden Sie den Inhalt des Repositories in das Verzeichnis `/blocks/empfohlene_kurse/` Ihrer Moodle-Installation.
2. Besuchen Sie als Administrator die Seite "Website-Administration" > "Benachrichtigungen", um die Installation abzuschließen.
3. Fügen Sie den Block zu Ihrem Dashboard oder einer anderen Seite hinzu.

## Konfiguration
1. Als Administrator können Sie den Block zu Ihrem Dashboard hinzufügen und dann auf das Zahnrad-Symbol klicken, um die Blockeinstellungen zu öffnen.
2. Im Konfigurationsmenü können Sie:
   - Den Titel des Blocks anpassen
   - Die Ausrichtung des Titels auswählen
   - Den Text für den Einschreibe-Button ändern
   - Kurse für den Slider auswählen

## Anforderungen
- Moodle 4.5 oder höher (kompatibel mit Moodle 5.0)
- PHP 7.4 oder höher

## Changelog

### Version 0.2.0 (2025-10-09)
**Bug-Fixes und Verbesserungen:**
- **KRITISCHER BUG BEHOBEN:** SQL-Query JOIN-Reihenfolge korrigiert
- **KRITISCHER BUG BEHOBEN:** Fehlende coursesJson bei leeren Kursen (verhinderte Dashboard-Laden)
- **KRITISCHER BUG BEHOBEN:** JavaScript AMD-Modul nicht kompiliert (Slider funktionierte nicht)
- Fehlende globale Variable $OUTPUT hinzugefügt
- Kompatibilität mit Moodle 4.5 und 5.0 verbessert
- Fehlende uniqid für Block-Instanzen hinzugefügt
- URL-Objekte werden nun korrekt in Strings konvertiert
- JavaScript direkt im Template eingebettet (keine Build-Abhängigkeit mehr)
- Robustere Kursbild-Generierung mit Fehlerbehandlung
- Fehlerbehandlung für gelöschte Kurse hinzugefügt
- Unnötige Abhängigkeiten in edit_form.php entfernt

**Getestet auf:**
- ✅ Moodle 5.0.2+ (Build: 20250923)
- ✅ PHP 8.4.5
- ✅ MariaDB 11.4.7
- ✅ Slider-Funktionalität vollständig getestet

Siehe [DEMO_KURSE.md](DEMO_KURSE.md) für Beispielkurse mit Nachhaltigkeitsthemen

### Version 0.1.1
- Initiale Veröffentlichung

## Autor
- Alexander Noack - Hochschule für nachhaltige Entwicklung Eberswalde (HNEE)

## Lizenz
MIT - Siehe [LICENSE](LICENSE) für weitere Informationen
