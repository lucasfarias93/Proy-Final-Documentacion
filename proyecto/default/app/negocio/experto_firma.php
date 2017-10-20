<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ExpertoOficios {

    public static function firmar_pdf($acta_id) {
        require_once('fpdf.php');

        // create an instance of FPDF
        $pdf = new fpdf();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 14);

        // let's write some dynamic content
        $text = 'This document is created with FPDF on '
                . date('r')
                . ' and digital signed with the SetaPDF-Signer component.';
        $pdf->MultiCell(0, 8, $text);

        // Output the PDF document to a string
        $fpdf = $pdf->Output('', 'F');

        require_once("library/SetaPDF/Autoload.php");

        // create a Http writer
        $writer = new SetaPDF_Core_Writer_Http("fpdf-sign-demo.pdf", true);
        // load document by filename
        $document = SetaPDF_Core_Document::loadByString($fpdf, $writer);

        // let's prepare the temporary file writer:
        SetaPDF_Core_Writer_TempFile::setTempDir("_tmp/");

        // create a signer instance for the document
        $signer = new SetaPDF_Signer($document);

        // set some signature properties
        $signer->setReason('Demo with FPDF');
        $signer->setLocation('setasign.com');

        // ccreate an OpenSSL module instance
        $module = new SetaPDF_Signer_Signature_Module_OpenSsl();
        // set the sign certificate
        $module->setCertificate(file_get_contents("certificate.pem"));
        // set the private key for the sign certificate
        $module->setPrivateKey(array(file_get_contents("private-key.pem"), "password"));

        // sign/certify the document
        $signer->sign($module);
    }

}
