<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
ini_set('memory_limit', '-1');
// set max execution time 2 hours / mostly used for exporting PDF
ini_set('max_execution_time', 3600);

class Filemanager extends Admin
{
    var $path = 'cc-content/modules/filemanager/';
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {   $this->data['asset_path'] = $this->path.'public/';
        $this->template->title(lang('filemanager'));
        $this->data['theme'] = 'window'; // material or window
        $this->render('filemanager/backend/filemanager', $this->data);
    }
    public function elfinder_init()
    {
            $this->load->helper('file');
            $this->load->helper('path');
            $_allowed_files = $this->config_item('allowed_files');
            $config_allowed_files = array();
            if (is_array($_allowed_files)) {
                foreach ($_allowed_files as $v_extension) {
                    array_push($config_allowed_files, '.' . $v_extension);
                }
            }

            $allowed_files = array();
            if (is_array($config_allowed_files)) {
                foreach ($config_allowed_files as $extension) {
                    $_mime = get_mime_by_extension($extension);

                    if ($_mime == 'application/x-zip') {
                        array_push($allowed_files, 'application/zip');
                    }
                    if ($extension == '.exe') {
                        array_push($allowed_files, 'application/x-executable');
                        array_push($allowed_files, 'application/x-msdownload');
                        array_push($allowed_files, 'application/x-ms-dos-executable');
                    }
                    if(!empty($_mime))
                        array_push($allowed_files, $_mime);
                }
            }

            $path = $this->path.'uploads';

            $root_options = array(
                'driver' => 'LocalFileSystem',
                'path' => set_realpath($path),
                'URL' => base_url($path) . '/',
                'uploadMaxSize' => $this->config_item('max_file_size') . 'M',
                'accessControl' => 'access',
                'uploadAllow' => $allowed_files,
                'uploadDeny' => [
                    'application/x-httpd-php',
                    'application/php',
                    'application/x-php',
                    'text/php',
                    'text/x-php',
                    'application/x-httpd-php-source',
                    'application/perl',
                    'application/x-perl',
                    'application/x-python',
                    'application/python',
                    'application/x-bytecode.python',
                    'application/x-python-bytecode',
                    'application/x-python-code',
                    'wwwserver/shellcgi', // CGI
                ],
                'uploadOrder' => array(
                    'allow',
                    'deny'
                ),
                'uploadMaxConn' => -1, //disabled
                'attributes' => array(
                    array(
                        'pattern' => '/.tmb/',
                        'hidden' => true
                    ),
                    array(
                        'pattern' => '/.quarantine/',
                        'hidden' => true
                    )
                )
            );
            if (!$this->aauth->is_admin()) {
                $user = $this->aauth->get_user();
                $slug = $this->slug_it($user->full_name);
                $path = $path.'/'.$slug;
                
                if (!is_dir($path)) {
                    mkdir($path);
                    sleep(5);
                    return $this->elfinder_init();
                }
                
                array_push($root_options['attributes'], array(
                    'pattern' => '/.(' . $slug . '+)/', // Prevent deleting/renaming folder
                    'read' => true,
                    'write' => true,
                    'locked' => true
                ));
                $root_options['path'] = set_realpath($path);
                $root_options['URL'] = base_url($path) . '/';
            }
            $opts = array(
                'bind' => array('upload' => array(array($this, 'setToken'))),
                'roots' => array(
                    $root_options
                )
            );
            if (!file_exists($path . '/index.html')) {
                fopen($path . '/index.html', 'w');
            }

            $this->load->library('filemanager/elfinder_lib', $opts);
    }


public function setToken($cmd, &$result, $args, $elfinder) {
    $token_name = $this->security->get_csrf_token_name(); //return string 'token'
    $hash = $this->security->get_csrf_hash();
    $result[$token_name] = $hash;
}

public function config_item($key){
    $config = [
        'max_file_size'=>5000000000000,
        'language'=>'english',
        'allowed_files' => ["xlsx", "csv", "xls", "docx", "doc", "pdf", "ppt", "pptx", "txt", "png", "jpg", "jpeg", "mp3", "mp4", "ogg", "mkv", "mov", "3gp", "movie", "flv", "wvm", "webm", "avi", "wma", "m4a"]
    ];
    return @$config[$key];
}

public function slug_it($str, $options = array())
    {
        // Make sure string is in UTF-8 and strip invalid UTF-8 characters
        $str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());
        $defaults = array(
            'delimiter' => '_',
            'limit' => null,
            'lowercase' => true,
            'replacements' => array(
                '
                /\b(ѓ)\b/i' => 'gj',
                '/\b(ч)\b/i' => 'ch',
                '/\b(ш)\b/i' => 'sh',
                '/\b(љ)\b/i' => 'lj'
            ),
            'transliterate' => true
        );
        // Merge options
        $options = array_merge($defaults, $options);
        $char_map = array(
            // Latin
            'À' => 'A',
            'Á' => 'A',
            'Â' => 'A',
            'Ã' => 'A',
            'Ä' => 'A',
            'Å' => 'A',
            'Æ' => 'AE',
            'Ç' => 'C',
            'È' => 'E',
            'É' => 'E',
            'Ê' => 'E',
            'Ë' => 'E',
            'Ì' => 'I',
            'Í' => 'I',
            'Î' => 'I',
            'Ï' => 'I',
            'Ð' => 'D',
            'Ñ' => 'N',
            'Ò' => 'O',
            'Ó' => 'O',
            'Ô' => 'O',
            'Õ' => 'O',
            'Ö' => 'O',
            'Ő' => 'O',
            'Ø' => 'O',
            'Ù' => 'U',
            'Ú' => 'U',
            'Û' => 'U',
            'Ü' => 'U',
            'Ű' => 'U',
            'Ý' => 'Y',
            'Þ' => 'TH',
            'ß' => 'ss',
            'à' => 'a',
            'á' => 'a',
            'â' => 'a',
            'ã' => 'a',
            'ä' => 'a',
            'å' => 'a',
            'æ' => 'ae',
            'ç' => 'c',
            'è' => 'e',
            'é' => 'e',
            'ê' => 'e',
            'ë' => 'e',
            'ì' => 'i',
            'í' => 'i',
            'î' => 'i',
            'ï' => 'i',
            'ð' => 'd',
            'ñ' => 'n',
            'ò' => 'o',
            'ó' => 'o',
            'ô' => 'o',
            'õ' => 'o',
            'ö' => 'o',
            'ő' => 'o',
            'ø' => 'o',
            'ù' => 'u',
            'ú' => 'u',
            'û' => 'u',
            'ü' => 'u',
            'ű' => 'u',
            'ý' => 'y',
            'þ' => 'th',
            'ÿ' => 'y',
            // Latin symbols
            '©' => '(c)',
            // Greek
            'Α' => 'A',
            'Β' => 'B',
            'Γ' => 'G',
            'Δ' => 'D',
            'Ε' => 'E',
            'Ζ' => 'Z',
            'Η' => 'H',
            'Θ' => '8',
            'Ι' => 'I',
            'Κ' => 'K',
            'Λ' => 'L',
            'Μ' => 'M',
            'Ν' => 'N',
            'Ξ' => '3',
            'Ο' => 'O',
            'Π' => 'P',
            'Ρ' => 'R',
            'Σ' => 'S',
            'Τ' => 'T',
            'Υ' => 'Y',
            'Φ' => 'F',
            'Χ' => 'X',
            'Ψ' => 'PS',
            'Ω' => 'W',
            'Ά' => 'A',
            'Έ' => 'E',
            'Ί' => 'I',
            'Ό' => 'O',
            'Ύ' => 'Y',
            'Ή' => 'H',
            'Ώ' => 'W',
            'Ϊ' => 'I',
            'Ϋ' => 'Y',
            'α' => 'a',
            'β' => 'b',
            'γ' => 'g',
            'δ' => 'd',
            'ε' => 'e',
            'ζ' => 'z',
            'η' => 'h',
            'θ' => '8',
            'ι' => 'i',
            'κ' => 'k',
            'λ' => 'l',
            'μ' => 'm',
            'ν' => 'n',
            'ξ' => '3',
            'ο' => 'o',
            'π' => 'p',
            'ρ' => 'r',
            'σ' => 's',
            'τ' => 't',
            'υ' => 'y',
            'φ' => 'f',
            'χ' => 'x',
            'ψ' => 'ps',
            'ω' => 'w',
            'ά' => 'a',
            'έ' => 'e',
            'ί' => 'i',
            'ό' => 'o',
            'ύ' => 'y',
            'ή' => 'h',
            'ώ' => 'w',
            'ς' => 's',
            'ϊ' => 'i',
            'ΰ' => 'y',
            'ϋ' => 'y',
            'ΐ' => 'i',
            // Turkish
            'Ş' => 'S',
            'İ' => 'I',
            'Ç' => 'C',
            'Ü' => 'U',
            'Ö' => 'O',
            'Ğ' => 'G',
            'ş' => 's',
            'ı' => 'i',
            'ç' => 'c',
            'ü' => 'u',
            'ö' => 'o',
            'ğ' => 'g',
            // Russian
            'А' => 'A',
            'Б' => 'B',
            'В' => 'V',
            'Г' => 'G',
            'Д' => 'D',
            'Е' => 'E',
            'Ё' => 'Yo',
            'Ж' => 'Zh',
            'З' => 'Z',
            'И' => 'I',
            'Й' => 'J',
            'К' => 'K',
            'Л' => 'L',
            'М' => 'M',
            'Н' => 'N',
            'О' => 'O',
            'П' => 'P',
            'Р' => 'R',
            'С' => 'S',
            'Т' => 'T',
            'У' => 'U',
            'Ф' => 'F',
            'Х' => 'H',
            'Ц' => 'C',
            'Ч' => 'Ch',
            'Ш' => 'Sh',
            'Щ' => 'Sh',
            'Ъ' => '',
            'Ы' => 'Y',
            'Ь' => '',
            'Э' => 'E',
            'Ю' => 'Yu',
            'Я' => 'Ya',
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'yo',
            'ж' => 'zh',
            'з' => 'z',
            'и' => 'i',
            'й' => 'j',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'h',
            'ц' => 'c',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'sh',
            'ъ' => '',
            'ы' => 'y',
            'ь' => '',
            'э' => 'e',
            'ю' => 'yu',
            'я' => 'ya',
            // Ukrainian
            'Є' => 'Ye',
            'І' => 'I',
            'Ї' => 'Yi',
            'Ґ' => 'G',
            'є' => 'ye',
            'і' => 'i',
            'ї' => 'yi',
            'ґ' => 'g',
            // Czech
            'Č' => 'C',
            'Ď' => 'D',
            'Ě' => 'E',
            'Ň' => 'N',
            'Ř' => 'R',
            'Š' => 'S',
            'Ť' => 'T',
            'Ů' => 'U',
            'Ž' => 'Z',
            'č' => 'c',
            'ď' => 'd',
            'ě' => 'e',
            'ň' => 'n',
            'ř' => 'r',
            'š' => 's',
            'ť' => 't',
            'ů' => 'u',
            'ž' => 'z',
            // Polish
            'Ą' => 'A',
            'Ć' => 'C',
            'Ę' => 'e',
            'Ł' => 'L',
            'Ń' => 'N',
            'Ó' => 'o',
            'Ś' => 'S',
            'Ź' => 'Z',
            'Ż' => 'Z',
            'ą' => 'a',
            'ć' => 'c',
            'ę' => 'e',
            'ł' => 'l',
            'ń' => 'n',
            'ó' => 'o',
            'ś' => 's',
            'ź' => 'z',
            'ż' => 'z',
            // Latvian
            'Ā' => 'A',
            'Č' => 'C',
            'Ē' => 'E',
            'Ģ' => 'G',
            'Ī' => 'i',
            'Ķ' => 'k',
            'Ļ' => 'L',
            'Ņ' => 'N',
            'Š' => 'S',
            'Ū' => 'u',
            'Ž' => 'Z',
            'ā' => 'a',
            'č' => 'c',
            'ē' => 'e',
            'ģ' => 'g',
            'ī' => 'i',
            'ķ' => 'k',
            'ļ' => 'l',
            'ņ' => 'n',
            'š' => 's',
            'ū' => 'u',
            'ž' => 'z',
        );

        // Make custom replacements
        $str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);
        // Transliterate characters to ASCII
        if ($options['transliterate']) {
            $str = str_replace(array_keys($char_map), $char_map, $str);
        }
        // Replace non-alphanumeric characters with our delimiter
        $str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);
        // Remove duplicate delimiters
        $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);
        // Truncate slug to max. characters
        $str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');
        // Remove delimiter from ends
        $str = trim($str, $options['delimiter']);
        return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
    }


}
