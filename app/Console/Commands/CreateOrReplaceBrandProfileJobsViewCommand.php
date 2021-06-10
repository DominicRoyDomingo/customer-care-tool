<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateOrReplaceBrandProfileJobsViewCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'view:CreateOrReplaceBrandProfileJobsViewCommand';

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
            CREATE OR REPLACE VIEW brand_profile_jobs_vw
            AS (
                SELECT
                    bc.brand_id AS brand_id,
                    cj.profile_id AS profile_id,
                    COALESCE(
                        jc.category, json_object(
                            'en', '(No Category)'
                        )
                    ) AS category,
                    COALESCE(
                        jp.profession, json_object(
                            'en', '(No Profession)'
                        )
                    ) AS profession,
                    COALESCE(
                        jd.description, json_object(
                            'en', '(No Specialization)'
                        )
                    ) AS specialization
                FROM
                    (
                        (
                            (
                                (
                                    client_jobs cj
                                JOIN brand_clients bc ON
                                    (
                                        bc.profile_id = cj.profile_id
                                    )
                                )
                            LEFT JOIN job_category jc ON
                                (
                                    jc.id = cj.job_category_id
                                )
                            )
                        LEFT JOIN job_profession jp ON
                            (
                                jp.id = cj.job_profession_id
                            )
                        )
                    LEFT JOIN job_description jd ON
                        (
                            jd.id = cj.job_description_id
                        )
                    )
                UNION
                SELECT
                    0 AS brand_id,
                    cj1.profile_id AS profile_id,
                    COALESCE(
                        jc1.category, json_object(
                            'en', '(No Category)'
                        )
                    ) AS category,
                    COALESCE(
                        jp1.profession, json_object(
                            'en', '(No Profession)'
                        )
                    ) AS profession,
                    COALESCE(
                        jd1.description, json_object(
                            'en', '(No Specialization)'
                        )
                    ) AS specialization
                FROM
                    (
                        (
                            (
                                client_jobs cj1
                            LEFT JOIN job_category jc1 ON
                                (
                                    jc1.id = cj1.job_category_id
                                )
                            )
                        LEFT JOIN job_profession jp1 ON
                            (
                                jp1.id = cj1.job_profession_id
                            )
                        )
                    LEFT JOIN job_description jd1 ON
                        (
                            jd1.id = cj1.job_description_id
                        )
                    )
                WHERE
                    EXISTS(
                        SELECT
                            1
                        FROM
                            brand_clients bc2
                        WHERE
                            bc2.profile_id = cj1.profile_id
                    ) IS FALSE
            )
            "
        );
    }
}
