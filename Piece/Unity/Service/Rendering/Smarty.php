<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP versions 4 and 5
 *
 * Copyright (c) 2008 KUBO Atsuhiro <iteman@users.sourceforge.net>,
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
 * @copyright  2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id$
 * @link       http://smarty.php.net/
 * @since      File available since Release 1.2.0
 */

// {{{ Piece_Unity_Service_Rendering_Smarty

/**
 * A rendering service based on Smarty.
 *
 * @package    Piece_Unity
 * @subpackage Piece_Unity_Component_Smarty
 * @copyright  2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @link       http://smarty.php.net/
 * @since      Class available since Release 1.2.0
 */
class Piece_Unity_Service_Rendering_Smarty extends Piece_Unity_Plugin_Renderer_HTML
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    var $_smartyClassVariables;

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ constructor

    /**
     * Sets a Smarty class variables array to the property.
     *
     * @param stdClass|array $smartyClassVariables
     */
    function Piece_Unity_Service_Rendering_Smarty($smartyClassVariables)
    {
        $this->_smartyClassVariables = (array)$smartyClassVariables;
    }

    // }}}
    // {{{ render()

    /**
     * Renders a HTML or HTML fragment.
     *
     * @param string                  $file
     * @param Piece_Unity_ViewElement &$viewElement
     * @throws PIECE_UNITY_ERROR_NOT_FOUND
     * @throws PIECE_UNITY_ERROR_INVOCATION_FAILED
     */
    function render($file, &$viewElement)
    {
        $this->_loadSmarty();
        $smarty = &new Smarty();

        foreach ($this->_smartyClassVariables as $key => $value) {
            if ($key == 'plugins_dir' && is_array($value)) {
                $oldPluginDirectories = $smarty->plugins_dir;
                $smarty->plugins_dir = array_merge($value, $oldPluginDirectories);
                 continue;
            }

            if (!is_null($value)) {
                $smarty->$key = $this->_adjustEndingSlash($value);
            } else {
                $smarty->$key = null;
            }
        }

        $viewElements = $viewElement->getElements();
        foreach (array_keys($viewElements) as $elementName) {
            $smarty->assign_by_ref($elementName, $viewElements[$elementName]);
        }

        set_error_handler(array('Piece_Unity_Error', 'pushPHPError'));
        Piece_Unity_Error::disableCallback();
        $smarty->display($file);
        Piece_Unity_Error::enableCallback();
        restore_error_handler();
        if (Piece_Unity_Error::hasErrors()) {
            $error = Piece_Unity_Error::pop();
            if ($error['code'] == PIECE_UNITY_ERROR_PHP_ERROR
                && array_key_exists('repackage', $error)
                && preg_match('/^Smarty error: unable to read resource:/',
                              $error['repackage']['message'])
                ) {
                Piece_Unity_Error::push(PIECE_UNITY_ERROR_NOT_FOUND,
                                        "The HTML template file [ $file ] is not found or not readable.",
                                        'exception',
                                        array(),
                                        $error
                                        );
            } else {
                Piece_Unity_Error::push(PIECE_UNITY_ERROR_INVOCATION_FAILED,
                                        'Failed to invoke Smarty::display() for any reasons.',
                                        $error['level'],
                                        array(),
                                        $error
                                        );
            }
        }
    }

    /**#@-*/

    /**#@+
     * @access private
     */

    // }}}
    // {{{ _loadSmarty()

    /**
     * Loads the Smarty class.
     *
     * @throws PIECE_UNITY_ERROR_NOT_FOUND
     */
    function _loadSmarty()
    {
        if (defined('SMARTY_DIR')) {
            $included = @include_once SMARTY_DIR . 'Smarty.class.php';
        } else {
            $included = @include_once 'Smarty.class.php';
        }

        if (!$included) {
            Piece_Unity_Error::push(PIECE_UNITY_ERROR_NOT_FOUND,
                                    'The Smarty class file [ Smarty.class.php ] is not found or not readable.'
                                    );
            return;
        }

        if (version_compare(phpversion(), '5.0.0', '<')) {
            $loaded = class_exists('Smarty');
        } else {
            $loaded = class_exists('Smarty', false);
        }

        if (!$loaded) {
            Piece_Unity_Error::push(PIECE_UNITY_ERROR_NOT_FOUND,
                                    'The class [ Smarty ] does not defined in the class file [ Smarty.class.php ].'
                                    );
        }
    }

    // }}}
    // {{{ _adjustEndingSlash()

    /**
     * Added an ending slash to the directory if it is required.
     *
     * @param string $directory
     * @return string
     * @static
     */
    function _adjustEndingSlash($directory)
    {
        if (substr($directory, -1, 1) != '/' && substr($directory, -1, 1) != '\\') {
            $directory .= '/';
        }

        return $directory;
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
