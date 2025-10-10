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

### Version 1.3.1 (2025-10-10) - STABLE RELEASE

**Neue Features:**
- 👤 **Hauptansprechpartner:** Zeigt den Kursleiter mit Profilbild an
- 📅 **Datum der letzten Bearbeitung:** Zeigt wann der Kurs zuletzt aktualisiert wurde
- ⚙️ **Flexible Kursinformationen:** Kategorie, Ansprechpartner und Datum einzeln ein-/ausblendbar
- 🖼️ **Profilbild-Option:** Profilbild des Ansprechpartners optional anzeigbar
- 🎨 **Moderne Meta-Tags:** Informationen in übersichtlichen Badges mit Icons
- 💡 **Tooltips:** Bei Mouseover über Meta-Informationen werden Erklärungen angezeigt

**Verbesserungen:**
- Automatische Ermittlung des Hauptansprechpartners (editingteacher/teacher)
- Flexgroup-Layout für Meta-Informationen mit automatischem Umbruch
- Klickbare Kontaktnamen führen zum Benutzerprofil
- Hover-Effekte auf Meta-Badges (Hintergrundfarbe ändert sich)
- Cursor: help bei Tooltips für bessere UX
- Responsive Darstellung auf mobilen Geräten

### Version 1.2.1 (2025-10-10) - STABLE RELEASE

**Neue Features:**
- 🎯 **Indikator-Punkte:** Dots unter dem Hauptkurs zeigen Anzahl und Position der Slides
- 💡 **Kursname-Tooltips:** Bei Hover über Indikator-Punkt wird Kursname angezeigt
- 🖱️ **Direkte Navigation:** Click auf Indikator-Punkt springt direkt zum Kurs
- 🎨 **Optimierte Navigationspfeile:** Außerhalb des Contents am Rand, Moodle-Blau mit weißen Icons
- 🖼️ **Volle Bildbreite:** Hauptkurs-Bilder nutzen volle Breite mit proportionaler Höhe
- 🔗 **Klickbare Kurstitel:** Titel im Haupt-Slider führen direkt zum Kurs
- 📱 **Responsive:** Navigationspfeile passen sich auf mobilen Geräten an

**Verbesserungen:**
- Hover-Effekt für Kurstitel (Unterstreichung + dunkleres Blau)
- Focus-Outline für Barrierefreiheit
- Dynamische Link-Updates beim Slider-Wechsel

### Version 1.1.0 (2025-10-09) - STABLE RELEASE

**Neue Features:**
- 🎨 **4 Layout-Modi:** Vertikal, Horizontal, Karte (zentriert), Minimal (nur Bild+Titel)
- ⏱️ **Automatisches Sliding:** 3-10 Sekunden konfigurierbar, pausiert bei Hover
- 👁️ **Ein-/Ausblendbare Elemente:** Kurskacheln und Button einzeln konfigurierbar
- 🎭 **Kein grauer Hintergrund:** Transparenter Hintergrund bei Contain-Modus
- 🖼️ **Größere Kachel-Bilder:** 180px (wie in Kursübersicht)

### Version 1.0.1 (2025-10-09) - STABLE RELEASE

**Neue Features:**
- 📐 **Zwei Layout-Modi:** Vertikal (Standard) oder Horizontal (Bild links, Inhalt rechts)
- 📂 **Kursbereich-Anzeige:** Zeigt die Kurskategorie zwischen Titel und Beschreibung
- 🖼️ **Größere Kachel-Bilder:** 180px statt 120px (wie in der Kursübersicht)
- 📱 **Responsive Design:** Horizontales Layout wird auf mobil zu vertikal

### Version 1.0.0 (2025-10-09) - STABLE RELEASE

**Neue Features:**
- 🎨 **Bildanpassungsmodus:** Cover / Contain / Fill
- 📏 **Konfigurierbare Bildhöhe:** 150-350px
- 🔲 **Anpassbarer Eckenradius:** 0-12px  
- ⚡ **Animationsgeschwindigkeit:** Keine bis Langsam
- ✨ Vollständig anpassbare Darstellung über Block-Einstellungen

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
