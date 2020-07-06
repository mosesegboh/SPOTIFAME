<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

use App\Helpers\AppHelper as Helperfunctions;
use App\Helpers\SpotifyHelper;

use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

use App\Helpers\UserAgentHelper;

class AddGenrePlaylistsToProfile extends Command
{
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:addgenreplayliststoprofile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Genre Playlists To Profile';

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
        
        ini_set('max_execution_time', 600); // 600 = 10 minutes
        
        SpotifyHelper::instance()->addGenrePlaylistsToProfile();




    }


    

}
