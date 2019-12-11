########### 06-Sept ###########

ALTER TABLE `users` ADD `remember_token` VARCHAR(255) NOT NULL AFTER `password`;

########### 10-Sept - MT ###########


ALTER TABLE `ratings` CHANGE `comment` `comment` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '';
ALTER TABLE `users` CHANGE `image` `profile_picture` VARCHAR(250) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';


############# 07-Sept #############

ALTER TABLE `block_reasons` CHANGE `reason` `reason` VARCHAR(250) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;
ALTER TABLE `block_reasons` CHANGE `created_at` `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `vehicle_types` CHANGE `city_id` `city` VARCHAR(100) NOT NULL DEFAULT '';
ALTER TABLE `users` CHANGE `city_id` `city` VARCHAR(100) NOT NULL DEFAULT '';   
UPDATE `vehicle_types` set `city`='Jaipur' 
UPDATE `users` set `city`='Jaipur' 
ALTER TABLE `users` CHANGE `remember_token` `remember_token` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';
ALTER TABLE `users` CHANGE `forgot_key` `forgot_key` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';   
ALTER TABLE `users` CHANGE `cancellation_charge` `cancellation_charge` DECIMAL(10,2) NOT NULL DEFAULT '0.0';   
ALTER TABLE `users` CHANGE `identity_verification` `identity_verification` INT(11) NOT NULL DEFAULT '0', CHANGE `vehicle_verification` `vehicle_verification` INT(11) NOT NULL DEFAULT '0', CHANGE `document_verification` `document_verification` INT(11) NOT NULL DEFAULT '0', CHANGE `block_reason` `block_reason` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '';   
ALTER TABLE `users` DROP `block_reason`;

#################### 10-Sept - AP ##############################

INSERT INTO `cms` (`id`, `name`, `slug`, `content`, `status`, `created_at`, `updated_at`) VALUES (NULL, 'About Us', 'about-us', '', 'AC', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP), (NULL, 'FAQ', 'faq', '', 'AC', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP), (NULL, 'Terms & Conditions', 'terms-&-conditions', '', 'AC', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

############## 11-Sept ####################

ALTER TABLE `vehicle_types` CHANGE `per_minute` `per_minute` DECIMAL(10,2) NOT NULL DEFAULT '0.00';

############## 12-Sept #######################

ALTER TABLE `vehicle_types` CHANGE `price` `price` DECIMAL(10,2) NOT NULL, CHANGE `distance_time` `distance_time` DECIMAL(10,2) NOT NULL COMMENT 'sec/km';
ALTER TABLE `bus_rule_ref` CHANGE `rule_value` `rule_value` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `vehicle_types` CHANGE `status` `status` ENUM('AC','IN','DL')CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'AC';
ALTER TABLE `bookings` CHANGE `status` `status` ENUM('AC','IN','DL') CHARACTERSET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'AC';
ALTER TABLE `bus_rule_ref` CHANGE `sts_cd` `sts_cd` ENUM('AC','IN','DL')CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'AC';
ALTER TABLE `cities` CHANGE `status` `status` ENUM('AC','IN','DL') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'AC';
ALTER TABLE `cms` CHANGE `status` `status` ENUM('AC','IN','DL') CHARACTER SETlatin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'AC';
ALTER TABLE `countries` CHANGE `status` `status` ENUM('AC','IN','DL')CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'AC';
ALTER TABLE `document_types` CHANGE `status` `status` ENUM('AC','IN','DL')CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'AC';
ALTER TABLE `ratings` CHANGE `status` `status` ENUM('AC','IN','DL') CHARACTERSET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'AC';
ALTER TABLE `roles` CHANGE `status` `status` ENUM('AC','IN','DL') CHARACTERSET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'AC';
ALTER TABLE `states` CHANGE `status` `status` ENUM('AC','IN','DL') CHARACTERSET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'AC';
ALTER TABLE `users` CHANGE `status` `status` ENUM('AC','IN','DL') CHARACTERSET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'AC';
ALTER TABLE `user_documents` CHANGE `status` `status` ENUM('AC','IN','DL') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'AC';
ALTER TABLE `user_driver` CHANGE `status` `status` ENUM('AC','IN','DL')CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'AC';
ALTER TABLE `user_roles` CHANGE `status` `status` ENUM('AC','IN','DL')CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'AC';

############# 13-Sept ######################

ALTER TABLE `promo_codes` CHANGE `no_of_use` `no_of_applies` INT(11) NOT NULL COMMENT 'No of times user has used this coupon';
ALTER TABLE `bookings` ADD `promo_code_id` INT(11) NOT NULL AFTER `vehicle_type_id`;
ALTER TABLE `bookings` CHANGE `promo_code_id` `promo_code_id` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `users` ADD `wallet_amount` DECIMAL(10,2) NOT NULL AFTER `payment_method`;
ALTER TABLE `bookings` ADD `trusted_contacts` ENUM('0','1') NOT NULL DEFAULT '0' COMMENT 'flag to check booking sharing to contacts' AFTER `path_image`, ADD `sos` ENUM('0','1') NOT NULL DEFAULT '0' COMMENT 'flag to check booking sharing to admin' AFTER `trusted_contacts`;
ALTER TABLE `promo_codes` ADD `description` TEXT NOT NULL DEFAULT '' AFTER `promo_code`;
ALTER TABLE `users` ADD `referral_code` VARCHAR(50) NOT NULL AFTER `wallet_amount`, ADD `prnt_id` INT(11) NOT NULL COMMENT 'stores referrer id' AFTER `referral_code`;
ALTER TABLE `bookings` CHANGE `driver_id` `driver_id` INT(11) NULL; 
ALTER TABLE `bookings` CHANGE `driver_id` `driver_id` INT(11) NULL DEFAULT '0', CHANGE `distance` `distance` DECIMAL(10,2) NOT NULL DEFAULT '0.00', CHANGE `cost` `cost` DECIMAL(10,2) NOT NULL DEFAULT '0.00', CHANGE `after_promo` `after_promo` DECIMAL(10,2) NOT NULL DEFAULT '0.00', CHANGE `cancellation_charge` `cancellation_charge` DECIMAL(10,2) NOT NULL DEFAULT '0.00', CHANGE `total` `total` DECIMAL(10,2) NOT NULL DEFAULT '0.00', CHANGE `booking_status` `booking_status` INT(11) NOT NULL DEFAULT '8' COMMENT '0: declined, 1: accepted, 2:arrived, 3:start_ride, 4:cancelled, 5:end_ride, 6:schedule, 7:finished,8:pending';   
ALTER TABLE `bookings` CHANGE `scheduled_dateTime` `scheduled_dateTime` DATETIME NULL; 
ALTER TABLE `bookings` CHANGE `schedule` `schedule` INT(11) NOT NULL DEFAULT '0'; 

############### 14-Sept #########################

ALTER TABLE `bookings` ADD `cgst` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `cancellation_charge`, ADD `sgst` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `cgst`, ADD `igst` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `sgst`;


############## 14-Sept MT #######################

ALTER TABLE `otp` CHANGE `opt` `otp` INT(10) NOT NULL;
ALTER TABLE `otp` CHANGE `user_id` `phone_no` VARCHAR(15) NOT NULL DEFAULT ''; 
ALTER TABLE `otp` ADD `id` INT(11) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`); 

################ 17-Sept #######################

ALTER TABLE `promo_codes` ADD `min_amount` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `city`;
ALTER TABLE `promo_codes` ADD `state` VARCHAR(100) NOT NULL DEFAULT '' AFTER `description`, ADD `city` VARCHAR(100) NOT NULL DEFAULT '' AFTER `state`;
ALTER TABLE `users` ADD `state` VARCHAR(255) NOT NULL DEFAULT '' AFTER `profile_picture`;   
ALTER TABLE `vehicle_types` ADD `state` VARCHAR(255) NOT NULL DEFAULT '' AFTER `id`; 

################## 18-Sept-2018#################

ALTER TABLE `bookings` CHANGE `after_promo` `promo_deduct` DECIMAL(10,2) NOT NULL DEFAULT '0.00';
ALTER TABLE `bookings` ADD `cancelled_by` INT(11) NULL DEFAULT NULL AFTER `cancellation_charge`;

############# 19-Sept-2018 ###################

INSERT INTO `bus_rule_ref` (`id`, `rule_name`, `rule_value`, `comment`, `sts_cd`, `created_at`, `updated_at`) VALUES (NULL, 'trusted_contacts_limit', '2', NULL, 'AC', '0000-00-00 00:00:00', '2018-09-14 11:04:58');  

############ 20-Sept-2018 #################

RENAME TABLE `vltaxi`.`promo_codes` TO `vltaxi`.`coupon_codes`;
ALTER TABLE `coupon_codes` CHANGE `promo_code` `coupon_code` VARCHAR(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;
RENAME TABLE `vltaxi`.`user_promocode` TO `vltaxi`.`user_couponcode`;
ALTER TABLE `user_couponcode` CHANGE `promo_code_id` `coupon_code_id` INT(11) NOT NULL;
RENAME TABLE `vltaxi`.`user_couponcode` TO `vltaxi`.`user_coupon_code`;
ALTER TABLE `coupon_codes` CHANGE `no_of_applies` `no_of_applies` INT(11) NOT NULL DEFAULT '1' COMMENT 'No of times user will use this coupon';
ALTER TABLE `coupon_codes` ADD `min_rides` INT(11) NOT NULL DEFAULT '1' COMMENT 'if user has completed this no. or rides then he can use this coupon' AFTER `no_of_rides`;

############ 25-Sept-2018 ####################

ALTER TABLE `coupon_codes` CHANGE `min_rides` `min_rides` INT(11) NULL DEFAULT '1' COMMENT 'if user has completed this no. of rides then he can use this coupon';
CREATE TABLE `faq_questions` (
  `id` int(11) NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `status` enum('AC','IN','DL') NOT NULL DEFAULT 'AC',
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `faq_questions`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `faq_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;COMMIT;
