# Ziele unseres Projekts

## Firmenname X Logistics

Bau einer Webseite, auf der man sich registrieren und einloggen kann. Funktion der Webseite ist die Zeiterfassung und wahlweise zusätzlich auch Buchhaltungsrechnungen. Optional lassen sich Stunden vllt. auch auf Projekte oder bestimmte Aufgaben zur gezielten Abrechnung wie bspw. bei echten Dienstleistern buchen.

## Funktionen

- Registrierung (Freischaltung wird von Admins durchgeführt - man sieht Webseite die auf Wartezeit hinweist)
- Login
- Zeiterfassung
- Suche nach Arbeitszeiten einzelner Mitarbeiter oder Tagen

## Projektziel Gruppenarbeit LF10aV2

Aufbau einer Webseite zur Zeiterfassung für die fiktive Firma “X Logistics”, einem Dienstleistungsunternehmen aus der Logistikbranche.
Mitarbeiter sollen in der Lage sein sich auf der Webseite nach der erfolgreichen Registrierung und Freischaltung, täglich ein- und ausstempeln zu können und ihre “Stundenzettel” einzusehen. Aus der Zeiterfassung heraus sollen zusätzlich Buchhaltungsrechnungen möglich sein.

## Inhaltsverzeichnis

1. Projektübersicht
    - Projektziel
    - Teammitglieder
    - Zeitplan
2. Verwendete Tools
    - Git & GitHub
    - XAMPP (Apache, MySQL, MariaDB)
    - HedgeDoc, Visual Studio Code, MailHog
3. Systemarchitektur
    - Frontend
    - Benutzeroberfläche
    - CSS-Struktur und Design
    - Backend
4. Implementierungsdetails
    - Datenbankstruktur
    - Funktionalitäten
    - Sicherheitsmaßnahmen
5. Installation und Deployment
    - Installationsanleitung
    - Systemvoraussetzungen
    - MailHog-Konfiguration
6. Fazit

## Eingesetzte Tools

### Git & Github

    - Git: Tool um Snapshots von Dateien auf dem lokalen Rechner zu speichern und bei Bedarf zu teilen (https://git-scm.com/downloads)
    - GitHub: Webseite auf der man seine Dateien für andere zugänglich hochladen und bei Bedarf mergen kann (https://github.com/)

#### XAMPP (Apache, MySQL, MariaDB)

    - Apache: Webserver zum hosten unserer erstellten Webseite
    - MySQL: Datenbankmanagementsystem für relationale Datenbanken
    - MariaDB: ebenfalls ein Datenbankmanagementsystem für relationale Datenbanken
    #### HedgeDoc
    - OpenSource Markdown Editor, webbasiert, selbstgehostet - geeignet für gemeinsames Arbeiten in Echtzeit an Notizen, Graphen, Präsentationen etc. (https://hedgedoc.org/)
    #### Visual Studio Code
    - Quelltext Editor für diverse Programmiersprachen um die Codes für unsere Webseite und deren Funktionen zu schreiben (https://code.visualstudio.com/)
    #### MailHog
    - erzeugt einen Fake SMTP Server über den lokal Emails verschickt werden können um bspw. Funktionalitäten wie Passwort Resetting zu testen (https://github.com/mailhog/MailHog/releases/tag/v1.0.1)

## Projektübersicht

### Projektziel

    Errichtung einer Webseite zur Zeiterfassung für Mitarbeiter. Anforderungen sind die Kontoerstellung, anschließende Login-Funktion, Einsicht der bisherigen Arbeitszeiten-Logs und die Möglichkeit, seine Gehaltsabrechnung im Blick zu haben. Zusätzlich gibt es einen getrennten Bereich für Mitarbeiter und Admins, bei dem Letzere Funktionen zur Mitarbeiterverwaltung verfügbar gemacht werden.

### Teammitglieder

    - Manuel Kilzer (Projektleiter)
    - Marcel Baumgardt (Co-Leiter)
    - Stephanie Hartwig
    - Anastasiia Kolomiiets
    - Tobias Walter
    - Willy Kirchhof

### Zeitplan

    - Projektstart: 06.11.2024
    - Projektende: 15.11.2024

    Errichtung des HTML-Grundgerüstes (07.11.2024)
    Erstellung der SQL-Datenbank (07.11.2024)
    Funktionalität für Login und Registrierung (08.11.2024)
    …

### Verwendete Technologien

    - HTML: Grundaufbau Objektanordnung, Tabellendarstellung, Formulare
    - PHP: Session-/Datenbankabfragen,
    - SQL: Datenbankstruktur Backend
    - CSS: Stylesheet zur Layoutgestaltung
    - JavaScript: Script für Funktion der Zeiterfassung

## 2. Systemarchitektur

## 2.1 Frontend

## 2.1.1 Benutzeroberfläche

    Die Benutzeroberfläche ist seitenübergreifend klar und minimalistisch strukturiert,
     da für die angestrebten Funktionen auf Übersichtlichkeit gesetzt wird.

(Abbildung Login-Page)

     Die Navigation soll in Bezug auf die tägliche Nutzung vereinfacht und schnell erfolgen können,
    daher wurde von unnötigen Designelementen abgesehen. Ein einfacher zentrierter Login mit den passenden Userdaten
    führt zu den getrennten Bereichen für normale Mitarbeiter und Admins. Zusätzlich wurde in der rechten oberen Bildschirmecke eine Funktion eingefügt,
     welche die Farbgebung der Seite auf einen barrierefreien, weißen Hintergrund setzt.

(Abbildung Zeiterfassung)

    Die Zeiterfassung wird über klickbare Buttons gestartet und gestoppt.
    Dabei wir die erfasste Zeit für jeden Tag summiert und zur Übersicht für den Mitarbeiter dargestellt. 
    Zusätzlich kann der Mitarbeiter seine Gehaltsabrechnungen über einen Button in der rechten oberen Seitenecke erreichen.

(Abbildung Gehaltsabrechnung)

![Hier folgen die weiteren Unterseiten sobald fertig]!

### 2.1.2 CSS Struktur und Design

### 2.2 Backend

## 3. Implementierungsdetails

### 3.1 Datenbank

#### Mitarbeiter Tabelle mit zusätzlichen Angaben zu Abteilungen, Einstellungsdatum und Positionen

```SQL

CREATE TABLE Userlogin (
    UserID INT PRIMARY KEY AUTO_INCREMENT,
    User VARCHAR(50) NOT NULL,
    Passwort VARCHAR(255) NOT NULL,
    Status ENUM('member', 'admin', 'gesperrt') NOT NULL
);

CREATE TABLE Mitarbeiter (
    MitarbeiterID INT PRIMARY KEY,
    Vorname VARCHAR(50) NOT NULL,
    Nachname VARCHAR(50) NOT NULL,
    Straße VARCHAR(100) NOT NULL,
    Postleitzahl VARCHAR(10) NOT NULL,
    Ort VARCHAR(50) NOT NULL,
    Geburtsdatum DATE NOT NULL,
    Email VARCHAR(100) NOT NULL UNIQUE,
    Datum VARCHAR(100),
    FOREIGN KEY (MitarbeiterID) REFERENCES Userlogin(UserID)
);
```

#### Tabelle für die eigentliche Zeiterfassung. Speichert die Einstempel-/Ausstempelzeiten und errechnet automatisch die jeweilige Zeit, die dazwischen vergangen sind

```SQL
CREATE TABLE Zeiterfassung (
    id INT AUTO_INCREMENT PRIMARY KEY,
    MitarbeiterID INT NOT NULL,
    startzeit DATETIME NOT NULL,
    endzeit DATETIME DEFAULT NULL,
    dauer INT DEFAULT NULL,
    status ENUM('aktiv', 'abgeschlossen') NOT NULL DEFAULT 'aktiv',
    FOREIGN KEY (MitarbeiterID) REFERENCES Mitarbeiter(MitarbeiterID) ON DELETE CASCADE
);
```

## 3.2 Funktionalitäten

### Die wichtigsten Funktionen des Projekts umfassen

    - Benutzerverwaltung: Registrierung, Anmeldung und Profilverwaltung.
    - Zeiterfassung: Möglichkeit für Benutzer, Arbeitszeiten zu erfassen und zu verwalten.
    - Berichte und Analysen: Bereitstellung von Statistiken über Arbeitszeiten, Pausen und Überstunden.
    - Lohnabrechnung: Berechnung von Lohn basierend auf erfassten Arbeitszeiten und festgelegten Stundensätzen.
    - E-Mail-Benachrichtigungen: Automatische Benachrichtigungen für Benutzer (z. B. Passwort-Reset).

## 3.3 Sicherheit

### Sicherheitsmaßnahmen

    - Passwortverschlüsselung: Passwörter werden sicher verschlüsselt gespeichert.
    - Sichere Authentifizierung: Token-basierte Passwort-Reset-Funktion und zeitlich begrenzte Links.
    - Session-Management: Automatische Abmeldung bei Inaktivität und sichere Handhabung von Sessions.
    - Datenbank-Sicherheit: Vorbereitete Statements zur Vermeidung von SQL-Injections.

## 4. Installation und Deployment

### Installationsanleitung

    - Repository klonen: Klone das Projekt auf den Server oder lokalen Entwicklungsrechner:
    - bash/cmd
    - git clone https://github.com/medusalich/projekt_lf10.git
    - Abhängigkeiten installieren: Stelle sicher, dass alle PHP- 
      und Datenbankabhängigkeiten installiert sind (siehe composer.json, falls Composer verwendet wird).

### Datenbank einrichten

    - Erstelle eine neue MySQL-Datenbank.
    - Importiere die mitgelieferte SQL-Datei (z. B. database.sql) in die MySQL-Datenbank.
    - Konfigurationsdatei bearbeiten: In der Datei config.php (oder .env falls vorhanden) die Datenbankverbindungen und andere Umgebungsvariablen eintragen.
    - Webserver konfigurieren: Stelle sicher, dass der Webserver für die korrekte Ausführung von PHP-Dateien und Routing konfiguriert ist.
    - Anwendung starten: Rufe die URL des Projekts im Browser auf, um sicherzustellen, dass die Anwendung ordnungsgemäß ausgeführt wird.

## 5 Systemvoraussetzungen

### Serveranforderungen

- PHP 7.0 oder höher
- MySQL/MariaDB als Datenbank
- Apache oder Nginx Webserver
- Mindestens 1GB RAM und 500MB Speicherplatz (empfohlen)

### Clientanforderungen

- Moderner Webbrowser (Chrome, Firefox, Edge, Safari)

## 5.1 Mailhog

1. Download der aktuellen Version für das eigene System (bspw. Windows) unter (<https://github.com/mailhog/MailHog/releases/tag/v1.0.1>)!

2. Ausführen der heruntergeladenen .EXE Datei

3. Mailhog erstellt unter localhost:1025 einen Fake SMTP Server und unter localhost:8025 ein ansteuerbares Interface zur Einsicht der Mails

4. Anpassung des PHP-Ports für Emailverkehr wird in der jeweiligen Datei auf den Fake Server von Mailhog angepasst

5. Ist alles richtig eingerichtet erreichen den Server nun Mails vom Support

## 6. Fazit

Das Abschlussprojekt LF10a ermöglichte es, einen Unternehmensprozess digital abzubilden und förderte sowohl technische als auch organisatorische Fähigkeiten. Die Entwicklung einer Webanwendung mit PHP, SQL, HTML und CSS bot praxisnahe Erfahrung in der Teamarbeit, während tägliche Präsentationen und Protokolle die Zusammenarbeit unterstützten. Die Anwendungsdokumentation rundet das Projekt ab und bereiteten die Teilnehmer auf reale Aufgaben in der Webentwicklung vor
