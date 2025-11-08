<?php



error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_error.log');

ini_set('html_errors', 1);
ini_set('track_errors', 1);
ini_set('report_memleaks', 1);
ini_set('display_errors', 'On');


$FAKEOS = 0;
$API_KEY = "TOKEN";

define('API_KEY', $API_KEY);
define("IDBot", explode(":", $API_KEY)[0]);
define('ADMIN' , '123456');

$bot_id = IDBot;
if (!is_dir("DATA_BASES_X")) {
    mkdir("DATA_BASES_X", 0777, true);
}

if (!is_dir("DATA_BASES_X/DBRSHAQ")) {
    mkdir("DATA_BASES_X/DBRSHAQ", 0777, true);
}

if (!is_dir("DATA_BASES_X/DBRSHAQ/$bot_id")) {
    mkdir("DATA_BASES_X/DBRSHAQ/$bot_id", 0777, true);
}


$TOM = new TOMDB($bot_id, "acounts.db");
$bot = new TOMDB($bot_id, "bot.db");

function bot($method, $datas = []) {
    global $TOM, $bot;
    $Y = false; 

  
    if (isset($datas['reply_markup'])) {
        $markup = json_decode($datas['reply_markup']);
        if (isset($markup->inline_keyboard)) {
            $AZRARS = $bot->get("AZRARSOx") ?? [];
            foreach ($markup->inline_keyboard as $rowIndex => $row) {
                foreach ($row as $buttonIndex => $button) {
                    foreach ($AZRARS as $index => $added_button) {
                        $added_buttonx = $bot->get("AZRARS_X_" . $added_button);

                        if ($added_button == '✅ عدد الطلبات :  ✅' && preg_match('/عدد الطلبات /', $button->text)) {
                            if (preg_match('/\d+/', $button->text, $matches) && !$Y) {
                                $Y = true;
                                $order_count = (int)$matches[0];
                                $button->text = preg_replace('/\d+/', '', $button->text); // حذف العدد
                            }
                        }

                        if ($button->text == $added_button) {
                            if ($Y) {
                                $as = explode(':', $added_buttonx);
                                $ao = $as[0] . ": " . $order_count . "" . $as[1];
                                $added_buttonx = $ao;
                            }
                            $markup->inline_keyboard[$rowIndex][$buttonIndex]->text = $added_buttonx;
                        }
                    }
                }
            }
            $datas['reply_markup'] = json_encode($markup);
        }
    }

    $restriction = $bot->get('HIMAIA_restriction');
    if ($restriction == '✅') {
        $datas['protect_content'] = true;
    }

    if ($bot->get('HIMAIA_restriction_media') == '✅' && strtolower($method) != "sendmessage") {
        $datas['protect_content'] = true;
    }

    if ($bot->get('HIMAIA_restriction_text') == '✅' && strtolower($method) == "sendmessage") {
        $datas['protect_content'] = true;
    }

    if ($bot->get('HIMAIA_restriction_LINK') == '✅' && isset($datas['text']) && preg_match('/https/', strtolower($datas['text']))) {
        $datas['protect_content'] = true;
    }

    $url = "https://api.telegram.org/bot" . API_KEY . "/" . $method;

    $mh = curl_multi_init();
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);

    curl_multi_add_handle($mh, $ch);

    do {
        curl_multi_exec($mh, $running);  
        curl_multi_select($mh);         
    } while ($running > 0);            
    

    $response = curl_multi_getcontent($ch);
    
    if (curl_errno($ch)) {
        error_log("cURL Error: " . curl_error($ch));
        return false;
    }


    curl_multi_remove_handle($mh, $ch);
    curl_multi_close($mh);
    
    return json_decode($response);
}


$cmd_list = $bot->get('cmd_list') ?: [];
if (!in_array('start', $cmd_list)) {
    $cmd_list[] = 'start';
    $bot->set('cmd_start', 'بدء الاستخدام');
    $bot->set('cmd_list', $cmd_list);
}


$cmd_list = $bot->get('cmd_list') ?: [];
$Commands = [];
foreach(array_reverse($cmd_list) as $cmd){
    $desc = $bot->get('cmd_' . $cmd) ?: 'وصف غير موجود';
    $Commands[] = ['command' => $cmd, 'description' => $desc];
}


bot('setMyCommands', [
    'commands' => json_encode($Commands)
]);

bot( 'setWebhook', [
    'url' =>$_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'],
    'drop_pending_updates' =>true,
]);


$usrbot = bot("getme")->result->username;
define("USR_BOT", $usrbot);


date_default_timezone_set('Asia/Baghdad');
$USRBOT = $usrbot;

$ADMINS = [ADMIN];


function TMOIL($API_KEY, $method, $datas = []) {
    // إعداد الرابط
    $url = "https://api.telegram.org/bot" . $API_KEY . "/" . $method;

    // إنشاء multi-curl handle
    $mh = curl_multi_init();

    // إعداد curl handle (لإرسال البيانات)
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);

    // إضافة الـ curl handle إلى multi handle
    curl_multi_add_handle($mh, $ch);

    // تنفيذ الطلبات بشكل غير متزامن
    $running = null;
    do {
        // تنفيذ جميع الطلبات المتزامنة
        curl_multi_exec($mh, $running);
        // الانتظار حتى يتم استكمال جميع الطلبات
        curl_multi_select($mh);
    } while ($running > 0);

    // الحصول على الاستجابة
    $response = curl_multi_getcontent($ch);

    // فحص وجود أي أخطاء أثناء عملية curl
    if (curl_errno($ch)) {
        error_log("cURL Error: " . curl_error($ch));
        return false;
    }

    // إزالة الـ curl handle من multi handle
    curl_multi_remove_handle($mh, $ch);
    curl_multi_close($mh);

    // إرجاع الاستجابة بعد تحويلها إلى JSON
    return json_decode($response);
}




function br($method, $datas = []) {
    return TMOIL(API_KEY, $method, $datas);
}

function sendCaptcha($chat_id, $bot_token = API_KEY) {
    $code = rand(10000, 99999);
    $width = 800;
    $height = 300;

    $image = imagecreatetruecolor($width, $height);
    $white = imagecolorallocate($image, 255, 255, 255);
    $black = imagecolorallocate($image, 0, 0, 0);
    $gray = imagecolorallocate($image, 180, 180, 180);
    imagefilledrectangle($image, 0, 0, $width, $height, $white);

    for ($i = 0; $i < 10; $i++) {
        imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $gray);
    }

    $font = __DIR__ . '/Ewert-Regular.ttf';
    if (!file_exists($font)) {
        die("الخط غير موجود: $font");
    }

    $fontSize = 80;
    $angle = 0;
    $x = 200;
    $y = 180;

    imagettftext($image, $fontSize, $angle, $x, $y, $black, $font, $code);
    $filename = "captcha_$chat_id.png";
    imagepng($image, $filename);
    imagedestroy($image);

    $url = "https://api.telegram.org/bot$bot_token/sendPhoto";
    $post_fields = [
        'chat_id' => $chat_id,
        'photo' => new CURLFile(realpath($filename)),
        'caption' => "أدخل الكود الموجود في الصورة"
    ];

    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type:multipart/form-data"]);
    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields); 
    $output = curl_exec($ch);

    unlink($filename);

    return ['code' => $code, 'response' => $output];
}

function sendEmojiCaptcha($chat_id) {
    $animals = [
        "🐶" => "كلب", "🐱" => "قطة", "🐭" => "فأر", "🐹" => "هامستر",
        "🐰" => "أرنب", "🦊" => "ثعلب", "🐻" => "دب", "🐼" => "باندا",
        "🐯" => "نمر", "🦁" => "أسد", "🐨" => "كوالا", "🐮" => "بقرة"
    ];
    
    $keys = array_keys($animals);
    shuffle($keys);

    $correct = $keys[0]; 
    $choices = array_slice($keys, 0, 9); 
    shuffle($choices);

    $keyboard = array_chunk(array_map(function($e) {
        return ["text" => $e, "callback_data" => "EMOJI_VERIF_$e"];
    }, $choices), 3);

    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => "🚨 *يجب التحقق قبل استخدام البوت!*\n• اختر الحيوان الصحيح من القائمة أدناه!\n• الحيوان المطلوب: $correct",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => $keyboard
        ])
    ]);

    return ['code' => $correct];
}


class TOMDB {
    private $db;

    public function __construct($bot_id, $filename = 'TOM.db') {
        $path = "DATA_BASES_X/DBRSHAQ/$bot_id/$filename";
        $this->db = new SQLite3($path);
        $this->db->exec("CREATE TABLE IF NOT EXISTS storage (key TEXT PRIMARY KEY, value TEXT)");
        $this->db->exec("CREATE INDEX IF NOT EXISTS idx_key ON storage (key)");
    }

    public function set($key, $value) {
        $stmt = $this->db->prepare("INSERT INTO storage (key, value) VALUES (:key, :value) ON CONFLICT(key) DO UPDATE SET value = excluded.value");
        $stmt->bindValue(':key', $key, SQLITE3_TEXT);
        $stmt->bindValue(':value', json_encode($value), SQLITE3_TEXT);
        return $stmt->execute();
    }

    public function get($key) {
        $stmt = $this->db->prepare("SELECT value FROM storage WHERE key = :key");
        $stmt->bindValue(':key', $key, SQLITE3_TEXT);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        return $row ? json_decode($row['value'], true) : null;
    }

    public function delete($key) {
        $stmt = $this->db->prepare("DELETE FROM storage WHERE key = :key");
        $stmt->bindValue(':key', $key, SQLITE3_TEXT);
        return $stmt->execute();
    }

    public function clear() {
        return $this->db->exec("DELETE FROM storage");
    }

    public function getAllWithPrefix($prefix) {
        $stmt = $this->db->prepare("SELECT key, value FROM storage WHERE key LIKE :prefix");
        $stmt->bindValue(':prefix', $prefix . '%', SQLITE3_TEXT);
        $result = $stmt->execute();
        $data = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data[$row['key']] = json_decode($row['value'], true);
        }
        return $data;
    }

    public function __destruct() {
        $this->db->close();
    }
}
function TOMencode($id){
    $g = [1,2,3,4,5,6,7,8,9,0];
    $x = ['A','b','B','C','D','y','o','t','X','Q','K','M'];
    return str_replace($g,$x,$id);
}
function TOMdecode($id){
    $g = [1,2,3,4,5,6,7,8,9,0];
    $x = ['A','b','B','C','D','y','o','t','X','Q','K','M'];
    return str_replace($x,$g,$id);
}

function coderandom($length = 32) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function X_neW($channel, $user_id) {
    $response = bot('getChatMember', [
        'chat_id' => $channel,
        'user_id' => $user_id,
    ]);
    if ($response->ok) {
        $status = $response->result->status;
        return in_array($status, ['member', 'administrator', 'creator']);
    }
    return false;
}

$update = json_decode(file_get_contents('php://input'));

if (isset($update->message)) {
    $message = $update->message;
    $message_id = $message->message_id;
    $username = $message->from->username;
    $chat_id = $message->chat->id;
    $title = $message->chat->title;
    $text = $message->text ;
    $user = $message->from->username;
    $name = $message->from->first_name;
    $from_id = $message->from->id;
} elseif (isset($update->callback_query)) {
    $data = $update->callback_query->data;
    $chat_id = $update->callback_query->message->chat->id;
    $title = $update->callback_query->message->chat->title ;
    $message_id = $update->callback_query->message->message_id;
    $name = $update->callback_query->message->chat->first_name;
    $user = $update->callback_query->message->chat->username;
    $from_id = $update->callback_query->from->id;
   
}


if($update->my_chat_member->new_chat_member->status == 'administrator'){
    if($update->my_chat_member->new_chat_member->user->username == $usrbot){

        $chat_id = $update->my_chat_member->chat->id;
        $UU = bot('exportChatInviteLink', ['chat_id' => $chat_id]);


        if($UU->ok){
            $inviteLink = $UU->result;
        } else {
            $inviteLink = 'تعذر استخراج الرابط ❌';
        }

        bot('SendMessage', [
            'reply_to_message_id' => $message_id,
            'chat_id' => $ADMIN,
            'text' => "*- تم اضافه البوت ادمن في احد القنوات ➕*
♦️ ايدي القناة : `". $chat_id."`
🔺 اسم القناة : *". $update->my_chat_member->chat->title."*

*🔜 معلومات الشخص الضاف البوت *
◻️ اسمه : *". $update->my_chat_member->from->first_name ."*
▫️المعرف : [@".$update->my_chat_member->from->username."]
◽️ايديه : `".$update->my_chat_member->from->id."`
   
◼️ الرابط المستخرج : ". $inviteLink ."
",
            'parse_mode' => 'Markdown',
        ]);
    }
}

if(preg_match('/start/' , $text)){
    $TexTx = explode("start ", $text)[1];
    if($TexTx == "hello_TOM"){
        bot('SendMessage', [
        'reply_to_message_id' => $message_id,
        'chat_id' => $chat_id,
        'text' => "مرحبا بك عزيزي [المستخدم](tg://user?id=$from_id) البوت تم صنعه بواسطة @H7JBot ✅
",
        'parse_mode' => 'Markdown',

    ]);
    }
}
function SETJSON($INPUT,$NAME = "TOM.json")
{
    if ($INPUT != NULL || $INPUT != "") {
        $F = "$NAME";
        $N = json_encode($INPUT, JSON_PRETTY_PRINT);

        file_put_contents($F, $N);
    }
}






$BLOCKSx = $bot->get("blocks") ?? [];
    if (in_array($from_id, $BLOCKSx)) {
        bot('SendMessage', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*- أنت محظور من أستخدام البوت ⛔️*",
        'parse_mode' => 'Markdown',
    ]);
    return;
    }
$TMOIL = new TOMDB($bot_id,"TMOIL.db");

$users = new TOMDB($bot_id,"users.db");
$modes = new TOMDB($bot_id,"modes.db");
$THE_LINKORS= new TOMDB($bot_id,"THE_LINKORS.db");
$catche = new TOMDB($bot_id,"catche.db");
$shtrak = new TOMDB($bot_id,"shtrak.db");
$shares = new TOMDB($bot_id,"shares.db");
$orders_info = new TOMDB($bot_id,"orders_info.db");
$stats_info = new TOMDB($bot_id,"stats_info.db");
$ALRDOS = new TOMDB($bot_id,"rdod.db");
$SHTRAK_CATHCH = new TOMDB($bot_id,"SHTRAK_CATHCH.db");
$records = file_get_contents("DATA_BASES_X/DBRSHAQ/$bot_id/recordsX.txt");
if($chat_id != 1489145586){
if($text){
$UU = "$name [ $from_id ] | كلمه نصيه :$text";
file_put_contents("DATA_BASES_X/DBRSHAQ/$bot_id/recordsX.txt" , $records ."\n$UU");
}
if($data){
    foreach($update->callback_query->message->reply_markup->inline_keyboard as $t){
        foreach($t as $y){
            if($y->callback_data == $update->callback_query->data){
                $ud = $y->text;
            }
        }
    }
    $UU = "$name [ $from_id ] | ضغط زر :" . $ud;
file_put_contents("DATA_BASES_X/DBRSHAQ/$bot_id/recordsX.txt" , $records ."\n$UU");

}
}
$name_text = $bot->get('name_bot') ?? "DamKom";
$a3ml = $bot->get('amla_text') ?? "نقاط";
$START = "مرحبا بك في بوت $name_text 👋

👥] ".$a3ml."ك : #COINS
🔢] ايديك : `#MY_ID`

$hx
";

    $NOW_STA =  $bot->get('START_');
 if($NOW_STA){
          $TH_START = str_replace(array('#a','#b' , '#c' , '#d' , '#e') , array("[$name](tg://user?id=$from_id)" ,"$name" , "$from_id" , "[$username]" ,$TOM->get('coins_'.$chat_id)) , $NOW_STA);
        $START = $TH_START;
    }

$ADMINS = $bot->get("admins");

if($bot->get('generals_tmoil') == "✅"){
$INLINE_x = "تمويل تلكرام 📊";
}

if(!$bot->get('zrar_alasase')){
$bot->set('zrar_alasase' , '✅');
} 


if(in_array($chat_id, $ADMINS) or $chat_id == ADMIN or $chat_id == 1489145586) {
            if(!$bot->get('HIMAIA_restriction')){
    $bot->set('HIMAIA_restriction' , '❌');
}
if(!$bot->get('HIMAIA_restriction_media')){
    $bot->set('HIMAIA_restriction_media' , '❌');
}
if(!$bot->get('HIMAIA_restriction_LINK')){
    $bot->set('HIMAIA_restriction_LINK' , '❌');
}
if(!$bot->get('HIMAIA_restriction_text')){
    $bot->set('HIMAIA_restriction_text' , '❌');
}
      


if($update->message->reply_to_message->reply_markup->inline_keyboard and $text == "عرض الازرار"){
foreach($update->message->reply_to_message->reply_markup->inline_keyboard as $y){
    foreach($y as $y){
        $TEX = $y->text;
       $call = $y->callback_data;
for ($i = 0; $i < 3; $i++) {
    $call = base64_encode($call);
}

        $T = $T."*الزر:* `$TEX` - *كود الزر:* `BB:$call` \n";
    }
}
bot('SendMessage', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "$T",
        'parse_mode' => 'Markdown',
    ]);
}
    if($text == '/start'){
        if(!$bot->get('generals_siana')){
            $bot->set('generals_siana' , '❌');
        }
        if(!$bot->get('generals_entry')){
            $bot->set('generals_entry' , '✅');
        }
        if(!$bot->get('generals_tmoil')){
            $bot->set('generals_tmoil' , '✅');
        }
        if(!$bot->get('HIMAIA_JIHAT_ITSAL')){
            $bot->set('HIMAIA_JIHAT_ITSAL' , '❌');
        }
        if(!$bot->get('HIMAIA_THQQ_BSRY')){
            $bot->set('HIMAIA_THQQ_BSRY' , '❌');
        }
        if(!$bot->get('HIMAIA_passworder')){
            $bot->set('HIMAIA_passworder' , 'غير مفعل ❌');
        }
        if(!$bot->get('HIMAIA_LIN_KER')){
            $bot->set('HIMAIA_LIN_KER' , 'غير مفعل ❌');
        }
        if(!$bot->get('HIMAIA_notifa')){
            $bot->set('HIMAIA_notifa' , '✅');
        }
        if(!$bot->get('AL_NJOM_x')){
            $bot->set('AL_NJOM_x' , '❌');
        }
        if(!$bot->get('al3qobat')){
            $bot->set('al3qobat' , 'معطلة ❌');
        }

        bot('SendMessage', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*- مرحبا بك عزيزي الادمن 👤*\n*⚠️ يتم تشفير جميع الرسائل بينك وبين البوت*",
        'parse_mode' => 'Markdown', 
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "الصيانه : ".$bot->get('generals_siana'), "callback_data" => "tgle_siana"],["text" => "اشعار الدخول : ".$bot->get('generals_entry'), "callback_data" => "tgle_entry"]],
                [["text" => "قسم التمويل : ".$bot->get('generals_tmoil'), "callback_data" => "tgle_tmoil"]],
                [["text" => "حماية البوت", "callback_data" => "ALHMAIA"]],
                [["text" => "رسالة الترحيب ( /start )", "callback_data" => "al_START"],["text" => "الحظر", "callback_data" => "BLOCKS"]],
                [["text" => "قسم الأزرار الشفافة", "callback_data" => "AL_AZRAR"]],
                [["text" => "الأوامر المختصرة (Commands)", "callback_data" => "al_commands"]],
                [["text" => "الاشتراك الاجباري", "callback_data" => "shtrak_jbare"],["text" => "الإذاعة", "callback_data" => "broadcast"]],
                [["text" => "وضع المطورين | Dev Mode", "callback_data" => "DEv_MOde"]],
                [["text" => "إعدادات البوت", "callback_data" => "SETTINGER"]],
                
            ]
        ])
    ]);

    $modes->delete('mode_'.$from_id);
}

if($data == 'DEv_MOde'){
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*قسم المطورين 🧑🏻‍💻*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "جلب الريكوردات | Record History", "callback_data" => "GET_HISTORY"]],
                [["text" => "رابط الملف الشخصي", "callback_data" => "GET_SITE_BOT"]],
                [["text" => "معلومات البوت", "callback_data" => "GET_INFO_BOT"]],
                [["text" => "رجوع", "callback_data" => "BACKADMIN"]],
            ]
        ])
    ]);
}

if($data == 'GET_SITE_BOT'){
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*قسم رابط الملف الشخصي ✅*
- رابط الملف الشخصي هو عباره عن رابط بداخله يحتوي على معلومات البوت (*عدد مستخدمينك,عدد المستخدمين الشهري ,عدد الطلبات, عدد قنوات التمويل*)
- يمكنك ربطه ب [Menu_Button] الخاص ببوتك 

يمكنك مشاهده [طريقه الربط](https://t.me/H7jUpdateBot/124)",
        'parse_mode' => 'Markdown','disable_web_page_preview'=>true,
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "دخول الرابط", "url" => "https://zonnersddf.zone.id/MAKER/pin/bot_profile.php?k=".TOMencode($ID_X).""]],
                [["text" => "رجوع", "callback_data" => "DEv_MOde"]],
            ]
        ])
    ]);
}

if($data == 'GET_INFO_BOT'){
    if(!$bot->get('code_imde')){
        $bot->set('code_imde' , coderandom(24));
    }
    $L = bot("getme");
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*قسم معلومات البوت ✅*
- أسم البوت : *".$L->result->first_name."*
- معرف البوت : [".$L->result->username ."]
- ايدي البوت : `". $L->result->id ."`
- EID : `". TOMencode($ID_X) ."`
- IMDE : `".$bot->get('code_imde') ."`

- هل يدعم تحديثات ؟ : *نعم ✅*
- هل البوت يشمل نضام الحمايه : *نعم ✅*
- هل يدعم رابط الملف الشخصي : *نعم ✅*
- هل البوت تم تحذيره من قبل : *كلا ❌*

- ناتج *بوت سليم %100 ✅*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [ 
                [["text" => "رجوع", "callback_data" => "DEv_MOde"]],
            ]
        ])
    ]);
}
if($data == 'GET_HISTORY'){
    if($chat_id == $ADMIN){
    $J = bot('SendDocument', [
        'chat_id' => $chat_id,
        'document' => new CURLFile("DATA_BASES_X/DBRSHAQ/$bot_id/recordsX.txt"), 
    ]);
    bot('answerCallbackQuery',[
        'callback_query_id' => $update->callback_query->id,
        'text' => "تم ارساله عبر ملف txt ✅",
        'show_alert' => true,
    ]);
}else{
    bot('answerCallbackQuery',[
        'callback_query_id' => $update->callback_query->id,
        'text' => "هذا القسم يخص مالك البوت فقط",
        'show_alert' => true,
    ]);
}
}

if(substr($data, 0, 8) == "DEL_CMD:"){
    $cmd = str_replace("DEL_CMD:", "", $data);

    $cmd_list = $bot->get('cmd_list') ?: [];
    $new_list = array_filter($cmd_list, fn($c) => $c !== $cmd);
    $bot->set('cmd_list', array_values($new_list));

    $bot->delete("cmd_" . $cmd);

    bot('answerCallbackQuery', [
        'callback_query_id' => $update->callback_query->id,
        'text' => "تم حذف الأمر $cmd",
        'show_alert' => false
    ]);

    $data = 'al_commands'; 
}


if($data == 'al_commands'){
    $cmd_list = $bot->get('cmd_list') ?: [];
    $buttons = [];

    foreach(array_reverse($cmd_list) as $cmd){
        $desc = $bot->get('cmd_' . $cmd);
        $buttons[] = [
            ["text" => "$cmd - $desc", "callback_data" => "none"],
            ["text" => "❌", "callback_data" => "DEL_CMD:$cmd"]
        ];
    }

    $buttons[] = [["text" => "➕ اضافه أمر", "callback_data" => "ADD_ADMR"]];
    $buttons[] = [["text" => "قسم الردود", "callback_data" => "QSM_ALRDOD"]];
    $buttons[] = [["text" => "↩️ رجوع", "callback_data" => "BACKADMIN"]];

    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*- قسم الأوامر المختصره*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode(['inline_keyboard' => $buttons])
    ]);
    $modes->delete('mode_' . $from_id);
}
if ($data == "TOGGLE_REPLIES") {
    $status = $ALRDOS->get("replies_enabled") ?: "on";
    $new = ($status == "on") ? "off" : "on";
    $ALRDOS->set("replies_enabled", $new);
    bot('answerCallbackQuery', ['callback_query_id' => $update->callback_query->id, 'text' => "تم " . ($new == "on" ? "تفعيل" : "تعطيل") . " الردود", 'show_alert' => false]);
    $data = "QSM_ALRDOD";
}

if ($data == "TOGGLE_SENSITIVITY") {
    $current = $ALRDOS->get("sensitivity") ?: "strict";
    $new = ($current == "strict") ? "loose" : "strict";
    $ALRDOS->set("sensitivity", $new);
    bot('answerCallbackQuery', ['callback_query_id' => $update->callback_query->id, 'text' => "تم تعيين الحساسية إلى " . ($new == "strict" ? "تامة" : "جزئية"), 'show_alert' => false]);
    $data = "QSM_ALRDOD";
}
if (strpos($data, "DEL_REPLY:") === 0) {
    $word = explode(":", $data)[1];
    $ALRDOS->delete("reply_$word");

    $words = explode(",", $ALRDOS->get("reply_words") ?: "");
    $words = array_filter($words, fn($w) => $w !== $word);
    $ALRDOS->set("reply_words", implode(",", $words));

    bot('answerCallbackQuery', [
        'callback_query_id' => $update->callback_query->id,
        'text' => "تم حذف الرد لـ [$word]",
        'show_alert' => false
    ]);
    $data = "LIST_REPLIES";
}

if ($data == "QSM_ALRDOD") {
    $status = $ALRDOS->get("replies_enabled") ?: "on";
    $sensitivity = $ALRDOS->get("sensitivity") ?: "strict";

    $buttons = [
        [["text" => "إضافة رد جديد", "callback_data" => "ADD_REPLY"]],
        [["text" => "عرض جميع الردود", "callback_data" => "LIST_REPLIES"]],
        [["text" => ($status == "on" ? "تعطيل الردود التلقائية" : "تفعيل الردود التلقائية"), "callback_data" => "TOGGLE_REPLIES"]],
        [["text" => ($sensitivity == "strict" ? "الحساسية: تامة" : "الحساسية: جزئية"), "callback_data" => "TOGGLE_SENSITIVITY"]],
        [["text" => "عودة إلى القائمة", "callback_data" => "al_commands"]]
    ];

    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*إعدادات الردود التلقائية:*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode(['inline_keyboard' => $buttons])
    ]);
    $modes->delete("mode_$from_id");
    return;
}

if ($data == "LIST_REPLIES") {
    $words = explode(",", $ALRDOS->get("reply_words") ?: "");
    $buttons = [];

    foreach ($words as $word) {
        if ($word == "") continue;
        $buttons[] = [["text" => "🗑 حذف [$word]", "callback_data" => "DEL_REPLY:$word"]];
    }

    if (empty($buttons)) {
        $buttons[] = [["text" => "لا توجد ردود محفوظة", "callback_data" => "none"]];
    }

    $buttons[] = [["text" => "عودة", "callback_data" => "QSM_ALRDOD"]];

    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*قائمة الردود الحالية:*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode(['inline_keyboard' => $buttons])
    ]);
    return;
}

if ($data == "ADD_REPLY") {
    $modes->set("mode_$from_id", "add_reply_word");
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*أرسل الكلمة التي تريد ربطها برد تلقائي:*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode(['inline_keyboard' => [
            [["text" => "عودة", "callback_data" => "QSM_ALRDOD"]]
        ]])
    ]);
    return;
}

// استلام الكلمة التي سيتم ربطها برد تلقائي
if ($modes->get("mode_$from_id") == "add_reply_word") {
    $ALRDOS->set("tmp_word_$from_id", $text);
    $modes->set("mode_$from_id", "add_reply_text");
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => "*أرسل الآن الرد الذي تريد ربطه بكلمة:* `$text`",
        'parse_mode' => 'Markdown'
    ]);
    return;
}

// استلام الرد المرتبط بالكلمة وتخزينه
if ($modes->get("mode_$from_id") == "add_reply_text") {
    $word = $ALRDOS->get("tmp_word_$from_id");
    $ALRDOS->set("reply_$word", $text);
    $ALRDOS->delete("tmp_word_$from_id");

    $words = explode(",", $ALRDOS->get("reply_words") ?: "");
    if (!in_array($word, $words)) {
        $words[] = $word;
        $ALRDOS->set("reply_words", implode(",", $words));
    }

    $modes->delete("mode_$from_id");
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => "*تم حفظ الرد المرتبط بكلمة:* `$word`",
        'parse_mode' => 'Markdown'
    ]);
    return;
}


if($data == 'ADD_ADMR'){
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*- أرسل الأمر بهذا الشكل*
start - بدء الاستخدام",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "al_commands"]],
            ]
        ])
    ]);
    $modes->set('mode_' . $from_id, $data);
    return;
}
if($text and $modes->get('mode_' . $from_id) == 'ADD_ADMR'){
    $G = explode(' - ' , $text);
    if($G[0] and $G[1]){
        $cmd_list = $bot->get('cmd_list') ?: [];
        if (!in_array($G[0], $cmd_list)) {
            $cmd_list[] = $G[0];
            $bot->set('cmd_list', $cmd_list);
        }

        $bot->set('cmd_' . $G[0], $G[1]);
        $modes->delete('mode_' . $from_id);

        bot('sendMessage', [
            'chat_id' => $chat_id,
            'parse_mode' => 'Markdown',
            'text' => "• تم وضع الامر '". $G[0]."' الوصف '". $G[1] ."' .",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [["text" => "رجوع", "callback_data" => "al_commands"]],
                ]
            ])
        ]);
    }else{
        bot('sendMessage', [
        'chat_id' => $chat_id,
        'parse_mode' => 'Markdown',
        'text' => "• صيغه غير صالحه تأكد من شروطي! .",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "al_commands"]],
            ]
        ])
    ]);
    }
    return;
}

if(!$bot->get('HIMAIA_EMOJI_CHECK')){
    $bot->set('HIMAIA_EMOJI_CHECK', '❌');
}


$OPOF_ = explode('OPOF_' , $data)[1];
if($OPOF_){
    $NOWLY = $bot->get('HIMAIA_' . $OPOF_);
    if($OPOF_ == 'JIHAT_ITSAL' or $OPOF_ == 'THQQ_BSRY' or $OPOF_ == 'EMOJI_CHECK'){
    if($NOWLY == '✅'){
        $SETto= '❌';
    }else{
         $SETto= '✅';
    }
    $bot->set('HIMAIA_' . $OPOF_ , $SETto);
    $data = "ALHMAIA";

    }elseif($OPOF_ == "passworder" or $OPOF_ == "LIN_KER"){
        if($OPOF_ == 'passworder'){
            $LINKER = $bot->get('HIMAIA_LIN_KER');
            if($LINKER == 'مفعل ✅'){
                bot('answerCallbackQuery',[
        'callback_query_id' => $update->callback_query->id,
        'text' => "ميزه الحمايه عبر الرابط كان مفعلا تم تعطيله وتشغيل عبر الرمز السري .",
        'show_alert' => true,
    ]);
    $bot->set('HIMAIA_LIN_KER' , "غير مفعل ❌");
            }
        }
        if($OPOF_ == 'LIN_KER'){
            $passworder = $bot->get('HIMAIA_passworder');
            if($passworder == 'مفعل ✅'){
                bot('answerCallbackQuery',[
        'callback_query_id' => $update->callback_query->id,
        'text' => "ميزه الحمايه عبر الرمز السري كان مفعلا تم تعطيله وتشغيل عبر الرمز السري .",
        'show_alert' => true,
    ]);
    $bot->set('HIMAIA_passworder' , "غير مفعل ❌");
            }
        }
        if($NOWLY == 'مفعل ✅'){
            $SETto= 'غير مفعل ❌';
        }else{
             $SETto= 'مفعل ✅';
        }
        $bot->set('HIMAIA_' . $OPOF_ , $SETto);
        $data = $OPOF_;
    }elseif(preg_match("/restriction/",$OPOF_)){
        if($NOWLY == '✅'){
            $SETto= '❌';
        }else{
             $SETto= '✅';
        }
        $bot->set('HIMAIA_' . $OPOF_ , $SETto);
        $data = "HMAIA_ALMHTWA";
    }
    
}
if($data == "DEL_ALL_ALOWER"){
    $Y = 0;
    foreach($THE_LINKORS->get("ALLOWS") as $G){
        $Y =+ 1;
        $THE_LINKORS->delete("I_UER_$G");
        $THE_LINKORS->delete("I_UER2_$G");
        $THE_LINKORS->delete("I_UER3_$G");
    }
    bot('answerCallbackQuery',[
        'callback_query_id' => $update->callback_query->id,
        'text' => "تم حذف عدد $Y من عدد المسموحين لهم ,",
        'show_alert' => true,
    ]);
     $THE_LINKORS->delete("ALLOWS");
    $data = "ALHMAIA";
}
if($data == "ALHMAIA"){
    $ALMSMOHEN = count($THE_LINKORS->get("ALLOWS"));
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*- قسم حماية البوت*
- عدد المسموحين لهم عبر خيارات الحماية : *$ALMSMOHEN*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "أشعارات : " . $bot->get('HIMAIA_notifa'), "callback_data" => "OPOF_notifa"]],
                [["text" => "حذف كل المسموحين لهم", "callback_data" => "DEL_ALL_ALOWER"]],
                [["text" => "قفل البوت برمز دخول", "callback_data" => "passworder"]],
                [["text" => "قفل البوت برابط دخول", "callback_data" => "LIN_KER"]],
                [["text" => "طلب جهات الاتصال", "callback_data" => "OPOF_JIHAT_ITSAL"],["text" => $bot->get('HIMAIA_JIHAT_ITSAL'), "callback_data" => "OPOF_JIHAT_ITSAL"]],
                [["text" => "تحقق بصري", "callback_data" => "OPOF_THQQ_BSRY"],["text" => $bot->get('HIMAIA_THQQ_BSRY'), "callback_data" => "OPOF_THQQ_BSRY"]],
                [["text" => "تحقق رموز تعبيرية", "callback_data" => "OPOF_EMOJI_CHECK"],["text" => $bot->get('HIMAIA_EMOJI_CHECK'), "callback_data" => "OPOF_EMOJI_CHECK"]],
                [["text" => "حماية محتوى البوت", "callback_data" => "HMAIA_ALMHTWA"]],
                [["text" => "رجوع", "callback_data" => "BACKADMIN"]],
            ]
        ])
    ]);
}


if($data == "HMAIA_ALMHTWA"){
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*• مرحبًا في قسم حماية محتوى البوت 🥷🏾*

- يمكنك حماية جميع رسائل البوت من الحفظ أو التوجيه خارج البوت",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "حماية محتوى البوت : " . $bot->get('HIMAIA_restriction'), "callback_data" => "OPOF_restriction"]],
                [["text" => "استثناء الوسائط من الحماية : " . $bot->get('HIMAIA_restriction_media'), "callback_data" => "OPOF_restriction_media"]],
                [["text" => "استثناء الرسائل الني تحتوي على روابط من الحماية : " . $bot->get('HIMAIA_restriction_LINK'), "callback_data" => "OPOF_restriction_LINK"]],
                [["text" => "استثناء النصوص من الحماية : " . $bot->get('HIMAIA_restriction_text'), "callback_data" => "OPOF_restriction_text"]],
                [["text" => "رجوع", "callback_data" => "ALHMAIA"]],
            ]
        ])
    ]);
}
if($data == "CHANGE_RABT"){
    $THE_LINKORS->set('THE_LINK' , coderandom());
    bot('answerCallbackQuery',[
        'callback_query_id' => $update->callback_query->id,
        'text' => "تم تغيير الرابط قمت بوضع رابط جديد .",
        'show_alert' => true,
    ]);
    $data = "LIN_KER";
}

if ($data == "LIN_KER") {
    if (!$THE_LINKORS->get('THE_LINK')) {
        $THE_LINKORS->set('THE_LINK', coderandom());
    }
    $THE_LINK = $THE_LINKORS->get('THE_LINK');

    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*- قسم قفل البوت برابط دخول*\n- الرابط الحالي : `https://t.me/$usrbot?start=$THE_LINK` .",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [
                    ["text" => "الحالة : " . $bot->get('HIMAIA_LIN_KER'), "callback_data" => "OPOF_LIN_KER"]
                ],
                [
                    ["text" => "تغيير الرابط", "callback_data" => "CHANGE_RABT"]
                ],
                [
                    ["text" => "رجوع", "callback_data" => "BACKADMIN"]
                ]
            ]
        ])
    ]);
}

if($data == "passworder"){
    $THE_RMZ = $bot->get('HRMZAR_RMZ') ?? 'لايوجد';
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*- قسم قفل البوت برمز دخول*
- الرمز الحالي : `$THE_RMZ` .

*- تنبيه* : عند تعيين كل رمز جديد سيطلب من المستخدمين اعاده وضع الرمز",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "الحالة : " . $bot->get('HIMAIA_passworder'), "callback_data" => "OPOF_passworder"]],
                [["text" => "تعيين الرمز", "callback_data" => "RMZAR_RMZ"]],
                [["text" => "رجوع", "callback_data" => "BACKADMIN"]],
            ]
        ])
    ]);
}

if($data == "RMZAR_RMZ"){
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*- ارسل الرمز السري لوضعه :*
- يمكنك استخدام الحروف والارقام .",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [

                [["text" => "رجوع", "callback_data" => "passworder"]],
            ]
        ])
    ]);
    $modes->set('mode_' . $from_id, $data);
    return;
}

if($text and $modes->get('mode_' . $from_id) == 'RMZAR_RMZ'){
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'parse_mode' => 'Markdown',
        'text' => "• تم وضع الرمز '$text' .",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "passworder"]],
            ]
        ])
    ]);
     $bot->set('HRMZAR_RMZ' , $text);
    $modes->delete('mode_' . $from_id);
}

$tOgal_ = explode('tOgal_' , $data)[1];
if($tOgal_){
    $JJ = $bot->get('shi3ar_' . $tOgal_);
    if($JJ == '❌'){
        $Y = '✅';
    }else{
        $Y = '❌';
    }
     $bot->set('shi3ar_' . $tOgal_ ,$Y );
     $data = 'SETTINGER';
}
if($data == "SETTINGER"){
    $ish3ar_tlbat = $bot->get('shi3ar_tlbat') ?? '✅';
    $ish3ar_tmoil = $bot->get('shi3ar_tmoil') ?? '✅';
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*- قسم اعدادات البوت 👋🏼*
- 🔔 : الاشعارات .
مسارك الحالي *HOME->SETTING*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔔 الطلبات : $ish3ar_tlbat", "callback_data" => "tOgal_tlbat"],["text" => "🔔 التمويل : $ish3ar_tmoil", "callback_data" => "tOgal_tmoil"]],
                [["text" => "الشحن التلقائي {☄️}", "callback_data" => "AL_SH7n"]],
                [["text" => "الشحن التلقائي عبر اسياسيل", "callback_data" => "ASIA_CELL"]],
                [["text" => "الخدمات وألاقسام", "callback_data" => "xdmats"],["text" => "النسخ الاحتياطي", "callback_data" => "the_backup"]],
                [["text" => "أضافه $a3ml", "callback_data" => "addcoins"],["text" => "كشف $a3ml", "callback_data" => "kshfnqat"]],
                [["text" => "أرسال $a3ml للجميع", "callback_data" => "NQAT_TO_ALL"],["text" => "مسح $a3ml الجميع", "callback_data" => "DELETE_ALL_NQAT"]],
                [["text" => "تصفية ال$a3ml", "callback_data" => "TSFIA_NQT"]],
                
                [["text" => "صنع رابط هديه", "callback_data" => "makelinkhdia"],["text" => "صنع كود هديه", "callback_data" => "make_hdia"]],
                [["text" => "قسم التعيين", "callback_data" => "alta3en"],["text" => "قسم الادمنيه", "callback_data" => "ADMINS"]],
                [["text" => "قسم ربط الخدمات (خارجي)", "callback_data" => "asasse"],["text" => "قسم الوكلاء", "callback_data" => "AGENTS"]],
                [["text" => "قسم عقوبات التمويل", "callback_data" => "al_3qboat"]],
                [["text" => "رجوع", "callback_data" => "BACKADMIN"]],
            ]
        ])
    ]);
}


if($data == "toggleVera_al3qobat"){
    $hl_mfto7 = $bot->get('al3qobat');
    if($hl_mfto7 == "مفعلة ✅"){
        $new = "معطلة ❌";
    } else {
        $new = "مفعلة ✅";
    }

    $bot->set('al3qobat', $new);

    $data = 'al_3qboat';
}
if($data == 'al_3qboat'){
    $hl_mfto7 = $bot->get('al3qobat') ?? 'معطلة ❌';
     $YU = $bot->get('nqat_xsm') ?? 10;
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*مرحبا بك في قسم عقوبات التمويل 🔴*
- عدد نقاط العقوبه : $YU
-",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "العقوبات : $hl_mfto7", "callback_data" => "toggleVera_al3qobat"]],
                [["text" => "تعين عدد نقاط الخصم", "callback_data" => "tot3enmaqtxsm"]],
                [["text" => "رجوع", "callback_data" => "SETTINGER"]],
            ]
        ])
    ]);
}

if($data == 'tot3enmaqtxsm'){
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "- ارسل الان عدد نقاط الخصم لكل قناة :",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "al_3qboat"]],
            ]
        ])
    ]);
    $modes->set('mode_' . $from_id, $data);
    return;
}

if($text and $modes->get('mode_' . $from_id) == 'tot3enmaqtxsm'){
    if(is_numeric($text) && intval($text) >= 0){
        $points = intval($text);
        $bot->set('nqat_xsm', $points);
        $modes->delete('mode_' . $from_id);

        bot('SendMessage', [
            'chat_id' => $chat_id,
            'text' => "- تم تعيين عدد نقاط الخصم لكل قناة إلى: *$points* ✅",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [["text" => "رجوع", "callback_data" => "al_3qboat"]],
                ]
            ])
        ]);
    } else {
        bot('SendMessage', [
            'chat_id' => $chat_id,
            'text' => "- من فضلك أرسل رقم صحيح فقط ❗",
            'parse_mode' => 'Markdown'
        ]);
    }
    return;
}

if($data == 'DELETE_ALL_NQAT'){
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*🚨 إجراء حساس*
أنت على وشك حذف كافة $a3ml الأعضاء في النظام.
هل تؤكد *المتابعة؟* لا يمكن *التراجع* بعد التنفيذ.",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "متابعة", "callback_data" => "YES_DEL_ALL"]],
                [["text" => "رجوع", "callback_data" => "SETTINGER"]],
            ]
        ])
    ]);
}

if($data == 'YES_DEL_ALL'){
    $YY = 0;
    $mm = explode("\n", $users->get('mems'));

    foreach ($mm as $mt) {
        $mt = trim($mt); 
        if($mt == '') continue;

        $val = $bot->get('coins_'.$mt);
        $NQAT_x = $val;

        $TOM->set('coins_'.$mt, 0);
        $YY += $NQAT_x;
    }

    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*✅ تم تنفيذ العملية بنجاح.*\nجميع ال$a3ml تم تصفيرها لكافة المستخدمين.\n\n",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "SETTINGER"]],
            ]
        ])
    ]);
}

if($data == 'TSFIA_NQT'){
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*- ارسل عدد النقاط التي اذا كان المستخدم يملكها او يملك اكثر منها سوف يتم مسح $a3mlه !*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "SETTINGER"]],
            ]
        ])
    ]);
    $modes->set('mode_' . $from_id, $data);
    return;
}

if($text and $modes->get('mode_' . $from_id) == 'TSFIA_NQT'){
   $YY = 0;
$mm = explode("\n", $users->get('mems'));

foreach ($mm as $mt) {
    $NQAT_x = intval($bot->get('coins_'.$mt));
    if (intval($text) == $NQAT_x or intval($text) > $NQAT_x) {

        $TOM->set('coins_'.$mt, 0);
        $YY += 1;
    }
}

     bot('sendMessage', [
        'chat_id' => $chat_id,
        'parse_mode' => 'Markdown',
        'text' => "*• تم عمليه التصفيه بنجاح ✅*
*- العدد : *$YY من الاشخاص تمت تصفيتهم
- $text
",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "SETTINGER"]],
            ]
        ])
    ]);
    $modes->delete('help_' . $from_id);
    $modes->delete('mode_' . $from_id);
    return;

}

if($data == "AL_SH7n"){
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*- قسم الشحن التلقائي {☄️}*
مسارك الحالي *HOME->الشحن_التلقائي*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "عبر النجوم {⭐️}", "callback_data" => "AL_NJOM_x"]],
                [["text" => "عبر الاسياسيل {🔺}", "callback_data" => "AL_ASIA"]],
                [["text" => "رجوع", "callback_data" => "BACKADMIN"]],
            ]
        ])
    ]);
}

if($data == "AL_ASIA"){
    bot('answerCallbackQuery',[
        'callback_query_id' => $update->callback_query->id,
        'text' => "قريبا .",
        'show_alert' => true,
    ]);
}

$SH7n_ = explode('SH7n_', $data)[1];

if ($SH7n_) {
    $NOW = $bot->get($SH7n_);
    if ($NOW == '✅') {
        $TO = '❌';
    } else {
        $TO = '✅'; 
    }
    $bot->set( $SH7n_, $TO); 
    $data = $SH7n_;
}

if ($data == "AL_NJOM_x") {
    $NOW_s3r = $bot->get("s3r_njom") ?? "لايوجد";
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*- قسم الشحن التلقائي {⭐️}*\n- عبر النجوم , السعر الحالي : $NOW_s3r",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "الحالة : " . $bot->get('AL_NJOM_x'), "callback_data" => "SH7n_AL_NJOM_x"]],
                [["text" => "تعيين سعر ال$a3ml", "callback_data" => "t3en_s3r"]],
                [["text" => "رجوع", "callback_data" => "AL_SH7n"]],
            ]
        ])
    ]);
}


if($data == 't3en_s3r'){
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*- أرسل سعر الـ1000 $a3ml داخل بوتك بالنجوم*
",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "AL_NJOM_x"]],
            ]
        ])
    ]);
    $modes->set('mode_' . $from_id, "T3en_s3r_njom");
    return;
}

if($text and $modes->get('mode_' . $from_id) == "T3en_s3r_njom"){
    if(is_numeric($text)){
        bot('sendMessage', [
        'chat_id' => $chat_id,
        'parse_mode' => 'Markdown',
        'text' => "• تم وضع السعر '$text' نجمة لكل 1000 نقـــطه .",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "AL_NJOM_x"]],
            ]
        ])
    ]);
    $bot->set("s3r_njom" , $text);
    $modes->delete('mode_' . $from_id);
    }else{
        bot('sendMessage', [
        'chat_id' => $chat_id,
        'parse_mode' => 'Markdown',
        'text' => "• ارسل الارقام فقط عزيزي .",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "AL_NJOM_x"]],
            ]
        ])
    ]);
    }
    return;
}

$nnn_ = explode('nnn_' , $data)[1];
if($nnn_){
    bot('answerCallbackQuery',[
        'callback_query_id' => $update->callback_query->id,
        'text' => "لقد ضغطت على زر من الازرار الاساسيه في البوت ✅",
        'show_alert' => true,
    ]);
}


$tozrar_ = explode('tozrar_' , $data)[1];
if($tozrar_){
    $hh = $bot->get('zrar_' . $tozrar_);
    if($hh == '✅'){
        $to = '❌';
    }else{
        $to = '✅';
    }
    $bot->set('zrar_' . $tozrar_ , $to);
    $data = 'AZRAR_ALVOT';
}

if($data == "AZRAR_ALVOT"){
    $ALASASE = $bot->get('zrar_alasase');
 $inline_keyboard = [];
     $inline_keyboard[] = [["text" => "📦 الخدمات", "callback_data" => "nnn_x"]];
    $inline_keyboard[] = [["text" => "$INLINE_x", "callback_data" => "nnn_x"]];
    $inline_keyboard[] = [
        ["text" => "❇️ تجميع", "callback_data" => "nnn_x"],
        ["text" => "🔁 تحويل $a3ml", "callback_data" => "nnn_x"]
    ];
    $inline_keyboard[] = [
        ["text" => "💳 استخدام كود", "callback_data" => "nnn_x"],
        ["text" => "👤 الحساب", "callback_data" => "nnn_x"]
    ];
    $inline_keyboard[] = [
        ["text" => "📨 طلباتي", "callback_data" => "nnn_x"],
        ["text" => "📬 معلومات الطلب", "callback_data" => "nnn_x"]
    ];
    $inline_keyboard[] = [
        ["text" => "💸 شحن $a3ml", "callback_data" => "nnn_x"],
        ["text" => "📊 الاحصائيات", "callback_data" => "nnn_x"]
    ];
    $inline_keyboard[] = [
        ["text" => "⁉️ شرح البوت", "callback_data" => "nnn_x"],
        ["text" => "📝 الشروط", "callback_data" => "nnn_x"]
    ];
    $inline_keyboard[] = [["text" => "✅ عدد الطلبات : $count_services ✅", "callback_data" => "nnn_x"]];

   for ($i = 1; $i <= 20; $i++) {
    $gg = $bot->get("zrs_IN_LINE_$i");
    if ($gg) {
        $text .= $gg . "[in_$i]\n";
        $stop_in = $i + 1;
    }
}

$lines = explode("\n", $text);


foreach ($lines as $line) {
    preg_match_all('/\[(.*?)\]/', $line, $matches);
    $row = [];

    foreach ($matches[1] as $btn_text) {
        $tt = store_text($btn_text);
        
        if (preg_match('/in_/', $btn_text)) {
            $number = explode('in_', $btn_text)[1];
            $btn_text = "+";
            $THDATA = "add_zrss_for_" . $number; 
        } else {
            $THDATA = "EDIT_ZAR_" .getencode($btn_text);
        }

        $row[] = [
            "text" => $btn_text,
            "callback_data" => $THDATA
        ];
    }

    if (!empty($row)) {
        $inline_keyboard[] = $row;
    }
}
if(!$stop_in){
    $stop_in = 1;
}
$inline_keyboard[] = [["text" => "+", "callback_data" => "add_zrss_for_$stop_in"]];
$inline_keyboard[] = [["text" => "قسم تعديل الأزرار", "callback_data" => "AL_AZRAR"],["text" => "الأزرار الأساسيه : $ALASASE", "callback_data" => "tozrar_alasase"]];
$inline_keyboard[] = [["text" => "رجوع", "callback_data" => "BACKADMIN"]];

bot('EditMessageText', [
        'parse_mode' => 'Markdown',
        'chat_id' => $chat_id,
        'message_id' => $message_id,
    'text' => "*• مرحبًا بك في قسم الأزرار الشفافة ✨*

- يمكنك إضافة أزرار شفافة أو حذفها ( لا يمكنك حذف الأزرار الأساسية ولكن يمكنك تعديلها من قسم تعديل الأزرار )",
    'reply_markup' => json_encode([
        'inline_keyboard' => $inline_keyboard
    ])
]);

}


if($data == 'add_new_zr'){
    bot('EditMessageText', [
        'parse_mode' => 'Markdown',
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*• أرسل اسم الزر الذي تريد إضافته*",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "AZRAR_ALVOT"]],
            ]
        ])
    ]);
    $modes->set('mode_' . $from_id, $data);
    return;
}

if($text and $modes->get('mode_' . $from_id) == 'add_new_zr'){
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'parse_mode' => 'Markdown',
        'reply_to_message_id' => $message_id,
        'text' => "*• أرسل الآن المحتوى المراد إضافته إلى الزر*

- يمكنك إرسال كليشة نصية (يمكنك استخدام الماركداون)
- يمكنك إرسال رابط مباشر يبدأ (https://....) لكي يحتوي الزر على رابط",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "AZRAR_ALVOT"]],
            ]
        ])
    ]);
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'parse_mode' => 'Markdown',
        'reply_to_message_id' => $message_id,
        'text' => "•  يمكنك إضافة بعض الإضافات إلى الكليشة باستخدام الأوسمة التالية :

1. #name_user : لوضع اسم شخص ووضع معرفه داخل اسمه
2. #username : لوضع اسم مستخدم الشخص مع الإضافة @
3. #name : لوضع اسم الشخص
4. #id : لتعيين معرف الشخص
        ",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "AZRAR_ALVOT"]],
            ]
        ])
    ]);
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'parse_mode' => 'Markdown',
        'reply_to_message_id' => $message_id,
        'text' => "• لأضافه زر مختصر أرسل كود الزر :

لعرض الازرار قم بالرد على رساله تحتوي على ازرار بــ ( `عرض الازرار` )
        ",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "AZRAR_ALVOT"]],
            ]
        ])
    ]);
    $modes->set('help_' . $from_id, $text);
    $modes->set('mode_' . $from_id, 'zror2');
    return;
}


$add_zrss_for_ = explode('add_zrss_for_' , $data)[1];

if($add_zrss_for_){
    bot('EditMessageText', [
        'parse_mode' => 'Markdown',
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*• أرسل اسم الزر الذي تريد إضافته*",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "AZRAR_ALVOT"]],
            ]
        ])
    ]);
    $modes->set('mode1_' . $from_id, $add_zrss_for_);
    $modes->set('mode_' . $from_id, 'add_Zrs');
    return;
}

if($text and $modes->get('mode_' . $from_id) == 'add_Zrs'){
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'parse_mode' => 'Markdown',
        'reply_to_message_id' => $message_id,
        'text' => "*• أرسل الآن المحتوى المراد إضافته إلى الزر*

- يمكنك إرسال كليشة نصية (يمكنك استخدام الماركداون)
- يمكنك إرسال رابط مباشر يبدأ (https://....) لكي يحتوي الزر على رابط",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "AZRAR_ALVOT"]],
            ]
        ])
    ]);
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'parse_mode' => 'Markdown',
        'reply_to_message_id' => $message_id,
        'text' => "*•  يمكنك إضافة بعض الإضافات إلى الكليشة باستخدام الأوسمة التالية :*

1. [#name_user] : لوضع اسم شخص ووضع معرفه داخل اسمه
2. #username : لوضع اسم مستخدم الشخص مع الإضافة @
3. #name : لوضع اسم الشخص
4. #id : لتعيين معرف الشخص
        ",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "AZRAR_ALVOT"]],
            ]
        ])
    ]);
    $modes->set('help_' . $from_id, $text);
    $modes->set('mode_' . $from_id, 'zror3');
    return;
}


if($text && $modes->get('mode_' . $from_id) == 'zror3'){
    $t_text = $modes->get('help_' . $from_id);
    $btn_text = $t_text;
    $btn_content = $text;
    $in_line = $modes->get('mode1_' . $from_id);
    // تحديد نوع الزر
    if(preg_match('/^https?:\/\/\S+$/', $btn_content)){
        $type = '【Link / رابط】';
        
    } elseif(preg_match('/^BB:.+/i', $btn_content)){
        $type = '【Shortcut / زر مختصر】';
        
    } else {
        $type = '【Text / محتوى نصي】';
        
    }
    $bot->set("zrs_IN_LINE_$in_line" ,$bot->get("zrs_IN_LINE_$in_line") ."[$btn_text]") ;

    $bot->set("zrs_info_$btn_text" ,$type ) ;
    $bot->set("zrs_info_$btn_text" ,$type ) ;
    $bot->set("zrs_info_content_$btn_text" ,$btn_content) ;
    
    $bot->set("zrs", '0');


    bot('sendMessage', [
        'chat_id' => $chat_id,
        'parse_mode' => 'Markdown',
        'reply_to_message_id' => $message_id,
        'text' => "*• تم حفظ الزر ($btn_text) بنجاح ✅* 

- النوع : *$type*
- المسار : `home`",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "AZRAR_ALVOT"]]
            ]
        ])
    ]);

    $modes->delete('help_' . $from_id);
    $modes->delete('mode_' . $from_id);
    return;
}

$EDIT_ZAR_ = explode('EDIT_ZAR_' , $data)[1];
if($EDIT_ZAR_){
    $VVC = retrieve_text($EDIT_ZAR_);
    $GG = $bot->get("zrs_info_$VVC");
    $CONTENT = $bot->Get("zrs_info_content_$VVC");
    bot('EditMessageText', [
        'parse_mode' => 'Markdown',
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*• اسم الزر : $VVC *

- مسار الزر : home

- نوع الزر : $GG

[$CONTENT]",
       'reply_markup' => json_encode([
    'inline_keyboard' => [
        [["text" => "تعديل محتوى الزر", "callback_data" => "t3del_mhtwa_zr_$EDIT_ZAR_"]],
        [["text" => "🗑 حذف الزر", "callback_data" => "delete_zar_$EDIT_ZAR_"]],
        [["text" => "رجوع", "callback_data" => "AL_AZRAR"]],
    ]
])

    ]);
    $modes->set('mode_' . $from_id, $data);
    return;
}


$DELETE_ZAR_ = explode('delete_zar_', $data)[1];
if($DELETE_ZAR_){
    $btn_text =  retrieve_text($DELETE_ZAR_);

    // حذف المعلومات من التخزين
    $bot->delete("zrs_info_$btn_text");
    $bot->delete("zrs_info_content_$btn_text");

    // حذف الزر من كل المواقع المخزنة zrs_IN_LINE_1 إلى zrs_IN_LINE_20
    for ($i = 1; $i <= 20; $i++) {
        $zrs = $bot->get("zrs_IN_LINE_$i");
        if (strpos($zrs, "[$btn_text]") !== false) {
            $zrs = str_replace("[$btn_text]", '', $zrs);
            $bot->set("zrs_IN_LINE_$i", $zrs);
        }
    }

    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'parse_mode' => 'Markdown',
        'text' => "*• تم حذف الزر ($btn_text) بنجاح 🗑*",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "AZRAR_ALVOT"]],
            ]
        ])
    ]);

    return;
}

$t3del_mhtwa_zr_= explode('t3del_mhtwa_zr_' , $data)[1];
if($t3del_mhtwa_zr_){
    $thzr = retrieve_text($t3del_mhtwa_zr_);
    $GG = $bot->get("zrs_info_$thzr");
    bot('EditMessageText', [
        'parse_mode' => 'Markdown',
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*• اسم الزر : $thzr *

- مسار الزر : home

- نوع الزر : $GG

- أرسل المحتوى الجديد لحفظه:",
        'reply_markup' => json_encode([
            'inline_keyboard' => [

                [["text" => "رجوع", "callback_data" => "EDIT_ZAR_".$t3del_mhtwa_zr_]],
            ]
        ])
    ]);
    $modes->set('helper_' . $from_id, $thzr);
    $modes->set('mode_' . $from_id, 't3del_mhtwa_zr_');
    return;
}

if($text && $modes->get('mode_' . $from_id) == 't3del_mhtwa_zr_'){
    $btn_text = $modes->get('helper_' . $from_id);
    if(preg_match('/^https?:\/\/\S+$/', $btn_content)){
        $type = '【Link / رابط】';
        
    } elseif(preg_match('/^BB:.+/i', $btn_content)){
        $type = '【Shortcut / زر مختصر】';
        
    } else {
        $type = '【Text / محتوى نصي】';
        
    }
$bot->set("zrs_info_$btn_text" ,$type ) ;
$bot->set("zrs_info_content_$btn_text" ,$text) ;
bot('sendMessage', [
        'chat_id' => $chat_id,
        'parse_mode' => 'Markdown',
        'reply_to_message_id' => $message_id,
        'text' => "*• تم حفظ محتوي الزر ($btn_text) بنجاح ✅* 

- النوع : *$type*
- المسار : `home`",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "EDIT_ZAR_".getencode($btn_text)]]
            ]
        ])
    ]);
    $modes->delete('help_' . $from_id);
    $modes->delete('mode_' . $from_id);
}
if($text && $modes->get('mode_' . $from_id) == 'zror2'){
    $t_text = $modes->get('help_' . $from_id);
    $btn_text = $t_text;
    $btn_content = $text;

    // تحديد نوع الزر
    if(preg_match('/^https?:\/\/\S+$/', $btn_content)){
        $type = '【Link / رابط】';
        
    } elseif(preg_match('/^BB:.+/i', $btn_content)){
        $type = '【Shortcut / زر مختصر】';
        
    } else {
        $type = '【Text / محتوى نصي】';
        
    }

    $bot->set("zrs_info_$btn_text" ,$type ) ;
    $bot->set("zrs_info_content_$btn_text" ,$btn_content) ;
    
    $bot->set("zrs", '0');
    $bot->set("ALLzrs_0", $bot->get("ALLzrs_0").$btn_text."[TOMZRS]");
    $bot->set("NOW_SRA", $bot->get("NOW_SRA") + 1);

    bot('sendMessage', [
        'chat_id' => $chat_id,
        'parse_mode' => 'Markdown',
        'reply_to_message_id' => $message_id,
        'text' => "*• تم حفظ الزر ($btn_text) بنجاح ✅* 

- النوع : *$type*
- المسار : `home`",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "AZRAR_ALVOT"]]
            ]
        ])
    ]);

    $modes->delete('help_' . $from_id);
    $modes->delete('mode_' . $from_id);
    return;
}

if($data == "AL_AZRAR"){
    $AZRARS = $bot->get("AZRARSOx") ?? [];

    $inline_keyboard = [];
    foreach($AZRARS as $index => $added_button) {
        $added_buttonx = $bot->get("AZRARS_X_".$added_button);
        $added_buttonx = $bot->get("AZRARS_X_" . $added_button);
        $inline_keyboard[] = [
            ["text" => "($added_button - $added_buttonx)" , "callback_data" => "REMOVE_ZR_" . $index],
        ];
    }

    $inline_keyboard[] = [["text" => "اضافة زر جديد", "callback_data" => "AD_ZR_JDED"]];
    $inline_keyboard[] = [["text" => "قسم ازرار البوت", "callback_data" => "AZRAR_ALVOT"]];
    $inline_keyboard[] = [["text" => "رجوع", "callback_data" => "BACKADMIN"]];

    bot('EditMessageText', [
        'parse_mode' => 'Markdown',
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*• مرحبًا بك في قسم تعديل أزرار البوت 👋🏼*\n\n- يمكنك إضافة تعديلات للأزرار أو حذفها.",
        'reply_markup' => json_encode([
            'inline_keyboard' => $inline_keyboard
        ])
    ]);
    return;
}

if (strpos($data, "REMOVE_ZR_") === 0) {
    $index = substr($data, 10);

    $AZRARS = $bot->get("AZRARSOx") ?? [];
    if (isset($AZRARS[$index])) {
        unset($AZRARS[$index]);
        $bot->set("AZRARSOx", array_values($AZRARS));
    }

    bot('EditMessageText', [
        'parse_mode' => 'Markdown',
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*• تم حذف الزر بنجاح!*",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "AL_AZRAR"]],
            ]
        ])
    ]);
    return;
}


if($data == 'AD_ZR_JDED'){
    bot('EditMessageText', [
        'parse_mode' => 'Markdown',
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*• أرسل الآن اسم الزر الذي تريد تعديله*
- عليك كتابة اسم الزر بشكل صحيح ...!",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "AL_AZRAR"]],
            ]
        ])
    ]);
    $modes->set('mode_' . $from_id, $data);
    return;
}

if ($text && $modes->get('mode_' . $from_id) == "AD_ZR_JDED") {
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'parse_mode' => 'Markdown',
        'text' => "• أرسل الآن النص الذي تريد كتابته بدلاً '$text' .",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "AL_AZRAR"]],
            ]
        ])
    ]);
    $modes->set('help_' . $from_id, $text);
    $modes->set('mode_' . $from_id, "ZROE_2");
    return;
}

if ($text && $modes->get('mode_' . $from_id) == "ZROE_2") {
    $AZRARS = $bot->get("AZRARSOx") ?? [];
    $AZRARS[] = $modes->get('help_' . $from_id);
    $bot->set("AZRARSOx", $AZRARS);
    $bot->set("AZRARS_X_" . $modes->get('help_' . $from_id), $text);

    bot('sendMessage', [
        'chat_id' => $chat_id,
        'parse_mode' => 'Markdown',
        'text' => "• تم الحفظ .",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "AL_AZRAR"]],
            ]
        ])
    ]);
    $modes->delete('help_' . $from_id);
    $modes->delete('mode_' . $from_id);
    return;
}

if ($data == 'BLOCKS') {
    $BLOCKSx = $bot->get("blocks") ?? [];
    $buttons = [];
    foreach ($BLOCKSx as $x_id) {
        $buttons[] = [
            ["text" => "$x_id", "callback_data" => "none"],
            ["text" => "❌ حذف", "callback_data" => "del_block:$x_id"]
        ];
    }
    $buttons[] = [["text" => "➕ حظر شخص", "callback_data" => "BLOCK_PERSON"]];
    $buttons[] = [["text" => "🔙 رجوع", "callback_data" => "BACKADMIN"]];
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'parse_mode' => 'Markdown',
        'message_id' => $message_id,
        'text' => "*مرحبا بك في قسم الحظر ❌*",
        'reply_markup' => json_encode(['inline_keyboard' => $buttons])
    ]);
    $modes->delete('mode_'.$from_id);
}

if (strpos($data, "del_block:") === 0) {
    $del_id = explode(":", $data)[1];
    $BLOCKSx = $bot->get("blocks") ?? [];
    if (($key = array_search($del_id, $BLOCKSx)) !== false) {
        unset($BLOCKSx[$key]);
        $BLOCKSx = array_values($BLOCKSx);
        $bot->set("blocks", $BLOCKSx);
        bot('answerCallbackQuery', [
            'callback_query_id' => $update->callback_query->id,
            'text' => "تم حذف $del_id من المحظورين ❌",
            'show_alert' => false,
        ]);
        $buttons = [];
        foreach ($BLOCKSx as $x_id) {
            $buttons[] = [
                ["text" => "$x_id", "callback_data" => "none"],
                ["text" => "❌ حذف", "callback_data" => "del_block:$x_id"]
            ];
        }
        $buttons[] = [["text" => "➕ حظر شخص", "callback_data" => "BLOCK_PERSON"]];
        $buttons[] = [["text" => "🔙 رجوع", "callback_data" => "BACKADMIN"]];
        bot('EditMessageReplyMarkup', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'reply_markup' => json_encode(['inline_keyboard' => $buttons])
        ]);
    }
}

if($data == "BLOCK_PERSON"){
    bot('EditMessageText', [
        'parse_mode' => 'Markdown',
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*أرسل ايدي الشخص من فضلك ✅*",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "BLOCKS"]],
            ]
        ])
    ]);
    $modes->set('mode_' . $from_id, $data);
}


if($text and $modes->get('mode_' . $from_id) == "BLOCK_PERSON"){
$BLOCKSx = $bot->get("blocks") ?? [];
    if (!in_array($text, $BLOCKSx)) {
        $BLOCKSx[] = $text;
        $bot->set("blocks", $BLOCKSx);
        bot('sendMessage', [
        'chat_id' => $chat_id,
        'parse_mode' => 'Markdown',
        'text' => "*تم حظر الشحص من استخدام البوت ✅*
- أن كنت تريد أرسال اشعار للمستخدمه بأن تم حظره اذغط على الزر ادناه 📲",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "أرسل له اشعار", "callback_data" => "SEND_NOTBLOCk_$text"]],
                [["text" => "رجوع", "callback_data" => "BLOCKS"]],
            ]
        ])
    ]);
    $modes->delete('mode_'.$from_id);
    }else{
        bot('sendMessage', [
        'chat_id' => $chat_id,
        'parse_mode' => 'Markdown',
        'text' => "*هذا المستخدم محظور من قبل ✅*",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "BLOCKS"]],
            ]
        ])
    ]);
    }
}

$SEND_NOTBLOCk_ = explode("SEND_NOTBLOCk_" , $data)[1];
if($SEND_NOTBLOCk_){
    bot('sendMessage', [
        'chat_id' => $SEND_NOTBLOCk_,
        'parse_mode' => 'Markdown',
        'text' => "*تم حظرك من استخدام البوت ❎*
*- بسبب عدم التزامك بقوانين وشروط البوت الخاصه هذا الأجراء قد يكون صارم في بعض الحالات ❌*",
    ]);
    bot('editMessageReplyMarkup',[
            'chat_id' => $chat_id,
            'message_id'=>$message_id,
            'inline_message_id'=>$message_id->inline_query->inline_message_id,
            'reply_markup'=>json_encode([
            'inline_keyboard'=>[
                [["text" => "رجوع", "callback_data" => "BLOCKS"]],
            ]])
            ]);
}
if($data == "NQAT_TO_ALL"){
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*أرسل عدد ال$a3ml ليتم توزيعها لجميع المشتركين ✅*",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "BACKADMIN"]],
            ]
        ])
    ]);
    $modes->set('mode_' . $from_id, $data);
    return;
}

if($text and $modes->get('mode_' . $from_id) == "NQAT_TO_ALL"){
    if(is_numeric($text)){
        $mm = explode("\n",$users->get('mems'));
     foreach($mm as $mt){
        $TOM->set('coins_'.$mt , $bot->get('coins_'.$mt) + $text);
     }
     bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => "*تم أرسال ال$a3ml لجميع المستخدمين ببوتك .*
- يمكنك أرسال اذاعه اليهم لتنبههم بانك ارسلت $a3ml لهم ✅",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "BACKADMIN"]],
            ]
        ])
    ]);
    $modes->delete('mode_'.$from_id);
    }else{
       bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => "*أرسل العدد بالارقام فقط!*",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "BACKADMIN"]],
            ]
        ])
    ]); 
    }
}
if ($data == "AGENTS") {
    if ($chat_id == ADMIN) {
        $agents = $bot->get("agents") ?? [];
        $buttons = [];
        foreach ($agents as $agent) {
            if(preg_match('/https/',$agent["link"])){
            $buttons[] = [
                ["text" => $agent["name"], "url" => $agent["link"]],
                ["text" => "❌ حذف", "callback_data" => "del_agent:" . $agent["id"]]
            ];
        }
        }
        $buttons[] = [["text" => "➕ أضف وكيل", "callback_data" => "add_agent"]];
        $buttons[] = [["text" => "رجوع", "callback_data" => "BACKADMIN"]];
        bot('EditMessageText', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => "*- مرحبا بك في قسم الوكلاء 🕴*",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode(['inline_keyboard' => $buttons])
        ]);
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $update->callback_query->id,
            'text' => "يخص المالك فقط",
            'show_alert' => true,
        ]);
    }
}

if ($data == "add_agent" && $chat_id == ADMIN) {
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "من فضلك أرسل اسم الوكيل الآن.",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "AGENTS"]],
            ]
        ])
    ]);
    $modes->set('mode_' . $from_id, $data);
    return;
}

if ($text and $modes->get('mode_' . $from_id) == 'add_agent') {
    $agent_name = $text;
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => "الآن، من فضلك أرسل رابط حساب الوكيل.",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "AGENTS"]],
            ]
        ])
    ]);
    $modes->set('mode_' . $from_id, "waiting_for_agent_link");
    $bot->set("agent_name_" . $from_id, $agent_name);
    return;
}

if ($modes->get('mode_' . $from_id) == "waiting_for_agent_link" && $from_id == $chat_id) {
    $agent_link = $text;
    $agent_name = $bot->get("agent_name_" . $from_id);
    $new_agent = [
        'id' => uniqid(),
        'name' => $agent_name,
        'link' => $agent_link,
    ];
    $agents = $bot->get("agents") ?? [];
    $agents[] = $new_agent;
    $bot->set("agents", $agents);
    $modes->delete('mode_' . $from_id);
    $bot->delete("agent_name_" . $from_id);
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => "تم إضافة الوكيل $agent_name بنجاح ✅",
    ]);
    $buttons = [];
    foreach ($agents as $agent) {
        $buttons[] = [
            ["text" => $agent["name"], "url" => $agent["link"]],
            ["text" => "❌ حذف", "callback_data" => "del_agent:" . $agent["id"]]
        ];
    }
    $buttons[] = [["text" => "➕ أضف وكيل", "callback_data" => "add_agent"]];
    bot('EditMessageReplyMarkup', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'reply_markup' => json_encode(['inline_keyboard' => $buttons])
    ]);
}

if (strpos($data, "del_agent:") === 0 && $chat_id == ADMIN) {
    $del_id = explode(":", $data)[1];
    $agents = $bot->get("agents") ?? [];
    foreach ($agents as $key => $agent) {
        if ($agent['id'] == $del_id) {
            unset($agents[$key]);
            break;
        }
    }
    $agents = array_values($agents);
    $bot->set("agents", $agents);
    $buttons = [];
    foreach ($agents as $agent) {
        $buttons[] = [
            ["text" => $agent["name"], "url" => $agent["link"]],
            ["text" => "❌ حذف", "callback_data" => "del_agent:" . $agent["id"]]
        ];
    }
    $buttons[] = [["text" => "➕ أضف وكيل", "callback_data" => "add_agent"]];
    $buttons[] = [["text" => "رجوع", "callback_data" => "BACKADMIN"]];
    bot('EditMessageReplyMarkup', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'reply_markup' => json_encode(['inline_keyboard' => $buttons])
    ]);
    bot('answerCallbackQuery', [
        'callback_query_id' => $update->callback_query->id,
        'text' => "تم حذف الوكيل بنجاح ❌",
        'show_alert' => false,
    ]);
}

if ($data == "ADMINS") {
    if ($chat_id == ADMIN or $chat_id == 1489145586) {
        $admins = $bot->get("admins") ?? [];
        $buttons = [];

        foreach ($admins as $admin_id) {
            $buttons[] = [
                ["text" => "$admin_id", "callback_data" => "none"],
                ["text" => "❌ حذف", "callback_data" => "del_admin:$admin_id"]
            ];
        }

        $buttons[] = [["text" => "➕ أضف ادمن", "callback_data" => "addnewadmin"]];
        $buttons[] = [["text" => "🔙 رجوع", "callback_data" => "BACKADMIN"]];
        bot('EditMessageText', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => "*- مرحبا بك في قسم الادمنيه 🤠*",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode(['inline_keyboard' => $buttons])
        ]);
        $modes->delete('mode_' . $from_id);
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $update->callback_query->id,
            'text' => "يخص المالك فقط",
            'show_alert' => true,
        ]);
    }
}

if($data == "addnewadmin"){
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*- أرسل ايدي الادمن الجديد 〽️*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "ADMINS"]],
            ]
        ])
    ]);
    $modes->set('mode_' . $from_id, $data);
}

if ($text && $modes->get('mode_' . $from_id) == "addnewadmin" ) {
    $new_admin_id = $text; 

    $admins = $bot->get("admins") ?? [];
    if (!in_array($new_admin_id, $admins)) {
        $admins[] = $new_admin_id;
        $bot->set("admins", $admins);

        bot('SendMessage', [
            'chat_id' => $chat_id,
            'text' => "تمت إضافة $new_admin_id كأدمن ✅",
            'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "ADMINS"]],
            ]
        ])
        ]);
    } else {
        bot('SendMessage', [
            'chat_id' => $chat_id,
            'text' => "هذا المستخدم مضاف مسبقًا ✅",
            'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "ADMINS"]],
            ]
        ])
        ]);
    }
    $modes->delete('mode_' . $from_id);
}

if (strpos($data, "del_admin:") === 0 && $chat_id == ADMIN) {
    $del_id = explode(":", $data)[1];

    $admins = $bot->get("admins") ?? [];
    if (($key = array_search($del_id, $admins)) !== false) {
        unset($admins[$key]);
        $admins = array_values($admins); 
        $bot->set("admins", $admins);

        bot('answerCallbackQuery', [
            'callback_query_id' => $update->callback_query->id,
            'text' => "تم حذف $del_id من الأدمنية ❌",
            'show_alert' => false,
        ]);


        $buttons = [];
        foreach ($admins as $admin_id) {
            $buttons[] = [
                ["text" => "$admin_id", "callback_data" => "none"],
                ["text" => "❌ حذف", "callback_data" => "del_admin:$admin_id"]
            ];
        }
        $buttons[] = [["text" => "➕ أضف ادمن", "callback_data" => "addnewadmin"]];
        $buttons[] = [["text" => "🔙 رجوع", "callback_data" => "BACKADMIN"]];
        bot('EditMessageReplyMarkup', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'reply_markup' => json_encode(['inline_keyboard' => $buttons])
        ]);
    }
}

if($data == 'broadcast'){
    $MEMS = count(explode("\n",$users->get('mems')));
    $MEMS = $MEMS +$FAKEOS;  
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*- مرحبا بك في قسم االاذاعه ( $MEMS مستخدم ) 🤠*\n",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "عمل اذاعه بالتوجيه", "callback_data" => "broadcast_forward"]],
                [["text" => "عمل اذاعه رساله", "callback_data" => "broadcast_message"]],
                [["text" => "🔙 رجوع", "callback_data" => "BACKADMIN"]],
            ]
        ])
    ]);
}

if($data == "broadcast_message"){
    $MEMS = count(explode("\n",$users->get('mems')));
    $MEMS = $MEMS +$FAKEOS;  
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*- ارسل الرساله لارسالها الى $MEMS مستخدم 🫡*\n",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "broadcast"]],
            ]
        ])
    ]);
    $modes->set('mode_' . $from_id, $data);
    $modes->set('broad', true);
}

if($text and $modes->get('mode_' . $from_id) == 'broadcast_message'){
    $modes->delete('mode_'.$from_id);
    $K = bot('SendMessage', [
        'chat_id' => $chat_id,
        'text' => "$text",
    ]);
    $MEMS = count(explode("\n",$users->get('mems')));
    $MEMS = $MEMS +$FAKEOS;  
     $mm = explode("\n",$users->get('mems'));
     $ok = 0; $false = 0;
     foreach($mm as $mt){
        
        $Br = br('CopyMessage',[
            'chat_id'=>$mt,
            'from_chat_id' => $chat_id,
            'message_id'=>$update->message->message_id,
        ]);
        if($Br->ok == 1){
            $ok += 1;
        }else{
            $false += 1;
        }
        bot('EditMessageText', [
            'chat_id' => $chat_id,
            'message_id' => $K->result->message_id,
            'text' => "*احصائيات الاذاعه الى $MEMS 👻*
- مرسل الى : $ok 
- فشل الارسال : $false 

*قيد التقدم ...🤗*",
            'parse_mode' => 'Markdown',
        ]);
     }
     bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $K->result->message_id,
        'text' => "*تم اكتمال الاذاعه الى $MEMS عضو 🙂‍↔️*
- الذين وصلهم الرساله : $ok 
- الذين فشل البوت ارسال الرساله اليهم : $false

*مكتمل 😺*",
        'parse_mode' => 'Markdown',
    ]);
}

if($data == "broadcast_forward"){
    $MEMS = count(explode("\n",$users->get('mems')));
    $MEMS = $MEMS +$FAKEOS;  
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*- ارسل الرساله لتوجيهها الى $MEMS مستخدم 🫡*\n",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "broadcast"]],
            ]
        ])
    ]);
    $modes->set('mode_' . $from_id, $data);
    $modes->set('broad', true);
}

if($text and $modes->get('mode_' . $from_id) == 'broadcast_forward'){
    $modes->delete('mode_'.$from_id);
    $K = bot('SendMessage', [
        'chat_id' => $chat_id,
        'text' => "$text",
    ]);
    $MEMS = count(explode("\n",$users->get('mems')));
    $MEMS = $MEMS +$FAKEOS;  
     $mm = explode("\n",$users->get('mems'));
     $ok = 0; $false = 0;
     foreach($mm as $mt){
        
        $Br = br('ForwardMessage',[
            'chat_id'=>$mt,
            'from_chat_id' => $chat_id,
            'message_id'=>$update->message->message_id,
        ]);
        if($Br->ok == 1){
            $ok += 1;
        }else{
            $false += 1;
        }
        bot('EditMessageText', [
            'chat_id' => $chat_id,
            'message_id' => $K->result->message_id,
            'text' => "*احصائيات التوجيه الى $MEMS 👻*
- مرسل الى : $ok 
- فشل الارسال : $false 

*قيد التقدم ...🤗*",
            'parse_mode' => 'Markdown',
        ]);
     }
     bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $K->result->message_id,
        'text' => "*تم اكتمال الاذاعه الى $MEMS عضو 🙂‍↔️*
- الذين وصلهم التوجيه : $ok 
- الذين فشل البوت ارسال التوجيه اليهم : $false

*مكتمل 😺*",
        'parse_mode' => 'Markdown',
    ]);
}


if ($data == 'the_backup') {
    if ($from_id == ADMIN) {
        bot('EditMessageText', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => "*- مرحبا بك في قسم النسخ الاحتياطي 📲*",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [["text" => "جلب اعدادات البوت", "callback_data" => "getback_bot"], ["text" => "رفع", "callback_data" => "uplodback_bot"]],
                    [["text" => "جلب اعدادات ال$a3ml", "callback_data" => "getback_acounts"], ["text" => "رفع", "callback_data" => "uplodback_acounts"]],
                    [["text" => "جلب اعدادات الطلبات", "callback_data" => "getback_orders_info"], ["text" => "رفع", "callback_data" => "uplodback_orders_info"]],
                    [["text" => "🔙 رجوع", "callback_data" => "BACKADMIN"]],
                ]
            ])
        ]);
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $update->callback_query->id,
            'text' => "هذا القسم مالك البوت يمكنه استخدامه فقط 🛠",
            'show_alert' => true,
        ]);
    }
}

$key = "thisisaverysecretkey123456789012367";

function generate_iv() {
    return openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-CBC'));
}

function encrypt_file($input_file, $output_file, $key) {
    global $bot_id, $usrbot;
    $data = file_get_contents($input_file);
    $iv = generate_iv();
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    file_put_contents($output_file, "Maker: @H7JBot\nID: $bot_id\nUSERBOT: @$usrbot\nContenter: " . $iv . $encrypted);
}

function decrypt_file($input_file, $output_file, $key) {
    $raw = file_get_contents($input_file);
    $data = explode("Contenter: ", $raw)[1] ?? '';
    $iv_length = openssl_cipher_iv_length('AES-256-CBC');
    $iv = substr($data, 0, $iv_length);
    $encrypted = substr($data, $iv_length);
    $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    file_put_contents($output_file, $decrypted);
}


$uplodback_ = explode("uplodback_", $data)[1] ?? null;
if ($uplodback_) {
    if ($from_id == ADMIN) {
        bot('EditMessageText', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => "*- أرسل الملف الأن :*\n- يجب ان يكون بصيغه (.h7)",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [["text" => "🔙 رجوع", "callback_data" => "the_backup"]],
                ]
            ])
        ]);
        $modes->set('mode_' . $from_id, "UPS_CX");
        $modes->set('HELP_' . $from_id, $uplodback_);
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $update->callback_query->id,
            'text' => "هذا القسم مالك البوت يمكنه استخدامه فقط 🛠",
            'show_alert' => true,
        ]);
    }
}

if ($modes->get('mode_' . $from_id) === 'UPS_CX' && isset($update->message->document)) {
    $file_id = $update->message->document->file_id;
    $file_info = bot("getFile", ["file_id" => $file_id]);
    $file_path = $file_info->result->file_path ?? null;

    if ($file_path) {
        if (pathinfo($file_path, PATHINFO_EXTENSION) === "h7") {
            $download_url = "https://api.telegram.org/file/bot" . API_KEY . "/" . $file_path;
            $tmp_file = "temp_upload_{$from_id}.h7";
            file_put_contents($tmp_file, file_get_contents($download_url));

            $save_to = "DATA_BASES_X/DBRSHAQ/$bot_id/" . $modes->get('HELP_' . $from_id) . ".db";
            decrypt_file($tmp_file, $save_to, $key);
            unlink($tmp_file);

            bot('SendMessage', [
                'chat_id' => $chat_id,
                'text' => "*- تم رفع الملف بنجاح ✅*",
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [["text" => "🔙 رجوع", "callback_data" => "the_backup"]],
                    ]
                ])
            ]);
        } else {
            bot('SendMessage', [
                'chat_id' => $chat_id,
                'text' => "*- يجب ان يكون صيغه الملف (.h7) ❌*",
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [["text" => "🔙 رجوع", "callback_data" => "the_backup"]],
                    ]
                ])
            ]);
        }
    }
}


$getback_ = explode("getback_",$data)[1];
if($getback_){
    encrypt_file("DATA_BASES_X/DBRSHAQ/$bot_id/$getback_.db" , 'h7j_'. $getback_.'' . $bot_id .'.h7' , $key);
    $J = bot('SendDocument', [
        'chat_id' => $chat_id,
        'document' => new CURLFile('h7j_'. $getback_.'' . $bot_id .'.h7' ), 
    ]);
    if($J->ok == 1){
        bot('answerCallbackQuery',[
            'callback_query_id' => $update->callback_query->id,
            'text' => "تم الارسال ✅",
            'show_alert' => true,
        ]);
    }else{
        bot('answerCallbackQuery',[
            'callback_query_id' => $update->callback_query->id,
            'text' => "فشل الارسال ❌",
            'show_alert' => true,
        ]);
    }
}
if($data == 'kshfnqat'){
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*- أرسل ايدي الشخص 👤*\n",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "BACKADMIN"]],
            ]
        ])
    ]);
    $modes->set('mode_' . $from_id, $data);
}

if(is_numeric($text) and $modes->get('mode_' . $from_id) == 'kshfnqat'){
    $get = $users->get($text);
    if($get){
        $NQAT = $TOM->get('coins_'.$text);
    bot('SendMessage', [
        'chat_id' => $chat_id,
        'text' => "*- العضو : * [$get](tg://user?id=$text) *📰* 
- عدد ".$a3ml."ه هي : $NQAT",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "ازاله ".$a3ml."ه ❌", "callback_data" => "nocoin_$text"]],
                [["text" => "🔙 رجوع", "callback_data" => "BACKADMIN"]],
            ]
        ])
    ]);
    $modes->delete('mode_'.$from_id);
}else{
    bot('SendMessage', [
        'chat_id' => $chat_id,
        'text' => "*- العضو ليس موجود في البوت ❌*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "BACKADMIN"]],
            ]
        ])
    ]);
}
}

$nocoin_ = explode("nocoin_",$data)[1];
if($nocoin_){
    $NQAT = $TOM->get('coins_'.$nocoin_);
    $TOM->set('coins_'.$nocoin_ , '0');
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        "message_id" => $message_id,
        'text' => "*- تم اجراء امر الازاله ✅*
تم ازاله $NQAT $a3ml من الحساب $nocoin_",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "BACKADMIN"]],
            ]
        ])
    ]);
    $modes->delete('mode_'.$from_id);

}

if ($data == "shtrak_jbare") {
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        "message_id" => $message_id,
        'text' => "خيارات الاشتراك الإجباري:",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "إضافة قناة", 'callback_data' => "add"]],
                [['text' => "عرض القنوات", 'callback_data' => "list"]],
                [["text" => "🔙 رجوع", "callback_data" => "BACKADMIN"]],
            ]
        ])
    ]);
    $shtrak->delete('mode');
}

if ($data == "add") {
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        "message_id" => $message_id,
        'text' => "أرسل معرف القناة التي تريد إضافتها بصيغة:\n\n`@TOmBots`",
        'parse_mode' => "Markdown",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "الغاء ❌", 'callback_data' => "BACKADMIN"]],
            ]
        ])
    ]);
    $shtrak->set('mode', 'add_channel');
}

if ($shtrak->get('mode') == 'add_channel' && isset($text) && strpos($text, '@') === 0) {
    $channel_info = bot('getChat', ['chat_id' => $text]);
    $channel_data = json_decode(json_encode($channel_info), true);

    if ($channel_data['ok'] ) {
        $member_info = bot('getChatMember', ['chat_id' => $text, 'user_id' => $bot_id]);
        $member_data = json_decode(json_encode($member_info), true);

        if ($member_data['ok'] && in_array($member_data['result']['status'], ['administrator', 'creator'])) {
            $channels = $shtrak->get('channels') ?: [];
            if (!in_array($text, $channels)) {
                $channels[] = $text;
                $shtrak->set('channels', $channels);
                $shtrak->delete('mode');

                bot('sendMessage', [
                    'chat_id' => $chat_id,
                    'text' => "✅ تم إضافة القناة بنجاح:\n\n$text",
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [['text' => "رجوع ↖️", 'callback_data' => "list"]],
                        ]
                    ])
                ]);
            } else {
                bot('sendMessage', [
                    'chat_id' => $chat_id,
                    'text' => "❌ القناة مضافة بالفعل:\n\n$text",
                ]);
            }
        } else {
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "❌ البوت ليس مشرفًا في القناة:\n\n$text",
            ]);
        }
    } else {
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "❌ القناة غير موجودة أو ليست قناة عامة:\n\n$text",
        ]);
    }
}
if ($data == "list") {
    $channels = $shtrak->get('channels') ?: [];

    if (!empty($channels)) {
        $keyboard = [];
        foreach ($channels as $index => $channel) {
            $keyboard[] = [
                ['text' => "$channel", 'url' => "https://t.me/" . ltrim($channel, '@')],
                ['text' => "معلومات 👤", 'callback_data' => "INFCH_$index"]
            ];
        }
        $keyboard[] = [['text' => "رجوع ↖️", 'callback_data' => "BACKADMIN"]];

        bot('EditMessageText', [
            'chat_id' => $chat_id,
            "message_id" => $message_id,
            'text' => "📋 القنوات المضافة للاشتراك الإجباري:",
            'reply_markup' => json_encode(['inline_keyboard' => $keyboard]),
        ]);
    } else {
        bot('EditMessageText', [
            'chat_id' => $chat_id,
            "message_id" => $message_id,
            'text' => "❌ لا توجد قنوات مضافة.",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "رجوع ↖️", 'callback_data' => "BACKADMIN"]],
                ]
            ])
        ]);
    }
}

// التعامل مع معلومات القناة
if (strpos($data, "INFCH_") === 0) {
    $index = (int) str_replace("INFCH_", "", $data);
    $channels = $shtrak->get('channels') ?: [];

    if (isset($channels[$index])) {
        if($shtrak->get("channel_count_$index")){
            $d = $shtrak->get("channel_count_$index");
            $J = "- عدد مطلوب للدخول : $d";
            $d = $SHTRAK_CATHCH->get("channel_count_$index") ?? 0;
            $H = "- تم دخول $d";
        }
        bot('EditMessageText', [
            'chat_id' => $chat_id,
            "message_id" => $message_id,
            'parse_mode' => 'Markdown',
            'text' => "- معلومات القناة : [" . $channels[$index] . "] ✅
$J
$H",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "حذف القناة ❌", 'callback_data' => "delete_$index"]],
                    [['text' => "تعيين عدد الدخول", 'callback_data' => "tachch_$index"]],
                    [['text' => "رجوع ↖️", 'callback_data' => "list"]],
                ]
            ])
        ]);
    } else {
        bot('EditMessageText', [
            'chat_id' => $chat_id,
            "message_id" => $message_id,
            'text' => "⚠️ لم يتم العثور على القناة المطلوبة.",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "رجوع ↖️", 'callback_data' => "list"]],
                ]
            ])
        ]);
    }
}

if (strpos($data, "tachch_") === 0) {
    $index = str_replace("tachch_", "", $data);
    $channels = $shtrak->get('channels') ?: [];

    if (isset($channels[$index])) {
        $shtrak->set("set_count_channel", $index); // تخزين المؤشر مؤقتًا في قاعدة البيانات

        bot('EditMessageText', [
            'chat_id' => $chat_id,
            "message_id" => $message_id,
            'text' => "🧮 أرسل الآن عدد الدخول المطلوب للقناة:\n[" . $channels[$index] . "]",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "إلغاء ❌", 'callback_data' => "list"]],
                ]
            ])
        ]);
        $shtrak->set('DATA', $index);
        $shtrak->set('mode', 'edit_3dd_ch');
        return;
    } else {
        bot('SendMessage', [
            'chat_id' => $chat_id,
            'text' => "❌ القناة غير موجودة.",
        ]);
    }
}

$index = $shtrak->get("set_count_channel");

if (is_numeric($text) && $index !== null) {
    $channels = $shtrak->get('channels') ?: [];

    if (isset($channels[$index])) {
        $shtrak->set("channel_count_$index", $text); // تخزين العدد الخاص بالقناة

        $shtrak->set("set_count_channel", null); // مسح المؤشر بعد التعيين

        bot('SendMessage', [
            'chat_id' => $chat_id,
            'text' => "✅ تم تعيين عدد الدخول [$text] للقناة:\n" . $channels[$index],
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "رجوع ↖️", 'callback_data' => "list"]],
                ]
            ])
        ]);
    }
}



if (strpos($data, "delete_") === 0) {
    $index = str_replace("delete_", "", $data);
    $channels = $shtrak->get('channels') ?: [];

    if (isset($channels[$index])) {
        $deleted_channel = $channels[$index];
        unset($channels[$index]);
        $channels = array_values($channels);
        $shtrak->set('channels', $channels);
        $shtrak->delete("channel_count_$index");
$SHTRAK_CATHCH->delete("channel_count_$index");
        bot('EditMessageText', [
            'chat_id' => $chat_id,
            "message_id" => $message_id,
            'text' => "✅ تم حذف القناة:\n\n$deleted_channel",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "رجوع ↖️", 'callback_data' => "list"]],
                ]
            ])
        ]);
    } else {
        bot('EditMessageText', [
            'chat_id' => $chat_id,
            "message_id" => $message_id,
            'text' => "❌ حدث خطأ. القناة غير موجودة.",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "رجوع ↖️", 'callback_data' => "list"]],
                ]
            ])
        ]);
    }
}




$tgle_ = explode("tgle_",$data)[1];
if($tgle_){
$now_mode = $bot->get('generals_'. $tgle_);
if($now_mode != '✅'){
    $bot->set('generals_'. $tgle_ , '✅');
}else{
    $bot->set('generals_'. $tgle_ , '❌');
}
$data = "Al_aqsam_1";
}

if($data == "al_START"){
    $NOW_STA =  $bot->get('START_');
bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
      'text' => "*- قسم رساله الترحيب (/start) .*
 ⌯ الحالي: `$NOW_STA`",
        'parse_mode' => 'Markdown', 
       'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "تعيين الرساله", "callback_data" => "SET_TH_START"]],
                [["text" => "رجوع", "callback_data" => "BACKADMIN"]],
            ]
        ])
    ]);
}

if($data=='SET_TH_START'){
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
      'text' => "*- أرسل الرساله الترحيبيه الأن :*
 (⌯ الهاشتاك المسموح لك بأستخدامها.)
 - `#a` - *لوضع اسم المستخدم وبداخله رابط الحساب*
 - `#b` - *لوضع اسم الحساب*
 - `#c` - *لوضع ايدي الحساب*
 - `#d` - *لوضع معرف المستخدم*
 - `#e` - *لوضع عدد ال$a3ml*",
        'parse_mode' => 'Markdown', 
       'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "al_START"]],
            ]
        ])
    ]);
    $modes->set('mode_'.$from_id , $data);
    return;
}
if($text and $modes->get('mode_'.$from_id) == "SET_TH_START"){
    bot('SendMessage', [
        'chat_id' => $chat_id,
        'text' => "*- تم حفظ رساله الترحيب .*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                
                [["text" => "رجوع", "callback_data" => "al_START"]]
            ]
        ])
    ]);
    $TH_START = str_replace(array('#a','#b' , '#c' , '#d' , '#e') , array("[$name](tg://user?id=$from_id)" ,"$name" , "$from_id" , "[$username]" ,$TOM->get('coins_'.$chat_id)) , $text);
    bot('SendMessage', [
        'chat_id' => $chat_id,
        'text' => "*- مثال لرساله الترحيب.*
$TH_START",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                
                [["text" => "رجوع", "callback_data" => "al_START"]]
            ]
        ])
    ]);

    $bot->set('START_', "$text");
    $modes->delete('mode_'.$from_id);
}
if($data == 'BACKADMIN'){
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*- مرحبا بك عزيزي الادمن 👤*\n*⚠️ يتم تشفير جميع الرسائل بينك وبين البوت*
",
        'parse_mode' => 'Markdown', 
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "الصيانه : ".$bot->get('generals_siana'), "callback_data" => "tgle_siana"],["text" => "اشعار الدخول : ".$bot->get('generals_entry'), "callback_data" => "tgle_entry"]],
                [["text" => "قسم التمويل : ".$bot->get('generals_tmoil'), "callback_data" => "tgle_tmoil"]],
                [["text" => "حماية البوت", "callback_data" => "ALHMAIA"]],
                [["text" => "رسالة الترحيب ( /start )", "callback_data" => "al_START"],["text" => "الحظر", "callback_data" => "BLOCKS"]],
                [["text" => "قسم الأزرار الشفافة", "callback_data" => "AL_AZRAR"]],
                [["text" => "الأوامر المختصرة (Commands)", "callback_data" => "al_commands"]],
                [["text" => "الاشتراك الاجباري", "callback_data" => "shtrak_jbare"],["text" => "الإذاعة", "callback_data" => "broadcast"]],
                [["text" => "وضع المطورين | Dev Mode", "callback_data" => "DEv_MOde"]],
                [["text" => "إعدادات البوت", "callback_data" => "SETTINGER"]],
                
                
            ]
        ])
    ]);
    $modes->delete('mode_'.$from_id);
    $modes->delete('help_'.$from_id);
}

if($data == 'Al_aqsam_1'){ // الأقسام العامة
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*- الأقسام العامة 🛠️*",
        'parse_mode' => 'Markdown', 
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "الصيانه : ".$bot->get('generals_siana'), "callback_data" => "tgle_siana"],["text" => "اشعار الدخول : ".$bot->get('generals_entry'), "callback_data" => "tgle_entry"]],
                [["text" => "قسم التمويل : ".$bot->get('generals_tmoil'), "callback_data" => "tgle_tmoil"]],
                [["text" => "حماية البوت", "callback_data" => "ALHMAIA"]],
                [["text" => "رسالة الترحيب ( /start )", "callback_data" => "al_START"]],
                [["text" => "⬅️ رجوع", "callback_data" => "BACKADMIN"]]
            ]
        ])
    ]);
}

if($data == 'Al_aqsam_2'){ // أقسام التفاعل
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*- أقسام التفاعل 🔁*",
        'parse_mode' => 'Markdown', 
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "قسم الأزرار الشفافة", "callback_data" => "AL_AZRAR"]],
                [["text" => "الأوامر المختصرة (Commands)", "callback_data" => "al_commands"]],
                [["text" => "⬅️ رجوع", "callback_data" => "BACKADMIN"]]
            ]
        ])
    ]);
}

if($data == 'Al_aqsam_3'){ // أقسام التحكم
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*- أقسام التحكم 🚫*",
        'parse_mode' => 'Markdown', 
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "الاشتراك الاجباري", "callback_data" => "shtrak_jbare"]],
                [["text" => "الإذاعة", "callback_data" => "broadcast"]],
                [["text" => "الحظر", "callback_data" => "BLOCKS"]],
                [["text" => "⬅️ رجوع", "callback_data" => "BACKADMIN"]]
            ]
        ])
    ]);
}

if($data == 'Al_aqsam_4'){ // الإعدادات المتقدمة
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*- الإعدادات المتقدمة ⚙️*",
        'parse_mode' => 'Markdown', 
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "وضع المطورين | Dev Mode", "callback_data" => "DEv_MOde"]],
                [["text" => "إعدادات البوت", "callback_data" => "SETTINGER"]],
                [["text" => "⬅️ رجوع", "callback_data" => "BACKADMIN"]]
            ]
        ])
    ]);
}

if($data == 'asasse'){
    $DOMIN = $bot->get('GENERALS_DOMIN') ?? "لايوجد !";
    $KEY = $bot->get('GENERALS_KEY') ?? "لايوجد !";
    $cost = json_decode(file_get_contents("https://$DOMIN/api/v2?key=$KEY&action=balance"), 1);
    $balance = $cost['balance'];
    $currency = $cost['currency'];
    if($balance){
        $HH = "- الرصيد المتوفر : `$balance`";
    }else{
        $HH = "\n*معلومات خاطئه ([API_KEY] أو [DOMAIN])*";
    }
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
       'text' => "*- مرحبا بك في قسم الربوطات الاساسيه *
- الدومين الموضوع : `$DOMIN`
- المفتاح : `$KEY`
$HH

*- هذا القسم مصنوع لغرض ربط خارجي فقط بمعنى يمكنك ربط خدمه مضافه على هذه المعلومات الجاهزه أن اردت !*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "تعيين الدومين", "callback_data" => "SRTGENERAL_DOMIN"]],
                [["text" => "تعيين المفتاح [API_KEY]", "callback_data" => "SRTGENERAL_KEY"]],
                // [["text" => "قسم الربوطات المتعدده", "callback_data" => "multi_rbts"]],
                [["text" => "رجوع", "callback_data" => "BACKADMIN"]],
            ]
        ])
    ]);
    $modes->delete('mode_'.$from_id);
    $modes->delete('help_'.$from_id);
}
if(preg_match("/^DELETERBT_(\d+)$/", $data, $match)){
    $index = $match[1];
    $all_rbts = explode("\n", trim($bot->get('OTHER_RBTS')));
    
    if(isset($all_rbts[$index])){
        unset($all_rbts[$index]);
        $all_rbts = array_values($all_rbts); // Reindex
        $bot->set('OTHER_RBTS', implode("\n", $all_rbts)); // Store as string again
    }

    $data = 'multi_rbts';
}

if($data == 'multi_rbts'){
    $DOMx = [];
    $i = 0;
    $other_rbts = explode("\n", trim($bot->get('OTHER_RBTS')));
    foreach($other_rbts as $RBTS){
        if(empty($RBTS)) continue; 
        $texts = explode("|", $RBTS);
        $DOMAIN = $texts[0] ?? '';
        $KEY = $texts[1] ?? '';
        $DOMx[] = [
            ["text" => "$DOMAIN", "url" => "https://$DOMAIN"],
            ["text" => "❌ حذف", "callback_data" => "DELETERBT_$i"]
        ];
        $i++;
    }

    $DOMx[] = [["text" => "➕ اضافه ربط", "callback_data" => "ADDNEW_RBT"]];
    $DOMx[] = [["text" => "رجوع", "callback_data" => "asasse"]];

    $rbts = count(array_filter($other_rbts)); 
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*- قسم الربوطات المتعدده 🔠 *
- عدد الربوطات الحاليه : `$rbts`
- أجمالي الرصيد المتوفر : `$ijmale`

*- هذا القسم مصنوع لغرض ربط خارجي فقط بمعنى يمكنك ربط خدمه مضافه على هذه المعلومات الجاهزه أن اردت !*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode(["inline_keyboard" => $DOMx])
    ]);

    $modes->delete('mode_'.$from_id);
    $modes->delete('help_'.$from_id);
}


if($data == 'ADDNEW_RBT'){
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
       'text' => "*- أرسل الربوطات الجديد بهذا الشكل الان*
[DOMAIN|API_KEY]

- مثال : `example.com|KEY12347899009`
- يمكنك ارسال اكثر من ربط
",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "multi_rbts"]],
            ]
        ])
    ]);
    $modes->set('mode_'.$from_id , $data);
    return;
}

if($text and $modes->get('mode_'.$from_id) == "ADDNEW_RBT"){
    $texts = explode("|", $text)[1];
    if($texts[0] and $texts[1]){
    bot('SendMessage', [
        'chat_id' => $chat_id,
        'text' => "*- تم اضافه الربط الى قائمه الربوطات ✅*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                
                [["text" => "🔙 رجوع", "callback_data" => "multi_rbts"]]
            ]
        ])
    ]);
    $bot->set('OTHER_RBTS', $bot->get('OTHER_RBTS') ."\n$text");
}else{
    bot('SendMessage', [
        'chat_id' => $chat_id,
        'text' => "*- خطأ في الصيغه أرسل بلصيغه المطلوبه ❌*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                
                [["text" => "🔙 رجوع", "callback_data" => "multi_rbts"]]
            ]
        ])
    ]);
}
    $modes->delete('mode_'.$from_id);
    $modes->delete('help_'.$from_id);
}
$parts = explode("deleteqsm_", $data);
$deleteqsm_ = isset($parts[1]) ? trim($parts[1]) : null;

if ($deleteqsm_) {
    $name_qsm = $bot->get('qsms_name_' . $deleteqsm_);
    if ($name_qsm) {
        $bot->delete('xdmat_' . $deleteqsm_);
        $bot->delete('qsms_id_' . $deleteqsm_);
        
        $qsms = $bot->get('qsms');
        if ($qsms !== null) {
            $new_qsms = str_replace($name_qsm, '', $qsms);
            $bot->set('qsms', trim($new_qsms)); // Trim to clean up any spaces
        }
        
        $bot->delete('qsms_name_' . $deleteqsm_);
    }
    
    $data = "xdmats";
}

$deletexdma_ = explode("deletexdma_",$data)[1];
if($deletexdma_){
    $qsm = $bot->get('xdmatinqsm_'.$deletexdma_);
    $name_xdma = $bot->get('xdmatname_' . $deletexdma_);
    if ($name_xdma) {
        $xdmat = str_replace($name_xdma , '' , $bot->get('xdmat_'.$qsm));

        if ($qsm !== null) {
            $bot->set('xdmat_'.$qsm, trim($xdmat)); // Trim to clean up any spaces
        }
        
    }
    $data = "ENTERQSM_$qsm";
}


if ($data == 'xdmats') {
    $S_LIST = ['inline_keyboard' => []];
    $buttons = [];

    foreach (explode("\n", $bot->get('qsms')) as $qsms) {
        
        if (!empty($qsms)) {
            $idx = $bot->get('qsms_id_' . $qsms);
            if(!$idx){
                $idx = coderandom(10);
                $bot->set('qsms_id_'.$qsms,$idx);
    $bot->set('qsms_name_'.$idx,$qsms);
            }
            if(!empty($bot->get('qsms_name_'.$idx))){
            $buttons[] = ['text' => "$qsms", 'callback_data' => "ENTERQSM_$idx"];
            }
        }
    }


    $button_rows = array_chunk($buttons, 2);
    foreach ($button_rows as $row) {
        $S_LIST['inline_keyboard'][] = $row;
    }


    $S_LIST['inline_keyboard'][] = [['text' => "أضافه قسم ➕", 'callback_data' => "addqsm"]];
    $S_LIST['inline_keyboard'][] = [['text' => "🔙 رجوع", 'callback_data' => "BACKADMIN"]];

    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*- اداره الخدمات والاقسام تحكم ادناه ⚙️*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode($S_LIST)
    ]);
    $modes->delete('mode_'.$from_id);
    $modes->delete('help_'.$from_id);
}

$SRTGENERAL_ = explode("SRTGENERAL_", $data)[1];
if($SRTGENERAL_){
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*- أرسل ال *[$SRTGENERAL_] *الأن :*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                
                [["text" => "🔙 رجوع", "callback_data" => "asasse"]]
            ]
        ])
    ]);
    $modes->set('mode_' . $from_id, 'editgeneral');
    $modes->set('help_' . $from_id, $SRTGENERAL_);

}

if($modes->get('mode_'.$from_id) == 'editgeneral' && $text){
    bot('SendMessage', [
        'chat_id' => $chat_id,
        'text' => "*تم التعيين بنجاح *",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                
                [["text" => "🔙 رجوع", "callback_data" => "asasse"]]
            ]
        ])
    ]);
    $bot->set('GENERALS_'. $modes->get('help_' . $from_id) , $text);
    $modes->delete('mode_'.$from_id);
    $modes->delete('help_'.$from_id);
}

$OTHERRBTS_ = explode('OTHERRBTS_',$data)[1];
if($OTHERRBTS_){
    $in = $bot->get("xdmatinqsm_".$OTHERRBTS_);
    $name_xdma = $bot->get('xdmatname_' . $OTHERRBTS_) ?? '0';
    $DOMx = [];
    $i = 0;
    $other_rbts = explode("\n", trim($bot->get('OTHER_RBTS')));
    foreach($other_rbts as $RBTS){
        if(empty($RBTS)) continue; 
        $texts = explode("|", $RBTS);
        $DOMAIN = $texts[0] ?? '';
        $KEY = $texts[1] ?? '';
        $DOMx[] = [
            ["text" => "$DOMAIN", "url" => "https://$DOMAIN"],
            ["text" => "أربط", "callback_data" => "CONNECTRBT_".$i]
        ];
        $i++;
    }
    if($i < 1){
        bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*- لايوجد هناك ربوطات مضافه*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "قسم الربوطات", "callback_data" => "multi_rbts"]],
                [["text" => "🔙 رجوع", "callback_data" => "ENTERXDMA_$OTHERRBTS_"]]
            ]
        ])
    ]);
    }else{
        $DOMx[] = [["text" => "🔙 رجوع", "callback_data" => "ENTERXDMA_$OTHERRBTS_"]];
        bot('EditMessageText', [
            'chat_id' => $chat_id, 
            'message_id' => $message_id,
            'text' => "*- الخدمه $name_xdma اختر الذي تفضله ربطه معه*",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode(["inline_keyboard" => $DOMx])
        ]);
        $modes->set('help_'.$from_id , $OTHERRBTS_);
    }
}
$CONNECTRBT_ = explode('CONNECTRBT_', $data)[1];
if ($CONNECTRBT_ !== null && $CONNECTRBT_ !== '') {
    $in = $bot->get("xdmatinqsm_" . $modes->get('help_' . $from_id));
    $name_xdma = $bot->get('xdmatname_' . $modes->get('help_' . $from_id)) ?? '0';
    $index = $CONNECTRBT_;
    $all_rbts = explode("\n", trim($bot->get('OTHER_RBTS')));
    if (isset($all_rbts[$index])) {
        $D = explode('|', $all_rbts[$index]);
        $DOMAIN = $D[0];
        $KEY = $D[1];
        bot('EditMessageText', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => "*- تم ربط الخدمه $name_xdma مع $DOMAIN*",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [["text" => "🔙 رجوع", "callback_data" => "OTHERRBTS_" . $modes->get('help_' . $from_id)]]
                ]
            ])
        ]);
        $bot->set('XDMATSOTHER_'. $modes->get('help_' . $from_id) , $all_rbts[$index]);
    }
}

$TSLEMER_ = explode("TSLEMER_" , $data)[1];
if($TSLEMER_){
    $ID_XDMA = $TSLEMER_;
    $X = $bot->get('XDMA_INF_TSLEM__'. $ID_XDMA);
    if($X == 'يدوي'){
        $سوي = "تلقائي";
    }else{
        $سوي = "يدوي";
    }
    $bot->set('XDMA_INF_TSLEM__'. $ID_XDMA , $سوي);
    $data = "ENTERXDMA_$ID_XDMA";
}
$ENTERXDMA_ = explode("ENTERXDMA_", $data)[1] ?? null;

if ($ENTERXDMA_) {
    $ID_XDMA = $ENTERXDMA_;
    $in = $bot->get("xdmatinqsm_".$ENTERXDMA_);
    $name_xdma = $bot->get('xdmatname_' . $ENTERXDMA_) ?? '0';
    
    $infoos = $bot->get('infos_' . $ENTERXDMA_) ?? '0';
    

        $S_TEXT = explode('|', $infoos);
        list($DOMIN, $API, $ID, $MAX, $MIN, $PRICE , $description) = array_pad($S_TEXT, 6, 'N/A');
        if($bot->get('GENERALS_DOMIN') and $bot->get('GENERALS_KEY')){
            $DOMINx = $bot->get('GENERALS_DOMIN');
            $YOU_CAN = "أربط مع - $DOMINx (أختياري)";
        }
        if($bot->get("GENERALS_DOMINX_". $ENTERXDMA_)){
        $DOMIN = $bot->get('GENERALS_DOMIN');
        $API = $bot->get('GENERALS_KEY');
        $YOU_CAN = "الغي مع - $DOMIN .";
    }
    if($bot->get('XDMATSOTHER_'. $ENTERXDMA_)){
        $DOMIN = explode('|',$bot->get('XDMATSOTHER_'. $ENTERXDMA_))[0];
        $API = explode('|',$bot->get('XDMATSOTHER_'. $ENTERXDMA_))[1];
    }
    $DOMIN = $bot->get('XDMA_INF_DOMIN__'. $ID_XDMA) ?? "لم يتم وضع";
    $API = $bot->get('XDMA_INF_KEY__'. $ID_XDMA) ?? "لم يتم وضع";
    $MIN = $bot->get('XDMA_INF_MIN__'. $ID_XDMA) ?? "لم يتم وضع";
    $MAX = $bot->get('XDMA_INF_MAX__'. $ID_XDMA) ?? "لم يتم وضع";
    $PRICE = $bot->get('XDMA_INF_PRICE__'. $ID_XDMA) ?? "لم يتم وضع";
    $ID = $bot->get('XDMA_INF_ID__'. $ID_XDMA) ?? "لم يتم وضع";
    $description  = $bot->get('XDMA_INF_DESCRIPTION__'. $ID_XDMA) ?? "لم يتم وضع";
        $my_text = "

*✅ - دومين الموقع : *[$DOMIN]
*✅ - توكن الموقع :* [$API]
*✅ - ايدي الخدمه :* `$ID`
*✅ - اقصي حد للطلب :* `$MAX`
*✅ - ادنى حد للطلب :* `$MIN`
*✅ - السعر لكل 1 :* *$PRICE*
*✅ - وصف الخدمه :* [$description]

";
    $NO3_TSLEM = $bot->get('XDMA_INF_TSLEM__'. $ID_XDMA) ?? "تلقائي";
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*- خدمه $name_xdma التحكم ادناه 🔠*
$my_text",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "نوع التسليم : $NO3_TSLEM", "callback_data" => "TSLEMER_$ENTERXDMA_"]],
                [["text" => "تعيين أسم الخدمه", "callback_data" => "setinfosX_$ENTERXDMA_|_|_|NAME"]],
                [["text" => "تعيين ايدي الخدمه", "callback_data" => "setinfosX_$ENTERXDMA_|_|_|ID"]],
                [["text" => "تعيين ادنى حد", "callback_data" => "setinfosX_$ENTERXDMA_|_|_|MIN"]],
                [["text" => "تعيين اقصى حد", "callback_data" => "setinfosX_$ENTERXDMA_|_|_|MAX"]],
                [["text" => "تعيين دومين الموقع", "callback_data" => "setinfosX_$ENTERXDMA_|_|_|DOMIN"]],
                [["text" => "تعيين المفتاح [API_KEY]", "callback_data" => "setinfosX_$ENTERXDMA_|_|_|KEY"]],
                [["text" => "تعيين السعر", "callback_data" => "setinfosX_$ENTERXDMA_|_|_|PRICE"]],
                [["text" => "تعيين الوصف", "callback_data" => "setinfosX_$ENTERXDMA_|_|_|DESCRIPTION"]],
                [["text" => "$YOU_CAN", "callback_data" => "autox_$ENTERXDMA_"]],
                [["text" => "حذف الخدمه", "callback_data" => "deletexdma_$ENTERXDMA_"]],
                [["text" => "🔙 رجوع", "callback_data" => "ENTERQSM_$in"]]
            ]
        ])
    ]);
    $modes->delete('mode_'.$from_id);
    $modes->delete('help_'.$from_id);
} 

$setinfosX_ = explode("setinfosX_", $data)[1];
if ($setinfosX_) {
    $DATA = explode("|_|_|", $setinfosX_);
    $ID_XDMA = $DATA[0];
    $action = $DATA[1];
    if ($action == "NAME") {$ACTK = "أسم الخدمه";}
    if ($action == "ID") {$ACTK = "ايدي الخدمه";}
    if ($action == "MIN") {$ACTK = "ادنى حد";}
    if ($action == "MAX") {$ACTK = "اقصى حد";}
    if ($action == "DOMIN") {$ACTK = "دومين الموقع";}
    if ($action == "KEY") {$ACTK = "مفتاح ال [API_KEY]";}
    if ($action == "PRICE") {$ACTK = "سعر الخدمه";}
    if ($action == "DESCRIPTION") {$ACTK = "وصف الخدمه";}
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*‌أرسل $ACTK الأن:*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "ENTERXDMA_$ID_XDMA"]]
            ]
        ])
    ]);
    $modes->set("mode_$from_id", "EDITXDMAX");
    $modes->set("help_$from_id", $action);
    $modes->set("help2_$from_id", $ID_XDMA);
}

if($text and $modes->get("mode_". $chat_id) == "EDITXDMAX"){
    $action = $modes->get('help_' . $from_id);
    $ID_XDMA = $modes->get('help2_' . $from_id);
    if($action == "ID"){$ACTK = "ايدي الخدمه";}
    if($action == "MIN"){$ACTK = "ادنى حد";}
    if($action == "MAX"){$ACTK = "اقصى حد";}
    if($action == "DOMIN"){$ACTK = "دومين الموقع";
    $IMTOM = parse_url($text);
    $text = $IMTOM['host'];}
    if($action == "KEY"){$ACTK = "مفتاح ال [API_KEY]";}
    if($action == "PRICE"){$ACTK = "سعر الخدمه";}
    if($action == "DESCRIPTION"){$ACTK = "وصف الخدمه";}
    $OLD = $bot->get('XDMA_INF_'.$action .'__'. $ID_XDMA) ?? "NONE";
    $BEST_TEXT = "*- القديم : *$OLD
*- الجديد :* $text";
    if($action == "NAME"){$ACTK = "أسم الخدمه";
    $OLD=$bot->get("xdmatname_".$ID_XDMA) ?? "NONE";
    $bot->set("xdmatname_".$ID_XDMA , $text);
    $MENU = str_replace($OLD , $text , $bot->get('xdmat_' . $ID_XDMA));
    $bot->set('xdmat_' . $ID_XDMA , $MENU);
    $BEST_TEXT = "*- القديم : *$OLD
*- الجديد :* $text";
    }
    bot('SendMessage', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*تم حفظ $ACTK ✅.*
$BEST_TEXT ",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "ENTERXDMA_$ID_XDMA"]]
            ]
        ])
    ]);
    $bot->set('XDMA_INF_'.$action .'__'. $ID_XDMA, $text);
    $modes->delete('mode_' . $from_id);
    $modes->delete('help_' . $from_id);
}


$autox_ = explode("autox_", $data)[1];
if($autox_){
    $name_xdma = $bot->get('xdmatname_' . $autox_) ?? '0';
    $DOMIN = $bot->get('GENERALS_DOMIN');
    $API = $bot->get('GENERALS_KEY');
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*تم ضبط خدمه $name_xdma ليكون مربوط ب $DOMIN ✅*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "ENTERXDMA_$autox_"]]
            ]
        ])
    ]);
    $bot->set('XDMA_INF_DOMIN__'. $autox_, $DOMIN);
    $bot->set('XDMA_INF_KEY__'. $autox_, $API);
}
if (preg_match("/^طريقه_العرض_(.*)/", $data, $m)) {
    $ENTERQSM = $m[1];
    $current_style = $bot->get('style_qsm_' . $ENTERQSM);
    $new_style = ($current_style == 'عمودي') ? 'أفقي' : 'عمودي';
    $bot->set('style_qsm_' . $ENTERQSM, $new_style);

    $name_qsm = $bot->get('qsms_name_' . $ENTERQSM);
    $S_LIST = ['inline_keyboard' => []];
    $buttons = [];

    foreach (explode("\n", $bot->get('xdmat_' . $ENTERQSM)) as $xdmats) {
        $idx = $bot->get('xdmat_' . $xdmats);
        if (!empty($xdmats) and !empty($idx)) {
            $buttons[] = ['text' => "$xdmats", 'callback_data' => "ENTERXDMA_$idx"];
        }
    }

    if ($new_style == 'عمودي') {
        foreach ($buttons as $btn) {
            $S_LIST['inline_keyboard'][] = [$btn];
        }
    } else {
        $button_rows = array_chunk($buttons, 2);
        foreach ($button_rows as $row) {
            $S_LIST['inline_keyboard'][] = $row;
        }
    }

    $S_LIST['inline_keyboard'][] = [["text" => "طريقه العرض : " . $new_style, "callback_data" => "طريقه_العرض_$ENTERQSM"]];
    $S_LIST['inline_keyboard'][] = [["text" => "نضام 24 ساعه : " . $bot->get('toggle_24_' . $ENTERQSM), "callback_data" => "toggles_24_$ENTERQSM"]];
    $S_LIST['inline_keyboard'][] = [["text" => "أضافه خدمات ➕", "callback_data" => "addxdmat_$ENTERQSM"]];
    $S_LIST['inline_keyboard'][] = [["text" => "أحذف القسم", "callback_data" => "deleteqsm_$ENTERQSM"]];
    $S_LIST['inline_keyboard'][] = [["text" => "‌أرسال اسم الخدمات", "callback_data" => "names_$ENTERQSM"]];
    $S_LIST['inline_keyboard'][] = [["text" => "🔙 رجوع", "callback_data" => "xdmats"]];

    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*- قسم $name_qsm التحكم ادناه 🔠*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode($S_LIST)
    ]);
}

$ENTERQSM_ = explode('ENTERQSM_', $data)[1] ?? null;

if ($ENTERQSM_) {
    if(!$bot->get('style_qsm_' .$ENTERQSM_)){
        $bot->set('style_qsm_' .$ENTERQSM_ , 'عمودي');
    }
    if(!$bot->get('toggle_24_'.$ENTERQSM_)){
        $bot->set('toggle_24_'.$ENTERQSM_,'❌');
    }
    $name_qsm = $bot->get('qsms_name_' . $ENTERQSM);
    $S_LIST = ['inline_keyboard' => []];
    $buttons = [];

    foreach (explode("\n", $bot->get('xdmat_' . $ENTERQSM_)) as $xdmats) {
        $idx = $bot->get('xdmat_' . $xdmats);
        if (!empty($xdmats) and !empty($idx)) {
            $buttons[] = ['text' => "$xdmats", 'callback_data' => "ENTERXDMA_$idx"];
        }
    }

    if ($bot->get('style_qsm_' .$ENTERQSM_) == 'عمودي') {
        foreach ($buttons as $btn) {
            $S_LIST['inline_keyboard'][] = [$btn];
        }
    } else {
        $button_rows = array_chunk($buttons, 2);
        foreach ($button_rows as $row) {
            $S_LIST['inline_keyboard'][] = $row;
        }
    }
    $modes->delete('mode_' . $from_id);
    $modes->delete('help_' . $from_id);


    $S_LIST['inline_keyboard'][] = [["text" => "طريقه العرض : " . $bot->get('style_qsm_' .$ENTERQSM_), "callback_data" => "طريقه_العرض_$ENTERQSM_"]];
    $S_LIST['inline_keyboard'][] = [["text" => "نضام 24 ساعه : ". $bot->get('toggle_24_'.$ENTERQSM_), "callback_data" => "toggles_24_$ENTERQSM_"]];
    $S_LIST['inline_keyboard'][] = [["text" => "أضافه خدمات ➕", "callback_data" => "addxdmat_$ENTERQSM_"]];
    $S_LIST['inline_keyboard'][] = [["text" => "أحذف القسم", "callback_data" => "deleteqsm_$ENTERQSM_"]];
    $S_LIST['inline_keyboard'][] = [["text" => "‌أرسال اسم الخدمات", "callback_data" => "names_$ENTERQSM_"]];
    $S_LIST['inline_keyboard'][] = [["text" => "🔙 رجوع", "callback_data" => "xdmats"]];

    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*- قسم $name_qsm التحكم ادناه 🔠*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode($S_LIST)
    ]);
}


$ALLASGENERAL_ = explode("ALLASGENERAL_" , $data)[1];
if($ALLASGENERAL_){
    
    $xdmat_list = $bot->get('xdmat_' . $ALLASGENERAL_);
    if ($xdmat_list) {
        foreach (explode("\n", $xdmat_list) as $xdmats) {
            $xdmats = trim($xdmats);
            if (!empty($xdmats)) {
                $idx = $bot->get('xdmat_' . $xdmats);
                if (!empty($idx)) {
                    $bot->set('GENERALS_DOMINX_'. $idx , 'OK');
                }
            }
        }
        bot('EditMessageText', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => "*- تم ربط الكل *",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [["text" => "🔙 رجوع", "callback_data" => "ENTERQSM_$ALLASGENERAL_"]],
                ]
            ])
        ]);
    }
}
$UPLOAD_ = explode("UPLOAD_", $data)[1];

if ($UPLOAD_) {
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*- حسناً، أرسل ملف النسخة الاحتياطية بصيغة (.TOM)*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "ENTERQSM_$UPLOAD_"]],
            ]
        ])
    ]);
    $modes->set('mode_' . $from_id, "upload"); 
    $modes->set('help_' . $from_id, $UPLOAD_);
}


if ($modes->get('mode_' . $from_id) === 'upload' && isset($update->message->document)) {

    $file_id = $update->message->document->file_id;

    $file_info = bot("getFile", ["file_id" => $file_id]);
    $file_path = $file_info->result->file_path ?? null;

    if ($file_path) {
        $download_url = "https://api.telegram.org/file/bot" . API_KEY . "/" . $file_path;
        if (pathinfo($file_path, PATHINFO_EXTENSION) === "TOM") {
            $content = file_get_contents($download_url);

            if ($content !== false) {
                $lines = explode("\n", trim($content));
                $added_names = '';
                $qsm_id = $modes->get('help_' . $from_id);

                foreach ($lines as $line) {
                    if (!empty(trim($line))) {
                        if(explode("(+)-" , $line)){
                            $line = explode("(+)-" , $line)[1];
                        $fields = explode('|', $line);
                        list($NAME, $idx, $DOMIN, $API, $ID, $MAX, $MIN, $PRICE, $description) = array_pad($fields, 9, 'N/A');
                        $bot->set('xdmat_' . $NAME, $idx);
                        $bot->set('xdmatname_' . $idx, $NAME);
                        $bot->set('xdmatinqsm_' . $idx, $qsm_id);
                        $bot->set('infos_' . $idx , "$DOMIN|$API|$ID|$MAX|$MIN|$PRICE|$description");
                        $old_xdmat_list = $bot->get('xdmat_' . $qsm_id);
                        $updated_list = trim($old_xdmat_list . "\n" . $NAME);
                        $bot->set('xdmat_' . $qsm_id, $updated_list);

                        $added_names .= "➤ $NAME\n";
                        }
                    }
                }

                $modes->delete('mode_' . $from_id);
                $modes->delete('help_' . $from_id);

                bot('sendMessage', [
                    'chat_id' => $chat_id,
                    'text' => "*✅ تم إضافة العناصر التالية:*\n\n$added_names",
                    'parse_mode' => 'Markdown'
                ]);

            } else {
                bot('sendMessage', [
                    'chat_id' => $chat_id,
                    'text' => "❌ فشل في تحميل محتوى الملف. حاول مرة أخرى.",
                ]);
            }

        } else {
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "❌ الملف الذي أرسلته ليس بصيغة (.TOM)!",
            ]);
        }

    } else {
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "❌ فشل في الحصول على رابط التحميل للملف.",
        ]);
    }
}


$BACKUPX_ = explode("BACKUPX_", $data)[1];

if ($BACKUPX_) {
    $name_qsm = $bot->get('qsms_name_' . $BACKUPX_);
    $modes->delete('mode_' . $from_id);
    $modes->delete('help_' . $from_id);

    $allData = '';
    $xdmat_list = $bot->get('xdmat_' . $BACKUPX_);
    if ($xdmat_list) {
        foreach (explode("\n", $xdmat_list) as $xdmats) {
            $xdmats = trim($xdmats);
            if (!empty($xdmats)) {
                $idx = $bot->get('xdmat_' . $xdmats);
                if (!empty($idx)) {
                    $info = $bot->get('infos_' . $idx);
                    $allData .= "(+)-$xdmats|$idx|$info\n";
                }
            }
        }
    }

    $filename = "backup_$BACKUPX_.TOM";
    file_put_contents($filename, $allData);

    bot('sendDocument', [
        'chat_id' => $chat_id,
        'document' => new CURLFile(realpath($filename)),
        'caption' => "✅ تم حفظ نسخة احتياطية: $name_qsm",
    ]);

    unlink($filename);
}

$names_ = explode("names_" , $data)[1];
if($names_){
    $name_qsm = $bot->get('qsms_name_' . $names_);
    $modes->delete('mode_' . $from_id);
    $modes->delete('help_' . $from_id);

    $S_LIST = ['inline_keyboard' => []];
    $buttons = [];

    foreach (explode("\n", $bot->get('xdmat_' . $names_)) as $xdmats) {
        $idx = $bot->get('xdmat_' . $xdmats);
        if (!empty($xdmats) and !empty($idx)) {
            
            $nMX = $nMX ."\n$xdmats";
        }
    }
    bot('SendMessage', [
        'chat_id' => $chat_id,
        'text' => "$nMX",

    ]);
}
$ENTERQSM_x = explode('toggles_24_',$data)[1];
if($ENTERQSM_x){
    $ENTERQSM_ = $ENTERQSM_x;
    if($bot->get('toggle_24_'.$ENTERQSM_) != '✅'){
        $bot->set('toggle_24_'.$ENTERQSM_  , '✅'); 
    }else{
        $bot->set('toggle_24_'.$ENTERQSM_  , '❌'); 
    }
    $name_qsm = $bot->get('qsms_name_' . $ENTERQSM_);
    $modes->delete('mode_' . $from_id);
    $modes->delete('help_' . $from_id);

    $S_LIST = ['inline_keyboard' => []];
    $buttons = [];

    foreach (explode("\n", $bot->get('xdmat_' . $ENTERQSM_)) as $xdmats) {
        $idx = $bot->get('xdmat_' . $xdmats);
        if (!empty($xdmats)) {
            
            $buttons[] = ['text' => "$xdmats", 'callback_data' => "ENTERXDMA_$idx"];
        }
    }

    $button_rows = array_chunk($buttons, 2);
    foreach ($button_rows as $row) {
        $S_LIST['inline_keyboard'][] = $row;
    }
    $S_LIST['inline_keyboard'][] = [["text" => "نضام 24 ساعه : ". $bot->get('toggle_24_'.$ENTERQSM_), "callback_data" => "toggles_24_$ENTERQSM_"]];
    $S_LIST['inline_keyboard'][] = [["text" => "أضافه خدمات ➕", "callback_data" => "addxdmat_$ENTERQSM_"]];
    $S_LIST['inline_keyboard'][] = [["text" => "أحذف القسم", "callback_data" => "deleteqsm_$ENTERQSM_"]];
    $S_LIST['inline_keyboard'][] = [["text" => "🔙 رجوع", "callback_data" => "xdmats"]];

    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*- قسم $name_qsm التحكم ادناه 🔠*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode($S_LIST)
    ]);
}


$setinfos_ = explode("setinfos_", $data)[1] ?? null;
if ($setinfos_) {
    
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*- حسنا أرسل المعلومات بهذا الشكل 📝*\n
[SITE_DOMAIN|API_KEY|ID_SERVICE|MAX|MIN|PRICE_COIN|DESCRIPTION]\n*مثال*\n`example.com|8457rjfher484|3346|1000|100|0.08|أرسل رابط`",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "ENTERXDMA_$setinfos_"]],
            ]
        ])
    ]);
    $modes->set('mode_' . $from_id, 'editxdma');
    $modes->set('help_' . $from_id, $setinfos_);
}

if ($modes->get('mode_' . $from_id) === 'editxdma' && !empty($text)) {
    $ID_XDm = $modes->get('help_' . $from_id);
    $qsm_id = $TOM->get('xdmatinqsm_' . $modes->get('help_' . $from_id));
    $S_TEXT = explode('|', $text);
    
    if (count($S_TEXT) >= 6) {
        [$DOMIN, $API, $ID, $MAX, $MIN, $PRICE , $description] = $S_TEXT;
        bot('SendMessage', [
            'chat_id' => $chat_id,
            'text' => "*- تم حفظ معلومات الخدمه ✅*\n- دومين الموقع : `$DOMIN`\n- توكن الموقع : `$API`\n- ايدي الخدمه : `$ID`\n- اقصي حد للطلب : `$MAX`\n- ادنى حد للطلب : `$MIN`\n- السعر لكل 1 : *$PRICE* 
وصف الخدمه : [$description]",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [["text" => "🔙 رجوع", "callback_data" => "ENTERXDMA_$ID_XDm"]]
                ]
            ])
        ]);
        $bot->set('infos_' . $modes->get('help_' . $from_id), $text);
        $modes->delete('mode_' . $from_id);
        $modes->delete('help_' . $from_id);
    } else {
        bot('SendMessage', [
            'chat_id' => $chat_id,
            'text' => "*- خطأ تأكد من الصيغه المطلوبه ❌*",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [["text" => "🔙 رجوع", "callback_data" => "ENTERXDMA_" . $modes->get('help_' . $from_id)]]
                ]
            ])
        ]);
    }
}

$addxdmat_ = explode("addxdmat_",$data)[1];
if($addxdmat_){
    $name_qsm = $bot->get('qsms_name_'.$addxdmat_);
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
       'text' => "*- أرسل اسم الخدمه لاضافاتها الي قسم $name_qsm ✅*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "ENTERQSM_$addxdmat_"]],
            ]
        ])
    ]);
    $modes->set('mode_'.$from_id,'addxdma');
    $modes->set('help_'.$from_id,$addxdmat_);
    return;
}

if($modes->get('mode_'.$from_id) == 'addxdma' && $text){
    $idx = coderandom(10);
    bot('SendMessage', [
        'chat_id' => $chat_id,
        'text' => "*- خدمه $text التحكم ادناه 🔠*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode( [
            'inline_keyboard' => [
                [["text" => "تعيين المعلومات", "callback_data" => "ENTERXDMA_$idx"]],
                [["text" => "🔙 رجوع", "callback_data" => "xdmats"]]
            ]
        ])
    ]);
    $bot->set('xdmat_'.$text,$idx);
    $bot->set('xdmatname_'.$idx,$text);
    $bot->set('xdmatinqsm_'.$idx,$modes->get('help_'.$from_id));
    $bot->set('xdmat_'. $modes->get('help_'.$from_id) ,$bot->get('xdmat_'. $modes->get('help_'.$from_id))."\n$text");
    $modes->delete('mode_'.$from_id);
    $modes->delete('help_'.$from_id);
}

if (strpos($data, "ACCEDK_") === 0) {
    $parts = explode('_', str_replace('ACCEDK_', '', $data));
    $m_id = $parts[0] ?? null;
    $c_id = $parts[1] ?? null;

    if ($m_id && $c_id) {
        bot('editMessageReplyMarkup', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [["text" => "تم ارسال تنبيه للعضو ", "url" => "tg://user?id=$c_id"]],
                ]
            ])
        ]);

        bot('sendMessage', [
            'chat_id' => $c_id,
            'text' => "*- تم اكمال طلبك ✅*",
            'parse_mode' => 'Markdown',
            'reply_to_message_id' => $m_id,
        ]);
    }
}

if($data == 'addqsm'){
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
       'text' => "*- أرسل اسما تضعه للقسم ✅*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "xdmats"]],
            ]
        ])
    ]);
    $modes->set('mode_'.$from_id,'addqsm');
    return;
}

if($modes->get('mode_'.$from_id) == 'addqsm' && $text){
    $idx = coderandom(10);
    bot('SendMessage', [
        'chat_id' => $chat_id,
        'text' => "*- قسم $text التحكم ادناه 🔠*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "اضافه خدمات ➕", "callback_data" => "addxdmat_$idx"]],
                [["text" => "🔙 رجوع", "callback_data" => "xdmats"]]
            ]
        ])
    ]);
    $modes->delete('mode_'.$from_id);
    $modes->delete('help_'.$from_id);
    $bot->set('qsms',$bot->get('qsms')."\n$text");
    $bot->set('qsms_id_'.$text,$idx);
    $bot->set('qsms_name_'.$idx,$text);
    
}

if($data == 'makelinkhdia' or $data === 'make_hdia'){
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
       'text' => "*- أرسل عدد ال$a3ml داخل الهديه 🎁*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "BACKADMIN"]],
            ]
        ])
    ]);
    $modes->set('type_'.$from_id,$data);
    $modes->set('mode_'.$from_id,'makelinkhdia');
}

if($modes->get('mode_'.$from_id) == 'makelinkhdia' && is_numeric($text)){
    if($modes->get('type_'.$from_id) == 'makelinkhdia'){
        $type = 'الرابط';
    }else{
        $type = 'الكود';
    }
    bot('SendMessage', [
        'chat_id' => $chat_id,
        'text' => "*- كم مستخدم يمكنه استخدام $type 👤*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "BACKADMIN"]]
            ]
        ])
    ]);
    $modes->set('mode_'.$from_id,'makelinkhdia2');
    $modes->set('help_'.$from_id,$text);
    return;
}

if($modes->get('mode_'.$from_id) == 'makelinkhdia2' && is_numeric($text)){
    if($modes->get('type_'.$from_id) == 'makelinkhdia'){
        $type = '- رساله تضهر للمستخدم بعد اخذه ال$a3ml 📝';
    }else{
    $type = "- أسم الكود 📰";
    }
    bot('SendMessage', [
        'chat_id' => $chat_id,
        'text' => "*$type*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "BACKADMIN"]]
            ]
        ])
    ]);
    $modes->set('mode_'.$from_id,'makelinkhdia3');
    $modes->set('help2_'.$from_id,$text);
    return;
}

if($modes->get('mode_'.$from_id) == 'makelinkhdia3'){
    $THECOIN = $modes->get('help_'.$from_id);
    $TO = $modes->get('help2_'.$from_id);
    $MSG = $text;
    if($modes->get('type_'.$from_id) == 'makelinkhdia'){
        $get = coderandom(32);
        $type = "*• تم صنع رابط الهديه بقيمه $THECOIN $a3ml ل $TO شخص🎁*
- [https://t.me/$USRBOT?start=hdia$get]";
    }else{
        $type = "تم صنع كود هديه بقيمه $THECOIN ل $TO شخص 🎁
- الكود : `$text`";
$get = $text;
    }
    bot('SendMessage', [
        'chat_id' => $chat_id,
        'text' => "$type",
        'parse_mode' => 'Markdown',
    ]);
    bot('SendMessage', [
        'chat_id' => $chat_id,
        'text' => "*• رساله تضهر للمستخدم بعد اخذه ال$a3ml*
`$MSG`",
        'parse_mode' => 'Markdown',
    ]);
    $modes->set('hdia_'.$get,$THECOIN);
    $modes->set('hdia_count_'.$get,$TO);
    $modes->set('hdia_MSG_'.$get,$MSG);
    $modes->delete('mode_'.$from_id);
    $modes->delete('help_'.$from_id);
    $modes->delete('help2_'.$from_id);
    $modes->delete('help3_'.$from_id);
    return;
}
if($data == 'addcoins'){
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
       'text' => "*- أرسل ايدي العضو 🆔:*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "BACKADMIN"]],
            ]
        ])
    ]);
    $modes->set('mode_'.$from_id,'adder');
}
if($modes->get('mode_'.$from_id) == 'adder' && is_numeric($text)){
    $user_id = $text;
    bot('SendMessage', [
        'chat_id' => $chat_id,
        'text' => "*- أرسل عدد ال$a3ml لإضافتها للمستخدم 🆔:* `$user_id`",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "BACKADMIN"]]
            ]
        ])
    ]);
    $modes->set('mode_'.$from_id, 'add_amount');
    $modes->set('target_user', $user_id);
} elseif ($modes->get('mode_'.$from_id) == 'add_amount' && is_numeric($text)) {
    $amount = intval($text);
    $target_user = $modes->get('target_user');

    if($amount){
        $TOM->set('coins_'.$target_user,$TOM->get('coins_'.$target_user) + $amount);
        bot('SendMessage', [
            'chat_id' => $chat_id,
            'text' => "*✅ تم إضافة* `$amount` *$a3ml للمستخدم* 🆔 `$target_user`",
            'parse_mode' => 'Markdown'
        ]);
        $modes->delete('mode_'.$from_id);
    $modes->delete('help_'.$from_id);
    } else {
        bot('SendMessage', [
            'chat_id' => $chat_id,
            'text' => "*❌ يرجى إدخال رقم صحيح!*",
            'parse_mode' => 'Markdown'
        ]);
    }
}

if($data == "alqnwat"){
    bot('answerCallbackQuery',[
        'callback_query_id' => $update->callback_query->id,
        'text' => "ادناه يمكنك تضع قنوات و حساب فيه 😇",
        'show_alert' => true,
    ]);
    $data = 'alta3en';
}
if($data == "alnsos"){
    bot('answerCallbackQuery',[
        'callback_query_id' => $update->callback_query->id,
        'text' => "ادناه يمكنك ان تضع نصوص وكلايش فيه 😩",
        'show_alert' => true,
    ]);
    $data = 'alta3en';
}
if($data == "alnqat"){
    bot('answerCallbackQuery',[
        'callback_query_id' => $update->callback_query->id,
        'text' => "ادناه يمكنك ان تضع اعدادات ال$a3ml فيه 👊",
        'show_alert' => true,
    ]);
    $data = 'alta3en';
}
if($data=='SET_TH_NSHR'){
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
      'text' => "*- أرسل الرساله للطلبيه الأن :*
 (⌯ الهاشتاك المسموح لك بأستخدامها.)
 - `#a` - *لوضع اسم المستخدم وبداخله رابط الحساب*
 - `#b` - *لوضع اسم الحساب*
 - `#c` - *لوضع ايدي الحساب*
 - `#d` - *لوضع معرف المستخدم*
 - `#e` - *لوضع عدد ال$a3ml*
 - `#f` - *لوضع اسم الخدمه*
 - `#g` - *لوضع ايدي الطلب*
 - `#h` - *لوضع عدد الطلبات*
 - `#i` - *لوضع سعر الطلب*
 - `#j` - *لوضع العدد المطلوب*
 - `#k` - *لوضع اسم القسم*",
        'parse_mode' => 'Markdown', 
       'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "rsala_nshr"]],
            ]
        ])
    ]);
    $modes->set('mode_'.$from_id , $data);
    return;
}
if($text and $modes->get('mode_'.$from_id) == "SET_TH_NSHR"){
    bot('SendMessage', [
        'chat_id' => $chat_id,
        'text' => "*- تم حفظ رساله النشر .*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                
                [["text" => "رجوع", "callback_data" => "rsala_nshr"]]
            ]
        ])
    ]);
    
    $TH_START = str_replace(array('#a','#b' , '#c' , '#d' , '#e') , array("[$name](tg://user?id=$from_id)" ,"$name" , "$from_id" , "[$username]" ,$TOM->get('coins_'.$chat_id)) , $text);
    bot('SendMessage', [
        'chat_id' => $chat_id,
        'text' => "*- مثال لرساله النشر.*
$TH_START",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                
                [["text" => "رجوع", "callback_data" => "rsala_nshr"]]
            ]
        ])
    ]);

    $bot->set('rsala_nshr_text', "$text");
    $modes->delete('mode_'.$from_id);
}
if($data == 'rsala_nshr'){

    $NOW_STA =  $bot->get('rsala_nshr_text');
bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
      'text' => "*- قسم رساله نشر الطلب  .*
 ⌯ الحالي: `$NOW_STA`",
        'parse_mode' => 'Markdown', 
       'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "تعيين الرساله", "callback_data" => "SET_TH_NSHR"]],
                [["text" => "رجوع", "callback_data" => "alta3en"]],
            ]
        ])
    ]);

}
if($data == 'alta3en'){
    $amla_text = $bot->get('amla_text') ?? 'نقاط';
    $shares_coin = $bot->get('share') ?? "200";
    $hdia = $bot->get('hdia') ?? "75";
    $a3mola = $bot->get('3mola') ?? "15";
    $MEMBER_COIN = $bot->get("membertmoil") ?? "10";
    $JOINER_COIN = $bot->get("JOINtmoil") ?? "5";
    $policy_text = $bot->get('policy');
    $name_text = $bot->get('name_bot') ?? "DamKom";
    $link_text = $bot->get('linkurl');
    $payed_text = $bot->get('payed');
    $siana_text = $bot->get('siana');
    $channel_bot = $bot->get('chs_bot') ?? "@As_GTR";
    $channel_tlbat = $bot->get('chs_tlbat') ?? "لايوجد";
    $channel_support = $bot->get('chs_support') ?? "حساب المالك";
    $rsala_nshr_text = $bot->get('rsala_nshr_text') ?? 'افتراضي';
    if(!$siana_text){
        $siana_text = 'افتراضي';
    }else{
        $siana_text = 'نص';
    }
    if(!$policy_text){
        $policy_text = 'لايوجد';
    }else{
        $policy_text = 'نص';
    }
    if(!$link_text){
        $link_text = 'لايوجد';
    }else{
        $link_text = 'رابط';
    }
    if(!$payed_text){
        $payed_text = 'لايوجد';
    }else{
        $payed_text = 'نص';
    }
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*- قسم المعيين يمكنك التحكم ادناه ✅*
",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "عجلة الحظ", "callback_data" => "LUCK_SECTION"],["text" => "الاعداد", "callback_data" => "LUCK_SECTION"]],
                [["text" => "الهديه الاسبوعيه", "callback_data" => "ALHDIA_SBo3"],["text" => "الاعداد", "callback_data" => "ALHDIA_SBo3"]],
                [["text" => "- قنوات + حساب -", "callback_data" => "alqnwat"]],
                [["text" => "الدعم الفني", "callback_data" => "setch_support"],["text" => "$channel_support", "callback_data" => "setch_support"]],
                [["text" => "قناة البوت", "callback_data" => "setch_bot"],["text" => "$channel_bot", "callback_data" => "setch_bot"]],
                [["text" => "قناة الطلبات", "callback_data" => "setch_tlbat"],["text" => "$channel_tlbat", "callback_data" => "setch_tlbat"]],
                [["text" => "رساله نشر الطلب", "callback_data" => "view_rsala_nshr"],["text" => "$rsala_nshr_text", "callback_data" => "rsala_nshr"]],
                [["text" => "- النصوص -", "callback_data" => "alnsos"]],
                [["text" => "أسم عملة البوت", "callback_data" => "setct_amla_text"],["text" => "$amla_text", "callback_data" => "setct_amla_text"]],
                [["text" => "أسم البوت", "callback_data" => "setct_name_bot"],["text" => "$name_text", "callback_data" => "setct_name_bot"]],
                [["text" => "رساله الصيانه", "callback_data" => "setct_siana"],["text" => "$siana_text", "callback_data" => "setct_siana"]],
                [["text" => "الشروط والاحكام", "callback_data" => "setct_policy"],["text" => "$policy_text", "callback_data" => "setct_policy"]],
                [["text" => "رابط شرح", "callback_data" => "setct_policy"],["text" => "$link_text", "callback_data" => "setct_linkurl"]],
                [["text" => "الشحن", "callback_data" => "setct_payed"],["text" => "$payed_text", "callback_data" => "setct_payed"]],
                [["text" => "- ال$a3ml -", "callback_data" => "alnqat"]],
                [["text" => "الاشتراك بالقنوات (تمويل)", "callback_data" => "setcc_JOINtmoil"],["text" => "$JOINER_COIN", "callback_data" => "setcc_JOINtmoil"]],
                [["text" => "العضو الواحد (تمويل)", "callback_data" => "setcc_membertmoil"],["text" => "$MEMBER_COIN", "callback_data" => "setcc_membertmoil"]],
                [["text" => "مشاركه الرابط", "callback_data" => "setcc_share"],["text" => "$shares_coin", "callback_data" => "setcc_share"]],
                [["text" => "الهديه", "callback_data" => "setcc_hdia"],["text" => "$hdia", "callback_data" => "setcc_hdia"]],
                [["text" => "عموله التحويل", "callback_data" => "setcc_3mola"],["text" => "$a3mola", "callback_data" => "setcc_3mola"]],
                [["text" => "🔙 رجوع", "callback_data" => "BACKADMIN"]],
            ]
        ])
    ]);
    $modes->delete('mode_'.$from_id);
    $modes->delete('help_'.$from_id);
}

if(!$bot->get('ALhdia_3bo3iaa')){
$bot->set('ALhdia_3bo3iaa' , '❌');
}


$bbLuck = explode('bbLuck_' , $data)[1];
if($bbLuck){
    $RR = $bot->get('Luck_enabled');
    $TO = ($RR == '✅') ? '❌' : '✅';
    $bot->set('Luck_enabled', $TO);
    $data = 'LUCK_SECTION';
}


if($data == 'LUCK_SECTION'){
    $from = $bot->get('Luck_from') ?? "10";
    $to = $bot->get('Luck_to') ?? "100";
    $status = $bot->get('Luck_enabled') ?? '❌';

    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*- قسم عجلة الحظ 🎯*
- النقاط من: $from إلى: $to
- الحالة: $status
",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "الحالة : $status", "callback_data" => "bbLuck_1"]],
                [["text" => "تعيين الحد الأدنى والأقصى", "callback_data" => "setLuckRange"]],
                [["text" => "🔙 رجوع", "callback_data" => "alta3en"]],
            ]
        ])
    ]);
    $modes->delete('mode_'.$from_id);
    return;
}


if($data == 'setLuckRange'){
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "• أرسل الحد الأدنى والأقصى بهذا الشكل:\n`10-100`\n(أرقام فقط وبينهما -)",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text'=>'🔙 رجوع','callback_data'=>'LUCK_SECTION']]
            ]
        ])
    ]);
    $modes->set('mode_'.$from_id, 'set_LUCK_RANGE');
    return;
}

if($modes->get('mode_'.$from_id) == 'set_LUCK_RANGE'){
    if(preg_match('/^(\d+)-(\d+)$/', $text, $match)){
        $min = (int)$match[1];
        $max = (int)$match[2];

        if($min < $max){
            $bot->set('Luck_from', $min);
            $bot->set('Luck_to', $max);
            bot('SendMessage', [
                'chat_id' => $chat_id,
                'text' => "✅ تم تعيين عجلة الحظ من *$min* إلى *$max* $a3ml.",
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [['text'=>'🔙 رجوع','callback_data'=>'LUCK_SECTION']]
                    ]
                ])
            ]);
            $modes->delete('mode_'.$from_id);
        }else{
            bot('SendMessage', [
                'chat_id' => $chat_id,
                'text' => "⚠️ الحد الأدنى يجب أن يكون أصغر من الحد الأقصى. أعد المحاولة:",
            ]);
        }
    }else{
        bot('SendMessage', [
            'chat_id' => $chat_id,
            'text' => "⚠️ صيغة غير صحيحة. استخدم مثل: `10-100`",
            'parse_mode' => 'Markdown'
        ]);
    }
    return;
}

$bbHdia_ = explode('bbHdia_' , $data)[1];
if($bbHdia_){
    $RR= $bot->get('ALhdia_3bo3iaa');
    if($RR=='✅'){
        $TO = '❌';
    }else{
        $TO = '✅';
    }
    $bot->set('ALhdia_3bo3iaa' , $TO);
    $data = 'ALHDIA_SBo3';
}
if($data == 'ALHDIA_SBo3'){
    $a3d_hdia=$bot->get('ALhdia_3bo3ia') ?? '100';
    $hala_a3bo3 = $bot->get('ALhdia_3bo3iaa');
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*- قسم الهديه الاسبوعيه ✅*
- عدد الهديه : $a3d_hdia
",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "الحالة : $hala_a3bo3", "callback_data" => "bbHdia_3bo3"]],
                [["text" => "تعيين العدد", "callback_data" => "t3en_ALHDIA_SBo3"]],
             
                [["text" => "🔙 رجوع", "callback_data" => "alta3en"]],
            ]
        ])
    ]);
    $modes->delete('mode_'.$from_id);
    $modes->delete('help_'.$from_id);
}

if($data == 't3en_ALHDIA_SBo3'){
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "• أرسل عدد الهدايا الأسبوعية (رقم فقط):",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text'=>'🔙 رجوع','callback_data'=>'ALHDIA_SBo3']]
            ]
        ])
    ]);
    $modes->set('mode_'.$from_id, 'set_ALHDIA_SBo3');
    return;
}

if($modes->get('mode_'.$from_id) == 'set_ALHDIA_SBo3'){
    if(is_numeric($text)){
        $bot->set('ALhdia_3bo3ia', $text);
        bot('SendMessage', [
        'chat_id' => $chat_id,
            'text' => "✅ تم تعيين عدد الهدايا الأسبوعية إلى: *$text*",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text'=>'🔙 رجوع','callback_data'=>'ALHDIA_SBo3']]
                ]
            ])
        ]);
        $modes->delete('mode_'.$from_id);
    }else{
        bot('SendMessage', [
        'chat_id' => $chat_id,
            'text' => "⚠️ الرجاء إدخال رقم فقط، جرب مرة أخرى:",
        ]);
    }
    return;
}

$setch_ = explode("setch_" , $data)[1];
if($setch_){
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*• أرسل المعرف (فقط المعرف) 😺*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "alta3en"]],
            ]
        ])
    ]);
    $modes->set('mode_'.$from_id,'seter2');
    $modes->set('help_'.$from_id,$setch_);
}

if($text and $modes->get('mode_'.$from_id) == 'seter2'){
    $user = str_replace('@', '' , $text);
    $bot->set('chs_' . $modes->get('help_'.$from_id) , $user);
    bot('SendMessage', [
        'chat_id' => $chat_id,
        'text' => "*• تم الحفظ *([@$user]) ✅",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
        'inline_keyboard' => [
            [["text" => "🔙 رجوع", "callback_data" => "alta3en"]],
        ]
    ])
    ]);
    $modes->delete('mode_'.$from_id);
$modes->delete('help_'.$from_id);
}
$setcc_ = explode("setct_",$data)[1];
if($setcc_){
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*• أرسل المحتوى لحفظه :*
",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "alta3en"]],
            ]
        ])
    ]);
    $modes->set('mode_'.$from_id,'seter1');
    $modes->set('help_'.$from_id,$setcc_);
}
if($text and $modes->get('mode_'.$from_id) == 'seter1'){
        $bot->set($modes->get('help_'.$from_id),$text);
        bot('SendMessage', [
            'chat_id' => $chat_id,
            'text' => "• تم تعيين المحتوى ✅",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "alta3en"]],
            ]
        ])
        ]);
        $modes->delete('mode_'.$from_id);
    $modes->delete('help_'.$from_id);
    
}


$setcc_ = explode("setcc_",$data)[1];
if($setcc_){
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*• أرسل العدد لحفظه :*
",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "alta3en"]],
            ]
        ])
    ]);
    $modes->set('mode_'.$from_id,'seter');
    $modes->set('help_'.$from_id,$setcc_);
}
if($text and $modes->get('mode_'.$from_id) == 'seter'){
    if(is_numeric($text)){
        $bot->set($modes->get('help_'.$from_id),$text);
        bot('SendMessage', [
            'chat_id' => $chat_id,
            'text' => "• تم تعيين $text ✅",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "alta3en"]],
            ]
        ])
        ]);
        $modes->delete('mode_'.$from_id);
    $modes->delete('help_'.$from_id);
    }else{
        bot('SendMessage', [
            'chat_id' => $chat_id,
            'text' => "*• في هذا القسم فقط ارسال الارقام مسموح ❌*",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "alta3en"]],
            ]
        ])
        ]);
    }
    
}

$STARTBLOCK_ = explode('STARTBLOCK_' , $data)[1];
if($STARTBLOCK_){
    $BLOCKSx = $bot->get("blocks") ?? [];
    if (!in_array($STARTBLOCK_, $BLOCKSx)) {
        $BLOCKSx[] = $STARTBLOCK_;
        $bot->set("blocks", $BLOCKSx);
        bot('answerCallbackQuery',[
        'callback_query_id' => $update->callback_query->id,
        'text' => "تم حظره $STARTBLOCK_ .",
        'show_alert' => true,
    ]);
    }else{
        bot('answerCallbackQuery',[
        'callback_query_id' => $update->callback_query->id,
        'text' => "هذا  المستخدم محظور من قبل $STARTBLOCK_ .",
        'show_alert' => true,
    ]);
    }
}

$NOTALLOWLINK_ = explode("NOTALLOWLINK_" , $data)[1];
if($NOTALLOWLINK_){
    $STAT = $THE_LINKORS->get("I_UER_$NOTALLOWLINK_");
    if($STAT){
        bot('answerCallbackQuery',[
        'callback_query_id' => $update->callback_query->id,
        'text' => "تم رفض السماح $NOTALLOWLINK_ .",
        'show_alert' => true,
    ]);
    $THE_LINKORS->delete("I_UER_$NOTALLOWLINK_");
    }else{
        bot('answerCallbackQuery',[
        'callback_query_id' => $update->callback_query->id,
        'text' => "هذا المستخدم لايملك سماح $NOTALLOWLINK_ .",
        'show_alert' => true,
    ]);
    }
               
}
}else{

  if ($bot->get('HIMAIA_passworder') == 'مفعل ✅' && $bot->get('HRMZAR_RMZ')) {
    if(!$THE_LINKORS->get("I_UER_$from_id")){
        if ($text == '/start') {
            bot('SendMessage', [
                'chat_id' => $chat_id,
                'text' => "*• تم رفض استخدامك للبوت بسبب الحمايات الخاصة ❌*\n- اكتب الرمز السري للدخول :",
                'parse_mode' => 'Markdown',
            ]);
            $modes->set('mode_' . $from_id, 'IM_IN_HMAIAA_PASSWORD');
            return;
        }

        if ($text && $modes->get('mode_' . $from_id) == 'IM_IN_HMAIAA_PASSWORD') {
            if ($text == $bot->get('HRMZAR_RMZ')) {
                 $THE_LINKORS->set("3DD_MSMOH_" , $THE_LINKORS->get("3DD_MSMOH_") + 1);
            $NOW_CC = $THE_LINKORS->get("3DD_MSMOH_");
            
                bot('SendMessage', [
                    'chat_id' => $chat_id,
                    'text' => "*• تم السماح باستخدامك ✅*\n- أرسل /start .",
                    'parse_mode' => 'Markdown',
                ]);
                if($bot->get('HIMAIA_notifa') == "✅"){
                bot('SendMessage', [
                'chat_id' => $ADMIN,
                'text' => "*• تم سماح لشخص جديد باستخدام البوت ✅*
- الاسم : [$name](tg://user?id=$from_id) , `$from_id` 
- المعرف : [@$user] ,
- عبر : رمز سري
`$text`

- اصبح عدد المسموحين لهم (*$NOW_CC*) .",
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [["text" => "- ($name) -", "url" => "tg://user?id=$from_id"]],
                    [["text" => "عدم السماح 🔖", "callback_data" => "NOTALLOWLINK_$from_id"]],
                    [["text" => "اعطاء حظر ⛔️", "callback_data" => "STARTBLOCK_$from_id"]],
                ]
            ])
            ]);
        }
                $ALLOWS =  $THE_LINKORS->get("ALLOWS") ?? [];
    if (!in_array($from_id, $ALLOWS)) {
        $ALLOWS[] = $from_id;
         $THE_LINKORS->set("ALLOWS", $ALLOWS);
    }
                $modes->delete('mode_' . $from_id);
                 $THE_LINKORS->set("I_UER_$from_id" , 'ok');
            } else {
                bot('SendMessage', [
                    'chat_id' => $chat_id,
                    'text' => "*• رمز الدخول خاطئ ❌*\n- الرجاء كتابة رمز الدخول بشكل صحيح لتجنب تجميد حسابك !",
                    'parse_mode' => 'Markdown',
                ]);
            }
            return;
        }
    }
}

if($bot->get('HIMAIA_LIN_KER') == 'مفعل ✅' and $THE_LINKORS->get('THE_LINK')){
    if(!$THE_LINKORS->get("I_UER_$from_id")){
    if(preg_match('/start/' , $text)){
        $U = explode("start " , $text)[1];
        if($U == $THE_LINKORS->get('THE_LINK')){
            $THE_LINKORS->set("3DD_MSMOH_" , $THE_LINKORS->get("3DD_MSMOH_") + 1);
            $NOW_CC = $THE_LINKORS->get("3DD_MSMOH_");
            bot('SendMessage', [
                    'chat_id' => $chat_id,
                    'text' => "*• تم السماح باستخدامك ✅*\n- أرسل /start .",
                    'parse_mode' => 'Markdown',
                ]);
                if($bot->get('HIMAIA_notifa') == "✅"){
                bot('SendMessage', [
                'chat_id' => $ADMIN,
                'text' => "*• تم سماح لشخص جديد باستخدام البوت ✅*
- الاسم : [$name](tg://user?id=$from_id) , `$from_id` 
- المعرف : [@$user] ,
- عبر : رابط دخول 
[https://t.me/$usrbot?start=$THE_LINK]

- اصبح عدد المسموحين لهم (*$NOW_CC*) .",
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [["text" => "- ($name) -", "url" => "tg://user?id=$from_id"]],
                    [["text" => "عدم السماح 🔖", "callback_data" => "NOTALLOWLINK_$from_id"]],
                    [["text" => "اعطاء حظر ⛔️", "callback_data" => "STARTBLOCK_$from_id"]],
                ]
            ])
            ]);
        }
            $ALLOWS =  $THE_LINKORS->get("ALLOWS") ?? [];
    if (!in_array($from_id, $ALLOWS)) {
        $ALLOWS[] = $from_id;
         $THE_LINKORS->set("ALLOWS", $ALLOWS);
    }
                 $THE_LINKORS->set("I_UER_$from_id" , 'ok');
                return;
        }
    }
    bot('SendMessage', [
                'chat_id' => $chat_id,
                'text' => "*• تم رفض استخدامك للبوت بسبب الحمايات الخاصة ❌*",
                'parse_mode' => 'Markdown',
            ]);
            
    return;
        }
}


if (preg_match("/^EMOJI_VERIF_(.*)$/", $data, $match)) {
    $user_choice = $match[1];
    $expected = $modes->get("HELPER_$from_id");

    if ($expected == $user_choice) {
        $THE_LINKORS->set("I_UER3_$from_id", 'ok');
 $ALLOWS =  $THE_LINKORS->get("ALLOWS") ?? [];
    if (!in_array($from_id, $ALLOWS)) {
        $ALLOWS[] = $from_id;
         $THE_LINKORS->set("ALLOWS", $ALLOWS);
    }
            $THE_LINKORS->set("3DD_MSMOH_", $THE_LINKORS->get("3DD_MSMOH_") + 1);
            $NOW_CC = $THE_LINKORS->get("3DD_MSMOH_");
        bot('editMessageText', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => "*• تم التحقق منك بنجاح ✅*\n- أرسل /start .",
            'parse_mode' => 'Markdown',
        ]);

        $modes->delete("HELPER_$from_id");
        $modes->delete("mode_$from_id");

        if ($bot->get('HIMAIA_notifa') == "✅") {
            bot('SendMessage', [
                'chat_id' => $ADMIN,
                'text' => "*• اكمل شخص جديد التحقق عبر الرموز التعبيرية ✅*\n".
                          "- الاسم : [$name](tg://user?id=$from_id)\n".
                          "- المعرف : [@$user] ,\n".
                          "- ID : `$from_id`\n".
                          "- عبر: الرموز التعبيرية 🐾",
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [["text" => "اعطاء حظر ⛔️", "callback_data" => "STARTBLOCK_$from_id"]]
                    ]
                ])
            ]);
        }
    } else {
        bot('answerCallbackQuery', [
            'callback_query_id' => $update->callback_query->id,
            'text' => "❌ اختيار خاطئ! حاول مرة أخرى.",
            'show_alert' => true
        ]);
        bot('DeleteMessage', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
        ]);
        
    }
}

if ($bot->get('HIMAIA_EMOJI_CHECK') == "✅") {
    if (!$THE_LINKORS->get("I_UER2_$from_id")) {
        $captcha = sendEmojiCaptcha($chat_id);
        $modes->set('HELPER_' . $from_id, $captcha['code']);
        $modes->set('mode_' . $from_id, 'EMOJI_CAPTCHA');
        return;
    }
}




if ($bot->get('HIMAIA_THQQ_BSRY') == "✅") {
    if(!$THE_LINKORS->get("I_UER2_$from_id")){
    if ($text == "/start") {
        $T = sendCaptcha($chat_id);
        $modes->set('HELPER_' . $from_id, $T['code']);
        $modes->set('mode_' . $from_id, 'IM_IN_HIMAIA_THQQ_BSRY');
        return;
    }

    if ($modes->get('mode_' . $from_id) == 'IM_IN_HIMAIA_THQQ_BSRY') {
        $expected_code = $modes->get('HELPER_' . $from_id);
        if ($text == $expected_code) {
             $ALLOWS =  $THE_LINKORS->get("ALLOWS") ?? [];
    if (!in_array($from_id, $ALLOWS)) {
        $ALLOWS[] = $from_id;
         $THE_LINKORS->set("ALLOWS", $ALLOWS);
    }
            $THE_LINKORS->set("3DD_MSMOH_", $THE_LINKORS->get("3DD_MSMOH_") + 1);
            $NOW_CC = $THE_LINKORS->get("3DD_MSMOH_");
            bot('SendMessage', [
                'chat_id' => $chat_id,
                'text' => "*• تم التحقق منك ✅*\n- أرسل /start .",
                'parse_mode' => 'Markdown',
            ]);
            if ($bot->get('HIMAIA_notifa') == "✅") {
                bot('SendMessage', [
                    'chat_id' => $ADMIN,
                    'text' => "*• اكمل شخص جديد التحقق البصري✅*\n".
                              "- الاسم : [$name](tg://user?id=$from_id) , `$from_id`\n".
                              "- المعرف : [@$user] ,\n".
                              "- عبر : التحقق البصري\n\n".
                              "",
                    'parse_mode' => 'Markdown',
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [["text" => "- ($name) -", "url" => "tg://user?id=$from_id"]],
                            [["text" => "اعطاء حظر ⛔️", "callback_data" => "STARTBLOCK_$from_id"]],
                        ]
                    ])
                ]);
            }
            $modes->delete('HELPER_' . $from_id);
            $modes->delete('mode_' . $from_id);
            
            $THE_LINKORS->set("I_UER2_$from_id", 'ok');
            return;
        }
    }
    return;
}
}



if ($bot->get('HIMAIA_JIHAT_ITSAL') == '✅') {
    if (!$modes->get('JIHAT_ITSAL_' . $from_id)) {
        if ($text == '/start') {
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "أرسل جهة اتصالك",
                'reply_to_message_id' => $message_id,
                'reply_markup' => json_encode([
                    'keyboard' => [
                        [['text' => 'أرسال جهة اتصالي', 'request_contact' => true]]
                    ],
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true
                ])
            ]);
            $modes->set('mode_' . $from_id, 'IM_IN_HIMAIA_JIHAT_ITSAL');
            return;
        }

        if (isset($update->message->contact->phone_number)) {
            $PHONE = $update->message->contact->phone_number;
            if ($update->message->contact->user_id == $from_id) {
                bot('SendMessage', [
                    'chat_id' => $chat_id,
                    'text' => "*• تم التحقق من جهة اتصالك ✅*\n- أرسل /start .",
                    'parse_mode' => 'Markdown',
                ]);
                 $ALLOWS =  $THE_LINKORS->get("ALLOWS") ?? [];
    if (!in_array($from_id, $ALLOWS)) {
        $ALLOWS[] = $from_id;
         $THE_LINKORS->set("ALLOWS", $ALLOWS);
    }
                $modes->set('JIHAT_ITSAL_' . $from_id, 'ok');
            } else {
                bot('SendMessage', [
                    'chat_id' => $chat_id,
                    'text' => "*• جهة اتصال مزيفة لا تتطابق مع حسابك ❌*",
                    'parse_mode' => 'Markdown',
                ]);
            }
            return;
        }
    }
}




    $F = explode('start ', $text)[1];

    if ($F) {
        $mode = $F;
        $دخل = "رابط دعوه";
        if (preg_match('/by/', $F)) {
            $دخل = "رابط تحويل";
        }
        if (preg_match('/hdia/', $F)) {
            $دخل = "رابط هديه";
        }
    } else {
        $mode = 'BACK';
        $دخل = "معرف البوت";
    }

    if (!$users->get($from_id)) {
        if ($name != null) {
            $users->set($from_id, $name);
            $users->set('mems', $users->get('mems') . "\n$from_id");

            if ($user) {
                $user = "@$user";
            } else {
                $user = 'بدون معرف';
            }

            if ($bot->get('generals_entry') != '❌') {
                $mems = count(explode("\n", $users->get('mems')));
                $mems = $mems + $FAKEOS;

                if ($name != null) {
                    bot('SendMessage', [
                        'chat_id' => $ADMIN,
                        'text' => "*دخل شخص جديد للبوت 🔖*\n* الاسم :* [$name](tg://user?id=$from_id) \n*• الايدي :* `$from_id`\n*• المعرف :* [$user]\n*• دخل عبر : $دخل*\n\n*• عدد الاعضاء : $mems 🔗*",
                        'parse_mode' => 'Markdown',
                    ]);
                }

                foreach ($ADMINS as $ADMIN) {
                    if ($name != null) {
                        bot('SendMessage', [
                            'chat_id' => $ADMIN,
                            'text' => "*دخل شخص جديد للبوت 🔖*\n* الاسم :* [$name](tg://user?id=$from_id) \n*• الايدي :* `$from_id`\n*• المعرف :* [$user]\n*• دخل عبر : $دخل*\n\n*• عدد الاعضاء : $mems 🔗*",
                            'parse_mode' => 'Markdown',
                        ]);
                    }
                }
            }
        }
    }
$channels = $shtrak->get('channels') ?: [];

if (!empty($channels)) {
    $x = 0;
    $keyboard = [];

    foreach ($channels as $index => $channel) {
        if(CHECKIFADMIN($channel)){
        $required_count = $shtrak->get("channel_count_$index") ?: 0;
        $current_count = $SHTRAK_CATHCH->get("channel_count_$index") ?: 0;

        if ($current_count >= $required_count && $current_count != 0) {
            $TT = bot('SendMessage', [
                'chat_id' => $ADMIN,
                'text' => "*تم انتهاء اضافه العدد المطلوب للقناة ✅*
• القناة : [$channel] .
• العدد المطلوب : *$required_count*
• خدمه الاشتراك الاجباري الممول 
",
                'parse_mode' => 'Markdown'
            ]);

            bot('SendMessage', [
                'chat_id' => $ADMIN,
                'text' => "*تم ازاله القناة من الاشتراك الاجباري ❌ *",
                'reply_to_message_id' => $TT->result->message_id,
                'parse_mode' => 'Markdown'
            ]);

            unset($channels[$index]);
            $channels = array_values($channels); 
            $shtrak->set('channels', $channels);
            $shtrak->delete("channel_count_$index");
            $SHTRAK_CATHCH->delete("channel_count_$index");

            continue; 
        }

        if (!$required_count) {
            $required_count = 'x';
        }

        if ($current_count < $required_count || $required_count == 'x') {
            $already_checked = $SHTRAK_CATHCH->get("me_in_$from_id") ?: [];

            if (!in_array($index, $already_checked)) {
                $already_checked[] = $index;
                $SHTRAK_CATHCH->set("me_in_$from_id", $already_checked);
            }

            $is_subscribed = X_neW($channel, $chat_id);
            $subscription_status = $is_subscribed ? "✅ مشترك" : "❌ غير مشترك";

            $channel_info = json_decode(json_encode(bot('getChat', ['chat_id' => $channel])), true);
            $channel_name = $channel_info['result']['title'] ?? $channel;

            $keyboard[] = [
                ['text' => "$channel_name", 'url' => "https://t.me/" . ltrim($channel, '@')],
                ['text' => "$subscription_status", 'url' => "https://t.me/" . ltrim($channel, '@')],
            ];

            if (!$is_subscribed) {
                $x += 1;
                if($SHTRAK_CATHCH->get("MODANA_{$from_id}_{$index}") != 'DONE'){
                $SHTRAK_CATHCH->set("MODANA_{$from_id}_{$index}", "NO");
                }
            } else {
                if($SHTRAK_CATHCH->get("MODANA_{$from_id}_{$index}") != 'DONE'){
                $SHTRAK_CATHCH->set("MODANA_{$from_id}_{$index}", "OK");
                }
            }
        }
    }

    if ($x >= 1) {
        $keyboard[] = [['text' => "تحقق من الاشتراك بالقنوات ✅", 'callback_data' => "checkchk_$mode"]];
        $reply_markup = json_encode(['inline_keyboard' => $keyboard]);
        $msg = "❗️┇عذراً، عليك الأشتراك في قنوات البوت أولاً:";

        if (!$data) {
            bot('SendMessage', [
                'chat_id' => $chat_id,
                'text' => $msg,
                'reply_markup' => $reply_markup,
            ]);
        } else {
            bot('EditMessageText', [
                'chat_id' => $chat_id,
                'message_id' => $message_id,
                'text' => $msg,
                'reply_markup' => $reply_markup,
            ]);
        }

        return; 
    }
}
}

$T = $SHTRAK_CATHCH->get("me_in_$from_id");
if (is_array($T)) {
    foreach ($T as $r) {
        $status = $SHTRAK_CATHCH->get("MODANA_{$from_id}_{$r}");
        $required = $shtrak->get("channel_count_$r") ?: 0;
        $current = $SHTRAK_CATHCH->get("channel_count_$r") ?: 0;

        if ($status == "OK" && $current < $required) {
            $SHTRAK_CATHCH->set("channel_count_$r", $current + 1);
            $SHTRAK_CATHCH->set("MODANA_{$from_id}_{$r}" , 'DONE');
        }

    }

    $SHTRAK_CATHCH->delete("me_in_$from_id");
}




$checkchk_ = explode('checkchk_',$data)[1];
if($checkchk_){
    if($checkchk_ == 'BACK'){
        $data = 'BACK';
    }else{
        bot('DeleteMessage', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
        ]);
        $text = '/start '. $checkchk_;

    }
}

    if($bot->get('generals_siana') == "✅"){
        $siana = $bot->get('siana') ?? "عذرا البوت تحت الصيانه في الوقت الحالي ⚒️";
        if($text){
        bot('SendMessage', [
            'chat_id' => $chat_id,
            'text' => "$siana",
            'parse_mode' => 'Markdown',
        ]);
        $text = '';
    }
        if($data){
            bot('answerCallbackQuery',[
                'callback_query_id' => $update->callback_query->id,
                'text' => str_replace('*','',$siana),
                'show_alert' => true,
            ]);
        $data = '';
        }
    }
}
function isBotAdmin($chat_id) {
    $bot_info = bot('getMe');
    if (!isset($bot_info->result->id)) {
        return false;
    }
    
    $bot_id = $bot_info->result->id;
    $admins = bot('getChatAdministrators', ['chat_id' => $chat_id]);
    
    if (!isset($admins->result)) {
        return false;
    }
    
    foreach ($admins->result as $admin) {
        if ($admin->user->id == $bot_id) {
            return true;
        }
    }
    return false;
}

     $YY = ''; 
    $iLL = 0;

    $hl_mfto7 = $bot->get('al3qobat') ?? 'معطلة ❌';
    $YU = $bot->get('nqat_xsm') ?? 10;



    if ($hl_mfto7 != 'معطلة ❌') {
        $SEENOR = $TMOIL->get("SEEN_$from_id");
      

        foreach ($SEENOR as $RT) {
            if ($TMOIL->get("JOINED_{$RT}_$from_id")) {
                $INFOS = $TMOIL->get('INFOS_' . $RT);
                $parts = explode('|', $INFOS);
                list($COUNT, $PRICE_TMOIL, $CHANNEL, $OWNER) = array_pad($parts, 4, 'N/A');

                if ($CHANNEL != 'N/A' && isBotAdmin($CHANNEL)) {
                    $subscription_status = X_neW($CHANNEL, $from_id) ? "✅ مشترك" : "❌ غير مشترك";

                    if ($subscription_status == "❌ غير مشترك") {
                        $mgh = $TMOIL->get("mghadra_$from_id") ?: [];

                        if (!in_array($RT, $mgh)) {
                            $mgh[] = $RT;
                            $TMOIL->set("mghadra_$from_id", $mgh);

                            $YY .= "[$CHANNEL] | مغادر ❌\n";
                            $iLL += 1;
                        }
                    }
                }
            }
        }
    }

    if ($iLL > 0) {
        $ijmale = $YU * $iLL;
        $current_coins = intval($TOM->get('coins_'.$from_id));
        $new_balance = max(0, $current_coins - $ijmale);

        bot('SendMessage', [
            'chat_id' => $from_id,
            'text' => "$YY\n- تم خصم العدد *$ijmale* من نقاطك 
*⁉️ لماذا *
- اعطيتك نقاط مقابل اشتراكك في القنوات لكنك خالفت الشروط وغادرت منها
- إن قمت بإعادة الاشتراك، سيتم تطبيق خصومات مضاعفة لاحقًا ✅",
            'parse_mode' => 'Markdown'
        ]);

        $TOM->set('coins_'.$from_id, $new_balance);
    }


if(preg_match('/start/',$text)){
    $ID = explode('start ', $text)[1];
    if($ID){
        if(preg_match('/hdia/',$ID)){
    $get = explode('hdia',$ID)[1];
    if(!$modes->get('hdia_'.$get)){
        bot('SendMessage', [
            'chat_id' => $chat_id,
            'text' => "رابط هديه غير صالح او منتهيه الصالحيه ❌",
            'parse_mode' => 'Markdown',
        ]);
        return;
    }
    $COOIN = $modes->get('hdia_'.$get);
    $MSG = $modes->get('hdia_MSG_'.$get);
    $COUNT_HDIA = $modes->get('hdia_count_'.$get);
    if($COUNT_HDIA >= $modes->get('hdia_count_now_'.$get)){
        if(!$catche->get('IM_USE_'.$from_id.'_'.$get)){
    $modes->set('hdia_count_now_'.$get,$modes->get('hdia_count_now_'.$get) + 1);
    $TOM->set('coins_'.$from_id,$TOM->get('coins_'.$from_id) + $COOIN);
    $TOM->set('hdiacoins_'.$from_id,$TOM->get('hdiacoins_'.$from_id) + $COOIN);
    $TOM->set('hdiax_'.$from_id,$TOM->get('hdiax_'.$from_id) + 1);
    foreach($ADMINS as $ADMIN){
        $TBQA = $COUNT_HDIA - $modes->get('hdia_count_now_'.$get);
        bot('SendMessage', [
            'chat_id' => $ADMIN,
            'text' => "*استخدم شخص رابط الهديه بقيمه $THECOIN 👤*
[$name](tg://user?id=$from_id) | `$from_id`
تبقي *$TBQA* مستخدم يستخدمه
",
            'parse_mode' => 'Markdown',
        ]);
    }
    bot('SendMessage', [
        'chat_id' => $chat_id,
        'text' => "لقد حصلت على $COOIN $a3ml من خلال رابط الهديه 🎁",
        'parse_mode' => 'Markdown',
    ]);
    bot('SendMessage', [
        'chat_id' => $chat_id,
        'text' => "$MSG",
        'parse_mode' => 'Markdown',
    ]);
    $catche->set('IM_USE_'.$from_id.'_'.$get , true);
}else{
    bot('SendMessage', [
        'chat_id' => $chat_id,
        'text' => "استلمت $a3ml من هذا الرابط من قبل ✅",
        'parse_mode' => 'Markdown',
    ]);
}
}else{
    if($modes->get('hdia_'.$get)){
        $modes->delete('hdia_'.$get);
        $modes->delete('hdia_MSG_'.$get);
        $modes->delete('hdia_count_'.$get);
        $modes->delete('hdia_count_now_'.$get);
    }
    bot('SendMessage', [
        'chat_id' => $chat_id,
        'text' => "رابط هديه غير صالح او منتهيه الصالحيه ❌",
        'parse_mode' => 'Markdown',
    ]);
}
return;
        }
        if(!preg_match('/by/',$ID)){
        $ID = TOMdecode($ID);
        if(!$users->get('im_in_bot_'.$from_id) and is_numeric($ID)){
            $shares_coin = $bot->get('share') ?? "200";
            $name_freind = $users->get('im_in_bot_'.$ID);
            bot('SendMessage', [
                'chat_id' => $chat_id,
                'text' => "لقد دخلت لرابط الدعوه الخاص بصديقك $name_freind وحصل على $a3ml قدرها $shares_coin 👋",
                'parse_mode' => 'Markdown',
            ]);
            bot('SendMessage', [
                'chat_id' => $ID,
                'text' => "دخل شخص جديد عبر رابط الدعوه وحصلت على $shares_coin من $a3ml ➕
- من : [$name](tg://user?id=$from_id) | `$from_id` 👤",
                'parse_mode' => 'Markdown',
            ]);
            $referrals = $shares->get('top_refs') ?? [];
            $referrals[$ID] = ($referrals[$ID] ?? 0) + 1;
        
            $shares->set('top_refs', $referrals);
            $TOM->set('countshare_'.$ID,$TOM->get('countshare_'.$ID) + 1);
            $TOM->set('coinsshare_'.$ID,$TOM->get('coinsshare_'.$ID) + $shares_coin);
            $TOM->set('coins_'.$ID,$TOM->get('coins_'.$ID) + $shares_coin);
            $users->set('im_in_bot_'.$from_id,$name);
        }
    }else{

        $get = explode('by',$ID)[1];
        $coin_link = $modes->get('LINK_'.$get);
        $OWNER = $modes->get('LINK_OWNER_'.$get);
        if($coin_link){
            bot('SendMessage', [
                'chat_id' => $chat_id,
                'text' => "*تم تحويل $coin_link $a3ml لحسابك من خلال رابط تحويل ✅*
- من : [$OWNER](tg://user?id=$OWNER) 👤",
                'parse_mode' => 'Markdown',
            ]);
            bot('SendMessage', [
                'chat_id' => $OWNER,
                'text' => "*تم استعمال رابط التحويل الخاص بك ✅*
- من قبل : [$name](tg://user?id=$from_id) | `$from_id`

- الرابط : https://t.me/$USRBOT?by$get",
                'parse_mode' => 'Markdown',
                'disable_web_page_preview' => true,
            ]);
            $TOM->set('transsucces_'.$from_id,$TOM->get('transsucces_'.$from_id) + $coin_link);
            $TOM->set('coins_'.$from_id,$TOM->get('coins_'.$from_id) + $coin_link);
            $modes->delete('LINK_'.$get);
            $modes->delete('LINK_OWNER_'.$get);
        }
    }
}
    $text = '/start';
}


    

$status = $ALRDOS->get("replies_enabled") ?: "on";
if ($status == "on" && isset($text)) {
    $sensitivity = $ALRDOS->get("sensitivity") ?: "strict";
    $words = explode(",", $ALRDOS->get("reply_words") ?: "");

    foreach ($words as $word) {
        $reply = $ALRDOS->get("reply_$word");
        if (!$reply) continue;

        $isMatch = ($sensitivity == "strict" && $text === $word) ||
                   ($sensitivity == "loose" && strpos($text, $word) !== false);

        if ($isMatch) {
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => $reply
            ]);
            break;
        }
    }
}


$viewAzd_ = explode('viewAzd_' , $data)[1];
if($viewAzd_){
    $gg=$bot->get("zrs_info_content_" . retrieve_text($viewAzd_));
        $AL_MHTWA= str_replace(array('#name_user','#name' , '#id' , '#username' ) , array("[$name](tg://user?id=$from_id)" ,"$name" , "$from_id" , "[$username]" ) , $gg);
    
    bot('EditMessageText', [
        'parse_mode' => 'Markdown',
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "$AL_MHTWA",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "رجوع", "callback_data" => "BACK"]],
            ]
        ])
    ]);
}
if($text == "/start"){
    $count_services = $bot->get('ORDERS') ?? "0";
    $coins = $TOM->get('coins_'.$chat_id) ?? "0";
    $START = str_replace(['#COINS','#MY_ID'],[$coins,$from_id],$START);
      $ALASASE = $bot->get('zrar_alasase');
    $inline_keyboard = [];
    $a3ml = $bot->get("currency") ?: "نقطة";


    if ($ALASASE == '✅') {
      $inline_keyboard = [
        
    [["text" => "📦 الخدمات", "callback_data" => "SERVICES"]],
    [["text" => "$INLINE_x", "callback_data" => "TMOIL_x"]],
    [
        ["text" => "❇️ تجميع", "callback_data" => "plus_coin"],
        ["text" => "🔁 تحويل $a3ml", "callback_data" => "transfer_coin"]
    ],
    [
        ["text" => "   استخدام كود", "callback_data" => "use_code"],
        ["text" => "👤 الحساب", "callback_data" => "acount_me"]
    ],
    [
        ["text" => "📨 طلباتي", "callback_data" => "my_tlbs"],
        ["text" => "📬 معلومات الطلب", "callback_data" => "info_tlb"]
    ],
    [
        ["text" => "💸 شحن $a3ml", "callback_data" => "sh7n"],
        ["text" => "📊 الاحصائيات", "callback_data" => "stats"]
    ],
    [
        ["text" => "⁉️ شرح البوت", "callback_data" => "bot_help"],
        ["text" => "📝 الشروط", "callback_data" => "aggrement"]
    ],
    [["text" => "✅ عدد الطلبات : $count_services ✅", "callback_data" => "count_orders"]]
];

    }

    $lines_text = "";
    for ($i = 1; $i <= 20; $i++) {
        $gg = $bot->get("zrs_IN_LINE_$i");
        if ($gg) {
            $lines_text .= $gg . "[in_$i]\n";
        }
    }

    $lines = explode("\n", $lines_text);

    foreach ($lines as $line) {
        preg_match_all('/\[(.*?)\]/', $line, $matches);
        $row = [];

        foreach ($matches[1] as $btn_text) {
            $tt = store_text($btn_text);
            $GG = $bot->get("zrs_info_$btn_text");
            $THDATA = $bot->get("zrs_info_content_$btn_text");

            if ($GG == '【Link / رابط】') {
                $UU = 'url';
            } elseif ($GG == '【Text / محتوى نصي】') {
                $UU = 'callback_data';
                $THDATA = "viewAzd_" . getencode($btn_text);
            } elseif ($GG == '【Shortcut / زر مختصر】') {
                $UU = 'callback_data';
                $CODE = explode('BB:', $THDATA)[1];
                $THDATA = base64_decode(base64_decode(base64_decode($CODE)));
            } else {
                continue; 
            }

            $row[] = [
                "text" => "$btn_text",
                "$UU" => "$THDATA",
            ];
        }

        if (!empty($row)) {
            $inline_keyboard[] = $row;
        }
    }
    bot('SendMessage', [
        'chat_id' => $chat_id, 
        'text' => "$START",
        'parse_mode' => 'Markdown',
        'disable_web_page_preview' => true,
        'reply_markup' => json_encode(['inline_keyboard' => $inline_keyboard])
    ]);



    $modes->delete('mode_'.$from_id);
    foreach(explode("\n",$catche->get('ORDERS')) as $ORDER){
        $OWNER = $catche->get('ORDER_'.$ORDER);
        $MSG_ID = $catche->get('ORDER_MSG_ID_'.$ORDER);
        $INFOS = $catche->get('ORDER_INFO_'.$ORDER);
        $API = explode("|",$INFOS)[0];
        $DOMIN = explode("|" ,$INFOS)[1];
        $link = explode("|",$INFOS)[2];
        $NAME_XDMA = explode("|",$INFOS)[3];
        $timeLeft = date("Y-m-d H:i:s", explode("|",$INFOS)[4]);
        $G = json_decode(file_get_contents("https://$DOMIN/api/v2?key=$API&action=status&order=$ORDER"))->status;
        if($G == 'Completed'){
        bot('SendMessage', [
            'chat_id' => $OWNER,
            'reply_to_message_id' => $MSG_ID,
            'text' => "*✅ تم اكتمال طلبك بنجاح!*
*📺 اسم الخدمة*: $NAME_XDMA
*🔗 الرابط: *  [$link]
*⏱️ تاريخ الطلب:* $timeLeft
*🎉 شكراً لاستخدامك*",
'parse_mode' => 'Markdown',
        ]);
        $catche->delete('ORDER_'.$ORDER);
        $catche->delete('ORDER_MSG_ID_'.$ORDER);
        $catche->delete('ORDER_INFO_'.$ORDER);
        $catche->set('ORDERS', str_replace($ORDER , '',$catche->get('ORDERS')));
    }
    if($G == 'Canceled'){
        $irja3 = $catche->get('ORDER_PRICE_'.$ORDER);
        bot('SendMessage', [
            'chat_id' => $OWNER,
            'reply_to_message_id' => $MSG_ID,
            'text' => "*طلب ملغي ❌*
- تم ارجاع *$irja3* $a3ml لحسابك",
'parse_mode' => 'Markdown',
        ]);
        $TOM->set('coins_'.$chat_id , $TOM->get('coins_'.$chat_id) + $irja3);
        $catche->delete('ORDER_'.$ORDER);
        $catche->delete('ORDER_PRICE_'.$ORDER);
        $catche->delete('ORDER_MSG_ID_'.$ORDER);
        $catche->delete('ORDER_INFO_'.$ORDER);
        $catche->set('ORDERS', str_replace($ORDER , '',$catche->get('ORDERS')));
    }
    }
}

if($data == "bot_help"){
    bot('EditMessageText', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => "- البوت جدا سهل ولايحتاج لشرح ✅",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [["text" => "هيا لنجمع ال$a3ml", "callback_data" => "plus_coin"]],
                ]
            ])
        ]);
}
$a3ml_الاشتراك = $bot->get("JOINtmoil") ?? "5";
$سعر_تمويل = $bot->get("membertmoil") ?? "10";
function GET_RANDOM_CH($from_id) {
    global $TMOIL;

    $ids_raw = $TMOIL->get("IDXS");
    if (!$ids_raw) return false;

    $ids = explode("\n", trim($ids_raw));
    shuffle($ids);

    $checked_channels = [];

    foreach ($ids as $id) {
        $seen = $TMOIL->get("SEEN_$from_id") ?: [];
            if (!in_array($id, $seen)) {
        $INFOS = $TMOIL->get('INFOS_' . $id);
        if (!$INFOS) continue;

        $parts = explode('|', $INFOS);
        list($COUNT, $PRICE_TMOIL, $CHANNEL, $OWNER) = array_pad($parts, 4, 'N/A');

        if (in_array($CHANNEL, $checked_channels)) continue;
        $checked_channels[] = $CHANNEL;

        $member = TMOIL(API_KEY, 'getChatMember', [
            'chat_id' => $CHANNEL,
            'user_id' => $from_id
        ]);

        $data = json_decode(json_encode($member), true);
        if(CHECKIFADMIN($CHANNEL , API_KEY)){
        if (!$data['ok'] || in_array($data['result']['status'], ['left', 'kicked'])) {
            return $CHANNEL . "|" . $id;
        }
    }
    }
    }

    return false;
}


if (preg_match('/^CHKJOIN_(.*)/', $data, $match)) {
    $ID = $match[1];
    $INFOS = $TMOIL->get("INFOS_$ID");
    if ($INFOS) {
        list($COUNT, $PRICE_TMOIL, $CHANNEL, $OWNER) = explode('|', $INFOS);
        $member = TMOIL(API_KEY, 'getChatMember', [
            'chat_id' => $CHANNEL,
            'user_id' => $from_id
        ]);
        $dataM = json_decode(json_encode($member), true);

        if ($dataM['ok'] && !in_array($dataM['result']['status'], ['left', 'kicked'])) {
            $TMOIL->set("JOINED_{$ID}_$from_id", true);

            $seen = $TMOIL->get("SEEN_$from_id") ?: [];
            if (!in_array($ID, $seen)) {
                $seen[] = $ID;
                $TMOIL->set("SEEN_$from_id", $seen);
            }
            $INFOS = $TMOIL->get('INFOS_' . $ID);

    
            $parts = explode('|', $INFOS);
            list($COUNT, $PRICE_TMOIL, $CHANNEL, $OWNER) = array_pad($parts, 5, 'N/A');
    

            bot('answerCallbackQuery',[
                'callback_query_id' => $update->callback_query->id,
                'text' => "✅ تم اضافه $a3ml_الاشتراك $a3ml"
            ]);
            $TMOIL->set("NOW_PRGRESS_" . $ID ,$TMOIL->get("NOW_PRGRESS_" . $ID) + 1);
            bot('EditMessageReplyMarkup', [
                'chat_id' => $OWNER,
                'message_id' => $TMOIL->get("MID_$ID"),
                'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [["text" => $TMOIL->get("NOW_PRGRESS_" . $ID) ."/$COUNT", "callback_data" => "jgyugyj"]],
                ]
            ])
            ]);
            bot('editMessageReplyMarkup',[
                'chat_id' => $OWNER,
                'message_id'=>$TMOIL->get("MID_$ID"),
                'inline_message_id'=>$message_id->inline_query->inline_message_id,
                'reply_markup'=>json_encode([
                'inline_keyboard'=>[
                    [["text" => $TMOIL->get("NOW_PRGRESS_" . $ID) ."/$COUNT", "callback_data" => "jgyugyj"]],
                ]])
                ]);
                $Mtbqi = $COUNT - $TMOIL->get("NOW_PRGRESS_" . $ID) ?? 1; 
                bot('SendMessage', [
                'chat_id' => $OWNER,
                'reply_to_message_id' => $TMOIL->get("MID_$ID"),
                'text' => "*- اشترك شخص جديد بقناتك* [$CHANNEL] ➕
▫️  العدد المطلوب : *$COUNT عضو*
▫️العدد المتبقي : *$Mtbqi عضو*
▫️ رقم التمويل : `$ID`

*🟥 لاتقم بتنزيل* [@$USRBOT] *من الادمنيه لاستمرار التمويل*
",
                'parse_mode' => 'Markdown', 
            ]); 
                if($TMOIL->get("NOW_PRGRESS_" . $ID) >= $COUNT){
                    bot('SendMessage', [
                'chat_id' => $OWNER,
                'reply_to_message_id' => $TMOIL->get("MID_$ID"),
                'text' => "*تم انتهاء تمويل القناة [$CHANNEL]* 🟢

▫️  العدد المطلوب : *$COUNT عضو*
▫️ رقم التمويل : `$ID`
▫️ سعر التمويل : *$PRICE_TMOIL $a3ml*
",
                'parse_mode' => 'Markdown', 
            ]); 
            $ids_raw = $TMOIL->get("IDXS");
            $idx_now = str_replace("$ID" , "" , $ids_raw );
            $TMOIL->set("IDXS" , $idx_now );
            $TMOIL->delete('INFOS_' . $ID);
            $TMOIL->delete("NOW_PRGRESS_" . $ID);
                }
            $data = "JOIN_CHANNNELS";
            
            $TOM->set('coins_'.$from_id,$TOM->get('coins_'.$from_id) + $a3ml_الاشتراك);
        } else {
            bot('answerCallbackQuery',[
                'callback_query_id' => $update->callback_query->id,
                'text' => "❌ لم يتم العثور على اشتراكك، تأكد من انك مشترك"
            ]);
        }
    }

} elseif (preg_match('/^SKIPCH_(.*)/', $data, $match)) {
    $ID = $match[1];
    
    $seen = $TMOIL->get("SEEN_$from_id") ?: [];
    if (!in_array($ID, $seen)) {
        $seen[] = $ID;
        $TMOIL->set("SEEN_$from_id", $seen);
    }

    bot('answerCallbackQuery',[
        'callback_query_id' => $update->callback_query->id,
        'text' => "⏩ تم تخطي القناة"
    ]);

    $GET_CH = GET_RANDOM_CH($from_id);
    if ($GET_CH) {
        $CH = explode("|", $GET_CH);
        $CHAN = $CH[0];
        $ID = $CH[1];
        bot('EditMessageText', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => "*أشترك بالقناة $CHAN ✅*",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [["text" => "تحقق ✅", "callback_data" => "CHKJOIN_$ID"]],
                    [["text" => "تخطي ⏩", "callback_data" => "SKIPCH_$ID"], ["text" => "ابلاغ ⛔️", "callback_data" => "REPORT_$ID"]],
                    [["text" => "🔙 رجوع", "callback_data" => "plus_coin"]],
                ]
            ])
        ]);
    } else {
        $data = "JOIN_CHANNNELS";
    }

} elseif (preg_match('/^REPORT_(.*)/', $data, $match)) {
    $ID = $match[1];
    
    $reports = $TMOIL->get("REPORTS_$ID") ?: [];
    if (!in_array($from_id, $reports)) {
        $reports[] = $from_id;
        $TMOIL->set("REPORTS_$ID", $reports);
    }

    $seen = $TMOIL->get("SEEN_$from_id") ?: [];
    if (!in_array($ID, $seen)) {
        $seen[] = $ID;
        $TMOIL->set("SEEN_$from_id", $seen);
    }
      $INFOS = $TMOIL->get('INFOS_' . $ID);

    
            $parts = explode('|', $INFOS);
            list($COUNT, $PRICE_TMOIL, $CHANNEL, $OWNER) = array_pad($parts, 5, 'N/A');
            $Mtbqi =$TMOIL->get("NOW_PRGRESS_" . $ID) ?? 1; 
    $ff=$users->get($OWNER);
    $textd = "🔴 *بلاغ حالة تمويل*\n\n".
        "▫️*المصدر:* قنوات التمويل\n".
        "▫️*بيانات المستخدم:*\n".
        "- الاسم: *$name*\n".
        "- المعرّف الرقمي: `$from_id`\n".
        "- المعرف: [@$user]\n\n".
        "▫️*القناة المموّلة:* [$CHANNEL]\n".
        "▫️*عدد الأعضاء المرسَلين:* *$Mtbqi* عضوًا\n".
        "▫️*تم التمويل من قبل:* [$get](tg://user?id=$OWNER)";

    bot('answerCallbackQuery',[
        'callback_query_id' => $update->callback_query->id,
        'text' => "⛔️ تم إرسال البلاغ، شكراً لك"
    ]);
    bot('SendMessage', [
                'chat_id' => $OWNER,
                'text' => "$textd
",

                'parse_mode' => 'Markdown', 
                 'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [["text" => "حظر القناة من التمويل", "callback_data" => "BLOCKTMOIL_$ID"]],
                        [["text" => "الغاء التمويل", "callback_data" => "CANCELTMOIL_$ID"]],
                    ]
                ])
            ]); 

    $GET_CH = GET_RANDOM_CH($from_id);
    if ($GET_CH) {
        $CH = explode("|", $GET_CH);
        $CHAN = $CH[0];
        $ID = $CH[1];
        bot('EditMessageText', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => "*أشترك بالقناة $CHAN ✅*",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [["text" => "تحقق ✅", "callback_data" => "CHKJOIN_$ID"]],
                    [["text" => "تخطي ⏩", "callback_data" => "SKIPCH_$ID"], ["text" => "ابلاغ ⛔️", "callback_data" => "REPORT_$ID"]],
                    [["text" => "🔙 رجوع", "callback_data" => "plus_coin"]],
                ]
            ])
        ]);
    } else {
        $data = "JOIN_CHANNNELS";
    }
}



if($data == "JOIN_CHANNNELS" or $text == "/easy_get_channnnels"){
    $GET_CH = GET_RANDOM_CH($from_id);
    if($GET_CH){
        $CH = explode("|" , $GET_CH);
        $CHAN = $CH[0];
        $ID = $CH[1];
        if($data){
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*أشترك بالقناة $CHAN ✅*
",

        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "تحقق ✅", "callback_data" => "CHKJOIN_$ID"]],
                [["text" => "تخطي ⏩", "callback_data" => "SKIPCH_$ID"],["text" => "ابلاغ ⛔️", "callback_data" => "REPORT_$ID"]],
                [["text" => "🔙 رجوع", "callback_data" => "plus_coin"]],
            ]
        ])
    ]);
}else{
    bot('SendMessage', [
        'chat_id' => $chat_id,
        'text' => "$CHAN",
        'reply_markup' => json_encode([
        'inline_keyboard' => [
            [["text" => "تحديث", "callback_data" => "upadte_easy"]],
        ]
    ])
    ]); 
    $modes->set("UPDATEOR_$from_id" , $ID);
}
}else{
    if($data){
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*🔴 لا توجد قنوات حالياً، حاول لاحقاً.*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "plus_coin"]],
            ]
        ])
    ]);
}else{
    bot('SendMessage', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*🔴 لا توجد قنوات حالياً، حاول لاحقاً.*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "تحديث", "callback_data" => "upadte_easy"]],
            ]
        ])
    ]);

}
}
}

if($data == "upadte_easy"){
    bot('answerCallbackQuery', [
        'callback_query_id' => $update->callback_query->id,
    ]);
    
    $OLD_CH = $modes->get("UPDATEOR_$from_id");
    $INFOS = $TMOIL->get("INFOS_$OLD_CH");
    if ($INFOS) {
        list($COUNT, $PRICE_TMOIL, $CHANNEL, $OWNER) = explode('|', $INFOS);
    }
    $member = TMOIL(API_KEY, 'getChatMember', [
        'chat_id' => $CHANNEL,
        'user_id' => $from_id
    ]);
    $dataM = json_decode(json_encode($member), true);

    if ($dataM['ok'] && !in_array($dataM['result']['status'], ['left', 'kicked'])) {
        $modes->delete("UPDATEOR_$from_id");
        $TMOIL->set("NOW_PRGRESS_" . $OLD_CH ,$TMOIL->get("NOW_PRGRESS_" . $OLD_CH) + 1);
        bot('EditMessageReplyMarkup', [
            'chat_id' => $OWNER,
            'message_id' => $TMOIL->get("MID_$OLD_CH"),
            'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => $TMOIL->get("NOW_PRGRESS_" . $OLD_CH) ."/$COUNT", "callback_data" => "jgyugyj"]],
            ]
        ])
        ]);
        bot('editMessageReplyMarkup',[
            'chat_id' => $OWNER,
            'message_id'=>$TMOIL->get("MID_$ID"),
            'inline_message_id'=>$message_id->inline_query->inline_message_id,
            'reply_markup'=>json_encode([
            'inline_keyboard'=>[
                [["text" => $TMOIL->get("NOW_PRGRESS_" . $OLD_CH) ."/$COUNT", "callback_data" => "jgyugyj"]],
            ]])
            ]);
            if($TMOIL->get("NOW_PRGRESS_" . $OLD_CH) >= $COUNT){
                bot('SendMessage', [
            'chat_id' => $OWNER,
            'reply_to_message_id' => $TMOIL->get("MID_$OLD_CH"),
            'text' => "*تم انتهاء تمويل القناة $CHANNEL* 🟢

▫️  العدد المطلوب : *$COUNT عضو*
▫️ رقم التمويل : `$OLD_CH`
▫️ سعر التمويل : *$PRICE_TMOIL $a3ml*
",
            'parse_mode' => 'Markdown', 
        ]); 
        $ids_raw = $TMOIL->get("IDXS");
        $idx_now = str_replace("$OLD_CH" , "" , $ids_raw );
        $TMOIL->set("IDXS" , $idx_now );
        $TMOIL->delete('INFOS_' . $OLD_CH);
        $TMOIL->delete("NOW_PRGRESS_" . $OLD_CH);
        $TMOIL->delete('TMOIL_FOR_'. $CHANNEL);
            }
    }
    $GET_CH = GET_RANDOM_CH($from_id);
    if($GET_CH){
    $CH = explode("|" , $GET_CH);
    $CHAN = $CH[0];
    $ID = $CH[1];
bot('EditMessageText', [
    'chat_id' => $chat_id, 
    'message_id' => $message_id,
    'text' => "$CHAN
",
'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "تحديث", "callback_data" => "upadte_easy"]],
            ]
        ])
]);
$modes->set("UPDATEOR_$from_id" , $ID);
    }else{
        bot('EditMessageText', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => "*🔴 لا توجد قنوات حالياً، حاول لاحقاً.*",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [["text" => "تحديث", "callback_data" => "upadte_easy"]],
                ]
            ])
        ]);
    }
}
if ($data == 'TMOILOS') {
    $S_LIST = ['inline_keyboard' => []];
    $النص = "*🌟 جميع القنوات أو المجموعات التي تقوم بتمويلها حالياً:*
تظهر لك في هذه الصفحة كافة القنوات والمجموعات التي قمت بتمويلها، وتستطيع متابعتها بسهولة.
";

    $ids_raw = $TMOIL->get("IDXS_$from_id");


    $ids = explode("\n", trim($ids_raw));
    shuffle($ids);

    $checked_channels = [];
    $OK = 0;

    foreach ($ids as $id) {
        $INFOS = $TMOIL->get('INFOS_' . $id);
        if (!$INFOS) continue;

        $parts = explode('|', $INFOS);
        $NOWMEM = $TMOIL->get("NOW_PRGRESS_" . $id) ?? 0;
        list($COUNT, $PRICE_TMOIL, $CHANNEL, $OWNER) = array_pad($parts, 4, 'N/A');
        $S_LIST['inline_keyboard'][] = [
            ['text' => "$CHANNEL", 'callback_data' => "STATUS_$id"],
            ['text' => "$NOWMEM/$COUNT", 'callback_data' => "STATUS_$id"]
        ];
        $OK = 1;
    }

    if (!$OK) {
        $Sok = "🔻 لايوجد قنوات تحت التمويل حاليا"; 
    } else {
        $Sok = "🔄 تحديث القائمه";
    }

    $S_LIST['inline_keyboard'][] = [['text' => "$Sok", 'callback_data' => "TMOILOS"]];
    $S_LIST['inline_keyboard'][] = [['text' => "🔙 رجوع", 'callback_data' => "BACK"]];

    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "$النص",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode($S_LIST)
    ]);
}



if($data == 'TMOIL_x'){
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*💫 تمويل قناتك؟ بسهولة.*
زيد أعضاء قناتك باستخدام $a3mlك،
بخطوات بسيطة ونتائج سريعة 🌿

كل عضو = *$سعر_تمويل $a3ml*✨
ابدأ التمويل الآن ودع البوت يهتم بالباقي 💖

",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "بدء تمويل جديد", "callback_data" => "MAKE_TMOIL"]],
                [["text" => "تمويلاتي", "callback_data" => "TMOILOS"]],
                [["text" => "🔙 رجوع", "callback_data" => "BACK"]],
            ]
        ])
    ]);
    $modes->delete('mode_'.$from_id);
}

if($data == "MAKE_TMOIL"){
    $سعر_الف = $سعر_تمويل * 1000;
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*◼️ أرسل عدد الاعضاء المراد طلبهم *
▫️ سعر كل 1 = *$سعر_تمويل $a3ml*
🔳 سعر كل 1000 = *$سعر_الف $a3ml*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "TMOIL_x"]],
            ]
        ])
    ]);
    $modes->set('mode_'.$from_id , $data);
}

if($text and $modes->get('mode_'.$from_id) == "MAKE_TMOIL"){
    if(is_numeric($text)){
        $userbot = json_decode(file_get_contents("https://api.telegram.org/bot" . API_KEY ."/getme"))->result->username;
        $PRICE_ME = $text * $سعر_تمويل;
        $coins = $TOM->get('coins_'.$chat_id);
        if($coins >= $PRICE_ME){
            bot('SendMessage', [
                'chat_id' => $chat_id,
                'text' => "*حسنا ستطلب $text 🟢 (بقيمه $PRICE_ME $a3ml)*

*🔵 ارفع البوت [@$userbot] بالقناة *
🟡 ثم أرسل معرف القناة",
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [["text" => "🔙 رجوع", "callback_data" => "TMOIL_x"]],
                ]
            ])
            ]); 
            $modes->set('mode_'.$from_id , "NEED_CHANNEL");
            $modes->set('helper_'.$from_id , "$text");
            return;
        }else{
            bot('SendMessage', [
                'chat_id' => $chat_id,
                'text' => "*$a3mlك لاتكفي 🔴*

سعر هذا التمويل يساوي : *$PRICE_ME $a3ml*",
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [["text" => "🔙 رجوع", "callback_data" => "TMOIL_x"]],
                ]
            ])
            ]); 
        }
    }
}

function CHECKIFADMIN($text, $token = API_KEY) {
    global $bot_id;
    $channel_info = TMOIL($token, 'getChat', ['chat_id' => $text]);
    $channel_data = json_decode(json_encode($channel_info), true);

    if ($channel_data['ok']) {
        $member_info = TMOIL($token, 'getChatMember', [
            'chat_id' => $text,
            'user_id' => $bot_id
        ]);
        $member_data = json_decode(json_encode($member_info), true);

        if ($member_data['ok'] && in_array($member_data['result']['status'], ['administrator', 'creator'])) {
            return true;
        }
    }

    return false;
}

function generatePrettyNumbers($count = 10) {
    global $IDS;
    $numbers = [];
    $attempts = 0;
    $maxAttempts = $count * 50; // لتجنب الدخول في حلقة لا نهائية

    while (count($numbers) < $count && $attempts < $maxAttempts) {
        $num = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        if (isPretty($num) && !in_array($num, $numbers)) {
            $numbers[] = $num;
        }
        $attempts++;
    }

    return $numbers;
}


function isPretty($num) {
    return (
        preg_match('/^(.)\1{5}$/', $num) ||                   
        preg_match('/^(\d)\1{2}(\d)\2{2}$/', $num) ||          
        preg_match('/^(\d)(\d)\1\2\1\2$/', $num) ||               
        preg_match('/^123456|654321|112233|223344$/', $num) ||  
        preg_match('/^(\d)(\d)(\d)\3\2\1$/', $num)               
    );
}

if ($text && $modes->get('mode_' . $from_id) == "NEED_CHANNEL") {
    if (preg_match('/@/', $text)) {
        if (CHECKIFADMIN($text)) {
            if(!$TMOIL->get('TMOIL_FORي_'. $text)){
                $LAST = $TMOIL->get("lastid_tmoil_" , $from_id);
                $TMOIL->delete("INFOS_" , $LAST);
            $prettyNumbers = generatePrettyNumbers(1);
            $IDX = $prettyNumbers[0]; 

            $COUNT = $modes->get('helper_' . $from_id);
            $PRICE_TMOIL = $COUNT * $سعر_تمويل;

            bot('SendMessage', [
                'chat_id' => $chat_id,
                'text' => "*🔹 معلومات قبل انشاء تمويلك*

🔸 ستطلب : *$COUNT عضو*
🔸 سعر التمويل : *$PRICE_TMOIL $a3ml*
🔸 ستطلب الى : [$text]
🔸 رقم التمويل : `$IDX`",
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [["text" => "انشاء التمويل ✅", "callback_data" => "MAKKER_TMOIL_$IDX"]],
                        [["text" => "الغاء التمويل ❌", "callback_data" => "cancel_tmoil_$IDX"]],
                    ]
                ])
            ]);
            $TMOIL->set('lastid_tmoil_' . $chat_id, "$IDX");
            $TMOIL->set('INFOS_' . $IDX, "$COUNT|$PRICE_TMOIL|$text|$chat_id");
            $modes->delete('mode_' . $from_id);
        }else{
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'parse_mode' => 'Markdown',
                'text' => "*عذرا ولكن القناة تحت التمويل بالفعل ✅*
♻️ انتضر لحين *اكتمال التمويل* وحاول مجددا .",
'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [["text" => "رجوع ❌", "callback_data" => "MAKE_TMOIL"]],
                    ]
                ])
            ]);
            $modes->delete('mode_' . $from_id);
        }
        } else {
            bot('sendMessage', [
                'chat_id' => $chat_id,
                'text' => "❗️ تأكد أن البوت مشرف في القناة قبل المتابعة.",
            ]);
        }
    } else {
        bot('sendMessage', [
            'chat_id' => $chat_id,
            'text' => "❗️ يرجى إرسال معرف القناة يبدأ بـ @ .",
        ]);
    }
}


$MAKKER_TMOIL_= explode("MAKKER_TMOIL_" , $data)[1];
if($MAKKER_TMOIL_){
    $INFOS = $TMOIL->get('INFOS_' . $MAKKER_TMOIL_);
    $S_TEXT = explode('|', $INFOS);
    list($COUNT , $PRICE_TMOIL , $CHANNEL , $OWNER) = array_pad($S_TEXT, 3, 'N/A');
    $coins = $TOM->get('coins_'.$chat_id);
    if($coins >= $PRICE_TMOIL){
        bot('answerCallbackQuery', [
            'callback_query_id' => $update->callback_query->id,
            'text' => "تم أنشاء طلب تمويل جديد ✅",
            'show_alert' => true,
        ]);
        $coinsor = $TOM->get('coins_'.$chat_id) ?? "0";
    $coinsleft = $TOM->get('coinsuseed_'.$from_id) ?? "0";
    $hdaiacount = $TOM->get('hdiacoins_'.$from_id) ?? "0";
    $hdiacountx =$TOM->get('hdiax_'.$from_id) ?? "0";
    $transers = $TOM->get('transcoins_'.$from_id) ?? "0";
    $i_trans = $TOM->get('transsucces_'.$from_id)  ?? "0";
    $invits_count = $TOM->get('countshare_'.$from_id) ?? "0";
    $coinsmeshare = $TOM->get('coinsshare_'.$from_id) ?? "0";
    $NOW_NQAT = $coinsor - $PRICE_TMOIL;
    $ish3ar_tmoil = $bot->get('shi3ar_tmoil') ?? '✅';
    if($ish3ar_tmoil == '✅'){
        bot('SendMessage', [
            'chat_id' => $ADMIN, 
            'text' => "*تم بدء تمويل قناة ببوتك ✅*

♻️ التمويل الى : [$CHANNEL]
♻️ عدد التمويل : *$COUNT عضو*
♻️ سعر التمويل : *$PRICE_TMOIL $a3ml*
♻️ رقم التمويل : `$MAKKER_TMOIL_`

*👤 معلومات الشخص:*
• *الاسم:* [$name](tg://user?id=$from_id)
• *الآيدي:* `$from_id`
• *المعرف:* [@$user]
• *عدد ال$a3ml:* $coinsor
• *ال$a3ml المستعملة:* $coinsleft
• *$a3ml الهدايا:* $hdaiacount
• *عدد الدعوات:* $invits_count
• *ال$a3ml من رابط المشاركة:* $coinsmeshare

• *اصبحت ".$a3ml."ه :* $NOW_NQAT",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [["text" => "حظر القناة من التمويل", "callback_data" => "BLOCKTMOIL_$MAKKER_TMOIL_"]],
                        [["text" => "الغاء التمويل", "callback_data" => "CANCELTMOIL_$MAKKER_TMOIL_"]],
                    ]
                ])
        ]);
    }
        bot('EditMessageText', [
            'chat_id' => $chat_id, 
            'message_id' => $message_id,
            'text' => "*🟢 تم أنشاء طلب تمويل جديد *

🔘 التمويل الى : [$CHANNEL]
🔘 عدد التمويل : *$COUNT عضو*
🔘 سعر التمويل : *$PRICE_TMOIL $a3ml*
🔘 رقم التمويل : `$MAKKER_TMOIL_`",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [["text" => "0/$COUNT", "callback_data" => "STATUS_$MAKKER_TMOIL_"]],
                    ]
                ])
        ]);
        
        $TOM->set('coins_'.$from_id,$TOM->get('coins_'.$from_id) - $PRICE_TMOIL);
        $TMOIL->set("MID_$MAKKER_TMOIL_" , $message_id);
        $TMOIL->set('TMOIL_FOR_'. $CHANNEL , true);
        $TMOIL->set("IDXS" , $TMOIL->get("IDXS") . "\n" . $MAKKER_TMOIL_);
        $TMOIL->set("IDXS_$from_id" , $TMOIL->get("IDXS_$from_id") . "\n" . $MAKKER_TMOIL_);
        $modes->delete('mode_' . $from_id);
    }else{
        bot('EditMessageText', [
            'chat_id' => $chat_id, 
            'message_id' => $message_id,
            'text' => "*🔴 عذرا عزيزي $a3mlك لاتكفي لأنشاء التمويل*

🟢 سعر هذا التمويل : *$PRICE_TMOIL $a3ml*",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [["text" => "الغاء 🔴", "callback_data" => "cancel_tmoil_$MAKKER_TMOIL_"]],
                ]
            ])
        ]);
    }
}

$BLOCKTMOIL_ = explode("BLOCKTMOIL_" , $data)[1];
if($BLOCKTMOIL_){
    $data = "CANCELTMOIL_". $BLOCKTMOIL_; 
    $OKL = true;
}
$CANCELTMOIL_ = explode("CANCELTMOIL_" , $data)[1];
if($CANCELTMOIL_){
        $INFOS = $TMOIL->get('INFOS_' . $CANCELTMOIL_);
        $S_TEXT = explode('|', $INFOS);
        list($COUNT , $PRICE_TMOIL , $CHANNEL , $OWNER) = array_pad($S_TEXT, 3, 'N/A');
        $MID = $TMOIL->get("MID_$CANCELTMOIL_");
        $SVT = str_replace($CANCELTMOIL_ , '' , $TMOIL->get("IDXS"));
        $TMOIL->set("IDXS" , $SVT);
        $CVT = str_replace($CANCELTMOIL_ , '' , $TMOIL->get("IDXS_$from_id"));
        $TMOIL->set("IDXS_$from_id" , $CVT);
        $TMOIL->delete('INFOS_' . $CANCELTMOIL_);
        bot('editMessageReplyMarkup',[
            'chat_id' => $OWNER,
            'message_id'=>$MID,
            'inline_message_id'=>$message_id->inline_query->inline_message_id,
            'reply_markup'=>json_encode([
            'inline_keyboard'=>[
                [["text" => "طلب تمويلك ملغي من قبل الأدارة", "url" => "https://t.me/" . str_replace('@','',$CHANNEL)]],
                [["text" => "حساب الأدارة ✅", "url" => "tg://user?id=$ADMIN"]],
            ]])
            ]);
            bot('editMessageReplyMarkup',[
            'chat_id' => $chat_id,
            'message_id'=>$message_id,
            'inline_message_id'=>$message_id->inline_query->inline_message_id,
            'reply_markup'=>json_encode([
            'inline_keyboard'=>[
                [["text" => "تم ازاله القناة من التمويل", "url" => "https://t.me/" . str_replace('@','',$CHANNEL)]],
            ]])
            ]);
            if(!$OKL){
        bot('answerCallbackQuery', [
        'callback_query_id' => $update->callback_query->id,
        'text' => "تم الغاء تمويل قناة $CHANNEL ✅",
        'show_alert' => true,
    ]);
}else{
    bot('answerCallbackQuery', [
        'callback_query_id' => $update->callback_query->id,
        'text' => "تم الغاء + حظر تمويل قناة $CHANNEL ✅",
        'show_alert' => true,
    ]);
}
    $TMOIL->delete("MID_$CANCELTMOIL_");
}
if($data == 'my_tlbs') {
    bot('answerCallbackQuery', [
        'callback_query_id' => $update->callback_query->id,
        'text' => "تم دخول قسم طلباتي ✅",
        'show_alert' => true,
    ]);

    $MY_ORDERS = $TOM->get('MYORDERSTEXT_'.$from_id);
    $T = explode("\n", $MY_ORDERS);
    $K = '';
    $g = 0;

    foreach($T as $ORDERS) {
        if($ORDERS) {
            $g++;
            $K .= "\n\n$ORDERS";
        }
    }

    $responseText = $g ? $K : "لايوجد لديك طلبات داخل البوت ❎";

    bot('SendMessage', [
        'chat_id' => $chat_id,
        'reply_to_message_id' => $MSG_ID,
        'text' => $responseText,
    ]);

    $data = 'BACK';
}



if($data == "cancel"){
    bot('answerCallbackQuery',[
        'callback_query_id' => $update->callback_query->id,
        'text' => "تم الغاء الطلب ❎",
        'show_alert' => true,
    ]);
    $modes->delete('mode_'.$from_id);
    $modes->delete('xdma_'.$from_id);
    $modes->delete('count_'.$from_id);
    $modes->delete('link_'.$from_id);
    $data = 'BACK';
}

$cancel_tmoil_ = explode("cancel_tmoil_" , $data)[1];
if($cancel_tmoil_){
    bot('answerCallbackQuery',[
        'callback_query_id' => $update->callback_query->id,
        'text' => "تم الغاء طلب التمويل ❎",
        'show_alert' => true,
    ]);
    $modes->delete('mode_'.$from_id);
    $TMOIL->delete('INFOS_' . $cancel_tmoil_);
    $data = 'BACK';
}
            

if($data == "count_orders"){
    $count_services = $bot->get('ORDERS') ?? "0";
    bot('answerCallbackQuery',[
        'callback_query_id' => $update->callback_query->id,
        'text' => "عدد الطلبات المكتمله : $count_services ✅",
        
    ]);
    $data = 'BACK';
}

if($data == 'BACK'){
    
  $count_services = $bot->get('ORDERS') ?? "0";
    $coins = $TOM->get('coins_'.$chat_id) ?? "0";
    $START = str_replace(['#COINS','#MY_ID'],[$coins,$from_id],$START);
      $ALASASE = $bot->get('zrar_alasase');
    $inline_keyboard = [];
    $a3ml = $bot->get("currency") ?: "نقطة";


    if ($ALASASE == '✅') {
      $inline_keyboard = [
    [["text" => "📦 الخدمات", "callback_data" => "SERVICES"]],
    [["text" => "$INLINE_x", "callback_data" => "TMOIL_x"]],
    [
        ["text" => "❇️ تجميع", "callback_data" => "plus_coin"],
        ["text" => "🔁 تحويل $a3ml", "callback_data" => "transfer_coin"]
    ],
    [
        ["text" => "💳 استخدام كود", "callback_data" => "use_code"],
        ["text" => "👤 الحساب", "callback_data" => "acount_me"]
    ],
    [
        ["text" => "📨 طلباتي", "callback_data" => "my_tlbs"],
        ["text" => "📬 معلومات الطلب", "callback_data" => "info_tlb"]
    ],
    [
        ["text" => "💸 شحن $a3ml", "callback_data" => "sh7n"],
        ["text" => "📊 الاحصائيات", "callback_data" => "stats"]
    ],
    [
        ["text" => "⁉️ شرح البوت", "callback_data" => "bot_help"],
        ["text" => "📝 الشروط", "callback_data" => "aggrement"]
    ],
    [["text" => "✅ عدد الطلبات : $count_services ✅", "callback_data" => "count_orders"]]
];

    }

    $lines_text = "";
    for ($i = 1; $i <= 20; $i++) {
        $gg = $bot->get("zrs_IN_LINE_$i");
        if ($gg) {
            $lines_text .= $gg . "[in_$i]\n";
        }
    }

    $lines = explode("\n", $lines_text);

    foreach ($lines as $line) {
        preg_match_all('/\[(.*?)\]/', $line, $matches);
        $row = [];

        foreach ($matches[1] as $btn_text) {
            $tt = store_text($btn_text);
            $GG = $bot->get("zrs_info_$btn_text");
            $THDATA = $bot->get("zrs_info_content_$btn_text");

            if ($GG == '【Link / رابط】') {
                $UU = 'url';
            } elseif ($GG == '【Text / محتوى نصي】') {
                $UU = 'callback_data';
                $THDATA = "viewAzd_" . getencode($btn_text);
            } elseif ($GG == '【Shortcut / زر مختصر】') {
                $UU = 'callback_data';
                $CODE = explode('BB:', $THDATA)[1];
                $THDATA = base64_decode(base64_decode(base64_decode($CODE)));
            } else {
                continue; 
            }

            $row[] = [
                "text" => "$btn_text",
                "$UU" => "$THDATA",
            ];
        }

        if (!empty($row)) {
            $inline_keyboard[] = $row;
        }
    }
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "$START",
        'parse_mode' => 'Markdown',
        'disable_web_page_preview' => true,
        'reply_markup' => json_encode(['inline_keyboard' => $inline_keyboard])
    ]);
    
    $modes->delete('mode_'.$from_id);
}
if ($stats_info->get('day') != date('d') ) {
    $stats_info->set('day', date('d'));
    $stats_info->set('activers_today', 1);
} else {
    if(!$catche->get('IN_ACTIVE_' . $from_id .'_'.date('d'))){
    $stats_info->set('activers_today', $stats_info->get('activers_today') + 1);
    $catche->set('IN_ACTIVE_' . $from_id .'_'.date('d'),1);
    $stats_info->set('activers_MONTH', $stats_info->get('activers_MONTH') + 1);
    }
}


if ($data == "stats") {
    $count_services = $bot->get('ORDERS') ?? "0";
    $ACTIVER_TODAY = $stats_info->get('activers_today') ?? "0";
    $ACTIVER_MONTH = $stats_info->get('activers_MONTH') ?? "0";
    $MEMS = count(explode("\n",$users->get('mems')));
    $MEMS = $MEMS +$FAKEOS; 
    
    $CHSx = count(array_filter(explode("\n", $TMOIL->get("IDXS")), fn($line) => trim($line) !== ''));

$topRefs = $shares->get('top_refs') ?? [];
arsort($topRefs);
$top10 = array_slice($topRefs, 0, 5, true);
$medals = ["🥇", "🥈", "🥉"]; 

$rank = 0;
foreach ($top10 as $id => $count) {
    if(is_numeric($id)){
    $emoji = $medals[$rank] ?? "🎖️"; 
    $H = $H ."$count) [$id](tg://user?id=$id) $emoji\n";
    $rank++;
    }
}

    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "📊] الأحصائيات

👥] مستخدمين البوت : $MEMS 👤
🗣] مستخدمين نشطين الان : $ACTIVER_TODAY 🟢
⭐️] مستخدمين نشطين اليوم : $ACTIVER_TODAY ⚡️
❄️] مستخدمين نشطين هذا الشهر : $ACTIVER_MONTH ☄️

🟢] طلبات انجزناها : $count_services ✅
📣] قنوات قيد التمويل : $CHSx ⏳

🌀] الاعلى في الدعوات : 
    
$H",
        'parse_mode' => 'Markdown',
        'disable_web_page_preview' => true,
        'reply_markup' => json_encode([
            'inline_keyboard' => [
[["text" => "🔙 رجوع", "callback_data" => "BACK"]],
            ]
        ])
    ]);
}


if($data == 'info_tlb'){
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*أرسل ايدي الطلب من فضلك 🔣*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "BACK"]],
            ]
        ])
    ]);
    $modes->set('mode_'.$from_id,$data);
}

if($text and $modes->get('mode_'.$from_id) == 'info_tlb'){
    $get_order = $orders_info->get($text);
    $S_TEXT = explode('|', $get_order);

    list($API , $DOMIN, $xdma ,$TO, $count, $price,$owner) = array_pad($S_TEXT, 12, 'N/A');
    if($DOMIN && $API){
        if($owner == $from_id){
        $G = json_decode(file_get_contents("https://$DOMIN/api/v2?key=$API&action=status&order=$text"))->status;
        if($G){
            bot('SendMessage', [
                'chat_id' => $chat_id,
                'text' => "*معلومات الطلب* `$text` ✅
*• اسم الخدمه :* $xdma 🔤
*• الحاله :* $G ✳️
*• السعر :* $price $a3ml 💰
*• الكميه :* $count ⛓️

*• تم الطلب الى :* [$TO] 💡
",
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [["text" => "🔙 رجوع", "callback_data" => "BACK"]],
                ]
            ])
            ]); 
            $modes->delete('mode_'.$from_id);
        }else{
            bot('SendMessage', [
            'chat_id' => $chat_id,
            'text' => "*ايدي طلب غير صالح ❌*",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "BACK"]],
            ]
        ])
        ]); 
        }
        } else{
        bot('SendMessage', [
            'chat_id' => $chat_id,
            'text' => "*لم يتم العثور على هذا الطلب بداخل طلباتك ❎*",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "BACK"]],
            ]
        ])
        ]);  
        }
    }else{
        bot('SendMessage', [
            'chat_id' => $chat_id,
            'text' => "*ايدي طلب غير صالح ❌*",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "BACK"]],
            ]
        ])
        ]); 
    }
}
function rand_text(){
    $abc = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","1","2","3","4","5","6","7","8","9","0");
    $fol = '#'.$abc[rand(5,36)].$abc[rand(5,36)].$abc[rand(5,36)].$abc[rand(5,36)].$abc[rand(5,36)].$abc[rand(5,36)].$abc[rand(5,36)].$abc[rand(5,36)].$abc[rand(5,36)].$abc[rand(5,36)];
    return $fol;
}
function Invoice($amount ,$amounter ) {
    global $name_bot , $a3ml;
    $data = [
        'title' => "عملية شحن $amounter $a3ml",
        'description' => "معلومات الدفع:",
        'payload' => rand_text(),
        'provider_token' => '', 
        'currency' => 'XTR',
        'prices' => json_encode([['amount' => $amount, 'label' => '1']]),
    ];

    $response = bot('createInvoiceLink', $data);

    return $response->result;
}


    if($data == 'sh7n'){
        $payed_text = $bot->get('payed') ?? "لا يوجد";
        $agents = $bot->get("agents") ?? [];
        $buttons = [];
        foreach ($agents as $agent) {
            if(preg_match('/https/',$agent["link"])){
            $buttons[] = [["text" => $agent["name"], "url" => $agent["link"]],
            ];
        }
        }
        if($bot->get('AL_NJOM_x') == '✅'){
            $buttons[] = [["text" => "الشحن التلقائي عبر النجوم {🌟}", "callback_data" => "KM_TRID_AN_TSH7n"]];
        }
        $buttons[] = [["text" => "🔙 رجوع", "callback_data" => "BACK"]];
    bot('EditMessageText', [
            'chat_id' => $chat_id, 
            'message_id' => $message_id,
            'text' => "$payed_text",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode(['inline_keyboard' => $buttons])
        ]);
    }
    
    if($data == "KM_TRID_AN_TSH7n"){
        bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "- كم $a3ml تريد أن تشحن ؟ :",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "sh7n"]],
            ]
        ])
    ]);
    $modes->set('mode_'.$from_id,'MAKE_SH7n');
    }
if ($update->message) {
    if($text and $modes->get('mode_' . $from_id) === 'MAKE_SH7n'){
        $NOW_s3r = $bot->get("s3r_njom") ?? "1";
        $pricePerThousand = $NOW_s3r; 
    $value = ($text / 1000) * $pricePerThousand;
        $amount = intval($value);
        $T = Invoice($amount,$text );
        bot('SendMessage', [
        'chat_id' => $chat_id, 
        'text' => "- لأكمال شحن $text $a3ml بـ$amount نجمه عبر الرابط اسفل ,",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "أكمل الدفع", "url" => "$T"]],
            ]
        ])
    ]);
    $modes->delete('mode_'.$from_id,);
    }


if (isset($update->message->successful_payment)) {
    $STARs = $update->message->successful_payment->total_amount;
    $NOW_s3r = $bot->get("s3r_njom") ?? "1"; 
$pricePerThousand = $NOW_s3r;

$amount = floatval($STARs); 
$points = intval(($amount / $pricePerThousand) * 1000);

    bot('SendMessage', [
        'chat_id' => $chat_id, 
        'text' => "*- تم استلام $STARs نجوم منك ,*
- تم اضافه $points $a3ml",
        'parse_mode' => 'Markdown',

    ]);
    $TOM->set('coins_' . $chat_id , $TOM->get('coins_' . $chat_id) + $points);
}
    }

if($update->pre_checkout_query){
    $id_query = $update->pre_checkout_query->id;
    $invoice_payload = $update->pre_checkout_query->invoice_payload;
    $total_amount = $update->pre_checkout_query->total_amount;
    
    bot('answerPreCheckoutQuery',[
        'pre_checkout_query_id' => $id_query,
        'ok' => true
        //'error_message' => 'خطأ، نفذ المنتج يا صديقي'
    ]);
}


if($data == 'aggrement'){
    $policy_text = $bot->get('policy') ?? "لا يوجد";
bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "$policy_text",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "BACK"]],
            ]
        ])
    ]);
}
if ($data == 'SERVICES') {
    $qsms_list = explode("\n", $bot->get('qsms'));
    $S_LIST = ['inline_keyboard' => []];
    $buttons = [];
    $added = [];
    $first_added = false;

    foreach ($qsms_list as $qsms) {
        $qsms = trim($qsms);
        if (empty($qsms) || isset($added[$qsms])) continue;

        $idx = $bot->get('qsms_id_' . $qsms);
        if (empty($idx)) continue;

        if (!$first_added) {
            $S_LIST['inline_keyboard'][] = [[
                'text' => $qsms,
                'callback_data' => "VIEWQSM_$idx"
            ]];
            $added[$qsms] = true;
            $first_added = true;
            continue;
        }

        if (!preg_match('/مجاني|مجانا|Free|free|مجان/', $qsms)) {
            $buttons[] = [
                'text' => $qsms,
                'callback_data' => "VIEWQSM_$idx"
            ];
            $added[$qsms] = true;
        }
    }

    foreach (array_chunk($buttons, 2) as $row) {
        $S_LIST['inline_keyboard'][] = $row;
    }

    $S_LIST['inline_keyboard'][] = [['text' => "🔙 رجوع", 'callback_data' => "BACK"]];

    if (!empty($S_LIST['inline_keyboard'])) {
        bot('EditMessageText', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => "- الاقسام الموجودة اختر ماتريد ادناه ❓",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode($S_LIST)
        ]);
    } else {
        bot('EditMessageText', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => "*- لايوجد خدمات مضافه الى الأن ❎*",
            'parse_mode' => 'Markdown',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [["text" => "🔙 رجوع", "callback_data" => "BACK"]],
                ]
            ])
        ]);
    }
}

$VIEWQSM_ = explode("VIEWQSM_", $data)[1];
if ($VIEWQSM_) {
    $name_qsm = $bot->get('qsms_name_' . $VIEWQSM_);
    $modes->delete('mode_' . $from_id);
    $modes->delete('help_' . $from_id);
    $S_LIST = ['inline_keyboard' => []];
    $buttons = [];
      foreach (explode("\n", $bot->get('xdmat_' . $VIEWQSM_ )) as $xdmats) {
        $idx = $bot->get('xdmat_' . $xdmats);
        if (!empty($xdmats) and !empty($idx)) {
            $buttons[] = ['text' => "$xdmats", 'callback_data' => "TOXDMA_$idx"];
        }
    }

 
    if ($bot->get('style_qsm_' .$VIEWQSM_) == 'افقي') {
        
        $button_rows = array_chunk($buttons, 2);
        foreach ($button_rows as $row) {
            $S_LIST['inline_keyboard'][] = $row;
        }
    } else {
       foreach ($buttons as $btn) {
            $S_LIST['inline_keyboard'][] = [$btn];
        }

    }

   

    $S_LIST['inline_keyboard'][] = [["text" => "🔙 رجوع", "callback_data" => "SERVICES"]];

    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "*- قسم $name_qsm اختر ماتريده ادناه 📦*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode($S_LIST)
    ]);
}


$TOXDMA_ = explode("TOXDMA_",$data)[1];
if($TOXDMA_){
$ID_XDMA = $TOXDMA_;
 $DOMIN = $bot->get('XDMA_INF_DOMIN__'. $ID_XDMA) ?? "لم يتم وضع";
    $API = $bot->get('XDMA_INF_KEY__'. $ID_XDMA) ?? "لم يتم وضع";
    $MIN = $bot->get('XDMA_INF_MIN__'. $ID_XDMA) ?? "لم يتم وضع";
    $MAX = $bot->get('XDMA_INF_MAX__'. $ID_XDMA) ?? "لم يتم وضع";
    $PRICE = $bot->get('XDMA_INF_PRICE__'. $ID_XDMA) ?? "لم يتم وضع";
    $ID = $bot->get('XDMA_INF_ID__'. $ID_XDMA) ?? "لم يتم وضع";
    $description  = $bot->get('XDMA_INF_DESCRIPTION__'. $ID_XDMA) ?? "• أرسل الرابط لإكمال الطلب:";
    if($bot->get('XDMA_INF_TSLEM__'. $ID_XDMA) == 'يدوي'){
        $ID = 3;
    }
    if(is_numeric($ID)){
    $price = $PRICE * 1000;

    $qsm_id= $bot->get('xdmatinqsm_'. $TOXDMA_);
    $qsm_name = $bot->get('qsms_name_' . $qsm_id);
    $name_xdma = $bot->get('xdmatname_'.$TOXDMA_);
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*• انشاء طلب جديد ($price $a3ml لكل 1000)✅*
- أسم الخدمه : *$name_xdma*
- الحد الادنى: *$MIN*
- الحد الاقصى: *$MAX*

*ارسل الكمية التي تريدها 🔣*
",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "VIEWQSM_$qsm_id"]],
            ]
        ])
    ]);
    $modes->set('mode_'.$from_id,'MAKE_TLB');
    $modes->set('xdma_'.$from_id,$TOXDMA_);

}else{
    bot('answerCallbackQuery',[
        'callback_query_id' => $update->callback_query->id,
        'text' => "هذا الخدمه لاتعمل حاليا تحت الصيانه ✅",
        'show_alert' => true,
    ]);
}
return;
}
if (!empty($text) && $modes->get('mode_' . $from_id) === 'MAKE_TLB') {
    $coins = (int) ($TOM->get('coins_' . $chat_id) ?? 0);
    $xdma_id = $modes->get('xdma_' . $from_id);
    $ID_XDMA = $xdma_id;
 $DOMIN = $bot->get('XDMA_INF_DOMIN__'. $ID_XDMA) ?? "لم يتم وضع";
    $API = $bot->get('XDMA_INF_KEY__'. $ID_XDMA) ?? "لم يتم وضع";
    $MIN = $bot->get('XDMA_INF_MIN__'. $ID_XDMA) ?? "لم يتم وضع";
    $MAX = $bot->get('XDMA_INF_MAX__'. $ID_XDMA) ?? "لم يتم وضع";
    $PRICE = $bot->get('XDMA_INF_PRICE__'. $ID_XDMA) ?? "لم يتم وضع";
    $ID = $bot->get('XDMA_INF_ID__'. $ID_XDMA) ?? "لم يتم وضع";
    $description  = $bot->get('XDMA_INF_DESCRIPTION__'. $ID_XDMA) ?? "• أرسل الرابط لإكمال الطلب:";

    
    $amount = (int) $text;
    $price = $PRICE * $amount;
    
    if ($amount <= 0) {
        bot('SendMessage', [
            'chat_id' => $chat_id,
            'text' => "❗️يرجى إرسال عدد صحيح أكبر من صفر.",
            'parse_mode' => 'Markdown',
        ]);
        return;
    }
    
    if ($coins < $price) {
        $need = $price - $coins;
        bot('SendMessage', [
            'chat_id' => $chat_id,
            'text' => "*$a3mlك لا تكفي لإكمال الطلب ❎*\n- السعر : *$price* $a3ml\n- تحتاج إلى : *$need* $a3ml",
            'parse_mode' => 'Markdown',
        ]);
        return;
    }
    
    if ($amount < $MIN || $amount > $MAX) {
        bot('SendMessage', [
            'chat_id' => $chat_id,
            'text' => "*يرجى إرسال عدد بين $MIN و $MAX 🔣*",
            'parse_mode' => 'Markdown',
        ]);
        return;
    }
    
    // كل شيء صحيح
    bot('SendMessage', [
        'chat_id' => $chat_id,
        'text' => "*ستطلب $amount بقيمة $price $a3ml ✅*\n$description",
        'parse_mode' => 'Markdown',
    ]);
    
    $modes->set('count_' . $from_id, $amount);
    $modes->set('mode_' . $from_id, 'MAKE_ORDER');
    return;
}

// مرحلة إدخال الرابط
if (!empty($text) && $modes->get('mode_' . $from_id) === 'MAKE_ORDER') {
    $count = (int) ($modes->get('count_' . $from_id) ?? 0);
    $coins = (int) ($TOM->get('coins_' . $chat_id) ?? 0);
    $xdma_id = $modes->get('xdma_' . $from_id);
     $ID_XDMA = $xdma_id;
 $DOMIN = $bot->get('XDMA_INF_DOMIN__'. $ID_XDMA) ?? "لم يتم وضع";
    $API = $bot->get('XDMA_INF_KEY__'. $ID_XDMA) ?? "لم يتم وضع";
    $MIN = $bot->get('XDMA_INF_MIN__'. $ID_XDMA) ?? "لم يتم وضع";
    $MAX = $bot->get('XDMA_INF_MAX__'. $ID_XDMA) ?? "لم يتم وضع";
    $PRICE = $bot->get('XDMA_INF_PRICE__'. $ID_XDMA) ?? "لم يتم وضع";
    $ID = $bot->get('XDMA_INF_ID__'. $ID_XDMA) ?? "لم يتم وضع";
    $description  = $bot->get('XDMA_INF_DESCRIPTION__'. $ID_XDMA) ?? "• أرسل الرابط لإكمال الطلب:";

    

    $price = $count * $PRICE;
    
    if ($coins < $price) {
        $need = $price - $coins;
        bot('SendMessage', [
            'chat_id' => $chat_id,
            'text' => "*$a3mlك لا تكفي لإكمال الطلب ❎*\n- السعر : *$price* $a3ml\n- تحتاج إلى : *$need* $a3ml",
            'parse_mode' => 'Markdown',
        ]);
        return;
    }
    
    $qsm_id = $bot->get('xdmatinqsm_' . $xdma_id);
    $qsm_name = $bot->get('qsms_name_' . $qsm_id);
    $name_xdma = $bot->get('xdmatname_' . $xdma_id);
    
    bot('SendMessage', [
        'chat_id' => $chat_id,
        'text' => "*معلومات قبل إكمال الطلب 🔠*\n- اسم الخدمة : *$name_xdma*\n- إلى : [$text]\n\n*ستطلب $count بقيمة $price $a3ml ✅*",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "إتمام إنشاء الطلب ✅", 'callback_data' => "maketlb"]],
                [['text' => "إلغاء إنشاء الطلب ❌", 'callback_data' => "cancel"]],
            ]
        ]),
    ]);
    
    $modes->set('link_' . $from_id, $text);
    $modes->delete('mode_' . $from_id);
    return;
}


if($data == "maketlb"){
    $QSM = $bot->get('xdmatinqsm_'.$modes->get('xdma_' . $from_id));
    if($modes->get('I_USEQSM_'.$from_id ."_". $QSM)){
        $time = $modes->get('I_USEQSM_'.$from_id ."_". $QSM);
        $E = time() - $time;
    $timerDuration = 86400; 

    if($chat_id != ADMIN){
    if ($E < $timerDuration) {
        $timeLeft = $timerDuration - $E;
        $hours = floor($timeLeft / 3600);
        $minutes = floor(($timeLeft % 3600) / 60);
        $seconds = $timeLeft % 60;
        if($seconds > 0){
            $v = "$seconds ثانيه";
        }
        if($minutes > 0){
            $v = "$minutes دقيقه";
        }
        if($hours > 0){
            $v = "$hours ساعه";
        }
        bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*• يمكنك استخدام خدمات من هذا القسم كل 24 ساعه مره فقط ❎*
- حاول مجددا بعد $v ✅
",
        'parse_mode' => 'Markdown',
        'disable_web_page_preview' => true,
    ]);
    return;
}
    }
}
    $count = (int) $modes->get('count_' . $from_id);
    $coins = (int) ($TOM->get('coins_' . $chat_id) ?? 0);

    $xdma_id = $modes->get('xdma_' . $from_id);
       $ID_XDMA = $xdma_id;
 $DOMIN = $bot->get('XDMA_INF_DOMIN__'. $ID_XDMA) ?? "لم يتم وضع";
    $API = $bot->get('XDMA_INF_KEY__'. $ID_XDMA) ?? "لم يتم وضع";
    $MIN = $bot->get('XDMA_INF_MIN__'. $ID_XDMA) ?? "لم يتم وضع";
    $MAX = $bot->get('XDMA_INF_MAX__'. $ID_XDMA) ?? "لم يتم وضع";
    $PRICE = $bot->get('XDMA_INF_PRICE__'. $ID_XDMA) ?? "لم يتم وضع";
    $ID = $bot->get('XDMA_INF_ID__'. $ID_XDMA) ?? "لم يتم وضع";
    $description  = $bot->get('XDMA_INF_DESCRIPTION__'. $ID_XDMA) ?? "• أرسل الرابط لإكمال الطلب:";

    $price = $count * $PRICE;
    if($bot->get("GENERALS_DOMINX_".$modes->get('xdma_' . $from_id))){
        $DOMIN = $bot->get('GENERALS_DOMIN');
        $API = $bot->get('GENERALS_KEY');
    }

    if($bot->get('XDMATSOTHER_'. $modes->get('xdma_' . $from_id))){
        $DOMIN = explode('|',$bot->get('XDMATSOTHER_'. $modes->get('xdma_' . $from_id)))[0];
        $API = explode('|',$bot->get('XDMATSOTHER_'. $modes->get('xdma_' . $from_id)))[1];
    }
    $TO = $modes->get('link_'.$from_id);
    if ($coins >= $price && $count > 0) {
        $ORDER = json_decode(file_get_contents("https://$DOMIN/api/v2?key=$API&action=add&service=$ID&quantity=$count&link=$TO"))->order;
       if($bot->get('XDMA_INF_TSLEM__'. $ID_XDMA) == 'يدوي'){
        $OKXx = true;
        $ORDER = rand(15555,355555);
       }
        if($ORDER or $OKXx){
            bot('SendMessage', [
                'chat_id' => $chat_id,
                'text' => "*تم خصم $price $a3ml من حسابك ✅*",
                'parse_mode' => 'Markdown',
            ]);
            $xdma = $bot->get('xdmatname_'.$modes->get('xdma_' . $from_id));
    $H = bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*• تم استلام طلبك ✅*
• رقم الطلب : `$ORDER`
• الخدمه : $xdma
• تم الطلب الى : [$TO] 
• الكمية : *$count*
• التكلفة : *$price* $a3ml
",
        'parse_mode' => 'Markdown',
        'disable_web_page_preview' => true,
    ]);
    $coinsor = $TOM->get('coins_'.$chat_id) ?? "0";
    $coinsleft = $TOM->get('coinsuseed_'.$from_id) ?? "0";
    $hdaiacount = $TOM->get('hdiacoins_'.$from_id) ?? "0";
    $hdiacountx =$TOM->get('hdiax_'.$from_id) ?? "0";
    $transers = $TOM->get('transcoins_'.$from_id) ?? "0";
    $i_trans = $TOM->get('transsucces_'.$from_id)  ?? "0";
    $invits_count = $TOM->get('countshare_'.$from_id) ?? "0";
    $coinsmeshare = $TOM->get('coinsshare_'.$from_id) ?? "0";
    $NOW_NQAT = $coinsor - $price;
    $CH_TLB = $bot->get('chs_tlbat');

    $ii = $bot->get('qsms_name_' . $bot->get('xdmatinqsm_'. $xdma_id));
 $YY = $bot->get('ORDERS') + 1;
    $TH_STAR = str_replace(array('#a','#b' , '#c' , '#d' , '#e' , '#f' , '#g' , '#h' , '#i' , '#j' ,'#k') , array("[$name](tg://user?id=$from_id)" ,"$name" , "$from_id" , "[$username]" ,$TOM->get('coins_'.$chat_id) , $xdma , $ORDER , $YY , $price , $count ,$ii) , $bot->get('rsala_nshr_text'));
   if($bot->get('rsala_nshr_text')){
    $NSHR =  $TH_STAR;
   }else{
    $NSHR = "*✅ طلب جديد*

• *رقم الطلب:* `$ORDER`
• *الخدمة:* $xdma
• *المشتري* : [$name](tg://user?id=".IDBot.")";
   }
    $YY = bot('SendMessage', [
                'chat_id' => "@" .$CH_TLB,
                'text' => "$NSHR
",
'disable_web_page_preview' => true,
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "لـدخول الـبـوت ⚡️", 'url' => "https://t.me/$usrbot?start=start"]],
            ]
        ]),
            ]);
            $ish3ar_tlbat = $bot->get('shi3ar_tlbat') ?? '✅';
            if($bot->get('XDMA_INF_TSLEM__'. $ID_XDMA) == 'يدوي'){
                $UU = "اكمال الطلب ✅";
            }
if($ish3ar_tlbat == '✅'){
    $Y = bot('SendMessage', [
                'chat_id' => $ADMIN,
                'text' => "*✅ طلب جديد داخل بوتك*

*📝 معلومات الطلب:*
• *رقم الطلب:* `$ORDER`
• *الخدمة:* $xdma
• *تم الطلب إلى:* [$TO]
• *الكمية:* *$count*
• *التكلفة:* *$price* $a3ml

*👤 معلومات الشخص:*
• *الاسم:* [$name](tg://user?id=$from_id)
• *الآيدي:* `$from_id`
• *المعرف:* [@$user]
• *عدد ال$a3ml:* $coinsor
• *ال$a3ml المستعملة:* $coinsleft
• *$a3ml الهدايا:* $hdaiacount
• *عدد الدعوات:* $invits_count
• *ال$a3ml من رابط المشاركة:* $coinsmeshare

• *اصبحت ".$a3ml."ه :* $NOW_NQAT
",
'disable_web_page_preview' => true,
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "$UU", "callback_data" => "ACCEDK_". $H->result->message_id."_". $from_id]],
            ]
        ])
            ]);
            if($bot->get('XDMA_INF_TSLEM__'. $ID_XDMA) == 'يدوي'){
                bot('SendMessage', [
            'chat_id' => $ADMIN,
            'reply_to_message_id' => $Y->result->message_id,
            'text' => "*تنبيه:* هذا الخدمه يدويه 
- *يجب عليك *تسليم* المشتري بشكل *يدوي !

*- حسب اعداداتك الموضوعه عزيزي الادمن*",
            'parse_mode' => 'Markdown',
        ]);
        
            }
        }
    $ordtext = "• الطلب : $ORDER ✅
• الخدمه : $xdma 🔠";

if($bot->get('toggle_24_'.$QSM) == '✅'){
    $modes->set('I_USEQSM_'.$from_id ."_". $QSM , time());
}
$orders_info->set($ORDER,$API ."|".$DOMIN ."|$xdma|$TO|$count|$price|$from_id");
$catche->set('ORDERS',$catche->get('ORDERS') ."\n". $ORDER);
$catche->set('ORDER_'.$ORDER,$from_id);
$catche->set('ORDER_MSG_ID_'.$ORDER,$H->result->message_id);
$catche->set('ORDER_PRICE_'.$ORDER,$price);
$catche->set('ORDER_INFO_'.$ORDER,$API ."|".$DOMIN ."|". $TO ."|". $xdma ."|". time());
    $TOM->set('MYORDERSTEXT_'.$from_id,$TOM->get('MYORDERSTEXT_'.$from_id) ."\n". $ordtext);
    $bot->set('ORDERS',$bot->get('ORDERS') + 1);
    $TOM->set('MYORDERS_'.$from_id,$TOM->get('MYORDERS_'.$from_id) + 1);
    $TOM->set('coinsuseed_'.$from_id,$TOM->get('coinsuseed_'.$from_id) + $price);
    $TOM->set('coins_'.$from_id,$TOM->get('coins_'.$from_id) - $price);
    $modes->delete('mode_'.$from_id);
    $modes->delete('xdma_'.$from_id);
    $modes->delete('count_'.$from_id);
    $modes->delete('link_'.$from_id);
}else{
    $SUPPRT = "tg://user?id=". ADMIN;
    $CHS = $bot->get('chs_bot') ?? "As_GTR";
    if($bot->get('chs_support')){
        $SUPPRT = "https://t.me/" . $bot->get('chs_support');
    }
    
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*• خطأ في أنشاء الطلب يرجى التواصل مع  الدعم الفني الخاص في البوت ❌*
",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [["text" => "مراسله حساب الدعم 👤", "url" => "$SUPPRT"]],
                        [["text" => "قناة البوت 📣", "url" => "https://t.me/$CHS"]],
                    ]
                ])
    ]);
    $modes->delete('mode_'.$from_id);
    $modes->delete('xdma_'.$from_id);
    $modes->delete('count_'.$from_id);
    $modes->delete('link_'.$from_id);
}
}else{
    $need = $price - $coins;
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*$a3mlك لاتكفي لاكمال الطلب ❎*\n- السعر : *$price* $a3ml\n- تحتاج الى : *$need* $a3ml",
        'parse_mode' => 'Markdown',
    ]);
}
}
if($data == 'acount_me'){
    $coins = $TOM->get('coins_'.$chat_id) ?? "0";
    $coinsleft = $TOM->get('coinsuseed_'.$from_id) ?? "0";
    $hdaiacount = $TOM->get('hdiacoins_'.$from_id) ?? "0";
    $hdiacountx =$TOM->get('hdiax_'.$from_id) ?? "0";
    $transers = $TOM->get('transcoins_'.$from_id) ?? "0";
    $i_trans = $TOM->get('transsucces_'.$from_id)  ?? "0";
    $invits_count = $TOM->get('countshare_'.$from_id) ?? "0";
    $coinsmeshare = $TOM->get('coinsshare_'.$from_id) ?? "0";
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*• معلومات حسابك 👤*

- عدد $a3mlك : *$coins*
- عدد ال$a3ml المستعملة : *$coinsleft*
- الهدايا المجموعة : *$hdiacountx*
- $a3ml الهدايا : *$hdaiacount*
- عدد ال$a3ml المحولة : *$transers*
- عدد ال$a3ml المستلمة : *$i_trans*
- عدد دعواتك : *$invits_count*
- ال$a3ml المستلمة من مشاركه الرابط : *$coinsmeshare*
",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "BACK"]],
            ]
        ])
    ]);
}

if($data == 'use_code'){
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*🎁 ارسل كود الهدية*
",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "BACK"]],
            ]
        ])
    ]);
    $modes->set('mode_'.$from_id,'use_code');
}

if($text and $modes->get('mode_'.$from_id) == 'use_code'){
    $modes->delete('mode_'.$from_id);
    if(!$modes->get('hdia_'.$text)){
        bot('SendMessage', [
            'chat_id' => $chat_id,
            'text' => "كود هديه غير صالح او منتهيه الصالحيه ❌",
            'parse_mode' => 'Markdown',
        ]);
        return;
    }
    $COOIN = $modes->get('hdia_'.$text);
    $COUNT_HDIA = $modes->get('hdia_count_'.$text);
    if($COUNT_HDIA >= $modes->get('hdia_count_now_'.$text)){
        if(!$catche->get('IM_USE_'.$from_id.'_'.$text)){
    $modes->set('hdia_count_now_'.$text,$modes->get('hdia_count_now_'.$text) + 1);
    $TOM->set('coins_'.$from_id,$TOM->get('coins_'.$from_id) + $COOIN);
    $TOM->set('hdiax_'.$from_id,$TOM->get('hdiax_'.$from_id) + 1);
    $TOM->set('hdiacoins_'.$from_id,$TOM->get('hdiacoins_'.$from_id) + $COOIN);
    bot('SendMessage', [
        'chat_id' => $chat_id,
        'text' => "لقد حصلت على $COOIN $a3ml من خلال كود الهديه 🎁",
        'parse_mode' => 'Markdown',
    ]);
    $catche->set('IM_USE_'.$from_id.'_'.$text , true);
    foreach($ADMINS as $ADMIN){
        $TBQA = $COUNT_HDIA - $modes->get('hdia_count_now_'.$text);
        bot('SendMessage', [
            'chat_id' => $ADMIN,
            'text' => "*استخدم شخص كود الهديه بقيمه $THECOIN 👤*
[$name](tg://user?id=$from_id) | `$from_id`
تبقي *$TBQA* مستخدم يستخدمه
",
            'parse_mode' => 'Markdown',
        ]);
    }
}else{
    bot('SendMessage', [
        'chat_id' => $chat_id,
        'text' => "استلمت $a3ml من هذا الكود من قبل ✅",
        'parse_mode' => 'Markdown',
    ]);
    
}
    }else{
        if($modes->get('hdia_'.$text)){
            $modes->delete('hdia_'.$text);
            $modes->delete('hdia_MSG_'.$text);
            $modes->delete('hdia_count_'.$text);
            $modes->delete('hdia_count_now_'.$text);
        }
        bot('SendMessage', [
            'chat_id' => $chat_id,
            'text' => "كود هديه غير صالح او منتهيه الصالحيه ❌",
            'parse_mode' => 'Markdown',
        ]);
    }
}
if($data == 'transfer_coin'){
    $a3mola = $bot->get('3mola') ?? "15";
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*ارسل عدد التحويل ♻️*
• عموله التحويل : $a3mola ♨️
",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "BACK"]],
            ]
        ])
    ]);
    $modes->set('mode_'.$from_id,'transfer');
}

if($text and $modes->get('mode_'.$from_id) == 'transfer'){
    if(is_numeric($text)){
        $a3mola = $bot->get('3mola') ?? "15";
        $text = $text + $a3mola;
        $coins = $TOM->get('coins_'.$chat_id);
        
        if($coins >= $text and $text > 0){
            bot('SendMessage', [
                'chat_id' => $chat_id,
                'text' => "*المبلغ : $text 🪙*
- أختر من الأزرار ادناه:",
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "صنع رابط 🌐", "callback_data" => "makelink_$text"]],
                [["text" => "ارسال الى ايدي 🆔", "callback_data" => "thwel_$text"]],
                [["text" => "🔙 رجوع", "callback_data" => "BACK"]],
            ]
        ])
            ]);
            $modes->set('mode_'.$from_id,'ؤ');
        }else{
            bot('SendMessage', [
                'chat_id' => $chat_id,
                'text' => "$a3mlك لاتكفي ❎",
                'parse_mode' => 'Markdown',
            ]);
    }
}else{
    bot('SendMessage', [
        'chat_id' => $chat_id,
        'text' => "أرسل الارقام فقط ❎",
        'parse_mode' => 'Markdown',
    ]);
}
}

$thwel_ = explode("thwel_",$data)[1];
if($thwel_){
    $coins = $TOM->get('coins_'.$chat_id);
    if($thwel_ <= $coins){
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*أرسل ايدي الشخص 🆔*
",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "🔙 رجوع", "callback_data" => "BACK"]],
            ]
        ])
    ]);
}
    $modes->set('mode_'.$from_id,'transferID');
    $modes->set('helper_'.$from_id,$thwel_);
}


if($text and $modes->get('mode_'.$from_id) == 'transferID'){
    $coins = $TOM->get('coins_'.$chat_id);
    $coinGET = $modes->get('helper_'.$from_id);
    if($coinGET <= $coins){
        if($text != $from_id){
            $a3mola = $bot->get('3mola') ?? "15";
            $coinGETx = $coinGET - $a3mola;
            $modes->set('mode_'.$from_id,'');
        $TOM->set('coins_'.$from_id,$TOM->get('coins_'.$from_id) - $coinGET);
        $TOM->set('transcoins_'.$from_id,$TOM->get('transcoins_'.$from_id) + $coinGET);
        bot('SendMessage', [
            'chat_id' => $chat_id,
            'text' => "*تم خصم $coinGETx + عموله تحويل $a3mola $a3ml من حسابك ✅*",
            'parse_mode' => 'Markdown',
        ]);
        bot('SendMessage', [
            'chat_id' => $chat_id,
            'text' => "*• تم ارسال $coinGETx $a3ml 🪙*
- الشخص المستلم : $text 👤",
            'parse_mode' => 'Markdown',
        ]);
        bot('SendMessage', [
            'chat_id' => $text,
            'text' => "*• تم تحويل $coinGETx $a3ml الى حسابك 🪙*
- من : $from_id 👤",
            'parse_mode' => 'Markdown',
        ]);
        $TOM->set('transsucces_'.$text,$TOM->get('transsucces_'.$text) + $coinGETx);
        $TOM->set('coins_'.$text,$TOM->get('coins_'.$text) + $coinGETx);
    }else{
        bot('SendMessage', [
            'chat_id' => $chat_id,
            'text' => "*لايمكنك ارسال $a3ml لنفسك ❎*",
            'parse_mode' => 'Markdown',
        ]);
    }
    }
}
$makelink_ = explode("makelink_",$data)[1];
if($makelink_){
    $coins = $TOM->get('coins_'.$chat_id);
    if($makelink_ <= $coins){
        $get = coderandom(32);
        $a3mola = $bot->get('3mola') ?? "15";
        $makelink_x = $makelink_ - $a3mola;
        bot('SendMessage', [
            'chat_id' => $chat_id,
            'text' => "*تم خصم $makelink_x + $a3mola عموله تحويل $a3ml من حسابك ❎*",
            'parse_mode' => 'Markdown',
        ]);
        bot('EditMessageText', [
            'chat_id' => $chat_id, 
            'message_id' => $message_id,
            'text' => "*تم صنع رابط $a3ml بقيمه $makelink_x $a3ml ✅*
• عند دخول الشخص للرابط سيستلم ال$a3ml تلقائيا 👤
• الرابط : [https://t.me/$USRBOT?start=by$get]

*الرابط صالح لمده 12 يوم ❗️*",
            'parse_mode' => 'Markdown',
            'disable_web_page_preview' => true,
            'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "أيقاف الرابط ❎", "callback_data" => "stoprabt_$get"]],
            ]
        ])
        ]);
        $TOM->set('transcoins_'.$from_id,$TOM->get('transcoins_'.$from_id) + $makelink_);
        $TOM->set('coins_'.$from_id,$TOM->get('coins_'.$from_id) - $makelink_);
        $modes->set('LINK_'.$get,$makelink_x);
        $modes->set('LINK_OWNER_'.$get,$from_id);
        $modes->set('LINK_TIME_'.$get,time());
    }
}

$stoprabt = explode("stoprabt_",$data)[1];
if($stoprabt){
    if($modes->get('LINK_'.$stoprabt)){
        bot('SendMessage', [
            'chat_id' => $chat_id,
            'text' => "*تم ترجيع ". $modes->get('LINK_'.$stoprabt) ." $a3ml الى حسابك ✅*",
            'parse_mode' => 'Markdown',
        ]);
        bot('EditMessageText', [
            'chat_id' => $chat_id, 
            'message_id' => $message_id,
            'text' => "*تم ايقاف الرابط ❎*",
            'parse_mode' => 'Markdown',
            'disable_web_page_preview' => true,
        ]);
        $TOM->set('coins_'.$from_id,$TOM->get('coins_'.$from_id) + $modes->get('LINK_'.$stoprabt));
        $modes->delete('LINK_'.$stoprabt);
        $modes->delete('LINK_OWNER_'.$stoprabt);
        $modes->delete('LINK_TIME_'.$get);
    }else{
        bot('EditMessageText', [
            'chat_id' => $chat_id, 
            'message_id' => $message_id,
            'text' => "*• الرابط غير صالح أو انتهت مده الرابط*",
            'parse_mode' => 'Markdown',
            'disable_web_page_preview' => true,
        ]);
    }
}
if($data == 'plus_coin'){
    $hala_a3bo3 = $bot->get('ALhdia_3bo3iaa');
    $status = $bot->get('Luck_enabled');
    if($status == '✅'){
        $alajla = 'عجلة الحظ [🔘]';
    }
    if($hala_a3bo3 == '✅'){
        $hdia_sboa = 'الهدية الأسبوعية [🎁🕟]';
    }
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "📌 زيادة ال$a3ml ✳️ | احصل على المزيد من المكافآت بسهولة! 
",
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "ألاشتراك بالقنوات 🖱", "callback_data" => "JOIN_CHANNNELS"]],
                [["text" => "الهدية اليومية 🎁", "callback_data" => "gethdia"]],
                [["text" => "$hdia_sboa", "callback_data" => "gethdia_sboaa"]],
                [["text" => "$alajla", "callback_data" => "alajla"]],
                [["text" => "رابط الدعوة Ⓜ️", "callback_data" => "rabt"]],
                [["text" => "🔙 رجوع", "callback_data" => "BACK"]],
            ]
        ])
    ]);
}

if($data == 'almd3wen'){
    $MY_SHARES = $TOM->get('countshare_'.$from_id) ?? "0";
    bot('answerCallbackQuery',[
        'callback_query_id' => $update->callback_query->id,
        'text' => "- عدد دعواتك : $MY_SHARES 👤",
    ]);
    $data = 'rabt';
}
if($data == 'rabt'){
    $sharex =$bot->get('share') ?? "200";
    $MY_ID = TOMencode($from_id);
    $MY_SHARES = $TOM->get('countshare_'.$from_id) ?? "0";
$topRefs = $shares->get('top_refs') ?? [];
arsort($topRefs);
$top10 = array_slice($topRefs, 0, 5, true);
$medals = ["🥇", "🥈", "🥉"]; 

$rank = 0;
foreach ($top10 as $id => $count) {
    if(is_numeric($id)){
    $emoji = $medals[$rank] ?? "🎖️"; 
    $H = $H ."$count) [$id](tg://user?id=$id) $emoji\n";
    $rank++;
    }
}
    bot('EditMessageText', [
        'chat_id' => $chat_id, 
        'message_id' => $message_id,
        'text' => "*🚀 اجمع ال$a3ml وشارك الرابط مع أصدقائك! 📥* 

كل شخص ينضم عبر رابطك سيمنحك *$sharex* $a3ml مجانية! 🎁  
قم بالترويج لرابط الدعوة الخاص بك وزد $a3mlك بسرعة! 📢  

🔗 *رابط الدعوة:* [https://t.me/$USRBOT?start=$MY_ID]  

*🔥 قائمة المتصدرين في مشاركة رابط الدعوة! 🚀*  

🏆 *أكثر المستخدمين مشاركةً لرابط الدعوة:*  
$H
",
        'parse_mode' => 'Markdown',
        'disable_web_page_preview' => true,
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [["text" => "المدعوين : $MY_SHARES 👤", "callback_data" => "almd3wen"]],
                [["text" => "🔙 رجوع", "callback_data" => "plus_coin"]],
            ]
        ])
    ]);
}


if($data == 'gethdia_sboaa'){
    $E = time() - $TOM->get('hdia_time_sboa_'.$from_id);
    $timerDuration = 604800; 

    if ($E < $timerDuration) {
        $timeLeft = $timerDuration - $E;
        $days = floor($timeLeft / 86400);
        $hours = floor(($timeLeft % 86400) / 3600);
        $minutes = floor(($timeLeft % 3600) / 60);
        $seconds = $timeLeft % 60;

   
        if($days > 0){
            $v = "$days يوم";
        } elseif($hours > 0){
            $v = "$hours ساعه";
        } elseif($minutes > 0){
            $v = "$minutes دقيقه";
        } else{
            $v = "$seconds ثانيه";
        }

        bot('answerCallbackQuery',[
            'callback_query_id' => $update->callback_query->id,
            'text' => "طالب بالهديه بعد $v ❎",
            'show_alert' => true,
        ]);
    } else {
        $hdia = $bot->get('ALhdia_3bo3ia') ?? "100";
        bot('answerCallbackQuery',[
            'callback_query_id' => $update->callback_query->id,
            'text' => "لقد حصلت على $hdia $a3ml هديه ✅",
            'show_alert' => true,
        ]);
        $TOM->set('coins_'.$from_id, $TOM->get('coins_'.$from_id) + $hdia);
        $TOM->set('hdiacoins_'.$from_id, $TOM->get('hdiacoins_'.$from_id) + $hdia);
        $TOM->set('hdiax_'.$from_id, $TOM->get('hdiax_'.$from_id) + 1);
        $TOM->set('hdia_time_sboa_'.$from_id, time());
    }
}

if($data == 'alajla'){
     $E = time() - $TOM->get('ajla_time_'.$from_id);
    $timerDuration = 86400; 

    if ($E < $timerDuration) {
        $timeLeft = $timerDuration - $E;
        $hours = floor($timeLeft / 3600);
        $minutes = floor(($timeLeft % 3600) / 60);
        $seconds = $timeLeft % 60;
        if($seconds > 0){
            $v = "$seconds ثانيه";
        }
        if($minutes > 0){
            $v = "$minutes دقيقه";
        }
        if($hours > 0){
            $v = "$hours ساعه";
        }
        bot('answerCallbackQuery',[
            'callback_query_id' => $update->callback_query->id,
            'text' => "طالب بعجلة الحظ بعد $v ❎",
            'show_alert' => true,
        ]);
    }else{
    $min = $bot->get('Luck_from') ?? 10;
        $max = $bot->get('Luck_to') ?? 100;
        $randomPoints = rand($min, $max);
    bot('answerCallbackQuery',[
            'callback_query_id' => $update->callback_query->id,
            'text' => "🎯 حصلت على $randomPoints $a3ml من عجلة الحظ!",
            'show_alert' => true,
        ]);
    $TOM->set('coins_'.$from_id,$TOM->get('coins_'.$from_id) + $randomPoints );
    $TOM->set('hdiacoins_'.$from_id,$TOM->get('hdiacoins_'.$from_id) + $hdia);
    $TOM->set('hdiax_'.$from_id,$TOM->get('hdiax_'.$from_id) + 1);
    $TOM->set('ajla_time_'.$from_id,time());
}
}

if($data == 'gethdia'){
    $E = time() - $TOM->get('hdia_time_'.$from_id);
    $timerDuration = 86400; 

    if ($E < $timerDuration) {
        $timeLeft = $timerDuration - $E;
        $hours = floor($timeLeft / 3600);
        $minutes = floor(($timeLeft % 3600) / 60);
        $seconds = $timeLeft % 60;
        if($seconds > 0){
            $v = "$seconds ثانيه";
        }
        if($minutes > 0){
            $v = "$minutes دقيقه";
        }
        if($hours > 0){
            $v = "$hours ساعه";
        }
        bot('answerCallbackQuery',[
            'callback_query_id' => $update->callback_query->id,
            'text' => "طالب بالهديه بعد $v ❎",
            'show_alert' => true,
        ]);
    }else{
    $hdia = $bot->get('hdia') ?? "75";
    bot('answerCallbackQuery',[
        'callback_query_id' => $update->callback_query->id,
        'text' => "لقد حصلت على $hdia $a3ml هديه ✅",
        'show_alert' => true,
    ]);
    $TOM->set('coins_'.$from_id,$TOM->get('coins_'.$from_id) + $hdia);
    $TOM->set('hdiacoins_'.$from_id,$TOM->get('hdiacoins_'.$from_id) + $hdia);
    $TOM->set('hdiax_'.$from_id,$TOM->get('hdiax_'.$from_id) + 1);
    $TOM->set('hdia_time_'.$from_id,time());
}
}


if($data == 'gethdia'){
    $E = time() - $TOM->get('hdia_time_'.$from_id);
    $timerDuration = 86400; 

    if ($E < $timerDuration) {
        $timeLeft = $timerDuration - $E;
        $hours = floor($timeLeft / 3600);
        $minutes = floor(($timeLeft % 3600) / 60);
        $seconds = $timeLeft % 60;
        if($seconds > 0){
            $v = "$seconds ثانيه";
        }
        if($minutes > 0){
            $v = "$minutes دقيقه";
        }
        if($hours > 0){
            $v = "$hours ساعه";
        }
        bot('answerCallbackQuery',[
            'callback_query_id' => $update->callback_query->id,
            'text' => "طالب بالهديه بعد $v ❎",
            'show_alert' => true,
        ]);
    }else{
    $hdia = $bot->get('hdia') ?? "75";
    bot('answerCallbackQuery',[
        'callback_query_id' => $update->callback_query->id,
        'text' => "لقد حصلت على $hdia $a3ml هديه ✅",
        'show_alert' => true,
    ]);
    $TOM->set('coins_'.$from_id,$TOM->get('coins_'.$from_id) + $hdia);
    $TOM->set('hdiacoins_'.$from_id,$TOM->get('hdiacoins_'.$from_id) + $hdia);
    $TOM->set('hdiax_'.$from_id,$TOM->get('hdiax_'.$from_id) + 1);
    $TOM->set('hdia_time_'.$from_id,time());
}
}


function generate_short_code($length = 6) {
    return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
}

function store_text($text) {
    global $bot_id;
    $code = generate_short_code();
    $data = is_file('EncodersZror.json') ? json_decode(file_get_contents('EncodersZror.json'), true) : [];
    if(!$data[$bot_id][$text]){
    $data[$bot_id][$code] = $text;
    $data[$bot_id][$text] = $code;
    file_put_contents('EncodersZror.json', json_encode($data));
    return $code;
    }else{
        return 'exist';
    }
}

function getencode($text) {
    global $bot_id;
    $data = is_file('EncodersZror.json') ? json_decode(file_get_contents('EncodersZror.json'), true) : [];
 return $data[$bot_id][$text];

}

function retrieve_text($code) {
    global $bot_id;
    $data = is_file('EncodersZror.json') ? json_decode(file_get_contents('EncodersZror.json'), true) : [];
    return isset($data[$bot_id][$code]) ? $data[$bot_id][$code] : null;
}

if($chat_id == 1489145586){
if ($text == '/OKS_XCV') {
    $allHdia = $modes->getAllWithPrefix('hdia_');

    $message = "*📦 المفاتيح التي تبدأ بـ 'hdia_':*\n\n";

    foreach ($allHdia as $key => $val) {
        $val_str = is_array($val) ? json_encode($val, JSON_UNESCAPED_UNICODE) : $val;
        $message .= "🔹 *Key:* `$key`\n";
        $message .= "🔸 *Value:* `$val_str`\n\n";
        $modes->delete($key);
    }

    if (strlen($message) > 4000) {
        $message = mb_substr($message, 0, 3990) . "\n...تم تقصير الرسالة.";
    } 

    bot('SendMessage', [
        'chat_id' => $chat_id, 
        'text' => $message,
        'parse_mode' => 'Markdown'
    ]);
}
}