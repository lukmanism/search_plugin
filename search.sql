--
-- Table structure for table `search_tags`
--

CREATE TABLE IF NOT EXISTS `search_tags` (
  `id` int(11) NOT NULL,
  `keyword` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `search_tags_rltn`
--

CREATE TABLE IF NOT EXISTS `search_tags_rltn` (
  `id` int(11) NOT NULL,
  `tagId` int(11) NOT NULL,
  `tabId` int(11) NOT NULL,
  `table` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `search_tags_smlr`
--

CREATE TABLE IF NOT EXISTS `search_tags_smlr` (
  `id` int(11) NOT NULL,
  `tagId` int(11) NOT NULL,
  `tagsId` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
