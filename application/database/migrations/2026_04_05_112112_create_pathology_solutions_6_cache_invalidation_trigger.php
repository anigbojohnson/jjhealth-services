<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
  public function up(): void
    {
        DB::unprepared("
            CREATE OR REPLACE FUNCTION notify_cache_invalidation()
            RETURNS TRIGGER AS \$\$
            BEGIN
                IF (TG_OP = 'INSERT' AND NEW.category_id = 6) THEN
                    INSERT INTO cache_invalidation (cache_key) 
                    VALUES ('pathology_solutions_6');
                    RETURN NEW;
                END IF;

                IF (TG_OP = 'DELETE' AND OLD.category_id = 6) THEN
                    INSERT INTO cache_invalidation (cache_key) 
                    VALUES ('pathology_solutions_6');
                    RETURN OLD;
                END IF;

                IF (TG_OP = 'UPDATE' AND (OLD.category_id = 6 OR NEW.category_id = 6)) THEN
                    INSERT INTO cache_invalidation (cache_key) 
                    VALUES ('pathology_solutions_6');
                    RETURN NEW;
                END IF;

                IF (TG_OP = 'DELETE') THEN
                    RETURN OLD;
                END IF;

                RETURN NEW;
            END;
            \$\$ LANGUAGE plpgsql;
        ");

        DB::unprepared("
            CREATE TRIGGER solutions_cache_invalidation_trigger
            AFTER INSERT OR UPDATE OR DELETE ON solutions
            FOR EACH ROW
            EXECUTE FUNCTION notify_cache_invalidation();
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS solutions_cache_invalidation_trigger ON solutions');
        DB::unprepared('DROP FUNCTION IF EXISTS notify_cache_invalidation');
    }
};