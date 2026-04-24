<?php

namespace Modules\CMS\Database\Seeders;

use Illuminate\Database\Seeder;

class CMSDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Order matters: LandingPage first (it sets up the homepage panels
        // + CMS pages that other modules may rely on), then Portfolio so
        // the studio's showcase work is available on /portfolio.
        $this->call([
            LandingPageSeeder::class,
            PortfolioSeeder::class,
        ]);
    }
}
