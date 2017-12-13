<?php
/**
 * Created by PhpStorm.
 * User: Eric Zeidan
 * Date: 25/04/2017
 * Time: 11:06
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * We create the class for the plugin
 * @author: Eric Zeidan <ezeidan@kapturall.com>
 */

if(!class_exists('PdfPlugin')) {
    class PdfPlugin
    {

        const VERSION = '1.0';
        private static $instance;

        // varibles for pdf creation
        public $reference;
        public $asunto;
        public $addresse;

        public static function get_instance()
        {
            if (null == self::$instance) {
                self::$instance = new PdfPlugin();
            } // end if
            return self::$instance;
        }

        /**
         * The Class Constructor
         */
        public function __construct()
        {
            add_action('admin_menu', array($this, "add_option_menu"));
        }

        public function pdf_activate()
        {
            add_option('pdf_do_activation_redirect', true);

        }

        public function pdf_redirect()
        {
            if (get_option('pdf_do_activation_redirect', false)) {
                delete_option('pdf_do_activation_redirect');
                if (!isset($_GET['activate-multi'])) {
                    wp_redirect("options-general.php?page=caae-pdf-generator%2Finc%2Fclass-pdf-generator.php");
                }
            }
        }


        public function add_option_menu()
        {
            add_options_page("pdf_plugin", "PDF Generator", "read", __FILE__, array($this, 'admin_menu'));
        }


        public function admin_menu()
        {
            include('pdf-options.php');
        }

        public function pdf_admin_init()
        {
            wp_enqueue_script("jquery");
        }

        public function pdf_front_scripts() {
            wp_enqueue_script("jquery");
        }

        public function pdf_upload_dir( $dir ) {
            return array(
                'path' => $dir['basedir'] . '/certificates',
                'url' => $dir['baseurl'] . '/certificates',
                'subdir' => '/certificates',
            ) + $dir;
        }

        public function pdf_generate($filename, $postid, $postdata) {
            // Creaci�n del objeto de la clase heredada
            $pdf = new PDF();
            $pdf->setMargins(17.5, 29.7, 15);
            $pdf->SetAutoPageBreak(true, 22.5);
            $post = get_post($postid);
            $author = $post->post_author;
            $authorname = get_the_author_meta('user_firstname', $author) . ' ' . get_the_author_meta('user_lastname', $author);
            $authoremail = get_the_author_meta('user_email', $author);
            $operatorid = get_the_author_meta('_operator', $author);
            $pdf->asunto = "PE-SAS-C";
            $pdf->reference = $operatorid . "/DP/AAS";
            $pdf->addresse = "Usuario: " . $authorname . "\nCorreo electr�nico: " . $authoremail . "\nOperador: " . $operatorid;
            $pdf->AliasNbPages();
            $pdf->AddPage();
            $pageH = $pdf->GetPageHeight();
            $marginBottomBreak = $pageH - 22.5;
            // Movernos a la derecha
            $pdf->SetY(45);
            $pdf->Cell(92.5);
            $pdf->SetFont('Times','',10);
            // Direcci�n
            $pdf->MultiCell(85,5, $pdf->addresse, 1, 'L');
            // Salto de l�nea
            $pdf->SetX(10);
            $pdf->SetY(42);
            // Movernos a la izquierda
            //$this->Cell(10);
            // Date
            $pdf->SetFont('Times','',10);
            $pdf->Cell(30,10,'Fecha: ' . date('d.m.Y'),0,0,'L');
            $pdf->Ln(5);
            //Asunto
            $pdf->Cell(30,10,'Asunto: ' . $pdf->asunto,0,0,'L');
            $pdf->Ln(5);
            //Referencia
            $pdf->Cell(30,10,'Referencia: ' . $pdf->reference,0,0,'L');
            $pdf->Ln(25);
            $pdf->SetFont('Times','',10);
            //Handling the postdata
            $pdf->Cell(0,5,'Estimado Se�or/Se�ora',0,1);
            $pdf->Ln(5);
            $pdf->Cell(0,5,'El Servicio de Certificaci�n CAAE ha revisado:',0,1);
            $pdf->SetFont('Times','B',12);
            $postdate = get_the_date('d/m/Y', $postid);
            $pdf->MultiCell(0,5,'La SOLICITUD DE AUTORIZACI�N PARA SEMBRAR SEMILLA NO ECOL�GICA recibida en fecha ' . $postdate,0,1);
//            $pdf->Ln(3);
            $pdf->SetFont('Times','',10);
            $posY = $pdf->GetY() - 1.5;
            $pdf->Text(70,$posY,' y la informaci�n/documentaci�n de su expediente.');
            $pdf->Ln(5);
            $pdf->SetFont('Times','B',10);
            $pdf->Cell(0,5,'Para el alcance:',0,1);
            $pdf->SetFont('Times','',10);
            $pdf->Cell(0,5,'PRODUCCI�N ECOL�GICA AGRICULTURA',0,1);
            $pdf->Ln(5);
            $pdf->SetFont('Times','B',10);
            $pdf->Cell(0,5,'Con el siguiente resultado:',0,1);
            $pdf->SetFont('Times','',10);
            $pdf->Cell(0,5,'Se adjunta el resultado de la revisi�n de su solicitud de autorizaci�n:',0,1);
            $pdf->Ln(5);
            $header = array('Nombre', 'Nombre Cient�fico', 'Variedad', 'Autorizaci�n', 'Justificaci�n', 'Cantidad', 'Fecha Siembra', 'Tratamiento');
            // Colores, ancho de l�nea y fuente en negrita
            $pdf->SetFillColor(191,191,191);
            $pdf->SetTextColor(0);
            $pdf->SetDrawColor(0);
            $pdf->SetLineWidth(.3);
            $pdf->SetFont('Times','B',10);
            // Cabecera
            $posY = $pdf->GetY();
            $w = array(22.18, 22.18, 22.18, 22.18, 22.18, 22.18, 22.18, 22.18);
            for($i=0;$i<count($header);$i++):
                $pdf->SetY($posY);
                $wi = ($w[$i] * $i) + 17.5;
                $pdf->SetX($wi);
                if($i != 1 && $i != 6):
                    $h = 10;
                else:
                    $h= 5;
                endif;
                $pdf->MultiCell($w[$i], $h, $header[$i], 1, 'C', true);
            endfor;
            // Restauraci�n de colores y fuentes
            $pdf->SetFillColor(255);
            $pdf->SetTextColor(0);
            $pdf->SetFont('');
            // Datos
            $autorizado = 0; $noautorizado = 0;
            $aut = ''; $motivo = '';
            foreach($postdata["certificados"] as $i => $certificados)://each($data as $row){
                $posY = $pdf->GetY();
                $cantidad = $certificados['cantidad'] . ' ' . $certificados['unidad'];
                switch ($certificados["aproved"]){
                    case 1:
                        $aut = "Autorizado";
                        $autorizado = 1;
                        break;
                    case 0:
                        $aut = "No Autorizado";
                        $noautorizado = 1;
                        break;
                }
                switch($certificados['tipo-motivo']){
                    case 'A';
                        $motivo = "MOTIVO A";
                        break;
                    case 'B';
                        $motivo = "MOTIVO B";
                        break;
                    case 'C';
                        $motivo = "MOTIVO C";
                        break;
                }
                $content = array(utf8_decode($certificados['cultivo']), utf8_decode($certificados['nombre-cientifico']), utf8_decode($certificados['variedad']), utf8_decode($aut), utf8_decode($motivo), $cantidad, $certificados['fecha_siembra'], utf8_decode($certificados['tratamiento']));
                for($j=0;$j<count($content);$j++):
                    $pdf->SetY($posY);
                    $wi = ($w[$j] * $j) + 17.5;
                    $pdf->SetX($wi);
                    $pdf->SetFontSize(9);
                    if($j < 2):
                        if(strlen($content[$j]) > 13){
                            $h = 5;
                            $pdf->MultiCell($w[$j], $h, $content[$j], 'RTL', 'C', true);
                        }elseif(strlen($content[$j]) <= 13){
                            $h = 15;
                            $pdf->MultiCell($w[$j], $h, $content[$j], 1, 'C', true);
                        }else{
                            $h= 7.5;
                            $pdf->MultiCell($w[$j], $h, $content[$j], 1, 'C', true);
                        }
                    else:
                        if(strlen($content[$j]) <= 14){
                            $h = 15;
                        }else{
                            $h= 7.5;
                        }
                        $pdf->MultiCell($w[$j], $h, $content[$j], 1, 'C', true);
                    endif;
                endfor;
            endforeach;
            // L�nea de cierre
            $pdf->Cell(array_sum($w),0,'','T');
            $pdf->SetFont('Times','',10);
            $pdf->Ln(5);
            if($autorizado == 1) {
                $pdf->MultiCell(0, 5, 'Para las semillas indicadas como AUTORIZADAS se ACEPTA su SOLICITUD DE AUTORIZACI�N.', 0, 1);
                $pdf->Ln(5);
            }
            if($noautorizado == 1){
                $pdf->MultiCell(0,5,'Para las semillas indicadas como NO AUTORIZADAS se DENIEGA su SOLICITUD DE AUTORIZACI�N, debido a la aplicaci�n por parte de la autoridad competente de la Ley 30/2006 de Semillas y Plantas de Vivero y recursos Filogen�ticos por la que no se pueden comercializar variedades que no se encuentren inscritas en el cat�logo com�n de variedades de la Uni�n Europea.',0,1);
                $pdf->Ln(5);
            }
            $pdf->SetFont('Times','B','10');
            $pdf->MultiCell(0,5,'Es importante que lea atentamente las siguientes indicaciones:',0,1);
            $pdf->Ln(5);
            $pdf->SetFont('Times','',10);
            $pdf->SetTextColor(0,176,80);
            $pdf->MultiCell(0,5,'En la pr�xima visita de control debe aportar al inspector la factura o albar�n de la compra de semillas o material vegetal de reproducci�n y al menos una etiqueta de los sacos de semillas o de los haces de plantas empleados.',0,1);
            $pdf->Ln(5);
            if($noautorizado == 1){
                $pdf->MultiCell(0,5,'Cuando una variedad no est� autorizada no debe utilizar estas semillas para la producci�n de cultivos ecol�gicos o en conversi�n. Le informamos que en la pr�xima inspecci�n se verificar� el uso de semillas no autorizadas, ya que su utilizaci�n puede suponer una no conformidad y su exclusi�n del certificado de conformidad.',0,1);
                $pdf->Ln(5);
            }
            $pdf->Ln(10);
            $pdf->SetTextColor(0);
            $pdf->MultiCell(0,5,'Para cualquier aclaraci�n puede contactar en los siguientes tel�fonos que ponemos a su disposici�n:',0,1);
            $pdf->MultiCell(0,5,'+34 955 018 968 (Andaluc�a e Internacional) / +34 926 200 339 (Castilla La Mancha y Castilla y Le�n)  o en el correo electr�nico ',0,1);
            $posY = $pdf->GetY() - 1.5;
            $pdf->SetFont('Times','B',10);
            $pdf->Text(35,$posY,"agricultura@caae.es");
            $pdf->SetFont('Times','',10);
            $pdf->Ln(15);
            $posY = $pdf->GetY();
            // Si la imagen no cabe dentro del margen, agrega nueva p�gina
            if(($posY + 45) > $marginBottomBreak):
                $pdf->AddPage();
            endif;
            $pdf->MultiCell(0,5,'Atentamente:',0,1);
            $posY = $pdf->GetY();
            $pdf->Image(plugin_dir_path(__FILE__) . '/img/caae-firma.jpg',90,$posY,30,30);
            $pdf->Ln(30);
            $pdf->SetFont('Times','B',9);
            $pdf->MultiCell(0,5,'Fdo. Juan Manuel S�nchez Adame',0,'C');
            $pdf->MultiCell(0,5,'Director de Certificaci�n',0,'C');

            $filename2 = wp_upload_dir()["url"] . "/" . $filename;
            $filename= wp_upload_dir()["path"] . "/" . $filename;
            $pdf->Output($filename, 'F');

//            print_r($postid . ' ' . $filename2);
            //Aqui guardar el pdf en el post
	        $result = update_post_meta($postid, "autorization_file", $filename2);

	        if(is_wp_error($result)) {
	        	if(IS_CRON_CALL) {
			        write_log_delay('Error al guardar el meta del PDF');
		        } else {
			        wp_die( 'Error al guardar el PDF, intente m&aacute;s tarde o comunique el error al administrador', null, array( 'back_link' ) );
		        }
	        } else {
	        	return true;
	        }

        }

        public function pdf_generate_certificate($filename, $postid, $postdata) {
            // Creaci&oacute;n del objeto de la clase heredada
            $pdf = new PDF();
            $pdf->AliasNbPages();
            $pdf->AddPage();
            $pdf->SetFont('Times','',12);

            //Handling the postdata
            for($i=1;$i<=40;$i++)
                $pdf->Cell(0,10,'Imprimiendo l�nea n�mero '.$i,0,1);

            $filename= wp_upload_dir()['path'] . $filename;
            $pdf->Output($filename, 'F');

            //Aqui guardar el pdf en el post
        }

    }

} //Endif