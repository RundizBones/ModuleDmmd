/**
 * Installer SQL.
 * 
 * Please follow these instruction strictly.
 * The table name in this file must wrap with %$...% and have no prefix. Example: `%$users%` will be converted to `prefix_users`.
 * No `ENGINE=xxx` in the SQL.
 * No `COLLATE xxx` in each table or column (except it is special such as `utf8_bin` for work with case sensitive).
 * Use only `CHARSET=utf8` in the `CREATE TABLE`, nothing else, no `utf8mb4` or anything. Just `utf8`.
 *
 * DO NOT just paste the SQL data that exported from MySQL. Please modify by read the instruction above first.
 */


-- Begins the SQL string below this line. ------------------------------------------------------------------


SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


-- @TODO[dmmd]: write your own create table SQL if you want to use DB.
CREATE TABLE IF NOT EXISTS `%$demo_management_dialog%` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(191) DEFAULT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='demonstration how management page (CRUD) work with dialog and AJAX.' AUTO_INCREMENT=2 ;