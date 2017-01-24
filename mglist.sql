-- phpMyAdmin SQL Dump
-- version 4.1.6
-- http://www.phpmyadmin.net
--
-- 主機: 127.0.0.1
-- 產生時間： 2017 �?01 ??24 ??13:45
-- 伺服器版本: 5.6.16
-- PHP 版本： 5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 資料庫： `mglist`
--

-- --------------------------------------------------------

--
-- 資料表結構 `game`
--

CREATE TABLE IF NOT EXISTS `game` (
  `g_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  `platform` varchar(10) DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `img_url` varchar(50) DEFAULT NULL,
  `content` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`g_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=80 ;

--
-- 資料表的匯出資料 `game`
--

INSERT INTO `game` (`g_id`, `title`, `type`, `platform`, `release_date`, `img_url`, `content`) VALUES
(1, '伊蘇 8 -丹娜的隕涕日-', '角色扮演', 'PSV', '2016-08-11', 'cover/01.PNG', '本作故事將以一座位於格利克南方的蓋提海上的浮島「青蓮島（セイレン島）」為舞台'),
(2, 'aa', NULL, 'bb', NULL, 'cover/', NULL),
(3, '魔兵驚天錄 2', '動作', 'Wii U', '2014-09-20', 'cover/3.JPG', NULL),
(4, 'sdfa', NULL, 'ff', NULL, 'cover/', NULL),
(5, 'sdfa', NULL, 'ff', NULL, 'cover/', NULL),
(6, '任天堂明星大亂鬥 Wii U', '動作', 'Wii U', '2014-12-06', 'cover/6.PNG', NULL),
(7, '漆彈大作戰', '射擊', 'Wii U', '2015-05-28', 'cover/7.JPG', NULL),
(8, 'opo', NULL, '', NULL, 'cover/', NULL),
(9, 'ooo', NULL, '', NULL, 'cover/', NULL),
(10, '核心機群', '動作', 'Xbox One', '2016-09-13', 'cover/10.JPG', NULL),
(11, 'iii', NULL, '', NULL, 'cover/', NULL),
(12, 'sdfsf', NULL, '', NULL, 'cover/', NULL),
(13, 'oioioio', NULL, '', NULL, 'cover/', NULL),
(14, 'jhk', NULL, '', NULL, 'cover/', NULL),
(15, 'jhk', NULL, '', NULL, 'cover/', NULL),
(16, 'jhk', NULL, '', NULL, 'cover/', NULL),
(17, 'bvnv', NULL, '', NULL, 'cover/', NULL),
(18, 'bvnv', NULL, '', NULL, 'cover/', NULL),
(19, '精靈寶可夢 太陽', '角色扮演', '3DS', '2016-11-18', 'cover/19.PNG', NULL),
(20, 'bvnv', NULL, '', NULL, NULL, NULL),
(21, 'www', '冒險', '', NULL, NULL, NULL),
(22, 'aa', 'å‹•ä½œ', 'ff', NULL, NULL, NULL),
(23, 'aa', 'å‹•ä½œ', 'ff', NULL, NULL, NULL),
(24, 'dsfs', 'å‹•ä½œ', '', NULL, NULL, NULL),
(25, 'sdfa', 'å†’éšª', 'PC online', NULL, NULL, NULL),
(26, '極限競速：地平線 3', '競速', 'Xbox One', '2016-09-27', 'cover/26.PNG', NULL),
(76, 'SD 鋼彈 G 世代 創世', '策略模擬', 'PS4', '2016-11-12', 'cover/76.JPG', NULL),
(77, '女神異聞錄 5', '角色扮演', 'PS4', '2017-09-15', 'cover/77.PNG', NULL),
(78, '戰爭機器 4', '射擊', 'Xbox One', '2017-10-11', 'cover/78.JPG', NULL),
(79, '食人巨鷹 TRICO', '冒險', 'PS4', '2016-12-06', 'cover/79.JPG', '《食人巨鷹 TRICO》是一手打造《迷霧古城》、《汪達與巨像》等獨特風格遊戲而廣受全球好評的遊戲創作者上田文人領軍製作的最新作品');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
