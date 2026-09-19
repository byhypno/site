-- EGESER PREFABRIK - BLOG ICERIKLERINI GERI AL
SET NAMES utf8mb4;
START TRANSACTION;
UPDATE `oc_egeser_blog_post` current_post
INNER JOIN `oc_egeser_blog_post_backup_20260919` backup_post ON backup_post.blog_id = current_post.blog_id
SET current_post.description = backup_post.description;
COMMIT;
