<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateOrReplaceBrandProfileLocationsViewCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'view:CreateOrReplaceBrandProfileLocationsView';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or Replace brand_profile_locations_vw View';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::statement(
            "
            CREATE OR REPLACE VIEW brand_profile_locations_vw
            AS (
                SELECT
                    0 AS brand_id,
                    cl.profile_id AS profile_id,
                    COALESCE(
                        wcn.name, '(No Country)'
                    ) AS country,
                    COALESCE(
                        wd.name, '(No Province)'
                    ) AS province,
                    COALESCE(
                        wc.name, '(No City)'
                    ) AS city
                FROM
                    (
                        (
                            (
                                client_locations cl
                            LEFT JOIN world_countries wcn ON
                                (
                                    wcn.id = cl.world_countries_id
                                )
                            )
                        LEFT JOIN world_divisions wd ON
                            (
                                wd.id = cl.world_provinces_id
                            )
                        )
                    LEFT JOIN world_cities wc ON
                        (
                            wc.id = cl.world_cities_id
                        )
                    )
                WHERE
                    EXISTS(
                        SELECT
                            1
                        FROM
                            brand_clients bc
                        WHERE
                            bc.profile_id = cl.profile_id
                    ) IS FALSE
                GROUP BY
                    cl.profile_id,
                    wcn.name,
                    wd.name,
                    wc.name
                UNION
                SELECT
                    bc.brand_id AS brand_id,
                    cl.profile_id AS profile_id,
                    COALESCE(
                        wcn.name, '(No Country)'
                    ) AS country,
                    COALESCE(
                        wd.name, '(No Province)'
                    ) AS province,
                    COALESCE(
                        wc.name, '(No City)'
                    ) AS city
                FROM
                    (
                        (
                            (
                                (
                                    client_locations cl
                                JOIN brand_clients bc ON
                                    (
                                        bc.profile_id = cl.profile_id
                                    )
                                )
                            LEFT JOIN world_countries wcn ON
                                (
                                    wcn.id = cl.world_countries_id
                                )
                            )
                        LEFT JOIN world_divisions wd ON
                            (
                                wd.id = cl.world_provinces_id
                            )
                        )
                    LEFT JOIN world_cities wc ON
                        (
                            wc.id = cl.world_cities_id
                        )
                    )
                GROUP BY
                    bc.brand_id,
                    cl.profile_id,
                    wcn.name,
                    wd.name,
                    wc.name
            )
            "
        );
    }
}
