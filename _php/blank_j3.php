<?php defined('_JEXEC') or die;

defined('DS') or define('DS', DIRECTORY_SEPARATOR);

require_once __DIR__.'/renderer/head_j3.php';
require_once __DIR__.'/renderer/foot_j3.php';

class blank_j3{
    
    protected $_template;
    protected $_params;
    
    
    public function __construct(JDocumentHTML $template)
    {
        if (!$template instanceof JDocumentHTML)
        {
            return false;
        }
        
        $this->_template    = $template;
        $this->_params      = $template->params;
        
        $menu = JFactory::getApplication()->getMenu();
        $activeMenu = $menu->getActive();
        $defaultMenu = $menu->getDefault();
        //detect frontpage
        $this->isFront =  $activeMenu->id == $defaultMenu->id;
        //merge menu params with template params
        jimport( 'joomla.utilities.arrayhelper' );
        $params = array_merge($activeMenu->params->toArray(), $this->_params->toArray());
        $this->params = JArrayHelper::toObject($params, 'JRegistry');
        
        // check for position
        $this->show_top = $template->countModules('top');
        $this->show_top_left = $template->countModules('top-left');
        $this->show_top_center = $template->countModules('top-center');
        $this->show_top_right = $template->countModules('top-right');
        $this->show_user1 = $template->countModules('user1');
        $this->show_user2 = $template->countModules('user2');
        $this->show_user3 = $template->countModules('user3');
        $this->show_content_header = $template->countModules('content-header');
        $this->show_content_footer = $template->countModules('content-footer');
        $this->show_left = $template->countModules('left');
        $this->show_right = $template->countModules('right');
        $this->show_bottom_left = $template->countModules('bottom-left');
        $this->show_bottom_center = $template->countModules('bottom-center');
        $this->show_bottom_right = $template->countModules('bottom-right');
        $this->show_bottom = $template->countModules('bottom');
        $this->show_bottom_socials = $template->countModules('bottom-socials');
        $this->show_footer = $template->countModules('footer');

        // size blocks
        $this->class_top_left = $this->_params->get('top_left_class', 'col-xs-12 col-sm-4 col-md-4 col-lg-4');
        $this->class_top_center = $this->_params->get('top_center_class', 'col-xs-12 col-sm-4 col-md-4 col-lg-4');
        $this->class_top_right = $this->_params->get('top_right_class', 'col-xs-12 col-sm-4 col-md-4 col-lg-4');

        $this->class_left = $this->_params->get('left_class', 'col-xs-12 col-sm-3 col-md-3 col-lg-3');
        $this->class_right = $this->_params->get('right_class', 'hidden-xs col-sm-2 col-md-2 col-lg-2');

        $this->class_bottom_left = $this->_params->get('bottom_left_class', 'col-xs-12 col-sm-4 col-md-4 col-lg-4');
        $this->class_bottom_center = $this->_params->get('bottom_center_class', 'col-xs-12 col-sm-4 col-md-4 col-lg-4');
        $this->class_bottom_right = $this->_params->get('bottom_right_class', 'col-xs-12 col-sm-4 col-md-4 col-lg-4');

        //classes for blocks
        $this->class_user1 = $this->_params->get('user1_class', 'col-xs-12 col-sm-12 col-md-12 col-lg-12');
        $this->class_user2 = $this->_params->get('user2_class', 'col-xs-12 col-sm-12 col-md-12 col-lg-12');
        $this->class_user3 = $this->_params->get('user3_class', 'col-xs-12 col-sm-12 col-md-12 col-lg-12');
    }

    public function getContentClass()
    {
        if($this->show_left)
        {
            $class_left = $this->_params->get('left_class', 'col-xs-12 col-sm-3 col-md-3 col-lg-3');
        }
        else
        {
            $class_left = 'col-xs-0 col-sm-0 col-md-0 col-lg-0';
        }

        if($this->show_right)
        {
            $class_right = $this->_params->get('right_class', 'hidden-xs col-sm-2 col-md-2 col-lg-2');
        }
        else
        {
            $class_right = 'col-xs-0 col-sm-0 col-md-0 col-lg-0';   
        }

        $max_grid_size = 12;

        $left_classes = explode(' ', $class_left);
        $right_classes = explode(' ', $class_right);
        $left_class_assoc = array();
        $right_class_assoc = array();
        $content_classes = array();
                
        foreach ($left_classes as $str)
        {
            $t_arr = preg_split('/-+(?=\\d{1,2})/', $str, 2);
            if(count($t_arr) == 2)
            {
                list($grid_class, $size) = $t_arr;
                $left_class_assoc[$grid_class] = (int) $size;
            }
        }

        foreach ($right_classes as $str)
        {
            $t_arr = preg_split('/-+(?=\\d{1,2})/', $str, 2);
            if(count($t_arr) == 2)
            {
                list($grid_class, $size) = $t_arr;
                $right_class_assoc[$grid_class] = (int) $size;
            }
        }

        $left_class_assoc = array_intersect_key($left_class_assoc, $right_class_assoc);
        foreach ($left_class_assoc as $grid_class => $size) {
            $content_size = abs($max_grid_size - $left_class_assoc[$grid_class] - $right_class_assoc[$grid_class]);
            if($content_size>0) $content_classes[] = $grid_class.'-'.$content_size;
            else $content_classes[] = $grid_class.'-'.$max_grid_size;
        }

        $content_class = implode(' ',$content_classes);
        return $content_class;
    }
    
    function createFavicon()
    {
        $favicon_image = $this->_params->get('favicon_image');
        $favicon_path = 'templates/blank_j3/favicon/';
        if(isset($favicon_image)) {
            if (!is_dir(__DIR__.'/../favicon')) {
                mkdir(__DIR__.'/../favicon', 0777, true);
            }

            $favicon_set = array(
                'apple-touch-icon' => array(
                    57,60,72,76,114,120,144,152,
                ),
                'favicon' => array(
                    16,32,96,160,196,
                ),
                'mstile' => array(
                    70,144,150,310
                ),
            );

            $return_favicons = '';
            
            // create favicons for all except Win
            foreach ($favicon_set as $name => $sizes) {
                foreach ($sizes as $size) {
                    $favicon_name = "$name-$size"."x$size.png";
                    $outFile = __DIR__."/../favicon/$favicon_name";
                    $image = new Imagick($favicon_image);
                    $image->adaptiveResizeImage($size, $size, true);
                    $image->setImageBackgroundColor('None');
                    $image->thumbnailImage($size,$size,1,'None');
                    $image->writeImage($outFile);
                    //echo "<img src='$favicon_path/$favicon_name' />";
                    
                    if($name != 'mstile')
                        $return_favicons .= "<link rel='$name' sizes='$size"."x$size' href='$favicon_path/$favicon_name'>";
                    unset($image);
                }
            }

            // create favicon for Win
            // generate XML
            $tile_bg_win = $this->_params->get('bg_win');
            $xml_data = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<browserconfig>
  <msapplication>
    <tile>
      <square70x70logo src="$favicon_path/mstile-70x70.png"/>
      <square150x150logo src="$favicon_path/mstile-150x150.png"/>
      <square310x310logo src="$favicon_path/mstile-310x310.png"/>
      <wide310x150logo src="$favicon_path/mstile-310x150.png"/>
      <TileColor>$tile_bg_win</TileColor>
    </tile>
  </msapplication>
</browserconfig>
XML;
            file_put_contents(__DIR__."/../favicon/browserconfig.xml", $xml_data);
            $return_favicons .= "<meta name='msapplication-TileColor' content='$tile_bg_win'>";
            $return_favicons .= "<meta name='msapplication-TileImage' content='$favicon_path/mstile-144x144.png'>";
            $return_favicons .= "<meta name='msapplication-config' content='$favicon_path/browserconfig.xml'>";
            
            // generate favicon.ico
            $image = new Imagick($favicon_image);
            $image->cropThumbnailImage(32,32);
            $image->setFormat('ico');
            $image->writeImage("$favicon_path/favicon.ico");
            unset($image);

            $return_favicons .= "<link rel='shortcut icon' href='$favicon_path/favicon.ico'>";
            return $return_favicons;
        }
    }
}

?>
