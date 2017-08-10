<?php 
 class PDF extends FPDF{  	
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
  	
  	/* M�todo que genera una tabla de personas */
  	public function table($personas){
  		// Colors, line width and bold font
  		$this->SetFillColor(224, 235, 255); //color de fondo del header de la tabla
  		$this->SetTextColor(0,0,150); //(r,g,b)
  		$this->SetDrawColor(0,0,0);
  		$this->SetLineWidth(0.3);
  		$this->SetFont('helvetica','B','12');
  		$this->setX(20);
  		
  		$this->Cell(50, 5, 'NOMBRE', 1, 0, 'C', 1);
  		$this->Cell(20, 5, 'EDAD', 1, 0, 'C', 1);
  		$this->Cell(70, 5, 'DOMICILIO', 1, 0, 'C', 1);
  		$this->Cell(30, 5, 'TELEFONO', 1, 0, 'C', 1);
  		$this->Cell(50, 5, 'EMAIL', 1, 0, 'C', 1);
  		$this->Ln();
  		// Color and font restoration
  		$this->SetFillColor(255, 255, 255); //color de fondo del contenido de la tabla
  		  		
  		foreach ($personas as $persona){
  			$this->setX(20);
  			$this->Cell(50, 5, $persona->nombre, 1, 0, 'L', 1); //alineacion = Left
  			$this->Cell(20, 5, $persona->edad, 1, 0, 'L', 1);
  			$this->Cell(70, 5, $persona->domicilio, 1, 0, 'L', 1);
  			$this->Cell(30, 5, $persona->telefono, 1, 0, 'L', 1);
  			$this->Cell(50, 5, $persona->email, 1, 0, 'L', 1);
  			$this->Ln();
  		}
  	}  	
 }
?>