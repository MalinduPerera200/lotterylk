<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Include database connection
include '../includes/db_connect.php';
include '../includes/functions.php';

// Define Lottery IDs as constants if not already included from functions.php
// These should ideally be in a central config file or functions.php
if (!defined('MAHAJANA_SAMPATHA_ID')) define('MAHAJANA_SAMPATHA_ID', 1);
if (!defined('GOVISETHA_ID')) define('GOVISETHA_ID', 2);
if (!defined('MEGA_POWER_ID')) define('MEGA_POWER_ID', 3);
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


include 'includes_admin/admin_header.php';

$results = [];
$lotteries_filter_list = [];
$total_pages = 1;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;
$search_term = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
$filter_lottery_id = isset($_GET['lottery_id']) ? (int)$_GET['lottery_id'] : 0;
$filtered = !empty($search_term) || $filter_lottery_id > 0;

if (isset($pdo) && $db_connected) {
    try {
        $lotteries_filter_list = get_all_lotteries($pdo);
    } catch (PDOException $e) {
        error_log("DB error (lotteries list manage_results): " . $e->getMessage(), 0);
    }

    try {
        $count_sql = 'SELECT COUNT(r.id) FROM results r JOIN lotteries l ON r.lottery_id = l.id WHERE 1=1';
        $params_count = [];
        if (!empty($search_term)) {
            $count_sql .= ' AND l.name LIKE :search_term';
            $params_count[':search_term'] = '%' . $search_term . '%';
        }
        if ($filter_lottery_id > 0) {
            $count_sql .= ' AND r.lottery_id = :filter_lottery_id';
            $params_count[':filter_lottery_id'] = $filter_lottery_id;
        }
        $stmt_count = $pdo->prepare($count_sql);
        $stmt_count->execute($params_count);
        $total_results = $stmt_count->fetchColumn();
        $total_pages = ceil($total_results / $per_page);
        if ($page > $total_pages && $total_pages > 0) {
            $page = $total_pages;
            $offset = ($page - 1) * $per_page;
        }

        $data_sql = 'SELECT r.id, r.lottery_id, r.draw_date, r.winning_numbers, r.jackpot_amount, r.published_at, l.name as lottery_name, l.type as lottery_type 
                     FROM results r JOIN lotteries l ON r.lottery_id = l.id WHERE 1=1';
        $params_data = [];
        if (!empty($search_term)) {
            $data_sql .= ' AND l.name LIKE :search_term';
            $params_data[':search_term'] = '%' . $search_term . '%';
        }
        if ($filter_lottery_id > 0) {
            $data_sql .= ' AND r.lottery_id = :filter_lottery_id';
            $params_data[':filter_lottery_id'] = $filter_lottery_id;
        }
        $data_sql .= ' ORDER BY r.draw_date DESC, r.id DESC LIMIT :offset, :per_page';
        $stmt_data = $pdo->prepare($data_sql);
        foreach ($params_data as $key => $value) {
            $stmt_data->bindValue($key, $value);
        }
        $stmt_data->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt_data->bindValue(':per_page', $per_page, PDO::PARAM_INT);
        $stmt_data->execute();
        $results = $stmt_data->fetchAll();
    } catch (PDOException $e) {
        error_log("DB error (results data manage_results): " . $e->getMessage(), 0);
        $_SESSION['flash_message'] = "ප්‍රතිඵල ලබාගැනීමේදී දත්ත සමුදාය දෝෂයක් ඇතිවිය.";
        $_SESSION['flash_message_type'] = "error";
    }
}

/**
 * Helper function to display winning numbers for the admin panel.
 */
function display_admin_winning_numbers($lottery_id, $winning_numbers_json)
{
    $data = json_decode($winning_numbers_json, true);
    $output = '';

    // Fallback for non-JSON or error in JSON or empty data
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($data) || empty($data)) {
        $numbers_fallback = explode('-', $winning_numbers_json);
        if (count($numbers_fallback) > 0 && !empty(trim($numbers_fallback[0]))) {
            $output .= '<div class="flex flex-wrap justify-center items-center gap-1">';
            $should_remove_leading_zero_fallback = in_array($lottery_id, [MAHAJANA_SAMPATHA_ID, ADA_SAMPATHA_ID, JAYA_SAMPATHA_ID, NLB_JAYA_ID]);
            foreach ($numbers_fallback as $part) {
                $trimmed_part = trim($part);
                if (empty($trimmed_part) && $trimmed_part !== '0') continue;
                $is_letter = ctype_alpha($trimmed_part) && strlen($trimmed_part) == 1;
                $ball_class = $is_letter ? 'bg-red-500 font-bold' : 'bg-primaryBlue';
                $display_val = $is_letter ? htmlspecialchars(strtoupper($trimmed_part))
                    : (is_numeric($trimmed_part) ?
                        ($should_remove_leading_zero_fallback ? htmlspecialchars((string)(int)$trimmed_part) : sprintf("%02d", (int)$trimmed_part))
                        : htmlspecialchars($trimmed_part));
                $output .= '<span class="inline-block text-white rounded-full w-6 h-6 flex items-center justify-center text-xs ' . $ball_class . '">' . $display_val . '</span>';
            }
            $output .= '</div>';
        } else {
            $output .= '<span class="text-xs text-gray-500">N/A</span>';
        }
        return $output;
    }

    $output = '<div class="flex flex-col items-center gap-0.5 text-xs">';

    switch ($lottery_id) {
        case MAHAJANA_SAMPATHA_ID: // No leading zeros for numbers
            if (isset($data['letter']) && ctype_alpha($data['letter']) && isset($data['numbers']) && is_array($data['numbers']) && count($data['numbers']) == 6) {
                $output .= '<div class="flex items-center gap-1">';
                $output .= '<span class="inline-block bg-red-500 text-white ball-xs-admin font-bold">' . htmlspecialchars($data['letter']) . '</span>';
                foreach ($data['numbers'] as $num) if (is_numeric($num)) $output .= '<span class="inline-block bg-primaryBlue text-white ball-xs-admin">' . htmlspecialchars((string)(int)$num) . '</span>';
                $output .= '</div>';
            } else {
                $output .= '<span class="text-red-500">දත්ත දෝෂයකි</span>';
            }
            break;

        case SUPIRI_DHANA_SAMPATHA_ID: // Keep leading zeros
            if (isset($data['letter']) && ctype_alpha($data['letter']) && isset($data['numbers']) && is_array($data['numbers']) && count($data['numbers']) == 6) {
                $output .= '<div class="flex items-center gap-1">';
                $output .= '<span class="inline-block bg-red-500 text-white ball-xs-admin font-bold">' . htmlspecialchars($data['letter']) . '</span>';
                foreach ($data['numbers'] as $num) if (is_numeric($num)) $output .= '<span class="inline-block bg-purple-500 text-white ball-xs-admin">' . sprintf("%02d", (int)$num) . '</span>';
                $output .= '</div>';
            } else {
                $output .= '<span class="text-red-500">දත්ත දෝෂයකි</span>';
            }
            break;

        case NLB_JAYA_ID: // No leading zeros for numbers
            if (isset($data['letter']) && ctype_alpha($data['letter']) && isset($data['numbers']) && is_array($data['numbers']) && count($data['numbers']) == 4) {
                $output .= '<div class="flex items-center gap-1">';
                $output .= '<span class="inline-block bg-red-500 text-white ball-xs-admin font-bold">' . htmlspecialchars($data['letter']) . '</span>';
                foreach ($data['numbers'] as $num) if (is_numeric($num)) $output .= '<span class="inline-block bg-primaryGreen text-white ball-xs-admin">' . htmlspecialchars((string)(int)$num) . '</span>';
                $output .= '</div>';
            } else {
                $output .= '<span class="text-red-500">දත්ත දෝෂයකි</span>';
            }
            break;

        case GOVISETHA_ID:
        case ADA_KOTIPATHI_ID:
        case SUPER_BALL_ID:
        case SHANIDA_ID:
        case JAYODA_ID: // Keep leading zeros
            if (isset($data['letter']) && ctype_alpha($data['letter']) && isset($data['numbers']) && is_array($data['numbers']) && count($data['numbers']) == 4) {
                $output .= '<div class="flex items-center gap-1">';
                $output .= '<span class="inline-block bg-red-500 text-white ball-xs-admin font-bold">' . htmlspecialchars($data['letter']) . '</span>';
                foreach ($data['numbers'] as $num) if (is_numeric($num)) $output .= '<span class="inline-block bg-primaryGreen text-white ball-xs-admin">' . sprintf("%02d", (int)$num) . '</span>';
                $output .= '</div>';
            } else {
                $output .= '<span class="text-red-500">දත්ත දෝෂයකි</span>';
            }
            break;

        case MEGA_POWER_ID: // Letter + 4 Main Nums + 1 Special Num (Main numbers display with leading zero, special with leading zero)
            if (
                isset($data['letter']) && ctype_alpha($data['letter']) &&
                isset($data['numbers']) && is_array($data['numbers']) && count($data['numbers']) == 4 &&
                isset($data['special_number']) && is_numeric($data['special_number'])
            ) {
                $output .= '<div class="flex flex-col items-center gap-0.5">';
                $output .= '<div class="flex items-center gap-1">';
                $output .= '<span class="inline-block bg-blue-600 text-white ball-xs-admin font-bold">' . htmlspecialchars($data['letter']) . '</span>';
                if (isset($data['numbers'][0]) && is_numeric($data['numbers'][0])) $output .= '<span class="inline-block bg-red-600 text-white ball-xs-admin">' . sprintf("%02d", (int)$data['numbers'][0]) . '</span>';
                for ($i = 1; $i < 4; $i++) {
                    if (isset($data['numbers'][$i]) && is_numeric($data['numbers'][$i])) $output .= '<span class="inline-block bg-yellow-400 text-black ball-xs-admin">' . sprintf("%02d", (int)$data['numbers'][$i]) . '</span>';
                }
                $output .= '</div>';
                $output .= '<div class="flex items-center gap-1 mt-0.5">';
                $output .= '<span class="text-xs font-semibold mr-1">Super No:</span>';
                $output .= '<span class="inline-block bg-yellow-400 text-black ball-xs-admin">' . sprintf("%02d", (int)$data['special_number']) . '</span>';
                $output .= '</div></div>';
            } else {
                $output .= '<span class="text-red-500">Mega Power: දත්ත දෝෂයකි</span>';
            }
            break;

        case DHANA_NIDHANAYA_ID: // Main numbers no leading zero, LDC single digit
            $output .= '<div class="space-y-0.5 text-left text-tiny">';
            $dn_valid = false;
            if (isset($data['main_letter'], $data['main_numbers']) && is_array($data['main_numbers']) && count($data['main_numbers']) == 4) {
                $dn_valid = true;
                $output .= '<div class="flex items-center gap-0.5"><span class="font-semibold w-12">ප්‍රධාන:</span><span class="inline-block bg-red-500 text-white ball-xs-admin">' . htmlspecialchars($data['main_letter']) . '</span>';
                foreach ($data['main_numbers'] as $num) if (is_numeric($num)) $output .= '<span class="inline-block bg-primaryBlue text-white ball-xs-admin">' . htmlspecialchars((string)(int)$num) . '</span>'; // No leading zero
                $output .= '</div>';
            }
            if (isset($data['lakshapathi_double_chance_no']) && is_array($data['lakshapathi_double_chance_no']) && count($data['lakshapathi_double_chance_no']) == 5) {
                $dn_valid = true;
                $output .= '<div class="flex items-center gap-0.5"><span class="font-semibold w-12">LDC:</span>';
                foreach ($data['lakshapathi_double_chance_no'] as $num) if (is_numeric($num)) $output .= '<span class="inline-block bg-green-500 text-white ball-xs-admin">' . htmlspecialchars((string)(int)$num) . '</span>'; // No leading zero
                $output .= '</div>';
            }
            if (!$dn_valid) $output .= '<span class="text-red-500">දත්ත දෝෂයකි</span>';
            $output .= '</div>';
            break;

        case HANDAHANA_ID: // Lagna numbers no leading zero, Dhanayogaya single, Daiwa anka no leading zero
            $output .= '<div class="space-y-0.5 text-left text-tiny">';
            $h_valid = false;
            if (isset($data['lagna_name'], $data['lagna_numbers']) && is_array($data['lagna_numbers']) && count($data['lagna_numbers']) == 4) {
                $h_valid = true;
                $output .= '<div class="flex items-center gap-0.5"><span class="font-semibold mr-1">ලග්නය: <span class="text-purple-700 bg-purple-100 px-1 py-0.5 rounded-sm">' . htmlspecialchars(ucfirst($data['lagna_name'])) . '</span></span>';
                foreach ($data['lagna_numbers'] as $num) if (is_numeric($num)) $output .= '<span class="inline-block bg-purple-500 text-white ball-xs-admin">' . htmlspecialchars((string)(int)$num) . '</span>'; // No leading zero
                $output .= '</div>';
            }
            if (isset($data['dhanayogaya']) && is_array($data['dhanayogaya']) && count($data['dhanayogaya']) == 5) {
                $h_valid = true;
                $output .= '<div class="flex items-center gap-0.5"><span class="font-semibold mr-1">ධන.යෝ:</span>';
                foreach ($data['dhanayogaya'] as $num) if (is_numeric($num)) $output .= '<span class="inline-block bg-yellow-500 text-white ball-xs-admin">' . htmlspecialchars((string)(int)$num) . '</span>'; // No leading zero
                $output .= '</div>';
            }
            if (isset($data['daiwa_ankaya']) && is_numeric($data['daiwa_ankaya'])) {
                $h_valid = true;
                $output .= '<div class="flex items-center gap-0.5"><span class="font-semibold mr-1">දෛ.අං:</span><span class="inline-block bg-red-600 text-white ball-xs-admin font-bold">' . htmlspecialchars((string)(int)$data['daiwa_ankaya']) . '</span></div>'; // No leading zero
            }
            if (!$h_valid) $output .= '<span class="text-red-500">දත්ත දෝෂයකි</span>';
            $output .= '</div>';
            break;

        case LAGNA_WASANA_ID: // Keep leading zeros for numbers for ball appearance
            if (isset($data['lagna_name']) && !empty($data['lagna_name']) && isset($data['numbers']) && is_array($data['numbers']) && count($data['numbers']) == 4) {
                $output .= '<div class="flex items-center gap-1"><span class="font-semibold bg-pink-200 text-pink-700 px-1 py-0.5 rounded-sm text-xs">' . htmlspecialchars(ucfirst($data['lagna_name'])) . '</span>';
                foreach ($data['numbers'] as $num) if (is_numeric($num)) $output .= '<span class="inline-block bg-pink-500 text-white ball-xs-admin">' . sprintf("%02d", (int)$num) . '</span>';
                $output .= '</div>';
            } else {
                $output .= '<span class="text-red-500">දත්ත දෝෂයකි</span>';
            }
            break;

        case KAPRUKA_ID: // Keep leading zeros for numbers and special number
            if (isset($data['letter']) && ctype_alpha($data['letter']) && isset($data['numbers']) && is_array($data['numbers']) && count($data['numbers']) == 4 && isset($data['special_number']) && is_numeric($data['special_number'])) {
                $output .= '<div class="flex items-center gap-1">';
                $output .= '<span class="inline-block bg-red-500 text-white ball-xs-admin font-bold">' . htmlspecialchars($data['letter']) . '</span>';
                foreach ($data['numbers'] as $num) if (is_numeric($num)) $output .= '<span class="inline-block bg-green-600 text-white ball-xs-admin">' . sprintf("%02d", (int)$num) . '</span>';
                $output .= '<span class="font-bold mx-0.5 text-gray-600 text-sm">+</span><span class="inline-block bg-black text-white ball-xs-admin font-bold">' . sprintf("%02d", (int)$data['special_number']) . '</span>';
                $output .= '</div>';
            } else {
                $output .= '<span class="text-red-500">දත්ත දෝෂයකි</span>';
            }
            break;

        case ADA_SAMPATHA_ID:
        case JAYA_SAMPATHA_ID: // Assuming Jaya Sampatha also might have a similar multi-row structure
            $html_output_for_as_js = '';
            $as_js_data_found = false;

            // Row 1
            if (isset($data['row1_numbers']) && is_array($data['row1_numbers']) && !empty($data['row1_numbers'])) {
                $as_js_data_found = true;
                $html_output_for_as_js .= '<div class="flex items-center gap-0.5"><span class="font-semibold mr-1 w-8 text-gray-600">R1:</span>';
                foreach ($data['row1_numbers'] as $num) {
                    if ($num !== null && is_numeric($num)) {
                        $html_output_for_as_js .= '<span class="inline-block bg-teal-500 text-white ball-xs-admin">' . htmlspecialchars((string)(int)$num) . '</span>';
                    }
                }
                $html_output_for_as_js .= '</div>';
            }

            // Row 2
            if (isset($data['row2_numbers']) && is_array($data['row2_numbers']) && !empty($data['row2_numbers'])) {
                $as_js_data_found = true;
                $html_output_for_as_js .= '<div class="flex items-center gap-0.5 mt-0.5"><span class="font-semibold mr-1 w-8 text-gray-600">R2:</span>';
                foreach ($data['row2_numbers'] as $num) {
                    if ($num !== null && is_numeric($num)) {
                        $html_output_for_as_js .= '<span class="inline-block bg-cyan-500 text-white ball-xs-admin">' . htmlspecialchars((string)(int)$num) . '</span>';
                    }
                }
                $html_output_for_as_js .= '</div>';
            }

            // Row 3 (assuming it can have numbers and a letter)
            if (isset($data['row3_numbers']) && is_array($data['row3_numbers']) && !empty($data['row3_numbers'])) {
                $as_js_data_found = true;
                $html_output_for_as_js .= '<div class="flex items-center gap-0.5 mt-0.5"><span class="font-semibold mr-1 w-8 text-gray-600">R3:</span>';
                foreach ($data['row3_numbers'] as $num) {
                    if ($num !== null && is_numeric($num)) {
                        $html_output_for_as_js .= '<span class="inline-block bg-sky-500 text-white ball-xs-admin">' . htmlspecialchars((string)(int)$num) . '</span>';
                    }
                }
                // Check for a letter in row 3, if it exists
                if (isset($data['row3_letter']) && ctype_alpha($data['row3_letter'])) {
                    $html_output_for_as_js .= '<span class="inline-block bg-red-500 text-white ball-xs-admin font-bold">' . htmlspecialchars(strtoupper($data['row3_letter'])) . '</span>';
                }
                $html_output_for_as_js .= '</div>';
            }

            if ($as_js_data_found) {
                $output .= '<div class="space-y-0.5 text-left text-tiny">' . $html_output_for_as_js . '</div>';
            } else {
                $lottery_name_temp = ($lottery_id == ADA_SAMPATHA_ID) ? "අද සම්පත" : (($lottery_id == JAYA_SAMPATHA_ID) ? "ජය සම්පත" : "මෙම ලොතරැයිය");
                $output .= '<span class="text-red-500 text-xs">' . $lottery_name_temp . ': දත්ත ආකෘතිය දෝෂ සහිතයි.</span>';
            }
            break;
        default:
            if (isset($data['numbers_string'])) {
                $numbers = explode('-', $data['numbers_string']);
                $output .= '<div class="flex flex-wrap justify-center items-center gap-1">';
                // For 'standard_winning_numbers', decide if leading zero is needed. Let's assume yes for now.
                $should_remove_leading_zero_default = in_array($lottery_id, [MAHAJANA_SAMPATHA_ID, ADA_SAMPATHA_ID, JAYA_SAMPATHA_ID, NLB_JAYA_ID]);
                foreach ($numbers as $part) {
                    $trimmed_part = trim($part);
                    if (empty($trimmed_part) && $trimmed_part !== '0') continue;
                    $is_letter = ctype_alpha($trimmed_part) && strlen($trimmed_part) == 1;
                    $ball_class = $is_letter ? 'bg-red-500 font-bold' : 'bg-gray-500';
                    $display_val = $is_letter ? htmlspecialchars(strtoupper($trimmed_part))
                        : (is_numeric($trimmed_part) ?
                            ($should_remove_leading_zero_default ? htmlspecialchars((string)(int)$trimmed_part) : sprintf("%02d", (int)$trimmed_part))
                            : htmlspecialchars($trimmed_part));
                    $output .= '<span class="inline-block text-white rounded-full w-6 h-6 flex items-center justify-center text-xs ">' . $display_val . '</span>';
                }
                $output .= '</div>';
            } else {
                $output .= '<div class="text-left text-xs break-all p-1 bg-gray-100 rounded max-w-xs mx-auto">';
                $output .= '<span class="font-semibold">JSON දත්ත (Unhandled):</span><pre class="whitespace-pre-wrap text-xs">' . htmlspecialchars(print_r($data, true)) . '</pre>';
                $output .= '</div>';
            }
            break;
    }
    $output .= '</div>';
    return $output;
}
?>
<style>
    .ball-xs-admin {
        width: 1.4rem;
        height: 1.4rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 9999px;
        font-size: 0.7rem;
        line-height: 1;
        font-weight: 600;
        padding: 0.1rem;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .text-tiny {
        font-size: 0.68rem;
        line-height: 0.9rem;
    }
</style>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-bold text-gray-800">ප්‍රතිඵල කළමනාකරණය</h2>
    <a href="add_result.php" class="bg-primaryBlue hover:bg-blue-700 text-white font-bold py-2 px-4 rounded flex items-center">
        <i class="fas fa-plus mr-2"></i> නව ප්‍රතිඵලයක් එකතු කරන්න
    </a>
</div>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-bold text-gray-700 mb-4">පෙරීම් හා සෙවීම</h3>
    <form action="manage_results.php" method="get" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label for="search_term" class="block text-gray-700 mb-2">සෙවීම (ලොතරැයි නම අනුව)</label>
            <input type="text" id="search_term" name="search" value="<?php echo htmlspecialchars($search_term); ?>" placeholder="ලොතරැයි නම..." class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue">
        </div>
        <div>
            <label for="lottery_id_filter" class="block text-gray-700 mb-2">ලොතරැයිය අනුව පෙරන්න</label>
            <select id="lottery_id_filter" name="lottery_id" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primaryBlue">
                <option value="0">සියල්ල පෙන්වන්න</option>
                <?php foreach ($lotteries_filter_list as $lottery_item): ?>
                    <option value="<?php echo $lottery_item['id']; ?>" <?php echo ($filter_lottery_id == $lottery_item['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($lottery_item['name']) . ' (' . htmlspecialchars($lottery_item['type']) . ')'; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="flex items-end space-x-4">
            <button type="submit" class="bg-primaryGreen hover:bg-green-700 text-white font-bold py-2 px-4 rounded flex-1">
                <i class="fas fa-search mr-2"></i> සොයන්න
            </button>
            <?php if ($filtered): ?>
                <a href="manage_results.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded flex-1 text-center">
                    <i class="fas fa-times mr-2"></i> පෙරීම් ඉවත් කරන්න
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>


<div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
    <?php if (empty($results)): ?>
        <div class="p-6 text-center text-gray-500">
            <i class="fas fa-info-circle text-3xl mb-3"></i>
            <p>ප්‍රතිඵල හමු නොවීය. <?php echo $filtered ? 'කරුණාකර වෙනත් පෙරීම් පරාමිතීන් උත්සාහ කරන්න.' : ''; ?></p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ලොතරැයිය</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">දිනය</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">ජයග්‍රාහී අංක / තොරතුරු</th>
                        <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">ජැක්පොට්</th>
                        <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">ප්‍රකාශිත</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">ක්‍රියා</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($results as $result): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="text-xs font-semibold inline-block py-0.5 px-1.5 uppercase rounded text-white bg-<?php echo (strtoupper($result['lottery_type']) == 'NLB') ? 'primaryBlue' : 'primaryGreen'; ?> mr-2">
                                        <?php echo htmlspecialchars($result['lottery_type']); ?>
                                    </span>
                                    <span class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($result['lottery_name']); ?></span>
                                </div>
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('y-m-d', strtotime($result['draw_date'])); ?>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 text-center align-middle">
                                <div class="flex justify-center items-center h-full">
                                    <?php echo display_admin_winning_numbers($result['lottery_id'], $result['winning_numbers']); ?>
                                </div>
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500 text-right">
                                <?php echo ($result['jackpot_amount'] > 0 && is_numeric($result['jackpot_amount'])) ? 'Rs.' . number_format($result['jackpot_amount'], 2) : 'N/A'; ?>
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500 text-center hidden md:table-cell">
                                <?php echo date('y-m-d H:i', strtotime($result['published_at'])); ?>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                <a href="edit_result.php?id=<?php echo $result['id']; ?>" class="text-indigo-600 hover:text-indigo-900 mr-2" title="සංස්කරණය කරන්න">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="delete_result.php?id=<?php echo $result['id']; ?>" class="text-red-600 hover:text-red-900 delete-button" title="මකා දමන්න">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php if ($total_pages > 1):
    $pagination_params_url = [];
    if (!empty($search_term)) $pagination_params_url['search'] = $search_term;
    if ($filter_lottery_id > 0) $pagination_params_url['lottery_id'] = $filter_lottery_id;
    $query_string_for_pagination = http_build_query($pagination_params_url);
    $base_url_pagination = 'manage_results.php?' . $query_string_for_pagination . (empty($query_string_for_pagination) ? '' : '&');
?>
    <div class="flex justify-center mt-6 mb-6">
        <nav class="inline-flex rounded-md shadow-sm" aria-label="Pagination">
            <a href="<?php echo $base_url_pagination; ?>page=<?php echo max(1, $page - 1); ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 <?php if ($page <= 1) echo ' cursor-not-allowed opacity-50'; ?>">
                <span class="sr-only">Previous</span><i class="fas fa-chevron-left h-5 w-5"></i>
            </a>
            <?php
            $links_limit = 5;
            $start = max(1, $page - floor($links_limit / 2));
            $end = min($total_pages, $start + $links_limit - 1);
            if ($end - $start + 1 < $links_limit) {
                $start = max(1, $end - $links_limit + 1);
            }

            if ($start > 1) {
                echo '<a href="' . $base_url_pagination . 'page=1" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">1</a>';
                if ($start > 2) echo '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>';
            }
            for ($i = $start; $i <= $end; $i++): ?>
                <a href="<?php echo $base_url_pagination; ?>page=<?php echo $i; ?>" class="relative inline-flex items-center px-4 py-2 border <?php echo $i == $page ? 'border-primaryBlue bg-primaryBlue text-white z-10' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'; ?> text-sm font-medium">
                    <?php echo $i; ?>
                </a>
            <?php endfor;
            if ($end < $total_pages) {
                if ($end < $total_pages - 1) echo '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>';
                echo '<a href="' . $base_url_pagination . 'page=' . $total_pages . '" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">' . $total_pages . '</a>';
            }
            ?>
            <a href="<?php echo $base_url_pagination; ?>page=<?php echo min($total_pages, $page + 1); ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 <?php if ($page >= $total_pages) echo ' cursor-not-allowed opacity-50'; ?>">
                <span class="sr-only">Next</span><i class="fas fa-chevron-right h-5 w-5"></i>
            </a>
        </nav>
    </div>
<?php endif; ?>


<div class="mt-8 bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-bold text-gray-700 mb-4">CSV ආයාත කිරීම / අපනයනය කිරීම</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h4 class="font-bold text-gray-600 mb-2">CSV ආයාත කිරීම</h4>
            <form action="import_results.php" method="post" enctype="multipart/form-data">
                <div class="mb-4">
                    <input type="file" name="csv_file" id="csv_file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primaryBlue file:text-white hover:file:bg-blue-700" required accept=".csv">
                </div>
                <button type="submit" class="bg-primaryBlue hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-file-import mr-2"></i> ආයාත කරන්න
                </button>
            </form>
            <p class="text-xs text-gray-500 mt-2">සැලකිය යුතුයි: CSV ගොනුවේ 'winning_numbers' තීරුව, අදාළ ලොතරැයියට ගැලපෙන JSON string එකක් ලෙස තිබිය යුතුය.</p>
        </div>
        <div>
            <h4 class="font-bold text-gray-600 mb-2">CSV අපනයනය කිරීම</h4>
            <p class="text-sm text-gray-500 mb-4">දැනට පවතින සියලුම ප්‍රතිඵල CSV ලෙස අපනයනය කරන්න.</p>
            <a href="export_results.php" class="bg-primaryGreen hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-block">
                <i class="fas fa-file-export mr-2"></i> අපනයනය කරන්න
            </a>
        </div>
    </div>
</div>

<?php
include 'includes_admin/admin_footer.php';
?>