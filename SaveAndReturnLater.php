<?php

namespace Unisante\SaveAndReturnLater;

class SaveAndReturnLater extends \ExternalModules\AbstractExternalModule
{

    public function redirect() {
        $survey_name = $_REQUEST['survey_name'];
        $project_id = $_REQUEST['pid'];
        $redirectUrl = $this->getProjectSetting('return_web_site_url', $project_id);
        
        header('Location: '."$redirectUrl?survey_name=$survey_name&survey_status=Completed");
        $this->exitAfterHook();
    }

    public function redcap_every_page_top()
    {
        if (PAGE === "surveys/index.php" && isset($_GET['__return'])) {
            $instrument = '';
            $hash = db_real_escape_string($_GET['s']);
            $sql = "SELECT form_name FROM redcap_surveys WHERE survey_id = (SELECT survey_id FROM redcap_surveys_participants WHERE hash = '$hash') LIMIT 1;";
            $query = db_query($sql);
            while($row = db_fetch_assoc($query)){
                $instrument =  $row['form_name'];
            }

            $redirectUrl = $this->getProjectSetting('return_web_site_url');
            $url = "$redirectUrl?survey_name=$instrument&survey_status=Incomplete";
            
            header('Location: '."$url");
            $this->exitAfterHook();
        }
    }
}
