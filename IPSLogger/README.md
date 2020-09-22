# IPSLogger Modul for IP-Symcon

Das Modul stellt einen Logger und Errorhandler zur Verfügung.

### Inhaltverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [PHP-Befehlsreferenz](#6-php-befehlsreferenz)

### 1. Funktionsumfang

* Protokollieren von Meldungen
* Ausgabe der letzen x Meldungen in eine HTML Variable
* Ausgabe der letzen Meldung in eine String Variable
* Weiterleiten der Meldungen in das IP-Symcon Log

### 2. Voraussetzungen

- IP-Symcon ab Version 5.4

### 3. Software-Installation

* Über den Module Store das Modul ViewConnect installieren.
* Alternativ über das Module Control folgende URL hinzufügen:
`https://github.com/brownson/IPSLogger`

### 4. Einrichten der Instanzen in IP-Symcon

- Unter "Instanz hinzufügen" kann das 'IPSLogger'-Modul mithilfe des Schnellfilters gefunden werden.
    - Weitere Informationen zum Hinzufügen von Instanzen in der [Dokumentation der Instanzen](https://www.symcon.de/service/dokumentation/konzepte/instanzen/#Instanz_hinzufügen)

__Konfigurationsseite__:

Name                          | Beschreibung
----------------------------- | ---------------------------------
MessagesContextLen       | Länge für Ausgabe des Context 
MessagesFormatDate             | Format für Datum
MessagesMicroLen         | Anzahl der Stellen für Micro Sekunden
MessagesStyleTable            | Style für HTML Table
MessagesStyleColumn         | Stype for HTML Column
MessagesAddNewOnTop              | Neue Meldungen oben anhängen
MessagesOutputLimit            | Anzahl der Meldungen

### 5. Statusvariablen und Profile

Die Statusvariablen/Kategorien werden automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.

##### Statusvariablen

Name                          | Beschreibung
----------------------------- | ---------------------------------
MessagesOutput                | Liste mit Meldungen (HTML)
MessagesLogLevel              | LogLevel für Output "Meldungen"
MessagesClear                 | Alle Einträge aus der Liste löschen
LastMessageOutput             | Letzte Meldung
LastMessageLogLevel           | LogLevel für Output "Letzte Meldung"
SymconLogLevel                | LogLevel für Output "IP-Symcon Meldungen"


##### Profile:

Name                          | Beschreibung
----------------------------- | ---------------------------------
IPSLogger.LogLevel            | Profile mit unterstützten LogLevel Werten

### 6. PHP-Befehlsreferenz

Name                          | Beschreibung
----------------------------- | ---------------------------------
IPSLogger_Fat                 | Fatale Meldung 
IPSLogger_Err                 | Fehler Meldung 
IPSLogger_Wrn                 | Warnung Meldung 
IPSLogger_Not                 | Notifizierung
IPSLogger_Inf                 | Informative Meldung
IPSLogger_Dbg                 | Debug Meldung 
IPSLogger_Trc                 | Trace Meldung 


