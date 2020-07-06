<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\GetSpotifySearches::class,
        Commands\GetArtistsClaimState::class,
        Commands\AddRandomUsers::class,
        Commands\SaveArtistPicks::class,
        Commands\GenerateStats::class,
        Commands\GenerateHomePageStats::class,
        Commands\HomePageActiveGenresChange::class,
        Commands\CheckAccountActiveState::class,
        Commands\GetAccountTokens::class,
        Commands\AddPlaylistToAccounts::class,
        Commands\FillGenrePlaylists::class,
        Commands\AddGenrePlaylistsToProfile::class,
        Commands\GetTracksFromPlaylists::class,
        
    ];
    
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        
        $schedule->command('cron:getspotifysearches')
                ->cron('*/2 * * * *')
                ->appendOutputTo('cron_log.txt');

        $schedule->command('cron:getartistsclaimstate')
                ->cron('*/2 * * * *')
                ->appendOutputTo('cron_log.txt');

        $schedule->command('cron:addrandomusers')
               ->cron('*/3 * * * *')
                ->appendOutputTo('cron_log.txt');

        $schedule->command('cron:saveartistpicks')
                ->cron('*/30 * * * *')
                 ->appendOutputTo('cron_log.txt');
                        
        $schedule->command('cron:generatestats')
                 ->cron('14 */2 * * *')
                 ->appendOutputTo('cron_log.txt');

        $schedule->command('cron:generatehomepagestats')
                 ->cron('20 21 * * *')
                 ->appendOutputTo('cron_log.txt');

        $schedule->command('cron:homepageactivegenreschange')
                 ->cron('*/20 * * * *')
                 ->appendOutputTo('cron_log.txt');
                 
        $schedule->command('cron:checkaccountactivestate')
                 ->cron('20 20 * * *')
                 ->appendOutputTo('cron_log.txt');

        $schedule->command('cron:getaccounttokens')
                 ->cron('24 * * * *')
                 ->appendOutputTo('cron_log.txt');

        $schedule->command('cron:fillgenreplaylists')
                 ->cron('12 */3 * * *')
                 ->appendOutputTo('cron_log.txt');

        //$schedule->command('cron:gettracksfromplaylists') //should turn off after finished
        //        ->cron('*/30 * * * *')
        //        ->appendOutputTo('cron_log.txt');

        $schedule->command('cron:addplaylisttoaccounts') //add playlists to Artist playlists,should run cause genres are created constantly
                 ->cron('35 10 */3 * *')
                 ->appendOutputTo('cron_log.txt');
                 
        //$schedule->command('cron:addgenreplayliststoprofile') //included in adduserstoouraccounts,turn off
         //        ->cron('14 8 * * *')
         //        ->appendOutputTo('cron_log.txt');

        $schedule->command('cron:adduserstoouraccounts') //adding artist playlists and posting them on artist profiles
                 ->cron('44 8 * * *')
                 ->appendOutputTo('cron_log.txt');
       


        
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
