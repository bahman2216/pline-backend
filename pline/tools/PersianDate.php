<?php

namespace app\pline\tools;


class PersianDate
{
    public static function gregorian_to_jalali($gy, $gm, $gd, $mod = '')
    {
        $g_d_m = array(0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334);
        if ($gy > 1600) {
            $jy = 979;
            $gy -= 1600;
        } else {
            $jy = 0;
            $gy -= 621;
        }
        $gy2 = ($gm > 2) ? ($gy + 1) : $gy;
        $days = (365 * $gy) + ((int)(($gy2 + 3) / 4)) - ((int)(($gy2 + 99) / 100)) + ((int)(($gy2 + 399) / 400)) - 80 + $gd + $g_d_m[$gm - 1];
        $jy += 33 * ((int)($days / 12053));
        $days %= 12053;
        $jy += 4 * ((int)($days / 1461));
        $days %= 1461;
        if ($days > 365) {
            $jy += (int)(($days - 1) / 365);
            $days = ($days - 1) % 365;
        }
        $jm = ($days < 186) ? 1 + (int)($days / 31) : 7 + (int)(($days - 186) / 30);
        $jd = 1 + (($days < 186) ? ($days % 31) : (($days - 186) % 30));
        return ($mod == '') ? array($jy, $jm, $jd) : $jy . $mod . (strlen($jm) == 1 ? '0' . $jm : $jm) . $mod . (strlen($jd) == 1 ? '0' . $jd : $jd);
    }

    public static function jalali_to_gregorian($jy, $jm, $jd, $mod = '')
    {
        if ($jy > 979) {
            $gy = 1600;
            $jy -= 979;
        } else {
            $gy = 621;
        }
        $days = (365 * $jy) + (((int)($jy / 33)) * 8) + ((int)((($jy % 33) + 3) / 4)) + 78 + $jd + (($jm < 7) ? ($jm - 1) * 31 : (($jm - 7) * 30) + 186);
        $gy += 400 * ((int)($days / 146097));
        $days %= 146097;
        if ($days > 36524) {
            $gy += 100 * ((int)(--$days / 36524));
            $days %= 36524;
            if ($days >= 365) $days++;
        }
        $gy += 4 * ((int)($days / 1461));
        $days %= 1461;
        if ($days > 365) {
            $gy += (int)(($days - 1) / 365);
            $days = ($days - 1) % 365;
        }
        $gd = $days + 1;
        foreach (array(0, 31, (($gy % 4 == 0 and $gy % 100 != 0) or ($gy % 400 == 0)) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31) as $gm => $v) {
            if ($gd <= $v) break;
            $gd -= $v;
        }
        return ($mod == '') ? array($gy, $gm, $gd) : $gy . $mod . $gm . $mod . $gd;
    }

    public function jdate($format, $timestamp = '', $none = '', $time_zone = 'Asia/Tehran', $tr_num = 'fa')
    {

        $T_sec = 0;/* <= ?????? ???????? ???????? ???????? ?? ???? ?????????? '+' ?? '-' ???? ?????? ?????????? */

        if ($time_zone != 'local') date_default_timezone_set(($time_zone === '') ? 'Asia/Tehran' : $time_zone);
        $ts = $T_sec + (($timestamp === '') ? time() : $this->tr_num($timestamp));
        $date = explode('_', date('H_i_j_n_O_P_s_w_Y', $ts));
        list($j_y, $j_m, $j_d) = PersianDate::gregorian_to_jalali($date[8], $date[3], $date[2]);
        $doy = ($j_m < 7) ? (($j_m - 1) * 31) + $j_d - 1 : (($j_m - 7) * 30) + $j_d + 185;
        $kab = (((($j_y % 33) % 4) - 1) == ((int)(($j_y % 33) * 0.05))) ? 1 : 0;
        $sl = strlen($format);
        $out = '';
        for ($i = 0; $i < $sl; $i++) {
            $sub = substr($format, $i, 1);
            if ($sub == '\\') {
                $out .= substr($format, ++$i, 1);
                continue;
            }
            switch ($sub) {

                case 'E':
                case 'R':
                case 'x':
                case 'X':
                    $out .= 'http://jdf.scr.ir';
                    break;

                case 'B':
                case 'e':
                case 'g':
                case 'G':
                case 'h':
                case 'I':
                case 'T':
                case 'u':
                case 'Z':
                    $out .= date($sub, $ts);
                    break;

                case 'a':
                    $out .= ($date[0] < 12) ? '??.??' : '??.??';
                    break;

                case 'A':
                    $out .= ($date[0] < 12) ? '?????? ???? ??????' : '?????? ???? ??????';
                    break;

                case 'b':
                    $out .= (int)($j_m / 3.1) + 1;
                    break;

                case 'c':
                    $out .= $j_y . '/' . $j_m . '/' . $j_d . ' ??' . $date[0] . ':' . $date[1] . ':' . $date[6] . ' ' . $date[5];
                    break;

                case 'C':
                    $out .= (int)(($j_y + 99) / 100);
                    break;

                case 'd':
                    $out .= ($j_d < 10) ? '0' . $j_d : $j_d;
                    break;

                case 'D':
                    $out .= self::jdate_words(array('kh' => $date[7]), ' ');
                    break;

                case 'f':
                    $out .= self::jdate_words(array('ff' => $j_m), ' ');
                    break;

                case 'F':
                    $out .= self::jdate_words(array('mm' => $j_m), ' ');
                    break;

                case 'H':
                    $out .= $date[0];
                    break;

                case 'i':
                    $out .= $date[1];
                    break;

                case 'j':
                    $out .= $j_d;
                    break;

                case 'J':
                    $out .= self::jdate_words(array('rr' => $j_d), ' ');
                    break;

                case 'k';
                    $out .= $this->tr_num(100 - (int)($doy / ($kab + 365) * 1000) / 10, $tr_num);
                    break;

                case 'K':
                    $out .= $this->tr_num((int)($doy / ($kab + 365) * 1000) / 10, $tr_num);
                    break;

                case 'l':
                    $out .= self::jdate_words(array('rh' => $date[7]), ' ');
                    break;

                case 'L':
                    $out .= $kab;
                    break;

                case 'm':
                    $out .= ($j_m > 9) ? $j_m : '0' . $j_m;
                    break;

                case 'M':
                    $out .= self::jdate_words(array('km' => $j_m), ' ');
                    break;

                case 'n':
                    $out .= $j_m;
                    break;

                case 'N':
                    $out .= $date[7] + 1;
                    break;

                case 'o':
                    $jdw = ($date[7] == 6) ? 0 : $date[7] + 1;
                    $dny = 364 + $kab - $doy;
                    $out .= ($jdw > ($doy + 3) and $doy < 3) ? $j_y - 1 : (((3 - $dny) > $jdw and $dny < 3) ? $j_y + 1 : $j_y);
                    break;

                case 'O':
                    $out .= $date[4];
                    break;

                case 'p':
                    $out .= self::jdate_words(array('mb' => $j_m), ' ');
                    break;

                case 'P':
                    $out .= $date[5];
                    break;

                case 'q':
                    $out .= self::jdate_words(array('sh' => $j_y), ' ');
                    break;

                case 'Q':
                    $out .= $kab + 364 - $doy;
                    break;

                case 'r':
                    $key = self::jdate_words(array('rh' => $date[7], 'mm' => $j_m));
                    $out .= $date[0] . ':' . $date[1] . ':' . $date[6] . ' ' . $date[4] . ' ' . $key['rh'] . '?? ' . $j_d . ' ' . $key['mm'] . ' ' . $j_y;
                    break;

                case 's':
                    $out .= $date[6];
                    break;

                case 'S':
                    $out .= '????';
                    break;

                case 't':
                    $out .= ($j_m != 12) ? (31 - (int)($j_m / 6.5)) : ($kab + 29);
                    break;

                case 'U':
                    $out .= $ts;
                    break;

                case 'v':
                    $out .= self::jdate_words(array('ss' => ($j_y % 100)), ' ');
                    break;

                case 'V':
                    $out .= self::jdate_words(array('ss' => $j_y), ' ');
                    break;

                case 'w':
                    $out .= ($date[7] == 6) ? 0 : $date[7] + 1;
                    break;

                case 'W':
                    $avs = (($date[7] == 6) ? 0 : $date[7] + 1) - ($doy % 7);
                    if ($avs < 0) $avs += 7;
                    $num = (int)(($doy + $avs) / 7);
                    if ($avs < 4) {
                        $num++;
                    } elseif ($num < 1) {
                        $num = ($avs == 4 or $avs == ((((($j_y % 33) % 4) - 2) == ((int)(($j_y % 33) * 0.05))) ? 5 : 4)) ? 53 : 52;
                    }
                    $aks = $avs + $kab;
                    if ($aks == 7) $aks = 0;
                    $out .= (($kab + 363 - $doy) < $aks and $aks < 3) ? '01' : (($num < 10) ? '0' . $num : $num);
                    break;

                case 'y':
                    $out .= substr($j_y, 2, 2);
                    break;

                case 'Y':
                    $out .= $j_y;
                    break;

                case 'z':
                    $out .= $doy;
                    break;

                default:
                    $out .= $sub;
            }
        }
        return ($tr_num != 'en') ? $this->tr_num($out, 'fa', '.') : $out;
    }

    public function jstrftime($format, $timestamp = '', $none = '', $time_zone = 'Asia/Tehran', $tr_num = 'fa')
    {

        $T_sec = 0;/* <= ?????? ???????? ???????? ???????? ?? ???? ?????????? '+' ?? '-' ???? ?????? ?????????? */

        if ($time_zone != 'local') date_default_timezone_set(($time_zone === '') ? 'Asia/Tehran' : $time_zone);
        $ts = $T_sec + (($timestamp === '') ? time() : $this->tr_num($timestamp));
        $date = explode('_', date('h_H_i_j_n_s_w_Y', $ts));
        list($j_y, $j_m, $j_d) = PersianDate::gregorian_to_jalali($date[7], $date[4], $date[3]);
        $doy = ($j_m < 7) ? (($j_m - 1) * 31) + $j_d - 1 : (($j_m - 7) * 30) + $j_d + 185;
        $kab = (((($j_y % 33) % 4) - 1) == ((int)(($j_y % 33) * 0.05))) ? 1 : 0;
        $sl = strlen($format);
        $out = '';
        for ($i = 0; $i < $sl; $i++) {
            $sub = substr($format, $i, 1);
            if ($sub == '%') {
                $sub = substr($format, ++$i, 1);
            } else {
                $out .= $sub;
                continue;
            }
            switch ($sub) {

                    /* Day */
                case 'a':
                    $out .= self::jdate_words(array('kh' => $date[6]), ' ');
                    break;

                case 'A':
                    $out .= self::jdate_words(array('rh' => $date[6]), ' ');
                    break;

                case 'd':
                    $out .= ($j_d < 10) ? '0' . $j_d : $j_d;
                    break;

                case 'e':
                    $out .= ($j_d < 10) ? ' ' . $j_d : $j_d;
                    break;

                case 'j':
                    $out .= str_pad($doy + 1, 3, 0, STR_PAD_LEFT);
                    break;

                case 'u':
                    $out .= $date[6] + 1;
                    break;

                case 'w':
                    $out .= ($date[6] == 6) ? 0 : $date[6] + 1;
                    break;

                    /* Week */
                case 'U':
                    $avs = (($date[6] < 5) ? $date[6] + 2 : $date[6] - 5) - ($doy % 7);
                    if ($avs < 0) $avs += 7;
                    $num = (int)(($doy + $avs) / 7) + 1;
                    if ($avs > 3 or $avs == 1) $num--;
                    $out .= ($num < 10) ? '0' . $num : $num;
                    break;

                case 'V':
                    $avs = (($date[6] == 6) ? 0 : $date[6] + 1) - ($doy % 7);
                    if ($avs < 0) $avs += 7;
                    $num = (int)(($doy + $avs) / 7);
                    if ($avs < 4) {
                        $num++;
                    } elseif ($num < 1) {
                        $num = ($avs == 4 or $avs == ((((($j_y % 33) % 4) - 2) == ((int)(($j_y % 33) * 0.05))) ? 5 : 4)) ? 53 : 52;
                    }
                    $aks = $avs + $kab;
                    if ($aks == 7) $aks = 0;
                    $out .= (($kab + 363 - $doy) < $aks and $aks < 3) ? '01' : (($num < 10) ? '0' . $num : $num);
                    break;

                case 'W':
                    $avs = (($date[6] == 6) ? 0 : $date[6] + 1) - ($doy % 7);
                    if ($avs < 0) $avs += 7;
                    $num = (int)(($doy + $avs) / 7) + 1;
                    if ($avs > 3) $num--;
                    $out .= ($num < 10) ? '0' . $num : $num;
                    break;

                    /* Month */
                case 'b':
                case 'h':
                    $out .= self::jdate_words(array('km' => $j_m), ' ');
                    break;

                case 'B':
                    $out .= self::jdate_words(array('mm' => $j_m), ' ');
                    break;

                case 'm':
                    $out .= ($j_m > 9) ? $j_m : '0' . $j_m;
                    break;

                    /* Year */
                case 'C':
                    $tmp = (int)($j_y / 100);
                    $out .= ($tmp > 9) ? $tmp : '0' . $tmp;
                    break;

                case 'g':
                    $jdw = ($date[6] == 6) ? 0 : $date[6] + 1;
                    $dny = 364 + $kab - $doy;
                    $out .= substr(($jdw > ($doy + 3) and $doy < 3) ? $j_y - 1 : (((3 - $dny) > $jdw and $dny < 3) ? $j_y + 1 : $j_y), 2, 2);
                    break;

                case 'G':
                    $jdw = ($date[6] == 6) ? 0 : $date[6] + 1;
                    $dny = 364 + $kab - $doy;
                    $out .= ($jdw > ($doy + 3) and $doy < 3) ? $j_y - 1 : (((3 - $dny) > $jdw and $dny < 3) ? $j_y + 1 : $j_y);
                    break;

                case 'y':
                    $out .= substr($j_y, 2, 2);
                    break;

                case 'Y':
                    $out .= $j_y;
                    break;

                    /* Time */
                case 'H':
                    $out .= $date[1];
                    break;

                case 'I':
                    $out .= $date[0];
                    break;

                case 'l':
                    $out .= ($date[0] > 9) ? $date[0] : ' ' . (int)$date[0];
                    break;

                case 'M':
                    $out .= $date[2];
                    break;

                case 'p':
                    $out .= ($date[1] < 12) ? '?????? ???? ??????' : '?????? ???? ??????';
                    break;

                case 'P':
                    $out .= ($date[1] < 12) ? '??.??' : '??.??';
                    break;

                case 'r':
                    $out .= $date[0] . ':' . $date[2] . ':' . $date[5] . ' ' . (($date[1] < 12) ? '?????? ???? ??????' : '?????? ???? ??????');
                    break;

                case 'R':
                    $out .= $date[1] . ':' . $date[2];
                    break;

                case 'S':
                    $out .= $date[5];
                    break;

                case 'T':
                    $out .= $date[1] . ':' . $date[2] . ':' . $date[5];
                    break;

                case 'X':
                    $out .= $date[0] . ':' . $date[2] . ':' . $date[5];
                    break;

                case 'z':
                    $out .= date('O', $ts);
                    break;

                case 'Z':
                    $out .= date('T', $ts);
                    break;

                    /* Time and Date Stamps */
                case 'c':
                    $key = self::jdate_words(array('rh' => $date[6], 'mm' => $j_m));
                    $out .= $date[1] . ':' . $date[2] . ':' . $date[5] . ' ' . date('P', $ts) . ' ' . $key['rh'] . '?? ' . $j_d . ' ' . $key['mm'] . ' ' . $j_y;
                    break;

                case 'D':
                    $out .= substr($j_y, 2, 2) . '/' . (($j_m > 9) ? $j_m : '0' . $j_m) . '/' . (($j_d < 10) ? '0' . $j_d : $j_d);
                    break;

                case 'F':
                    $out .= $j_y . '-' . (($j_m > 9) ? $j_m : '0' . $j_m) . '-' . (($j_d < 10) ? '0' . $j_d : $j_d);
                    break;

                case 's':
                    $out .= $ts;
                    break;

                case 'x':
                    $out .= substr($j_y, 2, 2) . '/' . (($j_m > 9) ? $j_m : '0' . $j_m) . '/' . (($j_d < 10) ? '0' . $j_d : $j_d);
                    break;

                    /* Miscellaneous */
                case 'n':
                    $out .= "\n";
                    break;

                case 't':
                    $out .= "\t";
                    break;

                case '%':
                    $out .= '%';
                    break;

                default:
                    $out .= $sub;
            }
        }
        return ($tr_num != 'en') ? $this->tr_num($out, 'fa', '.') : $out;
    }

    public function jmktime($h = '', $m = '', $s = '', $jm = '', $jd = '', $jy = '', $none = '', $timezone = 'Asia/Tehran')
    {
        if ($timezone != 'local') date_default_timezone_set($timezone);
        if ($h === '') {
            return time();
        } else {
            list($h, $m, $s, $jm, $jd, $jy) = explode('_', $this->tr_num($h . '_' . $m . '_' . $s . '_' . $jm . '_' . $jd . '_' . $jy));
            if ($m === '') {
                return mktime($h);
            } else {
                if ($s === '') {
                    return mktime($h, $m);
                } else {
                    if ($jm === '') {
                        return mktime($h, $m, $s);
                    } else {
                        $jdate = explode('_', $this->jdate('Y_j', '', '', $timezone, 'en'));
                        if ($jd === '') {
                            list($gy, $gm, $gd) = PersianDate::jalali_to_gregorian($jdate[0], $jm, $jdate[1]);
                            return mktime($h, $m, $s, $gm);
                        } else {
                            if ($jy === '') {
                                list($gy, $gm, $gd) = PersianDate::jalali_to_gregorian($jdate[0], $jm, $jd);
                                return mktime($h, $m, $s, $gm, $gd);
                            } else {
                                list($gy, $gm, $gd) = PersianDate::jalali_to_gregorian($jy, $jm, $jd);
                                return mktime($h, $m, $s, $gm, $gd, $gy);
                            }
                        }
                    }
                }
            }
        }
    }

    public function jgetdate($timestamp = '', $none = '', $timezone = 'Asia/Tehran', $tn = 'en')
    {
        $ts = ($timestamp === '') ? time() : $this->tr_num($timestamp);
        $jdate = explode('_', $this->jdate('F_G_i_j_l_n_s_w_Y_z', $ts, '', $timezone, $tn));
        return array(
            'seconds' => $this->tr_num((int)$this->tr_num($jdate[6]), $tn),
            'minutes' => $this->tr_num((int)$this->tr_num($jdate[2]), $tn),
            'hours' => $jdate[1],
            'mday' => $jdate[3],
            'wday' => $jdate[7],
            'mon' => $jdate[5],
            'year' => $jdate[8],
            'yday' => $jdate[9],
            'weekday' => $jdate[4],
            'month' => $jdate[0],
            0 => $this->tr_num($ts, $tn)
        );
    }

    public function jcheckdate($jm, $jd, $jy)
    {
        list($jm, $jd, $jy) = explode('_', $this->tr_num($jm . '_' . $jd . '_' . $jy));
        $l_d = ($jm == 12) ? ((((($jy % 33) % 4) - 1) == ((int)(($jy % 33) * 0.05))) ? 30 : 29) : 31 - (int)($jm / 6.5);
        return ($jm > 12 or $jd > $l_d or $jm < 1 or $jd < 1 or $jy < 1) ? false : true;
    }

    public function tr_num($str, $mod = 'en', $mf = '??')
    {
        $num_a = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.');
        $key_a = array('??', '??', '??', '??', '??', '??', '??', '??', '??', '??', $mf);
        return ($mod == 'fa') ? str_replace($num_a, $key_a, $str) : str_replace($key_a, $num_a, $str);
    }

    public function jdate_words($array, $mod = '')
    {
        foreach ($array as $type => $num) {
            $num = (int)$this->tr_num($num);
            switch ($type) {

                case 'ss':
                    $sl = strlen($num);
                    $xy3 = substr($num, 2 - $sl, 1);
                    $h3 = $h34 = $h4 = '';
                    if ($xy3 == 1) {
                        $p34 = '';
                        $k34 = array('????', '??????????', '????????????', '??????????', '????????????', '????????????', '????????????', '????????', '????????', '??????????');
                        $h34 = $k34[substr($num, 2 - $sl, 2) - 10];
                    } else {
                        $xy4 = substr($num, 3 - $sl, 1);
                        $p34 = ($xy3 == 0 or $xy4 == 0) ? '' : ' ?? ';
                        $k3 = array('', '', '????????', '????', '??????', '??????????', '??????', '??????????', '??????????', '??????');
                        $h3 = $k3[$xy3];
                        $k4 = array('', '????', '????', '????', '????????', '??????', '????', '??????', '??????', '????');
                        $h4 = $k4[$xy4];
                    }
                    $array[$type] = (($num > 99) ? str_replace(
                        array('12', '13', '14', '19', '20'),
                        array('???????? ?? ??????????', '???????? ?? ????????', '???????? ?? ????????????', '???????? ?? ????????', '????????????'),
                        substr($num, 0, 2)
                    ) . ((substr($num, 2, 2) == '00') ? '' : ' ?? ') : '') . $h3 . $p34 . $h34 . $h4;
                    break;

                case 'mm':
                    $key = array('??????????????', '????????????????', '??????????', '??????', '??????????', '????????????', '??????', '????????', '??????', '????', '????????', '??????????');
                    $array[$type] = $key[$num - 1];
                    break;

                case 'rr':
                    $key = array(
                        '????', '????', '????', '????????', '??????', '????', '??????', '??????', '????', '????', '??????????', '????????????', '??????????', '????????????', '????????????', '????????????', '????????', '????????', '??????????', '????????', '???????? ?? ????', '???????? ?? ????', '???????? ?? ????', '???????? ?? ????????', '???????? ?? ??????', '???????? ?? ????', '???????? ?? ??????', '???????? ?? ??????', '???????? ?? ????', '????', '???? ?? ????'
                    );
                    $array[$type] = $key[$num - 1];
                    break;

                case 'rh':
                    $key = array('????????????', '????????????', '???? ????????', '????????????????', '??????????????', '????????', '????????');
                    $array[$type] = $key[$num];
                    break;

                case 'sh':
                    $key = array('??????', '??????', '????????????', '??????????', '??????', '????', '??????', '??????', '??????', '????????', '??????????', '????????');
                    $array[$type] = $key[$num % 12];
                    break;

                case 'mb':
                    $key = array('??????', '??????', '????????', '??????????', '??????', '??????????', '??????????', '????????', '??????', '??????', '??????', '??????');
                    $array[$type] = $key[$num - 1];
                    break;

                case 'ff':
                    $key = array('????????', '??????????????', '??????????', '????????????');
                    $array[$type] = $key[(int)($num / 3.1)];
                    break;

                case 'km':
                    $key = array('????', '????', '????', '???????', '????', '???????', '???????', '???????', '????', '????', '???????', '???????');
                    $array[$type] = $key[$num - 1];
                    break;

                case 'kh':
                    $key = array('??', '??', '??', '??', '??', '??', '??');
                    $array[$type] = $key[$num];
                    break;

                default:
                    $array[$type] = $num;
            }
        }
        return ($mod === '') ? $array : implode($mod, $array);
    }

    public function dateDifference($firstDate, $secondDate)
    {
        list($fdY, $fdM, $fdD) = explode('/', $firstDate);
        list($sdY, $sdM, $sdD) = explode('/', $secondDate);
        $fts = $this->jmktime(0, 0, 0, $fdM, $fdD, $fdY);
        $sts = $this->jmktime(0, 0, 0, $sdM, $sdD, $sdY);
        $diff = $sts - $fts;
        return round($diff / 86400);
    }

    public static function getYear()
    {
        return explode('/', PersianDate::gregorian_to_jalali(date("Y"), date("m"), date("d"), "/"))[0];
    }

    public static function getMonth()
    {
        return explode('/', PersianDate::gregorian_to_jalali(date("Y"), date("m"), date("d"), "/"))[1];
    }

    public static function getDay()
    {
        return explode('/', PersianDate::gregorian_to_jalali(date("Y"), date("m"), date("d"), "/"))[2];
    }

    /**
     * @param $time1
     * @param $time2
     * @return false|int
     */
    public static function differenceTime($time1, $time2, $second = false)
    {
        if ($second == false) {
            $time1 = $time1 . ':00';
            $time1 = $time2 . ':00';
        }
        $t1 = strtotime($time1);
        $t2 = strtotime($time2);
        if ($t2 > $t1) {
            $start = strtotime('00:00:00');
            $end = strtotime('23:59:59');
            return ($end - $t2) + ($t1 - $start);
        }
        return $t1 - $t2;
    }

    public static function sumSecondesToTime($time, $second)
    {

        $t1 = strtotime($time);
        $second = strtotime(self::SecToTimeString($second, true));
        return $t1 + $second;
    }

    public static function minusSecondesToTime($time, $second)
    {
        $t1 = strtotime($time);
        $second = strtotime(self::SecToTimeString($second, true));
        return $t1 - $second;
    }


    /**
     *
     * @param bool $out_array
     * @return array|string
     */
    public static function getCurDate($out_array = false)
    {
        if ($out_array === true) {
            return PersianDate::gregorian_to_jalali(date("Y"), date("m"), date("d"));
        }
        return PersianDate::gregorian_to_jalali(date("Y"), date("m"), date("d"), "/");
    }

    /**
     *
     * @param int $month
     * @return int
     */
    public static function GetMaxDayMonth($month = null)
    {
        $month = $month == null ? (PersianDate::gregorian_to_jalali(date("Y"), date("m"), date("d"))[1]) : $month;
        $month = intval($month);
        if ($month >= 1 && $month <= 6) {
            return 31;
        } else if ($month >= 7 && $month <= 11) {
            return 30;
        }
        return 29;
    }

    public static function SecToTimeString($seconds, $show_sec = false)
    {

        $hours = floor($seconds / 3600);
        $mins = floor($seconds / 60 % 60);
        $secs = floor($seconds % 60);
        if ($show_sec === false) {
            return sprintf('%02d:%02d', $hours, $mins);
        }
        return sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
    }

    /**
     * secondDate - firstDate
     * @param $firstDate
     * @param $secondDate
     * @param $without_holiday
     * @return int
     */
    public static function DateDifferenceDate($firstDate, $secondDate)
    {
        $jDate = new PersianDate();
        list($fdY, $fdM, $fdD) = explode('/', $firstDate);
        list($sdY, $sdM, $sdD) = explode('/', $secondDate);
        list($fdY, $fdM, $fdD) = PersianDate::jalali_to_gregorian($fdY, $fdM, $fdD);
        list($sdY, $sdM, $sdD) = PersianDate::jalali_to_gregorian($sdY, $sdM, $sdD); //Big

        $fts = $jDate->jmktime(0, 0, 0, $fdM, $fdD, $fdY);
        $sts = $jDate->jmktime(0, 0, 0, $sdM, $sdD, $sdY); //Big
        $diff = $sts - $fts;
        $day = round($diff / 86400);
        return $day;
    }

    public static function AddDayToDate($date, $day)
    {
        list($fdY, $fdM, $fdD) = explode('/', $date);
        $mdate = self::jalali_to_gregorian($fdY, $fdM, $fdD, "-");
        $date = explode("/", date('Y/m/d', strtotime($mdate . " + {$day} days")));
        return self::gregorian_to_jalali($date[0], $date[1], $date[2], "/");
    }

    public static  function isValidTime($time, $seconds = false)
    {
        $time = explode(':', $time);
        if ($seconds) {
            if (count($time) != 3) return false;
            if (!($time[0] >= '00' && $time[0] <= '23')) return false;
            if (!($time[1] >= '00' && $time[1] <= '59')) return false;
            if (!($time[2] >= '00' && $time[2] <= '59')) return false;
            return true;
        } else {
            if (count($time) != 2) return false;
            if (!($time[0] >= '00' && $time[0] <= '23')) return false;
            if (!($time[1] >= '00' && $time[1] <= '59')) return false;
            return true;
        }
    }

    public static function isValidDate($date)
    {
        $ndate  = explode('/', $date);
        if (count($ndate) !== 3) {
            return false;
        }

        if (strlen($ndate[0]) != 4 || $ndate[0] <= '1362') {
            return false;
        }

        if (!(strlen($ndate[1]) == 2 && $ndate[1] >= '01' && $ndate[1] <= '12')) {
            return false;
        }

        if (strlen($ndate[2]) == 2 && $ndate[2] >= '01' && $ndate[2] <= '31' && $ndate[1] >= '01' && $ndate[1] <= '06') {
            return true;
        } else {
            return false;
        }

        if (strlen($ndate[2]) == 2 && $ndate[2] >= '01' && $ndate[2] <= '30' && $ndate[1] >= '07' && $ndate[1] <= '12') {
            return true;
        } else {
            return false;
        }
    }
}
