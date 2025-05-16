<?php
/**
 * Helper Functions for lotteryLK website
 */
if (!defined('MAHAJANA_SAMPATHA_ID')) define('MAHAJANA_SAMPATHA_ID', 1);
if (!defined('GOVISETHA_ID')) define('GOVISETHA_ID', 2);
if (!defined('MEGA_POWER_ID')) define('MEGA_POWER_ID', 3);
if (!defined('ADA_KOTIPATHI_ID')) define('ADA_KOTIPATHI_ID', 4);
if (!defined('DHANA_NIDHANAYA_ID')) define('DHANA_NIDHANAYA_ID', 7);
if (!defined('HANDAHANA_ID')) define('HANDAHANA_ID', 9);
if (!defined('ADA_SAMPATHA_ID')) define('ADA_SAMPATHA_ID', 10); // This was the missing one in the error
if (!defined('NLB_JAYA_ID')) define('NLB_JAYA_ID', 13);
if (!defined('LAGNA_WASANA_ID')) define('LAGNA_WASANA_ID', 14);
if (!defined('SHANIDA_ID')) define('SHANIDA_ID', 15);
if (!defined('SUPER_BALL_ID')) define('SUPER_BALL_ID', 16);
if (!defined('KAPRUKA_ID')) define('KAPRUKA_ID', 17);
if (!defined('SUPIRI_DHANA_SAMPATHA_ID')) define('SUPIRI_DHANA_SAMPATHA_ID', 18);
if (!defined('JAYA_SAMPATHA_ID')) define('JAYA_SAMPATHA_ID', 19);
if (!defined('JAYODA_ID')) define('JAYODA_ID', 20);
if (!defined('SASIRI_ID')) define('SASIRI_ID', 21);
/**
 * Sanitize user input
 * @param string $data Input data to sanitize
 * @return string Sanitized data
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Get all lotteries 
 * @param PDO $pdo Database connection
 * @param string $type Filter by lottery type (optional)
 * @return array Array of lottery data
 */
function get_all_lotteries($pdo, $type = null) {
    try {
        if ($type) {
            $stmt = $pdo->prepare('SELECT * FROM lotteries WHERE type = :type ORDER BY name');
            $stmt->bindParam(':type', $type, PDO::PARAM_STR);
            $stmt->execute();
        } else {
            $stmt = $pdo->query('SELECT * FROM lotteries ORDER BY name');
        }
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Database error in get_all_lotteries(): " . $e->getMessage(), 0);
        return [];
    }
}

/**
 * Get latest lottery results
 * @param PDO $pdo Database connection
 * @param int $limit Number of results to return
 * @return array Array of result data
 */
function get_latest_results($pdo, $limit = 5) {
    try {
        $stmt = $pdo->prepare('
            SELECT r.*, l.name as lottery_name, l.type 
            FROM results r
            JOIN lotteries l ON r.lottery_id = l.id
            ORDER BY r.draw_date DESC, r.published_at DESC
            LIMIT :limit
        ');
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Database error in get_latest_results(): " . $e->getMessage(), 0);
        return [];
    }
}

/**
 * Get latest news articles
 * @param PDO $pdo Database connection
 * @param int $limit Number of news articles to return
 * @return array Array of news article data
 */
function get_latest_news($pdo, $limit = 3) {
    try {
        $stmt = $pdo->prepare('
            SELECT * FROM news_articles
            ORDER BY published_date DESC
            LIMIT :limit
        ');
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Database error in get_latest_news(): " . $e->getMessage(), 0);
        return [];
    }
}

/**
 * Get a single lottery result by ID
 * @param PDO $pdo Database connection
 * @param int $id Result ID
 * @return array|bool Result data or false if not found
 */
function get_result_by_id($pdo, $id) {
    try {
        $stmt = $pdo->prepare('
            SELECT r.*, l.name as lottery_name, l.type 
            FROM results r
            JOIN lotteries l ON r.lottery_id = l.id
            WHERE r.id = :id
        ');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Database error in get_result_by_id(): " . $e->getMessage(), 0);
        return false;
    }
}

/**
 * Get lottery results by lottery ID
 * @param PDO $pdo Database connection
 * @param int $lottery_id Lottery ID
 * @param int $limit Number of results to return
 * @return array Array of result data
 */
function get_results_by_lottery($pdo, $lottery_id, $limit = 10) {
    try {
        $stmt = $pdo->prepare('
            SELECT r.*, l.name as lottery_name, l.type 
            FROM results r
            JOIN lotteries l ON r.lottery_id = l.id
            WHERE r.lottery_id = :lottery_id
            ORDER BY r.draw_date DESC, r.published_at DESC
            LIMIT :limit
        ');
        $stmt->bindParam(':lottery_id', $lottery_id, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Database error in get_results_by_lottery(): " . $e->getMessage(), 0);
        return [];
    }
}

/**
 * Get a single news article by ID
 * @param PDO $pdo Database connection
 * @param int $id News article ID
 * @return array|bool News article data or false if not found
 */
function get_news_by_id($pdo, $id) {
    try {
        $stmt = $pdo->prepare('SELECT * FROM news_articles WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Database error in get_news_by_id(): " . $e->getMessage(), 0);
        return false;
    }
}

/**
 * Format winning numbers for display
 * @param string $winning_numbers Comma-separated winning numbers
 * @return string HTML formatted winning numbers
 */
function format_winning_numbers($lottery_id, $winning_numbers_json)
{
    $data = json_decode($winning_numbers_json, true);
    $html_output = '';

    // Fallback logic ... (කලින් තිබූ පරිදිම)
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($data) || empty($data)) {
        // ... (Fallback code from previous response) ...
        $numbers_fallback = explode('-', $winning_numbers_json);
        if (count($numbers_fallback) > 0 && !empty(trim($numbers_fallback[0]))) {
            $html_output .= '<div class="flex flex-wrap justify-center items-center gap-2 py-2">';
            foreach ($numbers_fallback as $part) {
                $trimmed_part = trim($part);
                if (empty($trimmed_part) && $trimmed_part !== '0') continue;

                $is_letter = ctype_alpha($trimmed_part) && strlen($trimmed_part) == 1;
                // For these specific lotteries, numbers should not have leading zeros if not inherent
                $should_remove_leading_zero_fallback = in_array($lottery_id, [MAHAJANA_SAMPATHA_ID, ADA_SAMPATHA_ID, JAYA_SAMPATHA_ID, NLB_JAYA_ID]);

                $display_val = $is_letter ? htmlspecialchars(strtoupper($trimmed_part))
                    : (is_numeric($trimmed_part) ?
                        ($should_remove_leading_zero_fallback ? htmlspecialchars((string)(int)$trimmed_part) : sprintf("%02d", (int)$trimmed_part))
                        : htmlspecialchars($trimmed_part));

                $ball_class = $is_letter ? 'bg-accentYellow text-primaryBlue font-bold' : 'bg-primaryBlue text-white';
                $html_output .= '<span class="lottery-ball ' . $ball_class . '">' . $display_val . '</span>';
            }
            $html_output .= '</div>';
        } else {
            $html_output .= '<div class="flex justify-center items-center py-2">';
            $html_output .= '<span class="lottery-ball bg-gray-400 text-white" title="Lottery ID">' . htmlspecialchars((string)$lottery_id) . '</span>';
            $html_output .= '</div>';
        }
        return $html_output;
    }


    $html_output = '<div class="flex flex-col items-center gap-1 text-sm py-2">';

    switch ($lottery_id) {
        case MAHAJANA_SAMPATHA_ID: // No leading zeros for numbers
            // case SUPIRI_DHANA_SAMPATHA_ID: // Keep leading zeros for this one as per previous logic
            if (isset($data['letter']) && ctype_alpha($data['letter']) && isset($data['numbers']) && is_array($data['numbers']) && count($data['numbers']) == 6) {
                $html_output .= '<div class="flex items-center gap-1.5">';
                $html_output .= '<span class="lottery-ball bg-accentYellow text-primaryBlue font-bold">' . htmlspecialchars($data['letter']) . '</span>';
                foreach ($data['numbers'] as $num) {
                    if (is_numeric($num)) $html_output .= '<span class="lottery-ball bg-primaryBlue text-white">' . htmlspecialchars((string)(int)$num) . '</span>'; // No leading zero
                }
                $html_output .= '</div>';
            } else {
                $html_output .= '<span class="text-red-600 text-xs">දත්ත දෝෂයකි (ID: ' . htmlspecialchars($lottery_id) . ')</span>';
            }
            break;

        case SUPIRI_DHANA_SAMPATHA_ID: // Letter + 6 Nums (Keep leading zeros for this)
            if (isset($data['letter']) && ctype_alpha($data['letter']) && isset($data['numbers']) && is_array($data['numbers']) && count($data['numbers']) == 6) {
                $html_output .= '<div class="flex items-center gap-1.5">';
                $html_output .= '<span class="lottery-ball bg-accentYellow text-primaryBlue font-bold">' . htmlspecialchars($data['letter']) . '</span>';
                foreach ($data['numbers'] as $num) {
                    if (is_numeric($num)) $html_output .= '<span class="lottery-ball bg-purple-500 text-white">' . sprintf("%02d", (int)$num) . '</span>'; // With leading zero
                }
                $html_output .= '</div>';
            } else {
                $html_output .= '<span class="text-red-600 text-xs">දත්ත දෝෂයකි (ID: ' . htmlspecialchars($lottery_id) . ')</span>';
            }
            break;


        case NLB_JAYA_ID: // Letter + 4 Nums (No leading zeros for numbers)
            if (isset($data['letter']) && ctype_alpha($data['letter']) && isset($data['numbers']) && is_array($data['numbers']) && count($data['numbers']) == 4) {
                $html_output .= '<div class="flex items-center gap-1.5">';
                $html_output .= '<span class="lottery-ball bg-accentYellow text-primaryBlue font-bold">' . htmlspecialchars($data['letter']) . '</span>';
                foreach ($data['numbers'] as $num) {
                    if (is_numeric($num)) $html_output .= '<span class="lottery-ball bg-primaryGreen text-white">' . htmlspecialchars((string)(int)$num) . '</span>'; // No leading zero
                }
                $html_output .= '</div>';
            } else {
                $html_output .= '<span class="text-red-600 text-xs">දත්ත දෝෂයකි (ID: ' . htmlspecialchars($lottery_id) . ')</span>';
            }
            break;

        // For these, keep leading zeros as per previous general L4N logic
        case GOVISETHA_ID:
        case ADA_KOTIPATHI_ID:
        case SUPER_BALL_ID:
        case SHANIDA_ID:
        case JAYODA_ID:
            if (isset($data['letter']) && ctype_alpha($data['letter']) && isset($data['numbers']) && is_array($data['numbers']) && count($data['numbers']) == 4) {
                $html_output .= '<div class="flex items-center gap-1.5">';
                $html_output .= '<span class="lottery-ball bg-accentYellow text-primaryBlue font-bold">' . htmlspecialchars($data['letter']) . '</span>';
                foreach ($data['numbers'] as $num) {
                    if (is_numeric($num)) $html_output .= '<span class="lottery-ball bg-primaryGreen text-white">' . sprintf("%02d", (int)$num) . '</span>'; // With leading zero
                }
                $html_output .= '</div>';
            } else {
                $html_output .= '<span class="text-red-600 text-xs">දත්ත දෝෂයකි (ID: ' . htmlspecialchars($lottery_id) . ')</span>';
            }
            break;

        case MEGA_POWER_ID: // Letter + 4 Main Nums + 1 Special Num
            if (
                isset($data['letter']) && ctype_alpha($data['letter']) &&
                isset($data['numbers']) && is_array($data['numbers']) && count($data['numbers']) == 4 &&
                isset($data['special_number']) && is_numeric($data['special_number'])
            ) {
                $html_output .= '<div class="flex flex-col items-center gap-1">';
                $html_output .= '<div class="flex items-center gap-1.5">';
                $html_output .= '<span class="lottery-ball bg-blue-500 text-white font-bold">' . htmlspecialchars($data['letter']) . '</span>';
                if (isset($data['numbers'][0]) && is_numeric($data['numbers'][0]))
                    $html_output .= '<span class="lottery-ball bg-red-500 text-white">' . sprintf("%02d", (int)$data['numbers'][0]) . '</span>';
                for ($i = 1; $i < 4; $i++) {
                    if (isset($data['numbers'][$i]) && is_numeric($data['numbers'][$i]))
                        $html_output .= '<span class="lottery-ball bg-yellow-400 text-black">' . sprintf("%02d", (int)$data['numbers'][$i]) . '</span>';
                }
                $html_output .= '</div>';
                $html_output .= '<div class="flex items-center gap-1.5 mt-1">';
                $html_output .= '<span class="text-xs font-semibold mr-1">Super:</span>';
                $html_output .= '<span class="lottery-ball bg-yellow-400 text-black">' . sprintf("%02d", (int)$data['special_number']) . '</span>';
                $html_output .= '</div></div>';
            } else {
                $html_output .= '<span class="text-red-600 text-xs">දත්ත දෝෂයකි (ID: ' . htmlspecialchars($lottery_id) . ')</span>';
            }
            break;

        case DHANA_NIDHANAYA_ID:
            $html_output .= '<div class="space-y-1 text-xs text-center">';
            if (isset($data['main_letter'], $data['main_numbers']) && is_array($data['main_numbers']) && count($data['main_numbers']) == 4) {
                $html_output .= '<div class="flex items-center justify-center gap-1"><span class="font-semibold mr-1">ප්‍රධාන:</span><span class="lottery-ball bg-accentYellow text-primaryBlue font-bold">' . htmlspecialchars($data['main_letter']) . '</span>';
                foreach ($data['main_numbers'] as $num) if (is_numeric($num)) $html_output .= '<span class="lottery-ball bg-primaryBlue text-white">' . htmlspecialchars((string)(int)$num) . '</span>'; // No leading zero
                $html_output .= '</div>';
            }
            if (isset($data['lakshapathi_double_chance_no']) && is_array($data['lakshapathi_double_chance_no']) && count($data['lakshapathi_double_chance_no']) == 5) {
                $html_output .= '<div class="flex items-center justify-center gap-1 mt-0.5"><span class="font-semibold mr-1">LDC:</span>';
                foreach ($data['lakshapathi_double_chance_no'] as $num) if (is_numeric($num)) $html_output .= '<span class="lottery-ball bg-primaryGreen text-white" style="width:28px; height:28px; font-size:0.7rem;">' . htmlspecialchars((string)(int)$num) . '</span>'; // No leading zero
                $html_output .= '</div>';
            }
            $html_output .= '</div>';
            break;

        case HANDAHANA_ID:
            $html_output .= '<div class="space-y-0.5 text-xs text-center">';
            if (isset($data['lagna_name'], $data['lagna_numbers']) && is_array($data['lagna_numbers']) && count($data['lagna_numbers']) == 4) {
                $html_output .= '<div class="flex items-center justify-center gap-1"><span class="font-semibold mr-1">ලග්නය: <span class="text-purple-700 bg-purple-100 px-1.5 py-0.5 rounded">' . htmlspecialchars(ucfirst($data['lagna_name'])) . '</span></span>';
                foreach ($data['lagna_numbers'] as $num) if (is_numeric($num)) $html_output .= '<span class="lottery-ball bg-purple-500 text-white">' . htmlspecialchars((string)(int)$num) . '</span>'; // No leading zero
                $html_output .= '</div>';
            }
            if (isset($data['dhanayogaya']) && is_array($data['dhanayogaya']) && count($data['dhanayogaya']) == 5) {
                $html_output .= '<div class="flex items-center justify-center gap-1 mt-0.5"><span class="font-semibold mr-1">ධන.යෝ:</span>';
                foreach ($data['dhanayogaya'] as $num) if (is_numeric($num)) $html_output .= '<span class="lottery-ball bg-yellow-500 text-white" style="width:28px; height:28px; font-size:0.7rem;">' . htmlspecialchars((string)(int)$num) . '</span>'; // No leading zero
                $html_output .= '</div>';
            }
            if (isset($data['daiwa_ankaya']) && is_numeric($data['daiwa_ankaya'])) {
                $html_output .= '<div class="flex items-center justify-center gap-1 mt-0.5"><span class="font-semibold mr-1">දෛ.අං:</span><span class="lottery-ball bg-red-600 text-white font-bold">' . htmlspecialchars((string)(int)$data['daiwa_ankaya']) . '</span></div>'; // No leading zero
            }
            $html_output .= '</div>';
            break;

        case LAGNA_WASANA_ID: // Keep leading zeros for consistency of ball appearance if they are 00-99 range
            if (isset($data['lagna_name']) && !empty($data['lagna_name']) && isset($data['numbers']) && is_array($data['numbers']) && count($data['numbers']) == 4) {
                $html_output .= '<div class="flex items-center justify-center gap-1.5"><span class="font-semibold bg-pink-100 text-pink-700 px-1.5 py-0.5 rounded text-xs">' . htmlspecialchars(ucfirst($data['lagna_name'])) . '</span>';
                foreach ($data['numbers'] as $num) if (is_numeric($num)) $html_output .= '<span class="lottery-ball bg-pink-500 text-white">' . sprintf("%02d", (int)$num) . '</span>'; // Keep leading zero here
                $html_output .= '</div>';
            } else {
                $html_output .= '<span class="text-red-600 text-xs">දත්ත දෝෂයකි (ID: ' . htmlspecialchars($lottery_id) . ')</span>';
            }
            break;

        case KAPRUKA_ID: // Keep leading zeros for numbers, special number
            if (isset($data['letter']) && ctype_alpha($data['letter']) && isset($data['numbers']) && is_array($data['numbers']) && count($data['numbers']) == 4 && isset($data['special_number']) && is_numeric($data['special_number'])) {
                $html_output .= '<div class="flex items-center justify-center gap-1.5">';
                $html_output .= '<span class="lottery-ball bg-accentYellow text-primaryBlue font-bold">' . htmlspecialchars($data['letter']) . '</span>';
                foreach ($data['numbers'] as $num) if (is_numeric($num)) $html_output .= '<span class="lottery-ball bg-green-600 text-white">' . sprintf("%02d", (int)$num) . '</span>'; // Keep leading zero
                $html_output .= '<span class="font-bold mx-0.5 text-gray-600 text-lg">+</span><span class="lottery-ball bg-black text-white font-bold">' . sprintf("%02d", (int)$data['special_number']) . '</span>'; // Keep leading zero
                $html_output .= '</div>';
            } else {
                $html_output .= '<span class="text-red-600 text-xs">දත්ත දෝෂයකි (ID: ' . htmlspecialchars($lottery_id) . ')</span>';
            }
            break;

        case ADA_SAMPATHA_ID:
        case JAYA_SAMPATHA_ID:
            // For these complex multi-row ones, numbers are usually single digits or directly represented.
            $html_output .= '<div class="space-y-0.5 text-xs text-center">';
            $as_js_output_exists = false;
            if (isset($data['row1_numbers']) && is_array($data['row1_numbers'])) {
                $as_js_output_exists = true;
                $html_output .= '<div class="flex items-center justify-center gap-1"><span class="font-semibold mr-1 w-8">R1:</span>';
                foreach ($data['row1_numbers'] as $num) if ($num !== null && is_numeric($num)) $html_output .= '<span class="lottery-ball bg-teal-500 text-white" style="width:28px; height:28px; font-size:0.7rem;">' . htmlspecialchars((string)(int)$num) . '</span>'; // No leading zero
                $html_output .= '</div>';
            }
            if (isset($data['row2_numbers']) && is_array($data['row2_numbers'])) {
                $as_js_output_exists = true;
                $html_output .= '<div class="flex items-center justify-center gap-1 mt-0.5"><span class="font-semibold mr-1 w-8">R2:</span>';
                foreach ($data['row2_numbers'] as $num) if ($num !== null && is_numeric($num)) $html_output .= '<span class="lottery-ball bg-cyan-500 text-white" style="width:28px; height:28px; font-size:0.7rem;">' . htmlspecialchars((string)(int)$num) . '</span>'; // No leading zero
                $html_output .= '</div>';
            }
            if (isset($data['row3_numbers']) && is_array($data['row3_numbers']) && isset($data['row3_letter']) && ctype_alpha($data['row3_letter'])) {
                $as_js_output_exists = true;
                $html_output .= '<div class="flex items-center justify-center gap-1 mt-0.5"><span class="font-semibold mr-1 w-8">R3:</span>';
                foreach ($data['row3_numbers'] as $num) if ($num !== null && is_numeric($num)) $html_output .= '<span class="lottery-ball bg-sky-500 text-white" style="width:28px; height:28px; font-size:0.7rem;">' . htmlspecialchars((string)(int)$num) . '</span>'; // No leading zero
                $html_output .= '<span class="lottery-ball bg-accentYellow text-primaryBlue font-bold" style="width:28px; height:28px; font-size:0.7rem;">' . htmlspecialchars($data['row3_letter']) . '</span>';
                $html_output .= '</div>';
            }
            if (!$as_js_output_exists) $html_output .= '<span class="text-red-600 text-xs">දත්ත දෝෂයකි (ID: ' . htmlspecialchars($lottery_id) . ')</span>';
            $html_output .= '</div>';
            break;

        default:
            if (isset($data['numbers_string'])) {
                $numbers = explode('-', $data['numbers_string']);
                $html_output .= '<div class="flex flex-wrap justify-center items-center gap-1.5 py-2">';
                foreach ($numbers as $part) {
                    // ... (default handling - decide if leading zeros should be removed or kept for 'standard_winning_numbers')
                    // For now, let's assume standard might need leading zeros for uniform ball appearance
                    $trimmed_part = trim($part);
                    if (empty($trimmed_part) && $trimmed_part !== '0') continue;
                    $is_letter = ctype_alpha($trimmed_part) && strlen($trimmed_part) == 1;
                    $ball_class = $is_letter ? 'bg-accentYellow text-primaryBlue font-bold' : 'bg-gray-500 text-white';
                    $display_val = $is_letter ? htmlspecialchars(strtoupper($trimmed_part)) : (is_numeric($trimmed_part) ? sprintf("%02d", (int)$trimmed_part) : htmlspecialchars($trimmed_part));
                    $html_output .= '<span class="lottery-ball ' . $ball_class . '">' . $display_val . '</span>';
                }
                $html_output .= '</div>';
            } else {
                $html_output .= '<div class="text-center text-xs p-1 bg-gray-100 rounded">';
                $html_output .= '<span class="lottery-ball bg-gray-400 text-white" title="Lottery ID">' . htmlspecialchars((string)$lottery_id) . '</span>';
                $html_output .= '</div>';
            }
            break;
    }
    $html_output .= '</div>';
    return $html_output;
}

/**
 * Format date for display in Sinhala
 * @param string $date Date in Y-m-d format
 * @return string Formatted date
 */
function format_date_sinhala($date) {
    $timestamp = strtotime($date);
    
    // Sinhala month names
    $sinhala_months = [
        1 => 'ජනවාරි',
        2 => 'පෙබරවාරි',
        3 => 'මාර්තු',
        4 => 'අප්‍රේල්',
        5 => 'මැයි',
        6 => 'ජූනි',
        7 => 'ජූලි',
        8 => 'අගෝස්තු',
        9 => 'සැප්තැම්බර්',
        10 => 'ඔක්තෝබර්',
        11 => 'නොවැම්බර්',
        12 => 'දෙසැම්බර්',
    ];
    
    $day = date('j', $timestamp);
    $month = $sinhala_months[date('n', $timestamp)];
    $year = date('Y', $timestamp);
    
    return $day . ' ' . $month . ' ' . $year;
}

/**
 * Format currency for display
 * @param float $amount Amount to format
 * @return string Formatted amount
 */
function format_currency($amount) {
    return 'රු. ' . number_format($amount, 2);
}

/**
 * Generate a WhatsApp order link
 * @param string $lottery_name Lottery name
 * @param string $draw_date Draw date (optional)
 * @return string WhatsApp URL
 */
// WhatsApp හාඩ්කෝඩ් අංකය ඉවත් කර වෙනත් තැනක තබා ගන්න
// includes/functions.php හි මෙම ෆංක්ෂන් එක යටතේ

function generate_whatsapp_order_link($lottery_name, $draw_date = '')
{
    // වසම් අවසානයේ මෙය කොන්ෆිගරේශන් ලෙස ගන්න
    global $config;
    $phone = isset($config['whatsapp_number']) ? $config['whatsapp_number'] : '+94771234567';

    $message = "හෙලෝ, මට " . $lottery_name . " ලොතරැයිය ඇණවුම් කිරීමට අවශ්‍යයි";

    if (!empty($draw_date)) {
        $message .= " - දිනුම් ඇදීම් දිනය: " . $draw_date;
    }

    // URL encode the message
    $message = urlencode($message);

    return "https://wa.me/{$phone}?text={$message}";
}

/**
 * Shorten text to a specific length
 * @param string $text Text to shorten
 * @param int $length Maximum length
 * @param string $append Text to append if shortened
 * @return string Shortened text
 */
function shorten_text($text, $length = 150, $append = '...') {
    if (strlen($text) > $length) {
        $text = substr($text, 0, $length) . $append;
    }
    return $text;
}

/**
 * Get winning number frequency for patterns analysis
 * @param PDO $pdo Database connection
 * @param int $lottery_id Lottery ID
 * @param int $limit Number of results to analyze
 * @return array Number frequency data
 */
function get_number_frequency($pdo, $lottery_id, $limit = 10) {
    try {
        $stmt = $pdo->prepare('
            SELECT winning_numbers
            FROM results
            WHERE lottery_id = :lottery_id
            ORDER BY draw_date DESC
            LIMIT :limit
        ');
        $stmt->bindParam(':lottery_id', $lottery_id, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $results = $stmt->fetchAll();
        $numbers_count = [];
        
        foreach ($results as $result) {
            $numbers = explode('-', $result['winning_numbers']);
            foreach ($numbers as $number) {
                if (isset($numbers_count[$number])) {
                    $numbers_count[$number]++;
                } else {
                    $numbers_count[$number] = 1;
                }
            }
        }
        
        // Sort by frequency (highest first)
        arsort($numbers_count);
        
        return $numbers_count;
    } catch (PDOException $e) {
        error_log("Database error in get_number_frequency(): " . $e->getMessage(), 0);
        return [];
    }
}
/**
 * Generate CSRF token
 * @return string Generated token
 */
function generate_csrf_token()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token Token to verify
 * @return bool True if token is valid
 */
function verify_csrf_token($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
/**
 * Validate and process uploaded image
 * @param array $file $_FILES array element
 * @param string $target_dir Directory to store uploads
 * @param array $allowed_types Array of allowed MIME types
 * @param int $max_size Maximum file size in bytes
 * @return array Result array with status and message/path
 */
function validate_and_upload_image($file, $target_dir, $allowed_types = ['image/jpeg', 'image/png', 'image/gif'], $max_size = 2000000)
{
    $result = [
        'success' => false,
        'message' => '',
        'path' => ''
    ];

    // Check if file exists and no errors occurred
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        $result['message'] = 'ගොනුව උඩුගත කිරීමේදී දෝෂයක් ඇති විය.';
        return $result;
    }

    // Check file size
    if ($file['size'] > $max_size) {
        $result['message'] = 'ගොනුව විශාල වැඩියි. උපරිම ප්‍රමාණය: ' . ($max_size / 1000000) . 'MB.';
        return $result;
    }

    // Check MIME type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $file_mime = $finfo->file($file['tmp_name']);

    if (!in_array($file_mime, $allowed_types)) {
        $result['message'] = 'අවසර නැති ගොනු වර්ගයකි. අවසර ලත් වර්ග: ' . implode(', ', $allowed_types);
        return $result;
    }

    // Create target directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    // Generate unique filename
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $unique_name = uniqid() . '.' . $file_extension;
    $target_path = $target_dir . $unique_name;

    // Try to move the uploaded file
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        $result['success'] = true;
        $result['path'] = $target_path;
    } else {
        $result['message'] = 'ගොනුව උඩුගත කිරීමේදී දෝෂයක් ඇති විය.';
    }

    return $result;
}

