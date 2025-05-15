<?php
// Start session if not already started (important for flash messages)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
include '../includes/db_connect.php';
include '../includes/functions.php'; // Functions like sanitize_input, get_all_lotteries

// Define Lottery IDs as constants for easier management and readability
// These IDs should match your database `lotteries` table from image_d59f6c.png
if (!defined('MAHAJANA_SAMPATHA_ID')) define('MAHAJANA_SAMPATHA_ID', 1);
if (!defined('GOVISETHA_ID')) define('GOVISETHA_ID', 2);
if (!defined('MEGA_POWER_ID')) define('MEGA_POWER_ID', 3); // This is the one we are changing
if (!defined('ADA_KOTIPATHI_ID')) define('ADA_KOTIPATHI_ID', 4);
if (!defined('DHANA_NIDHANAYA_ID')) define('DHANA_NIDHANAYA_ID', 7);
if (!defined('HANDAHANA_ID')) define('HANDAHANA_ID', 9);
if (!defined('ADA_SAMPATHA_ID')) define('ADA_SAMPATHA_ID', 10);
if (!defined('NLB_JAYA_ID')) define('NLB_JAYA_ID', 13);
if (!defined('LAGNA_WASANA_ID')) define('LAGNA_WASANA_ID', 14);
if (!defined('SHANIDA_ID')) define('SHANIDA_ID', 15);
if (!defined('SUPER_BALL_ID')) define('SUPER_BALL_ID', 16);
if (!defined('KAPRUKA_ID')) define('KAPRUKA_ID', 17);
if (!defined('SUPIRI_DHANA_SAMPATHA_ID')) define('SUPIRI_DHANA_SAMPATHA_ID', 18);
if (!defined('JAYA_SAMPATHA_ID')) define('JAYA_SAMPATHA_ID', 19);
if (!defined('JAYODA_ID')) define('JAYODA_ID', 20);
// if (!defined('SASIRI_ID')) define('SASIRI_ID', 21);

// Initialize variables
$lotteries = [];
$form_data = [ // To repopulate form on error
    'lottery_id' => '',
    'draw_date' => date('Y-m-d'),
    'jackpot_amount' => '',
    'other_prize_details' => '',
    // Specific fields for repopulation (add all you use in the form)
    'ms_winning_letter' => '',
    'ms_winning_num1' => '',
    'ms_winning_num2' => '',
    'ms_winning_num3' => '',
    'ms_winning_num4' => '',
    'ms_winning_num5' => '',
    'ms_winning_num6' => '',
    'g_letter' => '',
    'g_num1' => '',
    'g_num2' => '',
    'g_num3' => '',
    'g_num4' => '',
    // Mega Power: letter, 4 main numbers, 1 special number
    'mp_letter' => '',
    'mp_num1' => '',
    'mp_num2' => '',
    'mp_num3' => '',
    'mp_num4' => '',
    'mp_special_num1' => '',
    'dn_main_letter' => '',
    'dn_main_num1' => '',
    'dn_main_num2' => '',
    'dn_main_num3' => '',
    'dn_main_num4' => '',
    'dn_lakshapathi_num1' => '',
    'dn_lakshapathi_num2' => '',
    'dn_lakshapathi_num3' => '',
    'dn_lakshapathi_num4' => '',
    'dn_lakshapathi_num5' => '',
    'h_lagna_name' => '',
    'h_lagna_num1' => '',
    'h_lagna_num2' => '',
    'h_lagna_num3' => '',
    'h_lagna_num4' => '',
    'h_dhanayogaya_num1' => '',
    'h_dhanayogaya_num2' => '',
    'h_dhanayogaya_num3' => '',
    'h_dhanayogaya_num4' => '',
    'h_dhanayogaya_num5' => '',
    'h_daiwa_ankaya' => '',
    'lw_lagna_name' => '',
    'lw_num1' => '',
    'lw_num2' => '',
    'lw_num3' => '',
    'lw_num4' => '',
    'k_letter' => '',
    'k_num1' => '',
    'k_num2' => '',
    'k_num3' => '',
    'k_num4' => '',
    'k_special_num' => '',
    'as_r1_num1' => '',
    'as_r1_num2' => '',
    'as_r2_num1' => '',
    'as_r2_num2' => '',
    'as_r2_num3' => '',
    'as_r3_letter' => '',
    'as_r3_num1' => '',
    'as_r3_num2' => '',
    'as_r3_num3' => '',
    'as_r3_num4' => '',
    'standard_winning_numbers' => ''
];
$error_message = '';

if (isset($pdo) && $db_connected) {
    try {
        $lotteries = get_all_lotteries($pdo);
    } catch (PDOException $e) {
        error_log("Database error in add_result.php (lotteries): " . $e->getMessage(), 0);
        $error_message = "ලොතරැයි ලැයිස්තුව ලබාගැනීමේදී දෝෂයක් ඇතිවිය.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($form_data as $key => $value) {
        if (isset($_POST[$key])) {
            $form_data[$key] = $_POST[$key]; // Repopulate for sticky form
        }
    }

    $form_data['lottery_id'] = isset($_POST['lottery_id']) ? (int)$_POST['lottery_id'] : 0;
    $form_data['draw_date'] = isset($_POST['draw_date']) ? sanitize_input($_POST['draw_date']) : date('Y-m-d');
    $form_data['jackpot_amount'] = isset($_POST['jackpot_amount']) ? filter_var($_POST['jackpot_amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : '';
    $form_data['other_prize_details'] = isset($_POST['other_prize_details']) ? sanitize_input($_POST['other_prize_details']) : '';

    $winning_numbers_json_data = [];
    $winning_numbers_final_json_string = '';

    if ($form_data['lottery_id'] <= 0) {
        $error_message = 'කරුණාකර ලොතරැයිය තෝරන්න.';
    } elseif (empty($form_data['draw_date'])) {
        $error_message = 'කරුණාකර දිනුම් ඇදීම් දිනය ඇතුළත් කරන්න.';
    }

    if (empty($error_message)) {
        $selected_lottery_id = $form_data['lottery_id'];
        $temp_numbers = [];

        switch ($selected_lottery_id) {
            case MAHAJANA_SAMPATHA_ID:
            case SUPIRI_DHANA_SAMPATHA_ID:
                $winning_numbers_json_data['letter'] = isset($_POST['ms_winning_letter']) ? strtoupper(sanitize_input(trim($_POST['ms_winning_letter']))) : '';
                for ($i = 1; $i <= 6; $i++) {
                    $num_val = isset($_POST['ms_winning_num' . $i]) ? trim(sanitize_input($_POST['ms_winning_num' . $i])) : '';
                    if ($num_val !== '' && is_numeric($num_val) && $num_val >= 0 && $num_val <= 99) $temp_numbers[] = (int)$num_val;
                    else $temp_numbers[] = null;
                }
                if (!ctype_alpha($winning_numbers_json_data['letter']) || strlen($winning_numbers_json_data['letter']) !== 1) $error_message = "ජයග්‍රාහී අකුර (Letter) නිවැරදි නැත.";
                foreach ($temp_numbers as $k => $n) if ($n === null) {
                    $error_message = "අංක " . ($k + 1) . " නිවැරදිව ඇතුළත් කරන්න (0-99).";
                    break;
                }
                if (empty($error_message)) $winning_numbers_json_data['numbers'] = $temp_numbers;
                break;

            case GOVISETHA_ID:
            case ADA_KOTIPATHI_ID:
            case NLB_JAYA_ID:
            case SUPER_BALL_ID:
            case SHANIDA_ID:
            case JAYODA_ID:
                $winning_numbers_json_data['letter'] = isset($_POST['g_letter']) ? strtoupper(sanitize_input(trim($_POST['g_letter']))) : '';
                for ($i = 1; $i <= 4; $i++) { // L4N means 4 numbers
                    $num_val = isset($_POST['g_num' . $i]) ? trim(sanitize_input($_POST['g_num' . $i])) : '';
                    if ($num_val !== '' && is_numeric($num_val) && $num_val >= 0 && $num_val <= 99) $temp_numbers[] = (int)$num_val;
                    else $temp_numbers[] = null;
                }
                if (!ctype_alpha($winning_numbers_json_data['letter']) || strlen($winning_numbers_json_data['letter']) !== 1) $error_message = "ජයග්‍රාහී අකුර නිවැරදි නැත.";
                foreach ($temp_numbers as $k => $n) if ($n === null) {
                    $error_message = "අංක " . ($k + 1) . " නිවැරදිව ඇතුළත් කරන්න (0-99).";
                    break;
                }
                if (empty($error_message)) $winning_numbers_json_data['numbers'] = $temp_numbers;
                break;

            case MEGA_POWER_ID: // UPDATED: Letter + 4 Main Nums + 1 Special Num
                $winning_numbers_json_data['letter'] = isset($_POST['mp_letter']) ? strtoupper(sanitize_input(trim($_POST['mp_letter']))) : '';
                $main_numbers_mp = [];
                for ($i = 1; $i <= 4; $i++) { // Changed from 5 to 4 main numbers
                    $num_val = isset($_POST['mp_num' . $i]) ? trim(sanitize_input($_POST['mp_num' . $i])) : '';
                    if ($num_val !== '' && is_numeric($num_val) && $num_val >= 0 && $num_val <= 99) $main_numbers_mp[] = (int)$num_val;
                    else $main_numbers_mp[] = null;
                }

                $special_number_mp_val = isset($_POST['mp_special_num1']) ? trim(sanitize_input($_POST['mp_special_num1'])) : ''; // Only one special number
                $special_number_mp = null;
                if ($special_number_mp_val !== '' && is_numeric($special_number_mp_val) && $special_number_mp_val >= 0 && $special_number_mp_val <= 99) {
                    $special_number_mp = (int)$special_number_mp_val;
                }

                if (!ctype_alpha($winning_numbers_json_data['letter']) || strlen($winning_numbers_json_data['letter']) !== 1) {
                    $error_message = "Mega Power: ජයග්‍රාහී අකුර නිවැරදි නැත.";
                }
                if (empty($error_message)) {
                    foreach ($main_numbers_mp as $k => $n) {
                        if ($n === null) {
                            $error_message = "Mega Power: ප්‍රධාන අංක " . ($k + 1) . " නිවැරදිව ඇතුළත් කරන්න (0-99).";
                            break;
                        }
                    }
                }
                if (empty($error_message)) {
                    if ($special_number_mp === null) {
                        $error_message = "Mega Power: විශේෂ අංකය නිවැරදිව ඇතුළත් කරන්න (0-99).";
                    }
                }

                if (empty($error_message)) {
                    $winning_numbers_json_data['numbers'] = $main_numbers_mp; // Array of 4 numbers
                    $winning_numbers_json_data['special_number'] = $special_number_mp; // Single special number
                }
                break;

            // ... (rest of the cases for other lotteries remain the same as before) ...
            case DHANA_NIDHANAYA_ID:
                $winning_numbers_json_data['main_letter'] = isset($_POST['dn_main_letter']) ? strtoupper(sanitize_input(trim($_POST['dn_main_letter']))) : '';
                $main_dn_nums = [];
                $ldc_nums = [];
                for ($i = 1; $i <= 4; $i++) {
                    $num_val = isset($_POST['dn_main_num' . $i]) ? trim(sanitize_input($_POST['dn_main_num' . $i])) : '';
                    if ($num_val !== '' && is_numeric($num_val) && $num_val >= 0 && $num_val <= 99) $main_dn_nums[] = (int)$num_val;
                    else $main_dn_nums[] = null;
                }
                for ($i = 1; $i <= 5; $i++) {
                    $num_val = isset($_POST['dn_lakshapathi_num' . $i]) ? trim(sanitize_input($_POST['dn_lakshapathi_num' . $i])) : '';
                    if ($num_val !== '' && is_numeric($num_val) && $num_val >= 0 && $num_val <= 9) $ldc_nums[] = (int)$num_val;
                    else $ldc_nums[] = null;
                }
                if (!ctype_alpha($winning_numbers_json_data['main_letter']) || strlen($winning_numbers_json_data['main_letter']) !== 1) $error_message = "ධන නිධානය: ප්‍රධාන අකුර නිවැරදි නැත.";
                foreach ($main_dn_nums as $k => $n) if ($n === null) {
                    $error_message = "ධන නිධානය: ප්‍රධාන අංක " . ($k + 1) . " නිවැරදි නැත.";
                    break;
                }
                if (empty($error_message)) foreach ($ldc_nums as $k => $n) if ($n === null) {
                    $error_message = "ධන නිධානය: LDC අංක " . ($k + 1) . " නිවැරදි නැත.";
                    break;
                }
                if (empty($error_message)) {
                    $winning_numbers_json_data['main_numbers'] = $main_dn_nums;
                    $winning_numbers_json_data['lakshapathi_double_chance_no'] = $ldc_nums;
                }
                break;

            case HANDAHANA_ID:
                $winning_numbers_json_data['lagna_name'] = isset($_POST['h_lagna_name']) ? sanitize_input(trim($_POST['h_lagna_name'])) : '';
                $lagna_nums = [];
                $dhanayogaya_nums = [];
                for ($i = 1; $i <= 4; $i++) {
                    $num_val = isset($_POST['h_lagna_num' . $i]) ? trim(sanitize_input($_POST['h_lagna_num' . $i])) : '';
                    if ($num_val !== '' && is_numeric($num_val) && $num_val >= 0 && $num_val <= 99) $lagna_nums[] = (int)$num_val;
                    else $lagna_nums[] = null;
                }
                for ($i = 1; $i <= 5; $i++) {
                    $num_val = isset($_POST['h_dhanayogaya_num' . $i]) ? trim(sanitize_input($_POST['h_dhanayogaya_num' . $i])) : '';
                    if ($num_val !== '' && is_numeric($num_val) && $num_val >= 0 && $num_val <= 9) $dhanayogaya_nums[] = (int)$num_val;
                    else $dhanayogaya_nums[] = null;
                }
                $daiwa_ankaya = isset($_POST['h_daiwa_ankaya']) ? trim(sanitize_input($_POST['h_daiwa_ankaya'])) : '';
                if (empty($winning_numbers_json_data['lagna_name'])) $error_message = "හඳහන: ලග්නය තෝරා නැත.";
                foreach ($lagna_nums as $k => $n) if ($n === null) {
                    $error_message = "හඳහන: ලග්න අංක " . ($k + 1) . " නිවැරදි නැත.";
                    break;
                }
                if (empty($error_message)) foreach ($dhanayogaya_nums as $k => $n) if ($n === null) {
                    $error_message = "හඳහන: ධන යෝගය අංක " . ($k + 1) . " නිවැරදි නැත.";
                    break;
                }
                if (empty($error_message) && ($daiwa_ankaya === '' || !is_numeric($daiwa_ankaya) || (int)$daiwa_ankaya < 0 || (int)$daiwa_ankaya > 99)) $error_message = "හඳහන: දෛව අංකය නිවැරදි නැත.";
                if (empty($error_message)) {
                    $winning_numbers_json_data['lagna_numbers'] = $lagna_nums;
                    $winning_numbers_json_data['dhanayogaya'] = $dhanayogaya_nums;
                    $winning_numbers_json_data['daiwa_ankaya'] = (int)$daiwa_ankaya;
                }
                break;

            case LAGNA_WASANA_ID:
                $winning_numbers_json_data['lagna_name'] = isset($_POST['lw_lagna_name']) ? sanitize_input(trim($_POST['lw_lagna_name'])) : '';
                for ($i = 1; $i <= 4; $i++) {
                    $num_val = isset($_POST['lw_num' . $i]) ? trim(sanitize_input($_POST['lw_num' . $i])) : '';
                    if ($num_val !== '' && is_numeric($num_val) && $num_val >= 0 && $num_val <= 99) $temp_numbers[] = (int)$num_val;
                    else $temp_numbers[] = null;
                }
                if (empty($winning_numbers_json_data['lagna_name'])) $error_message = "ලග්න වාසනාව: ලග්නය තෝරා නැත.";
                foreach ($temp_numbers as $k => $n) if ($n === null) {
                    $error_message = "ලග්න වාසනාව: අංක " . ($k + 1) . " නිවැරදි නැත.";
                    break;
                }
                if (empty($error_message)) $winning_numbers_json_data['numbers'] = $temp_numbers;
                break;

            case KAPRUKA_ID:
                $winning_numbers_json_data['letter'] = isset($_POST['k_letter']) ? strtoupper(sanitize_input(trim($_POST['k_letter']))) : '';
                for ($i = 1; $i <= 4; $i++) {
                    $num_val = isset($_POST['k_num' . $i]) ? trim(sanitize_input($_POST['k_num' . $i])) : '';
                    if ($num_val !== '' && is_numeric($num_val) && $num_val >= 0 && $num_val <= 99) $temp_numbers[] = (int)$num_val;
                    else $temp_numbers[] = null;
                }
                $special_num = isset($_POST['k_special_num']) ? trim(sanitize_input($_POST['k_special_num'])) : '';
                if (!ctype_alpha($winning_numbers_json_data['letter']) || strlen($winning_numbers_json_data['letter']) !== 1) $error_message = "කප්රුක: ජයග්‍රාහී අකුර නිවැරදි නැත.";
                foreach ($temp_numbers as $k => $n) if ($n === null) {
                    $error_message = "කප්රුක: අංක " . ($k + 1) . " නිවැරදි නැත.";
                    break;
                }
                if (empty($error_message) && ($special_num === '' || !is_numeric($special_num) || (int)$special_num < 0 || (int)$special_num > 99)) $error_message = "කප්රුක: විශේෂ අංකය නිවැරදි නැත.";
                if (empty($error_message)) {
                    $winning_numbers_json_data['numbers'] = $temp_numbers;
                    $winning_numbers_json_data['special_number'] = (int)$special_num;
                }
                break;

            case ADA_SAMPATHA_ID:
            case JAYA_SAMPATHA_ID: // Assuming Jaya Sampatha uses the same input names (as_rX_...)
                $winning_numbers_json_data = ['row1_numbers' => [], 'row2_numbers' => [], 'row3_numbers' => [], 'row3_letter' => null];
                $valid_as_js_input = true; // Flag to check overall validity

                // Row 1 processing (assuming 2 numbers for Ada Sampatha example)
                $num_r1_1 = isset($_POST['as_r1_num1']) ? trim(sanitize_input($_POST['as_r1_num1'])) : '';
                $num_r1_2 = isset($_POST['as_r1_num2']) ? trim(sanitize_input($_POST['as_r1_num2'])) : '';

                if ($num_r1_1 !== '' && is_numeric($num_r1_1)) $winning_numbers_json_data['row1_numbers'][] = (int)$num_r1_1;
                else $winning_numbers_json_data['row1_numbers'][] = null;
                if ($num_r1_2 !== '' && is_numeric($num_r1_2)) $winning_numbers_json_data['row1_numbers'][] = (int)$num_r1_2;
                else $winning_numbers_json_data['row1_numbers'][] = null;
                // Validate row 1 numbers (check for nulls if they are mandatory)
                foreach ($winning_numbers_json_data['row1_numbers'] as $n) {
                    if ($n === null) {
                        $valid_as_js_input = false;
                        $error_message = "අද/ජය සම්පත: 1 වන පේළියේ අංක නිවැරදි නැත.";
                        break;
                    }
                }


                // Row 2 processing (assuming 3 numbers)
                if ($valid_as_js_input) {
                    $num_r2_1 = isset($_POST['as_r2_num1']) ? trim(sanitize_input($_POST['as_r2_num1'])) : '';
                    $num_r2_2 = isset($_POST['as_r2_num2']) ? trim(sanitize_input($_POST['as_r2_num2'])) : '';
                    $num_r2_3 = isset($_POST['as_r2_num3']) ? trim(sanitize_input($_POST['as_r2_num3'])) : '';

                    if ($num_r2_1 !== '' && is_numeric($num_r2_1)) $winning_numbers_json_data['row2_numbers'][] = (int)$num_r2_1;
                    else $winning_numbers_json_data['row2_numbers'][] = null;
                    if ($num_r2_2 !== '' && is_numeric($num_r2_2)) $winning_numbers_json_data['row2_numbers'][] = (int)$num_r2_2;
                    else $winning_numbers_json_data['row2_numbers'][] = null;
                    if ($num_r2_3 !== '' && is_numeric($num_r2_3)) $winning_numbers_json_data['row2_numbers'][] = (int)$num_r2_3;
                    else $winning_numbers_json_data['row2_numbers'][] = null;
                    foreach ($winning_numbers_json_data['row2_numbers'] as $n) {
                        if ($n === null) {
                            $valid_as_js_input = false;
                            $error_message = "අද/ජය සම්පත: 2 වන පේළියේ අංක නිවැරදි නැත.";
                            break;
                        }
                    }
                }

                // Row 3 processing (assuming 4 numbers and 1 letter)
                if ($valid_as_js_input) {
                    $num_r3_1 = isset($_POST['as_r3_num1']) ? trim(sanitize_input($_POST['as_r3_num1'])) : '';
                    $num_r3_2 = isset($_POST['as_r3_num2']) ? trim(sanitize_input($_POST['as_r3_num2'])) : '';
                    $num_r3_3 = isset($_POST['as_r3_num3']) ? trim(sanitize_input($_POST['as_r3_num3'])) : '';
                    $num_r3_4 = isset($_POST['as_r3_num4']) ? trim(sanitize_input($_POST['as_r3_num4'])) : '';
                    $letter_r3 = isset($_POST['as_r3_letter']) ? strtoupper(sanitize_input(trim($_POST['as_r3_letter']))) : '';

                    if ($num_r3_1 !== '' && is_numeric($num_r3_1)) $winning_numbers_json_data['row3_numbers'][] = (int)$num_r3_1;
                    else $winning_numbers_json_data['row3_numbers'][] = null;
                    if ($num_r3_2 !== '' && is_numeric($num_r3_2)) $winning_numbers_json_data['row3_numbers'][] = (int)$num_r3_2;
                    else $winning_numbers_json_data['row3_numbers'][] = null;
                    if ($num_r3_3 !== '' && is_numeric($num_r3_3)) $winning_numbers_json_data['row3_numbers'][] = (int)$num_r3_3;
                    else $winning_numbers_json_data['row3_numbers'][] = null;
                    if ($num_r3_4 !== '' && is_numeric($num_r3_4)) $winning_numbers_json_data['row3_numbers'][] = (int)$num_r3_4;
                    else $winning_numbers_json_data['row3_numbers'][] = null;

                    foreach ($winning_numbers_json_data['row3_numbers'] as $n) {
                        if ($n === null) {
                            $valid_as_js_input = false;
                            $error_message = "අද/ජය සම්පත: 3 වන පේළියේ අංක නිවැරදි නැත.";
                            break;
                        }
                    }

                    if ($valid_as_js_input) { // Only check letter if numbers are valid
                        if (!empty($letter_r3) && ctype_alpha($letter_r3) && strlen($letter_r3) == 1) {
                            $winning_numbers_json_data['row3_letter'] = $letter_r3;
                        } else {
                            // If a letter is expected for this lottery but not provided or invalid
                            // Depending on "Jaya Sampatha" specific rules, this might be an error or optional
                            $error_message = "අද/ජය සම්පත: 3 වන පේළියේ අකුර නිවැරදි නැත.";
                            $valid_as_js_input = false;
                        }
                    }
                }

                if (!$valid_as_js_input) {
                    // If there was an error, clear the json_data to prevent saving partial/incorrect data
                    $winning_numbers_json_data = [];
                    // $error_message would have been set by the failing validation
                }
                break;
            default:
                $winning_numbers_json_data['numbers_string'] = isset($_POST['standard_winning_numbers']) ? sanitize_input(trim($_POST['standard_winning_numbers'])) : '';
                if (empty($winning_numbers_json_data['numbers_string'])) {
                    $error_message = 'කරුණාකර ජයග්‍රාහී අංක (Standard) ඇතුළත් කරන්න.';
                }
                break;
        }

        if (empty($error_message) && !empty($winning_numbers_json_data)) {
            $winning_numbers_final_json_string = json_encode($winning_numbers_json_data);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $error_message = "දත්ත JSON බවට පරිවර්තනය කිරීමේදී දෝෂයක් ඇතිවිය: " . json_last_error_msg();
                $winning_numbers_final_json_string = '';
            }
        } elseif (empty($error_message) && empty($winning_numbers_json_data) && $selected_lottery_id > 0) {
            $error_message = "තෝරාගත් ලොතරැයිය (ID: {$selected_lottery_id}) සඳහා ආදාන සැකසුම් දෝෂයකි.";
        }

        if (empty($error_message) && $form_data['lottery_id'] != HANDAHANA_ID && $form_data['lottery_id'] != ADA_SAMPATHA_ID && $form_data['lottery_id'] != JAYA_SAMPATHA_ID && (empty($form_data['jackpot_amount']) || !is_numeric($form_data['jackpot_amount']) || $form_data['jackpot_amount'] <= 0)) {
            // Jackpot validation, can be made more specific if certain lotteries NEVER have jackpots
            // $error_message = 'කරුණාකර වලංගු ජැක්පොට් මුදලක් ඇතුළත් කරන්න.';
        }
    }

    if (empty($error_message) && !empty($winning_numbers_final_json_string)) {
        try {
            // ... (Database Insert Logic - remains the same) ...
            $sql = "
                INSERT INTO results (lottery_id, draw_date, winning_numbers, jackpot_amount, other_prize_details)
                VALUES (:lottery_id, :draw_date, :winning_numbers, :jackpot_amount, :other_prize_details)
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':lottery_id', $form_data['lottery_id'], PDO::PARAM_INT);
            $stmt->bindParam(':draw_date', $form_data['draw_date']);
            $stmt->bindParam(':winning_numbers', $winning_numbers_final_json_string);
            $stmt->bindParam(':jackpot_amount', $form_data['jackpot_amount']);
            $stmt->bindParam(':other_prize_details', $form_data['other_prize_details']);

            if ($stmt->execute()) {
                $_SESSION['flash_message'] = 'ප්‍රතිඵලය සාර්ථකව එකතු කරන ලදී.';
                $_SESSION['flash_message_type'] = 'success';
                header('Location: manage_results.php');
                exit;
            } else {
                $error_message = 'ප්‍රතිඵලය එකතු කිරීමේදී දෝෂයක් ඇති විය. (DB execute error)';
            }
        } catch (PDOException $e) {
            // ... (Error catching - remains the same) ...
            error_log("Database error in add_result.php (insert): " . $e->getMessage() . " JSON: " . $winning_numbers_final_json_string, 0);
            if (isset($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
                $error_message = 'මෙම ලොතරැයිය සඳහා මෙම දිනුම් ඇදීමේ දිනයට අදාළ ප්‍රතිඵලයක් දැනටමත් ඇතුළත් කර ඇත.';
            } else {
                $error_message = 'ප්‍රතිඵලය එකතු කිරීමේදී දත්ත සමුදාය දෝෂයක් ඇති විය.';
            }
        }
    } elseif (empty($error_message) && empty($winning_numbers_final_json_string) && $form_data['lottery_id'] > 0) {
        if (empty($error_message)) $error_message = "ජයග්‍රාහී අංක තොරතුරු සැකසීමේදී දෝෂයකි. කරුණාකර ආදානයන් පරීක්ෂා කරන්න.";
    }
}

// --- HTML Part ---
include 'includes_admin/admin_header.php';
?>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-gray-800">නව ප්‍රතිඵලයක් එකතු කරන්න</h2>
    <a href="manage_results.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> ආපසු යන්න
    </a>
</div>

<?php if (!empty($error_message)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <p><?php echo $error_message; ?></p>
    </div>
<?php endif; ?>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <form action="add_result.php" method="post" class="p-6">
        <div class="mb-6">
            <label for="lottery_id" class="block text-gray-700 font-bold mb-2">ලොතරැයිය *</label>
            <select id="lottery_id" name="lottery_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue" required>
                <option value="">ලොතරැයිය තෝරන්න...</option>
                <?php foreach ($lotteries as $lottery): ?>
                    <option value="<?php echo $lottery['id']; ?>" <?php echo (isset($form_data['lottery_id']) && $form_data['lottery_id'] == $lottery['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($lottery['name']) . ' (' . htmlspecialchars($lottery['type']) . ')'; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-6">
            <label for="draw_date" class="block text-gray-700 font-bold mb-2">දිනුම් ඇදීම් දිනය *</label>
            <input type="date" id="draw_date" name="draw_date" value="<?php echo htmlspecialchars($form_data['draw_date']); ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue" required>
        </div>

        <div id="winning_numbers_area" class="mb-6">
            <label class="block text-gray-700 font-bold mb-2">ජයග්‍රාහී අංක / තොරතුරු *</label>

            <div id="input_type_L6N" class="specific-input-area space-y-2" style="display: none;">
                <div class="flex items-center space-x-2 flex-wrap">
                    <input type="text" name="ms_winning_letter" placeholder="අකුර" maxlength="1" value="<?php echo htmlspecialchars($form_data['ms_winning_letter'] ?? ''); ?>" class="w-16 input-style text-center uppercase">
                    <?php for ($i = 1; $i <= 6; $i++): ?>
                        <input type="text" name="ms_winning_num<?php echo $i; ?>" placeholder="අං<?php echo $i; ?>" value="<?php echo htmlspecialchars($form_data['ms_winning_num' . $i] ?? ''); ?>" maxlength="2" pattern="\d{1,2}" class="w-16 input-style text-center">
                    <?php endfor; ?>
                </div>
                <p class="input-hint">ඉංග්‍රීසි අකුරක් සහ අංක 6 ක්.</p>
            </div>

            <div id="input_type_L4N" class="specific-input-area space-y-2" style="display: none;">
                <div class="flex items-center space-x-2 flex-wrap">
                    <input type="text" name="g_letter" placeholder="අකුර" maxlength="1" value="<?php echo htmlspecialchars($form_data['g_letter'] ?? ''); ?>" class="w-16 input-style text-center uppercase">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <input type="text" name="g_num<?php echo $i; ?>" placeholder="අං<?php echo $i; ?>" value="<?php echo htmlspecialchars($form_data['g_num' . $i] ?? ''); ?>" maxlength="2" pattern="\d{1,2}" class="w-16 input-style text-center">
                    <?php endfor; ?>
                </div>
                <p class="input-hint">ඉංග්‍රීසි අකුරක් සහ අංක 4 ක්.</p>
            </div>

            <div id="input_type_MEGA_POWER" class="specific-input-area space-y-2" style="display: none;">
                <p class="font-medium text-sm">ප්‍රධාන අංක:</p>
                <div class="flex items-center space-x-2 flex-wrap">
                    <input type="text" name="mp_letter" placeholder="අකුර" maxlength="1" value="<?php echo htmlspecialchars($form_data['mp_letter'] ?? ''); ?>" class="w-16 input-style text-center uppercase">
                    <?php for ($i = 1; $i <= 4; $i++): // Changed to 4 main numbers 
                    ?>
                        <input type="text" name="mp_num<?php echo $i; ?>" placeholder="අං<?php echo $i; ?>" value="<?php echo htmlspecialchars($form_data['mp_num' . $i] ?? ''); ?>" maxlength="2" pattern="\d{1,2}" class="w-16 input-style text-center">
                    <?php endfor; ?>
                </div>
                <p class="font-medium text-sm mt-2">විශේෂ අංකය (Super Number):</p>
                <div class="flex items-center space-x-2 flex-wrap">
                    <input type="text" name="mp_special_num1" placeholder="වි.අං" value="<?php echo htmlspecialchars($form_data['mp_special_num1'] ?? ''); ?>" maxlength="2" pattern="\d{1,2}" class="w-20 input-style text-center">
                </div>
                <p class="input-hint">ඉංග්‍රීසි අකුරක්, ප්‍රධාන අංක 4ක්, සහ එක් විශේෂ අංකයක්.</p>
            </div>

            <div id="input_type_DN" class="specific-input-area space-y-3" style="display: none;">
                <p class="font-medium">ප්‍රධාන දිනුම:</p>
                <div class="flex items-center space-x-2 flex-wrap">
                    <input type="text" name="dn_main_letter" placeholder="අකුර" maxlength="1" value="<?php echo htmlspecialchars($form_data['dn_main_letter'] ?? ''); ?>" class="w-16 input-style text-center uppercase">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <input type="text" name="dn_main_num<?php echo $i; ?>" placeholder="අං<?php echo $i; ?>" value="<?php echo htmlspecialchars($form_data['dn_main_num' . $i] ?? ''); ?>" maxlength="2" pattern="\d{1,2}" class="w-16 input-style text-center">
                    <?php endfor; ?>
                </div>
                <p class="font-medium mt-2">ලක්ෂපති ද්විත්ව අවස්ථා අංකය:</p>
                <div class="flex items-center space-x-2 flex-wrap">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <input type="text" name="dn_lakshapathi_num<?php echo $i; ?>" placeholder="අං<?php echo $i; ?>" value="<?php echo htmlspecialchars($form_data['dn_lakshapathi_num' . $i] ?? ''); ?>" maxlength="1" pattern="\d{1}" class="w-12 input-style text-center">
                    <?php endfor; ?>
                </div>
            </div>

            <div id="input_type_HANDAHANA" class="specific-input-area space-y-3" style="display: none;">
                <p class="font-medium">ලග්නය සහ අංක:</p>
                <div class="flex items-center space-x-2 flex-wrap">
                    <select name="h_lagna_name" class="input-style" style="min-width: 100px;">
                        <option value="">ලග්නය..</option>
                        <?php /* Options populated by JS */ ?>
                    </select>
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <input type="text" name="h_lagna_num<?php echo $i; ?>" placeholder="අං<?php echo $i; ?>" value="<?php echo htmlspecialchars($form_data['h_lagna_num' . $i] ?? ''); ?>" maxlength="2" pattern="\d{1,2}" class="w-16 input-style text-center">
                    <?php endfor; ?>
                </div>
                <p class="font-medium mt-2">ධන යෝගය:</p>
                <div class="flex items-center space-x-2 flex-wrap">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <input type="text" name="h_dhanayogaya_num<?php echo $i; ?>" placeholder="අං<?php echo $i; ?>" value="<?php echo htmlspecialchars($form_data['h_dhanayogaya_num' . $i] ?? ''); ?>" maxlength="1" pattern="\d{1}" class="w-12 input-style text-center">
                    <?php endfor; ?>
                </div>
                <p class="font-medium mt-2">දෛව අංකය:</p>
                <input type="text" name="h_daiwa_ankaya" placeholder="අංකය" maxlength="2" pattern="\d{1,2}" value="<?php echo htmlspecialchars($form_data['h_daiwa_ankaya'] ?? ''); ?>" class="w-16 input-style text-center">
            </div>

            <div id="input_type_LW" class="specific-input-area space-y-2" style="display: none;">
                <div class="flex items-center space-x-2 flex-wrap">
                    <select name="lw_lagna_name" class="input-style" style="min-width: 100px;">
                        <option value="">ලග්නය..</option>
                        <?php /* Options populated by JS */ ?>
                    </select>
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <input type="text" name="lw_num<?php echo $i; ?>" placeholder="අං<?php echo $i; ?>" value="<?php echo htmlspecialchars($form_data['lw_num' . $i] ?? ''); ?>" maxlength="2" pattern="\d{1,2}" class="w-16 input-style text-center">
                    <?php endfor; ?>
                </div>
                <p class="input-hint">ලග්නය සහ අංක 4 ක්.</p>
            </div>

            <div id="input_type_KAPRUKA" class="specific-input-area space-y-2" style="display: none;">
                <div class="flex items-center space-x-2 flex-wrap">
                    <input type="text" name="k_letter" placeholder="අකුර" maxlength="1" value="<?php echo htmlspecialchars($form_data['k_letter'] ?? ''); ?>" class="w-16 input-style text-center uppercase">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <input type="text" name="k_num<?php echo $i; ?>" placeholder="අං<?php echo $i; ?>" value="<?php echo htmlspecialchars($form_data['k_num' . $i] ?? ''); ?>" maxlength="2" pattern="\d{1,2}" class="w-16 input-style text-center">
                    <?php endfor; ?>
                    <span class="mx-1 text-gray-500">+</span>
                    <input type="text" name="k_special_num" placeholder="විශේෂ අංකය" value="<?php echo htmlspecialchars($form_data['k_special_num'] ?? ''); ?>" maxlength="2" pattern="\d{1,2}" class="w-20 input-style text-center">
                </div>
                <p class="input-hint">අකුර, අංක 4 සහ විශේෂ අංකය.</p>
            </div>

            <div id="input_type_AS_JS" class="specific-input-area space-y-3" style="display: none;">
                <p class="input-hint">අද සම්පත / ජය සම්පත ආකෘතියට අනුව අංක සහ අකුරු ඇතුළත් කරන්න.</p>
                <div class="flex items-center space-x-2 flex-wrap">
                    <label class="text-sm w-16">පේළිය 1:</label>
                    <input type="text" name="as_r1_num1" placeholder="අං1" value="<?php echo htmlspecialchars($form_data['as_r1_num1'] ?? ''); ?>" class="w-12 input-style text-center">
                    <input type="text" name="as_r1_num2" placeholder="අං2" value="<?php echo htmlspecialchars($form_data['as_r1_num2'] ?? ''); ?>" class="w-12 input-style text-center">
                </div>
                <div class="flex items-center space-x-2 flex-wrap">
                    <label class="text-sm w-16">පේළිය 2:</label>
                    <input type="text" name="as_r2_num1" placeholder="අං1" value="<?php echo htmlspecialchars($form_data['as_r2_num1'] ?? ''); ?>" class="w-12 input-style text-center">
                    <input type="text" name="as_r2_num2" placeholder="අං2" value="<?php echo htmlspecialchars($form_data['as_r2_num2'] ?? ''); ?>" class="w-12 input-style text-center">
                    <input type="text" name="as_r2_num3" placeholder="අං3" value="<?php echo htmlspecialchars($form_data['as_r2_num3'] ?? ''); ?>" class="w-12 input-style text-center">
                </div>
                <div class="flex items-center space-x-2 flex-wrap">
                    <label class="text-sm w-16">පේළිය 3:</label>
                    <input type="text" name="as_r3_num1" placeholder="අං1" value="<?php echo htmlspecialchars($form_data['as_r3_num1'] ?? ''); ?>" class="w-12 input-style text-center">
                    <input type="text" name="as_r3_num2" placeholder="අං2" value="<?php echo htmlspecialchars($form_data['as_r3_num2'] ?? ''); ?>" class="w-12 input-style text-center">
                    <input type="text" name="as_r3_num3" placeholder="අං3" value="<?php echo htmlspecialchars($form_data['as_r3_num3'] ?? ''); ?>" class="w-12 input-style text-center">
                    <input type="text" name="as_r3_num4" placeholder="අං4" value="<?php echo htmlspecialchars($form_data['as_r3_num4'] ?? ''); ?>" class="w-12 input-style text-center">
                    <input type="text" name="as_r3_letter" placeholder="අකුර" value="<?php echo htmlspecialchars($form_data['as_r3_letter'] ?? ''); ?>" maxlength="1" class="w-12 input-style text-center uppercase">
                </div>
            </div>

            <div id="input_type_STANDARD" class="specific-input-area space-y-2" style="display: none;">
                <input type="text" name="standard_winning_numbers" value="<?php echo htmlspecialchars($form_data['standard_winning_numbers'] ?? ''); ?>" placeholder="උදා: 01-12-23-34-45-66" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue">
                <p class="input-hint">ජයග්‍රාහී අංක කැඩි ඉරි (-) මගින් වෙන් කරන්න, හෝ ලොතරැයි පත්‍රිකාවේ ඇති පරිදි ඇතුළත් කරන්න.</p>
            </div>

            <p id="select_lottery_prompt_wg" class="text-sm text-gray-500 mt-1">කරුණාකර ලොතරැයියක් තෝරා අදාළ ආදාන ක්ෂේත්‍ර පුරවන්න.</p>
        </div>

        <div class="mb-6">
            <label for="jackpot_amount" class="block text-gray-700 font-bold mb-2">ජැක්පොට් මුදල (රුපියල්)</label>
            <input type="number" id="jackpot_amount" name="jackpot_amount" value="<?php echo htmlspecialchars($form_data['jackpot_amount']); ?>" step="0.01" min="0" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue">
            <p class="text-sm text-gray-500 mt-1">සමහර ලොතරැයි සඳහා මෙය අදාළ නොවිය හැක. අදාළ නැත්නම් 0 ලෙස හෝ හිස්ව තබන්න.</p>
        </div>

        <div class="mb-6">
            <label for="other_prize_details" class="block text-gray-700 font-bold mb-2">වෙනත් ත්‍යාග විස්තර (JSON ආකෘතිය)</label>
            <textarea id="other_prize_details" name="other_prize_details" rows="3" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue"><?php echo htmlspecialchars($form_data['other_prize_details']); ?></textarea>
            <p class="text-sm text-gray-500 mt-1">JSON ආකෘතියෙන් - උදා: {"second_prize": 1000000, "third_prize": 50000}</p>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-primaryGreen hover:bg-green-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline">
                <i class="fas fa-save mr-2"></i> සුරකින්න
            </button>
        </div>
    </form>
</div>

<style>
    .input-style {
        padding: 0.5rem 0.75rem;
        border-width: 1px;
        border-radius: 0.5rem;
        border-color: #D1D5DB;
    }

    .input-style:focus {
        outline: 2px solid transparent;
        outline-offset: 2px;
        border-color: #0A3D62;
        box-shadow: 0 0 0 2px #0A3D62;
    }

    .input-hint {
        font-size: 0.875rem;
        color: #6B7280;
        margin-top: 0.25rem;
    }

    .specific-input-area {
        padding: 10px;
        border: 1px solid #F3F4F6;
        border-radius: 5px;
        margin-bottom: 10px;
        background-color: #F9FAFB;
    }

    .uppercase {
        text-transform: uppercase;
    }

    .flex-wrap {
        flex-wrap: wrap;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lotterySelect = document.getElementById('lottery_id');
        const winningNumbersArea = document.getElementById('winning_numbers_area');
        const allSpecificInputAreas = winningNumbersArea.querySelectorAll('.specific-input-area');
        const promptMessage = document.getElementById('select_lottery_prompt_wg');

        const LOTTERY_IDS = {
            MAHAJANA_SAMPATHA: '<?php echo MAHAJANA_SAMPATHA_ID; ?>',
            GOVISETHA: '<?php echo GOVISETHA_ID; ?>',
            MEGA_POWER: '<?php echo MEGA_POWER_ID; ?>', // This is the one we are focusing on
            ADA_KOTIPATHI: '<?php echo ADA_KOTIPATHI_ID; ?>',
            DHANA_NIDHANAYA: '<?php echo DHANA_NIDHANAYA_ID; ?>',
            HANDAHANA: '<?php echo HANDAHANA_ID; ?>',
            ADA_SAMPATHA: '<?php echo ADA_SAMPATHA_ID; ?>',
            NLB_JAYA: '<?php echo NLB_JAYA_ID; ?>',
            LAGNA_WASANA: '<?php echo LAGNA_WASANA_ID; ?>',
            SHANIDA: '<?php echo SHANIDA_ID; ?>',
            SUPER_BALL: '<?php echo SUPER_BALL_ID; ?>',
            KAPRUKA: '<?php echo KAPRUKA_ID; ?>',
            SUPIRI_DHANA_SAMPATHA: '<?php echo SUPIRI_DHANA_SAMPATHA_ID; ?>',
            JAYA_SAMPATHA: '<?php echo JAYA_SAMPATHA_ID; ?>',
            JAYODA: '<?php echo JAYODA_ID; ?>'
        };

        const lagnaOptions = [{
                value: '',
                text: 'ලග්නය තෝරන්න..'
            }, // Added default empty option
            {
                value: 'mesha',
                text: 'මේෂ'
            }, {
                value: 'vrushabha',
                text: 'වෘෂභ'
            },
            {
                value: 'mithuna',
                text: 'මිථුන'
            }, {
                value: 'kataka',
                text: 'කටක'
            },
            {
                value: 'sinha',
                text: 'සිංහ'
            }, {
                value: 'kanya',
                text: 'කන්‍යා'
            },
            {
                value: 'thula',
                text: 'තුලා'
            }, {
                value: 'vrushchika',
                text: 'වෘශ්චික'
            },
            {
                value: 'dhanu',
                text: 'ධනු'
            }, {
                value: 'makara',
                text: 'මකර'
            },
            {
                value: 'kumbha',
                text: 'කුම්භ'
            }, {
                value: 'meena',
                text: 'මීන'
            }
        ];

        function populateLagnaDropdown(selectElementName) {
            const selectElement = document.querySelector(`select[name="${selectElementName}"]`);
            if (selectElement) {
                const currentValue = selectElement.dataset.currentValue || (selectElement.options.length > 0 ? selectElement.options[0].value : ''); // Preserve or use default
                selectElement.innerHTML = ''; // Clear all existing options

                lagnaOptions.forEach(opt => {
                    const option = document.createElement('option');
                    option.value = opt.value;
                    option.textContent = opt.text;
                    selectElement.appendChild(option);
                });
                // Try to re-select the value if it was set (e.g. from PHP $form_data)
                const phpValue = selectElement.dataset.currentValue; // Get value passed from PHP
                if (phpValue) {
                    selectElement.value = phpValue;
                } else {
                    selectElement.value = ''; // Default to "ලග්නය තෝරන්න.."
                }
            }
        }

        // Populate Lagna dropdowns and store their initial values if set by PHP
        const hLagnaSelect = document.querySelector('select[name="h_lagna_name"]');
        if (hLagnaSelect) hLagnaSelect.dataset.currentValue = "<?php echo htmlspecialchars($form_data['h_lagna_name'] ?? ''); ?>";
        populateLagnaDropdown('h_lagna_name');

        const lwLagnaSelect = document.querySelector('select[name="lw_lagna_name"]');
        if (lwLagnaSelect) lwLagnaSelect.dataset.currentValue = "<?php echo htmlspecialchars($form_data['lw_lagna_name'] ?? ''); ?>";
        populateLagnaDropdown('lw_lagna_name');


        function setFieldsRequired(areaId, required) {
            const area = document.getElementById(areaId);
            if (area) {
                const inputs = area.querySelectorAll('input[type="text"], select');
                inputs.forEach(input => {
                    input.required = required;
                    input.disabled = !required;
                    // if (!required) { input.value = ''; } // Optional: clear values
                });
            }
        }

        function toggleInputFields() {
            const selectedLotteryId = lotterySelect.value;
            let activeAreaId = null;

            allSpecificInputAreas.forEach(area => {
                area.style.display = 'none';
                setFieldsRequired(area.id, false);
            });
            promptMessage.style.display = 'block';

            if (selectedLotteryId) {
                promptMessage.style.display = 'none';
                switch (selectedLotteryId) {
                    case LOTTERY_IDS.MAHAJANA_SAMPATHA:
                    case LOTTERY_IDS.SUPIRI_DHANA_SAMPATHA:
                        activeAreaId = 'input_type_L6N';
                        break;
                    case LOTTERY_IDS.GOVISETHA:
                    case LOTTERY_IDS.ADA_KOTIPATHI:
                    case LOTTERY_IDS.NLB_JAYA:
                    case LOTTERY_IDS.SUPER_BALL:
                    case LOTTERY_IDS.SHANIDA:
                    case LOTTERY_IDS.JAYODA:
                        activeAreaId = 'input_type_L4N';
                        break;
                    case LOTTERY_IDS.MEGA_POWER: // Correctly maps to the updated HTML ID
                        activeAreaId = 'input_type_MEGA_POWER';
                        break;
                    case LOTTERY_IDS.DHANA_NIDHANAYA:
                        activeAreaId = 'input_type_DN';
                        break;
                    case LOTTERY_IDS.HANDAHANA:
                        activeAreaId = 'input_type_HANDAHANA';
                        break;
                    case LOTTERY_IDS.LAGNA_WASANA:
                        activeAreaId = 'input_type_LW';
                        break;
                    case LOTTERY_IDS.KAPRUKA:
                        activeAreaId = 'input_type_KAPRUKA';
                        break;
                    case LOTTERY_IDS.ADA_SAMPATHA:
                    case LOTTERY_IDS.JAYA_SAMPATHA:
                        activeAreaId = 'input_type_AS_JS';
                        break;
                    default:
                        activeAreaId = 'input_type_STANDARD';
                        break;
                }

                if (activeAreaId) {
                    const activeArea = document.getElementById(activeAreaId);
                    if (activeArea) {
                        activeArea.style.display = 'block';
                        setFieldsRequired(activeAreaId, true);
                    } else {
                        document.getElementById('input_type_STANDARD').style.display = 'block';
                        setFieldsRequired('input_type_STANDARD', true);
                    }
                }
            }
        }

        lotterySelect.addEventListener('change', toggleInputFields);
        toggleInputFields();
    });
</script>

<?php
include 'includes_admin/admin_footer.php';
?>