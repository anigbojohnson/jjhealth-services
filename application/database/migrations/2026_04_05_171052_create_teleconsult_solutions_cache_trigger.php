<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION teleconsult_solutions_cache()
            RETURNS TRIGGER AS $$
            BEGIN
                IF (TG_OP = 'INSERT' AND NEW.category_id = 1) THEN
                    INSERT INTO cache_invalidation (cache_key) VALUES ('teleconsult_solutions_1');
                    RETURN NEW;
                END IF;

                IF (TG_OP = 'DELETE' AND OLD.category_id = 1) THEN
                    INSERT INTO cache_invalidation (cache_key) VALUES ('teleconsult_solutions_1');
                    RETURN OLD;
                END IF;

                IF (TG_OP = 'UPDATE' AND (OLD.category_id = 1 OR NEW.category_id = 1)) THEN
                    INSERT INTO cache_invalidation (cache_key) VALUES ('teleconsult_solutions_1');
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
            CREATE TRIGGER teleconsult_solutions_cache_trigger
            AFTER INSERT OR UPDATE OR DELETE ON solutions
            FOR EACH ROW
            EXECUTE FUNCTION teleconsult_solutions_cache();
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS teleconsult_solutions_cache_trigger ON solutions');
        DB::unprepared('DROP FUNCTION IF EXISTS teleconsult_solutions_cache');
    }
};