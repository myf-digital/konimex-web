<?php

class PDF extends FPDF
{

function Footer()
{
    // Go to 1.5 cm from bottom
    //$this->SetY(-10);
    // Select Arial italic 8
    //$this->SetFont(''Arial'',''I'',8);
    // Print current and total page numbers
    //$this->Cell(0,10,''Halaman ''.$this->PageNo().''/{nb}'',0,0,''C'');
}


function Header()
{

    $this->SetFillColor(255, 255, 255);  
      $this->image(base_url().'assets/logo/hrd_logo.jpg',12,10,20,20);
    $this->SetFont('Arial','B',14);    
    $this->setx(40);
      $this->Cell(100,5,'Dinas Kesehatan Republik Indonesia',0,0,'L',1); $this->Ln();   
    $this->setx(40);
    $this->SetFont('Arial','B',10);    
      $this->Cell(100,5,'Jl. Raya Jakarta',0,0,'L',1); $this->Ln();   
    $this->setx(40);
      $this->Cell(100,5,'Telp. 0260 888888 Fax. 0260 9999999',0,0,'L',1); $this->Ln();   
    $this->setx(40);
      $this->Cell(100,5,'Website: www.dinkesjkt.go.id  - Email: info@dinkesjkt.go.id',0,0,'L',1); $this->Ln();   
      $this->Ln(); 
      $this->SetFont('Arial','B',12);    
      $this->Cell(400,6,'Basic CRUD Print PDF',0,0,'C',1); $this->Ln();   
      $this->SetFont('Arial','B',10);    
	$this->setx(250);  
	  $this->Cell(10,6,'LAMPIRAN SURAT EDARAN KEPALA BADAN ADMINISTRASI KEPEGAWAIAN NEGARA',0,0,'L',1); 
	$this->Ln();   
      $this->setx(250);
	  $this->Cell(20,5,'NOMOR : 03/SE/1980',0,0,'L',1);		 
    $this->Ln();   
      $this->setx(250);
	  $this->Cell(20,5,'TANGGAL : 11 FEBRUARI 1980',0,0,'L',1); 
	  $this->Ln();
	  $this->setx(20);	
	  $this->Cell(100,5,'UNIT ORGANISASI : DINAS KESEHATAN PROVINSI DKI JAKARTA',0,0,'L',1);	  
	  $this->setx(250);	
	  $this->Cell(100,5,'BERLAKU UNTUK TAHUN '.date("Y-m"),0,0,'L',1);
    $this->Ln();

  // memberi warna latar merah pada kepala tabel
  $this->SetFillColor(194, 194, 194);  
    // setting huruf bold pada kepala tabel
    $this->SetFont('Arial','B',7);    
      $this->SetX(20);			
	  $this->Cell(16,7,'NO',1,0,'C',1);    
      $this->SetX(36);
	  $this->Cell(20,7,'Input Example',1,0,'C',1);
	  $this->SetX(56);
	  $this->Cell(30,7,'Input Date',1,0,'C',1);	
      $this->SetX(86);
	  $this->Cell(40,7,'Email',1,0,'C',1);
	  $this->SetX(126);
	  $this->Cell(35,7,'Select Box',1,0,'C',1);
	  $this->SetX(161);
	  $this->Cell(70,7,'Text Area',1,0,'C',1);
	  $this->SetX(231);
	  $this->Cell(45,7,'Area',1,0,'C',1);
	  $this->SetX(276);
	  $this->Cell(57,7,'Region',1,0,'C',1);
	  $this->SetX(333);
	  $this->Cell(50,7,'Branch',1,0,'C',1);  
    $this->Ln();
    
}

}
$nama_file ='Basic_CRUD.pdf';
$pdf=new PDF('L','mm','A3'); 
$pdf->SetMargins(10,10,30);
$pdf->setAutoPageBreak(true, 10);
$pdf->AddPage();
$pdf->AliasNbPages();

  $pdf->SetFont('Arial','',8);       
  $pdf->SetFillColor(255, 255, 255);  
	
	$i=1;
	foreach ($data_detail as $value) {
		
		  $pdf->SetX(20);			
		  $pdf->Cell(16,7,$i,1,0,'C');    
		  $pdf->SetX(36);
		  $pdf->Cell(20,7,$value['input_text'],1,0,'C');
		  $pdf->SetX(56);
		  $pdf->Cell(30,7,$value['startdate'],1,0,'C');	
		  $pdf->SetX(86);
		  $pdf->Cell(40,7,$value['email'],1,0,'C');
		  $pdf->SetX(126);
		  $pdf->Cell(35,7,$value['select_box'],1,0,'C');
		  $pdf->SetX(161);
		  $pdf->Cell(70,7,$value['text_area'],1,0,'C');
		  $pdf->SetX(231);
		  $pdf->Cell(45,7,$value['area_name'],1,0,'C');
		  $pdf->SetX(276);
		  $pdf->Cell(57,7,$value['region_name'],1,0,'C');
		  $pdf->SetX(333);
		  $pdf->Cell(50,7,$value['branch_name'],1,0,'C');  
		$pdf->Ln();
		$i++;
	} 
$pdf->Output($nama_file,'I');
?>
