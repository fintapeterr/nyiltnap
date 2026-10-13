
DELETE FROM `student_professions`;
DELETE FROM `professions`;

ALTER TABLE `professions` AUTO_INCREMENT = 1;


INSERT INTO `professions` (`id`, `name`) VALUES
(1, ' Gazdálkodás és menedzsment: Pénzügyi-számviteli ügyintéző'),
(2, ' Informatika és távközlés: Szoftverfejlesztő és -tesztelő'),
(3, ' Specializált gép- és járműgyártás: Mechatronikai technikus'),
(4, ' Turisztikai technikus: Szálloda szervező'),
(5, ' Pincér-vendégtéri szakember'),
(6, ' Szakács'),
(7, ' Cukrász'),
(8, ' Szerszám- és készülékgyártó'),
(9, ' CNC-programozó'),
(10, ' Mechatronikus karbantartó');
