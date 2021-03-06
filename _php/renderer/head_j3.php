<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Document
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * JDocument head renderer
 *
 * @package     Joomla.Platform
 * @subpackage  Document
 * @since       11.1
 */
class JDocumentRendererHeadJ3 extends JDocumentRenderer
{
	/**
	 * Renders the document head and returns the results as a string
	 *
	 * @param   string  $head     (unused)
	 * @param   array   $params   Associative array of values
	 * @param   string  $content  The script
	 *
	 * @return  string  The output of the script
	 *
	 * @since   11.1
	 *
	 * @note    Unused arguments are retained to preserve backward compatibility.
	 */
	public function render($head, $params = array(), $content = null)
	{
		return $this->fetchHead($this->_doc);
	}

	/**
	 * Generates the head HTML and return the results as a string
	 *
	 * @param   JDocument  $document  The document for which the head will be created
	 *
	 * @return  string  The head hTML
	 *
	 * @since   11.1
	 */
	public function fetchHead($document)
	{
		// Convert the tagids to titles
		if (isset($document->_metaTags['standard']['tags']))
		{
			$tagsHelper = new JHelperTags;
			$document->_metaTags['standard']['tags'] = implode(', ', $tagsHelper->getTagNames($document->_metaTags['standard']['tags']));
		}

		// Trigger the onBeforeCompileHead event
		$app = JFactory::getApplication();
		$app->triggerEvent('onBeforeCompileHead');

		// Get line endings
		$lnEnd = $document->_getLineEnd();
		$tab = $document->_getTab();
		$tagEnd = ' />';
		$buffer = '';

		// Generate charset when using HTML5 (should happen first)
		if ($document->isHtml5())
		{
			$buffer .= $tab . '<meta charset="' . $document->getCharset() . '" />' . $lnEnd;
		}

		// Generate base tag (need to happen early)
		$base = $document->getBase();
		if (!empty($base))
		{
			$buffer .= $tab . '<base href="' . $document->getBase() . '" />' . $lnEnd;
		}

		// Generate META tags (needs to happen as early as possible in the head)
		foreach ($document->_metaTags as $type => $tag)
		{
			foreach ($tag as $name => $content)
			{
				if ($type == 'http-equiv' && !($document->isHtml5() && $name == 'content-type'))
				{
					$buffer .= $tab . '<meta http-equiv="' . $name . '" content="' . htmlspecialchars($content) . '" />' . $lnEnd;
				}
				elseif ($type == 'standard' && !empty($content))
				{
					$buffer .= $tab . '<meta name="' . $name . '" content="' . htmlspecialchars($content) . '" />' . $lnEnd;
				}
			}
		}

		// Don't add empty descriptions
		$documentDescription = $document->getDescription();
		if ($documentDescription)
		{
			$buffer .= $tab . '<meta name="description" content="' . htmlspecialchars($documentDescription) . '" />' . $lnEnd;
		}

		// Don't add empty generators
		$generator = $document->getGenerator();
		if ($generator)
		{
			$buffer .= $tab . '<meta name="generator" content="' . htmlspecialchars($generator) . '" />' . $lnEnd;
		}

		$buffer .= $tab . '<title>' . htmlspecialchars($document->getTitle(), ENT_COMPAT, 'UTF-8') . '</title>' . $lnEnd;

		// Generate link declarations
		foreach ($document->_links as $link => $linkAtrr)
		{
			$buffer .= $tab . '<link href="' . $link . '" ' . $linkAtrr['relType'] . '="' . $linkAtrr['relation'] . '"';
			if ($temp = JArrayHelper::toString($linkAtrr['attribs']))
			{
				$buffer .= ' ' . $temp;
			}
			$buffer .= ' />' . $lnEnd;
		}

		// Generate stylesheet

		$css = '';
		$css_path = JPATH_THEMES.'/'.$document->template.'/css/';
		$params = $document->params;
		
		
		$css .= 'body {'
		    .'background-color:'  . $params->get('body_background_color', '#fff').';'
		    .($params->get('body_background_image', '') ? 'background-image: url(/' . $params->get('body_background_image', '').');' : '')
		    .'color:'             . $params->get('body_font_color', '#222').';'
		    .'font-size:'         . $params->get('body_font_size', '14px').'}';
		
		$css .= 'a{'
		    .'color:'             . $params->get('link_color', '#e67e22').'}';
		
		$css .= 'a:hover{'
		    .'color:'             . $params->get('link_hover_color', '#ffa348').'}';
		
		
		
		$css .= '.mainmenu .nav a{'
		    .'color:'             . $params->get('mainmenu_font_color', '#222').' !important;'
		    .'font-size:'         . $params->get('menu_font_size', '16px').'}';
		
		
		$css .= '.mainmenu .nav li.active a, .mainmenu .nav li a:hover{'
		    .'background-color:'  . $params->get('mainmenu_active_background_color', '#fff').';'
		    .'color:'             . $params->get('mainmenu_active_font_color', '#fff').' !important}';
		
		
		if ($params->get('full_width_head', 0))
		{
			$css .= '#top-blocks{'
			.'background-color:'  . $params->get('head_background_color', '#222').';'
			.'color:'		. $params->get('head_font_color', '#fff').'}';
		} else {
			$css .= '#top-blocks > .container{'
			.'background-color:'  . $params->get('head_background_color', '#222').';'
			.'color:'		. $params->get('head_font_color', '#fff').'}';
			
		}
		
		if ($params->get('full_width_menu', 0))
		{
			$css .= '.mainmenu {'
			.'background-color:'  . $params->get('mainmenu_background_color', '#e67e22').'}';
			
		} else {
			$css .= '.mainmenu > .container{'
			.'background-color:'  . $params->get('mainmenu_background_color', '#e67e22').'}';
			
		}
		
		if ($params->get('full_width_footer', 0))
		{
			$css .= 'footer{'
			.'background-color:'  . $params->get('footer_background_color', '#222').';'
			.'color:'             . $params->get('footer_font_color', '#fff').'}';
		} else {
			$css .= 'footer > .container{'
			.'background-color:'  . $params->get('footer_background_color', '#222').';'
			.'color:'             . $params->get('footer_font_color', '#fff').'}';
			
		}
		
		
		
		$css .= 'h1{font-size:'  . $params->get('h1_font_size', '1.8em').'}';
		$css .= 'h2{font-size:'  . $params->get('h2_font_size', '1.6em').'}';
		$css .= 'h3{font-size:'  . $params->get('h3_font_size', '1.4em').'}';
		$css .= 'h4{font-size:'  . $params->get('h4_font_size', '1.3em').'}';
		$css .= 'h5{font-size:'  . $params->get('h5_font_size', '1.2em').'}';
		
		$css = $this->_compress($css);
		
		file_put_contents($css_path.'global.css', $css);
		
		$rel_path = 'templates/'.$document->template.'/css/global.css';
		
		$document->addStyleSheet($rel_path);
		
		if ($document->params->get('compile_css', 0))
		{
			ob_start();
			
			foreach ($document->_styleSheets as $strSrc => $strAttr)
			{
				$cssFile = JPATH_BASE.DS.$strSrc;
				
				if (file_exists($cssFile))
				{
				    include($cssFile);
				}
			}
			
			foreach ($document->_style as $type => $content)
			{
				echo $content;
			}
			
			$oc = ob_get_contents();
			ob_end_clean();
			
			$css = $this->_compress($oc);
			
			file_put_contents($css_path.'all.css', $css);
			
			$rel_path = 'templates/'.$document->template.'/css/all.css';
			
			$buffer .= $tab . '<link rel="stylesheet" href="' . $rel_path . '"';
			$buffer .= $tagEnd . $lnEnd;
			
		} else {
			
			foreach ($document->_styleSheets as $strSrc => $strAttr)
			{
				$buffer .= $tab . '<link rel="stylesheet" href="' . $strSrc . '"';
			
				if (!is_null($strAttr['mime']) && (!$document->isHtml5() || $strAttr['mime'] != 'text/css'))
				{
					$buffer .= ' type="' . $strAttr['mime'] . '"';
				}
			
				if (!is_null($strAttr['media']))
				{
					$buffer .= ' media="' . $strAttr['media'] . '"';
				}
			
				if ($temp = JArrayHelper::toString($strAttr['attribs']))
				{
					$buffer .= ' ' . $temp;
				}
			
				$buffer .= $tagEnd . $lnEnd;
			}
			
		}
		
		
		if (!$document->params->get('disable_scripts', 0))
		{
			// Generate script file links
			foreach ($document->_scripts as $strSrc => $strAttr)
			{
				$buffer .= $tab . '<script src="' . $strSrc . '"';
				$defaultMimes = array(
					'text/javascript', 'application/javascript', 'text/x-javascript', 'application/x-javascript'
				);
	
				if (!is_null($strAttr['mime']) && (!$document->isHtml5() || !in_array($strAttr['mime'], $defaultMimes)))
				{
					$buffer .= ' type="' . $strAttr['mime'] . '"';
				}
	
				if ($strAttr['defer'])
				{
					$buffer .= ' defer="defer"';
				}
	
				if ($strAttr['async'])
				{
					$buffer .= ' async="async"';
				}
	
				$buffer .= '></script>' . $lnEnd;
			}
	
			// Generate script declarations
			foreach ($document->_script as $type => $content)
			{
				$buffer .= $tab . '<script type="' . $type . '">' . $lnEnd;
	
				// This is for full XHTML support.
				if ($document->_mime != 'text/html')
				{
					$buffer .= $tab . $tab . '<![CDATA[' . $lnEnd;
				}
	
				$buffer .= $content . $lnEnd;
	
				// See above note
				if ($document->_mime != 'text/html')
				{
					$buffer .= $tab . $tab . ']]>' . $lnEnd;
				}
				$buffer .= $tab . '</script>' . $lnEnd;
			}
	
			// Generate script language declarations.
			if (count(JText::script()))
			{
				$buffer .= $tab . '<script type="text/javascript">' . $lnEnd;
				$buffer .= $tab . $tab . '(function() {' . $lnEnd;
				$buffer .= $tab . $tab . $tab . 'var strings = ' . json_encode(JText::script()) . ';' . $lnEnd;
				$buffer .= $tab . $tab . $tab . 'if (typeof Joomla == \'undefined\') {' . $lnEnd;
				$buffer .= $tab . $tab . $tab . $tab . 'Joomla = {};' . $lnEnd;
				$buffer .= $tab . $tab . $tab . $tab . 'Joomla.JText = strings;' . $lnEnd;
				$buffer .= $tab . $tab . $tab . '}' . $lnEnd;
				$buffer .= $tab . $tab . $tab . 'else {' . $lnEnd;
				$buffer .= $tab . $tab . $tab . $tab . 'Joomla.JText.load(strings);' . $lnEnd;
				$buffer .= $tab . $tab . $tab . '}' . $lnEnd;
				$buffer .= $tab . $tab . '})();' . $lnEnd;
				$buffer .= $tab . '</script>' . $lnEnd;
			}
		}
		
		if (!$document->params->get('compile_css', 0))
		{
			// Generate stylesheet declarations
			foreach ($document->_style as $type => $content)
			{
				$buffer .= $tab . '<style type="' . $type . '">' . $lnEnd;
	
				// This is for full XHTML support.
				if ($document->_mime != 'text/html')
				{
					$buffer .= $tab . $tab . '<![CDATA[' . $lnEnd;
				}
	
				$buffer .= $content . $lnEnd;
	
				// See above note
				if ($document->_mime != 'text/html')
				{
					$buffer .= $tab . $tab . ']]>' . $lnEnd;
				}
				$buffer .= $tab . '</style>' . $lnEnd;
			}
		}
		

		foreach ($document->_custom as $custom)
		{
			$buffer .= $tab . $custom . $lnEnd;
		}

		return $buffer;
	}
	
	protected function _compress($buffer) {
	/* remove comments */
	    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
	/* remove tabs, spaces, new lines, etc. */        
	    $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
	/* remove unnecessary spaces */        
	    $buffer = str_replace('{ ', '{', $buffer);
	    $buffer = str_replace(' }', '}', $buffer);
	    $buffer = str_replace('; ', ';', $buffer);
	    $buffer = str_replace(', ', ',', $buffer);
	    $buffer = str_replace(' {', '{', $buffer);
	    $buffer = str_replace('} ', '}', $buffer);
	    $buffer = str_replace(': ', ':', $buffer);
	    $buffer = str_replace(' ,', ',', $buffer);
	    $buffer = str_replace(' ;', ';', $buffer);
		
	    return $buffer;
	}
}
