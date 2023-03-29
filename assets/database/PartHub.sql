-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 29. Mrz 2023 um 09:08
-- Server-Version: 10.3.38-MariaDB-0ubuntu0.20.04.1
-- PHP-Version: 8.2.4

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `PartHub`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bom_elements`
--

CREATE TABLE IF NOT EXISTS `bom_elements` (
  `bom_elements_id` int(11) NOT NULL AUTO_INCREMENT,
  `bom_id_fk` int(11) NOT NULL,
  `part_id_fk` int(11) NOT NULL,
  `element_quantity` int(11) NOT NULL,
  PRIMARY KEY (`bom_elements_id`) USING BTREE,
  KEY `bom_id` (`bom_id_fk`),
  KEY `part_id` (`part_id_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `bom_elements`
--

INSERT INTO `bom_elements` (`bom_elements_id`, `bom_id_fk`, `part_id_fk`, `element_quantity`) VALUES
(22, 28, 1055, 1),
(23, 28, 712, 8),
(24, 28, 964, 2),
(25, 28, 477, 2),
(26, 29, 629, 82),
(27, 29, 532, 12),
(28, 29, 655, 1),
(29, 29, 422, 12),
(30, 29, 1233, 21),
(31, 29, 567, 47),
(32, 29, 998, 2),
(33, 29, 1055, 1),
(34, 29, 351, 1),
(35, 29, 1149, 1),
(36, 29, 964, 1),
(37, 30, 351, 10),
(38, 30, 998, 20),
(39, 30, 335, 30),
(40, 30, 655, 40),
(41, 30, 655, 50),
(42, 31, 351, 234),
(43, 32, 351, 23),
(44, 32, 351, 23),
(45, 32, 351, 3232),
(46, 32, 351, 32),
(47, 33, 351, 23),
(48, 34, 629, 2),
(49, 34, 532, 3),
(50, 35, 618, 327),
(51, 36, 351, 342),
(52, 37, 351, 342),
(53, 38, 351, 342),
(54, 39, 351, 342),
(55, 40, 351, 2345),
(56, 40, 629, 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bom_names`
--

CREATE TABLE IF NOT EXISTS `bom_names` (
  `bom_id` int(11) NOT NULL AUTO_INCREMENT,
  `bom_name` varchar(255) NOT NULL,
  `bom_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`bom_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `bom_names`
--

INSERT INTO `bom_names` (`bom_id`, `bom_name`, `bom_description`) VALUES
(28, 'First BOM', 'It\'s the first one!'),
(29, 'Second BOM', 'Let\'s do another'),
(30, '10 to 50', NULL),
(31, '234', NULL),
(32, 'Second BOM', NULL),
(33, '34r2', NULL),
(34, '4234', NULL),
(35, 'fc23', NULL),
(36, 'q34zt5', NULL),
(37, 'q34zt5', NULL),
(38, 'q34zt5', NULL),
(39, 'q34zt5', NULL),
(40, 'w43ret2345', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bom_runs`
--

CREATE TABLE IF NOT EXISTS `bom_runs` (
  `bom_run_id` int(11) NOT NULL AUTO_INCREMENT,
  `bom_id_fk` int(11) NOT NULL,
  `bom_run_quantity` int(11) NOT NULL,
  `bom_run_datetime` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`bom_run_id`),
  KEY `bom_id_fk` (`bom_id_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `footprints`
--

CREATE TABLE IF NOT EXISTS `footprints` (
  `footprint_id` int(11) NOT NULL AUTO_INCREMENT,
  `footprint_name` varchar(255) NOT NULL,
  `footprint_alias` varchar(255) NOT NULL,
  PRIMARY KEY (`footprint_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `footprints`
--

INSERT INTO `footprints` (`footprint_id`, `footprint_name`, `footprint_alias`) VALUES
(1, 'A Footprint', 'Its alias');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `location_names`
--

CREATE TABLE IF NOT EXISTS `location_names` (
  `location_id` int(11) NOT NULL AUTO_INCREMENT,
  `location_name` varchar(255) NOT NULL,
  `location_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`location_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `location_names`
--

INSERT INTO `location_names` (`location_id`, `location_name`, `location_description`) VALUES
(1, 'Main Storage', 'Like, all of it'),
(2, 'EMS Partner', ''),
(3, 'Hardware Supplier', ''),
(4, 'External Storage', NULL),
(5, 'Increase', 'General Stock Increase'),
(6, 'Decrease', 'General Stock Decrease');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `minstock_levels`
--

CREATE TABLE IF NOT EXISTS `minstock_levels` (
  `minstock_lvl_id` int(11) NOT NULL AUTO_INCREMENT,
  `part_id_fk` int(11) NOT NULL,
  `location_id_fk` int(11) NOT NULL,
  `minstock_level` int(11) NOT NULL,
  PRIMARY KEY (`minstock_lvl_id`),
  KEY `location_id` (`location_id_fk`),
  KEY `part_id` (`part_id_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `parts`
--

CREATE TABLE IF NOT EXISTS `parts` (
  `part_id` int(11) NOT NULL AUTO_INCREMENT,
  `part_name` varchar(255) NOT NULL,
  `part_description` varchar(255) DEFAULT NULL,
  `part_comment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `part_category_fk` int(11) NOT NULL DEFAULT 1,
  `part_footprint_fk` int(11) DEFAULT NULL,
  `part_unit_fk` int(11) DEFAULT NULL,
  `part_owner_u_fk` int(11) NOT NULL,
  `part_owner_g_fk` int(11) NOT NULL,
  PRIMARY KEY (`part_id`),
  UNIQUE KEY `part_name` (`part_name`),
  KEY `part_category_fk` (`part_category_fk`),
  KEY `part_unit_fk` (`part_unit_fk`),
  KEY `part_footprint_fk` (`part_footprint_fk`),
  KEY `part_owner_u_fk` (`part_owner_u_fk`),
  KEY `part_owner_g_fk` (`part_owner_g_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=1463 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `parts`
--

INSERT INTO `parts` (`part_id`, `part_name`, `part_description`, `part_comment`, `created_at`, `part_category_fk`, `part_footprint_fk`, `part_unit_fk`, `part_owner_u_fk`, `part_owner_g_fk`) VALUES
(335, 'BC847B', 'Transistor NPN 45V 100mA SOT-23', 'Your general NPN transistor workhorse', '2023-03-07 00:24:12', 6, 1, 1, 1, 1),
(337, 'CD4017BE', 'Decade Counter/Divider with 10 Decoded Outputs', 'Can be used as a sequencer or for LED chasers.', '2023-03-07 00:24:12', 2, 1, 1, 1, 1),
(338, '1N4148', 'General Purpose Diode', 'fast-switching, widely used signal diode', '0000-00-00 00:00:00', 1, 1, 1, 1, 1),
(339, 'LM386N-1', 'Audio Amplifier', 'Low voltage audio power amplifier, ideal for battery-powered devices.', '2023-03-07 00:24:12', 12, 1, 1, 1, 1),
(343, 'LM7905', 'Fixed Negative Voltage Regulator', 'Outputs a constant -5V DC voltage.', '2023-03-07 00:24:12', 1, 1, 1, 1, 1),
(344, 'LM7912', 'Fixed Negative Voltage Regulator', 'Outputs a constant -12V DC voltage.', '2023-03-07 00:24:12', 1, 1, 1, 1, 1),
(347, 'NE555P', 'Timer IC', 'Can be used as a clock, pulse generator or oscillator.', '2023-03-07 00:24:12', 1, 1, 1, 1, 1),
(348, 'LM324N', 'Quad Op-Amp IC', 'Has four independent operational amplifiers in one package.', '2023-03-07 00:24:12', 1, 1, 1, 1, 1),
(350, 'LM741CN', 'Op-Amp IC', 'A widely used operational amplifier with high gain and low distortion.', '2023-03-07 00:24:12', 1, 1, 1, 1, 1),
(351, '1N4007', 'Rectifier Diode', 'Can handle a maximum current of 1A and a maximum voltage of 1000V.', '2023-03-07 00:24:12', 1, 1, 1, 1, 1),
(422, 'Inductor 100uH, 10%, 0805', 'An inductor with 100uH', 'Ideal for low-frequency filtering applications', '2022-03-06 09:15:34', 1, 1, 1, 1, 1),
(439, 'Transistor', 'Transistor', 'Generic transistor', '2023-03-06 13:12:24', 1, 1, 1, 1, 1),
(477, 'Capacitor 10uF', '10uF Capacaitor, 35V', 'Generic capacitor', '2023-03-06 13:12:24', 1, 1, 1, 1, 1),
(532, 'Capacitor 100pF, 5%, 0603', 'Surface mount capacitor with 100 picofarad capacitance and 5% tolerancedc', 'Useful for high-frequency filtering and decoupling applications', '2022-03-06 09:15:39', 1, 1, 1, 1, 1),
(567, 'Resistor 10K, 1%, 0603', 'Surface mount resistor with 10K ohm resistance and 1% tolerance', 'Good for precision circuits', '2022-03-06 09:15:32', 1, 1, 1, 1, 1),
(618, 'BC327', 'PNP transistor', 'good for switching and amplification', '0000-00-00 00:00:00', 13, 1, 1, 1, 1),
(629, 'Capacitor 0.1uF, 10%, 0805', 'Surface mount capacitor with 0.1 microfarad capacitance and 10% tolerance', 'Useful for decoupling and filtering applications', '2022-03-06 09:15:41', 1, 1, 1, 1, 1),
(655, 'Capacitor 1uF, 10%, 0805', 'Surface mount capacitor with 1 microfarad capacitance and 10% tolerance', 'Ideal for audio filtering and coupling applications', '2022-03-06 09:15:36', 1, 1, 1, 1, 1),
(672, 'LM7805', 'Voltage regulator', 'output voltage of 5V, maximum current of 1A', '0000-00-00 00:00:00', 1, 1, 1, 1, 1),
(680, 'LM7812', 'Voltage regulator', 'output voltage of 12V, maximum current of 1A', '0000-00-00 00:00:00', 1, 1, 1, 1, 1),
(699, 'LM7808', 'Voltage regulator', 'output voltage of 8V, maximum current of 1A', '0000-00-00 00:00:00', 1, 1, 1, 1, 1),
(712, 'Resistor 10K', 'Resistor, 10K ohm, 1%, 0603', 'Some fitting comment', '2023-03-06 13:12:24', 1, 1, 1, 1, 1),
(743, 'LM7809', 'Voltage regulator', 'output voltage of 9V, maximum current of 1A', '0000-00-00 00:00:00', 1, 1, 1, 1, 1),
(791, 'Resistor 1K, 1%, 1206', 'Surface mount resistor with 1K ohm resistance and 1% tolerance', 'Ideal for precision voltage dividers and signal conditioning circuits', '2022-03-06 09:15:40', 1, 1, 1, 1, 1),
(849, 'LM317', 'Adjustable voltage regulator', 'output voltage can be adjusted from 1.25V to 37V', '0000-00-00 00:00:00', 1, 1, 1, 1, 1),
(868, '74HC595', '8-bit shift register', 'great for expanding outputs of microcontrollers', '0000-00-00 00:00:00', 1, 1, 1, 1, 1),
(893, 'Capacitor 10uF, 20%, 0805', 'Surface mount capacitor with 10 microfarad capacitance and 20% tolerance', 'Useful for smoothing out power supply voltages', '2022-03-06 09:15:33', 1, 1, 1, 1, 1),
(916, 'LM386', 'Audio amplifier', 'low voltage and low current operation, great for small audio projects', '0000-00-00 00:00:00', 1, 1, 1, 1, 1),
(964, 'NE555', 'Timer IC', 'versatile and widely used timer IC', '0000-00-00 00:00:00', 1, 1, 1, 1, 1),
(972, 'ATtiny85', 'Microcontroller', 'low-power, high-performance AVR microcontroller with 8KB of flash memory', '0000-00-00 00:00:00', 1, 1, 1, 1, 1),
(986, 'LM317T', 'Adjustable voltage regulator', 'output voltage can be adjusted from 1.25V to 37V', '0000-00-00 00:00:00', 1, 1, 1, 1, 1),
(998, 'Resistor 1M, 5%, 1206', 'Surface mount resistor with 1 megohm resistance and 5% tolerance', 'Useful for high-impedance voltage dividers', '2022-03-06 09:15:35', 1, 1, 1, 1, 1),
(1055, 'ATmega328P', 'Microcontroller', 'powerful 8-bit AVR microcontroller with 32KB of flash memory', '0000-00-00 00:00:00', 1, 1, 1, 1, 1),
(1076, 'ULN2003', 'Darlington transistor array', 'used for driving high-power devices with low-power control signals', '0000-00-00 00:00:00', 1, 1, 1, 1, 1),
(1077, 'LM2596', 'Adjustable voltage regulator', 'high efficiency, output voltage can be adjusted from 1.23V to 37V', '0000-00-00 00:00:00', 1, 1, 1, 1, 1),
(1101, 'BC548', 'NPN transistor', 'good for switching and amplification', '0000-00-00 00:00:00', 1, 1, 1, 1, 1),
(1112, 'LM7807', 'Voltage regulator', 'output voltage of 7V, maximum current of 1A', '0000-00-00 00:00:00', 1, 1, 1, 1, 1),
(1148, 'Inductor 10uH, 20%, 0805', 'Surface mount inductor with 10 microhenry inductance and 20% tolerance', 'Good for power supply filtering and DC-DC converter applications', '2022-03-06 09:15:38', 4, 1, 1, 1, 1),
(1149, 'CD4017', 'Decade counter', 'can drive up to 10 LEDs, great for sequencing and timing circuits', '0000-00-00 00:00:00', 1, 1, 1, 1, 1),
(1162, 'LM741', 'Operational amplifier', 'high-gain, versatile op-amp for various applications', '0000-00-00 00:00:00', 1, 1, 1, 1, 1),
(1165, 'LM7806', 'Voltage regulator', 'output voltage of 6V, maximum current of 1A', '0000-00-00 00:00:00', 1, 1, 1, 1, 1),
(1187, 'Resistor 100K, 5%, 1206', 'Surface mount resistor with 100K ohm resistance and 5% tolerance', 'Commonly used for biasing transistors and op-amps', '2022-03-06 09:15:37', 1, 1, 1, 1, 1),
(1199, 'Inductor 4.7uH, 10%, 0805', 'Surface mount inductor with 4.7 microhenry inductance and 10% tolerance', 'Ideal for power supply filtering and switching regulator applications', '2022-03-06 09:15:43', 1, 1, 1, 1, 1),
(1217, 'LM358N', 'Dual operational amplifier', 'low power consumption, wide supply voltage range', '0000-00-00 00:00:00', 1, 1, 1, 1, 1),
(1227, 'IRFZ44N', 'N-Channel MOSFET', 'high-current capability, low on-resistance, fast switching speed', '0000-00-00 00:00:00', 1, 1, 1, 1, 1),
(1233, 'Resistor 1.5K, 5%, 1206', 'Surface mount resistor with 1.5K ohm resistance and 5% tolerance', 'Good for audio and low-frequency signal conditioning', '2022-03-06 09:15:42', 1, 1, 1, 1, 1),
(1273, 'PC817', 'Optocoupler', 'used for signal isolation and noise reduction in digital circuits', '0000-00-00 00:00:00', 1, 1, 1, 1, 1),
(1326, 'LM393', 'Dual voltage comparator', 'low power consumption, wide supply voltage range', '0000-00-00 00:00:00', 1, 1, 1, 1, 1),
(1365, 'IRF540N', 'N-Channel MOSFET', 'high-current capability, low on-resistance, fast switching speed', '0000-00-00 00:00:00', 1, 1, 1, 1, 1),
(1392, 'BC639', 'PNP transistor', 'good for switching and amplification', '0000-00-00 00:00:00', 1, 1, 1, 1, 1),
(1462, 'LM358', 'Dual operational amplifier', 'low power consumption, wide supply voltage range', '0000-00-00 00:00:00', 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `part_categories`
--

CREATE TABLE IF NOT EXISTS `part_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) NOT NULL,
  `parent_category` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `part_categories`
--

INSERT INTO `part_categories` (`category_id`, `category_name`, `parent_category`) VALUES
(1, 'Electronics', 0),
(2, 'Passive Components', 1),
(3, 'Resistors', 2),
(4, 'Thermistors', 2),
(5, 'Varistors', 2),
(6, 'Inductors', 2),
(7, 'Ferrite Beads', 2),
(8, 'Capacitors', 2),
(9, 'Ceramic Capacitors', 8),
(10, 'Tantalum Capacitors', 8),
(11, 'Aluminum Electrolytic Capacitors', 8),
(12, 'Film Capacitors', 8),
(13, 'Super Capacitors', 8),
(14, 'Electromechanical Components', 1),
(15, 'Switches', 14),
(16, 'Tactile Switches', 15),
(17, 'Push Button Switches', 15),
(18, 'Rocker Switches', 15),
(19, 'Toggle Switches', 15),
(20, 'Slide Switches', 15),
(21, 'DIP Switches', 15),
(22, 'Micro Switches', 15),
(23, 'Rotary Switches', 15),
(24, 'Encoder Switches', 15),
(25, 'Keypads', 15),
(26, 'Relays', 14),
(27, 'Electromagnetic Relays', 26),
(28, 'Solid State Relays', 26),
(29, 'Contactors', 26),
(30, 'Connectors', 14),
(31, 'PCB Mount Connectors', 30),
(32, 'Circular Connectors', 30),
(33, 'D-Sub Connectors', 30),
(34, 'RF Connectors', 30),
(35, 'Terminal Blocks', 30),
(36, 'Headers', 30),
(37, 'Cable Assemblies', 14),
(38, 'Wiring Accessories', 14),
(39, 'Cable Ties', 38),
(40, 'Sleeving', 38),
(41, 'Heat Shrink Tubing', 38),
(42, 'Cable Glands', 38),
(43, 'Adhesives', 14),
(44, 'Tapes', 14),
(45, 'Enclosures', 14),
(46, 'Plastic Enclosures', 45),
(47, 'Metal Enclosures', 45),
(48, 'Box Enclosures', 45),
(49, 'Panel Mount Enclosures', 45),
(50, 'Heat Sinks', 14),
(51, 'Aluminum Heat Sinks', 50),
(52, 'Copper Heat Sinks', 50),
(53, 'Fans', 14),
(54, 'DC Fans', 53),
(55, 'AC Fans', 53),
(56, 'Blowers', 53),
(57, 'Thermal Management Accessories', 14),
(58, 'Thermal Interface Materials', 57),
(59, 'Fans Accessories', 57),
(60, 'Heaters', 14),
(61, 'Cartridge Heaters', 60),
(62, 'Band Heaters', 60),
(63, 'Strip Heaters', 60),
(64, 'Immersion Heaters', 60),
(65, 'Thermocouples', 14),
(66, 'Thermostats', 14),
(67, 'Proximity Sensors', 14),
(68, 'Level Sensors', 14),
(69, 'Potentiometers', 14),
(70, 'Rotary Potentiometers', 69),
(71, 'Linear Potentiometers', 69),
(72, 'Trimmers', 69),
(73, 'Encoders', 14),
(74, 'Optoelectronics', 1),
(75, 'LEDs', 74),
(76, 'LED Displays', 74),
(77, 'LED Strips', 74),
(78, 'Infrared Components', 74),
(79, 'Laser Diodes', 74),
(80, 'Photoelectric Sensors', 74),
(81, 'Optocouplers', 74),
(82, 'Optical Filters', 74),
(83, 'Power Supplies', 1),
(84, 'AC-DC Power Supplies', 83),
(85, 'DC-DC Converters', 83),
(86, 'Inverters', 83),
(87, 'UPS Systems', 83),
(88, 'Batteries', 1),
(89, 'Alkaline Batteries', 88),
(90, 'NiMH Batteries', 88);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `part_units`
--

CREATE TABLE IF NOT EXISTS `part_units` (
  `unit_id` int(11) NOT NULL AUTO_INCREMENT,
  `unit_name` varchar(255) NOT NULL,
  PRIMARY KEY (`unit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `part_units`
--

INSERT INTO `part_units` (`unit_id`, `unit_name`) VALUES
(1, 'PCS');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `shortage_parts`
--

CREATE TABLE IF NOT EXISTS `shortage_parts` (
  `part_id` int(11) NOT NULL AUTO_INCREMENT,
  `part_name` varchar(255) DEFAULT NULL,
  `global_stock` int(11) DEFAULT NULL,
  `part_description` varchar(255) DEFAULT NULL,
  `part_project` varchar(255) DEFAULT NULL,
  `part_comment` varchar(255) DEFAULT NULL,
  `storage_location` varchar(255) NOT NULL DEFAULT 'KOMA',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `part_category_fk` int(11) DEFAULT NULL,
  PRIMARY KEY (`part_id`),
  UNIQUE KEY `part_name` (`part_name`),
  KEY `part_category_fk` (`part_category_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=267 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `shortage_parts`
--

INSERT INTO `shortage_parts` (`part_id`, `part_name`, `global_stock`, `part_description`, `part_project`, `part_comment`, `storage_location`, `created_at`, `updated_at`, `part_category_fk`) VALUES
(1, 'TPS54332DDAR', 1000, 'Switching Voltage Regulator', 'FKFX, Strom', 'Strom, FKFX, ...', 'KOMA', '2023-02-18 15:36:13', NULL, 1),
(2, 'LM25575MH/NOPB', 120, 'Switching Voltage Regulator', 'Komplex', NULL, 'KOMA', '2023-02-18 15:36:13', NULL, 1),
(3, 'ATMEGA325PA-AUR', 500, '8-bit Microcontroller MCU', 'Komplex', NULL, 'KOMA', '2023-02-18 15:36:13', NULL, 1),
(4, 'CAT4016HV6-GT2', 240, 'LED Driver', 'None', 'Wäre Komplex, aber jetzt gibts wieder original Teil', 'KOMA', '2023-02-18 15:36:13', NULL, 1),
(5, 'MCP3202T-BI/SN', 15, '12-bit ADC SPI', 'Komplex', 'Das extra \'T\' steht wohl nur für die Standardpackungsgröße', 'KOMA', '2023-02-18 15:36:13', NULL, 1),
(6, 'TL084HIPWR', 2100, 'Opamp JFET TSSOP', 'None', 'Wäre Komplex Ersatz für MC3303 gewesen, aber original wieder verfügbar', 'KOMA', '2023-02-18 15:36:13', NULL, 1),
(7, 'MCP4822-E/SN', 550, '12-bit DAC SPI', 'Komplex', NULL, 'KOMA', '2023-02-18 15:36:13', NULL, 1),
(8, 'TS5A23159DGST', 600, 'Analogue Switch ICs 1Ohm Dual SPDT', 'Komplex', NULL, 'KOMA', '2023-02-18 15:36:13', NULL, 1),
(9, 'MCP3202-BI/SN', 292, '12-bit ADC SPI', 'Komplex', NULL, 'KOMA', '2023-02-18 15:36:13', NULL, 1),
(10, 'TLC5947RHBT', 110, 'LED Driver', 'Komplex', NULL, 'KOMA', '2023-02-18 15:36:13', NULL, 1),
(11, 'LM2700LD-ADJ/NOPB', 106, 'Switching Voltage Regulator', 'Komplex', NULL, 'KOMA', '2023-02-18 15:36:13', NULL, 1),
(12, 'MCP4131-502E/MS-ND', 480, 'Digital Potentiometer 5KOhm', 'Komplex', NULL, 'KOMA', '2023-02-18 15:36:13', NULL, 1),
(13, 'ATMEGA328PB-AU', 500, '8-bit Microcontroller MCU', 'Komplex', NULL, 'KOMA', '2023-02-18 15:36:13', NULL, 1),
(14, 'SI4825-A10-CSR', 209, 'Radio IC', 'FK oder Radio Module', NULL, 'KOMA', '2023-02-18 15:36:13', NULL, 1),
(15, 'TPS55340PWPR', 500, 'Switching Voltage Regulator', 'Strom Mobil PR2', NULL, 'KOMA', '2023-02-18 15:36:13', NULL, 1),
(16, 'SSI2164S-RT', 10000, 'VCA', 'FKFX', '2 haben wir für Chromaplane rausgenommen, 1 Reel also geöffnet', 'KOMA', '2023-02-18 15:36:13', NULL, 1),
(17, 'GD32F303VCT6', 2160, '32-bit Microcontroller MCU', 'FKFX', 'Original wieder verfügbar?', 'KOMA', '2023-02-18 15:36:13', NULL, 1),
(18, 'ATTINY44A-SSURCT-ND', 250, '8-bit Microcontroller MCU', 'SVF-201', NULL, 'KOMA', '2023-02-18 15:36:13', NULL, 1),
(19, 'PT2399', 4054, 'Digital Delay', 'FKFX oder Delay Module', NULL, 'KOMA', '2023-02-18 15:36:13', NULL, 1),
(20, 'LM27313XMF/NOPB', 2600, 'Switching Voltage Regulators 1.6 MHZ BOOST CONVERTER', 'FKFX', NULL, 'KOMA', '2023-02-18 15:36:13', NULL, 1),
(263, 'DC-Motor', 1000, 'Expansion Pack Motor', 'FK Expansion Pack', NULL, 'KOMA', '2023-02-18 18:27:56', NULL, 1),
(265, 'Solenoid', 1000, 'Expansion Pack Soleoid\r\n\r\n', 'FK Expansion Pack', NULL, 'KOMA', '2023-02-18 21:27:25', NULL, 1),
(266, '3.5mm Jack Cable', 1000, 'Jack Cable', 'Various', NULL, 'KOMA', '2023-02-18 21:27:25', NULL, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `stock_levels`
--

CREATE TABLE IF NOT EXISTS `stock_levels` (
  `stock_level_id` int(11) NOT NULL AUTO_INCREMENT,
  `part_id_fk` int(11) NOT NULL,
  `location_id_fk` int(11) NOT NULL,
  `stock_level_quantity` int(11) NOT NULL,
  PRIMARY KEY (`stock_level_id`),
  KEY `part_id` (`part_id_fk`),
  KEY `location_id` (`location_id_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=161 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `stock_levels`
--

INSERT INTO `stock_levels` (`stock_level_id`, `part_id_fk`, `location_id_fk`, `stock_level_quantity`) VALUES
(1, 335, 1, 123),
(2, 337, 2, 4213),
(3, 338, 3, 1),
(4, 339, 4, 312),
(5, 343, 2, 432),
(6, 344, 3, 386),
(7, 347, 1, 32),
(8, 348, 2, 59),
(9, 350, 3, 1932),
(10, 351, 4, 2),
(11, 422, 2, 0),
(12, 439, 3, 0),
(13, 477, 1, 42),
(14, 532, 4, 4384),
(15, 567, 1, 94),
(16, 618, 3, 1754),
(17, 629, 1, 11),
(18, 655, 4, 75),
(19, 672, 1, 1456),
(20, 680, 1, 13),
(21, 699, 2, 38),
(22, 712, 3, 98),
(23, 743, 4, 38),
(24, 791, 2, 97),
(25, 849, 1, 389),
(26, 868, 2, 3),
(27, 893, 3, 90),
(28, 916, 4, 678),
(29, 964, 2, 3),
(30, 972, 3, 213),
(31, 986, 1, 4213),
(32, 998, 4, 23),
(33, 1055, 1, 312),
(34, 1076, 1, 432),
(35, 1077, 2, 386),
(36, 1101, 3, 39),
(37, 1112, 4, 59),
(38, 1148, 2, 1932),
(39, 1149, 3, 8),
(40, 1162, 1, 187),
(41, 1165, 2, 239),
(42, 1187, 3, 42),
(43, 1199, 4, 4384),
(44, 1217, 2, 94),
(45, 1227, 3, 1754),
(46, 1233, 1, 11),
(47, 1273, 4, 62),
(48, 1326, 1, 1456),
(49, 1365, 2, 13),
(50, 1392, 1, 39),
(51, 1462, 2, 98),
(52, 335, 3, 38),
(53, 337, 4, 101),
(54, 338, 2, 392),
(55, 339, 3, 3),
(56, 343, 1, 104),
(57, 344, 4, 682),
(58, 347, 1, 32),
(59, 348, 2, 213),
(60, 350, 3, 4213),
(61, 351, 1, 23),
(62, 422, 1, 600),
(63, 439, 2, 0),
(64, 477, 3, 386),
(65, 532, 4, 39),
(66, 567, 2, 59),
(67, 618, 3, 1932),
(68, 629, 1, 8),
(69, 655, 2, 187),
(70, 672, 3, 239),
(71, 680, 4, 42),
(72, 699, 2, 4384),
(73, 712, 3, 94),
(74, 743, 1, 1754),
(75, 791, 4, 11),
(76, 849, 1, 75),
(77, 868, 2, 1456),
(78, 893, 3, 13),
(79, 916, 4, 38),
(80, 964, 2, 98),
(81, 972, 3, 38),
(82, 986, 1, 97),
(83, 998, 2, 389),
(84, 1055, 3, 37),
(85, 1076, 4, 90),
(86, 1077, 2, 678),
(87, 1101, 3, 3),
(88, 1112, 1, 213),
(89, 1148, 4, 4213),
(90, 1149, 1, 23),
(91, 1162, 3, 312),
(92, 1165, 1, 432),
(93, 1187, 1, 386),
(94, 1199, 2, 39),
(95, 1217, 3, 59),
(96, 1227, 4, 1932),
(97, 1233, 2, 8),
(98, 1273, 3, 0),
(99, 1326, 1, 239),
(100, 1365, 1, 42),
(101, 1392, 2, 4384),
(102, 1462, 3, 94),
(103, 335, 4, 1754),
(104, 337, 2, 11),
(105, 338, 3, 1),
(106, 339, 1, 2),
(107, 343, 4, 0),
(108, 344, 1, 41),
(109, 347, 2, 110),
(110, 348, 3, 357),
(111, 350, 4, 21),
(112, 351, 2, 389),
(113, 422, 3, 0),
(114, 439, 1, 572),
(115, 477, 2, 2678),
(116, 532, 3, 2320),
(117, 567, 4, 213),
(118, 618, 2, 4214),
(119, 629, 3, 23),
(120, 655, 1, 312),
(121, 672, 4, 433),
(122, 680, 1, 386),
(123, 699, 4, 39),
(124, 712, 2, 59),
(125, 743, 3, 1932),
(126, 791, 1, 8),
(127, 849, 4, 187),
(128, 868, 1, 239),
(129, 893, 1, 42),
(130, 916, 2, 4384),
(131, 964, 3, 94),
(132, 972, 4, 1754),
(133, 986, 2, 11),
(134, 998, 3, 75),
(135, 1055, 1, 1456),
(136, 1076, 4, 13),
(137, 1077, 1, 38),
(138, 1101, 1, 98),
(139, 1112, 2, 38),
(140, 1148, 3, 96),
(141, 1149, 4, 389),
(142, 1162, 2, 3),
(143, 1165, 3, 90),
(144, 1187, 1, 678),
(145, 1199, 1, 5),
(146, 1217, 2, 213),
(147, 1227, 3, 4213),
(148, 1233, 4, 23),
(149, 1273, 2, 312),
(150, 1326, 3, 432),
(151, 1365, 1, 386),
(152, 1392, 2, 39),
(153, 1462, 3, 59),
(154, 335, 1, 123),
(155, 337, 2, 8),
(156, 338, 3, 1),
(157, 339, 4, 239),
(158, 343, 2, 42),
(159, 344, 3, 4384),
(160, 347, 1, 32);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `stock_level_change_history`
--

CREATE TABLE IF NOT EXISTS `stock_level_change_history` (
  `stock_lvl_chng_id` int(11) NOT NULL AUTO_INCREMENT,
  `part_id_fk` int(11) NOT NULL,
  `from_location_fk` int(11) DEFAULT NULL,
  `to_location_fk` int(11) DEFAULT NULL,
  `stock_lvl_chng_quantity` int(11) NOT NULL,
  `stock_lvl_chng_datetime` timestamp NOT NULL DEFAULT current_timestamp(),
  `stock_lvl_chng_comment` varchar(255) DEFAULT NULL,
  `stock_lvl_chng_user_fk` int(11) NOT NULL,
  PRIMARY KEY (`stock_lvl_chng_id`),
  KEY `part_id` (`part_id_fk`),
  KEY `from_location` (`from_location_fk`),
  KEY `to_location` (`to_location_fk`),
  KEY `stock_lvl_chng_user_fk` (`stock_lvl_chng_user_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=251 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `stock_level_change_history`
--

INSERT INTO `stock_level_change_history` (`stock_lvl_chng_id`, `part_id_fk`, `from_location_fk`, `to_location_fk`, `stock_lvl_chng_quantity`, `stock_lvl_chng_datetime`, `stock_lvl_chng_comment`, `stock_lvl_chng_user_fk`) VALUES
(1, 350, 5, 6, 123, '2023-03-22 21:42:03', 'description', -1),
(2, 339, 5, 6, 123, '2023-03-22 21:43:57', 'description', -1),
(3, 339, 5, 6, 123, '2023-03-22 21:43:58', 'description', -1),
(4, 339, 5, 6, 123, '2023-03-22 21:43:59', 'description', -1),
(5, 339, 5, 6, 123, '2023-03-22 21:43:59', 'description', -1),
(6, 339, 5, 6, 123, '2023-03-22 21:43:59', 'description', -1),
(7, 339, 5, 6, 123, '2023-03-22 21:44:00', 'description', -1),
(8, 339, 5, 6, 123, '2023-03-22 21:44:00', 'description', -1),
(9, 339, 5, 6, 123, '2023-03-22 21:44:00', 'description', -1),
(10, 339, 5, 6, 123, '2023-03-22 21:44:00', 'description', -1),
(11, 339, 5, 6, 123, '2023-03-22 21:44:00', 'description', -1),
(12, 344, 5, 6, 123, '2023-03-22 21:48:45', 'description', -1),
(13, 344, 5, 6, 123, '2023-03-22 21:48:46', 'description', -1),
(14, 344, 5, 6, 123, '2023-03-22 21:48:46', 'description', -1),
(15, 344, 5, 6, 123, '2023-03-22 21:48:46', 'description', -1),
(16, 344, 5, 6, 123, '2023-03-22 21:48:46', 'description', -1),
(17, 344, 5, 6, 123, '2023-03-22 21:48:46', 'description', -1),
(18, 344, 5, 6, 123, '2023-03-22 21:48:46', 'description', -1),
(19, 344, 5, 6, 123, '2023-03-22 21:48:47', 'description', -1),
(20, 344, 5, 6, 123, '2023-03-22 21:48:47', 'description', -1),
(21, 348, 5, 6, 123, '2023-03-22 21:50:27', 'description', -1),
(22, 348, 5, 6, 123, '2023-03-22 21:50:28', 'description', -1),
(23, 348, 5, 6, 123, '2023-03-22 21:50:28', 'description', -1),
(24, 348, 5, 6, 123, '2023-03-22 21:50:28', 'description', -1),
(25, 347, 5, 6, 123, '2023-03-22 21:50:44', 'description', -1),
(26, 350, 5, 6, 5, '2023-03-22 21:52:14', 'bought them', -1),
(27, 348, 5, 6, 5, '2023-03-22 21:53:01', 'bought them', -1),
(28, 348, 5, 6, 5, '2023-03-22 21:53:02', 'bought them', -1),
(29, 348, 5, 6, 5, '2023-03-22 21:53:02', 'bought them', -1),
(30, 348, 5, 6, 5, '2023-03-22 21:53:02', 'bought them', -1),
(31, 348, 5, 6, 5, '2023-03-22 21:53:03', 'bought them', -1),
(32, 338, 5, 6, 5, '2023-03-22 21:53:34', 'bought them', -1),
(33, 338, 5, 6, 5, '2023-03-22 21:53:34', 'bought them', -1),
(34, 338, 5, 6, 5, '2023-03-22 21:53:34', 'bought them', -1),
(35, 338, 5, 6, 5, '2023-03-22 21:53:34', 'bought them', -1),
(36, 343, 5, 6, 123, '2023-03-22 22:41:40', 'description', -1),
(37, 343, 5, 6, 123, '2023-03-22 22:41:47', 'description', -1),
(38, 343, 5, 6, 123, '2023-03-22 22:41:48', 'description', -1),
(39, 343, 5, 6, 123, '2023-03-22 22:41:48', 'description', -1),
(40, 343, 5, 6, 123, '2023-03-22 22:41:48', 'description', -1),
(41, 344, 5, 6, 123, '2023-03-22 22:45:14', 'description', -1),
(42, 348, 5, 6, 123, '2023-03-22 22:47:10', 'description', -1),
(43, 348, 5, 6, 123, '2023-03-22 22:47:11', 'description', -1),
(44, 339, 5, 6, 123, '2023-03-22 22:47:26', 'description', -1),
(45, 339, 5, 6, 123, '2023-03-22 22:47:44', 'bought them', -1),
(46, 347, 5, 6, 5, '2023-03-22 22:48:38', 'bought them', -1),
(47, 337, 5, 6, 5, '2023-03-22 22:48:47', 'bought them', -1),
(48, 422, 5, 6, 123, '2023-03-22 23:26:07', '', -1),
(49, 422, 5, 6, 123, '2023-03-22 23:26:08', '', -1),
(50, 422, 5, 6, 123, '2023-03-22 23:26:10', 'description', -1),
(51, 343, 5, 6, 123, '2023-03-22 23:59:10', 'description', -1),
(52, 849, 5, 6, 32, '2023-03-23 00:06:12', 'bla', -1),
(53, 348, 5, 6, 1, '2023-03-23 00:06:46', 'Schwund', -1),
(54, 351, 5, 6, 21, '2023-03-23 00:07:38', 'bla', -1),
(55, 350, 5, 6, 123, '2023-03-23 13:17:09', 'description', -1),
(56, 339, 5, 6, 123, '2023-03-23 14:16:04', '53', -1),
(57, 339, 5, 6, 123345345, '2023-03-23 14:16:18', 'twth\"§$%&/(', -1),
(58, 343, 5, 6, 5, '2023-03-25 00:20:17', 'twth\"§$%&/(', -1),
(61, 335, 5, 6, 123, '2023-03-25 00:48:49', '123', -1),
(62, 335, 5, 6, 123, '2023-03-25 00:50:30', '123', -1),
(63, 335, 5, 1, 123, '2023-03-25 00:50:46', '123', -1),
(64, 335, 5, 1, 123, '2023-03-25 00:51:25', '12', -1),
(65, 335, 5, 1, 123, '2023-03-25 00:51:45', '123', -1),
(66, 335, 5, 1, 123123, '2023-03-25 00:52:07', '123123', -1),
(67, 335, 5, 1, 123, '2023-03-25 00:54:22', '123', -1),
(68, 338, 5, 1, 1, '2023-03-25 10:07:44', 'Trying 1', -1),
(69, 338, 5, 3, 1, '2023-03-25 10:08:42', 'Actually trying 1 now', -1),
(70, 532, 5, 3, 320, '2023-03-25 11:54:56', '', -1),
(71, 672, 5, 4, 433, '2023-03-25 12:02:23', '', -1),
(72, 618, 5, 2, 4214, '2023-03-25 12:03:44', '', -1),
(73, 344, 5, 4, 679, '2023-03-25 12:05:33', '', -1),
(74, 344, 5, 4, 678, '2023-03-25 12:05:56', '', -1),
(75, 1148, 5, 3, 96, '2023-03-25 12:06:13', '', -1),
(76, 1392, 5, 1, 39, '2023-03-25 12:19:07', '', -1),
(77, 1199, 5, 1, 5, '2023-03-25 12:19:16', '', -1),
(78, 337, 5, 4, 98, '2023-03-25 16:46:39', '', -1),
(79, 337, 5, 4, 101, '2023-03-25 16:47:03', '', -1),
(80, 344, 5, 4, 681, '2023-03-25 16:47:30', '', -1),
(81, 343, 5, 4, 14, '2023-03-25 16:47:54', '', -1),
(82, 350, 5, 4, 99, '2023-03-25 16:49:17', '', -1),
(83, 344, 5, 4, 682, '2023-03-25 16:51:05', '', -1),
(84, 1055, 5, 3, 37, '2023-03-25 16:51:18', '', -1),
(85, 351, 5, 4, 242, '2023-03-25 21:45:32', '', -1),
(86, 567, 5, 1, -906, '2023-03-25 21:49:53', '', -1),
(87, 567, 5, 1, 94, '2023-03-25 21:50:10', '', -1),
(88, 567, 5, 1, 94, '2023-03-25 21:50:10', '', -1),
(89, 339, 5, 1, 1457, '2023-03-25 21:58:23', '', -1),
(90, 351, 5, 4, 245, '2023-03-25 21:59:21', '', -1),
(91, 348, 5, 3, 351, '2023-03-25 22:01:27', '', -1),
(92, 339, 5, 1, 1460, '2023-03-25 22:03:59', '', -1),
(93, 339, 5, 1, 1463, '2023-03-25 22:04:05', '', -1),
(94, 339, 5, 1, -1457, '2023-03-25 22:04:05', '', -1),
(95, 339, 5, 1, -1457, '2023-03-25 22:04:05', '', -1),
(96, 339, 5, 1, 0, '2023-03-25 22:05:25', '', -1),
(97, 339, 5, 1, 0, '2023-03-25 22:05:25', '', -1),
(98, 339, 5, 1, 0, '2023-03-25 22:05:25', '', -1),
(99, 339, 5, 1, 0, '2023-03-25 22:05:25', '', -1),
(100, 339, 5, 1, 0, '2023-03-25 22:05:25', '', -1),
(101, 339, 5, 1, 0, '2023-03-25 22:05:25', '', -1),
(102, 339, 5, 1, 1000, '2023-03-25 22:06:12', '', -1),
(103, 339, 5, 1, 1000, '2023-03-25 22:06:12', '', -1),
(104, 339, 5, 1, 1000, '2023-03-25 22:06:12', '', -1),
(105, 339, 5, 1, 1000, '2023-03-25 22:06:12', '', -1),
(106, 339, 5, 1, 1000, '2023-03-25 22:06:12', '', -1),
(107, 339, 5, 1, 1000, '2023-03-25 22:06:12', '', -1),
(108, 339, 5, 1, 1000, '2023-03-25 22:06:12', '', -1),
(109, 339, 5, 1, 1000, '2023-03-25 22:06:12', '', -1),
(110, 339, 5, 1, 1000, '2023-03-25 22:06:12', '', -1),
(111, 339, 5, 1, 1000, '2023-03-25 22:06:12', '', -1),
(112, 339, 5, 1, 1000, '2023-03-25 22:06:12', '', -1),
(113, 339, 5, 1, 1000, '2023-03-25 22:06:12', '', -1),
(114, 339, 5, 1, 1000, '2023-03-25 22:06:12', '', -1),
(115, 339, 5, 1, 1000, '2023-03-25 22:06:12', '', -1),
(116, 339, 5, 1, 1000, '2023-03-25 22:06:12', '', -1),
(117, 339, 5, 1, 1000, '2023-03-25 22:06:12', '', -1),
(118, 339, 5, 1, 1000, '2023-03-25 22:06:12', '', -1),
(119, 339, 5, 1, 1000, '2023-03-25 22:06:12', '', -1),
(120, 338, 5, 2, 392, '2023-03-25 22:08:24', '', -1),
(121, 351, 5, 4, 2, '2023-03-25 22:10:16', '', -1),
(122, 351, 5, 4, 2, '2023-03-25 22:10:16', '', -1),
(123, 351, 5, 4, 2, '2023-03-25 22:10:16', '', -1),
(124, 351, 5, 4, 2, '2023-03-25 22:10:16', '', -1),
(125, 351, 5, 4, 2, '2023-03-25 22:10:16', '', -1),
(126, 351, 5, 4, 2, '2023-03-25 22:10:16', '', -1),
(127, 350, 5, 4, 1, '2023-03-25 22:18:17', '', -1),
(128, 350, 5, 4, 2, '2023-03-25 22:19:06', '', -1),
(129, 350, 5, 4, 3, '2023-03-25 22:19:10', '', -1),
(130, 350, 5, 4, 3, '2023-03-25 22:19:10', '', -1),
(131, 350, 5, 4, -2, '2023-03-25 22:19:13', '', -1),
(132, 350, 5, 4, -2, '2023-03-25 22:19:13', '', -1),
(133, 350, 5, 4, -2, '2023-03-25 22:19:13', '', -1),
(134, 350, 5, 4, -2, '2023-03-25 22:19:13', '', -1),
(135, 350, 5, 4, -1, '2023-03-25 22:19:34', '', -1),
(136, 350, 5, 4, -1, '2023-03-25 22:19:34', '', -1),
(137, 350, 5, 4, -1, '2023-03-25 22:19:34', '', -1),
(138, 350, 5, 4, -1, '2023-03-25 22:19:34', '', -1),
(139, 350, 5, 4, -1, '2023-03-25 22:19:34', '', -1),
(140, 350, 5, 4, -1, '2023-03-25 22:19:34', '', -1),
(141, 350, 5, 4, -1, '2023-03-25 22:19:34', '', -1),
(142, 350, 5, 4, -1, '2023-03-25 22:19:34', '', -1),
(143, 350, 5, 4, 0, '2023-03-25 22:20:12', '', -1),
(144, 350, 5, 4, 0, '2023-03-25 22:20:12', '', -1),
(145, 350, 5, 4, 1, '2023-03-25 22:22:14', '', -1),
(146, 350, 5, 4, 2, '2023-03-25 22:22:17', '', -1),
(147, 350, 5, 4, 3, '2023-03-25 22:22:19', '', -1),
(148, 350, 5, 4, -2, '2023-03-25 22:22:59', '', -1),
(149, 350, 5, 4, -1, '2023-03-25 22:26:26', '', -1),
(150, 350, 5, 4, 0, '2023-03-25 22:26:30', '', -1),
(151, 350, 5, 4, 1, '2023-03-25 22:28:40', '', -1),
(152, 350, 5, 4, 1, '2023-03-25 22:28:48', '', -1),
(153, 350, 5, 4, 1, '2023-03-25 22:28:58', '', -1),
(154, 350, 5, 4, 2, '2023-03-25 22:29:00', '', -1),
(155, 350, 5, 4, -1, '2023-03-25 22:29:06', '', -1),
(156, 350, 5, 4, -2, '2023-03-25 22:29:58', '', -1),
(157, 350, 5, 4, -1, '2023-03-25 22:30:03', '', -1),
(158, 350, 5, 4, -2, '2023-03-25 22:30:13', '', -1),
(159, 350, 5, 4, -3, '2023-03-25 22:30:14', '', -1),
(160, 350, 5, 4, -4, '2023-03-25 22:30:16', '', -1),
(161, 350, 5, 4, -3, '2023-03-25 22:30:20', '', -1),
(162, 350, 5, 4, -2, '2023-03-25 22:30:22', '', -1),
(163, 350, 5, 4, -3, '2023-03-25 22:31:29', '', -1),
(164, 350, 5, 4, -4, '2023-03-25 22:31:33', '', -1),
(165, 350, 5, 4, -5, '2023-03-25 22:31:35', '', -1),
(166, 350, 5, 4, -4, '2023-03-25 22:34:52', '', -1),
(167, 350, 5, 4, -3, '2023-03-25 22:36:48', '', -1),
(168, 350, 5, 4, -2, '2023-03-25 22:36:52', '', -1),
(169, 350, 5, 4, -1, '2023-03-25 22:36:54', '', -1),
(170, 350, 5, 4, 0, '2023-03-25 22:36:56', '', -1),
(171, 350, 5, 4, 1, '2023-03-25 22:36:58', '', -1),
(172, 350, 5, 4, 0, '2023-03-25 22:37:00', '', -1),
(173, 350, 5, 4, -1, '2023-03-25 22:37:02', '', -1),
(174, 350, 5, 4, -2, '2023-03-25 22:37:04', '', -1),
(175, 350, 5, 4, 1, '2023-03-25 23:05:03', '', -1),
(176, 350, 5, 4, 0, '2023-03-25 23:05:10', '', -1),
(177, 350, 5, 4, -1, '2023-03-25 23:05:33', '', -1),
(178, 350, 5, 4, 1, '2023-03-25 23:05:42', '', -1),
(179, 347, 5, 2, 88, '2023-03-26 00:13:31', '', -1),
(180, 347, 5, 2, 78, '2023-03-26 00:13:37', '', -1),
(181, 347, 5, 2, 88, '2023-03-26 00:13:41', '', -1),
(182, 347, 5, 2, 98, '2023-03-26 00:13:43', '', -1),
(183, 347, 5, 2, 101, '2023-03-26 00:15:02', '', -1),
(184, 347, 5, 2, 104, '2023-03-26 00:15:06', '', -1),
(185, 347, 5, 2, 104, '2023-03-26 00:15:06', '', -1),
(186, 347, 5, 2, 107, '2023-03-26 00:15:09', '', -1),
(187, 347, 5, 2, 107, '2023-03-26 00:15:09', '', -1),
(188, 347, 5, 2, 107, '2023-03-26 00:15:09', '', -1),
(189, 347, 5, 2, 110, '2023-03-26 00:15:15', '', -1),
(190, 347, 5, 2, 110, '2023-03-26 00:15:15', '', -1),
(191, 347, 5, 2, 110, '2023-03-26 00:15:15', '', -1),
(192, 347, 5, 2, 110, '2023-03-26 00:15:15', '', -1),
(193, 348, 5, 3, 354, '2023-03-26 00:16:00', '', -1),
(194, 348, 5, 3, 357, '2023-03-26 00:16:03', '', -1),
(195, 348, 5, 3, 360, '2023-03-26 00:16:05', '', -1),
(196, 348, 5, 3, 363, '2023-03-26 00:16:07', '', -1),
(197, 348, 5, 3, 360, '2023-03-26 00:16:09', '', -1),
(198, 348, 5, 3, 357, '2023-03-26 00:16:12', '', -1),
(199, 348, 5, 3, 457, '2023-03-26 00:21:29', '', -1),
(200, 348, 5, 3, 357, '2023-03-26 00:21:35', '', -1),
(201, 439, 5, 1, 99, '2023-03-26 00:51:54', '', -1),
(202, 344, 5, 1, 41, '2023-03-26 08:58:06', '', -1),
(203, 347, 5, 1, 1, '2023-03-26 09:57:29', '', -1),
(204, 348, 5, 1, 1, '2023-03-26 09:58:20', '', -1),
(205, 348, 5, 1, 1, '2023-03-26 09:58:50', '', -1),
(206, 348, 5, 1, 1, '2023-03-26 10:00:19', '', -1),
(207, 350, 5, 1, 1, '2023-03-26 10:01:42', '', -1),
(208, 339, 5, 1, 1001, '2023-03-26 10:36:47', '', -1),
(209, 339, 5, 1, 1000, '2023-03-26 10:36:53', '', -1),
(210, 339, 5, 1, 1, '2023-03-26 10:36:59', '', -1),
(211, 339, 5, 1, 2, '2023-03-26 10:38:09', '', -1),
(212, 348, 5, 1, 2, '2023-03-26 10:42:34', '', -1),
(213, 350, 5, 4, 22, '2023-03-26 10:43:07', '', -1),
(214, 350, 5, 4, 21, '2023-03-26 10:43:21', '', -1),
(215, 348, 5, 1, 123, '2023-03-26 10:44:57', '', -1),
(216, 348, 5, 1, 23, '2023-03-26 10:48:05', '', -1),
(217, 347, 5, 1, 32, '2023-03-26 10:49:00', '', -1),
(218, 439, 3, 1, 130, '2023-03-26 11:27:33', '', -1),
(219, 439, 3, 1, -98, '2023-03-26 11:30:20', '', -1),
(221, 439, 3, 1, 3677, '2023-03-26 11:33:54', '', -1),
(222, 439, 3, 1, 3676, '2023-03-26 11:36:40', '', -1),
(223, 439, 1, 3, 3679, '2023-03-26 11:49:10', '', -1),
(224, 439, NULL, 3, 3680, '2023-03-26 11:54:54', '', -1),
(225, 439, 1, NULL, 130, '2023-03-26 12:06:43', '', -1),
(226, 439, NULL, 1, 131, '2023-03-26 12:06:48', '', -1),
(227, 439, 3, 1, 3685, '2023-03-26 12:07:08', '', -1),
(228, 439, 3, 1, 3552, '2023-03-26 12:08:10', '', -1),
(229, 439, 3, NULL, 1, '2023-03-26 12:10:35', '', -1),
(230, 439, 1, NULL, 0, '2023-03-26 12:10:45', '', -1),
(231, 439, 3, 1, 1, '2023-03-26 12:10:53', '', -1),
(232, 439, 1, 3, 9, '2023-03-26 12:29:34', '', -1),
(233, 439, NULL, 1, 0, '2023-03-26 12:30:33', '', -1),
(234, 439, 3, NULL, 0, '2023-03-26 12:30:42', '', -1),
(235, 439, NULL, 3, 120, '2023-03-26 12:30:48', '', -1),
(236, 439, 1, 3, 120, '2023-03-26 12:30:56', '', -1),
(237, 439, NULL, 1, 0, '2023-03-26 12:33:05', '', -1),
(238, 439, 3, 1, 120, '2023-03-26 12:33:22', '', -1),
(239, 422, 3, 1, 100, '2023-03-26 15:54:17', '', -1),
(240, 439, 2, 1, 432, '2023-03-26 16:03:28', 'FK PR6', -1),
(241, 439, 3, 1, 120, '2023-03-26 16:29:18', 'FK PR6', -1),
(242, 532, NULL, 3, 2320, '2023-03-26 16:52:25', '', -1),
(243, 477, NULL, 2, 2678, '2023-03-26 16:55:48', '', -1),
(244, 422, 3, NULL, 0, '2023-03-26 16:56:05', '', -1),
(245, 422, 2, 1, 187, '2023-03-26 16:56:16', '', -1),
(246, 1273, 3, 4, 187, '2023-03-27 14:34:50', '', -1),
(247, 1273, NULL, 4, 1062, '2023-03-27 14:51:14', 'For PR8', -1),
(248, 1273, 4, NULL, 62, '2023-03-27 15:11:35', '', -1),
(249, 343, 4, 1, 14, '2023-03-27 15:53:09', 'Field Kit PR7', -1),
(250, 439, 1, NULL, 572, '2023-03-27 19:56:22', 'Für DIY Kit', -1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_passwd` varchar(255) NOT NULL,
  `user_group_fk` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `user_group_fk` (`user_group_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`user_id`, `user_passwd`, `user_group_fk`, `user_name`, `user_email`) VALUES
(-1, '', 1, 'demo user', ''),
(1, '', 1, 'chrisi', 'christian@koma-elektronik.com');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_groups`
--

CREATE TABLE IF NOT EXISTS `user_groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) NOT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `user_groups`
--

INSERT INTO `user_groups` (`group_id`, `group_name`) VALUES
(1, 'First Group');

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `bom_elements`
--
ALTER TABLE `bom_elements`
  ADD CONSTRAINT `bom_elements_ibfk_1` FOREIGN KEY (`bom_id_fk`) REFERENCES `bom_names` (`bom_id`),
  ADD CONSTRAINT `bom_elements_ibfk_2` FOREIGN KEY (`part_id_fk`) REFERENCES `parts` (`part_id`);

--
-- Constraints der Tabelle `bom_runs`
--
ALTER TABLE `bom_runs`
  ADD CONSTRAINT `bom_runs_ibfk_1` FOREIGN KEY (`bom_id_fk`) REFERENCES `bom_names` (`bom_id`);

--
-- Constraints der Tabelle `minstock_levels`
--
ALTER TABLE `minstock_levels`
  ADD CONSTRAINT `location_id` FOREIGN KEY (`location_id_fk`) REFERENCES `location_names` (`location_id`),
  ADD CONSTRAINT `minstock_levels_ibfk_1` FOREIGN KEY (`part_id_fk`) REFERENCES `parts` (`part_id`);

--
-- Constraints der Tabelle `parts`
--
ALTER TABLE `parts`
  ADD CONSTRAINT `parts_ibfk_1` FOREIGN KEY (`part_category_fk`) REFERENCES `part_categories` (`category_id`),
  ADD CONSTRAINT `parts_ibfk_2` FOREIGN KEY (`part_unit_fk`) REFERENCES `part_units` (`unit_id`),
  ADD CONSTRAINT `parts_ibfk_3` FOREIGN KEY (`part_footprint_fk`) REFERENCES `footprints` (`footprint_id`),
  ADD CONSTRAINT `parts_ibfk_4` FOREIGN KEY (`part_owner_u_fk`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `parts_ibfk_5` FOREIGN KEY (`part_owner_g_fk`) REFERENCES `user_groups` (`group_id`);

--
-- Constraints der Tabelle `stock_levels`
--
ALTER TABLE `stock_levels`
  ADD CONSTRAINT `part_id` FOREIGN KEY (`part_id_fk`) REFERENCES `parts` (`part_id`),
  ADD CONSTRAINT `stock_levels_ibfk_1` FOREIGN KEY (`location_id_fk`) REFERENCES `location_names` (`location_id`);

--
-- Constraints der Tabelle `stock_level_change_history`
--
ALTER TABLE `stock_level_change_history`
  ADD CONSTRAINT `stock_level_change_history_ibfk_1` FOREIGN KEY (`part_id_fk`) REFERENCES `parts` (`part_id`),
  ADD CONSTRAINT `stock_level_change_history_ibfk_2` FOREIGN KEY (`from_location_fk`) REFERENCES `location_names` (`location_id`),
  ADD CONSTRAINT `stock_level_change_history_ibfk_3` FOREIGN KEY (`to_location_fk`) REFERENCES `location_names` (`location_id`),
  ADD CONSTRAINT `stock_level_change_history_ibfk_4` FOREIGN KEY (`stock_lvl_chng_user_fk`) REFERENCES `users` (`user_id`);

--
-- Constraints der Tabelle `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`user_group_fk`) REFERENCES `user_groups` (`group_id`);
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
