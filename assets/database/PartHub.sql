-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 19, 2023 at 12:22 PM
-- Server version: 10.3.38-MariaDB-0ubuntu0.20.04.1
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `PartHub`
--

-- --------------------------------------------------------

--
-- Table structure for table `bom_elements`
--

CREATE TABLE IF NOT EXISTS `bom_elements` (
  `bom_elements_id` int(11) NOT NULL AUTO_INCREMENT,
  `bom_id_fk` int(11) NOT NULL,
  `part_id_fk` int(11) NOT NULL,
  `element_quantity` int(11) NOT NULL,
  PRIMARY KEY (`bom_elements_id`) USING BTREE,
  KEY `bom_elements_ibfk_2` (`part_id_fk`),
  KEY `bom_elements_ibfk_1` (`bom_id_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bom_elements`
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
(42, 31, 351, 2),
(58, 42, 1148, 2),
(59, 42, 849, 4),
(60, 42, 477, 10),
(61, 42, 567, 15),
(62, 42, 1055, 4),
(81, 62, 335, 1),
(82, 63, 335, 1),
(83, 68, 335, 1),
(84, 68, 344, 3);

-- --------------------------------------------------------

--
-- Table structure for table `bom_names`
--

CREATE TABLE IF NOT EXISTS `bom_names` (
  `bom_id` int(11) NOT NULL AUTO_INCREMENT,
  `bom_name` varchar(255) NOT NULL,
  `bom_description` varchar(255) DEFAULT NULL,
  `bom_owner_g_fk` int(11) DEFAULT NULL,
  `bom_owner_u_fk` int(11) NOT NULL,
  PRIMARY KEY (`bom_id`),
  KEY `bom_owner_u_fk` (`bom_owner_u_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bom_names`
--

INSERT INTO `bom_names` (`bom_id`, `bom_name`, `bom_description`, `bom_owner_g_fk`, `bom_owner_u_fk`) VALUES
(28, 'First BOM', 'It\'s the first one!', NULL, -1),
(29, 'Second BOM', 'Let\'s do another', NULL, -1),
(30, '10 to 50 element quantities', 'Go figure', NULL, -1),
(31, 'Luxury product', 'It\'s pricey!', NULL, -1),
(42, 'New awesome product', 'Description added afterwards', NULL, -1),
(62, 'name', 'desc', NULL, -1),
(63, 'now it', 'works for sure', NULL, -1),
(64, 'new', 'bom', NULL, -1),
(68, 'Hectic Oscillator', 'It\'s, like, crazy', NULL, -1);

-- --------------------------------------------------------

--
-- Table structure for table `bom_runs`
--

CREATE TABLE IF NOT EXISTS `bom_runs` (
  `bom_run_id` int(11) NOT NULL AUTO_INCREMENT,
  `bom_id_fk` int(11) NOT NULL,
  `bom_run_quantity` int(11) NOT NULL,
  `bom_run_datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `bom_run_user_fk` int(11) NOT NULL,
  PRIMARY KEY (`bom_run_id`),
  KEY `bom_runs_ibfk_1` (`bom_id_fk`),
  KEY `bom_run_user_fk` (`bom_run_user_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `footprints`
--

CREATE TABLE IF NOT EXISTS `footprints` (
  `footprint_id` int(11) NOT NULL AUTO_INCREMENT,
  `footprint_name` varchar(255) NOT NULL,
  `footprint_alias` varchar(255) NOT NULL,
  PRIMARY KEY (`footprint_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `footprints`
--

INSERT INTO `footprints` (`footprint_id`, `footprint_name`, `footprint_alias`) VALUES
(1, 'A Footprint', 'Its alias'),
(2, 'TO-92', '');

-- --------------------------------------------------------

--
-- Table structure for table `location_names`
--

CREATE TABLE IF NOT EXISTS `location_names` (
  `location_id` int(11) NOT NULL AUTO_INCREMENT,
  `location_name` varchar(255) NOT NULL,
  `location_description` varchar(255) DEFAULT NULL,
  `location_owner_u_fk` int(11) NOT NULL,
  `location_owner_g_fk` int(11) DEFAULT NULL,
  PRIMARY KEY (`location_id`),
  KEY `location_owner_g_fk` (`location_owner_g_fk`),
  KEY `location_owner_u_fk` (`location_owner_u_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `location_names`
--

INSERT INTO `location_names` (`location_id`, `location_name`, `location_description`, `location_owner_u_fk`, `location_owner_g_fk`) VALUES
(1, 'Main Storage', 'Like, all of it', -1, NULL),
(2, 'EMS Partner', '', -1, NULL),
(3, 'Hardware Supplier', '', -1, NULL),
(4, 'External Storage', NULL, -1, NULL),
(5, 'Increase', 'General Stock Increase', -1, NULL),
(6, 'Decrease', 'General Stock Decrease', -1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `minstock_levels`
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
-- Table structure for table `parts`
--

CREATE TABLE IF NOT EXISTS `parts` (
  `part_id` int(11) NOT NULL AUTO_INCREMENT,
  `part_name` varchar(255) NOT NULL,
  `part_description` varchar(255) DEFAULT NULL,
  `part_comment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `part_category_fk` int(11) DEFAULT 1,
  `part_footprint_fk` int(11) DEFAULT 1,
  `part_unit_fk` int(11) DEFAULT 1,
  `part_owner_u_fk` int(11) NOT NULL,
  `part_owner_g_fk` int(11) DEFAULT NULL,
  PRIMARY KEY (`part_id`),
  KEY `part_category_fk` (`part_category_fk`),
  KEY `part_unit_fk` (`part_unit_fk`),
  KEY `part_footprint_fk` (`part_footprint_fk`),
  KEY `part_owner_u_fk` (`part_owner_u_fk`),
  KEY `part_owner_g_fk` (`part_owner_g_fk`),
  KEY `part_name` (`part_name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1748 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parts`
--

INSERT INTO `parts` (`part_id`, `part_name`, `part_description`, `part_comment`, `created_at`, `part_category_fk`, `part_footprint_fk`, `part_unit_fk`, `part_owner_u_fk`, `part_owner_g_fk`) VALUES
(335, 'BC847B', 'Transistor NPN 45V 100mA SOT-23', 'Your general NPN transistor workhorse', '2023-03-07 00:24:12', 3, 2, 1, -1, 1),
(337, 'CD4017BE', 'Decade Counter/Divider with 10 Decoded Outputs', 'Can be used as a sequencer or for LED chasers.', '2023-03-07 00:24:12', 2, 1, 1, -1, 1),
(338, '1N4148', 'General Purpose Diode', 'fast-switching, widely used signal diode', '0000-00-00 00:00:00', 1, 1, 1, -1, 1),
(339, 'LM386N-1', 'Audio Amplifier', 'Low voltage audio power amplifier, ideal for battery-powered devices.', '2023-03-07 00:24:12', 3, 1, 1, -1, 1),
(343, 'LM7905', 'Fixed Negative Voltage Regulator', 'Outputs a constant -5V DC voltage.', '2023-03-07 00:24:12', 1, 1, 1, -1, 1),
(344, 'LM7912', 'Fixed Negative Voltage Regulator', 'Outputs a constant -12V DC voltage.', '2023-03-07 00:24:12', 1, 1, 1, -1, 1),
(347, 'NE555P', 'Timer IC', 'Can be used as a clock, pulse generator or oscillator.', '2023-03-07 00:24:12', 1, 1, 1, -1, 1),
(348, 'LM324N', 'Quad Op-Amp IC', 'Has four independent operational amplifiers in one package.', '2023-03-07 00:24:12', 1, 1, 1, -1, 1),
(350, 'LM741CN', 'Op-Amp IC', 'A widely used operational amplifier with high gain and low distortion.', '2023-03-07 00:24:12', 1, 1, 1, -1, 1),
(351, '1N4007', 'Rectifier Diode', 'Can handle a maximum current of 1A and a maximum voltage of 1000V.', '2023-03-07 00:24:12', 1, 1, 1, -1, 1),
(422, 'Inductor 100uH, 10%, 0805', 'An inductor with 100uH', 'Ideal for low-frequency filtering applications', '2022-03-06 09:15:34', 1, 1, 1, -1, 1),
(439, 'Transistor', 'Transistor', 'Generic transistor', '2023-03-06 13:12:24', 1, 1, 1, -1, 1),
(477, 'Capacitor 10uF', '10uF Capacaitor, 35V', 'Generic capacitor', '2023-03-06 13:12:24', 1, 1, 1, -1, 1),
(532, 'Capacitor 100pF, 5%, 0603', 'Surface mount capacitor with 100 picofarad capacitance and 5% tolerance', 'Useful for high-frequency filtering and decoupling applications', '2022-03-06 09:15:39', 1, 1, 1, -1, 1),
(567, 'Resistor 10K, 1%, 0603', 'Surface mount resistor with 10K ohm resistance and 1% tolerance', 'Good for precision circuits', '2022-03-06 09:15:32', 1, 1, 1, -1, 1),
(618, 'BC327', 'PNP transistor', 'good for switching and amplification', '0000-00-00 00:00:00', 13, 1, 1, -1, 1),
(629, 'Capacitor 0.1uF, 10%, 0805', 'Surface mount capacitor with 0.1 microfarad capacitance and 10% tolerance', 'Useful for decoupling and filtering applications', '2022-03-06 09:15:41', 1, 1, 1, -1, 1),
(655, 'Capacitor 1uF, 10%, 0805', 'Surface mount capacitor with 1 microfarad capacitance and 10% tolerance', 'Ideal for audio filtering and coupling applications', '2022-03-06 09:15:36', 1, 1, 1, -1, 1),
(672, 'LM7805', 'Voltage regulator', 'output voltage of 5V, maximum current of 1A', '0000-00-00 00:00:00', 1, 1, 1, -1, 1),
(680, 'LM7812', 'Voltage regulator', 'output voltage of 12V, maximum current of 1A', '0000-00-00 00:00:00', 1, 1, 1, -1, 1),
(699, 'LM7808', 'Voltage regulator', 'output voltage of 8V, maximum current of 1A', '0000-00-00 00:00:00', 1, 1, 1, -1, 1),
(712, 'Resistor 10K', 'Resistor, 10K ohm, 1%, 0603', 'Some fitting comment', '2023-03-06 13:12:24', 1, 1, 1, -1, 1),
(743, 'LM7809', 'Voltage regulator', 'output voltage of 9V, maximum current of 1A', '0000-00-00 00:00:00', 1, 1, 1, -1, 1),
(791, 'Resistor 1K, 1%, 1206', 'Surface mount resistor with 1K ohm resistance and 1% tolerance', 'Ideal for precision voltage dividers and signal conditioning circuits', '2022-03-06 09:15:40', 1, 1, 1, -1, 1),
(849, 'LM317', 'Adjustable voltage regulator', 'output voltage can be adjusted from 1.25V to 37V', '0000-00-00 00:00:00', 1, 1, 1, -1, 1),
(868, '74HC595', '8-bit shift register', 'great for expanding outputs of microcontrollers', '0000-00-00 00:00:00', 1, 1, 1, -1, 1),
(893, 'Capacitor 10uF, 20%, 0805', 'Surface mount capacitor with 10 microfarad capacitance and 20% tolerance', 'Useful for smoothing out power supply voltages', '2022-03-06 09:15:33', 1, 1, 1, -1, 1),
(916, 'LM386', 'Audio amplifier', 'low voltage and low current operation, great for small audio projects', '0000-00-00 00:00:00', 1, 1, 1, -1, 1),
(964, 'NE555', 'Timer IC', 'versatile and widely used timer IC', '0000-00-00 00:00:00', 1, 1, 1, -1, 1),
(972, 'ATtiny85', 'Microcontroller', 'low-power, high-performance AVR microcontroller with 8KB of flash memory', '0000-00-00 00:00:00', 1, 1, 1, -1, 1),
(986, 'LM317T', 'Adjustable voltage regulator', 'output voltage can be adjusted from 1.25V to 37V', '0000-00-00 00:00:00', 1, 1, 1, -1, 1),
(998, 'Resistor 1M, 5%, 1206', 'Surface mount resistor with 1 megohm resistance and 5% tolerance', 'Useful for high-impedance voltage dividers', '2022-03-06 09:15:35', 1, 1, 1, -1, 1),
(1055, 'ATmega328P', 'Microcontroller', 'powerful 8-bit AVR microcontroller with 32KB of flash memory', '0000-00-00 00:00:00', 1, 1, 1, -1, 1),
(1076, 'ULN2003', 'Darlington transistor array', 'used for driving high-power devices with low-power control signals', '0000-00-00 00:00:00', 1, 1, 1, -1, 1),
(1077, 'LM2596', 'Adjustable voltage regulator', 'high efficiency, output voltage can be adjusted from 1.23V to 37V', '0000-00-00 00:00:00', 1, 1, 1, -1, 1),
(1101, 'BC548', 'NPN transistor', 'good for switching and amplification', '0000-00-00 00:00:00', 1, 1, 1, -1, 1),
(1112, 'LM7807', 'Voltage regulator', 'output voltage of 7V, maximum current of 1A', '0000-00-00 00:00:00', 1, 1, 1, -1, 1),
(1148, 'Inductor 10uH, 20%, 0805', 'Surface mount inductor with 10 microhenry inductance and 20% tolerance', 'Good for power supply filtering and DC-DC converter applications', '2022-03-06 09:15:38', 4, 1, 1, -1, 1),
(1149, 'CD4017', 'Decade counter', 'can drive up to 10 LEDs, great for sequencing and timing circuits', '0000-00-00 00:00:00', 1, 1, 1, -1, 1),
(1162, 'LM741', 'Operational amplifier', 'high-gain, versatile op-amp for various applications', '0000-00-00 00:00:00', 1, 1, 1, -1, 1),
(1165, 'LM7806', 'Voltage regulator', 'output voltage of 6V, maximum current of 1A', '0000-00-00 00:00:00', 1, 1, 1, -1, 1),
(1187, 'Resistor 100K, 5%, 1206', 'Surface mount resistor with 100K ohm resistance and 5% tolerance', 'Commonly used for biasing transistors and op-amps', '2022-03-06 09:15:37', 1, 1, 1, -1, 1),
(1199, 'Inductor 4.7uH, 10%, 0805', 'Surface mount inductor with 4.7 microhenry inductance and 10% tolerance', 'Ideal for power supply filtering and switching regulator applications', '2022-03-06 09:15:43', 1, 1, 1, -1, 1),
(1217, 'LM358N', 'Dual operational amplifier', 'low power consumption, wide supply voltage range', '0000-00-00 00:00:00', 1, 1, 1, -1, 1),
(1227, 'IRFZ44N', 'N-Channel MOSFET', 'high-current capability, low on-resistance, fast switching speed', '0000-00-00 00:00:00', 1, 1, 1, -1, 1),
(1233, 'Resistor 1.5K, 5%, 1206', 'Surface mount resistor with 1.5K ohm resistance and 5% tolerance', 'Good for audio and low-frequency signal conditioning', '2022-03-06 09:15:42', 1, 1, 1, -1, 1),
(1273, 'PC817', 'Optocoupler', 'used for signal isolation and noise reduction in digital circuits', '0000-00-00 00:00:00', 1, 1, 1, -1, 1),
(1326, 'LM393', 'Dual voltage comparator', 'low power consumption, wide supply voltage range', '0000-00-00 00:00:00', 1, 1, 1, -1, 1),
(1365, 'IRF540N', 'N-Channel MOSFET', 'high-current capability, low on-resistance, fast switching speed', '0000-00-00 00:00:00', 1, 1, 1, -1, 1),
(1392, 'BC639', 'PNP transistor', 'good for switching and amplification', '0000-00-00 00:00:00', 1, 1, 1, -1, 1),
(1491, 'BC847B', 'Transistor NPN 45V 100mA SOT-23', 'Your general NPN transistor workhorse', '2023-03-07 00:24:12', 2, 1, 1, 1, 1),
(1722, 'Newer value', 'Newest description', NULL, '2023-04-16 10:16:32', 1, 1, 1, -1, NULL),
(1746, '^', NULL, NULL, '2023-04-19 10:09:01', 1, 1, 1, -1, NULL),
(1747, 'sdg', NULL, NULL, '2023-04-19 10:11:13', 1, 1, 1, -1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `part_categories`
--

CREATE TABLE IF NOT EXISTS `part_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) NOT NULL,
  `parent_category` int(11) NOT NULL DEFAULT 1,
  `part_category_owner_u_fk` int(11) NOT NULL,
  PRIMARY KEY (`category_id`),
  KEY `part_category_owner_u_fk` (`part_category_owner_u_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `part_categories`
--

INSERT INTO `part_categories` (`category_id`, `category_name`, `parent_category`, `part_category_owner_u_fk`) VALUES
(1, 'Electronics', 0, -1),
(2, 'Passive Components', 1, -1),
(3, 'Resistors', 2, -1),
(4, 'Thermistors', 2, -1),
(5, 'Varistors', 2, -1),
(6, 'Inductors', 2, -1),
(7, 'Ferrite Beads', 2, -1),
(8, 'Capacitors', 2, -1),
(9, 'Ceramic Capacitors', 8, -1),
(10, 'Tantalum Capacitors', 8, -1),
(11, 'Aluminum Electrolytic Capacitors', 8, -1),
(12, 'Film Capacitors', 8, -1),
(13, 'Super Capacitors', 8, -1),
(14, 'Electromechanical Components', 1, -1),
(15, 'Switches', 14, -1),
(16, 'Tactile Switches', 15, -1),
(17, 'Push Button Switches', 15, -1),
(18, 'Rocker Switches', 15, -1),
(19, 'Toggle Switches', 15, -1),
(20, 'Slide Switches', 15, -1),
(21, 'DIP Switches', 15, -1),
(22, 'Micro Switches', 15, -1),
(23, 'Rotary Switches', 15, -1),
(24, 'Encoder Switches', 15, -1),
(25, 'Keypads', 15, -1),
(26, 'Relays', 14, -1),
(27, 'Electromagnetic Relays', 26, -1),
(28, 'Solid State Relays', 26, -1),
(29, 'Contactors', 26, -1),
(30, 'Connectors', 14, -1),
(31, 'PCB Mount Connectors', 30, -1),
(32, 'Circular Connectors', 30, -1),
(33, 'D-Sub Connectors', 30, -1),
(34, 'RF Connectors', 30, -1),
(35, 'Terminal Blocks', 30, -1),
(36, 'Headers', 30, -1),
(37, 'Cable Assemblies', 14, -1),
(38, 'Wiring Accessories', 14, -1),
(39, 'Cable Ties', 38, -1),
(40, 'Sleeving', 38, -1),
(41, 'Heat Shrink Tubing', 38, -1),
(42, 'Cable Glands', 38, -1),
(43, 'Adhesives', 14, -1),
(44, 'Tapes', 14, -1),
(45, 'Enclosures', 14, -1),
(46, 'Plastic Enclosures', 45, -1),
(47, 'Metal Enclosures', 45, -1),
(48, 'Box Enclosures', 45, -1),
(49, 'Panel Mount Enclosures', 45, -1),
(50, 'Heat Sinks', 14, -1),
(51, 'Aluminum Heat Sinks', 50, -1),
(52, 'Copper Heat Sinks', 50, -1),
(53, 'Fans', 14, -1),
(54, 'DC Fans', 53, -1),
(55, 'AC Fans', 53, -1),
(56, 'Blowers', 53, -1),
(57, 'Thermal Management Accessories', 14, -1),
(58, 'Thermal Interface Materials', 57, -1),
(59, 'Fans Accessories', 57, -1),
(60, 'Heaters', 14, -1),
(61, 'Cartridge Heaters', 60, -1),
(62, 'Band Heaters', 60, -1),
(63, 'Strip Heaters', 60, -1),
(64, 'Immersion Heaters', 60, -1),
(65, 'Thermocouples', 14, -1),
(66, 'Thermostats', 14, -1),
(67, 'Proximity Sensors', 14, -1),
(68, 'Level Sensors', 14, -1),
(69, 'Potentiometers', 14, -1),
(70, 'Rotary Potentiometers', 69, -1),
(71, 'Linear Potentiometers', 69, -1),
(72, 'Trimmers', 69, -1),
(73, 'Encoders', 14, -1),
(74, 'Optoelectronics', 1, -1),
(75, 'LEDs', 74, -1),
(76, 'LED Displays', 74, -1),
(77, 'LED Strips', 74, -1),
(78, 'Infrared Components', 74, -1),
(79, 'Laser Diodes', 74, -1),
(80, 'Photoelectric Sensors', 74, -1),
(81, 'Optocouplers', 74, -1),
(82, 'Optical Filters', 74, -1),
(83, 'Power Supplies', 1, -1),
(84, 'AC-DC Power Supplies', 83, -1),
(85, 'DC-DC Converters', 83, -1),
(86, 'Inverters', 83, -1),
(87, 'UPS Systems', 83, -1),
(88, 'Batteries', 1, -1),
(89, 'Alkaline Batteries', 88, -1),
(90, 'NiMH Batteries', 88, -1);

-- --------------------------------------------------------

--
-- Table structure for table `part_units`
--

CREATE TABLE IF NOT EXISTS `part_units` (
  `unit_id` int(11) NOT NULL AUTO_INCREMENT,
  `unit_name` varchar(255) NOT NULL,
  PRIMARY KEY (`unit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `part_units`
--

INSERT INTO `part_units` (`unit_id`, `unit_name`) VALUES
(1, 'PCS');

-- --------------------------------------------------------

--
-- Table structure for table `stock_levels`
--

CREATE TABLE IF NOT EXISTS `stock_levels` (
  `stock_level_id` int(11) NOT NULL AUTO_INCREMENT,
  `part_id_fk` int(11) NOT NULL,
  `location_id_fk` int(11) NOT NULL,
  `stock_level_quantity` int(11) NOT NULL,
  PRIMARY KEY (`part_id_fk`,`location_id_fk`),
  UNIQUE KEY `stock_level_id` (`stock_level_id`) USING BTREE,
  KEY `location_id` (`location_id_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=442 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_levels`
--

INSERT INTO `stock_levels` (`stock_level_id`, `part_id_fk`, `location_id_fk`, `stock_level_quantity`) VALUES
(196, 335, 1, 300),
(199, 335, 2, 100),
(211, 337, 1, 80),
(213, 337, 4, 100),
(224, 338, 2, 689),
(205, 339, 1, 180),
(206, 339, 2, 20),
(218, 343, 1, 8100),
(209, 347, 1, 250),
(208, 347, 3, 250),
(216, 348, 1, 100),
(215, 348, 2, 1700),
(225, 350, 1, 6844),
(219, 351, 1, 1467),
(222, 422, 1, 25),
(227, 532, 1, 69),
(220, 567, 1, 742),
(221, 629, 1, 4432),
(226, 1199, 4, 3698),
(223, 1491, 1, 1000),
(434, 1722, 1, 1),
(440, 1746, 1, 1),
(441, 1747, 2, 12);

-- --------------------------------------------------------

--
-- Table structure for table `stock_level_change_history`
--

CREATE TABLE IF NOT EXISTS `stock_level_change_history` (
  `stock_lvl_chng_id` int(11) NOT NULL AUTO_INCREMENT,
  `part_id_fk` int(11) NOT NULL,
  `from_location_fk` int(11) DEFAULT NULL,
  `to_location_fk` int(11) DEFAULT NULL,
  `stock_lvl_chng_quantity` int(11) NOT NULL,
  `stock_lvl_chng_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `stock_lvl_chng_comment` varchar(255) DEFAULT NULL,
  `stock_lvl_chng_user_fk` int(11) NOT NULL,
  PRIMARY KEY (`stock_lvl_chng_id`),
  KEY `from_location` (`from_location_fk`),
  KEY `to_location` (`to_location_fk`),
  KEY `stock_lvl_chng_user_fk` (`stock_lvl_chng_user_fk`),
  KEY `stock_level_change_history_ibfk_1` (`part_id_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=509 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_level_change_history`
--

INSERT INTO `stock_level_change_history` (`stock_lvl_chng_id`, `part_id_fk`, `from_location_fk`, `to_location_fk`, `stock_lvl_chng_quantity`, `stock_lvl_chng_timestamp`, `stock_lvl_chng_comment`, `stock_lvl_chng_user_fk`) VALUES
(281, 335, NULL, 1, 100, '2023-04-03 18:07:14', 'Initial stock entry', -1),
(282, 335, NULL, 1, 100, '2023-04-03 18:07:46', 'Initial stock entry', -1),
(283, 335, NULL, 1, 100, '2023-04-03 18:07:51', 'Initial stock entry', -1),
(284, 335, NULL, 2, 100, '2023-04-03 18:07:57', 'Initial stock entry', -1),
(289, 339, NULL, 1, 200, '2023-04-03 19:23:44', '', -1),
(290, 339, 1, 2, 20, '2023-04-03 19:23:52', '', -1),
(291, 347, NULL, 3, 500, '2023-04-03 22:17:54', 'For later', -1),
(292, 347, 3, 1, 250, '2023-04-03 22:18:42', '', -1),
(293, 337, NULL, 1, 200, '2023-04-04 08:30:09', '', -1),
(294, 337, 1, NULL, 20, '2023-04-04 08:30:17', '', -1),
(295, 337, 1, 4, 100, '2023-04-04 08:30:30', '', -1),
(296, 348, NULL, 2, 1800, '2023-04-04 08:33:26', '', -1),
(297, 348, 2, 1, 100, '2023-04-04 08:33:37', '', -1),
(298, 343, NULL, 1, 8100, '2023-04-04 11:35:07', '', -1),
(299, 351, NULL, 1, 1467, '2023-04-04 13:30:20', '', -1),
(300, 567, NULL, 1, 742, '2023-04-04 13:34:00', '', -1),
(301, 629, NULL, 1, 4432, '2023-04-04 13:35:42', '', -1),
(302, 422, NULL, 1, 25, '2023-04-04 13:36:47', '', -1),
(303, 1491, NULL, 1, 1000, '2023-04-04 13:57:50', 'Initial stock entry', 1),
(304, 338, NULL, 2, 689, '2023-04-04 14:01:09', '', -1),
(305, 350, NULL, 1, 6844, '2023-04-04 14:01:37', 'Initial stock entry', -1),
(306, 1199, NULL, 4, 3698, '2023-04-04 14:03:08', 'Leftover from PR2', -1),
(307, 532, NULL, 1, 69, '2023-04-04 14:24:50', '', -1),
(502, 1722, NULL, 1, 1, '2023-04-16 10:16:32', NULL, -1),
(507, 1746, NULL, 1, 1, '2023-04-19 10:09:01', NULL, -1),
(508, 1747, NULL, 2, 12, '2023-04-19 10:11:13', NULL, -1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_passwd` varchar(255) NOT NULL,
  `user_group_fk` int(11) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `user_email` varchar(255) NOT NULL,
  `register_date` timestamp NULL DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `user_group_fk` (`user_group_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_passwd`, `user_group_fk`, `user_name`, `user_email`, `register_date`, `last_login`) VALUES
(-1, '', 1, 'demo user', '', '2023-03-31 19:13:39', '0000-00-00 00:00:00'),
(1, '$argon2i$v=19$m=65536,t=4,p=1$amxnekpzQmFiT2oySlg5RQ$s7xb/YpNdh4GNvkVBCA7jHj8hDMrRPia7YKMZmGjEwE', 1, 'chrisi', 'christian@koma-elektronik.com', '2023-03-31 19:13:39', '0000-00-00 00:00:00'),
(27, '$argon2i$v=19$m=65536,t=4,p=1$MGRmTjA5bXFmU1lIWGJVZQ$mzRkAwRgOtqkyMhviFjzm9e0jtB6inJz3J31tLGHXHc', NULL, 'Great BOM', 'info@koma-elektronik.com', '2023-04-10 21:13:53', '2023-04-10 21:13:53');

-- --------------------------------------------------------

--
-- Table structure for table `user_groups`
--

CREATE TABLE IF NOT EXISTS `user_groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) NOT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_groups`
--

INSERT INTO `user_groups` (`group_id`, `group_name`) VALUES
(1, 'First Group');

-- --------------------------------------------------------

--
-- Table structure for table `user_settings`
--

CREATE TABLE IF NOT EXISTS `user_settings` (
  `user_settings_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id_fk` int(11) NOT NULL,
  `setting_name` varchar(255) NOT NULL,
  `setting_value` varchar(255) NOT NULL,
  PRIMARY KEY (`user_settings_id`),
  KEY `user_id_fk` (`user_id_fk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bom_elements`
--
ALTER TABLE `bom_elements`
  ADD CONSTRAINT `bom_elements_ibfk_1` FOREIGN KEY (`bom_id_fk`) REFERENCES `bom_names` (`bom_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bom_elements_ibfk_2` FOREIGN KEY (`part_id_fk`) REFERENCES `parts` (`part_id`) ON DELETE CASCADE;

--
-- Constraints for table `bom_names`
--
ALTER TABLE `bom_names`
  ADD CONSTRAINT `bom_names_ibfk_1` FOREIGN KEY (`bom_owner_u_fk`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `bom_runs`
--
ALTER TABLE `bom_runs`
  ADD CONSTRAINT `bom_runs_ibfk_1` FOREIGN KEY (`bom_id_fk`) REFERENCES `bom_names` (`bom_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bom_runs_ibfk_2` FOREIGN KEY (`bom_run_user_fk`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `location_names`
--
ALTER TABLE `location_names`
  ADD CONSTRAINT `location_names_ibfk_1` FOREIGN KEY (`location_owner_g_fk`) REFERENCES `user_groups` (`group_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `location_names_ibfk_2` FOREIGN KEY (`location_owner_u_fk`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `minstock_levels`
--
ALTER TABLE `minstock_levels`
  ADD CONSTRAINT `location_id` FOREIGN KEY (`location_id_fk`) REFERENCES `location_names` (`location_id`),
  ADD CONSTRAINT `minstock_levels_ibfk_1` FOREIGN KEY (`part_id_fk`) REFERENCES `parts` (`part_id`);

--
-- Constraints for table `parts`
--
ALTER TABLE `parts`
  ADD CONSTRAINT `parts_ibfk_1` FOREIGN KEY (`part_category_fk`) REFERENCES `part_categories` (`category_id`),
  ADD CONSTRAINT `parts_ibfk_2` FOREIGN KEY (`part_unit_fk`) REFERENCES `part_units` (`unit_id`),
  ADD CONSTRAINT `parts_ibfk_3` FOREIGN KEY (`part_footprint_fk`) REFERENCES `footprints` (`footprint_id`),
  ADD CONSTRAINT `parts_ibfk_4` FOREIGN KEY (`part_owner_u_fk`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `parts_ibfk_5` FOREIGN KEY (`part_owner_g_fk`) REFERENCES `user_groups` (`group_id`);

--
-- Constraints for table `part_categories`
--
ALTER TABLE `part_categories`
  ADD CONSTRAINT `part_categories_ibfk_1` FOREIGN KEY (`part_category_owner_u_fk`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_levels`
--
ALTER TABLE `stock_levels`
  ADD CONSTRAINT `part_id` FOREIGN KEY (`part_id_fk`) REFERENCES `parts` (`part_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_levels_ibfk_1` FOREIGN KEY (`location_id_fk`) REFERENCES `location_names` (`location_id`);

--
-- Constraints for table `stock_level_change_history`
--
ALTER TABLE `stock_level_change_history`
  ADD CONSTRAINT `stock_level_change_history_ibfk_1` FOREIGN KEY (`part_id_fk`) REFERENCES `parts` (`part_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_level_change_history_ibfk_2` FOREIGN KEY (`from_location_fk`) REFERENCES `location_names` (`location_id`),
  ADD CONSTRAINT `stock_level_change_history_ibfk_3` FOREIGN KEY (`to_location_fk`) REFERENCES `location_names` (`location_id`),
  ADD CONSTRAINT `stock_level_change_history_ibfk_4` FOREIGN KEY (`stock_lvl_chng_user_fk`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`user_group_fk`) REFERENCES `user_groups` (`group_id`);

--
-- Constraints for table `user_settings`
--
ALTER TABLE `user_settings`
  ADD CONSTRAINT `user_settings_ibfk_1` FOREIGN KEY (`user_id_fk`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
