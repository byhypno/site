-- EGESER V8 geri alma
-- Yalnız daha önce yedeklenen yazının yayın durumunu eski değerine döndürür.

UPDATE `oc_egeser_blog_post` p
INNER JOIN `oc_egeser_blog_post_removed_20260919` b
  ON b.`blog_id` = p.`blog_id`
SET p.`status` = b.`status`, p.`date_modified` = NOW()
WHERE p.`slug` = 'izmir-hobi-bahcesi-konteyner-ev-prefabrik-ev';

SELECT `blog_id`, `slug`, `title`, `status`
FROM `oc_egeser_blog_post`
WHERE `slug` = 'izmir-hobi-bahcesi-konteyner-ev-prefabrik-ev';
