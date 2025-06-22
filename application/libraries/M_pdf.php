<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class M_pdf {
    
    function m_pdf()
    {
        $CI = & get_instance();
        log_message('Debug', 'mPDF class is loaded.');
    }
 
    function load($param=NULL)
    {
        include_once APPPATH.'/third_party/mpdf60/mpdf.php';
         
        if ($params == NULL)
        {
			//($mode='',$format='A4',$default_font_size=0,$default_font='',$mgl=15,$mgr=15,$mgt=16,$mgb=16,$mgh=9,$mgf=9, $orientation='P')
            $param = '"en-GB-x","LEGALCUST-L","","",1,1,1,1,6,3,"L"';
        }
         
        return new mPDF($param);
    }
}