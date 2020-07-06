<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Support\Facades\DB;


trait SettingsTrait
{

    private $currentconfig;
	
    private $settings;
    
    public function generateConfig() {
        
        
        
        $row_config_get = DB::table('spotify_settings')
        ->where('active', '=', '1')
        ->get();

        
			foreach ($row_config_get as $row)
			{
				/*
				if ($row['realvalue']=='')
				$row['realvalue']=$row['defaultvalue'];
				*/
				if($row->type=='select')
						{
							$selectvalues=explode('||',$row->realvalue);
							
							foreach ($selectvalues as $selectvalues_s)
							{
								$isactive='0';
								
								$selectvalueactive=explode(';',$selectvalues_s);
								if($selectvalueactive['1']=='1')
								{
									$isactive='1';
								}
								
								if($selectvalues_s!='')
								{
									
									if($isactive=='1')
						$row->realvalue=$selectvalueactive['0'];
									
								}
								
							}
							
						}
				
				$settings[$row->realname]=$row->realvalue;
			}
            
            
            /*
		private $searchcachekeep=86400;

	private $perpage=10;


	private $rangeslidervalues=array(0,100000000,1700000);

	*/

        $this->settings=$settings;
        
		


    }


    public function getConfig()
    {
        return $this->currentconfig;
	}

    public function getSettings()
    {
        return $this->settings;
	}



}