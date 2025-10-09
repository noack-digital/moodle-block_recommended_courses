# GitHub Upload-Anleitung für v0.2.0

## Status
✅ **Commit erstellt:** Release v0.2.0  
✅ **Git-Tag erstellt:** v0.2.0  
⏳ **Upload zu GitHub:** Authentifizierung erforderlich

---

## Option 1: GitHub CLI (Empfohlen)

```bash
# GitHub CLI installieren
sudo snap install gh

# Mit GitHub authentifizieren
gh auth login

# Pushen
cd /home/anoack/block_empfohlene_kurse/block_empfohlene_kurse-1
git push origin main
git push origin v0.2.0
```

---

## Option 2: SSH-Key verwenden

```bash
# 1. SSH-Key zu GitHub hinzufügen (falls noch nicht vorhanden)
# Neuen SSH-Key generieren (falls keiner existiert)
ssh-keygen -t ed25519 -C "alexander.noack@example.com"

# SSH-Key anzeigen
cat ~/.ssh/id_ed25519.pub

# Diesen Key zu GitHub hinzufügen: https://github.com/settings/keys

# 2. GitHub zu known_hosts hinzufügen
ssh-keyscan -t ed25519 github.com >> ~/.ssh/known_hosts

# 3. Pushen
cd /home/anoack/block_empfohlene_kurse/block_empfohlene_kurse-1
git push origin main
git push origin v0.2.0
```

---

## Option 3: Personal Access Token (HTTPS)

```bash
# 1. Personal Access Token auf GitHub erstellen:
# https://github.com/settings/tokens
# Berechtigungen: repo (Full control)

# 2. Remote-URL zurück auf HTTPS setzen
cd /home/anoack/block_empfohlene_kurse/block_empfohlene_kurse-1
git remote set-url origin https://github.com/noack-digital/block_empfohlene_kurse.git

# 3. Git Credential Helper konfigurieren
git config --global credential.helper store

# 4. Pushen (verwendet Token statt Passwort)
git push origin main
# Username: Ihr GitHub-Username
# Password: Ihr Personal Access Token (nicht Ihr GitHub-Passwort!)

git push origin v0.2.0
```

---

## Nach erfolgreichem Upload

### GitHub Release erstellen

1. Gehen Sie zu: https://github.com/noack-digital/block_empfohlene_kurse/releases
2. Klicken Sie auf "Create a new release"
3. Wählen Sie Tag: `v0.2.0`
4. Release-Titel: **Release v0.2.0 - Stable Version für Moodle 4.5/5.0**
5. Beschreibung:

```markdown
# Release v0.2.0 - Bugfixes und Moodle 4.5/5.0 Kompatibilität

Diese Version behebt **11 kritische Bugs** und ist vollständig kompatibel mit **Moodle 4.5 und 5.0**.

## 🔥 Kritische Bugfixes

1. **SQL-Query JOIN-Reihenfolge korrigiert** - Verhinderte Datenbankfehler
2. **Fehlende coursesJson bei leeren Kursen** - Dashboard konnte nicht geladen werden
3. **JavaScript AMD-Modul nicht kompiliert** - Slider funktionierte nicht

## ✨ Verbesserungen

- Fehlende global $OUTPUT Variable hinzugefügt
- Moodle 4.5 und 5.0 Kompatibilität (requires: 2024042200)
- Fehlende uniqid für mehrere Block-Instanzen
- URL-Objekte werden korrekt in Strings konvertiert
- JavaScript direkt im Template (keine Build-Abhängigkeit)
- Robustere Kursbild-Generierung
- Fehlerbehandlung für gelöschte Kurse
- Code-Bereinigung in edit_form.php

## ✅ Getestet auf

- Moodle 5.0.2+ (Build: 20250923)
- PHP 8.4.5
- MariaDB 11.4.7
- **Slider-Funktionalität vollständig getestet** ✅

## 📦 Installation

1. Laden Sie das Plugin herunter
2. Entpacken Sie es nach `[moodle]/blocks/empfohlene_kurse/`
3. Besuchen Sie Website-Administration → Benachrichtigungen
4. Fügen Sie den Block zu Ihrem Dashboard hinzu

## 🎓 Beispiel-Demo

Siehe [DEMO_KURSE.md](DEMO_KURSE.md) für 10 Beispielkurse mit Nachhaltigkeitsthemen.

---

**Status:** STABLE  
**Maturity:** BETA  
**Plugin-Version:** 2025100900
```

6. Klicken Sie auf "Publish release"

---

## Aktueller Commit-Status

```
Commit: 33b20d2
Message: Release v0.2.0 - Bugfixes und Moodle 4.5/5.0 Kompatibilität
Tag: v0.2.0
Branch: main
Remote: https://github.com/noack-digital/block_empfohlene_kurse.git
```

**Änderungen bereit zum Upload:**
- block_empfohlene_kurse.php
- version.php
- classes/output/main.php
- edit_form.php
- templates/main.mustache
- README.md
- DEMO_KURSE.md (neu)

