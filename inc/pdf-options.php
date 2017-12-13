<?php
/**
 * Created by PhpStorm.
 * User: Eric Zeidan
 * Date: 03/04/2017
 * Time: 12:27
 */


defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


if (!current_user_can('manage_options')) {
    wp_die(_e('You are not authorized to view this page.','clone'));
}

// Get the current tab
$current = empty( $_GET['tab'] )?"general":$_GET['tab'];

// Set the Tabs Arary
$tabs = array(
    'tab1'   => 'Tab 1',
    'tab2'  => 'Tab 2',
);

if ( isset ( $_REQUEST['tab'] ) )
    $tab = $_REQUEST['tab'];
else
    $tab = 'general';

switch ( $tab ) {
    case 'tab1' :
        $option_name = 'wp_clone_tab1';
        break;
    case 'tab2' :
        $option_name = 'wp_clone_tab2';
        break;
}

if (isset( $_POST['cloneoptions'] ) && wp_verify_nonce( $_POST['cloneoptions'], 'cloneoptionsnonce' )) {


//    $inputs = $_POST['inputs'];
//    if ( get_option( $option_name ) !== false ) {
//        $update = update_option($option_name, $inputs);
//    } else {
//        $deprecated = null;
//        $autoload = 'no';
//        $update = add_option($option_name, $inputs, $deprecated, $autoload);
//    }

    if($update) {
        echo '<div class="updated">';
        _e('settings saved.','clone');
        echo '</strong></p></div>';
    } else {
        echo '<div class="error"><p><strong>';
        _e('Error - Url does not seems to be correct.','clone');
        echo '</strong></p></div>';
    }
}
?>
<div class="wrap" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
    <div id="welcome-panel" class="welcome-panel">
        <div class="welcome-panel-content">
            <h2><?php _e('PDF Generator','clone'); ?></h2>
            <p><?php _e('Settings Page','clone'); ?></p>
        </div>
        <div class="clear"></div>

        <h2 class="nav-tab-wrapper">
            <?php

            foreach( $tabs as $tab => $name ){
                $class = ( $tab == $current ) ? ' nav-tab-active' : '';
                echo "<a class='nav-tab$class' href='?page=caae-pdf-generator%2Finc%2Fclass-pdf-generator.php&tab=$tab'>$name</a>";
            }
            ?>
        </h2>

        <?php   if ( isset ( $_GET['tab'] ) ) $tab = $_GET['tab'];
        else $tab = 'tab1'; ?>

        <?php switch ( $tab ){
            case 'tab1':
                ?>
                    TAB 1
                <?php
                break;
            case 'tab2':
                $pdf = new PdfPlugin();
                $pdf->asunto = "PE-SAS-C";
                $pdf->reference = "DP/AAS";
                $pdf->addresse = "una linea \n dos lineas \n tres lineas";
                $result = $pdf->pdf_generate('test2.pdf', null, null);
                var_dump($result);
                die;
                ?>
                    TAB 2
                <?php
                break;
        }
        ?>


    </div>
</div><!-- end wrap -->