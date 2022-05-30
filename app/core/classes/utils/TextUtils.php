<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE
define('REGEX_FIELD_SUBSTITUTION', '/\{\$([^\$]+)\$\}/i' );

class TextUtils
{
    const REGEX_SHARE = '/(\\\\)(\\\\[^<\s]+){2,}(\\\\)?/im';

    public static function breakLongWords( $content, $maxLength = 80 ) {
        return join('', array_map(
            function($line) use ($maxLength) {
                return mb_strlen($line) > $maxLength && strpos($line, 'src="') === false && strpos($line, 'href="') === false
                    ? join('<', array_map(
                        function($line) use ($maxLength) {
                            return TextUtils::mb_wordwrap($line, $maxLength, "\n", true);
                        },
                        preg_split('/</u', $line)
                      ))
                    : $line;
            },
            preg_split('/\040+/u', $content)
        ));
    }

    public static function mb_wordwrap($str, $width = 75, $break = "\n", $cut = false) {
        $lines = explode($break, $str);
        foreach ($lines as &$line) {
            $line = rtrim($line);
            if (mb_strlen($line) <= $width)
                continue;
            $words = explode(' ', $line);
            $line = '';
            $actual = '';
            foreach ($words as $word) {
                if (mb_strlen($actual.$word) <= $width)
                    $actual .= $word.' ';
                else {
                    if ($actual != '')
                        $line .= rtrim($actual).$break;
                    $actual = $word;
                    if ($cut) {
                        while (mb_strlen($actual) > $width) {
                            $line .= mb_substr($actual, 0, $width).$break;
                            $actual = mb_substr($actual, $width);
                        }
                    }
                    $actual .= ' ';
                }
            }
            $line .= trim($actual);
        }
        return implode($break, $lines);
    }

    public static function versionToString( $versionString ) {
        return join('',array_map(
            function ($value) {
                return str_pad($value, 6, "0", STR_PAD_LEFT);
            },
            array_pad(
                preg_split('/\./', $versionString), 4, "0"
            )
        ));
    }

    public static function removeHtmlEntities( $text ) {
        return html_entity_decode(
                    htmlentities(
                        str_replace("&nbsp;", " ",$text), ENT_COMPAT | ENT_HTML401, APP_ENCODING
                    ), ENT_COMPAT | ENT_HTML401, APP_ENCODING
                );
    }

    public static function stripAnyTags( $text ) {
        $text = preg_replace(
            '/(?:<|&lt;)\/?(a|p|div|br|h[\d]+|span|ul|ol|li|strong|font|br|hr|table|tr|td|th|tbody|colgroup|col|thead|button|iframe|html|body|link|script|style|i|b|del|strike|em|code|pre|\!\-\-)\s*[^>]*?(?:>|&gt;)/i',
                '', self::removeHtmlEntities($text));
        $text = preg_replace('/<img\s+[^>]+>/i', '', $text);
        return trim($text, ' '.PHP_EOL);
    }

    public static function getCleansedHtml( $body )
   {
        $body = preg_replace('/<!--[^-]+-->/', '', $body);
        $body = self::_getCleansedHtml(
            $body,
            array(
                '/<link[^>]*>/i',
                '/<\/link>/i',
                '/<script[^>]*>/i',
                '/<\/script>/i',
                '/<style[^>]*>/i',
                '/<\/style>/i',
                '/<base[^>]*>/i',
                '/<\/base>/i'
            )
        );

        $body = preg_replace(
            array(
                '/<o:[A-Za-z]>/',
                '/<\/o:[A-Za-z]>/'
            ),
            array (
                '',
                ''
            ), $body);

        return $body;
    }

    protected function _getCleansedHtml( $body, array $tags )
    {
        $replaceTags = array();
        foreach( array_keys($tags) as $index ) {
            $replaceTags[] = $index % 2 == 0 ? '[skip-style]' : '[/skip-style]';
        }
        $body = preg_replace( $tags, $replaceTags, $body);

        $lines = preg_split('/\[skip\-style\]/i', $body);
        $cleansedBody = array_shift($lines);
        foreach( $lines as $line ) {
            $parts = preg_split('/\[\/skip\-style\]/i', $line);
            $cleansedBody .= array_pop($parts);
        }
        return $cleansedBody;
    }

    public static function getUnstyledHtml( $content )
    {
        return preg_replace(
            array (
                '#(<[a-z ]+)(style=("|\')(.*?)("|\'))([^>]*>)#',
                '#(<[a-z ]+)(class=("|\')(.*?)("|\'))([^>]*>)#',
                '/(<table[^>]+)/i',
                '/(<\/?span[^>]*>)/i',
                '/(&nbsp;|\xC2\xA0)/i'
            ),
            array (
                '\1 was-stl="\4" \6',
                '\1 was-cls="\4" \6',
                '$1 border="1"',
                '',
                ' '
            ), $content);
    }

    public static function getValidHtml( $body )
    {
        $text = preg_replace('/<meta\s+[^>]+>/i', '', $body);
        if ( mb_stripos($text, '<body>') === false ) {
            $text = '<body>'.$text.'</body>';
        }
        else {
            $text = array_pop(preg_split('/<body>/i', $text));
            $text = array_shift(preg_split('/<\/body>/i', $text));
            $text = '<body>'.$text.'</body>';
        }
        $text = '<?xml version="1.0" encoding="'.APP_ENCODING.'"?>'.$text;

        $was_state = libxml_use_internal_errors(true);
        $doc = new \DOMDocument("1.0", APP_ENCODING);
        if ( $doc->loadHTML($text) ) {
            $bodyElement = $doc->getElementsByTagName('body');
            if ( $bodyElement->length > 0 ) {
                $text = $doc->saveHTML($bodyElement->item(0));
                $body = preg_replace(
                    array(
                        '/<tr>[\s\r\n]*<\/tr>/i',
                        '/<tr>[\s\r\n]*<table/i',
                        '/<\/table>[\s\r\n]*<\/tr>/i',
                        '/<\/?body>/i'
                    ),
                    array (
                        '<tr><td></td></tr>',
                        '<tr><td><table',
                        '</table></td></tr>',
                        ''
                    ), $text);
            }
            else {
                $body = htmlentities($text);
            }
        }
        else {
            $body = htmlentities($text);
        }
        libxml_clear_errors();
        libxml_use_internal_errors($was_state);

        return $body;
    }

    public static function checkHtml( $body )
    {
        $text = preg_replace('/<meta\s+[^>]+>/i', '', $body);
        if ( mb_stripos($text, '<body>') === false ) {
            return "";
        }
        else {
            $text = array_pop(preg_split('/<html>/i', $text));
            $text = array_shift(preg_split('/<\/html>/i', $text));
            $text = '<html>'.$text.'</html>';
        }
        $text = '<?xml version="1.0" encoding="'.APP_ENCODING.'"?>'.$text;

        $was_state = libxml_use_internal_errors(true);
        $doc = new \DOMDocument("1.0", APP_ENCODING);
        if ( $doc->loadHTML($text) ) {
            $bodyElement = $doc->getElementsByTagName('body');
            if ( $bodyElement->length > 0 ) {
                return $doc->saveHTML($bodyElement->item(0));
            }
        }
        libxml_clear_errors();
        libxml_use_internal_errors($was_state);

        return "";
    }

    public function EscapeShellArgument( $text ) {
        return preg_replace('/`/','\\`',trim(escapeshellarg($text),'"\''));
    }

    public function getXmlString( $text ) {
        return htmlentities(
            preg_replace("/[^\\x{0009}\\x{000A}\\x{000D}\\x{0020}-\\x{D7FF}\\x{E000}-\\x{FFFD}]/u", "",
                mb_convert_encoding(
                    $text, APP_ENCODING, APP_ENCODING
                ) // remove non-utf characters
            ), // remove non-xml characters
            ENT_XML1, APP_ENCODING, false
        ); // escape allowed UTF-characters
    }

    public function decodeHtml( $text ) {
        $text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML401, APP_ENCODING );
        $text = preg_replace('/\x{00A0}/u', ' ', $text);
        return $text;
    }

    public function getAlphaNumericPunctuationString( $text ) {
        $text = preg_replace( "/[^\p{L}|\p{N}\+\-\&\(\)\=\@\/\.,:;_\{\}]+/u", " ", $text );
        return preg_replace( "/[\p{Z}]{2,}/u", " ", $text );
    }

    public function getAlphaNumericString( $text ) {
        $text = preg_replace( "/[^\p{L}|\p{N}\-\_\.\&\@]+/u", " ", $text );
        return preg_replace( "/[\p{Z}]{2,}/u", " ", $text );
    }

    public function getFileSafeString( $text ) {
        return preg_replace('/\s+/', '_', self::getAlphaNumericString($text));
    }

    public static function getWords( $text, $wordsCount = 1 ) {
        $items = preg_split('/\s+/', $text);
        $result = join(' ', array_slice($items, 0, $wordsCount));
        return $result != $text ? $result . '...' : $result;
    }

    public static function encodeImage( $filePath )
    {
        if ( file_exists(realpath($filePath)) ) {
            $maxImageWidth = 1024;
            if ( filesize($filePath) > 1048576 && class_exists('Imagick') ) {
                try {
                    $imagick = new Imagick(realpath($filePath));
                    $geometry = $imagick->getImageGeometry();
                    if ( $geometry['width'] > $maxImageWidth ) {
                        $imagick->scaleImage($maxImageWidth, 0, false);
                    }
                    return base64_encode($imagick->getImageBlob());
                }
                catch( Exception $e ) {
                    return base64_encode(file_get_contents($filePath));
                }
            }
            else {
                return base64_encode(file_get_contents($filePath));
            }
        }
        else {
            $curl = CurlBuilder::getCurl();
            curl_setopt($curl, CURLOPT_URL, $filePath);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($curl, CURLOPT_HTTPGET, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $result = curl_exec($curl);
            curl_close($curl);
            return base64_encode($result);
        }
    }

    protected static function getHashIdsInstance() {
        return new Hashids\Hashids(
            md5(INSTALLATION_UID.CUSTOMER_UID), 4, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
        );
    }

    public static function buildIds( $ids )
    {
        $ids = preg_split('/[,-]/', join(',', $ids));
        return self::getHashIdsInstance()->encode(
            array_values(
                array_filter(
                    array_map(function($value) {
                        return intval($value, 10);
                    }, $ids),
                    function($value) {
                        return $value > 0;
                    }
                )
            )
        );
    }

    public static function parseIds( $text )
    {
        if ( is_array($text) ) {
            return array_filter(
                $text,
                function($value) {
                    return is_numeric($value) && $value >= 0;
                }
            );
        }
        if ( is_numeric($text) && $text > 0 ) return array($text);

        try {
            $ids = self::getHashIdsInstance()->decode($text);
            if ( count($ids) > 0 ) return $ids;

            $query = self::secureData($text, 'decrypt');
            if ( stripos($query, 'select ') !== false ) {
                return array_map(
                    function( $row ) {
                        return $row[0];
                    },
                    DAL::Instance()->QueryAllRows($query)
                );
            }
        }
        catch( \Exception $e ) {
        }

        return array_unique(
            array_filter(
                preg_split('/[,-]/', trim($text, '-,') ),
                function($value) {
                    return is_numeric($value) && $value >= 0;
                }
            )
        );
    }

    public static function parseItems( $text, $separators = ',' )
    {
        if ( is_array($text) ) {
            return array_filter($text, function($value) {
                return $value != '';
            });
        }

        if ( is_numeric($text) && $text > 0 ) return array(trim($text));

        return array_unique(
            array_filter(
                array_map(
                    function($item) {
                        return trim($item);
                    },
                    preg_split('/['.preg_quote($separators).']/', trim($text, $separators) )
                ),
                function($value) {
                    return $value != '';
                }
            )
        );
    }

    public static function parseFilterItems( $text, $separators = ',' )
    {
        return array_diff(
            self::parseItems($text, $separators),
            array(
                'none', 'all', 'any', 'hide'
            )
        );
    }

    public static function pathToUnixStyle($path) {
        return str_replace("\\", "/", realpath($path));
    }

    public static function removeHtmlTag( $tagName, $content )
    {
        $beforeTag = preg_split('/<'.$tagName.'[^>]*>/i', $content);
        foreach( $beforeTag as $index => $text ) {
            $afterTag = preg_split('/<\/'.$tagName.'>/', $text);
            if ( count($afterTag) > 1 ) {
                array_shift($afterTag);
            }
            $beforeTag[$index] = join('', $afterTag);
        }
        return join('', $beforeTag);
    }

    public static function skipHtmlTag( $tagName, $content )
    {
        return preg_replace('/<\/?' .$tagName. '[^>]*>/', '', $content);
    }

    public static function checkDatabaseColumnName( $text ) {
        return preg_match("/^[a-zA-Z][a-zA-Z0-9\_]+$/i", $text);
    }

    public static function checkReferenceName( $text ) {
        return preg_match("/^[a-zA-Z0-9\_\s]+$/i", $text);
    }

    public static function getRandomPassword() {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $password = substr(str_shuffle($chars), 0, 12);
        return $password;
    }

    public static function shrinkLongShare( $match )
    {
        return '<a target="_blank" href="file:'.str_replace('\\', '/', $match[0]).'">'.$match[0].'</a>';
    }

    public static function htmlSpecialCharsExceptImage( $text )
    {
        $text = preg_replace('/<img\s([^>]+)>/i', 'INTERNAL_TAG_IMG_START \\1 INTERNAL_TAG_IMG_END', $text);
        $text = htmlspecialchars($text, ENT_HTML401);
        $text = preg_replace('/INTERNAL_TAG_IMG_START/', '<img ', $text);
        $text = preg_replace('/INTERNAL_TAG_IMG_END/', '>', $text);
        return $text;
    }

    public static function getNormalizedString( $text, $languageCode = 'en' )
    {
        return join(' ', array_diff(
            preg_split('/\s+/u', self::getAlphaNumericString(self::stripAnyTags(mb_strtolower($text)))),
            JsonWrapper::decode(
                file_get_contents(SERVER_ROOT_PATH . 'lang/ru/stopwords.json')
            )
        ));
    }

    public static function hasStopWords( $text, $languageCode = 'en' )
    {
        return count(array_intersect(
            preg_split('/\s+/u', self::stripAnyTags(mb_strtolower($text))),
            JsonWrapper::decode(
                file_get_contents(SERVER_ROOT_PATH . 'lang/'.strtolower($languageCode).'/stopwords.json')
            )
        )) > 0;
    }

    public static function generateCodeName()
    {
        $animals = array( "Aardvark", "Albatross", "Alligator", "Alpaca", "Ant", "Anteater", "Antelope", "Ape", "Armadillo", "Baboon", "Badger", "Barracuda", "Bat", "Bear", "Beaver", "Bee", "Bison", "Boar", "Buffalo", "Butterfly", "Camel", "Capybara", "Cassowary", "Caterpillar", "Cattle", "Cheetah", "Chicken", "Chimpanzee", "Chinchilla", "Chough", "Clam", "Cobra", "Cockroach", "Cod", "Cormorant", "Coyote", "Crab", "Crane", "Crocodile", "Crow", "Curlew", "Deer", "Dinosaur", "Dog", "Dogfish", "Dolphin", "Donkey", "Dotterel", "Dove", "Dragonfly", "Duck", "Dugong", "Dunlin", "Eagle", "Echidna", "Eel", "Eland", "Elephant", "Elephant", "Elk", "Emu", "Falcon", "Ferret", "Finch", "Fish", "Flamingo", "Fly", "Fox", "Frog", "Gaur", "Gazelle", "Gerbil", "Giant", "Giraffe", "Gnat", "Gnu", "Goat", "Goose", "Goldfinch", "Goldfish", "Gorilla", "Goshawk", "Grasshopper", "Grouse", "Guanaco", "Guinea", "Guinea", "Gull", "Hamster", "Hare", "Hawk", "Migrating", "Hedgehog", "Heron", "Herring", "Hippopotamus", "Hornet", "Horse", "Human", "Hummingbird", "Hyena", "Ibex", "Ibis", "Jackal", "Jaguar", "Jay", "Jellyfish", "Kangaroo", "Kingfisher", "Koala", "Komodo", "Kookabura", "Kouprey", "Kudu", "Lapwing", "Lark", "Lemur", "Leopard", "Lion", "Llama", "Lobster", "Locust", "Loris", "Louse", "Lyrebird", "Magpie", "Mallard", "Also", "Manatee", "Mandrill", "Mantis", "Marten", "Meerkat", "Mink", "Mole", "Mongoose", "Monkey", "Moose", "Mouse", "Mosquito", "Mule", "Narwhal", "Newt", "Nightingale", "Octopus", "Okapi", "Opossum", "Oryx", "Ostrich", "Otter", "Owl", "Oyster", "Panther", "Parrot", "Partridge", "Peafowl", "Pelican", "Penguin", "Pheasant", "Pig", "Also", "Pigeon", "Polar", "Pony", "Porcupine", "Porpoise", "Prairie", "Quail", "Quelea", "Quetzal", "Rabbit", "Raccoon", "Rail", "Ram", "Also", "Rat", "Raven", "Red", "Red", "Reindeer", "Rhinoceros", "Rook", "Salamander", "Salmon", "Sand", "Sandpiper", "Sardine", "Scorpion", "Sea", "Sea", "Seahorse", "Seal", "Shark", "Sheep", "Also", "Shrew", "Skunk", "Snail", "Snake", "Sparrow", "Spider", "Spoonbill", "Squid", "Squirrel", "Starling", "Stingray", "Stinkbug", "Stork", "Swallow", "Swan", "Tapir", "Tarsier", "Termite", "Tiger", "Toad", "Trout", "Turkey", "Turtle", "Viper", "Vulture", "Wallaby", "Walrus", "Wasp", "Water", "Weasel", "Whale", "Wolf", "Wolverine", "Wombat", "Woodcock", "Woodpecker", "Worm", "Wren", "Yak", "Zebra");
        $fruits = array("Apple", "Apricot", "Avocado", "Banana", "Breadfruit", "Bilberry", "Blackberry", "Blackcurrant", "Blueberry", "Boysenberry", "Cantaloupe", "Currant", "Cherry", "Cherimoya", "Cloudberry", "Coconut", "Cranberry", "Cucumber", "Damson", "Date", "Dragonfruit", "Durian", "Eggplant", "Elderberry", "Feijoa", "Fig", "Goji", "Gooseberry", "Grape", "Raisin", "Grapefruit", "Guava", "Huckleberry", "Honeydew", "Jackfruit", "Jambul", "Jujube", "Kiwi", "Kumquat", "Lemon", "Lime", "Loquat", "Lychee", "Mango", "Marionberry", "Melon", "Cantaloupe", "Honeydew", "Watermelon", "Mulberry", "Nectarine", "Nut", "Olive", "Orange", "Clementine", "Mandarine", "Tangerine", "Papaya", "Passionfruit", "Peach", "Pepper", "Pear", "Persimmon", "Physalis", "Plum", "Pineapple", "Pomegranate", "Pomelo", "Quince", "Raspberry", "Rambutan", "Redcurrant", "Salalberry", "Salmonberry", "Satsuma", "Starfruit", "Strawberry", "Tamarillo", "Tomato");
        $nouns = array_merge($animals, $fruits);
        srand();
        return strtolower($nouns[rand(0, count($nouns) - 1)]);
    }

    public static function generatePassword() {
        return substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.-+{}[]()"),0,16);
    }

    public static function parseAttributeFilter( $value ) {
        $attributeFilters = array();
        foreach( explode(';', $value) as $filter ) {
            list($filterName, $filterValue) = explode(':', $filter);
            $attributeFilters[$filterName] = trim($filterValue);
        }
        return $attributeFilters;
    }

    public static function getPlantUMLUrl( $uml )
    {
        $uml = "@startuml".PHP_EOL."scale max 2048 width".PHP_EOL. $uml . "@enduml";
        return trim(EnvironmentSettings::getPlantUMLServer(), "/ ").
                    '/plantuml/img/'.encode64(gzdeflate($uml, 9));
    }

    public static function isNullValue( $value ) {
        return !is_array($value) && (trim($value) == '' || strtolower(trim($value)) == "null");
    }

    public static function isValueDefined( $value ) {
        return trim($value) != '';
    }

    public static function secureData( $string, $method = 'encrypt' )
    {
        $encrypt_method = "AES-256-CBC";
        $secret_key = md5(\EnvironmentSettings::getServerSalt());

        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', INSTALLATION_UID), 0, 16);

        if ( $method == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }

        return $output;
    }
}