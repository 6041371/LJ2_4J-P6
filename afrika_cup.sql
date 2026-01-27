-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: db:3306
-- Gegenereerd op: 26 jan 2026 om 12:24
-- Serverversie: 8.1.0
-- PHP-versie: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `afrika_cup`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `beheerders`
--

CREATE TABLE `beheerders` (
  `beheerder_id` int NOT NULL,
  `naam` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `wachtwoord` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Gegevens worden geëxporteerd voor tabel `beheerders`
--

INSERT INTO `beheerders` (`beheerder_id`, `naam`, `email`, `wachtwoord`) VALUES
(1, 'Admin 1', 'admin1@afrika.com', '$2y$10$xxxxxxxxxxxxxxxxxxxxxxxxxxxx'),
(2, 'Admin 2', 'admin2@afrika.com', '$2y$10$xxxxxxxxxxxxxxxxxxxxxxxxxxxx');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `landen`
--

CREATE TABLE `landen` (
  `land_id` int NOT NULL,
  `naam` varchar(100) NOT NULL,
  `poule_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Gegevens worden geëxporteerd voor tabel `landen`
--

INSERT INTO `landen` (`land_id`, `naam`, `poule_id`) VALUES
(1, 'Nigeria', 1),
(2, 'Egypte', 1),
(3, 'Marokko', 2),
(4, 'Senegal', 2);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `poules`
--

CREATE TABLE `poules` (
  `poule_id` int NOT NULL,
  `naam` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Gegevens worden geëxporteerd voor tabel `poules`
--

INSERT INTO `poules` (`poule_id`, `naam`) VALUES
(1, 'A'),
(2, 'B');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `scheidsrechters`
--

CREATE TABLE `scheidsrechters` (
  `scheidsrechter_id` int NOT NULL,
  `naam` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `wachtwoord` varchar(255) NOT NULL,
  `beschikbaarheid` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Gegevens worden geëxporteerd voor tabel `scheidsrechters`
--

INSERT INTO `scheidsrechters` (`scheidsrechter_id`, `naam`, `email`, `wachtwoord`, `beschikbaarheid`) VALUES
(1, 'Ref 1', 'ref1@afrika.com', '$2y$10$xxxxxxxxxxxxxxxxxxxx', 1),
(2, 'Ref 2', 'ref2@afrika.com', '$2y$10$xxxxxxxxxxxxxxxxxxxx', 1);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `stadions`
--

CREATE TABLE `stadions` (
  `stadion_id` int NOT NULL,
  `naam` varchar(100) NOT NULL,
  `locatie` varchar(100) NOT NULL,
  `capaciteit` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Gegevens worden geëxporteerd voor tabel `stadions`
--

INSERT INTO `stadions` (`stadion_id`, `naam`, `locatie`, `capaciteit`) VALUES
(1, 'Stadion 1', 'Kaapstad', 50000),
(2, 'Stadion 2', 'Kaïro', 60000);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `supporters`
--

CREATE TABLE `supporters` (
  `supporter_id` int NOT NULL,
  `naam` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `wachtwoord` varchar(255) NOT NULL,
  `adres` text NOT NULL,
  `fan_id` varchar(50) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Gegevens worden geëxporteerd voor tabel `supporters`
--

INSERT INTO `supporters` (`supporter_id`, `naam`, `email`, `wachtwoord`, `adres`, `fan_id`, `status`) VALUES
(1, 'Jan Jansen', 'jan@example.com', '$2y$10$xxxxxxxxxxxxxxxxxxxx', 'Straat 1, Stad', NULL, 'approved'),
(2, 'Piet Pietersen', 'piet@example.com', '$2y$10$xxxxxxxxxxxxxxxxxxxx', 'Straat 2, Stad', NULL, 'pending'),
(3, 'Klaas Klaassen', 'klaas@example.com', '$2y$10$xxxxxxxxxxxxxxxxxxxx', 'Straat 3, Stad', NULL, 'pending'),
(4, 'jaden van de Jaden', 'milan@flitz-events.nl', '$2y$10$8D8EgO2e5kJkN7gKr2vXB.xeGVosDOP6WSU4Nawbi7l.6gsVJDxr.', 'Kadelaan 169', NULL, 'pending');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `tickets`
--

CREATE TABLE `tickets` (
  `ticket_id` int NOT NULL,
  `supporter_id` int NOT NULL,
  `wedstrijd_id` int NOT NULL,
  `aantal` int NOT NULL,
  `status` varchar(50) DEFAULT 'besteld'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `wedstrijden`
--

CREATE TABLE `wedstrijden` (
  `wedstrijd_id` int NOT NULL,
  `datum` date NOT NULL,
  `tijd` time NOT NULL,
  `status` varchar(50) DEFAULT 'open',
  `stadion_id` int NOT NULL,
  `scheidsrechter_id` int DEFAULT NULL,
  `land_thuis_id` int NOT NULL,
  `land_uit_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Gegevens worden geëxporteerd voor tabel `wedstrijden`
--

INSERT INTO `wedstrijden` (`wedstrijd_id`, `datum`, `tijd`, `status`, `stadion_id`, `scheidsrechter_id`, `land_thuis_id`, `land_uit_id`) VALUES
(1, '2026-02-10', '18:00:00', 'open', 1, NULL, 1, 2),
(2, '2026-02-11', '20:00:00', 'open', 1, NULL, 3, 4),
(3, '2026-02-12', '19:30:00', 'open', 2, NULL, 2, 3);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `wedstrijdverslagen`
--

CREATE TABLE `wedstrijdverslagen` (
  `verslag_id` int NOT NULL,
  `wedstrijd_id` int NOT NULL,
  `scheidsrechter_id` int NOT NULL,
  `score_thuis` int NOT NULL,
  `score_uit` int NOT NULL,
  `gele_kaarten` int DEFAULT '0',
  `rode_kaarten` int DEFAULT '0',
  `opmerkingen` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `beheerders`
--
ALTER TABLE `beheerders`
  ADD PRIMARY KEY (`beheerder_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexen voor tabel `landen`
--
ALTER TABLE `landen`
  ADD PRIMARY KEY (`land_id`),
  ADD KEY `poule_id` (`poule_id`);

--
-- Indexen voor tabel `poules`
--
ALTER TABLE `poules`
  ADD PRIMARY KEY (`poule_id`);

--
-- Indexen voor tabel `scheidsrechters`
--
ALTER TABLE `scheidsrechters`
  ADD PRIMARY KEY (`scheidsrechter_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexen voor tabel `stadions`
--
ALTER TABLE `stadions`
  ADD PRIMARY KEY (`stadion_id`);

--
-- Indexen voor tabel `supporters`
--
ALTER TABLE `supporters`
  ADD PRIMARY KEY (`supporter_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexen voor tabel `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`ticket_id`),
  ADD KEY `supporter_id` (`supporter_id`),
  ADD KEY `wedstrijd_id` (`wedstrijd_id`);

--
-- Indexen voor tabel `wedstrijden`
--
ALTER TABLE `wedstrijden`
  ADD PRIMARY KEY (`wedstrijd_id`),
  ADD KEY `stadion_id` (`stadion_id`),
  ADD KEY `scheidsrechter_id` (`scheidsrechter_id`),
  ADD KEY `land_thuis_id` (`land_thuis_id`),
  ADD KEY `land_uit_id` (`land_uit_id`);

--
-- Indexen voor tabel `wedstrijdverslagen`
--
ALTER TABLE `wedstrijdverslagen`
  ADD PRIMARY KEY (`verslag_id`),
  ADD KEY `wedstrijd_id` (`wedstrijd_id`),
  ADD KEY `scheidsrechter_id` (`scheidsrechter_id`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `beheerders`
--
ALTER TABLE `beheerders`
  MODIFY `beheerder_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT voor een tabel `landen`
--
ALTER TABLE `landen`
  MODIFY `land_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT voor een tabel `poules`
--
ALTER TABLE `poules`
  MODIFY `poule_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT voor een tabel `scheidsrechters`
--
ALTER TABLE `scheidsrechters`
  MODIFY `scheidsrechter_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT voor een tabel `stadions`
--
ALTER TABLE `stadions`
  MODIFY `stadion_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT voor een tabel `supporters`
--
ALTER TABLE `supporters`
  MODIFY `supporter_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT voor een tabel `tickets`
--
ALTER TABLE `tickets`
  MODIFY `ticket_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `wedstrijden`
--
ALTER TABLE `wedstrijden`
  MODIFY `wedstrijd_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT voor een tabel `wedstrijdverslagen`
--
ALTER TABLE `wedstrijdverslagen`
  MODIFY `verslag_id` int NOT NULL AUTO_INCREMENT;

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `landen`
--
ALTER TABLE `landen`
  ADD CONSTRAINT `landen_ibfk_1` FOREIGN KEY (`poule_id`) REFERENCES `poules` (`poule_id`) ON DELETE CASCADE;

--
-- Beperkingen voor tabel `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`supporter_id`) REFERENCES `supporters` (`supporter_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`wedstrijd_id`) REFERENCES `wedstrijden` (`wedstrijd_id`) ON DELETE CASCADE;

--
-- Beperkingen voor tabel `wedstrijden`
--
ALTER TABLE `wedstrijden`
  ADD CONSTRAINT `wedstrijden_ibfk_1` FOREIGN KEY (`stadion_id`) REFERENCES `stadions` (`stadion_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wedstrijden_ibfk_2` FOREIGN KEY (`scheidsrechter_id`) REFERENCES `scheidsrechters` (`scheidsrechter_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `wedstrijden_ibfk_3` FOREIGN KEY (`land_thuis_id`) REFERENCES `landen` (`land_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wedstrijden_ibfk_4` FOREIGN KEY (`land_uit_id`) REFERENCES `landen` (`land_id`) ON DELETE CASCADE;

--
-- Beperkingen voor tabel `wedstrijdverslagen`
--
ALTER TABLE `wedstrijdverslagen`
  ADD CONSTRAINT `wedstrijdverslagen_ibfk_1` FOREIGN KEY (`wedstrijd_id`) REFERENCES `wedstrijden` (`wedstrijd_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wedstrijdverslagen_ibfk_2` FOREIGN KEY (`scheidsrechter_id`) REFERENCES `scheidsrechters` (`scheidsrechter_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
