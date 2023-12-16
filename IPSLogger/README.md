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
* Protokollieren von Fehlern in IP-Symcon Skripten
* Ausgabe der letzen x Meldungen in eine HTML Variable
* Ausgabe der letzen Meldung in eine String Variable
* Weiterleiten der Meldungen in das IP-Symcon Log

### 2. Voraussetzungen

- IP-Symcon ab Version 5.4

### 3. Software-Installation

Die Installation erfolgt über den Module Store.

### 4. Einrichten der Instanzen in IP-Symcon

- Unter "Instanz hinzufügen" kann das 'IPSLogger'-Modul mithilfe des Schnellfilters gefunden werden.
Weitere Informationen zum Hinzufügen von Instanzen in der [Dokumentation der Instanzen](https://www.symcon.de/service/dokumentation/konzepte/instanzen/#Instanz_hinzufügen)

__Konfigurationsseite__:

Name                          | Beschreibung
----------------------------- | ---------------------------------
MessagesContextLen            | Länge für Ausgabe des Kontext 
MessagesFormatDate            | Format für Datum
MessagesMicroLen              | Anzahl der Stellen für Mikrosekunden
MessagesStyleTable            | Style für HTML Table
MessagesStyleColumn           | Stype for HTML Column
MessagesAddNewOnTop           | Neue Meldungen oben anhängen
MessagesOutputLimit           | Anzahl der Meldungen

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
IPSLogger.Clear               | Profile für das Löschen der Log Meldungen

### 6. PHP-Befehlsreferenz

Name                          | Beschreibung
----------------------------- | ---------------------------------
IPSLogger_LogFat              | Fatale Meldung 
IPSLogger_LogErr              | Fehler Meldung 
IPSLogger_LogWrn              | Warnung Meldung 
IPSLogger_LogNot              | Notifizierung
IPSLogger_LogInf              | Informative Meldung
IPSLogger_LogDbg              | Debug Meldung 
IPSLogger_LogTrc              | Trace Meldung 

Beispiel:
```IPSLogger_LogInf(12345, __file__, "Eine Meldung zum Testen ...");```

----------
### Errorhandler

Für die Integration des PHP Errorhandlers muss folgende Zeile im Skript inkludiert werden (alternativ kann die Zeile auch direkt in die Datei "__autoload.php"):
```
require_once(IPS_GetKernelDir().'/modules/.store/at.brownson.ipslogger/PhpErrorHandler.inc.php');
```

----------
### Globale Logger Funktionen

Zusätzlich steht auch noch eine Möglichkeit zur Verfügung den Logger ohne Angabe der InstanceID aufzurufen (auch diese Zeile kann alternativ direkt in die Datei "__autoload.php" eingetragen werden):
```
require_once(IPS_GetKernelDir().'/modules/.store/at.brownson.ipslogger/IPSLogger.inc.php');
```

Name                          | Beschreibung
----------------------------- | ---------------------------------
IPSLogger_Fat                 | Fatale Meldung 
IPSLogger_Err                 | Fehler Meldung 
IPSLogger_Wrn                 | Warnung Meldung 
IPSLogger_Not                 | Notifizierung
IPSLogger_Inf                 | Informative Meldung
IPSLogger_Dbg                 | Debug Meldung 
IPSLogger_Trc                 | Trace Meldung 

Beispiel:
```
IPSLogger_Inf(__file__, "Eine Meldung zum Testen ...");
```
