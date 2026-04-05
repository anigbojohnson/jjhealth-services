<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION referrals_solutions_cache()
            RETURNS TRIGGER AS $$
            BEGIN
                IF (TG_OP = 'INSERT' AND NEW.category_id = 4) THEN
                    INSERT INTO cache_invalidation (cache_key) VALUES ('referrals_solutions_4');
                    RETURN NEW;
                END IF;

                IF (TG_OP = 'DELETE' AND OLD.category_id = 4) THEN
                    INSERT INTO cache_invalidation (cache_key) VALUES ('referrals_solutions_4');
                    RETURN OLD;
                END IF;

                IF (TG_OP = 'UPDATE' AND (OLD.category_id = 4 OR NEW.category_id = 4)) THEN
                    INSERT INTO cache_invalidation (cache_key) VALUES ('referrals_solutions_4');
                    RETURN NEW;
                END IF;

                IF (TG_OP = 'DELETE') THEN
                    RETURN OLD;
                END IF;

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER referrals_solutions_cache_trigger
            AFTER INSERT OR UPDATE OR DELETE ON solutions
            FOR EACH ROW
            EXECUTE FUNCTION referrals_solutions_cache();
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS referrals_solutions_cache_trigger ON solutions');
        DB::unprepared('DROP FUNCTION IF EXISTS referrals_solutions_cache');
    }
};