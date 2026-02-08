/*M!999999\- enable the sandbox mode */ 
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `alternative_group_elements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `alternative_group_elements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `alternative_group_id` bigint(20) unsigned NOT NULL,
  `alternative_part_id` int(11) NOT NULL,
  `owner_u_fk` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alternative_group_elements_alternative_group_id_part_id_unique` (`alternative_group_id`,`alternative_part_id`),
  KEY `alternative_group_elements_part_id_index` (`alternative_part_id`),
  KEY `alternative_group_elements_owner_u_fk_foreign` (`owner_u_fk`),
  CONSTRAINT `alternative_group_elements_alternative_group_id_foreign` FOREIGN KEY (`alternative_group_id`) REFERENCES `alternative_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `alternative_group_elements_owner_u_fk_foreign` FOREIGN KEY (`owner_u_fk`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `alternative_group_elements_part_id_foreign` FOREIGN KEY (`alternative_part_id`) REFERENCES `parts` (`part_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `alternative_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `alternative_groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `owner_u_fk` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `alternative_groups_owner_u_fk_foreign` (`owner_u_fk`),
  CONSTRAINT `alternative_groups_owner_u_fk_foreign` FOREIGN KEY (`owner_u_fk`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bom_elements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bom_elements` (
  `bom_elements_id` int(11) NOT NULL AUTO_INCREMENT,
  `bom_id_fk` int(11) NOT NULL,
  `part_id_fk` int(11) NOT NULL,
  `element_quantity` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `bom_element_owner_u_fk` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`bom_elements_id`) USING BTREE,
  KEY `bom_elements_ibfk_2` (`part_id_fk`),
  KEY `bom_elements_ibfk_1` (`bom_id_fk`),
  KEY `bom_elements_bom_element_owner_u_fk_foreign` (`bom_element_owner_u_fk`),
  CONSTRAINT `FK_BOM_Elements_BOM_ID_To_BOMs_BOM_ID` FOREIGN KEY (`bom_id_fk`) REFERENCES `boms` (`bom_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_BOM_Elements_Part_ID_To_Parts_Part_ID` FOREIGN KEY (`part_id_fk`) REFERENCES `parts` (`part_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `bom_elements_bom_element_owner_u_fk_foreign` FOREIGN KEY (`bom_element_owner_u_fk`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bom_revisions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bom_revisions` (
  `bom_revision_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `bom_id_fk` bigint(20) unsigned NOT NULL,
  `label` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_by_u_fk` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`bom_revision_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `bom_runs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bom_runs` (
  `bom_run_id` int(11) NOT NULL AUTO_INCREMENT,
  `bom_id_fk` int(11) NOT NULL,
  `bom_revision_id_fk` bigint(20) unsigned DEFAULT NULL,
  `bom_run_quantity` int(11) NOT NULL,
  `bom_run_datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `bom_run_user_fk` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`bom_run_id`),
  KEY `bom_runs_ibfk_1` (`bom_id_fk`),
  KEY `bom_run_user_fk` (`bom_run_user_fk`),
  CONSTRAINT `FK_BOM_Runs_BOM_ID_FK_To_BOMs_BOM_ID` FOREIGN KEY (`bom_id_fk`) REFERENCES `boms` (`bom_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_BOM_Runs_BOM_Run_User_FK_To_Users_ID` FOREIGN KEY (`bom_run_user_fk`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `boms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `boms` (
  `bom_id` int(11) NOT NULL AUTO_INCREMENT,
  `bom_name` varchar(255) NOT NULL,
  `bom_description` varchar(255) DEFAULT NULL,
  `bom_owner_g_fk` int(11) DEFAULT NULL,
  `bom_owner_u_fk` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`bom_id`),
  KEY `bom_owner_u_fk` (`bom_owner_u_fk`),
  CONSTRAINT `FK_BOMs_BOM_Owner_U_FK_To_Users_ID` FOREIGN KEY (`bom_owner_u_fk`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `delete_me`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `delete_me` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `associated_id` bigint(20) unsigned NOT NULL,
  `document_owner_u_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `display_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `email_changes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_changes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `new_email` varchar(255) NOT NULL,
  `verification_token` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email_changes_user_id_foreign` (`user_id`),
  CONSTRAINT `email_changes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `footprints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `footprints` (
  `footprint_id` int(11) NOT NULL AUTO_INCREMENT,
  `footprint_name` varchar(255) NOT NULL,
  `footprint_alias` varchar(255) DEFAULT NULL,
  `footprint_owner_u_fk` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`footprint_id`),
  KEY `FK_Footprints_Footprint_Owner_U_FK_To_Users_ID` (`footprint_owner_u_fk`),
  CONSTRAINT `FK_Footprints_Footprint_Owner_U_FK_To_Users_ID` FOREIGN KEY (`footprint_owner_u_fk`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `images` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `image_owner_u_id` bigint(20) unsigned NOT NULL,
  `type` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `associated_id` bigint(20) unsigned NOT NULL,
  `display_name` varchar(255) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `FK_Images_Image_Owner_U_ID_To_Users_ID` (`image_owner_u_id`),
  CONSTRAINT `FK_Images_Image_Owner_U_ID_To_Users_ID` FOREIGN KEY (`image_owner_u_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `locations` (
  `location_id` int(11) NOT NULL AUTO_INCREMENT,
  `location_name` varchar(255) NOT NULL,
  `location_description` varchar(255) DEFAULT NULL,
  `location_owner_u_fk` bigint(20) unsigned NOT NULL,
  `location_owner_g_fk` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`location_id`),
  KEY `location_owner_g_fk` (`location_owner_g_fk`),
  KEY `location_owner_u_fk` (`location_owner_u_fk`),
  CONSTRAINT `FK_Locations_Location_Owner_G_FK_To_User_Groups_Group_ID` FOREIGN KEY (`location_owner_g_fk`) REFERENCES `user_groups` (`group_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_Locations_Location_Owner_U_FK_To_Users_ID` FOREIGN KEY (`location_owner_u_fk`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `minstock_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `minstock_levels` (
  `minstock_lvl_id` int(11) NOT NULL AUTO_INCREMENT,
  `part_id_fk` int(11) NOT NULL,
  `location_id_fk` int(11) NOT NULL,
  `minstock_level` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`minstock_lvl_id`),
  KEY `location_id` (`location_id_fk`),
  KEY `part_id` (`part_id_fk`),
  CONSTRAINT `FK_Minstock_Levels_Location_ID_FK_To_Locations_Location_ID` FOREIGN KEY (`location_id_fk`) REFERENCES `locations` (`location_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_Minstock_Levels_Part_ID_FK_To_Parts_Part_ID` FOREIGN KEY (`part_id_fk`) REFERENCES `parts` (`part_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `part_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `part_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) NOT NULL,
  `parent_category` int(11) NOT NULL DEFAULT 1,
  `part_category_owner_u_fk` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`category_id`),
  KEY `part_category_owner_u_fk` (`part_category_owner_u_fk`),
  CONSTRAINT `FK_Part_Categories_Part_Category_Owner_U_FK_To_Users_ID` FOREIGN KEY (`part_category_owner_u_fk`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `part_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `part_meta` (
  `meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `meta_key` varchar(255) NOT NULL,
  `meta_description` varchar(255) NOT NULL,
  `meta_value` varchar(255) NOT NULL,
  `part_id_fk` int(11) NOT NULL,
  `meta_owner_u_fk` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `FK_Part_Meta_Part_ID_FK_To_Parts_Part_ID` (`part_id_fk`),
  KEY `FK_Part_Meta_Meta_Owner_U_FK_To_Users_ID` (`meta_owner_u_fk`),
  CONSTRAINT `FK_Part_Meta_Meta_Owner_U_FK_To_Users_ID` FOREIGN KEY (`meta_owner_u_fk`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_Part_Meta_Part_ID_FK_To_Parts_Part_ID` FOREIGN KEY (`part_id_fk`) REFERENCES `parts` (`part_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `part_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `part_units` (
  `unit_id` int(11) NOT NULL AUTO_INCREMENT,
  `unit_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `parts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `parts` (
  `part_id` int(11) NOT NULL AUTO_INCREMENT,
  `part_name` varchar(255) NOT NULL,
  `part_description` varchar(255) DEFAULT NULL,
  `part_comment` varchar(255) DEFAULT NULL,
  `part_category_fk` int(11) DEFAULT 1,
  `part_footprint_fk` int(11) DEFAULT 1,
  `part_unit_fk` int(11) DEFAULT 1,
  `part_owner_u_fk` bigint(20) unsigned NOT NULL,
  `part_owner_g_fk` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `part_supplier_fk` int(11) DEFAULT NULL,
  `stocklevel_notification_threshold` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`part_id`),
  KEY `part_category_fk` (`part_category_fk`),
  KEY `part_unit_fk` (`part_unit_fk`),
  KEY `part_footprint_fk` (`part_footprint_fk`),
  KEY `part_owner_u_fk` (`part_owner_u_fk`),
  KEY `part_owner_g_fk` (`part_owner_g_fk`),
  KEY `part_name` (`part_name`) USING BTREE,
  KEY `FK_Parts_Part_Supplier_FK_To_Suppliers_Supplier_ID` (`part_supplier_fk`),
  CONSTRAINT `FK_Parts_Part_Category_FK_To_Part_Categories_Category_ID` FOREIGN KEY (`part_category_fk`) REFERENCES `part_categories` (`category_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_Parts_Part_Footprint_FK_To_Footprints_Footprint_ID` FOREIGN KEY (`part_footprint_fk`) REFERENCES `footprints` (`footprint_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_Parts_Part_Owner_G_FK_To_User_Groups_Group_ID` FOREIGN KEY (`part_owner_g_fk`) REFERENCES `user_groups` (`group_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_Parts_Part_Owner_U_FK_To_Users_ID` FOREIGN KEY (`part_owner_u_fk`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_Parts_Part_Supplier_FK_To_Suppliers_Supplier_ID` FOREIGN KEY (`part_supplier_fk`) REFERENCES `suppliers` (`supplier_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_Parts_Part_Unit_FK_To_Part_Units_Unit_ID` FOREIGN KEY (`part_unit_fk`) REFERENCES `part_units` (`unit_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `shortage_parts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `shortage_parts` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `stock_level_change_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_level_change_history` (
  `stock_lvl_chng_id` int(11) NOT NULL AUTO_INCREMENT,
  `part_id_fk` int(11) NOT NULL,
  `from_location_fk` int(11) DEFAULT NULL,
  `to_location_fk` int(11) DEFAULT NULL,
  `stock_lvl_chng_quantity` int(11) NOT NULL,
  `stock_lvl_chng_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `stock_lvl_chng_comment` varchar(255) DEFAULT NULL,
  `stock_lvl_chng_user_fk` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`stock_lvl_chng_id`),
  KEY `from_location` (`from_location_fk`),
  KEY `to_location` (`to_location_fk`),
  KEY `stock_lvl_chng_user_fk` (`stock_lvl_chng_user_fk`),
  KEY `stock_level_change_history_ibfk_1` (`part_id_fk`),
  CONSTRAINT `FK_StckLvlChng_Hst_From_Location_FK_To_Locations_Location_ID` FOREIGN KEY (`from_location_fk`) REFERENCES `locations` (`location_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_StckLvlChng_Hst_Part_ID_FK_To_Parts_Part_ID` FOREIGN KEY (`part_id_fk`) REFERENCES `parts` (`part_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_StckLvlChng_Hst_Stock_Lvl_Chng_User_FK_To_Users_ID` FOREIGN KEY (`stock_lvl_chng_user_fk`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_StckLvlChng_Hst_To_Location_FK_To_Locations_Location_ID` FOREIGN KEY (`to_location_fk`) REFERENCES `locations` (`location_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `stock_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_levels` (
  `stock_level_id` int(11) NOT NULL AUTO_INCREMENT,
  `part_id_fk` int(11) NOT NULL,
  `location_id_fk` int(11) NOT NULL,
  `stock_level_quantity` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`part_id_fk`,`location_id_fk`),
  UNIQUE KEY `stock_level_id` (`stock_level_id`) USING BTREE,
  KEY `location_id` (`location_id_fk`),
  CONSTRAINT `FK_Stock_Levels_Location_ID_FK_To_Locations_Location_ID` FOREIGN KEY (`location_id_fk`) REFERENCES `locations` (`location_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_Stock_Levels_Part_ID_FK_To_Parts_Part_ID` FOREIGN KEY (`part_id_fk`) REFERENCES `parts` (`part_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `subscription_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscription_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subscription_id` bigint(20) unsigned NOT NULL,
  `stripe_id` varchar(255) NOT NULL,
  `stripe_product` varchar(255) NOT NULL,
  `stripe_price` varchar(255) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscription_items_subscription_id_stripe_price_unique` (`subscription_id`,`stripe_price`),
  UNIQUE KEY `subscription_items_stripe_id_unique` (`stripe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscriptions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `stripe_id` varchar(255) NOT NULL,
  `stripe_status` varchar(255) NOT NULL,
  `stripe_price` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscriptions_stripe_id_unique` (`stripe_id`),
  KEY `subscriptions_user_id_stripe_status_index` (`user_id`,`stripe_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `supplier_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_data` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `part_id_fk` int(11) NOT NULL,
  `supplier_id_fk` int(11) NOT NULL,
  `URL` varchar(255) DEFAULT NULL,
  `SPN` varchar(255) DEFAULT NULL,
  `price` decimal(20,6) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `supplier_data_owner_u_fk` bigint(20) unsigned DEFAULT NULL,
  `supplier_data_owner_g_fk` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_data_supplier_data_owner_u_fk_foreign` (`supplier_data_owner_u_fk`),
  KEY `supplier_data_part_id_fk_foreign` (`part_id_fk`),
  KEY `supplier_data_supplier_id_fk_foreign` (`supplier_id_fk`),
  CONSTRAINT `supplier_data_part_id_fk_foreign` FOREIGN KEY (`part_id_fk`) REFERENCES `parts` (`part_id`) ON DELETE CASCADE,
  CONSTRAINT `supplier_data_supplier_data_owner_u_fk_foreign` FOREIGN KEY (`supplier_data_owner_u_fk`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `supplier_data_supplier_id_fk_foreign` FOREIGN KEY (`supplier_id_fk`) REFERENCES `suppliers` (`supplier_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `supplier_id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_name` varchar(255) NOT NULL,
  `supplier_owner_g_fk` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `supplier_owner_u_fk` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`supplier_id`),
  KEY `FK_Suppliers_Supplier_Owner_U_FK_To_Users_ID` (`supplier_owner_u_fk`),
  CONSTRAINT `FK_Suppliers_Supplier_Owner_U_FK_To_Users_ID` FOREIGN KEY (`supplier_owner_u_fk`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_groups` (
  `group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_settings` (
  `user_settings_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id_fk` bigint(20) unsigned NOT NULL,
  `setting_name` varchar(255) NOT NULL,
  `setting_value` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_settings_id`),
  KEY `user_id_fk` (`user_id_fk`),
  CONSTRAINT `FK_User_Settings_User_ID_FK_To_Users_ID` FOREIGN KEY (`user_id_fk`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `stripe_id` varchar(255) DEFAULT NULL,
  `pm_type` varchar(255) DEFAULT NULL,
  `pm_last_four` varchar(4) DEFAULT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `selected_plan` varchar(255) DEFAULT NULL,
  `price_id` varchar(255) DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_stripe_id_index` (`stripe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

/*M!999999\- enable the sandbox mode */ 
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2014_10_12_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2014_10_12_100000_create_password_reset_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2019_08_19_000000_create_failed_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2019_12_14_000001_create_personal_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2024_02_06_215029_create_bom_elements_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2024_02_06_215029_create_bom_runs_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2024_02_06_215029_create_boms_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2024_02_06_215029_create_failed_jobs_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2024_02_06_215029_create_footprints_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2024_02_06_215029_create_locations_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2024_02_06_215029_create_minstock_levels_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2024_02_06_215029_create_part_categories_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2024_02_06_215029_create_part_units_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2024_02_06_215029_create_parts_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2024_02_06_215029_create_password_reset_tokens_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2024_02_06_215029_create_personal_access_tokens_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2024_02_06_215029_create_stock_level_change_history_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2024_02_06_215029_create_stock_levels_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2024_02_06_215029_create_user_groups_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2024_02_06_215029_create_user_settings_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2024_02_06_215029_create_users_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2024_02_06_220244_create_PartHubDemo_table',0);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2024_02_10_111050_add_auto_increment_to_stock_levels',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2024_02_19_203502_modify_footprint_alias_nullable',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2024_02_21_165619_add_timestamps_to_all_tables',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2024_02_22_155000_add_timestamps_to_all_tables copy',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2024_02_27_185856_create_suppliers_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2024_02_27_211426_add_part_supplier_fk_to_parts_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2024_03_17_192911_create_part_meta_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2024_04_06_104734_create_images_table',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2024_04_06_131758_add_associated_id_to_images_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2024_04_23_145629_remove_foreign_key_constraints_on_initial_tables',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2024_04_23_153924_remove_remaining_foreign_key_constraints',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2024_04_23_155428_create_foreign_key_constraints_without_triggers',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (77,'2024_04_23_192744_create_missing_user_key_constraints',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (78,'2024_06_02_123419_add_notification_threshold_to_parts_table',14);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (82,'2024_07_31_123420_create_customer_columns',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (83,'2024_07_31_123421_create_subscriptions_table',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (84,'2024_07_31_123422_create_subscription_items_table',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (86,'2024_08_03_140546_create_supplier_data_table',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (87,'2024_08_11_165516_create_email_changes_table',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (88,'2024_09_01_183918_create_documents_table',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (89,'2024_09_03_192123_add_display_name_to_images_and_documents_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (90,'2024_09_04_183950_add_order_column_to_images_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (91,'2024_09_08_111256_make_spn_and_price_nullable_in_supplier_data',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (92,'2024_09_17_142048_add_owner_columns_to_supplier_data',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (93,'2024_10_06_220258_add_intended_subscription_details_to_user_table',23);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (94,'2024_10_07_183641_update_supplier_data_foreign_keys_for_cascade_delete',24);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (95,'2024_10_12_223419_add_last_login_at_to_users_table',25);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (96,'2024_11_12_004454_add_bom_element_owner_fk_to_bom_elements_table',26);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (99,'2024_11_12_005722_fill_bom_elements_owner_column_from_bom_table',27);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (100,'2025_01_23_221815_create_alternative_parts_table',28);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (101,'2025_02_02_173029_add_owner_column_to_alternative_parts_table',29);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (102,'2025_02_07_202105_create_alternative_groups_table_and_drop_alternative_parts_table',30);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (103,'2025_03_07_200655_add_owner_column_to_alternative_group_elements_table',30);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (104,'2025_03_07_202411_add_timestamp_columns_to_alternative_group_elements_table',30);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (105,'2025_03_07_203532_rename_part_id_column_to_alternative_part_id_in_alternative_group_elements',30);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (106,'2026_02_07_150440_alter_user_settings_setting_value_to_text',31);
