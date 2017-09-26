<?php 
 class PDFTramites extends FPDF{  	
    /* Redefino el método que es usado para generar la cabecera de página. 
     * Es automáticamente invocada por AddPage()     
     */
  	public function Header(){     
   		$this->SetFont('helvetica','B','14');
   		$this->setX(20);
   		$this->Cell(200, 5, 'Reporte de personas', 0, 0, 'C', 0);
   		$this->Ln();
   		// Line break
   		$this->Ln(20);
  	}
        
  	/* Redefino el método que es usado para generar el pie de página. 
  	 * Es automáticamente invocado por AddPage()
  	 */
  	public function Footer(){
  		// Position at 1.5 cm from bottom
  		$this->SetY(-15);
  		// Set font
  		$this->SetFont('helvetica', 'I', 10);
  		// Page number
  		$this->Cell(0, 10, $this->PageNo(), 0, 0, 'R');
  	}
  	
 }
?>