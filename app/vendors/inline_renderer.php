<?php

    /*
        file: inline_renderer.php

        This file is a modified renderer for the Text_Diff PEAR package.

        The inline_diff code was written by Ciprian Popovici in 2004,
        as a hack building upon the Text_Diff PEAR package.
        It is released under the GPL.
     
        There are 3 files in this package: inline_example.php, inline_function.php, inline_renderer.php
    */

     

        include_once 'Text/Diff/Renderer.php';

        class Text_Diff_Renderer_inline extends Text_Diff_Renderer {

     

        var $ins_prefix = '<ins>';

        var $ins_suffix = '</ins>';

        var $del_prefix = '<del>';

        var $del_suffix = '</del>';

       

        function Text_Diff_Renderer_inline($context_lines = 10000, $ins_prefix = '<ins>', $ins_suffix = '</ins>', $del_prefix = '<del>', $del_suffix = '</del>')

        {

            $this->$ins_prefix = $ins_prefix;

            $this->$ins_suffix = $ins_suffix;

            $this->$del_prefix = $del_prefix;

            $this->$del_suffix = $del_suffix;

           

            $this->_leading_context_lines = $context_lines;

            $this->_trailing_context_lines = $context_lines;

        }

     

        function _lines($lines)

        {

            $out = '';

            foreach ($lines as $line) {

                $out .= "$line ";

            }

            return $out;

        }

     

        function _blockHeader($xbeg, $xlen, $ybeg, $ylen)

        {

            return '';

        }

     

        function _startBlock($header)

        {

            return $header;

        }

     

        function _added($lines)

        {

            $out = $this->ins_prefix;

            $out .= $this->_lines($lines);

            $out .= $this->ins_suffix;

            return $out;

        }

     

        function _deleted($lines)

        {

            $out = $this->del_prefix;

            $out .= $this->_lines($lines);

            $out .= $this->del_suffix;

            return $out;

        }

     

        function _changed($orig, $final)

        {

            $out = '';

            $out .= $this->_deleted($orig);

            $out .= $this->_added($final);

            return $out;

        }

     

    }

    ?>