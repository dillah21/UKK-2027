-- Jalankan sekali di phpMyAdmin pada database db_ukk_2026
UPDATE t_users
SET name = 'ARI SANDRIANA', updated_at = NOW()
WHERE id = 7 AND role = 'guru';

UPDATE t_guru
SET nama = 'ARI SANDRIANA', updated_at = NOW()
WHERE user_id = 7;
