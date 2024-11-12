-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 12. Nov 2024 um 16:39
-- Server-Version: 10.4.32-MariaDB
-- PHP-Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `projekt`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mitarbeiter`
--

CREATE TABLE `mitarbeiter` (
  `MitarbeiterID` int(11) NOT NULL,
  `Vorname` varchar(50) NOT NULL,
  `Nachname` varchar(50) NOT NULL,
  `Straße` varchar(100) NOT NULL,
  `Postleitzahl` varchar(10) NOT NULL,
  `Ort` varchar(50) NOT NULL,
  `Geburtsdatum` date NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Datum` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `mitarbeiter`
--

INSERT INTO `mitarbeiter` (`MitarbeiterID`, `Vorname`, `Nachname`, `Straße`, `Postleitzahl`, `Ort`, `Geburtsdatum`, `Email`, `Datum`) VALUES
(3, 'Test', 'Tester', 'Testweg 1', '12345', 'Teststadt', '2024-09-10', 'test@tester.de', NULL),
(4, 'Max', 'Mustermann', 'Musterweg 1', '23456', 'Musterstadt', '1970-11-01', 'max@mustermann.de', NULL),
(5, 'Frida', 'Freundlich', 'Freundlichweg 5', '45698', 'Freundlichstadt', '1990-05-05', 'frida@freundlich.de', NULL),
(6, 'Horst', 'Handbuch', 'Handbuchweg 7', '47392', 'Handbuchstadt', '1962-08-15', 'horst@handbuch.de', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `projects`
--

CREATE TABLE `projects` (
  `project_id` int(11) NOT NULL,
  `project_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `department` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `userlogin`
--

CREATE TABLE `userlogin` (
  `UserID` int(11) NOT NULL,
  `User` varchar(50) NOT NULL,
  `Passwort` varchar(255) NOT NULL,
  `Status` enum('member','admin','gesperrt') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `userlogin`
--

INSERT INTO `userlogin` (`UserID`, `User`, `Passwort`, `Status`) VALUES
(3, 'Test', '$2y$10$L2IZUFkClojKAmuNSyY2UOl9twugQIFg326B8TeuAHPS1HdndZG0i', 'admin'),
(4, 'Max', '$2y$10$uOTdb5rQVDAhl98rkEEjG.mlNuxvYM.m4Q2Kgy6erG7QkJB7.Z96C', 'member'),
(5, 'Frida', '$2y$10$T5slb2Uf8aeZXGk0JwQBq.fQc1rq/YmkldQzkrmjt1MryaFAO2bpC', 'gesperrt'),
(6, 'Horst', '$2y$10$Hqc3iEuCpEfVs7bH9M7fdegvUrY1zDUXKDPHjGlLH16LH2tiPeqhO', 'gesperrt');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `zeiterfassung`
--

CREATE TABLE `zeiterfassung` (
  `id` int(11) NOT NULL,
  `MitarbeiterID` int(11) NOT NULL,
  `startzeit` datetime NOT NULL,
  `endzeit` datetime DEFAULT NULL,
  `dauer` int(11) DEFAULT NULL,
  `status` enum('aktiv','abgeschlossen') NOT NULL DEFAULT 'aktiv'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `zeiterfassung`
--

INSERT INTO `zeiterfassung` (`id`, `MitarbeiterID`, `startzeit`, `endzeit`, `dauer`, `status`) VALUES
(1, 3, '2024-11-12 16:34:54', '2024-11-12 16:34:58', 4, 'abgeschlossen'),
(2, 3, '2024-11-12 16:35:13', '2024-11-12 16:35:15', 2, 'abgeschlossen'),
(3, 3, '2024-11-12 16:35:19', '2024-11-12 16:35:23', 4, 'abgeschlossen'),
(4, 3, '2024-11-12 16:35:41', '2024-11-12 16:35:50', 9, 'abgeschlossen'),
(5, 3, '2024-11-12 16:35:54', '2024-11-12 16:36:01', 7, 'abgeschlossen'),
(6, 3, '2024-11-12 16:36:12', '2024-11-12 16:36:15', 3, 'abgeschlossen'),
(7, 3, '2024-11-12 16:36:19', '2024-11-12 16:36:36', 17, 'abgeschlossen');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `mitarbeiter`
--
ALTER TABLE `mitarbeiter`
  ADD PRIMARY KEY (`MitarbeiterID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indizes für die Tabelle `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`project_id`);

--
-- Indizes für die Tabelle `userlogin`
--
ALTER TABLE `userlogin`
  ADD PRIMARY KEY (`UserID`);

--
-- Indizes für die Tabelle `zeiterfassung`
--
ALTER TABLE `zeiterfassung`
  ADD PRIMARY KEY (`id`),
  ADD KEY `MitarbeiterID` (`MitarbeiterID`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `projects`
--
ALTER TABLE `projects`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `userlogin`
--
ALTER TABLE `userlogin`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT für Tabelle `zeiterfassung`
--
ALTER TABLE `zeiterfassung`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `mitarbeiter`
--
ALTER TABLE `mitarbeiter`
  ADD CONSTRAINT `mitarbeiter_ibfk_1` FOREIGN KEY (`MitarbeiterID`) REFERENCES `userlogin` (`UserID`);

--
-- Constraints der Tabelle `zeiterfassung`
--
ALTER TABLE `zeiterfassung`
  ADD CONSTRAINT `zeiterfassung_ibfk_1` FOREIGN KEY (`MitarbeiterID`) REFERENCES `mitarbeiter` (`MitarbeiterID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
