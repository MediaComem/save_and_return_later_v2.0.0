<?php

namespace Unisante\SaveAndReturnLater;

class SaveAndReturnLater extends \ExternalModules\AbstractExternalModule
{

    public function redirect() {
        $this->log("Mon message de log", [
            "user" => 'admin',
            "details" => "Launch redirect"
        ]);

        $survey_name = $_REQUEST['survey_name'];
        # Ask to Vincent why this parameter isn't available here
        #$redirectUrl = $this->getProjectSetting('return_web_site_url');
        
        header('Location: '."http://localhost:8080/submit-survey//?survey_name=$survey_name&survey_status=Completed");
        die();
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

            $this->log("Mon message de log", [
                "details" => "instrument: $instrument"
            ]);

            $redirectUrl = $this->getProjectSetting('return_web_site_url');
            $this->log("Mon message de log", [
                "details" => "url: $redirectUrl"
            ]);
            $url = "$redirectUrl?survey_name=$instrument&survey_status=Incomplete";
            
            print "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$this->getUrl("style.css")."\" media=\"screen\">"; ?>

          <script type = "text/javascript">
            $(document).ready(function(){

                //Hide existing fields (email, return code)
                $(document.querySelector("#return_instructions > div:nth-child(2)")).remove();
                $(document.querySelector("#return_instructions > div:nth-child(2)")).remove();
                $(document.querySelector("#return_continue_form > b")).remove();

                //Replace text of #provideEmail div
                $('#provideEmail').html("");

                //Append button which allows to return to instruments
                $( "#return_instructions" ).append('<button class = "jqbutton" id="redirect_to_instruments">OK</button>');

                //On click, return to instruments page
                $("#redirect_to_instruments").click(function() {
                    location.href = "<?php echo $url; ?>";

                })
            })
        </script>
     <?php
        }
    }
}
