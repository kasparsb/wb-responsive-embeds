<?php

namespace ResponsiveEmbeds;

class Plugin extends Base {

    public function __construct() {
        parent::__construct();
        
        if (!is_admin()) {
            //add_filter('embed_oembed_html', [$this, 'embed_oembed_html']);
            add_filter('the_content', [$this, 'the_content']);
        }
    }

    public function the_content($content) {
        $safe = 0;

        $p = 0;

        while ($iframe = $this->find_iframe($content, $p)) {
            $wraped_iframe = $this->handle_iframe($iframe->content);

            $content = substr($content, 0, $iframe->start).$wraped_iframe.substr($content, $iframe->end);

            $p = $iframe->start + strlen($wraped_iframe);

            if ($safe++ > 100) {
                break;
            }
        }

        return ($p > 0 ? $this->css() : '').$content;
    }

    public function find_iframe($content, $startPos=0) {
        $pos = strpos($content, '<iframe', $startPos);
        if ($pos !== false) {
            $pos2 = strpos($content, '</iframe>', $pos);

            if ($pos2 !== false) {
                $pos2 += 9;

                return (object)[
                    'start' => $pos,
                    'end' => $pos2,
                    'length' => $pos2 - $pos,
                    'content' => substr($content, $pos, $pos2 - $pos)
                ];    
            }
        }

        return false;
    }

    public function handle_iframe($embed) {
        $pos = strpos($embed, '<iframe');
        if ($pos !== false) {

            $close_pos = strpos($embed, '>', $pos);

            $p = explode(' ', trim(substr($embed, $pos+7, $close_pos - $pos)));

            $d = $this->extract_dimensions($p);

            $r = round(($d->height / $d->width)*100);

            $style = 'padding-bottom:'.$r.'%';

            // Nolasām platumu un augstumu, lai varētu aprēķināt ratio
            return sprintf('<div style="%s" class="iframe-embed">%s</div>', $style, $embed);
        }
        return $embed;
    }

    private function extract_dimensions($parts) {
        $width = '';
        $height = '';

        foreach ($parts as $p) {
            $p = trim($p);
            
            if (substr($p, 0, 7) == 'width="') {
                $width = intval(substr($p, 7));
            }
            else if (substr($p, 0, 7) == "width='") {
                $width = intval(substr($p, 7));
            }
            else if (substr($p, 0, 6) == 'width=') {
                $width = intval(substr($p, 6));
            }
            else if (substr($p, 0, 8) == 'height="') {
                $height = intval(substr($p, 8));
            }
            else if (substr($p, 0, 8) == "height='") {
                $height = intval(substr($p, 8));
            }
            else if (substr($p, 0, 7) == 'height=') {
                $height = intval(substr($p, 7));
            }
        }

        return (object)[
            'width' => $width,
            'height' => $height
        ];
    }

    private function css() {
        return sprintf(
            '<style>%s</style>',
            file_get_contents($this->path.'assets/embed.css')
        );
    }
}