<?php 

class PaginationHelper extends AppHelper
{
    var $helpers = array('Html', 'Ajax');
    function paginate( $link, $page, $total, $options = array(), $show = 9, $skip='&hellip;' )
    {
        /*
            $link    string of what link is to be (utilizes $html->link helper)... page numbers will be appended to its end
            $page    int current page you're on
            $total    int total number of pages
            $show    int how many page numbers / "skips" to show between first and last numbers
            $skip    string text to be displayed for "skips"... inside <span>
        */
		$obj = isset($options['update']) ? 'Ajax' : 'Html';
        // Get out early if there's no total pages
        if ( $total < 1 ) return false;

        // Init
        if ( $show < 1 ) $show = 1;                        // make sure you're showing at least 1
        $show_mid = ceil( $show / 2 );                    // find the midpoint of the shown page numbers / skips
        $skip = '<span class="skip">'.$skip.'</span>';    // add spans around skip text
        $out = "\n";

        // Figure out start point for In-between numbers
        if ( $page <= $show_mid ) $start = 2;
        elseif ( $page > ($total-$show) ) $start = $total - $show;
        else $start = $page - $show_mid + 1;

        // Previous link
        $out .= ( ($page-1) > 0 )
                    ? $this->{$obj}->link( 'Prev', $link.($page-1), array_merge(array('title'=>'View the previous index page', 'class'=>'nextprev'), $options) ) 
                    : '<span class="nextprev">Prev</span>';
        $out .= "\n";

        // First number
        $out .= ( $page == 1 )
                    ? '<span class="current">1</span>'
                    : $this->{$obj}->link( '1', $link.'1', array_merge(array('title'=>'View index page 1'), $options) );
        $out .= "\n";

        // In-between numbers
        for ( $i=0; $i<( ($total<$show+2) ? $total-2 : $show ); $i++ )
        {
            // First in-between number...
            if ( $i == 0 )
            {
                $out .= ( $start == 2 ) 
                            ? ( $page == 2 )
                                ? '<span class="current">2</span>'
                                : $this->{$obj}->link( '2', $link.'2', array_merge(array('title'=>'View index page 2'), $options) )
                            : $skip;
            }

            // Last in-between number...
            elseif ( $i == ($show-1) )
            {
                $out .= ( $start >= ($total-$show) ) 
                            ? ( $page == ($total-1) )
                                ? '<span class="current">'.($total-1).'</span>'
                                : $this->{$obj}->link( ($total-1), $link.($total-1), array_merge(array('title'=>'View index page '.($total-1)), $options) )
                            : $skip;
            }

            // Else...
            else 
            {
                $out .= ( $page == ($start+$i) )
                            ? '<span class="current">'.($start+$i).'</span>'
                            : $this->{$obj}->link( ($start+$i), $link.($start+$i), array_merge(array('title'=>'View index page '.($start+$i)), $options) );
            }

            $out .= "\n";
        }

        // Last number
        if ( $total > 1 )
        {
            $out .= ( $page == $total )
                        ? '<span class="current">'.$total.'</span>'
                        : $this->{$obj}->link( $total, $link.$total, array_merge(array('title'=>'View index page '.$total), $options) );
            $out .= "\n";
        }

        // Next link
        $out .= ( ($page+1) <= $total )
                    ? $this->{$obj}->link( 'Next', $link.($page+1), array_merge(array('title'=>'View the next index page', 'class'=>'nextprev'), $options) )
                    : '<span class="nextprev">Next</span>';
        $out .= "\n";

        // Return
        return '<div class="pagination">'.$out.'</div>';
    }

}

?>