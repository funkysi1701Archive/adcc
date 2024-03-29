ALTER TABLE  `content` ADD  `in_use` INT NOT NULL;
ALTER TABLE  `entries` ADD  `imageid` INT NOT NULL;
delete from config where name='localmap1';
delete from config where name='localmap2';
delete from config where name='bg';
delete from config where name='logo';

CREATE TABLE  `permission` (
`userid` VARCHAR( 255 ) NOT NULL ,
`location` VARCHAR( 255 ) NOT NULL ,
`level` INT NOT NULL
) ENGINE = MYISAM;

CREATE TABLE  `permission_pages` (
`page` VARCHAR( 255 ) NOT NULL,
`name` VARCHAR( 255 ) NOT NULL
) ENGINE = MYISAM;

INSERT INTO `adcc`.`config` (`id`, `name`, `setting`) VALUES (NULL, 'bg', '../imgs/site/bg.png');
INSERT INTO `adcc`.`config` (`id`, `name`, `setting`) VALUES (NULL, 'logo', '../imgs/site/adccnew_ie6.png');

update image_store set name='Tony Mann' where author_id='tony';
update image_store set author_id='tonym' where author_id='tony';

INSERT INTO `permission` (`userid`, `location`, `level`) VALUES
('gregf', 'permission', 1),
('tonym', 'permission', 1),
('simon', 'permission', 1),
('alang', 'gall', 1),
('barryw', 'gall', 1),
('beresforda', 'gall', 1),
('bobr', 'gall', 1),
('brians', 'gall', 1),
('briant', 'gall', 1),
('carnival03', 'gall', 1),
('carnival06', 'gall', 1),
('carnival08', 'gall', 1),
('combsd', 'gall', 1),
('craigl', 'gall', 1),
('daveh', 'gall', 1),
('doreenc', 'gall', 1),
('eddiec', 'gall', 1),
('franke', 'gall', 1),
('garyl', 'gall', 1),
('geoffb', 'gall', 1),
('greg', 'gall', 1),
('gregf', 'gall', 1),
('hallm', 'gall', 1),
('irenep', 'gall', 1),
('ivans', 'gall', 1),
('johnb', 'gall', 1),
('johnw', 'gall', 1),
('keithh', 'gall', 1),
('kenw', 'gall', 1),
('kurtg', 'gall', 1),
('louisew', 'gall', 1),
('mackaya', 'gall', 1),
('margaretj', 'gall', 1),
('mikep', 'gall', 1),
('pauln', 'gall', 1),
('philfull', 'gall', 1),
('philh', 'gall', 1),
('philhow', 'gall', 1),
('progsec', 'gall', 1),
('rayp', 'gall', 1),
('reedm', 'gall', 1),
('rexs', 'gall', 1),
('richardw', 'gall', 1),
('ritam', 'gall', 1),
('robertw', 'gall', 1),
('rufford1940', 'gall', 1),
('sarahd', 'gall', 1),
('shelias', 'gall', 1),
('simon', 'gall', 1),
('steveg', 'gall', 1),
('sued', 'gall', 1),
('suefull', 'gall', 1),
('tony', 'gall', 1),
('tonym', 'gall', 1),
('wainm', 'gall', 1),
('simon', 'home', 1),
('simon', 'purpose', 1),
('simon', 'news', 1),
('simon', 'schedule', 1),
('simon', 'membership', 1),
('simon', 'location', 1),
('simon', 'links', 1),
('simon', 'download', 1),
('simon', 'competition', 1),
('simon', 'committee', 1),
('simon', 'delusers', 1),
('simon', 'editusers', 1),
('simon', 'createuser', 1),
('simon', 'resetpass', 1),
('simon', 'sitesettings',1);



INSERT INTO  `permission_pages` (
`page`,`name`
)
VALUES ('home','Home'), 
('news','News'),
('purpose','Purpose'),
('permission','User Permissions'), 
('membership','Membership'), 
('schedule','Schedule'), 
('committee','Committee'), 
('competition','Competition'), 
('location','Location'), 
('download','Download'), 
('links','Links'), 
('gall','Gallery'),
('password','Change Password'),
('resetpass','Reset User Password'),
('editusers','Edit Users'),
('delusers','Delete Users'),
('createuser','Create User Account'),
('sitesettings','Site Settings');
