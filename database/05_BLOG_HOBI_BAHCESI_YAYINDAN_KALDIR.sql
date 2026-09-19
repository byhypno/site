-- EGESER V8
-- Hobi bahçesi + konteyner odaklı yazıyı güvenli biçimde yayından kaldırır.
-- Kayıt silinmez. Önce tam satır yedeği alınır, ardından status=0 yapılır.

CREATE TABLE IF NOT EXISTS `oc_egeser_blog_post_removed_20260919`
LIKE `oc_egeser_blog_post`;

REPLACE INTO `oc_egeser_blog_post_removed_20260919`
SELECT *
FROM `oc_egeser_blog_post`
WHERE `slug` = 'izmir-hobi-bahcesi-konteyner-ev-prefabrik-ev';

UPDATE `oc_egeser_blog_post`
SET `status` = '0', `date_modified` = NOW()
WHERE `slug` = 'izmir-hobi-bahcesi-konteyner-ev-prefabrik-ev';

-- Beklenen sonuç: 1 satır ve status=0.
SELECT `blog_id`, `slug`, `title`, `status`
FROM `oc_egeser_blog_post`
WHERE `slug` = 'izmir-hobi-bahcesi-konteyner-ev-prefabrik-ev';
