<?php
/**
 * Created by PhpStorm.
 * User: Eric Zeidan
 * Date: 25/04/2017
 * Time: 12:04
 */

class PDF extends FPDF
{
    public $reference;
    public $asunto;
    public $addresse;
    var $angle=0;

    // Cabecera de página
    function Header()
    {
        // Logo
        $this->Image(plugin_dir_path(__FILE__) . '../img/caae-logo.jpg',10,5,33);
        //Titulo
        $this->SetY(25);
        $this->SetFont('Times','B',20);
        $this->MultiCell(0,10,'TRÁMITES DE DOCUMENTACIÓN',0,'C');
        $this->Ln(10);
        $this->SetFont('Times','',6);
        $this->RotatedText(8.75,145,"CIF: B-91607663",90);
    }

    function Rotate($angle,$x=-1,$y=-1)
    {
        if($x==-1)
            $x=$this->x;
        if($y==-1)
            $y=$this->y;
        if($this->angle!=0)
            $this->_out('Q');
        $this->angle=$angle;
        if($angle!=0)
        {
            $angle*=M_PI/180;
            $c=cos($angle);
            $s=sin($angle);
            $cx=$x*$this->k;
            $cy=($this->h-$y)*$this->k;
            $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
        }
    }

    function RotatedText($x,$y,$txt,$angle)
    {
        //Text rotated around its origin
        $this->Rotate($angle,$x,$y);
        $this->Text($x,$y,$txt);
        $this->Rotate(0);
    }

    function RotatedImage($file,$x,$y,$w,$h,$angle)
    {
        //Image rotated around its upper-left corner
        $this->Rotate($angle,$x,$y);
        $this->Image($file,$x,$y,$w,$h);
        $this->Rotate(0);
    }

    // Pie de página
    function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-25);
        $this->SetFont('Times','',8);
        $this->RotatedText(100,-20,'ENDTEXT',90);
        // Arial italic 8
        // Número de página
        $this->Cell(40,5,'F/PGT-01/02   Rev. 00 ' . date("d/m/y"),0,0);
        $this->Cell(117.5);
        $this->Cell(20,5,'Pág. '.$this->PageNo().' de {nb}',0,0,'R');
        $this->Ln(10);
        $this->SetFont('Times','B',10);
        $this->MultiCell(0,3,'Servicio de Certificación CAAE, S.L.U.    caae@caae.es - caae.es',0,'R');
        $this->SetFont('Times','',8);
        $this->MultiCell(0,3,'Avda. Emilio Lemos, 2 - Edificio Torre Este - Módulo 603 - 41020 - Sevilla',0,'R');
        $this->MultiCell(0,3,'C/Carlos Vázquez, 4 - 3ª planta - 13001 - Ciudad Real',0,'R');
    }

    function _endpage()
    {
        if($this->angle!=0)
        {
            $this->angle=0;
            $this->_out('Q');
        }
        parent::_endpage();
    }
}