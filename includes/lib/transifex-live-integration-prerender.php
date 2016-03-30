<?php

class Transifex_Live_Integration_Prerender
{

	private $prerender_url;
	private $override_prerender_check;
	
    public function __construct($prerender_url, $override_prerender_check) 
    {
        Plugin_Debug::logTrace();
		$this->prerender_url = $prerender_url;
		$this->override_prerender_check = ($override_prerender_check)?true:false;
    }

    function wp_head_hook() 
    {
        Plugin_Debug::logTrace();
        $status = '';
        if (is_404() ) {
            $status .= <<< STATUS
<meta name="prerender-status-code" content="404">\n
STATUS;
        }
        echo $status;
    }

    function wp_headers_hook( $headers ) 
    {
        Plugin_Debug::logTrace();
        $headers['X-PreRender-Req'] = 'TRUE';
        return $headers;
    }

    static public function prerender_check( $req_user_agent, $req_escaped_fragment,
        $bot_types, $whitelist_names 
    ) {
        Plugin_Debug::logTrace();

        $bot_types_escaped = addcslashes($bot_types, '/');
        $whitelist_names_escaped = addcslashes($whitelist_names, '/');

        $is_bot = self::is_bot_type($req_user_agent, $bot_types_escaped);
        $is_whitelisted = ($is_bot) ? true : self::is_whitelist_name($req_user_agent, $whitelist_names_escaped);
        $has_escaped_fragment = ($is_whitelisted) ? true : ($req_escaped_fragment) ? true : false;
        $prerender_ok = ($has_escaped_fragment) ? true : self::is_prerender_req();

        return $prerender_ok;
    }


    function callback( $buffer ) 
    {
        global $wp;
        $output = $buffer;
        $page_url = home_url($wp->request);
        $page_url = rtrim($page_url, '/') . '/';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->prerender_url . $page_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $response = curl_exec($ch);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        if ($response === false ) {
            $error = curl_error($ch);
            // write to db??
        } else {
            if (strpos($header, 'X-PreRender-Req: TRUE') || $this->override_prerender_check ) {
                $output = $body;
            }
        }
        curl_close($ch);
        return $output;
    }

    function after_setup_theme_hook() 
    {
        ob_start([$this, 'callback' ]);
    }

    function shutdown_hook() 
    {
        ob_end_flush();
    }

}
