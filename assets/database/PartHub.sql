-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 11, 2023 at 09:20 AM
-- Server version: 10.3.38-MariaDB-0ubuntu0.20.04.1
-- PHP Version: 8.2.4

SET FOREIGN_KEY_CHECKS=0;
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

--
-- Dumping data for table `bom_names`
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

--
-- Dumping data for table `footprints`
--

INSERT INTO `footprints` (`footprint_id`, `footprint_name`, `footprint_alias`) VALUES
(1, 'A Footprint', 'Its alias'),
(2, 'TO-92', '');

--
-- Dumping data for table `location_names`
--

INSERT INTO `location_names` (`location_id`, `location_name`, `location_description`) VALUES
(1, 'Main Storage', 'Like, all of it'),
(2, 'EMS Partner', ''),
(3, 'Hardware Supplier', ''),
(4, 'External Storage', NULL),
(5, 'Increase', 'General Stock Increase'),
(6, 'Decrease', 'General Stock Decrease');

--
-- Dumping data for table `parts`
--

INSERT INTO `parts` (`part_id`, `part_name`, `part_description`, `part_comment`, `created_at`, `part_category_fk`, `part_footprint_fk`, `part_unit_fk`, `part_owner_u_fk`, `part_owner_g_fk`) VALUES
(335, 'BC847B', 'Transistor NPN 45V 100mA SOT-23', 'Your general NPN transistor workhorse', '2023-03-07 00:24:12', 2, 2, 1, -1, 1),
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
(532, 'Capacitor 100pF, 5%, 0603', 'Surface mount capacitor with 100 picofarad capacitance and 5% tolerancedc', 'Useful for high-frequency filtering and decoupling applications', '2022-03-06 09:15:39', 1, 1, 1, -1, 1),
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
(1491, 'BC847B', 'Transistor NPN 45V 100mA SOT-23', 'Your general NPN transistor workhorse', '2023-03-07 00:24:12', 2, 1, 1, 1, 1);

--
-- Dumping data for table `part_categories`
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

--
-- Dumping data for table `part_units`
--

INSERT INTO `part_units` (`unit_id`, `unit_name`) VALUES
(1, 'PCS');

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
(223, 1491, 1, 1000);

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
(307, 532, NULL, 1, 69, '2023-04-04 14:24:50', '', -1);

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_passwd`, `user_group_fk`, `user_name`, `user_email`, `register_date`, `last_login`) VALUES
(-1, '', 1, 'demo user', '', '2023-03-31 19:13:39', '0000-00-00 00:00:00'),
(1, '$argon2i$v=19$m=65536,t=4,p=1$amxnekpzQmFiT2oySlg5RQ$s7xb/YpNdh4GNvkVBCA7jHj8hDMrRPia7YKMZmGjEwE', 1, 'chrisi', 'christian@koma-elektronik.com', '2023-03-31 19:13:39', '0000-00-00 00:00:00'),
(27, '$argon2i$v=19$m=65536,t=4,p=1$MGRmTjA5bXFmU1lIWGJVZQ$mzRkAwRgOtqkyMhviFjzm9e0jtB6inJz3J31tLGHXHc', NULL, 'Great BOM', 'info@koma-elektronik.com', '2023-04-10 21:13:53', '2023-04-10 21:13:53');

--
-- Dumping data for table `user_groups`
--

INSERT INTO `user_groups` (`group_id`, `group_name`) VALUES
(1, 'First Group');
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
