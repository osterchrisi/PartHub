-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 16. Mrz 2023 um 11:35
-- Server-Version: 10.3.38-MariaDB-0ubuntu0.20.04.1
-- PHP-Version: 8.2.3

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
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(46, 32, 351, 32);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bom_names`
--

CREATE TABLE IF NOT EXISTS `bom_names` (
  `bom_id` int(11) NOT NULL AUTO_INCREMENT,
  `bom_name` varchar(255) NOT NULL,
  `bom_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`bom_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `bom_names`
--

INSERT INTO `bom_names` (`bom_id`, `bom_name`, `bom_description`) VALUES
(28, 'First BOM', 'It\'s the first one!'),
(29, 'Second BOM', 'Let\'s do another'),
(30, '10 to 50', NULL),
(31, '234', NULL),
(32, 'Second BOM', NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `location_names`
--

INSERT INTO `location_names` (`location_id`, `location_name`, `location_description`) VALUES
(1, 'Main Storage', 'Like, all of it'),
(2, 'EMS Partner', ''),
(3, 'Hardware Supplier', ''),
(4, 'External Storage', NULL);

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
(335, 'BC847B', 'Transistor NPN 45V 100mA SOT-23', 'Your general NPN transistor workhorse', '2023-03-07 00:24:12', 1, 1, 1, 1, 1),
(337, 'CD4017BE', 'Decade Counter/Divider with 10 Decoded Outputs', 'Can be used as a sequencer or for LED chasers.', '2023-03-07 00:24:12', 1, 1, 1, 1, 1),
(338, '1N4148', 'General Purpose Diode', 'fast-switching, widely used signal diode', '0000-00-00 00:00:00', 1, 1, 1, 1, 1),
(339, 'LM386N-1', 'Audio Amplifier', 'Low voltage audio power amplifier, ideal for battery-powered devices.', '2023-03-07 00:24:12', 1, 1, 1, 1, 1),
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
(618, 'BC327', 'PNP transistor', 'good for switching and amplification', '0000-00-00 00:00:00', 1, 1, 1, 1, 1),
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
(1148, 'Inductor 10uH, 20%, 0805', 'Surface mount inductor with 10 microhenry inductance and 20% tolerance', 'Good for power supply filtering and DC-DC converter applications', '2022-03-06 09:15:38', 1, 1, 1, 1, 1),
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
(1, 335, 1, 213),
(2, 337, 2, 4213),
(3, 338, 3, 23),
(4, 339, 4, 312),
(5, 343, 2, 432),
(6, 344, 3, 386),
(7, 347, 1, 39),
(8, 348, 2, 59),
(9, 350, 3, 1932),
(10, 351, 4, 8),
(11, 422, 2, 187),
(12, 439, 3, 239),
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
(47, 1273, 4, 75),
(48, 1326, 1, 1456),
(49, 1365, 2, 13),
(50, 1392, 1, 38),
(51, 1462, 2, 98),
(52, 335, 3, 38),
(53, 337, 4, 97),
(54, 338, 2, 389),
(55, 339, 3, 3),
(56, 343, 1, 90),
(57, 344, 4, 678),
(58, 347, 1, 3),
(59, 348, 2, 213),
(60, 350, 3, 4213),
(61, 351, 1, 23),
(62, 422, 1, 312),
(63, 439, 2, 432),
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
(84, 1055, 3, 3),
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
(98, 1273, 3, 187),
(99, 1326, 1, 239),
(100, 1365, 1, 42),
(101, 1392, 2, 4384),
(102, 1462, 3, 94),
(103, 335, 4, 1754),
(104, 337, 2, 11),
(105, 338, 3, 75),
(106, 339, 1, 1456),
(107, 343, 4, 13),
(108, 344, 1, 38),
(109, 347, 2, 98),
(110, 348, 3, 38),
(111, 350, 4, 97),
(112, 351, 2, 389),
(113, 422, 3, 3),
(114, 439, 1, 90),
(115, 477, 2, 678),
(116, 532, 3, 3),
(117, 567, 4, 213),
(118, 618, 2, 4213),
(119, 629, 3, 23),
(120, 655, 1, 312),
(121, 672, 4, 432),
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
(140, 1148, 3, 97),
(141, 1149, 4, 389),
(142, 1162, 2, 3),
(143, 1165, 3, 90),
(144, 1187, 1, 678),
(145, 1199, 1, 3),
(146, 1217, 2, 213),
(147, 1227, 3, 4213),
(148, 1233, 4, 23),
(149, 1273, 2, 312),
(150, 1326, 3, 432),
(151, 1365, 1, 386),
(152, 1392, 2, 39),
(153, 1462, 3, 59),
(154, 335, 1, 1932),
(155, 337, 2, 8),
(156, 338, 3, 187),
(157, 339, 4, 239),
(158, 343, 2, 42),
(159, 344, 3, 4384),
(160, 347, 1, 94);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `stock_level_change_history`
--

CREATE TABLE IF NOT EXISTS `stock_level_change_history` (
  `stock_lvl_chng_id` int(11) NOT NULL AUTO_INCREMENT,
  `part_id_fk` int(11) NOT NULL,
  `from_location_fk` int(11) NOT NULL,
  `to_location_fk` int(11) NOT NULL,
  `stock_lvl_chng_quantity` int(11) NOT NULL,
  `stock_lvl_chng_datetime` timestamp NOT NULL DEFAULT current_timestamp(),
  `stock_lvl_chng_comment` int(11) DEFAULT NULL,
  PRIMARY KEY (`stock_lvl_chng_id`),
  KEY `part_id` (`part_id_fk`),
  KEY `from_location` (`from_location_fk`),
  KEY `to_location` (`to_location_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`user_id`, `user_passwd`, `user_group_fk`, `user_name`, `user_email`) VALUES
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
  ADD CONSTRAINT `stock_level_change_history_ibfk_3` FOREIGN KEY (`to_location_fk`) REFERENCES `location_names` (`location_id`);

--
-- Constraints der Tabelle `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`user_group_fk`) REFERENCES `user_groups` (`group_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;