<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>,
 *               2007 Chihiro Sakatoku <csakatoku@users.sourceforge.net>,
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    Piece_Unity
 * @subpackage Piece_Unity_Component_Smarty
 * @copyright  2006-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2007 Chihiro Sakatoku <csakatoku@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id$
 * @link       http://smarty.php.net/
 * @since      File available since Release 1.0.0
 */

require_once 'Piece/Unity/Plugin/Renderer/HTML.php';
require_once 'Piece/Unity/Error.php';
require_once 'Piece/Unity/Service/Rendering/Smarty.php';

// {{{ Piece_Unity_Plugin_Renderer_Smarty

/**
 * A renderer based on Smarty.
 *
 * @package    Piece_Unity
 * @subpackage Piece_Unity_Component_Smarty
 * @copyright  2006-2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @copyright  2007 Chihiro Sakatoku <csakatoku@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @link       http://smarty.php.net/
 * @since      Class available since Release 1.0.0
 */
class Piece_Unity_Plugin_Renderer_Smarty extends Piece_Unity_Plugin_Renderer_HTML
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_smartyClassVariables = array('template_dir' => null,
                                       'compile_dir'  => null,
                                       'config_dir'   => null,
                                       'cache_dir'    => null,
                                       'plugins_dir'  => null
                                       );

    /**#@-*/

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _initialize()

    /**
     * Defines and initializes extension points and configuration points.
     */
    function _initialize()
    {
        parent::_initialize();
        $this->_addConfigurationPoint('templateExtension', '.tpl');
        $this->_addConfigurationPoint('SMARTY_DIR');
        foreach ($this->_smartyClassVariables as $point => $default) {
            $this->_addConfigurationPoint($point, $default);
        }
    }

    // }}}
    // {{{ _doRender()

    /**
     * Renders a HTML.
     *
     * @param boolean $isLayout
     * @throws PIECE_UNITY_ERROR_INVOCATION_FAILED
     */
    function _doRender($isLayout)
    {
        if (!defined('SMARTY_DIR')) {
            $SMARTY_DIR = $this->_getConfiguration('SMARTY_DIR');
            if (!is_null($SMARTY_DIR)) {
                define('SMARTY_DIR',
                       Piece_Unity_Service_Rendering_Smarty::_adjustEndingSlash($SMARTY_DIR)
                       );
            }
        }

        foreach (array_keys($this->_smartyClassVariables) as $smartyClassVariable) {
            $this->_smartyClassVariables[$smartyClassVariable] =
                $this->_getConfiguration($smartyClassVariable);
        }

        if (!$isLayout) {
            $view = $this->_context->getView();
        } else {
            $layoutDirectory = $this->_getConfiguration('layoutDirectory');
            if (!is_null($layoutDirectory)) {
                $this->_smartyClassVariables['template_dir'] = $layoutDirectory;
            }

            $layoutCompileDirectory =
                $this->_getConfiguration('layoutCompileDirectory');
            if (!is_null($layoutCompileDirectory)) {
                $this->_smartyClassVariables['compile_dir'] = $layoutCompileDirectory;
            }

            $view = $this->_getConfiguration('layoutView');
        }

        $file = str_replace('_', '/', str_replace('.', '', $view)) .
            $this->_getConfiguration('templateExtension');
        $viewElement = &$this->_context->getViewElement();

        $rendering =
            &new Piece_Unity_Service_Rendering_Smarty($this->_smartyClassVariables);
        $rendering->render($file, $viewElement);
        if (Piece_Unity_Error::hasErrors()) {
            $error = Piece_Unity_Error::pop();
            if ($error['code'] == PIECE_UNITY_ERROR_NOT_FOUND) {
                Piece_Unity_Error::push('PIECE_UNITY_PLUGIN_RENDERER_HTML_ERROR_NOT_FOUND',
                                        $error['message'],
                                        'exception',
                                        array(),
                                        $error
                                        );
                return;
            }

            Piece_Unity_Error::push(PIECE_UNITY_ERROR_INVOCATION_FAILED,
                                    "Failed to invoke the plugin [ {$this->_name} ].",
                                    'exception',
                                    array(),
                                    $error
                                    );
        }
    }

    // }}}
    // {{{ _prepareFallback()

    /**
     * Prepares another view as a fallback.
     */
    function _prepareFallback()
    {
        $config = &$this->_context->getConfiguration();

        $fallbackDirectory = $this->_getConfiguration('fallbackDirectory');
        if (!is_null($fallbackDirectory)) {
            $config->setConfiguration('Renderer_Smarty',
                                      'template_dir',
                                      $fallbackDirectory
                                      );
        }

        $fallbackCompileDirectory =
            $this->_getConfiguration('fallbackCompileDirectory');
        if (!is_null($fallbackCompileDirectory)) {
            $config->setConfiguration('Renderer_Smarty',
                                      'compile_dir',
                                      $fallbackCompileDirectory
                                      );
        }
    }

    /**#@-*/

    // }}}
}

// }}}

/*
 * Local Variables:
 * mode: php
 * coding: iso-8859-1
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 */
