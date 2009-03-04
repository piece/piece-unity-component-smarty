<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006-2009 KUBO Atsuhiro <kubo@iteman.jp>,
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
 * @copyright  2006-2009 KUBO Atsuhiro <kubo@iteman.jp>
 * @copyright  2007 Chihiro Sakatoku <csakatoku@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    GIT: $Id$
 * @since      File available since Release 1.0.0
 */

require_once realpath(dirname(__FILE__) . '/../../../../prepare.php');
require_once 'Piece/Unity/Plugin/Renderer/HTML/CompatibilityTests.php';
require_once 'Piece/Unity/Config.php';
require_once 'Piece/Unity/Context.php';
require_once 'Piece/Unity/Plugin/Renderer/Smarty.php';

// {{{ Piece_Unity_Plugin_Renderer_SmartyTestCase

/**
 * Some tests for Piece_Unity_Plugin_Renderer_Smarty.
 *
 * @package    Piece_Unity
 * @subpackage Piece_Unity_Component_Smarty
 * @copyright  2006-2009 KUBO Atsuhiro <kubo@iteman.jp>
 * @copyright  2007 Chihiro Sakatoku <csakatoku@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      Class available since Release 1.0.0
 */
class Piece_Unity_Plugin_Renderer_SmartyTestCase extends Piece_Unity_Plugin_Renderer_HTML_CompatibilityTests
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_target = 'Smarty';

    /**#@-*/

    /**#@+
     * @access public
     */

    function testLoadingPlugins()
    {
        $context = &Piece_Unity_Context::singleton();
        $context->setView("{$this->_target}LoadingPlugins");
        $viewElement = &$context->getViewElement();
        $viewElement->setElement('content', 'This is a dynamic content.');
        $config = &$this->_getConfig();
        $context->setConfiguration($config);

        $this->assertEquals('Hello World', trim($this->_render()));
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    function &_getConfig()
    {
        $config = &new Piece_Unity_Config();
        $config->setConfiguration('Renderer_Smarty', 'template_dir', "{$this->_cacheDirectory}/templates/Content");
        $config->setConfiguration('Renderer_Smarty', 'compile_dir', "{$this->_cacheDirectory}/compiled-templates/Content");
        $config->setConfiguration('Renderer_Smarty', 'plugins_dir', array("{$this->_cacheDirectory}/plugins"));

        return $config;
    }

    function _doSetUp()
    {
        $this->_cacheDirectory = dirname(__FILE__) . '/' . basename(__FILE__, '.php');
        @mkdir("{$this->_cacheDirectory}/compiled-templates/Content");
        @mkdir("{$this->_cacheDirectory}/compiled-templates/Layout");
        @mkdir("{$this->_cacheDirectory}/compiled-templates/Fallback");
    }

    /**
     * @since Method available since Release 1.1.0
     */
    function &_getConfigForLayeredStructure()
    {
        $config = &new Piece_Unity_Config();
        $config->setConfiguration('Renderer_Smarty', 'template_dir', "{$this->_cacheDirectory}/templates");
        $config->setConfiguration('Renderer_Smarty', 'compile_dir', "{$this->_cacheDirectory}/compiled-templates");
        $config->setConfiguration('Renderer_Smarty', 'plugins_dir', array("{$this->_cacheDirectory}/plugins"));

        return $config;
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
