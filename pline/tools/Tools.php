<?php

namespace app\pline\tools;

use app\models\azans\TblAzans;
use app\models\pagers\TblPagers;
use app\models\schedules\TblSchedules;
use app\models\sounds\TblSounds;
use Yii;
use Exception;

class Tools
{

    public static $TypeSIP = 1;
    public static $TypeALSA = 0;

    public static function convertBytes($value)
    {
        $value = trim($value);
        if (is_numeric($value)) {
            return $value;
        } else {
            $value_length = strlen($value);
            $qty = substr($value, 0, $value_length - 1);
            $unit = strtolower(substr($value, $value_length - 1));
            switch ($unit) {
                case 'k':
                    $qty *= 1024;
                    break;
                case 'm':
                    $qty *= 1048576;
                    break;
                case 'g':
                    $qty *= 1073741824;
                    break;
            }
            return $qty;
        }
    }

    public static function runCmd($cmd)
    {
        $result = shell_exec("echo \"3656\" | sudo -S {$cmd}");
        if ($result === false)
            syslog(LOG_ERR, "Command: {$cmd} -> Result: Error");
        else
            syslog(LOG_INFO, "Command: {$cmd} -> Result: ${result}");
        return $result;
    }

    public static function convertToWav($sound, $wav, $deleteOldSound = true)
    {
        try {
            $cmd = Yii::$app->params['ffmpeg'];
            $args = " -i {$sound} -ar 8000 -ac 1 -acodec pcm_s16le {$wav}";
            if (is_file($wav)) unlink($wav);
            self::runCmd($cmd . $args);
            if ($deleteOldSound) {
                // sleep(3);
                unlink($sound);
            }
            return true;
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
        return false;
    }

    public static function getWavSoundLength($fileName)
    {
        try {
            $result = self::runCmd("/usr/bin/soxi -D \"${fileName}\"");
            $f = floatval(str_replace("\n", "", trim($result)));
            return (int) $f;
        } catch (Exception $ex) {
            return 0;
        }
    }

    public static function callFileOnAgent($sound, $agent, $volume = 0)
    {
        if (str_ends_with($sound, ".wav")) {
            $sound = rtrim($sound, '.wav');
        }

        try {
            $uid = uniqid();
            $strCall = "Channel: Local/*0000@pline-page\n" .
                "Setvar: users={$agent}\n" .
                "Setvar: vol={$volume}\n" .
                "CallerID: \"00000000\"<pline-page>\n" .
                //"MaxRetries: 0\n" .
                //"WaitTime: 45\n" .
                //"RetryTime: 1\n" .
                "Application: Playback\n" .
                "Data: {$sound}\n";
            $path = "/var/spool/asterisk/outgoing/{$uid}.call";
            file_put_contents($path, $strCall);
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public static function callFilesOnAgents($sounds, $agents, $volume = 0)
    {
        $soundString = "";
        foreach ($sounds as $sound) {
            if (str_ends_with($sound, ".wav")) {
                $sound = rtrim($sound, '.wav');
            }
            $soundString .= $sound . "&";
        }
        if ($soundString) {
            $soundString = rtrim($soundString, '&');
        }

        $pagerString = "";
        foreach ($agents as $agent) {
            $pagerString .= $agent . "&";
        }
        if ($pagerString) {
            $pagerString = rtrim($pagerString, '&');
        }

        try {
            $uid = uniqid();
            $strCall = "Channel: Local/*0000@pline-page\n" .
                "Setvar: users={$pagerString}\n" .
                "Setvar: vol={$volume}\n" .
                "CallerID: \"00000000\"<pline-page>\n" .
                //"MaxRetries: 0\n" .
                //"WaitTime: 45\n" .
                //"RetryTime: 1\n" .
                "Application: Playback\n" .
                "Data: {$soundString}\n";
            $path = "/var/spool/asterisk/outgoing/{$uid}.call";
            file_put_contents($path, $strCall);
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public static function hangup($agent)
    {
        $r = false;
        $result = self::runCmd("/usr/sbin/asterisk -x \"core show channels\"");
        if ($result != null && trim($result) != "") {
            $items = explode("\n", $result);
            foreach ($items as $i) {
                if (str_starts_with(trim($i), $agent)) {
                    $channels = explode(" ", $i);
                    if (count($channels) > 0) {
                        self::runCmd('/usr/sbin/asterisk -x "hangup request ' . trim($channels[0]) . '"');
                        $r = true;
                    }
                }
            }
        }

        return $r;
    }

    public static function hangupAll()
    {

        $result = self::runCmd('/usr/sbin/asterisk -x "hangup request all"');
        return $result;
    }

    public static function genrateAgents()
    {
        $contents = file_get_contents("/etc/asterisk/extensions.conf");
        if (strpos($contents, "[pline-page]") === false) {
            $extentions = "[pline-page]\n" .
                "exten => _*0000,1,NoOp(\${users},\${vol})\n" .
                "\tsame => n,Set(VOLUME(TX)=\${vol})\n" .
                "\tsame => n,Set(VOLUME(RX)=\${vol})\n" .
                "\tsame => n,Page(\${users})\n" .
                "\tsame => n,Hangup()\n";
            $contents .= "\n${extentions}\n";
            file_put_contents("/etc/asterisk/extensions.conf", $contents);
        }

        $sip = file_get_contents("/etc/asterisk/sip.conf");
        if (strpos($sip, "#include sip-pline-pager.conf") === false) {
            $sip .= "\n#include sip-pline-pager.conf\n";
            file_put_contents("/etc/asterisk/sip.conf", $sip);
        }

        $agents = TblPagers::find()
            ->where(['enable' => true])
            ->all();

        $plinePager = "";
        foreach ($agents as $agent) {
            if ($agent->type_pager == Self::$TypeSIP) {
                $plinePager .= "[{$agent->username}]\n";
                $plinePager .= "username={$agent->username}\n";
                $plinePager .= "secret={$agent->password}\n";
                $plinePager .= "type=friend\n";
                $plinePager .= "host=dynamic\n";
                $plinePager .= "disallow=all\n";
                $plinePager .= "call-limit=1\n";
                $plinePager .= "allow=ulaw,ulaw,gsm\n";
                $plinePager .= "\n";
            }
        }

        file_put_contents("/etc/asterisk/sip-pline-pager.conf", $plinePager);
        $result = self::runCmd("/usr/sbin/asterisk -x reload");
        return $result;
    }

    // Minute, specified as 0 - 59
    // Hour, specified as 0 - 23
    // Day, specified as 1 - 31
    // Month, specified as 1 - 12
    // Weekday, specified as 0 - 6, 0 => Sundays

    public static function genrateSchedule()
    {
        srand(time());
        $yiiPath = Yii::getAlias("@app");
        $rules = "";
        $weeks = [0 => "6", 1 => "0", 2 => "1", 3 => "2", 4 => "3", 5 => "4", 6 => "5"];
        $model = TblSchedules::findAll(['enable' => true]);
        foreach ($model as $value) {
            $schedules = json_decode($value->schedules, true);
            if ($schedules['type'] == "date") {
                $date = explode("/", $schedules['date']['date']);
                $date = PersianDate::jalali_to_gregorian($date[0], $date[1], $date[2]);
                if (date("Y/m/d") >= "{$date[0]}/{$date[1]}/{$date[2]}") {
                    continue;
                }
                $time = explode(":", $schedules['date']['time']);
                $time[0] = intval($time[0]);
                $time[1] = intval($time[1]);
                $date[0] = intval($date[0]);
                $date[1] = intval($date[1]);
                $rules .= "{$time[1]} {$time[0]} {$date[2]} {$date[1]} * /usr/bin/php {$yiiPath}/yii schedule/run " . $value->id . " &> /dev/null\n";
            } else if ($schedules['type'] == "week") {
                for ($i = 0; $i < 7; $i++) {
                    if ($schedules['week']["w{$i}"]["enable"] == true) {
                        $time = explode(":", $schedules['week']["w{$i}"]['time']);
                        $w = $weeks[$i];
                        $time[0] = intval($time[0]);
                        $time[1] = intval($time[1]);
                        $rules .= "{$time[1]} {$time[0]} * * {$w} /usr/bin/php {$yiiPath}/yii schedule/run " . $value->id . " &> /dev/null\n";
                    }
                }
            }
        }

        $persian_date = PersianDate::GetCurDate();
        $model = TblAzans::findOne(['date' => $persian_date]);
        if ($model) {
            for ($i = 1; $i <= 3; $i++) {
                $time = $model->getAttribute("time{$i}");
                if ($model->getAttribute("befor_sound{$i}") == 0) {
                    $sound = array_diff(scandir("{$yiiPath}/web/before-azans/"), array('..', '.'));
                    $sound = array_values($sound);
                    $rnd = rand(0, count($sound) - 1);
                    $sound = "{$yiiPath}/web/before-azans/{$sound[$rnd]}";
                    $len = self::getWavSoundLength($sound);
                } else if ($model->getAttribute("befor_sound{$i}") == -1) {
                    $sound = "none";
                    $len = 0;
                } else {
                    if ($m = TblSounds::findOne($model->getAttribute("befor_sound{$i}"))) {
                        $sound =  "{$yiiPath}/web/uploads/{$m->file}";
                        $len = self::getWavSoundLength($sound);
                    }
                }

                $time = PersianDate::SecToTimeString(PersianDate::minusSecondesToTime($time, $len), true);
                $time = explode(":", $time);
                $time[0] = intval($time[0]);
                $time[1] = intval($time[1]);
                $time[2] = intval($time[2]);
                $rules .= "{$time[1]} {$time[0]} * * * (sleep {$time[2]}; /usr/bin/php {$yiiPath}/yii schedule/azan {$model->id} {$i} {$sound} &> /dev/null)\n";
            }
        }
        $rules .= "0 0 * * * (sleep 5; /usr/bin/php {$yiiPath}/yii schedule/reload-azan &> /dev/null)\n";
        $rules .= "@reboot (sleep 5; /usr/bin/php {$yiiPath}/yii schedule/reload-azan &> /dev/null)\n";
        file_put_contents("{$yiiPath}/web/cron-rules", $rules);
        Tools::runCmd("/usr/bin/crontab {$yiiPath}/web/cron-rules");
    }
}
