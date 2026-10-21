# Helden-Software XML-Betrachter für Nextcloud

Dieser Parser ergänzt den [XML-Betrachter für Nextcloud](https://github.com/Jungbluth11/XmlViewer) um die Unterstützung für XML-Exportdateien der [Helden-Software](https://www.helden-software.de).

## Voraussetzungen

- Eine Nextcloud-Installation ab Version 28
- Die installierte und aktivierte Nextcloud-App [XmlViewer](https://github.com/Jungbluth11/XmlViewer)
- PHP mit der SimpleXML-Erweiterung (in der Regel bereits in der Nextcloud-PHP-Installation enthalten)

## Installation

1. Installiere und aktiviere zunächst die App **XmlViewer** in Nextcloud.
2. Lade das letzte Release herunter.
3. Kopiere den Ordner `Helden` in das Parser-Verzeichnis der XmlViewer-App:

   ```text
   <nextcloud>/apps/xmlviewer/lib/Parser/Helden/
   ```

   Bei abweichender Installation kann die App stattdessen unter `custom_apps/xmlviewer` oder `extra-apps/xmlviewer` liegen.
